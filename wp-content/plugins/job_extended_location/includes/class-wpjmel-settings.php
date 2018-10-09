<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class WPJMEL_Settings.
 * @version    1.0.0
 * @author     Astoundify
 */
class WPJMEL_Settings {


	/**
	 * Constructor.
	 * @since 1.0.0
	 */
	public function __construct() {

		/* Add Settings Tab */
		add_action( 'job_manager_settings', array( $this, 'settings' ) );

		/* Register Settings */
		add_action( 'admin_init', array( $this, 'register_geo_settings' ) );

		/* Sanitize Options */
		add_filter( 'sanitize_option_wpjmel_enable_city_suggest', array( $this, 'sanitize_checkbox' ) );
		add_filter( 'sanitize_option_wpjmel_enable_map', array( $this, 'sanitize_checkbox' ) );
		add_filter( 'sanitize_option_wpjmel_start_geo_lat', 'esc_attr' );
		add_filter( 'sanitize_option_wpjmel_start_geo_long', 'esc_attr' );
		add_filter( 'sanitize_option_wpjmel_ip_api_localization', array( $this, 'sanitize_ip_api_localization' ) );

		/* Admin Notice */
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );

		/* Scripts */
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
	}


	/**
	 * Register Settings.
	 * This input is added via JS.
	 * @since 2.0.0
	 */
	public function register_geo_settings() {
		register_setting( 'job_manager', 'wpjmel_start_geo_lat' );
		register_setting( 'job_manager', 'wpjmel_start_geo_long' );
	}


	/**
	 * Settings page.
	 * Add an settings tab to the Listings -> settings page.
	 *
	 * @since   1.0.0
	 * @param   array    $settings   Array of default settings.
	 * @return  array    $settings   Array including the new settings.
	 */
	public function settings( $settings )  {

		$settings['wpjmel_settings'] = array(
			__( 'Location', 'wp-job-manager-extended-location' ),
			array(
				array(
					'name'          => 'wpjmel_enable_city_suggest',
					'type'          => 'checkbox',
					'label'         => __( 'Auto Location', 'wp-job-manager-extended-location' ),
					'cb_label'      => __( 'User Location Suggestion', 'wp-job-manager-extended-location' ),
					'desc'          => __( 'Attempt to automatically locate the current user&#39;s location to display location-specific results.', 'wp-job-manager-extended-location' ),
					'std'           => 1,
				),
				array(
					'name'          => 'wpjmel_enable_map',
					'type'          => 'checkbox',
					'label'         => __( 'Submission Form', 'wp-job-manager-extended-location' ),
					'cb_label'      => __( 'Display Map', 'wp-job-manager-extended-location' ),
					'desc'          => __( 'When checked there will be a small Google Map positioned beneath the location field.', 'wp-job-manager-extended-location' ),
					'std'           => 1,
				),
				array(
					'name'          => 'wpjmel_map_start_location',
					'type'          => 'text',
					'label'         => __( 'Default Location', 'wp-job-manager-extended-location' ),
					'desc'          => __( 'The start location if the map is enabled', 'wp-job-manager-extended-location' ),
					'std'           => '',
				),
				array(
					'name'          => 'wpjmel_ip_api_localization',
					'type'          => 'select',
					'std'           => WP_Job_Manager_Extended_Location::ip_api_lang_default(),
					'options'       => WP_Job_Manager_Extended_Location::ip_api_valid_lang(),
					'label'         => __( 'IP-API.com Localization', 'wp-job-manager-extended-location' ),
					'desc'          => __( 'Localization for user location based on IP address using IP-API.com.', 'wp-job-manager-extended-location' )
				),
				array(
					'name'         => 'wp-job-manager-extended-location',         // plugin slug
					'type'         => 'wp-job-manager-extended-location_license', // {plugin_slug}_license
					'std'          => '',
					'placeholder'  => '',
					'label'        => __( 'License Key', 'wp-job-manager-extended-location' ),
					'desc'         => __( 'Enter the license key you received with your purchase receipt to continue receiving plugin updates.', 'wp-job-manager-extended-location' )
				),
			),
		);
		return $settings;
	}

	/**
	 * Admin Notice
	 * Display notice if Google Maps Api Key is not set.
	 */
	public function admin_notices(){
		$map_key = strip_tags( trim( get_option( 'job_manager_google_maps_api_key' ) ) );
		if( ! $map_key ){
			$url = add_query_arg( array(
				'post_type'  => 'job_listing',
				'page'       => 'job-manager-settings',
			), admin_url( 'edit.php' ) );
			?>
			<div class="notice notice-info is-dismissible">
				<p><?php printf( __( 'Please input your Google Maps API Key in <a href="%s">Job Listings Settings</a> to use Extended Location for WP Job Manager.', 'wp-job-manager-extended-location' ), esc_url( $url ) ); ?></p>
			</div>
			<?php
		}
	}


	/**
	 * Admin scripts.
	 * @since 3.0.0
	 */
	public function scripts( $hook_suffix ){

		/* Settings Page */
		if( 'job_listing_page_job-manager-settings' == $hook_suffix ){
			$wpjmel = WP_Job_Manager_Extended_Location::instance();

			wp_enqueue_style( 'wpjmel-settings', $wpjmel->url . 'assets/settings/settings.css', array( 'mapify' ), $wpjmel->version );
			wp_enqueue_script( 'wpjmel-settings', $wpjmel->url . 'assets/settings/settings.js', array( 'jquery', 'google-maps', 'mapify' ), $wpjmel->version, true );

			$options = array(
				'lat'         => esc_attr( get_option( 'wpjmel_start_geo_lat', 40.712784 ) ),
				'lng'         => esc_attr( get_option( 'wpjmel_start_geo_long', -74.005941 ) ),
				'lat_input'   => 'wpjmel_start_geo_lat',
				'lng_input'   => 'wpjmel_start_geo_long',
			);
			wp_localize_script( 'wpjmel-settings', 'wpjmel', $options );
		}
	}


	/**
	 * Sanitize Checkbox
	 * @since 3.0.0
	 */
	public function sanitize_checkbox( $input ){
		return $input ? 1 : 0;
	}

	/**
	 * Sanitize IP API Localization.
	 *
	 * @since 3.4.0
	 *
	 * @param  $input string Option value.
	 * @return string Valid output.
	 */
	public function sanitize_ip_api_localization( $input ){
		return array_key_exists( $input, WP_Job_Manager_Extended_Location::ip_api_valid_lang() ) ? $input : 'en';
	}

}
