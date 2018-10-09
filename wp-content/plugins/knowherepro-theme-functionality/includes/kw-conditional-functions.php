<?php
/**
 *
 * Functions for determining the current query/page.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'taxonomy_is_listing_attribute' ) ) {

	/**
	 * Returns true when the passed taxonomy name is a product attribute.
	 * @uses   $wc_product_attributes global which stores taxonomy names upon registration
	 * @param  string $name of the attribute
	 * @return bool
	 */
	function taxonomy_is_listing_attribute( $name ) {
		global $kw_product_attributes;

		return taxonomy_exists( $name ) && array_key_exists( $name, (array) $kw_product_attributes );
	}
}

if ( ! function_exists( 'meta_is_listing_attribute' ) ) {

	/**
	 * Returns true when the passed meta name is a product attribute.
	 * @param  string $name of the attribute
	 * @param  string $value
	 * @param  int $product_id
	 * @return bool
	 */
	function meta_is_listing_attribute( $name, $value, $product_id ) {
		$product = wc_get_product( $product_id );

		if ( $product && method_exists( $product, 'get_variation_attributes' ) ) {
			$variation_attributes = $product->get_variation_attributes();
			$attributes           = $product->get_attributes();
			return ( in_array( $name, array_keys( $attributes ) ) && in_array( $value, $variation_attributes[ $attributes[ $name ]['name'] ] ) );
		} else {
			return false;
		}
	}
}
