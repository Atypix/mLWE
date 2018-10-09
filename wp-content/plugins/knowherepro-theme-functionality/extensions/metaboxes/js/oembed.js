jQuery( function ( $ ) {
	'use strict';

	/**
	 * Show preview of oembeded media.
	 */
	function showPreview( e ) {
		e.preventDefault();

		var $this = $( this ),
			$spinner = $this.siblings( '.spinner' ),
			data = {
				action: 'mad_get_embed',
				url: $this.siblings( 'input' ).val()
			};

		$spinner.css( 'visibility', 'visible' );
		$.post( ajaxurl, data, function ( r ) {
			$spinner.css( 'visibility', 'hidden' );
			$this.siblings( '.mad-embed-media' ).html( r.data );
		}, 'json' );
	}

	/**
	 * Remove oembed preview when cloning.
	 */
	function removePreview() {
		$( this ).siblings( '.mad-embed-media' ).html( '' );
	}

	// Show oembeded media when clicking "Preview" button
	$( 'body' ).on( 'click', '.mad-embed-show', showPreview );

	// Remove oembed preview when cloning
	$( '.mad-input' ).on( 'clone', '.mad-oembed', removePreview );
} );
