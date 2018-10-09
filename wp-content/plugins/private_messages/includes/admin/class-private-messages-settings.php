<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings Class.
 *
 * @since 1.0.0
 */
class Private_Messages_Settings {

	/**
	 * Settings.
	 *
	 * @since 1.0.0
	 */
	public $settings = false;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function __construct() {

		// Register settings.
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Add settings page.
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 12 );

		// Scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Register Settings.
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {

		// Register Settings.
		register_setting(
			$options_group  = 'pm_settings',
			$options_name   = 'pm_settings',
			$sanitize_cb    = array( $this, 'settings_sanitize' )
		);

		// Register License Settings.
		register_setting(
			$options_group  = 'pm_settings',
			$options_name   = 'private-messages',
			$sanitize_cb    = 'esc_attr'
		);

		// Custom Sections.
		$this->settings = apply_filters( 'private_messages_settings', array(
			'general'           => array(
				'label'       => __( 'General', 'private-messages' ),
				'fields'      => array(
					array(
						'label'        => __( 'Message Dashboard Page', 'private-messages' ),
						'description'  => __( 'This page allows users to compose, view and reply to messages from the front-end. The [private_messages] short code must be on this page.', 'private-messages' ),
						'id'           => 'pm_dashboard_page_id',
						'type'         => 'select',
						'options'      => pm_get_pages(),
						'default'      => '',
						'sanitize'     => 'absint',
					),
					array(
						'label'        => __( 'Compose from Dashboard', 'private-messages' ),
						'description'  => __( 'Allow all users to compose a new message from the Message Dashboard page', 'private-messages' ),
						'id'           => 'pm_allow_compose_from_dashboard',
						'type'         => 'checkbox',
						'default'      => true,
						'sanitize'     => 'wp_validate_boolean',
					),
					array(
						'label'        => __( 'Enable Message Attachments', 'private-messages' ),
						'description'  => __( 'Allow user to upload file attachments in Private Messages.', 'private-messages' ),
						'id'           => 'pm_allow_attachments',
						'type'         => 'checkbox',
						'default'      => true,
						'sanitize'     => 'wp_validate_boolean',
					),
					array(
						'label'        => __( 'Attachment max file size', 'private-messages' ),
						'description'  => sprintf( __( 'Maximum file size for each attachments (in bytes). %1$s equals to %2$s', 'private-messages' ), intval( pm_get_option( 'pm_attachments_max_file_size' , wp_max_upload_size() ) ), size_format( intval( pm_get_option( 'pm_attachments_max_file_size' , wp_max_upload_size() ) ) ) ),
						'id'           => 'pm_attachments_max_file_size',
						'type'         => 'text',
						'default'      => wp_max_upload_size(),
						'sanitize'     => 'intval',
					),
					array(
						'label'        => __( 'License Key', 'private-messages' ),
						'description'  => '',
						'id'           => 'pm_license_key',
						'type'         => 'license-key',
						'default'      => '',
						'sanitize'     => 'esc_attr', // Not really needed.
					),
				),
			),
			'notifications'      => array(
				'label'       => __( 'Notifications', 'private-messages' ),
				'fields'      => array(
					array(
						'label'        => __( 'Send Message Notifications', 'private-messages' ),
						'description'  => __( 'Notify the recipient when a new message is added.', 'private-messages' ),
						'id'           => 'send_message_notification',
						'type'         => 'checkbox',
						'default'      => true,
						'sanitize'     => 'wp_validate_boolean',
					),
					array(
						'label'        => __( 'Notification Subject', 'private-messages' ),
						'id'           => 'notification_subject',
						'description'  => __( 'Edit the subject for the email sent to a recipient after a message is received.', 'private-messages' ),
						'type'         => 'text',
						'default'      => pm_default_new_message_notification_subject(),
						'sanitize'     => 'wp_kses_post',
					),
					array(
						'label'        => __( 'Notification Message', 'private-messages' ),
						'description'  => __( 'Edit the email text sent to a recipient after a message is received. Available tags: <code>{site_name}</code>, <code>{sender_name}</code>, <code>{recipient_name}</code>, <code>{message}</code>, <code>{link_to_message}</code>', 'private-messages' ),
						'id'           => 'notification_message',
						'type'         => 'textarea',
						'default'      => pm_default_new_message_notification_message(),
						'sanitize'     => 'wp_kses_post',
					),
				),
			),
		) );
	}

