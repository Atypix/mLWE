<?php
/**
 * Functions
 *
 * @since 3.0.0
 **/
namespace wpjmcl\job_listing;

/**
 * Job Listing Functions
 *
 * @since 3.0.0
 */
final class Functions {

	/**
	 * Is Claimable
	 *
	 * @return bool true if we can claim it. false if cannot claim it/invalid.
	 */
	public static function is_claimable( $job_listing_id ) {
		$claimable = true;

		/* Check if listing entry exist */
		$post_obj = get_post( $job_listing_id );

		if ( ! $post_obj ) {
			$claimable = false;
		}

		/* Check post type */
		if ( 'job_listing' !== $post_obj->post_type ) {
			$claimable = false;
		}

		/* Check status. */
		if ( 'preview' === $post_obj->post_status ) {
			$claimable = false;
		}

		/* Check if it's already claimed/verified. */
		$claimed = get_post_meta( $job_listing_id, '_claimed', true );

		if ( 1 == $claimed ) {
			$claimable = false;
		}

		return apply_filters( 'wpjmcl_is_claimable', $claimable, $post_obj );
	}


	/**
	 * Get Submit Claim URL
	 *
	 * @since 3.0.0
	 */
	public static function submit_claim_url( $job_listing_id ) {

		/* Claim Listing Page URL */
		$submit_claim_page_url = job_manager_get_permalink( 'claim_listing' );
		if ( ! $submit_claim_page_url ) { return false; // page not set.
		}

		/* Job Listing Check */
		if ( ! self::is_claimable( $job_listing_id ) ) {
			return false;
		}

		/* Check if it's the author of the listing */
		if ( is_user_logged_in() ) {
			$post_obj = get_post( $job_listing_id );
			$curr_user_id = get_current_user_id();
			$can_claim_own_listing = get_option( 'wpjmcl_claim_own_listing', false );
			if ( ! $can_claim_own_listing && $curr_user_id == $post_obj->post_author ) {
				return false;
			}
		}

		/* Build URL */
		$url = add_query_arg( array(
			'listing_id' => $job_listing_id,
		), $submit_claim_page_url );

		return esc_url( $url );
	}


	/**
	 * Get Submit Claim Link
	 *
	 * @since 3.0.0
	 */
	public static function submit_claim_link( $job_listing_id ) {

		/* Var */
		$link = '';

		/* Get Submit Claim URL */
		$submit_claim_url = self::submit_claim_url( $job_listing_id );

		/* Link HTML */
		if ( $submit_claim_url ) {
			$link = '<a href="' . esc_url( $submit_claim_url ) . '" class="claim-listing"><span>' . __( 'Claim this listing', 'wp-job-manager-claim-listing' ) . '</span></a>';
		}

		/* Filter The output. */
		return apply_filters( 'wpjmcl_submit_claim_link', $link, $job_listing_id, $submit_claim_url );
	}

	/**
	 * Update lising on claim approval
	 *
	 * @since 3.1.0
	 */
	public static function update_listing_on_claim_approval( $claim_id, $listing_id ) {

		/* Package ID */
		$package_id = get_post_meta( $claim_id, '_package_id', true );

		/* Check if package exist and WooCommerce Active */
		if ( $package_id && class_exists( 'WooCommerce' ) ) {

			/* Get WC Product */
			$package = wc_get_product( $package_id );

			/* Set Listing Data */
			update_post_meta( $listing_id, '_job_duration', $package->get_duration() );

			if ( 'job_package_subscription' === $package->get_type() && 'listing' === $package->get_package_subscription_type() ) {
				update_post_meta( $listing_id, '_job_expires', '' ); // Never expire automatically.
			} else {
				$expire_time = calculate_job_expiry( $listing_id );
				if ( $expire_time ) {
					update_post_meta( $listing_id, '_job_expires', $expire_time );
				}
			}

			// Paid listings.
			if ( function_exists( 'wp_job_manager_wcpl_init' ) ) {
				update_post_meta( $listing_id, '_featured', $package->is_featured() ? 1 : 0 );
				wp_update_post( array(
					'ID' => $listing_id,
					'menu_order' => $package->is_featured() ? -1 : 0,
				) );
			} elseif ( defined( 'ASTOUNDIFY_WPJMLP_PLUGIN' ) ) { // Listing Payments.
				update_post_meta( $listing_id, '_featured', $package->is_listing_featured() ? 1 : 0 );
				wp_update_post( array(
					'ID' => $listing_id,
					'menu_order' => $package->is_listing_featured() ? -1 : 0,
				) );
			}
		}
	}

}
