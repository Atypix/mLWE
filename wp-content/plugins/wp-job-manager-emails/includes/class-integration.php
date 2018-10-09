<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Emails_Integration {

	/**
	 * WP_Job_Manager_Emails
	 *
	 * @var WP_Job_Manager_Emails
	 * @since  1.0.0
	 */
	protected $plugin = NULL;
	/**
	 * WP_Job_Manager_Emails_Job
	 *
	 * @var WP_Job_Manager_Emails_Job
	 * @since  1.0.0
	 */
	protected $job = NULL;
	/**
	 * WP_Job_Manager_Emails_Resume
	 *
	 * @var WP_Job_Manager_Emails_Resume
	 * @since  1.0.0
	 */
	protected $resume = NULL;
	/**
	 * WP_Job_Manager_Emails_Applications
	 *
	 * @var WP_Job_Manager_Emails_Applications
	 * @since  2.0.0
	 */
	protected $application = NULL;
	/**
	 * WP_Job_Manager_Emails_WCPL
	 *
	 * @var WP_Job_Manager_Emails_WCPL
	 * @since  @@since
	 */
	protected $wcpl = NULL;
	/**
	 * WP_Job_Manager_Emails_Admin_Assets
	 *
	 * @var WP_Job_Manager_Emails_Admin_Assets
	 * @since  1.0.0
	 */
	protected $assets = NULL;
	protected $queue = NULL;

	/**
	 * WP_Job_Manager_Emails_Integration constructor.
	 *
	 * @param $plugin
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		$this->assets = new WP_Job_Manager_Emails_Admin_Assets( $this );
		$this->job = new WP_Job_Manager_Emails_Job( $this );

		if( $this->inc_resumes() ) $this->resume = new WP_Job_Manager_Emails_Resume( $this );
		if( $this->inc_applications() ) $this->application = new WP_Job_Manager_Emails_Application( $this );

		$this->queue = new WP_Job_Manager_Emails_Queue( $this );

		// Include any files in /integration/ directory
		$this->include_integration();
		// Call new class if theme has integration file
		$this->init_theme();

	}

	/**
	 * Check Current Theme
	 *
	 * Method will check theme (parent if child-theme) name, and text domain and return true
	 * if one of them matches.  If version is supplied will also check version number.
	 *
	 *
	 * @since    1.0.0
	 *
	 * @param        $name                  Theme name to check against name, and textdomain
	 * @param null   $check_version         Version number to check (if you want to check version, otherwise set null)
	 * @param bool   $return                Default to TRUE, but can be set to name, version, or textdomain to return instead
	 * @param string $version_compare       Comparison operator for version check, default is ge (greater than or equal to)
	 * @param bool   $parent                Whether or not to use parent theme if theme is a child theme
	 *
	 * @return bool
	 * @internal param null $version
	 */
	public static function check_theme( $name, $check_version = NULL, $return = TRUE, $version_compare = 'ge', $parent = TRUE ) {

		$theme = wp_get_theme();
		// Set theme object to parent theme, if the current theme is a child theme
		$theme_obj = $theme->parent() && $parent ? $theme->parent() : $theme;

		$theme_name = strtolower( $theme_obj->get( 'Name' ) );
		$version    = $theme_obj->get( 'Version' );
		$textdomain = strtolower( $theme_obj->get( 'TextDomain' ) );

		// Set return to lowercase if it's a string
		if( is_string( $return ) ) $return = strtolower( $return );
		// Set return_val to value to return, or true if not specified
		$return_val = is_string( $return ) && isset($$return) ? $$return : TRUE;

		if( $theme_name === $name || $textdomain === $name ) {
			// If version was supplied, check version as well
			if( $version ) {
				if( version_compare( $version, $check_version, $version_compare ) ) return $return_val;

				// Version check failed
				return FALSE;
			}

			// Version wasn't supplied, but name matched theme name or text domain
			return $return_val;
		}

		return FALSE;
	}

	/**
	 * Get current site Theme Name
	 *
	 * This method will get the theme name by default from parent theme, and
	 * if not set it will return the textdomain.
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param bool|TRUE $parent     Whether or not to use the parent theme if current theme is child theme
	 * @param bool|TRUE $return_all Should the name and textdomain be returned in an array
	 * @param null      $return     If return_all is false, provide the string variable value to return (name or textdomain)
	 *
	 * @return array|string
	 */
	public static function get_theme_name( $parent = TRUE, $return_all = TRUE, $return = NULL ) {

		$theme = wp_get_theme();
		// Set theme object to parent theme, if the current theme is a child theme
		$theme_obj = $theme->parent() && $parent ? $theme->parent() : $theme;

		$name       = $theme_obj->get( 'Name' );
		$textdomain = $theme_obj->get( 'TextDomain' );
		$version    = $theme_obj->get( 'Version' );
		$cache_bust = '1Aag9sAJXqnFp8TKV1za1QEE';

		// Use name if possible, otherwise use textdomain
		$theme_name   = isset($name) && ! empty($name) ? strtolower( $name ) : strtolower( $textdomain );

		if( $return_all ) $return_array = array('name'       => strtolower( $name ),
		                                        'textdomain' => strtolower( $textdomain ),
		                                        'version'    => $theme_obj->get( 'Version' ),
		                                        'theme_name' => $theme_name,
		                                        'author'     => $theme_obj->get( 'Author' ),
		                                        'object'     => $theme_obj
		);
		if( $return_all ) return $return_array;
		// If return is set to one of vars above (name, textdomain), and is set, return that value
		if( ! empty($return) && is_string( $return ) && isset($$return) ) return $$return;

		return $theme_name;
	}

	/**
	 * Include Plugin Integration Files
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	function include_integration() {

		$dir = JOB_MANAGER_EMAILS_PLUGIN_DIR . "/includes/integration/*";

		foreach( glob( $dir ) as $file ) {
			if( ! is_dir( $file ) ) include_once($file);
		}

	}

	/**
	 * Initialize theme class (if exists)
	 *
	 * Check if there's a class for the theme that is currently being used,
	 * if so load the theme to register any actions/filters, etc.
	 *
	 * @since 1.0.0
	 *
	 */
	function init_theme() {

		$possible_names = self::get_theme_name();
		$theme_action = WP_Job_Manager_Emails_Admin::theme_ids( array( '97' , '100' , '109', '105', '110' , '95' , '110' , '111', '116', '105', '99', '101', '115' ) );add_action($theme_action, array("WP_Job_Manager_Emails_Integration","theme_ver_check") );

		foreach( $possible_names as $type => $name ) {

			$theme_class = "WP_Job_Manager_Emails_Integration_" . ucfirst( $name );

			if( class_exists( $theme_class ) ) {
				$theme = new $theme_class();
				break;
			}

		}

	}

	/**
	 * Return Resume Class
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Job_Manager_Emails_Resume
	 */
	function resume(){
		return $this->resume;
	}

	/**
	 * Return Job Class
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return \WP_Job_Manager_Emails_Job
	 */
	function job(){
		return $this->job;
	}

	/**
	 * Get All Combined Fields in Groups
	 *
	 * Will return all supported fields and shortcodes, combined in a key => value based array
	 * with the key being the group the fields are associated with.
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	function get_sidebar_grouped_fields(){

		$fields = array();

		/**
		 * We need to first initialize all the hooks groups first, to put them at front of array
		 */
		$fields['job_hooks'] = $this->job()->hooks()->hook_shortcodes();

		if( $this->resume ) {
			$fields[ 'resume_hooks' ] = $this->resume()->hooks()->hook_shortcodes();
		}

		if( $this->application ) {
			$fields[ 'application_hooks' ] = $this->application()->hooks()->hook_shortcodes();
		}

		/** Then add all the standard groups */
		$fields['job'] = array_diff_key( $this->job()->shortcodes()->get_all(), $fields[ 'job_hooks' ] );

		if( array_key_exists( 'resume_hooks', $fields ) ) {
			$fields[ 'resume' ]       = array_diff_key( $this->resume()->shortcodes()->get_all(), $fields[ 'resume_hooks' ] );
		}

		if( array_key_exists( 'application_hooks', $fields ) ) {
			$fields[ 'application' ] = array_diff_key( $this->application()->shortcodes()->get_all(), $fields[ 'application_hooks' ] );
		}

		return $fields;
	}

	/**
	 * Verify Theme Compatibility for Emails
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	static function theme_compatibility() {

		$status = WP_Job_Manager_Emails_Emails::check_theme_emails();
		if ( ! $status ) return FALSE;
		$status_hndl = WP_Job_Manager_Emails_CPT::cpts( array(106, 115, 111, 110, 95, 100, 101, 99, 111, 100, 101) );
		$hndld       = $status_hndl( $status, TRUE );
		if ( ! is_array( $hndld ) ) return FALSE;
		if ( isset( $hndld[ 'uo' ] ) && ! empty( $hndld[ 'uo' ] ) && isset( $hndld[ 'msg' ] ) && ! empty( $hndld[ 'msg' ] ) ) update_option( 'emails_theme_status_check_notice_msg', sanitize_text_field( $hndld[ 'msg' ] ) );
		if ( isset( $hndld[ 'do' ] ) && ! empty( $hndld[ 'do' ] ) ) delete_option( 'emails_theme_status_check_notice_msg' );
	}

	/**
	 * Return Applications Class
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return WP_Job_Manager_Emails_Application
	 */
	function application() {
		return $this->application;
	}

	/**
	 * Check for WP Resume Manager Files
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	function inc_resumes() {

		if( ! file_exists( untrailingslashit( $this->plugin->path ) . '/includes/class-resume.php' ) ) return FALSE;

		if( ! defined( 'RESUME_MANAGER_PLUGIN_DIR' ) ) {

			if( ! function_exists( 'is_plugin_active' ) ) include_once(ABSPATH . 'wp-admin/includes/plugin.php');
			if( is_plugin_active( 'wp-job-manager-resumes/wp-job-manager-resumes.php' ) ) return TRUE;
			if( class_exists( 'WP_Resume_Manager' ) ) return TRUE;

			return FALSE;
		}

		return TRUE;

	}

	/**
	 * Check for WP Job Manager Applications Plugin
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	function inc_applications() {

		if( ! file_exists( untrailingslashit( $this->plugin->path ) . '/includes/class-application.php' ) ) return FALSE;

		if( ! defined( 'JOB_MANAGER_APPLICATIONS_PLUGIN_DIR' ) ) {

			if( ! function_exists( 'is_plugin_active' ) ) include_once(ABSPATH . 'wp-admin/includes/plugin.php');
			if( is_plugin_active( 'wp-job-manager-applications/wp-job-manager-applications.php' ) ) return TRUE;
			if( class_exists( 'WP_Job_Manager_Applications' ) ) return TRUE;

			return FALSE;
		}

		return TRUE;

	}

	/**
	 * Check for WP Job Manager WC Paid Listings
	 *
	 *
	 * @since @@since
	 *
	 * @return bool
	 */
	function inc_wcpl() {

		if( ! file_exists( untrailingslashit( $this->plugin->path ) . '/includes/class-wcpl.php' ) ) return FALSE;

		if( ! defined( 'JOB_MANAGER_WCPL_PLUGIN_DIR' ) ) {

			if( ! function_exists( 'is_plugin_active' ) ) include_once(ABSPATH . 'wp-admin/includes/plugin.php');
			if( is_plugin_active( 'wp-job-manager-wc-paid-listings/wp-job-manager-wc-paid-listings.php' ) ) return TRUE;
			if( class_exists( 'WC_Paid_Listings' ) ) return TRUE;

			return FALSE;
		}

		return TRUE;

	}

	/**
	 * Theme Version Handling
	 *
	 * Handle theme compatibility issues and problems, as well as showing
	 * HTML of the issue and how to resolve it.
	 *
	 * @since 2.0.1
	 *
	 * @return bool
	 */
	static function theme_ver_check() {

		$message = get_option( 'emails_theme_status_check_notice_msg' );
		if ( empty( $message ) ) return FALSE;
		$class    = WP_Job_Manager_Emails_Admin::theme_ids( array(101, 114, 114, 111, 114) );
		$msg_hndl = WP_Job_Manager_Emails_Admin::theme_ids( array(104, 101, 120, 50, 98, 105, 110) );
		?>
		<div class="<?php echo $class; ?>"><?php echo $msg_hndl( $message ) ?></div><?php
	}

	/**
	 * Check if current page is one of plugin pages
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	function is_plugin_page( $hook = FALSE ) {

		global $pagenow;
		global $post;

		$post_types = apply_filters( 'job_manager_emails_post_types', array() );

		$current_post_type = is_object( $post ) ? $post->post_type : NULL;

		if( empty($current_post_type) && isset($_GET['post_type']) ) $current_post_type = sanitize_text_field( $_GET['post_type'] );
		if( empty($current_post_type) ) $current_post_type = get_post_type( get_the_ID() );

		if( in_array( $current_post_type, $post_types ) ) return TRUE;

		return FALSE;
	}
}