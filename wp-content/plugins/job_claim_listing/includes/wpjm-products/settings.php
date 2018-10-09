<?php
/**
 * Settings
 *
 * @since 3.0.0
 */
namespace wpjmcl\wpjm_products;
if ( ! defined( 'WPINC' ) ) { die; }


/* Load Class */
Settings::get_instance();


/**
 * Settings
 */
final class Settings {

	/**
	 * Construct
	 */
	public function __construct() {

		/* Add WP Job Manager Settings */
		add_filter( 'job_manager_settings', array( $this, 'add_settings' ), 14 );

		/* Sanitize Options */
		add_filter( 'sanitize_option_wpjmcl_transfer_product_ownership', function( $input ) {
			return $input ? 1 : 0;
		} );
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
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
			'label'      => __( 'WP Job Manager - Products', 'wp-job-manager-claim-listing' ),
			'desc'       => '',
		);

		/* Allow claimer submit claim data */
		$settings['wpjmcl_settings'][1][] = array(
			'name'      => 'wpjmcl_transfer_product_ownership',
			'std'       => '',
			'label'     => __( 'Product Owner', 'wp-job-manager-claim-listing' ),
			'cb_label'  => __( 'Transfer product owner when a listing is claimed', 'wp-job-manager-claim-listing' ),
			'desc'      => __( 'When a listing is claimed, the product owner for any attached products will not change unless this option is enabled. This gives ownership to the person claiming the listing.', 'wp-job-manager-claim-listing' ),
			'type'      => 'checkbox',
		);

		return $settings;
	}

}

