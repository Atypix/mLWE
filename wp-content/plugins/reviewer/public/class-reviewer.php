<?php

/**
 * Reviewer Plugin v.3
 * Created by Michele Ivani
 */
class RWP_Reviewer
{
	// Plugin Version
	const VERSION = '3.17.0';

	// Plugin Slug
	protected $plugin_slug = 'reviewer';

	// Instace of this class
	protected static $instance = null;

	function __construct()
	{
		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Session
		//add_action( 'init', array( $this, 'session_start' ) );

		// Load admin style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_scripts' ) );

		// Load custom css rules
		add_action( 'wp_head', array( $this, 'enqueue_custom_style' ) );

		// Add Shortcode
		$this->init_shortcodes();

		// Add Rating Support
		$this->rating();

		// Add widgets
		add_action( 'widgets_init', array( $this, 'widgets') );

		// Add html for PhotoSwipe
		add_action( 'wp_footer', array( $this, 'addHtmlForPhotoSwipe'), 19 );

		// Check license
		add_action('rwp_check_license', array('RWP_License_Page', 'check_remote_license'));

		// Hook for schediling reviews notice emails.
		add_action('reviewer_notify_about_new_reviews', array( 'RWP_Rating', 'send_notification'), 10, 2 );

		// Update the post meta about review box scores - users and reviewer.
		add_action('reviewer_update_postmeta_of_box_scores', array( 'RWP_User_Review', 'update_postmeta_scores'), 10, 2 );
	}

	public function session_start()
	{
		$have_captcha 	= false;
		$templates 		= self::get_option('rwp_templates');

		foreach ($templates as $template) {

			if( isset( $template['template_user_rating_options'] ) && in_array( 'rating_option_captcha', $template['template_user_rating_options'] ) ) {
				$have_captcha = true;
				break;
			}
		}

		//var_dump( $have_captcha);

		if ( $have_captcha && !session_id() ) {
	      session_start();
	   	}
	}

	public function init_shortcodes()
	{
		$includes = array( 'class-review-shortcode', 'class-table-shortcode', 'class-reviews-list-shortcode', 'class-rating-stars-shortcode', 'class-user-reviews-shortcode' );

		foreach ($includes as $file)
			include_once('includes/'. $file .'.php');

		$review_shortcode 			= RWP_Review_Shortcode::get_instance();
		$table_shortcode  			= RWP_Table_Shortcode::get_instance();
		$reviews_list_shortcode  	= RWP_Reviews_List_Shortcode::get_instance();
		$reviews_list_shortcode  	= RWP_Reviews_List_Shortcode::get_instance();
		$rating_stars_shortcode  	= RWP_Rating_Stars_Shortcode::get_instance();
		$user_reviews_shortcode  	= RWP_User_Reviews_Shortcode::get_instance();

		// Add WP filter for automatic shortcodes
		add_filter( 'the_content', array( $this, 'filter_content') );
	}

	public function filter_content( $content ) {

		// Get Templates
		$templates = self::get_option('rwp_templates');

		$terms = wp_get_object_terms( get_the_ID(),  array_keys( get_taxonomies() ) );

		$terms_keys = array();
		foreach ($terms as $term)
			$terms_keys[ $term->taxonomy ][] = $term->term_id;

		//self::pretty_print( $terms_keys );

		foreach ($templates as $template) {

			if( isset( $template['template_auto_reviews'] ) && !empty( $template['template_auto_reviews'] ) ) {

				if( is_singular( $template['template_auto_reviews'] ) && is_main_query() ) {

					if( isset( $template['template_exclude_terms'] ) ) {

						$to_exclude = false;
						foreach ($template['template_exclude_terms'] as $id) {
							$i = explode('-', $id);

							if( isset( $i[0] ) && isset( $terms_keys[ $i[0] ] ) && is_array( $terms_keys[ $i[0] ] ) && in_array( $i[1], $terms_keys[ $i[0] ] ) ) {
								$to_exclude = true;
								break;
							}
						}

						if($to_exclude) continue;
					}

					$new_content = '[rwp-review id="-1" template="'. $template['template_id'] .'"]';
					$content .= $new_content;
				}
			}
		}

		return $content;
	}

