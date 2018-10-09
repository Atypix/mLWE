<?php
/**
 * The file that defines Related Reviews Widget Addon.
 *
 * @link       https://themeisle.com
 * @since      2.0.0
 *
 * @package    WPPR_Pro
 * @subpackage WPPR_Pro/includes/addons/abstract
 */

/**
 * Class WPPR_Pro_Related_Reviews
 *
 * @since       2.0.0
 * @package     WPPR_Pro
 * @subpackage  WPPR_Pro/includes/addons
 */
class WPPR_Pro_Related_Reviews extends WPPR_Pro_Addon_Abstract {

	/**
	 * WPPR_Pro_Listings constructor.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public function __construct() {
		$this->name = __( 'Pro Related reviews' , 'wp-product-review' );
		$this->slug = 'wppr-pro-related-reviews';
		$this->version = '1.2.1';
	}

	/**
	 * Registers the hooks needed by the addon.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public function hooks() {

		$this->model = new WPPR_Query_Model();

		$widget = new WPPR_Pro_Related_Reviews_Widget();
		$this->loader->add_action( 'widgets_init', $widget, 'register' );
		$this->loader->add_action( 'admin_notices', $widget, 'widget_admin_notice' );
	}
}
