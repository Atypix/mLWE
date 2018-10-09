<?php

if ( !class_exists('Knowhere_Job_Manager_Config') ) {

	class Knowhere_Job_Manager_Config {

		public $action_job_application = 'action_job_application';
		protected $distances = array();

		function __construct() {

			$includes = array(
				'class-listing-factory.php',
				'php/wp-job-manager-abstract-listing.php',
				'php/wp-job-manager.php',
				'php/wp-job-details.php',
				'php/wp-job-manager-listing.php',
				'php/wp-job-manager-open-hours.php',
				'php/wp-job-manager-bookmarks.php',
				'php/wp-job-manager-writepanels.php',
				'php/wp-job-post-views/wp-job-post-views.php',
			);

			foreach ( $includes as $file ) {
				require( get_theme_file_path( 'config-job-manager/' . $file ) );
			}

			add_action( 'init', array( $this, 'init' ), 0 );
			add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 12 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );
			add_action( 'admin_enqueue_scripts', array($this, 'admin_enqueue_scripts_and_styles') );

			add_action( 'admin_init', array( $this, 'admin_head' ) );
			add_action( 'widgets_init', array( $this, 'job_manager_register_widget_areas' ) );

			add_filter( 'job_manager_show_addons_page', '__return_false' );
			add_filter( 'body_class', array( $this, 'job_body_class'), 5 );
			add_filter( 'get_job_listings_query_args', array( $this, 'get_job_listings_query_args' ), 10, 2 );
			add_filter( 'get_job_listings_query_args', array( $this, 'apply_proximity_filter' ), 10, 2 );
			add_filter( 'register_post_type_job_listing', array( $this, 'change_job_listing' ) );
			add_filter( 'register_taxonomy_job_listing_type_args', array( $this, 'change_taxonomy_job_listing_type_args' ) );
			add_filter( 'register_taxonomy_job_listing_tag_args', array( $this, 'change_taxonomy_job_listing_tag_args' ) );
			add_filter( 'register_taxonomy_job_listing_category_args', array( $this, 'change_taxonomy_job_listing_category_args' ) );
			add_filter( 'job_manager_job_listing_data_fields', array( $this, 'job_listing_data_fields'), 1 );
			add_filter( 'get_search_query', array( $this, 'search_keywords_query' ) );
			add_filter( 'submit_job_form_fields', array( $this, 'custom_submit_job_form_fields' ) );

			add_filter( 'job_manager_job_listings_output', array( $this, 'output_the_listings' ), 10, 1 );
			add_filter( 'job_manager_output_jobs_defaults', array( $this, 'job_manager_output_jobs_defaults' ) );
			add_filter( 'job_manager_geolocation_endpoint',  array( $this, 'add_google_language_param' ) , 10, 2 );
			add_filter( 'manage_edit-job_listing_columns', array( $this, 'job_listing_admin_columns' ) );
			add_filter( 'job_manager_settings',  array( $this, 'job_manager_settings' ), 998 );
			add_filter( 'job_manager_get_listings_result', array( $this, 'add_total_jobs_response' ), 10, 2  );
			add_filter( 'job_manager_get_listings_custom_filter_text', array( $this, 'job_manager_get_listings_custom_filter_text') );

			add_action( 'job_manager_save_job_listing', array( $this, 'save_job_listing' ), 10, 2 );
			add_action( 'manage_job_listing_posts_custom_column', array( $this, 'job_listing_admin_custom_columns' ), 1 );
			add_action( 'job_manager_job_filters_end', array( $this, 'add_submit_button_and_controls' ), 35 );
			add_action( 'single_job_listing_end', array( $this, 'job_single_actions' ) );
			add_action( 'single_job_listing_end', array( $this, 'job_single_tags' ) );

			if ( function_exists('knowhere_job_single_share') ) {
				add_action( 'single_job_listing_end', 'knowhere_job_single_share');
			}

//			if ( ! ( get_option( 'job_manager_regions_filter', true ) ) ) {
				add_action( 'job_manager_job_filters_search_jobs_end',  'knowhere_job_manager_job_filters_distance', 0 );
//			}

			add_action( 'save_post', array( $this, 'gallery_images_synced' ) );
			add_action( 'save_post', array( $this, 'save_post_type' ), 10, 3);
			add_action( 'job_manager_update_job_data', array( $this, 'update_job_data' ), 10, 2 );

			add_action( 'knowhere_header_after', array( $this, 'template_header_style_hook' ) );
			add_action( 'knowhere_page_content_prepend', array( $this, 'page_header_style_2' ) );

			add_action( 'created_term', array( $this, 'save_job_listing_type_custom_meta'), 10, 3 );
			add_action( 'edit_term', array( $this, 'save_job_listing_type_custom_meta'), 10, 3 );
			add_action( 'job_listing_type_add_form_fields', array( $this, 'taxonomy_add_new_meta_field' ), 10, 2 );
			add_action( 'job_listing_type_edit_form_fields', array( $this, 'taxonomy_edit_meta_field' ), 10, 2 );

		}

		public static function geolocation_search( $args = array() ) {
			global $wpdb;

			$defaults = array(
				'earth_radius' => knowhere_results_map_unit()[0],
				'orderby' => array(),
				'latitude' => null,
				'longitude' => null,
				'radius' => null,
			);

			$args = wp_parse_args( $args, $defaults );

			$args['orderby'][] = 'distance ASC';

			$sql = $wpdb->prepare( "
			SELECT $wpdb->posts.ID,
				( %s * acos(
					cos( radians(%s) ) *
					cos( radians( latitude.meta_value ) ) *
					cos( radians( longitude.meta_value ) - radians(%s) ) +
					sin( radians(%s) ) *
					sin( radians( latitude.meta_value ) )
				) )
				AS distance, latitude.meta_value AS latitude, longitude.meta_value AS longitude
				FROM $wpdb->posts
				INNER JOIN $wpdb->postmeta
					AS latitude
					ON $wpdb->posts.ID = latitude.post_id
				INNER JOIN $wpdb->postmeta
					AS longitude
					ON $wpdb->posts.ID = longitude.post_id
				WHERE 1=1
					AND ($wpdb->posts.post_status = 'publish' )
					AND latitude.meta_key='geolocation_lat'
					AND longitude.meta_key='geolocation_long'
				HAVING distance < %s
				ORDER BY " . implode( ',', $args['orderby'] ),
				$args['earth_radius'],
				$args['latitude'],
				$args['longitude'],
				$args['latitude'],
				$args['radius']
			);

			return $wpdb->get_results( $sql, OBJECT_K );
		}

		function apply_proximity_filter( $query_args, $args = array() ) {
			$params = array();

			if ( ! isset( $_REQUEST['form_data'] ) ) {
				return $query_args;
			}

			global $wpdb, $wp_query;

			parse_str( $_REQUEST['form_data'], $params );

			$use_radius = isset( $params['use_search_radius'] ) && '1' == $params['use_search_radius'];
			$lat = isset( $params['search_lat'] ) ? (float) $params['search_lat'] : false;
			$lng = isset( $params['search_lng'] ) ? (float) $params['search_lng'] : false;
			$radius = isset( $params['search_radius'] ) ? (int) $params['search_radius'] : false;

			if ( ! ( $use_radius && $lat && $lng && $radius ) ) {
				return $query_args;
			}

			if ( is_tax( 'job_listing_region' ) ) {
				return $query_args;
			}

			$post_ids = self::geolocation_search( array(
				'latitude' => $lat,
				'longitude' => $lng,
				'radius' => $radius,
			) );

			if ( empty( $post_ids ) || ! $post_ids ) {
				$post_ids = array( 0 );
			}

			$this->distances = $post_ids;
			$query_args['post__in'] = array_keys( (array) $post_ids );
			$query_args['orderby'] = 'post__in';

			$query_args = $this->remove_location_meta_query( $query_args );

			return $query_args;
		}

		private function remove_location_meta_query( $query_args ) {
			$found = false;

			if ( ! isset( $query_args['meta_query'] ) ) {
				return $query_args;
			}

			foreach ( $query_args['meta_query'] as $query_key => $meta ) {
				foreach ( $meta as $key => $args ) {
					if ( ! is_int( $key ) ) {
						continue;
					}

					if ( 'geolocation_formatted_address' == $args['key'] ) {
						$found = true;
						unset( $query_args['meta_query'][ $query_key ] );
						break;
					}
				}

				if ( $found ) {
					break;
				}
			}

			return $query_args;
		}

		function get_job_listings_query_args( $query_args, $args = array() ) {

			global $knowhere_settings;

			if ( isset( $_GET['user_id'] ) && !empty($_GET['user_id']) ) {
				$query_args['author'] = intval($_GET['user_id']);
				return $query_args;
			}

			if ( ! isset( $_REQUEST['form_data'] ) ) {
				return $query_args;
			}

			parse_str( $_REQUEST['form_data'], $request );

			$beds_baths_search = $knowhere_settings['beds_baths_search'];

			$search_criteria = '=';
			if ( $beds_baths_search == 'greater' ) {
				$search_criteria = '>=';
			}

			// Bedrooms Logic

			if ( isset( $request['search_bedrooms'] ) && !empty($request['search_bedrooms']) ) {

				$query_args['meta_query'][] = array(
					'key'     => '_bedrooms',
					'value'   => absint($request['search_bedrooms']),
					'type'    => 'NUMERIC',
					'compare' => $search_criteria
				);

			}

			// Bathrooms Logic

			if ( isset( $request['search_bathrooms'] ) && !empty($request['search_bathrooms']) ) {

				$query_args['meta_query'][] = array(
					'key'     => '_bathrooms',
					'value'   => absint($request['search_bathrooms']),
					'type'    => 'NUMERIC',
					'compare' => $search_criteria
				);

			}

			if ( isset( $request[ 'search_region' ] ) && 0 != $request[ 'search_region' ] ) {
				$region = $request[ 'search_region' ];

				if ( is_int( $region ) ) {
					$region = array( $region );
				}

				$query_args[ 'tax_query' ][] = array(
					'taxonomy' => 'job_listing_region',
					'field'    => 'id',
					'terms'    => $region,
					'type'    => 'NUMERIC'
				);

			}

			// Features

			if ( isset( $request['search_feature'] ) && !empty($request['search_feature']) ) {

				if ( is_array($request['search_feature']) ) {

					$features = $request['search_feature'];

					foreach ( $features as $feature ):
						$query_args['tax_query'][] = array(
							'taxonomy' => 'job_listing_tag',
							'field' => 'slug',
							'terms' => $feature
						);
					endforeach;

				}

			}

			// Attribute Tanominies

			if ( function_exists('kw_get_attribute_taxonomies') ) {

				$attribute_taxonomies = kw_get_attribute_taxonomies();

				if ( ! empty( $attribute_taxonomies ) ) {

					foreach ( $attribute_taxonomies as $tax ) {

						$taxonomy = kw_attribute_taxonomy_name( $tax->attribute_name );

						if ( taxonomy_exists( $taxonomy ) ) {

							if ( isset( $request[$taxonomy] ) && !empty($request[$taxonomy]) ) {
								$query_args['tax_query'][] = array(
									'taxonomy' => $taxonomy,
									'field' => 'slug',
									'terms' => $request[$taxonomy]
								);
							}

						}

					}

				}

			}

			// Sort Logic

			if ( isset( $request['search_sort'] ) ) {
				if ( '' === $request['search_sort'] ) {

					$query_args['orderby'] = array(
						'menu_order' => 'ASC',
						'date'       => 'DESC',
					);

					$query_args['order'] = 'DESC';

				} else {

					$query_args = knowhere_sort_listings_query( $query_args, $request['search_sort'] );

				}
			}

			// Min and Max Price Logic

			if ( $knowhere_settings['job-type-fields'] == 'property' ) {

				if ( isset($request['search_min_price']) && !empty($request['search_min_price']) && isset($request['search_max_price']) && !empty($request['search_max_price']) ) {

					$min_price = doubleval(knowhere_cleaner($request['search_min_price']));
					$max_price = doubleval(knowhere_cleaner($request['search_max_price']));

					if ( $min_price >= 0 && $max_price > $min_price ) {
						$query_args['meta_query'][] = array(
							'key' => '_prop_price',
							'value' => array( $min_price, $max_price ),
							'type' => 'NUMERIC',
							'compare' => 'BETWEEN'
						);
					}

				} elseif ( isset($request['search_min_price']) && !empty($request['search_min_price']) ) {

					$min_price = doubleval(knowhere_cleaner($request['search_min_price']));

					if ( $min_price >= 0 ) {
						$query_args['meta_query'][] = array(
							'key' => '_prop_price',
							'value' => $min_price,
							'type' => 'NUMERIC',
							'compare' => '>='
						);
					}

				} elseif ( isset($request['search_max_price']) && !empty($request['search_max_price']) ) {

					$max_price = doubleval(knowhere_cleaner($request['search_max_price']));

					if ( $max_price >= 0 ) {
						$query_args['meta_query'][] = array(
							'key' => '_prop_price',
							'value' => $max_price,
							'type' => 'NUMERIC',
							'compare' => '<='
						);
					}

				}


			} else {

				if ( isset($request['search_min_price']) && !empty($request['search_min_price']) && isset($request['search_max_price']) && !empty($request['search_max_price']) ) {

					$min_price = doubleval(knowhere_cleaner($request['search_min_price']));
					$max_price = doubleval(knowhere_cleaner($request['search_max_price']));

					if ( $min_price > 0 && $max_price > $min_price ) {

						$query_args['meta_query'][] = array(
							'key' => '_job_price_range_min',
							'value' => $min_price,
							'type' => 'NUMERIC',
							'compare' => '>='
						);

						$query_args['meta_query'][] = array(
							'key' => '_job_price_range_max',
							'value' => $max_price,
							'type' => 'NUMERIC',
							'compare' => '<='
						);

					}

				} elseif ( isset($request['search_min_price']) && !empty($request['search_min_price']) ) {

					$min_price = doubleval(knowhere_cleaner($request['search_min_price']));

					if ( $min_price >= 0 ) {
						$query_args['meta_query'][] = array(
							'key' => '_job_price_range_min',
							'value' => $min_price,
							'type' => 'NUMERIC',
							'compare' => '>='
						);
					}

				} elseif ( isset($request['search_max_price']) && !empty($request['search_max_price']) ) {

					$max_price = doubleval(knowhere_cleaner($request['search_max_price']));

					if ( $max_price >= 0 ) {
						$query_args['meta_query'][] = array(
							'key' => '_job_price_range_max',
							'value' => $max_price,
							'type' => 'NUMERIC',
							'compare' => '<='
						);
					}

				}

			}

			return $query_args;

		}

		function save_job_listing_type_custom_meta( $term_id, $tt_id, $taxonomy ) {

			if ( !$term_id ) return;

			if ( isset( $_POST['knowhere_job_listing_term_meta'] ) ) {
				$t_id = $term_id;
				$term_meta = get_option( "taxonomy_$t_id" );
				$cat_keys = array_keys( $_POST['knowhere_job_listing_term_meta'] );
				foreach ( $cat_keys as $key ) {
					if ( isset ( $_POST['knowhere_job_listing_term_meta'][$key] ) ) {
						$term_meta[$key] = $_POST['knowhere_job_listing_term_meta'][$key];
					}
				}
				update_option( "taxonomy_$t_id", $term_meta );
			}

		}

		function job_manager_register_widget_areas() {

			global $knowhere_settings;

			$widgets = array(
				'job-listing-map.php',
				'job-listing-search.php',
				'job-listing-related-listings.php',
				'job-button-claim-listing.php',
				'job-listing-open-hours.php',
				'job-listing-info-employer.php',
				'job-contact-form.php'
			);

			if ( $knowhere_settings['job-type-fields'] == 'property' ) {

				$widgets[] = 'job-listing-agent.php';

				register_sidebar(array(
					'name' => esc_html__('Agent', 'knowherepro'),
					'id' => 'agent_sidebar',
					'description' => esc_html__('Widgets in this area will be shown in agents template and agent single page.', 'knowherepro'),
					'before_widget' => '<div id="%1$s" class="widget  %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<h3 class="kw-widget-title">',
					'after_title'   => '</h3>',
				));

				register_sidebar(array(
					'name' => esc_html__('Agency', 'knowherepro'),
					'id' => 'agency_sidebar',
					'description' => esc_html__('Widgets in this area will be shown in agency template and agency single page.', 'knowherepro'),
					'before_widget' => '<div id="%1$s" class="widget  %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<h3 class="kw-widget-title">',
					'after_title'   => '</h3>',
				));

			}

			foreach ( $widgets as $widget ) {
				include_once( get_theme_file_path( 'config-job-manager/widgets/widget-' . $widget ) );
			}

			register_widget( 'Knowhere_Sidebar_Listing_Search_Widget' );
			register_widget( 'Knowhere_Sidebar_Listing_Map_Widget' );
			register_widget( 'Knowhere_Widget_Listing_Related_Listings' );
			register_widget( 'Knowhere_Sidebar_Listing_Open_Hours' );

			if ( $knowhere_settings['job-type-fields'] == 'property' ) {
				register_widget( 'Knowhere_Sidebar_Listing_Agent' );
			}

			register_widget( 'Knowhere_Sidebar_Listing_Employer_Widget' );

			register_widget( 'Knowhere_Widget_Listing_Contact_Form' );

			if ( defined( 'WPJMCL_VERSION' ) ) {
				register_widget( 'Knowhere_Widget_Button_Claim_Listing' );
			}

			register_sidebar( array(
				'name'          => esc_html__( 'Listing Archive', 'knowherepro' ),
				'description'   => esc_html__( 'For listing archive pages, taxonomies.', 'knowherepro' ),
				'id'            => 'listing_sidebar',
				'before_widget' => '<div id="%1$s" class="widget  %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="kw-widget-title">',
				'after_title'   => '</h3>',
			) );

			register_sidebar( array(
				'name'          => esc_html__( 'Listing Detail Page', 'knowherepro' ),
				'description'   => esc_html__( 'For listing Detail Page.', 'knowherepro' ),
				'id'            => 'listing_sidebar_single',
				'before_widget' => '<div id="%1$s" class="widget  %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="kw-widget-title">',
				'after_title'   => '</h3>',
			) );

		}

		public function taxonomy_add_new_meta_field() {
			?>
			<div class="form-field">
				<label for="knowhere_job_listing_term_meta[bg_color_label]"><?php esc_html_e( 'Label Background Color', 'knowherepro' ); ?></label>
				<input type="text" name="knowhere_job_listing_term_meta[bg_color_label]" id="knowhere_job_listing_term_meta[bg_color_label]" value="">
				<p class="description"><?php _e( 'Choose a color for label','knowherepro' ); ?></p>
			</div>
			<?php
		}

		function taxonomy_edit_meta_field($term) {

			$t_id = $term->term_id;
			$term_meta = get_option( "taxonomy_$t_id" );

			?>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="knowhere_job_listing_term_meta[bg_color_label]"><?php esc_html_e( 'Label Background Color', 'knowherepro' ); ?></label>
				</th>
				<td>
					<input class="knowhere-term-meta-bg-color-picker" value="<?php echo esc_attr( $term_meta['bg_color_label'] ) ? esc_attr( $term_meta['bg_color_label'] ) : ''; ?>" type="text" name="knowhere_job_listing_term_meta[bg_color_label]" />
					<p class="description"><?php _e( 'Choose a color for label','knowherepro' ); ?></p>
				</td>
			</tr>
			<?php
		}

		public static function get_listing_ribbon_slider() {

			if ( knowhere_is_realy_job_manager_single() || knowhere_is_realy_job_manager_submit_job_form() ) {

				$photos = knowhere_get_listing_gallery_ids();

				if ( $photos ) : ?>

					<div class="kw-ribbon-slider owl-carousel">

						<?php foreach ( $photos as $key => $photo_id ):
							$src = wp_get_attachment_image_src( $photo_id, 'knowhere-ribbon-image' ); ?>
							<img src="<?php echo esc_url($src[0]); ?>" alt="" />
						<?php endforeach; ?>

					</div><!--/ .kw-ribbon-slider-->

				<?php endif;

			}

		}

		public function template_header_style_hook( $style_page_header ) {

			if ( knowhere_is_realy_job_manager_single() || knowhere_is_realy_job_manager_submit_job_form() ) {
				get_template_part( 'job_manager/job-single-header' , $style_page_header );
			} elseif ( is_singular('knowhere_agent') ) {
				get_template_part( 'job_manager/job-agent' );
			} elseif ( is_singular('knowhere_agency') ) {
				get_template_part( 'job_manager/job-agency' );
			}
		}

		public function page_header_style_2() {
			if ( knowhere_is_realy_job_manager_single() || knowhere_is_realy_job_manager_submit_job_form() ) {
				get_template_part( 'job_manager/job-single-header-style-2' );
			}
		}

		function job_body_class($classes) {
			global $post, $knowhere_settings;

			if ( is_singular('job_listing') || knowhere_is_realy_job_manager_submit_job_form() ) {

				$style = get_post_meta( get_the_ID(), 'knowhere_job_style_single_page', true );

				if ( empty($style) || $style == ' ' ) {
					$style = $knowhere_settings['job-single-style'];
				}

				if ( $style ) {
					$classes[] = $style;
				}

			}

			if ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'jobs' )
				|| ( isset( $post->post_content ) && is_archive() && 'job_listing' == $post->post_type )
				|| is_tax( array( 'job_listing_category', 'job_listing_tag', 'job_listing_region', 'job_listing_type' ) )
				|| ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'submit_job_form' ) )
			) {
				$classes[] = 'kw-page-listings';
				$show_map = knowhere_listings_page_shortcode_get_show_map_param();
				if( !$show_map ) {
					$classes[] = 'kw-has-no-listings';
				}

				$style = $knowhere_settings['job-listings-style'];

				$term_style = knowhere_job_get_term('pix_term_style');
				if ( !empty($term_style) ) {
					$style = $term_style;
				}

				$filter_position = $knowhere_settings['job-filter-style-position'];
				$filter_position_left_extend = $knowhere_settings['job-filter-left-position-extend'];

				$term_filter_position = knowhere_job_get_term('pix_term_filter_position');
				if ( !empty($term_filter_position) ) {
					$filter_position = $term_filter_position;
				}

				if ( $filter_position == 'kw-left-position' ) {
					if ( $filter_position_left_extend ) {
						$classes[] = 'kw-body-left-position-extend';
					}
				}

				$classes[] = str_replace('kw-', 'kw-body-', $filter_position);
				$classes[] = str_replace('kw-type', 'kw-listings-type', $style);

			}

			if ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'job_dashboard' ) ) {
				$classes[] = 'kw-page-job-dashboard';
			}

			if ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'my_bookmarks' ) ) {
				$classes[] = 'kw-page-my-bookmarks';
			}

			if ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'woocommerce_my_account' ) ) {
				$classes[] = 'kw-page-login';
			}

			if ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'submit_job_form' ) ) {
				$classes[] = 'kw-page-add-listing';
			}

			if ( knowhere_using_facetwp() ) {
				$classes[] = 'kw-using-facetwp';
			}

			return $classes;
		}

		function admin_enqueue_scripts_and_styles() {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );

			global $wp_scripts;

			$screen = get_current_screen();

			if ( in_array( $screen->id, apply_filters( 'job_manager_admin_screen_ids', array( 'edit-job_listing', 'plugins', 'job_listing', 'job_listing_page_job-manager-settings', 'job_listing_page_job-manager-addons' ) ) ) ) {

				global $knowhere_settings;
				$google_maps_key = $knowhere_settings['gmap-api'];

				if ( ! empty( $google_maps_key ) ) {
					$google_maps_key = '&amp;key=' . $google_maps_key;
				} else {
					$google_maps_key = '';
				}

				wp_enqueue_script( 'google-maps', '//maps.google.com/maps/api/js?v=3.exp&amp;libraries=places' . $google_maps_key, array(), null, true );
			}

		}

		function enqueue_styles_scripts() {

			global $knowhere_settings;
			$ajax_url = WP_Job_Manager_Ajax::get_endpoint();

			ob_start();
			get_job_manager_template( 'form-fields/uploaded-file-html.php', array(
				'name' => '',
				'value' => '',
				'extension' => 'jpg',
			) );
			$js_field_html_img = ob_get_clean();

			$pintpoint_event = $knowhere_settings['job-pintpoint-event'];

			$min_price = $knowhere_settings['min_price'];
			$max_price = $knowhere_settings['max_price'];

			if ( isset( $_GET['search_min_price'] ) && !empty($_GET['search_min_price']) ) {
				$min_price = doubleval(knowhere_cleaner($_GET['search_min_price']));
			}

			if ( isset( $_GET['search_max_price'] ) && !empty($_GET['search_max_price']) ) {
				$max_price = doubleval(knowhere_cleaner($_GET['search_max_price']));
			}

			if ( empty($min_price) ) { $min_price = '0'; }
			if ( empty($max_price) ) { $max_price = '2100000'; }

			$currency_symbol = $knowhere_settings['currency_symbol'];
			$thousands_separator = $knowhere_settings['thousands_separator'];
			$min_radius = $knowhere_settings['min_radius'];
			$max_radius = $knowhere_settings['max_radius'];
			$default_radius = $knowhere_settings['default_radius'];

			wp_enqueue_script( 'knowhere-job-manager-mod', get_theme_file_uri( 'config-job-manager/assets/js/wp-job-manager'. ( WP_DEBUG ? '' : '.min' ) .'.js' ), array('jquery', 'jquery-ui-slider') );

			wp_localize_script('knowhere-job-manager-mod', 'knowhere_job_manager_localize', array(
				'ajax_url' => $ajax_url,
				'ajaxurl' => admin_url('admin-ajax.php'),
				'js_field_html_img' => $js_field_html_img,
				'strings' => array(
					'results' => esc_html__( 'results found', 'knowherepro'),
					'wp-job-manager-file-upload' => esc_html__( 'Add Image', 'knowherepro'),
				),
				'pintpoint_event' => $pintpoint_event,
				'min_price' => $min_price,
				'max_price' => $max_price,
				'currency_symbol' => $currency_symbol,
				'thousands_separator' => $thousands_separator,
				'min_radius' => $min_radius,
				'max_radius' => $max_radius,
				'default_radius' => $default_radius,
				'i18n' => array(
					'noResults' => esc_html__( 'No Results. Try revising your search keyword!', 'knowherepro' ),
					'resultsFound' => esc_html__( '%d results found', 'knowherepro' ),
					'show_more' => esc_html__( 'Show More', 'knowherepro' ),
					'show_less' => esc_html__( 'Show Less', 'knowherepro' ),
				),
			));

		}

