
<?php if ( !class_exists( 'WP_Job_Manager' ) ) return; ?>

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

<form class="job_filters" action="<?php echo knowhere_get_listings_page_url(); ?>" method="GET">

	<div class="search_jobs knowhere-search-jobs-frontpage <?php if ( $show_categories ): ?>knowhere-has-categories<?php endif; ?>">

		<div class="search_keywords">
			<label for="search_keywords"><?php _e( 'Keywords', 'knowherepro' ); ?></label>
			<input type="text" name="search_keywords" placeholder="<?php esc_attr_e( 'What are you looking for?', 'knowherepro' ); ?>" />
		</div>

		<div class="search_location">
			<label for="search_location"><?php _e( 'Location', 'knowherepro' ); ?></label>
			<input type="text" name="search_location" class="search_location" placeholder="<?php esc_attr_e( 'Location', 'knowherepro' ); ?>" />
		</div>

		<?php do_action( 'job_manager_job_filters_search_jobs_end', $atts ); ?>

		<?php if ( get_option( 'job_manager_enable_categories' ) ) : ?>

			<div class="search_categories">
				<label for="search_categories"><?php esc_html_e( 'Category', 'knowherepro' ); ?></label>
				<?php job_manager_dropdown_categories( array( 'id' => ' ', 'taxonomy' => 'job_listing_category', 'hierarchical' => 1, 'show_option_all' => esc_html__( 'All categories', 'knowherepro' ), 'name' => 'search_categories', 'orderby' => 'name', 'multiple' => false ) ); ?>
			</div>

		<?php endif; ?>

	</div>

	<?php $label = _x( 'Search', 'search filters submit', 'knowherepro' ); ?>

	<div class="kw-oneline-action">
		<button type="submit" data-label="<?php echo esc_attr($label) ?>" class="kw-update-form"><?php echo esc_html($label) ?></button>
	</div>

</form>



