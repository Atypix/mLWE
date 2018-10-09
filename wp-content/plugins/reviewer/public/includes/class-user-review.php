<?php

/**
 * Reviewer Plugin v.3
 * Created by Michele Ivani
 */
class RWP_User_Review
{
	// Plugin Slug
	protected $plugin_slug = 'reviewer';

	// Instace of this class
	protected static $instance = null;

	function __construct()
	{
		add_action( 'wp_enqueue_scripts', array( $this, 'localize_script') );
	}

	public function localize_script()
	{
		$action_name = 'rwp_reviews_box_query_users_reviews';
		wp_localize_script( $this->plugin_slug .'-front-end-script', 'reviewerQueryURs', array('ajax_nonce' => wp_create_nonce( $action_name ), 'ajax_url' => admin_url('admin-ajax.php'), 'action' => $action_name ) );

		$action_name = 'rwp_reviews_box_query_all_users_reviews';
		wp_localize_script( $this->plugin_slug .'-front-end-script', 'reviewerQueryAllURs', array('ajax_nonce' => wp_create_nonce( $action_name ), 'ajax_url' => admin_url('admin-ajax.php'), 'action' => $action_name ) );
	}

	public static function query_users_reviews()
	{
		check_ajax_referer( $_POST['action'], 'security' );

        if( ! isset( $_POST['post_id'] ) || ! isset( $_POST['box_id'] ) || ! isset( $_POST['template_id'] ) ) {
            wp_send_json_error( __( 'Unable to get users reviews: bad request','reviewer' ) );
        }
        $post_id 		= intval( $_POST['post_id'] );
        $box_id  		= $_POST['box_id'];
        $template_id  	= $_POST['template_id'];

        $data = self::users_reviews( $post_id, $box_id, $template_id );
		if( !isset( $data['reviews'] ) || !is_array( $data['reviews'] ) ) {
            wp_send_json_error( __( 'Unable to get users reviews: query failed','reviewer' ) );
		}

		wp_send_json_success( $data );
	}

	public static function query_all_users_reviews()
	{
		check_ajax_referer( $_POST['action'], 'security' );

        if( ! isset( $_POST['templates'] ) || ! isset( $_POST['sorting'] ) || ! isset( $_POST['limit'] ) ) {
            wp_send_json_error( __( 'Unable to get users reviews: bad request','reviewer' ) );
        }

        $templates = explode(':', $_POST['templates']);
        if( !is_array( $templates ) ) {
        	$templates_option = get_option('rwp_templates', array());
        	$templates = array_keys( $templates_option );
        }
        $sorting  	= in_array( $_POST['sorting'], array('latest', 'top_score') ) ? $_POST['sorting'] : 'latest';
        $limit  	= intval( $_POST['limit'] );

        $data = self::all_users_reviews( $templates, $sorting, $limit );
		if( !is_array( $data ) ) {
            wp_send_json_error( __( 'Unable to get users reviews: query failed','reviewer' ) );
		}

		wp_send_json_success( $data );
	}

