<?php
/**
 * Dashboard - Logged Out message.
 *
 * Displayed in the [private_messages] when the user is not logged in.
 *
 * @since 1.0.0
 * @version 1.0.0
 */
?>
 
<p class="pm-logged-out"><?php printf( __( 'Only registered users can send messages. Please %1$slog in%2$s to continue.', 'private-messages' ), '<a href="' . wp_login_url( pm_get_permalink( 'dashboard' ) ) . '">', '</a>' ); ?></p>
