<?php
/**
 * GarageRisteriet – tema-funktioner.
 *
 * @package garageristeriet
 */

defined( 'ABSPATH' ) || exit;

/**
 * Tema-opsætning: WooCommerce-support og editor-styles.
 */
function garageristeriet_setup() {
	add_theme_support( 'woocommerce' );

	// Sørg for at supplerende CSS også gælder i blok-editoren.
	add_editor_style( 'assets/css/garageristeriet.css' );

	load_theme_textdomain( 'garageristeriet', get_template_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'garageristeriet_setup' );

/**
 * Frontend-assets: stylesheet og minimal vanilla JS til sticky header.
 */
function garageristeriet_enqueue_assets() {
	$version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'garageristeriet',
		get_theme_file_uri( 'assets/css/garageristeriet.css' ),
		array(),
		$version
	);

	wp_enqueue_script(
		'garageristeriet-header',
		get_theme_file_uri( 'assets/js/garageristeriet.js' ),
		array(),
		$version,
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);
}
add_action( 'wp_enqueue_scripts', 'garageristeriet_enqueue_assets' );

/**
 * Registrér pattern-kategorien "GarageRisteriet".
 */
function garageristeriet_register_pattern_category() {
	register_block_pattern_category(
		'garageristeriet',
		array(
			'label'       => __( 'GarageRisteriet', 'garageristeriet' ),
			'description' => __( 'Sektioner til GarageRisteriets onepager-forside.', 'garageristeriet' ),
		)
	);
}
add_action( 'init', 'garageristeriet_register_pattern_category' );

// Automatiske opdateringer fra GitHub Releases.
require get_template_directory() . '/inc/updater.php';
