<?php

namespace Stachethemes\Stec;




add_shortcode('stachethemes_ec_export', function($atts = array()) {

    ob_start();

    $stachethemes_ec_main = stachethemes_ec_main::get_instance();
    $id                   = uniqid('stec-export-button-');
    $calendar_id          = isset($atts['cal']) ? $atts['cal'] : null;
    $text                 = isset($atts['text']) ? $atts['text'] : __('Export calendar', 'stec');

    $calendar = new Calendar_Post($calendar_id);

    if ( !$calendar->get_id() ) {
        return '';
    }

    if ( false === Calendars::can_export($calendar) ) {
        return '';
    }

    $export_url = site_url('?task=stec_public_export_to_ics&calendar_id=' . $calendar_id);

    if ( $stachethemes_ec_main->lcns() !== false ) {
        include ($stachethemes_ec_main->get_path('FRONT_VIEW') . 'buttons/export.php');
    } else {
        echo '<p>Stachethemes Event Calendar is not activated. To activate it go to <a href="' . get_admin_url(0, 'admin.php?page=stec_menu__license') . '">Dashboard -> Stachethemes Event Calendar -> Product License</a></p>';
    }

    return ob_get_clean();
});
