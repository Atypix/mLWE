<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Emails_Admin_MetaBoxes_Application extends WP_Job_Manager_Emails_Admin_MetaBoxes {

	/**
	 * Filters/Actions Specific to this Extending Class
	 *
	 *
	 * @since @@since
	 *
	 */
	function factions(){

		add_filter( 'postbox_classes_jm_app_emails_job_shortcodes', array( $this, 'job_shortcode_classes' ) );

	}

	/**
	 * Add Specific Classes to Job Shortcodes Metabox
	 *
	 * We want the job shortcodes metabox to be hidden by default, and as such, we add our own custom
	 * class using this filter.
	 *
	 *
	 * @since @@since
	 *
	 */
	function job_shortcode_classes( $classes = array() ){

		$classes[] = 'hide-if-js';
		$classes[] = 'jme_hidden_mb';

		return $classes;
	}

	/**
	 *
	 *
	 *
	 * @since @@since
	 *
	 * @return mixed|void
	 */
	function init_meta_boxes(){

		//$job_resume_fields = array_merge( $this->job()->get_fields(), $this->resume()->get_fields() );

		$this->meta_boxes = apply_filters( 'job_manager_emails_apps_meta_boxes',
				array(
					'attachments' => array(
						'id'            => 'attachments_mb',
						'title'         => __( 'Attachments', 'wp-job-manager-emails' ),
						'callback'      => array($this, 'output_attachments'),
						'screen'        => $this->cpt()->get_post_type(),
						'context'       => 'normal',
						'priority'      => 'low',
						'callback_args' => array( 'fields' => $this->cpt()->get_fields() )
					),
					'app_shortcodes' => array(
						'id'            => 'app_shortcodes',
						'title'         => sprintf( __( '%s Shortcodes', 'wp-job-manager-emails' ), $this->cpt()->get_singular() ),
						'callback'      => array($this, 'output_shortcodes'),
						'screen'        => $this->cpt()->get_post_type(),
						'context'       => 'side',
						'priority'      => 'low',
						'callback_args' => array( 'fields' => $this->cpt()->shortcodes()->get_all() )
					),
					'job_shortcodes' => array(
						'id'            => 'job_shortcodes',
						'title'         => sprintf( __( '%s Shortcodes', 'wp-job-manager-emails' ), $this->job()->get_singular() ),
						'callback'      => array($this, 'output_shortcodes'),
						'screen'        => $this->cpt()->get_post_type(),
						'context'       => 'side',
						'priority'      => 'low',
						'callback_args' => array( 'fields' => $this->job()->shortcodes()->get_all(), 'hidden' => true )
					),
				)
			);

		if( $this->resume() ){

			$this->meta_boxes['resume_shortcodes'] = array(
				'id'            => 'resume_shortcodes',
				'title'         => sprintf( __( '%s Shortcodes', 'wp-job-manager-emails' ), $this->resume()->get_singular() ),
				'callback'      => array($this, 'output_shortcodes'),
				'screen'        => $this->cpt()->get_post_type(),
				'context'       => 'side',
				'priority'      => 'low',
				'callback_args' => array( 'fields' => $this->resume()->shortcodes()->get_all(), 'hidden' => true )
			);

		}

		return $this->meta_boxes;

	}
}