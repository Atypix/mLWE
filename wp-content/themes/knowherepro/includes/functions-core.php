<?php

if ( !function_exists('mad_meta') ) {

	function mad_meta() {
		return '';
	}

}

/*	String Truncate
/* ---------------------------------------------------------------------- */

if ( !function_exists('knowhere_string_truncate')) {
	function knowhere_string_truncate($string, $limit, $break=".", $pad="...", $stripClean = false, $excludetags = '<strong><em><span>', $safe_truncate = false) {
		if ( empty($limit) ) return $string;

		if ( $stripClean ) {
			$string = strip_shortcodes(strip_tags($string, $excludetags));
		}

		if ( strlen($string) <= $limit ) return $string;

		if ( false !== ($breakpoint = strpos($string, $break, $limit)) ) {
			if ($breakpoint < strlen($string) - 1) {
				if ($safe_truncate || is_rtl()) {
					$string = mb_strimwidth($string, 0, $breakpoint) . $pad;
				} else {
					$string = substr($string, 0, $breakpoint) . $pad;
				}
			}
		}

		// if there is no breakpoint an no tags we could accidentaly split split inside a word
		if ( !$breakpoint && strlen(strip_tags($string)) == strlen($string) ) {
			if ( $safe_truncate || is_rtl() ) {
				$string = mb_strimwidth($string, 0, $limit) . $pad;
			} else {
				$string = substr($string, 0, $limit) . $pad;
			}
		}

		return $string;
	}
}

/*	Get Site Icon
/* ---------------------------------------------------------------------- */

if (!function_exists('knowhere_get_site_icon_url')) {

	function knowhere_get_site_icon_url( $size = 512, $url = '' ) {

		global $knowhere_settings;

		$site_icon_id = '';
		$favicon_url = $knowhere_settings['favicon']['url'];
		if ( isset($knowhere_settings['favicon']['id']) ) {
			$site_icon_id = $knowhere_settings['favicon']['id'];
		}

		if ( $site_icon_id ) {
			if ( $size >= 512 ) {
				$size_data = 'full';
			} else {
				$size_data = array( $size, $size );
			}

			$url_data = wp_get_attachment_image_src( $site_icon_id, $size_data );
			if ( $url_data ) {
				$url = $url_data[0];
			}
		} elseif( $favicon_url ) {
			return $favicon_url;
		}

		return $url;
	}
}

/*	Site Icon
/* ---------------------------------------------------------------------- */

if (!function_exists('knowhere_wp_site_icon')) {

	function knowhere_wp_site_icon() {

		if ( !has_site_icon() ) {

			global $knowhere_settings;
			$favicon = $knowhere_settings['favicon'];

			if ( ! $favicon ) { return; }

			$meta_tags = array(
				sprintf( '<link rel="icon" href="%s" sizes="32x32" />', esc_url( knowhere_get_site_icon_url( 32 ) ) ),
				sprintf( '<link rel="icon" href="%s" sizes="192x192" />', esc_url( knowhere_get_site_icon_url( 192 ) ) ),
				sprintf( '<link rel="apple-touch-icon-precomposed" href="%s">', esc_url( knowhere_get_site_icon_url( 180 ) ) ),
				sprintf( '<meta name="msapplication-TileImage" content="%s">', esc_url( knowhere_get_site_icon_url( 270 ) ) ),
			);

			$meta_tags = array_filter( $meta_tags );

			foreach ( $meta_tags as $meta_tag ) {
				echo "$meta_tag\n";
			}

		}

	}
}
add_action( 'wp_head', 'knowhere_wp_site_icon', 99 );

/* 	Regex
/* ---------------------------------------------------------------------- */

