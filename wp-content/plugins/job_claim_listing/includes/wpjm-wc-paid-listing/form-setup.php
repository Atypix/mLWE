<?php
/**
 * Setup
 *
 * @since 3.0.0
 */
namespace wpjmcl\wpjm_wc_paid_listing;
use wpjmcl\submit_claim\Submit_Claim_Form;
if ( ! defined( 'WPINC' ) ) { die; }


/* Load Class */
Form_Setup::get_instance();

/**
 * Setup Class
 */
final class Form_Setup {

	/**
	 * Construct
	 */
	public function __construct() {

		/* Paid listing enabled? */
		$paid_listing_enabled = get_option( 'wpjmcl_paid_claiming' );
		if ( $paid_listing_enabled ) {

			/*
			 Submit Claim Form
			------------------------------------------ */

			/* Filter Form Steps */
			add_filter( 'wpjmcl_submit_claim_form_steps', array( $this, 'add_form_step' ) );

			/* "Claim Listing" Step Handler */
			add_action( 'wpjmcl_submit_claim_form_login_register_handler_after', array( $this, 'submit_claim_handler_after' ) );

			/* "Claim Detail" View */
			add_action( 'wpjmcl_submit_claim_form_claim_detail_view_close', array( $this, 'add_claim_detail' ) );

		}
	}

	/**
	 * Returns the instance.
	 */
	public static function get_instance() {
		static $instance = null;
		if ( is_null( $instance ) ) { $instance = new self;
		}
		return $instance;
	}


	/*
	 Submit Claim Form
	------------------------------------------ */


	/**
	 * Add Form Step
	 * Add Select Package Step In Submit Claim Form
	 *
	 * @since 3.0.0
	 */
	public function add_form_step( $steps ) {

		/* Register on checkout */
		if ( get_option( 'wpjmcl_register_on_checkout' ) ) {
			unset( $steps['login_register'] );
		}

		/* Change button on claim package */
		$steps['claim_listing']['submit'] = __( 'Choose a package &rarr;', 'wp-job-manager-claim-listing' );

		/* Add select package step. */
		$steps['claim_package'] = array(
			'name'     => __( 'Choose a package', 'wp-job-manager-claim-listing' ),
			'view'     => array( $this, 'claim_package_view' ),
			'handler'  => array( $this, 'claim_package_handler' ),
			'priority' => 4,
			'submit'   => __( 'Checkout &rarr;', 'wp-job-manager-claim-listing' ),
		);
		return $steps;
	}


	/**
	 * Claim Select Package View
	 *
	 * @see WCPL/includes/class-wp-paid-listings-submit-job-form.php
	 * @see WP_Job_Manager_WCPL_Submit_Job_Form::choose_package()
	 */
	public function claim_package_view() {
		$form = Submit_Claim_Form::instance();
		$packages = self::get_packages_for_claiming();
		?>
		<form id="<?php echo esc_attr( $form->get_form_name() ); ?>" class="job-manager-form wpjmcl_form wpjmcl_form_claim_package" method="post">

			<div class="job_listing_packages_title">

				<?php if ( $packages ) { ?>
					<input type="submit" value="<?php echo esc_attr( $form->get_step_submit() ); ?>" class="button" name="submit">
					<input type="hidden" name="claim_id" value="<?php echo esc_attr( $form->claim_id ); ?>" />
					<input type="hidden" name="step" value="<?php echo intval( $form->get_step() ); ?>">
				<?php } ?>

				<h2><?php _e( 'Choose a package', 'wp-job-manager-claim-listing' ); ?></h2>
			</div><!-- .job_listing_packages_title -->

			<div class="job_listing_packages">

				<?php if ( $packages ) {
					$checked = 1;
					?>

					<ul class="job_packages">

						<li class="package-section"><?php _e( 'Purchase Package:', 'wp-job-manager-claim-listing' ); ?></li>

						<?php foreach ( $packages as $key => $package ) {
							$product = wc_get_product( $package );
							/* Skip if not purchase-able. */
							if ( ! $product->is_type( array( 'job_package', 'job_package_subscription' ) ) || ! $product->is_purchasable() ) {
								continue;
							}
							?>

							<li class="job-package">

								<input type="radio" <?php checked( $checked, 1 );
								$checked = 0; ?> name="job_package" value="<?php echo $product->get_id(); ?>" id="package-<?php echo $product->get_id(); ?>" />

								<label for="package-<?php echo $product->get_id(); ?>"><?php echo $product->get_title(); ?></label><br/>

								<?php echo ( ! empty( $product->get_short_description() ) ) ? apply_filters( 'woocommerce_short_description', $product->get_short_description() ) : '' ?>

								<?php echo $product->get_price_html() . ' '; ?>
								<?php echo $product->get_duration() ? sprintf( _n( '(Listed for %s day)', '(Listed for %s days)', $product->get_duration(), 'wp-job-manager-claim-listing' ), $product->get_duration() ) : ''; ?>

							</li>

						<?php } // End foreach().
?>

					</ul><!-- .job_packages-->

				<?php } else { // package not available  ?>

					<p><?php _e( 'No packages found', 'wp-job-manager-claim-listing' ); ?></p>

				<?php } // End if().
?>

			</div><!-- .job_listing_packages -->

		</form>
		<?php
	}

