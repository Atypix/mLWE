<?php
/**
 * Messages - User Info
 *
 * @since 1.0.0
 * @version 1.8.0
 */
?>

<div class="pm-userinfo">
	<?php if ( get_option( 'show_avatars', true ) ) : ?>
		<p class="pm-userinfo__avatar">
			<a href="<?php echo esc_url( get_author_posts_url( $recipient->ID ) ); ?>">
				<?php echo pm_get_avatar( $recipient->ID ); ?>
			</a>
		</p>
	<?php endif; ?>
	<p class="pm-userinfo__author">
		<a href="<?php echo esc_url( get_author_posts_url( $recipient->ID ) ); ?>">
			<?php echo esc_attr( pm_get_user_display_name( $recipient ) ); ?>
		</a>
	</p>

	<p class="pm-userinfo__date">
		<span class="pm-userinfo__date-date"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $thread->data->post_date ) ); ?></span>
	</p>
</div>
