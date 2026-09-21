<?php
/**
 * Formaling som valg paa kurv-linjen.
 *
 * Formaling aendrer ikke prisen, saa den er ikke en variation. I stedet
 * gemmes valget som linjedata paa kurven — saa slipper vi for 30+
 * variationer pr. kaffe, og vaegt-chipsene kan blive i produktgriddet.
 *
 * Valget foelger hele vejen: kurv, kasse, ordre og ordrebekraeftelsen,
 * saa det staar paa den seddel, I pakker efter.
 *
 * @package GarageRisteriet
 */

defined( 'ABSPATH' ) || exit;

/**
 * De formalinger vi tilbyder. Filtrerbar, hvis listen skal aendres.
 *
 * @return array
 */
function gr_grind_options() {
	return apply_filters(
		'gr_grind_options',
		array(
			'hele-bonner'  => __( 'Hele bønner', 'garageristeriet' ),
			'stempelkande' => __( 'Stempelkande', 'garageristeriet' ),
			'filter'       => __( 'Filter', 'garageristeriet' ),
			'espresso'     => __( 'Espresso', 'garageristeriet' ),
			'moka'         => __( 'Moka', 'garageristeriet' ),
		)
	);
}

/**
 * Feltet i produktgriddet og paa produktsiden.
 *
 * @param string $uid Unikt praefiks til id'er.
 */
function gr_grind_field( $uid = 'gr-grind' ) {
	$options = gr_grind_options();
	$id      = $uid . '-formaling';
	?>
	<div class="gr-field gr-grind">
		<label class="gr-field__label" for="<?php echo esc_attr( $id ); ?>">
			<?php echo esc_html__( 'Formaling', 'garageristeriet' ); ?>
		</label>
		<span class="gr-select">
			<select class="gr-control" id="<?php echo esc_attr( $id ); ?>" name="gr_formaling">
				<?php foreach ( $options as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
			<span class="gr-select__chev" aria-hidden="true">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
			</span>
		</span>
	</div>
	<?php
}

/**
 * Har produktet formaling som rigtig variations-egenskab?
 *
 * Hvis ja, skal vi ikke vise vores eget felt — saa ville kunden se to.
 *
 * @param WC_Product $product Produktet.
 * @return bool
 */
function gr_product_has_grind_attribute( $product ) {
	if ( ! $product instanceof WC_Product || ! $product->is_type( 'variable' ) ) {
		return false;
	}

	foreach ( array_keys( $product->get_variation_attributes() ) as $taxonomy ) {
		$haystack = remove_accents( strtolower( $taxonomy ) );
		if ( false !== strpos( $haystack, 'formaling' ) || false !== strpos( $haystack, 'grind' ) || false !== strpos( $haystack, 'maling' ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Kan dette produkt overhovedet males?
 *
 * Feltet blev foer vist paa ALT der ikke havde formaling som variation, og
 * det lovede noget der ikke findes: paa /vare/colombia-to-go/ stod der en
 * formalingsvaelger paa en brewbag. En brewbag er faerdigpakket.
 *
 * @param WC_Product $product Produktet.
 * @return bool
 */
function gr_product_takes_grind( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return false;
	}

	// Et grupperet produkt er ikke én kaffe, det er en raekke kaffer, og
	// formularen sender ét gr_formaling for dem alle. Valget ville blive
	// haeftet paa hver linje i gruppen paa én gang.
	if ( $product->is_type( array( 'grouped', 'external' ) ) ) {
		return false;
	}

	// Kategorier hvor formaling ikke giver mening. Brewbags er faerdigpakkede,
	// et gavekort er ikke kaffe. Filtrerbar, saa listen kan aendres uden at
	// roere temaet.
	$excluded = apply_filters( 'gr_grind_excluded_categories', array( 'brewbags', 'gavekort' ) );
	if ( $excluded && has_term( $excluded, 'product_cat', $product->get_id() ) ) {
		return false;
	}

	// Har den formaling som rigtig variation, ville kunden se to vaelgere.
	if ( gr_product_has_grind_attribute( $product ) ) {
		return false;
	}

	return true;
}

/**
 * Viser feltet paa den almindelige produktside — men kun hvor det giver
 * mening. Se gr_product_takes_grind().
 */
function gr_grind_field_single() {
	global $product;

	if ( ! gr_product_takes_grind( $product ) ) {
		return;
	}

	gr_grind_field( 'gr-grind-single-' . $product->get_id() );
}
add_action( 'woocommerce_before_add_to_cart_button', 'gr_grind_field_single', 20 );

/**
 * Gemmer valget paa kurv-linjen.
 *
 * @param array $cart_item_data Eksisterende linjedata.
 * @return array
 */
function gr_add_grind_to_cart_item( $cart_item_data ) {
	if ( empty( $_POST['gr_formaling'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return $cart_item_data;
	}

	$value   = sanitize_key( wp_unslash( $_POST['gr_formaling'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$options = gr_grind_options();

	if ( isset( $options[ $value ] ) ) {
		$cart_item_data['gr_formaling'] = $value;
	}

	return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'gr_add_grind_to_cart_item', 10, 1 );

/**
 * Viser valget i kurven og paa kassen.
 *
 * @param array $item_data Linjer der vises under produktnavnet.
 * @param array $cart_item Kurv-linjen.
 * @return array
 */
function gr_display_grind_in_cart( $item_data, $cart_item ) {
	if ( empty( $cart_item['gr_formaling'] ) ) {
		return $item_data;
	}

	$options = gr_grind_options();
	$value   = $cart_item['gr_formaling'];

	if ( isset( $options[ $value ] ) ) {
		$item_data[] = array(
			'key'   => __( 'Formaling', 'garageristeriet' ),
			'value' => $options[ $value ],
		);
	}

	return $item_data;
}
add_filter( 'woocommerce_get_item_data', 'gr_display_grind_in_cart', 10, 2 );

/**
 * Gemmer valget paa ordrelinjen, saa det staar paa pakkesedlen.
 *
 * @param WC_Order_Item_Product $item   Ordrelinjen.
 * @param string                $key    Kurv-linjens noegle.
 * @param array                 $values Kurv-linjen.
 */
function gr_save_grind_on_order_item( $item, $key, $values ) {
	if ( empty( $values['gr_formaling'] ) ) {
		return;
	}

	$options = gr_grind_options();
	$value   = $values['gr_formaling'];

	if ( isset( $options[ $value ] ) ) {
		$item->add_meta_data( __( 'Formaling', 'garageristeriet' ), $options[ $value ] );
	}
}
add_action( 'woocommerce_checkout_create_order_line_item', 'gr_save_grind_on_order_item', 10, 3 );

/**
 * Holder linjer med forskellig formaling adskilt i kurven.
 *
 * @param string $key            Den genererede noegle.
 * @param int    $product_id     Produkt-id.
 * @param int    $variation_id   Variation-id.
 * @param array  $cart_item_data Linjedata.
 * @return string
 */
function gr_unique_cart_key( $key, $product_id, $variation_id, $cart_item_data ) {
	if ( ! empty( $cart_item_data['gr_formaling'] ) ) {
		$key = md5( $key . $cart_item_data['gr_formaling'] );
	}
	return $key;
}
add_filter( 'woocommerce_cart_id', 'gr_unique_cart_key', 10, 4 );
