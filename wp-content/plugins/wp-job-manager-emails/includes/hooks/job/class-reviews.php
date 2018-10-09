<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Emails_Hooks_Job_Reviews
 *
 * @since @@since
 *
 */
class WP_Job_Manager_Emails_Hooks_Job_Reviews {

	/**
	 * @var \WP_Job_Manager_Emails_Hooks_Job
	 */
	private $hooks;
	/**
	 * @var \WP_Job_Manager_Emails_Job
	 */
	private $job_obj;
	/**
	 * @var WP_Post
	 */
	public $job;
	/**
	 * @var WP_Comment
	 */
	public $comment = null;
	/**
	 * @var bool
	 */
	public $force = false;

	/**
	 * WP_Job_Manager_Emails_Hooks_Job_Reviews constructor.
	 *
	 * This method is setup before the hooks object, so immediate access to hooks
	 * object is not going to be available.
	 *
	 * @param WP_Job_Manager_Emails_Job $job_obj
	 */
	public function __construct( $job_obj ) {

		$this->job_obj = $job_obj;

		add_filter( 'job_manager_emails_job_actions', array( $this, 'add_actions' ) );
//		add_filter( 'job_manager_emails_job_default_email_keys', array( $this, 'default_emails' ) );

		add_action( 'wpjmr_process_dashboard_comment_action', array( $this, 'review_dashboard_action' ), 99999, 3 );
	}

	/**
	 * Review Dashboard Action Called
	 *
	 * This method is called whenever an action is triggered from the review dashboard.  This is done before any processing of
	 * the action.  We use this to set the comment and post (job listing) in our class object, to use with shortcodes.
	 *
	 *
	 * @since 2.4.0
	 *
	 * @param $action
	 * @param $comment
	 * @param $post
	 */
	function review_dashboard_action( $action, $comment, $post ){

		$this->comment = $comment;
		$this->job = $post;

		if ( $action === 'report' ) {
			// Add filters to review send mail args, to send our custom emails if they exist
			add_filter( 'wpjmr_send_mail_args', array( $this, 'report_job_review_admin' ), 99999 );

			/**
			 * We still add this filter for instances where a custom email is setup for the owner/user,
			 * but there is no custom email setup for the administrator.
			 */
			add_filter( 'wpjmr_send_mail_args', array( $this, 'report_job_review_owner' ), 99999 );
		}
	}

	/**
	 * Add Default Email Template to Create on Install
	 *
	 *
	 * @since 2.4.0
	 *
	 * @param $emails
	 *
	 * @return array
	 */
	function default_emails( $emails ){
//
//		$review_emails = array( 'new_review_created_user', 'new_review_created_admin', 'review_updated_user', 'review_updated_admin' );
//		$emails = array_merge( $emails, $review_emails );

		return $emails;
	}

