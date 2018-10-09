<?php

namespace Stachethemes\Stec;




global $plugin_page;
$stachethemes_ec_main = stachethemes_ec_main::get_instance();


// GET
switch ( Admin_Helper::get("task") ) :

    case 'delete_cache' :

        Admin_Helper::verify_nonce('', 'GET');

        $result = Admin_Helper::delete_cache();

        if ( $result ) {
            Admin_Helper::set_message(__('Cache files deleted', 'stec'));
        } else {
            Admin_Helper::set_message(__('Unable to delete cache fiels!', 'stec'), 'error');
        }

        break;

endswitch;

// POST
switch ( Admin_Helper::post("task") ) :

    case 'save' :

        Admin_Helper::verify_nonce("?page={$plugin_page}");

        Settings::update_admin_settings($plugin_page);

        Admin_Helper::set_message(__('Settings updated', 'stec'));

        break;

endswitch;

// VIEW
switch ( Admin_Helper::get("view") ) :

    case "cache":
    default:

        include __dir__ . '/cache.php';

endswitch;
