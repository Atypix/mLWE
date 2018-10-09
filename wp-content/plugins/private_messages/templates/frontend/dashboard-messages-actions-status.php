<?php
/**
 * Dashboard - Messages Actions: Star
 *
 * Display the messages read status.
 *
 * @since 1.0.0
 * @version 1.0.0
 */
?>
<span class="pm-message-status pm-message-status--<?php echo esc_attr( $status ); ?>">
	<em>
		<?php echo 'unread' == $status ? __( 'Unread', 'private-messages' ) : __( 'Read', 'private-messages' ); ?>
	</em>
</span>
