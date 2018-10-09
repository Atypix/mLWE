<?php
/**
 * Claim Post Type Admin Setup
 *
 * @since 3.0.0
 **/
namespace wpjmcl\claim;
if ( ! defined( 'WPINC' ) ) { die; }

/* Load Class */
Admin_Setup::get_instance();

/**
 * Admin Setup Class
 * This class handle all "claim" post type admin modification/setup.
 * For meta boxes. Check "meta-boxes.php".
 *
 * - Add Admin Menu Under "Job Listing"
 * - Manage Columns
 * - Custom Filter by Status Meta
 * - Post Updated Message
 * - Admin Scripts
 *
 * @since 3.0.0
 */
final class Admin_Setup {

	/**
	 * Construct
	 */
	public function __construct() {

		/*
		 Admin Menu
		------------------------------------------ */

		/* Add Admin Menu */
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		/* Parent Menu/Sub Menu Highlight Fix */
		add_filter( 'parent_file', array( $this, 'admin_menu_parent_file' ) );
		add_filter( 'submenu_file', array( $this, 'admin_menu_submenu_file' ) );

		/*
		 Manage Columns
		------------------------------------------ */

		/* Add Custom Columns */
		add_filter( 'manage_edit-claim_columns', array( $this, 'manage_columns' ) );
		add_action( 'manage_claim_posts_custom_column', array( $this, 'manage_custom_column' ), 10, 2 );

		/* Disable Quick Edit */
		add_filter( 'post_row_actions', array( $this, 'disable_quick_edit' ), 10, 2 );
		add_filter( 'bulk_actions-edit-claim', array( $this, 'disable_quick_edit_bulk' ) );

		/* Custom Dropdown Filter */
		add_action( 'restrict_manage_posts', array( $this, 'manage_column_filter_status_dropdown' ) );
		add_action( 'pre_get_posts', array( $this, 'manage_column_status_filter' ) );

		/*
		 Edit/Writing Screen
		------------------------------------------ */

		/* Updated message */
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );

		/* Redirect and verify new claim */
		add_action( 'admin_init', array( $this, 'check_new_claim' ) );

		/*
		 Scripts
		------------------------------------------ */

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


	/*
	 Admin Menu
	------------------------------------------ */

	/**
	 * Add Admin Menu
	 *
	 * @since 1.0.0
	 * @link https://shellcreeper.com/how-to-add-wordpress-cpt-admin-menu-as-sub-menu/
	 */
	function admin_menu() {

		/* CPT Object */
		$cpt = 'claim';
		$cpt_obj = get_post_type_object( $cpt );

		/* Sub Menu Page Under Job Listing */
		add_submenu_page(
			$parent_slug = 'edit.php?post_type=job_listing',
			$page_title  = $cpt_obj->labels->name,
			$menu_title  = $cpt_obj->labels->menu_name,
			$capability  = 'manage_options', // $cpt_obj->cap->edit_posts ??
			$menu_slug   = "edit.php?post_type={$cpt}"
		);
	}

	/**
	 * Fix active parent admin menu
	 *
	 * @since 3.0.0
	 */
	function admin_menu_parent_file( $parent_file ) {
		global $current_screen;
		if ( in_array( $current_screen->base, array( 'post', 'edit' ) ) && 'claim' == $current_screen->post_type ) {
			$parent_file = 'edit.php?post_type=job_listing';
		}
		return $parent_file;
	}

	/**
	 * Fix Admin Sub Menu File
	 *
	 * @since 3.0.0
	 */
	function admin_menu_submenu_file( $submenu_file ) {
		global $current_screen;
		if ( in_array( $current_screen->base, array( 'post', 'edit' ) ) && 'claim' == $current_screen->post_type ) {
			$submenu_file = 'edit.php?post_type=claim';
		}
		return $submenu_file;
	}


	/*
	 Manage Columns: Custom Columns
	------------------------------------------ */

	/**
	 * Manage Columns
	 *
	 * @since 3.0.0
	 */
	function manage_columns( $columns ) {

		$old_columns = $columns;
		$columns = array(
			'cb'       => $old_columns['cb'],
			'status'   => __( 'Status', 'wp-job-manager-claim-listing' ),
			'title'    => __( 'Claimed Listing', 'wp-job-manager-claim-listing' ),
			'author'   => __( 'Claimer', 'wp-job-manager-claim-listing' ),
			'date'     => $old_columns['date'],
		);
		return $columns;
	}

	/**
	 * Custom Columns
	 *
	 * Maybe to do:
	 * Custom "title" column (Listing),
	 * check WP/wp-admin/includes/class-wp-posts-list-table.php line 834
	 *
	 * @since 3.0.0
	 */
	function manage_custom_column( $column, $post_id ) {
		switch ( $column ) {
			case 'status' :
				$status   = get_post_meta( $post_id, '_status', true );
				$statuses = Functions::claim_statuses();
				?>
				<span class='status status-<?php echo sanitize_html_class( strtolower( $status ) ); ?>'>
					<?php echo isset( $statuses[ $status ] ) ? $statuses[ $status ] : __( 'Unknown', 'wp-job-manager-claim-listing' ); ?>
				</span>
				<?php
				break;
			default :
				break;
		}
		return $column;
	}

	/*
	 Manage Columns: Disable Quick Edit
	------------------------------------------ */

	/**
	 * Simplify By Disable Quick Edit
	 *
	 * @since 3.0.0
	 */
	function disable_quick_edit( $actions, $post ) {
		if ( 'claim' == $post->post_type ) {
			unset( $actions['inline hide-if-no-js'] );
		}
		return $actions;
	}

