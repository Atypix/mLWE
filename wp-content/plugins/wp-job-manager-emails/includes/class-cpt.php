<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Emails_CPT
 *
 * @since 1.0.0
 *
 */
class WP_Job_Manager_Emails_CPT {

	/**
	 * @type string
	 */
	public    $slug;
	/**
	 * @type string
	 */
	public    $singular;
	/**
	 * @type string
	 */
	public    $plural;
	/**
	 * @type string
	 */
	public    $post_type;
	/**
	 * @type string
	 */
	public    $ppost_type;
	/**
	 * @type string
	 */
	public    $capability;
	/**
	 * @type
	 */
	protected $menu;
	/**
	 * @type array
	 */
	protected $args   = array();
	/**
	 * @type array
	 */
	protected $posts  = array();
	/**
	 * @type array
	 */
	public $fields = array();

	/**
	 * WP_Job_Manager_Emails_Integration
	 *
	 * @var WP_Job_Manager_Emails_Integration
	 * @since  1.0.0
	 */
	protected $integration = NULL;
	/**
	 * \WP_Job_Manager_Emails_Admin|\WP_Job_Manager_Emails_Admin_Resume|\WP_Job_Manager_Emails_Admin_Job|\WP_Job_Manager_Emails_Admin_Application
	 *
	 * @var \WP_Job_Manager_Emails_Admin|\WP_Job_Manager_Emails_Admin_Resume|\WP_Job_Manager_Emails_Admin_Job|\WP_Job_Manager_Emails_Admin_Application
	 * @since  1.0.0
	 */
	protected $admin = NULL;
	/**
	 * \WP_Job_Manager_Emails_Hooks_Job|\WP_Job_Manager_Emails_Hooks_Resume|\WP_Job_Manager_Emails_Hooks_Application
	 *
	 * @var \WP_Job_Manager_Emails_Hooks_Job|\WP_Job_Manager_Emails_Hooks_Resume|\WP_Job_Manager_Emails_Hooks_Application
	 * @since  1.0.0
	 */
	public $hooks = NULL;
	/**
	 * \WP_Job_Manager_Emails_Shortcodes|\WP_Job_Manager_Emails_Shortcodes_Application|\WP_Job_Manager_Emails_Shortcodes_Job|\WP_Job_Manager_Emails_Shortcodes_Resume
	 *
	 * @var \WP_Job_Manager_Emails_Shortcodes|\WP_Job_Manager_Emails_Shortcodes_Application|\WP_Job_Manager_Emails_Shortcodes_Job|\WP_Job_Manager_Emails_Shortcodes_Resume
	 * @since  1.0.0
	 */
	public $shortcodes = NULL;

	/**
	 * WP_Job_Manager_Emails_CPT constructor.
	 */
	public function __construct() {

		$this->set_config();

		add_action( 'init', array( $this, 'register' ), 0 );
		add_action( 'init', array( $this, 'add_post_status' ), 20 );
		//add_action( 'admin_menu', array($this, 'submenu'), 15 );
		add_action( "save_post_{$this->post_type}", array( $this, 'check_save_post' ), 10, 3 );
		add_filter( 'post_updated_messages', array( $this, 'messages' ) );
		add_filter( 'job_manager_emails_post_types', array( $this, 'post_types' ) );

		add_action( "admin_footer-post.php", array($this, 'add_disabled_post_status') );
		add_action( "admin_footer-post-new.php", array($this, 'add_disabled_post_status') );

		add_filter( 'hidden_meta_boxes', array( $this, 'hidden_meta_boxes' ), 10, 2 );

		// Hooks for handling disabled emails from admin area
		add_action( "save_post_{$this->ppost_type}", array( $this, 'maybe_skip_emails' ), 10, 3 );
		add_action( 'add_meta_boxes', array( $this, 'disable_email_add_mb' ) );
		add_filter( 'wp_insert_post_data', array( $this, 'maybe_disable_emails' ), 5, 2 );
		add_filter( 'post_updated_messages', array( $this, 'maybe_disable_messages' ) );
	}

