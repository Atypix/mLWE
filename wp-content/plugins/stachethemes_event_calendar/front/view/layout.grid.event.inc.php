<?php

namespace Stachethemes\Stec;
?>


<div class="stec-layout-grid-event">

    <a href="#stec_replace_permalink">
        <span class="stec-layout-grid-event-image" stec_replace_image></span>
    </a>

    <div class="stec-layout-grid-invited"><?php _e('Invited', 'stec'); ?></div>

    <div class="stec-layout-grid-event-wrap">

        <div class="stec-layout-grid-icon" stec_replace_event_background><i class="stec_replace_icon_class"></i></div>

        <span class="stec-layout-grid-event-title"><a href="#stec_replace_permalink">stec_replace_summary</a> <?php if ( is_super_admin() ) : ?><a href="<?php echo get_admin_url(); ?>#stec-replace-edit-link"><?php _e('(Edit event)', 'stec'); ?></a><?php endif; ?></span>
        <span class="stec-layout-grid-event-short-desc">stec_replace_short_desc</span>

        <div class="stec-layout-grid-event-status">
            <div class="stec-layout-grid-event-status-expired"><?php _e('Expired', 'stec'); ?></div>
            <div class="stec-layout-grid-event-status-progress"><?php _e('In Progress', 'stec'); ?></div>
        </div>
    </div>

    <div class="stec-layout-grid-event-ul">
        <span class="stec-layout-grid-has-guests">
            <span class="stec-layout-grid-guest" stec_replace_guest_image></span>
            <span class="stec-layout-grid-guest-name">stec_replace_guest_name</span>
        </span>
        <span class="stec-layout-grid-has-products">
            <i class="fa fa-shopping-cart"></i>
            <span>stec_replace_product_name</span>
        </span>
        <span class="stec-layout-grid-has-location">
            <i class="fa fa-map-marker"></i>
            <span>stec_replace_location</span>
        </span>
        <span class="stec-layout-grid-date">
            <i class="fa fa-clock-o"></i>
            <span>stec_replace_date</span>
        </span>
    </div>

</div>