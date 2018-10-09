<?php

class WPBakeryShortCode_VC_mad_listing_cards extends WPBakeryShortCode {

	public $atts = array();
	public $listings = '';

	protected function content( $atts, $content = null ) {

		$this->atts = shortcode_atts(array(
			'title' => '',
			'subtitle' => '',
			'font_container' => 'h1',
			'title_color' => '',
			'subtitle_color' => '',
			'align_title' => '',
			'columns' => 3,
			'type' => 'kw-type-1',
			'sort' => false,
			'carousel' => '',
			'list_view' => false,
			'number_of_items' => 6,
			'show' => 'all',
			'orderby' => 'date',
			'categories' => '',
			'show_button' => false
		), $atts, 'vc_mad_listing_cards');

		$this->query_entries();
		$html = $this->html();

		return $html;
	}

	public function query_entries($params = array()) {

		if ( empty($params) ) $params = $this->atts;

		extract($params);

		$query_args = array(
			'post_type'   => 'job_listing',
			'post_status' => 'publish'
		);

		if ( ! empty( $number_of_items ) && is_numeric( $number_of_items ) ) {
			$query_args['posts_per_page'] = $number_of_items;
		}

		if ( ! empty( $orderby ) && is_string( $orderby ) ) {
			$query_args['orderby'] = $orderby;
		}

		if ( ! empty( $show ) && $show === 'featured' ) {
			$query_args['featured']   = '=';
		}

		if ( ! empty( $categories ) && is_string( $categories ) ) {
			$categories = explode( ',', $categories );
			$query_args['search_categories'] = $categories;
		}

		$this->listings = get_job_listings( $query_args );

	}

	public function get_reviews($id) {
		global $wpdb;
		$result = array();

		$post_meta = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE post_id = $id AND meta_key = 'rwp_reviews';", ARRAY_A );

		if( ! is_array( $post_meta ) ) {
			return $result;
		}

		foreach( $post_meta as $meta ) {

			$reviews = unserialize( $meta['meta_value'] );

			if( ! is_array( $reviews ) ) {
				continue;
			}

			foreach( $reviews as $review ){
				$review['review_post_id'] = $meta['post_id'];
				$result[] = $review;
			}

		}

		return $result;
	}

	function sort_links( $listings, $params ) {

		$categories = get_categories(array(
			'taxonomy'	=> 'job_listing_category',
			'hide_empty'=> 0
		));
		$current_cats = array();
		$display_cats = is_array($params['categories']) ? $params['categories'] : array_filter(explode(',', $params['categories']));
		$show_button = $params['show_button'] == true ? true : false;
		$align_title = !empty($params['align_title']) ? $params['align_title'] : '';

		foreach ( $listings as $listing ) {
			if ( $current_item_cats = get_the_terms( $listing->ID, 'job_listing_category' ) ) {
				if ( !empty($current_item_cats) ) {
					foreach ( $current_item_cats as $current_item_cat ) {
						if ( empty($display_cats) || in_array($current_item_cat->slug, $display_cats) ) {
							$current_cats[$current_item_cat->slug] = $current_item_cat->slug;
						}
					}
				}
			}
		}

		ob_start(); ?>

		<header class="kw-section-header">

			<div class="kw-left-col">

				<div class="kw-filter" id="listings-filter">

					<a href="javascript:void(0)" data-filter="*" class="kw-filter-item kw-active"><?php esc_html_e('All', 'knowherepro') ?></a>

					<?php foreach ( $categories as $category ): ?>

						<?php if ( in_array($category->slug, $current_cats) ): ?>
							<?php $nicename = str_replace('%', '', $category->category_nicename); ?>
							<a href="javascript:void(0)" data-filter=".<?php echo esc_attr($nicename) ?>" class="kw-filter-item"><?php echo esc_html(trim($category->cat_name)); ?></a>
						<?php endif; ?>

					<?php endforeach ?>

				</div><!--/ .kw-filter-->

			</div><!--/ .kw-left-col -->

			<div class="kw-right-col">

			<?php if ( $align_title == 'align-left' && $show_button ): ?>
				<a class="kw-btn-medium kw-gray" target="_blank" href="<?php echo knowhere_get_listings_page_url(); ?>"><?php esc_html_e('View All', 'knowherepro') ?></a>
			<?php endif; ?>

			</div><!--/ .kw-right-col -->

		</header>

		<?php return ob_get_clean();
	}

