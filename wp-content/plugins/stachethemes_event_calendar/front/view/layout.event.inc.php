<?php

namespace Stachethemes\Stec;
?>

<li class="stec-layout-event">

    <div class="stec-layout-event-preview">

        <div class="stec-layout-event-preview-left">

            <div class="stec-layout-event-preview-left-icon" stec_replace_event_background>
                <i class="stec_replace_icon_class"></i>
            </div>

            <div class="stec-layout-event-preview-left-text">
                <p class="stec-layout-event-preview-left-text-featured">
                    <i class="fa fa-star"></i>
                    <span><?php _e('Featured', 'stec'); ?></span>
                </p>
                <p class="stec-layout-event-preview-left-text-title">stec_replace_summary <?php if ( is_super_admin() ) : ?><a href="<?php echo get_admin_url(); ?>#stec-replace-edit-link"><?php _e('(Edit event)', 'stec'); ?></a><?php endif; ?></p>
                <p class="stec-layout-event-preview-left-text-date">stec_replace_date</p>

                <?php if ( $calendar->get_shortcode_option('stec_menu__general', 'reminder') == '1' ) : ?>
                    <a href="javascript:void(0);" class="stec-layout-event-preview-left-reminder-toggle">
                        <?php _e('Reminder', 'stec'); ?>
                    </a>
                <?php endif; ?>

            </div>

        </div>

        <div class="stec-layout-event-preview-right">

            <?php if ( $calendar->get_shortcode_option('stec_menu__general', 'reminder') == '1' ) : ?>

                <div class="stec-layout-event-preview-right-menu">
                    <i class="fa fa-bell"></i>
                </div>

            <?php endif; ?>

            <div class="stec-layout-event-preview-right-event-toggle">
                <i class="fa fa-plus"></i>
                <i class="fa fa-minus"></i>
            </div>

        </div>

    </div>

    <?php
    // event.inner.inc.php is added dynamically by js
    // template included in default.php
    // include("layout.event.inner.inc.php");
    ?>
</li>
