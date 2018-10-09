<?php
/**
 * Installation related functions and actions.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 */
class KW_Install {

	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'check_version' ), 5 );
		add_filter( 'wpmu_drop_tables', array( __CLASS__, 'wpmu_drop_tables' ) );
	}

	/**
	 *
	 * This check is done on all requests and runs if the versions do not match.
	 */
	public static function check_version() {
		self::install();
	}

	/**
	 * Install WC.
	 */
	public static function install() {
		if ( ! is_blog_installed() ) {
			return;
		}

		// Check if we are not already running this routine.
		if ( 'yes' === get_transient( 'kw_installing' ) ) {
			return;
		}

		// If we made it till here nothing is running yet, lets set the transient now.
		set_transient( 'kw_installing', 'yes', MINUTE_IN_SECONDS * 10 );

		if ( ! defined( 'KW_INSTALLING' ) ) {
			define( 'KW_INSTALLING', true );
		}

		self::create_tables();

		delete_transient( 'kw_installing' );

	}

	/**
	 * Set up the database tables which the plugin needs to function.
	 */
	private static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( self::get_schema() );
	}

	/**
	 *
	 * @return string
	 */
	private static function get_schema() {
		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}

		$tables = "
			CREATE TABLE {$wpdb->prefix}kw_attribute_taxonomies (
			  attribute_id BIGINT UNSIGNED NOT NULL auto_increment,
			  attribute_name varchar(200) NOT NULL,
			  attribute_label varchar(200) NULL,
			  attribute_type varchar(20) NOT NULL,
			  attribute_category varchar(20) NOT NULL,
			  PRIMARY KEY  (attribute_id),
			  KEY attribute_name (attribute_name(20))
			) $collate;
		";

		/**
		 * Term meta is only needed for old installs and is now @deprecated by WordPress term meta.
		 */
		if ( ! function_exists( 'get_term_meta' ) ) {
			$tables .= "
				CREATE TABLE {$wpdb->prefix}kw_termmeta (
				  meta_id BIGINT UNSIGNED NOT NULL auto_increment,
				  kw_term_id BIGINT UNSIGNED NOT NULL,
				  meta_key varchar(255) default NULL,
				  meta_value longtext NULL,
				  PRIMARY KEY  (meta_id),
				  KEY kw_term_id (kw_term_id),
				  KEY meta_key (meta_key(32))
				) $collate;
			";
		}

		return $tables;
	}

	/**
	 * @param  array $tables
	 * @return string[]
	 */
	public static function wpmu_drop_tables( $tables ) {
		global $wpdb;

		$tables[] = $wpdb->prefix . 'kw_attribute_taxonomies';
		$tables[] = $wpdb->prefix . 'kw_termmeta';

		return $tables;
	}

}

KW_Install::init();
