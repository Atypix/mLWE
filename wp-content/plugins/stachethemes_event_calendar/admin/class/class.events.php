<?php

namespace Stachethemes\Stec;




class Events {



    /**
     * Get events editable by current logged admin
     */
    public static function get_admin_events($calendar_id = null, $custom_meta_query = null) {
        $events = self::get_events($calendar_id, $custom_meta_query);
        foreach ( $events as $k => $event ) {
            if ( false === self::user_can_edit_event($event) ) {
                unset($events[$k]);
            }
        }
        return array_values($events);
    }



    /**
     * Get events visible on the front-end by the current visitor
     */
    public static function get_front_events($calendar_id = null, $custom_meta_query = null) {
        $events = self::get_events($calendar_id, $custom_meta_query);
        foreach ( $events as $k => $event ) {
            if ( false === self::user_can_view_event($event) ) {
                unset($events[$k]);
            }
        }


        return apply_filters('stec_front_get_events', array_values($events));
    }



    public static function get_events($calendar_id = null, $custom_meta_query = null) {

        $events     = array();
        $meta_query = array();

        if ( $calendar_id ) {
            $meta_query[] = array(
                    'key'     => 'calid',
                    'value'   => $calendar_id,
                    'compare' => is_array($calendar_id) ? 'IN' : '=',
            );
        }

        if ( $custom_meta_query ) {
            $meta_query[] = $custom_meta_query;
        }

        $event_ids = get_posts(array(
                'posts_per_page' => -1,
                'post_type'      => 'stec_event',
                'fields'         => 'ids',
                'meta_query'     => $meta_query,
                'meta_key'       => 'start_date',
                'orderby'        => 'meta_value',
                'order'          => 'DESC',
        ));

        if ( !$event_ids ) {
            return array();
        }

        foreach ( $event_ids as $id ) {

            $event_post = new Event_Post($id);

            $events[] = $event_post;
        }

        return apply_filters('stec_admin_get_events', $events);
    }



    public static function get_aaproval_count($calendar_id = null) {

        $meta_query = array(
                array(
                        'key'     => 'approved',
                        'value'   => '0',
                        'compare' => '=',
                        'type'    => ''
                )
        );

        if ( $calendar_id ) {
            $meta_query[0][] = array(
                    'key'     => 'calid',
                    'value'   => $calendar_id,
                    'compare' => '=',
                    'type'    => ''
            );
        }


        $event_ids = get_posts(array(
                'posts_per_page' => -1,
                'post_type'      => 'stec_event',
                'fields'         => 'ids',
                'meta_query'     => $meta_query
        ));

        return count($event_ids);
    }



    public static function bulk_delete($event_ids = array()) {
        $filter_event_ids = filter_var_array($event_ids, FILTER_VALIDATE_INT);
        $unique_event_ids = array_filter($filter_event_ids);

        foreach ( $unique_event_ids as $event_id ) {
            $event = new Event_Post($event_id);
            if ( !$event->get_id() ) {
                continue;
            }

            $event->delete_post();
        }
        unset($event_id);

        /**
         * @todo Delete calendar events data as well
         */
        return true;
    }



    /**
     * Inserts or Updates post
     */
    public static function insert_post(Event_Post $event) {


        $post_data = apply_filters('stec_insert_post', $event->get_post_data());

        if ( $event->get_id() ) {
            $result = wp_update_post($post_data, true);
        } else {
            $result = wp_insert_post($post_data, true);
        }


        if ( is_wp_error($result) ) {
            $errors = $result->get_error_messages();
            $msg    = implode('<br>', $errors);
            throw new Stec_Exception($msg);
        }

        if ( !$result ) {
            throw new Stec_Exception(__('Error processing post', 'stec'));
        }

        return $result;
    }



    public static function duplicate_event($event_id) {

        $event_post = new Event_Post($event_id);

        if ( $event_post->get_id() ) {
            $event_post->set_id(null);
            $event_post->set_title($event_post->get_title() . ' (' . __('DUPLICATE', 'stec') . ')');
            return $event_post->insert_post();
        } else {
            throw new Stec_Exception(sprintf(__('Event with id %s not found', 'stec'), $event_id));
        }
    }



    public static function approve_event($event_id) {

        $event         = new Event_Post($event_id);
        $contact_email = $event->get_custom_meta('contact_email');

        if ( !$event->get_id() || false === filter_var($contact_email, FILTER_VALIDATE_EMAIL) ) {
            return false;
        }

        $event->set_approved(1);
        $event->insert_post();

        // Send notification to the user that their event have been approved
        $to      = $contact_email;
        $subject = apply_filters('stec_event_approved_subject', __('Your event has been approved', 'stec'));
        $content = __('Congratulations! Your event has been approved!', 'stec');

        // Permalink
        $single_page_id = get_option('stec-single-page-id', false);
        if ( $single_page_id ) {
            $single_page     = get_post($single_page_id);
            $event_permalink = site_url() . '/' . $single_page->post_name . '/' . $event->alias;
            $content         .= PHP_EOL;
            $content         .= sprintf(__("You can view your event at %s", 'stec'), $event_permalink);
        }

        $content = apply_filters('stec_event_approved_content', $content);

        wp_mail($to, $subject, $content);

        return true;
    }



    // Checks if user can view the event on the front-end
    public static function user_can_view_event(Event_Post $event_post) {

        if ( is_super_admin() ) {
            return true;
        }

        $visibility = $event_post->get_visibility();

        if ( 'stec_cal_default' === $visibility ) {

            $calendar_post = new Calendar_Post($event_post->get_calid());

            return Calendars::user_can_view_calendar($calendar_post);
        } elseif ( 'stec_private' === $visibility ) {
            if ( $event_post->get_author() === get_current_user_id() ) {
                return true;
            }
        } else {
            $user_roles = Admin_Helper::get_user_roles();
            if ( in_array($visibility, $user_roles) ) {
                return true;
            }
        }

        return false;
    }



    // Checks if user can edit the event
    public static function user_can_edit_event(Event_Post $event_post) {

        if ( is_super_admin() ) {
            return true;
        }

        $back_visibility = $event_post->get_back_visibility();

        if ( 'stec_cal_default' === $back_visibility ) {

            $calendar_post = new Calendar_Post($event_post->get_calid());

            return Calendars::user_can_edit_calendar($calendar_post);
        } elseif ( 'stec_private' === $back_visibility ) {
            if ( $event_post->get_author() === get_current_user_id() ) {
                return true;
            }
        } else {
            $user_roles = Admin_Helper::get_user_roles();
            if ( in_array($back_visibility, $user_roles) ) {
                return true;
            }
        }

        return false;
    }

}
