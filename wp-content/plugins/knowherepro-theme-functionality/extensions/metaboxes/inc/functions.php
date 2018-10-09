<?php
/**
 * Plugin public functions.
 */

if ( ! function_exists( 'mad_meta' ) ) {
	/**
	 * Get post meta
	 *
	 * @param string   $key     Meta key. Required.
	 * @param int|null $post_id Post ID. null for current post. Optional
	 * @param array    $args    Array of arguments. Optional.
	 *
	 * @return mixed
	 */
	function mad_meta( $key, $args = array(), $post_id = null ) {
		$args = wp_parse_args( $args );
		/*
		 * If meta boxes is registered in the backend only, we can't get field's params
		 * This is for backward compatibility with version < 4.8.0
		 */
		$field = MAD_Helper::find_field( $key, $post_id );

		/*
		 * If field is not found, which can caused by registering meta boxes for the backend only or conditional registration
		 * Then fallback to the old method to retrieve meta (which uses get_post_meta() as the latest fallback)
		 */
		if ( false === $field ) {
			return apply_filters( 'mad_meta', MAD_Helper::meta( $key, $args, $post_id ) );
		}
		$meta = in_array( $field['type'], array( 'oembed', 'map' ) ) ?
			mad_the_value( $key, $args, $post_id, false ) :
			mad_get_value( $key, $args, $post_id );
		return apply_filters( 'mad_meta', $meta, $key, $args, $post_id );
	}
}

if ( ! function_exists( 'mad_get_value' ) ) {
	/**
	 * Get value of custom field.
	 * This is used to replace old version of mad_meta key.
	 *
	 * @param  string   $field_id Field ID. Required.
	 * @param  array    $args     Additional arguments. Rarely used. See specific fields for details
	 * @param  int|null $post_id  Post ID. null for current post. Optional.
	 *
	 * @return mixed false if field doesn't exist. Field value otherwise.
	 */
	function mad_get_value( $field_id, $args = array(), $post_id = null ) {
		$args  = wp_parse_args( $args );
		$field = MAD_Helper::find_field( $field_id, $post_id );

		// Get field value
		$value = $field ? MAD_Field::call( 'get_value', $field, $args, $post_id ) : false;

		/*
		 * Allow developers to change the returned value of field
		 * For version < 4.8.2, the filter name was 'mad_get_field'
		 *
		 * @param mixed    $value   Field value
		 * @param array    $field   Field parameter
		 * @param array    $args    Additional arguments. Rarely used. See specific fields for details
		 * @param int|null $post_id Post ID. null for current post. Optional.
		 */
		$value = apply_filters( 'mad_get_value', $value, $field, $args, $post_id );

		return $value;
	}
}

if ( ! function_exists( 'mad_the_value' ) ) {
	/**
	 * Display the value of a field
	 *
	 * @param  string   $field_id Field ID. Required.
	 * @param  array    $args     Additional arguments. Rarely used. See specific fields for details
	 * @param  int|null $post_id  Post ID. null for current post. Optional.
	 * @param  bool     $echo     Display field meta value? Default `true` which works in almost all cases. We use `false` for  the [mad_meta] shortcode
	 *
	 * @return string
	 */
	function mad_the_value( $field_id, $args = array(), $post_id = null, $echo = true ) {
		$args  = wp_parse_args( $args );
		$field = MAD_Helper::find_field( $field_id, $post_id );

		if ( ! $field ) {
			return '';
		}

		$output = MAD_Field::call( 'the_value', $field, $args, $post_id );

		/*
		 * Allow developers to change the returned value of field
		 * For version < 4.8.2, the filter name was 'mad_get_field'
		 *
		 * @param mixed    $value   Field HTML output
		 * @param array    $field   Field parameter
		 * @param array    $args    Additional arguments. Rarely used. See specific fields for details
		 * @param int|null $post_id Post ID. null for current post. Optional.
		 */
		$output = apply_filters( 'mad_the_value', $output, $field, $args, $post_id );

		if ( $echo ) {
			echo $output;
		}

		return $output;
	}
}// End if().
