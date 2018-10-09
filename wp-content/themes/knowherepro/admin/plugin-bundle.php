<?php
/**
 * This file represents an example of the code that themes would use to register
 * the required plugins.
 *
 * It is expected that theme authors would copy and paste this code into their
 * functions.php file, and amend to suit.
 *
 * @see http://tgmpluginactivation.com/configuration/ for detailed documentation.
 *
 * @package    TGM-Plugin-Activation
 * @subpackage Example
 * @version    2.6.1 for parent theme KnowherePro for publication on ThemeForest
 * @author     Thomas Griffin, Gary Jones, Juliette Reinders Folmer
 * @copyright  Copyright (c) 2011, Thomas Griffin
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/TGMPA/TGM-Plugin-Activation
 */

/**
 * Include the TGM_Plugin_Activation class.
 */

require_once get_template_directory() . '/admin/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'knowhere_register_required_plugins' );

if (!function_exists('knowhere_added_admin_action')) {

	function knowhere_added_admin_action() {
		add_action( 'admin_enqueue_scripts', 'knowhere_added_plugin_style' );
	}

	function knowhere_added_plugin_style() {
		wp_enqueue_style( 'knowhere_admin_plugins', get_theme_file_uri('css/admin-plugin.css'), array() );
	}

	add_action( 'load-plugins.php', 'knowhere_added_admin_action', 1 );

}
/**
 * Register the required plugins for this theme.
 *
 * In this example, we register two plugins - one included with the TGMPA library
 * and one from the .org repo.
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function knowhere_register_required_plugins() {

	// disable visual composer automatic update
	global $vc_manager;
	if ( $vc_manager ) {

		$vc_updater = $vc_manager->updater();

		if ( $vc_updater ) {
			remove_filter('upgrader_pre_download', array(&$vc_updater, 'upgradeFilterFromEnvato'));
			remove_filter('upgrader_pre_download', array(&$vc_updater, 'preUpgradeFilter'));
			remove_action('wp_ajax_nopriv_vc_check_license_key', array(&$vc_updater, 'checkLicenseKeyFromRemote'));
		}
	}

	$is_plugins_page = false;
	if ( ( isset( $_GET['page'] ) && 'knowhere-plugins' === $_GET['page'] ) ||
		( isset( $_GET['page'] ) && 'install-required-plugins' === $_GET['page'] ) ||
		( isset( $_SERVER['HTTP_REFERER'] ) && false !== strpos( $_SERVER['HTTP_REFERER'], 'HTTP_REFERER' ) )
	) {
		$is_plugins_page = true;
	}

	$bundled_plugins = KnowherePro()->get_bundled_plugins();

	/**
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(

		array(
			'name'     => esc_html__('Redux Framework', 'knowherepro'),
			'slug'     => 'redux-framework',
			'required' => true
		),

		array(
			'name'      => esc_html__('WP Job Manager - Predefined Regions', 'knowherepro'),
			'slug'      => 'wp-job-manager-locations',
			'required'  => false
		),

		array(
			'name'     => esc_html__('WooCommerce', 'knowherepro'),
			'slug'     => 'woocommerce',
			'required' => false
		),

		array(
			'name'      		 => esc_html__('Login With Ajax', 'knowherepro'),
			'slug'      		 => 'login-with-ajax',
			'required'  		 => false
		),

		array(
			'name'      		 => esc_html__('WP Job Manager', 'knowherepro'),
			'slug'      		 => 'wp-job-manager',
			'required'  		 => true
		),

		array(
			'name'      		 => esc_html__('Login With Ajax', 'knowherepro'),
			'slug'      		 => 'login-with-ajax',
			'required'  		 => false
		),

		array(
			'name'     => esc_html__('Contact Form 7', 'knowherepro'),
			'slug'     => 'contact-form-7',
			'required' => false
		),

		array(
			'name' => esc_html__('MailPoet Newsletters', 'knowherepro'),
			'slug' => 'wysija-newsletters',
			'required' => false
		),

		array(
			'name' => esc_html__('Latest Tweets Widget', 'knowherepro'),
			'slug' => 'latest-tweets-widget',
			'required' => false
		),

		array(
			'name' => esc_html__('Google Captcha (reCAPTCHA) by BestWebSoft', 'knowherepro'),
			'slug' => 'google-captcha',
			'required' => false
		),

		// This is an example of how to include a plugin from the WordPress Plugin Repository.

		array(
			'name'               => $bundled_plugins['theme_functionality']['name'],
			'slug'               => $bundled_plugins['theme_functionality']['slug'],
			'source'             => $bundled_plugins['theme_functionality']['source'],
			'required'           => false,
			'version'            => $bundled_plugins['theme_functionality']['version'],
			'force_activation'   => false,
			'force_deactivation' => false
		),

		array(
			'name'               => $bundled_plugins['composer']['name'],
			'slug'               => $bundled_plugins['composer']['slug'],
			'source'             => $bundled_plugins['composer']['source'],
			'required'           => false,
			'version'            => $bundled_plugins['composer']['version'],
			'force_activation'   => false,
			'force_deactivation' => false
		),

		array(
			'name'               => $bundled_plugins['reviewer']['name'],
			'slug'               => $bundled_plugins['reviewer']['slug'],
			'source'             => $bundled_plugins['reviewer']['source'],
			'required'           => false,
			'version'            => $bundled_plugins['reviewer']['version'],
			'force_activation'   => false,
			'force_deactivation' => false
		),

//		array(
//			'name'               => $bundled_plugins['private_messages']['name'],
//			'slug'               => $bundled_plugins['private_messages']['slug'],
//			'source'             => $bundled_plugins['private_messages']['source'],
//			'required'           => false,
//			'version'            => $bundled_plugins['private_messages']['version'],
//			'force_activation'   => false,
//			'force_deactivation' => false
//		),

		array(
			'name'               => $bundled_plugins['flashsale']['name'],
			'slug'               => $bundled_plugins['flashsale']['slug'],
			'source'             => $bundled_plugins['flashsale']['source'],
			'required'           => false,
			'version'            => $bundled_plugins['flashsale']['version'],
			'force_activation'   => false,
			'force_deactivation' => false
		),

		array(
			'name'               => $bundled_plugins['job_applications']['name'],
			'slug'               => $bundled_plugins['job_applications']['slug'],
			'source'             => $bundled_plugins['job_applications']['source'],
			'required'           => false,
			'version'            => $bundled_plugins['job_applications']['version'],
			'force_activation'   => false,
			'force_deactivation' => false
		),

		array(
			'name'               => $bundled_plugins['job_paid_listings']['name'],
			'slug'               => $bundled_plugins['job_paid_listings']['slug'],
			'source'             => $bundled_plugins['job_paid_listings']['source'],
			'required'           => false,
			'version'            => $bundled_plugins['job_paid_listings']['version'],
			'force_activation'   => false,
			'force_deactivation' => false
		),

		array(
			'name'               => $bundled_plugins['job_stats']['name'],
			'slug'               => $bundled_plugins['job_stats']['slug'],
			'source'             => $bundled_plugins['job_stats']['source'],
			'required'           => false,
			'version'            => $bundled_plugins['job_stats']['version'],
			'force_activation'   => false,
			'force_deactivation' => false
		),

		array(
			'name'               => $bundled_plugins['job_company']['name'],
			'slug'               => $bundled_plugins['job_company']['slug'],
			'source'             => $bundled_plugins['job_company']['source'],
			'required'           => false,
			'version'            => $bundled_plugins['job_company']['version'],
			'force_activation'   => false,
			'force_deactivation' => false
		),

		array(
			'name'               => $bundled_plugins['job_tags']['name'],
			'slug'               => $bundled_plugins['job_tags']['slug'],
			'source'             => $bundled_plugins['job_tags']['source'],
			'required'           => false,
			'version'            => $bundled_plugins['job_tags']['version'],
			'force_activation'   => false,
			'force_deactivation' => false
		),

		array(
			'name'               => $bundled_plugins['job_bookmarks']['name'],
			'slug'               => $bundled_plugins['job_bookmarks']['slug'],
			'source'             => $bundled_plugins['job_bookmarks']['source'],
			'required'           => false,
			'version'            => $bundled_plugins['job_bookmarks']['version'],
			'force_activation'   => false,
			'force_deactivation' => false
		),

		array(
			'name'               => $bundled_plugins['job_resumes']['name'],
			'slug'               => $bundled_plugins['job_resumes']['slug'],
			'source'             => $bundled_plugins['job_resumes']['source'],
			'required'           => false,
			'version'            => $bundled_plugins['job_resumes']['version'],
			'force_activation'   => false,
			'force_deactivation' => false
		),

		array(
			'name'               => $bundled_plugins['job_claim_listing']['name'],
			'slug'               => $bundled_plugins['job_claim_listing']['slug'],
			'source'             => $bundled_plugins['job_claim_listing']['source'],
			'required'           => false,
			'version'            => $bundled_plugins['job_claim_listing']['version'],
			'force_activation'   => false,
			'force_deactivation' => false
		),

		array(
			'name'               => $bundled_plugins['accesspress_social_login']['name'],
			'slug'               => $bundled_plugins['accesspress_social_login']['slug'],
			'source'             => $bundled_plugins['accesspress_social_login']['source'],
			'required'           => false,
			'version'            => $bundled_plugins['accesspress_social_login']['version'],
			'force_activation'   => false,
			'force_deactivation' => false
		),

		array(
			'name'               => $bundled_plugins['job_extended_location']['name'],
			'slug'               => $bundled_plugins['job_extended_location']['slug'],
			'source'             => $bundled_plugins['job_extended_location']['source'],
			'required'           => false,
			'version'            => $bundled_plugins['job_extended_location']['version'],
			'force_activation'   => false,
			'force_deactivation' => false
		)

//		array(
//			'name'               => $bundled_plugins['facetwp']['name'],
//			'slug'               => $bundled_plugins['facetwp']['slug'],
//			'source'             => $bundled_plugins['facetwp']['source'],
//			'required'           => false,
//			'version'            => $bundled_plugins['facetwp']['version'],
//			'force_activation'   => false,
//			'force_deactivation' => false
//		),


//		array(
//			'name'               => $bundled_plugins['wpml']['name'],
//			'slug'               => $bundled_plugins['wpml']['slug'],
//			'source'             => $bundled_plugins['wpml']['source'],
//			'required'           => false,
//			'version'            => $bundled_plugins['wpml']['version'],
//			'force_activation'   => false,
//			'force_deactivation' => false
//		)

	);

	/**
	 * Array of configuration settings. Amend each line as needed.
	 * If you want the default strings to be available under your own theme domain,
	 * leave the strings uncommented.
	 * Some of the strings are added into a sprintf, so see the comments at the
	 * end of each line for what each argument will be.
	 */
	$config = array(
		'domain'       => 'knowherepro', // Text domain - likely want to be the same as your theme.
		'id'           => 'knowherepro',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => get_theme_file_path( 'admin/plugins/' ), // Default absolute path to bundled plugins.
		'menu'         => 'install-required-plugins',
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => true,                   // Automatically activate plugins after installation or not.
		'message'      => ''
	);

	tgmpa( $plugins, $config );

}