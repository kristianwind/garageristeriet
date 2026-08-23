/**
 * GarageRisteriet – sticky header-tilstand.
 *
 * Transparent header øverst på siden, solid baggrund ved scroll.
 * Holder desuden CSS-variablen --gr-header-h synkroniseret med
 * headerens faktiske højde, så scroll-margin og hero-offset passer.
 */
( function () {
	'use strict';

	var header = document.querySelector( '.gr-header' );

	if ( ! header ) {
		return;
	}

	var setHeaderHeight = function () {
		document.documentElement.style.setProperty(
			'--gr-header-h',
			header.offsetHeight + 'px'
		);
	};

	var ticking = false;

	var onScroll = function () {
		if ( ticking ) {
			return;
		}

		ticking = true;

		window.requestAnimationFrame( function () {
			header.classList.toggle( 'is-scrolled', window.scrollY > 24 );
			ticking = false;
		} );
	};

	setHeaderHeight();
	onScroll();

	window.addEventListener( 'resize', setHeaderHeight );
	window.addEventListener( 'scroll', onScroll, { passive: true } );
} )();
