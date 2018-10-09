<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product, $woocommerce_loop, $knowhere_settings;
/*
// Get visble related products then sort them at random.
$related_products = array_filter( array_map( 'wc_get_product', wc_get_related_products( $product->get_id(), $knowhere_settings['product-related-count'], $product->get_upsell_ids() ) ), 'wc_products_array_filter_visible' );

// Handle orderby.
$related_products = wc_products_array_orderby( $related_products, 'rand', 'desc' );

$woocommerce_loop['columns'] = isset($knowhere_settings['product-related-cols']) ? absint($knowhere_settings['product-related-cols']) : 3;

if ( isset($related_products) && $related_products ) : ?>

	<div class="kw-box">

		<h3><?php esc_html_e( 'Related Products', 'knowherepro' ); ?></h3>

		<div class="kw-products-holder columns-<?php echo esc_attr($woocommerce_loop['columns']) ?>">

			<?php woocommerce_product_loop_start(); ?>

			<?php foreach ( $related_products as $related_product ) : ?>

				<?php
				$post_object = get_post( $related_product->get_id() );

				setup_postdata( $GLOBALS['post'] =& $post_object );

				wc_get_template_part( 'content', 'product' ); ?>

			<?php endforeach; ?>

			<?php woocommerce_product_loop_end(); ?>

		</div>

	</div>

<?php endif;

wp_reset_postdata();
*/
