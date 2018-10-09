<?php

if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'WP_Job_Manager_Form' ) )
	include get_plugin_directory_path( 'WP Job Manager', JOB_MANAGER_PLUGIN_DIR ) . '/includes/abstracts/abstract-wp-job-manager-form.php';

if( ! class_exists( 'WP_Job_Manager_Form_Submit_Job' ) )
	require_once get_plugin_directory_path( array( 'text domain' => 'wp-job-manager' ), JOB_MANAGER_PLUGIN_DIR ) . '/includes/forms/class-wp-job-manager-form-submit-job.php';

/**
 * Class WP_Job_Manager_Emails_Integration_Job_Form
 *
 * @since 1.0.0
 *
 */
Class WP_Job_Manager_Emails_Integration_Job_Form extends WP_Job_Manager_Form_Submit_Job {

	private $wpjm;
	/**
	 * Latest git commit version compatible with this plugin.  Will be checked against current theme
	 * to determine if compatibility with current version, or if need of update.
	 *
	 * @var string
	 */
	public static $COMPAT_GIT_COMMIT = "JaoUsTj8ONXwtpJaj4228gEE";

	function __construct() {

		$this->wpjm = WP_Job_Manager_Form_Submit_Job::instance();

	}

	function wpjm() {

		if( ! $this->wpjm ) $this->wpjm = WP_Job_Manager_Form_Submit_Job::instance();

		return $this->wpjm;

	}

	/**
	 * Null Job and Company $fields
	 *
	 * @since 1.0.0
	 */
	function remove_traces() {

		$this->wpjm()->fields = NULL;
	}

	/**
	 * Get All Job Fields
	 *
	 * @since    1.0.0
	 *
	 * @param bool $with_key    Whether or not to return in array with 'job' and 'company' as parent key for fields
	 *
	 * @return array
	 */
	function get_all_fields( $with_key = false ) {

		$this->remove_traces();
		$this->wpjm()->init_fields();

		$job = $this->wpjm()->get_fields( 'job' );
		$company = $this->wpjm()->get_fields( 'company' );

		$fields = $with_key ? array( 'job' => $job, 'company' => $company ) : array_merge( $job, $company );

		return $fields;
	}

	/**
	 * Get Company Fields
	 *
	 * @since 1.0.0
	 *
	 * @param string $type
	 *
	 * @return array
	 */
	function get_company_fields() {

		$this->remove_traces();
		$this->wpjm()->init_fields();

		return $this->wpjm()->get_fields( 'company' );
	}

	/**
	 * Get Job Fields
	 *
	 * @since 1.0.0
	 * @param string $type
	 *
	 * @return array
	 */
	function get_job_fields() {

		$this->remove_traces();
		$this->wpjm()->init_fields();

		return $this->wpjm()->get_fields( 'job' );
	}

}