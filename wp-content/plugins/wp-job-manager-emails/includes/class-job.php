<?php

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Emails_Job
 *
 * @since 2.0.0
 *
 */
class WP_Job_Manager_Emails_Job extends WP_Job_Manager_Emails_CPT_Job {

	/**
	 * WP_Job_Manager_Emails_Job constructor.
	 *
	 * @param WP_Job_Manager_Emails_Integration
	 */
	public function __construct( $integration ) {

		$this->integration = $integration;

		// Custom post type constructor
		parent::__construct();

		if( $this->alerts_available() ){
			$alerts = new WP_Job_Manager_Emails_Hooks_Job_Alerts( $this );
		}

		if( $this->claim_listing_available() ){
			$claim = new WP_Job_Manager_Emails_Hooks_Job_Claim( $this );
		}

		if( $this->reviews_available() ){
			$reviews = new WP_Job_Manager_Emails_Hooks_Job_Reviews( $this );
		}

		// Hooks
		$this->hooks = new WP_Job_Manager_Emails_Hooks_Job( $this );
		// Shortcodes
		$this->shortcodes = new WP_Job_Manager_Emails_Shortcodes_Job( $this );
		// Admin
		if( is_admin() ) $this->admin = new WP_Job_Manager_Emails_Admin_Job( $this );

	}

	/**
	 * Set CPT Variable Configuration
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	function set_config() {

		$this->slug       = 'job';
		$this->singular   = $this->get_label();
		$this->plural     = $this->get_name();
		$this->post_type  = 'jm_job_emails';
		$this->ppost_type = 'job_listing';
		$this->capability = 'manage_job_emails';
		$this->menu       = 'edit.php?post_type=job_listing';

	}

	/**
	 * Get Job Listing Post Type Singular Label
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return string|void
	 */
	public function get_label() {

		$job_obj  = get_post_type_object( 'job_listing' );
		$singular = is_object( $job_obj ) ? $job_obj->labels->singular_name : __( 'Job', 'wp-job-manager-emails' );

		return $singular;

	}

	/**
	 * Get Plural Post Type Label
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return string|void
	 */
	public function get_name(){

		$job_obj  = get_post_type_object( 'job_listing' );
		$plural = is_object( $job_obj ) ? $job_obj->labels->name : __( 'Jobs', 'wp-job-manager-emails' );

		return $plural;
	}

	/**
	 * Get All Job Fields
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param string $type
	 *
	 * @return array
	 */
	function get_fields( $type = 'all' ){

		$form = new WP_Job_Manager_Emails_Integration_Job_Form();

		switch( $type ){

			case 'job':
				$fields = $form->get_job_fields();
				break;
			case 'company':
				$fields = $form->get_company_fields();
				break;
			case 'combined':
				$fields = $form->get_all_fields( true );
				break;
			default:
				$fields = $form->get_all_fields();
				break;
		}

		return $fields;

	}

	/**
	 * Get taxonomy slug from passed meta key
	 *
	 * Checks for taxonomy configuration in passed meta key, if found it returns the taxonomy slug, otherwise
	 * returns false.
	 *
	 *
	 * @since 2.0.2
	 *
	 * @param $meta_key
	 *
	 * @return bool|string      Returns taxonomy slug if found in field config, otherwise returns FALSE
	 */
	function get_taxonomy_slug( $meta_key ){

		$fields = $this->get_fields();

		// IF taxonomy found in job fields, set to slug otherwise set to false
		$taxonomy_slug = is_array( $fields ) && isset( $fields[$meta_key], $fields[$meta_key]['taxonomy'] ) ? $fields[$meta_key]['taxonomy'] : FALSE;

		return $taxonomy_slug;
	}

	/**
	 * Send Job Email
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param array|string $templates
	 * @param bool         $job_id
	 * @param bool         $alert_id
	 *
	 * @return bool
	 */
	function send_email( $templates, $job_id = FALSE, $alert_id = FALSE ) {

		/**
		 * String means hook name was passed instead of array of emails
		 */
		if( is_string( $templates ) && ! empty( $templates ) ) {
			$this->hooks()->hook = $templates;
			$templates = $this->get_emails( $templates );
		}

		if ( empty( $templates ) ) {
			return FALSE;
		}

		$this->shortcodes()->set_job_id( $job_id );

		if( $alert_id ){
			$this->shortcodes()->set_alert_id( $alert_id );
		}

		$emails = new WP_Job_Manager_Emails_Emails_Job( $this );
		$emails->queue( $templates, $job_id );

		$this->shortcodes()->clear_ids();

		return true;
	}

	function reviews_available() {

		if ( ! class_exists( 'WP_Job_Manager_Reviews' ) ) {

			if ( ! function_exists( 'validate_plugin' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			// validate_plugin returns 0 on success (WP_Error on failure)
			if ( validate_plugin( 'wp-job-manager-reviews/wp-job-manager-reviews.php' ) === 0 ) {
				return true;
			}

			return false;
		}

		return true;

	}

	/**
	 * Check if Alerts Installing/Activated
	 *
	 *
	 * @since 2.0.5
	 *
	 * @return bool
	 */
	function alerts_available() {

		if ( ! defined( 'JOB_MANAGER_ALERTS_PLUGIN_DIR' ) ) {

			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			if ( is_plugin_active( 'wp-job-manager-alerts/wp-job-manager-alerts.php' ) ) {
				return TRUE;
			}
			if ( class_exists( 'WP_Job_Manager_Alerts' ) ) {
				return TRUE;
			}

			return FALSE;
		}

		return TRUE;

	}

	/**
	 * Check if Claim Listing Install/Activated
	 *
	 *
	 * @since 2.0.5
	 *
	 * @return bool
	 */
	function claim_listing_available() {

		if ( ! defined( 'WPJMCL_PATH' ) ) {

			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			if ( is_plugin_active( 'wp-job-manager-claim-listing/wp-job-manager-claim-listing.php' ) ) {
				return TRUE;
			}
			if ( class_exists( 'wpjmcl\\claim\\Setup' ) ) {
				return TRUE;
			}

			return FALSE;
		}

		return TRUE;

	}
}