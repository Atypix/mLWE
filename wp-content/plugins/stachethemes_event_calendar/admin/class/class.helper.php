<?php

namespace Stachethemes\Stec;




use DateTime;
use DateTimeZone;




class Admin_Helper {



    private static $messages;



    public static function debug_log($msg) {

        if ( defined('WP_DEBUG_LOG') && true === WP_DEBUG_LOG ) {
            $pretext = 'STEC LOG :: ';
            error_log($pretext . $msg);
        }
    }



    public static function get($var, $default = false, $filter = FILTER_DEFAULT) {

        $value = filter_input(INPUT_GET, $var, $filter);

        return $value ? $value : $default;
    }



    public static function post($var, $default = false, $filter = FILTER_DEFAULT, $opt = FILTER_REQUIRE_SCALAR) {

        $value = filter_input(INPUT_POST, $var, $filter, $opt);

        return $value || $value === 0 || $value === '0' ? $value : $default;
    }



    public static function set_message($text, $type = '') {

        self::session_set('stec-admin-message', array(
                'text' => $text,
                'type' => $type
        ));
    }



    public static function session_set($var, $value = '') {

        $_SESSION[$var] = $value;
    }



    public static function session_get($var, $default = false) {

        return isset($_SESSION[$var]) ? $_SESSION[$var] : $default;
    }



    public static function display_message() {

        self::$messages = self::session_get('stec-admin-message');

        if ( self::$messages !== false ) {

            switch ( self::$messages['type'] ) :

                case 'error' :
                    echo "<p class='stachethemes-admin-msg-error'>" . self::$messages['text'] . "</p>";
                    break;

                default:
                    echo "<p class='stachethemes-admin-msg-notice'>" . self::$messages['text'] . "</p>";

            endswitch;
        }

        self::session_set('stec-admin-message', false);
        self::$messages = false;
    }



    public static function verify_nonce($action, $method = "POST") {

        switch ( strtolower($method) ) :

            case 'ajax' :

                if ( check_ajax_referer('stec-nonce-string', 'security', false) === false ) {
                    die();
                }

                break;

            case 'get' :

                $nonce = self::get('_wpnonce');

                if ( !wp_verify_nonce($nonce) ) {
                    die('<h1>Nonce did not verify</h1>');
                }

                break;


            default:

                $nonce = self::post('stec_admin_form_nonce');

                if ( !wp_verify_nonce($nonce, $action) ) {
                    die('<h1>Nonce did not verify</h1>');
                }
        endswitch;
    }



    public static function get_hours_array() {

        $arr = array();

        $time_format = Settings::get_admin_setting_value('stec_menu__general', 'time_format');

        if ( $time_format == '24' ) {
            // 24 hours

            $hour = 0;

            for ( $i = 0; $i < 24; $i++ ) {
                $hour       = sprintf("%02d", $hour);
                $arr[$hour] = $hour;
                $hour++;
            }
        } else {
            // 12/12 hours
            $h24  = 0;
            $hour = 12;
            $ampm = "am";

            for ( $i = 0; $i < 24; $i++ ) {


                if ( $i == 12 ) {
                    $ampm = "pm";
                }

                if ( $hour > 12 ) {
                    $hour = 1;
                }

                $hour = sprintf("%02d", $hour);
                $val  = $hour . "" . $ampm;

                $arr[sprintf("%02d", $h24)] = $val;

                $h24++;
                $hour++;
            }
        }

        return $arr;
    }



    public static function minutes_array() {
        $arr = array();
        for ( $i = 0; $i < 60; $i += 5 ) {
            $val       = sprintf("%02d", $i);
            $arr[$val] = $val;
        }
        return $arr;
    }



