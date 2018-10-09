<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Emails_Admin_Settings_Job extends WP_Job_Manager_Emails_Admin_Settings {

	function hooks(){
		add_filter( 'job_manager_settings', array( $this, 'add_tab' ) );
	}

}