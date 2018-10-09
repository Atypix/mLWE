<?php
/**
 * Message
 * Single Message/Comment Object.
 *
 * @since 1.0.0
 */
class Private_Messages_Message {

	/**
	 * ID
	 *
	 * @var $ID
	 */
	public $ID;

	/**
	 * Data.
	 *
	 * @var $data WP_Comment
	 */
	public $data;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object|int $message Comment object or ID.
	 */
	public function __construct( $message = false ) {
		if ( is_int( $message ) ) {
			$this->ID = $message;
			$this->data = get_comment( $this->ID );
		} else {
			$this->ID = $message->comment_ID;
			$this->data = $message;
		}
	}

	/**
	 * Get the message creation date
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_date() {
		return date_i18n( get_option( 'date_format' ), strtotime( $this->data->comment_date ) );
	}

	/**
	 * Get the message creation time
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_time() {
		return date_i18n( get_option( 'time_format' ), strtotime( $this->data->comment_date ) );
	}

	/**
	 * Get the message content
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_content( $limit = false ) {
		$content = $this->data->comment_content;

		if ( $limit ) {
			$content = wp_trim_words( $content, $limit );
		}

		$content = wp_unslash( $content ); // Back compat. In prev version accidentally slashes all message content.
		$content = wp_kses_post( $content ); // Make sure it's sanitized.

		return apply_filters( 'get_private_message_text', $content );
	}

	/**
	 * Get the message author ID
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_author_id() {
		return $this->data->user_id;
	}

	/**
	 * Get the message author
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_author() {
		return new WP_User( $this->get_author_id() );
	}

	/**
	 * Get the message author display name
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_author_name() {
		return $this->data->comment_author;
	}

	/**
	 * Get Attachments
	 *
	 * @since 1.3.0
	 *
	 * @return array List of attachments.
	 */
	public function get_attachments() {
		$attachments = get_comment_meta( $this->ID, 'pm_attachments', true );
		$attachments = is_array( $attachments ) ? $attachments : array();
		$files = array();
		foreach ( $attachments as $file ) {
			if ( isset( $file['name'], $file['file'], $file['url'], $file['type'], $file['size'], $file['extension'] ) ) {
				$files[] = $file;
			}
		}
		return apply_filters( 'pm_get_attachments', $files, $this->ID );
	}

	/**
	 * Archive a Reply.
	 * This method will archive/hide a reply from a user.
	 *
	 * @since 1.4.0
	 *
	 * @param int $user_id The ID of user who delete the comment.
	 * @return bool
	 */
	public function delete( $user_id = false ) {

		// User not set, use current user.
		$user_id = $user_id ? $user_id : get_current_user_id();

		// Output.
		$deleted = false;

		// Comment deleted/archived by.
		$deleted_by = get_comment_meta( $this->ID, 'pm_deleted_by', false );

		// Get thread object.
		$thread = new Private_Messages_MessageThread( $this->data->comment_post_ID );

		// Check if already deleted + Only sender and recipient can delete/archive comment.
		if ( ! in_array( $user_id, $deleted_by ) && in_array( $user_id, array( $thread->get_author_id(), $thread->get_recipient_id() ) ) ) {

			// Delete it.
			$deleted = add_comment_meta( $this->ID, 'pm_deleted_by', $user_id, false );
		}
		return $deleted;
	}

	/**
	 * Reply Is Deleted (Archived/Hidden) by a user
	 *
	 * @since 1.4.0
	 *
	 * @param int $user_id User ID who delete message. Optional, use current user if not set.
	 * @return bool
	 */
	public function is_deleted( $user_id = false ) {

		// User not set, use current user.
		$user_id = $user_id ? $user_id : get_current_user_id();

		// Get array of user who deleted the reply.
		$deleted_by = get_comment_meta( $this->ID, 'pm_deleted_by', false );

		// True if user is in the deleted by array.
		return in_array( $user_id, $deleted_by );
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
			'pm-action'       => 'view_message',
			'view_message'    => $this->data->comment_post_ID,
			'pm_delete_reply' => $this->ID,
			'_nonce'          => wp_create_nonce( 'pm_delete_reply' ),
		), get_permalink() );
		return esc_url( $url );
	}

}