if (!function_exists('knowhere_regex')) {

	/*
	*	Regex for url: http://mathiasbynens.be/demo/url-regex
	*/
	function knowhere_regex($string, $pattern = false, $start = "^", $end = "") {
		if (!$pattern) return false;

		if ($pattern == "url") {
			$pattern = "!$start((https?|ftp)://(-\.)?([^\s/?\.#-]+\.?)+(/[^\s]*)?)$end!";
		} else if ($pattern == "link") {
			$pattern = '/(((http|ftp|https):\/{2})+(([0-9a-z_-]+\.)+(aero|asia|biz|cat|com|coop|edu|gov|info|int|jobs|mil|mobi|museum|name|net|org|pro|tel|travel|ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|cu|cv|cx|cy|cz|cz|de|dj|dk|dm|do|dz|ec|ee|eg|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mk|ml|mn|mn|mo|mp|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nu|nz|nom|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|ps|pt|pw|py|qa|re|ra|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw|arpa)(:[0-9]+)?((\/([~0-9a-zA-Z\#\+\%@\.\/_-]+))?(\?[0-9a-zA-Z\+\%@\/&\[\];=_-]+)?)?))\b/imuS';
		} else if ($pattern == "mail") {
			$pattern = "!$start\w[\w|\.|\-]+@\w[\w|\.|\-]+\.[a-zA-Z]{2,4}$end!";
		} else if ($pattern == "image") {
			$pattern = "!$start(https?(?://([^/?#]*))?([^?#]*?\.(?:jpg|gif|png)))$end!";
		} else if ($pattern == "mp4") {
			$pattern = "!$start(https?(?://([^/?#]*))?([^?#]*?\.(?:mp4)))$end!";
		} else if (strpos($pattern,"<") === 0) {
			$pattern = str_replace('<',"",$pattern);
			$pattern = str_replace('>',"",$pattern);

			if (strpos($pattern,"/") !== 0) { $close = "\/>"; $pattern = str_replace('/',"",$pattern); }
			$pattern = trim($pattern);
			if (!isset($close)) $close = "<\/".$pattern.">";

			$pattern = "!$start\<$pattern.+?$close!";
		}

		preg_match($pattern, $string, $result);

		if (empty($result[0])) {
			return false;
		} else {
			return $result;
		}
	}
}

/*	Search Query Filter
/* ---------------------------------------------------------------------- */

if(!function_exists('knowhere_search_query_filter')) {
	function knowhere_search_query_filter($query) {
		if( is_admin() ) return;

		if ( isset($_GET['s']) && empty($_GET['s']) && $query->is_main_query() && empty($query->queried_object) ) {
			foreach( $query as $key => &$query_attr ) {
				if( strpos($key, 'is_') === 0 ) $query_attr = false;
			}

			$query->is_search = true;
			$query->set( 'post_type', 'fake_search_no_results' );
		}

		return $query;

	}
	add_filter( 'pre_get_posts', 'knowhere_search_query_filter' );
}

/*	Tag Archive Page
/* ---------------------------------------------------------------------- */

if (!function_exists('knowhere_tag_archive_page')) {

	function knowhere_tag_archive_page($query) {
		$post_types = get_post_types();
//		global $knowhere_settings;

		if ( is_category() || is_tag() ) {
			if ( !is_admin() && $query->is_main_query() ) {

				$post_type = get_query_var(get_post_type());

				if ($post_type) {
					$post_type = $post_type;
				} else {
					$post_type = $post_types;
				}
				$query->set('post_type', $post_type);
			}
		}

//		if ( $query->is_main_query() ) {
//
//			if ( $query->is_post_type_archive('portfolio') ) {
//				$query->query_vars['posts_per_page'] = $knowhere_settings['portfolio-archive-count'];
//			} elseif ( $query->is_post_type_archive('testimonials') ) {
//				$query->query_vars['posts_per_page'] = $knowhere_settings['testimonials-archive-count'];
//			} elseif ( $query->is_post_type_archive('team-members') ) {
//				$query->query_vars['posts_per_page'] = $knowhere_settings['team-members-archive-count'];
//			}
//
//		}

		return $query;
	}
	add_filter('pre_get_posts', 'knowhere_tag_archive_page');
}

/* 	Filter Hook for Comments
/* --------------------------------------------------------------------- */

if ( !function_exists('knowhere_output_comments')) {

	function knowhere_output_comments($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment; ?>

		<li class="comment" id="comment-<?php echo comment_ID() ?>">

			<div class="comment-body">

				<div class="comment-author vcard">

					<?php echo get_avatar($comment, 100, '', esc_html(get_comment_author())); ?>
					<?php
					$author = '<span class="fn">' . get_comment_author() . '</span>';
					$link = get_comment_author_url();
					if ( !empty($link) ) {
						$author = '<a href="' . esc_url($link) . '">' . $author . '</a>';
					}
					echo sprintf( '%s', $author );
					?>

				</div>

				<div class="comment-meta">

					<?php echo sprintf( '<time>%s</time>', get_comment_date(get_option('date_format')) ); ?>

					<?php
					echo get_comment_reply_link(array_merge(
						array( 'reply_text' => esc_html__('Reply', 'knowherepro') ),
						array( 'depth' => $depth, 'max_depth' => $args['max_depth'] )
					));
					?>

				</div>

				<div class="comment-text"><?php comment_text(); ?></div>

			</div>

		</li>

	<?php
	}
}

