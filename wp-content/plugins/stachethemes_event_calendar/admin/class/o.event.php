<?php

namespace Stachethemes\Stec;




use DateTime;
use DateTimeZone;




class Event_Post extends Stec_Post {



    private $post_type          = 'stec_event';
    private $post_status        = 'publish';
    private $author             = null;
    private $uid                = null;
    private $recurrence_id      = '';
    private $calid              = null;
    private $slug               = null;
    private $title              = null;
    private $color              = '#f15e6e';
    private $icon               = null;
    private $visibility         = 'stec_public';
    private $back_visibility    = 'stec_public';
    private $featured           = 0;
    private $start_date         = null;
    private $end_date           = null;
    private $all_day            = 0;
    private $keywords           = '';
    private $counter            = 0;
    private $comments           = 0;
    private $link               = null;
    private $approved           = 0;
    private $exdate             = null;
    private $rrule              = '';
    private $is_advanced_rrule  = 0;
    private $location           = null;
    private $location_details   = null;
    private $location_forecast  = null;
    private $location_use_coord = null;
    private $description        = '';
    private $description_short  = '';
    private $review_note        = '';
    private $images             = array();
    private $schedule           = array();
    private $guests             = array();
    private $attendance         = array();
    private $attachments        = array();
    private $products           = array();



    public function __construct($post_id = null) {

        if ( $post_id && $this->post_type === get_post_type($post_id) ) {
            $this->id                 = $post_id;
            $this->post_status        = get_post_status($post_id);
            $this->author             = get_post_field('post_author', $post_id);
            $this->title              = get_post_field('post_title', $post_id);
            $this->slug               = get_post_field('post_name', $post_id);
            $this->description        = get_post_field('post_content', $post_id);
            $this->description_short  = get_post_field('post_excerpt', $post_id);
            $this->review_note        = get_post_meta($post_id, 'review_note', true);
            $this->uid                = get_post_meta($post_id, 'uid', true);
            $this->recurrence_id      = get_post_meta($post_id, 'recurrence_id', true);
            $this->calid              = get_post_meta($post_id, 'calid', true);
            $this->color              = get_post_meta($post_id, 'color', true);
            $this->icon               = get_post_meta($post_id, 'icon', true);
            $this->visibility         = get_post_meta($post_id, 'visibility', true);
            $this->back_visibility    = get_post_meta($post_id, 'back_visibility', true);
            $this->featured           = get_post_meta($post_id, 'featured', true);
            $this->start_date         = get_post_meta($post_id, 'start_date', true);
            $this->end_date           = get_post_meta($post_id, 'end_date', true);
            $this->all_day            = get_post_meta($post_id, 'all_day', true);
            $this->keywords           = get_post_meta($post_id, 'keywords', true);
            $this->counter            = get_post_meta($post_id, 'counter', true);
            $this->comments           = get_post_meta($post_id, 'comments', true);
            $this->link               = get_post_meta($post_id, 'link', true);
            $this->approved           = get_post_meta($post_id, 'approved', true);
            $this->exdate             = get_post_meta($post_id, 'exdate', true);
            $this->rrule              = get_post_meta($post_id, 'rrule', true);
            $this->is_advanced_rrule  = get_post_meta($post_id, 'is_advanced_rrule', true);
            $this->location           = get_post_meta($post_id, 'location', true);
            $this->location_details   = get_post_meta($post_id, 'location_details', true);
            $this->location_forecast  = get_post_meta($post_id, 'location_forecast', true);
            $this->location_use_coord = get_post_meta($post_id, 'location_use_coord', true);
            $this->images             = get_post_meta($post_id, 'images', true);
            $this->schedule           = get_post_meta($post_id, 'schedule', true);
            $this->guests             = get_post_meta($post_id, 'guests', true);
            $this->attendance         = get_post_meta($post_id, 'attendance', true);
            $this->attachments        = get_post_meta($post_id, 'attachments', true);
            $this->products           = get_post_meta($post_id, 'products', true);
        }
    }



    public function set_author($author) {
        $this->author = $author;
    }



    public function get_uid() {
        return $this->uid;
    }



    public function get_recurrence_id() {
        return $this->recurrence_id;
    }



    public function get_calid() {
        return $this->calid;
    }



    public function get_slug() {
        return $this->slug;
    }



    public function get_title() {
        return $this->title;
    }



    public function get_color() {
        return $this->color;
    }



    public function get_icon() {
        return $this->icon;
    }



