<?php

namespace Stachethemes\Stec;




global $plugin_page;

// GET
switch ( Admin_Helper::get("task") ) {

    case 'reset_calendar_session' :
        $_SESSION['stec_admin_calendar_id'] = null;
        break;

    case 'duplicate' :

        Admin_Helper::verify_nonce('', 'GET');

        $event_id = Admin_Helper::get('event_id', 0, FILTER_VALIDATE_INT);

        $result = Events::duplicate_event($event_id);

        if ( $result ) {
            Admin_Helper::set_message(__('Event duplicated', 'stec'));
        } else {
            Admin_Helper::set_message(__('Unable to duplicate event', 'stec'), 'error');
        }

        break;

    case 'approve' :

        Admin_Helper::verify_nonce('', 'GET');

        $event_id = Admin_Helper::get('event_id', 0, FILTER_VALIDATE_INT);

        $result = Events::approve_event($event_id);

        if ( $result ) {
            Admin_Helper::set_message(__('Event approved', 'stec'));
        } else {
            Admin_Helper::set_message(__('Unable to approve event', 'stec'), 'error');
        }

        break;

    case 'delete' :

        Admin_Helper::verify_nonce('', 'GET');

        $event_id   = Admin_Helper::get('event_id', 0, FILTER_VALIDATE_INT);
        $event_post = new Event_Post($event_id);
        $result     = $event_post->delete_post();

        if ( $result ) {
            Admin_Helper::set_message(__('Event deleted', 'stec'));
        } else {
            Admin_Helper::set_message(__('Unable to delete event', 'stec'), 'error');
        }

        break;
}

