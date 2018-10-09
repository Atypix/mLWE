<?php

namespace Stachethemes\Stec;
?>

<div class="stachethemes-admin-wrapper">

    <h1><?php _e('Calendars Management / Edit', 'stec'); ?></h1>

    <?php if ( isset($calendar) && $calendar instanceof Calendar_Post ) : ?>

        <div class="stachethemes-admin-section">

            <h2><?php _e('Edit calendar', 'stec'); ?></h2>

            <?php
            Admin_Html::html_form_start("?page=$plugin_page", "POST");

            Admin_Html::html_info(__('Desired name of the calendar:', 'stec'));
            Admin_Html::html_input('calendar_name', $calendar->get_title(), '', __('Calendar Name', 'stec'), true);

            Admin_Html::html_info(__('Desired color. This will be the default color of the events. Can be changed later.', 'stec'));
            Admin_Html::html_color('calendar_color', $calendar->get_color(), '#f15e6e');

            Admin_Html::html_info(__('Default Icon', 'stec'));
            Admin_Html::html_icon('calendar_icon', Admin_Helper::get_icon_list(), $calendar->get_icon(), true);

            Admin_Html::html_info(__('Timezone', 'stec'));
            Admin_Html::html_select('calendar_timezone', Admin_Helper::timezones_list(), $calendar->get_timezone(), __('Calendar Timezone', 'stec'));

            Admin_Html::html_info(__('Front-End Visibility', 'stec'));
            Admin_Html::html_select('calendar_visibility', Admin_Helper::calendar_visibility_list(), $calendar->get_visibility(), __('Calendar Front-End Visibility', 'stec'));

            Admin_Html::html_info(__('Back-End Visibility', 'stec'));
            Admin_Html::html_select('calendar_back_visibility', Admin_Helper::calendar_visibility_list(), $calendar->get_back_visibility(), __('Calendar Back-End Visibility', 'stec'));

            Admin_Html::html_info(__('Who can add events from the front-end', 'stec'));
            Admin_Html::html_select('calendar_writable', Admin_Helper::calendar_writable_list(), $calendar->get_writable(), true);

            Admin_Html::html_checkbox('calendar_req_approval', $calendar->get_req_approval() ? true : false, true, __('Require approval by admin for events added from font-end', 'stec'), false);

            Admin_Html::html_hidden('calendar_id', $calendar->get_id());
            Admin_Html::html_hidden('task', 'stec_update_calendar');
            Admin_Html::html_button(__('Update Calendar', 'stec'));
            Admin_Html::html_button(__('Go Back', 'stec'), "?page={$plugin_page}", true);

            Admin_Html::html_form_end();
            ?>

        </div>

    <?php endif; ?>

</div>
