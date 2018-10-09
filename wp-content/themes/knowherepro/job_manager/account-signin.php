<?php if ( is_user_logged_in() ) : ?>

	<fieldset class="fieldset-account-sign-in">
		<div class="field account-sign-in">

			<div class="field-sign-in">
				<?php
				$user = wp_get_current_user();
				printf( __( 'You are currently signed in as <strong>%s</strong>.', 'knowherepro' ), $user->user_login );
				?>
			</div>

			<div class="field-sign-button">
				<a class="button" href="<?php echo apply_filters( 'submit_job_form_logout_url', wp_logout_url( get_permalink() ) ); ?>"><?php esc_html_e( 'Sign Out', 'knowherepro' ); ?></a>
			</div>

		</div>
	</fieldset>

<?php else :

	$account_required             = job_manager_user_requires_account();
	$registration_enabled         = job_manager_enable_registration();
	$generate_username_from_email = job_manager_generate_username_from_email();
	?>
	<fieldset>
		<label><?php esc_html_e( 'Have an account?', 'knowherepro' ); ?></label>
		<div class="field account-sign-in lwa">
			<a class="button lwa-links-modal" href="<?php echo apply_filters( 'submit_job_form_login_url', wp_login_url( get_permalink() ) ); ?>"><?php _e( 'Sign in', 'knowherepro' ); ?></a>

			<?php if ( $registration_enabled ) : ?>

				<?php printf( __( 'If you don&rsquo;t have an account you can %screate one below by entering your email address/username. Your account details will be confirmed via email.', 'knowherepro' ), $account_required ? '' : __( 'optionally', 'knowherepro' ) . ' ' ); ?>

			<?php elseif ( $account_required ) : ?>

				<?php echo apply_filters( 'submit_job_form_login_required_message',  esc_html__('You must sign in to create a new listing.', 'knowherepro' ) ); ?>

			<?php endif; ?>
		</div>
	</fieldset>
	<?php if ( $registration_enabled ) : ?>
		<?php if ( ! $generate_username_from_email ) : ?>
			<fieldset>
				<label><?php esc_html_e( 'Username', 'knowherepro' ); ?> <?php echo apply_filters( 'submit_job_form_required_label', ( ! $account_required ) ? ' <small>' . esc_html__( '(optional)', 'knowherepro' ) . '</small>' : '' ); ?></label>
				<div class="field">
					<input type="text" class="input-text" name="create_account_username" id="account_username" value="<?php echo empty( $_POST['create_account_username'] ) ? '' : esc_attr( sanitize_text_field( stripslashes( $_POST['create_account_username'] ) ) ); ?>" />
				</div>
			</fieldset>
		<?php endif; ?>
		<fieldset>
			<label><?php esc_html_e( 'Your email', 'knowherepro' ); ?> <?php echo apply_filters( 'submit_job_form_required_label', ( ! $account_required ) ? ' <small>' . esc_html__( '(optional)', 'knowherepro' ) . '</small>' : '' ); ?></label>
			<div class="field">
				<input type="email" class="input-text" name="create_account_email" id="account_email" placeholder="you@yourdomain.com" value="<?php echo empty( $_POST['create_account_email'] ) ? '' : esc_attr( sanitize_text_field( stripslashes( $_POST['create_account_email'] ) ) ); ?>" />
			</div>
		</fieldset>
		<?php do_action( 'job_manager_register_form' ); ?>
		
	<?php endif; ?>

<?php endif; ?>
