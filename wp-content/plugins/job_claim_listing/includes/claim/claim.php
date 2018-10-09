<?php
/**
 * CLAIM Post Type
 * This will handle anything related to "Claim" Post Type.
 *
 * @since 3.0.0
 **/
namespace wpjmcl\claim;

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

/* Register Post Type */
require_once( PATH . 'custom-content.php' );

/* Admin Setup */
require_once( PATH . 'admin-setup.php' );

/* Meta Boxes */
require_once( PATH . 'meta-boxes.php' );

/* Setup */
require_once( PATH . 'setup.php' );

