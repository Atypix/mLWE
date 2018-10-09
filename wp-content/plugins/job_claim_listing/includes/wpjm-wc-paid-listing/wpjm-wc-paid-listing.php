<?php
/**
 * WP Job Manager - Paid Listing (add-on of WP Job Manager+WooCommerce)
 * This File Is to handle anything related to "WP Job Manager - Paid Listing" Plugin.
 *
 * @link https://wpjobmanager.com/add-ons/wc-paid-listings/
 * @since 3.0.0
 **/
namespace wpjmcl\wpjm_wc_paid_listing;
if ( ! defined( 'WPINC' ) ) { die; }


/*
 Constants
------------------------------------------ */

define( __NAMESPACE__ . '\PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( __NAMESPACE__ . '\URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( __NAMESPACE__ . '\VERSION', WPJMCL_VERSION );


/*
 Load Files
------------------------------------------ */

/* Settings */
require_once( PATH . 'settings.php' );

/* Form Setup */
require_once( PATH . 'form-setup.php' );

/* Setup */
require_once( PATH . 'setup.php' );

/* Checkout Setup */
require_once( PATH . 'checkout-setup.php' );

/* Order Setup */
require_once( PATH . 'order-setup.php' );

/* Meta Boxes */
require_once( PATH . 'meta-boxes.php' );


