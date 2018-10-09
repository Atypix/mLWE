<?php



/**

 * Reviewer Plugin v.2

 * Created by Michele Ivani

 */

class RWP_Review_Shortcode

{

	// Instace of this class

	protected static $instance = null;

	protected $shortcode_tag  			= 'rwp-review';

	protected $shortcode_tag_recap 		= 'rwp-review-recap';

	protected $shortcode_tag_scores 	= 'rwp-review-scores';

	protected $shortcode_tag_ratings 	= 'rwp-review-ratings';

	protected $shortcode_tag_form 		= 'rwp-review-form';



	protected $preferences;

	protected $template;

	protected $review;

	protected $rating_blacklist;

	protected $likes_blacklist;

	protected $ratings;

	protected $likes;

	protected $ratings_scores;



	protected $default_preferences = null;

	protected $default_template = null;

	protected $default_review = null;



	protected $themes;

	protected $branch;



	protected $enable_rating = null;



	protected $ratings_per_page = 3;



	protected $auto_review_id = -1;



	protected $users_data;



	protected $snippets = null;

	protected $schema_type = '';



	protected $vueID;



	function __construct()

	{

		$this->plugin_slug = 'reviewer';

		$this->set_themes();



		$shortcodes = array(

			array(

				'tag' => 'rwp_box',

				'renderer' => array( $this, 'do_shortcode' ),

				'alias' => array('rwp-review'),

			),



			array(

				'tag' => 'rwp_box_recap',

				'renderer' => array( $this, 'do_shortcode_recap' ),

				'alias' => array('rwp-review-recap'),

			),



			array(

				'tag' => 'rwp_box_criteria',

				'renderer' => array( $this, 'do_shortcode_scores' ),

				'alias' => array('rwp-review-scores'),

			),



			array(

				'tag' => 'rwp_box_reviews',

				'renderer' => array( $this, 'do_shortcode_ratings' ),

				'alias' => array('rwp-review-ratings'),

			),



			array(

				'tag' => 'rwp_box_form',

				'renderer' => array( $this, 'do_shortcode_form' ),

				'alias' => array('rwp-review-form'),

			),

		);



		foreach ($shortcodes as $shortcode ) {

			add_shortcode( $shortcode['tag'] , $shortcode['renderer'] );

			if( empty( $shortcode['alias'] ) ) {

				continue;

			}

			foreach ($shortcode['alias'] as $alias) {

				add_shortcode( $alias , $shortcode['renderer'] );

			}

		}

	}



