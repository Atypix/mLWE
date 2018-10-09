<?php
/**
 * Custom KnowherePro template tags
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package WordPress
 * @subpackage KnowherePro
 * @since KnowherePro 1.0
 */

if ( ! function_exists('knowhere_excerpt') ) :
	/**
	 * Displays the optional excerpt.
	 *
	 * Wraps the excerpt in a div element.
	 *
	 * Create your own twentysixteen_excerpt() function to override in a child theme.
	 *
	 * @since KnowherePro 1.0
	 *
	 * @param string $class Optional. Class string of the div element. Defaults to 'entry-summary'.
	 */
	function knowhere_excerpt( $class = 'entry-summary' ) {
		$class = esc_attr( $class );

		if ( has_excerpt() || is_search() ) : ?>
			<div class="<?php echo sanitize_html_class($class); ?>">
				<?php the_excerpt(); ?>
			</div><!-- .<?php echo sanitize_html_class($class); ?> -->
		<?php endif;
	}
endif;

if ( ! function_exists('knowhere_post_thumbnail') ) :
	function knowhere_post_thumbnail($size = 'post-thumbnail') {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}

		if ( is_singular() ) :
			?>

			<div class="kw-entry-thumb">
				<?php the_post_thumbnail($size); ?>
			</div>

		<?php else : ?>

			<a class="kw-entry-thumb" href="<?php the_permalink(); ?>" aria-hidden="true">
				<?php the_post_thumbnail( 'post-thumbnail', array( 'alt' => the_title_attribute( 'echo=0' ) ) ); ?>
			</a>

		<?php endif; // End is_singular()
	}
endif;

if ( ! function_exists('knowhere_prev_next_page_links') ) :

	function knowhere_prev_next_page_links() {

		global $knowhere_settings;

		$next_post = get_next_post();
		$prev_post = get_previous_post();
		$next_post_url = $prev_post_url = "";
		$next_post_title = $prev_post_title = "";

		if ( is_object($next_post) ) {
			$next_post_url = get_permalink($next_post->ID);
			$next_post_title = $next_post->post_title;
		}
		if ( is_object($prev_post) ) {
			$prev_post_url = get_permalink($prev_post->ID);
			$prev_post_title = $prev_post->post_title;
		}

		if ( !empty($prev_post_url) || !empty($next_post_url) ): ?>

			<?php if ( $knowhere_settings['job-type-fields'] == 'property' ): ?>

				<?php if ( !empty($prev_post_url) ): ?>
					<a class="kw-nav-prev" href="<?php echo esc_url($prev_post_url) ?>" title="<?php echo esc_attr($prev_post_title) ?>"><?php esc_html_e('Prev Property', 'knowherepro') ?></a>
				<?php endif; ?>

				<?php if ( !empty($next_post_url) ): ?>
					<a class="kw-nav-next" href="<?php echo esc_url($next_post_url) ?>" title="<?php echo esc_attr($next_post_title) ?>"><?php esc_html_e('Next Property', 'knowherepro') ?></a>
				<?php endif; ?>

			<?php else: ?>

				<?php if ( !empty($prev_post_url) ): ?>
					<div class="kw-page-nav-item">
						<span class="lnr icon-chevron-left-circle"></span><a title="<?php echo esc_attr($prev_post_title) ?>" href="<?php echo esc_url($prev_post_url) ?>"><?php esc_html_e('Previous Ad', 'knowherepro') ?></a>
					</div>
				<?php endif; ?>

				<?php if ( !empty($next_post_url) ): ?>
					<div class="kw-page-nav-item kw-right-icon">
						<span class="lnr icon-chevron-right-circle"></span><a title="<?php echo esc_attr($next_post_title) ?>" href="<?php echo esc_url($next_post_url) ?>"><?php esc_html_e('Next Ad', 'knowherepro') ?></a>
					</div>
				<?php endif; ?>

			<?php endif; ?>

		<?php endif;

	}
endif;

