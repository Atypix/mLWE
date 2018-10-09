<?php

if (!class_exists('Knowhere_Widgets_Meta_Box')) {

	class Knowhere_Widgets_Meta_Box {

		public $paths = array();
		public static $pathes = array();

		public $screen = array(
			'post', 'page', 'job_listing', 'resume'
		);

		public function path($name, $file = '') {
			return $this->paths[$name] . (strlen($file) > 0 ? '/' . preg_replace('/^\//', '', $file) : '');
		}

		public function assetUrl($file)  {
			return $this->paths['BASE_URI'] . $this->path('ASSETS_DIR_NAME', $file);
		}

		function __construct () {

			$dir = get_template_directory() . '/config-widget-meta-box';

			$this->paths = array(
				'PHP' => $dir . '/php/',
				'ASSETS_DIR_NAME' => 'assets',
				'BASE_URI' => get_template_directory_uri() . '/config-widget-meta-box/'
			);

			self::$pathes = $this->paths;
			require_once($this->paths['PHP'] . 'sidebar-generator.class.php');

			$this->init();
		}

		public function init() {

			if ( is_admin() ) {
				add_action('add_meta_boxes', array($this, 'add_meta_box') );
				add_action('save_post', array($this, 'save_post'));
				add_action('load-post.php', array($this, 'admin_enqueue_scripts'));
				add_action('load-post-new.php', array($this, 'admin_enqueue_scripts'));
				add_action('admin_enqueue_scripts', array($this, 'add_json') );
			}

		}

		public function admin_enqueue_scripts() {

			if ( is_admin() && function_exists( 'get_current_screen' ) ) {

				$screen = get_current_screen();

				if ( in_array($screen->post_type, $this->screen ) ) {
					$css_file = $this->assetUrl('css/widget-meta-box.css');
					$js_file = $this->assetUrl('js/widget-meta-box.js');
					wp_enqueue_style( 'knowhere_widget-meta-box', $css_file );
					wp_enqueue_script( 'knowhere_widget-meta-box', $js_file, array('jquery'), 1, true );
				}

			}

		}

		public function add_meta_box() {
			add_meta_box("knowhere_widets_footer_meta_box", esc_html__("Widgets Row Footer", 'knowherepro'), array($this, 'draw_widgets_meta_box' ), $this->screen, "normal", "low");
		}

		public function add_json() {

			if ( is_admin() && function_exists( 'get_current_screen' ) ) {
				$screen = get_current_screen();

				if ( in_array($screen->post_type, $this->screen) ) {
					?>
					<script type='text/html' id='knowhere-tmpl-options-hidden'>
						<?php echo json_encode($this->columns_grid()); ?>
					</script>
					<?php
				}

			}

		}

		public function columns_grid() {
			return array( "1" => array( array( "12" ) ),
						  "2" => array( array( "6", "6" ), array( "8", "4" ), array( "4", "8" ) ),
						  "3" => array( array( "4", "4", "4" ), array( "6", "3", "3" ), array( "3", "6", "3" ) ),
						  "4" => array( array( "3", "3", "3", "3" ) ),
						  "5" => array( array( "3", "2", "2", "2", "3" ) )
			);
		}

		public static function get_page_settings($post_id) {
			$results = array();

			$results['footer_row_top_show'] = get_post_meta($post_id, 'footer_row_top_show', true);
			$results['footer_row_middle_show'] = get_post_meta($post_id, 'footer_row_middle_show', true);

			$results['get_sidebars_top_widgets'] = get_post_meta($post_id, 'get_sidebars_top_widgets', true);
			$results['get_sidebars_middle_widgets'] = get_post_meta($post_id, 'get_sidebars_middle_widgets', true);

			$results['footer_row_top_columns_variations'] = get_post_meta($post_id, 'footer_row_top_columns_variations', true);
			$results['footer_row_middle_columns_variations'] = get_post_meta($post_id, 'footer_row_middle_columns_variations', true);

			$results['footer_row_top_full_width'] = get_post_meta($post_id, 'footer_row_top_full_width', true);
			$results['footer_row_middle_full_width'] = get_post_meta($post_id, 'footer_row_middle_full_width', true);

			if ( $results['footer_row_top_show'] == null ) { $results['footer_row_top_show'] = 1; }
			if ( $results['footer_row_middle_show'] == null ) { $results['footer_row_middle_show'] = 0; }
			if ( $results['footer_row_top_full_width'] == null ) { $results['footer_row_top_full_width'] = 0; }
			if ( $results['footer_row_middle_full_width'] == null ) { $results['footer_row_middle_full_width'] = 0; }
			if ( $results['footer_row_top_columns_variations'] == null ) { $results['footer_row_top_columns_variations'] = '{"4":[["3","3","3","3"]]}'; }
			if ( $results['footer_row_middle_columns_variations'] == null ) { $results['footer_row_middle_columns_variations'] = '{"4":[["3","3","3","3"]]}'; }

			if ( $results['get_sidebars_top_widgets'] == null ) {
				$results['get_sidebars_top_widgets'] = array(
					'Footer Row - widget 1',
					'Footer Row - widget 2',
					'Footer Row - widget 3',
					'Footer Row - widget 4',
					'Footer Row - widget 5'
				);
			}

			if ( $results['get_sidebars_middle_widgets'] == null ) {
				$results['get_sidebars_middle_widgets'] = array(
					'Footer Row - widget 1',
					'Footer Row - widget 2',
					'Footer Row - widget 3',
					'Footer Row - widget 4',
					'Footer Row - widget 5'
				);
			}

			return $results;
		}

		public function get_registered_sidebars() {
			$registered_sidebars = Knowhere_Helper::get_registered_sidebars();
			$registered_footer_sidebars = array();

			foreach($registered_sidebars as $key => $value) {
				if (strpos($key, 'Footer Row') !== false) {
					$registered_footer_sidebars[$key] = $value;
				}
			}
			return $registered_footer_sidebars;
		}

		public function draw_widgets_meta_box() {
			global $post;

			// Use nonce for verification
			wp_nonce_field( 'knowhere-post-meta-box', 'knowhere-post-meta-box-nonce' );

			$data = array();
			$data['columns_variations'] = $this->columns_grid();
			$data['get_sidebars'] = $this->get_registered_sidebars();
			$data['columns'] = 5;

			$data = array_merge( $data, self::get_page_settings($post->ID)  );

			echo $this->draw_page( $this->path('PHP', 'meta_box.php'), $data );
		}

		public function save_post($post_id) {
			global $post;

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
				return;

			if ( !isset( $_POST['knowhere-post-meta-box-nonce'] ) )
				return;

			if ( !wp_verify_nonce( $_POST['knowhere-post-meta-box-nonce'], 'knowhere-post-meta-box' ) )
				return;

			if ( is_object($post) AND !empty($_POST) ) {
				update_post_meta($post_id, "footer_row_top_show", @$_POST["footer_row_top_show"]);
				update_post_meta($post_id, "footer_row_middle_show", @$_POST["footer_row_middle_show"]);
				update_post_meta($post_id, "footer_row_top_columns_variations", @$_POST["footer_row_top_columns_variations"]);
				update_post_meta($post_id, "footer_row_middle_columns_variations", @$_POST["footer_row_middle_columns_variations"]);
				update_post_meta($post_id, "get_sidebars_top_widgets", @$_POST["get_sidebars_top_widgets"]);
				update_post_meta($post_id, "get_sidebars_middle_widgets", @$_POST["get_sidebars_middle_widgets"]);
				update_post_meta($post_id, "footer_row_top_full_width", @$_POST["footer_row_top_full_width"]);
				update_post_meta($post_id, "footer_row_middle_full_width", @$_POST["footer_row_middle_full_width"]);
			}
		}

		public function draw_page($pagepath, $data = array()) {
			@extract($data);
			ob_start();
			include $pagepath;
			return ob_get_clean();
		}

	}

	new Knowhere_Widgets_Meta_Box();

}