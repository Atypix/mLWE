<?php

namespace Stachethemes\Stec;




class Calendars {



    /**
     * Get calendars editable by current logged admin
     */
    public static function get_admin_calendars() {
        $calendars = self::get_calendars();
        foreach ( $calendars as $k => $calendar ) {
            if ( false === self::user_can_edit_calendar($calendar) ) {
                unset($calendars[$k]);
            }
        }
        return $calendars;
    }



    /**
     * Get calendars visible on the front-end by the current visitor
     */
    public static function get_front_calendars() {
        $calendars = self::get_calendars();
        foreach ( $calendars as $k => $calendar ) {
            if ( false === self::user_can_view_calendar($calendar) ) {
                unset($calendars[$k]);
            }
        }
        return $calendars;
    }



    /**
     * Returns all calendars
     * @return type calendars
     */
    public static function get_calendars($custom_meta_query = null) {

        $meta_query = array();

        if ( $custom_meta_query ) {
            $meta_query[] = $custom_meta_query;
        }

        $calendars = array();

        $calendar_ids = get_posts(array(
                'posts_per_page' => -1,
                'post_type'      => 'stec_calendar',
                'fields'         => 'ids',
                'meta_query'     => $meta_query
        ));

        if ( !$calendar_ids ) {
            return array();
        }

        foreach ( $calendar_ids as $id ) {

            $calendar_post = new Calendar_Post($id);

            $calendars[] = $calendar_post;
        }

        return apply_filters('stec_get_calendars', $calendars);
    }



    public static function get_calendars_list() {
        $calendars = self::get_calendars();
        $list      = array();
        foreach ( $calendars as $cal ) {
            $list[$cal->get_id()] = $cal->get_title();
        }

        return $list;
    }



    public static function bulk_delete($calendar_ids = array()) {
        $filter_calendar_ids = filter_var_array($calendar_ids, FILTER_VALIDATE_INT);
        $unique_caledanr_ids = array_filter($filter_calendar_ids);

        foreach ( $unique_caledanr_ids as $calendar_id ) {
            $calendar = new Calendar_Post($calendar_id);

            if ( !$calendar->get_id() ) {
                continue;
            }

            if ( false === self::user_can_edit_calendar($calendar) ) {
                continue;
            }

            $calendar->delete_post();
        }
        unset($calendar_id);

        return true;
    }



    /**
     * Inserts or Updates post
     */
    public static function insert_post(Calendar_Post $calendar) {


        $post_data = apply_filters('stec_insert_post', $calendar->get_post_data());

        if ( $calendar->get_id() ) {
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



    /**
     * Checks if user can add events to this calendar from the front-end
     */
    public static function user_can_write_calendar(Calendar_Post $calendar_post) {

        $visibility = $calendar_post->get_writable();

        if ( 'stec_private' === $visibility ) {
            if ( $calendar_post->get_author() === get_current_user_id() ) {
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



    /**
     * Checks if user can add edit/delete event
     */
    public static function user_can_edit_calendar(Calendar_Post $calendar_post) {

        if ( is_super_admin() ) {
            return true;
        }

        $visibility = $calendar_post->get_back_visibility();

        if ( 'stec_private' === $visibility ) {
            if ( $calendar_post->get_author() === get_current_user_id() ) {
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



    // Checks if user can view the calendar on the front-end
    public static function user_can_view_calendar(Calendar_Post $calendar_post) {

        if ( is_super_admin() ) {
            return true;
        }

        $visibility = $calendar_post->get_visibility();

        if ( 'stec_private' === $visibility ) {
            if ( $calendar_post->get_author() === get_current_user_id() ) {
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



    /**
     * Get writable by user calendars list
     */
    public static function get_writable_calendar_list() {

        $writables = array();
        $calendars = self::get_calendars();

        foreach ( $calendars as $calendar ) {
            if ( false === self::user_can_write_calendar($calendar) ) {
                continue;
            }

            $writables[] = $calendar;
        }

        return $writables;
    }



    public static function can_export($calendar) {

        $events = Events::get_front_events($calendar->get_id());

        return !empty($events);
    }

}
