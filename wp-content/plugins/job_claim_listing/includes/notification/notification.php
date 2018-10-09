<?php
/**
 * Email Notification
 * This will handle anything related to email notification.
 *
 * @since 3.0.0
 **/
namespace wpjmcl\notification;
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
require_once( PATH . 'setup.php' );

/* Settings */
require_once( PATH . 'settings.php' );

