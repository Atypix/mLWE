jQuery( document ).ready( function( $ ){

	/**
	 * Helper function to track submission via AJAX
	 */
	function formSubmitted() {
		var that = $( this );
		var wrap = $( this ).closest( '.application_details' );
		if( ! wrap.hasClass( 'wpjms_submitted' ) ){

			/* Ajax */
			wp.ajax.post( 'wpjms_stat_apply_form_submit', {
				nonce      : wpjms_stat_afs.ajax_nonce,
				post_id    : wpjms_stat_afs.post_id,
			} )
			.done( function( data ) {
				wrap.addClass( 'wpjms_submitted' );
			} )
			.fail( function( data ) {
				return;
			} );

		}
	}

	/* On form submit */
	$( 'body' ).on( 'submit', '.application_details form', function(){
		formSubmitted();
	});

	if ( typeof nfRadio !== 'undefined' ) {
		nfRadio.channel( 'forms' ).on( 'submit:response', function() {
			formSubmitted();
		});
	}

});