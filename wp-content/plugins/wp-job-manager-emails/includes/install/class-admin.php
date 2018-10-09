<?php

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Emails_Install_Admin extends WP_Job_Manager_Emails_Install {

	/**
	 * WP_Job_Manager_Emails_Install_Admin constructor.
	 *
	 * @param $admin WP_Job_Manager_Emails_Admin
	 */
	public function __construct( $admin ) {
		$this->admin = $admin;
	}

}