	public static function all_users_reviews( $templates = array(), $sort = 'latest', $limit = 5 )
	{
		global $wpdb;
		$result = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE meta_key LIKE 'rwp_rating%';", ARRAY_A );

		$templates_option = get_option('rwp_templates', array());

		$users_reviews = array();
		foreach( $result as $meta ) {

			$user_review = maybe_unserialize( $meta['meta_value'] );

			// Checks
            if( !isset( $user_review['rating_id'] ) ) { // $meta does not contain user review
                continue;
            }
            if( isset( $user_review['rating_status'] ) && $user_review['rating_status'] != 'published') { // The users review was not approved yet
            	continue;
            }
            if( !in_array($user_review['rating_template'], $templates ) ) {
            	continue;
            }

            // Preferences
			$preferences = RWP_Reviewer::get_option( 'rwp_preferences' );
			$step = isset( $preferences['preferences_step'] ) ? $preferences['preferences_step'] : 0.5;
			$precision = self::get_decimal_places( $step );

			$date_format = get_option('date_format');
			$time_format = get_option('time_format');
			$human_format = ( isset( $preferences['preferences_users_reviews_human_date_format'] ) && $preferences['preferences_users_reviews_human_date_format'] == 'yes' );


            // Post Meta ID
            $user_review['rating_meta_id'] = $meta['meta_id'];

            // Post title
            $user_review['rating_post_title'] = get_the_title( $user_review['rating_post_id'] );

            // Get user display name for registered user
			if( $user_review['rating_user_id'] > 0 ) {
				$display_name = get_user_by( 'id', $user_review['rating_user_id'] )->display_name;
				$user_review['rating_user_name'] = $display_name;
			}

			// Avatar image url
			$avatar = ( $user_review['rating_user_id'] == 0 && isset( $user_review['rating_user_email'] ) && !empty( $user_review['rating_user_email'] ) ) ? $user_review['rating_user_email'] : $user_review['rating_user_id'];
			// $url    = get_avatar_url( $avatar, array('size' => 60 ) );
			$url    = self::get_avatar_url( $avatar, 60 );
			$user_review['rating_user_avatar'] = $url;

			// Calculate overall score
			$user_review['rating_overall'] = round( self::get_avg( $user_review['rating_score'] ),  $precision);

			// Format date
			if ( $human_format ) {
				$user_review['rating_formatted_date'] = sprintf( __( '%s ago', 'reviewer' ), human_time_diff( intval( $user_review['rating_date'] ), current_time( 'timestamp' ) ) );
			} else {
				$user_review['rating_formatted_date'] = date_i18n(  $date_format . ', ' . $time_format , intval( $user_review['rating_date'] ) );
			}
			$user_review['rating_date'] = intval( $user_review['rating_date'] );

			// Review url
			$user_review['rating_url'] = add_query_arg( 'rwpurid', $user_review['rating_id'], get_permalink( $user_review['rating_post_id'] ) );

			// Template data
			$template = isset( $templates_option[ $user_review['rating_template'] ] ) ?  $templates_option[ $user_review['rating_template'] ] : array();

			$user_review['rating_template_maximum_score'] = isset( $template['template_maximum_score'] ) ? floatval($template['template_maximum_score']) : 10;
			$user_review['rating_template_minimum_score'] = isset( $template['template_minimum_score'] ) ? floatval($template['template_minimum_score']) : 0;

			$range  = isset( $template['template_score_percentages'] ) ? $template['template_score_percentages'] : '30-69';
			$range  = explode( '-', $range );
			$user_review['rating_template_low_pct'] = floatval( $range[0] );
			$user_review['rating_template_high_pct'] = floatval( $range[1] );

			$user_review['rating_template_low_score_color'] 	= isset( $template['template_low_score_color'] ) ? $template['template_low_score_color'] : '#323232';
			$user_review['rating_template_high_score_color'] 	= isset( $template['template_high_score_color'] ) ? $template['template_high_score_color'] : '#323232';
			$user_review['rating_template_medium_score_color'] 	= isset( $template['template_medium_score_color'] ) ? $template['template_medium_score_color'] : '#323232';

			$user_review['rating_template_rate_image'] 			= isset( $template['template_rate_image'] ) ? $template['template_rate_image'] : '';

            $users_reviews[] = $user_review;
		}

		//return $users_reviews;

		// Sort
		switch ( $sort ) {
			case 'top_score':
				usort( $users_reviews, array( 'RWP_Ratings_Widget', 'sort_score' ) );
				break;

			case 'latest':
			default:
				usort( $users_reviews, array( 'RWP_Ratings_Widget', 'sort_latest' ) );
				break;
		}

		// Limit
		$urs = array_slice( $users_reviews , 0, $limit );

		return $urs;
	}

