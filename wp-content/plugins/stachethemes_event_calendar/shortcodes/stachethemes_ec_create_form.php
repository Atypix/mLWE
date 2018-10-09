<?php

namespace Stachethemes\Stec;




add_shortcode('stachethemes_ec_create_form', function($atts = array()) {

    ob_start();

    $id                                      = uniqid('stec-create-form-');
    $stachethemes_ec_main                    = stachethemes_ec_main::get_instance();
    $atts['id']                              = $id;
    $atts["captcha"]['enabled']              = Settings::get_admin_setting_value('stec_menu__general_google_captcha', 'enabled');
    $atts["captcha"]['site_key']             = Settings::get_admin_setting_value('stec_menu__general_google_captcha', 'site_key');
    $atts["create_event_form_allow_expired"] = Settings::get_admin_setting_value('stec_menu__general', 'create_event_form_allow_expired');
    $is_single_form                          = true;
    $is_popup                                = isset($atts['selector']) && $atts['selector'] != '' ? true : false;
    $writable_cal_array                      = isset($atts['create_form_cal']) ? explode(',', $atts['create_form_cal']) : array();
    $json                                    = json_encode($atts);
    ?>

    <script type="text/javascript">
        if ( typeof stachethemes_ec_create_form_instance === "undefined" ) {
            var stachethemes_ec_create_form_instance = [];
        }
        stachethemes_ec_create_form_instance.push(<?php echo $json; ?>);
    </script>

    <?php
    // Before html hook
    do_action('stachethemes_ec_create_form_before_html');

    if ( $stachethemes_ec_main->lcns() !== false ) {
        include ($stachethemes_ec_main->get_path('FRONT_VIEW') . 'forms/create.form.php');
    } else {
        echo '<p>Stachethemes Event Calendar is not activated. To activate it go to <a href="' . get_admin_url(0, 'admin.php?page=stec_menu__license') . '">Dashboard -> Stachethemes Event Calendar -> Product License</a></p>';
    }

    // After html hook
    do_action('stachethemes_ec_create_form_after_html');

    return ob_get_clean();
});

add_action('wp_enqueue_scripts', function() {

    global $post;

    $stachethemes_ec_main = stachethemes_ec_main::get_instance();

    if ( $stachethemes_ec_main->scripts_are_forced() === true || (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'stachethemes_ec_create_form')) ) {

        if ( Settings::get_admin_setting_value('stec_menu__general_other', 'load_jquery_ui_css') == '1' ) {
            $stachethemes_ec_main->add_front_css("stec-ui", "//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css");
        }

        $stachethemes_ec_main->add_front_css("stec-colorpicker", "colorpicker/css/colorpicker.css");
        $stachethemes_ec_main->add_front_css("stec-create-form", "create-form.css");

        // calendar default fonts
        $stachethemes_ec_main->add_font("stec-google-fonts", "//fonts.googleapis.com/css?family=Roboto:300,400,500,700");
        $stachethemes_ec_main->add_font("stec-font-awesome", "font-awesome-4.5.0/css/font-awesome.css");

        // less compiler
        // $stachethemes_ec_main->add_front_js("stec-less-js", "libs/less.js");

        if ( Settings::get_admin_setting_value('stec_menu__general_google_captcha', 'enabled') == '1' ) {
            $stachethemes_ec_main->add_front_js("stec-google-captcha-api", "https://www.google.com/recaptcha/api.js?render=explicit async defer");
        }

        if ( Settings::get_admin_setting_value('stec_menu__general_other', 'load_jmobile_js') == '1' ) {
            $stachethemes_ec_main->add_front_js("stec-jquery-mobile-js", "libs/jquery.mobile.js", "jquery");
        }

        $stachethemes_ec_main->add_front_js("stec-rrule-js", "libs/rrule.js");
        $stachethemes_ec_main->add_front_js("stec-nlp-js", "libs/nlp.js", "stec-rrule-js");
        $stachethemes_ec_main->add_front_js("stec-colorpicker-js", "libs/colorpicker/colorpicker.js", 'jquery');

        if ( Settings::get_admin_setting_value('stec_menu__general_other', 'load_moment_js') == '1' ) {
            $stachethemes_ec_main->add_front_js("stec-moment-js", "libs/moment.js");
        }

        $stachethemes_ec_main->add_front_js("stec-create-form-js", "create_form.js", "jquery,stec-rrule-js,jquery-ui-datepicker");

        $stachethemes_ec_main->load_js_locales();

        add_action('wp_head', function() use ($stachethemes_ec_main) {
            $stachethemes_ec_main->load_head();
        }, 9999);
    }
});

