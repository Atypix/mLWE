<?php

class RWP_Reviews_Widget extends WP_Widget {

	public $plugin_slug;
	public $widget_fields;
	public $templates;

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct()
	{
		// widget actual processes

		$this->plugin_slug = 'reviewer';
		$this->templates = RWP_Reviewer::get_option('rwp_templates');

		add_action( 'init', array( $this ,'set_widget_fields') );

		$options = array(
			'description'	=> __( 'Reviewer Plugin Widget allows you to display your latest, top score, top rated reviews boxes.', $this->plugin_slug),
			'name'			=> 'Reviewer | Reviews Boxes'
		);

		parent::__construct('rwp-reviews-widget', '', $options);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance )
	{
		//$this->pretty_print($instance);

		extract( $instance );

		echo $args['before_widget'];

		if(  isset( $title ) && !empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];

		// Templates
		if( isset( $template ) && !is_array( $template ) ) {
			$template = ( $template == 'all' ) ? array_keys( $this->templates ) : array( $template );
		} elseif ( !isset( $template ) ) {
			$template = array_keys( $this->templates );
		}

		// Theme
		$theme = $this->widget_field( $instance, 'theme', true );
		// Sort
		$sort = $this->widget_field( $instance, 'to_display', true );
		//Limit
		$limit = $this->widget_field( $instance, 'to_display_count', true );

		// Get Reviews
		$reviews = self::query_reviews( $template, $sort, $limit );

		echo '<ul class="rwp-widget-reviews rwp-widget-'. $theme .'">';

		foreach ($reviews as $i => $review ) {

			//$this->pretty_print($review);

			$rank_num = '';

			if( $sort != 'latest' ) {
				$rank_num = '<span class="rwp-ranking-number">'. ($i + 1) .'</span>';
			}

			$has_rank = ( !empty( $rank_num ) ) ? 'rwp-has-ranking' : '';

			$review_title = '';
			$review_title_option = $this->review_field('review_title_options', $review, true);

                switch ( $review_title_option ) {
                    case 'hidden':
                        break;

                    case 'post_title':
                    	$post_id = $this->review_field('review_post_id', $review, true );
                       	$review_title = get_the_title( $post_id );
                        break;

                    default:
                        $review_title = $this->review_field('review_title', $review, true );
                        break;
                }

			switch ( $theme ) {

				case 'theme-3':
					// Has Image
					$img = $review['review_image'];
					if( $review['review_use_featured_image'] == 'no' ) {
						$has_image = (!empty( $img ) ) ? true : false;
					} else {
						$has_image =  has_post_thumbnail( $review['review_post_id'] );
						$thumb 	 = wp_get_attachment_image_src( get_post_thumbnail_id( $review['review_post_id']), 'full' );
						$img 	 = $thumb[0];
					}
					$hi 		= ($has_image) ? 'rwp-has-image' : 'rwp-no-image';

					echo '<li class="'. $hi .' '. $has_rank .'" >';
						echo $rank_num;

						echo '<a href="'. $review['review_permalink'] .'">';

							echo ($has_image) ? '<div class="rwp-w-icon" style="background-image: url('. $img .');"></div>' : '';

							echo '<span class="rwp-title">'. $review_title .'</span>';

							$review_template = $this->templates[ $review[ 'review_template' ] ];

							echo RWP_Reviewer::get_stars( $review['review_score']['overall'], $review_template );

							if( $review['review_type'] == 'UR' ) {
								$count = $review['review_score']['count'];
								$count_label = ( $count == 1 ) ? self::template_field('template_users_count_label_s', $template, true) : self::template_field('template_users_count_label_p', $template, true);
								echo '<div class="rwp-rating-stars-count">('. $count .' '. $count_label .')</div>';
							}
						echo '</a>';
					echo '</li>';

					break;

				case 'theme-2':

					echo '<li class="'. $has_rank .'">';
						echo $rank_num;
						echo '<span class="rwp-overall" style="background-color: '. $review['review_color'] .';">'.$review['review_score']['overall'].'<em>'. $review['review_score']['label'] .'</em></span>';
						echo '<a href="'. $review['review_permalink'] .'">'. $review_title .'</a>';
					echo '</li>';
					break;

				case 'theme-1':
				default:
					// Has Image
					$img = $review['review_image'];
					if( $review['review_use_featured_image'] == 'no' ) {
						$has_image = (!empty( $img ) ) ? true : false;
					} else {
						$has_image =  has_post_thumbnail( $review['review_post_id'] );
						$thumb 	 = wp_get_attachment_image_src( get_post_thumbnail_id( $review['review_post_id']), 'full' );
						$img 	 = $thumb[0];
					}
					$hi = ($has_image) ? 'rwp-has-image' : 'rwp-no-image';

					echo '<li class="'. $hi .' '. $has_rank .'" >';
						echo $rank_num;

						$bg2 = ($has_image) ? 'style="background-image: url('. $img .');"' : 'style="background-color: '. $review['review_color'] .';"';
						echo '<a href="'. $review['review_permalink'] .'" '. $bg2.'>';

							$bg = ($has_image) ? 'style="background-color: '. $review['review_color'] .';"' : '';
							echo '<span class="rwp-overall" '. $bg .'>'.$review['review_score']['overall'].'<em>'. $review['review_score']['label'] .'</em></span>';

							echo '<span class="rwp-title">'. $review_title .'</span>';

						echo '</a>';
					echo '</li>';
					break;
			}
		}

		echo '</ul>';

		echo $args['after_widget'];
	}

