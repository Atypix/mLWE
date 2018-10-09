<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Emails_Admin_Assets {

	/**
	 * WP_Job_Manager_Emails_Integration
	 *
	 * @var WP_Job_Manager_Emails_Integration
	 * @since  1.0.0
	 */
	protected $integration = NULL;

	/**
	 * WP_Job_Manager_Emails_Admin_Assets constructor.
	 */
	public function __construct( $integration ) {
		$this->integration = $integration;

		add_action( 'admin_enqueue_scripts', array( $this, 'register' ), 99 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ), 999 );
		add_action( 'admin_enqueue_scripts', array( $this, 'localize' ), 9999 );
		add_action( 'admin_enqueue_scripts', array( $this, 'death_to_sloppy_devs' ), 99999 );
	}

	/**
	 * Dequeue scripts/styles that conflict with plugin
	 *
	 * Sloppy developers eneuque their scripts and styles on all pages instead of
	 * only the pages they are needed on.  This almost always causes problems and
	 * to try and prevent this, I dequeue any known scripts/styles that have known
	 * compatibility issues.
	 *
	 * @since @@since
	 *
	 * @param $hook
	 */
	public function death_to_sloppy_devs( $hook ) {

		// Return if not on plugin page, which some devs fail to check!
		if ( ! $this->integration->is_plugin_page() ) {
			return;
		}

		$scripts = array(
			'bootstrap', // Bootstrap 3 Shortcodes
		);

		$styles = array(
			'woocommerce_admin_styles', // YITH WooCommerce Social Login Premium (all KINDS of sloppy enqueues)
		    'cuar.admin', // WP Customer Area (Enqueued on ALL admin pages)
			'ots-common', // Our Team Showcase Plugin (loads on every page)
			'woocommerce_admin', // WooCommerce Email Customizer
			'caldera-forms-admin-styles', // Caldera Forms
			'pods-styles'
		);

		foreach ( $scripts as $script ) {
			if ( wp_script_is( $script, 'enqueued' ) ) {
				wp_dequeue_script( $script );
			} elseif( wp_script_is( $script, 'registered' ) ){
				wp_deregister_script( $script );
			}
		}

		foreach ( $styles as $style ) {
			if ( wp_style_is( $style, 'enqueued' ) ) {
				wp_dequeue_style( $style );
			} elseif( wp_style_is( $style, 'registered' ) ){
				wp_deregister_style( $style );
			}
		}

		// Remove Bootstrap 3 Shortcodes media button
		remove_action( 'media_buttons', 'add_bootstrap_button', 11 );
		// Bootstrap 3 Shortcodes plugin enqueues on media_buttons action (which is just sloppy ... mehhh)
		remove_action( 'media_buttons', 'bootstrap_shortcodes_help_styles' );

	}

	function enqueue(){

		if( $this->integration->is_plugin_page() ) {
			wp_enqueue_style( 'jme-semantic' );
			wp_enqueue_script( 'jme-semantic' );
			wp_enqueue_script( 'jme-admin' );
			wp_enqueue_style( 'jme-admin' );
		}

	}

	/**
	 * Localize JS Variables
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	function localize(){

		$support_ticket_url = 'https://plugins.smyl.es/support/new/';

		$translations = array(
			'error_submit_ticket'  => sprintf( __( 'If you continue receive this error, please submit a <a target="_blank" href="%s">support ticket</a>.', 'wp-job-manager-emails' ), esc_url( $support_ticket_url ) ),
			'field_required'       => __( 'This field is required!', 'wp-job-manager-emails' ),
			'yes'                  => __( 'Yes', 'wp-job-manager-emails' ),
			'loading'              => __( 'Loading', 'wp-job-manager-emails' ),
			'no'                   => __( 'No', 'wp-job-manager-emails' ),
			'cancel'               => __( 'Cancel', 'wp-job-manager-emails' ),
			'close'                => __( 'Close', 'wp-job-manager-emails' ),
			'enable'               => __( 'Enable', 'wp-job-manager-emails' ),
			'disable'              => __( 'Disable', 'wp-job-manager-emails' ),
			'error'                => __( 'Error', 'wp-job-manager-emails' ),
			'unknown_error'        => __( 'Uknown Error! Refresh the page and try again.', 'wp-job-manager-emails' ),
			'success'              => __( 'Success', 'wp-job-manager-emails' ),
			'ays_remove'           => __( 'Are you sure you want to remove this configuration?', 'wp-job-manager-emails' ),
			'attachments_loader'   => __( 'Updating supported attachment fields...', 'wp-job-manager-emails' ),
			'hook_loader'          => __( 'Loading hook configuration...', 'wp-job-manager-emails' ),
			'shortcodes'           => __( 'Shortcodes', 'wp-job-manager-emails' )
		);

		wp_localize_script( 'jme-admin', 'jmelocale', $translations );
	}

	/**
	 * Register Admin CSS & JS
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	function register(){

		$script_version = defined( 'WPJME_DEBUG' ) && WPJME_DEBUG !== FALSE ? filemtime( __FILE__ ) : NULL;
		$debug_min = defined( 'WPJME_DEBUG' ) && WPJME_DEBUG !== FALSE ? '.' : '.min.';

		wp_register_style( 'jme-semantic', JOB_MANAGER_EMAILS_PLUGIN_URL . "/assets/vendor/semantic/dist/semantic{$debug_min}css", array(), $script_version );
		wp_register_script( 'jme-semantic', JOB_MANAGER_EMAILS_PLUGIN_URL . "/assets/vendor/semantic/dist/semantic{$debug_min}js", array('jquery'), $script_version, TRUE );
		//wp_register_script( 'jme-semantic', 'http://semantic-ui.com/dist/semantic.js', array('jquery'), $script_version, TRUE );

		wp_register_style( 'jme-admin', $this->path( 'admin.css' ), array(), $script_version );
		wp_register_script( 'jme-admin', $this->path( 'admin.js' ), array('jquery'), $script_version, TRUE );
		wp_register_script( 'jme-resume', $this->path( 'resume.js' ), array('jquery'), $script_version, TRUE );
		wp_register_script( 'jme-edit', $this->path( 'edit.js' ), array('jquery'), $script_version, TRUE );

		wp_localize_script( 'jme-edit', 'jme_translations', array(
			'add' => __( 'Add', 'wp-job-manager-emails' ),
			'click_to_add' => __( 'Click to add', 'wp-job-manager-emails' ),
		    'invalid' => __( 'Invalid', 'wp-job-manager-emails' ),
		    'valid' => __( 'Valid', 'wp-job-manager-emails' ),
		    'enter_valid_email' => __( 'Please enter a valid email to add a custom email address.', 'wp-job-manager-emails' ),
		    'no_users_found' => __( 'No users found for', 'wp-job-manager-emails' ),
		    'search_results' => __( 'Search results for', 'wp-job-manager-emails' ),
		));
		//wp_register_style( 'jme-admin-css', $this->css_path('admin'), array( 'chosen' ), $script_version );
		//wp_register_style( 'jme-vendor-css', $this->css_path('vendor'), array(), $script_version );
		//wp_register_script( 'jme-vendor-js', $this->js_path('vendor'), array( 'jquery' ), $script_version, true );
		//wp_register_script( 'jme-admin-js', $this->js_path('admin'), array( 'jquery', 'chosen', 'jquery-ui-spinner', 'jme-vendor-js' ), $script_version, true );
		//wp_register_script( 'jme-default-js', JOB_MANAGER_EMAILS_PLUGIN_URL . "/assets/js/single/default.js", array('jquery', 'chosen' ), $admin_js_time, TRUE );
		//wp_register_script( 'jme-groups-js', JOB_MANAGER_EMAILS_PLUGIN_URL . "/assets/js/single/groups.js", array( 'jquery', 'chosen', 'jquery-ui-spinner' ), $admin_js_time, true );

	}

	/**
	 * Return path to Asset
	 *
	 * @since 1.0.0
	 *
	 * @param $file
	 *
	 * @return string
	 */
	function path( $file ){

		$debug_path = defined( 'WPJME_DEBUG' ) && WPJME_DEBUG !== FALSE ? 'src/' : '';
		$debug_min  = defined( 'WPJME_DEBUG' ) && WPJME_DEBUG !== FALSE ? '.' : '.min.';
		$file = str_replace( '.', $debug_min, $file );

		return JOB_MANAGER_EMAILS_PLUGIN_URL . "/assets/core/{$debug_path}{$file}";
	}

	/**
	 * Return path to CSS
	 *
	 * Returns path to CSS files based on WPJME_DEBUG value.  Will return
	 * path to .min.css if FALSE or not set, or path to /build/*.css if set TRUE
	 *
	 * @since 1.0.0
	 *
	 * @param $file
	 *
	 * @return string
	 */
	function css_path( $file ) {

		$min       = defined( 'WPJME_DEBUG' ) && WPJME_DEBUG !== FALSE ? '' : '.min';
		$build_dir = defined( 'WPJME_DEBUG' ) && WPJME_DEBUG !== FALSE ? '/build' : '';

		return JOB_MANAGER_EMAILS_PLUGIN_URL . "/assets/css{$build_dir}/{$file}{$min}.css";
	}

}