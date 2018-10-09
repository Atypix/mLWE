<?php

class WPBakeryShortCode_VC_mad_blog_posts extends WPBakeryShortCode {

	public $atts = array();
	public $entries = '';
	public $loop = array();

	protected function content($atts, $content = null) {

		$this->atts = shortcode_atts(array(
			'title' => '',
			'subtitle' => '',
			'title_color' => '',
			'subtitle_color' => '',
//			'align_title' => '',
			'layout' => 'kw-blog-default',
			'columns' => 3,
			'categories' => array(),
			'orderby' => 'date',
			'order' => 'DESC',
			'items' => 6,
			'paginate' => 'none',
			'show_button' => false,
			'hide_cover_image' => false
		), $atts, 'vc_mad_blog_posts');

		$this->query_entries();
		$html = $this->html();

		return $html;
	}

	public function query_entries() {

		$params = $this->atts;

		$query = array(
			'post_type' => 'post',
			'posts_per_page' => $params['items'],
			'orderby' => $params['orderby'],
			'order' => $params['order'],
			'post_status' => array('publish')
		);

		if ( !empty($params['categories']) ) {
			$categories = explode(',', $params['categories']);
			$query['category__in'] = $categories;
		}

		$paged = get_query_var( 'page' ) ? get_query_var( 'page' ) : ( get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1 );
		$query['paged'] = $paged;

		$this->entries = new WP_Query($query);
		$this->prepare_entries($params);
	}

	public function html() {

		if ( empty($this->loop) ) return;

		$atts = $this->atts;
		$wrapper_attributes = $data_attributes = array();
		$title = !empty($atts['title']) ? $atts['title'] : '';
		$subtitle = !empty($atts['subtitle']) ? $atts['subtitle'] : '';
		$title_color = !empty($atts['title_color']) ? $atts['title_color'] : '';
		$subtitle_color = !empty($atts['subtitle_color']) ? $atts['subtitle_color'] : '';
		$formats = array('standard', 'gallery', 'video', 'audio', 'image');
		$hide_cover_image = $atts['hide_cover_image'] == true ? 1 : 0;

		extract($atts);

		$defaults = array(
			'id' => '', 'link' => '', 'title' => 10, 'post_format' => 'standard',
			'format_class' => '', 'content' => '', 'image_size' => '',
			'post_content' => '', 'before_content' => ''
		);

		global $knowhere_settings;
		$limit = absint($knowhere_settings['excerpt-count-thumbs']);

		$css_classes = array( 'kw-entries' );
		$css_classes[] = $layout;

		if ( $layout == 'kw-carousel' ) {
			$css_classes[] = 'owl-carousel';
		} elseif ( $layout == 'kw-isotope' ) {
			$css_classes[] = 'kw-cols-' . absint($columns);
			$data_attributes[] = 'data-masonry="true"';
		}

		$css_class = preg_replace( '/\s+/', ' ', implode( ' ', array_filter( $css_classes ) ) );

		$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';
		$wrapper_attributes[] = implode( ' ', $data_attributes);

		ob_start(); ?>

			<?php if ( $show_button ): ?>

				<header class="kw-section-header">

					<div class="kw-left-col">

						<?php
						echo Knowhere_Vc_Config::getParamTitle(
							array(
								'heading' => 'h2',
								'title' => $title,
								'subtitle' => $subtitle,
								'title_color' => $title_color,
								'subtitle_color' => $subtitle_color
							)
						);
						?>

					</div><!--/ .kw-left-col -->

					<div class="kw-right-col">
						<a class="kw-btn-small kw-gray" href="<?php echo get_post_type_archive_link('post') ?>"><?php esc_html_e('View All', 'knowherepro') ?></a>
					</div><!--/ .kw-right-col -->

				</header>

			<?php else: ?>

				<?php
				echo Knowhere_Vc_Config::getParamTitle(
					array(
						'title' => $title,
						'subtitle' => $subtitle,
						'title_color' => $title_color,
						'subtitle_color' => $subtitle_color
					)
				);
				?>

			<?php endif; ?>

			<div class="kw-listings-holder">

				<div <?php echo implode( ' ', $wrapper_attributes ) ?>>

					<?php foreach ( $this->loop as $entry ): extract($entry); ?>

						<div class="kw-entry-wrap" id="post-<?php echo absint($id) ?>">

							<article class="kw-entry <?php echo sanitize_html_class($format_class) ?>">

								<?php if ( !$hide_cover_image ): ?>

									<div class="kw-entry-thumb">

										<?php if ( $layout == 'kw-carousel' ): ?>
											<?php
											if ( has_post_thumbnail($id) ) {
												echo "<a href='{$link}' class='kw-thumbnail-attachment'>" . Knowhere_Helper::get_the_post_thumbnail( $id, $image_size, true, '' ) . "</a>";
											}
											?>
										<?php else: ?>

											<?php if ( in_array( $post_format, $formats ) ): ?>
												<?php echo ( !empty($before_content) ) ? $before_content : ''; ?>
											<?php endif; ?>

										<?php endif; ?>

									</div><!--/ .kw-entry-thumb-->

								<?php endif; ?>

								<div class="kw-entry-info">

									<?php echo knowhere_blog_post_meta($id) ?>

									<h3 class="kw-entry-title">
										<a href="<?php echo esc_url($link) ?>"><?php echo esc_html($title) ?></a>
									</h3>

									<?php if ( $layout !== 'kw-carousel' ) : ?>

										<?php if ( $post_format == 'quote' || $post_format == 'link' || $post_format == 'aside' || $post_format == 'status' ): ?>
											<?php echo ( !empty($before_content) ) ? $before_content : ''; ?>
										<?php endif; ?>

									<?php endif; ?>

									<div class="kw-entry-meta">
										<?php knowhere_entry_date($id, $atts); ?>
									</div><!--/ .kw-entry-meta-->

									<?php if ( $layout !== 'kw-carousel' ): ?>

										<?php if ( in_array( $post_format, $formats ) ): ?>
											<div class="kw-entry-content">
												<?php
												if ( has_excerpt($id) ) {
													echo knowhere_get_excerpt( $excerpt, $limit );
												} else {
													echo apply_filters( 'the_content', $content );
												}
												?>
											</div><!--/ .kw-entry-content-->
										<?php endif; ?>

									<?php else: ?>

										<div class="kw-entry-content">
											<?php
											if ( has_excerpt($id) ) {
												echo knowhere_get_excerpt( $excerpt, $limit );
											}
											?>
										</div>

									<?php endif; ?>

									<?php if ( $layout !== 'kw-carousel' ): ?>
										<?php if ( in_array( $post_format, $formats ) ): ?>
											<a href="<?php echo esc_url($link) ?>" class="kw-btn kw-theme-color kw-medium"><?php esc_html_e('Read More', 'knowherepro') ?><i class="lnr icon-chevron-right kw-post-icon"></i></a>
										<?php endif; ?>
									<?php endif; ?>

								</div><!--/ .kw-entry-info -->

							</article>

						</div><!--/ .kw-entry-wrap -->

					<?php endforeach; wp_reset_postdata(); ?>

				</div>

				<?php if ( $paginate == "pagination" && $knowhere_pagination = knowhere_pagination($this->entries) ) : ?>
					<?php echo $knowhere_pagination; ?>
				<?php endif; ?>

			</div>

		<?php return ob_get_clean();
	}

