<?php
/**
 * Dashboard - Messages Actions
 *
 * Display the star and read/unread status (currently)
 *
 * @since 1.0.0
 * @version 1.0.0
 */

echo Private_Messages_Templates::get_template( 'frontend/dashboard-messages-actions-status.php', array(
	'status' => $thread->is_unread() ? 'unread' : 'read',
) );

echo Private_Messages_Templates::get_template( 'frontend/dashboard-messages-actions-star.php', array(
	'starred' => $thread->is_starred(),
	'thread' => $thread,
) );

echo Private_Messages_Templates::get_template( 'frontend/dashboard-messages-actions-delete.php', array(
	'thread' => $thread,
) );
