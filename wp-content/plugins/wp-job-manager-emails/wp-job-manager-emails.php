<?php
/**
 * Plugin Name: WP Job Manager - Emails
 * Plugin URI:  https://plugins.smyl.es
 * Description: Complete email customization for WP Job Manager, and addon plugins.  Create emails for every aspect of WP Job Manager using built-in templates, or customize your own.
 * Version:     2.4.1
 * Author:      Myles McNamara
 * Author URI:  https://smyl.es
 * Donate link: https://plugins.smyl.es
 * Text Domain: wp-job-manager-emails
 * Domain Path: /languages
 * Requires at least: 4.4.0
 * Tested up to: 4.9.4
 * Last Updated: Mon Mar 05 2018 14:30:26
 */

/**
 * Minimum Required PHP Version Notice
 *
 * @since 2.4.0
 */
function job_manager_emails_deactivated_php_notice() {
	// translators: %1$s minimum PHP version, %2$s current PHP version.
	$notice = sprintf( __( 'WP Job Manager Emails requires at least PHP %1$s. You are running PHP %2$s. Please upgrade and try again, or see %3$s for more details.', 'wp-job-manager-emails' ), '<code>5.3.0</code>', '<code>' . PHP_VERSION . '</code>', '<a href="http://www.wpupdatephp.com/" target="_blank">wpupdatephp.com</a>' );
	?><div class="notice notice-error"><p><?php echo wp_kses_post( $notice, array( 'code' ) ); ?></p></div><?php
}

// Check for PHP version..
if ( version_compare( PHP_VERSION, '5.3', '<' ) ) {
	add_action( 'admin_notices', 'job_manager_emails_deactivated_php_notice' );
	return;
}

/**
 * Autoloads files with classes when needed
 *
 * @since  1.0.0
 * @param  string $class Name of the class being requested
 * @return void
 */
function wp_job_manager_emails_autoload_classes( $class ) {

	// Exit autoload if being called by a class other than ours
	if( FALSE === strpos( $class, 'WP_Job_Manager_Emails_' ) ) return;

	$class_file = str_replace( 'WP_Job_Manager_Emails_', '', $class );
	$file_array = array_map( 'strtolower', explode( '_', $class_file ) );

	$dirs = 0;
	$path = '';

	while( $dirs ++ < count( $file_array ) ) {
		if( $dirs >= count( $file_array ) ){
			$filename = $file_array[ $dirs - 1 ];
		} else {
			$path .= '/' . $file_array[ $dirs - 1 ];
		}
	}

	WP_Job_Manager_Emails::include_file( $filename, $path );
}

spl_autoload_register( 'wp_job_manager_emails_autoload_classes' );

// PHP 5.4 or older compatibility
require_once('includes/compatibility.php');
require_once('includes/functions.php');

if( ! class_exists( 'sMyles_Updater_v2' ) ){
	include_once( 'includes/updater/smyles-updater.php' );
}

/**
 * Main initiation class
 *
 * @since  1.0.0
 * @var  string $version  Plugin version
 * @var  string $basename Plugin basename
 * @var  string $url      Plugin URL
 * @var  string $path     Plugin Path
 */
class WP_Job_Manager_Emails {

	/**
	 * Current version
	 *
	 * @var  string
	 * @since  1.0.0
	 */
	const VERSION = '2.4.1';

	/**
	 * Plugin Slug
	 *
	 * @var  string
	 * @since 1.0.0
	 */
	const PLUGIN_SLUG = 'wp-job-manager-emails';

	/**
	 * Plugin Product ID
	 *
	 * @var  string
	 * @since 1.0.0
	 */
	const PROD_ID = 'WP Job Manager - Emails';

