<?php

/*  Register Widget Areas
/* ----------------------------------------------------------------- */

if (!function_exists('knowhere_widgets_register')) {

	function knowhere_widgets_register () {

		$before_widget = '<div id="%1$s" class="widget %2$s">';

		$widget_args = array(
			'before_widget' => $before_widget,
			'after_widget' => '</div>',
			'before_title' => '<h3 class="kw-widget-title">',
			'after_title' => '</h3>'
		);

		// General Widget Area
		register_sidebar(array(
			'name' => esc_html__('General Widget Area', 'knowherepro'),
			'id' => 'general-widget-area',
			'description'   => esc_html__('For all pages and posts.', 'knowherepro'),
			'before_widget' => $widget_args['before_widget'],
			'after_widget' => $widget_args['after_widget'],
			'before_title' => $widget_args['before_title'],
			'after_title' => $widget_args['after_title']
		));

		for ($i = 1; $i <= 10; $i++) {
			register_sidebar(array(
				'name' => 'Footer Row - widget ' . $i,
				'id' => 'footer-row-' . $i,
				'before_widget' => $widget_args['before_widget'],
				'after_widget' => $widget_args['after_widget'],
				'before_title' => $widget_args['before_title'],
				'after_title' => $widget_args['after_title']
			));
		}
	}

	add_action( 'widgets_init', 'knowhere_widgets_register' );

}

/*	Include Widgets
/* ----------------------------------------------------------------- */

if (!function_exists('knowhere_unregistered_widgets')) {
	function knowhere_unregistered_widgets () {
		unregister_widget( 'LayerSlider_Widget' );
	}
	add_action('widgets_init', 'knowhere_unregistered_widgets', 1);
}

/*	Widget Facebook Like Box
/* ----------------------------------------------------------------- */

if (!class_exists('knowhere_like_box_facebook')) {

	class knowhere_like_box_facebook extends WP_Widget {

		private static $id_of_like_box = 0;

		function __construct() {
			$widget_ops = array( 'classname' => 'like_box_facebook', 'description' => 'Like box Facebook' ); // Widget Settings
			$control_ops = array( 'id_base' => 'like_box_facebook' ); // Widget Control Settings

			parent::__construct( 'like_box_facebook', 'Like box Facebook', $widget_ops, $control_ops ); // Create the widget
		}

		function widget($args, $instance) {
			self::$id_of_like_box++;
			extract( $args );
			$title = $instance['title'];
			$profile_id = $instance['profile_id'];
			$facebook_likebox_theme = $instance['facebook_likebox_theme'];
			$width = $instance['width'];
			$height = $instance['height'];
			$connections = $instance['connections'];
			$header = ($instance['header'] == 'yes') ? 'true' : 'false';

			// Before widget //
			echo $before_widget;

			// Title of widget //
			if ( $title ) { echo $before_title . $title . $after_title; }

			// Widget output //
			echo '<iframe id="like_box_widget_'. self::$id_of_like_box .'" src="https://www.facebook.com/plugins/likebox.php?href='. $profile_id .'&amp;colorscheme='. $facebook_likebox_theme .'&amp;width='. $width .'&amp;height='. $height .'&amp;connections='. $connections .'&amp;stream=false&amp;show_border=false&amp;header='. $header .'&amp;" scrolling="no" frameborder="0" allowTransparency="true" style="width:'. $width .'px; height:'. $height .'px;"></iframe>';

			echo $after_widget;
		}

		// Update Settings //
		function update ($new_instance, $old_instance) {
			$instance = $old_instance;

			$instance['title'] = strip_tags($new_instance['title']);
			$instance['profile_id'] = $new_instance['profile_id'];
			$instance['facebook_likebox_theme'] = $new_instance['facebook_likebox_theme'];
			$instance['width'] = $new_instance['width'];
			$instance['height'] = $new_instance['height'];
			$instance['connections'] = $new_instance['connections'];
			$instance['header'] =  $new_instance['header'];
			return $instance;
		}

		/* admin page opions */
		function form($instance) {

			$defaults = array(
				'title' => esc_html__('Like Us on Facebook', 'knowherepro'),
				'profile_id' => '',
				'facebook_likebox_theme' => 'light',
				'width' => '235',
				'height' => '345',
				'connections' => 10,
				'header' => 'yes'
			);
			$instance = wp_parse_args( (array) $instance, $defaults );
			?>

			<p class="flb_field">
				<label for="title"><?php esc_html_e('Title', 'knowherepro') ?>:</label><br>
				<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" class="widefat">
			</p>

			<p class="flb_field">
				<label for="<?php echo $this->get_field_id('profile_id'); ?>"><?php esc_html_e('Page ID', 'knowherepro') ?>:</label><br>
				<input id="<?php echo $this->get_field_id('profile_id'); ?>" name="<?php echo $this->get_field_name('profile_id'); ?>" type="text" value="<?php echo $instance['profile_id']; ?>" class="widefat">
			</p>

			<p>
				<label><?php esc_html_e('Facebook Like box Theme', 'knowherepro'); ?>:</label><br>
				<select name="<?php echo $this->get_field_name('facebook_likebox_theme'); ?>">
					<option selected="selected" value="light"><?php esc_html_e('Light', 'knowherepro') ?></option>
					<option value="dark"><?php esc_html_e('Dark', 'knowherepro') ?></option>
				</select>
			</p>

			<p class="flb_field">
				<label for="<?php echo $this->get_field_id('width'); ?>"><?php esc_html_e('Like box Width', 'knowherepro') ?>:</label>
				<br>
				<input id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $instance['width']; ?>" class="" size="3">
				<small>(<?php esc_html_e('px', 'knowherepro') ?>)</small>
			</p>

			<p class="flb_field">
				<label for="<?php echo $this->get_field_id('height'); ?>"><?php esc_html_e("Like box Height", 'knowherepro') ?>:</label>
				<br>
				<input id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $instance['height']; ?>" class="" size="3">
				<small>(<?php esc_html_e('px', 'knowherepro') ?>)</small>
			</p>

			<p class="flb_field">
				<label for="<?php echo $this->get_field_id('connections'); ?>"><?php esc_html_e('Number of connections', 'knowherepro') ?>:</label>
				<br>
				<input id="<?php echo $this->get_field_id('connections'); ?>" name="<?php echo $this->get_field_name('connections'); ?>" type="text" value="<?php echo $instance['connections']; ?>" class="" size="3">
				<small>(<?php esc_html_e("Max. 100", 'knowherepro') ?>)</small>
			</p>

			<p class="flb_field">
				<label><?php esc_html_e('Show Header', 'knowherepro') ?>:</label><br>
				<input name="<?php echo $this->get_field_name('header'); ?>" type="radio" value="yes" <?php checked( $instance[ 'header' ], 'yes' ); ?>><?php esc_html_e("Yes", 'knowherepro') ?>
				<input name="<?php echo $this->get_field_name('header'); ?>" type="radio" value="no" <?php checked( $instance[ 'header' ], 'no'); ?>><?php esc_html_e("No", 'knowherepro') ?>
			</p>

			<?php
		}
	}

}

