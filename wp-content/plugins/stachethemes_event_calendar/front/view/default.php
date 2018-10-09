<?php

namespace Stachethemes\Stec; ?>

<div id="<?php echo $calendar; ?>" class="stec">

    <?php if ( $calendar->get_shortcode_option('stec_menu__general', 'show_top') == '1' ) : ?>

        <?php include("inc.top.php"); ?>

    <?php endif; ?>

    <div class="stec-layout">
        <?php include("layout.agenda.inc.php"); ?>
        <?php include("layout.month.inc.php"); ?>
        <?php include("layout.week.inc.php"); ?>
        <?php include("layout.day.inc.php"); ?>
        <?php include("layout.grid.inc.php"); ?>
    </div>

    <div class="stec-layout-event-preview-reminder-template">
        <ul class="stec-layout-event-preview-reminder">
            <li>
                <input type="email" name="email" value="<?php echo Admin_Helper::get_current_user_email(); ?>" placeholder="<?php _e('E-Mail Address', 'stec'); ?>" />
            </li>
            <li>
                <input type="text" name="number" value="" />
            </li>
            <li class="stec-layout-event-preview-reminder-units-selector">
                <p data-value='hours'><?php _e('hours', 'stec'); ?></p>
                <ul>
                    <li data-value="hours"><?php _e('hours', 'stec'); ?></li>
                    <li data-value="days"><?php _e('days', 'stec'); ?></li>
                    <li data-value="weeks"><?php _e('weeks', 'stec'); ?></li>
                </ul>
            </li>
            <li>
                <button class="stec-layout-event-preview-remind-button"><?php _e('Remind me', 'stec'); ?></button>
            </li>
        </ul>
    </div>

    <div class="stec-tooltip-template">
        <?php include("layout.tooltip.inc.php"); ?>
    </div>

    <div class="stec-event-template">
        <?php include("layout.event.inc.php"); ?>
    </div>

    <div class="stec-event-create-form-template">
        <?php
        if ( Settings::get_admin_setting_value('stec_menu__general', 'show_create_event_form') == '1' ) {

            $is_single_form = false;
            $is_popup       = false;

            include("forms/create.form.inc.php");
        }
        ?>
    </div>

    <div class="stec-event-awaiting-approval-template">
        <?php include("layout.event.aaproval.inc.php"); ?>
    </div>

    <div class="stec-grid-event-template">
        <?php include("layout.grid.event.inc.php"); ?>
    </div>

    <div class="stec-event-inner-template">
        <?php include("layout.event.inner.inc.php"); ?>
    </div>

    <div class="stec-preloader-template">
        <div class="stec-preloader"></div>
    </div>

    <div class="stec-share-template">
        <?php include(__DIR__ . "/popup/share.php"); ?>
    </div>

</div>