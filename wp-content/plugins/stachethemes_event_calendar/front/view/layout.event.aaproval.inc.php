<?php

namespace Stachethemes\Stec;
?>

<li class="stec-layout-event stec-layout-event-awaiting-approval">

    <div class="stec-layout-event-preview">

        <div class="stec-layout-event-preview-left">

            <div class="stec-layout-event-preview-left-text">

                <p class="stec-layout-event-preview-left-text-title">stec_replace_summary</p>
                <p class="stec-layout-event-preview-left-text-sub"><?php _e('Awaiting approval', 'stec'); ?></p>

                <a href="javascript:void(0);" class="stec-layout-event-preview-left-approval-cancel">
                    <?php _e('Cancel', 'stec'); ?>
                </a>

            </div>

        </div>

        <div class="stec-layout-event-preview-right">
            <i class="fa fa-check"></i>
            <i class="fa fa-times"></i>
            <div class="stec-layout-event-awaiting-approval-cancel stec-layout-event-btn-fontandcolor stec-layout-event-inner-button-style"><?php _e('Cancel', 'stec'); ?></div>
        </div>

    </div>

</li>
