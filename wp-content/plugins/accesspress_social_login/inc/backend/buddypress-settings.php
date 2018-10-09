<?php defined( 'ABSPATH' ) or die( "No script kiddies please!" ); ?>
<div class='apsl-settings'>
<div class="apsl-profile-mapping-wrapper">
	<div class="apsl-label"><?php _e('XProfile Mapping Enable?', APSL_TEXT_DOMAIN ); ?> <span class='apsl_show_hide'><i class="fa fa-caret-down"></i></span> </div>
	<div class='apsl_network_settings_wrapper apsl-profile-mapping' style='display:none'>
		<input type='radio' id='apsl-profile-mapping-enabled' class='apsl-profile-mapping-enabled apsl_profile_mapping_options' name='apsl_profile_mapping_options' value='yes' <?php if(isset($options['apsl_profile_mapping_options'])){ checked( $options['apsl_profile_mapping_options'], 'yes', 'true' ); } ?> /> <label for='apsl-profile-mapping-enabled'><?php _e('Yes', APSL_TEXT_DOMAIN ); ?></label><br /><br />
		<input type='radio' id='apsl-profile-mapping-disabled' class='apsl-profile-mapping-disabled apsl_profile_mapping_options' name='apsl_profile_mapping_options' value='no' <?php if(isset($options['apsl_profile_mapping_options'])){ checked( $options['apsl_profile_mapping_options'], 'no', 'true' ); } ?> /> <label for='apsl-profile-mapping-disabled'><?php _e('No', APSL_TEXT_DOMAIN ); ?></label><br /><br />
	</div>
</div>
</div>

