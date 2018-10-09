<?php

if( ! defined( 'ABSPATH' ) ) exit;

if( ! function_exists( 'get_job_manager_emails_admin_view' ) ){

	/**
	 * Get Admin View Template
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param $view_name
	 */
	function get_job_manager_emails_admin_view( $view_name ) {

		if( ! current_user_can( 'edit_post', get_the_ID() ) ) return;

		$req_view = apply_filters( 'job_manager_emails_admin_get_view',JOB_MANAGER_EMAILS_PLUGIN_DIR . "/includes/admin/views/{$view_name}.php", $view_name );

		if ( ! empty( $req_view ) && file_exists( $req_view ) ) {
			require_once $req_view;
		}

	}

}

if( ! function_exists( 'get_plugin_directory_path') ){

	/**
	 * Get Plugin's Directory Path
	 *
	 * Search for a plugin's directory path, based on configuration in the plugin's main plugin
	 * file.  By default if passed search value is a string, will search the `Name` value.  To
	 * search multiple values, or different value to match, pass as an array with key => value.
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param string|array $search         Value to find plugin based on `Name`, or an array with key => value pairs to match.
	 *                                     Search is case-insensitive, and will only return if all search values match.
	 * @param string|bool  $const          Constant value to pass and check first, before doing search.
	 *
	 * @param bool         $trailing_slash Whether or not to include trailing slash (default is false)
	 *
	 * @return bool|string Returns directory path to plugin if match found, otherwise FALSE if no match found.
	 */
	function get_plugin_directory_path( $search, $const = false, $trailing_slash = false ){

		if( file_exists( $const ) ){
			return $const;
		}

		if ( ! function_exists( 'get_plugins' ) )
			require_once ABSPATH . 'wp-admin/includes/plugin.php';

		$plugins = get_plugins();

		foreach( (array) $plugins as $plugin_path => $readme ){

			if( is_array( $search ) ){

				// Remove spaces from array keys (keys returned by WordPress have spaces removed)
				$search = array_combine( str_replace( ' ', '', array_keys( $search ) ), array_values( $search ) );

				// Do case-insensitive comparison between passed search array
				$diff_check = array_udiff_uassoc( $search, $readme, 'strcasecmp', 'strcasecmp' );

				if( empty( $diff_check ) ){
					$path = trailingslashit( WP_PLUGIN_DIR ) . plugin_dir_path( $plugin_path );
					return $trailing_slash ? $path : untrailingslashit( $path );
				}

			} else {

				if( strtolower( $readme[ 'Name' ] ) === strtolower( $search ) ){
					$path = trailingslashit( WP_PLUGIN_DIR ) . plugin_dir_path( $plugin_path );
					return $trailing_slash ? $path : untrailingslashit( $path );
				}

			}

		}

		return FALSE;
	}

}

if( ! function_exists( 'remove_class_filter') ) {
	/**
	 * Remove Class Filter Without Access to Class Object
	 *
	 * In order to use the core WordPress remove_filter() on a filter added with the callback
	 * to a class, you either have to have access to that class object, or it has to be a call
	 * to a static method.  This method allows you to remove filters with a callback to a class
	 * you don't have access to.
	 *
	 * Works with WordPress 1.2+ (4.7+ support added 9-19-2016)
	 *
	 * @param string $tag         Filter to remove
	 * @param string $class_name  Class name for the filter's callback
	 * @param string $method_name Method name for the filter's callback
	 * @param int    $priority    Priority of the filter (default 10)
	 *
	 * @return bool Whether the function is removed.
	 */
	function remove_class_filter( $tag, $class_name = '', $method_name = '', $priority = 10 ) {

		global $wp_filter;

		// Check that filter actually exists first
		if ( ! isset( $wp_filter[ $tag ] ) ) return FALSE;

		/**
		 * If filter config is an object, means we're using WordPress 4.7+ and the config is no longer
		 * a simple array, rather it is an object that implements the ArrayAccess interface.
		 *
		 * To be backwards compatible, we set $callbacks equal to the correct array as a reference (so $wp_filter is updated)
		 *
		 * @see https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/
		 */
		if ( is_object( $wp_filter[ $tag ] ) && isset( $wp_filter[ $tag ]->callbacks ) ) {
			$fob = $wp_filter[ $tag ];
			$callbacks = &$wp_filter[ $tag ]->callbacks;
		} else {
			$callbacks = &$wp_filter[ $tag ];
		}

		// Exit if there aren't any callbacks for specified priority
		if ( ! isset( $callbacks[ $priority ] ) || empty( $callbacks[ $priority ] ) ) return FALSE;

		// Loop through each filter for the specified priority, looking for our class & method
		foreach( (array) $callbacks[ $priority ] as $filter_id => $filter ) {

			// Filter should always be an array - array( $this, 'method' ), if not goto next
			if ( ! isset( $filter[ 'function' ] ) || ! is_array( $filter[ 'function' ] ) ) continue;

			// If first value in array is not an object, it can't be a class
			if ( ! is_object( $filter[ 'function' ][ 0 ] ) ) continue;

			// Method doesn't match the one we're looking for, goto next
			if ( $filter[ 'function' ][ 1 ] !== $method_name ) continue;

			// Method matched, now let's check the Class
			// Method matched, now let's check the Class
			if ( get_class( $filter[ 'function' ][ 0 ] ) === $class_name ) {

				// WordPress 4.7+ use core remove_filter() since we found the class object
				if( isset( $fob ) ){
					// Handles removing filter, reseting callback priority keys mid-iteration, etc.
					$fob->remove_filter( $tag, $filter['function'], $priority );

				} else {
					// Use legacy removal process (pre 4.7)
					unset( $callbacks[ $priority ][ $filter_id ] );
					// and if it was the only filter in that priority, unset that priority
					if ( empty( $callbacks[ $priority ] ) ) {
						unset( $callbacks[ $priority ] );
					}
					// and if the only filter for that tag, set the tag to an empty array
					if ( empty( $callbacks ) ) {
						$callbacks = array();
					}
					// Remove this filter from merged_filters, which specifies if filters have been sorted
					unset( $GLOBALS['merged_filters'][ $tag ] );
				}

				return TRUE;
			}
		}

		return FALSE;
	}
}

if( ! function_exists( 'jm_emails_logger' ) ){

	function jm_emails_logger( $type ){
		return WP_Job_Manager_Emails_Log::get_instance( $type );
	}

}