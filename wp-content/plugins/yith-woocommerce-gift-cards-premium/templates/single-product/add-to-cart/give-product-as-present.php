<?php
/**
 * Variable product add to cart
 *
 * @author  Yithemes
 * @package YITH WooCommerce Gift Cards
 *
 */
if ( ! defined ( 'ABSPATH' ) ) {
    exit;
}

do_action ( 'yith_gift_cards_template_before_add_to_cart_form' );


if ( $product->get_type() == 'variable'){
    ?>
    <button id="give-as-present"
            class="btn btn-ghost give-as-present variable-gift-this-product"><?php echo apply_filters( 'yith_wcgc_gift_this_product_button_label',__( "Gift this product", 'yith-woocommerce-gift-cards' )); ?></button>
    <?php
}
else{
    ?>
    <button id="give-as-present"
            class="btn btn-ghost give-as-present"><?php echo apply_filters( 'yith_wcgc_gift_this_product_button_label',__( YITH_YWGC()->ywgc_gift_this_product_label, 'yith-woocommerce-gift-cards' ) ); ?></button>
    <?php
}
//get_option( 'ywgc_gift_this_product_label' )
YITH_YWGC ()->frontend->show_gift_card_generator ();

$yith_show_gift_this_product_form = ( isset( $_REQUEST[ 'yith-gift-this-product-form' ] ) ? $_REQUEST[ 'yith-gift-this-product-form' ] : 0 );

echo "<input type='hidden' id='yith_show_gift_this_product_form' value='$yith_show_gift_this_product_form'>";