	public function do_shortcode( $atts ) {



		extract( shortcode_atts( array(

			'id' 		=> '',

			'theme' 	=> '',

			'template'	=> '',

			'rating'	=> '',

			'branch'	=> '',

			'post'		=> get_the_ID(),

			'hide_criteria_scores' => 'false',

		), $atts ) );





		$this->post_id = $post;

		$post_id = $post;

		//return "Review " . $theme;



		$review_id = intval( $id );



		//echo "Review ID: " . $review_id;

		//echo "Branch: " . $branch . '<br/>';



		if( $review_id != $this->auto_review_id ) { // Manul Review



			// Get post reviews

			$reviews = get_post_meta( $this->post_id, 'rwp_reviews', true );



			// Check if user has inserted a valid review ID

			if( ! isset( $reviews[ $id ] ) )

				return '<p>' . __('No review found! Insert a valid review ID.', $this->plugin_slug) . '</p>';



			// Get Review

			$this->review = $reviews[ $id ];



		} else { // Auto Review



			$this->review = $this->get_auto_review( $template );

		}



		// Get Options

		$this->preferences 					= RWP_Reviewer::get_option('rwp_preferences');

		$templates 							= RWP_Reviewer::get_option('rwp_templates');

		$this->template 					= (isset( $templates[ $this->review['review_template'] ] )) ? $templates[ $this->review['review_template'] ] : array();

		$this->template['template_theme']	= (empty($theme)) ? $this->template['template_theme'] : 'rwp-theme-'. $theme;



		// Ratings per page

		$this->ratings_per_page = $this->preferences_field( 'preferences_users_reviews_per_page', true );



		// Rating param

		$this->preferences['preferences_rating_mode'] = (empty($rating)) ? $this->preferences['preferences_rating_mode'] : $rating;



		// Utility variable

		$this->is_UR 	= ($this->review_field('review_type', true) == 'UR') ? true : false;



		$img = $this->review_field('review_image', true);



		if ( $review_id != $this->auto_review_id ) { // Manual box



			if( $this->review_field('review_use_featured_image', true) == 'no' ) {

				$this->has_img 	= (!empty( $img ) ) ? true : false;

			} else {

				$this->has_img 	=  has_post_thumbnail( $this->post_id );

			}



		} else { // Auto Box



			$auto_need_img = $this->template_field('template_auto_reviews_featured_image', true);

			$this->has_img = ( $auto_need_img == 'yes' &&  has_post_thumbnail( $this->post_id ) );



		}



		// Ratings

		//$ratings = get_post_meta( $post_id, 'rwp_rating_' . $this->review['review_id'] );

		//$this->ratings = is_array( $ratings ) ? $ratings : array();

		$this->ratings = array();

		$ratings = array();



		// Filter ratings

		//$moderation 	= $this->preferences_field( 'preferences_rating_before_appears', true );

		//$this->ratings 	= RWP_Reviewer::filter_ratings( $this->ratings, $moderation );



		$rating_blacklist = get_post_meta( $post_id, 'rwp_rating_blacklist', true);

		$this->rating_blacklist = is_array( $rating_blacklist ) ? $rating_blacklist : array();



		$likes_blacklist = get_post_meta( $post_id, 'rwp_likes_blacklist', true);

		$this->likes_blacklist = is_array( $likes_blacklist ) ? $likes_blacklist : array();



		//$likes = get_post_meta( $post_id, 'rwp_likes', true );

		//$this->likes = is_array( $likes ) ? $likes : array();



		if( !$this->is_users_rating_disabled() ) {

			$this->users_data = RWP_User_Review::users_reviews( $post_id, $this->review['review_id'], $this->review['review_template'] );

		}



		// Ratings Scores

		//$this->ratings_scores = RWP_Reviewer::get_ratings_single_scores( $this->post_id, $this->review_field('review_id', true), $this->review_field('review_template', true) );

		$this->ratings_scores = array();

		// Branch

		$this->branch = $branch;



		// Google rich snippets

		$this->schema_type = $this->template_field('template_schema_type', true);

		$this->snippets = new RWP_Snippets( array(), $this->schema_type);



		// Dropzone

		$attachments = $this->preferences_field('preferences_user_review_images', true);

		$action_name = 'rwp_reviews_box_upload_image';

		$attachments['actions'][ $action_name ] = wp_create_nonce( $action_name );

		$dropzone = array_merge( array(

		    'dictDefaultMessage'            => $attachments['field_placeholder'],

		    'dictFallbackMessage'           => __("Your browser does not support drag'n'drop file uploads.", $this->plugin_slug),

		    'dictInvalidFileType'           => __('You can upload images only.', $this->plugin_slug),

		    'dictFileTooBig'                => sprintf( __('The image (%sMB) is too big. Maximum image size is %sMB.', $this->plugin_slug), '{{filesize}}', '{{maxFilesize}}'),

		    'dictResponseError'             => sprintf( __('Unable to upload the review images, error %s.', $this->plugin_slug), '{{statusCode}}'),

		    'dictCancelUpload'              => __('Cancel upload', $this->plugin_slug),

		    'dictCancelUploadConfirmation'  => __('Do you want to cancel the upload.', $this->plugin_slug),

		    'dictRemoveFile'                => __('Remove Image', $this->plugin_slug),

		    'dictMaxFilesExceeded'          => sprintf(__('You can upload %s images per review.', $this->plugin_slug), $attachments['field_bound']),

		), $attachments );

		wp_localize_script( $this->plugin_slug .'-front-end-script', 'rwpDropzone', $dropzone );



		// VueJS ID

		$this->vueID = 'rwp-review-'. $this->post_id .'-'. $this->review_field('review_id', true) .'-'. rand();



		ob_start();



		include 'themes/layout.php';



		//RWP_Reviewer::pretty_print( $this->ratings );

		//RWP_Reviewer::pretty_print( $this->rating_blacklist );

		//RWP_Reviewer::pretty_print( $this->likes );

		//RWP_Reviewer::pretty_print( $this->likes_blacklist );

		//RWP_Reviewer::pretty_print( $this->review );

		//RWP_Reviewer::pretty_print( $this->template );

		//RWP_Reviewer::pretty_print( $this->preferences );



		return ob_get_clean();

	}



