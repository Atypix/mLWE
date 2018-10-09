<?php
/**
 * Custom functions of WP Job Manager.
 * See: https://wpjobmanager.com/
 *
 * @package KnowherePro
 */

/*  Sort listings
/* ---------------------------------------------------------------------- */

if ( !function_exists('knowhere_sort_listings_query') ) {
	function knowhere_sort_listings_query( $query_args, $sort_option ) {
		if ( 'date-desc' === $sort_option ) {
			$query_args['orderby'] = 'date';
			$query_args['order'] = 'DESC';
		} elseif ( 'date-asc' === $sort_option ) {
			$query_args['orderby'] = 'date';
			$query_args['order'] = 'ASC';
		} elseif ( 'featured' === $sort_option ) {
			$query_args['meta_query'][] = array(
				'key'     => '_featured',
				'value'   => '1',
				'compare' => '='
			);
		} elseif ( 'random' === $sort_option ) { // Random.
			$query_args['orderby'] = 'rand';
		}

		return $query_args;
	}
}

/*  Get sort options
/* ---------------------------------------------------------------------- */

if ( !function_exists('knowhere_get_sort_options') ) {
	function knowhere_get_sort_options() {

		$options = apply_filters('knowhere_get_sort_options', array(
			'date-desc' => esc_html__('Newest', 'knowherepro'),
			'date-asc' => esc_html__('Oldest', 'knowherepro'),
			'featured' => esc_html__('Featured', 'knowherepro'),
			'random' => esc_html__('Random', 'knowherepro')
		));

		return $options;
	}
}

/* Cleaner
/* ---------------------------------------------------------------------- */

if ( ! function_exists( 'knowhere_cleaner' ) ) {
	function knowhere_cleaner($string) {
		$string = preg_replace('/&#36;/', '', $string);
		$string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
		$string = preg_replace('/\D/', '', $string);
		return $string;
	}
}

if ( ! function_exists( 'knowhere_job_manager_job_filters_distance' ) ) {
	function knowhere_job_manager_job_filters_distance() {

		global $knowhere_settings;
		$checked = get_option( 'job_manager_enable_radius', true );

		if ( is_tax( 'job_listing_region' ) || ! $checked ) {
			return;
		}

		$radius = isset( $_GET['search_radius'] ) ? absint( $_GET['search_radius'] ) : $knowhere_settings['default_radius'];

	?>
		<div class="search_radius">

			<label for="use_search_radius" class="search_radius_label"><?php echo esc_html__('Radius', 'knowherepro') ?></label>

			<div class="search-radius-wrapper">

				<div class="search-radius-label">
					<label for="use_search_radius">
						<input type="checkbox" name="use_search_radius" id="use_search_radius" value="<?php echo esc_attr($checked) ?>">
						<?php printf( __( '<span class="radi_text">%1$s: </span><span class="radi">%2$s</span> %3$s', 'knowherepro' ), esc_html__('Radius', 'knowherepro'), $radius, knowhere_results_map_unit()[1] ); ?>
					</label>
				</div>

				<div class="search-radius-slider">
					<div class="radius-range-wrap">
						<div id="radius-range-slider"></div>
					</div>
				</div>

				<input type="hidden" id="search_radius" name="search_radius" value="<?php echo isset( $_GET['search_radius'] ) ? absint( $_GET['search_radius'] ) : $radius; ?>" />
				<input type="hidden" id="search_lat" name="search_lat" value="<?php echo isset( $_GET['search_lat'] ) ? esc_attr( $_GET['search_lat'] ) : 0; ?>" />
				<input type="hidden" id="search_lng" name="search_lng" value="<?php echo isset( $_GET['search_lng'] ) ? esc_attr( $_GET['search_lng'] ) : 0; ?>" />

			</div>

		</div>
	<?php
	}
}

if ( !function_exists('knowhere_get_google_maps_api_key') ) {
	function knowhere_get_google_maps_api_key() {

		global $knowhere_settings;
		$google_maps_key = $knowhere_settings['gmap-api'];

		if ( ! empty( $google_maps_key ) ) {
		} else {
			$google_maps_key = '';
		}
		return esc_attr( trim( $google_maps_key ) );
	}
}

if ( !function_exists('knowhere_get_search_keywords') ) {
	function knowhere_get_search_keywords() {

		ob_start(); ?>

			<div class="kw-search-keywords">
				<input type="text" name="search_keywords" placeholder="<?php esc_attr_e( 'Search', 'knowherepro' ); ?>" value="" />
				<button class="kw-search-keywords-btn"><?php echo esc_html__('Search', 'knowherepro') ?></button>
			</div>

		<?php return ob_get_clean();
	}
}

if ( !function_exists('knowhere_get_sort_filter') ) {
	function knowhere_get_sort_filter() {
			$options = knowhere_get_sort_options();

			if ( ! $options ) { return; }

			ob_start(); ?>

			<div class="kw-titled-select">

				<select name="search_sort" class="job-manager-filter" autocomplete="off">
					<option value="" selected="selected"><?php esc_html_e( 'Sort by', 'knowherepro' ); ?></option>
					<?php foreach ( $options as $id => $option ) : ?>
						<option value="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $option ); ?></option>
					<?php endforeach; ?>
				</select>

			</div><!--/ .kw-titled-select-->

		<?php return ob_get_clean();
	}
}

if ( !function_exists('knowhere_get_google_maps_api_url') ) {
	function knowhere_get_google_maps_api_url() {
		$base = '//maps.googleapis.com/maps/api/js';

		$args = array(
			'language' => get_locale() ? substr( get_locale(), 0, 2 ) : '',
		);

		$args['libraries'] = 'places';

		// API key.
		$key = knowhere_get_google_maps_api_key();

		if ( '' !== $key ) {
			$args['key'] = $key;
		}

		$url = esc_url_raw( add_query_arg( $args, $base ) );

		return apply_filters( 'knowhere_google_maps_api_url', $url, $args );
	}
}

if ( !function_exists('knowhere_results_map_unit') ) {
	function knowhere_results_map_unit() {
		global $knowhere_settings;

		$radius_unit = $knowhere_settings['radius_unit'];
		if ( !$radius_unit ) $radius_unit = 'km';

		if ( $radius_unit == 'km' ) {
			$earth_radius = 6371;
		} elseif ( $radius_unit == 'mi' ) {
			$earth_radius = 3959;
		} else {
			$earth_radius = 6371;
		}

		return array( $earth_radius, $radius_unit );
	}
}

/*  Is job manager page single
/* ---------------------------------------------------------------------- */

if ( !function_exists('knowhere_listings_page_shortcode_get_show_map_param') ) {
	function knowhere_listings_page_shortcode_get_show_map_param() {

		global $knowhere_settings, $knowhere_config;

		if ( knowhere_is_realy_job_manager_tax() ) {

			$show_map = knowhere_job_get_term('pix_term_show_map');

			if ( empty($show_map) && !$knowhere_settings['job-show-map'] || $show_map == 'no' || $knowhere_config['sidebar_position'] !== 'kw-no-sidebar' ) {
				return false;
			}

		}

		$listings_page_id = get_option('job_manager_jobs_page_id', false);

		if ( false !== $listings_page_id ) {
			$listings_page = get_post($listings_page_id);

			if ( !is_wp_error($listings_page) ) {
				return knowhere_jobs_shortcode_get_show_map_param($listings_page->post_content);
			}
		}

		return true;
	}
}

if ( !function_exists('knowhere_string_to_bool') ) {
	function knowhere_string_to_bool($value)
	{
		return ( is_bool($value) && $value ) || in_array( $value, array( '1', 'true', 'yes' ) ) ? true : false;
	}
}

