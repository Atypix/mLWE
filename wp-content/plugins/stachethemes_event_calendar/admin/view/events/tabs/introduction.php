<?php

namespace Stachethemes\Stec;
?>
<div class="stachethemes-admin-section-tab" data-tab="introduction">

    <?php
    Admin_Html::html_info(__('Images (optional)', 'stec'));
    Admin_Html::html_add_image('images[]', $event ? $event->get_images() : false, __('Add Images', 'stec'), false);

    Admin_Html::html_info(__('Introduction', 'stec'));
    wp_editor($event ? $event->get_description() : null, 'description', array(
            'editor_height' => 250
    ));

    Admin_Html::html_info(__('Short Description ', 'stec'));
    Admin_Html::html_textarea('description_short', $event ? $event->get_description_short() : null, '', __('Short info about the event', 'stec'), false);

    Admin_Html::html_info(__('External Link', 'stec'));
    Admin_Html::html_input('link', $event ? $event->get_link() : null, '', __('External Link', 'stec'), false);
    ?>

</div>