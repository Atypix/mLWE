<?php

/* Load Class */
WPJMS_Stat_Unique_Visits::get_instance();

/**
 * Stat: Unique Visits
 */
class WPJMS_Stat_Unique_Visits extends WPJMS_Stat {

	/**
	 * Returns the instance.
	 */
	public static function get_instance() {
		static $instance = null;
		if ( is_null( $instance ) ) { $instance = new self;
		}
		return $instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->post_types   = array( 'job_listing' );
		$this->stat_id      = 'unique_visits';
		$this->stat_label   = __( 'Unique Visits', 'wp-job-manager-stats' );
		$this->cookie_name  = 'listings_visited';
		parent::__construct();
	}

	/**
	 * Update Stats on Visits
	 */
	public function update_stat_value( $post_id ) {
		$post_ids = $this->get_cookie();
		if ( ! in_array( $post_id, $post_ids ) ) {
			$update_stat = wpjms_update_stat_value( $post_id, $this->stat_id );
			if ( $update_stat ) {
				$this->add_cookie( $post_id );
			}
		}
	}
}
