<?php
/**
 * Template tags.
 *
 * @since 1.0.0
 */

if ( ! function_exists( 'pm_get_avatar' ) ) :

	/**
	 * Get an avatar for a user.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $user_id
	 * @param int    $size
	 * @param string $default
	 * @param string $alt
	 * @param array  $args
	 * @return false|string `<img>` tag for the user's avatar. False on failure.
	 */
	function pm_get_avatar( $user_id, $size = 96, $default = '', $alt = '', $args = array() ) {
		$size = apply_filters( 'pm_gravatar_size_dasbhoard', $size );
		$default = apply_filters( 'pm_gravatar_default_dashboard', $default );
		$alt = apply_filters( 'pm_gravatar_alt_dashboard', $alt );
		$args = apply_filters( 'pm_gravatar_args_dashboard', $args );

		return get_avatar( $user_id, $size, $default, $alt, $args );
	}

endif;

/**
 * Get User Display Name
 *
 * @since 1.8.0
 *
 * @param WP_User $user User Object.
 * @return string
 */
function pm_get_user_display_name( $user ) {
	return apply_filters( 'pm_user_display_name', $user->display_name, $user );
}

/**
 * Get the title for the messages being shown.
 *
 * @since 1.0.0
 *
 * @return string
 */
function pm_get_messages_title() {
	return Private_Messages_Dashboard::get_title();
}

/**
 * Get the title for the messages being shown.
 *
 * @since 1.4.0
 *
 * @return string
 */
function pm_get_mark_all_as_read_link() {
	return Private_Messages_Dashboard::get_mark_as_read_link();
}

/**
 * Delete all user read messages
 * This will make all user messages set as read
 *
 * @since 1.4.0
 */
