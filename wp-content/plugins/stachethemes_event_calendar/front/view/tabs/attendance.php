<div class="stec-layout-event-inner-attendance-invited">
    <p class="stec-layout-event-title2-fontandcolor">
        <?php _e('You are invited to this event!', 'stec'); ?>
    </p>
    <ul class="stec-layout-event-inner-attendance-invited-buttons">
        <li class="stec-layout-event-inner-button-style stec-layout-event-inner-attendance-invited-buttons-accept stec-layout-event-btn-fontandcolor">
            <p><?php _e('Attend', 'stec'); ?></p>
        </li>
        
        <li class="stec-layout-event-inner-button-style stec-layout-event-inner-attendance-invited-buttons-decline stec-layout-event-btn-fontandcolor">
            <p><?php _e('Decline', 'stec'); ?></p>
        </li>
    </ul>
</div>

<ul class="stec-layout-event-inner-attendance-attendees">
    
    <li class="stec-layout-event-inner-attendance-attendee stec-layout-event-inner-attendance-attendee-template">
        
        <div data-userid="stec_replace_userid" class="stec-layout-event-inner-attendance-attendee-avatar">
            <img src="#stec_replace_avatar" alt="stec_replace_name" />
            <ul>
                stec_replace_status
                <!--<li class=""><i class="fa fa-question"></i></li>-->
                <!--<li class=""><i class="fa fa-times"></i></li>-->
                <!--<li class=""><i class="fa fa-check"></i></li>-->
            </ul>
        </div>
        
        <p class="stec-layout-event-title2-fontandcolor">stec_replace_name</p>
    </li>
</ul>