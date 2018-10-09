<?php

namespace Stachethemes\Stec;




global $plugin_page;
$stachethemes_ec_main = stachethemes_ec_main::get_instance();



// GET
switch ( Admin_Helper::get("task") ) {

    case 'reset_calendar_session' :
        $_SESSION['stec_admin_calendar_id'] = null;
        break;

    case 'delete' :

        Admin_Helper::verify_nonce('', 'GET');

        $job_id = Admin_Helper::get('id', 0, FILTER_VALIDATE_INT);

        $result = Cron::delete_job($job_id);

        if ( $result ) {
            Admin_Helper::set_message(__('Cronjob deleted', 'stec'));
        } else {
            Admin_Helper::set_message(__('Unable to delete cronjob', 'stec'), 'error');
        }

        break;
}

// POST
switch ( Admin_Helper::post("task") ) {

    case 'import' :

        try {

            $importer = new Import();
            $has_file = !empty($_FILES["ics_filename"]['name']) ? true : false;
            $has_url  = Admin_Helper::post('ics_url', false, FILTER_SANITIZE_URL);

            $ignore_expired   = Admin_Helper::post('ignore_expired', false) !== false ? true : false;
            $overwrite_events = Admin_Helper::post('overwrite_events', false) !== false ? true : false;
            $delete_removed   = Admin_Helper::post('delete_removed', false) !== false ? true : false;
            $calid            = Admin_Helper::post('calendar_id', false, FILTER_VALIDATE_INT);
            $icon             = Admin_Helper::post('icon', false, FILTER_SANITIZE_STRING);

            $importer->set_ignore_expired($ignore_expired);
            $importer->set_overwrite_events($overwrite_events);
            $importer->set_delete_removed($delete_removed);
            $importer->set_calendar_id($calid);
            $importer->set_icon($icon);

            Admin_Helper::verify_nonce("?page={$plugin_page}&calendar_id={$calid}");

            if ( $has_file ) :

                $importer->set_ics_url($_FILES["ics_filename"]["tmp_name"]);

                $result = $importer->import_ics();

                if ( $result !== false && is_object($result) ) {

                    Admin_Helper::set_message(
                            sprintf(__('%d event(s) imported. %d event(s) overwritten. %d event(s) removed.', 'stec'), $result->imported_count, $result->overwritten_count, $result->deleted_count));
                } else {
                    Admin_Helper::set_message(__('Unable to import events', 'stec'), 'error');
                }

            endif;

            if ( $has_url ) {

                $url = Admin_Helper::post('ics_url', false, FILTER_SANITIZE_URL);
                $url = str_replace('webcal://', 'http://', $url);

                $importer->set_ics_url($url);

                $result = $importer->import_ics();

                if ( $result !== false && is_object($result) ) {

                    Admin_Helper::set_message(
                            sprintf(__('%d event(s) imported. %d event(s) overwritten. %d event(s) removed.', 'stec'), $result->imported_count, $result->overwritten_count, $result->deleted_count));
                } else {
                    Admin_Helper::set_message(__('Unable to import events', 'stec'), 'error');
                }
            }
        } catch ( Stec_Exception $ex ) {
            Admin_Helper::set_message($ex->getMessage(), 'error');
        }

        break;

    case 'create_cronjob' :

        $has_url          = Admin_Helper::post('ics_url', false, FILTER_SANITIZE_URL);
        $ignore_expired   = Admin_Helper::post('ignore_expired', false) !== false ? true : false;
        $overwrite_events = Admin_Helper::post('overwrite_events', false) !== false ? true : false;
        $delete_removed   = Admin_Helper::post('delete_removed', false) !== false ? true : false;
        $calid            = Admin_Helper::post('calendar_id', false, FILTER_VALIDATE_INT);
        $icon             = Admin_Helper::post('icon', false, FILTER_SANITIZE_STRING);
        $freq             = Admin_Helper::post('ics_cronjob_freq', false, FILTER_VALIDATE_INT);

        Admin_Helper::verify_nonce("?page={$plugin_page}&calendar_id={$calid}");

        if ( !$has_url ) :
            Admin_Helper::set_message(__('Invalid URL', 'stec'), 'error');
            break;
        endif;

        $url = str_replace('webcal://', 'http://', $has_url);

        $importer = new Import();
        $importer->set_ics_url($url);
        $importer->set_ignore_expired($ignore_expired);
        $importer->set_overwrite_events($overwrite_events);
        $importer->set_delete_removed($delete_removed);
        $importer->set_calendar_id($calid);
        $importer->set_icon($icon);

        $result = Cron::create_import_job($freq, $importer);

        if ( $result ) {
            Admin_Helper::set_message(__('Cronjob created', 'stec'));
        } else {
            Admin_Helper::set_message(__('Unable to create cronjob', 'stec'), 'error');
        }

        break;

    case 'delete_bulk' :

        $calid = Admin_Helper::post('calendar_id', false, FILTER_VALIDATE_INT);

        Admin_Helper::verify_nonce("?page={$plugin_page}&calendar_id={$calid}");

        $jobs = Admin_Helper::post('idlist', false, FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);

        if ( $jobs ) {

            $result = Cron::bulk_delete($jobs);

            if ( $result ) {
                Admin_Helper::set_message(__('Cronjobs deleted', 'stec'));
            } else {
                Admin_Helper::set_message(__('Unable to delete cronjobs', 'stec'), 'error');
            }
        }

        break;
}

// VIEW
switch ( Admin_Helper::get("view") ) :

    case "import":
    default:

        $calendar_id = Admin_Helper::get('calendar_id', false, FILTER_VALIDATE_INT);
        $cronjobs    = array();

        if ( !$calendar_id && isset($_SESSION['stec_admin_calendar_id']) ) {
            // Check if session calendar_id is selected
            $calendar_id = $_SESSION['stec_admin_calendar_id'];
        }

        if ( $calendar_id ) {
            // Check if calendar exists
            $calendar = new Calendar_Post($calendar_id);
            if ( !$calendar->get_id() ) {
                $calendar_id = null;
            }
        }

        if ( !$calendar_id ) {
            $calendars = Calendars::get_admin_calendars();
        } else {
            $calendar = new Calendar_Post($calendar_id);
            $cronjobs = Cron::get_jobs(array(
                            'key'     => 'import',
                            'compare' => 'EXISTS',
                            'type'    => ''
            ));
        }

        $_SESSION['stec_admin_calendar_id'] = $calendar_id;

        include __dir__ . '/import.php';

endswitch;
