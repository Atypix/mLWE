<?php
// reset all previous queries
wp_reset_postdata();

global $knowhere_settings, $knowhere_config;
$post_id = knowhere_post_id();
$custom_sidebar = $knowhere_settings['sidebar'];
$page_sidebar = trim( get_post_meta( $post_id, 'knowhere_page_sidebar', true ) );

if ( isset($knowhere_config['sidebar_position'])) {
	if ( $knowhere_config['sidebar_position'] == 'kw-no-sidebar' ) {
		return;
	}
}

if ( $page_sidebar ) {
	$custom_sidebar = $page_sidebar;
}

if ( is_singular(array('page', 'post')) && !empty($post_id) ) {
	$custom_sidebar = $page_sidebar;
}

if ( knowhere_is_realy_job_manager_tax() || is_post_type_archive('job_listing') || knowhere_job_listing_has_shortcode_jobs() ) {

	$custom_sidebar = $knowhere_settings['job-category-sidebar'];

} elseif ( knowhere_is_realy_job_manager_single() ) {

	$custom_sidebar = $knowhere_settings['job-single-sidebar'];

} elseif ( is_singular('knowhere_agent') || is_post_type_archive('knowhere_agent') ) {

	$custom_sidebar = $knowhere_settings['job-agent-sidebar'];

} elseif ( is_singular('knowhere_agency') || is_post_type_archive('knowhere_agency') ) {

	$custom_sidebar = $knowhere_settings['job-agency-sidebar'];

} elseif ( is_singular('resume') ) {

	$custom_sidebar = $knowhere_settings['job-resume-sidebar'];

} elseif ( knowhere_is_realy_job_manager_tax() ) {

	$custom_sidebar = $knowhere_settings['job-category-sidebar'];

} elseif ( knowhere_is_realy_woocommerce_page() ) {

	$custom_sidebar = $knowhere_settings['product-sidebar'];

	if ( knowhere_is_product() ) {

		if ( !empty($post_id) ) {
			$custom_sidebar = $page_sidebar;
		}

		if ( empty($custom_sidebar) ) {
			$custom_sidebar = $knowhere_settings['product-sidebar'];
		}

	}

}

?>
<aside id="sidebar" class="kw-sidebar">
	<?php
	if ( !empty($custom_sidebar) ) {
		dynamic_sidebar($custom_sidebar);
	} else {
		if ( is_active_sidebar('general-widget-area') ) {
			dynamic_sidebar('General Widget Area');
		}
	}
	?>
</aside><!--/ .kw-sidebar-->