function pm_mark_all_as_read( $user_id = false ) {
	if ( ! is_user_logged_in() ) {
		return false;
	}

	// Get user id.
	$user_id = $user_id ? $user_id : get_current_user_id();

	// Delete count.
	delete_transient( 'pm_unread_count_' . $user_id );

	// Get all read thread meta keys rows.
	global $wpdb;
	$query = $wpdb->prepare( "
		SELECT meta_key 
		FROM $wpdb->usermeta 
		WHERE meta_key LIKE '%%_pm_unread_%%' 
		AND user_id = '%d' 
	", $user_id );
	$unread_datas = $wpdb->get_results( $query, 'ARRAY_A' );
	if ( ! $unread_datas ) {
		return false;
	}

	// Delete all user meta.
	foreach ( $unread_datas as $rows ) {
		foreach ( $rows as $data ) {
			// Just to make sure.
			if ( strpos( $data, '_pm_unread_' ) !== false ) {
				delete_user_meta( $user_id, $data );
			}
		}
	}
	return true;
}

/**
 * Get the order for the messages being shown.
 *
 * @since 1.0.0
 *
 * @return string
 */
function pm_get_messages_order() {
	return Private_Messages_Dashboard::get_order();
}

/**
 * Get the key for the messages being shown.
 *
 * @since 1.0.0
 *
 * @return string
 */
function pm_get_messages_showing() {
	return Private_Messages_Dashboard::get_showing();
}

/**
 * Queries messages and returns them.
 *
 * @since 1.0.0
 *
 * @param int $pm_id PM thread ID.
 * @return array
 */
function pm_get_messages( $pm_id ) {
	$thread = new Private_Messages_Message_Thread( $pm_id );
	return $thread->get_messages();
}

/**
 * Get the number of unread messages for a specific user.
 *
 * @since 1.0.0
 *
 * @param int $user_id
 * @return int $unread_count
 */
function pm_get_unread_count( $user_id ) {
	global $wpdb;

	$count = get_transient( 'pm_unread_count_' . $user_id );

	if ( false == $count ) {
		$count = $wpdb->get_var( $wpdb->prepare( "
			SELECT COUNT(*) FROM $wpdb->usermeta WHERE user_id = '%d' AND meta_key LIKE '%%_pm_unread_%%' AND meta_value = '1'",
			$user_id
		) );

		set_transient( 'pm_unread_count_' . $user_id, $count );
	}

	return $count;
}

/**
 * Get a specific message for any thread.
 *
 * @since 1.0.0
 *
 * @param int $mid Message (comment) ID
 * @return object Private_Messages_Message
 */
function pm_get_message( $m_id ) {
	return new Private_Messages_Message( $m_id );
}


/**
 * Get Message Attachments HTML
 *
 * @since 1.3.0
 *
 * @param int|object $m_id Message (comment) ID or comment object
 * @return string
 */
function pm_message_attachments_html( $message ) {
	$attachments = $message->get_attachments();
	if ( $attachments ) {
?>
<div class="pm-attachments">
	<ul>
	<?php foreach ( $attachments as $file ) : ?>
		<li><a href="<?php echo esc_url( $file['url'] )?>" download><?php echo $file['name']; ?></a> (<?php echo size_format( $file['size'] ); ?>)</li>
	<?php endforeach; ?>
	</ul>
</div><!-- .pm-attachments -->
<?php
	}
}

/**
 * Create a link to compose a new message
 *
 * @since 1.0.0
 */
function pm_get_new_message_url( $recipient = false, $subject = false, $message = false ) {
	$args = array(
		'pm-action'    => 'new_message',
		'pm_recipient' => $recipient,
		'pm_subject'   => $subject,
		'pm_message'   => $message,
	);
	$args = apply_filters( 'pm_new_message_url_args', $args );

	$url = add_query_arg( $args, pm_get_permalink( 'dashboard' ) );

	if ( ! pm_can_compose_from_dashboard() ) {
		$url = wp_nonce_url( $url, 'pm-new-message' );
	}

	return esc_url( apply_filters( 'pm_new_message_url', $url ) );
}

/**
 * Get the value of a field that has data associated with it.
 *
 * @since 1.0.0
 *
 * @return mixed
 */
function pm_get_posted_field( $field ) {
	if ( ! isset( $_REQUEST[ $field ] ) ) {
		return false;
	}

	if ( '' == $_REQUEST[ $field ] ) {
		return false;
	}

	$field = $_REQUEST[ $field ];

	if ( is_array( $field ) ) {
		$field = array_map( pm_sanitize_field( $field ), $field );
	} else {
		$field = pm_sanitize_field( $field );
	}

	return $field;
}

/**
 * Sanitize a posted value
 *
 * @since 1.0.0
 *
 * @return mixed
 */
function pm_sanitize_field( $value ) {
	return $value;
}

/**
 * Get the permalink of a page if set
 *
 * @since 1.0.0
 *
 * @param  string $page e.g. private_messages
 * @return string|bool
 */
function pm_get_permalink( $page ) {
	$page_id = pm_get_option( 'pm_' . $page . '_page_id', false );

	if ( $page_id ) {
		return get_permalink( $page_id );
	} else {
		return false;
	}
}


/**
 * Get an option
 *
 * Looks to see if the specified setting exists, returns default if not
 *
 * @since 1.0.0
 *
 * @return mixed
 */
function pm_get_option( $key, $default = false ) {
	// Get db.
	$out = $default;
	$data = get_option( 'pm_settings' );

	// DB not set/invalid format, return default.
	if ( ! is_array( $data ) ) {
		$out = $default;
	} elseif ( isset( $data[ $key ] ) ) {
		$out = $data[ $key ];
	}

	return apply_filters( 'pm_get_option_' . $key, $out, $key, $default );
}

/**
 * Update Option.
 *
 * @since 1.7.0
 *
 * @param string       $name Option name.
 * @param string|array $data Option value.
 * @return bool
 */
function pm_update_option( $key, $value ) {
	// Sanitize Key.
	$key = sanitize_key( $key );
	if ( ! $key ) {
		return;
	}

	// Filter to sanitize value.
	$value = apply_filters( "pm_update_option_{$key}", $value );

	// Add to option data.
	$data = get_option( 'pm_settings' );
	$data[ $key ] = $value;

	// Update it.
	return update_option( 'pm_settings',  $data );
}

/**
 * Retrieve a list of all published pages
 *
 * On large sites this can be expensive, so only load if on the settings page or $force is set to true
 *
 * @since 1.0.0
 *
 * @param bool $force Force the pages to be loaded even if not on settings
 * @return array $pages_options An array of the pages
 */
function pm_get_pages( $force = false ) {
	$pages_options = array(
		'' => '',
	); // Blank option.

	if ( ( ! isset( $_GET['page'] ) || 'pm-settings' != $_GET['page'] ) && ! $force ) {
		return $pages_options;
	}

	$pages = get_pages();

	if ( $pages ) {
		foreach ( $pages as $page ) {
			$pages_options[ $page->ID ] = $page->post_title;
		}
	}

	return $pages_options;
}

/**
 * Whether or not users can compose from the dashboard.
 *
 * @since 1.0.0
 *
 * @return bool
 */
function pm_can_compose_from_dashboard() {
	return pm_get_option( 'pm_allow_compose_from_dashboard', true );
}

/**
 * Whether or not users can send attachment in message
 *
 * @since 1.4.0
 *
 * @return bool
 */
function pm_can_upload_attachments() {
	return pm_get_option( 'pm_allow_attachments', true ) ? true : false;
}

/**
 * Editor
 *
 * @since 1.4.0
 *
 * @param string $message Message content.
 * @param string $name    Field name.
 */
function pm_message_editor( $message = '', $name = 'pm_message' ) {
	wp_editor( wp_kses_post( $message ), $name, apply_filters( 'pm_editor_settings', array(
		'media_buttons' => false,
		'textarea_rows' => 8,
		'quicktags'     => false,
		'tinymce'       => array(
			'plugins'                       => 'lists,paste,link,tabfocus,wordpress',
			'paste_as_text'                 => true,
			'paste_auto_cleanup_on_paste'   => true,
			'paste_remove_spans'            => true,
			'paste_remove_styles'           => true,
			'paste_remove_styles_if_webkit' => true,
			'paste_strip_class_attributes'  => true,
			'toolbar1'                      => 'bold,italic,bullist,numlist,link,unlink,undo,redo',
			'toolbar2'                      => '',
			'toolbar3'                      => '',
			'toolbar4'                      => '',
		),
	) ) );
}

/**
 * Register original TinyMCE "Link" Plugin.
 *
 * @since 1.8.0
 *
 * @param array $plugins TinyMCE Plugins
 * @return array
 */
function pm_register_mce_plugins( $plugins ) {
	if ( ! is_admin() && ! isset( $plugins['link'] ) ) {
		$plugins['link'] = private_messages()->plugin_url . '/assets/js/mce-plugins/link.js';
	}
	return $plugins;
}
add_filter( 'mce_external_plugins', 'pm_register_mce_plugins' );

/**
 * Get User Deleted Threads.
 *
 * @since 1.4.0
 * @param int $user_id Optional will use current user if not set.
 * @return array Ids of thread.
 */
function pm_get_user_deleted_threads( $user_id = false ) {

	/* Use current user if user not set */
	$user_id = $user_id ? $user_id : get_current_user_id();

	/* Get deleted thread */
	$pm_deleted = get_user_meta( $user_id,  'pm_deleted', true );
	$pm_deleted = is_array( $pm_deleted ) ? $pm_deleted : array();

	/* Return */
	return $pm_deleted;
}

/*
 * Helper function to get pagination item URL.
 *
 * @since 1.4.0
 * @param int $page Page item.
 * @return string URL of pagination item.
 */
function pm_get_pagination_item_url( $page ) {

	// Pretty permalink.
	if ( get_option( 'permalink_structure' ) ) {
		$url = user_trailingslashit( trailingslashit( get_permalink() ) . $page );
	} else { // Ugly permalink.
		$url = add_query_arg( 'page', $page, get_permalink() );
	}

	// Add current query args.
	if ( isset( $_GET['pm_showing'] ) ) {
		$url = add_query_arg( 'pm_showing', $_GET['pm_showing'], $url );
	}
	if ( isset( $_GET['pm_order'] ) ) {
		$url = add_query_arg( 'pm_order', $_GET['pm_order'], $url );
	}
	return esc_url( $url );
}

/**
 * PM Default New Message Notification Subject
 *
 * @since 1.8.1
 */
function pm_default_new_message_notification_subject() {
	return '{site_name} - Message from {sender_name}';
}

/**
 * PM Default New Message Notification Message
 *
 * @since 1.8.1
 */
function pm_default_new_message_notification_message() {
	return 'Hello {recipient_name},

{sender_name} sent you a message.

Message:

{message}

To respond to this message please visit this URL {link_to_message}

Note: Replying to this email will not send the recipient the message.

Tip: We highly recommend responding promptly. This makes for a faster, smoother transaction and keeps the conversation moving along. No one likes awkward silences.

Cheers, 
{site_name}';
}