	/**
	 * Return Standard Shortcodes
	 *
	 *
	 * @since 2.4.0
	 *
	 * @return array
	 */
	function get_shortcodes(){

		$singular = $this->cpt()->get_singular();

		$shortcodes = array(
			'reviewer_name'  => array(
				'label'       => __( 'Reviewer Name', 'wp-job-manager-emails' ),
				'description' => __( 'Name of person who submitted the review', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'reviewer_name' )
			),
			'reviewer_email' => array(
				'label'       => __( 'Reviewer Email', 'wp-job-manager-emails' ),
				'description' => __( 'Email address of user who submitted the review', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'reviewer_email' )
			),
			'reviewer_url' => array(
				'label'       => __( 'Reviewer URL', 'wp-job-manager-emails' ),
				'description' => __( 'URL entered when submitting review', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'reviewer_url' )
			),
			'reviewer_ip' => array(
				'label'       => __( 'Reviewer IP', 'wp-job-manager-emails' ),
				'description' => __( 'IP address of person who submitted review', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'reviewer_ip' )
			),
			'reviewer_review' => array(
				'label'       => __( 'Reviewer Review', 'wp-job-manager-emails' ),
				'description' => __( 'Content of the review that was submitted', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'reviewer_review' )
			),
			'reviewer_rating' => array(
				'label'       => __( 'Reviewer Rating', 'wp-job-manager-emails' ),
				'description' => __( 'This will output a SINGLE category rating item (must be passed via category arg)', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'reviewer_rating' ),
				'args' => array(
					'category' => array(
						'desc'     => __( 'Category to output rating for', 'wp-job-manager-emails' ),
						'required' => true,
						'example'  => 'Speed'
					),
				)
			),
			'reviewer_ratings' => array(
				'label'       => __( 'Reviewer Ratings', 'wp-job-manager-emails' ),
				'description' => __( 'This will output ratings for ALL categories', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'reviewer_ratings' )
			),
			'reviewer_ratings_avg' => array(
				'label'       => __( 'Reviewer Ratings Average', 'wp-job-manager-emails' ),
				'description' => __( 'This will output the average rating for ALL categories', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'reviewer_ratings_avg' )
			),
			'reviewer_images' => array(
				'label'       => __( 'Reviewer Images', 'wp-job-manager-emails' ),
				'description' => __( 'This will output URL', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'reviewer_ratings' )
			),
			'review_id'      => array(
				'label'       => __( 'Review ID', 'wp-job-manager-emails' ),
				'description' => __( 'Internal WordPress ID for the review/comment', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'review_id' )
			),
			'review_date'    => array(
				'label'       => __( 'Date Review was Submitted', 'wp-job-manager-emails' ),
				'description' => __( 'This will output the date the review was submitted', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'review_date' )
			),
			'review_status'  => array(
				'label'       => sprintf( __( '%s Review status', 'wp-job-manager-emails' ), $singular ),
				'description' => __( 'The status of the review', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'review_status' )
			),
			'review_url'     => array(
				'label'       => __( 'Review URL', 'wp-job-manager-emails' ),
				'description' => __( 'This will output the full URL to view the review online', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'review_url' )
			),
		);

		return $shortcodes;
	}

	/**
	 * Return Admin Shortcodes
	 *
	 *
	 * @since 2.4.0
	 *
	 * @return array
	 */
	function get_admin_shortcodes(){

		$admin_shortcodes = array(
			'review_edit_url' => array(
				'label'       => __( 'Review Edit URL', 'wp-job-manager-emails' ),
				'description' => __( 'This will output the full URL to edit the review in admin area', 'wp-job-manager-emails' ),
				'nonmeta'     => TRUE,
				'visible'     => FALSE,
				'callback'    => array( $this, 'review_edit_url' )
			),
		);

		return $admin_shortcodes;
	}

	/**
	 *
	 *
	 *
	 * @since 2.4.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return string
	 */
	function reviewer_name( $args = array(), $content = '' ){
		return $this->comment && $this->comment->comment_author ? $this->comment->comment_author : '';
	}

	/**
	 *
	 *
	 *
	 * @since 2.4.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return string
	 */
	function reviewer_email( $args = array(), $content = '' ){
		return $this->comment && $this->comment->comment_author_email ? $this->comment->comment_author_email : '';
	}

	/**
	 *
	 *
	 *
	 * @since 2.4.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return string
	 */
	function reviewer_url( $args = array(), $content = '' ){
		return $this->comment && $this->comment->comment_author_url ? $this->comment->comment_author_url : '';
	}

	/**
	 *
	 *
	 *
	 * @since 2.4.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return string
	 */
	function reviewer_ip( $args = array(), $content = '' ){
		return $this->comment && $this->comment->comment_author_IP ? $this->comment->comment_author_IP : '';
	}

	/**
	 *
	 *
	 *
	 * @since 2.4.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return string
	 */
	function reviewer_review( $args = array(), $content = '' ){
		return $this->comment && $this->comment->comment_content ? $this->comment->comment_content : '';
	}

	/**
	 *
	 *
	 *
	 * @since 2.4.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return mixed|string
	 */
	function reviewer_rating( $args = array(), $content = '' ){

		if ( ! $this->comment || empty( $args ) || ! array_key_exists( 'category', $args ) ) {
			return '';
		}

		$stars = get_comment_meta( $this->comment->comment_ID, 'review_stars', true );

		if( ! $stars ){
			return '';
		}

		$single_rating = '';

		foreach( (array) $stars as $category => $rating ){

			if( strtolower( trim( $category ) ) === strtolower( trim( $args['category'] ) ) ){
				$single_rating = $rating;
				break;
			}

		}

		return $single_rating;
	}

