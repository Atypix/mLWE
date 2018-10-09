<?php
defined( 'ABSPATH' ) or die( "No script kiddies please!" );
//include( APSL_PLUGIN_DIR . 'inc/functions.php' );

// if(isset($_REQUEST['error']) || isset($_REQUEST['denied']) || isset($_REQUEST['oauth_problem'])){
//    $_SESSION['apsl_login_error_flag'] = 1;
//    if(isset($_REQUEST['redirect_to'])){
//     $redirect_to = $_REQUEST['redirect_to'];
//     echo $redirect_to;
//     die();
//     echo "<script> window.close(); window.opener.location.href='$redirect_to'; </script>";
//     // APSL_Functions::redirect($_REQUEST['redirect_to']);
//    }else{
//     echo "You have access denied. Please reauthorize the app to access the login with this site.";
//    }
//    die();
// }

if( isset( $_GET['apsl_login_id'] ) ) {

    if( isset( $_REQUEST['state'] ) ) {
        parse_str( base64_decode( $_REQUEST['state'] ), $state_vars );

        if( isset( $state_vars['redirect_to'] ) ) {
            $_GET['redirect_to'] = $_REQUEST['redirect_to'] = $state_vars['redirect_to'];
        }
    }

    $exploder = explode( '_', $_GET['apsl_login_id'] );
    switch( $exploder[0] ) {
        case 'facebook':
            if( version_compare( PHP_VERSION, '5.4.0', '<' ) ) {
                echo _e( 'The Facebook SDK requires PHP version 5.4 or higher. Please notify about this error to site admin.', APSL_TEXT_DOMAIN );
                die();
            }
            onFacebookLogin();
            break;

        case 'twitter':
            if( !class_exists( 'TwitterOAuth' ) ) {
                include( APSL_PLUGIN_DIR . 'twitter/OAuth.php' );
                include( APSL_PLUGIN_DIR . 'twitter/twitteroauth.php' );
            }
            onTwitterLogin();
            break;

        case 'google':
            include( APSL_PLUGIN_DIR . 'google/Client.php' );
            include( APSL_PLUGIN_DIR . 'google/Service/Plus.php' );
            onGoogleLogin();
            break;

        case 'linkedin':
            include( APSL_PLUGIN_DIR . 'linkedin/linkedin_class.php' );
            if( !class_exists( 'OAuthException' ) ) {
                include( APSL_PLUGIN_DIR . 'linkedin/OAuth.php' );
            }
            onLinkedInLogin();
            break;

        case 'instagram':
            include( APSL_PLUGIN_DIR . 'instagram/instagram.class.php' );
            include( APSL_PLUGIN_DIR . 'instagram/InstagramException.php' );
            onInstagramLogin();
            break;

        case 'foursquare':
            onFourSquareLogin();
            break;

        case 'wordpress':
            onWordPressLogin();
            break;

        case 'vk':
            include( APSL_PLUGIN_DIR . 'vk/vk.php' );
            include( APSL_PLUGIN_DIR . 'vk/vkException.php' );
            onVkLogin();
            break;

        case 'buffer':
            onBufferLogin();
            break;
    }
}


