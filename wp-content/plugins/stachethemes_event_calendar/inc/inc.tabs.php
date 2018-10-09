<?php

namespace Stachethemes\Stec;




if ( !$this instanceof stachethemes_ec_main ) {
    die('File is loaded incorrectly!');
}



function add_event_tab($slug, $title, $icon, $content = "", $file = false) {

    add_action('stachethemes_ec_add_event_tab', function() use($slug, $title, $icon) {
        echo "<li data-tab='stec-layout-event-inner-{$slug}'><i class='{$icon}'></i><p>{$title}</p></li>";
    });

    add_action('stachethemes_ec_add_event_tab_content', function() use($slug, $content, $file) {

        $stachethemes_ec_main = stachethemes_ec_main::get_instance();

        echo "<div class='stec-layout-event-inner-{$slug}'>";

        if ( $file !== false ) :
            include($stachethemes_ec_main->get_path('FRONT_VIEW') . $file);
        endif;

        if ( $content !== "" ) :
            echo $content;
        endif;

        echo "</div>";
    });
}

/**
 * Add event inner tabs
 * You can comment out tab to disable it or change tabs order
 */
$event_tabs = array(
        array("intro", __('Event Info', 'stec'), "fa fa-info", "", "tabs/intro.php"),
        array("location", __('Location', 'stec'), "fa fa-map-marker", "", "tabs/location.php"),
        array("schedule", __('Schedule', 'stec'), "fa fa-th-list", "", "tabs/schedule.php"),
        array("guests", __('Guests', 'stec'), "fa fa-star-o", "", "tabs/guests.php"),
        array("attendance", __('Attendance', 'stec'), "fa fa-user", "", "tabs/attendance.php")
);

// if woocommerce is running
if ( class_exists('WooCommerce') ) {
    $event_tabs[] = array("woocommerce", __('Shop', 'stec'), "fa fa-shopping-cart", "", "tabs/woocommerce.php");
    Stec_WooCommerce::add_filters();
}

$event_tabs[] = array("forecast", __('Forecast', 'stec'), "fa fa-sun-o", "", "tabs/forecast.php");
$event_tabs[] = array("comments", __('Comments', 'stec'), "fa fa-commenting-o", "", false);
$event_tabs   = apply_filters('stec_event_tabs', $event_tabs);

foreach ( $event_tabs as $event_tab ) {
    add_event_tab($event_tab[0], $event_tab[1], $event_tab[2], $event_tab[3], $event_tab[4]);
}

