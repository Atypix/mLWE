<?php

namespace Stachethemes\Stec;
?>

<div class="stachethemes-admin-wrapper">

    <?php if ( $calendar ) : ?>

        <h1>
            <?php
            _e('Events Management', 'stec');

            if ( $event ) {
                echo " / " . $calendar->get_title() . " / " . $event->get_title() . " - " . __('Edit Event', 'stec');
            } else {
                echo " / " . $calendar->get_title() . " - " . __('Add Event', 'stec');
            }
            ?>
        </h1>

    <?php endif; ?>

    <ul class="stachethemes-admin-tabs-list">

        <?php if ( $event && $event->get_approved() == "0" ) : ?>
            <li data-tab="author"><?php _e('Author Info', 'stec'); ?></li>
        <?php endif; ?>

        <li data-tab="general"><?php _e('General', 'stec'); ?></li>
        <li data-tab="introduction"><?php _e('Introduction', 'stec'); ?></li>
        <li data-tab="location"><?php _e('Location', 'stec'); ?></li>
        <li data-tab="schedule"><?php _e('Schedule', 'stec'); ?></li>
        <li data-tab="guests"><?php _e('Guests', 'stec'); ?></li>
        <li data-tab="attendance"><?php _e('Attendance', 'stec'); ?></li>

        <?php if ( class_exists('WooCommerce') ) : ?>
            <li data-tab="woocommerce"><?php _e('WooCommerce', 'stec'); ?></li>
        <?php endif; ?>

        <li data-tab="attachments"><?php _e('Attachments', 'stec'); ?></li>
    </ul>

    <div class="stachethemes-admin-section">

        <?php
        Admin_Html::html_form_start("?page=$plugin_page&calendar_id={$calendar_id}", "POST");

        if ( $event && $event->get_approved() == "0" ) {
            include('tabs/author.php');
        }

        include('tabs/general.php');
        include('tabs/location.php');
        include('tabs/introduction.php');
        include('tabs/schedule.php');
        include('tabs/guests.php');
        include('tabs/attendance.php');

        if ( class_exists('WooCommerce') ) :
            include('tabs/woocommerce.php');
        endif;

        include('tabs/attachments.php');
        ?>

    </div>

    <div class="stachethemes-admin-separator"></div>
    <?php
    Admin_Html::html_hidden('calendar_id', $calendar_id);
    Admin_Html::html_hidden('approved', '1');

    if ( $event_id !== false ) {
        Admin_Html::html_hidden('event_id', $event_id);
        Admin_Html::html_hidden('task', 'update');

        if ( $event->get_approved() == '0' ) {
            Admin_Html::html_button(__('Approve Event', 'stec'));
        } else {
            Admin_Html::html_button(__('Update Event', 'stec'));
        }
    } else {
        Admin_Html::html_button(__('Add Event', 'stec'));
        Admin_Html::html_hidden('task', 'create');
    }

    Admin_Html::html_button(__('Back', 'stec'), "?page={$plugin_page}&calendar_id={$calendar_id}", true);

    Admin_Html::html_form_end();
    ?>

</div>