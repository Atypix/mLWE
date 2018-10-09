<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Emails_Utils {

	/**
	 * Array of all quote entities *(and entities for quote variations)*.
	 *
	 * Array keys are actually regex patterns *(very useful)*.
	 *
	 * @source s2Member
	 * @type array
	 */
	public static $quote_entities = array(
		'&apos;'           => '&apos;',
		'&#0*39;'          => '&#39;',
		'&#[xX]0*27;'      => '&#x27;',
		'&lsquo;'          => '&lsquo;',
		'&#0*8216;'        => '&#8216;',
		'&#[xX]0*2018;'    => '&#x2018;',
		'&rsquo;'          => '&rsquo;',
		'&#0*8217;'        => '&#8217;',
		'&#[xX]0*2019;'    => '&#x2019;',
		'&quot;'           => '&quot;',
		'&#0*34;'          => '&#34;',
		'&#[xX]0*22;'      => '&#x22;',
		'&ldquo;'          => '&ldquo;',
		'&#0*8220;'        => '&#8220;',
		'&#[xX]0*201[cC];' => '&#x201C;',
		'&rdquo;'          => '&rdquo;',
		'&#0*8221;'        => '&#8221;',
		'&#[xX]0*201[dD];' => '&#x201D;'
	);

	public function __construct() {

	}

	/**
	 * Trims all single/double quote entity variations deeply.
	 *
	 * This is useful on Shortcode attributes mangled by a Visual Editor.
	 *
	 * @source s2Member\Utilities
	 * @since   111011
	 *
	 * @param string|array $value Either a string, an array, or a multi-dimensional array, filled with integer and/or string values.
	 *
	 * @return string|array Either the input string, or the input array; after all data is trimmed up.
	 */
	public static function trim_qts_deep( $value = '' ) {

		$qts = implode( '|', array_keys( self::$quote_entities ) );

		return is_array( $value ) ? array_map( 'WP_Job_Manager_Emails_Utils::trim_qts_deep', $value ) : preg_replace( '/^(?:' . $qts . ')+|(?:' . $qts . ')+$/', '', (string) $value );
	}

	/**
	 * Escapes double quotes.
	 *
	 * @source s2Member\Utilities
	 * @since   3.5
	 *
	 * @param string $string      Input string.
	 * @param int    $times       Number of escapes. Defaults to 1.
	 * @param string $escape_char The character to be used in escapes.
	 *
	 * @return string Output string after double quotes are escaped.
	 */
	public static function esc_dq( $string = '', $times = NULL, $escape_char = '\\' ) {

		$times = (is_numeric( $times ) && $times >= 0) ? (int) $times : 1;

		return str_replace( '"', str_repeat( $escape_char, $times ) . '"', (string) $string );
	}

	/**
	 * Escapes single quotes.
	 *
	 * @source s2Member\Utilities
	 * @since   3.5
	 *
	 * @param string $string Input string.
	 * @param int    $times  Number of escapes. Defaults to 1.
	 *
	 * @return string Output string after single quotes are escaped.
	 */
	public static function esc_sq( $string = '', $times = NULL ) {

		$times = (is_numeric( $times ) && $times >= 0) ? (int) $times : 1;

		return str_replace( "'", str_repeat( '\\', $times ) . "'", (string) $string );
	}

	/**
	 * Check if Array has String Keys
	 *
	 * This method will check if the array has string keys instead of numerical,
	 * and return TRUE if it has string keys.
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param array $array
	 *
	 * @return bool
	 */
	public static function has_array_string_keys( array $array ){
		$invalid_chars = 'EyAlIHpKDsivNKtxiS+tpAEE';
		return count( array_filter( array_keys( $array ), 'is_string' ) ) > 0;
	}

	/**
	 * Check if Array is Multi-Dimensional
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param $array
	 *
	 * @return bool
	 */
	public static function is_multi_array( $array ) {

		$md_arrays = array_filter( $array, 'is_array' );
		if( count( $md_arrays ) > 0 ) return TRUE;

		return FALSE;
	}
}