if ( !function_exists('knowhere_jobs_shortcode_get_show_map_param') ) {
	function knowhere_jobs_shortcode_get_show_map_param($content = '')
	{
		global $post;

		if ( empty($content) && isset($post->post_content) ) {

			$content = get_the_content();

			if ( is_archive() || empty($content) ) {

				global $current_jobs_shortcode;
				if ( isset($current_jobs_shortcode) && !empty($current_jobs_shortcode) ) {
					$content = $current_jobs_shortcode;
				} else {
					return true;
				}
			}
		}

		$show_map = knowhere_get_shortcode_param_value( $content, 'jobs', 'show_map', true );

		//if it is a string like "true" we need to remove the "
		if ( is_string($show_map) ) {
			$show_map = str_replace('"', '', $show_map);
		}

		return knowhere_string_to_bool($show_map);
	}
}

if ( !function_exists('knowhere_listings_page_shortcode_get_orderby_param') ) {
	function knowhere_listings_page_shortcode_get_orderby_param()
	{
		//if there is a page set in the Listings settings use that
		$listings_page_id = get_option('job_manager_jobs_page_id', false);
		if (false !== $listings_page_id) {
			$listings_page = get_post($listings_page_id);
			if (!is_wp_error($listings_page)) {
				return knowhere_jobs_shortcode_get_orderby_param($listings_page->post_content);
			}
		}

		return 'featured';
	}
}

if ( !function_exists('knowhere_jobs_shortcode_get_orderby_param') ) {
	function knowhere_jobs_shortcode_get_orderby_param($content = '')
	{
		if (empty($content)) {
			$content = get_the_content();
			if (empty($content)) {
				//check to see if we have a global shortcode - probably coming from a archive template
				global $current_jobs_shortcode;
				if (isset($current_jobs_shortcode) && !empty($current_jobs_shortcode)) {
					$content = $current_jobs_shortcode;
				} else {
					//if there is no content of the current page/post and no global shortcode
					return true;
				}
			}
		}
		//lets see if we have a orderby parameter
		$orderby = knowhere_get_shortcode_param_value($content, 'jobs', 'orderby', 'featured');
		//if it is a string like "true" we need to remove the "
		if (is_string($orderby)) {
			$orderby = str_replace('"', '', $orderby);
		}

		return $orderby;
	}
}

if ( !function_exists('knowhere_listings_page_shortcode_get_order_param') ) {
	function knowhere_listings_page_shortcode_get_order_param()
	{
		//if there is a page set in the Listings settings use that
		$listings_page_id = get_option('job_manager_jobs_page_id', false);
		if (false !== $listings_page_id) {
			$listings_page = get_post($listings_page_id);
			if (!is_wp_error($listings_page)) {
				return knowhere_jobs_shortcode_get_order_param($listings_page->post_content);
			}
		}

		//the default order
		return 'DESC';
	}
}

if ( !function_exists('knowhere_jobs_shortcode_get_order_param') ) {
	function knowhere_jobs_shortcode_get_order_param($content = '') {
		if (empty($content)) {
			$content = get_the_content();
			if (empty($content)) {
				//check to see if we have a global shortcode - probably coming from a archive template
				global $current_jobs_shortcode;
				if (isset($current_jobs_shortcode) && !empty($current_jobs_shortcode)) {
					$content = $current_jobs_shortcode;
				} else {
					//if there is no content of the current page/post and no global shortcode
					return true;
				}
			}
		}
		//lets see if we have a order parameter
		$order = knowhere_get_shortcode_param_value($content, 'jobs', 'order', 'DESC');
		//if it is a string like "true" we need to remove the "
		if (is_string($order)) {
			$order = str_replace('"', '', $order);
		}

		return $order;
	}
}

if ( !function_exists('knowhere_get_formatted_address') ) {
	function knowhere_get_formatted_address($post = null, $args = array())
	{
		global $knowhere_settings;

		if ( $post === null ) { global $post; }

		$classes = array();

		$default_args = array(
			'classes' => array( 'kw-icons-list' )
		);

		$args = wp_parse_args($args, $default_args);
		extract($args);

		$classes = implode(' ', $classes);

		$replace = array_map('esc_html', array(
			'{geolocation_street}' => trim(get_post_meta($post->ID, 'geolocation_street', true), ''),
			'{geolocation_street_number}' => trim(get_post_meta($post->ID, 'geolocation_street_number', true), ''),
			'{phone}' => trim(get_post_meta($post->ID, '_company_phone', true), ''),
			'{website}' => get_the_company_website($post),
			'{website_url}' => esc_url(get_post_meta($post->ID, '_company_website', true), ''),
			'{geolocation_city}' => trim(get_post_meta($post->ID, 'geolocation_city', true), ''),
			'{geolocation_country_short}' => trim(get_post_meta($post->ID, 'geolocation_country_short', true), ''),
		));

		$formats = '<ul class="' . $classes . '">';
if ( get_post_meta( $post->ID, '_job_location_visible', true ) != 1) {
		if ( $knowhere_settings['job-single-address'] && empty(get_post_meta( $post_id, '_job_location_visible', true ))) {
			if (!empty($replace['{geolocation_street_number}']) || !empty($replace['{geolocation_street}']) || !empty($replace['{geolocation_city}']) || !empty($replace['{geolocation_country_short}'])) {

				$formats .= '<li class="address__street" style="font-size:20px !important"><span class="lnr icon-map-marker" style="font-size:20px !important"></span>';

				if ( !empty($replace['{geolocation_street_number}']) ) {
					$formats .= '{geolocation_street_number}';
				}

				if ( !empty($replace['{geolocation_street}']) ) {
					$formats .= ' {geolocation_street}';
				}

				if ( !empty($replace['{geolocation_city}']) ) {
					$formats .= ' {geolocation_city}';
				}

				if (!empty($replace['{geolocation_country_short}'])) {
					$formats .= ' {geolocation_country_short}';
				}

				$formats .= '</li>';

			}
		}
} else {
	$formats .= '<li class="address__street" style="font-size:20px !important"><span class="lnr icon-map-marker" style="font-size:20px !important"></span>';
	if ( !empty($replace['{geolocation_city}']) ) {
			if (!empty(get_post_meta($post->ID, 'geolocation_postcode', true))) {
				$formats .= get_post_meta($post->ID, 'geolocation_postcode', true);
			}
					$formats .= ' {geolocation_city}';

				}

	$formats .= '</li>';
}

		if ( $knowhere_settings['job-single-phone'] ) {
			/*if (!empty($replace['{phone}'])) {
				$formats .= '<li class="address__phone"><span class="lnr icon-telephone"></span>{phone}</li>';
			}*/
		}


		if ( $knowhere_settings['job-single-website'] ) {
			if (!empty($replace['{website_url}']) || !empty($replace['{website}'])) {
				$formats .= '<li class="address__website"><span class="lnr icon-link"></span>';

				$formats .= '<a target="_blank" ';

				if (!empty($replace['{website_url}'])) {
					$formats .= ' href="{website_url}"';
				}

				$formats .= '>';

				if (!empty($replace['{website}'])) {
					$formats .= '{website}';
				}

				$formats .= '</a>';

				$formats .= '</li>';
			}
		}

		$formats .= '</ul>';

		$formatted_address = str_replace(array_keys($replace), $replace, $formats);
		$formatted_address = preg_replace('/  +/', ' ', trim($formatted_address));
		$formatted_address = preg_replace('/\n\n+/', "\n", $formatted_address);
		return $formatted_address;
	}
}