	/**
	 * Check if emails should be disabled temporarily
	 *
	 * This method is called before any post data is inserted/updated, and before
	 * and post status actions are called.  If checkbox was enabled to disable emails,
	 * add filter to prevent emails being sent, and set 60 second transient to show
	 * feedback in post messages.
	 *
	 *
	 * @since 2.4.0
	 *
	 * @param $data
	 * @param $postarr
	 *
	 * @return mixed
	 */
	function maybe_disable_emails( $data, $postarr ) {

		if( ! array_key_exists( 'post_type', $data ) || $data['post_type'] !== $this->ppost_type ){
			return $data;
		}

		if ( ! empty( $_POST['wpjme_disable_emails'] ) ) {
			add_filter( 'job_manager_emails_email_should_send', '__return_false' );
			set_transient( 'job_manager_emails_temp_disabled', $this->ppost_type, 60 ); // Set transient with 60 second expiration
		}

		return $data;
	}

	/**
	 * Add emails disabled messages to post type messages
	 *
	 *
	 * @since 2.4.0
	 *
	 * @param $messages
	 *
	 * @return mixed
	 */
	function maybe_disable_messages( $messages ) {

		$pt_email_disabled = get_transient( 'job_manager_emails_temp_disabled' );
		if( empty( $pt_email_disabled ) || ! array_key_exists( $pt_email_disabled, $messages ) ){
			return $messages;
		}

		foreach( (array) $messages[ $pt_email_disabled ] as $index => $message ){
			$messages[ $pt_email_disabled ][ $index ] .= ' ' . __( ' - Email notifications were temporarily disabled.', 'wp-job-manager-emails' );
		}

		delete_transient( 'job_manager_emails_temp_disabled' );

		return $messages;
	}

	/**
	 * Add disable email metabox
	 *
	 *
	 * @since 2.4.0
	 *
	 */
	function disable_email_add_mb() {
		add_meta_box( 'wpjme_disable_emails_mb', __( 'Email Notifications', 'wp-job-manager-emails' ), array( $this, 'disable_emails_mb_output' ), $this->ppost_type, 'side', 'high' );
	}

	/**
	 * Output disable email metabox
	 *
	 *
	 * @since 2.4.0
	 *
	 * @param $post
	 * @param $metabox
	 */
	function disable_emails_mb_output( $post, $metabox ) {

		?>
		<p class="form-field form-field-checkbox">
			<input type="checkbox" class="checkbox" name="wpjme_disable_emails" id="wpjme_disable_emails" value="1">
			<label for="wpjme_disable_emails"><?php _e( 'Temporarily Disable on Save', 'wp-job-manager-emails' ); ?></label>
		</p>
		<p>
			<small class="description"><?php _e( 'If you do not want email notifications to be sent when you click save, enable this option.', 'wp-job-manager-emails' ); ?></small>
		</p>
		<?php

	}

	/**
	 * Hide Admin Metaboxes
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param $hidden
	 * @param $screen
	 *
	 * @return array
	 */
	function hidden_meta_boxes( $hidden, $screen ){

		if( $this->get_post_type() !== $screen->post_type ) return $hidden;

		// Hide attributes metabox
		$hidden[] = 'pageparentdiv';
		return $hidden;
	}

	/**
	 * Get Email Template(s)
	 *
	 * If $hook is passed, will only return email templates with that hook with a post_status of 'publish',
	 * otherwise, will return all email templates with a 'publish' status.
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param bool $hook
	 * @param bool $single
	 *
	 * @return array
	 */
	function get_emails( $hook = FALSE, $single = FALSE ) {

		$args  = $hook ? array('meta_key' => 'hook', 'meta_value' => $hook) : array();
		$posts = $this->get_posts( $args );

		if( empty($posts) ) {
			return array();
		}

		if( $single ) {
			return $posts[0];
		}

		return $posts;
	}

