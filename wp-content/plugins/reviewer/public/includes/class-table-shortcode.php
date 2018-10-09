<?php

/**
 * Reviewer Plugin v.2
 * Created by Michele Ivani
 */
class RWP_Table_Shortcode
{
	// Instace of this class
	protected static $instance = null;
	protected $shortcode_tag = 'rwp-table';
	protected $template; // Table template
	protected $table; // Table
	protected $reviews; // Table reviews
	protected $pref; // Preferences

	protected $default_template = null;

	function __construct()
	{
		$this->plugin_slug = 'reviewer';

		add_shortcode( $this->shortcode_tag , array( $this, 'do_shortcode' ) );
		add_shortcode( 'rwp_comparison_table' , array( $this, 'do_shortcode' ) );
	}

	public function do_shortcode( $atts ) {

		extract( shortcode_atts( array(
			'id' 		=> '',
			'post'		=> get_the_ID()
		), $atts ) );

		$this->post_id = $post;
		$post_id = $post;

		// Get post tables
		$tables = get_post_meta( $this->post_id, 'rwp_tables', true );

		// Check if user has inserted a valid table ID
		if( ! isset( $tables[ $id ] ) )
			return '<p>' . __('No table found! Insert a valid table ID.', $this->plugin_slug) . '</p>';

		// All templates
		$templates_option = RWP_Reviewer::get_option( 'rwp_templates' );

		// Set table
		$this->table = $tables[ $id ];

		// Set template
		$this->template = $templates_option[ $this->table['table_template'] ];

		// Preferences
		$this->pref = RWP_Reviewer::get_option( 'rwp_preferences' );

		// Check if user has inserted one review in table
		if( empty( $this->table['table_reviews'] ) )
			return '<p>' . __('Add reviews to table.', $this->plugin_slug) . '</p>';

		// Get table reviews
		$this->reviews = $this->get_table_reviews( $this->table['table_reviews'] );

		//RWP_Reviewer::pretty_print($this->reviews); //return;

		// Sort comparison tables
		$this->sort_tables();

		$table_theme = $this->get_table_theme();

		ob_start();

		include 'themes/layout-table.php';

		//RWP_Reviewer::pretty_print( $this->table );
		//RWP_Reviewer::pretty_print( $this->template );
		//RWP_Reviewer::pretty_print( $this->reviews );

		return ob_get_clean();
	}

	private function sort_tables()
	{
		$sorting = isset( $this->table['table_sorting'] ) ?  $this->table['table_sorting'] : 'latest';
		switch ( $sorting ) {
			case 'reviewer_score' :
				usort( $this->reviews, array( $this, 'sort_tables_reviewer_score' ) );
				break;

			case 'users_score' :
				usort( $this->reviews, array( $this, 'sort_tables_users_score' ) );
				break;

			case 'combo' :
				usort( $this->reviews, array( $this, 'sort_tables_combo' ) );
				break;

			case 'latest':
			default:
				usort( $this->reviews, array( $this, 'sort_tables_latest' ) );
				break;
		}
	}

	public function sort_tables_latest( $a, $b )
	{
		if ($a["review_date"] == $b["review_date"])
        	return 0;

   		return ($a["review_date"] > $b["review_date"]) ? -1 : 1;
	}

	public function sort_tables_reviewer_score( $a, $b )
	{
		$avg_a = ( isset( $a['review_scores'] ) && is_array( $a['review_scores'] ) ) ?  RWP_Reviewer::get_avg( $a['review_scores'] ) : 0;
		$avg_b = ( isset( $b['review_scores'] ) && is_array( $b['review_scores'] ) ) ?  RWP_Reviewer::get_avg( $b['review_scores'] ) : 0;

		if (  $avg_a ==  $avg_b )
        	return 0;

   		return ( $avg_a >  $avg_b ) ? -1 : 1;
	}

	public function sort_tables_users_score( $a, $b )
	{
		$avg_a = ( isset( $a['review_ratings_scores']['scores'] ) && is_array( $a['review_ratings_scores']['scores'] ) ) ?  RWP_Reviewer::get_avg( $a['review_ratings_scores']['scores'] ) : 0;
		$avg_b = ( isset( $b['review_ratings_scores']['scores'] ) && is_array( $b['review_ratings_scores']['scores'] ) ) ?  RWP_Reviewer::get_avg( $b['review_ratings_scores']['scores'] ) : 0;

		if (  $avg_a ==  $avg_b )
        	return 0;

   		return ( $avg_a >  $avg_b ) ? -1 : 1;
	}