if ( !function_exists('knowhere_get_listings_page_url') ) {
	function knowhere_get_listings_page_url($default_link = null) {
		$listings_page_id = get_option('job_manager_jobs_page_id', false);

		if ( !empty($listings_page_id) ) {
			return get_permalink($listings_page_id);
		}
		if ( $default_link !== null ) {
			return $default_link;
		}
		return get_post_type_archive_link('job_listing');
	}
}

if ( !function_exists('knowhere_get_job_meta_value') ) {
	if ( !function_exists('knowhere_get_job_meta_value') ) {
		function knowhere_get_job_meta_value($meta_key, $term) {

			$value = '';
			$t_id = $term->term_id;
			$term_meta = get_option("taxonomy_$t_id");

			if ( $term_meta[$meta_key] ) {
				$value = esc_attr($term_meta[$meta_key]);
			}

			return $value;
		}

	}
}

if ( !function_exists('knowhere_has_integration') ) {
	function knowhere_has_integration($integration) {
		return array_key_exists($integration, Knowhere_Integration::get_integrations());
	}
}


if ( !function_exists('knowhere_bg_color_label') ) {
	function knowhere_bg_color_label($term) {
		$styles = '';
		$css = array();

		if ( is_array($term) ) {
			$term = $term[0];
		}

		$bg_color_label = knowhere_get_job_meta_value( 'bg_color_label', $term );

		if ( !empty($bg_color_label) ) {
			$css[] = "background-color: $bg_color_label;";
		}

		if ( !empty($css) ) {
			$styles = 'style="' . implode(' ', $css) . '"';
		}

		?><div class="kw-label-job" <?php echo sprintf( '%s', $styles ) ?>><?php echo sprintf('%s', $term->name) ?></div><?php
	}
}

if ( !function_exists('knowhere_get_price_format') ) {
	function knowhere_get_price_format() {
		global $knowhere_settings;
		$currency_pos = $knowhere_settings['currency_pos'];
		$format = '%1$s%2$s';

		switch ( $currency_pos ) {
			case 'left' :
				$format = '%1$s%2$s';
			break;
			case 'right' :
				$format = '%2$s%1$s';
			break;
		}

		return apply_filters( 'knowhere_price_format', $format, $currency_pos );
	}
}

if ( !function_exists('knowhere_price_range_output') ) {
	function knowhere_price_range_output( $post_id, $view = 'list' ) {

		$output = '';
		$price_range_min = knowhere_get_invoice_price( '', get_post_meta( $post_id, '_job_price_range_min', true ) );
		$price_range_max = knowhere_get_invoice_price( '', get_post_meta( $post_id, '_job_price_range_max', true ) );
		$price_range_min = get_post_meta( $post_id, '_job_price_range_min', true )."€";
		if ( $price_range_min && !$price_range_max ) {
			$output = sprintf('%s', $price_range_min);
		}

		if ( !$price_range_min && $price_range_max ) {
			$output = sprintf('%s', $price_range_max);
		}

		if ( $price_range_min && $price_range_max ) {
			$output = sprintf( '%s - %s', $price_range_min, $price_range_max );
		}

		if ( !$output ) return '';

		if ( $view == 'list' ): ?>
			<li><span class="lnr icon-wallet"></span><?php echo esc_html($output) ?></li>
		<?php else: ?>
			<div class="kw-price-range-label"><span class="lnr icon-wallet"></span>
				<?php esc_html_e('Price Range', 'knowherepro') ?>: <strong><?php echo esc_html($output) ?></strong>
			</div>
		<?php endif;
	}
}

if ( !function_exists('knowhere_job_listing_header_rating') ) {

	function knowhere_job_listing_header_rating($id) {

		if ( !class_exists('RWP_Reviewer') ) return '';

		$rate = array();
		global $knowhere_settings;
		$template = $knowhere_settings['job-single-review-template'];

		if ( !$template ) { return $rate; }

		$likes = get_post_meta( $id, 'rwp_likes', true );
		$likes = is_array($likes) ? $likes : array();

		if ( !empty($likes) ) {
			foreach ( $likes as $like ) {

				$ratings_scores = RWP_Reviewer::get_ratings_single_scores($id, $like['review_id'], $template);

				if ( empty($ratings_scores) ) return;

				$data = RWP_Reviewer::get_users_overall_score($ratings_scores, $id, $like['review_id']);

				if (isset($data['count']) && $data['count'] > 0 && $data['score'] > 0) {
					$rate['score'] = $data['score'];
					$rate['count'] = $data['count'];
				}

			}
		}

		$rate = is_array($rate) ? $rate : array();
		return $rate;
	}

}

if ( !function_exists('knowhere_job_listing_rating') ) {
	function knowhere_job_listing_rating() {

		if ( !class_exists('RWP_Reviewer') ) return;

		global $knowhere_settings;
		$template = $knowhere_settings['job-single-review-template'];

		if ( !$template ) return;

		$likes = get_post_meta(get_the_ID(), 'rwp_likes', true);
		$likes = is_array($likes) ? $likes : array();
		$rate = array();

		if ( !empty($likes) ) {
			foreach ( $likes as $like ) {

				$ratings_scores = RWP_Reviewer::get_ratings_single_scores(get_the_ID(), $like['review_id'], $template);

				if ( empty($ratings_scores) ) return;

				$data = RWP_Reviewer::get_users_overall_score($ratings_scores, get_the_ID(), $like['review_id']);

				if ( isset($data['count']) && $data['count'] > 0 && $data['score'] > 0 ) {
					$rate['score'] = $data['score'];
				}

			}
		}

		$rate = is_array($rate) ? $rate : array(); ?>

		<?php if ( !empty($rate) ) : ?>
			<div class="kw-listing-item-rating kw-rating" data-rating="<?php echo absint($rate['score']) ?>"></div>
		<?php endif;

	}
}

if ( !function_exists('knowhere_job_single_gallery') ) {
	function knowhere_job_single_gallery( $args = array() ) {

		$type = $echo = '';

		$defaults = array(
			'type' => '',
			'echo' => false
		);

		$args = wp_parse_args( $args, $defaults );

		extract($args);

		$photos = knowhere_get_listing_gallery_ids();

		if ( $photos ) :

			$hidden_gallery = get_post_meta( get_the_ID(), 'knowhere_job_hidden_gallery', true );

			if ( $hidden_gallery ) return false;

			$count = count($photos);

			ob_start(); ?>

			<?php if ( $count >=1 || $count >= 2 ): ?>

				<div class="kw-listing-item-media">

					<div class="kw-slideshow-with-thumbs">

						<?php if ( $count >= 1 ): ?>

							<div id="kw-slideshow" class="kw-slideshow <?php if ( $count >= 2 ): ?>owl-carousel<?php endif; ?>">

								<?php foreach ( $photos as $key => $photo_id ):
									$src = wp_get_attachment_image_src( $photo_id, 'knowhere-slideshow-image' );
									$full = wp_get_attachment_image_src( $photo_id, '' );
									?>
									<a href="<?php echo esc_url($full[0]); ?>" class="kw-popup-gallery"><img src="<?php echo esc_url($src[0]); ?>" alt=""/></a>
								<?php endforeach; ?>

							</div><!--/ .kw-slideshow-->

							<?php if ( $count >= 2 ): ?>

								<div data-sync="#kw-slideshow" class="kw-slideshow-thumbs <?php echo sanitize_html_class($type) ?> owl-carousel">

									<?php $i = 1; ?>

									<?php foreach ( $photos as $key => $photo_id ):
										$src = wp_get_attachment_image_src($photo_id, array(100, 100)); ?>
										<div class="kw-slideshow-thumb <?php if ( $i == 1 ): ?>kw-active<?php endif; ?>">
											<img src="<?php echo esc_url($src[0]); ?>" alt=""/>
										</div>
										<?php $i++; ?>
									<?php endforeach; ?>

								</div><!--/ .kw-slideshow-thumbs-->

							<?php endif; ?>

						<?php endif; ?>

					</div><!--/ .kw-slideshow-with-thumbs-->

				</div><!--/ .kw-listing-item-media-->

			<?php endif; ?>

			<?php

			if ( $echo ) {
				echo ob_get_clean();
			} else {
				return ob_get_clean();
			}

		endif;

	}
}

