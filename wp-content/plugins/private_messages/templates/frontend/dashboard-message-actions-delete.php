<?php
/**
 * Dashboard - Message Actions: Delete
 *
 * Display the delete reply link.
 * it will not actually delete the replies, only hiding/archive it from user view.
 *
 * @since 1.4.0
 */
?>

<p><a href="<?php echo esc_url( $message->delete_url() ); ?>" class="pm-delete-reply">
	<?php _e( 'Delete Reply', 'private-messages' ); ?>
</a></p>
