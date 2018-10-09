<?php
/**
 * Extend WooCommerce + Paid Listing
 * This handle all Product Data Setup.
 *
 * @since 3.0.0
 */
namespace wpjmcl\wpjm_wc_paid_listing;
use wpjmcl\job_listing\Functions as Listing;
if ( ! defined( 'WPINC' ) ) { die; }


/* Load Class */
Setup::get_instance();

/**
 * Setup Class
 */
final class Setup {

	/**
	 * Construct
	 */
	public function __construct() {

		/* Option */
		$paid_listing_enabled = get_option( 'wpjmcl_paid_claiming' );

		/*
		 CLAIM
		------------------------------------------ */

		if ( $paid_listing_enabled ) {

			/* Add new claim status */
			add_filter( 'wpjmcl_claim_statuses', array( $this, 'add_claim_status' ) );

			/* Set default listing to "pending_purchase" if option enabled. */
			add_action( 'wpjmcl_create_new_claim', array( $this, 'set_new_claim_status' ), 5, 2 );

			/* Disable default new claim email notification. */
			add_filter( 'wpjmcl_notification_mail_claimer_new_claim_args', '__return_empty_array' );
			add_filter( 'wpjmcl_notification_mail_admin_new_claim_args', '__return_empty_array' );

			/* Update listing when Claim status updated */
			add_action( 'wpjmcl_claim_status_updated', array( $this, 'set_listing_claim_status' ), 11, 3 );
		}

		/*
		 FRONT END
		------------------------------------------ */

		/* Exclude job packages. */
		add_filter( 'wcpl_get_job_packages_args', array( $this, 'exclude_claim_package' ) );

		/*
		 ADMIN
		------------------------------------------ */

		/* Add checkbox "Use for claiming listing?" If Job Package selected. */
		add_filter( 'product_type_options', array( $this, 'add_job_package_use_for_claims_options' ) );

		/* Add "Claimed Listing?" for auto approve if order completed. */
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_claimed_checkbox' ), 20 );

		/* Save data. Use priority 20, so we can override WC Paid Listing data. */
		add_action( 'woocommerce_process_product_meta_job_package', array( $this, 'save_data' ), 20 );
		add_action( 'woocommerce_process_product_meta_job_package_subscription', array( $this, 'save_data' ), 20 );

		/* Claim Columns */
		add_filter( 'manage_edit-claim_columns', array( $this, 'manage_columns' ) );
		add_action( 'manage_claim_posts_custom_column', array( $this, 'manage_custom_column' ), 10, 2 );

		/* Admin Scripts */
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

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
	 * Add Claim Status "Pending Purchase".
	 *
	 * @since 3.0.0
	 */
	function add_claim_status( $statuses ) {
		$statuses['pending_purchase'] = __( 'Pending Purchase', 'wp-job-manager-claim-listing' );
		$statuses['pending_order']    = __( 'Pending Order', 'wp-job-manager-claim-listing' );
		$statuses['order_completed']  = __( 'Order Completed', 'wp-job-manager-claim-listing' );
		return $statuses;
	}


	/**
	 * Set New Claim Status To "Pending Purchase"
	 *
	 * @since 3.0.0
	 */
	public function set_new_claim_status( $claim_id, $context ) {
		if ( 'front' == $context ) {
			update_post_meta( $claim_id, '_status', 'pending_purchase' );
		}
	}


	/**
	 * Set Listing Data as Package Claim Data When Approved
	 *
	 * @since 3.1.0
	 */
	function set_listing_claim_status( $claim_id, $old_status, $request ) {

		/* Claim Data */
		$claim_status = get_post_meta( $claim_id, '_status', true );

		/* Listing Data */
		$listing_id = get_post_meta( $claim_id, '_listing_id', true );
		$listing_claimed = get_post_meta( $listing_id, '_claimed', true );

		/* Status is approved */
		if ( ( 'approved' == $claim_status ) && $listing_claimed ) {
			Listing::update_listing_on_claim_approval( $claim_id, $listing_id );
		}
	}


	/**
	 * Exclude job packages for claim in WC Paid Listing Plugin.
	 *
	 * @since 3.0.0
	 */
	public function exclude_claim_package( $args ) {
		$args['meta_query'][] = array(
			'key'     => '_use_for_claims',
			'value'   => 'yes',
			'compare' => '!=',
		);
		return $args;
	}


	/**
	 * Add product type option.
	 * Add a product type option to allow job_listings to also be claim listing packages
	 * For array key, do not use "_" underscore. WC will add that
	 * The array key is the "post meta key".
	 *
	 * @see WC/includes/admin/meta-boxes/class-wc-meta-box-product-data.php line 66 (v.2.6.4)
	 */
	public function add_job_package_use_for_claims_options( $product_type_options ) {
		$product_type_options['use_for_claims'] = array(
			'id'            => '_use_for_claims',
			'wrapper_class' => 'show_if_job_package show_if_job_package_subscription',
			'label'         => __( 'Use for Claiming a Listing', 'wp-job-manager-claim-listing' ),
			'description'   => __( 'Allow this package to be a option for claiming a listing. These packages will not appear on the standard listing submission form.', 'wp-job-manager-claim-listing' ),
			'default'       => 'no',
		);
		return $product_type_options;
	}


	/**
	 * Add claimed checkbox to the listing products.
	 * When checked the created listing will be set to claimed automatically.
	 * This checkbox is added in "woocommerce_product_options_general_product_data"
	 *
	 * @uses woocommerce_wp_checkbox()
	 * @see WC/includes/admin/wc-meta-box-functions.php line 135 (v.2.6.4)
	 * @see WC/includes/admin/meta-boxes/class-wc-meta-box-product-data.php line 272 (v.2.6.4)
	 */
	public function add_claimed_checkbox() {

		/* Get post data */
		$post = get_post();

		/* Add checkbox. */
		woocommerce_wp_checkbox( array(
			'id'             => '_default_to_claimed',
			'name'           => '_default_to_claimed',
			'label'          => __( 'Claimed Listing?', 'wp-job-manager-claim-listing' ),
			'description'    => __( 'Automatically be mark listing as claimed/verified if user completed the purchase.', 'wp-job-manager-claim-listing' ),
			'value'          => get_post_meta( $post->ID, '_default_to_claimed', true ),
			'cbvalue'        => 'yes',
			'wrapper_class'  => 'show_if_job_package show_if_job_package_subscription',
		) );

	}


	/**
	 * Save Product Data
	 *
	 * @since 3.0.0
	 */
	public function save_data( $post_id ) {

		/* Save Product Data Type Options. Product type options do not have "value" attr. */
		$for_claims = isset( $_POST['_use_for_claims'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_use_for_claims', $for_claims );

		/* if use for claim */
		if ( 'yes' == $for_claims ) {

			/* Set job listing limit to 1 */
			update_post_meta( $post_id, '_job_listing_limit', 1 );

			/* Set listing subs package to "listing" */
			update_post_meta( $post_id, '_package_subscription_type', 'listing' );
		}

		/* Save default to claimed data */
		$value = ( isset( $_POST['_default_to_claimed'] ) && 'yes' == $_POST['_default_to_claimed'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_default_to_claimed', $value );
	}


	/**
	 * Manage Columns
	 *
	 * @since 3.0.0
	 */
	function manage_columns( $columns ) {
		$columns['order'] = __( 'Order', 'wp-job-manager-claim-listing' );
		return $columns;
	}


	/**
	 * Custom Columns
	 *
	 * @since 3.0.0
	 */
	function manage_custom_column( $column, $post_id ) {
		switch ( $column ) {
			case 'order' :

				/* Var */
				$text = '';

				/* Order */
				$order_id = intval( get_post_meta( $post_id, '_order_id', true ) );
				if ( $order_id ) {

					/* Add order ID in text */
					$text = "#{$order_id}";

					/* Get order object */
					$order_obj = wc_get_order( $order_id );
					if ( $order_obj ) {

						/* Add Edit Link */
						if ( $edit_link = get_edit_post_link( $order_id ) ) {
							$text = '<a target="_blank" href="' . esc_url( $edit_link ) . '">' . $text . '</a>';
						}

						/* Add Order Status */
						$status = $order_obj->get_status();
						$text .= " &ndash; <em>{$status}</em>";
					} else {
						$text .= ' &ndash; ' . __( 'Cannot retrive order.', 'wp-job-manager-claim-listing' );
					}
				}
				echo $text;
			break;
		}
		return $column;
	}


	/**
	 * Admin Scripts
	 */
	public function admin_scripts( $hook_suffix ) {
		global $post_type;

		/*
		 Product Data Scripts */
		// wp_register_style( 'wpjmcl_product_data_meta_box', URI . 'assets/meta-box-product-data.css', array(), VERSION );
		wp_register_script( 'wpjmcl_product_data_meta_box', URI . 'assets/meta-box-product-data.js', array( 'jquery' ), VERSION, true );

		/* Check post type */
		if ( 'product' == $post_type && in_array( $hook_suffix, array( 'post.php', 'post-new.php' ) ) ) {
			// wp_enqueue_style( 'wpjmcl_product_data_meta_box' );
			wp_enqueue_script( 'wpjmcl_product_data_meta_box' );
		}
	}
}


