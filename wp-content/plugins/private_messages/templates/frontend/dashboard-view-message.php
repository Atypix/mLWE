<?php
/**
 * Dashboard - View Message
 *
 * Display a single message. Includes:
 *
 * - Link to Dashboard.
 * - List of replies in the message.
 * - Compose Reply form.
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 * @var array  $messages
 * @var object $thread
 */
?>

<?php echo Private_Messages_Templates::get_template( 'frontend/dashboard-return.php' ); ?>
<?php echo Private_Messages_Templates::get_template( 'frontend/dashboard-report.php', array(
	'report_url' => esc_url( $thread->get_report_url() ),
) ); ?>

<h3 class="pm-message-subject">
	<?php echo esc_attr( get_the_title( $thread->ID ) ); ?>
</h3>

<table class="pm-table pm-table--message-list">
	<tbody>
		<?php foreach ( $messages as $id => $message ) : ?>

			<?php if ( $message->is_deleted() ) : ?>
				<?php continue; // skip if message is deleted/archived by user ?>
			<?php endif; ?>

			<tr id="message-<?php echo esc_attr( $id ); ?>">
				<td class="pm-column pm-column--message">
					<?php echo Private_Messages_Templates::get_template( 'frontend/dashboard-message-user-info.php', array(
						'message' => $message,
					) ); ?>

					<?php echo Private_Messages_Templates::get_template( 'frontend/dashboard-message-message.php', array(
						'message' => $message,
					) ); ?>

					<?php echo Private_Messages_Templates::get_template( 'frontend/dashboard-message-actions.php', array(
						'message' => $message,
					) ); ?>

					<?php if ( pm_get_option( 'pm_allow_attachments' , true ) ) : ?>
						<?php echo Private_Messages_Templates::get_template( 'frontend/dashboard-message-attachments.php', array(
							'message' => $message,
						) ); ?>
					<?php endif; ?>
				</td>
			</tr>

		<?php endforeach; ?>
	</tbody>
</table>

<?php
$receiver = get_current_user_id() === $thread->get_author_id() ? $thread->get_recipient_id() : $thread->get_author_id();
if ( ! $thread->is_deleted( $receiver ) ) {
	echo Private_Messages_Templates::get_template( 'frontend/dashboard-compose-reply.php', array(
		'message' => '',
	) );
}
?>
