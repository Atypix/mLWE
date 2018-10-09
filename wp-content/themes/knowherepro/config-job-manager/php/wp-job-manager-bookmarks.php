<?php
/**
 * @package KnowherePro
 */

add_action( 'job_manager_settings', 'knowhere_add_settings_bookmarks_page' );

function knowhere_add_settings_bookmarks_page( $settings ) {

	/* Select Bookmarks Page */
	$settings['job_pages'][1][] = array(
		'name'        => 'wp_job_manager_bookmarks_page_id',
		'std'         => '',
		'label'       => __( 'Bookmarks Page', 'knowherepro' ),
		'desc'        => __( 'Select the page where you have placed the [my_bookmarks] shortcode.', 'knowherepro' ),
		'type'        => 'page',
	);

	return $settings;
}