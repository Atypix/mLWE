<?php

namespace Stachethemes\Stec;




global $plugin_page;



// GET
switch ( Admin_Helper::get("task") ) {
    case 'reset' :

        Admin_Helper::verify_nonce('', 'GET');

        Settings::reset_admin_settings($plugin_page);
        Settings::reset_admin_settings($plugin_page . '_google_captcha');
        Settings::reset_admin_settings($plugin_page . '_other');

        Admin_Helper::set_message(__('Settings Reset', 'stec'));


        break;
}


// POST
switch ( Admin_Helper::post("task") ) {

    case 'save' :

        Admin_Helper::verify_nonce("?page={$plugin_page}");

        Settings::update_admin_settings($plugin_page);
        Settings::update_admin_settings($plugin_page . '_google_captcha');
        Settings::update_admin_settings($plugin_page . '_other');

        Admin_Helper::set_message(__('Settings updated', 'stec'));

        break;
}

// VIEW
switch ( Admin_Helper::get("view") ) :

    default:
        include __dir__ . '/settings.php';

endswitch;



