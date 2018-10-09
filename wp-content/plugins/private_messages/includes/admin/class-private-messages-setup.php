<?php
/**
 * Setup Page
 *
 * @package Private Messages
 * @category Setup
 * @author Astoundify
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Setup
 *
 * @since 1.0.0
 */
class Private_Messages_Setup {

	/**
	 * Init
	 *
	 * @since 1.8.0
	 * @access public
	 *
	 * @return void
	 */
	public static function init() {
		if ( pm_get_option( 'pm_dashboard_page_id' ) ) {
			return;
		}

		// Load library.
		require_once( PM_PATH . 'vendor/astoundify/plugin-setup/astoundify-pluginsetup.php' );

		$config = array(
			'id'           => 'private-messages-setup',
			'capability'   => 'manage_options',
			'menu_title'   => __( 'Private Messages Setup', 'private-messages' ),
			'page_title'   => __( 'Setup Private Messages', 'private-messages' ),
			'redirect'     => true,
			'steps'        => array( // Steps must be using 1, 2, 3... in order, last step have no handler.
				'1' => array(
					'view'    => array( __CLASS__, 'step1_view' ),
					'handler' => array( __CLASS__, 'step1_handler' ),
				),
				'2' => array(
					'view'    => array( __CLASS__, 'step2_view' ),
				),
			),
			'labels'       => array(
				'next_step_button' => __( 'Next Step', 'private-messages' ),
				'skip_step_button' => __( 'Skip', 'private-messages' ),
			),
		);

		// Init setup.
		new Astoundify_PluginSetup( $config );
	}

	/**
	 * Step 1 View.
	 *
	 * @since 1.8.0
	 */
	public static function step1_view() {
?>

<p><?php _e( 'Thanks for installing <em>Private Messages</em>!', 'private-messages' ); ?> <?php _e( 'This setup wizard will help you get started by creating the messages dashboard page.', 'private-messages' ); ?></p>

<p><?php printf( __( 'If you want to skip the wizard and setup the page and shortcode yourself manually, the process is still reletively simple. Refer to the %1$sdocumentation%2$s for help.', 'private-messages' ), '<a href="http://docs.astoundify.com/category/1061-private-messages" target="_blank">', '</a>' ); ?></p>

<h2 class="title"><?php esc_html_e( 'Message Dashboard Setup', 'private-messages' ); ?></h2>

<p><?php printf( __( '<em>Private Messages</em> includes a %1$sshortcode%2$s which can be used within your %3$spage%2$s to output the message dashboard. This can be created for you below. For more information on the shortcode view the %4$sshortcode documentation%2$s.', 'private-messages' ), '<a href="http://codex.wordpress.org/Shortcode" title="What is a shortcode?" target="_blank" class="help-page-link">', '</a>', '<a href="http://codex.wordpress.org/Pages" target="_blank" class="help-page-link">', '<a href="http://docs.astoundify.com/category/1061-private-messages" target="_blank" class="help-page-link">' ); ?></p>

<table class="pm-shortcodes widefat">
	<thead>
		<tr>
			<th>&nbsp;</th>
			<th><?php esc_html_e( 'Page Title', 'private-messages' ); ?></th>
			<th><?php esc_html_e( 'Page Description', 'private-messages' ); ?></th>
			<th><?php esc_html_e( 'Content Shortcode', 'private-messages' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><input type="checkbox" checked="checked" name="private-messages-create-page[dashboard]" /></td>
			<td><input type="text" value="<?php echo esc_attr( _x( 'Message Dashboard', 'Default page title (wizard)', 'private-messages' ) ); ?>" name="private-messages-page-title[dashboard]" /></td>
			<td>
				<p><?php esc_html_e( 'This page allows users to compose, view and reply to messages from the front-end.', 'private-messages' ); ?></p>
			</td>
			<td><code>[private_messages]</code></td>
		</tr>
	</tbody>
</table>

<?php
	}

	/**
	 * Step 1 Handler.
	 *
	 * @since 1.8.0
	 */
	public static function step1_handler() {
		if ( ! isset( $_POST['private-messages-create-page'] ) ) {
			return;
		}

		// Create dashboard pages.
		if ( isset( $_POST['private-messages-create-page']['dashboard'] ) ) {

			// Page Title.
			$title = isset( $_POST['private-messages-page-title']['dashboard'] ) && $_POST['private-messages-page-title']['dashboard'] ? esc_html( $_POST['private-messages-page-title']['dashboard'] ) : esc_html__( 'Message Dashboard', 'private-messages' );

			// Create page.
			$page_data = array(
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_author'    => get_current_user_id(),
				'post_name'      => sanitize_title( $title ),
				'post_title'     => $title,
				'post_content'   => '[private_messages]',
				'post_parent'    => 0,
				'comment_status' => 'closed',
			);
			$page_id = wp_insert_post( $page_data );

			// Update Option.
			pm_update_option( 'pm_dashboard_page_id', intval( $page_id ) );
		}
	}

	/**
	 * Step 2 View.
	 *
	 * @since 1.8.0
	 */
	public static function step2_view() {
?>
<h3><?php _e( 'All Done!', 'private-messages' ); ?></h3>

<p><?php _e( "Looks like you're all set to start using the plugin. In case you're wondering where to go next:", 'private-messages' ); ?></p>

<ul class="pm-next-steps">
	<li><a href="<?php echo admin_url( 'edit.php?post_type=private-messages&page=pm-settings' ); ?>"><?php _e( 'Adjust the plugin settings', 'private-messages' ); ?></a></li>
	<li><a href="<?php echo admin_url( 'post-new.php?post_type=private-messages' ); ?>"><?php _e( 'Create a message via the back-end', 'private-messages' ); ?></a></li>

	<?php if ( $permalink = pm_get_permalink( 'dashboard' ) ) : ?>
		<li><a href="<?php echo esc_url( $permalink ); ?>"><?php _e( 'Create a message via the front-end', 'private-messages' ); ?></a></li>
	<?php endif; ?>

</ul>
<?php
	}

}

Private_Messages_Setup::init();
