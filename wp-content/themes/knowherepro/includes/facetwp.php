<?php

function knowhere_get_facets( $area = 'front_page' ) {
	$facets = array();

	$knowhere_facets = (array) json_decode( get_option( 'knowhere_facets_config' ), true ) ;

	if ( isset( $knowhere_facets[ $area ] ) ) {
		$facets = $knowhere_facets[ $area ];
	}

	return apply_filters( 'knowhere_get_facets', $facets, $area );
}

/*
 * Output the loop for the listings when using the "listings" template
 */
function knowhere_facetwp_template_html( $output, $class ) {

	if ( 'listings' != $class->template[ 'name' ] ) {
		return $output;
	}

	$query = $class->query;

	global $knowhere_settings, $knowhere_config;

	$classes = apply_filters('knowhere_listings_classes', array(
		'job_listings', 'kw-listings'
	));

	$style = $knowhere_settings['job-listings-style'];

	$term_style = knowhere_job_get_term('pix_term_style');
	if ( !empty($term_style) ) {
		$style = $term_style;
	}

	$classes[] = $style;

	$count_columns = $knowhere_settings['job-listings-columns'];
	$term_count_columns = knowhere_job_get_term('pix_term_count_columns');

	if ( $term_count_columns && absint($term_count_columns) ) {
		$count_columns = $term_count_columns;
	}

	$view = $knowhere_settings['job-category-view'];
	$term_view = knowhere_job_get_term('pix_term_view');

	if ( $term_view ) {
		$view = $term_view;
	}

	$knowhere_config['term_view'] = $view;

	$classes[] = $view;
	$classes[] = 'kw-cols-' . $count_columns;

	ob_start();

	echo '<div class="'. implode( ' ', $classes ).'">';
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			get_template_part( 'job_manager/content', 'job_listing' );
		}
		wp_reset_postdata();
	} else {
		get_template_part( 'job_manager/content', 'no-jobs-found' );
	}
	echo '</div>';
	$output = ob_get_clean();

	return $output;
}

add_filter( 'facetwp_template_html', 'knowhere_facetwp_template_html', 10, 2 );

/*
 * Add a listing template
 */
function knowhere_register_listings_template( $templates ) {
	$templates[] = array(
		'label' => esc_html__( 'Listings', 'knowherepro' ), 'name' => 'listings', 'query' => '', 'template' => ''
	);
	return $templates;
}

add_filter( 'facetwp_templates', 'knowhere_register_listings_template' );

/*
 * Return the markup for facets
 *
 */
function knowhere_get_display_facets( $facets ) {
	$output = ''; $facets = array_values( $facets );

	if ( ! empty( $facets ) ) {
		foreach ( $facets as $facet ) {
			$output .= facetwp_display( 'facet', $facet['name'] );
		}
	}

	return $output;
}

/*
 * Filter the FacetWP query when using the "listings" template
 */
function knowhere_facetwp_query_args( $query_args, $facet ) {

	if ( 'listings' != $facet->template[ 'name' ] ) {
		return $query_args;
	}

	if ( '' == $query_args ) $query_args = array();

	$search = '';
	if ( ! empty( $facet->http_params[ 'get' ][ 's' ] ) ) {
		$search = $facet->http_params[ 'get' ][ 's' ];
	}

	$defaults = array(
		'post_type' => 'job_listing',
		'post_status' => 'publish',
		's' => $search,
	);

	$query_args = wp_parse_args( $query_args, $defaults );

	return $query_args;
}

add_filter( 'facetwp_query_args', 'knowhere_facetwp_query_args', 10, 2 );
add_filter( 'facetwp_template_use_archive', '__return_true' );


/*
 * Add labels
 */
function knowhere_facetwp_facet_html( $html, $params ) {
	if ( isset( $params['facet'] ) && isset( $params['facet']['label'] ) ) {
		$html = '<label class="knowhere-facetwp-filter-title">' . facetwp_i18n( $params['facet']['label'] ) . '</label><div class="knowhere-facet-wrapper">' . $html . '</div>';
	}

	return $html;
}

