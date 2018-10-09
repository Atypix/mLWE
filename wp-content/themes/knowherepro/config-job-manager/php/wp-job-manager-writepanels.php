<?php
if ( ! class_exists( 'WP_Job_Manager_Writepanels' ) ) {
	include( JOB_MANAGER_PLUGIN_DIR . '/includes/admin/class-wp-job-manager-writepanels.php' );
}

class Knowhere_WP_Job_Manager_Writepanels extends WP_Job_Manager_Writepanels {

	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
	}

	public function add_meta_boxes() {
		add_meta_box( 'knowhere_open_hours', esc_html__( 'Opening Hours', 'knowherepro' ), array( $this, 'display_hours_metabox' ), 'job_listing', 'side', 'low' );
		add_meta_box( 'knowhere_job_manager_details_fields', esc_html__( 'Details Fields', 'knowherepro' ), array( $this, 'display_details_fields_metabox' ), 'job_listing', 'advanced', 'high');
	}

	public function display_hours_metabox( $post ) {
		global $post;

		do_action( 'knowhere_writepanels_open_hours', $post );
	}

	public function display_details_fields_metabox( $post ) {
		global $post;

		do_action( 'knowhere_writepanels_details_fields', $post );
	}

}
