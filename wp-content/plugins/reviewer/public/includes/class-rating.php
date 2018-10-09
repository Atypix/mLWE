<?php

/**
 * Reviewer Plugin v.2
 * Created by Michele Ivani
 */
class RWP_Rating
{
	// Instance of this class
	protected static $instance = null;
	protected $templates_option;

	function __construct()
	{
		$this->plugin_slug = 'reviewer';
		add_action( 'wp_enqueue_scripts', array( $this, 'localize_script') );
	}

	public function localize_script()
	{
		$action_name = 'rwp_ajax_action_rating';
		wp_localize_script( $this->plugin_slug .'-front-end-script', 'reviewerRatingObj', array('ajax_nonce' => wp_create_nonce( $action_name ), 'ajax_url' => admin_url('admin-ajax.php'), 'action' => $action_name ) );

		$action_name = 'rwp_ajax_action_like';
		wp_localize_script( $this->plugin_slug .'-front-end-script', 'reviewerJudgeObj', array('ajax_nonce' => wp_create_nonce( $action_name ), 'ajax_url' => admin_url('admin-ajax.php'), 'action' => $action_name ) );

		//$action_name = 'rwp_ajax_action_refresh_captcha';
		//wp_localize_script( $this->plugin_slug . '-public-script', 'rwpCaptchaObj', array('ajax_nonce' => wp_create_nonce( $action_name ), 'ajax_url' => admin_url('admin-ajax.php'), 'action' => $action_name ) );

	}

	public static function refresh_captcha() {

		$fields = array(
			'post_id',
			'review_id'
		);

		$res = array( 'code' => 400, 'data'=> array( 'msg' => __( 'The form was not submitted correctly!', 'reviewer' ) ) );

		// Check if fields are set
		foreach ($fields as $key) {
			if( !isset( $_POST[ $key ] ) )
				die( json_encode( $res ) );
		}

		$post_id 	= intval( $_POST['post_id'] );
		$review_id 	= intval( $_POST['review_id'] );

		$captcha = RWP_Captcha::get_instance();
		$image = $captcha->generate( $post_id, $review_id );

		$res['code'] = 200;
		$res['captcha'] = $image;

		die( json_encode( $res ) );
	}

	public static function ajax_callback_like() {

		check_ajax_referer( $_POST['action'], 'security' );

		$fields = array(
			'post_id',
			'rating_id',
			'method',
			'user_id'
		);

		// Check if fields are set
		foreach ($fields as $key) {
			if( !isset( $_POST[ $key ] ) )
				wp_send_json_error( __( 'Bad request! :(', 'reviewer' ) );
		}

		$rating_id 	= $_POST['rating_id'];
		$post_id 	= $_POST['post_id'];
		$method 	= $_POST['method'];

		$user 		= wp_get_current_user();
		$user_id 	= (($user instanceof WP_User) && $user->ID == intval($_POST['user_id']) ) ?  $user->ID : 0;;

		$cookie_name	= 'rwp_like_' . $rating_id . '_' . $user_id;

		// Check if user already rated
		if( isset( $_COOKIE[ $cookie_name ] ) ) {
			wp_send_json_error( __( 'You already judged this review', 'reviewer' ) );
		}

		// Blacklist
		$blacklist = get_post_meta( $post_id, 'rwp_likes_blacklist', true );

		if( isset( $blacklist[ $rating_id ] ) && in_array( $user_id, $blacklist[ $rating_id ] ) ) {
			wp_send_json_error( __( 'You already judged this review', 'reviewer' ) );
		}

		// Likes
		$likes = get_post_meta( $post_id, 'rwp_likes', true );

		// Check if rating exists
		if( !isset( $likes[ $rating_id ] ) ) {
			if( is_array( $likes ) ) {
				$likes[ $rating_id ]['yes'] = 0;
				$likes[ $rating_id ]['no'] = 0;
			} else {
				$likes = array();
				$likes[ $rating_id ]['yes'] = 0;
				$likes[ $rating_id ]['no'] = 0;
			}
		}

		// Incrementing count
		if( $method == 'like' ) {
			$likes[ $rating_id ]['yes']++;
		} else {
			$likes[ $rating_id ]['no']++;
		}

		// Update
		$process = update_post_meta( $post_id, 'rwp_likes', $likes);

		// Check process res
		if ($process === FALSE) {
			 wp_send_json_error( __( 'Unable to judge this review', 'reviewer' ) );
		}

		// Success!
		$res = array(
			'msg' => __( 'Done', 'reviewer' ),
			'helpful' => $likes[ $rating_id ]['yes'],
			'unhelpful' => $likes[ $rating_id ]['no']
		);

		// Set the cookie
		setcookie( $cookie_name , 'true', time() + 60 * 60 * 24 * 30, '/' );

		// Update Blacklist
		if( $user_id > 0 ) {
			if( is_array( $blacklist ) ) {
				$blacklist[ $rating_id ][] = $user_id;
			} else {
				$blacklist = array();
				$blacklist[ $rating_id ][] = $user_id;
			}

			update_post_meta( $post_id, 'rwp_likes_blacklist', $blacklist);
		}

		wp_send_json_success( $res );
	}

