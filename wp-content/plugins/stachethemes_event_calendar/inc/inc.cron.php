<?php

namespace Stachethemes\Stec;




// Add weekly interval to wp cron schedule
function stec_cron_weekly_filter($schedules) {

    // add a 'weekly' schedule to the existing set
    $schedules['weekly'] = array(
            'interval' => 604800,
            'display'  => __('Once Weekly', 'stec')
    );
    return $schedules;
}

add_filter('cron_schedules', 'Stachethemes\Stec\stec_cron_weekly_filter');










/**
 * Reminder Cronjob
 */
add_action('stec_cronjobs_hourly', 'Stachethemes\Stec\stec_cronjobs_reminder');



function stec_cronjobs_reminder() {

    if ( Settings::get_admin_setting_value('stec_menu__general', 'reminder') == '1' ) {

        Cron::do_remind_job();
    }
}

/**
 * Importer Cronjob
 */
// hourly import
add_action('stec_cronjobs_hourly', 'Stachethemes\Stec\stec_cronjobs_import_hourly');



function stec_cronjobs_import_hourly() {

    Cron::do_import_job(0);
}

// twice daily import
add_action('stec_cronjobs_twice_daily', 'Stachethemes\Stec\stec_cronjobs_import_twice_daily');



function stec_cronjobs_import_twice_daily() {

    Cron::do_import_job(1);
}

// once daily import
add_action('stec_cronjobs_daily', 'Stachethemes\Stec\stec_cronjobs_import_daily');



function stec_cronjobs_import_daily() {

    Cron::do_import_job(2);
}

// weekly import && lcns check
add_action('stec_cronjobs_weekly', 'Stachethemes\Stec\stec_cronjobs_import_weekly');



function stec_cronjobs_import_weekly() {

    Cron::do_import_job(3);
}
