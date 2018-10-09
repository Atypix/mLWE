<?php
/**
 * Claim CPT Meta Boxes
 *
 * @since 3.0.0
 **/
namespace wpjmcl\claim;
use wpjmcl\MetaBox;
if ( ! defined( 'WPINC' ) ) { die; }


/* Load Class */
Meta_Boxes_Setup::get_instance();

/**
 * Meta Boxes Setup
 * This class handle all meta boxes system in "claim" post type.
 *
 * - Remove unneeded meta boxes
 * - Register claim meta boxes
 *
 * @since 3.0.0
 */
final class Meta_Boxes_Setup {

	/**
	 * Construct
	 */
	public function __construct() {

		/* Add Meta Boxes */
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ) );

		/* Add Meta Boxes */
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		/* Save post meta on the 'save_post' hook. */
		add_action( 'save_post', array( $this, 'save_claim_data_meta_box' ), 10, 2 );

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
	 * Remove Unneeded Meta Boxes
	 *
	 * @since 1.0.0
	 **/
	function remove_meta_boxes() {
		remove_meta_box( 'authordiv', 'claim', 'normal' ); // author
		remove_meta_box( 'slugdiv', 'claim', 'normal' ); // slug
	}


	/**
	 * Add Meta Boxes
	 *
	 * @since 1.0.0
	 **/
	function add_meta_boxes() {

		/* Claim Information Meta Box */
		add_meta_box(
			$id         = 'claim_data',
			$title      = __( 'Claim Information', 'wp-job-manager-claim-listing' ),
			$callback   = array( $this, 'claim_data_meta_box' ),
			$screen     = array( 'claim' ),
			$context    = 'normal'
		);
	}

	/**
	 * Claim Data Meta Box Callback
	 *
	 * @since 3.0.0
	 */
	function claim_data_meta_box( $post, $box ) {
		global $user_ID, $hook_suffix;
		$post_id = $post->ID;

		/* == Select Status == */
		$args = array(
			'label'         => __( 'Status', 'wp-job-manager-claim-listing' ),
			'control_attr'  => array(
				'id'              => 'claim-status',
				'name'            => '_status',
				'value'           => Functions::sanitize_claim_status( get_post_meta( $post_id, '_status', true ) ),
				'data-old-status' => Functions::sanitize_claim_status( get_post_meta( $post_id, '_status', true ) ),
			),
			'choices'       => Functions::claim_statuses(),
			'option_none'   => false,
		);
		MetaBox::select_field( $args );

		/* == Email Notification == */
		if ( 'post.php' == $hook_suffix ) {
			$args = array(
				'multiple'      => true,
				'field_attr'    => array(
					'id'            => 'email-notification-options',
					'style'         => 'display:none;',
				),
				'control_attr'  => array(
					'name'          => '_send_notification[]',
					'value'         => array( 'claimer', 'admin' ),
				),
				'choices'       => array(
					'claimer'             => __( 'Send email notification to claimer about this status change.', 'wp-job-manager-claim-listing' ),
					'admin'             => sprintf( __( 'Send email notification to admin (%s) about this status change.', 'wp-job-manager-claim-listing' ), get_option( 'admin_email' ) ),
				),
			);
			MetaBox::checkbox_field( $args );
		}

		/* == Select Job Listing == */
		$text = '';
		/* Listing ID */
		if ( 'post-new.php' == $hook_suffix && isset( $_GET['listing_id'] ) ) {
			$listing_id = intval( $_GET['listing_id'] );
		} else {
			$listing_id = get_post_meta( $post_id, '_listing_id', true );
		}
		if ( $listing_id ) {
			$text = "#{$listing_id} &ndash; " . get_the_title( $listing_id );
			if ( $edit_link = get_edit_post_link( $listing_id ) ) {
				$text = '<a target="_blank" href="' . esc_url( $edit_link ) . '">' . $text . '</a>';
			}
		} else {
			$text = __( 'No Listing Found', 'wp-job-manager-claim-listing' );
		}
		?>
		<div id="job-listing-field" class="fx-mb-field">
			<div class="fx-mb-label">
				<p><span><?php _e( 'Listing', 'wp-job-manager-claim-listing' ); ?></span></p>
			</div><!-- .fx-mb-label -->
			<div class="fx-mb-content">
				<p><?php echo $text; ?></p>
				<input type="hidden" name="_listing_id" value="<?php echo intval( $listing_id )?>">
			</div><!-- .fx-mb-content -->

		</div><!-- .fx-mb-field -->
		<?php

		/* == Select Claimer == */
		$text = '';
		$claimer_id = empty( $post_id ) ? $user_ID : $post->post_author;
		$claimer_obj = get_userdata( $claimer_id );
		if ( $claimer_obj ) {
			$text = "#{$claimer_id} &ndash; {$claimer_obj->data->display_name} ({$claimer_obj->data->user_login})";
			if ( $link = get_edit_user_link( $claimer_id ) ) {
				$text = '<a target="_blank" href="' . esc_url( $link ) . '">' . $text . '</a>';
			}
		} else {
			$text = __( 'Guest', 'wp-job-manager-claim-listing' );
		}
		$args = array(
			'label'         => __( 'Claimer', 'wp-job-manager-claim-listing' ),
			'description'   => '<span class="claimer-name">' . $text . ' <span id="edit-claimer" class="button button-small">Change</span></span>',
			'field_attr'    => array(
				'id'            => 'claimer-field',
			),
			'control_attr'  => array(
				'id'            => 'claimer-input',
				'name'          => 'post_author_override',
				'value'         => empty( $post_id ) ? $user_ID : $post->post_author,
				'type'          => 'number',
				'class'         => 'small-text',
				'style'         => 'display:none;',
			),
		);
		MetaBox::input_field( $args );

		/* == Claim Data == */
		$args = array(
			'label'         => __( 'Claim Information', 'wp-job-manager-claim-listing' ),
			'description'   => __( 'Data submitted by claimer to validate their claim.', 'wp-job-manager-claim-listing' ),
			'control_attr'  => array(
				'name'          => '_claim_data',
				'value'         => wp_kses_post( get_post_meta( $post_id, '_claim_data', true ) ),
			),
		);
		MetaBox::wp_editor_field( $args );

		/* Add nonce */
		wp_nonce_field( __FILE__ , MetaBox::nonce_id( $box['id'] ) );
	}


	/**
	 * Save Post Data
	 *
	 * @since 1.0.0
	 */
	function save_claim_data_meta_box( $post_id, $post ) {

		/* Verify save post */
		if ( ! MetaBox::verify_save_post( 'claim_data', __FILE__, $post_id, $post ) ) {
			return $post_id;
		}

		/* Stripslashes Submitted Data */
		$request = stripslashes_deep( $_POST );

		/* All fields data, except for "_status". */
		$fields = array(
			array(
				'key'  => '_listing_id',
				'data' => isset( $request['_listing_id'] ) ? intval( $request['_listing_id'] ) : '',
			),
			array(
				'key'  => '_user_id',
				'data' => isset( $request['post_author_override'] ) ? intval( $request['post_author_override'] ) : '',
			),
			array(
				'key'  => '_claim_data',
				'data' => isset( $request['_claim_data'] ) ? wp_kses_post( $request['_claim_data'] ) : '',
			),
		);
		foreach ( $fields as $args ) {
			MetaBox::save_post_meta( $post_id, $args['key'], $args['data'] );
		}

		/* Update Post title with Listing Title */
		if ( isset( $request['_listing_id'] ) && ! empty( $request['_listing_id'] ) ) {

			/* Listing Title */
			$listing_title = get_the_title( $request['_listing_id'] );
			$this_post = array(
				'ID'           => $post_id,
				'post_title' => sanitize_post_field( 'post_title', $listing_title, $post_id, 'db' ),
			);

			/**
			 * Prevent infinite loop.
			 *
			 * @link https://developer.wordpress.org/reference/functions/wp_update_post/
			 */
			remove_action( 'save_post', array( $this, 'save_claim_data_meta_box' ), 10 );
			wp_update_post( $this_post );
			add_action( 'save_post', array( $this, 'save_claim_data_meta_box' ), 10, 2 );
		}

		/* Status Update */
		if ( isset( $request['_status'] ) ) {

			$old_status = get_post_meta( $post_id, '_status', true );
			$new_status = Functions::sanitize_claim_status( $request['_status'] ); // always exists.

			/* If "old status" is not set, it's a new entry. */
			if ( ! $old_status ) {
				add_post_meta( $post_id, '_status', $new_status, true );

				/* Create new claim hook */
				do_action( 'wpjmcl_create_new_claim', $post_id, $context = 'admin' );
			} // End if().

			elseif ( $old_status != $new_status ) {

				/* Update status */
				$update = update_post_meta( $post_id, '_status', $new_status );

				/* Successfully update new status, fire hook! */
				if ( $update ) {
					do_action( 'wpjmcl_claim_status_updated', $post_id, $old_status, $request );
				}
			}
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
		wp_register_style( 'wpjmcl_claim_data_meta_box', URI . 'assets/meta-box-claim-data.css', array( 'fx-meta-box' ), VERSION );
		wp_register_script( 'wpjmcl_claim_data_meta_box', URI . 'assets/meta-box-claim-data.js', array( 'jquery' ), VERSION, true );

		/* Check post type */
		if ( 'claim' == $post_type && in_array( $hook_suffix, array( 'post-new.php', 'post.php' ) ) ) {
			wp_enqueue_style( 'wpjmcl_claim_data_meta_box' );
			wp_enqueue_script( 'wpjmcl_claim_data_meta_box' );
		}
	}


} // end class
