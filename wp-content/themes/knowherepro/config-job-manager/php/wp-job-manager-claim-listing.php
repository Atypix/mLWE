<?php

class Knowhere_WP_Job_Manager_Claim_Listing {

	public function __construct() {

		if ( !defined( 'WPJMCL_VERSION' ) ) return;

		$job_listing = wpjmcl\job_listing\Setup::get_instance();
		remove_action( 'single_job_listing_start', array( $job_listing, 'add_claim_link' ) );
		add_action( 'knowhere_job_listing_actions_start', array( $job_listing, 'add_claim_link') );
	}

}

new Knowhere_WP_Job_Manager_Claim_Listing();