	/**
	 * Settings Sanitize Callback.
	 *
	 * @since 1.0.0
	 */
	public function settings_sanitize( $input ) {
		// Get all fields output.
		$output = array();
		foreach ( $this->settings as $tab_id => $data ) {
			foreach ( $data['fields'] as $field ) {
				$name = $field['id'];
				if ( is_callable( $field['sanitize'] ) ) {
					// Unchecked checkbox is not set.
					if ( 'checkbox' === $field['type'] && ! isset( $input[ $name ] ) ) {
						$input[ $name ] = false;
					}
					if ( isset( $input[ $name ] ) ) {
						$output[ $name ] = call_user_func( $field['sanitize'], $input[ $name ] );
					}
				}
			}
		}

		return $output;
	}

	/**
	 * Add settings menu function.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function admin_menu() {
		add_submenu_page(
			$parent_slug  = 'edit.php?post_type=private-messages',
			$page_title   = __( 'Settings', 'private-messages' ),
			$menu_title   = __( 'Settings', 'private-messages' ),
			$capability   = 'manage_options',
			$menu_slug    = 'pm-settings',
			$function     = array( $this, 'output' )
		);
	}

	/**
	 * Settings Output
	 *
	 * @since 1.0.0
	 */
	public function output() {
?>
	<div class="wrap">
		<form method="post" action="options.php">

			<h1 class="private-messages-settings-tab nav-tab-wrapper wp-clearfix">
			<?php foreach ( $this->settings as $tab_id => $data ) : ?>
				<a href="#private-messages_<?php echo esc_attr( $tab_id ); ?>" class="nav-tab"><?php echo esc_html( $data['label'] ); ?></a>
			<?php endforeach; ?>
			</h1>

			<?php settings_errors(); ?>

			<?php foreach ( $this->settings as $tab_id => $data ) : ?>
				<div id="private-messages_<?php echo esc_attr( $tab_id ); ?>" class="private-messages-section">

					<table class="form-table">
						<tbody>
							<?php foreach ( $data['fields'] as $field ) : ?>
								<?php
									$defaults = array(
										'label'        => $field['id'],
										'description'  => '',
										'name_input'   => 'pm_settings[' . $field['id'] . ']',
										'option_name'  => $field['id'],
										'type'         => 'text',
										'default'      => '',
										'sanitize'     => 'esc_attr',
									);
									$field = wp_parse_args( $field, $defaults );
								?>
								<tr>
									<th scope="row">
										<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo $field['label']; ?></label>
									</th>
									<td>
										<?php echo Private_Messages_Templates::get_template( 'admin/settings/' . $field['type'] . '.php', $field ); ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php endforeach; ?>

			<?php do_settings_sections( 'pm-settings' ); ?>
			<?php settings_fields( 'pm_settings' ); ?>
			<?php submit_button(); ?>

		</form>
	</div>
<?php
	}

	/**
	 * Admin Scripts
	 *
	 * @since 1.5.0
	 */
	public function admin_enqueue_scripts( $hook_suffix ) {
		if ( 'private-messages_page_pm-settings' !== $hook_suffix ) {
			return;
		}

		// Get assets URL.
		$pm  = private_messages();
		$url = $pm->plugin_url;
		$ver = $pm->version;

		wp_enqueue_style( 'private-messages-settings', $url . '/assets/css/settings.css', null, $ver );
		wp_enqueue_script( 'private-messages-settings', $url . '/assets/js/settings.js', array( 'jquery' ), $ver, true );
	}

}
