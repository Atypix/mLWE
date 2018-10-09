<?php
/**
 * Dashboard - Messages Actions: Star
 *
 * Display the messages star'd status with the ability to toggle.
 *
 * @since 1.0.0
 * @version 1.0.0
 */

$url = add_query_arg( array(
	'set_star' => $starred ? 'unstar' : 'star',
	'id' => $thread->ID,
), get_permalink() );
?>

<a href="<?php echo esc_url( $url ); ?>" class="pm-set-star pm-star-<?php echo esc_attr( $starred ? 'starred' : 'unstarred' ); ?> dashicons dashicons-star-<?php echo esc_attr( $starred ? 'filled' : 'empty' ); ?>">
	<span class="screen-reader-text">
		<?php echo $starred ? __( 'Starred', 'private-messages' ) : __( 'Unstarred', 'private-messages' ); ?>
	</span>
</a>
