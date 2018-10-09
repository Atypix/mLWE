<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://themeisle.com
 * @since             2.0.0
 * @package           WPPR_Pro
 *
 * @wordpress-plugin
 * Plugin Name:       WP Product Review
 * Plugin URI:        https://themeisle.com/plugins/wp-product-review/
 * Description:       This bundle contains all the premium add-on like: custom icons, preloader, product in post shortcode, comparision chart.
 * Version:           2.0.6
 * Author:            ThemeIsle
 * Author URI:        https://themeisle.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-product-review
 * WordPress Available:  no
 * Requires License:    yes
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wppr-pro-activator.php
 */
function activate_wp_product_review_pro() {
	WPPR_Pro_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wppr-pro-deactivator.php
 */
function deactivate_wp_product_review_pro() {
	WPPR_Pro_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_product_review_pro' );
register_deactivation_hook( __FILE__, 'deactivate_wp_product_review_pro' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.0
 */
function run_wppr_pro() {
	define( 'WPPR_PRO_VERSION', '2.0.6' );
	define( 'WPPR_PRO_PATH', dirname( __FILE__ ) );
	define( 'WPPR_PRO_SLUG', 'wppr-pro' );
	define( 'WPPR_PRO_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );
	define( 'WPPR_PRO_ADDONS_ASSETS', WPPR_PRO_URL . '/includes/addons/assets/' );
	$plugin = new WPPR_Pro();
	$plugin->run();
	$vendor_file = WPPR_PRO_PATH . '/vendor/autoload_52.php';
	if ( is_readable( $vendor_file ) ) {
		include_once( $vendor_file );
	}
	add_filter( 'themeisle_sdk_products', 'wppr_pro_register_sdk' );

}

/**
 * Register products to sdk.
 *
 * @param array $products The old products array.
 *
 * @return array The products array.
 */
function wppr_pro_register_sdk( $products ) {
	$products[] = __FILE__;

	return $products;
}

require( 'class-wppr-pro-autoloader.php' );
WPPR_Pro_Autoloader::define_namespaces( array( 'WPPR_Pro', 'WPPR' ) );
/**
 * Invocation of the Autoloader::loader method.
 *
 * @since   1.0.0
 */
spl_autoload_register( array( 'WPPR_Pro_Autoloader', 'loader' ) );
add_action( 'plugins_loaded', 'run_wppr_pro' );
