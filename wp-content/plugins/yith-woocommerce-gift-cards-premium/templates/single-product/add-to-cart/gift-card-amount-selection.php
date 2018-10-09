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

if ( $amounts ) : ?>
	<select id="gift_amounts" name="gift_amounts">
		<?php do_action( 'yith_gift_card_amount_selection_first_option', $product ); ?>
		<?php foreach ( $amounts as $value => $item ) :

            $array_price = explode( ">", wc_price ( $item['price'] ) );
			$array_price = explode( "<span", $array_price[1] );
			$price = $array_price[0];

			?>
			<option
				value="<?php echo $item['price']; ?>"
				<?php echo selected( $price, $item['price'], false ); ?>
				data-price="<?php echo $price; ?>"
				data-wc-price="<?php echo strip_tags(wc_price($item['price'])); ?>">
				<?php echo $item['title']; ?>
			</option>
		<?php endforeach; ?>
		<?php do_action( 'yith_gift_card_amount_selection_last_option', $product ); ?>
	</select>
	<?php
endif;

do_action( 'yith_gift_cards_template_after_amounts', $product );
