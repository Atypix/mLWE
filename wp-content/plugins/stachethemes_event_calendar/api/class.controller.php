<?php

namespace Stachethemes\Stec;




use WP_REST_Request;




class Api_Controller {



    private static $query_cache_time = 300;



    private static function get_query_cache_time() {
        return (int) apply_filters('stec_api_query_cache_time', self::$query_cache_time);
    }



    /**
     * Retrieves calendars or calendar if id is specified with front visibility filter
     * 
     * @param WP_REST_Request $data
     * @return json
     */
    public static function get_calendars(WP_REST_Request $data) {

        $cache = Admin_Helper::get_cache($data);

        if ( false !== $cache ) {
            return $cache;
        }

        $calendars = array();

        if ( $data->get_param('calendar_id') ) {
            $calendar = new Calendar_Post($data->get_param('calendar_id'));

            if ( $calendar->get_id() && Calendars::user_can_view_calendar($calendar) ) {
                $calendars[] = $calendar->get_front_data();
            }
        } else {

            foreach ( Calendars::get_calendars() as $calendar ) {
                if ( Calendars::user_can_view_calendar($calendar) ) {
                    $calendars[] = $calendar->get_front_data();
                }
            }
        }

        $response = new Api_Response();

        $response->set_data(array(
                'total'     => count($calendars),
                'calendars' => $calendars
        ));

        $response_content = $response->get();

        Admin_Helper::set_cache($data, $response_content, self::get_query_cache_time());

        return $response_content;
    }



    /**
     * Retrieves events with front visibility
     * If no range is specified the rrule ocurrencies are not parsed
     * 
     * @param WP_REST_Request $data
     * @return json
     */
    public static function get_events(WP_REST_Request $data) {


        $cache = Admin_Helper::get_cache($data);

        if ( false !== $cache ) {
            return $cache;
        }

        $range_start = $data->get_param('range_start');
        $range_end   = $data->get_param('range_end');
        $calendar_id = $data->get_param('calendar_id');
        $event_id    = $data->get_param('event_id');

        if ( !$calendar_id ) {
            $calendar_id = null;
        }

        if ( !$event_id ) {
            $event_id = null;
        }

        $meta_query = array();

        // Add range filter
        if ( $range_start && $range_end ) {

            $meta_query[] = array(
                    'relation' => 'OR',
                    array(
                            'relation' => 'AND',
                            array(
                                    'key'     => 'rrule',
                                    'value'   => '',
                                    'compare' => '!='
                            ),
                            array(
                                    'key'     => 'start_date',
                                    'value'   => $range_end,
                                    'compare' => '<=',
                                    'type'    => 'DATE'
                            )
                    ),
                    array(
                            'relation' => 'AND',
                            array(
                                    'value'   => '',
                                    'compare' => '='
                            ),
                            array(
                                    'key'     => 'end_date',
                                    'value'   => $range_start,
                                    'compare' => '>=',
                                    'type'    => 'DATE'
                            ),
                            array(
                                    'key'     => 'start_date_unix',
                                    'value'   => $range_end,
                                    'compare' => '<=',
                                    'type'    => 'DATE'
                            ),
                    )
            );
        }

        if ( $event_id ) {

            $query_events = array();
            $the_event    = new Event_Post($event_id);

            if ( $the_event->get_id() && Events::user_can_view_event($the_event) ) {
                $query_events[] = $the_event;
            }
        } else {
            $query_events = Events::get_front_events($calendar_id, $meta_query);
        }

        $events      = array();
        $count       = 0;
        $ocurrencies = false;

        foreach ( $query_events as $event ) {

            if ( $range_start && $range_end ) {
                $ocurrencies = $event->get_ocurrencies($range_start, $range_end);

                if ( !$ocurrencies ) {
                    continue;
                }
            }

            array_push($events, array(
                    'data'        => $event->get_front_data(),
                    'ocurrencies' => $ocurrencies ? $ocurrencies : null
            ));

            if ( $ocurrencies ) {
                $count += count($ocurrencies);
            }

            $count++;
        }

        $response = new Api_Response();

        $response->set_data(array(
                'total'  => $count,
                'range'  => array(
                        'start' => $range_start,
                        'end'   => $range_end,
                ),
                'events' => $events
        ));

        $response_content = $response->get();

        Admin_Helper::set_cache($data, $response_content, self::get_query_cache_time());

        return $response_content;
    }



    /**
     * @todo Unfinished
     * @param WP_REST_Request $data
     * @return html
     */
    public static function get_embed(WP_REST_Request $data) {
    
        if ( Settings::get_admin_setting_value('stec_menu__general', 'allow_embedding') != '1' ) {
            $r = new Api_Response();
            $r->set_error(1);
            $r->set_code(403);
            $r->set_error_message(__('Embedding is disabled', 'stec'));
            return $r->get();
        }

        // Force raw format
        $data->set_param('format', 'stec_raw');
        $r = new Api_Response();

        $event_id      = $data->get_param('event_id');
        $repeat_offset = $data->get_param('repeat_offset');
        $event         = new Event_Post($event_id);
        ob_start();

        $stec = stachethemes_ec_main::get_instance();

        require($stec->get_path('FRONT_VIEW') . '/embed/event.php');

        $r->set_data(ob_get_clean());

        return $r->get();
    }

}
