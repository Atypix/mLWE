<?php if ( empty( $messages ) ) : ?>

	<h2><?php _e( 'There are currently no messages in this conversation.', 'private-messages' ); ?></h2>

<?php else : ?>

	<table class="form-table">
		<tbody>

			<?php foreach ( $messages as $message ) : ?>
				<tr>
					<th scope="row">
						<?php echo $message->comment_author; ?><br>
						<span class="description">
							<?php echo date_i18n( get_option( 'date_format' ), strtotime( $message->comment_date ) ); ?><br>
							<?php echo date_i18n( get_option( 'time_format' ), strtotime( $message->comment_date ) ); ?>
						</span>
					</th>
					<td valign="top">
						<?php echo apply_filters( 'the_content', $message->comment_content ); ?>
						<?php pm_message_attachments_html( pm_get_message( $message ) ); ?>
					</td>
				</tr>
			<?php endforeach; ?>

		</tbody>
	</table>
	
<?php endif; ?>
