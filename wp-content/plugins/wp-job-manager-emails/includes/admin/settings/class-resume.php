<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Emails_Admin_Settings_Resume extends WP_Job_Manager_Emails_Admin_Settings {

	function hooks(){
		add_filter( 'resume_manager_settings', array( $this, 'add_tab' ) );
	}

	function add_fields(){

		$label = $this->type->get_label();

		$fields = array(
			array(
				'name'       => 'resume_manager_submission_notification',
				'std'        => '0',
				'label'      => sprintf( __( 'New %s Notification', 'wp-job-manager-emails' ), $label ),
				'cb_label'   => __( 'Enable', 'wp-job-manager-emails' ),
				'desc'       => sprintf( __( 'Email %s details to the admin/notification recipient after submission.', 'wp-job-manager-emails' ), strtolower( $label ) ),
				'type'       => 'checkbox',
				'attributes' => array()
			),
		);

		return array();
	}
}