<?php

namespace Stachethemes\Stec;




/**
 * Activation Hook 
 */
register_activation_hook(STACHETHEMES_EC_FILE__, '\Stachethemes\Stec\stec_on_activate');



function stec_on_activate($networkwide) {

    global $wpdb;

    if ( function_exists('is_multisite') && is_multisite() ) {

        // check if it is a network activation - if so, run the activation function for each blog id

        if ( $networkwide ) {

            $old_blog = $wpdb->blogid;

            // Get all blog ids
            $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

            foreach ( $blogids as $blog_id ) {
                switch_to_blog($blog_id);
                stec_on_activate_tables_and_settings();
            }

            switch_to_blog($old_blog);
            return;
        }
    }

    stec_on_activate_tables_and_settings();
}



function stec_on_activate_tables_and_settings() {

    // register plugin settings
    require(dirname(__FILE__) . '/settings/register-settings.php');

    // register cron hooks
    wp_schedule_event(time(), 'hourly', 'stec_cronjobs_hourly');
    wp_schedule_event(time(), 'twicedaily', 'stec_cronjobs_twice_daily');
    wp_schedule_event(time(), 'daily', 'stec_cronjobs_daily');
    wp_schedule_event(time(), 'weekly', 'stec_cronjobs_weekly');
}
