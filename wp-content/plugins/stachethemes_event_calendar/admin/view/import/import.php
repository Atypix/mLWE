<?php

namespace Stachethemes\Stec; ?>

<div class="stachethemes-admin-wrapper">

    <?php echo Admin_Helper::display_message(); ?>

    <?php if ( isset($calendar) ) : ?>

        <h1 class="stachethemes-admin-flex-head">
            <?php
            _e('Import Events Management', 'stec');
            echo " / " . $calendar->get_title();
            Admin_Html::html_button(__('Switch Calendar', 'stec'), "?page={$plugin_page}&task=reset_calendar_session", true);
            ?>
        </h1>

    <?php else: ?>

        <h1>
            <?php
            _e('Import Events Management / Select Calendar', 'stec');
            ?>
        </h1>

    <?php endif ?>

    <?php if ( isset($calendars) ) : ?>

        <div class="stachethemes-admin-section">

            <h2><?php _e('Select the calendar on which the events should be imported', 'stec'); ?></h2>

            <?php
            if ( empty($calendars) ) :

                Admin_Html::html_info(__('You have no calendars', 'stec'));

                Admin_Html::html_button(__('Create calendar', 'stec'), '?page=stec_menu__calendars');

            else:

                echo '<ul class="stec-list">';

                foreach ( $calendars as $calendar ) :
                    ?>
                    <li>
                        <div class='calinfo'>
                            <p class='color' style='background: <?php echo $calendar->get_color(); ?>'>
                                <i class="<?php echo $calendar->get_icon(); ?>"></i>
                            </p>
                            <p class='title'><a href='<?php echo "?page=stec_menu__import&calendar_id={$calendar->get_id()}"; ?>'><?php echo $calendar->get_title(); ?> (id#<?php echo $calendar->get_id(); ?>)</a></p>
                        </div>
                        <div class='ctrl'>
                            <a href='<?php echo "?page=stec_menu__import&calendar_id={$calendar->get_id()}"; ?>'><?php _e('Select', 'stec'); ?></a>
                        </div>
                    </li>
                    <?php
                endforeach;

                echo '</ul>';

            endif;
            ?>

        </div>

    <?php endif; ?>

    <?php if ( $calendar_id ) : ?>

        <div class="stachethemes-admin-section">

            <h2><?php _e('Import .ics Events', 'stec'); ?></h2>

            <?php
            Admin_Html::html_form_start("?page=$plugin_page&calendar_id={$calendar_id}", "POST", true);

            Admin_Html::html_info(__('Import from URL', 'stec'));
            Admin_Html::html_input('ics_url', '', '', __('URL', 'stec'), false);

            Admin_Html::html_info(__('Import from .ics file', 'stec'));
            Admin_Html::html_input('ics_filename', '', '', __('ICS File', 'stec'), false, "file", 'accept=".ics"');

            Admin_Html::html_info(__('Event Icon', 'stec'));
            Admin_Html::html_icon('icon', Admin_Helper::get_icon_list(), $calendar->get_icon(), true);

            Admin_Html::html_checkbox('ignore_expired', 1, 1, __("Don't import expired events", 'stec'), false);
            ?>
            <p class="desc">
                <?php __('If checked non-recurring expired events will be ignored.', 'stec'); ?>
            </p>
            <?php
            Admin_Html::html_checkbox('overwrite_events', 0, 0, __('Overwrite events', 'stec'), false);
            ?>
            <p class="desc">
                <?php __('Existing events will be overwritten.', 'stec'); ?>
            </p>
            <?php
            Admin_Html::html_checkbox('delete_removed', 0, 0, __('Delete events', 'stec'), false);
            ?>
            <p class="desc">
                <?php __('Delete calendar event if it does not exist in the ics file.', 'stec'); ?>
            </p>
            <?php
            Admin_Html::html_hidden('calendar_id', $calendar_id);
            Admin_Html::html_hidden('task', 'import');
            Admin_Html::html_button(__('Import Events', 'stec'));

            Admin_Html::html_form_end();
            ?>


        </div>

        <h1><?php _e('Auto-import events from URL', 'stec'); ?></h1>

        <div class="stachethemes-admin-section">

            <h2><?php _e('Schedule Cronjob', 'stec'); ?></h2>

            <?php
            Admin_Html::html_form_start("?page=$plugin_page&calendar_id={$calendar_id}");

            Admin_Html::html_info(__('Import URL', 'stec'));
            Admin_Html::html_input('ics_url', '', '', __('URL', 'stec'), false);

            Admin_Html::html_info(__('Cronjob Frequency', 'stec'));
            Admin_Html::html_select('ics_cronjob_freq', array(
                    '0' => __('Hourly', 'stec'),
                    '1' => __('Twice Daily', 'stec'),
                    '2' => __('Daily', 'stec'),
                    '3' => __('Weekly', 'stec')
                    ), 0, false);

            Admin_Html::html_info(__('Event Icon', 'stec'));
            Admin_Html::html_icon('icon', Admin_Helper::get_icon_list(), $calendar->get_icon(), true);

            Admin_Html::html_checkbox('ignore_expired', 1, 1, __("Don't import expired events", 'stec'), false);
            ?>

            <p class="desc">
                <?php __('If checked non-recurring expired events will be ignored.', 'stec'); ?>
            </p>

            <?php
            Admin_Html::html_checkbox('overwrite_events', 0, 0, __('Overwrite events', 'stec'), false);
            ?>
            <p class="desc">
                <?php __('Existing events will be overwritten.', 'stec'); ?>
            </p>

            <?php
            Admin_Html::html_checkbox('delete_removed', 0, 0, __('Delete events', 'stec'), false);
            ?>
            <p class="desc">
                <?php __('Delete calendar event if it does not exist in the ics file.', 'stec'); ?>
            </p>

            <?php
            Admin_Html::html_hidden('calendar_id', $calendar_id);
            Admin_Html::html_hidden('task', 'create_cronjob');
            Admin_Html::html_button(__('Create Cronjob', 'stec'));

            Admin_Html::html_form_end();
            ?>

        </div>

        <h1><?php _e('Manage Cronjob list', 'stec'); ?></h1>

        <div class="stachethemes-admin-section">

            <h2><?php _e('List of your created cronjobs', 'stec'); ?></h2>

            <?php
            if ( empty($cronjobs) ) :

                Admin_Html::html_info(__('(No cronjobs to display)', 'stec'));

            else :
                ?>
                <div class="stec-list-bulk">
                    <div>
                        <input type="checkbox" name="all_cronjobs" value="1" /> <?php __('Select all jobs', 'stec'); ?>
                        <p><?php _e('Selected all jobs', 'stec'); ?></p>

                        <?php Admin_Html::html_form_start("?page=$plugin_page&calendar_id={$calendar_id}"); ?>

                        <button class='delete-all-items' data-confirm='<?php _e('Delete all jobs?', 'stec'); ?>'><p><i class="fa fa-trash-o"></i><?php _e('Delete selected jobs', 'stec'); ?></p></button>

                        <?php
                        Admin_Html::html_hidden('calendar_id', $calendar_id);
                        Admin_Html::html_hidden('task', 'delete_bulk');
                        ?>

                        <?php Admin_Html::html_form_end(); ?>
                    </div>   
                </div>
                <?php
                echo '<ul class="stec-list">';

                foreach ( $cronjobs as $job ) :

                    $importer = $job->get_custom_meta('import');
                    $freq     = $job->get_custom_meta('freq');

                    if ( !$importer instanceof Import || is_nan($freq) ) {
                        continue;
                    }

                    $calendar = new Calendar_Post($importer->get_calendar_id());
                    $icon     = $importer->get_icon() ? $importer->get_icon() : $calendar->get_icon();
                    ?>
                    <li>
                        <div class='calinfo'>
                            <input type="checkbox" name="id" value="<?php echo $job->get_id(); ?>" />
                            <p class='color' style='background: <?php echo $calendar->get_color(); ?>'><i class="<?php echo $icon; ?>"></i></p>
                            <p class='title'>
                                <?php echo htmlspecialchars($importer->get_ics_url()); ?>
                                (<?php
                                switch ( $freq ) :
                                    case 0: _e('hourly', 'stec');
                                        break;
                                    case 1: _e('twice daily', 'stec');
                                        break;
                                    case 2: _e('daily', 'stec');
                                        break;
                                    case 3: _e('once weekly', 'stec');
                                        break;
                                endswitch;

                                if ( $importer->get_overwrite_events() == 1 ) :
                                    echo ', ' . __('overwrites', 'stec');
                                endif;

                                if ( $importer->get_ignore_expired() == 1 ) :
                                    echo ', ' . __('ignores expired', 'stec');
                                endif;

                                if ( $importer->get_delete_removed() == 1 ) :
                                    echo ', ' . __('deletes obsolete', 'stec');
                                endif;
                                ?>)
                            </p>
                        </div>
                        <div class='ctrl'>
                            <a class='delete-item' data-confirm='<?php _e('Delete item?', 'stec'); ?>' href='<?php echo wp_nonce_url("?page={$plugin_page}&task=delete&id={$job->get_id()}&calendar_id={$calendar_id}"); ?>'><?php _e('Delete', 'stec'); ?></a>
                        </div>
                    </li>
                    <?php
                endforeach;

                echo '</ul>';
            endif;
            ?>

        </div>

    <?php endif; ?>

</div>

