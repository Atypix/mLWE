<?php
/**
 * Easy Digital Downloads - Frontend Submissions
 *
 * @class Private_Messages_Integration_FES
 * @version 1.0.0
 * @author Astoundify
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Private_Messages_Integration_FES extends Private_Messages_Integration {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->is_active = class_exists( 'EDD_Front_End_Submissions' );

		parent::__construct();
	}

	/**
	 * Integration hooks/filters.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function setup_actions() {
		add_filter( 'shortcode_atts_private-message', array( $this, 'filter_to' ) );
	}

	/**
	 * Filter who the shortcode sends to if we are on a Vendor page or a download.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array $atts
	 */
	public function filter_to( $atts ) {
		// Check a download.
		if ( is_singular( 'download' ) ) {
			$atts['to'] = get_post()->post_author;

			return $atts;
		}

		// Check vendor page.
		$user = fes_get_vendor();

		if ( $user ) {
			$atts['to'] = $user->ID;

			return $atts;
		}

		return $atts;
	}

}

new Private_Messages_Integration_FES();
