<?php

class Knowhere_Views_Counter_Counter {

	const GROUP = 'knowherepro';
	const NAME_ALLKEYS = 'knowhere_cached_key_names';
	const CACHE_KEY_SEPARATOR = '.';

	private $cookie = array(
		'exists'		 => false,
		'visited_posts'	 => array(),
		'expiration'	 => 0
	);

	public function __construct() {
		add_action( 'wp_loaded', array( $this, 'check_cookie' ), 1 );
		add_action( 'deleted_post', array( $this, 'delete_post_views' ) );
		add_action( 'wp', array( $this, 'check_post_php' ) );
	}

	/**
	 * Check whether to count visit.
	 *
	 * @param int $id
	 */
	public function check_post( $id = 0 ) {

		$id = (int) ( empty( $id ) ? get_the_ID() : $id );

		if ( empty( $id ) )
			return;

		if ( $this->cookie['exists'] ) {

			if ( in_array( $id, array_keys( $this->cookie['visited_posts'] ), true ) && current_time( 'timestamp', true ) < $this->cookie['visited_posts'][$id] ) {

				$this->save_cookie( $id, $this->cookie, false );
				return;

			} else {
				$this->save_cookie( $id, $this->cookie );
			}

		} else {
			$this->save_cookie( $id );
		}

		$count_visit = (bool) apply_filters( 'knowhere_count_visit', true, $id );

		if ( $count_visit ) {
			return $this->count_visit( $id );
		} else {
			return;
		}
	}

	/**
	 * Check whether to count visit via PHP request.
	 */
	public function check_post_php() {
		// do not count admin entries
		if ( is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) )
			return;

		// whether to count this post type
		if ( ! is_singular( 'job_listing' ) )
			return;

