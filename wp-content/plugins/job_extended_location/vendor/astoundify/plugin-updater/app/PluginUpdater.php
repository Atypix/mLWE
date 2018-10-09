<?php
/**
 * Update a plugin.
 *
 * @since 1.0.0
 *
 * @package Astoundify_PluginUpdater
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

if ( ! class_exists( 'Astoundify_PluginUpdater' ) ) :
/**
 * Main PluginUpdater Class.
 *
 * @since 1.0.0
 * @version 1.0.0 
 * @class Astoundify_PluginUpdater
 */
class Astoundify_PluginUpdater {

	/**
	 * Monitor for updates in the admin.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct( $plugin_file ) {
		if ( ! is_admin() ) {
			return;
		}

		$this->includes();

		$api     = new Astoundify_PluginUpdater_Api();
		$plugin  = new Astoundify_PluginUpdater_Plugin( $plugin_file );
		$license = new Astoundify_PluginUpdater_License( $plugin_file );

		// monitor for actions
		Astoundify_PluginUpdater_Actions::init();

		return new EDD_SL_Plugin_Updater( $api->get_api_url(), $plugin->get_file(), array(
			'version' => $plugin->get_version(),
			'license' => $license->get_key(),
			'item_name' => $plugin->get_name(),
			'author' => 'Astoundify'
		) );
	}

	/**
	 * Include necessary files.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function includes() {
		if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
			require_once( dirname( __FILE__ ) . '/lib/EDD_SL_Plugin_Updater.php' );
		}

		require_once( dirname( __FILE__ ) . '/Helpers.php' );
		require_once( dirname( __FILE__ ) . '/Api.php' );
		require_once( dirname( __FILE__ ) . '/Plugin.php' );
		require_once( dirname( __FILE__ ) . '/License.php' );
		require_once( dirname( __FILE__ ) . '/Actions.php' );

		require_once( dirname( __FILE__ ) . '/Integrations/WPJobManager.php' );
	}

}
endif;
