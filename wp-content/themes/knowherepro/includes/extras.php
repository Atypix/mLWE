<?php
/**
 * Custom functions of the theme templates.
 *
 * @package KnowherePro
 */

if ( !function_exists('knowhere_get_listing_gallery_ids') ) {

	function knowhere_get_listing_gallery_ids( $listing_ID = null ) {

		if ( empty( $listing_ID ) ) { $listing_ID = get_the_ID(); }
		if ( empty( $listing_ID ) ) { return false; }

		$gallery_string = get_post_meta( $listing_ID, 'mad_perm_metadata', true );
		$gallery_string = str_replace( ' ', '', $gallery_string );

		if ( ',' === substr( $gallery_string, - 1, 1 ) ) {
			$gallery_string = substr( $gallery_string, 0, - 1 );
		}

		if ( ! empty( $gallery_string ) ) {
			$gallery_ids = explode( ',', $gallery_string );

			foreach ( $gallery_ids as $key => $value ) {
				if ( false === filter_var( $value, FILTER_VALIDATE_INT, array(
						'options' => array( 'min_range' => 1 ) )))
				{ unset( $gallery_ids[ $key ] ); }
			}
			$gallery_ids = array_values( $gallery_ids );
		}

		if ( ! empty( $gallery_ids ) ) { return $gallery_ids; }

		return false;
	}

}

