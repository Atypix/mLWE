<div class='network-settings'>
<?php foreach($options['network_ordering'] as $key=>$value):  ?>
	<?php switch($value){
		 case 'facebook': ?>
		<div class='apsl-settings apsl-facebook-settings'>
		<!-- Facebook Settings -->
			<div class='apsl-label'><?php _e( "Facebook", APSL_TEXT_DOMAIN ); ?><span class='apsl_show_hide' id='apsl_show_hide_<?php echo $value; ?>'><i class="fa fa-caret-down"></i></span> </div>
			<div class='apsl_network_settings_wrapper' id='apsl_network_settings_<?php echo $value; ?>' style='display:none'>
				<div class='apsl-enable-disable'>
				<label><?php _e('Enable?', APSL_TEXT_DOMAIN ); ?></label>
				<input type='hidden' name='network_ordering[]' value='facebook' />
				<input type="checkbox" id='aspl-facbook-enable' value='enable' name='apsl_facebook_settings[apsl_facebook_enable]' <?php checked( 'enable', $options['apsl_facebook_settings']['apsl_facebook_enable'] ); ?>  />
				</div>
				<div class='apsl-app-id-wrapper'>
				<label><?php _e( 'App ID:', APSL_TEXT_DOMAIN ); ?></label><input type='text' id='apsl-facebook-app-id' name='apsl_facebook_settings[apsl_facebook_app_id]' value='<?php if(isset($options['apsl_facebook_settings']['apsl_facebook_app_id'])){ echo $options['apsl_facebook_settings']['apsl_facebook_app_id']; } ?>' />
				</div>
				<div class='apsl-app-secret-wrapper'>
				<label><?php _e( 'App Secret:', APSL_TEXT_DOMAIN ); ?></label><input type='text' id='apsl-facebook-app-secret' name='apsl_facebook_settings[apsl_facebook_app_secret]' value='<?php if(isset($options['apsl_facebook_settings']['apsl_facebook_app_secret'])){ echo $options['apsl_facebook_settings']['apsl_facebook_app_secret']; } ?>' />
				</div>

				<div class='apsl-fb-profile-image-size'>
	                <label><?php _e( 'Profile picture image size', 'accesspress-social-login-lite' ); ?></label><br />
	                <label for='apsl-fb-profile-image-width'><?php _e( 'Width:', 'accesspress-social-login-lite' ); ?></label>  <input type='number' name='apsl_facebook_settings[apsl_profile_image_width]' id='apsl-fb-profile-image-width' value='<?php
	                                                                                                                                                        if( isset( $options['apsl_facebook_settings']['apsl_profile_image_width'] ) ) {
	                                                                                                                                                            echo $options['apsl_facebook_settings']['apsl_profile_image_width'];
	                                                                                                                                                        }
	                                                                                                                                            ?>' style="width: 60px;" /> px
	                <br />
	                <label for='apsl-fb-profile-image-height'><?php _e( 'Height:', 'accesspress-social-login-lite' ); ?></label> <input type='number' name='apsl_facebook_settings[apsl_profile_image_height]' id='apsl-fb-profile-image-height' value='<?php
	                                                                                                                                                        if( isset( $options['apsl_facebook_settings']['apsl_profile_image_height'] ) ) {
	                                                                                                                                                            echo $options['apsl_facebook_settings']['apsl_profile_image_height'];
	                                                                                                                                                        }
	                                                                                                                                            ?>' style="width: 60px;" /> px
	            <div class='apsl-info'>Please note that the facebook might not provide the exact dimention of the image as settings above.</div>
	            </div>
				<div class='apsl-info'>
					<span class='apsl-info-note'><?php _e('Note:', APSL_TEXT_DOMAIN ); ?></span> <br />
					<span class='apsl-info-content'>You need to create a new facebook API Applitation to setup facebook login. Please follow the instructions to create new app.</span>
					<br />
					<ul class='apsl-info-lists'>
						<li>Go to <a href='https://developers.facebook.com/apps' target='_blank'>https://developers.facebook.com/apps</a>.</li>
						<li>Click on 'Add a New App' button. A popup will open. Then choose website.</li>
						<li>Add the required informations and don't forget to make your app live. This is very important otherwise your app will not work for all users.</li>
						<li>Then Click the "Create App" button and follow the instructions, your new app will be created. </li>
						<li>Copy and Paste "App ID" and "App Secret" here.</li>
						<li>Site url: <input type='text' value='<?php echo site_url(); ?>' readonly='readonly' /></li>
					</ul>
				</div>
			</div>
		</div>
		<?php break; ?>
		
		<?php case 'twitter': ?>
		 <div class='apsl-settings apsl-twitter-settings'>
		 <!-- Twitter Settings -->
		 	<div class='apsl-label'><?php _e( "Twitter", APSL_TEXT_DOMAIN ); ?> <span class='apsl_show_hide' id='apsl_show_hide_<?php echo $value; ?>'><i class="fa fa-caret-down"></i></span> </div>
		 	<div class='apsl_network_settings_wrapper' id='apsl_network_settings_<?php echo $value; ?>' style='display:none'>
			 	<div class='apsl-enable-disable'> 
			 	<label><?php _e('Enable?', APSL_TEXT_DOMAIN ); ?></label>
			 		<input type="checkbox" id='aspl-twitter-enable' value='enable' name='apsl_twitter_settings[apsl_twitter_enable]' <?php checked( 'enable', $options['apsl_twitter_settings']['apsl_twitter_enable'] ); ?>  />
			 	</div>

			 	<div class='apsl-app-id-wrapper'>
			 	<label><?php _e( 'Consumer Key (API Key):', APSL_TEXT_DOMAIN ); ?></label><input type='text' id='apsl-twitter-app-id' name='apsl_twitter_settings[apsl_twitter_api_key]' value='<?php if(isset($options['apsl_twitter_settings']['apsl_twitter_api_key'])){ echo $options['apsl_twitter_settings']['apsl_twitter_api_key']; } ?>' />
			 	</div>

			 	<div class='apsl-app-secret-wrapper'>
			 	<label><?php _e( 'Consumer Secret (API Secret):', APSL_TEXT_DOMAIN ); ?></label><input type='text' id='apsl-twitter-app-secret' name='apsl_twitter_settings[apsl_twitter_api_secret]' value='<?php if(isset($options['apsl_twitter_settings']['apsl_twitter_api_secret'])){ echo $options['apsl_twitter_settings']['apsl_twitter_api_secret']; } ?>' />
			 	</div>

			 	<input type='hidden' name='network_ordering[]' value='twitter' />
			 	<div class='apsl-info'>
			 		<span class='apsl-info-note'><?php _e('Note:', APSL_TEXT_DOMAIN ); ?> <br /> </span>
			 		<span class='apsl-info-content'>You need to create new twitter API application to setup the twitter login. Please follow the instructions to create new app.</span>
			 		<ul class='apsl-info-lists'>
				 		<li>Go to <a href='https://apps.twitter.com/' target='_blank'>https://apps.twitter.com/</a></li>
				 		<li>Click on Create New App button. A new application details form will appear. Please fill up the application details and click on "create your twitter application" button.</li>
				 		<li>Please note that before creating twiiter API application, You must add your mobile phone to your Twitter profile.</li>
				 		<li>After successful creation of the app. Please go to "Keys and Access Tokens" tabs and get Consumer key(API Key) and Consumer secret(API secret).</li>
				 		<li>Website: <input type='text' value='<?php echo site_url(); ?>' readonly='readonly'/></li>
				 		<li>Callback URL: <input type='text' value='<?php echo site_url(); ?>' readonly='readonly'/></li>
				 		<li><strong>Note: </strong>To get the user's email address please go to app's permission tab and in additional Permissions there you will find a checkbox to request for user email address. Please enable it. To enable it you need to enter privacy policy url and terms of service url.</li>
			 		</ul>

			 	</div>
		  	</div>
		 </div>
		 <?php break;

		 case 'google': ?>
		 <div class='apsl-settings apsl-google-settings'>
		 <!-- Google Settings -->
		 	<div class='apsl-label'><?php _e( "Google", APSL_TEXT_DOMAIN ); ?> <span class='apsl_show_hide' id='apsl_show_hide_<?php echo $value; ?>'><i class="fa fa-caret-down"></i></span> </div>
		 	<div class='apsl_network_settings_wrapper' id='apsl_network_settings_<?php echo $value; ?>' style='display:none'>
			 	<div class='apsl-enable-disable'> 
			 	<label><?php _e('Enable?', APSL_TEXT_DOMAIN ); ?></label>
			 		<input type="checkbox" id='aspl-google-enable' value='enable' name='apsl_google_settings[apsl_google_enable]' <?php checked( 'enable', $options['apsl_google_settings']['apsl_google_enable'] ); ?>  />
			 	</div>
			 	<div class='apsl-app-id-wrapper'>
			 		<label><?php _e( 'Client ID:', APSL_TEXT_DOMAIN ); ?></label><input type='text' id='apsl-google-client-id' name='apsl_google_settings[apsl_google_client_id]' value='<?php if(isset($options['apsl_google_settings']['apsl_google_client_id'])){ echo $options['apsl_google_settings']['apsl_google_client_id']; } ?>' />
			 	</div>
			 	<div class='apsl-app-secret-wrapper'>
			 		<label><?php _e( 'Client Secret:', APSL_TEXT_DOMAIN ); ?></label><input type='text' id='apsl-google-client-secret' name='apsl_google_settings[apsl_google_client_secret]' value='<?php if(isset($options['apsl_google_settings']['apsl_google_client_secret'])){ echo $options['apsl_google_settings']['apsl_google_client_secret']; } ?>' />
			 	</div>
			 	<input type='hidden' name='network_ordering[]' value='google' />
			 	<div class='apsl-info'>
			 		<span class='apsl-info-note'><?php _e('Note:', APSL_TEXT_DOMAIN ); ?></span> <br />
			 		<span class='apsl-info-content'>You need to create new google API application to setup the google login. Please follow the instructions to create new application.</span>
			 		<ul class='apsl-info-lists'>
				 		<li>Go to <a href='https://console.developers.google.com/project' target='_blank'>https://console.developers.google.com/project.</a> </li>
				 		<li>Click on "Create Project" button. A popup will appear.</li>
				 		<li>Please enter Project name and click on "Create" button.</li>
				 		<li>A App will be created and a dashobard will appear.</li>
				 		<li>In the blue box please click on Enable and manage APIs link. A new page will load.</li>
				 		<li>Now In the Social APIs section click on Google+ API and click "Enable API" button. Then the Google+ API will be activated.</li>
				 		<li>Now click on Credentials section and go to OAuth consent screen and enter the app details there.</li>
				 		<li>Click on Credentials tab and click on "New credentials" or "Add credentials" if you have already created one, a selection will appear and click on "OAuth client ID".</li>
				 		<li>A new page will load. Please select Application type to Web application and click "create" button. Further forms will loaded up and enter the details there.</li>
				 		<li>In the authorized redirect URIs please enter the details provided in the note section from plugin and click save button.</li>
				 		<li>In the popup you will get Client ID and client secret.</li>
				 		<li>And please enter those credentials in the google setting in our plugin.</li>
				 		<li>Rediret uri setup:<br />
				 			Please use <input type='text' value='<?php echo site_url(); ?>/wp-login.php?apsl_login_id=google_check' readonly='readonly'/> - for wordpress login page.<br />
				 			Please use <input type='text' value='<?php echo site_url(); ?>/index.php?apsl_login_id=google_check' readonly='readonly'/> - if you have used the shortcode or widget in frontend.
				 		</li>
				 		<li>
				 			Please note: Make sure to check the protocol "http://" or "https://" as google checks protocol as well. Better to add both URL in the list if you site is https so that google social login work properly for both https and http browser.
				 		</li>
			 		</ul>
			 	</div>
		 	</div>	
		 </div>
		 <?php break; ?>

		 <?php case 'linkedin': ?>
		 <div class='apsl-settings apsl-linkedin-settings'>
		 <!-- Linkedin Settings -->
		 	<div class='apsl-label'><?php _e( "LinkedIn", APSL_TEXT_DOMAIN ); ?> <span class='apsl_show_hide' id='apsl_show_hide_<?php echo $value; ?>'><i class="fa fa-caret-down"></i></span> </div>
		 	<div class='apsl_network_settings_wrapper' id='apsl_network_settings_<?php echo $value; ?>' style='display:none'>
			 	<div class='apsl-enable-disable'> 
			 	<label><?php _e('Enable?', APSL_TEXT_DOMAIN ); ?></label>
			 		<input type="checkbox" id='aspl-linkedin-enable' value='enable' name='apsl_linkedin_settings[apsl_linkedin_enable]' <?php checked( 'enable', $options['apsl_linkedin_settings']['apsl_linkedin_enable'] ); ?>  />
			 	</div>
			 	<div class='apsl-app-id-wrapper'>
			 		<label><?php _e( 'Client ID:', APSL_TEXT_DOMAIN ); ?></label><input type='text' id='apsl-linkedin-client-id' name='apsl_linkedin_settings[apsl_linkedin_client_id]' value='<?php if(isset($options['apsl_linkedin_settings']['apsl_linkedin_client_id'])){ echo $options['apsl_linkedin_settings']['apsl_linkedin_client_id']; } ?>' />
			 	</div>
			 	<div class='apsl-app-secret-wrapper'>
			 		<label><?php _e( 'Client Secret:', APSL_TEXT_DOMAIN ); ?></label><input type='text' id='apsl-linkedin-client-secret' name='apsl_linkedin_settings[apsl_linkedin_client_secret]' value='<?php if(isset($options['apsl_linkedin_settings']['apsl_linkedin_client_secret'])){ echo $options['apsl_linkedin_settings']['apsl_linkedin_client_secret']; } ?>' />
			 	</div>
			 	<input type='hidden' name='network_ordering[]' value='linkedin' />
			 	<div class='apsl-info'>
			 		<span class='apsl-info-note'><?php _e('Note:', APSL_TEXT_DOMAIN ); ?></span><br />
			 		<span class='apsl-info-content'>You need to create a new linkedin API application to setup the linkedin login. Please follow the instrcutions to create new application.</span>
			 		<ul class='apsl-info-lists'>
			 		<li>Go to <a href='https://www.linkedin.com/developer/apps' target='_blank'>https://www.linkedin.com/developer/apps</a></li>
			 		<li>Click on "Create Application" button.</li>
			 		<li>Please enter the application details here. and click create app</li>
			 		<li>Get the Client ID and Client secret.</li>
			 		<li>Authorized Redirect URLs: <?php echo site_url(); ?></li>
			 		<li>OAuth 1.0a <br />
							Default "Accept" Redirect URL: <input type='text' value='<?php echo site_url(); ?>' readonly='readonly' /> <br />
							Default "Cancel" Redirect URL: <input type='text' value='<?php echo site_url(); ?>' readonly='readonly' /> <br />
					</li>
			 		</ul>
			 	</div>
		 	</div>
		 </div>
		 <?php break; ?>


		 <?php case 'instagram': ?>
		 <div class='apsl-settings apsl-instagram-settings'>
		 <!-- instagram Settings -->
		 	<div class='apsl-label'><?php _e( "Instagram", APSL_TEXT_DOMAIN ); ?> <span class='apsl_show_hide' id='apsl_show_hide_<?php echo $value; ?>'><i class="fa fa-caret-down"></i></span> </div>
		 	<div class='apsl_network_settings_wrapper' id='apsl_network_settings_<?php echo $value; ?>' style='display:none'>
			 	<div class='apsl-enable-disable'>
			 	<label><?php _e('Enable?', APSL_TEXT_DOMAIN ); ?></label>
			 		<input type="checkbox" id='aspl-instagram-enable' value='enable' name='apsl_instagram_settings[apsl_instagram_enable]' <?php checked( 'enable', $options['apsl_instagram_settings']['apsl_instagram_enable'] ); ?>  />
			 	</div>
			 	<div class='apsl-app-id-wrapper'>
			 	<label><?php _e( 'Client ID:', APSL_TEXT_DOMAIN ); ?></label><input type='text' id='apsl-instagram-api_key' name='apsl_instagram_settings[apsl_instagram_api_key]' value='<?php if(isset($options['apsl_instagram_settings']['apsl_instagram_api_key'])){ echo $options['apsl_instagram_settings']['apsl_instagram_api_key']; } ?>' />
			 	</div>
			 	<div class='apsl-app-secret-wrapper'>
			 		<label><?php _e( 'Client Secret:', APSL_TEXT_DOMAIN ); ?></label><input type='text' id='apsl-instagram-api-secret' name='apsl_instagram_settings[apsl_instagram_api_secret]' value='<?php if(isset($options['apsl_instagram_settings']['apsl_instagram_api_secret'])){ echo $options['apsl_instagram_settings']['apsl_instagram_api_secret']; } ?>' />
			 	</div>
			 	<input type='hidden' name='network_ordering[]' value='instagram' />
			 	<div class='apsl-info'>
			 		<span class='apsl-info-note'><?php _e('Note:', APSL_TEXT_DOMAIN ); ?></span><br />
			 		<span class='apsl-info-content'>You need to register a new client to setup the instagram social login. Please follow the instructions to create new client.</span>
			 		<ul class='apsl-info-lists'>
				 		<li>Go to <a href='https://instagram.com/developer/clients/manage/' target='_blank'>https://instagram.com/developer/clients/manage/</a></li>
				 		<li>Click on "Register a new client" button.</li>
				 		<li>Enter the required details in the form and click the "Register" button.</li>
				 		<li>Get the client id and client secret.</li>
				 		<li> Redirect URI: <br />
				 			<input type='text' value='<?php echo site_url(); ?>' readonly='readonly' /> - If you have used the shortcode or widget in home page.  <br />
				 			<input type='text' value='<?php echo site_url(); ?>/wp-login.php' readonly='readonly' /> - For wordpress default login. <br />
				 			<input type='text' value='<?php echo site_url(); ?>/index.php' readonly='readonly' /> - If you have used the shortcode or widget in home page or other pages. <br />
				 		</li>
				 		<li>
				 			With the change in the instagram API on June 01 2016 now the app created will be in sandbox mode and to make the social login to work for all instagram users you need to make app live. And to make app live you need to approve the app by instagram review team. For social login the app permission requires only basic information. Please go to the permissions tab and submit the app for review.
				 		</li>
				 	</ul>
			 	</div>
		 	</div>
		 </div>
		 <?php break; ?>

		 <?php case 'foursquare': ?>
		 <div class='apsl-settings apsl-foursquare-settings'>
		 <!-- foursquare Settings -->
		 	<div class='apsl-label'><?php _e( "Foursquare", APSL_TEXT_DOMAIN ); ?> <span class='apsl_show_hide' id='apsl_show_hide_<?php echo $value; ?>'><i class="fa fa-caret-down"></i></span> </div>
		 	<div class='apsl_network_settings_wrapper' id='apsl_network_settings_<?php echo $value; ?>' style='display:none'>
			 	<div class='apsl-enable-disable'>
			 	<label><?php _e('Enable?', APSL_TEXT_DOMAIN ); ?></label>
			 		<input type="checkbox" id='aspl-foursquare-enable' value='enable' name='apsl_foursquare_settings[apsl_foursquare_enable]' <?php checked( 'enable', $options['apsl_foursquare_settings']['apsl_foursquare_enable'] ); ?>  />
			 	</div>
			 	<div class='apsl-app-id-wrapper'>
			 	<label><?php _e( 'Client ID:', APSL_TEXT_DOMAIN ); ?></label><input type='text' id='apsl-foursquare-client-id' name='apsl_foursquare_settings[apsl_foursquare_client_id]' value='<?php if(isset($options['apsl_foursquare_settings']['apsl_foursquare_client_id'])){ echo $options['apsl_foursquare_settings']['apsl_foursquare_client_id']; } ?>' />
			 	</div>
			 	<div class='apsl-app-secret-wrapper'>
			 	<label><?php _e( 'Client Secret:', APSL_TEXT_DOMAIN ); ?></label><input type='text' id='apsl-foursquare-client-secret' name='apsl_foursquare_settings[apsl_foursquare_client_secret]' value='<?php if(isset($options['apsl_foursquare_settings']['apsl_foursquare_client_secret'])){ echo $options['apsl_foursquare_settings']['apsl_foursquare_client_secret']; } ?>' />
			 	</div>
			 	<input type='hidden' name='network_ordering[]' value='foursquare' />
			 	<div class='apsl-info'>
			 		<span class='apsl-info-note'><?php _e('Note:', APSL_TEXT_DOMAIN ); ?></span><br />
			 		<span class='apsl-info-content'>You need to register a new app to setup the foursquare social login. Please follow the instructions below to create new app.</span>
			 		<ul class='apsl-info-lists'>
				 		<li>Go to <a href='https://foursquare.com/developers/apps' target='_blank'>https://foursquare.com/developers/apps</a></li>
				 		<li>Click on "Create a new app" button.</li>
				 		<li>Enter the required details in the form and click the "Save Changes" button.</li>
				 		<li>Get the client id and client secret.</li>
				 		<li> Redirect URI(s) : <br />
								<input type='text' value='<?php echo site_url(); ?>' readonly='readonly' />
				 		</li>
			 		</ul>
			 	</div>
		 	</div>
		 </div>
		 <?php break; ?>

		 <?php case 'wordpress': ?>
		 <div class='apsl-settings apsl-wordpress-settings'>
		 <!-- wordpress Settings -->
		 	<div class='apsl-label'><?php _e( "wordpress", APSL_TEXT_DOMAIN ); ?> <span class='apsl_show_hide' id='apsl_show_hide_<?php echo $value; ?>'><i class="fa fa-caret-down"></i></span> </div>
		 	<div class='apsl_network_settings_wrapper' id='apsl_network_settings_<?php echo $value; ?>' style='display:none'>
			 	<div class='apsl-enable-disable'> 
			 	<label><?php _e('Enable?', APSL_TEXT_DOMAIN ); ?></label>
			 		<input type="checkbox" id='aspl-wordpress-enable' value='enable' name='apsl_wordpress_settings[apsl_wordpress_enable]' <?php checked( 'enable', $options['apsl_wordpress_settings']['apsl_wordpress_enable'] ); ?>  />
			 	</div>
			 	<div class='apsl-app-id-wrapper'>
			 	<label><?php _e( 'Client ID:', APSL_TEXT_DOMAIN ); ?></label><input type='text' id='apsl-wordpress-client-id' name='apsl_wordpress_settings[apsl_wordpress_client_id]' value='<?php if(isset($options['apsl_wordpress_settings']['apsl_wordpress_client_id'])){ echo $options['apsl_wordpress_settings']['apsl_wordpress_client_id']; } ?>' />
			 	</div>
			 	<div class='apsl-app-secret-wrapper'>
			 	<label><?php _e( 'Client Secret:', APSL_TEXT_DOMAIN ); ?></label><input type='text' id='apsl-wordpress-client-secret' name='apsl_wordpress_settings[apsl_wordpress_client_secret]' value='<?php if(isset($options['apsl_wordpress_settings']['apsl_wordpress_client_secret'])){ echo $options['apsl_wordpress_settings']['apsl_wordpress_client_secret']; } ?>' />
			 	</div>
			 	<input type='hidden' name='network_ordering[]' value='wordpress' />
			 	<div class='apsl-info'>
			 		<span class='apsl-info-note'><?php _e('Note:', APSL_TEXT_DOMAIN ); ?></span><br />
			 		<span class='apsl-info-content'>You need to register a new app to setup the wordpress social login. Please follow the instructions below to create new app.</span>
			 		<ul class='apsl-info-lists'>
				 		<li>Go to <a href='https://developer.wordpress.com/apps/' target='_blank'>https://developer.wordpress.com/apps/</a></li>
				 		<li>Click on "Create new Application" button.</li>
				 		<li>Enter the required details in the form and click the "Create" button.</li>
				 		<li>A message will appear informing the application has been created.</li>
				 		<li>Click on the Application link to get the application details.</li>
				 		<li>Get the oauth details - client id and client secret there.</li>
				 		<li> Redirect URL: <br />
				 			<input type='text' value='<?php echo site_url(); ?>' readonly='readonly' />
				 		</li>
			 		</ul>

			 	</div>
			</div>
		 </div>
		 <?php break; ?>

		 <?php case 'vk': ?>
		 <div class='apsl-settings apsl-vk-settings'>
		 <!-- vk Settings -->
		 	<div class='apsl-label'><?php _e( "Vkonate", APSL_TEXT_DOMAIN ); ?> <span class='apsl_show_hide' id='apsl_show_hide_<?php echo $value; ?>'><i class="fa fa-caret-down"></i></span> </div>
		 	<div class='apsl_network_settings_wrapper' id='apsl_network_settings_<?php echo $value; ?>' style='display:none'>
			 	<div class='apsl-enable-disable'> 
			 	<label><?php _e('Enable?', APSL_TEXT_DOMAIN ); ?></label>
			 		<input type="checkbox" id='aspl-vk-enable' value='enable' name='apsl_vk_settings[apsl_vk_enable]' <?php checked( 'enable', $options['apsl_vk_settings']['apsl_vk_enable'] ); ?>  />
			 	</div>
			 	<div class='apsl-app-id-wrapper'>
			 	<label><?php _e( 'App ID:', APSL_TEXT_DOMAIN ); ?></label><input type='text' id='apsl-vk-app-id' name='apsl_vk_settings[apsl_vk_app_id]' value='<?php if(isset($options['apsl_vk_settings']['apsl_vk_app_id'])){ echo $options['apsl_vk_settings']['apsl_vk_app_id']; } ?>' />
			 	</div>
			 	<div class='apsl-app-secret-wrapper'>
			 	<label><?php _e( 'Secure Key:', APSL_TEXT_DOMAIN ); ?></label><input type='text' id='apsl-vk-secure_key' name='apsl_vk_settings[apsl_vk_secure_key]' value='<?php if(isset($options['apsl_vk_settings']['apsl_vk_secure_key'])){ echo $options['apsl_vk_settings']['apsl_vk_secure_key']; } ?>' />
			 	</div>
			 	<input type='hidden' name='network_ordering[]' value='vk' />
			 	<div class='apsl-info'>
			 		<span class='apsl-info-note'><?php _e('Note:', APSL_TEXT_DOMAIN ); ?></span><br />
			 		<span class='apsl-info-content'>You need to register a new app to setup the vkonate social login. Please follow the instructions below to create new app.</span>
			 		<ul class='apsl-info-lists'>
				 		<li>Go to <a href='https://vk.com/apps?act=manage' target='_blank'>https://vk.com/apps?act=manage</a></li>
				 		<li>Click on "Create an Application" button. Enter the title, and please choose website as category, enter the site address and base domain. And click "Connect site". Now  a popup will appear and you will receive a confirmation code in text message in your mobile device after clicking "get code" button.</li>
				 		<li>After that you need to give addition information about the application and click save.</li>
				 		<li>Now click on Settings tab and there you will get the application id and secure key.</li>
				 		<li>Open API: <br />
				 			Site Address: <input type='text' value='<?php echo site_url(); ?>' readonly='readonly' />
				 		</li>
			 		</ul>
			 		
			 	</div>
		 	</div>
		 </div>
		 <?php break; ?>

		<?php case 'buffer': ?>
		 <div class='apsl-settings apsl-buffer-settings'>
		 <!-- Buffer Settings -->
		 	<div class='apsl-label'><?php _e( "Buffer", APSL_TEXT_DOMAIN ); ?> <span class='apsl_show_hide' id='apsl_show_hide_<?php echo $value; ?>'><i class="fa fa-caret-down"></i></span> </div>
		 	<div class='apsl_network_settings_wrapper' id='apsl_network_settings_<?php echo $value; ?>' style='display:none'>
			 	<div class='apsl-enable-disable'> 
			 	<label><?php _e('Enable?', APSL_TEXT_DOMAIN ); ?></label>
			 		<input type="checkbox" id='aspl-buffer-enable' value='enable' name='apsl_buffer_settings[apsl_buffer_enable]' <?php checked( 'enable', $options['apsl_buffer_settings']['apsl_buffer_enable'] ); ?>  />
			 	</div>
			 	<div class='apsl-app-id-wrapper'>
			 	<label><?php _e( 'Client ID:', APSL_TEXT_DOMAIN ); ?></label><input type='text' id='apsl-buffer-client-id' name='apsl_buffer_settings[apsl_buffer_client_id]' value='<?php if(isset($options['apsl_buffer_settings']['apsl_buffer_client_id'])){ echo $options['apsl_buffer_settings']['apsl_buffer_client_id']; } ?>' />
			 	</div>
			 	<div class='apsl-app-secret-wrapper'>
			 	<label><?php _e( 'Client Secret:', APSL_TEXT_DOMAIN ); ?></label><input type='text' id='apsl-buffer-client-secret' name='apsl_buffer_settings[apsl_buffer_client_secret]' value='<?php if(isset($options['apsl_buffer_settings']['apsl_buffer_client_secret'])){ echo $options['apsl_buffer_settings']['apsl_buffer_client_secret']; } ?>' />
			 	</div>
			 	<input type='hidden' name='network_ordering[]' value='buffer' />
			 	<div class='apsl-info'>
			 		<span class='apsl-info-note'><?php _e('Note:', APSL_TEXT_DOMAIN ); ?></span><br />
			 		<span class='apsl-info-content'>You need to register a new app to setup the buffer social login. Please follow the instructions below to create new app.</span>
			 		<ul class='apsl-info-lists'>
				 		<li>Go to <a href='https://buffer.com/developers/apps' target='_blank'>https://buffer.com/developers/apps</a></li>
				 		<li>Click on "Create an App" button.</li>
				 		<li>Enter the application details and click on "Create applciation" button.</li>
				 		<li>Get the client id and client secret.</li>
				 		<li>Redirect URI: <br />
				 			<input type='text' value='<?php echo site_url(); ?>' readonly='readonly' />
				 		</li>
					</ul>
			 	</div>
		 	</div>
		 </div>
		 <?php  break; ?>
		 
		 <?php default:
		 echo "should not reach here";
		 break;

		} ?>
<?php endforeach; ?>
</div>