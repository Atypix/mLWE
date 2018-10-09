<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Emails_Shortcodes_Resume
 *
 * @since 2.0.0
 *
 */
class WP_Job_Manager_Emails_Shortcodes_Resume extends WP_Job_Manager_Emails_Shortcodes {

	/**
	 * @var string
	 */
	public $post_title   = '[candidate_name]';
	/**
	 * @var string
	 */
	public $post_content = '[resume_content]';
	/**
	 * @var string
	 */
	public $submitted_by = '[candidate_name]';

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

		$shortcodes = apply_filters( 'job_manager_emails_resume_shortcodes',
            array(
				'resume_id' => array(
					'label' => __( 'Resume ID', 'wp-job-manager-emails' ),
					'nonmeta' => TRUE
				),
				'application_message' => array(
					'label' => __( 'User Application Message', 'wp-job-manager-emails' ),
					'nonmeta' => TRUE
				),
				'candidate_name'   => array(
					'label' => sprintf( __( '%s Candidate Name', 'wp-job-manager-emails' ), $singular ),
					'description' => sprintf( __( '%s Candidate Name', 'wp-job-manager-emails' ), $singular ),
					'meta_key'   => 'post_title',
					'post_title' => TRUE,
					'required'   => TRUE
				),
				'resume_content' => array(
					'label' => sprintf( __( '%s Content', 'wp-job-manager-emails' ), $singular ),
					'description'  => sprintf( __( 'Main %s Content', 'wp-job-manager-emails' ), $singular ),
					'meta_key'     => 'post_content',
					'post_content' => TRUE,
					'required'     => TRUE
				),
				'resume_dashboard_url' => array(
					'label'       => sprintf( __( '%s Dashboard URL', 'wp-job-manager-emails' ), $singular ),
					'description' => sprintf( __( 'URL to the %s Dashboard', 'wp-job-manager-emails' ), $singular ),
					'nonmeta'     => TRUE
				),
				'resume_url'            => array(
					'label'       => sprintf( __( '%s URL', 'wp-job-manager-emails' ), $singular ),
					'description' => sprintf( __( '%s frontend URL (to view only when resume is published)', 'wp-job-manager-emails' ), $singular ),
					'nonmeta'     => TRUE
				),
				'view_resume_url'            => array(
					'label'       => sprintf( __( 'View %s URL', 'wp-job-manager-emails' ), $singular ),
					'description' => sprintf( __( '%s share link URL (to view resume regardless of status)', 'wp-job-manager-emails' ), $singular ),
					'nonmeta'     => TRUE
				),
				'view_resume_url_admin' => array(
					'label'       => sprintf( __( '%s Admin URL', 'wp-job-manager-emails' ), $singular ),
					'description' => sprintf( __( '%s Admin Area Edit/View URL', 'wp-job-manager-emails' ), $singular ),
					'nonmeta'     => TRUE
				),
				'resume_fields'             => array(
					'label'       => sprintf( __( 'All %s Fields', 'wp-job-manager-emails' ), $singular ),
					'description' => sprintf( __( 'Will output all %s in list format', 'wp-job-manager-emails' ), $singular ),
					'callback'    => 'output_all_fields',
					'nonmeta'     => TRUE
				),
                'new_resume_recipient' => array(
	                'label' => sprintf( __( 'New %s Recipient', 'wp-job-manager-emails' ), $singular ),
	                'description' => sprintf( __( 'Value of the "Notify Email Address(es)" Resume Manager setting, if set, otherwise the admin email.', 'wp-job-manager-emails' ), $singular ),
	                'nonmeta'     => TRUE
                ),
				'resume_author_email' => array(
					'label'       => __( 'Resume Author Email', 'wp-job-manager-emails' ),
					'description' => __( 'Email address of the author who submitted the resume listing', 'wp-job-manager-emails' ),
					'callback'    => 'resume_author_email',
					'nonmeta'     => TRUE
				),
				'resume_expires' => array(
					'label'       => sprintf( __( '%s Expires Date', 'wp-job-manager-emails' ), $singular ),
					'description' => __( 'Will output the expiration date', 'wp-job-manager-emails' ),
					'nonmeta'     => TRUE,
					'callback'    => 'resume_expires'
				),
				'featured' => array(
					'label'       => sprintf( __( '%s Featured', 'wp-job-manager-emails' ), $singular ),
					'description' => __( 'Whether or not a listing is a featured listing', 'wp-job-manager-emails' ),
				),
				'resume_meta' => array(
					'label'       => sprintf( __( '%s Custom Meta', 'wp-job-manager-emails' ), $singular ),
					'description' => __( 'Can be used to output any custom meta from a listing, passing exact meta key in the key argument', 'wp-job-manager-emails' ),
					'nonmeta'     => true,
					'callback'    => 'resume_meta',
					'args'        => array(
						'key' => array(
							'desc'     => __( 'The exact meta key to use and obtain the value from', 'wp-job-manager-emails' ),
							'value'    => __( 'Exact meta key', 'wp-job-manager-emails' ),
							'required' => true,
							'example'  => 'some_meta_key'
						)
					)
				),
			)
		);

