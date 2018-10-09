=== YITH WooCommerce Gift Cards Premium ===

Contributors: yithemes
Tags: gift card, gift cards, coupon, gift, discount
Requires at least: 4.0
Tested up to: 4.9.x
Stable tag: 2.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Documentation: https://docs.yithemes.com/yith-woocommerce-gift-cards/

== Changelog ==

= Version 2.0.0 - Released: Aug 09, 2018 =

* New: Settings Admin panel
* New: PHP UNIT
* Tweak: plugin action links and row metas
* Tweak: prevent error with gift this product button on shop loop
* Tweak: added new status to the gift card table
* Tweak: Check the recipient names as array
* Update: Spanish translation
* Update: Dutch translation
* Updated: official documentation url of the plugin
* Fix: Fixed a issue with the AJAX Call in Frontend
* Fix: string gft to gift on privacy policy content
* Fix: Change the hook of the gift this product button in variable products
* Dev: adding the product to the gift this product template
* Dev: fixed the filter to the variation products with gift this product button
* Dev: checking YITH_Privacy_Plugin_Abstract for old plugin-fw versions
* Dev: adding new filters in the checkout order tables

= Version 1.8.5 - Released: May 28, 2018 =

* GDPR:
   - New: exporting user question and answers data info
   - New: erasing user question and answers data info
   - New: privacy policy content

= Version 1.8.4 - Released: Feb 12, 2018 =

* New: added a filter to register the script URL
* Fix: hide select in case of single amount in product page
* Fix: error when try to upload an image and the limit is 0
* Dev: added a new filter in to the shop url in email button

= Version 1.8.3 - Released: Feb 07, 2018 =

Fix: Aelia compatibility when adding to the cart selecting amount of a gift card and gift this product
Fix: Wrong calculation on shipping total row
Tweak: checking if the gift card is object

= Version 1.8.2 - Released: Jan 29, 2018 =
New: support to WooCommerce 3.3-RC2
New: show gift card code in order detail page inside my account section
New: plugin fw version 3.0.10
Update: french language
Tweak: Adding 'use product image' for virtual gift cards
Dev: new filter 'ywgc_remove_gift_card_text'
Dev: new filter 'ywgc_checkout_enter_code_text'
Fix: fatal error showing quantity table with Dynamic Pricing and Discounts plugin
Fix: fatal error get_default_subject (for WooCommerce version minor of 3.1)


= Version 1.8.1 - Released: Dic 21, 2017 =

* New: Apply filters yith_ywgc_give_product_as_present
* Update: Plugin core framework version 3
* Update: French language files
* Dev: filter yith_ywgc_preset_image_size
* Dev: added second argument $args on filter yith_ywgc_email_automatic_cart_discount_url
* Dev: Create YWGC_META_GIFT_CARD_CODE for a order created
* Dev: new filter "yith_ywgc_update_totals_calculate_taxes"
* Dev: override method get_subject() inside class YITH_YWGC_Email_Send_Gift_Card
* Fix: PayPal error when gift discount can’t pay total shipping costs
* Fix: use the featured image when no image selected
* Fix: usage of nl2br() function to show the gift card message
* Fix: subscription totals
* Fix: Compatibility with YITH WooCommerce Subscription Premium
* Fix: error_log in code
* Fix: Compatibility Aelia: Converting the price in the gift cards of my account
* Fix: Remove “selected more than one recipient” for Safari

= Version 1.8.0 - Released: Nov 23, 2017 =

* New: Adding recipient details to physical gift cards
* Fix: Avoid new gift cards with the same name
* Update: Dutch translations
* Fix: Gift card default hidden product is indexed by Google

= Version 1.7.13 - Released: Nov 20, 2017 =

* New: Norwegian translations thanks to Rune Kristoffersen
* Fix: Coupons behaviour

= Version 1.7.12 - Released: Nov 16, 2017 =

* Fix: Coupons not correctly applied with gift cards in cart and checkout
* New: Partial French translations thanks to Yaël Fazy
* Fix: Warning on class-yith-ywgc-backend-premium.php with some php configuration


= Version 1.7.11 - Released: Nov 15, 2017 =

