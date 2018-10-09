<?php

namespace Stachethemes\Stec;




use DateTime;
use DateTimeZone;




class Import {



    private $ics_url          = false;
    private $calendar_id      = false;
    private $recurrence       = false;
    private $icon             = '';
    private $ignore_expired   = false;
    private $overwrite_events = false;
    private $delete_removed   = false;



    public function get_ics_url() {
        return $this->ics_url;
    }



    public function get_calendar_id() {
        return $this->calendar_id;
    }



    public function get_recurrence() {
        return $this->recurrence;
    }



    public function get_icon() {
        return $this->icon;
    }



    public function get_ignore_expired() {
        return $this->ignore_expired;
    }



    public function get_overwrite_events() {
        return $this->overwrite_events;
    }



    public function get_delete_removed() {
        return $this->delete_removed;
    }



    public function set_ics_url($ics_url) {
        $this->ics_url = $ics_url;
    }



    public function set_calendar_id($calendar_id) {
        $this->calendar_id = $calendar_id;
    }



    public function set_recurrence($recurrence) {
        $this->recurrence = $recurrence;
    }



    public function set_icon($icon) {
        $this->icon = $icon;
    }



    public function set_ignore_expired($ignore_expired) {
        $this->ignore_expired = $ignore_expired;
    }



    public function set_overwrite_events($overwrite_events) {
        $this->overwrite_events = $overwrite_events;
    }



    public function set_delete_removed($delete_removed) {
        $this->delete_removed = $delete_removed;
    }



    public function import_ics() {

        if ( $this->ics_url === false || is_nan($this->calendar_id) ) {
            Admin_Helper::debug_log('Import job no valid source or calendar destination');
            return false;
        }

        Admin_Helper::debug_log('Import job importing ' . $this->ics_url . ' to calendar ' . $this->calendar_id);

        set_time_limit(STEC_EXEC_TIME_LIMIT);

        $calendar = new Calendar_Post($this->calendar_id);

        if ( false === Calendars::user_can_edit_calendar($calendar) ) {
            return false;
        }

        $ical_import = new Ical_Import($this->ics_url, $this->recurrence);
        $events      = $ical_import->events();

        $result            = true;
        $imported_count    = 0;
        $overwritten_count = 0;
        $ics_uids          = array();

        foreach ( $events as $event ) {

            $ics_uids[] = $event->uid;

            if ( !isset($event->summary) || $event->summary == '' ) {
                /**
                 * Don't import unnamed events
                 */
                continue;
            }

            $start_timestamp = $event->dtstart_array[2];
            $end_timestamp   = $event->dtend_array[2];
            $ical_import->setTheDateTz($calendar->get_timezone());

            if ( isset($event->dtstart_array[0]['VALUE']) ) {
                switch ( $event->dtstart_array[0]['VALUE'] ) {
                    case 'DATE' :
                        /**
                         * If VALUE is DATE the timestamp will be zeroed to UTC time (12:00AM) 
                         * It is not adjusted to the timetable timezone
                         * Adjust to timetable timezone
                         */
                        $calendar_date            = new DateTime('now', new DateTimeZone($calendar->get_timezone()));
                        $calendar_datetime_offset = $calendar_date->getOffset();
                        $start_timestamp          -= $calendar_datetime_offset;
                        $end_timestamp            -= $calendar_datetime_offset;
                        break;
                }
            }

            $all_day = 0;
            
            if ( $end_timestamp - $start_timestamp === 86400 ) {
               
                $end_timestamp = $start_timestamp;
                $all_day       = 1;
                
            }
            
            // check expired filter
            if ( $this->ignore_expired === true ) {

                if ( gmdate('U', time()) > $end_timestamp && (!isset($event->rrule) || $event->rrule == '') ) {
                    continue;
                }
            }

            if ( !isset($event->recurrence_id) ) {
                $event->recurrence_id = '';
            }


            // check if uid exists in THIS ID calendar
            // allow recurrence-id override
            $exists = Events::get_events($this->calendar_id, array(
                            array(
                                    'key'     => 'uid',
                                    'value'   => $event->uid,
                                    'compare' => '=',
                                    'type'    => ''
                            ),
                            array(
                                    'key'     => 'recurrence_id',
                                    'value'   => $event->recurrence_id,
                                    'compare' => '=',
                                    'type'    => ''
                            )
            ));

            if ( $exists ) {

                if ( true === $this->overwrite_events ) {

                    foreach ( $exists as $exist ) {
                        $exist->delete_post();
                        $overwritten_count++;
                    }
                    unset($exist);
                } else {
                    // Event uid already exists; Don't add again
                    continue;
                }
            }

            $visibility = 'stec_cal_default';

            if ( isset($event->class) ) :

                if ( $event->class == 'PUBLIC' ) {
                    $visibility = 'stec_public';
                }

                if ( $event->class == 'PRIVATE' ) {
                    $visibility = 'stec_private';
                }

            endif;

            $event_post = new Event_Post();

            $event_post->set_calid($calendar->get_id());
            $event_post->set_title($event->summary);
            $event_post->set_color($calendar->get_color());
            $event_post->set_icon($this->icon);
            $event_post->set_visibility($visibility);
            $event_post->set_start_date($ical_import->theDate('Y-m-d H:i:s', $start_timestamp));
            $event_post->set_end_date($ical_import->theDate('Y-m-d H:i:s', $end_timestamp));
            $event_post->set_all_day($all_day);
            $event_post->set_rrule(isset($event->rrule) ? $event->rrule : '');
            $event_post->set_exdate(isset($event->exdate) ? $event->exdate : '');
            $event_post->set_is_advanced_rrule(isset($event->is_advanced_rrule) ? $event->is_advanced_rrule : 0);
            $event_post->set_approved(1);
            $event_post->set_location(isset($event->location) ? $event->location : '');
            $event_post->set_description(isset($event->description) ? str_replace("\\n", "\n", $event->description) : '');
            $event_post->set_uid($event->uid);
            $event_post->set_recurrence_id($event->recurrence_id);

            $result = $event_post->insert_post();

            if ( $result === false ) {
                return false;
            }

            $imported_count++;
        }

        // Remove events with uid NOT in the ics and in this calendar 
        $deleted_count = 0;
        if ( $this->delete_removed === true ) :

            /**
             * @todo get with meta_query id not in...?
             */
            $calendar_events = Events::get_events($this->calendar_id);

            foreach ( $calendar_events as $event ) {

                if ( in_array($event->get_uid(), $ics_uids) !== true ) {
                    $event->delete_post();
                    $deleted_count++;
                }
            }
        endif;

        Admin_Helper::debug_log('Import job ' . $this->ics_url . ' imported');

        return (object) array(
                        'deleted_count'     => $deleted_count,
                        'overwritten_count' => $overwritten_count,
                        'imported_count'    => $imported_count
        );
    }

}
