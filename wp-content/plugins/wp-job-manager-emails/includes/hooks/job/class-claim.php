<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Emails_Hooks_Job_Claim
 *
 * @since @@since
 *
 */
class WP_Job_Manager_Emails_Hooks_Job_Claim {

	/**
	 * @var \WP_Job_Manager_Emails_Hooks_Job
	 */
	private $hooks;
	/**
	 * @var \WP_Job_Manager_Emails_Job
	 */
	private $job_obj;
	/**
	 * @var
	 */
	public $job;
	/**
	 * @var WP_Post
	 */
	public $claim = null;
	/**
	 * @var array $data Array of user and claim data
	 */
	public $data = array();
	/**
	 * @var bool
	 */
	public $force = false;

	/**
	 * WP_Job_Manager_Emails_Hooks_Job_Claim constructor.
	 *
	 * This method is setup before the hooks object, so immediate access to hooks
	 * object is not going to be available.
	 *
	 * @param WP_Job_Manager_Emails_Job $job_obj
	 */
	public function __construct( $job_obj ) {

		// 3.0.0+ required for integration
		if ( $this->older_than_v3() ) {
			return;
		}

		$this->job_obj = $job_obj;

		add_filter( 'job_manager_emails_job_actions', array( $this, 'add_actions' ) );
		add_filter( 'job_manager_emails_job_default_email_keys', array( $this, 'default_emails' ) );
	}

	/**
	 * Add Default Email Template to Create on Install
	 *
	 *
	 * @since 2.0.5
	 *
	 * @param $emails
	 *
	 * @return array
	 */
	function default_emails( $emails ){

		$claim_emails = array( 'new_claim_created_user', 'new_claim_created_admin', 'claim_updated_user', 'claim_updated_admin' );
		$emails = array_merge( $emails, $claim_emails );

		return $emails;
	}

	/**
	 * Return User Submitted Claim Data
	 *
	 *
	 * @since 2.0.5
	 *
	 * @return mixed
	 */
	function claim_data(){
		return get_post_meta( $this->claim->ID, '_claim_data', true );
	}

	/**
	 * Return Standard Shortcodes
	 *
	 *
	 * @since 2.0.5
	 *
	 * @return array
	 */
	function get_shortcodes(){

		$singular = $this->cpt()->get_singular();

		$shortcodes = array(
			'claimer_id'    => array(
				'label'       => sprintf( __( '%s claim user ID', 'wp-job-manager-emails' ), $singular ),
				'description' => __( 'WordPress User ID of the user submitting the claim', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'claimer_id' )
			),
			'claimer_name'  => array(
				'label'       => sprintf( __( '%s claim user display name', 'wp-job-manager-emails' ), $singular ),
				'description' => __( 'Display name of the user submitting the claim', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'claimer_name' )
			),
			'claimer_login' => array(
				'label'       => sprintf( __( '%s claim user login', 'wp-job-manager-emails' ), $singular ),
				'description' => __( 'Login username of user submitting claim', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'claimer_login' )
			),
			'claimer_email' => array(
				'label'       => sprintf( __( '%s claim user email', 'wp-job-manager-emails' ), $singular ),
				'description' => __( 'Email address of user who created the claim', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'claimer_email' )
			),
			'claim_data'    => array(
				'label'       => __( 'Claim Data', 'wp-job-manager-emails' ),
				'description' => __( 'Additional information provided by user submitting the claim.', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'claim_data' )
			),
			'claim_id'      => array(
				'label'       => __( 'Claim ID', 'wp-job-manager-emails' ),
				'description' => __( 'Internal WordPress ID for the claim', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'claim_id' )
			),
			'claim_title'   => array(
				'label'       => __( 'Claim Title', 'wp-job-manager-emails' ),
				'description' => __( 'Claim Title', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'claim_title' )
			),
			'claim_date'    => array(
				'label'       => __( 'Date of the claim', 'wp-job-manager-emails' ),
				'description' => __( 'This will output the date of the claim', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'claim_date' )
			),
			'claim_status'  => array(
				'label'       => sprintf( __( '%s claim status', 'wp-job-manager-emails' ), $singular ),
				'description' => __( 'The status of the claim', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'claim_status' )
			),
			'claim_url'     => array(
				'label'       => __( 'Claim URL', 'wp-job-manager-emails' ),
				'description' => __( 'This will output the full URL to view the claim online', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'claim_url' )
			),
			'listing_title' => array(
				'label'       => __( 'Listing Title', 'wp-job-manager-emails' ),
				'description' => __( 'This will output the listing title for the claim (same as job_title)', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'listing_title' )
			),
			'listing_id'    => array(
				'label'       => __( 'Listing ID', 'wp-job-manager-emails' ),
				'description' => __( 'Internal WordPress ID of the Listing being claimed (same as job_id)', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'listing_id' )
			),
			'listing_url'   => array(
				'label'       => __( 'Listing URL', 'wp-job-manager-emails' ),
				'description' => __( 'Full URL to view listing on frontend of site (same as view_job_url)', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'listing_url' )
			),
		);

		// Remove claim_data shortcode if not enabled in settings
		if ( ! get_option( 'wpjmcl_submit_claim_data', FALSE ) ) {
			unset( $shortcodes['claim_data'] );
		}

		return $shortcodes;
	}