<div class='apsl-settings apsl-profile-mapping-outer' style="<?php if(isset($options['apsl_profile_mapping_options']) && $options['apsl_profile_mapping_options'] == 'yes'){ ?> display:block; <?php }else{ ?> display:none; <?php } ?>">
<div class="apsl-profile-mapping-wrapper">
	<div class="apsl-label"><?php _e('Fields Mapping', APSL_TEXT_DOMAIN ); ?> <span class='apsl_show_hide'><i class="fa fa-caret-down"></i></span> </div>
		

	<div class='apsl_network_settings_wrapper apsl-profile-mapping' style='display:none'>
		<div class='apsl-note'>
			Here you can map users profiles fields to the appropriate buddypress xprofile fields. The left column shows the available users profiles fields: These select boxes are called source fields. The right column shows the list of Buddypress profiles fields: Those are the destination fields. If you don't want to map a particular Buddypress field, then leave the source for that field blank.
		</div>

		<div class='xprofile-mapping-fields'>
			<?php

			$ha_profile_fields = array(
				array( 'field' => 'provider'    , 'label' => __( "Provider name"            , 'wordpress-social-login'), 'description' => __( "The provider or social network name the user used to connected"                                                     , 'wordpress-social-login') ),
				array( 'field' => 'identifier'  , 'label' => __( "Provider user Identifier" , 'wordpress-social-login'), 'description' => __( "The Unique user's ID on the connected provider. Depending on the provider, this field can be an number, Email, URL, etc", 'wordpress-social-login') ),
				array( 'field' => 'profileURL'  , 'label' => __( "Profile URL"              , 'wordpress-social-login'), 'description' => __( "Link to the user profile on the provider web site"                                                                      , 'wordpress-social-login') ),
				array( 'field' => 'webSiteURL'  , 'label' => __( "Website URL"              , 'wordpress-social-login'), 'description' => __( "User website, blog or web page"                                                                                         , 'wordpress-social-login') ),
				array( 'field' => 'photoURL'    , 'label' => __( "Photo URL"                , 'wordpress-social-login'), 'description' => __( "Link to user picture or avatar on the provider web site"                                                                , 'wordpress-social-login') ),
				array( 'field' => 'displayName' , 'label' => __( "Display name"             , 'wordpress-social-login'), 'description' => __( "User Display name."  																								   , 'wordpress-social-login') ),
				array( 'field' => 'description' , 'label' => __( "Description"              , 'wordpress-social-login'), 'description' => __( "A short about me"                                                                                                       , 'wordpress-social-login') ),
				array( 'field' => 'firstName'   , 'label' => __( "First name"               , 'wordpress-social-login'), 'description' => __( "User's first name"                                                                                                      , 'wordpress-social-login') ),
				array( 'field' => 'lastName'    , 'label' => __( "Last name"                , 'wordpress-social-login'), 'description' => __( "User's last name"                                                                                                       , 'wordpress-social-login') ),
				array( 'field' => 'gender'      , 'label' => __( "Gender"                   , 'wordpress-social-login'), 'description' => __( "User's gender. Values are 'female', 'male' or blank"                                                                    , 'wordpress-social-login') ),
				array( 'field' => 'language'    , 'label' => __( "Language"                 , 'wordpress-social-login'), 'description' => __( "User's language"                                                                                                        , 'wordpress-social-login') ),
				array( 'field' => 'age'         , 'label' => __( "Age"                      , 'wordpress-social-login'), 'description' => __( "User' age. Note that We return it as it was provided"                                  								   , 'wordpress-social-login') ),
				array( 'field' => 'birthDay'    , 'label' => __( "Birth day"                , 'wordpress-social-login'), 'description' => __( "The day in the month in which the person was born. Not to confuse it with 'Birth date'"                                 , 'wordpress-social-login') ),
				array( 'field' => 'birthMonth'  , 'label' => __( "Birth month"              , 'wordpress-social-login'), 'description' => __( "The month in which the person was born"                                                                                 , 'wordpress-social-login') ),
				array( 'field' => 'birthYear'   , 'label' => __( "Birth year"               , 'wordpress-social-login'), 'description' => __( "The year in which the person was born"                                                                                  , 'wordpress-social-login') ),
				array( 'field' => 'birthDate'   , 'label' => __( "Birth date"               , 'wordpress-social-login'), 'description' => __( "Complete birthday in which the person was born. Format: YYYY-MM-DD"                                                     , 'wordpress-social-login') ),
				array( 'field' => 'email'       , 'label' => __( "Email"                    , 'wordpress-social-login'), 'description' => __( "User's email address. Not all of provider grant access to the user email"                                               , 'wordpress-social-login') ),
				array( 'field' => 'phone'       , 'label' => __( "Phone"                    , 'wordpress-social-login'), 'description' => __( "User's phone number"                                                                                                    , 'wordpress-social-login') ),
				array( 'field' => 'address'     , 'label' => __( "Address"                  , 'wordpress-social-login'), 'description' => __( "User's address"                                                                                                         , 'wordpress-social-login') ),
				array( 'field' => 'country'     , 'label' => __( "Country"                  , 'wordpress-social-login'), 'description' => __( "User's country"                                                                                                         , 'wordpress-social-login') ),
				array( 'field' => 'region'      , 'label' => __( "Region"                   , 'wordpress-social-login'), 'description' => __( "User's state or region"                                                                                                 , 'wordpress-social-login') ),
				array( 'field' => 'city'        , 'label' => __( "City"                     , 'wordpress-social-login'), 'description' => __( "User's city"                                                                                                            , 'wordpress-social-login') ),
				array( 'field' => 'zip'         , 'label' => __( "Zip"                      , 'wordpress-social-login'), 'description' => __( "User's zipcode"                                                                                                         , 'wordpress-social-login') ),
			);

				
			$ha_profile_fields = array(
										array( 'field' => 'deutype'    	, 'label' => __( "Social Provider name"         , 'wordpress-social-login'), 'description' => __( "The Social network name the user used to connect."                                                     					, 'wordpress-social-login') ),
										array( 'field' => 'deuid'  		, 'label' => __( "Social Provider unique ID" 	, 'wordpress-social-login'), 'description' => __( "The Unique user's ID provided by social newtork. Depending on the provider, this field can be an number, Email, URL, etc", 'wordpress-social-login') ),
										array( 'field' => 'first_name'  , 'label' => __( "First name"               	, 'wordpress-social-login'), 'description' => __( "User's first name"                                                                                                      	, 'wordpress-social-login') ),
										array( 'field' => 'last_name'   , 'label' => __( "Last name"                	, 'wordpress-social-login'), 'description' => __( "User's last name"                                                                                                       	, 'wordpress-social-login') ),
										array( 'field' => 'email'       , 'label' => __( "Email"                    	, 'wordpress-social-login'), 'description' => __( "User's email address. Not all of provider grant access to the user email"                                               	, 'wordpress-social-login') ),
										array( 'field' => 'gender'      , 'label' => __( "Gender"                   	, 'wordpress-social-login'), 'description' => __( "User's gender."                                                                    										, 'wordpress-social-login') ),
										array( 'field' => 'deuimage'    , 'label' => __( "Photo URL"                	, 'wordpress-social-login'), 'description' => __( "Link to user's picture on the provider social network."                                                                	, 'wordpress-social-login') ),
										array( 'field' => 'url'  		, 'label' => __( "Profile URL"              	, 'wordpress-social-login'), 'description' => __( "User's profile link on the connected social network."                                                                    , 'wordpress-social-login') ),
										array( 'field' => 'about' 		, 'label' => __( "Description"              	, 'wordpress-social-login'), 'description' => __( "A short description about user."                                                                                         , 'wordpress-social-login') ),
									);

			if ( bp_has_profile() )
			{
				while ( bp_profile_groups() )
				{
					global $group;

					bp_the_profile_group();
					?>
						<h4><?php echo sprintf( __("Fields Group '%s'", 'wordpress-social-login'), $group->name ) ?> :</h4>

						<table width="100%" border="0" cellpadding="5" cellspacing="2" style="border-top:1px solid #ccc;">
							<?php
								while ( bp_profile_fields() )
								{
									global $field;

									bp_the_profile_field();
									?>
										<tr>
											<td width="270" align="right" valign="top">
												<?php
													$map = isset( $options['apsl_settings_buddypress_xprofile_map'][$field->id] ) ? $options['apsl_settings_buddypress_xprofile_map'][$field->id] : 0;
													$can_map_it = true;

													if( ! in_array( $field->type, array( 'textarea', 'textbox', 'url', 'datebox', 'number' ) ) ){
														$can_map_it = false;
													}
												?>
												<select name="apsl_settings_buddypress_xprofile_map[<?php echo $field->id; ?>]" style="width:255px" id="bb_profile_mapping_selector_<?php echo $field->id; ?>" onChange="showMappingConfirm( <?php echo $field->id; ?> );" <?php if( ! $can_map_it ) echo "disabled"; ?>>
													<option value=""></option>
													<?php
														if( $can_map_it ){
															foreach( $ha_profile_fields as $item ){
															?>
																<option value="<?php echo $item['field']; ?>" <?php if( $item['field'] == $map ) echo "selected"; ?> ><?php echo $item['label']; ?></option>
															<?php
															}
														}
													?>
												</select>
											</td>
											<td valign="top" align="center" width="50">
												<!-- <img src="<?php echo $assets_base_url; ?>arr_right.png" /> -->
											</td>
											<td valign="top">
												<strong><?php echo $field->name; ?></strong>
												<?php
													if( ! $can_map_it ){
													?>
														<p class="description">
															<?php __(" This field cannot be mapped. Supported field types are: <em>Multi-line Text Areax, Text Box, URL, Date Selector and Number</em>", 'wordpress-social-login'); ?>.
														</p>
													<?php
													}
													else{
													?>
														<?php
															foreach( $ha_profile_fields as $item ){
														?>
															<p class="description bb_profile_mapping_confirm_<?php echo $field->id; ?>" style="margin-left:0;<?php if( $item['field'] != $map ) echo "display:none;"; ?>" id="bb_profile_mapping_confirm_<?php echo $field->id; ?>_<?php echo $item['field']; ?>">
																<?php echo sprintf( __( "<b>%s</b> is mapped to Buddypress <b>%s</b> field", 'wordpress-social-login' ), $item['label'], $field->name ); ?>.
																<br />
																<em><b><?php echo $item['label']; ?>:</b> <?php echo $item['description']; ?>.</em>
															</p>
														<?php
															}
														?>
													<?php
													}
												?>
											</td>
										</tr>
									<?php
								}
							?>
						</table>
					<?php
				}
			}
		?>
		</div>
	</div>
</div>
</div>