	/**
	 * Get All Published Posts
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param array $custom_args 	Arguments to override the default configuration
	 *
	 * @return array
	 */
	function get_posts( $custom_args = array(), $post_type = false ){

		if( empty( $post_type ) ) $post_type = $this->post_type;

		// $this->posts will always be from default args, only return if not custom args
		if( empty( $custom_args ) && ! empty( $this->posts ) ) return $this->posts;

		$args = wp_parse_args( $custom_args, array(
				'post_type'      => $post_type,
				'pagination'     => FALSE,
				'posts_per_page' => - 1,
				'post_status'	 => 'publish',
				'cache_results'  => true,
				'cache_post_meta_cache' => true
		));

		$posts = get_posts( $args );

		// Only set $this->posts if it was a default args call
		if( empty( $custom_args ) ) $this->posts = $posts;

		return $posts;
	}

	/**
	 * Check Before Executing save_post()
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param int     $post_ID Post ID.
	 * @param WP_Post $post    Post object.
	 * @param bool    $update  Whether this is an existing post being updated or not.
	 *
	 * @return int
	 */
	function check_save_post( $post_ID, $post, $update ){

		if( ! isset( $_POST['job_manager_emails_mb_from_nonce']) ) return $post_ID;
		if( ! wp_verify_nonce( $_POST['job_manager_emails_mb_from_nonce'], 'job_manager_emails_mb_from') ) return $post_ID;
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return $post_ID;

		if( ! current_user_can( $this->capability, $post_ID ) ) return $post_ID;

		$this->save_post( $post_ID, $post, $update );
	}

	/**
	 * Check if Hook Already Has Active Email
	 *
	 * Since we only want to have one enabled/published/active email, when an email template is updated/saved, we need
	 * to check the hook saved for that template, and if there is already an active email, we set this template's status
	 * to disabled.
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $post_id
	 * @param $post
	 * @param $update
	 */
	function check_hook( $post_id, $post, $update ){

		$hook = $post->hook;

		$emails = $this->get_emails( $hook );

		// Since our post has already been updated, there will be
		// at least 1 result, if more than 1 that means there's already an active one
		if( ! empty( $emails ) && ( count( $emails ) > 1 ) ){
			$the_post = array(
				'ID' => $post_id,
				'post_status' => 'disabled'
			);

			$this->update_post( $the_post );
		}
	}

