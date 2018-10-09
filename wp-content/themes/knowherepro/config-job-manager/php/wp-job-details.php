<?php

if ( !class_exists('Knowhere_Job_Manager_Details_Fields') ) {

	class Knowhere_Job_Manager_Details_Fields {

		function __construct() {
			$this->setup_actions();
		}

		public function setup_actions() {

			if ( !is_admin() ) {

				add_action('submit_job_form_start', array( $this, 'add_tmpl' ) );

			} else {
				add_action('print_media_templates', array( $this, 'add_tmpl' ) );
			}

			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'add_enqueue_scripts_and_styles' ) );
			add_action( 'job_manager_update_job_data', array( $this, 'job_manager_update_job_data' ), 10, 2 );
			add_action( 'job_manager_save_job_listing', array( $this, 'job_manager_update_job_data' ), 10, 2 );
			add_action( 'knowhere_writepanels_details_fields', array( $this, 'output' ) );

			add_filter( 'submit_job_form_fields', array( $this, 'submit_job_form_fields' ) );
			add_filter( 'submit_job_form_fields_get_job_data', array( $this, 'get_job_data' ), 10, 2 );
		}

		public function wp_enqueue_scripts() {
			$this->add_enqueue_scripts();
		}

		public function add_enqueue_scripts_and_styles() {
			$this->add_enqueue_scripts();
			$this->add_enqueue_styles();
		}

		public function add_enqueue_scripts() {
			wp_enqueue_script( 'knowhere-job-details', get_theme_file_uri('config-job-manager/assets/js/wp-job-details.js'), array('jquery-ui-sortable') );
		}

		public function add_enqueue_styles() {
			wp_enqueue_style( 'knowhere-admin-job-details', get_theme_file_uri('config-job-manager/assets/css/admin-job-manager.css') );
		}

		public function add_tmpl() { ?>

			<script type="text/template" id="knowhere-tmpl-details-field">
				<li>
					<div class="job-details-handle-area"></div>
					<div class="item">
						<h3><?php esc_html_e('Title', 'knowherepro'); ?></h3>
						<input type="text" name="knowhere_job_details[__REPLACE_SSS__][title]" value=""/>
						<p class="desc"><?php esc_html_e('Enter a title (required field)', 'knowherepro'); ?></p>
					</div>
					<div class="item wp-editor">
						<h3><?php esc_html_e('Content', 'knowherepro'); ?></h3>
						<?php wp_editor( '', '__REPLACE_SSS__', array(
							'textarea_name' => 'knowhere_job_details[__REPLACE_SSS__][content]',
							'textarea_rows' => 3,
							'quicktags' => true,
							'tinymce' => false
						) ); ?>
					</div>
					<div class="item">
						<a href="javascript:void(0)" class="button button-secondary remove-job-field-tab kw-btn kw-gray kw-small"><?php esc_html_e('Remove', 'knowherepro'); ?></a>
					</div>
				</li>
			</script>

			<?php
		}

		public function submit_job_form_fields( $fields ) {

			$fields['company']['job_details'] = $this->fields();

			return $fields;
		}

		public function fields() {
			$args = array(
				'label'       => esc_html__( 'Details Fields', 'knowherepro' ),
				'type'        => 'details-fields',
				'required'    => false,
				'placeholder' => '',
				'priority'    => 9,
				'default'     => ''
			);
			return $args;
		}

		public function get_job_data( $fields, $job ) {
			$details_fields = get_post_meta( $job->ID, 'knowhere_job_details', true );

			if ( ! $details_fields ) {
				return $fields;
			}

			$fields[ 'company' ][ 'job_details' ][ 'value' ] = $details_fields;

			return $fields;
		}

		public function job_manager_update_job_data( $job_id, $values ) {
			if ( ! isset( $_POST[ 'knowhere_job_details' ] ) ) {
				return;
			}

			update_post_meta( $job_id, 'knowhere_job_details', $_POST[ 'knowhere_job_details' ] );
		}

		public function output($post) {

			$field = $this->fields(); ?>

			<div class="form-field">

				<?php get_job_manager_template( 'form-fields/details-fields-field.php', array(
					'key'   => 'job_details_fields',
					'field' => $field
				) ); ?>

			</div>

			<?php
		}

	}

	new Knowhere_Job_Manager_Details_Fields();

}