* Fix: PHP Fatal error "Call to undefined method WC_Cart::get_shipping_tax()"


= Version 1.7.10 - Released: Nov 15, 2017 =

* New: German translation (thanks to Wolfgang Männel)
* New: Dutch translation
* Dev: new filter 'ywgc_email_image_size'
* Fix: gift card discount is not calculate correctly
* Fix: scroll on choose design modal window


= Version 1.7.9 - Released: Nov 10, 2017 =
* Fix: shipping totals not included in gift card discount

= Version 1.7.8 - Released: Nov 09, 2017 =
* Tweak: My Account message when no gift cards found
* Fix: Zero amount display for zero balance gift cards
* Fix: Empty 'used' tab in Gift Cards backend panel
* Fix: Gift cards not applied to shipping taxes
* Fix: Error message when try to use a zero balance gift card

= Version 1.7.7 - Released: Nov 08, 2017 =
* New: gift-card endpoint to my-account area
* Update: Moved Gift Cards from my-account dashboard to the new gift-card area
* Fix: Pretty Photo modal position on Gift Card product page
* Fix: Allowing the administrator to use the product image when gifting a product
* Update: Spanish language files


= Version 1.7.6 - Released: Nov 08, 2017 =
* Add: filter ywgc_preview_code_title
* Add: filter ywgc_design_section_title
* Add: filter ywgc_checkout_box_title
* Add: filter ywgc_checkout_box_placeholder
* Add: filter ywgc_checkout_apply_code
* Fix: Zero amount warning for current balance on PHP 7.1.X

= Version 1.7.5 - Released: Nov 07, 2017 =
* Fix: field "name" not showed creating manually a new gift card
* Fix: missed string in yith-woocommerce-gift-cards.pot file
* Update: italian language file
* Update: spanish language file

= Version 1.7.4 - Released: Oct 27, 2017 =
* Fix: Zero amount displayed in gift cards with multiple amounts

= Version 1.7.3 - Released: Oct 26, 2017 =
* Fix: wrong amount added to cart in case of thousands

= Version 1.7.2 - Released: Oct 25, 2017 =
* Fix: price format gift card
* Fix: unminified js file was not updated

= Version 1.7.1 - Released: Oct 24, 2017 =
* New: use product image as gift card image
* Dev: new filter 'yith_ywcgc_attachment_image_url'
* Fix: custom image is not showed on gift card

= Version 1.7.0 - Released: Oct 17, 2017 =

* New: support to WooCommerce 3.2.1
* Updated: language files
* Fix: shop page URL link to get auto discount button
* Fix: gift card code pattern is not set by default
* Fix: Php warning and notices on canceled order without gift cards applied if paying with 2checkout
* Fix: default gift card image is not recovered correctly
* Fix: PayPal error if the shipping total is higher than cart subtotal
* Fix: issue layout when removing a coupon
* Fix: gift card expiration is not set for gift card created manually
* Fix: same name for multiple recipient emails
* Dev: new hook 'yith_wcgc_template_after_logo'
* Dev: new hook 'yith_wcgc_template_after_main_image'
* Dev: new hook 'yith_wcgc_template_after_amount'
* Dev: new hook 'yith_wcgc_template_after_code'
* Dev: new hook 'yith_wcgc_template_after_message'
* Tweak: scheduling cron to check scheduled gift card

= Version 1.6.18 - Released: Oct 17, 2017 =

* New: Add button 'use product image' for gift this product

= Version 1.6.17 - Released: Oct 16, 2017 =

* New: Add email and name for each recipient in virtual gift

= Version 1.6.16 - Released: Sep 19, 2017 =

* New: "expiration date" column in gift card table
* Tweak: button position inside product detail page
* Fix: incorrect gift card amount if all variations have same price and no variation is set as default
* Dev: add $gift_card as parameter for filter 'yith_ywgc_gift_card_email_expiration_message'
* Dev: new filter 'yith_wcgc_date_format'


= Version 1.6.15 - Released: Sep 12, 2017 =

