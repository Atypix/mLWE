jQuery( function ( $ ) {
	'use strict';

	$( 'body' ).on( 'change', '.mad-image-select input', function () {
		var $this = $( this ),
			type = $this.attr( 'type' ),
			selected = $this.is( ':checked' ),
			$parent = $this.parent(),
			$others = $parent.siblings();
		if ( selected ) {
			$parent.addClass( 'mad-active' );
			if ( type === 'radio' ) {
				$others.removeClass( 'mad-active' );
			}
		} else {
			$parent.removeClass( 'mad-active' );
		}
	} );
	$( '.mad-image-select input' ).trigger( 'change' );
} );
