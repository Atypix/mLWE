<?php

namespace Stachethemes\Stec;
?>
<div class="stachethemes-admin-wrapper">

    <?php echo Admin_Helper::display_message(); ?>

    <h1>
        <?php
        _e('Cache Settings', 'stec');
        ?>
    </h1>

    <div class="stachethemes-admin-section">

        <h2><?php _e('Cache Settings', 'stec'); ?></h2>

        <?php
        Admin_Html::html_form_start("?page=$plugin_page", "POST");

        Admin_Html::build_settings_html($plugin_page);

        Admin_Html::html_button(__('Update Settings', 'stec'));
        Admin_Html::html_button(__('Delete Cache', 'stec'), wp_nonce_url("?page={$plugin_page}&task=delete_cache"), true, 'blue-button');

        Admin_Html::html_hidden('task', 'save');

        Admin_Html::html_form_end();
        ?>

    </div>

</div>