* Fix: wrong order total when the order is saved
* Fix: incorrect gift card amount if all variations have same price
* Dev: new filter ywgc_amount_order_total_item
* Dev: new filter 'ywgc_sender_name_label'
* Dev: new filter 'ywgc_sender_name_value'
* Dev: new filter 'ywgc_edit_message_label'
* Dev: new filter 'ywgc_edit_message_placeholder'
* Dev: new filter 'ywgc_postdated_field_label'
* Dev: new filter 'ywgc_choose_delivery_date_placeholder'
* Dev: new filter 'ywgc_cancel_gift_card_button_text'

= Version 1.6.14 - Released: Sep 07, 2017 =

* Fix: update gift card balance when order status is cancelled or refunded


= Version 1.6.13 - Released: Aug 31, 2017 =

* Fix: order item meta warning
* New: filter 'yith_wcgc_gift_this_product_button_label'
* New: filter 'yith_wcgc_manual_amount_input_placeholder'
* New: filter 'yith_wcgc_manual_amount_option_text'


= Version 1.6.12 - Released: Aug 24, 2017 =

* Tweak: order item meta is shown as string
* Update: language files

= Version 1.6.11 - Released: Aug 09, 2017 =

* Add: Filters on Gift Card Post Type admin page

= Version 1.6.10 - Released: Aug 09, 2017 =

* Fix: wrong amount shown in cart page when using Aelia Currency Switcher plugin
* Fix: 'Add another recipient' string not shonw in gift card page due a typo in gift-card-details.php

= Version 1.6.9 - Released: Jul 03, 2017 =

* New: Support to WooCommerce 3.1
* Fix: disable 'gift this product' button when a variation is not selected.

= Version 1.6.8 - Released: Jun 19, 2017 =

* Update: improved the layout of gift this product section for variable products.
* Fix: wrong CSS prop set in ywgc-frontend.js.
* Fix: gift card image is shown and then hide on product's page when 'Gift this product' option is enabled.

= Version 1.6.7 - Released: May 19, 2017 =

* New: added 'D' placeholder in gift card code pattern, it will replaced by a digit in place of a random letter.
* Fix: missing amount conversion with Aelia Currency Switcher and WooCommerce 3.
* Fix: prevent a Javascript error when WooCommerce trigger 'found_variation' and the variation object is not set.
* Fix: avoid a fatal error when activating the plugin on website with PHP prior than 5.4.

= Version 1.6.6 - Released: May 08, 2017 =

* Fix: 'choose design' button does nothing when clicked, showing the modal window when clicked again.
* Fix: 'choose design' not visible when using the 'gift this product' feature.
* Tweak: improved layout for preset design modal window.

= Version 1.6.5 - Released: Apr 27, 2017 =

* Update: plugin language file.
* Tweak: improved the gift card layout.
* Fix: 'Add gift' text not localizable.
* Fix: billing_first_name property called directly in WooCommerce 3.

= Version 1.6.4 - Released: Apr 21, 2017 =

* Update: plugin-fw
* Fix: using 'Gift this product' feature do not add the product to the cart due to product not purchasable.
* Fix: fatal error due to huge amount of post meta

= Version 1.6.3 - Released: Apr 14, 2017 =

* Fix: 'missing recipient' notice shown when purchasing a physical gift card.
* Fix: broken compatibility with Aelia Currency Switcher.
* Fix: usage notification not sent to the buyer in case of physical gift card.
* Fix: prevent email sending duplicates.
* Dev: use 'ywgc-reset' query string var in order to recover the default gift card product.

= Version 1.6.2 - Released: Apr 11, 2017 =

* New: compatible with both WPML "Product Translation Interface" mode.
* New: template /single-product/add-to-cart/gift-card-add-to-cart.php.
* Update: language files.
* Tweak: purchasable status depends on gift card amounts and to the manual amount status.
* Tweak: gift card template not shown on gift card product page if the product is not purchasable.
* Fix: digital gift card shown as physical product on back end when used with WooCommerce 3.0 or newer.

= Version 1.6.1 - Released: Mar 28, 2017 =

* Fix: removed 'debugger' on front end script
* Fix: updated minified front end script.

= Version 1.6.0 - Released: Mar 27, 2017 =

