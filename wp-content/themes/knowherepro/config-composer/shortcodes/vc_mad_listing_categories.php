<?php

class WPBakeryShortCode_VC_mad_listing_categories extends WPBakeryShortCode {

	public $atts = array();
	public $term_list_category = '';
	public $term_list_region = '';
	public $custom_category_labels = '';

	protected function content( $atts, $content = null ) {

		$this->atts = shortcode_atts(array(
			'title' => '',
			'subtitle' => '',
			'title_color' => '',
			'subtitle_color' => '',
			'align_title' => '',
			'type' => 'kw-type-1',
			'columns' => 5,
			'number_of_items' => 10,
			'childs_number_of_items' => 5,
			'columns_per_page' => 3,
			'orderby' => 'name',
			'categories_slug' => '',
			'categories' => '',
			'by_location' => '',
			'region_number_of_items' => 10
		), $atts, 'vc_mad_listing_categories');

		$this->query_entries_job_listing_category();

		if ( $this->atts['by_location'] ) {
			$this->query_entries_job_listing_region();
		}

		$html = $this->html();

		return $html;
	}

	public function assemble_terms_childs( $term ) {
		$res = new stdClass();

		$res->term_id = $term->term_id;
		$res->name = $term->name;
		$res->slug = $term->slug;
		$res->parent = $term->parent;
		$res->count = $term->count;
		$res->taxonomy = $term->taxonomy;
		$res->childs = get_term_children( $term->term_id, 'job_listing_category' );

		return $res;
	}

	public function query_entries_job_listing_category( $params = array() ) {

		if ( empty($params) ) $params = $this->atts;

		$term_list = array();

		extract($params);

		$query_args = array( 'order' => 'DESC', 'hide_empty' => false, 'hierarchical' => true, 'pad_counts' => true );
		if ( ! empty( $orderby ) && is_string( $orderby ) ) {
			$query_args['orderby'] = $orderby;
		}

		if ( ! empty( $categories ) && is_string( $categories ) ) {

			$categories = explode( ',', $categories );

			foreach ( $categories as $key => $cat ) {
				$categories[ $key ] = sanitize_title( $cat );
			}

			$query_args['slug'] = $categories;

		}

		$all_terms = get_terms(
			'job_listing_category',
			$query_args
		);

		if ( is_wp_error( $all_terms ) ) {
			return;
		}

		//now create an array with the category slug as key so we can reference/search easier
		$all_categories = array();
		foreach ( $all_terms as $key => $term ) {
			$all_categories[ $term->slug ] = $this->assemble_terms_childs($term);
		}

		if ( ! $number_of_items = intval( $number_of_items ) ) {
			$number_of_items = 10;
		}

		$term_list = array_slice( $all_categories, 0, $number_of_items );

		$this->term_list_category = $term_list;

	}

	public function query_entries_job_listing_region( $params = array() ) {

		if ( empty($params) ) $params = $this->atts;

		$term_list = array();
		extract($params);

		$query_args = array( 'order' => 'DESC', 'hide_empty' => false, 'hierarchical' => true, 'pad_counts' => true );

		$all_terms = get_terms(
			'job_listing_region',
			$query_args
		);

		if ( is_wp_error( $all_terms ) ) {
			return;
		}

		//now create an array with the category slug as key so we can reference/search easier
		$all_regions = array();
		foreach ( $all_terms as $key => $term ) {
			$all_regions[ $term->slug ] = $term;
		}

		$term_list = array_slice( $all_regions, 0, $region_number_of_items );
		$this->term_list_region = $term_list;

	}

