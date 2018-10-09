<?php

namespace Stachethemes\Stec;




add_shortcode('stachethemes_ec', function($atts = array()) {

    ob_start();

    $stachethemes_ec_main = stachethemes_ec_main::get_instance();

    $calendar           = new Calendar_Instance($atts);
    $writable_cal_array = isset($atts['create_form_cal']) ? explode(',', $atts['create_form_cal']) : array();

    // Before html hook
    do_action('stachethemes_ec_before_html');

    if ( $stachethemes_ec_main->lcns() !== false ) {
        include ($stachethemes_ec_main->get_path('FRONT_VIEW') . 'default.php');
    } else {
        echo '<p>Stachethemes Event Calendar is not activated. To activate it go to <a href="' . get_admin_url(0, 'admin.php?page=stec_menu__license') . '">Dashboard -> Stachethemes Event Calendar -> Product License</a></p>';
    }

    // After html hook
    do_action('stachethemes_ec_after_html');

    return ob_get_clean();
});

add_action('wp_enqueue_scripts', function() {

    global $post;

    $stachethemes_ec_main = stachethemes_ec_main::get_instance();

    if ( $stachethemes_ec_main->scripts_are_forced() === true || (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'stachethemes_ec')) ) {

        if ( Settings::get_admin_setting_value('stec_menu__general_other', 'load_jquery_ui_css') == '1' ) {
            $stachethemes_ec_main->add_front_css("stec-ui", "//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css");
        }
        
        $stachethemes_ec_main->add_front_css("stec-forecast-css", "forecast/forecast.css");
        $stachethemes_ec_main->add_front_css("stec-css", "style.css");
        $stachethemes_ec_main->add_front_css("stec-animate-css", "animate.css");
        $stachethemes_ec_main->add_front_css("stec-media-med-css", "style.media-med.css");
        $stachethemes_ec_main->add_front_css("stec-media-small-css", "style.media-small.css");
        $stachethemes_ec_main->add_front_css("stec-colorpicker", "colorpicker/css/colorpicker.css");

        // calendar default fonts
        $stachethemes_ec_main->add_font("stec-google-fonts", "//fonts.googleapis.com/css?family=Roboto:300,400,500,700");
        $stachethemes_ec_main->add_font("stec-font-awesome", "font-awesome-4.5.0/css/font-awesome.css");

        // less compiler
        // $stachethemes_ec_main->add_front_js("stec-less-js", "libs/less.js");

        if ( Settings::get_admin_setting_value('stec_menu__general_google_captcha', 'enabled') == '1' ) {
            $stachethemes_ec_main->add_front_js("stec-google-captcha-api", "https://www.google.com/recaptcha/api.js?render=explicit async defer");
        }

        if ( Settings::get_admin_setting_value('stec_menu__general_other', 'load_gmaps') == '1' ) {
            $stachethemes_ec_main->add_front_js("stec-google-maps", "//maps.googleapis.com/maps/api/js?key=" . Settings::get_admin_setting_value('stec_menu__general', 'gmaps_api_key'));
        }


        if ( Settings::get_admin_setting_value('stec_menu__general_other', 'load_jmobile_js') == '1' ) {
            $stachethemes_ec_main->add_front_js("stec-jquery-mobile-js", "libs/jquery.mobile.js", "jquery");
        }

        $stachethemes_ec_main->add_front_js("stec-touchpunch", "libs/touch-punch.js", "jquery,jquery-ui-draggable");

        if ( Settings::get_admin_setting_value('stec_menu__general_other', 'load_chart_js') == '1' ) {
            $stachethemes_ec_main->add_front_js("stec-chart", "libs/chart-2.2.1.min.js", "jquery");
        }

        if ( Settings::get_admin_setting_value('stec_menu__general_other', 'load_moment_js') == '1' ) {
            $stachethemes_ec_main->add_front_js("stec-moment-js", "libs/moment.js");
        }

        $stachethemes_ec_main->add_front_js("stec-rrule-js", "libs/rrule.js");
        $stachethemes_ec_main->add_front_js("stec-nlp-js", "libs/nlp.js", "stec-rrule-js");

        $stachethemes_ec_main->add_front_js("stec-animate-js", "animate.js", "jquery");
        $stachethemes_ec_main->add_front_js("stec-js", "stec.js", "jquery,jquery-ui-draggable", "stec-rrule-js");

        /**
         * Add Inner tabs javascript
         */
        $stachethemes_ec_main->add_front_js("stec-extend", "stec-extend.js", "stec-js");
        $stachethemes_ec_main->add_front_js("search-js", "adds/search.js", "stec-extend");
        $stachethemes_ec_main->add_front_js("top-calfilter-js", "adds/top.calfilter.js", "stec-extend");
        $stachethemes_ec_main->add_front_js("tooltip-js", "adds/tooltip.js", "stec-extend");
        $stachethemes_ec_main->add_front_js("media-js", "adds/media.js", "stec-extend");
        $stachethemes_ec_main->add_front_js("intro-js", "adds/intro.js", "stec-extend");
        $stachethemes_ec_main->add_front_js("location-js", "adds/location.js", "stec-extend");
        $stachethemes_ec_main->add_front_js("schedule-js", "adds/schedule.js", "stec-extend");
        $stachethemes_ec_main->add_front_js("guests-js", "adds/guests.js", "stec-extend");
        $stachethemes_ec_main->add_front_js("attendance-js", "adds/attendance.js", "stec-extend");
        $stachethemes_ec_main->add_front_js("comments-js", "adds/comments.js", "stec-extend");
        $stachethemes_ec_main->add_front_js("forecast-js", "adds/forecast.js", "stec-extend");

        // if woocommerce is running
        if ( class_exists('WooCommerce') ) {
            $stachethemes_ec_main->add_front_js("woocommerce-js", "adds/woocommerce.js", "stec-extend");
        }

        // Front Event Creation
        $stachethemes_ec_main->add_front_js("stec-colorpicker-js", "libs/colorpicker/colorpicker.js", 'jquery');
        $stachethemes_ec_main->add_front_js("stec-event-create-js", "adds/event.create.js", "stec-extend,jquery-ui-datepicker");
        
        // Share and embeding
        $stachethemes_ec_main->add_front_js("stec-sharer-js", "share.js");
        
        $stachethemes_ec_main->load_js_locales();

        add_action('wp_head', function() use ($stachethemes_ec_main) {
            $stachethemes_ec_main->load_head();
        }, 9999);
    }
});