<?php
require_once( get_theme_file_path( 'includes/metadata/meta_values.php' ) );
require_once( get_theme_file_path( 'includes/metadata/functions-types.php' ) );
require_once( get_theme_file_path( 'includes/metadata/product.php' ) );

if ( !function_exists('knowhere_get_term_from_query_var') ) {

	function knowhere_get_term_from_query_var() {

		$qterm = get_query_var( 'term', null );
		$qtaxonomy = get_query_var( 'taxonomy', null );

		if ( $qterm && $qtaxonomy ) {
			$term = get_term_by('slug', $qterm, $qtaxonomy);
		} else {
			$term = false;
		}

		return $term;
	}

}

if ( !function_exists('knowhere_get_meta_value') ) {

	function knowhere_get_meta_value($meta_key) {

		$value = '';

		if ( knowhere_is_product_category() ) {

			$term = knowhere_get_term_from_query_var();

			if ( $term ) {
				$value = get_metadata($term->taxonomy, $term->term_id, $meta_key, true);
			}
		}

		return $value;
	}

}