	/**
	 *
	 *
	 *
	 * @since 2.4.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return string
	 */
	function reviewer_ratings( $args = array(), $content = '' ){

		if ( ! $this->comment ) {
			return '';
		}

		$stars = get_comment_meta( $this->comment->comment_ID, 'review_stars', true );

		if( ! $stars ){
			return '';
		}

		$ratings = '';

		foreach( (array) $stars as $category => $rating ){
			$ratings .= trim( $category ) . ': ' . $rating . "\n";
		}

		return $ratings;
	}

	/**
	 *
	 *
	 *
	 * @since 2.4.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return string
	 */
	function reviewer_ratings_avg( $args = array(), $content = '' ){

		if( ! $this->comment ){
			return '';
		}

		$average = get_comment_meta( $this->comment->comment_ID, 'review_average', true );

		return $average ?: '';
	}

	/**
	 *
	 *
	 *
	 * @since 2.4.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return string
	 */
	function reviewer_images( $args = array(), $content = '' ){

		return '';
	}

	/**
	 *
	 *
	 *
	 * @since 2.4.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return int|string
	 */
	function review_id( $args = array(), $content = '' ){
		return $this->comment && $this->comment->comment_ID ? $this->comment->comment_ID : '';
	}

	/**
	 *
	 *
	 *
	 * @since 2.4.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return string
	 */
	function review_date( $args = array(), $content = '' ){
		return $this->comment && $this->comment->comment_date ? $this->comment->comment_date : '';
	}

	/**
	 *
	 *
	 *
	 * @since 2.4.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return false|string
	 */
	function review_status( $args = array(), $content = '' ){
		return $this->comment ? wp_get_comment_status( $this->comment ) : '';
	}

	/**
	 *
	 *
	 *
	 * @since 2.4.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return string
	 */
	function review_url( $args = array(), $content = '' ){
		return $this->comment ? get_comment_link( $this->comment ) : '';
	}

	/**
	 *
	 *
	 *
	 * @since 2.4.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return string|void
	 */
	function review_edit_url( $args = array(), $content = '' ){
		// We have to manually generate the URL since the email is triggered by frontend user, and must do this to get around cap check
		return $this->comment ? admin_url( 'comment.php?action=editcomment&amp;c=' ) . $this->comment->comment_ID : '';
	}

