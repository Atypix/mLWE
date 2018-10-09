<?php
/**
 * The file that defines Custom Icon Addon.
 *
 * @link       https://themeisle.com
 * @since      2.0.0
 *
 * @package    WPPR_Pro
 * @subpackage WPPR_Pro/includes/addons/abstract
 */

/**
 * Class WPPR_Pro_Custom_Icon
 *
 * @since       2.0.0
 * @package     WPPR_Pro
 * @subpackage  WPPR_Pro/includes/addons
 */
class WPPR_Pro_Custom_Icon extends WPPR_Pro_Addon_Abstract {

	/**
	 * WPPR_Pro_Custom_Icon constructor.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public function __construct() {
		$this->name    = __( 'Pro Custom Icon', 'wp-product-review' );
		$this->slug    = 'wppr-pro-custom-icon';
		$this->version = '1.1.1';
	}

	/**
	 * Registers the hooks needed by the addon.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public function hooks() {
		$this->loader->add_filter( 'wppr_settings_fields', $this, 'add_fields', 10, 1 );
		$this->loader->add_filter( 'wppr_get_old_option', $this, 'get_old_option', 10, 2 );

		$this->loader->add_action( 'admin_enqueue_scripts', $this, 'wppr_custom_bar_icon_scripts' );
	}

	/**
	 * Method to filter old value from DB.
	 *
	 * @since   2.0.0
	 * @access  public
	 *
	 * @param   string $value The value passed by the filter.
	 * @param   string $key The key passed by the filter.
	 *
	 * @return mixed
	 */
	public function get_old_option( $value, $key ) {
		$allowed_options = array(
			'cwppos_change_bar_icon',
		);
		if ( in_array( $key, $allowed_options ) && $value == false ) {
			$global_settings_fields = WPPR_Global_Settings::instance()->get_fields();
			$value                  = get_option( $key, isset( $global_settings_fields['pro_listings'][ $key ]['default'] ) ? $global_settings_fields['pro_listings'][ $key ]['default'] : '' );
		}

		return $value;
	}

	/**
	 * Registers a new fields list for the section defined in add_section().
	 *
	 * @since   2.0.0
	 * @access  public
	 *
	 * @param   array $fields The fields array.
	 *
	 * @return mixed
	 */
	public function add_fields( $fields ) {

		$pos        = array_search( 'cwppos_option_nr', array_keys( $fields['general'] ) );
		$new_fields = array(
			'cwppos_change_bar_icon' => array(
				'id'          => 'change_bar_icon',
				'name'        => __( 'Change Default Rating Icon', 'wp-product-review' ),
				'description' => __( 'Choose which icon would you like to use for the rating bar.', 'wp-product-review' ),
				'type'        => 'icon_font',
				'default'     => '',
			),
		);
		$start_part = array_slice( $fields['general'], 0, $pos + 1, true );
		$end_part   = array_slice( $fields['general'], $pos, null, true );

		$fields['general'] = array_merge( $start_part, $new_fields, $end_part );

		return $fields;
	}

	/**
	 * Register scripts and styles for this addon.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public function wppr_custom_bar_icon_scripts() {
		wp_enqueue_script( $this->slug . '-main-script', WPPR_PRO_ADDONS_ASSETS . 'js/wppr-pro-custom-icon.js', false, $this->version, 'all' );
		wp_enqueue_style( 'font-awesome-cdn', '//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css' );
	}

}