    public static function social_array() {

        $arr = array();

        $arr["fa fa-behance"]     = "Behance";
        $arr["fa fa-deviantart"]  = "Devian Art";
        $arr["fa fa-dribbble"]    = "Dribbble";
        $arr["fa fa-facebook"]    = "Facebook";
        $arr["fa fa-flickr"]      = "Flickr";
        $arr["fa fa-github"]      = "Github";
        $arr["fa fa-google-plus"] = "Google Plus";
        $arr["fa fa-lastfm"]      = "LastFM";
        $arr["fa fa-linkedin"]    = "LinkedIn";
        $arr["fa fa-reddit"]      = "Reddit";
        $arr["fa fa-soundcloud"]  = "Soundcloud";
        $arr["fa fa-tumblr"]      = "Tumblr";
        $arr["fa fa-twitch"]      = "Twitch";
        $arr["fa fa-twitter"]     = "Twitter";
        $arr["fa fa-vimeo"]       = "Vimeo";
        $arr["fa fa-youtube"]     = "Youtube";
        $arr["fa fa-instagram"]   = "Instagram";
        $arr["fa fa-pinterest"]   = "Pinterest";
        $arr["fa fa-skype"]       = "Skype";
        $arr["fa fa-steam"]       = "Steam";
        $arr["fa fa-envelope"]    = "E-Mail";

        return apply_filters('stec_social', $arr);
    }



