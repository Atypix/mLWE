<?php
/**
 * Handle individual listing data.
 *
 * @since 1.0.0
 *
 */
abstract class Knowhere_Listing {

	/**
	 * The associated WordPress post object.
	 *
	 * @since 2.0.0
	 * @var WP_Post $post
	 */
	protected $post;

	/**
	 * Load a new instance of a listing.
	 *
	 * @since 2.0.0
	 *
	 * @param null|int|WP_Post $post Current object.
	 */
	public function __construct( $post ) {
		if ( ! $post ) {
			$this->post = get_post();
		} elseif ( is_int( $post ) ) {
			$this->post = get_post( $post );
		} elseif ( is_a( $post, 'WP_Post' ) ) {
			$this->post = $post;
		}
	}

	/**
	 * Listing ID
	 *
	 * @since 2.0.0
	 *
	 * @return int
	 */
	public function get_id() {
		return $this->get_object()->ID;
	}

	/**
	 * Associated listing object
	 *
	 * @since 2.0.0
	 */
	public function get_object() {
		return $this->post;
	}

	/**
	 * Get Business Hours
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	abstract public function get_business_hours();

	/**
	 * Get Business Hours Timezone
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	abstract public function get_business_hours_timezone();

	/**
	 * Get Business Hours GMT
	 *
	 * @since 2.1.0
	 *
	 * @return int
	 */
	abstract public function get_business_hours_gmt();

	public function get_current_day() {
		return strtolower( knowhere_get_current_time( 'D', $this->get_business_hours_gmt() ) );
	}

	public function get_current_time( $format ) {
		return strtolower( knowhere_get_current_time( $format, $this->get_business_hours_gmt() ) );
	}

	/**
	 * Is Hour Open.
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	public function is_open() {
		$is_open = false;
		$format = get_option( 'time_format' );

		$current_day = $this->get_current_day();
		$current_time = $this->get_current_time($format);
		$time = DateTime::createFromFormat( $format, $current_time );

		$opening_hours = $this->get_business_hours();

		if ( ! isset( $opening_hours[ $current_day ] ) ) {
			return $is_open;
		}

		// Loop each hour and compare with current time.
		foreach ( $opening_hours[ $current_day ] as $hours ) {

			if ( isset( $hours['start'], $hours['end'] ) && $hours['start'] && $hours['end'] ) {

				$open = DateTime::createFromFormat( $format,  $hours['start'] );
				$close = DateTime::createFromFormat( $format, $hours['end'] );

				if ( ! $close || ! $open ) {
					$is_open = false;
					break;
				}

				if ( $close < $open ) {
					$close = $close->add( new DateInterval( 'P1D' ) );
				}

				if ( $time > $open && $time < $close ) {
					$is_open = true;
					break;
				}
			}
		}

		return (bool) apply_filters( 'knowhere_get_listing_is_open', $is_open, $this );
	}

	abstract public function get_the_company_tagline();

	abstract public function get_the_company_description();

	abstract public function get_the_company_name();

	abstract public function get_the_company_logo();

}
