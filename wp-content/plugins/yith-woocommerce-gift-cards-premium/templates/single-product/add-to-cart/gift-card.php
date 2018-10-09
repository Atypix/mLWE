<?php
/**
 * Gift Card product add to cart
 *
 * @author  Yithemes
 * @package YITH WooCommerce Gift Cards
 * 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**  @var WC_Product_Gift_Card $product */
global $product;

do_action( 'yith_gift_cards_template_before_add_to_cart_form' );
do_action( 'woocommerce_before_add_to_cart_form' );
?>

<form class="gift-cards_form cart" method="post" enctype='multipart/form-data'
      data-product_id="<?php echo absint( yit_get_prop($product, 'id' )); ?>">

	<?php do_action( 'yith_gift_cards_template_after_form_opening' ); ?>

	<?php if ( ! $product->is_purchasable() ) : ?>
		<p class="gift-card-not-valid">
			<?php _e( "This product cannot be purchased", 'yith-woocommerce-gift-cards' ); ?>
		</p>
	<?php else : ?>
		<table class="gift-cards-list" cellspacing="0" <?php if( count( $product->get_amounts_to_be_shown()) == 1 ) echo 'style="display: none"'; ?>>
			<tbody>
			<tr>
				<td class="ywgc-amount-label"><label
						for="gift_amounts"><?php echo __( "Amount", 'yith-woocommerce-gift-cards' ); ?></label>
				</td>
				<td class="ywgc-amount-value">
					<?php do_action('yith_ywgc_show_gift_card_amount_selection', $product); ?>
				</td>
			</tr>
			</tbody>
		</table>

		<?php do_action( 'yith_gift_cards_template_before_add_to_cart_button' ); ?>

		<div class="ywgc-product-wrap" style="display:none;">
			<?php
			/**
			 * yith_gift_cards_template_before_gift_card Hook
			 */
			do_action( 'yith_gift_cards_template_before_gift_card' );

			/**
			 * yith_gift_cards_template_gift_card hook. Used to output the cart button and placeholder for variation data.
			 *
			 * @since  2.4.0
			 * @hooked yith_gift_cards_template_gift_card - 10 Empty div for variation data.
			 * @hooked yith_gift_cards_template_gift_card_add_to_cart_button - 20 Qty and cart button.
			 */
			do_action( 'yith_gift_cards_template_gift_card' );

			/**
			 * yith_gift_cards_template_after_gift_card Hook
			 */
			do_action( 'yith_gift_cards_template_after_gift_card' );
			?>
		</div>

		<?php do_action( 'yith_gift_cards_template_after_add_to_cart_button' ); ?>

	<?php endif; ?>

	<?php do_action( 'yith_gift_cards_template_after_gift_card_form' ); ?>
</form>

<?php do_action( 'yith_gift_cards_template_after_add_to_cart_form' ); ?>