    /**
     * Event icons
     * @return arr
     */
    public static function get_icon_list() {


        $arr = array();

        $arr['fa']                                        = __('No Icon', 'stec');
        $arr['fa fa-paw']                                 = __('Animals', 'stec');
        $arr['fa fa-plane']                               = __('Airport', 'stec');
        $arr['fa fa-glass']                               = __('Bar', 'stec');
        $arr['fa fa-birthday-cake']                       = __('Birthday Party', 'stec');
        $arr['fa fa-briefcase']                           = __('Business', 'stec');
        $arr['fa fa-bus']                                 = __('Bus tour', 'stec');
        $arr['fa fa-motorcycle']                          = __('Bike', 'stec');
        $arr['fa fa-bomb']                                = __('Bomb', 'stec');
        $arr['fa fa-tree']                                = __('Camping ', 'stec');
        $arr['fa fa-ship']                                = __('Cruise', 'stec');
        $arr['fa fa-coffee']                              = __('Coffee', 'stec');
        $arr['fa fa-bicycle']                             = __('Cycling', 'stec');
        $arr['fa fa-balance-scale']                       = __('Court Hall', 'stec');
        $arr['fa fa-futbol-o']                            = __('Football', 'stec');
        $arr['fa fa-gamepad']                             = __('Gaming', 'stec');
        $arr['fa fa-graduation-cap']                      = __('Graduation', 'stec');
        $arr['fa fa-suitcase']                            = __('Hotel', 'stec');
        $arr['fa fa-microphone']                          = __('Karaoke', 'stec');
        $arr['fa fa-book']                                = __('Library ', 'stec');
        $arr['fa fa-film']                                = __('Movie', 'stec');
        $arr['fa fa-university']                          = __('Museum', 'stec');
        $arr['fa fa-music']                               = __('Music', 'stec');
        $arr['fa fa-camera']                              = __('Photo', 'stec');
        $arr['fa fa-picture-o']                           = __('Picture/Painting', 'stec');
        $arr['fa fa-cutlery']                             = __('Restaurant', 'stec');
        $arr['fa fa-flag-checkered']                      = __('Race', 'stec');
        $arr['fa fa-shopping-basket']                     = __('Shopping', 'stec');
        $arr['fa fa-magic']                               = __('Show', 'stec');
        $arr['fa fa-beer']                                = __('Tavern', 'stec');
        $arr['fa fa-comments']                            = __('Talks', 'stec');
        $arr['fa fa-ticket']                              = __('Ticket', 'stec');
        $arr['fa fa-train']                               = __('Train', 'stec');
        $arr['fa fa-diamond']                             = __('Wedding', 'stec');
        $arr['fa fa-wheelchair']                          = __('Wheelchair', 'stec');
        $arr['fa fa-binoculars']                          = __('Binocular', 'stec');
        $arr['fa fa-bed']                                 = __('Bed', 'stec');
        $arr['fa fa-bolt']                                = __('Bolt', 'stec');
        $arr['fa fa-check']                               = __('Check Sign', 'stec');
        $arr['fa fa-comment-o']                           = __('Conversation', 'stec');
        $arr['fa fa-credit-card']                         = __('Credit Card', 'stec');
        $arr['fa fa-diamond']                             = __('Diamond', 'stec');
        $arr['fa fa-envelope-o']                          = __('Envelope', 'stec');
        $arr['fa fa-female']                              = __('Female Silhouette', 'stec');
        $arr['fa fa-male']                                = __('Male Silhouette', 'stec');
        $arr['fa fa-child']                               = __('Child Silhouette', 'stec');
        $arr['fa fa-fire-extinguisher']                   = __('Fire Extinguisher', 'stec');
        $arr['fa fa-hand-peace-o']                        = __('Hand Peace Sign', 'stec');
        $arr['fa fa-hand-o-up']                           = __('Hand Point Up', 'stec');
        $arr['fa fa-heart-o']                             = __('Heart', 'stec');
        $arr['fa fa-key']                                 = __('Key', 'stec');
        $arr['fa fa-gavel']                               = __('Gravel', 'stec');
        $arr['fa fa-life-ring']                           = __('Life Ring', 'stec');
        $arr['fa fa-lightbulb-o']                         = __('Lightbulb', 'stec');
        $arr['fa fa-money']                               = __('Money', 'stec');
        $arr['fa fa-moon-o']                              = __('Moon', 'stec');
        $arr['fa fa-shopping-bag']                        = __('Shopping Bag', 'stec');
        $arr['fa fa-video-camera']                        = __('Video Camera', 'stec');
        $arr['fa fa-ambulance']                           = __('Ambulance', 'stec');
        $arr['fa fa-paypal']                              = __('PayPal', 'stec');
        $arr['fa fa-google-wallet']                       = __('Google Wallet', 'stec');
        $arr['fa fa-usd']                                 = __('USD Sign', 'stec');
        $arr['fa fa-btc']                                 = __('Bitcoin Sign', 'stec');
        $arr['fa fa-gbp']                                 = __('British Pound Sign', 'stec');
        $arr['fa fa-eur']                                 = __('Euro Sign', 'stec');
        $arr['fa fa-apple']                               = __('Apple Logo', 'stec');
        $arr['fa fa-amazon']                              = __('Amazon Logo', 'stec');
        $arr['fa fa-android']                             = __('Android Logo', 'stec');
        $arr['fa fa-instagram']                           = __('Instagram Logo', 'stec');
        $arr['fa fa-facebook']                            = __('Facebook Logo', 'stec');
        $arr['fa fa-google-plus']                         = __('Google Plus Logo', 'stec');
        $arr['fa fa-steam']                               = __('Steam Logo', 'stec');
        $arr['fa fa-twitch']                              = __('Twitch Logo', 'stec');
        $arr['fa fa-yelp']                                = __('Yelp Logo', 'stec');
        $arr['fa fa-twitter']                             = __('Twitter Logo', 'stec');
        $arr['fa fa-youtube']                             = __('YouTube Logo', 'stec');
        $arr['fa fa-stethoscope']                         = __('Stethoscope', 'stec');
        $arr['fa fa-heartbeat']                           = __('Heartbeat', 'stec');
        $arr['fa fa-user-md']                             = __('Doctor', 'stec');
        $arr['fa fa-hospital-o']                          = __('Hospital', 'stec');
        $arr['fa fa-medkit']                              = __('Medkit', 'stec');
        $arr['fa fa-scissors']                            = __('Scissors', 'stec');
        $arr['fa fa-pie-chart']                           = __('Pie Chart', 'stec');
        $arr['fa fa-transgender']                         = __('Transgender Symbol', 'stec');
        $arr['fa fa-mars']                                = __('Male Sex Symbol', 'stec');
        $arr['fa fa-venus']                               = __('Female Sex Symbol', 'stec');
        $arr['fa fa-blind']                               = __('Blind Men Symbol', 'stec');
        $arr['fa fa-american-sign-language-interpreting'] = __('Sign Language Symbol', 'stec');
        $arr['fa fa-newspaper-o']                         = __('Newspaper', 'stec');
        $arr['fa fa-flag']                                = __('Flag', 'stec');
        $arr['fa fa-hashtag']                             = __('Hashtag', 'stec');
        $arr['fa fa-snapchat-ghost']                      = __('Snapchat Logo', 'stec');
        $arr['fa fa-subway']                              = __('Subway', 'stec');
        $arr['fa fa-share-alt']                           = __('Share Icon', 'stec');
        $arr['fa fa-sign-language']                       = __('Sign Language Icon', 'stec');
        $arr['fa fa-odnoklassniki']                       = __('Odnoklassniki Logo', 'stec');
        $arr['fa fa-language']                            = __('Multi-language Icon', 'stec');
        $arr['fa fa-book']                                = __('Book Icon', 'stec');
        $arr['fa fa-puzzle-piece']                        = __('Puzzle Piece', 'stec');
        $arr['fa fa-television']                          = __('Television', 'stec');
        $arr['fa fa-desktop']                             = __('Desktop', 'stec');
        $arr['fa fa-calculator']                          = __('Calculator', 'stec');
        $arr['fa fa-cubes']                               = __('Cubes', 'stec');

        return apply_filters('stec_icons', $arr);
    }