	public function rating()
	{
		$includes = array( 'class-rating', 'class-user-review' );

		foreach ($includes as $file)
			include_once('includes/'. $file .'.php');

		$rating  = RWP_Rating::get_instance();
		$reviews = RWP_User_Review::get_instance();
	}

	public function widgets()
	{
		$includes = array( 'class-reviews-widget', 'class-ratings-widget' );

		foreach ($includes as $file)
			include_once('includes/'. $file .'.php');

		register_widget( 'RWP_Reviews_Widget' );
		register_widget( 'RWP_Ratings_Widget' );
	}

	public function enqueue_public_styles() {
		wp_enqueue_style( $this->plugin_slug .'-photoswipe', plugins_url( 'assets/css/photoswipe.css', __FILE__ ), array(), RWP_Reviewer::VERSION );
		wp_enqueue_style( $this->plugin_slug .'-public', plugins_url( 'assets/css/reviewer-public.css', __FILE__ ), array('dashicons'), RWP_Reviewer::VERSION );
	}

	public function enqueue_public_scripts() {
		$pref = RWP_Reviewer::get_option( 'rwp_preferences' );
		if( isset( $pref['preferences_users_reviews_captcha'] ) && $pref['preferences_users_reviews_captcha']['enabled'] ) {
			if( !isset($pref['preferences_users_reviews_captcha']['exclude_posts']) || !in_array(get_the_ID(), $pref['preferences_users_reviews_captcha']['exclude_posts']) ) {
				wp_enqueue_script( $this->plugin_slug .'-recaptcha', 'https://www.google.com/recaptcha/api.js?onload=rwpReCaptchaLoad&render=explicit', array(), RWP_Reviewer::VERSION, true );
			}
		}

		// wp_enqueue_script( $this->plugin_slug .'-nouislider-plugin', plugins_url( 'assets/js/jquery.nouislider.all.min.js', __FILE__ ), array( 'jquery'), RWP_Reviewer::VERSION, true );
		// wp_enqueue_script( $this->plugin_slug .'-photoswipe', plugins_url( 'assets/js/photoswipe.js', __FILE__ ), array(), RWP_Reviewer::VERSION, true );
		// wp_enqueue_script( $this->plugin_slug .'-knob-plugin', plugins_url( 'assets/js/jquery.knob.js', __FILE__ ), array('jquery'), RWP_Reviewer::VERSION, true );
		// wp_enqueue_script( $this->plugin_slug .'-public-script', plugins_url( 'assets/js/reviewer.public.min.js', __FILE__ ), array('jquery'), RWP_Reviewer::VERSION, true );
		// wp_enqueue_script( $this->plugin_slug .'-reviews-boxes-script', plugins_url( 'assets/js/reviewer-reviews-boxes.js', __FILE__ ), array('jquery'), RWP_Reviewer::VERSION, true );
		// wp_enqueue_script( $this->plugin_slug .'-widget-users-reviews-script', plugins_url( 'assets/js/reviewer-widget-users-reviews.js', __FILE__ ), array('jquery'), RWP_Reviewer::VERSION, true );
		// wp_enqueue_script( $this->plugin_slug .'-shortcode-user-reviews-script', plugins_url( 'assets/js/reviewer-shortcode-user-reviews.js', __FILE__ ), array('jquery'), RWP_Reviewer::VERSION, true );
		wp_enqueue_script( $this->plugin_slug .'-front-end-script', plugins_url( 'assets/js/reviewer-front-end.js', __FILE__ ), array('jquery'), RWP_Reviewer::VERSION, true );

		// Send php constants to js scope
		// wp_localize_script( $this->plugin_slug .'-reviews-boxes-script', 'rwpConstants', array( 'debugVue' => WP_DEBUG ) );
		// wp_localize_script( $this->plugin_slug .'-widget-users-reviews-script', 'rwpConstants', array( 'debugVue' => WP_DEBUG ) );
		wp_localize_script( $this->plugin_slug .'-front-end-script', 'rwpConstants', array( 'debugVue' => WP_DEBUG ) );
	}

	public function enqueue_custom_style() {

		$pref = RWP_Reviewer::get_option( 'rwp_preferences' );

		if( isset( $pref['preferences_custom_css'] ) && !empty( $pref['preferences_custom_css'] ) ) {

			$output = "<style type=\"text/css\">". $pref['preferences_custom_css'] ."</style>";

			echo $output;
	    }
	}

