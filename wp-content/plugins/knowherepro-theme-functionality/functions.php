<?php

if( ! function_exists ( 'knowhere_admin_taxonomy_terms' ) ) {
	function knowhere_admin_taxonomy_terms( $post_id, $taxonomy, $post_type ) {

		$terms = get_the_terms( $post_id, $taxonomy );

		if ( ! empty ( $terms ) ) {
			$out = array();
			foreach ( $terms as $term ) {
				$out[] = sprintf( '<a href="%s">%s</a>',
					esc_url( add_query_arg( array( 'post_type' => $post_type, $taxonomy => $term->slug ), 'edit.php' ) ),
					esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, $taxonomy, 'display' ) )
				);
			}
			return join( ', ', $out );
		}

		return false;
	}
}


if ( !function_exists('knowhere_job_single_share') ) {
	function knowhere_job_single_share() {
		$post_id = get_the_ID();
		$image = esc_url(wp_get_attachment_url( get_post_thumbnail_id( $post_id ) ));
		$permalink = esc_url( apply_filters('the_permalink', get_the_permalink( $post_id )) );
		$title = esc_attr(get_the_title( $post_id ));
		$extra_attr = 'target="_blank"';

		global $knowhere_settings;

		if ( !$knowhere_settings['job-single-share'] ) return;
		?>
		<div class="kw-entry-share">

			<ul class="kw-social-links kw-type-3">

				<?php if ( $knowhere_settings['job-share-facebook'] ): ?>
					<li><a target="_blank" href="http://www.facebook.com/sharer.php?m2w&amp;s=100&amp;p&#091;url&#093;=<?php echo $permalink ?>&amp;p&#091;images&#093;&#091;0&#093;=<?php echo $image ?>&amp;p&#091;title&#093;=<?php echo $title ?>" <?php echo $extra_attr ?> class="kw-facebook"><i class="fa fa-facebook"></i><?php esc_html_e('Facebook', 'knowherepro_app_textdomain') ?></a></li>
				<?php endif; ?>

				<?php if ($knowhere_settings['job-share-twitter']): ?>
					<li><a target="_blank" href="https://twitter.com/intent/tweet?text=<?php echo $title ?>&amp;url=<?php echo $permalink ?>" <?php echo $extra_attr ?> class="kw-twitter" title="<?php echo esc_html__('Twitter', 'knowherepro_app_textdomain') ?>"><i class="fa fa-twitter"></i><?php esc_html_e('Twitter', 'knowherepro_app_textdomain') ?></a></li>
				<?php endif; ?>

				<?php if ($knowhere_settings['job-share-googleplus']): ?>
					<li><a target="_blank" href="https://plus.google.com/share?url=<?php echo $permalink ?>" <?php echo $extra_attr ?> class="kw-google" title="<?php echo esc_html__('Google +', 'knowherepro_app_textdomain') ?>"><i class="fa fa-google-plus"></i><?php esc_html_e('Google Plus', 'knowherepro_app_textdomain') ?></a></li>
				<?php endif; ?>

				<?php if ($knowhere_settings['job-share-pinterest']): ?>
					<li><a href="https://pinterest.com/pin/create/button/?url=<?php echo $permalink ?>&amp;media=<?php echo $image ?>" <?php echo $extra_attr ?> title="<?php echo esc_html__('Pinterest', 'knowherepro_app_textdomain') ?>" class="kw-pinterest"><i class="fa fa-pinterest-p"></i><?php echo esc_html__('Pinterest', 'knowherepro_app_textdomain') ?></a></li>
				<?php endif; ?>

				<?php if ($knowhere_settings['job-share-email']): ?>
					<li><a href="mailto:?subject=<?php echo $title ?>&amp;body=<?php echo $permalink ?>" <?php echo $extra_attr ?> title="<?php echo esc_html__('Email to a Friend', 'knowherepro_app_textdomain') ?>" class="kw-email"><i class="fa fa-envelope"></i><?php echo esc_html__('Email to a Friend', 'knowherepro_app_textdomain') ?></a></li>
				<?php endif; ?>

			</ul>

		</div><!--/ .kw-entry-share -->
		<?php
	}
}

if ( !function_exists('knowhere_product_share') ) {

	function knowhere_product_share($post_id) {
		$image = esc_url(wp_get_attachment_url( get_post_thumbnail_id( $post_id ) ));
		$permalink = esc_url( apply_filters('the_permalink', get_the_permalink( $post_id )) );
		$title = esc_attr(get_the_title( $post_id ));
		$extra_attr = 'target="_blank"';

		global $knowhere_settings;

		if ( !$knowhere_settings['product-single-share'] ) return;
		?>

		<div class="kw-entry-share">

			<ul class="kw-social-links kw-type-3">

				<?php if ($knowhere_settings['product-share-facebook']): ?>
					<li><a class="kw-facebook" href="http://www.facebook.com/sharer.php?m2w&amp;s=100&amp;p&#091;url&#093;=<?php echo $permalink ?>&amp;p&#091;images&#093;&#091;0&#093;=<?php echo $image ?>&amp;p&#091;title&#093;=<?php echo $title ?>" <?php echo $extra_attr ?>><i class="fa fa-facebook"></i><?php echo esc_html__('Facebook', 'knowherepro_app_textdomain') ?></a></li>
				<?php endif; ?>

				<?php if ($knowhere_settings['product-share-twitter']): ?>
					<li><a class="kw-twitter" href="https://twitter.com/intent/tweet?text=<?php echo $title ?>&amp;url=<?php echo $permalink ?>" <?php echo $extra_attr ?>><i class="fa fa-twitter"></i><?php echo esc_html__('Twitter', 'knowherepro_app_textdomain') ?></a></li>
				<?php endif; ?>

				<?php if ($knowhere_settings['product-share-googleplus']): ?>
					<li><a class="kw-google"" href="https://plus.google.com/share?url=<?php echo $permalink ?>" <?php echo $extra_attr ?>><i class="fa fa-google-plus"></i><?php echo esc_html__('Google Plus', 'knowherepro_app_textdomain') ?></a></li>
				<?php endif; ?>

				<?php if ($knowhere_settings['product-share-pinterest']) : ?>
					<li><a class="kw-pinterest" href="https://pinterest.com/pin/create/button/?url=<?php echo $permalink ?>&amp;media=<?php echo $image ?>" <?php echo $extra_attr ?>><i class="fa fa-pinterest-p"></i><?php echo esc_html__('Pinterest', 'knowherepro_app_textdomain') ?></a></li>
				<?php endif; ?>

				<?php if ($knowhere_settings['product-share-email']) : ?>
					<li><a href="mailto:?subject=<?php echo $title ?>&amp;body=<?php echo $permalink ?>" <?php echo $extra_attr ?>><i class="fa fa-envelope"></i><?php echo esc_html__('Email', 'knowherepro_app_textdomain') ?></a></li>
				<?php endif; ?>

			</ul>

		</div>
		<?php
	}

	add_action( 'woocommerce_share', 'knowhere_product_share');

}

