<?php
    $next_post = get_next_post();
    $prev_post = get_previous_post();
    $next_post_url = $prev_post_url = "";
	$next_post_title = $prev_post_title = "";

    if ( is_object($next_post) ) {
        $next_post_url = get_permalink($next_post->ID);
        $next_post_title = $next_post->post_title;
    }
    if ( is_object($prev_post) ) {
        $prev_post_url = get_permalink($prev_post->ID);
		$prev_post_title = $prev_post->post_title;
    }
?>

<?php if ( !empty($prev_post_url) || !empty($next_post_url) ): ?>

	<div class="kw-entries-nav">

		<?php if ( !empty($prev_post_url) ): ?>

			<div class="kw-entry-wrap kw-previous-entry">

				<div class="kw-entry">

					<?php if ( has_post_thumbnail($prev_post->ID) ): ?>

						<div class="kw-entry-thumb">
							<a href="<?php echo esc_url($prev_post_url) ?>">
								<?php echo get_the_post_thumbnail($prev_post->ID); ?>
							</a>
						</div>

					<?php endif; ?>

					<div class="kw-entry-info">
						<span class="kw-caption"><?php echo esc_html__('Previous Post', 'knowherepro') ?></span>
						<h3 class="kw-entry-title"><a href="<?php echo esc_url($prev_post_url) ?>"><?php echo esc_html($prev_post_title); ?></a></h3>
					</div><!--/ .entry-info -->

				</div>

			</div><!--/ .kw-entry-wrap -->

		<?php endif; ?>

		<?php if ( !empty($next_post_url) ): ?>

			<div class="kw-entry-wrap kw-next-entry">

				<div class="kw-entry">

					<?php if ( has_post_thumbnail($next_post->ID) ): ?>

						<div class="kw-entry-thumb">
							<a href="<?php echo esc_url($next_post_url) ?>">
								<?php echo get_the_post_thumbnail($next_post->ID); ?>
							</a>
						</div>

					<?php endif; ?>

					<div class="kw-entry-info">
						<span class="kw-caption"><?php echo esc_html__('Next Post', 'knowherepro') ?></span>
						<h3 class="kw-entry-title"><a href="<?php echo esc_url($next_post_url) ?>"><?php echo esc_html($next_post_title); ?></a></h3>
					</div><!--/ .entry-info -->

				</div>

			</div><!--/ .kw-entry-wrap -->

		<?php endif; ?>

	</div><!--/ .kw-entries-nav-->

<?php endif; ?>