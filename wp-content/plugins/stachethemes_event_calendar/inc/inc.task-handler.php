<?php

namespace Stachethemes\Stec;




if ( !$this instanceof stachethemes_ec_main ) {
    die('File is loaded incorrectly!');
}



// Attendance by URL Listener
$event_id     = filter_input(INPUT_GET, "stec_attendance", FILTER_VALIDATE_INT);
$access_token = filter_input(INPUT_GET, "access_token", FILTER_SANITIZE_STRING);
$status       = filter_input(INPUT_GET, "status", FILTER_VALIDATE_INT);

if ( $event_id && $access_token && $status ) {

    $result = Admin_Helper::update_attendance_by_url($event_id, $access_token, $status);

    if ( $result === true ) {

        switch ( $status ) :

            case 1:
                $this->set_fixed_message(__('You have accepted event invitation', 'stec'), 'success');
                break;

            case 2:
                $this->set_fixed_message(__('You have declined event invitation', 'stec'), 'error');
                break;

        endswitch;
    }

    wp_redirect(site_url('/'));

    exit;
}


// $_GET
switch ( Admin_Helper::get("task") ) {

    case 'stec_export_to_ics' :

        $calendar_id = Admin_Helper::get('calendar_id', null, FILTER_VALIDATE_INT);
        $event_id    = Admin_Helper::get('event_id', null, FILTER_VALIDATE_INT);
        $exporter    = new Export($calendar_id, $event_id);

        try {
            $exporter->export_ics();
        } catch ( Stec_Exception $ex ) {
            if ( is_admin() ) {
                Admin_Helper::set_message($ex->getMessage(), 'error');
            }
        }

        break;

    case 'stec_public_export_to_ics' :

        $calendar_id = Admin_Helper::get('calendar_id', false, FILTER_VALIDATE_INT);
        $event_id    = Admin_Helper::get('event_id', false, FILTER_VALIDATE_INT);

        $exporter = new Export($calendar_id, $event_id);

        try {
            $exporter->export_ics();
        } catch ( Stec_Exception $ex ) {
            
        }

        break;

    case 'stec_delete_calendar' :

        $calendar_id = (int) Admin_Helper::get("calendar_id", false, FILTER_VALIDATE_INT);

        Admin_Helper::verify_nonce('', 'GET');

        try {

            if ( !$calendar_id ) {
                throw new Stec_Exception(__('Calendar has no id', 'stec'));
            }

            $calendar_post = new Calendar_Post($calendar_id);

            if ( !$calendar_post->get_id() ) {
                throw new Stec_Exception(__("Calendar does not exist", 'stec'));
            }

            if ( false === Calendars::user_can_edit_calendar($calendar_post) ) {
                throw new Stec_Exception(__("Unable to delete calendar. Check permissions.", 'stec'));
            }

            $result = $calendar_post->delete_post();

            if ( $result ) {
                Admin_Helper::set_message(__('Calendar deleted', 'stec'));
            } else {
                Admin_Helper::set_message(__('Unable to delete calendar', 'stec'), 'error');
            }
        } catch ( Stec_Exception $ex ) {
            Admin_Helper::set_message($ex->getMessage(), 'error');
        }

        break;
}

