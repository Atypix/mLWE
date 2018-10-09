<?php
/**
 * Dashboard - Single Message (Reply) Actions
 *
 * Display delete action link
 *
 * @since 1.4.0
 */

echo Private_Messages_Templates::get_template( 'frontend/dashboard-message-actions-delete.php', array(
	'message' => $message,
) );
