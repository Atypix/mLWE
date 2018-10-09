<?php
	// Exit if accessed directly
	if( ! defined( 'ABSPATH' ) ) exit;
	wp_nonce_field( 'job_manager_emails_mb_from', 'job_manager_emails_mb_from_nonce' );
	$slug = $this->cpt->get_slug();
	wp_enqueue_script( 'jme-edit' );
	echo "<script type='text/javascript'>var jme_cpt_slug = '{$slug}';</script>";
	$subject = $this->get_subject();
?>

<div id="header_form" class="ui equal width large form">
	<div class="fields">
		<div class="ui left icon input fluid field tooltip">
			<input type="text" name="post_title" id="post_title" value="<?php $this->the_post_title(); ?>" placeholder="<?php _e( 'Template Title', 'wp-job-manager-emails' ); ?>" data-content="<?php _e('Choose a title to use for this specific template (only admins see this)', 'wp-job-manager-emails'); ?>" data-variation="small" data-title="<?php _e( 'Template Title', 'wp-job-manager-emails' ); ?>" data-position="bottom center">
			<i class="wordpress icon"></i>
		</div>
	</div>
	<div class="fields">
		<div class="ui left icon input fluid field tooltip">
			<input type="text" name="to" id="to" value="<?php $this->the_to(); ?>" placeholder="<?php _e( 'To (optional)', 'wp-job-manager-emails' ); ?>" data-content="<?php _e( 'Who this email should be sent to (leave blank for default).  Shortcodes are accepted. If a default value does not exist or can not be found, this email will not be sent.', 'wp-job-manager-emails' ); ?>" data-variation="small" data-title="<?php _e( 'To (optional)', 'wp-job-manager-emails' ); ?>">
			<i class="users icon"></i>
		</div>
		<div class="ui left icon input fluid field tooltip">
			<input type="text" name="bcc" id="bcc" value="<?php $this->the_bcc(); ?>" placeholder="<?php _e( 'BCC (optional)', 'wp-job-manager-emails' ); ?>" data-content="<?php _e( 'Comma separated email addresses to send a Blind Carbon Copy to.', 'wp-job-manager-emails' ); ?>" data-variation="small" data-title="<?php _e( 'BCC (optional)', 'wp-job-manager-emails' ); ?>">
			<i class="users outline icon"></i>
		</div>
	</div>
	<div class="fields">
		<div class="ui left icon input fluid field tooltip">
			<input type="text" name="subject" id="subject" value="<?php echo htmlspecialchars( $subject, ENT_QUOTES | ENT_HTML401 ); ?>" placeholder="<?php _e( 'Email Subject', 'wp-job-manager-emails' ); ?>" data-content="<?php _e( 'The subject to use for this email template.  You can use shortcodes in the email subject.', 'wp-job-manager-emails' ); ?>" data-variation="small" data-title="<?php _e( 'Email Subject', 'wp-job-manager-emails' ); ?>">
			<i class="mail outline icon"></i>
		</div>
	</div>
	<div id="jme_advanced_headers" class="ui fluid accordion">
		<div class="title"><i class="dropdown icon"></i><?php _e('Advanced', 'wp-job-manager-emails' ); ?></div>
		<div class="content">
			<div class="fields">
				<div class="field ui left icon input with tooltip">
					<input type="text" name="from_name" id="from_name" value="<?php $this->the_from_name(); ?>" placeholder="<?php echo $this->cpt()->default_from_name(); ?>" data-content="<?php _e( 'This only needs to be set if you want to use a custom From Name for this template, otherwise the default will be used.  The default value is the value seen (placeholder) when no value has been entered.', 'wp-job-manager-emails' ); ?>" data-variation="small" data-title="<?php _e( 'From Name (optional)', 'wp-job-manager-emails' ); ?>">
					<i class="user icon"></i>
				</div>
				<div class="field ui left icon input tooltip">
					<input type="text" name="from_email" id="from_email" value="<?php $this->the_from_email(); ?>" placeholder="<?php echo $this->cpt()->default_from_email(); ?>" data-content="<?php _e( 'This only needs to be set if you want to use a custom From Email for this template, otherwise the default will be used. The default value is the value seen (placeholder) when no value has been entered.', 'wp-job-manager-emails' ); ?>" data-variation="small" data-title="<?php _e( 'From Email (optional)', 'wp-job-manager-emails' ); ?>">
					<i class="at icon"></i>
				</div>
				<div class="field ui left icon input tooltip">
					<input type="text" name="reply_to" id="reply_to" value="<?php $this->the_reply_to(); ?>" placeholder="" data-content="<?php _e( 'Set a custom Reply-To address in the email (shortcodes supported)', 'wp-job-manager-emails' ); ?>" data-variation="small" data-title="<?php _e( 'Reply-To (optional)', 'wp-job-manager-emails' ); ?>">
					<i class="reply icon"></i>
				</div>
			</div>
			<div class="field">
				<?php
					$exclude_values = $this->get_exclude() ? maybe_unserialize( $this->get_exclude() ) : array();
					wp_nonce_field( 'jme_exclude_nonce', 'jme_exclude_nonce' );
				?>
				<div id="exclude_dropdown" class="ui fluid small search multiple selection dropdown fluid_auto exclude_dropdown" data-content="<?php _e( 'Search and add any user, or custom email address to exclude from being sent this email when it is triggered. Type in at least 3 characters to start search, or a valid email address to add.', 'wp-job-manager-emails' ); ?>" data-variation="small" data-title="<?php _e( 'Exclude from Email (optional)', 'wp-job-manager-emails' ); ?>">
					<input id="exclude_dropdown_val" type="hidden" name="exclude" value="<?php echo implode( ',', $exclude_values ); ?>">
					<i class="dropdown icon"></i>
					<div class="default text"><?php _e( 'Start typing to search for user, or enter a valid email address to exclude from being sent this email', 'wp-job-manager-emails' ); ?></div>
					<div id="exclude_menu" class="menu">
						<?php
							foreach ( (array) $exclude_values as $exclude_value ):
								$exclude_label = $exclude_value;
								$exclude_text  = $exclude_value;

								if ( strpos( $exclude_value, 'userid_' ) !== FALSE ) {
									$user_id = str_replace( 'userid_', '', $exclude_value );
									$user    = get_user_by( 'ID', $user_id );

									if ( $user instanceof WP_User ) {
										$exclude_label = '<i class="user icon"></i>' . $user->user_login;
										$exclude_text  = $user->user_login;
									} else {
										continue;
									}
								}
								?>
								<div class="item" data-value="<?php echo $exclude_value; ?>" data-text="<?php echo $exclude_text; ?>">
									<?php echo $exclude_label; ?>
								</div>
								<?php
							endforeach;
						?>
					</div>
				</div>
			</div>
			<div class="fields">
				<div class="ui left icon input fluid field tooltip">
					<input type="text" name="template_name" id="template_name" value="<?php $this->the_template_name(); ?>" placeholder="<?php _e( 'my_custom_template_name_here', 'wp-job-manager-emails' ); ?>" data-content="<?php _e( 'Set a custom template name for this email template.  This is only needed if you plan on using filters or custom PHP code, and need some way to identify this specific email template.  You SHOULD only use alphanumeric characters, without spaces, to identify templates easily.', 'wp-job-manager-emails' ); ?>" data-variation="small" data-title="<?php _e( 'Template Name', 'wp-job-manager-emails' ); ?>" data-position="bottom center">
					<i class="wordpress icon"></i>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include_once JOB_MANAGER_EMAILS_PLUGIN_DIR . '/includes/admin/views/modals.php'; ?>