//for facebook login
function onFacebookLogin() {
    $response = new stdClass();
    $result = APSL_Functions::facebookLogin( $response );
    global $wpdb;
    if( isset( $result->status ) && $result->status == 'SUCCESS' ) {
        $unique_verifier = sha1($result->deutype.$result->deuid);
        $sql = "SELECT *  FROM  `{$wpdb->prefix}apsl_users_social_profile_details` WHERE  `provider_name` LIKE  '$result->deutype' AND  `identifier` LIKE  '$result->deuid' AND `unique_verifier` LIKE '$unique_verifier'";
        $row = $wpdb->get_row($sql);
        $options = get_option( APSL_SETTINGS );
        if( !$row ) {

            //check if there is already a user with the email address provided from social login already
            $user_details_by_email = APSL_Functions:: getUserByMail($result->email);
            if( $user_details_by_email != false ){
                //user already there so log him in
                $id = $user_details_by_email->ID;
                $sql = "SELECT *  FROM  `{$wpdb->prefix}apsl_users_social_profile_details` WHERE  `user_id` LIKE  '$id'; ";
                $row = $wpdb->get_row($sql);
                if(!$row){
                APSL_Functions:: link_user($id, $result);
                }
                APSL_Functions::loginUser( $id );
                die();
            }
            $_SESSION['user_details']= $result;
            if($options['apsl_custom_email_allow']=='allow' || $options['apsl_custom_username_allow'] == 'allow'){
                //perform the username and email address entry here
                $url = site_url().'?page=register_page';
                APSL_Functions:: redirect($url);
                die();
            }else{
                APSL_Functions::creatUser( $result->username, $result->email );
                $user_row = APSL_Functions::getUserByMail( $result->email );
                $id = $user_row->ID;
                $result = $result;
                $role = $options['apsl_user_role'];
                APSL_Functions::UpdateUserMeta( $id, $result, $role );
                APSL_Functions::loginUser( $id );
                exit();
            }

        }else{
            if( ($row->provider_name == $result->deutype) && ($row->identifier == $result->deuid) ){
                //echo "user found in our database";
                APSL_Functions:: loginUser( $row->user_id );
                exit();
            }else{
                // user not found in our database
            }
        }

    }else{
        if(isset($_REQUEST['error'])){
            $_SESSION['apsl_login_error_flag'] = 1;

            if( isset( $_REQUEST['redirect_to'] ) ) {
                $redirect_url = $_REQUEST['redirect_to'];
            }else{
                $options = get_option( APSL_SETTINGS );
                if( isset( $options['apsl_custom_login_redirect_options'] ) && $options['apsl_custom_login_redirect_options'] != '' ) {
                    if( $options['apsl_custom_login_redirect_options'] == 'home' ) {
                        $user_login_url = home_url();
                    } 
                    else if( $options['apsl_custom_login_redirect_options'] == 'current_page' ) {
                        if( isset( $_REQUEST['redirect_to'] ) ) {
                            $redirect_to = $_REQUEST['redirect_to'];
                            
                            // Redirect to https if user wants ssl
                            if( isset( $secure_cookie ) && false !== strpos( $redirect_url, 'wp-admin' ) ) $user_login_url = preg_replace( '|^http://|', 'https://', $redirect_url );
                        } 
                        else {
                            $user_login_url = home_url();
                        }
                    } 
                    else if( $options['apsl_custom_login_redirect_options'] == 'custom_page' ) {
                        if( $options['apsl_custom_login_redirect_link'] != '' ) {
                            $login_page = $options['apsl_custom_login_redirect_link'];
                            $user_login_url = $login_page;
                        } 
                        else {
                            $user_login_url = home_url();
                        }
                    }
                }else {
                    $user_login_url = home_url();
                }
                $redirect_url = $user_login_url;
            }

            echo "<script> window.close(); window.opener.location.href='$redirect_url'; </script>";
        }
        die();
    }
}

//for twitter login
function onTwitterLogin() {
    $result = APSL_Functions::twitterLogin();
    global $wpdb;
    if( isset($result->status) && $result->status == 'SUCCESS' ) {
        $unique_verifier = sha1($result->deutype.$result->deuid);
        $sql = "SELECT *  FROM  `{$wpdb->prefix}apsl_users_social_profile_details` WHERE  `provider_name` LIKE  '$result->deutype' AND  `identifier` LIKE  '$result->deuid' AND `unique_verifier` LIKE '$unique_verifier'";
        $row = $wpdb->get_row($sql);
        $options = get_option( APSL_SETTINGS );

        if( !$row ) {
            //check if there is already a user with the email address provided from social login already
            $user_details_by_email = APSL_Functions:: getUserByMail($result->email);
            if( $user_details_by_email != false ){
                //user already there so log him in
                $id = $user_details_by_email->ID;
                $sql = "SELECT *  FROM  `{$wpdb->prefix}apsl_users_social_profile_details` WHERE  `user_id` LIKE  '$id'; ";
                $row = $wpdb->get_row($sql);
                if(!$row){
                APSL_Functions:: link_user($id, $result);
                }
                APSL_Functions::loginUser( $id );
                die();
            }

            $_SESSION['user_details']= $result;
            if($options['apsl_custom_email_allow']=='allow' || $options['apsl_custom_username_allow'] == 'allow'){
                //perform the username and email address entry here
                $url = site_url().'?page=register_page';
                APSL_Functions:: redirect($url);
                die();
            }else{
                APSL_Functions::creatUser( $result->username, $result->email );
                $user_row = APSL_Functions::getUserByMail( $result->email );
                $id = $user_row->ID;
                $result = $result;
                $role = $options['apsl_user_role'];
                APSL_Functions::UpdateUserMeta( $id, $result, $role );
                APSL_Functions::loginUser( $id );
                exit();
            }

        }else{
            if( ($row->provider_name == $result->deutype) && ($row->identifier == $result->deuid) ){
                //user found in our database so let login
                APSL_Functions:: loginUser( $row->user_id );
                exit();
            }else{
                // user not found in our database so do nothing
            }
        }
    }else{
        if(isset($_REQUEST['denied'])){
            $_SESSION['apsl_login_error_flag'] = 1;

            if( isset( $_REQUEST['redirect_to'] ) ) {
                $redirect_url = $_REQUEST['redirect_to'];
            }else{
                $options = get_option( APSL_SETTINGS );
                if( isset( $options['apsl_custom_login_redirect_options'] ) && $options['apsl_custom_login_redirect_options'] != '' ) {
                    if( $options['apsl_custom_login_redirect_options'] == 'home' ) {
                        $user_login_url = home_url();
                    } 
                    else if( $options['apsl_custom_login_redirect_options'] == 'current_page' ) {
                        if( isset( $_REQUEST['redirect_to'] ) ) {
                            $redirect_to = $_REQUEST['redirect_to'];
                            
                            // Redirect to https if user wants ssl
                            if( isset( $secure_cookie ) && false !== strpos( $redirect_url, 'wp-admin' ) ) $user_login_url = preg_replace( '|^http://|', 'https://', $redirect_url );
                        } 
                        else {
                            $user_login_url = home_url();
                        }
                    } 
                    else if( $options['apsl_custom_login_redirect_options'] == 'custom_page' ) {
                        if( $options['apsl_custom_login_redirect_link'] != '' ) {
                            $login_page = $options['apsl_custom_login_redirect_link'];
                            $user_login_url = $login_page;
                        } 
                        else {
                            $user_login_url = home_url();
                        }
                    }
                }else {
                    $user_login_url = home_url();
                }
                $redirect_url = $user_login_url;
            }

            echo "<script> window.close(); window.opener.location.href='$redirect_url'; </script>";
        }
        die();
    }

}

