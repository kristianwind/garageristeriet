<?php
/**
 * Vaegt- og formalingsvaelger direkte i produktgriddet paa forsiden.
 *
 * WooCommerce viser normalt knappen "Vaelg muligheder" paa variable
 * produkter, og den sender kunden videre til produktsiden. Her erstattes
 * knappen med vaegt-chips, formaling og "Laeg i kurv", saa engangskoeb kan
 * klares paa forsiden. Abonnement kraever produktsiden — der ligger
 * pluginnets egne felter.
 *
 * Vaegten styrer prisen og vises som chips. Alle andre egenskaber
 * (typisk formaling) vises som rullelister. Vi sender IKKE variation_id;
 * WooCommerce finder selv den rigtige variation ud fra egenskaberne, saa
 * opsaetningen virker baade med "Alle"-variationer (3 pr. kaffe) og med
 * fuldt udfoldede variationer (15 pr. kaffe).
 *
 * Formularen postes til den aktuelle side, som WooCommerce haandterer paa
 * wp_loaded — den virker derfor ogsaa uden JavaScript.
 *
 * @package GarageRisteriet
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gaetter hvilken egenskab der er vaegt, ud fra taksonominavnet.
 *
 * @param string $taxonomy Egenskabens taksonomi, fx pa_vaegt.
 * @return bool
 */