// POST
switch ( Admin_Helper::post("task") ) {

    case 'update' :
    case 'create' :

        $event_id    = Admin_Helper::post('event_id', null);
        $calendar_id = Admin_Helper::post('calendar_id');
        Admin_Helper::verify_nonce("?page={$plugin_page}&calendar_id={$calendar_id}");

        $event_post = new Event_Post($event_id);
        $calendar   = new Calendar_Post($calendar_id);
        $calid      = Admin_Helper::post('calid', false, FILTER_VALIDATE_INT);
        $event_post->set_calid($calid ? $calid : $calendar_id);
        $event_post->set_title(Admin_Helper::post('summary'));
        $event_post->set_slug(Admin_Helper::post('alias'));
        $event_post->set_color(Admin_Helper::post('event_color'));
        $event_post->set_icon(Admin_Helper::post('icon'));
        $event_post->set_visibility(Admin_Helper::post('visibility'));
        $event_post->set_back_visibility(Admin_Helper::post('back_visibility'));
        $event_post->set_featured(Admin_Helper::post('featured'));

        $start_date         = Admin_Helper::post('start_date');
        $start_time_hours   = Admin_Helper::post('start_time_hours');
        $start_time_minutes = Admin_Helper::post('start_time_minutes');
        $end_date           = Admin_Helper::post('end_date');
        $end_time_hours     = Admin_Helper::post('end_time_hours');
        $end_time_minutes   = Admin_Helper::post('end_time_minutes');
        $start_date_time    = $start_date . ' ' . $start_time_hours . ':' . $start_time_minutes . ':00';
        $end_date_time      = $end_date . ' ' . $end_time_hours . ':' . $end_time_minutes . ':00';

        $event_post->set_start_date($start_date_time);
        $event_post->set_end_date($end_date_time);
        $event_post->set_all_day(Admin_Helper::post('all_day', false) !== false ? 1 : 0);
        $event_post->set_keywords(Admin_Helper::post('keywords'));
        $event_post->set_rrule(Admin_Helper::post('rrule', ''));
        $event_post->set_exdate(Admin_Helper::post('exdate', ''));
        $event_post->set_is_advanced_rrule(Admin_Helper::post('is_advanced_rrule', 0));
        $event_post->set_counter(Admin_Helper::post('counter'));
        $event_post->set_comments(Admin_Helper::post('comments'));
        $event_post->set_link(Admin_Helper::post('link'));
        $event_post->set_approved(Admin_Helper::post('approved', 0));
        $event_post->set_location(Admin_Helper::post('location', ''));
        $event_post->set_location_details(Admin_Helper::post('location_details', ''));
        $event_post->set_location_forecast(Admin_Helper::post('location_forecast', ''));
        $event_post->set_location_use_coord(Admin_Helper::post('location_use_coord', false) !== false ? 1 : 0);
        $event_post->set_images(Admin_Helper::post('images', false, FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY));
        $event_post->set_description(Admin_Helper::post('description'));
        $event_post->set_description_short(Admin_Helper::post('description_short'));

        if ( $event_post->get_id() ) {
            // Empties old data
            $event_post->set_schedules(array());
            $event_post->set_guests(array());
            $event_post->set_attachments(array());
            $event_post->set_attendees(array());
            $event_post->set_products(array());
        }

        $schedules = Admin_Helper::post('schedule', false, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        if ( $schedules ) {
            foreach ( $schedules as $schedule ) {

                $schedule_object = new Event_Meta_Schedule();

                $schedule_object->start_date = $schedule['schedule_date_from'] . ' ' . $schedule['schedule_time_hours_from'] . ':' . $schedule['schedule_time_minutes_from'] . ':00';
                $schedule_object->title      = $schedule['schedule_title'];
                $schedule_object->icon       = $schedule['schedule_icon'];
                $schedule_object->icon_color = $schedule['schedule_icon_color'];
                $schedule_object->details    = $schedule['schedule_details'];

                $event_post->set_schedule($schedule_object);
            }
        }

        $guests = Admin_Helper::post('guests', false, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if ( $guests ) {
            foreach ( $guests as $guest ) {

                if ( !isset($guest['name']) || $guest['name'] == '' ) {
                    continue;
                }

                $social = array();

                foreach ( $guest['social'] as $soc ) {
                    if ( trim($soc['link']) != '' ) {
                        $social[] = $soc;
                    }
                }

                $guest_object        = new Event_Meta_Guest();
                $guest_object->name  = $guest['name'];
                $guest_object->photo = $guest['photo'];
                $guest_object->links = $social;
                $guest_object->about = $guest['about'];
                $event_post->set_guest($guest_object);
            }
        }

        $attendees = Admin_Helper::post('attendee', false, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        if ( $attendees ) {

            foreach ( $attendees as $attendee ) {

                $attendee_object = new Event_Meta_Attendee();

                if ( isset($attendee['email']) && false !== filter_var($attendee['email'], FILTER_VALIDATE_EMAIL) ) {
                    $attendee_object->email = $attendee['email'];
                } else if ( isset($attendee['userid']) && $attendee['userid'] > 0 ) {
                    $attendee_object->userid = (int) $attendee['userid'];
                } else {
                    continue;
                }

                if ( isset($attendee['access_token']) ) {
                    $attendee_object->access_token = $attendee['access_token'];
                }

                if ( isset($attendee['mail_sent']) ) {
                    $attendee_object->mail_sent = $attendee['mail_sent'];
                }

                if ( isset($attendee['status']) && is_array($attendee['status']) ) {
                    $attendee_object->status = $attendee['status'];
                }

                $event_post->set_attendee($attendee_object);
            }
        }

        $attachments = Admin_Helper::post('attachment', false, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if ( $attachments ) {
            foreach ( $attachments as $attachment ) {
                $attachment_object     = new Event_Meta_Attachment();
                $attachment_object->id = $attachment['id'];
                $event_post->set_attachment($attachment_object);
            }
        }

        $products = Admin_Helper::post('wc_product', false, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if ( $products ) {
            foreach ( $products as $id ) {

                $product_object     = new Event_Meta_Product();
                $product_object->id = $id;
                $event_post->set_product($product_object);
            }
        }

        try {
            $result = $event_post->insert_post();


            if ( $result ) {

                Admin_Helper::set_message(sprintf(_x('Event %s', 'created/updated', 'stec'), $event_post->get_id() ? __('updated', 'stec') : __('created', 'stec')));
            } else {

                Admin_Helper::set_message(sprintf(_x('Error %s event', 'creating/updating', 'stec'), $event_post->get_id() ? __('updating', 'stec') : __('creating', 'stec')), 'error');
            }
        } catch ( Stec_Exception $ex ) {
            Admin_Helper::set_message($ex->getMessage(), 'error');
        }

        break;

    case 'delete_bulk' :

        $calendar_id = Admin_Helper::post('calendar_id');

        Admin_Helper::verify_nonce("?page={$plugin_page}&calendar_id={$calendar_id}");

        $events = Admin_Helper::post('idlist', false, FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);

        if ( $events ) {

            $result = Events::bulk_delete($events);

            if ( $result ) {
                Admin_Helper::set_message(__('Events deleted', 'stec'));
            } else {
                Admin_Helper::set_message(__('Unable to delete events', 'stec'), 'error');
            }
        }

        break;
}

// VIEW
switch ( Admin_Helper::get("view") ) :

    case "edit" :

        $event_id    = Admin_Helper::get('event_id');
        $event       = new Event_Post($event_id);
        $calendar_id = $event->get_calid();
        $calendar    = new Calendar_Post($calendar_id);
        $cal_array   = Calendars::get_calendars_list();
        $users       = get_users();

        include __dir__ . '/addedit.php';

        break;

    case "add" :

        $event_id    = false;
        $event       = false;
        $calendar_id = Admin_Helper::get('calendar_id');
        $calendar    = new Calendar_Post($calendar_id);
        $users       = get_users(array('fields' => array('display_name', 'ID')));

        include __dir__ . '/addedit.php';

        break;

    case "list":
    default:

        $calendar_id = Admin_Helper::get('calendar_id');

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
            $events   = Events::get_admin_events($calendar_id);
        }

        $_SESSION['stec_admin_calendar_id'] = $calendar_id;

        include __dir__ . '/list.php';
endswitch;