	public static function ajax_callback()
	{

		// var_dump( $_POST); die();

		$res = array( 'code' => 400, 'data'=> array( 'msg' =>  '') );

		if( RWP_DEMO_MODE ) {
			wp_send_json_error( __( 'The user review feature is disabled for demo mode.', 'reviewer' ) );
		}

		$fields = array(
			'review_id',
            'post_id',
            'user_id',
            'user_email',
            'scores',
            'user_name',
            'title',
            'comment',
            'method',
            'template',
            'captcha',
        );

        // Fix no scores.
        if( !isset( $_POST['scores'] ) ) {
        	$_POST['scores'] = 0;
        }

        // Check if fields are set
		foreach ($fields as $key) {
			if( !isset( $_POST[ $key ] ) )
				wp_send_json_error( __( 'The form was not submitted correctly', 'reviewer' ) );
		}

		// Review ID
		$review_id 	= $_POST['review_id'];

		// Post ID
		$post_id 	= intval( $_POST['post_id'] );

		// User ID
		$user 		= wp_get_current_user();
		$user_id 	= (($user instanceof WP_User) && $user->ID == intval($_POST['user_id'] ) ) ?  $user->ID : 0;

		// Preferences
		$preferences_option = RWP_Reviewer::get_option( 'rwp_preferences' );

		$rating_limit = self::preferences_field( 'preferences_rating_limit', $preferences_option, true );

		if( $rating_limit == 'single' ) {
			// Cookie name
			$cookie_name 	= 'rwp_rating_' . $post_id .'_' . $review_id .'_' . $user_id;

			// Check if user already rated
			if( isset( $_COOKIE[ $cookie_name ] ) ) {
				wp_send_json_error( __( 'You already reviewed this item', 'reviewer' ) );
			}

			// Blacklist
			$blacklist = get_post_meta( $post_id, 'rwp_rating_blacklist', true );

			if( isset( $blacklist[ $post_id . '-' . $review_id ] ) && in_array( $user_id, $blacklist[ $post_id . '-' . $review_id ] ) ) {
				wp_send_json_error( __( 'You already reviewed this item', 'reviewer' ) . '!!' );
			}
		}

		// Get post reviews
		$reviews = get_post_meta( $post_id, 'rwp_reviews', true );

		// Review
		$review = ( isset( $reviews[ $review_id ] ) ) ? $reviews[ $review_id ] : array();

		// Templates
		$templates_option = RWP_Reviewer::get_option( 'rwp_templates' );

		if( !isset( $templates_option[ $_POST['template'] ] )) {
			wp_send_json_error( __( 'The form was not submitted correctly', 'reviewer' ) );
		}

		// Review Template
		$template = $templates_option[ $_POST['template'] ];

		// Review rating options
		//$rating_options = self::review_field( 'review_user_rating_options', $review, true );
		$rating_options = self::template_field('template_user_rating_options', $template, true);

		// Validate form fields
		$errors = array();

		// Captcha
		if( isset( $preferences_option['preferences_users_reviews_captcha']['enabled'] ) && $preferences_option['preferences_users_reviews_captcha']['enabled'] ) {

			// Send reCapatcha response token
			$response = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', array(
				'body' => array(
					'secret' 	=> isset( $preferences_option['preferences_users_reviews_captcha']['secret_key'] ) ? $preferences_option['preferences_users_reviews_captcha']['secret_key'] : '',
					'response' 	=> $_POST['captcha']
				)
			) );

