<?php

if ( is_admin() ) {

	if ( !class_exists('Knowhere_WP_Resume_Manager') ) {
		class Knowhere_WP_Resume_Manager extends WP_Resume_Manager_Writepanels {

			function __construct() {
				add_action( 'add_meta_boxes', array( $this, 'knowhere_resume_meta_box' ) );
				add_action( 'resume_manager_save_resume', array( $this, 'save_resume_data' ), 1, 2 );
			}

			function knowhere_resume_meta_box() {
				add_meta_box( 'resume_awards_data', esc_html__( 'Awards', 'knowherepro' ), array( $this, 'knowhere_meta_box_awards_data' ), 'resume', 'normal', 'high' );
			}

			public function save_resume_data( $post_id, $post ) {
				global $wpdb;

				$save_repeated_fields = array(
					'_candidate_awards'  => $this->resume_awards_fields()
				);

				foreach ( $save_repeated_fields as $meta_key => $fields ) {
					$this->save_repeated_row( $post_id, $meta_key, $fields );
				}
			}

			public static function resume_awards_fields() {
				return apply_filters( 'resume_manager_resume_awards_fields', array(
					'title' => array(
						'label'       => esc_html__( 'Title', 'knowherepro' ),
						'name'        => 'resume_awards_title[]',
						'placeholder' => '',
						'description' => ''
					),
					'description' => array(
						'label'       => esc_html__( 'Description', 'knowherepro' ),
						'name'        => 'resume_awards_description[]',
						'placeholder' => '',
						'description' => '',
						'type'        => 'textarea',
					)
				) );
			}

			function knowhere_meta_box_awards_data( $post ) {
				$fields = $this->resume_awards_fields();
				$this->repeated_rows_html( esc_html__( 'Awards', 'knowherepro' ), $fields, get_post_meta( $post->ID, '_candidate_awards', true ) );
			}

		}
	}

	new Knowhere_WP_Resume_Manager();

}

if ( !function_exists('knowhere_custom_submit_manager_resume_fields') ) {
	function knowhere_custom_submit_manager_resume_fields( $fields ) {

		$fields['_job_salary_wage'] = array(
			'label'       => esc_html__( 'Salary Wage', 'knowherepro' ),
			'type'        => 'text',
			'placeholder' => '45.00',
			'description' => sprintf( '%s', esc_html__( 'Salary wage.', 'knowherepro' ) ),
			'required'    => false,
		);

		$fields['_job_salary_time_period'] = array(
			'label'       => esc_html__( 'Salary Time Period', 'knowherepro' ),
			'type'        => 'select',
			'options' => array(
				'hour' => esc_html__('Hour', 'knowherepro'),
				'year' => esc_html__('Year', 'knowherepro')
			),
			'description' => sprintf( '%s', esc_html__( 'Salary time period.', 'knowherepro' ) ),
			'required'    => false,
		);


		return $fields;

	}
}

add_filter('resume_manager_resume_fields', 'knowhere_custom_submit_manager_resume_fields' );


if ( !function_exists('knowhere_job_salary') ) {
	function knowhere_job_salary( $post ) {

		$job_salary_wage = get_post_meta( $post->ID, '_job_salary_wage', true );
		$job_salary_time_period = get_post_meta( $post->ID, '_job_salary_time_period', true );

		if ( !$job_salary_wage ) return;

		?><li><span class="lnr icon-wallet"></span><?php echo sprintf( '%s/%s', $job_salary_wage, $job_salary_time_period ); ?></li><?php

	}
}

if ( !function_exists('knowhere_job_resume_register_widget_areas') ) {
	function knowhere_job_resume_register_widget_areas() {

		if ( class_exists('WP_Resume_Manager') ) {

			register_sidebar( array(
				'name'          => esc_html__( 'Resume', 'knowherepro' ),
				'description'   => esc_html__( 'Placed below the Sidebar Right, this area brings together all the widgets under the same container.', 'knowherepro' ),
				'id'            => 'resume_sidebar',
				'before_widget' => '<div id="%1$s" class="widget  %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="kw-widget-title">',
				'after_title'   => '</h3>',
			) );

		}

	}
}

add_action( 'widgets_init', 'knowhere_job_resume_register_widget_areas' );

if ( !function_exists('knowhere_job_resume_header_top') ) {
	function knowhere_job_resume_header_top() {

		if ( is_singular('resume') ) {
			get_job_manager_template_part( 'content', 'header', 'wp-job-manager-resumes' );
		}

	}
}

add_action( 'knowhere_header_after', 'knowhere_job_resume_header_top' );

