<?php
/**
 * Extend WooCommerce + Paid Listing
 * This handle all Product Data Setup.
 *
 * @since 3.0.0
 */
namespace wpjmcl\wpjm_listing_payments;
use wpjmcl\claim\Functions as Claim;

if ( ! defined( 'WPINC' ) ) { die; }

/* Load Class */
Order_Setup::get_instance();

/**
 * Setup Class
 */
final class Order_Setup {

	/**
	 * Construct
	 */
	public function __construct() {

		/* Order Created On Checkout */
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'order_created' ) );

		/* Two hook, but only process this once. Which ever first. */
		add_action( 'woocommerce_order_status_processing', array( $this, 'order_paid' ), 11 );
		add_action( 'woocommerce_order_status_completed', array( $this, 'order_paid' ), 11 );

		// Use "default to claim" user package.
		add_action( 'astoundify_wpjmlp_process_package_for_job_listing', array( $this, 'default_to_claim_for_user_package' ), 10, 3 );
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
	 * Triggered when order created on checkout
	 * set claim status to "pending_order".
	 *
	 * @param int $order_id Order ID.
	 * @return void
	 */
	public function order_created( $order_id ) {
		$order = wc_get_order( $order_id );

		// Order WP_User
		$order_user = $order->get_user();

		/* Loop each item, and process. */
		foreach ( $order->get_items() as $item_id => $item ) {
			$product_id = $item['product_id'];
			$product = wc_get_product( $product_id );

			if ( $product->is_type( array( 'job_package', 'job_package_subscription' ) ) && isset( $item['claim_id'] ) ) {

				$claim_id = $item['claim_id'];

				if ( $order_user ) {

					/* Set claim author */
					$claim_args = array(
						'ID' => $claim_id,
						'post_author' => $order_user->ID,
					);
					$claim_id = wp_update_post( $claim_args );

					/* Update Order Meta */
					wc_update_order_item_meta( $item_id, 'Claim By', $order_user->display_name );
					wc_update_order_item_meta( $item_id, '_claimer_id', $order_user->ID );
				}

				/* Add order and product info in claim */
				add_post_meta( $claim_id, '_order_id', $order_id );
				add_post_meta( $claim_id, '_package_id', $product_id );

				/* Update claim status and send notification. */
				$old_status = get_post_meta( $claim_id, '_status', true );
				$update = update_post_meta( $claim_id, '_status', 'pending_order' );
				if ( $update ) {
					do_action( 'wpjmcl_claim_status_updated', $claim_id, $old_status, array(
						'_order_id' => $order_id,
						'_package_id' => $product_id,
						'context' => 'order_created',
					) );
				}
			}
		}// End foreach().
	}

	/**
	 * Triggered when an order is paid
	 * This will handle "use for claim" purchases.
	 *
	 * This need to be loaded on priority 11, after user package is created in payment plugin.
	 *
	 * @param  int $order_id
	 * @return void
	 */
	public function order_paid( $order_id ) {
		// Get the order obj
		$order = wc_get_order( $order_id );

		/* Only do it once, if not processing/completed. */
		if ( get_post_meta( $order_id, 'wpjmcl_claim_packages_processed', true ) ) {
			return;
		}

		/* Loop each item, and process. */
		foreach ( $order->get_items() as $item ) {

			// Get product.
			$product = wc_get_product( $item['product_id'] );

			// Only for job package and job package subscription.
			if ( $product && $product->is_type( array( 'job_package', 'job_package_subscription' ) ) && $order->get_user_id() ) {

				// Claiming a listing package.
				if ( 'yes' === get_post_meta( $product->get_id(), '_use_for_claims', true ) && isset( $item['claim_id'] ) &&  $item['claim_id'] ) {

					// Update claim status and send notification.
					$old_status = get_post_meta( $item['claim_id'], '_status', true );
					$new_status = 'completed' === $order->get_status() ? 'approved' : 'order_completed';
					$update = update_post_meta( $item['claim_id'], '_status', $new_status );
					if ( $update ) {
						do_action( 'wpjmcl_claim_status_updated', $item['claim_id'], $old_status, array(
							'_send_notification' => array( 'admin' ),
							'context' => 'order_paid',
						) );
					}

					// Look for the subscription ID for user packages if exists
					if ( class_exists( 'WC_Subscriptions' ) ) {
						if ( wcs_order_contains_subscription( $order ) ) {
							$subs = wcs_get_subscriptions_for_order( $order_id );
							if ( ! empty( $subs ) ) {
								$sub = current( $subs );
								$order_id = $sub->id;
							}
						}
					}

					// Get user package (created by payment plugin).
					global $wpdb;
					$user_package = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wcpl_user_packages WHERE user_id = %d AND order_id = %d AND ( package_count < package_limit OR package_limit = 0 );", $order->get_user_id(), $order_id ) );

					if ( $user_package ) {

						// Increase package count to 1/1.
						astoundify_wpjmlp_increase_package_count( $order->get_user_id(), $user_package->id );

						if ( isset( $item['job_id'] ) ) {
							$job = get_post( $item['job_id'] );
							if ( $job ) {

								update_post_meta( $job->ID, '_user_package_id', $user_package->id );

								if ( $product->is_type( 'job_package_subscription' ) ) {
									do_action( 'astoundify_wpjmlp_switched_subscription', $job->ID, $user_package );
								}
							}
						}

					}

				} elseif ( 'yes' === get_post_meta( $product->get_id(), '_default_to_claimed', true ) && isset( $item['job_id'] ) && $item['job_id'] ) { // Initial purchase of package with default to claim.

					$job = get_post( $item['job_id'] );
					if ( $job ) {
						// All needed is to set the listing to claimed, everything else is done by payment plugin.
						$claim_id = Claim::create_new_claim( $job->ID, $order->get_user_id(), __( 'Automatically verified with initial purchase.', 'wp-job-manager-claim-listing' ), false );
						update_post_meta( $claim_id, '_order_id', $order_id );
						update_post_meta( $claim_id, '_status', 'approved' );
						update_post_meta( $job->ID, '_claimed', 1 );
					}
				}

			} // End check if it's a job package.
		} // End foreach() of order items.

		// Set order meta to not re-process again.
		update_post_meta( $order_id, 'wpjmcl_claim_packages_processed', true );
	}

	/**
	 * Set listing to verified if using default to claim package. 
	 *
	 * @since 3.6.0
	 *
	 * @param int  $package_id User Package ID/Product ID.
	 * @param bool $is_user_package Is user package or WC Product.
	 * @param int  $job_id Listing ID.
	 */
	function default_to_claim_for_user_package( $package_id, $is_user_package, $job_id ) {
		// Only for user package.
		if ( ! $is_user_package ) {
			return;
		}

		// Get user package, product and order var.
		$user_package = astoundify_wpjmlp_get_user_package( $package_id );
		$product = wc_get_product( $user_package->get_product_id() );
		$order_id = $user_package->get_order_id();
		if ( ! $product ) {
			return;
		}

		$job = get_post( $job_id );

		// Default to claim?
		$default_to_claim = 'yes' === get_post_meta( $product->get_id(), '_default_to_claimed', true ) ? true : false;

		// If default to claim, create new claim, and auto claimed it.
		if ( $default_to_claim ) {

			$claim_id = Claim::create_new_claim( $job_id, $job->post_author, __( 'Automatically verified with initial purchase.', 'wp-job-manager-claim-listing' ), false );
			if ( $order_id ) {
				update_post_meta( $claim_id, '_order_id', $order_id );
			}
			update_post_meta( $claim_id, '_status', 'approved' );
			update_post_meta( $job_id, '_claimed', 1 );
		}
	}

} // end class