		$this->check_post( get_the_ID() );
	}

	/**
	 * Initialize cookie session.
	 */
	public function check_cookie() {
		// do not run in admin except for ajax requests
		if ( is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) )
			return;

		// is cookie set?
		if ( isset( $_COOKIE['knowhere_visits'] ) && ! empty( $_COOKIE['knowhere_visits'] ) ) {
			$visited_posts = $expirations = array();

			foreach ( $_COOKIE['knowhere_visits'] as $content ) {
				// is cookie valid?
				if ( preg_match( '/^(([0-9]+b[0-9]+a?)+)$/', $content ) === 1 ) {
					// get single id with expiration
					$expiration_ids = explode( 'a', $content );

					// check every expiration => id pair
					foreach ( $expiration_ids as $pair ) {
						$pair = explode( 'b', $pair );
						$expirations[] = (int) $pair[0];
						$visited_posts[(int) $pair[1]] = (int) $pair[0];
					}
				}
			}

			$this->cookie = array(
				'exists'		 => true,
				'visited_posts'	 => $visited_posts,
				'expiration'	 => max( $expirations )
			);

		}
	}

	/**
	 * Save cookie function.
	 *
	 * @param int $id
	 * @param array $cookie
	 * @param bool $expired
	 */
	private function save_cookie( $id, $cookie = array(), $expired = true ) {
		$expiration = $this->get_timestamp( Knowhere_Job_Views_Counter()->options['general']['time_between_counts']['type'], Knowhere_Job_Views_Counter()->options['general']['time_between_counts']['number'] );

		// is this a new cookie?
		if ( empty( $cookie ) ) {
			// set cookie
			setcookie( 'knowhere_visits[0]', $expiration . 'b' . $id, $expiration, COOKIEPATH, COOKIE_DOMAIN, (isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' ? true : false ), true );
		} else {
			if ( $expired ) {
				// add new id or chang expiration date if id already exists
				$cookie['visited_posts'][$id] = $expiration;
			}

			// create copy for better foreach performance
			$visited_posts_expirations = $cookie['visited_posts'];

			// get current gmt time
			$time = current_time( 'timestamp', true );

			// check whether viewed id has expired - no need to keep it in cookie (less size)
			foreach ( $visited_posts_expirations as $post_id => $post_expiration ) {
				if ( $time > $post_expiration )
					unset( $cookie['visited_posts'][$post_id] );
			}

			// set new last expiration date if needed
			$cookie['expiration'] = max( $cookie['visited_posts'] );

			$cookies = $imploded = array();

			// create pairs
			foreach ( $cookie['visited_posts'] as $id => $exp ) {
				$imploded[] = $exp . 'b' . $id;
			}

			// split cookie into chunks (4000 bytes to make sure it is safe for every browser)
			$chunks = str_split( implode( 'a', $imploded ), 4000 );

			// more then one chunk?
			if ( count( $chunks ) > 1 ) {
				$last_id = '';

				foreach ( $chunks as $chunk_id => $chunk ) {
					// new chunk
					$chunk_c = $last_id . $chunk;

					// is it full-length chunk?
					if ( strlen( $chunk ) === 4000 ) {
						// get last part
						$last_part = strrchr( $chunk_c, 'a' );

						// get last id
						$last_id = substr( $last_part, 1 );

						// add new full-lenght chunk
						$cookies[$chunk_id] = substr( $chunk_c, 0, strlen( $chunk_c ) - strlen( $last_part ) );
					} else {
						// add last chunk
						$cookies[$chunk_id] = $chunk_c;
					}
				}
			} else {
				// only one chunk
				$cookies[] = $chunks[0];
			}

			foreach ( $cookies as $key => $value ) {
				// set cookie
				setcookie( 'knowhere_visits[' . $key . ']', $value, $cookie['expiration'], COOKIEPATH, COOKIE_DOMAIN, (isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' ? true : false ), true );
			}
		}
	}

	/**
	 * Count visit function.
	 *
	 * @global object $wpdb
	 * @param int $id
	 * @return int $id
	 */
	private function count_visit( $id ) {
		global $wpdb;

		$cache_key_names = array();
		$using_object_cache = $this->using_object_cache();
		$increment_amount = (int) apply_filters( 'knowhere_views_increment_amount', 1, $id );

		// get day, week, month and year
		$date = explode( '-', date( 'W-d-m-Y', current_time( 'timestamp' ) ) );

		foreach ( array(
					  0	 => $date[3] . $date[2] . $date[1],  1	 => $date[3] . $date[0],
					  2	 => $date[3] . $date[2],  3	 => $date[3], 4	 => 'total'
				  ) as $type => $period ) {
			if ( $using_object_cache ) {
				$cache_key = $id . self::CACHE_KEY_SEPARATOR . $type . self::CACHE_KEY_SEPARATOR . $period;
				wp_cache_add( $cache_key, 0, self::GROUP );
				wp_cache_incr( $cache_key, $increment_amount, self::GROUP );
				$cache_key_names[] = $cache_key;
			} else {
				$this->db_insert( $id, $type, $period, $increment_amount );
			}
		}

		// update the list of cache keys to be flushed
		if ( $using_object_cache && ! empty( $cache_key_names ) ) {
			$this->update_cached_keys_list_if_needed( $cache_key_names );
		}

		do_action( 'knowhere_after_count_visit', $id );

		return $id;
	}

	/**
	 * Remove post views from database when post is deleted.
	 *
	 * @global object $wpdb
	 * @param int $post_id
	 */
	public function delete_post_views( $post_id ) {
		global $wpdb;
		$wpdb->delete( $wpdb->prefix . 'knowhere_job_listing_post_views', array( 'id' => $post_id ), array( '%d' ) );
	}

	/**
	 * Get timestamp convertion.
	 *
	 * @param string $type
	 * @param int $number
	 * @param int $timestamp
	 * @return string
	 */
	public function get_timestamp( $type, $number, $timestamp = true ) {
		$converter = array(
			'minutes'	 => 60,
			'hours'		 => 3600,
			'days'		 => 86400,
			'weeks'		 => 604800,
			'months'	 => 2592000,
			'years'		 => 946080000
		);

		return (int) ( ( $timestamp ? current_time( 'timestamp', true ) : 0 ) + $number * $converter[$type] );
	}

	/**
	 * Check if object cache is in use.
	 *
	 * @param bool $using
	 * @return bool
	 */
	public function using_object_cache( $using = null ) {
		$using = wp_using_ext_object_cache( $using );

		if ( $using ) {
			// check if explicitly disabled by flush_interval setting/option <= 0
			$flush_interval_number = Knowhere_Job_Views_Counter()->options['general']['flush_interval']['number'];
			$using = ( $flush_interval_number <= 0 ) ? false : true;
		}

		return $using;
	}

	/**
	 * Update the single cache key which holds a list of all the cache keys
	 * that need to be flushed to the db.
	 *
	 * @param array $key_names
	 */
	private function update_cached_keys_list_if_needed( $key_names = array() ) {
		$existing_list = wp_cache_get( self::NAME_ALLKEYS, self::GROUP );
		if ( ! $existing_list ) {
			$existing_list = '';
		}

		$list_modified = false;

		// modify the list contents if/when needed
		if ( empty( $existing_list ) ) {
			// the simpler case of an empty initial list where we just
			// transform the specified key names into a string
			$existing_list = implode( '|', $key_names );
			$list_modified = true;
		} else {
			// search each specified key name and append it if it's not found
			foreach ( $key_names as $key_name ) {
				if ( false === strpos( $existing_list, $key_name ) ) {
					$existing_list .= '|' . $key_name;
					$list_modified = true;
				}
			}
		}

		// save modified list back in cache
		if ( $list_modified ) {
			wp_cache_set( self::NAME_ALLKEYS, $existing_list, self::GROUP );
		}
	}

	/**
	 * Insert or update views count.
	 *
	 * @global object $wpdb
	 * @param int $id
	 * @param string $type
	 * @param string $period
	 * @param int $count
	 * @return bool
	 */
	private function db_insert( $id, $type, $period, $count = 1 ) {
		global $wpdb;

		$count = (int) $count;

		if ( ! $count ) {
			$count = 1;
		}

		return $wpdb->query(
			$wpdb->prepare( "
				INSERT INTO " . $wpdb->prefix . "knowhere_job_listing_post_views (id, type, period, count)
				VALUES (%d, %d, %s, %d)
				ON DUPLICATE KEY UPDATE count = count + %d", $id, $type, $period, $count, $count
			)
		);
	}

}