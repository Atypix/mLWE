<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WP_Job_Manager_Field_Editor_Conditionals_Resume
 *
 * @since 1.7.10
 *
 */
class WP_Job_Manager_Field_Editor_Conditionals_Resume extends WP_Job_Manager_Field_Editor_Conditionals {


	/**
	 * Actions/Filters
	 *
	 *
	 * @since 1.7.10
	 *
	 */
	public function hooks() {

		add_action( 'submit_resume_form_resume_fields_start', array( $this, 'form' ) );

	}

	/**
	 * Add Filter for Fields (to set required false)
	 *
	 *
	 * @since 1.7.10
	 *
	 */
	public function add_fields_filter() {

		if ( empty( $_POST['submit_resume'] ) ) {
			return;
		}

		add_filter( 'submit_resume_form_fields', array( $this, 'set_required_false' ), 9999999999 );
	}

	/**
	 * Get Logic Fields
	 *
	 *
	 * @since 1.7.10
	 *
	 * @return array|bool
	 */
	public function get_logic(){

		if ( $this->logic !== null ) {
			return $this->logic;
		}

		$logic = get_option( 'field_editor_resume_conditional_logic', array() );

		// Remove any disabled field groups
		$this->logic = wp_list_filter( $logic, array( 'status' => 'disabled' ), 'NOT' );

		if ( empty( $this->logic ) ) {
			return false;
		}

		return $this->logic;
	}

	/**
	 * Get Fields
	 *
	 *
	 * @since 1.7.10
	 *
	 */
	public function get_fields() {

		$jmfe   = WP_Job_Manager_Field_Editor_Fields::get_instance();
		$fields = $jmfe->get_fields( 'resume_fields' );

		return $fields;

	}

}