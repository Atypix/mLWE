<?php
/**
 * Job Listing Admin Setup
 *
 * @since 3.0.0
 */
namespace wpjmcl\job_listing;

if ( ! defined( 'WPINC' ) ) { die; }

/* Load Class */
Admin_Setup::get_instance();

/**
 * Setup Job Listing Post Type
 */
final class Admin_Setup {

	/**
	 * Construct
	 */
	public function __construct() {

		/* Add column */
		add_filter( 'manage_edit-job_listing_columns', array( $this, 'columns' ), 20 );
		add_action( 'manage_job_listing_posts_custom_column', array( $this, 'custom_columns' ), 2 );

		/* Enqueue Scripts */
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
	 * Column
	 */
	function columns( $columns ) {
		if ( ! is_array( $columns ) ) {
			$columns = array();
		}
		$columns['wpjmcl_claimed'] = '<span class="tips" data-tip="' . __( 'Claimed?', 'wp-job-manager-claim-listing' ) . '">' . __( 'Claimed?', 'wp-job-manager-claim-listing' ) . '</span>';
		return $columns;
	}

	/**
	 * Custom Column
	 */
	function custom_columns( $column ) {
		global $post;
		$post_id = $post->ID;
		switch ( $column ) {
			case 'wpjmcl_claimed' :
				$claimed = get_post_meta( $post_id, '_claimed', true );
				if ( $claimed ) {
					echo '<span data-tip="' . __( 'Verified listing', 'wp-job-manager-claim-listing' ) . '" class="tips listing_claimed">' . __( 'Verified listing', 'wp-job-manager-claim-listing' ) . '</span>';
				} else {

					$action_url = add_query_arg( array(
						'post_type'    => 'claim',
						'listing_id'   => $post_id,
					), admin_url( 'post-new.php' ) );
					$action = 'add_claim';
					$action_name = 'Create Claim';
					printf( '<a class="button button-icon tips icon-%1$s" target="_blank" href="%2$s" data-tip="%3$s">%4$s</a>', $action, esc_url( $action_url ), esc_attr( $action_name ), esc_html( $action_name ) );
				}
			break;
		}
	}


	/**
	 * Admin Scripts
	 *
	 * @since 1.0.0
	 */
	function admin_scripts( $hook_suffix ) {
		global $post_type;

		/* Check post type */
		if ( 'job_listing' == $post_type && 'edit.php' == $hook_suffix ) {
			wp_enqueue_style( 'wpjmcl_job_listing_cpt_admin_columns', URI . 'assets/admin-columns.css', array(), VERSION );
		}
	}
}
