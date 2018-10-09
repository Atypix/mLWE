<?php

namespace Stachethemes\Stec\Migrate;




use Stachethemes\Stec\Admin_Helper;
use Exception;

header('Content-Type: application/json');

$task = filter_input(INPUT_POST, "task", FILTER_SANITIZE_STRING);

switch ( $task ) :

    case 'stec_ajax_migrate_forget':

        Admin_Helper::verify_nonce("?page=stec__migrate");

        $r = new response_object();

        $r->set_data(array(
                'location' => get_admin_url(null, 'admin.php?page=stec_menu__calendars')
        ));

        $message = __('Database migrated successfully!', 'stec');
        Admin_Helper::set_message($message);

        update_option('stec-db-migrated', 1);

        $r->get();

        break;

    case 'stec_ajax_db_status' :

        Admin_Helper::verify_nonce("?page=stec__migrate");

        $r = new response_object();

        try {

            $calendars = Helper::get_calendars();

            $r->set_data(array(
                    'calendars' => $calendars
            ));

            $r->get();
        } catch ( Exception $ex ) {

            $r->set_error(1);
            $r->set_error_msg($ex->getMessage());
            $r->get();
        }

        break;

    case 'stec_ajax_migrate' :

        Admin_Helper::verify_nonce("?page=stec__migrate");

        $r = new response_object();

        $calendar_id     = Admin_Helper::post('calendar_id', null, FILTER_VALIDATE_INT);
        $offset          = Admin_Helper::post('offset', null, FILTER_VALIDATE_INT);
        $new_calendar_id = Admin_Helper::post('new_calendar_id', null, FILTER_VALIDATE_INT);

        try {

            $result  = $migrate = Helper::step_migrate($calendar_id, $offset, $new_calendar_id);

            $r->set_data(array(
                    'calendar_id'     => $calendar_id,
                    'new_calendar_id' => $result->new_calendar_id,
                    'next_offset'     => $result->next_offset,
                    'completed'       => $result->completed
            ));

            $r->get();
        } catch ( Exception $ex ) {

            $r->set_error(1);
            $r->set_error_msg($ex->getMessage());
            $r->get();
        }

        break;

endswitch;


exit;
