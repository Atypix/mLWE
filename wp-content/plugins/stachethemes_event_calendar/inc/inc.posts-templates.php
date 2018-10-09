<?php

namespace Stachethemes\Stec;




if ( !$this instanceof stachethemes_ec_main ) {
    die('File is loaded incorrectly!');
}

$stachethemes_ec_main = $this;

add_filter('single_template', function($single) use ($stachethemes_ec_main) {

    global $post;

    // If is not part of plugin single pages return normal
    if ( strpos($post->post_type, 'stec_') === false ) {
        return $single;
    }

    // Check if single saem file exists in the theme and return it
    if ( strpos($single, 'stec_') !== false ) {
        if ( file_exists($single) ) {
            return $single;
        }
    }

    switch ( $post->post_type ) {
        case 'stec_event' :
            
            add_action('wp_enqueue_scripts', function() use ($stachethemes_ec_main) {

                // calendar default fonts
                $stachethemes_ec_main->add_font("stec-google-fonts", "//fonts.googleapis.com/css?family=Roboto:300,400,500,700");
                $stachethemes_ec_main->add_font("stec-font-awesome", "font-awesome-4.5.0/css/font-awesome.css");
                $stachethemes_ec_main->add_front_css("stec-forecast-css", "forecast/forecast.css");

                // main stylesheet
                $stachethemes_ec_main->add_front_css("stec-single", "style.single.css");
                $stachethemes_ec_main->add_front_css("stec-single-media-med-css", "style.single.media-med.css");
                $stachethemes_ec_main->add_front_css("stec-single-media-small-css", "style.single.media-small.css");

                // less compiler
                // $stachethemes_ec_main->add_front_js("stec-less-js", "libs/less.js");
                // js

                if ( Settings::get_admin_setting_value('stec_menu__general_other', 'load_jmobile_js') == '1' ) {
                    $stachethemes_ec_main->add_front_js("stec-jquery-mobile-js", "libs/jquery.mobile.js", "jquery");
                }

                if ( Settings::get_admin_setting_value('stec_menu__general_other', 'load_gmaps') == '1' ) {
                    $stachethemes_ec_main->add_front_js("stec-google-maps", "//maps.googleapis.com/maps/api/js?key=" . Settings::get_admin_setting_value('stec_menu__general', 'gmaps_api_key'));
                }

                if ( Settings::get_admin_setting_value('stec_menu__general_other', 'load_chart_js') == '1' ) {
                    $stachethemes_ec_main->add_front_js("stec-chart", "libs/chart-2.2.1.min.js", "jquery");
                }

                if ( Settings::get_admin_setting_value('stec_menu__general_other', 'load_moment_js') == '1' ) {
                    $stachethemes_ec_main->add_front_js("stec-moment", "libs/moment.js", "jquery");
                }

                $stachethemes_ec_main->add_front_js("stec-single-js", "single/stec-single.js", "jquery");
                
                // Share and embeding
                $stachethemes_ec_main->add_front_js("stec-sharer-js", "share.js");

                $stachethemes_ec_main->load_head();
                $stachethemes_ec_main->load_js_locales();
            });

            return $stachethemes_ec_main->get_path('FRONT_VIEW') . 'single.php';


        default: return $single;
    }
});