    public function get_visibility() {
        return $this->visibility;
    }



    public function get_featured() {
        return $this->featured;
    }



    public function get_start_date() {
        return $this->start_date;
    }



    public function get_end_date() {
        return $this->end_date;
    }



    public function get_all_day() {
        return $this->all_day;
    }



    public function get_keywords() {
        return $this->keywords;
    }



    public function get_counter() {
        return $this->counter;
    }



    public function get_comments() {
        return $this->comments;
    }



    public function get_link() {
        return $this->link;
    }



    public function get_approved() {
        return $this->approved;
    }



    public function get_exdate() {
        return $this->exdate;
    }



    public function get_rrule() {
        return $this->rrule;
    }



    public function get_is_advanced_rrule() {
        return $this->is_advanced_rrule;
    }



    public function get_location() {
        return $this->location;
    }



    public function get_location_details() {
        return $this->location_details;
    }



    public function get_location_forecast() {
        return $this->location_forecast;
    }



    public function get_location_use_coord() {
        return $this->location_use_coord;
    }



    public function get_description() {
        return $this->description;
    }



    public function get_description_short() {
        return $this->description_short;
    }



    public function get_images() {
        return $this->images;
    }



    public function get_parsed_images() {

        $parsed_images = array();

        if ( !$this->images ) {
            return $parsed_images;
        }

        foreach ( $this->images as $id ) {
            $attachment = get_post($id);
            $src        = wp_get_attachment_image_src($id, 'full');
            $thumb      = wp_get_attachment_image_src($id, 'medium');

            $parsed_images[] = (object) array(
                            'alt'         => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
                            'caption'     => $attachment->post_excerpt,
                            'description' => $attachment->post_content,
                            'src'         => $src[0],
                            'thumb'       => $thumb[0],
                            'title'       => $attachment->post_title
            );
        }

        return $parsed_images;
    }



    public function get_schedule() {
        return $this->schedule;
    }



    public function get_guests() {
        return $this->guests;
    }



    public function get_attendance() {
        return $this->attendance;
    }



    public function get_attachments() {
        return $this->attachments;
    }



    public function get_parsed_attachments() {
        $parsed_attachments = array();

        foreach ( $this->attachments as $attachment ) {

            $id = $attachment->id;

            $parsed_attachments[] = (object) array(
                            'id'          => $id,
                            'filename'    => basename(get_attached_file($id, true)),
                            'size'        => size_format(filesize(get_attached_file($id))),
                            'link'        => wp_get_attachment_url($id),
                            'description' => get_post_field('post_content', $id),
            );
        }

        return $parsed_attachments;
    }



    public function get_products() {
        return $this->products;
    }



    public function set_uid($uid = null) {
        if ( null === $uid ) {
            $uid = uniqid('e-') . '_' . md5(microtime()) . '@stachethemes_ec.com';
        }
        $this->uid = $uid;
    }



    public function set_recurrence_id($recurrence_id) {
        $this->recurrence_id = $recurrence_id;
    }



    public function set_calid($calid) {
        $this->calid = $calid;
    }



    public function set_slug($slug) {
        $this->slug = $slug;
    }



    public function set_title($title) {
        $this->title = $title;
    }



    public function set_color($color) {
        $this->color = $color;
    }



    public function set_icon($icon) {
        $this->icon = $icon;
    }



    public function set_visibility($visibility) {
        $this->visibility = $visibility;
    }



    public function set_featured($featured) {
        $this->featured = $featured;
    }



    public function set_start_date($start_date) {
        $this->start_date = $start_date;
    }



    public function set_end_date($end_date) {
        $this->end_date = $end_date;
    }



    public function set_all_day($all_day) {
        $this->all_day = $all_day;
    }



    public function set_keywords($keywords) {
        $this->keywords = $keywords;
    }



    public function set_counter($counter) {
        $this->counter = $counter;
    }



    public function get_back_visibility() {
        return $this->back_visibility;
    }



    public function set_back_visibility($back_visibility) {
        $this->back_visibility = $back_visibility;
    }



    public function set_comments($comments) {
        $this->comments = $comments;
    }



    public function set_link($link) {
        $this->link = $link;
    }



    public function set_approved($approved) {
        $this->approved = $approved;
    }



    public function set_exdate($exdate) {
        $this->exdate = $exdate;
    }



    public function set_rrule($rrule) {
        $this->rrule = $rrule;
    }



