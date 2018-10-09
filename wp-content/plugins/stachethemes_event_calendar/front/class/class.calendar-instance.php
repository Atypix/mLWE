<?php

namespace Stachethemes\Stec;




/**
 * Front Calendar Class
 * Creates calendar instance. 
 * Used by [stachethemes_ec] shortcode
 */
class Calendar_Instance {



    private $atts    = array();
    private $options = array();



    /**
     * @param array $atts Shortcode custom attributes 
     */
    public function __construct($atts = array()) {

        /**
         * @todo get relevant data only? currently gets all general data
         * 
         */
        $this->atts = $atts;

        $general                   = Settings::get_admin_setting('stec_menu__general');
        $general['grid_gutter']    = Settings::get_admin_setting('stec_menu__fontsandcolors_grid', 'grid_gutter');
        $general['grid_columns']   = Settings::get_admin_setting('stec_menu__fontsandcolors_grid', 'grid_columns');
        $general['grid_per_click'] = Settings::get_admin_setting('stec_menu__fontsandcolors_grid', 'grid_per_click');

        $values = array();

        foreach ( $general as $g ) {

            // check if attribute is overrided from shortcode
            if ( isset($atts[$g["name"]]) ) {

                $values[$g["name"]] = $atts[$g["name"]];

                // don't duplicate in global if setting is in general scope
                unset($atts[$g["name"]]);
            } else {

                $values[$g["name"]] = $g["value"];
            }
        }

        $this->options["general_settings"]    = $values;
        $this->options["captcha"]['enabled']  = Settings::get_admin_setting_value('stec_menu__general_google_captcha', 'enabled');
        $this->options["captcha"]['site_key'] = Settings::get_admin_setting_value('stec_menu__general_google_captcha', 'site_key');
        $this->options["id"]                  = uniqid('stec-id-');
        $this->options["siteurl"]             = get_site_url();

        $writable_cal_list = Calendars::get_writable_calendar_list();

        if ( empty($writable_cal_list) ) {
            $this->options["general_settings"]["show_create_event_form"] = 0;
        }

        if ( is_user_logged_in() ) {
            $user                    = wp_get_current_user();
            $this->options['userid'] = $user->ID;
        }

        $this->_register_instance($atts);
    }



    /**
     * Return Calendar Id
     * @return string Calendar id
     */
    public function __toString() {
        return $this->options["id"];
    }



    /**
     * Get Calendar Options
     * @return array
     */
    public function get_options() {
        return $this->options;
    }



    /**
     * Overrides default options
     * @param array $new_options
     */
    private function _update_options($new_options = array()) {
        foreach ( $new_options as $key => $value ) {
            if ( $key == 'general_settings' ) {
                continue; // general settings are not overridable
            }
            $this->options[$key] = $value;
        }
    }



    /**
     * Registers calendar js instance
     * @param array $atts Shortcode custom attributes 
     */
    private function _register_instance($atts = array()) {

        // override options from shortcode attributes
        if ( !empty($atts) ) {
            $this->_update_options($atts);
        }

        $options = $this->get_options();

        // push instance to the js stachethemes_ec_instance array
        $json = json_encode($options);
        ?>
        <script type="text/javascript">
            if ( typeof stachethemes_ec_instance === "undefined" ) {
                var stachethemes_ec_instance = [];
            }
            stachethemes_ec_instance.push(<?php echo $json; ?>);
        </script>
        <?php
    }



    /**
     * Get overrided attribute from shortcode or return the admin setting
     * 
     * @global class $stachethemes_ec_main
     * @param string $page
     * @param string $att
     * @return type string
     */
    public function get_shortcode_option($page, $att) {

        $default = Settings::get_admin_setting_value($page, $att);

        return isset($this->atts[$att]) ? $this->atts[$att] : $default;
    }

}
