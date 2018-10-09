<?php
/*
 * Plugin Name: Private Messages
 * Plugin URI: https://astoundify.com
 * Description: Easily allow users to send and receive direct private messages.
 * Version: 1.9.1
 * Author: Astoundify
 * Author URI: https://astoundify.com/
 * Requires at least: 4.7.0
 * Tested up to: 4.9.0
 *
 * Text Domain: private-messages
 * Domain Path: /languages
 *
 * @package Private Messages
 * @category Core
 * @author Astoundify
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin version.
define( 'PM_VERSION', '1.9.1' );
define( 'PM_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );

/**
 * Private Messages.
 *
 * @since 1.0.0
 */
class Private_Messages {

	/**
	 * Class Instance.
	 *
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Make sure only one instance is only running.
	 *
	 * @since 1.0.0
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Constructor: Start things up.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->version      = defined( 'PM_VERSION' );
		$this->file         = __FILE__;
		$this->basename     = plugin_basename( $this->file );
		$this->plugin_dir   = plugin_dir_path( $this->file );
		$this->plugin_url   = plugin_dir_url( $this->file );
		$this->lang_dir     = trailingslashit( $this->plugin_dir . 'languages' );

		// Load all files.
		$this->includes();

		// Load classes.
		$this->settings = new Private_Messages_Settings();
		$this->post_types = new Private_Messages_Post_Types();

		// Load actions.
		$this->setup_actions();
	}

	/**
	 * Load all files.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function includes() {
		$files = array(
			'includes/private-messages-functions.php',              // Template and utility functions.

			'includes/class-private-messages-post-types.php',       // Register post type, clean up cpt data, etc.
			'includes/class-private-messages-comments.php',         // Exclude PM comments everywhere.

			'includes/class-private-messages-message-thread.php',   // PM Thread Object.
			'includes/class-private-messages-message.php',          // Single Message/Comment Object.

			'includes/class-private-messages-dashboard.php',        // Dashboard Shortcodes.
			'includes/class-private-messages-templates.php',        // Template functions.
			'includes/class-private-messages-shortcodes.php',       // Other shortcodes.
			'includes/class-private-messages-notifications.php',    // Email notification to user.
			'includes/class-private-messages-menu.php',             // Display unread count in menu item.
			'includes/class-private-messages-files.php',            // Message Attachment.

			'includes/class-private-messages-integration.php',      // Integration base class.
			'includes/class-private-messages-integration-fes.php',  // EDD Front end Submission.
			'includes/class-private-messages-integration-wpjm.php', // WP Job Manager.

			'includes/admin/class-private-messages-setup.php',      // Admin: Initial setup.
			'includes/admin/class-private-messages-settings.php',   // Admin: Settings.
			'includes/admin/class-private-messages-admin-edit.php', // Admin: PM Edit Screen, Meta Box, etc.
		);

		foreach ( $files as $file ) {
			include_once( $this->plugin_dir . '/' . $file );
		}
	}

	/**
	 * Hooks and filters
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function setup_actions() {
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_register_scripts' ), 5 );
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'admin_init', array( $this, 'updater' ), 9 );
	}

	/**
	 * Load translations.
	 *
	 * @since 1.4.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'private-messages', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * frontend_scripts function.
	 *
	 * @access public
	 * @return void
	 */
	public function frontend_register_scripts() {
		$ajaxurl = admin_url( 'admin-ajax.php', 'relative' );

		// WPML workaround until this is standardized.
		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$ajaxurl = add_query_arg( 'lang', ICL_LANGUAGE_CODE, $ajaxurl );
		}

		if ( ! wp_script_is( 'select2', 'enqueued' ) ) {
			wp_register_script( 'select2', $this->plugin_url . '/assets/js/select2.min.js', array( 'jquery' ), '4.0.3', true );
		}

		wp_register_script( 'private-messages-frontend', $this->plugin_url . '/assets/js/frontend.js', array( 'jquery', 'select2' ), $this->version, true );

		wp_localize_script( 'private-messages-frontend', 'PrivateMessages', array(
			'ajaxurl' => $ajaxurl,
			'show_avatars' => get_option( 'show_avatars', true ),
			'confirm_delete' => __( 'Are you sure you want to delete this message?', 'private-messages' ),
			'empty_message' => __( 'You can\'t send and empty message.', 'private-messages' ),
			'select2_i18n'  => array(
				'errorLoading' => __( 'The results could not be loaded.', 'private-messages' ),
				'inputTooLong' => __( 'Please delete %s character', 'private-messages' ),
				'inputTooLong_plural' => __( 'Please delete %s characters', 'private-messages' ),
				'inputTooShort' => __( 'Please enter %s or more characters', 'private-messages' ),
				'loadingMore' => __( 'Loading more results&hellip;', 'private-messages' ),
				'maximumSelected' => __( 'You can only select %s item', 'private-messages' ),
				'maximumSelected_plural' => __( 'You can only select %s items', 'private-messages' ),
				'noResults' => __( 'No results found', 'private-messages' ),
				'searching' => __( 'Searching&hellip;', 'private-messages' ),
			),
		) );

		wp_register_style( 'select2', $this->plugin_url . '/assets/css/select2.min.css', null, $this->version );
		wp_register_style( 'private-messages-frontend', $this->plugin_url . '/assets/css/frontend.css', array( 'dashicons' ), $this->version );
	}

	/**
	 * Updater
	 *
	 * @since 1.6.0
	 */
	public function updater() {
		require_once( dirname( __FILE__ ) . '/vendor/astoundify/plugin-updater/astoundify-pluginupdater.php' );

		new Astoundify_PluginUpdater( __FILE__ );
	}

}


/**
 * Return the singleton instance.
 *
 * @since 1.0.0
 * @return object Private_Messages
 */
function private_messages() {
	return Private_Messages::instance();
}

// Load plugin instances on plugins loaded hook.
add_action( 'plugins_loaded', 'private_messages' );


/**
 * Activation Hook.
 * Setup is set in Admin/Private_Messages_Setup Class.
 *
 * @since 1.6.0
 * @link https://github.com/Astoundify/private-messages/issues/70
 */
function private_messages_install() {
	$old_version = get_option( 'pm_version' );

	// Redirect to setup screen for new insalls.
	if ( ! $old_version ) {

		set_transient( '_pm_activation_redirect', 1, HOUR_IN_SECONDS );

	} else {

		// Fix PM held for moderation.
		if ( version_compare( $old_version, '1.2.0', '<=' ) ) {
			global $wpdb;
			// Get all "on holds" PM replies and set to approved.
			$on_holds = $wpdb->get_results( "SELECT comment_ID, comment_post_ID FROM $wpdb->comments WHERE comment_type = 'private-messages' AND  comment_approved = '0'" );
			if ( $on_holds ) {
				foreach ( $on_holds as $comment ) {
					if ( isset( $comment->comment_ID ) && $comment->comment_ID ) {
						$wpdb->update( $wpdb->comments, array(
							'comment_approved' => '1',
							), array(
							'comment_ID' => $comment->comment_ID,
						) );
						do_action( 'wp_set_comment_status', $comment->comment_ID, 'approve' );
						clean_comment_cache( $comment->comment_ID );
						wp_update_comment_count( $comment->comment_post_ID );
					}
				}
			}
		}
	}

	// Update with current version.
	update_option( 'pm_version', PM_VERSION );
}

// Register activation hook.
register_activation_hook( __FILE__, 'private_messages_install', 10 );
