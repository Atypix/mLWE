<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Emails_Resume extends WP_Job_Manager_Emails_CPT_Resume {


	/**
	 * WP_Job_Manager_Emails_Resume constructor.
	 *
	 * @param WP_Job_Manager_Emails_Integration
	 */
	public function __construct( $integration ) {

		$this->integration = $integration;

		// Custom post type constructor
		parent::__construct();

		// Hooks
		$this->hooks = new WP_Job_Manager_Emails_Hooks_Resume( $this );
		// Shortcodes
		$this->shortcodes = new WP_Job_Manager_Emails_Shortcodes_Resume( $this );
		// Admin
		if( is_admin() ) $this->admin = new WP_Job_Manager_Emails_Admin_Resume( $this );
	}

	/**
	 * Set CPT Variable Configuration
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	function set_config(){

		$this->slug = 'resume';
		$this->singular = $this->get_label();
		$this->plural = $this->get_name();
		$this->post_type = 'jm_resume_emails';
		$this->ppost_type = 'resume';
		$this->capability = 'manage_resume_emails';
		$this->menu = 'edit.php?post_type=resume';

	}

	/**
	 * Get Resume Listing Post Type Singular Label
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_label() {

		$resume_obj  = get_post_type_object( 'resume' );
		$singular = is_object( $resume_obj ) ? $resume_obj->labels->singular_name : __( 'Resume', 'wp-job-manager-emails' );

		return $singular;

	}

	/**
	 * Get Job Listing Post Type Plural Label
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_name() {

		$resume_obj  = get_post_type_object( 'resume' );
		$plural = is_object( $resume_obj ) ? $resume_obj->labels->name : __( 'Resumes', 'wp-job-manager-emails' );

		return $plural;

	}

	/**
	 * Get All Resume Fields
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function get_fields() {

		if( ! empty( $this->fields ) ) return $this->fields;

		$form = new WP_Job_Manager_Emails_Integration_Resume_Form();

		$this->fields = $form->get_all_fields();

		return $this->fields;

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

		// IF taxonomy found in resume fields, set to slug otherwise set to false
		$taxonomy_slug = is_array( $fields ) && isset( $fields[$meta_key], $fields[$meta_key]['taxonomy'] ) ? $fields[$meta_key]['taxonomy'] : FALSE;

		return $taxonomy_slug;
	}

	/**
	 * Send Resume Email
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param array|string $templates
	 * @param bool|integer|string $resume_id
	 * @param bool|integer|string $job_id
	 *
	 * @return bool
	 */
	function send_email( $templates, $resume_id = false, $job_id = false ){

		/**
		 * String means hook name was passed instead of array of emails
		 */
		if ( is_string( $templates ) && ! empty( $templates ) ) {
			$templates = $this->get_emails( $templates );
		}

		if ( empty( $templates ) ) {
			return FALSE;
		}

		$this->shortcodes->set_resume_id( $resume_id );
		$this->shortcodes->set_job_id( $job_id );

		$emails = new WP_Job_Manager_Emails_Emails_Resume( $this );
		$emails->queue( $templates, $resume_id );

		$this->shortcodes()->clear_ids();

		return true;
	}
}