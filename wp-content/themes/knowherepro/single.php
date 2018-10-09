<?php
/**
 * The template for displaying all single posts
 *
 * @package WordPress
 * @subpackage KnowherePro
 * @since KnowherePro 1.0
 */
get_header(); ?>

<?php if ( have_posts() ): ?>

	<?php global $knowhere_settings; ?>

	<div class="kw-entry-wrap">

		<?php while ( have_posts() ) : the_post(); ?>

			<?php
			global $post;
			$this_post = array();
			$this_post['id'] = $this_id = get_the_ID();
			$this_post['content'] = get_the_content();
			$this_post['post_format'] = get_post_format() ? get_post_format() : 'standard';
			$this_post['url'] = $link = get_permalink($this_id);
			$this_post['image_size'] = '';
			$this_post = apply_filters( 'knowhere-entry-format-single', $this_post );
			extract($this_post);
			?>

			<article id="<?php echo get_the_ID() ?>" <?php post_class('kw-entry kw-single'); ?>>

				<div class="kw-entry-info">

					<?php echo knowhere_blog_post_meta($id, array( 'tags' => false )) ?>

					<h3 class="kw-entry-title">
						<a href="<?php echo esc_url($link) ?>"><?php echo esc_html($title) ?></a>
					</h3>

					<div class="kw-entry-meta">
						<?php knowhere_entry_date($id); ?>
					</div><!--/ .kw-entry-meta-->

					<?php if ( !empty($this_post['content']) ): ?>
						<div class="kw-entry-content">
							<?php echo apply_filters( 'the_content', $this_post['content'] ); ?>
							<?php wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'knowherepro' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) ); ?>
						</div><!--/ .kw-entry-content-->
					<?php endif; ?>

					<?php if ( $knowhere_settings['post-tag'] ): ?>
						<?php
							$tags_list = get_the_tag_list( '', '' );
							if ( $tags_list ) {
								echo '<div class="kw-entry-tags"><span class="screen-reader-text">' . esc_html__( 'Tags', 'knowherepro' ) . ':</span>' . $tags_list . '</div>';
							}
						?>
					<?php endif; ?>

					<?php if ( $knowhere_settings['post-single-share'] ): ?>
						<?php if ( function_exists('knowhere_content_share') ): ?>
							<?php knowhere_content_share(); ?>
						<?php endif; ?>
					<?php endif; ?>

					<?php if ( $knowhere_settings['post-nav'] ): ?>
						<?php get_template_part( 'template-parts/single', 'link-pages' ) ?>
					<?php endif; ?>

				</div><!--/ .kw-entry-info -->

			</article><!--/ .kw-entry-->

		<?php endwhile ?>

	</div><!--/ .kw-entry-wrap-->

	<?php if ( $knowhere_settings['post-author'] ): ?>
		<?php get_template_part( 'template-parts/single', 'author-box' ); ?>
	<?php endif; ?>

	<?php if ( $knowhere_settings['post-related-posts'] ): ?>
		<?php get_template_part('template-parts/single', 'related-posts'); ?>
	<?php endif; ?>

	<?php if ( $knowhere_settings['post-comments'] ): ?>
		<?php if ( comments_open() || '0' != get_comments_number() ): ?>
			<?php comments_template(); ?>
		<?php endif; ?>
	<?php endif; ?>

<?php endif; ?>

<?php get_footer(); ?>