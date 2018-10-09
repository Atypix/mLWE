<?php
/**
 * Load plugin's files with check for installing it as a standalone plugin or
 * a module of a theme / plugin. If standalone plugin is already installed, it
 * will take higher priority.
 *
 * @package Meta Box
 */

/**
 * Plugin loader class.
 *
 * @package Meta Box
 */
class MAD_Loader {

	/**
	 * Define plugin constants.
	 */
	protected function constants() {
		// Script version, used to add version for scripts and styles
		define( 'MAD_VER', '1.0' );

		list( $path, $url ) = self::get_path();

		// Plugin URLs, for fast enqueuing scripts and styles
		define( 'MAD_URL', $url );
		define( 'MAD_JS_URL', trailingslashit( MAD_URL . 'js' ) );
		define( 'MAD_CSS_URL', trailingslashit( MAD_URL . 'css' ) );

		// Plugin paths, for including files
		define( 'MAD_DIR', $path );
		define( 'MAD_INC_DIR', trailingslashit( MAD_DIR . 'inc' ) );
	}

	/**
	 * Get plugin base path and URL.
	 * The method is static and can be used in extensions.
	 *
	 * @link http://www.deluxeblogtips.com/2013/07/get-url-of-php-file-in-wordpress.html
	 * @param string $path Base folder path
	 * @return array Path and URL.
	 */
	public static function get_path( $base = '' )
	{
		// Plugin base path
		$path        = $base ? $base : dirname( dirname( __FILE__ ) );
		$path        = wp_normalize_path( untrailingslashit( $path ) );
		$content_dir = wp_normalize_path( untrailingslashit( WP_CONTENT_DIR ) );

		// Default URL
		$url = plugins_url( '', $path . '/' . basename( $path ) . '.php' );

		// Included into themes
		if (
			0 !== strpos( $path, wp_normalize_path( WP_PLUGIN_DIR ) )
			&& 0 !== strpos( $path, wp_normalize_path( WPMU_PLUGIN_DIR ) )
			&& 0 === strpos( $path, $content_dir )
		)
		{
			$content_url = untrailingslashit( dirname( dirname( get_stylesheet_directory_uri() ) ) );
			$url         = str_replace( $content_dir, $content_url, $path );
		}

		$path = trailingslashit( $path );
		$url  = trailingslashit( $url );

		return array( $path, $url );
	}

	/**
	 * Bootstrap the plugin.
	 */
	public function init() {
		$this->constants();

		// Register autoload for classes
		require_once MAD_INC_DIR . 'autoloader.php';
		$autoloader = new MAD_Autoloader;
		$autoloader->add( MAD_INC_DIR, 'MAD_RW_' );
		$autoloader->add( MAD_INC_DIR, 'MAD_' );
		$autoloader->add( MAD_INC_DIR . 'fields', 'MAD_', '_Field' );
		$autoloader->add( MAD_INC_DIR . 'walkers', 'MAD_Walker_' );
		$autoloader->register();

		// Plugin core
		new MAD_Core;

		// Plugin Conditional Logic
		new MAD_Conditional;

		if ( is_admin() ) {
			// Validation module
			new MAD_Validation;

			$sanitize = new MAD_Sanitizer;
			$sanitize->init();
		}

		// Public functions
		require_once MAD_INC_DIR . 'functions.php';
	}
}