//		function add_meta_boxes() {
//			remove_meta_box( 'job_listing_statusdiv', 'job_listing', 'side');
//			$job_listing_status = get_taxonomy( 'job_listing_status' );
//			add_meta_box( 'job_listing_status', $job_listing_status->labels->menu_name, array( $this, 'job_listing_metabox' ),'job_listing' ,'side','core');
//		}

		/**
		 * Displays job listing metabox.
		 *
		 * @param int|WP_Post $post
		 */
		public function job_listing_metabox( $post ) {
			// Set up the taxonomy object and get terms
			$taxonomy = 'job_listing_status';
			$tax = get_taxonomy( $taxonomy );// This is the taxonomy object

			// The name of the form
			$name = 'tax_input[' . $taxonomy . ']';

			// Get all the terms for this taxonomy
			$terms = get_terms( $taxonomy, array( 'hide_empty' => 0 ) );
			$postterms = get_the_terms( $post->ID, $taxonomy );
			$current = ( $postterms ? array_pop( $postterms ) : false );
			$current = ( $current ? $current->term_id : 0 );
			// Get current and popular terms
			$popular = get_terms( $taxonomy, array( 'orderby' => 'count', 'order' => 'DESC', 'number' => 10, 'hierarchical' => false ) );
			$postterms = get_the_terms( $post->ID,$taxonomy );
			$current = ($postterms ? array_pop($postterms) : false);
			$current = ($current ? $current->term_id : 0);
			?>

			<div id="taxonomy-<?php echo esc_attr($taxonomy); ?>" class="categorydiv">

				<!-- Display tabs-->
				<ul id="<?php echo esc_attr($taxonomy); ?>-tabs" class="category-tabs">
					<li class="tabs"><a href="#<?php echo esc_attr($taxonomy); ?>-all" tabindex="3"><?php echo esc_html($tax->labels->all_items); ?></a></li>
					<li class="hide-if-no-js"><a href="#<?php echo esc_attr($taxonomy); ?>-pop" tabindex="3"><?php esc_html_e( 'Most Used', 'knowherepro' ); ?></a></li>
				</ul>

				<!-- Display taxonomy terms -->
				<div id="<?php echo esc_attr($taxonomy); ?>-all" class="tabs-panel">
					<ul id="<?php echo esc_attr($taxonomy); ?>checklist" class="list:<?php echo esc_attr($taxonomy)?> categorychecklist form-no-clear">
						<?php   foreach($terms as $term){
							$id = $taxonomy.'-'.$term->term_id;
							echo "<li id='$id'><label class='selectit'>";
							echo "<input type='radio' id='in-$id' name='{$name}'".checked($current,$term->term_id,false)."value='$term->term_id' />$term->name<br />";
							echo "</label></li>";
						}?>
					</ul>
				</div>

				<!-- Display popular taxonomy terms -->
				<div id="<?php echo esc_attr($taxonomy); ?>-pop" class="tabs-panel" style="display: none;">
					<ul id="<?php echo esc_attr($taxonomy); ?>checklist-pop" class="categorychecklist form-no-clear" >
						<?php   foreach($popular as $term){
							$id = 'popular-'.$taxonomy.'-'.$term->term_id;
							echo "<li id='$id'><label class='selectit'>";
							echo "<input type='radio' id='in-$id'".checked($current,$term->term_id,false)."value='$term->term_id' />$term->name<br />";
							echo "</label></li>";
						}?>
					</ul>
				</div>

			</div>
			<?php
		}

		function include_template_functions() {
			remove_action( 'single_job_listing_start', 'job_listing_meta_display', 20 );
			remove_action( 'single_job_listing_start', 'job_listing_company_display', 30 );
		}

		function save_post_type( $post_id, $post, $update ) {

			if ( !is_object($post) || !isset($post->post_type) ) {
				return;
			}

			$slug = 'job_listing';
			if ( $slug != $post->post_type ) {
				return;
			}

		}

		function gallery_images_synced( $post_id ) {
			if ( ! current_user_can( 'edit_post', $post_id ) ) return;

			if ( ! empty( $_POST['mad_perm_metadata'] ) ) {
				$array = explode( ',', $_POST['mad_perm_metadata'] );

				foreach ( $array as $key => $id ) {
					$src = wp_get_attachment_image_src( $id );
					if ( ! is_wp_error( $src ) && ! empty( $src[0] ) ) {
						$array[$key] = $src[0];
					} else {
						unset( $array[$key] );
					}
				}

				update_post_meta( $post_id, '_mad_perm_metadata', $array );
			}
		}

		function update_job_data( $id, $values ) {

			$images = '';

			if ( isset($values['company']['mad_perm_metadata']) && !empty($values['company']['mad_perm_metadata']) ) {
				$images = $values['company']['mad_perm_metadata'];
			}

			if ( isset( $images ) && ! empty( $images ) ) {

				$image_string = ''; $image_array = array();

				if ( is_numeric( $images ) ) {
					$attach_id = knowhere_get_attachment_id_from_url( $images );
					if ( ! empty( $attach_id ) && is_numeric( $attach_id ) ) {
						$image_string = $attach_id;
						$image_array = $images;
					}
				} elseif ( is_array( $images ) && ! empty( $images ) ) {

					foreach ( $images as $key => $url ) {
						$attach_id = knowhere_get_attachment_id_from_url( $url );
						if ( ! empty( $attach_id ) && is_numeric( $attach_id ) ) {
							$image_string .= $attach_id;

							if ( $key < count( $images ) ) {
								$image_string .= ',';
							}

							$image_array[] = $images;
						}
					}
				}

				update_post_meta( $id, 'mad_perm_metadata', $image_string );
				update_post_meta( $id, '_mad_perm_metadata', $image_array );
			}

			if ( isset( $_POST['job_hours'] ) && !empty($_POST['job_hours']) ) {
				update_post_meta( $id, '_job_hours', knowhere_sanitize_hours( $_POST['job_hours'] ) );
			}

			if ( isset($values['company']['company_description']) && !empty($values['company']['company_description']) ) {

				update_post_meta( $id, '_company_description', $values['company']['company_description'] );

				if ( is_user_logged_in() ) {
					update_user_meta( get_current_user_id(), '_company_description', $values['company']['company_description'] );
				}

			}

		}

		function job_single_actions() {

			global $knowhere_settings, $job_manager_bookmarks; ?>

			<?php if ( $knowhere_settings['job-type-fields'] == 'job' ): ?>

				<ul class="kw-listing-item-bottom-actions">

					<?php if ( candidates_can_apply() ) : ?>
						<li><?php get_job_manager_template( 'job-application.php' ); ?></li>
					<?php endif; ?>

					<?php if ( $job_manager_bookmarks !== null && method_exists( $job_manager_bookmarks, 'bookmark_form' ) ) : ?>
						<li><?php echo $job_manager_bookmarks->bookmark_form(); ?></li>
					<?php endif; ?>

					<li><span class="lnr icon-printer"></span><a href="javascript:window.print()"><?php esc_html_e('Print', 'knowherepro') ?></a></li>

				</ul>

			<?php endif;

		}

		function job_single_tags() {
			if ( !class_exists('WP_Job_Manager_Job_Tags') ) return;

			$tags = get_the_term_list( get_the_ID(), 'job_listing_tag' );

			if ( $tags ) {

				global $knowhere_settings;

				$text = esc_html__( 'Tags', 'knowherepro' );

				if ( $knowhere_settings['job-type-fields'] == 'property' ) {
					$text = esc_html__( 'Features', 'knowherepro' );
				}

				echo '<div class="kw-entry-tags"><span class="screen-reader-text">' . $text . ':</span>' . $tags . '</div>';
			}
		}

		function init() {

			$this->writepanels = new Knowhere_WP_Job_Manager_Writepanels();

		}

		function change_job_listing( $args ) {

			global $knowhere_settings;

			$singular = $knowhere_settings['name-of-listing-singular'];
			$plural   = $knowhere_settings['name-of-listing-plural'];

			$args['labels']      = array(
				'name'               => $plural,
				'singular_name'      => $singular,
				'menu_name'          => $plural,
				'all_items'          => sprintf( esc_html__( 'All %s', 'knowherepro' ), $plural ),
				'add_new'            => esc_html__( 'Add New', 'knowherepro' ),
				'add_new_item'       => sprintf( esc_html__( 'Add %s', 'knowherepro' ), $singular ),
				'edit'               => esc_html__( 'Edit', 'knowherepro' ),
				'edit_item'          => sprintf( esc_html__( 'Edit %s', 'knowherepro' ), $singular ),
				'new_item'           => sprintf( esc_html__( 'New %s', 'knowherepro' ), $singular ),
				'view'               => sprintf( esc_html__( 'View %s', 'knowherepro' ), $singular ),
				'view_item'          => sprintf( esc_html__( 'View %s', 'knowherepro' ), $singular ),
				'search_items'       => sprintf( esc_html__( 'Search %s', 'knowherepro' ), $plural ),
				'not_found'          => sprintf( esc_html__( 'No %s found', 'knowherepro' ), $plural ),
				'not_found_in_trash' => sprintf( esc_html__( 'No %s found in trash', 'knowherepro' ), $plural ),
				'parent'             => sprintf( esc_html__( 'Parent %s', 'knowherepro' ), $singular ),
//				'featured_image'        => __( 'Company Logo', 'knowherepro' ),
//				'set_featured_image'    => __( 'Set company logo', 'knowherepro' ),
//				'remove_featured_image' => __( 'Remove company logo', 'knowherepro' ),
//				'use_featured_image'    => __( 'Use as company logo', 'knowherepro' ),
			);
			$args['description'] = sprintf( esc_html__( 'This is where you can create and manage %s.', 'knowherepro' ), $plural );
			$args['supports']    = array( 'title', 'editor', 'custom-fields', 'publicize', 'comments', 'thumbnail' );
			$args['rewrite']     = array( 'slug' => 'listings' );
			$args['show_in_nav_menus'] = true;

			$permalinks = get_option( 'knowhere_permalinks_settings' );
			if ( isset( $permalinks['listing_base'] ) && ! empty( $permalinks['listing_base'] ) ) {
				$args['rewrite']['slug'] = $permalinks['listing_base'];
			}

			return $args;
		}

		function change_taxonomy_job_listing_tag_args( $args ) {

			global $knowhere_settings;

			$singular = $knowhere_settings['name-of-listing-singular'] . ' ' . esc_html__( 'Tag', 'knowherepro' );
			$plural   = $knowhere_settings['name-of-listing-plural'] . ' ' . esc_html__( 'Tags', 'knowherepro' );

			$args['label']  = $plural;
			$args['labels'] = array(
				'name' 				=> $plural,
				'singular_name' 	=> $singular,
				'search_items' 		=> sprintf( __( 'Search %s', 'knowherepro' ), $plural ),
				'all_items' 		=> sprintf( __( 'All %s', 'knowherepro' ), $plural ),
				'parent_item' 		=> sprintf( __( 'Parent %s', 'knowherepro' ), $singular ),
				'parent_item_colon' => sprintf( __( 'Parent %s:', 'knowherepro' ), $singular ),
				'edit_item' 		=> sprintf( __( 'Edit %s', 'knowherepro' ), $singular ),
				'update_item' 		=> sprintf( __( 'Update %s', 'knowherepro' ), $singular ),
				'add_new_item' 		=> sprintf( __( 'Add New %s', 'knowherepro' ), $singular ),
				'new_item_name' 	=> sprintf( __( 'New %s Name', 'knowherepro' ),  $singular )
			);

		}

		function change_taxonomy_job_listing_type_args( $args ) {

			global $knowhere_settings;

			$singular = $knowhere_settings['name-of-listing-singular'] . ' ' . esc_html__( 'Type', 'knowherepro' );
			$plural   = $knowhere_settings['name-of-listing-plural'] . ' ' . esc_html__( 'Types', 'knowherepro' );

			$args['label']  = $plural;
			$args['labels'] = array(
				'name'              => $plural,
				'singular_name'     => $singular,
				'menu_name'         => esc_html__( 'Types', 'knowherepro' ),
				'search_items'      => sprintf( esc_html__( 'Search %s', 'knowherepro' ), $plural ),
				'all_items'         => sprintf( esc_html__( 'All %s', 'knowherepro' ), $plural ),
				'parent_item'       => sprintf( esc_html__( 'Parent %s', 'knowherepro' ), $singular ),
				'parent_item_colon' => sprintf( esc_html__( 'Parent %s:', 'knowherepro' ), $singular ),
				'edit_item'         => sprintf( esc_html__( 'Edit %s', 'knowherepro' ), $singular ),
				'update_item'       => sprintf( esc_html__( 'Update %s', 'knowherepro' ), $singular ),
				'add_new_item'      => sprintf( esc_html__( 'Add New %s', 'knowherepro' ), $singular ),
				'new_item_name'     => sprintf( esc_html__( 'New %s Name', 'knowherepro' ), $singular )
			);

			if ( isset( $args['rewrite'] ) && is_array( $args['rewrite'] ) ) {
				$args['rewrite']['slug'] = _x( 'listing-type', 'Listing type slug - resave permalinks after changing this', 'knowherepro' );
			}

			return $args;
		}

		function change_taxonomy_job_listing_category_args( $args ) {

			global $knowhere_settings;

			$singular = $knowhere_settings['name-of-listing-singular'] . ' ' . esc_html__( 'Category', 'knowherepro' );
			$plural   = $knowhere_settings['name-of-listing-plural'] . ' ' . esc_html__( 'Categories', 'knowherepro' );

			$args['label'] = $plural;

			$args['labels'] = array(
				'name'              => $plural,
				'singular_name'     => $singular,
				'menu_name'         => esc_html__( 'Categories', 'knowherepro' ),
				'search_items'      => sprintf( esc_html__( 'Search %s', 'knowherepro' ), $plural ),
				'all_items'         => sprintf( esc_html__( 'All %s', 'knowherepro' ), $plural ),
				'parent_item'       => sprintf( esc_html__( 'Parent %s', 'knowherepro' ), $singular ),
				'parent_item_colon' => sprintf( esc_html__( 'Parent %s:', 'knowherepro' ), $singular ),
				'edit_item'         => sprintf( esc_html__( 'Edit %s', 'knowherepro' ), $singular ),
				'update_item'       => sprintf( esc_html__( 'Update %s', 'knowherepro' ), $singular ),
				'add_new_item'      => sprintf( esc_html__( 'Add New %s', 'knowherepro' ), $singular ),
				'new_item_name'     => sprintf( esc_html__( 'New %s Name', 'knowherepro' ), $singular )
			);

			if ( isset( $args['rewrite'] ) && is_array( $args['rewrite'] ) ) {
				$args['rewrite']['slug'] = _x( 'listing-category', 'Listing category slug - resave permalinks after changing this', 'knowherepro' );
			}

			$permalinks = get_option( 'knowhere_permalinks_settings' );
			if ( isset( $permalinks['category_base'] ) && ! empty( $permalinks['category_base'] ) ) {
				$args['rewrite']['slug'] = $permalinks['category_base'];
			}

			return $args;
		}

		function search_keywords_query( $query ) {
			if ( isset( $_REQUEST['search_keywords'] ) ) {
				$keyword = sanitize_text_field( stripslashes( $_REQUEST['search_keywords'] ) );
				if ( ! empty( $keyword ) ) {
					return $keyword;
				}
			}
			return $query;
		}

		function change_comment_field_names( $translated_text, $text, $domain ) {

			global $knowhere_settings;
			$singular = $knowhere_settings['name-of-listing-singular'];
			$plural   = $knowhere_settings['name-of-listing-plural'];

			switch ( $translated_text ) {
				case 'Post a Job' :
					$translated_text = esc_html__( 'Post a', 'knowherepro' ) . ' ' . $singular;
					break;

				case 'Job Dashboard' :
					$translated_text = $singular . ' ' . esc_html__( 'Dashboard', 'knowherepro' );
					break;

				case 'Jobs':
					$translated_text = $plural;
					break;

				default:
					break;
			}

			return $translated_text;
		}

		function permalink_settings_init() {

			add_settings_field(
				'listing_slug',
				esc_html__( 'Listing URL Base', 'knowherepro' ),
				array($this, 'listing_slug_input'),
				'permalink',
				'optional'
			);
			add_settings_field(
				'listing_category_slug',
				esc_html__( 'Listing Category Base', 'knowherepro' ),
				array($this, 'listing_category_slug_input'),
				'permalink',
				'optional'
			);
			add_settings_field(
				'listing_tag_slug',
				esc_html__( 'Listing Tag Base', 'knowherepro' ),
				array($this, 'listing_tag_slug_input'),
				'permalink',
				'optional'
			);

			if ( ! is_admin() ) {
				return;
			}

			// We need to save the options ourselves; settings api does not trigger save for the permalinks page
			if ( isset( $_POST['listing_category_slug'] ) && isset( $_POST['listing_tag_slug'] ) ) {
				// Cat and tag bases
				$listings_slug = sanitize_text_field( $_POST['listing_base_slug'] );
				$category_slug = sanitize_text_field( $_POST['listing_category_slug'] );
				$tag_slug      = sanitize_text_field( $_POST['listing_tag_slug'] );

				$permalinks = get_option( 'knowhere_permalinks_settings' );

				if ( ! $permalinks ) {
					$permalinks = array();
				}

				$permalinks['listing_base']  = untrailingslashit( $listings_slug );
				$permalinks['category_base'] = untrailingslashit( $category_slug );
				$permalinks['tag_base']      = untrailingslashit( $tag_slug );

				update_option( 'knowhere_permalinks_settings', $permalinks );
			}
		}

		function listing_slug_input() {
			$permalinks = get_option( 'knowhere_permalinks_settings' ); ?>
			<input name="listing_base_slug" type="text" class="regular-text code" value="<?php if ( isset( $permalinks['listing_base'] ) ) {
				echo esc_attr( $permalinks['listing_base'] );
			} ?>" placeholder="<?php echo esc_attr_x( 'listings', 'slug', 'knowherepro' ) ?>"/>
			<?php
		}

		function listing_category_slug_input() {
			$permalinks = get_option( 'knowhere_permalinks_settings' ); ?>
			<input name="listing_category_slug" type="text" class="regular-text code" value="<?php if ( isset( $permalinks['category_base'] ) ) {
				echo esc_attr( $permalinks['category_base'] );
			} ?>" placeholder="<?php echo esc_attr_x( 'listing-category', 'slug', 'knowherepro' ) ?>"/>
			<?php
		}

		function listing_tag_slug_input() {
			$permalinks = get_option( 'knowhere_permalinks_settings' ); ?>
			<input name="listing_tag_slug" type="text" class="regular-text code" value="<?php if ( isset( $permalinks['tag_base'] ) ) {
				echo esc_attr( $permalinks['tag_base'] );
			} ?>" placeholder="<?php echo esc_attr_x( 'listing-tag', 'slug', 'knowherepro' ) ?>"/>
			<?php
		}

		function admin_head( ) {
			$this->permalink_settings_init();

			if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] === 'job-manager-setup' ) {
				add_filter( 'gettext_with_context', array( $this, 'change_comment_field_names' ), 99998, 3 );
			}
		}

		function job_listing_data_fields( $fields ) {

			global $knowhere_settings;

			// reorder fields
			$fields['_company_tagline']['priority'] = 2.0;
			$fields['_job_location']['priority']    = 2.1;
			$fields['_company_website']['priority'] = 2.8;
			$fields['_company_twitter']['priority'] = 2.9;

			$fields['_company_phone'] = array(
				'label'       => esc_html__( 'Phone', 'knowherepro' ),
				'placeholder' => esc_html__( '+1 800 559 6580', 'knowherepro' ),
				'priority'    => 2.2
			);

			$fields['_company_facebook'] = array(
				'label'       => esc_html__( 'Facebook URL', 'knowherepro' ),
				'placeholder' => 'http://',
				'priority'    => 2.3
			);

			$fields['_company_googleplus'] = array(
				'label'       => esc_html__( 'Google+ URL', 'knowherepro' ),
				'placeholder' => 'http://',
				'priority'    => 2.4
			);

			$fields['_company_linkedin'] = array(
				'label'       => esc_html__( 'LinkedIn URL', 'knowherepro' ),
				'placeholder' => 'http://',
				'priority'    => 2.5
			);

			$fields['_company_pinterest'] = array(
				'label'       => esc_html__( 'Pinterest URL', 'knowherepro' ),
				'placeholder' => 'http://',
				'priority'    => 2.6
			);

			$fields['_company_instagram'] = array(
				'label'       => esc_html__( 'Instagram URL', 'knowherepro' ),
				'placeholder' => 'http://',
				'priority'    => 2.7
			);

			unset( $fields['_company_tagline'] );

			switch ( $knowhere_settings['job-type-fields'] ) {
				case 'listing':

					$fields['_job_price_range_min'] = array(
						'label'       => esc_html__( 'Price Range Min', 'knowherepro' ),
						'placeholder' => '',
						'description' => esc_html__('Example: 20', 'knowherepro'),
						'priority'    => 2.7
					);

					$fields['_job_price_range_max'] = array(
						'label'       => esc_html__( 'Price Range Max', 'knowherepro' ),
						'placeholder' => '',
						'description' => esc_html__('Example: 50', 'knowherepro'),
						'priority'    => 2.8
					);

					if ( class_exists( 'Astoundify_Job_Manager_Companies' ) && '' != knowhere_get_the_company_name() ) {
						$fields['_company_description'] = array(
							'label' => esc_html__( 'Description', 'knowherepro' ),
							'placeholder' => '',
							'type'        => 'textarea',
						);
					}

					unset( $fields['_company_video'] );
					unset( $fields['_filled'] );

					break;
				case 'job':

					$fields['_job_price_range_min'] = array(
						'label'       => esc_html__( 'Price Range Min', 'knowherepro' ),
						'placeholder' => '',
						'description' => esc_html__('Example: 20', 'knowherepro'),
						'priority'    => 2.7
					);

					$fields['_job_price_range_max'] = array(
						'label'       => esc_html__( 'Price Range Max', 'knowherepro' ),
						'placeholder' => '',
						'description' => esc_html__('Example: 50', 'knowherepro'),
						'priority'    => 2.8
					);

					if ( class_exists( 'Astoundify_Job_Manager_Companies' ) && '' != knowhere_get_the_company_name() ) {
						$fields['_company_description'] = array(
							'label' => esc_html__('Company Description', 'knowherepro'),
							'placeholder' => '',
							'type' => 'textarea',
						);
					}

					unset( $fields['_company_video'] );

					break;
				case 'property':

					$agents_array = array( -1 => esc_html__('None', 'knowherepro') );
					$agents_posts = get_posts( array( 'post_type' => 'knowhere_agent', 'posts_per_page' => -1, 'suppress_filters' => 0 ) );
					if ( !empty($agents_posts) ) {
						foreach ( $agents_posts as $agent_post ) {
							$agents_array[$agent_post->ID] = $agent_post->post_title;
						}
					}

					$fields['_property_size'] = array(
						'label'       => esc_html__( 'Property Size', 'knowherepro' ),
						'placeholder' => esc_html__('Eg: 1300', 'knowherepro'),
						'priority'    => 2.9
					);

					$fields['_area_size'] = array(
						'label'       => esc_html__( 'Area Size', 'knowherepro' ),
						'placeholder' => esc_html__('Eg: 1500', 'knowherepro'),
						'priority'    => 3.0
					);

					$fields['_bedrooms'] = array(
						'label'       => esc_html__( 'Bedrooms', 'knowherepro' ),
						'placeholder' => '',
						'priority'    => 3.2
					);

					$fields['_bathrooms'] = array(
						'label'       => esc_html__( 'Bathrooms', 'knowherepro' ),
						'placeholder' => esc_html__('Eg: 2', 'knowherepro'),
						'priority'    => 3.3
					);

					$fields['_garages'] = array(
						'label'       => esc_html__( 'Garages', 'knowherepro' ),
						'placeholder' => esc_html__('Eg: 1', 'knowherepro'),
						'priority'    => 3.4
					);

					$fields['_garages_size'] = array(
						'label'       => esc_html__( 'Garages Size', 'knowherepro' ),
						'placeholder' => '',
						'priority'    => 3.5
					);

					$fields['_rooms'] = array(
						'label'       => esc_html__( 'Rooms', 'knowherepro' ),
						'placeholder' => esc_html__('Eg: 3', 'knowherepro'),
						'priority'    => 3.6
					);

					$fields['_year_of_construction'] = array(
						'label'       => esc_html__( 'Year of Construction', 'knowherepro' ),
						'placeholder' => _x( 'yyyy-mm-dd', 'Date format placeholder.', 'knowherepro' ),
						'classes'     => array( 'job-manager-datepicker' ),
						'priority'    => 3.7
					);

					$fields['_prop_price'] = array(
						'label'       => esc_html__( 'Sale or Rent Price', 'knowherepro' ),
						'placeholder' => esc_html__( 'Enter Sale or Rent Price', 'knowherepro' ),
						'priority'    => 3.7
					);

					$fields['_prop_postfix'] = array(
						'label'       => esc_html__( 'After Price Label', 'knowherepro' ),
						'placeholder' => esc_html__( 'Eg: Per Month', 'knowherepro' ),
						'priority'    => 3.8
					);

					$fields['_property_id'] = array(
						'label'       => esc_html__( 'Property ID', 'knowherepro' ),
						'placeholder' => '',
						'priority'    => 3.9
					);

					$fields['_agent_id'] = array(
						'label'       => esc_html__( 'Agent ID', 'knowherepro' ),
						'placeholder' => '',
						'type' => 'select',
						'options' => $agents_array,
						'priority'    => 4.0
					);

					$fields['_postal_code'] = array(
						'label'       => esc_html__( 'Postal Code / Zip', 'knowherepro' ),
						'placeholder' => esc_html__( 'Enter your property zip code', 'knowherepro' ),
						'priority'    => 4.1
					);

					$fields['_open_house_date'] = array(
						'label'       => esc_html__( 'Open House date', 'knowherepro' ),
						'placeholder' => _x( 'yyyy-mm-dd', 'Date format placeholder.', 'knowherepro' ),
						'classes'     => array( 'job-manager-datepicker' ),
						'priority'    => 4.2
					);

					$fields['_last_renovation'] = array(
						'label'       => esc_html__( 'Last Renovation', 'knowherepro' ),
						'placeholder' => _x( 'yyyy-mm-dd', 'Date format placeholder.', 'knowherepro' ),
						'classes'     => array( 'job-manager-datepicker' ),
						'priority'    => 4.3
					);

					$fields['_company_video']['label'] = esc_html__( 'Video', 'knowherepro' );

					unset( $fields['_company_name'] );
					unset( $fields['_company_website'] );
					unset( $fields['_filled'] );

					break;
			}

			return $fields;
		}

		function save_job_listing( $job_id, $post ) {
			update_post_meta( $job_id, '_company_description', wp_kses_post( $_POST['_company_description'] ) );
		}

		function replace_jobs_with_listings( &$item, $key ) {

			if ( $item === 'Job Listings' ) {
				$item = esc_html__( 'Listing', 'knowherepro' );
			}

			if ( $item === 'Job Submission' ) {
				$item = esc_html__( 'Submission', 'knowherepro' );
			}

			if ( $key === 'desc' || $key === 'any' || $key === 'all' || $key === 'label' ) {
				if ( is_numeric( strpos( $item, 'Job' ) ) ) {
					$item = str_replace( 'Job', esc_html__( 'Listing', 'knowherepro' ), $item );
				}
			}

			return $item;
		}

		function custom_submit_job_form_fields( $fields ) {

			global $knowhere_settings;
			$singular = $knowhere_settings['name-of-listing-singular'];

			array_walk_recursive( $fields, array( $this, 'replace_jobs_with_listings') );

			$fields['job']['job_title']['label']       = sprintf( esc_html__('%s Name', 'knowherepro') , $singular);
			$fields['job']['job_title']['placeholder'] = sprintf( esc_html__('Your %s name', 'knowherepro'), strtolower($singular) );

			$fields['job']['job_description']['priority']    = 2.2;
			$fields['job']['job_description']['placeholder'] = sprintf( esc_html__( 'An overview of your %s and the things you love about it.', 'knowherepro' ) , $singular);

			$fields['job']['job_category']['priority']    = 4;
			$fields['job']['job_category']['label'] = sprintf(esc_html__( '%s Category', 'knowherepro' ), $singular);
			$fields['job']['job_category']['description'] = sprintf( '<div class="kw-info-icon" data-tooltip="%s" data-tooltip-position="right"></div>', esc_html__( 'Visitors can filter their search by the categories and amenities they want - so make sure you choose them wisely and include all the relevant ones', 'knowherepro' ) );

//			$fields['job']['job_location']['priority']    = 2.5;
			$fields['job']['job_location']['placeholder'] = esc_html__( 'e.g.9870 St Vincent Place, Glasgow', 'knowherepro' );
			$fields['job']['job_location']['description'] = sprintf( '<div class="kw-info-icon" data-tooltip="%s" data-tooltip-position="right"></div>', esc_html__( 'Leave this blank if the location is not important.', 'knowherepro' ));

			$fields['job']['job_tags']['priority'] = 3;

			$fields['company']['company_phone'] = array(
				'label'       => esc_html__( 'Phone', 'knowherepro' ),
				'type'        => 'text',
				'placeholder' => esc_html__( '+1 800 559 6580', 'knowherepro' ),
				'required'    => false,
				'priority'    => 2.8
			);

			$fields['company']['company_logo']['label'] = esc_html__( 'Featured Image', 'knowherepro' );
			$fields['company']['company_logo']['priority'] = 2.7;

			$fields['company']['company_tagline']['priority']    = 2.1;
			$fields['company']['company_tagline']['description'] = sprintf( '<div class="kw-info-icon" data-tooltip="%s" data-tooltip-position="right"></div>', esc_html__( 'Keep it short and descriptive as it will appear on search results instead of the link description', 'knowherepro' ) );

			$fields['company']['company_website']['priority']    = 2.9;
			$fields['company']['company_website']['placeholder'] = esc_html__( 'e.g yourwebsite.com, London', 'knowherepro' );
			$fields['company']['company_website']['description'] = sprintf( '<div class="kw-info-icon" data-tooltip="%s" data-tooltip-position="right"></div>', esc_html__( 'You can add more similar panels to better help the user fill the form', 'knowherepro' ) );

			switch ( $knowhere_settings['job-type-fields'] ) {
				case 'listing':

					$fields['company']['mad_perm_metadata']['label']              = esc_html__( 'Gallery Images', 'knowherepro' );
					$fields['company']['mad_perm_metadata']['priority']           = 2.6;
					$fields['company']['mad_perm_metadata']['required']           = false;
					$fields['company']['mad_perm_metadata']['type']               = 'file';
					$fields['company']['mad_perm_metadata']['ajax']               = true;
					$fields['company']['mad_perm_metadata']['placeholder']        = '';
					$fields['company']['mad_perm_metadata']['allowed_mime_types'] = $fields['company']['company_logo']['allowed_mime_types'];
					$fields['company']['mad_perm_metadata']['multiple']           = true;
					$fields['company']['mad_perm_metadata']['description']        = sprintf( '<div class="kw-info-icon" data-tooltip="%s" data-tooltip-position="right"></div>', esc_html__( 'The first image will be shown on listing cards.', 'knowherepro' ));

					if ( class_exists( 'Astoundify_Job_Manager_Companies' ) && '' != knowhere_get_the_company_name() ) {

						$fields['company']['company_description'] = array(
							'label' => _x('Description', 'description on submission form', 'knowherepro'),
							'type' => 'wp-editor',
							'required' => false,
							'placeholder' => '',
							'priority' => 3.5,
						);

					}

					$fields['job']['job_price_range_min'] = array(
						'label'       => esc_html__( 'Price Range Min', 'knowherepro' ),
						'type' => 'text',
						'placeholder' => '',
						'required'    => false,
						'description' => esc_html__('Example: 20', 'knowherepro'),
						'priority'    => 9.1,
						'css_class'   => 'one-fifth'
					);

					$fields['job']['job_price_range_max'] = array(
						'label'       => esc_html__( 'Price Range Max', 'knowherepro' ),
						'type' => 'text',
						'placeholder' => '',
						'required'    => false,
						'description' => esc_html__('Example: 50', 'knowherepro'),
						'priority'    => 9.2,
						'css_class'   => 'one-fifth'
					);

					unset( $fields['company']['company_video'] );

					break;
				case 'job':

					if ( class_exists( 'Astoundify_Job_Manager_Companies' ) && '' != knowhere_get_the_company_name() ) {

						$fields['company']['company_description'] = array(
							'label' => _x('Description', 'company description on submission form', 'knowherepro'),
							'type' => 'wp-editor',
							'required' => false,
							'placeholder' => '',
							'priority' => 3.5,
						);

					}

					$fields['job']['job_price_range_min'] = array(
						'label'       => esc_html__( 'Price Range Min', 'knowherepro' ),
						'type' => 'text',
						'placeholder' => '',
						'required'    => false,
						'description' => esc_html__('Example: 20', 'knowherepro'),
						'priority'    => 9.1,
						'css_class'   => 'one-fifth'
					);

					$fields['job']['job_price_range_max'] = array(
						'label'       => esc_html__( 'Price Range Max', 'knowherepro' ),
						'type' => 'text',
						'placeholder' => '',
						'required'    => false,
						'description' => esc_html__('Example: 50', 'knowherepro'),
						'priority'    => 9.2,
						'css_class'   => 'one-fifth'
					);

					unset( $fields['company']['company_video'] );

					break;
				case 'property':

					$fields['company']['mad_perm_metadata']['label']              = esc_html__( 'Gallery Images', 'knowherepro' );
					$fields['company']['mad_perm_metadata']['priority']           = 2.6;
					$fields['company']['mad_perm_metadata']['required']           = false;
					$fields['company']['mad_perm_metadata']['type']               = 'file';
					$fields['company']['mad_perm_metadata']['ajax']               = true;
					$fields['company']['mad_perm_metadata']['placeholder']        = '';
					$fields['company']['mad_perm_metadata']['allowed_mime_types'] = $fields['company']['company_logo']['allowed_mime_types'];
					$fields['company']['mad_perm_metadata']['multiple']           = true;
					$fields['company']['mad_perm_metadata']['description']        = sprintf( '<div class="kw-info-icon" data-tooltip="%s" data-tooltip-position="right"></div>', esc_html__( 'The first image will be shown on listing cards.', 'knowherepro' ));

					$agents_array = array( -1 => esc_html__('None', 'knowherepro') );
					$agents_posts = get_posts( array( 'post_type' => 'knowhere_agent', 'posts_per_page' => -1, 'suppress_filters' => 0 ) );
					if ( !empty($agents_posts) ) {
						foreach ( $agents_posts as $agent_post ) {
							$agents_array[$agent_post->ID] = $agent_post->post_title;
						}
					}

					$fields['job']['property_size'] = array(
						'label'       => esc_html__( 'Property Size', 'knowherepro' ),
						'type'        => 'text',
						'placeholder' => esc_html__('Eg: 1300', 'knowherepro'),
						'required'    => false,
						'priority'    => 7.0,
						'css_class'   => 'one-sixth'
					);

					$fields['job']['area_size'] = array(
						'label'       => esc_html__( 'Area Size', 'knowherepro' ),
						'type'        => 'text',
						'placeholder' => esc_html__('Eg: 1500', 'knowherepro'),
						'required'    => false,
						'priority'    => 7.1,
						'css_class'   => 'one-sixth'
					);

					$fields['job']['bedrooms'] = array(
						'label'       => esc_html__( 'Bedrooms', 'knowherepro' ),
						'type'        => 'text',
						'placeholder' => esc_html__('Eg: 1500', 'knowherepro'),
						'required'    => false,
						'priority'    => 7.2,
						'css_class'   => 'one-sixth'
					);

					$fields['job']['bathrooms'] = array(
						'label'       => esc_html__( 'Bathrooms', 'knowherepro' ),
						'type'        => 'text',
						'placeholder' => esc_html__('Eg: 2', 'knowherepro'),
						'required'    => false,
						'priority'    => 7.3,
						'css_class'   => 'one-sixth'
					);

					$fields['job']['garages'] = array(
						'label'       => esc_html__( 'Garages', 'knowherepro' ),
						'type'        => 'text',
						'placeholder' => esc_html__('Eg: 1', 'knowherepro'),
						'required'    => false,
						'priority'    => 7.4,
						'css_class'   => 'one-sixth'
					);

					$fields['job']['_garages_size'] = array(
						'label'       => esc_html__( 'Garages Size', 'knowherepro' ),
						'type'        => 'text',
						'placeholder' => esc_html__('Eg: 1', 'knowherepro'),
						'required'    => false,
						'priority'    => 7.5,
						'css_class'   => 'one-sixth'
					);

					$fields['job']['rooms'] = array(
						'label'       => esc_html__( 'Rooms', 'knowherepro' ),
						'type'        => 'text',
						'placeholder' => esc_html__('Eg: 3', 'knowherepro'),
						'required'    => false,
						'priority'    => 7.6,
						'css_class'   => 'one-sixth'
					);

					$fields['job']['prop_price'] = array(
						'label' => esc_html__( 'Sale or Rent Price', 'knowherepro' ),
						'type' => 'text',
						'required' => true,
						'placeholder' => esc_html__('Enter Sale or Rent Price', 'knowherepro'),
						'priority'    => 7.8,
						'css_class'   => 'one-sixth'
					);

					$fields['job']['prop_postfix'] = array(
						'label' => esc_html__( 'After Price Label', 'knowherepro' ),
						'type' => 'text',
						'placeholder' => esc_html__('Eg: Per Month', 'knowherepro'),
						'priority'    => 7.9,
						'required'    => false,
						'css_class'   => 'one-sixth'
					);

					$fields['job']['property_id'] = array(
						'label' => esc_html__( 'Property ID', 'knowherepro' ),
						'type' => 'text',
						'required'    => false,
						'placeholder' => '',
						'priority'    => 8.0,
						'css_class'   => 'one-sixth'
					);

					$fields['job']['agent_id'] = array(
						'label' => esc_html__( 'Agent ID', 'knowherepro' ),
						'type' => 'select',
						'options' => $agents_array,
						'placeholder' => '',
						'required'    => false,
						'priority'    => 8.1,
						'css_class'   => 'one-sixth'
					);

					$fields['job']['postal_code'] = array(
						'label'       => esc_html__( 'Postal Code / Zip', 'knowherepro' ),
						'type' 		  => 'text',
						'placeholder' => esc_html__( 'Enter your property zip code', 'knowherepro' ),
						'required'    => false,
						'priority'    => 8.2,
						'css_class'   => 'one-sixth'
					);

					$fields['job']['year_of_construction'] = array(
						'label'       => esc_html__( 'Year of Construction', 'knowherepro' ),
						'type' => 'text',
						'placeholder' => _x( 'yyyy-mm-dd', 'Date format placeholder.', 'knowherepro' ),
						'css_class'   => 'one-half',
						'priority'    => 8.3
					);

					$fields['job']['open_house_date'] = array(
						'label'       => esc_html__( 'Open House date', 'knowherepro' ),
						'type' => 'text',
						'required'    => false,
						'placeholder' => _x( 'yyyy-mm-dd', 'Date format placeholder.', 'knowherepro' ),
						'priority'    => 8.4,
						'css_class'   => 'one-half'
					);

					$fields['job']['last_renovation'] = array(
						'label'       => esc_html__( 'Last Renovation', 'knowherepro' ),
						'type' => 'text',
						'required'    => false,
						'placeholder' => _x( 'yyyy-mm-dd', 'Date format placeholder.', 'knowherepro' ),
						'priority'    => 8.5,
						'css_class'   => 'one-half'
					);

					$fields['company']['company_video'] = array(
						'label' => esc_html__( 'Video', 'knowherepro' ),
						'type' => 'text',
						'required'    => false,
						'placeholder' => esc_html__('A link to a video', 'knowherepro'),
						'priority'    => 8.6
					);

					break;
			}

			$fields['company']['company_facebook'] = array(
				'label'       => esc_html__( 'Facebook URL', 'knowherepro' ),
				'type'        => 'text',
				'placeholder' => 'http://facebook.com/yourusername',
				'priority'    => 3.0,
				'required'    => false,
				'css_class'   => 'one-third'
			);

			$fields['company']['company_googleplus'] = array(
				'label'       => esc_html__( 'Google+ URL', 'knowherepro' ),
				'type'        => 'text',
				'placeholder' => 'http://',
				'priority'    => 3.1,
				'required'    => false,
				'css_class'   => 'one-third'
			);

			$fields['company']['company_linkedin'] = array(
				'label'       => esc_html__( 'LinkedIn URL', 'knowherepro' ),
				'type'        => 'text',
				'placeholder' => 'http://',
				'priority'    => 3.2,
				'required'    => false,
				'css_class'   => 'one-third'
			);

			$fields['company']['company_pinterest'] = array(
				'label'       => esc_html__( 'Pinterest URL', 'knowherepro' ),
				'type'        => 'text',
				'placeholder' => 'http://',
				'priority'    => 3.3,
				'required'    => false,
				'css_class'   => 'one-half'
			);

			$fields['company']['company_instagram'] = array(
				'label'       => esc_html__( 'Instagram URL', 'knowherepro' ),
				'type'        => 'text',
				'placeholder' => 'http://',
				'priority'    => 3.4,
				'required'    => false,
				'css_class'   => 'one-half'
			);

			unset( $fields['company']['company_name'] );
			unset( $fields['company']['company_tagline'] );
//			unset( $fields['company']['company_logo'] );

			return $fields;
		}

		function add_total_jobs_response( $results, $jobs ) {

			if ( true === $results['found_jobs'] ) {

				$results['total_found'] = $jobs->found_posts;

				if ( $jobs->post_count ) {
					$message = sprintf( _n( 'Search completed. Found %d matching record.', 'Search completed. Found %d matching records.', $jobs->found_posts, 'knowherepro' ), $jobs->found_posts );
				} else {
					$message = '';
				}

				$results['showing'] = $message;

			} else {
				$results['total_found'] = 0;
			}
			return $results;
		}

		function job_manager_get_listings_custom_filter_text( $search_values ) {
			return $search_values;
		}

		function job_manager_settings( $args ) {

			$args['job_listings'][1][] = array(
				'name'       => 'job_manager_enable_radius',
				'std'        => '1',
				'label'      => __( 'Radius', 'knowherepro' ),
				'cb_label'   => __( 'Enable listing radius', 'knowherepro' ),
				'type'       => 'checkbox',
				'desc' => '',
				'attributes' => array()
			);

			if ( knowhere_using_facetwp() ) {

				$args['job_fwp'] = array(
					esc_html__( 'FacetWP', 'knowherepro' ),
					array(
						array(
							'name'  => 'knowhere_facets_config',
							'type'  => 'knowhere_facetwp_drag_and_drop',
							'label' => ''
						),
					)
				);

			}

			return $args;
		}


		function job_listing_admin_columns( $columns ) {

			if ( ! is_array( $columns ) ) {
				$columns = array();
			}

			unset ( $columns["job_listing_type"] );
			unset ( $columns["job_position"] );

			$columns = array_slice( $columns, 0, 1, true ) +
				array( "knowhere_job_image" => esc_html__( "Image", 'knowherepro' ) )
				+ array_slice( $columns, 1, count( $columns ) - 1, true );

			$columns = array_slice( $columns, 0, 2, true ) +
				array( "knowhere_job_position" => esc_html__( "Position", 'knowherepro' ) )
				+ array_slice( $columns, 2, count( $columns ) - 1, true );

			$columns = array_slice( $columns, 0, 3, true ) +
				array( "knowhere_job_listing_type" => esc_html__( "Type", 'knowherepro' ) )
				+ array_slice( $columns, 3, count( $columns ) - 1, true );

			return $columns;
		}

		function job_listing_admin_custom_columns( $column ) {
			global $post;

			switch ( $column ) {
				case "knowhere_job_image":
					$company_logo_ID = knowhere_get_post_image_id( $post->ID );

					$company_logo = '';
					if ( ! empty( $company_logo_ID ) ) {
						$company_logo = wp_get_attachment_image_src( $company_logo_ID );
					}

					if ( ! empty( $company_logo ) && ( strstr( $company_logo[0], 'http' ) || file_exists( $company_logo[0] ) ) ) {
						$company_logo = $company_logo[0];
						$company_logo = job_manager_get_resized_image( $company_logo, 'thumbnail' );
						echo '<img class="company_logo" src="' . esc_attr( $company_logo ) . '" alt="' . esc_attr( get_the_company_name( $post ) ) . '" />';
					}
					break;
				case "knowhere_job_position":
					echo '<div class="job_position">';
						echo '<a href="' . admin_url('post.php?post=' . $post->ID . '&action=edit') . '" class="tips job_title" data-tip="' . sprintf( __( 'ID: %d', 'knowherepro' ), $post->ID ) . '">' . $post->post_title . '</a>';
						echo '<div class="company">';

						if ( get_the_company_website() ) {
							the_company_name( '<span class="tips" data-tip="' . esc_attr( get_the_company_tagline() ) . '"><a href="' . esc_url( get_the_company_website() ) . '">', '</a></span>' );
						} else {
							the_company_name( '<span class="tips" data-tip="' . esc_attr( get_the_company_tagline() ) . '">', '</span>' );
						}

						echo '</div>';
					echo '</div>';
					break;
				case "knowhere_job_listing_type":
					$type = wpjm_get_the_job_types( $post );

					if ( is_array($type) && !empty($type) ) {
						$type = $type[0];

						if ( $type ) {
							knowhere_bg_color_label( $type );
						}
					}

					break;
			}
		}

		function output_the_listings( $html ) {
			$output = '';

			global $post, $knowhere_settings, $knowhere_config;

			// if this is a page with [jobs] shortcode, extract the show_map param
			if ( ! empty( $post->post_content ) && has_shortcode( $post->post_content, 'jobs' ) ) {
				$show_map = knowhere_jobs_shortcode_get_show_map_param();
			} elseif ( !empty ( $post->post_content ) && has_shortcode( $post->post_content, 'vc_mad_listing_map' ) ) {
				$show_map = false;
			} else {
				$show_map = knowhere_listings_page_shortcode_get_show_map_param();
			}

			$filter_position = $knowhere_settings['job-filter-style-position'];
			$filter_position_left_extend = $knowhere_settings['job-filter-left-position-extend'];
			$selected_extend_type = $knowhere_settings['job-filter-left-extend-type'];

			$term_filter_position = knowhere_job_get_term('pix_term_filter_position');
			if ( !empty($term_filter_position) ) {
				$filter_position = $term_filter_position;
			}

			$classes = array( 'kw-flex-holder', 'kw-listings', $filter_position );

			if ( $filter_position == 'kw-left-position' ) {
				if ( $filter_position_left_extend ) {
					$classes[] = 'kw-left-position-extend';
					$classes[] = $selected_extend_type;
				}
			}

			$count_columns = $knowhere_settings['job-listings-columns'];
			$term_count_columns = knowhere_job_get_term('pix_term_count_columns');

			if ( knowhere_is_realy_job_manager_tax() ) {
				$count_columns = $knowhere_settings['job-category-columns'];
			}

			if ( $term_count_columns && absint($term_count_columns) ) {
				$count_columns = $term_count_columns;
			}

			$style = $knowhere_settings['job-listings-style'];
			$term_style = knowhere_job_get_term('pix_term_style');
			if ( !empty($term_style) ) { $style = $term_style; }

			$view = $knowhere_settings['job-category-view'];
			$term_view = knowhere_job_get_term('pix_term_view');
			if ( $term_view ) { $view = $term_view; }

			if ( in_array($style, array('kw-type-2') ) ) { $view = 'kw-grid-view'; }
			if ( in_array($style, array('kw-type-4') ) ) { $view = 'kw-list-view'; }

			$knowhere_config['term_view'] = $view;

			$classes[] = $style;
			$classes[] = $view;
			$classes[] = 'kw-cols-' . $count_columns;

			if ( false === $show_map ) {
				$classes[] = 'kw-without-map';
			}

			$output .= '<div class="' . esc_attr(implode( ' ', $classes )) . '">';
			if ( true === $show_map ) {
				$output .= '<div class="kw-flex-left">' . $html . '	</div>';
				$output .= '<div class="kw-flex-right kw-listings-gmap" id="kw-listings-gmap"></div>';
			} else {
				$output .= $html;
			}
			$output .= '</div>';

			return $output;
		}

		function job_manager_output_jobs_defaults( $atts ) {

			$type = get_queried_object();

			$atts['show_map'] = true;
			$atts['selected_min_price'] = '';
			$atts['selected_max_price'] = '';
			$atts['selected_bedrooms'] = '';
			$atts['selected_bathrooms'] = '';
			$atts['selected_region'] = '';
			$atts['selected_feature'] = array();

			if ( is_tax( 'job_listing_type' ) ) {
				$atts['job_types'] = $type->slug;
				$atts['selected_job_types'] = $type->slug;
				$atts['show_categories'] = true;
			} elseif ( is_tax( 'job_listing_category' ) ) {
				$atts['show_categories'] = true;
				$atts['categories'] = true;
				$atts['selected_category'] = $type->term_id;
			} elseif ( is_search() ) {
				$atts['keywords'] = get_search_query();
				$atts['show_filters'] = false;
			}

			if ( isset( $_GET['search_categories'] ) ) {
				$categories = array_filter( array_map( 'esc_attr', $_GET['search_categories'] ) );

				if ( ! empty( $categories ) ) {
					$atts['selected_category'] = $categories[0];
				}

				$atts['show_categories'] = true;
				$atts['categories'] = false;
			}

			if ( isset( $_GET['search_region']) && !empty($_GET['search_region']) ) {
				$atts['selected_region'] = $_GET['search_region'][0];
			}

			if ( isset( $_GET['search_bedrooms'] ) && !empty($_GET['search_bedrooms']) ) {
				$bedrooms = $_GET['search_bedrooms'];
				if ( ! empty( $bedrooms ) ) {
					$atts['selected_bedrooms'] = intval($bedrooms[0]);
				}
			}

			if ( isset( $_GET['search_bathrooms'] ) && !empty($_GET['search_bathrooms']) ) {
				$bathrooms = $_GET['search_bathrooms'];
				if ( ! empty( $bathrooms ) ) {
					$atts['selected_bathrooms'] = intval($bathrooms[0]);
				}
			}

			if ( isset( $_GET['search_min_price'] ) && !empty($_GET['search_min_price']) ) {
				$min_price = doubleval(knowhere_cleaner($_GET['search_min_price']));
				if ( ! empty( $min_price ) ) {
					$atts['selected_min_price'] = $min_price;
				}
			}

			if ( isset( $_GET['search_max_price'] ) && !empty($_GET['search_max_price']) ) {
				$max_price = doubleval(knowhere_cleaner($_GET['search_max_price']));
				if ( ! empty( $max_price ) ) {
					$atts['selected_max_price'] = $max_price;
				}
			}

			if ( isset( $_GET['search_feature'] ) && !empty($_GET['search_feature']) ) {
				if ( is_array($_GET['search_feature']) ) {
					$features = $_GET['search_feature'];
					if ( !empty($features) ) {
						$atts['selected_feature'] = $features;
					}
				}
			}

			return $atts;
		}

		function add_google_language_param( $url, $raw_address ) {
			$url = add_query_arg( array( 'language' => get_locale() ), $url );

			return $url;
		}

		public function add_submit_button_and_controls( $atts ) {
			global $knowhere_settings;

			$view = $knowhere_settings['job-category-view'];
			$term_view = knowhere_job_get_term('pix_term_view');

			if ( $term_view ) { $view = $term_view; }

			ob_start(); ?>

			<div class="kw-job-filters-controls-form">

				<div class="kw-job-filters-results"><div class="kw-results-count"></div></div>

				<div class="kw-listings-controls-wrap">

					<?php echo knowhere_get_sort_filter(); ?>

					<div class="kw-listing-layout-controls">
						<a href="javascript:void(0)" class="kw-listing-layout-control <?php echo ( $view == 'kw-grid-view' ) ? 'kw-active' : '' ?>"
						   data-layout="grid"><i
								class="fa fa-th"></i></a>
						<a href="javascript:void(0)" class="kw-listing-layout-control <?php echo ( $view == 'kw-list-view' ) ? 'kw-active' : '' ?>"
						   data-layout="list"><i class="fa fa-list"></i></a>
					</div>

				</div><!--/ .kw-listings-controls-wrap-->

			</div><!--/ .kw-job-filters-controls-form-->

			<?php echo ob_get_clean();
		}


	}

	new Knowhere_Job_Manager_Config();

}