//for google login
function onGoogleLogin() {
    $result = APSL_Functions::GoogleLogin();
    global $wpdb;
    if( isset( $result->status ) && $result->status == 'SUCCESS' ) {
        $unique_verifier = sha1($result->deutype.$result->deuid);
        $sql = "SELECT *  FROM  `{$wpdb->prefix}apsl_users_social_profile_details` WHERE  `provider_name` LIKE  '$result->deutype' AND  `identifier` LIKE  '$result->deuid' AND `unique_verifier` LIKE '$unique_verifier'";
        $row = $wpdb->get_row($sql);
        $options = get_option( APSL_SETTINGS );
        
        if( !$row ) {
            //check if there is already a user with the email address provided from social login already
            $user_details_by_email = APSL_Functions:: getUserByMail($result->email);
            if( $user_details_by_email != false ){
                //user already there so log him in
                $id = $user_details_by_email->ID;
                $sql = "SELECT *  FROM  `{$wpdb->prefix}apsl_users_social_profile_details` WHERE  `user_id` LIKE  '$id'; ";
                $row = $wpdb->get_row($sql);
                if(!$row){
                APSL_Functions:: link_user($id, $result);
                }
                APSL_Functions::loginUser( $id );
                die();
            }

            $_SESSION['user_details']= $result;
            if($options['apsl_custom_email_allow']=='allow' || $options['apsl_custom_username_allow'] == 'allow'){
                //perform the username and email address entry here
                $url = site_url().'?page=register_page';
                APSL_Functions:: redirect($url);
                die();
            }else{
                APSL_Functions::creatUser( $result->username, $result->email );
                $user_row = APSL_Functions::getUserByMail( $result->email );
                $id = $user_row->ID;
                $result = $result;
                $role = $options['apsl_user_role'];
                APSL_Functions::UpdateUserMeta( $id, $result, $role );
                APSL_Functions::loginUser( $id );
                exit();
            }

        }else{
            if( ($row->provider_name == $result->deutype) && ($row->identifier == $result->deuid) ){
                //user found in our database so let login
                APSL_Functions:: loginUser( $row->user_id );
                exit();
            }else{
                // user not found in our database so do nothing
            }
        }
        
    }else{
        if(isset($_REQUEST['error'])){
            $_SESSION['apsl_login_error_flag'] = 1;

            if( isset( $_REQUEST['state'] ) ) {
                parse_str( base64_decode( $_REQUEST['state'] ), $state_vars );
                if( isset( $state_vars['redirect_to'] ) ) {
                    $_GET['redirect_to'] = $_REQUEST['redirect_to'] = $state_vars['redirect_to'];
                }
                $redirect_url = $_REQUEST['redirect_to'];
            }else{
                $options = get_option( APSL_SETTINGS );
                if( isset( $options['apsl_custom_login_redirect_options'] ) && $options['apsl_custom_login_redirect_options'] != '' ) {
                    if( $options['apsl_custom_login_redirect_options'] == 'home' ) {
                        $user_login_url = home_url();
                    } 
                    else if( $options['apsl_custom_login_redirect_options'] == 'current_page' ) {
                        if( isset( $_REQUEST['redirect_to'] ) ) {
                            $redirect_to = $_REQUEST['redirect_to'];
                            
                            // Redirect to https if user wants ssl
                            if( isset( $secure_cookie ) && false !== strpos( $redirect_url, 'wp-admin' ) ) $user_login_url = preg_replace( '|^http://|', 'https://', $redirect_url );
                        } 
                        else {
                            $user_login_url = home_url();
                        }
                    } 
                    else if( $options['apsl_custom_login_redirect_options'] == 'custom_page' ) {
                        if( $options['apsl_custom_login_redirect_link'] != '' ) {
                            $login_page = $options['apsl_custom_login_redirect_link'];
                            $user_login_url = $login_page;
                        } 
                        else {
                            $user_login_url = home_url();
                        }
                    }
                }else {
                    $user_login_url = home_url();
                }
                $redirect_url = $user_login_url;
            }

            echo "<script> window.close(); window.opener.location.href='$redirect_url'; </script>";
        }
        die();
    }
}