    public static function timezones_list() {

        $zones_array = array();
        $timestamp   = time();

        foreach ( timezone_identifiers_list() as $zone ) {
            date_default_timezone_set($zone);
            $zones_array[$zone] = $zone . ' (' . 'UTC/GMT ' . date('P', $timestamp) . ')';
        }

        return $zones_array;
    }



    public static function activate_license($purchase_code, $server_name) {

        $result = wp_remote_get("https://api.stachethemes.com/calendar/activate/{$purchase_code}/{$server_name}");
        $json   = json_decode($result['body']);

        if ( isset($json->success) && $json->success === 1 ) {
            $nfo = array(
                    'purchase_code' => $purchase_code,
                    'server_name'   => $server_name
            );
            update_option('stec_activated', $nfo);
            return true;
        }

        return false;
    }



    public static function deactivate_license($purchase_code, $server_name) {

        $result = wp_remote_get("https://api.stachethemes.com/calendar/deactivate/{$purchase_code}/{$server_name}");
        $json   = json_decode($result["body"]);
        if ( isset($json->success) && $json->success === 1 ) {
            delete_option('stec_activated');
            return true;
        }

        return false;
    }



    public static function calendar_writable_list() {
        $arr                   = self::get_user_roles_list();
        $arr['stec_private']   = 'Just Me';
        $arr['stec_logged_in'] = 'Logged in users';
        $arr['stec_public']    = 'Anyone';
        $arr['0']              = 'None';
        return apply_filters('stec_calendar_writable_list', array_reverse($arr));
    }



    public static function calendar_visibility_list() {
        $arr                   = self::get_user_roles_list();
        $arr['stec_private']   = 'Just Me';
        $arr['stec_logged_in'] = 'Logged in users';
        $arr['stec_public']    = 'Public';
        return apply_filters('stec_calendar_visibility_list', array_reverse($arr));
    }



    public static function event_visibility_list() {
        $arr                     = self::get_user_roles_list();
        $arr['stec_private']     = 'Just Me';
        $arr['stec_logged_in']   = 'Logged in users';
        $arr['stec_public']      = 'Public';
        $arr['stec_cal_default'] = 'Calendar Default';
        return apply_filters('stec_event_visibility_list', array_reverse($arr));
    }



    public static function get_user_roles_list() {

        require_once( ABSPATH . '/wp-admin/includes/user.php' );

        $roles = get_editable_roles();
        $roles = array_reverse($roles);
        $arr   = array();

        foreach ( $roles as $role => $details ) {
            $name                 = translate_user_role($details['name']);
            $arr[esc_attr($role)] = $name;
        }

        return apply_filters('stec_get_user_roles_list', $arr);
    }



    public static function get_user_roles($userid = null) {

        if ( null === $userid ) {
            $userid = get_current_user_id();
        }

        $userid = (int) $userid;

        $user_info    = get_userdata($userid);
        $user_roles   = isset($user_info->roles) ? $user_info->roles : array();
        $user_roles[] = 'stec_public';

        if ( is_user_logged_in() ) {
            $user_roles[] = 'stec_logged_in';
        }

        return $user_roles;
    }



    public static function get_plugin_version() {
        $plugin_data = get_plugin_data(STACHETHEMES_EC_FILE__);
        $version     = $plugin_data['Version'];
        return $version;
    }