	/**
	 * Add Job Review Hooks
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

		$review_actions = array(
			'new_job_review_submitted' => array(
				'args'       => 3,
				'label'      => sprintf( __( 'New %s Review Submitted', 'wp-job-manager-emails' ), $singular ),
				'callback'   => array( $this, 'new_job_review_submitted' ),
				'priority'   => 11, // has to be 11 to be called after Reviews addon adds review metadata
				'desc'       => sprintf( __( 'When a %s Review is submitted/created', 'wp-job-manager-emails' ), $singular ),
				'ext_desc' => __( 'If this email is enabled, it will be sent anytime a review is submitted, regardless of whether it is auto-approved or not.', 'wp-job-manager-emails' ),
				'hook'       => 'comment_post',
				'defaults'   => array(
					'to'           => '[job_author_email]',
					'post_content' => $this->new_job_review_submitted_default_content(),
					'subject'      => sprintf( __( 'New Review for %s Submitted', 'wp-job-manager-emails' ), '[job_title]' ),
					'post_title'   => __( 'New Review Submitted', 'wp-job-manager-emails' ),
				),
				'shortcodes' => $shortcodes
			),
			'new_job_review_pending_approval' => array(
				'args'       => 3,
				'label'      => sprintf( __( 'New %s Review Pending Approval (reviewer)', 'wp-job-manager-emails' ), $singular ),
				'callback'   => array( $this, 'new_job_review_pending_approval' ),
				'priority'   => 11, // has to be 11 to be called after Reviews addon adds review metadata
				'desc'       => sprintf( __( 'When a %s Review is created and pending approval', 'wp-job-manager-emails' ), $singular ),
				'ext_desc' => __( 'This email will be sent when a new review is created, and pending approval (admin or listing owner).', 'wp-job-manager-emails' ),
				'hook'       => 'comment_post',
				'defaults'   => array(
					'to'           => '[reviewer_email]',
					'post_content' => $this->new_job_review_pending_approval_reviewer_default_content(),
					'subject'      => sprintf( __( 'Your Review for %s is Pending Approval', 'wp-job-manager-emails' ), '[job_title]' ),
					'post_title'   => __( 'New Review Pending Approval (reviewer)', 'wp-job-manager-emails' ),
				),
				'shortcodes' => array_merge( $shortcodes, $admin_shortcodes ),
				'templates' => array(
					array(
						'label'        => sprintf( __( 'New %s Review Pending Approval (admin)', 'wp-job-manager-emails' ), $singular ),
						'to'           => '[admin_email]',
						'post_content' => $this->new_job_review_pending_approval_admin_default_content(),
						'subject'      => sprintf( __( 'New Review for %s Pending Admin Approval', 'wp-job-manager-emails' ), '[job_title]' ),
						'post_title'   => __( 'New Review Pending Admin Approval', 'wp-job-manager-emails' ),
					),
					array(
						'label'        => sprintf( __( 'New %s Review Pending Approval (owner)', 'wp-job-manager-emails' ), $singular ),
						'to'           => '[job_author_email]',
						'post_content' => $this->new_job_review_pending_approval_owner_default_content(),
						'subject'      => sprintf( __( 'New Review for %s Pending Owner Approval', 'wp-job-manager-emails' ), '[job_title]' ),
						'post_title'   => __( 'New Review Pending Owner Approval', 'wp-job-manager-emails' ),
					)
				)
			),
			'job_review_approved' => array(
				'args'       => 3,
				'label'      => sprintf( __( '%s Review Approved', 'wp-job-manager-emails' ), $singular ),
				'callback'   => array( $this, 'job_review_approved' ),
				'priority'   => 11, // has to be 11 to be called after Reviews addon handles review metadata
				'desc'       => sprintf( __( 'When a %s Review is Approved', 'wp-job-manager-emails' ), $singular ),
				'hook'       => 'transition_comment_status',
				'defaults'   => array(
					'to'           => '[reviewer_email]',
					'post_content' => $this->job_review_approved_default_content(),
					'subject'      => sprintf( __( 'Your Review for %s has been Approved!', 'wp-job-manager-emails' ), '[job_title]' ),
					'post_title'   => __( 'Reviewer Review Approved ', 'wp-job-manager-emails' ),
				),
				'shortcodes' => $shortcodes
			),
			'report_job_review_admin' => array(
				'label'      => sprintf( __( '%s Review Reported (admin email)', 'wp-job-manager-emails' ), $singular ),
				'desc'       => sprintf( __( 'When a %s Review is reported to admin (admin email)', 'wp-job-manager-emails' ), $singular ),
				'ext_desc'   => __( 'If this email is enabled, it will override (and disable) the default core WP Job Manager Reviews email sent to the administrator, when a review is reported.', 'wp-job-manager-emails' ) . '<br/><strong>' . __( 'If you enable this custom email (for admin), you MUST create and enable a custom email for the user/owner as well, otherwise the listing owner (person who reported review) will NEVER receive an email!  This is due to the way the Reviews addon plugin works internally.', 'wp-job-manager-emails' ) . '</strong>',
				'hook'       => false,
				'defaults'   => array(
					'to'           => '[admin_email]',
					'post_content' => $this->report_job_review_admin_default_content(),
					'subject'      => __( 'Review Reported Notification', 'wp-job-manager-emails' ),
					'post_title'   => __( 'Review Reported Notification (admin email)', 'wp-job-manager-emails' ),
				),
				'shortcodes' => array_merge( $shortcodes, $admin_shortcodes )
			),
			'report_job_review_owner' => array(
				'label'      => sprintf( __( '%s Review Reported (listing owner email)', 'wp-job-manager-emails' ), $singular ),
				'desc'       => sprintf( __( 'When a %s Review is reported to admin (listing owner email)', 'wp-job-manager-emails' ), $singular ),
				'ext_desc'   => __( 'If this email is enabled, it will override (and disable) the default core WP Job Manager Reviews email sent to the Listing Owner, when they report a review.', 'wp-job-manager-emails' ),
				'hook'       => false,
				'defaults'   => array(
					'to'           => '[job_author_email]',
					'post_content' => $this->report_job_review_owner_default_content(),
					'subject'      => __( 'Review Reported Notification', 'wp-job-manager-emails' ),
					'post_title'   => __( 'Review Reported Notification (listing owner email)', 'wp-job-manager-emails' ),
				),
				'shortcodes' => $shortcodes
			),
		);

		$can_moderate = get_option( 'wpjmr_listing_authors_can_moderate', '0' );

		if( ! $can_moderate ){

			$review_actions[ 'new_job_review_pending_approval' ]['warning'] = __( 'This hook should NOT be used for sending a notice to the Listing Owner, as you currently have the "Listing owners can moderate reviews" setting DISABLED!', 'wp-job-manager-emails' );

			$moderation_hooks = array( 'report_job_review_admin', 'report_job_review_owner' );

			foreach ( $moderation_hooks as $moderation_key ) {
				$review_actions[ $moderation_key ]['warning'] = __( '"Listing owners can moderate reviews" MUST be enabled for this email to send!  You currently have this setting DISABLED! This email will NEVER send!', 'wp-job-manager-emails' );
			}
		}

		if ( ! $this->cpt()->reviews_available() ) {
			foreach ( $review_actions as $caction_key ) {
				$review_actions[ $caction_key ]['warning'] = __( 'WP Job Manager Review Listings was NOT detected as being activated on your site! This hook/action requires the plugin to be installed, and activated, otherwise this email will never be sent!', 'wp-job-manager-emails' );
			}
		}

		return array_merge( $actions, $review_actions );
	}

	/**
	 * Review approved handler
	 *
	 * @since 2.4.0
	 *
	 * @param string $new_status New comment status.
	 * @param string $old_status Old/edited comment status.
	 * @param WP_Comment $comment    Comment object.
	 *
	 * @return void.
	 */
	public function job_review_approved( $new_status, $old_status, $comment ) {

		$post = get_post( $comment->comment_post_ID );

		if ( 'job_listing' !== $post->post_type ) {
			return;
		}
		// Bail if not top level comment.
		if ( 0 !== (int) $comment->comment_parent ) {
			return;
		}

		// Approved args.
		if ( 'approved' === $new_status ) {

			$custom_emails = $this->cpt()->get_emails( 'job_review_approved' );

			if ( empty( $custom_emails ) ) {
				return; // Allow sending default email if we don't have any custom ones setup
			}

			// Set class object vals for shortcode handling
			$this->job     = $post;
			$this->comment = $comment;

			$this->hooks()->hook = 'job_review_approved';
			$this->cpt()->send_email( $custom_emails, $this->job->ID );

		}

	}

