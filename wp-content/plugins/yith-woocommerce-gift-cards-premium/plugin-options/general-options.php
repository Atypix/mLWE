<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
$general_options = array(

	'general' => array(
        /**
         *
         * General settings
         *
         */
		array(
			'name' => __( 'General settings', 'yith-woocommerce-gift-cards' ),
			'type' => 'title',
		),
		'ywgc_permit_free_amount'     => array(
			'name'    => __( 'Allow variable amount option on every kind of gift card', 'yith-woocommerce-gift-cards' ),
			'type'    => 'checkbox',
			'id'      => 'ywgc_permit_free_amount',
			'desc'    => __( 'Allow users to enter the amount they want to gift on the gift card page.', 'yith-woocommerce-gift-cards' ),
			'default' => 'no',
		),
        'ywgc_permit_free_amount_desc' => array(
            'type'             => 'yith-field',
            'yith-type'        => 'html',
            'yith-display-row' => true,
            'html'             => "<span class='yith_wc_gift_card_admin_menu_additional_description yith_wc_gift_card_admin_menu_additional_description_margin_higher_top'>" . __( 'On the product page, users will see a field where they can enter the amount of their gift card before adding it to the cart. You can, however, enable and disable this feature at product level from the product edit page.', 'yith-woocommerce-gift-cards' ) . "</span>",
        ),
        'ywgc_minimal_amount_gift_card'           => array(
            'id'      => 'ywgc_minimal_amount_gift_card',
            'name'    => __( 'Minimal manual amount of a gift card', 'yith-woocommerce-gift-cards' ),
            'desc'    => __( "Choose the minimal manual amount for a gift card. This amount will be applied for all the gift cards and the user will get a message indicating the minimum amount expected.", 'yith-woocommerce-gift-cards' ),
            'type'    => 'number',
        ),
        'ywgc_enable_send_later'      => array(
            'name'    => __( 'Enable "Send later"', 'yith-woocommerce-gift-cards' ),
            'type'    => 'checkbox',
            'id'      => 'ywgc_enable_send_later',
            'desc'    => __( 'Allow users to send the gift card they are purchasing at a later date', 'yith-woocommerce-gift-cards' ),
            'default' => 'no',
        ),
        'ywgc_enable_send_later_desc' => array(
            'type'             => 'yith-field',
            'yith-type'        => 'html',
            'yith-display-row' => true,
            'html'             => "<span class='yith_wc_gift_card_admin_menu_additional_description yith_wc_gift_card_admin_menu_additional_description_margin_top'>" . __( 'If you enable this option, users will be able to pick a date and let the gift card email be sent on that day.', 'yith-woocommerce-gift-cards' ) . "<span style='display: block;'> " . __( 'You will no longer forget a birthday nor any other event!', 'yith-woocommerce-gift-cards' ) . " </span></span>",
        ),
        'ywgc_allow_multi_recipients' => array(
            'name'    => __( 'Allow multiple recipients on virtual gift cards', 'yith-woocommerce-gift-cards' ),
            'type'    => 'checkbox',
            'id'      => 'ywgc_allow_multi_recipients',
            'desc'    => __( 'Allows you to set multiple recipients for single gift cards', 'yith-woocommerce-gift-cards' ),
            'default' => 'yes',
        ),
        'ywgc_allow_multi_recipients_desc' => array(
            'type'             => 'yith-field',
            'yith-type'        => 'html',
            'yith-display-row' => true,
            'html'             => "<span class='yith_wc_gift_card_admin_menu_additional_description yith_wc_gift_card_admin_menu_additional_description_margin_higher_top'>" . __( 'If you enable this option, users will be able to send the same gift card to different recipients.', 'yith-woocommerce-gift-cards' ) . "<span style='display: block;'> <span class='yith_wc_gift_card_admin_menu_red_note'>" . __( 'Note', 'yith-woocommerce-gift-cards' ) . "</span>: " . __( 'if they buy a $50 gift card for 3 different email addresses, there will be 3 gift cards worth $50 each in the cart and they will be charged $150 in total.', 'yith-woocommerce-gift-cards' ) . " </span></span>",
        ),
        'ywgc_usage_expiration'       => array(
            'id'                => 'ywgc_usage_expiration',
            'name'              => __( 'Gift cad expiration date', 'yith-woocommerce-gift-cards' ),
            'desc'              => "<span>" . __( 'Set the number of months after which a gift card expires. To never let the gift card expire, enter 0.', 'yith-woocommerce-gift-cards' ) . "<br><span style='display: block;'> <span class='yith_wc_gift_card_admin_menu_red_note'>" . __( 'Note', 'yith-woocommerce-gift-cards' ) . "</span>: " . __( 'expired gift cards cannot be re-used even though they are re-activated.', 'yith-woocommerce-gift-cards' ) . " </span></span>",
            'type'              => 'number',
            'default'           => 0,
            'custom_attributes' => array(
                'min' => 0,
            )
        ),
        'ywgc_show_recipient_on_cart' => array(
            'id'      => 'ywgc_show_recipient_on_cart',
            'name'    => __( "Recipient's name and email address", 'yith-woocommerce-gift-cards' ),
            'desc'    => __( "Show recipient's name and email address in the cart.", 'yith-woocommerce-gift-cards' ),
            'type'    => 'checkbox',
            'default' => 'no',
        ),
        'ywgc_show_recipient_on_cart_desc' => array(
            'type'             => 'yith-field',
            'yith-type'        => 'html',
            'yith-display-row' => true,
            'html'             => "<span class='yith_wc_gift_card_admin_menu_additional_description yith_wc_gift_card_admin_menu_additional_description_margin_higher_top'>" . __( 'If you enable this option, the Recipient\'s Name and Email Address will show up in the cart next to the Gift card product.', 'yith-woocommerce-gift-cards' ),
        ),
        'ywgc_auto_discount'          => array(
            'name'    => __( 'Auto-apply the Gift Card discount through link', 'yith-woocommerce-gift-cards' ),
            'type'    => 'checkbox',
            'id'      => 'ywgc_auto_discount',
            'desc'    => __( 'Allow gift card recipients to get the gift card discount automatically by simply clicking on the link in the email they have received', 'yith-woocommerce-gift-cards' ),
            'default' => 'no',
        ),
        'ywgc_auto_discount_desc' => array(
            'type'             => 'yith-field',
            'yith-type'        => 'html',
            'yith-display-row' => true,
            'html'             => "<span class='yith_wc_gift_card_admin_menu_additional_description yith_wc_gift_card_admin_menu_additional_description_margin_higher_top'>" . __( 'If you enable this option, the gift card recipients will be able to click on the link in the gift card email they\'ve received and the gift card amount will automatically be deducted from the cart total, without entering any code. The gift card will be applied to guest users too.', 'yith-woocommerce-gift-cards' ),
        ),
		'ywgc_physical_details'    => array(
			'name'    => __( 'Allow Recipient details for Physical gift card', 'yith-woocommerce-gift-cards' ),
			'type'    => 'checkbox',
			'id'      => 'ywgc_physical_details',
			'desc'    => __( 'Add a form to the physical gift cards with; recipient name, sender name and message', 'yith-woocommerce-gift-cards' ),
			'default' => 'no',
		),
		'ywgc_physical_details_mandatory'    => array(
			'name'    => __( 'Physical recipient details is mandatory', 'yith-woocommerce-gift-cards' ),
			'type'    => 'checkbox',
			'id'      => 'ywgc_physical_details_mandatory',
			'desc'    => __( 'Choose if the recipient name is mandatory for physical gift cards.', 'yith-woocommerce-gift-cards' ),
			'default' => 'no',
		),
        'ywgc_minimal_car_total'           => array(
            'id'      => 'ywgc_minimal_car_total',
            'name'    => __( 'Minimal amount on the cart', 'yith-woocommerce-gift-cards' ),
            'desc'    => __( "Choose the minimal amount of the total of the cart to allow the gift card to be applied. Leave it blank or enter 0 not to apply minimal amount", 'yith-woocommerce-gift-cards' ),
            'type'    => 'number',
        ),
        array(
            'type' => 'sectionend',
        ),

        /**
         *
         * Gift this product
         *
         */

        array(
            'name' => __( 'Gift this product', 'yith-woocommerce-gift-cards' ),
            'type' => 'title',
        ),
        'ywgc_permit_its_a_present'   => array(
            'name'    => __( 'Enable "Gift this product" option', 'yith-woocommerce-gift-cards' ),
            'type'    => 'checkbox',
            'id'      => 'ywgc_permit_its_a_present',
            'desc'    => __( 'Allow users to buy a gift card with the same amount of the product they would like to gift.', 'yith-woocommerce-gift-cards' ),
        ),
        'ywgc_permit_its_a_present_desc' => array(
            'type'             => 'yith-field',
            'yith-type'        => 'html',
            'yith-display-row' => true,
            'html'             => "<span class='yith_wc_gift_card_admin_menu_additional_description yith_wc_gift_card_admin_menu_additional_description_margin_higher_top'>" . __( 'This feature appears on the product page. The product chosen will be suggested in the recipient\'s email message together with the gift card. Note: this option can only be enabled on simple and variable products. It doesn\'t work on gift-card products. ', 'yith-woocommerce-gift-cards' ) . "</span>",
        ),
        'ywgc_gift_this_product_label'           => array(
            'id'      => 'ywgc_gift_this_product_label',
            'name'    => __( 'Button label on product page', 'yith-woocommerce-gift-cards' ),
            'desc'    => __( "Choose the name of the button for the feature 'gift this product' which will appears on the product page", 'yith-woocommerce-gift-cards' ),
            'type'    => 'text',
            'default' => __( 'Gift this product', 'yith-woocommerce-gift-cards' ),
        ),
        'ywgc_permit_its_a_present_shop_page'   => array(
            'name'    => __( 'Shop page button', 'yith-woocommerce-gift-cards' ),
            'type'    => 'checkbox',
            'id'      => 'ywgc_permit_its_a_present_shop_page',
            'desc'    => __( 'Display the button on the shop page.', 'yith-woocommerce-gift-cards' ),
            'default' => 'no',
        ),
        'ywgc_permit_its_a_present_shop_page_desc' => array(
            'type'             => 'yith-field',
            'yith-type'        => 'html',
            'yith-display-row' => true,
            'html'             => "<span class='yith_wc_gift_card_admin_menu_additional_description yith_wc_gift_card_admin_menu_additional_description_margin_top ywgc_permit_its_a_present_shop_page_desc'>" . __( 'This button redirects you to the product page and opens automatically the gift this product form. ', 'yith-woocommerce-gift-cards' ) . "</span>",
        ),
        'ywgc_gift_this_product_add_to_cart'   => array(
            'name'    => __( 'Email button actions', 'yith-woocommerce-gift-cards' ),
            'type'    => 'checkbox',
            'id'      => 'ywgc_gift_this_product_add_to_cart',
            'desc'    => __( 'Add the product to the cart', 'yith-woocommerce-gift-cards' ),
        ),
        'ywgc_gift_this_product_apply_gift_card'   => array(
            'name'    => '',
            'type'    => 'checkbox',
            'id'      => 'ywgc_gift_this_product_apply_gift_card',
            'desc'    => __( 'Apply automatically the discount', 'yith-woocommerce-gift-cards' ),
        ),
        'ywgc_gift_this_product_email_button_actions_desc' => array(
            'type'             => 'yith-field',
            'yith-type'        => 'html',
            'yith-display-row' => true,
            'html'             => "<span class='yith_wc_gift_card_admin_menu_additional_description ywgc_gift_this_product_email_button_actions_desc'>" . __( 'Configure which actions will be performed when the button in the gift card email is clicked.', 'yith-woocommerce-gift-cards' ) . "</span>",
        ),
        'ywgc_gift_this_product_email_button_redirect'                    => array(
            'name'    => __( 'Email button redirect', 'yith-woocommerce-gift-cards' ),
            'type'    => 'radio',
            'id'      => 'ywgc_gift_this_product_email_button_redirect',
            'options' => array(
                'prodcut_page'     => __( "Product page", 'yith-woocommerce-gift-cards' ),
                'customize_page'   => __( "Choose a page", 'yith-woocommerce-gift-cards' ),
            ),
            'default' => 'prodcut_page',
        ),
        'ywgc_gift_this_product_redirected_page' => array(
            'name'     => __( '', 'yith-woocommerce-gift-cards' ),
            'desc'     => __( 'Set the page you want the user to be redirected when the button in the gift card email is clicked.', 'yith-woocommerce-gift-cards' ),
            'id'       => 'ywgc_gift_this_product_redirected_page',
            'type'     => 'single_select_page',
            'default'  => '',
            'class'    => 'chosen_select_nostd',
            'css'      => 'min-width:300px;',
            'desc_tip' => false,
        ),
        'ywgc_gift_this_product_email_button_label'           => array(
            'id'      => 'ywgc_gift_this_product_email_button_label',
            'name'    => __( 'Email button label', 'yith-woocommerce-gift-cards' ),
            'desc'    => __( "Choose the name of the gift card email button ", 'yith-woocommerce-gift-cards' ),
            'type'    => 'text',
            'default' => __( 'Go to the site', 'yith-woocommerce-gift-cards' ),
        ),
		array(
			'type' => 'sectionend',
		),

        /**
         *
         * Design and images
         *
         */

        array(
            'name' => __( 'Design and images', 'yith-woocommerce-gift-cards' ),
            'type' => 'title',
        ),
        'ywgc_custom_design'          => array(
            'name'    => __( 'Custom image', 'yith-woocommerce-gift-cards' ),
            'type'    => 'checkbox',
            'id'      => 'ywgc_custom_design',
            'desc'    => __( 'Allow users to upload a custom image that can be used as gift card', 'yith-woocommerce-gift-cards' ),
            'default' => 'yes',
        ),
        'ywgc_custom_design_desc' => array(
            'type'             => 'yith-field',
            'yith-type'        => 'html',
            'yith-display-row' => true,
            'html'             => "<span class='yith_wc_gift_card_admin_menu_additional_description yith_wc_gift_card_admin_menu_additional_description_margin_top'>" . __( 'If you enable this option, users will be able to upload an image and use it as a gift card. Excellent for anniversaries.', 'yith-woocommerce-gift-cards' ) . "<span style='display: block;'> <span class='yith_wc_gift_card_admin_menu_red_note'>" . __( 'Note', 'yith-woocommerce-gift-cards' ) . "</span>: " . __( 'This option works only with virtual gift cards. Allowed extensions: .jpg, .jpeg, .png, .gif and .bmp.', 'yith-woocommerce-gift-cards' ) . " </span></span>",
        ),
        'ywgc_custom_image_max_size' => array (
            'name'              => __ ( 'Image max size', 'yith-woocommerce-gift-cards' ),
            'type'              => 'number',
            'id'                => 'ywgc_custom_image_max_size',
            'desc'              => __ ( 'Set up a limit (in MB) to the size of the custom images uploaded by customers. Enter 0 if you don\'t want to set any limit.', 'yith-woocommerce-gift-cards' ),
            'custom_attributes' => array (
                'min'      => 0,
                'step'     => 1,
                'required' => 'required',
            ),
            'default'           => 1,
        ),
        'ywgc_permit_its_a_present_product_image'   => array(
            'name'    => __( 'Enable "Use product image" option', 'yith-woocommerce-gift-cards' ),
            'type'    => 'checkbox',
            'id'      => 'ywgc_permit_its_a_present_product_image',
            'desc'    => __( 'Allow users to use the product image as main gift card image.', 'yith-woocommerce-gift-cards' ),
            'default' => 'no',
        ),
        'ywgc_permit_its_a_present_product_image_desc' => array(
            'type'             => 'yith-field',
            'yith-type'        => 'html',
            'yith-display-row' => true,
            'html'             => "<span class='yith_wc_gift_card_admin_menu_additional_description yith_wc_gift_card_admin_menu_additional_description_margin_higher_top'>" . __( 'If you enable this option, a button will appear on the product page. The product image will be used to create a notification email for the gift card recipient.', 'yith-woocommerce-gift-cards' ) . "</span>",
        ),
        'ywgc_template_design'        => array(
            'name'    => __( 'Enable photo gallery', 'yith-woocommerce-gift-cards' ),
            'type'    => 'checkbox',
            'id'      => 'ywgc_template_design',
            'desc'    => __( 'Allow users to choose the gift card image among the ones in the gallery.', 'yith-woocommerce-gift-cards' ) .
                ' <a href="' . admin_url( 'edit-tags.php?taxonomy=giftcard-category&post_type=attachment' ) . '" title="' . __( 'Set your template categories', 'yith-woocommerce-gift-cards' ) . '">' . __( 'Set your template categories', 'yith-woocommerce-gift-cards' ) . '</a>',
            'default' => 'yes',
        ),
        'ywgc_template_design_desc' => array(
            'type'             => 'yith-field',
            'yith-type'        => 'html',
            'yith-display-row' => true,
            'html'             => "<span class='yith_wc_gift_card_admin_menu_additional_description yith_wc_gift_card_admin_menu_additional_description_margin_top'> <span class='yith_wc_gift_card_admin_menu_red_note'>" . __( 'Note', 'yith-woocommerce-gift-cards' ) . "</span>: " . __( 'Images that can be used by customers have to be uploaded through the', 'yith-woocommerce-gift-cards' ) . " <a href='" . admin_url( 'upload.php' ) . "' title='" . __( 'media gallery', 'yith-woocommerce-gift-cards' ) . "'>" . __( 'media gallery', 'yith-woocommerce-gift-cards' ) . "</a>. " . __( 'To make the search easier, you can group gallery images in categories (Christmas, Easter, Birthday etc.) by using this link:', 'yith-woocommerce-gift-cards' ) . " <a href='" . admin_url( 'edit-tags.php?taxonomy=giftcard-category&post_type=attachment' ) . "' title='" . __( 'Set your template categories', 'yith-woocommerce-gift-cards' ) . "'>" . __( 'Set your gallery categories', 'yith-woocommerce-gift-cards' ) . "</a></span>",
        ),
        'ywgc_show_preset_title'      => array(
            'id'      => 'ywgc_show_preset_title',
            'name'    => __( 'Image title', 'yith-woocommerce-gift-cards' ),
            'desc'    => __( 'Show the image title in the photo gallery.', 'yith-woocommerce-gift-cards' ),
            'type'    => 'checkbox',
            'default' => 'no',
        ),
        'ywgc_show_preset_title_desc' => array(
            'type'             => 'yith-field',
            'yith-type'        => 'html',
            'yith-display-row' => true,
            'html'             => "<span class='yith_wc_gift_card_admin_menu_additional_description yith_wc_gift_card_admin_menu_additional_description_margin_top'>" . __( 'If you enable this option, the image title will appear below every photo/image in the gallery.', 'yith-woocommerce-gift-cards' ) . "</span>",
        ),
        array(
            'type' => 'sectionend',
        ),

        /**
         *
         * Gift card code
         *
         */

        array(
            'name' => __( 'Gift card code', 'yith-woocommerce-gift-cards' ),
            'type' => 'title',
        ),
        'ywgc_enable_pre_printed'     => array(
            'name'    => __( 'Manual code generation', 'yith-woocommerce-gift-cards' ),
            'type'    => 'checkbox',
            'id'      => 'ywgc_enable_pre_printed',
            'desc'    => __( 'Do not generate any gift card code.', 'yith-woocommerce-gift-cards' ),
            'default' => 'no',
        ),
        'ywgc_enable_pre_printed_desc' => array(
            'type'             => 'yith-field',
            'yith-type'        => 'html',
            'yith-display-row' => true,
            'html'             => "<span class='yith_wc_gift_card_admin_menu_additional_description yith_wc_gift_card_admin_menu_additional_description_margin_top'>" . __( 'If you enable this option, the gift card code is not automatically generated during the purchase. Administrators can enter the code before completing the order. This option has to be used if you have pre-printed gift cards.', 'yith-woocommerce-gift-cards' ) . "</span>",
        ),
        'ywgc_code_pattern'           => array(
            'id'      => 'ywgc_code_pattern',
            'name'    => __( 'Code pattern', 'yith-woocommerce-gift-cards' ),
            'desc'    => __( "Choose the pattern of new gift card codes. Either use '*' that will be replaced by a random character or 'D' by a single digit. Leave blank for default pattern '****-****-****-****'.", 'yith-woocommerce-gift-cards' ),
            'type'    => 'text',
            'default' => '****-****-****-****',
        ),
        array(
            'type' => 'sectionend',
        ),

        /**
         *
         * Gift card orders
         *
         */

        array(
            'name' => __( 'Gift card orders', 'yith-woocommerce-gift-cards' ),
            'type' => 'title',
        ),
        'ywgc_order_cancelled_action' => array(
            'name'    => __( 'Action to perform on order cancelled', 'yith-woocommerce-gift-cards' ),
            'type'    => 'select',
            'id'      => 'ywgc_order_cancelled_action',
            'desc'    => __( 'Choose what happens to gift cards purchased through orders that go to Cancelled', 'yith-woocommerce-gift-cards' ),
            'options' => array(
                'nothing' => __( 'Do nothing', 'yith-woocommerce-gift-cards' ),
                'disable' => __( 'Disable the gift cards', 'yith-woocommerce-gift-cards' ),
                'dismiss' => __( 'Dismiss the gift cards', 'yith-woocommerce-gift-cards' ),
            ),
            'default' => 'nothing',
        ),
        'ywgc_order_refunded_action'  => array(
            'name'    => __( 'Action to perform on order refunded', 'yith-woocommerce-gift-cards' ),
            'type'    => 'select',
            'id'      => 'ywgc_order_refunded_action',
            'desc'    => __( 'Choose what happens to gift cards purchased through orders that go to Refunded', 'yith-woocommerce-gift-cards' ),
            'options' => array(
                'nothing' => __( 'Do nothing', 'yith-woocommerce-gift-cards' ),
                'disable' => __( 'Disable the gift cards', 'yith-woocommerce-gift-cards' ),
                'dismiss' => __( 'Dismiss the gift cards', 'yith-woocommerce-gift-cards' ),
            ),
            'default' => 'nothing',
        ),
        array(
            'type' => 'sectionend',
        ),

        /**
         *
         * Emails and notificatoins
         *
         */

        array(
            'name' => __( 'Emails and notificatoins', 'yith-woocommerce-gift-cards' ),
            'type' => 'title',
        ),
        'ywgc_notify_customer'        => array(
            'name'    => __( 'Purchase notification', 'yith-woocommerce-gift-cards' ),
            'type'    => 'checkbox',
            'id'      => 'ywgc_notify_customer',
            'desc'    => __( 'Allow buyers to get a notification when the gift card is used.', 'yith-woocommerce-gift-cards' ),
            'default' => 'no',
        ),
        'ywgc_notify_customer_desc' => array(
            'type'             => 'yith-field',
            'yith-type'        => 'html',
            'yith-display-row' => true,
            'html'             => "<span class='yith_wc_gift_card_admin_menu_additional_description yith_wc_gift_card_admin_menu_additional_description_margin_top'>" . __( 'If you enable this option, a notification will be sent to the person who bought the gift card when the gift card is used by the recipient.', 'yith-woocommerce-gift-cards' ) . "</span>",
        ),
        'ywgc_blind_carbon_copy'      => array(
            'name'    => __( 'Gift card code notification', 'yith-woocommerce-gift-cards' ),
            'type'    => 'checkbox',
            'id'      => 'ywgc_blind_carbon_copy',
            'desc'    => __( 'Allow buyers to receive a BBC email with the gift card code.', 'yith-woocommerce-gift-cards' ),
            'default' => 'no',
        ),
        'ywgc_blind_carbon_copy_desc' => array(
            'type'             => 'yith-field',
            'yith-type'        => 'html',
            'yith-display-row' => true,
            'html'             => "<span class='yith_wc_gift_card_admin_menu_additional_description yith_wc_gift_card_admin_menu_additional_description_margin_top'>" . __( 'Enable this option to let the gift card buyer be notified of the gift card code via a blind carbon copy email.', 'yith-woocommerce-gift-cards' ) . "</span>",
        ),
        array(
            'type' => 'sectionend',
        ),

	),
);

return $general_options;
