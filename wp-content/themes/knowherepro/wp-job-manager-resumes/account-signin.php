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
				<a class="button" href="<?php echo apply_filters( 'submit_resume_form_logout_url', wp_logout_url( get_permalink() ) ); ?>"><?php _e( 'Sign out', 'knowherepro' ); ?></a>
			</div>

		</div>
	</fieldset>

<?php else :

	$account_required             = resume_manager_user_requires_account();
	$registration_enabled         = resume_manager_enable_registration();
	$generate_username_from_email = resume_manager_generate_username_from_email();
	?>
	<fieldset>
		<label><?php esc_html_e( 'Have an account?', 'knowherepro' ); ?></label>
		<div class="field account-sign-in">
			<a class="button" href="<?php echo apply_filters( 'submit_resume_form_login_url', wp_login_url( add_query_arg( array( 'job_id' => $class->get_job_id() ), get_permalink() ) ) ); ?>"><?php _e( 'Sign in', 'knowherepro' ); ?></a>

			<?php if ( $registration_enabled ) : ?>

				<?php _e( 'If you don&rsquo;t have an account you can create one below by entering your email address. Your account details will be confirmed via email.', 'knowherepro' ); ?>

			<?php elseif ( $account_required ) : ?>

				<?php echo apply_filters( 'submit_resume_form_login_required_message',  __( 'You must sign in to submit a resume.', 'knowherepro' ) ); ?>

			<?php endif; ?>
		</div>
	</fieldset>
	<?php if ( $registration_enabled ) : ?>
		<?php if ( ! $generate_username_from_email ) : ?>
			<fieldset>
				<label><?php _e( 'Username', 'knowherepro' ); ?> <?php echo apply_filters( 'submit_resume_form_required_label', ( ! $account_required ) ? ' <small>' . __( '(optional)', 'knowherepro' ) . '</small>' : '' ); ?></label>
				<div class="field">
					<input type="text" class="input-text" name="create_account_username" id="account_username" value="<?php if ( ! empty( $_POST['create_account_username'] ) ) echo sanitize_text_field( stripslashes( $_POST['create_account_username'] ) ); ?>" />
				</div>
			</fieldset>
		<?php endif; ?>
		<?php do_action( 'resume_manager_register_form' ); ?>
	<?php endif; ?>

<?php endif; ?>
