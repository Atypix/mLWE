<?php
$this_id = get_the_ID();
$tag_ids = array();
$tags = wp_get_post_terms($this_id, 'post_tag');

if ( !empty($tags) && is_array($tags) ) {

	$query = array(
		'post_type' => 'post',
		'numberposts' => 3,
		'ignore_sticky_posts'=> 1,
		'post__not_in' => array($this_id)
	);

	foreach ($tags as $tag) {
		$tag_ids[] = (int) $tag->term_id;
	}

	if ( !empty($tag_ids) ) {

		$query['tag__in'] = $tag_ids; $entries = get_posts($query); ?>

		<?php if ( !empty($entries) ): ?>

			<div class="kw-box">

				<h3><?php esc_html_e('Related Posts', 'knowherepro'); ?></h3>

				<div class="kw-entries kw-related-entries kw-cols-3">

					<?php foreach( $entries as $post ): setup_postdata($post); ?>

						<div class="kw-entry-wrap">

							<div class="kw-entry" id="post-<?php the_ID(); ?>">

								<?php knowhere_post_thumbnail('knowhere-related-posts-image'); ?>

								<div class="kw-entry-info">

									<h3 class="kw-entry-title">
										<a href="<?php echo esc_url(get_permalink()) ?>"><?php the_title(); ?></a>
									</h3>

									<div class="kw-entry-meta">
										<?php knowhere_entry_date(get_the_ID(), array('type' => 'related')); ?>
									</div><!--/ .kw-entry-meta-->

								</div><!--/ .kw-entry-info -->

							</div><!--/ .kw-entry-->

						</div><!--/ .kw-entry-wrap-->

					<?php endforeach; ?>

				</div><!--/ .kw-entries-->

			</div><!--/ .kw-box-->

			<?php wp_reset_postdata(); ?>

		<?php endif; ?>

	<?php
	}
}