<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://www.webtoffee.com
 * @since      1.0.0
 *
 * @package    Webtoffee_Wordpress_Migrator
 * @subpackage Webtoffee_Wordpress_Migrator/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Webtoffee_Wordpress_Migrator
 * @subpackage Webtoffee_Wordpress_Migrator/includes
 * @author     WebToffee <info@webtoffee.com>
 */
class Webtoffee_Wordpress_Migrator_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'webtoffee-wordpress-migrator',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
