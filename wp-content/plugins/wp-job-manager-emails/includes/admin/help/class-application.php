<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Emails_Admin_Help_Application extends WP_Job_Manager_Emails_Admin_Help {

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

	function overview(){
	?>
		<p>
			<?php _e('Job Application Email Template Overview', 'wp-job-manager-emails'); ?>
		</p>
	<?php
	}

}