if ( !function_exists('knowhere_get_listing_term_ids') ) {
	function knowhere_get_listing_term_ids( $listing_id, $taxonomy ) {
		$terms = get_the_terms( $listing_id, $taxonomy );
		return (empty($terms) || is_wp_error($terms)) ? array() : wp_list_pluck($terms, 'term_id');
	}
}

if ( !function_exists('knowhere_get_related_listings') ) {
	function knowhere_get_related_listings( $id, $limit = 3, $exclude_ids = array() ) {
		$id = absint($id);
		$exclude_ids = array_merge( array(0, $id), $exclude_ids );
		$limit = $limit > 0 ? $limit : 3;

		$query_args = array(
			'post_type'   => 'job_listing',
			'post_status' => 'publish',
			'posts_per_page'   => $limit,
			'post__not_in' => $exclude_ids
		);

		$query_args['tax_query'][] = array(
			'taxonomy'         => 'job_listing_category',
			'field'            => 'term_id',
			'terms'            => array_values( knowhere_get_listing_term_ids( $id, 'job_listing_category' ) )
		);

		$query_args['tax_query'][] = array(
			'taxonomy'         => 'job_listing_tag',
			'field'            => 'term_id',
			'terms'            => array_values( knowhere_get_listing_term_ids( $id, 'job_listing_tag' ) ),
		);

		return new WP_Query( $query_args );
	}
}

