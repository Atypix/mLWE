<?php
/**
 * Helper functions.
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
 * WP Job Manager
 *
 * @since 1.0.0
 * @version 1.0.0 
 * @class Astoundify_PluginUpdater_Hlpers
 */
class Astoundify_PluginUpdater_Helpers {

	/**
	 * Create a deactivation link.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_file
	 */
	public static function deactivate_license_link( $plugin_file, $redirect = false ) {
		if ( ! $redirect ) {
			$redirect = admin_url();
		}

		$query_args = array(
			'astoundify-pluginupdater' => 'deactivate-license',
			'plugin_file' => $plugin_file
		);

		$url = add_query_arg( $query_args, $redirect );

		return wp_nonce_url( $url, 'deactivate-license' );
	}

}
