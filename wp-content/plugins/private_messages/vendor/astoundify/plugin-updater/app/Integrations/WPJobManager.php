<?php
/**
 * Output a setting for WP Job Manager
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
 * @class Astoundify_PluginUpdater
 */
class Astoundify_PluginUpdater_Integration_WPJobManager {

	/**
	 * Plugin
	 *
	 * @since 1.0.0
	 * @var Astoundify_PluginUpdater_Plugin
	 */
	protected $plugin;

	/**
	 * License
	 *
	 * @since 1.0.0
	 * @var Astoundify_PluginUpdater_Plugin
	 */
	protected $license;

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct( $plugin_file ) {
		$this->plugin = new Astoundify_PluginUpdater_Plugin( $plugin_file );
		$this->license = new Astoundify_PluginUpdater_License( $plugin_file );

		// add a setting with a type the same name of the plugin slug.
		add_action( 'wp_job_manager_admin_field_' . $this->plugin->get_slug() . '_license', array( $this, 'license_field' ), 10, 4 );
	}

	/**
	 * Output field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function license_field( $option, $attributes, $value, $placeholder ) {
		$status = $this->license->get_status();
		$deactivate_link = Astoundify_PluginUpdater_Helpers::deactivate_license_link( 
			$this->plugin->get_file(),
			admin_url( 'edit.php?post_type=job_listing&page=job-manager-settings' )
		);
?>

<input id="setting-<?php echo $option[ 'name' ]; ?>" class="regular-text" type="text" name="<?php echo $option[ 'name' ]; ?>" value="<?php esc_attr_e( $value ); ?>" <?php echo implode( ' ', $attributes ); ?> <?php echo $placeholder; ?> />

<?php if ( $option['desc'] ) { ?>
	<p class="description"><?php echo $option['desc']; ?></p>
<?php } ?>

<?php if( $status !== false && $status == 'valid' ) { ?>
	<p>
		<a href="<?php echo esc_url( $deactivate_link ); ?>" class="button-secondary"><?php _e( 'Deactivate', 'wp-job-manager-stats' ); ?></a>
	</p>
<?php } ?>

<?php
	}

}
