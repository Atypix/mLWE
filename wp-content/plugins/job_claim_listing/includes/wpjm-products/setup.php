<?php
/**
 * Setup
 *
 * @since 3.0.0
 */
namespace wpjmcl\wpjm_products;
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

		/* Set Product Owner if Enabled */
		add_action( 'wpjmcl_claim_status_updated', array( $this, 'set_product_owner' ), 10, 3 );

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
	 * Set Product Owner
	 *
	 * @since 3.0.0
	 */
	public function set_product_owner( $claim_id, $old_status, $request ) {

		/* Check if option enabled */
		if ( ! get_option( 'wpjmcl_transfer_product_ownership' ) ) {  return;
		}

		/* Check if claim is set to approved */
		$claim_status = get_post_meta( $claim_id, '_status_id', true );
		if ( 'approved' != $claim_status ) { return false;
		}

		/* Check listing exist */
		$listing_id = get_post_meta( $claim_id, '_listing_id', true );
		$listing_obj = get_post( $listing_id );
		if ( ! $listing_obj ) { return false;
		}

		/* Get array of products */
		$products = get_post_meta( $listing_id, '_products', true );
		if ( ! $products || ! is_array( $products ) ) { return false;
		}

		/* Vars */
		$claim_obj = get_post( $claim_id );
		$claimer_obj = get_userdata( $claim_obj->post_author );
		if ( ! $claimer_obj ) { return false;
		}
		$claimer_id = $claimer_obj->ID;

		/* For each products */
		foreach ( $products as $product ) {

			/* Change author */
			wp_update_post( array(
				'ID'          => $product,
				'post_author' => $claimer_id,
			) );

			/**
			 * WooCommerce Product Vendors Compat
			 *
			 * @link https://woocommerce.com/products/product-vendors/
			 */
			if ( class_exists( 'WC_Product_Vendors_Utils' ) ) {
				if ( ! WC_Product_Vendors_Utils::is_vendor( $claimer_id ) ) {
					continue;
				}
				$new_vendors = WC_Product_Vendors_Utils::get_all_vendor_data( $claimer_id );
				foreach ( $new_vendors as $term => $vendor_data ) {
					wp_set_object_terms( $product, $term, WC_PRODUCT_VENDORS_TAXONOMY );
				}
			}
		}

	}

}
