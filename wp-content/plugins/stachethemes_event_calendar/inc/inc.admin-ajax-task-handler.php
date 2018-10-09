<?php

namespace Stachethemes\Stec;




if ( !$this instanceof stachethemes_ec_main ) {
    die('File is loaded incorrectly!');
}

header('Content-Type: application/json');

$task = filter_input(INPUT_POST, "task", FILTER_SANITIZE_STRING);

switch ( $task ) :

    case 'throwerror_license' :

        Admin_Helper::verify_nonce('', 'AJAX');

        Admin_Helper::set_message(__('License activator encountered an error. Make sure your purchase key is not activated somewhere else.', 'stec'), 'error');

        echo json_encode(array(
                'error' => 0
        ));

        break;

    case 'activate_license' :

        Admin_Helper::verify_nonce('', 'AJAX');

        $purchase_code = Admin_Helper::post('purchase_code', false, FILTER_SANITIZE_STRING);
        $server_name   = Admin_Helper::post('server_name', false, FILTER_DEFAULT);

        if ( !$purchase_code || !$server_name ) {

            echo json_encode(array(
                    'error' => 1
            ));

            Admin_Helper::set_message(__('Error activating license', 'stec'), 'error');

            break;
        }

        update_option('stec_activated', array(
                'purchase_code' => $purchase_code,
                'server_name'   => $server_name
        ));

        Settings::update_admin_settings('stec_menu__license');

        Admin_Helper::set_message(__('License Activated', 'stec'));

        echo json_encode(array(
                'error' => 0
        ));

        break;

    case 'deactivate_license' :

        Admin_Helper::verify_nonce('', 'AJAX');


        $purchase_code = Admin_Helper::post('purchase_code', false, FILTER_SANITIZE_STRING);
        $server_name   = Admin_Helper::post('server_name', false, FILTER_DEFAULT);

        if ( !$purchase_code || !$server_name ) {

            echo json_encode(array(
                    'error' => 1
            ));

            Admin_Helper::set_message(__('Error deactivating license', 'stec'), 'error');

            break;
        }

        update_option('stec_activated', false);

        Settings::reset_admin_settings('stec_menu__license');

        Admin_Helper::set_message(__('License Deactivated', 'stec'));

        echo json_encode(array(
                'error' => 0
        ));

        break;

endswitch;

exit;
