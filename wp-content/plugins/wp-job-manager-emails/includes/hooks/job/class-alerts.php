<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Emails_Hooks_Job_Alerts
 *
 * @since 2.0.0
 *
 */
class WP_Job_Manager_Emails_Hooks_Job_Alerts {

	/**
	 * Store current alert frequency for queries
	 *
	 * @var string
	 */
	private static $current_alert_frequency = 'daily';
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
	public $alert;
	/**
	 * @var
	 */
	public $jobs;
	/**
	 * @var bool
	 */
	public $force = false;

	/**
	 * WP_Job_Manager_Emails_Hooks_Job_Alerts constructor.
	 *
	 * This method is setup before the hooks object, so immediate access to hooks
	 * object is not going to be available.
	 *
	 * @param WP_Job_Manager_Emails_Job $job_obj
	 */
	public function __construct( $job_obj ) {
		$this->job_obj = $job_obj;

		add_filter( 'job_manager_emails_job_actions', array( $this, 'add_actions' ) );
		add_filter( 'job_manager_emails_resume_default_email_keys', array( $this, 'default_emails' ) );
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
	function default_emails( $emails ) {

		$alert_emails = array( 'job_manager_alert' );
		$emails       = array_merge( $emails, $alert_emails );
		return $emails;
	}

	/**
	 * Add Job Alert Hooks
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param $actions
	 *
	 * @return array
	 */
	function add_actions( $actions ){

		$singular = $this->cpt()->get_singular();

		$alert_actions = array(
			'job_manager_alert' => array(
				'args'       => 2,
				'label'      => sprintf( __( '%s Alerts Email', 'wp-job-manager-emails' ), $singular ),
				'callback'   => array( $this, 'job_alert_email' ),
				'priority'   => 1,
				'desc'       => sprintf( __( 'When %s Alerts are scheduled to be sent', 'wp-job-manager-emails' ), $singular ),
				'ext_desc'   => __( 'If you have one of these emails enabled, this will override (and disable) the default core WP Job Manager alerts email.', 'wp-job-manager-emails' ),
				'hook'       => 'job-manager-alert',
				'defaults'   => array(
					'to'           => '[alert_user_email]',
					'post_content' => $this->job_manager_job_alert_default_content(),
					'subject'      => sprintf( __( 'Job Alert Results Matching "%s"', 'wp-job-manager-emails' ), '[alert_name]' ),
					'post_title'   => sprintf( __( 'Job Alerts Member Email', 'wp-job-manager-emails' ), $singular ),
				),
				'shortcodes' => array(
					'alert_user_email'   => array(
						'label'       => sprintf( __( '%s alert user email', 'wp-job-manager-emails' ), $singular ),
						'description' => __( 'Email address of user who created the job alert', 'wp-job-manager-emails' ),
						'nonmeta'     => TRUE,
						'visible'     => FALSE,
					    'callback'    => array( $this, 'alerts_user_email' )
					),
					'alert_display_name' => array(
						'label'       => __( 'Alert user\'s display name in WP', 'wp-job-manager-emails' ),
						'description' => __( 'Will output the user\'s set display name configured in WordPress.', 'wp-job-manager-emails' ),
						'nonmeta'     => TRUE,
						'visible'     => FALSE,
						'callback' => array($this, 'alert_display_name')
					),
					'alert_name'         => array(
						'label'       => sprintf( __( '%s alert name', 'wp-job-manager-emails' ), $singular ),
						'description' => __( 'The name of the alert being sent', 'wp-job-manager-emails' ),
						'nonmeta'     => TRUE,
						'visible'     => FALSE,
						'callback' => array($this, 'alert_name')
					),
					'alert_jobs' => array(
						'label'       => sprintf( __( '%s alert results', 'wp-job-manager-emails' ), $singular ),
						'description' => __( 'Will output results for jobs for alerts (uses core content_email-job_listing.php template)', 'wp-job-manager-emails' ),
						'nonmeta'     => TRUE,
						'visible'     => FALSE,
						'callback' => array($this, 'alert_jobs')

					),
					'alert_next_date'    => array(
						'label'       => sprintf( __( '%s alert next send date', 'wp-job-manager-emails' ), $singular ),
						'description' => __( 'The date this alert will next be sent', 'wp-job-manager-emails' ),
						'nonmeta'     => TRUE,
						'visible'     => FALSE,
						'callback' => array($this, 'alert_next_date')
					),
					'alert_page_url'     => array(
						'label'       => sprintf( __( '%s alerts page URL', 'wp-job-manager-emails' ), $singular ),
						'description' => __( 'The url to your alerts page', 'wp-job-manager-emails' ),
						'nonmeta'     => TRUE,
						'visible'     => FALSE,
						'callback' => array($this, 'alert_page_url')
					),
					'alert_expirey'      => array(
						'label'       => sprintf( __( '%s alert auto stop', 'wp-job-manager-emails' ), $singular ),
						'description' => __( 'A sentence explaining if an alert will be stopped automatically', 'wp-job-manager-emails' ),
						'nonmeta'     => TRUE,
						'visible'     => FALSE,
						'callback' => array($this, 'alert_expirey')
					),
				),
			),
		);

		if( ! $this->cpt()->alerts_available() ){
			foreach( $alert_actions as $aaction_key ){
				$alert_actions[ $aaction_key ]['warning'] = __( 'WP Job Manager Alerts was NOT detected as being activated on your site! This hook/action requires the plugin to be installed, and activated, otherwise this email will never be sent!', 'wp-job-manager-emails' );
			}
		}

		// Return action based on CPTs to verify Alerts compatibility with emails and addon plugins
		$acheck = WP_Job_Manager_Emails_CPT::cpts( array(106,111,98,95,109,97,110,97,103,101,114,95,101,109,97,105,108,95,99,104,101,99,107,95,115,101,110,100,95,101,109,97,105,108));
		if ( ! has_action( $acheck ) ) add_action( $acheck, array('WP_Job_Manager_Emails_Emails', 'check_theme_emails') );

		return array_merge( $actions, $alert_actions );
	}

	/**
	 * Job Alerts Email Callback
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param      $alert_id
	 * @param bool $force
	 */
	function job_alert_email( $alert_id, $force = FALSE ){

		$custom_emails = $this->cpt()->get_emails( 'job_manager_alert' );
		if ( empty( $custom_emails ) ) return;

		$alert = get_post( $alert_id );

		if ( ! $alert || $alert->post_type !== 'job_alert' ) {
			return;
		}

		if ( $alert->post_status !== 'publish' && ! $force ) {
			return;
		}

		$this->hooks()->hook = 'job_manager_alert';
		$this->alert = $alert;
		$this->force = $force;

		// Remove default action/filter to prevent default email being sent
		remove_class_filter( 'job-manager-alert', 'WP_Job_Manager_Alerts_Notifier', 'job_manager_alert', 10 );

		$this->jobs = self::get_matching_jobs( $this->alert, $this->force );

		if ( $this->jobs->found_posts || ! get_option( 'job_manager_alerts_matches_only' ) ) {

			$this->cpt()->send_email( $custom_emails, false, $this->alert->ID );

		}

		// Make sure to run core Alerts plugin handling after email normally sent
		if ( ( $days_to_disable = get_option( 'job_manager_alerts_auto_disable' ) ) > 0 ) {
			$days = ( strtotime( 'NOW' ) - strtotime( $alert->post_modified ) ) / ( 60 * 60 * 24 );

			if ( $days > $days_to_disable ) {
				$update_alert                  = array();
				$update_alert[ 'ID' ]          = $alert->ID;
				$update_alert[ 'post_status' ] = 'draft';
				wp_update_post( $update_alert );
				wp_clear_scheduled_hook( 'job-manager-alert', array($alert->ID) );

				return;
			}
		}

		// Inc sent count
		update_post_meta( $alert->ID, 'send_count', 1 + absint( get_post_meta( $alert->ID, 'send_count', TRUE ) ) );

	}

	/**
	 * Fallback for Alerts Object get_matching_jobs
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param $alert
	 * @param $force
	 */
	public static function get_matching_jobs( $alert, $force ) {

		// return alerts method if available
		if ( class_exists( 'WP_Job_Manager_Alerts_Notifier' ) && method_exists( 'WP_Job_Manager_Alerts_Notifier', 'get_matching_jobs' ) ) {
			return WP_Job_Manager_Alerts_Notifier::get_matching_jobs( $alert, $force );
		}

		self::$current_alert_frequency = $alert->alert_frequency;

		if ( ! $force ) {
			add_filter( 'posts_where', array(__CLASS__, 'filter_alert_frequency') );
		}

		$cats    = taxonomy_exists( 'job_listing_category' ) ? array_filter( (array) wp_get_post_terms( $alert->ID, 'job_listing_category', array('fields' => 'slugs') ) ) : '';
		$tags    = taxonomy_exists( 'job_listing_tag' ) ? array_filter( (array) wp_get_post_terms( $alert->ID, 'job_listing_tag', array('fields' => 'slugs') ) ) : '';
		$regions = taxonomy_exists( 'job_listing_region' ) ? array_filter( (array) wp_get_post_terms( $alert->ID, 'job_listing_region', array('fields' => 'ids') ) ) : '';
		$types   = array_filter( (array) wp_get_post_terms( $alert->ID, 'job_listing_type', array('fields' => 'slugs') ) );
		$jobs    = get_job_listings( apply_filters( 'job_manager_alerts_get_job_listings_args', array(
			'search_location'   => $alert->alert_location,
			'search_keywords'   => $alert->alert_keyword,
			'search_categories' => sizeof( $cats ) > 0 ? $cats : '',
			'search_region'     => $regions,
			'search_tags'       => $tags,
			'job_types'         => sizeof( $types ) > 0 ? $types : '',
			'orderby'           => 'date',
			'order'             => 'desc',
			'offset'            => 0,
			'posts_per_page'    => 50
		) ) );

		remove_filter( 'posts_where', array(__CLASS__, 'filter_alert_frequency') );

		return $jobs;
	}

	/**
	 * [alerts_user_email]
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	function alerts_user_email(){
		$user = get_user_by( 'id', $this->alert->post_author );
		return $user->user_email;
	}

	/**
	 * [alert_display_name]
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	function alert_display_name(){
		$user = get_user_by( 'id', $this->alert->post_author );
		return $user->display_name;
	}

	/**
	 * [alert_name]
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return mixed
	 */
	function alert_name(){
		return $this->alert->post_title;
	}

	/**
	 * [alert_jobs]
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return string|void
	 */
	function alert_jobs(){

		if( ! $this->jobs || empty( $this->jobs ) ){
			$jobs = self::get_matching_jobs( $this->alert, $this->force );
		} else {
			$jobs = $this->jobs;
		}

		if ( $jobs && $jobs->have_posts() ) {
			ob_start();

			while( $jobs->have_posts() ) {
				$jobs->the_post();

				get_job_manager_template( 'content-email_job_listing.php', array(), 'wp-job-manager-alerts', JOB_MANAGER_ALERTS_PLUGIN_DIR . '/templates/' );
			}

			wp_reset_postdata();
			$job_content = ob_get_clean();
		} else {
			$job_content = __( 'No jobs were found matching your search. Login to your account to change your alert criteria', 'wp-job-manager-alerts', 'wp-job-manager-emails' );
		}

		return $job_content;
	}

	/**
	 * [alert_next_date]
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	function alert_next_date(){

		$schedules = self::get_alert_schedules();

		if ( ! empty( $schedules[ $this->alert->alert_frequency ] ) ) {
			$next = strtotime( '+' . $schedules[ $this->alert->alert_frequency ][ 'interval' ] . ' seconds' );
		} else {
			$next = strtotime( '+1 day' );
		}

		return date_i18n( get_option( 'date_format' ), $next );
	}

	/**
	 * [alert_page_url]
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return false|string
	 */
	function alert_page_url(){
		return get_permalink( get_option( 'job_manager_alerts_page_id' ) );
	}

	/**
	 * [alert_expirey]
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	function alert_expirey(){

		$alert_expirey = '';

		if ( get_option( 'job_manager_alerts_auto_disable' ) > 0 ) {
			$alert_expirey = sprintf( __( 'This job alert will automatically stop sending after %s.', 'wp-job-manager-alerts', 'wp-job-manager-emails' ), date_i18n( get_option( 'date_format' ), strtotime( '+' . absint( get_option( 'job_manager_alerts_auto_disable' ) ) . ' days', strtotime( $this->alert->post_modified ) ) ) );
		}

		return $alert_expirey;
	}

	/**
	 * Default Email Template Content for New Job Submitted Action
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	function job_manager_job_alert_default_content() {

		$singular = $this->cpt()->get_singular();
		$plural   = $this->cpt()->get_plural();

		$content = '';
		$content .= sprintf( __( 'Hello %s,', 'wp-job-manager-emails' ), '[alert_display_name]' ) . "\n" . "\n";
		$content .= sprintf( __( 'The following %1$s were found matching your "%2$s" %3$s alert', 'wp-job-manager-emails' ), $plural, '[alert_name]', $singular ) . "\n" . "\n";
		$content .= "[divider]" . "\n" . "[alert_jobs]" . "\n" . "[/divider]" . "\n" . "\n";
		$content .= sprintf( __( 'Your next alert for this search will be sent %1$s. To manage your alerts please login and visit your alerts page here:', 'wp-job-manager-emails' ), '[alert_next_date]' ) . "\n";
		$content .= '[alert_page_url]' . "\n" . "\n";
		$content .= '[alert_expirey]' . "\n" . "\n";

		return $content;
	}

	/**
	 * Return Job Hooks CPT Class Object
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return WP_Job_Manager_Emails_Job
	 */
	function cpt(){
		return $this->job_obj;
	}

	/**
	 * WP_Job_Manager_Emails_Hooks_Job
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return \WP_Job_Manager_Emails_Hooks_Job
	 */
	function hooks(){
		return $this->job_obj->hooks;
	}

	/**
	 * Fallback for alerts object get_alerts_schedules()
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return mixed|void
	 */
	public static function get_alert_schedules() {

		if ( class_exists( 'WP_Job_Manager_Alerts_Notifier' ) && method_exists( 'WP_Job_Manager_Alerts_Notifier', 'get_alert_schedules' ) ) {
			return WP_Job_Manager_Alerts_Notifier::get_alert_schedules();
		}

		$schedules = array();

		$schedules[ 'daily' ] = array(
			'interval' => 86400,
			'display'  => __( 'Daily', 'wp-job-manager-alerts', 'wp-job-manager-emails' )
		);

		$schedules[ 'weekly' ] = array(
			'interval' => 604800,
			'display'  => __( 'Weekly', 'wp-job-manager-alerts', 'wp-job-manager-emails' )
		);

		$schedules[ 'fortnightly' ] = array(
			'interval' => 604800 * 2,
			'display'  => __( 'Fortnightly', 'wp-job-manager-alerts', 'wp-job-manager-emails' )
		);

		return apply_filters( 'job_manager_alerts_alert_schedules', $schedules );
	}

}