<?php

namespace Stachethemes\Stec;




/**
 * Un-install hook
 */
register_uninstall_hook(STACHETHEMES_EC_FILE__, '\Stachethemes\Stec\stec_on_uninstall');



function stec_on_uninstall() {

    global $wpdb;

    if ( function_exists('is_multisite') && is_multisite() ) {

        $old_blog = $wpdb->blogid;

        // Get all blog ids
        $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

        foreach ( $blogids as $blog_id ) {
            switch_to_blog($blog_id);
            stec_on_uninstall_tables_and_settings();
        }

        switch_to_blog($old_blog);
        return;
    }

    stec_on_uninstall_tables_and_settings();
}



function stec_on_uninstall_tables_and_settings() {

    /**
     * @todo unify into single variable ?
     */
    delete_option("stec_activated");
    delete_option("stec_menu__license");
    delete_option("stec_menu__general");
    delete_option("stec_menu__general_google_captcha");
    delete_option("stec_menu__general_other");
    delete_option("stec_menu__fontsandcolors_top");
    delete_option("stec_menu__fontsandcolors_agenda");
    delete_option("stec_menu__fontsandcolors_monthweek");
    delete_option("stec_menu__fontsandcolors_day");
    delete_option("stec_menu__fontsandcolors_event");
    delete_option("stec_menu__fontsandcolors_preview");
    delete_option("stec_menu__fontsandcolors_tooltip");
    delete_option("stec_menu__fontsandcolors_custom");
    delete_option("stec_menu__cache");
}
