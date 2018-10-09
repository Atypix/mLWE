<?php
/**
 * Claim Setup
 */
namespace wpjmcl\claim;
if ( ! defined( 'WPINC' ) ) { die; }


/*
 Load Class */
// Setup::get_instance();
/**
 * Setup Class
 *
 * @since 3.0.0
 */
final class Setup {

	/**
	 * Construct
	 */
	public function __construct() {

	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 */
	public static function get_instance() {
		static $instance = null;
		if ( is_null( $instance ) ) { $instance = new self;
		}
		return $instance;
	}




} // end class






























