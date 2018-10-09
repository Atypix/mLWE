<?php

namespace Stachethemes\Stec;
?>
<div class="stachethemes-admin-section-tab" data-tab="author">

    <?php
    $user_info = false;
    $user_info = get_user_by('id', $event->get_author());

    Admin_Html::html_info(__('Created by', 'stec'));
    Admin_Html::html_input('', $user_info ? $user_info->display_name . " (id#$user_info->ID) " : null, 'Anonymous', '', false, "text", 'disabled');

    Admin_Html::html_info(__('User E-Mail', 'stec'));
    Admin_Html::html_input('', $user_info ? $user_info->user_email : $event->contact_email, '', '', false, "text", 'disabled');

    Admin_Html::html_info(__('User Roles', 'stec'));
    Admin_Html::html_input('', $user_info ? implode(',', $user_info->roles) : __('No Roles', 'stec'), '', '', false, "text", 'disabled');

    Admin_Html::html_info(__('Submitted on', 'stec'));
    Admin_Html::html_input('', get_the_date('', $event->get_id()), '', '', false, "text", 'disabled');

    Admin_Html::html_info(__('Note to reviewer', 'stec'));
    Admin_Html::html_textarea('', $event->get_review_note(), "", "", false, 'disabled');
    ?>

</div>
