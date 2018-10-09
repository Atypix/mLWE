<?php
/**
 * The template for displaying tag pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package KnowherePro
 */

get_header(); ?>

<div id="primary" class="kw-content-area">
	<div class="kw-entry-content">
		<?php
		global $wp_query;
		$term =	$wp_query->queried_object;

		if ( isset( $term->slug) ) {

			$shortcode = '[jobs tags="' . $term->slug . '" show_tags="true"';

			//show map
			$show_map = knowhere_listings_page_shortcode_get_show_map_param();
			if ( false === $show_map ) {
				$shortcode .= ' show_map="false"';
			} else {
				$shortcode .= ' show_map="true"';
			}

			$orderby = knowhere_listings_page_shortcode_get_orderby_param();
			$shortcode .= ' orderby="' . $orderby . '"';

			$order = knowhere_listings_page_shortcode_get_order_param();
			$shortcode .= ' order="' . $order . '"';

			$shortcode .= ']';
			echo do_shortcode(  $shortcode );

		} ?>
	</div>
</div><!--/ #primary-->

<?php get_footer(); ?>
