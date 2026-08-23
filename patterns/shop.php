<?php
/**
 * Title: Kaffe / Shop
 * Slug: garageristeriet/shop
 * Categories: garageristeriet
 * Viewport Width: 1400
 * Description: Produktgrid med WooCommerce Product Collection – ankersektionen #kaffe.
 *
 * @package garageristeriet
 */

?>
<!-- wp:group {"tagName":"section","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"anchor":"kaffe","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull" id="kaffe" style="padding-top:var(--wp--preset--spacing--60);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--30)">
	<!-- wp:paragraph {"align":"center","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.14em","fontWeight":"600"}},"textColor":"sage","fontSize":"small"} -->
	<p class="has-text-align-center has-sage-color has-text-color has-small-font-size" style="font-weight:600;letter-spacing:0.14em;text-transform:uppercase"><?php esc_html_e( 'Webshop', 'garageristeriet' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"textAlign":"center"} -->
	<h2 class="wp-block-heading has-text-align-center"><?php esc_html_e( 'Vores kaffe', 'garageristeriet' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"align":"center"} -->
	<p class="has-text-align-center"><?php esc_html_e( 'Alle kaffer ristes på bestilling og sendes som hele bønner eller friskmalet – lige som du vil have det.', 'garageristeriet' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:woocommerce/product-collection {"queryId":10,"query":{"perPage":8,"pages":1,"offset":0,"postType":"product","order":"asc","orderBy":"title","search":"","exclude":[],"inherit":false,"taxQuery":[],"isProductCollectionBlock":true,"featured":false,"woocommerceOnSale":false,"woocommerceStockStatus":["instock","onbackorder"],"woocommerceAttributes":[],"woocommerceHandPickedProducts":[]},"tagName":"div","displayLayout":{"type":"flex","columns":4,"shrinkColumns":true},"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-woocommerce-product-collection alignwide" style="margin-top:var(--wp--preset--spacing--50)">
		<!-- wp:woocommerce/product-template -->
			<!-- wp:woocommerce/product-image {"isDescendentOfQueryLoop":true,"saleBadgeAlign":"left"} /-->

			<!-- wp:post-title {"level":3,"isLink":true,"fontSize":"large","__woocommerceNamespace":"woocommerce/product-collection/product-title","style":{"typography":{"fontWeight":"500"},"spacing":{"margin":{"top":"0.75rem","bottom":"0.25rem"}}}} /-->

			<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true} /-->

			<!-- wp:woocommerce/product-button {"isDescendentOfQueryLoop":true} /-->
		<!-- /wp:woocommerce/product-template -->

		<!-- wp:woocommerce/product-collection-no-results -->
		<div class="wp-block-woocommerce-product-collection-no-results">
			<!-- wp:paragraph {"align":"center"} -->
			<p class="has-text-align-center"><?php esc_html_e( 'Der er ingen produkter endnu – tilføj dine kaffer under Produkter i WooCommerce.', 'garageristeriet' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:woocommerce/product-collection-no-results -->
	</div>
	<!-- /wp:woocommerce/product-collection -->
</section>
<!-- /wp:group -->
