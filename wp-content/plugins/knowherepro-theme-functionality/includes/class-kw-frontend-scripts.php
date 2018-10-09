<?php
/**
 * Handle frontend scripts
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 */
class KW_Frontend_Scripts {

	/**
	 * Contains an array of script handles registered by WC.
	 * @var array
	 */
	private static $scripts = array();

	/**
	 * Contains an array of script handles registered by WC.
	 * @var array
	 */
	private static $styles = array();

	/**
	 * Contains an array of script handles localized by WC.
	 * @var array
	 */
	private static $wp_localize_scripts = array();

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
		add_action( 'wp_print_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
		add_action( 'wp_print_footer_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
	}

	/**
	 * Add theme support for default WP themes.
	 *
	 * @since 3.0.0
	 */

	/**
	 * Get styles for the frontend.
	 *
	 * @return array
	 */
	public static function get_styles() {
		return apply_filters( 'kw_job_listing_enqueue_styles', array(
			'kw-job-listing-mod' => array(
				'src'     => self::get_asset_url( 'assets/css/kw-job-listing-mod.css' ),
				'deps'    => '',
				'version' => '1.0.0',
				'media'   => 'all'
			)
		) );
	}

	/**
	 * Return asset URL.
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	private static function get_asset_url( $path ) {
		return apply_filters( 'kw_get_asset_url', plugins_url( $path, KW_PLUGIN_FILE ), $path );
	}

	/**
	 * Register a script for use.
	 *
	 * @uses   wp_register_script()
	 * @access private
	 * @param  string   $handle
	 * @param  string   $path
	 * @param  string[] $deps
	 * @param  string   $version
	 * @param  boolean  $in_footer
	 */
	private static function register_script( $handle, $path, $deps = array( 'jquery' ), $version = null, $in_footer = true ) {
		self::$scripts[] = $handle;
		wp_register_script( $handle, $path, $deps, $version, $in_footer );
	}

	/**
	 * Register and enqueue a script for use.
	 *
	 * @uses   wp_enqueue_script()
	 * @access private
	 * @param  string   $handle
	 * @param  string   $path
	 * @param  string[] $deps
	 * @param  string   $version
	 * @param  boolean  $in_footer
	 */
	private static function enqueue_script( $handle, $path = '', $deps = array( 'jquery' ), $version = null, $in_footer = true ) {
		if ( ! in_array( $handle, self::$scripts ) && $path ) {
			self::register_script( $handle, $path, $deps, $version, $in_footer );
		}
		wp_enqueue_script( $handle );
	}

	/**
	 * Register a style for use.
	 *
	 * @uses   wp_register_style()
	 * @access private
	 * @param  string   $handle
	 * @param  string   $path
	 * @param  string[] $deps
	 * @param  string   $version
	 * @param  string   $media
	 */
	private static function register_style( $handle, $path, $deps = array(), $version = null, $media = 'all' ) {
		self::$styles[] = $handle;
		wp_register_style( $handle, $path, $deps, $version, $media );
	}

	/**
	 * Register and enqueue a styles for use.
	 *
	 * @uses   wp_enqueue_style()
	 * @access private
	 * @param  string   $handle
	 * @param  string   $path
	 * @param  string[] $deps
	 * @param  string   $version
	 * @param  string   $media
	 */
	private static function enqueue_style( $handle, $path = '', $deps = array(), $version = null, $media = 'all' ) {
		if ( ! in_array( $handle, self::$styles ) && $path ) {
			self::register_style( $handle, $path, $deps, $version, $media );
		}
		wp_enqueue_style( $handle );
	}

	/**
	 * Register all WC scripts.
	 */
	private static function register_scripts() {
		$suffix           = defined( 'WP_DEBUG' ) ? '' : '.min';
		$register_scripts = array(
//			'kw-vue-js' => array(
//				'src' => 'https://unpkg.com/vue',
//				'deps' => array()
//			),
			'kw-job-listing-mod' => array(
				'src'     => self::get_asset_url( 'assets/js/job-listing-mod'. $suffix .'.js' ),
				'deps'    => array(),
				'version' => '1.0.0',
			)
		);
		foreach ( $register_scripts as $name => $props ) {
			self::register_script( $name, $props['src'], $props['deps'], $props['version'] );
		}
	}

	/**
	 * Register all WC sty;es.
	 */
	private static function register_styles() {
		$register_styles = array(
			'kw-job-listing-mod' => array(
				'src'     => self::get_asset_url( 'assets/css/kw-job-listing-mod.css' ),
				'deps'    => array(),
				'version' => '1.0.0'
			)
		);
		foreach ( $register_styles as $name => $props ) {
			self::register_style( $name, $props['src'], $props['deps'], $props['version'], 'all' );
		}
	}

	/**
	 * Register/queue frontend scripts.
	 */
	public static function load_scripts() {
		global $post;

		self::register_scripts();
		self::register_styles();

		// Global frontend scripts
		self::enqueue_script( 'kw-job-listing-mod' );

		// CSS Styles
		if ( $enqueue_styles = self::get_styles() ) {
			foreach ( $enqueue_styles as $handle => $args ) {
				self::enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'] );
			}
		}
	}

	/**
	 * @access private
	 * @param  string $handle
	 */
	private static function localize_script( $handle ) {
		if ( ! in_array( $handle, self::$wp_localize_scripts ) && wp_script_is( $handle ) && ( $data = self::get_script_data( $handle ) ) ) {
			$name                        = str_replace( '-', '_', $handle ) . '_params';
			self::$wp_localize_scripts[] = $handle;
			wp_localize_script( $handle, $name, apply_filters( $name, $data ) );
		}
	}

	/**
	 * Return data for script handles.
	 * @access private
	 * @param  string $handle
	 * @return array|bool
	 */
	private static function get_script_data( $handle ) {
		global $wp;

		$ajax_url         = WP_Job_Manager_Ajax::get_endpoint();

		switch ( $handle ) {
			case 'kw-job-listing-mod' :
				return array(
					'ajax_url' => $ajax_url
				);
			break;
		}
		return false;
	}

	/**
	 * Localize scripts only when enqueued.
	 */
	public static function localize_printed_scripts() {
		foreach ( self::$scripts as $handle ) {
			self::localize_script( $handle );
		}
	}
}

KW_Frontend_Scripts::init();