	/**
	 * URL of plugin directory
	 *
	 * @var string
	 * @since  1.0.0
	 */
	protected $url = '';
	/**
	 * Path of plugin directory
	 *
	 * @var string
	 * @since  1.0.0
	 */
	protected $path = '';
	/**
	 * Plugin basename
	 *
	 * @var string
	 * @since  1.0.0
	 */
	protected $basename = '';
	/**
	 * Singleton instance of plugin
	 *
	 * @var WP_Job_Manager_Emails
	 * @since  1.0.0
	 */
	protected static $single_instance = null;
	/**
	 * Instance of WP_Job_Manager_Emails_Integration
	 *
	 * @var WP_Job_Manager_Emails_Integration
	 */
	protected $integration;
	/**
	 * Instance of WP_Job_Manager_Emails_Admin
	 *
	 * @var WP_Job_Manager_Emails_Admin
	 */
	protected $admin;
	/**
	 * @var null
	 */
	public $tmp_job_id = NULL;
	/**
	 * @var null
	 */
	public $tmp_alert_id = NULL;
	/**
	 * @var null
	 */
	public $tmp_resume_id = NULL;
	/**
	 * @var null
	 */
	public $tmp_app_id = NULL;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since  1.0.0
	 * @return WP_Job_Manager_Emails A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin
	 *
	 * @since  1.0.0
	 */
	protected function __construct() {

		$this->basename          = plugin_basename( __FILE__ );
		$this->url               = plugin_dir_url( __FILE__ );
		$this->path              = plugin_dir_path( __FILE__ );
		$this->plugin_version    = self::VERSION;
		$this->plugin_product_id = self::PROD_ID;
		$this->constants();

	}

	/**
	 * Define Constants
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	function constants(){

		if( ! defined( 'JOB_MANAGER_EMAILS_VERSION' ) ) define( 'JOB_MANAGER_EMAILS_VERSION', WP_Job_Manager_Emails::VERSION );
		if( ! defined( 'JOB_MANAGER_EMAILS_PROD_ID' ) ) define( 'JOB_MANAGER_EMAILS_PROD_ID', WP_Job_Manager_Emails::PROD_ID );
		if( ! defined( 'JOB_MANAGER_EMAILS_PLUGIN_DIR' ) ) define( 'JOB_MANAGER_EMAILS_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		if( ! defined( 'JOB_MANAGER_EMAILS_PLUGIN_URL' ) ) define( 'JOB_MANAGER_EMAILS_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function plugin_classes() {

		new sMyles_Updater_v2( __FILE__, self::PROD_ID, self::VERSION );

		$this->integration = new WP_Job_Manager_Emails_Integration( $this );
		if( is_admin() ) $this->admin = new WP_Job_Manager_Emails_Admin( $this );
	}

	/**
	 * Add hooks and filters
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function hooks() {

		$this->plugin_classes();

		register_activation_hook( __FILE__, array( $this, '_activate' ) );
		register_deactivation_hook( __FILE__, array( $this, '_deactivate' ) );

		add_action( 'init', array( $this, 'init' ) );
		add_filter( 'plugin_row_meta', array( $this, 'add_plugin_row_meta' ), 10, 4 );
		add_filter( 'cron_schedules', array($this, 'add_weekly') );

	}

	/**
	 * Activate the plugin
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function _activate() {
		// Make sure any rewrite functionality has been loaded
		flush_rewrite_rules();
		wp_schedule_event( time() + 60, 'weekly', 'job_manager_email_check_send_email' );
	}

	/**
	 * Deactivate the plugin
	 * Uninstall routines should be in uninstall.php
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function _deactivate() {
		wp_clear_scheduled_hook( 'job_manager_email_check_send_email' );
	}

	/**
	 * Init hooks
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function init() {
		if ( $this->check_requirements() ) {
			load_plugin_textdomain( 'wp-job-manager-emails', false, dirname( $this->basename ) . '/languages/' );
		}
	}

	/**
	 * Check if the plugin meets requirements and
	 * disable it if they are not present.
	 *
	 * @since  1.0.0
	 * @return boolean result of meets_requirements
	 */
	public function check_requirements() {
		if ( ! $this->meets_requirements() ) {

			// Add a dashboard notice
			add_action( 'all_admin_notices', array( $this, 'requirements_not_met_notice' ) );

			// Deactivate our plugin
			deactivate_plugins( $this->basename );

			return false;
		}

		return true;
	}

	/**
	 * Check that all plugin requirements are met
	 *
	 * @since  1.0.0
	 * @return boolean
	 */
	public static function meets_requirements() {
		// Do checks for required classes / functions
		// function_exists('') & class_exists('')

		// We have met all requirements
		return true;
	}