	public function do_shortcode_recap( $atts )

	{

		extract( shortcode_atts( array(

			'id' 		=> '',

			'template'	=> '',

			'post'		=> get_the_ID(),

			'hide_criteria_scores' => 'false',

		), $atts ) );



		$shortcode = '[rwp-review id="'. $id .'" branch="recap" post="'. $post .'" template="'. $template .'" hide_criteria_scores="'. $hide_criteria_scores .'"]';



		return do_shortcode( $shortcode );



	}



	public function do_shortcode_scores( $atts )

	{

		extract( shortcode_atts( array(

			'id' 		=> '',

			'template'	=> '',

			'post'		=> get_the_ID()

		), $atts ) );



		$shortcode = '[rwp-review id="'. $id .'" branch="scores" post="'. $post .'" template="'. $template .'"]';



		return do_shortcode( $shortcode );



	}



	public function do_shortcode_ratings( $atts )

	{

		extract( shortcode_atts( array(

			'id' 		=> '',

			'template'	=> '',

			'post'		=> get_the_ID()

		), $atts ) );



		$shortcode = '[rwp-review id="'. $id .'" branch="ratings" post="'. $post .'" template="'. $template .'"]';



		return do_shortcode( $shortcode );



	}



	public function do_shortcode_form( $atts )

	{

		extract( shortcode_atts( array(

			'id' 		=> '',

			'template'	=> '',

			'post'		=> get_the_ID()

		), $atts ) );



		$shortcode = '[rwp-review id="'. $id .'" branch="form" post="'. $post .'" template="'. $template .'"]';



		return do_shortcode( $shortcode );



	}



	public static function get_instance()

	{

		// If the single instance hasn't been set, set it now.

		if ( null == self::$instance ) {

			self::$instance = new self;

		}



		return self::$instance;

	}



	public function get_auto_review( $template ) {



		$post_type 	= get_post_type( $this->post_id );

		$auto_id 	= $this->auto_review_id;



		$review_id 	= md5( 'rwp-'. $template .'-'. $post_type . '-' . $this->post_id . '-' . $auto_id );



		$review = array(

			'review_id' 		=> $review_id,

			'review_title' 		=> '',

			'review_template' 	=> $template,

			'review_type' 		=> 'UR',

			'review_image'		=> '',

			'review_use_featured_image' => 'yes',

		);



		return $review;



	}



	public function already_rated() {



		$user = wp_get_current_user();

		$cookie_name = 'rwp_rating_' . $this->post_id .'_' . $this->review_field( 'review_id', true ) .'_' . $user->ID;



		if( isset( $_COOKIE[ $cookie_name ] ) ) {

			return true;

		}



		// Blacklist

		$blacklist = $this->rating_blacklist;



		if( isset( $blacklist[ $this->post_id . '-' . $this->review_field( 'review_id', true ) ] ) && in_array( $user->ID, $blacklist[ $this->post_id . '-' . $this->review_field( 'review_id', true ) ] ) ) {

			return true;

		}



		return false;

	}