if ( !class_exists('knowhere_widget_popular_widget') ) {

	class knowhere_widget_popular_widget extends WP_Widget {

		public $defaults = array();
		public $version = "1.0.1";

		function __construct() {

			parent::__construct( 'popular-widget', esc_html__('KnowherePro Popular and Latest Posts', 'knowherepro'),
				array(
					'classname' => 'widget_popular_posts',
					'description' => esc_html__("Display most popular and latest posts", 'knowherepro')
				)
			);

			define('KNOWHERE_POPWIDGET_URL', get_template_directory_uri() . '/includes/widgets/popular-widget/');
			define('KNOWHERE_POPWIDGET_ABSPATH', str_replace("\\", "/", get_template_directory() . '/includes/widgets/popular-widget'));

			$this->defaults = array(
				'title' => '',
				'counter' => false,
				'excerptlength' => 5,
				'meta_key' => '_popular_views',
				'calculate' => 'visits',
				'limit' => 3,
				'thumb' => false,
				'excerpt' => false,
				'type' => 'popular'
			);

			add_action('admin_enqueue_scripts', array($this, 'load_admin_styles'));
			add_action('wp_enqueue_scripts', array($this, 'load_scripts_styles'), 1);
			add_action('wp_ajax_popwid_page_view_count', array($this, 'set_post_view'));
			add_action('wp_ajax_nopriv_popwid_page_view_count', array($this, 'set_post_view'));

		}

		function widget($args, $instance) {
			if (file_exists(KNOWHERE_POPWIDGET_ABSPATH . '/inc/widget.php')) {
				include(KNOWHERE_POPWIDGET_ABSPATH . '/inc/widget.php');
			}
		}

		function form($instance) {
			if (file_exists(KNOWHERE_POPWIDGET_ABSPATH . '/inc/form.php')) {
				include(KNOWHERE_POPWIDGET_ABSPATH . '/inc/form.php');
			}
		}

		function update($new_instance, $old_instance) {
			foreach ($new_instance as $key => $val) {
				if (is_array($val)) {
					$new_instance[$key] = $val;
				} elseif (in_array($key, array('limit', 'excerptlength'))) {
					$new_instance[$key] = intval($val);
				} elseif (in_array($key, array('calculate'))) {
					$new_instance[$key] = trim($val, ',');
				}
			}
			if (empty($new_instance['meta_key'])) {
				$new_instance['meta_key'] = $this->defaults['meta_key'];
			}
			return $new_instance;
		}

		function load_admin_styles() {
			global $pagenow;
			if ($pagenow != 'widgets.php' ) return;

			wp_enqueue_style( 'knowhere_popular-admin', KNOWHERE_POPWIDGET_URL . 'css/admin.css', NULL, $this->version );
			wp_enqueue_script( 'knowhere_popular-admin', KNOWHERE_POPWIDGET_URL . 'js/admin.js', array('jquery',), $this->version, true );
		}

		function load_scripts_styles(){

			if (! is_admin() || is_active_widget( false, false, $this->id_base, true )) {
				wp_enqueue_script( 'knowhere_popular-widget', KNOWHERE_POPWIDGET_URL . 'js/pop-widget.js', array('jquery'), $this->version, true);
			}

			if (! is_singular() && ! apply_filters( 'pop_allow_page_view', false )) return;

			global $post;
			wp_localize_script ( 'knowhere_popular-widget', 'popwid', apply_filters( 'pop_localize_script_variables', array(
				'postid' => $post->ID
			), $post ));
		}

		function field_id($field) {
			echo $this->get_field_id($field);
		}

		function field_name($field) {
			echo $this->get_field_name($field);
		}

		function limit_words($string, $word_limit) {
			$words = explode(" ", wp_strip_all_tags(strip_shortcodes($string)));

			if ($word_limit && (str_word_count($string) > $word_limit)) {
				return $output = implode(" ",array_splice( $words, 0, $word_limit )) ."...";
			} else if( $word_limit ) {
				return $output = implode(" ", array_splice( $words, 0, $word_limit ));
			} else {
				return $string;
			}
		}

		function get_post_image($post_id, $size) {

			if (has_post_thumbnail($post_id) && function_exists('has_post_thumbnail')) {
				return get_the_post_thumbnail($post_id, $size);
			}

			$images = get_children(array(
				'order' => 'ASC',
				'numberposts' => 1,
				'orderby' => 'menu_order',
				'post_parent' => $post_id,
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
			), $post_id, $size);

			if (empty($images)) return false;

			foreach($images as $image) {
				return wp_get_attachment_image($image->ID, $size);
			}
		}

		function set_post_view() {

			if (empty($_POST['postid'])) return;
			if (!apply_filters('pop_set_post_view', true)) return;

			global $wp_registered_widgets;

			$meta_key_old = false;
			$postid = (int) $_POST['postid'];
			$widgets = get_option($this->option_name);

			foreach ((array) $widgets as $number => $widget) {
				if (!isset($wp_registered_widgets["popular-widget-{$number}"])) continue;

				$instance = $wp_registered_widgets["popular-widget-{$number}"];
				$meta_key = isset( $instance['meta_key'] ) ? $instance['meta_key'] : '_popular_views';

				if ($meta_key_old == $meta_key) continue;

				do_action( 'pop_before_set_pos_view', $instance, $number );

				if (isset($instance['calculate']) && $instance['calculate'] == 'visits') {
					if (!isset( $_COOKIE['popular_views_'.COOKIEHASH])) {
						setcookie( 'popular_views_' . COOKIEHASH, "$postid|", 0, COOKIEPATH );
						update_post_meta( $postid, $meta_key, get_post_meta( $postid, $meta_key, true ) +1 );
					} else {
						$views = explode("|", $_COOKIE['popular_views_' . COOKIEHASH]);
						foreach( $views as $post_id ){
							if( $postid == $post_id ) {
								$exist = true;  break;
							}
						}
					}
					if (empty($exist)) {
						$views[] = $postid;
						setcookie( 'popular_views_' . COOKIEHASH, implode( "|", $views ), 0 , COOKIEPATH );
						update_post_meta( $postid, $meta_key, get_post_meta( $postid, $meta_key, true ) +1 );
					}
				} else {
					update_post_meta( $postid, $meta_key, get_post_meta( $postid, $meta_key, true ) +1 );
				}
				$meta_key_old = $meta_key;
				do_action( 'pop_after_set_pos_view', $instance, $number );
			}
			die();
		}

		function get_latest_posts() {
			extract($this->instance);
			$posts = wp_cache_get("pop_latest_{$number}", 'pop_cache');

			if ($posts == false) {
				$args = array(
					'suppress_fun' => true,
					'post_type' => 'post',
					'posts_per_page' => $limit
				);
				$posts = get_posts(apply_filters('pop_get_latest_posts_args', $args));
				wp_cache_set("pop_latest_{$number}", $posts, 'pop_cache');

			}
			return $this->display_posts($posts);
		}

		function get_most_viewed() {
			extract($this->instance);
			$viewed = wp_cache_get("pop_viewed_{$number}", 'pop_cache');

			if ($viewed == false) {
				global $wpdb;  $join = $where = '';
				$viewed = $wpdb->get_results( $wpdb->prepare( "SELECT SQL_CALC_FOUND_ROWS p.*, meta_value as views FROM $wpdb->posts p " .
					"JOIN $wpdb->postmeta pm ON p.ID = pm.post_id AND meta_key = %s AND meta_value != '' " .
					"WHERE 1=1 AND p.post_status = 'publish' AND post_date >= '{$this->time}' AND p.post_type IN ( 'post' )" .
					"GROUP BY p.ID ORDER BY ( meta_value+0 ) DESC LIMIT $limit", $meta_key));
				wp_cache_set( "pop_viewed_{$number}", $viewed, 'pop_cache');
			}
			return $this->display_posts($viewed);
		}

		function display_posts($posts) {

			if ( empty ($posts) && !is_array($posts) ) return;

			extract( $this->instance );

			ob_start(); ?>

			<?php foreach ( $posts as $key => $post ) : $commentCount = get_comments_number($post->ID);
			?>

			<div class="kw-entry-wrap">

				<div class="kw-entry">

					<div class="kw-entry-info">

						<!-- - - - - - - - - - - - - - End of Meta - - - - - - - - - - - - - - - - -->

						<a href="<?php echo esc_url(get_permalink($post->ID)) ?>">
							<?php echo esc_html($post->post_title) ?>
						</a>

						<span class="post-date"><?php echo get_the_date(); ?></span>

					</div>

				</div>

			</div>

			<?php endforeach; return ob_get_clean();
		}

	}
}

