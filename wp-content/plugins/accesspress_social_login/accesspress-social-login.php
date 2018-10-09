<?php

defined( 'ABSPATH' ) or die( "No script kiddies please!" );

/*

  Plugin name: AccessPress Social Login

  Plugin URI: https://accesspressthemes.com/wordpress-plugins/accesspress-social-login/

  Description: A plugin to add various social logins to a site.

  version: 1.2.9

  Author: AccessPress Themes

  Author URI: https://accesspressthemes.com/

  Text Domain: apsl

  Domain Path: /languages/

  License: GPLv2 or later

*/



//Declearation of the necessary constants for plugin

if( !defined( 'APSL_VERSION' ) ) {

    define( 'APSL_VERSION', '1.2.9' );

}



if( !defined( 'APSL_IMAGE_DIR' ) ) {

    define( 'APSL_IMAGE_DIR', plugin_dir_url( __FILE__ ) . 'images' );

}



if( !defined( 'APSL_JS_DIR' ) ) {

    define( 'APSL_JS_DIR', plugin_dir_url( __FILE__ ) . 'js' );

}



if( !defined( 'APSL_CSS_DIR' ) ) {

    define( 'APSL_CSS_DIR', plugin_dir_url( __FILE__ ) . 'css' );

}



if( !defined( 'APSL_LANG_DIR' ) ) {

    define( 'APSL_LANG_DIR', basename( dirname( __FILE__ ) ) . '/languages/' );

}



if( !defined( 'APSL_TEXT_DOMAIN' ) ) {

    define( 'APSL_TEXT_DOMAIN', 'apsl' );

}



if( !defined( 'APSL_SETTINGS' ) ) {

    define( 'APSL_SETTINGS', 'apsl-settings' );

}



