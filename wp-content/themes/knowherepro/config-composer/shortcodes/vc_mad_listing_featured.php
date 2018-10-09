<?php

class WPBakeryShortCode_VC_mad_listing_featured extends WPBakeryShortCode {

	public $atts = array();
	public $listings = '';

	protected function content( $atts, $content = null ) {

		$this->atts = shortcode_atts(array(
			'title' => '',
			'subtitle' => '',
			'title_color' => '',
			'subtitle_color' => '',
			'align_title' => '',
			'number_of_items' => 10,
			'items_ids' => '',
			'categories' => '',
		), $atts, 'vc_mad_listing_featured');

		$this->query_entries();
		$html = $this->html();

		return $html;
	}

	public function query_entries($params = array()) {

		if ( empty($params) ) $params = $this->atts;

		extract($params);

		$query_args = array(
			'post_type'   => 'job_listing',
			'post_status' => 'publish',
			'meta_key' => '_featured',
			'meta_value' => '1'
		);

		if ( ! empty( $number_of_items ) && is_numeric( $number_of_items ) ) {
			$query_args['posts_per_page'] = $number_of_items;
		}

		if ( ! empty( $orderby ) && is_string( $orderby ) ) {
			$query_args['orderby'] = $orderby;
		}

		if ( ! empty( $items_ids ) && is_string( $items_ids ) ) {
			$query_args['post__in'] = explode( ',', $items_ids );
		}

		if ( ! empty( $categories ) && is_string( $categories ) ) {
			$categories = explode( ',', $categories );

			foreach ( $categories as $key => $cat ) {
				$categories[ $key ] = sanitize_title( $cat );
			}
			$query_args['tax_query'] = array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'job_listing_category',
					'field'    => 'id',
					'terms'    => $categories,
				)
			);
		}

		$this->listings = get_job_listings( $query_args );

	}

	public function html() {

		if ( empty($this->listings) ) return;

		global $post;

		$atts = $this->atts;
		$listings = $this->listings;

		$title = !empty($atts['title']) ? $atts['title'] : '';
		$subtitle = !empty($atts['subtitle']) ? $atts['subtitle'] : '';
		$title_color = !empty($atts['title_color']) ? $atts['title_color'] : '';
		$subtitle_color = !empty($atts['subtitle_color']) ? $atts['subtitle_color'] : '';
		$align_title = !empty($atts['align_title']) ? $atts['align_title'] : '';

		$css_classes = array(
			'kw-listings', 'kw-type-3', 'kw-listings-carousel-v2', 'owl-carousel', 'owl-nav-position-bottom'
		);

		global $knowhere_settings;
		$limit = absint($knowhere_settings['job-excerpt-count-content']);

		$css_class = preg_replace( '/\s+/', ' ', implode( ' ', array_filter( $css_classes ) ) );

		ob_start(); ?>

		<div class="kw-listings-holder">

			<?php
			echo Knowhere_Vc_Config::getParamTitle(
				array(
					'title' => $title,
					'subtitle' => $subtitle,
					'title_color' => $title_color,
					'subtitle_color' => $subtitle_color,
					'align_title' => $align_title
				)
			);
			?>

			<?php if ( $listings->have_posts() ): ?>

				<div class="<?php echo esc_attr( trim($css_class) ) ?>">

					<?php while ( $listings->have_posts() ) : $listings->the_post();

						$listing_classes = array(
							'kw-listing-item-wrap'
						);

						$listing_class = preg_replace( '/\s+/', ' ', implode( ' ', array_filter( $listing_classes ) ) );
						?>

						<div class="<?php echo esc_attr( trim($listing_class) ) ?>">

							<article class="kw-listing-item" data-longitude="<?php echo esc_attr( $post->geolocation_long ); ?>" data-latitude="<?php echo esc_attr( $post->geolocation_lat ); ?>">

								<?php knowhere_listing_media_output( array(
									'post' => $listings,
									'image_size' => 'knowhere-featured-image'
								) ); ?>

								<div class="kw-listing-item-info">

									<header class="kw-listing-item-header">

										<div class="kw-xs-table-row">

											<?php if ( get_option( 'job_manager_enable_types' ) && $job_type = wpjm_get_the_job_types( $post ) ) : ?>
												<div class="col-xs-6">
													<?php knowhere_bg_color_label( $job_type ); ?>
												</div>
											<?php endif; ?>

											<div class="col-xs-6 kw-right-edge">
												<span class="kw-listing-item-date"><?php the_job_publish_date($post); ?></span>
											</div>

										</div>

									</header>

									<h3 class="kw-listing-item-title"><a href="<?php the_job_permalink($post); ?>"><?php echo get_the_title(); ?></a></h3>

									<ul class="kw-listing-item-data kw-icons-list">

										<?php knowhere_job_listing_company($post) ?>

										<?php if ( get_the_job_location($post) ): ?>
											<li><span class="lnr icon-map-marker"></span><?php echo get_the_job_location($post); ?></li>
										<?php endif; ?>

										<?php knowhere_price_range_output($post); ?>

									</ul>

									<div class="kw-listing-item-description">
										<?php echo knowhere_get_excerpt( apply_filters( 'the_content', get_the_content() ), $limit ); ?>
									</div>

									<?php if ( candidates_can_apply() ) : ?>
										<?php get_job_manager_template( 'job-application.php' ); ?>
									<?php endif; ?>

								</div>

							</article>

						</div>

					<?php endwhile; wp_reset_postdata(); ?>

				</div><!--/ .kw-listings-->

			<?php endif; ?>

		</div><!--/ .kw-listings-holder-->

		<?php return ob_get_clean();
	}

}