	public function sort_tables_combo( $a, $b )
	{
		$avg_ar = ( isset( $a['review_scores'] ) && is_array( $a['review_scores'] ) ) ?  RWP_Reviewer::get_avg( $a['review_scores'] ) : 0;
		$avg_br = ( isset( $b['review_scores'] ) && is_array( $b['review_scores'] ) ) ?  RWP_Reviewer::get_avg( $b['review_scores'] ) : 0;

		$avg_au = ( isset( $a['review_ratings_scores']['scores'] ) && is_array( $a['review_ratings_scores']['scores'] ) ) ?  RWP_Reviewer::get_avg( $a['review_ratings_scores']['scores'] ) : 0;
		$avg_bu = ( isset( $b['review_ratings_scores']['scores'] ) && is_array( $b['review_ratings_scores']['scores'] ) ) ?  RWP_Reviewer::get_avg( $b['review_ratings_scores']['scores'] ) : 0;

		$avg_a = ( $avg_ar + $avg_au ) / 2;
		$avg_b = ( $avg_br + $avg_bu ) / 2;

		if (  $avg_a ==  $avg_b )
        	return 0;

   		return ( $avg_a >  $avg_b ) ? -1 : 1;
	}

	private function get_table_theme() {

		if( isset( $this->table['table_theme'] ) && $this->table['table_theme'] != 'default' ) {
			return $this->table['table_theme'];
		}

		switch( $this->template['template_theme'] ) {

			case 'rwp-theme-2':
			case 'rwp-theme-6':
			case 'rwp-theme-8':
				return 'rwp-theme-2';
				break;

			case 'rwp-theme-3':
				return 'rwp-theme-3';
				break;

			case 'rwp-theme-4':
				return 'rwp-theme-4';
				break;

			case 'rwp-theme-1':
			case 'rwp-theme-5':
			default:
				return 'rwp-theme-1';
				break;
		}
	}

	public function get_table_reviews( $table_reviews )
	{
		global $wpdb;

		$posts = array();
		$reviews = array();
		$result = array();

		foreach( $table_reviews as $review_ids ) {

			// Extract ids
			$ids = explode(':', $review_ids);

			$posts[] 	= $ids[0];
			$reviews[ $ids[0] ][] = $ids[1];;
		}

		$posts = array_unique( $posts );

		$query  = "";
		$query .= "SELECT * FROM $wpdb->postmeta WHERE meta_key = 'rwp_reviews' AND ( ";

		$last_post_id = array_pop( $posts );

		foreach( $posts as $post_id ) {

			$query .= " post_id = $post_id OR";
		}

		$query .= " post_id = $last_post_id );";

		$post_meta = $wpdb->get_results( $query, ARRAY_A );

		foreach( $post_meta as $meta ) {

			$revs = unserialize( $meta['meta_value'] );

			foreach( $revs as $review_id => $review ) {

				if( get_post_status( $meta['post_id'] ) != 'publish' )
					continue;

				if( in_array( $review_id , $reviews[ $meta['post_id'] ] ) ){
					$review['review_post_id'] 			= $meta['post_id'];
					$review['review_ratings_scores']	= RWP_Reviewer::get_ratings_single_scores( $meta['post_id'], $review['review_id'], $review['review_template'] );
					$post = get_post( $meta['post_id'] , ARRAY_A);
					$review['review_date'] = strtotime( $post['post_date_gmt'] );
					$result[] = $review;
				}

			}

		}

		return $result;
	}

	protected function get_score_bar( $score, $theme = '', $size = 0, $horizontal = false ) {

		$max 	= floatval( $this->template_field('template_maximum_score', true) );
		$value 	= floatval( $score );
		$range 	= explode( '-', $this->template_field('template_score_percentages', true) );
		$low 	= floatval( $range[0] );
		$high 	= floatval( $range[1] );

		$pct = round ( (( $value / $max ) * 100), 1);

		if ( $pct < $low ) {
			$color = $this->template_field('template_low_score_color', true);
		} else if( $pct > $high ) {
			$color = $this->template_field('template_high_score_color', true);
		} else {
			$color = $this->template_field('template_medium_score_color', true);
		}

		if( $horizontal ) {

			$html  = '';
			$html .= '<div class="rwp-criteria-bar" style="width: '. $pct .'%; background: '. $color .';">';
			$html .= '<span class="rwp-criterion-score">'. RWP_Reviewer::format_number( $score ) .'</span>';
			$html .= '</div>';

		} else {

			$html  = '';
			$html .= '<div class="rwp-criteria-bar" style="height: '. (100 - $pct) .'%;"></div>';
			$html .= '<div class="rwp-criteria-bar-value" style="height: '. $pct .'%; background: '. $color .';" ><span class="rwp-criteria-score">'. RWP_Reviewer::format_number( $score ) .'</span></div>';
	   	}

		return $html;
	}

	protected function get_stars( $scores = array(), $stars = 5 ) {

		$avg 	= ( is_array( $scores ) ) ? RWP_Reviewer::get_avg( $scores ) : floatval( $scores );
		$value 	= RWP_Reviewer::get_in_base( $this->template_field('template_maximum_score', true), $stars, $avg);

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

		$html  = '<div class="rwp-str">';

		$j = 0;
		for ($i = 0; $i < $count; $i++) {

			$oe = ($i % 2 == 0) ? 'rwp-o' : 'rwp-e';
			$fx = ($j < $score) ? 'rwp-f' : 'rwp-x';

			$html .= '<span class="rwp-s '. $oe .' '. $fx .'" style="background-image: url('. $this->template_field('template_rate_image', true) .');"></span>';

			$j += .5;
		}

		$html .= '</div><!-- /stars -->';

		return $html;
	}

