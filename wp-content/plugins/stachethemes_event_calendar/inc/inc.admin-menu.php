<?php

namespace Stachethemes\Stec;




if ( !$this instanceof stachethemes_ec_main ) {
    die('File is loaded incorrectly!');
}

$stachethemes_ec_main = $this;

add_action('stec_add_admin_menu', function() use ($stachethemes_ec_main) {

    // Admin Menu

    $has_calendars = count(Calendars::get_admin_calendars()) > 0 ? true : false;
    $stachethemes_ec_main->add_menu("St. Event Calendar", "stec_menu__general", "dashicons-calendar-alt", 26);
    $stachethemes_ec_main->add_submenu(__('General', 'stec'), "stec_menu__general");
    $stachethemes_ec_main->add_submenu(__('Fonts & Colors', 'stec'), "stec_menu__fontsandcolors");
    $stachethemes_ec_main->add_submenu(__('Calendars', 'stec'), "stec_menu__calendars");
    if ( $has_calendars ) {
        $stachethemes_ec_main->add_submenu(__('Events', 'stec'), "stec_menu__events");
        $stachethemes_ec_main->add_submenu(__('Import Events', 'stec'), "stec_menu__import");
        $stachethemes_ec_main->add_submenu(__('Export Events', 'stec'), "stec_menu__export");
    }
    $stachethemes_ec_main->add_submenu(__('Cache', 'stec'), "stec_menu__cache");
    $stachethemes_ec_main->add_submenu(__('Backup Settings', 'stec'), "stec_menu__iesettings");
    $stachethemes_ec_main->add_submenu(__('Activator', 'stec'), "stec_menu__license");
});

add_action('stec_add_admin_menu_assets', function() use ($stachethemes_ec_main) {

    $stachethemes_ec_main->add_menu_font("stec-fa", "font-awesome-4.5.0/css/font-awesome.css");
    $stachethemes_ec_main->add_menu_css("stec-ui", "//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css");
    $stachethemes_ec_main->add_menu_css("stec-fonts", "//fonts.googleapis.com/css?family=Roboto:300,400,700");
    $stachethemes_ec_main->add_menu_css("stec-css", "style.css");
    $stachethemes_ec_main->add_menu_css("stec-colorpicker", "colorpicker/css/colorpicker.css");
    $stachethemes_ec_main->add_menu_js("stec-colorpicker-js", "libs/colorpicker/colorpicker.js", 'jquery');
    $stachethemes_ec_main->add_menu_js("stec-rrule-js", "libs/rrule.js");
    $stachethemes_ec_main->add_menu_js("stec-nlp-js", "libs/nlp.js", "stec-rrule-js");
    $stachethemes_ec_main->add_menu_js("stec-js", "admin.js", 'jquery-ui-datepicker,jquery-ui-sortable');
    $stachethemes_ec_main->add_menu_js("stec-updater-js", "updater.js", 'jquery');
    // less compiler for admin section
//    $stachethemes_ec_main->add_menu_js("stec-less-js", "less.js");
});

do_action('stec_add_admin_menu');
do_action('stec_add_admin_menu_assets');

$this->register_menu();
