<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Emails_Admin_Settings_Application extends WP_Job_Manager_Emails_Admin_Settings {

	function hooks(){
		add_filter( 'job_manager_applications_settings', array( $this, 'add_tab' ) );
	}
}