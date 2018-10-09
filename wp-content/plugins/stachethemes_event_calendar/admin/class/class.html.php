<?php

namespace Stachethemes\Stec;




class Admin_Html {



    public static function html_color($name, $value, $default = '', $desc = '') {

        $val = $value ? $value : $default;

        echo "<input class='stachethemes-admin-colorpicker' name='{$name}' value='{$val}' autocomplete='off' title='{$desc}' />";
    }



    public static function html_info($text) {
        if ( $text == '' ) {
            return;
        }
        echo "<p class='stachethemes-admin-info'>{$text}</p>";
    }



    public static function html_input($name, $value, $default = "", $placeholder = "", $required = false, $type = "text", $custom = '') {

        $val = $value ? esc_html($value) : $default;
        $req = $required ? 'required="required"' : '';

        echo "<input class='stachethemes-admin-input' type='{$type}' name='{$name}' placeholder='{$placeholder}' value='{$val}' $req $custom />";
    }



    public static function html_checkbox($name, $checked, $default = false, $label = false, $required = false) {

        $id = 'id-' . uniqid();

        $checked = $checked ? 'checked' : $default;
        $req     = $required ? 'required="required"' : '';

        echo "<input id='$id' class='stachethemes-admin-checkbox' type='checkbox' name='{$name}' $checked $req />";

        if ( $label ) {
            echo "<label for='$id' class='stachethemes-admin-checkbox-label'>$label</label>";
        }
    }



    public static function html_radio($name, $checked, $default = false, $label = false, $required = false) {

        $id = 'id-' . uniqid();

        $checked = $checked ? 'checked' : $default;
        $req     = $required ? 'required="required"' : '';

        echo "<input id='$id' class='stachethemes-admin-checkbox' type='radio' name='{$name}' $checked $req />";

        if ( $label ) {
            echo "<label for='$id' class='stachethemes-admin-checkbox-label'>$label</label>";
        }
    }



    public static function html_date($name, $value, $default = "", $placeholder = "", $required = false, $type = "text") {

        $val = $value ? $value : $default;
        $req = $required ? 'required="required"' : '';

        echo "<input class='stachethemes-admin-input input-date' type='{$type}' name='{$name}' placeholder='{$placeholder}' value='{$val}' $req />";
    }



    public static function html_hidden($name, $value) {
        echo "<input type='hidden' name='{$name}' value='{$value}' />";
    }



    public static function html_textarea($name, $value, $default = "", $placeholder = "", $required = false, $custom = '') {

        $val = $value ? $value : $default;
        $req = $required ? 'required="required"' : '';

        echo "<textarea class='stachethemes-admin-textarea' name='{$name}' placeholder='{$placeholder}' $req $custom>{$val}</textarea>";
    }



    /**
     * @todo Graphic implementation
     */
    public static function html_icon($name, $list, $default = "", $required = false) {
        return self::html_select($name, $list, $default, $required);
    }



    public static function html_select($name, $options, $default = "", $required = false) {

        $req  = $required ? 'required="required"' : '';
        $html = "<select class='stachethemes-admin-select' name='{$name}' $req autocomplete='off'>";

        foreach ( $options as $val => $name ) :
            $sel  = $default == $val ? 'selected="selected"' : "";
            $html .= "<option value='{$val}' {$sel}>{$name}</option>";
        endforeach;

        $html .= "</select>";

        echo $html;
    }



    public static function html_button($text, $href = false, $noclear = false, $customclass = "") {

        if ( $noclear === true ) {
            $noclear = 'stachethemes-admin-button-no-clear';
        }

        if ( $href ) {
            echo "<a class='stachethemes-admin-button-a {$noclear} {$customclass}' href='{$href}' >{$text}</a>";
        } else {
            echo "<button class='stachethemes-admin-button {$noclear} {$customclass}'>{$text}</button>";
        }
    }



    public static function html_form_start($action = "/", $method = "POST", $formdata = false) {

        $enctype = $formdata ? 'enctype="multipart/form-data"' : '';

        echo "<form class='stachethemes-admin-form' method='{$method}' action='{$action}' {$enctype}>";

        wp_nonce_field($action, 'stec_admin_form_nonce');
    }



    public static function html_form_end() {

        echo "</form>";
    }



    public static function html_add_image($name, $default = false, $title = "Add Image", $required = false, $single = false) {

        $req = $required ? 'required="required"' : '';

        echo "<ul class='stachethemes-admin-add-image-list' data-single='{$single}' data-name='{$name}' $req>";

        if ( $default ) :

            if ( !is_array($default) ) {
                $default = explode(',', $default);
            }

            foreach ( $default as $image_id ) :
                $image_source = wp_get_attachment_image_src($image_id, "medium");
                ?>
                <li class="stachethemes-admin-image-container"><i class="fa fa-times"></i>
                    <img alt="" src="<?php echo $image_source[0]; ?>" data-id="<?php echo $image_id; ?>"><input type="hidden" value="<?php echo $image_id; ?>" name="<?php echo $name; ?>">
                </li>
                <?php
            endforeach;

        endif;

        echo "</ul>";

        echo "<button data-single='{$single}' data-name='{$name}' class='stachethemes-admin-add-image'>{$title}</button>";
    }



    private static function setting_to_html($setting) {

        $setting = (object) $setting;

        switch ( $setting->type ) :

            case 'checkbox' :

                self::html_checkbox($setting->name, $setting->value, $setting->default, $setting->title, $setting->req);

                break;

            case 'input' :

                self::html_info($setting->title);
                self::html_input($setting->name, $setting->value, $setting->default, "", $setting->req);

                break;

            case 'textarea' :

                self::html_info($setting->title);
                self::html_textarea($setting->name, $setting->value, $setting->default, $placeholder = "", $setting->req);

                break;

            case 'select' :

                self::html_info($setting->title);
                self::html_select($setting->name, $setting->select, $setting->value, $setting->req);

                break;

            case 'color' :

                self::html_info($setting->title);
                self::html_color($setting->name, $setting->value, $setting->default, $setting->desc);

                break;

            case 'font' :

                $fontsizes = array();
                for ( $i = 9; $i <= 100; $i++ ) {
                    $fontsizes["{$i}px"] = "{$i}px";
                }

                $fontweights = array();
                for ( $i = 1; $i <= 9; $i++ ) {
                    $fontweights[$i * 100] = $i * 100;
                }

                $fontlineheights = array();
                $lh              = 0.9;
                while ( $lh < 2 ) {
                    $lh                     = $lh + 0.1;
                    $fontlineheights["$lh"] = $lh;
                }

                self::html_info($setting->title);
                echo '<div class="stachethemes-admin-section-flex">';
                self::html_input($setting->name . '_font_family', $setting->value[0], $setting->default[0], "Font-Family", false);
                self::html_select($setting->name . '_font_weight', $fontweights, $setting->value[1], $setting->req);
                self::html_select($setting->name . '_font_size', $fontsizes, $setting->value[2], $setting->req);
                if ( isset($setting->value[3]) && $setting->value[3] != "" ) {
                    self::html_select($setting->name . '_font_lineheight', $fontlineheights, $setting->value[3], $setting->req);
                }
                echo '</div>';
                break;

        endswitch;
    }



    /**
     * Build html settings from given $page and $name settings
     */
    public static function build_settings_html($page, $name = false) {

        $setting = Settings::get_admin_setting($page, $name);

        if ( $setting === null || empty($setting) ) {
            return;
        }

        if ( $name === false ) {
            foreach ( $setting as $single ) {
                self::setting_to_html($single);
            }
        } else {
            self::setting_to_html($setting);
        }
    }

}
