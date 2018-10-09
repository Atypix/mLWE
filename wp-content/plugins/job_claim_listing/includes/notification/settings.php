<?php
/**
 * Notification Settings
 *
 * @since 3.8.0
 */
namespace wpjmcl\notification;
if ( ! defined( 'WPINC' ) ) {
	die;
}

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
		add_filter( 'job_manager_settings', array( $this, 'add_settings' ), 15 );

		/* Sanitize Options */
		add_filter( 'sanitize_option_wpjmcl_email_message_new_claim_claimer', __NAMESPACE__ . '\Functions::sanitize_email_message' );
		add_filter( 'sanitize_option_wpjmcl_email_message_new_claim_admin', __NAMESPACE__ . '\Functions::sanitize_email_message' );
		add_filter( 'sanitize_option_wpjmcl_email_message_status_update_claimer', __NAMESPACE__ . '\Functions::sanitize_email_message' );
		add_filter( 'sanitize_option_wpjmcl_email_message_status_update_claimer', __NAMESPACE__ . '\Functions::sanitize_email_message' );
	}

	/**
	 * Returns the instance.
	 *
	 * @since  3.0.0
	 */
	public static function get_instance() {
		static $instance = null;
		if ( is_null( $instance ) ) {
			$instance = new self;
		}
		return $instance;
	}

	/**
	 * Settings
	 */
	function add_settings( $settings ) {
		if ( ! isset( $settings['wpjmcl_settings'] ) ) {
			return $settings;
		}

		/* Add Heading */
		$settings['wpjmcl_settings'][1][] = array(
			'name'       => 'wpjmcl_heading',
			'type'       => 'wpjmcl_heading',
			'label'      => __( 'Notification', 'wp-job-manager-claim-listing' ),
			'desc'       => '',
		);

		/* New Claim Content: Claimer */
		$settings['wpjmcl_settings'][1][] = array(
			'name'      => 'wpjmcl_email_message_new_claim_claimer',
			'std'       => Functions::default_email_message_new_claim_claimer(),
			'label'     => __( 'New Claim Email Content For Claimer', 'wp-job-manager-claim-listing' ),
			'desc'      => __( 'Available tag: <br/> %claimer_name%, %claim_date%, %listing_url%, %claim_status%, %claim_url%', 'wp-job-manager-claim-listing' ),
			'type'      => 'textarea',
		);

		/* New Claim Content: Admin */
		$settings['wpjmcl_settings'][1][] = array(
			'name'      => 'wpjmcl_email_message_new_claim_admin',
			'std'       => Functions::default_email_message_new_claim_admin(),
			'label'     => __( 'New Claim Email Content For Admin', 'wp-job-manager-claim-listing' ),
			'desc'      => __( 'Available tag: <br/> %claimer_name%, %claim_date%, %listing_url%, %claim_status%, %claim_url%, %claim_edit_url%', 'wp-job-manager-claim-listing' ),
			'type'      => 'textarea',
		);

		/* Status Update Content: Claimer */
		$settings['wpjmcl_settings'][1][] = array(
			'name'      => 'wpjmcl_email_message_status_update_claimer',
			'std'       => Functions::default_email_message_status_update_claimer(),
			'label'     => __( 'Status Update Email Content For Claimer', 'wp-job-manager-claim-listing' ),
			'desc'      => __( 'Available tag: <br/> %claimer_name%, %claim_date%, %listing_url%, %claim_status%, %claim_status_old%, %claim_url%', 'wp-job-manager-claim-listing' ),
			'type'      => 'textarea',
		);

		/* Status Update Content: Admin */
		$settings['wpjmcl_settings'][1][] = array(
			'name'      => 'wpjmcl_email_message_status_update_admin',
			'std'       => Functions::default_email_message_status_update_admin(),
			'label'     => __( 'Status Update Email Content For Admin', 'wp-job-manager-claim-listing' ),
			'desc'      => __( 'Available tag: <br/> %claimer_name%, %claim_date%, %listing_url%, %claim_status%, %claim_status_old%, %claim_url%, %claim_edit_url%', 'wp-job-manager-claim-listing' ),
			'type'      => 'textarea',
		);

		return $settings;
	}

}