/*	Widget Social Links
/* ----------------------------------------------------------------- */

if (!class_exists('knowhere_widget_social_links')) {

	class knowhere_widget_social_links extends Knowhere_Widget {

		function __construct() {
			$this->widget_cssclass    = 'widget_social_links';
			$this->widget_description =  esc_html__('Displays website social links', 'knowherepro');
			$this->widget_id          = 'widget-social-links';
			$this->widget_name        = esc_html__('KnowherePro Social Links', 'knowherepro');
			$this->settings           = array(
				'title'  => array(
					'type'  => 'text',
					'label' => esc_html__( 'Title', 'knowherepro' ),
					'std'   => esc_html__( 'Follow Us', 'knowherepro' )
				),
				'linkedin_links'  => array(
					'type'  => 'text',
					'label' => esc_html__('LinkedIn Link', 'knowherepro'),
					'std'   => ''
				),
				'tumblr_links'  => array(
					'type'  => 'text',
					'label' => esc_html__('Tumblr Link', 'knowherepro'),
					'std'   =>''
				),
				'vimeo_links'  => array(
					'type'  => 'text',
					'label' => esc_html__('Vimeo Link', 'knowherepro'),
					'std'   => ''
				),
				'facebook_links'  => array(
					'type'  => 'text',
					'label' => esc_html__('Facebook Link', 'knowherepro'),
					'std'   => ''
				),
				'flickr_links'  => array(
					'type'  => 'text',
					'label' => esc_html__('Flickr Link', 'knowherepro'),
					'std'   => ''
				),
				'twitter_links'  => array(
					'type'  => 'text',
					'label' => esc_html__('Twitter Link', 'knowherepro'),
					'std'   => ''
				),
				'gplus_links'  => array(
					'type'  => 'text',
					'label' => esc_html__('Google Plus Link', 'knowherepro'),
					'std'   => ''
				),
				'pinterest_links'  => array(
					'type'  => 'text',
					'label' => esc_html__('Pinterest Link', 'knowherepro'),
					'std'   => ''
				),
				'instagram_links'  => array(
					'type'  => 'text',
					'label' => esc_html__('Instagram Link', 'knowherepro'),
					'std'   => ''
				),
				'youtube_links'  => array(
					'type'  => 'text',
					'label' => esc_html__('Youtube Link', 'knowherepro'),
					'std'   => ''
				)
			);
			parent::__construct();
		}

		function widget($args, $instance) {
			$data = array();
			$data['linkedin_links'] = isset( $instance['linkedin_links'] ) ? $instance['linkedin_links'] : $this->settings['linkedin_links']['std'];
			$data['tumblr_links'] = isset( $instance['tumblr_links'] ) ? $instance['tumblr_links'] : $this->settings['tumblr_links']['std'];
			$data['vimeo_links'] = isset( $instance['vimeo_links'] ) ? $instance['vimeo_links'] : $this->settings['vimeo_links']['std'];
			$data['facebook_links'] = isset( $instance['facebook_links'] ) ? $instance['facebook_links'] : $this->settings['facebook_links']['std'];
			$data['flickr_links'] = isset( $instance['flickr_links'] ) ? $instance['flickr_links'] : $this->settings['flickr_links']['std'];
			$data['youtube_links'] = isset( $instance['youtube_links'] ) ? $instance['youtube_links'] : $this->settings['youtube_links']['std'];
			$data['twitter_links'] = isset( $instance['twitter_links'] ) ? $instance['twitter_links'] : $this->settings['twitter_links']['std'];
			$data['gplus_links'] = isset( $instance['gplus_links'] ) ? $instance['gplus_links'] : $this->settings['gplus_links']['std'];
			$data['pinterest_links'] = isset( $instance['pinterest_links'] ) ? $instance['pinterest_links'] : $this->settings['pinterest_links']['std'];
			$data['instagram_links'] = isset( $instance['instagram_links'] ) ? $instance['instagram_links'] : $this->settings['instagram_links']['std'];

			$this->widget_start( $args, $instance );
				echo Knowhere_Helper::output_widgets_html('social_links', $data);
			$this->widget_end($args);
		}

	}
}