/* 	Filter Hooks for Respond
/* ---------------------------------------------------------------------- */

if ( !function_exists('knowhere_comments_form_hook')) {

	function knowhere_comments_form_hook ($defaults) {

		$commenter = wp_get_current_commenter();

		$req      = get_option( 'require_name_email' );
		$aria_req = ( $req ? " aria-required='true'" : '' );
		$html_req = ( $req ? " required='required'" : '' );
		$required_text = sprintf( ' ' . esc_html__('Required fields are marked %s', 'knowherepro'), esc_html__('(required)', 'knowherepro') );

		$defaults['fields']['author'] = '<div class="row"><div class="col-sm-4"><p class="comment-form-author"><label for="author">' . esc_html__( 'Name', 'knowherepro' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label><input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . $html_req . ' /></p></div>';

		$defaults['fields']['email'] = '<div class="col-sm-4"><p class="comment-form-email"><label for="email">' . esc_html__( 'Email', 'knowherepro' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label><input id="email" name="email" type="email" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" aria-describedby="email-notes"' . $aria_req . $html_req  . ' /></p></div>';

		$defaults['fields']['url'] = '<div class="col-sm-4"><p class="comment-form-url"><label for="url">' . esc_html__( 'Website', 'knowherepro' ) . '</label><input id="url" name="url" type="url" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p></div></div>';

		$defaults['comment_notes_before'] = '<p class="comment-notes"><span id="email-notes">' . esc_html__( 'Your email address will not be published.', 'knowherepro' ) . '</span>'. ( $req ? $required_text : '' ) . '</p>';

		$defaults['comment_field'] = '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Comment', 'knowherepro' ) . ' ' . ( $req ? ' <span class="required">*</span>' : '' ) . '</label><textarea id="comment" name="comment" rows="4" aria-describedby="form-allowed-tags" aria-required="true" required="required"></textarea></p>';

//		$defaults['cancel_reply_link'] = ' - ' . esc_html__('Cancel reply', 'knowherepro');

//		$defaults['class_submit'] = '';

		return $defaults;
	}
	add_filter('comment_form_defaults', 'knowhere_comments_form_hook');
}

if ( !function_exists('knowhere_comments_form_fields') ) {

	function knowhere_comments_form_fields($comment_fields) {
		$a = $comment_fields;
		$a = array_reverse($a);
		$b = array_pop($a);
		$a = array_reverse($a);
		$a['comment'] = $b;

		return $a;
	}

	add_filter('comment_form_fields', 'knowhere_comments_form_fields');

}

/*	Array to data string
/* ---------------------------------------------------------------------- */

if ( !function_exists('knowhere_create_data_string') ) {
	function knowhere_create_data_string($data = array()) {
		$data_string = "";

		if ( empty($data) ) return;

		foreach ( $data as $key => $value ) {
			if ( is_array($value) ) $value = implode(", ", $value);
			$data_string .= " data-$key='$value' ";
		}
		return $data_string;
	}
}

/*	Inline CSS
/* ---------------------------------------------------------------------- */

if (!function_exists('knowhere_inline_css')) {

	function knowhere_inline_css() {
		$post_id = knowhere_post_id();

		$content_padding = array();
		$body_bg_color = get_post_meta( $post_id, 'knowhere_body_bg_color', true );
		$image = mad_meta( 'knowhere_bg_image', '', $post_id );
		$footer_bg_color = get_post_meta( $post_id, 'knowhere_footer_bg_color', true );

		if ( !empty($image) && $image > 0 ) {

			$image = array_shift($image);

			if ( isset($image['ID']) ) {
				$image = wp_get_attachment_image_src($image['ID'], '');

				if ( is_array($image) && isset($image[0]) ) {
					$image = $image[0];
				}

			}

		}

		$image_repeat     = get_post_meta( $post_id, 'knowhere_bg_image_repeat', true );
		$image_position   = get_post_meta( $post_id, 'knowhere_bg_image_position', true );
		$image_attachment = get_post_meta( $post_id, 'knowhere_bg_image_attachment', true );

		$page_content_padding = array();

		if ( $post_id )
			$content_padding = get_post_meta( $post_id, 'knowhere_page_content_padding', true );

		if ( $content_padding && is_array($content_padding) ) {
			$page_content_padding = array_filter(get_post_meta( $post_id, 'knowhere_page_content_padding', true ));
		}

		$css = $body_css = $footer_css = $inline_css = array();

		if ( !empty( $body_bg_color ) ) { $body_css[] = "background-color: $body_bg_color;"; }
		if ( !empty( $footer_bg_color ) ) { $footer_css[] = "background-color: $footer_bg_color;"; }

		if ( get_post_meta( $post_id, 'knowhere_footer_hidden_bg_image', true ) ) {
			$footer_css[] = "background-image: none !important;";
		}

		if ( !empty( $image ) && $image != 'none') { $body_css[] = "background-image: url('$image');"; }

		if ( !empty( $page_content_padding ) && is_array($page_content_padding) ) {
			if ( isset($page_content_padding[0]) && !empty($page_content_padding[0]) ) {
				$padding_top = absint($page_content_padding[0]);
				$inline_css[] = "padding-top: $padding_top;";
			}

			if ( isset($page_content_padding[1]) && !empty($page_content_padding[1]) ) {
				$padding_bottom = absint($page_content_padding[1]);
				$inline_css[] = "padding-bottom: $padding_bottom;";
			}
		}

		if ( !empty( $image ) && !empty( $image_attachment ) ) { $body_css[] = "background-attachment: $image_attachment;"; }
		if ( !empty( $image ) && !empty( $image_position ) )   { $body_css[] = "background-position: $image_position;"; }
		if ( !empty( $image ) && !empty( $image_repeat ) )     { $body_css[] = "background-repeat: $image_repeat;"; }

		?>
		<style type="text/css">
			<?php if ( $body_css ): ?>
				body { <?php echo implode( ' ', $body_css ) ?> }
			<?php endif; ?>

			<?php if ( $footer_css ): ?>
				.kw-footer { <?php echo implode( ' ', $footer_css ) ?> }
			<?php endif; ?>

			<?php if ( $inline_css ): ?>
				.kw-page-content { <?php echo implode( ' ', $inline_css ) ?>}
			<?php endif; ?>
		</style>

		<?php
	}

	add_filter('wp_head', 'knowhere_inline_css');
}

/*	Title
/* ---------------------------------------------------------------------- */

if ( !function_exists('knowhere_title') ) {

	function knowhere_title( $args = false, $id = false ) {

		if ( empty($id) ) $id = knowhere_post_id();

		$defaults = array(
			'title' 	  => get_the_title($id),
			'subtitle'    => "",
			'output_html' => "<{heading} {attributes} class='kw-page-title {class}'>{title}</{heading}>{additions}",
			'attributes'  => '',
			'class'		  => '',
			'heading'	  => 'h1',
			'additions'	  => ""
		);

		$args = wp_parse_args($args, $defaults);
		extract($args, EXTR_SKIP);

		if ( !empty($subtitle) ) {
			$class .= ' kw-with-subtitle';
			$additions .= "<div class='kw-title-meta'>" . do_shortcode(wpautop($subtitle)) . "</div>";
		}

		$output_html = str_replace('{class}', $class, $output_html);
		$output_html = str_replace('{attributes}', $attributes, $output_html);
		$output_html = str_replace('{heading}', $heading, $output_html);
		$output_html = str_replace('{title}', $title, $output_html);
		$output_html = str_replace('{additions}', $additions, $output_html);
		return $output_html;
	}
}

/*	Which Archive
/* ---------------------------------------------------------------------- */

if (!function_exists('knowhere_which_archive')) {

	function knowhere_which_archive() {

		ob_start(); ?>

		<?php if ( is_category() ): ?>

			<?php echo esc_html__('Archive for Category:', 'knowherepro') . " " . single_cat_title('', false); ?>

		<?php elseif ( is_day() ): ?>

			<?php echo esc_html__('Daily Archives:', 'knowherepro') . " " . get_the_time( __('F jS, Y', 'knowherepro')); ?>

		<?php elseif ( is_month() ): ?>

			<?php echo esc_html__('Monthly Archives:', 'knowherepro') . " " . get_the_time( __('F, Y', 'knowherepro')); ?>

		<?php elseif ( is_year() ): ?>

			<?php echo esc_html__('Yearly Archives:', 'knowherepro') . " " . get_the_time( __('Y', 'knowherepro')); ?>

		<?php elseif ( is_search() ): global $wp_query; ?>

			<?php if ( !empty($wp_query->found_posts) ): ?>

				<?php if ( $wp_query->found_posts > 1 ): ?>

					<?php echo esc_html__('Search results for:', 'knowherepro')." " . esc_attr(get_search_query()) . " (". $wp_query->found_posts .")"; ?>

				<?php else: ?>

					<?php echo esc_html__('Search result for:', 'knowherepro')." " . esc_attr(get_search_query()) . " (". $wp_query->found_posts .")"; ?>

				<?php endif; ?>

			<?php else: ?>

				<?php if ( !empty($_GET['s']) ): ?>

					<?php echo esc_html__('Search results for:', 'knowherepro') . " " . esc_attr(get_search_query()); ?>

				<?php else: ?>

					<?php echo esc_html__('To search the site please enter a valid term', 'knowherepro'); ?>

				<?php endif; ?>

			<?php endif; ?>

		<?php elseif ( is_author() ): ?>

			<?php $auth = ( get_query_var('author_name') ) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author')); ?>

			<?php if ( isset($auth->nickname) && isset($auth->ID) ): ?>

				<?php $name = $auth->nickname; ?>

				<?php echo esc_html__('Author Archive', 'knowherepro'); ?>
				<?php echo esc_html__('for:', 'knowherepro') . " " . $name; ?>

			<?php endif; ?>

		<?php elseif ( is_tag() ): ?>

			<?php echo esc_html__('Posts tagged &ldquo;', 'knowherepro') . " " . single_tag_title('', false) . '&rdquo;'; ?>

			<?php
			$term_description = term_description();
			if ( ! empty( $term_description ) ) {
				printf( '<div class="taxonomy-description">%s</div>', $term_description );
			}
			?>

		<?php elseif ( is_tax() ): ?>

			<?php $term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy')); ?>

			<?php if ( knowhere_is_product_tag() ): ?>
				<?php echo esc_html__('Products by:', 'knowherepro') . ' "' . $term->name . '" tag'; ?>
			<?php elseif( knowhere_is_product_category() ): ?>
				<?php echo esc_html__('Archive for category:', 'knowherepro') . " " . single_cat_title('', false); ?>
			<?php else: ?>
				<?php echo esc_html__('Archive for:', 'knowherepro') . " " . $term->name; ?>
			<?php endif; ?>

		<?php else: ?>

			<?php if ( is_post_type_archive() ): ?>
				<?php echo sprintf(__('Archive %s', 'knowherepro'), get_query_var('post_type')); ?>
			<?php else: ?>
				<?php echo esc_html__('Archive', 'knowherepro'); ?>
			<?php endif; ?>

		<?php endif; ?>

		<?php return ob_get_clean();
	}
}

if ( !function_exists('knowhere_breadcrumbs') ) {

	function knowhere_breadcrumbs( $args = array() ) {
		global $wp_query, $wp_rewrite;

		$trail = array();
		$path = '';
		$breadcrumb = '';

		$defaults = array(
			'after' => false,
			'separator' => '&raquo;',
			'front_page' => true,
			'show_home' => esc_html__( 'Home', 'knowherepro' ),
			'show_posts_page' => true,
			'truncate' => 80
		);

		if (is_singular()) {
			$defaults["singular_{$wp_query->post->post_type}_taxonomy"] = false;
		}
		extract( wp_parse_args( $args, $defaults ) );

		if (!is_front_page() && $show_home) {
			$trail[] = '<a href="' . esc_url(home_url('/')) . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . $show_home . '</a>';
		}

		if (is_front_page()) {
			if (!$front_page) {
				$trail = false;
			} elseif ($show_home) {
				$trail['end'] = "{$show_home}";
			}
		} elseif (is_home()) {
			$home_page = get_page( $wp_query->get_queried_object_id() );
			$trail = array_merge( $trail, knowhere_breadcrumbs_get_parents( $home_page->post_parent, '' ) );
			$trail['end'] = get_the_title( $home_page->ID );
		} elseif (is_singular()) {
			$post = $wp_query->get_queried_object();
			$post_id = absint( $wp_query->get_queried_object_id() );
			$post_type = $post->post_type;
			$parent = $post->post_parent;

			if ('page' !== $post_type && 'post' !== $post_type) {
				$post_type_object = get_post_type_object( $post_type );

				if (!empty( $post_type_object->rewrite['slug'] ) ) {
					$path .= $post_type_object->rewrite['slug'];
				}
				if (!empty($path)) {
					$trail = array_merge( $trail, knowhere_breadcrumbs_get_parents( '', $path ) );
				}
				if (!empty( $post_type_object->has_archive) && function_exists( 'get_post_type_archive_link' ) ) {
					$trail[] = '<a href="' . esc_url( get_post_type_archive_link( $post_type ) ) . '" title="' . esc_attr( $post_type_object->labels->name ) . '">' . $post_type_object->labels->name . '</a>';
				}
			}

			if (empty($path) && 0 !== $parent || 'attachment' == $post_type) {
				$trail = array_merge($trail, knowhere_breadcrumbs_get_parents($parent, ''));
			}

			if ( 'post' == $post_type && $show_posts_page == true && 'page' == get_option('show_on_front')) {
				$posts_page = get_option('page_for_posts');
				if ($posts_page != '' && is_numeric($posts_page)) {
					$trail = array_merge( $trail, knowhere_breadcrumbs_get_parents($posts_page, '' ));
				}
			}

			if ('post' == $post_type) {
				$category = get_the_category();

				foreach ($category as $cat)  {
					if (!empty($cat->parent))  {
						$parents = get_category_parents($cat->cat_ID, TRUE, '$$$', FALSE);
						$parents = explode("$$$", $parents);
						foreach ($parents as $parent_item) {
							if ($parent_item) $trail[] = $parent_item;
						}
						break;
					}
				}

				if (isset($category[0]) && empty($parents)) {
					$trail[] = '<a href="'. esc_url(get_category_link($category[0]->term_id )) .'">'.$category[0]->cat_name.'</a>';
				}

			}

			if (isset( $args["singular_{$post_type}_taxonomy"]) && $terms = get_the_term_list( $post_id, $args["singular_{$post_type}_taxonomy"], '', ', ', '' ) ) {
				$trail[] = $terms;
			}

			$post_title = get_the_title($post_id);

			if (!empty($post_title)) {
				$trail['end'] = $post_title;
			}

		} elseif (is_archive()) {

			if (is_tax() || is_category() || is_tag()) {
				$term = $wp_query->get_queried_object();
				$taxonomy = get_taxonomy( $term->taxonomy );

				if ( is_category() ) {
					$path = get_option( 'category_base' );
				} elseif ( is_tag() ) {
					$path = get_option( 'tag_base' );
				} else {
					if ($taxonomy->rewrite['with_front'] && $wp_rewrite->front) {
						$path = trailingslashit($wp_rewrite->front);
					}
					$path .= $taxonomy->rewrite['slug'];
				}

				if ($path) {
					$trail = array_merge($trail, knowhere_breadcrumbs_get_parents( '', $path ));
				}

				if (is_taxonomy_hierarchical($term->taxonomy) && $term->parent) {
					$trail = array_merge($trail, knowhere_get_term_parents( $term->parent, $term->taxonomy ) );
				}

				$trail['end'] = $term->name;

			} elseif (function_exists( 'is_post_type_archive' ) && is_post_type_archive()) {

				$post_type_object = get_post_type_object(get_query_var('post_type'));

				if (!empty($post_type_object->rewrite['archive'])) {
					$path .= $post_type_object->rewrite['archive'];
				}

				if (!empty($path)) {
					$trail = array_merge( $trail, knowhere_breadcrumbs_get_parents( '', $path ));
				}

				$trail['end'] = $post_type_object->labels->name;

			} elseif (is_author()) {
				if (!empty($wp_rewrite->front)) {
					$path .= trailingslashit($wp_rewrite->front);
				}
				if (!empty($wp_rewrite->author_base)) {
					$path .= $wp_rewrite->author_base;
				}
				if (!empty($path)) {
					$trail = array_merge( $trail, knowhere_breadcrumbs_get_parents( '', $path ));
				}
				$trail['end'] =  apply_filters('knowhere_author_name', get_the_author_meta('display_name', get_query_var('author')), get_query_var('author'));
			} elseif ( is_time()) {
				if (get_query_var( 'minute' ) && get_query_var('hour')) {
					$trail['end'] = get_the_time( esc_html__('g:i a', 'knowherepro' ));
				} elseif ( get_query_var( 'minute' ) ) {
					$trail['end'] = sprintf( esc_html__('Minute %1$s', 'knowherepro' ), get_the_time( esc_html__( 'i', 'knowherepro' ) ) );
				} elseif ( get_query_var( 'hour' ) ) {
					$trail['end'] = get_the_time( esc_html__( 'g a', 'knowherepro'));
				}
			} elseif (is_date()) {

				if ($wp_rewrite->front) {
					$trail = array_merge($trail, knowhere_breadcrumbs_get_parents('', $wp_rewrite->front));
				}

				if (is_day()) {
					$trail[] = '<a href="' . esc_url(get_year_link( get_the_time( 'Y' ) )) . '" title="' . get_the_time( esc_attr__( 'Y', 'knowherepro' ) ) . '">' . get_the_time( esc_html__( 'Y', 'knowherepro' ) ) . '</a>';
					$trail[] = '<a href="' . esc_url(get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) )) . '" title="' . get_the_time( esc_attr__( 'F', 'knowherepro' ) ) . '">' . get_the_time( __( 'F', 'knowherepro' ) ) . '</a>';
					$trail['end'] = get_the_time( esc_html__( 'j', 'knowherepro' ) );
				} elseif ( get_query_var( 'w' ) ) {
					$trail[] = '<a href="' . esc_url(get_year_link( get_the_time( 'Y' ) )) . '" title="' . get_the_time( esc_attr__( 'Y', 'knowherepro' ) ) . '">' . get_the_time( esc_html__( 'Y', 'knowherepro' ) ) . '</a>';
					$trail['end'] = sprintf( esc_html__( 'Week %1$s', 'knowherepro' ), get_the_time( esc_attr__( 'W', 'knowherepro' ) ) );
				} elseif ( is_month() ) {
					$trail[] = '<a href="' . esc_url(get_year_link( get_the_time( 'Y' ) )) . '" title="' . get_the_time( esc_attr__( 'Y', 'knowherepro' ) ) . '">' . get_the_time( esc_html__( 'Y', 'knowherepro' ) ) . '</a>';
					$trail['end'] = get_the_time( esc_html__( 'F', 'knowherepro' ) );
				} elseif ( is_year() ) {
					$trail['end'] = get_the_time( esc_html__( 'Y', 'knowherepro' ) );
				}
			}
		} elseif ( is_search() ) {
			$trail['end'] = sprintf( esc_html__( 'Search results for &quot;%1$s&quot;', 'knowherepro' ), esc_attr( get_search_query() ) );
		} elseif ( is_404() ) {
			$trail['end'] = esc_html__( '404 Not Found', 'knowherepro' );
		}

		if (is_array($trail)) {
			if (!empty($trail['end'])) {
				if (!is_search()) {
					$trail['end'] = $trail['end'];
				}
				$trail['end'] = '<span class="trail-end">' . $trail['end'] . '</span>';
			}
			if (!empty($separator)) {
				$separator = '<span class="separate">'. $separator .'</span>';
			}
			$breadcrumb = join(" {$separator} ", $trail);

			if (!empty($after)) {
				$breadcrumb .= ' <span class="breadcrumb-after">' . $after . '</span>';
			}
		}
		return $breadcrumb;
	}
}