	/**
	 * Return Admin Shortcodes
	 *
	 *
	 * @since 2.0.5
	 *
	 * @return array
	 */
	function get_admin_shortcodes(){

		$admin_shortcodes = array(
			'claim_edit_url' => array(
				'label'       => __( 'Claim Edit URL', 'wp-job-manager-emails' ),
				'description' => __( 'This will output the full URL to edit the claim in admin area', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'claim_edit_url' )
			),
		);

		return $admin_shortcodes;
	}

	/**
	 * Return Shortcodes for Updated Emails
	 *
	 *
	 * @since 2.0.5
	 *
	 * @return array
	 */
	function get_updated_shortcodes(){

		$updated_claim_shortcodes = array(
			'claim_status_old' => array(
				'label'       => __( 'Old Claim Status', 'wp-job-manager-emails' ),
				'description' => __( 'This will output the old claim status', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'claim_status_old' )
			),
		);

		return $updated_claim_shortcodes;
	}

	/**
	 * Add Job Claim Hooks
	 *
	 *
	 * @since @@since
	 *
	 * @param $actions
	 *
	 * @return array
	 */
	function add_actions( $actions ){

		$singular = $this->cpt()->get_singular();
		$shortcodes = $this->get_shortcodes();
		$admin_shortcodes = $this->get_admin_shortcodes();
		$updated_claim_shortcodes = $this->get_updated_shortcodes();

		$claim_actions = array(
			'new_claim_created_user' => array(
				'args'       => 2,
				'label'      => sprintf( __( 'New %s Frontend Claim (user)', 'wp-job-manager-emails' ), $singular ),
				'callback'   => array( $this, 'new_claim_created_user' ),
				'priority'   => 1,
				'desc'       => sprintf( __( 'When a new %s claim is created via frontend (user email)', 'wp-job-manager-emails' ), $singular ),
				'ext_desc'   => __( 'If this email is enabled, it will override (and disable) the default core WP Job Manager Claim Listing email sent to the user, when a claim is submitted from the frontend.', 'wp-job-manager-emails' ),
				'hook'       => 'wpjmcl_create_new_claim',
				'defaults'   => array(
					'to'           => '[claimer_email]',
					'post_content' => $this->new_claim_user_content(),
					'subject'      => __( 'Your Claim Information', 'wp-job-manager-emails' ),
					'post_title'   => __( 'New Claim User Email', 'wp-job-manager-emails' ),
				),
				'shortcodes' => $shortcodes
			),
			'new_claim_created_admin' => array(
				'args'       => 2,
				'label'      => sprintf( __( 'New %s Frontend Claim (admin)', 'wp-job-manager-emails' ), $singular ),
				'callback'   => array( $this, 'new_claim_created_admin' ),
				'priority'   => 1,
				'desc'       => sprintf( __( 'When a new %s claim is created via frontend (admin email)', 'wp-job-manager-emails' ), $singular ),
				'ext_desc'   => __( 'If this email is enabled, it will override (and disable) the default core WP Job Manager Claim Listing email sent to the administrator, when the claim is submitted from the frontend.', 'wp-job-manager-emails' ),
				'hook'       => 'wpjmcl_create_new_claim',
				'defaults'   => array(
					'to'           => '[admin_email]',
					'post_content' => $this->new_claim_admin_content(),
					'subject'      => __( '[WP Job Man] New Claim Submitted', 'wp-job-manager-emails' ),
					'post_title'   => __( 'New Claim Admin Email', 'wp-job-manager-emails' ),
				),
				'shortcodes' => array_merge( $shortcodes, $admin_shortcodes )
			),
			'new_claim_created_admin_area' => array(
				'args'       => 2,
				'label'      => sprintf( __( 'New %s Admin Area Claim Created', 'wp-job-manager-emails' ), $singular ),
				'callback'   => array( $this, 'new_claim_created_admin_area' ),
				'priority'   => 1,
				'desc'       => sprintf( __( 'When a new %s claim is created via Admin area', 'wp-job-manager-emails' ), $singular ),
				'ext_desc'   => __( 'If this email is enabled, it will only be sent when a new claim is created via the administrator (wp-admin) area.', 'wp-job-manager-emails' ),
				'hook'       => 'wpjmcl_create_new_claim',
				'defaults'   => array(
					'to'           => '[admin_email]',
					'post_content' => $this->new_claim_admin_content(),
					'subject'      => __( '[WP Job Man] New Admin Area Claim Created', 'wp-job-manager-emails' ),
					'post_title'   => __( 'New Claim Admin Area Email', 'wp-job-manager-emails' ),
				),
				'shortcodes' => array_merge( $shortcodes, $admin_shortcodes )
			),
			'claim_updated_user' => array(
				'args'       => 3,
				'label'      => sprintf( __( '%s Claim Updated (user)', 'wp-job-manager-emails' ), $singular ),
				'callback'   => array( $this, 'claim_updated_user' ),
				'priority'   => 1,
				'desc'       => sprintf( __( 'When a %s claim is updated (user email)', 'wp-job-manager-emails' ), $singular ),
				'ext_desc'   => __( 'If this email is enabled, it will override (and disable) the default core WP Job Manager Claim Listing email sent to the user.', 'wp-job-manager-emails' ),
				'hook'       => 'wpjmcl_claim_status_updated',
				'defaults'   => array(
					'to'           => '[claimer_email]',
					'post_content' => $this->updated_claim_user_content(),
					'subject'      => sprintf( __( 'Your Claim For "%1$s" is %2$s', 'wp-job-manager-emails' ), '[listing_title]', '[claim_status]' ),
					'post_title'   => __( 'Claim Updated User Email', 'wp-job-manager-emails' ),
				),
				'shortcodes' => array_merge( $shortcodes, $updated_claim_shortcodes )
			),
			'claim_updated_admin' => array(
				'args'       => 3,
				'label'      => sprintf( __( '%s Claim Updated (admin)', 'wp-job-manager-emails' ), $singular ),
				'callback'   => array( $this, 'claim_updated_admin' ),
				'priority'   => 1,
				'desc'       => sprintf( __( 'When a %s claim is updated (admin email)', 'wp-job-manager-emails' ), $singular ),
				'ext_desc'   => __( 'If this email is enabled, it will override (and disable) the default core WP Job Manager Claim Listing email sent to the administrator when claim is updated via the ADMIN area.', 'wp-job-manager-emails' ),
				'hook'       => 'wpjmcl_claim_status_updated',
				'defaults'   => array(
					'to'           => '[admin_email]',
					'post_content' => $this->updated_claim_admin_content(),
					'subject' => sprintf( __( '[WP Job Man] Claim for "%1$s" is updated to %2$s', 'wp-job-manager-emails' ), '[listing_title]', '[claim_status]' ),
					'post_title'   => __( 'Claim Updated Admin Email', 'wp-job-manager-emails' ),
				),
				'shortcodes' => array_merge( $shortcodes, $admin_shortcodes, $updated_claim_shortcodes )
			),
		);

		if ( ! $this->cpt()->claim_listing_available() ) {
			foreach ( $claim_actions as $caction_key ) {
				$claim_actions[ $caction_key ]['warning'] = __( 'WP Job Manager Claim Listings was NOT detected as being activated on your site! This hook/action requires the plugin to be installed, and activated, otherwise this email will never be sent!', 'wp-job-manager-emails' );
			}
		}

		return array_merge( $actions, $claim_actions );
	}

