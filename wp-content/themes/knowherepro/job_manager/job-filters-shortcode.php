
<?php if ( !class_exists( 'WP_Job_Manager' ) ) return; ?>

<?php if ( knowhere_using_facetwp() ) : ?>

	<form class="job_filters">

		<div class="search_jobs">

			<?php
			$facets = knowhere_get_facets( 'shortcode_listing_map' );

			if ( ! empty( $facets ) ) {
				echo knowhere_get_display_facets( $facets );
			}
			?>

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

	</form>

<?php else: ?>

	<?php
	$show_categories = true;
	if ( ! get_option( 'job_manager_enable_categories' ) ) {
		$show_categories = false;
	}
	$atts = apply_filters( 'job_manager_ouput_jobs_defaut', array(
		'per_page' => get_option( 'job_manager_per_page' ),
		'orderby' => 'featured',
		'order' => 'DESC',
		'show_categories' => $show_categories,
		'show_tags' => false,
		'categories' => true,
		'selected_category' => false,
		'job_types' => false,
		'location' => false,
		'keywords' => false,
		'selected_job_types' => false,
		'show_category_multiselect' => false,
		'selected_region' => false
	) );
	?>

	<?php do_action( 'job_manager_job_filters_before', $atts ); ?>

	<form class="job_filters">

		<?php do_action( 'job_manager_job_filters_start', $atts ); ?>

		<div class="search_jobs knowhere-search-jobs-frontpage <?php if ( $show_categories ): ?>knowhere-has-categories<?php endif; ?>">

			<?php do_action( 'job_manager_job_filters_search_jobs_start', $atts ); ?>

			<div class="search_keywords">
				<label for="search_keywords"><?php _e( 'Keywords', 'knowherepro' ); ?></label>
				<input type="text" name="search_keywords" id="search_keywords" placeholder="<?php esc_attr_e( 'What are you looking for?', 'knowherepro' ); ?>" />
			</div>

			<div class="search_location">
				<label for="search_location"><?php _e( 'Location', 'knowherepro' ); ?></label>
				<input type="text" name="search_location" id="search_location" placeholder="<?php esc_attr_e( 'Location', 'knowherepro' ); ?>" />
			</div>

			<?php if ( get_option( 'job_manager_enable_categories' ) ) : ?>

				<div class="search_categories">
					<label for="search_categories"><?php esc_html_e( 'Category', 'knowherepro' ); ?></label>
					<?php job_manager_dropdown_categories( array( 'taxonomy' => 'job_listing_category', 'hierarchical' => 1, 'show_option_all' => esc_html__( 'All categories', 'knowherepro' ), 'name' => 'search_categories', 'orderby' => 'name', 'multiple' => false ) ); ?>
				</div>

			<?php endif; ?>

			<?php do_action( 'job_manager_job_filters_search_jobs_end', $atts ); ?>

		</div>

		<?php $label = _x( 'Search', 'search filters submit', 'knowherepro' ); ?>

		<div class="kw-oneline-action">
			<button type="submit" data-label="<?php echo esc_attr($label) ?>" class="kw-update-form"><?php echo esc_html($label) ?></button>
		</div>

		<?php do_action( 'job_manager_job_filters_end', $atts ); ?>
	</form>

	<?php do_action( 'job_manager_job_filters_after', $atts ); ?>

	<noscript><?php esc_html_e( 'Your browser does not support JavaScript, or it is disabled. JavaScript must be enabled in order to view listings.', 'knowherepro' ); ?></noscript>

<?php endif; ?>


