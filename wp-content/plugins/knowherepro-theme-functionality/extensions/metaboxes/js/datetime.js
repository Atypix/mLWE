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
			$timestamp = $this.siblings( '.mad-datetime-timestamp' ),
			current = $this.val(),
			$picker = $inline.length ? $inline : $this;

		$this.siblings( '.ui-datepicker-append' ).remove(); // Remove appended text
		if ( $timestamp.length ) {
			options.onClose = options.onSelect = function () {
				$timestamp.val( getTimestamp( $picker.datetimepicker( 'getDate' ) ) );
			};
		}

		if ( $inline.length ) {
			options.altField = '#' + $this.attr( 'id' );
			$this.on( 'keydown', _.debounce( function () {
				$picker
					.datepicker( 'setDate', $this.val() )
					.find( ".ui-datepicker-current-day" )
					.trigger( "click" );
			}, 600 ) );

			$inline
				.removeClass( 'hasDatepicker' )
				.empty()
				.prop( 'id', '' )
				.datetimepicker( options )
				.datetimepicker( 'setDate', current );
		}
		else {
			$this.removeClass( 'hasDatepicker' ).datetimepicker( options );
		}
	}

	/**
	 * Convert date to Unix timestamp in milliseconds
	 * @link http://stackoverflow.com/a/14006555/556258
	 * @param date
	 * @return number
	 */
	function getTimestamp( date ) {
		if ( date === null ) {
			return "";
		}
		var milliseconds = Date.UTC( date.getFullYear(), date.getMonth(), date.getDate(), date.getHours(), date.getMinutes(), date.getSeconds() );
		return Math.floor( milliseconds / 1000 );
	}

	// Set language if available
	$.datepicker.setDefaults( $.datepicker.regional[""] );
	if ( $.datepicker.regional.hasOwnProperty( MAD_Datetime.locale ) ) {
		$.datepicker.setDefaults( $.datepicker.regional[MAD_Datetime.locale] );
	}
	else if ( $.datepicker.regional.hasOwnProperty( MAD_Datetime.localeShort ) ) {
		$.datepicker.setDefaults( $.datepicker.regional[MAD_Datetime.localeShort] );
	}
	$.timepicker.setDefaults( $.timepicker.regional[""] );
	if ( $.timepicker.regional.hasOwnProperty( MAD_Datetime.locale ) ) {
		$.timepicker.setDefaults( $.timepicker.regional[MAD_Datetime.locale] );
	}
	else if ( $.timepicker.regional.hasOwnProperty( MAD_Datetime.localeShort ) ) {
		$.timepicker.setDefaults( $.timepicker.regional[MAD_Datetime.localeShort] );
	}

	$( ':input.mad-datetime' ).each( update );
	$( '.mad-input' ).on( 'clone', ':input.mad-datetime', update );
} );
