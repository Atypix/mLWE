<?php
/**
 * The template for displaying product widget entries.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-widget-product.php.
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
?>
<li>
	<?php do_action( 'woocommerce_widget_product_item_start', $args ); ?>

	<div class="kw-entry-thumb">
		<a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>">
			<?php echo $product->get_image(array(100, 100)); ?>
		</a>
	</div>

	<div class="kw-entry-info">

		<h3 class="kw-entry-title">
			<a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>"><?php echo esc_html($product->get_title()); ?></a>
		</h3>

		<div class="kw-entry-shop-controls">

			<div class="price">
				<?php echo $product->get_price_html(); ?>
			</div>

			<?php if ( ! empty( $show_rating ) ) : ?>
				<?php echo wc_get_rating_html( $product->get_average_rating() ); ?>
			<?php endif; ?>

		</div>

	</div>

	<?php do_action( 'woocommerce_widget_product_item_end', $args ); ?>
</li>