	public function is_users_rating_disabled() {



		$auth = $this->preferences_field('preferences_authorization', true);



		if( $auth == 'disabled' || $this->review_field( 'review_disable_user_rating', true) == 'yes' ) {

			return true;

		}



		return false;

	}



	public function enable_rating() {



		$auth = $this->preferences_field('preferences_authorization', true);



		if( $auth == 'disabled' || $this->review_field( 'review_disable_user_rating', true) == 'yes' ) {

			$this->enable_rating = false;

			return false;

		}



		if( $auth == 'logged_in' && !is_user_logged_in() ) {

			$this->enable_rating = false;

			return false;

		}



		if( $auth == 'roles' && !$this->has_user_required_roles() ) {

			return false;

		}



		$limit = $this->preferences_field('preferences_rating_limit', true);



		if( $limit == 'single' ) {

			$user = wp_get_current_user();

			$cookie_name = 'rwp_rating_' . $this->post_id .'_' . $this->review_field( 'review_id', true ) .'_' . $user->ID;



			if( isset( $_COOKIE[ $cookie_name ] ) ) {

				$this->enable_rating = false;

				return false;

			}



			// Blacklist

			$blacklist = $this->rating_blacklist;



			if( isset( $blacklist[ $this->post_id . '-' . $this->review_field( 'review_id', true ) ] ) && in_array( $user->ID, $blacklist[ $this->post_id . '-' . $this->review_field( 'review_id', true ) ] ) ) {

				$this->enable_rating = false;

				return false;

			}

		}



		return true;

	}



	public function has_user_required_roles() {

		global $post;
		global $wpdb;

		$roles = $this->preferences_field('preferences_authorization_roles', true);

		$user = wp_get_current_user();

		$users_data = get_userdata( $user->ID );

		if( $user->ID == 0 && $users_data === false  ) {

			return false;

		}

		$user_roles = $users_data->roles;

		foreach ( $user_roles as $role ) {

			if( in_array( $role, $roles ) ) {

				return true;

			}

		}



		return false;

	}



	public function get_auth_roles() {

		global $wp_roles;



		if ( ! isset( $wp_roles ) ) {

			$wp_roles = new WP_Roles();

		}

		$wproles = $wp_roles->get_names();

		$authroles = $this->preferences_field('preferences_authorization_roles', true);



		$roles = array();

		foreach( $wproles as $key => $name ) {

			if( in_array( $key, $authroles ) ) {

				$roles[ $key ] = $name;

			}

		}



		return $roles;

	}



	public function enable_like( $rating_id = '' ) {



		$auth = $this->preferences_field('preferences_authorization', true);



		if( $auth == 'disabled' || $this->review_field( 'review_disable_user_rating', true) == 'yes' ) {

			return false;

		}



		if( $auth == 'logged_in' && !is_user_logged_in() ) {

			return false;

		}



		$user = wp_get_current_user();

		$cookie_name = 'rwp_like_' . $rating_id . '_' . $user->ID;



		if( isset( $_COOKIE[ $cookie_name ] ) ) {

			return false;

		}



		// Blacklist

		$blacklist = $this->likes_blacklist;



		if( isset( $blacklist[ $rating_id ] ) && in_array( $user->ID, $blacklist[ $rating_id ] ) ) {

			return false;

		}



		return true;

	}



