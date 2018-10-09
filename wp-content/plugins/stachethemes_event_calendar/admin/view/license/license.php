<?php

namespace Stachethemes\Stec;
?>
<div class="stachethemes-admin-wrapper">

    <?php echo Admin_Helper::display_message(); ?>

    <h1><?php _e('Stachethemes Event Calendar - Product License', 'stec'); ?></h1>


    <div id="stec-activator" class="stachethemes-admin-section">

        <?php
        Admin_Html::html_form_start("?page=$plugin_page", "POST");

        $activated = false;

        if ( is_array($stachethemes_ec_main->lcns()) ) {
            $activated = true;
        }

        if ( $activated ) {
            ?><h2><?php _e('Deactivate License', 'stec'); ?></h2><?php
        } else {
            ?><h2><?php _e('Activate License', 'stec'); ?></h2><?php
        }

        Admin_Html::build_settings_html($plugin_page);
        ?>
        <div class="stec-activator-beforesend">
            <?php Admin_Html::html_info(__('Connecting to server...', 'stec')); ?>
        </div>
        <div class="stec-activator-success">
            <?php Admin_Html::html_info(__('Validating...', 'stec')); ?>
        </div>
        <?php
        if ( $activated ) {
            Admin_Html::html_button(__('Deactivate', 'stec'));
            Admin_Html::html_hidden('task', 'deactivate');
        } else {
            Admin_Html::html_button(__('Activate', 'stec'));
            Admin_Html::html_hidden('task', 'activate');
        }

        Admin_Html::html_hidden('action', 'stec_ajax_action');

        Admin_Html::html_form_end();
        ?>

    </div>
</div>
