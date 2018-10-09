<?php

/**
 * Reviewer Plugin v.2
 * Created by Michele Ivani
 */

require_once RWP_PLUGIN_PATH . 'public/includes/class-reviews-list-shortcode.php';

class RWP_TinyMCE
{
	// Instance of this class
	protected static $instance = null;
	// Slug of the plugin screen
	protected $plugin_slug = null;
	// Buttons IDs
	public $button_id  = "rwp_reviewer";
	public $button_id2 = "rwp_reviewer_widget"; 

	public $reviews_meta_key = 'rwp_reviews';
	public $tables_meta_key = 'rwp_tables';

	function __construct()
	{
		$this->plugin_slug = 'reviewer';
		add_action( 'init', array( $this, 'add_tinymce_button' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'localize_script' ) );
	}

	// Method for registering the editor button
	function add_tinymce_button() 
	{
		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
			return;

		if ( get_user_option('rich_editing') == 'true' ) {
	   
		  add_filter( 'mce_external_plugins', array($this, 'add_button_script') );
		  add_filter( 'mce_buttons', array($this, 'register_button') );
	   }  
	}

	function localize_script( $hook )
	{
		if('post.php' == $hook || 'post-new.php' == $hook) {

			$reviews 	= get_post_meta( get_the_ID(), $this->reviews_meta_key, true );
			$tables 	= get_post_meta( get_the_ID(), $this->tables_meta_key, true );
			
			$content = array('reviews' => array(), 'tables' => array(), 'msg' => __( 'Create a review box or comparison table', $this->plugin_slug) );
						
			if( !empty( $reviews ) ) 
				foreach( $reviews as $r)
					$content['reviews'][] = $r;

			if( !empty( $tables ) ) 
				foreach( $tables as $t)
					$content['tables'][] = $t;
						
			wp_localize_script( $this->plugin_slug . '-admin-script', 'rwpTinyMCEContent',  $content );
			wp_localize_script( $this->plugin_slug . '-admin-script', 'rwpRlForm', RWP_Reviews_List_Shortcode::get_form() );
		
		}
	}

	// Register tinymce button | Reviews and tables 
	public function register_button( $buttons ) 
	{
	   array_push( $buttons, "|", $this->button_id );
	   array_push( $buttons, "|", $this->button_id2 );
	   return $buttons; 
	}

	
	// Register js file for button
	public function add_button_script( $plugin_array ) 
	{
	   $plugin_array[$this->button_id] = RWP_PLUGIN_URL . '/admin/assets/js/tinymce-support.js';
	   $plugin_array[$this->button_id2] = RWP_PLUGIN_URL . '/admin/assets/js/tinymce-support.js';
	   return $plugin_array;
	}

	public static function get_instance() 
	{
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		//var_dump(self::$instance);
		return self::$instance;
	}
}