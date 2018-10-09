<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Emails_Hooks_Application
 *
 * @since 2.0.0
 *
 */
class WP_Job_Manager_Emails_Hooks_Application extends WP_Job_Manager_Emails_Hooks {

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
	 * Add Resume Specific Hooks (Filters/Actions)
	 *
	 *
	 * @since @@since
	 *
	 */
	function hooks(){

		add_action( 'save_post_job_application', array( $this, 'setup_short_circuiting' ), 10, 3 );
		add_action( 'job_application_note_data', array( $this, 'job_application_note_data' ), 10, 2 );
	}

	/**
	 * Applications Addon Comment Hack
	 *
	 * Applications addon annoyingly passes same exact data when adding comment for status update, as well as user comments,
	 * and as such, I had to come up with a hack to work around this to prevent emails being sent when status is updated.
	 *
	 * To do this I set the `comment_author_url` to `#` whenever $application_id is empty(), which turns out to be 100% of the
	 * time whenever adding a comment for post status update, because that variable isn't even set ... tsk tsk.
	 *
	 *
	 * @since 2.0.3
	 *
	 * @param $data
	 * @param $application_id
	 *
	 * @return mixed
	 */
	function job_application_note_data( $data, $application_id ){

		// Set author URL on status comment updates ($application_id will never have a value on status updates)
		if( empty( $application_id ) ){
			$data['comment_author_url'] = '#';
		}

		return $data;
	}

	/**
	 * Check if comment added is note on application
	 *
	 * @since @@since
	 *
	 * @param int        $id      The comment ID.
	 * @param WP_Comment $comment Comment object.
	 */
	function new_job_application_note_added( $id, $comment ){

		if( empty( $comment->comment_type ) || $comment->comment_type !== 'job_application_note' ){
			return;
		}

		// Don't send when #, means comment is status update
		if( $comment->comment_author_url === '#' ){
			return;
		}

		$this->cpt()->send_email( 'new_job_application_note_added', wp_get_post_parent_id( $comment->comment_post_ID ), $comment->comment_post_ID, get_post_meta( $comment->comment_post_ID, '_resume_id', true ) );
	}

		/**
	 * Get Default Email Array Keys
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function get_default_email_keys() {

		return apply_filters( 'job_manager_emails_application_default_email_keys', array(
			'new_job_application',
			'preview_to_pending_job_listing',
			'pending_to_publish_job_listing'
		) );
	}

	/**
	 * Setup Applications wp_mail short circuiting
	 *
	 * This method is called when a new application is added to the database, it checks if we
	 * have any custom emails, and if so, adds filters to prevent the candidate email from being
	 * sent, as well as adds a filter to add headers to the employer email that will short circuit
	 * wp_mail, allowing us to send our own emails.
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param $post_ID
	 * @param $post
	 * @param $update
	 *
	 * @return bool|void
	 */
	function setup_short_circuiting( $post_ID, $post, $update ){

		if( $post_ID && $update !== TRUE ){

			$custom_emails = $this->cpt()->get_emails( 'new_job_application' );
			// If we don't have any custom emails, let normal operation resume
			if ( empty( $custom_emails ) ) return FALSE;

			// Add one-off filter return false to prevent candidate email from being sent
			// This is called after the create_application() function completes
			add_filter( 'job_application_candidate_email_content', '__return_false' );

			// Here we add a filter to the headers, which we will add in our own that includes
			// information we can check through the wp_mail filter, and if it exists, short circuit wp_mail
			// main hooks class handles wp_mail short circuiting
			add_filter( 'create_job_application_notification_headers', array($this, 'add_short_circuit_headers'), 999999, 3 );

		}
	}

	/**
	 * Add Header to Email to short circuit wp_mail
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param $headers
	 * @param $job_id
	 * @param $application_id
	 *
	 * @return array|string
	 */
	function add_short_circuit_headers( $headers, $job_id, $application_id ) {

		$new_headers   = array();
		$new_headers[] = "X-Job-Manager-Job-ID: {$job_id}";
		$new_headers[] = "X-Job-Manager-App-ID: {$application_id}";

		// 4th param adds short circuit header
		return $this->add_headers( $headers, $new_headers, TRUE, TRUE );
	}

