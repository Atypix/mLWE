<?php

namespace Stachethemes\Stec;
?>
<div class="stachethemes-admin-wrapper">

    <?php echo Admin_Helper::display_message(); ?>

    <h1><?php _e('Stachethemes Event Calendar - General Settings', 'stec'); ?></h1>

    <ul class="stachethemes-admin-tabs-list">
        <li data-tab="general"><?php _e('General', 'stec'); ?></li>
        <li data-tab="google_captcha"><?php _e('Captcha', 'stec'); ?></li>
        <li data-tab="other"><?php _e('Misc', 'stec'); ?></li>
    </ul>

    <div class="stachethemes-admin-section">

        <?php
        Admin_Html::html_form_start("?page=$plugin_page", "POST");
        ?>

        <div class="stachethemes-admin-section-tab" data-tab="general">
            <h2><?php _e('General Settings', 'stec'); ?></h2>
            <?php Admin_Html::build_settings_html($plugin_page); ?>
        </div>


        <div class="stachethemes-admin-section-tab" data-tab="email_reminder">
            <h2><?php _e('Reminder E-Mail', 'stec'); ?></h2>
            <?php Admin_Html::build_settings_html($plugin_page . '_email_reminder'); ?>
        </div>

        <div class="stachethemes-admin-section-tab" data-tab="google_captcha">
            <h2><?php _e('Captcha Settings', 'stec'); ?></h2>
            <?php Admin_Html::html_info(__('NOTE: To manage your API keys go to', 'stec') . ' <a target="_BLANK" href="https://www.google.com/recaptcha/admin#list">Google reCAPTCHA</a>'); ?>
            <?php Admin_Html::build_settings_html($plugin_page . '_google_captcha'); ?>
        </div>

        <div class="stachethemes-admin-section-tab" data-tab="other">
            <h2><?php _e('Miscellaneous Settings', 'stec'); ?></h2>
            <?php Admin_Html::build_settings_html($plugin_page . '_other'); ?>
        </div>

        <?php
        // save
        Admin_Html::html_button(__('Save settings', 'stec'));
        Admin_Html::html_hidden('task', 'save');

        Admin_Html::html_button(__('Reset all', 'stec'), wp_nonce_url("?page={$plugin_page}&task=reset"), true);

        Admin_Html::html_form_end();
        ?>

    </div>
</div>
