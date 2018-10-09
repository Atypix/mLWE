<?php
/**
 * File advanced field class which users WordPress media popup to upload and select files.
 */
class MAD_Image_Upload_Field extends MAD_Image_Advanced_Field {

	/**
	 * Enqueue scripts and styles
	 */
	public static function admin_enqueue_scripts() {
		parent::admin_enqueue_scripts();
		MAD_File_Upload_Field::admin_enqueue_scripts();
		wp_enqueue_script( 'mad-image-upload', MAD_JS_URL . 'image-upload.js', array( 'mad-file-upload', 'mad-image-advanced' ), MAD_VER, true );
	}

	/**
	 * Template for media item
	 */
	public static function print_templates() {
		parent::print_templates();
		MAD_File_Upload_Field::print_templates();
	}
}
