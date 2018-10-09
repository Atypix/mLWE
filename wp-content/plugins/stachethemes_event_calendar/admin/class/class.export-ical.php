<?php

namespace Stachethemes\Stec;




use DateTime;
use DateTimeZone;




/**
 * Very basic ics generator
 * Converts calendar event objects to ics
 */
class Ical_Export {



    private $eol;
    private $events_content;
    private $calendar;



    public function __construct($calendar) {

        if ( !$calendar instanceof Calendar_Post ) {
            throw new Stec_Exception(__('Invalid calendar object instance', 'stec'));
        }

        $this->calendar       = $calendar;
        $this->events_content = '';
        $this->eol            = "\n";
    }



    public function date_to_cal($date_string) {

        $date = new DateTime(($date_string), new DateTimeZone($this->calendar->get_timezone()));

        return $date->format('Ymd\THis');
    }



    public function escape_string($string) {
        return preg_replace('/([\,;])/', '', $string);
    }



    private function uid($event) {
        return $event->get_uid() ? $event->get_uid() : uniqid('e-') . '_' . md5(microtime()) . '@stachethemes_ec.com';
    }



    public function proccess_event($event) {

        if ( !$event instanceof Event_Post ) {
            throw new Stec_Exception(__('Invalid event object instance', 'stec'));
        }
        
        if ( $event->get_all_day() == '1' ) {
            $date = new DateTime(($event->get_end_date()), new DateTimeZone($this->calendar->get_timezone()));
            $date->setTime(24, 0, 0);
            $event->set_end_date($date->format('Y-m-d H:i:s'));
        }

        // multiline fix
        $event->set_description(preg_replace('/\r\n|\n\r|\r|\n/', '\n', $event->get_description()));

        $content = '';
        $content .= 'BEGIN:VEVENT' . $this->eol;
        $content .= 'DTEND:' . $this->date_to_cal($event->get_end_date()) . $this->eol;
        if ( $event->get_rrule() ) {
            $content .= 'RRULE:' . $event->get_rrule() . $this->eol;
        }
        $content .= 'UID:' . $this->uid($event) . $this->eol;
        $content .= 'DTSTAMP:' . $this->date_to_cal('now') . $this->eol;
        $content .= 'LOCATION:' . $this->escape_string($event->get_location()) . $this->eol;
        $content .= 'DESCRIPTION:' . $this->escape_string($event->get_description()) . $this->eol;
        $content .= 'SUMMARY:' . $this->escape_string($event->get_title()) . $this->eol;
        $content .= 'DTSTART:' . $this->date_to_cal($event->get_start_date()) . $this->eol;

        if ( $event->get_exdate() ) {
            $exdates = explode(',', $event->get_exdate());
            foreach ( $exdates as $exdate ) {
                $content .= 'EXDATE;VALUE=DATE:' . $exdate . $this->eol;
            }
        }

        if ( $event->get_recurrence_id() ) {

            // If is UTC do not include timezone
            if ( strpos($event->get_recurrence_id(), 'Z') !== false ) {
                $content .= 'RECURRENCE-ID:' . $event->get_recurrence_id() . $this->eol;
            } else {
                $content .= 'RECURRENCE-ID;TZID=' . $this->calendar->get_timezone() . ':' . $event->get_recurrence_id() . $this->eol;
            }
        }

        $content .= 'END:VEVENT' . $this->eol;

        $this->events_content .= $content;

        return true;
    }



    public function get_content() {

        $content = '';

        $content .= 'BEGIN:VCALENDAR' . $this->eol;
        $content .= 'VERSION:2.0' . $this->eol;
        $content .= 'X-WR-TIMEZONE:' . $this->calendar->get_timezone() . $this->eol;
        $content .= $this->events_content;
        $content .= 'END:VCALENDAR';

        return $content;
    }



    public function download($filename = false) {

        $title = $filename ? $filename : $this->calendar->get_title() . ' ' . date('y-m-d His');

        header('Content-type: text/calendar; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"" . $title . ".ics\"");

        echo $this->get_content();

        exit();
    }

}