function gr_is_weight_attribute( $taxonomy ) {
	$needles = array( 'vaegt', 'vægt', 'weight', 'storrelse', 'størrelse', 'size' );
	$haystack = remove_accents( strtolower( $taxonomy ) );

	foreach ( $needles as $needle ) {
		if ( false !== strpos( $haystack, remove_accents( $needle ) ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Bytter loopets add-to-cart ud med vores egen vaelger.
 */
function gr_swap_loop_add_to_cart() {
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	add_action( 'woocommerce_after_shop_loop_item', 'gr_loop_add_to_cart', 10 );

	// Vaelgeren viser selv prisen for den valgte vaegt, saa loopets
	// interval-pris ("94,00 kr. – 293,00 kr.") ville staa dobbelt.
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
	add_action( 'woocommerce_after_shop_loop_item_title', 'gr_loop_price', 10 );
}

/**
 * Loopets pris — springes over paa variable produkter med vores vaelger.
 */
function gr_loop_price() {
	global $product;

	if ( $product instanceof WC_Product
		&& $product->is_type( 'variable' )
		&& apply_filters( 'gr_loop_selector_enabled', true, $product )
		&& ! empty( $product->get_variation_attributes() ) ) {
		return;
	}

	woocommerce_template_loop_price();
}
add_action( 'init', 'gr_swap_loop_add_to_cart' );

/**
 * Udskriver vaelgeren for variable produkter, ellers standardknappen.
 */
function gr_loop_add_to_cart() {
	global $product;

	if ( ! $product instanceof WC_Product || ! $product->is_type( 'variable' ) ) {
		woocommerce_template_loop_add_to_cart();
		return;
	}

	// Produkter der kun kan koebes som abonnement hoerer paa produktsiden.
	if ( ! apply_filters( 'gr_loop_selector_enabled', true, $product ) ) {
		woocommerce_template_loop_add_to_cart();
		return;
	}

	$attributes = $product->get_variation_attributes();

	if ( empty( $attributes ) ) {
		woocommerce_template_loop_add_to_cart();
		return;
	}

	// Del egenskaberne op: vaegt bliver chips, resten bliver rullelister.
	$weight_taxonomy = '';
	$other           = array();

	foreach ( $attributes as $taxonomy => $options ) {
		if ( '' === $weight_taxonomy && gr_is_weight_attribute( $taxonomy ) ) {
			$weight_taxonomy = $taxonomy;
			continue;
		}
		$other[ $taxonomy ] = $options;
	}

	// Uden en genkendt vaegt-egenskab bliver den foerste chips-raekken.
	if ( '' === $weight_taxonomy ) {
		$weight_taxonomy = array_key_first( $attributes );
		unset( $other[ $weight_taxonomy ] );
	}

	// Mere end vaegt + en enkelt ekstra egenskab kan ikke staa i et kort.
	if ( count( $other ) > 1 ) {
		woocommerce_template_loop_add_to_cart();
		return;
	}

	$weight_options = $attributes[ $weight_taxonomy ];
	$weight_field   = 'attribute_' . sanitize_title( $weight_taxonomy );

	// Pris og lagerstatus pr. vaegt. Formaling aendrer ikke prisen, saa
	// foerste variation med den vaegt er nok til visningen.
	$by_weight = array();

	foreach ( $product->get_available_variations() as $variation ) {
		foreach ( $variation['attributes'] as $key => $value ) {
			if ( sanitize_title( $key ) !== sanitize_title( $weight_field ) || '' === $value ) {
				continue;
			}

			$slug = sanitize_title( $value );

			if ( isset( $by_weight[ $slug ] ) && ! $by_weight[ $slug ]['in_stock'] ) {
				// Behold en variation paa lager frem for en udsolgt.
				unset( $by_weight[ $slug ] );
			}

			if ( ! isset( $by_weight[ $slug ] ) ) {
				$by_weight[ $slug ] = array(
					'value'      => $value,
					'price_html' => $variation['price_html'] ? $variation['price_html'] : $product->get_price_html(),
					'in_stock'   => ! empty( $variation['is_in_stock'] ),
				);
			}
		}
	}

	if ( empty( $by_weight ) ) {
		woocommerce_template_loop_add_to_cart();
		return;
	}

	$uid     = 'gr-var-' . $product->get_id();
	$default = $product->get_variation_default_attribute( $weight_taxonomy );
	$first   = '';

	foreach ( $weight_options as $option ) {
		$slug = sanitize_title( $option );

		if ( ! isset( $by_weight[ $slug ] ) || ! $by_weight[ $slug ]['in_stock'] ) {
			continue;
		}

		if ( '' === $first ) {
			$first = $slug;
		}

		if ( $default && sanitize_title( $default ) === $slug ) {
			$first = $slug;
		}
	}

	if ( '' === $first ) {
		echo '<p class="gr-small gr-variations__soldout">' . esc_html__( 'Udsolgt lige nu', 'garageristeriet' ) . '</p>';
		return;
	}
	?>
	<form class="gr-variations" method="post" action="<?php echo esc_url( gr_current_url() ); ?>" data-gr-variations>
		<div class="gr-variations__options" role="group" aria-label="<?php echo esc_attr( wc_attribute_label( $weight_taxonomy ) ); ?>">
			<?php
			foreach ( $weight_options as $option ) {
				$slug = sanitize_title( $option );

				if ( ! isset( $by_weight[ $slug ] ) ) {
					continue;
				}

				$row      = $by_weight[ $slug ];
				$label    = taxonomy_exists( $weight_taxonomy ) ? gr_attribute_label( $weight_taxonomy, $option ) : $option;
				$input_id = $uid . '-' . $slug;
				?>
				<label class="gr-variations__option" for="<?php echo esc_attr( $input_id ); ?>">
					<input
						type="radio"
						id="<?php echo esc_attr( $input_id ); ?>"
						name="<?php echo esc_attr( $weight_field ); ?>"
						value="<?php echo esc_attr( $row['value'] ); ?>"
						data-price="<?php echo esc_attr( $row['price_html'] ); ?>"
						<?php checked( $slug, $first ); ?>
						<?php disabled( ! $row['in_stock'] ); ?>
					>
					<span><?php echo esc_html( $label ); ?></span>
				</label>
				<?php
			}
			?>
		</div>

		<?php
		// Formaling (eller hvad den anden egenskab nu er) som rulleliste.
		foreach ( $other as $taxonomy => $options ) {
			$field    = 'attribute_' . sanitize_title( $taxonomy );
			$field_id = $uid . '-' . sanitize_title( $taxonomy );
			$selected = $product->get_variation_default_attribute( $taxonomy );
			?>
			<div class="gr-field gr-variations__grind">
				<label class="gr-field__label" for="<?php echo esc_attr( $field_id ); ?>">
					<?php echo esc_html( wc_attribute_label( $taxonomy ) ); ?>
				</label>
				<span class="gr-select">
					<select class="gr-control" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field ); ?>">
						<?php
						foreach ( $options as $option ) {
							$label = taxonomy_exists( $taxonomy ) ? gr_attribute_label( $taxonomy, $option ) : $option;
							?>
							<option value="<?php echo esc_attr( $option ); ?>" <?php selected( sanitize_title( $selected ), sanitize_title( $option ) ); ?>>
								<?php echo esc_html( $label ); ?>
							</option>
							<?php
						}
						?>
					</select>
					<span class="gr-select__chev" aria-hidden="true">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
					</span>
				</span>
			</div>
			<?php
		}
		?>

		<div class="gr-variations__row">
			<span class="gr-variations__price" data-gr-price><?php echo wp_kses_post( $by_weight[ $first ]['price_html'] ); ?></span>
			<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>">
			<input type="hidden" name="quantity" value="1">
			<button type="submit" class="gr-btn gr-btn--secondary gr-btn--sm">
				<?php echo esc_html__( 'Læg i kurv', 'garageristeriet' ); ?>
			</button>
		</div>

		<a class="gr-variations__more" href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>">
			<?php echo esc_html__( 'Se kaffen og abonnement', 'garageristeriet' ); ?>
		</a>
	</form>
	<?php
}

/**
 * Laeser den pæne etiket for en egenskabsvaerdi.
 *
 * @param string $taxonomy Egenskabens taksonomi.
 * @param string $value    Vaerdien (slug eller navn).
 * @return string
 */
function gr_attribute_label( $taxonomy, $value ) {
	$term = get_term_by( 'slug', $value, $taxonomy );

	if ( ! $term ) {
		$term = get_term_by( 'name', $value, $taxonomy );
	}

	return $term ? $term->name : $value;
}

/**
 * Den aktuelle URL, saa kunden bliver paa forsiden efter tilfoejelse.
 *
 * @return string
 */
function gr_current_url() {
	// add_query_arg( array() ) giver den aktuelle URI relativt til websted-roden.
	$uri = remove_query_arg( array( 'add-to-cart', 'variation_id', 'quantity' ), add_query_arg( array() ) );
	return esc_url_raw( $uri ) . '#kaffen';
}

/**
 * Lille script: skifter prisen, naar kunden vaelger en anden vaegt.
 */
function gr_variation_script() {
	?>
	<script>
	document.addEventListener('change', function (event) {
		var input = event.target;
		if (!input.matches('[data-gr-variations] input[type="radio"]')) return;
		var form = input.closest('[data-gr-variations]');
		var price = form.querySelector('[data-gr-price]');
		if (price && input.dataset.price) price.innerHTML = input.dataset.price;
	});
	</script>
	<?php
}
add_action( 'wp_footer', 'gr_variation_script', 20 );