	public function preferences_field( $field, $return = false ) {



		if ( null == $this->default_preferences ) {

			$this->default_preferences = RWP_Preferences_Page::get_preferences_fields();

		}



		$value = isset( $this->preferences[ $field ] ) ? $this->preferences[ $field ] : $this->default_preferences[ $field ]['default'];



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



	public function review_field( $field, $return = false ) {



		if ( null == $this->default_review ) {

			$this->default_review = RWP_Reviews_Meta_Box::get_review_fields();

		}



		$value = isset( $this->review[ $field ] ) ? $this->review[ $field ] : $this->default_review[ $field ]['default'];



		if( $return )

			return $value;



		echo $value;

	}



	protected function set_themes()

	{

		$this->themes = array(



			'rwp-theme-1' => array(

				'header' => array(

					'theme-section-image',

					'overalls' => array(

						'theme-section-overall-score',

						'theme-section-users-score'

					),

					'theme-section-tabs',

				),

				'theme-section-links',

				'prosandcons' => array(

					'theme-section-pros-cons'

				),

				'theme-section-summary',

				'theme-section-scores',

				'theme-section-users-ratings',

				'theme-section-users-ratings-form'

			),



			'rwp-theme-2' => array(

				'header' => array(

					'theme-section-image',

					'overalls' => array(

						'theme-section-overall-score',

						'theme-section-users-score'

					),

					'theme-section-tabs',

				),

				'theme-section-links',

				'prosandcons' => array(

					'theme-section-pros-cons'

				),

				'theme-section-summary',

				'theme-section-scores',

				'theme-section-users-ratings',

				'theme-section-users-ratings-form'

			),



			'rwp-theme-3' => array(

				'header' => array(

					'theme-section-image',

					'overalls' => array(

						'theme-section-overall-score',

						'theme-section-users-score'

					),

					'theme-section-tabs',

				),

				'theme-section-links',

				'prosandcons' => array(

					'theme-section-pros-cons'

				),

				'theme-section-summary',

				'theme-section-scores',

				'theme-section-users-ratings',

				'theme-section-users-ratings-form'

			),



			'rwp-theme-4' => array(

				'theme-section-scores',

				'header' => array(

					'theme-section-image',

					'overalls' => array(

						'theme-section-overall-score',

						'theme-section-users-score',

						'theme-section-tabs',

					)

				),

				'theme-section-links',

				'theme-section-summary',

				'theme-section-users-ratings',

				'theme-section-users-ratings-form'

			),



			'rwp-theme-5' => array(

				'header' => array(

					'theme-section-image',

					'overalls' => array(

						'theme-section-overall-score',

						'theme-section-users-score'

					),

					'theme-section-tabs',

					'theme-section-scores'

				),

				'theme-section-links',

				'theme-section-summary',

				'theme-section-users-ratings',

				'theme-section-users-ratings-form'

			),



			'rwp-theme-6' => array(

				'header' => array(

					'theme-section-image',

					'overalls' => array(

						'theme-section-overall-score',

						'theme-section-users-score'

					),

					'theme-section-tabs',

					'theme-section-scores'

				),

				'theme-section-links',

				'theme-section-summary',

				'theme-section-users-ratings',

				'theme-section-users-ratings-form'

			),



			'rwp-theme-7' => array(

				'theme-section-users-ratings',

				'theme-section-users-ratings-form'

			),



			'rwp-theme-8' => array(

				'box' => array(

					'theme-section-image',

					'theme-section-overall-score',

					//'inner' => array(

						'theme-section-scores',

						'theme-section-users-score',

					//),

					'theme-section-tabs',

				),

				'theme-section-users-ratings',

				'theme-section-users-ratings-form'

			),



			'rwp-theme-9' => array(

				'header' => array(

					'theme-section-image',

					'overalls' => array(

						'theme-section-overall-score',

						'theme-section-users-score'

					),

					'theme-section-tabs',

				),

				'theme-section-links',

				'prosandcons' => array(

					'theme-section-pros-cons'

				),

				'theme-section-summary',

				'theme-section-users-ratings',

				'theme-section-users-ratings-form'

			),



		);

	}



	protected function include_sections($src, $hide_criteria_scores = 'false') {



		$is_UR 		= $this->is_UR;

		$has_img 	= $this->has_img;



		foreach ($src as $key => $value) {



			if( !empty( $this->branch )  && $this->branch == 'recap' && !is_array( $value ) && ( ( $value == 'theme-section-scores' && $hide_criteria_scores == 'true' )  || $value == 'theme-section-users-ratings' || $value == 'theme-section-users-ratings-form' ) ) {

				continue;

			}



			if( !empty( $this->branch )  && $this->branch == 'scores' && ( ( !is_array( $value ) && $value != 'theme-section-scores' ) || ( is_array( $value ) ) ) ) {

				continue;

			}



			if( !empty( $this->branch )  && $this->branch == 'ratings' && ( ( !is_array( $value ) && $value != 'theme-section-users-ratings' ) || ( is_array( $value ) ) ) ) {

				continue;

			}



			if( !empty( $this->branch )  && $this->branch == 'form' && ( ( !is_array( $value ) && $value != 'theme-section-users-ratings-form' ) || is_array( $value ) ) ) {

				continue;

			}



			if( is_array( $value ) ) {



				echo '<div class="rwp-'. $key;



				if( $key == 'header' )

					echo ($has_img) ? ' rwp-has-image' : ' rwp-no-image';



				echo '">';

                    $this->include_sections( $value, $hide_criteria_scores );

                echo '</div> <!-- /'. $key .' -->';



			} else {



				include(RWP_PLUGIN_PATH . 'public/includes/themes/'.$value . '.php');

			}

		}

	}



	protected function get_score_bar( $score, $theme = '', $size = 0 ) {



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



		$in = ( !empty( $theme ) ) ? '<span class="rwp-criterion-score" style="font-size: '. ($size + 2) .'px;">'. RWP_Reviewer::format_number( $score ) .'</span>' : '';



		return '<div class="rwp-score-bar" style="width: '. $pct .'%; background: '. $color .';">'. $in .'</div>';

	}



	protected function get_stars_form( $review_id = 0, $stars = 5, $multiple = false, $criterion_id = 0 ) {



		$html  = '<div class="rwp-stars">';



		$count = $stars * 2;



		$value = 5;



		$input_name = ( $multiple ) ? 'rating-'. get_the_ID() . '-'. $review_id . '-' . $criterion_id : 'rating-'. get_the_ID() . '-'. $review_id;



		for ($i = $count; $i >= 0 ; $i--) {



			$rand = rand();



			$ck = ($i == 0) ? 'checked="checked"' : '';

			$oe = (($i-1) % 2 == 0) ? 'rwp-odd': 'rwp-even';



			$icon = ($i == 0) ? '' : ' style="background-image: url('. $this->template_field('template_rate_image', true) .');" ';



			$html .= '<input class="rwp-rating" id="rwp-rating-'. $this->post_id . '-'. $review_id .'-'. $i .'-'. $rand .'" name="rwp-ur['. $input_name .']" type="radio" value="'. $value .'" '. $ck .' data-index="'. $criterion_id .'" />';

			$html .= '<label '. $icon .' for="rwp-rating-'. $this->post_id . '-'. $review_id .'-'. $i .'-'. $rand .'" class="rwp-star '. $oe .'" onclick=""></label>';



			$value -= .5;

		}



		$html .= '</div><!-- /stars -->';



		return $html;

	}



	protected function get_stars_form2( $review_id = 0, $stars = 5, $multiple = false, $criterion_id = 0 ) {



		$html  = '<div class="rwp-stars2">';



		$count = $stars;



		$value = 5;



		$input_name = ( $multiple ) ? 'rating-'. get_the_ID() . '-'. $review_id . '-' . $criterion_id : 'rating-'. get_the_ID() . '-'. $review_id;



		for ($i = $count; $i >= 0 ; $i--) {



			$rand = rand();



			$ck = ($i == 0) ? 'checked="checked"' : '';



			$icon = ($i == 0) ? '' : ' style="background-image: url('. $this->template_field('template_rate_image', true) .');" ';



			$html .= '<input class="rwp-rating2" id="rwp-rating-'. $this->post_id . '-'. $review_id .'-'. $i .'-'. $rand .'" name="rwp-ur['. $input_name .']" type="radio" value="'. $value .'" '. $ck .' data-index="'. $criterion_id .'" />';

			$html .= '<label '. $icon .' for="rwp-rating-'. $this->post_id . '-'. $review_id .'-'. $i .'-'. $rand .'" class="rwp-star2" onclick=""></label>';



			$value --;

		}



		$html .= '</div><!-- /stars -->';



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



	public function get_knobs( $score ) {



		$max = $this->template_field('template_maximum_score', true);

		$min = $this->template_field('template_minimum_score', true);



		$value 	= RWP_Reviewer::get_in_base( $this->template_field('template_maximum_score', true), $max, floatval( $score ) );



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



		return '<input type="text" value="'. $value .'" class="rwp-knob" data-min="'. $min .'" data-max="'. $max .'" data-fgColor="'. $color .'" />';

	}



	public function get_circle( $score ) {



		$max = $this->template_field('template_maximum_score', true);

		$min = $this->template_field('template_minimum_score', true);



		$value 	= RWP_Reviewer::get_in_base( $this->template_field('template_maximum_score', true), $max, floatval( $score ) );



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



		return '<div class="rwp-score-circle" id="'. uniqid('rwp-score-circle-') .'" data-value="'. $value .'" data-min="'. $min .'" data-max="'. $max .'" data-fgColor="'. $color .'" data-pct="'. $ptc2 .'"></div>';

	}



	public static function get_facebook( $post_id, $rating_id ) {

		$url = 'http://www.facebook.com/sharer/sharer.php?u='. urlencode( add_query_arg( 'rwpurid', $rating_id, get_permalink( $post_id ) ) );

		return $url;

		//return '<a href="' . esc_url( $url) . '" class="rwp-share rwp-facebook rwp-d"></a>';

	}



	public static function facebook( $post_id, $rating_id ) {

		echo self::get_facebook( $post_id, $rating_id );

	}



	public static function get_twitter( $post_id, $rating_id ) {

		$url = 'http://twitter.com/intent/tweet?text='. urlencode('Take a look at my review') .'&url='. urlencode( add_query_arg( 'rwpurid', $rating_id, get_permalink( $post_id ) ) ) .'&hashtags=ReviewerPlugin';

		return $url;

		//return '<a href="' . esc_url( $url) . '" class="rwp-share rwp-twitter rwp-d"></a>';

	}



	public static function twitter( $post_id, $rating_id ) {

		echo self::get_twitter( $post_id, $rating_id );

	}



	public static function get_google( $post_id, $rating_id ) {

		$url = 'https://plus.google.com/share?url='. urlencode( add_query_arg( 'rwpurid', $rating_id, get_permalink( $post_id ) ) );

		return $url;

		//return '<a href="' . esc_url( $url) . '" class="rwp-share rwp-google rwp-d"></a>';

	}



	public static function google( $post_id, $rating_id ) {

		echo self::get_google( $post_id, $rating_id );

	}



	public static function get_email( $post_id, $rating_id ) {

		$url = 'mailto:?subject=ReviewerPlugin&body=Take a look at my review '. add_query_arg( 'rwpurid', $rating_id, get_permalink( $post_id ) );

		return $url;

		//return '<a href="' . $url . '" class="rwp-share rwp-email"></a>';

	}



	public static function email( $post_id, $rating_id ) {

		echo self::get_email( $post_id, $rating_id );

	}



	public static function get_link( $post_id, $rating_id ) {

		$url = add_query_arg( 'rwpurid', $rating_id, get_permalink( $post_id ) );

		return $url;

		//return '<a href="' . esc_url( $url) . '" class="rwp-share rwp-sharing-link" data-label="'. __( 'Copy and paste the URL to share the review', 'reviewer' ) .'"></a>';

	}



	public static function link( $post_id, $rating_id ) {

		echo self::get_link( $post_id, $rating_id );

	}

}

