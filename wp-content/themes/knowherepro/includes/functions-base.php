<?php

/*  Base Function Class
/* ---------------------------------------------------------------------- */

if (!class_exists('Knowhere_Base')) {

	class Knowhere_Base {

		public $action_search = 'knowhere_action_search';
		public $action_post_share = 'knowhere_action_post_share';
		public $paths = array();
		public $directory_uri;
		private static $_instance;
		protected $used_fonts = array();
		protected $fontlist = array();

		/* 	Instance
		/* ---------------------------------------------------------------------- */

		public static function getInstance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		function __construct() {

			$this->directory_uri = get_theme_file_uri('css');

			add_action( 'template_redirect', array($this, 'predefined_config'), 1 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles_scripts' ), 100 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );
			add_filter( 'body_class', array($this, 'body_class'), 5 );

			new knowhere_admin_user_profile();

			/*  Load Textdomain
			/* --------------------------------------------- */
			$this->load_textdomain();

		}

		/* 	Initialization
		/* ---------------------------------------------------------------------- */

		function body_class($classes) {
			global $post, $knowhere_config;

			if ( isset($knowhere_config['header_type']) ) {
				$classes[] = 'kw-header-' . str_replace('kw-', '', $knowhere_config['header_type']);
			}

			$classes[] = $knowhere_config['sidebar_position'];

			return $classes;
		}

		public function admin_enqueue_styles_scripts() {
			$this->admin_enqueue_styles();
			$this->admin_enqueue_scripts();
		}

		public function enqueue_styles_scripts() {

			global $knowhere_settings;

			/* Vendor CSS */
			wp_enqueue_style( 'owl-carousel', get_theme_file_uri('js/libs/owl-carousel/assets/owl.carousel.min.css') );
			wp_enqueue_style( 'magnific-popup', get_theme_file_uri('js/libs/magnific/magnific-popup.css') );

			/* Theme CSS */
			wp_enqueue_style( 'linearicons', get_theme_file_uri('css/icon-font.min.css'), array(), null );
			wp_enqueue_style( 'animate', get_theme_file_uri('css/animate.css'), array(), null );
			wp_enqueue_style( 'bootstrap', get_theme_file_uri('css/bootstrap.min.css'), array(), null );
			wp_enqueue_style( 'linear', get_theme_file_uri('css/linear-icons.css'), array(), null );
			wp_enqueue_style( 'fontawesome', get_theme_file_uri('css/font-awesome.min.css'), array(), null );

			wp_enqueue_style( 'knowhere-style', get_stylesheet_uri(), array(), null );

			if ( class_exists('WooCommerce') ) {
				wp_enqueue_style( 'knowhere-woocommerce-mod', get_theme_file_uri('config-woocommerce/assets/css/woocommerce-mod' . (WP_DEBUG ? '' : '.min') . '.css') );
			}

			if ( class_exists('WP_Job_Manager') ) {
				wp_enqueue_style('knowhere-job-manager-mod', get_theme_file_uri('config-job-manager/assets/css/job-manager-mod' . (WP_DEBUG ? '' : '.min') . '.css'));
				wp_enqueue_style('jquery-ui', get_theme_file_uri('js/libs/jquery-ui/jquery-ui.min.css'));
			}

			// Skin Styles
			wp_deregister_style( 'knowhere-skin' );
			$prefix_name = 'skin_' . knowhere_get_blog_id() . '.css';
			$wp_upload_dir = wp_upload_dir();
			$stylesheet_dynamic_dir = $wp_upload_dir['basedir'] . '/dynamic_knowhere_dir';
			$stylesheet_dynamic_dir = str_replace('\\', '/', $stylesheet_dynamic_dir);
			$filename = trailingslashit($stylesheet_dynamic_dir) . $prefix_name;

			$version = get_option( 'knowhere_stylesheet_version' . $prefix_name );
			if ( empty($version) ) $version = '1';

			$demo = get_option( 'knowhere_demo' );
			if ( empty($demo) ) $demo = 'wbc-import-1';

			if ( file_exists($filename) ) {
				if ( is_ssl() ) {
					$wp_upload_dir['baseurl'] = str_replace("http://", "https://", $wp_upload_dir['baseurl']);
				}
				wp_register_style( 'knowhere-skin', $wp_upload_dir['baseurl'] . '/dynamic_knowhere_dir/' . $prefix_name, null, $version );
			} else {
				wp_register_style( 'knowhere-skin', get_theme_file_uri( "css/skin-{$demo}.css" ), null, $version );
			}
			wp_enqueue_style( 'knowhere-skin' );

			if ( is_rtl() ) {
				wp_enqueue_style( 'knowhere-rtl',  get_theme_file_uri( 'css/rtl.css' ), array( 'knowhere-style', 'knowhere-woocommerce-mod' ), '1', 'all' );
			}

			wp_enqueue_style( 'knowhere-layout', get_theme_file_uri( 'css/layout.css' ) , array(), null );

			// Load Google Fonts
			$google_fonts = array();
			$fonts = array( 'body', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'menu', 'sub-menu', 'vr-menu', 'vr-sub-menu' );
			foreach ( $fonts as $option ) {
				if ( isset($knowhere_settings[$option.'-font']['google']) && $knowhere_settings[$option.'-font']['google'] !== 'false' ) {
					$font = $knowhere_settings[$option.'-font']['font-family'];
					if ( !in_array($font, $google_fonts) ) {
						$google_fonts[] = $font;
					}
				}
			}

			$font_family = array();
			foreach ( $google_fonts as $font ) {

				/*
				Translators: If there are characters in your language that are not supported
				by chosen font(s), translate this to 'off'. Do not translate into your own language.
				 */
				$f = sprintf( _x( 'on', '%s font: on or off', 'knowherepro' ), $font );

				if ( 'off' !== $f ) {
					$font_family[] .= $font . ':300,300italic,400,400italic,500,600,600italic,700,700italic,800,800italic%7C';
				}

			}

			if ( $font_family ) {
				$charsets = '';

				if ( isset($knowhere_settings['select-google-charset']) && $knowhere_settings['select-google-charset'] && isset($knowhere_settings['google-charsets']) && $knowhere_settings['google-charsets']) {
					$i = 0;
					foreach ( $knowhere_settings['google-charsets'] as $charset ) {
						if ( $i == 0 ) {
							$charsets .= $charset;
						} else {
							$charsets .= ",".$charset;
						}
						$i++;
					}
				}

				$fonts_url = add_query_arg( array(
					'family' => urlencode( implode('|', $font_family) ),
					'subset' => urlencode( $charsets )
				), '//fonts.googleapis.com/css' );

				wp_enqueue_style( 'knowhere-google-fonts', esc_url_raw($fonts_url) . $charsets );
			}

			// Enqueue scripts

			if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
				wp_enqueue_script( 'comment-reply' );
			}

			$scripts_deps = array('jquery');

			// Google Maps.
			wp_register_script( 'google-maps', knowhere_get_google_maps_api_url(), array(), '3.exp' );

			/* Include Libs & Plugins */
			wp_enqueue_script( 'modernizr', get_theme_file_uri('js/libs/modernizr.min.js') );
			wp_enqueue_script( 'magnific-popup', get_theme_file_uri('js/libs/magnific/jquery.magnific-popup.min.js'), array('jquery'), '', true);
			wp_enqueue_script( 'owl-carousel', get_theme_file_uri('js/libs/owl-carousel/owl.carousel.min.js'), array('jquery'), '', true);
			wp_enqueue_script( 'isotope', get_theme_file_uri('js/libs/isotope.pkgd.min.js'), array('jquery'), '', true);

			if ( class_exists( 'WP_Job_Manager' ) ) {

				global $post;
				if ( ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'jobs' ) && true === knowhere_jobs_shortcode_get_show_map_param( $post->post_content ) )
					|| ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'vc_mad_listing_map' ) )
					|| ( is_single() && 'job_listing' == $post->post_type ) || is_search()
					|| ( isset( $post->post_content ) && is_archive() && 'job_listing' == $post->post_type )
					|| is_tax( array( 'job_listing_category', 'job_listing_tag', 'job_listing_region' ) )
					|| ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'submit_job_form' ) )
				) {
					wp_enqueue_style( 'leaflet-css', get_theme_file_uri('js/libs/leaflet/leaflet.css') );
					wp_enqueue_script( 'leaflet', get_theme_file_uri('js/libs/leaflet/leaflet.js'), array( 'jquery', 'wp-util' ), '1.0.3', true );
					$scripts_deps[] = 'leaflet';
				}

			}

			$scripts_deps[] = 'google-maps';

			/* Theme files */
			wp_enqueue_script( 'knowhere-plugins', get_theme_file_uri('js/knowhere.plugins' . ( WP_DEBUG ? '' : '.min' ) . '.js'), array('jquery'), '', true );
			wp_enqueue_script( 'knowhere-core', get_theme_file_uri('js/knowhere.core' . ( WP_DEBUG ? '' : '.min' ) . '.js'), $scripts_deps, '', true );

			if ( isset($knowhere_settings['js-code-head']) && $knowhere_settings['js-code-head']) {
				wp_add_inline_script( 'knowhere-core', $knowhere_settings['js-code-head'] );
			}

			wp_register_script( 'kw_youtube_iframe_api_js', '//www.youtube.com/iframe_api', array(), null, false );

			wp_localize_script('jquery', 'knowhere_global_vars', array(
				'template_base_uri' => get_template_directory_uri() . '/',
				'sticky' => $knowhere_settings['header-sticky-menu'],
				'ajax_nonce' => wp_create_nonce('ajax-nonce'),
				'ajaxurl' => admin_url('admin-ajax.php'),
				'ajax_loader_url' => get_template_directory_uri() . '/images/ajax-loader.gif',
				'rtl' => is_rtl() ? 1 : 0,
				'strings' => array(
					'results' => esc_html__( 'results found', 'knowherepro')
				)
			));
		}

		/* 	Enqueue Admin Styles
		/* ---------------------------------------------------------------------- */

		public function admin_enqueue_styles() {
			wp_enqueue_style( 'knowhere_admin', $this->directory_uri . '/admin.css', false);
		}

		/*  Enqueue Admin Scripts
		/* ---------------------------------------------------------------------- */

		public function admin_enqueue_scripts() {
			if ( function_exists('add_thickbox') )
				add_thickbox();

			wp_enqueue_media();
			wp_enqueue_script( 'knowhere_admin', get_theme_file_uri('js/admin.js') );
		}

		/* 	Load Textdomain
		/* ---------------------------------------------------------------------- */

		public function load_textdomain () {
			load_theme_textdomain( 'knowherepro', get_template_directory()  . '/lang' );
		}

		/*	Check page layout
		/* ---------------------------------------------------------------------- */

		public function check_page_layout () {
			global $knowhere_config, $knowhere_settings;

			$result = false;
			$sidebar_position = 'page-layout';

			$post_id = knowhere_post_id();

			if ( is_page() ) { $sidebar_position = 'page-layout'; }

			if ( is_archive() || is_front_page() || is_search() || is_attachment() ) { $sidebar_position = 'post-archive-layout'; }

			if ( is_single() ) { $sidebar_position = 'post-single-layout'; }

			if ( is_singular() ) {
				$result = trim(get_post_meta( $post_id, 'knowhere_page_sidebar_position', true ));
			}

			if ( is_singular('knowhere_agent') ) {
				$sidebar_position = 'agent-archive-layout';
			}

			if ( is_singular('knowhere_agency') ) {
				$sidebar_position = 'agency-archive-layout';
			}

			if ( class_exists( 'WP_Job_Manager' ) ) {

				if ( knowhere_is_realy_job_manager_tax() || is_post_type_archive('job_listing') || knowhere_job_listing_has_shortcode_jobs() ) {

					if ( knowhere_is_realy_job_manager_tax()  ) {

						$result = knowhere_job_get_term('pix_term_page_layout');

						if ( empty($result) ) {
							$sidebar_position = 'job-category-layout';
						}

					} elseif ( is_post_type_archive('job_listing') || knowhere_job_listing_has_shortcode_jobs() ) {
						$sidebar_position = 'job-category-layout';
					}

				}

				if ( knowhere_is_realy_job_manager_single() ) {
					$sidebar_position = 'job-single-layout';
				}

				if ( knowhere_is_realy_job_manager_submit_job_form() ) {
					$result = 'kw-no-sidebar';
				}

				if ( is_singular('resume') ) {
					$sidebar_position = 'job-resume-layout';
				}

			}

			if ( is_404() ) { $result = 'kw-no-sidebar'; }

			if ( knowhere_is_shop_installed() ) {

				if ( knowhere_is_realy_woocommerce_page(false) || knowhere_is_shop() || knowhere_is_product_category() || knowhere_is_product_tax() ) {

					if ( knowhere_is_realy_woocommerce_page(false) ) {

						$result = 'kw-no-sidebar';

					} elseif ( knowhere_is_product_category() ) {

						$result = knowhere_get_meta_value('sidebar_position');

						if ( empty($result) ) {
							$result = $knowhere_settings['product-archive-layout'];
						}

					} else {
						$result = $knowhere_settings['product-archive-layout'];
					}
				}

				if ( knowhere_is_product() ) {
					$result_sidebar_position = trim(get_post_meta( $post_id, 'knowhere_page_sidebar_position', true ));

					if ( empty($result_sidebar_position) ) {
						$result = $knowhere_settings['product-single-layout'];
					} else {
						$result = $result_sidebar_position;
					}
				}

			}

			if ( !$result ) {
				$result = $knowhere_settings[$sidebar_position];
			}

			if ( !$result ) {
				$result = 'kw-no-sidebar';
			}

			if ( $result ) {
				$knowhere_config['sidebar_position'] = $result;
			}

		}

		public function check_header_classes() {
			global $knowhere_config, $knowhere_settings;

			$result = array();
			$post_id = knowhere_post_id();

			$header_type = $knowhere_settings['header-type'];

			$knowhere_config['term_header'] = '';
			$knowhere_config['job-single-style'] = $knowhere_settings['job-single-style'];

			if ( knowhere_is_realy_job_manager_tax() || is_post_type_archive('job_listing') || is_singular('job_listing') || knowhere_job_listing_has_shortcode_jobs() ) {
				$header_type = $knowhere_settings['listing-header-type'];
			}

			if ( knowhere_is_product() || knowhere_is_product_category() || knowhere_is_product_tag() || is_tax( get_object_taxonomies( 'product' ) ) || is_post_type_archive('product') ) {
				$header_type = $knowhere_settings['product-header-type'];
			}

			$meta_header_type = trim(get_post_meta( $post_id, 'knowhere_header_type', true ));

			if ( $meta_header_type ) { $header_type = $meta_header_type; }

			$result['header_type'] = $header_type;

			switch ( $header_type ) {
				case 'kw-type-1':
					$result[] = 'kw-dark';
					break;
				case 'kw-type-2':
					$result[] = 'kw-dark';
					$result[] = 'kw-translucent';
					break;
				case 'kw-type-3':
					$result[] = 'kw-light';
					break;
				case 'kw-type-4':
					$result[] = 'kw-dark';
					$result[] = 'kw-theme-color';
					break;
				case 'kw-type-5':
					$result[] = 'kw-light';
					break;
				case 'kw-type-6':
					$result[] = '';
					break;
			}

			if ( is_singular('job_listing') || knowhere_is_realy_job_manager_submit_job_form() || knowhere_is_realy_job_manager_submit_resume_form() || knowhere_is_realy_resume_manager_page() ) {

				$style = get_post_meta( $post_id, 'knowhere_job_style_single_page', true );

				if ( empty($style) || $style == ' ' ) {
					$style = $knowhere_settings['job-single-style'];
				}

				if ( knowhere_is_realy_job_manager_submit_job_form() || knowhere_is_realy_job_manager_submit_resume_form() || knowhere_is_realy_resume_manager_page() ) {
					if ( $header_type == 'kw-type-2' ) {
						$result[] = 'kw-transparent';
					}
				}

				if ( $style ) {
					$knowhere_config['job-single-style'] = $style;
				}

			} elseif ( knowhere_is_realy_job_manager_tax() || is_post_type_archive('job_listing') || knowhere_job_listing_has_shortcode_jobs() ) {

				$term_header = knowhere_job_get_term('pix_term_header');

				if ( empty($term_header) ) {
					$term_header = $knowhere_settings['job-category-header'];
				}

				switch ( $term_header ) {
					case 'kw-theme-color':

						$result[] = 'kw-theme-color';

						if ( in_array( 'kw-light', $result ) ) {
							array_pop($result);
						}

						break;
					case 'kw-dark':
						$result[] = 'kw-dark';
						break;
					case 'kw-light':

						if ( in_array( 'kw-dark', $result ) ) {
							array_pop($result);
						}

						$result[] = 'kw-light';
						break;
				}

				if ( knowhere_is_realy_job_manager_tax() || knowhere_job_listing_has_shortcode_jobs() || knowhere_listings_page_shortcode_get_show_map_param()  ) {

					if ( knowhere_listings_page_shortcode_get_show_map_param() ) {
						$result[] = 'kw-sticked';
					}

				}

				$knowhere_config['term_header'] = $term_header;

			} elseif ( is_page() || is_singular('post') || knowhere_is_realy_woocommerce_page() ) {

				if ( knowhere_job_listing_has_shortcode_companies() ) {
					if ( $header_type == 'kw-type-2' ) {

						$url = $knowhere_settings['header-type-2-bg']['background-image'];

						if ( !empty($url) ) {
							$result[] = 'kw-transparent';
							$result[] = 'kw-has-image';
						}
					}
				}

				$upload_id = knowhere_page_title_get_value('upload');
				$link = knowhere_get_video();

				if ( !empty($upload_id) || $post_id > 0 && has_post_thumbnail($post_id) || !empty($link) ) {
					$result[] = 'kw-transparent';
					$result[] = 'kw-has-image';
				}

			} elseif ( is_archive() ) {

				if ( $header_type == 'kw-type-2' ) {

					$url = $knowhere_settings['header-type-2-bg']['background-image'];

					if ( !empty($url) ) {
						$result[] = 'kw-transparent';
						$result[] = 'kw-has-image';
					}
				}

			}

			$result = array_unique($result);

			$knowhere_config['header_classes'] = implode( ' ', array_values($result) );
			$knowhere_config['header_type'] = $result['header_type'];
		}

		public function check_footer_classes() {
			global $knowhere_config, $knowhere_settings;
			$classes = array();

			$image = str_replace(array('http://', 'https://'), array('//', '//'), $knowhere_settings['footer-bg']['background-image']);
			$image = trim($image);

			if ( !empty($image) && get_post_meta( knowhere_post_id(), 'knowhere_footer_hidden_bg_image', true ) != 1 ) {
				$classes[] = 'kw-has-bg-image';
			}

			$knowhere_config['footer_classes'] = implode( ' ', array_values($classes) );
		}

		public function check_page_content_classes() {
			global $knowhere_config;

			$result = array();
			$result[] = 'kw-page-content';
			$result[] = $knowhere_config['sidebar_position'];

			$knowhere_config['page_content_classes'] = implode( ' ', array_filter(array_values($result)) );
		}

		public function predefined_config() {
			$this->check_page_layout();
			$this->check_header_classes();
			$this->check_footer_classes();
			$this->check_page_content_classes();
		}

		/* 	Instance
		/* ---------------------------------------------------------------------- */

		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

	}

	if ( ! function_exists('knowhere_base') ) {

		function knowhere_base() {
			// Load required classes and functions
			return Knowhere_Base::getInstance();
		}

		knowhere_base();

	}

}