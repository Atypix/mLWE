<?php

namespace Stachethemes\Stec;




/**
 * Front Create/Delete event
 */
class Submit_Event {



    public static function get_user_cookie($create = true) {

        $cookie_name = "stachethemes_ec_anon_user";

        if ( !isset($_COOKIE[$cookie_name]) ) {

            if ( $create === true ) {

                $id = uniqid('stec-user-');

                if ( setcookie($cookie_name, $id, time() + (10 * 365 * 24 * 60 * 60), "/") === false ) {
                    return false;
                }

                $_COOKIE[$cookie_name] = $id;
            }
        }

        return strlen($_COOKIE[$cookie_name]) > 13 ? $_COOKIE[$cookie_name] : false;
    }



    /**
     * Creates event
     * 
     * @return event|false
     */
    public static function create_event() {

        $images   = array();
        $image_id = self::calendar_front_image_file_upload();

        if ( $image_id !== false ) {
            $images = array((int) $image_id);
        }

        $event = new Event_Post();

        $event->set_calid(Admin_Helper::post('calendar_id', false, FILTER_VALIDATE_INT));
        $event->set_title(Admin_Helper::post('title', ''));
        $event->set_color(Admin_Helper::post('event_color'));
        $event->set_icon(Admin_Helper::post('icon'));
        $event->set_visibility('stec_cal_default');
        $event->set_featured(Admin_Helper::post('featured'));

        $start_date         = Admin_Helper::post('start_date');
        $start_time_hours   = Admin_Helper::post('start_time_hours');
        $start_time_minutes = Admin_Helper::post('start_time_minutes');
        $end_date           = Admin_Helper::post('end_date');
        $end_time_hours     = Admin_Helper::post('end_time_hours');
        $end_time_minutes   = Admin_Helper::post('end_time_minutes');
        $start_date_time    = $start_date . ' ' . $start_time_hours . ':' . $start_time_minutes . ':00';
        $end_date_time      = $end_date . ' ' . $end_time_hours . ':' . $end_time_minutes . ':00';
        $event->set_start_date($start_date_time);
        $event->set_end_date($end_date_time);

        $event->set_rrule(Admin_Helper::post('rrule', ''));
        $event->set_exdate(Admin_Helper::post('exdate', ''));
        $event->set_is_advanced_rrule(Admin_Helper::post('is_advanced_rrule', 0));
        $event->set_keywords(Admin_Helper::post('keywords'));
        $event->set_all_day(Admin_Helper::post('all_day', false) !== false ? 1 : 0);
        $event->set_counter(Admin_Helper::post('counter'));
        $event->set_comments(0);
        $event->set_link(Admin_Helper::post('link'));
        $event->set_images($images);
        $event->set_location(Admin_Helper::post('location', ''));
        $event->set_description(Admin_Helper::post('description', ''));
        $event->set_description_short(Admin_Helper::post('description_short', ''));
        $event->set_custom_meta(array(
                'contact_email' => Admin_Helper::post('contact_email', false, FILTER_VALIDATE_EMAIL)
        ));
        $event->set_review_note(Admin_Helper::post('review_note', ''));
        $event->set_approved(0);

        $calendar = new Calendar_Post($event->get_calid());

        if ( $calendar->get_req_approval() == 0 ) {
            $event->set_approved(1);
        }

        if ( !is_user_logged_in() ) {
            $cookie = self::get_user_cookie();

            if ( false === $cookie ) {
                return false;
            }

            $event->set_custom_meta(array(
                    'created_by_cookie' => $cookie
            ));
        }

        $result = $event->insert_post();

        if ( !is_wp_error($result) ) {
            if ( is_numeric($result) ) {
                $event->set_id($result);
                return $event;
            }

            return false;
        }

        return false;
    }



    public static function is_image($mediapath) {
        return @is_array(getimagesize($mediapath));
    }



