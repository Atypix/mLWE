<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.webtoffee.com
 * @since             1.0.0
 * @package           Webtoffee_Wordpress_Migrator
 *
 * @wordpress-plugin
 * Plugin Name:       WordPress Migration & Duplicator
 * Plugin URI:        https://wordpress.org/plugins/wp-migration-duplicator/
 * Description:       Migrate WordPress Contents and database quickly with ease.
 * Version:           1.0.1
 * Author:            WebToffee
 * Author URI:        https://www.webtoffee.com/  
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-migration-duplicator
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WEBTOFFEE_MIGRATOR_VERSION', '1.0.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-webtoffee-wordpress-migrator-activator.php
 */
function activate_webtoffee_wordpress_migrator() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-webtoffee-wordpress-migrator-activator.php';
    Webtoffee_Wordpress_Migrator_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-webtoffee-wordpress-migrator-deactivator.php
 */
function deactivate_webtoffee_wordpress_migrator() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-webtoffee-wordpress-migrator-deactivator.php';
    Webtoffee_Wordpress_Migrator_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_webtoffee_wordpress_migrator' );
register_deactivation_hook( __FILE__, 'deactivate_webtoffee_wordpress_migrator' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-webtoffee-wordpress-migrator.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-hf-migration-uninstall-feedback.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_webtoffee_wordpress_migrator() {

    $plugin = new Webtoffee_Wordpress_Migrator();
    $plugin->run();

}

//todo move this to inner page
function wt_mg_support_link( $links ) {
    $links = array_merge( array(
        '<a  target="_blank" href="https://wordpress.org/support/plugin/wp-migration-duplicator/">' . __( 'Support', 'wt-social-login' ) . '</a>'
    ), $links );
    return $links;
}
add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wt_mg_support_link' );

function wt_mg_review( $links ) {
    $links = array_merge( array(
        '<a  target="_blank" href="https://wordpress.org/support/plugin/wp-migration-duplicator/reviews/?filter=5#new-post">' . __( 'Review', 'wt-social-login' ) . '</a>'
    ), $links );
    return $links;
}
add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wt_mg_review' );

run_webtoffee_wordpress_migrator();