	/**
	 * Handle new review submitted/created
	 *
	 * @since 2.4.0
	 * @link  https://developer.wordpress.org/reference/hooks/comment_post/
	 *
	 * @param int        $comment_id       ID of the current comment.
	 * @param int|string $comment_approved Value is 1 if comment approved.
	 * @param array      $commentdata      Comment data.
	 *
	 * @return void
	 */
	function new_job_review_submitted( $comment_id, $comment_approved, $commentdata ){

		$post = get_post( $commentdata['comment_post_ID'] ); // Get post data.

		// Check post type.
		if ( 'job_listing' !== $post->post_type ) {
			return;
		}

		// Bail if not top level comment.
		if ( 0 !== (int) $commentdata['comment_parent'] ) {
			return;
		}

		$custom_emails = $this->cpt()->get_emails( 'new_job_review_submitted' );

		if ( empty( $custom_emails ) ) {
			return; // Allow sending default email if we don't have any custom ones setup
		}

		// Set class object vals for shortcode handling
		$this->job = $post;
		$this->comment = get_comment( $comment_id );

		$this->hooks()->hook = 'new_job_review_submitted';
		$this->cpt()->send_email( $custom_emails, $this->job->ID );
	}

	/**
	 * Handle new review submitted/created pending approval
	 *
	 * @since 2.4.0
	 * @link  https://developer.wordpress.org/reference/hooks/comment_post/
	 *
	 * @param int        $comment_id       ID of the current comment.
	 * @param int|string $comment_approved Value is 1 if comment approved.
	 * @param array      $commentdata      Comment data.
	 *
	 * @return void
	 */
	function new_job_review_pending_approval( $comment_id, $comment_approved, $commentdata ){

		$post = get_post( $commentdata['comment_post_ID'] ); // Get post data.

		// Check post type.
		if ( 'job_listing' !== $post->post_type ) {
			return;
		}

		// Bail if not top level comment.
		if ( 0 !== (int) $commentdata['comment_parent'] ) {
			return;
		}

		// We do not want to send email since it's already approved
		if( $comment_approved === 1 ){
			return;
		}

		$custom_emails = $this->cpt()->get_emails( 'new_job_review_pending_approval' );

		if ( empty( $custom_emails ) ) {
			return; // Allow sending default email if we don't have any custom ones setup
		}

		// Set class object vals for shortcode handling
		$this->job = $post;
		$this->comment = get_comment( $comment_id );

		$this->hooks()->hook = 'new_job_review_pending_approval';
		$this->cpt()->send_email( $custom_emails, $this->job->ID );
	}

