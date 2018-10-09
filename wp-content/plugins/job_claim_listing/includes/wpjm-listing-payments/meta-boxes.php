<?php
/**
 * Job Listing CPT Meta Boxes
 *
 * @since 3.0.0
 **/
namespace wpjmcl\wpjm_listing_payments;
use wpjmcl\MetaBox;
if ( ! defined( 'WPINC' ) ) { die; }


/* Load Class */
Meta_Boxes_Setup::get_instance();

/**
 * Meta Boxes Setup
 *
 * @since 3.0.0
 */
final class Meta_Boxes_Setup {

	/**
	 * Construct
	 */
	public function __construct() {

		/* Add Meta Boxes */
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		/* Enqueue Scripts */
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
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
	 * Add Meta Boxes
	 *
	 * @since 1.0.0
	 **/
	function add_meta_boxes() {

		/* Claim Listing Meta Box */
		add_meta_box(
			$id         = 'wpjmcl_wc_paid_listing',
			$title      = __( 'Paid Claim Information', 'wp-job-manager-claim-listing' ),
			$callback   = array( $this, 'wc_advanced_paid_listing_meta_box' ),
			$screen     = array( 'claim' ),
			$context    = 'side'
		);
	}

	/**
	 * Meta Box Callback
	 *
	 * @since 3.0.0
	 */
	function wc_advanced_paid_listing_meta_box( $post, $box ) {
		$post_id = $post->ID;

		/* === Order ID === */
		$text = __( 'No Order Found', 'wp-job-manager-claim-listing' );
		$desc = '';
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
		?>
		<div id="order-field" class="fx-mb-field">
			<div class="fx-mb-label">
				<p><span><?php _e( 'Order', 'wp-job-manager-claim-listing' ); ?></span></p>
			</div><!-- .fx-mb-label -->
			<div class="fx-mb-content">
				<p><?php echo $text; ?></p>
			</div><!-- .fx-mb-content -->

		</div><!-- .fx-mb-field -->
		<?php
	}


	/**
	 * Admin Scripts
	 *
	 * @since 1.0.0
	 */
	function scripts( $hook_suffix ) {
		global $post_type;

		/* Check post type */
		if ( 'claim' == $post_type && in_array( $hook_suffix, array( 'post-new.php', 'post.php' ) ) ) {
			wp_enqueue_style( 'fx-meta-box' );
		}
	}

} // end class.
