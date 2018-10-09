<?php
/**
 * Recipient.
 *
 * @since 1.0.0
 * @version 1.8.0
 */
?>
<?php if ( empty( $recipient ) ) : ?>

	<select name="pm_recipient">
		<option value=""><?php _e( 'Select a Recipient', 'private-messages' ); ?></option>
	</select>
	
<?php else : ?>

	<?php $user = get_userdata( $recipient ); ?>
	<?php echo pm_get_user_display_name( $user ); ?>
	<?php if ( ! empty( $user->user_email ) ) : ?>
		(<?php echo $user->user_email; ?>)
	<?php endif; ?>

<?php endif; ?>
