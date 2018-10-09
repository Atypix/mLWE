<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class WPJMEL_Location_Suggestion_Setup.
 * @version    3.0.0
 * @author     Astoundify
 */
class WPJMEL_Location_Suggestion_Setup{


	/**
	 * Constructor.
	 * @since 3.0.0
	 */
	public function __construct() {

		/* Load Script on Search Form */
		add_action( 'job_manager_job_filters_search_jobs_start', array( $this, 'scripts' ) );
	}


	/**
	 * Scripts.
	 * @since 3.0.0
	 */
	public function scripts(){

		/* Check User Location */
		$wpjmel = WP_Job_Manager_Extended_Location::instance();
		$loc = $wpjmel->get_user_ip_location();

		/* If location */
		if( $loc && isset( $loc['formatted_address'] ) ){

			wp_enqueue_script( 'wpjmel-user-location', $wpjmel->url . 'assets/front/wpjmel-user-location.js', array( 'jquery' ), $wpjmel->version, true );
			wp_localize_script( 'wpjmel-user-location', 'wpjmel_loc', array(
				'address' => sanitize_text_field( $loc['formatted_address'] ) 
			) );
		}
	}

}