if ( !function_exists('knowhere_content_share') ) {

	function knowhere_content_share() {

		global $knowhere_settings;
		$image = esc_url(wp_get_attachment_url( get_post_thumbnail_id() ));
		$permalink = esc_url( apply_filters('the_permalink', get_the_permalink()) );
		$title = esc_attr(get_the_title());
		$extra_attr = 'target="_blank"';
		?>

		<div class="kw-entry-share">

			<ul class="kw-social-links kw-type-3">

				<?php if ( $knowhere_settings['post-share-facebook'] ): ?>
					<li><a class="kw-facebook" href="http://www.facebook.com/sharer.php?m2w&amp;s=100&amp;p&#091;url&#093;=<?php echo $permalink ?>&amp;p&#091;images&#093;&#091;0&#093;=<?php echo $image ?>&amp;p&#091;title&#093;=<?php echo $title ?>" <?php echo $extra_attr ?>><i class="fa fa-facebook"></i><?php echo esc_html__('Facebook', 'knowherepro_app_textdomain') ?></a></li>
				<?php endif; ?>

				<?php if ( $knowhere_settings['post-share-twitter'] ): ?>
					<li><a class="kw-twitter" href="https://twitter.com/intent/tweet?text=<?php echo $title ?>&amp;url=<?php echo $permalink ?>" <?php echo $extra_attr ?>><i class="fa fa-twitter"></i><?php echo esc_html__('Twitter', 'knowherepro_app_textdomain') ?></a></li>
				<?php endif; ?>

				<?php if ( $knowhere_settings['post-share-googleplus'] ): ?>
					<li><a class="kw-google"" href="https://plus.google.com/share?url=<?php echo $permalink ?>" <?php echo $extra_attr ?>><i class="fa fa-google-plus"></i><?php echo esc_html__('Google Plus', 'knowherepro_app_textdomain') ?></a></li>
				<?php endif; ?>

				<?php if ( $knowhere_settings['post-share-pinterest'] ) : ?>
					<li><a class="kw-pinterest" href="https://pinterest.com/pin/create/button/?url=<?php echo $permalink ?>&amp;media=<?php echo $image ?>" <?php echo $extra_attr ?>><i class="fa fa-pinterest-p"></i><?php echo esc_html__('Pinterest', 'knowherepro_app_textdomain') ?></a></li>
				<?php endif; ?>

				<?php if ( $knowhere_settings['post-share-email'] ) : ?>
					<li><a href="mailto:?subject=<?php echo $title ?>&amp;body=<?php echo $permalink ?>" <?php echo $extra_attr ?>><i class="fa fa-envelope"></i><?php echo esc_html__('Email', 'knowherepro_app_textdomain') ?></a></li>
				<?php endif; ?>

			</ul>

		</div>

	<?php
	}
}

if (!class_exists('Knowhere_Admin_Helper')) {

	class Knowhere_Admin_Helper {

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
			if ( empty($img_src) ) return;
			if ( empty($dimensions) ) return $img_src;

			$sizes = explode('*', $dimensions);
			$src = aq_resize($img_src, $sizes[0], $sizes[1], $crop);

			if ( !$src ) {
				return $img_src;
			}
			return $src;
		}

		public static function get_the_post_thumbnail ($post_id, $dimensions, $crop = true, $thumbnail_atts = array(), $image_atts = array()) {
			$atts = '';
			$sizes = array_filter(explode("*", $dimensions));
			if (is_array($sizes) && !empty($sizes)) {
				$atts = "width={$sizes[0]} height={$sizes[1]}";
			}
			return '<img '. esc_attr($atts) .' src="' . self::get_post_featured_image($post_id, $dimensions, $crop) . '" ' . self::create_data_string($thumbnail_atts) . ' ' . self::create_atts_string($image_atts) . ' />';
		}

		public static function get_the_thumbnail ($attach_id, $dimensions, $crop = true, $thumbnail_atts = array(), $image_atts = array()) {
//			$atts = '';
//			$sizes = array_filter(explode("*", $dimensions));
//			if (is_array($sizes) && !empty($sizes)) {
//				$atts = "width={$sizes[0]} height={$sizes[1]}";
//			}
			return '<img src="' . self::get_post_attachment_image($attach_id, $dimensions, $crop) . '" ' . self::create_data_string($thumbnail_atts) . ' ' . self::create_atts_string($image_atts) . ' />';
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

	}

}