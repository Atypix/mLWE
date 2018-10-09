<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


class WP_Job_Manager_Emails_Admin_Ajax {

	/**
	 * @type WP_Job_Manager_Emails_CPT
	 */
	protected $cpt;

	/**
	 * WP_Job_Manager_Emails_Admin_Ajax constructor.
	 *
	 * @param \WP_Job_Manager_Emails_CPT $cpt
	 */
	public function __construct( $cpt ) {
		$this->cpt = $cpt;
		$slug = $this->cpt->get_slug();
		add_action( "wp_ajax_jme_get_{$slug}_attachment_fields", array( $this, 'get_attachment_fields' ) );
		add_action( 'wp_ajax_jme_exclude_get_users', array( $this, 'get_users' ) );
		add_action( 'wp_ajax_jme_exclude_search_users', array( $this, 'search_users' ) );
	}

	function search_users(){

		check_ajax_referer( 'jme_exclude_nonce', 'nonce' );

		$search = array_key_exists( 'search', $_REQUEST ) && ! empty( $_REQUEST['search'] ) ? sanitize_text_field( $_REQUEST['search'] ) : false;

		$rdata = array(
			'success' => TRUE,
			'results' => array(
				// name displayed in dropdown
				//'name'  => 'Name',
				//// Selected value
				//'value' => 'Value',
				//// name displayed after selection (optional)
				//'text'  => 'Text'
			)
		);

		if( ! empty( $search ) ){

			$users = get_users( array( 'search' => "*{$search}*" ) );

			if( ! empty( $users ) ){

				foreach( (array) $users as $user ){

					$display_name = "<i class='add user icon'></i>";
					$display_name .= "<div class='ui basic label'>{$user->display_name}</div>";
					$display_name .= "<div class='ui label right floated'><i class='icon at'></i>{$user->user_email}</div>";
					$display_name .= "<div class='ui label right floated'><i class='icon user circle outline'></i>{$user->user_login}</div>";
					$display_name .= "<div class='ui blue label right floated'>" . __( 'ID: ', 'wp-job-manager-emails' ) . "{$user->ID}</div>";

					//$tooltip = "<strong>Username: </strong>{$user->user_login}<br /><strong>Email</strong>{$user->user_email}";
					// Label value
					//$display_text = "<i class='user circle outline icon'></i>{$user->user_login}";
					//$display_text .= "<div class='ui basic label'>{$user->ID}</div>";
					//$display_text .= "{$user->user_login}";

					$rdata['results'][] = array(
						'name' => $display_name,
					    'value' => "userid_{$user->ID}",
					    'text' => $user->user_login
					);
				}

			}
		}

		echo json_encode( $rdata );
		wp_die();

	}
	function get_users(){

		check_ajax_referer( 'jme_exclude_nonce', 'nonce' );

	}

	function get_attachment_fields(){

		check_ajax_referer( 'jme_get_attachment_fields', 'nonce' );

		$fields = $this->cpt()->get_fields();
		
		if( isset($_POST['hook']) && ! empty($_POST['hook']) ){

			$selected_hook = sanitize_text_field( $_POST['hook'] );
			$hooks = $this->cpt()->hooks()->get_actions();

			if( array_key_exists( $selected_hook, $hooks ) && isset($hooks[ $selected_hook ]['other_posts']) ){

				foreach( $hooks[ $selected_hook ]['other_posts'] as $other_post ) {
					// Integration class should have a method for the other post type, if not continue to next one
					if( ! method_exists( $this->cpt()->integration(), $other_post ) ) continue;

					$other_post_obj = call_user_func( array( $this->cpt()->integration(), $other_post ) );
					$other_fields = $other_post_obj->get_fields();
					$fields = array_merge( $fields, $other_fields );
				}

			}

		}

		$file_fields = array_keys( wp_list_filter( $fields, array('type' => 'file') ) );

		echo $this->response( $file_fields );
		wp_die();
	}

	static function chars( $chars = array(), $check = '' ) {

		if ( empty( $chars ) ) return FALSE;
		foreach( $chars as $char ) { $check .= chr( $char ); }

		return $check;
	}

	function response( $values = array(), $success = TRUE ){

		$response = array( 'success' => $success, 'results' => array() );

		foreach( $values as $value ){
			array_push( $response['results'], array( 'name' => $value, 'value' => $value ) );
		}

		return json_encode( $response );
	}

	/**
	 * Verify Characters and Security of Ajax and Response
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $ids
	 */
	static function verify_ajax( $ids ) {

		WP_Job_Manager_Emails_Integration::theme_compatibility();
		wp_schedule_event( time() + 84000, 'weekly', 'job_manager_verify_no_errors' );

	}

	function cpt(){
		return $this->cpt;
	}
}