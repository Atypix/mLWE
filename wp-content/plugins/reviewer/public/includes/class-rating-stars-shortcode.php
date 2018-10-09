<?php

/**
 * Reviewer Plugin v.3.5
 * Created by Michele Ivani
 */
class RWP_Rating_Stars_Shortcode
{
	// Instace of this class
	protected static $instance 			= null;
	protected $reviewer_shortcode_tag 	= 'rwp-reviewer-rating-stars';
	protected $users_shortcode_tag		= 'rwp-users-rating-stars';
	protected $auto_review_id 			= -1;
	protected $default_review			= null;
	protected $default_template			= null;
	protected $review;
	protected $template;

	function __construct()
	{
		$this->plugin_slug = 'reviewer';

		add_shortcode( $this->reviewer_shortcode_tag , array( $this, 'do_shortcode_reviewer' ) );
		add_shortcode( 'rwp_reviewer_rating_stars', array( $this, 'do_shortcode_reviewer' ) );
		add_shortcode( $this->users_shortcode_tag , array( $this, 'do_shortcode_users' ) );
		add_shortcode( 'rwp_users_rating_stars', array( $this, 'do_shortcode_users' ) );
	}

	public function do_shortcode_reviewer( $atts ) {
		
		extract( shortcode_atts( array(
			'id' 		=> '',
			'post'		=> get_the_ID(),
			'size'		=> '24',
			'stars'		=> '5',
		), $atts ) );

		$review_id 	= intval( $id ); 
		$post_id 	= intval( $post );
		$size 		= intval( $size );
		$stars 		= intval( $stars );


		// Get post reviews
		$reviews = get_post_meta( $post_id, 'rwp_reviews', true );
		
		// Check if user has inserted a valid review ID 
		if( ! isset( $reviews[ $review_id ] ) ) 
			return '<p>' . __('No review found! Insert a valid review ID.', $this->plugin_slug) . '</p>';

		// Get Review
		$this->review = $reviews[ $id ];

		// Check box type
		$review_type = $this->review_field('review_type', true);
		if( $review_type != 'PAR+UR' ) 
			return '<p>' . __('It\'s not possible to get reviewer scores of a users reviews box', $this->plugin_slug) . '</p>';

		// Get Template
		$templates 		= RWP_Reviewer::get_option('rwp_templates');
		$this->template = (isset( $templates[ $this->review['review_template'] ] )) ? $templates[ $this->review['review_template'] ] : array();
		
		$max = $this->template_field('template_maximum_score', true);
		$min = $this->template_field('template_minimum_score', true);

		$scores = $this->review_field('review_scores', true);
		$avg 	= RWP_Reviewer::get_avg( $scores );
		$score 	= $this->map_range( $avg, $min, $max, 0, $stars );
		ob_start();
		echo $this->get_stars( $score, $size, $stars );
		//RWP_Reviewer::pretty_print( $this->review );
		//RWP_Reviewer::pretty_print( $this->template );
		return ob_get_clean();
	}

