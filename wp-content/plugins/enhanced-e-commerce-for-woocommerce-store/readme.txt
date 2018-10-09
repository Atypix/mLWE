===  Enhanced Ecommerce Google Analytics Plugin for WooCommerce ===
Contributors: Tatvic
Plugin Name: Enhanced Ecommerce for Woocommerce store
Plugin URI: http://wordpress.org/plugins/enhanced-e-commerce-for-woocommerce-store/
Tags: Google Analytics, Universal Analytics, Enhanced E-commerce, E-commerce, e-commerce, woo-commerce,Ecommerce,woocommerce, commerce, Wordpress Enhanced Ecommerce, Woocommerce Enhanced Ecommerce, Woocommerce Google Analytics, Google Analytics Plugin, Enhanced Ecommerce Plugin
Author URI: http://www.tatvic.com/
Author: Tatvic
Requires at least: 3.6
Tested up to: 4.9
Stable tag: 2.0.2
Version: 2.0.2
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Provides integration between Enhanced Ecommerce feature of Google Analytics and WooCommerce.

== Description ==
<a href="http://www.tatvic.com/enhanced-ecommerce-google-analytics-plugin-woocommerce/">Enhanced Ecommerce Google Analytics</a> is a Free Plugin for Woocommerce stores which allows you to use the newly launched feature of Google Analytics – Enhanced Ecommerce.You can track the user behavior across your e-commerce store starting from product views to thank you page. Enhanced Ecommerce is only supported by Universal Analytics.

= Features of Plugin =
1. Quick & Easy installation from the wordpress interface
2. Supports four New Reports in Enhanced Ecommerce
     * Shopping Behaviour Report
     * Checkout Behaviour Report
     * Product Performance Report
     * Sales Performance Report
3. Supports Guest checkout functionality
4. Supports Display Advertising Feature
5. Captures Product Impressions, Add to Cart & Product Clicks events on category page 
6. Captures Product Impressions, Add to Cart & Product Clicks events on product page
7. Captures Product Impressions, Add to Cart & Product Clicks events on featured Product Section on Homepage
8. Captures Product Impressions, Add to Cart & Product Clicks events on Recent Product Section on Homepage
9. Captures Product Impressions, Add to Cart & Product Clicks events on Related Product Section on Productpage 
10. Set your local currency
11. Google Analytics Opt Out
12. IP Anonymization


= Installation Instructions  =
* Enable Enhanced E-commerce for your profile/view. This is a profile / view level setting and can be accessed under Admin > View > E-commerce Settings

* Add meaningful labels for your checkout steps. We recommend you to label as, Step 1 : Checkout View; Step 2 : Billing Info; Step 3 : Proceed to payment

* Activate our plug-in from the Settings page. You can access the setting page from here WooCommerce -> Settings ->Integration ->Enhanced Ecommerce Google Analytics.

* Find “Add Enhanced Ecommerce Tracking Code” in the settings page and check the box to add the tracking code

* If you have a guest checkout on your WooCommerce store, then Check the box “Add Code to Track the Login Step of Guest Users”. If you have a guest login but you do not check the box, then it might cause an uneven funnel reporting in Google Analytics.

* All the product sections on homepage other than feature product will be fired as Recent Product and will be available in product list performance report.

* All the product sections on product page will be fired as Related Product and will be available in product list performance report.

= Need an Advanced Google Analytics Plugin? =
We have an Advanced Google Analytics Plugin for WooCommerce which includes tracking of 9 Reports of Enhanced Ecommerce, User ID Tracking, Product Refund, I.P. Anonymization, 15+ Custom Dimenensions & Metrics, Form Field Tracking, Content Grouping, Google Optimize & much more. <a href="https://codecanyon.net/item/actionable-google-analytics-for-woocommerce/9899552?ref=tatvic" target="_blank">Learn More</a>

== Note : ==
== Our plugin does not support the below features out of the box ==
* Highly Customized store
* Product types other than Simple Product
* Store with the Subscription product for Orders.
* Ecommerce Pages with Shortcodes
* Not fully compatible with the child/custom Theme

