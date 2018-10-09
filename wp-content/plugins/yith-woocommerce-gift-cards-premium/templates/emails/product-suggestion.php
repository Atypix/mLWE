<?php
/**
 * Show a section with a product suggestion if the gift card was purchased as a gift for a product in the shop
 *
 * @author  YITHEMES
 * @package yith-woocommerce-gift-cards-premium\templates\emails
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$is_wc_ge3 = version_compare( WC()->version, '3.0', '>=' );

$args = array(
    YWGC_ACTION_ADD_DISCOUNT_TO_CART => $gift_card->gift_card_number,
    YWGC_ACTION_VERIFY_CODE          => YITH_YWGC ()->hash_gift_card ( $gift_card ),
    YWGC_ACTION_PRODUCT_ID           => $product->get_id(),
);

if ( get_option ( 'ywgc_gift_this_product_email_button_redirect' ) == 'customize_page' )
    $product_link = esc_url ( add_query_arg ( $args, get_page_link( get_option ( 'ywgc_gift_this_product_redirected_page' ) ) ) );
else
    $product_link = esc_url ( add_query_arg ( $args, get_permalink( yit_get_prop( $product, 'id' ) ) ) );

?>
<div class="ywgc-product-suggested">
	<span class="ywgc-suggested-text">
		<?php echo sprintf( __( "%s would like to suggest you to use this gift card to purchase the following product:", 'yith-woocommerce-gift-cards' ), $gift_card->sender_name ); ?>
	</span>

    <div style="overflow: hidden">
        <img class="ywgc-product-image"
             src="<?php echo $product->get_image_id() ? current( wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' ) ) : wc_placeholder_img_src(); ?>" />

        <div class="ywgc-product-description">
            <span class="ywgc-product-title"><?php echo $product->get_title(); ?></span>

            <div
                class="ywgc-product-excerpt"><?php echo wp_trim_words( $is_wc_ge3 ? $product->get_short_description() : $product->post->post_excerpt, 20 ); ?></div>

            <a class="ywgc-product-link" href="<?php echo $product_link; ?>">
				<?php echo ( empty( get_option ( 'ywgc_gift_this_product_email_button_label' ) ) ? 'Go to the product' : get_option ( 'ywgc_gift_this_product_email_button_label' ) ); ?></a>
        </div>
    </div>
</div>