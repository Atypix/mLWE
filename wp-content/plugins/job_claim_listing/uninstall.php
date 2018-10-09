<?php
/**
 * Uninstall
 **/
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit();
}


/*
 Options
------------------------------------------ */

/* settings */
delete_option( 'job_manager_claim_listing_page_id' );
delete_option( 'wpjmcl_submit_claim_data' );

/* wpjm-products */
delete_option( 'wpjmcl_transfer_product_ownership' );

/* wpjm-wc-paid-listing */
delete_option( 'wpjmcl_paid_claiming' );

