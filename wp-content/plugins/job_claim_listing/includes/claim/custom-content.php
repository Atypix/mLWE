<?php
/**
 * CLAIM: Register Custom Content
 *
 * @since 3.0.0
 */
namespace wpjmcl\claim;


/* add register post type and taxonomy on the 'init' hook */
add_action( 'init', __NAMESPACE__ . '\register_custom_content' );

/**
 * Register Post Type
 *
 * @since  3.0.0
 */
function register_custom_content() {

	/* === "claim" post type === */
	$args = array(
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => false,
		'query_var'          => true,
		'rewrite'            => false,
		'capability_type'    => 'job_listing',
		'map_meta_cap'       => true,
		'has_archive'        => false,
		'hierarchical'       => false,
		'supports'           => array( 'title', 'author' ),
		'labels'             => array(
			'name'               => __( 'Claims', 'wp-job-manager-claim-listing' ),
			'singular_name'      => __( 'Claim', 'wp-job-manager-claim-listing' ),
			'menu_name'          => __( 'Claims', 'wp-job-manager-claim-listing' ),
			'name_admin_bar'     => __( 'Claims', 'wp-job-manager-claim-listing' ),
			'add_new'            => __( 'Add New', 'wp-job-manager-claim-listing' ),
			'add_new_item'       => __( 'Add New Claim', 'wp-job-manager-claim-listing' ),
			'new_item'           => __( 'New Claim', 'wp-job-manager-claim-listing' ),
			'edit_item'          => __( 'Edit Claim', 'wp-job-manager-claim-listing' ),
			'view_item'          => __( 'View Claim', 'wp-job-manager-claim-listing' ),
			'all_items'          => __( 'All Claims', 'wp-job-manager-claim-listing' ),
			'search_items'       => __( 'Search Claims', 'wp-job-manager-claim-listing' ),
			'parent_item_colon'  => __( 'Parent Claims:', 'wp-job-manager-claim-listing' ),
			'not_found'          => __( 'No Claims found.', 'wp-job-manager-claim-listing' ),
			'not_found_in_trash' => __( 'No Claims found in Trash.', 'wp-job-manager-claim-listing' ),
		),
	);

	/* Register Post Type */
	register_post_type( 'claim', $args );
}
