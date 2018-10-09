<?php

namespace Stachethemes\Stec;
?>
<div class="stachethemes-admin-section-tab" data-tab="general">

    <?php
    Admin_Html::html_info(__('Desired name of the event (Summary)', 'stec'));
    Admin_Html::html_input('summary', $event ? $event->get_title() : null, '', __('Event Name', 'stec'), true);

    Admin_Html::html_info(__('Title slug. Must be unique. Leave empty to auto-generate.', 'stec'));
    Admin_Html::html_input('alias', $event ? $event->get_slug() : null, '', __('Slug', 'stec'), false);

    Admin_Html::html_info(__('Desired color. By default will use the calendar color.', 'stec'));
    Admin_Html::html_color('event_color', $event ? $event->get_color() : null, $calendar->get_color());

    Admin_Html::html_info(__('Event Icon', 'stec'));
    Admin_Html::html_icon('icon', Admin_Helper::get_icon_list(), $event ? $event->get_icon() : $calendar->get_icon(), true);

    if ( $event_id !== false && isset($cal_array) && count($cal_array) > 1 ) {
        Admin_Html::html_info(__('Calendar', 'stec'));
        Admin_Html::html_select('calid', $cal_array, $calendar_id, true);

        $calendars = Calendars::get_admin_calendars();
        foreach ( $calendars as $cal ) :
            Admin_Html::html_hidden("calendar_colors_by_id[" . $cal->get_id() . "]", $cal->get_color());
        endforeach;
    }

    Admin_Html::html_info(__('Front-End Visibility', 'stec'));
    Admin_Html::html_select('visibility', Admin_Helper::event_visibility_list(), $event ? $event->get_visibility() : null, true);

    Admin_Html::html_info(__('Back-End Visibility', 'stec'));
    Admin_Html::html_select('back_visibility', Admin_Helper::event_visibility_list(), $event ? $event->get_back_visibility() : null, true);

    Admin_Html::html_info(__('Featured', 'stec'));
    Admin_Html::html_select('featured', array(
            0 => __('No', 'stec'),
            1 => __('Yes', 'stec'),
            2 => __('Yes with Background', 'stec')
            ), $event ? $event->get_featured() : 0, true);

    Admin_Html::html_info(__('Starts On', 'stec'));
    ?>
    <div class="stachethemes-admin-section-flex">
        <?php
        if ( $event ) {
            $start_fulldate = explode(' ', $event->get_start_date());
            $start_fulltime = explode(':', $start_fulldate[1]);

            $end_fulldate = explode(' ', $event->get_end_date());
            $end_fulltime = explode(':', $end_fulldate[1]);
        }

        Admin_Html::html_date('start_date', isset($start_fulldate) ? $start_fulldate[0] : date('Y-m-d'), '', __('Start Date', 'stec'), true);
        Admin_Html::html_select('start_time_hours', Admin_Helper::get_hours_array(), isset($start_fulltime) ? $start_fulltime[0] : null, '', true);
        Admin_Html::html_select('start_time_minutes', Admin_Helper::minutes_array(), isset($start_fulltime) ? $start_fulltime[1] : null, '', true);
        ?>
    </div>


    <?php
    Admin_Html::html_info(__('Ends On', 'stec'));
    ?>
    <div class="stachethemes-admin-section-flex">
        <?php
        Admin_Html::html_date('end_date', isset($end_fulldate) ? $end_fulldate[0] : null, date('Y-m-d'), __('End Date', 'stec'), true);
        Admin_Html::html_select('end_time_hours', Admin_Helper::get_hours_array(), isset($end_fulltime) ? $end_fulltime[0] : null, '', true);
        Admin_Html::html_select('end_time_minutes', Admin_Helper::minutes_array(), isset($end_fulltime) ? $end_fulltime[1] : null, '', true);
        ?>
    </div>

    <?php
    Admin_Html::html_checkbox('all_day', ($event ? ($event->get_all_day() ? 1 : 0) : 1), 0, __('All Day', 'stec'));
    ?>

    <?php
    if ( !$event || !$event->get_recurrence_id() ) :
        Admin_Html::html_info(__('Repeater Scheme ', 'stec'));
        Admin_Html::html_button(__('Set repeater', 'stec'), '', false, "blue-button set-repeater-button");
        ?>
        <p class="stec-repeater-summary"><?php _e('Summary: ', 'stec') ?>
            <span>-</span>
        </p>
    <?php endif; ?>

    <?php
    Admin_Html::html_info(__('Search keywords (optional)', 'stec'));
    Admin_Html::html_input('keywords', $event ? $event->get_keywords() : null, '', __('Search keywords', 'stec'), false);
    ?>

    <?php
    Admin_Html::html_info(__('Counter', 'stec'));
    Admin_Html::html_select('counter', array(
            '0' => __('Disable', 'stec'),
            '1' => __('Enable', 'stec')
            )
            , $event ? $event->get_counter() : null, true);
    ?>

    <?php
    Admin_Html::html_info(__('Comments', 'stec'));
    Admin_Html::html_select('comments', array(
            '0' => __('Disable', 'stec'),
            '1' => __('Enable', 'stec')
            )
            , $event ? $event->get_comments() : null, true);
    ?>

