<?php
defined('ABSPATH') or die("No script kiddies please!");
/**
 * Adds AccessPress Social Login Widget
 */

class APSL_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
                'apsl_widget', // Base ID
                __('AccessPress Social Login', APSL_TEXT_DOMAIN ), // Name
                array('description' => __('AccessPress Social Login Widget', APSL_TEXT_DOMAIN )) // Args
        );
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance) {

        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = '';
        }

        

        if (isset($instance['theme'])) {
            $theme = $instance['theme'];
        } else {
            $theme = '';
        }

        if (isset($instance['login_text'])) {
            $login_text = $instance['login_text'];
        } else {
            $login_text = '';
        }

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title: ', APSL_TEXT_DOMAIN ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('login_text'); ?>"><?php _e( 'Login Text: ', APSL_TEXT_DOMAIN ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('login_text'); ?>" name="<?php echo $this->get_field_name('login_text'); ?>" type="text" value="<?php echo esc_attr($login_text); ?>">
        </p>

        

        <p>
            <label for="<?php echo $this->get_field_id('theme'); ?>"><?php _e('Theme: ', APSL_TEXT_DOMAIN ); ?></label> 
            <select name="<?php echo $this->get_field_name('theme'); ?>">
                <?php for($i=1; $i<=17; $i++){ ?>
                <option value="<?php echo $i; ?>" <?php selected( $theme, $i ); ?>><?php _e( $i, APSL_TEXT_DOMAIN ); ?></option>
                <?php } ?>
            </select>
        </p>

        <?php
    }

     /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        global $post;
        if(have_posts()){
            $widget_flag = get_post_meta($post->ID, 'apsl_widget_flag', true);
        }else{
            $widget_flag=0;
        }
        if($widget_flag !='1'){
        echo "<div class='apsl-widget'>";
        echo do_shortcode("[apsl-login theme='{$instance['theme']}' login_text='{$instance['login_text']}']");
        echo "</div>";
        }
        echo $args['after_widget'];
    }


    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
        $instance['login_text'] = (!empty($new_instance['login_text']) ) ? strip_tags($new_instance['login_text']) : '';
        $instance['theme'] = (!empty($new_instance['theme']) ) ? strip_tags($new_instance['theme']) : '';
        return $instance;
    }



}


class APSL_Widget_With_Login_Form extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
                'apsl_login_form_widget', // Base ID
                __('AccessPress Social Login with login form', APSL_TEXT_DOMAIN ), // Name
                array('description' => __('AccessPress Social Login with login form Widget', APSL_TEXT_DOMAIN )) // Args
        );
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance) {

        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = '';
        }

        if (isset($instance['template'])) {
            $template = $instance['template'];
        } else {
            $template = '';
        }

        if (isset($instance['theme'])) {
            $theme = $instance['theme'];
        } else {
            $theme = '';
        }

        if (isset($instance['login_text'])) {
            $login_text = $instance['login_text'];
        } else {
            $login_text = '';
        }

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title: ', APSL_TEXT_DOMAIN ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('login_text'); ?>"><?php _e( 'Login Text: ', APSL_TEXT_DOMAIN ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('login_text'); ?>" name="<?php echo $this->get_field_name('login_text'); ?>" type="text" value="<?php echo esc_attr($login_text); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('template'); ?>"><?php _e('Template: ', APSL_TEXT_DOMAIN ); ?></label> 
            <select name="<?php echo $this->get_field_name('template'); ?>">
                <?php for($i=1; $i<=4; $i++){ ?>
                <option value="<?php echo $i; ?>" <?php selected( $template, $i ); ?>><?php _e( $i, APSL_TEXT_DOMAIN ); ?></option>
                <?php } ?>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('theme'); ?>"><?php _e('Theme: ', APSL_TEXT_DOMAIN ); ?></label> 
            <select name="<?php echo $this->get_field_name('theme'); ?>">
                <?php for($i=1; $i<=17; $i++){ ?>
                <option value="<?php echo $i; ?>" <?php selected( $theme, $i ); ?>><?php _e( $i, APSL_TEXT_DOMAIN ); ?></option>
                <?php } ?>
            </select>
        </p>

        <?php
    }

     /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        global $post;
        if(have_posts()){
            $widget_flag = get_post_meta($post->ID, 'apsl_widget_flag', true);
        }else{
            $widget_flag=0;
        }
        if($widget_flag !='1'){
        echo "<div class='apsl-widget'>";
        echo do_shortcode("[apsl-login-with-login-form template='{$instance['template']}' theme='{$instance['theme']}' login_text='{$instance['login_text']}']");
        echo "</div>";
        }
        echo $args['after_widget'];
    }


    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
        $instance['login_text'] = (!empty($new_instance['login_text']) ) ? strip_tags($new_instance['login_text']) : '';
        $instance['theme'] = (!empty($new_instance['theme']) ) ? strip_tags($new_instance['theme']) : '';
        $instance['template'] = (!empty($new_instance['template']) ) ? strip_tags($new_instance['template']) : '';
        return $instance;
    }



}

?>