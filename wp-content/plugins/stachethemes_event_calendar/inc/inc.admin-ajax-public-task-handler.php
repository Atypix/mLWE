<?php

namespace Stachethemes\Stec;




if ( !$this instanceof stachethemes_ec_main ) {
    die('File is loaded incorrectly!');
}

header('Content-Type: application/json');

$task = Admin_Helper::post('task');

switch ( $task ) :

    case 'front_delete_event' :

        $event_id = Admin_Helper::post('id', false, FILTER_VALIDATE_INT);

        if ( $event_id === false ) {
            echo json_encode(array(
                    'error' => 1,
                    'text'  => ''
            ));
        }

        $result = Submit_Event::delete_event($event_id);

        if ( $result === false ) {
            echo json_encode(array(
                    'error' => 1,
                    'text'  => __('Error occured', 'stec')
            ));
        } else {

            echo json_encode(array(
                    'error' => 0,
                    'text'  => __('Event deleted', 'stec')
            ));
        }

        break;

    case 'front_create_event' :

        if ( Settings::get_admin_setting_value('stec_menu__general_google_captcha', 'enabled') == '1' ) {

            $secret   = Settings::get_admin_setting_value('stec_menu__general_google_captcha', 'secret_key');
            $response = Admin_Helper::post('g-recaptcha-response');
            $ip       = $_SERVER['REMOTE_ADDR'];

            $is_captcha_valid = Submit_Event::validate_captcha($secret, $response, $ip);

            if ( $is_captcha_valid !== true ) {

                echo json_encode(array(
                        'error' => 1,
                        'text'  => __('Error occured', 'stec')
                ));

                die();
            }
        }

        $the_event = Submit_Event::create_event();

        if ( $the_event !== false ) {

            if ( $the_event->get_approved() == 0 ) {
                // Send email to administrator
                Submit_Event::notify_admin($the_event->get_id());
                
                // Send email to event owner
                Submit_Event::notify_owner($the_event->get_id());
            }

            echo json_encode(array(
                    'error' => 0,
                    'text'  => __('Event created', 'stec'),
                    'event' => (array) $the_event->get_front_data()
            ));
        } else {

            echo json_encode(array(
                    'error' => 1,
                    'text'  => __('Error occured', 'stec')
            ));
        }

        break;

    case 'get_events' :

        $events   = array();
        $cal      = Admin_Helper::post('cal', null, FILTER_SANITIZE_STRING);
        $min_date = Admin_Helper::post('min_date', null, FILTER_SANITIZE_STRING);
        $max_date = Admin_Helper::post('max_date', null, FILTER_SANITIZE_STRING);

        if ( $cal ) {
            $cal = explode(',', $cal);
        }

        $meta_query = array();

        if ( $min_date ) {
            $meta_query[] = array(
                    'key'     => 'start_date',
                    'value'   => $min_date,
                    'compare' => '>=',
                    'type'    => 'DATE'
            );
        }

        if ( $max_date ) {
            $meta_query[] = array(
                    'key'     => 'end_date',
                    'value'   => $max_date,
                    'compare' => '<=',
                    'type'    => 'DATE'
            );
        }

        if ( !is_user_logged_in() ) {
            $meta_query[] = array(
                    'relation' => 'OR',
                    array(
                            'key'     => 'approved',
                            'value'   => '1',
                            'compare' => '='
                    ),
                    array(
                            array(
                                    'key'     => 'approved',
                                    'value'   => '0',
                                    'compare' => '='
                            ),
                            array(
                                    'key'     => 'created_by_cookie',
                                    'value'   => Admin_Helper::get_user_created_by_cookie(),
                                    'compare' => '='
                            )
                    )
            );
        }

        $cache = Admin_Helper::get_cache(array($cal, $meta_query, get_current_user_id()));
        
        if ( $cache ) {
            echo json_encode($cache);
            exit;
        }

        $events = Events::get_front_events($cal, $meta_query);

        foreach ( $events as $key => $event ) {
            $events[$key] = $event->get_front_data();
        }

        Admin_Helper::set_cache(array($cal, $meta_query, get_current_user_id()), $events);

        echo json_encode($events);

        break;

    case 'set_user_event_attendance' :

        $event_id = Admin_Helper::post('event_id', false, FILTER_VALIDATE_INT);

        if ( $event_id === false ) {
            return array();
        }

        if ( !is_user_logged_in() ) {
            return array();
        }

        $status        = Admin_Helper::post('status', null, FILTER_VALIDATE_INT);
        $repeat_offset = Admin_Helper::post('repeat_offset', 0, FILTER_VALIDATE_INT);
        $event_post    = new Event_Post($event_id);

        if ( !$event_post->get_id() ) {
            echo json_encode(array(
                    'status' => 0,
                    'id'     => 0
            ));
            exit;
        }

        $user   = wp_get_current_user();
        $result = $event_post->set_attendee_status($status, array(
                'userid'        => $user->ID,
                'repeat_offset' => $repeat_offset
        ));

        echo json_encode(array(
                'status' => $status
        ));

        break;

    case 'get_weather_data' :

        $location = Admin_Helper::post('location', '');

        $weather_data = Admin_Helper::get_weather_data($location);

        echo $weather_data;

        break;

    case 'add_reminder' :

        $event_id      = Admin_Helper::post('event_id', false, FILTER_VALIDATE_INT);
        $repeat_offset = Admin_Helper::post('repeat_offset', 0, FILTER_VALIDATE_INT);
        $date          = Admin_Helper::post('date', false, FILTER_SANITIZE_STRING);
        $email         = Admin_Helper::post('email', false, FILTER_VALIDATE_EMAIL);

        $reminder = Admin_Helper::set_reminder($event_id, $repeat_offset, $email, $date);

        echo json_encode($reminder);

        break;

endswitch;

exit;
