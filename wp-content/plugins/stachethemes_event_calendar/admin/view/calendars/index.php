<?php

namespace Stachethemes\Stec;




global $plugin_page;
$stachethemes_ec_main = stachethemes_ec_main::get_instance();


// Delete, Update and Create are handled by inc.task-handler.php

// GET
switch ( Admin_Helper::get("task") ) {
}

// POST
switch ( Admin_Helper::post("task") ) {
}

// VIEW
switch ( Admin_Helper::get("view") ) :

    case "edit":

        $calendar_id = (int) Admin_Helper::get("calendar_id", false, FILTER_VALIDATE_INT);

        if ( !$calendar_id ) {
            Admin_Helper::set_message(__('Calendar has no id', 'stec'), 'error');
        } else {
            $calendar = new Calendar_Post($calendar_id);
        }
        
        include __dir__ . '/edit.php';

        break;

    case "list":
    default:

        $calendars = Calendars::get_admin_calendars();

        include __dir__ . '/list.php';
endswitch;



