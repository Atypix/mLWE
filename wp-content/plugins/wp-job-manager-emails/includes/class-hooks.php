<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Emails_Hooks
 *
 * @since 2.0.0
 *
 */
class WP_Job_Manager_Emails_Hooks extends WP_Job_Manager_Emails_Hooks_PostStatus {

	/**
	 * @type array
	 */
	protected $actions;
	/**
	 * @var WP_Post
	 */
	protected $email_template;
	/**
	 * @var string
	 */
	public $hook;
	/**
	 * @type WP_Job_Manager_Emails_CPT|WP_Job_Manager_Emails_CPT_Job|WP_Job_Manager_Emails_CPT_Resume|WP_Job_Manager_Emails_CPT_Application
	 */
	protected $cpt;
	/**
	 * @var array
	 */
	protected $defaults;
	/**
	 * @type string
	 */
	private $emails_header = 'X-Job-Manager-Emails: true';
	/**
	 * @var string
	 */
	private $short_circuit = 'X-Job-Manager-Short-Circuit: true';

	/**
	 * WP_Job_Manager_Emails_Hooks constructor.
	 *
	 * @param $cpt
	 */
	public function __construct( $cpt ) {
		$this->cpt = $cpt;

		//$this->poststatus_hooks();

		$this->hooks();
		$this->add_actions();

		// Hooks need to be setup on init to allow theme filtering overrides
		add_action( 'init', array( $this, 'poststatus_hooks' ) );

		add_filter( 'wp_mail', array( $this, 'wp_mail' ), 999999 );
		add_action( 'admin_init', array($this, 'generate_email') );
		add_action( 'all_admin_notices', array($this, 'email_generated') );
		add_action( 'admin_enqueue_scripts', array( $this, 'localize'), 9999 );
		add_filter( 'job_manager_emails_actions_shortcodes', array( $this, 'add_shortcodes' ) );
	}

	/**
	 * Check if Email Template is HTML
	 *
	 * This method checks if the email template has been specifically set to plain text,
	 * otherwise will return true to use HTML email type.
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return bool|mixed
	 */
	function is_html_email(){

		$plain_text = $this->email_template( 'plain_text' );
		$email_format = $this->email_template( 'email_format' );

		/**
		 * Backwards compatibility support for `plain_text` value in meta
		 */
		if ( empty( $email_format ) ) {
			$is_html_email = empty( $plain_text );
		} else {
			$is_html_email = strtolower( $email_format ) === 'html';
		}

		return $is_html_email;
	}

	/**
	 * Get Actions (Initialize if Empty)
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param bool $force
	 *
	 * @return mixed
	 */
	public function get_actions( $force = false ){

		if( empty($this->actions) || $force ) {
			$ps_actions = $this->init_ps_actions();
			$class_actions = $this->init_actions();

			$this->actions = array_merge( $class_actions, $ps_actions );
		}

		return $this->actions;
	}

	/**
	 * Add email template actions
	 *
	 * This method will add any output actions that are not already defined in this
	 * class construct method.
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	public function add_actions() {

		$this->get_actions( true );

		foreach( $this->actions as $hook => $config ) {

			// Goto next if hook/callback not set or set to false in config
			if( ! isset( $config['hook'], $config['callback'] ) || ! $config['hook'] || ! $config['callback'] ) continue;

			/**
			 * Allow hook to be defined in config array, and override hook value as the key from the config.
			 * This allows multiple hooks to be defined for the same action or filter, without being overwritten
			 * when arrays are merged together.
			 */
			if( ! empty( $config['hook'] ) && $config['hook'] !== TRUE ){
				$hook = $config['hook'];
			}

			// IF callback is array, should be another class object callback
			if( is_array( $config['callback'] ) && method_exists( $config['callback'][0], $config['callback'][1] ) ){

				$callback = $config['callback'];

			// If it isn't an array, should be callback to our class object
			} elseif( ! is_array( $config['callback'] ) && method_exists( $this, $config['callback'] ) ) {

				$callback = array( $this, $config['callback'] );

			// Or to a specific function call
			} elseif( ! is_array( $config['callback'] ) && function_exists( $config['callback'] ) ){

				$callback = $config['callback'];

			} else {
				// Otherwise skip to next hook/action
				continue;
			}

