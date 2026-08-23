<?php
/**
 * Title: Om os
 * Slug: garageristeriet/om-os
 * Categories: garageristeriet
 * Viewport Width: 1400
 * Description: To-kolonne sektion med billede og historien om risteriet – ankersektionen #om-os.
 *
 * @package garageristeriet
 */

?>
<!-- wp:group {"tagName":"section","align":"full","backgroundColor":"surface","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"anchor":"om-os","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull has-surface-background-color has-background" id="om-os" style="padding-top:var(--wp--preset--spacing--60);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--30)">
	<!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-columns alignwide are-vertically-aligned-center">
		<!-- wp:column {"verticalAlignment":"center","width":"45%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:45%">
			<!-- wp:image {"sizeSlug":"full","linkDestination":"none","style":{"border":{"radius":"8px"}}} -->
			<figure class="wp-block-image size-full has-custom-border"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/placeholder-om-os.svg' ) ); ?>" alt="<?php esc_attr_e( 'Stiliseret illustration af en kop friskbrygget kaffe', 'garageristeriet' ); ?>" style="border-radius:8px" /></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"center","width":"55%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%">
			<!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.14em","fontWeight":"600"}},"textColor":"sage","fontSize":"small"} -->
			<p class="has-sage-color has-text-color has-small-font-size" style="font-weight:600;letter-spacing:0.14em;text-transform:uppercase"><?php esc_html_e( 'Om os', 'garageristeriet' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:heading -->
			<h2 class="wp-block-heading"><?php esc_html_e( 'Det startede i en garage i Ebeltoft', 'garageristeriet' ); ?></h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p><?php esc_html_e( 'GarageRisteriet begyndte som en hobby med en lille trommerister og en stor nysgerrighed. I dag rister vi stadig hver eneste portion selv – i garagen, med vinduerne på klem og duften af kaffe ud over hele kvarteret.', 'garageristeriet' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph -->
			<p><?php esc_html_e( 'Vi køber bønner fra små farme og kooperativer, rister lyst til mellemristet for at bevare bønnernes egen karakter, og pakker først kaffen når du har bestilt den. Friskere bliver det ikke.', 'garageristeriet' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}}} -->
			<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--30)">
				<!-- wp:button {"className":"is-style-outline"} -->
				<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#ristning"><?php esc_html_e( 'Sådan rister vi', 'garageristeriet' ); ?></a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</section>
<!-- /wp:group -->