	/**
	 * Ouputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance )
	{
		// outputs the options form on admin

		//$this->pretty_print ($instance);
		//$this->pretty_print ($this->templates);
		echo '<div class="rwp-widget-form-wrap">';

		foreach( $this->widget_fields as $field_key => $field ) {
			$value = ( isset( $instance[ $field_key ] ) ) ? $instance[ $field_key ] : '';

			echo '<p>';
			echo '<label for="">'. $field['label'] .':';
			switch ( $field_key) {
				case 'to_display':
				case 'theme':
					foreach ($field['options'] as $key => $label) {
						$ck = ( $key == $value ) ? 'checked' : '';
						echo '<span class="rwp-block"><input type="radio" id="'. $this->get_field_id( $field_key.$key ) .'" name="'. $this->get_field_name( $field_key ) .'" value="'. $key .'" '. $ck .'/> ' . $label . '</span>';
					}
					break;

				case 'template':

					if( !is_array( $value )  )
						$value = ( $value == 'all' ) ? array_keys( $this->templates ) : array( $value );

					foreach ($this->templates as $key => $t) {
						$ck = ( in_array($key, $value) ) ? 'checked' : '';
						echo '<span class="rwp-block"><input type="checkbox" id="'. $this->get_field_id( $field_key.$key ) .'" name="'. $this->get_field_name( $field_key ) .'[]" value="'. $key .'" '. $ck .'/> ' .$t['template_name'] . '</span>';
					}
					break;

				default:
					echo ( ! empty( $field['description'] ) ) ? '<span class="description">'. $field['description'] .'</span>' : '';
					echo'</label>';
					echo '<input class="widefat" type="text" id="'. $this->get_field_id( $field_key ) .'" name="'. $this->get_field_name( $field_key ) .'" value="'.$value.'" placeholder="'. $field['default'] .'" />';
					break;
			}
			echo '</p>';
		}

		echo '</div><!--/widget-form-wrap-->';
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance )
	{
		//return array();

		$valid_instance = array();

		//RWP_Reviewer::pretty_print($new_instance); die();

		foreach( $this->widget_fields as $field_key => $field ) {

			if( ! isset( $new_instance[ $field_key ]  ) ) { // Check if field is set
				$valid_instance[ $field_key ] = $field['default'];
				continue;
			}

			$value = ( is_array( $new_instance[ $field_key ] ) ) ? $new_instance[ $field_key ] : trim( $new_instance[ $field_key ] );

			switch ( $field_key) {
				case 'to_display':
				case 'theme':
					$valid_instance[ $field_key ] = ( isset( $field['options'][ $value ] ) ) ? $value : $field['default'];
					break;

				case 'to_display_count':
					$value = intval( $value );
					$valid_instance[ $field_key ] = ( $value > 0 ) ? $value : $field['default'];
					break;

				case 'box_color' :
					$valid_instance[ $field_key ] = ( preg_match( '/^#[a-f0-9]{6}$/i', $value ) ) ? $value : $field['default'];
					break;

				case 'template':
					$valid_instance[ $field_key ] = ( ! empty( $value) ) ?  $value : $field['default'];
					break;

				default:
					$valid_instance[ $field_key ] = ( ! empty( $value) ) ? esc_sql( esc_html( $value ) ) : $field['default'];
					break;
			}
		}

		return $valid_instance;
	}

	public function widget_field( $instance, $field, $return = false ) {

		$value = isset( $instance[ $field ] ) ? $instance[ $field ] : $this->widget_fields[ $field ]['default'];

		if( $return )
			return $value;

		echo $value;
	}

	public static function query_reviews( $template, $sort, $limit )
	{
		global $wpdb;

		$result = array();

		$post_meta = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE meta_key = 'rwp_reviews';", ARRAY_A );

		foreach( $post_meta as $meta ) {

			// Get post info
			$post_id	= $meta['post_id'];
			$post 		= get_post( $post_id, 'OBJECT');
			$post_date 	= strtotime( $post->post_date, current_time('timestamp') );

			// Reviews
			$reviews = unserialize( $meta['meta_value'] );

			if( ! is_array( $reviews ) ) {
				continue;
			}

			foreach( $reviews as $review ) {

				if( !in_array( $review['review_template'], $template) )
					continue;

				if( get_post_status( $post_id ) != 'publish' )
					continue;

				if( isset( $review['review_type'] ) && $review['review_type'] == 'UR' && ( $sort == 'top_score' ) )
					continue;

				$r = self::manage_review( $review, $post_id, $sort );
				$r['review_permalink'] 	= get_permalink( $post_id );
				$r['review_post_date'] 	= $post_date;
				$r['review_post_id']	= $post_id;


				$result[] = $r;
			}

		}

		switch ( $sort ) {

			case 'top_score':
			case 'top_users_scores':
			case 'combo_1':
				usort( $result, array( 'RWP_Reviews_Widget', 'sort_score' ) );
				break;

			case 'top_rated':
				usort( $result, array( 'RWP_Reviews_Widget', 'sort_rated' ) );
				break;

			case 'latest':
			default:
				usort( $result, array( 'RWP_Reviews_Widget', 'sort_latest' ) );
				break;
		}

		// Limit
		$revs = array_slice ( $result , 0, $limit );

		//RWP_Reviewer::pretty_print( $revs);
		return $revs;
	}

	public static function manage_review( $review, $post_id, $sort ) {

		$templates = RWP_Reviewer::get_option('rwp_templates');
		$template  = $templates[ self::review_field( 'review_template', $review, true ) ];

		$return = array();
		$return['review_title'] 				= self::review_field( 'review_title', $review, true );
		$return['review_image'] 				= self::review_field( 'review_image', $review, true );
		$return['review_type'] 					= self::review_field( 'review_type', $review, true );
		$return['review_id'] 					= self::review_field( 'review_id', $review, true );
		$return['review_type']					= self::review_field( 'review_type', $review, true );
		$return['review_title_options']			= self::review_field( 'review_title_options', $review, true );
		$return['review_custom_tabs']			= self::review_field( 'review_custom_tabs', $review, true );
		$return['review_template']				= self::review_field( 'review_template', $review, true );
		$return['review_use_featured_image']	= self::review_field( 'review_use_featured_image', $review, true );

		$template_tabs = self::template_field('template_custom_tabs', $template, true);

		foreach ($return['review_custom_tabs'] as $tab_key => $tab_value) {

			$return['review_custom_tabs'][ $tab_key ]['tab_label'] = $template_tabs[ $tab_key ]['tab_label'];
		}

		$return['review_color'] = ( self::review_field( 'review_type', $review, true ) == 'UR' ) ? self::template_field('template_users_score_box_color', $template, true) : self::template_field('template_total_score_box_color', $template, true);

		switch ($sort) {

			case 'top_users_scores':
				$data 	 	= RWP_User_Review::users_reviews( $post_id, $return['review_id'], self::review_field( 'review_template', $review, true ) );
				$overall 	= array( 'overall' => $data['overall'], 'count' => $data['count'], 'label' => $template['template_users_score_label'] );
				break;

			case 'combo_1':

				$author_custom_score = self::review_field( 'review_custom_overall_score', $review, true );
				$author_score 	  	 =  ( empty( $custom_score ) ) ? RWP_Reviewer::get_review_overall_score( $review )  : $custom_score;

				$users_data = RWP_User_Review::users_reviews( $post_id, $return['review_id'], self::review_field( 'review_template', $review, true ) );

				if( $return['review_type'] != 'UR' ) {
					$o = ( $users_data['count'] > 0 ) ? round( ( $author_score + $users_data['overall'] ) / 2 , 1 ) : $author_score;
				} else {
					$o = $users_data['overall'];
				}
				$c = ( $return['review_type'] != 'UR' ) ?  $users_data['count'] + 1 : $users_data['count'];
				$overall = array( 'overall' => $o, 'count' => $c, 'label' => __('Score', 'reviewer') );
				break;

			case 'top_rated':
				$data 	 = RWP_User_Review::users_reviews( $post_id, $return['review_id'], self::review_field( 'review_template', $review, true ) );
				$overall = array( 'overall' => $data['overall'], 'count' => $data['count'], 'label' => $template['template_users_score_label'] );
				break;

			case 'top_score':
				$custom_score 	= self::review_field( 'review_custom_overall_score', $review, true );
				$data	 = ( empty( $custom_score ) ) ? RWP_Reviewer::get_review_overall_score( $review )  : $custom_score;
				$overall = array( 'overall' => $data, 'count' => 1, 'label' => $template['template_total_score_label'] );

			case 'latest':
			default:
				if( $return['review_type'] == 'UR' ) {
					$data 	 = RWP_User_Review::users_reviews( $post_id, $return['review_id'], self::review_field( 'review_template', $review, true ) );
					$overall = array( 'overall' => $data['overall'], 'count' => $data['count'], 'label' => $template['template_users_score_label'] );
				} else {
					$custom_score 	= self::review_field( 'review_custom_overall_score', $review, true );
					$data	 = ( empty( $custom_score ) ) ? RWP_Reviewer::get_review_overall_score( $review )  : $custom_score;
					$overall = array( 'overall' => $data, 'count' => 1, 'label' => $template['template_total_score_label'] );
				}
				break;
		}

		$return['review_score'] = $overall;
		return $return;
	}

	public static function review_field( $field, $review, $return = false ) {

		$default_review = RWP_Reviews_Meta_Box::get_review_fields();

		$value = isset( $review[ $field ] ) ? $review[ $field ] : $default_review[ $field ]['default'];

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

	public static function sort_latest( $a, $b )
	{
		if ($a["review_post_date"] == $b["review_post_date"])
        	return 0;

   		return ($a["review_post_date"] > $b["review_post_date"]) ? -1 : 1;
	}

	public static function sort_score2( $a, $b )
	{
		if (  floatval($a["review_score"]['overall']) ==  floatval($b["review_score"]['overall']) )
        	return 0;

   		return ( floatval($a["review_score"]['overall']) >  floatval($b["review_score"]['overall']) ) ? -1 : 1;
	}

	public static function sort_score( $a, $b )
	{
		// echo '<pre>'; print_r($a); echo '</pre>';
		if (  floatval($a["review_score"]['overall']) ==  floatval($b["review_score"]['overall']) ) {
			if ( floatval($a["review_score"]['count']) ==  floatval($b["review_score"]['count']) ) {
				return 0;
			}

   			return ( floatval($a["review_score"]['count']) >  floatval($b["review_score"]['count']) ) ? -1 : 1;
		}

   		return ( floatval($a["review_score"]['overall']) >  floatval($b["review_score"]['overall']) ) ? -1 : 1;
	}

	public static function sort_rated( $a, $b )
	{
		if (  floatval($a["review_score"]['count']) ==  floatval($b["review_score"]['count']) )
        	return 0;

   		return ( floatval($a["review_score"]['count']) >  floatval($b["review_score"]['count']) ) ? -1 : 1;
	}

	public function set_widget_fields()
	{
		$this->widget_fields = array(
			'title' => array(
				'label' 		=> __( 'Title', $this->plugin_slug ),
				//'default'		=> __( 'Reviews', $this->plugin_slug ),
				'default'		=> '',
				'description' 	=> ''
			),

			'template' => array(
				'label' 		=> __( 'Template', $this->plugin_slug ),
				'default'		=> array_keys($this->templates),
				'description' 	=> ''
			),

			'to_display' => array(
				'label' 		=> __( 'To display', $this->plugin_slug ),
				'options' 		=> array(
					'latest'			=> __( 'Latest boxes', $this->plugin_slug ),
					'top_score'			=> __( 'Top scores boxes by reviewer', $this->plugin_slug ),
					'top_rated' 		=> __( 'Top rated boxes by users', $this->plugin_slug ),
					'top_users_scores' 	=> __( 'Top score boxes by users', $this->plugin_slug ),
					'combo_1' 			=> __( 'Combo 1 | Average of reviewer and users scores', $this->plugin_slug ),
				),
				'default'		=> 'latest',
				'description' 	=> ''
			),

			'theme' => array(
				'label' 		=> __( 'Theme', $this->plugin_slug ),
				'options' 		=> array(
					'theme-1'			=> __( 'Theme 1 - Big Format', $this->plugin_slug ),
					'theme-2'			=> __( 'Theme 2 - Small Format', $this->plugin_slug ),
					'theme-3'			=> __( 'Theme 3 - Small Format with stars', $this->plugin_slug ),
				),
				'default'		=> 'theme-1',
				'description' 	=> ''
			),

			'to_display_count' => array(
				'label' 	=> __( 'Number of reviews to display', $this->plugin_slug ),
				'default'	=> '5'
			),

			/*'box_color' => array(
				'label' 		=> __( 'Background Color of total score value', $this->plugin_slug ),
				'default'		=> '',
				'description' 	=> __( 'HEX Color', $this->plugin_slug )
			),*/

			/*'' => array(
				'label' 	=> __( '', $this->plugin_slug ),
				'default'	=> ''
			),*/
		);

	}

	// Method that well print data - debug
	public function pretty_print( $data = array() )
	{
		echo "<pre>";
		print_r($data);
		echo "</pre>";
	}
}
