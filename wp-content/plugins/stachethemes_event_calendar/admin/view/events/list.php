<?php

namespace Stachethemes\Stec;
?>
<div class="stachethemes-admin-wrapper">

    <?php echo Admin_Helper::display_message(); ?>

    <?php if ( isset($calendar) ) : ?>

        <h1 class="stachethemes-admin-flex-head">
            <?php
            _e('Events Management', 'stec');
            echo " / " . $calendar->get_title();
            Admin_Html::html_button(__('Switch Calendar', 'stec'), "?page={$plugin_page}&task=reset_calendar_session", true);
            ?>
        </h1>

    <?php else: ?>

        <h1>
            <?php
            _e('Events Management / Select Calendar', 'stec');
            ?>
        </h1>

    <?php endif ?>

    <?php if ( isset($calendars) ) : ?>

        <div class="stachethemes-admin-section">

            <h2><?php _e('Select Calendar', 'stec'); ?></h2>

            <?php
            if ( empty($calendars) ) :


                Admin_Html::html_info(__('You have no calendars', 'stec'));

                Admin_Html::html_button(__('Create calendar', 'stec'), '?page=stec_menu__calendars');

            else:

                echo '<ul class="stec-list">';

                foreach ( $calendars as $calendar ) :

                    $add_text       = '';
                    $aaproval_count = Events::get_aaproval_count($calendar->get_id());
                    if ( $aaproval_count > 0 ) {
                        $add_text = '-- ' . $aaproval_count . ' ' . ($aaproval_count == 1 ? __('event awaiting approval', 'stec') : __('events awaiting approval', 'stec')) . ' --';
                    }
                    ?>
                    <li>
                        <div class='calinfo'>
                            <p class='color' style='background: <?php echo $calendar->get_color(); ?>'>
                                <i class="<?php echo $calendar->get_icon(); ?>"></i>
                            </p>
                            <p class='title'>
                                <a href='<?php echo "?page=stec_menu__events&calendar_id={$calendar->get_id()}"; ?>'>
                                    <?php echo $calendar->get_title(); ?> (id#<?php echo $calendar->get_id(); ?>)
                                </a> 
                                <span class="stec-text-red"><?php echo $add_text; ?></span></p>
                        </div>
                        <div class='ctrl'>
                            <a href='<?php echo "?page=stec_menu__events&calendar_id={$calendar->get_id()}"; ?>'><?php _e('View Events', 'stec'); ?></a>
                        </div>
                    </li>
                    <?php
                endforeach;

                echo '</ul>';

            endif;
            ?>

        </div>

    <?php endif; ?>

    <?php if ( isset($events) ) : ?>

        <div class="stachethemes-admin-section">

            <h2><?php _e('Add Events', 'stec'); ?></h2>

            <?php
            Admin_Html::html_info(__('Add new event to this calendar', 'stec'));
            Admin_Html::html_button(__('Add Event', 'stec'), "?page={$plugin_page}&view=add&calendar_id={$calendar_id}");
            ?>

        </div>

        <div class="stachethemes-admin-section">

            <h2><?php _e('Manage Events', 'stec'); ?></h2>

            <?php
            Admin_Html::html_info(__('List of your created events', 'stec'));

            if ( empty($events) ) :

                Admin_Html::html_info(__('(No events to display)', 'stec'));

            else :
                ?>
                <div class="stec-list-bulk">
                    <div>
                        <input type="checkbox" name="all_events" value="1" /> <?php __('Select all events', 'stec'); ?>
                        <p><?php _e('Selected all events', 'stec'); ?></p>

                        <?php Admin_Html::html_form_start("?page=$plugin_page&calendar_id={$calendar_id}"); ?>

                        <button class='delete-all-items' data-confirm='<?php _e('Delete all items?', 'stec'); ?>'><p><i class="fa fa-trash-o"></i><?php _e('Delete selected events', 'stec'); ?></p></button>

                        <?php
                        Admin_Html::html_hidden('calendar_id', $calendar_id);
                        Admin_Html::html_hidden('task', 'delete_bulk');
                        ?>

                        <?php Admin_Html::html_form_end(); ?>

                    </div>   
                </div>
                <?php
                echo '<ul class="stec-list">';

                foreach ( $events as $event ) :
                    if ( !$event instanceof \Stachethemes\Stec\Event_Post ) {
                        continue;
                    }
                    ?>
                    <li>
                        <div class='calinfo'>
                            <input type="checkbox" name="eventid" value="<?php echo $event->get_id(); ?>" />
                            <p class='color' style='background: <?php echo $event->get_color() ? $event->get_color() : $calendar->get_color(); ?>'><i class="<?php echo $event->get_icon(); ?>"></i></p>
                            <p class='title'>
                                <a href='<?php echo "?page={$plugin_page}&view=edit&calendar_id={$calendar_id}&event_id={$event->get_id()}"; ?>'>
                                    <?php echo $event->get_title(); ?> (id#<?php echo $event->get_id(); ?>)</a> 
                                <?php
                                if ( $event->get_approved() == "0" ) :
                                    echo '<span class="stec-text-red"> -- ' . __('Awaiting approval', 'stec') . ' -- </span>';
                                endif
                                ?>
                                <span class="stec-event-timespan">
                                    <?php
                                    echo $event->get_start_date();

                                    if ( $event->get_rrule() != '' ) {
                                        echo ' (' . __('Recurring', 'stec') . ')';
                                    }

                                    if ( $event->get_recurrence_id() != '' ) {
                                        echo ' (' . __('Recurrence Override: ', 'stec') . $event->get_recurrence_id() . ')';
                                    }
                                    ?>
                                </span>
                            </p>
                        </div>
                        <div class='ctrl'>

                            <?php if ( $event->get_approved() == '0' ) : ?>

                                <a href='<?php echo wp_nonce_url("?page={$plugin_page}&task=approve&view=list&calendar_id={$calendar_id}&event_id={$event->get_id()}"); ?>'><?php _e('Approve', 'stec'); ?></a>
                                <a href='<?php echo wp_nonce_url("?page={$plugin_page}&view=edit&calendar_id={$calendar_id}&event_id={$event->get_id()}"); ?>'><?php _e('Review', 'stec'); ?></a>

                            <?php else: ?>

                                <a href='<?php echo wp_nonce_url("?page={$plugin_page}&task=duplicate&view=list&calendar_id={$calendar_id}&event_id={$event->get_id()}"); ?>'><?php _e('Duplicate', 'stec'); ?></a>
                                <a href='<?php echo ("?page={$plugin_page}&view=edit&calendar_id={$calendar_id}&event_id={$event->get_id()}"); ?>'><?php _e('Edit', 'stec'); ?></a>

                            <?php endif; ?>

                            <a class='delete-item' data-confirm='<?php _e('Delete item?', 'stec'); ?>' href='<?php echo wp_nonce_url("?page={$plugin_page}&calendar_id={$calendar_id}&task=delete&event_id={$event->get_id()}"); ?>'><?php _e('Delete', 'stec'); ?></a>
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