<?php

/* Load Class */
WPJMS_Stat_Visits::get_instance();

/**
 * Stat: Visits
 */
class WPJMS_Stat_Visits extends WPJMS_Stat {

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
		$this->post_types = array( 'job_listing' );
		$this->stat_id    = 'visits';
		$this->stat_label = __( 'Visits', 'wp-job-manager-stats' );
		parent::__construct();
	}
}
