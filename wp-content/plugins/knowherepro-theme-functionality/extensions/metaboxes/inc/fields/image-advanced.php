<?php
/**
 * Image advanced field class which users WordPress media popup to upload and select images.
 */
class MAD_Image_Advanced_Field extends MAD_Media_Field {

	/**
	 * Enqueue scripts and styles
	 */
	static function admin_enqueue_scripts() {
		parent::admin_enqueue_scripts();
		wp_enqueue_style( 'mad-image-advanced', MAD_CSS_URL . 'image-advanced.css', array( 'mad-media' ), MAD_VER );
		wp_enqueue_script( 'mad-image-advanced', MAD_JS_URL . 'image-advanced.js', array( 'mad-media' ), MAD_VER, true );
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	static function normalize( $field ) {
		$field              = parent::normalize( $field );
		$field['mime_type'] = 'image';
		return $field;
	}

	/**
	 * Get the field value.
	 *
	 * @param array $field
	 * @param array $args
	 * @param null  $post_id
	 * @return mixed
	 */
	static function get_value( $field, $args = array(), $post_id = null ) {
		return MAD_Image_Field::get_value( $field, $args, $post_id );
	}

	/**
	 * Get uploaded file information.
	 *
	 * @param int   $file Attachment image ID (post ID). Required.
	 * @param array $args Array of arguments (for size).
	 * @return array|bool False if file not found. Array of image info on success
	 */
	static function file_info( $file, $args = array() ) {
		return MAD_Image_Field::file_info( $file, $args );
	}

	/**
	 * Format value for the helper functions.
	 *
	 * @param array        $field Field parameter
	 * @param string|array $value The field meta value
	 * @return string
	 */
	public static function format_value( $field, $value ) {
		return MAD_Image_Field::format_value( $field, $value );
	}

	/**
	 * Format a single value for the helper functions.
	 *
	 * @param array $field Field parameter
	 * @param array $value The value
	 * @return string
	 */
	public static function format_single_value( $field, $value ) {
		return MAD_Image_Field::format_single_value( $field, $value );
	}

	/**
	 * Template for media item
	 *
	 * @return void
	 */
	public static function print_templates() {
		parent::print_templates();
		require_once MAD_INC_DIR . 'templates/image-advanced.php';
	}
}
