<?php

namespace Stachethemes\Stec\Updater;




require_once(__DIR__ . '/response.php');
require_once(__DIR__ . '/handler.php');

// Load scripts
add_action('admin_enqueue_scripts', function($hook) {

    if ( $hook != 'st-event-calendar_page_stec__updates' ) {
        return;
    }

    wp_enqueue_style('stec-updater', plugins_url('css/style.css', __FILE__));
    wp_enqueue_script('stec-updater-js', plugins_url('js/fn.js', __FILE__));

    wp_localize_script('stec-updater-js', 'stecUpdaterSettings', array(
            'nonce' => wp_create_nonce('stec-updater-nonce')
    ));
});

// Add updater menu
add_action('admin_menu', function() {
    add_submenu_page(
            'stec_menu__general', 'Updates', 'Updates', 'edit_posts', 'stec__updates', function() {
        require __DIR__ . '/display.php';
    });
});

add_action('admin_notices', function() {
    if ( isset($_SESSION['stec_updater_success_message']) ) {
        $class   = 'notice notice-success is-dismissible';
        $message = __('Stachethemes Event Calendar updated successfully!', 'stec');
        printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);

        $_SESSION['stec_updater_success_message'] = null;
    }
});