	protected function get_knob_params( $score ) {

		$max 	= floatval( $this->template_field('template_maximum_score', true) );
		$min 	= floatval( $this->template_field('template_minimum_score', true) );
		$value 	= floatval( $score );
		$range 	= explode( '-', $this->template_field('template_score_percentages', true) );
		$low 	= floatval( $range[0] );
		$high 	= floatval( $range[1] );

		$pct = round ( (( $value / $max ) * 100), 1);

		if ( $pct < $low ) {
			$color = $this->template_field('template_low_score_color', true);
		} else if( $pct > $high ) {
			$color = $this->template_field('template_high_score_color', true);
		} else {
			$color = $this->template_field('template_medium_score_color', true);
		}

		return 'data-min="'. $min .'" data-max="'. $max .'" data-fgColor="'. $color .'"; ';
	}

	protected function get_circle_score_params( $score ) {

		$max 	= floatval( $this->template_field('template_maximum_score', true) );
		$min 	= floatval( $this->template_field('template_minimum_score', true) );
		$value 	= floatval( $score );
		$range 	= explode( '-', $this->template_field('template_score_percentages', true) );
		$low 	= floatval( $range[0] );
		$high 	= floatval( $range[1] );

		$pct = round ( (( $value / $max ) * 100), 1);
		$ptc2 = round( ( $pct / 100 ), 1 ); // 70% => .7, the circle component required a value between 0 and 1

		if ( $pct < $low ) {
			$color = $this->template_field('template_low_score_color', true);
		} else if( $pct > $high ) {
			$color = $this->template_field('template_high_score_color', true);
		} else {
			$color = $this->template_field('template_medium_score_color', true);
		}

		return 'data-min="'. $min .'" data-max="'. $max .'" data-fgcolor="'. $color .'" data-pct="'. $ptc2 .'" data-value="'. $value .'"';
	}

	public function rating_image($echo = true)
	{
		$res = 'style="background-image: url('. $this->template['template_rate_image'] .')"';

		if (!$echo)
			return $res;

		echo $res;
	}

	public function stars( $value )
	{
		$count = $this->template['template_maximum_score'];

		for ($i=0; $i < $count; $i++) {

			$full = ( $value >= $i+1 ) ? 'rwp-full' : '';
			echo '<span class="rwp-star '. $full .'" '.$this->rating_image(false) .'></span>';
		}
	}

	public function style_total_score()
	{
		$bg_color = $this->template['template_total_score_box_color'];
		$full_width = ( $this->pref['preferences_authorization'] != 'disabled' ) ? '' : 'width: 100%;';
		echo 'style="background: '. $bg_color .'; '. $full_width  .'"';
	}

	public function style_letters($ret= false)
	{
		$bg_color = $this->template['template_total_score_box_color'];
		$r = 'style="background: '. $bg_color .'; "';

		if( $ret )
			return $r;

		echo $r;

	}

	public function style_users_score()
	{
		$bg_color = $this->template['template_users_score_box_color'];
		echo 'style="background: '. $bg_color .'; "';
	}

	public static function get_instance()
	{
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function score_bar_style( $review,  $key )
	{
		$max = $this->template['template_maximum_score'];
		$value = intval( $review['review_scores'][ $key ] );
		$range = explode( '-', $this->template['template_score_percentages'] );

		$res = ( $value / intval( $max ) ) * 100;
		$pct = round( $res, 1 );

		if ( $pct < intval($range[0]) ) {
			$color = $this->template['template_low_score_color'];
		} else if( $pct > intval($range[1]) ) {
			$color = $this->template['template_high_score_color'];
		} else {
			$color = $this->template['template_medium_score_color'];
		}

		return array( 'style="height: '. (100 - $pct) .'%;"', 'style="height: '. $pct .'%; background: '. $color .';"' );
	}

	public function data_for_knob( $review, $key )
	{
		$max = $this->template['template_maximum_score'];
		$min = $this->template['template_minimum_score'];
		$value = intval( $review['review_scores'][ $key ] );
		$range = explode( '-', $this->template['template_score_percentages'] );

		$res = ( $value / intval( $max ) ) * 100;
		$pct = round( $res, 1 );

		if ( $pct < intval($range[0]) ) {
			$color = $this->template['template_low_score_color'];
		} else if( $pct > intval($range[1]) ) {
			$color = $this->template['template_high_score_color'];
		} else {
			$color = $this->template['template_medium_score_color'];
		}

		echo 'data-min="'. $min .'" data-max="'. $max .'" data-fgColor="'. $color .'"; ';
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

}
