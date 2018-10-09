<?php

/**
 * Reviewer Plugin v.2
 * Created by Michele Ivani
 */
class RWP_Reviewer_Admin
{
	// Instance of this class
	protected static $instance = null;
	// Plugin Hooknames
	protected $hooknames = array('toplevel_page_reviewer-main-page', 'admin_page_reviewer-template-manager-page', 'reviewer_page_reviewer-preferences-page');

	function __construct()
	{
		// Call $plugin_slug from public plugin class
		$this->plugin_slug = 'reviewer';

		// Activation Notice
		add_action( 'admin_notices', array( 'RWP_Notification', 'display_notifications' ) );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options pages and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add bubble for pending ratings
		add_action( 'admin_menu', array( $this, 'add_pending_ratings_bubble' ) );

		// Add reviews meta box
		$this->add_plugin_meta_boxes();

		// Add the TinyMCE button
		$this->add_tinyMCE_support();

		// Add User ID column in users admin page
		$this->add_user_id_column();

		// Add Attachment ID column in media library page
		$this->add_attachment_id_column();

		// Customizate links of reviewer plugin in WP Plugin page
		$this->customize_plugin_details();

		// Localize media box
		// add_action( 'admin_init', array($this, 'localize_media_box') ); // [WPMUO] uncomment for old version of wp media loader
	}

	public function add_plugin_admin_menu()
	{
		$includes = array( 'class-menu-page', 'class-main-page', 'class-reviews-page', 'class-users-ratings-page', 'class-support-page', 'class-license-page', 'class-io-page', 'class-api-page' );

		foreach ($includes as $file)
			include_once('includes/'. $file .'.php');

		RWP_Menu_Page::get_instance();

		if( !isset( $_POST['rwp_io'] ) ) {
			RWP_Template_Manager_Page::get_instance();
		}

		RWP_Main_Page::get_instance();
		RWP_Reviews_Page::get_instance();
		RWP_Users_Ratings_Page::get_instance();
		RWP_Preferences_Page::get_instance();
		RWP_IO_Page::get_instance();
		RWP_License_Page::get_instance();
		// if( ! RWP_EXTENDED_LICENSE )
		// 	RWP_Support_Page::get_instance();
		//RWP_API_Page::get_instance();
	}

	public function add_pending_ratings_bubble()
	{
	 	$pend_count = get_option( 'rwp_pending_ratings', 0 );

	 	if( $pend_count == 0 )
	 		return;

	  	global $submenu;

	  	if( !isset( $submenu['reviewer-main-page'] ) )
	  		return;

		foreach ( $submenu['reviewer-main-page'] as $key => $value ) {

	        if ( $submenu['reviewer-main-page'][$key][2] == 'reviewer-users-ratings-page' ) {

	        	$submenu['reviewer-main-page'][$key][0] .= " <span class='update-plugins count-$pend_count'><span class='plugin-count'>" . $pend_count . '</span></span>';
	       		return;
	    	}
		}
	}

	public function add_tinyMCE_support()
	{
		$includes = array( 'class-tinymce' );

		foreach ($includes as $file)
			include_once('includes/'. $file .'.php');

		RWP_TinyMCE::get_instance();
	}

	public function add_plugin_meta_boxes()
	{
		$includes = array( 'class-meta-box-tables' );

		foreach ($includes as $file)
			include_once('includes/'. $file .'.php');

		// Reviews Meta Box
		$reviews_meta_box = RWP_Reviews_Meta_Box::get_instance();
		$reviews_meta_box->init();

		// Tables Meta Box
		$tables_meta_box = RWP_Tables_Meta_Box::get_instance();
		$tables_meta_box->init();
	}

	public function enqueue_admin_styles() {

		$screen = get_current_screen();

		if( 'admin_page_reviewer-template-manager-page' == $screen->id || 'reviewer_page_reviewer-preferences-page' == $screen->id ) { // Add color picker css files
			wp_enqueue_style( 'wp-color-picker' );
		}

		if( 'reviewer_page_reviewer-preferences-page' == $screen->id ) { // Add Codemirror css
			wp_enqueue_style( $this->plugin_slug .'-codemirror-styles', plugins_url( 'assets/css/codemirror.css', __FILE__ ), array(), RWP_Reviewer::VERSION );
		}

		wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/reviewer-admin.css', __FILE__ ), array( 'dashicons' ), RWP_Reviewer::VERSION );

	}

