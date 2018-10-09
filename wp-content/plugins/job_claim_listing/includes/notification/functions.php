<?php
/**
 * Email Notification Functions
 *
 * @since 3.0.0
 **/
namespace wpjmcl\notification;
use wpjmcl\claim\Functions as Claim;
if ( ! defined( 'WPINC' ) ) { die; }

/**
 * Reuseable Functions to Email Notification
 *
 * @since 1.0.0
 */
final class Functions {

	/**
	 * Site Name
	 *
	 * @see WP/wp-includes/pluggable.php line 321
	 * @since 3.0.0
	 */
	public static function site_name() {
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );
		if ( substr( $sitename, 0, 4 ) == 'www.' ) {
			$sitename = substr( $sitename, 4 );
		}
		return $sitename;
	}


	/**
	 * Send Mail
	 * Wrapper function for wp_mail()
	 *
	 * @uses wp_mail()
	 * @link https://developer.wordpress.org/reference/functions/wp_mail/
	 * @since 1.0.0
	 */
	public static function send_mail( $args ) {
		$args_default = array(
			'to'             => '',
			'from'           => 'wordpress@' . self::site_name(),
			'from_name'      => get_bloginfo( 'name' ),
			'reply_to'       => get_bloginfo( 'admin_email' ),
			'subject'        => '',
			'message'        => '',
			'content_type'   => 'text/html',
			'charset'        => get_bloginfo( 'charset' ),
		);
		$args = wp_parse_args( $args, $args_default );
		$args = apply_filters( 'wpjmcl_notification_send_mail_args', $args );

		$headers  = array(
			'From: "' . strip_tags( $args['from_name'] ) . '" <' . sanitize_email( $args['from'] ) . '>',
			'Reply-To: ' . $args['reply_to'],
			'Content-type: ' . $args['content_type'] . '; charset: ' . $args['charset'],
		);

		if ( $args['to'] && is_email( $args['to'] ) && $args['subject'] && $args['message'] ) {
			wp_mail( sanitize_email( $args['to'] ), esc_attr( $args['subject'] ), self::sanitize_email_message( $args['message'] ), $headers );
		}
	}


	/**
	 * Sanitize Email Message
	 *
	 * @since 3.0.0
	 */
	public static function sanitize_email_message( $input ) {

		/* allowed tags */
		$allowed_tags = array(
			'a' => array(
				'href' => array(),
				'title' => array(),
				'target' => array(),
			),
			'abbr' => array(
				'title' => array(),
			),
			'acronym' => array(
				'title' => array(),
			),
			'code' => array(),
			'pre' => array(),
			'em' => array(),
			'strong' => array(),
			'br' => array(),
				'div' => array(),
			'p' => array(),
			'ul' => array(),
			'ol' => array(),
			'li' => array(),
				'h1' => array(),
			'h2' => array(),
			'h3' => array(),
			'h4' => array(),
			'h5' => array(),
			'h6' => array(),
				'img' => array(
					'src' => array(),
					'class' => array(),
					'alt' => array(),
				),
			);

		$allowed_tags = apply_filters( 'wpjmcl_notification_email_message_allowed_tags', $allowed_tags );

		return wp_kses( $input, $allowed_tags );
	}

	/**
	 * New Claim Default Mail Message to Claimer.
	 *
	 * @since 3.8.0
	 */
	public static function default_email_message_new_claim_claimer() {
		$message = __(
				'Hi %claimer_name%,' . "\n" .
				"On %claim_date% you submitted a claim for a listing. Here's the details." . "\n\n" .
				'Listing URL: %listing_url%' . "\n" .
				'Claimed by: %claimer_name%' . "\n" .
				'Claim Status: %claim_status%' . "\n\n" .
				'You can also view your claim online: %claim_url%' . "\n\n" .
				'Thank you.' . "\n"
			,'wp-job-manager-claim-listing' );
		return $message;
	}

	/**
	 * New Claim Default Mail Message to Admin.
	 *
	 * @since 3.8.0
	 */
	public static function default_email_message_new_claim_admin() {
		$message = __(
				'Hi Admin,' . "\n" .
				"New claim submitted, here's the details." . "\n\n" .
				'Listing URL: %listing_url%' . "\n" .
				'Claimed by: %claimer_name%' . "\n" .
				'Claim Status: %claim_status%' . "\n\n" .
				'Edit Claim: %claim_edit_url%' . "\n\n" .
				'Thank you.' . "\n"
			,'wp-job-manager-claim-listing' );
		return $message;
	}

	/**
	 * Claim Status Update Default Mail Message to Claimer.
	 *
	 * @since 3.8.0
	 */
	public static function default_email_message_status_update_claimer() {
		$message = __(
				'Hi %claimer_name%,' . "\n" .
				"On %claim_date% you submitted a claim for a listing. Your claim status is updated. Here's the details." . "\n\n" .
				'Listing URL: %listing_url%' . "\n" .
				'Claimed by: %claimer_name%' . "\n\n" .
				'Previous Claim Status: %claim_status_old%' . "\n" .
				'New Claim Status: %claim_status%' . "\n\n" .
				'Thank you.' . "\n"
			,'wp-job-manager-claim-listing' );
		return $message;
	}

	/**
	 * Claim Status Update Default Mail Message to Admin.
	 *
	 * @since 3.8.0
	 */
	public static function default_email_message_status_update_admin() {
		$message = __(
				'Hi Admin,' . "\n" .
				"Claim status for listing %listing_title% is updated, here's the details." . "\n\n" .
				'Listing URL: %listing_url%' . "\n" .
				'Claimed by: %claimer_name%' . "\n\n" .
				'Previous Claim Status: %claim_status_old%' . "\n" .
				'New Claim Status: %claim_status%' . "\n\n" .
				'You can edit this claim: %claim_edit_url%' . "\n\n" .
				'Thank you.' . "\n"
			,'wp-job-manager-claim-listing' );
		return $message;
	}


} // end class

