<?php
/**
 * Listing Payments for WP Job Manager (add-on of WP Job Manager+WooCommerce)
 * This File Is to handle anything related to "Listing Payments for WP Job Manager" Plugin.
 *
 * @link https://astoundify.com/products/listing-payments/
 * @since 3.1.0
 **/
namespace wpjmcl\wpjm_listing_payments;
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


