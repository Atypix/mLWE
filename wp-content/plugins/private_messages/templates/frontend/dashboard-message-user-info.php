<?php
/**
 * Message - User Info
 *
 * @since 1.0.0
 * @version 1.8.0
 */
?>

<div class="pm-userinfo">
	<?php if ( get_option( 'show_avatars', true ) ) : ?>
		<p class="pm-userinfo__avatar"><?php echo pm_get_avatar( $message->get_author()->ID ); ?></p>
	<?php endif; ?>
	<p class="pm-userinfo__author"><?php echo esc_attr( $message->get_author_name() ); ?></p>

	<p class="pm-userinfo__date">
		<span class="pm-userinfo__date-date"><?php echo esc_attr( $message->get_date() ); ?></span>
		<span class="pm-userinfo__date-time"><?php echo esc_attr( $message->get_time() ); ?></span>
	</p>
</div>