if (!function_exists('knowhere_breadcrumbs_get_parents')) {

	function knowhere_breadcrumbs_get_parents($post_id = '', $path = '') {
		$trail = array();

		if (empty($post_id) && empty($path)) {
			return $trail;
		}

		if (empty($post_id)) {
			$parent_page = get_page_by_path($path);

			if (empty($parent_page)) {
				$parent_page = get_page_by_title($path);
			}
			if (empty($parent_page)) {
				$parent_page = get_page_by_title (str_replace( array('-', '_'), ' ', $path));
			}
			if (!empty($parent_page)) {
				$post_id = $parent_page->ID;
			}
		}

		if ($post_id == 0 && !empty($path )) {
			$path = trim( $path, '/' );
			preg_match_all( "/\/.*?\z/", $path, $matches );

			if ( isset( $matches ) ) {
				$matches = array_reverse( $matches );
				foreach ( $matches as $match ) {

					if ( isset( $match[0] ) ) {
						$path = str_replace( $match[0], '', $path );
						$parent_page = get_page_by_path( trim( $path, '/' ) );

						if ( !empty( $parent_page ) && $parent_page->ID > 0 ) {
							$post_id = $parent_page->ID;
							break;
						}
					}
				}
			}
		}

		while ( $post_id ) {
			$page = get_page($post_id);
			$parents[]  = '<a href="' . esc_url(get_permalink( $post_id )) . '" title="' . esc_attr( get_the_title( $post_id ) ) . '">' . get_the_title( $post_id ) . '</a>';
			if(is_object($page)) {
				$post_id = $page->post_parent;
			} else {
				$post_id = "";
			}
		}
		if (isset($parents)) {
			$trail = array_reverse($parents);
		}
		return $trail;
	}

}

