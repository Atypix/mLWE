<?php

/**
 * Heading field class.
 */
class MAD_Heading_Field extends MAD_Field {

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	static function admin_enqueue_scripts() {
		wp_enqueue_style( 'mad-heading', MAD_CSS_URL . 'heading.css', array(), MAD_VER );
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
		return sprintf( '<h4%s>%s</h4>', $attributes, $field['name'] );
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
		return self::element_description( $field );
	}
}
