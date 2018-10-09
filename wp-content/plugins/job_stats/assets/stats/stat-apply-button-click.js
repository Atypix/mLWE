jQuery( document ).ready( function( $ ){

	/* Click apply button */
	$( 'body' ).on( 'click', '.application_button.button', function(e){

		var that = $( this );
		if( ! that.hasClass( 'wpjms_clicked' ) ){

			/* Ajax */
			wp.ajax.post( 'wpjms_stat_apply_button_click', {
				nonce      : wpjms_stat_abc.ajax_nonce,
				post_id    : wpjms_stat_abc.post_id,
			} )
			.done( function( data ) {
				that.addClass( 'wpjms_clicked' );
			} )
			.fail( function( data ) {
				return;
			} );

		}
	});
});