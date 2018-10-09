<?php echo Private_Messages_Templates::get_template( 'frontend/dashboard-filters.php' ); ?>

<h2 class="pm-section-title">
	<?php echo pm_get_messages_title(); ?>
</h2>

<?php if ( ! empty( $my_messages ) ) : ?>
	<table class="pm-table pm-table--messages-list">
		<tbody>
			<?php foreach ( $my_messages as $row ) : ?>
				<tr>
					<?php foreach ( $row as $key => $column ) : ?>
						<td class="pm-column pm-column--<?php echo esc_attr( $key ); ?>"><?php echo $column; ?></td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php else : ?>
	<p class="pm-no-messages"><?php _e( 'No Messages', 'private-messages' ); ?></p>
<?php endif; ?>
