<?php 
/**
 * The plugin admin notification.
 *
 * This is used to define and manage admin notification for Reviewer plugin
 *
 */

class RWP_Notification {

	// @string 	Notification ID 
	protected $id;

	// @string 	Text of notification
	protected $message;
	
	// @string 	Type of notification [ updated, error, update-nag ]
	protected $class;
	
	// @bool 	Define if the notifcation will be deleted after it is been shown
	protected $flash;
	
	// @bool 	Define if the notification is hidden 
	protected $hidden;

	// WordPress option name for storing the notifcations
	protected static $option_name = 'rwp_notifications';

	public function __construct( $id = null, $message = '', $class = 'updated', $flash = true, $hidden = false ) {

		$this->id 		= is_null( $id) ? uniqid() : $id ;
		$this->message 	= $message;
		$this->class 	= $class;
		$this->flash 	= $flash;
		$this->hidden 	= $hidden;
	
	}

	/**
	 * Getters
	 */
	public function get_id() { return $this->id; }
	public function get_message() { return $this->message; }
	public function get_flash() { return $this->flash; }
	public function get_class() { return $this->class; }
	public function get_hidden() { return $this->hidden; }

	/**
	 *	Display all registered notifications
	 */
	public static function display_notifications() {

		// Get notifications
		$notifications = get_option( self::$option_name, array() );

		// Flash notifcation to remove
		$flash_notifications = array();

		// Echo notifications
		foreach ( $notifications as $key => $notification ) {

			//if( $notification->id == 'license' ) continue;

			$hidden = $notification->get_hidden() ? 'rwp-hidden' : '';
			echo '<div class="rwp-admin-notice notice '. $notification->get_class() .' '. $hidden .'"><p>'. $notification->get_message() .'</p></div>';
			
			if( $notification->get_flash() ) {
				$flash_notifications[] = $key;
			}
		}

		// Remove flash notifcations
		foreach ( $flash_notifications as $notification ) {
			unset( $notifications[ $notification ] );
		}
		update_option( self::$option_name, $notifications );

	}

	/**
	 * Add new notification to display
	 */
	public static function push( $id = null, $message = '', $class = 'updated', $flash = true, $hidden = false ) {

		$notifications = get_option( self::$option_name, array() );
		$notification = new RWP_Notification( $id, $message, $class, $flash, $hidden );
		$id = $notification->get_id();
		$notifications[ $id ] = $notification;
		update_option( self::$option_name, $notifications );

	}

	/**
	 * Remove a notification
	 */
	public static function delete( $id = '' ) {

		$notifications = get_option( self::$option_name, array() );
		unset( $notifications[ $id ] );
		update_option( self::$option_name, $notifications );

	}

	/**
	 * Add License notice
	 */
	public static function pushLicenseNotice()
	{
		$url = admin_url('admin.php?page=reviewer-license-page');
		self::push( 'license', __('Thank you for purchasing the Reviewer Plugin. It is necessary to register your license for using the plugin.', 'reviewer' ) .' <a href="'. $url .'">'. __('Register now', 'reviewer') .'</a>', 'update-nag', false );
	}
}