if( !defined( 'APSL_PLUGIN_DIR' ) ) {

    define( 'APSL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

}



/**

 * Register a widget

 *

 */

include_once( 'inc/backend/widget.php' );

include_once( 'inc/frontend/apsl-login_check_class.php' );





// Redefine user notification function



if (version_compare(get_bloginfo('version'), '4.3.1', '>=')){

    // for wordpress version greater than or equal to 4.3.1

    if ( !function_exists('wp_new_user_notification') ) {



        function wp_new_user_notification( $user_id, $deprecated = null, $notify = 'both' ) {



        $options = get_option( APSL_SETTINGS );



        if ( $deprecated !== null ) {

            _deprecated_argument( __FUNCTION__, '4.3.1' );

        }



        if( isset( $options['apsl_email_sender_email'] ) && $options['apsl_email_sender_email'] != '' ) {

                $sender_email_address = $options['apsl_email_sender_email'];

        }

        else {

            $sender_email_address = get_option( 'admin_email' );

        }



        $email_body = $options['apsl_email_body'];



        global $wpdb, $wp_hasher;

        $user = get_userdata( $user_id );

        if ( empty ( $user ) )

            return;



        // The blogname option is escaped with esc_html on the way into the database in sanitize_option

        // we want to reverse this for the plain text arena of emails.

        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);



        $message  = sprintf(__('New user registration on your site %s:'), $blogname) . "<br />";

        $message .= sprintf(__('Username: %s'), $user->user_login) . "<br />";

        $message .= sprintf(__('E-mail: %s'), $user->user_email) . "<br />";

        $message .= "<br /><br />";

        $message .= 'Thank you';

        

        $headers = 'Content-type: text/html' . "\r\n" . 'From:' . get_option( 'blogname' ) . ' <' . $sender_email_address . '>' . "\r\n" . 'X-Mailer: PHP/' . phpversion();

        

        add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));

        @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message, $headers);



        if ( 'admin' === $notify || empty( $notify ) ) {

            return;

        }



        // Generate something random for a password reset key.

        $key = wp_generate_password( 20, false );



        /** This action is documented in wp-login.php */

        do_action( 'retrieve_password_key', $user->user_login, $key );



        // Now insert the key, hashed, into the DB.

        if ( empty( $wp_hasher ) ) {

            require_once ABSPATH . WPINC . '/class-phpass.php';

            $wp_hasher = new PasswordHash( 8, true );

        }

        $hashed = time() . ':' . $wp_hasher->HashPassword( $key );

        $wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );

        $password_set_link = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login');

        $patterns = array('/#blogname/', '/#username/', '/#password_set_link/');

        $replacements = array(get_option('blogname'), $user->user_login, $password_set_link);

        $message = preg_replace($patterns, $replacements, $email_body);

        $headers = "MIME-Version: 1.0\r\n" . "From: " . get_option( 'blogname' ) . " " . "<" . $sender_email_address . ">\n" . "Content-Type: text/HTML; charset=\"" . get_option('blog_charset') . "\"\r\n";

        

        add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));

        wp_mail($user->user_email, sprintf(__('[%s] Your username and password info'), $blogname), $message, $headers );

        }

    }

}else{

    // for wordpress version less than 4.3.1

    if( !function_exists( 'wp_new_user_notification' ) ) {

    

        function wp_new_user_notification( $user_id, $plaintext_pass = '' ) {

        $options = get_option( APSL_SETTINGS );

        

        if( isset( $options['apsl_email_sender_email'] ) && $options['apsl_email_sender_email'] != '' ) {

            $sender_email_address = $options['apsl_email_sender_email'];

        } 

        else {

            $sender_email_address = get_option( 'admin_email' );

        }

        

        $email_body = $options['apsl_email_body'];

        

        $user = new WP_User( $user_id );

        

        $user_login = stripslashes( $user->user_login );

        $user_email = stripslashes( $user->user_email );

   $subject = "Terminez votre incription sur myLittleWE !";
    $message = get_header_email("Il reste une dernière broutille !");     

    $message .= '
<strong>Ce n\'est qu\'un détail, mais il faut le faire !</strong>
<br/><br/>

Nous vous invitons à créer un mot de passe en <a target="_blank" href="https://www.mylittlewe.com/my-account/edit-account/">cliquant ici !</a>
<br/>
<br/>
L\'équipe de myLittleWE vous remercie pour votre contribution.
<br/><br/><br/>
</div></td>
</tr>
</tbody>
</table>
<!-- End Content --></td>
</tr>
</tbody>
</table>
<!-- End Body --></td>
</tr>
<tr>
<td align="center" valign="top"><!-- Footer -->
<table id="template_footer" border="0" width="600" cellspacing="0" cellpadding="10">
<tbody>
<tr>
<td valign="top">
<table border="0" width="100%" cellspacing="0" cellpadding="10">
<tbody>
<tr>
<td id="credit" style="padding: 0 48px 48px 48px; border: 0; color: #c6acc8; font-family: Arial; font-size: 12px; line-height: 125%; text-align: center;" colspan="2" valign="middle">myLittleWE</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<!-- End Footer --></td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</div>';

$message .= get_footer_email ();





        

        $headers = 'Content-type: text/html' . "\r\n" . 'From:' . get_option( 'blogname' ) . ' <' . $sender_email_address . '>' . "\r\n" . 'X-Mailer: PHP/' . phpversion();

        

        add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));

        @wp_mail( get_option( 'admin_email' ), sprintf( __( '[%s] New User Registration' ), get_option( 'blogname' ) ), $message, $headers );

        

        if( empty( $plaintext_pass ) )return;

        $patterns = array('/#blogname/', '/#username/', '/#password/');

        $replacements = array(get_option('blogname'), $user->user_login, $plaintext_pass);

        $message = preg_replace($patterns, $replacements, $email_body);

        $headers = 'Content-type: text/html' . "\r\n" . 'From:' . get_option( 'blogname' ) . ' <' . $sender_email_address . '>' . "\r\n" . 'X-Mailer: PHP/' . phpversion();

        

        add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
        $headers = array('Content-Type: text/html; charset=UTF-8','From: myLittleWE <infos@mylittlewe.com>');
      

      

        @wp_mail( $user_email, $subject, get_option( 'blogname' ) , $message, $headers );

        }

    }

}



// Declaration of the class