	/**
	 * Adds a notice to the dashboard if the plugin requirements are not met
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function requirements_not_met_notice() {
		// Output our error
		echo '<div id="message" class="error">';
		echo '<p>' . sprintf( __( 'WP Job Manager - Emails is missing requirements and has been <a href="%s">deactivated</a>. Please make sure all requirements are available.', 'wp-job-manager-emails' ), admin_url( 'plugins.php' ) ) . '</p>';
		echo '</div>';
	}

	/**
	 * Add Plugin Row Links on WordPress Plugin Page
	 *
	 * @since 1.0.0
	 *
	 * @param $plugin_meta
	 * @param $plugin_file
	 * @param $plugin_data
	 * @param $status
	 *
	 * @return array
	 */
	public function add_plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {

		if( self::PLUGIN_SLUG . '/' . self::PLUGIN_SLUG . '.php' == $plugin_file ) {
			$plugin_meta[] = sprintf( '<a href="%s" target="_blank">%s</a>', "https://plugins.smyl.es/my-account/", __( 'My Account', 'wp-job-manager-emails' ) );
			$plugin_meta[] = sprintf( '<a href="%s" target="_blank">%s</a>', "https://plugins.smyl.es/support/", __( 'Support', 'wp-job-manager-emails' ) );
			$plugin_meta[] = sprintf( '<a href="%s" target="_blank">%s</a>', "https://www.transifex.com/projects/p/" . self::PLUGIN_SLUG, __( 'Translations', 'wp-job-manager-emails' ) );
		}

		return $plugin_meta;
	}

	/**
	 * Magic getter for our object.
	 *
	 * @since  1.0.0
	 * @param string $field
	 * @throws Exception Throws an exception if the field is invalid.
	 * @return mixed
	 */
	public function __get( $field ) {
		switch ( $field ) {
			case 'version':
				return self::VERSION;
			case 'basename':
			case 'url':
			case 'path':
			case 'integration':
			case 'job':
			case 'resume':
			case 'applications':
				return $this->$field;
			default:
				throw new Exception( 'Invalid '. __CLASS__ .' property: ' . $field );
		}
	}

	/**
	 * Add a weekly option to cron jobs
	 *
	 *
	 * @since 1.3.5
	 *
	 * @param $schedules
	 *
	 * @return mixed
	 */
	function add_weekly( $schedules ) {

		// add a 'weekly' schedule to the existing set
		$schedules[ 'weekly' ] = array(
			'interval' => 604800,
			'display'  => __( 'Once Weekly', 'wp-job-manager-field-editor', 'wp-job-manager-emails' )
		);

		return $schedules;
	}

	/**
	 * Include a file from the includes directory
	 *
	 * @since  1.0.0
	 * @param  string  $filename   Name of the file to be included
	 * @param  string  $directory  Additional sub directories
	 * @return bool    Result of include call.
	 */
	public static function include_file( $filename, $directory = '' ) {

		if( ! empty( $directory ) ) {
			// Make sure directory starts with slash (/) but does not end with one
			$directory = untrailingslashit( '/' . ltrim( $directory, '/\\' ) );
		}

		$file = self::dir( "includes{$directory}/class-". $filename .'.php' );

		if ( file_exists( $file ) ) {
			return include_once( $file );
		}

		return false;
	}

	/**
	 * This plugin's directory
	 *
	 * @since  1.0.0
	 * @param  string $path (optional) appended path
	 * @return string       Directory and path
	 */
	public static function dir( $path = '' ) {
		static $dir;
		$dir = $dir ? $dir : trailingslashit( dirname( __FILE__ ) );
		return $dir . $path;
	}

	/**
	 * This plugin's url
	 *
	 * @since  1.0.0
	 * @param  string $path (optional) appended path
	 * @return string       URL and path
	 */
	public static function url( $path = '' ) {
		static $url;
		$url = $url ? $url : trailingslashit( plugin_dir_url( __FILE__ ) );
		return $url . $path;
	}
}

/**
 * Grab the WP_Job_Manager_Emails object and return it.
 * Wrapper for WP_Job_Manager_Emails::get_instance()
 *
 * @since  1.0.0
 * @return WP_Job_Manager_Emails  Singleton instance of plugin class.
 */
function wp_job_manager_emails() {
	return WP_Job_Manager_Emails::get_instance();
}

add_action( 'plugins_loaded', array( wp_job_manager_emails(), 'hooks' ) );
