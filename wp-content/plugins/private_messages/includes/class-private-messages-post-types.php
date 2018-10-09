<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Post Type
 *
 * Creates the post type for Private Messages.
 *
 * @since 1.0.0
 * @category Class
 * @author Astoundify
 */
class Private_Messages_Post_Types {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Register Post Type.
		add_action( 'init', array( $this, 'register_post_types' ) );

		// On delete PM. Clean up data.
		add_action( 'delete_post', array( $this, 'delete_post' ) );
	}

	/**
	 * Register Post Types.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_post_types() {

		// Vars.
		$admin_capability = 'moderate_comments';
		$singular         = __( 'Private Message', 'private-messages' );
		$plural           = __( 'Private Messages', 'private-messages' );

		$args = array(
			'labels'              => array(
				'name'                => $plural,
				'singular_name'       => $singular,
				'menu_name'           => $plural,
				'all_items'           => sprintf( __( 'All %s', 'private-messages' ), $plural ),
				'add_new'             => __( 'Add New', 'private-messages' ),
				'add_new_item'        => sprintf( __( 'New %s', 'private-messages' ), $singular ),
				'edit'                => __( 'Edit', 'private-messages' ),
				'edit_item'           => sprintf( __( '%s', 'private-messages' ), $singular ),
				'new_item'            => sprintf( __( 'New %s', 'private-messages' ), $singular ),
				'view'                => sprintf( __( 'View %s', 'private-messages' ), $singular ),
				'view_item'           => sprintf( __( 'View %s', 'private-messages' ), $singular ),
				'search_items'        => sprintf( __( 'Search %s', 'private-messages' ), $plural ),
				'not_found'           => sprintf( __( 'No %s found', 'private-messages' ), $plural ),
				'not_found_in_trash'  => sprintf( __( 'No %s found in trash', 'private-messages' ), $plural ),
				'parent'              => sprintf( __( 'Parent %s', 'private-messages' ), $singular ),
			),
			'description'         => __( 'This is where you can create and manage private messages.', 'private-messages' ),
			'public'              => true, // Maybe better as non public.
			'show_ui'             => true,
			'capability_type'     => 'post',
			'capabilities' => array(
				'publish_posts'       => $admin_capability,
				'edit_posts'          => $admin_capability,
				'edit_others_posts'   => $admin_capability,
				'delete_posts'        => $admin_capability,
				'delete_others_posts' => $admin_capability,
				'read_private_posts'  => $admin_capability,
				'edit_post'           => $admin_capability,
				'delete_post'         => $admin_capability,
				'read_post'           => $admin_capability,
			),
			'publicly_queryable'  => true,
			'exclude_from_search' => true,
			'hierarchical'        => false,
			'rewrite'             => false,
			'query_var'           => true,
			'supports'            => array( 'title' ),
			'has_archive'         => false,
			'show_in_nav_menus'   => false,
			'menu_icon'           => 'dashicons-email-alt',
		);

		// Register Post Type.
		register_post_type( 'private-messages', apply_filters( 'register_post_type_private_message', $args ) );
	}

	/**
	 * Delete Post
	 * This also delete attachments.
	 *
	 * @since 1.3.0
	 *
	 * @param int $post_id Post ID.
	 */
	public function delete_post( $post_id ) {
		Private_Messages_Files::delete_files( $post_id );

		// Remove Post ID from User deleted thread.
		$this->remove_user_deleted_thread( get_post_field( 'post_author', $post_id ), $post_id ); // Del author.
		$this->remove_user_deleted_thread( get_post_meta( $post_id, '_pm_recipient', true ), $post_id ); // Del recipient.
	}

	/**
	 * Clean Up User Deleted Thread
	 *
	 * @since 1.4.0
	 *
	 * @param int $user_id User ID.
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function remove_user_deleted_thread( $user_id, $post_id ) {
		if ( ! $post_id || ! $user_id ) {
			return;
		}

		// Delete "read" status on thread.
		delete_transient( 'pm_unread_count_' . $user_id );
		delete_user_meta( $user_id, '_pm_unread_' . $post_id );

		// Get user deleted thread IDs.
		$deleted = pm_get_user_deleted_threads( $user_id );

		// If the post is deleted, clean up.
		if ( in_array( $post_id, $deleted ) ) {
			$deleted = array_diff( $deleted, array( $post_id ) );
			if ( $deleted ) {
				update_user_meta( $user_id, 'pm_deleted', $deleted );
			} else {
				delete_user_meta( $user_id, 'pm_deleted' );
			}
		}
	}

}
