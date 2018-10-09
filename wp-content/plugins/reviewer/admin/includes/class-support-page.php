<?php

/**
 * Reviewer Plugin v.2
 * Created by Michele Ivani
 */
class RWP_Support_Page extends RWP_Admin_Page
{
	//private static $base_url 	= 'http://localhost:8000/';
	private static $base_url 	= 'http://reviewerplugin.com/';
	private static $url 		= 'support/activate';
	private static $token 		= '349bfe51067695b7e80ba1885ffa8213a74dee8b';


	protected static $instance = null;
	public $support_fields;
	public $option_value;

	protected $user;

	public function __construct()
	{
		parent::__construct();

		$this->user = wp_get_current_user();

		$this->set_support_fields(); 
		$this->menu_slug = 'reviewer-support-page';
		$this->parent_menu_slug = 'reviewer-main-page';
		$this->option_name = 'rwp_support';
		$this->option_value = RWP_Reviewer::get_option( $this->option_name );
		$this->add_menu_page();
		$this->register_page_fields();
	}

	public function add_menu_page()
	{
		add_submenu_page( $this->parent_menu_slug, __( 'Support', $this->plugin_slug), __( 'Support', $this->plugin_slug), $this->capability, $this->menu_slug, array( $this, 'display_plugin_admin_page' ) );
	} 


	public function display_plugin_admin_page()
	{
		?>
		<div class="wrap">
			<h2><?php _e( 'Support', $this->plugin_slug ); ?></h2>

 			<div class="rwp-support-desc">
 				<p>You need to activate your plugin license in order to get support from Reviewer Plugin Team.</p>
 				<p>The <a href="http://codecanyon.net/item/reviewer-wordpress-plugin/5532349/support">support terms</a> do not include:</p>
 				<ul>
 					<li>Customization and installation services.</li>
					<li>Support for third party software and plug-ins.</li>
 				</ul>
 				<p>You can contact the Support Team via the following methods:</p>
				<ul>
					<li>Start a <strong>chat</strong> from the box you can find the bottom right corner of <a href="http://reviewerplugin.com/support">this page</a></li>
					<?php $parse = isset( $this->option_value['support_copy_id'] ) ? '['. $this->option_value['support_copy_id'] .']' : ''; ?>
 					<li>Send an <strong>email</strong> to <a href="mailto:support@reviewerplugin.com?subject=[Reviewer Plugin]<?php echo $parse ?> Support Request">support@reviewerplugin.com</a> including the copy ID inside subject.</li>
					<li>Send a <strong>message</strong> via the plugin <a href="http://codecanyon.net/item/reviewer-wordpress-plugin/5532349/support">support page</a> at Envato Market.</li>
 				</ul>
 				<p><strong>Please do not post comments on plugin page for support requests. The Support Team does not manage plugin comments.</strong></p>
 				<p>You can follow the plugin author <a href="https://twitter.com/Michele_Ivani">@Michele_Ivani</a> on Twitter for news and updates about Reviewer Plugin.</p>
 			</div> 		
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
			<?php
				settings_fields( $this->option_name );
				do_settings_sections( $this->menu_slug );


				if( isset( $this->option_value['support_copy_id'] ) )
					echo '<p class="rwp-cp-id"><strong>'. __('Your Plugin Copy ID', $this->plugin_slug) .'</strong> <span>'. $this->option_value['support_copy_id'] .'</span></p>';


				$other_attributes = NULL;

				if( isset( $this->option_value['support_copy_id'] ) ) 
					$other_attributes = array('disabled' => 'disabled');

				submit_button( __( 'Activate', $this->plugin_slug), 'primary', 'submit', true, $other_attributes );

			?>
			</form>

			<?php //RWP_Reviewer::pretty_print(  $this->option_value ); ?>
		</div><!--/wrap-->
		<?php
	}

	public function register_page_fields()
	{
		// Add section
		add_settings_section( 'rwp_support_section', '', array( $this, 'display_section'), $this->menu_slug );

		foreach ($this->support_fields as $field_id => $field) {

			// Get selected value for the field
			$selected = ( isset( $this->option_value[ $field_id ] ) && ! empty( $this->option_value[ $field_id ] ) ) ? $this->option_value[ $field_id ] : $this->support_fields[ $field_id ]['default'];

			add_settings_field( $field_id, $field['title'], array( $this, $field_id . '_cb' ), $this->menu_slug, 'rwp_support_section', array( 'field_id' => $field_id, 'value' => $selected, 'default' => $field['default'], 'description' => $field['description'] ) );
		}

		register_setting( $this->option_name, $this->option_name, array( $this, 'validate_fields' ) );
	}