            // Check for error
			if ( is_wp_error( $response ) ) {
				wp_send_json_error( __( 'Secure code is not correct', 'reviewer' ) . '!!' );
			}

             // Parse remote HTML file
			$data = wp_remote_retrieve_body( $response );
			$data = json_decode( $data, true );

			if( !isset( $data['success'] ) ||  $data['success'] === false ) {
				wp_send_json_error( __( 'Secure code is not correct', 'reviewer' ) );
			}

		}

		// Name
		if( in_array( 'rating_option_name', $rating_options ) ) {

			if( $user_id == 0 ) {

				$user_name = trim( $_POST['user_name'] );

				if( empty( $user_name ) )
					$errors[] = __( 'Your name is required', 'reviewer' );

				//$user_name = wp_kses_post( $user_name );
				$user_name = sanitize_text_field( stripslashes_deep( $user_name ) );
			} else
				$user_name = '';

		} else {
			$user_name = '';
		}

		// Email
		if( in_array( 'rating_option_email', $rating_options ) ) {

			if( $user_id == 0 ) {

				$email = trim( $_POST['user_email'] );

				if( empty( $email ) ) {
					$errors[] = __( 'Your email is required', 'reviewer' );
				} elseif( !is_email( $email ) )
				    $errors[] = __( 'Your email is not valid', 'reviewer' );

			} else {
				$email = '';
			}

		} else
			$email = '';


		// Title
		if( in_array( 'rating_option_title', $rating_options ) ) {

			$title = trim( $_POST['title'] );

			// if( empty( $title ) )
			// 	$errors[] = __( 'A review title is required', 'reviewer' );

			$title = sanitize_text_field( stripslashes_deep( $title ) );

			// Check limits
			$limit 	= self::preferences_field( 'preferences_rating_title_limits', $preferences_option, true );
			$range 	= explode( '-', $limit );
			$min 	= intval( $range[0] );
			$max 	= ( $range[1] == 'inf' ) ? false : intval( $range[1] );

			$len = strlen( $title );

			if( $len < $min ) {
				$errors[] = __( 'A review title is required', 'reviewer' ) . '. ' . sprintf( __( 'The minimum number of characters is %d for review title', 'reviewer' ), $min );
			}

			if( $max !== false && $len > $max ) {
				$errors[] = sprintf( __( 'The maximum number of characters is %d for review title', 'reviewer' ), $max);
			}

			//$title = wp_kses_post( $title );


		} else
			$title = '';


		// Comment
		if( in_array( 'rating_option_comment', $rating_options ) ) {

			$comment = trim( $_POST['comment'] );

			// if( empty( $comment ) )
			// 	$errors[] = __( 'A review comment is required', 'reviewer' );

			$comment = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", stripslashes_deep( $comment ) ) ) );

			// Check limits
			$limit 	= self::preferences_field( 'preferences_rating_comment_limits', $preferences_option, true );
			$range 	= explode( '-', $limit );
			$min 	= intval( $range[0] );
			$max 	= ( $range[1] == 'inf' ) ? false : intval( $range[1] );

			$len = strlen( $comment );

			if( $len < $min ) {
				$errors[] = __( 'A review comment is required', 'reviewer' ) . '. ' . sprintf( __( 'The minimum number of characters is %d for review comment', 'reviewer' ), $min );
			}

			if( $max !== false && $len > $max ) {
				$errors[] = sprintf( __( 'The maximum number of characters is %d for review comment', 'reviewer' ), $max);
			}

			//$comment = wp_kses_post( $comment );

		} else
			$comment = '';

		// Method
		$method	 = trim( $_POST['method'] );

		// Criteria count
		$criteria_count = count($template['template_criterias']);

		// Score
		$score = array();

		switch ( $method ) {

			case 'five_stars':

				$score_value = floatval($_POST['scores']);

				// Check if the 0 <= score <= 5
				if( $score_value < 0 || $score_value > 5)
					wp_send_json_error( __( 'The form was not submitted correctly', 'reviewer' ) );

				$score_value = RWP_Reviewer::get_in_base( 5, $template['template_maximum_score'], $score_value);
				$precision   = RWP_Reviewer::get_decimal_places( self::preferences_field( 'preferences_step', $preferences_option, true ) );

				$score_value = round( $score_value, $precision );

				$order 		= self::template_field('template_criteria_order', $template, true);
				$criteria 	= self::template_field('template_criterias', $template, true);
				$order 		= ( $order == null ) ? array_keys( $criteria) : $order;

				foreach ($order as $i) {
					$score[ $i ] = $score_value;
				}
				break;

			case 'full_five_stars':

				if( is_array( $_POST['scores'] ) && count( $_POST['scores'] ) == $criteria_count ) {

					foreach ($_POST['scores'] as $s) {

						$score_value = floatval( $s['val'] );

						// Check if the 0 <= score <= 5
						if( $score_value < 0 || $score_value > 5)
							wp_send_json_error( __( 'The form was not submitted correctly', 'reviewer' ) );

						$score_value = RWP_Reviewer::get_in_base( 5, $template['template_maximum_score'], $score_value);
						$precision   = RWP_Reviewer::get_decimal_places( self::preferences_field( 'preferences_step', $preferences_option, true ) );

						$score_value = round( $score_value, $precision );

						$score[ $s['i'] ] = $score_value;
					}

				} else {

					for ($i=0; $i < $criteria_count; $i++) {
						$score[] = 0;
					}
				}
				break;

			default: // Slider rating mode

				if( is_array( $_POST['scores'] ) && count( $_POST['scores'] ) == $criteria_count ) {

					foreach ($_POST['scores'] as $s) {
						$score[ $s['i'] ] = $s['val'];
					}

				} else {

					for ($i=0; $i < $criteria_count; $i++) {
						$score[] = 0;
					}
				}
				break;
		}

		// Validate Scores
		$allow_zero_pref = self::preferences_field( 'preferences_rating_allow_zero', $preferences_option, true );

		if( $allow_zero_pref == 'no') {

			foreach ($score as $value) {

				if( $value == 0 ) {
					$errors[] = __( 'Scores with zero value are not allowed', 'reviewer' );
					break;
				}
			}
		}

		// Validate Images
		$reviewImages = ( isset($_POST['images']) && is_array( $_POST['images'] ) ) ? $_POST['images'] : array();
		$imageSettings = self::preferences_field( 'preferences_user_review_images', $preferences_option, true );
		$vmin = isset( $imageSettings['field_min'] ) ? intval( $imageSettings['field_min'] ) : 0;
		if( count( $reviewImages ) < $vmin  ) {
			if( $vmin > 1 ){
				$errors[] = sprintf( __( 'You need to upload at least %d images', 'reviewer' ), $vmin);
			} else {
				$errors[] = sprintf( __( 'You need to upload at least %d image', 'reviewer' ), $vmin);
			}
		}

		$images = array();
		foreach ( $reviewImages as $image ) {
			$images[] = intval( $image );
		}

		// Check errors
		if( !empty( $errors ) ) {
			wp_send_json_error( $errors );
		}

		// Status
		$moderation = self::preferences_field( 'preferences_rating_before_appears', $preferences_option, true );

		$status = ( $moderation == 'nothing'  ) ? 'published' : 'pending';

		// Update pending count
		if( $status == 'pending' ) {
			$key = 'rwp_pending_ratings';
			$pend_count = get_option( $key, 0 );
			$pend_count++;
			update_option( $key, $pend_count );
		}

		$rating = array(
			'rating_id'				=> uniqid('rwp_rating_'),
			'rating_post_id'		=> $post_id,
			'rating_review_id'		=> $review_id,
			'rating_score'	 		=> $score,
			'rating_user_id'		=> $user_id,
			'rating_user_name'		=> $user_name,
			'rating_user_email'		=> $email,
			'rating_title'			=> $title,
			'rating_comment'		=> $comment,
			'rating_images'			=> $images,
			'rating_date'			=> current_time( 'timestamp' ),
			'rating_status'			=> $status,
			'rating_verified'		=> false,
			'rating_template'		=> $template['template_id'],
		);

		// RWP_Reviewer::pretty_print($rating); die();

		// Apply rwp_before_saving_review filter hook
		$rating = apply_filters('rwp_before_saving_review', $rating);

		// Save ratings
		$process = add_post_meta( $post_id, 'rwp_rating_' . $review_id, $rating );

		// Check process res
		if ($process === FALSE) {
			wp_send_json_error( $template['template_failure_message'] );
		}

		$review_db_id = $process;

		// Map the review to user.
		RWP_User::map_review( $user_id, $review_db_id );

		// Likes
		$likes = get_post_meta( $post_id, 'rwp_likes', true );

		if( is_array( $likes ) ) {
			$likes[ $rating['rating_id'] ] = array('yes' => 0, 'no' => 0, 'post_id' => $post_id, 'review_id' => $review_id );
		} else {
			$likes = array( '' . $rating['rating_id'] => array('yes' => 0, 'no' => 0, 'post_id' => $post_id, 'review_id' => $review_id ) );
		}

		// Update
		update_post_meta( $post_id, 'rwp_likes', $likes);

		if( $moderation == 'nothing' ) {

			// Success!
			$res['code'] = 200;
			$res['msg'] = $template['template_success_message'];


		} else {

			// Success!
			$res['code'] = 201;
			$res['msg'] = self::template_field('template_moderation_message', $template, true);

		}

		if( $rating_limit == 'single' ) {
			// Set the cookie
			setcookie( $cookie_name , 'true', time() + 60 * 60 * 24 * 30, '/' );

			// Update Blacklist
			if( $user_id > 0 ) {
				if( is_array( $blacklist ) ) {
					$blacklist[ $post_id . '-' . $review_id ][] = $user_id;
				} else {
					$blacklist = array();
					$blacklist[ $post_id . '-' . $review_id ][] = $user_id;
				}

				update_post_meta( $post_id, 'rwp_rating_blacklist', $blacklist);
			}
		}

		// Notification
		$notification_pref 	= intval( self::preferences_field( 'preferences_notification', $preferences_option, true ) );
		$notification_email = self::preferences_field( 'preferences_notification_email', $preferences_option, true );

		if( $notification_pref > 0 ) { // Check if notification is enabled

			$notification_key 		= 'rwp_notification_ratings';
			$notification_ratings 	= RWP_Reviewer::get_option( $notification_key );
			$notification_ratings[]	= $review_db_id;
			$notification_count	 	= count( $notification_ratings );

			if( $notification_count >= $notification_pref ) {
				static::schedule_reviews_notice( $notification_ratings, $notification_email );
			} else {
				update_option( $notification_key, $notification_ratings );
			}
		}

		// Apply rwp_after_saving_review filter hook
		$rating = do_action('rwp_after_saving_review', $rating, $review_db_id);

		static::schedule_postmeta_updating($post_id, $review_id);

		wp_send_json_success( $res );

	}

	public static function send_notification( $args = array(), $email = '' )
	{
		global $wpdb;

		if( !is_email( $email ) || count( $args ) < 1 ) return false;

		// Query reviews
		$result = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE meta_id IN (". implode(',', $args) .")", ARRAY_A );

		$reviews = array();
		foreach ($result as $meta) {
			$r = maybe_unserialize( $meta['meta_value'] );

			if( !isset( $r['rating_id']) ) {
				continue;
			}

			$review = new stdClass();
			$review->post_title = get_the_title( $r['rating_post_id'] );
			$review->post_permalink = get_permalink( $r['rating_post_id'] );

			$user_id = intval($r['rating_user_id']);
			if( $user_id > 0 ) {
				$user = get_userdata( $user_id );
				$display_name = $user->display_name;
				$user_email = $user->user_email;
			} else {
				$display_name = $r['rating_user_name'];
				$user_email = empty($r['rating_user_email']) ? '<em>User has no email</em>' : $r['rating_user_email'];
			}
			$review->author = $display_name;
			$review->author_email = $user_email;

			$review->title 		= strlen( $r['rating_title'] ) > 100 ? substr( $r['rating_title'], 0, 100 )."..." : $r['rating_title'];
			$review->comment 	= strlen( $r['rating_comment'] ) > 200 ? substr( $r['rating_comment'], 0, 200 )."..." : $r['rating_comment'];

			$review->score = round( RWP_User_Review::get_avg( $r['rating_score'] ) );

			$reviews[] = $review;
		}

		ob_start();
		include(RWP_PLUGIN_PATH . 'share/email-templates/template.php');
		$message = ob_get_clean();

		$domain_name =  preg_replace('/^www\./','',$_SERVER['SERVER_NAME']);

		$to 		= $email;
		$subject	= __('[Reviewer] New reviews', 'reviewer');
		$headers	= array(
			'From: Reviewer Plugin <do-not-reply@' . $domain_name,
			'Content-Type: text/html; charset=UTF-8'
		);

		$result = wp_mail( $to, $subject, $message, $headers );

		if( $result ) {
			update_option( 'rwp_notification_ratings', array() );
		} else {
			update_option( 'rwp_notification_ratings', $args );
		}
	}

	public static function review_field( $field, $review, $return = false ) {

		$default_review = RWP_Reviews_Meta_Box::get_review_fields();

		$value = isset( $review[ $field ] ) ? $review[ $field ] : $default_review[ $field ]['default'];

		if( $return )
			return $value;

		echo $value;
	}

	public static function preferences_field( $field, $preferences, $return = false ) {

		$default_preferences = RWP_Preferences_Page::get_preferences_fields();

		$value = isset( $preferences[ $field ] ) ? $preferences[ $field ] : $default_preferences[ $field ]['default'];

		if( $return )
			return $value;

		echo $value;
	}

	public static function template_field( $field, $template, $return = false ) {

		$default_template = RWP_Template_Manager_Page::get_template_fields();

		$value = isset( $template[ $field ] ) ? $template[ $field ] : $default_template[ $field ]['default'];

		if( $return )
			return $value;

		echo $value;
	}

	public static function get_instance()
	{
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public static function schedule_reviews_notice( $reviews = array(), $email = '') {
		if(! wp_next_scheduled( 'reviewer_notify_about_new_reviews' ) ) {
			wp_schedule_single_event( time() + 10, 'reviewer_notify_about_new_reviews', array( $reviews, $email ) );
	    }
	}

	public static function unschedule_reviews_notice() {
		wp_clear_scheduled_hook('reviewer_notify_about_new_reviews');
	}

	public static function schedule_postmeta_updating($post, $box) {
		if(! wp_next_scheduled( 'reviewer_update_postmeta_of_box_scores' ) ) {
			wp_schedule_single_event( time() + 5, 'reviewer_update_postmeta_of_box_scores', array( $post, $box ) );
	    }
	}

	public static function unschedule_postmeta_updating() {
		wp_clear_scheduled_hook('reviewer_update_postmeta_of_box_scores');
	}
}
