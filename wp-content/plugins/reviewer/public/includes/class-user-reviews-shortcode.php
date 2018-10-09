<?php
/**
 * Reviewer Plugin v.3.15.0
 * Created by Michele Ivani
 */

class RWP_User_Reviews_Shortcode
{
	// Instace of this class
	protected static $instance = null;

	protected $shortcode_tag = 'rwp_user_reviews';

    protected $plugin_slug = 'reviewer';

	private $default_preferences;

	private $preferences;

	function __construct()
	{
		add_shortcode( $this->shortcode_tag , array( $this, 'render' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'localize_script') );
	}

    public function render( $atts ) 
    {
        // Shortcode attributes.	
		extract( shortcode_atts( array(
			'user' 		=> '0',
			'order'		=> 'latest',
			'url'		=> '',
			'limit'		=> null,
			'stats'		=> 'true',
		), $atts ) );

		// Component ID, VueJS.
		$component_id = uniqid('rwp-component-');

		// User
		$user_obj = new RWP_User( $user );

		// Preferences.
		$this->preferences = get_option('rwp_preferences', array());

		// Verified badge.
		$verified_badge = $this->preferences_field('preferences_user_review_verified_badge', true);

		// Stats
		$show_stats = ($stats === 'false') ? false : true;

		ob_start();

		include('themes/layout-user-reviews-shortcode.php');

		return ob_get_clean();
	}

	public function localize_script() 
	{
		$action_name = 'rwp_reviews_of_single_user';
		wp_localize_script( $this->plugin_slug .'-front-end-script', 'reviewerReviewsOfSingleUser', array('ajax_nonce' => wp_create_nonce( $action_name ), 'ajax_url' => admin_url('admin-ajax.php'), 'action' => $action_name ) );
	}

	public function preferences_field( $field, $return = false ) {

		if ( null == $this->default_preferences ) {
			$this->default_preferences = RWP_Preferences_Page::get_preferences_fields();
		}

		$value = isset( $this->preferences[ $field ] ) ? $this->preferences[ $field ] : $this->default_preferences[ $field ]['default'];

		if( $return )
			return $value;

		echo $value; 
	}

    public static function get_instance() 
	{
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}