if (!function_exists('knowhere_get_term_parents')) {

	function knowhere_get_term_parents($parent_id = '', $taxonomy = '') {
		$trail = array();
		$parents = array();

		if (empty( $parent_id ) || empty($taxonomy)) {
			return $trail;
		}
		while ($parent_id) {
			$parent = get_term( $parent_id, $taxonomy );
			$parents[] = '<a href="' . esc_url(get_term_link( $parent, $taxonomy )) . '" title="' . esc_attr($parent->name) . '">' . $parent->name . '</a>';
			$parent_id = $parent->parent;
		}
		if (!empty($parents)) {
			$trail = array_reverse($parents);
		}
		return $trail;
	}

}

if ( !function_exists('knowhere_woocommerce_set_defaults') ) {

	function knowhere_woocommerce_set_defaults() {
		global $knowhere_config;

		$knowhere_config['themeImgSizes']['shop_thumbnail'] = array( 'width' => 100, 'height' => 100 );
		$knowhere_config['themeImgSizes']['shop_catalog']   = array( 'width' => 390, 'height' => 230 );
		$knowhere_config['themeImgSizes']['shop_single']    = array( 'width'=> 750, 'height'=> 480 );

		update_option( 'shop_thumbnail_image_size', $knowhere_config['themeImgSizes']['shop_thumbnail'] );
		update_option( 'shop_catalog_image_size', $knowhere_config['themeImgSizes']['shop_catalog'] );
		update_option( 'shop_single_image_size', $knowhere_config['themeImgSizes']['shop_single'] );

		$disabled_options = array('woocommerce_enable_lightbox', 'woocommerce_frontend_css');

		foreach ( $disabled_options as $option ) {
			update_option( $option, false );
		}

	}

	add_action('knowhere_backend_theme_activation', 'knowhere_woocommerce_set_defaults');

}

