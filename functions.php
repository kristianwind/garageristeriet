<?php
/**
 * GarageRisteriet - blocktheme opsætning.
 *
 * @package GarageRisteriet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GR_VERSION', '1.3.2' );

/**
 * Temaunderstøttelse.
 */
function gr_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_editor_style( 'style.css' );

	// WooCommerce.
	add_theme_support( 'woocommerce', array(
		'thumbnail_image_width' => 800,
		'single_image_width'    => 1400,
		'product_grid'          => array( 'default_columns' => 4, 'min_columns' => 1, 'max_columns' => 4 ),
	) );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	load_theme_textdomain( 'garageristeriet', get_template_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'gr_setup' );

/**
 * Stilark.
 */
function gr_styles() {
	wp_enqueue_style( 'garageristeriet', get_stylesheet_uri(), array(), GR_VERSION );
}
add_action( 'wp_enqueue_scripts', 'gr_styles' );

/**
 * Egen kategori til temaets patterns.
 */
function gr_pattern_category() {
	if ( function_exists( 'register_block_pattern_category' ) ) {
		register_block_pattern_category( 'garageristeriet', array(
			'label'       => __( 'GarageRisteriet', 'garageristeriet' ),
			'description' => __( 'Sektioner fra onepager-designet.', 'garageristeriet' ),
		) );
	}
}
add_action( 'init', 'gr_pattern_category' );

/**
 * Produktgrid: 3 op paa desktop.
 */
function gr_loop_columns() {
	return 4;
}
add_filter( 'loop_shop_columns', 'gr_loop_columns' );

/**
 * Danske knaptekster i WooCommerce-loopet.
 */
function gr_add_to_cart_text( $text, $product = null ) {
	// Et variabelt produkt kan ikke laegges i kurv fra griddet — knappen er et
	// link til produktsiden, hvor vaegt og formaling vaelges. Saa maa der ikke
	// staa "Laeg i kurv" paa den; det lover noget klikket ikke goer.
	if ( $product instanceof WC_Product
		&& ( $product->is_type( 'variable' ) || ! $product->is_purchasable() || ! $product->supports( 'ajax_add_to_cart' ) ) ) {
		return __( 'Læs mere', 'garageristeriet' );
	}
	return __( 'Læg i kurv', 'garageristeriet' );
}
// 10, 2 — ikke bare 10. Uden antallet kommer $product aldrig frem, og saa
// falder hver eneste knap tilbage paa "Laeg i kurv" uden at noget fejler.
add_filter( 'woocommerce_product_add_to_cart_text', 'gr_add_to_cart_text', 10, 2 );

/**
 * Ingen Google Fonts udefra (GDPR) - fonte hostes lokalt via theme.json.
 */
function gr_remove_remote_fonts() {
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_webfonts' );
}
add_action( 'init', 'gr_remove_remote_fonts' );

/**
 * Saetter temaets eget logo som sitelogo, hvis der ikke allerede er valgt et.
 */
function gr_default_logo() {
	if ( get_theme_mod( 'custom_logo' ) ) {
		return;
	}

	$file = get_template_directory() . '/assets/logo/garageristeriet-lockup-black.png';
	if ( ! file_exists( $file ) ) {
		return;
	}

	$existing = get_posts( array(
		'post_type'   => 'attachment',
		'name'        => 'garageristeriet-lockup-black',
		'numberposts' => 1,
		'fields'      => 'ids',
	) );

	if ( $existing ) {
		set_theme_mod( 'custom_logo', $existing[0] );
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$upload = wp_upload_bits( 'garageristeriet-lockup-black.png', null, file_get_contents( $file ) );
	if ( ! empty( $upload['error'] ) ) {
		return;
	}

	$attachment_id = wp_insert_attachment( array(
		'post_mime_type' => 'image/png',
		'post_title'     => 'GarageRisteriet',
		'post_status'    => 'inherit',
	), $upload['file'] );

	if ( is_wp_error( $attachment_id ) ) {
		return;
	}

	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
	set_theme_mod( 'custom_logo', $attachment_id );
}
add_action( 'after_switch_theme', 'gr_default_logo' );

/**
 * Dansk prisformat: "94,00 kr." i stedet for "kr. 94,00".
 * Fjern denne funktion, hvis butikken skal have kr. foran igen.
 */
function gr_price_format( $format, $currency_pos ) {
	return '%2$s&nbsp;%1$s';
}
add_filter( 'woocommerce_price_format', 'gr_price_format', 10, 2 );

require_once get_template_directory() . '/inc/updater.php';
require_once get_template_directory() . '/inc/grind.php';
