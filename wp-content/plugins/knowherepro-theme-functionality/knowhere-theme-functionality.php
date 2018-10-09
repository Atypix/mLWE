<?php
/*
Plugin Name: KnowherePro Theme - Functionality
Description: Adds functionality to KnowherePro Theme.
Version: 1.1.5
Author: monkeysan
Author URI: https://themeforest.net/user/monkeysan/portfolio
License:     GPL2
*/

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( !class_exists('Knowhere_Functionality') ) {

	class Knowhere_Functionality {

		public $paths = array();
		public static $pathes = array();
		protected $plugin_slug = 'knowhere-theme-functionality';
		public $version = '1.1.4';

		protected $default_settings = array(
			'post_types' => array(
				'knowhere_agent' => 'off',
				'knowhere_agency' => 'off',
				'testimonials' => 'on'
			),
			'extensions' => array(
				'knowhere_classified' => 'off'
			)
		);

		public $extensions = array(
			'knowhere_classified' => 'Classified Extenstion'
		);

		public $custom_post_types = array(
			'knowhere_agent' => 'knowhere_agent',
			'knowhere_agency' => 'knowhere_agency',
			'testimonials' => 'testimonials'
		);

		public $custom_classes = array(
			'knowhere_agent' => 'Knowhere_Post_Type_Agent',
			'knowhere_agency' => 'Knowhere_Post_Type_Agency',
			'testimonials' => 'Knowhere_Post_Type_Testimonials'
		);

		function __construct() {

			if ( ! defined( 'Knowhere_Content_Type_Version' ) ) {
				define( 'Knowhere_Content_Type_Version', '1.0' );
			}

			if ( ! defined( 'KW_PLUGIN_FILE' ) ) {
				define( 'KW_PLUGIN_FILE', __FILE__ );
			}

			$options = get_option($this->plugin_slug);

			if ( empty( $options ) ) {
				$options = $this->get_defaults();
				update_option( $this->plugin_slug, $options );
			}

			// Load text domain
			add_action('plugins_loaded', array( &$this, 'load_textdomain' ) );

			$dir = plugin_dir_path(__FILE__);

			$this->paths = array(
				'APP_ROOT' => $dir,
				'APP_DIR' => basename( $dir ),
				'EXT_DIR' => $dir . 'inc/extensions/',
				'ASSETS_DIR_NAME' => 'knowherepro-theme-functionality/assets',
				'BASE_URI' => plugin_dir_url(__FILE__),
				'CLASSES_PATH' => $dir . 'classes/',
				'METABOXES_PATH' => $dir . 'metaboxes/',
				'INC_PATH' => $dir . 'includes/',
				'XML_PATH' => $dir . 'xml/',
			);

			add_filter( 'widget_text', 'do_shortcode' );

			self::$pathes = $this->paths;

			include_once( $this->paths['APP_ROOT'] . 'functions.php' );
			include_once( $this->paths['APP_ROOT'] . 'extensions/metaboxes/metaboxes.php' );

			// Include classes
			$this->include_classes();

			// Init classes
			add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
			add_action( 'init', array( &$this, 'plugin_init' ) );


			$selected_extensions = $this->get_plugin_option('extensions');

			if ( !empty($selected_extensions) && isset ( $selected_extensions['knowhere_classified'] ) && $selected_extensions['knowhere_classified'] == 'on' ) {

				add_action( 'admin_menu', array( $this, 'add_plugin_attribute_admin_menu') );
				$this->define_constants();
				$this->includes();

			}

			if ( class_exists('ReduxFramework') ) {
				add_action( 'redux/extensions/knowhere_settings/before', array( $this, 'register_custom_extension_loader' ), 0 );

				/*  Importer
				/* ---------------------------------------------------------------------- */
				require_once( $this->paths['APP_ROOT'] . 'importer/import-class.php' );
			}

			add_shortcode( 'knowhere_post_gallery', array( $this, 'gallery_shortcode' ) );
			add_shortcode( 'knowhere_audio', array( $this, 'audio_shortcode' ) );

			add_action( 'wp_footer', array( $this, 'pin_templates' ) );

			add_action( 'admin_enqueue_scripts', array($this, 'admin_enqueue_scripts') );

		}

		function admin_enqueue_scripts() {
			wp_enqueue_script( 'chosen', $this->paths['BASE_URI'] . 'assets/js/jquery-chosen/chosen.jquery.min.js', array( 'jquery' ), '1.1.0', true );
			wp_enqueue_style( 'chosen', $this->paths['BASE_URI'] . 'assets/css/chosen.css', array(), '1.1.0' );

			$deps[] = 'chosen';

			wp_enqueue_script( 'knowhere-admin', $this->paths['BASE_URI'] . 'assets/js/admin-functionality.js', $deps, null, true );
		}

		function define_constants() {
			$this->define( 'KW_ABSPATH', dirname( KW_PLUGIN_FILE ) . '/' );
			$this->define( 'KW_DELIMITER', '|' );
			$this->define( 'KW_VERSION', $this->version );
		}

		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin' :
					return is_admin();
				case 'ajax' :
					return defined( 'DOING_AJAX' );
				case 'cron' :
					return defined( 'DOING_CRON' );
				case 'frontend' :
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}
		}

		function includes() {

			/**
			 * Class autoloader.
			 */
			include_once( $this->paths['INC_PATH'] . 'class-autoloader.php' );

			/**
			 * Core classes.
			 */
			include( $this->paths['INC_PATH'] . 'kw-conditional-functions.php' );
			include( $this->paths['INC_PATH'] . 'kw-formatting-functions.php' );
			include( $this->paths['INC_PATH'] . 'kw-attribute-functions.php' );
			include_once( $this->paths['INC_PATH'] . 'class-kw-post-types.php' );
			include_once( $this->paths['INC_PATH'] . 'class-kw-install.php' );

			if ( $this->is_request( 'frontend' ) ) {
				include_once( $this->paths['INC_PATH'] . 'class-kw-listing-manager.php' );
				include_once( $this->paths['INC_PATH'] . 'class-kw-frontend-scripts.php' );
			}

		}

		function register_custom_extension_loader($ReduxFramework) {
			$path    = $this->paths['EXT_DIR'];
			$folders = scandir( $path, 1 );
			foreach ( $folders as $folder ) {
				if ( $folder === '.' or $folder === '..' or ! is_dir( $path . $folder ) ) {
					continue;
				}
				$extension_class = 'ReduxFramework_Extension_' . $folder;
				if ( ! class_exists( $extension_class ) ) {
					// In case you wanted override your override, hah.
					$class_file = $path . $folder . '/extension_' . $folder . '.php';
					$class_file = apply_filters( 'redux/extension/' . $ReduxFramework->args['opt_name'] . '/' . $folder, $class_file );
					if ( $class_file ) {
						require_once( $class_file );
					}
				}
				if ( ! isset( $ReduxFramework->extensions[ $folder ] ) ) {
					$ReduxFramework->extensions[ $folder ] = new $extension_class( $ReduxFramework );
				}
			}
		}

		function add_plugin_admin_menu() {
			add_theme_page(
				esc_html__( 'Theme Functionality', 'knowherepro_app_textdomain' ),
				esc_html__( 'Theme Functionality', 'knowherepro_app_textdomain' ),
				'manage_options',
				$this->plugin_slug, array( $this, 'display_plugin_admin_page' )
			);
		}

		function add_plugin_attribute_admin_menu() {
			add_submenu_page(
				'edit.php?post_type=job_listing',
				__( 'Attributes', 'knowherepro_app_textdomain' ),
				__( 'Attributes', 'knowherepro_app_textdomain' ),
				'manage_options',
				'job-manager-attributes', array( $this, 'attributes_page' )
			);
		}

		function attributes_page() {
			KW_Admin_Attributes::output();
		}

		function display_plugin_admin_page() { ?>

			<div class="wrap" id="admin_page">
				<form action="options.php" method="post">
					<?php
					settings_fields($this->plugin_slug);
					do_settings_sections($this->plugin_slug); ?>
					<input name="Submit" type="submit" value="<?php esc_html_e('Save Changes', 'knowherepro_app_textdomain'); ?>" />
				</form>
			</div>

		<?php }

		function admin_init() {

			register_setting( $this->plugin_slug, $this->plugin_slug, array( $this, 'save_setting_values' ) );
			add_settings_section( $this->plugin_slug, esc_html__( 'General settings', 'knowherepro_app_textdomain' ), '', $this->plugin_slug );

			add_settings_field( 'extensions', esc_html__( 'Classified Extension', 'knowherepro_app_textdomain' ), array( $this, 'render_extension_checkbox' ), $this->plugin_slug, $this->plugin_slug );
			add_settings_field( 'post_types', esc_html__( 'Select Post Types', 'knowherepro_app_textdomain' ), array( $this, 'render_post_types_checkbox' ), $this->plugin_slug, $this->plugin_slug );

		}

		function render_extension_checkbox() {

			$custom_extensions = $this->extensions;

			$options = get_option($this->plugin_slug);
			$selected = array();

			if ( isset( $options['extensions'] ) ) {
				$selected = $options['extensions'];
			}

			?>

			<field>
				<?php
				foreach ( $custom_extensions as $key => $extension ) {
					$checked = '';

					if ( ! empty( $selected ) && isset( $selected[$key] ) && $selected[$key] == 'on' ) {
						$checked = ' checked="selected"';
					}

					$full_key = 'knowhere-theme-functionality[extensions][' . $key  . ']'; ?>
					<label for="<?php echo esc_attr($full_key); ?>">
						<input id='<?php echo esc_attr($full_key); ?>' name='<?php echo esc_attr($full_key); ?>' size='40' type='checkbox' <?php echo esc_attr($checked) ?>/>
						<?php echo esc_html($extension) ?>
						</br>
					</label>
				<?php } ?>
			</field>

			<?php
		}

		function render_post_types_checkbox ( $val ) {

			$custom_post_types = $this->custom_post_types;
			$options = get_option($this->plugin_slug);
			$selected = array();

			if ( isset( $options['post_types'] ) ) {
				$selected = $options['post_types'];
			}

			?>

			<field>
				<?php
				foreach ( $custom_post_types as $key => $post_type ) {
					$checked = '';

					if ( ! empty( $selected ) && isset( $selected[$key] ) && $selected[$key] == 'on' ) {
						$checked = ' checked="selected"';
					}

					$full_key = 'knowhere-theme-functionality[post_types][' . $key  . ']'; ?>
					<label for="<?php echo esc_attr($full_key); ?>">
						<input id='<?php echo esc_attr($full_key); ?>' name='<?php echo esc_attr($full_key); ?>' size='40' type='checkbox' <?php echo esc_attr($checked) ?>/>
						<?php echo esc_html($key) ?>
						</br>
					</label>
				<?php } ?>
			</field>

		<?php
		}

		function save_setting_values( $input ) {
			return $input;
		}

		function render_settings_section_title() { ?>
			<h2><?php esc_html_e('Options', 'knowherepro_app_textdomain'); ?></h2>
		<?php }

		function get_plugin_option( $key ) {

			$options = get_option($this->plugin_slug);

			if ( isset( $options [$key] ) ) {
				return $options [$key];
			}

			return null;
		}

		function get_defaults() {
			return $this->default_settings;
		}

		// include classes
		function include_classes() {

			require_once( $this->paths['CLASSES_PATH'] . 'register-agency-post-type.php' );
			require_once( $this->paths['CLASSES_PATH'] . 'register-agent-post-type.php' );
			require_once( $this->paths['CLASSES_PATH'] . 'register-knowhere-query.php' );
			require_once( $this->paths['CLASSES_PATH'] . 'register-testimonials-post-type.php' );

		}

		// init plugin
		function plugin_init() {

			$custom_classes = $this->custom_classes;
			$selected_post_types = $this->get_plugin_option('post_types');

			foreach ( $custom_classes as $post_type => $custom_class ) {
				if ( !empty($selected_post_types) && isset ( $selected_post_types[$post_type] ) && $selected_post_types[$post_type] == 'on' ) {
					new $custom_class;
				}
			}

		}

		private function get_file_name_from_class( $class ) {
			return 'register-' . str_replace( '_', '-', $class ) . '.php';
		}

		// load plugin text domain
		function load_textdomain() {
			load_plugin_textdomain( 'knowherepro_app_textdomain', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		// Get content type labels
		function getLabels($singular_name, $name, $title = FALSE) {
			if ( !$title )
				$title = $name;

			return array(
				"name" => $title,
				"singular_name" => $singular_name,
				"add_new" => esc_html__("Add New", 'knowherepro_app_textdomain'),
				"add_new_item" => sprintf( __("Add New %s", 'knowherepro_app_textdomain'), $singular_name),
				"edit_item" => sprintf( __("Edit %s", 'knowherepro_app_textdomain'), $singular_name),
				"new_item" => sprintf( __("New %s", 'knowherepro_app_textdomain'), $singular_name),
				"view_item" => sprintf( __("View %s", 'knowherepro_app_textdomain'), $singular_name),
				"search_items" => sprintf( __("Search %s", 'knowherepro_app_textdomain'), $name),
				"not_found" => sprintf( __("No %s found", 'knowherepro_app_textdomain'), $name),
				"not_found_in_trash" => sprintf( __("No %s found in Trash", 'knowherepro_app_textdomain'), $name),
				"parent_item_colon" => ""
			);
		}

		// Get content type taxonomy labels
		function getTaxonomyLabels($singular_name, $name) {
			return array(
				"name" => $name,
				"singular_name" => $singular_name,
				"search_items" => sprintf( __("Search %s", 'knowherepro_app_textdomain'), $name),
				"all_items" => sprintf( __("All %s", 'knowherepro_app_textdomain'), $name),
				"parent_item" => sprintf( __("Parent %s", 'knowherepro_app_textdomain'), $singular_name),
				"parent_item_colon" => sprintf( __("Parent %s:", 'knowherepro_app_textdomain'), $singular_name),
				"edit_item" => sprintf( __("Edit %", 'knowherepro_app_textdomain'), $singular_name),
				"update_item" => sprintf( __("Update %s", 'knowherepro_app_textdomain'), $singular_name),
				"add_new_item" => sprintf( __("Add New %s", 'knowherepro_app_textdomain'), $singular_name),
				"new_item_name" => sprintf( __("New %s Name", 'knowherepro_app_textdomain'), $singular_name),
				'not_found' => sprintf(__('No %s found', 'knowherepro_app_textdomain'), $singular_name),
				'not_found_in_trash' => sprintf(__('No %s found in Trash', 'knowherepro_app_textdomain'), $singular_name),
				"menu_name" => $name,
			);
		}

		public function pin_templates() {

			if ( function_exists('knowhere_is_realy_job_manager_page') && function_exists('knowhere_check_theme_options') ) {
				if ( knowhere_is_realy_job_manager_page() || knowhere_is_realy_job_manager_submit_job_form() ) {
					include('templates/tmpl-map-pin-cluster-svg.php');
					include('templates/tmpl-map-pin-empty-svg.php');
					include('templates/tmpl-map-pin-selected-svg.php');
				}
			}

		}

		public function audio_shortcode($atts) {
			$mp3 = '';

			extract(shortcode_atts(array(
				'mp3' => ''
			), $atts));

			ob_start(); ?>

			<?php if ( !empty($mp3) ): ?>
				<audio controls src="<?php echo esc_url($mp3) ?>"></audio>
			<?php endif; ?>

			<?php return ob_get_clean();
		}

		public function gallery_shortcode($atts) {
			$image_size = $size = $post_id = $ids = '';

			extract(shortcode_atts(array(
				'image_size' => '',
				'size' => '',
				'post_id' => '',
				'ids' => '',
			), $atts));

			$attachments = get_posts(array(
				'include' => $ids,
				'orderby' => 'post__in',
				'post_status' => 'inherit',
				'post_type' => 'attachment',
				'post_mime_type' => 'image'
			));

			$data_rel = 'data-rel=post-'. rand() .'';

			ob_start(); ?>

			<?php if ( !empty($attachments) && is_array($attachments) ): ?>

				<div class="owl-carousel kw-slideshow">

					<?php foreach ( $attachments as $attachment ): ?>

						<?php
							$attachment_id = $attachment->ID;
							$title = get_the_title($attachment_id);
							$permalink = Knowhere_Admin_Helper::get_post_attachment_image($attachment_id, '');
						?>

						<div class="kw-slideshow-entry-item">
							<a class="kw-popup-gallery" <?php echo esc_attr($data_rel) ?> href="<?php echo esc_url($permalink) ?>" title="<?php echo esc_attr($title) ?>">
								<?php if ( !empty($image_size) ): ?>
									<?php echo Knowhere_Admin_Helper::get_the_thumbnail( $attachment_id, $image_size, true, '', array( 'alt' => '', 'class' => '' ) ) ?>
								<?php elseif( !empty($size) ): ?>
									<?php echo Knowhere_Admin_Helper::get_attachment_image( $attachment_id, $size, false, array( 'alt' => '', 'class' => '' ) ) ?>
								<?php else: ?>
									<?php echo Knowhere_Admin_Helper::get_the_thumbnail( $attachment_id, $image_size, true, '', array( 'alt' => '', 'class' => '' ) ) ?>
								<?php endif; ?>
							</a>
						</div>

					<?php endforeach; ?>

				</div><!--/ .kw-slideshow-->

			<?php endif; return ob_get_clean();

		}

	}

	new Knowhere_Functionality();

}
