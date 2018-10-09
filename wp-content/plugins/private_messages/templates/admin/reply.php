<?php

wp_nonce_field( 'pm_message_nonce', 'pm_message_nonce' );

$editor_settings = apply_filters( 'pm_editor_settings', array(
	'media_buttons' => false,
) );

wp_editor( '', 'pm_message', $editor_settings );

?>
<?php if ( pm_get_option( 'pm_allow_attachments' , true ) ) : ?>
	<p class="pm-attachments-fields">
		<?php _e( 'Attachments:', 'private-messages' ); ?><br/>
		<input name="pm_attachments[]" multiple="multiple" type="file">
	</p>
<?php endif; ?>
<p><?php submit_button( $pagenow == 'post-new.php' ? __( 'Send Message', 'private-messages' ) : __( 'Send Reply', 'private-messages' ), 'primary', 'send_message', false ); ?></p>