	/**
	 * Standard New Job Application Hook
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param $application_id
	 * @param $job_id
	 */
	function new_job_application( $application_id, $job_id ) {

		/**
		 * Prevent execution when call from post transition action
		 *
		 * Just so happens that action called by core WPJM `new_job_application`, matches the action called
		 * by post transition action for `new` post status because post type is `job_application`.  This checks
		 * if second arg passed is object (passed is WP_Post object), and exits from method if so.
		 *
		 * @@see https://github.com/Automattic/wp-job-manager-applications/issues/78
		 */
		if( is_object( $job_id ) ) return;

		$resume_id = array_key_exists( 'resume_id', $_POST ) ? absint( $_POST[ 'resume_id' ] ) : FALSE;
		$this->cpt()->send_email( 'new_job_application', $job_id, $application_id, $resume_id );

	}

	/**
	 * Return Default Core Statuses (without filtering)
	 *
	 *
	 * @since 2.1.0
	 *
	 * @param bool $filtered
	 *
	 * @return array
	 */
	public function get_core_statuses( $filtered = false ){

		$statuses = array(
			'new'         => __( 'New', 'wp-job-manager-emails' ),
			'interviewed' => __( 'Interviewed', 'wp-job-manager-emails' ),
			'offer'       => __( 'Offer extended', 'wp-job-manager-emails' ),
			'hired'       => __( 'Hired', 'wp-job-manager-emails' ),
			'rejected'    => __( 'Rejected', 'wp-job-manager-emails' ),
			'archived'    => __( 'Archived', 'wp-job-manager-emails' )
		);

		if( $filtered ){
			$statuses = apply_filters( 'job_application_statuses', $statuses );
		}

		return $statuses;
	}