if ( !function_exists('knowhere_get_attachment_id_from_url') ) {
	function knowhere_get_attachment_id_from_url( $attachment_url = '' ) {

		global $wpdb;
		$attachment_id = false;

		if ('' == $attachment_url) {
			return false;
		}

		$upload_dir_paths = wp_upload_dir();

		if (false !== strpos($attachment_url, $upload_dir_paths['baseurl'])) {

			$attachment_url = preg_replace('/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url);
			$attachment_url = str_replace($upload_dir_paths['baseurl'] . '/', '', $attachment_url);
			$attachment_id = $wpdb->get_var($wpdb->prepare("SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url));

		}

		return $attachment_id;
	}
}

if ( !function_exists('knowhere_get_term_icon_url') ) {
	function knowhere_get_term_icon_url( $term_id = null, $size = 'thumbnail' ) {

		$attachment_id = knowhere_get_term_icon_id($term_id);

		if ( !empty($attachment_id) ) {
			$attach_args = wp_get_attachment_image_src($attachment_id, $size);
			if ( isset($attach_args[0]) ) {
				return $attach_args[0];
			}
		}

		return false;
	}
}

/*  Is really job manager page
/* ---------------------------------------------------------------------- */

if ( ! function_exists('knowhere_is_realy_job_manager_single') ) {
	function knowhere_is_realy_job_manager_single() {

		if ( is_singular( 'job_listing' ) ) {
			return true;
		}

		return false;
	}
}

if ( !function_exists('knowhere_job_listing_has_shortcode_companies') ) {
	function knowhere_job_listing_has_shortcode_companies() {
		global $post;

		if ( ! empty( $post->post_content ) && has_shortcode( $post->post_content, 'job_manager_companies' ) ) {
			return true;
		}

		return false;

	}
}


if ( !function_exists('knowhere_job_listing_has_shortcode_jobs') ) {
	function knowhere_job_listing_has_shortcode_jobs() {
		global $post;

		if ( ! empty( $post->post_content ) && has_shortcode( $post->post_content, 'jobs' ) ) {
			return true;
		}

		return false;

	}
}

if ( !function_exists('knowhere_is_realy_job_manager_submit_job_form') ) {
	function knowhere_is_realy_job_manager_submit_job_form()
	{
		global $post;

		if ( isset($post->post_content) && has_shortcode($post->post_content, 'submit_job_form') ) {
			return true;
		}

		return false;

	}
}

if ( !function_exists('knowhere_is_realy_job_manager_submit_resume_form') ) {
	function knowhere_is_realy_job_manager_submit_resume_form()
	{
		global $post;

		if ( isset($post->post_content) && has_shortcode($post->post_content, 'submit_resume_form') ) {
			return true;
		}

		return false;

	}
}

if ( ! function_exists('knowhere_is_realy_job_manager_page') ) {
	function knowhere_is_realy_job_manager_page( $args = array() ) {

		global $post;

		if ( !$post ) return;

		$single = $jobs = $submit_job_form = '';

		$defaults = array(
			'single' => true,
			'jobs' => has_shortcode( $post->post_content, 'jobs' ) || has_shortcode( $post->post_content, 'vc_mad_listing_map' ) ? true : false,
			'submit_job_form' => has_shortcode( isset($post->post_content) && $post->post_content, 'submit_job_form' ) ? true : false
		);

		$args = wp_parse_args( $args, $defaults );

		extract($args);

		if ( $single ) {
			$single = is_singular('job_listing') ? true : false;
		} else {
			$single = false;
		}

		if ( ( isset( $post->post_content ) && $jobs && true === knowhere_jobs_shortcode_get_show_map_param( $post->post_content ) )
			|| ( $single && 'job_listing' == $post->post_type ) || is_search()
			|| ( isset( $post->post_content ) && is_archive() && 'job_listing' == $post->post_type )
			|| is_tax( array( 'job_listing_category', 'job_listing_tag', 'job_listing_region', 'job_listing_type' ) )
			|| ( isset( $post->post_content ) && $submit_job_form )
		) {
			return true;
		}

		return false;
	}
}


if ( ! function_exists('knowhere_is_realy_resume_manager_page') ) {
	function knowhere_is_realy_resume_manager_page( $args = array() ) {

		global $post;

		if ( !$post ) return;

		$single = $resumes = $submit_resume_form = $candidate_dashboard = '';

		$defaults = array(
			'single' => true,
			'resumes' => has_shortcode( $post->post_content, 'resumes' )  ? true : false,
			'submit_resume_form' => has_shortcode( $post->post_content, 'submit_resume_form' ) ? true : false,
			'candidate_dashboard' => has_shortcode( $post->post_content, 'candidate_dashboard' ) ? true : false
		);

		$args = wp_parse_args( $args, $defaults );

		extract($args);

		if ( $single ) {
			$single = is_singular('resume') ? true : false;
		} else {
			$single = false;
		}

		if ( ( isset( $post->post_content ) && $resumes )
			|| ( $single && 'resume' == $post->post_type )
			|| ( isset( $post->post_content ) && $submit_resume_form )
			|| ( isset( $post->post_content ) && $candidate_dashboard )
		) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists('knowhere_is_realy_job_manager_tax') ) {
	function knowhere_is_realy_job_manager_tax() {

		if ( is_tax( array( 'job_listing_category', 'job_listing_tag', 'job_listing_region', 'job_listing_type' ) )
		) {
			return true;
		}

		return false;
	}
}

if ( !function_exists('knowhere_output_single_listing_icon') ) {
	function knowhere_output_single_listing_icon( $job_id = null ) {
		global $post;

		$the_term = null;
		$post_id = $post->ID;

		if ( $job_id ) {
			$post_id = $job_id;
		}

		$cat_list = wp_get_post_terms(
			$post_id,
			'job_listing_category',
			array('fields' => 'all')
		);

		if ( !empty($cat_list) && !is_wp_error($cat_list) ) {
			foreach ( $cat_list as $term ) :
				if ( knowhere_get_term_icon_url($term->term_id) ) {
					$the_term = $term;
					break;
				}
			endforeach;
		}

		if ( $the_term == null ) {
			$tag_list = wp_get_post_terms(
				$post->ID,
				'job_listing_tag',
				array('fields' => 'all')
			);

			if ( !empty($tag_list) && !is_wp_error($tag_list) ) {
				foreach ( $tag_list as $term ) :
					if ( knowhere_get_term_icon_url($term->term_id) ) {
						$the_term = $term;
						break;
					}
				endforeach;
			}
		}

		if ( $the_term != null ) {
			$icon_url = knowhere_get_term_icon_url($the_term->term_id);
			$attachment_id = knowhere_get_term_icon_id($the_term->term_id);
			echo '<div class="kw-single-map-category-icon">';
				knowhere_display_icon_or_image($icon_url, '', true, $attachment_id);
			echo '</div>';
		}
	}
}

if ( !function_exists('knowhere_display_icon_or_image') ) {
	function knowhere_display_icon_or_image($url, $class = '', $wrap_as_img = true, $attachment_id = null) {

		if ( !empty($url) && is_string($url) ) {

			global $wp_filesystem;

			if ( empty($wp_filesystem) ) {
				require_once(ABSPATH . '/wp-admin/includes/file.php');
				WP_Filesystem();
			}

			if ( substr($url, -4) === '.svg' ) {

				if ( !empty($attachment_id) ) {
					echo $wp_filesystem->get_contents(get_attached_file($attachment_id));
				} elseif ( false !== ($svg_code = get_transient(md5($url))) ) {
					echo $svg_code;
				} else {

					$svg_code = $wp_filesystem->get_contents($url);

					if ( !empty($svg_code) ) {
						set_transient(md5($url), $svg_code, 12 * HOUR_IN_SECONDS);
						echo $svg_code;
					}

				}

			} elseif ($wrap_as_img) {

				if (!empty($class)) {
					$class = ' class="' . $class . '"';
				}

				echo '<img alt="" src="' . esc_url($url) . '"' . $class . '/>';

			} else {
				echo $url;
			}
		}
	}
}

if ( !function_exists('knowhere_get_login_url') ) {
	function knowhere_get_login_url() {
		if ( !is_user_logged_in() ) {
			$url = esc_url(wp_login_url(get_permalink())) . '&modal_login=true#login';
		} else {
			$url = esc_url(wp_logout_url(home_url()));
		}

		return $url;
	}
}

if ( !function_exists('knowhere_get_login_link_class') ) {
	function knowhere_get_login_link_class($class = '') {

		global $knowhere_settings;

		$search_and_login = $knowhere_settings['header-type-1-show-search-and-login'];
		$meta_show_search_and_login = mad_meta('knowhere_header_show_search_and_login');
		$show_search_and_login = array_unique(array_merge($search_and_login, $meta_show_search_and_login));

		if ( knowhere_using_login_with_ajax() ) {

			if (!is_user_logged_in()) {
				$class .= ' lwa-links-modal';
			} else {
				$class .= ' lwa-logout-link';
			}

		} else {

			if (!is_user_logged_in()) {
				$class .= ' lwa-login-link';
			} else {
				$class .= ' lwa-logout-link';
			}

		}

		if ( in_array( 'login', $show_search_and_login ) ) {
			$class .= ' kw-icon-login';
		}

		return $class;
	}
}

if ( !function_exists('knowhere_get_login_text') ) {
	function knowhere_get_login_text() {
		if (!is_user_logged_in()) {
			$text = esc_html__('Login', 'knowherepro');
		} else {
			$text = esc_html__('Logout', 'knowherepro');
		}
		return $text;
	}
}

if ( !function_exists('knowhere_get_term_image_id') ) {
	function knowhere_get_term_image_id($term_id = null, $taxonomy = null) {

		if (function_exists('get_term_meta')) {

			if (null === $term_id) {
				global $wp_query;
				$term = $wp_query->queried_object;
				$term_id = $term->term_id;

			}

			return get_term_meta($term_id, 'pix_term_image', true);
		}

		return false;
	}
}

function knowhere_get_term_image_url( $term_id = null, $size = 'thumbnail' ) {

	$attachment_id = knowhere_get_term_image_id( $term_id );

	if ( ! empty( $attachment_id ) ) {
		$attach_args = wp_get_attachment_image_src( $attachment_id, $size );

		// $attach_args[0] should be the url
		if ( isset( $attach_args[0] ) ) {
			return $attach_args[0];
		}
	}

	return false;
}

if ( !function_exists('knowhere_get_term_icon_id') ) {
	function knowhere_get_term_icon_id( $term_id = null, $taxonomy = null ) {

		if ( function_exists('get_term_meta') ) {

			if ( null === $term_id ) {
				global $wp_query;
				$term = $wp_query->queried_object;
				$term_id = $term->term_id;
			}

			return get_term_meta( $term_id, 'pix_term_icon', true );
		}

		return false;
	}
}

if ( ! function_exists( 'knowhere_get_post_image_id' ) ) {
	function knowhere_get_post_image_id( $post_ID = null ) {

		if ( empty( $post_ID ) ) {
			$post_ID = get_the_ID();
		}

		$gallery_ids = knowhere_get_listing_gallery_ids( $post_ID );

		if ( ! empty( $gallery_ids ) ) {
			return $gallery_ids[0];
		} else {
			return esc_sql( get_post_thumbnail_id( $post_ID ) );
		}

		return false;
	}
}

if ( !function_exists('knowhere_job_get_term') ) {
	function knowhere_job_get_term( $term, $term_id = null, $is_tax = true ) {

		if ( function_exists('get_term_meta') ) {
			if ( $is_tax ) {
				if ( is_tax( array('job_listing_category', 'job_listing_tag', 'job_listing_region') ) ) {

					if ( null === $term_id ) {
						global $wp_query;
						$term_queried = $wp_query->queried_object;
						$term_id = $term_queried->term_id;
					}

					return get_term_meta( $term_id, $term, true );

				}
			} else {
				return get_term_meta( $term_id, $term, true );
			}
		}

		return false;
	}

}


/**
 * Return the src of the post image.
 *
 * @param null $post_id
 * @param string $size
 *
 * @return bool
 */
if ( ! function_exists( 'knowhere_get_post_image_src' ) ) {
	function knowhere_get_post_image_src( $post_id = null, $size = 'thumbnail' ) {

		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}

		$attach_id = knowhere_get_post_image_id( $post_id );

		if ( empty( $attach_id ) || is_wp_error( $attach_id ) ) {
			return '';
		}

		$data = wp_get_attachment_image_src( $attach_id, $size );

		if ( isset( $data[0] ) && !empty( $data ) ) {
			return $data[0];
		}

		return false;
	}
}

if ( ! function_exists( 'knowhere_get_shortcode_param_value' ) ) {
	function knowhere_get_shortcode_param_value($content, $shortcode, $param, $default)
	{

		$param_value = $default;
		if ( has_shortcode( $content, $shortcode ) ) {
			$pattern = get_shortcode_regex( array( $shortcode ) );

			if ( preg_match_all( '/'. $pattern .'/s', $content, $matches ) ) {
				$keys = array();
				$result = array();
				foreach( $matches[0] as $key => $value) {
					$get = str_replace(" ", "&" , $matches[3][$key] );
					parse_str($get, $output);
					$keys = array_unique( array_merge(  $keys, array_keys($output)) );
					$result[] = $output;

				}

				if ( ! empty( $result ) ) {
					$value = knowhere_preg_match_array_get_value_by_key( $result, $param );

					if ( null !== $value ) {
						$param_value = stripslashes_deep( $value );
					}
				}
			}
		}

		return $param_value;
	}
}

if ( ! function_exists( 'knowhere_preg_match_array_get_value_by_key' ) ) {
	function knowhere_preg_match_array_get_value_by_key($arrs, $searched)
	{
		foreach ( $arrs as $arr ) {
			foreach ( $arr as $key => $value ) {
				if ( $key == $searched ) {
					return $value;
				}
			}
		}

		return null;
	}
}

/*	Listing Breadcrumbs
/* ---------------------------------------------------------------------- */

if ( ! function_exists( 'knowhere_job_listing_breadcrumbs' ) ) {
	function knowhere_job_listing_breadcrumbs() {
		global $post, $knowhere_settings; ?>

		<div class="kw-breadcrumb-container">

			<div class="container">

				<?php if ( $knowhere_settings['job-type-fields'] == 'property' ): ?>

					<div class="kw-sm-table-row row kw-xs-small-offset">

						<div class="col-sm-9">

							<ul class="kw-breadcrumb">

								<li><a href="<?php echo knowhere_get_listings_page_url(); ?>"><?php esc_html_e('Listings', 'knowherepro'); ?></a></li>

								<?php
								$term_list = wp_get_post_terms(
									$post->ID,
									'job_listing_category',
									array(
										"fields" => "all",
										'orderby' => 'parent',
									)
								);

								if ( !empty($term_list) && !is_wp_error($term_list) ) {
									foreach ($term_list as $key => $term) {
										echo '<li><a href="' . esc_url(get_term_link($term)) . '">' . $term->name . '</a></li>';
									}
								} ?>

							</ul><!--/ .kw-breadcrumb -->

						</div>

						<div class="col-sm-3 kw-right-edge">
							<?php echo knowhere_prev_next_page_links() ?>
						</div>

					</div>

				<?php else: ?>

					<ul class="kw-breadcrumb">

						<li><a href="<?php echo knowhere_get_listings_page_url(); ?>"><?php esc_html_e('Listings', 'knowherepro'); ?></a></li>

						<?php
						$term_list = wp_get_post_terms(
							$post->ID,
							'job_listing_category',
							array(
								"fields" => "all",
								'orderby' => 'parent',
							)
						);

						if ( !empty($term_list) && !is_wp_error($term_list) ) {
							foreach ($term_list as $key => $term) {
								echo '<li><a href="' . esc_url(get_term_link($term)) . '">' . $term->name . '</a></li>';
							}
						} ?>

					</ul><!--/ .kw-breadcrumb -->

				<?php endif; ?>

			</div><!--/ .container -->

		</div><!--/ .kw-breadcrumb-container-->

		<?php
	}
}

/*	Post ID
/* ---------------------------------------------------------------------- */

if ( !function_exists('knowhere_post_id') ) {

	function knowhere_post_id() {
		$object_id = get_queried_object_id();

		$post_id = false;

		if ( get_option( 'show_on_front' ) && get_option( 'page_for_posts' ) && is_home() ) {
			$post_id = get_option( 'page_for_posts' );
		} else {
			// Use the $object_id if available.
			if ( isset( $object_id ) ) {
				$post_id = $object_id;
			}
			// If we're not on a singular post, set to false.
			if ( ! is_singular() ) {
				$post_id = false;
			}
			// Front page is the posts page.
			if ( isset( $object_id ) && 'posts' == get_option( 'show_on_front' ) && is_home() ) {
				$post_id = $object_id;
			}
			// The woocommerce shop page.
			if ( class_exists( 'WooCommerce' ) && ( is_shop() || is_tax( 'product_cat' ) || is_tax( 'product_tag' ) ) ) {
				$post_id = get_option( 'woocommerce_shop_page_id' );
			}
		}

		return $post_id;
	}
}

/*	Blog alias
/* ---------------------------------------------------------------------- */

if ( !function_exists('knowhere_blog_alias') ) {

	function knowhere_blog_alias ( $format = 'standard', $atts ) {

		if ( $atts['layout'] == 'kw-carousel' ) {

			switch ( $format ) {
				case 'standard':
				case 'gallery':
					$alias = array(360, 230);
				break;
				default:
					$alias = array(360, 230);
					break;
			}
			return $alias;

		} else {

			switch ( $format ) {
				case 'standard':
				case 'gallery':
					$alias = array(1170, 750);
				break;
				default:
					$alias = array(1170, 750);
					break;
			}
			return $alias;

		}

	}
}

/*	Debug function print_r
/* ---------------------------------------------------------------------- */

if (!function_exists('t_print_r')) {
	function t_print_r( $arr ) {
		echo "<pre>";
		print_r($arr);
		echo "</pre>";
	}
}

/* 	Pagination
/* ---------------------------------------------------------------------- */

if( !function_exists( 'knowhere_pagination' ) ) {

	function knowhere_pagination( $entries = '', $args = array(), $range = 10 ) {

		global $wp_query;

		$paged = (get_query_var('paged')) ? get_query_var('paged') : false;

		if ( $paged === false ) $paged = (get_query_var('page')) ? get_query_var('page') : false;
		if ( $paged === false ) $paged = 1;

		if ($entries == '') {

			if ( isset( $wp_query->max_num_pages ) )
				$pages = $wp_query->max_num_pages;

			if( !$pages )
				$pages = 1;

		} else {
			$pages = $entries->max_num_pages;
		}

		if ( 1 != $pages ) { ob_start(); ?>

			<!-- - - - - - - - - - - - - - Pagination - - - - - - - - - - - - - - - - -->

			<ul class="kw-pagination">

				<?php if( $paged > 1 ):  ?>
					<li><a class='prev page-numbers' href='<?php echo esc_url(get_pagenum_link( $paged - 1 )) ?>'><?php echo esc_html__('Previous', 'knowherepro') ?></a></li>
				<?php endif; ?>

				<?php for( $i=1; $i <= $pages; $i++ ): ?>
					<?php if ( 1 != $pages &&( !( $i >= $paged + $range + 1 || $i <= $paged - $range - 1 ) || $pages <= $range ) ): ?>
						<?php $class = ( $paged == $i ) ? " current" : ''; ?>
						<li><a class="page-numbers <?php echo sanitize_html_class($class) ?>" href='<?php echo esc_url(get_pagenum_link( $i )) ?>'><?php echo esc_html($i) ?></a></li>
					<?php endif; ?>
				<?php endfor; ?>

				<?php if ( $paged < $pages ):  ?>
					<li><a class='next page-numbers' href='<?php echo esc_url(get_pagenum_link( $paged + 1 )) ?>'><?php echo esc_html__('Next', 'knowherepro') ?></a></li>
				<?php endif; ?>

			</ul>

			<!-- - - - - - - - - - - - - - End of Pagination - - - - - - - - - - - - - - - - -->

		<?php return ob_get_clean(); }
	}
}

/*  Is shop installed
/* ---------------------------------------------------------------------- */

if (!function_exists('knowhere_is_shop_installed')) {
	function knowhere_is_shop_installed() {
		global $woocommerce;
		if ( isset( $woocommerce ) ) {
			return true;
		} else {
			return false;
		}
	}
}

/*  Is product
/* ---------------------------------------------------------------------- */

if ( ! function_exists('knowhere_is_product') ) {
	function knowhere_is_product() {
		return is_singular( array( 'product' ) );
	}
}

/*  Get WC page id
/* ---------------------------------------------------------------------- */

if ( ! function_exists('knowhere_wc_get_page_id') ) {
	function knowhere_wc_get_page_id( $page ) {

		if ( $page == 'pay' || $page == 'thanks' ) {
			_deprecated_argument( __FUNCTION__, '2.1', 'The "pay" and "thanks" pages are no-longer used - an endpoint is added to the checkout instead. To get a valid link use the WC_Order::get_checkout_payment_url() or WC_Order::get_checkout_order_received_url() methods instead.' );

			$page = 'checkout';
		}
		if ( $page == 'change_password' || $page == 'edit_address' || $page == 'lost_password' ) {
			_deprecated_argument( __FUNCTION__, '2.1', 'The "change_password", "edit_address" and "lost_password" pages are no-longer used - an endpoint is added to the my-account instead. To get a valid link use the wc_customer_edit_account_url() function instead.' );

			$page = 'myaccount';
		}

		$page = apply_filters( 'woocommerce_get_' . $page . '_page_id', get_option('woocommerce_' . $page . '_page_id' ) );

		return $page ? absint( $page ) : -1;
	}
}

/*  Is shop
/* ---------------------------------------------------------------------- */

if ( ! function_exists('knowhere_is_shop') ) {
	function knowhere_is_shop() {
		return is_post_type_archive( 'product' ) || is_page( knowhere_wc_get_page_id( 'shop' ) );
	}
}

/*  Is product tax
/* ---------------------------------------------------------------------- */

if ( ! function_exists('knowhere_is_product_tax') ) {
	function knowhere_is_product_tax() {
		return is_tax( get_object_taxonomies( 'product' ) );
	}
}

/*  Is product category
/* ---------------------------------------------------------------------- */

if ( ! function_exists('knowhere_is_product_category') ) {
	function knowhere_is_product_category( $term = '' ) {
		return is_tax( 'product_cat', $term );
	}
}

/*  Is product tag
/* ---------------------------------------------------------------------- */

if ( ! function_exists('knowhere_is_product_tag') ) {
	function knowhere_is_product_tag( $term = '' ) {
		return is_tax( 'product_tag', $term );
	}
}

/*  Is really woocommerce pages
/* ---------------------------------------------------------------------- */

if ( ! function_exists('knowhere_is_realy_woocommerce_page') ) {
	function knowhere_is_realy_woocommerce_page( $archive = true ) {

		if ( is_search() ) { return false; }

		if ( $archive ) {
			if ( knowhere_is_shop() || knowhere_is_product_tax() || knowhere_is_product() ) {
				return true;
			}
		}

		$woocommerce_keys = array("knowhere_woocommerce_shop_page_id",
			"woocommerce_terms_page_id",
			"woocommerce_cart_page_id",
			"woocommerce_checkout_page_id",
			"woocommerce_pay_page_id",
			"woocommerce_thanks_page_id",
			"woocommerce_myaccount_page_id",
			"woocommerce_edit_address_page_id",
			"woocommerce_view_order_page_id",
			"woocommerce_change_password_page_id",
			"woocommerce_logout_page_id",
			"woocommerce_lost_password_page_id");

		foreach ( $woocommerce_keys as $wc_page_id ) {

			if ( get_the_ID() == get_option($wc_page_id, 0 ) ) {
				return true;
			}
		}
		return false;
	}
}

if ( !function_exists('knowhere_name_of_listing') ) {
	function knowhere_name_of_listing( $before_text = '', $strtolower = false ) {
		global $knowhere_settings;

		if ( empty($before_text) ) {
			$before_text = $knowhere_settings['before-text-of-listing'];
		}

		$name = $knowhere_settings['name-of-listing-singular'];

		if ( !$name ) {
			$name = esc_html__('Listing', 'knowherepro');
		}

		if ( $strtolower ) {
			$name = strtolower($name);
		}

		echo sprintf( '%s %s', $before_text, $name );
	}
}

if ( ! function_exists('knowhere_get_login_links') ) {
	function knowhere_get_login_links() { ?>

		<?php if ( class_exists('WP_Job_Manager') ) : ?>

			<?php knowhere_account_links_output(); ?>

		<?php else: ?>

			<?php if ( !is_user_logged_in() ): ?>
				<li class="lwa"><a href="<?php echo esc_url(wp_login_url(get_permalink())) . '&modal_login=true#login'; ?>" class="<?php echo knowhere_get_login_link_class( 'kw-login' ); ?>"><?php echo esc_html__('Login', 'knowherepro') ?></a></li>
			<?php endif; ?>

		<?php endif; ?>

	<?php

	}

}

if ( ! function_exists('knowhere_account_links_output') ) {
	function knowhere_account_links_output() {

		?>

		<?php if ( is_user_logged_in() ):

			global $knowhere_settings;
			$my_account_url = $dashboard_page_url = $resumes_dashboard_id_url = $resumes_candidate_dashboard_id_url = $resume_form_page_id_url = $job_form_page_id_url = $edit_account_url = $job_bookmarks_url = $job_stats_url = '';
			$current_user = wp_get_current_user();
			$user_name = knowhere_get_user_name($current_user);

			if ( class_exists('WooCommerce') ) {
				$page_id = wc_get_page_id( 'myaccount' );
				$my_account_url = 0 < $page_id ? get_permalink( $page_id ) : '';

				if ( !empty($my_account_url) ) {
					$edit_account_url = wc_customer_edit_account_url();
				}
			}

			$dashboard_id = get_option( 'job_manager_job_dashboard_page_id' );
			$resumes_dashboard_id = get_option( 'resume_manager_resumes_page_id' );
			$resumes_candidate_dashboard_id = get_option( 'resume_manager_candidate_dashboard_page_id' );
			$job_form_page_id = get_option( 'job_manager_submit_job_form_page_id', false );
			$resume_form_page_id = get_option( 'resume_manager_submit_resume_form_page_id' );
			$job_bookmarks_page_id = get_option('wp_job_manager_bookmarks_page_id' );

			if ( !empty($dashboard_id) ) {
				$dashboard_page_url = get_permalink( $dashboard_id );
			}

			if ( !empty($resumes_dashboard_id) ) {
				$resumes_dashboard_id_url = get_permalink( $resumes_dashboard_id );
			}

			if ( !empty($resumes_candidate_dashboard_id) ) {
				$resumes_candidate_dashboard_id_url = get_permalink( $resumes_candidate_dashboard_id );
			}

			if ( !empty($job_form_page_id ) ) {
				$job_form_page_id_url = get_permalink($job_form_page_id);
			}

			if ( !empty($resume_form_page_id ) ) {
				$resume_form_page_id_url = get_permalink($resume_form_page_id);
			}

			if ( class_exists( 'WP_Job_Manager_Bookmarks' ) ) {

				if ( !empty($job_bookmarks_page_id) ) {
					$job_bookmarks_url = get_permalink($job_bookmarks_page_id);
				}

			}

			if ( function_exists('wpjms_stat_page_id') ) {
				$job_stats_page_id = wpjms_stat_page_id();
				if ( $job_stats_page_id ) {
					$job_stats_url = get_permalink($job_stats_page_id);
				}
			}

		?>

			<li>

				<div class="kw-dropdown kw-profile-nav">

					<span class="kw-dropdown-invoker">
						<?php echo get_avatar( $current_user->user_email, 30 ); ?>
						<?php echo esc_html($user_name) ?>
					</span>

					<ul class="kw-dropdown-list">

						<?php if ( !empty($my_account_url) ): ?>
							<li><a href="<?php echo esc_url($my_account_url) ?>"><span class="lnr icon-user-lock"></span><?php esc_html_e('My Account', 'knowherepro') ?></a></li>
						<?php endif; ?>

						
						
							<li><a href="/my-account/gestion-activites"><span class="lnr icon-gestion-activites"></span>Mes activités</a></li>
						<?php if ( $knowhere_settings['job-resume-resumes-url'] ): ?>
							<?php if ( !empty($resumes_dashboard_id_url) ): ?>
								<li><a href="<?php echo esc_url($resumes_dashboard_id_url) ?>"><span class="lnr icon-list"></span><?php echo get_the_title($resumes_dashboard_id) ?></a></li>
							<?php endif; ?>
						<?php endif; ?>

						<?php if ( !empty($job_form_page_id_url) ): ?>
							<li><a href="<?php echo esc_url($job_form_page_id_url) ?>"><span class="lnr icon-file-add"></span><?php echo get_the_title($job_form_page_id) ?></a></li>
						<?php endif; ?>
							<li><a href="/my-account/messages"><span class="lnr icon-message"></span>Mes messages</a></li>
							<li><a href="/my-account/bookings"><span class="lnr icon-bookings"></span>Mes réservations</a></li>
						<?php if ( $knowhere_settings['job-resume-post-form-url'] ): ?>
							<?php if ( !empty($resume_form_page_id_url) ): ?>
								<li><a href="<?php echo esc_url($resume_form_page_id_url) ?>"><span class="lnr icon-file-add"></span><?php echo get_the_title($resume_form_page_id) ?></a></li>
							<?php endif; ?>
						<?php endif; ?>

						<?php if ( !empty($job_bookmarks_url) ): ?>
							<li><a href="<?php echo esc_url($job_bookmarks_url) ?>"><span class="lnr icon-heart"></span><?php esc_html_e('Bookmarks', 'knowherepro') ?></a></li>
						<?php endif; ?>

						<?php if ( !empty($job_stats_url) ): ?>
							<li><a href="<?php echo esc_url($job_stats_url) ?>"><span class="lnr icon-graph"></span><?php esc_html_e('Statistics', 'knowherepro') ?></a></li>
						<?php endif; ?>

						<?php if ( !empty($edit_account_url) ): ?>
							<li><a href="<?php echo esc_url($edit_account_url) ?>"><span class="lnr icon-pencil"></span><?php esc_html_e('Edit Profile', 'knowherepro') ?></a></li>
						<?php endif; ?>


							<li><a href="/my-account/customer-logout"><span class="lnr icon-logout"></span>Se déconnecter</a></li>
						

					</ul>

				</div><!--/ .kw-dropdown -->

			</li>

		<?php else: ?>

			<li class="lwa"><a href="<?php echo esc_url(wp_login_url(get_permalink())) . '&modal_login=true#login'; ?>" class="<?php echo knowhere_get_login_link_class( 'kw-login' ); ?>"><?php echo esc_html__('Login', 'knowherepro') ?></a></li>

		<?php endif;

	}
}

if ( !function_exists('knowhere_listing_media_output') ) {
	function knowhere_listing_media_output( $args = array() ) {

		global $knowhere_settings;

		$image_size = '';

		$defaults = array(
			'post' => '',
			'image_size' => 'thumbnail'
		);

		$args = wp_parse_args( $args, $defaults ); extract($args);

		$company_archive_link = $upload_url = $company_logo = '';

		if ( class_exists( 'Astoundify_Job_Manager_Companies' ) && '' != knowhere_get_the_company_name() ) {
			$companies   = Astoundify_Job_Manager_Companies::instance();
			$company_url = esc_url( $companies->company_url( knowhere_get_the_company_name() ) );
			$company_archive_link = $company_url;
		}

		?>

		<?php if ( $knowhere_settings['job-type-fields'] == 'job' ): ?>

			<?php if ( knowhere_get_the_company_logo() ): ?>

				<div class="kw-listing-item-media kw-listing-style-4">
					<a href="<?php echo esc_url($company_archive_link); ?>" class="kw-listing-item-thumbnail">
						<?php knowhere_the_company_logo(); ?>
					</a>
				</div>

			<?php endif; ?>

		<?php else: ?>

			<?php $src = knowhere_get_post_image_src( $args['post']->ID, $image_size ); ?>

			<?php if ( $src ): ?>

				<div class="kw-listing-item-media kw-listing-style-4">
					<a href="<?php the_job_permalink(); ?>" class="kw-listing-item-thumbnail">
						<img src="<?php echo knowhere_get_post_image_src( $args['post']->ID, $image_size ); ?>" alt="">
					</a>
				</div>

			<?php endif; ?>

		<?php endif;

	}
}

if ( !function_exists('knowhere_employers_carousel') ) {
	function knowhere_employers_carousel()
	{

		global $knowhere_settings;

		$title = $knowhere_settings['job-title-job-front-page-carousel'];
		$number = $knowhere_settings['job-number-job-front-page-carousel'];
		$columns = $knowhere_settings['job-columns-job-front-page-carousel'];

		if ( !$number )  $number = 6;
		if ( !$columns ) $columns = 4;

		$employers = array();

		if ( class_exists( 'Astoundify_Job_Manager_Companies' ) ) {

			global $wpdb;

			$_companies   = $wpdb->get_col(
				"SELECT pm.meta_value FROM {$wpdb->postmeta} pm
				 LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				 WHERE pm.meta_key = '_company_name'
				 AND p.post_status = 'publish'
				 AND p.post_type = 'job_listing'
				 GROUP BY pm.meta_value
				 ORDER BY pm.meta_value"
			);

			$companies = Astoundify_Job_Manager_Companies::instance();

			$_companies = array_slice($_companies, 0, $number);

			if ( !empty($_companies) ) {
				foreach ( $_companies as $key => $company_name ) {

					if ( empty($company_name) ) continue;
					$employers[$key]['url'] = esc_url( $companies->company_url( $company_name ) );
					$employers[$key]['logo'] = knowhere_get_the_company_logo('thumbnail', '', get_post(knowhere_get_post_id_by_meta_key_and_value('_company_name', $company_name)));

					$employers[$key]['post_url'] = get_the_post_thumbnail_url(knowhere_get_post_id_by_meta_key_and_value('_company_name', $company_name));
					$employers[$key]['post_id'] = knowhere_get_post_id_by_meta_key_and_value('_company_name', $company_name);
					$employers[$key]['name'] = $company_name;
					$employers[$key]['count'] = count( get_posts( array( 'post_type' => 'job_listing', 'meta_key' => '_company_name', 'meta_value' => $company_name, 'nopaging' => true )) );
				}
			}

		}

		?>

		<?php if ( !empty($employers) ): ?>

		<div class="kw-section kw-fw kw-without-bottom-spacing">

			<h4><?php echo esc_html($title) ?></h4>

			<!-- - - - - - - - - - - - - - Employers - - - - - - - - - - - - - - - - -->

			<div class="kw-employers owl-carousel" data-columns="<?php echo absint($columns) ?>">

				<?php foreach ( $employers as $employer ): ?>

					<div class="kw-employer-wrap">

						<div class="kw-employer">

							<?php $company_logo = ''; ?>

							<?php if ( isset($employer['post_url']) && !empty($employer['post_url']) ): ?>

								<?php
								$attach_id = knowhere_get_attachment_id_from_url( $employer['post_url'] );

								if ( ! empty( $attach_id ) && is_numeric( $attach_id ) ) {
									$company_logo = wp_get_attachment_image_src( $attach_id, 'knowhere-thumbnail' );
								}

								if ( ! empty( $company_logo ) && ( strstr( $company_logo[0], 'http' ) || file_exists( $company_logo[0] ) ) ) {
									$company_logo = $company_logo[0];
									$company_logo = job_manager_get_resized_image( $company_logo, 'knowhere-thumbnail' );
								}

								?>

							<?php endif; ?>

							<div class="kw-employer-logo" style="background-image: url(<?php echo esc_url($company_logo) ?>)">

								<?php if ( isset($employer['url']) && !empty($employer['url']) ): ?>
									<a href="<?php echo esc_url($employer['url']); ?>" class="kw-clickbox"></a>
								<?php endif; ?>

							</div><!--/ .kw-employer-logo -->

							<div class="kw-employer-info">

								<h5 class="kw-employer-name">
									<?php if ( $employer['url'] ): ?><a target="_blank" href="<?php echo esc_url($employer['url']); ?>"><?php endif; ?>
										<?php echo sprintf('%s', $employer['name']) ?>
									<?php if ( $employer['url'] ): ?></a><?php endif; ?>
								</h5>

								<span class="kw-employer-stats">
									<?php $count = absint($employer['count']) ?>
									<?php printf(_n('%s live job', '%s live jobs', $count, 'knowherepro'), $count); ?>
								</span>

							</div><!--/ .kw-employer-info -->

						</div><!--/ .kw-employer -->

					</div><!--/ .kw-employer-wrap-->

				<?php endforeach; ?>

			</div>

		</div>

	<?php endif; ?>

		<?php
	}
}

/*  Get Blog ID
/* ---------------------------------------------------------------------- */

if ( ! function_exists('knowhere_get_blog_id') ) {
	function knowhere_get_blog_id()
	{
		return apply_filters( 'knowhere_get_blog_id', get_current_blog_id() );
	}
}

/*  Add bookmark heart to content
/* ---------------------------------------------------------------------- */

if ( !function_exists('knowhere_add_bookmark_heart_to_content')) {
	function knowhere_add_bookmark_heart_to_content( $post ) {
		global $job_manager_bookmarks;

		if ( $job_manager_bookmarks !== null && method_exists( $job_manager_bookmarks, 'is_bookmarked' ) ) { ?>

			<?php if (  $job_manager_bookmarks->is_bookmarked( $post->ID ) ): ?>

				<a href="<?php the_job_permalink(); ?>" class="kw-listing-action knowhere-is-bookmarked">
					<span class="lnr icon-heart"></span>
				</a>

			<?php else: ?>

				<a class="kw-listing-action" href="<?php the_job_permalink(); ?>">
					<span class="lnr icon-heart"></span>
				</a>

			<?php endif; ?>

		<?php }
	}
}

if ( !function_exists('knowhere_add_bookmark_heart_to_listing') ) {

	function knowhere_add_bookmark_heart_to_listing( $post ) {
		global $job_manager_bookmarks;

		if ( $job_manager_bookmarks !== null && method_exists( $job_manager_bookmarks, 'is_bookmarked' ) ) { ?>

			<?php if (  $job_manager_bookmarks->is_bookmarked( $post->ID ) ): ?>

				<li>
					<a href="<?php the_job_permalink(); ?>" class="kw-listing-item-like knowhere-is-bookmarked">
						<span class="fa fa-heart"></span>
					</a>
				</li>

			<?php else: ?>

				<li>
					<a href="<?php the_job_permalink(); ?>" class="kw-listing-item-like">
						<span class="lnr icon-heart"></span>
					</a>
				</li>

			<?php endif; ?>


		<?php }
	}

	add_action( 'knowhere_listing_card_meta_end', 'knowhere_add_bookmark_heart_to_listing' );

}

if ( ! function_exists( 'knowhere_job_listing_post_views' ) ) {

	function knowhere_job_listing_post_views( $post_id = 0, $echo = true ) {

		$post_id = (int) ( empty( $post_id ) ? get_the_ID() : $post_id );

		$views = knowhere_get_post_views( $post_id );

		$html = '<li class="post-views post-' . $post_id . '">
				<span class="lnr icon-eye"></span> ' . number_format_i18n( $views ) . '</li>';

		if ( $echo )
			echo $html;

		return $html;

	}

}

if ( ! function_exists( 'knowhere_get_post_views' ) ) {

	function knowhere_get_post_views( $post_id = 0 ) {
		if ( empty( $post_id ) )
			$post_id = get_the_ID();

		if ( is_array( $post_id ) )
			$post_id = implode( ',', array_map( 'intval', $post_id ) );
		else
			$post_id = (int) $post_id;

		global $wpdb;

		$query = "SELECT SUM(count) AS views
	    FROM " . $wpdb->prefix . "knowhere_job_listing_post_views
	    WHERE id IN (" . $post_id . ") AND type = 4";

		// get cached data
		$post_views = wp_cache_get( md5( $query ), 'knowhere-get_post_views' );
		
		// cached data not found?
		if ( $post_views === false ) {
			$post_views = (int) $wpdb->get_var( $query );

			// set the cache expiration, 5 minutes by default
			$expire = absint( apply_filters( 'knowhere_object_cache_expire', 5 * 60 ) );

			wp_cache_add( md5( $query ), $post_views, 'knowhere-get_post_views', $expire );
		}

		return apply_filters( 'knowhere_get_post_views', $post_views, $post_id );
	}

}

if ( ! function_exists( 'knowhere_get_random_object' ) ) {
	function knowhere_get_video( $post_id = null ) {

		if ( $post_id === null ) {
			$post_id = get_the_ID();
		}

		if ( !$post_id ) return '';

		$video_background = get_post_meta( $post_id, 'knowhere_page_add_video', true );

		if ( !empty($video_background) ) {
			return $video_background;
		}

		return '';
	}
}

if ( !function_exists('knowhere_display_frontpage_listing_categories') ) {
	function knowhere_display_frontpage_listing_categories( $default_count = 5 ) {

		$term_list = array();

		$query_args = array(
			'orderby' => 'count',
			'order' => 'DESC',
			'hide_empty' => false,
			'hierarchical' => true,
			'pad_counts' => true
		);

		$all_terms = get_terms(
			'job_listing_category',
			$query_args
		);

		if ( is_wp_error( $all_terms ) ) { return; }

		$all_categories = array();
		foreach ( $all_terms as $key => $term ) {
			$all_categories[ $term->slug ] = $term;
		}

		$categories = get_post_meta( get_the_ID(), 'knowhere_frontpage_listing_categories', true );
		$custom_category_labels = array();

		if ( ! empty( $categories ) ) {
			$categories = explode( ',', $categories );
			foreach ( $categories as $key => $category ) {
				if ( strpos( $category, '(' ) !== false ) {
					$category  = explode( '(', $category );
					$term_slug = trim( $category[0] );

					if ( substr( $category[1], - 1, 1 ) == ')' ) {
						$custom_category_labels[ $term_slug ] = trim( substr( $category[1], 0, - 1 ) );
					}

					if ( array_key_exists( $term_slug, $all_categories ) ) {
						$term_list[] = $all_categories[ $term_slug ];
					}
				} else {
					$term_slug = trim( $category );

					if ( array_key_exists( $term_slug, $all_categories ) ) {
						$term_list[] = $all_categories[ $term_slug ];
					}
				}
			}
		} else {
			$term_list = array_slice( $all_categories, 0, $default_count);
		}

		if ( $term_list ) {
			echo '<h4 class="kw-cta-text">' . esc_html__( 'Or browse the highlights picked by us', 'knowherepro' ) . '</h4>';
		}

		foreach ( $term_list as $key => $term ) :
			if ( ! $term || ( is_array( $term ) && isset( $term['invalid_taxonomy'] ) ) ) {
				continue;
			} ?>

			<div class="kw-cat-item">

				<a href="<?php echo esc_url( get_term_link( $term ) ); ?>">

					<?php
					$url = knowhere_get_term_icon_url( $term->term_id );
					$attachment_id = knowhere_get_term_icon_id( $term->term_id );
					if ( ! empty( $url ) ) : ?>
						<span class="kw-category-item-icon">
							<?php knowhere_display_icon_or_image( $url, '', true, $attachment_id ); ?>
						</span>
					<?php endif; ?>

					<span class="kw-cat-text"><?php echo isset( $custom_category_labels[ $term->slug ] ) ? $custom_category_labels[ $term->slug ] : $term->name; ?></span>

				</a>

			</div><!--/ .kw-front-category-item-->

		<?php endforeach;

	}
}

if ( !function_exists('knowhere_frontpage_page_header_bg') ) {
	function knowhere_frontpage_page_header_bg() {

		global $wp_embed;
		$has_image_url = false;
		$video_w = 1200;
		$video_h = $video_w / 1.61;
		$embed = $output = $has_video_class = $link = '';
		$link = knowhere_get_video();

		if ( !empty($link) ) {

			if ( strpos($link, 'youtube') > 0 ) {
				parse_str( parse_url( $link, PHP_URL_QUERY ), $my_array_of_vars );
				wp_enqueue_script( 'kw_youtube_iframe_api_js' );
				$embed .= "<div data-youtube-video-id='". esc_attr($my_array_of_vars['v']) ."'></div>";
			} else {
				if ( is_object( $wp_embed ) ) {
					$embed .= $wp_embed->run_shortcode( '[embed width="' . $video_w . '"' . $video_h . ']' . $link . '[/embed]' );
				}
			}

			if ( has_shortcode($embed, 'video') ) {
				$embed = wp_video_shortcode(array(
					'src' => $link
				));
				$has_video_class = 'has-video-shortcode';
			}

			if ( !empty($embed) ) {
				$output .= '<div class="kw-page-entry-featured '. $has_video_class .'"><div class="kw-video-bg">'. $embed .'</div></div>';
				echo $output;
			}

			if ( strpos($link, 'vimeo') > 0 ): ?>

				<script src="https://player.vimeo.com/api/player.js"></script>
				<script>
					var kw_vimeo_iframe = document.querySelector('iframe');

					var kw_options = {
						loop: true
					};

					var kw_vimeo_player = new Vimeo.Player(kw_vimeo_iframe, kw_options);

					kw_vimeo_player.setVolume(0);

					if ( kw_vimeo_player ) { kw_vimeo_player.play(); }

				</script>

			<?php endif;

		} else {

			if ( has_post_thumbnail() ) {
				$has_image_url = get_the_post_thumbnail_url();
			}
			?>

			<div class="kw-page-header-media"<?php if ( ! empty( $has_image_url ) ) {
				echo ' style="background-image: url(' . esc_url($has_image_url) . ');"';
			} ?>></div><!--/ .kw-page-header-media-->

			<?php
		}
	}

}