		$this->shortcodes = $shortcodes;

		return $shortcodes;

	}

	/**
	 * Get any custom resume meta field through shortcode
	 *
	 *
	 * @since 2.4.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return string
	 */
	public function resume_meta( $args = array(), $content = '' ) {

		if ( empty( $args ) || ! array_key_exists( 'key', $args ) ) {
			return '';
		}

		$meta_value = get_post_meta( $this->get_resume_id(), trim( $args['key'] ), true );

		return apply_filters( 'job_manager_emails_shortcode_resume_meta_return', $meta_value, $this->get_resume_id(), $this );
	}

	/**
	 * [resume_expires]
	 *
	 *
	 * @since @@since
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return string
	 */
	public function resume_expires( $args = array(), $content = '' ) {

		$resume_expires = get_post_meta( $this->get_job_id(), '_resume_expires', TRUE );

		if ( empty( $resume_expires ) ) {
			return '';
		}

		$resume_expires = strtotime( $resume_expires );

		return date_i18n( get_option( 'date_format' ), $resume_expires );
	}

	/**
	 * Get Resume ID
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return int
	 */
	function resume_id( $args = array(), $content = '' ){
		return $this->get_the_id();
	}

	/**
	 * [resume_author_email]
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return string
	 */
	function resume_author_email( $args = array(), $content = '' ) {

		$post_author = get_post_field( 'post_author', $this->get_resume_id() );
		if ( empty( $post_author ) ) return '';

		$author_email = get_the_author_meta( 'user_email', $post_author );

		return $author_email;
	}

	/**
	 * [new_resume_recipient]
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return mixed|void
	 */
	function new_resume_recipient( $args = array(), $content = '' ){

		$new_resume_recipient = get_option( 'resume_manager_email_notifications' );
		$new_resume_recipient = ! empty($new_resume_recipient) ? $new_resume_recipient : get_option( 'admin_email' );

		return $new_resume_recipient;
	}

	/**
	 * Return Resume Post ID
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	function get_the_id() {
		return $this->get_resume_id();
	}

	/**
	 * [candidate_name]
	 *
	 * Will check for meta value in _candidate_name, and if empty or does not exist, will
	 * return the value from the post `post_title`
	 *
	 * @since 1.0.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return mixed|null|string
	 */
	function candidate_name( $args = array(), $content = '' ){

		$name = get_post_meta( $this->get_the_id(), '_candidate_name', true );
		if( ! empty( $name ) ) return $name;

		$post = get_post( $this->get_the_id() );
		if( ! is_object( $post ) || ! isset( $post->post_title ) ) return '';

		return $post->post_title;
	}

	/**
	 * [resume_content]
	 *
	 * Will check for meta value in _resume_content, and if empty or doesn't exist, will
	 * return the value from the post `post_content`
	 *
	 * @since 1.0.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return mixed|null|string
	 */
	function resume_content( $args = array(), $content = '' ){

		$resume_content = get_post_meta( $this->get_resume_id(), '_resume_content', TRUE );
		if( ! empty($resume_content) ) return $resume_content;

		$post = get_post( $this->get_resume_id() );
		if( ! is_object( $post ) || ! isset($post->post_content) ) return '';

		$resume_content = $this->cpt()->hooks()->is_html_email() ? wpautop( $post->post_content ) : $post->post_content;

		return $resume_content;
	}

	/**
	 * [view_resume_url]
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	function view_resume_url( $args = array(), $content = '' ) {
		if( ! function_exists( 'get_resume_share_link' ) ) return '';

		return get_resume_share_link( $this->get_resume_id() );
	}

	/**
	 * [resume_url]
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	function resume_url( $args = array(), $content = '' ) {
		return get_permalink( $this->get_resume_id() );
	}

	/**
	 * [view_resume_url_admin]
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return null|string
	 */
	function view_resume_url_admin( $args = array(), $content = '' ){
		return $this->get_edit_post_link( $this->get_resume_id() );
	}

	/**
	 * [application_message]
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	function application_message( $args = array(), $content = '' ) {

		return str_replace( '[nl]', "\n", sanitize_text_field( str_replace( "\n", '[nl]', strip_tags( stripslashes( $_POST['application_message'] ) ) ) ) );
	}

	/**
	 * [candidate_email]
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return mixed|null|string
	 */
	function candidate_email( $args = array(), $content = '' ){

		$email = get_post_meta( $this->get_resume_id(), '_candidate_email', TRUE );

		if( empty($email) ) {
			$user = get_current_user();
			$email = is_object( $user ) && isset( $user->user_email ) ? $user->user_email : '';
		}

		return $email;
	}

	/**
	 * [candidate_education]
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $value
	 *
	 * @return string
	 */
	function candidate_education( $args = array(), $content = '' ) {

		$educations = maybe_unserialize( get_post_meta( $this->get_resume_id(), '_candidate_education', TRUE ) );

		if( empty( $educations ) || ! is_array( $educations ) ) return '';

		$output = '';
		$output .= __( 'Education:', 'wp-job-manager-resumes', 'wp-job-manager-emails' ) . "\n" . "\n";
		foreach( $educations as $key => $item ) {
			$output .= sprintf( __( 'Location: %s', 'wp-job-manager-resumes', 'wp-job-manager-emails' ), $item['location'] ) . "\n";
			$output .= sprintf( __( 'Date: %s', 'wp-job-manager-resumes', 'wp-job-manager-emails' ), $item['date'] ) . "\n";
			$output .= sprintf( __( 'Qualification: %s', 'wp-job-manager-resumes', 'wp-job-manager-emails' ), $item['qualification'] ) . "\n";
			$output .= $item['notes'] . "\n" . "\n";
		}

		return $output;
	}

	/**
	 * [candidate_experience]
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $value
	 *
	 * @return string
	 */
	function candidate_experience( $args = array(), $content = '' ) {

		$experiences = maybe_unserialize( get_post_meta( $this->get_resume_id(), '_candidate_experience', TRUE ) );

		if( empty($experiences) || ! is_array( $experiences ) ) return '';

		$output = '';
		$output .= __( 'Experience:', 'wp-job-manager-resumes', 'wp-job-manager-emails' ) . "\n" . "\n";
		foreach( $experiences as $key => $item ) {
			$output .= sprintf( __( 'Employer: %s', 'wp-job-manager-resumes', 'wp-job-manager-emails' ), $item['employer'] ) . "\n";
			$output .= sprintf( __( 'Date: %s', 'wp-job-manager-resumes', 'wp-job-manager-emails' ), $item['date'] ) . "\n";
			$output .= sprintf( __( 'Job Title: %s', 'wp-job-manager-resumes', 'wp-job-manager-emails' ), $item['job_title'] ) . "\n";
			$output .= $item['notes'] . "\n" . "\n";
		}

		return $output;
	}

	/**
	 * [links]
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $value
	 *
	 * @return string
	 */
	function links( $args = array(), $content = '' ) {

		$links = maybe_unserialize( get_post_meta( $this->get_resume_id(), '_links', TRUE ) );

		if( empty($links) || ! is_array( $links ) ) return '';

		$output = '';
		$output .= __( 'Links:', 'wp-job-manager-resumes', 'wp-job-manager-emails' ) . "\n" . "\n";
		foreach( $links as $key => $item ) {
			$output .= $item['name'] . ': ' . $item['url'] . "\n";
		}

		return $output;
	}

	/**
	 * [resume_dashboard_url]
	 *
	 *
	 * @since @@since
	 *
	 * @return string
	 */
	function resume_dashboard_url( $args = array(), $content = '' ) {

		$dashboard_id      = get_option( 'resume_manager_candidate_dashboard_page_id' );
		$resume_dashboard_url = $dashboard_id ? get_permalink( $dashboard_id ) : '';

		return $resume_dashboard_url;
	}
}