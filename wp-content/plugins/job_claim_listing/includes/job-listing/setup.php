<?php
/**
 * Job Listing Setup
 *
 * @since 3.0.0
 */

/* Set NameSpace */
namespace wpjmcl\job_listing;
use wpjmcl\claim\Functions as Claim;

/* Prevent direct access */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/* Load Class */
Setup::get_instance();

/**
 * Setup Job Listing Post Type
 *
 * @since 3.0.0
 */
final class Setup {

	/**
	 * Construct
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		/* Add claim link in listing entry */
		add_action( 'single_job_listing_start', array( $this, 'add_claim_link' ) );

		/* Filter "submit claim" link */
		add_filter( 'wpjmcl_submit_claim_link', array( $this, 'add_user_notice' ), 10, 3 );
		add_filter( 'wpjmcl_submit_claim_link', array( $this, 'add_verified_badge' ), 99, 3 );

		/* Front End Scripts */
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );

		/* Set as Claimed if claim status approved, unclaimed otherwise. */
		add_action( 'wpjmcl_claim_status_updated', array( $this, 'set_listing_claim_status' ), 10, 3 );

		/* Add Post Class */
		add_filter( 'post_class', array( $this, 'add_post_class' ), 10, 3 );
	}

	/**
	 * Returns the instance.
	 *
	 * @since  3.0.0
	 *
	 * @return null|object
	 */
	public static function get_instance() {
		static $instance = null;
		if ( is_null( $instance ) ) {
			$instance = new self;
		};
		return $instance;
	}

	/**
	 * Add Claim Link
	 *
	 * @since 3.0.0
	 **/
	public function add_claim_link() {
		$job_listing_id = get_the_ID();
		echo Functions::submit_claim_link( $job_listing_id );
	}

	/**
	 * Replace claim link with notice if user already submit claim to listing.
	 * This is database extensive process. So It's best to be used on single listing page.
	 *
	 * @since 3.0.0
	 *
	 * @param string $link
	 * @param int    $job_listing_id
	 * @param string $url
	 * @return string $link
	 */
	public function add_user_notice( $link, $job_listing_id, $url ) {
		if ( $link && is_user_logged_in() ) {
			$curr_user_id = get_current_user_id();

			/* Get claims matched with listing ID and current user */
			$claims = get_posts( array(
				'post_type'      => 'claim',
				'posts_per_page' => 1, // only one.
				'author'         => $curr_user_id,
				'meta_key'       => '_listing_id',
				'meta_value'     => $job_listing_id,
			) );

			/* Match found. */
			if ( $claims && isset( $claims[0]->ID ) ) {
				$claim_status = Claim::get_claim_status_label( $claims[0]->ID );

				$link  = '<span class="claim-user-notice">';
				$link .= ' <a href="' . $url . '">' . sprintf( __( 'View %s Claim', 'wp-job-manager-claim-listing' ), $claim_status ) . '</a>';
				$link .= '</span>';
			}
		}

		return $link;
	}


	/**
	 * Replace claim link with verify badge, if listing is claimed.
	 *
	 * @since 3.0.0
	 *
	 * @param string $link
	 * @param int    $job_listing_id
	 * @param string $url
	 * @return string $link
	 */
	public function add_verified_badge( $link, $job_listing_id, $url ) {
		$claimed = get_post_meta( $job_listing_id, '_claimed', true );
		if ( 1 == $claimed ) {
			$link = '<span class="claim-verified">' . __( 'Verified Listing', 'wp-job-manager-claim-listing' ) . '</span>';
			$link = apply_filters( 'wpjmcl_claim_verified_badge', $link );
		}
		return $link;
	}


	/**
	 * Scripts
	 *
	 * @since 3.0.0
	 */
	function scripts() {

		if ( apply_filters( 'wpjmcl_job-listing_front_css', true ) ) {
			wp_enqueue_style( 'wpjmcl_job-listing_front', URI . 'assets/front.css', array(), VERSION );
		}
	}


	/**
	 * Set Listing Claimed If claim status set to approved/unclaimed if unset.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $claim_id
	 * @param string $old_status
	 * @param array  $request
	 * @return void
	 */
	function set_listing_claim_status( $claim_id, $old_status, $request ) {

		/* Claim Data */
		$claim_obj = get_post( $claim_id );
		$claimer_id = $claim_obj->post_author;
		$claim_status = get_post_meta( $claim_id, '_status', true );

		/* Listing Data */
		$listing_id = get_post_meta( $claim_id, '_listing_id', true );
		$listing_obj = get_post( $listing_id );
		if ( ! $listing_obj ) {
			return false;
		}
		$listing_claimed = get_post_meta( $listing_id, '_claimed', true );

		/* Status is approved */
		if ( ( 'approved' == $claim_status ) && ! $listing_claimed ) {

			/* Set to claimed */
			update_post_meta( $listing_id, '_claimed', 1 );

			/* Change Listing Author */
			$args = array(
				'ID'          => $listing_id,
				'post_author' => $claimer_id,
			);
			wp_update_post( $args );
		} // End if().

		else {
			delete_post_meta( $listing_id, '_claimed' );
		}
	}


	/**
	 * Add Post Class
	 *
	 * @since 3.0.0
	 *
	 * @param array  $classes
	 * @param string $class
	 * @param int    $post_id
	 * @return array $classes
	 */
	public function add_post_class( $classes, $class, $post_id ) {
		$post_type = get_post_type( $post_id );
		if ( 'job_listing' == $post_type ) {
			$claimed = get_post_meta( $post_id, '_claimed', true );
			$classes[] = $claimed ? 'claimed' : 'not-claimed';
		}
		return $classes;
	}

}

