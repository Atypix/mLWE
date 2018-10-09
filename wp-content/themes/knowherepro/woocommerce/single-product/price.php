<?php
/**
 * Single Product Price, including microdata for SEO
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

?>

<div class="kw-entry-shop-controls row">

	<div class="col-sm-7">
		<div class="price"><?php echo $product->get_price_html(); ?></div>

		<?php if ( $product->managing_stock() ):
			$units_sold = get_post_meta( $product->get_id(), 'total_sales', true );
			$stock_quantity = $product->get_stock_quantity();
			$stock_text = sprintf( __('Hurry! Only %s coupons left! %s coupons already purchased', 'knowherepro' ), $stock_quantity, $units_sold );
			?>
			<div class="kw-limited-offer"><div class="kw-lo-message"><?php echo sprintf('%s', $stock_text) ?></div></div>
		<?php endif ?>

		<?php woocommerce_template_single_rating(); ?>
	</div>

	<div class="col-sm-5 kw-right-edge">

		<?php if ( $product->is_on_sale() ) : ?>

			<?php
			$percentage = 0;

			if ( $product->get_regular_price() ) {
				$percentage = round( ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100 );
			}

			$save_price = $product->get_regular_price() - $product->get_sale_price();

			if ( defined('RC_TC_BASE_FILE') ) {

				$rules = array();
				$query_meta_query=array('relation' => 'AND');
				$query_meta_query[] = array(
					'key' =>'status',
					'value' => "active",
					'compare' => '=',
				);
				$matched_products = get_posts(
					array(
						'post_type' 	=> 'flash_sale',
						'numberposts' 	=> -1,
						'post_status' 	=> 'publish',
						'fields' 		=> 'ids',
						'no_found_rows' => true,
						'orderby'	=>'modified',
						'meta_query' => $query_meta_query,
					)
				);

				foreach( $matched_products as $pr ) {
					$pw_type = get_post_meta($pr,'pw_type',true);
					if ( $pw_type != "flashsale" ) {
						continue;
					}
					$pw_discount = get_post_meta($pr,'pw_discount',true);
					$pw_type_discount = get_post_meta($pr,'pw_type_discount',true);

					$rules[$pr]=array(
						"pw_discount"=>$pw_discount,
						"pw_type_discount"=>$pw_type_discount,
					);
				}

				if ( is_array($rules) && !empty($rules) ) {

					$base_price = PW_Discount_function::pw_get_base_price_by_product($product);

					foreach ( $rules as $rule_key => $rule ) {
						$discount = PW_Discount_function::pw_get_discunt_price( $base_price, $rule['pw_type_discount'], $rule['pw_discount'] );
					}

					$percentage = round( ( ( $product->get_price() - $discount ) / $product->get_price() ) * 100 );
					$save_price = $product->get_price() - $discount;

				}

			}

			?>

			<div class="kw-stats">

				<div class="kw-stats-inner">

					<?php if ( $product->get_price() ): ?>

						<dl class="kw-stats-item">
							<dt class="kw-caption"><?php echo esc_html__('Value', 'knowherepro') ?>:</dt>
							<dd class="kw-value"><?php echo wc_price($product->get_price()); ?></dd>
						</dl><!--/ .kw-stats-item -->

					<?php endif; ?>

					<?php if ( $percentage ): ?>

						<dl class="kw-stats-item">
							<dt class="kw-caption"><?php echo esc_html__('Discount', 'knowherepro') ?>:</dt>
							<dd class="kw-value"><?php echo absint($percentage) ?>%</dd>
						</dl><!--/ .kw-stats-item -->

					<?php endif; ?>

					<?php if ( $save_price ): ?>

						<dl class="kw-stats-item">
							<dt class="kw-caption"><?php echo esc_html__('You Save', 'knowherepro') ?>:</dt>
							<dd class="kw-value"><?php echo wc_price( $save_price ) ?></dd>
						</dl><!--/ .kw-stats-item -->

					<?php endif; ?>

				</div><!--/ .kw-stats-inner -->

			</div>

		<?php endif; ?>

	</div>

</div>


