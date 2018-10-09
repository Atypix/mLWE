<?php

namespace Stachethemes\Stec;
?>

<div class="stachethemes-admin-wrapper">

    <?php echo Admin_Helper::display_message(); ?>


    <h1 class="stachethemes-admin-flex-head">
        <?php
        _e('Export Events Management ', 'stec');
        echo " / " . $calendar->get_title();
        Admin_Html::html_button(__('Switch Calendar', 'stec'), "?page={$plugin_page}&task=reset_calendar_session", true);
        ?>
    </h1>

    <div class="stachethemes-admin-section">

        <h2><?php _e('Export Single Events', 'stec'); ?></h2>

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

                    <?php Admin_Html::html_form_start("?page=$plugin_page&view=list&calendar_id={$calendar_id}"); ?>

                    <button class='export-all-items'><p><i class="fa fa-calendar-check-o"></i><?php _e('Export selected events', 'stec'); ?></p></button>

                    <?php
                    Admin_Html::html_hidden('calendar_id', $calendar_id);
                    Admin_Html::html_hidden('task', 'stec_export_to_ics_bulk');
                    ?>

                    <?php Admin_Html::html_form_end(); ?>

                </div>   
            </div>
            <?php
            echo '<ul class="stec-list">';

            foreach ( $events as $event ) :
                ?>
                <li>
                    <div class='calinfo'>
                        <input type="checkbox" name="eventid" value="<?php echo $event->get_id(); ?>" />
                        <p class='color' style='background: <?php echo $event->get_color() ? $event->get_color() : $calendar->get_color(); ?>'></p>
                        <p class='title'><a href='<?php echo "?page={$plugin_page}&task=stec_export_to_ics&calendar_id={$calendar_id}&event_id={$event->get_id()}"; ?>'><?php echo $event->get_title(); ?> (id#<?php echo $event->get_id(); ?>)</a></p>
                    </div>
                    <div class='ctrl'>
                        <a href='<?php echo "?page={$plugin_page}&task=stec_export_to_ics&calendar_id={$calendar_id}&event_id={$event->get_id()}"; ?>'><?php _e('Export', 'stec'); ?></a>
                    </div>
                </li>
                <?php
            endforeach;

            echo '</ul>';
        endif;
        ?>

    </div>


</div>