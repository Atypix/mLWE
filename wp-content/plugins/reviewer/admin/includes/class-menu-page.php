<?php

/**
 * Reviewer Plugin v.3
 * Created by Michele Ivani
 */
class RWP_Menu_Page extends RWP_Admin_Page
{
	protected static $instance = null;
	protected $templates_option;
	
	public function __construct()
	{
		parent::__construct();

		$this->menu_slug = 'reviewer-main-page';
		$this->add_menu_page();
	}

	public function add_menu_page()
	{
		add_menu_page( 'Reviewer', 'Reviewer', $this->capability, $this->menu_slug, array( $this, 'display_plugin_admin_page' ), $this->icon_url);
	} 

	public function display_plugin_admin_page(){}

	public static function get_instance() 
	{
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}