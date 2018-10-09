jQuery( function ( $ ) {
	'use strict';

	/**
	 * Update date picker element
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
				$timestamp.val( getTimestamp( $picker.datepicker( 'getDate' ) ) );
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
				.datepicker( options )
				.datepicker( 'setDate', current );
		}
		else {
			$this.removeClass( 'hasDatepicker' ).datepicker( options );
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

	$.datepicker.setDefaults( $.datepicker.regional[""] );
	if ( $.datepicker.regional.hasOwnProperty( MAD_Date.locale ) ) {
		$.datepicker.setDefaults( $.datepicker.regional[MAD_Date.locale] );
	}
	else if ( $.datepicker.regional.hasOwnProperty( MAD_Date.localeShort ) ) {
		$.datepicker.setDefaults( $.datepicker.regional[MAD_Date.localeShort] );
	}

	$( ':input.mad-date' ).each( update );
	$( '.mad-input' ).on( 'clone', ':input.mad-date', update );
} );
