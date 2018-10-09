<?php

/**
 * Reviewer Plugin v.3
 * Created by Michele Ivani
 */
class RWP_License_Page extends RWP_Admin_Page
{
	protected static $instance = null;
	protected $user;

	public static $api_url_register = 'http://reviewerplugin.com/api/license/register';
	public static $api_url_remove = 'http://reviewerplugin.com/api/license/remove';
	public static $api_url_update = 'http://reviewerplugin.com/api/license/check';
	private static $token = 'ca94e46f220a54c0181f1d067ec474a6';

	public static $option_key = 'rwp_license';

	private $license;

	public function __construct()
	{
		parent::__construct();

		$this->menu_slug = 'reviewer-license-page';
		$this->parent_menu_slug = 'reviewer-main-page';
		$this->add_menu_page();
		$this->user = wp_get_current_user();

		// Localize
		add_action( 'admin_enqueue_scripts', array( $this, 'localize_script') );
	}

	public function add_menu_page()
	{
		add_submenu_page( $this->parent_menu_slug, __( 'License', $this->plugin_slug), __( 'License', $this->plugin_slug), $this->capability, $this->menu_slug, array( $this, 'display_plugin_admin_page' ) );
	}

	public function display_plugin_admin_page()
	{
		$license = get_option( get_option( self::$option_key ) );
		?>
		<div class="wrap">
			<h2><?php _e( 'License', $this->plugin_slug ); ?></h2>

			<?php if( ! $license ): ?>
			<div id="rwp-license-notice" class="updated notice">
				<p><strong></strong></p>
			</div>
			<p class="description"><?php printf(__( 'The Reviewer Plugin needs to be activated before using its functionality. This is been necessary for preventing illegal uses of the plugin. %sPlease check the documentation out for all details and possible problems about plugin license.%s', $this->plugin_slug ), '<strong>', '</strong>'); ?></p>

			<div id="rwp-license-form">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><?php _e( 'First Name', $this->plugin_slug ); ?>*</th>
							<td>
								<input type="text" class="regular-text" id="rwp-license-first-name" value="<?php echo !empty( $this->user->user_firstname ) ? $this->user->user_firstname : ''; ?>" />
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Last Name', $this->plugin_slug ); ?>*</th>
							<td>
								<input type="text" class="regular-text" id="rwp-license-last-name" value="<?php echo !empty( $this->user->user_lastname ) ? $this->user->user_lastname : ''; ?>" />
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Email', $this->plugin_slug ); ?>*</th>
							<td>
								<input type="text" class="regular-text" id="rwp-license-email" value="<?php echo !empty( $this->user->user_email ) ? $this->user->user_email : ''; ?>" />
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Envato Username', $this->plugin_slug ); ?>*</th>
							<td>
								<input type="text" class="regular-text" id="rwp-license-envato-username" />
								<p class="description"><?php _e( 'Your username for accessing to Envato Marktplace', $this->plugin_slug ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Item Purchase Code', $this->plugin_slug ); ?>*</th>
							<td>
								<input type="text" class="regular-text" id="rwp-license-purchase-code" placeholder="xxxxxxxx-xxx-xxxx-xxxx-xxxxxxxxxxxx" />
								<p class=" description"><?php _e( 'Insert the purchase code you download with the Reviewer plugin', $this->plugin_slug ); ?> <a href="<?php echo RWP_PLUGIN_URL .'admin/assets/images/where-i-can-find-purchase-code.png' ?>"><?php _e('Where can I find it?', $this->plugin_slug) ?></a></p>
							</td>
						</tr>
					</tbody>
				</table>
				<p class="submit">
					<input type="button" id="rwp-activate-license" class="button button-primary" value="<?php _e('Activate License', $this->plugin_slug)?>" />
					<img class="rwp-loader" style="vertical-align: middle; margin: 0;" src="<?php echo admin_url(); ?>images/spinner.gif" alt="loading" />
				</p>
				<p class="description"><?php _e( 'The collected data will not be redistributed.', $this->plugin_slug ); ?></p>
			</div><!--- /license form -->
			<?php else: ?>
				<div id="rwp-license-notice" class="updated notice">
					<p><strong></strong></p>
				</div>

				<div class="updated notice">
					<p><strong><?php _e('The current copy of Reviewer plugin is active :-)', 'reviewer')?></strong></strong></p>
				</div>

				<div class="rwp-license-card">
					<h4><?php _e('Reviewer Plugin License', 'reviewer')?></h4>
					<ul>
						<li><i><?php _e('License ID', 'reviewer')?></i> <?php echo $license['license'] ?></li>
						<li><i><?php _e('Customer ID', 'reviewer')?></i> <?php echo $license['customer'] ?></li>
						<li><i><?php _e('Purchase Code', 'reviewer')?></i>  <?php echo $license['code'] ?></li>
					</ul>
				</div>

				<p class="submit">
					<input type="button" id="rwp-remove-license" class="button" value="<?php _e('Remove License', $this->plugin_slug)?>" />
					<img class="rwp-loader" style="vertical-align: middle; margin: 0;" src="<?php echo admin_url(); ?>images/spinner.gif" alt="loading" />
				</p>
			<?php endif; ?>
		</div><!--/wrap-->
		<?php
	}

	public function localize_script()
	{
		$lic = get_option( get_option( self::$option_key ) );
		$license = array(
			'ajax_nonce' 		  => wp_create_nonce( 'rwp_register_license' ),
			'ajax_nonce_remove'   => wp_create_nonce( 'rwp_remove_license' ),
			'ajax_url' 			  => admin_url('admin-ajax.php'),
			'action' 			  => 'rwp_register_license',
			'action_remove'		  => 'rwp_remove_license',
			'redirect_to'		  => menu_page_url('reviewer-main-page', false),
			'license_page'		  => menu_page_url('reviewer-license-page', false),
			'licensed'			  => $lic ? true : false,
		);
		wp_localize_script( 'reviewer-admin-script', 'rwpLicense', $license );
	}

	public static function register_license()
	{
		check_ajax_referer( $_POST['action'], 'security' );

		global $wp_version;
		$theme = wp_get_theme();

		$fields = array( 'first_name', 'last_name', 'email', 'envato_username', 'purchase_code' );
		foreach ($fields as $field) {
			if( !isset( $_POST[ $field ] ) ) {
				wp_send_json_error( __( 'Bad request', 'reviewer' ) );
			}
			$_POST[ $field ] = trim( $_POST[ $field ] );
		}

		$errors = array();
		if( empty( $_POST[ 'first_name' ] ) ) {
			$errors[] = 'The first name field is required.';
		}

		if( empty( $_POST[ 'last_name' ] ) ) {
			$errors[] = 'The last name field is required.';
		}

		if( empty( $_POST[ 'email' ] ) ) {
			$errors[] = 'The last name field is required.';
		} elseif( !is_email( $_POST[ 'email' ] ) ) {
			$errors[] = 'The email must be a valid email address.';
		}

		if( empty( $_POST[ 'envato_username' ] ) ) {
			$errors[] = 'The envato username field is required.';
		}

		if( empty( $_POST[ 'purchase_code' ] ) ) {
			$errors[] = 'The purchase code field is required.';
		}

		if( !empty( $errors ) ) {
			wp_send_json_error( $errors );
		}

		if( substr( $_POST[ 'purchase_code' ], 0, 1 ) === '#' ) {
			die( json_encode( self::special_license( $_POST[ 'purchase_code' ] ) ) );
		}

		$body = array(
			'first_name' 		=> $_POST['first_name'],
			'last_name' 		=> $_POST['last_name'],
			'email' 			=> $_POST['email'],
			'envato_username' 	=> $_POST['envato_username'],
			'purchase_code' 	=> $_POST['purchase_code'],
			'plugin_version' 	  => substr(RWP_Reviewer::VERSION, 0, 10),
			'wordpress_version'   => substr($wp_version, 0, 10),
			'wordpress_theme' 	  => $theme->get('Name') ? $theme->get('Name') : 'Unknown',
			'wordpress_theme_url' => ($theme->get('ThemeURI') && (!filter_var($theme->get('ThemeURI'), FILTER_VALIDATE_URL) === false)) ? $theme->get('ThemeURI') : 'http://unknown.site',
			'php_version'		  => substr(PHP_VERSION, 0, 10),
			'site_url'			  => (site_url() && (!filter_var(site_url(), FILTER_VALIDATE_URL) === false)) ? site_url() : 'http://unknown.site',
			'user_agent'		  => $_SERVER['HTTP_USER_AGENT'],
		);

		$response = wp_remote_post( self::$api_url_register, array(
			'redirection' 	=> 5,
			'body' 			=> $body,
			'headers' 		=> array(
				'X-REVIEWER-TOKEN' => self::$token
			),
		));

		if ( is_wp_error( $response ) || $response['response']['code'] != 200 ){
			wp_send_json_error( 'Reviewer Plugin server error: ' . $response->get_error_message() );
		}

		$body = wp_remote_retrieve_body( $response );

		$response = json_decode( $body, true );

		if( isset( $response['success'] ) && $response['success']  &&  isset( $response['data']['license']['id'] ) && isset( $response['data']['license']['customer']['id'] ) && isset( $response['data']['license']['purchase_code'] ) ) {
			$license = md5($response['license']['id'] . $response['license']['customer']['id'] . $response['license']['purchase_code']);
			update_option( self::$option_key, $license );
			update_option( $license, array(
				'license' 	=> $response['data']['license']['id'],
				'code' 		=> $response['data']['license']['purchase_code'],
				'customer' 	=> $response['data']['license']['customer']['id'],
			));
			RWP_Notification::delete('license');
			self::schedule_license_checking();
		}

		die( $body );
	}

	public static function remove_license()
	{
		check_ajax_referer( $_POST['action'], 'security' );

		$key = get_option( self::$option_key );
		$license = get_option( $key );
		if(!$license) {
			wp_send_json_error( 'Error. No license found!' );
		}

		$body = array(
			'license' 		=> $license['license'],
			'customer'		=> $license['customer'],
		);

		$response = wp_remote_post( self::$api_url_remove, array(
			'redirection' 	=> 5,
			'body' 			=> $body,
			'headers' 		=> array(
				'X-REVIEWER-TOKEN' => self::$token
			),
		));

		if ( is_wp_error( $response ) || $response['response']['code'] != 200 ){
			wp_send_json_error( 'Reviewer Plugin server error: ' . $response->get_error_message() );
		}

		$body = wp_remote_retrieve_body( $response );

		$response = json_decode( $body, true );

		if( isset( $response['success'] ) && $response['success'] ) {
			delete_option( $key );
			delete_option( self::$option_key );
			RWP_Notification::pushLicenseNotice();
			self::unschedule_license_checking();
		}

		die( $body );
	}

	public static function check_remote_license()
	{
		global $wp_version;
		$theme = wp_get_theme();

		$key = get_option( self::$option_key );
		$license = get_option( $key );

		$body = array(
			'license' 		=> isset($license['license']) ? $license['license'] : '',
			'customer' 		=> isset($license['customer']) ? $license['customer'] : '',
			'code' 			=> isset($license['code']) ? $license['code'] : '',

			'plugin_version' 	  => substr(RWP_Reviewer::VERSION, 0, 10),
			'wordpress_version'   => substr($wp_version, 0, 10),
			'wordpress_theme' 	  => $theme->get('Name') ? $theme->get('Name') : 'Unknown',
			'wordpress_theme_url' => ($theme->get('ThemeURI') && (!filter_var($theme->get('ThemeURI'), FILTER_VALIDATE_URL) === false)) ? $theme->get('ThemeURI') : 'http://unknown.site',
			'php_version'		  => substr(PHP_VERSION, 0, 10),
			'site_url'			  => (site_url() && (!filter_var(site_url(), FILTER_VALIDATE_URL) === false)) ? site_url() : 'http://unknown.site',
			'user_agent'		  => $_SERVER['HTTP_USER_AGENT'],
		);

		$response = wp_remote_post( self::$api_url_update, array(
			'redirection' 	=> 5,
			'body' 			=> $body,
			'headers' 		=> array(
				'X-REVIEWER-TOKEN' => self::$token
			),
		));

		if ( is_wp_error( $response ) || $response['response']['code'] != 200 ){
			return;
		}

		$body = wp_remote_retrieve_body( $response );

		$response = json_decode( $body, true );

		if( isset( $response['success'] ) && $response['success']  &&  isset( $response['data']['invalid'] ) && $response['data']['invalid'] ) {
			delete_option( $key );
			delete_option( self::$option_key );
			RWP_Notification::pushLicenseNotice();
			self::unschedule_license_checking();
		}
	}

	public static function schedule_license_checking()
	{
		$license = get_option( get_option( self::$option_key ) );
		if ( $license && ! wp_next_scheduled ( 'rwp_check_license' ) ) {
			// wp_schedule_event(current_time('timestamp')+20, 'minute', 'rwp_check_license');
			wp_schedule_event(current_time('timestamp')+82800, 'daily', 'rwp_check_license');
	    }
	}

	private static function special_license( $lic = '' )
	{
		$json = base64_decode(substr($lic, 1), true);
		$data = json_decode($json);
		if(is_null($data) || $data === false) {
			return array('success' => false, 'data' => 'Invalid special purchase code');
		}

		$properties = array('license_id', 'purchase_code', 'customer', 'allow_ip', 'time');
		foreach ($properties as $property) {
			if( !property_exists($data, $property)) {
				return array('success' => false, 'data' => 'Unable to parse special purchase code');
			}
		}

		$license_id = intval($data->license_id);
		$code 		= $data->purchase_code;
		$customer 	= intval($data->customer);
		$time 		= intval($data->time);
		$ips		= $data->allow_ip;

		if( $time < time() ) {
			return array('success' => false, 'data' => 'Purchase code is expired');
		}

		$m = 'You can not activate the plugin on this host. IP address: ' .$_SERVER['SERVER_ADDR'];
		if(!in_array($_SERVER['SERVER_ADDR'], $ips) && !in_array('*', $ips)) {
			return array('success' => false, 'data' => $m );
		}

		$license = md5($license_id . $customer . $code);
		update_option( self::$option_key, $license );
		update_option( $license, array(
			'license' 	=> $license_id,
			'code' 		=> $code,
			'customer' 	=> $customer,
		));
		RWP_Notification::delete('license');
		self::schedule_license_checking();
		return array('success' => true, 'data' => array( 'message' => __('License activated manually. Thank you ;)', 'reviewer') ) );
	}

	public static function unschedule_license_checking()
	{
		wp_clear_scheduled_hook('rwp_check_license');
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
