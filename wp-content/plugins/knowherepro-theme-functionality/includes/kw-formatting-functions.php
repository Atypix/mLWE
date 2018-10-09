<?php
/**
 * Formatting
 *
 * Functions for formatting data.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Converts a string (e.g. yes or no) to a bool.
 * @since 3.0.0
 * @param string $string
 * @return bool
 */
function kw_string_to_bool( $string ) {
	return is_bool( $string ) ? $string : ( 'yes' === $string || 1 === $string || 'true' === $string || '1' === $string );
}

/**
 * Converts a bool to a string.
 * @since 3.0.0
 * @param bool $bool
 * @return string yes or no
 */
function kw_bool_to_string( $bool ) {
	if ( ! is_bool( $bool ) ) {
		$bool = wc_string_to_bool( $bool );
	}
	return true === $bool ? 'yes' : 'no';
}

/**
 * Explode a string into an array by $delimiter and remove empty values.
 * @since 3.0.0
 * @param string $string
 * @param string $delimiter
 * @return array
 */
function kw_string_to_array( $string, $delimiter = ',' ) {
	return is_array( $string ) ? $string : array_filter( explode( $delimiter, $string ) );
}

/**
 * Sanitize taxonomy names. Slug format (no spaces, lowercase).
 *
 * urldecode is used to reverse munging of UTF8 characters.
 *
 * @param mixed $taxonomy
 * @return string
 */
function kw_sanitize_taxonomy_name( $taxonomy ) {
	return apply_filters( 'sanitize_taxonomy_name', urldecode( sanitize_title( urldecode( $taxonomy ) ) ), $taxonomy );
}

/**
 * Gets the filename part of a download URL.
 *
 * @param string $file_url
 * @return string
 */
function kw_get_filename_from_url( $file_url ) {
	$parts = parse_url( $file_url );
	if ( isset( $parts['path'] ) ) {
		return basename( $parts['path'] );
	}
}

/**
 * Convert a float to a string without locale formatting which PHP adds when changing floats to strings.
 * @param  float $float
 * @return string
 */
function kw_float_to_string( $float ) {
	if ( ! is_float( $float ) ) {
		return $float;
	}

	$locale = localeconv();
	$string = strval( $float );
	$string = str_replace( $locale['decimal_point'], '.', $string );

	return $string;
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 * @param string|array $var
 * @return string|array
 */
function kw_clean( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'kw_clean', $var );
	} else {
		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}

/**
 * Run wc_clean over posted textarea but maintain line breaks.
 * @since  3.0.0
 * @param string $var
 * @return string
 */
function kw_sanitize_textarea( $var ) {
	return implode( "\n", array_map( 'wc_clean', explode( "\n", $var ) ) );
}

/**
 * Merge two arrays.
 *
 * @param array $a1
 * @param array $a2
 * @return array
 */
function kw_array_overlay( $a1, $a2 ) {
	foreach ( $a1 as $k => $v ) {
		if ( ! array_key_exists( $k, $a2 ) ) {
			continue;
		}
		if ( is_array( $v ) && is_array( $a2[ $k ] ) ) {
			$a1[ $k ] = kw_array_overlay( $v, $a2[ $k ] );
		} else {
			$a1[ $k ] = $a2[ $k ];
		}
	}
	return $a1;
}


