<?php
/**
 * Settings
 *
 * @since 3.0.0
 **/
namespace wpjmcl\settings;
if ( ! defined( 'WPINC' ) ) { die; }

/* Load Class */
Setup::get_instance();

/**
 * Setup Settings
 */
final class Setup {

	/**
	 * Construct
	 */
	public function __construct() {

		/* Add WP Job Manager Settings */
		add_filter( 'job_manager_settings', array( $this, 'add_settings' ) );

		/* Sanitize Options */
		add_filter( 'sanitize_option_job_manager_claim_listing_page_id', 'intval' );
		add_filter( 'sanitize_option_wpjmcl_submit_claim_data', function( $input ) {
			return $input ? 1 : 0;
		} );

		/* Add Custom Settings Fields */
		add_filter( 'wp_job_manager_admin_field_wpjmcl_heading', '__return_false', 10, 4 );

		/* Style For heading */
		add_action( 'admin_head-job_listing_page_job-manager-settings', array( $this, 'settings_css' ) );
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 */
	public static function get_instance() {
		static $instance = null;
		if ( is_null( $instance ) ) { $instance = new self;
		}
		return $instance;
	}

	/**
	 * Add "Claim Listing" Tab on WP Job Manager Settings
	 *
	 * @since 3.0.0
	 */
	public function add_settings( $settings ) {

		/* Claim Listing Page */
		$settings['job_pages'][1][] = array(
			'name'      => 'job_manager_claim_listing_page_id',
			'std'       => '',
			'label'     => __( 'Claim Listing Page', 'wp-job-manager-claim-listing' ),
			'desc'      => __( 'Select the page where you have placed the [claim_listing] shortcode (required).', 'wp-job-manager-claim-listing' ),
			'type'      => 'page',
		);

		/* Header */
		$settings['wpjmcl_settings'] = array(
			__( 'Claim Listing', 'wp-job-manager-claim-listing' ),
			array(),
		);

		/* Allow claimer submit claim data */
		$settings['wpjmcl_settings'][1][] = array(
			'name'       => 'wpjmcl_submit_claim_data',
			'std'        => '',
			'label'      => __( 'Collect Additional Information', 'wp-job-manager-claim-listing' ),
			'cb_label'   => __( 'Allow the person claiming the listing add additional verification information.', 'wp-job-manager-claim-listing' ),
			'desc'       => __( 'If enabled an input will be displayed so user can add claim verification data.', 'wp-job-manager-claim-listing' ),
			'type'       => 'checkbox',
			'attributes' => array(),
		);

		/* Allow claimer submit claim data */
		$settings['wpjmcl_settings'][1][] = array(
			'name'       => 'wpjmcl_claim_own_listing',
			'std'        => '',
			'label'      => __( 'Claim Own Listing', 'wp-job-manager-claim-listing' ),
			'cb_label'   => __( 'Allow non-claimed listings to be claimed by the same owner.', 'wp-job-manager-claim-listing' ),
			'desc'       => __( 'If enabled user can claim their own listing.', 'wp-job-manager-claim-listing' ),
			'type'       => 'checkbox',
			'attributes' => array(),
		);

		$settings['wpjmcl_settings'][1][] = array(
			'name'        => 'wp-job-manager-claim-listing',
			'type'        => 'wp-job-manager-claim-listing_license',
			'std'         => '',
			'placeholder' => '',
			'label'       => __( 'License Key', 'wp-job-manager-claim-listing' ),
			'desc'        => __( 'Enter the license key you received with your purchase receipt to continue receiving plugin updates.', 'wp-job-manager-claim-listing' ),
			'attributes'  => array(),
		);

		return $settings;
	}

	/**
	 * Style For Fake Heading
	 *
	 * @since 3.0.0
	 */
	function settings_css() {
		?>
		<style id="wpjmcl-settings-css" type="text/css">
			label[for="setting-wpjmcl_heading"] {
				position: absolute;
				color: #23282d;
				font-size: 1.2em;
				margin: 0;
			}
			@media screen and ( max-width: 782px ) {
				label[for="setting-wpjmcl_heading"] {
					position: inherit;
				}
			}
		</style>
		<?php
	}

}
