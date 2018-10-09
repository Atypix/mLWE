<?php

class WPBakeryShortCode_VC_mad_products extends WPBakeryShortCode {

	/**
	 * Shortcode type.
	 *
	 * @since 3.2.0
	 * @var   string
	 */
	protected $type = 'vc_mad_products';

	/**
	 * Attributes.
	 *
	 * @since 3.2.0
	 * @var   array
	 */
	protected $attributes = array();

	protected function content( $atts, $content = null ) {
		$this->attributes = $this->parse_attributes( $atts );
		$this->query_args = $this->parse_query_args();

		return $this->product_loop();
	}

	protected function parse_legacy_attributes( $attributes ) {
		$mapping = array(
			'per_page' => 'limit',
			'operator' => 'cat_operator',
			'filter'   => 'terms',
		);

		foreach ( $mapping as $old => $new ) {
			if ( isset( $attributes[ $old ] ) ) {
				$attributes[ $new ] = $attributes[ $old ];
				unset( $attributes[ $old ] );
			}
		}

		return $attributes;
	}

	protected function get_wrapper_classes( $columns ) {
		$classes = array( 'woocommerce' );

		if ( 'product' !== $this->type ) {
			$classes[] = 'columns-' . $columns;
		}

		$classes[] = $this->attributes['class'];

		return $classes;
	}

	protected function parse_attributes( $attributes ) {

		$attributes = $this->parse_legacy_attributes( $attributes );

		return shortcode_atts( array(
			'limit'          => '-1',      // Results limit.
			'columns'        => '4',       // Number of columns.
			'orderby'        => 'title',   // menu_order, title, date, rand, price, popularity, rating, or id.
			'order'          => 'ASC',     // ASC or DESC.
			'ids'            => '',        // Comma separated IDs.
			'skus'           => '',        // Comma separated SKUs.
			'category'       => '',        // Comma separated category slugs.
			'cat_operator'   => 'IN',      // Operator to compare categories. Possible values are 'IN', 'NOT IN', 'AND'.
			'attribute'      => '',        // Single attribute slug.
			'terms'          => '',        // Comma separated term slugs.
			'terms_operator' => 'IN',      // Operator to compare terms. Possible values are 'IN', 'NOT IN', 'AND'.
			'visibility'     => 'visible', // Possible values are 'visible', 'catalog', 'search', 'hidden'.
			'class'          => '',        // HTML class.
		), $attributes, $this->type );
	}

	protected function parse_query_args() {
		$query_args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'orderby'             => $this->attributes['orderby'],
			'order'               => strtoupper( $this->attributes['order'] ),
		);

		// @codingStandardsIgnoreStart
		$query_args['posts_per_page'] = (int) $this->attributes['limit'];
		$query_args['meta_query']     = WC()->query->get_meta_query();
		$query_args['tax_query']      = array();
		// @codingStandardsIgnoreEnd

		// Categories.
		$this->set_categories_query_args( $query_args );

		return apply_filters( 'kw_shortcode_products_query', $query_args, $this->attributes, $this->type );
	}

	protected function set_categories_query_args( &$query_args ) {
		if ( ! empty( $this->attributes['category'] ) ) {
			$query_args['tax_query'][] = array(
				'taxonomy' => 'product_cat',
				'terms'    => array_map( 'sanitize_title', explode( ',', $this->attributes['category'] ) ),
				'field'    => 'slug',
				'operator' => $this->attributes['cat_operator'],
			);
		}
	}

	protected function get_products() {
		$transient_name = 'wc_loop' . substr( md5( wp_json_encode( $this->query_args ) . $this->type ), 28 ) . WC_Cache_Helper::get_transient_version( 'product_query' );
		$products = get_transient( $transient_name );

		if ( false === $products || ! is_a( $products, 'WP_Query' ) ) {
			$products = new WP_Query( $this->query_args );
			set_transient( $transient_name, $products, DAY_IN_SECONDS * 30 );
		}

		// Remove ordering query arguments.
		if ( ! empty( $this->attributes['category'] ) ) {
			WC()->query->remove_ordering_args();
		}

		return $products;
	}

	/**
	 * Loop over found products.
	 *
	 * @since  3.2.0
	 * @return string
	 */
	protected function product_loop() {
		global $woocommerce_loop;

		$columns                     = absint( $this->attributes['columns'] );
		$classes                     = $this->get_wrapper_classes( $columns );
		$woocommerce_loop['columns'] = $columns;
		$woocommerce_loop['name']    = $this->type;
		$products                    = $this->get_products();

		ob_start();

		if ( $products->have_posts() ) {
			// Prime caches before grabbing objects.
			update_post_caches( $products->posts, array( 'product', 'product_variation' ) );

			do_action( "woocommerce_shortcode_before_{$this->type}_loop", $this->attributes );

			woocommerce_product_loop_start();

			while ( $products->have_posts() ) {
				$products->the_post();
				wc_get_template_part( 'content', 'product' );
			}

			woocommerce_product_loop_end();

			do_action( "woocommerce_shortcode_after_{$this->type}_loop", $this->attributes );
		} else {
			do_action( "woocommerce_shortcode_{$this->type}_loop_no_results", $this->attributes );
		}

		woocommerce_reset_loop();
		wp_reset_postdata();

		return '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">' . ob_get_clean() . '</div>';
	}

}