//for linkedin login
function onLinkedInLogin() {
    $result = APSL_Functions::linkedInLogin();
    global $wpdb;
    if( isset( $result->status ) && $result->status == 'SUCCESS' ) {
        $unique_verifier = sha1($result->deutype.$result->deuid);
        $sql = "SELECT *  FROM  `{$wpdb->prefix}apsl_users_social_profile_details` WHERE  `provider_name` LIKE  '$result->deutype' AND  `identifier` LIKE  '$result->deuid' AND `unique_verifier` LIKE '$unique_verifier'";
        $row = $wpdb->get_row($sql);
        $options = get_option( APSL_SETTINGS );

        if( !$row ) {
            //check if there is already a user with the email address provided from social login already
            $user_details_by_email = APSL_Functions:: getUserByMail($result->email);
            if( $user_details_by_email != false ){
                //user already there so log him in
                $id = $user_details_by_email->ID;
                $sql = "SELECT *  FROM  `{$wpdb->prefix}apsl_users_social_profile_details` WHERE  `user_id` LIKE  '$id'; ";
                $row = $wpdb->get_row($sql);
                if(!$row){
                APSL_Functions:: link_user($id, $result);
                }
                APSL_Functions::loginUser( $id );
                die();
            }

            $_SESSION['user_details']= $result;
            if($options['apsl_custom_email_allow']=='allow' || $options['apsl_custom_username_allow'] == 'allow'){
                //perform the username and email address entry here
                $url = site_url().'?page=register_page';
                APSL_Functions:: redirect($url);
                die();
            }else{
                APSL_Functions::creatUser( $result->username, $result->email );
                $user_row = APSL_Functions::getUserByMail( $result->email );
                $id = $user_row->ID;
                $result = $result;
                $role = $options['apsl_user_role'];
                APSL_Functions::UpdateUserMeta( $id, $result, $role );
                APSL_Functions::loginUser( $id );
                exit();
            }

        }else{
            if( ($row->provider_name == $result->deutype) && ($row->identifier == $result->deuid) ){
                //user found in our database so let login
                APSL_Functions:: loginUser( $row->user_id );
                exit();
            }else{
                // user not found in our database so do nothing
            }
        }
        
    }else{
        if(isset($_REQUEST['error'])){
            $_SESSION['apsl_login_error_flag'] = 1;

            if( isset( $_REQUEST['redirect_to'] ) ) {
                $redirect_url = $_REQUEST['redirect_to'];
            }else{
                $options = get_option( APSL_SETTINGS );
                if( isset( $options['apsl_custom_login_redirect_options'] ) && $options['apsl_custom_login_redirect_options'] != '' ) {
                    if( $options['apsl_custom_login_redirect_options'] == 'home' ) {
                        $user_login_url = home_url();
                    } 
                    else if( $options['apsl_custom_login_redirect_options'] == 'current_page' ) {
                        if( isset( $_REQUEST['redirect_to'] ) ) {
                            $redirect_to = $_REQUEST['redirect_to'];
                            
                            // Redirect to https if user wants ssl
                            if( isset( $secure_cookie ) && false !== strpos( $redirect_url, 'wp-admin' ) ) $user_login_url = preg_replace( '|^http://|', 'https://', $redirect_url );
                        } 
                        else {
                            $user_login_url = home_url();
                        }
                    } 
                    else if( $options['apsl_custom_login_redirect_options'] == 'custom_page' ) {
                        if( $options['apsl_custom_login_redirect_link'] != '' ) {
                            $login_page = $options['apsl_custom_login_redirect_link'];
                            $user_login_url = $login_page;
                        } 
                        else {
                            $user_login_url = home_url();
                        }
                    }
                }else {
                    $user_login_url = home_url();
                }
                $redirect_url = $user_login_url;
            }

            echo "<script> window.close(); window.opener.location.href='$redirect_url'; </script>";
        }
    }
}