add_filter( 'facetwp_facet_html', 'knowhere_facetwp_facet_html', 10, 2 );

/* Enqueue scripts and styles for FacetWP */

function knowhere_fwp_admin_scripts() {
	if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'job-manager-settings' ) { return; }

	wp_enqueue_style( 'knowhere_fwp_job_manager_admin_css', get_theme_file_uri('config-job-manager/assets/css/facetwp.css'), array( 'job_manager_admin_css' ) );
	wp_register_script( 'knowhere_fwp_sortable_js', get_theme_file_uri('config-job-manager/assets/js/facetwp/sortable.js'), array(), null, true );
	wp_enqueue_script( 'knowhere_fwp_job_manager_admin_js', get_theme_file_uri('config-job-manager/assets/js/facetwp/fwp.js'), array( 'job_manager_admin_js', 'knowhere_fwp_sortable_js' ), null, true );
}

add_action( 'admin_enqueue_scripts', 'knowhere_fwp_admin_scripts', 12 );

function knowhere_wpjm_facets_drag_drop ( $option, $attr, $value ) {
	$current_values = json_decode( $value );
	$facetwp_settings = json_decode( get_option( 'facetwp_settings' ) );
	?>

	<div class="available_block">

		<h2><?php esc_html_e( 'Facets Filtering', 'knowherepro' ); ?></h2>
		<p><?php esc_html_e( 'Add filtering fields to your site', 'knowherepro' ); ?></p>

		<div class="sortable_block">
			<h3><?php esc_html_e( 'Available Facets', 'knowherepro' ); ?></h3>
			<p><em><?php esc_html_e( 'Drag and drop the facets you\'d like to add into the boxes', 'knowherepro' ); ?></em></p>
			<ul id="knowhere_facets_list" class="facets">
				<?php knowhere_admin_show_facets_items( $facetwp_settings->facets ); ?>
			</ul>
		</div>
	</div>

	<div class="knowhere-facets-config">

		<input type="hidden" id="setting-knowhere_facets_config" name="knowhere_facets_config" value='<?php echo json_encode( $current_values ); ?>'>

		<div class="sortable_block">
			<h3><?php esc_html_e( 'Listing Archive', 'knowherepro' ); ?></h3>

			<ul id="listings_archive" class="facets">
				<?php
				if ( isset( $current_values->listings_archive ) && ! empty( $current_values->listings_archive ) ) {
					knowhere_admin_show_facets_items( $current_values->listings_archive );
				} ?>
			</ul>

		</div>

		<div class="sortable_block">
			<h3><?php esc_html_e( 'Shortcode Listing Map', 'knowherepro' ); ?></h3>

			<ul id="shortcode_listing_map" class="facets">
				<?php
				if ( isset( $current_values->shortcode_listing_map ) && ! empty( $current_values->shortcode_listing_map ) ) {
					knowhere_admin_show_facets_items( $current_values->shortcode_listing_map );
				} ?>
			</ul>
		</div>
	</div>
	<?php
}

add_action( 'wp_job_manager_admin_field_knowhere_facetwp_drag_and_drop', 'knowhere_wpjm_facets_drag_drop', 10, 4 );



function knowhere_admin_show_facets_items( $facetwp_settings = array() ) {

	if ( empty( $facetwp_settings ) ) return;

	foreach ( $facetwp_settings as $facet ) {

		if ( empty( $facet ) ) continue;

		$title = '';
		if ( isset(  $facet->label ) ) $title = $facet->label;

		$type = 'dropdown';
		if ( isset( $facet ) ) $type = $facet->type; ?>

		<li data-facet='<?php echo json_encode( $facet ); ?>'>
			<span class="title"><?php echo sprintf('%s', $title); ?></span>
			<span class="type"><?php echo sprintf('%s', $type); ?></span>
			<span class="facet-remove">x</span>
		</li>

	<?php }
}

add_filter( 'facetwp_proximity_load_js', '__return_false' );
