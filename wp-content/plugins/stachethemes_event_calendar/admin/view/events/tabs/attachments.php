<?php

namespace Stachethemes\Stec;
?>
<div class="stachethemes-admin-section-tab" data-tab="attachments">

    <?php Admin_Html::html_info(__('Attachments List', 'stec')); ?>

    <?php if ( $event ) : foreach ( $event->get_attachments() as $attachment ) : ?>

            <?php $filename = basename(get_attached_file($attachment->id, true)); ?>

            <div class="stachethemes-admin-attachments-attachment">

                <div class="stachethemes-admin-attachments-attachment-head">
                    <p class="stachethemes-admin-attachments-attachment-title"><span><?php echo $filename; ?></span></p>

                    <div>
                        <?php
                        Admin_Html::html_button(__('Delete', 'stec'), '', true, "light-btn delete");
                        ?>
                    </div>
                </div>

            </div>

            <?php
        endforeach;
    endif;
    ?>

    <div class="stachethemes-admin-attachments-attachment stachethemes-admin-attachments-attachment-template">

        <div class="stachethemes-admin-attachments-attachment-head">
            <p class="stachethemes-admin-attachments-attachment-title"><span>%title%</span></p>

            <div>
                <?php
                Admin_Html::html_button(__('Delete', 'stec'), '', true, "light-btn delete");
                ?>
            </div>
        </div>

        <?php
        Admin_Html::html_hidden('attachment[0][id]', '%id%');
        ?>
    </div>

    <?php
    Admin_Html::html_button(__('Add Attachment', 'stec'), false, false, 'add-attachments-attachment');
    ?>

</div>
