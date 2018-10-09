<?php

namespace Stachethemes\Stec;
?>
<div class="stachethemes-admin-section-tab" data-tab="schedule">

    <?php
    Admin_Html::html_info(__('Schedule Timespan', 'stec'));

    if ( $event ) :

        foreach ( $event->get_schedule() as $schedule ) :
        
            ?>
            <div class="stachethemes-admin-schedule-timespan">

                <div class="stachethemes-admin-schedule-timespan-head">
                    <p class="stachethemes-admin-schedule-timespan-title"><span></span><span><?php _e('Schedule Timespan (optional)', 'stec'); ?></span></p>

                    <div>
                        <?php
                        Admin_Html::html_button(__('Expand', 'stec'), '', true, "light-btn expand");
                        Admin_Html::html_button(__('Collapse', 'stec'), '', true, "light-btn collapse");
                        Admin_Html::html_button(__('Delete', 'stec'), '', true, "light-btn delete");
                        ?>
                    </div>
                </div>

                <div class="stachethemes-admin-schedule-toggle-wrap">
                    <?php
                    Admin_Html::html_info(__('Date and time', 'stec'));
                    ?>
                    <div class="stachethemes-admin-section-flex">
                        <?php
                        $schedule_fulldate = explode(' ', $schedule->start_date);
                        $schedule_fulltime = explode(':', $schedule_fulldate[1]);

                        Admin_Html::html_date("schedule[0][schedule_date_from]", $schedule_fulldate[0], '', __('Date', 'stec'), false);
                        Admin_Html::html_select("schedule[0][schedule_time_hours_from]", Admin_Helper::get_hours_array(), $schedule_fulltime[0], '', true);
                        Admin_Html::html_select("schedule[0][schedule_time_minutes_from]", Admin_Helper::minutes_array(), $schedule_fulltime[1], '', true);
                        ?>
                    </div>
                    <?php
                    Admin_Html::html_info(__('Title', 'stec'));
                    Admin_Html::html_input("schedule[0][schedule_title]", $schedule->title, '', __('Timespan Title', 'stec'), false);

                    Admin_Html::html_info(__('Icon', 'stec'));
                    Admin_Html::html_select("schedule[0][schedule_icon]", Admin_Helper::get_icon_list(), $schedule->icon, false);

                    Admin_Html::html_info(__('Icon Color', 'stec'));
                    Admin_Html::html_color("schedule[0][schedule_icon_color]", $schedule->icon_color, "#000000");

                    Admin_Html::html_info(__('Details', 'stec'));
                    Admin_Html::html_textarea("schedule[0][schedule_details]", $schedule->details, '', __('Timespan Details', 'stec'), false);
                    ?>
                </div>

            </div>

            <?php
        endforeach;

    endif;
    ?>

    <!-- schedule template --> 
    <div class="stachethemes-admin-schedule-timespan stachethemes-admin-schedule-timespan-template">

        <div class="stachethemes-admin-schedule-timespan-head">
            <p class="stachethemes-admin-schedule-timespan-title"><span></span><span><?php _e('Schedule Timespan (optional)', 'stec'); ?></span></p>

            <div>
                <?php
                Admin_Html::html_button(__('Expand', 'stec'), '', true, "light-btn expand");
                Admin_Html::html_button(__('Collapse', 'stec'), '', true, "light-btn collapse");
                Admin_Html::html_button(__('Delete', 'stec'), '', true, "light-btn delete");
                ?>
            </div>
        </div>

        <div class="stachethemes-admin-schedule-toggle-wrap">
            <?php
            Admin_Html::html_info(__('Date and time', 'stec'));
            ?>
            <div class="stachethemes-admin-section-flex">
                <?php
                Admin_Html::html_date('schedule[0][schedule_date_from]', '', '', __('Date', 'stec'), false);
                Admin_Html::html_select('schedule[0][schedule_time_hours_from]', Admin_Helper::get_hours_array(), '00', '', true);
                Admin_Html::html_select('schedule[0][schedule_time_minutes_from]', Admin_Helper::minutes_array(), '00', '', true);
                ?>
            </div>
            <?php
            Admin_Html::html_info(__('Title', 'stec'));
            Admin_Html::html_input('schedule[0][schedule_title]', '', '', __('Timespan Title', 'stec'), false);

            Admin_Html::html_info(__('Icon', 'stec'));
            Admin_Html::html_select("schedule[0][schedule_icon]", Admin_Helper::get_icon_list(), 'fa', false);

            Admin_Html::html_info(__('Icon Color', 'stec'));
            Admin_Html::html_color("schedule[0][schedule_icon_color]", "#000000", "#000000");

            Admin_Html::html_info(__('Details', 'stec'));
            Admin_Html::html_textarea('schedule[0][schedule_details]', '', '', __('Timespan Details', 'stec'), false);
            ?>
        </div>
    </div>


    <?php
    Admin_Html::html_button(__('Add Timespan', 'stec'), false, false, 'add-schedule-timespan');
    ?>

</div>