	public function get_plugin_slug()
	{
		return $this->plugin_slug;
	}

	public static function get_instance()
	{
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public static function activate()
	{
		global  $wp_version;

		if( version_compare( $wp_version, '4.2', '>=' ) && version_compare( PHP_VERSION, '5.3', '>=' ) ) { // Check ok!

			// Set default preferences
			$pref = RWP_Reviewer::get_option( 'rwp_preferences' );

			if ( empty( $pref ) ) {
				$default = array(
					'preferences_authorization' 	=> 'all',
				    'preferences_rating_mode' 		=> 'full_five_stars',
				    'preferences_post_types' 		=> array('post', 'page'),
				    'preferences_step' 				=> 0.5
				);
				add_option( 'rwp_preferences', $default );
			}

			// Init templates
			$temps = RWP_Reviewer::get_option( 'rwp_templates' );

			if ( empty( $temps ) )
				add_option( 'rwp_templates', array() );

			// Check if there are some previous version references
			$old = RWP_Reviewer::get_option( 'rwp_reviewer_templates' );

			if ( empty( $old ) )
				update_option('rwp_restore', 1);

			// Init support info
			$sup = RWP_Reviewer::get_option( 'rwp_support' );

			if ( empty( $sup ) )
				add_option( 'rwp_support', array() );

			// Init support info
			$pend = get_option( 'rwp_pending_ratings');

			if ( !$pend )
				add_option( 'rwp_pending_ratings', 0 );

		} else {
			RWP_Notification::push( 'activation', __('The Reviewer Plugin needs WordPress >= 4.2 and PHP >= 5.3 to work. If data are correct you can ignore the notice', 'reviewer' ), 'error' );
		}

		// Add Cap to admin
		$capManageReviews = 'rwp_manage_user_reviews';
		$role = get_role('administrator');
		if( !is_null( $role ) ) {
			$has_cap    = ( isset( $role->capabilities[ $capManageReviews ] ) && $role->capabilities[ $capManageReviews ] == 1 );
	        if( !$has_cap ) {
	            $role->add_cap( $capManageReviews );
	        }
		}

		$license = get_option( get_option( 'rwp_license' ) );
		if( !$license ) {
			RWP_Notification::pushLicenseNotice();
		} else {
			RWP_Notification::delete('license');
		}

		RWP_License_Page::schedule_license_checking();

		RWP_Notification::delete('support');
		return 0;
	}

	public static function deactivate()
	{
		RWP_License_Page::unschedule_license_checking();
		RWP_Rating::unschedule_reviews_notice();
		RWP_Rating::unschedule_postmeta_updating();
	}

	public static function pretty_print( $data = array() )
	{
		echo '<pre>';
		print_r( $data );
		echo '</pre>';
	}

	public static function get_decimal_places($value)
	{
		$str_value = "" . $value;
		$parts = explode('.', $str_value);

		return (isset( $parts[1] ) ) ? strlen($parts[1]) : 0;
	}

	public static function get_option( $option_name )
	{
		$value = get_option( $option_name );
		return ( $value === FALSE) ? array() : $value;
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

	public static function get_in_base( $current_base, $base, $value )
	{
		return ( ( floatval($value) * floatval($base) ) / floatval($current_base) );
	}

	public static function format_number( $number ) {

		$floatval 	= floatval( $number );
		$intval 	= intval( $floatval );
		$decimalval	= $floatval - $intval;

		if( $decimalval == 0 )
			return $intval;

		return $floatval;
	}

	public function load_plugin_textdomain()
	{
		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );
	}


	public static function filter_ratings( $ratings, $moderation ) {

		$count = count( $ratings );

		for ( $i = 0; $i < $count; $i++ ) {

			if( isset( $ratings[ $i ]['rating_status'] ) && $ratings[ $i ]['rating_status'] == 'pending' )
				unset( $ratings[ $i ] );
		}

		return $ratings;
	}

	public static function get_review_overall_score( $review = array() ) {

		$scores 		= self::review_field( 'review_scores', $review, true );
		$preferences 	= self::get_option( 'rwp_preferences' );
		$precision 		= self::get_decimal_places( self::preferences_field( 'preferences_step', $preferences, true ) );

		$avg = round( self::get_avg( $scores ), $precision );

		return self::format_number( $avg );
	}

	public static function get_users_overall_score( $data = array(), $post_id, $review_id ) {

		$scores = $data['scores'];

		$preferences 	= self::get_option( 'rwp_preferences' );
		$precision 		= self::get_decimal_places( self::preferences_field( 'preferences_step', $preferences, true ) );

		$avg = self::get_avg( $scores );

		// Old Rating
		$ratings 		= get_post_meta( $post_id, 'rwp_ratings', true );
		$old_ratings	= ( isset( $ratings[ $review_id ] ) ) ? $ratings[ $review_id ] : array( 'rating_count' => 0, 'rating_total_score' => 0 );

		if( $old_ratings['rating_count'] > 0 ) {
			$old_avg 	= $old_ratings['rating_total_score'] / $old_ratings['rating_count'];

			$result = ( $avg > 0 ) ? round( (($avg + $old_avg ) / 2), $precision ) : round( $old_avg, $precision );

		} else {
			$result 	= round( $avg, $precision);
		}

		return array('score' => self::format_number( $result ), 'count' => ($data['count'] + $old_ratings['rating_count'] ));
	}

	public static function get_ratings_single_scores( $post_id, $review_id, $template_id ) {

		// Old Rating
		//$ratings 		= get_post_meta( $post_id, 'rwp_ratings', true );
		//$old_ratings	= ( isset( $ratings[ $review_id ] ) ) ? $ratings[ $review_id ] : array( 'rating_count' => 0, 'rating_total_score' => 0 );

		// Templates
		$templates 		= self::get_option('rwp_templates');
		$template 		= (isset( $templates[ $template_id ] )) ? $templates[ $template_id ] : array();

		// Preferences
		$preferences 	= RWP_Reviewer::get_option( 'rwp_preferences' );

		// New Ratings
		$ratings 		= get_post_meta( $post_id, 'rwp_rating_' . $review_id );
		$new_ratings 	= is_array( $ratings ) ? $ratings : array();

		$moderation 	= self::preferences_field( 'preferences_rating_before_appears', $preferences, true );
		$new_ratings	= RWP_Reviewer::filter_ratings( $new_ratings, $moderation );

		if( empty( $template ) ) return array();

		$order = self::template_field( 'template_criteria_order', $template,  true );
		$criteria = self::template_field( 'template_criterias', $template,  true );
		$order = ( $order == null ) ? array_keys( $criteria ) : $order;

		$scores 	= array();
		$count 		= count( $new_ratings );

		if( $count > 0 ) {

			$precision  = self::get_decimal_places( self::preferences_field( 'preferences_step', $preferences, true ) );

			foreach ($order as $i) {
				$scores[$i] = 0;
			}

			foreach ($new_ratings as $rating) {

				if( !is_array($rating) ) {
					$rating = maybe_unserialize( $rating );
				}

				foreach ($order as $i) {
					$scores[$i] += ( isset( $rating['rating_score'] ) && isset( $rating['rating_score'][$i] ) )  ? $rating['rating_score'][$i] : 0;
				}
			}



			foreach ($order as $i) {
				$scores[$i] = round( $scores[$i] / $count, $precision);
			}

		} else {

			foreach ($order as $i) {
				$scores[ $i ] = 0;
			}
		}

		//self::pretty_print($new_ratings);
		//self::pretty_print($old_ratings);
		//self::pretty_print($scores);

		return array('scores' => $scores, 'count' => $count);
	}

	public static function rating_cmp($a, $b)
	{
	    if ($a['rating_date'] == $b['rating_date']) {
	        return 0;
	    }
	    return ($a['rating_date'] > $b['rating_date']) ? -1 : 1;
	}

	public static function get_stars( $scores = array(), $template = array(), $stars = 5 ) {

		$avg 	= ( is_array( $scores ) ) ? RWP_Reviewer::get_avg( $scores ) : floatval( $scores );
		$value 	= RWP_Reviewer::get_in_base( self::template_field('template_maximum_score', $template, true), $stars, $avg);

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

			$html .= '<span class="rwp-s '. $oe .' '. $fx .'" style="background-image: url('. self::template_field('template_rate_image', $template, true) .');"></span>';

			$j += .5;
		}

		$html .= '</div><!-- /stars -->';

		return $html;
	}