if ( !function_exists('knowhere_job_single_related') ) {

	function knowhere_job_single_related( $job_id = null ) {

		global $post, $knowhere_settings;
		$listing_id = $post->ID;
		if ( $job_id ) { $listing_id = $job_id; }

		if ( !$knowhere_settings['job-related'] ) return;

		$title = $knowhere_settings['job-related-title'];
		$limit = $knowhere_settings['job-related-count'];
		$columns = $knowhere_settings['job-related-columns'];
		$type = $knowhere_settings['job-related-style'];
		$list_view = $knowhere_settings['job-related-list-view'] ? true : false;
		$card_image = 'knowhere-card-image';
		$css_classes = array( 'kw-listings', $type );

		if ( !$list_view ) { $css_classes[] = 'kw-cols-' . $columns; }

		if ( $list_view ) {
			$css_classes[] = 'kw-list-view';
		} else {
			$css_classes[] = 'kw-grid-view';
		}

		$css_class = preg_replace( '/\s+/', ' ', implode( ' ', array_filter( $css_classes ) ) );

		$related_listings = knowhere_get_related_listings( $listing_id, $limit );

		if ( $related_listings->have_posts() ) : ?>

			<?php if ( $title ): ?>
				<h3 class="kw-section-title kw-type-2"><?php echo esc_html($title) ?></h3>
			<?php endif; ?>

			<div class="<?php echo esc_attr( trim($css_class) ) ?>">

				<?php while ( $related_listings->have_posts() ) : $related_listings->the_post(); ?>
						
	<?php 
	$adresse = get_post_meta( get_the_ID(), '_job_location', true );
	if(!empty($adresse)) {
		preg_match("/^(.*)(\d{5})(.*)$/", $adresse, $elems);
	}  
	$product_id = get_post_meta( get_the_ID(), '_id_product', true );
	
	$aDates = get_post_meta( $product_id, '_wc_booking_availability', true );



	
	foreach ($aDates as $date_activite) {
		if ( $date_activite['type'] == "custom") {
			if(strtotime($date_activite['from']) > strtotime(date("d-m-Y")) && empty($startDate)) {
				$startDate = $date_activite['from'];
			} 
		} else {
			if ($date_activite['type'] == "time:1") $startDate = date('d-m-Y', strtotime('next monday'));
			if ($date_activite['type'] == "time:2") $startDate = date('d-m-Y', strtotime('next tuesday'));
			if ($date_activite['type'] == "time:3") $startDate = date('d-m-Y', strtotime('next wednesday'));
			if ($date_activite['type'] == "time:4") $startDate = date('d-m-Y', strtotime('next thursday'));
			if ($date_activite['type'] == "time:5") $startDate = date('d-m-Y', strtotime('next friday'));
			if ($date_activite['type'] == "time:6") $startDate = date('d-m-Y', strtotime('next saturday'));
			if ($date_activite['type'] == "time:7") $startDate = date('d-m-Y', strtotime('next sunday'));
		}
		
	}

	$user_data = get_userdata($post->post_author);
	$username = $user_data->display_name;

					$terms = get_the_terms( get_the_ID(), 'job_listing_category' );

					$listing_classes = array(
						'kw-listing-item-wrap'
					);

					$post_image_src = knowhere_get_post_image_src( $post->ID, $card_image );

					$listing_class = preg_replace( '/\s+/', ' ', implode( ' ', array_filter( $listing_classes ) ) );
					?>

					<div class="<?php echo esc_attr( trim($listing_class) ) ?>">

						<?php if ( $type == 'kw-type-1' ): ?>

							<article <?php job_listing_class('kw-listing-item'); ?> data-longitude="<?php echo esc_attr( $post->geolocation_long ); ?>" data-latitude="<?php echo esc_attr( $post->geolocation_lat ); ?>" itemscope itemtype="http://schema.org/Event">


	<meta itemprop="name" content="<?php echo esc_attr( $post->post_title ); ?>" />
	<meta  itemprop="description" content="<?php echo  strip_tags(get_the_content()); ?>" />
			
	<div itemprop="location" itemscope itemtype="http://schema.org/Place">
		<meta itemprop="name" content="<?php echo esc_attr( $post->post_title ); ?>" />
		<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress" >
			<meta itemprop="name" content="<?php echo esc_attr( $post->post_title ); ?>" />
			<meta itemprop="postalCode" content="<?php echo get_post_meta( $post->ID, 'geolocation_postcode', true ); ?>" />
			<meta itemprop="streetAddress" content="<?php echo get_post_meta( $post->ID, 'geolocation_street', true ); ?>" />
			<meta itemprop="addressLocality" content="<?php echo get_post_meta( $post->ID, 'geolocation_city', true ); ?>" />
		</div>
	</div>
	<div itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer">
		<meta itemprop="priceCurrency" content="EUR"/>
		<meta itemprop="price" content="<?php echo  get_post_meta( $post->ID, '_job_price_range_min', true ); ?>"/>
		<meta itemprop="lowprice" content="<?php echo  get_post_meta( $post->ID, '_job_price_range_min', true ); ?>"/>
		<meta itemprop="availability" content="In stock"/>
		<meta itemprop="offerCount" content="<?php echo  get_post_meta( $post->ID, '_nb_personnes_max', true ); ?>"/>
		<meta itemprop="url" content="<?php echo  get_permalink(); ?>"/>
		<meta itemprop="validFrom" content="<?php echo $startDate; ?>"/>
	</div>

	<div itemprop="performer" itemscope="" itemtype="http://schema.org/Person">
		<meta itemprop="name" content='<?php echo $username; ?>'>
	</div>
	<meta itemprop="startDate" content="<?php echo  $startDate; ?>"/>
	<meta itemprop="endDate" content="<?php echo  $startDate; ?>"/>
	<meta itemprop="image" content="<?php echo  get_the_post_thumbnail_url($post->ID,'full'); ?>"/>


								<?php if ( !empty($post_image_src) ): ?>

									<!-- - - - - - - - - - - - - - Media - - - - - - - - - - - - - - - - -->

									<div class="kw-listing-item-media">

										<?php knowhere_label_hours_output( get_the_ID() ); ?>

										<?php knowhere_label_featured( get_the_ID() ); ?>

										<a href="<?php the_job_permalink(); ?>" class="kw-listing-item-thumbnail">
											<img src="<?php echo esc_url($post_image_src); ?>" alt="">
										</a>

										<ul class="kw-listing-card-meta">
											<?php if ( ! is_wp_error( $terms ) && ( is_array( $terms ) || is_object( $terms ) ) ) { ?>

												<?php $i = 0; $j = 0; ?>

												<?php foreach ( $terms as $term ) {
													$icon_url      = knowhere_get_term_icon_url( $term->term_id );
													$attachment_id = knowhere_get_term_icon_id( $term->term_id );

													if ( $j > 3 ) continue;

													if ( empty( $icon_url ) ) {
														continue;
													} ?>


													<li class="kw-listing-term-list">
														<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" title="<?php echo sprintf('%s', $term->name ) ?>" class="kw-listing-item-icon">
															<?php knowhere_display_icon_or_image( $icon_url, '', true, $attachment_id ); ?>
														</a>
													</li>

													<?php $i++; $j++; ?>

												<?php } ?>
											<?php } ?>

											<li>
												<a href="<?php the_job_permalink(); ?>" class="kw-listing-item-like">
													<span class="lnr icon-heart"></span>
												</a>
											</li>

										</ul><!--/ .kw-listing-card-meta-->

									</div>

									<!-- - - - - - - - - - - - - - End of Media - - - - - - - - - - - - - - - - -->

								<?php endif; ?>

								<!-- - - - - - - - - - - - - - Description - - - - - - - - - - - - - - - - -->

								<div class="kw-listing-item-info">

									<header class="kw-listing-item-header">

										<h3 class="kw-listing-item-title">
											<a href="<?php the_job_permalink(); ?>"><?php the_title(); ?></a>
										</h3>

										<div class="kw-card-rating">
											<?php knowhere_job_listing_rating() ?>
										</div>

									</header>

									<ul class="kw-listing-item-data kw-icons-list">
										<li class="kw-listing-item-location"><span class="lnr icon-map-marker"></span><?php echo get_the_job_location(); ?></li>
										<li class="kw-listing-item-phone"><span class="lnr icon-telephone"></span><?php echo get_post_meta( $post->ID, '_company_phone', true); ?></li>
									</ul>

								</div>

								<!-- - - - - - - - - - - - - - End of Description - - - - - - - - - - - - - - - - -->

							</article>

						<?php elseif ( $type == 'kw-type-5' ): ?>

							<article <?php job_listing_class('kw-listing-item'); ?> data-longitude="<?php echo esc_attr( $post->geolocation_long ); ?>" data-latitude="<?php echo esc_attr( $post->geolocation_lat ); ?>">

								<!-- - - - - - - - - - - - - - Media - - - - - - - - - - - - - - - - -->

								<?php if ( !empty($post_image_src) ): ?>

									<!-- - - - - - - - - - - - - - Media - - - - - - - - - - - - - - - - -->

									<div class="kw-listing-item-media">

										<?php knowhere_label_hours_output( get_the_ID() ); ?>

										<?php knowhere_label_featured( get_the_ID() ); ?>

										<a href="<?php the_job_permalink(); ?>" class="kw-listing-item-thumbnail">
											<img src="<?php echo esc_url($post_image_src); ?>" alt="">
										</a>

										<a href="<?php the_job_permalink(); ?>" class="kw-listing-item-like">
											<span class="lnr icon-heart"></span>
										</a>

										<?php $photos = knowhere_get_listing_gallery_ids(); ?>

										<?php if ( $photos ): ?>
											<div class="kw-listing-item-photo-amount"><?php echo absint(count($photos)) ?></div>
										<?php endif; ?>

									</div><!--/ .kw-listing-item-media-->

									<!-- - - - - - - - - - - - - - End of Media - - - - - - - - - - - - - - - - -->

								<?php endif; ?>

								<!-- - - - - - - - - - - - - - Description - - - - - - - - - - - - - - - - -->

								<div class="kw-listing-item-info kw-listing-style-5">

									<header class="kw-listing-item-header">

										<?php if ( !is_wp_error( $terms ) && ( is_array( $terms ) || is_object( $terms ) ) ) { ?>

											<div class="kw-listing-item-categories">
												<?php echo get_the_term_list( get_the_ID(), 'job_listing_category', '', ', ', '' ); ?>
											</div>

										<?php } ?>

										<h3 class="kw-listing-item-title">
											<a href="<?php the_job_permalink(); ?>"><?php the_title(); ?></a>
										</h3>

									</header>

									<?php if ( knowhere_get_the_company_description() ) : ?>
										<div class="kw-listing-item-description">
											<?php knowhere_the_company_description(); ?>
										</div>
									<?php endif; ?>

									<?php echo knowhere_the_job_publish_date(); ?>

									<footer class="kw-listing-item-footer">

										<div class="kw-xs-table-row row">

											<div class="col-xs-5">
												<?php if ( knowhere_get_invoice_price( get_the_ID(), get_post_meta( get_the_ID(), '_job_price_range_min', true ) ) ): ?>
													<strong class="kw-listing-item-price">
														<?php echo knowhere_get_invoice_price( get_the_ID(), get_post_meta( get_the_ID(), '_job_price_range_min', true ) ) ?>
													</strong>
												<?php endif; ?>
											</div>

											<div class="col-xs-7 kw-right-edge">
												<?php $job_location = get_post_meta( get_the_ID(), 'geolocation_city', true); ?>
												<?php if ( !empty($job_location) ): ?>
													<ul class="kw-listing-item-data kw-icons-list">
														<li><span class="lnr icon-map-marker"></span><?php echo trim( $job_location ); ?></li>
													</ul>
												<?php endif; ?>
											</div>

										</div>

									</footer><!--/ .kw-listing-item-footer-->

								</div><!--/ .kw-listing-item-info-->

								<!-- - - - - - - - - - - - - - End of Description - - - - - - - - - - - - - - - - -->

							</article>

						<?php endif; ?>

					</div><!--/ .kw-listing-item-wrap-->

				<?php endwhile; ?>

			<?php wp_reset_postdata();

		endif;

	}

}

	function next_day_related($day_number)
	{
	  for ($i = 2; $i <= 8; $i++)
	  {
	    $next_day = mktime(0,0,0, date("m"), date("d")+$i, date("Y"));
	    if(date("w",$next_day)==$day_number)
	    {
	      $XDate = getdate($next_day);
	      $next_day_fund = sprintf('%02d', $XDate['mday']).'-'.sprintf('%02d', 
	      $XDate['mon']).'-'.sprintf('%02d', $XDate['year']);
	    }
	  }
	  return $next_day_fund;
	}

if ( !function_exists('knowhere_job_single_details') ) {
	function knowhere_job_single_details($job_id = null)
	{

		global $post;
		$post_id = $post->ID;

		if ( $job_id ) {
			$post_id = $job_id;
		}

		$details = get_post_meta($post_id, 'knowhere_job_details', true);

		if ( isset($details) && !empty($details) && count($details) > 0 ): ?>

			<div class="kw-box" id="kw-details">

				<h2><?php echo esc_html__('Details', 'knowherepro') ?></h2>

				<dl class="kw-def-list">

					<?php if ( isset($details) && !empty($details) && count($details) > 0 ): ?>

						<?php foreach ( $details as $id => $field ): ?>

							<?php if ( isset($field['title'] ) || isset($field['content']) ): ?>
								<dt><?php echo sprintf('%s', $field['title']) ?>:</dt>
								<dd><?php echo do_shortcode(sprintf('%s', $field['content'])) ?></dd>
							<?php endif; ?>

						<?php endforeach; ?>

					<?php endif; ?>

				</dl><!--/ .kw-def-list-->

			</div><!--/ .kw-box-->

		<?php endif;

	}
}


