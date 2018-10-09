<?php
/**
 * Show a section for the automatic discount link and description
 *
 * @author YITHEMES
 * @package yith-woocommerce-gift-cards-premium\templates\emails
 */
if ( ! defined ( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="ywgc-add-cart-discount">
                <span
	                class="ywgc-discount-message"><?php _e( "In order to use this gift card you can enter the gift card code in the appropriate field of the cart page or you can click the following link to obtain the discount automatically.", 'yith-woocommerce-gift-cards' ); ?></span>

	<div class="ywgc-discount-link-section">
		<a class="ywgc-discount-link"
		   href="<?php echo $apply_discount_url; ?>"><?php _e( 'Click here for the discount', 'yith-woocommerce-gift-cards' ); ?></a>
	</div>
</div>
