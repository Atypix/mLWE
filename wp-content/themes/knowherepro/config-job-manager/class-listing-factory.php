<?php
/**
 * Determine which Listing child class to load based on the
 * active integration.
 *
 * @since 2.0.0
 *
 */
class Knowhere_Listing_Factory {

	/**
	 * Get a single listing.
	 *
	 * @since 2.0.0
	 *
	 * @param mixed $listing Get a listing.
	 */
	public function get_listing( $listing ) {
		return new Knowhere_WP_Job_Manager_Listing( $listing );
	}

}