	/**
	 * Short circuit and send custom email for review report (to admin)
	 *
	 *
	 * @since 2.4.0
	 *
	 * @param $args
	 *
	 * @return mixed
	 */
	function report_job_review_admin( $args ){

		$current_user = wp_get_current_user();
		$custom_emails = $this->cpt()->get_emails( 'report_job_review_admin' );

		if ( empty( $custom_emails ) ) {
			return $args; // Allow sending default email if we don't have any custom ones setup
		}

		/**
		 * Currently the reviews plugin adds NO way to prevent emails from being sent, or determine which email
		 * is being sent.  As such, we have to check that the email being sent has the to value set as admin email
		 * and reply_to set to current user's email (listing owner)
		 */
		if( $args['reply_to'] !== $current_user->user_email || $args['to'] !== get_bloginfo( 'admin_email' ) ){
			return $args;
		}

		// Setting message and to value to empty string essentially short circuits wp_mail
		// @see https://core.trac.wordpress.org/ticket/35069
		$args['to'] = '';
		$args['message'] = '';

		// Go ahead and send out emails before returning args to short circuit
		$this->hooks()->hook = 'report_job_review_admin';
		$this->cpt()->send_email( $custom_emails, $this->job->ID );

		/**
		 * Due to the way that Review addon works, it ONLY sends email to user/owner when wp_mail returns true,
		 * but since there's no way to prevent default emails from being sent in Reviews plugin, we have to short
		 * circuit wp_mail, which causes false to be returned, thus resulting in user email never sending.
		 *
		 * Basically this results in REQUIRING a custom email being setup for user/listing owner if the admin wants
		 * to use a custom email that is sent to them for reporting a review.
		 */
		$custom_owner_emails = $this->cpt()->get_emails( 'report_job_review_owner' );

		if ( ! empty( $custom_owner_emails ) ) {
			// Go ahead and send out emails before returning args to short circuit
			$this->hooks()->hook = 'report_job_review_owner';
			$this->cpt()->send_email( $custom_owner_emails, $this->job->ID );
		}

		// We have to manually set the notice on dashboard, since that part of code will never be actually called
		if( function_exists( 'wpjmr_set_dashboard_notices' ) ){
			wpjmr_set_dashboard_notices( sprintf( __( 'Review #%1$d for %2$s reported to site admin.', 'wp-job-manager-reviews', 'wp-job-manager-emails' ), $this->comment->comment_ID, $this->job->post_title ) );
		}

		return $args;
	}

