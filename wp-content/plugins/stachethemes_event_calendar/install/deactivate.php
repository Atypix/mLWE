<?php

namespace Stachethemes\Stec;




/**
 * Deactivation hook
 */
register_deactivation_hook(STACHETHEMES_EC_FILE__, '\Stachethemes\Stec\on_deactivate');



function on_deactivate() {
    
    // deregister cron hooks
    wp_clear_scheduled_hook('stec_cronjobs_hourly');
    wp_clear_scheduled_hook('stec_cronjobs_twice_daily');
    wp_clear_scheduled_hook('stec_cronjobs_daily');
    wp_clear_scheduled_hook('stec_cronjobs_weekly');
}
