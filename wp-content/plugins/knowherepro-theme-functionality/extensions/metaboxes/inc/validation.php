<?php
/**
 * Validation module.
 *
 * @package Meta Box
 */

/**
 * Validation class.
 */
class MAD_Validation {

	/**
	 * Add hooks when module is loaded.
	 */
	public function __construct() {
		add_action( 'mad_after', array( $this, 'rules' ) );
		add_action( 'mad_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Output validation rules of each meta box.
	 * The rules are outputted in [data-rules] attribute of an hidden <script> and will be converted into JSON by JS.
	 *
	 * @param MAD_RW_Meta_Box $object Meta Box object
	 */
	public function rules( $object ) {
		if ( ! empty( $object->meta_box['validation'] ) ) {
			echo '<script type="text/html" class="mad-validation-rules" data-rules="' . esc_attr( json_encode( $object->meta_box['validation'] ) ) . '"></script>';
		}
	}

	/**
	 * Enqueue scripts for validation.
	 *
	 * @param MAD_RW_Meta_Box $object Meta Box object
	 */
	public function enqueue( $object ) {
		if ( empty( $object->meta_box['validation'] ) ) {
			return;
		}
		wp_enqueue_script( 'jquery-validation', MAD_JS_URL . 'jquery-validation/jquery.validate.min.js', array( 'jquery' ), '1.15.0', true );
		wp_enqueue_script( 'jquery-validation-additional-methods', MAD_JS_URL . 'jquery-validation/additional-methods.min.js', array( 'jquery-validation' ), '1.15.0', true );
		wp_enqueue_script( 'mad-validate', MAD_JS_URL . 'validate.js', array( 'jquery-validation', 'jquery-validation-additional-methods' ), MAD_VER, true );

		MAD_Field::localize_script( 'mad-validate', 'madValidate', array(
			'summaryMessage' => esc_html__( 'Please correct the errors highlighted below and try again.', 'knowherepro_app_textdomain' ),
		) );
	}
}
