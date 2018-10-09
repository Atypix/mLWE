jQuery( document ).ready( function($) {

	/**
	 * When Changing Status Show Email Notification Options.
	 */
	$( '#claim-status' ).change( function() {
		var old_value = $(this).data( 'old-status' );
		var new_value = $(this).val();
		if( new_value !== old_value ){
			$( '#email-notification-options' ).slideDown();
		}
		else{
			$( '#email-notification-options' ).slideUp();
		}
	});

	/**
	 * Edit Claimer
	 * 
	 * 
	 */
	$( '#edit-claimer' ).click( function(e) {
		e.preventDefault();
		$( this ).parents( '.claimer-name' ).hide();
		$( '#claimer-input' ).show().focus();
	});

});