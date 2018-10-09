<?php
// exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Knowhere_Job_Views_Counter_Settings class.
 * 
 * @class Knowhere_Job_Views_Counter_Settings
 */
class Knowhere_Knowhere_Job_Views_Counter_Settings {

	private $tabs;
	private $choices;
	private $modes;
	private $time_types;
	private $groups;
	private $user_roles;
	private $positions;
	private $display_styles;
	public $post_types;
	public $page_types;

	public function __construct() {
		// actions
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu_options' ) );
		add_action( 'after_setup_theme', array( $this, 'load_defaults' ) );
	}

	/**
	 * Load default settings.
	 */
	public function load_defaults() {

		if ( ! is_admin() )
			return;

		$this->modes = array(
			'php'		=> __( 'PHP', 'knowherepro' ),
			'js'		=> __( 'JavaScript', 'knowherepro' )
		);
		
		if ( function_exists( 'register_rest_route' ) ) {
			$this->modes['rest_api'] = __( 'REST API', 'knowherepro' );
		}

		$this->time_types = array(
			'minutes'	 => __( 'minutes', 'knowherepro' ),
			'hours'		 => __( 'hours', 'knowherepro' ),
			'days'		 => __( 'days', 'knowherepro' ),
			'weeks'		 => __( 'weeks', 'knowherepro' ),
			'months'	 => __( 'months', 'knowherepro' ),
			'years'		 => __( 'years', 'knowherepro' )
		);

		$this->groups = array(
			'robots' => __( 'robots', 'knowherepro' ),
			'users'	 => __( 'logged in users', 'knowherepro' ),
			'guests' => __( 'guests', 'knowherepro' ),
			'roles'	 => __( 'selected user roles', 'knowherepro' )
		);

		$this->positions = array(
			'before' => __( 'before the content', 'knowherepro' ),
			'after'	 => __( 'after the content', 'knowherepro' ),
			'manual' => __( 'manual', 'knowherepro' )
		);

		$this->display_styles = array(
			'icon'	 => __( 'icon', 'knowherepro' ),
			'text'	 => __( 'label', 'knowherepro' )
		);

		$this->tabs = array(
			'general'	 => array(
				'name'	 => __( 'General', 'knowherepro' ),
				'key'	 => 'kw_post_views_counter_settings_general',
				'submit' => 'save_pvc_general',
				'reset'	 => 'reset_pvc_general'
			),
//			'display'	 => array(
//				'name'	 => __( 'Display', 'knowherepro' ),
//				'key'	 => 'post_views_counter_settings_display',
//				'submit' => 'save_pvc_display',
//				'reset'	 => 'reset_pvc_display'
//			)
		);

		$this->user_roles = $this->get_user_roles();

		$this->page_types = apply_filters( 'pvc_page_types_display_options', array(
			'home'		 => __( 'Home', 'knowherepro' ),
			'archive'	 => __( 'Archives', 'knowherepro' ),
			'singular'	 => __( 'Single pages', 'knowherepro' ),
			'search'	 => __( 'Search results', 'knowherepro' ),
		) );
	}

	/**
	 * Get all user roles.
	 * 
	 * @global object $wp_roles
	 */
	public function get_user_roles() {
		global $wp_roles;

		$roles = array();

		foreach ( apply_filters( 'editable_roles', $wp_roles->roles ) as $role => $details ) {
			$roles[$role] = translate_user_role( $details['name'] );
		}

		asort( $roles, SORT_STRING );

		return $roles;
	}

	/**
	 * Add options page.
	 */
	public function admin_menu_options() {
		add_options_page(
		__( 'Listing Views Counter', 'knowherepro' ), __( 'Listing Views Counter', 'knowherepro' ), 'manage_options', 'knowhere', array( $this, 'options_page' )
		);
	}

	/**
	 * Options page callback.
	 * 
	 * @return mixed
	 */
	public function options_page() {
		$tab_key = (isset( $_GET['tab'] ) ? esc_attr( $_GET['tab'] ) : 'general');

		echo '
		<h2>' . __( 'Listing Views Counter', 'knowherepro' ) . '</h2>
		<div class="post-views-counter-settings">
		    <form action="options.php" method="post">';

		wp_nonce_field( 'update-options' );
		settings_fields( $this->tabs[$tab_key]['key'] );
		do_settings_sections( $this->tabs[$tab_key]['key'] );

		echo '
			<p class="submit">';

			submit_button( '', 'primary', $this->tabs[$tab_key]['submit'], false );
		echo ' ';

//		submit_button( __( 'Reset to defaults', 'knowherepro' ), 'secondary reset_pvc_settings', $this->tabs[$tab_key]['reset'], false );

		echo '
			</p>
		    </form>
		</div>
	    <div class="clear"></div>
	    </div>';
	}

