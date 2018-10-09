<?php

namespace Stachethemes\Stec;




if ( !$this instanceof stachethemes_ec_main ) {
    die('File is loaded incorrectly!');
}

$stachethemes_ec_main = $this;

add_action('stec_register_custom_post', function() use ($stachethemes_ec_main) {

    $stachethemes_ec_main->register_custom_post('stec_calendar', __('Calendar', 'stec'), __('Calendars', 'stec'), $stachethemes_ec_main->get_permalinks('calendar'), array('title'));
    $stachethemes_ec_main->register_custom_post('stec_event', __('Event', 'stec'), __('Events', 'stec'), $stachethemes_ec_main->get_permalinks('event'), array('title', 'editor'));
    $stachethemes_ec_main->register_custom_post('stec_cron', __('Cron', 'stec'), __('Crons', 'stec'), $stachethemes_ec_main->get_permalinks('cron'), array('title'));
});


do_action('stec_register_custom_post');