* New: Support WooCommerce 3.0
* New: apply gift card to the cart totals after applying coupon and calculate taxes.
* New: register gift cards usage as order notes.
* New: set your own pattern for the gift card code to generate.
* New: if the gift card code pattern is not so complex to allow unique code, the gift card code will be created but will be not valid until a manual code is entered.
* New: avoid unique code conflicts for new or edited gift cards
* Remove: option to choose if gift card discount should applies to shipping fee as it is the default behavior.
* Remove: gift card code can no longer be entered in the coupon field.
* Remove: gift cards are no more linked to WooCommerce coupon system.
* Fix: YITH Plugin Framework initialization.
* Fix: gift card usage notification not sent to the buyer

= Version 1.5.16 - Released: Feb 17, 2017 =

* New: add internal notes to gift cards and show them on gift cards table.
* Fix: notice shown when 'free shipping' is the selected shipping method.
* Fix: custom image set from edit product page not saved correctly.
* Fix: the order action for sending gift cards not triggered.
* Fix: wrong amount shown on gift card product page when using third party plugin for currency switching.
* Fix: amount not shown on admin product page when added to the gift card product, needing a page refresh.
* Dev: customize the gift card's parameters with the filter 'yith_ywgc_gift_card_coupon_data' when the gift card code is applied to the cart.

= Version 1.5.15 - Released: Feb 07, 2017 =

* Fix: scheduled gift card email sent to the recipient even if already delivered.

= Version 1.5.14 - Released: Feb 07, 2017 =

* Update: gift-card-details.php template
* Update: language files
* Tweak: in gift card product page, the 'quantity' field and 'add to cart' button are moved under the gift card area
* Fix: scheduled date for gift cards was not set correctly during the purchase.
* Fix: gift card design not enabled for products translated with WPML.
* Fix: gift card manual amount not enabled for products translated with WPML.

= Version 1.5.13 - Released: Feb 02, 2017 =

* New: show message about the expiration date in gift card email.
* Tweak: add Prettyphoto scripts in gift card product page.
* Fix: gift card amounts shown on products translated with WPML.

= Version 1.5.12 - Released: Feb 01, 2017 =

* New: show gift card expiration date in customer gift card table.
* Fix: issues with gift card expiration date.
* Fix: issues with shipping of scheduled gift cards.
* Dev: yith_ywgc_my_gift_cards_columns filter lets third-party plugins customize columns shown on customer gift card table.

= Version 1.5.11 - Released: Jan 27, 2017 =

* Fix: gift card amount ordering was not sorted correctly
* Fix: wrong image on gift cart product pages used when the user clicks on 'default image' button

= Version 1.5.10 - Released: Jan 18, 2017 =

* New: choose if the image title should be shown in design list pop-up
* Dev: the filter 'yith_ywgc_gift_cards_amounts' lets third party plugin to change the content of the amounts dropdown

= Version 1.5.9 - Released: Jan 13, 2017 =

* New: template automatic-discount.php in /templates/emails
* Tweak: footer content in emails can be customized

= Version 1.5.8 - Released: Jan 03, 2017 =

* Fixed: gift card with empty recipient email not added to the cart

= Version 1.5.7 - Released: Jan 02, 2017 =

* Added: gift cards with multi-recipient will be added to the cart one per row
* Added: choose if the recipient email should be shown in cart details

= Version 1.5.6 - Released: Dec 22, 2016 =

* Added: email format validation before adding the gift card to the cart
* Added: set gift card expiration
* Tweaked: improved cart messages when a gift card code is used
* Fixed: gift card email not send to the admin email when BCC option is set

= Version 1.5.5 - Released: Dec 21, 2016 =

* Fixed: wrong amount shown in variable products with 'Gift this product' option
* Fixed: missing currency conversion with Aelia Currency Switcher
* Fixed: sender name not shown in product suggestion template
* Fixed: amount not shown in user currency in email
* Fixed: WPML currency for gift cards
* Fixed: multiple gift cards not added to the cart correctly

= Version 1.5.4 - Released: Dec 15, 2016 =

* Fixed: gift card first usage email should be sent to the customer instead of the gift card recipient
* Fixed: the real gift card image was not emailed to the recipient

