<?php
/**
 * Attribute Functions
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Gets text attributes from a string.
 *
 * @param string $raw_attributes
 * @return array
 */
function kw_get_text_attributes( $raw_attributes ) {
	return array_filter( array_map( 'trim', explode( KW_DELIMITER, html_entity_decode( $raw_attributes, ENT_QUOTES, get_bloginfo( 'charset' ) ) ) ), 'kw_get_text_attributes_filter_callback' );
}

/**
 * See if an attribute is actually valid.
 * @since  3.0.0
 * @param  string $value
 * @return bool
 */
function kw_get_text_attributes_filter_callback( $value ) {
	return '' !== $value;
}

/**
 * Implode an array of attributes using KW_DELIMITER.
 * @since  3.0.0
 * @param  array $attributes
 * @return string
 */
function kw_implode_text_attributes( $attributes ) {
	return implode( ' ' . KW_DELIMITER . ' ', $attributes );
}

/**
 * Get attribute taxonomies.
 *
 * @return array of objects
 */
function kw_get_attribute_taxonomies() {
	if ( false === ( $attribute_taxonomies = get_transient( 'kw_attribute_taxonomies' ) ) ) {
		global $wpdb;

		$attribute_taxonomies = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "kw_attribute_taxonomies WHERE attribute_name != '' ORDER BY attribute_name ASC;" );

		set_transient( 'kw_attribute_taxonomies', $attribute_taxonomies );
	}

	return (array) array_filter( apply_filters( 'kw_attribute_taxonomies', $attribute_taxonomies ) );
}



/**
 * Get a product attribute name.
 *
 * @param string $attribute_name Attribute name.
 * @return string
 */
function kw_attribute_taxonomy_name( $attribute_name ) {
	return $attribute_name ? 'kw_' . kw_sanitize_taxonomy_name( $attribute_name ) : '';
}

/**
 * Get a product attribute name by Category.
 *
 * @return string Return an empty string if attribute doesn't exist.
 */
