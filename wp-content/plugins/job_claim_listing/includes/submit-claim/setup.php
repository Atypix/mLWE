<?php
/**
 * Job Listing Setup
 *
 * @since 3.0.0
 */
namespace wpjmcl\submit_claim;
use wpjmcl\claim\Functions as Claim;
use wpjmcl\job_listing\Functions as Listing;
if ( ! defined( 'WPINC' ) ) { die; }

/* Load Class */
Setup::get_instance();

/**
 * Static Functions
 */
final class Setup {

	/**
	 * Construct
	 */
	public function __construct() {

		/* Load Form */
		add_action( 'template_redirect', array( $this, 'load_form' ) );

		/* Register Shortcode */
		add_action( 'init', array( $this, 'register_shortcodes' ) );
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
	 * Is "Submit Claim" Page
	 *
	 * @since 3.0.0
	 */
	public function is_submit_claim_page() {
		$page_id = job_manager_get_page_id( 'claim_listing' );
		if ( ! $page_id ) { return false;
		}
		if ( is_page( $page_id ) ) { return true;
		}
		return false;
	}


	/**
	 * Load Form
	 *
	 * @since 3.0.0
	 */
	public function load_form() {

		/* Only in submit claim page */
		if ( $this->is_submit_claim_page() ) {

			// Make sure registration enabled and account required in claim page.
			add_filter( 'job_manager_enable_registration', '__return_true' );
			add_filter( 'job_manager_user_requires_account', '__return_true' );

			// Load Form.
			require_once( PATH . 'forms/submit-claim-form.php' );
			if ( class_exists( __NAMESPACE__ . '\Submit_Claim_Form' ) ) {
				$form = Submit_Claim_Form::instance();
				$form->process();
			}
		}
	}

	/**
	 * Register Shortcode
	 *
	 * @since 3.0.0
	 */
	function register_shortcodes() {
		if ( ! is_admin() ) {
			add_shortcode( 'claim_listing', array( $this, 'claim_listing_shortcode' ) );
		}
	}

	/**
	 * Claim Listing Shortcode
	 *
	 * @since 3.0.0
	 */
	function claim_listing_shortcode() {
		if ( $this->is_submit_claim_page() && class_exists( __NAMESPACE__ . '\Submit_Claim_Form' ) ) {
			$form = Submit_Claim_Form::instance();
			ob_start();
			$form->output();
			return ob_get_clean();
		}
	}

}
