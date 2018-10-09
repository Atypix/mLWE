<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Emails_Admin_Help_Resume extends WP_Job_Manager_Emails_Admin_Help {

	function init_config(){

		$this->default_tabs = array(
				'actions'  => array(
					'title' => __( 'Send Email Actions', 'wp-job-manager-emails' ),
				),
				'shortcodes' => array(
					'title' => __( 'Shortcodes', 'wp-job-manager-emails' )
				),
				'if_shortcode' => array(
					'title' => __( 'If Shortcode', 'wp-job-manager-emails' )
				),
				'each_shortcode' => array(
					'title' => __( 'Each Shortcode', 'wp-job-manager-emails' )
				)
		);

		$this->screens = array(
				'list' => array(
						'tabs' => array(
								'overview' => array(
										'title' => __( 'Overview', 'wp-job-manager-emails' ),
								),
								'defaults' => array(
										'title' => __( 'Defaults', 'wp-job-manager-emails' ),
								),
						),
				),
				'new'  => array(
						'tabs' => $this->tabs,
				),
				'edit' => array(
						'tabs' => $this->tabs,
				)
		);
	}

	function defaults_list(){
		$query_args = array( 'disabled_emails', 'enabled_emails', 'generate_email' );
		?>
		<br/>
		<p>
			<?php _e( 'There are two different default emails for WP Job Manager Resumes, one for when new resume\'s are submitted, and one for when visitors apply to a job with a resume.', 'wp-job-manager-emails' ); ?>
		</p>
		<p>
			<?php _e( 'These emails are automatically created and set to disabled whenever you install this plugin ... if for some reason you removed or changed them, and need to regenerate them, click the appropriate button below.', 'wp-job-manager-emails' ); ?>
		</p>
		<div class="ui warning message">
			<?php _e('There is now a fully integrated templating system included with this plugin, available on the edit/add template page. To get there, just click the Add New button, and on that page click Email Templates (defaults below will be removed in upcoming release).', 'wp-job-manager-emails'); ?>
		</div>
		<div class="ui small buttons">
			<a href="<?php echo remove_query_arg( $query_args, wp_nonce_url( add_query_arg( 'generate_email', 'resume_manager_resume_submitted' ), 'generate_email' ) ); ?>" class="ui labeled icon button">
				<i class="wizard icon"></i>
				<?php _e( 'New Resume Submitted Template', 'wp-job-manager-emails' ); ?>
			</a>
			<div class="or"></div>
			<a href="<?php echo remove_query_arg( $query_args, wp_nonce_url( add_query_arg( 'generate_email', 'applied_with_resume_email' ), 'generate_email' ) ); ?>" class="ui right labeled icon button">
				<?php _e( 'Applied with Email Template', 'wp-job-manager-emails' ); ?>
				<i class="wizard icon"></i>
			</a>
		</div>
		<?php
	}

	function overview(){
	?>
		<p>
			<?php _e('Resume Email Template Overview', 'wp-job-manager-emails'); ?>
		</p>
	<?php
	}

}