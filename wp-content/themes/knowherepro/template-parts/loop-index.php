<?php

global $knowhere_settings;
$limit = absint($knowhere_settings['excerpt-count-thumbs']);

$this_post = array();
$this_post['id'] = $id = get_the_ID();
$this_post['link'] = $link = get_permalink();
$this_post['title'] = $title = get_the_title();
$this_post['post_format'] = $format = get_post_format() ? get_post_format() : 'standard';
$this_post['image_size'] = knowhere_blog_alias( $format, array('layout' => 'kw-blog-default') );
$this_post['content'] = get_the_content();
$this_post = apply_filters( 'knowhere-entry-format-' . $format, $this_post );

$formats = array( 'standard', 'gallery', 'video', 'audio', 'image' );

extract($this_post);

$post_content = has_excerpt() ? get_the_excerpt() : $content;

switch ( $format ) {
	case 'standard': $format_class = 'format-standard'; break;
	case 'gallery':  $format_class = 'format-slideshow'; break;
	case 'video': 	 $format_class = 'format-video'; break;
	case 'link': 	 $format_class = 'format-link'; break;
	case 'audio': 	 $format_class = 'format-audio'; break;
	case 'quote': 	 $format_class = 'format-quote'; break;
	case 'image': 	 $format_class = 'format-image'; break;
	case 'aside': 	 $format_class = 'format-aside'; break;
	case 'status': 	 $format_class = 'format-status'; break;
	case 'chat': 	 $format_class = 'format-chat'; break;
	default: 		 $format_class = 'format-standard'; break;
}

?>

<div class="kw-entry-wrap" id="post-<?php echo absint($id) ?>">

	<article class="kw-entry <?php echo sanitize_html_class($format_class) ?>">

		<?php knowhere_post_thumbnail() ?>

		<div class="kw-entry-info">

			<?php echo knowhere_blog_post_meta($id) ?>

			<h3 class="kw-entry-title">
				<a href="<?php echo esc_url($link) ?>"><?php echo esc_html($title) ?></a>
			</h3>

			<div class="kw-entry-meta">
				<?php knowhere_entry_date($id); ?>
			</div><!--/ .kw-entry-meta-->

			<div class="kw-entry-content">
				<?php echo apply_filters('the_content', $post_content); ?>
				<?php wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'knowherepro' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) ); ?>
			</div><!--/ .kw-entry-content-->

			<a href="<?php echo esc_url($link) ?>" class="kw-btn kw-theme-color kw-medium"><?php esc_html_e('Read More', 'knowherepro') ?><i class="lnr icon-chevron-right kw-post-icon"></i></a>

		</div><!--/ .kw-entry-info -->

	</article>

</div><!--/ .kw-entry-wrap -->