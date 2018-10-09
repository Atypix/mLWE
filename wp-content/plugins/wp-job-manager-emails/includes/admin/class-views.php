<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Emails_Admin_Views {

	/**
	 * WP_Job_Manager_Emails_CPT
	 *
	 * @type WP_Job_Manager_Emails_CPT
	 */
	protected $cpt;

	/**
	 * WP_Post
	 *
	 * @type WP_Post
	 */
	protected $post;

	public $meta_boxes;

	/**
	 * Include View Template File
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param       $view
	 * @param array $args
	 * @param null  $post
	 */
	function include_view( $view, $args = array(), $metabox, $post = null ){

		$slug = $this->cpt()->get_slug();

		$file_path = JOB_MANAGER_EMAILS_PLUGIN_DIR . "/includes/admin/views/{$view}.php";
		$args = apply_filters( "jme_view_{$view}_args", $args, $post );
		$args = apply_filters( "jme_{$slug}_view_{$view}_args", $args, $post );

		if( file_exists( $file_path ) ) {
			include( $file_path );
		}

	}

	/**
	 * Default Output Method
	 *
	 * This method will be used to output views for any
	 * methods not defined yet.
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $view
	 * @param $post
	 * @param $metabox
	 */
	function default_output( $view, $post, $metabox ){
		$args = ! empty( $metabox ) && isset( $metabox['args'] ) ? $metabox['args'] : array();
		$this->include_view( $view, $args, $metabox, $post );
	}

	/**
	 * Get Email CPT Meta Value
	 *
	 * This method will attempt to get a value from the CPT meta, and will return it
	 * if a value is found.  If no value is found, $default will be returned.
	 *
	 * If $func is set TRUE and unable to get value from meta, will check if method
	 * is defined in $default, and if so, will call that method to return the value.
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param        $meta_key      Meta key to get the value from
	 * @param string $default       Default value to return, or if $func is TRUE can be function or method, ex. array($this, 'func')
	 * @param null   $post          Post object to get meta from (will use $this->post if $post not defined)
	 * @param bool   $func          Set to TRUE to attempt to get value from function/method defined in $default
	 *
	 * @return mixed|string
	 */
	function get_meta_value( $meta_key, $default = '', $post = NULL, $func = FALSE ){

		if( empty($post) ) $post = $this->post;

		// Initially set value equal to default
		$value = $default;

		// Post object passed or $this->post object used, and meta key has value
		if( is_object( $post ) && isset($post->$meta_key) ) {
			$value = htmlspecialchars_decode( $post->$meta_key, ENT_QUOTES | ENT_HTML401 );
		} elseif( is_numeric( $post ) ) {
			// Maybe a Post ID was passed instead of object
			$post_obj = get_post( $post );
			$value = $this->get_meta_value( $meta_key, $post_obj, $default );
		} elseif( $func && ! empty($default) ) {
			// No value exists, or error getting post data, check if default
			// value should come from method/function call
			if( is_array( $default ) && isset($default[0], $default[1]) ) {
				$value = method_exists( $default[0], $default[1] ) ? call_user_func( $default ) : '';
			} elseif( function_exists( $default ) ) {
				$value = call_user_func( $default );
			}
		}

		return $value;
	}

	/**
	 * Echo Out Meta Value
	 *
	 * Basically get_meta_value() with an echo
	 *
	 * @since 1.0.0
	 *
	 * @param        $meta_key
	 * @param string $default
	 * @param null   $post
	 * @param bool   $func
	 */
	function echo_meta_value( $meta_key, $default = '', $post = NULL, $func = false ){
		echo $this->get_meta_value( $meta_key, $default, $post, $func );
	}

	/**
	 * Magic Method to catch output_ or display_ method calls
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $method_name
	 * @param $args
	 *
	 * @return
	 */
	public function __call( $method_name, $args ) {

		if( preg_match( '/(?P<action>(output|display|the|get)+)_(?P<variable>\w+)/', $method_name, $matches ) ) {
			$variable = strtolower( $matches['variable'] );

			switch( $matches['action'] ) {
				case 'display':
				case 'output':
					$this->__check_arguments( $args, 0, 0, $method_name );
					$post = is_array( $args ) && isset( $args[0] ) ? $args[0] : null;
					$metabox = is_array( $args ) && isset( $args[1] ) ? $args[1] : null;
					$this->default_output( $variable, $post, $metabox );
					break;
				case 'the':
					$default = isset( $args[0] ) ? $args[0] : '';
					$post = isset( $args[1] ) ? $args[1] : null;
					$func = isset( $args[2] ) ? $args[2] : false;
					$this->echo_meta_value( $variable, $default, $post, $func );
					break;
				case 'get':
					$default = isset($args[0]) ? $args[0] : '';
					$post    = isset($args[1]) ? $args[1] : NULL;
					$func    = isset($args[2]) ? $args[2] : FALSE;
					return $this->get_meta_value( $variable, $default, $post, $func );
					break;
				case 'default':
					error_log( 'Method ' . $method_name . ' not exists' );

			}
		}
	}

	/**
	 * Method used by Magic Method to check arguments
	 *
	 * @param array   $args
	 * @param integer $min
	 * @param integer $max
	 * @param         $method_name
	 */
	protected function __check_arguments( array $args, $min, $max, $method_name ) {

		$argc = count( $args );
		if( $argc < $min || $argc > $max ) {
			error_log( 'Method ' . $method_name . ' needs minimaly ' . $min . ' and maximaly ' . $max . ' arguments. ' . $argc . ' arguments given.' );
		}
	}

	/**
	 * WP_Job_Manager_Emails_Job
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_Job_Manager_Emails_Job
	 */
	function job() {
		return $this->cpt()->integration()->job();
	}

	/**
	 * WP_Job_Manager_Emails_Resume
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_Job_Manager_Emails_Resume
	 */
	function resume() {
		return $this->cpt()->integration()->resume();
	}

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