	public function enqueue_admin_scripts() {

		$screen = get_current_screen();

		//if( ! in_array( $screen->id, $this->hooknames) )
		//	return;

		$preferences = RWP_Reviewer::get_option('rwp_preferences');

		if( ( isset( $preferences['preferences_post_types'] ) && is_array( $preferences['preferences_post_types'] ) && in_array( get_current_screen()->id , $preferences['preferences_post_types'] ) ) || 'admin_page_reviewer-template-manager-page' == get_current_screen() -> id  ) {

	        wp_enqueue_script('thickbox');
	        wp_enqueue_style('thickbox');

	        // New WP Media Uploader
 			wp_enqueue_media();
 			wp_enqueue_script( 'reviewer-admin-script-wp-uploader', plugins_url( 'assets/js/wp-media-uploader.min.js', __FILE__ ), array( 'jquery'), RWP_Reviewer::VERSION );

    	}

    	if( 'reviewer_page_reviewer-preferences-page' == $screen->id ) {
 			wp_enqueue_script( 'reviewer-codemirror-script', plugins_url( 'assets/js/codemirror.min.js', __FILE__ ), null, RWP_Reviewer::VERSION );
 		}

 		wp_enqueue_script( 'reviewer-nouislider-script', plugins_url( 'assets/js/jquery.nouislider.all.min.js', __FILE__ ), array( 'jquery', 'jquery-ui-slider', 'jquery-ui-tooltip', 'wp-color-picker' ), RWP_Reviewer::VERSION );
 		wp_enqueue_script( 'reviewer-admin-script', plugins_url( 'assets/js/reviewer.admin.min.js', __FILE__ ), array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-tooltip', 'wp-color-picker' ), RWP_Reviewer::VERSION );

 		// Send preferences option to js
		wp_localize_script( 'reviewer-admin-script', 'rwpPreferences',  RWP_Reviewer::get_option( 'rwp_preferences' ) );
	}

	public function localize_media_box()
	{
	    global $pagenow;

	    if ( 'media-upload.php' == $pagenow || 'async-upload.php' == $pagenow ) {
	        // Now we'll replace the 'Insert into Post Button' inside Thickbox
	        add_filter( 'gettext', array($this, 'replace_thickbox_text')  , 1, 3 );
	    }
	}

	public function replace_thickbox_text($translated_text, $text, $domain)
	{
		if ('Insert into Post' == $text) {
	        $referer = strpos( wp_get_referer(), 'rwp-replace-star-image' );
	        if ( $referer != '' ) {
	            return __('Use as rating image', $this->plugin_slug );
		    }

		    $referer = strpos( wp_get_referer(), 'rwp-add-review-image' );
	        if ( $referer != '' ) {
	            return __('Use as review image', $this->plugin_slug );
		    }
		}

		return $translated_text;
	}

	public function activation_notice() {

		$support = RWP_Reviewer::get_option('rwp_support');

		if( !isset( $support['support_copy_id'] ) ) {

			echo '<div class="update-nag">';
	    		echo '<p>'. __('Thank you for purchasing the Reviewer Plugin. Activate your copy in order to get support.', $this->plugin_slug ) .' <a href="'.admin_url('admin.php?page=reviewer-support-page').'">'. __('Activate Now', $this->plugin_slug) .'</a></p>';
			echo '</div>';
		}
	}

	public function add_user_id_column()
	{
		add_filter('manage_users_columns', array($this, 'manage_users_columns'));
		add_action('manage_users_custom_column', array($this, 'manage_users_custom_column'), 10, 3);
	}

	public function manage_users_columns($columns)
	{
	    $columns['rwp_user_id'] = __('User ID', 'reviewer');
	    return $columns;
	}

	public function manage_users_custom_column($value, $column_name, $user_id)
	{
	    $user = get_userdata( $user_id );
		if ( 'rwp_user_id' == $column_name )
			return $user_id;
	    return $value;
	}

	public function add_attachment_id_column()
	{
		add_filter('manage_media_columns', array($this, 'manage_media_columns'));
		add_action('manage_media_custom_column', array($this, 'manage_media_custom_column'), 10, 2);
	}

	public function manage_media_columns($columns)
	{
	    $columns['rwp_attachment_id'] = __('File ID', 'reviewer');
	    return $columns;
	}

	public function manage_media_custom_column( $column_name, $attachment_id )
	{
	    if( 'rwp_attachment_id' == $column_name ){
	    	echo $attachment_id;
	    }
	}

	public static function get_instance()
	{
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function customize_plugin_details() {
		// Display the link with the plugin action links.
		//add_filter( 'plugin_action_links', array( $this, 'plugin_links' ), 10, 3 );
		// Display the link with the plugin meta.
		add_filter( 'plugin_row_meta', array( $this, 'plugin_links' ), 10, 4 );
	}

	public function plugin_links( $links, $plugin_file, $plugin_data ) {
		if( isset( $plugin_data['Name'] ) && $plugin_data['Name'] == 'Reviewer' && $plugin_file == 'reviewer/reviewer.php') {
			$links[2] = sprintf('<a href="%s" target="_blank">%s</a>', 'http://bit.ly/reviewer-plugin', __( 'View Details' ));
			$links[] = sprintf('<a href="%s" target="_blank">%s</a>', 'http://bit.ly/reviewer-plugin-support', __( 'Get Help' ));
		}
		return $links;
	}
}
