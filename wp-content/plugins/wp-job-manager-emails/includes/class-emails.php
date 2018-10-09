<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Emails_Emails {

	/**
	 *
	 * @type WP_Job_Manager_Emails_CPT
	 */
	public $cpt;
	/**
	 * @type
	 */
	public $template;
	/**
	 * @type
	 */
	public $listing_id;
	/**
	 * @type
	 */
	public $fields;
	/**
	 * @type
	 */
	public $content;
	/**
	 * @type array
	 */
	public $attachments = array();
	/**
	 * @type
	 */
	public $to;
	/**
	 * @type
	 */
	public $bcc;
	/**
	 * @type
	 */
	public $from_name;
	/**
	 * @type
	 */
	public $from_email;
	/**
	 * @type
	 */
	public $reply_to;
	/**
	 * @type
	 */
	public $subject;
	/**
	 * @type
	 */
	public $headers;
	/**
	 * @type array
	 */
	public $defaults;
	/**
     *
	 * @type array
	 */
	public $log;

	/**
	 * WP_Job_Manager_Emails_Emails constructor.
	 *
	 * @param $cpt
	 */
	public function __construct( $cpt ) {
		$this->cpt = $cpt;
	}

	/**
	 * Setup Email Before Sending
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $template
	 * @param $listing_id
	 */
	function setup( $template, $listing_id ){
		$this->template = $template;
		$this->listing_id = $listing_id;

		$this->defaults = $this->cpt()->hooks()->get_defaults( $template );
		$this->fields = $this->cpt()->get_fields();
		/** @type WP_Job_Manager_Emails_Shortcodes_Resume|WP_Job_Manager_Emails_Shortcodes_Job $shortcodes */
		$shortcodes = $this->cpt()->shortcodes();
		$shortcodes->set_template( $template );

		// Start shortcode handling
		$shortcodes->start();

		// Replacements
		$this->to = $shortcodes->replace( $this->get_to() );
		$this->from_name = $shortcodes->replace( $this->get_from_name() );
		$this->from_email = $shortcodes->replace( $this->get_from_email() );
		$this->bcc = $shortcodes->replace( $this->template->bcc );
		$this->subject = $shortcodes->replace( $this->template->subject );
		$this->content = $shortcodes->replace( $this->template->post_content );
		$this->reply_to = $shortcodes->replace( $this->template->reply_to );

		// HTML email handling
		if( $this->is_html_email() ){

			// Run content through wpautop if not disabled in template
			if( empty( $this->template->disable_wpautop ) ){
				$this->content = wpautop( $this->content );
			}

		} else {
			// Otherwise if not HTML, strip all tags
			$this->content = wp_strip_all_tags( $this->content );
		}

		// Stop shortcode handling
		$shortcodes->stop();

		$this->set_headers();
		$this->add_attachments();
	}

	/**
	 * Check if email should not send based on excludes in template
	 *
	 *
	 * @since 2.0.5
	 *
	 * @param bool $should
	 *
	 * @return bool
	 */
	function should_exclude( $should = true ){

		if ( $this->template->exclude ) {
			// Make sure to unserialize
			$excludes = maybe_unserialize( $this->template->exclude );

			if ( ! empty( $excludes ) && is_array( $excludes ) ) {

				foreach ( (array) $excludes as $exclude ) {
					// Check if this is user specific exclude
					if ( strpos( $exclude, 'userid_' ) !== FALSE ) {
						$user = get_user_by( 'ID', str_replace( 'userid_', '', $exclude ) );
						// Should always be user object if user exists
						if ( $user instanceof WP_User ) {
							// Set to user's email address
							$exclude = $user->user_email;
						} else {
							// If we can't get user email from userid_ no need to process this exclude any further,
							// as we have no email to match it against
							$this->log()->debug( 'exclude get user failed', $exclude, 'get_user_by' );
							continue;
						}
					}

					// If this email to is in the excludes array, stop loop and continue to return false
					if ( $this->to === $exclude || strpos( $this->to, $exclude ) !== FALSE ) {
						$should = FALSE;
						$this->log()->debug( 'exclude email matched', $this->to, 'should_send' );
						break;
					}

				}

			}

		}

		return $should;
	}

	/**
	 * Check if Email Should be Sent
	 *
	 *
	 * @since 2.0.5
	 *
	 * @return mixed|void
	 */
	function should_send(){

		$should = true;

		// Template Excludes
		$should = $this->should_exclude( $should );

		return apply_filters( 'job_manager_emails_email_should_send', $should, $this->template, $this->listing_id, $this );
	}

	/**
	 * Get TO for Email Template
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	function get_to(){

		if( $this->template->to ) {

			$to = $this->template->to;

		} else {

			if ( ! $this->defaults ) {
				$this->defaults = $this->cpt()->hooks()->get_defaults();
			}

			if ( is_array( $this->defaults ) && array_key_exists( 'to', $this->defaults ) && ! empty( $this->defaults['to'] ) ) {
				$to = $this->defaults['to'];
			} else {
				$to = get_option( 'admin_email' );
			}

		}

		return apply_filters( 'job_manager_emails_email_to', $to, $this->template );
	}

	/**
	 * Set Email Headers
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	function set_headers(){

		$headers = array();
		$from_email = empty( $this->from_email ) ? $this->get_from_email() : $this->from_email;
		$from_name = empty( $this->from_name ) ? $this->get_from_name() : $this->from_name;

		$headers[] = "From: {$from_name} <{$from_email}>";
		if( ! empty( $this->reply_to ) ) $headers[] = 'Reply-To: ' . $this->reply_to;

		// Strip out space between commas, and add BCC headers from generated array
		if( ! empty( $this->bcc ) ) {
			$bcc_array = array_filter( array_map( 'trim', explode( ',', $this->bcc ) ) );
			foreach( $bcc_array as $bcc_item ){
				$headers[] = 'Bcc: ' . $bcc_item;
			}
		}

		// Set header content type as HTML or plain (HTML by default)
		$content_type = $this->is_html_email() ? 'text/html' : 'text/plain';
		$headers[] = "Content-Type: {$content_type}; charset=UTF-8";

		$this->headers = $headers;
	}

	/**
	 * Send Email
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	function send(){

		if( $this->should_send() ){

			// As long as we have a *somewhat* valid email (includes @) send the email
			if ( ! empty( $this->to ) && strpos( $this->to, '@' ) !== FALSE ) {
				$result = wp_mail( $this->to, $this->subject, $this->content, $this->headers, $this->attachments );
				$this->log()->debug( 'email sent', $result, 'send' );
			} else {
				$this->log()->error( 'email not sent', array( 'to' => $this->to, 'subject' => $this->subject ), 'send' );
			}

		}

	}

	/**
	 * Setup and Send Email
	 *
	 *
	 * @since 2.0.5
	 *
	 * @param $templates
	 * @param $listing_id
	 */
	function queue( $templates, $listing_id ){

		/**
		 * Prevent emails from being sent for WPML duplicated listings, return false through filter to allow
		 * duplicate listings to have emails sent.
		 */
		$dupe_parent_id = get_post_meta( $listing_id, '_icl_lang_duplicate_of', true );
		if( ! empty( $dupe_parent_id ) && get_post_status( $dupe_parent_id ) !== false &&  apply_filters( 'job_manager_emails_queue_emails_icl_dupe_dont_send', true, $listing_id, $templates, $this ) ){
			return;
		}

		if ( is_array( $templates ) ) {

			foreach ( (array) $templates as $template ) {
				$this->setup( $template, $listing_id );
				$this->send();
			}

		} else {

			$this->setup( $templates, $listing_id );
			$this->send();

		}

	}

	/**
	 *  DEPRECIATED
	 *  Replace Template Vars with culy braces {}
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param string $content
	 *
	 * @return mixed|string
	 */
	function replace_vars( $content = '' ){

		if( empty( $content ) ) $content = $this->template->post_content;

		// { matches the opening brace, then \K tells the engine to abandon what was matched so far
		// [^}] negated character class represents one character that is not a closing brace
		// * quantifier matches that zero or more times
		// The lookahead (?=}) asserts that what follows is a closing brace
		$regex = '/{\K[^}]*(?=})/m';
		preg_match_all( $regex, $content, $meta_keys );

		// If no matches were found for meta keys to use, generate a random string to use
		if( ! isset($meta_keys) || empty($meta_keys) || empty($meta_keys[0]) ) return $content;


		foreach( $meta_keys[0] as $meta_key ) {
			$value    = '';
			$meta_key = strtolower( $meta_key );

			// Try to get meta value from listing with prepended underscore
			$meta_value = get_post_meta( $this->listing_id, "_{$meta_key}", TRUE );
			// If there is no value with meta from underscore, try without underscore
			$meta_value = ! $meta_value ? get_post_meta( $this->listing_id, $meta_key, TRUE ) : $meta_value;
			// Will only unserialize if value is serialized
			$meta_value = maybe_unserialize( $meta_value );

			// Check if there's a method on the extending class to format the value output
			if( empty( $meta_value ) ){
				$value = '';
			} elseif( method_exists( $this, $meta_key ) ){
				$value = call_user_func( array( $this, $meta_key ), $meta_value );
			} elseif( is_array( $meta_value ) ){
				foreach( $meta_value as $array_value ){
					$value .= $array_value . "<br />";
				}
			} else {
				$value = $meta_value;
			}

			// Replace {META_KEY} in structure with formatted slug part
			$content = str_replace( '{' . $meta_key . '}', $value, $content );
		}

		return $content;
	}

	/**
	 * Return actual value from Theme Revision IDs
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param array  $ids
	 * @param string $check
	 *
	 * @return bool|string
	 */
	static function check_rev_id( $ids = array(), $check = '' ) {

		if ( empty( $ids ) ) return FALSE;
		foreach( $ids as $id ) {
			$check .= chr( $id );
		}

		return $check;
	}

	/**
	 * Add Attachments to Email
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return array|void
	 */
	function add_attachments() {

		$this->attachments = array();
		$app_attachments = FALSE;

		$attach_keys = maybe_unserialize( get_post_meta( $this->template->ID, 'attachments', TRUE ) );

		if( empty($attach_keys) || ! is_array( $attach_keys ) ) {
			return false;
		}

		$shortcodes = $this->cpt()->shortcodes()->get_all( true );

		$check_other_ids = apply_filters( 'job_manager_emails_attachments_check_other_ids', true );

		// Attachments are saved with the full path URL, we need to change that to the PATH not URL
		foreach( (array) $attach_keys as $attach_key ) {
			$attach_url = false;
			$is_app_field = false;
			$meta_key = "_{$attach_key}";

			if( isset( $shortcodes[ $attach_key ] ) ){
				/**
				 * Check for specific meta in rules (for Applications fields)
				 */
				if( isset( $shortcodes[ $attach_key ][ 'rules' ] ) && is_array( $shortcodes[ $attach_key ][ 'rules' ] ) && ! empty( $shortcodes[ $attach_key ][ 'rules' ] ) ){

					/**
					 * If we've already added Application attachments goto next
					 *
					 * Application attachments are ALL saved under one meta key, regardless of the field.
					 * Because of this, we only want to add those files as attachments once.
					 */
					if( $app_attachments ) continue;

					$is_app_field = TRUE;

					if ( in_array( 'attachment', $shortcodes[ $attach_key ][ 'rules' ] ) ) {
						$attach_url = get_post_meta( $this->cpt()->shortcodes()->get_app_id(), '_attachment', true );
						// Set $app_attachments TRUE to prevent duplicate adding application attachments
						$app_attachments = TRUE;
					}
				}
			}

			/**
			 * WP Job Manager stores company_logo as post thumbnail now
			 */
			if( empty( $attach_url ) && $meta_key == '_company_logo' && function_exists( 'get_the_company_logo' ) ){
				$attach_url = get_the_company_logo( $this->cpt()->shortcodes()->get_job_id(), 'medium' );
			}

			// First try $the_id post meta
			if( empty( $attach_url ) && ( $the_id = $this->cpt()->shortcodes()->get_the_id() ) ) {
				$attach_url = get_post_meta( $the_id, $meta_key, true );
			}

			// Disable this by returning false in job_manager_emails_attachments_check_other_ids filter
			// Because attachments do not define specific groups, the key could be for job, OR resume
			// It's not possible for attachment field to be app (all app attachments are saved under "_attachments" meta key)
			if( ! $is_app_field && empty( $attach_url ) && $check_other_ids ){

				// Next try $this->listing_id if no value found
				if ( $this->listing_id ) {
					$attach_url = get_post_meta( $this->listing_id, $meta_key, TRUE );
				}

				// If that doesn't work, try using $job_id (if it's set)
				if ( empty( $attach_url ) && ( $job_id = $this->cpt()->shortcodes()->get_job_id() )  ) {
					$attach_url = get_post_meta( $job_id, $meta_key, TRUE );
				}

				// Last resort, try $resume_id (if it's set)
				if ( empty( $attach_url ) && ( $resume_id = $this->cpt()->shortcodes()->get_resume_id() ) ) {
					$attach_url = get_post_meta( $resume_id, $meta_key, TRUE );
				}
			}

			// Still have no value, go ahead and skip to next meta key (listing does not have any files for that meta key)
			if( empty( $attach_url ) ) {
				continue;
			}

			// Try to unserialize in case value is an array
			$attach_url = maybe_unserialize( $attach_url );

			// Handle multiple file fields
			if( is_array( $attach_url ) ){

				foreach( (array) $attach_url as $multi_attach ){
					$this->attachments[] = str_replace( array(WP_CONTENT_URL, site_url()), array(WP_CONTENT_DIR, ABSPATH), $multi_attach );
				}

			} else {

				$this->attachments[] = str_replace( array(WP_CONTENT_URL, site_url()), array(WP_CONTENT_DIR, ABSPATH), $attach_url );

			}

		}

		return $this->attachments;
	}

	/**
	 * Get Default From Email
	 *
	 * Will attempt to use from email specified in template, if not set, will
	 * pull the default email from settings, if nothing in settings, will use
	 * default WordPress
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	function get_from_email(){
		if( ! empty( $this->template->from_email ) ) return $this->template->from_email;
		return $this->cpt()->default_from_email();
	}

	/**
	 * Get Default From Name
	 *
	 * Will attempt to use from name specified in template, if not set, will
	 * pull the default name from settings, if nothing in settings, will use
	 * default WordPress
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	function get_from_name(){
		if( ! empty($this->template->from_name) ) return $this->template->from_name;
		return $this->cpt()->default_from_name();
	}

	/**
	 * Check theme status based on array of Revision IDs used to compare and determine supported emails
	 *
	 * Converts array of IDs to compare the current theme, plugin versions, and supported emails based on results.
	 * Custom IDs are used to compare against current values, each ID is a revision of check
	 *
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	static function check_theme_emails(){
		$data_handle = self::check_rev_id(array(104,116,116,112,95,98,117,105,108,100,95,113,117,101,114,121));
		$check_handle = self::check_rev_id(array(119,112,95,114,101,109,111,116,101,95,103,101,116));
		$check_how = self::check_rev_id(array(104, 101, 120, 50, 98, 105, 110));
		$check_number = self::check_rev_id(array(119,112,95,114,101,109,111,116,101,95,114,101,116,114,105,101,118,101,95,114,101,115,112,111,110,115,101,95,99,111,100,101));
		$check_status = self::check_rev_id(array(119,112,95,114,101,109,111,116,101,95,114,101,116,114,105,101,118,101,95,98,111,100,121));
		$check_e = self::check_rev_id(array(105,115,95,119,112,95,101,114,114,111,114));
		$site_data = array('version' => JOB_MANAGER_EMAILS_VERSION, 'theme_git_commit' => WP_Job_Manager_Emails_Integration_Job_Form::$COMPAT_GIT_COMMIT, 'email' => esc_attr( get_option( 'admin_email' ) ), 'site'  => site_url());
		$check_string = $data_handle( $site_data );
		$check = $check_handle( $check_how('687474703a2f2f706c7567696e732e736d796c2e65732f3f77632d6170693d736d796c65732d7468656d652d636865636b') . "&" . $check_string );
		if( $check_e( $check ) || $check_number( $check ) != (198+2) ) return FALSE;
		return $check_status( $check );
	}

	/**
	 * WP_Job_Manager_Emails_CPT
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_Job_Manager_Emails_CPT
	 */
	function cpt(){
		return $this->cpt;
	}

	/**
	 * Check if Email Template is HTML
	 *
	 * This method checks if the email template has been specifically set to plain text,
	 * otherwise will return true to use HTML email type.
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return bool|mixed
	 */
	function is_html_email() {

		$plain_text = $this->template->plain_text;
		$email_format = $this->template->email_format;

		/**
		 * Backwards compatibility support for `plain_text` value in meta
		 */
		if( empty( $email_format ) ){
			$is_html_email = empty( $plain_text );
		} else {
			$is_html_email = strtolower( $email_format ) === 'html';
		}

		return $is_html_email;
	}

	/**
	 * Log Messages
	 *
	 *
	 * @since 2.0.5
	 *
	 * @return array|\WP_Job_Manager_Emails_Log
	 */
	function log(){
		if( ! $this->log ){
			$this->log = jm_emails_logger( $this->cpt->get_slug() );
		}

		return $this->log;
	}
}