	/**
	 * Claim Updated User Email
	 *
	 *
	 * @since 2.0.5
	 *
	 * @param $claim_id
	 * @param $old_status
	 * @param $request
	 *
	 * @return bool|void
	 */
	function claim_updated_user( $claim_id, $old_status, $request ){

		if( ! is_admin() ) return false;
		if( ! isset( $request['_send_notification'] ) ) return false;
		if( ! is_array( $request['_send_notification'] ) ) return false;
		if( ! in_array( 'claimer', $request['_send_notification'] ) ) return false;

		$custom_emails = $this->cpt()->get_emails( 'claim_updated_user' );

		if ( empty( $custom_emails ) || ! $this->claim = get_post( $claim_id ) ) {
			return;
		}

		// Build data array so we can add old status to it
		$this->get_data( $claim_id );

		// Format status if possible (which it should always be, but you never know)
		if ( class_exists( '\wpjmcl\claim\Functions' ) ) {
			$statuses = \wpjmcl\claim\Functions::claim_statuses();
			$old_status = array_key_exists( $old_status, $statuses ) ? $statuses[ $old_status ] : $old_status;
		}

		// Set claim_status_old in data array
		$this->data['claim_status_old'] = $old_status;

		// Remove default email action
		remove_class_filter( 'wpjmcl_claim_status_updated', 'wpjmcl\notification\Setup', 'mail_claimer_claim_status_updated', 10 );

		$this->hooks()->hook = 'claim_updated_user';

		$this->cpt()->send_email( $custom_emails, $this->claim->_listing_id );
	}

