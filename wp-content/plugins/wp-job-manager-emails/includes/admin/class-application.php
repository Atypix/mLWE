<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Emails_Admin_Application extends WP_Job_Manager_Emails_Admin {

	/**
	 * WP_Job_Manager_Emails_Job_Admin constructor.
	 *
	 * @param $cpt WP_Job_Manager_Emails_CPT_Application
	 */
	public function __construct( $cpt ) {

		$this->cpt       = $cpt;
		$this->help      = new WP_Job_Manager_Emails_Admin_Help_Application( $cpt );
		$this->ajax      = new WP_Job_Manager_Emails_Admin_Ajax( $cpt );
		$this->settings  = new WP_Job_Manager_Emails_Admin_Settings_Application( $cpt );
		$this->listtable = new WP_Job_Manager_Emails_Admin_ListTable( $cpt );

		// Load assets
		//parent::__construct();

		add_action( 'admin_init', array( $this, 'check_install' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'init_pointers' ) );
	}

	/**
	 * Initialize and Add Pointers to Pointer Class
	 *
	 * This method should be called be the `admin_enqueue_scripts` action which will then initialize a new instance
	 * of the pointers class, with the pointer configuration, and set $this->pointers to that class' instance.
	 *
	 * @since 1.0.0
	 *
	 */
	function init_pointers(){

		$pointers = array(
			array(
				'id'       => 'jm-application-emails-template-default-tab',
				'screen'   => 'jm_app_emails',
				'target'   => '#htm_default_tab_link',
				'title'    => __( 'Additional Templates', 'wp-job-manager-emails' ),
				'content'  => __( 'Click here to view the Default Templates (recommended for sending emails).  If themes or other plugins add templates, you will see additional tabs next to this one.', 'wp-job-manager-emails' ),
				'position' => array(
					'edge'  => 'bottom', // top, bottom, left, right
					'align' => 'top' // top, bottom, left, right, middle
				),
			),
			array(
				'id'       => 'jm-application-emails-sc-tab-pointer',
				'screen'   => 'jm_app_emails',
				'target'   => '#shortcodes-tab',
				'title'    => __( 'Shortcodes List', 'wp-job-manager-emails' ),
				'content'  => __( 'Click on this tab to show a list of available shortcodes to use in your email.  Click on one to add it to the template, click outside of it to close the sidebar.', 'wp-job-manager-emails' ),
				'position' => array(
					'edge'  => 'right', // top, bottom, left, right
					'align' => 'right' // top, bottom, left, right, middle
				),
			),
			array(
				'id'       => 'jm-application-emails-templates-pointer',
				'screen'   => 'jm_app_emails',
				'target'   => '#templates-media-btn',
				'title'    => __( 'Email Templates', 'wp-job-manager-emails' ),
				'content'  => __( 'Click this button to open the Email Templates modal, which provides numerous amount of email templates which you can preview, and then use if you decide to.', 'wp-job-manager-emails' ),
				'position' => array(
					'edge'  => 'top', // top, bottom, left, right
					'align' => 'left' // top, bottom, left, right, middle
				),
			),
			array(
				'id'       => 'jm-application-emails-help-pointer',
				'screen'   => 'jm_app_emails',
				'target'   => '#contextual-help-link-wrap',
				'title'    => __( 'Shortcodes, Documentation, and Help ...', 'wp-job-manager-emails' ),
				'content'  => __( 'You can find documentation on how to use shortcodes, the supported arguments, and example code, as well as additional help information, by clicking on this help dropdown link.', 'wp-job-manager-emails' ),
				'position' => array(
					'edge'  => 'top', // top, bottom, left, right
					'align' => 'right' // top, bottom, left, right, middle
				),
			),
			array(
				'id'       => 'jm-application-emails-menu-item',
				'screen'   => 'all',
				'target'   => '#menu-posts-job_application',
				'title'    => __( 'Email Templates', 'wp-job-manager-emails' ),
				'content'  => __( 'You can find Application email templates here, under the Applications main menu item.', 'wp-job-manager-emails' ),
				'position' => array(
					'edge'  => 'left', // top, bottom, left, right
					'align' => 'right' // top, bottom, left, right, middle
				),
			),
		);

		$this->pointers = new WP_Job_Manager_Emails_Admin_Pointers( $pointers );
	}
}