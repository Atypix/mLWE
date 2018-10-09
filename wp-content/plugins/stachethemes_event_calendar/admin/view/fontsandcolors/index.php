<?php

namespace Stachethemes\Stec;




global $plugin_page;
$stachethemes_ec_main = stachethemes_ec_main::get_instance();



// GET
switch ( Admin_Helper::get("task") ) {
    case 'reset' :

        Admin_Helper::verify_nonce('', 'GET');

        Settings::reset_admin_settings($plugin_page . "_top");
        Settings::reset_admin_settings($plugin_page . "_agenda");
        Settings::reset_admin_settings($plugin_page . "_monthweek");
        Settings::reset_admin_settings($plugin_page . "_day");
        Settings::reset_admin_settings($plugin_page . "_grid");
        Settings::reset_admin_settings($plugin_page . "_custom");
        Settings::reset_admin_settings($plugin_page . "_preview");
        Settings::reset_admin_settings($plugin_page . "_event");
        Settings::reset_admin_settings($plugin_page . "_tooltip");
        Settings::reset_admin_settings($plugin_page . "_custom");

        Admin_Helper::set_message(__('Settings Reset', 'stec'));


        break;
}

// POST
switch ( Admin_Helper::post("task") ) {

    case 'save' :

        Admin_Helper::verify_nonce("?page={$plugin_page}");

        Settings::update_admin_settings($plugin_page . "_top");
        Settings::update_admin_settings($plugin_page . "_agenda");
        Settings::update_admin_settings($plugin_page . "_monthweek");
        Settings::update_admin_settings($plugin_page . "_day");
        Settings::update_admin_settings($plugin_page . "_grid");
        Settings::update_admin_settings($plugin_page . "_custom");
        Settings::update_admin_settings($plugin_page . "_preview");
        Settings::update_admin_settings($plugin_page . "_event");
        Settings::update_admin_settings($plugin_page . "_tooltip");
        Settings::update_admin_settings($plugin_page . "_custom");

        Admin_Helper::set_message(__('Settings updated', 'stec'));


        break;
}


// VIEW
switch ( Admin_Helper::get("view") ) :

    default:
        include __dir__ . '/settings.php';

endswitch;



