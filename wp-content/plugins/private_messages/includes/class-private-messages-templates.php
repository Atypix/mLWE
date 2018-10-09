<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Handle Templates.
 *
 * @category Class
 * @author   Astoundify
 */
class Private_Messages_Templates {

	/**
	 * Get Template.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_name Template name.
	 * @param array  $args          Vars to load in template.
	 * @param string $template_path Template path.
	 * @param string $default_path  Default name.
	 * @return string Template content.
	 */
	public static function get_template( $template_name, $args = array(), $template_path = 'private-messages', $default_path = '' ) {
		if ( $args && is_array( $args ) ) {
			extract( $args );
		}

		ob_start();
		include( self::locate_template( $template_name, $template_path, $default_path ) );
		return ob_get_clean();
	}

	/**
	 * Locate Template
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_name Template name.
	 * @param string $template_path Template path.
	 * @param string $default_path  Default name.
	 * @return string Template name.
	 */
	public static function locate_template( $template_name, $template_path = 'private-messages', $default_path = '' ) {
		$pm = private_messages();

		// Look within passed path within the theme - this is priority.
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name,
			)
		);

		// Get default template.
		if ( ! $template && $default_path !== false ) {
			$default_path = $default_path ? $default_path : $pm->plugin_dir . '/templates/';
			if ( file_exists( trailingslashit( $default_path ) . $template_name ) ) {
				$template = trailingslashit( $default_path ) . $template_name;
			}
		}

		// Return what we found.
		return apply_filters( 'pm_locate_template', $template, $template_name, $template_path );
	}
}
