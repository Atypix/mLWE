<?php
/**
 * Plugin Name: Extended Location for WP Job Manager
 * Plugin URI: https://astoundify.com/downloads/wp-job-manager-extended-location/
 * Description: Use Google Places to auto suggest locations when submitting a listing or searching.
 * Version: 3.4.0
 * Author: Astoundify
 * Author URI: http://astoundify.com
 * Text Domain: wp-job-manager-extended-location
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 *  Class WP_Job_Manager_Extended_Location
 *  Main WPJMEL class initializes the plugin
 *
 *  @class      WP_Job_Manager_Extended_Location
 *  @version    1.0.0
 */
class WP_Job_Manager_Extended_Location{


	/**
	 * Plugin version.
	 *
	 * @since 1.0.0
	 * @var string $version Plugin version number.
	 */
	public $version = '3.3.0';


	/**
	 * Plugin file.
	 *
	 * @since 1.0.0
	 * @var string $file Plugin file path.
	 */
	public $file = __FILE__;


	/**
	 * Plugin URI.
	 *
	 * @since 3.0.0
	 * @var string $url Plugin URL path.
	 */
	public $url = '';


	/**
	 * Plugin PATH.
	 *
	 * @since 3.0.0
	 * @var string $url Plugin file path.
	 */
	public $path = '';


	/**
	 * Instace of WP_Job_Manager_Extended_Location.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var object $instance The instance of WPJMEL.
	 */
	private static $instance;


	/**
	 * Construct.
	 * Initialize the class and plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		/* Vars */
		$this->url  = trailingslashit( plugin_dir_url( $this->file ) );
		$this->path = trailingslashit( plugin_dir_path( $this->file ) );

		/* Load Text Domain */
		load_plugin_textdomain( 'wp-job-manager-extended-location', false, basename( dirname( $this->file ) ) . '/languages' );

