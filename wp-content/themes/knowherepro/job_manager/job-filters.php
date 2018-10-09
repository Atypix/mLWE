
<?php global $knowhere_settings; ?>

<?php if ( knowhere_using_facetwp() ) : ?>

	<form class="job_filters">

		<div class="search_jobs">

			<?php
			$facets = knowhere_get_facets( 'listings_archive' );

			if ( ! empty( $facets ) ) {
				echo knowhere_get_display_facets( $facets );
			}
			?>

			<?php do_action( 'job_manager_job_filters_search_jobs_end', $atts ); ?>

			<div class="clear"></div>

		</div><!--/ .search_jobs-->

		<div class="kw-touch-buttons">

			<button class="kw-button-filter">
				<?php esc_html_e('Filter', 'knowherepro'); ?>
				<span><?php esc_html_e('Listings', 'knowherepro'); ?></span>
			</button>

			<button class="kw-button-view kw-button-view-map">
				<span><?php esc_html_e('Map View', 'knowherepro'); ?></span>
			</button>

			<button class="kw-button-view kw-button-view-cards">
				<span><?php esc_html_e('Cards View', 'knowherepro'); ?> </span>
			</button>

		</div><!--/ .kw-touch-buttons-->

		<?php do_action( 'job_manager_job_filters_end', $atts ); ?>

	</form>