	/**
	 * Disable Bulk Action Quick Edit Option
	 *
	 * @since 3.0.0
	 */
	function disable_quick_edit_bulk( $bulk_actions ) {
		unset( $bulk_actions['edit'] );
		return $bulk_actions;
	}

	/*
	 Manage Columns: Custom Dropdown Filter
	------------------------------------------ */

	/**
	 * Filter Dropdown Based on Claim Status (Post Meta)
	 *
	 * @since 1.0.0
	 */
	function manage_column_filter_status_dropdown( $post_type ) {

		/* Bail early if not claim post type */
		if ( 'claim' !== $post_type ) { return;
		}

		/* Vars */
		$statuses = Functions::claim_statuses();
		$request = stripslashes_deep( $_GET );
		?>
		<select name='claim_status' id='dropdown_claim_status'>
			<option value=''><?php _e( 'All claim statuses', 'wp-job-manager-claim-listing' ); ?></option>

			<?php foreach ( $statuses as $key => $status ) { ?>

				<option value='<?php echo esc_attr( $key ); ?>' <?php selected( isset( $request['claim_status'] ) ? $request['claim_status'] : '', $key ); ?>><?php echo esc_html( $status ); ?></option>

			<?php } ?>

		</select><!-- #dropdown_claim_status -->
		<?php
	}

	/**
	 * Filter Claim Based On Status Selected
	 *
	 * @since 1.0.0
	 */
	function manage_column_status_filter( $query ) {

		/* Vars */
		global $hook_suffix, $post_type;
		$request = stripslashes_deep( $_GET );
		$statuses = Functions::claim_statuses();

		/* Only in Admin Edit Column Screen */
		if ( is_admin() && 'edit.php' == $hook_suffix && 'claim' == $post_type && $query->is_main_query() && isset( $request['claim_status'] ) && array_key_exists( $request['claim_status'], $statuses ) ) {

			/* Set simple meta query */
			$query->query_vars['meta_key']   = '_status';
			$query->query_vars['meta_value'] = esc_attr( $request['claim_status'] );
		}
	}


	/*
	 Edit Screen: Post Updated Message
	------------------------------------------ */

	/**
	 * Custom Updated Message
	 *
	 * @since 0.1.0
	 */
	function post_updated_messages( $messages ) {
		$post      = get_post();
		$post_type = 'claim';

		$messages[ $post_type ] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Claim updated.', 'wp-job-manager-claim-listing' ),
			2  => __( 'Custom field updated.', 'wp-job-manager-claim-listing' ),
			3  => __( 'Custom field deleted.', 'wp-job-manager-claim-listing' ),
			4  => __( 'Claim updated.', 'wp-job-manager-claim-listing' ),
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Claim restored to revision from %s', 'wp-job-manager-claim-listing' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Claim published.', 'wp-job-manager-claim-listing' ),
			7  => __( 'Claim saved.', 'wp-job-manager-claim-listing' ),
			8  => __( 'Claim submitted.', 'wp-job-manager-claim-listing' ),
			9  => sprintf(
				__( 'Claim scheduled for: <strong>%1$s</strong>.', 'wp-job-manager-claim-listing' ),
				date_i18n( __( 'M j, Y @ G:i', 'wp-job-manager-claim-listing' ), strtotime( $post->post_date ) )
			),
			10 => __( 'Claim draft updated.', 'wp-job-manager-claim-listing' ),
		);

		$return_to_claims_link = sprintf( ' <a href="%s">%s</a>', esc_url( admin_url( 'edit.php?post_type=claim' ) ), __( 'Return to claims', 'wp-job-manager-claim-listing' ) );

		$messages[ $post_type ][1]  .= $return_to_claims_link;
		$messages[ $post_type ][6]  .= $return_to_claims_link;
		$messages[ $post_type ][9]  .= $return_to_claims_link;
		$messages[ $post_type ][8]  .= $return_to_claims_link;
		$messages[ $post_type ][10] .= $return_to_claims_link;

		return $messages;
	}


	/**
	 * Check New Claim Request
	 */
	function check_new_claim() {
		global $pagenow;
		$claim_archive = add_query_arg( 'post_type', 'claim', admin_url( 'edit.php' ) );
		if ( 'post-new.php' == $pagenow && isset( $_GET['post_type'] ) && 'claim' == $_GET['post_type'] ) {
			if ( isset( $_GET['listing_id'] ) ) {
				$claimed = get_post_meta( $_GET['listing_id'], '_claimed', true );
				if ( $claimed ) {
					wp_redirect( esc_url_raw( $claim_archive ) );
					exit;
				}
			} else {
				wp_redirect( esc_url_raw( $claim_archive ) );
				exit;
			}
		}
	}


	/*
	 Admin Scripts
	------------------------------------------ */

	/**
	 * Admin Scripts
	 *
	 * @since 1.0.0
	 */
	function admin_scripts( $hook_suffix ) {
		global $post_type;

		/* Check post type */
		if ( 'claim' == $post_type ) {

			/* manage column */
			if ( in_array( $hook_suffix, array( 'edit.php' ) ) ) {
				wp_enqueue_style( 'wpjmcl_claim_cpt_admin_columns', URI . 'assets/admin-columns.css', array(), VERSION );
			}
			/* edit screen */
			if ( in_array( $hook_suffix, array( 'post-new.php', 'post.php' ) ) ) {
				wp_enqueue_style( 'wpjmcl_claim_cpt_admin_edit', URI . 'assets/admin-edit.css', array(), VERSION );
			}
		}
	}

} // end class