== Installation ==
1. Download the plugin file to your computer and unzip it
2. Using an FTP program, or your hosting control panel, upload the unzipped plugin folder to your WordPress installation’s wp-content/plugins/ directory
3. Activate the plugin from the Plugins menu within the WordPress admin
4. Enter your Universal Analytics ID for the plugin to enable the tracking code
5. Enable the required features
5. Accept the Privacy Policy of the Plugin

== Screenshots ==
1. Enable Enhanced E-commerce for your profile/view. This is a profile / view level setting and can be accessed under Admin > View > E-commerce Settings. Also, add meaningful labels for your checkout steps. We recommend you to label as, Step 1 : Checkout View; Step 2 : Login; Step 3 : Proceed to payment;
2. Next, you need to activate your plugin from the Settings page by clicking the checkbox – “Add Enhanced Ecommerce Tracking Code". You can access the same from: WooCommerce > Settings > Integration > Enhanced Ecommerce Google Analytics.
3. To Track Guest Users, Check the box – Add Code to Track the Login Steps of Guest Users. If you have a Guest Check out & if it’s Unchecked, then it might cause an uneven funnel reporting in Google Analytics.

== Frequently Asked Questions ==
= Where can I find the setting for this plugin? =

This plugin will add the settings to the Integration tab, to be found in the WooCommerce > Settings menu.

= Does this conflict with the WooCommerce? =

Starting the WooCommerce 2.1 release there are no conflicts. However for earlier the plugin might conflict with the default Google Analytics integration for WooCommerce.

= Do I Need to add any custom code for it? =

As our plugin automatically tracks all the Enhanced Ecommerce data ( including product name, price, etc dynamically) for your store, you don't need to add any custom/manual code to trackEcommerce events on your store from your end.

= Does this plugin match with GDPR compliance? =

Yes, our plugin is under GDPR complaince. For more information, read the <a href="https://www.tatvic.com/privacy-policy/?ref=plugin_policy&utm_source=plugin_backend&utm_medium=woocommerce_free_plugin&utm_campaign=GDPR_complaince_ecomm_plugins" target="_blank">privacy policy</a> of our plugin.

= Why are my PayPal transaction data not getting recorded in GA? =

If you are facing this issue, please check if you have configured auto return in PayPal settings.  Configuring auto return will resolve your issue. Here’s a PayPal <a href="https://www.paypal.com/in/cgi-bin/webscr?cmd=p/mer/express_return_summary-outside" target="_blank">documentation</a> & WooCommerce <a href="http://docs.woothemes.com/document/paypal-standard/#section-5" target="_blank">documentation</a> on understanding & setting up Auto Return.

In case you have already configured auto return for your store, we request you to create a new support thread <a href="https://wordpress.org/support/plugin/enhanced-e-commerce-for-woocommerce-store" target="_blank">here</a> & reach out to us.

= I’ve install the plugin but I do not see any data in my GA =

Following are one or more reasons:

* Please make sure that you have Enabled Enhanced Ecommerce setting in your GA Account. Check out the Step 1 of this <a href="http://www.tatvic.com/blog/enhanced-ecommerce/#steps" target="_blank">blogpost</a>.

* If you have just installed our plugin, then please wait for at-least 24 hours before you 	start seeing any data in your GA. If you still face this issue after 24 hours, please reach out to us via <a href="https://wordpress.org/support/plugin/enhanced-e-commerce-for-woocommerce-store" target="_blank">support thread</a>.

= Since I have Implemented GA Script (UA tag) Via GTM, I didn't enable Add Global site Tracking Code option, but seems that it is not working. =

When you have the UA script/tag implemented via your GTM, it may happen sometimes that the script might take/make any delay in loading on your store, due to which our plugin may not work well on your store.

Reason :

* Our Plugin's script works/fetches the data based on the GTAG's default tracker ('gtag' in the case of Universal Analytics script used in our plugin). While you implement the UA tracking script from your GTM, the script in your store may not be able to initialize the tracker, which in turn will hinder the plugin from populating insights in your Analytics account.

= Does your plugin supports new Global Site Tag (gtag.js)? =

