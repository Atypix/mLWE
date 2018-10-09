<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Emails_Hooks_PostStatus
 *
 * @since 2.1.0
 *
 */
class WP_Job_Manager_Emails_Hooks_PostStatus {

	/**
	 * @var
	 */
	public $ps_actions;

	/**
	 * Add Post Status actions and filters (hooks)
	 *
	 *
	 * @since 2.0.0
	 *
	 */
	function poststatus_hooks(){

		// Clear tracked emails for soon to expire emails
		add_action( 'expired_to_publish', array($this, 'clear_sent_mail_meta') );
		add_action( 'added_post_meta', array( $this, 'add_new_listing_meta' ), 10, 4 );

		/**
		 * Start post_type actions
		 */
		$statuses = $this->get_post_statuses();

		foreach ( (array) $statuses as $sfrom ) {

			foreach ( (array) $statuses as $sto ) {
				$action = "{$sfrom}_to_{$sto}";
				// Skip to next if from is same as to, or if action already exists
				if ( $sfrom === $sto || has_action( $action, array( $this, "do_{$action}" ) ) ) {
					continue;
				}

				// Add Post Status action
				add_action( $action, array( $this, "do_{$action}" ) );
			}

		}
	}

	/**
	 * Set Post Status action config sub array value
	 *
	 * This method is used by extending classes to set sub array configuration for
	 * post status action hooks.  An example would be to set `attachments` in the `defaults`
	 * config array, without having to define all config in `defaults`
	 *
	 * @since 2.0.0
	 *
	 * @param $hook
	 * @param $sub
	 * @param $key
	 * @param $val
	 *
	 * @return bool
	 */
	function set_ps_action_sub( $hook, $sub, $key, $val ) {

		if( array_key_exists( $hook, $this->ps_actions ) ){
			$this->ps_actions[$sub][$key] = $val;
			return true;
		}

		return false;
	}

