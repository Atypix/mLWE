<?php

namespace Stachethemes\Stec;




use DateTime;
use DateTimeZone;




class Calendar_Post extends Stec_Post {



    private $post_type       = 'stec_calendar';
    private $post_status     = 'publish';
    private $title           = null;
    private $icon            = null;
    private $color           = '#f15e6e';
    private $timezone        = 'UTC';
    private $writable        = 0;
    private $req_approval    = 1;
    private $back_visibility = 'stec_public';
    private $visibility      = 'stec_public';
    private $author          = null;



    public function __construct($post_id = null) {

        if ( $post_id && $this->post_type === get_post_type($post_id) ) {
            $this->id              = $post_id;
            $this->post_status     = get_post_status($post_id);
            $this->title           = get_post_field('post_title', $post_id);
            $this->icon            = get_post_meta($post_id, 'icon', true);
            $this->color           = get_post_meta($post_id, 'color', true);
            $this->timezone        = get_post_meta($post_id, 'timezone', true);
            $this->writable        = get_post_meta($post_id, 'writable', true);
            $this->req_approval    = get_post_meta($post_id, 'req_approval', true);
            $this->back_visibility = get_post_meta($post_id, 'back_visibility', true);
            $this->visibility      = get_post_meta($post_id, 'visibility', true);
        }
    }



    public function set_author($author) {
        $this->author = $author;
    }



    public function get_title() {
        return $this->title;
    }



    public function get_icon() {
        return $this->icon;
    }



    public function get_color() {
        return $this->color;
    }



    public function get_timezone() {
        return $this->timezone;
    }



    public function get_writable() {
        return $this->writable;
    }



    public function get_req_approval() {
        return $this->req_approval;
    }



    public function get_back_visibility() {
        return $this->back_visibility;
    }



    public function get_visibility() {
        return $this->visibility;
    }



    public function set_title($title) {
        $this->title = $title;
    }



    public function set_icon($icon) {
        $this->icon = $icon;
    }



    public function set_color($color) {
        $this->color = $color;
    }



    public function set_timezone($timezone) {
        $this->timezone = $timezone;
    }



    public function set_writable($writable) {
        $this->writable = $writable;
    }



    public function set_req_approval($req_approval) {
        $this->req_approval = $req_approval;
    }



    public function set_back_visibility($back_visibility) {
        $this->back_visibility = $back_visibility;
    }



    public function set_visibility($visibility) {
        $this->visibility = $visibility;
    }



    public function get_post_data() {

        return array(
                'ID'           => $this->id,
                'post_author'  => $this->author ? $this->author : get_current_user_id(),
                'post_content' => '',
                'post_title'   => $this->title,
                'post_type'    => $this->post_type,
                'post_status'  => $this->post_status,
                'meta_input'   => array_merge(array(
                        'icon'            => $this->icon,
                        'color'           => $this->color,
                        'timezone'        => $this->timezone,
                        'writable'        => $this->writable,
                        'req_approval'    => $this->req_approval,
                        'back_visibility' => $this->back_visibility,
                        'visibility'      => $this->visibility,
                        ), $this->custom_meta)
        );
    }



    public function delete_post() {

        $result = parent::delete_post();

        if ( false === $result ) {
            return $result;
        }

        // Delete all events from this calendar 
        $events = Events::get_events($this->id);

        foreach ( $events as $event ) {
            $event->delete_post();
        }

        // Delete all cron jobs for this calendar
        Cron::delete_calendar_jobs($this->id);

        return true;
    }



    public function get_timezone_offset() {

        if ( !$this->timezone ) {
            return 0;
        }

        $dateTimeZoneUTC     = new DateTimeZone("UTC");
        $dateTimeZoneCAL     = new DateTimeZone($this->timezone);
        $dateTimeUTC         = new DateTime("now", $dateTimeZoneUTC);
        $timezone_utc_offset = $dateTimeZoneCAL->getOffset($dateTimeUTC);

        return $timezone_utc_offset;
    }



    public function get_front_data() {
        return array(
                'id'       => $this->id,
                'title'    => $this->title,
                'color'    => $this->color,
                'timezone' => $this->timezone
        );
    }



    public function insert_post() {

        if ( $this->id ) {
            if ( !Calendars::user_can_edit_calendar($this) ) {
                throw new Exception(__("You don't have permission to edit this calendar", 'stec'));
            }
        }

        return parent::insert_post();
    }

}
