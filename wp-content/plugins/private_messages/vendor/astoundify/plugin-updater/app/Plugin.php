<?php
/**
 * Plugin
 *
 * @since 1.0.0
 *
 * @package Astoundify_PluginUpdater
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * A plugin to update.
 *
 * @since 1.0.0
 * @version 1.0.0 
 * @class Astoundify_PluginUpdater_Plugin
 */
class Astoundify_PluginUpdater_Plugin {

	/**
	 * Plugin file.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $file;

	/**
	 * Plugin data.
	 *
	 * @since 1.0.0
	 * @var array WP_Plugin (yeah right).
	 */
	protected $data;

	/**
	 * Setup a plugin.
	 *
	 * @since 1.0.0
	 * @return self
	 */
	public function __construct( $file = null ) {
		if ( ! $file ) {
			return;
		}

		$this->file = $file;
		$this->data = get_plugin_data( $file );
	}

	/**
	 * Get file.
	 *
	 * @since 1.0.0
	 *
	 * @return string $file
	 */
	public function get_file() {
		if ( ! $this->file ) {
			return;
		}

		return $this->file;
	}

	/**
	 * Get version.
	 *
	 * @since 1.0.0
	 *
	 * @return string $version
	 */
	public function get_version() {
		if ( ! $this->data ) {
			return;
		}

		return $this->data[ 'Version' ];
	}

	/**
	 * Get slug.
	 *
	 * @since 1.0.0
	 *
	 * @return string $slug
	 */
	public function get_slug() {
		if ( ! $this->file ) {
			return;
		}

		return str_replace( '.php', '', basename( $this->file ) );
	}

	/**
	 * Get name.
	 *
	 * @since 1.0.0
	 *
	 * @return string $name
	 */
	public function get_name() {
		if ( ! $this->data ) {
			return;
		}

		return $this->data[ 'Name' ];
	}

}
