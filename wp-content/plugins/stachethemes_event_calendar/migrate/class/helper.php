<?php

namespace Stachethemes\Stec\Migrate;




use Stachethemes\Stec\Calendar_Post;
use Stachethemes\Stec\Event_Post;
use Stachethemes\Stec\Event_Meta_Product;
use Stachethemes\Stec\Event_Meta_Schedule;
use Stachethemes\Stec\Event_Meta_Attachment;
use Stachethemes\Stec\Event_Meta_Attendee;
use Stachethemes\Stec\Event_Meta_Guest;




class Helper {



    /**
     * Checks if old sql data is found
     */
    public static function has_old_data() {

        global $wpdb;

        if ( !$wpdb->query("SHOW TABLES LIKE '{$wpdb->prefix}stec_calendars'") ) {
            return false;
        }

        if ( !$wpdb->query("SELECT * FROM {$wpdb->prefix}stec_calendars") ) {
            return false;
        }

        return true;
    }



    /**
     * Retrieves old db calendars
     */
    public static function get_calendars() {

        global $wpdb;

        $calendars = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}stec_calendars");

        return $calendars;
    }



    /**
     * Retrieves old db calendar
     */
    public static function get_calendar($calendar_id) {

        global $wpdb;

        $calendar = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}stec_calendars WHERE id = '{$calendar_id}' ");

        return $calendar[0];
    }



    public static function get_events($calendar_id) {

        global $wpdb;

        $query = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}stec_events WHERE calid = '{$calendar_id}' ");

        if ( !$query ) {
            return array();
        }

        $events = array();

        foreach ( $query as $event ) {
            $events[] = self::get_event($event->id);
        }

        return $events;
    }



    /**
     * Get event by id
     * @return object or FALSE
     */
    public static function get_event($event_id) {

        global $wpdb;

        $event = $wpdb->get_row(""
                . " SELECT event.*, repeater.rrule, repeater.exdate, repeater.is_advanced_rrule, meta.created, meta.review_note, meta.contact_email, meta.recurrence_id, meta.uid "
                . " FROM {$wpdb->prefix}stec_events as event "
                . " LEFT JOIN {$wpdb->prefix}stec_events_repeater as repeater ON  "
                . " event.id = repeater.eventid "
                . " LEFT JOIN {$wpdb->prefix}stec_events_meta as meta ON  "
                . " event.id = meta.eventid "
                . " WHERE event.id = '{$event_id}' ", OBJECT);

        if ( $event === false ) {
            return false;
        }

        if ( empty($event) ) {
            return false;
        }

        // get schedule

        $schedule = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}stec_schedule WHERE eventid='{$event_id}' ", OBJECT);

        if ( $schedule === false ) {
            $schedule = array();
        }

        $event->schedule = $schedule;


        // get guests

        $guests = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}stec_guests WHERE eventid='{$event_id}' ", OBJECT);

        if ( $guests === false ) {
            $guests = array();
        }

        $event->guests = $guests;


        // get attendees

        $attendance = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}stec_attendance WHERE eventid='{$event_id}' AND `repeat_offset` = '0' ", OBJECT);

        if ( $attendance === false ) {
            $attendance = array();
        }

        $event->attendance = $attendance;


        // get attachments

        $attachments = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}stec_attachments WHERE eventid='{$event_id}' ", OBJECT);

        if ( $attachments === false ) {
            $attachments = array();
        }

        $event->attachments = $attachments;



        // get woocommerce

        $wc_products = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}stec_woocommerce WHERE eventid='{$event_id}' ", OBJECT);

        if ( $wc_products === false ) {
            $wc_products = array();
        }

        $event->woocommerce = $wc_products;


        return $event;
    }



    public static function migrate() {

        $calendars = self::get_calendars();

        foreach ( $calendars as $calendar ) {

            $cal_post = new Calendar_Post();

            $cal_post->set_back_visibility($calendar->back_visibility);
            $cal_post->set_visibility($calendar->visibility);
            $cal_post->set_title($calendar->title);
            $cal_post->set_color($calendar->color);
            $cal_post->set_icon($calendar->icon);
            $cal_post->set_writable($calendar->writable);
            $cal_post->set_req_approval($calendar->req_approval);
            $cal_post->set_author($calendar->created_by);
            $cal_post->set_timezone($calendar->timezone);

            $orig_id = $calendar->id;
            $new_id  = $cal_post->insert_post();
            $events  = self::get_events($orig_id);

            if ( $events ) {
                foreach ( $events as $event ) {

                    $event_post = new Event_Post();

                    $event_post->set_title($event->summary);
                    $event_post->set_all_day($event->all_day);
                    $event_post->set_approved($event->approved);
                    $event_post->set_calid($new_id);
                    $event_post->set_visibility($event->visibility);
                    $event_post->set_featured($event->featured);
                    $event_post->set_color($event->color);
                    $event_post->set_icon($event->icon);
                    $event_post->set_comments($event->comments);
                    $event_post->set_counter($event->counter);
                    $event_post->set_description($event->description);
                    $event_post->set_description_short($event->description_short);
                    $event_post->set_end_date($event->end_date);
                    $event_post->set_start_date($event->start_date);
                    $event_post->set_keywords($event->keywords);
                    $event_post->set_location($event->location);
                    $event_post->set_location_details($event->location_details);
                    $event_post->set_location_forecast($event->location_forecast);
                    $event_post->set_location_use_coord($event->location_use_coord);
                    $event_post->set_uid($event->uid);
                    $event_post->set_review_note($event->review_note);
                    $event_post->set_author($event->created_by);
                    $event_post->set_rrule($event->rrule);
                    $event_post->set_is_advanced_rrule($event->is_advanced_rrule);

                    if ( $event->images ) {
                        $images = explode(',', $event->images);
                        $event_post->set_images($images);
                    }

                    if ( $event->recurrence_id ) {
                        $event_post->set_recurrence_id($event->recurrence_id);
                    }

                    if ( $event->exdate ) {
                        $event_post->set_exdate($event->exdate);
                    }

                    if ( isset($event->created_by_cookie) ) {
                        $event_post->set_custom_meta(array(
                                'created_by_cookie' => $event->created_by_cookie
                        ));
                    }

                    // Schedules
                    if ( $event->schedule ) {
                        foreach ( $event->schedule as $schedule ) {

                            $schedule_post             = new Event_Meta_Schedule();
                            $schedule_post->details    = $schedule->description;
                            $schedule_post->start_date = $schedule->start_date;
                            $schedule_post->title      = $schedule->title;
                            $schedule_post->icon       = $schedule->icon;
                            $schedule_post->icon_color = $schedule->icon_color;

                            $event_post->set_schedule($schedule_post);
                        }
                    }

                    // Guests
                    if ( $event->guests ) {
                        foreach ( $event->guests as $guest ) {

                            $guest_post        = new Event_Meta_Guest();
                            $guest_post->name  = $guest->name;
                            $guest_post->photo = $guest->photo;
                            $guest_post->about = $guest->about;

                            $new_links = array();
                            $old_links = explode("||", $guest->links);

                            foreach ( $old_links as $old_link ) {
                                $old_link = explode('::', $old_link);

                                if ( isset($old_link[1]) ) {
                                    $new_links[] = array(
                                            'ico'  => $old_link[0],
                                            'link' => $old_link[1],
                                    );
                                }
                            }

                            $guest_post->links = $new_links;
                            $event_post->set_guest($guest_post);
                        }
                    }

                    // Attendance
                    if ( $event->attendance ) {
                        foreach ( $event->attendance as $attendee ) {

                            $attendee_post = new Event_Meta_Attendee();

                            if ( $attendee->mail_sent == 1 || $attendee->repeat_offset != 0 ) {
                                continue;
                            }

                            $attendee_post->access_token = $attendee->access_token;
                            $attendee_post->email        = $attendee->email;
                            $attendee_post->userid       = $attendee->userid;

                            $event_post->set_attendee($attendee_post);
                        }
                    }

                    // Products
                    if ( $event->woocommerce ) {
                        foreach ( $event->woocommerce as $product ) {

                            $product_post     = new Event_Meta_Product();
                            $product_post->id = $product->product_id;

                            $event_post->set_product($product_post);
                        }
                    }

                    // Attachments
                    if ( $event->attachments ) {
                        foreach ( $event->attachments as $attachment ) {

                            $attachment_post     = new Event_Meta_Attachment();
                            $attachment_post->id = $attachment->attachment;

                            $event_post->set_attachment($attachment_post);
                        }
                    }

                    $event_post->insert_post();
                }
            }
        }

        return true;
    }



    public static function step_migrate($calendar_id, $offset = 0, $new_calendar_id = null) {

        $MAX_STEP    = 10;
        $CSTEP       = 0;
        $next_offset = $offset + $MAX_STEP;
        $result      = new \stdClass();

        $calendar = self::get_calendar($calendar_id);

        if ( !$new_calendar_id ) {
            $cal_post = new Calendar_Post();
            $cal_post->set_back_visibility($calendar->back_visibility);
            $cal_post->set_visibility($calendar->visibility);
            $cal_post->set_title($calendar->title);
            $cal_post->set_color($calendar->color);
            $cal_post->set_icon($calendar->icon);
            $cal_post->set_writable($calendar->writable);
            $cal_post->set_req_approval($calendar->req_approval);
            $cal_post->set_author($calendar->created_by);
            $cal_post->set_timezone($calendar->timezone);
            $orig_id  = $calendar->id;
            $new_id   = $cal_post->insert_post();
        } else {
            $orig_id = $calendar_id;
            $new_id  = $new_calendar_id;
        }

        $events = self::get_events($orig_id);

        $_skip = $offset;

        if ( $events ) {
            foreach ( $events as $event ) {

                if ( $_skip > 0 ) {
                    $_skip--;
                    continue;
                }

                if ( $CSTEP >= $MAX_STEP ) {
                    break;
                }

                self::migrate_event($event, $new_id);

                $CSTEP++;
            }
        }

        $total     = count($events);
        $processed = $CSTEP + $offset;

        $result->completed       = ($processed / $total) * 100;
        $result->next_offset     = $next_offset;
        $result->new_calendar_id = $new_id;

        return $result;
    }



    private static function migrate_event($event, $new_id) {
        $event_post = new Event_Post();
        $event_post->set_title($event->summary);
        $event_post->set_all_day($event->all_day);
        $event_post->set_approved($event->approved);
        $event_post->set_calid($new_id);
        $event_post->set_visibility($event->visibility);
        $event_post->set_featured($event->featured);
        $event_post->set_color($event->color);
        $event_post->set_icon($event->icon);
        $event_post->set_comments($event->comments);
        $event_post->set_counter($event->counter);
        $event_post->set_description($event->description);
        $event_post->set_description_short($event->description_short);
        $event_post->set_end_date($event->end_date);
        $event_post->set_start_date($event->start_date);
        $event_post->set_keywords($event->keywords);
        $event_post->set_location($event->location);
        $event_post->set_location_details($event->location_details);
        $event_post->set_location_forecast($event->location_forecast);
        $event_post->set_location_use_coord($event->location_use_coord);
        $event_post->set_uid($event->uid);
        $event_post->set_review_note($event->review_note);
        $event_post->set_author($event->created_by);
        $event_post->set_rrule($event->rrule);
        $event_post->set_is_advanced_rrule($event->is_advanced_rrule);

        if ( $event->images ) {
            $images = explode(',', $event->images);
            $event_post->set_images($images);
        }

        if ( $event->recurrence_id ) {
            $event_post->set_recurrence_id($event->recurrence_id);
        }

        if ( $event->exdate ) {
            $event_post->set_exdate($event->exdate);
        }

        if ( isset($event->created_by_cookie) ) {
            $event_post->set_custom_meta(array(
                    'created_by_cookie' => $event->created_by_cookie
            ));
        }

        // Schedules
        if ( $event->schedule ) {
            foreach ( $event->schedule as $schedule ) {

                $schedule_post             = new Event_Meta_Schedule();
                $schedule_post->details    = $schedule->description;
                $schedule_post->start_date = $schedule->start_date;
                $schedule_post->title      = $schedule->title;
                $schedule_post->icon       = $schedule->icon;
                $schedule_post->icon_color = $schedule->icon_color;

                $event_post->set_schedule($schedule_post);
            }
        }

        // Guests
        if ( $event->guests ) {
            foreach ( $event->guests as $guest ) {

                $guest_post        = new Event_Meta_Guest();
                $guest_post->name  = $guest->name;
                $guest_post->photo = $guest->photo;
                $guest_post->about = $guest->about;

                $new_links = array();
                $old_links = explode("||", $guest->links);

                foreach ( $old_links as $old_link ) {
                    $old_link = explode('::', $old_link);

                    if ( isset($old_link[1]) ) {
                        $new_links[] = array(
                                'ico'  => $old_link[0],
                                'link' => $old_link[1],
                        );
                    }
                }

                $guest_post->links = $new_links;
                $event_post->set_guest($guest_post);
            }
        }

        // Attendance
        if ( $event->attendance ) {
            foreach ( $event->attendance as $attendee ) {

                $attendee_post = new Event_Meta_Attendee();

                if ( $attendee->mail_sent == 1 || $attendee->repeat_offset != 0 ) {
                    continue;
                }

                $attendee_post->access_token = $attendee->access_token;
                $attendee_post->email        = $attendee->email;
                $attendee_post->userid       = $attendee->userid;

                $event_post->set_attendee($attendee_post);
            }
        }

        // Products
        if ( $event->woocommerce ) {
            foreach ( $event->woocommerce as $product ) {

                $product_post     = new Event_Meta_Product();
                $product_post->id = $product->product_id;

                $event_post->set_product($product_post);
            }
        }

        // Attachments
        if ( $event->attachments ) {
            foreach ( $event->attachments as $attachment ) {

                $attachment_post     = new Event_Meta_Attachment();
                $attachment_post->id = $attachment->attachment;

                $event_post->set_attachment($attachment_post);
            }
        }

        $event_post->insert_post();
    }

}
