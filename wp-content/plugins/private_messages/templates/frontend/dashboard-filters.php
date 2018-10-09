<?php
/**
 * Dashboard - Filters
 *
 * @since 1.0.0
 * @version 1.0.0
 */
?>

<form type="GET" action="" class="pm-form pm-form--filters">
	<p class="pm-form__row">
		<label for="pm-showing" class="pm-form__label"><?php _e( 'Show:', 'private-messages' ); ?></label>
		<select id="pm-showing" name="pm_showing" class="pm-form__input">
			<option value="all" <?php selected( pm_get_messages_showing(), 'all' ); ?>><?php _e( 'All', 'private-messages' ); ?></option>
			<option value="starred" <?php selected( pm_get_messages_showing(), 'starred' ); ?>><?php _e( 'Starred', 'private-messages' ); ?></option>
			<option value="unread" <?php selected( pm_get_messages_showing(), 'unread' ); ?>><?php _e( 'Unread', 'private-messages' ); ?></option>
		</select>
	</p>

	<p class="pm-form__row">
		<label for="pm-order" class="pm-form__label"><?php _e( 'Order:', 'private-messages' ); ?></label>
		<select id="pm-order" name="pm_order" class="pm-form__input">
			<option value="desc" <?php selected( pm_get_messages_order(), 'desc' ); ?>><?php _e( 'Newest to Oldest', 'private-messages' ); ?></option>
			<option value="asc" <?php selected( pm_get_messages_order(), 'asc' ); ?>><?php _e( 'Oldest to Newest', 'private-messages' ); ?></option>
		</select>
	</p>

	<p class="pm-form__row pm-form__row--submit">
		<input type="submit" value="<?php esc_attr_e( 'Filter', 'private-messages' ); ?>">
	</p>

	<?php if ( pm_can_compose_from_dashboard() ) : ?>
	<p class="pm-form__row pm-action-row">
		<a href="<?php echo esc_url( pm_get_new_message_url() ); ?>" class="button pm-button pm-button--new-message"><?php _e( 'New Message', 'private-messages' ); ?></a>
	</p>
	<?php endif; ?>

	<?php echo pm_get_mark_all_as_read_link(); ?>
</form>
