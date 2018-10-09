<?php
/**
 * Autoload plugin classes.
 *
 * @package Meta Box
 */

/**
 * Autoload class
 */
class MAD_Autoloader {

	/**
	 * List of directories to load classes.
	 *
	 * @var array
	 */
	protected $dirs = array();

	/**
	 * Adds a base directory for a class name prefix and/or suffix.
	 *
	 * @param string $base_dir A base directory for class files.
	 * @param string $prefix   The class name prefix.
	 * @param string $suffix   The class name suffix.
	 */
	public function add( $base_dir, $prefix, $suffix = '' ) {
		$this->dirs[] = array(
			'dir'    => trailingslashit( $base_dir ),
			'prefix' => $prefix,
			'suffix' => $suffix,
		);
	}

	/**
	 * Register autoloader for plugin classes.
	 * In PHP 5.3, SPL extension cannot be disabled and it's safe to use autoload.
	 * However, hosting providers can disable it in PHP 5.2. In that case, we provide a fallback for autoload.
	 *
	 * @link http://php.net/manual/en/spl.installation.php
	 * @link https://github.com/rilwis/meta-box/issues/810
	 */
	public function register() {
		spl_autoload_register( array( $this, 'autoload' ) );
	}

	/**
	 * Autoload fields' classes.
	 *
	 * @param string $class Class name
	 * @return mixed Boolean false if no mapped file can be loaded, or the name of the mapped file that was loaded.
	 */
	public function autoload( $class ) {

		foreach ( $this->dirs as $dir ) {
			if (
				( $dir['prefix'] && 0 !== strpos( $class, $dir['prefix'] ) )
				&& ( $dir['suffix'] && substr( $class, - strlen( $dir['suffix'] ) ) !== $dir['suffix'] )
			) {
				continue;
			}
			$file = substr( $class, strlen( $dir['prefix'] ) );

			if ( $dir['suffix'] && strlen( $file ) > strlen( $dir['suffix'] ) ) {
				$file = substr( $file, 0, - strlen( $dir['suffix'] ) );
			}
			$file = strtolower( str_replace( '_', '-', $file ) ) . '.php';
			$file = $dir['dir'] . $file;

			if ( $this->require_file( $file ) ) {
				return $file;
			}
		}
		return false;
	}

	/**
	 * If a file exists, require it from the file system.
	 *
	 * @param string $file The file to require.
	 * @return bool True if the file exists, false if not.
	 */
	protected function require_file( $file ) {
		if ( file_exists( $file ) ) {
			require_once $file;
			return true;
		}
		return false;
	}
}