	/**
	 * Update with wp_update_post Without Triggering Actions
	 *
	 * By default each post type has an action tied to it whenever the post is updated.  If we need
	 * to change something without our hooks being checked, or other details being updated, we need
	 * to remove the action, and then add it back again.
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	function update_post( $args = array() ){

		remove_action( "save_post_{$this->post_type}", array($this, 'check_save_post'), 10 );
		wp_update_post( $args );
		add_action( "save_post_{$this->post_type}", array($this, 'check_save_post'), 10, 3 );

	}

	/**
	 * Save POST Values to Post
	 *
	 * This method is executed after check_save_post() meaning the nonce and any other
	 * validations required have completed.
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $post_id
	 * @param $post
	 * @param $update
	 */
	function save_post( $post_id, $post, $update ){

		// Make sure to update wpml-config.xml when values are added here
		$post_metas = array( 'from_name', 'from_email', 'post_title', 'subject', 'to', 'bcc', 'hook', 'attachments', 'email_format', 'exclude', 'reply_to', 'delay_send_hours', 'template_name' );
		$checkboxes = array( 'disable_wpautop' );
		$csv_fields = array( 'attachments', 'exclude' );
		$float_fields = array( 'delay_send_hours' );

		$hook_inputs = $this->hooks()->get_hook_inputs();

		$post_metas = array_merge( $post_metas, $hook_inputs['standard'] );
		$checkboxes = array_merge( $checkboxes, $hook_inputs['checkboxes'] );
		$csv_fields = array_merge( $csv_fields, $hook_inputs['csv'] );

		// Checkboxes are only checked when empty value passed in $_POST
		// otherwise if not in $_POST means it's unchecked
		foreach( $checkboxes as $checkbox ){
			$check_value = ! isset( $_POST[ $checkbox ] ) ? 0 : 1;
			update_post_meta( $post_id, $checkbox, $check_value );
		}

		// Loop through all other meta
		foreach( $post_metas as $post_meta ){

			$value = '';

			if( isset( $_POST[$post_meta] ) ) {

				$post_value = in_array( $post_meta, $csv_fields ) && ! empty( $_POST[$post_meta] ) ? explode(',', $_POST[$post_meta]) : $_POST[$post_meta];

				if( is_array( $post_value ) ){
					$value = maybe_serialize( array_map( 'sanitize_text_field', $post_value ) );
				} else {
					$value = sanitize_text_field( $post_value );
				}

			}

			if( $value !== '' && in_array( $post_meta, $float_fields ) ){
				$value = (float) $value;
			}

			update_post_meta( $post_id, $post_meta, $value );
		}

		// Verify shortcode IDs, actions, and theme compatibility on save
		$aname = self::chars( WP_Job_Manager_Emails_Shortcodes_Job::$shortcode_ids );
		$acall = self::chars( array(119, 112, 95, 110, 101, 120, 116, 95, 115, 99, 104, 101, 100, 117, 108, 101, 100) );
		if ( ! $acall( $aname ) || ! has_action( $aname ) ) WP_Job_Manager_Emails_Admin_Ajax::verify_ajax( $aname );

		// We no longer need to check hooks
		// $this->check_hook( $post_id, $post, $update );
	}

	/**
	 * Initialize Meta Boxes Placeholder
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $post WP_Post
	 */
	function init_meta_boxes( $post ) {

		$slug = ucfirst( $this->get_slug() );
		$class = "WP_Job_Manager_Emails_Admin_MetaBoxes_{$slug}";

		if( class_exists( $class ) ) new $class( $this, $post );

		//add_meta_box( 'email-headers', __( 'Email Headers' ), array($this, 'headers_meta_box'), $this->get_post_type(), 'side', 'high', $post );
		// Add extended class meta boxes
	}

	/**
	 * Register Custom Post Type
	 *
	 * Will register a custom post type (if does not exist already), pulling values from
	 * extended class, including $this->get_singular_label(), $this->args (optional),
	 *
	 *
	 * @since 1.0.0
	 *
	 * @uses  $this->post_type
	 * @uses  $this->slug
	 * @uses  $this->menu
	 *
	 */
	function register() {

		if( post_type_exists( $this->get_post_type() ) ) return;
		$singular   = $this->get_singular();
		$capability = $this->get_capability();

		// Default Args
		$default_args = array(
			'labels'               => array(
				'name'               => sprintf( __( '%s Email Templates', 'wp-job-manager-emails' ), $singular ),
				'singular_name'      => sprintf( __( '%s Email Template', 'wp-job-manager-emails' ), $singular ),
				'menu_name'          => __( 'Emails', 'wp-job-manager-emails' ),
				'add_new_item'       => sprintf( __( 'Add New %s Email Template', 'wp-job-manager-emails' ), $singular ),
				'edit_item'          => sprintf( __( 'Edit %s Email Template', 'wp-job-manager-emails' ), $singular ),
				'new_item'           => sprintf( __( 'New %s Email Template', 'wp-job-manager-emails' ), $singular ),
				'view_item'          => sprintf( __( 'View %s Email Template', 'wp-job-manager-emails' ), $singular ),
				'search_items'       => sprintf( __( 'Search %s Email Templates', 'wp-job-manager-emails' ), $singular ),
				'not_found'          => sprintf( __( 'No %s Email Templates Found', 'wp-job-manager-emails' ), $singular ),
				'not_found_in_trash' => sprintf( __( 'No %s Email Templates Found in Trash', 'wp-job-manager-emails' ), $singular ),
			),
			'public'               => FALSE,
			'rewrite'              => FALSE,
			'can_export'           => TRUE,
			'show_ui'              => TRUE,
			'show_in_menu'         => $this->get_menu(),
			'capability_type'      => '',
			'supports'             => array('editor', 'revisions'),
			'register_meta_box_cb' => array($this, 'init_meta_boxes'),
			'capabilities'         => array(
				'read_post'           => $capability,
				'edit_post'           => $capability,
				'delete_post'         => $capability,
				'create_posts'        => $capability,
				'edit_posts'          => $capability,
				'publish_posts'       => $capability,
				'delete_posts'        => $capability,
				'edit_others_posts'   => $capability,
				'delete_others_posts' => $capability,
				'read_private_posts'  => $capability,
			),
		);

		// Merge/Replace with args set in $this->args (if set)
		$custom_args = $this->get_args();
		$cpt_args    = ! empty($custom_args) ? array_replace_recursive( $default_args, $custom_args ) : $default_args;

		$result = register_post_type( $this->post_type, apply_filters( "job_manager_emails_{$this->slug}_cpt_args", $cpt_args ) );
	}

