<?php

namespace Stachethemes\Stec; ?>
<style>

    @-ms-viewport { 
        width: device-width;
        height: device-height;
    }  

    /* stec style generator */

    <?php
    // top
    Settings::get_style_from_setting('stec_menu__fontsandcolors_top', Settings::get_admin_setting_value('stec_menu__fontsandcolors_top', 'top_important'));

    // agenda
    Settings::get_style_from_setting('stec_menu__fontsandcolors_agenda', Settings::get_admin_setting_value('stec_menu__fontsandcolors_agenda', 'agenda_important'));

    // day
    Settings::get_style_from_setting('stec_menu__fontsandcolors_day', Settings::get_admin_setting_value('stec_menu__fontsandcolors_day', 'day_important'));

    // grid
    Settings::get_style_from_setting('stec_menu__fontsandcolors_grid', Settings::get_admin_setting_value('stec_menu__fontsandcolors_grid', 'grid_important'));

    $grid_has_border = Settings::get_admin_setting_value('stec_menu__fontsandcolors_grid', 'grid_border');

    if ( null === $grid_has_border ) {
        ?>
        body .stec-layout-grid .stec-layout-grid-event {
            border: none <?php
            if ( Settings::get_admin_setting_value('stec_menu__fontsandcolors_grid', 'grid_important') == 1 ) {
                echo '!important';
            }
            ?>;
        }
        <?php
    }

    // preview
    Settings::get_style_from_setting('stec_menu__fontsandcolors_preview', Settings::get_admin_setting_value('stec_menu__fontsandcolors_preview', 'preview_important'));

    // month and week
    Settings::get_style_from_setting('stec_menu__fontsandcolors_monthweek', Settings::get_admin_setting_value('stec_menu__fontsandcolors_monthweek', 'monthweek_important'));

    // inner event
    Settings::get_style_from_setting('stec_menu__fontsandcolors_event', Settings::get_admin_setting_value('stec_menu__fontsandcolors_event', 'event_important'));

    // tooltip css
    echo Settings::get_style_from_setting('stec_menu__fontsandcolors_tooltip', Settings::get_admin_setting_value('stec_menu__fontsandcolors_tooltip', 'tooltip_important'));

    // custom css
    echo Settings::get_admin_setting_value('stec_menu__fontsandcolors_custom', 'custom_css');
    ?>

</style>