	/**
	 * Initialize Post Status Actions/Hooks
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return mixed|void
	 */
	function init_ps_actions() {

		$singular    = $this->cpt()->get_singular();
		$singular_lc = strtolower( $singular );
		$ppost_type  = $this->cpt()->get_ppost_type();
		$post_title  = $this->post_title;

		$ps_actions = array(
				"new_to_interviewed_{$ppost_type}"      => array(
					'label'      => sprintf( __( 'New %s Interviewed', 'wp-job-manager-emails' ), $singular ),
					'desc'       => sprintf( __( 'When a %s is updated from New (initial status) to Interviewed', 'wp-job-manager-emails' ), $singular ),
					'defaults'   => array(
						'to'           => '[from_email]',
						'post_content' => $this->default_post_content( 'new', 'interviewed' ),
						'subject'      => sprintf( __( 'New %1$s Interviewed', 'wp-job-manager-emails' ), $singular ),
						'post_title'   => sprintf( __( 'New %s Interviewed (user email)', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => TRUE,
					'job_fields' => TRUE,
					'metaboxes'  => array( 'job_shortcodes' ),
				    'templates' => array(
				    	array(
				    		'label'        => sprintf( __( 'New %s Interviewed (user)', 'wp-job-manager-emails' ), $singular ),
						    'to'           => '[from_email]',
						    'post_content' => $this->default_post_content( 'new', 'interviewed' ),
						    'subject'      => sprintf( __( 'New %1$s Interviewed', 'wp-job-manager-emails' ), $singular ),
						    'post_title'   => sprintf( __( 'New %s Interviewed (user email)', 'wp-job-manager-emails' ), $singular ),
					    )
				    )
				),
				"new_to_offer_{$ppost_type}"            => array(
					'label'      => sprintf( __( 'New %s to Offer Extended', 'wp-job-manager-emails' ), $singular ),
					'desc'       => sprintf( __( 'When a %s is updated from New (initial status) to Offer Extended', 'wp-job-manager-emails' ), $singular ),
					'defaults' => array(
						'to'           => '[from_email]',
						'post_content' => $this->default_post_content( 'new', 'offer extended' ),
						'subject'      => sprintf( __( 'New %1$s Offer Extended', 'wp-job-manager-emails' ), $singular ),
						'post_title'   => sprintf( __( 'New %s Offer Extended (user email)', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => TRUE,
					'job_fields' => TRUE,
					'metaboxes'  => array( 'job_shortcodes' )
				),
				"new_to_archived_{$ppost_type}"         => array(
					'label'      => sprintf( __( 'New %s to Archived', 'wp-job-manager-emails' ), $singular ),
					'desc'       => sprintf( __( 'When a %s is updated from New (initial status) to Archived', 'wp-job-manager-emails' ), $singular ),
					'defaults' => array(
						'to'           => '[from_email]',
						'post_content' => $this->default_post_content( 'new', 'archived' ),
						'subject'      => sprintf( __( 'New %1$s Archived', 'wp-job-manager-emails' ), $singular ),
						'post_title'   => sprintf( __( 'New %s Archived (user email)', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => TRUE,
					'job_fields' => TRUE,
					'metaboxes'  => array( 'job_shortcodes' )
				),
				"new_to_rejected_{$ppost_type}"         => array(
					'label'      => sprintf( __( 'New %s to Rejected', 'wp-job-manager-emails' ), $singular ),
					'desc'       => sprintf( __( 'When a %s is updated from New (initial status) to Rejected', 'wp-job-manager-emails' ), $singular ),
					'defaults' => array(
						'to'           => '[from_email]',
						'post_content' => $this->default_post_content( 'new', 'rejected' ),
						'subject'      => sprintf( __( 'New %1$s Rejected', 'wp-job-manager-emails' ), $singular ),
						'post_title'   => sprintf( __( 'New %s Rejected (user email)', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => TRUE,
					'job_fields' => TRUE,
					'metaboxes'  => array( 'job_shortcodes' )
				),
				"new_to_hired_{$ppost_type}" => array(
					'label'      => sprintf( __( 'New %s to Hired', 'wp-job-manager-emails' ), $singular ),
					'desc'       => sprintf( __( 'When a %s is updated from New (initial status) to Hired.', 'wp-job-manager-emails' ), $singular ),
					'defaults'   => array(
						'to'           => '[from_email]',
						'post_content' => $this->default_post_content( 'new', 'hired' ),
						'subject'      => sprintf( __( 'New %1$s Hired', 'wp-job-manager-emails' ), $singular ),
						'post_title'   => sprintf( __( 'New %1$s Hired (user email)', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => true,
					'job_fields' => true,
					'metaboxes'  => array( 'job_shortcodes' )
				),
				"interviewed_to_offer_{$ppost_type}"    => array(
					'label'      => sprintf( __( 'Interviewed %s to Offer Extended', 'wp-job-manager-emails' ), $singular ),
					'desc'       => sprintf( __( 'When a %s is updated from Interviewed to Offer Extended', 'wp-job-manager-emails' ), $singular ),
					'defaults' => array(
						'to'           => '[from_email]',
						'post_content' => $this->default_post_content( 'interviewed', 'offer extended' ),
						'subject'      => sprintf( __( 'Interviewed %1$s Offer Extended', 'wp-job-manager-emails' ), $singular ),
						'post_title'   => sprintf( __( 'Interviewed %1$s Offer Extended (user email)', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => TRUE,
					'job_fields' => TRUE,
					'metaboxes'  => array( 'job_shortcodes' )
				),
				"interviewed_to_hired_{$ppost_type}"    => array(
					'label'      => sprintf( __( 'Interviewed %s to Hired', 'wp-job-manager-emails' ), $singular ),
					'desc'       => sprintf( __( 'When a %s is updated from Interviewed to Hired.', 'wp-job-manager-emails' ), $singular ),
					'defaults' => array(
						'to'           => '[from_email]',
						'post_content' => $this->default_post_content( 'interviewed', 'hired' ),
						'subject'      => sprintf( __( 'Interviewed %1$s Hired', 'wp-job-manager-emails' ), $singular ),
						'post_title'   => sprintf( __( 'Interviewed %1$s Hired (user email)', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => TRUE,
					'job_fields' => TRUE,
					'metaboxes'  => array( 'job_shortcodes' )
				),
				"interviewed_to_archived_{$ppost_type}" => array(
					'label'      => sprintf( __( 'Interviewed %s to Archived', 'wp-job-manager-emails' ), $singular ),
					'desc'       => sprintf( __( 'When a %s is updated from Interviewed to Archived.', 'wp-job-manager-emails' ), $singular ),
					'defaults' => array(
						'to'           => '[from_email]',
						'post_content' => $this->default_post_content( 'interviewed', 'archived' ),
						'subject'      => sprintf( __( 'Interviewed %1$s Archived', 'wp-job-manager-emails' ), $singular ),
						'post_title'   => sprintf( __( 'Interviewed %1$s Archived (user email)', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => TRUE,
					'job_fields' => TRUE,
					'metaboxes'  => array( 'job_shortcodes' )
				),
				"interviewed_to_rejected_{$ppost_type}" => array(
					'label'      => sprintf( __( 'Interviewed %s Rejected', 'wp-job-manager-emails' ), $singular ),
					'desc'       => sprintf( __( 'When a %s is updated from Interviewed to Rejected.', 'wp-job-manager-emails' ), $singular ),
					'defaults' => array(
						'to'           => '[from_email]',
						'post_content' => $this->default_post_content( 'interviewed', 'rejected' ),
						'subject'      => sprintf( __( 'Interviewed %1$s Rejected', 'wp-job-manager-emails' ), $singular ),
						'post_title'   => sprintf( __( 'Interviewed %1$s Rejected (user email)', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => TRUE,
					'job_fields' => TRUE,
					'metaboxes'  => array( 'job_shortcodes' )
				),
				"offer_to_hired_{$ppost_type}"          => array(
					'label'      => sprintf( __( 'Offer Extended to Hired', 'wp-job-manager-emails' ), $singular ),
					'desc'       => sprintf( __( 'When a %s is updated from Offer Extended to Hired.', 'wp-job-manager-emails' ), $singular ),
					'defaults' => array(
						'to'           => '[from_email]',
						'post_content' => $this->default_post_content( 'offer extended', 'hired' ),
						'subject'      => sprintf( __( 'Offer Extended %1$s Hired', 'wp-job-manager-emails' ), $singular ),
						'post_title'   => sprintf( __( 'Offer Extended %1$s Hired (user email)', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => TRUE,
					'job_fields' => TRUE,
					'metaboxes'  => array( 'job_shortcodes' )
				),
				"offer_to_archived_{$ppost_type}"       => array(
					'label'      => sprintf( __( 'Offer Extended to Archived', 'wp-job-manager-emails' ), $singular ),
					'desc'       => sprintf( __( 'When a %s is updated from Offer Extended to Archived.', 'wp-job-manager-emails' ), $singular ),
					'defaults' => array(
						'to'           => '[from_email]',
						'post_content' => $this->default_post_content( 'offer extended', 'archived' ),
						'subject'      => sprintf( __( 'Offer Extended %1$s Archived', 'wp-job-manager-emails' ), $singular ),
						'post_title'   => sprintf( __( 'Offer Extended %1$s Archived (user email)', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => TRUE,
					'job_fields' => TRUE,
					'metaboxes'  => array( 'job_shortcodes' )
				),
				"offer_to_rejected_{$ppost_type}"       => array(
					'label'      => sprintf( __( 'Offer Extended to Rejected', 'wp-job-manager-emails' ), $singular ),
					'desc'       => sprintf( __( 'When a %s is updated from Offer Extended to Rejected.', 'wp-job-manager-emails' ), $singular ),
					'defaults' => array(
						'to'           => '[from_email]',
						'post_content' => $this->default_post_content( 'offer extended', 'rejected' ),
						'subject'      => sprintf( __( '%1$s Offer Rejected', 'wp-job-manager-emails' ), $singular ),
						'post_title'   => sprintf( __( '%1$s Offer Rejected (user email)', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => TRUE,
					'job_fields' => TRUE,
					'metaboxes'  => array( 'job_shortcodes' )
				),
				"rejected_to_offer_{$ppost_type}"       => array(
					'label'      => sprintf( __( 'Rejected to Offer Extended', 'wp-job-manager-emails' ), $singular ),
					'desc'       => sprintf( __( 'When a %s is updated from Rejected to Offer Extended.', 'wp-job-manager-emails' ), $singular ),
					'defaults' => array(
						'to'           => '[from_email]',
						'post_content' => $this->default_post_content( 'rejected', 'offer extended' ),
						'subject'      => sprintf( __( 'Rejected %1$s to Offer Extended', 'wp-job-manager-emails' ), $singular ),
						'post_title'   => sprintf( __( 'Rejected %1$s to Offer Extended (user email)', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => TRUE,
					'job_fields' => TRUE,
					'metaboxes'  => array( 'job_shortcodes' )
				),
				"rejected_to_interviewed_{$ppost_type}" => array(
					'label'      => sprintf( __( 'Rejected to Interviewed', 'wp-job-manager-emails' ), $singular ),
					'desc'       => sprintf( __( 'When a %s is updated from Rejected to Interviewed.', 'wp-job-manager-emails' ), $singular ),
					'defaults' => array(
						'to'           => '[from_email]',
						'post_content' => $this->default_post_content( 'rejected', 'interviewed' ),
						'subject'      => sprintf( __( 'Rejected %1$s to Interviewed', 'wp-job-manager-emails' ), $singular ),
						'post_title'   => sprintf( __( 'Rejected %1$s to Interviewed (user email)', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => TRUE,
					'job_fields' => TRUE,
					'metaboxes'  => array( 'job_shortcodes' )
				),
				"rejected_to_hired_{$ppost_type}"       => array(
					'label'      => sprintf( __( 'Rejected to Hired', 'wp-job-manager-emails' ), $singular ),
					'desc'       => sprintf( __( 'When a %s is updated from Rejected to Hired.', 'wp-job-manager-emails' ), $singular ),
					'defaults' => array(
						'to'           => '[from_email]',
						'post_content' => $this->default_post_content( 'rejected', 'hired' ),
						'subject'      => sprintf( __( 'Rejected %1$s Hired', 'wp-job-manager-emails' ), $singular ),
						'post_title'   => sprintf( __( 'Rejected %1$s Hired (user email)', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => TRUE,
					'job_fields' => TRUE,
					'metaboxes'  => array( 'job_shortcodes' )
				),
				"rejected_to_archived_{$ppost_type}"    => array(
					'label'      => sprintf( __( 'Rejected to Archived', 'wp-job-manager-emails' ), $singular ),
					'desc'       => sprintf( __( 'When a %s is updated from Rejected to Archived.', 'wp-job-manager-emails' ), $singular ),
					'defaults' => array(
						'to'           => '[from_email]',
						'post_content' => $this->default_post_content( 'rejected', 'archived' ),
						'subject'      => sprintf( __( 'Rejected %1$s Archived', 'wp-job-manager-emails' ), $singular ),
						'post_title'   => sprintf( __( 'Rejected %1$s Archived (user email)', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => TRUE,
					'job_fields' => TRUE,
					'metaboxes'  => array( 'job_shortcodes' )
				),
				"archived_to_offer_{$ppost_type}"       => array(
					'label'      => sprintf( __( 'Archived to Offer Extended', 'wp-job-manager-emails' ), $singular ),
					'desc'       => sprintf( __( 'When a %s is updated from Archived to Offer Extended.', 'wp-job-manager-emails' ), $singular ),
					'defaults'   => array(
						'to'           => '[from_email]',
						'post_content' => $this->default_post_content( 'archived', 'offer extended' ),
						'subject'      => sprintf( __( 'Archived %1$s to Offer Extended', 'wp-job-manager-emails' ), $singular ),
						'post_title'   => sprintf( __( 'Archived %1$s to Offer Extended (user email)', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => true,
					'job_fields' => true,
					'metaboxes'  => array( 'job_shortcodes' )
				),
				"archived_to_interviewed_{$ppost_type}" => array(
					'label'      => sprintf( __( 'Archived to Interviewed', 'wp-job-manager-emails' ), $singular ),
					'desc'       => sprintf( __( 'When a %s is updated from Archived to Interviewed.', 'wp-job-manager-emails' ), $singular ),
					'defaults'   => array(
						'to'           => '[from_email]',
						'post_content' => $this->default_post_content( 'archived', 'interviewed' ),
						'subject'      => sprintf( __( 'Archived %1$s to Interviewed', 'wp-job-manager-emails' ), $singular ),
						'post_title'   => sprintf( __( 'Archived %1$s to Interviewed (user email)', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => true,
					'job_fields' => true,
					'metaboxes'  => array( 'job_shortcodes' )
				),
				"archived_to_hired_{$ppost_type}"       => array(
					'label'      => sprintf( __( 'Archived to Hired', 'wp-job-manager-emails' ), $singular ),
					'desc'       => sprintf( __( 'When a %s is updated from Archived to Hired.', 'wp-job-manager-emails' ), $singular ),
					'defaults'   => array(
						'to'           => '[from_email]',
						'post_content' => $this->default_post_content( 'archived', 'hired' ),
						'subject'      => sprintf( __( 'Archived %1$s Hired', 'wp-job-manager-emails' ), $singular ),
						'post_title'   => sprintf( __( 'Archived %1$s Hired (user email)', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => true,
					'job_fields' => true,
					'metaboxes'  => array( 'job_shortcodes' )
				),
				"archived_to_new_{$ppost_type}"    => array(
					'label'      => sprintf( __( 'Archived to New (initial status)', 'wp-job-manager-emails' ), $singular ),
					'desc'       => sprintf( __( 'When a %s is updated from Archived to New (initial status).', 'wp-job-manager-emails' ), $singular ),
					'defaults'   => array(
						'to'           => '[from_email]',
						'post_content' => $this->default_post_content( 'archived', 'new' ),
						'subject'      => sprintf( __( 'Archived %1$s to New (initial status)', 'wp-job-manager-emails' ), $singular ),
						'post_title'   => sprintf( __( 'Archived %1$s to New (user email)', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => true,
					'job_fields' => true,
					'metaboxes'  => array( 'job_shortcodes' )
				),
				"archived_to_rejected_{$ppost_type}"    => array(
					'label'      => sprintf( __( 'Archived to Rejected', 'wp-job-manager-emails' ), $singular ),
					'desc'       => sprintf( __( 'When a %s is updated from Archived to Rejected.', 'wp-job-manager-emails' ), $singular ),
					'defaults'   => array(
						'to'           => '[from_email]',
						'post_content' => $this->default_post_content( 'archived', 'rejected' ),
						'subject'      => sprintf( __( 'Archived %1$s to Rejected', 'wp-job-manager-emails' ), $singular ),
						'post_title'   => sprintf( __( 'Archived %1$s to Rejected (user email)', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => true,
					'job_fields' => true,
					'metaboxes'  => array( 'job_shortcodes' )
				),
			);

		$ps_actions = $this->add_custom_statuses( $ps_actions );

		$this->ps_actions = apply_filters( 'job_manager_emails_application_post_status_actions', $ps_actions, $this );

		return $this->ps_actions;
	}

	/**
	 * Initialize Application Actions
	 *
	 *
	 * @since @@since
	 *
	 */
	function init_actions() {

		$singular = $this->cpt()->get_singular();

		$actions = array(
			'new_job_application' => array(
				'args'       => 2,
				'label'      => sprintf( __( 'New %s Created', 'wp-job-manager-emails' ), $singular ),
				'callback'   => 'new_job_application',
				'desc'       => sprintf( __( 'When a new Application is created/submitted', 'wp-job-manager-emails' ), $singular ),
				'warning'    => $this->get_new_job_application_email_notices(),
				'hook'       => TRUE,
				'defaults'   => array(
					'to'           => '[application]',
					'post_content' => $this->applications_new_job_application_default_content(),
					'attachments'  => array('resume_file'),
					'subject'      => sprintf( __( 'New %s for [job_title]', 'wp-job-manager-emails' ), $singular ),
					'post_title'   => sprintf( __( 'New %s for [job_title]', 'wp-job-manager-emails' ), $singular ),
				),
				'template'    => array(
					'label' => __( 'New Application Employer Email', 'wp-job-manager-emails' ),
					'desc' => __( 'This email template matches the core WP Job Manager email sent to the employer when a new application is submitted.', 'wp-job-manager-emails' ),
				),
				'job_fields' => TRUE,
				'metaboxes'  => array( 'job_shortcodes' )
			),
			'new_job_application_note_added' => array(
				'args'       => 2,
				'label'      => sprintf( __( 'New %s Note Added', 'wp-job-manager-emails' ), $singular ),
				'callback'   => 'new_job_application_note_added',
				'desc'       => sprintf( __( 'When a new note is added to a %s.', 'wp-job-manager-emails' ), $singular ),
				'hook'       => 'wp_insert_comment',
				'defaults'   => array(
					'to'           => '[job_author_email]',
					'post_content' => $this->new_job_application_note_added_default_content(),
					'subject'      => sprintf( __( 'New %1$s Note Added on %2$s', 'wp-job-manager-emails' ), $singular,'[full-name]' ),
					'post_title'   => sprintf( __( 'New %s Note Added (Listing Author)', 'wp-job-manager-emails' ), $singular ),
				),
				'job_fields' => TRUE, // This adds job attachment fields to hook configuration
				'metaboxes'  => array( 'job_shortcodes' ),
				'shortcodes' => array(
					'application_note' => array(
						'label'        => sprintf( __( 'Application Note', 'wp-job-manager-emails' ), $singular ),
						'description'  => __( 'Will output the content of the note added on an application', 'wp-job-manager-emails' ),
						'callback'     => 'application_note',
						'nonmeta'      => TRUE,
						'templatemeta' => FALSE,
						'visible'      => FALSE
					)
				),
			),
		);

		if ( function_exists( 'get_resume_share_link' ) ) {
			$actions[ 'new_job_application' ][ 'resume_fields' ] = TRUE;
			$actions[ 'new_job_application' ][ 'metaboxes' ][]   = 'resume_shortcodes';
		}

		$this->actions = apply_filters( 'job_manager_emails_resume_actions', $actions );

		return $this->actions;
	}

	/**
	 * Add Custom Core Templates
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	function core_templates(){

		$singular = $this->cpt()->get_singular();

		$templates = array(
			'new_job_application_applicant' => array(
				'label'      => __( 'New Application Applicant Email', 'wp-job-manager-emails' ),
				'desc'       => sprintf( __( 'Email sent to applicant, when a new application is submitted', 'wp-job-manager-emails' ), $singular ),
				'hook'       => 'new_job_application',
				'defaults'   => array(
					'to'           => '[from_email]',
					'post_content' => $this->applications_new_job_application_applicant_default_content(),
					'subject'      => sprintf( __( 'Application for %s submitted!', 'wp-job-manager-emails' ), '[job_title]' ),
					'post_title'   => __( 'New Application Applicant Email', 'wp-job-manager-emails' ),
				),
				'job_fields' => TRUE,
				'metaboxes'  => array('job_shortcodes')
			)
		);

		if ( function_exists( 'get_resume_share_link' ) ) {
			$templates[ 'new_job_application_applicant' ][ 'resume_fields' ] = TRUE;
			$templates[ 'new_job_application_applicant' ][ 'metaboxes' ][]   = 'resume_shortcodes';
		}

		return $templates;
	}

	/**
	 * Default Post Status Application Email Content
	 *
	 *
	 * @since 2.0.3
	 *
	 * @param $from
	 * @param $to
	 *
	 * @return string
	 */
	function default_post_content( $from, $to ){

		$content = '';
		$content .= __( 'Hello', 'wp-job-manager-emails' ) . "\n" . "\n";
		$content .= sprintf( __( 'An application has been updated from %1$s to %2$s.', 'wp-job-manager-emails' ), $from, $to ) . "\n";
		$content .= "\n" . '[meta_data divider]' . "\n";

		return $content;

	}

	/**
	 * Default New Job Application Content
	 *
	 *
	 * @since @@since
	 *
	 * @return string
	 */
	function applications_new_job_application_default_content() {

		$content = '';
		$content .= __( 'Hello', 'wp-job-manager-emails' ) . "\n" . "\n";
		$content .= sprintf( __( 'A candidate (%1$s) has submitted their application for the position "%2$s".', 'wp-job-manager-emails' ), '[from_name]', '[job_title]' ) . "\n";
		$content .= "\n" . '[meta_data divider]' . "\n";
		$content .= "\n" . '[job_dashboard_url]' . __( 'You can view this and any other applications here: ', 'wp-job-manager-emails') . '[/job_dashboard_url]' . "\n";
		$content .= sprintf( __( 'You can contact them directly at: %s', 'wp-job-manager-emails' ), '[from_email]' ) . "\n";
		$content .= sprintf( __( 'You can view/edit this application in the backend by clicking here: %s', 'wp-job-manager-emails' ), '[view_app_url_admin]' ) . "\n" . "\n";

		return $content;
	}

	/**
	 * Default New Job Application Note Added
	 *
	 *
	 * @since @@since
	 *
	 * @return string
	 */
	function new_job_application_note_added_default_content() {

		$content = '';
		$content .= __( 'Hello', 'wp-job-manager-emails' ) . "\n" . "\n";
		$content .= sprintf( __( 'A new note has been added to %1$s application on the %2$s listing.', 'wp-job-manager-emails' ), '[from_name]', '[job_title]' ) . "\n";
		$content .= "\n" . '[application_note divider]' . "\n";
		$content .= "\n" . '[job_dashboard_url]' . __( 'You can view this and any other applications here: ', 'wp-job-manager-emails') . '[/job_dashboard_url]' . "\n";

		return $content;
	}

	/**
	 * Default New Job Application Content
	 *
	 *
	 * @since @@since
	 *
	 * @return string
	 */
	function applications_new_job_application_applicant_default_content() {

		$content = '';
		$content .= __( 'Hello [from_name],', 'wp-job-manager-emails' ) . "\n" . "\n";
		$content .= sprintf( __( 'Your application for (%1$s), has been submitted. The details are as follows:', 'wp-job-manager-emails' ), '[job_title]' ) . "\n" . "\n";
		$content .= "\n" . '[meta_data divider top]' . "\n";
		$content .= sprintf( __( 'You can view the listing here: %s', 'wp-job-manager-emails' ), '[view_job_url]' ) . "\n";

		return $content;
	}

	/**
	 * New Job Application Notices
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return string|void
	 */
	function get_new_job_application_email_notices() {

		if( ! function_exists( 'get_resume_share_link' ) ) return '';

		$msg = '';
		$force_apply_resumes = get_option( 'resume_manager_force_application' );
		$email_method        = get_option( 'resume_manager_enable_application' );
		$allow_methods     = get_option( 'job_manager_allowed_application_method', '' );

		$apps_email_method = get_option( 'job_application_form_for_email_method' );
		$apps_url_method   = get_option( 'job_application_form_for_url_method' );
		$url_enabled       = get_option( 'job_application_form_for_url_method' ) || get_option( 'resume_manager_enable_application_for_url_method' );

		if( $force_apply_resumes && $url_enabled && $apps_email_method && ( empty( $allow_methods ) || $allow_methods === 'email' ) ){
			$msg = __( 'Based on your current Resume Manager settings, with force apply with resumes enabled, this email will only be sent for URL based applications. Create an email template in Resume emails to handle email based applications, or disable Force Apply with Resumes.', 'wp-job-manager-emails' );
		}

		return $msg;
	}

	/**
	 * Send email for application post status update
	 *
	 *
	 * @since @@since
	 *
	 * @param $type
	 * @param $post \WP_Post
	 */
	function post_status_hook( $type, $post ){

		$ppost_type = $this->cpt()->get_ppost_type();

		/**
		 * Verify post type to prevent duplicate method runs and emails
		 *
		 * This method is called by the magic method for any post status hooks, so
		 * it could be called multiple times for each extending hook class.
		 */
		if( $post->post_type !== $ppost_type ){
			return;
		}

		$this->hook    = "{$type}_{$ppost_type}";
		$custom_emails = $this->cpt()->get_emails( $this->hook );

		if( empty( $custom_emails ) ) return;

		// Applications send email requires $templates, $job_id, $app_id, $resume_id
		$this->cpt()->send_email( $custom_emails, $post->post_parent, $post->ID, $post->_resume_id );

	}

	/**
	 * Get Default Status Slugs
	 *
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	public function get_default_statuses() {

		$default_statuses = apply_filters( 'job_manager_emails_post_status_application_default_post_statuses', array(
			'preview',
			'pending_payment',
			'pending',
			'hidden',
			'expired',
			'publish',
			'new',
			'interviewed',
			'rejected',
			'offer',
			'hired',
			'archived'
		));

		return $default_statuses;
	}

	/**
	 * Magic Method to handle post_status hooks
	 *
	 *
	 * @since @@since
	 *
	 * @param $method_name
	 * @param $args
	 *
	 */
	public function __call( $method_name, $args ){

		$post_statuses = $this->get_post_statuses( true );

		//$post_statuses = 'auto-draft|draft|preview|pending_payment|pending|hidden|expired|publish|new|interviewed|offer|hired|archived';
		$check_results = preg_match( "/do_(?P<from>({$post_statuses})+)_to_(?P<to>({$post_statuses})+)/", $method_name, $matches );
		if( $check_results && array_key_exists( 'from', $matches ) && array_key_exists( 'to', $matches ) ){
			$from = $matches['from'];
			$to   = $matches['to'];
			$this->post_status_hook( "{$from}_to_{$to}", $args[0] );
		}
	}
}