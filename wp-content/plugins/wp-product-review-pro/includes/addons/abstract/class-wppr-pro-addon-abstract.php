<?php
/**
 * The file that defines the abstract class for addons.
 *
 * A class definition that includes attributes and functions used across
 * the addons of the plugin.
 *
 * @link       https://themeisle.com
 * @since      2.0.0
 *
 * @package    WPPR_Pro
 * @subpackage WPPR_Pro/includes/addons/abstract
 */

/**
 * Class WPPR_Pro_Addon_Abstract
 *
 * Inherited by all addons for this plugin.
 *
 * @since       2.0.0
 * @package     WPPR_Pro
 * @subpackage  WPPR_Pro/includes/addons/abstract
 */
abstract class WPPR_Pro_Addon_Abstract {

	/**
	 * The name of the addon.
	 *
	 * @since   2.0.0
	 * @access  protected
	 * @var string $name The name of the addon.
	 */
	protected $name;

	/**
	 * The slug o the addon.
	 *
	 * @since   2.0.0
	 * @access  protected
	 * @var string $slug The slug o the addon.
	 */
	protected $slug;

	/**
	 * The version of the addon.
	 *
	 * @since   2.0.0
	 * @access  protected
	 * @var string $version The version of the addon.
	 */
	protected $version;

	/**
	 * Instance of WPPR_Pro_Loader.
	 *
	 * @since   2.0.0
	 * @access  protected
	 * @var WPPR_Pro_Loader $loader Instance of WPPR_Pro_Loader.
	 */
	protected $loader;

	/**
	 * Instance of WPPR_Query_Model.
	 *
	 * @since   2.0.0
	 * @access  protected
	 * @var     WPPR_Query_Model $model Instance of WPPR_Query_Model.
	 */
	protected $model;

	/**
	 * Registers the hooks needed by the addon.
	 *
	 * @since   2.0.0
	 * @access  public
	 * @return mixed
	 */
	abstract public function hooks();

	/**
	 * Register the model to use.
	 *
	 * @since   2.0.0
	 * @access  public
	 * @param   WPPR_Query_Model $model An instance of WPPR_Query_Model.
	 */
	public final function register_model( WPPR_Query_Model $model ) {
		$this->model = $model;
	}

	/**
	 * Register the loader to use.
	 *
	 * @since   2.0.0
	 * @access  public
	 * @param   WPPR_Pro_Loader $loader An instance of WPPR_Query_Model.
	 */
	public final function register_loader( WPPR_Pro_Loader $loader ) {
		$this->loader = $loader;
	}
}
