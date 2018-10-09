<?php
/**
 * User model.
 *
 * @since      3.15.0
 *
 * @package    Reviewer
 */

/**
 * Review model.
 *
 * @package    Reviewer
 */
class RWP_User extends WP_User {

	/**
	 * Key for WP option that maps reviews of all users.
	 *
	 * @var string
	 */
	protected static $option_name = 'rwp_users__reviews';

	/**
	 * Plugin preferences
	 *
	 * @var stdClass
	 */
	protected $preferences;

	/**
	 * Plugin templates
	 *
	 * @var stdClass
	 */
	protected $templates;

	/**
	 * Constructor.
	 *
	 * @param integer $id User ID.
	 */
	public function __construct( $id = 0 ) {
		parent::__construct( $id );
		$this->ip = static::current_ip();
		$this->avatar = static::avatar_url( $id ); // !!! Do not query avatar if it's disabled.
	}

	public function avatar( $size = 50 ) {
		$url = static::avatar_url( $this->ID, intval( $size ) );
		$this->avatar = $url;
		return $url;
	}

	/**
	 * Get raw user reviews.
	 *
	 * @param string $filter
	 * @return void
	 */
	public function get_reviews( $filter = 'public' ) {
		global $wpdb;

		// Get the ids reviews list
		$map = $this->get_reviews_map();

		// Query the reveviews.
		$result = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE meta_id IN (". implode(',', $map) .")", ARRAY_A );

		// Validate filter.
		$filter = in_array( $filter, array(
			'public',
		) ) ? $filter : 'public';

		// Fetch preferences and templates
		$this->get_preferences();
		$this->get_templates();

		$reviews = array();

		// Filter and build reviews.
		foreach ($result as $meta) {
			$review = maybe_unserialize( $meta['meta_value'] );

			if( 'public' == $filter && isset( $review['rating_status'] ) && $review['rating_status'] != 'published') { // The users review was not approved yet
				continue;
			}

			 if( !array_key_exists( $review['rating_template'], $this->templates ) ) {
            	continue;
            }

			$reviews[] = $this->build_review_obj( $review );
		}

		unset($result);
		return $reviews;
	}

	protected function build_review_obj( $rating ) {
		$review = new stdClass();
		// ID.
		$review->id = $rating['rating_id'];

		// Box.
		$review->box = intval( $rating['rating_review_id'] );

		// Post.
		$post = new stdClass();
		$post->id = $rating['rating_post_id'];
		$post->title = get_the_title( $post->id );
		$post->permalink = get_permalink( $post->id);
		$review->post = $post;

		// Score.
		$review->score = round( RWP_User_Review::get_avg( $rating['rating_score'] ),  $this->preferences->score_precision);

		// Criteria.
		$review->criteria = $rating['rating_score'];

		// Title.
		$review->title = $rating['rating_title'];

		// Comment.
		$review->comment = $rating['rating_comment'];

		// Images.
		if( isset( $rating['rating_images'] ) && is_array( $rating['rating_images'] ) ) {
			$review->images = array();

			foreach ($rating['rating_images'] as $attachment_id ) {
				$imageData = wp_get_attachment_image_src( $attachment_id, array( $this->preferences->thumb_width * 2, $this->preferences->thumb_height * 2 ) );
				$imageFullSize = wp_get_attachment_image_src( $attachment_id, 'full' );
				if( $imageData === false ) {
					continue;
				}
				$image = new stdClass();
				$image->id = $attachment_id;
				$image->thumb_url = $imageData[0];
				$image->thumb_real_width = $imageData[1];
				$image->thumb_real_height = $imageData[2];
				$image->thumb_width = $this->preferences->thumb_width;
				$image->thumb_height = $this->preferences->thumb_height;
				$image->src = $imageFullSize[0];
				$image->w = $imageFullSize[1];
				$image->h = $imageFullSize[2];

				$review->images[] = $image;
			}
		} else {
			$review->images = array();
		}

		// Date.
		$date = new stdClass();
		$date->timestamp = intval($rating['rating_date']);
		if ( $this->preferences->human_format ) {
			$date->visual = sprintf( __( '%s ago', 'reviewer' ), human_time_diff( intval($rating['rating_date']), current_time( 'timestamp' ) ) );
		} else {
			$date->visual = date_i18n(  $this->preferences->date_format . ', ' . $this->preferences->time_format , intval( $rating['rating_date'] ) );
		}
		$review->date = $date;

		// Verified.
		$review->verified = $rating['rating_verified'];

		// Positive judgements.
		$likes = get_post_meta( $review->post->id, 'rwp_likes', true );
		$likes = is_array( $likes ) ? $likes : array();

		$judgements = new stdClass();
		$judgements->positive = ( isset( $likes[ $review->id ]['yes']  ) ) ? $likes[ $review->id ]['yes'] : 0;

		$review->judgements = $judgements;

		// Template.
		$temp = $this->templates[ $rating['rating_template'] ];
		$template = new stdClass();
		$template->id = $temp['template_id'];

		$criteria = new stdClass();
		$criteria->labels = $temp['template_criterias'];
		$criteria->order = $temp['template_criteria_order'];
		$template->criteria = $criteria;

		$score = new stdClass();
		$score->maximum = floatval( $temp['template_maximum_score'] );
		$score->minimum = floatval( $temp['template_minimum_score'] );
		$score->icon = $temp['template_rate_image'];

		$range = explode( '-', $temp['template_score_percentages'] );
		$percentages = new stdClass();
		$percentages->low = floatval( $range[0] );
		$percentages->high = floatval( $range[1] );
		$score->percentages = $percentages;

		$colors = new stdClass();
		$colors->low = $temp['template_low_score_color'];
		$colors->high = $temp['template_high_score_color'];
		$colors->medium = $temp['template_medium_score_color'];
		$score->colors = $colors;

		$template->score = $score;

		$template->rfields = $temp['template_user_rating_options'];

		$review->template = $template;
		unset($temp);

		// return $rating;
		return $review;
	}

