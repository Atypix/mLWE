<?php
/**
 * Job Manager Application Form.
 * Var $apply contain method data.
 *
 * @var object $apply
 */
?>

<?php if ( absint( get_current_user_id() ) === absint( $apply->to ) ) : ?>

	<p><?php _e( 'You cannot send a message to yourself..', 'private-messages' ); ?></p>

<?php elseif( ! is_user_logged_in() ) : ?>

	<p><?php printf( __( 'Only registered users can send messages. Please %1$slog in%2$s to continue.', 'private-messages' ), '<a href="' . wp_login_url( get_permalink() ) . '">', '</a>' ); ?></p>

<?php else : ?>

	<?php echo do_shortcode( '[private_message_compose to="' . esc_attr( $apply->to ) . '" subject="' . esc_attr( $apply->subject ) . '" message="' . esc_attr( $apply->message ) . '"]' ); ?>

<?php endif; ?>