// view details here : https://instagram.com/developer/
// http://www.9lessons.info/2012/05/login-with-instagram-php.html
//for instagram login
function onInstagramLogin() {
    $response = new stdClass();
    $result = APSL_Functions::instagramLogin( $response );
    global $wpdb;
    if( isset( $result->status ) && $result->status == 'SUCCESS' ) {
        $unique_verifier = sha1($result->deutype.$result->deuid);
        $sql = "SELECT *  FROM  `{$wpdb->prefix}apsl_users_social_profile_details` WHERE  `provider_name` LIKE  '$result->deutype' AND  `identifier` LIKE  '$result->deuid' AND `unique_verifier` LIKE '$unique_verifier'";
        $row = $wpdb->get_row($sql);
        $options = get_option( APSL_SETTINGS );
        
        if( !$row ) {
            //check if there is already a user with the email address provided from social login already
            $user_details_by_email = APSL_Functions:: getUserByMail($result->email);
            if( $user_details_by_email != false ){
                //user already there so log him in
                $id = $user_details_by_email->ID;
                $sql = "SELECT *  FROM  `{$wpdb->prefix}apsl_users_social_profile_details` WHERE  `user_id` LIKE  '$id'; ";
                $row = $wpdb->get_row($sql);
                if(!$row){
                APSL_Functions:: link_user($id, $result);
                }
                APSL_Functions::loginUser( $id );
                die();
            }

            $_SESSION['user_details']= $result;
            if($options['apsl_custom_email_allow']=='allow' || $options['apsl_custom_username_allow'] == 'allow'){
                //perform the username and email address entry here
                $url = site_url().'?page=register_page';
                APSL_Functions:: redirect($url);
                die();
            }else{
                APSL_Functions::creatUser( $result->username, $result->email );
                $user_row = APSL_Functions::getUserByMail( $result->email );
                $id = $user_row->ID;
                $result = $result;
                $role = $options['apsl_user_role'];
                APSL_Functions::UpdateUserMeta( $id, $result, $role );
                APSL_Functions::loginUser( $id );
                exit();
            }

        }else{
            if( ($row->provider_name == $result->deutype) && ($row->identifier == $result->deuid) ){
                //user found in our database so let login
                APSL_Functions:: loginUser( $row->user_id );
                exit();
            }else{
                // user not found in our database so do nothing
            }
        }
        
    }else{
        if(isset($_REQUEST['error'])){
            $_SESSION['apsl_login_error_flag'] = 1;
            if( isset( $_REQUEST['redirect_to'] ) ) {
                $redirect_url = $_REQUEST['redirect_to'];
            }else{
                $options = get_option( APSL_SETTINGS );
                if( isset( $options['apsl_custom_login_redirect_options'] ) && $options['apsl_custom_login_redirect_options'] != '' ) {
                    if( $options['apsl_custom_login_redirect_options'] == 'home' ) {
                        $user_login_url = home_url();
                    } 
                    else if( $options['apsl_custom_login_redirect_options'] == 'current_page' ) {
                        if( isset( $_REQUEST['redirect_to'] ) ) {
                            $redirect_to = $_REQUEST['redirect_to'];
                            
                            // Redirect to https if user wants ssl
                            if( isset( $secure_cookie ) && false !== strpos( $redirect_url, 'wp-admin' ) ) $user_login_url = preg_replace( '|^http://|', 'https://', $redirect_url );
                        } 
                        else {
                            $user_login_url = home_url();
                        }
                    } 
                    else if( $options['apsl_custom_login_redirect_options'] == 'custom_page' ) {
                        if( $options['apsl_custom_login_redirect_link'] != '' ) {
                            $login_page = $options['apsl_custom_login_redirect_link'];
                            $user_login_url = $login_page;
                        } 
                        else {
                            $user_login_url = home_url();
                        }
                    }
                }else {
                    $user_login_url = home_url();
                }
                $redirect_url = $user_login_url;
            }
            echo "<script> window.close(); window.opener.location.href='$redirect_url'; </script>";
        }
        die();
    }
}