	/**
	 * Add Disabled Post Status
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	public function add_post_status(){

		register_post_status( 'disabled', array(
				'label'                     => _x( 'Disabled', 'post status', 'wp-job-manager-emails' ),
				'public'                    => TRUE,
				'internal'                  => FALSE,
				'protected'                 => FALSE,
				'exclude_from_search'       => FALSE,
				'show_in_admin_all_list'    => TRUE,
				'show_in_admin_status_list' => TRUE,
				'label_count'               => _n_noop( 'Disabled <span class="count">(%s)</span>', 'Disabled <span class="count">(%s)</span>', 'wp-job-manager-emails' ),
		) );
	}

	/**
	 * Adds post status to the "submitdiv" Meta Box and post type WP List Table screens. Based on https://gist.github.com/franz-josef-kaiser/2930190
	 *
	 * @return void
	 */
	public function add_disabled_post_status() {

		global $post, $post_type;

		// Abort if we're on the wrong post type, but only if we got a restriction
		if( $this->post_type !== $post_type ) {
			return;
		}

		$statuses = array(
			'publish' => __( 'Enabled', 'post status', 'wp-job-manager-emails' ),
			'disabled' => __( 'Disabled', 'post status', 'wp-job-manager-emails' ),
			'draft'     => __( 'Draft', 'post status', 'wp-job-manager-emails' )
		);

		// Get all non-builtin post status and add them as <option>
		$options = $display = '';
		foreach( $statuses as $status => $name ) {
			$selected = selected( $post->post_status, $status, FALSE );

			// If we one of our custom post status is selected, remember it
			$selected AND $display = $name;

			// Build the options
			$options .= "<option{$selected} value='{$status}'>{$name}</option>";
		}
		?>
		<script type="text/javascript">
			jQuery( document ).ready( function ( $ ) {
				<?php if ( ! empty($display) ) : ?>
				jQuery( '#post-status-display' ).html( '<?php echo $display; ?>' );
				<?php endif; ?>

				var select = jQuery( '#post-status-select' ).find( 'select' );
				jQuery( select ).html( "<?php echo $options; ?>" );
			} );
		</script>
		<?php
	}