<?php else : ?>

	<?php wp_enqueue_script( 'wp-job-manager-ajax-filters' ); ?>

	<?php do_action( 'job_manager_job_filters_before', $atts ); ?>

	<?php $job_tags = get_terms( array( 'taxonomy' => 'job_listing_tag', 'hierarchical' => 1 ) ); ?>

	<?php
	$classes = array();

	if ( ! is_wp_error( $job_tags ) && ! empty ( $job_tags ) ) {
		$classes[] = 'knowhere-has-search-tags';
	}
	?>

	<div class="kw-finder-filters">

		<div class="job-controls-top">

			<h4 class="job-filters-title"><?php esc_html_e('Filters', 'knowherepro') ?></h4>

			<div class="job-filters-showing">
				<div class="showing_jobs"></div>
			</div>

		</div><!--/ .job-controls-top-->

		<form class="job_filters">

			<?php do_action( 'job_manager_job_filters_start', $atts ); ?>

			<?php if ( $knowhere_settings['job-type-fields'] == 'property' ): ?>

					<div class="search_jobs">

						<?php do_action( 'job_manager_job_filters_search_jobs_start', $atts ); ?>

						<div class="search_keywords">
							<label for="search_keywords"><?php esc_html_e( 'Keywords', 'knowherepro' ); ?></label>
							<input type="text" name="search_keywords" id="search_keywords" placeholder="<?php esc_attr_e( 'Type a city, ZIP, address or MLS #', 'knowherepro' ); ?>" value="<?php echo esc_attr( $keywords ); ?>" />
						</div>

						<?php
						$has_listing_categories = get_terms( 'job_listing_category' );

						if ( $show_categories && ! is_wp_error( $has_listing_categories ) && ! empty( $has_listing_categories ) ) :

							//select the current category
							if ( empty( $selected_category ) ) {
								//try to see if there is a search_categories (notice the plural form) GET param
								$search_categories = isset( $_REQUEST['search_categories'] ) ? $_REQUEST['search_categories'] : '';

								if ( ! empty( $search_categories ) && is_array( $search_categories ) ) {
									$search_categories = $search_categories[0];
								}

								$search_categories = sanitize_text_field( stripslashes( $search_categories ) );

								if ( ! empty( $search_categories ) ) {
									if ( is_numeric( $search_categories ) ) {
										$selected_category = intval( $search_categories );
									} else {
										$term              = get_term_by( 'slug', $search_categories, 'job_listing_category' );
										$selected_category = $term->term_id;
									}
								} elseif ( ! empty( $categories ) && isset( $categories[0] ) ) {
									if ( is_string( $categories[0] ) ) {
										$term              = get_term_by( 'slug', $categories[0], 'job_listing_category' );
										$selected_category = $term->term_id;
									} else {
										$selected_category = intval( $categories[0] );
									}
								}
							} ?>

							<div class="search_categories">

								<label for="search_categories"><?php esc_html_e( 'Categories', 'knowherepro' ); ?></label>

								<?php if ( $show_category_multiselect ) : ?>
									<?php job_manager_dropdown_categories( array(
											'taxonomy' => 'job_listing_category',
											'hierarchical' => 1,
											'name' => 'search_categories',
											'orderby' => 'name',
											'selected' => $selected_category,
											'hide_empty' => false )
									); ?>
								<?php else : ?>
									<?php job_manager_dropdown_categories( array(
										'taxonomy' => 'job_listing_category',
										'hierarchical' => 1,
										'show_option_all' => esc_html__( 'Any category', 'knowherepro' ),
										'name' => 'search_categories', 'orderby' => 'name',
										'selected' => $selected_category,
										'multiple' => false,
										'hide_empty' => false
									) ); ?>
								<?php endif; ?>

							</div>

						<?php endif; ?>

						<?php if ( get_option( 'job_manager_regions_filter' ) ):  ?>

							<div class="search_b">

								<label for="search_region"><?php esc_html_e( 'Regions', 'knowherepro' ); ?></label>

								<?php
								wp_dropdown_categories( apply_filters( 'job_manager_regions_dropdown_args', array(
									'show_option_all' => __( 'All Regions', 'knowherepro' ),
									'hierarchical' => true,
									'orderby' => 'name',
									'taxonomy' => 'job_listing_region',
									'name' => 'search_region',
									'class' => 'filter-job-regions',
									'hide_empty' => 0,
									'selected' => isset( $atts['selected_region'] ) ? $atts['selected_region'] : ''
								) ) );
								?>

							</div>

						<?php endif; ?>

						<div class="search_b">

							<label for="search_bedrooms"><?php esc_html_e( 'Bedrooms', 'knowherepro' ); ?></label>

							<select name="search_bedrooms" id="search_bedrooms" class="search_bedrooms job-manager-filter" autocomplete="off">

								<option value=""><?php esc_html_e( 'Bedrooms', 'knowherepro' ); ?></option>

								<?php for ( $i = 1; $i < 6; $i++ ): ?>
									<option <?php selected( $atts['selected_bedrooms'], $i ) ?> value="<?php echo esc_attr($i) ?>"><?php echo esc_attr($i) ?></option>
								<?php endfor; ?>

							</select>

						</div>

						<div class="search_b">

							<label for="search_bathrooms"><?php esc_html_e( 'Bathrooms', 'knowherepro' ); ?></label>

							<select name="search_bathrooms" id="search_bathrooms" class="search_bathrooms job-manager-filter" autocomplete="off">

								<option value=""><?php esc_html_e( 'Bathrooms', 'knowherepro' ); ?></option>

								<?php for ( $i = 1; $i < 6; $i++ ): ?>
									<option <?php selected( $atts['selected_bathrooms'], $i ) ?> value="<?php echo esc_attr($i) ?>"><?php echo esc_attr($i) ?></option>
								<?php endfor; ?>

							</select>

						</div>

						<?php if ( $knowhere_settings['show-search-price'] ): ?>

							 <div class="search_price_range">

								<label for="search_price_range"><?php esc_html_e( 'Price', 'knowherepro' ); ?></label>

								<div class="range-text">
									<input type="hidden" name="search_min_price" class="min-price-range-hidden range-input" readonly>
									<input type="hidden" name="search_max_price" class="max-price-range-hidden range-input" readonly>
									<span class="min-price-range"></span><span class="max-price-range"></span>
								</div>
								<div class="range-wrap">
									<div class="price-range-advanced"></div>
								</div>

							</div>

						<?php endif; ?>



						<?php if ( ! is_wp_error( $job_tags ) && ! empty ( $job_tags ) ) : ?>

							<div class="search_features">

								<label><?php esc_html_e( 'Features', 'knowherepro' ); ?></label>

								<div class="kw-features-list">

									<?php foreach( $job_tags as $feature ): ?>

										<label for="kw-feature-<?php echo esc_attr( $feature->slug ) ?>" class="checkbox-inline">
											<?php $checked = ( in_array( $feature->slug, $atts['selected_feature'] ) ) ? ' checked="checked"' : ''; ?>
											<input class="search_feature" name="search_feature[]" <?php echo $checked  ?> id="kw-feature-<?php echo esc_attr( $feature->slug ) ?>" type="checkbox" value="<?php echo esc_attr($feature->slug) ?>">
											<?php echo esc_attr($feature->name) ?>
										</label>

									<?php endforeach; ?>

								</div><!--/ .kw-features-list-->

							</div>

						<?php endif; ?>

					</div><!--/ .search_jobs-->

				</ul><!--/ .kw-job-filters-->

				<?php do_action( 'job_manager_job_filters_end', $atts ); ?>

			<?php else: ?>

				<div class="search_jobs <?php echo implode( ' ', $classes ) ?>">

					<?php do_action( 'job_manager_job_filters_search_jobs_start', $atts ); ?>

					<div class="search_keywords">
						<label for="search_keywords"><?php esc_html_e( 'Keywords', 'knowherepro' ); ?></label>
						<input type="text" name="search_keywords" id="search_keywords" placeholder="<?php esc_attr_e( 'Keywords', 'knowherepro' ); ?>" value="<?php echo esc_attr( $keywords ); ?>" />
					</div>

					<div class="search_location">
						<label for="search_location"><?php esc_html_e( 'Location', 'knowherepro' ); ?></label>
						<input type="text" name="search_location" id="search_location" placeholder="<?php esc_attr_e( 'Location', 'knowherepro' ); ?>" value="<?php echo esc_attr( $location ); ?>" />
					</div>

					<?php
					$has_listing_categories = get_terms( 'job_listing_category' );

					if ( $show_categories && ! is_wp_error( $has_listing_categories ) && ! empty( $has_listing_categories ) ) :

						//select the current category
						if ( empty( $selected_category ) ) {
							//try to see if there is a search_categories (notice the plural form) GET param
							$search_categories = isset( $_REQUEST['search_categories'] ) ? $_REQUEST['search_categories'] : '';

							if ( ! empty( $search_categories ) && is_array( $search_categories ) ) {
								$search_categories = $search_categories[0];
							}

							$search_categories = sanitize_text_field( stripslashes( $search_categories ) );

							if ( ! empty( $search_categories ) ) {
								if ( is_numeric( $search_categories ) ) {
									$selected_category = intval( $search_categories );
								} else {
									$term              = get_term_by( 'slug', $search_categories, 'job_listing_category' );
									$selected_category = $term->term_id;
								}
							} elseif ( ! empty( $categories ) && isset( $categories[0] ) ) {
								if ( is_string( $categories[0] ) ) {
									$term              = get_term_by( 'slug', $categories[0], 'job_listing_category' );
									$selected_category = $term->term_id;
								} else {
									$selected_category = intval( $categories[0] );
								}
							}
						} ?>

						<div class="search_categories">

							<label for="search_categories"><?php esc_html_e( 'Categories', 'knowherepro' ); ?></label>

							<?php if ( $show_category_multiselect ) : ?>
								<?php job_manager_dropdown_categories( array(
										'taxonomy' => 'job_listing_category',
										'hierarchical' => 1,
										'name' => 'search_categories',
										'orderby' => 'name',
										'selected' => $selected_category,
										'hide_empty' => false )
								); ?>
							<?php else : ?>
								<?php job_manager_dropdown_categories( array(
									'taxonomy' => 'job_listing_category',
									'hierarchical' => 1,
									'show_option_all' => esc_html__( 'Any category', 'knowherepro' ),
									'name' => 'search_categories', 'orderby' => 'name',
									'selected' => $selected_category,
									'multiple' => false,
									'hide_empty' => false
								) ); ?>
							<?php endif; ?>

						</div>

					<?php endif; ?>

					<?php if ( $knowhere_settings['job-filter-style-position'] == 'kw-left-position' ): ?>
						<div class="kw-job-filters-search"></div>
					<?php endif; ?>

					<?php
					if ( ! is_wp_error( $job_tags ) && ! empty ( $job_tags ) ) { ?>
						<!--<div class="search_tags">
							<label for="search_tags"><?php esc_html_e( 'Tags', 'knowherepro' ); ?></label>
							<select multiple class="knowhere-tags-select" data-placeholder="<?php esc_html_e( 'Filter by tags', 'knowherepro' ); ?>"
									name="job_tag_select">
								<?php foreach ( $job_tags as $term ) : ?>
									<option value="<?php echo esc_attr($term->name) ?>"><?php echo esc_html($term->name); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="knowhere-active-tags"></div>-->
					<?php } ?>

					<?php if ( $knowhere_settings['job-type-fields'] == 'job' || $knowhere_settings['job-type-fields'] == 'listing' ): ?>
						<?php if ( $knowhere_settings['show-search-price'] ): ?>

							 <div class="search_price_range">

								<label for="search_price_range"><?php esc_html_e( 'Price', 'knowherepro' ); ?></label>

								<div class="range-text">
									<input type="hidden" name="search_min_price" class="min-price-range-hidden range-input" readonly>
									<input type="hidden" name="search_max_price" class="max-price-range-hidden range-input" readonly>
									<span class="min-price-range"></span><span class="max-price-range"></span>
								</div>
								<div class="range-wrap">
									<div class="price-range-advanced"></div>
								</div>

							</div><!--/ .search_price_range-->

						<?php endif; ?>
					<?php endif; ?>
					

					<?php if ( get_option( 'job_manager_regions_filter' ) ): ?>
						<span class="knowhere-active-regions"></span>
					<?php endif; ?>

					<div class="clear"></div>
					<?php 
							if (count($categories) == 1) {
								$term              = get_term_by( 'slug', $categories[0], 'job_listing_category' );
								echo '<div style="text-align:left!important;width:100%">';
								echo '<h1 style="font-size:25px">'.$term->name.'</h1>';
								echo '<p>'.$term->description.'</p>';
								echo '</div>';
							}
							 //var_dump($categories);
							  
							?>

					<?php do_action( 'job_manager_job_filters_search_jobs_end', $atts ); ?>

				</div><!--/ .search_jobs-->

				<?php do_action( 'job_manager_job_filters_end', $atts ); ?>

			<?php endif; ?>
			
			<div class="kw-touch-buttons">

				<button class="kw-button-filter">
					<?php esc_html_e('Filter', 'knowherepro'); ?>
					<span><?php esc_html_e('Listings', 'knowherepro'); ?></span>
				</button>

				<button class="kw-button-view kw-button-view-map">
					<span><?php esc_html_e('Map View', 'knowherepro'); ?></span>
				</button>

				<button class="kw-button-view kw-button-view-cards">
					<span><?php esc_html_e('Cards View', 'knowherepro'); ?> </span>
				</button>

			</div><!--/ .kw-touch-buttons-->

		</form>

	</div>

	<?php do_action( 'job_manager_job_filters_after', $atts ); ?>

	<noscript><?php esc_html_e( 'Your browser does not support JavaScript, or it is disabled. JavaScript must be enabled in order to view listings.', 'knowherepro' ); ?></noscript>

<?php endif; ?>