</div>

<div class="stec-repeater-popup-bg"></div>
<div class="stec-repeater-popup" tabindex="0">
    <div>
        <table>
            <tbody>
                <tr>
                    <th><?php _e('Repeats:', 'stec') ?></th>
                    <td>
                        <select name="repeat_freq">
                            <option value="0"><?php _e('No Repeat', 'stec') ?></option>
                            <option value="1"><?php _e('Daily', 'stec') ?></option>
                            <option value="2"><?php _e('Weekly', 'stec') ?></option>
                            <option value="3"><?php _e('Monthly', 'stec') ?></option>
                            <option value="4"><?php _e('Yearly', 'stec') ?></option>
                        </select>
                    </td>
                </tr>

                <tr class="stec-repeater-popup-weekdays">
                    <th><?php _e('By Day:', 'stec') ?></th>
                    <td class="stec-repeater-popup-repeat-on">
                        <div>
                            <span>
                                <input name="SU" title="<?php _e('Sunday', 'stec') ?>" type="checkbox">
                                <label for="" title="<?php _e('Sunday', 'stec') ?>"><?php _e('SU', 'stec') ?></label>
                            </span>
                            <span>
                                <input name="MO" title="<?php _e('Monday', 'stec') ?>" type="checkbox">
                                <label for="" title="<?php _e('Monday', 'stec') ?>"><?php _e('MO', 'stec') ?></label>
                            </span>
                            <span>
                                <input name="TU" title="<?php _e('Tuesday', 'stec') ?>" type="checkbox">
                                <label for="" title="<?php _e('Tuesday', 'stec') ?>"><?php _e('TU', 'stec') ?></label>
                            </span>
                            <span>
                                <input name="WE" title="<?php _e('Wednesday', 'stec') ?>" type="checkbox">
                                <label for="" title="<?php _e('Wednesday', 'stec') ?>"><?php _e('WE', 'stec') ?></label>
                            </span>
                            <span>
                                <input name="TH" title="<?php _e('Thursday', 'stec') ?>" type="checkbox">
                                <label for="" title="<?php _e('Thursday', 'stec') ?>"><?php _e('TH', 'stec') ?></label>
                            </span>
                            <span>
                                <input name="FR" title="<?php _e('Friday', 'stec') ?>" type="checkbox">
                                <label for="" title="<?php _e('Friday', 'stec') ?>"><?php _e('FR', 'stec') ?></label>
                            </span>
                            <span>
                                <input name="SA" title="<?php _e('Saturday', 'stec') ?>" type="checkbox">
                                <label for="" title="<?php _e('Saturday', 'stec') ?>"><?php _e('SA', 'stec') ?></label>
                            </span>
                        </div>

                    </td>
                </tr>

            </tbody>
            <tbody>
                <tr>
                    <th><?php _e('Repeat gap:', 'stec') ?></th>
                    <td>
                        <select name="repeat_gap">
                            <option value=""><?php _e('No gap', 'stec') ?></option>
                            <?php
                            for ( $i = 2; $i < 31; $i++ ) {
                                ?>
                                <option value="<?php echo $i; ?>"><?php echo $i - 1; ?></option>
                                <?php
                            }
                            ?>
                        </select>

                    </td>
                </tr>
                <tr>
                    <th>
                        <?php _e('Repeat ends on:', 'stec') ?>
                    </th>
                    <td class="stec-repeater-popup-endson-options">
                        <span>
                            <input id="stec-repeater-popup-repeat-endson-never" name="repeat_endson" value="0" checked="checked" type="radio">
                            <label for="stec-repeater-popup-repeat-endson-never"><?php _e('Never', 'stec') ?></label>
                        </span>
                        <span>
                            <input id="stec-repeater-popup-repeat-endson-after-n" name="repeat_endson" value="1" type="radio">
                            <label for="stec-repeater-popup-repeat-endson-after-n">
                                <?php _e('After', 'stec') ?> 
                                <input name="repeat_occurences" size="3" value="" disabled="disabled">
                                <?php _e('occurences', 'stec') ?>
                            </label>
                        </span>
                        <span>
                            <input id="stec-repeater-popup-repeat-endson-date" name="repeat_endson" value="2" type="radio">
                            <label for="stec-repeater-popup-repeat-endson-date">
                                <?php _e('On Date', 'stec') ?>
                                <input id="repeat_end_date" name="repeat_ends_on_date" size="10" value="" disabled="disabled" autocomplete="off">
                            </label>
                        </span>
                    </td>
                </tr>
                <tr class="stec-repeater-popup-exdate-menu">
                    <th>
                        <?php _e('Date Exception:', 'stec') ?>
                    </th>
                    <td class="stec-repeater-popup-exdate-options">
                        <input id="stec-repeater-popup-exdate-datepicker" value="" type="text">
                        <input id="stec-repeater-popup-exdate-datepicker-exdate-value" value="" type="hidden">
                        <a id="stec-add-exdate" href="javascript:void(0);"><?php _e('Add exdate', 'stec'); ?></a>
                        <ul class="stec-repeater-popup-exdate-datelist">
                            <li class="stec-repeater-popup-exdate-datelist-template">
                                <span>stec_replace_date</span>
                                <span class="stec-repeater-popup-exdate-datelist-submit-value">stec_replace_altdate</span>
                                <a class="stec-remove-exdate" href="javascript:void(0);">
                                    <?php _e('Remove', 'stec'); ?>
                                </a>
                            </li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Summary:', 'stec') ?></th>
                    <td class="stec-repeater-popup-repeat-summary">-</td>
                </tr>
            </tbody>
            <tbody class="stec-repeater-popup-repeat-advanced">
                <tr>
                    <th><?php _e('RRULE String:', 'stec') ?></th>
                    <td>
                        <input type="text" name="advanced_rrule" value="<?php echo $event ? $event->get_rrule() : null; ?>" />
                        <input type="hidden" name="is_advanced_rrule" value="<?php echo $event ? $event->get_is_advanced_rrule() : 0; ?>" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="stec-repeater-popup-buttons">
        <?php
        Admin_Html::html_button(__('Update', 'stec'), '', false, "blue-button");
        Admin_Html::html_button(__('Cancel', 'stec'), '', true, "blue-button");
        ?>
    </div>
    <?php
    Admin_Html::html_hidden('rrule', $event ? $event->get_rrule() : '');
    Admin_Html::html_hidden('exdate', $event ? $event->get_exdate() : '');
    ?>
</div>