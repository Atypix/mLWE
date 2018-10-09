<?php

if ( !class_exists('Knowhere_Page_Title_Config') ) {

	class Knowhere_Page_Title_Config {

		public $paths = array();
		public $action_page_title = 'knowhere_page_title';

		public function path($name, $file = '') {
			return $this->paths[$name] . (strlen($file) > 0 ? '/' . preg_replace('/^\//', '', $file) : '');
		}

		public function assetUrl($file)  {
			return $this->paths['BASE_URI'] . $file;
		}

		function __construct () {

			$dir = get_template_directory() . '/includes/page-title/';

			$this->paths = array(
				'VIEW_PATH' => $dir . 'views',
				'PHP_PATH' => $dir . 'php',
				'BASE_URI' => get_template_directory_uri() . '/includes/page-title/'
			);

			require_once( $this->paths['PHP_PATH'] . '/functions-types.php' );

			$this->init();
		}

		public function init() {
			add_action( 'add_meta_boxes', array($this, 'add_meta_box') );
			add_action( 'save_post', array($this, 'save_perm_metadata') );
			add_action( 'admin_enqueue_scripts', array($this, 'enqueue_scripts_and_styles') );

			add_action( 'wp_ajax_' . $this->action_page_title, array($this, 'inserted_media_to_page_title') );
		}

		public function enqueue_scripts_and_styles() {
			$css_file = $this->assetUrl('css/page-title.css');
			$css_file_form_styler = $this->assetUrl('js/jQueryFormStyler/jquery.formstyler.css');
			$js_file_form_styler = $this->assetUrl('js/jQueryFormStyler/jquery.formstyler.min.js');
			$js_file = $this->assetUrl('js/page-title-config.js');

			wp_enqueue_style( 'knowhere_page_title', $css_file );
			wp_enqueue_style( 'knowhere_form_styler', $css_file_form_styler );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'knowhere_form_styler', $js_file_form_styler, array( 'jquery' ), true);
			wp_enqueue_script( 'knowhere_page_title', $js_file, array( 'jquery', 'knowhere_form_styler' ), true);

			wp_localize_script( 'knowhere_form_styler', 'knowhere_page_title_vars', array(
				'img' => $this->paths['BASE_URI'] . 'img/default-placeholder.png'
			));
		}

		public function add_meta_box() {

			$post_types = array(
				'page', 'post', 'product'
			);

			add_meta_box( "knowhere_page_title_meta_box", esc_html__("Page Title", 'knowherepro'), array($this, 'draw_page_title_meta_box' ), $post_types, "normal", "low" );
		}

		public function draw_page_title_meta_box($post) {
			// Use nonce for verification
			wp_nonce_field( $this->action_page_title, 'knowhere_page_title_meta_box_nonce' );

			$data = $this->get_page_settings($post->ID);
			echo $this->draw_page($this->path('VIEW_PATH', 'form-meta-box.php'), $data);
		}

		public function save_perm_metadata( $post_id ) {

			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
				return;

			if ( !isset( $_POST['knowhere_page_title_meta_box_nonce'] ) )
				return;

			if ( !wp_verify_nonce( $_POST['knowhere_page_title_meta_box_nonce'], $this->action_page_title ) )
				return;

			if ( !current_user_can('edit_post', $post_id) )
				return;

			update_post_meta( $post_id, 'knowhere_page_title', $_POST['knowhere_page_title'] );

		}

		public function get_theme_page_title($postid) {
			$page_title = get_post_meta( $postid, "knowhere_page_title", true );
			if ( !is_array($page_title) ) {
				$page_title = array();
			}
			return $page_title;
		}

		public function get_page_settings($post_id) {
			$page_title = $this->get_theme_page_title($post_id);

			$data = array();
			$data['mode'] = isset($page_title['mode']) ? $page_title['mode'] : 'default';
			$data['options'] = $this->options();
			$data['page_title'] = $page_title;
			return $data;
		}

		public function draw_page( $pagepath, $data = array() ) {
			@extract($data);
			ob_start();
			include($pagepath);
			return ob_get_clean();
		}

		public function inserted_media_to_page_title() {

			if ( function_exists('check_ajax_referer') ) {
				check_ajax_referer($this->action_page_title, 'knowhere_page_title_meta_box_nonce');
			}

			$id = esc_attr($_POST['id']);

			if ( absint($id) && $id > 0 ) {
				$html = "<div class='img-preview add_animation'><img alt='' src='". Knowhere_Helper::get_post_attachment_image( $id, '100*100', true ) ."'></div>";

				wp_send_json(
					array(
						'html' => $html,
						'id' => $id
					)
				);
			}

		}

		public static function output_attributes() {

			switch( knowhere_page_title_get_value('mode') ) {
				case 'default':
					self::attributes_from_options();
					break;
				case 'custom':
					self::attributes_from_meta();
					break;
				default:
					self::attributes_from_options();
					break;
			}

		}

		public static function attributes_from_options() {

			global $knowhere_settings, $knowhere_config;

			$url = '';

			$css_classes = apply_filters('knowhere_page_header_media_classes', array(
				'kw-page-header-media'
			));

			if ( knowhere_is_realy_woocommerce_page() ) {

				if ( knowhere_is_product_category() ) {
					global $wp_query;
					$cat = $wp_query->get_queried_object();
					$thumbnail_id = get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true );
					$image = Knowhere_Helper::get_post_attachment_image( $thumbnail_id, '1600*1200' );
					if ( $image ) {
						$url = $image;
					}
				} elseif ( knowhere_is_product() ) {

					if ( has_post_thumbnail() ) {

						$image = Knowhere_Helper::get_attachment_url(get_post_thumbnail_id(), '1600*1200');

						if ( !empty($image) ) {
							$url = $image;
						}

					}

				}

			} elseif ( is_singular() ) {

				if ( has_post_thumbnail() ) {
					$url = Knowhere_Helper::get_attachment_url(get_post_thumbnail_id(), '1600*1200');
				}

				if ( empty($url) ) {
					$url = $knowhere_settings['page-header-upload']['url'];
				}

			} elseif ( is_archive() ) {

				$header_type = $knowhere_config['header_type'];

				if ( $header_type == 'kw-type-2' ) {
					$url = $knowhere_settings['header-type-2-bg']['background-image'];
				}

			}

			if ( !empty($url) ) {
				$wrapper_attributes[] = 'style="background-image: url(' . esc_attr($url) . ')"';
			}

			$css_class = preg_replace( '/\s+/', ' ', implode( ' ', array_unique(array_filter( $css_classes )) ) );
			$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';
			echo implode( ' ', $wrapper_attributes );
		}

		public static function attributes_from_meta() {

			$css_classes = apply_filters('knowhere_page_header_media_classes', array(
				'kw-page-header-media'
			));

			$upload = knowhere_page_title_get_value('upload');

			if ( absint($upload) && $upload > 0 ) {

				$url = Knowhere_Helper::get_post_attachment_image( $upload, '1600*1200', true );

				if ( !empty($url) ) {
					$wrapper_attributes[] = 'style="background-image: url(' . esc_attr($url) . ')"';
				}

			}

			$css_class = preg_replace( '/\s+/', ' ', implode( ' ', array_unique(array_filter( $css_classes )) ) );
			$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';
			echo implode( ' ', $wrapper_attributes );
		}

		public function options() {
			return array(
				'breadcrumb' => array(
					'name'    => 'breadcrumb',
					'title'    => esc_html__('Breadcrumb Navigation', 'knowherepro'),
					'desc'    => esc_html__('Display the Breadcrumb Navigation?', 'knowherepro'),
					'type'    => 'checkbox',
					'default' => ''
				),
				'align' => array(
					'name'    => 'align',
					'title'    => esc_html__('Align', 'knowherepro'),
					'desc'    => esc_html__('Align title and Breadcrumb Navigation', 'knowherepro'),
					'type'    => 'select',
					'options' => array(
						'align-left' => esc_html__('Left', 'knowherepro'),
						'align-center' => esc_html__('Center', 'knowherepro')
					),
					'default' => 'align-left'
				),
				'upload' => array(
					'name' => 'upload',
					'title' => esc_html__('Upload Background Image', 'knowherepro'),
					'desc' => esc_html__('Background Image for the header page title', 'knowherepro'),
					'type' => 'upload',
					'default' => ''
				),
			);
		}

		public static function check_video_url($video_url = '') {

			if ( preg_match("/\.mp4$/", $video_url) ) {
				$video_url = trim($video_url);
			}

			return $video_url;
		}

	}

	new Knowhere_Page_Title_Config();

}