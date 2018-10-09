<?php

class WPBakeryShortCode_VC_mad_listing_and_resumes extends WPBakeryShortCode {

	public $atts = array();
	public $listings = '';
	public $resumes = '';
	public $rand = '';
	public $current_types = array();

	protected function content( $atts, $content = null ) {

		$this->atts = shortcode_atts(array(
			'title' => '',
			'subtitle' => '',
			'title_color' => '',
			'subtitle_color' => '',
			'align_title' => '',
			'categories' => '',
			'number_of_items' => 10,

			'show_resumes' => '',
			'resumes_number_of_items' => 10,
			'resumes_orderby' => 'DESC',
		), $atts, 'vc_mad_listing_and_resumes');

		$this->rand = rand();

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

		$this->prepare_entries($params);

		if ( class_exists('WP_Resume_Manager') ) {

			if ( $show_resumes ) {

				$this->resumes = get_resumes( apply_filters( 'resume_manager_output_resumes_args', array(
					'posts_per_page'    => $resumes_number_of_items,
					'order'             => $resumes_orderby
				) ) );

			}

		}

	}

	public function prepare_entries($params) {
		$this->loop = array();

		if ( empty($params ) ) $params = $this->atts;
		if ( empty($this->listings) || empty($this->listings->posts) ) return;

		foreach ( $this->listings->posts as $key => $listing ) {

			if ( $current_item_types = get_the_terms( $listing->ID, 'job_listing_type' ) ) {
				if ( empty($current_item_types ) ) {
					continue;
				}
			}

			$this->loop[$key]['id'] = $id = $listing->ID;
			$this->loop[$key]['link'] = get_permalink($id);
			$this->loop[$key]['title'] = get_the_title($id);
			$this->loop[$key]['post'] = get_post($id);
			$this->loop[$key]['user_id'] = get_current_user_id();
			$this->loop[$key]['upload_url'] = get_the_author_meta( 'knowhere_cupp_upload_meta', $listing->post_author );

			if ( is_array($current_item_types ) ) {
				foreach ( $current_item_types as $current_item_type ) {
					$this->loop[$key]['term_id'] = $current_item_type->term_id;
				}
			}

		}


	}

	public function output_types( $listings, $params ) {

		$types_terms = get_terms(array(
			'taxonomy' => 'job_listing_type'
		));

		$current_types = array();

		foreach ( $listings as $listing ) {
			if ( $current_item_types = get_the_terms( $listing->ID, 'job_listing_type' ) ) {
				if ( !empty($current_item_types) ) {
					foreach ( $current_item_types as $current_item_type ) {
						$current_types[$current_item_type->term_id] = $current_item_type->term_id;
					}
				}
			}
		}

		$this->current_types = $current_types;

		?>

		<!-- - - - - - - - - - - - - - Tabs Navigation - - - - - - - - - - - - - - - - -->

		<ul class="kw-tabs-nav">

			<li><a href="#tab-<?php echo absint($this->rand) ?>"><?php esc_html_e('All', 'knowherepro') ?></a></li>

			<?php foreach ( $types_terms as $type ) : ?>

				<?php if ( in_array($type->term_id, $current_types) ): ?>
					<li><a href="#tab-<?php echo absint($type->term_id) ?>"><?php echo esc_html(trim($type->name)); ?></a></li>
				<?php endif; ?>

			<?php endforeach ?>

		</ul>

		<!-- - - - - - - - - - - - - - End of Tabs Navigation - - - - - - - - - - - - - - - - -->

		<?php
	}

