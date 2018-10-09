<?php
/**
 * Dashboard - Compose New Message
 *
 * Displayed in the [private_messages] when creating a new message.
 * Also displayed in [private_message_compose] shortcode.
 *
 * @since 1.0.0
 * @version 1.8.0
 */
?>

<?php echo Private_Messages_Templates::get_template( 'frontend/dashboard-return.php' ); ?>

<h2 class="pm-section-title">
	<?php _e( 'Compose Message', 'private-messages' ); ?>
</h2>

<form action="" method="post" class="pm-form pm-form--compose-message" enctype="multipart/form-data" encoding="multipart/form-data">

	<p class="pm-form__row pm-form__row--recipient">
		<label for="pm-recipient" class="pm-form__label"><?php _e( 'To:', 'private-messages' ); ?></label>

		<select id="pm-recipient" name="pm_recipient" class="pm-form__input" <?php disabled( true, $disable_to ); ?>>
			<?php if ( $recipient ) : $recipient = new WP_User( $recipient ); ?>
				<option value="<?php echo absint( $recipient->ID ); ?>">
					<?php echo esc_attr( pm_get_user_display_name( $recipient ) ); ?>
				</option>
			<?php else : ?>
				<option value=""><?php _e( 'Select a Recipient', 'private-messages' ); ?></option>
			<?php endif; ?>
		</select>
	</p>

	<p class="pm-form__row pm-form__row--subject">
		<label for="pm-subject" class="pm-form__label"><?php _e( 'Subject:', 'private-messages' ); ?></label>
		<input id="pm-subject" type="text" name="pm_subject" value="<?php echo esc_attr( $subject ); ?>" class="pm-form__input" placeholder="<?php _e( 'Subject', 'private-messages' ); ?>">
	</p>

	<?php pm_message_editor( $message ); ?>

	<?php if ( pm_can_upload_attachments() ) : ?>
	<p class="pm-form__row pm-form__row--attachments">
		<?php _e( 'Attachments:', 'private-messages' ); ?><br/>
		<input id="pm_attachments" name="pm_attachments[]" multiple="multiple" type="file">
	</p>
	<?php endif; ?>

	<p class="pm-form__row pm-form__row--submit">
		<?php wp_nonce_field( 'pm_message_nonce', 'pm_message_nonce' ) ?>
		<input id="pm_send_message" type="submit" value="<?php esc_attr_e( 'Send Message', 'private-messages' ); ?>">
	</p>
</form>
