<?php

namespace Stachethemes\Stec;
?>
<div class="stachethemes-admin-wrapper">

    <?php
    $fontsizes = array();
    for ( $i = 12; $i <= 100; $i++ ) {
        $fontsizes["{$i}px"] = "{$i}px";
    }

    $fontweights = array();
    for ( $i = 1; $i <= 9; $i++ ) {
        $fontweights[$i * 100] = $i * 100;
    }
    ?>

    <?php echo Admin_Helper::display_message(); ?>

    <h1><?php _e('Fonts and Colors', 'stec'); ?></h1>

    <ul class="stachethemes-admin-tabs-list">
        <li data-tab="top"><?php _e('Top Navigation', 'stec'); ?></li>
        <li data-tab="agenda"><?php _e('Agenda Layout', 'stec'); ?></li>
        <li data-tab="monthweek"><?php _e('Month & Week Layout', 'stec'); ?></li>
        <li data-tab="day"><?php _e('Day Layout', 'stec'); ?></li>
        <li data-tab="grid"><?php _e('Grid Layout', 'stec'); ?></li>
        <li data-tab="preview"><?php _e('Event Preview', 'stec'); ?></li>
        <li data-tab="event"><?php _e('Event Content', 'stec'); ?></li>
        <li data-tab="tooltip"><?php _e('Tooltip', 'stec'); ?></li>
        <li data-tab="custom"><?php _e('Custom Style', 'stec'); ?></li>
    </ul>

    <div class="stachethemes-admin-section">

        <?php
        Admin_Html::html_form_start("?page=$plugin_page", "POST");

        // STEP
        ?>

        <div class="stachethemes-admin-section-tab" data-tab="top">

            <?php Admin_Html::build_settings_html($plugin_page . "_top"); ?>

        </div>

        <div class="stachethemes-admin-section-tab" data-tab="agenda">

            <?php Admin_Html::build_settings_html($plugin_page . "_agenda"); ?>

        </div>

        <div class="stachethemes-admin-section-tab" data-tab="monthweek">

            <?php Admin_Html::build_settings_html($plugin_page . "_monthweek"); ?>

        </div>

        <div class="stachethemes-admin-section-tab" data-tab="day">

            <?php Admin_Html::build_settings_html($plugin_page . "_day"); ?>

        </div>

        <div class="stachethemes-admin-section-tab" data-tab="grid">

            <?php Admin_Html::build_settings_html($plugin_page . "_grid"); ?>

        </div>

        <div class="stachethemes-admin-section-tab" data-tab="preview">

            <?php Admin_Html::build_settings_html($plugin_page . "_preview"); ?>

        </div>

        <div class="stachethemes-admin-section-tab" data-tab="event">
            <?php Admin_Html::build_settings_html($plugin_page . "_event"); ?>
        </div>

        <div class="stachethemes-admin-section-tab" data-tab="tooltip">

            <?php Admin_Html::build_settings_html($plugin_page . "_tooltip"); ?>

        </div>

        <div class="stachethemes-admin-section-tab" data-tab="custom">

            <?php Admin_Html::build_settings_html($plugin_page . "_custom"); ?>

        </div>

        <div class="stachethemes-admin-section-tab" data-tab="notification">

            <?php Admin_Html::build_settings_html($plugin_page . "_notification"); ?>

        </div>


        <?php
        Admin_Html::build_settings_html($plugin_page);
        ?>

    </div>

    <div class="stachethemes-admin-separator"></div>
    <?php
    Admin_Html::html_button(__('Save Settings', 'stec'));
    Admin_Html::html_hidden('task', 'save');
    Admin_Html::html_button(__('Reset all', 'stec'), wp_nonce_url("?page={$plugin_page}&task=reset"), true);

    Admin_Html::html_form_end();
    ?>

</div>