	public static function users_reviews( $post_id = 1, $box_id = 0, $template_id = '' )
	{
		$users_reviews = get_post_meta( intval( $post_id ), 'rwp_rating_' . $box_id );
		$users_reviews = is_array( $users_reviews ) ? $users_reviews : array();

		$likes = get_post_meta( $post_id, 'rwp_likes', true );
		$likes = is_array( $likes ) ? $likes : array();

		// Template
		$templates = get_option('rwp_templates', array());
		$template  = (isset( $templates[ $template_id ] )) ? $templates[ $template_id ] : array();

		$order 		= isset( $template['template_criteria_order'] ) ? $template['template_criteria_order'] : null;
		$criteria 	= isset( $template['template_criterias'] ) ? $template['template_criterias'] : array();
		$order 		= ( is_null( $order ) ) ? array_keys( $criteria ) : $order;

		// Preferences
		$preferences = RWP_Reviewer::get_option( 'rwp_preferences' );
		$step = isset( $preferences['preferences_step'] ) ? $preferences['preferences_step'] : 0.5;
		$precision = self::get_decimal_places( $step );
		$networks = ( isset( $preferences['preferences_sharing_networks'] ) && is_array( $preferences['preferences_sharing_networks'] ) && count( $preferences['preferences_sharing_networks'] ) > 0 ) ? $preferences['preferences_sharing_networks'] : null;
		$thumbWidth = isset( $preferences['preferences_user_review_images']['field_dim']['width'] ) ? intval( $preferences['preferences_user_review_images']['field_dim']['width'] )  : 100;
		$thumbHeight = isset( $preferences['preferences_user_review_images']['field_dim']['height'] ) ? intval( $preferences['preferences_user_review_images']['field_dim']['height'] )  : 100;
		$date_format = get_option('date_format');
		$time_format = get_option('time_format');
		$human_format = ( isset( $preferences['preferences_users_reviews_human_date_format'] ) && $preferences['preferences_users_reviews_human_date_format'] == 'yes' );

		$avg_single_criteria = array();
		$overall = 0;

		foreach ($order as $i) {
			$avg_single_criteria[ $i ] = 0;
		}

		if( count( $users_reviews ) < 1 ) {
			return array( 'overall' => $overall, 'count' => 0, 'criteria' => $avg_single_criteria, 'reviews' => $users_reviews );
		}

		foreach ( $users_reviews as $i => $user_review ) {

			// Check if is a users review
			if( !isset( $user_review['rating_id'] ) ) {
				unset( $users_reviews[ $i ] );
				continue;
			}

			// Remove pending users reviews
			if( isset( $user_review['rating_status'] ) && $user_review['rating_status'] == 'pending' ) {
				unset( $users_reviews[ $i ] );
				continue;
			}

			// Socials URLs
			if( !is_null( $networks ) ) {
				$users_reviews[ $i ]['rating_socials_url'] = array();
				foreach ($networks as $network) {
					$fn = 'get_' . $network;
					$users_reviews[ $i ]['rating_socials_url'][$network] = RWP_Review_Shortcode::$fn( $post_id, $user_review['rating_id'] );
				}
			} else {
				$users_reviews[ $i ]['rating_socials_url'] = array();
			}

			// Get user display name for registered user
			if( $user_review['rating_user_id'] > 0 ) {
				$display_name = get_user_by( 'id', $user_review['rating_user_id'] )->display_name;
				$users_reviews[ $i ]['rating_user_name'] = $display_name;
			}

			// nl2br in comment
			//$users_reviews[ $i ]['rating_user_avatar'] = nl2br( $user_review['rating_comment'] );

			// Avatar image url
			$avatar = ( $user_review['rating_user_id'] == 0 && isset( $user_review['rating_user_email'] ) && !empty( $user_review['rating_user_email'] ) ) ? $user_review['rating_user_email'] : $user_review['rating_user_id'];
			// $url    = get_avatar_url( $avatar, array('size' => 100 ) );
			$url    = self::get_avatar_url( $avatar, 60 );
			$users_reviews[ $i ]['rating_user_avatar'] = $url;

			// Calculate overall score
			$users_reviews[ $i ]['rating_overall'] = round( self::get_avg( $user_review['rating_score'] ),  $precision);

			// Format date
			if ( $human_format ) {
				$users_reviews[ $i ]['rating_formatted_date'] = sprintf( __( '%s ago', 'reviewer' ), human_time_diff( intval( $user_review['rating_date'] ), current_time( 'timestamp' ) ) );
			} else {
				$users_reviews[ $i ]['rating_formatted_date'] = date_i18n(  $date_format . ', ' . $time_format , intval( $user_review['rating_date'] ) );
			}
			$users_reviews[ $i ]['rating_date'] = intval( $user_review['rating_date'] );

			// Append likes and dislike counts
			$users_reviews[ $i ]['rating_helpful']   = ( isset( $likes[ $user_review['rating_id'] ]['yes']  ) ) ? $likes[ $user_review['rating_id'] ]['yes'] : 0;
			$users_reviews[ $i ]['rating_unhelpful'] = ( isset( $likes[ $user_review['rating_id'] ]['no'] ) ) ? $likes[ $user_review['rating_id'] ]['no'] : 0;

			// Calculate avg of single criteria
			foreach ($order as $j) {
				$avg_single_criteria[$j] += ( isset( $user_review['rating_score'] ) && isset( $user_review['rating_score'][$j] ) )  ? $user_review['rating_score'][$j] : 0;
			}

			// Review Image
			if( isset( $user_review['rating_images'] ) && is_array( $user_review['rating_images'] ) ) {
				foreach ($user_review['rating_images'] as $attachment_id ) {
					$imageData = wp_get_attachment_image_src( $attachment_id, array( $thumbWidth * 2, $thumbHeight * 2 ) );
					$imageFullSize = wp_get_attachment_image_src( $attachment_id, 'full' );
					if( $imageData === false ) {
						continue;
					}
					$image = array(
						'id' => $attachment_id,
						'thumb_url' => $imageData[0],
						'thumb_real_width' => $imageData[1],
						'thumb_real_height' => $imageData[2],
						'thumb_width' => $thumbWidth,
						'thumb_height' => $thumbHeight,
						'src' => $imageFullSize[0],
						'w' => $imageFullSize[1],
						'h' => $imageFullSize[2],
					);

					$users_reviews[ $i ]['attachments'][] = $image;
				}
			} else {
				$users_reviews[ $i ]['attachments'] = array();
			}

			// Service fields
			$users_reviews[ $i ]['judging_ok'] = false;
			$users_reviews[ $i ]['judging_failed'] = false;
			$users_reviews[ $i ]['judging_loading'] = false;
			$users_reviews[ $i ]['judging_msg'] = '';
			$users_reviews[ $i ]['rating_highlighted'] = false;

		} // end main foreach

		$count = count( $users_reviews );

		if( $count >  0) {
			foreach ($order as $j) {
				$avg_single_criteria[$j] = round( $avg_single_criteria[$j] / $count, $precision);
			}
		} else {
			foreach ($order as $j) {
				$avg_single_criteria[ $j ] = 0;
			}
		}
		$overall = round( self::get_avg( $avg_single_criteria ),  $precision);

		return array( 'overall' => $overall, 'count' => $count, 'criteria' => $avg_single_criteria, 'reviews' => array_values( $users_reviews ) );
	}

