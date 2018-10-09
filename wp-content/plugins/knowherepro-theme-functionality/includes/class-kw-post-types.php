<?php
/**
 * Registers taxonomies.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Post_types Class.
 */
class KW_Post_types {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
	}

	/**
	 * Register core taxonomies.
	 */
	public static function register_taxonomies() {

		if ( ! is_blog_installed() ) {
			return;
		}

		$admin_capability = 'manage_job_listings';

		global $kw_product_attributes;

		$kw_product_attributes = array();

		if ( $attribute_taxonomies = kw_get_attribute_taxonomies() ) {
			foreach ( $attribute_taxonomies as $tax ) {
				if ( $name = kw_attribute_taxonomy_name( $tax->attribute_name ) ) {
//					$tax->attribute_public          = absint( isset( $tax->attribute_public ) ? $tax->attribute_public : 1 );
					$label                          = ! empty( $tax->attribute_label ) ? $tax->attribute_label : $tax->attribute_name;
					$kw_product_attributes[ $name ] = $tax;
					$taxonomy_data                  = array(
						'hierarchical'          => true,
//						'hierarchical'          => false,
						'update_count_callback' => '_update_post_term_count',
						'labels'                => array(
								'name'              => sprintf( _x( 'Listing %s', 'Listing Attribute', 'knowherepro_app_textdomain' ), $label ),
								'singular_name'     => $label,
								'search_items'      => sprintf( __( 'Search %s', 'knowherepro_app_textdomain' ), $label ),
								'all_items'         => sprintf( __( 'All %s', 'knowherepro_app_textdomain' ), $label ),
								'parent_item'       => sprintf( __( 'Parent %s', 'knowherepro_app_textdomain' ), $label ),
								'parent_item_colon' => sprintf( __( 'Parent %s:', 'knowherepro_app_textdomain' ), $label ),
								'edit_item'         => sprintf( __( 'Edit %s', 'knowherepro_app_textdomain' ), $label ),
								'update_item'       => sprintf( __( 'Update %s', 'knowherepro_app_textdomain' ), $label ),
								'add_new_item'      => sprintf( __( 'Add new %s', 'knowherepro_app_textdomain' ), $label ),
								'new_item_name'     => sprintf( __( 'New %s', 'knowherepro_app_textdomain' ), $label ),
								'not_found'         => sprintf( __( 'No &quot;%s&quot; found', 'knowherepro_app_textdomain' ), $label ),
							),
						'show_ui'            => true,
						'show_in_quick_edit' => false,
						'show_in_menu'       => false,
//						'meta_box_cb'        => false,
						'query_var'          => 1,
						'rewrite'            => false,
						'sort'               => false,
						'public'             => 1,
//						'show_in_nav_menus'  => 1,
						'capabilities'			=> array(
							'manage_terms' 		=> $admin_capability,
							'edit_terms' 		=> $admin_capability,
							'delete_terms' 		=> $admin_capability,
							'assign_terms' 		=> $admin_capability,
						),
					);
//
//					if ( 1 === $tax->attribute_public && sanitize_title( $tax->attribute_name ) ) {
//						$taxonomy_data['rewrite'] = array(
//							'slug'         => trailingslashit( $permalinks['attribute_rewrite_slug'] ) . sanitize_title( $tax->attribute_name ),
//							'with_front'   => false,
//							'hierarchical' => true,
//						);
//					}

					register_taxonomy( $name, apply_filters( "kw_taxonomy_objects_{$name}", array( 'job_listing' ) ), apply_filters( "kw_taxonomy_args_{$name}", $taxonomy_data ) );
				}
			}
		}

	}

}

KW_Post_types::init();
