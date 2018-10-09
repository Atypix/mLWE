<?php
/**
 * Order Customer Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-customer.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$show_shipping = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address();
?>

<section class="woocommerce-customer-details col2-set">

	<h3>Informations du participant</h3>

	<div class="shop-table">

		<table class="woocommerce-table woocommerce-table--customer-details shop_table customer_details">

			<?php if ( $order->get_customer_note() ) : ?>
				<tr>
					<th><?php esc_html_e( 'Note:', 'knowherepro' ); ?></th>
					<td><?php echo wptexturize( $order->get_customer_note() ); ?></td>
				</tr>
			<?php endif; ?>

			<?php if ( $order->get_billing_email() ) : ?>
				<tr>
					<th><?php esc_html_e( 'Email:', 'knowherepro' ); ?></th>
					<td><?php echo esc_html( $order->get_billing_email() ); ?></td>
				</tr>
			<?php endif; ?>

			<?php if ( $order->get_billing_phone() ) : ?>
				<tr>
					<th><?php esc_html_e( 'Phone:', 'knowherepro' ); ?></th>
					<td><?php echo esc_html( $order->get_billing_phone() ); ?></td>
				</tr>
			<?php endif; ?>

			<?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>

		</table>

	</div>

</section>

<?php if ( $show_shipping ) : ?>

	<section class="woocommerce-billing-address woocommerce-columns woocommerce-columns--2 woocommerce-columns--addresses col2-set addresses">

		<div class="woocommerce-column woocommerce-column--1 woocommerce-column--billing-address col-1">

<?php endif; ?>

			<h3 class="woocommerce-column__title"><?php esc_html_e( 'Billing address', 'knowherepro' ); ?></h3>

			<address>
				<?php echo ( $address = $order->get_formatted_billing_address() ) ? $address : __( 'N/A', 'knowherepro' ); ?>
				<?php if ( $order->get_billing_phone() ) : ?>
					<p class="woocommerce-customer-details--phone"><?php echo esc_html( $order->get_billing_phone() ); ?></p>
				<?php endif; ?>
				<?php if ( $order->get_billing_email() ) : ?>
					<p class="woocommerce-customer-details--email"><?php echo esc_html( $order->get_billing_email() ); ?></p>
				<?php endif; ?>
			</address>

<?php if ( $show_shipping ) : ?>

		</div><!-- /.col-1 -->

		<div class="woocommerce-column woocommerce-column--2 woocommerce-column--shipping-address col-2">

			<h3 class="woocommerce-column__title"><?php esc_html_e( 'Shipping address', 'knowherepro' ); ?></h3>

			<address>
				<?php echo ( $address = $order->get_formatted_shipping_address() ) ? $address : __( 'N/A', 'knowherepro' ); ?>
			</address>

		</div><!-- /.col-2 -->

	</section><!-- /.col2-set -->

<?php endif; ?>
