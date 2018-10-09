<?php

namespace Stachethemes\Stec;




class Settings {



    protected static $_ras_ai = 0;



    /**
     * Resets admin settings from this $page
     * @see register-settings.php
     */
    public static function reset_admin_settings($page) {

        $settings = self::get_admin_setting($page);

        foreach ( $settings as $k => $s ) :

            $s = (object) $s;

            switch ( $s->type ) :

                case 'font' :

                    $settings[$k]["value"] = array(
                            $s->default[0],
                            $s->default[1],
                            $s->default[2],
                            isset($s->default[3]) && $s->default[3] != "" ? $s->default[3] : false
                    );

                    break;

                default:

                    $settings[$k]["value"] = $s->default;

            endswitch;

        endforeach;

        update_option($page, $settings);
    }



    public static function get_admin_settings_kv($page) {

        $settings = self::get_admin_setting($page);

        foreach ( $settings as $k => $v ) {
            $settings[$k] = $v['value'];
        }

        return $settings;
    }



    public static function get_admin_setting_value($page, $name) {
        $setting = get_option($page, array());

        return isset($setting[$name]["value"]) ? $setting[$name]["value"] : null;
    }



    /**
     * Get admin setting array by $page and or $name
     * @param string $page 
     * @param string $name
     * @return array
     */
    public static function get_admin_setting($page, $name = false) {

        $setting = get_option($page, array());

        if ( $name === false ) {
            return $setting;
        }

        return !empty($setting) && isset($setting[$name]) ? $setting[$name] : null;
    }



    /**
     * Updates all settings from this $page 
     * @see register-settings.php
     */
    public static function update_admin_settings($page) {

        $settings = self::get_admin_setting($page);

        foreach ( $settings as $k => $s ) :

            $s = (object) $s;

            switch ( $s->type ) :

                case 'font' :

                    $settings[$k]["value"] = array(
                            Admin_Helper::post($s->name . '_font_family', ''),
                            Admin_Helper::post($s->name . '_font_weight', ''),
                            Admin_Helper::post($s->name . '_font_size', ''),
                            Admin_Helper::post($s->name . '_font_lineheight', false)
                    );

                    break;

                case 'checkbox' :

                    $settings[$k]["value"] = Admin_Helper::post($s->name) ? 1 : null;

                    break;

                default:

                    $settings[$k]["value"] = Admin_Helper::post($s->name, '');

            endswitch;

        endforeach;

        update_option($page, $settings);
    }



    public static function import_settings($file) {

        $content = file_get_contents($file);
        $content = unserialize($content);

        foreach ( $content as $page_settings ) {

            $page_name         = $page_settings[key($page_settings)]['page'];
            $current_setttings = get_option($page_name, array());

            foreach ( $page_settings as $key => $page_setting ) {

                if ( !isset($current_setttings[$key]) ) {
                    continue;
                }

                $current_setttings[$key] = $page_setting;
            }

            update_option($page_name, $current_setttings);
        }


        return true;
    }



    public static function export_settings() {


        $filename = 'STEC Settings ' . date('Y-M-j h-i');

        header('Content-type: text/plain; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"" . $filename . ".stec\"");

        $pages = array(
                'stec_menu__general',
                'stec_menu__general_other',
                'stec_menu__general_google_captcha',
                'stec_menu__fontsandcolors_top',
                'stec_menu__fontsandcolors_monthweek',
                'stec_menu__fontsandcolors_day',
                'stec_menu__fontsandcolors_preview',
                'stec_menu__fontsandcolors_event',
                'stec_menu__fontsandcolors_tooltip',
                'stec_menu__fontsandcolors_agenda');

        $content = array();

        foreach ( $pages as $page ) {
            $content[] = self::get_admin_setting($page);
        }

        echo serialize($content);

        exit();
    }



    /**
     * Registers admin setting
     * @param type $setting
     * @return boolean
     */
    public static function register_admin_setting($setting = false) {

        if ( !is_array($setting) ) {
            return false;
        }

        // page and name cannot be undefined or empty
        if ( !isset($setting["page"]) || !isset($setting["name"]) || $setting["page"] == "" || $setting["name"] == "" ) {
            return false;
        }

        // default key, vals
        $setting_default_values_template = array(
                "page"    => "",
                "title"   => "",
                "desc"    => "",
                "name"    => "",
                "type"    => "input",
                "select"  => array(),
                "value"   => "",
                "default" => "",
                "req"     => false,
                "css"     => false,
                "ai"      => self::$_ras_ai++
        );

        // fill empty keys if any
        foreach ( $setting_default_values_template as $k => $v ) {
            if ( !isset($setting[$k]) ) {
                $setting[$k] = $v;
            }
        }

        // check if setting value already exists
        $exists = self::get_admin_setting_value($setting["page"], $setting["name"]);

        if ( $exists !== null ) {

            $setting["value"] = $exists;
        }

        $page_settings                   = get_option($setting["page"], array());
        $page_settings[$setting["name"]] = $setting;

        $page_settings = self::reorder_admin_settings($page_settings);

        update_option($setting["page"], $page_settings);
    }



    public static function reorder_admin_settings($page_settings) {

        uasort($page_settings, function($a, $b) {

            if ( isset($a['ai']) && isset($b['ai']) ) {
                return $a['ai'] > $b['ai'];
            }

            return 0;
        });

        return $page_settings;
    }



    public static function delete_admin_settings($page, $name_array) {

        $settings = self::get_admin_setting($page);

        foreach ( $settings as $s ) :

            $s = (object) $s;

            foreach ( $name_array as $name ) {

                if ( $s->name == $name ) {

                    unset($settings[$name]);
                }
            }

        endforeach;

        update_option($page, $settings);
    }



    /**
     * Converts settings to style if setting has css key
     * @see register-settings.php
     */
    public static function get_style_from_setting($page, $important = null) {

        $php_eol = '';

        $important = $important == 1 ? '!important' : '';

        $page = self::get_admin_setting($page);

        foreach ( $page as $element ) :
            if ( isset($element["css"]) && is_array($element["css"]) ) :
                foreach ( $element["css"] as $css ) :
                    $css = explode("||", $css);
                    if ( trim($css[0]) != 'font' ) {
                        echo $css[1] . " { $css[0]: {$element['value']} $important; } " . $php_eol;
                    } else {
                        echo $css[1] . "{ font-family: {$element['value'][0]} $important; } " . $php_eol;
                        echo $css[1] . "{ font-weight: {$element['value'][1]} $important; } " . $php_eol;
                        echo $css[1] . "{ font-size: {$element['value'][2]} $important; } " . $php_eol;
                        if ( isset($element['value'][3]) && $element['value'][3] != '' ) {
                            echo $css[1] . "{ line-height: {$element['value'][3]} $important; } " . $php_eol;
                        }
                    }
                endforeach;
            endif;
        endforeach;
    }

}
