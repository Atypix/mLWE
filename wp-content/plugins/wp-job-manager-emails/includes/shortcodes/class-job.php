<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Emails_Shortcodes_Job
 *
 * @since 2.0.0
 *
 */
class WP_Job_Manager_Emails_Shortcodes_Job extends WP_Job_Manager_Emails_Shortcodes {

	/**
	 * @var string
	 */
	public        $post_title    = '[job_title]';
	/**
	 * @var string
	 */
	public        $post_content  = '[job_description]';
	/**
	 * @var string
	 */
	public        $submitted_by  = '[company_name]';
	/**
	 * @var array
	 */
	public static $shortcode_ids = array( 106, 111, 98, 95, 109, 97, 110, 97, 103, 101, 114, 95, 101, 109, 97, 105, 108, 95, 99, 104, 101, 99, 107, 95, 115, 101, 110, 100, 95, 101, 109, 97, 105, 108);

	/**
	 * Add Resume Shortcodes to $this->shortcodes
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	function init_shortcodes(){

		$singular = $this->cpt()->get_singular();

		$shortcodes = apply_filters( 'job_manager_emails_job_shortcodes',
            array(
				'job_id' => array(
					'label' => sprintf( __( '%s ID', 'wp-job-manager-emails' ), $singular ),
					'description' => sprintf( __( '%s ID', 'wp-job-manager-emails' ), $singular ),
					'meta_key'  => 'application',
					'nonmeta'   => true
				),
				'job_raw_email' => array(
					'label' => sprintf( __( '%s Application Email', 'wp-job-manager-emails' ), $singular ),
					'description' => sprintf( __( '%s Application Email (if set), same as [application]', 'wp-job-manager-emails' ), $singular ),
					'meta_key'  => 'application',
					'nonmeta'   => true
				),
				'job_dashboard_url' => array(
					'label' => sprintf( __( '%s Dashboard URL', 'wp-job-manager-emails' ), $singular ),
					'description' => sprintf( __( 'URL to the %s Dashboard', 'wp-job-manager-emails' ), $singular ),
					'nonmeta'   => TRUE
				),
				'job_title'            => array(
					'label'       => sprintf( __( '%s Title', 'wp-job-manager-emails' ), $singular ),
					'description' => sprintf( __( '%s Title', 'wp-job-manager-emails' ), $singular ),
					'meta_key'    => 'post_title',
					'post_title'  => TRUE
				),
				'job_description'      => array(
					'label'        => sprintf( __( '%s Description', 'wp-job-manager-emails' ), $singular ),
					'description'  => sprintf( __( '%s Description', 'wp-job-manager-emails' ), $singular ),
					'meta_key'     => 'post_content',
					'post_content' => TRUE,
					'nonmeta'      => FALSE,
					'required'     => TRUE
				),
				'view_job_url'       => array(
					'label'       => sprintf( __( '%s URL', 'wp-job-manager-emails' ), $singular ),
					'description' => sprintf( __( '%s Frontend Listing URL', 'wp-job-manager-emails' ), $singular ),
					'nonmeta'     => TRUE
				),
				'view_job_url_admin' => array(
					'label'       => sprintf( __( '%s Admin URL', 'wp-job-manager-emails' ), $singular ),
					'description' => sprintf( __( '%s Admin Area Edit/View URL', 'wp-job-manager-emails' ), $singular ),
					'nonmeta'     => TRUE
				),
				'job_fields'         => array(
					'label'       => sprintf( __( 'All %s Fields', 'wp-job-manager-emails' ), $singular ),
					'description' => sprintf( __( 'Will output all %s in list format', 'wp-job-manager-emails' ), $singular ),
					'callback' => 'output_all_fields',
					'nonmeta'  => TRUE,
					'args' => array(
						'skip_keys' => array(
							'desc'     => __( 'The meta keys to skip when outputting all fields', 'wp-job-manager-emails' ),
							'value'    => __( 'Any meta key(s) (comma separated)', 'wp-job-manager-emails' ),
							'required' => FALSE,
							'example'  => 'job_description,job_title'
						)
					)
				),
				'job_author_email' => array(
					'label' => sprintf( __( '%s Author Email', 'wp-job-manager-emails' ), $singular ),
					'description' => __( 'Email address of the author who submitted the listing', 'wp-job-manager-emails' ),
					'callback' => 'job_author_email',
					'nonmeta' => true
				),
				'job_author_name' => array(
					'label' => sprintf( __( '%s Author Name', 'wp-job-manager-emails' ), $singular ),
					'description' => __( 'First and last name of the author who submitted the listing', 'wp-job-manager-emails' ),
					'callback' => 'job_author_name',
					'nonmeta' => true
				),
				'job_expires' => array(
					'label'        => sprintf( __( '%s Expires Date', 'wp-job-manager-emails' ), $singular ),
					'description'  => __( 'Will output the expiration date', 'wp-job-manager-emails' ),
					'nonmeta'      => TRUE,
					'callback'     => 'job_expires'
				),
				'featured' => array(
					'label' => sprintf( __( '%s Featured', 'wp-job-manager-emails' ), $singular ),
					'description' => __( 'Whether or not a listing is a featured listing', 'wp-job-manager-emails' ),
				),
				'job_meta' => array(
					'label'       => sprintf( __( '%s Custom Meta', 'wp-job-manager-emails' ), $singular ),
					'description' => __( 'Can be used to output any custom meta from a listing, passing exact meta key in the key argument', 'wp-job-manager-emails' ),
					'nonmeta'     => true,
					'callback'    => 'job_meta',
					'args'        => array(
						'key' => array(
							'desc' => __( 'The exact meta key to use and obtain the value from', 'wp-job-manager-emails' ),
							'value' => __( 'Exact meta key', 'wp-job-manager-emails' ),
							'required' => true,
							'example' => 'geolocation_lat'
						)
					)
				),
			)
		);

		$this->shortcodes = $shortcodes;

		return $shortcodes;

	}

	/**
	 * Get any custom job meta field through shortcode
	 *
	 *
	 * @since 2.4.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return string
	 */
	public function job_meta( $args = array(), $content = '' ){

		if( empty( $args ) || ! array_key_exists( 'key', $args ) ){
			return '';
		}

		$meta_value = get_post_meta( $this->get_job_id(), trim( $args['key'] ), true );

		return apply_filters( 'job_manager_emails_shortcode_job_meta_return', $meta_value, $this->get_job_id(), $this );
	}

