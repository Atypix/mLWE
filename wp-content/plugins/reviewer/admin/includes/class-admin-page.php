<?php

/**
 * Reviewer Plugin v.2
 * Created by Michele Ivani
 */
class RWP_Admin_Page
{
	// Instance of this class
	protected static $instance = null;

	// Slug of the plugin screen
	protected $plugin_slug = null;

	// Page fields
	protected $capability = 'manage_options';
	protected $menu_slug = '';
	protected $icon_url = 'dashicons-star-filled';

	function __construct()
	{
		// Call $plugin_slug from public plugin class
		$this->plugin_slug = 'reviewer';
	}
	public static function get_instance() 
	{
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	protected function is_licensed() 
	{
		$license = get_option( get_option( 'rwp_license' ) );

		if( isset( $license['license'] ) && $license['license'] ) {
			return true;
		}

		return false;
	}

	protected function check_license() 
	{
		if( !$this->is_licensed() ) {
			wp_redirect( menu_page_url('reviewer-license-page', true), 200 );
			exit();
		}
	}

	protected function license_notice()
	{
		?>
		<div class="error notice"> 
			<p><strong><?php _e( 'Please register your Reviewer license before using the plugin. Go to Reviewer > License.', $this->plugin_slug ); ?></strong></p>
		</div>
		<?php
	}
}