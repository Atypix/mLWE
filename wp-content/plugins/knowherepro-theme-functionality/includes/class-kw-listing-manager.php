<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class KW_Listing_Manager {

	public function __construct() {

		add_filter( 'submit_job_form_fields', array( $this, 'custom_submit_job_form_fields' ), 9 );
//		add_action( 'job_manager_update_job_data', array( $this, 'job_manager_update_job_data' ), 10, 2 );
//		add_filter( 'submit_job_form_fields_get_job_data', array( $this, 'submit_job_form_fields_get_job_data' ), 9 );

//		add_filter( 'job_manager_chosen_multiselect_args', array( $this, 'job_manager_chosen_multiselect_args') );

		// JM Ajax endpoints
		add_action( 'job_manager_ajax_get_attributes', array( $this, 'get_attributes' ) );
		add_action( 'wp_ajax_nopriv_job_manager_get_attributes', array( $this, 'get_attributes' ) );
		add_action( 'wp_ajax_job_manager_get_attributes', array( $this, 'get_attributes' ) );
//		add_action( 'job_manager_ajax_update_form', array( $this, 'update_form' ) );


		// BW compatible handlers

//		add_action( 'wp_ajax_nopriv_job_manager_update_form', array( $this, 'update_form' ) );
//		add_action( 'wp_ajax_job_manager_update_form', array( $this, 'update_form' ) );

	}

//	function job_manager_chosen_multiselect_args ( $args ) {
//
//
//		return $args;
//	}

	function get_job_ids( $categories ) {

		$query_args = array(
			'post_type'      => 'job_listing',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'tax_query' => array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'job_listing_category',
					'field' => 'id',
					'terms' => $categories
				)
			)
		);

		return new WP_Query( $query_args );
	}

	public function attribute_taxonomy_name_type_by_category( $search_categories ) {
		global $wpdb;

		$attr = $wpdb->get_results( "
			SELECT attribute_name, attribute_type, attribute_category
			FROM {$wpdb->prefix}kw_attribute_taxonomies
			WHERE attribute_name != ''
		");

		if ( $attr && ! is_wp_error( $attr ) ) {

			$taxonomy = array();

				foreach ( $attr as $i => $row ) {

					$categories = explode( ',', $row->attribute_category );

					foreach ( $search_categories as $cat ) {

						if ( in_array( $cat, $categories) ) {
							$taxonomy[ $i ] = new stdClass();
							$taxonomy[ $i ]->name = kw_attribute_taxonomy_name( $row->attribute_name );
							$taxonomy[ $i ]->type = $row->attribute_type;
						}

					}

				}

				if ( isset($taxonomy) && !empty($taxonomy) && is_array($taxonomy) ) {

					foreach ( $taxonomy as $tax ) {

						if ( taxonomy_exists($tax->name) ) {

							$terms = get_terms( array(
								'taxonomy' => $tax->name,
								'object_ids' => $this->get_job_ids( $search_categories )->posts
							) );

							if ( !is_wp_error($terms) && ( is_array($terms) || is_object($terms) ) && !empty($terms) ): ?>
								<?php if ( $tax->type == 'select' ): ?>
									<?php echo $this->layered_nav_dropdown( $terms, $tax->name, true ); ?>
								<?php elseif ( $tax->type == 'checkbox' ): ?>
									<?php echo $this->layered_nav_checkbox( $terms, $tax->name, true ); ?>
								<?php endif; ?>
							<?php endif;

						}

					}

				}

		}

	}

	public function get_attributes() {

		$result = array();
		$search_categories = isset( $_REQUEST['search_categories'] ) ? $_REQUEST['search_categories'] : '';

		if ( is_array( $search_categories ) ) {
			$search_categories = array_filter( array_map( 'sanitize_text_field', array_map( 'stripslashes', $search_categories ) ) );
		} else {
			$search_categories = array_filter( array( sanitize_text_field( stripslashes( $search_categories ) ) ) );
		}

		if ( empty($search_categories) ) return;

		ob_start();

//		if ( is_array($search_categories) ) {
//
//			foreach ( $search_categories as $cat ) {

				$this->attribute_taxonomy_name_type_by_category( $search_categories );
//				kw_attribute_taxonomy_name_type_by_category( $search_categories );

//			}
//
//		}

		$result['html'] = ob_get_clean();

		wp_send_json( $result );

	}

	public function update_form () {

		$result = array();
		$category = isset( $_REQUEST['category'] ) ? array_filter( array_map( 'sanitize_title', (array) $_REQUEST['category'] ) ) : null;
		$category = $category[0];
		if ( !$category ) return;

		$taxonomy = kw_attribute_taxonomy_name_by_category( $category );

		ob_start();

		if ( taxonomy_exists( $taxonomy ) ) {

			$terms = get_terms( array(
				'taxonomy' => $taxonomy
			) );

			if ( !is_wp_error($terms) && ( is_array($terms) || is_object($terms) ) && !empty($terms) ): ?>
				<?php echo $this->layered_nav_dropdown( $terms, $taxonomy, true ); ?>
			<?php endif;

		}

		$result['html'] = ob_get_clean();

		wp_send_json( $result );

	}

	public function custom_submit_job_form_fields( $fields ) {

		$fields['job']['job_category']['type'] = 'term-select';

		$attribute_taxonomies = kw_get_attribute_taxonomies();

		if ( !empty($attribute_taxonomies) ) {

			foreach ( $attribute_taxonomies as $tax ) {

				$taxonomy = kw_attribute_taxonomy_name( $tax->attribute_name );

				if ( taxonomy_exists($taxonomy) ) {

					switch ( $tax->attribute_type ) {
						case 'select': 	 $tax_type = 'term-select'; break;
						case 'checkbox': $tax_type = 'term-multiselect'; break;
						default: 		 $tax_type = 'term-select'; break;
					}

					$fields['job'][$taxonomy] = array(
						'label' => kw_attribute_label( $taxonomy ),
						'type' => $tax_type ? $tax_type : 'term-select',
						'required'    => false,
						'placeholder' => '',
						'priority'    => 5,
						'default'     => '',
						'taxonomy'    => $taxonomy
					);

				}

			}

		}

		return $fields;

	}

	public function layered_nav_dropdown( $terms, $taxonomy, $label = false ) {

		$taxonomy_label = kw_attribute_label( $taxonomy ); ?>

		<div class="search_<?php echo esc_attr($taxonomy) ?>">

			<?php if ( $label ): ?>
				<label for="search_<?php echo esc_attr($taxonomy) ?>"><?php echo esc_html($taxonomy_label) ?></label>
			<?php endif; ?>

			<select name="<?php echo esc_attr($taxonomy) ?>" class="job-manager-nav-dropdown">

				<option value=""><?php echo esc_html(sprintf( __( 'Any %s', 'knowherepro_app_textdomain' ), strtolower($taxonomy_label) )) ?></option>

				<?php foreach ( $terms as $term ): ?>
					<option value="<?php echo esc_attr($term->slug) ?>"><?php echo esc_html($term->name) ?></option>
				<?php endforeach; ?>

			</select>

		</div>

		<?php

	}

	public function layered_nav_checkbox( $terms, $taxonomy, $label = false ) {

		$taxonomy_label = kw_attribute_label( $taxonomy ); ?>

		<div class="search_checkbox">

			<?php if ( $label ): ?>
				<label for="search_<?php echo esc_attr($taxonomy) ?>"><?php echo esc_html($taxonomy_label) ?></label>
			<?php endif; ?>

			<ul class="job-manager-nav-checkbox">
				<?php foreach ( $terms as $term ) : ?>
					<li><label for="search_<?php echo $term->slug; ?>">
							<input type="checkbox" name="<?php echo esc_attr($taxonomy) ?>[]" value="<?php echo esc_attr($term->slug); ?>" id="search_<?php echo esc_attr($term->slug); ?>" /> <?php echo esc_html($term->name); ?></label>
					</li>
				<?php endforeach; ?>
			</ul>

		</div>

		<?php

	}

	public function submit_job_form_fields_get_job_data( $fields, $job ) { return $fields; }
	public function job_manager_update_job_data(  $id, $values ) { }

}

new KW_Listing_Manager();