    /**
     * Returns human readable format of the timespan
     * @param object $event the event object
     * @return string the timespan
     */
    public static function get_the_timespan($event, $repeat_offset = 0) {

        if ( !$event instanceof Event_Post ) {
            return '';
        }

        $dateformat      = Settings::get_admin_setting_value('stec_menu__general', 'date_format');
        $timeformat      = Settings::get_admin_setting_value('stec_menu__general', 'time_format');
        $show_utc_offset = Settings::get_admin_setting_value('stec_menu__general', 'date_label_gmtutc');
        $start_date_unix = strtotime($event->get_start_date()) + $repeat_offset;
        $end_date_unix   = strtotime($event->get_end_date()) + $repeat_offset;

        // Translate months
        $sm = '<span class="stec-layout-single-month-full">' . ucfirst(__(strtolower(date('F', $start_date_unix)), 'stec')) . '</span>';
        $em = '<span class="stec-layout-single-month-full">' . ucfirst(__(strtolower(date('F', $end_date_unix)), 'stec')) . '</span>';

        $sm .= '<span class="stec-layout-single-month-short">' . ucfirst(__(strtolower(date('M', $start_date_unix)), 'stec')) . '</span>';
        $em .= '<span class="stec-layout-single-month-short">' . ucfirst(__(strtolower(date('M', $end_date_unix)), 'stec')) . '</span>';

        switch ( $dateformat ) :

            case 'dd.mm.yyyy' :

                $start_date = '<span class="stec-layout-single-day">' . date('d', $start_date_unix) . '</span>.' .
                        $sm .
                        '.<span class="stec-layout-single-year">' . date('Y', $start_date_unix) . '</span>';

                $end_date = '<span class="stec-layout-single-day">' . date('d', $end_date_unix) . '</span>.' .
                        $sm .
                        '.<span class="stec-layout-single-year">' . date('Y', $end_date_unix) . '</span>';

                break;

            case 'dd-mm-yy' :

                $start_date = '<span class="stec-layout-single-day">' . date('d', $start_date_unix) . '</span> ' .
                        $sm .
                        ' <span class="stec-layout-single-year">' . date('Y', $start_date_unix) . '</span>';

                $end_date = '<span class="stec-layout-single-day">' . date('d', $end_date_unix) . '</span> ' .
                        $sm .
                        ' <span class="stec-layout-single-year">' . date('Y', $end_date_unix) . '</span>';
                break;

            case 'mm-dd-yy' :

                $start_date = $sm .
                        ' <span class="stec-layout-single-day">' . date('d', $start_date_unix) . '</span> ' .
                        ' <span class="stec-layout-single-year">' . date('Y', $start_date_unix) . '</span>';

                $end_date = $sm .
                        ' <span class="stec-layout-single-day">' . date('d', $end_date_unix) . '</span> ' .
                        ' <span class="stec-layout-single-year">' . date('Y', $end_date_unix) . '</span>';

                break;

            case 'yy-mm-dd' :

            default:
                $start_date = ' <span class="stec-layout-single-year">' . date('Y', $start_date_unix) . '</span>' .
                        $sm .
                        ' <span class="stec-layout-single-day">' . date('d', $start_date_unix) . '</span> ';

                $end_date = ' <span class="stec-layout-single-year">' . date('Y', $end_date_unix) . '</span>' .
                        $sm .
                        ' <span class="stec-layout-single-day">' . date('d', $end_date_unix) . '</span> ';

        endswitch;

        switch ( $timeformat ) :
            case '12':
                $timeformat = 'h:ia';
                break;

            case '24':
                $timeformat = 'H:i';
            default:

        endswitch;

        $start_time = '<span class="stec-layout-single-start-time">' . date($timeformat, $start_date_unix) . '</span>';
        $end_time   = '<span class="stec-layout-single-end-time">' . date($timeformat, $end_date_unix) . '</span>';

        if ( $start_date == $end_date ) {

            if ( $event->get_all_day() == '1' ) {
                $the_date = $start_date;
            } else {
                $the_date = "$start_date $start_time - $end_time";
            }
        } else {

            if ( $event->get_all_day() == '1' ) {
                $the_date = "$start_date - $end_date";
            } else {
                $the_date = "$start_date $start_time - $end_date $end_time";
            }
        }

        if ( $show_utc_offset == '1' ) {
            $the_date .= ' ' . '<span class="stec-layout-single-timezone">' . $event->get_timezone() . '</span>';
        }

        return $the_date;
    }