if ( ! function_exists('knowhere_entry_date') ) :
	/**
	 * Prints HTML with date information for current post.
	 *
	 */
	function knowhere_entry_date( $id = null, $atts = array() ) {

		global $knowhere_settings;

		$defaults = array(
			'layout' => 'kw-blog-default',
			'type' => 'default'
		);

		$atts = wp_parse_args($atts, $defaults);

		if ( in_array('date', $knowhere_settings['post-metas']) ) {

			if ( $atts['layout'] == 'kw-carousel' ) {

				echo sprintf( '<time datetime="%1$s">%2$s</time>, %3$s <a href="%4$s">%5$s</a>',
					esc_attr( get_the_date( 'c', $id ) ),
					esc_attr( get_the_date( 'F j, Y', $id ) ),
					esc_html__('by', 'knowherepro'),
					esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
					get_the_author()
				);

			} else {

				if ( $atts['type'] == 'related' ) {

					echo sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><time datetime="%3$s">%4$s</time></a>',
						esc_url( get_permalink($id) ),
						esc_attr( sprintf( __( 'Permalink to %s', 'knowherepro' ), get_the_title($id)) ),
						esc_attr( get_the_date( 'c', $id ) ),
						esc_attr( get_the_date( 'F j, Y', $id ) )
					);

				} else {

					echo sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><time datetime="%3$s">%4$s</time></a>, %5$s <a href="%6$s">%7$s</a>, %8$s',
						esc_url( get_permalink($id) ),
						esc_attr( sprintf( __( 'Permalink to %s', 'knowherepro' ), get_the_title($id)) ),
						esc_attr( get_the_date( 'c', $id ) ),
						esc_attr( get_the_date( 'F j, Y', $id ) ),
						esc_html__('by', 'knowherepro'),
						esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
						get_the_author(),
						knowhere_comments_popup_link( $id )
					);

				}
			}

		}

	}
endif;



/*	Blog Post Meta
/* ---------------------------------------------------------------------- */

if ( ! function_exists('knowhere_blog_post_meta') ) :

	function knowhere_blog_post_meta( $id = 0, $args = array() ) {

		global $knowhere_settings;
		$defaults = array(
			'container' => true,
			'tags' => true,
			'cats' => true,
			'sticky' => true
		);
		$args = wp_parse_args( $args, $defaults );
		$args = (object) $args;

		ob_start(); ?>

		<?php if ( is_single() ): ?>

			<?php if ( $args->container ): ?><div class="entry-meta"><?php endif; ?>

				<?php if ( $args->cats ): ?>

					<?php $categories = get_the_category_list(", ", '', $id); ?>

					<?php if ( !empty($categories) ): ?>
						<ul class="kw-entry-cats">
							<li><?php echo sprintf('<span class="screen-reader-text">%1$s </span>%2$s', esc_html__( 'Categories:', 'knowherepro' ), $categories) ?></li>
						</ul><!--/ .kw-entry-cats-->
					<?php endif; ?>

				<?php endif; ?>

				<?php if ( $args->tags && has_tag() && !post_password_required() ): ?>

					<?php
					$tags_list = get_the_tag_list( '', ', ', '' );
					if ( $tags_list ) {
						printf( '<div class="kw-tags-links"><span class="screen-reader-text">%1$s </span>%2$s</div>',
							esc_html__( 'Tags:', 'knowherepro' ),
							$tags_list
						);
					}
					?>

				<?php endif; ?>

			<?php if ( $args->container ): ?></div><?php endif; ?>

		<?php else: ?>

			<?php if ( $args->container ): ?><div class="entry-meta"><?php endif; ?>

				<?php if ( $args->cats ): ?>

					<?php if ( in_array('cats', $knowhere_settings['post-metas']) ): ?>

						<?php $categories = get_the_category_list(", ", '', $id); ?>

						<?php if ( !empty($categories) ): ?>
							<ul class="kw-entry-cats">
								<li><?php echo sprintf('%1$s', $categories) ?></li>
							</ul><!--/ .kw-entry-cats-->
						<?php endif; ?>

					<?php endif; ?>

				<?php endif; ?>

				<?php if ( $args->sticky ): ?>
					<?php if ( is_sticky($id) ): ?>
						<?php printf( '<div class="kw-sticky"><span class="sticky">%s</span></div>', esc_html__( 'Featured', 'knowherepro' ) ); ?>
					<?php endif; ?>
				<?php endif; ?>

			<?php if ( $args->container ): ?></div><?php endif; ?>

			<?php if ( in_array('tags', $knowhere_settings['post-metas']) ): ?>

				<?php $tags_list = get_the_tag_list( '', '', '', $id ); ?>

				<?php if ( !empty($tags_list) ) : ?>
					 <div class="kw-entry-tags"><?php echo sprintf('%s', $tags_list) ?></div>
				<?php endif; ?>

			<?php endif; ?>

		<?php endif; ?>

		<?php return ob_get_clean();
	}
