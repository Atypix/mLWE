<?php
/**
 * Job Listing (WP Job Manager)
 *
 * @since 3.0.0
 **/
namespace wpjmcl\job_listing;

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

/* Front Setup */
require_once( PATH . 'setup.php' );

/* Admin Setup */
require_once( PATH . 'admin-setup.php' );

/* Meta Boxes */
require_once( PATH . 'meta-boxes.php' );