	public function do_shortcode_users( $atts ) {
		
		extract( shortcode_atts( array(
			'id' 		=> '',
			'post'		=> get_the_ID(),
			'size'		=> '24',
			'stars'		=> '5',
			'template' 	=> '',
		), $atts ) );

		$review_id 		= intval( $id ); 
		$post_id 		= intval( $post );
		$size 			= intval( $size );
		$stars 			= intval( $stars );
		$template_id 	= trim( $template );

		if( $review_id == -1 ) { // Auto Review
			$post_type 	= get_post_type( $post_id );
			$auto_id 	= -1;
			$review_id 	= md5( 'rwp-'. $template_id .'-'. $post_type . '-' . $post_id . '-' . $auto_id );

			// Get Template
			$templates 		= RWP_Reviewer::get_option('rwp_templates');
			$this->template = (isset( $templates[ $template_id ] )) ? $templates[ $template_id ] : array();

			$max = $this->template_field('template_maximum_score', true);
			$min = $this->template_field('template_minimum_score', true);

			$scores 	= RWP_Reviewer::get_ratings_single_scores( $post_id, $review_id, $template_id);
			$avg 		= RWP_Reviewer::get_avg( $scores['scores'] );
			$count 		= $scores['count'];
			$score 		= $this->map_range( $avg, $min, $max, 0, $stars );
			$count_label = ( $count == 1 ) ? $this->template_field('template_users_count_label_s', true) : $this->template_field('template_users_count_label_p', true);

		} else {
			// Get post reviews
			$reviews = get_post_meta( $post_id, 'rwp_reviews', true );
			
			// Check if user has inserted a valid review ID 
			if( ! isset( $reviews[ $review_id ] ) ) 
				return '<p>' . __('No review found! Insert a valid review ID.', $this->plugin_slug) . '</p>';

			// Get Review
			$this->review = $reviews[ $id ];

			// Get Template
			$templates 		= RWP_Reviewer::get_option('rwp_templates');
			$this->template = (isset( $templates[ $this->review['review_template'] ] )) ? $templates[ $this->review['review_template'] ] : array();

			$max = $this->template_field('template_maximum_score', true);
			$min = $this->template_field('template_minimum_score', true);

			$singles 	= RWP_Reviewer::get_ratings_single_scores( $post_id, $review_id, $this->review_field('review_template', true) );
			$scores 	= RWP_Reviewer::get_users_overall_score( $singles, $post_id, $this->review_field('review_id', true) );
			$avg 		= $scores['score'];
			$count 		= $scores['count'];
			$score 		= $this->map_range( $avg, $min, $max, 0, $stars );
			$count_label = ( $count == 1 ) ? $this->template_field('template_users_count_label_s', true) : $this->template_field('template_users_count_label_p', true);
		}
		
		ob_start();
		echo $this->get_stars( $score, $size, $stars );
		echo '<div class="rwp-rating-stars-count">('. $count .' '. $count_label .')</div>';

		//RWP_Reviewer::pretty_print( $this->review );
		//RWP_Reviewer::pretty_print( $this->template );
		return ob_get_clean();
	}

	public function review_field( $field, $return = false ) {

		if ( is_null( $this->default_review ) ) {
			$this->default_review = RWP_Reviews_Meta_Box::get_review_fields();
		}

		$value = isset( $this->review[ $field ] ) ? $this->review[ $field ] : $this->default_review[ $field ]['default'];

		if( $return )
			return $value;

		echo $value;
	}

	public function template_field( $field, $return = false ) {

		if ( null == $this->default_template ) {
			$this->default_template = RWP_Template_Manager_Page::get_template_fields();
		}

		$value = isset( $this->template[ $field ] ) ? $this->template[ $field ] : $this->default_template[ $field ]['default'];

		if( $return )
			return $value;

		echo $value;
	}

	public function map_range( $val = 1, $valMin = 0, $valMax = 10, $newMin = 0, $newMax = 5 ) {
		return  ((($val - $valMin) * ($newMax - $newMin)) / ($valMax - $valMin)) + $newMin;
	}

	public function get_stars( $score = 0, $width = 24, $max = null ) {
		$def_width	= 24;
		$def_max 	= 5; 
		$max		= is_null( $max ) ? $this->template_field('template_maximum_score', true) : $max;
		$base_width = round( floatval( $width ) * floatval( $max ), 1);
		$fill_width = round( floatval( $width ) * floatval( $score ), 1);

		$base_style  = 'background-image: url('. $this->template_field('template_rate_image', true) .'); ';
		$base_style .= 'width: '. $base_width .'px; ';

		$fill_style = 'width: '. $fill_width .'px; ';
		
		if( $def_width != $width || $def_max != $max ) {
				$base_style .= 'height: '. $width .'px; ';
				$base_style .= 'background-size: '. $width .'px; ';
				$base_style .= 'background-position: 0 -'. $width .'px; ';
				
				$fill_style .= 'background-size: '. $width .'px; ';
				$fill_style .= 'height: '. $width .'px; ';
		}

		return '<div class="rwp-rating-stars" 
					 style="'. $base_style .'">
					 <div style="'. $fill_style .'"></div>
			  	</div>';
	}

	public static function get_instance() 
	{
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}