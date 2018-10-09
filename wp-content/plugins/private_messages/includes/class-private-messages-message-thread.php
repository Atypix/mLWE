<?php
/**
 * Message Thread
 *
 * @since 1.0.0
 */
class Private_Messages_MessageThread {

	/**
	 * Thread ID.
	 *
	 * @since 1.0.0
	 * @var $thread_id
	 */
	public $ID;

	/**
	 * Thread.
	 *
	 * @since 1.0.0
	 * @var $thread_id WP_Post
	 */
	public $thread;

	/**
	 * Messages.
	 *
	 * @since 1.0.0
	 * @var $messages
	 */
	public $messages;

	/**
	 * Thread Recipient.
	 *
	 * @since 1.0.0
	 * @var $recipient
	 */
	public $recipient;

	/**
	 * Thread Author.
	 *
	 * @since 1.0.0
	 * @var $author
	 */
	public $author;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @param int $thread_id Post ID.
	 */
	public function __construct( $thread_id = false ) {
		$this->ID = $thread_id;
		$this->data = get_post( $this->ID );
	}

	/**
	 * Get the associated messages
	 *
	 * @since 1.0.0
	 *
	 * @return array $messages A list of Private_Messages_Message objects.
	 */
	public function get_messages( $args = array() ) {
		global $wpdb;

		$defaults = array(
			'thread' => $this->ID,
			'limit' => 0,
			'offset' => 0,
		);

		$args = wp_parse_args( $args, $defaults );

		$limit = '';
		$thread = '';

		if ( $args['limit'] && 0 != $args['limit'] ) {
			$limit_clause = $wpdb->prepare( ' LIMIT %d, %d', $args['offset'], $args['limit'] );
		}

		$thread = $wpdb->prepare( ' AND comment_post_ID IN(%d)', $args['thread'] );

		$_messages = array();
		$messages = $wpdb->get_results( "SELECT * FROM $wpdb->comments WHERE comment_approved = '1' $thread $limit" );

		if ( ! empty( $messages ) ) {
			foreach ( $messages as $message ) {
				$_messages[ $message->comment_ID ] = new Private_Messages_Message( $message );
			}
		}

		return $_messages;
	}

	/**
	 * Get author ID
	 *
	 * @since 1.0.0
	 *
	 * @return int thread author ID
	 */
	public function get_author_id() {
		$this->author = $this->data->post_author;
		return absint( $this->author );
	}

	/**
	 * Get the author
	 *
	 * @since 1.0.0
	 *
	 * @return object WP_User object.
	 */
	public function get_author() {
		return new WP_User( $this->get_author_id() );
	}

	/**
	 * Get the recipient
	 *
	 * @since 1.0.0
	 *
	 * @return int Thread recipient ID.
	 */
	public function get_recipient_id() {
		$this->recipient = get_post_meta( $this->ID, '_pm_recipient', true );
		return absint( $this->recipient );
	}

	/**
	 * Get the recipient
	 *
	 * @since 1.0.0
	 *
	 * @return object WP_User object.
	 */
	public function get_recipient() {
		return new WP_User( $this->get_recipient_id() );
	}

	/**
	 * Set the recipient
	 *
	 * @since 1.0.0
	 *
	 * @param int $recipient The ID of the recipient.
	 * @return bool
	 */
	public function set_recipient( $recipient ) {
		if ( is_a( $recipient, 'WP_User' ) ) {
			$recipient = $recipient->ID;
		}

		return update_post_meta( $this->ID, '_pm_recipient', absint( $recipient ) );
	}

	/**
	 * Get the star status.
	 *
	 * @since 1.0.0
	 *
	 * @return bool.
	 */
	public function is_starred() {
		return get_user_meta( get_current_user_id(), '_pm_starred_' . $this->ID, true );
	}

	/**
	 * Set the star status
	 *
	 * @since 1.0.0
	 *
	 * @param bool $starred Starred thread status.
	 * @return bool
	 */
	public function set_starred( $starred ) {
		return update_user_meta( get_current_user_id(), '_pm_starred_' . $this->ID, $starred, ! $starred );
	}

	/**
	 * Get unread status
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_unread() {
		return get_user_meta( get_current_user_id(), '_pm_unread_' . $this->ID, true );
	}

	/**
	 * Set unread status for recipient
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function set_unread() {

		// Message sender.
		$sender = get_current_user_id();

		// Receiver.
		$receiver = $this->get_author_id();
		if ( $receiver == $sender ) {
			$receiver = $this->get_recipient_id();
		}

		// Clear count.
		delete_transient( 'pm_unread_count_' . $receiver );

		return update_user_meta( $receiver, '_pm_unread_' . $this->ID, true, false );
	}

	/**
	 * Set read status for the current user
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function set_read() {
		// Get current user.
		$user_id = get_current_user_id();

		// Clear count.
		delete_transient( 'pm_unread_count_' . $user_id );

		// Delete user meta.
		return delete_user_meta( $user_id, '_pm_unread_' . $this->ID );
	}

	/**
	 * Is Deleted
	 * Check if thread is deleted by user
	 *
	 * @since 1.4.0
	 *
	 * @param int $user_id User ID who delete thread.
	 * @return bool
	 */
	public function is_deleted( $user_id = false ) {

		// User not set, use current user.
		$user_id = $user_id ? $user_id : get_current_user_id();

		// Get list of deleted thread ID in user meta.
		$pm_deleted = pm_get_user_deleted_threads( $user_id );

		return in_array( $this->ID, $pm_deleted );
	}

	/**
	 * Delete/Archive a thread
	 *
	 * @since 1.4.0
	 *
	 * @param int $user_id User ID who delete the thread.
	 * @return bool
	 */
	public function delete( $user_id = false ) {

		// Set to read.
		$this->set_read();

		// User not set, use current user.
		$user_id = $user_id ? $user_id : get_current_user_id();

		// Output.
		$deleted = false;

		// Get list of deleted thread ID in user meta.
		$pm_deleted = pm_get_user_deleted_threads( $user_id );

		// Check if thread is not already deleted.
		if ( ! in_array( $this->ID, $pm_deleted ) && in_array( $user_id, array( $this->get_author_id(), $this->get_recipient_id() ) ) ) {

			// Deleted thread.
			$pm_deleted[] = $this->ID;
			$deleted = update_user_meta( $user_id, 'pm_deleted', $pm_deleted );
		}

		return $deleted;
	}

	/**
	 * Delete URL
	 *
	 * @since 1.4.0
	 *
	 * @return string
	 */
	public function delete_url() {
		$url = add_query_arg( array(
			'pm_delete_thread' => $this->ID,
			'_nonce'           => wp_create_nonce( 'pm_delete_thread' ),
		), get_permalink() );
		return esc_url( $url );
	}

	/**
	 * Get the URL for the message thread.
	 *
	 * @since 1.0.0
	 *
	 * @return string $url
	 */
	public function get_url() {
		return esc_url_raw( add_query_arg( array(
			'pm-action' => 'view_message',
			'view_message' => $this->ID,
		), pm_get_permalink( 'dashboard' ) ) );
	}

	/**
	 * Get the URL to report user as spam.
	 *
	 * @since 1.5.0
	 *
	 * @return string $url
	 */
	public function get_report_url() {
		return esc_url_raw( add_query_arg( array(
			'pm-action' => 'report_spam',
			'id'        => $this->ID,
			'_nonce'    => wp_create_nonce( 'pm_report_spam_nonce_' . $this->ID ),
		), pm_get_permalink( 'dashboard' ) ) );
	}

}
