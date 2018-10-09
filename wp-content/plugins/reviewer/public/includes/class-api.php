<?php 

if ( !class_exists( 'RWP_API' ) ) {

	/**
	 * Reviewer Plugin v.3.0.0
	 * Created by Michele Ivani
	 */
	class RWP_API 
	{

		public static function get_review( $post_id = 1, $review_id = 0, $include_user_rating = false, $include_criteria_labels = false ) 
		{
			$result = array();

			$reviews = get_post_meta( $post_id, 'rwp_reviews', true ); // Get post reviews

			if( is_array( $reviews ) ) { // Check if the post has reviews

				foreach ($reviews as $review) { // Find the review defined in $review_id

					if( $review['review_id'] == $review_id ) {
						$result = $review;
						break;
					}
				}

				if( empty( $result ) ) { // If reviews box does not exists
					return $result;
				}
				
				$scores = isset( $result['review_scores'] ) ? $result['review_scores'] : array();
				$result['review_overall_score'] = RWP_Reviewer::get_avg( $scores );

				if( $include_user_rating ) { // Add user rating if it was requested
					$result['review_users_score'] =  RWP_Reviewer::get_ratings_single_scores( $post_id, $review_id, $result['review_template'] );
				}

				if( $include_criteria_labels ) {
					$templates = get_option( 'rwp_templates', array() );

					if( is_array( $templates ) && isset( $result['review_template'] ) && isset( $templates[ $result['review_template'] ] ) ) { // template exists
						$template 		= $templates[ $result['review_template'] ];
						$criteria 		= $template['template_criterias'];
						$scores  		= $result['review_scores'];
						$users_scores 	= ( $include_user_rating ) ? $result['review_users_score']['scores'] : array();

						$rs = array();
						$us = array();
						foreach ( $criteria as $key => $label ) { // Loop criteria
							if( isset( $scores[ $key ] ) ) { // Reviewer score
								$rs[ $key ] = array( 'label' => $label, 'score' => $scores[ $key ] );
							}
							if( isset( $users_scores[ $key ] ) ) { // Users score
								$us[ $key ] = array( 'label' => $label, 'score' => $users_scores[ $key ] );
							}
						}

						$result['review_scores'] = $rs;
						if ( $include_user_rating ) { $result['review_users_score']['scores'] = $us; }
					}
				}

			}

			return $result; // Return the review
		}

		public static function get_reviews_box( $post_id = 1, $review_id = 0, $include_user_rating = false, $include_criteria_labels = false ) {
			return self::get_review( $post_id, $review_id, $include_user_rating, $include_criteria_labels );
		}

		public static function get_post_reviews( $post_id = 1, $include_user_rating = false) 
		{
			$result = array();

			$reviews = get_post_meta( $post_id, 'rwp_reviews', true ); // Get post reviews

			if( is_array( $reviews ) ) { // Check if the post has reviews

				foreach ($reviews as $review) { // Find the review defined in $review_id

					$scores = isset( $review['review_scores'] ) ? $review['review_scores'] : array();
					$review['review_overall_score'] = RWP_Reviewer::get_avg( $scores );

					if( $include_user_rating ) { // Add user rating if it was requested

						$review['review_users_score'] =  RWP_Reviewer::get_ratings_single_scores( $post_id, $review['review_id'], $review['review_template'] );
					}

					$result[] = $review;		
				}
			}

			return $result; // Return the review
		}

		public static function get_post_reviews_boxes( $post_id = 1, $include_user_rating = false) {
			return self::get_post_reviews( $post_id, $include_user_rating );
		}

		public static function get_review_users_rating( $post_id = 1, $review_id = 0, $template = '', $include_criteria_labels = false ) 
		{
			$tmp = $template;
			if( intval( $review_id ) != -1 ) {
				$reviews = get_post_meta( $post_id, 'rwp_reviews', true ); // Get post reviews

				if( is_array( $reviews ) ) { // Check if the post has reviews

					foreach ($reviews as $review) { // Find the review defined in $review_id

						if( $review['review_id'] == $review_id ) {
							$tmp = $review['review_template'];
							break;
						}
					}
				}
			} else {
				$post_type 	= get_post_type( $post_id );
				$auto_id 	= -1;
				$review_id 	= md5( 'rwp-'. $template .'-'. $post_type . '-' . $post_id . '-' . $auto_id );
			}

			$result = RWP_Reviewer::get_ratings_single_scores( $post_id, $review_id, $tmp);

			if( $include_criteria_labels ) {
				$templates = get_option( 'rwp_templates', array() );

				if( is_array( $templates ) && !empty( $template ) && isset( $templates[ $template ] ) ) { // template exists
					$temp 		= $templates[ $template ];
					$criteria   = $temp['template_criterias'];
					$scores     = $result['scores'];

					$us = array();
					foreach ( $criteria as $key => $label ) { // Loop criteria
						if( isset( $scores[ $key ] ) ) { // Users score
							$us[ $key ] = array( 'label' => $label, 'score' => $scores[ $key ] );
						}
					}

					$result['scores'] = $us;
				}
			}

			return $result;

		}
		
		public static function get_reviews_box_users_rating( $post_id = 1, $review_id = 0, $template = '', $include_criteria_labels = false ) {
			return self::get_review_users_rating( $post_id, $review_id, $template, $include_criteria_labels );
		}

		public static function get_review_users_rating_in_html( $post_id = 0, $review_id = 1 ) 
		{
			$result = array();
			$reviews = get_post_meta( $post_id, 'rwp_reviews', true ); // Get post reviews

			if( is_array( $reviews ) ) { // Check if the post has reviews

				foreach ($reviews as $r) { // Find the review defined in $review_id

					if( $r['review_id'] == $review_id ) {
						$review = $r;
						break;
					}
				}

				$ratings = RWP_Reviewer::get_ratings_single_scores( $post_id, $review_id, $review['review_template'] );

				$templates 		= get_option('rwp_templates');
				$template_id 	= $review['review_template'];

				if (isset( $templates[ $template_id ] )) {

					$icon_url = $templates[ $template_id ]['template_rate_image'];
					$max = $templates[ $template_id ]['template_maximum_score'];

					echo $max;

					$ratings['html'] = self::get_stars( $ratings['scores'], $max, 5, $icon_url );

					$result = $ratings;
				}
					
			}

			return $result;
		}

		private static function get_stars( $scores = array(), $max = 10,  $stars = 5, $icon = '' ) {

			$avg 	= ( is_array( $scores ) ) ? RWP_Reviewer::get_avg( $scores ) : floatval( $scores );
			$value 	= RWP_Reviewer::get_in_base( $max, $stars, $avg);

			$int_value = intval( $value );
			$decimal_value = $value - $int_value;

			if( $decimal_value >= .4 && $decimal_value <= .6 ) {
				$score = $int_value + 0.5;
			} else if( $decimal_value > .6 ) {
				$score = $int_value + 1;
			} else {
				$score = $int_value;
			}

			$count = $stars * 2;

			$html  = '<div class="rwp-str rwp-api-rating-wrapper">';

			$j = 0;
			for ($i = 0; $i < $count; $i++) { 

				$oe = ($i % 2 == 0) ? 'rwp-o' : 'rwp-e';
				$fx = ($j < $score) ? 'rwp-f' : 'rwp-x';

				$html .= '<span class="rwp-s '. $oe .' '. $fx .'" style="background-image: url('. $icon .');"></span>';

				$j += .5;
			}
		
			$html .= '</div><!-- /stars -->';

			return $html;
		}

		public static function pretty_print( $data = array() )
		{
			echo '<pre>';
			print_r( $data );
			echo '</pre>';
		}

		public static function get_reviews_box_users_reviews( $post_id = 1, $review_id = 0, $template_id = '' ) 
		{
			if( intval( $review_id ) == -1 ) {
				$post_type 	= get_post_type( $post_id );
				$auto_id 	= -1;
				$review_id 	= md5( 'rwp-'. $template_id .'-'. $post_type . '-' . $post_id . '-' . $auto_id );
			}
			return RWP_User_Review::users_reviews( $post_id, $review_id, $template_id );
		}

		public static function get_users_reviews( $templates = array(),  $sort = 'latest', $limit = 5 ) 
		{
			return RWP_User_Review::all_users_reviews( $templates, $sort, $limit );
		}

	}
}
?>
