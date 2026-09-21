/**
 * Klik-for-at-hente kort.
 *
 * Der ligger med vilje ingen <iframe> i sidens kilde. Google saetter cookies
 * og modtager den besoegendes IP-adresse i det oejeblik et Maps-iframe
 * indlaeses — ikke naar nogen klikker paa det — og cookiepolitikken paa
 * garageristeriet.dk lover ordret at forsiden ikke saetter en eneste cookie
 * og derfor ikke har nogen cookiebanner. Et almindeligt embed ville goere den
 * saetning usand samme sekund det blev lagt ind.
 *
 * Derfor bygges iframen foerst her, af et klik. Foer det er der ikke sendt et
 * eneste kald til google.com. Det er samtykke ved handling, og det er hele
 * grunden til at det her ikke bare er et <iframe> i skabelonen.
 *
 * <details> duer ikke til det samme: en iframe i et lukket <details> bliver
 * stadig hentet af browseren. Skjult er ikke det samme som ikke hentet.
 *
 * @package GarageRisteriet
 */
( function () {
	'use strict';

	document.addEventListener( 'click', function ( event ) {
		var button = event.target.closest( '.gr-map__load' );

		if ( ! button ) {
			return;
		}

		var box = button.closest( '.gr-map' );

		if ( ! box || ! box.dataset.grMapSrc || box.dataset.grMapLoaded ) {
			return;
		}

		// Saettes foer iframen bygges, saa to hurtige klik ikke giver to kort.
		box.dataset.grMapLoaded = '1';

		var frame = document.createElement( 'iframe' );

		frame.src             = box.dataset.grMapSrc;
		frame.title           = box.dataset.grMapTitle || 'Kort';
		frame.loading         = 'lazy';
		frame.allowFullscreen = true;
		frame.referrerPolicy  = 'no-referrer-when-downgrade';

		box.replaceChildren( frame );

		// Flyt fokus med over. Uden det staar en tastaturbruger tilbage paa en
		// knap der ikke findes laengere, og fokus falder til toppen af siden.
		frame.focus();
	} );
}() );
