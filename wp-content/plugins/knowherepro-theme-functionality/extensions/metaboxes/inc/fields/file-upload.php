<?php
/**
 * File advanced field class which users WordPress media popup to upload and select files.
 */
class MAD_File_Upload_Field extends MAD_Media_Field {

	/**
	 * Enqueue scripts and styles
	 */
	public static function admin_enqueue_scripts() {
		parent::admin_enqueue_scripts();
		wp_enqueue_style( 'mad-upload', MAD_CSS_URL . 'upload.css', array( 'mad-media' ), MAD_VER );
		wp_enqueue_script( 'mad-file-upload', MAD_JS_URL . 'file-upload.js', array( 'mad-media' ), MAD_VER, true );
	}

	/**
	 * Template for media item
	 */
	public static function print_templates() {
		parent::print_templates();
		require_once MAD_INC_DIR . 'templates/upload.php';
	}
}