	/**
	 * Claim Updated Admin Email
	 *
	 *
	 * @since 2.0.5
	 *
	 * @param $claim_id
	 * @param $old_status
	 * @param $request
	 *
	 * @return bool|void
	 */
	function claim_updated_admin( $claim_id, $old_status, $request ){

		if( ! is_admin() ) return false;
		if( ! isset( $request['_send_notification'] ) ) return false;
		if( ! is_array( $request['_send_notification'] ) ) return false;
		if( ! in_array( 'admin', $request['_send_notification'] ) ) return false;

		$custom_emails = $this->cpt()->get_emails( 'claim_updated_admin' );

		if ( empty( $custom_emails ) || ! $this->claim = get_post( $claim_id ) ) {
			return;
		}

		// Build data array so we can add old status to it
		$this->get_data( $claim_id );

		// Format status if possible (which it should always be, but you never know)
		if ( class_exists( '\wpjmcl\claim\Functions' ) ) {
			$statuses   = \wpjmcl\claim\Functions::claim_statuses();
			$old_status = array_key_exists( $old_status, $statuses ) ? $statuses[ $old_status ] : $old_status;
		}

		// Set claim_status_old in data array
		$this->data['claim_status_old'] = $old_status;

		// Remove default email action
		remove_class_filter( 'wpjmcl_claim_status_updated', 'wpjmcl\notification\Setup', 'mail_admin_claim_status_updated', 10 );

		$this->hooks()->hook = 'claim_updated_admin';

		$this->cpt()->send_email( $custom_emails, $this->claim->_listing_id );
	}

