<?php
/**
 * Title: Hero
 * Slug: garageristeriet/hero
 * Categories: garageristeriet
 * Block Types: core/cover
 * Viewport Width: 1400
 * Description: Fuldbredde hero med stemningsbillede, overskrift og CTA der scroller til shoppen.
 *
 * @package garageristeriet
 */

?>
<!-- wp:cover {"url":"<?php echo esc_url( get_theme_file_uri( 'assets/images/placeholder-hero.svg' ) ); ?>","dimRatio":10,"overlayColor":"base","isUserOverlayColor":true,"minHeight":88,"minHeightUnit":"vh","contentPosition":"center center","align":"full","className":"gr-hero","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-cover alignfull gr-hero" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60);min-height:88vh">
	<span aria-hidden="true" class="wp-block-cover__background has-base-background-color has-background-dim-10 has-background-dim"></span>
	<img class="wp-block-cover__image-background" alt="" src="<?php echo esc_url( get_theme_file_uri( 'assets/images/placeholder-hero.svg' ) ); ?>" data-object-fit="cover" />
	<div class="wp-block-cover__inner-container">
		<!-- wp:paragraph {"align":"center","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.14em","fontWeight":"600"}},"textColor":"primary","fontSize":"small"} -->
		<p class="has-text-align-center has-primary-color has-text-color has-small-font-size" style="font-weight:600;letter-spacing:0.14em;text-transform:uppercase"><?php esc_html_e( 'Specialkaffe · Ristet på Djursland', 'garageristeriet' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"textAlign":"center","level":1,"textColor":"contrast","fontSize":"xxx-large"} -->
		<h1 class="wp-block-heading has-text-align-center has-contrast-color has-text-color has-xxx-large-font-size"><?php esc_html_e( 'Friskristet kaffe fra Ebeltoft', 'garageristeriet' ); ?></h1>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"align":"center","textColor":"contrast","fontSize":"large"} -->
		<p class="has-text-align-center has-contrast-color has-text-color has-large-font-size"><?php esc_html_e( 'Små portioner, nøje udvalgte bønner og en garage der dufter af nyristet kaffe.', 'garageristeriet' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}}} -->
		<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--30)">
			<!-- wp:button -->
			<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#kaffe"><?php esc_html_e( 'Køb kaffe', 'garageristeriet' ); ?></a></div>
			<!-- /wp:button -->

			<!-- wp:button {"className":"is-style-outline"} -->
			<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#om-os"><?php esc_html_e( 'Vores historie', 'garageristeriet' ); ?></a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
</div>
<!-- /wp:cover -->
