<?php

namespace Stachethemes\Stec;
?>
<div class="stachethemes-admin-wrapper">

    <?php echo Admin_Helper::display_message(); ?>

    <?php if ( isset($calendar) ) : ?>

        <h1 class="stachethemes-admin-flex-head">
            <?php
            _e('Export Events Management', 'stec');
            echo " / " . $calendar->title;
            Admin_Html::html_button(__('Switch Calendar', 'stec'), "?page={$plugin_page}&task=reset_calendar_session", true);
            ?>
        </h1>

    <?php else: ?>

        <h1>
            <?php
            _e('Export Events Management / Select Calendar', 'stec');
            ?>
        </h1>

    <?php endif ?>


    <?php if ( isset($calendars) ) : ?>

        <div class="stachethemes-admin-section">

            <h2><?php _e('Select the calendar from which events will be exported', 'stec'); ?></h2>

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
                            <p class='color' style='background: <?php echo $calendar->get_color(); ?>'></p>
                            <p class='title'><a href='<?php echo "?page=stec_menu__export&task=stec_export_to_ics&calendar_id={$calendar->get_id()}"; ?>'><?php echo $calendar->get_title(); ?> (id#<?php echo $calendar->get_id(); ?>)</a></p>
                        </div>
                        <div class='ctrl'>
                            <a href='<?php echo "?page=stec_menu__export&view=list&calendar_id={$calendar->get_id()}"; ?>'><?php _e('View Events', 'stec'); ?></a>
                            <a href='<?php echo "?page=stec_menu__export&task=stec_export_to_ics&calendar_id={$calendar->get_id()}"; ?>'><?php _e('Export', 'stec'); ?></a>
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