<?php
$id = get_the_ID();
$name = get_the_title($id);
$link = get_permalink($id);
$address = get_post_meta( $id, 'knowhere_agent_address', true );
?>

<figure class="kw-team-member">

	<a href="<?php echo esc_url($link) ?>" class="kw-team-member-photo">
		<?php echo get_the_post_thumbnail( $id, 'knowhere-agent-photo' ); ?>
	</a>

	<figcaption class="kw-team-member-info">

		<h4 class="kw-team-member-name"><a href="<?php echo esc_url($link) ?>"><?php echo esc_html($name) ?></a></h4>

		<?php if ( isset($address) && !empty($address) ): ?>
			<div class="kw-team-member-position"><?php echo esc_html($address) ?></div>
		<?php endif; ?>

	</figcaption>

</figure><!--/ .kw-team-member -->