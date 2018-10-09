<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Emails_Hooks_Resume
 *
 * @since 2.0.0
 *
 */
class WP_Job_Manager_Emails_Hooks_Resume extends WP_Job_Manager_Emails_Hooks {

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
	 * Add Resume Specific Hooks (Filters/Actions)
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	function hooks(){
		// Filters for when using default Resumes notifications
		// ... not needed now that using new_resume method below (and removing action)
		//add_filter( 'apply_with_resume_email_headers', array( $this, 'apply_with_resume_headers' ), 99, 3 );
		//add_filter( 'resume_manager_new_resume_notification_headers', array( $this, 'new_resume_headers' ), 99, 2 );

		// Added by init_actions() now
		// add_action( 'resume_manager_resume_submitted', array( $this, 'new_resume' ), 1 );

		// All other core/default filters are added in the `apply_with_resume_handler` method
		add_action( 'wp', array( $this, 'apply_with_resume_handler' ), 1 );
		add_action( 'hidden_to_publish', array($this, 'do_hidden_to_publish') );
		// Handled in WP_Job_Manager_Emails_Hooks_PostStatus
		add_action( 'resume_manager_check_for_expired_resumes', array($this, 'check_soon_to_expire_listings'), 11 );

		add_action( 'publish_resume', array( $this, 'publish_resume' ), 10, 2 );
		add_action( 'submit_resume_form_save_resume_data', array( $this, 'check_updated_location' ), 10, 6 );

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
	public function get_core_statuses( $filtered = false ) {

		$statuses = array(
			'draft'           => __( 'Draft', 'wp-job-manager-emails' ),
			'expired'         => __( 'Expired', 'wp-job-manager-emails' ),
			'hidden'          => __( 'Hidden', 'wp-job-manager-emails' ),
			'preview'         => __( 'Preview', 'wp-job-manager-emails' ),
			'pending'         => __( 'Pending approval', 'wp-job-manager-emails' ),
			'pending_payment' => __( 'Pending payment', 'wp-job-manager-emails' ),
			'publish'         => __( 'Published', 'wp-job-manager-emails' ),
		);

		if ( $filtered ) {
			$statuses = apply_filters( 'resume_post_statuses', $statuses );
		}

		return $statuses;
	}

	/**
	 * publish_resume hook
	 *
	 *
	 * @since 2.0.5
	 *
	 * @param $id
	 * @param $post
	 */
	function publish_resume( $id, $post ) {
		$this->post_status_hook( 'publish', $post );
		$this->queued_featured_emails( $post );
	}

	/**
	 * Get Default Email Array Keys
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function get_default_email_keys(){
		return apply_filters( 'job_manager_emails_resume_default_email_keys', array( 'resume_soon_to_expire', 'applied_with_resume_email', 'preview_to_publish_resume', 'preview_to_pending_resume', 'pending_to_publish_resume' ) );
	}

	/**
	 * Return core Resumes Email Action Notices
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return string|void
	 */
	function get_applied_with_resume_email_notices(){

		$force_apply_resumes = get_option( 'resume_manager_force_application' );
		$email_method = get_option( 'resume_manager_enable_application' );
		$allowed_methods = get_option( 'job_manager_allowed_application_method', '' );

		/**
		 * Setup initial notices (without Applications addon), which will be overwritten if necessary
		 */
		if ( empty( $email_method ) ) {
			$msg = __( 'Based on your current Resume Manager Settings, Email Based Applications <strong>disabled</strong>, this email will never be sent.', 'wp-job-manager-emails' );
		} elseif ( empty( $force_apply_resumes ) ) {
			// By this execution, we already know $email_method is not empty
			$msg = __( 'Based on your current Resume Manager Settings, Email Based Applications <strong>enabled</strong>, and force apply through Resume Manager <strong>disabled</strong>, this email will ONLY be sent if the visitor uses the Resume Application form.', 'wp-job-manager-emails' );
		} elseif ( $allowed_methods === 'url' ) {
			// URL only supported in Application Addon
			$msg = __( 'Based on your current Job Manager Settings, URL only application method, this email will never be sent.', 'wp-job-manager-emails' );
			if ( class_exists( 'WP_Job_Manager_Applications' ) ) $msg .= ' ' . __( 'Even though the Resume Form is shown, the Applications addon handles URL based application email.  Create an email template in Applications emails.', 'wp-job-manager-emails' );
		} elseif( empty( $allowed_methods ) ){
			$msg = __( 'Based on your current Job Manager Settings, URL or Email application method, with force apply with resumes enabled, this email will only be sent for email based applications.', 'wp-job-manager-emails' );
			if ( class_exists( 'WP_Job_Manager_Applications' ) ) $msg .= ' ' . __( 'Even though the Resume Form is shown, the Applications addon handles URL based application email.  Create an email template in Applications emails to handle URL based applications.', 'wp-job-manager-emails' );
		} else {
			// By this point we know that $email_method is not empty, $force_apply_resumes is not empty, and $allowed_method is either email
			$msg = __( 'Based on your current Resume Manager Settings, this email will ONLY be sent for email based applications.', 'wp-job-manager-emails' );
		}

		return $msg;
	}

	/**
	 * Initialize Resume Actions
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	function init_actions() {

		$singular = $this->cpt()->get_singular();

		$this->actions = apply_filters( 'job_manager_emails_resume_actions',
		                                array(
			                                'resume_manager_resume_submitted' => array(
				                                'args'     => 1,
				                                'label'    => sprintf( __( 'New %s Created/Submitted', 'wp-job-manager-emails' ), $singular ),
				                                'callback' => 'new_resume',
				                                'priority' => 1,
				                                'desc'     => sprintf( __( 'When a New %s is Created/Submitted !! (DEPRECIATED) !!', 'wp-job-manager-emails' ), $singular ),
				                                'ext_desc' => __( 'This email will ONLY be sent when the listing does NOT require payment OR approval!!! DEPRECIATED !! This hook will be removed in the next release of this plugin!', 'wp-job-manager-emails' ),
				                                'warning'  => __( 'This hook will be removed in an upcoming release, you should use one of the new Post Status action/hook types for sending this email!', 'wp-job-manager-emails' ),
				                                'hook'     => TRUE,
				                                'defaults' => array(
					                                'to'           => '[new_resume_recipient]',
					                                'post_content' => $this->resume_manager_resume_submitted_default_content(),
					                                'attachments'  => array('resume_file'),
					                                'subject'      => sprintf( __( 'New Resume Submission From %s', 'wp-job-manager-emails' ), '[candidate_name]' ),
					                                'post_title'   => sprintf( __( 'New %s Created/Submitted', 'wp-job-manager-emails' ), $singular ),
				                                ),
				                                'template' => array(
					                                'label' => sprintf( __( 'New %s Created/Submitted (DEPRECIATED!)', 'wp-job-manager-emails' ), $singular ),
					                                'desc' => sprintf( __( 'You can use the email template but choose another hook after importing the template!', 'wp-job-manager-emails' ), $singular ),
				                                )
			                                ),
			                                'applied_with_resume_email' => array(
				                                'args'        => 1,
				                                'tag'         => 'wp',
				                                'label'       => sprintf( __( '%s Email Based Application', 'wp-job-manager-emails' ), $singular ),
				                                'callback'    => FALSE,
				                                'priority'    => 1,
				                                'desc'        => sprintf( __( 'When a Listing is applied to through Resume Manager with Email Method Type', 'wp-job-manager-emails' ), $singular ),
				                                'ext_desc'    => __( 'This is specifically for when emails are sent through Resume Manager Application Form (form with only two fields, Resume, and Message), using the email application method, which requires the <code>application</code> meta key to have a value of a valid email address.', 'wp-job-manager-emails' ),
				                                'warning'     => $this->get_applied_with_resume_email_notices(),
				                                'hook'        => FALSE,
				                                'defaults'    => array(
					                                'to'           => '[application]',
					                                'post_content' => $this->applied_with_resume_default_content(),
					                                'attachments'  => array('resume_file'),
					                                'subject'      => sprintf( __( 'New Resume Submission From %s', 'wp-job-manager-emails' ), '[candidate_name]' ),
					                                'post_title'   => sprintf( __( 'New %s Email Application', 'wp-job-manager-emails' ), $singular ),
				                                ),
				                                'job_fields' => true, // This adds job attachment fields to hook configuration
				                                'metaboxes' => array( 'job_shortcodes' )
			                                ),
			                                'resume_manager_update_resume_data'        => array(
				                                'args'     => 2,
				                                'label'    => sprintf( __( 'After %s Saved/Updated', 'wp-job-manager-emails' ), $singular ),
				                                'callback' => 'update_resume_data',
				                                'desc'     => sprintf( __( 'When a %s is Saved/Updated on Frontend', 'wp-job-manager-emails' ), $singular ),
				                                'hook'     => TRUE,
			                                ),
			                                'resume_manager_candidate_location_edited' => array(
				                                'args'     => 2,
				                                'label'    => sprintf( __( 'After %s Location Changed/Updated', 'wp-job-manager-emails' ), $singular ),
				                                'callback' => 'location_edited',
				                                'desc'     => sprintf( __( 'When a %s Location is Changed or Updated', 'wp-job-manager-emails' ), $singular ),
				                                'hook'     => TRUE,
			                                ),
			                                'resume_manager_resume_featured'   => array(
				                                'args'     => 4,
				                                'label'    => sprintf( __( '%s Featured', 'wp-job-manager-emails' ), $singular ),
				                                'callback' => 'listing_featured',
				                                'priority' => 11,
				                                'desc'     => sprintf( __( 'When a %s is changed from un-featured, to featured.', 'wp-job-manager-emails' ), $singular ),
				                                'ext_desc' => __( 'This email will be sent when a listing is set as a featured listing.', 'wp-job-manager-emails' ),
				                                'hook'     => 'update_postmeta',
				                                'defaults' => array(
					                                'to'           => '[resume_author_email]',
					                                'post_content' => $this->featured_default_content( '[candidate_name]', '[view_resume_url]' ),
					                                'subject'      => sprintf( __( 'The listing "[candidate_name]" is now a featured listing', 'wp-job-manager-emails' ), $singular ),
					                                'post_title'   => sprintf( __( '%s set as Featured', 'wp-job-manager-emails' ), $singular ),
				                                ),
				                                'inputs'   => array(
					                                'featured_send_on_create' => array(
						                                'label'    => __( 'Send email on newly created listings', 'wp-job-manager-emails' ),
						                                'type'     => 'checkbox',
						                                'checkbox' => 'slider',
						                                'help'     => __( 'When disabled, this email will only send when an active (published) listing is updated to featured listing.', 'wp-job-manager-emails' )
					                                ),
				                                )
			                                ),
			                                'resume_manager_resume_unfeatured' => array(
				                                'args'     => 4,
				                                'label'    => sprintf( __( '%s Un-Featured', 'wp-job-manager-emails' ), $singular ),
				                                'callback' => 'listing_unfeatured',
				                                'priority' => 11,
				                                'desc'     => sprintf( __( 'When a %s is changed from featured, to un-featured.', 'wp-job-manager-emails' ), $singular ),
				                                'ext_desc' => __( 'This email will be sent when a listing is CHANGED from Featured to Un-Featured.', 'wp-job-manager-emails' ),
				                                'hook'     => 'update_postmeta',
				                                'defaults' => array(
					                                'to'           => '[resume_author_email]',
					                                'post_content' => $this->unfeatured_default_content( '[candidate_name]' ),
					                                'subject'      => sprintf( __( 'The %s "[candidate_name]" is no longer a featured listing.', 'wp-job-manager-emails' ), $singular ),
					                                'post_title'   => sprintf( __( '%s set as Un-Featured', 'wp-job-manager-emails' ), $singular ),
				                                )
			                                ),
		                                )
		);

		$this->set_ps_action_sub( 'preview_to_publish_resume', 'defaults', 'attachments', array( 'resume_file' ) );
		$this->set_ps_action_sub( 'preview_to_pending_resume', 'defaults', 'attachments', array( 'resume_file' ) );

		return $this->actions;
	}

	/**
	 * Check when resume is updated, if the location has changed, and trigger email if so
	 *
	 * This handling is for the frontend, as the admin area has it's own hook to handle this.
	 *
	 * @since 2.4.0
	 *
	 * @param $data
	 * @param $post_title
	 * @param $post_content
	 * @param $status
	 * @param $values
	 * @param $that
	 *
	 * @return mixed
	 */
	function check_updated_location( $data, $post_title, $post_content, $status, $values, $that ){

		$resume_id = ! empty( $_REQUEST['resume_id'] ) ? absint( $_REQUEST['resume_id'] ) : 0;

		// Make sure not new listing by checking for resume_id and 'publish' status
		if( empty( $resume_id ) || $status !== 'publish' || ! isset( $values['resume_fields']['candidate_location'] ) ){
			return $data;
		}

		$current_location = get_post_meta( $resume_id, '_candidate_location', true );

		if ( $values['resume_fields']['candidate_location'] !== $current_location ) {
			$this->location_edited( $resume_id, $values['resume_fields']['candidate_location'] );
		}

		return $data;
	}

	/**
	 * Resume Location Edited
	 *
	 *
	 * @since 2.4.0
	 *
	 * @param $resume_id
	 * @param $value
	 */
	function location_edited( $resume_id, $value ){

		$custom_emails = $this->cpt()->get_emails( 'resume_manager_candidate_location_edited' );
		$this->hook    = 'resume_manager_candidate_location_edited';
		$this->cpt()->send_email( $custom_emails, $resume_id );

	}

	/**
	 * Resume Data Updated
	 *
	 *
	 * @since 2.4.0
	 *
	 * @param $resume_id
	 * @param $values
	 */
	function update_resume_data( $resume_id, $values ){

		$custom_emails = $this->cpt()->get_emails( 'resume_manager_update_resume_data' );
		$this->hook    = 'resume_manager_update_resume_data';
		$this->cpt()->send_email( $custom_emails, $resume_id );

	}

	/**
	 * New Resume Submitted
	 *
	 * This method gets called when a new resume is submitted.  It is called before the
	 * WP Job Manager Resumes plugin's action to send the notification email.  If a
	 * customized email exists, we remove the default action and send our own email.
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $resume_id
	 */
	function new_resume( $resume_id ) {

		$preview_to_pending = $this->cpt()->get_emails( 'preview_to_pending_resume' );
		$preview_to_publish = $this->cpt()->get_emails( 'preview_to_publish_resume' );

		$custom_emails = $this->cpt()->get_emails( 'resume_manager_resume_submitted' );
		$this->hook = 'resume_manager_resume_submitted';

		// Check if preview to publish, or preview to pending emails exist, and remove default filter to prevent
		// default email from being sent.
		if( ! empty( $preview_to_pending ) || ! empty( $preview_to_publish ) || ! empty( $custom_emails ) ){
			// Remove core action/filter to prevent default email from being sent
			$this->remove_class_filter( 'resume_manager_resume_submitted', 'WP_Resume_Manager_Email_Notification', 'new_resume_submitted' );
		}

		if( empty( $custom_emails ) ){
			return;
		}

		$this->cpt()->send_email( $custom_emails, $resume_id );
	}

	/**
	 * Add Filters for Email Based Applications
	 *
	 * This method checks for `wp_job_manager_resumes_apply_with_resume` in $_POST to determine
	 * if Resume Manager was used to submit an application.  It will then check if job application
	 * method and determine if the listing being applied for as a URL or Email set for `application`
	 * If the value is an email, this method will add filters to allow our custom email to be used,
	 * otherwise it will exit the method.
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return object|void
	 */
	function apply_with_resume_handler(){

		// This POST var must be set
		if( empty($_POST['wp_job_manager_resumes_apply_with_resume']) ) return false;

		$resume_id = absint( $_POST[ 'resume_id' ] );
		$job_id    = absint( $_POST['job_id'] );

		if( empty( $job_id ) ) return false;

		$method = get_the_job_application_method( $job_id );

		// raw email means email is sent through Resumes plugin
		if( ! empty( $method->raw_email ) ){

			// Return method class if we don't have any custom emails
			$custom_emails = $this->cpt()->get_emails( 'applied_with_resume_email' );
			if( empty( $custom_emails ) ) return false;

			// Loop through emails looking for default user email
			foreach( $custom_emails as $email_index => $custom_email ){
				$email_to = $custom_email->to;
				if( empty( $email_to ) || $email_to === '[application]' ){
					// Found user email, set $user_email equal to that post object
					$user_email = $custom_emails[ $email_index ];
					// Remove it from the array of emails
					unset( $custom_emails[ $email_index ] );
					// Reindex array (in case user email was not last on array)
					$custom_emails = array_values( $custom_emails );
					break;
				}
			}

			$this->email_template = $user_email;
			$this->hook = 'applied_with_resume_email';

			add_filter( 'apply_with_resume_email_message', array($this, 'apply_with_resume_email_message'), 9999, 5 );
			add_filter( 'apply_with_resume_email_recipient', array($this, 'apply_with_resume_email_recipient'), 9999, 3 );
			add_filter( 'apply_with_resume_email_subject', array($this, 'apply_with_resume_email_subject'), 9999, 3 );
			add_filter( 'apply_with_resume_email_headers', array($this, 'apply_with_resume_email_headers'), 9999, 3 );
			add_filter( 'apply_with_resume_email_attachments', array($this, 'apply_with_resume_email_attachments'), 9999, 3 );

			// Support sending multiple emails defined for this hook
			if( ! empty( $custom_emails ) ){
				$this->cpt()->send_email( $custom_emails, $resume_id, $job_id );
			}
		}

	}

	/**
	 * Apply with Resume Email Recipient
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $raw_email
	 * @param $job_id
	 * @param $resume_id
	 *
	 * @return string
	 */
	function apply_with_resume_email_recipient( $raw_email, $job_id, $resume_id ) {

		$to_email = $this->email_template( 'to' );
		if( empty($to_email) ) return $raw_email;

		// Doesn't appear to have any shortcode, probably an actual email address
		if( ! $this->has_shortcode( $to_email ) ) return $to_email;

		// Listing ID set as Job ID as TO should reference the _application value in Job ID
		$to_email = $this->shortcodes()->single( $to_email, $job_id, $resume_id );

		return $to_email;
	}

	/**
	 * Apply with Resume Email Subject
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $subject
	 * @param $job_id
	 * @param $resume_id
	 *
	 * @return string
	 */
	function apply_with_resume_email_subject( $subject, $job_id, $resume_id ) {

		$custom_subject = $this->email_template( 'subject' );
		if( empty($custom_subject) ) return $subject;

		// Doesn't appear to have any shortcode, return the custom subject
		if( ! $this->has_shortcode( $custom_subject ) ) return $custom_subject;

		$custom_subject = $this->shortcodes()->single( $custom_subject, $job_id, $resume_id );

		return $custom_subject;
	}

	/**
	 * Apply with Resume Email Headers
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $headers
	 * @param $job_id
	 * @param $resume_id
	 *
	 * @return array
	 */
	function apply_with_resume_email_headers( $headers, $job_id, $resume_id ) {

		$from_name = $this->email_template( 'from_name' );
		$from_email = $this->email_template( 'from_email' );

		// Shortcode replacements, if needed
		$from_name = $this->has_shortcode( $from_name ) ? $this->shortcodes()->single( $from_name, $job_id, $resume_id ) : $from_name;
		$from_email = $this->has_shortcode( $from_email ) ? $this->shortcodes()->single( $from_email, $job_id, $resume_id ) : $from_email;

		// Handle From Name/Email
		// TODO: maybe set from using option config
		if( ! empty( $from_name ) && ! empty( $from_email ) ){
			// Resume Manager sets From as first header in array
			$headers[0] = 'From: ' . $from_name . ' <' . $from_email . '>';
		}

		// Handle BCC
		$bcc = $this->email_template( 'bcc' );
		if( ! empty( $bcc ) ){
			// Replace any shortcodes in BCC (eg. `Administrator <[admin_email]>`) only if they exist
			$bcc = $this->has_shortcode( $bcc ) ? $this->shortcodes()->single( $bcc, $job_id, $resume_id ): $bcc;
			// Trim out spaces and create array from CSV
			$bcc_array = array_filter( array_map( 'trim', explode( ',', $bcc ) ) );

			foreach( $bcc_array as $bcc_item ) {
				$headers[] = 'Bcc: ' . $bcc_item;
			}
		}

		if( $this->is_html_email() ) {
			$headers[] = 'Content-Type: text/html; charset=UTF-8';
		} else {
			$headers[] = 'Content-Type: text/plain; charset=UTF-8';
		}

		return $headers;
	}

	/**
	 * Apply with Resume Email Attachments
	 *
	 *
	 * @since    1.0.0
	 *
	 * @param $existing_attachments
	 * @param $job_id
	 * @param $resume_id
	 *
	 * @return array
	 * @internal param $attachments
	 */
	function apply_with_resume_email_attachments( $existing_attachments, $job_id, $resume_id ) {

		$attachments = maybe_unserialize( get_post_meta( $this->email_template( 'ID' ), 'attachments', TRUE ) );

		// Return empty array if no attachments set in our custom email template
		// ... could mean the user doesn't want attachments sent with the email
		if( empty( $attachments ) || ! is_array( $attachments ) ) return array();

		$resume_fields = $this->resume()->get_fields();

		// Attachments are saved with the full path URL, we need to change that to the PATH not URL
		foreach( $attachments as $attachment_key ) {
			// If meta key is in resume fields array, use $resume_id as post_id, otherwise use $job_id
			$post_id = in_array( $attachment_key, array_keys( $resume_fields ) ) ? $resume_id : $job_id;
			// Try to get attachment from resume first
			$file_url = get_post_meta( $post_id, "_{$attachment_key}", TRUE );
			// If no file found, try to get it from post meta without the prepended underscore (very rare)
			if( empty( $file_url ) ) $file_url = get_post_meta( $post_id, $attachment_key, true );
			// Only add the attachment if we were able to get the URL to it
			if( ! empty( $file_url ) ) $attachments[] = str_replace( array(site_url( '/', 'http' ), site_url( '/', 'https' )), ABSPATH, $file_url );
		}

		return $attachments;
	}

	/**
	 * Apply with Resume Email Message
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param array $message
	 * @param       $current_user_id
	 * @param       $job_id
	 * @param       $resume_id
	 * @param       $application_message
	 *
	 * @return array
	 */
	function apply_with_resume_email_message( $message = array(), $current_user_id, $job_id, $resume_id, $application_message ) {

		$custom_msg = $this->email_template( 'post_content' );
		// Return default email content if our template one is empty
		if( empty($custom_msg) ) return $message;

		// Has a shortcode
		if( $this->has_shortcode( $custom_msg ) ) {
			$custom_msg = $this->shortcodes()->single( $custom_msg, $job_id, $resume_id );
		}

		if( $this->is_html_email() ) {
			$custom_msg = wpautop( $custom_msg );
		} else {
			$custom_msg = wp_strip_all_tags( $custom_msg );
		}

		// Resume Manager expects message to be returned in array format, as it uses
		// implode('', $message) when calling wp_mail
		$message = array();
		$message[] = $custom_msg;

		return $message;
	}

	/**
	 * Default Applied with Resume Email Content
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	function applied_with_resume_default_content(){

		$content = '';
		$content .= __( 'Hello', 'wp-job-manager-resumes', 'wp-job-manager-emails' );
		$content .= sprintf( "\n\n" . __( 'A candidate has applied online for the position "%s".', 'wp-job-manager-resumes', 'wp-job-manager-emails' ), '[job_title]' ) . "\n" . "\n";
		$content .= "\n" . "[application_message divider]" . "\n" . "\n";
		$content .= sprintf( __( 'You can view their online resume here: %s.', 'wp-job-manager-resumes', 'wp-job-manager-emails' ), '[view_resume_url]' );
		$content .= "\n" . sprintf( __( 'Or you can contact them directly at: %s.', 'wp-job-manager-resumes', 'wp-job-manager-emails' ), '[candidate_email]' );

		return $content;
	}

	/**
	 *
	 *
	 *
	 * @since 2.0.5
	 *
	 * @return string
	 */
	function preview_to_publish_default_content(){
		return $this->resume_manager_resume_submitted_default_content();
	}

	/**
	 * Default New Resume Submitted Content
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	function resume_manager_resume_submitted_default_content(){

		$content = '';
		$content .= __( 'Hello', 'wp-job-manager-resumes', 'wp-job-manager-emails' ) . "\n" . "\n";
		$content .= sprintf( __( 'A new resume has just been submitted by *%s*. The details of their resume are as follows:', 'wp-job-manager-resumes', 'wp-job-manager-emails' ), '[candidate_name]') . "\n" . "\n";
		$content .= "[divider]" . "\n" . "[resume_fields]" . "\n" . "[/divider]" . "\n" . "\n";
		$content .= "[resume_content]" . __( 'The content of their resume is as follows:', 'wp-job-manager-resumes', 'wp-job-manager-emails' ) . "\n" . "[/resume_content]";
		$content .= "\n" . "[links divider top]" . "\n";
		$content .= "\n" . "[candidate_education divider top]" . "\n";
		$content .= "\n" . "[candidate_experience divider top]" . "\n";
		$content .= sprintf( __( 'You can view this resume here: %s', 'wp-job-manager-resumes', 'wp-job-manager-emails' ), '[view_resume_url]' ) . "\n";
		$content .= sprintf( __( 'You can view/edit this resume in the backend by clicking here: %s', 'wp-job-manager-resumes', 'wp-job-manager-emails' ), '[view_resume_url_admin]' ) . "\n" . "\n";

		return $content;
	}

	/**
	 * Add Job ID and Resume ID to Email Headers
	 *
	 * This method is called by the Apply with Resume Notification header filter,
	 * which we used to add our own custom headers with the Resume and Job ID so
	 * we can use that value in our wp_mail filter.
	 *
	 * @since 1.0.0
	 *
	 * @param $existing_headers
	 * @param $job_id
	 * @param $resume_id
	 *
	 * @return array|string
	 */
	function apply_with_resume_headers( $existing_headers, $job_id, $resume_id ){

		$new_headers = array();
		$new_headers[] = "X-Job-Manager-Job-ID: {$job_id}";
		$new_headers[] = "X-Job-Manager-Resume-ID: {$resume_id}";

		return $this->add_headers( $existing_headers, $new_headers );
	}

	/**
	 * Add Resume ID to Email Headers
	 *
	 * This method is called by the New Resume Notification header filter,
	 * which we used to add our own custom headers with the Resume ID so
	 * we can use that value in our wp_mail filter.
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $existing_headers
	 * @param $resume_id
	 *
	 * @return array|string
	 */
	function new_resume_headers( $existing_headers, $resume_id ) {

		$new_headers   = array();
		$new_headers[] = "X-Job-Manager-Resume-ID: {$resume_id}";

		return $this->add_headers( $existing_headers, $new_headers );
	}

}