    public static function calendar_front_image_file_upload() {

        if ( !isset($_FILES['fileimage']) ) {
            return false;
        }

        $ext = strtolower(pathinfo($_FILES['fileimage']['name'], PATHINFO_EXTENSION));

        if ( $ext != 'jpg' && $ext != 'png' ) {
            return false;
        }

        if ( self::is_image($_FILES['fileimage']['tmp_name']) === false ) {
            return false;
        }

        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );

        $attachment_id = media_handle_upload('fileimage', 0);

        if ( is_wp_error($attachment_id) ) {

            return false;
        } else {

            return $attachment_id;
        }
    }



    public static function get_max_upload_size() {
        $u_bytes = wp_convert_hr_to_bytes(ini_get('upload_max_filesize'));
        $p_bytes = wp_convert_hr_to_bytes(ini_get('post_max_size'));
        $bytes   = apply_filters('upload_size_limit', min($u_bytes, $p_bytes), $u_bytes, $p_bytes);
        return round($bytes * 1e-6);
    }



    public static function delete_event($event_id) {

        $event = new Event_Post($event_id);

        if ( !$event->get_id() ) {
            return false;
        }

        if ( Events::user_can_edit_event($event) ) {
            if ( false !== $event->delete_post() ) {
                return true;
            }
        }

        return false;
    }



    /**
     * Returns minutes list
     * @return array
     */
    public static function get_minutes_array() {



        return Admin_Helper::minutes_array();
    }



    public static function validate_captcha($secret, $response, $ip) {

        $fields = array(
                'secret'   => $secret,
                'response' => $response,
                'remoteip' => $ip
        );

        $fields_string = "";

        foreach ( $fields as $key => $value ) {

            $fields_string .= $key . '=' . $value . '&';
        }

        rtrim($fields_string, '&');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

        $result = curl_exec($ch);

        curl_close($ch);

        $json = json_decode($result);

        return isset($json->success) && $json->success == 'true' ? true : false;
    }



    /**
     * Notify admin an event is awaiting approval via mail
     * @param int $event_id the event id
     */
    public static function notify_admin($event_id) {

        $event     = new Event_Post($event_id);
        $user_info = get_user_by('id', $event->get_author());
        $author    = isset($user_info->display_name) ? $user_info->display_name : 'Anonymous';
        $to        = get_option('admin_email');
        $subject   = __('A new event is awaiting your approval', 'stec');
        $admin_url = admin_url('admin.php?page=stec_menu__events&view=edit&calendar_id=' . $event->get_calid() . '&event_id=' . $event->get_id());
        $content   = sprintf(__("A new event has been submitted by %s. \n\nTo review the event visit %s", 'stec'), $author, $admin_url);

        $subject = apply_filters('stec_submit_event_notify_admin_subject', $subject);
        $content = apply_filters('stec_submit_event_notify_admin_content', $content);
        $to      = apply_filters('stec_admin_email', $to);

        wp_mail($to, $subject, $content);
    }



    /**
     * Notify owner their event is submitted for approval via mail
     * @param int $event_id the event id
     */
    public static function notify_owner($event_id) {

        $event     = new Event_Post($event_id);
        $user_info = get_user_by('id', $event->get_author());
        $author    = isset($user_info->display_name) ? $user_info->display_name : 'Anonymous';
        $to        = $event->get_custom_meta('contact_email');
        $subject   = __('Your event has been submitted', 'stec');
        $admin_url = admin_url('admin.php?page=stec_menu__events&view=edit&calendar_id=' . $event->get_calid() . '&event_id=' . $event->get_id());
        $content   = __('Your event has been submitted and is awaiting review. You will be notified by email once your event is approved.', 'stec');

        $subject = apply_filters('stec_submit_event_notify_owner_subject', $subject);
        $content = apply_filters('stec_submit_event_notify_owner_content', $content);

        wp_mail($to, $subject, $content);
    }

}
