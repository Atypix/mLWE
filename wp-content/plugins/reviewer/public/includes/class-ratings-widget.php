<?php

class RWP_Ratings_Widget extends WP_Widget {

	public $plugin_slug;
	public $widget_fields;
	public $templates;
	public $preferences;
	public $ratings_options;

	private $comment_limit = 100;

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct()
	{
		// widget actual processes

		$this->plugin_slug 		= 'reviewer';
		$this->templates 		= RWP_Reviewer::get_option('rwp_templates');
		$this->preferences 		= RWP_Reviewer::get_option('rwp_preferences');

		$template_fields 		= RWP_Template_Manager_Page::get_template_fields();
		$this->ratings_options 	= $template_fields['template_user_rating_options']['options'];
		unset( $this->ratings_options['rating_option_captcha'], $this->ratings_options['rating_option_email'], $this->ratings_options['rating_option_like'] );
		$this->ratings_options['rating_option_post_title'] = __( 'Post Title', $this->plugin_slug );
		$this->ratings_options['rating_option_link'] = __( 'Show Link', $this->plugin_slug );

		add_action( 'init', array( $this ,'set_widget_fields') );

		$options = array(
			'description'	=> __( 'Reviewer Plugin Widget allows you to display your latest, top score users reviews.', $this->plugin_slug),
			'name'			=> 'Reviewer | Users Reviews'
		);

		parent::__construct('rwp-ratings-widget', '', $options);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance )
	{
		extract( $instance );

		// Templates
		if( isset( $template ) && !is_array( $template ) ) {
			$templates = ( $template == 'all' ) ? array_keys( $this->templates ) : array( $template );
		} elseif ( !isset( $template ) ) {
			$templates = array_keys( $this->templates );
		} else {
			$templates = $template;
		}

		// Sort
		$sort = $this->widget_field( $instance, 'to_display', true );
		//Limit
		$limit = $this->widget_field( $instance, 'to_display_count', true );

		$options = $this->widget_field( $instance, 'options' ,true);

		echo $args['before_widget'];
		if(  isset( $title ) && !empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		echo '<div class="rwp-widget-ratings-wrap" id="'. uniqid('rwp-widget-ratings-') .'" data-templates="'. implode(':', $templates) .'" data-limit="'. $limit .'" data-sorting="'. $sort .'" data-comment-limit="'. $this->comment_limit .'" >';
			echo '<span class="rwp-loading-icon" v-show="loading"></span><!-- /loader-->';
			echo '<p v-show="!loading && !successs" v-text="errorMsg"></p>';

			echo '<ul class="rwp-widget-ratings">';

				$has_rank = ( $sort != 'latest' ) ? 'rwp-has-ranking' : '';
				echo '<li v-for="review in reviews" class="'. $has_rank .'">';
					if( !empty( $has_rank ) ) { echo '<span class="rwp-ranking-number" v-text="$index + 1"></span>'; }
					echo '<div class="rwp-wdj-content">';
						// Post title
						if( in_array( 'rating_option_post_title', $options ) ) {
							echo '<span class="rwp-w-post-title">{{{ review.rating_post_title }}}</span>';
						}
						echo '<div class="rwp-cell">';
							// Avatar
							$has_avatar = '';
							if( in_array( 'rating_option_avatar' , $options ) ) {
								$has_avatar = 'rwp-has-avatar';
								echo '<img v-bind:src="review.rating_user_avatar" alt="User Avatar"/>';
							}

							echo '<div class="rwp-cell-content '. $has_avatar .'">';

								// Username
								if( in_array( 'rating_option_name', $options ) ) {
									echo '<span class="rwp-w-name" v-text="review.rating_user_name"></span>';
								}

								// Date
								echo '<span class="rwp-w-date" v-text="review.rating_formatted_date"></span>';

								// Score
								if( in_array( 'rating_option_score', $options ) ) {

									$mode = $this->preferences['preferences_rating_mode'];

									switch ( $mode ) {

										case 'five_stars':
										case 'full_five_stars':

											echo '<rwp-score-5-star
												v-bind:score="parseFloat(review.rating_overall)"
												v-bind:min="parseFloat(review.rating_template_minimum_score)"
												v-bind:max="parseFloat(review.rating_template_maximum_score)"
												v-bind:icon="review.rating_template_rate_image"></rwp-score-5-star>';
											break;

										default:
											echo '<rwp-widget-score-bar
												v-bind:score="parseFloat(review.rating_overall)"
												v-bind:min="parseFloat(review.rating_template_minimum_score)"
												v-bind:max="parseFloat(review.rating_template_maximum_score)"
												v-bind:low="parseInt(review.rating_template_low_pct)"
												v-bind:high="parseInt(review.rating_template_high_pct)"
												v-bind:color-low="review.rating_template_low_score_color"
												v-bind:color-medium="review.rating_template_medium_score_color"
												v-bind:color-high="review.rating_template_high_score_color"
												></rwp-widget-score-bar>';
											break;
									}

								}

							echo '</div><!-- /cell-content -->';
						echo '</div><!-- /cell -->';

						// Title
						if( in_array( 'rating_option_title', $options ) ) {
							echo '<span class="rwp-w-title" v-show="review.rating_title.length > 0">{{{ review.rating_title }}}</span>';
						}

						// Comment
						if( in_array( 'rating_option_comment', $options ) ) {
							echo '<p class="rwp-w-comment" v-show="review.rating_comment.length > 0">{{{review.rating_comment | sstr | nl2br}}}</p>';
						}

						// Show Link
						if( in_array( 'rating_option_link', $options ) ) {
							if( isset( $instance['custom_url'] ) && !empty( $instance['custom_url'] ) ) {
								$url = $instance['custom_url'];
								echo '<a v-bind:href="\''. $url .'?rwpurid=\'+review.rating_id">'. $this->widget_field( $instance, 'show', true ) .'</a>';
							} else {
								echo '<a v-bind:href="review.rating_url">'. $this->widget_field( $instance, 'show', true ) .'</a>';
							}
						} // link

					echo '</div> <!-- /content -->';
				echo '</li>';

			echo '</ul>';
		echo '</div>';
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

		// $this->pretty_print ($instance);
		//$this->pretty_print ($this->templates);
		echo '<div class="rwp-widget-form-wrap">';

		foreach( $this->widget_fields as $field_key => $field ) {
			$value = ( isset( $instance[ $field_key ] ) ) ? $instance[ $field_key ] : '';

			echo '<p>';
			echo '<label for="">'. $field['label'] .':';
			switch ( $field_key) {

				case 'to_display':
					foreach ($field['options'] as $key => $label) {
						$ck = ( $key == $value ) ? 'checked' : '';
						echo '<span class="rwp-block"><input type="radio" id="'. $this->get_field_id( $field_key.$key ) .'" name="'. $this->get_field_name( $field_key ) .'" value="'. $key .'" '. $ck .'/> ' . $label . '</span>';
					}
					break;

				case 'options':

					if( !is_array( $value ) ) {
						$value = array_keys( $this->ratings_options );
					}

					foreach ($this->ratings_options as $key => $t) {
						$ck = ( in_array($key, $value) ) ? 'checked' : '';
						echo '<span class="rwp-block"><input type="checkbox" id="'. $this->get_field_id( $field_key.$key ) .'" name="'. $this->get_field_name( $field_key ) .'[]" value="'. $key .'" '. $ck .'/> ' .$t . '</span>';
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

				case 'options':
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

	public function query_ratings( $template, $sort, $limit )
	{
		global $wpdb;
		$result = array();

		$post_meta = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE meta_key LIKE 'rwp_rating%';", ARRAY_A );

		foreach( $post_meta as $meta ) {

			$rating = unserialize( $meta['meta_value'] );

            if( !isset( $rating['rating_id'] ) )
                continue;

            $rating['rating_meta_id'] = $meta['meta_id'];

            if( isset( $rating['rating_status'] ) && $rating['rating_status'] != 'published')
            	continue;

            $result[ $rating['rating_id'] ] = $rating;
		}

		switch ( $sort ) {

			case 'top_score':
				usort( $result, array( 'RWP_Ratings_Widget', 'sort_score' ) );
				break;

			case 'latest':
			default:
				usort( $result, array( 'RWP_Ratings_Widget', 'sort_latest' ) );
				break;
		}

		// Limit
		$rts = array_slice ( $result , 0, $limit );

		return $rts;
	}

	public static function sort_latest( $a, $b )
	{
		if ($a["rating_date"] == $b["rating_date"])
        	return 0;

   		return ($a["rating_date"] > $b["rating_date"]) ? -1 : 1;
	}

	public static function sort_score( $a, $b )
	{
		$avg_a = RWP_Reviewer::get_avg( $a['rating_score'] );
		$avg_b = RWP_Reviewer::get_avg( $b['rating_score'] );

		if (  $avg_a ==  $avg_b )
        	return 0;

   		return ( $avg_a >  $avg_b ) ? -1 : 1;
	}

	public function widget_field( $instance, $field, $return = false ) {

		$value = isset( $instance[ $field ] ) ? $instance[ $field ] : $this->widget_fields[ $field ]['default'];

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

	public function set_widget_fields()
	{
		$this->widget_fields = array(
			'title' => array(
				'label' 		=> __( 'Title', $this->plugin_slug ),
				'default'		=> '',
				'description' 	=> ''
			),

			'template' => array(
				'label' 		=> __( 'Template', $this->plugin_slug ),
				'default'		=> array_keys($this->templates),
				'description' 	=> ''
			),

			'options' => array(
				'label' 		=> __( 'Rating Options', $this->plugin_slug ),
				'default'		=> array_keys($this->ratings_options),
				'description' 	=> ''
			),

			'to_display' => array(
				'label' 		=> __( 'To display', $this->plugin_slug ),
				'options' 		=> array(
					'latest'			=> __( 'Latest Ratings', $this->plugin_slug ),
					'top_score'			=> __( 'Top Score Ratings', $this->plugin_slug ),
				),
				'default'		=> 'latest',
				'description' 	=> ''
			),

			/*'theme' => array(
				'label' 		=> __( 'Theme', $this->plugin_slug ),
				'options' 		=> array(
					'theme-1'			=> __( 'Theme 1 - Big Format', $this->plugin_slug ),
					'theme-2'			=> __( 'Theme 2 - Small Format', $this->plugin_slug ),
				),
				'default'		=> 'theme-1',
				'description' 	=> ''
			),*/

			'show' => array(
				'label' 	=> __( 'Show Link Label', $this->plugin_slug ),
				'default'	=> __( 'Show', $this->plugin_slug ),
			),

			'custom_url' => array(
				'label' 	=> __( 'Custom redirect URL', $this->plugin_slug ),
				'default'	=> '',
			),

			'to_display_count' => array(
				'label' 	=> __( 'Number of ratings to display', $this->plugin_slug ),
				'default'	=> '5'
			),
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