if ( !function_exists('knowhere_job_single_reviewer') ) {
	function knowhere_job_single_reviewer($job_id = null)
	{

		if (class_exists('RWP_Reviewer')) {

			$post_id = get_the_ID();

			if ($job_id) {
				$post_id = $job_id;
			}

			$hidden_reviews = get_post_meta( $post_id, 'knowhere_job_hidden_reviews', true );

			if ($hidden_reviews) return;

			global $knowhere_settings;

			$template = $knowhere_settings['job-single-review-template']; ?>

			<?php if ($template): ?>

				<div class="kw-single-box" id="kw-reviews">
					<?php echo do_shortcode('[rwp-review-ratings id="-1" post="' . $post_id . '" template="' . $template . '"]'); ?>
				</div>

				<div class="kw-single-box" id="kw-write-review">
					<?php echo do_shortcode('[rwp-review-form id="-1" post="' . $post_id . '" template="' . $template . '"]'); ?>
				</div>

			<?php endif; ?>

		<?php }

	}
}

if ( !function_exists('knowhere_job_listing_company') ) {
	function knowhere_job_listing_company($post)
	{
		$company_name = get_the_company_name($post);
		$company_website = get_the_company_website($post);
		?>

		<?php if ( $company_website ): ?>

		<?php if ( strlen($company_name) !== 0 ): ?>
			<li><span class="lnr icon-briefcase"></span>
				<a target="_blank" href="<?php echo esc_url($company_website) ?>"><?php the_company_name() ?></a>
			</li>
		<?php endif; ?>

	<?php else: ?>

		<?php if ( strlen($company_name) !== 0 ): ?>
			<li><span class="lnr icon-briefcase"></span><?php the_company_name() ?></li>
		<?php endif; ?>

	<?php endif;

	}
}

/* Get Currency */

if ( !function_exists('knowhere_get_currency') ) {
	function knowhere_get_currency() {
		global $knowhere_settings;
		$default_currency = $knowhere_settings['currency_symbol'];
		if ( empty($default_currency) ) {
			return esc_html__( '$' , 'knowherepro' );
		}
		return $default_currency;
	}
}

/* Get Price */

if ( !function_exists('knowhere_get_invoice_price') ) {
	function knowhere_get_invoice_price ( $id = null, $invoice_price = '' ) {

		global $knowhere_settings;

		$invoice_price = doubleval( $invoice_price );
		$invoice_currency = $price_postfix = '';
		$currency_pos = knowhere_get_price_format();

		if ( $knowhere_settings['job-type-fields'] == 'property' ) {

			if ( $id ) {
				$invoice_price = get_post_meta( $id, '_prop_price', true);
				$price_postfix = get_post_meta( $id, '_prop_postfix', true );
			}

		} elseif ( $knowhere_settings['job-type-fields'] == 'listing' ) {

			$output = '';
			$price_range_min = get_post_meta( $id, '_job_price_range_min', true );
			$price_range_max = get_post_meta( $id, '_job_price_range_max', true );

			if ( $price_range_min && !$price_range_max ) {
				$output = sprintf('%s', $price_range_min);
			}

			if ( !$price_range_min && $price_range_max ) {
				$output = sprintf('%s', $price_range_max);
			}

			if ( $price_range_min && $price_range_max ) {
				$output = sprintf( '%s - %s', $price_range_min, $price_range_max );
			}

			if ( $output ) {

				$pos = strpos( $output, '-' );
				if ( $pos ) {
					$output = explode('-', $output);
				}

				$invoice_price = $output;
			}
		}

		if ( $invoice_price ) {

			$invoice_currency = knowhere_get_currency();
			$thousands_separator = $knowhere_settings['thousands_separator'];
			$decimal_point_separator = $knowhere_settings['decimal_point_separator'];

			if ( is_array($invoice_price) ) {

				$final_price_array = array();

				foreach( $invoice_price as $price ) {
					$final_price_array[] = sprintf($currency_pos, $invoice_currency, number_format ( absint($price) , 2 , $decimal_point_separator , $thousands_separator ));
				}

				return implode(' - ', $final_price_array);

			} else {
				$final_price = number_format ( $invoice_price , 2 , $decimal_point_separator , $thousands_separator );
			}

			if ( !empty($price_postfix) ) {
				$price_postfix = '&#47;' . $price_postfix;
			} else {
				$price_postfix = '';
			}

			return sprintf( $currency_pos, $invoice_currency, $final_price ) . $price_postfix;

		}

		return $invoice_currency;
	}
}

if ( !function_exists('knowhere_get_property_size') ) {
	function knowhere_get_property_size( $size ) {
		global $knowhere_settings;
		$size = doubleval( $size );

		$area_prefix_default = $knowhere_settings['area_prefix_default'];
		if( $area_prefix_default == 'SqFt' ) {
			$area_prefix_default = $knowhere_settings['unit_sqft_text'];
		} elseif( $area_prefix_default == 'm?' ) {
			$area_prefix_default = $knowhere_settings['unit_square_meter_text'];
		}

		if ( $size ) {
			$decimals = 0;
			$thousands_separator = $knowhere_settings['thousands_separator'];
			$decimal_point_separator = $knowhere_settings['decimal_point_separator'];
			$final_size = number_format ( $size , $decimals , $decimal_point_separator , $thousands_separator );
			return $final_size . ' ' . $area_prefix_default;
		}

		return '';

	}
}

if ( !function_exists('knowhere_get_property_features') ) {
	function knowhere_get_property_features( $id ) {
		$bedrooms = get_post_meta( $id, '_bedrooms', true );
		$bathrooms = get_post_meta( $id, '_bathrooms', true );
		$property_size = get_post_meta( $id, '_property_size', true ); ?>

		<?php if ( empty($bedrooms) || empty($bathrooms) || empty($property_size) ) return; ?>

		<ul class="kw-dotted-list">
			<?php if ( !empty($bedrooms) ): ?>
				<li><?php echo absint($bedrooms) . esc_html__('bds', 'knowherepro') ?></li>
			<?php endif; ?>

			<?php if ( !empty($bathrooms) ): ?>
				<li><?php echo absint($bathrooms) . esc_html__('ba', 'knowherepro') ?></li>
			<?php endif; ?>

			<?php if ( !empty($property_size) ): ?>
				<li><?php echo knowhere_get_property_size($property_size) ?></li>
			<?php endif; ?>
		</ul><!--/ .kw-dotted-list-->

		<?php
	}
}

if ( !function_exists('knowhere_the_job_publish_date') ) {
	function knowhere_the_job_publish_date( $post = null ) {
		$date_format = get_option( 'job_manager_date_format' );

		if ( $date_format === 'default' ) {
			$display_date = esc_html__( 'Posted on ', 'knowherepro' ) . get_post_time( get_option( 'date_format' ) );
		} else {
			$display_date = sprintf( esc_html__( '%s ago', 'knowherepro' ), human_time_diff( get_post_time( 'U' ), current_time( 'timestamp' ) ) );
		}

		echo '<time class="kw-listing-item-date" datetime="' . get_post_time( 'Y-m-d' ) . '">' . $display_date . '</time>';
	}
}