/*	Widget Contact Us
/* ----------------------------------------------------------------- */

if (!class_exists('knowhere_widget_contact_us')) {

	class knowhere_widget_contact_us extends WP_Widget {

		function __construct() {
			$settings = array('classname' => 'widget_contact_us', 'description' => esc_html__('Displays contact us', 'knowherepro'));

			parent::__construct(__CLASS__, esc_html__('KnowherePro Contact Us', 'knowherepro'), $settings);
		}

		function widget($args, $instance) {
			extract($args, EXTR_SKIP);

			$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
			$text = empty($instance['text']) ? '' : $instance['text'];
			$address = empty($instance['address']) ? '' : $instance['address'];
			$phone = empty($instance['phone']) ? '' : $instance['phone'];
			$fax = empty($instance['fax']) ? '' : $instance['fax'];
			$email = empty($instance['email']) ? '' : $instance['email'];
			$facebook = empty($instance['facebook']) ? '' : $instance['facebook'];
			$googleplus = empty($instance['googleplus']) ? '' : $instance['googleplus'];
			$twitter = empty($instance['twitter']) ? '' : $instance['twitter'];
			$linkedin = empty($instance['linkedin']) ? '' : $instance['linkedin'];

			ob_start(); ?>

			<?php echo $before_widget; ?>

			<?php if ( $title !== '' ): ?>
				<?php echo $before_title . $title . $after_title; ?>
			<?php endif; ?>

			<?php if ( !empty($text) ): ?>
				<p><?php echo sprintf('%s', $text); ?></p>
			<?php endif; ?>

			<dl class="kw-def-list">

				<?php if (!empty($address)): ?>
					<dt><?php echo esc_html__('Address', 'knowherepro') ?>:</dt>
					<dd><?php echo sprintf('%s', $address) ?></dd>
				<?php endif; ?>

				<?php if (!empty($phone)): ?>
					<dt><?php echo esc_html__('Phone', 'knowherepro') ?>:</dt>
					<dd><?php echo sprintf('%s', $phone) ?></dd>
				<?php endif; ?>

				<?php if (!empty($fax)): ?>
					<dt><?php echo esc_html__('Fax', 'knowherepro') ?>:</dt>
					<dd><?php echo esc_html($fax) ?></dd>
				<?php endif; ?>

				<?php if (!empty($email)): ?>
					<dt><?php echo esc_html__('Email', 'knowherepro') ?>:</dt>
					<dd><a href="mailto:<?php echo antispambot($email, 1) ?>"><?php echo esc_html($email) ?></a></dd>
				<?php endif; ?>

			</dl>

			<ul class="kw-social-links">

				<?php if ( !empty($facebook) ): ?>
					<li><a href="<?php echo esc_url($facebook) ?>"><i class="fa fa-facebook"></i></a></li>
				<?php endif; ?>

				<?php if ( !empty($googleplus) ): ?>
					<li><a href="<?php echo esc_url($googleplus) ?>"><i class="fa fa-google-plus"></i></a></li>
				<?php endif; ?>

				<?php if ( !empty($twitter) ): ?>
					<li><a href="<?php echo esc_url($twitter) ?>"><i class="fa fa-twitter"></i></a></li>
				<?php endif; ?>

				<?php if ( !empty($linkedin) ): ?>
					<li><a href="<?php echo esc_url($linkedin) ?>"><i class="fa fa-linkedin"></i></a></li>
				<?php endif; ?>

			</ul>

			<?php echo $after_widget; ?>

			<?php echo ob_get_clean();
		}

		function update($new_instance, $old_instance) {
			$instance = $old_instance;
			foreach($new_instance as $key => $value) {
				$instance[$key]	= strip_tags($new_instance[$key]);
			}
			return $instance;
		}

		function form($instance) {

			$defaults = array(
				'title' => esc_html__('Contact Info', 'knowherepro'),
				'text' => '',
				'address' => '9870 St Vincent Place, Glasgow, DC 45 Fr 45',
				'phone' => '+1 800 559 6580',
				'fax' => '+1 800 889 9898',
				'email' => '',
				'facebook' => '',
				'googleplus' => '',
				'twitter' => '',
				'linkedin' => '',
			);
			$instance = wp_parse_args( (array) $instance, $defaults );
			?>

			<p>
				<label><?php esc_html_e('Title', 'knowherepro');?>:
					<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" type="text" />
				</label>
			</p>

			<p>
				<label><?php esc_html_e('Text', 'knowherepro');?>:
					<textarea name="<?php echo $this->get_field_name( 'text' ); ?>" id="<?php echo $this->get_field_id( 'text' ); ?>" cols="30" class="widefat" rows="10"><?php echo $instance['text']; ?></textarea>
				</label>
			</p>

			<p>
				<label><?php esc_html_e('Address', 'knowherepro');?>:
					<input id="<?php echo $this->get_field_id( 'address' ); ?>" name="<?php echo $this->get_field_name( 'address' ); ?>" value="<?php echo $instance['address']; ?>" class="widefat" type="text"/>
				</label>
			</p>

			<p>
				<label><?php esc_html_e('Phone', 'knowherepro');?>:
					<input id="<?php echo $this->get_field_id( 'phone' ); ?>" name="<?php echo $this->get_field_name( 'phone' ); ?>" value="<?php echo $instance['phone']; ?>" class="widefat" type="text"/>
				</label>
			</p>

			<p>
				<label><?php esc_html_e('Fax', 'knowherepro');?>:
					<input id="<?php echo $this->get_field_id( 'fax' ); ?>" name="<?php echo $this->get_field_name( 'fax' ); ?>" value="<?php echo $instance['fax']; ?>" class="widefat" type="text"/>
				</label>
			</p>

			<p>
				<label><?php esc_html_e('E-mail', 'knowherepro');?>:
					<input id="<?php echo $this->get_field_id( 'email' ); ?>" name="<?php echo $this->get_field_name( 'email' ); ?>" value="<?php echo $instance['email']; ?>" class="widefat" type="text"/>
				</label>
			</p>

			<p>
				<label><?php esc_html_e('Facebook', 'knowherepro');?>:
					<input id="<?php echo $this->get_field_id( 'facebook' ); ?>" name="<?php echo $this->get_field_name( 'facebook' ); ?>" value="<?php echo $instance['facebook']; ?>" class="widefat" type="text"/>
				</label>
			</p>

			<p>
				<label><?php esc_html_e('Google Plus', 'knowherepro');?>:
					<input id="<?php echo $this->get_field_id( 'googleplus' ); ?>" name="<?php echo $this->get_field_name( 'googleplus' ); ?>" value="<?php echo $instance['googleplus']; ?>" class="widefat" type="text"/>
				</label>
			</p>

			<p>
				<label><?php esc_html_e('Twitter', 'knowherepro');?>:
					<input id="<?php echo $this->get_field_id( 'twitter' ); ?>" name="<?php echo $this->get_field_name( 'twitter' ); ?>" value="<?php echo $instance['twitter']; ?>" class="widefat" type="text"/>
				</label>
			</p>

			<p>
				<label><?php esc_html_e('LinkedIn', 'knowherepro');?>:
					<input id="<?php echo $this->get_field_id( 'linkedin' ); ?>" name="<?php echo $this->get_field_name( 'linkedin' ); ?>" value="<?php echo $instance['linkedin']; ?>" class="widefat" type="text"/>
				</label>
			</p>

		<?php
		}

	}
}