    public function set_is_advanced_rrule($is_advanced_rrule) {
        $this->is_advanced_rrule = $is_advanced_rrule;
    }



    public function set_location($location) {
        $this->location = $location;
    }



    public function set_location_details($location_details) {
        $this->location_details = $location_details;
    }



    public function set_location_forecast($location_forecast) {
        $this->location_forecast = $location_forecast;
    }



    public function set_location_use_coord($location_use_coord) {
        $this->location_use_coord = $location_use_coord;
    }



    public function set_description($description) {
        $this->description = $description;
    }



    public function set_description_short($description_short) {
        $this->description_short = $description_short;
    }



    public function set_images($images) {
        $this->images = $images;
    }



    public function set_schedules($schedules) {
        $this->schedule = array();

        foreach ( $schedules as $schedule ) {
            $this->set_schedule($schedule);
        }
    }



    public function set_schedule(Event_Meta_Schedule $schedule) {
        $this->schedule[] = $schedule;
    }



    public function set_guests(Array $guests) {
        $this->guests = array();

        foreach ( $guests as $guest ) {
            $this->set_guest($guest);
        }
    }



    public function set_guest(Event_Meta_Guest $guest) {
        $this->guests[] = $guest;
    }



    public function set_attendees($attendees) {
        $this->attendance = array();

        foreach ( $attendees as $attendee ) {
            $this->set_attendee($attendee);
        }
    }



    public function set_attendee(Event_Meta_Attendee $attendee) {
        $this->attendance[] = $attendee;
    }



    public function set_attachments($attachments) {
        $this->attachments = array();

        foreach ( $attachments as $attachment ) {
            $this->set_attachment($attachment);
        }
    }



    public function set_attachment(Event_Meta_Attachment $attachment) {
        $this->attachments[] = $attachment;
    }



    public function set_products($products) {
        $this->products = array();

        foreach ( $products as $product ) {
            $this->set_product($product);
        }
    }



    public function set_product(Event_Meta_Product $product) {
        $this->products[] = $product;
    }



    public function get_author() {
        return $this->author ? $this->author : get_current_user_id();
    }



    public function get_review_note() {
        return $this->review_note;
    }



    public function set_review_note($review_note) {
        $this->review_note = $review_note;
    }



    public function get_post_data() {

        return array(
                'ID'           => $this->id,
                'post_author'  => $this->author ? $this->author : get_current_user_id(),
                'post_content' => $this->description,
                'post_excerpt' => $this->description_short,
                'post_title'   => $this->title,
                'post_name'    => $this->slug,
                'post_type'    => $this->post_type,
                'post_status'  => $this->post_status,
                'meta_input'   => array_merge(array(
                        'review_note'        => $this->review_note,
                        'uid'                => $this->uid,
                        'recurrence_id'      => $this->recurrence_id,
                        'calid'              => $this->calid,
                        'color'              => $this->color,
                        'icon'               => $this->icon,
                        'visibility'         => $this->visibility,
                        'back_visibility'    => $this->back_visibility,
                        'featured'           => $this->featured,
                        'start_date'         => $this->start_date,
                        'end_date'           => $this->end_date,
                        'all_day'            => $this->all_day,
                        'keywords'           => $this->keywords,
                        'counter'            => $this->counter,
                        'comments'           => $this->comments,
                        'link'               => $this->link,
                        'approved'           => $this->approved,
                        'exdate'             => $this->exdate,
                        'rrule'              => $this->rrule,
                        'is_advanced_rrule'  => $this->is_advanced_rrule,
                        'location'           => $this->location,
                        'location_details'   => $this->location_details,
                        'location_forecast'  => $this->location_forecast,
                        'location_use_coord' => $this->location_use_coord,
                        'images'             => $this->images,
                        'schedule'           => $this->schedule,
                        'guests'             => $this->guests,
                        'attendance'         => $this->attendance,
                        'attachments'        => $this->attachments,
                        'products'           => $this->products
                        ), $this->custom_meta)
        );
    }



    public function get_timezone_offset() {

        if ( !$this->calid ) {
            return 0;
        }

        $calendar = new Calendar_Post($this->calid);
        return $calendar->get_timezone_offset();
    }



    public function get_timezone() {

        if ( !$this->calid ) {
            return 0;
        }

        $calendar = new Calendar_Post($this->calid);
        return $calendar->get_timezone();
    }



