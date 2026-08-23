<?php
/**
 * Title: Kontakt og nyhedsbrev
 * Slug: garageristeriet/kontakt
 * Categories: garageristeriet
 * Viewport Width: 1400
 * Description: Kontaktinfo, åbningstider og plads til nyhedsbrevsformular – ankersektionen #kontakt.
 *
 * @package garageristeriet
 */

?>
<!-- wp:group {"tagName":"section","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"anchor":"kontakt","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull" id="kontakt" style="padding-top:var(--wp--preset--spacing--60);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--30)">
	<!-- wp:paragraph {"align":"center","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.14em","fontWeight":"600"}},"textColor":"sage","fontSize":"small"} -->
	<p class="has-text-align-center has-sage-color has-text-color has-small-font-size" style="font-weight:600;letter-spacing:0.14em;text-transform:uppercase"><?php esc_html_e( 'Kontakt', 'garageristeriet' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"textAlign":"center"} -->
	<h2 class="wp-block-heading has-text-align-center"><?php esc_html_e( 'Kig forbi garagen', 'garageristeriet' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|40"},"margin":{"top":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-columns alignwide" style="margin-top:var(--wp--preset--spacing--50)">
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":3,"fontSize":"large"} -->
			<h3 class="wp-block-heading has-large-font-size"><?php esc_html_e( 'Find os', 'garageristeriet' ); ?></h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p><?php esc_html_e( 'GarageRisteriet', 'garageristeriet' ); ?><br><?php esc_html_e( 'Garagevej 1', 'garageristeriet' ); ?><br><?php esc_html_e( '8400 Ebeltoft', 'garageristeriet' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph -->
			<p><a href="mailto:hej@garageristeriet.dk">hej@garageristeriet.dk</a><br><a href="tel:+4512345678">+45 12 34 56 78</a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":3,"fontSize":"large"} -->
			<h3 class="wp-block-heading has-large-font-size"><?php esc_html_e( 'Åbningstider', 'garageristeriet' ); ?></h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p><?php esc_html_e( 'Fredag: 14–17 (afhentning og smagsprøver)', 'garageristeriet' ); ?><br><?php esc_html_e( 'Lørdag: 10–13', 'garageristeriet' ); ?><br><?php esc_html_e( 'Øvrige dage efter aftale', 'garageristeriet' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":3,"fontSize":"large"} -->
			<h3 class="wp-block-heading has-large-font-size"><?php esc_html_e( 'Nyhedsbrev', 'garageristeriet' ); ?></h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"fontSize":"small"} -->
			<p class="has-small-font-size"><?php esc_html_e( 'Få besked når nye kaffer lander – cirka én mail om måneden, ingen spam.', 'garageristeriet' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:group {"className":"gr-newsletter-placeholder","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-group gr-newsletter-placeholder" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">
				<!-- wp:paragraph {"align":"center","textColor":"sage","fontSize":"small"} -->
				<p class="has-text-align-center has-sage-color has-text-color has-small-font-size"><?php esc_html_e( 'Erstat denne blok med din nyhedsbrevsformular (fx Mailchimp, MailPoet eller Brevo).', 'garageristeriet' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</section>
<!-- /wp:group -->