	public function get_sort_class( $id ) {
		$classes = "";
		$item_categories = get_the_terms( $id, 'job_listing_category' );
		if ( is_object($item_categories) || is_array($item_categories) ) {
			foreach ( $item_categories as $cat ) {
				$classes .= $cat->slug . ' ';
			}
		}
		return str_replace( '%', '', $classes );
	}

	public function html() {

		if ( empty($this->listings) ) return;

		global $post, $knowhere_settings;
		$atts = $this->atts;
		$listings = $this->listings;

		$list_view = $atts['list_view'] ? true : false;
		$font_container = $atts['font_container'];
		$title = !empty($atts['title']) ? $atts['title'] : '';
		$subtitle = !empty($atts['subtitle']) ? $atts['subtitle'] : '';
		$title_color = !empty($atts['title_color']) ? $atts['title_color'] : '';
		$subtitle_color = !empty($atts['subtitle_color']) ? $atts['subtitle_color'] : '';
		$align_title = !empty($atts['align_title']) ? $atts['align_title'] : '';
		$columns = !empty($atts['columns']) ? absint($atts['columns']) : 5;
		$type = !empty($atts['type']) ? $atts['type'] : '';
		$sort = !empty($atts['sort']) ? $atts['sort'] : false;
		$carousel = $atts['carousel'] == true ? true : false;
		$show_button = $atts['show_button'] == true ? true : false;
		$card_image = 'knowhere-card-image';

		$attributes = array();
		$css_classes = array(
			'kw-listings', $type
		);

		if ( !$list_view ) {
			$css_classes[] = 'kw-cols-' . $columns;
		}

		if ( $carousel && $type !== 'kw-type-3' ) {
			$css_classes[] = 'kw-listings-carousel-v1';
			$css_classes[] = 'owl-carousel';
			$attributes['columns'] = $columns;
		} elseif ( $carousel && $type == 'kw-type-3' ) {
			$css_classes[] = 'kw-featured-properties';
			$css_classes[] = 'owl-carousel';
			$attributes['columns'] = $columns;
		} elseif ( $carousel && $type == 'kw-type-5' ) {
			$css_classes[] = 'kw-listings-carousel-v3';
			$css_classes[] = 'owl-carousel';
		}

		if ( $list_view ) {
			$css_classes[] = 'kw-list-view';
		} else {
			$css_classes[] = 'kw-grid-view';
		}

		if ( $sort ) {
			$css_classes[] = 'kw-isotope';
			$css_classes[] = 'kw-loading';
			$attributes['sort'] = 'true';
		}

		$css_class = preg_replace( '/\s+/', ' ', implode( ' ', array_filter( $css_classes ) ) );

		ob_start(); ?>

		<div class="wpb_content_element">

			<div class="kw-listings-holder">

			<?php if ( $align_title == 'align-left' && !$sort && $show_button ): ?>

				<header class="kw-section-header">

					<div class="kw-left-col">

						<?php
						echo Knowhere_Vc_Config::getParamTitle(
							array(
								'heading' => $font_container,
								'title' => $title,
								'subtitle' => $subtitle,
								'title_color' => $title_color,
								'subtitle_color' => $subtitle_color
							)
						);
						?>

					</div><!--/ .kw-left-col -->

					<div class="kw-right-col">
						<a class="kw-btn-small kw-gray" href="<?php echo get_post_type_archive_link('job_listing') ?>"><?php esc_html_e('View All', 'knowherepro') ?></a>
					</div><!--/ .kw-right-col -->

				</header><!--/ .kw-section-header-->

			<?php else: ?>

				<?php
				echo Knowhere_Vc_Config::getParamTitle(
					array(
						'heading' => $font_container,
						'title' => $title,
						'subtitle' => $subtitle,
						'title_color' => $title_color,
						'subtitle_color' => $subtitle_color,
						'align_title' => $align_title
					)
				);
				?>

			<?php endif; ?>

			<?php if ( $listings->have_posts() ): ?>

				<?php echo ( $sort ) ? $this->sort_links( $this->listings->posts, $atts ) : ""; ?>

				<div class="<?php echo esc_attr( trim($css_class) ) ?>" <?php echo knowhere_create_data_string($attributes) ?>>

					<?php while ( $listings->have_posts() ) : $listings->the_post();

						$listing_type = get_the_terms( get_the_ID(), 'job_listing_type' );
						$terms = get_the_terms( get_the_ID(), 'job_listing_category' );

						$listing_classes = array(
							'kw-listing-item-wrap'
						);

						if ( $sort ) {
							$listing_classes[] = $this->get_sort_class( get_the_ID() );
						}

						if ( $type == 'kw-type-2' ) {

							$image_size = get_post_meta( get_the_ID(), 'knowhere_job_image_size', true );

							switch ( $image_size ) {
								case 'medium': 		$card_image = 'knowhere-card-image';	   		 break;
								case 'large': 		$card_image = 'knowhere-card-image-large'; 		 break;
								case 'extra-large': $card_image = 'knowhere-card-image-extra-large'; break;
								default: 			$card_image = 'knowhere-card-image'; 			 break;
							}

							$listing_classes[] = $image_size;

						}

						$post_image_src = knowhere_get_post_image_src( $post->ID, $card_image );

						$listing_class = preg_replace( '/\s+/', ' ', implode( ' ', array_filter( $listing_classes ) ) );
					?>

						<div class="<?php echo esc_attr( trim($listing_class) ) ?>">

							<?php if ( $type == 'kw-type-1' || $type == 'kw-type-2' ): ?>

								<article <?php job_listing_class('kw-listing-item'); ?> data-longitude="<?php echo esc_attr( $post->geolocation_long ); ?>" data-latitude="<?php echo esc_attr( $post->geolocation_lat ); ?>">

									<?php if ( !empty($post_image_src) ): ?>

										<!-- - - - - - - - - - - - - - Media - - - - - - - - - - - - - - - - -->

										<div class="kw-listing-item-media">

											<?php knowhere_label_hours_output( get_the_ID() ); ?>

											<?php knowhere_label_featured( get_the_ID() ); ?>

											<a href="<?php the_job_permalink(); ?>" class="kw-listing-item-thumbnail">
												<img src="<?php echo esc_url($post_image_src); ?>" alt="">
											</a>

											<ul class="kw-listing-card-meta">
												<?php if ( ! is_wp_error( $terms ) && ( is_array( $terms ) || is_object( $terms ) ) ) { ?>

													<?php $i = 0; $j = 0; ?>

													<?php foreach ( $terms as $term ) {
														$icon_url      = knowhere_get_term_icon_url( $term->term_id );
														$attachment_id = knowhere_get_term_icon_id( $term->term_id );

														if ( $type == 'kw-type-2' ) {
															if ( $i > 0 ) continue;
														}

														if ( $j > 3 ) continue;

														if ( empty( $icon_url ) ) {
															continue;
														} ?>


														<li class="kw-listing-term-list">
															<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" title="<?php echo sprintf('%s', $term->name ) ?>" class="kw-listing-item-icon">
																<?php knowhere_display_icon_or_image( $icon_url, '', true, $attachment_id ); ?>
															</a>
														</li>

														<?php $i++; $j++; ?>

													<?php } ?>
												<?php } ?>

												<li>
													<a href="<?php the_job_permalink(); ?>" class="kw-listing-item-like">
														<span class="lnr icon-heart"></span>
													</a>
												</li>

											</ul><!--/ .kw-listing-card-meta-->

										</div>

										<!-- - - - - - - - - - - - - - End of Media - - - - - - - - - - - - - - - - -->

									<?php endif; ?>

									<!-- - - - - - - - - - - - - - Description - - - - - - - - - - - - - - - - -->

									<div class="kw-listing-item-info">

										<header class="kw-listing-item-header">

											<h3 class="kw-listing-item-title">
												<a href="<?php the_job_permalink(); ?>"><?php the_title(); ?></a>
											</h3>

											<div class="kw-card-rating">
												<?php knowhere_job_listing_rating() ?>
											</div>

										</header>

										<ul class="kw-listing-item-data kw-icons-list">
											<li class="kw-listing-item-location"><span class="lnr icon-map-marker"></span><?php echo get_the_job_location(); ?></li>
											<li class="kw-listing-item-phone"><span class="lnr icon-telephone"></span><?php echo get_post_meta( $post->ID, '_company_phone', true); ?></li>
										</ul>

									</div>

									<!-- - - - - - - - - - - - - - End of Description - - - - - - - - - - - - - - - - -->

								</article>

							<?php elseif ( $type == 'kw-type-3' ): ?>

								<article <?php job_listing_class('kw-listing-item'); ?> data-longitude="<?php echo esc_attr( $post->geolocation_long ); ?>" data-latitude="<?php echo esc_attr( $post->geolocation_lat ); ?>">

									<?php $photos = knowhere_get_listing_gallery_ids(); ?>

									<?php if ( $photos || !empty($post_image_src) ): ?>

										<!-- - - - - - - - - - - - - - Media - - - - - - - - - - - - - - - - -->

										<div class="kw-listing-item-media">

											<?php if ( $photos && count($photos) > 1 ): ?>

												<div class="kw-property-slideshow owl-carousel">

													<?php foreach ( $photos as $key => $photo_id ):
														$src = wp_get_attachment_image_src( $photo_id, $card_image ); ?>
														<a href="<?php the_job_permalink() ?>"><img src="<?php echo esc_url($src[0]); ?>" alt=""/></a>
													<?php endforeach; ?>

												</div><!--/ .kw-property-slideshow-->

											<?php elseif ( !empty($post_image_src) ): ?>

												<a href="<?php the_job_permalink(); ?>" class="kw-listing-item-thumbnail">
													<img src="<?php echo esc_url($post_image_src); ?>" alt="">
												</a>

											<?php endif; ?>

											<?php knowhere_label_hours_output( get_the_ID() ) ?>

											<?php knowhere_label_featured( get_the_ID(), array( 'classes' => array('kw-right-aligned') ) ); ?>

											<?php
											if ( is_array( $listing_type ) || is_object( $listing_type ) ) {
												$firstType = $listing_type[0];
												if ( ! $firstType == NULL ) {
													$name = $firstType->name;
													echo '<span class="kw-label-type">'. esc_html($name) .'</span>';
												}
											}
											?>

										</div><!--/ .kw-listing-item-media-->

										<!-- - - - - - - - - - - - - - End of Media - - - - - - - - - - - - - - - - -->

									<?php endif; ?>

									<!-- - - - - - - - - - - - - - Description - - - - - - - - - - - - - - - - -->

									<div class="kw-listing-item-info">

										<div class="kw-listing-meta">

											<?php if ( ! is_wp_error( $terms ) && ( is_array( $terms ) || is_object( $terms ) ) ): ?>

												<ul class="kw-listing-cats">
													<?php echo get_the_term_list( get_the_ID(), 'job_listing_category', '<li>', ', ', '</li>' ); ?>
												</ul><!--/ .kw-listing-cats-->

											<?php endif; ?>

											<?php if ( $knowhere_settings['job-type-fields'] == 'property' ): ?>

												<?php $open_date = get_post_meta( get_the_ID(), '_open_house_date', true ); ?>

												<?php if ( !empty($open_date) ): ?>

													<?php $new_open_date = date( "d M", strtotime($open_date) ); ?>

													<?php if ( !empty($new_open_date) ): ?>
														<div class="kw-listing-pubdate"><?php echo esc_html__('Open At', 'knowherepro') . ' ' . $new_open_date ?></div>
													<?php endif; ?>

												<?php endif; ?>

											<?php endif; ?>

										</div><!--/ .kw-listing-meta-->

										<h4 class="kw-item-title">
											<a href="<?php the_job_permalink(); ?>"><?php the_title(); ?></a>
										</h4>

										<?php if ( knowhere_get_invoice_price( get_the_ID() ) ): ?>
											<h3 class="kw-listing-price">
												<?php echo knowhere_get_invoice_price( get_the_ID() ) ?>
											</h3>
										<?php endif; ?>

										<div class="kw-listing-address"><?php echo get_the_job_location(); ?></div>

										<?php if ( $knowhere_settings['job-type-fields'] == 'property' ): ?>
											<?php echo knowhere_get_property_features( get_the_ID() ) ?>
										<?php endif; ?>

									</div><!--/ .kw-listing-item-info-->

									<!-- - - - - - - - - - - - - - End of Description - - - - - - - - - - - - - - - - -->

									<footer class="kw-listing-extra">

										<div class="kw-listing-extra-container">

											<div class="kw-extended-link">
												<a href="<?php the_job_permalink() ?>"><?php esc_html_e( 'Details', 'knowherepro' ) ?></a>
											</div><!--/ .kw-extended-link -->

											<div class="kw-action-col">

												<?php $id = rand(200, 500); ?>

												<a class="kw-listing-action kw-share-popup-link" href="#kw-share-popup-<?php echo absint($id) ?>"><span class="lnr icon-share2"></span></a>

												<div id="kw-share-popup-<?php echo absint($id) ?>" class="kw-share-popup mfp-hide">
													<?php if ( function_exists('knowhere_job_single_share') ): ?>
														<?php knowhere_job_single_share(); ?>
													<?php endif; ?>
												</div>

											</div>

											<div class="kw-action-col">
												<?php knowhere_add_bookmark_heart_to_content( $post ) ?>
											</div>

										</div><!--/ .kw-listing-extra-container -->

									</footer><!--/ .kw-listing-extra-->

								</article><!--/ .kw-listing-item-->

							<?php elseif ( $type == 'kw-type-5' ): ?>

								<article <?php job_listing_class('kw-listing-item'); ?> data-longitude="<?php echo esc_attr( $post->geolocation_long ); ?>" data-latitude="<?php echo esc_attr( $post->geolocation_lat ); ?>">

									<!-- - - - - - - - - - - - - - Media - - - - - - - - - - - - - - - - -->

									<?php if ( !empty($post_image_src) ): ?>

										<!-- - - - - - - - - - - - - - Media - - - - - - - - - - - - - - - - -->

										<div class="kw-listing-item-media kw-listing-style-5">

											<?php knowhere_label_hours_output( get_the_ID() ); ?>

											<?php knowhere_label_featured( get_the_ID() ); ?>

											<a href="<?php the_job_permalink(); ?>" class="kw-listing-item-thumbnail">
												<img src="<?php echo esc_url($post_image_src); ?>" alt="">
											</a>

											<a href="<?php the_job_permalink(); ?>" class="kw-listing-item-like">
												<span class="lnr icon-heart"></span>
											</a>

											<?php $photos = knowhere_get_listing_gallery_ids(); ?>

											<?php if ( $photos ): ?>
												<div class="kw-listing-item-photo-amount"><?php echo absint(count($photos)) ?></div>
											<?php endif; ?>

										</div><!--/ .kw-listing-item-media-->

										<!-- - - - - - - - - - - - - - End of Media - - - - - - - - - - - - - - - - -->

									<?php endif; ?>

									<!-- - - - - - - - - - - - - - Description - - - - - - - - - - - - - - - - -->

									<div class="kw-listing-item-info kw-listing-style-5">

										<header class="kw-listing-item-header">

											<?php if ( !is_wp_error( $terms ) && ( is_array( $terms ) || is_object( $terms ) ) ) { ?>

												<div class="kw-listing-item-categories">
													<?php echo get_the_term_list( get_the_ID(), 'job_listing_category', '', ', ', '' ); ?>
												</div>

											<?php } ?>

											<h3 class="kw-listing-item-title">
												<a href="<?php the_job_permalink(); ?>"><?php the_title(); ?></a>
											</h3>

										</header>

										<?php if ( knowhere_get_the_company_description() ) : ?>
											<div class="kw-listing-item-description">
												<?php knowhere_the_company_description(); ?>
											</div>
										<?php endif; ?>

										<?php echo knowhere_the_job_publish_date(); ?>

										<footer class="kw-listing-item-footer">

											<div class="kw-xs-table-row row">

												<div class="col-xs-5">
													<?php if ( knowhere_get_invoice_price( get_the_ID(), get_post_meta( get_the_ID(), '_job_price_range_min', true ) ) ): ?>
														<strong class="kw-listing-item-price">
															<?php echo knowhere_get_invoice_price( get_the_ID(), get_post_meta( get_the_ID(), '_job_price_range_min', true ) ) ?>
														</strong>
													<?php endif; ?>
												</div>

												<div class="col-xs-7 kw-right-edge">
													<?php $job_location = get_post_meta( get_the_ID(), 'geolocation_city', true); ?>
													<?php if ( !empty($job_location) ): ?>
														<ul class="kw-listing-item-data kw-icons-list">
															<li><span class="lnr icon-map-marker"></span><?php echo trim( $job_location ); ?></li>
														</ul>
													<?php endif; ?>
												</div>

											</div>

										</footer><!--/ .kw-listing-item-footer-->

									</div><!--/ .kw-listing-item-info-->

									<!-- - - - - - - - - - - - - - End of Description - - - - - - - - - - - - - - - - -->

								</article>

							<?php endif; ?>

						</div><!--/ .kw-listing-item-wrap-->

					<?php endwhile; ?>

					<?php wp_reset_postdata(); ?>

				</div><!--/ .kw-listings-->

				<div class="align-center">
					<a class="kw-btn kw-theme-color kw-medium" href="<?php echo knowhere_get_listings_page_url() ?>"><?php esc_html_e('View All', 'knowherepro') ?></a>
				</div>

			<?php endif; ?>

			</div><!--/ .kw-listings-holder-->

		</div>

		<?php return ob_get_clean();
	}

}