    public static function meta_schema_datetime_iso8601($event, $repeat_offset = 0) {

        if ( !$event instanceof Event_Post ) {
            return '';
        }


        $format          = 'Y-m-d\TH:i:sO';
        $start_date_unix = strtotime($event->get_start_date()) + $repeat_offset;
        $end_date_unix   = strtotime($event->get_end_date()) + $repeat_offset;
        $start           = date($format, $start_date_unix);
        $end             = date($format, $end_date_unix);
        ?>
        <meta itemprop="startDate" content="<?php echo $start; ?>"/>
        <meta itemprop="endDate" content="<?php echo $end; ?>"/>
        <?php
    }



    /**
     * Whether or not event has valid reminder
     * @param type $event
     * @return boolean
     */
    public static function reminder_expired($event, $repeat_offset = 0) {

        if ( !$event instanceof Event_Post ) {
            return true;
        }

        $start = strtotime($event->get_start_date()) + $repeat_offset;


        $utc_start = self::to_utc_time(date('Y-m-d H:i:s', $start), $event->get_timezone(), 'U');

        if ( gmdate('U') >= $utc_start ) {
            return true;
        }

        return false;
    }



    /**
     * 
     * @param DateTime $date Date object
     * @param type $timezone event timezone string
     * @param type $format time format
     * @return type UTC time string
     */
    public static function to_utc_time($date, $timezone, $format = 'Ymd\THis') {

        $UTC    = new DateTimeZone("UTC");
        $caltTZ = new DateTimeZone($timezone);

        $date = new DateTime($date, $caltTZ);
        $date->setTimezone($UTC);

        return $date->format($format);
    }



    public static function user_is_invited($event) {

        if ( !$event instanceof Event_Post ) {
            return true;
        }

        if ( is_user_logged_in() === false ) {
            return false;
        }

        $userid = get_current_user_id();

        foreach ( $event->get_attendance() as $attendee ) {

            if ( $attendee->userid == $userid ) {
                return true;
            }
        }

        return false;
    }



    public static function get_user_attendance_status($event, $userid = false, $repeat_offset = 0) {

        if ( !$event instanceof Event_Post ) {
            return 0;
        }

        if ( !$userid ) {
            $userid = get_current_user_id();
        }

        foreach ( $event->get_attendance() as $attendee ) {

            if ( $attendee->userid == $userid && isset($attendee->status[$repeat_offset]) ) {
                return $attendee->status[$repeat_offset];
            }
        }

        return 0;
    }



    public static function event_has_tabs($event) {

        if ( !$event instanceof Event_Post ) {
            return false;
        }

        $has_weather_api_key = Settings::get_admin_setting_value('stec_menu__general', 'weather_api_key');

        if (
                !($event->get_schedule()) ||
                !($event->get_guests()) ||
                !($event->get_attendance()) ||
                !($event->get_products()) ||
                ($event->get_location_forecast() != '' && $has_weather_api_key != '') ||
                $event->get_comments() != '0'
        ) {
            return true;
        }

        return false;
    }



    /**
     * Returns human readable format of the timespan
     */
    public static function get_the_schedule_timespan($date_string, $event, $repeat_offset = 0) {

        $dateformat = Settings::get_admin_setting_value('stec_menu__general', 'date_format');
        $timeformat = Settings::get_admin_setting_value('stec_menu__general', 'time_format');

        $start_date_unix = strtotime($date_string) + $repeat_offset;

        // Translate months
        $sm = '<span class="stec-layout-single-month-full">' . ucfirst(__(strtolower(date('M', $start_date_unix)), 'stec')) . '</span>';

        switch ( $dateformat ) :

            case 'dd-mm-yy' :

                $start_date = '<span class="stec-layout-single-day">' . date('d', $start_date_unix) . '</span> ' .
                        $sm;

                break;

            case 'mm-dd-yy' :
                $start_date = $sm .
                        ' <span class="stec-layout-single-day">' . date('d', $start_date_unix) . '</span> ';
                break;

            case 'yy-mm-dd' :
            default:
                $start_date = $sm . ' <span class="stec-layout-single-day">' . date('d', $start_date_unix) . '</span> ';
                break;
        endswitch;

        switch ( $timeformat ) :
            case '12':
                $timeformat = 'h:ia';
                break;

            case '24':
                $timeformat = 'H:i';
            default:

        endswitch;

        $start_time = date($timeformat, $start_date_unix);

        if ( $event->get_all_day() == '1' ) {
            $the_date = $start_date;
        } else {
            $the_date = "$start_date <span class='stec-layout-single-time'>$start_time</span>";
        }

        return $the_date;
    }



