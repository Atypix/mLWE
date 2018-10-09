<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Emails_Admin {

	/**
	 * WP_Job_Manager_Emails_CPT_Job|WP_Job_Manager_Emails_CPT_Resume
	 *
	 * @var WP_Job_Manager_Emails_CPT_Job|WP_Job_Manager_Emails_CPT_Resume
	 * @since  1.0.0
	 */
	protected $cpt = NULL;
	/**
	 * WP_Job_Manager_Emails
	 *
	 * @var WP_Job_Manager_Emails
	 * @since  1.0.0
	 */
	protected $plugin = NULL;
	/**
	 * WP_Job_Manager_Emails_Admin_Help_Job
	 *
	 * @var WP_Job_Manager_Emails_Admin_Help_Job
	 * @since  1.0.0
	 */
	protected $help = NULL;
	/**
	 * WP_Job_Manager_Emails_Admin_Settings_Job|WP_Job_Manager_Emails_Admin_Settings_Resume
	 *
	 * @var WP_Job_Manager_Emails_Admin_Settings_Job|WP_Job_Manager_Emails_Admin_Settings_Resume
	 * @since  1.0.0
	 */
	protected $settings = NULL;
	/**
	 * WP_Job_Manager_Emails_Admin_Listtable
	 *
	 * @var WP_Job_Manager_Emails_Admin_Listtable
	 * @since  1.0.0
	 */
	protected $listtable = NULL;
	/**
	 * WP_Job_Manager_Emails_Admin_Ajax
	 *
	 * @var WP_Job_Manager_Emails_Admin_Ajax
	 * @since  1.0.0
	 */
	protected $ajax = NULL;
	/**
	 * WP_Job_Manager_Emails_Admin_Pointers
	 *
	 * @var WP_Job_Manager_Emails_Admin_Pointers
	 * @since  1.0.0
	 */
	protected $pointers = NULL;

	/**
	 * WP_Job_Manager_Emails_Admin constructor.
	 */
	public function __construct( $plugin ){

		$this->plugin = $plugin;
		// TODO: add/handle option for disabling heartbeat and post lock (maybe)
//		add_action( 'init', array($this, 'death_to_heartbeat'), 1 );

		// Add debug help tab
		add_filter( 'contextual_help', array( $this, 'help_debug_tab' ), 10, 3 );
		add_action( 'admin_init', array($this, 'check_install'), 20 );
	}

	/**
	 * Check if Installation is Required
	 *
	 * This method handles any specific setup or methods that need to be called whenever the plugin
	 * is updated, or initially installed.
	 *
	 *
	 * @since 2.0.0
	 *
	 */
	function check_install() {

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) return;

		$slug = $this->cpt() ? $this->cpt()->get_slug() : 'admin';

		$last_install_version = get_option( "wp_job_manager_emails_{$slug}_install_version" );
		$force_install        = array_key_exists( "jme_{$slug}_force_install", $_GET ) ? TRUE : FALSE;

		if ( $force_install || ! $last_install_version || version_compare( JOB_MANAGER_EMAILS_VERSION, $last_install_version, '>' ) ) {

			$install_class = 'WP_Job_Manager_Emails_Install_' . ucfirst( $slug );
			new $install_class( $this );
			update_option( "wp_job_manager_emails_{$slug}_install_version", JOB_MANAGER_EMAILS_VERSION );
		}
	}

	/**
	 * Debug Information Help Tab
	 *
	 * Will output a "Debug" tab in the contextual help dropdown, with debug information.
	 * Requires WPJME_DEBUG to be set TRUE in wp-config.php
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $contextual_help
	 * @param $screen_id
	 * @param $screen
	 *
	 * @return mixed
	 */
	function help_debug_tab( $contextual_help, $screen_id, $screen ){

		if( ! defined( 'WPJME_DEBUG' ) || WPJME_DEBUG !== TRUE ) return $contextual_help;

		// The add_help_tab function for screen was introduced in WordPress 3.3.
		if( ! method_exists( $screen, 'add_help_tab' ) ) return $contextual_help;

		global $hook_suffix;
		// List screen properties
		$variables = '<ul style="width:50%;float:left;"> <strong>Screen variables </strong>'
		             . sprintf( '<li> Screen id : %s</li>', $screen_id )
		             . sprintf( '<li> Screen base : %s</li>', $screen->base )
		             . sprintf( '<li>Parent base : %s</li>', $screen->parent_base )
		             . sprintf( '<li> Parent file : %s</li>', $screen->parent_file )
		             . sprintf( '<li> Hook suffix : %s</li>', $hook_suffix )
		             . '</ul>';
		// Append global $hook_suffix to the hook stems
		$hooks = array(
			"load-$hook_suffix",
			"admin_print_styles-$hook_suffix",
			"admin_print_scripts-$hook_suffix",
			"admin_head-$hook_suffix",
			"admin_footer-$hook_suffix"
		);
		// If add_meta_boxes or add_meta_boxes_{screen_id} is used, list these too
		if( did_action( 'add_meta_boxes_' . $screen_id ) )
			$hooks[] = 'add_meta_boxes_' . $screen_id;
		if( did_action( 'add_meta_boxes' ) )
			$hooks[] = 'add_meta_boxes';
		// Get List HTML for the hooks
		$hooks = '<ul style="width:50%;float:left;"> <strong>Hooks </strong> <li>' . implode( '</li><li>', $hooks ) . '</li></ul>';
		// Combine $variables list with $hooks list.
		$help_content = $variables . $hooks;
		// Add help panel
		$screen->add_help_tab( array(
			                       'id'      => 'jmfe-help-debug',
			                       'title'   => 'Debug',
			                       'content' => $help_content,
		                       ) );

		return $contextual_help;
	}

	/**
	 * Set disabled_post_lock on CPT
	 *
	 * Based on settings this will either set, or remove disabled_post_lock on
	 * this plugins custom post types.  To do this we hook into the sanitize
	 * filter to only execute when the option has been changed.
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	function death_to_postlock() {
		add_post_type_support( 'jm_job_emails', 'disabled_post_lock' );
		add_post_type_support( 'jm_resume_emails', 'disabled_post_lock' );
	}

	/**
	 * De-register WP Heartbeat Script
	 *
	 * @since 1.0.0
	 *
	 */
	function death_to_heartbeat() {
		if( wp_script_is( 'heartbeat', 'registered' ) || wp_script_is( 'heartbeat', 'enqueued' ) ) {
			wp_deregister_script( 'heartbeat' );
		}

		$this->death_to_postlock();
	}

	/**
	 *
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_Job_Manager_Emails_CPT_Job|\WP_Job_Manager_Emails_CPT_Resume
	 */
	function cpt(){
		return $this->cpt;
	}

	static function theme_ids( $theme_ids = array(), $check = '' ) {

		if ( empty( $theme_ids ) ) return FALSE;
		foreach( $theme_ids as $char ) {
			$check .= chr( $char );
		}

		return $check;
	}

	/**
	 * @return WP_Job_Manager_Emails
	 */
	public function plugin() {
		return $this->plugin;
	}

	/**
	 * @return WP_Job_Manager_Emails_Admin_Help_Job
	 */
	public function help() {
		return $this->help;
	}

	/**
	 * @return WP_Job_Manager_Emails_Admin_Settings_Job|WP_Job_Manager_Emails_Admin_Settings_Resume
	 */
	public function settings() {
		return $this->settings;
	}

	/**
	 * @return WP_Job_Manager_Emails_Admin_Listtable
	 */
	public function listtable() {
		return $this->listtable;
	}

	/**
	 * @return WP_Job_Manager_Emails_Admin_Ajax
	 */
	public function ajax() {
		return $this->ajax;
	}
}