// $_POST
switch ( Admin_Helper::post("task") ) {

    case 'stec_public_export_to_ics' :

        $calendar_id = Admin_Helper::post('calendar_id', false, FILTER_VALIDATE_INT);
        $event_id    = Admin_Helper::post('event_id', false, FILTER_VALIDATE_INT);

        $exporter = new Export($calendar_id, $event_id);

        try {
            $exporter->export_ics();
        } catch ( Stec_Exception $ex ) {
            
        }
        
        break;

    case "stec_export_settings" :

        Settings::export_settings();

        exit;
        break;

    case "stec_import_settings" :

        if ( strtolower(pathinfo($_FILES["settings_filename"]["name"], PATHINFO_EXTENSION)) !== 'stec' ) {
            Admin_Helper::set_message(__("File extension is not .stec", 'stec'), 'error');
            break;
        }

        $file   = $_FILES["settings_filename"]["tmp_name"];
        $result = Settings::import_settings($file);

        if ( true === $result ) {
            Admin_Helper::set_message(__("Settings Imported", 'stec'));
        } else {
            Admin_Helper::set_message(__("Error importing settings", 'stec'), 'error');
        }

        break;



    case 'stec_update_calendar' :

        Admin_Helper::verify_nonce("?page=stec_menu__calendars");

        $calendar_id     = Admin_Helper::post('calendar_id', false, FILTER_VALIDATE_INT);
        $title           = Admin_Helper::post('calendar_name', false, FILTER_SANITIZE_STRING);
        $color           = Admin_Helper::post('calendar_color', "#f15e6e", FILTER_SANITIZE_STRING);
        $icon            = Admin_Helper::post('calendar_icon', 'fa', FILTER_SANITIZE_STRING);
        $timezone        = Admin_Helper::post('calendar_timezone', "UTC", FILTER_SANITIZE_STRING);
        $back_visibility = Admin_Helper::post('calendar_back_visibility', "", FILTER_SANITIZE_STRING);
        $visibility      = Admin_Helper::post('calendar_visibility', "", FILTER_SANITIZE_STRING);
        $writable        = Admin_Helper::post('calendar_writable', "", FILTER_SANITIZE_STRING);
        $req_approval    = Admin_Helper::post('calendar_req_approval', false) ? 1 : 0;

        try {

            if ( !$calendar_id ) {
                throw new Stec_Exception(__('Calendar has no id', 'stec'));
            }

            if ( !$title ) {
                throw new Stec_Exception(__('Calendar has no title', 'stec'));
            }

            $calendar_post = new Calendar_Post($calendar_id);

            if ( !$calendar_post->get_id() ) {
                throw new Stec_Exception(__('Calendar does not exist', 'stec'));
            }

            if ( false === Calendars::user_can_edit_calendar($calendar_post) ) {
                throw new Stec_Exception(__("Unable to update calendar. Check permissions.", 'stec'));
            }

            $calendar_post->set_title($title);
            $calendar_post->set_icon($icon);
            $calendar_post->set_color($color);
            $calendar_post->set_timezone($timezone);
            $calendar_post->set_visibility($visibility);
            $calendar_post->set_back_visibility($back_visibility);
            $calendar_post->set_writable($writable);
            $calendar_post->set_req_approval($req_approval);

            $result = $calendar_post->insert_post();

            if ( $result ) {
                Admin_Helper::set_message(__('Calendar updated', 'stec'));
            } else {
                Admin_Helper::set_message(__('Unable to update calendar', 'stec'), 'error');
            }
        } catch ( Stec_Exception $ex ) {

            Admin_Helper::set_message($ex->getMessage(), 'error');
        }

        break;

    case 'stec_create_calendar' :

        Admin_Helper::verify_nonce("?page=stec_menu__calendars");

        $title           = Admin_Helper::post('calendar_name', false, FILTER_SANITIZE_STRING);
        $color           = Admin_Helper::post('calendar_color', "#f15e6e", FILTER_SANITIZE_STRING);
        $icon            = Admin_Helper::post('calendar_icon', 'fa', FILTER_SANITIZE_STRING);
        $timezone        = Admin_Helper::post('calendar_timezone', "UTC", FILTER_SANITIZE_STRING);
        $visibility      = Admin_Helper::post('calendar_visibility', "", FILTER_SANITIZE_STRING);
        $back_visibility = Admin_Helper::post('calendar_back_visibility', "", FILTER_SANITIZE_STRING);
        $writable        = Admin_Helper::post('calendar_writable', "", FILTER_SANITIZE_STRING);
        $req_approval    = Admin_Helper::post('calendar_req_approval', false) ? 1 : 0;

        try {

            if ( !$title ) {
                throw new Stec_Exception(__('Calendar has no title', 'stec'));
            }

            $calendar_post = new Calendar_Post();
            $calendar_post->set_title($title);
            $calendar_post->set_icon($icon);
            $calendar_post->set_color($color);
            $calendar_post->set_timezone($timezone);
            $calendar_post->set_visibility($visibility);
            $calendar_post->set_back_visibility($back_visibility);
            $calendar_post->set_writable($writable);
            $calendar_post->set_req_approval($req_approval);

            $result = $calendar_post->insert_post();

            if ( $result ) {
                Admin_Helper::set_message(__('Calendar created', 'stec'));
            } else {
                Admin_Helper::set_message(__('Unable to create new calendar', 'stec'), 'error');
            }
        } catch ( Stec_Exception $ex ) {

            Admin_Helper::set_message($ex->getMessage(), 'error');
        }

        break;

    case 'stec_delete_bulk_calendars' :

        Admin_Helper::verify_nonce("?page=stec_menu__calendars");

        $calendars = Admin_Helper::post('idlist', false, FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);

        if ( $calendars ) {

            $result = Calendars::bulk_delete($calendars);

            if ( $result ) {
                Admin_Helper::set_message(__('Calendars deleted', 'stec'));
            } else {
                Admin_Helper::set_message(__('Unable to delete calendars', 'stec'), 'error');
            }
        }


        break;
}