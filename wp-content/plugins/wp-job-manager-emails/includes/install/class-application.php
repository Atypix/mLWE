<?php

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Emails_Install_Application extends WP_Job_Manager_Emails_Install {


	/**
	 * WP_Job_Manager_Emails_Install_Job constructor.
	 *
	 * @param $admin WP_Job_Manager_Emails_Admin_Job
	 */
	public function __construct( $admin ) {

		$this->admin = $admin;
		$this->add_admin_capability();
		$this->add_default_emails();
	}

}