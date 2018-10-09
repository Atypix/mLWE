<?php

/**
 * File input field class which uses an input for file URL.
 */
class MAD_File_Input_Field extends MAD_Field {

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	static function admin_enqueue_scripts() {
		wp_enqueue_media();
		wp_enqueue_script( 'mad-file-input', MAD_JS_URL . 'file-input.js', array( 'jquery' ), MAD_VER, true );
		self::localize_script('mad-file-input', 'madFileInput', array(
			'frameTitle' => esc_html__( 'Select File', 'knowherepro_app_textdomain' ),
		) );
	}

	/**
	 * Get field HTML
	 *
	 * @param mixed $meta
	 * @param array $field
	 *
	 * @return string
	 */
	static function html( $meta, $field ) {
		return sprintf(
			'<input type="text" class="mad-file-input" name="%s" id="%s" value="%s" placeholder="%s" size="%s">
			<a href="#" class="mad-file-input-select button-primary">%s</a>
			<a href="#" class="mad-file-input-remove button %s">%s</a>',
			$field['field_name'],
			$field['id'],
			$meta,
			$field['placeholder'],
			$field['size'],
			esc_html__( 'Select', 'knowherepro_app_textdomain' ),
			$meta ? '' : 'hidden',
			esc_html__( 'Remove', 'knowherepro_app_textdomain' )
		);
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	static function normalize( $field ) {
		$field = parent::normalize( $field );
		$field = wp_parse_args( $field, array(
			'size'        => 30,
			'placeholder' => '',
		) );

		return $field;
	}
}
