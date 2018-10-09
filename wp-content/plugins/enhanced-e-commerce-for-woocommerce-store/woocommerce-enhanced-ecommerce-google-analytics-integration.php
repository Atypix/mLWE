<?php

/*  Copyright 2017 Tatvic

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
/*
  Plugin Name: Enhanced E-commerce for Woocommerce store
  Plugin URI: http://www.tatvic.com/enhanced-ecommerce-google-analytics-plugin-woocommerce/
  Description: Allows Enhanced E-commerce Google Analytics tracking code to be inserted into WooCommerce store pages.
  Author: Tatvic
  Author URI: http://www.tatvic.com
  Version: 2.0.2
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Add the integration to WooCommerce
function wc_enhanced_ecommerce_google_analytics_add_integration($integrations) {
    global $woocommerce;

    if (is_object($woocommerce)) {
        include_once( 'includes/class-wc-enhanced-ecommerce-google-analytics-integration.php' );
        $integrations[] = 'WC_Enhanced_Ecommerce_Google_Analytics';
    }
    return $integrations;
}

add_filter('woocommerce_integrations', 'wc_enhanced_ecommerce_google_analytics_add_integration', 10);

//plugin action links on plugin page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'tvc_ee_plugin_action_links');

function tvc_ee_plugin_action_links($links) {
    global $woocommerce;
    if (version_compare($woocommerce->version, "2.1", ">=")) {
        $setting_url = 'admin.php?page=wc-settings&tab=integration';
    } else {
        $setting_url = 'admin.php?page=woocommerce_settings&tab=integration';
    }
    $links[] = '<a href="' . get_admin_url(null, $setting_url) . '">Settings</a>';
    $links[] = '<a href="https://wordpress.org/plugins/enhanced-e-commerce-for-woocommerce-store/faq/" target="_blank">FAQ</a>';
    $links[] = '<a href="http://plugins.tatvic.com/downloads/EE-Woocommerce-Plugin-Documentation.pdf" target="_blank">Documentation</a>';
    $links[] = '<b><a href="https://codecanyon.net/item/actionable-google-analytics-for-woocommerce/9899552?ref=tatvic" target="_blank">Upgrade to Premium</a></b>';
    return $links;
}
?>