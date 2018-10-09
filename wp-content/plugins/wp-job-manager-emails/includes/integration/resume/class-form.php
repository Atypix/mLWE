<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Job_Manager_Form' ) )
	include get_plugin_directory_path( 'WP Job Manager', JOB_MANAGER_PLUGIN_DIR ) . '/includes/abstracts/abstract-wp-job-manager-form.php';

if ( ! class_exists( 'WP_Resume_Manager_Form_Submit_Resume' ) )
	require_once get_plugin_directory_path( 'WP Job Manager - Resume Manager', RESUME_MANAGER_PLUGIN_DIR ) . '/includes/forms/class-wp-resume-manager-form-submit-resume.php';

/**
 * Class WP_Job_Manager_Emails_Integration_Resume_Form
 *
 * @since 1.0.0
 *
 */
Class WP_Job_Manager_Emails_Integration_Resume_Form extends WP_Resume_Manager_Form_Submit_Resume {

	private $wprm;

	function __construct() {

		$this->wprm = WP_Resume_Manager_Form_Submit_Resume::instance();

	}

	function wprm() {

		if ( ! $this->wprm ) $this->wprm = WP_Resume_Manager_Form_Submit_Resume::instance();

		return $this->wprm;

	}

	function get_current_step(){
		return $this->wprm->get_step();
	}

	/**
	 * Null Resume $fields
	 *
	 * @since 1.0.0
	 *
	 */
	function remove_traces(){
		$this->wprm()->fields = null;
	}

	/**
	 * Null and regenerate Resume $fields
	 *
	 * @since 1.0.0
	 *
	 * @param string $type
	 */
	function regenerate_fields( $type ){

		$this->remove_traces();

		if( $type == 'resume_fields' ){
			$this->wprm()->init_fields();
		}
	}

	/**
	 * Get all Resume $fields
	 *
	 * @since 1.0.0
	 *
	 * @param string $type
	 *
	 * @return array
	 */
	function get_all_fields( $type = 'resume_fields' ){

		$this->regenerate_fields( $type );

		return $this->wprm()->get_fields( $type );
	}

}