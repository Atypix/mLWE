<?php

if (!class_exists('Knowhere_Helper')) {

	class Knowhere_Helper {

		/*	Get Registered Sidebars
		/* ---------------------------------------------------------------------- */

		public static function get_registered_sidebars($sidebars = array(), $exclude = array()) {
			global $wp_registered_sidebars;

			foreach ( $wp_registered_sidebars as $sidebar ) {
				if ( !in_array($sidebar['name'], $exclude) ) {
					$sidebars[$sidebar['name']] = $sidebar['name'];
				}
			}
			return $sidebars;
		}

		/*  Main Navigation
		/* ---------------------------------------------------------------------- */

		public static function main_navigation( $menu_class = 'kw-navigation', $theme_location = 'primary' ) {

			if ( is_array($menu_class) ) {
				$menu_class = implode(" ", $menu_class);
			}

			$defaults = array(
				'container' => 'ul',
				'menu_class' => $menu_class,
				'theme_location' => $theme_location,
				'fallback_cb' => false,
				'walker' => new knowhere_primary_navwalker
			);

			if ( has_nav_menu($theme_location) ) {
				wp_nav_menu( $defaults );
			} else {
				echo '<ul class="'. $menu_class .'">';
				wp_list_pages('title_li=');
				echo '</ul>';
			}

		}

		public static function output_widgets_html($view, $data = array()) {
			@extract($data);
			ob_start();
			include( get_template_directory() . '/includes/widgets/templates/' . $view . '.php' );
			return ob_get_clean();
		}

		public static function get_post_attachment_image($attachment_id, $dimensions, $crop = true) {
			$img_src = wp_get_attachment_image_src($attachment_id, $dimensions);
			$img_src = $img_src[0] ? $img_src[0] : '';
			return $img_src;
		}

		public static function get_post_featured_image($post_id, $dimensions, $crop = true) {
			$img_src = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), $dimensions);
			$img_src = $img_src[0] ? $img_src[0] : '';
			return $img_src;
		}

		public static function get_attachment_image($attachment_id, $size, $icon, $attr) {
			return wp_get_attachment_image($attachment_id, $size, $icon, $attr);
		}

		public static function get_attachment_url($attachment_id, $dimensions = '', $crop = true) {

			if ( empty($dimensions) ) return wp_get_attachment_url($attachment_id);

			$sizes = explode('*', $dimensions);
			$img_src = aq_resize(wp_get_attachment_url($attachment_id), $sizes[0], $sizes[1], $crop);

			if ( !$img_src ) {
				return wp_get_attachment_url($attachment_id);
			}

			return $img_src;
		}

		public static function get_image($img_src, $dimensions, $crop = true) {
			if ( empty($dimensions) ) return $img_src;

			$sizes = explode('*', $dimensions);
			$src = aq_resize($img_src, $sizes[0], $sizes[1], $crop);

			if ( !$src ) {
				return $img_src;
			}
			return $src;
		}

		public static function get_the_post_thumbnail ($post_id, $dimensions, $crop = true, $thumbnail_atts = array(), $image_atts = array()) {
//			$atts = '';
//			$sizes = array_filter(explode("*", $dimensions));
//			if ( is_array($sizes) && !empty($sizes) ) {
//				$atts = "width={$sizes[0]} height={$sizes[1]}";
//			}
			return '<img src="' . self::get_post_featured_image($post_id, $dimensions, $crop) . '" ' . self::create_data_string($thumbnail_atts) . ' ' . self::create_atts_string($image_atts) . ' />';
		}

		public static function get_the_thumbnail ($attach_id, $dimensions, $crop = true, $thumbnail_atts = array(), $image_atts = array()) {
			$atts = '';
			$sizes = array_filter(explode("*", $dimensions));
			if (is_array($sizes) && !empty($sizes)) {
				$atts = "width={$sizes[0]} height={$sizes[1]}";
			}
			return '<img '. esc_attr($atts) .' src="' . self::get_post_attachment_image($attach_id, $dimensions, $crop) . '" ' . self::create_data_string($thumbnail_atts) . ' ' . self::create_atts_string($image_atts) . ' />';
		}

		public static function create_data_string($data = array()) {
			$data_string = "";

			if (empty($data)) return;

			foreach ($data as $key => $value) {
				if (is_array($value)) $value = implode(", ", $value);
				$data_string .= " data-$key='$value' ";
			}
			return $data_string;

		}

		public static function create_atts_string($data = array()) {
			$string = "";

			if (empty($data)) return;

			foreach ($data as $key => $value) {

				if (is_array($value)) $value = implode(", ", $value);

				$string .= " $key='{$value}' ";
			}
			return $string;

		}

		public static function create_data_string_animation( $animation, $delay = 0, $scroll_factor = '-50', $return_data_string = true ) {
			$data_string = array();

			if ( !empty($animation) ) {
				$data_string['animation'] = esc_attr($animation);
			}
			if ( 0 != $delay ) {
				$data_string['animation-delay'] = esc_attr($delay);
			}
			if ( !empty($scroll_factor) ) {
				$data_string['scroll-factor'] = esc_attr($scroll_factor);
			}

			if ( $return_data_string ) {
				return self::create_data_string($data_string);
			} else {
				return $data_string;
			}

		}

		public static function which_video_service( $video_url ) {
			$videos = array();
			$videoIdRegex = null;

			if (strpos($video_url, 'youtube.com/watch') !== false || strpos($video_url, 'youtu.be/') !== false) {
				preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $video_url, $matches);
				$video_id = $matches[0];

				if (!empty($video_id)) {
					$videos['ytid'] = trim($video_id);
					$videos['videoattributes'] = "version=3&amp;enablejsapi=1&amp;html5=1&amp;volume=100&amp;hd=1&amp;wmode=opaque&showinfo=0&ref=0;";
				}
			} elseif (strpos($video_url, 'vimeo.com') !== false) {

				if (strpos($video_url, 'player.vimeo.com') !== false) {
					$videoIdRegex = '/player.vimeo.com\/video\/([0-9]+)\??/i';
				} else { $videoIdRegex = '/vimeo.com\/([0-9]+)\??/i'; }

				if ($videoIdRegex !== null) {
					if (preg_match($videoIdRegex, $video_url, $results)) {
						$video_id = $results[1];
					}
					if (!empty($video_id)) {
						$videos['vimeoid'] = trim($video_id);
						$videos['videoattributes'] = "title=0&byline=0&portrait=0&api=1";
					}
				}

			} else {
				if (preg_match("/\.mp4$/", $video_url)) {
					$videos['videomp4'] = trim($video_url);
				} else if (preg_match("/\.ogv$/", $video_url)) {
					$videos['videoogv'] = trim($video_url);
				} else if (preg_match("/\.webm$/", $video_url)) {
					$videos['videowebm'] = trim($video_url);
				}
			}

			return self::create_data_string($videos);
		}

		public static function hex2rgba($color, $opacity = false) {

			$default = 'rgb(0,0,0)';

			//Return default if no color provided
			if(empty($color))
				return $default;

			//Sanitize $color if "#" is provided
			if ($color[0] == '#' ) {
				$color = substr( $color, 1 );
			}

			//Check if color has 6 or 3 characters and get values
			if (strlen($color) == 6) {
				$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
			} elseif ( strlen( $color ) == 3 ) {
				$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
			} else {
				return $default;
			}

			//Convert hexadec to rgb
			$rgb =  array_map('hexdec', $hex);

			//Check if opacity is set(rgba or rgb)
			if($opacity){
				if(abs($opacity) > 1)
					$opacity = 1.0;
				$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
			} else {
				$output = 'rgb('.implode(",",$rgb).')';
			}

			//Return rgb(a) color string
			return $output;
		}

	}

}