/*	Widget Advertising Area
/* ----------------------------------------------------------------- */

if (!class_exists('knowhere_widget_advertising_area')) {

	class knowhere_widget_advertising_area extends Knowhere_Widget {

		function __construct() {
			$this->widget_cssclass    = 'widget_advertising_area';
			$this->widget_description = esc_html__('An advertising widget that displays image', 'knowherepro');
			$this->widget_id          = 'widget-advertising-area';
			$this->widget_name        = esc_html__('KnowherePro Advertising Area', 'knowherepro');
			$this->settings           = array(
				'title'  => array(
					'type'  => 'text',
					'std'   => '',
					'label' => esc_html__( 'Title', 'knowherepro' )
				),
				'image_url'  => array(
					'type'  => 'text',
					'std'   => '',
					'label' => esc_html__( 'Image URL', 'knowherepro' )
				),
				'ref_url'  => array(
					'type'  => 'text',
					'std'   => '#',
					'label' => esc_html__( 'Referal URL', 'knowherepro' )
				),
			);

			parent::__construct();
		}

		function widget($args, $instance) {
			$title = isset( $instance['title'] ) ? $instance['title'] : $this->settings['title']['std'];
			$image_url = isset( $instance['image_url'] ) ? $instance['image_url'] : $this->settings['image_url']['std'];
			$ref_url = isset( $instance['ref_url'] ) ? $instance['ref_url'] : $this->settings['ref_url']['std'];

			if ( empty($image_url) ) {
				$image_url = '<span>'.esc_html__('Advertise here', 'knowherepro').'</span>';
			} else {
				$image_url = '<img class="advertise-image" src="' . esc_url($image_url) . '" title="" alt=""/>';
			}

			ob_start(); ?>

			<?php $this->widget_start( $args, $instance ); ?>
				<a target="_blank" href="<?php echo esc_url($ref_url); ?>"><?php echo sprintf('%s', $image_url); ?></a>
			<?php $this->widget_end($args);

			echo ob_get_clean();
		}

	}
}

