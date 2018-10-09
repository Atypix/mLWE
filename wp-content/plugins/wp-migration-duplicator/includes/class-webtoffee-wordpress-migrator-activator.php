<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Fired during plugin activation
 *
 * @link       http://www.webtoffee.com
 * @since      1.0.0
 *
 * @package    Webtoffee_Wordpress_Migrator
 * @subpackage Webtoffee_Wordpress_Migrator/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Webtoffee_Wordpress_Migrator
 * @subpackage Webtoffee_Wordpress_Migrator/includes
 * @author     WebToffee <info@webtoffee.com>
 */
class Webtoffee_Wordpress_Migrator_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		if (!mkdir(WP_CONTENT_DIR . '/webtoffee_migrations', 0755)) {
			return false;
			die;
		}

	}

}
