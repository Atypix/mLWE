<?php

namespace Stachethemes\Stec;
?>
<div class="stachethemes-admin-wrapper">

    <?php echo Admin_Helper::display_message(); ?>

    <h1><?php _e('Stachethemes Event Calendar - Backup Settings', 'stec'); ?></h1>

    <div class="stachethemes-admin-section">

        <h2><?php _e('Import .STEC File Settings', 'stec'); ?></h2>

        <?php
        Admin_Html::html_form_start("?page=$plugin_page", "POST", true);

        Admin_Html::html_info(__('Import settings from file', 'stec'));
        Admin_Html::html_input('settings_filename', '', '', __('Settings FIle', 'stec'), true, "file", 'accept=".stec"');
        ?><div class="stachethemes-admin-separator"></div><?php
        Admin_Html::html_button(__('Import settings', 'stec'));
        Admin_Html::html_hidden('task', 'stec_import_settings');

        Admin_Html::html_form_end();
        ?>

    </div>

    <div class="stachethemes-admin-section">

        <h2><?php _e('Export to .STEC File', 'stec'); ?></h2>

        <?php
        Admin_Html::html_form_start("?page=$plugin_page", "POST", false);

        Admin_Html::html_info(__('Export settings to file', 'stec'));
        Admin_Html::html_button(__('Export settings', 'stec'));
        Admin_Html::html_hidden('task', 'stec_export_settings');

        Admin_Html::html_form_end();
        ?>

    </div>
</div>
