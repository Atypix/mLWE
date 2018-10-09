<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Emails_Admin_Settings {

	/**
	 * WP_Job_Manager_Emails_Admin_Job
	 *
	 * @var WP_Job_Manager_Emails_Admin_Job|WP_Job_Manager_Emails_Admin_Resume|WP_Job_Manager_Emails_Application
	 * @since  0.1.0
	 */
	protected $type = NULL;

	public function __construct( $type ) {
		$this->type = $type;
		$this->hooks();
	}

	/**
	 * Add Email Tab with Default Settings
	 *
	 * This method will add a tab to settings page with the default settings that
	 * are used for all emails (job, resume, etc)
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $settings
	 *
	 * @return mixed
	 */
	function add_tab( $settings ) {

		$slug = $this->type->get_slug();
		/* If we don't have an email from the input headers default to wordpress@$sitename
		 * Some hosts will block outgoing mail from this address if it doesn't exist but
		 * there's no easy alternative. Defaulting to admin_email might appear to be another
		 * option but some hosts may refuse to relay mail from an unknown domain. See
		 * https://core.trac.wordpress.org/ticket/5007.
		 */

		// Get the site domain and get rid of www.
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );
		if( substr( $sitename, 0, 4 ) == 'www.' ) {
			$sitename = substr( $sitename, 4 );
		}

		$from_email = 'wordpress@' . $sitename;

		$settings_fields = array(
			array(
				'name'        => "job_manager_emails_{$slug}_default_from_name",
				'placeholder' => apply_filters( 'wp_mail_from_name', 'WordPress' ),
				'label'       => __( 'Default From Name', 'wp-job-manager-emails' ),
				'desc'        => sprintf( __( 'Default name to use in %s emails (can also be set per template).', 'wp-job-manager-emails' ), ucfirst( $slug ) ),
				'std'         => ''
			),
			array(
				'name'        => "job_manager_emails_{$slug}_default_from_email",
				'placeholder' => apply_filters( 'wp_mail_from', $from_email ),
				'label'       => __( 'Default From Email', 'wp-job-manager-emails' ),
				'desc'        => sprintf( __( 'Default email to use in %s emails (can also be set per template).', 'wp-job-manager-emails' ), ucfirst( $slug ) ),
				'std'         => ''
			),
			array(
				'name'     => "job_manager_emails_{$slug}_enable_debug",
				'std'      => '0',
				'cb_label' => sprintf( __( 'Enable %s Email Debugging', 'wp-job-manager-emails' ), $this->type->get_label() ),
				'label'    => __( 'Debug', 'wp-job-manager-emails' ),
				'desc'     => sprintf( __( 'Enable debug logging for %s emails.', 'wp-job-manager-emails' ), ucfirst( $slug ) ),
				'type'     => 'checkbox'
			),
		);

		if( method_exists( $this, 'add_fields' ) ) $settings_fields = array_merge( $settings_fields, $this->add_fields() );

		$settings['emails'] = array(
			__( 'Emails', 'wp-job-manager-emails' ),
			$settings_fields
		);

		return $settings;
	}

	function hooks(){}
}