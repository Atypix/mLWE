<?php

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * The main theme class.
 */
class KnowherePro {

	/**
	 * The template directory URL.
	 *
	 * @static
	 * @access public
	 * @var string
	 */
	public static $template_dir_url = '';

	/**
	 * The one, true instance of the KnowherePro object.
	 *
	 * @static
	 * @access public
	 * @var null|object
	 */
	public static $instance = null;

	/**
	 * The theme version.
	 *
	 * @static
	 * @access public
	 * @var string
	 */
	public static $version = '1.0';

	/**
	 * Determine if we're currently upgrading/migration options.
	 *
	 * @static
	 * @access public
	 * @var bool
	 */
	public static $is_updating  = false;

	/**
	 * Bundled Plugins.
	 *
	 * @static
	 * @access public
	 * @var array
	 */
	public static $bundled_plugins = array();

	/**
	 * Knowhere_Product_registration
	 *
	 * @access public
	 * @var object Knowhere_Product_registration.
	 */
	public $registration;

	/**
	 * Access the single instance of this class.
	 *
	 * @return KnowherePro
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new KnowherePro();
		}
		return self::$instance;
	}

	/**
	 * The class constructor
	 */
	private function __construct() {

		// Initialize bundled plugins array.
		self::$bundled_plugins = array(
			'theme_functionality' => array( 'slug' => 'knowherepro-theme-functionality', 'name' => esc_html__('KnowherePro Theme - Functionality', 'knowherepro'), 'source' => 'http://velikorodnov.com/wordpress/sample-data/knowhere/plugins/knowherepro-theme-functionality.zip', 'version' => '1.1.5' ),
			'composer' => array( 'slug' => 'js_composer', 'name' => esc_html__('WPBakery Page Builder', 'knowherepro'), 'source' => 'http://velikorodnov.com/wordpress/sample-data/pluginus/js_composer.zip', 'version' => '5.4.5' ),
			'reviewer' => array( 'slug' => 'reviewer', 'name' => esc_html__('Reviewer', 'knowherepro'), 'source' => 'http://velikorodnov.com/wordpress/sample-data/knowhere/plugins/reviewer.zip', 'version' => '3.14.1' ),
			'accesspress_social_login' => array( 'slug' => 'accesspress_social_login', 'name' => esc_html__('AccessPress Social Login', 'knowherepro'), 'source' => 'http://velikorodnov.com/wordpress/sample-data/knowhere/plugins/accesspress-social-login.zip', 'version' => '1.2.9' ),
			'facetwp' => array( 'slug' => 'facetwp', 'name' => esc_html__('FacetWP', 'knowherepro'), 'source' => 'http://velikorodnov.com/wordpress/sample-data/knowhere/plugins/facetwp.zip', 'version' => '3.0.9.1' ),
			'private_messages' => array( 'slug' => 'private_messages', 'name' => esc_html__('Private Messages', 'knowherepro'), 'source' => 'http://velikorodnov.com/wordpress/sample-data/knowhere/plugins/private-messages.zip', 'version' => '1.9.1' ),
			'flashsale' => array( 'slug' => 'flashsale', 'name' => esc_html__('WooCommerce Flash Sale Pricing and Discounts', 'knowherepro'), 'source' => 'http://velikorodnov.com/wordpress/sample-data/knowhere/plugins/woocommerce-flashsale-pricing-and-discounts.zip', 'version' => '3.1' ),
			'job_tags' => array( 'slug' => 'job_tags', 'name' => esc_html__('WP Job Manager - Job Tags', 'knowherepro'), 'source' => 'http://velikorodnov.com/wordpress/sample-data/knowhere/plugins/wp-job-manager-tags.zip', 'version' => '1.3.8' ),
			'job_applications' => array( 'slug' => 'job_applications', 'name' => esc_html__('WP Job Manager Applications', 'knowherepro'), 'source' => 'http://velikorodnov.com/wordpress/sample-data/knowhere/plugins/wp-job-manager-applications.zip', 'version' => '2.2.4' ),
			'job_paid_listings' => array( 'slug' => 'job_paid_listings', 'name' => esc_html__('WC Paid Listings', 'knowherepro'), 'source' => 'http://velikorodnov.com/wordpress/sample-data/knowhere/plugins/wp-job-manager-wc-paid-listings.zip', 'version' => '2.7.2' ),
			'job_company' => array( 'slug' => 'job_company', 'name' => esc_html__('WP Job Manager - Company Profiles', 'knowherepro'), 'source' => 'http://velikorodnov.com/wordpress/sample-data/knowhere/plugins/wp-job-manager-companies.zip', 'version' => '1.3' ),
			'job_stats' => array( 'slug' => 'job_stats', 'name' => esc_html__('Stats for WP Job Manager', 'knowherepro'), 'source' => 'http://velikorodnov.com/wordpress/sample-data/knowhere/plugins/wp-job-manager-stats.zip', 'version' => '2.6.0' ),
			'job_bookmarks' => array( 'slug' => 'job_bookmarks', 'name' => esc_html__('WP Job Manager - Bookmarks', 'knowherepro'), 'source' => 'http://velikorodnov.com/wordpress/sample-data/knowhere/plugins/wp-job-manager-bookmarks.zip', 'version' => '1.2.1' ),
			'job_resumes' => array( 'slug' => 'job_resumes', 'name' => esc_html__('WP Job Manager - Resume Manager', 'knowherepro'), 'source' => 'http://velikorodnov.com/wordpress/sample-data/knowhere/plugins/wp-job-manager-resumes.zip', 'version' => '1.15.4' ),
			'job_claim_listing' => array( 'slug' => 'job_claim_listing', 'name' => esc_html__('Claim Listing for WP Job Manager', 'knowherepro'), 'source' => 'http://velikorodnov.com/wordpress/sample-data/knowhere/plugins/wp-job-manager-claim-listing.zip', 'version' => '3.7.0' ),
			'job_extended_location' => array( 'slug' => 'job_extended_location', 'name' => esc_html__('Extended Location for WP Job Manager', 'knowherepro'), 'source' => 'http://velikorodnov.com/wordpress/sample-data/knowhere/plugins/wp-job-manager-extended-location.zip', 'version' => '3.4.0' ),
			'wpml' => array( 'slug' => 'sitepress-multilingual-cms', 'name' => esc_html__('WPML Multilingual CMS', 'knowherepro'), 'source' => 'http://velikorodnov.com/wordpress/sample-data/pluginus/sitepress-multilingual-cms.zip', 'version' => '3.8.2' ),
		);

	}

	/**
	 * Gets the theme version.
	 *
	 * @since 5.0
	 *
	 * @return string
	 */
	public static function get_theme_version() {
		return self::$version;
	}

	/**
	 * Gets the bundled plugins.
	 *
	 * @since 5.0
	 *
	 * @return array Array of bundled plugins.
	 */
	public static function get_bundled_plugins() {
		return self::$bundled_plugins;
	}

}