	/**
	 * New Claim Submitted User Email
	 *
	 *
	 * @since 2.0.5
	 *
	 * @param $claim_id
	 * @param $context
	 */
	function new_claim_created_user( $claim_id, $context ){

		if( $context !== 'front' ){
			return;
		}

		$custom_emails = $this->cpt()->get_emails( 'new_claim_created_user' );

		if ( empty( $custom_emails ) || ! $this->claim = get_post( $claim_id ) ) {
			return;
		}

		// Remove default email action
		remove_class_filter( 'wpjmcl_create_new_claim', 'wpjmcl\notification\Setup', 'mail_claimer_new_claim', 10 );

		$this->hooks()->hook = 'new_claim_created_user';

		$this->cpt()->send_email( $custom_emails, $this->claim->_listing_id );
	}

	/**
	 * New Claim Submitted Admin Email
	 *
	 *
	 * @since 2.0.5
	 *
	 * @param $claim_id
	 * @param $context
	 */
	function new_claim_created_admin( $claim_id, $context ){

		if( $context !== 'front' ){
			return;
		}

		$custom_emails = $this->cpt()->get_emails( 'new_claim_created_admin' );

		if ( empty( $custom_emails ) || ! $this->claim = get_post( $claim_id ) ) {
			return;
		}

		// Remove default email action
		remove_class_filter( 'wpjmcl_create_new_claim', 'wpjmcl\notification\Setup', 'mail_admin_new_claim', 10 );

		$this->hooks()->hook = 'new_claim_created_admin';

		$this->cpt()->send_email( $custom_emails, $this->claim->_listing_id );
	}

	/**
	 * New Claim Created via Admin Area
	 *
	 *
	 * @since @@since
	 *
	 * @param $claim_id
	 * @param $context
	 */
	function new_claim_created_admin_area( $claim_id, $context ){

		if( $context !== 'admin' ){
			return;
		}

		$custom_emails = $this->cpt()->get_emails( 'new_claim_created_admin_area' );

		if ( empty( $custom_emails ) || ! $this->claim = get_post( $claim_id ) ) {
			return;
		}

		$this->hooks()->hook = 'new_claim_created_admin_area';

		$this->cpt()->send_email( $custom_emails, $this->claim->_listing_id );
	}

	/**
	 * Default Email Template Content for New Claim (User)
	 *
	 *
	 * @since @@since
	 *
	 * @return string
	 */
	function new_claim_user_content() {

		$content = '';
		$content .= sprintf( __( 'Hi %s,', 'wp-job-manager-emails' ), '[claimer_name]' ) . "\n" . "\n";
		$content .= sprintf( __( 'On %s you submitted a claim for a listing. Here\'s the details.', 'wp-job-manager-emails' ), '[claim_date]' ) . "\n" . "\n";
		$content .= '[divider]' . "\n" . "\n";
		$content .= sprintf( __( 'Listing URL: %s,', 'wp-job-manager-emails' ), '[listing_url]' ) . "\n" . "\n";
		$content .= sprintf( __( 'Claimed by: %s,', 'wp-job-manager-emails' ), '[claimer_name]' ) . "\n" . "\n";
		$content .= sprintf( __( 'Claim Status: %s,', 'wp-job-manager-emails' ), '[claim_status]' ) . "\n" . "\n";
		$content .= sprintf( __( 'You can also view your claim online: : %s,', 'wp-job-manager-emails' ), '[claim_url]' ) . "\n" . "\n";
		$content .= __( 'Thank you.', 'wp-job-manager-emails' ) . "\n" . "\n";

		return $content;
	}

	/**
	 * Default Email Template Content for Updated Claim (User)
	 *
	 *
	 * @since @@since
	 *
	 * @return string
	 */
	function updated_claim_user_content() {

		$content = '';
		$content .= sprintf( __( 'Hi %s,', 'wp-job-manager-emails' ), '[claimer_name]' ) . "\n" . "\n";
		$content .= sprintf( __( 'On %s you submitted a claim for a listing. Your claim status is updated. Here\'s the details.', 'wp-job-manager-emails' ), '[claim_date]' ) . "\n" . "\n";
		$content .= '[divider]' . "\n" . "\n";
		$content .= sprintf( __( 'Listing URL: %s,', 'wp-job-manager-emails' ), '[listing_url]' ) . "\n" . "\n";
		$content .= sprintf( __( 'Claimed by: %s,', 'wp-job-manager-emails' ), '[claimer_name]' ) . "\n" . "\n";
		$content .= sprintf( __( 'Previous Claim Status: %s,', 'wp-job-manager-emails' ), '[claim_status_old]' ) . "\n" . "\n";
		$content .= sprintf( __( 'New Claim Status: %s,', 'wp-job-manager-emails' ), '[claim_status]' ) . "\n" . "\n";
		$content .= __( 'Thank you.', 'wp-job-manager-emails' ) . "\n" . "\n";

		return $content;
	}