if ( !function_exists('knowhere_property_data_features_output') ) {
	function knowhere_property_data_features_output() {

		$id = get_the_ID();

		$bedrooms = get_post_meta( $id, '_bedrooms', true );
		$bathrooms = get_post_meta( $id, '_bathrooms', true );
		$property_size = get_post_meta( $id, '_property_size', true );
		$area_size = get_post_meta( $id, '_area_size', true );
		$garages = get_post_meta( $id, '_garages', true );
		$garages_size = get_post_meta( $id, '_garages_size', true );
		$rooms = get_post_meta( $id, '_rooms', true );
		$last_renovation = get_post_meta( $id, '_last_renovation', true );
		$open_house_date = get_post_meta( $id, '_open_house_date', true );
		$year_of_construction = get_post_meta( $id, '_year_of_construction', true );
		$property_id = get_post_meta( $id, '_property_id', true );
		$postal_code = get_post_meta( $id, '_postal_code', true );
		?>

		<ul class="kw-datalist kw-cols-2">

			<?php if ( !empty( $bedrooms ) ): ?>
				<li class="kw-datalist-item">
					<span class="lnr icon-bed"></span>
					<div class="kw-datalist-dt"><?php esc_html_e('Bedrooms', 'knowherepro') ?></div>
					<div class="kw-datalist-dd"><?php echo absint($bedrooms) . ' ' . esc_html__('bds', 'knowherepro') ?></div>
				</li>
			<?php endif; ?>

			<?php if ( !empty( $property_size ) ): ?>
				<li class="kw-datalist-item">
					<span class="lnr icon-home5"></span>
					<div class="kw-datalist-dt"><?php esc_html_e('Property Size', 'knowherepro') ?></div>
					<div class="kw-datalist-dd"><?php echo knowhere_get_property_size($property_size) ?></div>
				</li>
			<?php endif; ?>

			<?php if ( !empty( $area_size ) ): ?>
				<li class="kw-datalist-item">
					<span class="lnr icon-home5"></span>
					<div class="kw-datalist-dt"><?php esc_html_e('Area Size', 'knowherepro') ?></div>
					<div class="kw-datalist-dd"><?php echo knowhere_get_property_size($area_size) ?></div>
				</li>
			<?php endif; ?>

			<?php if ( !empty( $bathrooms ) ): ?>
				<li class="kw-datalist-item">
					<span class="lnr icon-bathtub"></span>
					<div class="kw-datalist-dt"><?php esc_html_e('Bathrooms', 'knowherepro') ?></div>
					<div class="kw-datalist-dd"><?php echo absint($bathrooms) . ' ' . esc_html__('ba', 'knowherepro') ?></div>
				</li>
			<?php endif; ?>

			<?php if ( !empty( $garages ) ): ?>
				<li class="kw-datalist-item">
					<span class="lnr icon-car2"></span>
					<div class="kw-datalist-dt"><?php esc_html_e('Garages', 'knowherepro') ?></div>
					<div class="kw-datalist-dd"><?php echo absint($garages) ?></div>
				</li>
			<?php endif; ?>

			<?php if ( !empty( $garages_size ) ): ?>
				<li class="kw-datalist-item">
					<span class="lnr icon-car"></span>
					<div class="kw-datalist-dt"><?php esc_html_e('Garages Size', 'knowherepro') ?></div>
					<div class="kw-datalist-dd"><?php echo knowhere_get_property_size($garages_size) ?></div>
				</li>
			<?php endif; ?>

			<?php if ( !empty( $rooms ) ): ?>
				<li class="kw-datalist-item">
					<span class="lnr icon-border-all"></span>
					<div class="kw-datalist-dt"><?php esc_html_e('Rooms', 'knowherepro') ?></div>
					<div class="kw-datalist-dd"><?php echo absint($rooms) ?></div>
				</li>
			<?php endif; ?>

			<?php if ( !empty( $property_id ) ): ?>
				<li class="kw-datalist-item">
					<span class="lnr icon-register"></span>
					<div class="kw-datalist-dt"><?php esc_html_e('Property ID', 'knowherepro') ?></div>
					<div class="kw-datalist-dd"><?php echo esc_html($property_id) ?></div>
				</li>
			<?php endif; ?>

			<?php if ( !empty( $postal_code ) ): ?>
				<li class="kw-datalist-item">
					<span class="lnr icon-mailbox-full"></span>
					<div class="kw-datalist-dt"><?php esc_html_e('Postal code', 'knowherepro') ?></div>
					<div class="kw-datalist-dd"><?php echo esc_html($postal_code) ?></div>
				</li>
			<?php endif; ?>

			<?php if ( !empty( $year_of_construction) ): ?>
				<li class="kw-datalist-item">
					<span class="lnr icon-shovel"></span>
					<div class="kw-datalist-dt"><?php esc_html_e('Year of Construction', 'knowherepro') ?></div>
					<div class="kw-datalist-dd"><?php echo absint($year_of_construction) ?></div>
				</li>
			<?php endif; ?>

			<?php if ( !empty( $open_house_date) ): ?>
				<li class="kw-datalist-item">
					<span class="lnr icon-alarm-ringing"></span>
					<div class="kw-datalist-dt"><?php esc_html_e('Open House date', 'knowherepro') ?></div>
					<div class="kw-datalist-dd"><?php echo esc_html($open_house_date) ?></div>
				</li>
			<?php endif; ?>

			<?php if ( !empty( $last_renovation) ): ?>
				<li class="kw-datalist-item">
					<span class="lnr icon-wall"></span>
					<div class="kw-datalist-dt"><?php esc_html_e('Last Renovation', 'knowherepro') ?></div>
					<div class="kw-datalist-dd"><?php echo absint($last_renovation) ?></div>
				</li>
			<?php endif; ?>

		</ul><!--/ .kw-datalist-->

		<?php
	}

}

if ( !function_exists('knowhere_get_listing') ) {
	function knowhere_get_listing($post = null)
	{
		$factory = new Knowhere_Listing_Factory();
		$listing = $factory->get_listing($post);
		return $listing;
	}
}

if ( !function_exists('knowhere_get_current_time') ) {
	function knowhere_get_current_time( $format, $gmt_offset ) {
		return date( $format, time() + ( intval( $gmt_offset ) * HOUR_IN_SECONDS ) );
	}
}

if ( !function_exists('knowhere_sanitize_hours') ) {
	function knowhere_sanitize_hours( $datas ) {
		// If no data, return empty data block.
		if ( ! is_array( $datas ) || ! $datas ) {
			return array();
		}

		$days = knowhere_get_days();

		if ( isset( $datas[1] ) ) {
			$old_datas = array();
			foreach ( $datas as $day_index => $data ) {
				$day = $days[ $day_index ];
				$old_datas[ $day ][0] = array(
					'start'  => isset( $data['start'] ) ? $data['start'] : '',
					'end' => isset( $data['end'] ) ? $data['end'] : '',
				);
			}
			$datas = $old_datas;
		}


		// Sanitize datas.
		$new_datas = array();
		foreach ( $datas as $day => $hours ) {

			if ( in_array( $day, $days ) && is_array( $hours ) ) {
				$i = 0;
				foreach ( $hours as $hour ) {
					if ( isset( $hour['start'], $hour['end'] ) && $hour['start'] && $hour['end'] ) {
						$new_datas[ $day ][ $i ] = $hour;
					}
					$i++;
				}
			}
		}

		return $new_datas;
	}
}


if ( !function_exists('knowhere_get_days') ) {
	function knowhere_get_days()
	{
		$days_name = array(
			0 => 'sun',
			1 => 'mon',
			2 => 'tue',
			3 => 'wed',
			4 => 'thu',
			5 => 'fri',
			6 => 'sat',
		);

		$num_days = knowhere_get_days_of_week();

		// Format days.
		$days = array();
		foreach ($num_days as $num_day) {
			$days[$num_day] = $days_name[$num_day];
		}

		return $days;
	}
}