		/* Only Load If WP Job Manager Active */
		if( class_exists( 'WP_Job_Manager' ) ){

			/* Register Scripts */
			add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), 1 );
			add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ), 1 );

			/* Load Settings Class */
			if ( is_admin() ) {
				require_once $this->path . 'includes/class-wpjmel-settings.php';
				$this->settings = new WPJMEL_Settings();
			}

			/* Map Setup */
			$map_option = get_option( 'wpjmel_enable_map', 1 );
			$map_key = get_option( 'job_manager_google_maps_api_key' );
			if( 1 == $map_option && $map_key ){
				require_once $this->path . 'includes/class-wpjmel-map-setup.php';
				$this->map_setup = new WPJMEL_Map_Setup();
			}

			/* Location Suggestion Setup */
			$loc_suggest_option = get_option( 'wpjmel_enable_city_suggest', 1 );
			if( 1 == $loc_suggest_option ){
				require_once $this->path . 'includes/class-wpjmel-location-suggestion-setup.php';
				$this->location_suggestion_setup = new WPJMEL_Location_Suggestion_Setup();
			}
		}

	}


	/**
	 * Instance.
	 * An global instance of the class. Used to retrieve the instance
	 * to use on other files/plugins/themes.
	 *
	 * @since 1.0.0
	 * @return object Instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Register scripts.
	 * @since 2.7.0
	 */
	public function register_scripts() {

		/* Google Maps Script
		------------------------------------------ */

		/* Google Maps JS URL */
		$url = add_query_arg( array(
			'v'         => '3.exp',
			'libraries' => 'places',
			'language'  => get_locale() ? substr( get_locale(), 0, 2 ) : '',
			'region'    => get_locale() ? substr( get_locale(), 3, 2 ) : ''
		), '//maps.googleapis.com/maps/api/js' );

		/* Add Google Map Key */
		$key = strip_tags( get_option( 'job_manager_google_maps_api_key' ) );
		$url = $key ? add_query_arg( 'key', urlencode( $key ), $url ) : $url;

		/* Register Google Maps Scripts */
		wp_register_script( 'google-maps', apply_filters( 'wpjmel_google_maps_url', $url ), array(), '3.exp', false );


		/* Mapify: Google Maps JS Handler
		------------------------------------------ */

		/* Mapify CSS */
		wp_register_style( 'mapify', $this->url . 'assets/mapify/mapify.css', array(), $this->version );

		/* Mapify JS */
		wp_register_script( 'mapify', $this->url . 'assets/mapify/jquery.mapify.js', array( 'jquery', 'google-maps' ), $this->version, false );

		/* Localize Data */
		$args = array(
			'locked'    => __( 'Lock Pin Location', 'wp-job-manager-extended-location' ),
			'unlocked'  => __( 'Unlock Pin Location', 'wp-job-manager-extended-location' ),
		);
		wp_localize_script( 'mapify', 'mapifyl10n', $args );
	}


	/**
	 * Get the users location information by IP.
	 * @since 1.0.1
	 */
	public function get_user_ip_location( $ip = false ) {

		/* Get client IP Address */
		if( false === $ip ){
			if( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			}
			elseif( ! empty($_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			else{
				$ip = $_SERVER['REMOTE_ADDR'];
			}
		}

		// IP-API.com Localization.
		$lang = get_option( 'wpjmel_ip_api_localization', self::ip_api_lang_default() );

		/* Create unique string for each to store in transient */
		$hash = 'wpjmel_location_' . md5( $ip . $lang . $this->version );

		/* Check Transient */
		if ( ! $location_data = get_transient( $hash ) ) {
			$url = add_query_arg( 'lang', $lang, 'http://ip-api.com/json/' . $ip );
			$response = wp_remote_get( esc_url_raw( $url ) );
			if ( is_wp_error( $response ) ) {
				return array();
			}
			$data = wp_remote_retrieve_body( $response );
			if ( is_wp_error( $data ) ) {
				return array();
			}
			$location_data = json_decode( $data, true );

			/* Add Formatted Address */
			$loc = array();

			if( isset( $location_data['city'] ) ){
				$loc[] = $location_data['city'];
			}

			if( isset( $location_data['country'] ) ){
				$loc[] = $location_data['country'];
			}

			$location_data['formatted_address'] = apply_filters( 'wpjmel_ip_location_string', implode( $loc, ', ' ), $location_data );

			/* Save in transient */
			set_transient( $hash, $location_data, DAY_IN_SECONDS );
		}

		return $location_data;
	}

	/* UTILITY FUNCTIONS
	------------------------------------------ */

	/**
	 * Get IP API Language.
	 *
	 * @since 3.4.0
	 *
	 * @return array
	 */
	public static function ip_api_valid_lang() {
		return array(
			'en'          => __( 'English (default)', 'wp-job-manager-extended-location' ),
			'de'          => __( 'Deutsch (German)', 'wp-job-manager-extended-location' ),
			'es'          => __( 'Español (Spanish)', 'wp-job-manager-extended-location' ),
			'pt-BR'       => __( 'Español - Argentina (Spanish)', 'wp-job-manager-extended-location' ),
			'fr'          => __( 'Français (French)', 'wp-job-manager-extended-location' ),
			'ja'          => __( '日本語 (Japanese)', 'wp-job-manager-extended-location' ),
			'zh-CN'       => __( '中国 (Chinese)', 'wp-job-manager-extended-location' ),
			'ru'          => __( 'Русский (Russian)', 'wp-job-manager-extended-location' ),
		);
	}

	/**
	 * Get IP API Language.
	 *
	 * @since 3.4.0
	 * @link http://ip-api.com/docs/api:returned_values
	 *
	 * @return string Default language code.
	 */
	public static function ip_api_lang_default() {
		$valid_lang     = self::ip_api_valid_lang();
		$default        = 'en';

		$default_lang_2 = get_locale() ? substr( get_locale(), 0, 2 ) : '';
		$default_lang_4 = get_locale() ? str_replace( '_', '-', get_locale() ) : '';

		// Set default.
		if ( array_key_exists( $default_lang_2, $valid_lang ) ) {
			$default = $default_lang_2;
		} elseif ( array_key_exists( $default_lang_4, $valid_lang ) ) {
			$default = $default_lang_4;
		}

		return $default;
	}

}

/**
 * Load the plugin updater.
 *
 * @since 3.1.0
 */
function wp_job_manager_extended_location_updater() {
	require_once( dirname( __FILE__ ) . '/vendor/astoundify/plugin-updater/astoundify-pluginupdater.php' );

	new Astoundify_PluginUpdater( __FILE__ );
	new Astoundify_PluginUpdater_Integration_WPJobManager( __FILE__ );
}
add_action( 'admin_init', 'wp_job_manager_extended_location_updater', 9 );

/**
 * The main function responsible for returning the WP_Job_Manager_Extended_Location object.
 * Use this function like you would a global variable, except without needing to declare the global.
 * Example: <?php WPJMEL()->method_name(); ?>
 *
 * @since 1.0.0
 * @return object WP_Job_Manager_Extended_Location class object.
 */
function WP_Job_Manager_Extended_Location() {
	return WP_Job_Manager_Extended_Location::instance();
}
add_action( 'plugins_loaded', 'WP_Job_Manager_Extended_Location' );
