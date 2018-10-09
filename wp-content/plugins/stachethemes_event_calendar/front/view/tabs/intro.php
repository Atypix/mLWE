<?php

namespace Stachethemes\Stec; ?>
<p class="stec-layout-event-inner-intro-title stec-layout-event-title-fontandcolor">stec_replace_summary</p>

<div class="stec-layout-event-inner-intro-media">

    <div class="stec-layout-event-inner-intro-media-content">

    </div>

    <div class="stec-layout-event-inner-intro-media-content-subs">
        <div>
            <p></p>
            <span></span>
        </div>
    </div>

    <div class="stec-layout-event-inner-intro-media-controls">
        <div class="stec-layout-event-inner-intro-media-controls-prev stec-layout-event-btn-fontandcolor"><i class="fa fa-angle-left"></i></div>
        <div class="stec-layout-event-inner-intro-media-controls-list-wrap">
            <ul class="stec-layout-event-inner-intro-media-controls-list">
            </ul>
        </div>
        <div class="stec-layout-event-inner-intro-media-controls-next stec-layout-event-btn-fontandcolor"><i class="fa fa-angle-right"></i></div>
    </div>

</div>

<div class="stec-layout-event-inner-intro-desc stec-layout-event-text-fontandcolor">stec_replace_description</div>

<a target="_BLANK" class="stec-layout-event-inner-intro-external-link stec-layout-event-inner-button-style stec-layout-event-btn-fontandcolor" href="#stec_replace_link"><?php _e('Visit Website', 'stec'); ?></a>

<ul class="stec-layout-event-inner-intro-counter">
    <li>
        <p class="stec-layout-event-inner-intro-counter-num">0</p>
        <p class="stec-layout-event-inner-intro-counter-label" data-plural-label="<?php _e('Days', 'stec'); ?>" data-singular-label="<?php _e('Day', 'stec'); ?>">days</p>
    </li>
    <li>
        <p class="stec-layout-event-inner-intro-counter-num">0</p>
        <p class="stec-layout-event-inner-intro-counter-label" data-plural-label="<?php _e('Hours', 'stec'); ?>" data-singular-label="<?php _e('Hour', 'stec'); ?>">hours</p>
    </li>
    <li>
        <p class="stec-layout-event-inner-intro-counter-num">0</p>
        <p class="stec-layout-event-inner-intro-counter-label" data-plural-label="<?php _e('Minutes', 'stec'); ?>" data-singular-label="<?php _e('Minute', 'stec'); ?>">minutes</p>
    </li>
    <li>
        <p class="stec-layout-event-inner-intro-counter-num">0</p>
        <p class="stec-layout-event-inner-intro-counter-label" data-plural-label="<?php _e('Seconds', 'stec'); ?>" data-singular-label="<?php _e('Second', 'stec'); ?>">seconds</p>
    </li>
</ul>

<p class="stec-layout-event-inner-intro-event-status-text event-expired stec-layout-event-title2-fontandcolor"><?php _e('Event expired', 'stec'); ?></p>
<p class="stec-layout-event-inner-intro-event-status-text event-inprogress stec-layout-event-title2-fontandcolor"><?php _e('Event is in progress', 'stec'); ?></p>

<ul class="stec-layout-event-inner-intro-attendance">
    <li class="stec-layout-event-inner-button-style stec-layout-event-inner-intro-attendance-attend stec-layout-event-btn-fontandcolor"><p><?php _e('Attend', 'stec'); ?></p></li>
    <li class="stec-layout-event-inner-button-style stec-layout-event-inner-intro-attendance-decline stec-layout-event-btn-fontandcolor"><p><?php _e('Decline', 'stec'); ?></p></li>
</ul>

<div class="stec-layout-event-inner-intro-attachments">

    <div class="stec-layout-event-inner-intro-attachments-top">

        <p class="stec-layout-event-title2-fontandcolor"><?php _e('Attachments', 'stec'); ?></p>

        <div class="stec-layout-event-inner-intro-attachments-toggle">
            <i class="fa fa-plus"></i>
            <i class="fa fa-minus"></i>
        </div>

    </div>

    <ul class="stec-layout-event-inner-intro-attachments-list">
        <li class="stec-layout-event-inner-intro-attachment stec-layout-event-inner-intro-attachment-template">
            <div>
                <p class="stec-layout-event-inner-intro-attachment-title stec-layout-event-title2-fontandcolor"><a href="#stec_replace_url">stec_replace_filename</a></p>
                <p class="stec-layout-event-inner-intro-attachment-desc stec-layout-event-text-fontandcolor">stec_replace_desc</p>
            </div>

            <div>
                <a href="#stec_replace_url" class="stec-layout-event-title2-fontandcolor"><?php _e('Download', 'stec'); ?></a>
                <p class="stec-layout-event-inner-intro-attachment-size stec-layout-event-text-fontandcolor">stec_replace_size</p>
            </div>
        </li>
    </ul>
</div>


<?php
if (Settings::get_admin_setting_value('stec_menu__general', 'social_links') == '1' ||
        Settings::get_admin_setting_value('stec_menu__general', 'show_export_buttons') == '1') :
    ?>  

    <div class="stec-layout-event-inner-intro-share-and-export">

        <div class="stec-layout-event-inner-intro-share">
            <?php if (Settings::get_admin_setting_value('stec_menu__general', 'social_links') == '1') : ?>

                <a target="_BLANK" href="http://www.facebook.com/sharer.php?u=stec_replace_event_single_url"><i class="fa fa-facebook"></i></a>
                <a target="_BLANK" href="http://twitter.com/home?status=stec_replace_event_single_url"><i class="fa fa-twitter"></i></a>
                <a target="_BLANK" href="https://plus.google.com/share?url=stec_replace_event_single_url"><i class="fa fa-google-plus"></i></a>
                <a class="stec-embed-button" target="_BLANK" href="#stec_replace_event_single_url"><i class="fa fa-link"></i></a>

            <?php endif; ?>
        </div>

        <?php if (Settings::get_admin_setting_value('stec_menu__general', 'show_export_buttons') == '1') : ?>

            <div class="stec-layout-event-inner-intro-export">
                <form method="POST"> 
                    <button class="stec-layout-event-inner-button-sec-style stec-layout-event-btn-sec-fontandcolor"><?php _e('Export to .ICS file', 'stec'); ?></button>
                    <input type="hidden" value="stec_replace_event_id" name="event_id">
                    <input type="hidden" value="stec_replace_calendar_id" name="calendar_id">
                    <input type="hidden" value="stec_public_export_to_ics" name="task">
                </form>

                <a class="stec-layout-event-inner-button-sec-style stec-layout-event-btn-sec-fontandcolor" href="#stec_replace_googlecal_import" target="_BLANK" class="stec-layout-event-text-fontandcolor"><?php _e('Import to Google Calendar', 'stec'); ?></a>
            </div>

        <?php endif; ?>

    </div>

<?php endif; ?>