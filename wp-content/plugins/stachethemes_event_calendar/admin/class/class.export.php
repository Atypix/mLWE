<?php

namespace Stachethemes\Stec;




class Export {



    private $calendar_id = null;
    private $event_id    = null;
    private $idlist      = null;



    public function __construct($calendar_id, $event_id = null, $idlist = null) {

        $this->calendar_id = (int) $calendar_id;

        if ( !$this->calendar_id ) {
            throw new Stec_Exception(__('No calendar id provided', 'stec'));
        }

        if ( $event_id ) {
            $this->event_id = (int) $event_id;
        }

        if ( is_array($idlist) ) {
            $this->idlist = $idlist;
        }
    }



    public function export_ics() {

//        if ( !is_user_logged_in() ) {
//            throw new Stec_Exception(__('User not logged-in', 'stec'));
//        }

        $calendar = new Calendar_Post($this->calendar_id);

        if ( !$calendar->get_id() ) {
            throw new Stec_Exception(__('Calendar does not exists', 'stec'));
        }

        $ical_export = new Ical_Export($calendar);
        $events      = array();
        $filename    = false;

        // include event by single id
        if ( $this->event_id ) :

            $event = new Event_Post($this->event_id);

            if ( !$event->get_id() ) {
                throw new Stec_Exception(__('Event does not exists', 'stec'));
            }


            if ( !Events::user_can_view_event($event) ) {
                throw new Stec_Exception(__('You have no permission to view this event', 'stec'));
            }

            $events[] = $event;
            $filename = $event->get_title();

        endif;

        // include selected events from idlist array
        if ( $this->idlist ) :
            foreach ( $this->idlist as $event_id ) {
                $events[] = new Event_Post($event_id);
            }
        endif;
        unset($event_id);

        if ( $this->event_id === null && $this->idlist === null ) {
            // Get all front-visible events from calendar id
            $events = Events::get_front_events($this->calendar_id);
        }

        if ( empty($events) ) {
            Admin_Helper::set_message(__('No events to export', 'stec'), 'error');
            return true;
        }

        foreach ( $events as $event ) :

            if ( false === Events::user_can_view_event($event) ) {
                continue;
            }

            $ical_export->proccess_event($event);
        endforeach;

        $ical_export->download($filename);
    }

}
