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

if ( $allow_templates || $allow_customer_images || $allow_use_product_image ) : ?>
	<div class="gift-card-content-editor step-appearance">
        <span class="ywgc-editor-section-title">
            <?php echo apply_filters( 'ywgc_design_section_title', __( "Gift card design", 'yith-woocommerce-gift-cards' ) ); ?>
        </span>

		<!-- Let the user to cancel a selection, turning back to the default design -->
		<input type="button"
		       class="ywgc-choose-image ywgc-default-picture"
		       value="<?php _e( "Default image", 'yith-woocommerce-gift-cards' ); ?>" />
		<input type="hidden" name="ywgc-design-type" id="ywgc-design-type" value="default" />
		<input type="hidden" name="ywgc-template-design" id="ywgc-template-design" value="-1" />

		<!-- Let the user to upload a file to be used as gift card main image -->
		<?php if ( $allow_customer_images ) : ?>
            <input type="button"
                   class="ywgc-choose-image ywgc-custom-picture"
                   value="<?php _e( "Customize", 'yith-woocommerce-gift-cards' ); ?>" />
            <input type="file" name="ywgc-upload-picture" id="ywgc-upload-picture"
                   accept="image/*" />
		<?php endif; ?>
        <?php if ( $allow_use_product_image ) : ?>
            <input type="button"
                   class="ywgc-choose-image ywgc-product-picture"
                   value="<?php _e( "Use product image", 'yith-woocommerce-gift-cards' ); ?>" />
        <?php endif; ?>
        <div id="yith_wc_gift_card_ajax_replace_for_product_image"></div>
		<?php if ( $allow_templates ) : ?>
			<input type="button"
			       class="ywgc-choose-image ywgc-choose-template"
			       href="#ywgc-choose-design"
			       rel="prettyPhoto[ywgc-choose-design]"
			       value="<?php _e( "Choose design", 'yith-woocommerce-gift-cards' ); ?>" />

		<?php endif; ?>
	</div>
<?php endif;
