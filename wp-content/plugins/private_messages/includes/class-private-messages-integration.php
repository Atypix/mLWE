<?php
/**
 * An integration that provides custom functionality when a plugin is active.
 *
 * @class Private_Messages_Integration
 * @version 1.0.0
 * @author Astoundify
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Private_Messages_Integration {

	/**
	 * Is active.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	public $is_active;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( $this->is_active() ) {
			$this->setup_actions();
		}
	}

	/**
	 * Integration hooks/filters.
	 *
	 * @access public
	 * @return void
	 */
	public function setup_actions() {}

	/**
	 * Check if active.
	 *
	 * @access public
	 * @return bool $is_active
	 */
	public function is_active() {
		return $this->is_active;
	}

}
