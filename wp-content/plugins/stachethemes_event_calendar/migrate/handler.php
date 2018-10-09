<?php

namespace Stachethemes\Stec\Migrate;




use Stachethemes\Stec\Admin_Helper;
use Exception;

// $_GET
switch ( Admin_Helper::get("task") ) {
    
}

// $_POST
switch ( Admin_Helper::post("task") ) {


    case 'stec_migrate' :

        Admin_Helper::verify_nonce("?page=stec__migrate");

        try {

            $result = Helper::migrate();

            if ( $result ) {

                update_option('stec-db-migrated', 1);

                $message = __('Database migrated successfully!', 'stec');
                Admin_Helper::set_message($message);

                wp_redirect(admin_url('/admin.php?page=stec_menu__calendars'));
                exit;
            } else {
                add_action('admin_notices', function() {
                    $class   = 'notice notice-error is-dismissible';
                    $message = __('Error migrating database!', 'stec');
                    printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);
                });
            }
        } catch ( Exception $ex ) {

            add_action('admin_notices', function() use ($ex) {
                $class   = 'notice notice-error is-dismissible';
                $message = $ex->getMessage();
                printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);
            });
        }

        break;

    case 'stec_forget_migrate' :

        Admin_Helper::verify_nonce("?page=stec__migrate");
        update_option('stec-db-migrated', 1);
        $message = __('Old database has been forgotten!', 'stec');
        Admin_Helper::set_message($message);

        wp_redirect(admin_url('/admin.php?page=stec_menu__calendars'));
        exit;

        break;
}