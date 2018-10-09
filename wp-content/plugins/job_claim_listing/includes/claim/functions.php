<?php
/**
 * Claim POst Type Functions
 *
 * @since 3.0.0
 **/
namespace wpjmcl\claim;
use wpjmcl\job_listing\Functions as Listing;

/**
 * Reuseable Functions to Handle Claim Post Type.
 *
 * @since 1.0.0
 */
final class Functions {

	/**
	 * Claim Statuses
	 *
	 * @since 3.0.0
	 */
	public static function claim_statuses() {
		$statuses = array(
			'approved'   => __( 'Approved', 'wp-job-manager-claim-listing' ),
			'pending'    => __( 'Pending', 'wp-job-manager-claim-listing' ),
			'declined'   => __( 'Declined', 'wp-job-manager-claim-listing' ),
		);
		return apply_filters( 'wpjmcl_claim_statuses', $statuses );
	}

	/**
	 * Default Claim Status
	 *
	 * @since 3.0.0
	 */
	public static function claim_status_default() {
		return apply_filters( 'wpjmcl_claim_status_default', 'pending' );
	}

	/**
	 * Sanitize status to make sure status is valid.
	 *
	 * @since 3.0.0
	 */
	public static function sanitize_claim_status( $status ) {
		$default = self::claim_status_default();
		if ( ! $status ) { return $default;
		}
		$statuses = self::claim_statuses();
		if ( array_key_exists( $status, $statuses ) ) {
			return $status;
		}
		return $default;
	}

	/**
	 * Get Claim Status.
	 *
	 * @since 3.0.0
	 */
	public static function get_claim_status( $claim_id ) {
		$status = self::sanitize_claim_status( get_post_meta( $claim_id, '_status', true ) );
		return $status;
	}

	/**
	 * Get Claim Status Label.
	 *
	 * @since 3.0.0
	 */
	public static function get_claim_status_label( $claim_id ) {
		$status = self::get_claim_status( $claim_id );
		$statuses = self::claim_statuses();
		$status_label = isset( $statuses[ $status ] ) ? $statuses[ $status ] : __( 'Unknown', 'wp-job-manager-claim-listing' );
		return $status_label;
	}

	/**
	 * Create New Claim
	 *
	 * @param $listing_id   intval    job_listing post_id.
	 * @param $user_id      numeric   claimer user id
	 * @param $claim_data   string
	 * @return mixed false if fail, numeric claim id if success.
	 * @since 3.0.0
	 */
	public static function create_new_claim( $listing_id, $user_id, $claim_data = '', $context = 'front' ) {

		/* Check listing */
		if ( ! Listing::is_claimable( $listing_id ) ) { return false;
		}

		/* Get listing post object */
		$listing_obj = get_post( $listing_id );

		/* Create Claim */
		$post_data = array(
			'post_author'  => $user_id,
			'post_title'   => $listing_obj->post_title,
			'post_type'    => 'claim',
			'post_status'  => 'publish',
		);
		$claim_id = wp_insert_post( $post_data );
		if ( ! is_wp_error( $claim_id ) ) {

			/* Update Status */
			add_post_meta( $claim_id, '_status', 'pending' );

			/* Listing ID */
			add_post_meta( $claim_id, '_listing_id', intval( $listing_id ) );

			/* User ID */
			add_post_meta( $claim_id, '_user_id', intval( $user_id ) );

			/* Claim Data */
			if ( $claim_data ) {
				add_post_meta( $claim_id, '_claim_data', wp_kses_post( $claim_data ) );
			}

			do_action( 'wpjmcl_create_new_claim', $claim_id, $context );

			return $claim_id;
		}
		return false;
	}

	/**
	 * Get all needed data from claim in an array.
	 *
	 * @since 3.0.0
	 * @return mixed false if fail, array of data on success.
	 */
	public static function get_data( $claim_id ) {

		/* Vars */
		$claim_obj = get_post( $claim_id );
		$claimer_obj = get_userdata( $claim_obj->post_author );
		$listing_id = get_post_meta( $claim_id, '_listing_id', true );
		$listing_obj = get_post( $listing_id );

		/* Bail if not complete data */
		if ( ! $claim_obj || ! $listing_obj ) {
			return false;
		}

		/* Datas */
		$claim_edit_url = add_query_arg( array(
			'post'   => $claim_id,
			'action' => 'edit',
		), admin_url( 'post.php' ) );
		$claim_url = add_query_arg( array(
			'listing_id' => $listing_id,
		), get_permalink( job_manager_get_page_id( 'claim_listing' ) ) );

		/* Output */
		$datas = array(
			/* Claim */
			'claim_id'         => $claim_id,
			'claim_title'      => get_the_title( $claim_id ),
			'claim_date'       => get_the_date( get_option( 'date_format' ), $claim_id ),
			'claim_status'     => self::get_claim_status_label( $claim_id ),
			'claim_edit_url'   => $claim_edit_url,
			'claim_url'        => $claim_url,
			/* Claimer */
			'claimer_id'       => $claimer_obj ? $claimer_obj->ID : 0,
			'claimer_name'     => $claimer_obj ? $claimer_obj->data->display_name : '',
			'claimer_login'    => $claimer_obj ? $claimer_obj->data->user_login : '',
			'claimer_email'    => $claimer_obj ? $claimer_obj->data->user_email : '',
			/* Listing */
			'listing_id'       => $listing_id,
			'listing_title'    => get_the_title( $claim_id ),
			'listing_url'      => get_permalink( $listing_id ),
		);

		return apply_filters( 'wpjmcl_claim_get_data', $datas, $claim_id );
	}

}