/*	Widget Instagram
/* ----------------------------------------------------------------- */

if (!class_exists('knowhere_instagram_widget')) {

	class knowhere_instagram_widget extends Knowhere_Widget {

		function __construct() {
			$this->widget_cssclass    = 'knowhere_instagram-feed';
			$this->widget_description = esc_html__( 'Displays your latest Instagram photos', 'knowherepro' );
			$this->widget_id          = 'knowhere_instagram-feed';
			$this->widget_name        = esc_html__('KnowherePro Instagram', 'knowherepro');
			$this->settings = array(
				'title'  => array(
					'type'  => 'text',
					'std'   => esc_html__( 'Instagram', 'knowherepro' ),
					'label' => esc_html__( 'Title', 'knowherepro' )
				),
				'username'  => array(
					'type'  => 'text',
					'std'   => '',
					'label' => esc_html__( 'Username', 'knowherepro' )
				),
				'number'  => array(
					'type'  => 'text',
					'std'   => 9,
					'label' => esc_html__( 'Number of photos', 'knowherepro' )
				),
				'target' => array(
					'type'  => 'select',
					'std'   => '_self',
					'label' => esc_html__( 'Open links in', 'knowherepro' ),
					'options' => array(
						'_self' => esc_html__('Current window (_self)', 'knowherepro'),
						'_blank' => esc_html__('New window (_blank)', 'knowherepro')
					)
				),
				'link'  => array(
					'type'  => 'text',
					'std'   => esc_html__( 'Follow Me!', 'knowherepro' ),
					'label' => esc_html__( 'Link text', 'knowherepro' )
				)
			);

			parent::__construct();
		}

		function widget( $args, $instance ) {

			$username = empty( $instance['username'] ) ? '' : $instance['username'];
			$limit = empty( $instance['number'] ) ? $this->settings['number']['std'] : $instance['number'];
			$target = empty( $instance['target'] ) ? $this->settings['target']['std'] : $instance['target'];
			$link = empty( $instance['link'] ) ? '' : $instance['link'];

			$this->widget_start( $args, $instance );

			if ( $username != '' ) {

				$media_array = $this->scrape_instagram( $username, $limit );

				if ( is_wp_error( $media_array ) ) {

					echo wp_kses_post( $media_array->get_error_message() );

				} else {

					// filter for images only?
					if ( $images_only = apply_filters( 'knowhere_wpiw_images_only', FALSE ) )
						$media_array = array_filter( $media_array, array( $this, 'images_only' ) );

					// filters for custom classes
					$ulclass = apply_filters( 'knowhere_wpiw_list_class', 'kw-instafeed' );
					$liclass = apply_filters( 'knowhere_wpiw_item_class', 'kw-instafeed-item' );
					$aclass = apply_filters( 'knowhere_wpiw_a_class', 'kw-lightbox' );
					$imgclass = apply_filters( 'knowhere_wpiw_img_class', '' );

					?><div class="<?php echo esc_attr( $ulclass ); ?>"><?php
					foreach ( $media_array as $item ) {
						echo '<div class="'. esc_attr( $liclass ) .'"><a href="'. esc_url( $item['link'] ) .'" target="'. esc_attr( $target ) .'"  class="'. esc_attr( $aclass ) .'"><img src="'. esc_url( $item['original'] ) .'"  alt="'. esc_attr( $item['description'] ) .'" title="'. esc_attr( $item['description'] ).'"  class="'. esc_attr( $imgclass ) .'"/></a></div>';
					}
					?></div><?php
				}
			}

			if ( $link != '' ) {
				?><p class="clear"><a href="//instagram.com/<?php echo esc_attr( trim( $username ) ); ?>" rel="me" target="<?php echo esc_attr( $target ); ?>"><?php echo wp_kses_post( $link ); ?></a></p><?php
			}

			$this->widget_end($args);
		}

		function scrape_instagram( $username, $slice = 9 ) {

			$username = strtolower( $username );
			$username = str_replace( '@', '', $username );

			if ( false === ( $instagram = get_transient( 'instagram-a3-'.sanitize_title_with_dashes( $username ) ) ) ) {

				$remote = wp_remote_get( 'http://instagram.com/'.trim( $username ) );

				if ( is_wp_error( $remote ) )
					return new WP_Error( 'site_down', esc_html__( 'Unable to communicate with Instagram.', 'knowherepro' ) );

				if ( 200 != wp_remote_retrieve_response_code( $remote ) )
					return new WP_Error( 'invalid_response', esc_html__( 'Instagram did not return a 200.', 'knowherepro' ) );

				$shards = explode( 'window._sharedData = ', $remote['body'] );
				$insta_json = explode( ';</script>', $shards[1] );
				$insta_array = json_decode( $insta_json[0], TRUE );

				if ( ! $insta_array )
					return new WP_Error( 'bad_json', esc_html__( 'Instagram has returned invalid data.', 'knowherepro' ) );

				if ( isset( $insta_array['entry_data']['ProfilePage'][0]['user']['media']['nodes'] ) ) {
					$images = $insta_array['entry_data']['ProfilePage'][0]['user']['media']['nodes'];
				} else {
					return new WP_Error( 'bad_json_2', esc_html__( 'Instagram has returned invalid data.', 'knowherepro' ) );
				}

				if ( ! is_array( $images ) )
					return new WP_Error( 'bad_array', esc_html__( 'Instagram has returned invalid data.', 'knowherepro' ) );

				$instagram = array();

				foreach ( $images as $image ) {

					$image['thumbnail_src'] = preg_replace( '/^https?\:/i', '', $image['thumbnail_src'] );
					$image['display_src'] = preg_replace( '/^https?\:/i', '', $image['display_src'] );

					// handle both types of CDN url
					if ( (strpos( $image['thumbnail_src'], 's640x640' ) !== false ) ) {
						$image['thumbnail'] = str_replace( 's640x640', 's160x160', $image['thumbnail_src'] );
						$image['small'] = str_replace( 's640x640', 's320x320', $image['thumbnail_src'] );
					} else {
						$urlparts = wp_parse_url( $image['thumbnail_src'] );
						$pathparts = explode( '/', $urlparts['path'] );
						array_splice( $pathparts, 3, 0, array( 's160x160' ) );
						$image['thumbnail'] = '//' . $urlparts['host'] . implode('/', $pathparts);
						$pathparts[3] = 's320x320';
						$image['small'] = '//' . $urlparts['host'] . implode('/', $pathparts);
					}

					$image['large'] = $image['thumbnail_src'];

					if ( $image['is_video'] == true ) {
						$type = 'video';
					} else {
						$type = 'image';
					}

					$caption = esc_html__( 'Instagram Image', 'knowherepro' );
					if ( ! empty( $image['caption'] ) ) {
						$caption = $image['caption'];
					}

					$instagram[] = array(
						'description'   => $caption,
						'link'		  	=> '//instagram.com/p/' . $image['code'],
						'time'		  	=> $image['date'],
						'comments'	  	=> $image['comments']['count'],
						'likes'		 	=> $image['likes']['count'],
						'thumbnail'	 	=> $image['thumbnail'],
						'small'			=> $image['small'],
						'large'			=> $image['large'],
						'original'		=> $image['display_src'],
						'type'		  	=> $type
					);
				}

				// do not set an empty transient - should help catch private or empty accounts
				if ( ! empty( $instagram ) ) {
					$instagram = serialize( $instagram );
					set_transient( 'instagram-a3-'.sanitize_title_with_dashes( $username ), $instagram, apply_filters( 'null_instagram_cache_time', HOUR_IN_SECONDS*2 ) );
				}
			}

			if ( ! empty( $instagram ) ) {

				$instagram = unserialize( $instagram );
				return array_slice( $instagram, 0, $slice );

			} else {

				return new WP_Error( 'no_images', esc_html__( 'Instagram did not return any images.', 'knowherepro' ) );

			}
		}

		function images_only( $media_item ) {
			if ( $media_item['type'] == 'image' )
				return true;

			return false;
		}
	}

}

