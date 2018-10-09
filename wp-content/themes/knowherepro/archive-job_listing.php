<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package KnowherePro
 */

get_header(); ?>

<div id="primary" class="kw-content-area">
	<div class="kw-entry-content">
		<main id="main" class="kw-site-main" role="main">

			<?php
			$shortcode = '[jobs';

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
			echo do_shortcode(  $shortcode ); ?>

		</main><!--/ #main-->
	</div>
</div><!--/ #primary-->

<?php get_footer(); ?>
