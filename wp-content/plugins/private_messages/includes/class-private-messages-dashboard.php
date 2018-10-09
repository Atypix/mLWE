<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Messaging Dashboard
 *
 * @class Private_Messages_Dashboard
 * @version 1.0.0
 * @author Astoundify
 */
class Private_Messages_Dashboard {

	/**
	 * Notices.
	 *
	 * @var $notice
	 */
	public $notice = false;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Shortcode to display PM Dashboard.
		add_shortcode( 'private_messages', array( $this, 'private_messages' ) );

		// Dashboard init.
		add_action( 'pm_dashboard_init', array( $this, '_set_star_thread' ) );
		add_action( 'pm_dashboard_init', array( $this, '_set_delete_thread' ) );
		add_action( 'pm_dashboard_init', array( $this, '_set_mark_all_read' ) );
		add_action( 'pm_dashboard_init', array( $this, '_set_delete_message' ) );

		// Display Notice In Dashboard.
		add_action( 'pm_dashboard_before', array( $this, 'display_notices' ) );

		// Dashboard Actions.
		add_action( 'pm_dashboard_action_view_messages', array( $this, '_action_view_messages' ) ); // View Messages Init Loaded Here.
		add_action( 'pm_dashboard_action_view_message', array( $this, '_action_view_message' ) ); // View Message Init.
		add_action( 'pm_dashboard_action_new_message', array( $this, '_action_new_message' ) );
		add_action( 'pm_dashboard_action_report_spam', array( $this, '_action_report_spam' ) );