function kw_attribute_taxonomy_name_by_category( $category ) {
	global $wpdb;

	$attribute_category = $wpdb->get_var( $wpdb->prepare( "
		SELECT attribute_name, attribute_type
		FROM {$wpdb->prefix}kw_attribute_taxonomies
		WHERE attribute_category = %d
	", $category ) );

	if ( $attribute_category && ! is_wp_error( $attribute_category ) ) {
		return kw_attribute_taxonomy_name( $attribute_category );
	}

	return '';
}


/**
 * Get a product attribute name, type by Category.
 *
 * @return string Return an empty string if attribute doesn't exist.
 */
function kw_attribute_taxonomy_name_type_by_category( $category ) {
	global $wpdb;

	$attr = $wpdb->get_results( "
		SELECT attribute_name, attribute_type, attribute_category
		FROM {$wpdb->prefix}kw_attribute_taxonomies
		WHERE attribute_name != ''
	");

	if ( $attr && ! is_wp_error( $attr ) ) {

		$var_by_ref = array();

		foreach ( $attr as $i => $row ) {

			$categories = explode( ',', $row->attribute_category );

			if ( in_array( $category, $categories) ) {
				$var_by_ref[ $i ] = new stdClass();
				$var_by_ref[ $i ]->name = kw_attribute_taxonomy_name( $row->attribute_name );
				$var_by_ref[ $i ]->type = $row->attribute_type;
			}

		}

		return $var_by_ref;
	}

	return '';
}

/**
 * Get a product attribute name by Category.
 *
 * @return string Return an empty string if attribute doesn't exist.
 */
function kw_attribute_taxonomy_category_by_name( $name ) {
	global $wpdb;

	$name = str_replace( 'kw_', '', kw_sanitize_taxonomy_name( $name ) );

	$attribute_name = $wpdb->get_var( $wpdb->prepare( "
		SELECT attribute_category
		FROM {$wpdb->prefix}kw_attribute_taxonomies
		WHERE attribute_name = %s
	", $name ) );

	if ( $attribute_name && ! is_wp_error( $attribute_name ) ) {
		return explode(',', $attribute_name);
	}

	return array();
}

/**
 * Get a product attribute ID by name.
 *
 * @param string $name Attribute name.
 * @return int
 */
function kw_attribute_taxonomy_id_by_name( $name ) {
	$name       = str_replace( 'kw_', '', kw_sanitize_taxonomy_name( $name ) );
	$taxonomies = wp_list_pluck( kw_get_attribute_taxonomies(), 'attribute_id', 'attribute_name' );

	return isset( $taxonomies[ $name ] ) ? (int) $taxonomies[ $name ] : 0;
}

/**
 * Get a product attributes label.
 *
 * @param string $name
 * @param object $product object Optional
 * @return string
 */
function kw_attribute_label( $name ) {
	if ( taxonomy_is_listing_attribute( $name ) ) {
		$name       = kw_sanitize_taxonomy_name( str_replace( 'kw_', '', $name ) );
		$all_labels = wp_list_pluck( kw_get_attribute_taxonomies(), 'attribute_label', 'attribute_name' );
		$label      = isset( $all_labels[ $name ] ) ? $all_labels[ $name ] : $name;
	} else {
		$label = $name;
	}

	return apply_filters( 'kw_attribute_label', $label, $name );
}

/**
 * Get a product attributes orderby setting.
 *
 * @param mixed $name
 * @return string
 */
//function kw_attribute_orderby( $name ) {
//	global $kw_product_attributes, $wpdb;
//
//	$name = str_replace( 'kw_', '', sanitize_title( $name ) );
//
//	if ( isset( $kw_product_attributes[ 'kw_' . $name ] ) ) {
//		$orderby = $kw_product_attributes[ 'kw_' . $name ]->attribute_orderby;
//	} else {
//		$orderby = $wpdb->get_var( $wpdb->prepare( "SELECT attribute_orderby FROM " . $wpdb->prefix . "kw_attribute_taxonomies WHERE attribute_name = %s;", $name ) );
//	}
//
//	return apply_filters( 'kw_attribute_orderby', $orderby, $name );
//}

/**
 * Get an array of product attribute taxonomies.
 *
 * @return array
 */
function kw_get_attribute_taxonomy_names() {
	$taxonomy_names = array();
	$attribute_taxonomies = kw_get_attribute_taxonomies();
	if ( ! empty( $attribute_taxonomies ) ) {
		foreach ( $attribute_taxonomies as $tax ) {
			$taxonomy_names[] = kw_attribute_taxonomy_name( $tax->attribute_name );
		}
	}
	return $taxonomy_names;
}

/**
 * Get attribute types.
 *
 * @return array
 */
function kw_get_attribute_types() {
	return (array) apply_filters( 'kw_attributes_type_selector', array(
		'select' => __( 'Select', 'knowherepro_app_textdomain' ),
		'checkbox'   => __( 'Checkbox', 'knowherepro_app_textdomain' ),
	) );
}

/**
 * Get job listing categories.
 *
 * @return array
 */
function kw_get_attribute_categories() {
	$r = get_terms( array(
		'taxonomy' => 'job_listing_category',
		'hide_empty' => false
	));

	if ( !is_wp_error($r) && ( !is_array($r) || !is_object($r) ) ) {
		return wp_list_pluck( $r, 'name', 'term_id' );
	}

	return array();
}

/**
 * Get attribute type label.
 *
 * @return string
 */
function kw_get_attribute_type_label( $type ) {
	$types = kw_get_attribute_types();

	return isset( $types[ $type ] ) ? $types[ $type ] : ucfirst( $type );
}


/**
 * Get attribute category label.
 *
 * @return string
 */
function kw_get_attribute_category_label( $type ) {
	$types = kw_get_attribute_categories();
	$explode_type = explode(',', $type);
	$labels = array();

	foreach ( $types as $key => $typa ) {
		if ( in_array( $key, $explode_type ) ) {
			$labels[] = $types[$key];
		}
	}

	return ( isset( $labels ) && !empty($labels) ) ? implode( ', ', $labels ) : ucfirst( $type );
}

/**
 * Check if attribute name is reserved.
 * https://codex.wordpress.org/Function_Reference/register_taxonomy#Reserved_Terms.
 *
 * @since  2.4.0
 * @param  string $attribute_name
 * @return bool
 */
function kw_check_if_attribute_name_is_reserved( $attribute_name ) {
	// Forbidden attribute names
	$reserved_terms = array(
		'attachment',
		'attachment_id',
		'author',
		'author_name',
		'calendar',
		'cat',
		'category',
		'category__and',
		'category__in',
		'category__not_in',
		'category_name',
		'comments_per_page',
		'comments_popup',
		'cpage',
		'day',
		'debug',
		'error',
		'exact',
		'feed',
		'hour',
		'link_category',
		'm',
		'minute',
		'monthnum',
		'more',
		'name',
		'nav_menu',
		'nopaging',
		'offset',
		'order',
		'orderby',
		'p',
		'page',
		'page_id',
		'paged',
		'pagename',
		'pb',
		'perm',
		'post',
		'post__in',
		'post__not_in',
		'post_format',
		'post_mime_type',
		'post_status',
		'post_tag',
		'post_type',
		'posts',
		'posts_per_archive_page',
		'posts_per_page',
		'preview',
		'robots',
		's',
		'search',
		'second',
		'sentence',
		'showposts',
		'static',
		'subpost',
		'subpost_id',
		'tag',
		'tag__and',
		'tag__in',
		'tag__not_in',
		'tag_id',
		'tag_slug__and',
		'tag_slug__in',
		'taxonomy',
		'tb',
		'term',
		'type',
		'w',
		'withcomments',
		'withoutcomments',
		'year',
	);

	return in_array( $attribute_name, $reserved_terms );
}

/**
 * Get attribute data by ID.
 *
 * @since  3.2.0
 * @param  int $id Attribute ID.
 * @return stdClass|null
 */
function kw_get_attribute( $id ) {
	global $wpdb;

	$data = $wpdb->get_row( $wpdb->prepare( "
		SELECT *
		FROM {$wpdb->prefix}kw_attribute_taxonomies
		WHERE attribute_id = %d
	 ", $id ) );

	if ( is_wp_error( $data ) || is_null( $data ) ) {
		return null;
	}

	$attribute               = new stdClass();
	$attribute->id           = (int) $data->attribute_id;
	$attribute->name         = $data->attribute_label;
	$attribute->slug         = kw_attribute_taxonomy_name( $data->attribute_name );
	$attribute->type         = $data->attribute_type;
	$attribute->category     = $data->attribute_category;

	return $attribute;
}

/**
 * Create attribute.
 *
 * @since  3.2.0
 * @param  array $args Attribute arguments {
 *     Array of attribute parameters.
 *
 *     @type int    $id           Unique identifier, used to update an attribute.
 *     @type string $name         Attribute name. Always required.
 *     @type string $slug         Attribute alphanumeric identifier.
 *     @type string $type         Type of attribute.
 *                                Core by default accepts: 'select' and 'text'.
 *                                Default to 'select'.
 *     @type string $order_by     Sort order.
 *                                Accepts: 'menu_order', 'name', 'name_num' and 'id'.
 *                                Default to 'menu_order'.
 *     @type bool   $has_archives Enable or disable attribute archives. False by default.
 * }
 * @return int|WP_Error
 */
function kw_create_attribute( $args ) {
	global $wpdb;

	$args   = wp_unslash( $args );
	$id     = ! empty( $args['id'] ) ? intval( $args['id'] ) : 0;
//	$format = array( '%s', '%s', '%s', '%s', '%d' );
	$format = array( '%s', '%s', '%s', '%s' );

	// Name is required.
	if ( empty( $args['name'] ) ) {
		return new WP_Error( 'missing_attribute_name', __( 'Please, provide an attribute name.', 'knowherepro_app_textdomain' ), array( 'status' => 400 ) );
	}

	// Set the attribute slug.
	if ( empty( $args['slug'] ) ) {
		$slug = kw_sanitize_taxonomy_name( $args['name'] );
	} else {
		$slug = preg_replace( '/^kw\_/', '', kw_sanitize_taxonomy_name( $args['slug'] ) );
	}

	// Validate slug.
	if ( strlen( $slug ) >= 28 ) {
		return new WP_Error( 'invalid_product_attribute_slug_too_long', sprintf( __( 'Slug "%s" is too long (28 characters max). Shorten it, please.', 'knowherepro_app_textdomain' ), $slug ), array( 'status' => 400 ) );
	} elseif ( kw_check_if_attribute_name_is_reserved( $slug ) ) {
		return new WP_Error( 'invalid_product_attribute_slug_reserved_name', sprintf( __( 'Slug "%s" is not allowed because it is a reserved term. Change it, please.', 'knowherepro_app_textdomain' ), $slug ), array( 'status' => 400 ) );
	} elseif ( ( 0 === $id && taxonomy_exists( kw_attribute_taxonomy_name( $slug ) ) ) || ( isset( $args['old_slug'] ) && $args['old_slug'] !== $args['slug'] && taxonomy_exists( kw_attribute_taxonomy_name( $slug ) ) ) ) {
		return new WP_Error( 'invalid_product_attribute_slug_already_exists', sprintf( __( 'Slug "%s" is already in use. Change it, please.', 'knowherepro_app_textdomain' ), $slug ), array( 'status' => 400 ) );
	}

	// Validate type.
	if ( empty( $args['type'] ) || ! array_key_exists( $args['type'], kw_get_attribute_types() ) ) {
		$args['type'] = 'select';
	}

	$data = array(
		'attribute_label'   => $args['name'],
		'attribute_name'    => $slug,
		'attribute_type'    => $args['type'],
		'attribute_category' => implode(', ', $args['category'])
//		'attribute_orderby' => $args['order_by'],
//		'attribute_public'  => isset( $args['has_archives'] ) ? (int) $args['has_archives'] : 0,
	);

	// Create or update.
	if ( 0 === $id ) {
		$results = $wpdb->insert(
			$wpdb->prefix . 'kw_attribute_taxonomies',
			$data,
			$format
		);

		if ( is_wp_error( $results ) ) {
			return new WP_Error( 'cannot_create_attribute', $results->get_error_message(), array( 'status' => 400 ) );
		}

		$id = $wpdb->insert_id;

		/**
		 * Attribute added.
		 *
		 * @param int   $id   Added attribute ID.
		 * @param array $data Attribute data.
		 */
		do_action( 'kw_attribute_added', $id, $data );
	} else {
		
		$results = $wpdb->update(
			$wpdb->prefix . 'kw_attribute_taxonomies',
			$data,
			array( 'attribute_id' => $id ),
			$format,
			array( '%d' )
		);

		if ( false === $results ) {
			return new WP_Error( 'cannot_update_attribute', __( 'Could not update the attribute.', 'knowherepro_app_textdomain' ), array( 'status' => 400 ) );
		}

		// Set old_slug to check for database changes.
		$args['old_slug'] = ! empty( $args['old_slug'] ) ? $args['old_slug'] : $args['slug'];

		/**
		 * Attribute updated.
		 *
		 * @param int    $id        Added attribute ID.
		 * @param array  $data      Attribute data.
		 * @param string $old_slug  Attribute old name.
		 */
		do_action( 'kw_attribute_updated', $id, $data, $args['old_slug'] );

		if ( $args['old_slug'] !== $args['slug'] ) {
			// Update taxonomies in the wp term taxonomy table.
			$wpdb->update(
				$wpdb->term_taxonomy,
				array( 'taxonomy' => kw_attribute_taxonomy_name( $data['attribute_name'] ) ),
				array( 'taxonomy' => 'kw_' . $args['old_slug'] )
			);

			// Update taxonomy ordering term meta.
			$table_name = get_option( 'db_version' ) < 34370 ? $wpdb->prefix . 'kw_termmeta' : $wpdb->termmeta;
			$wpdb->update(
				$table_name,
				array( 'meta_key' => 'order_kw_' . sanitize_title( $data['attribute_name'] ) ),
				array( 'meta_key' => 'order_kw_' . sanitize_title( $args['old_slug'] ) )
			);

			// Update product attributes which use this taxonomy.
//			$old_attribute_name_length = strlen( $args['old_slug'] ) + 3;
//			$attribute_name_length     = strlen( $data['attribute_name'] ) + 3;

//			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE( meta_value, %s, %s ) WHERE meta_key = '_product_attributes'",
//				's:' . $old_attribute_name_length . ':"kw_' . $args['old_slug'] . '"',
//				's:' . $attribute_name_length . ':"kw_' . $data['attribute_name'] . '"'
//			) );

			// Update variations which use this taxonomy.
			$wpdb->update(
				$wpdb->postmeta,
				array( 'meta_key' => 'attribute_kw_' . sanitize_title( $data['attribute_name'] ) ),
				array( 'meta_key' => 'attribute_kw_' . sanitize_title( $args['old_slug'] ) )
			);
		}
	}

	// Clear cache and flush rewrite rules.
	wp_schedule_single_event( time(), 'kw_flush_rewrite_rules' );
	delete_transient( 'kw_attribute_taxonomies' );

	return $id;
}

/**
 * Update an attribute.
 *
 * For available args see kw_create_attribute().
 *
 * @since  3.2.0
 * @param  int $id Attribute ID.
 * @param  array $args Attribute arguments.
 * @return int|WP_Error
 */
function kw_update_attribute( $id, $args ) {
	global $wpdb;

	$attribute = kw_get_attribute( $id );

	$args['id'] = $attribute ? $attribute->id : 0;

	if ( $args['id'] && empty( $args['name'] ) ) {
		$args['name'] = $attribute->name;
	}

	$args['old_slug'] = $wpdb->get_var( $wpdb->prepare( "
		SELECT attribute_name
		FROM {$wpdb->prefix}kw_attribute_taxonomies
		WHERE attribute_id = %d
	", $args['id']
	) );

	return kw_create_attribute( $args );
}

/**
 * Delete attribute by ID.
 *
 * @since  3.2.0
 * @param  int $id Attribute ID.
 * @return bool
 */
function kw_delete_attribute( $id ) {
	global $wpdb;

	$name = $wpdb->get_var( $wpdb->prepare( "
		SELECT attribute_name
		FROM {$wpdb->prefix}kw_attribute_taxonomies
		WHERE attribute_id = %d
	", $id ) );

	$taxonomy = kw_attribute_taxonomy_name( $name );

	if ( $name && $wpdb->query( "DELETE FROM {$wpdb->prefix}kw_attribute_taxonomies WHERE attribute_id = $id" ) ) {
		if ( taxonomy_exists( $taxonomy ) ) {
			$terms = get_terms( $taxonomy, 'orderby=name&hide_empty=0' );
			foreach ( $terms as $term ) {
				wp_delete_term( $term->term_id, $taxonomy );
			}
		}

		/**
		 * After deleting an attribute.
		 *
		 * @param int    $id       Attribute ID.
		 * @param string $name     Attribute name.
		 * @param string $taxonomy Attribute taxonomy name.
		 */
		wp_schedule_single_event( time(), 'kw_flush_rewrite_rules' );
		delete_transient( 'kw_attribute_taxonomies' );

		return true;
	}

	return false;
}
