<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Private_Messages_Notifications
 *
 * Send notification when a message is received
 *
 * @class    Private_Messages_Notifications
 * @category Class
 * @author   Astoundify
 */
class Private_Messages_Notifications {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// New message notification.
		add_action( 'pm_new_message', array( $this, 'new_message_notification' ), 10, 2 );

		// Replace tag pattern with actual content.
		add_filter( 'pm_new_message_notification_subject', array( $this, 'filter_tags' ), 10, 2 );
		add_filter( 'pm_new_message_notification_message', array( $this, 'filter_tags' ), 10, 2 );
	}

	/**
	 * New Message Notification.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Message.
	 * @param object $thread  Thread object.
	 * @return void
	 */
	public function new_message_notification( $message, $thread ) {
		if ( ! pm_get_option( 'send_message_notification', true ) ) {
			return;
		}

		// Thread data.
		$author_id    = absint( $thread->get_author_id() );
		$recipient_id = absint( $thread->get_recipient_id() );

		// Message sender, is the message author.
		$sender_id = absint( $message->get_author_id() );

		// Get message receiver.
		$receiver_id = ( $sender_id === $author_id ) ? $recipient_id : $author_id;

		// Receiver User.
		$receiver = new WP_User( $receiver_id );

		// Bail if receiver user is not exists.
		if ( ! $receiver->exists() ) {
			return;
		}

		// Hook context.
		$context = array(
			'thread'  => $thread,
			'message' => $message,
		);

		$to      = apply_filters( 'pm_message_notification_recipient', pm_get_user_display_name( $receiver ) . ' <' . $receiver->user_email . '>', $context );
		$subject = apply_filters( 'pm_new_message_notification_subject', pm_get_option( 'notification_subject', pm_default_new_message_notification_subject() ), $context );
		$message = apply_filters( 'pm_new_message_notification_message', wpautop( pm_get_option( 'notification_message', pm_default_new_message_notification_message() ) ), $context );
		$headers = apply_filters( 'pm_message_notification_headers', array( 'Content-Type: text/html; charset=UTF-8' ) );

		// Send email.
		$sent = wp_mail( $to, $subject, $message, $headers );
	}

	/**
	 * Replace message tags with the respective dynamic content.
	 *
	 * @todo Make this a bit more extensible. Very basic currently.
	 *
	 * @since 1.0.0
	 * @param string $content
	 * @param string $content The modified content
	 */
	public function filter_tags( $content, $context ) {

		// Sender name is always message author.
		$sender_id = absint( $context['message'] ->get_author_id() );
		$sender_name = pm_get_user_display_name( $context['message']->get_author() );

		// Thread data.
		$author_id = absint( $context['thread']->get_author_id() );
		$recipient_id = absint( $context['thread']->get_recipient_id() );

		// Get receiver name and ID.
		$receiver_id = ( $sender_id === $author_id ) ? $recipient_id : $author_id;
		$receiver = new WP_User( $receiver_id );
		$receiver_name = pm_get_user_display_name( $receiver );

		// Filters tags.
		$data_tags = apply_filters( 'pm_notification_data_tags', array(
			'{site_name}'        => get_bloginfo( 'name' ),
			'{sender_name}'      => $sender_name,
			'{recipient_name}'   => $receiver_name,
			'{message}'          => wpautop( $context['message']->get_content() ),
			'{link_to_message}'  => esc_url_raw( $context['thread']->get_url() ) . '#message-' . $context['message']->ID,
		) );

		$content = str_replace( array_keys( $data_tags ), array_values( $data_tags ), $content );

		return $content;
	}
}

new Private_Messages_Notifications();