Yes our plugin supports new Global Site Tag (gtag.js).

= Since I have Implemented Old GA Script (UA Script) Manually in my store, I didn't enable Add Global site Tracking Code option gtag.js, but seems that it is not working. =

When you have the Old UA script implemented Manually in your store, it is not working with our plugin.

Reason :

* Our Plugin's script works/fetches the data based on the GTAG's default tracker ('gtag' in the case of Universal Analytics script used in our plugin). While you implement the Old UA tracking script manually, the script in your store may not be able to initialize the tracker, which in turn will hinder the plugin from populating insights in your Analytics account.

= Where I can see my all Enhanced Ecommerce Reports (Eg. Sales Report,Product Performance Report)? =

You can Find all The Enhanced Ecommerce Reports in your Analytics Account under Conversions --> Ecommerce.

= Products with variant not getting recorded in GA =

Currently our plugin does not support products with variant & hence you may not see their transaction data in GA. This feature is only available with the <a href="https://codecanyon.net/item/actionable-google-analytics-for-woocommerce/9899552?ref=tatvic" target="_blank">premium version</a> of our plugin.

= I have noticed that some transactions are missing in my GA account, compared to my Woocmmerce backend (Orders) =

Possible reasons for not getting the accurate Transactions (in sales performance report) are as below :

* If a user completes the transaction via a 3rd party payment gateway and is not redirected back to your store’s thank you page.

* If any javascript error is detected on the "thank you" page of your store which may restrict plugin's code to get executed further.