	/**
	 * Short circuit and send custom email for review report (to listing owner)
	 *
	 * This method is specifically called/used only when there is NO custom email setup for admin
	 * when a review is reported
	 *
	 * @since 2.4.0
	 *
	 * @return boolean
	 */
	function report_job_review_owner( $args ){

		$current_user = wp_get_current_user();
		$custom_emails = $this->cpt()->get_emails( 'report_job_review_owner' );

		if ( empty( $custom_emails ) || $args['to'] !== $current_user->user_email ) {
			return $args; // Allow sending default email if we don't have any custom ones setup
		}

		/**
		 * Currently the reviews plugin adds NO way to prevent emails from being sent, or determine which email
		 * is being sent.  As such, we have to check that the email being sent has the to value set as the current
		 * user email (meaning listing owner), and reply_to set to admin email
		 */
		if ( $args['to'] !== $current_user->user_email || $args['reply_to'] !== get_bloginfo( 'admin_email' ) ) {
			return $args;
		}

		// Setting message and to value to empty string essentially short circuits wp_mail
		// @see https://core.trac.wordpress.org/ticket/35069
		$args['to'] = '';
		$args['message'] = '';

		// Go ahead and send out emails before returning args to short circuit
		$this->hooks()->hook = 'report_job_review_owner';
		$this->cpt()->send_email( $custom_emails, $this->job->ID );

		return $args;
	}

