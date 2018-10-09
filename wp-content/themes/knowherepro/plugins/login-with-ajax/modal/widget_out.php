<?php
/*
 * This is the page users will see logged out.
 * You can edit this, but for upgrade safety you should copy and modify this file into your template folder.
 * The location from within your template folder is plugins/login-with-ajax/ (create these directories if they don't exist)
*/
?>

<div class="lwa lwa-template-modal">

	<div class="lwa-modal" style="display: none;">

		<form name="lwa-form" class="lwa-form js-lwa-login knowhere-form-visible" action="<?php echo esc_attr(LoginWithAjax::$url_login); ?>" method="post">

			<h3><?php esc_html_e('Log In','knowherepro') ?></h3>

			<span class="lwa-status"></span>

			<p class="username_input">
				<label><?php esc_html_e( 'Username','knowherepro' ) ?></label>
				<input type="text" name="log" id="lwa_user_login" class="input" />
			</p>

			<p class="password_input">
				<label><?php esc_html_e( 'Password','knowherepro' ) ?> <span class="kw-required"></span></label>
				<input type="password" name="pwd" id="lwa_user_pass" class="input" value="" />
			</p>

			<p><?php do_action('login_form'); ?></p>

			<div class="lwa-submit">

				<div class="kw-sm-table-row row">

					<div class="col-sm-6">

						<div class="kw-input-wrapper">

							<input name="rememberme" type="checkbox" class="kw-small" id="lwa_rememberme" value="forever" />
							<label for="lwa_rememberme"><?php esc_html_e( 'Remember Me', 'knowherepro' ) ?></label>

						</div><!--/ .kw-input-wrapper -->

					</div>

					<div class="col-sm-6">

						<div class="align-right">

							<?php if( !empty($lwa_data['remember']) ): ?>
								<small>
									<a class="knowhere-lwa-links-remember knowhere-lwa-open-remember-form" href="<?php echo esc_url(LoginWithAjax::$url_remember); ?>" title="<?php esc_attr_e('Password Lost and Found','knowherepro') ?>"><?php esc_html_e('Lost your password?','knowherepro') ?></a>
								</small>
							<?php endif; ?>

						</div><!--/ .align-right -->

					</div>

				</div>

				<p class="lwa-submit-button">
					<input type="submit" name="wp-submit" class="lwa-wp-submit" value="<?php esc_attr_e('Log In','knowherepro'); ?>" tabindex="100" />
					<input type="hidden" name="lwa_profile_link" value="<?php echo !empty($lwa_data['profile_link']) ? 1:0 ?>" />
					<input type="hidden" name="login-with-ajax" value="login" />
					<?php if( !empty($lwa_data['redirect']) ): ?>
						<input type="hidden" name="redirect_to" value="<?php echo esc_url($lwa_data['redirect']); ?>" />
					<?php endif; ?>
				</p>

				
				<br/><br/>
					<div class="">
						<small><?php esc_html_e('Don\'t have an account?', 'knowherepro') ?> <a href="https://www.mylittlewe.com/my-account/" class=""><?php esc_html_e('Register','knowherepro'); ?></a></small>
					</div>

				

			</div>

		</form>

		<?php if( !empty($lwa_data['remember']) && $lwa_data['remember'] == 1 ): ?>

			<form name="lwa-remember" class="lwa-form js-lwa-remember" action="<?php echo esc_attr(LoginWithAjax::$url_remember); ?>" method="post" style="display:none;">

				<span class="lwa-status"></span>

				<p>
					<label><?php esc_html_e("Forgotten Password", 'knowherepro'); ?></label>
					<?php $msg = esc_html__("Enter username or email", 'knowherepro'); ?>
					<input type="text" name="user_login" id="lwa_user_remember" value="<?php echo esc_attr($msg); ?>" onfocus="if(this.value == '<?php echo esc_attr($msg); ?>'){this.value = '';}" onblur="if(this.value == ''){this.value = '<?php echo esc_attr($msg); ?>'}" />
					<?php do_action('lostpassword_form'); ?>
				</p>

				<p>
					<input type="submit" value="<?php esc_attr_e('Get New Password', 'knowherepro'); ?>" />
					<a href="javascript:void(0)" class="lwa-links-remember-cancel knowhere-js-lwa-close-remember-form"><?php esc_html_e("Cancel",'knowherepro'); ?></a>
					<input type="hidden" name="login-with-ajax" value="remember" />
				</p>

			</form>

		<?php endif; ?>

		<?php if ( get_option('users_can_register') && !empty($lwa_data['registration']) && $lwa_data['registration'] == 1 ) : //Taken from wp-login.php ?>

			<div class="lwa-form lwa-register js-lwa-register">

				<form name="lwa-register" action="<?php echo esc_attr(LoginWithAjax::$url_register); ?>" method="post">

					<h3><?php esc_html_e('Sign Up','knowherepro') ?></h3>

					<span class="lwa-status"></span>

					<p>
						<label><?php $msg = esc_html__('Username','knowherepro'); echo sprintf('%s', $msg); ?></label>
						<input type="text" name="user_login" id="user_login" onfocus="if(this.value == '<?php echo esc_attr($msg); ?>'){this.value = '';}" onblur="if(this.value == ''){this.value = '<?php echo esc_attr($msg); ?>'}" />
					</p>

					<p>
						<label><?php $msg = esc_html__('E-mail','knowherepro'); echo sprintf('%s', $msg); ?></label>
						<input type="text" name="user_email" id="user_email"  onfocus="if(this.value == '<?php echo esc_attr($msg); ?>'){this.value = '';}" onblur="if(this.value == ''){this.value = '<?php echo esc_attr($msg); ?>'}"/>
					</p>

					<p>
						<?php
						//If you want other plugins to play nice, you need this:
						do_action('register_form');
						?>
					</p>

					<p>
						<small><?php esc_html_e('A password will be e-mailed to you.', 'knowherepro'); ?></small>
					</p>

					<p>
						<input type="submit" class="lwa-wp-submit" value="<?php esc_attr_e('Register','knowherepro'); ?>" tabindex="100" />
						<input type="hidden" name="login-with-ajax" value="register" />
					</p>

					<div class="kw-additional-action">
						<small><?php esc_html_e('Already have an account?', 'knowherepro') ?> <a href="javascript:void(0)" class="lwa-links-register-inline-cancel"><?php echo esc_html__('Log in!', 'knowherepro') ?></a></small>
					</div>

				</form>

			</div>

		<?php endif; ?>

	</div>

</div>