if ( !function_exists('knowhere_maps_key_for_plugins') ) {

	add_filter( 'script_loader_src', 'knowhere_maps_key_for_plugins', 10 , 99, 2 );

	function knowhere_maps_key_for_plugins ( $url, $handle  ) {

		global $knowhere_settings;

		$key = $knowhere_settings['gmap-api'];

		if ( ! $key ) { return $url; }

		if ( strpos( $url, "maps.google.com/maps/api/js" ) !== false || strpos( $url, "maps.googleapis.com/maps/api/js" ) !== false ) {
			if ( strpos( $url, "key=" ) === false ) {
				$url = "https://maps.googleapis.com/maps/api/js?v=3.24";
				$url = esc_url( add_query_arg( 'key', $key, $url) );
			}
		}

		return $url;
	}
}

if( ! function_exists ( 'knowhere_get_taxonomy_terms' ) ) {
	function knowhere_get_taxonomy_terms( $post_id, $taxonomy, $args = array() ) {

		$defaults = array(
			'type' => '',
			'list_class' => 'kw-agent-skills-list'
		);

		$args = wp_parse_args ( $args, $defaults );

		$terms = get_the_terms( $post_id, $taxonomy );

		if ( ! empty ( $terms ) ) {
			$out = array();

			if ( $args['type'] == 'list' ) {
				$out[] = '<ul class="' . sanitize_html_class($args['list_class']) . '">';
			}

			foreach ( $terms as $term ) {

				if ( $args['type'] == 'list' ) {
					$out[] = sprintf( '<li>%s</li>',
						esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, $taxonomy, 'display' ) )
					);
				} else {
					$out[] = sprintf( '%s',
						esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, $taxonomy, 'display' ) )
					);
				}

			}

			if ( $args['type'] == 'list' ) {
				$out[] = '</ul>';
			}

			return implode( ' ', $out );
		}

		return false;
	}
}