add_action('widgets_init', create_function('', 'return register_widget("knowhere_widget_popular_widget");'));
add_action('widgets_init', create_function('', 'return register_widget("knowhere_widget_social_links");'));
add_action('widgets_init', create_function('', 'return register_widget("knowhere_widget_contact_us");'));
add_action('widgets_init', create_function('', 'return register_widget("knowhere_instagram_widget");'));
add_action('widgets_init', create_function('', 'return register_widget("knowhere_like_box_facebook");'));
add_action('widgets_init', create_function('', 'return register_widget("knowhere_widget_advertising_area");'));

if ( !function_exists('knowhere_dummy_widget')) {

	function knowhere_dummy_widget($number) {

		if  ( !$number ) return;

		switch($number) {
			case 1:
				$title = apply_filters('widget_title', esc_html__('Archive','knowherepro') );

				echo "<div class='widget widget_archive'>";
				echo "<h3 class='kw-widget-title'>" . $title . "</h3>";
				echo "<ul>";
				wp_get_archives('type=monthly');
				echo "</ul></div>";
				break;

			case 2:
				$title = apply_filters('widget_title', esc_html__('Categories','knowherepro') );

				echo "<div class='widget widget_categories'>";
				echo "<h3 class='kw-widget-title'>" . $title . "</h3>";
				echo "<ul>";
				wp_list_categories('sort_column=name&optioncount=0&hierarchical=0&title_li=');
				echo "</ul></div>";
				break;

			case 3:
				$title = apply_filters('widget_title', esc_html__('Pages','knowherepro') );

				echo "<div class='widget widget_pages'>";
				echo "<h3 class='kw-widget-title'>" . $title . "</h3>";
				echo "<ul>";
				wp_list_pages('title_li=&depth=-1' );
				echo "</ul></div>";
				break;
			case 4:
				$title = apply_filters('widget_title', esc_html__('Recent Posts','knowherepro') );

				echo "<div class='widget widget_meta'>";
				echo "<h3 class='kw-widget-title'>" . $title. "</h3>";
				echo "<ul>"; ?>

					<?php

					ob_start();

					$r = new WP_Query( apply_filters( 'widget_posts_args', array(
						'posts_per_page'      => 10,
						'no_found_rows'       => true,
						'post_status'         => 'publish',
						'ignore_sticky_posts' => true
					) ) );

					if ( $r->have_posts() ) : ?>

					<ul>
						<?php while ( $r->have_posts() ) : $r->the_post(); ?>
							<li>
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</li>
						<?php endwhile; ?>
					</ul>

					<?php wp_reset_postdata(); endif;

					echo ob_get_clean();

					echo "</ul></div>";
				break;
		}
	}

}


