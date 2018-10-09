<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Handles PM Edit Screen.
 *
 * @since 1.0.0
 *
 * @category Class
 * @author   Astoundify
 */
class Private_Messages_Admin_Edit {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Change title input placeholder.
		add_filter( 'enter_title_here', array( $this, 'change_title_to_subject' ), 10, 2 );

		// Add multipart form in edit post screen to enable file upload input.
		add_action( 'post_edit_form_tag', array( $this, 'post_edit_form_tag' ) );

		// Add meta boxes.
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		// Save PM meta boxes data.
		add_action( 'save_post', array( $this, 'save_meta' ), 10, 2 );

		// Admin scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 5 );

		// Get users.
		add_action( 'wp_ajax_pm_recipients_list', array( $this, 'recipients_list' ) );
	}

	/**
	 * Change the placeholder for the post type title.
	 *
	 * @since 1.0.0
	 *
	 * @param string $placeholder Input placeholder.
	 * @param object $post        WP Post.
	 * @return string
	 */
	public function change_title_to_subject( $placeholder, $post ) {
		if ( 'private-messages' == get_post_type( $post ) ) {
			$placeholder = __( 'Subject', 'private-messages' );
		}
		return $placeholder;
	}

	/**
	 * Allow File Uploads in PM Edit Screen
	 *
	 * @since 1.3.0
	 *
	 * @param object $post WP Post.
	 * @return void
	 */
	public function post_edit_form_tag( $post ) {
		if ( 'private-messages' == $post->post_type ) {
			printf( ' enctype="multipart/form-data" encoding="multipart/form-data"' );
		}
	}

	/**
	 * Add Meta Boxes.
	 *
	 * @since 1.0.0
	 */
	public function add_meta_boxes() {
		global $pagenow;

		// Remove comments meta box.
		remove_meta_box( 'commentsdiv', 'private-messages', 'normal' ); // Comment meta box.
		remove_meta_box( 'commentstatusdiv', 'private-messages', 'normal' ); // Discussion meta box.

		// PM Recipient Meta Box.
		add_meta_box(
			$id       = 'pm_recipient',
			$title    = __( 'Recipient', 'private-messages' ),
			$callback = array( $this, 'meta_box_pm_recipient' ),
			$screen   = 'private-messages',
			$context  = 'side',
			$priority = 'high'
		);

		// PM Reply Meta Box.
		add_meta_box(
			$id       = 'pm_reply',
			$title    = 'post-new.php' === $pagenow ? __( 'Compose Message', 'private-messages' ) : __( 'Reply', 'private-messages' ),
			$callback = array( $this, 'meta_box_pm_reply' ),
			$screen   = 'private-messages',
			$context  = 'normal',
			$priority = 'default'
		);

		// PM Messages Meta Box: Only in new post.
		if ( $pagenow !== 'post-new.php' ) {

			add_meta_box(
				$id       = 'pm_messages',
				$title    = __( 'Messages', 'private-messages' ),
				$callback = array( $this, 'meta_box_pm_messages' ),
				$screen   = 'private-messages',
				$context  = 'normal',
				$priority = 'default'
			);
		}
	}

	/**
	 * Recipient Meta Box.
	 *
	 * @since 1.0.0
	 *
	 * @param object $post WP Post.
	 */
	public function meta_box_pm_recipient( $post ) {
		echo Private_Messages_Templates::get_template( 'admin/recipient.php', array(
			'post'      => $post,
			'recipient' => get_post_meta( $post->ID, '_pm_recipient', true ),
		) );
	}

	/**
	 * PM Messages Meta Box.
	 *
	 * @since 1.0.0
	 *
	 * @param object $post WP Post.
	 */
	public function meta_box_pm_messages( $post ) {
		echo Private_Messages_Templates::get_template( 'admin/messages.php', array(
			'post'     => $post,
			'messages' => get_comments( array(
				'post_id'  => $post->ID,
			) ),
		) );
	}

	/**
	 * PM Reply Meta Box.
	 *
	 * @since 1.0.0
	 *
	 * @param object $post WP Post.
	 */
	public function meta_box_pm_reply( $post ) {
		global $pagenow;

		echo Private_Messages_Templates::get_template( 'admin/reply.php', array(
			'post'     => $post,
			'pagenow'  => $pagenow,
			'messages' => get_comments(),
		) );
	}

	/**
	 * Save Post Meta
	 *
	 * @since 1.0.0
	 *
	 * @param int    $post_id Post ID.
	 * @param object $post    Post object.
	 * @return void
	 */
	public function save_meta( $post_id, $post ) {
		global $typenow;

		// Bail.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		if ( $typenow !== 'private-messages' ) {
			return $post_id;
		}
		if ( ! isset( $_POST['pm_message_nonce'] ) || ! wp_verify_nonce( $_POST['pm_message_nonce'], 'pm_message_nonce' ) ) {
			return $post_id;
		}

		// Set status and comment status.
		remove_action( 'save_post', array( $this, 'save_meta' ) ); // Prevent infinite loop.
		wp_update_post(array(
			'ID'             => $post_id,
			'post_status'    => 'publish',
			'comment_status' => 'open',
		));
		add_action( 'save_post', array( $this, 'save_meta' ), 10, 2 ); // Re-add filter.

		// Get thread.
		$thread = new Private_Messages_MessageThread( $post_id );

		// Set recipient.
		if ( ! empty( $_POST['pm_recipient'] ) ) {
			$thread->set_recipient( esc_attr( $_POST['pm_recipient'] ) );
		}

		// Set PM Message.
		if ( ! empty( $_POST['pm_message'] ) ) {
			$current_user = wp_get_current_user();

			// Add new comment.
			remove_filter( 'preprocess_comment', array( 'WC_Comments', 'check_comment_rating' ), 0 ); // WooCommerce compat.
			$new_message = wp_new_comment( array(
				'comment_post_ID'      => $post_id,
				'comment_author'       => pm_get_user_display_name( $current_user ),
				'comment_author_url'   => '',
				'comment_author_email' => $current_user->user_email,
				'comment_content'      => wp_kses_post( $_POST['pm_message'] ),
				'comment_type'         => 'private-messages',
				'comment_parent'       => 0,
				'user_id'              => $current_user->ID,
				'comment_date'         => current_time( 'mysql' ),
				'comment_approved'     => 1,
			) );
			remove_filter( 'preprocess_comment', array( 'WC_Comments', 'check_comment_rating' ), 0 ); // WooCommerce compat.

			// Comment added sucessfully.
			if ( $new_message ) {

				// Set PM as unread.
				$thread->set_unread();

				// Always approve comment.
				wp_set_comment_status( $new_message, 'approve' );

				$new_message = pm_get_message( $new_message );

				/* Create Files/ Attachments */
				if ( pm_get_option( 'pm_allow_attachments' , true ) ) {
					if ( isset( $_FILES['pm_attachments'] ) && $_FILES['pm_attachments'] ) {
						Private_Messages_Files::create_files( $_FILES['pm_attachments'], $post_id, $new_message );
					}
				}

				do_action( 'pm_new_message', $new_message, $thread );
			} // End if().
		} // End if().

	}

	/**
	 * Enqueue script to post edit screen function.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook_suffix ) {

		// Only load scripts in PM edit post screen.
		global $post_type;
		if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) || 'private-messages' !== $post_type ) {
			return;
		}

		// Get assets URL.
		$pm  = private_messages();
		$url = $pm->plugin_url;
		$ver = $pm->version;

		// JS.
		wp_register_script( 'select2', $url . '/assets/js/select2.min.js', array( 'jquery' ), $ver, true );
		wp_enqueue_script( 'private-messages-admin-edit', $url . '/assets/js/admin-edit.js', array( 'jquery', 'select2' ), $ver, true );
		wp_localize_script( 'private-messages-admin-edit', 'PrivateMessages', array(
			'empty_message' => __( "You can't send and empty message.", 'private-messages' ),
			'show_avatars' => get_option( 'show_avatars', true ),
		) );

		// CSS.
		wp_register_style( 'select2', $url . '/assets/css/select2.min.css', null, $ver );
		wp_enqueue_style( 'private-messages-admin-edit', $url . '/assets/css/admin-edit.css', array( 'select2' ), $ver );
	}

	/**
	 * Create a list of recipients based on a search parameter.
	 *
	 * @since 1.0.0
	 *
	 * @return array $recipients
	 */
	public function recipients_list() {
		$output = array(
			'total_count' => 0,
			'recipients' => array(),
		);

		if ( ! empty( $_GET['q'] ) ) {
			$q = esc_attr( $_GET['q'] );

			$users_found = $this->search_for_users( $q );
			$current_user_id = get_current_user_id();

			if ( ! empty( $users_found ) ) {
				foreach ( $users_found as $user ) {
					if ( $current_user_id !== $user->ID ) {
						$output['recipients'][] = array(
							'id'         => $user->ID,
							'avatar_url' => get_avatar_url( $user->ID, array(
								'size' => 45,
							) ),
							'name'       => pm_get_user_display_name( $user ),
							'username'   => $user->user_login,
							'user'       => $user,
						);
					}
				}
			}
		}

		echo wp_json_encode( $output );
		die;
	}

	/**
	 * Search for users based on a query string.
	 *
	 * Currently searches:
	 *
	 * @todo Use a direct query to avoid 3 separate queries.
	 *
	 * @since 1.0.0
	 *
	 * @param string $q
	 * @return array $users_foudn
	 */
	public function search_for_users( $q ) {
		// search meta first
		$meta_search = new WP_User_Query( apply_filters( 'private_messages_recipient_list_search_user_meta', array(
			'fields' => 'ID',
			'meta_query' => array(
				'relation'    => 'OR',
				array(
					'key'     => 'nickname',
					'value'   => $q,
					'compare' => 'LIKE',
				),
				array(
					'key'     => 'first_name',
					'value'   => $q,
					'compare' => 'LIKE',
				),
				array(
					'key' => 'last_name',
					'value' => $q,
					'compare' => 'LIKE',
				)
			),
			'exclude' => array( get_current_user_id() ),
		) ) );

		// search main table
		$main_search = new WP_User_Query( apply_filters( 'private_messages_recipient_list_search_users', array(
			'fields' => 'ID',
			'search' => '*' . $q . '*',
			'search_columns' => array(
				'user_login',
				'user_nicename',
				'user_email',
				'user_url',
			),
			'exclude' => array( get_current_user_id() ),
		) ) );

		$users = new WP_User_Query( apply_filters( 'private_messages_recipient_list_search', array(
			'include' => array_merge( $main_search->get_results(), $meta_search->get_results() ),
		) ) );

		$users_found = $users->get_results();

		return $users_found;
	}

}

new Private_Messages_Admin_Edit();
