
<?php
global $knowhere_settings;

$count_columns = $knowhere_settings['job-listings-columns'];
$term_count_columns = knowhere_job_get_term('pix_term_count_columns');

if ( knowhere_is_realy_job_manager_tax() ) {
	$count_columns = $knowhere_settings['job-category-columns'];
}

if ( $term_count_columns && absint($term_count_columns) ) {
	$count_columns = $term_count_columns;
}

$count_columns = 'kw-cols-' . abs($count_columns);

$filter_position = $knowhere_settings['job-filter-style-position'];
$filter_position_left_extend = $knowhere_settings['job-filter-left-position-extend'];
$selected_extend_type = $knowhere_settings['job-filter-left-extend-type'];

$controls_extend = false;

if ( $filter_position == 'kw-left-position' ) {
	if ( $filter_position_left_extend ) {
		$controls_extend = true;
	}
}

?>

<?php

if ( knowhere_using_facetwp() ) :

	$output = '<div class="kw-finder-listings">';
	$output .= facetwp_display( 'template', 'listings' );
	$output .= facetwp_display( 'pager' );

	echo $output;

else : ?>

	<div class="kw-finder-listings">

		<?php knowhere_job_listing_breadcrumbs(); ?>

		<div class="kw-controls-form">

			<?php if ( $controls_extend ): ?>
				<?php echo knowhere_get_search_keywords(); ?>
			<?php endif; ?>

			<div class="kw-controls-holder">

				<div class="kw-filters-results"><div class="kw-results-count"></div></div>

				<div class="kw-controls-wrap">

					<?php if ( $controls_extend ): ?>

						<?php echo knowhere_get_sort_filter(); ?>

						<div class="kw-layout-controls">
							<a href="javascript:void(0)" data-col="kw-list-view" class="kw-layout-control <?php echo ( $selected_extend_type == 'kw-list-view' ) ? 'kw-active' : '' ?>"
							   data-layout="grid"><i class="fa fa-bars"></i></a>
							<a href="javascript:void(0)" data-col="kw-grid-view" class="kw-layout-control <?php echo ( $selected_extend_type == 'kw-grid-view' ) ? 'kw-active' : '' ?>"
							   data-layout="list"><i class="fa fa-th"></i></a>
							<a href="javascript:void(0)" data-col="kw-map-view" class="kw-layout-control <?php echo ( $selected_extend_type == 'kw-map-view' ) ? 'kw-active' : '' ?>"
							   data-layout="list"><i class="fa fa-map-o"></i></a>
						</div><!--/ .kw-layout-controls-->

					<?php else: ?>

						<div class="kw-layout-controls">
							<a href="javascript:void(0)" data-col="kw-cols-1" class="kw-layout-control <?php echo ( $count_columns == 'kw-cols-1' ) ? 'kw-active' : '' ?>"
							   data-layout="grid"><i class="fa fa-th-list"></i></a>
							<a href="javascript:void(0)" data-col="kw-cols-2" class="kw-layout-control <?php echo ( $count_columns == 'kw-cols-2' ) ? 'kw-active' : '' ?>"
							   data-layout="list"><i class="fa fa-th-large"></i></a>
							<a href="javascript:void(0)" data-col="kw-cols-3" class="kw-layout-control <?php echo ( $count_columns == 'kw-cols-3' ) ? 'kw-active' : '' ?>"
							   data-layout="list"><i class="fa fa-th"></i></a>
						</div><!--/ .kw-layout-controls-->

					<?php endif; ?>

				</div><!--/ .kw-controls-wrap-->

			</div>

		</div><!--/ .kw-controls-form-->

		<div class="kw-finder-extend">

			<div class="job_listings">

<?php endif; ?>




