<?php

if ( !class_exists( 'WP_Job_Manager' ) ) return;

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

$job_tags = get_terms( array( 'taxonomy' => 'job_listing_tag', 'hierarchical' => 1 ) );

?>

<form class="job_search_form" action="<?php echo get_post_type_archive_link( 'job_listing' ); ?>" method="GET">

	<ul class="kw-job-filters">

		<li class="kw-oneline-row-action">

			<?php do_action( 'job_manager_job_filters_start', $atts ); ?>

			<fieldset class="kw-oneline-fields">

				<div class="search_jobs knowhere-search-jobs-frontpage">

					<?php do_action( 'job_manager_job_filters_search_jobs_start', $atts ); ?>

					<div class="search_keywords">
						<label for="search_keywords"><?php esc_html_e( 'Keywords', 'knowherepro' ); ?></label>
						<input type="text" name="search_keywords" id="search_keywords" placeholder="<?php esc_attr_e( 'Type a city, ZIP, address or MLS #', 'knowherepro' ); ?>" />
					</div>

					<?php if ( get_option( 'job_manager_enable_categories' ) ) : ?>

						<div class="search_categories">
							<label for="search_categories"><?php esc_html_e( 'Category', 'knowherepro' ); ?></label>
							<?php job_manager_dropdown_categories( array( 'taxonomy' => 'job_listing_category', 'hierarchical' => 1, 'show_option_all' => esc_html__( 'All categories', 'knowherepro' ), 'name' => 'search_categories', 'orderby' => 'name', 'multiple' => false ) ); ?>
						</div>

					<?php endif; ?>

				</div><!--/ .search_jobs-->

				<?php $label = _x( 'Search', 'search filters submit', 'knowherepro' ); ?>

				<div class="kw-oneline-action">
					<button type="submit" data-label="<?php echo esc_attr($label) ?>" class="kw-update-form"><?php echo esc_html($label) ?></button>
				</div>

			</fieldset>

		</li>

		<li class="kw-oneline-row kw-hidden-item" id="form-line-fields">

			<fieldset class="kw-oneline-fields">

				<div class="search_jobs">

					<?php if ( get_option( 'job_manager_enable_types' ) ) : ?>

						<div class="search_types">

							<label for="search_types"><?php esc_html_e( 'Types', 'knowherepro' ); ?></label>

							<?php job_manager_dropdown_categories( array(
								'taxonomy' => 'job_listing_type',
								'hierarchical' => 1,
								'show_option_all' => esc_html__( 'All types', 'knowherepro' ),
								'name' => 'filter_job_type',
								'class' => 'job-manager-filter',
								'orderby' => 'name',
								'multiple' => false,
								'selected' => $atts['selected_job_types'],
								'value' => 'slug'
							) ); ?>

						</div>

					<?php endif; ?>

					<div class="search_b">

						<label for="search_bedrooms"><?php esc_html_e( 'Bedrooms', 'knowherepro' ); ?></label>

						<select name="search_bedrooms" id="search_bedrooms" class="search_bedrooms" autocomplete="off">
							<option value=""><?php esc_html_e( 'Bedrooms', 'knowherepro' ); ?></option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>

					</div>

					<div class="search_b">

						<label for="search_bathrooms"><?php esc_html_e( 'Bathrooms', 'knowherepro' ); ?></label>

						<select name="search_bathrooms" id="search_bathrooms" class="search_bathrooms" autocomplete="off">
							<option value=""><?php esc_html_e( 'Bathrooms', 'knowherepro' ); ?></option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>

					</div>

					<div class="search_b">
						<input type="text" name="search_min_price" id="search_min_price" placeholder="<?php esc_html_e('Min Price', 'knowherepro') ?>" autocomplete="off">
					</div>

					<div class="search_b">
						<input type="text" name="search_max_price" id="search_min_price" placeholder="<?php esc_html_e('Max Price', 'knowherepro') ?>" autocomplete="off">
					</div>

				</div>

			</fieldset>

		</li>

		<li class="kw-fields-buttons">

			<div class="kw-sm-table-row kw-xs-small-offset">

				<div class="col-sm-6">
					<a href="javascript:void(0)" class="kw-show-more-fields" data-second-state-text="<?php esc_html_e('Show less', 'knowherepro') ?>" data-hidden-item="#form-line-fields"><?php esc_html_e('Show more', 'knowherepro') ?></a>
				</div>

			</div>

		</li>
	</ul>

	<div class="clear"></div>

</form>