    public function get_front_data() {


        $calendar = new Calendar_Post($this->calid);

        return (array(
                'id'                  => $this->id,
                'author'              => get_the_author_meta('display_name', $this->author),
                'title'               => $this->title,
                'slug'                => $this->slug,
                'description'         => wpautop($this->description),
                'description_short'   => $this->description_short,
                'post_status'         => $this->post_status,
                'uid'                 => $this->uid,
                'recurrence_id'       => $this->recurrence_id,
                'calid'               => $this->calid,
                'color'               => $this->color,
                'icon'                => $this->icon,
                'visibility'          => $this->visibility,
                'back_visibility'     => $this->back_visibility,
                'featured'            => $this->featured,
                'start_date'          => $this->start_date,
                'end_date'            => $this->end_date,
                'all_day'             => $this->all_day,
                'keywords'            => $this->keywords,
                'counter'             => $this->counter,
                'comments'            => $this->comments,
                'link'                => $this->link,
                'approved'            => $this->approved,
                'exdate'              => $this->exdate,
                'rrule'               => $this->rrule,
                'is_advanced_rrule'   => $this->is_advanced_rrule,
                'location'            => $this->location,
                'location_details'    => $this->location_details,
                'location_forecast'   => $this->location_forecast,
                'location_use_coord'  => $this->location_use_coord,
                'images'              => $this->images,
                'images_meta'         => $this->get_parsed_images(),
                'schedule'            => $this->schedule,
                'guests'              => $this->parse_guests(),
                'attendance'          => $this->parse_attendees(),
                'attachments'         => $this->get_parsed_attachments(),
                'products'            => $this->get_parsed_products(),
                'timezone_utc_offset' => $this->get_timezone_offset(),
                'permalink'           => get_the_permalink($this->id),
                'calendar'            => $calendar->get_front_data()
        ));
    }



    /**
     * Returns guests with processed images
     * @return object array
     */
    public function parse_guests() {
        $guests = $this->guests;

        foreach ( $guests as &$guest ) {
            $photo             = wp_get_attachment_image_src($guest->photo, 'full');
            $guest->photo_full = $photo[0];
        }

        return $guests;
    }



    /**
     * Returns attendees with processed data
     * @return object array
     */
    public function parse_attendees() {

        foreach ( $this->attendance as &$attendee ) {
            if ( $attendee->userid ) {
                $userdata         = get_userdata($attendee->userid);
                $attendee->name   = $userdata->display_name;
                $attendee->avatar = get_avatar_url($attendee->userid);
            }

            if ( filter_var($attendee->email, FILTER_VALIDATE_EMAIL) !== false ) {
                $mailname         = explode('@', $attendee->email);
                $attendee->name   = $mailname[0];
                $attendee->avatar = get_avatar_url($attendee->email);
            }
        }

        return $this->attendance;
    }



    public function set_attendee_status($status = 0, $params = array(), $auto_update = true) {

        foreach ( $this->attendance as &$attendee ) {

            if ( isset($params['userid']) && $attendee->userid == $params['userid'] ) {

                $attendee->status[$params['repeat_offset']] = $status;

                if ( true === $auto_update ) {
                    update_post_meta($this->id, 'attendance', '');
                    update_post_meta($this->id, 'attendance', $this->attendance);
                }


                return true;
            } elseif ( isset($params['email']) && $attendee->email == $params['email'] ) {

                $attendee->status[$params['repeat_offset']] = $status;

                if ( true === $auto_update ) {
                    update_post_meta($this->id, 'attendance', '');
                    update_post_meta($this->id, 'attendance', $this->attendance);
                }


                return true;
            }
        }


        return false;
    }



    /**
     * Get event permalink with repeat offset if any
     * @param int $repeat_offset
     * @return false|string
     */
    public function get_permalink($repeat_offset = 0) {

        if ( !$this->id ) {
            return false;
        }

        $permalink = get_the_permalink($this->id);

        if ( $repeat_offset && $repeat_offset > 0 ) {
            $permalink = $permalink . $repeat_offset;
        }

        return $permalink;
    }



