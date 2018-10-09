jQuery( function ( $ ) {
	'use strict';

	function mad_update_slider() {
		var $input = $( this ),
			$slider = $input.siblings( '.mad-slider' ),
			$valueLabel = $slider.siblings( '.mad-slider-value-label' ).find( 'span' ),
			value = $input.val(),
			options = $slider.data( 'options' );


		$slider.html( '' );

		if ( ! value ) {
			value = 0;
			$input.val( 0 );
			$valueLabel.text( '0' );
		}
		else {
			$valueLabel.text( value );
		}

		// Assign field value and callback function when slide
		options.value = value;
		options.slide = function ( event, ui ) {
			$input.val( ui.value );
			$valueLabel.text( ui.value );
		};

		$slider.slider( options );
	}

	$( ':input.mad-slider-value' ).each( mad_update_slider );
	$( '.mad-input' ).on( 'clone', ':input.mad-slider-value', mad_update_slider );
} );