= Version 1.5.3 - Released: Dec 14, 2016 =

* Added: email style can be overwritten from the theme
* Fixed: recipient email validation on gift card page

= Version 1.5.2 - Released: Dec 13, 2016 =

* Fixed: 'Send now' not working on manual created digital gift cards

= Version 1.5.1 - Released: Dec 07, 2016 =

* Added: ready for WordPress 4.7

= Version 1.5.0 - Released: Dec 06, 2016 =

* Added: gift cards can be create manually from back end
* Added: gift cards balance can be managed from back end
* Added: gift cards details can be edited from back end
* Added: create your own gift card template overwriting the plugin templates
* Added: allow inventory for gift card products
* Added: allow 'sold individually' for gift cards
* Added: choose if in digital gift cards, the recipient email is mandatory.
* Added: recipient name in gift card product template
* Updated: gift card code are generated as soon as possible after the payment of the order(in 'processing' or 'completed' order status)
* Updated: the sender name is no more mandatory when a digital gift cards is purchased
* Fixed: various issues with Aelia Currency Switcher

= Version 1.4.13 - Released: Nov 18, 2016 =

* Fixed: a console log shown when manual amount option is not set
* Updated: plugin language files

= Version 1.4.12 - Released: Nov 15, 2016 =

* Added: gift cards can be used for paying shipping cost
* Updated: improved checks on the amount entered by the customer in manual amount mode
* Updated: plugin language files
* Fixed: the link for applying the gift card to the cart directly from the cart redirect to wrong page.
* Fixed: notice not shown if WooCommerce was not installed

= Version 1.4.11 - Released: Nov 03, 2016 =

* Fixed: when using the 'gift this product' feature, the amount is not correctly set.

= Version 1.4.10 - Released: Nov 02, 2016 =

* Added: gift card amounts managed through the accounting.js script
* Updated: gift card product page layout, removing default colors and style to let the page being rendered by the theme
* Fixed: gift card amount set to 0 when in manual mode only.

= Version 1.4.9 - Released: Oct 20, 2016 =

* Fixed: backward compatibility with WooCommerce version 2.6.0 or sooner: wrong product title shown when a gift card is added to the cart

= Version 1.4.8 - Released: Oct 11, 2016 =

* Updated: removed duplicated 'Add to cart' button
* Updated: the amounts dropdown is now selected on first item by default
* Updated: the preview layout on single product page
* Fixed: empty product title shown after adding a gift card to cart
* Added: spanish translation files

= Version 1.4.7 - Released: Jul 27, 2016 =

* Updated: Aelia Currency Switcher compatibility to latest plugin version
* Added: multiple BCC recipients for gift cards sold
* Added: template for gift card footer
* Added: template for gift card suggestion section

= Version 1.4.6 - Released: Jul 12, 2016 =

* Fixed: manual amount in physical product do not work
* Fixed: total not updated correctly in mini-cart

= Version 1.4.5 - Released: Jul 05, 2016 =

* Fixed: tab 'general' not visible in gift cards products after the update to WooCommerce 2.6.2

= Version 1.4.4 - Released: Jun 30, 2016 =

* Fixed: the amount shown on mini cart was not converted in current currency when using the Aelia Currency Switcher plugin

= Version 1.4.3 - Released: Jun 30, 2016 =

* Fixed: mini cart amounts not updated when a gift card is added to the cart

= Version 1.4.2 - Released: Jun 28, 2016 =

* Fixed: email footer and header not shown when using the "send now" feature

= Version 1.4.1 - Released: Jun 27, 2016 =

* Updated: do not show the amount dropdown if only manual amount is enabled

= Version 1.4.0 - Released: Jun 14, 2016 =

* Added: WooCommerce 2.6 ready
* Added: set the gift cards product as downloadable to let the payment gateway to set the order as completed when paid
* Fixed: issue that would prevent to edit a gift card when the order was in processing status
* Fixed: a warning was shown in product of type other than the gift cards due to a conflict with YITH Dynamic Pricing
* Fixed: wrong gift card object retrieved then using a numeric gift card code

