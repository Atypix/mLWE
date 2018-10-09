<?php
/**
 * Message - Message
 *
 * @since 1.0.0
 * @version 1.0.0
 */
?>

<div class="pm-message">
	<?php echo apply_filters( 'the_content', $message->get_content() ); ?>
</div>
