<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WP_Job_Manager_Emails_Queue
 *
 * @since 2.4.0
 *
 */
class WP_Job_Manager_Emails_Queue {

	/*
	 * WP_Job_Manager_Emails_Integration
	 */
	/**
	 * @var null|\WP_Job_Manager_Emails_Integration
	 */
	protected $integration = null;

	/**
	 * WP_Job_Manager_Emails_Queue constructor.
	 *
	 * @param $integration WP_Job_Manager_Emails_Integration
	 */
	public function __construct( $integration ) {
		$this->integration = $integration;
		add_filter( 'job_manager_emails_email_should_send', array( $this, 'maybe_queue' ), 99999, 4 );
		add_action( 'job_manager_emails_check_email_queue', array( $this, 'maybe_send' ) );
//		update_option( 'job_manager_emails_queued_delayed_pending', array() ); // used for debug testing
	}

	/**
	 * Maybe Queue a Delayed Email
	 *
	 *
	 * @since 2.4.0
	 *
	 * @param $should_send
	 * @param $template
	 * @param $listing_id
	 * @param $email
	 *
	 * @return bool
	 */
	public function maybe_queue( $should_send, $template, $listing_id, $email ){

		// First make sure this email should be sent, then check if the template has configured a delay
		if( $should_send && $template && $template->delay_send_hours && ! empty( $template->delay_send_hours ) ){

			$delayed_emails = get_option( 'job_manager_emails_queued_delayed_pending', array() );

			// Add this email as delayed email to send
			$delayed_emails[] = array(
				'delay_by' => $template->delay_send_hours,
				'epoch_send_after' => strtotime( "+{$template->delay_send_hours} hours" ),
				'to' => $email->to,
				'subject' => $email->subject,
				'content' => $email->content,
				'headers' => $email->headers,
				'attachments' => $email->attachments,
				'listing_id' => $listing_id,
			);

			update_option( 'job_manager_emails_queued_delayed_pending', $delayed_emails );

			// Schedule cron now that we have delayed emails to send
			self::maybe_schedule_cron();
			return false;
		}

		return $should_send;

	}

	/**
	 * Maybe send queued emails
	 *
	 * This method will be called by the cron that is registered when the queues are added.  This should
	 * also remove the cron after all queued emails have been sent.
	 *
	 *
	 * @since 2.4.0
	 *
	 */
	public function maybe_send(){

		$delayed_emails = get_option( 'job_manager_emails_queued_delayed_pending', array() );

		// No delayed emails, why was this called by cron?  Let's remove cron just in case
		if( empty( $delayed_emails ) ){
			self::remove_cron();
			return;
		}

		foreach( (array) $delayed_emails as $index => $de ){

			// Email has been delayed far enough, time to send
			if( (int) $de['epoch_send_after'] < time() ){

				// As long as we have a *somewhat* valid email (includes @) send the email
				if ( ! empty( $de['to'] ) && strpos( $de['to'], '@' ) !== false ) {
					$result = wp_mail( $de['to'], $de['subject'], $de['content'], $de['headers'], $de['attachments'] );
				}

				unset( $delayed_emails[ $index ] );
			}

		}

		// Remove cron if we don't have any other delayed emails to send
		if( empty( $delayed_emails ) ){
			self::remove_cron();
		}

		// Update delayed emails option after removing any that have been sent
		update_option( 'job_manager_emails_queued_delayed_pending', $delayed_emails );
	}

	/**
	 * Schedule Cron
	 *
	 *
	 * @since 2.4.0
	 *
	 */
	public static function maybe_schedule_cron() {

		if ( ! wp_next_scheduled( 'job_manager_emails_check_email_queue' ) ) {
			wp_schedule_event( time(), 'hourly', 'job_manager_emails_check_email_queue' );
		}
	}

	/**
	 * Remove the cron
	 *
	 *
	 * @since 2.4.0
	 *
	 */
	public static function remove_cron(){
		wp_clear_scheduled_hook( 'job_manager_emails_check_email_queue' );
	}
}