	/**
	 * Claim Select Package Handler
	 */
	public function claim_package_handler() {
		$form       = Submit_Claim_Form::instance();
		$listing_id = $form->listing_id;
		$claim_id   = ( isset( $_POST['claim_id'] ) && ! empty( $_POST['claim_id'] ) ) ? intval( $_POST['claim_id'] ) : $form->claim_id;

		/* Package ID. */
		$package_id = 0;
		if ( isset( $_POST['job_package'] ) ) {
			$package_id = intval( $_POST['job_package'] );
		}

		/* If order already in place: No need to checkout, Go to next step. */
		if ( false ) {
			$form->next_step();
		} // End if().

		else {
			/* Validate selected package */
			$validation = self::validate_package( $package_id );

			/* If not valid, display message */
			if ( is_wp_error( $validation ) ) {
				$form->add_error( $validation->get_error_message() );
			} // End if().

			else {

				/* Product object */
				$package = wc_get_product( $package_id );

				/**
				 * Update Claim With Package Data
				 */
				update_post_meta( $claim_id, '_package_id', $package_id );

				/**
				 * WC Paid Listing Compat
				 * Save the package ID to listing
				 *
				 * @see WCPL/includes/class-wc-paid-listings-submit-job-form.php line 272
				 */
				update_post_meta( $listing_id, '_package_id', $package_id );

				/* Add product to cart with all info needed. */
				WC()->cart->add_to_cart(
					$product_id     = $package_id,
					$quantity       = 1,
					$variation_id   = '',
					$variation      = array(),
					$cart_item_data = array(
						'job_id'      => $listing_id, // WC Paid Listing Compat.
						'claim_id'    => $claim_id,
					)
				);

				/* Redirect to checkout */
				wp_redirect( esc_url_raw( wc_get_checkout_url() ) );
				exit;
			}
		}
	}

	/*
	 Submit Listing Steps Mod
	------------------------------------------ */

	/**
	 * If Claim Status is "pending_order" or "order_completed"
	 * Skip the "purchase" step.
	 */
	public function submit_claim_handler_after( $claim_id ) {
		$form = Submit_Claim_Form::instance();
		if ( isset( $claim_id ) && ! empty( $claim_id ) ) {
			$status = get_post_meta( $claim_id, '_status', true );
			if ( in_array( $status, array( 'pending_order', 'order_completed' ) ) ) {
				$form->next_step();
			}
		}
	}

	/**
	 * Add Details in "Claim Details" view
	 */
	public function add_claim_detail( $claim_id ) {
		if ( ! isset( $claim_id ) || empty( $claim_id ) ) { return false;
		}

		/* Add order details */
		$order_id = get_post_meta( $claim_id, '_order_id', true );
		$order_obj = wc_get_order( $order_id );
		if ( $order_obj ) {
			$status = $order_obj->get_status();
			?>
				<fieldset>
					<label><?php _e( 'Order ID', 'wp-job-manager-claim-listing' ); ?></label>
					<div class="field">
						<?php echo "<strong>#{$order_id}</strong> ({$status})"; ?>
					</div>
				</fieldset>
			<?php

		}

		/* Package Details */
		$product_id = get_post_meta( $claim_id, '_package_id', true );
		$product_obj = wc_get_product( $product_id );
		if ( $product_obj ) {
			?>
			<fieldset>
				<label><?php _e( 'Package', 'wp-job-manager-claim-listing' ); ?></label>
				<div class="field">
					<?php echo $product_obj->get_title(); ?>
				</div>
			</fieldset>
			<?php
		}
	}

	/*
	 Utility Functions
	------------------------------------------ */

	/**
	 * Validate Product.
	 *
	 * @param  int  $package_id
	 * @param  bool $is_user_package
	 * @return bool|WP_Error
	 */
	private static function validate_package( $package_id ) {

		/* No Package Selected */
		if ( empty( $package_id ) ) {
			return new WP_Error( 'error', __( 'Invalid Package', 'wp-job-manager-claim-listing' ) );
		} // End if().

		else {

			/* Get packages */
			$packages = self::get_packages_for_claiming();
			if ( ! $packages ) {
				return new WP_Error( 'error', __( 'No package available to purchase.', 'wp-job-manager-claim-listing' ) );
			}

			/* Check if selected package is in the list. */
			$package_ids = array();
			foreach ( $packages as $package ) {
				$package_ids[] = $package->ID;
			}
			if ( ! in_array( $package_id, $package_ids ) ) {
				return new WP_Error( 'error', __( 'Invalid Package', 'wp-job-manager-claim-listing' ) );
			}
		}
		return true;
	}


	/**
	 * Get Packages For Claiming
	 * This will return array of "products" for "claiming".
	 * Used in the form.
	 */
	public static function get_packages_for_claiming( $post__in = array() ) {

		/* Query Args */
		$args = array(
			'post_type'        => 'product',
			'posts_per_page'   => -1,
			'post__in'         => $post__in,
			'order'            => 'asc',
			'orderby'          => 'menu_order',
			'suppress_filters' => false,
			'tax_query'        => array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'product_visibility',
					'field'    => 'slug',
					'terms'    => array( 'exclude-from-search', 'exclude-from-catalog' ),
					'operator' => 'NOT IN',
				),
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => array( 'job_package', 'job_package_subscription' ),
				),
			),
			'meta_query'     => array(
				array(
					'key'     => '_use_for_claims',
					'value'   => 'yes',
					'compare' => '=',
				),
			),
		);
		$args = apply_filters( 'wpjmcl_get_packages_for_claiming', $args );
		return get_posts( $args );
	}
}