= Version 1.3.8 - Released: May 18, 2016 =

* Updated: the form-gift-cards.php template file
* Fixed: the discount code was not applied correctly clicking on the email received

= Version 1.3.7 - Released: May 09, 2016 =

* Added: allow manual entered amounts for physical products
* Added: support to WPML Multiple Currency
* Added: let the vendor to manage his own gift cards when YITH Multi Vendor is active
* Added: gift card code fields could be removed from checkout page via a filter
* Fixed: wrong amount value retrieved when reading old gift cards

= Version 1.3.6 - Released: May 05, 2016 =

* Fixed: gift cards generated twice when used within YITH Multi Vendor plugin

= Version 1.3.5 - Released: Apr 28, 2016 =

* Added: support to WooCommerce 2.6.0 for edit product page
* Fixed: out of date get_status() function call removed from the /templates/myaccount/my-giftcards.php file
* Fixed: conflict on emails sent by the YITH WooCommerce Points and Rewards plugin

= Version 1.3.4 - Released: Apr 26, 2016 =

* Fixed: the 'alt' and 'title' attribute of the gift cards template were not localizable
* Updated: yith-woocommerce-gift-cards.pot file

= Version 1.3.3 - Released: Apr 21, 2016 =

* Fixed: the custom image chosen while purchasing a gift card was not used in the email
* Fixed: resetting the custom image from the edit product page, the featured image was not used anymore

= Version 1.3.2 - Released: Apr 13, 2016 =

* Fixed: customize gift card button not shown if 'show template' is set to false

= Version 1.3.1 - Released: Apr 12, 2016 =

* Updated: pre-printed gift cards are not sent automatically when the code is filled
* Fixed: gallery items do not load properly
* Fixed: option for show the shop logo on the gift card template not visible on plugin settings

= Version 1.3.0 - Released: Apr 11, 2016 =

* Added: create a gallery of standard design from which the customer can choose the one that best fits the festivity or recurrence for which the gift card is being purchased
* Added: new feature for shops selling pre-printed physical gift cards, you can add the code manually instead of being auto generated
* Added: new option let you use the product featured image can be used as the gift card header image
* Added: from the product edit page you can set any image from the media gallery as the gift card header image
* Added: new option let you choose if shop logo should be shown on the gift card template
* Added: you can choose between two layouts for the gift card template
* Fixed: email header not visible when using the bulk action "Order actions" from the order page

= Version 1.2.12 - Released: Mar 22, 2016 =

* Fixed: gift cards table filter fails after "send now" button pressed
* Fixed: Aelia Currency Switcher add-on, wrong currency shown on emails
* Fixed: unwanted edit link shown on gift cards email
* Fixed: standard coupon not accepted when used together with a gift card

= Version 1.2.11 - Released: Mar 15, 2016 =

* Fixed: wrong gift card value shown on gift this product for variable product

= Version 1.2.10 - Released: Mar 14, 2016 =

* Updated: on back end gift cards table page, show the sum of order totals instead of subtotals
* Fixed: duplicated orders shown on back end gift cards table page

= Version 1.2.9 - Released: Mar 11, 2016 =

* Added: new gift card status: "Dismissed" is for gift card not valid and no more usable.
* Added: Syncronization between gift cards status and order status
* Fixed: wrong calculation on gift card when a manual amount is entered
* Updated: YITH Plugin FW

= Version 1.2.8 - Released: Mar 09, 2016 =

* Added: Rich snippets for the gift card product
* Added: automatic cart discount clicking from the email received
* Deleted: yith-status-options.php file no more used

= Version 1.2.7 - Released: Mar 07, 2016 =

* Fixed: ywgc-frontend.min.js not updated to the latest version
* Added: let the customer to change the recipient of gift card, crating a new gift card with update balance
* Added: in my-account page show the order where a gift card was used
* Updated: yith-woocommerce-gift-cards.pot in /languages folder

= Version 1.2.6 - Released: Mar 01, 2016 =

* Updated: all the gift cards used by a customer are now shown on my-account page
* Added: Aelia Currency Switcher compatibility let you use gift cards in multiple currency environment

