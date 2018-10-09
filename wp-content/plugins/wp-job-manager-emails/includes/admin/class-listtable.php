<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Emails_Admin_ListTable {

	protected $new_columns;

	/**
	 * @type WP_Job_Manager_Emails_CPT
	 */
	protected $cpt;

	/**
	 * WP_Job_Manager_Emails_Admin_ListTable constructor.
	 *
	 */
	public function __construct( $cpt ) {
		$this->cpt = $cpt;

		$this->new_columns = array(
				'cb'      => "<input type=\"checkbox\" />",
				'title'   => __( 'Title', 'wp-job-manager-emails' ),
				'status' => __( 'Status', 'wp-job-manager-emails' ),
				'to' => __( 'To', 'wp-job-manager-emails' ),
				'subject' => __( 'Subject', 'wp-job-manager-emails' ),
				'hook' => __( 'Hook', 'wp-job-manager-emails' ),
				'delay' => __( 'Delay', 'wp-job-manager-emails' ),
				'actions' => __( 'Manage', 'wp-job-manager-emails' ),
				'date'    => __( 'Last Updated', 'wp-job-manager-emails' ),
		);

		$this->additional_columns();

		$post_type = $this->cpt()->get_post_type();
		if( ! $post_type ) return;

		add_filter( "manage_{$post_type}_posts_columns", array( $this, 'add_columns' ) );
		add_filter( "views_edit-{$post_type}", array( $this, 'update_views' ) );
		add_filter( 'default_hidden_columns', array( $this, 'default_hidden' ), 10, 2 );

		add_action( "manage_{$post_type}_posts_custom_column", array( $this, 'column_values' ), 10, 2 );
		add_action( "manage_edit-{$post_type}_sortable_columns", array( $this, 'sortable_columns' ), 10 );
		add_action( 'pre_get_posts', array( $this, 'sort_values' ), 1 );

		add_action( 'admin_init', array($this, 'disable_email') );
		add_action( 'admin_init', array($this, 'enable_email') );
		add_action( 'all_admin_notices', array($this, 'disabled_notice') );
		add_action( 'all_admin_notices', array($this, 'enabled_notice') );
	}

	function default_hidden( $hidden, $screen ){

		if( $screen && $screen->id && $screen->post_type === $this->cpt()->get_post_type() ){
			if( ! in_array( 'delay', $hidden ) ){
				$hidden[] = 'delay';
			}
		}

		return $hidden;
	}

	/**
	 * Handle List Table Sorting
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $query
	 */
	function sort_values( $query ){

		if( ! $query->is_main_query() || ! ( $orderby = $query->get( 'orderby' ) ) ) return;

		$string_orderby = array(
			'hook' => 'hook',
		);

		$num_orderby = array(
			'delay' => 'delay_send_hours',
		);

		if( $orderby === 'status' ) $query->set( 'orderby', 'post_status' );

		// Handle string value sorting
		if( in_array( $orderby, $string_orderby ) ){
			// set our query's meta_key, which is used for custom fields
			$query->set( 'meta_key', $string_orderby[ $orderby ] );

			/**
			 * Use 'meta_value_num' instead for numeric values. You may also specify 'meta_type' if you want to cast the meta value as a specific type.
			 * Possible values are 'NUMERIC', 'BINARY', 'CHAR', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED', same as in '$meta_query'.
			 * When using 'meta_type' you can also use meta_value_* accordingly. For example, when using DATETIME as 'meta_type' you can use 'meta_value_datetime'
			 * to define order structure.
			 *
			 * @see https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters
			 */
			$query->set( 'orderby', 'meta_value' );
		}

		// Handle number value sorting
		if ( in_array( $orderby, $num_orderby ) ) {
			// set our query's meta_key, which is used for custom fields
			$query->set( 'meta_key', $num_orderby[ $orderby ] );

			/**
			 * Use 'meta_value_num' instead for numeric values. You may also specify 'meta_type' if you want to cast the meta value as a specific type.
			 * Possible values are 'NUMERIC', 'BINARY', 'CHAR', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED', same as in '$meta_query'.
			 * When using 'meta_type' you can also use meta_value_* accordingly. For example, when using DATETIME as 'meta_type' you can use 'meta_value_datetime'
			 * to define order structure.
			 *
			 * @see https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters
			 */
			$query->set( 'orderby', 'meta_value_num' );
		}
	}

	/**
	 * Set Sortable Columns
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $sortable
	 *
	 * @return array
	 */
	function sortable_columns( $sortable ){

		$my_sortable = array(
			'status' => 'status',
			'hook' => 'hook',
			'delay' => 'delay'
		);

		$sortable = array_merge( $sortable, $my_sortable );

		return $sortable;

	}

	/**
	 * Update List Table Views
	 *
	 * This method is used to update the wording for the list table views, changing the `Published`
	 * wording to say `Enabled`
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $views
	 *
	 * @return mixed
	 */
	function update_views( $views ){

		if( ! array_key_exists( 'publish', $views ) ) return $views;

		$views['publish'] = str_replace( 'Published', __( 'Enabled', 'wp-job-manager-emails' ), $views['publish'] );

		return $views;
	}

	/**
	 * Output column values
	 *
	 * This method will check for {$column}_column in extending class
	 * and will call it to output value in list table column.
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $column
	 * @param $post_id
	 */
	function column_values( $column, $post_id ) {

		$slug = $this->cpt()->get_slug();

		if( ! array_key_exists( $column, $this->new_columns ) ) return;
		do_action( "jme_{$slug}_listtable_before_column_values", $column, $post_id );

		if( ! empty($column) && method_exists( $this, "{$column}_column" ) ) {
			call_user_func( array($this, "{$column}_column"), $post_id );
		} else {
			$check_meta = get_post_meta( $post_id, $column, true);
			if( ! empty( $check_meta ) ) echo $check_meta;
		}

		do_action( "jme_{$slug}_listtable_after_column_values", $column, $post_id );

	}

	function delay_column( $post_id ){

		$delay = get_post_meta( $post_id, 'delay_send_hours', true );
		$delay = empty( $delay ) ? 'n/a' : $delay;

		echo '<div style="width: 5.5em; text-align: center;">' . $delay . '</div>';

	}

	/**
	 * Add custom columns to list table
	 *
	 * Checks if $this->new_columns has all of the columns (keys) present in $columns
	 * and returns only $this->new_columns to allow for ordering of columns.  Otherwise
	 * method will merge arrays which puts all new columns at end of array.
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $columns
	 *
	 * @return array
	 */
	function add_columns( $columns ) {

		$diff = array_diff_key( $columns, $this->new_columns );
		if( empty( $diff ) ) return $this->new_columns;

		$columns = array_merge( $columns, $this->new_columns );

		return $columns;
	}

	/**
	 * Status Icon Column Output
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $post_id
	 */
	function status_column( $post_id ){

		$post_status = get_post_status( $post_id );

		switch( $post_status ){
			case 'publish':
				?>
				<div style="width: 5.5em; text-align: center;"><span class="" data-tooltip="<?php _e( 'Enabled', 'wp-job-manager-emails' ); ?>"><i class="check circle icon green large" data-variation="mini"></i></span></div>
				<?php
				break;
			default:
				?>
				<div style="width: 5.5em; text-align: center;"><span class="" data-tooltip="<?php _e( 'Disabled', 'wp-job-manager-emails' ); ?>"><i class="remove circle icon red large" data-variation="mini"></i></span></div>
				<?php
				break;
		}

	}

	/**
	 * Action Buttons Output
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $post_id
	 */
	function actions_column( $post_id ) {

		?>
		<div class="ui small basic right floated buttons">
			<a href="<?php echo get_edit_post_link( $post_id ); ?>" class="ui compact icon button" data-variation="mini" style="height: auto;" data-tooltip="<?php _e( 'Edit', 'wp-job-manager-emails' ); ?>">
				<i class="configure icon"></i>
			</a>
			<?php
				$post_status = get_post_status( $post_id );
				if( $post_status === 'publish' ):
					?>
					<a href="<?php echo remove_query_arg( array('disabled_emails', 'enabled_email'), wp_nonce_url( add_query_arg( 'disable_email', $post_id ), 'disable_email' ) ); ?>" class="ui compact icon button" style="height: auto;" data-variation="mini" data-tooltip="<?php _e( 'Disable', 'wp-job-manager-emails' ); ?>">
						<i class="toggle off icon"></i>
					</a>
					<?php
				elseif( $post_status === 'disabled' ):
				?>
					<a href="<?php echo remove_query_arg( array( 'disabled_emails', 'enabled_email' ) , wp_nonce_url( add_query_arg( 'enable_email', $post_id ), 'enable_email' ) ); ?>" class="ui compact icon button" style="height: auto;" data-variation="mini" data-tooltip="<?php _e( 'Enable', 'wp-job-manager-emails' ); ?>">
						<i class="toggle on icon"></i>
					</a>
				<?php
				endif;
			?>
			<a href="<?php echo get_delete_post_link( $post_id ); ?>" class="ui icon compact button" data-variation="mini" style="height: auto;" data-tooltip="<?php _e( 'Delete', 'wp-job-manager-emails' ); ?>">
				<i class="trash icon"></i>
			</a>
		</div>
		<?php

	}

	/**
	 * Show Disabled Notice in Admin
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	public function disabled_notice() {

		global $post_type, $pagenow;

		if( $pagenow == 'edit.php' && $post_type == $this->cpt()->post_type && ! empty($_REQUEST['disabled_emails'] ) ) {
			$disabled_emails = $_REQUEST['disabled_emails'];
			$msg_type = 'updated';

			if( is_array( $disabled_emails ) ) {
				$disabled_emails = array_map( 'absint', $disabled_emails );
				$titles        = array();
				foreach( $disabled_emails as $job_id ) {
					$titles[] = get_the_title( $job_id );
				}
				$msg = sprintf( __( '%s email has been disabled', 'wp-job-manager-emails' ), '&quot;' . implode( '&quot;, &quot;', $titles ) . '&quot;' );
			} else {
				$msg = sprintf( __( '%s email has been disabled', 'wp-job-manager-emails' ), '&quot;' . get_the_title( $disabled_emails ) . '&quot;' );
			}

			if( ! empty( $_REQUEST['enabled_email'] ) ){
				$msg_type = 'error';
				$msg .=  ' ' . __( 'as you activated another email template with the same email/hook action.', 'wp-job-manager-emails' );
			}

			echo "<div class=\"{$msg_type}\"><p>" . $msg . '</p></div>';
		}
	}

	/**
	 * Show Enabled Notice in Admin
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	public function enabled_notice() {

		global $post_type, $pagenow;

		if( $pagenow == 'edit.php' && $post_type == $this->cpt()->post_type && ! empty($_REQUEST['enabled_email']) ) {
			$enabled_email = $_REQUEST['enabled_email'];
			echo '<div class="updated"><p>' . sprintf( __( '%s email has been enabled, and will now be used for emails.', 'wp-job-manager-emails' ), '&quot;' . get_the_title( $enabled_email ) . '&quot;' ) . '</p></div>';
		}
	}

	/**
	 * Enable Email Template (and Disable others)
	 *
	 * This method will enable the email template specified in $_GET['enable_email'] and will check for any other email
	 * templates that may be active with the same hook/action.  If other templates are found with same hook/action, they
	 * will be disabled and the template passed will be enabled.
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	public function enable_email() {
		global $post_type;

		if( empty( $post_type ) && ! empty( $_REQUEST['post_type'] ) ) {
			$post_type = sanitize_text_field( $_REQUEST['post_type'] );
		}

		if( ! empty($_GET['enable_email']) && $post_type == $this->cpt()->post_type && wp_verify_nonce( $_REQUEST['_wpnonce'], 'enable_email' ) ) {

			$post_id  = absint( $_GET['enable_email'] );

			if( $this->cpt()->post_type !== $post_type ) {
				return;
			}

			$job_data = array(
					'ID'          => $post_id,
					'post_status' => 'publish'
			);

			wp_update_post( $job_data );

			wp_redirect( remove_query_arg( 'enable_email', add_query_arg( array( 'enabled_email' => $post_id ) ) ) );
			exit;
		}
	}

	/**
	 * Disable Email Template
	 *
	 * This method looks for $_GET['disable_email'] and will disable an array or single
	 * email template as specified in the URL
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	public function disable_email() {
		global $post_type;

		if( empty($post_type) && ! empty($_REQUEST['post_type']) ) $post_type = sanitize_text_field( $_REQUEST['post_type'] );

		if( ! empty($_GET['disable_email']) && $post_type == $this->cpt()->post_type && wp_verify_nonce( $_REQUEST['_wpnonce'], 'disable_email' ) ) {
			$post_id  = absint( $_GET['disable_email'] );
			$job_data = array(
					'ID'          => $post_id,
					'post_status' => 'disabled'
			);
			wp_update_post( $job_data );
			wp_redirect( remove_query_arg( 'disable_email', add_query_arg( 'disabled_emails', $post_id ) ) );
			exit;
		}
	}

	/**
	 * Additional Columns Placeholder
	 *
	 * Just a placeholder for any additional columns that should be
	 * added by class extending this one.
	 *
	 * @since 1.0.0
	 *
	 */
	function additional_columns(){}

	/**
	 * WP_Job_Manager_Emails_CPT
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_Job_Manager_Emails_CPT
	 */
	function cpt(){
		return $this->cpt;
	}
}