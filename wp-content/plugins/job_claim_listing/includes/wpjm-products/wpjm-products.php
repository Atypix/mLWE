<?php
/**
 * WP Job Manager - Products (add-on of WP Job Manager)
 * This File Is to handle anything related to "WP Job Manager - Products" Add-on.
 *
 * @link https://astoundify.com/downloads/wp-job-manager-products/
 * @link https://github.com/Astoundify/wp-job-manager-products
 * @since 3.0.0
 **/
namespace wpjmcl\wpjm_products;
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

/* Functions */
require_once( PATH . 'functions.php' );

/* Settings */
require_once( PATH . 'settings.php' );

/* Setup */
require_once( PATH . 'setup.php' );

