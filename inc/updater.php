<?php
/**
 * Automatiske tema-opdateringer fra GitHub Releases.
 *
 * Bruger WordPress' standardmekanisme for `Update URI` (WP 5.8+):
 * style.css har "Update URI: https://github.com/kristianwind/garageristeriet-theme",
 * og WordPress kalder derfor filteret `update_themes_github.com` under
 * opdaterings-tjekket. Her slår vi nyeste release op via GitHub API'et.
 *
 * @package garageristeriet
 */

defined( 'ABSPATH' ) || exit;

/**
 * GitHub-repo i formatet owner/repo.
 */
define( 'GARAGERISTERIET_GITHUB_REPO', 'kristianwind/garageristeriet-theme' );

/**
 * Transient-nøgle til cache af GitHub API-svaret.
 */
define( 'GARAGERISTERIET_UPDATE_TRANSIENT', 'garageristeriet_latest_release' );

/**
 * Hent nyeste release fra GitHub API med 12 timers cache.
 *
 * @return array|null Release-data (tag_name, html_url, zipball_url, assets) eller null.
 */
function garageristeriet_get_latest_release() {
	$cached = get_transient( GARAGERISTERIET_UPDATE_TRANSIENT );

	if ( 'error' === $cached ) {
		return null;
	}

	if ( is_array( $cached ) && ! empty( $cached['tag_name'] ) ) {
		return $cached;
	}

	$response = wp_remote_get(
		sprintf( 'https://api.github.com/repos/%s/releases/latest', GARAGERISTERIET_GITHUB_REPO ),
		array(
			'timeout' => 10,
			'headers' => array(
				'Accept'               => 'application/vnd.github+json',
				'X-GitHub-Api-Version' => '2022-11-28',
				'User-Agent'           => 'WordPress/garageristeriet-theme; ' . home_url( '/' ),
			),
		)
	);

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		// Cache fejl kortvarigt, så vi ikke hamrer GitHubs rate limit.
		set_transient( GARAGERISTERIET_UPDATE_TRANSIENT, 'error', HOUR_IN_SECONDS );
		return null;
	}

	$release = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( ! is_array( $release ) || empty( $release['tag_name'] ) ) {
		set_transient( GARAGERISTERIET_UPDATE_TRANSIENT, 'error', HOUR_IN_SECONDS );
		return null;
	}

	set_transient( GARAGERISTERIET_UPDATE_TRANSIENT, $release, 12 * HOUR_IN_SECONDS );

	return $release;
}

/**
 * Find download-pakken for en release.
 *
 * Foretrækker release-asset'et `garageristeriet.zip` (bygget af GitHub Actions
 * med korrekt mappenavn) og falder tilbage til GitHubs zipball.
 *
 * @param array $release Release-data fra GitHub API'et.
 * @return string URL til zip-pakken.
 */
function garageristeriet_release_package( $release ) {
	if ( ! empty( $release['assets'] ) && is_array( $release['assets'] ) ) {
		foreach ( $release['assets'] as $asset ) {
			if ( isset( $asset['name'], $asset['browser_download_url'] ) && 'garageristeriet.zip' === $asset['name'] ) {
				return $asset['browser_download_url'];
			}
		}
	}

	return isset( $release['zipball_url'] ) ? $release['zipball_url'] : '';
}

/**
 * Opdaterings-tjek for temaet via `update_themes_github.com`.
 *
 * @param array|false $update            Eksisterende update-data (false = ingen).
 * @param array       $theme_data        Tema-headers fra style.css.
 * @param string      $theme_stylesheet  Temaets mappe-slug.
 * @param string[]    $locales           Installerede sprog.
 * @return array|false Update-array eller false hvis der ikke er en opdatering.
 */
function garageristeriet_check_for_update( $update, $theme_data, $theme_stylesheet, $locales ) {
	if ( 'garageristeriet' !== $theme_stylesheet ) {
		return $update;
	}

	$release = garageristeriet_get_latest_release();

	if ( null === $release ) {
		return $update;
	}

	$new_version     = ltrim( (string) $release['tag_name'], 'vV' );
	$current_version = isset( $theme_data['Version'] ) ? $theme_data['Version'] : '0';

	if ( ! $new_version || version_compare( $new_version, $current_version, '<=' ) ) {
		return false;
	}

	$package = garageristeriet_release_package( $release );

	if ( ! $package ) {
		return false;
	}

	return array(
		'theme'   => $theme_stylesheet,
		'version' => $new_version,
		'url'     => isset( $release['html_url'] ) ? $release['html_url'] : 'https://github.com/' . GARAGERISTERIET_GITHUB_REPO . '/releases',
		'package' => $package,
	);
}
add_filter( 'update_themes_github.com', 'garageristeriet_check_for_update', 10, 4 );

/**
 * Omdøb GitHubs udpakkede mappe til temaets slug.
 *
 * GitHubs zipball udpakkes som fx `kristianwind-garageristeriet-theme-a1b2c3d/`.
 * Uden dette hook ville opdateringen lande i en ny tema-mappe i stedet for
 * at erstatte `garageristeriet/`.
 *
 * @param string                   $source        Sti til den udpakkede mappe.
 * @param string                   $remote_source Sti til den midlertidige rodmappe.
 * @param WP_Upgrader              $upgrader      Upgrader-instansen.
 * @param array                    $hook_extra    Kontekst om hvad der opdateres.
 * @return string|WP_Error Justeret sti eller fejl.
 */
function garageristeriet_fix_update_source( $source, $remote_source, $upgrader, $hook_extra = array() ) {
	if ( ! is_array( $hook_extra ) || empty( $hook_extra['theme'] ) || 'garageristeriet' !== $hook_extra['theme'] ) {
		return $source;
	}

	global $wp_filesystem;

	$desired = trailingslashit( $remote_source ) . 'garageristeriet/';

	if ( untrailingslashit( $source ) === untrailingslashit( $desired ) ) {
		return $source;
	}

	if ( $wp_filesystem && $wp_filesystem->move( untrailingslashit( $source ), untrailingslashit( $desired ) ) ) {
		return $desired;
	}

	return new WP_Error(
		'garageristeriet_rename_failed',
		__( 'Kunne ikke omdøbe opdateringsmappen til temaets navn.', 'garageristeriet' )
	);
}
add_filter( 'upgrader_source_selection', 'garageristeriet_fix_update_source', 10, 4 );

/**
 * Ryd update-cachen når temaet netop er blevet opdateret,
 * så versionsnummeret i Udseende → Temaer er friskt med det samme.
 *
 * @param WP_Upgrader $upgrader   Upgrader-instansen.
 * @param array       $hook_extra Kontekst om hvad der blev opdateret.
 */
function garageristeriet_clear_update_cache( $upgrader, $hook_extra ) {
	if ( isset( $hook_extra['type'] ) && 'theme' === $hook_extra['type'] ) {
		delete_transient( GARAGERISTERIET_UPDATE_TRANSIENT );
	}
}
add_action( 'upgrader_process_complete', 'garageristeriet_clear_update_cache', 10, 2 );
