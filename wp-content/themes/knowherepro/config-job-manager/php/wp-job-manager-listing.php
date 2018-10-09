<?php
/**
 * WP Job Manager listing implementation.
 *
 * @since 2.0.0
 */
class Knowhere_WP_Job_Manager_Listing extends Knowhere_Listing {

	/**
	 * Get Business Hours
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	public function get_business_hours() {
		return knowhere_sanitize_hours( get_post_meta( $this->get_id(), '_job_hours', true ) );
	}

	public function get_the_company_tagline() {
		return get_the_company_tagline( $this->get_object() );
	}

	public function get_the_company_description() {
		return apply_filters( 'the_company_description', $this->get_object()->_company_description, $this->get_object() );
	}

	public function get_the_company_name() {
		return get_the_company_name( $this->get_object() );
	}

	public function get_the_company_logo( $size = 'thumbnail' ) {
		return get_the_company_logo( $this->get_object(), $size );
	}

	public function get_the_company_website() {
		return esc_url( get_the_company_website( $this->get_object() ) );
	}

	public function get_the_company_twitter() {
		return get_the_company_twitter( $this->get_object() );
	}

	public function get_the_company_facebook() {
		$company_facebook = $this->get_object()->_company_facebook;

		if ( $company_facebook && filter_var( $company_facebook, FILTER_VALIDATE_URL ) === false ) {
			$company_facebook = 'https://facebook.com/' . $company_facebook;
		}

		return apply_filters( 'the_company_facebook', $company_facebook, $this->get_object() );
	}

	public function get_the_company_gplus() {
		$company_google = $this->get_object()->_company_google;

		if ( $company_google && filter_var( $company_google, FILTER_VALIDATE_URL ) === false ) {
			$company_google = 'https://plus.google.com/' . $company_google;
		}

		return apply_filters( 'the_company_google', $company_google, $this->get_object() );
	}

	public function get_the_company_linkedin() {
		$company_linkedin = $this->get_object()->_company_linkedin;

		if ( $company_linkedin && filter_var( $company_linkedin, FILTER_VALIDATE_URL ) === false ) {
			$company_linkedin = 'http://linkedin.com/company/' . $company_linkedin;
		}

		return apply_filters( 'the_company_linkedin', $company_linkedin, $this->get_object() );
	}

	public function get_the_company_pinterest() {
		$company_pinterest = $this->get_object()->_company_pinterest;

		if ( $company_pinterest && filter_var( $company_pinterest, FILTER_VALIDATE_URL ) === false ) {
			$company_pinterest = 'http://pinterest.com/' . $company_pinterest;
		}

		return apply_filters( 'the_company_pinterest', $company_pinterest, $this->get_object() );
	}

	public function get_the_company_phone() {
		$company_phone = $this->get_object()->_company_phone;
		return apply_filters( 'the_company_phone', $company_phone, $this->get_object() );
	}

	/**
	 * Get Business Hours Timezone
	 *
	 * @since 2.1.0
	 *
	 * @param bool $display Remove underscore for display in front end.
	 * @return string
	 */
	public function get_business_hours_timezone( $display = false ) {
		$post_timezone_string = $this->get_object()->_job_hours_timezone;
		if ( ! $post_timezone_string ) {
			$post_timezone_string = get_option( 'timezone_string' );
		}

		if (! $post_timezone_string ) {
			$post_gmt_offset = $this->get_business_hours_gmt();
			if ( 0 == $post_gmt_offset ) {
				$post_timezone_string = 'UTC+0';
			} elseif ( $post_gmt_offset < 0 ) {
				$post_timezone_string = 'UTC' . $post_gmt_offset;
			} else {
				$post_timezone_string = 'UTC+' . $post_gmt_offset;
			}
		}

		// Clean up underscores.
		if ( $display ) {
			$post_timezone_string =  str_replace( '_', ' ', $post_timezone_string );
		}
		return $post_timezone_string;
	}

	/**
	 * Get Business Hours GMT
	 *
	 * @since 2.1.0
	 *
	 * @return int
	 */
	public function get_business_hours_gmt() {
		return intval( get_option( 'gmt_offset' ) );
	}

}