	public function html() {

		if ( empty($this->listings) ) return;

		global $post;

		$atts = $this->atts;
		$listings = $this->listings;
		$resumes = $this->resumes;

		$title = !empty($atts['title']) ? $atts['title'] : '';
		$subtitle = !empty($atts['subtitle']) ? $atts['subtitle'] : '';
		$title_color = !empty($atts['title_color']) ? $atts['title_color'] : '';
		$subtitle_color = !empty($atts['subtitle_color']) ? $atts['subtitle_color'] : '';
		$align_title = !empty($atts['align_title']) ? $atts['align_title'] : '';

		$show_resumes = $atts['show_resumes'];

		ob_start(); ?>

		<div class="kw-listings-holder">

			<?php if ( $listings->have_posts() ): ?>

				<div class="kw-tabs kw-type-2">

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

								<li><a href="#tab-recent-jobs"><?php echo esc_html__('Recent Jobs', 'knowherepro') ?></a></li>

								<?php if ( class_exists('WP_Resume_Manager') ): ?>

									<?php if ( $show_resumes ): ?>
										<li><a href="#tab-recent-resumes"><?php echo esc_html__('Recent Resumes', 'knowherepro') ?></a></li>
									<?php endif; ?>

								<?php endif; ?>

							</ul>

						</div><!--/ .kw-right-col -->

					</header>

					<div class="kw-tabs-container">

						<!-- - - - - - - - - - - - - - Tab - - - - - - - - - - - - - - - - -->

						<div id="tab-recent-jobs" class="kw-tab">

							<!-- - - - - - - - - - - - - - Tabs - - - - - - - - - - - - - - - - -->

							<div class="kw-tabs kw-default">

								<?php echo $this->output_types( $this->listings->posts, $atts ); ?>

								<div class="kw-tabs-container">

									<div id="tab-<?php echo absint($this->rand) ?>" class="kw-tab">

										<div class="<?php echo esc_attr( trim(implode( ' ', array_filter( array( 'kw-listings', 'kw-list-view', 'kw-type-4' ) ) ) ) ) ?>">

											<?php while ( $listings->have_posts() ) : $listings->the_post();

												$listing_class = preg_replace( '/\s+/', ' ', implode( ' ', array_filter( array( 'kw-listing-item-wrap' ) ) ) );
												?>

												<div class="<?php echo esc_attr( trim($listing_class) ) ?>">

													<article class="kw-listing-item">

														<?php knowhere_listing_media_output(array('post' => $listings));  ?>

														<!-- - - - - - - - - - - - - - Description - - - - - - - - - - - - - - - - -->

														<div class="kw-listing-item-info kw-listing-style-4">

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

															<h3 class="kw-listing-item-title"><a href="<?php the_job_permalink($post); ?>"><?php echo get_the_title($post) ?></a></h3>

															<ul class="kw-listing-item-data kw-icons-list">

																<?php knowhere_job_listing_company( $post ); ?>

																<?php if ( get_the_job_location($post) ): ?>
																	<li><span class="lnr icon-map-marker"></span><?php echo get_the_job_location($post); ?></li>
																<?php endif; ?>

																<?php knowhere_price_range_output( $post->ID ) ?>

															</ul>

														</div>

														<!-- - - - - - - - - - - - - - End of Description - - - - - - - - - - - - - - - - -->

													</article>

												</div>

											<?php endwhile; wp_reset_postdata(); ?>

										</div><!--/ .kw-listings-->

									</div><!--/ .kw-tab-->

									<?php if ( !empty($this->current_types) ): ?>

										<?php $defaults = array(
											'id' => '',
											'link' => '',
											'title' => '',
											'term_id' => ''
										); ?>

										<?php foreach ( $this->current_types as $type_id ) : ?>

											<div id="tab-<?php echo absint($type_id) ?>" class="kw-tab">

												<div class="<?php echo esc_attr( trim(preg_replace( '/\s+/', ' ', implode( ' ', array_filter( array( 'kw-listings', 'kw-list-view', 'kw-type-4' ) ) ) ) ) ) ?>">

													<?php if ( !empty($this->loop) ): ?>

														<?php foreach ( $this->loop as $listing ): extract( array_merge( $defaults, $listing ) ) ?>

															<?php if ( $type_id == $term_id ):

																$listing_class = preg_replace( '/\s+/', ' ', implode( ' ', array_filter( array( 'kw-listing-item-wrap' ) ) ) );
																?>

																<div class="<?php echo esc_attr( trim($listing_class) ) ?>">

																	<article class="kw-listing-item">

																		<?php knowhere_listing_media_output(array('post' => $listing['post']));  ?>

																		<!-- - - - - - - - - - - - - - Description - - - - - - - - - - - - - - - - -->

																		<div class="kw-listing-item-info kw-listing-style-4">

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

																			<h3 class="kw-listing-item-title">
																				<a href="<?php the_job_permalink($post); ?>"><?php echo get_the_title($post) ?></a>
																			</h3>

																			<ul class="kw-listing-item-data kw-icons-list">

																				<?php knowhere_job_listing_company($post) ?>

																				<?php if ( get_the_job_location($post) ): ?>
																					<li><span class="lnr icon-map-marker"></span><?php echo get_the_job_location($post); ?></li>
																				<?php endif; ?>

																				<?php knowhere_price_range_output($post->ID) ?>

																			</ul>

																		</div>

																		<!-- - - - - - - - - - - - - - End of Description - - - - - - - - - - - - - - - - -->

																	</article>

																</div>

															<?php endif; ?>

														<?php endforeach; ?>

													<?php endif; ?>

												</div>

											</div><!--/ .kw-tab-->

										<?php endforeach; ?>

									<?php endif; ?>

								</div><!--/ .kw-tabs-container-->

							</div><!--/ .kw-tabs-->

						</div><!--/ .kw-tab-->

						<?php if ( class_exists('WP_Resume_Manager') ): ?>

							<?php if ( $show_resumes ): ?>

								<?php if ( $resumes->have_posts() ) : ?>

									<div id="tab-recent-resumes" class="kw-tab">

										<div class="<?php echo esc_attr( trim(preg_replace( '/\s+/', ' ', implode( ' ', array_filter( array( 'kw-listings', 'kw-list-view', 'kw-type-4' ) ) ) ) ) ) ?>">

											<?php while ( $resumes->have_posts() ) : $resumes->the_post(); ?>

												<div class="kw-listing-item-wrap">

													<article class="kw-listing-item">

														<!-- - - - - - - - - - - - - - Media - - - - - - - - - - - - - - - - -->

														<div class="kw-listing-item-media">

															<a href="<?php the_resume_permalink($post); ?>" class="kw-listing-item-thumbnail">
																<?php the_candidate_photo()  ?>
															</a>

														</div>

														<!-- - - - - - - - - - - - - - End of Media - - - - - - - - - - - - - - - - -->

														<!-- - - - - - - - - - - - - - Description - - - - - - - - - - - - - - - - -->

														<div class="kw-listing-item-info">

															<header class="kw-listing-item-header">
																<?php knowhere_the_job_publish_date() ?>
															</header>

															<div class="kw-listing-item-author-info">

																<a href="<?php the_resume_permalink($post); ?>" class="kw-listing-item-name"><?php the_title() ?></a>

																<h3 class="kw-listing-item-title"><?php the_candidate_title() ?></h3>

																<?php $category = get_the_resume_category(); ?>

																<?php if ( $category ) : ?>
																	<h6 class="knowhere-listing-resume-category"><?php echo esc_html($category) ?></h6>
																<?php endif; ?>

															</div>

															<ul class="kw-listing-item-data kw-icons-list">

																<?php if ( $post->_candidate_location ): ?>
																	<li><span class="lnr icon-map-marker"></span><?php the_candidate_location(false) ?></li>
																<?php endif; ?>

																<?php knowhere_job_salary($post); ?>

															</ul>

														</div>

														<!-- - - - - - - - - - - - - - End of Description - - - - - - - - - - - - - - - - -->

													</article>

												</div>

											<?php endwhile; ?>

										</div>

									</div><!--/ .kw-tab-->

								<?php endif; ?>

							<?php endif; ?>

						<?php endif; ?>

					</div><!--/ .kw-tabs-container-->

				</div><!--/ .kw-tabs-->

			<?php endif; ?>

		</div><!--/ .kw-listings-holder-->

		<?php return ob_get_clean();
	}

}