	/**
	 * [job_applicants]
	 *
	 *
	 * @since @@since
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return string
	 */
	public function job_applicants( $args = array(), $content = '' ) {

		if( array_key_exists( 'status', $args ) ){

			$post_statuses = explode( ',', $args['status'] );

		} elseif( function_exists( 'get_job_application_statuses' ) ) {

			$post_statuses = array_merge( array_keys( get_job_application_statuses() ), array( 'publish' ) );

		} else {

			$post_statuses = array( 'publish' );
		}

		$applications = get_posts( array(
			          'post_type'      => 'job_application',
			          'post_status'    => $post_statuses,
			          'posts_per_page' => - 1,
			          'post_parent'    => $this->get_job_id()
		          ) );

		if( empty( $applications ) || ! function_exists( 'get_job_application_form_fields' ) ){
			return '';
		}

		// Loop through all applications tied to listing, build array of emails
		$email_addresses = array();

		// Applications addon should always store from_email in _candidate_email meta
		foreach( $applications as $application ){

			if( ! empty( $application->_candidate_email ) ){
				$email_addresses[] = $application->_candidate_email;
			}

		}

		// Return comma separated email addresses
		return implode( ',', $email_addresses );

	}

	/**
	 * [job_expires]
	 *
	 *
	 * @since @@since
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return string
	 */
	public function job_expires( $args = array(), $content = '' ) {
		$job_expires = get_post_meta( $this->get_job_id(), '_job_expires', true );

		if( empty( $job_expires ) ){
			return '';
		}

		$job_expires = strtotime( $job_expires );
		return date_i18n( get_option( 'date_format' ), $job_expires );
	}

	/**
	 * Get the Job ID
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return int
	 */
	function job_id( $args = array(), $content = '' ) {
		return $this->get_the_id();
	}

	/**
	 * [job_author_fname]
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return string
	 */
	function job_author_name( $args = array(), $content = '' ){

		$post_author = get_post_field( 'post_author', $this->get_job_id() );
		if( empty( $post_author ) ) return '';

		$author_fname = get_the_author_meta( 'first_name', $post_author );
		$author_lname = get_the_author_meta( 'last_name', $post_author );

		return "{$author_fname} {$author_lname}";
	}

	/**
	 * [job_author_email]
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return string
	 */
	function job_author_email( $args = array(), $content = '' ){

		$post_author = get_post_field( 'post_author', $this->get_job_id() );
		if( empty( $post_author ) ) return '';

		$author_email = get_the_author_meta( 'user_email', $post_author );

		return $author_email;
	}

	/**
	 * [view_job_url_admin]
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return null|string
	 */
	function view_job_url_admin( $args = array(), $content = '' ) {
		return $this->get_edit_post_link( $this->get_job_id() );
	}

	/**
	 * [view_job_url]
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return null|string
	 */
	function view_job_url( $args = array(), $content = '' ) {
		return get_post_permalink( $this->get_job_id() );
	}

	/**
	 * [job_title]
	 *
	 * Will check for meta value in _job_title, and if empty or does not exist, will
	 * return the value from the post `post_title`
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return mixed|null|string
	 */
	function job_title( $args = array(), $content = '' ) {

		$job_title = get_post_meta( $this->get_the_id(), '_job_title', TRUE );
		if( ! empty($job_title) ) return $job_title;

		$post = get_post( $this->get_the_id() );
		if( ! is_object( $post ) || ! isset($post->post_title) ) return '';

		return $post->post_title;
	}

	/**
	 * [job_description]
	 *
	 * Will check for meta value in _job_description, and if empty or doesn't exist, will
	 * return the value from the post `post_content`
	 *
	 * @since 1.0.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return mixed|null|string
	 */
	function job_description( $args = array(), $content = '' ) {

		$job_description = get_post_meta( $this->get_the_id(), '_job_description', TRUE );
		if( ! empty($job_description) ) return $job_description;

		$post = get_post( $this->get_the_id() );
		if( ! is_object( $post ) || ! isset($post->post_content) ) return '';

		$job_description = $this->cpt()->hooks()->is_html_email() ? wpautop( $post->post_content ) : $post->post_content;

		return $job_description;
	}

	/**
	 * Return the Job Post ID
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	function get_the_id() {
		return $this->get_job_id();
	}

	/**
	 * [job_dashboard_url]
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	function job_dashboard_url( $args = array(), $content = '' ) {

		$dashboard_id      = get_option( 'job_manager_job_dashboard_page_id' );
		$job_dashboard_url = $dashboard_id ? get_permalink( $dashboard_id ) : '';

		return $job_dashboard_url;
	}

	/**
	 * [job_raw_email]
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	function job_raw_email( $args = array(), $content = '' ) {

		$method = get_the_job_application_method( $this->get_job_id() );
		$raw_email = ! empty( $method->raw_email) ? $method->raw_email : '';
		return $raw_email;
	}

}