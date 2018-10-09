<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined ( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
$template_options = array (

    'template' => array (

        array (
            'name' => __ ( 'Template settings', 'yith-woocommerce-gift-cards' ),
            'type' => 'title',
        ),
        'ywgc_shop_name'             => array (
            'name' => __ ( 'Shop name', 'yith-woocommerce-gift-cards' ),
            'type' => 'text',
            'id'   => 'ywgc_shop_name',
            'desc' => __ ( 'Set the name of the shop for the email sent to the customer.', 'yith-woocommerce-gift-cards' ),
        ),
        'ywgc_shop_logo_url'         => array (
            'name' => __ ( 'Shop logo', 'yith-woocommerce-gift-cards' ),
            'type' => 'ywgc_upload_image',
            'id'   => 'ywgc_shop_logo_url',
            'desc' => __ ( 'Set the logo of the shop you want to show in the gift card sent to customers. The logo will be showed with a maximum size of 100x60 pixels.', 'yith-woocommerce-gift-cards' ),
        ),
        'ywgc_shop_logo_on_gift_card' => array(
            'name' => __('Shop logo in gift card', 'yith-woocommerce-gift-cards'),
            'type' => 'checkbox',
            'id' => 'ywgc_shop_logo_on_gift_card',
            'desc' => __('Set if the shop logo should be shown on the gift card template. Disable it if for example, your gift cards template image contains your shop logo', 'yith-woocommerce-gift-cards'),
            'default' => 'yes',
        ),
        'yith_gift_card_header_url'  => array (
            'name' => __ ( 'Logo of the gift card product', 'yith-woocommerce-gift-cards' ),
            'type' => 'ywgc_upload_image',
            'id'   => 'ywgc_gift_card_header_url',
            'desc' => __ ( 'Select the logo of a default gift card product', 'yith-woocommerce-gift-cards' ),
        ),
        'ywgc_template_style'  => array (
            'name' => __ ( 'Template style', 'yith-woocommerce-gift-cards' ),
            'type' => 'radio',
            'options' => array (
                'style1' => __ ( 'Style 1', 'yith-woocommerce-gift-cards' ),
                'style2' => __ ( 'Style 2', 'yith-woocommerce-gift-cards' ),
            ),
            'default' => 'style1',
            'id'   => 'ywgc_template_style',
            'desc' => __ ( 'Select the style of the gift card template', 'yith-woocommerce-gift-cards' ),
        ),
        array (
            'type' => 'sectionend',
        ),
    ),
);


return $template_options;