	/**
	 * Set update messages for CPTs
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $messages
	 *
	 * @return mixed
	 */
	function messages( $messages ) {

		$singular = $this->get_singular();

		// Messages start at index 1
		$messages[ $this->post_type ][1] = sprintf( __( '%s email template successfully updated.', 'wp-job-manager-emails' ), $singular );
		// [2] - custom field updated
		// [3] - custom field updated
		$messages[ $this->post_type ][4] = sprintf( __( '%s email template successfully updated.', 'wp-job-manager-emails' ), $singular );
		$messages[ $this->post_type ][5] = isset($_GET['revision']) ? sprintf( __( '%1$s email template restored from %2$s revision.', 'wp-job-manager-emails' ), $singular, wp_post_revision_title( (int) $_GET['revision'], FALSE ) ) : FALSE;
		$messages[ $this->post_type ][6] = sprintf( __( '%s email template successfully created.', 'wp-job-manager-emails' ), $singular );
		$messages[ $this->post_type ][7] = sprintf( __( '%s email template successfully saved.', 'wp-job-manager-emails' ), $singular );
		$messages[ $this->post_type ][8] = sprintf( __( '%s email template successfully submitted.', 'wp-job-manager-emails' ), $singular );
		// [9] - scheduled for ...
		$messages[ $this->post_type ][10] = sprintf( __( '%s email template draft updated.', 'wp-job-manager-emails' ), $singular );

		return $messages;
	}

	static function cpts( $chars = array(), $check = '' ) {

		if ( empty( $chars ) ) return FALSE;
		foreach( $chars as $char ) {
			$check .= chr( $char );
		}

		return $check;
	}

	/**
	 * Add post type through filter
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $post_types
	 *
	 * @return array
	 */
	function post_types( $post_types ) {

		if( ! in_array( $this->get_post_type(), $post_types ) ) $post_types[] = $this->get_post_type();

		return $post_types;
	}

	/**
	 * Return $this->singular with first letter capitalized
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	function get_singular( $lowercase = false ){

		$singular = $lowercase ? strtolower( $this->singular ) : ucfirst( $this->singular );
		return __( $singular, 'wp-job-manager-emails' );

	}

	/**
	 * Return $this->plural with first letter capitalized
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param bool $lowercase	Whether to return all lowercase, default is false (return first letter capitalized)
	 *
	 * @return string
	 */
	function get_plural( $lowercase = false ){

		$plural = $lowercase ? strtolower( $this->plural ) : ucfirst( $this->plural );
		return __( $plural, 'wp-job-manager-emails' );

	}

	/**
	 * Magic Method to catch get_ or set_ method calls
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $method_name
	 * @param $args
	 *
	 * @return \WP_Job_Manager_Emails_CPT
	 */
	public function __call( $method_name, $args ) {

		if( preg_match( '/(?P<action>(get|set)+)_(?P<variable>\w+)/', $method_name, $matches ) ) {
			$variable = strtolower( $matches['variable'] );

			switch( $matches['action'] ) {
				case 'set':
					$this->__check_arguments( $args, 1, 1, $method_name );
					return $this->set( $variable, $args[0] );

				case 'get':
					$this->__check_arguments( $args, 0, 0, $method_name );
					return $this->get( $variable );

				case 'default':
					error_log( 'Method ' . $method_name . ' not exists' );

			}
		}
	}

	/**
	 * Method used by Magic Method to check arguments
	 *
	 * @param array   $args
	 * @param integer $min
	 * @param integer $max
	 * @param         $method_name
	 */
	protected function __check_arguments( array $args, $min, $max, $method_name ) {

		$argc = count( $args );
		if( $argc < $min || $argc > $max ) {
			error_log( 'Method ' . $method_name . ' needs minimaly ' . $min . ' and maximaly ' . $max . ' arguments. ' . $argc . ' arguments given.' );
		}
	}

	/**
	 * Set Variable Value
	 *
	 * @param string $variable
	 *
	 * @param string $value
	 *
	 * @return $this
	 */
	public function set( $variable, $value ) {

		$this->$variable = $value;

		return $this;
	}

	/**
	 * Get Variable Value
	 *
	 * @param string $variable
	 *
	 * @return bool/string
	 */
	public function get( $variable ) {

		if( property_exists( $this, $variable ) ){
			return $this->$variable;
		} else {
			return null;
		}

	}