	public static function get_score_bar( $score = array(), $template = array(), $theme = '', $size = 0 ) {

		$avg 	= ( is_array( $score ) ) ? RWP_Reviewer::get_avg( $score ) : floatval( $score );

		$max 	= floatval( self::template_field('template_maximum_score', $template, true) );
		$value 	= floatval( $avg );
		$range 	= explode( '-', self::template_field('template_score_percentages', $template, true) );
		$low 	= floatval( $range[0] );
		$high 	= floatval( $range[1] );

		$pct = round ( (( $value / $max ) * 100), 1);

		if ( $pct < $low ) {
			$color = self::template_field('template_low_score_color', $template, true);
		} else if( $pct > $high ) {
			$color = self::template_field('template_high_score_color', $template, true);
		} else {
			$color = self::template_field('template_medium_score_color', $template, true);
		}

		$in = ( !empty( $theme ) ) ? '<span class="rwp-criterion-score" style="font-size: '. ($size + 2) .'px;">'. RWP_Reviewer::format_number( $avg ) .'</span>' : '';

		return '<div class="rwp-score-bar" style="width: '. $pct .'%; background: '. $color .';">'. $in .'</div>';
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

	public static function preferences_field( $field, $preferences, $return = false ) {

		$default_preferences = RWP_Preferences_Page::get_preferences_fields();

		$value = isset( $preferences[ $field ] ) ? $preferences[ $field ] : $default_preferences[ $field ]['default'];

		if( $return )
			return $value;

		echo $value;
	}

	public static function set_html_content_type() {
		return 'text/html';
	}

	public static function sanitizeText( $text ) {
        return sanitize_text_field( stripslashes_deep( $text ) );
    }

    public static function getUploadLimit() {
		$max_upload     = floatval( ini_get('upload_max_filesize') );
        $max_post       = floatval( ini_get('post_max_size') );
        $memory_limit   = floatval( ini_get('memory_limit') );

        return min( $max_upload, $max_post, $memory_limit );
	}

	public static function add_custom_cron_schedule( $schedules ){
		$schedules['minute'] = array(
			'interval' => 60 * 1,
			'display' => __('Every Minute')
		);
		return $schedules;
	}

	public function addHtmlForPhotoSwipe()
	{
	?>
		<!-- Root element of PhotoSwipe. Must have class pswp. -->
		<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">

		    <!-- Background of PhotoSwipe.
		         It's a separate element as animating opacity is faster than rgba(). -->
		    <div class="pswp__bg"></div>

		    <!-- Slides wrapper with overflow:hidden. -->
		    <div class="pswp__scroll-wrap">

		        <!-- Container that holds slides.
		            PhotoSwipe keeps only 3 of them in the DOM to save memory.
		            Don't modify these 3 pswp__item elements, data is added later on. -->
		        <div class="pswp__container">
		            <div class="pswp__item"></div>
		            <div class="pswp__item"></div>
		            <div class="pswp__item"></div>
		        </div>

		        <!-- Default (PhotoSwipeUI_Default) interface on top of sliding area. Can be changed. -->
		        <div class="pswp__ui pswp__ui--hidden">

		            <div class="pswp__top-bar">

		                <!--  Controls are self-explanatory. Order can be changed. -->

		                <div class="pswp__counter"></div>

		                <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>

		                <button class="pswp__button pswp__button--share" title="Share"></button>

		                <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>

		                <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>

		                <!-- Preloader demo http://codepen.io/dimsemenov/pen/yyBWoR -->
		                <!-- element will get class pswp__preloader-active when preloader is running -->
		                <div class="pswp__preloader">
		                    <div class="pswp__preloader__icn">
		                      <div class="pswp__preloader__cut">
		                        <div class="pswp__preloader__donut"></div>
		                      </div>
		                    </div>
		                </div>
		            </div>

		            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
		                <div class="pswp__share-tooltip"></div>
		            </div>

		            <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
		            </button>

		            <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
		            </button>

		            <div class="pswp__caption">
		                <div class="pswp__caption__center"></div>
		            </div>

		        </div>

		    </div>

		</div>
		<?php
	}
}
