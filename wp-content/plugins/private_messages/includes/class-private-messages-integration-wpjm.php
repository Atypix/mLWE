<?php
/**
 * WP Job Manager
 *
 * @class Private_Messages_Integration_WPJM
 * @version 1.0.0
 * @author Astoundify
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Private_Messages_Integration_WPJM extends Private_Messages_Integration {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->is_active = class_exists( 'WP_Job_Manager' );

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

		if ( $this->is_active() ) {
			add_filter( 'private_messages_settings', array( $this, 'add_settings' ) );
			add_filter( 'the_job_application_method', array( $this, 'application_method' ), 10, 2 );
			add_action( 'job_manager_application_details_pm', array( $this, 'details_pm' ) );
		}
	}

	/**
	 * Add Field in General Settings.
	 *
	 * @since 1.5.0
	 *
	 * @param array $setting WPJM Setting.
	 */
	public function add_settings( $settings ) {
		$settings['wpjm'] = array(
			'label'  => __( 'WP Job Manager', 'private-messages' ),
			'fields' => array(
				array(
					'id'           => 'pm_wpjm_contact_method',
					'label'        => __( 'Listing Contact Method', 'private-messages' ),
					'description'  => __( 'Use private messages for communication between logged in users and listing owners.', 'private-messages' ),
					'type'         => 'checkbox',
					'default'      => true,
					'sanitize'     => 'wp_validate_boolean',
				),
				array(
					'id'           => 'pm_wpjm_contact_method_logout',
					'label'        => __( 'Logged Out User', 'private-messages' ),
					'description'  => __( 'Display login link for logged out user.', 'private-messages' ),
					'type'         => 'checkbox',
					'default'      => false,
					'sanitize'     => 'wp_validate_boolean',
				),
			),
		);
		return $settings;
	}

	/**
	 * Filter the application method to PM.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $method
	 * @return array $atts
	 */
	public function application_method( $method, $post ) {
		// Default.
		$enable = false;

		// Enabled as contact method.
		if ( pm_get_option( 'pm_wpjm_contact_method', true ) && is_user_logged_in() ) {
			$enable = true;
		}

		// Enabled for logged out and user logged out.
		if ( pm_get_option( 'pm_wpjm_contact_method_logout', false ) && ! is_user_logged_in() ) {
			$enable = true;
		}

		// No author, always false.
		if ( ! $post->post_author ) {
			$enable = false;
		}

		// Implement if enable.
		if ( $enable ) {

			// Create object, if listing have no application method.
			if ( ! $method ) {
				$method = new stdClass();
			}

			$method->type    = 'pm'; // Load "job_manager_application_details_pm" hook.
			$method->subject = apply_filters( 'private_messages_wpjm_default_subject', $post->post_title );
			$method->message = apply_filters( 'private_messages_wpjm_default_message', sprintf( __( 'Listing URL: %s', 'private-messages' ), get_permalink( $post ) ) );
			$method->to      = $post->post_author;
		}

		return $method;
	}

	/**
	 * Ouput the PM form.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $method
	 * @return array $atts
	 */
	public function details_pm( $apply ) {
		wp_enqueue_style( 'private-messages-frontend' );
		echo Private_Messages_Templates::get_template( 'frontend/job-application-pm.php', array(
			'apply' => $apply,
		) );
	}

}

new Private_Messages_Integration_WPJM();
