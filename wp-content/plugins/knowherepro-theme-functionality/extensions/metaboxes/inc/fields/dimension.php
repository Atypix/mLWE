<?php

/**
 * Abstract input field class which is used for all <input> fields.
 */
abstract class MAD_Dimension_Field extends MAD_Multiple_Values_Field {

	/**
	 * Get field HTML
	 *
	 * @param mixed $meta
	 * @param array $field
	 * @return string
	 */
	public static function html( $meta, $field ) {
		$html  = array();
		$input = '<div class="mad-builder-dimension"><span class="add-on"><i class="%s"></i></span><input type="text" class="mad-dimension" name="%s" value="%s"></div>';
		$count = 0;

		foreach ( $field['options'] as $label ) {
			$icon_class = 'fa fa-arrows-h';
			if ( false !== strpos( $label, 'height' ) ) {
				$icon_class = 'fa fa-arrows-v';
			}
			if ( false !== strpos( $label, 'top' ) ) {
				$icon_class = 'dashicons dashicons-arrow-up-alt';
			}
			if ( false !== strpos( $label, 'right' ) ) {
				$icon_class = 'dashicons dashicons-arrow-right-alt';
			}
			if ( false !== strpos( $label, 'bottom' ) ) {
				$icon_class = 'dashicons dashicons-arrow-down-alt';
			}
			if ( false !== strpos( $label, 'left' ) ) {
				$icon_class = 'dashicons dashicons-arrow-left-alt';
			}

			$html[] = sprintf(
				$input,
				$icon_class,
				$field['field_name'],
				isset( $meta[ $count ] ) ? esc_attr( $meta[ $count ] ) : ''
			);
			$count ++;
		}

		return implode( ' ', $html );
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	static function normalize( $field ) {
		$field               = parent::normalize( $field );
		$field['multiple']   = false;
		$field['field_name'] = $field['id'];
		if ( ! $field['clone'] ) {
			$field['field_name'] .= '[]';
		}

		return $field;
	}

	/**
	 * Format a single value for the helper functions.
	 *
	 * @param array  $field Field parameter
	 * @param string $value The value
	 * @return string
	 */
	static function format_single_value( $field, $value ) {
		return $field['options'][ $value ];
	}

}