	public static function get_instance() 
	{
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function display_section()
	{
		// Do Nothing!
	}

	public function validate_fields( $fields )
	{
		$valids = array();
		$errors = array();

		foreach ($this->support_fields as $field_id => $field) {
			
			if( ! isset( $fields[ $field_id ] ) ) { // The field is not set
				
				$errors[] = __('Invalid form submission', $this->plugin_slug);
				break;
			}

			$value = trim( $fields[ $field_id ] );

			if( empty( $value ) ) {

				if( empty( $field['default'] ) )
					$errors[] = sprintf( __('%s is required', $this->plugin_slug), $field['title'] );
				 else
					$valids[ $field_id ] = $field['default'];

			} else {

				$valids[ $field_id ] = esc_sql( esc_html ( $value ) );
			}
		}


		if( !empty( $errors ) ) { // Some errors

			add_settings_error( $this->option_name, 'rwp-support-warning', implode( '<br/>', $errors ), 'update-nag' );
			return array();

		} else { // Validation ok, register plugin copy

			$res = $this->register_plugin( $valids );

			if( $res['status'] === false ) { // Request failed

				add_settings_error( $this->option_name, 'rwp-support-warning', $res['body'] , 'update-nag' );
				return array();

			} else { // Request ok

				if( $res['body']['status'] ) { // Registered

					RWP_Notification::delete('support');
					$valids['support_copy_id'] = $res['body']['resp'];
					add_settings_error( $this->option_name, 'rwp-support-ok', __( 'Thank you for activating your plugin copy.', $this->plugin_slug ), 'updated' );

				} else { // Registration failed
					
					add_settings_error( $this->option_name, 'rwp-support-warning', __( 'Unable to register the plugin copy. Please check your Envato Username and Purchase Code fields.', $this->plugin_slug ) , 'update-nag' );
					return array();
				}
			}
		}

		return $valids;
	}

	private function register_plugin( $args ) 
	{
		global $wp_version;

		$body = array(
			'name' 				=> $args['support_name'],
			'email' 			=> $args['support_email'],
			'envato' 			=> $args['support_envato_username'].'@'.$args['support_purchase_code'],
			'pluginVersion' 	=> RWP_Reviewer::VERSION,
			'wordpressVersion' 	=> $wp_version,
			'siteUrl' 			=> site_url(),
			'userAgent' 		=> $_SERVER['HTTP_USER_AGENT'],
		);

		$api_url = self::$base_url . self::$url;

		$response = wp_remote_post( $api_url,  $a = array(
			'redirection' 	=> 5,
			'body' 			=> $body,
			'headers' 		=> array(
				'reviewer-plugin' => self::$token
			),
		));

		$result = array( 'status' => false, 'body'=> '');

		if ( is_wp_error( $response ) ) {

			$result['body'] = __( 'Unable to activate the plugin. Please contact the plugin support Team via email. Error: ', $this->plugin_slug ) . $response->get_error_message();
		
		} elseif( $response['response']['code'] != 200 ){

			$result['body'] = __( 'Unable to activate the plugin. Unauthorized request', $this->plugin_slug );

		} else {
		   
		   $content = json_decode( $response['body'], true );
		   $result['status'] = true;
		   $result['body'] = $content;
		}

		return $result;
	}

	public function set_support_fields()
	{
		$this->support_fields = array(
		
			'support_name' => array(
				'title' 	=> __( 'Full Name', $this->plugin_slug ), 
				'default' 	=> $this->user->user_firstname . ' ' . $this->user->user_lastname,
				'description'	=> ''
			),

			'support_email' => array(
				'title' 	=> __( 'Email', $this->plugin_slug ), 
				'default' 	=> $this->user->user_email,
				'description'	=> ''
			),

			'support_envato_username' => array(
				'title' 	=> __( 'Envato Username', $this->plugin_slug ), 
				'default' 	=> '',
				'description'	=> __('Your username for accessing to Envato Marktplace', $this->plugin_slug)
			),

			'support_purchase_code' => array(
				'title' 	=> __( 'Envato Item Purchase Code', $this->plugin_slug ), 
				'default' 	=> '',
				'description'	=> __('Insert the purchase code you download with the Reviewer plugin. Where can I find it?', $this->plugin_slug)
			)
		);
	}

/*----------------------------------------------------------------------------*
 * Callbacks for form fields
 *----------------------------------------------------------------------------*/

	public function support_name_cb( $args )
	{
		extract( $args );

		$value = (empty($value)) ? $this->user->user_firstname . ' ' . $this->user->user_lastname : $value;

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="' . $default . '" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}

	public function support_email_cb( $args )
	{
		extract( $args );

		$value = (empty($value)) ? $this->user->user_email : $value;

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="' . $default . '" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}

	public function support_envato_username_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="' . $default . '" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}

	public function support_purchase_code_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="' . $default . '" />';
		echo '<span class="rwp-field-desc description">'. $description .' <a href="'. RWP_PLUGIN_URL .'admin/assets/images/where-i-can-find-purchase-code.png">'. __('Here', $this->plugin_slug) .'</a></span>';
	}
}
