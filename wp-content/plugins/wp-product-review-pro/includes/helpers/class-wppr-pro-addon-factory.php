<?php
/**
 * The file that defines a factory class for building other addons.
 *
 * @link       https://themeisle.com
 * @since      2.0.0
 *
 * @package    WPPR_Pro
 * @subpackage WPPR_Pro/includes/helpers
 */

/**
 * Class WPPR_Pro_Addon_Factory
 *
 * A factory class for building addons.
 *
 * @since       2.0.0
 * @package     WPPR_Pro
 * @subpackage  WPPR_Pro/includes/helpers
 */
class WPPR_Pro_Addon_Factory {
	/**
	 * The build method for creating a new OBFX_Module class.
	 *
	 * @since   2.0.0
	 * @access  public
	 *
	 * @param   string $addon_name The name of the addon to instantiate.
	 *
	 * @return WPPR_Pro_Addon_Abstract
	 * @throws Exception Thrown if no addon class exists for provided $addon_name.
	 */
	public static function build( $addon_name ) {
		$addon = str_replace( '-', '_', implode( '-', array_map( 'ucfirst', explode( '-', $addon_name ) ) ) );
		$addon = str_replace( 'Wppr', 'WPPR', $addon );
		if ( class_exists( $addon ) ) {
			return new $addon;
		}
		// @codeCoverageIgnoreStart
		throw new Exception( 'Invalid addon name given.' );
		// @codeCoverageIgnoreEnd
	}
}