	public function html() {

		if ( empty($this->term_list_category) ) return;

		$atts = $this->atts;
		$term_list_category = $this->term_list_category;

		$title = !empty($atts['title']) ? $atts['title'] : '';
		$subtitle = !empty($atts['subtitle']) ? $atts['subtitle'] : '';
		$title_color = !empty($atts['title_color']) ? $atts['title_color'] : '';
		$subtitle_color = !empty($atts['subtitle_color']) ? $atts['subtitle_color'] : '';
		$align_title = !empty($atts['align_title']) ? $atts['align_title'] : '';
		$type = !empty($atts['type']) ? $atts['type'] : 'kw-type-1';
		$childs_number_of_items = !empty($atts['childs_number_of_items']) ? $atts['childs_number_of_items'] : 5;
		$columns = absint($atts['columns']);
		$columns_per_page = $atts['columns_per_page'];
		$by_location = $atts['by_location'];

		$css_classes = array(
			'kw-categories', $type, 'kw-cols-' . $columns
		);

		$wrapper_classes = array();

		if ( class_exists('Astoundify_Job_Manager_Regions') && $by_location ) {
			$wrapper_classes[] = 'kw-tabs kw-type-2';
		}

		$css_class = preg_replace( '/\s+/', ' ', implode( ' ', array_filter( $css_classes ) ) );

		ob_start(); ?>

		<div class="<?php echo implode( ' ', array_filter( $wrapper_classes ) )  ?>">

			<?php if ( class_exists('Astoundify_Job_Manager_Regions') && $by_location ): ?>

				<header class="kw-section-header kw-type-2">

					<div class="kw-left-col">

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

					</div><!--/ .kw-left-col -->

					<div class="kw-right-col">
						<ul class="kw-tabs-nav">
							<li><a href="#tab-by-category"><?php echo esc_html__('By Category', 'knowherepro') ?></a></li>
							<li><a href="#tab-by-location"><?php echo esc_html__('By Location', 'knowherepro') ?></a></li>
						</ul>
					</div>

				</header>

			<?php else: ?>

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

			<?php endif; ?>

			<div class="kw-tabs-container">

				<div id="tab-by-category" class="kw-tab">

					<div class="<?php echo esc_attr( trim($css_class) ) ?>">

						<div class="kw-categories-inner">

							<?php if ( $type == 'kw-type-3' ): ?>

								<?php $term_list_chunk = array_chunk($term_list_category, $columns_per_page); ?>

								<?php foreach( $term_list_chunk as $list_chunk ): ?>

									<div class="kw-category-item">

										<ul class="kw-categories-item-list">

											<?php foreach ( $list_chunk as $key => $term ) :
												if ( ! $term ) {
													continue;
												} ?>

												<li>
													<a href="<?php echo esc_url( get_term_link( $term->term_id ) ); ?>">
														<?php echo isset( $this->custom_category_labels[ $term->slug ] ) ? $this->custom_category_labels[ $term->slug ] : $term->name; ?>
														<span class="kw-amount"><?php echo absint($term->count) ?></span>
													</a>
												</li>

											<?php endforeach; ?>

										</ul>

									</div>

								<?php endforeach; ?>

							<?php elseif ( $type == 'kw-type-4' ): ?>

								<?php foreach ( $term_list_category as $key => $term ) :
									if ( ! $term ) {
										continue;
									}

									$icon_url = knowhere_get_term_icon_url( $term->term_id );
									$attachment_id = knowhere_get_term_icon_id( $term->term_id );
									?>

									<article class="kw-category-item">

										<header class="kw-category-item-header">

											<h2 class="kw-category-item-title kw-category-icon-color-<?php echo esc_attr($term->term_id) ?>">
												<a href="<?php echo esc_url( get_term_link( $term->term_id ) ); ?>">
													<?php if ( ! empty( $icon_url ) ) : ?>
														<span class="kw-category-item-icon">
															<?php knowhere_display_icon_or_image( $icon_url, '', true, $attachment_id ); ?>
														</span>
													<?php endif; ?>

													<?php echo isset( $this->custom_category_labels[ $term->slug ] ) ? $this->custom_category_labels[ $term->slug ] : $term->name; ?>
												</a>
											</h2>

										</header>

										<?php if ( !empty($term->childs) ): ?>

											<?php
												$term_slices = $term->childs;
												$count_childs = count($term_slices);
											?>

											<ul class="kw-categories-item-list">

												<?php $i = 0; ?>

												<?php foreach ( $term_slices as $child ): ?>

													<?php $term = get_term_by( 'id', $child, 'job_listing_category' ); ?>
														<li <?php if ( $i >= $childs_number_of_items ): ?>class="kw-list-item"<?php endif; ?>><a href="<?php echo esc_url(get_term_link( $term, 'job_listing_category' )) ?>"><?php echo esc_html($term->name) ?> <span class="kw-amount"><?php echo absint($term->count) ?></span></a></li>
													<?php $i++; ?>

												<?php endforeach; ?>

												<?php if ( $childs_number_of_items < $count_childs ): ?>
													<li><a class="kw-show-more-list-button" data-show="more" href="javascript:void(0)"><?php esc_html_e('Show More', 'knowherepro') ?></a></li>
												<?php endif; ?>

											</ul>

										<?php endif; ?>

									</article>

								<?php endforeach; ?>

							<?php elseif ( $type == 'kw-type-5' ): ?>

								<?php foreach ( $term_list_category as $key => $term ) :
									if ( ! $term ) {
										continue;
									}

									$image_src = '';
									$icon_url = knowhere_get_term_icon_url( $term->term_id );
									$attachment_id = knowhere_get_term_icon_id( $term->term_id );
									$thumbargs    = array(
										'posts_per_page' => 1,
										'post_type'      => 'job_listing',
										'meta_key'       => 'main_image',
										'orderby'          => 'rand',
										'tax_query'      => array(
											array(
												'taxonomy' => 'job_listing_category',
												'field'    => 'slug',
												'terms'    => $term->slug
											),
										)
									);
									$latest_thumb = new WP_Query( $thumbargs );

									if ( $latest_thumb->have_posts() ) {
										$image_ID  = knowhere_get_post_image_id( $latest_thumb->post->ID );
										$image_src = '';
										if ( ! empty( $image_ID ) ) {
											$image     = wp_get_attachment_image_src( $image_ID, 'knowhere-card-extra' );
											$image_src = $image[0];
										}
									}
									?>

									<article class="kw-category-item" <?php echo empty( $icon_url ) ? 'class="kw-no-icon"' : ''; ?>>

										<div class="kw-category-cover" style="background-image: url(<?php echo esc_url($image_src); ?>)">

											<a href="<?php echo esc_url( get_term_link( $term->term_id ) ); ?>">

												<header class="kw-category-item-header">

													<h2 class="kw-category-item-title kw-category-icon-color-<?php echo esc_attr($term->term_id) ?>">
														<?php if ( ! empty( $icon_url ) ) : ?>
															<span class="kw-category-item-icon">
																<?php knowhere_display_icon_or_image( $icon_url, '', true, $attachment_id ); ?>
															</span>
														<?php endif; ?>

														<div class="kw-category-text">
															<?php echo isset( $this->custom_category_labels[ $term->slug ] ) ? $this->custom_category_labels[ $term->slug ] : $term->name; ?>

															<span class="kw-amount"><?php printf(_n('%s listing', '%s listings', absint($term->count), 'knowherepro'), absint($term->count)); ?></span>
														</div>

													</h2>

												</header>

											</a>

										</div>

									</article>

								<?php endforeach; ?>

							<?php else: ?>

								<?php foreach ( $term_list_category as $key => $term ) :
									if ( ! $term ) {
										continue;
									}

									$icon_url = knowhere_get_term_icon_url( $term->term_id );
									$attachment_id = knowhere_get_term_icon_id( $term->term_id );
									?>

									<article class="kw-category-item">

										<header class="kw-category-item-header">

											<h2 class="kw-category-item-title kw-category-icon-color-<?php echo esc_attr($term->term_id) ?>">
												<a href="<?php echo esc_url( get_term_link( $term->term_id ) ); ?>">
													<?php if ( ! empty( $icon_url ) ) : ?>
														<span class="kw-category-item-icon">
															<?php knowhere_display_icon_or_image( $icon_url, '', true, $attachment_id ); ?>
														</span>
													<?php endif; ?>

													<?php echo isset( $this->custom_category_labels[ $term->slug ] ) ? $this->custom_category_labels[ $term->slug ] : $term->name; ?>
												</a>
											</h2>

										</header>

										<?php if ( $type == 'kw-type-2' ): ?>

											<?php if ( !empty($term->childs) ): ?>

												<ul class="kw-categories-item-list">

													<?php foreach ( $term->childs as $child ): ?>

														<?php $term = get_term_by( 'id', $child, 'job_listing_category' ); ?>

														<li><a href="<?php echo esc_url(get_term_link( $term, 'job_listing_category' )) ?>"><?php echo esc_html($term->name) ?><span class="kw-amount"><?php echo absint($term->count) ?></span></a></li>

													<?php endforeach; ?>

												</ul>

											<?php endif; ?>

										<?php endif; ?>

									</article>

								<?php endforeach; ?>

							<?php endif; ?>

						</div><!--/ .kw-categories-inner-->

					</div><!--/ .kw-categories-->

				</div>

				<?php if ( class_exists('Astoundify_Job_Manager_Regions') ): ?>

					<?php if ( $by_location ): ?>

						<div id="tab-by-location" class="kw-tab">

							<div class="<?php echo esc_attr( trim($css_class) ) ?>">

								<div class="kw-categories-inner">

									<?php $term_list_chunk_region = array_chunk($this->term_list_region, $columns_per_page); ?>

									<?php foreach( $term_list_chunk_region as $list_chunk ): ?>

										<article class="kw-category-item">

											<ul class="kw-categories-item-list">

												<?php foreach ( $list_chunk as $key => $term ) :
													if ( ! $term ) {
														continue;
													} ?>

													<li>
														<a href="<?php echo esc_url( get_term_link( $term->term_id ) ); ?>">
															<?php echo esc_html($term->name); ?>
															<span class="kw-amount"><?php echo absint($term->count) ?></span>
														</a>
													</li>
												<?php endforeach; ?>

											</ul>

										</article>

									<?php endforeach; ?>

								</div><!--/ .kw-categories-inner-->

							</div><!--/ .kw-categories-->

						</div>

					<?php endif; ?>

				<?php endif; ?>

			</div><!--/ .kw-tabs-container-->

		</div><!--/ .kw-tabs-->

		<?php return ob_get_clean();
	}

}