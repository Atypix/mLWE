<?php

namespace Stachethemes\Stec;




global $plugin_page;
$stachethemes_ec_main = stachethemes_ec_main::get_instance();



// GET
switch ( Admin_Helper::get("task") ) {

    case 'reset_calendar_session' :
        $_SESSION['stec_admin_calendar_id'] = null;
        break;

    case 'stec_export_to_ics' :
        // handled from INC/inc.task-handler
        // no headers task handler
        break;
}

// POST
switch ( Admin_Helper::post("task") ) {
    
}

// VIEW
switch ( Admin_Helper::get("view") ) :

    case "list" :

        $calendar_id = Admin_Helper::get('calendar_id', false, FILTER_VALIDATE_INT);

        if ( !$calendar_id && isset($_SESSION['stec_admin_calendar_id']) ) {
            // Check if session calendar_id is selected
            $calendar_id = $_SESSION['stec_admin_calendar_id'];
        }

        $calendar = new Calendar_Post($calendar_id);
        $events   = Events::get_front_events($calendar_id);

        $_SESSION['stec_admin_calendar_id'] = $calendar_id;

        include __dir__ . '/list.php';

        break;

    case "export":
    default:

        $calendars = Calendars::get_front_calendars();

        include __dir__ . '/export.php';
endswitch;