= Version 1.2.5 - Released: Feb 26, 2016 =

* Added: gift cards can be set as disabled and no discount will be applied
* Added: template myaccount/my-giftcards.php for showing gift cards balance
* Added: show balance of used gift cards in my-account page
* Fixed: coupon code section shown twice on cart page based on the theme used
* Updated: removed filter yith_woocommerce_gift_cards_empty_price_html

= Version 1.2.4 - Released: Feb 11, 2016 =

* Fixed: in cart page the "Coupon" text was not localizable.
* Fixed: the class selector for datepicker conflict with other datepicker in the page
* Fixed: adding to cart of product with "sold individually" flag set fails
* Fixed: require_once of class.ywgc-product-gift-card.php lead sometimes to "the Class 'WC_Product' not found" fatal error
* Added: compatibility with the YITH WooCommerce Points and Rewards plugin

= Version 1.2.3 - Released: Jan 18, 2016 =

* Fixed: notification email on gift card code used not delivered to the customer

= Version 1.2.2 - Released: Jan 15, 2016 =

* Added: compatibility with YITH WooCommerce Dynamic Pricing

= Version 1.2.1 - Released: Jan 14, 2016 =

* Fixed: missing parameter 2 on emails

= Version 1.2.0 - Released: Jan 13, 2016 =

* Updated: gift card code is generated only one time, even if the order status changes to 'completed' several time
* Updated: plugin ready for WooCommerce 2.5
* Updated: removed action ywgc_gift_cards_email_footer for woocommerce_email_footer on email template
* Fixed: prevent gift card message containing HTML or scripts to be rendered
* Added: resend gift card email on the resend order emails dropdown on order page

= Version 1.1.6 - Released: Dec 28, 2015 =

* Added: digital gift cards content shown on gift cards table in admin dashboard
* Added: option to force gift card code sending when automatic sending fails

= Version 1.1.5 - Released: Dec 14, 2015 =

* Fixed: YITH Plugin Framework breaks updates on WordPress multisite

= Version 1.1.4 - Released: Dec 11, 2015 =

* Fixed: manual entered text not used in emails

= Version 1.1.3 - Released: Dec 08, 2015 =

* Fixed: YIT panel script not enqueued in admin

= Version 1.1.2 - Released: Dec 07, 2015 =

* Fixed: temporary gift card tax calculation
* Updated: temporary gift card is visible on dashboard so it can be set the title and the image

= Version 1.1.1 - Released: Nov 30 2015 =

* Fixed: Emogrifier warning caused by typo in CSS
* Fixed: problem that prevent the gift card email from being sent
* Fixed: ask for a valid date when postdated delivery is checked

= Version 1.1.0 - Released: Nov 26 2015 =

* Added: optionally redirect to cart after a gift cards is added to cart
* Fixed: postdated gift cards was sent on wrong date

= Version 1.0.9 - Released: Nov 25 2015 =

* Fixed: missing function on YIT Plugin Framework
* Updated: gift cards sender and recipient details added on emails

= Version 1.0.8 - Released: Nov 24 2015 =

* Fixed: wrong gift card values generated when in WooCommerce Tax Options, prices are set as entered without tax and displayd inclusiding taxes

= Version 1.0.7 - Released: Nov 20 2015 =

* Updated: gift card price support price including or excluding taxes

= Version 1.0.6 - Released: Nov 19 2015 =

* Updated: Gift Cards object cast to array for third party compatibility

= Version 1.0.5 - Released: Nov 17 2015 =

* Fixed: tax not deducted when gift card code was used

= Version 1.0.4 - Released: Nov 13 2015 =

* Fixed: multiple gift cards code not generated

= Version 1.0.3 - Released: Nov 12 2015 =

* Added: tax class on gift card product type
* Updated: changed action used for YITH Plugin FW loading
* Updated: gift card full amount(product price plus taxes) used for cart discount

= Version 1.0.2 - Released: Nov 06, 2015 =

* Fixed: coupon conflicts at checkout

= Version 1.0.1 - Released: Oct 29, 2015 =

* Update: YITH plugin framework

= Version 1.0.0 - Released: Oct 22, 2015 =

* Initial release
