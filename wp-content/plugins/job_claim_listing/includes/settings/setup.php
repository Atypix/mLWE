<?php
/**
 * Setup Page
 *
 * @since 3.7.0
 */
namespace wpjmcl\settings\setup;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Setup
 *
 * @since 3.7.0
 */
class Setup {

	/**
	 * Init
	 *
	 * @since 3.7.0
	 * @access public
	 *
	 * @return void
	 */
	public static function init() {
		if ( get_option( 'job_manager_claim_listing_page_id' ) ) {
			return;
		}

		// Load library.
		require_once( WPJMCL_PATH . 'vendor/astoundify/plugin-setup/astoundify-pluginsetup.php' );

		$config = array(
			'id'           => 'wp-job-manager-claim-listing-setup',
			'capability'   => 'manage_options',
			'menu_title'   => __( 'Claim Listing Setup', 'wp-job-manager-claim-listing' ),
			'page_title'   => __( 'Claim Listing Setup', 'wp-job-manager-claim-listing' ),
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
				'next_step_button' => __( 'Submit', 'wp-job-manager-claim-listing' ),
				'skip_step_button' => __( 'Skip', 'wp-job-manager-claim-listing' ),
			),
		);

		// Init setup.
		new \Astoundify_PluginSetup( $config );
	}

	/**
	 * Step 1 View.
	 *
	 * @since 3.7.0
	 */
	public static function step1_view() {
?>

<p><?php _e( 'Thanks for installing <em>Claim Listing for WP Job Manager</em>!', 'wp-job-manager-claim-listing' ); ?> <?php _e( 'This setup wizard will help you get started by creating claim listing page.', 'wp-job-manager-claim-listing' ); ?></p>

<p><?php printf( __( 'If you want to skip the wizard and setup the page and shortcode yourself manually, the process is still reletively simple. Refer to the %1$sdocumentation%2$s for help.', 'wp-job-manager-claim-listing' ), '<a href="http://docs.astoundify.com/article/909-overview" target="_blank">', '</a>' ); ?></p>

<h2 class="title"><?php esc_html_e( 'Claim Page Setup', 'wp-job-manager-claim-listing' ); ?></h2>

<p><?php printf( __( '<em>Claim Listing for WP Job Manager</em> includes a %1$sshortcode%2$s which can be used within your %3$spage%2$s to output the claim form. This can be created for you below.', 'wp-job-manager-claim-listing' ), '<a href="http://codex.wordpress.org/Shortcode" title="What is a shortcode?" target="_blank" class="help-page-link">', '</a>', '<a href="http://codex.wordpress.org/Pages" target="_blank" class="help-page-link">' ); ?></p>

<table class="widefat">
	<thead>
		<tr>
			<th>&nbsp;</th>
			<th><?php esc_html_e( 'Page Title', 'wp-job-manager-claim-listing' ); ?></th>
			<th><?php esc_html_e( 'Page Description', 'wp-job-manager-claim-listing' ); ?></th>
			<th><?php esc_html_e( 'Content Shortcode', 'wp-job-manager-claim-listing' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><input type="checkbox" checked="checked" name="claim-page" /></td>
			<td><input type="text" value="<?php echo esc_attr__( 'Claim Listing', 'wp-job-manager-claim-listing' ); ?>" name="claim-page-title" /></td>
			<td>
				<p><?php esc_html_e( 'Page to claim a listing.', 'wp-job-manager-claim-listing' ); ?></p>
			</td>
			<td><code>[claim_listing]</code></td>
		</tr>
	</tbody>
</table>

<?php
	}

	/**
	 * Step 1 Handler.
	 *
	 * @since 3.7.0
	 */
	public static function step1_handler() {
		if ( ! isset( $_POST['claim-page'] ) ) {
			return;
		}

		// Page Title.
		$title = isset( $_POST['claim-page-title'] ) && $_POST['claim-page-title'] ? esc_html( $_POST['claim-page-title'] ) : esc_html__( 'Claim Listing', 'wp-job-manager-claim-listing' );

		// Create page.
		$page_data = array(
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_author'    => get_current_user_id(),
			'post_name'      => sanitize_title( $title ),
			'post_title'     => $title,
			'post_content'   => '[claim_listing]',
			'post_parent'    => 0,
			'comment_status' => 'closed',
		);
		$page_id = wp_insert_post( $page_data );

		// Update Option.
		update_option( 'job_manager_claim_listing_page_id', intval( $page_id ) );
	}

	/**
	 * Step 2 View.
	 *
	 * @since 3.7.0
	 */
	public static function step2_view() {
?>
<h3><?php _e( 'All Done!', 'wp-job-manager-claim-listing' ); ?></h3>

<p><?php _e( "Looks like you're all set to start using the plugin. In case you're wondering where to go next:", 'wp-job-manager-claim-listing' ); ?></p>

<ul>
	<li><a href="<?php echo admin_url( 'edit.php?post_type=job_listing&page=job-manager-settings' ); ?>"><?php _e( 'Adjust the plugin settings.', 'wp-job-manager-claim-listing' ); ?></a></li>
	<?php if ( class_exists( 'WooCommerce' ) && ( defined( 'ASTOUNDIFY_WPJMLP_PLUGIN' ) || function_exists( 'wp_job_manager_wcpl_init' ) ) ) : ?>
		<li><a href="<?php echo admin_url( 'post-new.php?post_type=product' ); ?>"><?php _e( 'Create a claim package.', 'wp-job-manager-claim-listing' ); ?></a></li>
	<?php endif; ?>
</ul>
<?php
	}

}

Setup::init();