	/**
	 *
	 *
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	function new_job_review_pending_approval_reviewer_default_content() {

		$content = '';
		$content .= sprintf( __( 'Your review for "%s" was sent successfully, and is pending approval.', 'wp-job-manager-emails' ), '[job_title]' ) . "\n";
		$content .= '[divider]' . "\n";
		$content .= sprintf( __( 'Listing URL: %s', 'wp-job-manager-emails' ), '[view_job_url]' ) . "\n";

		return $content;
	}

	/**
	 *
	 *
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	function new_job_review_pending_approval_admin_default_content() {

		$content = '';
		$content .= sprintf( __( 'A new review has been submitted for "%s", and is pending approval.', 'wp-job-manager-emails' ), '[job_title]' ) . "\n";
		$content .= '[divider]' . "\n" . "\n";
		$content .= sprintf( __( 'Listing URL: %s', 'wp-job-manager-emails' ), '[view_job_url]' ) . "\n";
		$content .= sprintf( __( 'Reviewed by: %s', 'wp-job-manager-emails' ), '[reviewer_name] ([reviewer_email])' ) . "\n";
		$content .= sprintf( __( 'Review Status: %s', 'wp-job-manager-emails' ), '[review_status]' ) . "\n";
		$content .= sprintf( __( 'Manage/Approve Review: %s', 'wp-job-manager-emails' ), '[review_edit_url]' ) . "\n" . "\n";

		$content .= __( 'Ratings:', 'wp-job-manager-emails' ) . "\n";
		$content .= '[reviewer_ratings divider]' . "\n" . "\n";
		$content .= __( 'Review:', 'wp-job-manager-emails' ) . "\n";
		$content .= '[reviewer_review divider]' . "\n" . "\n";
		$content .= '[review_url]' . "\n";

		return $content;
	}

	/**
	 *
	 *
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	function new_job_review_pending_approval_owner_default_content() {

		$content = '';
		$content .= sprintf( __( 'A new review has been submitted for "%s", and is pending approval.', 'wp-job-manager-emails' ), '[job_title]' ) . "\n";
		$content .= '[divider]' . "\n" . "\n";
		$content .= sprintf( __( 'Listing URL: %s', 'wp-job-manager-emails' ), '[view_job_url]' ) . "\n";
		$content .= sprintf( __( 'Reviewed by: %s', 'wp-job-manager-emails' ), '[reviewer_name] ([reviewer_email])' ) . "\n";
		$content .= sprintf( __( 'Review Status: %s', 'wp-job-manager-emails' ), '[review_status]' ) . "\n";

		$content .= __( 'Ratings:', 'wp-job-manager-emails' ) . "\n";
		$content .= '[reviewer_ratings divider]' . "\n" . "\n";
		$content .= __( 'Review:', 'wp-job-manager-emails' ) . "\n";
		$content .= '[reviewer_review divider]' . "\n" . "\n";
		$content .= '[review_url]' . "\n";

		$content .= "\n" . __( 'Please visit your review dashboard to approve this review.', 'wp-job-manager-emails' );
		return $content;
	}

	/**
	 *
	 *
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	function new_job_review_submitted_default_content() {

		$content = '';
		$content .= sprintf( __( 'A new review has been submitted for "%s"', 'wp-job-manager-emails' ), '[job_title]' ) . "\n";
		$content .= '[divider]' . "\n" . "\n";
		$content .= sprintf( __( 'Listing URL: %s', 'wp-job-manager-emails' ), '[view_job_url]' ) . "\n";
		$content .= sprintf( __( 'Reviewed by: %s', 'wp-job-manager-emails' ), '[reviewer_name] ([reviewer_email])' ) . "\n";
		$content .= sprintf( __( 'Review Status: %s', 'wp-job-manager-emails' ), '[review_status]' ) . "\n";
		$content .= __( 'Ratings:', 'wp-job-manager-emails' ) . "\n";
		$content .= '[reviewer_ratings divider]' . "\n" . "\n";
		$content .= __( 'Review:', 'wp-job-manager-emails' ) . "\n";
		$content .= '[reviewer_review divider]' . "\n" . "\n";
		$content .= '[review_url]' . "\n";

		return $content;
	}

	/**
	 *
	 *
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	function report_job_review_admin_default_content() {

		$content = '';
		$content .= __( 'Hi Admin,', 'wp-job-manager-emails' ) . "\n" . "\n";
		$content .= sprintf( __( 'A review moderation has been requested for "%s"', 'wp-job-manager-emails'), '[job_title]') . "\n";
		$content .= '[divider]' . "\n";
		$content .= sprintf( __( 'Listing URL: %s', 'wp-job-manager-emails' ), '[view_job_url]' ) . "\n";
		$content .= sprintf( __( 'Requested by: %s', 'wp-job-manager-emails' ), '[job_author_name] ([job_author_email])' ) . "\n";
		$content .= sprintf( __( 'Review ID: %s', 'wp-job-manager-emails' ), '[review_id]' ) . "\n";
		$content .= sprintf( __( 'Manage Review: %s', 'wp-job-manager-emails' ), '[review_edit_url]' ) . "\n" . "\n";
		$content .= __( 'Thank you.', 'wp-job-manager-emails' ) . "\n";

		return $content;
	}

	/**
	 *
	 *
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	function report_job_review_owner_default_content() {

		$content = '';
		$content .= sprintf( __( 'Your review moderation request for "%s" was sent successfully.', 'wp-job-manager-emails'), '[job_title]') . "\n";
		$content .= '[divider]' . "\n";
		$content .= sprintf( __( 'Listing URL: %s', 'wp-job-manager-emails' ), '[view_job_url]' ) . "\n";
		$content .= sprintf( __( 'Requested by: %s', 'wp-job-manager-emails' ), '[job_author_name] ([job_author_email])' ) . "\n";
		$content .= sprintf( __( 'Review ID: %s', 'wp-job-manager-emails' ), '[review_id]' ) . "\n";

		return $content;
	}

	/**
	 *
	 *
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	function job_review_approved_default_content() {

		$content = '';
		$content .= sprintf( __( 'Your review for "%s" has been approved!', 'wp-job-manager-emails'), '[job_title]') . "\n";
		$content .= '[divider]' . "\n";
		$content .= sprintf( __( 'Listing URL: %s', 'wp-job-manager-emails' ), '[view_job_url]' ) . "\n";
		$content .= sprintf( __( 'Review URL: %s', 'wp-job-manager-emails' ), '[review_url]' ) . "\n";

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
	 * @since 2.4.0
	 *
	 * @return \WP_Job_Manager_Emails_Hooks_Job
	 */
	function hooks(){
		return $this->job_obj->hooks;
	}

}