<?php
/**
 * Divider field class.
 */
class MAD_Divider_Field extends MAD_Field {

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	static function admin_enqueue_scripts() {
		wp_enqueue_style( 'mad-divider', MAD_CSS_URL . 'divider.css', array(), MAD_VER );
	}

	/**
	 * Show begin HTML markup for fields
	 *
	 * @param mixed $meta
	 * @param array $field
	 *
	 * @return string
	 */
	static function begin_html( $meta, $field ) {
		$attributes = empty( $field['id'] ) ? '' : " id='{$field['id']}'";
		return "<hr$attributes>";
	}

	/**
	 * Show end HTML markup for fields
	 *
	 * @param mixed $meta
	 * @param array $field
	 *
	 * @return string
	 */
	static function end_html( $meta, $field ) {
		return '';
	}
}