if( !class_exists( 'APSL_Class' ) ) {

    

    class APSL_Class

    {

        

        var $apsl_settings;

        

        function __construct() {

            $this->apsl_settings = get_option( APSL_SETTINGS );

            add_action( 'init', array($this, 'session_init') );

             //start the session if not started yet.

            add_action( 'template_redirect', array($this, 'check_login_data') );

            add_action( 'template_redirect', array($this, 'check_user_data') );

            add_action( 'template_redirect', array($this, 'allow_user_enter_data') );

            register_activation_hook( __FILE__, array($this, 'plugin_activation') );

             //load the default setting for the plugin while activating

            add_action( 'init', array($this, 'plugin_text_domain') );

             //load the plugin text domain

            add_action( 'admin_menu', array($this, 'add_apsl_menu') );

             //register the plugin menu in backend

            add_action( 'admin_enqueue_scripts', array($this, 'register_admin_assets') );

             //registers all the assets required for wp-admin

            add_action( 'wp_enqueue_scripts', array($this, 'register_frontend_assets') );

             // registers all the assets required for the frontend

            add_action( 'admin_post_apsl_save_options', array($this, 'save_settings') );

             //save settings of a plugin



            if(isset($this->apsl_settings['apsl_enable_disable_edd_checkout']) && $this->apsl_settings['apsl_enable_disable_edd_checkout'] == 'yes'){

                add_action('edd_purchase_form_before_register_login', array($this, 'add_social_login_form_to_comment'));

            }



            if( isset($this->apsl_settings['apsl_enable_disable_edd_login_shortcode']) && $this->apsl_settings['apsl_enable_disable_edd_login_shortcode'] == 'yes'){

                add_action('edd_login_fields_after', array($this, 'add_social_login_form_to_comment'));

            }

            

            if(isset($this->apsl_settings['apsl_enable_disable_edd_register_shortcode']) && $this->apsl_settings['apsl_enable_disable_edd_register_shortcode'] == 'yes'){

                add_action('edd_register_form_fields_after', array($this, 'add_social_login_form_to_comment'));

            }

            

            // wp_editor( 'afsa', 'apsl-email-body');



            $options = get_option( APSL_SETTINGS );

            if( $options['apsl_enable_disable_plugin'] == 'yes' ) {

                if( in_array( "login_form", $options['apsl_display_options'] ) ) {

                    add_action( 'login_form', array($this, 'add_social_login') );

                     // add the social logins to the login form

                    //buddypress compactibility check

                    if( $options['apsl_enable_disable_buddypress'] == 'yes' ) {

                        add_action( 'bp_before_sidebar_login_form', array($this, 'add_social_login_form_to_comment') );

                    }

                }

                

                if( in_array( "register_form", $options['apsl_display_options'] ) ) {

                    add_action( 'register_form', array($this, 'add_social_login') );

                     //add the social logins to the registration form

                    add_action( 'after_signup_form', array($this, 'add_social_login') );

                    

                    //buddypress compactibility check

                    if( $options['apsl_enable_disable_buddypress'] == 'yes' ) {

                        add_action( 'bp_before_account_details_fields', array($this, 'add_social_login_form_to_comment') );

                    }

                }

                

                if( in_array( "comment_form", $options['apsl_display_options'] ) ) {

                    add_action( 'comment_form_top', array($this, 'add_social_login_form_to_comment') );

                     //add the social logins to the comment form

                    add_action( 'comment_form_must_log_in_after', array($this, 'add_social_login_form_to_comment') ); // add the social login buttons if “Users must be registered and logged in to comment” checked in the discussions settings.

                }

            }

            

            //woocommerce compactibility check

            if( $options['apsl_enable_disable_woocommerce'] == 'yes' ) {

                add_action( 'woocommerce_after_template_part', array($this, 'social_buttons_in_checkout') );

                add_action( 'woocommerce_login_form', array($this, 'add_social_login_form_to_comment') );

            }

            

            // add_action( 'after_signup_form', array($this, 'add_social_login') ); //save settings of a plugin

            // add_action( 'social_connect_form', array($this, 'add_social_login_form_to_comment') ); //save settings of a plugin

            add_shortcode( 'apsl-login', array($this, 'apsl_shortcode') );

             //adds a shortcode

            add_shortcode( 'apsl-login-with-login-form', array($this, 'apsl_with_login_form_shortcode') );

             //adds social login with login form shortcode

            add_action( 'init', array($this, 'login_check') );

             //check for the social logins

            add_action( 'widgets_init', array($this, 'register_apsl_widget') );

             //register the widget of a plugin

            add_action( 'login_enqueue_scripts', array($this, 'apsl_login_form_enqueue_style'), 10 );

            add_action( 'login_enqueue_scripts', array($this, 'apsl_login_form__enqueue_script'), 1 );

            add_action( 'admin_post_apsl_restore_default_settings', array($this, 'apsl_restore_default_settings') );

             //restores default settings.

            

            

            

            /**

             * Hook to display custom avatars

             */

            add_filter( 'get_avatar', array($this, 'apsl_social_login_custom_avatar'), 10, 5 );

            

            // Display users avatars from social networks on buddypress

            add_filter( 'bp_core_fetch_avatar', array($this, 'apsl_social_login_bp_user_custom_avatar'), 10, 2 );

            

            //add_action( 'wp_login_failed', array($this, 'aa_login_failed') ); // hook failed login



            //add delete action when user is deleted from wordpress backend.

            add_action( 'delete_user', array ($this, 'apsl_delete_user') );

            

        }



        //starts the session with the call of init hook

        function session_init() {

            if( !session_id() && !headers_sent()) {

                session_start();

            }

        }

        

        function check_login_data() {

            if( isset( $_POST['apsl_login_submit'], $_POST['apsl_form_nonce'] ) ) {

                global $user;

                $creds = array();

                $creds['user_login'] = sanitize_user( $_POST['login_username'] );

                $creds['user_password'] = $_POST['login_password'];

                $creds['remember'] = isset( $_POST['login_remember'] ) ? true : false;

                $user = wp_signon( $creds, false );

                if( is_wp_error( $user ) ) {

                    $_SESSION['apsl_login_error'] = __( 'Invalid Username or Password', APSL_TEXT_DOMAIN );

                    wp_redirect( $this->curlPageURL() );

                }else{

                    wp_redirect( $_POST['redirect_url'] );

                }

				die();

            }

        }

        

        function check_user_data() {

            if( isset( $_POST['apsl_user_details_submit'] ) ) {

                // echo "here is em";

                // die();

                global $user;

                $creds = array();

                $username = $_POST['login_username'];

                $email = $_POST['login_password'];

                if( username_exists( $username ) ) {

                    echo "username exists";

                } 

                else {

                    echo "valid username";

                }

                

                if( email_exists( $email ) ) {

                    echo "email exist";

                }

                else {

                    echo "email valid";

                }

                

                $creds['user_login'] = sanitize_user( $_POST['login_username'] );

                $creds['user_password'] = $_POST['login_password'];

                $creds['remember'] = isset( $_POST['login_remember'] ) ? true : false;

                $user = wp_signon( $creds, false );

                if( is_wp_error( $user ) ) {

                    $_SESSION['apsl_login_error'] = __( 'Invalid Username or Password', APSL_TEXT_DOMAIN );

                }

                wp_redirect( $_POST['redirect_url'] );

				die();

            }

        }

        

        //redirect the user to custom registration page

        function allow_user_enter_data(){

            if( isset( $_GET['page'] ) && $_GET['page'] == 'register_page' ) {

                include('inc/frontend/register_page.php');

                die();

            }

        }



        //load the default settings of the plugin

        function plugin_activation() {

            global $wpdb;

            if ( is_multisite() ) {

                $current_blog = $wpdb->blogid;

                // Get all blogs in the network and activate plugin on each one

                $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

                foreach ( $blog_ids as $blog_id ) {

                    switch_to_blog( $blog_id );

                    if( !get_option( APSL_SETTINGS ) ) {

                        include( 'inc/backend/activation.php' );

                    }

                }

            }else{

                if( !get_option( APSL_SETTINGS ) ) {

                    include( 'inc/backend/activation.php' );

                }

            }

            //install database

            self:: apsl_database_install();



        }



        //create the table to store the user details to the plugin

        function apsl_database_install() {

            global $wpdb;

            // create user details table

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            if ( is_multisite() ) {

                $current_blog = $wpdb->blogid;

                // Get all blogs in the network and activate plugin on each one

                $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

                foreach ( $blog_ids as $blog_id ) {

                    switch_to_blog( $blog_id );

                    $apsl_userdetails = "{$wpdb->prefix}apsl_users_social_profile_details";

                    $sql = "CREATE TABLE IF NOT EXISTS `$apsl_userdetails` (

                            id int(11) NOT NULL AUTO_INCREMENT,

                            user_id int(11) NOT NULL,

                            provider_name varchar(50) NOT NULL,

                            identifier varchar(255) NOT NULL,

                            unique_verifier varchar(255) NOT NULL,

                            email varchar(255) NOT NULL,

                            email_verified varchar(255) NOT NULL,

                            first_name varchar(150) NOT NULL,

                            last_name varchar(150) NOT NULL,

                            profile_url varchar(255) NOT NULL,

                            website_url varchar(255) NOT NULL,

                            photo_url varchar(255) NOT NULL,

                            display_name varchar(150) NOT NULL,

                            description varchar(255) NOT NULL,

                            gender varchar(10) NOT NULL,

                            language varchar(20) NOT NULL,

                            age varchar(10) NOT NULL,

                            birthday int(11) NOT NULL,

                            birthmonth int(11) NOT NULL,

                            birthyear int(11) NOT NULL,

                            phone varchar(75) NOT NULL,

                            address varchar(255) NOT NULL,

                            country varchar(75) NOT NULL,

                            region varchar(50) NOT NULL,

                            city varchar(50) NOT NULL,

                            zip varchar(25) NOT NULL,

                            UNIQUE KEY id (id),

                            KEY user_id (user_id),

                            KEY provider_name (provider_name)

                        )";

                    dbDelta( $sql );

                    restore_current_blog();

                }

            }else{

                $apsl_userdetails = "{$wpdb->prefix}apsl_users_social_profile_details";

                $sql = "CREATE TABLE IF NOT EXISTS `$apsl_userdetails` (

                            id int(11) NOT NULL AUTO_INCREMENT,

                            user_id int(11) NOT NULL,

                            provider_name varchar(50) NOT NULL,

                            identifier varchar(255) NOT NULL,

                            unique_verifier varchar(255) NOT NULL,

                            email varchar(255) NOT NULL,

                            email_verified varchar(255) NOT NULL,

                            first_name varchar(150) NOT NULL,

                            last_name varchar(150) NOT NULL,

                            profile_url varchar(255) NOT NULL,

                            website_url varchar(255) NOT NULL,

                            photo_url varchar(255) NOT NULL,

                            display_name varchar(150) NOT NULL,

                            description varchar(255) NOT NULL,

                            gender varchar(10) NOT NULL,

                            language varchar(20) NOT NULL,

                            age varchar(10) NOT NULL,

                            birthday int(11) NOT NULL,

                            birthmonth int(11) NOT NULL,

                            birthyear int(11) NOT NULL,

                            phone varchar(75) NOT NULL,

                            address varchar(255) NOT NULL,

                            country varchar(75) NOT NULL,

                            region varchar(50) NOT NULL,

                            city varchar(50) NOT NULL,

                            zip varchar(25) NOT NULL,

                            UNIQUE KEY id (id),

                            KEY user_id (user_id),

                            KEY provider_name (provider_name)

                        )";

                dbDelta( $sql );

            }

        }



        //loads the text domain for translation

        function plugin_text_domain() {

            load_plugin_textdomain( APSL_TEXT_DOMAIN, false, APSL_LANG_DIR );

        }

        

        //register the plugin menu for backend.

        function add_apsl_menu() {

            add_menu_page( 'AccessPress Social Login', 'AccessPress Social Login', 'manage_options', APSL_TEXT_DOMAIN, array($this, 'main_page'), APSL_IMAGE_DIR . '/icon.png' );

        }

        

        //menu page

        function main_page() {

            include( 'inc/backend/main-page.php' );

        }

        

        //registration of the backend assets

        function register_admin_assets() {

            wp_enqueue_style( 'fontawsome-css', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', '', APSL_VERSION );

            

            if( isset( $_GET['page'] ) && $_GET['page'] == 'apsl' ) {

                

                //backend scripts

                wp_enqueue_script('tiny_mce');

                wp_enqueue_script( 'jquery-ui-sortable' );

                wp_enqueue_script( 'apsl-admin-js', APSL_JS_DIR . '/backend.js', array('jquery', 'jquery-ui-sortable'), APSL_VERSION );

                 //registering plugin's admin js

                //register backend css

                wp_enqueue_style( 'apsl-backend-css', APSL_CSS_DIR . '/backend.css', '', APSL_VERSION );

            }

        }

        

        //registration of the plugins frontend assets

        function register_frontend_assets() {

            

            //register frontend scripts

            wp_enqueue_script( 'apsl-frontend-js', APSL_JS_DIR . '/frontend.js', array('jquery'), APSL_VERSION );

            

            //register frontend css

            wp_enqueue_style( 'fontawsome-css', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', '', APSL_VERSION );

            wp_enqueue_style( 'apsl-frontend-css', APSL_CSS_DIR . '/frontend.css', '', APSL_VERSION );

        }

        

        //save the settings of a plugin

        function save_settings() {

            if( isset( $_POST['apsl_save_settings'] ) && $_POST['apsl_settings_action'] && wp_verify_nonce( $_POST['apsl_settings_action'], 'apsl_nonce_save_settings' ) ) {

                include( 'inc/backend/save-settings.php' );

            }

            else {

                die( 'No script kiddies please!' );

            }

        }

        

        //function to add the social login in the login and registration form.

        function add_social_login() {

            if( !is_user_logged_in() ) {

                include( 'inc/frontend/login_integration.php' );

            }

        }

        

        //function to add the social login in the comment form.

        function add_social_login_form_to_comment() {

            $options = get_option( APSL_SETTINGS );

            $login_text = $options['apsl_title_text_field'];

            if( !is_user_logged_in() ) {

                echo do_shortcode( "[apsl-login login_text='{$login_text}']" );

            }

        }

        

        public function social_buttons_in_checkout( $template_name ) {

            if( $template_name == 'checkout/form-login.php' ) {

                $options = $this->apsl_settings;

                $login_text = $options['apsl_title_text_field'];

                if( !is_user_logged_in() ) {

?>

					<p class="woocommerce-info"><?php

                    echo $options['apsl_title_text_field']; ?> <a href="#" class="show-apsl-container"><?php

                    _e( 'Click here to login', APSL_TEXT_DOMAIN ); ?></a> </p>

					<form class="login apsl-container" style="display: none;">

						<?php

                    echo do_shortcode( "[apsl-login login_text='{$login_text}']" ); ?>

					</form>

					<?php

                }

            }

        }

        

        //function for adding shortcode of a plugin

        function apsl_shortcode( $attr ) {

            ob_start();

            include( 'inc/frontend/shortcode.php' );

            $html = ob_get_contents();

            ob_get_clean();

            return $html;

        }

        

        function apsl_with_login_form_shortcode( $attr ) {

            ob_start();

            include( 'inc/frontend/shortcode_with_login_form.php' );

            $html = ob_get_contents();

            ob_get_clean();

            return $html;

        }

        

        //checking of the login

        function login_check() {

            

            include( 'inc/frontend/login_check.php' );

        }

        

        //registration of the social login widget

        function register_apsl_widget() {

            register_widget( 'APSL_Widget' );

            register_widget( 'APSL_Widget_With_Login_Form' );

        }

        

        //printing the array in proper format

        function print_array( $args ) {

            echo "<pre>";

            print_r( $args );

            echo "</pre>";

        }

        

        function apsl_login_form_enqueue_style() {

            wp_enqueue_style( 'fontawsome-css', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', '', APSL_VERSION );

            wp_enqueue_style( 'apsl-backend-css', APSL_CSS_DIR . '/backend.css', '', APSL_VERSION );

            wp_enqueue_style( 'apsl-frontend-css', APSL_CSS_DIR . '/frontend.css', '', APSL_VERSION );

        }

        

        function apsl_login_form__enqueue_script() {

            wp_enqueue_script( 'apsl-admin-js', APSL_JS_DIR . '/backend.js', array('jquery', 'jquery-ui-sortable'), APSL_VERSION );

            wp_enqueue_script( 'apsl-frontend-js', APSL_JS_DIR . '/frontend.js', array('jquery', 'jquery-ui-sortable'), APSL_VERSION );

             //registering plugin's admin js

            

        }

        

        function apsl_restore_default_settings() {

            $nonce = $_REQUEST['_wpnonce'];

            if( !empty( $_GET ) && wp_verify_nonce( $nonce, 'apsl-restore-default-settings-nonce' ) ) {

                

                //restore the default plugin activation settings from the activation page.

                include( 'inc/backend/activation.php' );

                $_SESSION['apsl_message'] = __( 'Settings restored Successfully.', APSL_TEXT_DOMAIN );

                wp_redirect( admin_url() . 'admin.php?page=apsl' );

                exit;

            } 

            else {

                die( 'No script kiddies please!' );

            }

        }

        

        function apsl_social_login_custom_avatar( $avatar, $mixed, $size, $default, $alt = '' ) {

            

            $options = get_option( APSL_SETTINGS );

            

            //Check if we have an user identifier

            if( is_numeric( $mixed ) AND $mixed > 0 ) {

                $user_id = $mixed;

            }

            

            //Check if we have an user email

            elseif( is_string( $mixed ) AND( $user = get_user_by( 'email', $mixed ) ) ) {

                $user_id = $user->ID;

            }

            

            //Check if we have an user object

            elseif( is_object( $mixed ) AND property_exists( $mixed, 'user_id' ) AND is_numeric( $mixed->user_id ) ) {

                $user_id = $mixed->user_id;

            }

            

            //None found

            else {

                $user_id = null;

            }

            

            //User found?

            if( !empty( $user_id ) ) {

                

                //Override current avatar ?

                $override_avatar = true;

                

                //Read the avatar

                $user_meta_thumbnail = get_user_meta( $user_id, 'deuimage', true );

                

                //read user details

                $user_meta_name = get_user_meta( $user_id, 'first_name', true );

                

                if( $options['apsl_user_avatar_options'] == 'social' ) {

                    $user_picture =( !empty( $user_meta_thumbnail ) ? $user_meta_thumbnail : '' );

                    

                    //Avatar found?

                    if( $user_picture !== false AND strlen( trim( $user_picture ) ) > 0 ) {

                        return '<img alt="' . $user_meta_name . '" src="' . $user_picture . '" class="avatar apsl-avatar-social-login avatar-' . $size . ' photo" height="' . $size . '" width="' . $size . '" />';

                    }

                }

            }

            return $avatar;

        }

        

        function apsl_social_login_bp_user_custom_avatar( $html, $args ) {

            $options = get_option( APSL_SETTINGS );

            

            //Check arguments

            if( is_array( $args ) ) {

                

                //User Object

                if( !empty( $args['object'] ) AND strtolower( $args['object'] ) == 'user' ) {

                    

                    //User Identifier

                    if( !empty( $args['item_id'] ) AND is_numeric( $args['item_id'] ) ) {

                        $user_id = $args['item_id'];

                        

                        // Only Overwrite gravatars

                        // https://wordpress.org/support/topic/buddypress-avatar-overwriting-problem?replies=1

                        if( bp_get_user_has_avatar( $user_id ) ) {

                            return $html;

                        }

                        

                        if( $options['apsl_user_avatar_options'] == 'social' ) {

                            

                            //Read the avatar

                            $user_meta_thumbnail = get_user_meta( $user_id, 'deuimage', true );

                            

                            //read user details

                            $user_meta_name = get_user_meta( $user_id, 'first_name', true );

                            

                            $apsl_avatar =( !empty( $user_meta_thumbnail ) ? $user_meta_thumbnail : '' );

                            

                            //Retrieve Avatar

                            if( $apsl_avatar ) {

                                $img_class =( 'class="' .( !empty( $args['class'] ) ?( $args['class'] . ' ' ) : '' ) . 'avatar-wordpress-social-login" ' );

                                $img_width =( !empty( $args['width'] ) ? 'width="' . $args['width'] . '" ' : 'width="' . bp_core_avatar_full_width() . '" ' );

                                $img_height =( !empty( $args['height'] ) ? 'height="' . $args['height'] . '" ' : 'height="' . bp_core_avatar_full_height() . '" ' );

                                $img_alt =( !empty( $args['alt'] ) ? 'alt="' . esc_attr( $args['alt'] ) . '" ' : '' );

                                

                                //Replace

                                $html = preg_replace( '#<img[^>]+>#i', '<img src="' . $apsl_avatar . '" ' . $img_alt . $img_class . $img_height . $img_width . '/>', $html );

                                return $html;

                            }

                            return $html;

                        }

                    }

                }

            }

            

            return $html;

        }

        

        //returns the login form html

        function custom_login_form() {



            $options = get_option( APSL_SETTINGS );

            if(isset($options['apsl_custom_login_redirect_options'])){

                if( $options['apsl_custom_login_redirect_options'] == 'home' ) {

                    $user_login_url = home_url();

                }else if( $options['apsl_custom_login_redirect_options'] == 'current_page' ) {

                    $user_login_url = $this->curlPageURL();

                }else if( $options['apsl_custom_login_redirect_options'] == 'custom_page' ) {

                    if( $options['apsl_custom_login_redirect_link'] != '' ) {

                        $login_page = $options['apsl_custom_login_redirect_link'];

                        $user_login_url = $login_page;

                    }else {

                        $user_login_url = home_url();

                    }

                }

            }else{

                $user_login_url = home_url();

            }

            $nonce_field = $this->get_nonce_field_html();

            $form = '<h2>' . __( 'Login', APSL_TEXT_DOMAIN ) . '</h2>';

            if( isset( $_SESSION['apsl_login_error'] ) ) {

                $login_error = $_SESSION['apsl_login_error'];

                $form.= '<div class="apsl-error apsl-login-error">' . $login_error . '</div>';

				unset($_SESSION['apsl_login_error']);

            }

            $form.= '<form method="post" action="" class="clearfix">

		                 <div class="apsl-login-field-wrapper">

		                   <label>'.__( 'Username', APSL_TEXT_DOMAIN ) .'</label>

		                   <div class="apsl-login-field apsl-username">

		                     <input type="text" name="login_username" required/>

		                   </div>

		                 </div>

		                 <div class="apsl-login-field-wrapper">

		                   <label>'.__( 'Password', APSL_TEXT_DOMAIN ) .'</label>

		                   <div class="apsl-login-field apsl-password">

		                     <input type="password" name="login_password" required/>

		                   </div>

		                 </div>

		                 <div class="apsl-login-field-wrapper apsl-fl-l">

		                   <div class="apsl-login-field apsl-remember-me">

		                     <input type="checkbox" name="login_remember" />

		                   </div>

		                   <label>'.__( 'Remember Me', APSL_TEXT_DOMAIN ) .'</label>

		                 </div>



		                 <div class="apsl-login-field-wrapper apsl-fl-r">

		                   <div class="apsl-login-field apsl-submit-login">

		                     <input type="submit" name="apsl_login_submit" value="' . __( 'Login', APSL_TEXT_DOMAIN ) . '">

		                   </div>

		                 </div>

		                 <input type="hidden" name="redirect_url" value="' . $user_login_url . '"/>';

            $form.= $nonce_field;

            $form.= '</form>';

            return $form;

        }

        

        //returns nonce field html as variable

        function get_nonce_field_html() {

            ob_start();

            wp_nonce_field( 'apsl_form_nonce', 'apsl_form_nonce' );

            $nonce_field = ob_get_contents();

            ob_end_clean();

            return $nonce_field;

        }



        function curlPageURL() {

            $pageURL = 'http';

            if ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ) {

                $pageURL .= "s";

            }

            $pageURL .= "://";

            if ( $_SERVER["SERVER_PORT"] != "80" ) {

                $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];

            } else {

                $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];

            }

            return $pageURL;

        }



        function apsl_delete_user( $user_id ) {

            global $wpdb;

            $table_name = $apsl_userdetails = "{$wpdb->prefix}apsl_users_social_profile_details";

            $user_obj = get_userdata( $user_id );

            $result = $wpdb->delete( $table_name, array( 'user_id' => $user_id ) );

        }



        //Sanitizes field by converting line breaks to <br /> tags

        function sanitize_escaping_linebreaks($text) {

            $text = implode( "<br \>", explode( "\n", $text ));

            return $text;

        }



        //outputs by converting <Br/> tags into line breaks

        function output_converting_br($text) {

            $text = implode( "\n", explode( "<br \>", $text ) );

            return $text;

        }

    }



    //class termination



}



$apsl_object = new APSL_Class();

