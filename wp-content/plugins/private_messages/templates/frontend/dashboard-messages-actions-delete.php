<?php
/**
 * Dashboard - Messages Actions: Delete
 *
 * Display the delete message icon.
 * it will not actually delete the message, only hiding/archive it from user view.
 *
 * @since 1.4.0
 */
?>

<a href="<?php echo esc_url( $thread->delete_url() ); ?>" class="pm-delete-thread dashicons dashicons-trash">
	<span class="screen-reader-text">
		<?php _e( 'Delete Message', 'private-messages' ); ?>
	</span>
</a>
