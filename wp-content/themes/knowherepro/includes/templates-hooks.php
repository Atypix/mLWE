<?php

if (!class_exists('Knowhere_Hooks')) {

	class Knowhere_Hooks {

		function __construct() {
			add_action('init', array($this, 'init'), 11);
			add_action('wp_loaded', array($this, 'wp_loaded'));
		}

		public function init() {

			global $knowhere_settings;

			if ( $knowhere_settings['job-how-it-works'] && absint($knowhere_settings['job-how-it-works']) ) {
				add_action( 'knowhere_body_append', array( $this, 'how_it_works' ) );
			}

		}

		public function wp_loaded() {
			$this->add_hooks();
		}

		public function add_hooks() {

			global $knowhere_settings, $post;

			add_action('knowhere_header_layout', array($this, 'template_header_layout_hook'));

//			if ( $knowhere_settings['header-type-1-show-search'] || $knowhere_settings['header-type-3-show-search'] ) {
				add_action('knowhere_body_append', array( $this, 'search_box' ));
//			}

			add_action( 'knowhere_header_after', array( $this, 'header_after_hook' ) );
			add_action( 'knowhere_footer_in_top_part', array( $this, 'template_footer_newsletter' ), 9 );
			add_action( 'knowhere_footer_in_top_part', array( $this, 'template_footer_widgets' ) );
		}

		public function template_header_layout_hook($type) {
			get_template_part( 'template-parts/header', $type );
		}

		public function template_footer_newsletter() {
			get_template_part( 'template-parts/footer', 'newsletter' );
		}

		public function template_footer_widgets() {
			get_template_part( 'template-parts/footer', 'widgets' );
		}

		public function header_after_hook() {
			$this->page_title();
		}

		public function page_title() {
			global $knowhere_settings, $knowhere_config;

			$mode = knowhere_page_title_get_value('mode');
			$job_id = ! empty( $_REQUEST[ 'job_id' ] ) ? absint( $_REQUEST[ 'job_id' ] ) : 0;

			if (
				is_404() ||
				is_search() ||
				is_front_page() ||
				is_singular('resume') ||
				is_singular('testimonials') ||
				is_singular('knowhere_agent') ||
				is_singular('knowhere_agency') ||
				is_post_type_archive('job_listing') ||
				get_query_var('company') ||
				knowhere_is_realy_job_manager_page() ||
				knowhere_job_listing_has_shortcode_jobs() ||
				$mode == 'none'
//				$job_id
			) return;

			$wrapper_attributes = array();
			$css_classes = array( 'kw-page-header', 'kw-dark' );

			if ( knowhere_post_id() ) {
				if ( get_post_meta( knowhere_post_id(), '_thumbnail_id', true ) ) {
					$css_classes[] = 'kw-transparent';
					$css_classes[] = 'kw-has-image';
				}
			}

			switch ( $mode ) {
				case 'default':
					$breadcrumb = $knowhere_settings['show-breadcrumbs'];
					$align = $knowhere_settings['align-title-and-breadcrumbs'];

					$url = $knowhere_settings['page-header-upload']['url'];

					if ( !empty($url) ) {
						$css_classes[] = 'kw-transparent';
						$css_classes[] = 'kw-has-image';
					}

					break;
				case 'custom':
					$breadcrumb = knowhere_page_title_get_value('breadcrumb');
					$upload_id = knowhere_page_title_get_value('upload');
					$align = knowhere_page_title_get_value('align');

					if ( $upload_id && absint($upload_id) ) {
						$css_classes[] = 'kw-transparent';
						$css_classes[] = 'kw-has-image';
					}

					break;
				default:
					$breadcrumb = $knowhere_settings['show-breadcrumbs'];
					$align = $knowhere_settings['align-title-and-breadcrumbs'];
					break;
			}

			if ( is_archive() ) {

				$header_type = $knowhere_config['header_type'];

				if ( $header_type == 'kw-type-2' ) {
					$url = $knowhere_settings['header-type-2-bg']['background-image'];
					if ( !empty($url) ) {
						$css_classes[] = 'kw-transparent';
						$css_classes[] = 'kw-has-image';
					}
				}

			}

			$css_class = preg_replace( '/\s+/', ' ', implode( ' ', array_unique(array_filter( $css_classes )) ) );
			$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';

			?>

			<div <?php echo implode( ' ', $wrapper_attributes ) ?>>

				<div class="kw-page-header-content <?php echo sanitize_html_class($align) ?>">

					<div class="container">

						<?php if ( is_page() ): ?>

							<?php if ( $knowhere_settings['show-pagetitle'] ): ?>
								<?php echo knowhere_title(); ?>
							<?php endif; ?>

							<?php if ( $breadcrumb ): ?>

								<nav class="kw-breadcrumb">
									<?php echo knowhere_breadcrumbs(array(
										'separator' => '/'
									)); ?>
								</nav>

							<?php endif; ?>

						<?php elseif ( knowhere_is_realy_woocommerce_page() ): ?>

							<?php
							if ( in_array('categories', $knowhere_settings['product-metas']) ) {
								echo wc_get_product_category_list( get_the_ID(), ', ', '<div class="kw-product-categories-list">', '</div>' );
							}
							?>

							<?php if ( in_array('pagetitle', $knowhere_settings['product-metas']) ) : ?>
								<?php echo knowhere_title(); ?>
							<?php endif; ?>

							<?php if ( in_array('breadcrumbs', $knowhere_settings['product-metas']) ) : ?>

								<?php woocommerce_breadcrumb(array(
									'wrap_before' => '<nav class="kw-breadcrumb" ' . ( is_single() ? 'itemprop="breadcrumb"' : '' ) . '>',
									'wrap_after'  => '</nav>',
								)); ?>

							<?php endif; ?>

						<?php elseif ( is_single() ): ?>

							<?php if ( in_array('categories', $knowhere_settings['single-post-metas']) ): ?>

								<?php $categories = get_the_category_list('', ''); ?>
								<?php if ( !empty($categories) ): ?>
									<?php echo get_the_category_list('', '') ?>
								<?php endif; ?>

							<?php endif; ?>

							<?php echo knowhere_title(); ?>

							<div class="kw-entry-meta">

								<?php if ( in_array('date', $knowhere_settings['single-post-metas']) ): ?>

									<?php if ( $knowhere_config['sidebar_position'] == 'kw-no-sidebar' ): ?>

										<?php
										printf( '<time datetime="%1$s">%2$s</time>, %3$s <a href="%4$s">%5$s</a>',
											esc_attr( get_the_date( 'c' ) ),
											esc_attr( get_the_date( 'F j, Y' ) ),
											esc_html__('by', 'knowherepro'),
											esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
											get_the_author()
										);
										?>

									<?php else: ?>

										<?php
										printf( '<time datetime="%1$s">%2$s</time>',
											esc_attr( get_the_date( 'c' ) ),
											esc_attr( get_the_date( 'F j, Y' ) )
										);
										?>

									<?php endif; ?>

								<?php endif; ?>

								<?php if ( in_array('comments', $knowhere_settings['single-post-metas']) ): ?>
									<?php if ( comments_open() ): ?>
										<?php comments_popup_link(esc_html__('0 Comments', 'knowherepro'), esc_html__('1 Comment', 'knowherepro'), '% '.esc_html__('Comments', 'knowherepro')); ?>
									<?php endif; ?>
								<?php endif; ?>

							</div><!--/ .kw-entry-meta-->

							<?php if ( $knowhere_settings['post-breadcrumbs'] ): ?>

								<?php if ( $breadcrumb ): ?>

									<nav class="kw-breadcrumb">
										<?php echo knowhere_breadcrumbs(array(
											'separator' => '/'
										)); ?>
									</nav>

								<?php endif; ?>

							<?php endif; ?>

						<?php elseif ( is_search() ): global $wp_query; ?>

							<?php if ( !empty($wp_query->found_posts) ): ?>

								<?php if ($wp_query->found_posts > 1): ?>

									<?php
									echo knowhere_title(
										array(
											'title' => esc_html__('Search results for:', 'knowherepro')." " . esc_attr(get_search_query()) . " (". $wp_query->found_posts .")"
										)
									); ?>

								<?php else: ?>

									<?php
									echo knowhere_title(
										array(
											'title' => esc_html__('Search result for:', 'knowherepro')." " . get_search_query() . " (". $wp_query->found_posts .")"
										)
									); ?>

								<?php endif; ?>

							<?php else: ?>

								<?php if ( !empty($_GET['s']) ): ?>

									<?php
									echo knowhere_title(
										array(
											'title' => esc_html__('Search results for:', 'knowherepro') . " " . get_search_query()
										)
									); ?>

								<?php else: ?>

									<?php
									echo knowhere_title(
										array(
											'title' => esc_html__('To search the site please enter a valid term', 'knowherepro')
										)
									); ?>

								<?php endif; ?>

							<?php endif; ?>

						<?php else: ?>

							<?php
							echo knowhere_title(
								array(
									'title' => get_the_archive_title(),
									'subtitle' => get_the_archive_description()
								)
							); ?>

							<?php if ( $breadcrumb ): ?>

								<nav class="kw-breadcrumb">
									<?php echo knowhere_breadcrumbs(array(
										'separator' => '/'
									)); ?>
								</nav>

							<?php endif; ?>

						<?php endif; ?>

					</div><!--/ .container -->

				</div><!--/ .kw-page-header-content -->

				<div <?php echo Knowhere_Page_Title_Config::output_attributes(); ?>></div>

			</div>
			<?php
		}

		public function search_box() { ?>
			<div class="kw-hidden-search-box" id="search-box">

				<!-- - - - - - - - - - - - - - Search Form - - - - - - - - - - - - - - - - -->

				<?php echo get_search_form() ?>

				<!-- - - - - - - - - - - - - - End of Search Form - - - - - - - - - - - - - - - - -->

			</div><!--/ .kw-hidden-search-box1-->
			<?php
		}

		public function how_it_works( $id ) {
			global $knowhere_settings;

			if ( !$id ) return;

			$page_template = get_page_template_slug( $id );

			if ( 'template-parts/front-page.php' == $page_template ) :

				$post_id = $knowhere_settings['job-how-it-works'];
				$content = apply_filters('the_content', get_post_field('post_content', $post_id));
				if ( !$content ) return; ?>

				<div class="kw-hidden-element kw-hidden-how-it-works" data-lock-body="true" data-animate-in="slideInDown" data-animate-out="slideOutUp" id="how-it-works">

					<span class="lnr icon-cross kw-hidden-element-close" data-hidden-container="#how-it-works"></span>

					<div class="container">
						<section class="kw-section kw-large">
							<?php echo do_shortcode( $content ); ?>
						</section><!--/ .kw-section -->
					</div><!--/ .container -->

				</div>

			<?php endif;

		}

		/* 	Get Cookie
		/* ---------------------------------------------------------------------- */

		public static function getcookie( $name ) {
			if ( isset( $_COOKIE[$name] ) )
				return maybe_unserialize( stripslashes( $_COOKIE[$name] ) );

			return array();
		}

	}

	new Knowhere_Hooks();
}
