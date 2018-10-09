<?php
/**
 * Settings
 *
 * @since 3.0.0
 */
namespace wpjmcl\wpjm_listing_payments;
if ( ! defined( 'WPINC' ) ) { die; }


/* Load Class */
Settings::get_instance();

/**
 * Settings Class
 */
final class Settings {

	/**
	 * Construct
	 */
	public function __construct() {

		/* Add WP Job Manager Settings */
		add_filter( 'job_manager_settings', array( $this, 'add_settings' ), 12 );

		/* Sanitize Options */
		add_filter( 'sanitize_option_wpjmcl_paid_claiming', function( $input ) {
			return $input ? 1 : 0;
		} );

	}

	/**
	 * Returns the instance.
	 *
	 * @since  3.0.0
	 */
	public static function get_instance() {
		static $instance = null;
		if ( is_null( $instance ) ) { $instance = new self;
		}
		return $instance;
	}

	/**
	 * Settings
	 */
	function add_settings( $settings ) {
		if ( ! isset( $settings['wpjmcl_settings'] ) ) { return $settings;
		}

		/* Add Heading */
		$settings['wpjmcl_settings'][1][] = array(
			'name'       => 'wpjmcl_heading',
			'type'       => 'wpjmcl_heading',
			'label'      => __( 'Listing Payments for WP Job Manager', 'wp-job-manager-claim-listing' ),
			'desc'       => '',
		);

		/* Allow claimer submit claim data */
		$settings['wpjmcl_settings'][1][] = array(
			'name'      => 'wpjmcl_paid_claiming',
			'std'       => '',
			'label'     => __( 'Paid Claims', 'wp-job-manager-claim-listing' ),
			'cb_label'  => __( 'Require a purchase', 'wp-job-manager-claim-listing' ),
			'desc'      => __( 'A listing is claimed by purchasing a listing package.', 'wp-job-manager-claim-listing' ),
			'type'      => 'checkbox',
		);

		/* Register on Checkout */
		$settings['wpjmcl_settings'][1][] = array(
			'name'      => 'wpjmcl_register_on_checkout',
			'std'       => '',
			'label'     => __( 'Register on Checkout', 'wp-job-manager-claim-listing' ),
			'cb_label'  => __( 'Register on Checkout', 'wp-job-manager-claim-listing' ),
			'desc'      => __( 'No login/register form and use WooCommerce checkout registration.', 'wp-job-manager-claim-listing' ),
			'type'      => 'checkbox',
		);

		return $settings;
	}

}
