<?php
if( !class_exists( 'APSL_Functions' ) ) {
    Class APSL_Functions
    {
        static function UpdateUserMeta( $id, $result, $role ) {
            update_user_meta( $id, 'email', $result->email );
            update_user_meta( $id, 'first_name', $result->first_name );
            update_user_meta( $id, 'last_name', $result->last_name );
            update_user_meta( $id, 'billing_first_name', $result->first_name );
            update_user_meta( $id, 'billing_last_name', $result->last_name );
            update_user_meta( $id, 'deuid', $result->deuid );
            update_user_meta( $id, 'deutype', $result->deutype );
            update_user_meta( $id, 'deuimage', $result->deuimage );
            update_user_meta( $id, 'description', $result->about );
            update_user_meta( $id, 'sex', $result->gender );
            wp_update_user( array(
                'ID' => $id,
                'display_name' => $result->first_name . ' ' . $result->last_name,
                'role' => $role,
                'user_url' => $result->url
            ) );

            global $wpdb;
            $unique_verifier = sha1($result->deutype.$result->deuid);
            $apsl_userdetails = "{$wpdb->prefix}apsl_users_social_profile_details";
            
            $first_name = sanitize_text_field($result->first_name);
            $last_name = sanitize_text_field($result->last_name);
            $profile_url = sanitize_text_field($result->url);
            $photo_url = sanitize_text_field( $result->deuimage);
            $display_name = sanitize_text_field( $result->first_name . ' ' . $result->last_name);
            $description = sanitize_text_field($result->about);
            
            // $sql = "INSERT INTO $apsl_userdetails ( `user_id`, 
            //                                         `provider_name`, 
            //                                         `identifier`,
            //                                         `unique_verifier`,
            //                                         `email`,
            //                                         `first_name`, 
            //                                         `last_name`, 
            //                                         `profile_url`, 
            //                                         `photo_url`, 
            //                                         `display_name`, 
            //                                         `description`, 
            //                                         `gender`)
            //                                 VALUES(
            //                                         '$id',
            //                                         '$result->deutype',
            //                                         '$result->deuid',
            //                                         '$unique_verifier',
            //                                         '$result->email',
            //                                         '$first_name',
            //                                         '$last_name',
            //                                         '$profile_url',
            //                                         '$photo_url',
            //                                         '$display_name',
            //                                         '$description',
            //                                         '$result->gender'
            //                                        )";
            // $user_profile_details = $result;
            // $result = $wpdb->query($sql);

            $table_name     = $apsl_userdetails;
            $submit_array   = array(
                                   "user_id"        => $id,
                                   "provider_name"  => $result->deutype,
                                   "identifier"     => $result->deuid,
                                   "unique_verifier" => $unique_verifier,
                                   "email"          => $result->email,
                                   "first_name"     => $first_name,
                                   "last_name"      => $last_name,
                                   "profile_url"    =>$profile_url,
                                   "photo_url"      =>$photo_url,
                                   "display_name"   =>$display_name,
                                   "description"    =>$description,
                                   "gender"         =>$result->gender
                                );
            $user_profile_details = $result;
            $wpdb->insert($table_name, $submit_array );

            if(function_exists('bp_has_profile')){
                self:: apsl_buddypress_xprofile_mapping($id, $user_profile_details->deutype, $user_profile_details);
            }
            if(!$result){
                echo "Data insertion failed";
                // die(mysql_error());
            }
        }


        static function link_user($id, $result){
            global $wpdb;
            $unique_verifier = sha1($result->deutype.$result->deuid);
            $apsl_userdetails = "{$wpdb->prefix}apsl_users_social_profile_details";
            
            $first_name = sanitize_text_field($result->first_name);
            $last_name = sanitize_text_field($result->last_name);
            $profile_url = sanitize_text_field($result->url);
            $photo_url = sanitize_text_field( $result->deuimage);
            $display_name = sanitize_text_field( $result->first_name . ' ' . $result->last_name);
            $description = sanitize_text_field($result->about);

            // $sql = "INSERT INTO $apsl_userdetails ( `user_id`, 
            //                                         `provider_name`, 
            //                                         `identifier`,
            //                                         `unique_verifier`,
            //                                         `email`,
            //                                         `first_name`, 
            //                                         `last_name`, 
            //                                         `profile_url`, 
            //                                         `photo_url`, 
            //                                         `display_name`, 
            //                                         `description`, 
            //                                         `gender`)
            //                                 VALUES(
            //                                         '$id',
            //                                         '$result->deutype',
            //                                         '$result->deuid',
            //                                         '$unique_verifier',
            //                                         '$result->email',
            //                                         '$first_name',
            //                                         '$last_name',
            //                                         '$profile_url',
            //                                         '$photo_url',
            //                                         '$display_name',
            //                                         '$description',
            //                                         '$result->gender'
            //                                        )";
            // $user_profile_details = $result;
            // $result = $wpdb->query($sql);

            $table_name     = $apsl_userdetails;
            $submit_array   = array(
                                   "user_id"        => $id,
                                   "provider_name"  => $result->deutype,
                                   "identifier"     => $result->deuid,
                                   "unique_verifier" => $unique_verifier,
                                   "email"          => $result->email,
                                   "first_name"     => $first_name,
                                   "last_name"      => $last_name,
                                   "profile_url"    =>$profile_url,
                                   "photo_url"      =>$photo_url,
                                   "display_name"   =>$display_name,
                                   "description"    =>$description,
                                   "gender"         =>$result->gender
                                );
            $user_profile_details = $result;
            $wpdb->insert($table_name, $submit_array );

            if(function_exists('bp_has_profile')){
                self:: apsl_buddypress_xprofile_mapping($id, $user_profile_details->deutype, $user_profile_details);
            }
            if(!$result){
                echo "Data insertion failed";
                // die(mysql_error());
            }
        }

        // --------------------------------------------------------------------

        static function apsl_buddypress_xprofile_mapping( $user_id, $provider, $user_profile_details )
        {
            // make sure buddypress is loaded.
            if( ! function_exists( 'xprofile_set_field_data' ) )
            {
                return;
            }

            // check if profiles mapping is enabled
            $options = get_option( APSL_SETTINGS );
            $apsl_profile_mapping_options = $options['apsl_profile_mapping_options'];
            // $wsl_settings_buddypress_enable_mapping = get_option( 'wsl_settings_buddypress_enable_mapping' );
            
            if( $apsl_profile_mapping_options != 'yes' )
            {
                return;
            }

            // get current mapping
            $apsl_settings_buddypress_xprofile_map = $options['apsl_settings_buddypress_xprofile_map'];

            $hybridauth_fields = array(
                            'deutype',
                            'deuid',
                            'first_name',
                            'last_name',
                            'email',
                            'gender',
                            'deuimage',
                            'url',
                            'about'
                            );
            
            $user_profile_details = (array) $user_profile_details;

            // all check: start mapping process
            if( $apsl_settings_buddypress_xprofile_map )
            {
                foreach( $apsl_settings_buddypress_xprofile_map as $buddypress_field_id => $field_name )
                {

                    // if data can be found in hybridauth profile
                    if( in_array( $field_name, $hybridauth_fields ) )
                    {

                        $value = $user_profile_details[ $field_name ];

                        xprofile_set_field_data( $buddypress_field_id, $user_id, $value );

                    }

                    // if eq provider
                    if( $field_name == 'deutype' )
                    {
                        xprofile_set_field_data( $buddypress_field_id, $user_id, $provider );
                    }

                    // if eq birthDate
                    if( $field_name == 'birthDate' )
                    {
                        $value = 
                            str_pad( (int) $user_profile_details[ 'birthYear'  ], 4, '0', STR_PAD_LEFT )
                            . '-' . 
                            str_pad( (int) $user_profile_details[ 'birthMonth' ], 2, '0', STR_PAD_LEFT )
                            . '-' . 
                            str_pad( (int) $user_profile_details[ 'birthDay'   ], 2, '0', STR_PAD_LEFT )
                            . ' 00:00:00';

                        xprofile_set_field_data( $buddypress_field_id, $user_id, $value );
                    }
                }
            }
        }

        
        //function to retrive the user details from facebook
        static function facebookLogin( $response ) {
            $request = $_REQUEST;
            $site = self::siteUrl();
            $callBackUrl = self::callBackUrl();
            $response = new stdClass();
            $return_user_details = new stdClass();
            $exploder = explode( '_', $_GET['apsl_login_id'] );
            $action = $exploder[1];
            $options = get_option( APSL_SETTINGS );
            if(isset($options['apsl_facebook_settings']['apsl_profile_image_width'])){
                $width  = $options['apsl_facebook_settings']['apsl_profile_image_width'];
            }else{
                $width  = 150;
            }

            if(isset($options['apsl_facebook_settings']['apsl_profile_image_height'])){
                $height = $options['apsl_facebook_settings']['apsl_profile_image_height'];
            }else{
                $height = 150;
            }
            $config = array(
                'app_id' => $options['apsl_facebook_settings']['apsl_facebook_app_id'],
                'app_secret' => $options['apsl_facebook_settings']['apsl_facebook_app_secret'],
                'default_graph_version' => 'v2.4'
            );
            
            include( APSL_PLUGIN_DIR . 'facebook/autoload.php' );
            $fb = new Facebook\Facebook( $config );
            
            $encoded_url = isset( $_GET['redirect_to'] ) ? $_GET['redirect_to'] : '';
            
            if( isset( $encoded_url ) && $encoded_url != '' ) {
                $callback = $callBackUrl . 'apsl_login_id' . '=facebook_check&redirect_to=' . $encoded_url;
            } 
            else {
                $callback = $callBackUrl . 'apsl_login_id' . '=facebook_check';
            }
            
            if( $action == 'login' ) {
                
                // Well looks like we are a fresh dude, login to Facebook!
                $helper = $fb->getRedirectLoginHelper();
                $permissions = array(
                    'email',
                    'public_profile'
                );
                 // optional
                $loginUrl = $helper->getLoginUrl( $callback, $permissions );
                self:: redirect( $loginUrl );
            }
            else {
                if( isset( $_REQUEST['error'] ) ) {
                    $response->status = 'ERROR';
                    $response->error_code = 2;
                    $response->error_message = 'INVALID AUTHORIZATION';
                    return $response;
                    die();
                }
                if( isset( $_REQUEST['code'] ) ) {
                    $helper = $fb->getRedirectLoginHelper();
                    // Trick below will avoid "Cross-site request forgery validation failed. Required param "state" missing." from Facebook
                    $_SESSION['FBRLH_state'] = $_REQUEST['state'];
                    try {
                        $accessToken = $helper->getAccessToken();
                    }
                    catch( Facebook\Exceptions\FacebookResponseException $e ) {
                        
                        // When Graph returns an error
                        echo 'Graph returned an error: ' . $e->getMessage();
                        exit;
                    }
                    catch( Facebook\Exceptions\FacebookSDKException $e ) {
                        
                        // When validation fails or other local issues
                        echo 'Facebook SDK returned an error: ' . $e->getMessage();
                        exit;
                    }

                    if( isset( $accessToken ) ) {
                        
                        // Logged in!
                        $_SESSION['facebook_access_token'] = (string)$accessToken;
                        $fb->setDefaultAccessToken( $accessToken );
                        
                        try {
                            // $response = $fb->get( '/me?fields=email,name, first_name, last_name, gender, link, about, address, bio, birthday, education, hometown, is_verified, languages, location, website' );
                            $response = $fb->get( '/me?fields=email,name, first_name, last_name, age_range, picture, timezone, gender, link, about, birthday, education, hometown, verified, languages, location, website' );
                            $userNode = $response->getGraphUser();
                        }
                        catch( Facebook\Exceptions\FacebookResponseException $e ) {
                            
                            // When Graph returns an error
                            echo 'Graph returned an error: ' . $e->getMessage();
                            exit;
                        }
                        catch( Facebook\Exceptions\FacebookSDKException $e ) {
                            
                            // When validation fails or other local issues
                            echo 'Facebook SDK returned an error: ' . $e->getMessage();
                            exit;
                        }
                        
                        $user_profile = self:: accessProtected( $userNode, 'items' );
                        if( $user_profile != null ) {
                            $return_user_details->status = 'SUCCESS';
                            $return_user_details->deuid = $user_profile['id'];
                            $return_user_details->deutype = 'facebook';
                            $return_user_details->first_name = $user_profile['first_name'];
                            $return_user_details->last_name = $user_profile['last_name'];
                            if(isset($user_profile['email']) || $user_profile['email'] != ''){
                                $user_email = $user_profile['email'];
                            }else{
                                $user_email = $user_profile['id'].'@facebook.com';
                            }
                            $return_user_details->email = $user_email;
                            $return_user_details->username = ($user_profile['first_name'] !='') ? strtolower( $user_profile['first_name'] ) : $user_email;
                            $return_user_details->gender = isset($user_profile['gender']) ? $user_profile['gender'] : 'N/A';
                            $return_user_details->url = $user_profile['link'];
                            $return_user_details->about = '';

                             //facebook doesn't return user about details.
                            $headers = get_headers( 'https://graph.facebook.com/' . $user_profile['id'] . '/picture?width='.$width.'&height='.$height, 1 );
                            // just a precaution, check whether the header isset...
                            if( isset( $headers['Location'] ) ) {
                                $return_user_details->deuimage = $headers['Location']; // string
                            }
                            else {
                                $return_user_details->deuimage = false; // nothing there? .. weird, but okay!
                            }
                            $return_user_details->error_message = '';
                        }
                        else {
                            $return_user_details->status = 'ERROR';
                            $return_user_details->error_code = 2;
                            $return_user_details->error_message = 'INVALID AUTHORIZATION';
                        }
                    }
                }
                else {
                    
                    // Well looks like we are a fresh dude, login to Facebook!
                    $helper = $fb->getRedirectLoginHelper();
                    $permissions = array(
                        'email',
                        'public_profile'
                    );
                     // optional
                    $loginUrl = $helper->getLoginUrl( $callback, $permissions );
                    self:: redirect( $loginUrl );

                }
            }

            return $return_user_details;
        }

        //function to retrive the user details from twitter
        static function twitterLogin() {
            $request = $_REQUEST;
            $site = self::siteUrl();
            $callBackUrl = self::callBackUrl();
            $response = new stdClass();
            $exploder = explode( '_', $_GET['apsl_login_id'] );
            $action = $exploder[1];
            @session_start();
            $options = get_option( APSL_SETTINGS );
            if( $action == 'login' ) {

                // Get identity from user and redirect browser to OpenID Server
                if( !isset( $request['oauth_token'] ) || $request['oauth_token'] == '' ) {
                    $twitterObj = new TwitterOAuth( $options['apsl_twitter_settings']['apsl_twitter_api_key'], $options['apsl_twitter_settings']['apsl_twitter_api_secret'] );
                    $encoded_url = isset( $_GET['redirect_to'] ) ? $_GET['redirect_to'] : '';
                    if( isset( $encoded_url ) && $encoded_url != '' ) {
                        $callback = $callBackUrl . 'apsl_login_id' . '=twitter_check&redirect_to=' . $encoded_url;
                    } 
                    else {
                        $callback = $callBackUrl . 'apsl_login_id' . '=twitter_check';
                    }
                    
                    $request_token = $twitterObj->getRequestToken( $callback );
                    $_SESSION['oauth_twitter'] = array();
                    
                    /* Save temporary credentials to session. */
                    $_SESSION['oauth_twitter']['oauth_token'] = $token = $request_token['oauth_token'];
                    $_SESSION['oauth_twitter']['oauth_token_secret'] = $request_token['oauth_token_secret'];
                    
                    /* If last connection failed don't display authorization link. */
                    switch( $twitterObj->http_code ) {
                        case 200:
                            try {
                                $url = $twitterObj->getAuthorizeUrl( $token );
                                self:: redirect( $url );
                            }
                            catch( Exception $e ) {
                                $response->status = 'ERROR';
                                $response->error_code = 2;
                                $response->error_message = 'Could not get AuthorizeUrl.';
                            }
                            break;

                        default:
                            $response->status = 'ERROR';
                            $response->error_code = 2;
                            $response->error_message = 'Could not connect to Twitter. Refresh the page or try again later.';
                            break;
                    }
                } 
                else {
                    $response->status = 'ERROR';
                    $response->error_code = 2;
                    $response->error_message = 'INVALID AUTHORIZATION';
                }
            } 
            else if( isset( $request['oauth_token'] ) && isset( $request['oauth_verifier'] ) ) {
                
                /* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
                $twitterObj = new TwitterOAuth( $options['apsl_twitter_settings']['apsl_twitter_api_key'], $options['apsl_twitter_settings']['apsl_twitter_api_secret'], $_SESSION['oauth_twitter']['oauth_token'], $_SESSION['oauth_twitter']['oauth_token_secret'] );
                
                /* Remove no longer needed request tokens */
                unset( $_SESSION['oauth_twitter'] );
                try {
                    $access_token = $twitterObj->getAccessToken( $request['oauth_verifier'] );
                    
                    /* If HTTP response is 200 continue otherwise send to connect page to retry */
                    if( 200 == $twitterObj->http_code ) {
                        $user_profile = $twitterObj->get( 'account/verify_credentials', array(
                                                                                            'screen_name' => $access_token['screen_name'],
                                                                                            'skip_status' => 'true',
                                                                                            'include_entities' => 'true',
                                                                                            'include_email' => 'true'
                                                                                        )
                                                        );

                        /* Request access twitterObj from twitter */
                        $response->status = 'SUCCESS';
                        $response->deuid = $user_profile->id;
                        $response->deutype = 'twitter';
                        $response->name = explode( ' ', $user_profile->name, 2 );
                        $response->first_name = $response->name[0];
                        $response->last_name =( isset( $response->name[1] ) ) ? $response->name[1] : '';

                        $response->deuimage = str_replace( '_normal', '', $user_profile->profile_image_url_https );
                         //$user_profile->profile_image_url_https;
                        $response->email = isset($user_profile->email) ? $user_profile->email : $user_profile->screen_name . '@twitter.com';
                        $response->username = ($user_profile->screen_name !='') ? strtolower($user_profile->screen_name) : $response->name[0];
                        $response->url = $user_profile->url;
                        $response->about = $user_profile->description;
                        $response->gender = isset( $user_profile->gender ) ? $user_profile->gender : 'N/A';
                        $response->location = $user_profile->location;
                        $response->error_message = '';
                    }
                    else {
                        $response->status = 'ERROR';
                        $response->error_code = 2;
                        $response->error_message = 'Could not connect to Twitter. Refresh the page or try again later.';
                    }
                }
                catch( Exception $e ) {
                    $response->status = 'ERROR';
                    $response->error_code = 2;
                    $response->error_message = 'Could not get AccessToken.';
                }
            }
            else {
                 // User Canceled your Request
                $response->status = 'ERROR';
                $response->error_code = 1;
                $response->error_message = "USER CANCELED REQUEST";
            }
            
            return $response;
        }
        
        //function to rerive the user details from google
        static function GoogleLogin() {
            $post = $_POST;
            $get = $_GET;
            $request = $_REQUEST;
            $site = self::siteUrl();
            $callBackUrl = self::callBackUrl();
            $options = get_option( APSL_SETTINGS );
            $response = new stdClass();
            $a = explode( '_', $_GET['apsl_login_id'] );
            $action = $a[1];
            $client_id = $options['apsl_google_settings']['apsl_google_client_id'];
            $client_secret = $options['apsl_google_settings']['apsl_google_client_secret'];
            
            $encoded_url = isset( $_GET['redirect_to'] ) ? $_GET['redirect_to'] : '';
            if( isset( $encoded_url ) && $encoded_url != '' ) {
                $callback = $callBackUrl . 'apsl_login_id' . '=google_check';
            } 
            else {
                $callback = $callBackUrl . 'apsl_login_id' . '=google_check';
            }
            
            $redirect_uri = $callback;
            
            $client = new Google_Client;
            $client->setClientId( $client_id );
            $client->setClientSecret( $client_secret );
            $client->setRedirectUri( $redirect_uri );
            $client->addScope( "https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/plus.profile.emails.read" );
            if( isset( $encoded_url ) && $encoded_url != '' ) {
                $client->setState( base64_encode( "redirect_to=$encoded_url" ) );
            }
            
            $service = new Google_Service_Plus( $client );
            
            if( $action == 'login' ) {
                unset($_SESSION['access_token']);
                // Get identity from user and redirect browser to OpenID Server
                if( !( isset( $_SESSION['access_token'] ) && $_SESSION['access_token'] ) ) {
                    $authUrl = $client->createAuthUrl();
                    self:: redirect( $authUrl );
                    die();
                } 
                else {
                    self:: redirect( $redirect_uri."&redirect_to=$encoded_url" );
                    die();
                }
            } 
            elseif( isset( $_GET['code'] ) ) {
                 // Perform HTTP Request to OpenID server to validate key
                $client->authenticate( $_GET['code'] );
                $_SESSION['access_token'] = $client->getAccessToken();
                self:: redirect( $redirect_uri."&redirect_to=$encoded_url" );
                die();
            } 
            elseif( isset( $_SESSION['access_token'] ) && $_SESSION['access_token'] ) {
                $client->setAccessToken( $_SESSION['access_token'] );
                try {
                    $user = $service->people->get( "me", array() );
                }
                catch( Exception $fault ) {
                    unset( $_SESSION['access_token'] );
                    $ref_object = self:: accessProtected( $fault, 'errors' );
                    echo $ref_object[0]['message'] . " Please notify about this error to the Site Admin.";
                    die();
                }
                
                if( !empty( $user ) ) {
                    if( !empty( $user->emails ) ) {
                        $response->email = $user->emails[0]->value;
                        $response->username = ($user->name->givenName !='') ? strtolower($user->name->givenName) : $user->emails[0]->value;
                        $response->first_name = $user->name->givenName;
                        $response->last_name = $user->name->familyName;
                        $response->deuid = $user->id;
                         //$user->emails[0]->value;
                        $imageUrl = substr($user->image->url,0,strpos($user->image->url."?sz=","?sz=")) . '?sz=450';
                        $response->deuimage = $imageUrl;
                        $response->gender = isset($user->gender) ? $user->gender : 'N/A';
                        $response->id = isset($user->id) ? $user->id : '';
                        $response->about = isset($user->aboutMe) ? $user->aboutMe : '';
                        $response->url = isset($user->url) ? $user->url : '';
                        $response->deutype = 'google';
                        $response->status = 'SUCCESS';
                        $response->error_message = 'no error';
                    }
                    else {
                        $response->status = 'ERROR';
                        $response->error_code = 2;
                        $response->error_message = "INVALID AUTHORIZATION";
                    }
                } 
                else {
                     // Signature Verification Failed
                    $response->status = 'ERROR';
                    $response->error_code = 2;
                    $response->error_message = "INVALID AUTHORIZATION";
                }
            }else {
                 // User failed to login
                $response->status = 'ERROR';
                $response->error_code = 3;
                $response->error_message = "USER LOGIN FAIL";
            }
            return $response;
        }
        
        //function to retrive the user details from linkedin
        static function linkedInLogin() {
            $post = $_POST;
            $get = $_GET;
            $request = $_REQUEST;
            $site = self::siteUrl();
            $callBackUrl = self::callBackUrl();
            $response = new stdClass();
            $exploder = explode( '_', $_GET['apsl_login_id'] );
            $action = $exploder[1];
            $options = get_option( APSL_SETTINGS );
            
            $encoded_url = isset( $_GET['redirect_to'] ) ? $_GET['redirect_to'] : '';
            if( isset( $encoded_url ) && $encoded_url != '' ) {
                $callback = $callBackUrl . 'apsl_login_id' . '=linkedin_check&redirect_to=' . $encoded_url;
            }
            else {
                $callback = $callBackUrl . 'apsl_login_id' . '=linkedin_check';
            }
            
            $API_CONFIG = array(
                'appKey' => $options['apsl_linkedin_settings']['apsl_linkedin_client_id'],
                'appSecret' => $options['apsl_linkedin_settings']['apsl_linkedin_client_secret'],
                'callbackUrl' => $callback
            );
            @session_start();
            $OBJ_linkedin = new LinkedIn( $API_CONFIG );
            if( $action == 'login' ) {
                
                // send a request for a LinkedIn access token
                $response_server = $OBJ_linkedin->retrieveTokenRequest( array(
                    'scope' => 'r_emailaddress'
                ) );
                if( $response_server['success'] === TRUE ) {
                    $_SESSION['oauth_linkedin'] = $response_server['linkedin'];
                    
                    // redirect the user to the LinkedIn authentication/authorisation page to initiate validation.
                    self:: redirect( LINKEDIN::_URL_AUTH . $response_server['linkedin']['oauth_token'] );
                } 
                else {
                    $response->status = 'ERROR';
                    $response->error_code = 1;
                    $response->error_message = 'Request token retrieval failed';
                }
            } 
            elseif( isset( $_GET['oauth_verifier'] ) ) {
                
                // LinkedIn has sent a response, user has granted permission, take the temp access token, the user's secret and the verifier to request the user's real secret key
                $response1 = $OBJ_linkedin->retrieveTokenAccess( $_SESSION['oauth_linkedin']['oauth_token'], $_SESSION['oauth_linkedin']['oauth_token_secret'], $_GET['oauth_verifier'] );
                
                if( $response1['success'] === TRUE ) {
                    $OBJ_linkedin->setTokenAccess( $response1['linkedin'] );
                    $OBJ_linkedin->setResponseFormat( LINKEDIN::_RESPONSE_JSON );
                    $response2 = $OBJ_linkedin->profile( '~:(id,email-address,first-name,last-name,picture-url,headline,location,summary,public-profile-url)' );

                    if( $response2['success'] === TRUE ) {
                        $data = json_decode( $response2['linkedin'] );
                        $response->status = 'SUCCESS';
                        $response->deutype = 'linkedin';
                        $response->first_name = $data->firstName;
                        $response->last_name = $data->lastName;
                        $response->email = $data->emailAddress;
                        $response->username = ($data->firstName !='') ? strtolower($data->firstName) : $data->emailAddress;
                        $response->deuid = $data->id;
                        $response->deuimage = $data->pictureUrl;
                        $response->url = $data->publicProfileUrl;
                        $response->about = isset($data->summary) ? $data->summary : 'N/A';
                        $response->gender = isset( $user_profile->gender ) ? $user_profile->gender : 'N/A';
                        $response->error_message = '';
                    } 
                    else {
                        $response->status = 'ERROR';
                        $response->error_code = 2;
                        $response->error_message = 'Error retrieving profile information';
                    }
                } 
                else {
                    $response->status = 'ERROR';
                    $response->error_code = 1;
                    $response->error_message = 'Access token retrieval failed';
                }
            } 
            else {
                $response->status = 'ERROR';
                $response->error_code = 1;
                if( isset( $get['oauth_problem'] ) && $get['oauth_problem'] == 'user_refused' ) {
                    $response->error_message = 'Access token retrieval failed';
                } 
                else {
                    $response->error_message = 'Request cancelled by user!';
                }
            }
            
            return $response;
        }
        
        //function to retrive the user details from instagram
        static function instagramLogin() {
            $request = $_REQUEST;
            $site = self::siteUrl();
            $callBackUrl = self::callBackUrl();
            $response = new stdClass();
            $exploder = explode( '_', $_GET['apsl_login_id'] );
            $action = $exploder[1];
            $options = get_option( APSL_SETTINGS );
            
            $encoded_url = isset( $_GET['redirect_to'] ) ? $_GET['redirect_to'] : '';

            if( isset( $encoded_url ) && $encoded_url != '' ) {
                $callback = $callBackUrl . 'apsl_login_id' . '=instagram_check&redirect_to=' . $encoded_url;
            } 
            else {
                $callback = $callBackUrl . 'apsl_login_id' . '=instagram_check';
            }
            
            $config = array(
                'apiKey' => $options['apsl_instagram_settings']['apsl_instagram_api_key'],
                'apiSecret' => $options['apsl_instagram_settings']['apsl_instagram_api_secret'],
                'apiCallback' => $callback
            );
            
            $instagram = new Instagram( $config );
            if( $action == 'login' ) {
                $loginUrl = $instagram->getLoginUrl();
                self:: redirect( $loginUrl );
                exit();
            } 
            else {
                
                // Receive OAuth code parameter
                
                // Check whether the user has granted access
                if( true === isset( $_GET['code'] ) ) {
                    $code = $_GET['code'];
                    try {
                         // Proceed knowing you have a logged in user who's authenticated.
                        // Receive OAuth token object
                        $user_profile = $instagram->getOAuthToken( $code );
                    }
                    catch( InstagramException $e ) {
                        error_log( $e );
                        $user_profile = null;
                    }
                }else{
                    $user_profile = null;
                }
                
                if( $user_profile != null ) {
                    $response->status = 'SUCCESS';
                    $response->deuid = $user_profile->user->id;
                    $response->deutype = 'instagram';
                    $response->name = explode( ' ', $user_profile->user->full_name, 2 );
                    $response->first_name = $response->name[0];
                    $response->last_name =( isset( $response->name[1] ) ) ? $response->name[1] : '';
                    $response->deuimage = $user_profile->user->profile_picture;
                    $response->email = $user_profile->user->username . '@instagram.com';
                    $response->username = ($user_profile->user->username !='') ? strtolower($user_profile->user->username) : $response->name[0];
                    $response->url = $user_profile->user->website;
                    $response->about = $user_profile->user->bio;
                    $response->gender = isset( $user_profile->gender ) ? $user_profile->gender : 'N/A';
                    $response->error_message = '';
                } 
                else {
                    $response->status = 'ERROR';
                    $response->error_code = 2;
                    $response->error_message = 'INVALID AUTHORIZATION';
                }
                return $response;
            }
        }
        
        //function to retrive the user details from vk
        static function vkLogin() {
            $post = $_POST;
            $get = $_GET;
            $request = $_REQUEST;
            $site = self::siteUrl();
            $callBackUrl = self::callBackUrl();
            $response = new stdClass();
            $a = explode( '_', $_GET['apsl_login_id'] );
            $action = $a[1];
            $options = get_option( APSL_SETTINGS );
            $response = new stdClass();
            $encoded_url = isset( $_GET['redirect_to'] ) ? urldecode($_GET['redirect_to']) : site_url();
            if( isset( $encoded_url ) && $encoded_url != '' ) {
                $callback = $callBackUrl . 'apsl_login_id' . '=vk_check&redirect_to=' . $encoded_url;
            }else {
                $callback = $callBackUrl . 'apsl_login_id' . '=vk_check';
            }
            $app_id = $options['apsl_vk_settings']['apsl_vk_app_id'];
            $secure_key = $options['apsl_vk_settings']['apsl_vk_secure_key'];
            
            if( $action == 'login' ) {
                $scope = 'status,photos,notify,email';
                $redirect_uri = $callback;
                $vk = new VK( $app_id, $secure_key );
                
                if( !isset( $_REQUEST['code'] ) ) {
                    
                    /**
                     * If you need switch the application in test mode,
                     * add another parameter "true". Default value "false".
                     * Ex. $vk->getAuthorizeURL($api_settings, $callback_url, true);
                     */
                    $authorize_url = $vk->getAuthorizeURL( $scope, $redirect_uri );
                    self:: redirect( $authorize_url );
                }
            }
            else if( isset( $_GET['code'] ) && $_GET['code'] != '' ) {
                $curl = curl_init( 'https://oauth.vk.com/access_token' );
                curl_setopt( $curl, CURLOPT_POST, true );
                curl_setopt( $curl, CURLOPT_POSTFIELDS, array(
                    'client_id' => $app_id,
                    'client_secret' => $secure_key,
                    'code' => $_GET['code'],
                     // The code from the previous request
                    'redirect_uri' => $callback,
                    
                    // 'grant_type' => 'authorization_code'
                    
                ) );
                curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
                $auth = curl_exec( $curl );
                $secret = json_decode( $auth );
                $user_email = $secret->email;
                $access_key = $secret->access_token;
                $user_id = $secret->user_id;
                $curl_url2 = 'https://api.vk.com/method/users.get?user_ids=' . $user_id . '&fields=sex,bdate,city,country,photo_200,about,screen_name,nickname';
                $curl2 = curl_init( $curl_url2 );
                curl_setopt( $curl2, CURLOPT_RETURNTRANSFER, 1 );
                $curl3 = curl_exec( $curl2 );
                $json_more_user_details = json_decode( $curl3, true );
                if( $json_more_user_details != null ) {
                    $response->status = 'SUCCESS';
                    $response->deuid = $json_more_user_details['response'][0]['uid'];
                    $response->deutype = 'vk';
                    $response->first_name = $json_more_user_details['response'][0]['first_name'];
                    $response->last_name = $json_more_user_details['response'][0]['last_name'];
                    $response->deuimage = $json_more_user_details['response'][0]['photo_200'];
                    $response->email = (isset($user_email) && $user_email !='') ? $user_email : $json_more_user_details['response'][0]['screen_name'] . '@vk.com';
                    $response->username = isset($json_more_user_details['response'][0]['first_name']) ? strtolower($json_more_user_details['response'][0]['first_name']) : strtolower( $json_more_user_details['response'][0]['uid'] );
                    $response->gender = isset( $user_profile->gender ) ? $user_profile->gender : 'N/A';
                    $response->url = '';
                    $response->about = '';
                    $response->error_message = '';
                }
                else {
                    $response->status = 'ERROR';
                    $response->error_code = 2;
                    $response->error_message = 'INVALID AUTHORIZATION';
                }
            }else{
                $response->status = 'ERROR';
                $response->error_code = 3;
                $response->error_message = 'USER DENIED ACCESS';
            }
            return $response;
        }
        
        //function to retrive the user details from foursquare
        static function fourSquareLogin() {
            $post = $_POST;
            $get = $_GET;
            $request = $_REQUEST;
            $site = self::siteUrl();
            $callBackUrl = self::callBackUrl();
            $response = new stdClass();
            $a = explode( '_', $_GET['apsl_login_id'] );
            $action = $a[1];
            $options = get_option( APSL_SETTINGS );
            $response = new stdClass();
            $encoded_url = isset( $_GET['redirect_to'] ) ? $_GET['redirect_to'] : '';
            if( isset( $encoded_url ) && $encoded_url != '' ) {
                $callback = $callBackUrl . 'apsl_login_id' . '=foursquare_check&redirect_to=' . $encoded_url;
            } 
            else {
                $callback = $callBackUrl . 'apsl_login_id' . '=foursquare_check';
            }
            $client_id = $options['apsl_foursquare_settings']['apsl_foursquare_client_id'];
            $client_secret = $options['apsl_foursquare_settings']['apsl_foursquare_client_secret'];
            if( $action == 'login' ) {
                $redirect_uri = $callback;
                $loginurl_new = "https://foursquare.com/oauth2/authenticate?client_id=" . $client_id . "&response_type=code&redirect_uri=" . $redirect_uri;
                self:: redirect( $loginurl_new );
                die();
            }
            else if( isset( $_GET['code'] ) && $_GET['code'] != '' ) {
                $code = $_GET['code'];
                $redirect_uri = $callback;
                $loginurl_new_one = "https://foursquare.com/oauth2/access_token?client_id=" . $client_id . "&client_secret=" . $client_secret . "&grant_type=authorization_code&redirect_uri=" . $redirect_uri . "&code=" . $code;
                $json_string = self:: get_json_values( $loginurl_new_one );
                $json = json_decode( $json_string, true );
                $access_token = $json['access_token'];
                
                $url_with_token = 'https://api.foursquare.com/v2/users/self?oauth_token=' . $access_token . '&v=' . date( 'YY MM DD' );
                
                //get user details in json format
                $json_strings_another = self:: get_json_values( $url_with_token );
                $json_user_details = json_decode( $json_strings_another, true );
                
                if( $json_user_details != null ) {
                    $response->status = 'SUCCESS';
                    $response->deuid = $json_user_details['response']['user']['id'];
                    $response->deutype = 'foursquare';
                    $response->first_name = $json_user_details['response']['user']['firstName'];
                    $response->last_name =( isset( $json_user_details['response']['user']['lastName'] ) ) ? $json_user_details['response']['user']['lastName'] : '';
                    $response->deuimage = $json_user_details['response']['user']['photo']['prefix'] . '300x300' . $json_user_details['response']['user']['photo']['suffix'];
                    $response->email = $json_user_details['response']['user']['contact']['email'];
                    $response->username = ($json_user_details['response']['user']['firstName'] !='') ? strtolower( $json_user_details['response']['user']['firstName'] ) : $json_user_details['response']['user']['id'];
                    $response->gender = isset($json_user_details['response']['user']['gender']) ? $json_user_details['response']['user']['gender'] : 'N/A';
                    $response->url = '';
                    $response->about = $json_user_details['response']['user']['bio'];
                    $response->location = $json_user_details['response']['user']['homeCity'];
                    $response->error_message = '';
                } 
                else {
                    $response->status = 'ERROR';
                    $response->error_code = 2;
                    $response->error_message = 'INVALID AUTHORIZATION';
                }
            }else {
                $response->status = 'ERROR';
                $response->error_code = 3;
                $response->error_message = 'USER ACCESS DENIED';
            }
            return $response;
        }
        
        //function to retrive the user details from wordpress
        static function wordPressLogin() {
            $post = $_POST;
            $get = $_GET;
            $request = $_REQUEST;
            $site = self::siteUrl();
            $callBackUrl = self::callBackUrl();
            $response = new stdClass();
            $a = explode( '_', $_GET['apsl_login_id'] );
            $action = $a[1];
            $options = get_option( APSL_SETTINGS );
            $response = new stdClass();
            
            $encoded_url = isset( $_GET['redirect_to'] ) ? $_GET['redirect_to'] : '';
            if( isset( $encoded_url ) && $encoded_url != '' ) {
                $callback = $callBackUrl . 'apsl_login_id' . '=wordpress_check';
            } 
            else {
                $callback = $callBackUrl . 'apsl_login_id' . '=wordpress_check';
            }
            $client_id = $options['apsl_wordpress_settings']['apsl_wordpress_client_id'];
            $client_secret = $options['apsl_wordpress_settings']['apsl_wordpress_client_secret'];;
            if( $action == 'login' ) {
                $redirect_uri = $callback;
                $login_url_new = 'https://public-api.wordpress.com/oauth2/authorize?client_id=' . $client_id . '&redirect_uri=' . $redirect_uri . '&response_type=code';
                self:: redirect( $login_url_new );
                die();
            } 
            else if( isset( $_GET['code'] ) && $_GET['code'] != '' ) {
                $curl = curl_init( 'https://public-api.wordpress.com/oauth2/token' );
                curl_setopt( $curl, CURLOPT_POST, true );
                curl_setopt( $curl, CURLOPT_POSTFIELDS, array(
                    'client_id' => $client_id,
                    'redirect_uri' => $callback,
                    'client_secret' => $client_secret,
                    'code' => $_GET['code'],
                     // The code from the previous request
                    'grant_type' => 'authorization_code'
                ) );
                curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
                $auth = curl_exec( $curl );
                $secret = json_decode( $auth );
                $access_key = $secret->access_token;
                
                $curl1 = curl_init( 'https://public-api.wordpress.com/rest/v1/me/' );
                curl_setopt( $curl1, CURLOPT_HTTPHEADER, array(
                    'Authorization: Bearer ' . $access_key
                ) );
                curl_setopt( $curl1, CURLOPT_RETURNTRANSFER, 1 );
                $curl = curl_exec( $curl1 );
                $json_user_details = json_decode( $curl, true );

                if( $json_user_details != null ) {
                    $response->status = 'SUCCESS';
                    $response->deuid = $json_user_details['ID'];
                    $response->deutype = 'wordpress';
                    $response->first_name = $json_user_details['display_name'];
                    $response->last_name = '';
                    $response->deuimage = $json_user_details['avatar_URL'];
                    $response->email = $json_user_details['email'];
                    $response->username = ($json_user_details['username']) ? strtolower( $json_user_details['username'] ) : $json_user_details['display_name'];
                    $response->url = '';
                    $response->about = '';
                    $response->gender = 'N/A';
                    $response->error_message = '';
                } 
                else {
                    $response->status = 'ERROR';
                    $response->error_code = 2;
                    $response->error_message = 'INVALID AUTHORIZATION';
                }
            }else{
                    $response->status = 'ERROR';
                    $response->error_code = 3;
                    $response->error_message = 'USER DENIED ACCESS';
            }
            return $response;
        }
        
        //function to retrive the user details form buffer
        static function bufferLogin() {
            $post = $_POST;
            $get = $_GET;
            $request = $_REQUEST;
            $site = self::siteUrl();
            $callBackUrl = self::callBackUrl();
            $response = new stdClass();
            $a = explode( '_', $_GET['apsl_login_id'] );
            $action = $a[1];
            $options = get_option( APSL_SETTINGS );
            $response = new stdClass();
            $encoded_url = isset( $_GET['redirect_to'] ) ? $_GET['redirect_to'] : '';
            
            if( isset( $encoded_url ) && $encoded_url != '' ) {
                $callback = $callBackUrl . 'apsl_login_id' . '=buffer_check&redirect_to=' . $encoded_url;
            } 
            else {
                $callback = $callBackUrl . 'apsl_login_id' . '=buffer_check';
            }
            
            $client_id = $options['apsl_buffer_settings']['apsl_buffer_client_id'];
            $client_secret = $options['apsl_buffer_settings']['apsl_buffer_client_secret'];
            if( $action == 'login' ) {
                $login_url_new = 'https://bufferapp.com/oauth2/authorize?client_id=' . $client_id . '&redirect_uri=' . $callback . '&response_type=code';
                self:: redirect( $login_url_new );
                die();
            } 
            else if( isset( $_GET['code'] ) && $_GET['code'] != '' ) {
                $curl = curl_init( 'https://api.bufferapp.com/1/oauth2/token.json' );
                curl_setopt( $curl, CURLOPT_POST, true );
                curl_setopt( $curl, CURLOPT_POSTFIELDS, array(
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'code' => $_GET['code'],
                     // The code from the previous request
                    'redirect_uri' => $callback,
                    'grant_type' => 'authorization_code'
                ) );
                curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
                $auth = curl_exec( $curl );
                $secret = json_decode( $auth );
                $access_key = $secret->access_token;
                $curl_url2 = 'https://api.bufferapp.com/1/profiles.json?access_token=' . $access_key;
                $curl2 = curl_init( $curl_url2 );
                curl_setopt( $curl2, CURLOPT_RETURNTRANSFER, 1 );
                $curl3 = curl_exec( $curl2 );
                $json_more_user_details = json_decode( $curl3, true );
                
                if( $json_more_user_details != '' ) {
                    $response->status = 'SUCCESS';
                    $response->deuid = $json_more_user_details[1]['_id'];
                    $response->deutype = 'buffer';
                    $response->name = explode( ' ', $json_more_user_details[1]['formatted_username'], 2 );
                    $response->first_name = $response->name[0];
                    $response->last_name =( isset( $response->name[1] ) ) ? $response->name[1] : '';
                    
                    //(isset($json_user_details['display_name']['last'])) ? $$json_user_details['display_name']['last'] : '';
                    $response->deuimage = $json_more_user_details[1]['avatar'];
                    $response->email = $json_more_user_details[1]['_id'] . '@buffer.com';
                    $response->username = (isset($response->name[0]) ) ? strtolower( $response->name[0] ) : $json_more_user_details[1]['_id'] . '_buffer';
                    $response->url = '';
                    $response->about = '';
                    $response->gender = '';
                    $response->error_message = '';
                } 
                else {
                    $response->status = 'ERROR';
                    $response->error_code = 2;
                    $response->error_message = 'INVALID AUTHORIZATION';
                }
            }else{
                    $response->status = 'ERROR';
                    $response->error_code = 3;
                    $response->error_message = 'USER DENIED ACCESS';
            }
            return $response;
        }
        
        static function siteUrl() {
            return site_url();
        }
        
        Public static function callBackUrl() {
            // $connection = !empty( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
            // $url = $connection . $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"];
            $url = wp_login_url();
            if( strpos( $url, '?' ) === false ) {
                $url.= '?';
            } 
            else {
                $url.= '&';
            }
            return $url;
        }
        
        //function to return json values from social media urls
        static function get_json_values( $url ) {
            $response = wp_remote_get( $url );
            $json_response = wp_remote_retrieve_body( $response );
            return $json_response;
        }
        
        static function redirect( $redirect ) {
            if( headers_sent() ) {
                
                // Use JavaScript to redirect if content has been previously sent (not recommended, but safe)
                echo '<script language="JavaScript" type="text/javascript">window.location=\'';
                echo $redirect;
                echo '\';</script>';
            } 
            else {
                
                // Default Header Redirect
                header( 'Location: ' . $redirect );
            }
            exit;
        }
        
        static function updateUser( $username, $email ) {
            $row = $this->getUserByUsername( $username );
            if( $row && $email != '' && $row->user_email != $email ) {
                $row = (array)$row;
                $row['user_email'] = $email;
                wp_update_user( $row );
            }
        }
        
        static function getUserByMail( $email ) {
            global $wpdb;
            $row = $wpdb->get_row( "SELECT * FROM $wpdb->users WHERE user_email = '$email'" );
            if( $row ) {
                return $row;
            }
            return false;
        }
        
        static function getUserByUsername( $username ) {
            global $wpdb;
            $row = $wpdb->get_row( "SELECT * FROM $wpdb->users WHERE user_login = '$username'" );
            if( $row ) {
                return $row;
            }
            return false;
        }

        static function get_username($user_name){
            $username = $user_name;
            $i = 1;
            while(username_exists( $username )){
                $username = $user_name.'_'.$i;
                $i++;
            }
            return $username;
        }
        
        static function creatUser( $user_name, $user_email ) {
            $username = self:: get_username($user_name);
            $random_password = wp_generate_password( 12, false );
            $user_id = wp_create_user( $username, $random_password, $user_email );
            
            //do_action( 'bp_core_activated_user', $user_id );
            $options = get_option( APSL_SETTINGS );
                if( $options['apsl_send_email_notification_options'] == 'yes' ) {
                    if (version_compare(get_bloginfo('version'), '4.3.1', '>=')){
                        wp_new_user_notification( $user_id, $deprecated = null, $notify = 'both' );
                    }else{
                        wp_new_user_notification( $user_id, $random_password );
                    }
                }
            return $user_id;
        }
        
        static function set_cookies( $user_id = 0, $remember = true ) {
            if( !function_exists( 'wp_set_auth_cookie' ) ) {
                return false;
            }
            if( !$user_id ) {
                return false;
            }
            wp_clear_auth_cookie();
            wp_set_auth_cookie( $user_id, $remember );
            wp_set_current_user( $user_id );
            return true;
        }

        static function loginUser( $user_id ) {
            $reauth = empty( $_REQUEST['reauth'] ) ? false : true;
            if( $reauth )wp_clear_auth_cookie();

            if( isset( $_REQUEST['redirect_to'] ) ) {
                $redirect_to = $_REQUEST['redirect_to'];

                // Redirect to https if user wants ssl
                if( isset( $secure_cookie ) && false !== strpos( $redirect_to, 'wp-admin' ) )$redirect_to = preg_replace( '|^http://|', 'https://', $redirect_to );
            }
            else {
                $options = get_option( APSL_SETTINGS );
                if( isset( $options['apsl_custom_login_redirect_options'] ) && $options['apsl_custom_login_redirect_options'] != '' ) {
                    if( $options['apsl_custom_login_redirect_options'] == 'home' ) {
                        $user_login_url = home_url();
                    }
                    else if( $options['apsl_custom_login_redirect_options'] == 'current_page' ) {
                        if( isset( $_REQUEST['redirect_to'] ) ) {
                            $redirect_to = $_REQUEST['redirect_to'];
                            
                            // Redirect to https if user wants ssl
                            if( isset( $secure_cookie ) && false !== strpos( $redirect_to, 'wp-admin' ) )$user_login_url = preg_replace( '|^http://|', 'https://', $redirect_to );
                        }else {
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
                $redirect_to = $user_login_url;
            }

            if( !isset( $secure_cookie ) && is_ssl() && force_ssl_login() && !force_ssl_admin() &&( 0 !== strpos( $redirect_to, 'https' ) ) &&( 0 === strpos( $redirect_to, 'http' ) ) )$secure_cookie = false;

            // If cookies are disabled we can't log in even with a valid user+pass
            if( isset( $_POST['testcookie'] ) && empty( $_COOKIE[TEST_COOKIE] ) )$user = new WP_Error( 'test_cookie', __( "<strong>ERROR</strong>: Cookies are blocked or not supported by your browser. You must <a href='http://www.google.com/cookies.html'>enable cookies</a> to use WordPress." ) );
            else $user = wp_signon( '', isset( $secure_cookie ) );
            
            if( ! self:: set_cookies( $user_id ) ) {
                return false;
            }
            
            $requested_redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : site_url();
            $user_login_url = apply_filters( 'login_redirect', $redirect_to, $requested_redirect_to, $user );
            
            $redirect_to = $user_login_url;
            echo "<script> window.close(); window.opener.location.href='$redirect_to'; </script>";
            // wp_safe_redirect( $redirect_to );
            exit();
        }
        
        //returns the current page url
        static function curPageURL() {
            $pageURL = 'http';
            if( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) {
                $pageURL.= "s";
            }
            $pageURL.= "://";
            if( $_SERVER["SERVER_PORT"] != "80" ) {
                $pageURL.= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
            } 
            else {
                $pageURL.= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
            }
            return $pageURL;
        }
        
        //function to access the protected object properties
        static function accessProtected( $obj, $prop ) {
            $reflection = new ReflectionClass( $obj );
            $property = $reflection->getProperty( $prop );
            $property->setAccessible( true );
            return $property->getValue( $obj );
        }
        
        //wsl function
        static function apsl_get_current_url() {
            
            //Extract parts
            $request_uri =( isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF'] );
            $request_protocol =( wsl_is_https_on() ? 'https' : 'http' );
            $request_host =( isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ? $_SERVER['HTTP_X_FORWARDED_HOST'] :( isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'] ) );
            
            //Port of this request
            $request_port = '';
            
            //We are using a proxy
            if( isset( $_SERVER['HTTP_X_FORWARDED_PORT'] ) ) {
                
                // SERVER_PORT is usually wrong on proxies, don't use it!
                $request_port = intval( $_SERVER['HTTP_X_FORWARDED_PORT'] );
            }
            
            //Does not seem like a proxy
            elseif( isset( $_SERVER['SERVER_PORT'] ) ) {
                $request_port = intval( $_SERVER['SERVER_PORT'] );
            }
            
            //Remove standard ports
            $request_port =( !in_array( $request_port, array(
                80,
                443
            ) ) ? $request_port : '' );
            
            //Build url
            $current_url = $request_protocol . '://' . $request_host .( !empty( $request_port ) ?( ':' . $request_port ) : '' ) . $request_uri;
            
            // overwrite all the above if ajax
            if( strpos( $current_url, 'admin-ajax.php' ) && isset( $_SERVER['HTTP_REFERER'] ) && $_SERVER['HTTP_REFERER'] ) {
                $current_url = $_SERVER['HTTP_REFERER'];
            }
            
            return $current_url;
        }
    }
    
    // termination of class
    
}
// termination of if statement
