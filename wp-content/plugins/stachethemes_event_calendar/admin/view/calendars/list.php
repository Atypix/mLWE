<?php

namespace Stachethemes\Stec;
?>



<div class="stachethemes-admin-wrapper">

    <?php echo Admin_Helper::display_message(); ?>

    <h1><?php _e('Calendars Management', 'stec'); ?></h1>

    <div class="stachethemes-admin-section">

        <h2><?php _e('Create new calendar', 'stec'); ?></h2>

        <?php
        Admin_Html::html_form_start("?page=$plugin_page", "POST");

        Admin_Html::html_info(__('Desired name of the calendar:', 'stec'));
        Admin_Html::html_input('calendar_name', '', '', __('Calendar Name', 'stec'), true);

        Admin_Html::html_info(__('Desired color. This will be the default color of the events. Can be changed later.', 'stec'));
        Admin_Html::html_color('calendar_color', '', '#f15e6e');

        Admin_Html::html_info(__('Default Icon', 'stec'));
        Admin_Html::html_icon('calendar_icon', Admin_Helper::get_icon_list(), null, true);

        Admin_Html::html_info(__('Timezone', 'stec'));
        Admin_Html::html_select('calendar_timezone', Admin_Helper::timezones_list(), "UTC", __('Calendar Timezone', 'stec'));

        Admin_Html::html_info(__('Front-End Visibility', 'stec'));
        Admin_Html::html_select('calendar_visibility', Admin_Helper::calendar_visibility_list(), '1', __('Calendar Front-End Visibility', 'stec'));

        Admin_Html::html_info(__('Back-End Visibility', 'stec'));
        Admin_Html::html_select('calendar_back_visibility', Admin_Helper::calendar_visibility_list(), '1', __('Calendar Back-End Visibility', 'stec'));

        Admin_Html::html_info(__('Who can add events from the front-end', 'stec'));
        Admin_Html::html_select('calendar_writable', Admin_Helper::calendar_writable_list(), '0', true);

        Admin_Html::html_checkbox('calendar_req_approval', true, true, __('Require approval by admin for events added from font-end', 'stec'), false);

        Admin_Html::html_hidden('task', 'stec_create_calendar');
        Admin_Html::html_button(__('Create Calendar', 'stec'));

        Admin_Html::html_form_end();
        ?>

    </div>

    <h1><?php _e('Manage Calendars', 'stec'); ?></h1>

    <div class="stachethemes-admin-section">

        <h2><?php _e('List with your created calendars', 'stec'); ?></h2>

        <?php
        if ( empty($calendars) ) :

            Admin_Html::html_info(__('(No calendars to display)', 'stec'));

        else :
            ?>
            <div class="stec-list-bulk">
                <div>
                    <input type="checkbox" name="all_calendars" value="1" /> <?php __('Select all calendars', 'stec'); ?>
                    <p><?php _e('Selected all calendars', 'stec'); ?></p>

                    <?php Admin_Html::html_form_start("?page=$plugin_page"); ?>

                    <button class='delete-all-items' data-confirm='<?php _e('Delete all items?', 'stec'); ?>'><p><i class="fa fa-trash-o"></i><?php _e('Delete selected calendars', 'stec'); ?></p></button>

                    <?php
                    Admin_Html::html_hidden('task', 'stec_delete_bulk_calendars');
                    ?>

                    <?php Admin_Html::html_form_end(); ?>
                </div>   
            </div>
            <?php
            echo '<ul class="stec-list">';

            foreach ( $calendars as $calendar ) :

                if ( !$calendar instanceof Calendar_Post ) {
                    continue;
                }

                $add_text       = '';
                $aaproval_count = Events::get_aaproval_count($calendar->get_id());
                if ( $aaproval_count > 0 ) {
                    $add_text = '-- ' . $aaproval_count . ' ' . ($aaproval_count == 1 ? __('event awaiting approval', 'stec') : __('events awaiting approval', 'stec')) . ' --';
                }
                ?>
                <li>
                    <div class='calinfo'>
                        <input type="checkbox" name="calid" value="<?php echo $calendar->get_id(); ?>" />
                        <p class='color' style='background: <?php echo $calendar->get_color(); ?>'>
                            <i class="<?php echo $calendar->get_icon(); ?>"></i>
                        </p>
                        <p class='title'><a href='<?php echo "?page=stec_menu__events&calendar_id={$calendar->get_id()}"; ?>'><?php echo $calendar->get_title(); ?> (id#<?php echo $calendar->get_id(); ?>)</a> <span class="stec-text-red"><?php echo $add_text; ?></span></p>
                    </div>
                    <div class='ctrl'>
                        <a href='<?php echo "?page=stec_menu__events&calendar_id={$calendar->get_id()}"; ?>'><?php _e('View Events', 'stec'); ?></a>
                        <a href='<?php echo "?page={$plugin_page}&view=edit&calendar_id={$calendar->get_id()}"; ?>'><?php _e('Edit', 'stec'); ?></a>
                        <a class='delete-item' data-confirm='<?php _e('Delete item?', 'stec'); ?>' href='<?php echo wp_nonce_url("?page={$plugin_page}&task=stec_delete_calendar&calendar_id={$calendar->get_id()}"); ?>'><?php _e('Delete', 'stec'); ?></a>
                    </div>
                </li>
                <?php
            endforeach;

            echo '</ul>';
        endif;
        ?>

    </div>

</div>
