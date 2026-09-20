<?php
/**
 * Selvopdatering via GitHub Releases (WordPress 6.1+ "Update URI").
 *
 * style.css har:  Update URI: https://github.com/kristianwind/garageristeriet-theme
 * WordPress bygger filternavnet af HOSTNAVNET og kalder derfor
 * update_themes_github.com for HVERT tema med samme host.
 *
 * @package GarageRisteriet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const GR_UPDATE_SLUG  = 'garageristeriet';                 // = mappenavnet. Maa aldrig aendres.
const GR_UPDATE_OWNER = 'kristianwind';
const GR_UPDATE_REPO  = 'garageristeriet-theme';
const GR_UPDATE_TTL   = 6 * HOUR_IN_SECONDS;

function gr_update_repo_url() {
	return 'https://github.com/' . GR_UPDATE_OWNER . '/' . GR_UPDATE_REPO;
}

/**
 * Seneste tag fra GitHub Releases. Tagget gemmes RAAT (fx "v1.2.0"),
 * fordi det indgaar i download-stien praecis som det er skrevet.
 *
 * URL'en bygges selv — aldrig en adresse fra API-svaret.
 *
 * @return string|false
 */
function gr_update_latest_tag() {
	$cached = get_transient( 'gr_update_latest_tag' );
	if ( false !== $cached ) {
		return '' === $cached ? false : $cached;
	}

	$api      = 'https://api.github.com/repos/' . GR_UPDATE_OWNER . '/' . GR_UPDATE_REPO . '/releases/latest';
	$response = wp_remote_get( $api, array(
		'timeout' => 8,
		'headers' => array(
			'Accept'     => 'application/vnd.github+json',
			'User-Agent' => 'GarageRisteriet-Theme/' . GR_UPDATE_SLUG,
		),
	) );

	if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
		// Kort cache paa fejl, saa vi ikke bruger GitHubs rate limit op.
		set_transient( 'gr_update_latest_tag', '', 15 * MINUTE_IN_SECONDS );
		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	$tag  = isset( $body['tag_name'] ) ? trim( (string) $body['tag_name'] ) : '';

	if ( '' === $tag || ! preg_match( '/^[vV]?[0-9][0-9A-Za-z.\-]*$/', $tag ) ) {
		set_transient( 'gr_update_latest_tag', '', 15 * MINUTE_IN_SECONDS );
		return false;
	}

	set_transient( 'gr_update_latest_tag', $tag, GR_UPDATE_TTL );
	return $tag;
}

/**
 * @param array|false $update           Svar fra en tidligere callback.
 * @param array       $theme_data       Headers fra style.css.
 * @param string      $theme_stylesheet Mappenavn paa det tema der spoerges om.
 * @return array|false
 */
function gr_theme_update( $update, $theme_data, $theme_stylesheet ) {
	// Filteret deles med ALLE temaer der opdaterer fra github.com.
	// Uden denne guard tilbyder vi vores release til naboens tema.
	if ( GR_UPDATE_SLUG !== $theme_stylesheet ) {
		return $update;
	}

	$installed = isset( $theme_data['Version'] ) ? (string) $theme_data['Version'] : '0.0.0';

	// Svarer feedet ikke, returneres den INSTALLEREDE version med tom package.
	// Returneres false, ryger temaet ud af opdaterings-transienten, og
	// "Aktivér auto-opdateringer" forsvinder fra skaermen.
	$fallback = array(
		'theme'   => GR_UPDATE_SLUG,
		'version' => $installed,
		'url'     => gr_update_repo_url() . '/releases',
		'package' => '',
	);

	$tag = gr_update_latest_tag();
	if ( false === $tag ) {
		return $fallback;
	}

	return array(
		'theme'        => GR_UPDATE_SLUG,
		'version'      => ltrim( $tag, 'vV' ),
		'url'          => gr_update_repo_url() . '/releases',
		'package'      => gr_update_repo_url() . '/releases/download/' . rawurlencode( $tag ) . '/' . GR_UPDATE_SLUG . '.zip',
		'requires'     => isset( $theme_data['RequiresWP'] ) ? $theme_data['RequiresWP'] : '',
		'requires_php' => isset( $theme_data['RequiresPHP'] ) ? $theme_data['RequiresPHP'] : '',
	);
}
add_filter( 'update_themes_github.com', 'gr_theme_update', 10, 3 );

function gr_update_flush_cache() {
	delete_transient( 'gr_update_latest_tag' );
}
add_action( 'upgrader_process_complete', 'gr_update_flush_cache' );
add_action( 'load-update-core.php', 'gr_update_flush_cache' );
