<?php
/**
 *
 */
?>

<a href="<?php echo esc_url( $thread->get_url() ); ?>#message-<?php echo esc_attr( $last_message->ID ); ?>">
	<strong class="message__subject"><?php echo esc_attr( get_the_title( $thread->ID ) ); ?></strong><br />
	<?php echo $last_message->get_content( 20 ); ?>
</a>
