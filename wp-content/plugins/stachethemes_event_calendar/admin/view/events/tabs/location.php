<?php

namespace Stachethemes\Stec;
?>
<div class="stachethemes-admin-section-tab" data-tab="location">

    <?php
    Admin_Html::html_info(__('Where (optional)', 'stec'));
    Admin_Html::html_input('location', $event ? $event->get_location() : null, '', __('Event Location', 'stec'), false);

    Admin_Html::html_info(__('Location additional details (optional)', 'stec'));
    Admin_Html::html_textarea('location_details', $event ? $event->get_location_details() : null, '', __('Location Details', 'stec'), false);

    Admin_Html::html_info(__('Longitude and latitude coordinates in decimal format. Used by Weather service and optionally by Google Maps. (optional)', 'stec'));
    Admin_Html::html_input('location_forecast', $event ? $event->get_location_forecast() : null, '', __('Forecast Location', 'stec'), false);

    Admin_Html::html_checkbox('location_use_coord', $event && $event->get_location_use_coord() ? true : false, false, __('Search location by coordinates instead by address name. Check this box if google maps has problems finding your event location.', 'stec'));
    ?>

</div>