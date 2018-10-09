<?php
/**
 * Submit Claim
 * This will handle anything related to claim submission.
 *
 * @since 3.0.0
 **/
namespace wpjmcl\submit_claim;
if ( ! defined( 'WPINC' ) ) { die; }

/* Bail if page not set. */
$claim_page = get_option( 'job_manager_claim_listing_page_id' );
if ( ! $claim_page ) { return false;
}

/*
 Constants
------------------------------------------ */

define( __NAMESPACE__ . '\PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( __NAMESPACE__ . '\URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( __NAMESPACE__ . '\VERSION', WPJMCL_VERSION );


/*
 Load Files
------------------------------------------ */

/* Functions */
require_once( PATH . 'setup.php' );



