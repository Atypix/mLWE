<?php

namespace Stachethemes\Stec;




use WP_REST_Server;




class Api_Routes {



    private $api_namespace;
    private $controller = '\Stachethemes\Stec\Api_Controller';



    public function __construct($api_namespace) {

        $this->api_namespace = $api_namespace;

        switch ( $this->api_namespace ) :

            case 'stec/v2':
            default:
                $this->set_routes();
        endswitch;
    }



    public function set_routes() {

        $range_regex = '([0-9]{4,4})-([0-9]{1,2})-([0-9]{1,2})|(\d{10,})';

        /**
         * Get list of all calendars
         * 
         * get / calendars / 
         */
        register_rest_route($this->api_namespace, '/get/calendars/', array(
                'methods'  => WP_REST_Server::READABLE,
                'callback' => array($this->controller, 'get_calendars'),
        ));

        /**
         * Get calendar by id
         * 
         * get / calendars / id
         */
        register_rest_route($this->api_namespace, '/get/calendars/(?P<calendar_id>\d+)', array(
                'methods'  => WP_REST_Server::READABLE,
                'callback' => array($this->controller, 'get_calendars'),
        ));


        /**
         * Get calendar events
         * 
         * get / calendars / id / events
         */
        register_rest_route($this->api_namespace, '/get/calendars/(?P<calendar_id>\d+)/events', array(
                'methods'  => WP_REST_Server::READABLE,
                'callback' => array($this->controller, 'get_events'),
        ));



        /**
         * Get calendar events within range
         * 
         * get / calendars / id / events / range_start / range_end
         */
        register_rest_route($this->api_namespace, "/get/calendars/(?P<calendar_id>\d+)/events/(?P<range_start>$range_regex)/(?P<range_end>$range_regex)", array(
                'methods'  => WP_REST_Server::READABLE,
                'callback' => array($this->controller, 'get_events'),
        ));



        /**
         * Get get all events from all calendars
         * 
         * get / events / 
         */
        register_rest_route($this->api_namespace, '/get/events/', array(
                'methods'  => WP_REST_Server::READABLE,
                'callback' => array($this->controller, 'get_events'),
        ));

        /**
         * Get events within range
         * 
         * get / events / range_start / range_end
         */
        register_rest_route($this->api_namespace, "/get/events/(?P<range_start>$range_regex)/(?P<range_end>$range_regex)", array(
                'methods'  => WP_REST_Server::READABLE,
                'callback' => array($this->controller, 'get_events'),
        ));

        /**
         * Get event by id
         * 
         * get / event / id
         */
        register_rest_route($this->api_namespace, '/get/event/(?P<event_id>\d+)', array(
                'methods'  => WP_REST_Server::READABLE,
                'callback' => array($this->controller, 'get_events'),
        ));

        /**
         * Get embed event by id
         * 
         * get / embed / event id
         */
        register_rest_route($this->api_namespace, '/get/embed/(?P<event_id>\d+)/', array(
                'methods'  => WP_REST_Server::READABLE,
                'callback' => array($this->controller, 'get_embed'),
        ));

        /**
         * Get embed event by id
         * 
         * get / embed / event id
         */
        register_rest_route($this->api_namespace, '/get/embed/(?P<event_id>\d+)/(?P<repeat_offset>\d+)', array(
                'methods'  => WP_REST_Server::READABLE,
                'callback' => array($this->controller, 'get_embed'),
        ));


        do_action('stec_api_add_route', $this->api_namespace);
    }

}
