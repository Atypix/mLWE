<?php
// get_header(); // can be activated if needed
$options = get_option( APSL_SETTINGS );
 if(isset($_SESSION['user_details'])){
	//var_dump($_SESSION['user_details']);
	$user_object = isset( $_SESSION['user_details'] ) ? $_SESSION['user_details'] : '';
	if(isset($_POST['apsl_complete_registration'])){
		if(isset($_POST['apsl-email-input'])){
		 	$email = sanitize_text_field($_POST['apsl-email-input']);
		 	if(APSL_Functions:: getUserByMail($email) == true ){
		 		$email_error = __( 'Hey! Your email already exist in our system. Please change email address to continue or you can link your account by going back.', APSL_TEXT_DOMAIN );
		 	}
		}

		if(isset($_POST['apsl-username-input'])){
		 	$username = sanitize_text_field($_POST['apsl-username-input']);
		 	if(APSL_Functions:: getUserByUsername($username) == true ){
		 		$username_error = __( 'Username already exist.', APSL_TEXT_DOMAIN );
		 	}
		}

		if(isset($options['apsl_custom_username_allow']) && $options['apsl_custom_username_allow'] =='allow' ){
			$username = sanitize_text_field($_POST['apsl-username-input']);
		}else{
			$username = $user_object->username;
		}

		if(isset($options['apsl_custom_email_allow']) && $options['apsl_custom_email_allow'] =='allow' ){
			$email = sanitize_text_field($_POST['apsl-email-input']);
		}else{
			$email = $user_object->email;
		}
			if(!isset($email_error) && !isset($username_error)){
				APSL_Functions::creatUser( $username, $email );
	            $row = APSL_Functions::getUserByMail( $email );
	            $id = $row->ID;

	            $result = $user_object;
	            $result->email = $email;
	            unset($_SESSION['user_details']);
	            $role = $options['apsl_user_role'];

	            APSL_Functions:: UpdateUserMeta( $id, $result, $role );
	            APSL_Functions:: loginUser( $id );
	            exit();
			}

			if(isset($username_error) &&  isset($email_error) )
				$message_string = $username_error .' '.$email_error;
			else if(isset($username_error))
				$message_string = $username_error;
			else if(isset($email_error))
				$message_string = $email_error;
		
	}

	if( isset( $_POST['apsl_login_details_submit'] ) ) {
		global $wpdb;
        $username = sanitize_user( $_POST['login_username'] );
        $password = sanitize_text_field($_POST['login_password']);
        $user = get_user_by( 'login', $username );
        if ( $user && wp_check_password( $password, $user->data->user_pass, $user->ID) ){
		   //echo "That's it. Now you can get the user details from there";
        	$id = $user->ID;
            $result = $user_object;
            unset($_SESSION['user_details']);
            $role = $options['apsl_user_role'];
            APSL_Functions:: link_user($id, $result);
	        APSL_Functions:: loginUser( $id );
	        exit();
        }else{
		   $error_message = __( 'Invalid Username or Password.', APSL_TEXT_DOMAIN );
        }
    }


	 $user_object = isset( $_SESSION['user_details'] ) ? $_SESSION['user_details'] : '';
	 $username = APSL_Functions:: get_username($user_object->username);
	?>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body>
	<div class='apsl-registration-wrapper'>
	<div class="apsl-login-wrap">
	<div class='apsl-social-profile-image'><img src='<?php echo $user_object->deuimage; ?>' alt='<?php echo $user_object->first_name; ?>' /></div>
	
	<div class='apsl-registration-info'>
		<?php echo _e('Hi', APSL_TEXT_DOMAIN ); ?> <span class='apsl-user_name'><?php echo $user_object->first_name; ?></span>,<p class="apsl-login-message">
		<?php _e('You\'ve now successfully authorized your social profile account but you are still one step away for logging into website.', APSL_TEXT_DOMAIN ); ?></p>
	</div>

	<div class='apsl-buttons-wrapper clearfix' style="<?php if( isset($_POST['apsl_complete_registration']) || isset($_POST['apsl_login_details_submit']) ){ echo "display:none"; }else{ echo "display:block"; } ?>" >
		<div class='apsl-login-form-wrapper' >
			<span class='apsl-have-account'><?php echo _e('Have an account?', APSL_TEXT_DOMAIN ); ?></span>
			<p><?php echo _e('Simply click the below button to link you social profile with existing account.', APSL_TEXT_DOMAIN ); ?></p>
			<button class='apsl-link-account-button'><?php echo _e('Link my account', APSL_TEXT_DOMAIN ); ?></button>
		</div>
		<div class='apsl-register-form-wrapper'>
			<span class='apsl-have-account'><?php echo _e('New to website?', APSL_TEXT_DOMAIN ); ?></span>
			<p><?php echo _e('Simply click the below button to do simple registration to our site.', APSL_TEXT_DOMAIN ); ?></p>
			<button class='apsl-create-account-button'><?php echo _e('Create my account', APSL_TEXT_DOMAIN ); ?></button>
		</div>
	</div>

	<div class='apsl-registration-form' style='<?php if(isset($_POST['apsl_complete_registration'])){ echo "display:block"; }else{ echo "display:none"; } ?>'>
		<div class='message-wrapper'> <?php if(isset($message_string)){ echo $message_string; } ?> </div>
		<form action='' method='POST' class="clearfix">
			<?php if(isset($options['apsl_custom_username_allow']) && $options['apsl_custom_username_allow'] =='allow' ){ ?>
			<div class='apsl-registration-form-wrapper' ><label for='apsl-username-input' ><?php echo _e('Username:', APSL_TEXT_DOMAIN ); ?></label><input type='text' name='apsl-username-input' value='<?php if(isset($_POST['apsl-username-input'])){ echo $_POST['apsl-username-input']; }else{ echo $username; } ?>' id='apsl-username-input' required/></div>
			<div class='apsl-inline-info'><?php _e('Note: Your username can not be changed later, please make sure to create it.'); ?></div>
			<?php	} ?>
			
			<?php if(isset($options['apsl_custom_email_allow'])  && $options['apsl_custom_email_allow'] =='allow'){ ?>
			<div class='apsl-registration-form-wrapper'><label for='apsl-email-input' ><?php echo _e('Email:', APSL_TEXT_DOMAIN ); ?></label><input type='email' name='apsl-email-input' value='<?php if(isset($_POST['apsl-email-input'])){ echo $_POST['apsl-email-input']; }else{ echo $user_object->email; } ?>' id='apsl-email-input' required /></div>
			<?php } ?>
			<div class="apsl-registration-field-wrapper">
		        <div class="apsl-registration-user-field apsl-submit-registration">
					<input type='submit' name='apsl_complete_registration' value='<?php echo _e('Continue', APSL_TEXT_DOMAIN ); ?>' />
				</div>
			</div>		<a href='javascript:void(0);'><div class='apsl-back-button'>  ← <?php echo _e('Back', APSL_TEXT_DOMAIN ); ?></div></a>
		</form>

	</div>
	<div class='apsl-login-form' style='<?php if(isset($_POST['apsl_login_details_submit'])){ echo "display:block"; }else{ echo "display:none"; } ?>'>
			<div class='message-wrapper'> <?php if(isset($error_message)){ echo $error_message; } ?> </div>
			<form method="post" action="" class="clearfix">
		                 <div class="apsl-login-user-field-wrapper">
		                   <label><?php echo _e('Username:', APSL_TEXT_DOMAIN ); ?></label>
		                   <div class="apsl-login-user-field apsl-user-username">
		                     <input type="text" name="login_username" required="">
		                   </div>
		                 </div>
		                 <div class="apsl-login-user-field-wrapper">
		                   <label><?php echo _e('Password:', APSL_TEXT_DOMAIN ); ?></label>
		                   <div class="apsl-login-user-field apsl-user-password">
		                     <input type="password" name="login_password" required="">
		                   </div>
		                 </div>
		                 <div class="apsl-login-field-wrapper">
		                   <div class="apsl-login-user-field apsl-submit-login">
		                     <input type="submit" name="apsl_login_details_submit" value="<?php echo _e('Continue', APSL_TEXT_DOMAIN ); ?>">
		                   </div>
		                 </div>


	<a href='javascript:void(0);'><div class='apsl-back-button'> ← <?php echo _e('Back', APSL_TEXT_DOMAIN ); ?></div></a></form>
	</div>
	</div>

	</div>
<?php }else{ ?>
	<h1><?php echo _e('You are not allowed to view this page.', APSL_TEXT_DOMAIN ); ?></h1>
<?php	} ?>
</body>
<?php wp_footer(); ?>