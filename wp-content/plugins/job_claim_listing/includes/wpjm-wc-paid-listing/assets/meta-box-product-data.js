jQuery( document ).ready( function($) {

	/**
	 * Show/Hide Field If Claim Package Selected.
	 */
	function prepare_claim_package( el ) {
		var product_type = $( '#product-type' ).val();

		// Only if job package selected.
		if ( 'job_package' === product_type || 'job_package_subscription' === product_type ) {

			/* Claim listing */
			if( $( el ).is( ":checked") ) {

				/* Set job listing limit to 1. use "readonly" (do not submit the data) */
				$( '#_job_listing_limit' ).val( '1' ).attr( 'readonly', 'readonly' );

				/* Subscription Package: "readonly" attr is not valid attr for "select" el  */
				if ( 'job_package_subscription' === $( '#product-type' ).val() ) {
					$( '#_job_listing_package_subscription_type' ).val( 'listing' ).attr( 'disabled', 'disabled' ).trigger( 'change' );
				}

				/* Hide "Feature Listing" option (not applied to claim listing) */
				//$( '._job_listing_featured_field' ).hide();

				/* Show Default to Claim Option */
				$( '._default_to_claimed_field' ).hide();

			} else { /* Not claim listing */

				/* Allow edit. */
				$( '#_job_listing_limit' ).removeAttr( 'readonly' );
				$( '#_job_listing_package_subscription_type' ).removeAttr( 'disabled' );
				//$( '._job_listing_featured_field' ).show();
				$( '._default_to_claimed_field' ).show();
			}
		}
	}

	/**
	 * Process
	 */
	prepare_claim_package( '#_use_for_claims' ); // initial page load
	$( 'body' ).on( 'change', '#_use_for_claims', function() {
		prepare_claim_package( this );
	} );

});