		// Process the form.
		add_action( 'template_redirect', array( $this, 'pm_dashboard_post_message' ) );
	}


	/**
	 * Shortcode To Display PM Dashboard
	 *
	 * @return string
	 */
	public function private_messages( $atts ) {

		// Get action.
		$action = isset( $_GET['pm-action'] ) ? esc_attr( $_GET['pm-action'] ) : 'view_messages';

		// Bail if no action specified.
		if ( ! $action ) {
			return;
		}

		// Start output buffering.
		ob_start();

		// Load scripts.
		$this->enqueue_scripts();

		// Bail early if user not logged in, show login link.
		if ( ! is_user_logged_in() ) {
			echo Private_Messages_Templates::get_template( 'frontend/dashboard-logged-out.php' );
			return ob_get_clean();
		}

		// Action hook.
		do_action( 'pm_dashboard_init' );   // User action loaded here.
		do_action( 'pm_dashboard_before' ); // Notices printed here.
		do_action( 'pm_dashboard_action_' . $action, $atts ); // Current action/view.
		do_action( 'pm_dashboard_after' );

		return ob_get_clean();
	}


	/**
	 * PM Action Init: Star/Unstar a thread.
	 *
	 * @since 1.0.0
	 */
	public function _set_star_thread() {
		if ( ! empty( $_GET['id'] ) ) {

			// Get current user ID.
			$current_user_id = get_current_user_id();

			// Thread Data.
			$thread_id = esc_attr( $_GET['id'] );
			$thread = new Private_Messages_MessageThread( $thread_id );

			if ( $thread->data && ! $thread->is_deleted() && in_array( $current_user_id, array( $thread->get_author_id(), $thread->get_recipient_id() ) ) ) {
				if ( ! empty( $_GET['set_star'] ) ) {
					$thread->set_starred( 'star' === $_GET['set_star'] );
					if ( 'star' === $_GET['set_star'] ) {
						$this->notice = __( 'Message stared.', 'private-messages' );
					} else {
						$this->notice = __( 'Message unstared.', 'private-messages' );
					}
				}
			}
		}
	}

	/**
	 * PM Action Init: Delete/archive thread.
	 *
	 * @since 1.0.0
	 */
	public function _set_delete_thread() {
		if ( isset( $_GET['pm_delete_thread'], $_GET['_nonce'] ) && $_GET['pm_delete_thread'] && wp_verify_nonce( $_GET['_nonce'], 'pm_delete_thread' ) ) {

			// Get current user ID.
			$current_user_id = get_current_user_id();

			// Post ID to delete.
			$thread_id = absint( $_GET['pm_delete_thread'] );
			$thread = new Private_Messages_MessageThread( $thread_id );

			// Thread Data.
			if ( $thread->data && ! $thread->is_deleted() && in_array( $current_user_id, array( $thread->get_author_id(), $thread->get_recipient_id() ) ) ) {

				// Delete thread.
				$deleted = $thread->delete();

				// Success, display deleted notice.
				if ( $deleted ) {
					$this->notice = __( 'Message is sucessfully deleted', 'private-messages' );
				}
			}
		}
	}

	/**
	 * PM Action Init: Mark all threads as read.
	 *
	 * @since 1.0.0
	 */
	public function _set_mark_all_read() {
		if ( isset( $_GET['pm_mark_all_as_read'] ) && wp_verify_nonce( $_GET['pm_mark_all_as_read'], 'pm_mark_all_as_read' ) ) {
			$mark_all_as_read = pm_mark_all_as_read();
			$this->notice = __( 'All conversations have been marked as read.', 'private-messages' );
		}
	}

	/**
	 * View Message Init: Delete single message.
	 *
	 * @since 1.0.0
	 */
	public function _set_delete_message() {
		if ( isset( $_GET['pm_delete_reply'], $_GET['_nonce'] ) && $_GET['pm_delete_reply'] && wp_verify_nonce( $_GET['_nonce'], 'pm_delete_reply' ) ) {

			// Load Comment Object.
			$pm_message = new Private_Messages_Message( absint( $_GET['pm_delete_reply'] ) );
			if ( $pm_message->data ) {

				// Current user delete (archive/hide) the message.
				$deleted = $pm_message->delete();

				// Delete success: Display Notice.
				if ( $deleted ) {
					$this->notice = __( 'Message is sucessfully deleted', 'private-messages' );
				}
			}
		}
	}

	/**
	 * Display Notices In Dashboard.
	 *
	 * @since 1.0.0
	 */
	public function display_notices() {
		if ( ! $this->notice ) {
			return;
		}
		echo Private_Messages_Templates::get_template( 'frontend/dashboard-notice.php', array(
			'notice' => $this->notice,
		) );
	}

	/**
	 * Dashboard action: View Messages
	 * Display thread archive.
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts
	 * @return void
	 */
	public function _action_view_messages( $atts ) {

		// Action for view messages view.
		do_action( 'pm_dashboard_action_view_messages_init' );

		// Extract shortcode option.
		extract( shortcode_atts( array(
			'messages_per_page' => '25',
		), $atts ) );

		// Get current page.
		$current_page = get_query_var( 'page' );
		$current_page = $current_page ? absint( $current_page ) : 1;

		// WP Query Args.
		$args = apply_filters( 'pm_get_dashboard_messages_args', array(
			'post_type'           => 'private-messages',
			'post_status'         => array( 'publish', 'draft' ),
			'posts_per_page'      => $messages_per_page,
			'paged'               => $current_page,
			'orderby'             => 'date',
			'order'               => self::get_order(),
		) );

		// Include/Exclude Posts: We cannot use 'post__in' and 'post__not_in' in the same query.
		$deleted_posts = pm_get_user_deleted_threads();
		$post__in = $this->pm_dashboard_messages_filter_in();

		// If user delete posts.
		if ( $deleted_posts ) {

			// If there are posts included.
			if ( $post__in ) {

				// Remove deleted messages.
				$post__in = array_diff( $post__in, $deleted_posts );

				// Post included still available.
				if ( $post__in ) {
					$args['post__in'] = $post__in;
				} else {
					$args['post__not_in'] = $deleted_posts;
				}
			} else { // No posts included (all posts), implement exclude.
				$args['post__not_in'] = $deleted_posts;
			}
		} else { // User never delete posts, only use include.
			$args['post__in'] = $post__in;
		}

		// Filter WP Query.
		add_filter( 'posts_join', array( $this, 'pm_dashboard_messages_filter_join' ) );
		add_filter( 'posts_where', array( $this, 'pm_dashboard_messages_filter_where' ) );
		add_filter( 'posts_groupby', array( $this, 'pm_dashboard_messages_filter_groupby' ) );

		// Get all posts.
		$messages = new WP_Query( $args );
		$total_posts = $messages->found_posts; // Need to be captured early.

		// Remove Filter WP Query.
		remove_filter( 'posts_join', array( $this, 'pm_dashboard_messages_filter_join' ) );
		remove_filter( 'posts_where', array( $this, 'pm_dashboard_messages_filter_where' ) );
		remove_filter( 'posts_groupby', array( $this, 'pm_dashboard_messages_filter_groupby' ) );

		// User Message Var
		$my_messages = array();

		// Check if available.
		if ( ! empty( $messages->posts ) ) {

			// Loop through all messages and format the data (loop each thread).
			foreach ( $messages->posts as $message ) {

				$thread = new Private_Messages_MessageThread( $message->ID );
				$messages = $thread->get_messages();

				if ( empty( $messages ) ) {
					continue;
				}

				// Get message receiver.
				$current_user_id = get_current_user_id();
				if ( $thread->get_recipient_id() === $current_user_id ) {
					$receiver = $thread->get_author();
				} else {
					$receiver = $thread->get_recipient();
				}

				// Get user information template.
				$user_info = Private_Messages_Templates::get_template( 'frontend/dashboard-messages-recipient.php', array(
					'recipient'    => $receiver,
					'thread'       => $thread,
				) );

				// Get the message overview template.
				$overview = Private_Messages_Templates::get_template( 'frontend/dashboard-messages-overview.php', array(
					'last_message' => array_pop( $messages ),
					'thread'       => $thread,
				) );

				// Get actions.
				$actions = Private_Messages_Templates::get_template( 'frontend/dashboard-messages-actions.php', array(
					'thread'       => $thread,
				) );

				// Add messages data.
				$my_messages[ $message->ID ] = apply_filters( 'pm_my_messages', array(
					'userinfo'     => $user_info,
					'overview'     => $overview,
					'actions'      => $actions,
				), $thread );
			}// End foreach().
		}// End if().

		// Load template.
		echo Private_Messages_Templates::get_template( 'frontend/dashboard-messages.php', array(
			'my_messages' => $my_messages,
		) );

		// Load pagination template if needed.
		$total_pages = ceil( $total_posts / $messages_per_page );
		if ( $total_pages > 1 ) {
			echo Private_Messages_Templates::get_template( 'frontend/dashboard-pagination.php', array(
				'pages'          => range( 1, $total_pages ), // Number of pages.
				'current_page'   => $current_page,
			) );
		}
	}


	/**
	 * Dashboard action: Compose New Message.
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts Shortcode Atts.
	 * @return void
	 */
	public function _action_new_message( $atts ) {
		if ( ! pm_can_compose_from_dashboard() && ! wp_verify_nonce( $_GET['_wpnonce'], 'pm-new-message' ) ) {
			_e( 'You do not have permission to create a new message.', 'private-messages' );
			return;
		}

		$recipient = pm_get_posted_field( 'pm_recipient' );
		$subject   = pm_get_posted_field( 'pm_subject' );
		$message   = pm_get_posted_field( 'pm_message' );

		echo do_shortcode( '[private_message_compose to="' . esc_attr( $recipient ) . '" subject="' . esc_attr( $subject ) . '" message="' . esc_attr( $message ) . '"]' );
	}

	/**
	 * Dashboard action: View Message.
	 * Viewing Single PM Thread.
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts Shortcode Atts.
	 * @return void
	 */
	public function _action_view_message( $atts ) {

		// Action for view messages view.
		do_action( 'pm_dashboard_action_view_message_init' );

		// Get current user ID.
		$current_user_id = get_current_user_id();

		// Get thread data.
		$thread_id = isset( $_GET['view_message'] ) ? absint( $_GET['view_message'] ) : false;
		$thread = new Private_Messages_MessageThread( $thread_id );

		// Check if data is set, current user, and thread not deleted.
		if ( $thread->data && ! $thread->is_deleted() && in_array( $current_user_id, array( $thread->get_author_id(), $thread->get_recipient_id() ) ) ) {

			// Get thread messages and set to read.
			$messages = $thread->get_messages();
			$thread->set_read();

			// Load template to display messages.
			echo Private_Messages_Templates::get_template( 'frontend/dashboard-view-message.php', array(
				'messages' => $messages,
				'thread'   => $thread,
			) );
		} else { // Thread not found.
			echo Private_Messages_Templates::get_template( 'frontend/dashboard-message-not-found.php' );
		}
	}

	/**
	 * Action Report Spam.
	 *
	 * @since 1.5.0
	 */
	public function _action_report_spam() {
		if ( isset( $_GET['_nonce'], $_GET['pm-action'], $_GET['id'] ) && wp_verify_nonce( $_GET['_nonce'], 'pm_report_spam_nonce_' . $_GET['id'] ) ) {

			// Get thread.
			$thread = new Private_Messages_MessageThread( $_GET['id'] );

			// Get current user ID.
			$current_user_id = get_current_user_id();

			// Check thread data.
			if ( ! $thread->data || ! in_array( $current_user_id, array( $thread->get_author_id(), $thread->get_recipient_id() ) ) ) {
				echo Private_Messages_Templates::get_template( 'frontend/dashboard-notice.php', array(
					'notice' => __( 'Thread not found.', 'private-messages' ),
				) );
				echo Private_Messages_Templates::get_template( 'frontend/dashboard-return.php' );
				return;
			}

			// Verify if current user is recipient or author.
			$current_user    = wp_get_current_user();
			$current_user_id = absint( $current_user->ID );
			$author_id       = absint( $thread->get_author_id() );
			$recipient_id    = absint( $thread->get_recipient_id() );
			if ( $current_user_id === $author_id || $current_user_id === $recipient_id ) {

				// Get spammer.
				$spammer_id = $author_id;
				if ( $current_user_id === $author_id ) {
					$spammer_id = $recipient_id;
				}
				$spammer = get_user_by( 'id', $spammer_id );

				// Get administrator email.
				$admin_email = get_option( 'admin_email' );

				// Edit URL:
				$edit_url = add_query_arg( array(
					'post'   => $thread->ID,
					'action' => 'edit',
				), admin_url( 'post.php' ) );

				// Report Message:
				$message = sprintf( __(
					'Hi Admin,' . "\n" .
					"A user reported a private message spam, here's the details:" . "\n\n" .
					'Thread URL: %s' . "\n" .
					'Reported by: %s' . "\n" .
					'Spammer Info: %s' . "\n\n" .
					'Thank you.' . "\n" .
					'Private Messages Plugin.' . "\n"
				, 'private-messages' ), esc_url_raw( $edit_url ), "{$current_user->user_login} ({$current_user_id})", "{$spammer->user_login} ({$spammer_id})" );

				// Send email.
				$sent = wp_mail( sanitize_email( $admin_email ), esc_html__( 'Private Message Spam Report', 'private-messages' ), $message );

				// Sent.
				if ( $sent ) {
					echo Private_Messages_Templates::get_template( 'frontend/dashboard-notice.php', array(
						'notice' => __( 'Spam Report sent. Thank you for your report.', 'private-messages' ),
					) );
					echo Private_Messages_Templates::get_template( 'frontend/dashboard-return.php' );
				} else {
					echo Private_Messages_Templates::get_template( 'frontend/dashboard-notice.php', array(
						'notice' => __( 'Fail to send spam report.', 'private-messages' ),
					) );
					echo Private_Messages_Templates::get_template( 'frontend/dashboard-return.php' );
				}
			} else {
				echo Private_Messages_Templates::get_template( 'frontend/dashboard-notice.php', array(
					'notice' => __( 'Invalid thread.', 'private-messages' ),
				) );
				echo Private_Messages_Templates::get_template( 'frontend/dashboard-return.php' );
			} // End if().
		} else {
			echo Private_Messages_Templates::get_template( 'frontend/dashboard-notice.php', array(
				'notice' => __( 'Fail to verify nonce.', 'private-messages' ),
			) );
			echo Private_Messages_Templates::get_template( 'frontend/dashboard-return.php' );
		} // End if().
	}


	/**
	 * Dashboard Scripts.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'select2' );
		wp_enqueue_script( 'private-messages-frontend' );

		// Because these aren't attached to a hook we need a filter to stop output.
		if ( apply_filters( 'pm_enqueue_frontend_styles', true ) ) {
			wp_enqueue_style( 'select2' );
			wp_enqueue_style( 'private-messages-frontend' );
		}
	}

	/**
	 * WP Query Filter: posts_join
	 *
	 * @since 1.0.0
	 *
	 * @param string $join SQL JOIN Clause.
	 * @return string
	 */
	public function pm_dashboard_messages_filter_join( $join ) {
		global $wpdb;

		if ( ! strstr( $join, $wpdb->postmeta ) ) {
			$join = " LEFT JOIN $wpdb->postmeta ON $wpdb->postmeta.post_id = $wpdb->posts.ID ";
		}
		return $join;

	}

	/**
	 * WP Query Filter: posts_where
	 *
	 * @since 1.0.0
	 *
	 * @param string $where SQL WHERE Clause.
	 * @return string
	 */
	public function pm_dashboard_messages_filter_where( $where ) {
		global $wpdb;

		$current_user = wp_get_current_user();

		if ( $where ) {
			$where .= ' AND ';
		}
		$where .= "(
			$wpdb->posts.post_author = $current_user->ID
			OR (
				$wpdb->postmeta.meta_key = '_pm_recipient'
				AND $wpdb->postmeta.meta_value = $current_user->ID
			)
		)";

		return $where;
	}

	/**
	 * WP Query Filter: posts_groupby
	 *
	 * @since 1.0.0
	 *
	 * @param string $groupby SQL groupby Clause.
	 * @return string
	 */
	public function pm_dashboard_messages_filter_groupby( $groupby ) {
		global $wpdb;
		$groupby = "$wpdb->posts.ID";
		return $groupby;
	}

	/**
	 * WP Query Args: post__in
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function pm_dashboard_messages_filter_in() {
		$_in = array();

		if ( 'all' != self::get_showing() ) {
			global $wpdb;

			$_in = array( -1 );

			$current_user = wp_get_current_user();
			$like = 'starred' == self::get_showing() ? '_pm_starred_' : '_pm_unread_';

			$search = $wpdb->get_results( $wpdb->prepare( "
				SELECT meta_key FROM $wpdb->usermeta WHERE user_id = '%d' AND meta_key LIKE '%%%s%%' AND meta_value = '1'",
				$current_user->ID,
				$like
			) );

			if ( $search ) {
				$_in = array();

				foreach ( $search as $found ) {
					$_in[] = str_replace( $like, '', $found->meta_key );
				}
			}
		}
		return $_in;
	}

	/**
	 * Process the Form
	 * This will create new thread or create new reply based on the form action.
	 *
	 * @since 1.0.0
	 */
	public function pm_dashboard_post_message() {

		// Check nonce.
		if ( ! isset( $_POST['pm_message_nonce'] ) || ! wp_verify_nonce( $_POST['pm_message_nonce'], 'pm_message_nonce' ) ) {
			return;
		}

		// Process form submission.
		try {

			// Create Reply.
			if ( isset( $_GET['pm-action'] ) && 'view_message' == $_GET['pm-action'] ) {

				// Var.
				$thread_id = absint( $_GET['view_message'] );
				$message = isset( $_POST['pm_message'] ) && '' != $_POST['pm_message'] ? $_POST['pm_message'] : false;
				if ( ! $message ) {
					throw new Exception( __( 'All fields are required.', 'private-messages' ) );
				}

				// Create reply.
				$new_message = self::compose_reply( $thread_id, $message );
				if ( ! $new_message ) {
					throw new Exception( __( 'Message could not be created.', 'private-messages' ) );
				}
			} elseif ( isset( $_GET['pm-action'] ) && 'new_message' == $_GET['pm-action'] ) { // Create new thread.

				$subject = pm_get_posted_field( 'pm_subject' );
				$message = pm_get_posted_field( 'pm_message' );
				$recipient = pm_get_posted_field( 'pm_recipient' );

				if ( ! $subject || ! $message || ! $recipient ) {
					throw new Exception( __( 'All fields are required.', 'private-messages' ) );
				}

				// Create new thread.
				$thread_id = self::compose_message( array(
					'recipient' => $recipient,
					'subject'   => $subject,
					'message'   => $message,
				) );

				if ( $thread_id ) {
					$url = add_query_arg( array(
						'pm-action'    => 'view_message',
						'view_message' => $thread_id,
						'new_message'  => false,
					), pm_get_permalink( 'dashboard' ) );
					wp_redirect( esc_url_raw( $url ) );
					exit();
				} else {
					throw new Exception( __( 'Message thread could not be created.', 'private-messages' ) );
				}
			}// End if().
		} catch ( Exception $e ) {
			$this->notice = $e->getMessage();
		}// End try().
	}

	/**
	 * Create New Thread
	 * All fields need to be sanitize and checked before using this function for custom error message
	 *
	 * @since 1.4.0
	 *
	 * @param $args array Compose message args.
	 * @return mixed Thread Post ID if success, false if fail.
	 */
	public static function compose_message( $args ) {
		$defaults = array(
			'from'           => get_current_user_id(),
			'recipient'      => '',
			'subject'        => '',
			'message'        => '',
		);
		$args = wp_parse_args( $args, $defaults );

		// Check required datas.
		if ( ! $args['from'] || ! $args['recipient'] || ! $args['subject'] || ! $args['message'] ) {
			return false;
		}

		// Create PM entry.
		$thread_id = wp_insert_post( array(
			'post_author'    => $args['from'],
			'post_title'     => wp_strip_all_tags( $args['subject'] ),
			'post_content'   => '',
			'post_status'    => 'publish',
			'post_type'      => 'private-messages',
			'comment_status' => 'open',
		) );

		// Successfully create PM entry.
		if ( $thread_id ) {
			$thread = new Private_Messages_MessageThread( $thread_id );
			$thread->set_recipient( $args['recipient'] );
			$thread->set_unread();

			// Create message.
			$new_message = self::compose_reply( $thread_id, $args['message'] );
			if ( $new_message ) {
				return $thread_id;
			}
		}
		return false;
	}

	/**
	 * Send Message Reply
	 * All fields need to be sanitize and checked before using this function for custom error message.
	 *
	 * @since 1.4.0
	 *
	 * @param int    $thread_id Required. PM Entry Post ID.
	 * @param string $message   Required. Message Content/Comment Text.
	 * @param int    $from      Optional. User ID who send message. Will use current user if empty.
	 * @return mixed Message/Comment ID if success, false if fail.
	 */
	public static function compose_reply( $thread_id, $message, $from = null ) {

		// From.
		$user = get_user_by( 'ID', $from ? $from : get_current_user_id() );

		// WooCommerce compat.
		remove_filter( 'preprocess_comment', array( 'WC_Comments', 'check_comment_rating' ), 0 );

		// Create new message.
		$new_message = wp_new_comment( array(
			'comment_post_ID'      => $thread_id,
			'comment_author'       => pm_get_user_display_name( $user ),
			'comment_author_email' => $user->user_email,
			'comment_author_url'   => '',
			'comment_content'      => wp_kses_post( $message ),
			'comment_type'         => 'private-messages',
			'comment_parent'       => 0,
			'comment_date'         => current_time( 'mysql' ),
			'comment_approved'     => 1,
			'user_id'              => $user->ID,
		) );

		// WooCommerce compat.
		add_filter( 'preprocess_comment', array( 'WC_Comments', 'check_comment_rating' ), 0 );

		// Successfully create new message.
		if ( $new_message ) {

			// Create Files/ Attachments.
			if ( pm_can_upload_attachments() ) {
				if ( isset( $_FILES['pm_attachments'] ) && $_FILES['pm_attachments'] ) {
					Private_Messages_Files::create_files( $_FILES['pm_attachments'], $thread_id, $new_message );
				}
			}

			// Always approve.
			wp_set_comment_status( $new_message, 'approve' );

			// Get Message.
			$message = pm_get_message( $new_message );

			// Get Thread & Set Unread.
			$thread = new Private_Messages_MessageThread( $thread_id );
			$thread->set_unread();

			// New Message Hook.
			do_action( 'pm_new_message', $message, $thread );

			// Return Thread ID.
			return $new_message;
		}
		return false;
	}

	/**
	 * Get the current filter view.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_showing() {
		$showing = isset( $_REQUEST['pm_showing'] ) ? esc_attr( $_REQUEST['pm_showing'] ) : 'all';

		return $showing;
	}

	/**
	 * Get the current order.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_order() {
		$order = isset( $_REQUEST['pm_order'] ) ? esc_attr( $_REQUEST['pm_order'] ) : 'desc';

		return $order;
	}

	/**
	 * Get the title for the messages being shown.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_title() {
		$showing = self::get_showing();

		if ( ! $showing || 'all' == $showing ) {
			return __( 'All Messages', 'private-messages' );
		}

		if ( 'starred' == $showing ) {
			return __( 'Starred Messages', 'private-messages' );
		} elseif ( 'unread' == $showing ) {
			return __( 'Unread Messages', 'private-messages' );
		}

		return false;
	}

	/**
	 * Get Mark As Read Link
	 *
	 * @since 1.4.0
	 *
	 * @return string
	 */
	public static function get_mark_as_read_link() {
		$url = add_query_arg( array(
			'pm_mark_all_as_read' => wp_create_nonce( 'pm_mark_all_as_read' ),
		), get_permalink() );

		return '<a href="' . esc_url( $url ) . '" class="pm-mark-all-as-read">' . __( 'Mark all as read', 'private-messages' ) . '</a>';
	}

}

// Load class.
new Private_Messages_Dashboard();
