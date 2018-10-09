<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

$apsl_settings = array();



// [network_ordering] => Array

//         (

//             [0] => vk

//             [1] => facebook

//             [2] => google

//             [3] => twitter

//             [4] => linkedin

//             [5] => instagram

//             [6] => foursquare

//             [7] => wordpress

//             [8] => buffer

//         )



$social_networks = array(0=>'facebook', 1=>'twitter', 2=>'google', 3=>'linkedin', 4=>'instagram', 5=>'vk', 6=>'foursquare', 7=>'wordpress', 8=>'buffer');

$apsl_settings['network_ordering'] = $social_networks;



//facebook settings

$facebook_parameters = array(

		'apsl_facebook_enable' =>'0',

		'apsl_facebook_app_id' =>'',

		'apsl_facebook_app_secret'=>'',

		'apsl_profile_image_width' => '480',

		'apsl_profile_image_height' => '480'

		);

$apsl_settings['apsl_facebook_settings'] = $facebook_parameters;



//twitter settings

$twitter_parameters = array(

		'apsl_twitter_enable' =>'0',

		'apsl_twitter_api_key' =>'',

		'apsl_twitter_api_secret'=>''

		);

$apsl_settings['apsl_twitter_settings'] = $twitter_parameters;



//google settings

$google_parameters = array(

	'apsl_google_enable' =>'0',

	'apsl_google_client_id' =>'',

	'apsl_google_client_secret'=>''

	);

$apsl_settings['apsl_google_settings'] = $google_parameters;



//linkedin settings

$linkedin_parameters = array(

						'apsl_linkedin_enable' =>'0',

						'apsl_linkedin_client_id' =>'',

						'apsl_linkedin_client_secret'=>''

						);

$apsl_settings['apsl_linkedin_settings'] = $linkedin_parameters;



//Instagram settings

$instagram_parameters = array(

	'apsl_instagram_enable' =>'0',

	'apsl_instagram_api_key' =>'',

	'apsl_instagram_api_secret'=>''

	);

$apsl_settings['apsl_instagram_settings'] = $instagram_parameters;



//vk settings

$vk_parameters = array(

	'apsl_vk_enable' =>'0',

	'apsl_vk_app_id' =>'',

	'apsl_vk_secure_key'=>''

	);

$apsl_settings['apsl_vk_settings'] = $vk_parameters;



//Foursquare settings

$foursquare_parameters = array(

	'apsl_foursquare_enable' =>'0',

	'apsl_foursquare_client_id' =>'',

	'apsl_foursquare_client_secret'=>''

	);

$apsl_settings['apsl_foursquare_settings'] = $foursquare_parameters;



//Wordpress settings

$wordpress_parameters = array(

	'apsl_wordpress_enable' =>'0',

	'apsl_wordpress_client_id' =>'',

	'apsl_wordpress_client_secret'=>''

	);

$apsl_settings['apsl_wordpress_settings'] = $wordpress_parameters;



//Buffer settings

$buffer_parameters = array(

	'apsl_buffer_enable' =>'0',

	'apsl_buffer_client_id' =>'',

	'apsl_buffer_client_secret'=>''

	);

$apsl_settings['apsl_buffer_settings'] = $buffer_parameters;



$apsl_settings['apsl_enable_disable_plugin'] = 'no';

$apsl_settings['apsl_enable_disable_buddypress'] = 'no';

$apsl_settings['apsl_enable_disable_woocommerce'] = 'no';



// for easy digital download settings

$apsl_settings['apsl_enable_disable_edd_login_shortcode'] = 'no';



$apsl_settings['apsl_enable_disable_edd_register_shortcode'] = 'no';



$apsl_settings['apsl_enable_disable_edd_checkout'] = 'no';

// for easy digital download settings ends



$display_options = array('login_form', 'register_form', 'comment_form');

$apsl_settings['apsl_display_options'] =$display_options;



$apsl_settings ['apsl_user_role'] = 'subscriber';



$apsl_settings['apsl_icon_theme'] = '1';



$apsl_settings['apsl_title_text_field'] = 'Social connect:';



$apsl_settings['apsl_login_short_text'] = '';



$apsl_settings['apsl_login_with_long_text']= '';



$apsl_settings['apsl_each_link_title_attribute'] ='';



$apsl_settings['apsl_login_error_message'] = '';



$apsl_settings['apsl_custom_logout_redirect_options'] = 'home';

$apsl_settings['apsl_custom_logout_redirect_link'] ='';



$apsl_settings['apsl_custom_login_redirect_options'] = 'home';

$apsl_settings['apsl_custom_login_redirect_link'] = '';



$apsl_settings['apsl_user_avatar_options'] = 'default';

$apsl_settings['apsl_send_email_notification_options'] = 'yes';



// $apsl_settings['apsl_email_sender_name'] = '';

$apsl_settings['apsl_email_sender_email']	 =	'';

$apsl_settings['apsl_email_body']			 = "Bonjour,

												\n

												Quel plaisir de vous recevoir sur #blogname

												\n

												Voici vos informations pour vous connecter:

												Votre pseudo: #username

												Pour créer votre mot de passe, visitez cette page: <a href='#password_set_link'>creer son mot de passe</a>

												\n

												Merci ! et à très vite :) ";



$apsl_settings['apsl_custom_username_allow'] ='';

$apsl_settings['apsl_custom_email_allow'] ='';

$apsl_settings['apsl_profile_mapping_options'] ='no';

update_option( APSL_SETTINGS, $apsl_settings );