<?php
	/**
	 * The template for displaying Search Results pages.
	 */
	get_header();
?>

<?php if ( !empty($_GET['s']) || have_posts() ): ?>

	<?php
	$loop_count = 1;
	$page = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
	if ( $page > 1 ) {
		$loop_count = ((int) ($page - 1) * (int) get_query_var('posts_per_page')) + 1;
	}

	global $knowhere_settings;
	$wrapper_attributes = array();

	$limit = absint($knowhere_settings['excerpt-count-thumbs']);

	$css_classes = array(
		'kw-entries', 'kw-blog-default'
	);

	$css_class = preg_replace( '/\s+/', ' ', implode( ' ', array_filter( $css_classes ) ) );
	$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';
	?>

	<div <?php echo implode( ' ', $wrapper_attributes ) ?>>

		<?php if ( have_posts() ): ?>

		<?php
		// Start the loop.
		while ( have_posts() ) : the_post();
			$this_post = array();
			$this_post['id'] = $id = get_the_ID();
			$this_post['link'] = $link = get_permalink();
			$this_post['title'] = $title = get_the_title();
			$this_post['post_format'] = $format = get_post_format() ? get_post_format() : 'standard';
			$this_post['image_size'] = knowhere_blog_alias( $format, array('layout' => 'kw-blog-default') );
			$this_post['content'] = get_the_content();
			$this_post = apply_filters( 'knowhere-entry-format-' . $format, $this_post );

			$formats = array('standard', 'gallery', 'video', 'audio');

			extract($this_post);

			$post_content = has_excerpt() ? get_the_excerpt() : $content;

			switch ( $format ) {
				case 'standard': $format_class = 'format-standard'; break;
				case 'gallery':  $format_class = 'format-slideshow'; break;
				case 'video': 	 $format_class = 'format-video'; break;
				case 'link': 	 $format_class = 'format-link'; break;
				case 'audio': 	 $format_class = 'format-audio'; break;
				case 'quote': 	 $format_class = 'format-quote'; break;
				default: 		 $format_class = 'format-standard'; break;
			}

			?>

			<div class="kw-entry-wrap" id="post-<?php echo absint($id) ?>">

				<span class="kw-search-result-counter">
					<span class="kw-dropcap-result"><?php echo esc_html($loop_count) ?></span>
				</span>

				<article class="kw-entry <?php echo sanitize_html_class($format_class) ?>">

					<div class="kw-entry-info">

						<?php $categories = get_the_category_list(", ", '', $id); ?>

						<?php if ( !empty($categories) ): ?>

							<ul class="kw-entry-cats">
								<li><?php echo sprintf('%s', $categories) ?></li>
							</ul><!--/ .kw-entry-cats-->

						<?php endif; ?>

						<h3 class="kw-entry-title">
							<a href="<?php echo esc_url($link) ?>"><?php echo esc_html($title) ?></a>
						</h3>

						<?php if ( $format == 'quote' || $format == 'link' ): ?>
							<?php echo ( !empty($before_content) ) ? $before_content : ''; ?>
						<?php endif; ?>

						<div class="kw-entry-meta">
							<?php knowhere_entry_date($id); ?>
						</div><!--/ .kw-entry-meta-->

						<?php if ( in_array( $format, $formats ) ): ?>
							<div class="kw-entry-content">
								<?php
								if ( has_excerpt($id) ) {
									echo knowhere_get_excerpt( $post_content, $limit );
								} else {
									echo apply_filters( 'the_content', $content );
								}
								?>
							</div><!--/ .kw-entry-content-->
						<?php endif; ?>

						<?php if ( in_array( $format, $formats ) ): ?>
							<a href="<?php echo esc_url($link) ?>" class="kw-btn kw-theme-color kw-medium"><?php esc_html_e('Read More', 'knowherepro') ?><i class="lnr icon-chevron-right kw-post-icon"></i></a>
						<?php endif; ?>

					</div><!--/ .kw-entry-info -->

				</article>

			</div><!--/ .kw-entry-wrap -->

		<?php $loop_count++; endwhile; ?>

		<?php else: ?>

			<?php
			// If no content, include the "No posts found" template.
			get_template_part( 'template-parts/content', 'none' );
			?>

		<?php endif; ?>

	</div>

	<?php echo knowhere_pagination(); ?>

<?php else:

	// If no content, include the "No posts found" template.
	get_template_part( 'template-parts/content', 'none' );

endif; ?>

<?php get_footer(); ?>
