<?php

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'Knowhere_Job_Views_Counter' ) ) :

	final class Knowhere_Job_Views_Counter {

		private static $instance;
		public $options;
		public $defaults = array(
			'general'	 => array(
				'time_between_counts'	 => array(
					'number' => 1,
					'type'	 => 'minutes'
				),
				'reset_counts'			 => array(
					'number' => 30,
					'type'	 => 'days'
				),
				'flush_interval'		 => array(
					'number' => 1,
					'type'	 => 'minutes'
				),
				'deactivation_delete'	 => false
			),
		);

		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Knowhere_Job_Views_Counter ) ) {

				self::$instance = new Knowhere_Job_Views_Counter;
				self::$instance->define_constants();
				self::$instance->includes();
				self::$instance->settings = new Knowhere_Knowhere_Job_Views_Counter_Settings();
				self::$instance->counter = new Knowhere_Views_Counter_Counter();
			}
			return self::$instance;
		}

		/**
		 * Setup plugin constants.
		 *
		 * @return void
		 */
		private function define_constants() {
			define( 'KNOWHERE_JOB_VIEWS_COUNTER_URL', get_theme_file_uri('config-job-manager/php/wp-job-post-views') );
			define( 'KNOWHERE_JOB_VIEWS_COUNTER_PATH', get_theme_file_path('config-job-manager/php/wp-job-post-views') );
		}

		/**
		 * Include required files.
		 *
		 * @return void
		 */
		private function includes() {
			include_once( KNOWHERE_JOB_VIEWS_COUNTER_PATH . '/settings.php' );
			include_once( KNOWHERE_JOB_VIEWS_COUNTER_PATH . '/wp-job-post-views-counter.php' );
		}

		/**
		 * Class constructor.
		 *
		 * @return void
		 */
		public function __construct() {

			add_action('after_setup_theme', array($this, 'activation'));
			add_action('after_switch_theme', array($this, 'deactivation'));

			// settings
			$this->options = array(
				'general' => array_merge($this->defaults['general'], get_option( 'kw_post_views_counter_settings_general', $this->defaults['general'] ))
			);

		}

		/**
		 * Plugin activation function.
		 */
		public function activation() {
			$this->activate_single();
		}

		public function deactivation() {
			$this->deactivate_single();
		}

		public function activate_single() {
			global $wpdb, $charset_collate;

			// required for dbdelta
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			// create post views table
			dbDelta( '
				CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'knowhere_job_listing_post_views (
					id bigint unsigned NOT NULL,
					type tinyint(1) unsigned NOT NULL,
					period varchar(8) NOT NULL,
					count bigint unsigned NOT NULL,
					PRIMARY KEY  (type, period, id),
					UNIQUE INDEX id_type_period_count (id, type, period, count) USING BTREE,
					INDEX type_period_count (type, period, count) USING BTREE
				) ' . $charset_collate . ';'
			);

			add_option( 'kw_post_views_counter_settings_general', $this->defaults['general'], '', 'no' );

		}

		public function deactivate_single() {

			$check = $this->options['general']['deactivation_delete'];

			if ( $check ) {
				global $wpdb;

				// delete table from database
				$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'knowhere_job_listing_post_views' );
			}
		}

		/**
		 * Load pluggable template functions.
		 */
		public function load_pluggable_functions() {
			include_once( KNOWHERE_JOB_VIEWS_COUNTER_PATH . '/functions.php' );
		}

	}

endif; // end if class_exists check

/**
 * @return object
 */
function Knowhere_Job_Views_Counter() {
	static $instance;

	if ( $instance === null || ! ( $instance instanceof Knowhere_Job_Views_Counter ) )
		$instance = Knowhere_Job_Views_Counter::instance();

	return $instance;
}

Knowhere_Job_Views_Counter();
