<?php
/**
 * Dashboard - Compose Reply
 *
 * Displayed in the [private_messages] when replying a message.
 *
 * @since 1.0.0
 * @version 1.0.0
 */
?>
<h2 class="pm-section-title">
	<?php _e( 'Respond', 'private-messages' ); ?>
</h2>

<form action="" method="post" class="pm-form pm-form--compose-message" enctype="multipart/form-data" encoding="multipart/form-data">

	<?php pm_message_editor( $message ); ?>

	<?php if ( pm_can_upload_attachments() ) : ?>
	<p class="pm-form__row pm-form__row--attachments">
		<?php _e( 'Attachments:', 'private-messages' ); ?><br/>
		<input id="pm_attachments" name="pm_attachments[]" multiple="multiple" type="file">
	</p>
	<?php endif; ?>

	<p class="pm-form__row pm-form__row--submit">
		<?php wp_nonce_field( 'pm_message_nonce', 'pm_message_nonce' ) ?>
		<input id="pm_send_message" type="submit" value="<?php esc_attr_e( 'Send Reply', 'private-messages' ); ?>">
	</p>
</form>
