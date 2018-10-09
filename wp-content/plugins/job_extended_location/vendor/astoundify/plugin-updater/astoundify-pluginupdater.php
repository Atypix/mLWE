<?php
/*
 * Plugin Name: Astoundify Plugin Updater
 * Plugin URI: https://astoundify.com
 * Description: Manage plugin licenses in the WordPress dashboard and allow automatic updates.
 * Version: 1.1.0
 * Author: Astoundify
 * Author URI: http://astoundify.com
 */

// require the app
require_once( dirname( __FILE__ ) . '/app/PluginUpdater.php' );

if ( ! function_exists( 'astoundify_pluginupdater' ) ) {
	/**
	 * Create a new instance of Astoundify_PluginUpdater
	 *
	 * @since 1.1.0
	 */
	function astoundify_pluginupdater( $file ) {
		return new Astoundify_PluginUpdater( $file );
	}
}
