<?php

if ( class_exists( 'WP_Job_Manager' ) ) {

	/* Load Job Manager compatibility file
	 * https://wpjobmanager.com/
	/* ---------------------------------------------------------------------- */
	require_once( get_theme_file_path('config-job-manager/config.php') );

	/**
	 * Load WP Job Manager Job Tags compatibility file
	 * https://wpjobmanager.com/add-ons/job-tags/
	/* ---------------------------------------------------------------------- */

	if ( class_exists( 'WP_Job_Manager_Job_Tags' ) ) {
		require get_theme_file_path('config-job-manager/php/wp-job-manager-tags.php');
	}

	/**
	 * Load WP Job Manager Resume compatibility file
	 * https://wpjobmanager.com/add-ons/resume-manager/
	 */
	/* ---------------------------------------------------------------------- */

	if ( class_exists( 'WP_Resume_Manager' ) ) {
		require get_theme_file_path('config-job-manager/php/wp-job-manager-resumes.php');
	}

	/**
	 * Load WP Job Manager Applications compatibility file
	 * https://wpjobmanager.com/add-ons/applications/
	 */
	/* ---------------------------------------------------------------------- */

	if ( class_exists( 'WP_Job_Manager_Applications' ) ) {
		require get_theme_file_path('config-job-manager/php/wp-job-manager-applications.php');
	}

	/* Load Job Manager Regions compatibility file
	/* ---------------------------------------------------------------------- */

	if ( class_exists( 'Astoundify_Job_Manager_Regions' ) ) {
		require get_theme_file_path('config-job-manager/php/wp-job-manager-regions.php');
	}

	/* Load Claim Listing compatibility file
	 * https://astoundify.com/products/wp-job-manager-claim-listing/
	/* ---------------------------------------------------------------------- */

	if ( defined( 'WPJMCL_VERSION' ) ) {
		require get_theme_file_path('config-job-manager/php/wp-job-manager-claim-listing.php');
	}

}

/* Load FacetWP compatibility file
 * https://facetwp.com/
/* ---------------------------------------------------------------------- */

function knowhere_using_facetwp() { return function_exists( 'FWP' ); }

if ( class_exists( 'FacetWP' ) ) {
	require get_theme_file_path('includes/facetwp.php');
}

/* Load WooCommerce compatibility file
 * https://wordpress.com/
/* ---------------------------------------------------------------------- */

if ( class_exists('WooCommerce') ) {
	require_once( get_theme_file_path('config-woocommerce/config.php') );
}

/* Load Composer compatibility file
 * https://vc.wpbakery.com/
/* ---------------------------------------------------------------------- */

if ( class_exists('Vc_Manager') ) {
	require_once( get_theme_file_path('config-composer/config.php') );
}

/* Load WPML compatibility file
 * https://wpml.org/
/* ---------------------------------------------------------------------- */

if ( class_exists('SitePress') ) {
	require_once( get_theme_file_path('config-wpml/config.php') );
}

/* Load Login With Ajax compatibility file
/* ---------------------------------------------------------------------- */

function knowhere_using_login_with_ajax() {
	return function_exists( 'login_with_ajax' );
}

if ( class_exists( 'LoginWithAjax' ) ) {
	require_once( get_theme_file_path('includes/login-with-ajax.php') );
}