	public static function old_rating( $post_id = 1, $review_id = 0 ) // not implemented in function above
	{
		$ratings 		= get_post_meta( $post_id, 'rwp_ratings', true );
		$old_ratings	= ( isset( $ratings[ $review_id ] ) ) ? $ratings[ $review_id ] : array( 'rating_count' => 0, 'rating_total_score' => 0 );

		if( $old_ratings['rating_count'] > 0 ) {
			return $old_ratings['rating_total_score'] / $old_ratings['rating_count'];
		}
		return 0;
	}

	public static function get_decimal_places($value)
	{
		$str_value = "" . $value;
		$parts = explode('.', $str_value);

		return (isset( $parts[1] ) ) ? strlen($parts[1]) : 0;
	}

	public static function get_avg( $scores = array() )
	{
		if (!is_array($scores))
			return 1;
		if( count( $scores) == 0 )
			return 0;

		$tot = array_sum($scores);
		$avg = $tot / count( $scores );
		return $avg;
	}

	public function get_plugin_slug()
	{
		return $this->plugin_slug;
	}

	public static function get_avatar_url( $user, $size = 60)
	{
		$avatar = get_avatar( $user, $size );

		if ( preg_match( '/src=(\'|")(.*?)(\'|")/i', $avatar, $matches ) && isset( $matches[2] ) ) {
			return str_replace('&#038;', '&', $matches[2]);
		}

		return get_avatar_url( $user, array(
			'size' => $size,
		) );
	}

	public static function get_instance()
	{
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public static function uploadImage()
    {
        check_ajax_referer( $_POST['action'], 'security' );

        $file_id = 'review_image';

        if( !isset( $_FILES[ $file_id ] ) || !is_array(  $_FILES[ $file_id ] ) ) {
            wp_send_json_error( __( 'Bad request', 'reviewer' ) );
        }

        // These files need to be included as dependencies when on the front end.
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );

        // Change the file name
        $filename   =  explode( '.', $_FILES[$file_id]['name'] );
        $ext        = $filename[ count( $filename ) - 1 ];
        $name       = uniqid('rwp-user-review-image-') . '.' . $ext;

        $_FILES[ $file_id ]['name'] = $name;

        // Let WordPress handle the upload.
        $attachment_id = media_handle_upload( $file_id, 0, array('post_title' => 'Reviewer Plugin - User review image'));

        if ( is_wp_error( $attachment_id ) ) {
            status_header(400);
            die( __( 'Unable to upload review image', 'reviewer' ) );
        }

        wp_send_json_success( $attachment_id );

        die();
	}

	public static function update_postmeta_scores($post, $box) {
		$reviews = get_post_meta( intval( $post ), 'rwp_rating_' . $box );
		$reviews = is_array( $reviews ) ? $reviews : array();

		$scores = [];
		foreach ($reviews as $review) {
			if( ! isset($review['rating_score']) || !is_array($review['rating_score']) ) {
				continue;
			}
			$avg = static::get_avg( $review['rating_score'] );
			$scores[] = $avg;
		}

		$score = static::get_avg( $scores );

		update_post_meta( $post, 'rwp_user_score', $score );
	}
}