	protected function get_reviews_map() {
		$maps = get_option( static::$option_name );

		// Generate the map if it was not created yet.
		if( false === $maps ) {
			$maps = static::generate_maps();
		}

		return (is_array( $maps ) && isset( $maps[ $this->ID ] ))? $maps[ $this->ID ] : array();
	}

	protected function get_preferences() {
		$preferences = RWP_Reviewer::get_option( 'rwp_preferences' );

		$obj = new stdClass();
		$obj->score_step = isset( $preferences['preferences_step'] ) ? $preferences['preferences_step'] : 0.5;
		$obj->score_precision = RWP_User_Review::get_decimal_places( $obj->score_step );
		$obj->thumb_width = isset( $preferences['preferences_user_review_images']['field_dim']['width'] ) ? intval( $preferences['preferences_user_review_images']['field_dim']['width'] )  : 100;
		$obj->thumb_height = isset( $preferences['preferences_user_review_images']['field_dim']['height'] ) ? intval( $preferences['preferences_user_review_images']['field_dim']['height'] )  : 100;
		$obj->date_format = get_option('date_format');
		$obj->time_format = get_option('time_format');
		$obj->human_format = ( isset( $preferences['preferences_users_reviews_human_date_format'] ) && $preferences['preferences_users_reviews_human_date_format'] == 'yes' );

		$this->preferences = $obj;
		return $obj;
	}

	protected function get_templates() {
		$templates = get_option( 'rwp_templates', array() );
		$this->templates = $templates;
		return $templates;
	}

	public static function generate_maps() {
		global $wpdb;
		// Final maps that contains the users reviews ids .
		$maps = array();

		// Query all reviews of all users.
		$result = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE meta_key LIKE 'rwp_rating%';", ARRAY_A );

		// Loop all the reviews
		foreach ( $result as $meta ) {
			// Unserialize the review
			$review = maybe_unserialize( $meta['meta_value'] );

			// Check if it's a user review
			if ( !isset( $review['rating_id'] ) ) {
				continue;
			}

			// Necessary data.
			$user_id = isset( $review['rating_user_id'] ) ? intval( $review['rating_user_id'] ) : 0;
			$meta_id = intval( $meta['meta_id'] );

			// Map the review
			if( !isset( $maps[ $user_id ] ) || !is_array($maps[ $user_id ] ) ){
				$maps[ $user_id ] = array();
			}
			if( !in_array( $user_id, $maps[ $user_id ] ) ) {
				$maps[ $user_id ][] = $meta_id;
			}
		}

		update_option( static::$option_name, $maps );

		return $maps;
	}

	public static function map_review( $user, $meta ) {
		$user_id = intval( $user );
		$meta_id = intval( $meta );

		$maps = get_option( static::$option_name );

		// Map the review
		if( !isset( $maps[ $user_id ] ) || !is_array($maps[ $user_id ] ) ){
			$maps[ $user_id ] = array();
		}
		if( !in_array( $user_id, $maps[ $user_id ] ) ) {
			$maps[ $user_id ][] = $meta_id;
		}

		update_option( static::$option_name, $maps );
	}

	public static function clear_reviews_maps() {
		check_ajax_referer( $_POST['action'], 'security' );
		$result = delete_option( static::$option_name );
		wp_send_json_success( $result );
	}

	/**
	 * User IP address.
	 *
	 * @return string IP.
	 */
	public static function current_ip() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			// check ip from share internet.
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			// to check ip is pass from proxy.
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return apply_filters( 'wpb_get_ip', $ip );
	}

	/**
	 * Get avatar url.
	 *
	 * @param  int     $user User id.
	 * @param  integer $size Avatar size in px.
	 * @return string        Url
	 */
	public static function avatar_url( $user, $size = 50 ) {
		$avatar = get_avatar( $user, $size );

		if ( preg_match( '/src=(\'|")(.*?)(\'|")/i', $avatar, $matches ) && isset( $matches[2] ) ) {
			return str_replace('&#038;', '&', $matches[2]);
		}

		return get_avatar_url( $user, array(
			'size' => $size,
		) );
	}

	public static function ajax_get_reviews()
	{
		check_ajax_referer( $_POST['action'], 'security' );

        if( ! isset( $_POST['user_id'] )  ) {
            wp_send_json_error( __( 'Unable to get user reviews: bad request','reviewer' ) );
        }
        $user_id = intval( $_POST['user_id'] );

		if( $user_id <= 0 ) {
			wp_send_json_error( __( 'Unable to get user reviews: Choose an user ID greater than 0','reviewer' ) );
		}

		$user = new static( $user_id );

		if( $user->ID <= 0  ) {
			wp_send_json_error( __( 'Unable to get user reviews: User not found','reviewer' ) );
		}

		$reviews = $user->get_reviews();

		wp_send_json_success( $reviews );
	}
}