	public function prepare_entries($params) {

		if ( empty($params ) ) $params = $this->atts;
		if ( empty($this->entries) || empty($this->entries->posts) ) return;

		$layout = $params['layout'];

		foreach ( $this->entries->posts as $key => $entry ) {
			$this->loop[$key]['id'] = $id = $entry->ID;
			$this->loop[$key]['link'] = get_permalink($id);
			$this->loop[$key]['title'] = get_the_title($id);
			$this->loop[$key]['post_format'] = $format = get_post_format($id) ? get_post_format($id) : 'standard';

			$this->loop[$key]['image_size'] = knowhere_blog_alias($format, $params);
			$this->loop[$key]['content'] = $entry->post_content;
			$this->loop[$key]['excerpt'] = has_excerpt( $id ) ? $entry->post_excerpt : '';

			if ( $layout == 'kw-carousel' ) {
				$this->loop[$key]['post_format'] = 'standard';
			}

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

			$this->loop[$key]['format_class'] = $format_class;
			$this->loop[$key]['this_post'] = apply_filters( 'knowhere-entry-format-'. $format, $this->loop[$key] );

			$this->loop[$key]['post_content'] = has_excerpt( $id ) ? $entry->post_excerpt : $this->loop[$key]['this_post']['content'];

			if ( isset($this->loop[$key]['this_post']['before_content']) && !empty($this->loop[$key]['this_post']['before_content']) ) {
				$this->loop[$key]['before_content'] = $this->loop[$key]['this_post']['before_content'];
			}

		}

	}

}