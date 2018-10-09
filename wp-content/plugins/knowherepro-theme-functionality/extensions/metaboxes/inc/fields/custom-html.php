<?php
/**
 * Custom HTML field class.
 */
class MAD_Custom_Html_Field extends MAD_Field {

	/**
	 * Get field HTML
	 *
	 * @param mixed $meta
	 * @param array $field
	 *
	 * @return string
	 */
	static function html( $meta, $field ) {
		$html = ! empty( $field['std'] ) ? $field['std'] : '';
		if ( ! empty( $field['callback'] ) && is_callable( $field['callback'] ) ) {
			$html = call_user_func_array( $field['callback'], array( $meta, $field ) );
		}
		return $html;
	}
}
