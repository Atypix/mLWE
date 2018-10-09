<?php
/**
 * Job Listing CPT Meta Boxes
 *
 * @since 3.0.0
 **/
namespace wpjmcl\job_listing;
use wpjmcl\MetaBox;
use wpjmcl\claim\Functions as Claim;
if ( ! defined( 'WPINC' ) ) { die; }


/* Load Class */
Meta_Boxes_Setup::get_instance();

/**
 * Meta Boxes Setup
 * This class handle all meta boxes system in "job_listing" post type.
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

		/* Save post meta on the 'save_post' hook. */
		add_action( 'save_post', array( $this, 'save_claim_listing_meta_box' ), 10, 2 );
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
			$id         = 'wpjmcl_claim_listing',
			$title      = __( 'Claim Listing', 'wp-job-manager-claim-listing' ),
			$callback   = array( $this, 'claim_listing_meta_box' ),
			$screen     = array( 'job_listing' ),
			$context    = 'side'
		);
	}

	/**
	 * Meta Box Callback
	 *
	 * @since 3.0.0
	 */
	function claim_listing_meta_box( $post, $box ) {
		global $user_ID;
		$post_id = $post->ID;

		/* == Claimed == */
		$claimed = get_post_meta( $post_id, '_claimed', true );
		$args = array(
			'label'         => __( 'Verified Listing', 'wp-job-manager-claim-listing' ),
			'multiple'      => false,
			'control_attr'  => array(
				'name'          => '_claimed',
				'value'         => $claimed ? 1 : 0,
			),
			'choices'       => array(
				1 => __( 'The owner has been verified.', 'wp-job-manager-claim-listing' ),
			),
		);
		MetaBox::checkbox_field( $args );

		/* List Of Claims For This Listing */
		?>
		<div class="fx-mb-field">
			<div class="fx-mb-label astoundify-claim-listing-claimed-label">
				<p>
					<span><?php _e( 'Claim History:', 'wp-job-manager-claim-listing' ); ?></span>
				</p><!-- .fx-mb-label -->
			</div>

			<div class="fx-mb-content">
				<?php
				$claims = get_posts( array(
					'post_type'      => 'claim',
					'posts_per_page' => -1,
					'meta_key'       => '_listing_id',
					'meta_value'     => $post_id,
				) );
				if ( $claims ) {
					foreach ( $claims as $claim ) {
						$claimer_obj = get_userdata( $claim->post_author );
						$text = "#{$claim->ID}:  " . Claim::get_claim_status_label( $claim->ID );
						if ( $edit_link = get_edit_post_link( $claim->ID ) ) {
							$text = '<a target="_blank" href="' . esc_url( $edit_link ) . '">' . $text . '</a>';
						}
						if ( $claimer_obj ) {
							$text .= '<br />' . sprintf( __( 'by %s', 'wp-job-manager-claim-listing' ), $claimer_obj->data->display_name . " ({$claimer_obj->data->user_login})" );
						} else {
							$text .= '<br />' . __( 'by Guest', 'wp-job-manager-claim-listing' );
						}
						echo "<p>{$text}</p>";
					}
				} else {
					echo wpautop( __( 'No Claim Found', 'wp-job-manager-claim-listing' ) );
				}
				?>
			</div>

		</div><!-- .fx-mb-field -->
		<?php

		/* Add nonce */
		wp_nonce_field( __FILE__ , MetaBox::nonce_id( $box['id'] ) );
	}


	/**
	 * Save Post Data
	 *
	 * @since 1.0.0
	 */
	function save_claim_listing_meta_box( $post_id, $post ) {

		/* Verify save post */
		if ( ! MetaBox::verify_save_post( 'wpjmcl_claim_listing', __FILE__, $post_id, $post ) ) {
			return $post_id;
		}

		/* Stripslashes Submitted Data */
		$request = stripslashes_deep( $_POST );

		/* All fields data */
		$fields = array(
			array(
				'key'  => '_claimed',
				'data' => isset( $request['_claimed'] ) && $request['_claimed'] ? 1 : 0,
			),
		);

		foreach ( $fields as $args ) {
			MetaBox::save_post_meta( $post_id, $args['key'], $args['data'] );
		}

	}

	/**
	 * Admin Scripts
	 *
	 * @since 1.0.0
	 */
	function scripts( $hook_suffix ) {
		global $post_type;

		/* Claim Meta Box Scripts */
		wp_register_style( 'wpjmcl_claim_listing_meta_box', URI . 'assets/meta-box-claim-listing.css', array( 'fx-meta-box' ), VERSION );

		/* Check post type */
		if ( 'job_listing' == $post_type && in_array( $hook_suffix, array( 'post-new.php', 'post.php' ) ) ) {
			wp_enqueue_style( 'wpjmcl_claim_listing_meta_box' );
		}
	}

} // end class.