	/**
	 * Register settings callback.
	 */
	public function register_settings() {
		// general options
		register_setting( 'kw_post_views_counter_settings_general', 'kw_post_views_counter_settings_general', array( $this, 'validate_settings' ) );
		add_settings_section( 'kw_post_views_counter_settings_general', __( 'General settings', 'knowherepro' ), '', 'kw_post_views_counter_settings_general' );
//		add_settings_field( 'pvc_time_between_counts', __( 'Count Interval', 'knowherepro' ), array( $this, 'time_between_counts' ), 'post_views_counter_settings_general', 'post_views_counter_settings_general' );
//		add_settings_field( 'kw_pvc_reset_counts', __( 'Reset Data Interval', 'knowherepro' ), array( $this, 'reset_counts' ), 'kw_post_views_counter_settings_general', 'kw_post_views_counter_settings_general' );
		add_settings_field( 'kw_pvc_deactivation_delete', __( 'Deactivation', 'knowherepro' ), array( $this, 'deactivation_delete' ), 'kw_post_views_counter_settings_general', 'kw_post_views_counter_settings_general' );
	}

	/**
	 * Time between counts option.
	 */
	public function time_between_counts() {
		echo '
	<div id="pvc_time_between_counts">
	    <input size="4" type="text" name="post_views_counter_settings_general[time_between_counts][number]" value="' . esc_attr( Knowhere_Job_Views_Counter()->options['general']['time_between_counts']['number'] ) . '" />
	    <select class="pvc-chosen-short" name="post_views_counter_settings_general[time_between_counts][type]">';

		foreach ( $this->time_types as $type => $type_name ) {
			echo '
		<option value="' . esc_attr( $type ) . '" ' . selected( $type, Knowhere_Job_Views_Counter()->options['general']['time_between_counts']['type'], false ) . '>' . esc_html( $type_name ) . '</option>';
		}

		echo '
	    </select>
	    <p class="description">' . __( 'Enter the time between single user visit count.', 'knowherepro' ) . '</p>
	</div>';
	}

	/**
	 * Reset counts option.
	 */
	public function reset_counts() {
		echo '
	<div id="kw_pvc_reset_counts">
	    <input size="4" type="text" name="kw_post_views_counter_settings_general[reset_counts][number]" value="' . esc_attr( Knowhere_Job_Views_Counter()->options['general']['reset_counts']['number'] ) . '" />
	    <select class="pvc-chosen-short" name="kw_post_views_counter_settings_general[reset_counts][type]">';

		foreach ( array_slice( $this->time_types, 2, null, true ) as $type => $type_name ) {
			echo '
		<option value="' . esc_attr( $type ) . '" ' . selected( $type, Knowhere_Job_Views_Counter()->options['general']['reset_counts']['type'], false ) . '>' . esc_html( $type_name ) . '</option>';
		}

		echo '
	    </select>
	    <p class="description">' . __( 'Delete single day post views data older than specified above. Enter 0 (number zero) if you want to preserve your data regardless of its age.', 'knowherepro' ) . '</p>
	</div>';
	}


	/**
	 * Plugin deactivation option.
	 */
	public function deactivation_delete() {
		echo '
	<div id="kw_pvc_deactivation_delete">
	    <label class="cb-checkbox"><input type="checkbox" name="kw_post_views_counter_settings_general[deactivation_delete]" value="1" ' . checked( true, Knowhere_Job_Views_Counter()->options['general']['deactivation_delete'], false ) . ' />' . __( 'Enable to delete all plugin data on deactivation.', 'knowherepro' ) . '</label>
	</div>';
	}