	/**
	 * Set Post Status action hook config value
	 *
	 * This method will overwrite and set value for top level array config
	 * based on passed key and value.
	 *
	 * @since 2.0.0
	 *
	 * @param $hook
	 * @param $key
	 * @param $val
	 *
	 * @return bool
	 */
	function set_ps_action( $hook, $key, $val ) {

		if ( array_key_exists( $hook, $this->ps_actions ) ) {
			$this->ps_actions[ $key ] = $val;
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Init Post Status Actions
	 *
	 *
	 * @since 2.0.0
	 *
	 */
	public function init_ps_actions() {

		$singular    = $this->cpt()->get_singular();
		$singular_lc = strtolower( $singular );
		$ppost_type   = $this->cpt()->get_ppost_type();
		$post_title  = $this->post_title;

		$ps_actions = array(
				"{$ppost_type}_soon_to_expire" => array(
					'label'      => sprintf( __( '%s Expiring Soon', 'wp-job-manager-emails' ), $singular ),
					'desc'       => sprintf( __( 'When a %s is about to expire', 'wp-job-manager-emails' ), $singular ),
					'defaults'   => array(
						'to'           => "[{$singular_lc}_author_email]",
						'post_content' => $this->soon_to_expire_default_content(),
						'subject'      => sprintf( __( 'Your %1$s, %2$s, is about to expire!', 'wp-job-manager-emails' ), $singular, $post_title ),
						'post_title'   => sprintf( __( '%s About to Expire', 'wp-job-manager-emails' ), $singular ),
					),
					'shortcodes' => array(
						'days_before_expire' => array(
							'label'        => sprintf( __( 'Days until %s expires', 'wp-job-manager-emails' ), $singular ),
							'description'  => __( 'Will output number of days until listing will expire', 'wp-job-manager-emails' ),
							'default'      => 7,
							'nonmeta'      => TRUE,
							'templatemeta' => TRUE,
							'visible'      => FALSE
						),
					),
					'poststatus' => TRUE,
					'inputs'     => array(
						'days_before_expire' => array(
							'label'       => __( 'Days before expiration', 'wp-job-manager-emails' ),
							'type'        => 'text',
							'placeholder' => '7',
							'icon'        => array(
								'name'     => 'wait',
								'position' => 'left'
							),
							'help'        => __( 'Number of days before listing expires to send email (default 7)', 'wp-job-manager-emails' )
						),
					)
				),
				"preview_to_publish_{$ppost_type}"         => array(
					'label'    => sprintf( __( 'New %s Published (admin)', 'wp-job-manager-emails' ), $singular ),
					'desc'     => sprintf( __( 'When a New %s is Published (and does not required approval or payment)', 'wp-job-manager-emails' ), $singular ),
					'ext_desc' => __( 'This action will only fire when new listing DO NOT require approval, or payment (after submitting listing).  This will still fire even if preview step is removed.', 'wp-job-manager-emails' ),
					'defaults' => array(
						'to'           => '[admin_email]',
						'post_content' => $this->preview_to_publish_default_content(),
						'subject'      => sprintf( __( 'New %1$s Published, %2$s', 'wp-job-manager-emails' ), $singular, $post_title ),
						'post_title'   => sprintf( __( 'New %s Published (admin)', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => true,
					'templates' => array(
						array(
							'label' => sprintf( __( 'New %s Published (user)', 'wp-job-manager-emails' ), $singular ),
							'to'           => "[{$singular_lc}_author_email]",
							'post_content' => $this->preview_to_publish_default_user_content(),
							'subject'    => sprintf( __( 'Your %1$s "%2$s" is Published!', 'wp-job-manager-emails' ), $singular, $post_title ),
							'post_title' => sprintf( __( 'New %s Published (user)', 'wp-job-manager-emails' ), $singular ),
						)
					)
				),
				"preview_to_pending_{$ppost_type}"         => array(
					'label'    => sprintf( __( 'New %s Pending Approval', 'wp-job-manager-emails' ), $singular ),
					'desc'     => sprintf( __( 'When a New %s is Pending Approval', 'wp-job-manager-emails' ), $singular ),
					'defaults' => array(
						'to'           => '[admin_email]',
						'post_content' => $this->preview_to_pending_default_content(),
						'subject'      => sprintf( __( 'New %1$s Pending Approval, %2$s', 'wp-job-manager-emails' ), $singular, $post_title ),
						'post_title'   => sprintf( __( 'New %s Pending Approval', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => TRUE
				),
				"preview_to_pending_payment_{$ppost_type}" => array(
					'label'    => sprintf( __( 'New %s Pending Payment', 'wp-job-manager-emails' ), $singular ),
					'desc'     => sprintf( __( 'When a New %s is Pending Payment', 'wp-job-manager-emails' ), $singular ),
					'defaults' => array(
						'to'         => '[admin_email]',
						'post_content' => $this->preview_to_pending_payment_default_content(),
						'subject'    => sprintf( __( 'New %1$s Pending Payment, %2$s', 'wp-job-manager-emails' ), $singular, $post_title ),
						'post_title' => sprintf( __( 'New %s Pending Payment', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => TRUE
				),
				"pending_to_publish_{$ppost_type}"         => array(
					'label'    => sprintf( __( '%s Approved', 'wp-job-manager-emails' ), $singular ),
					'desc'     => sprintf( __( 'When a pending %s is Approved', 'wp-job-manager-emails' ), $singular ),
					'defaults' => array(
						'to'           => "[{$singular_lc}_author_email]",
						'post_content' => $this->pending_to_publish_default_content(),
						'subject'      => sprintf( __( 'Your %1$s, %2$s, was Approved!', 'wp-job-manager-emails' ), $singular, $post_title ),
						'post_title'   => sprintf( __( 'Your %s was Approved', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => TRUE
				),
				"pending_payment_to_publish_{$ppost_type}" => array(
					'label'    => sprintf( __( '%s Pending Payment Completed', 'wp-job-manager-emails' ), $singular ),
					'desc'     => sprintf( __( 'When a %s pending payment is completed.', 'wp-job-manager-emails' ), $singular ),
					'ext_desc' => __( 'Make sure to test this email yourself, as the system used for payments may also be sending out an email when this happens.', 'wp-job-manager-emails' ),
					'defaults' => array(
						'to'           => '[admin_email]',
						'post_content' => $this->pending_payment_to_publish_default_content(),
						'subject'      => sprintf( __( '%1$s Payment Completed, %2$s', 'wp-job-manager-emails' ), $singular, $post_title ),
						'post_title'   => sprintf( __( '%s Pending Payment Completed/Approved', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => TRUE
				),
				"pending_payment_to_pending_{$ppost_type}" => array(
					'label'    => sprintf( __( '%s Pending Approval, Payment Completed', 'wp-job-manager-emails' ), $singular ),
					'desc'     => sprintf( __( 'When a %s pending payment is completed and is pending approval.', 'wp-job-manager-emails' ), $singular ),
					'ext_desc' => sprintf( __( 'This email will be sent once a %s that was pending payment, has been completed, and is now pending/awaiting approval.', 'wp-job-manager-emails' ), $singular ),
					'defaults' => array(
						'to'           => '[admin_email]',
						'post_content' => $this->pending_payment_to_pending_default_content(),
						'subject'      => sprintf( __( '%1$s (Paid) Pending Approval, %2$s', 'wp-job-manager-emails' ), $singular, $post_title ),
						'post_title'   => sprintf( __( '%s Pending Approval, Payment Completed', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => TRUE
				),
				"publish_to_expired_{$ppost_type}"         => array(
					'label'    => sprintf( __( '%s Expired', 'wp-job-manager-emails' ), $singular ),
					'desc'     => sprintf( __( 'When a %s expires.', 'wp-job-manager-emails' ), $singular ),
					'defaults' => array(
						'to'           => "[{$singular_lc}_author_email]",
						'post_content' => $this->publish_to_expired_default_content(),
						'subject'      => sprintf( __( '%1$s Expired, %2$s', 'wp-job-manager-emails' ), $singular, $post_title ),
						'post_title'   => sprintf( __( '%s Expired', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => TRUE
				),
				"expired_to_publish_{$ppost_type}"         => array(
					'label'    => sprintf( __( '%s Relisted', 'wp-job-manager-emails' ), $singular ),
					'desc'     => sprintf( __( 'When an expired %s is relisted.', 'wp-job-manager-emails' ), $singular ),
					'defaults' => array(
						'to'           => '[admin_email]',
						'post_content' => $this->expired_to_publish_default_content(),
						'subject'      => sprintf( __( '%1$s Relisted, %2$s', 'wp-job-manager-emails' ), $singular, $post_title ),
						'post_title'   => sprintf( __( 'Expired %s Relisted', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => TRUE
				),
				"expired_to_trash_{$ppost_type}"           => array(
					'label'    => sprintf( __( 'Expired %s Removed', 'wp-job-manager-emails' ), $singular ),
					'desc'     => sprintf( __( 'When an expired %s is sent to the trash (to be removed).', 'wp-job-manager-emails' ), $singular ),
					'ext_desc' => __( 'WP Job Manager will automatically send expired to trash if you have it configured to do so, which will trigger this email.', 'wp-job-manager-emails' ),
					'defaults' => array(
						'to'           => '[admin_email]',
						'post_content' => $this->expired_to_trash_default_content(),
						'subject'      => sprintf( __( 'Expired %1$s Removed, %2$s', 'wp-job-manager-emails' ), $singular, $post_title ),
						'post_title'   => sprintf( __( 'Expired %s Removed', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => TRUE
				),
				"publish_to_trash_{$ppost_type}"           => array(
					'label'    => sprintf( __( '%s Removed', 'wp-job-manager-emails' ), $singular ),
					'desc'     => sprintf( __( 'When a %s is sent to the trash (to be removed).', 'wp-job-manager-emails' ), $singular ),
					'defaults' => array(
						'to'           => '[admin_email]',
						'post_content' => $this->publish_to_trash_default_content(),
						'subject'      => sprintf( __( '%1$s Removed, %2$s', 'wp-job-manager-emails' ), $singular, $post_title ),
						'post_title'   => sprintf( __( '%s Removed', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => TRUE
				),
				"publish_{$ppost_type}" => array(
					'label'      => sprintf( __( '%s Published (Status)', 'wp-job-manager-emails' ), $singular ),
					'desc'       => sprintf( __( 'When a %s status is changed to published', 'wp-job-manager-emails' ), $singular ),
					'ext_desc'   => __( 'This action will fire whenever a listing status is updated to published.', 'wp-job-manager-emails' ),
					'warning'    => __( 'This action will ALWAYS be executed when a listing status is set to published (visible on frontend)! <strong>This should ONLY be used by those who KNOW what they are doing!</strong>', 'wp-job-manager-emails' ),
					'defaults'   => array(
						'to'           => '[admin_email]',
						'post_content' => $this->preview_to_publish_default_content(),
						'subject'      => sprintf( __( '%1$s Status set to Published, %2$s', 'wp-job-manager-emails' ), $singular, $post_title ),
						'post_title'   => sprintf( __( '%s Published (Status)', 'wp-job-manager-emails' ), $singular ),
					),
					'poststatus' => TRUE
				),
			);

		$ps_actions = $this->add_custom_statuses( $ps_actions );

		$this->ps_actions = apply_filters( 'job_manager_emails_post_status_actions', $ps_actions, $this );

		return $this->ps_actions;
	}

	/**
	 * Add Custom Post Status Hooks
	 *
	 *
	 * @since 2.1.0
	 *
	 * @param $ps_actions
	 *
	 * @return mixed
	 */
	public function add_custom_statuses( $ps_actions ){

		$custom_ps = $this->get_custom_statuses();

		if ( ! empty( $custom_ps ) && method_exists( $this, 'get_core_statuses' ) ) {

			$singular    = $this->cpt()->get_singular();
			$ppost_type  = $this->cpt()->get_ppost_type();

			$all_statuses = $this->get_core_statuses( TRUE );
			// Loop each custom status
			foreach ( (array) $custom_ps as $cs => $cs_label ) {

				// Loop each default status (to build customstatus_to_defaultstatus and defaultstatus_to_customstatus hooks)
				foreach ( (array) $all_statuses as $as => $as_label ) {
					// Goto next if custom status is equal to status in all loop
					if ( $cs === $as ) {
						continue;
					}

					// All Statuses to Custom Status
					$a_to_c = "{$as}_to_{$cs}_{$ppost_type}";
					if ( ! array_key_exists( $a_to_c, $ps_actions ) ) {
						$ps_actions[ $a_to_c ] = array(
							'label'      => sprintf( __( '%1$s %2$s to %3$s', 'wp-job-manager-emails' ), $singular, $as_label, $cs_label ),
							'desc'       => sprintf( __( 'When a %1$s is updated from %2$s to %3$s.', 'wp-job-manager-emails' ), $singular, $as_label, $cs_label ),
							'poststatus' => TRUE,
							'job_fields' => TRUE,
							'custom_ps'  => TRUE,
							'metaboxes'  => array( 'job_shortcodes' )
						);
					}

					// Custom to Default Status
					$c_to_d = "{$cs}_to_{$as}_{$ppost_type}";
					if ( ! array_key_exists( $c_to_d, $ps_actions ) ) {
						$ps_actions[ $c_to_d ] = array(
							'label'      => sprintf( __( '%1$s %2$s to %3$s', 'wp-job-manager-emails' ), $singular, $cs_label, $as_label ),
							'desc'       => sprintf( __( 'When a %1$s is updated from %2$s to %3$s.', 'wp-job-manager-emails' ), $singular, $cs_label, $as_label ),
							'poststatus' => TRUE,
							'job_fields' => TRUE,
							'custom_ps'  => TRUE,
							'metaboxes'  => array( 'job_shortcodes' )
						);
					}

				}

			}

		}

		return $ps_actions;
	}

	/**
	 * Send email for post status update action
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param $type
	 * @param $post
	 */
	function post_status_hook( $type, $post ) {

		$ppost_type = $this->cpt()->get_ppost_type();

		/**
		 * Verify post type to prevent duplicate method runs and emails
		 *
		 * This method is called by the magic method for any post status hooks, so
		 * it could be called multiple times for each extending hook class.
		 */
		if ( $post->post_type !== $ppost_type ) {
			return;
		}

		$this->hook    = "{$type}_{$ppost_type}";
		$custom_emails = $this->cpt()->get_emails( $this->hook );

		if( empty( $custom_emails ) ) return;

		$this->cpt()->send_email( $custom_emails, $post->ID );

	}

	/**
	 * Remove expire email sent meta from listing
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param $post
	 */
	function clear_sent_mail_meta( $post ) {

		// Expired listing relisted, so we need to remove `_expire_email_sent` meta
		// which stored email template IDs already sent for upcoming expired listings.
		delete_post_meta( $post->ID, '_expire_email_sent' );
	}

	/**
	 * Check if we need to send email about soon to expire listing
	 *
	 *
	 * @since 2.0.0
	 *
	 */
	function check_soon_to_expire_listings() {

		global $wpdb;

		$ppost_type  = $this->cpt()->get_ppost_type();
		$singular   = $this->cpt()->get_singular( TRUE );
		$this->hook = "{$ppost_type}_soon_to_expire";

		$custom_emails = $this->cpt()->get_emails( $this->hook );

		if ( empty( $custom_emails ) ) return;

		foreach( (array) $custom_emails as $custom_email ) {

			// Pull config from email template, or use 7 days as default
			$days_before_expire = empty( $custom_email->days_before_expire ) ? 7 : $custom_email->days_before_expire;

			// Build expirey to check, based on current timestamp plus X days
			$check_expirey = strtotime( "+ {$days_before_expire} days" );

			$listing_ids = $wpdb->get_col( $wpdb->prepare( "
				SELECT postmeta.post_id FROM {$wpdb->postmeta} as postmeta
				LEFT JOIN {$wpdb->posts} as posts ON postmeta.post_id = posts.ID
				WHERE postmeta.meta_key = '_{$singular}_expires'
				AND postmeta.meta_value > 0
				AND postmeta.meta_value < %s
				AND posts.post_status = 'publish'
				AND posts.post_type = '{$ppost_type}'
			", date( 'Y-m-d', $check_expirey ) ) );

			if ( $listing_ids ) {

				foreach( $listing_ids as $listing_id ) {

					// Continue to next in loop (means this email notice has already been sent)
					$email_sent = get_post_meta( $listing_id, '_expire_email_sent' );
					if ( in_array( $custom_email->ID, $email_sent ) ) continue;

					$this->cpt()->send_email( $custom_email, $listing_id );

					// Add this custom email template ID to listing meta of emails sent
					add_post_meta( $listing_id, '_expire_email_sent', $custom_email->ID );
				}

			}
		}
	}

	/**
	 * New listing (no approval required) default content
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	function preview_to_publish_default_content() {

		$singular    = $this->cpt()->get_singular();
		$singular_lc = strtolower( $singular );

		$content = '';
		$content .= __( 'Hello', 'wp-job-manager-emails' ) . "\n" . "\n";
		$content .= sprintf( __( 'A new %1$s has just been submitted by *%2$s*, and is now published.  The details are as follows:', 'wp-job-manager-emails' ), $singular, $this->submitted_by ) . "\n" . "\n";
		$content .= "[divider]" . "\n" . "[{$singular_lc}_fields]" . "\n" . "[/divider]" . "\n" . "\n";
		$content .= $this->post_content . "\n" . sprintf( __( 'The %s content is as follows:', 'wp-job-manager-emails' ), $singular ) . "\n" . str_replace( '[', '[/', $this->post_content ) . "\n" . "\n";
		$content .= sprintf( __( 'You can view this %1$s here: %2$s', 'wp-job-manager-emails' ), $singular, "[view_{$singular_lc}_url]" ) . "\n";
		$content .= sprintf( __( 'You can view this %1$s in the backend, by clicking here: %2$s', 'wp-job-manager-emails' ), $singular, "[view_{$singular_lc}_url_admin]" ) . "\n" . "\n";

		return $content;
	}

	/**
	 * New listing (no approval required) default user content
	 *
	 *
	 * @since @@since
	 *
	 * @return string
	 */
	function preview_to_publish_default_user_content() {

		$singular    = $this->cpt()->get_singular();
		$singular_lc = strtolower( $singular );

		$content = '';
		$content .= __( 'Hello', 'wp-job-manager-emails' ) . "\n" . "\n";
		$content .= sprintf( __( 'Your listing is now published.  The details are as follows:', 'wp-job-manager-emails' ), $singular, $this->submitted_by ) . "\n" . "\n";
		$content .= "[divider]" . "\n" . "[{$singular_lc}_fields]" . "\n" . "[/divider]" . "\n" . "\n";
		$content .= $this->post_content . "\n" . sprintf( __( 'The %s content is as follows:', 'wp-job-manager-emails' ), $singular ) . "\n" . str_replace( '[', '[/', $this->post_content ) . "\n" . "\n";
		$content .= sprintf( __( 'You can view this %1$s here: %2$s', 'wp-job-manager-emails' ), $singular, "[view_{$singular_lc}_url]" ) . "\n";

		return $content;
	}

	/**
	 * Expired listing sent to trash (removed after 30 days [default])
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	function expired_to_trash_default_content() {

		$singular    = $this->cpt()->get_singular();
		$singular_lc = strtolower( $singular );

		$content = '';
		$content .= __( 'Hello', 'wp-job-manager-emails' ) . "\n" . "\n";
		$content .= sprintf( __( 'An expired %1$s has just been moved to the trash to be deleted.  The details are as follows:', 'wp-job-manager-emails' ), $singular, $this->submitted_by ) . "\n" . "\n";
		$content .= "[divider]" . "\n" . "[{$singular_lc}_fields]" . "\n" . "[/divider]" . "\n" . "\n";
		$content .= $this->post_content . "\n" . sprintf( __( 'The %s content is as follows:', 'wp-job-manager-emails' ), $singular ) . "\n" . str_replace( '[', '[/', $this->post_content ) . "\n" . "\n";
		$content .= __( 'This listing will be permanently removed after 30 days (WordPress default), unless Trash is disabled (enabled by default).', 'wp-job-manager-emails' ) . "\n" . "\n";
		$content .= sprintf( __( 'You can view this %1$s in the backend, by clicking here: %2$s', 'wp-job-manager-emails' ), $singular, "[view_{$singular_lc}_url_admin]" ) . "\n" . "\n";

		return $content;
	}

	/**
	 * Active listing sent to trash (removed after 30 days [default])
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	function publish_to_trash_default_content() {

		$singular    = $this->cpt()->get_singular();
		$singular_lc = strtolower( $singular );

		$content = '';
		$content .= __( 'Hello', 'wp-job-manager-emails' ) . "\n" . "\n";
		$content .= sprintf( __( 'A previously active %1$s (non-expired) has just been moved to the trash to be deleted.  The details are as follows:', 'wp-job-manager-emails' ), $singular, $this->submitted_by ) . "\n" . "\n";
		$content .= "[divider]" . "\n" . "[{$singular_lc}_fields]" . "\n" . "[/divider]" . "\n" . "\n";
		$content .= $this->post_content . "\n" . sprintf( __( 'The %s content is as follows:', 'wp-job-manager-emails' ), $singular ) . "\n" . str_replace( '[', '[/', $this->post_content ) . "\n" . "\n";
		$content .= __( 'This listing will be permanently removed after 30 days (WordPress default), unless Trash is disabled (enabled by default).', 'wp-job-manager-emails' ) . "\n" . "\n";
		$content .= sprintf( __( 'You can view this %1$s in the backend, by clicking here: %2$s', 'wp-job-manager-emails' ), $singular, "[view_{$singular_lc}_url_admin]" ) . "\n" . "\n";

		return $content;
	}

	/**
	 * Expired listing relisted default content
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	function expired_to_publish_default_content() {

		$singular    = $this->cpt()->get_singular();
		$singular_lc = strtolower( $singular );

		$content = '';
		$content .= __( 'Hello', 'wp-job-manager-emails' ) . "\n" . "\n";
		$content .= sprintf( __( 'A previously expired %1$s, has just been relisted.  The details are as follows:', 'wp-job-manager-emails' ), $singular, $this->submitted_by ) . "\n" . "\n";
		$content .= "[divider]" . "\n" . "[{$singular_lc}_fields]" . "\n" . "[/divider]" . "\n" . "\n";
		$content .= $this->post_content . "\n" . sprintf( __( 'The %s content is as follows:', 'wp-job-manager-emails' ), $singular ) . "\n" . str_replace( '[', '[/', $this->post_content ) . "\n" . "\n";
		$content .= sprintf( __( 'You can view this %1$s here: %2$s', 'wp-job-manager-emails' ), $singular, "[view_{$singular_lc}_url]" ) . "\n";
		$content .= sprintf( __( 'You can view this %1$s in the backend, by clicking here: %2$s', 'wp-job-manager-emails' ), $singular, "[view_{$singular_lc}_url_admin]" ) . "\n" . "\n";

		return $content;
	}

	/**
	 * New listing pending approval default content
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	function preview_to_pending_default_content() {

		$singular    = $this->cpt()->get_singular();
		$singular_lc = strtolower( $singular );

		$content = '';
		$content .= __( 'Hello', 'wp-job-manager-emails' ) . "\n" . "\n";
		$content .= sprintf( __( 'A new %1$s has just been submitted by *%2$s*, and is pending approval.  The details are as follows:', 'wp-job-manager-emails' ), $singular, $this->submitted_by ) . "\n" . "\n";
		$content .= "[divider]" . "\n" . "[{$singular_lc}_fields]" . "\n" . "[/divider]" . "\n" . "\n";
		$content .= $this->post_content . "\n" . sprintf( __( 'The %s content is as follows:', 'wp-job-manager-emails' ), $singular ) . "\n" . str_replace( '[', '[/', $this->post_content ) . "\n" . "\n";
		$content .= sprintf( __( 'You can view this %1$s here: %2$s', 'wp-job-manager-emails' ), $singular, "[view_{$singular_lc}_url]" ) . "\n";
		$content .= sprintf( __( 'You can view this %1$s in the backend, and approve it, by clicking here: %2$s', 'wp-job-manager-emails' ), $singular, "[view_{$singular_lc}_url_admin]" ) . "\n" . "\n";

		return $content;
	}

	/**
	 * New listing pending payment
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	function preview_to_pending_payment_default_content() {

		$singular    = $this->cpt()->get_singular();
		$singular_lc = strtolower( $singular );

		$content = '';
		$content .= __( 'Hello', 'wp-job-manager-emails' ) . "\n" . "\n";
		$content .= sprintf( __( 'A new %1$s has just been submitted by *%2$s*, and is now pending payment.  The details are as follows:', 'wp-job-manager-emails' ), $singular, $this->submitted_by ) . "\n" . "\n";
		$content .= "[divider]" . "\n" . "[{$singular_lc}_fields]" . "\n" . "[/divider]" . "\n" . "\n";
		$content .= $this->post_content . "\n" . sprintf( __( 'The %s content is as follows:', 'wp-job-manager-emails' ), $singular ) . "\n" . str_replace( '[', '[/', $this->post_content ) . "\n" . "\n";
		$content .= sprintf( __( 'You can view this %1$s here: %2$s', 'wp-job-manager-emails' ), $singular, "[view_{$singular_lc}_url]" ) . "\n";
		$content .= sprintf( __( 'You can view this %1$s in the backend, by clicking here: %2$s', 'wp-job-manager-emails' ), $singular, "[view_{$singular_lc}_url_admin]" ) . "\n" . "\n";

		return $content;
	}

	/**
	 * New listing (no approval required) default content
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	function pending_payment_to_publish_default_content() {

		$singular    = $this->cpt()->get_singular();
		$singular_lc = strtolower( $singular );

		$content = '';
		$content .= __( 'Hello', 'wp-job-manager-emails' ) . "\n" . "\n";
		$content .= sprintf( __( 'A %1$s pending payment, has just been completed by *%2$s*, and is now published.  The details are as follows:', 'wp-job-manager-emails' ), $singular, $this->submitted_by ) . "\n" . "\n";
		$content .= "[divider]" . "\n" . "[{$singular_lc}_fields]" . "\n" . "[/divider]" . "\n" . "\n";
		$content .= $this->post_content . "\n" . sprintf( __( 'The %s content is as follows:', 'wp-job-manager-emails' ), $singular ) . "\n" . str_replace( '[', '[/', $this->post_content ) . "\n" . "\n";
		$content .= sprintf( __( 'You can view this %1$s here: %2$s', 'wp-job-manager-emails' ), $singular, "[view_{$singular_lc}_url]" ) . "\n";
		$content .= sprintf( __( 'You can view this %1$s in the backend, by clicking here: %2$s', 'wp-job-manager-emails' ), $singular, "[view_{$singular_lc}_url_admin]" ) . "\n" . "\n";

		return $content;
	}

	/**
	 * Pending Payment Complete, Pending Approval Default Content
	 *
	 *
	 * @since @@since
	 *
	 * @return string
	 */
	function pending_payment_to_pending_default_content() {

		$singular    = $this->cpt()->get_singular();
		$singular_lc = strtolower( $singular );

		$content = '';
		$content .= __( 'Hello', 'wp-job-manager-emails' ) . "\n" . "\n";
		$content .= sprintf( __( 'A %1$s pending payment, has just been completed by *%2$s*, and is pending approval.  The details are as follows:', 'wp-job-manager-emails' ), $singular, $this->submitted_by ) . "\n" . "\n";
		$content .= "[divider]" . "\n" . "[{$singular_lc}_fields]" . "\n" . "[/divider]" . "\n" . "\n";
		$content .= $this->post_content . "\n" . sprintf( __( 'The %s content is as follows:', 'wp-job-manager-emails' ), $singular ) . "\n" . str_replace( '[', '[/', $this->post_content ) . "\n" . "\n";
		$content .= sprintf( __( 'You can view this %1$s here: %2$s', 'wp-job-manager-emails' ), $singular, "[view_{$singular_lc}_url]" ) . "\n";
		$content .= sprintf( __( 'You can view this %1$s in the backend, and approve it, by clicking here: %2$s', 'wp-job-manager-emails' ), $singular, "[view_{$singular_lc}_url_admin]" ) . "\n" . "\n";

		return $content;
	}

	/**
	 * New listing approved default content
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	function pending_to_publish_default_content() {

		$singular    = $this->cpt()->get_singular();
		$singular_lc = strtolower( $singular );

		$content = '';
		$content .= __( 'Hello', 'wp-job-manager-emails' ) . "\n" . "\n";
		$content .= sprintf( __( 'Your %1$s, %2$s, has been approved!', 'wp-job-manager-emails' ), $singular, $this->post_title ) . "\n" . "\n";
		$content .= sprintf( __( 'You can view the %1$s here: %2$s', 'wp-job-manager-emails' ), $singular, "[view_{$singular_lc}_url]" ) . "\n";

		return $content;
	}

	/**
	 * Expired listing default content
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	function publish_to_expired_default_content() {

		$singular    = $this->cpt()->get_singular();
		$singular_lc = strtolower( $singular );

		$content = '';
		$content .= __( 'Hello', 'wp-job-manager-emails' ) . "\n" . "\n";
		$content .= sprintf( __( 'Your %1$s, *%2$s*, has just expired.', 'wp-job-manager-emails' ), $singular, $this->post_title ) . "\n" . "\n";
		$content .= sprintf( __( 'Login to your %1$s Dashboard via the URL below to manage this listing:', 'wp-job-manager-emails' ), $singular ) . "\n" . "\n";
		$content .= "[{$singular_lc}_dashboard_url]" . "\n" . "\n";

		return $content;
	}

	/**
	 * Soon to expire default content
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	function soon_to_expire_default_content() {

		$singular    = $this->cpt()->get_singular();
		$singular_lc = strtolower( $singular );

		$content = '';
		$content .= __( 'Hello', 'wp-job-manager-emails' ) . "\n" . "\n";
		$content .= sprintf( __( 'Your %1$s, *%2$s*, is about to expire in [days_before_expire] days!', 'wp-job-manager-emails' ), $singular, $this->post_title ) . "\n" . "\n";
		$content .= sprintf( __( 'Login to your %1$s Dashboard via the URL below to manage this listing:', 'wp-job-manager-emails' ), $singular ) . "\n" . "\n";
		$content .= "[{$singular_lc}_dashboard_url]" . "\n" . "\n";
		$content .= sprintf( __( 'You can view this %1$s here: %2$s', 'wp-job-manager-emails' ), $singular, "[view_{$singular_lc}_url]" ) . "\n";

		return $content;
	}

	/**
	 * Default Email Template Content for Listing Featured
	 *
	 *
	 * @since @@since
	 *
	 * @param $title string     Listing title shortcode
	 * @param $view string      View listing URL shortcode
	 *
	 * @return string
	 */
	public function featured_default_content( $title, $view ) {

		$singular = $this->cpt()->get_singular();

		$content = '';
		$content .= __( 'Congratulations!', 'wp-job-manager-emails' ) . "\n" . "\n";
		$content .= sprintf( __( 'Your %1$s, "%2$s", is now a featured %1$s!', 'wp-job-manager-emails' ), $singular, $title ) . "\n" . "\n";
		$content .= sprintf( __( 'You can view your %1$s here: %2$s', 'wp-job-manager-emails' ), $singular, $view ) . "\n";

		return $content;
	}

	/**
	 * Default Email Template Content for Listing Un-Featured
	 *
	 *
	 * @since @@since
	 *
	 * @param $title string Listing title shortcode
	 *
	 * @return string
	 */
	public function unfeatured_default_content( $title ) {

		$singular = $this->cpt()->get_singular();

		$content = '';
		$content .= __( 'Hello', 'wp-job-manager-emails' ) . "\n" . "\n";
		$content .= sprintf( __( 'Unfortunately, your %1$s, "%2$s", is no longer a featured %1$s.', 'wp-job-manager-emails' ), $singular, $title ) . "\n" . "\n";
		$content .= __( 'Warm Regards', 'wp-job-manager-emails' ) . "\n" . "\n";

		return $content;
	}

	/**
	 * Get Default Post Status Slugs
	 *
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	public function get_default_statuses(){

		$default_statuses = apply_filters( 'job_manager_emails_post_status_default_post_statuses', array(
			'preview',
			'pending_payment',
			'pending',
			'hidden',
			'expired',
			'publish',
		) );

		return $default_statuses;
	}

	/**
	 * Get Custom Statuses
	 *
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	public function get_custom_statuses() {

		$custom_ps = array();

		if ( method_exists( $this, 'get_core_statuses' ) ) {

			// Only used for comparison (by keys) so really value could equal anything
			$default_ps = $this->get_core_statuses();
			// Now get core statuses passed through core filters (to diff and determine which are custom)
			$filtered_ps = $this->get_core_statuses( true );

			// Set equal to statuses by key not in the $default_ps array
			$custom_ps = array_diff_key( $filtered_ps, $default_ps );
		}

		return $custom_ps;
	}

	/**
	 * Get All Post Status Slugs (including custom ones)
	 *
	 *
	 * @since 2.1.0
	 *
	 * @param bool $implode
	 *
	 * @return string
	 */
	public function get_post_statuses( $implode = false ){

		$default_statuses = $this->get_default_statuses();
		$custom_statuses  = $this->get_custom_statuses();

		$post_statuses = ! empty( $custom_statuses ) ? array_merge( $default_statuses, array_keys( $custom_statuses ) ) : $default_statuses;

		if( $implode ){
			$post_statuses = implode( '|', $post_statuses );
		}

		return $post_statuses;
	}

	/**
	 * Listing Featured Hook
	 *
	 *
	 * @since 2.1.0
	 *
	 * @param $meta_id
	 * @param $object_id
	 * @param $meta_key
	 * @param $meta_value
	 */
	public function listing_featured( $meta_id, $object_id, $meta_key, $meta_value ) {

		$was_featured = FALSE;

		// Metakey must be _featured, $meta_value must be true or 1, and post type must match parent post type
		if ( $meta_key !== '_featured' || empty( $meta_value ) || get_post_type( $object_id ) !== $this->cpt()->get_ppost_type() ) {
			return;
		}

		// Try to use core function first (for future updates)
		if ( function_exists( 'is_position_featured' ) ) {

			$was_featured = is_position_featured( $object_id );

		} else {

			$post = get_post( $object_id );

			if ( $post instanceof WP_Post ) {
				$was_featured = $post->_featured ? TRUE : FALSE;
			}
		}

		// We only want to process if the status has changed from previous value
		if ( ! $was_featured ) {
			$slug = $this->cpt()->get_slug();
			$featured_emails = $this->cpt()->get_emails( "{$slug}_manager_{$slug}_featured" );

			if ( empty( $featured_emails ) ) {
				return;
			}

			// If post status is not published, means listing is not active (yet, could be preview, pending, or pending_payment)
			if ( get_post_status( $object_id ) !== 'publish' ) {

				$emails_to_send = array();

				foreach ( (array) $featured_emails as $featured_email ) {
					if( $featured_email->featured_send_on_create ){
						$emails_to_send[] = $featured_email->ID;
					}
				}

				if( ! empty( $emails_to_send ) ){
					// Since we only send on publish listings, queue featured emails to send
					update_post_meta( $object_id, 'queued_featured_emails', $emails_to_send );
				}

			} else {

				$this->cpt()->send_email( "{$slug}_manager_{$slug}_featured", $object_id );

			}

		}
	}

	/**
	 * Listing Unfeatured Hook
	 *
	 *
	 * @since 2.1.0
	 *
	 * @param $meta_id
	 * @param $object_id
	 * @param $meta_key
	 * @param $meta_value
	 */
	public function listing_unfeatured( $meta_id, $object_id, $meta_key, $meta_value ) {

		$was_featured = FALSE;

		// Metakey must be _featured, $meta_value but not be empty (false, or 0), and post type must match parent post type
		if ( $meta_key !== '_featured' || ! empty( $meta_value ) || get_post_type( $object_id ) !== $this->cpt()->get_ppost_type() ) {
			return;
		}

		// Try to use core function first (for future updates)
		if ( function_exists( 'is_position_featured' ) ) {

			$was_featured = is_position_featured( $object_id );

		} else {

			$post = get_post( $object_id );

			if ( $post instanceof WP_Post ) {
				$was_featured = $post->_featured ? TRUE : FALSE;
			}
		}

		// We only want to process if the status has changed from previous value
		if ( $was_featured ) {
			$slug = $this->cpt()->get_slug();
			$this->cpt()->send_email( "{$slug}_manager_{$slug}_unfeatured", $object_id );
		}
	}

	/**
	 * Add Meta when _featured meta added (not updated)
	 *
	 * This method adds a custom meta to a listing when the add_post_meta() is called for the _featured
	 * meta key.  This allows us to determine once a listing is set to published, if it's a new listing
	 * or not, as core only uses add_post_meta() on initial creation.
	 *
	 * @since 2.1.0
	 *
	 * @param $mid
	 * @param $object_id
	 * @param $meta_key
	 * @param $_meta_value
	 */
	public function add_new_listing_meta( $mid, $object_id, $meta_key, $_meta_value ) {

		if ( $meta_key !== '_featured' || get_post_type( $object_id ) !== $this->cpt()->get_ppost_type() ) {
			return;
		}

		update_post_meta( $object_id, 'jme_new_listing', TRUE );
	}

	/**
	 * Check for Featured Queued Emails to Send
	 *
	 * This method should be called on new listing set to published, which checks for new listing meta
	 * and queued emails meta, if both exist, it will send the email.
	 *
	 * @since 2.1.0
	 *
	 * @param $post \WP_Post
	 */
	public function queued_featured_emails( $post ) {

		// Check for queued featured emails to send (on publish)
		if ( $post->queued_featured_emails && $post->jme_new_listing ) {

			$emails = maybe_unserialize( $post->queued_featured_emails );

			foreach ( (array) $emails as $email_id ) {

				$template = get_post( $email_id );

				if ( $template instanceof WP_Post && $template->featured_send_on_create ) {
					$this->cpt()->send_email( $template, $post->ID );
				}

			}

			// Remove queued featured emails now that they've been sent
			delete_post_meta( $post->ID, 'queued_featured_emails' );
		}

		// Remove new listing meta on first publish
		if ( $post->jme_new_listing ) {
			delete_post_meta( $post->ID, 'jme_new_listing' );
		}
	}

	/**
	 * Magic Method to handle post_status hooks
	 *
	 *
	 * @since @@since
	 *
	 * @param $method_name
	 * @param $args
	 *
	 */
	public function __call( $method_name, $args ) {

		$post_statuses = $this->get_post_statuses( true );

		$check_results = preg_match( "/do_(?P<from>({$post_statuses})+)_to_(?P<to>({$post_statuses})+)/", $method_name, $matches );
		if ( $check_results && array_key_exists( 'from', $matches ) && array_key_exists( 'to', $matches ) ) {
			$from = $matches['from'];
			$to = $matches['to'];
			$this->post_status_hook( "{$from}_to_{$to}", $args[ 0 ] );
		}
	}
}