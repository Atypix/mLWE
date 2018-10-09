<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Emails_Admin_MetaBoxes_Job extends WP_Job_Manager_Emails_Admin_MetaBoxes {

	function init_meta_boxes(){

		$this->meta_boxes = apply_filters( 'job_manager_emails_job_meta_boxes',
		                                   array(
			                                   'attachments'       => array(
				                                   'id'            => 'attachments_mb',
				                                   'title'         => __( 'Attachments', 'wp-job-manager-emails' ),
				                                   'callback'      => array($this, 'output_attachments'),
				                                   'screen'        => $this->cpt()->get_post_type(),
				                                   'context'       => 'normal',
				                                   'priority'      => 'low',
				                                   'callback_args' => array( 'fields' => $this->job()->get_fields() )
			                                   ),
			                                   'job_shortcodes'    => array(
				                                   'id'            => 'job_shortcodes',
				                                   'title'         => sprintf( __( '%s Shortcodes', 'wp-job-manager-emails' ), $this->cpt()->get_singular() ),
				                                   'callback'      => array($this, 'output_shortcodes'),
				                                   'screen'        => $this->cpt()->get_post_type(),
				                                   'context'       => 'side',
				                                   'priority'      => 'low',
				                                   'callback_args' => array('fields' => $this->job()->shortcodes()->get_all() )
			                                   ),
		                                   )
		);

		return $this->meta_boxes;

	}

}