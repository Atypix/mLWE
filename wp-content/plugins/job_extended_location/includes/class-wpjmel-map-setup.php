<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 *  Class WPJMEL_Map_Setup.
 *  @since 3.0.0
 */
class WPJMEL_Map_Setup{

	/**
	 * Constructor.
	 * @since 3.0.0
	 */
	public function __construct() {

		/* Save Location Data
		------------------------------------------ */

		/* Save Geo for New Post */
		add_action( 'job_manager_save_job_listing', array( $this, 'save_post' ), 30, 2 );
		add_action( 'resume_manager_save_resume', array( $this, 'save_post' ), 30, 2 );
		add_action( 'wpjm_events_save_event', array( $this, 'save_post' ), 30, 2 );

		/* Save Geo on Update Post */
		add_action( 'job_manager_update_job_data', array( $this, 'save_post' ), 25, 2 );
		add_action( 'resume_manager_update_resume_data', array( $this, 'save_post' ), 25, 2 );
		add_action( 'wpjm_events_update_event_data', array( $this, 'save_post' ), 25, 2 );


		/* Meta Boxes
		------------------------------------------ */

		/* Remove Fields */
		add_filter( 'job_manager_job_listing_data_fields', array( $this, 'remove_job_listing_mb_field' ), 9999 );
		add_filter( 'resume_manager_resume_fields', array( $this, 'remove_resume_mb_field' ), 9999 );

		/* Add Meta Box */
		add_action( 'add_meta_boxes', array( $this, 'add_location_meta_boxes' ), 9 );


		/* Scripts
		------------------------------------------ */

		/* Admin Scripts */
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		/**
		 * Front Scripts
		 * because "job_manager_update_job_data" action is fired too late
		 * (right before rendering output).
		 * wp_enqueue_scripts is too soon to get the updated post meta.
		 * So, scripts with dynamic data need to hooked via hook in form.
		 */
		add_action( 'submit_job_form_start', array( $this, 'scripts' ) );
		add_action( 'submit_resume_form_start', array( $this, 'scripts' ) );

	}


	/* Save Location Data
	------------------------------------------ */

	/**
	 * Save Item Coordinate
	 * @since 3.0.0
	 */
	public function save_post( $post_id, $values ){
		$post_type = get_post_type( $post_id );

		/* Job Listing Location */
		if( 'job_listing' == $post_type && isset( $_POST['_job_location'] ) ){
			if ( update_post_meta( $post_id, '_job_location', sanitize_text_field( $_POST['_job_location'] ) ) ){
				// Location data will be updated by hooked in methods
			}
			elseif ( apply_filters( 'job_manager_geolocation_enabled', true ) && ! WP_Job_Manager_Geocode::has_location_data( $post_id ) ) {
				WP_Job_Manager_Geocode::generate_location_data( $post_id, sanitize_text_field( $_POST['_job_location'] ) );
			}
		}

		/* Resume Candidate Location */
		if( 'resume' == $post_type && isset( $_POST['_candidate_location'] ) ){
				if ( update_post_meta( $post_id, '_candidate_location', sanitize_text_field( $_POST['_candidate_location'] ) ) ) {
					do_action( 'resume_manager_candidate_location_edited', $post_id, sanitize_text_field( $_POST['_candidate_location'] ) );
				}
				elseif ( apply_filters( 'resume_manager_geolocation_enabled', true ) && ! WP_Job_Manager_Geocode::has_location_data( $post_id ) ) {
					WP_Job_Manager_Geocode::generate_location_data( $post_id, sanitize_text_field( $_POST['_candidate_location'] ) );
				}
		}

		/* Update Coordinate */
		if( isset( $_REQUEST['geo_lat'], $_REQUEST['geo_lng'] ) ){
			update_post_meta( $post_id, 'geolocation_lat', esc_attr( $_REQUEST[ 'geo_lat' ] ) );
			update_post_meta( $post_id, 'geolocation_long', esc_attr( $_REQUEST[ 'geo_lng' ] ) );
			/* Lock Pin Status */
			if( isset( $_REQUEST['geo_map_lock_status'] ) ){
				$lock_status = 'lock' == $_REQUEST['geo_map_lock_status'] ? 'lock' : 'unlock';
				update_post_meta( $post_id, 'geolocation_map_lock_status', esc_attr( $lock_status ) );
			}
		}
	}

	/* Meta Boxes
	------------------------------------------ */

	/**
	 * Remove "Location" in Job Listing Fields
	 * @since 3.0.0
	 */
	public function remove_job_listing_mb_field( $fields ){
		unset( $fields['_job_location'] );
		return $fields;
	}

