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
} ?>

<div class="gift-card-content-editor step-content">
	<span class="ywgc-editor-section-title"><?php echo apply_filters('ywgc_editor_section_title', __( "Gift card details", 'yith-woocommerce-gift-cards' )); ?></span>

	<label
		for="ywgc-recipient-email">
		<?php if ( $ywgc_physical_details_mandatory ) {
			echo apply_filters('ywgc_editor_mandatory_recipient_email_label',__( "Recipient's details (*)", 'yith-woocommerce-gift-cards') );
		} else {
			echo apply_filters('ywgc_editor_recipient_email_label',__( "Recipient's details", 'yith-woocommerce-gift-cards' ));
		}
		?></label>


	<div class="ywgc-single-recipient">

		<input type="text" name="ywgc-recipient-name[]" placeholder="<?php _e( "name", 'yith-woocommerce-gift-cards' ); ?>" <?php echo ( $ywgc_physical_details_mandatory && ! $gift_this_product ) ? 'required' : ''; ?> class="ywgc-recipient yith_wc_gift_card_input_recipient_details">

		<a href="#" class="ywgc-remove-recipient hide-if-alone">x</a>

	</div>

	<?php
	//Only with gift card product type you can use multiple recipients
	if ( $allow_multiple_recipients ) : ?>
		<a href="#" class="add-physical-recipient"
		   id="add_recipient"><?php _e( "Add another recipient", 'yith-woocommerce-gift-cards' ); ?></a>
	<?php endif; ?>

	<div class="ywgc-sender-name">
		<label
			for="ywgc-sender-name"><?php echo apply_filters('ywgc_sender_name_label',__( "Your name", 'yith-woocommerce-gift-cards' )); ?></label>
		<input type="text" name="ywgc-sender-name" id="ywgc-sender-name" value="<?php echo apply_filters('ywgc_sender_name_value','') ?>">
	</div>
	<div class="ywgc-message">
		<label
			for="ywgc-edit-message"><?php echo apply_filters('ywgc_edit_message_label',__( "Message", 'yith-woocommerce-gift-cards' )); ?></label>
		<textarea id="ywgc-edit-message" name="ywgc-edit-message" rows="5"
		          placeholder="<?php echo apply_filters( 'ywgc_edit_message_placeholder', __( "Your message...", 'yith-woocommerce-gift-cards' )); ?>"></textarea>
	</div>
	
</div>