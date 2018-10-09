<?php

/* ---------------------------------------------------------------------- */
/*	Product Custom Tab Filter
/* ---------------------------------------------------------------------- */

if ( !function_exists('knowhere_woocommerce_product_custom_tab') ) {

	function knowhere_woocommerce_product_custom_tab($key) {
		global $post;

		$title_product_tab = $content_product_tab = '';
		$custom_tabs_array = get_post_meta($post->ID, 'knowhere_custom_tabs', true);
		$custom_tab = $custom_tabs_array[$key];

		extract($custom_tab);

		if ( $title_product_tab != '' ) {

			preg_match("!\[embed.+?\]|\[video.+?\]!", $content_product_tab, $match_video);
			preg_match("!\[(?:)?gallery.+?\]!", $content_product_tab, $match_gallery);

			if (!empty($match_video)) {

				global $wp_embed;

				$video = $match_video[0];
				$before = "<div class='kw-responsive-iframe'>";
				$before .= do_shortcode($wp_embed->run_shortcode($video));
				$before .= "</div>";
				$before = apply_filters('the_content', $before);
				echo $before;

			} elseif ( !empty($match_gallery) ) {

				$gallery = $match_gallery[0];
				if (strpos($gallery, 'vc_') === false) {
					$gallery = str_replace("gallery", 'knowhere_gallery image_size="848*370"', $gallery);
				}
				$before = apply_filters('the_content', $gallery);
				echo do_shortcode($before);

			} else {
				echo do_shortcode($content_product_tab);
			}

		}

	}
}

/* ---------------------------------------------------------------------- */
/*	Overwrite catalog ordering
/* ---------------------------------------------------------------------- */

if ( !function_exists('knowhere_overwrite_catalog_ordering') ) {

	function knowhere_overwrite_catalog_ordering($args) {

		global $knowhere_config;

		$keys = array( 'product_order', 'product_count' );
		if ( empty($knowhere_config['woocommerce'])) $knowhere_config['woocommerce'] = array();

		foreach ( $keys as $key ) {
			if (isset($_GET[$key]) ) {
				$_SESSION['knowhere_woocommerce'][$key] = esc_attr($_GET[$key]);
			}
			if ( isset($_SESSION['knowhere_woocommerce'][$key]) ) {
				$knowhere_config['woocommerce'][$key] = $_SESSION['knowhere_woocommerce'][$key];
			}
		}

		extract($knowhere_config['woocommerce']);

		if ( isset($product_order) && !empty($product_order) ) {
			switch ( $product_order ) {
				case 'date'  : $orderby = 'date'; $order = 'desc'; $meta_key = '';  break;
				case 'price' : $orderby = 'meta_value_num'; $order = 'asc'; $meta_key = '_price'; break;
				case 'popularity' : $orderby = 'meta_value_num'; $order = 'desc'; $meta_key = 'total_sales'; break;
				case 'title' : $orderby = 'title'; $order = 'asc'; $meta_key = ''; break;
				case 'default':
				default : $orderby = 'menu_order title'; $order = 'asc'; $meta_key = ''; break;
			}
		}

		if ( isset($orderby) )  $args['orderby'] = $orderby;
		if ( isset($order) ) 	$args['order'] = $order;

		if ( !empty($meta_key) ) {
			$args['meta_key'] = $meta_key;
		}

		return $args;
	}

	add_action( 'woocommerce_get_catalog_ordering_args', 'knowhere_overwrite_catalog_ordering');

}

/* ---------------------------------------------------------------------- */
/*	Product count
/* ---------------------------------------------------------------------- */

if ( !function_exists('knowhere_woocommerce_product_count') ) {
	function knowhere_woocommerce_product_count() {
		global $knowhere_settings;

		parse_str($_SERVER['QUERY_STRING'], $params);

		if ( $knowhere_settings['category-item'] ) {
			$per_page = absint($knowhere_settings['category-item']);
		} else {
			$per_page = 10;
		}

		$count = !empty($params['product_count']) ? $params['product_count'] : $per_page;
		return $count;
	}
}
