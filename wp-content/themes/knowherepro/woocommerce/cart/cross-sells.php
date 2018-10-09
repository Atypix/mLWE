<?php
/**
 * Cross-sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cross-sells.php.
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

if ( !$knowhere_settings['product-crossell'] ) return;

$woocommerce_loop['columns'] = isset($knowhere_settings['product-crossell-cols']) ? absint($knowhere_settings['product-crossell-cols']) : 3;

if ( isset($cross_sells) && $cross_sells ) : ?>

	<div class="kw-box">

		<h3><?php esc_html_e( 'You may be interested in&hellip;', 'knowherepro' ) ?></h3>

		<div class="kw-products-holder columns-<?php echo esc_attr($woocommerce_loop['columns']) ?>">

			<?php woocommerce_product_loop_start(); ?>

			<?php foreach ( $cross_sells as $cross_sell ) : ?>

				<?php
				$post_object = get_post( $cross_sell->get_id() );

				setup_postdata( $GLOBALS['post'] =& $post_object );

				wc_get_template_part( 'content', 'product' ); ?>

			<?php endforeach; ?>

			<?php woocommerce_product_loop_end(); ?>

		</div><!--/ .kw-products-holder-->

	</div><!--/ .kw-box-->

<?php endif;

wp_reset_query();
