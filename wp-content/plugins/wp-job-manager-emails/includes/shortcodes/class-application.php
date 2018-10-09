<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Emails_Shortcodes_Application
 *
 * @since 2.0.0
 *
 */
class WP_Job_Manager_Emails_Shortcodes_Application extends WP_Job_Manager_Emails_Shortcodes {

	/**
	 * @var string
	 */
	public $post_title   = '[candidate_name]';
	/**
	 * @var string
	 */
	public $post_content = '[message]';
	/**
	 * @var string
	 */
	public $submitted_by = '[candidate_name]';

	/**
	 * Add Application Shortcodes to $this->shortcodes
	 *
	 *
	 * @since @@since
	 *
	 * @return mixed|void
	 */
	function init_shortcodes(){

		$singular = $this->cpt()->get_singular();

		$shortcodes = apply_filters( 'job_manager_emails_application_shortcodes',
            array(
				'from_name' => array(
					'label' => __( 'Full name (field with from_name rule)', 'wp-job-manager-emails' ),
					'nonmeta' => TRUE,
					'meta_key' => 'post_title',
					'post_title' => true
				),
				'from_email' => array(
					'label' => __( 'User Application Message (field with from_email rule)', 'wp-job-manager-emails' ),
					'nonmeta' => TRUE
				),
				'application_id' => array(
					'label' => __( 'Application ID', 'wp-job-manager-emails' ),
					'nonmeta' => TRUE,
				),
				'application_rating' => array(
					'label' => __( 'Rating (out of 5)', 'wp-job-manager-emails' ),
					'nonmeta' => TRUE,
					'meta_key' => '_rating',
				),
				'message' => array(
					'label' => __( 'User Application Message (field with message rule)', 'wp-job-manager-emails' ),
					'nonmeta' => TRUE,
					'meta_key' => 'post_content',
					'post_content' => true
				),
				'application_attachments' => array(
					'label' => __( 'All attachment fields (fields with attachment rule)', 'wp-job-manager-emails' ),
					'nonmeta' => TRUE,
					'meta_key' => '_attachment',
				),
				'application_fields'             => array(
					'label'       => sprintf( __( 'All %s Fields', 'wp-job-manager-emails' ), $singular ),
					'description' => sprintf( __( 'Will output all %s fields in list format', 'wp-job-manager-emails' ), $singular ),
					'callback'    => 'output_all_fields',
					'nonmeta'     => TRUE
				),
				'meta_data'             => array(
					'label'       => sprintf( __( 'All %s Fields (same as application_fields)', 'wp-job-manager-emails' ), $singular ),
					'description' => sprintf( __( 'Will output all %s fields in list format', 'wp-job-manager-emails' ), $singular ),
					'callback'    => 'output_all_fields',
					'nonmeta'     => TRUE
				),
				'job_dashboard_url' => array(
					'label'       => sprintf( __( '%s Dashboard URL', 'wp-job-manager-emails' ), $singular ),
					'description' => sprintf( __( 'URL to the %s Dashboard', 'wp-job-manager-emails' ), $singular ),
					'nonmeta'     => TRUE
				),
				'view_app_url_admin' => array(
					'label'       => sprintf( __( '%s Admin URL', 'wp-job-manager-emails' ), $singular ),
					'description' => sprintf( __( 'URL to view the %s in admin section', 'wp-job-manager-emails' ), $singular ),
					'nonmeta'     => TRUE
				),
				'view_app_url' => array(
					'label'       => sprintf( __( 'View %s Frontend URL', 'wp-job-manager-emails' ), $singular ),
					'description' => sprintf( __( 'URL to view the %s in Job Dashboard on frontend.', 'wp-job-manager-emails' ), $singular ),
					'nonmeta'     => TRUE
				),
				//'application_note' => array(
				//	'label'       => sprintf( __( '%s Note' ), $singular ),
				//	'description' => sprintf( __( 'Content value of an %s note.' ), $singular ),
				//	'nonmeta'     => TRUE
				//),
				'app_meta' => array(
					'label'       => sprintf( __( '%s Custom Meta', 'wp-job-manager-emails' ), $singular ),
					'description' => __( 'Can be used to output any custom meta, passing exact meta key in the key argument', 'wp-job-manager-emails' ),
					'nonmeta'     => true,
					'callback'    => 'app_meta',
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
	 * Get any custom app meta field through shortcode
	 *
	 *
	 * @since 2.4.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return string
	 */
	public function app_meta( $args = array(), $content = '' ) {

		if ( empty( $args ) || ! array_key_exists( 'key', $args ) ) {
			return '';
		}

		$meta_value = get_post_meta( $this->get_app_id(), trim( $args['key'] ), true );

		return apply_filters( 'job_manager_emails_shortcode_app_meta_return', $meta_value, $this->get_app_id(), $this );
	}

	/**
	 * [application_id]
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return int
	 */
	function application_id( $args = array(), $content = '' ) {
		return $this->get_the_id();
	}

	/**
	 * [application_attachments]
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return mixed
	 */
	function application_attachments( $args = array(), $content = '' ){

		$app_id = $this->get_app_id();
		$attachments = maybe_unserialize( get_post_meta( $app_id, '_attachment', true ) );

		if( ! is_array( $attachments ) || empty( $attachments ) ){
			return '';
		}

		$output = implode( apply_filters( 'job_manager_emails_application_attachments_separator', ', ', $attachments, $app_id ), $attachments );

		return $output;
	}

	/**
	 * [from_email]
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return mixed|string
	 */
	function from_email( $args = array(), $content = '' ){

		if( function_exists( 'get_job_application_email' ) ){
			return get_job_application_email( $this->get_app_id() );
		} else {
			$candidate_email = get_post_meta( $this->get_app_id(), '_candidate_email', true );
			if( $candidate_email ) return $candidate_email;

			$current_user = is_user_logged_in() ? wp_get_current_user() : FALSE;

			if ( $current_user ) {
				return $current_user->user_email;
			}
		}

		return '';
	}

	/**
	 * [from_name]
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return string
	 */
	function from_name( $args = array(), $content = '' ){

		$post = get_post( $this->get_app_id() );

		if( $post && is_object( $post ) ){
			return $post->post_title;
		} else {
			$current_user = is_user_logged_in() ? wp_get_current_user() : FALSE;

			if( $current_user ){
				return $current_user->first_name . ' ' . $current_user->last_name;
			}
		}

		return '';
	}

	/**
	 * [application_note]
	 *
	 *
	 * @since 2.0.3
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return string
	 */
	function application_note( $args = array(), $content = '' ){

		$comment_args    = array(
			'post_id' => $this->get_app_id(),
			'orderby' => array( 'comment_date' ),
			'order'   => 'DESC',
			'number'  => 1
		);

		// Applications addon adds filter to prevent returning comments so we have to remove that before calling get_comments();
		remove_filter( 'comments_clauses', array( 'WP_Job_Manager_Applications_Dashboard', 'exclude_application_comments' ), 10, 1 );
		$comment = get_comments( $comment_args );
		add_filter( 'comments_clauses', array( 'WP_Job_Manager_Applications_Dashboard', 'exclude_application_comments' ), 10, 1 );

		$last_comment = ! empty( $comment ) && isset( $comment[0] ) ? $comment[0] : array();
		$comment_content = ! empty( $last_comment ) ? $last_comment->comment_content : '';

		return $comment_content;

	}

	/**
	 * [message]
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return mixed|string
	 */
	function message( $args = array(), $content = '' ){

		$post = get_post( $this->get_app_id() );

		if( $post ){
			$message = $post->post_content;
		} else {
			$message = get_post_meta( $this->get_app_id(), 'Message', TRUE );
		}

		return $message;
	}

	/**
	 * application_message
	 *
	 *
	 * @since 2.0.2
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return mixed|string
	 */
	function application_message( $args = array(), $content = '' ){
		return $this->message( $args, $content );
	}

	/**
	 * [view_app_url_admin]
	 *
	 *
	 * @since @@since
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return null|string
	 */
	function view_app_url_admin( $args = array(), $content = '' ) {
		return $this->get_edit_post_link( $this->get_app_id() );
	}

	/**
	 * [view_app_url]
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	function view_app_url( $args = array(), $content = '' ) {

		$dashboard_id      = get_option( 'job_manager_job_dashboard_page_id' );
		$job_dashboard_url = $dashboard_id ? htmlspecialchars_decode( add_query_arg( array(
			                                                                             'action' => 'show_applications',
			                                                                             'job_id' => $this->get_job_id()
		                                                                             ), get_permalink( $dashboard_id ) ) ) : '';

		return $job_dashboard_url;
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
		$job_dashboard_url = $dashboard_id ? htmlspecialchars_decode( add_query_arg( array(
			                                                                             'action' => 'show_applications',
			                                                                             'job_id' => $this->get_job_id()
		                                                                             ), get_permalink( $dashboard_id ) ) ) : '';

		return $job_dashboard_url;
	}

	/**
	 * Return Resume ID
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return int|mixed
	 */
	function get_resume_id(){

		if( ! empty( $this->resume_id ) ) {
			return $this->resume_id;
		}

		if( $app_id = $this->get_app_id() ){
			$resume_id = get_post_meta( $app_id, '_resume_id', true );
			if( $resume_id ) return $resume_id;
		}

		return parent::get_resume_id();
	}

	/**
	 * Return Job ID
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return int|mixed
	 */
	function get_job_id(){

		if( $this->job_id ) return $this->job_id;

		if( $app_id = $this->get_app_id() ){
			$job_id = wp_get_post_parent_id( $app_id );

			if( empty( $job_id ) && $job_title = get_post_meta( $app_id, '_job_applied_for', TRUE ) ){

				if( $job_obj = get_page_by_title( $job_title, OBJECT, 'job_listing' ) ){
					$job_id = $job_obj->ID;
				}

			}

			if( $job_id ) return $job_id;
		}

		return parent::get_job_id();
	}

	/**
	 * [application_fields]
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return string
	 */
	function application_fields( $args = array(), $content = '' ){
		return 'app fields';
	}

	/**
	 * Initialize Applications Addon shortcode handling
	 *
	 * This method sets up the required arguments and config values, and passes them to the applications addon function to
	 * add and handle application field shortcodes.  The applications addon will be responsible for replacing shortcode values
	 * in the email template using the addon functions and handling.
	 *
	 *
	 * @since 2.0.2
	 *
	 * @param $app_id
	 *
	 * @return bool
	 */
	function add_app_shortcodes( $app_id ){

		$post = get_post( $app_id );

		if( ! $post ){
			return false;
		}

		$meta = array();
		$post_meta = get_post_meta( $app_id );
		$job_id = ! empty( $_POST[ 'job_id' ] ) ? absint( $_POST[ 'job_id' ] ) : $post->post_parent;
		$user_id = ! empty( $post->_candidate_user_id ) ? $post->_candidate_user_id : get_current_user_id();


		/**
		 * Build meta array values
		 *
		 * Loop through all fields and build out core applications expected $meta array with values.
		 * We loop through all of the fields and build our own to unserialize and make sure the values
		 */
		foreach( get_job_application_form_fields() as $key => $field ){

			if( (bool) array_intersect( array( 'message', 'from_name', 'from_email', 'attachment' ), $field[ 'rules' ] ) ){
				continue;
			}

			$label = $field[ 'label' ];
			$value = isset( $post_meta[ $label ] ) ? maybe_unserialize( $post_meta[ $label ][0] ) : '';

			// Meta is stored with the label as the meta key (why i have no idea)
			$meta[ $label ] = $value;
		}

		$shortcode_data = array(
			'application_id'      => $app_id,
			'job_id'              => $job_id,
			'user_id'             => $user_id,
			'candidate_name'      => $post->post_title,
			'candidate_email'     => $post->_candidate_email,
			'application_message' => $post->post_content,
			'meta'                => $meta
		);

		job_application_email_add_shortcodes( $shortcode_data );

	}

	/**
	 * Check meta key rules
	 *
	 * This method checks the application fields "rules" for specific values, to map them to the correct associated
	 * value or method call when replacing shortcodes.
	 *
	 *
	 * @since 2.0.2
	 *
	 * @param $meta_key
	 *
	 * @return string
	 */
	function check_rules( $meta_key ){

		if( ! function_exists( 'get_job_application_form_fields' ) ){
			return $meta_key;
		}

		$app_fields = get_job_application_form_fields();

		if( array_key_exists( $meta_key, $app_fields ) && ! empty( $app_fields[$meta_key]['rules'] ) && is_array( $app_fields[$meta_key]['rules'] ) ){

			if( in_array( 'from_name', $app_fields[$meta_key]['rules'] ) ){
				$meta_key = 'from_name';
			} elseif( in_array( 'from_email', $app_fields[$meta_key]['rules'] ) ) {
				$meta_key = 'from_email';
			} elseif( in_array( 'message', $app_fields[$meta_key]['rules'] ) ) {
				$meta_key = 'application_message';
			} elseif( in_array( 'attachment', $app_fields[$meta_key]['rules'] ) ) {
				// Unfortunately Applications saves all attachments under one meta key, so we can't differentiate between attachment metakeys
				$meta_key = 'application_attachments';
			}

		}

		return $meta_key;
	}

	/**
	 * Check meta key to pull data from meta
	 *
	 * This method checks the passed meta key in the application field array.  If it's found, returns the label
	 * from that meta key, as that is what needs to be used when pulling the value from the application's post meta.
	 * If the field is not an application meta field, will pass to parent method to handle like normal.
	 *
	 * @since 2.0.2
	 *
	 * @param $meta_key
	 * @param $shortcodes
	 *
	 * @return string Label from application field config (if is application field)
	 */
	function check_meta_key( $meta_key, $shortcodes ){

		if( function_exists( 'get_job_application_form_fields' ) ){

			$app_fields = get_job_application_form_fields();

			// Applications addon stores meta values under the label of a field, so we return the label to check meta with
			if( array_key_exists( $meta_key, $app_fields ) && array_key_exists( 'label', $app_fields[$meta_key] ) ){
				return $app_fields[$meta_key]['label'];
			}

		}

		// Otherwise return parent check_meta_key value (as this method overrides the parent one)
		return parent::check_meta_key( $meta_key, $shortcodes );
	}

	/**
	 * Return Application ID
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return int
	 */
	function get_the_id(){
		return $this->get_app_id();
	}
}