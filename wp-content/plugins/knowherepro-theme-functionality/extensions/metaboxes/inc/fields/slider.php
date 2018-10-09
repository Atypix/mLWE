<?php
/**
 * jQueryUI slider field class.
 */
class MAD_Slider_Field extends MAD_Field {

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	static function admin_enqueue_scripts() {
		$url = MAD_CSS_URL . 'jqueryui';
		wp_enqueue_style( 'jquery-ui-core', "{$url}/jquery.ui.core.css", array(), '1.8.17' );
		wp_enqueue_style( 'jquery-ui-theme', "{$url}/jquery.ui.theme.css", array(), '1.8.17' );
		wp_enqueue_style( 'jquery-ui-slider', "{$url}/jquery.ui.slider.css", array(), '1.8.17' );
		wp_enqueue_style( 'mad-slider', MAD_CSS_URL . 'slider.css' );

		wp_enqueue_script( 'mad-slider', MAD_JS_URL . 'slider.js', array( 'jquery-ui-slider', 'jquery-ui-widget', 'jquery-ui-mouse', 'jquery-ui-core' ), MAD_VER, true );
	}

	/**
	 * Get div HTML
	 *
	 * @param mixed $meta
	 * @param array $field
	 *
	 * @return string
	 */
	static function html( $meta, $field ) {
		return sprintf(
			'<div class="clearfix">
				<div class="mad-slider" id="%s" data-options="%s"></div>
				<span class="mad-slider-value-label">%s<span>%s</span>%s</span>
				<input type="hidden" name="%s" value="%s" class="mad-slider-value">
			</div>',
			$field['id'], esc_attr( wp_json_encode( $field['js_options'] ) ),
			$field['prefix'], ( $meta >= 0 ) ? $meta : $field['std'], $field['suffix'],
			$field['field_name'], ( $meta >= 0 ) ? $meta : $field['std']
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
		$field               = parent::normalize( $field );
		$field               = wp_parse_args( $field, array(
			'prefix'     => '',
			'suffix'     => '',
			'std'      	 => '',
			'js_options' => array(),
		) );
		$field['js_options'] = wp_parse_args( $field['js_options'], array(
			'range' => 'min', // range = 'min' will add a dark background to sliding part, better UI
			'value' => $field['std'],
		) );

		return $field;
	}
}