	/**
	 * Default Email Template Content for New Claim (Admin)
	 *
	 *
	 * @since @@since
	 *
	 * @return string
	 */
	function new_claim_admin_content() {

		$content = '';
		$content .= __( 'Hi Admin,', 'wp-job-manager-emails' ) . "\n" . "\n";
		$content .= __( 'New claim submitted, here\'s the details.', 'wp-job-manager-emails' ) . "\n" . "\n";
		$content .= '[divider]' . "\n" . "\n";
		$content .= sprintf( __( 'Listing URL: %s,', 'wp-job-manager-emails' ), '[listing_url]' ) . "\n" . "\n";
		$content .= sprintf( __( 'Claimed by: %s,', 'wp-job-manager-emails' ), '[claimer_name]' ) . "\n" . "\n";
		$content .= sprintf( __( 'Claim Status: %s,', 'wp-job-manager-emails' ), '[claim_status]' ) . "\n" . "\n";
		$content .= sprintf( __( 'Edit Claim: %s,', 'wp-job-manager-emails' ), '[claim_edit_url]' ) . "\n" . "\n";
		$content .= __( 'Thank you.', 'wp-job-manager-emails' ) . "\n" . "\n";

		return $content;
	}

	/**
	 * Default Email Template Content for Updated Claim (Admin)
	 *
	 *
	 * @since @@since
	 *
	 * @return string
	 */
	function updated_claim_admin_content() {

		$content = '';
		$content .= __( 'Hi Admin,', 'wp-job-manager-emails' ) . "\n" . "\n";
		$content .= sprintf( __( 'Claim status for listing %s is updated, here\'s the details.', 'wp-job-manager-emails' ), '[listing_title]' ) . "\n" . "\n";
		$content .= '[divider]' . "\n" . "\n";
		$content .= sprintf( __( 'Listing URL: %s,', 'wp-job-manager-emails' ), '[listing_url]' ) . "\n" . "\n";
		$content .= sprintf( __( 'Claimed by: %s,', 'wp-job-manager-emails' ), '[claimer_name]' ) . "\n" . "\n";
		$content .= sprintf( __( 'Previous Claim Status: %s,', 'wp-job-manager-emails' ), '[claim_status_old]' ) . "\n" . "\n";
		$content .= sprintf( __( 'New Claim Status: %s,', 'wp-job-manager-emails' ), '[claim_status]' ) . "\n" . "\n";
		$content .= sprintf( __( 'You can edit this claim: %s,', 'wp-job-manager-emails' ), '[claim_edit_url]' ) . "\n" . "\n";
		$content .= __( 'Thank you.', 'wp-job-manager-emails' ) . "\n" . "\n";

		return $content;
	}

	/**
	 * WP_Job_Manager_Emails_Job
	 *
	 *
	 * @since @@since
	 *
	 * @return \WP_Job_Manager_Emails_Job
	 */
	function cpt(){
		return $this->job_obj;
	}

	/**
	 * WP_Job_Manager_Emails_Hooks_Job
	 *
	 *
	 * @since 2.0.5
	 *
	 * @return \WP_Job_Manager_Emails_Hooks_Job
	 */
	function hooks(){
		return $this->job_obj->hooks;
	}

	/**
	 * Get claim_ prepended claim data
	 *
	 *
	 * @since 2.0.5
	 *
	 * @param $key
	 *
	 * @return mixed|string
	 */
	function claim( $key ){
		$value = '';
		$data = $this->get_data();

		if( array_key_exists( "claim_{$key}", $data ) ){
			$value = $data[ "claim_{$key}" ];
		}

		return $value;
	}