	/**
	 * Remove "Candidate Location" in Resume Fields
	 * @since 3.0.0
	 */
	public function remove_resume_mb_field( $fields ){
		unset( $fields['_candidate_location'] );
		return $fields;
	}

	/**
	 * Add "Location" Meta Box in Job Listing
	 * @since 3.0.0
	 */
	public function add_location_meta_boxes(){
		add_meta_box(
			$id         = 'wpjmel_location',
			$title      = __( 'Job Location', 'wp-job-manager-extended-location' ),
			$callback   = array( $this, 'job_listing_location_meta_box' ),
			$screen     = array( 'job_listing' ),
			$context    = 'normal',
			$priority   = 'high'
		);
		add_meta_box(
			$id         = 'wpjmel_location',
			$title      = __( 'Candidate Location', 'wp-job-manager-extended-location' ),
			$callback   = array( $this, 'resume_location_meta_box' ),
			$screen     = array( 'resume' ),
			$context    = 'normal',
			$priority   = 'high'
		);
	}

	/**
	 * Job Listing Location Meta Box
	 * @since 3.0.0
	 */
	public function job_listing_location_meta_box( $post, $box ){
		$post_id = $post->ID;
		?>
		<p class="description"><?php _e( 'Leave this blank if the location is not important.', 'wp-job-manager-extended-location' ); ?></p>
		<div>
			<input autocomplete="off" type="text" value="<?php echo sanitize_text_field( get_post_meta( $post_id, '_job_location', true ) ); ?>" placeholder="<?php esc_attr_e( 'e.g. "London"', 'wp-job-manager-extended-location' ); ?>" id="_job_location" name="_job_location" class="wpjmel-location widefat">
		</div>
		<?php
	}


	/**
	 * Resume Location Meta Box
	 * @since 3.0.0
	 */
	public function resume_location_meta_box( $post, $box ){
		$post_id = $post->ID;
		?>
		<div>
			<input autocomplete="off" type="text" value="<?php echo sanitize_text_field( get_post_meta( $post_id, '_candidate_location', true ) ); ?>" placeholder="<?php esc_attr_e( 'e.g. "London, UK", "New York", "Houston, TX"', 'wp-job-manager-extended-location' ); ?>" id="_candidate_location" name="_candidate_location" class="wpjmel-location widefat">
		</div>
		<?php
	}


	/* Scripts
	------------------------------------------ */

	/**
	 * Admin Scripts
	 * @since 3.0.0
	 */
	public function admin_scripts( $hook_suffix ){
		global $post_type;

		if( in_array( $hook_suffix, array( 'post.php', 'post-new.php' ) ) && in_array( $post_type, array( 'job_listing', 'resume' ) ) ){
			$wpjmel = WP_Job_Manager_Extended_Location::instance();
			$post_id = get_the_ID();

			/* CSS */
			wp_enqueue_style( 'wpjmel-location-mb', $wpjmel->url . 'assets/writepanels/wpjmel-location-meta-box.css', array( 'mapify' ), $wpjmel->version );

			/* JS */
			wp_enqueue_script( 'wpjmel-location-mb', $wpjmel->url . 'assets/writepanels/wpjmel-location-meta-box.js', array( 'jquery', 'google-maps', 'mapify' ), $wpjmel->version, true );

			$options = array(
				'input'       => '.wpjmel-location',
				'lat'         => esc_attr( get_option( 'wpjmel_start_geo_lat', 40.712784 ) ),
				'lng'         => esc_attr( get_option( 'wpjmel_start_geo_long', -74.005941 ) ),
				'lock'        => 'unlock',
				'lat_input'   => 'geo_lat',
				'lng_input'   => 'geo_lng',
				'lock_input'  => 'geo_map_lock_status',
			);
			$lat = get_post_meta( $post_id, 'geolocation_lat', true );
			$lng = get_post_meta( $post_id, 'geolocation_long', true );
			if( $lat || $lng ){
				$options['lat'] = $lat;
				$options['lng'] = $lng;
			}
			$lock_status = get_post_meta( $post_id, 'geolocation_map_lock_status', true );
			$options['lock'] = 'lock' == $lock_status ? $lock_status : $options['lock'];
			wp_localize_script( 'wpjmel-location-mb', 'wpjmel_mb', $options );
		}
	}

