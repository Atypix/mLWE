<?php

if ( !class_exists('Knowhere_WP_Job_Manager_Open_Hours') ) {

	class Knowhere_WP_Job_Manager_Open_Hours {

		public function __construct() {

			$this->setup_actions();

		}

		public function setup_actions() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'job_manager_update_job_data', array( $this, 'job_manager_update_job_data' ), 10, 2 );
			add_action( 'job_manager_save_job_listing', array( $this, 'job_manager_update_job_data' ), 10, 2 );
			add_action( 'knowhere_writepanels_open_hours', array( $this, 'output' ) );

			add_filter( 'submit_job_form_fields', array( $this, 'submit_job_form_fields' ) );
			add_filter( 'submit_job_form_fields_get_job_data', array( $this, 'get_job_data' ), 10, 2 );
		}

		public function enqueue_scripts() {
			wp_enqueue_script( 'timepicker', get_theme_file_uri('config-job-manager/assets/js/jquery.timepicker.min.js') );
			wp_enqueue_style( 'timepicker', get_theme_file_uri('config-job-manager/assets/css/jquery.timepicker.css') );
		}

		public function submit_job_form_fields( $fields ) {
			$fields['job']['job_hours'] = $this->fields();
			return $fields;
		}

		public function fields() {
			$args = array(
				'label'       => esc_html__( 'Opening Hours', 'knowherepro' ),
				'type'        => 'open-hours',
				'required'    => false,
				'placeholder' => '',
				'priority'    => 5,
				'default'     => ''
			);
			return $args;
		}

		public function get_job_data( $fields, $job ) {
			$hours = get_post_meta( $job->ID, '_job_hours', true );

			if ( ! $hours ) {
				return $fields;
			}

			$fields[ 'job' ][ 'job_hours' ][ 'value' ] = $hours;

			return $fields;
		}

		public function job_manager_update_job_data( $job_id, $values ) {
			if ( ! isset( $_POST[ 'job_hours' ] ) ) {
				return;
			}

			update_post_meta( $job_id, '_job_hours', stripslashes_deep( $_POST[ 'job_hours' ] ) );
		}

		public function output($post) {

			$field = $this->fields(); ?>

			<div class="form-field">

				<?php get_job_manager_template( 'form-fields/open-hours-field.php', array(
					'key'   => 'job_hours',
					'field' => $field
				) ); ?>

			</div>

			<?php
		}
	}

	new Knowhere_WP_Job_Manager_Open_Hours();

}