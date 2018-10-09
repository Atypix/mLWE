<?php

namespace Stachethemes\Stec\Migrate;



require_once(__DIR__ . '/class/helper.php');
require_once(__DIR__ . '/response.php');
require_once(__DIR__ . '/handler.php');

add_action("wp_ajax_stec_migrate_ajax_action", function() {
    require_once(__DIR__ . '/ajax_handler.php');
});

if ( !Helper::has_old_data() || (1 == get_option('stec-db-migrated', 0) && !isset($_GET['force_migrate'])) ) {
    return;
}


// Load scripts
add_action('admin_enqueue_scripts', function($hook) {

    if ( $hook != 'st-event-calendar_page_stec__migrate' ) {
        return;
    }

    wp_enqueue_style('stec-migrate', plugins_url('css/style.css', __FILE__));
    wp_enqueue_script('stec-migrate-js', plugins_url('js/fn.js', __FILE__));

    wp_localize_script('stec-migrate-js', 'stecMigrateSettings', array(
            'nonce' => wp_create_nonce('stec-migrate-nonce')
    ));
});

// Add updater menu
add_action('admin_menu', function() {
    add_submenu_page(
            'stec_menu__general', __('Migrate', 'stec'), __('Migrate', 'stec'), 'edit_posts', 'stec__migrate', function() {
        require __DIR__ . '/display.php';
    });
});

add_action('admin_notices', function() {
    if ( isset($_SESSION['stec_migrate_success_message']) ) {
        $class   = 'notice notice-success is-dismissible';
        $message = __('Stachethemes Event Calendar database migrated successfully!', 'stec');
        printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);

        $_SESSION['stec_migrate_success_message'] = null;
    }
});
