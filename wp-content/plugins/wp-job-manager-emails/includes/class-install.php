<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Emails_Install {

	/**
	 * @type \WP_Job_Manager_Emails_Admin_Job|\WP_Job_Manager_Emails_Admin_Resume|\WP_Job_Manager_Emails_Admin
	 */
	protected $admin;

	/**
	 * Add CPT Role to Administrators
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $capability
	 */
	function add_admin_capability( $capability = false ){

		global $wp_roles;

		if( ! $capability ) $capability = $this->admin()->cpt()->get_capability();

		if( class_exists( 'WP_Roles' ) && ! isset($wp_roles) ) $wp_roles = new WP_Roles();

		if( is_object( $wp_roles ) ) $wp_roles->add_cap( 'administrator', $capability );

	}

	/**
	 * Add Default Email Templates
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	function add_default_emails(){

		$slug = $this->admin()->cpt()->get_slug();

		// Exit if we've already generated default emails on install or previous update
		if( get_option( "job_manager_emails_{$slug}_emails_generated" ) ) return;

		$hook_class = $this->admin()->cpt()->hooks();
		$email_keys = $hook_class->get_default_email_keys();
		$actions = $hook_class->get_actions();
		if( empty( $email_keys ) || empty($actions) ) return;

		$post_type = $this->admin->cpt()->get_post_type();

		// Loop through each email key and add default email if config exists
		foreach( $email_keys as $email_key ){
			// Goto next if defaults are not defined
			if( ! isset( $actions[$email_key], $actions[$email_key]['defaults'] ) ) continue;

			$defaults = $actions[ $email_key ]['defaults'];
			if( ! isset($defaults['hook']) ) $defaults['hook'] = $email_key;

			$email_generated = array(
				'post_status'  => 'disabled',
				'post_title'   => $defaults['post_title'],
				'post_content' => $defaults['post_content'],
				'post_type'    => $post_type
			);

			$email_post_id = wp_insert_post( $email_generated, TRUE );

			if( ! is_wp_error( $email_post_id ) ) {
				// Remove already set items from array
				unset($defaults['post_content']);

				foreach( $defaults as $default => $value ) {
					if( is_array( $value ) ) $value = maybe_serialize( $value );
					update_post_meta( $email_post_id, $default, $value );
				}

			}

		}

		// Update/Add option so we know emails have already been generated
		update_option( "job_manager_emails_{$slug}_emails_generated", JOB_MANAGER_EMAILS_VERSION );
	}

	/**
	 * Update email template meta from plain_text to email_format
	 *
	 *
	 * @since 2.0.0
	 *
	 */
	function update_plain_text(){

		$emails = $this->admin()->cpt()->get_posts( array( 'post_status' => 'any' ) );

		if( empty( $emails ) ) return;

		foreach( $emails as $email ){

			$email_format = ! empty( $email->plain_text ) ? 'plain' : 'html';
			$did_update = update_post_meta( $email->ID, 'email_format', $email_format );

			if( ! empty( $did_update ) ){
				delete_post_meta( $email->ID, 'plain_text', $email->plain_text );
			}
		}

	}

	/**
	 * \WP_Job_Manager_Emails_Admin_Job|\WP_Job_Manager_Emails_Admin_Resume
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_Job_Manager_Emails_Admin_Job|\WP_Job_Manager_Emails_Admin_Resume
	 */
	function admin(){
		return $this->admin;
	}
}