endif;

if ( ! function_exists('knowhere_comments_popup_link') ) :

	function knowhere_comments_popup_link( $id = false, $echo = false, $comments_without_text = false, $zero = false, $one = false, $more = false, $css_class = 'kw-entry-comments-link' ) {
		$number = get_comments_number( $id );

		if ( post_password_required() ) {
			esc_html_e( 'Enter your password to view comments.', 'knowherepro' );
			return;
		}


		$output = '<a href="';
		$output .= apply_filters( 'knowhere_respond_link', get_permalink($id) . '#respond', $id );
		$output .= '"';

		if ( !empty( $css_class ) ) {
			$output .= ' class="'. $css_class .'" ';
		}

		$output .= '>';

		if ( false === $more ) {

			if ( $comments_without_text ) {
				$output .= sprintf( '%s', number_format_i18n( $number ) );
			} else {
				$output .= sprintf( _n( '%s Comment', '%s Comments', $number, 'knowherepro' ), number_format_i18n( $number ) );
			}

		} else {
			// % Comments
			/* translators: If comment number in your language requires declension,
			 * translate this to 'on'. Do not translate into your own language.
			 */
			if ( 'on' === _x( 'off', 'Comment number declension: on or off', 'knowherepro' ) ) {
				$text = preg_replace( '#<span class="screen-reader-text">.+?</span>#', '', $more );
				$text = preg_replace( '/&.+?;/', '', $text ); // Kill entities
				$text = trim( strip_tags( $text ), '% ' );

				// Replace '% Comments' with a proper plural form
				if ( $text && ! preg_match( '/[0-9]+/', $text ) && false !== strpos( $more, '%' ) ) {
					/* translators: %s: number of comments */
					$new_text = _n( '%s Comment', '%s Comments', $number, 'knowherepro' );
					$new_text = trim( sprintf( $new_text, '' ) );

					$more = str_replace( $text, $new_text, $more );
					if ( false === strpos( $more, '%' ) ) {
						$more = '% ' . $more;
					}
				}
			}

			$output .= str_replace( '%', number_format_i18n( $number ), $more );
		}

		$output .= '</a>';

		if ( $echo ) {
			echo $output;
		} else {
			return $output;
		}

	}
endif;

if ( ! function_exists('knowhere_get_excerpt') ) :
	/**
	 * Displays the get excerpt.
	 *
	 */
	function knowhere_get_excerpt( $post_content, $limit = 120 ) {
		if ( empty($post_content) ) return '';
		$content = knowhere_string_truncate( $post_content, $limit, ' ', "...", true, '' );
		$content = apply_filters( 'the_excerpt', $content );
		$content = do_shortcode($content);
		return $content;
	}
endif;


if ( ! function_exists('knowhere_get_search_excerpt') ) :
	/**
	 * Displays the get excerpt for search.
	 *
	 */
	function knowhere_get_search_excerpt( $limit = 150, $more_link = true ) {

		if ( !$limit ) { $limit = 45; }

		if ( has_excerpt() ) {
			$content = strip_tags( strip_shortcodes(get_the_excerpt()) );
		} else {
			$content = strip_tags( strip_shortcodes(get_the_content()) );
		}

		$content = explode(' ', $content, $limit);

		if ( count($content) >= $limit ) {
			array_pop($content);
			if ($more_link)
				$content = implode(" ",$content).'... ';
			else
				$content = implode(" ",$content).' [...]';
		} else {
			$content = implode(" ",$content);
		}

		$content = apply_filters('the_content', $content);
		$content = do_shortcode($content);
		return $content;
	}
endif;