    /**
     * Create google calendar add event link with repeat offset if any
     * @param int $repeat_offset
     * @return string google cal link
     */
    public function get_gcal_link($repeat_offset = 0) {

        // Get real start date with repeat offset
        $start_date = date('Ymd\THis', strtotime($this->start_date) + $repeat_offset);
        $start      = null;

        if ( $this->get_all_day() == '1' ) {
            // Since it's date specific don't UTC the date
            $start = date('Ymd', strtotime($start_date));
        } else {
            $start = Admin_Helper::to_utc_time($start_date, $this->get_timezone()) . 'Z';
        }

        // Get real end date with repeat offset
        $end_date = date('Ymd\THis', strtotime($this->end_date) + $repeat_offset);
        $end      = null;

        if ( $this->get_all_day() == '1' ) {
            // Since it's date specific don't UTC the date
            $end = date('Ymd', strtotime($end_date) + 24 * 3600);
        } else {
            $end = Admin_Helper::to_utc_time($end_date, $this->get_timezone()) . 'Z';
        }

        return "https://calendar.google.com/calendar/render?action=TEMPLATE&amp;text={$this->title}&amp;dates=" . $start . "/" . $end . "&amp;details={$this->description_short}&amp;location={$this->location}&amp;sf=true&amp;output=xml";
    }



    public function insert_post() {

        if ( $this->id ) {
            if ( !Events::user_can_edit_event($this) ) {
                throw new Exception(__("You don't have permission to edit this event", 'stec'));
            }
        }

        if ( !$this->uid ) {
            $this->set_uid();
        }

        $result = parent::insert_post();

        if ( is_wp_error($result) ) {
            $errors = $result->get_error_messages();
            $msg    = implode('<br>', $errors);
            throw new Stec_Exception($msg);
        }

        Admin_Helper::send_mail_invites($this);

        return $result;
    }



    public function delete_post() {

        if ( Events::user_can_edit_event($this) ) {
            return parent::delete_post();
        }

        return false;
    }



    public function get_parsed_products() {
        $products = array();

        if ( !class_exists('WooCommerce') ) {
            return $products;
        }

        foreach ( $this->products as $k => $product ) {

            $wc_product           = WC()->product_factory->get_product($product->id);
            $wc_product_post_date = get_post($wc_product->get_id());

            $products[$k]                 = new \stdClass();
            $products[$k]->id             = $wc_product->get_id();
            $products[$k]->sku            = $wc_product->get_sku();
            $products[$k]->url            = $wc_product->get_permalink();
            $products[$k]->purchesable    = $wc_product->is_purchasable();
            $products[$k]->is_in_stock    = $wc_product->is_in_stock();
            $products[$k]->stock_quantity = $wc_product->get_stock_quantity();
            $products[$k]->is_featured    = $wc_product->is_featured();
            $products[$k]->is_on_sale     = $wc_product->is_on_sale();
            $products[$k]->has_child      = $wc_product->has_child();
            $products[$k]->image          = $wc_product->get_image();
            $products[$k]->html_price     = $wc_product->get_price_html();
            $products[$k]->title          = $wc_product->get_title();
            $products[$k]->post_data      = array(
                    'excerpt' => $wc_product_post_date->post_excerpt
            );
        }


        return $products;
    }



    /**
     * Get event ocurrencies between two dates 
     * 
     * Dates returned are in calendar timezone
     * The returned array includes the event initial start date if in scope
     * Skips exdates
     * 
     * @param string $range_start Y-m-d
     * @param string $range_end Y-m-d
     * @return array 
     */
    public function get_ocurrencies($range_start, $range_end) {

        if ( !$this->rrule ) {
            return array();
        }

        $dtrange_start = new DateTime($range_start, new DateTimeZone($this->get_timezone()));
        $dtrange_end   = new DateTime($range_end, new DateTimeZone($this->get_timezone()));
        $dtstart       = new DateTime($this->start_date, new DateTimeZone($this->get_timezone()));
        $rrule         = new \RRule\RRule($this->rrule, $dtstart);
        $dates         = $rrule->getOccurrencesBetween($range_start, $range_end);
        $exdates       = explode(',', $this->exdate);

        $processed_dates = array();

        if ( $dtstart >= $dtrange_start && $dtstart <= $dtrange_end ) {

            if ( !in_array($dtstart->format('Ymd'), $exdates) ) {
                array_push($processed_dates, array(
                        'start_date'    => $this->start_date,
                        'repeat_offset' => 0,
                ));
            }
        }

        foreach ( $dates as $date ) {

            if ( in_array($date->format('Ymd'), $exdates) ) {
                continue;
            }

            array_push($processed_dates, array(
                    'start_date'    => $date->format('Y-m-d H:i:s'),
                    'repeat_offset' => abs($dtstart->format('U') - $date->format('U'))
            ));
        }

        return $processed_dates;
    }

}
