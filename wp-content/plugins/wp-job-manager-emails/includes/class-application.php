<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Emails_Application
 *
 * @since 2.0.0
 *
 */
class WP_Job_Manager_Emails_Application extends WP_Job_Manager_Emails_CPT_Application {


	/**
	 * WP_Job_Manager_Emails_Applications constructor.
	 *
	 * @param WP_Job_Manager_Emails_Integration
	 */
	public function __construct( $integration ) {

		$this->integration = $integration;

		// Custom post type constructor
		parent::__construct();

		// Hooks
		$this->hooks = new WP_Job_Manager_Emails_Hooks_Application( $this );
		// Shortcodes
		$this->shortcodes = new WP_Job_Manager_Emails_Shortcodes_Application( $this );
		// Admin
		if( is_admin() ) $this->admin = new WP_Job_Manager_Emails_Admin_Application( $this );
	}

	/**
	 * Set CPT Variable Configuration
	 *
	 *
	 * @since @@since
	 *
	 */
	function set_config(){

		$this->slug = 'application';
		$this->singular = $this->get_label();
		$this->plural = $this->get_name();
		$this->post_type = 'jm_app_emails';
		$this->ppost_type = 'job_application';
		$this->capability = 'manage_job_application_emails';
		$this->menu = 'edit.php?post_type=job_application';

	}

	/**
	 * Get Job Application Listing Post Type Singular Label
	 *
	 *
	 * @since @@since
	 *
	 * @return string
	 */
	public function get_label() {

		$ja_obj  = get_post_type_object( 'job_application' );
		$singular = is_object( $ja_obj ) ? $ja_obj->labels->singular_name : __( 'Application', 'wp-job-manager-emails' );

		return $singular;

	}

	/**
	 * Get Job Application Post Type Plural Label
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_name() {

		$ja_obj  = get_post_type_object( 'job_application' );
		$plural = is_object( $ja_obj ) ? $ja_obj->labels->name : __( 'Applications', 'wp-job-manager-emails' );

		return $plural;

	}

	/**
	 * Get All Job Application Fields
	 *
	 *
	 * @since @@since
	 *
	 * @return array
	 */
	function get_fields() {

		if ( ! empty( $this->fields ) ) return $this->fields;

		$this->fields = get_job_application_form_fields();

		return $this->fields;

	}

	/**
	 * Send Job Application Email
	 *
	 *
	 * @since @@since
	 *
	 * @param array|string        $templates
	 * @param bool|integer $job_id
	 * @param bool|integer $app_id
	 * @param bool|integer $resume_id
	 *
	 * @return bool
	 */
	function send_email( $templates, $job_id = false, $app_id = false, $resume_id = false ){

		/**
		 * String means hook name was passed instead of array of emails
		 */
		if ( is_string( $templates ) && ! empty( $templates ) ) {
			$templates = $this->get_emails( $templates );
		}

		if ( empty( $templates ) ) {
			return FALSE;
		}

		$this->shortcodes()->set_job_id( $job_id );
		$this->shortcodes()->set_app_id( $app_id );

		// Must be ran after setting app and job id
		if( ! $resume_id ){
			$resume_id = $this->shortcodes()->get_resume_id();
		}

		if( $resume_id ) $this->shortcodes()->set_resume_id( $resume_id );

		$emails = new WP_Job_Manager_Emails_Emails_Application( $this );
		$emails->queue( $templates, $app_id );

		$this->shortcodes()->clear_ids();

		return true;
	}
}