function onFourSquareLogin() {
    $response = new stdClass();
    $result = APSL_Functions::fourSquareLogin();
    global $wpdb;
    if( isset( $result->status ) && $result->status == 'SUCCESS' ) {
        $unique_verifier = sha1($result->deutype.$result->deuid);
        $sql = "SELECT *  FROM  `{$wpdb->prefix}apsl_users_social_profile_details` WHERE  `provider_name` LIKE  '$result->deutype' AND  `identifier` LIKE  '$result->deuid' AND `unique_verifier` LIKE '$unique_verifier'";
        $row = $wpdb->get_row($sql);
        $options = get_option( APSL_SETTINGS );
        
        if( !$row ) {
            //check if there is already a user with the email address provided from social login already
            $user_details_by_email = APSL_Functions:: getUserByMail($result->email);
            if( $user_details_by_email != false ){
                //user already there so log him in
                $id = $user_details_by_email->ID;
                $sql = "SELECT *  FROM  `{$wpdb->prefix}apsl_users_social_profile_details` WHERE  `user_id` LIKE  '$id'; ";
                $row = $wpdb->get_row($sql);
                if(!$row){
                APSL_Functions:: link_user($id, $result);
                }
                APSL_Functions::loginUser( $id );
                die();
            }

            $_SESSION['user_details']= $result;
            if($options['apsl_custom_email_allow']=='allow' || $options['apsl_custom_username_allow'] == 'allow'){
                //perform the username and email address entry here
                $url = site_url().'?page=register_page';
                APSL_Functions:: redirect($url);
                die();
            }else{
                APSL_Functions::creatUser( $result->username, $result->email );
                $user_row = APSL_Functions::getUserByMail( $result->email );
                $id = $user_row->ID;
                $result = $result;
                $role = $options['apsl_user_role'];
                APSL_Functions::UpdateUserMeta( $id, $result, $role );
                APSL_Functions::loginUser( $id );
                exit();
            }

        }else{
            if( ($row->provider_name == $result->deutype) && ($row->identifier == $result->deuid) ){
                //user found in our database so let login
                APSL_Functions:: loginUser( $row->user_id );
                exit();
            }else{
                // user not found in our database so do nothing
            }
        }
        
    }else{
        if(isset($_REQUEST['error'])){
            $_SESSION['apsl_login_error_flag'] = 1;
            if( isset( $_REQUEST['redirect_to'] ) ) {
                $redirect_url = $_REQUEST['redirect_to'];
            }else{
                $options = get_option( APSL_SETTINGS );
                if( isset( $options['apsl_custom_login_redirect_options'] ) && $options['apsl_custom_login_redirect_options'] != '' ) {
                    if( $options['apsl_custom_login_redirect_options'] == 'home' ) {
                        $user_login_url = home_url();
                    } 
                    else if( $options['apsl_custom_login_redirect_options'] == 'current_page' ) {
                        if( isset( $_REQUEST['redirect_to'] ) ) {
                            $redirect_to = $_REQUEST['redirect_to'];
                            
                            // Redirect to https if user wants ssl
                            if( isset( $secure_cookie ) && false !== strpos( $redirect_url, 'wp-admin' ) ) $user_login_url = preg_replace( '|^http://|', 'https://', $redirect_url );
                        } 
                        else {
                            $user_login_url = home_url();
                        }
                    } 
                    else if( $options['apsl_custom_login_redirect_options'] == 'custom_page' ) {
                        if( $options['apsl_custom_login_redirect_link'] != '' ) {
                            $login_page = $options['apsl_custom_login_redirect_link'];
                            $user_login_url = $login_page;
                        } 
                        else {
                            $user_login_url = home_url();
                        }
                    }
                }else {
                    $user_login_url = home_url();
                }
                $redirect_url = $user_login_url;
            }
            echo "<script> window.close(); window.opener.location.href='$redirect_url'; </script>";
        }
        die();
    }

}

function onWordPressLogin() {
    $response = new stdClass();
    $result = APSL_Functions::wordPressLogin();
    global $wpdb;
    if( isset( $result->status ) && $result->status == 'SUCCESS' ) {
        $unique_verifier = sha1($result->deutype.$result->deuid);
        $sql = "SELECT *  FROM  `{$wpdb->prefix}apsl_users_social_profile_details` WHERE  `provider_name` LIKE  '$result->deutype' AND  `identifier` LIKE  '$result->deuid' AND `unique_verifier` LIKE '$unique_verifier'";
        $row = $wpdb->get_row($sql);
        $options = get_option( APSL_SETTINGS );
        
        if( !$row ) {
            //check if there is already a user with the email address provided from social login already
            $user_details_by_email = APSL_Functions:: getUserByMail($result->email);
            if( $user_details_by_email != false ){
                //user already there so log him in
                $id = $user_details_by_email->ID;
                $sql = "SELECT *  FROM  `{$wpdb->prefix}apsl_users_social_profile_details` WHERE  `user_id` LIKE  '$id'; ";
                $row = $wpdb->get_row($sql);
                if(!$row){
                APSL_Functions:: link_user($id, $result);
                }
                APSL_Functions::loginUser( $id );
                die();
            }

            $_SESSION['user_details']= $result;
            if($options['apsl_custom_email_allow']=='allow' || $options['apsl_custom_username_allow'] == 'allow'){
                //perform the username and email address entry here
                $url = site_url().'?page=register_page';
                APSL_Functions:: redirect($url);
                die();
            }else{
                APSL_Functions::creatUser( $result->username, $result->email );
                $user_row = APSL_Functions::getUserByMail( $result->email );
                $id = $user_row->ID;
                $result = $result;
                $role = $options['apsl_user_role'];
                APSL_Functions::UpdateUserMeta( $id, $result, $role );
                APSL_Functions::loginUser( $id );
                exit();
            }

        }else{
            if( ($row->provider_name == $result->deutype) && ($row->identifier == $result->deuid) ){
                //user found in our database so let login
                APSL_Functions:: loginUser( $row->user_id );
                exit();
            }else{
                // user not found in our database so do nothing
            }
        }
        
    }else{
        if(isset($_REQUEST['error'])){
            $_SESSION['apsl_login_error_flag'] = 1;
            if( isset( $_REQUEST['redirect_to'] ) ) {
                $redirect_url = $_REQUEST['redirect_to'];
            }else{
                $options = get_option( APSL_SETTINGS );
                if( isset( $options['apsl_custom_login_redirect_options'] ) && $options['apsl_custom_login_redirect_options'] != '' ) {
                    if( $options['apsl_custom_login_redirect_options'] == 'home' ) {
                        $user_login_url = home_url();
                    } 
                    else if( $options['apsl_custom_login_redirect_options'] == 'current_page' ) {
                        if( isset( $_REQUEST['redirect_to'] ) ) {
                            $redirect_to = $_REQUEST['redirect_to'];
                            
                            // Redirect to https if user wants ssl
                            if( isset( $secure_cookie ) && false !== strpos( $redirect_url, 'wp-admin' ) ) $user_login_url = preg_replace( '|^http://|', 'https://', $redirect_url );
                        } 
                        else {
                            $user_login_url = home_url();
                        }
                    } 
                    else if( $options['apsl_custom_login_redirect_options'] == 'custom_page' ) {
                        if( $options['apsl_custom_login_redirect_link'] != '' ) {
                            $login_page = $options['apsl_custom_login_redirect_link'];
                            $user_login_url = $login_page;
                        } 
                        else {
                            $user_login_url = home_url();
                        }
                    }
                }else {
                    $user_login_url = home_url();
                }
                $redirect_url = $user_login_url;
            }
            echo "<script> window.close(); window.opener.location.href='$redirect_url'; </script>";
        }
        die();
    }
    
}