	/**
	 * Enqueue scripts.
	 * @since 3.0.0
	 */
	public function scripts() {
		$item_id  = null;
		$input    = '';
		$page_ids = array();
		/* Job Listing Pages */
		if( $submit_job_page = job_manager_get_page_id( 'submit_job_form' ) ){
			$page_ids[] = intval( $submit_job_page );
			if( is_page( $submit_job_page ) ){
				$input   = "#job_location";
				$item_id = isset( $_REQUEST['job_id'] ) ? intval( $_REQUEST['job_id'] ) : $item_id;

				// check for a cookie
				if ( ! $item_id && ! empty( $_COOKIE['wp-job-manager-submitting-job-id'] ) && ! empty( $_COOKIE['wp-job-manager-submitting-job-key'] ) ) {
					$job_id     = absint( $_COOKIE['wp-job-manager-submitting-job-id'] );
					$job_status = get_post_status( $job_id );

					if ( ( 'preview' === $job_status || 'pending_payment' === $job_status ) && get_post_meta( $job_id, '_submitting_key', true ) === $_COOKIE['wp-job-manager-submitting-job-key'] ) {
						$item_id = $job_id;
					}
				}
			}
		}
		if( $job_dashboard_page = job_manager_get_page_id( 'job_dashboard' ) ){
			$page_ids[] = intval( $job_dashboard_page );
			if( is_page( $job_dashboard_page ) && isset( $_REQUEST['action'] ) && 'edit' == $_REQUEST['action'] ){
				$input   = "#job_location";
				$item_id = isset( $_REQUEST['job_id'] ) ? intval( $_REQUEST['job_id'] ) : $item_id;
			}
		}
		/* Resume Pages */
		if( $submit_resume_page = get_option( 'resume_manager_submit_resume_form_page_id' ) ){
			$page_ids[] = intval( $submit_resume_page );
			if( is_page( $submit_resume_page ) ){
				$input   = "#candidate_location";
				$item_id = isset( $_REQUEST['resume_id'] ) ? intval( $_REQUEST['resume_id'] ) : $item_id;
			}
		}
		if( $resume_dashboard_page = get_option( 'resume_manager_candidate_dashboard_page_id' ) ){
			$page_ids[] = intval( $resume_dashboard_page );
			if( is_page( $resume_dashboard_page ) && isset( $_REQUEST['action'] ) && 'edit' == $_REQUEST['action'] ){
				$input   = "#candidate_location";
				$item_id = isset( $_REQUEST['resume_id'] ) ? intval( $_REQUEST['resume_id'] ) : $item_id;
			}
		}
		/* Event Pages */
		if( $submit_event_page  = get_option( 'wpjm_events_submit_event_form_page_id' ) ){
			$page_ids[] = intval( $submit_event_page );
			if( is_page( $submit_event_page ) ){
				$input   = "#event_location";
				$item_id =  isset( $_REQUEST['event_id'] ) ? intval( $_REQUEST['event_id'] ) : $item_id;
			}
		}

		/* Submit Job Page */
		if ( $input && $page_ids && is_page( $page_ids ) ){
			$wpjmel = WP_Job_Manager_Extended_Location::instance();

			wp_enqueue_style( 'mapify' );
			wp_enqueue_script( 'wpjmel-map', $wpjmel->url . 'assets/front/wpjmel-map.js', array( 'jquery', 'google-maps', 'mapify' ), $wpjmel->version, true );

			$options = array(
				'input'       => esc_attr( $input ),
				'lat'         => esc_attr( get_option( 'wpjmel_start_geo_lat', 40.712784 ) ),
				'lng'         => esc_attr( get_option( 'wpjmel_start_geo_long', -74.005941 ) ),
				'lock'        => 'unlock',
				'lat_input'   => 'geo_lat',
				'lng_input'   => 'geo_lng',
				'lock_input'  => 'geo_map_lock_status',
			);

			/* Get coordinate saved. */
			if ( $item_id ){
				$options['lat'] = get_post_meta( $item_id, 'geolocation_lat', true );
				$options['lng'] = get_post_meta( $item_id, 'geolocation_long', true );

				/* Lock status */
				$options['lock'] = get_post_meta( $item_id, 'geolocation_map_lock_status', true );
			} else if ( isset( $_REQUEST[ 'geo_lat' ] ) ) {
				$options['lat'] = esc_attr( $_REQUEST[ 'geo_lat' ] );
				$options['lng'] = esc_attr( $_REQUEST[ 'geo_lng' ] );

				/* Lock status */
				$options['lock'] = esc_attr( $_REQUEST[ 'geo_map_lock_status' ] );
			}

			wp_localize_script( 'wpjmel-map', 'wpjmel', $options );
		}
	}


} // end class