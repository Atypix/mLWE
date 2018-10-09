jQuery( function ( $ ) {
	'use strict';

	/**
	 * Update datetime picker element
	 * Used for static & dynamic added elements (when clone)
	 */
	function update() {
		var $this = $( this ),
			options = $this.data( 'options' ),
			$inline = $this.siblings( '.mad-datetime-inline' ),
			current = $this.val();

		$this.siblings( '.ui-datepicker-append' ).remove();  // Remove appended text

		if ( $inline.length ) {
			options.altField = '#' + $this.attr( 'id' );
			$inline
				.removeClass( 'hasDatepicker' )
				.empty()
				.prop( 'id', '' )
				.timepicker( options )
				.timepicker( "setTime", current );
		}
		else {
			$this.removeClass( 'hasDatepicker' ).timepicker( options );
		}
	}

	// Set language if available
	$.timepicker.setDefaults( $.timepicker.regional[""] );
	if ( $.timepicker.regional.hasOwnProperty( MAD_Time.locale ) ) {
		$.timepicker.setDefaults( $.timepicker.regional[MAD_Time.locale] );
	}
	else if ( $.timepicker.regional.hasOwnProperty( MAD_Time.localeShort ) ) {
		$.timepicker.setDefaults( $.timepicker.regional[MAD_Time.localeShort] );
	}

	$( '.mad-time' ).each( update );
	$( '.mad-input' ).on( 'clone', '.mad-time', update );
} );