function onVKLogin() {
    $response = new stdClass();
    $result = APSL_Functions::vkLogin();
    global $wpdb;
    if( isset( $result->status ) && $result->status == 'SUCCESS' ) {
        $unique_verifier = sha1($result->deutype.$result->deuid);
        $sql = "SELECT *  FROM  `{$wpdb->prefix}apsl_users_social_profile_details` WHERE  `provider_name` LIKE  '$result->deutype' AND  `identifier` LIKE  '$result->deuid' AND `unique_verifier` LIKE '$unique_verifier'";
        $row = $wpdb->get_row($sql);
        $options = get_option( APSL_SETTINGS );
        
        if( !$row ) {
            //check if there is already a user with the email address provided from social login already
            $user_details_by_email = APSL_Functions:: getUserByMail($result->email);
            if( $user_details_by_email != false ){
                //user already there so log him in
                $id = $user_details_by_email->ID;
                $sql = "SELECT *  FROM  `{$wpdb->prefix}apsl_users_social_profile_details` WHERE  `user_id` LIKE  '$id'; ";
                $row = $wpdb->get_row($sql);
                if(!$row){
                APSL_Functions:: link_user($id, $result);
                }
                APSL_Functions::loginUser( $id );
                die();
            }

            $_SESSION['user_details']= $result;
            if($options['apsl_custom_email_allow']=='allow' || $options['apsl_custom_username_allow'] == 'allow'){
                //perform the username and email address entry here
                $url = site_url().'?page=register_page';
                APSL_Functions:: redirect($url);
                die();
            }else{
                APSL_Functions::creatUser( $result->username, $result->email );
                $user_row = APSL_Functions::getUserByMail( $result->email );
                $id = $user_row->ID;
                $result = $result;
                $role = $options['apsl_user_role'];
                APSL_Functions::UpdateUserMeta( $id, $result, $role );
                APSL_Functions::loginUser( $id );
                exit();
            }

        }else{
            if( ($row->provider_name == $result->deutype) && ($row->identifier == $result->deuid) ){
                //user found in our database so let login
                APSL_Functions:: loginUser( $row->user_id );
                exit();
            }else{
                // user not found in our database so do nothing
            }
        }

    }else{
        if(isset($_REQUEST['error'])){
            $_SESSION['apsl_login_error_flag'] = 1;
            if( isset( $_REQUEST['redirect_to'] ) ) {
                $redirect_url = $_REQUEST['redirect_to'];
            }else{
                $options = get_option( APSL_SETTINGS );
                if( isset( $options['apsl_custom_login_redirect_options'] ) && $options['apsl_custom_login_redirect_options'] != '' ) {
                    if( $options['apsl_custom_login_redirect_options'] == 'home' ) {
                        $user_login_url = home_url();
                    } 
                    else if( $options['apsl_custom_login_redirect_options'] == 'current_page' ) {
                        if( isset( $_REQUEST['redirect_to'] ) ) {
                            $redirect_to = $_REQUEST['redirect_to'];
                            
                            // Redirect to https if user wants ssl
                            if( isset( $secure_cookie ) && false !== strpos( $redirect_url, 'wp-admin' ) ) $user_login_url = preg_replace( '|^http://|', 'https://', $redirect_url );
                        } 
                        else {
                            $user_login_url = home_url();
                        }
                    } 
                    else if( $options['apsl_custom_login_redirect_options'] == 'custom_page' ) {
                        if( $options['apsl_custom_login_redirect_link'] != '' ) {
                            $login_page = $options['apsl_custom_login_redirect_link'];
                            $user_login_url = $login_page;
                        } 
                        else {
                            $user_login_url = home_url();
                        }
                    }
                }else {
                    $user_login_url = home_url();
                }
                $redirect_url = $user_login_url;
            }
            echo "<script> window.close(); window.opener.location.href='$redirect_url'; </script>";
        }
        die();
    }

}