if ( !function_exists('knowhere_get_days_of_week') ) {
	function knowhere_get_days_of_week() {
		$days = array(0, 1, 2, 3, 4, 5, 6);
		$start = get_option( 'start_of_week' );

		$first = array_splice( $days, $start, count( $days ) - $start );
		$second = array_splice( $days, 0, $start );
		$days = array_merge( $first, $second );

		return $days;
	}
}

if ( !function_exists('knowhere_label_featured') ) {

	function knowhere_label_featured( $id = null, $args = array() ) {

		global $knowhere_settings;

		if ( !$knowhere_settings['job-show-label-featured'] ) return;

		$text = $knowhere_settings['job-label-featured-text'];

		if ( empty($text) ) {
			$text = esc_html__( 'Featured', 'knowherepro' );
		}

		$defaults    = array(
			'classes' => array(),
		);

		$parse_args = wp_parse_args( $args, $defaults );

		extract($parse_args);

		$classes = implode(' ', $classes);

		if ( !$id ) $id = get_the_ID();

		$job_is_featured = false;
		if ( is_position_featured( $id ) ) $job_is_featured = true;

		if ( true === $job_is_featured ) {
			echo '<span class="kw-label-featured '. $classes .'">'. esc_html($text) .'</span>';
		}

	}

}

if ( !function_exists('knowhere_label_hours_output') ) {

	function knowhere_label_hours_output( $id = 0 ) {

		$id = (int) ( empty( $id ) ? get_the_ID() : $id );

		global $knowhere_settings;

		if ( !$knowhere_settings['job-show-label-open-hours'] ) return;

		$listing = knowhere_get_listing( get_post() );
		$job_hours = $listing->get_business_hours();
		if ( ! $job_hours ) {
			return;
		}

		// Loop all job hours and remove empty hours.
		foreach ( $job_hours as $day => $hours ) {
			foreach ( $hours as $index => $hour ) {
				if ( ! $hour['start'] || ! $hour['end'] ) {
					unset( $job_hours[ $day ][ $index ] );
				}
			}
		}
		// Remove empty days.
		foreach ( $job_hours as $day => $hours ) {
			if ( ! $hours ) {
				unset( $job_hours[ $day ] );
			}
		}

		if ( empty( $job_hours ) ) { return; }

		if ( $listing->is_open() ) {
			$text = esc_html__( 'Open Now', 'knowherepro' );
			$class = 'kw-open-now';
		} else {
			$text = esc_html__( 'Closed Now', 'knowherepro' );
			$class = 'kw-closed-now';
		}

		?>

		<span class="kw-label-hours <?php echo sanitize_html_class($class) ?>"><?php echo $text ?></span>

		<?php
	}

}


function knowhere_the_company_tagline( $before = '', $after = '', $echo = true, $post = null ) {
	$company_tagline = knowhere_get_the_company_tagline( $post );

	if ( strlen( $company_tagline ) == 0 ) {
		return false;
	}

	$company_tagline = esc_attr( strip_tags( $company_tagline ) );
	$company_tagline = $before . $company_tagline . $after;

	if ( $echo ) {
		echo $company_tagline;
	} else {
		return $company_tagline;
	}
}

function knowhere_get_listing( $post = null ) {
	$factory = new Knowhere_Listing_Factory();
	$listing = $factory->get_listing( $post );

	return $listing;
}


function knowhere_get_the_company_tagline( $post = null ) {
	return knowhere_get_listing( $post )->get_the_company_tagline();
}

function knowhere_get_the_company_description( $post = null ) {
	return knowhere_get_listing( $post )->get_the_company_description();
}


function knowhere_the_company_description( $before = '', $after = '', $post = null ) {
	$company_description = knowhere_get_the_company_description( $post );

	if ( strlen( $company_description ) == 0 ) {
		return;
	}

	$company_description = wp_kses_post( $company_description );
	$company_description = $before . wpautop( $company_description ) . $after;

	echo $company_description;
}

function knowhere_get_the_company_name( $post = null ) {
	return knowhere_get_listing( $post )->get_the_company_name();
}


function knowhere_get_the_company_logo( $size = 'thumbnail', $post = null ) {
	return knowhere_get_listing( $post )->get_the_company_logo( $size );
}

/**
 * The Company Logo
 *
 */
function knowhere_the_company_logo( $size = 'thumbnail', $default = null, $post = null ) {
	$logo = knowhere_get_the_company_logo( $post, $size );

	if ( has_post_thumbnail( $post ) ) {
		echo '<img class="company_logo" src="' . esc_attr( $logo ) . '" alt="' . esc_attr( knowhere_get_the_company_name( $post ) ) . '" />';
	} // End if().
	elseif ( ! empty( $logo ) && ( strstr( $logo, 'http' ) || file_exists( $logo ) ) ) {
		if ( $size !== 'full' && function_exists( 'job_manager_get_resized_image' ) ) {
			$logo = job_manager_get_resized_image( $logo, $size );
		}
		echo '<img class="company_logo" src="' . esc_attr( $logo ) . '" alt="' . esc_attr( knowhere_get_the_company_name( $post ) ) . '" />';
	} elseif ( $default ) {
		echo '<img class="company_logo" src="' . esc_attr( $default ) . '" alt="' . esc_attr( knowhere_get_the_company_name( $post ) ) . '" />';
	} elseif ( defined( 'JOB_MANAGER_PLUGIN_URL' ) ) {
		echo '<img class="company_logo" src="' . esc_attr( apply_filters( 'job_manager_default_company_logo', JOB_MANAGER_PLUGIN_URL . '/assets/images/company.png' ) ) . '" alt="' . esc_attr( knowhere_get_the_company_name( $post ) ) . '" />';
	}
}


/**
 * Get the Company Website
 *
 * @return string $company_twitter
 */
function knowhere_get_the_company_website( $post = null ) {
	return knowhere_get_listing( $post )->get_the_company_website();
}

/**
 * Get the Company Twitter
 *
 * @return string $company_twitter
 */
function knowhere_get_the_company_twitter( $post = null ) {
	return knowhere_get_listing( $post )->get_the_company_twitter();
}

/**
 * Get the Company Facebook
 *
 * @return string
 */
function knowhere_get_the_company_facebook( $post = null ) {
	return knowhere_get_listing( $post )->get_the_company_facebook();
}


/**
 * Get the Company Google Plus
 *
 * @return string
 */
function knowhere_get_the_company_gplus( $post = null ) {
	return knowhere_get_listing( $post )->get_the_company_gplus();
}

/**
 * Get the Company LinkedIn
 *
 * @return string
 */
function knowhere_get_the_company_linkedin( $post = null ) {
	return knowhere_get_listing( $post )->get_the_company_linkedin();
}

/**
 * Get the Company Pinterest
 *
 * @return string
 */
function knowhere_get_the_company_pinterest( $post = null ) {
	return knowhere_get_listing( $post )->get_the_company_pinterest();
}

function knowhere_get_the_company_phone($post = null) {
	return knowhere_get_listing( $post )->get_the_company_phone();
}

if ( !function_exists('knowhere_get_post_id_by_meta_key_and_value') ) {
	function knowhere_get_post_id_by_meta_key_and_value($key, $value) {
		global $wpdb;
		$meta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->postmeta." WHERE meta_key=%s AND meta_value=%s", $key, $value ) );
		if (is_array($meta) && !empty($meta) && isset($meta[0])) {
			$meta = $meta[0];
		}
		if (is_object($meta)) {
			return $meta->post_id;
		}
		else {
			return false;
		}
	}
}