	/**
	 * Validate general settings.
	 */
	public function validate_settings( $input ) {
		if ( isset( $_POST['save_pvc_general'] ) ) {

			// counter mode
//			$input['counter_mode'] = isset( $input['counter_mode'], $this->modes[$input['counter_mode']] ) ? $input['counter_mode'] : Knowhere_Job_Views_Counter()->defaults['general']['counter_mode'];

			// post views column
//			$input['post_views_column'] = isset( $input['post_views_column'] );

			// time between counts
			$input['time_between_counts']['number'] = (int) ( isset( $input['time_between_counts']['number'] ) ? $input['time_between_counts']['number'] : Knowhere_Job_Views_Counter()->defaults['general']['time_between_counts']['number'] );
			$input['time_between_counts']['type'] = isset( $input['time_between_counts']['type'], $this->time_types[$input['time_between_counts']['type']] ) ? $input['time_between_counts']['type'] : Knowhere_Job_Views_Counter()->defaults['general']['time_between_counts']['type'];

			// flush interval
//			$input['flush_interval']['number'] = (int) ( isset( $input['flush_interval']['number'] ) ? $input['flush_interval']['number'] : Knowhere_Job_Views_Counter()->defaults['general']['flush_interval']['number'] );
//			$input['flush_interval']['type'] = isset( $input['flush_interval']['type'], $this->time_types[$input['flush_interval']['type']] ) ? $input['flush_interval']['type'] : Knowhere_Job_Views_Counter()->defaults['general']['flush_interval']['type'];

			// Since the settings are about to be saved and cache flush interval could've changed,
			// we want to make sure that any changes done on the settings page are in effect immediately
			// (instead of having to wait for the previous schedule to occur).
			// We achieve that by making sure to clear any previous cache flush schedules and
			// schedule the new one if the specified interval is > 0
//			Knowhere_Job_Views_Counter()->remove_cache_flush();

//			if ( $input['flush_interval']['number'] > 0 ) {
//				Knowhere_Job_Views_Counter()->schedule_cache_flush();
//			}

			// reset counts
//			$input['reset_counts']['number'] = (int) ( isset( $input['reset_counts']['number'] ) ? $input['reset_counts']['number'] : Knowhere_Job_Views_Counter()->defaults['general']['reset_counts']['number'] );
//			$input['reset_counts']['type'] = isset( $input['reset_counts']['type'], $this->time_types[$input['reset_counts']['type']] ) ? $input['reset_counts']['type'] : Knowhere_Job_Views_Counter()->defaults['general']['reset_counts']['type'];

			// run cron on next visit?
//			$input['cron_run'] = ($input['reset_counts']['number'] > 0 ? true : false);
//			$input['cron_update'] = ($input['cron_run'] && (Knowhere_Job_Views_Counter()->options['general']['reset_counts']['number'] !== $input['reset_counts']['number'] || Knowhere_Job_Views_Counter()->options['general']['reset_counts']['type'] !== $input['reset_counts']['type']) ? true : false);

			// exclude
//			if ( isset( $input['exclude']['groups'] ) ) {
//				$groups = array();
//
//				foreach ( $input['exclude']['groups'] as $group => $set ) {
//					if ( isset( $this->groups[$group] ) )
//						$groups[] = $group;
//				}
//
//				$input['exclude']['groups'] = array_unique( $groups );
//			} else {
//				$input['exclude']['groups'] = array();
//			}

//			if ( in_array( 'roles', $input['exclude']['groups'], true ) && isset( $input['exclude']['roles'] ) ) {
//				$roles = array();
//
//				foreach ( $input['exclude']['roles'] as $role => $set ) {
//					if ( isset( $this->user_roles[$role] ) )
//						$roles[] = $role;
//				}
//
//				$input['exclude']['roles'] = array_unique( $roles );
//			} else
//				$input['exclude']['roles'] = array();

			// exclude ips
//			if ( isset( $input['exclude_ips'] ) ) {
//				$ips = array();
//
//				foreach ( $input['exclude_ips'] as $ip ) {
//					if ( strpos( $ip, '*' ) !== false ) {
//						$new_ip = str_replace( '*', '0', $ip );
//
//						if ( filter_var( $new_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) )
//							$ips[] = $ip;
//					} elseif ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) )
//						$ips[] = $ip;
//				}
//
//				$input['exclude_ips'] = array_unique( $ips );
//			}

			// restrict edit viewa
//			$input['restrict_edit_views'] = isset( $input['restrict_edit_views'] ) ? $input['restrict_edit_views'] : Knowhere_Job_Views_Counter()->defaults['general']['restrict_edit_views'];

			// deactivation delete
			$input['deactivation_delete'] = isset( $input['deactivation_delete'] );
		} elseif ( isset( $_POST['reset_pvc_general'] ) ) {
			$input = Knowhere_Job_Views_Counter()->defaults['general'];

			add_settings_error( 'reset_general_settings', 'settings_reset', __( 'General settings restored to defaults.', 'knowherepro' ), 'updated' );
		}

		return $input;
	}

}