			// Check if action has already been defined (requires special priority execution)
			if( has_action( $hook, $callback ) ) continue;

			// Total args default of 1 unless set in config
			$num_args = isset( $config['args'] ) ? $config['args'] : 1;

			// Priority default of 10 unless set in config
			$priority = isset( $config['priority'] ) ? $config['priority'] : 10;

			// Add the action
			add_action( $hook, $callback, $priority, $num_args );
		}
	}

	/**
	 * Generate Default Email Method
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	function generate_email() {

		global $post_type, $pagenow;

		if( empty($post_type) && ! empty($_REQUEST['post_type']) ) $post_type = sanitize_text_field( $_REQUEST['post_type'] );

		if( ! empty($_GET['generate_email']) && $post_type == $this->cpt()->post_type && wp_verify_nonce( $_REQUEST['_wpnonce'], 'generate_email' ) ) {
			$generate_email = sanitize_text_field( $_GET['generate_email'] );

			$actions = $this->get_actions();
			// Make sure our email to generate does have config in actions array, include defaults config
			if( ! isset($actions[ $generate_email ], $actions[ $generate_email ]['defaults']) ) return FALSE;

			$defaults = $actions[$generate_email]['defaults'];
			if( ! isset( $defaults['hook'] ) ) $defaults['hook'] = $generate_email;

			$email_generated = array(
				'post_status' => 'disabled',
				'post_title'  => $defaults['post_title'],
				'post_content'  => $defaults['post_content'],
				'post_type'     => $post_type
			);

			$email_post_id = wp_insert_post( $email_generated, true );

			if( is_wp_error( $email_post_id ) ) {
				wp_redirect( remove_query_arg( 'generate_email', add_query_arg( 'email_generated_error', urlencode( $email_post_id->get_error_message() ) ) ) );
			} else {
				// Remove already set items from array
				unset( $defaults['post_content'] );

				foreach( $defaults as $default => $value ){
					if( is_array( $value ) ) $value = maybe_serialize( $value );
					update_post_meta( $email_post_id, $default, $value );
				}

				wp_redirect( remove_query_arg( 'generate_email', add_query_arg( 'email_generated', $email_post_id ) ) );
			}
			exit;
		}
	}

	/**
	 * Email Generated Admin Notice
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	function email_generated() {

		global $post_type, $pagenow;

		if( $pagenow == 'edit.php' && $post_type == $this->cpt()->post_type ) {
			if( ! empty($_REQUEST['email_generated']) ){
				$post_id = absint( $_REQUEST['email_generated'] );
				echo '<div class="updated"><p>' . sprintf( __( 'The requested default email template has been generated, and set to disabled. Click <a href="%s">here</a> to edit it.', 'wp-job-manager-emails' ), get_edit_post_link( $post_id ) ) . '</p></div>';
			} elseif( ! empty( $_REQUEST['email_generated_error'] ) ){
				$error_message = sanitize_text_field( $_REQUEST['email_generated_error'] );
				echo '<div class="error"><p>' . sprintf( __( 'There was an error creating the default email template:<br /> %s', 'wp-job-manager-emails' ), $error_message ) . '</p></div>';
			}
		}
	}

	/**
	 * Remove Class Filter Without Access to Class Object
	 *
	 * In order to use the core WordPress remove_filter() on a filter added with the callback
	 * to a class, you either have to have access to that class object, or it has to be a call
	 * to a static method.  This method allows you to remove filters with a callback to a class
	 * you don't have access to.
	 *
	 * Works with WordPress 1.2+
	 *
	 * Supported for WordPress 4.7+ added on September 19, 2016
	 *
	 *
	 * @param string $tag         Filter to remove
	 * @param string $class_name  Class name for the filter's callback
	 * @param string $method_name Method name for the filter's callback
	 * @param int    $priority    Priority of the filter (default 10)
	 *
	 * @return bool Whether the function is removed.
	 */
	function remove_class_filter( $tag, $class_name = '', $method_name = '', $priority = 10 ) {

		global $wp_filter;

		// Check that filter actually exists first
		if ( ! isset( $wp_filter[ $tag ] ) ) return FALSE;

		/**
		 * If filter config is an object, means we're using WordPress 4.7+ and the config is no longer
		 * a simple array, rather it is an object that implements the ArrayAccess interface.
		 *
		 * To be backwards compatible, we set $callbacks equal to the correct array as a reference (so $wp_filter is updated)
		 *
		 * @see https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/
		 */
		if ( is_object( $wp_filter[ $tag ] ) && isset( $wp_filter[ $tag ]->callbacks ) ) {
			$callbacks = &$wp_filter[ $tag ]->callbacks;
		} else {
			$callbacks = &$wp_filter[ $tag ];
		}

		// Exit if there aren't any callbacks for specified priority
		if ( ! isset( $callbacks[ $priority ] ) || empty( $callbacks[ $priority ] ) ) return FALSE;

		// Loop through each filter for the specified priority, looking for our class & method
		foreach( (array) $callbacks[ $priority ] as $filter_id => $filter ) {

			// Filter should always be an array - array( $this, 'method' ), if not goto next
			if ( ! isset( $filter[ 'function' ] ) || ! is_array( $filter[ 'function' ] ) ) continue;

			// If first value in array is not an object, it can't be a class
			if ( ! is_object( $filter[ 'function' ][ 0 ] ) ) continue;

			// Method doesn't match the one we're looking for, goto next
			if ( $filter[ 'function' ][ 1 ] !== $method_name ) continue;

			// Method matched, now let's check the Class
			if ( get_class( $filter[ 'function' ][ 0 ] ) === $class_name ) {

				// Now let's remove it from the array
				unset( $callbacks[ $priority ][ $filter_id ] );

				// and if it was the only filter in that priority, unset that priority
				if ( empty( $callbacks[ $priority ] ) ) unset( $callbacks[ $priority ] );

				// and if the only filter for that tag, set the tag to an empty array
				if ( empty( $callbacks ) ) $callbacks = array();

				// If using WordPress older than 4.7
				if ( ! is_object( $wp_filter[ $tag ] ) ) {
					// Remove this filter from merged_filters, which specifies if filters have been sorted
					unset( $GLOBALS[ 'merged_filters' ][ $tag ] );
				}

				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Add Custom Headers to String or Array Headers Format
	 *
	 * This method will add custom headers to the existing headers,
	 * using the format the existing headers are in (string or array).
	 *
	 *
	 * @since    1.0.0
	 *
	 * @param string|array $existing_headers Existing headers to add to, in either string or array format
	 * @param array        $new_headers      New headers to add in array format
	 * @param bool         $add_emails       Whether or not to add the 'X-Job-Manager-Emails: true' header
	 * @param bool         $short_circuit    Whether or not to add the Short Circuit header (prevents wp_mail from sending)
	 *
	 * @return array|string Existing headers plus new headers in same format as $existing_headers
	 */
	function add_headers( $existing_headers = '', $new_headers = array(), $add_emails = TRUE, $short_circuit = FALSE ) {

		if( $add_emails ) $new_headers[] = $this->emails_header;
		if( $short_circuit ) $new_headers[] = $this->short_circuit;

		// If headers already exist in array format
		if( is_array( $existing_headers ) ) {

			foreach( $new_headers as $new_header ) {
				$existing_headers[] = $new_header;
			}

		} else {

			// Headers are not in array format, let's use string format instead
			// and just append to the end of the string with a linebreak
			foreach( $new_headers as $new_header ) {
				$existing_headers .= $new_header . "\n";
			}

		}

		return $existing_headers;
	}

	/**
	 * Maybe Short Circuit WP_Mail
	 *
	 * There may be instances where we need to short circuit wp_mail and prevent mail
	 * from being sent.  This method checks each wp_mail call, looking for our short
	 * circuit headers, and if they exist, short circuits wp_mail to prevent it being sent.
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param array $args A compacted array of wp_mail() arguments, including the "to" email,
	 *                    subject, message, headers, and attachments values.
	 *
	 * @return array
	 */
	function wp_mail( $args ) {

		// Our customized emails will always have custom headers
		if ( ! array_key_exists( 'headers', $args ) || empty( $args[ 'headers' ] ) ) return $args;

		$headers = $args[ 'headers' ];

		if ( ! is_array( $headers ) ) {
			// Explode the headers out, so we can check if our custom header is in the array
			$headers = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
		}

		// Let's check for our short circuit header
		if ( in_array( $this->short_circuit, $headers ) ) {
			// Setting message and to value to empty string essentially short circuits wp_mail
			// @see https://core.trac.wordpress.org/ticket/35069
			$short_circuit              = $args;
			$short_circuit[ 'message' ] = '';
			$short_circuit[ 'to' ]      = '';
			return $short_circuit;
		}

		return $args;
	}

	/**
	 * Check for Shortcode in String
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $string
	 *
	 * @return bool
	 */
	function has_shortcode( $string ){
		if( strpos( $string, "[" ) !== FALSE && strpos( $string, "]" ) !== FALSE ) return true;

		return false;
	}

	/**
	 * Return Variable from Email Template
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $variable
	 * @param $hook
	 *
	 * @return mixed
	 */
	function email_template( $variable, $hook = '' ) {

		//TODO: allow multiple templates
		// Try to get the email template if it's not already set
		if( empty($this->email_template) ) {
			$hook = empty( $hook ) ? $this->hook : $hook;
			if( empty( $hook ) ) return false;
			$this->email_template = $this->cpt()->get_emails( $hook, TRUE );
		}

		// If unable to get email template, or template is not an object return false
		if( empty( $this->email_template ) || ! is_object( $this->email_template ) ) return false;

		return $this->email_template->$variable;
	}

	/**
	 * Return Hook/Action Defaults
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param bool $template
	 *
	 * @return array|bool
	 */
	function get_defaults( $template = false ){
		$template = ! $template ? $this->email_template : $template;

		if( ! isset( $this->hook ) && isset( $template ) ) $this->hook = $template->hook;

		$actions = $this->get_actions();
		if( isset( $actions, $actions[ $this->hook ], $actions[ $this->hook ]['defaults'] ) ){
			return $actions[ $this->hook ][ 'defaults' ];
		}

		return false;
	}

	/**
	 * Return hooks with default content
	 *
	 * This method returns hooks that have default content defined in their configuration (defaults key),
	 * which is used on the edit screen to populate fields with those values.
	 *
	 * @see return at end of method for filter details to add your own templates
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	function get_hooks_with_templates(){

		$ps_actions = $this->init_ps_actions();
		$core_actions = $this->init_actions();

		/**
		 * Setup default templates array.  Any custom templates added should follow this design
		 * pattern to work correctly.  The key should include no spaces (think slug)
		 */
		$templates = array(
			'core' => array(
				'label'     => __( 'Core Plugin Templates', 'wp-job-manager-emails' ),
				'tab'       => __( 'Core', 'wp-job-manager-emails' ),
				'desc'      => __( 'These templates use actions/hooks called by WP Job Manager (and addons), which most override default emails sent by plugins.', 'wp-job-manager-emails' ),
				'templates' => array()
			),
			'default' => array(
				'label' => __( 'Post Status Templates', 'wp-job-manager-emails' ),
				'tab'   => __( 'Default', 'wp-job-manager-emails' ),
				'desc'  => __( 'These templates use actions/hooks called when a listing\'s Post Status is updated, instead of relying on a plugin to call the action/hook. These are the most reliable and recommended when sending multiple emails.', 'wp-job-manager-emails' ),
				'templates' => array()
			),
		);

		// Add templates through extending class method
		if ( $core_templates = $this->core_templates() ) {
			$core_actions = array_merge( $core_actions, $core_templates );
		}

		/**
		 * This loop is specifically for core templates
		 */
		foreach( (array) $core_actions as $hook => $hcfg ){

			/*
			 * Add defaults to templates array if values exist
			 */
			if ( array_key_exists( 'defaults', $hcfg ) && ! empty( $hcfg[ 'defaults' ] ) ){

				// Allow values to be overriden if placed inside 'template' key (for showing different label/desc on template)
				$template_overrides = array_key_exists( 'template', $hcfg ) ? $hcfg['template'] : array();

				// Use values from `templates` for additional templates on the same hook/config
				$additional_templates = array_key_exists( 'templates', $hcfg ) ? $hcfg['templates'] : array();

				/**
				 * Merge configuration array, with values from defaults, so we have the label, description, etc, as well
				 * as 'to', 'subject', 'post_title', 'post_content', and 'attachments' in same level of array.
				 */
				$template_to_add = array_merge( $hcfg, $hcfg['defaults'], $template_overrides );
				// Add $hook as value under 'action' key to know what action to associate it with
				$template_to_add['action'] = $hook;

				/**
				 * Add default template to templates array.  This filter is ONLY for customizing the default template values (not recommended).
				 * If you want to add your own custom templates you should do so in another array, with the main key matching your theme, or
				 * plugin.
				 */
				$templates[ 'core' ][ 'templates' ][] = apply_filters( "job_manager_emails_core_{$hook}_hook_templates", $template_to_add, $hcfg, $this );

				// Loop through additional templates if set and add after initial one
				if ( ! empty( $additional_templates ) ) {
					foreach ( (array) $additional_templates as $additional_template ) {
						/**
						 * Add additonal templates to templates array.  This filter is ONLY for customizing the additional core template values (not recommended).
						 * If you want to add your own custom templates you should do so in another array, with the main key matching your theme, or plugin.
						 */
						$templates['core']['templates'][] = apply_filters( "job_manager_emails_core_{$hook}_additional_hook_templates", array_merge( $template_to_add, (array) $additional_template ), $hcfg, $additional_template, $this );
					}
				}

			}
		}

		$templates['core']['templates'] = apply_filters( 'job_manager_emails_core_hook_templates', $templates[ 'core' ][ 'templates' ], $core_actions, $this );

		// Add templates through extending class method
		if ( $default_templates = $this->default_templates() ) {
			$ps_actions = array_merge( $ps_actions, $default_templates );
		}

		/**
		 * This loop is specifically for Post Status templates
		 */
		foreach( (array) $ps_actions as $hook => $hcfg ) {

			/*
			 * Add defaults to templates array if values exist
			 */
			if ( array_key_exists( 'defaults', $hcfg ) && ! empty( $hcfg[ 'defaults' ] ) ) {

				// Use values from `template` key to overwrite default values
				$template_overrides = array_key_exists( 'template', $hcfg ) ? $hcfg[ 'template' ] : array();

				// Use values from `templates` for additional templates on the same hook/config
				$additional_templates = array_key_exists( 'templates', $hcfg ) ? $hcfg[ 'templates' ] : array();

				/**
				 * Merge configuration array, with values from defaults, so we have the label, description, etc, as well
				 * as 'to', 'subject', 'post_title', 'post_content', and 'attachments' in same level of array.
				 */
				$template_to_add = array_merge( $hcfg, $hcfg[ 'defaults' ], $template_overrides );

				// Add $hook as value under 'action' key to know what action to associate it with
				$template_to_add[ 'action' ] = $hook;

				// TODO Maybe remove `templates` array key

				/**
				 * Add default template to templates array.  This filter is ONLY for customizing the default template values (not recommended).
				 * If you want to add your own custom templates you should do so in another array, with the main key matching your theme, or
				 * plugin.
				 */
				$templates['default']['templates'][] = apply_filters( "job_manager_emails_default_{$hook}_hook_templates", $template_to_add, $hcfg, $this );

				// Loop through additional templates if set and add after initial one
				if( ! empty( $additional_templates ) ){
					foreach( (array) $additional_templates as $additional_template ) {
						/**
						 * Add additonal templates to templates array.  This filter is ONLY for customizing the additional default template values (not recommended).
						 * If you want to add your own custom templates you should do so in another array, with the main key matching your theme, or plugin.
						 */
						$templates['default']['templates'][] = apply_filters( "job_manager_emails_default_{$hook}_additional_hook_templates", array_merge( $template_to_add, (array) $additional_template ), $hcfg, $additional_template, $this );
					}
				}

			}
		}

		// Filter the default (post status) templates
		$templates[ 'default' ][ 'templates' ] = apply_filters( 'job_manager_emails_default_hook_templates', $templates[ 'default' ][ 'templates' ], $ps_actions, $this );

		/**
		 * Filter to add Custom Theme, or Plugin templates
		 *
		 * If you want to add custom templates, this is the hook you should use.
		 * Custom templates should follow the pattern below.  Templates should be placed inside the 'templates' array key
		 * which is an array of non-key arrays.
		 *
		 * For the email template post content, you should NOT use HTML as this will be stripped if the user sets the email type
		 * to plain text.  Instead, you should use new line breaks (\n), which will be converted to HTML <br> automatically.
		 *
		 * Supported shortcodes (NOT WordPress shortcodes, this plugins shortcodes) can be used in almost every field (to, subject, post_content),
		 * except post_title.  If you want to add custom shortcodes, @see WP_Job_Manager_Emails_Shortcodes
		 *
		 * The `action` value in the template array of arrays should be the action to associate the template with.
		 * This is the key of the action/hook, defined in this class, or extending classes @see init_ps_actions() @see init_actions()
		 *
		 *  ---- EXAMPLE CODE: -----
		 * @see https://plugins.smyl.es/docs-kb/adding-custom-plugin-or-theme-email-templates/
		 * @see
		 *
		 *
		 * @since @@since
		 *
		 * @param array $templates    Array of templates based on active/available hooks
		 * @param array $ps_actions   Array of Post Status actions/hooks
		 * @param array $core_actions Array of Core actions/hooks
		 * @param class $this         Access to $this current object
		 */
		return apply_filters( 'job_manager_emails_hook_templates', $templates, $ps_actions, $core_actions, $this );
	}

	/**
	 * Get all hook config with additions
	 *
	 * Method returns all hooks, WITH attachment fields, existing email post IDs, etc.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function get_full_hooks() {

		$hooks = $this->get_actions( true );

		if( empty($hooks) ) return array();

		$attachment_fields = array_keys( wp_list_filter( $this->cpt()->get_fields(), array('type' => 'file') ) );

		$job_attachment_fields = array_keys( wp_list_filter( $this->job()->get_fields(), array('type' => 'file') ) );
		$application_attachment_fields = $this->cpt()->integration()->application() ? array_keys( wp_list_filter( $this->cpt()->integration()->application()->get_fields(), array('type' => 'file') ) ) : false;
		$resume_attachment_fields = $this->cpt()->integration()->resume() ? array_keys( wp_list_filter( $this->cpt()->integration()->resume()->get_fields(), array('type' => 'file') ) ) : false;

		foreach( (array) $hooks as $hook => $config ) {

			// Merge with Job attachment fields if set in hook config
			if( array_key_exists( 'job_fields', $config ) && ! empty( $config['job_fields'] ) ){
				$attachment_fields = array_merge( $attachment_fields, $job_attachment_fields );
			}

			// Add Resume attachment fields (if Resumes enabled, and supported in config)
			if( ! empty( $resume_attachment_fields ) && array_key_exists( 'resume_fields', $config ) && ! empty( $config['resume_fields'] ) ){
				$attachment_fields = array_merge( $attachment_fields, $resume_attachment_fields );
			}

			// Add Application attachment fields (if Applications enabled, and supported in config)
			if( ! empty( $application_attachment_fields ) && array_key_exists( 'application_fields', $config ) && ! empty( $config['application_fields'] ) ){
				$attachment_fields = array_merge( $attachment_fields, $application_attachment_fields );
			}

			$hooks[ $hook ][ 'attachment_fields' ] = $attachment_fields;
			// Add parent slug to config array
			$hooks[ $hook ][ 'parent_slug' ] = $this->cpt()->get_slug();

			// Add saved post meta value to array for hook specific inputs
			if( array_key_exists( 'inputs', $config ) ){
				foreach( (array) $config['inputs'] as $input_mk => $input_conf ){
					$hooks[$hook]['inputs'][$input_mk]['saved_value'] = get_post_meta( get_the_ID(), $input_mk, true );
				}
			}

			$emails = $this->cpt()->get_emails( $hook );
			if( empty($emails) ) continue;

			$hooks[ $hook ]['posts'] = wp_list_pluck( $emails, 'ID' );

		}

		return apply_filters( 'job_manager_emails_get_full_hooks', $hooks, $this );
	}

	/**
	 * Get custom input fields added in hook config
	 *
	 * Some hook configurations will include additional input fields, specific to
	 * that hook.  This method returns those iputs so $this->cpt can save the values.
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	function get_hook_inputs() {

		$hooks = $this->get_actions();
		$hook_inputs = array(
			'checkboxes' => array(),
			'standard' => array(),
			'csv'      => array()
		);

		foreach( (array) $hooks as $hook => $hcfg ){

			if( ! array_key_exists( 'inputs', $hcfg ) || empty( $hcfg['inputs'] ) ) continue;

			foreach( (array) $hcfg['inputs'] as $hinput => $hicfg ){

				switch ( $hinput ){
					case 'checkbox':
						$hitype = 'checkboxes';
						break;
					case 'csv':
						$hitype = 'csv';
						break;
					default:
						$hitype = 'standard';
				}

				$hook_inputs[ $hitype ][] = $hinput;

			}
		}

		return apply_filters( 'job_manager_emails_get_hook_inputs', $hook_inputs, $hooks );
	}

	/**
	 * Add Hook Shortcodes
	 *
	 * This method is called by shortcodes class to return all hook
	 * specific shortcodes.
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param bool $force
	 *
	 * @return array
	 */
	function hook_shortcodes( $force = false ){

		$hooks = $this->get_actions( $force );
		if ( empty( $hooks ) ) return array();

		$shortcodes = array();
		foreach( (array) $hooks as $hook => $hcfg ){

			if( ! array_key_exists( 'shortcodes', $hcfg ) || empty( $hcfg['shortcodes'] ) ) continue;

			$shortcodes = array_merge( $shortcodes, $hcfg['shortcodes'] );
		}

		return $shortcodes;
	}

	/**
	 * \WP_Job_Manager_Emails_CPT|WP_Job_Manager_Emails_CPT_Job|WP_Job_Manager_Emails_CPT_Resume|WP_Job_Manager_Emails_CPT_Application|WP_Job_Manager_Emails_Application
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_Job_Manager_Emails_CPT|WP_Job_Manager_Emails_CPT_Job|WP_Job_Manager_Emails_CPT_Resume|WP_Job_Manager_Emails_Job|WP_Job_Manager_Emails_Resume|WP_Job_Manager_Emails_CPT_Application|WP_Job_Manager_Emails_Application
	 */
	function cpt(){
		return $this->cpt;
	}

	/**
	 * WP_Job_Manager_Emails_Shortcodes
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_Job_Manager_Emails_Shortcodes
	 */
	function shortcodes(){
		return $this->cpt()->shortcodes();
	}

	/**
	 * WP_Job_Manager_Emails_Job
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_Job_Manager_Emails_Job
	 */
	function job(){
		return $this->cpt()->integration()->job();
	}

	/**
	 * WP_Job_Manager_Emails_Resume
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_Job_Manager_Emails_Resume
	 */
	function resume(){
		return $this->cpt()->integration()->resume();
	}

	/**
	 * Localize JS
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	function localize(){

		if( $this->cpt()->page() ){
			wp_localize_script( 'jme-admin', 'jmemails_actions', $this->get_full_hooks() );
			wp_localize_script( 'jme-admin', 'jmemails_templates', $this->get_hooks_with_templates() );
		}

	}

	/**
	 * Add Core Templates
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	function core_templates(){
		return array();
	}

	/**
	 * Add Default Templates
	 *
	 *
	 * @since 2.0.0
	 *
	 */
	function default_templates(){
		return array();
	}
}