function onBufferLogin() {
    $response = new stdClass();
    $result = APSL_Functions::bufferLogin();
    global $wpdb;
    if( isset( $result->status ) && $result->status == 'SUCCESS' ) {
        $unique_verifier = sha1($result->deutype.$result->deuid);
        $sql = "SELECT *  FROM  `{$wpdb->prefix}apsl_users_social_profile_details` WHERE  `provider_name` LIKE  '$result->deutype' AND  `identifier` LIKE  '$result->deuid' AND `unique_verifier` LIKE '$unique_verifier'";
        $row = $wpdb->get_row($sql);
        $options = get_option( APSL_SETTINGS );
        
        if( !$row ) {
            //check if there is already a user with the email address provided from social login already
            $user_details_by_email = APSL_Functions:: getUserByMail($result->email);
            if( $user_details_by_email != false ){
                //user already there so log him in
                $id = $user_details_by_email->ID;
                $sql = "SELECT *  FROM  `{$wpdb->prefix}apsl_users_social_profile_details` WHERE  `user_id` LIKE  '$id'; ";
                $row = $wpdb->get_row($sql);
                if(!$row){
                APSL_Functions:: link_user($id, $result);
                }
                APSL_Functions::loginUser( $id );
                die();
            }

            $_SESSION['user_details']= $result;
            if($options['apsl_custom_email_allow']=='allow' || $options['apsl_custom_username_allow'] == 'allow'){
                //perform the username and email address entry here
                $url = site_url().'?page=register_page';
                APSL_Functions:: redirect($url);
                die();
            }else{
                APSL_Functions::creatUser( $result->username, $result->email );
                $user_row = APSL_Functions::getUserByMail( $result->email );
                $id = $user_row->ID;
                $result = $result;
                $role = $options['apsl_user_role'];
                APSL_Functions::UpdateUserMeta( $id, $result, $role );
                APSL_Functions::loginUser( $id );
                exit();
            }

        }else{
            if( ($row->provider_name == $result->deutype) && ($row->identifier == $result->deuid) ){
                //user found in our database so let login
                APSL_Functions:: loginUser( $row->user_id );
                exit();
            }else{
                // user not found in our database so do nothing
            }
        }

    }else{
        if(isset($_REQUEST['error'])){
            $_SESSION['apsl_login_error_flag'] = 1;
            if( isset( $_REQUEST['redirect_to'] ) ) {
                $redirect_url = $_REQUEST['redirect_to'];
            }else{
                $options = get_option( APSL_SETTINGS );
                if( isset( $options['apsl_custom_login_redirect_options'] ) && $options['apsl_custom_login_redirect_options'] != '' ) {
                    if( $options['apsl_custom_login_redirect_options'] == 'home' ) {
                        $user_login_url = home_url();
                    } 
                    else if( $options['apsl_custom_login_redirect_options'] == 'current_page' ) {
                        if( isset( $_REQUEST['redirect_to'] ) ) {
                            $redirect_to = $_REQUEST['redirect_to'];
                            
                            // Redirect to https if user wants ssl
                            if( isset( $secure_cookie ) && false !== strpos( $redirect_url, 'wp-admin' ) ) $user_login_url = preg_replace( '|^http://|', 'https://', $redirect_url );
                        } 
                        else {
                            $user_login_url = home_url();
                        }
                    } 
                    else if( $options['apsl_custom_login_redirect_options'] == 'custom_page' ) {
                        if( $options['apsl_custom_login_redirect_link'] != '' ) {
                            $login_page = $options['apsl_custom_login_redirect_link'];
                            $user_login_url = $login_page;
                        } 
                        else {
                            $user_login_url = home_url();
                        }
                    }
                }else {
                    $user_login_url = home_url();
                }
                $redirect_url = $user_login_url;
            }
            echo "<script> window.close(); window.opener.location.href='$redirect_url'; </script>";
        }
        die();
    }

}