	/**
	 * Get claimer_ prepended claim data
	 *
	 *
	 * @since 2.0.5
	 *
	 * @param $key
	 *
	 * @return mixed|string
	 */
	function claimer( $key ){
		$value = '';
		$data = $this->get_data();

		if( array_key_exists( "claimer_{$key}", $data ) ){
			$value = $data[ "claimer_{$key}" ];
		}

		return $value;
	}

	/**
	 * Get listing_ prepended claim data
	 *
	 *
	 * @since 2.0.5
	 *
	 * @param $key
	 *
	 * @return mixed|string
	 */
	function listing( $key ){
		$value = '';
		$data = $this->get_data();

		if( array_key_exists( "listing_{$key}", $data ) ){
			$value = $data[ "listing_{$key}" ];
		}

		return $value;
	}

	/**
	 * Get Claim Data
	 *
	 *
	 * @since 2.0.5
	 *
	 * @return array|bool|mixed
	 */
	function get_data(){

		// If no data is set, or claim_id for data doesn't match our current claim ID, get new data values
		if( ! $this->data || $this->data['claim_id'] !== $this->claim->ID ){

			// Use Claim Listing internal method for obtaining data values
			if ( class_exists( '\wpjmcl\claim\Functions' ) ) {
				$this->data = \wpjmcl\claim\Functions::get_data( $this->claim->ID );
				// False is returned by claim listing, but we need it set to empty array instead of false
				if( $this->data === FALSE ){
					$this->data = array();
				}

			} else {

				$this->data = array();

			}

		}

		return $this->data;
	}

	/**
	 * Check if using old version of claim listing
	 *
	 *
	 * @since 2.0.5
	 *
	 * @return bool
	 */
	function older_than_v3() {

		//delete_option( 'jm_emails_old_claim_listing' );

		// Versions older than 3+ use the WP_Job_Manager_Claim_Listing() function to return class object
		if ( function_exists( 'WP_Job_Manager_Claim_Listing' ) ) {

			if ( ! get_option( 'jm_emails_old_claim_listing', FALSE ) ) {
				add_action( 'admin_notices', array( $this, 'update_required' ) );
				add_action( 'admin_init', array( $this, 'check_notices' ) );
			}

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Output Update Required Notice
	 *
	 *
	 * @since 2.0.5
	 *
	 */
	function update_required() {
		?>
		<div class="updated error">
			<p><?php _e( 'WP Job Manager Emails <strong>requires WP Job Manager Claim Listings 3.0 or newer</strong> for email support. An older version of the Claim Listings plugin was detected on your site.', 'wp-job-manager-emails' ); ?></p>
			<p><?php printf( __( '<em>Please upgrade to the latest version (at least 3.0) for email customization support. Once you update this notice will go away ... or you can <a href="%s">click here to hide this notice</a>.</em>', 'wp-job-manager-emails' ), add_query_arg( 'jm_emails_old_claim_listing', 0 ) ); ?></p>
		</div>
		<?php
	}

	/**
	 * Check Admin Notices
	 *
	 *
	 * @since 2.0.5
	 *
	 */
	function check_notices() {

		if ( isset( $_GET['jm_emails_old_claim_listing'] ) && '0' == $_GET['jm_emails_old_claim_listing'] ) {
			add_option( 'jm_emails_old_claim_listing', 'true' );
			// Now remove query arg, and redirect back to same page
			wp_safe_redirect( remove_query_arg( array( 'jm_emails_old_claim_listing', 0 ) ) );
			exit;
		}

	}

	/**
	 * Magic Method to catch claim_FIELD, claimer_FIELD, or listing_FIELD method callbacks
	 *
	 *
	 * @since @@since
	 *
	 * @param $method_name
	 * @param $args
	 *
	 * @return string
	 */
	public function __call( $method_name, $args ) {

		if ( preg_match( '/(?P<action>(claim|claimer|listing)+)_(?P<variable>\w+)/', $method_name, $matches ) ) {
			$variable = strtolower( $matches['variable'] );
			$action = $matches['action'];
			return $this->$action( $variable );
		}
	}

}