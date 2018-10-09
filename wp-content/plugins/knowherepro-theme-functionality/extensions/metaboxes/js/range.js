jQuery( function ( $ ) {
	'use strict';

	/**
	 * Update color picker element
	 * Used for static & dynamic added elements (when clone)
	 */
	function update() {
		var $this = $( this ),
			$output = $this.siblings( '.mad-output' );

		$this.on( 'input propertychange change', function ( e ) {
			$output.html( $this.val() );
		} );

	}

	$( ':input.mad-range' ).each( update );
	$( '.mad-input' ).on( 'clone', 'input.mad-range', update );
} );