* Some browsers and common ad blocking programs block certain JavaScripts
(including GA's script), which means Google Analytics is unable to record transactions.

* The user has left the page before the transaction has had a chance to send to Google Analytics.

Additionally, GA is a trend analysis tool, and as such cannot be expected to be 100% accurate. However, if the variance is greater than 12%, we request you to contact us!

= My Ecommerce transaction data are not getting recorded in GA =

Please check if you have auto return configured in your payment gateway settings. If a user completes the transaction via a 3rd party payment gateway and is not redirected back to your store’s thank you page, our plugin will not be able to send the transaction data.

Hence, this may result into missing transaction data in your GA. You can resolve this issue by configuring auto return in your payment gateway settings.

= Does this plugin help me create/configure goals/funnels in my GA account? =

Configuring goals are out of the scope of our plugin. Our plugin is designed to track checkout funnels only.

= Does your Plugin support Product Refund? =

Our existing plugin does not track product refund data, however you can buy our <a href="https://codecanyon.net/item/actionable-google-analytics-for-woocommerce/9899552?ref=tatvic" target="_blank">premium plugin</a> to get access to product Refund data 

= Does your plugin supports Multilingual Wordpress site? =

Our plugin does not support Multilingual Wordpress site.

= Does your plugin supports Child/Custom Theme? =

The free version of our plugin is not fully compatible with the child/custom theme. Request you to go through the <a href="https://codecanyon.net/item/actionable-google-analytics-for-woocommerce/9899552?ref=tatvic" target="_blank">premium version</a> of our plugin which is fully compatible with the child/custom theme. We are not providing any kind of support for Child/Custom Theme in the Free version of our plugin. For more information kindly contact us at analytics2(at)tatvic(dot)com.

= Have you Provided Full support for the free version plugin? =

We have a limited support policy for the free version of our plugin. Kindly go through the <a href="https://codecanyon.net/item/actionable-google-analytics-for-woocommerce/9899552?ref=tatvic" target="_blank">premium version</a> of our plugin to get full support for the product or you can also contact us at analytics2(at)tatvic(dot)com.

= How to verify if you have implemented the Plugin well? =

To verify if you have implemented the plugin well, just log in to your Google Analytics account & check if the data is coming well in your Enhanced Ecommerce Reports.

= How much time will it take to see the data in Google Analytics? =

It generally depends upon the traffic of the store. But in general it may take max up to 24 hours & min 4 hours to see the data in Google Analytics.

= Why the plugin does not sent data when I am logged in as Admin? =

To avoid sending your own transaction data or sessions data in Google Analytics, our plugin doesn't sent the data to GA when you are logged in. Having said, if you are logged in as Shop Manager, plugin will send the data to GA.

== Changelog ==

= 1.0 - 25/06/2014 =
 * Initial release

= 1.0.6.1 - 15/08/2014 =
 * Added new feature - Product impressions and Product click on category page view , including the    default pagination
 * Fixed-Allow Special Characters in javascript

= 1.0.7 - 28/08/2014 =
 * Added new feature - Display Advertising Feature
 * Fixed-Allow back quotes and single quotes in product name, category name etc.

= 1.0.8 - 09/09/2014 =
 * Fixed- Minor bugs 

= 1.0.9.1 - 09/11/2014 =
 * Fixed- Minor bug on order page

= 1.0.10 - 26/09/2014 =
 * Allows user to set local currency
 * Captures Impressions, Product Clicks and Add to Cart on Featured Product section and Recent Product section on Homepage
 * Captures Impressions, Product Clicks and Add to Cart on Related Product section on Product Page

= 1.0.11 - 28/10/2014 =
 * Fixed - Minor bugs

= 1.0.12 - 19/11/2014 =
 * Fixed - Settings not getting saved on few stores
 * Fixed - Broken layout issue

Important Note: When you update the plugin, please save your settings again.

= 1.0.13 - 19/12/2014 =
 * Currency as field removed from the plugin. Reason: we now automatically passed the currency which you may have set in WooCommerce store
 * Fixed - Minor bugs

 = 1.0.14 - 10/02/2015 =
 * Fixed - session_start() function warning 
 * Fixed - Notice: Undefined variable: coupons_list
 * Fixed - woocommerce deprecated function warning

 =  1.0.15 - 28/03/2015 =
 * Email Field made optional

 = 1.0.16 - 01/04/2015 =
 * Minor Bug Fixes as per Wordpress Guidlines

= 1.0.17 - 19/02/2016 =
 * Fixed - Notice: Undefined index: tab
 * Minor Bug Fixes as per Wordpress Guidlines

 = 1.0.18 - 11/04/2016 =
 * Fixed - Compatibility with Google Tag Manager for Wordpress by DuracellTomi

 = 1.0.19 - 21/12/2016 =
 * Fixed - Compatibility with Wordpress 4.7 & Woocommerce 2.6.x
 * Minor Bug Fixes.

 = 1.0.20 - 14/04/2017 =
 * Fixed - Compatibility with Woocommerce 3.x
 * Minor Bug Fixes.

 = 1.0.21 - 12/05/2017 =
 * Minor Bug Fixes.

 = 1.0.21.1 - 13/07/2017 =
 * Minor Changes.

 = 1.1.0 - 08/08/2017 =
 * Minor Bug Fixes & Optimization.

 = 1.1.1 - 04/09/2017 =
 * Minor Optimization on Order-Received Page.

 = 1.1.2 - 04/09/2017 =
 * Quick Fix of Minor Bugs.

 = 1.2.0 - 17/10/2017 =
 * Compatibility with Woocommerce 3.2
 * Minor Bug Fixes & Optimization.

 = 1.2.0.1 - 06/11/2017 =
 * Quick Bug Fixes.

 = 1.2.1 - 12/12/2017 =
 * Improvisation for Product Page
 * Minor Bug Fixes

 = 1.2.1.1 - 20/12/2017 =
 * Quick Fixes for Product Page & ATC
 * Minor Bug Fixes

 = 1.2.1.2 - 26/12/2017 =
 * Minor Fixes

 = 1.2.2 - 31/01/2018 =
 * Compatibility with Woocommerce 3.3
 * Minor Bug Fixes & Optimization.

 = 2.0.0 - 07/03/2018 =
 * New Implementation with Global Site Tag (gtag.js)
 * gtag.js supported
 * Minor Bug Fixes & Optimization.

  = 2.0.1 - 24/04/2018 =
 * Minor Bug Fixes & Optimization.

 = 2.0.2 - 23/05/2018 =
 * IP Anonymization Feature
 * Google Analytics Opt Out
 * Update the Privacy Policy under GDPR Compliance