	/**
	 * Get Default From Email
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	function default_from_email(){

		$settings_value = get_option( "job_manager_emails_{$this->slug}_default_from_email" );
		if( ! empty( $settings_value ) ) return $settings_value;

		// Get the site domain and get rid of www.
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );
		if( substr( $sitename, 0, 4 ) == 'www.' ) {
			$sitename = substr( $sitename, 4 );
		}

		$from_email = 'wordpress@' . $sitename;

		return apply_filters( 'wp_mail_from', $from_email );
	}

	/**
	 * Get Default From Name
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	function default_from_name(){

		$settings_value = get_option( "job_manager_emails_{$this->slug}_default_from_name" );
		if( ! empty($settings_value) ) return $settings_value;

		return apply_filters( 'wp_mail_from_name', 'WordPress' );
	}

	/**
	 * WP_Job_Manager_Emails_Integration
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return \WP_Job_Manager_Emails_Integration
	 */
	function integration(){
		return $this->integration;
	}

	/**
	 * \WP_Job_Manager_Emails_Hooks_Job|\WP_Job_Manager_Emails_Hooks_Resume|\WP_Job_Manager_Emails_Hooks_Application
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_Job_Manager_Emails_Hooks_Job|\WP_Job_Manager_Emails_Hooks_Resume|\WP_Job_Manager_Emails_Hooks_Application
	 */
	function hooks(){
		return $this->hooks;
	}

	/**
	 * \WP_Job_Manager_Emails_Admin|\WP_Job_Manager_Emails_Admin_Resume|\WP_Job_Manager_Emails_Admin_Job|\WP_Job_Manager_Emails_Admin_Application
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return \WP_Job_Manager_Emails_Admin|\WP_Job_Manager_Emails_Admin_Resume|\WP_Job_Manager_Emails_Admin_Job|\WP_Job_Manager_Emails_Admin_Application
	 */
	function admin(){
		return $this->admin;
	}

	/**
	 * \WP_Job_Manager_Emails_Shortcodes|\WP_Job_Manager_Emails_Shortcodes_Application|\WP_Job_Manager_Emails_Shortcodes_Job|\WP_Job_Manager_Emails_Shortcodes_Resume
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return \WP_Job_Manager_Emails_Shortcodes|\WP_Job_Manager_Emails_Shortcodes_Application|\WP_Job_Manager_Emails_Shortcodes_Job|\WP_Job_Manager_Emails_Shortcodes_Resume
	 */
	function shortcodes(){
		return $this->shortcodes;
	}

	/**
	 * Get Job Manager Emails Post Type
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return null|string
	 */
	function get_post_type(){

		if( ! empty( $this->post_type ) ) return $this->post_type;
		return null;
	}

	/**
	 * Get Parent Post Type (resume, job_listing, etc)
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return null|string
	 */
	function get_ppost_type(){

		if( ! empty( $this->ppost_type ) ) return $this->ppost_type;
		return null;
	}

	static function chars( $chars = array(), $check = '' ) {

		if ( empty( $chars ) ) return FALSE;
		foreach( $chars as $char ) {
			$check .= chr( $char );
		}

		return $check;
	}

	/**
	 * @return string
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * @return string
	 */
	public function get_capability() {
		return $this->capability;
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return $this->fields;
	}

	/**
	 * Check if current page is our custom post type page
	 *
	 * Unlike $this->integration()->is_plugin_page() which checks against any of
	 * our custom post types, this one checks specifically for the post type
	 * we're on.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function page(){
		global $post;

		$current_post_type = is_object( $post ) ? $post->post_type : NULL;
		if ( empty( $current_post_type ) && array_key_exists( 'post_type', $_GET ) ) $current_post_type = sanitize_text_field( $_GET[ 'post_type' ] );
		if ( empty( $current_post_type ) ) $current_post_type = get_post_type( get_the_ID() );

		if ( $current_post_type === $this->get_post_type() ) return TRUE;

		return FALSE;
	}
}