    public static function get_product_price($product) {

        $price = $product->html_price;

        if ( $product->has_child ) {
            $price .= '<a href="' . $product->url . '">' . __('(Select Options)', 'stec') . '</a>';
        }

        return $price;
    }



    public static function send_mail_invites(Event_Post $event) {

        $site_url     = site_url();
        $init_subject = __("You are invited to stec_replace_event_summary", 'stec');
        $init_message = __("You are invited to stec_replace_event_summary \n
        To accept invitation go to stec_replace_accept_url \n
        To decline invitation go to stec_replace_decline_url", 'stec');

        $init_subject = apply_filters('stec_mail_invite_subject', $init_subject);
        $init_message = apply_filters('stec_mail_invite_message', $init_message);

        $attendees = $event->get_attendance();

        foreach ( $attendees as &$attendee ) {

            if ( !$attendee instanceof Event_Meta_Attendee ) {
                continue;
            }

            $email = filter_var($attendee->email, FILTER_VALIDATE_EMAIL);

            if ( $attendee->mail_sent !== 0 ) {
                continue;
            }

            if ( !$email ) {

                if ( $attendee->userid ) {
                    $userdata = get_userdata($attendee->userid);
                    $email    = $userdata->user_email;
                }
            }

            if ( !$email ) {
                continue;
            }

            $subject     = $init_subject;
            $message     = $init_message;
            $accept_url  = $site_url . "?stec_attendance={$event->get_id()}&access_token={$attendee->access_token}&status=1";
            $decline_url = $site_url . "?stec_attendance={$event->get_id()}&access_token={$attendee->access_token}&status=2";

            $subject = str_replace('stec_replace_event_summary', $event->get_title(), $subject);
            $message = str_replace(
                    array('stec_replace_event_summary', 'stec_replace_accept_url', 'stec_replace_decline_url'), array($event->get_title(), $accept_url, $decline_url), $message
            );

            if ( $message ) {
                if ( wp_mail($email, $subject, $message) ) {
                    $attendee->mail_sent = 1;
                }
            }
        }

        update_post_meta($event->get_id(), 'attendance', '');
        update_post_meta($event->get_id(), 'attendance', $attendees);
    }



    public static function update_attendance_by_url($event_id, $access_token, $status) {

        $event = new Event_Post($event_id);

        if ( !$event->get_id() ) {
            return false;
        }

        $attendees = $event->get_attendance();

        foreach ( $attendees as &$attendee ) {

            if ( $attendee->access_token != $access_token ) {
                continue;
            }
        }

        $email  = filter_var($attendee->email, FILTER_VALIDATE_EMAIL);
        $params = array('repeat_offset' => 0);

        if ( $email ) {
            $params['email'] = $email;
        } elseif ( $attendee->userid ) {
            $params['userid'] = $attendee->userid;
        } else {
            return false;
        }

        return $event->set_attendee_status((int) $status, $params, true);
    }



    public function set_reminder($event_id, $repeat_offset, $email, $date) {

        if ( is_nan($event_id) ) {
            return array('error' => 1);
        }

        if ( is_nan($repeat_offset) ) {
            return array('error' => 1);
        }

        if ( !$email || filter_var($email, FILTER_VALIDATE_EMAIL) === false ) {
            return array('error' => 1);
        }

        if ( !$date ) {
            return array('error' => 1);
        }

        $new_remind = new Remind_Object($event_id, $repeat_offset, $email, $date);

        $reminders = get_option('stec_reminder', array());

        $exists = false;
        foreach ( $reminders as $reminder ) {

            if ( !$reminder instanceof Remind_Object ) {
                continue;
            }

            if ( $reminder->get_uid() == $new_remind->get_uid() ) {
                $exists = true;
                break;
            }
        }

        if ( true === $exists ) {
            return array('error' => 0);
        }

        array_push($reminders, $new_remind);

        update_option('stec_reminder', $reminders);

        return array('error' => 0);
    }



    /**
     * @return json
     */
    public static function get_weather_data($location) {

        $apikey = Settings::get_admin_setting_value('stec_menu__general', 'weather_api_key');
        $url    = "https://api.darksky.net/forecast/{$apikey}/" . urlencode($location);
        $data   = '';

        $cache = self::get_cache(array($apikey, $url), true);

        if ( $cache ) {
            return $cache;
        }

        $response = wp_remote_get($url);

        if ( is_array($response) ) {
            $data = ($response['body']);
            self::set_cache(array($apikey, $url), $data, 3600, true);
        }

        return $data;
    }



    public static function get_user_created_by_cookie() {

        $cookie_name = "stachethemes_ec_anon_user";

        return isset($_COOKIE[$cookie_name]) && strlen($_COOKIE[$cookie_name]) > 13 ? $_COOKIE[$cookie_name] : '';
    }



    public static function get_current_user() {

        $the_user = false;

        if ( is_user_logged_in() ) {
            $the_user = wp_get_current_user();
        }

        return $the_user;
    }



    public static function get_current_user_email() {
        $the_user = self::get_current_user();

        return $the_user && isset($the_user->user_email) ? $the_user->user_email : false;
    }



    /**
     * Send reminds
     * @param \Stachethemes\Stec\Remind_Object $remind
     * @return bool true if email is sent or remind is invalid or event doesn't exist
     */
    public static function send_mail_remind(Remind_Object $remind) {

        $data = unserialize($remind->get_uid());

        if ( !isset($data['eventid']) ) {
            return true; // consider task complete
        }

        $event = new Event_Post($data['eventid']);

        if ( !$event->get_id() ) {
            // Event does not exists consider task complete
            return true;
        }

        $email = $data['email'];

        if ( false === filter_var($email, FILTER_VALIDATE_EMAIL) ) {
            // Invalid e-mail consider task complete
            return true;
        }

        $gm_now         = gmdate(time());
        $gm_remind_date = strtotime(self::to_utc_time($remind['date'], $event->get_timezone()));

        if ( $gm_now < $gm_remind_date ) {
            // Not yet...
            return false;
        }

        $event_timespan = Admin_Helper::get_the_timespan($event, $data['repeat_offset']);
        $init_subject   = __('You have requested remind form stec_replace_event_summary', 'stec');
        $init_message   = __('You have requested remind for stec_replace_event_summary.\n\n'
                . 'Event date: stec_replace_event_timespan.\n'
                . 'For more info visit stec_replace_siteurl.', 'stec');

        $subject = apply_filters('stec_mail_remind_subject', $init_subject);
        $message = apply_filters('stec_mail_remind_message', $init_message);

        $subject = str_replace('stec_replace_event_summary', $event->get_title(), $subject);
        $message = str_replace(
                array('stec_replace_event_summary', 'stec_replace_event_timespan', 'stec_replace_siteurl'), array($event->get_title(), $event_timespan, $event->get_permalink($data['repeat_offset'])), $message
        );

        if ( $message ) {
            
            Admin_Helper::debug_log('Remind job sending email to ' . $email);
            
            if ( wp_mail($email, $subject, $message) ) {
                
                Admin_Helper::debug_log('Remind job email sent');
                
                return true;
                
            } else {
                
                Admin_Helper::debug_log('Remind job wp_mail failed!');
            }
        }

        return false;
    }



    private static function get_cache_string($request_data) {
        if ( $request_data instanceof WP_REST_Request ) {
            $cache_string = md5(serialize($request_data->get_params()));
        } else {
            $cache_string = md5(serialize($request_data));
        }

        return 'stec_transient_' . $cache_string;
    }



    // Is event cache enabled check
    private static function do_cache() {

        return Settings::get_admin_setting_value('stec_menu__cache', 'cache');
    }



    public static function get_cache($request_data, $force_get = false) {

        if ( false === $force_get && !self::do_cache() ) {
            return false;
        }

        $cache_string = self::get_cache_string($request_data);
        $cache        = get_transient($cache_string);

        return $cache;
    }



    public static function set_cache($request_data, $content, $time = 86400, $force_set = false) {

        if ( false === $force_set && !self::do_cache() ) {
            return false;
        }

        $cache_string = self::get_cache_string($request_data);

        return set_transient($cache_string, $content, $time);
    }



    public static function delete_cache() {
        global $wpdb;

        $wpdb->query("DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient%stec_transient_%')");
        $wpdb->query("DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_timeout%stec_transient_%')");
        wp_cache_flush();

        return true;
    }

}
