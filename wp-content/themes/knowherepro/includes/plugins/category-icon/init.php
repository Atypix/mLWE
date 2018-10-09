<?php
/*
Plugin Name: Category Icon
Plugin URI:  http://pixelgrade.com
Description: Easily add an icon to a category, tag or any other taxonomy.
Version: 0.6.0
Author: PixelGrade
Author URI: http://pixelgrade.com
Author Email: contact@pixelgrade.com
License:     GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

KnowherePixTaxonomyIconsPlugin::get_instance();

class KnowherePixTaxonomyIconsPlugin {
	protected static $instance;
	protected $plugin_screen_hook_suffix = null;
	protected $version = '0.6.0';
	protected $plugin_slug = 'knowhere-category-icon';
	protected $plugin_key = 'knowhere-category-icon';

	protected $default_settings = array(
		'taxonomies' => array(
			'job_listing_category' => 'on'
		)
	);

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 * @since     1.0.0
	 */
	protected function __construct() {

		$options = get_option('knowhere-category-icon');

		if ( empty( $options ) ) {
			$options = $this->get_defaults();
			update_option( 'knowhere-category-icon', $options );
		}

		/**
		 * As we code this, WordPress has a problem with uploading and viewing svg files.
		 * Until they get it done in core, we use these filters
		 * https://core.trac.wordpress.org/ticket/26256
		 * and
		 * https://gist.github.com/Lewiscowles1986/44f059876ec205dd4d27
		 */
		add_filter('upload_mimes', array( $this, 'allow_svg_in_mime_types' ), 1 );
		add_action('admin_head', array( $this, 'force_svg_with_visible_sizes' ) );

//		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'plugin_admin_init' ) );

		// Load plugin text domain
		add_action( 'init', array( $this, 'plugin_init' ), 9999999999 );
		add_action( 'init', array( $this, 'register_the_termmeta_table' ), 1 );
		add_action('wpmu_new_blog', array($this, 'new_blog'), 10, 6);

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		register_activation_hook( __FILE__, array($this, 'activate') );
	}

	/**
	 * This will run when the plugin will turn On
	 *
	 * @param bool|false $network_wide
	 */
	function activate( $network_wide = false ) {
		global $wpdb;

		// if activated on a particular blog, just set it up there.
		if ( !$network_wide ) {
			$this->create_the_termmeta_table();
			return;
		}

		$blogs = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs} WHERE site_id = '{$wpdb->siteid}'" );
		foreach ( $blogs as $blog_id ) {
			$this->create_the_termmeta_table( $blog_id );
		}
		// I feel dirty... this line smells like perl.
		do {} while ( restore_current_blog() );
	}

	function new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
		if ( is_plugin_active_for_network(plugin_basename(__FILE__)) )
			$this->create_the_termmeta_table($blog_id);
	}

	/**
	 * Return an instance of this class.
	 * @since     1.0.0
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	function plugin_init() {

		$selected_taxonomies = $this->get_plugin_option('taxonomies');

		if ( ! is_wp_error( $selected_taxonomies ) && ! empty( $selected_taxonomies ) ) {
			foreach ( $selected_taxonomies as $tax_name => $value ) {

				add_action( $tax_name . '_add_form_fields', array( $this, 'taxonomy_add_new_meta_field'), 10, 2 );
				add_action( $tax_name . '_edit_form_fields', array( $this, 'taxonomy_edit_new_meta_field'), 10, 2 );
				add_action( 'edited_' . $tax_name,  array( $this, 'save_taxonomy_custom_meta' ), 10, 2 );
				add_action( 'create_' . $tax_name,  array( $this, 'save_taxonomy_custom_meta' ), 10, 2 );
				add_filter( "manage_edit-" . $tax_name . "_columns", array( $this, 'add_custom_tax_column' ) );
				add_filter( "manage_" . $tax_name . "_custom_column", array( $this, 'output_custom_tax_column' ), 10, 3 );
			}
		}
	}

	function enqueue_admin_scripts () {
		wp_enqueue_style( $this->plugin_slug . '-admin-style', get_theme_file_uri('includes/plugins/category-icon/assets/css/category-icon.css'), array(  ), $this->version );
		wp_enqueue_media();
		wp_enqueue_script( $this->plugin_slug . '-admin-script', get_theme_file_uri('includes/plugins/category-icon/assets/js/category-icon.js'), array( 'jquery' ), $this->version );
		wp_localize_script( $this->plugin_slug . '-admin-script', 'locals', array(
			'ajax_url' => admin_url( 'admin-ajax.php' )
		) );
	}

	function taxonomy_add_new_meta_field ( $tax ) { ?>
		<div class="open_term_icon_preview form-field">
			<input type="hidden" name="term_icon_value" id="term_icon_value" value="">
			<span class="open_term_icon_upload button button-secondary">
				<?php esc_html_e( 'Select Icon', 'knowherepro' ); ?>
			</span>
		</div>
		<div class="form-field term-header-value">
			<label for="term_header_value"><?php echo esc_html__('Header Type', 'knowherepro') ?></label>
			<select name="term_header_value" id="term_header_value">
				<option value=""><?php echo esc_html__('Default', 'knowherepro') ?></option>
				<option value="kw-theme-color"><?php echo esc_html__('Theme Color', 'knowherepro') ?></option>
				<option value="kw-dark"><?php echo esc_html__('Dark', 'knowherepro') ?></option>
				<option value="kw-light"><?php echo esc_html__('Light', 'knowherepro') ?></option>
			</select>
		</div>
		<div class="form-field term-page-layout">
			<label for="term_page_layout_value"><?php echo esc_html__('Page Layout', 'knowherepro') ?></label>
			<select name="term_page_layout_value" id="term_page_layout_value">
				<option value=""><?php echo esc_html__('Default', 'knowherepro') ?></option>
				<option value="kw-no-sidebar"><?php esc_html_e('Without Sidebar', 'knowherepro') ?></option>
				<option value="kw-left-sidebar"><?php esc_html_e('Left Sidebar', 'knowherepro') ?></option>
				<option value="kw-right-sidebar"><?php esc_html_e('Right Sidebar', 'knowherepro') ?></option>
			</select>
		</div>
		<div class="form-field term-show-map">
			<label for="term_show_map_value"><?php echo esc_html__('Show Map', 'knowherepro') ?></label>
			<select name="term_show_map_value" id="term_show_map_value">
				<option value=""><?php echo esc_html__('Default', 'knowherepro') ?></option>
				<option value="yes"><?php esc_html_e('Yes', 'knowherepro') ?></option>
				<option value="no"><?php esc_html_e('No', 'knowherepro') ?></option>
			</select>
		</div>
		<div class="form-field term-filter-position">
			<label for="term_filter_position"><?php echo esc_html__('Filter Position', 'knowherepro') ?></label>
			<select name="term_filter_position" id="term_filter_position">
				<option value=""><?php echo esc_html__('Default', 'knowherepro') ?></option>
				<option value="kw-top-position"><?php esc_html_e('Top', 'knowherepro') ?></option>
				<option value="kw-left-position"><?php esc_html_e('Left', 'knowherepro') ?></option>
			</select>
		</div>
		<div class="form-field term-style">
			<label for="term_style"><?php echo esc_html__('Listing Style', 'knowherepro') ?></label>
			<select name="term_style" id="term_style">
				<option value=""><?php echo esc_html__('Default', 'knowherepro') ?></option>
				<option value="kw-type-1"><?php esc_html_e('Style 1', 'knowherepro') ?></option>
				<option value="kw-type-2"><?php esc_html_e('Style 2', 'knowherepro') ?></option>
				<option value="kw-type-3"><?php esc_html_e('Style 3 ( property )', 'knowherepro') ?></option>
				<option value="kw-type-4"><?php esc_html_e('Style 4 ( job )', 'knowherepro') ?></option>
				<option value="kw-type-5"><?php esc_html_e('Style 5 ( classified )', 'knowherepro') ?></option>
			</select>
		</div>
		<div class="form-field term-view">
			<label for="term_view"><?php echo esc_html__('View', 'knowherepro') ?></label>
			<select name="term_view" id="term_view">
				<option value=""><?php echo esc_html__('Default', 'knowherepro') ?></option>
				<option value="kw-grid-view"><?php esc_html_e('Grid', 'knowherepro') ?></option>
				<option value="kw-list-view"><?php esc_html_e('List', 'knowherepro') ?></option>
			</select>
		</div>
		<div class="form-field term-count-columns">
			<label for="term_header_value"><?php echo esc_html__('Count Columns', 'knowherepro') ?></label>
			<select name="term_count_columns" id="term_count_columns">
				<option value=""><?php echo esc_html__('Default', 'knowherepro') ?></option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
			</select>
		</div>
		<div class="form-field term-color-icon">
			<label for="term_color_icon"><?php echo esc_html__('Color Icon', 'knowherepro') ?></label>
			<div class="colorpicker-wrap">
				<input type="text" data-alpha="false" class="term-color-picker" name="term_color_icon" value="" />
			</div>
		</div>
		<?php
	}

	function taxonomy_edit_new_meta_field ( $term ) {
		$current_value = $current_header_value = $current_disable_map_value = '';
		if ( isset( $term->term_id ) ) {
			$current_value = get_term_meta( $term->term_id, 'pix_term_icon', true );
			$current_header_value = get_term_meta( $term->term_id, 'pix_term_header', true );
			$current_category_layout_value = get_term_meta( $term->term_id, 'pix_term_page_layout', true );
			$current_show_map_value = get_term_meta( $term->term_id, 'pix_term_show_map', true );
			$current_style = get_term_meta( $term->term_id, 'pix_term_style', true );
			$current_filter_position = get_term_meta( $term->term_id, 'pix_term_filter_position', true );
			$current_count_columns = get_term_meta( $term->term_id, 'pix_term_count_columns', true );
			$current_view = get_term_meta( $term->term_id, 'pix_term_view', true );
			$current_color_icon = get_term_meta( $term->term_id, 'pix_term_color_icon', true );
		}

		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="term_icon_value"><?php esc_html_e( 'Icon', 'knowherepro' ); ?></label></th>
			<td>
				<div class="open_term_icon_preview">
					<input type="hidden" name="term_icon_value" id="term_icon_value" value="<?php echo esc_attr($current_value); ?>">
					<?php if ( empty( $current_value ) ) { ?>
						<span class="open_term_icon_upload button button-secondary">
							<?php esc_html_e( 'Select Icon', 'knowherepro'); ?>
						</span>
					<?php } else {
						$thumb_src = wp_get_attachment_image_src( $current_value );?>
						<img src="<?php echo esc_url($thumb_src[0]); ?>" style="width: 90%; height:90%; padding: 5%" />
						<span class="open_term_icon_upload button button-secondary">
							<?php esc_html_e( 'Select', 'knowherepro' ); ?>
						</span>
						<span class="open_term_icon_delete button button-secondary">
							<?php esc_html_e( 'Remove', 'knowherepro' );?>
						</span>
				<?php } ?>
				</div>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="term_header_value"><?php esc_html_e( 'Header Type', 'knowherepro' ); ?></label></th>
			<td>
				<label for="term_header_value"><?php echo esc_html__('Header Type', 'knowherepro') ?></label>
				<p>
					<select name="term_header_value" id="term_header_value">
						<option value=""><?php echo esc_html__('Default Header Type', 'knowherepro') ?></option>
						<option <?php selected( $current_header_value, 'kw-theme-color' ) ?> value="kw-theme-color"><?php echo esc_html__('Theme Color', 'knowherepro') ?></option>
						<option <?php selected( $current_header_value, 'kw-dark' ) ?> value="kw-dark"><?php echo esc_html__('Dark', 'knowherepro') ?></option>
						<option <?php selected( $current_header_value, 'kw-light' ) ?> value="kw-light"><?php echo esc_html__('Light', 'knowherepro') ?></option>
					</select>
				</p>
			</td>
		</tr>

		<tr class="form-field">
			<th scope="row" valign="top"><label for="term_page_layout_value"><?php esc_html_e( 'Page Layout', 'knowherepro' ); ?></label></th>
			<td>
				<label for="term_page_layout_value"><?php echo esc_html__('Page Layout', 'knowherepro') ?></label>
				<p>
					<select name="term_page_layout_value" id="term_page_layout_value">
						<option value=""><?php echo esc_html__('Default', 'knowherepro') ?></option>
						<option <?php selected( $current_category_layout_value, 'kw-no-sidebar' ) ?> value="kw-no-sidebar"><?php esc_html_e('Without Sidebar', 'knowherepro') ?></option>
						<option <?php selected( $current_category_layout_value, 'kw-left-sidebar' ) ?> value="kw-left-sidebar"><?php esc_html_e('Left Sidebar', 'knowherepro') ?></option>
						<option <?php selected( $current_category_layout_value, 'kw-right-sidebar' ) ?> value="kw-right-sidebar"><?php esc_html_e('Right Sidebar', 'knowherepro') ?></option>
					</select>
				</p>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="term_show_map_value"><?php esc_html_e( 'Show Map', 'knowherepro' ); ?></label></th>
			<td>
				<label for="term_show_map_value"><?php echo esc_html__('Show Map', 'knowherepro') ?></label>
				<p>
					<select name="term_show_map_value" id="term_show_map_value">
						<option value=""><?php echo esc_html__('Default', 'knowherepro') ?></option>
						<option <?php selected( $current_show_map_value, 'yes' ) ?> value="yes"><?php esc_html_e('Yes', 'knowherepro') ?></option>
						<option <?php selected( $current_show_map_value, 'no' ) ?> value="no"><?php esc_html_e('No', 'knowherepro') ?></option>
					</select>
				</p>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="term_filter_position"><?php esc_html_e( 'Filter Position', 'knowherepro' ); ?></label></th>
			<td>
				<label for="term_filter_position"><?php echo esc_html__('Filter Position', 'knowherepro') ?></label>
				<p>
					<select name="term_filter_position" id="term_filter_position">
						<option value=""><?php echo esc_html__('Default', 'knowherepro') ?></option>
						<option <?php selected( $current_filter_position, 'kw-top-position' ) ?> value="kw-top-position"><?php esc_html_e('Top', 'knowherepro') ?></option>
						<option <?php selected( $current_filter_position, 'kw-left-position' ) ?> value="kw-left-position"><?php esc_html_e('Left', 'knowherepro') ?></option>
					</select>
				</p>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="term_style"><?php esc_html_e( 'Listing Style', 'knowherepro' ); ?></label></th>
			<td>
				<label for="term_style"><?php echo esc_html__('Listing Style', 'knowherepro') ?></label>
				<p>
					<select name="term_style" id="term_style">
						<option value=""><?php echo esc_html__('Default', 'knowherepro') ?></option>
						<option <?php selected( $current_style, 'kw-type-1' ) ?> value="kw-type-1"><?php esc_html_e('Style 1', 'knowherepro') ?></option>
						<option <?php selected( $current_style, 'kw-type-2' ) ?> value="kw-type-2"><?php esc_html_e('Style 2', 'knowherepro') ?></option>
						<option <?php selected( $current_style, 'kw-type-3' ) ?> value="kw-type-3"><?php esc_html_e('Style 3 ( property )', 'knowherepro') ?></option>
						<option <?php selected( $current_style, 'kw-type-4' ) ?> value="kw-type-4"><?php esc_html_e('Style 4 ( job )', 'knowherepro') ?></option>
						<option <?php selected( $current_style, 'kw-type-5' ) ?> value="kw-type-5"><?php esc_html_e('Style 5 ( classified )', 'knowherepro') ?></option>
					</select>
				</p>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="term_view"><?php esc_html_e( 'View', 'knowherepro' ); ?></label></th>
			<td>
				<label for="term_view"><?php echo esc_html__('View', 'knowherepro') ?></label>
				<p>
					<select name="term_view" id="term_view">
						<option value=""><?php echo esc_html__('Default View', 'knowherepro') ?></option>
						<option <?php selected( $current_view, 'kw-grid-view' ) ?> value="kw-grid-view"><?php esc_html_e('Grid', 'knowherepro') ?></option>
						<option <?php selected( $current_view, 'kw-list-view' ) ?> value="kw-list-view"><?php esc_html_e('List', 'knowherepro') ?></option>
					</select>
				</p>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="term_count_columns"><?php esc_html_e( 'Count Columns', 'knowherepro' ); ?></label></th>
			<td>
				<label for="term_count_columns"><?php echo esc_html__('Count Columns', 'knowherepro') ?></label>
				<p>
					<select name="term_count_columns" id="term_count_columns">
						<option value=""><?php echo esc_html__('Default Count Columns', 'knowherepro') ?></option>
						<option <?php selected( $current_count_columns, 2 ) ?> value="2">2</option>
						<option <?php selected( $current_count_columns, 3 ) ?> value="3">3</option>
						<option <?php selected( $current_count_columns, 4 ) ?> value="4">4</option>
						<option <?php selected( $current_count_columns, 5 ) ?> value="5">5</option>
					</select>
				</p>
			</td>
		</tr>
		<tr class="form-field">

			<th scope="row" valign="top"><label for="term_color_icon"><?php echo esc_html__('Color Icon', 'knowherepro') ?></label></th>
			<td>
				<label for="term_color_icon"><?php echo esc_html__('Color Icon', 'knowherepro') ?></label>
				<p>
					<div class="colorpicker-wrap">
						<input type="text" data-alpha="false" class="term-color-picker" name="term_color_icon" value="<?php echo esc_attr($current_color_icon) ?>" />
					</div>
				</p>
			</td>
		</tr>
		<?php
		$current_image_value = '';
		if ( isset( $term->term_id ) ) {
			$current_image_value = get_term_meta( $term->term_id, 'pix_term_image', true );
		} ?>
		<?php
	}

	function save_taxonomy_custom_meta ( $term_id ) {
		if ( isset( $_POST['term_icon_value'] ) ) {
			$value = $_POST['term_icon_value'];
			$current_value = get_term_meta( $term_id, 'pix_term_icon', true );

			if ( empty( $current_value ) ) {
				$updated = update_term_meta( $term_id, 'pix_term_icon', $value );
			} else {
				$updated = update_term_meta( $term_id, 'pix_term_icon', $value, $current_value );
			}
		}

		if ( isset( $_POST['term_image_value'] ) ) {
			$value_image = $_POST['term_image_value'];
			$current_value_image = get_term_meta( $term_id, 'pix_term_image', true );

			if ( empty( $current_value_image ) ) {
				$updated = update_term_meta( $term_id, 'pix_term_image', $value_image );
			} else {
				$updated = update_term_meta( $term_id, 'pix_term_image', $value_image, $current_value_image );
			}
			update_termmeta_cache( array( $term_id ) );
		}

		if ( isset( $_POST['term_header_value'] ) ) {
			$value = $_POST['term_header_value'];
			$current_value = get_term_meta( $term_id, 'pix_term_header', true );

			if ( empty( $current_value ) ) {
				$updated = update_term_meta( $term_id, 'pix_term_header', $value );
			} else {
				$updated = update_term_meta( $term_id, 'pix_term_header', $value, $current_value );
			}
		}

		if ( isset( $_POST['term_page_layout_value'] ) ) {
			$value = $_POST['term_page_layout_value'];
			$current_value = get_term_meta( $term_id, 'pix_term_page_layout', true );

			if ( empty( $current_value ) ) {
				$updated = update_term_meta( $term_id, 'pix_term_page_layout', $value );
			} else {
				$updated = update_term_meta( $term_id, 'pix_term_page_layout', $value, $current_value );
			}
		}

		if ( isset( $_POST['term_show_map_value'] ) ) {
			$value = $_POST['term_show_map_value'];
			$current_value = get_term_meta( $term_id, 'pix_term_show_map', true );

			if ( empty( $current_value ) ) {
				$updated = update_term_meta( $term_id, 'pix_term_show_map', $value );
			} else {
				$updated = update_term_meta( $term_id, 'pix_term_show_map', $value, $current_value );
			}
		}

		if ( isset( $_POST['term_view'] ) ) {
			$value = $_POST['term_view'];
			$current_value = get_term_meta( $term_id, 'pix_term_view', true );

			if ( empty( $current_value ) ) {
				$updated = update_term_meta( $term_id, 'pix_term_view', $value );
			} else {
				$updated = update_term_meta( $term_id, 'pix_term_view', $value, $current_value );
			}
		}

		if ( isset( $_POST['term_filter_position'] ) ) {
			$value = $_POST['term_filter_position'];
			$current_value = get_term_meta( $term_id, 'pix_term_filter_position', true );

			if ( empty( $current_value ) ) {
				$updated = update_term_meta( $term_id, 'pix_term_filter_position', $value );
			} else {
				$updated = update_term_meta( $term_id, 'pix_term_filter_position', $value, $current_value );
			}
		}

		if ( isset( $_POST['term_style'] ) ) {
			$value = $_POST['term_style'];
			$current_value = get_term_meta( $term_id, 'pix_term_style', true );

			if ( empty( $current_value ) ) {
				$updated = update_term_meta( $term_id, 'pix_term_style', $value );
			} else {
				$updated = update_term_meta( $term_id, 'pix_term_style', $value, $current_value );
			}
		}

		if ( isset( $_POST['term_count_columns'] ) ) {
			$value = $_POST['term_count_columns'];
			$current_value = get_term_meta( $term_id, 'pix_term_count_columns', true );

			if ( empty( $current_value ) ) {
				$updated = update_term_meta( $term_id, 'pix_term_count_columns', $value );
			} else {
				$updated = update_term_meta( $term_id, 'pix_term_count_columns', $value, $current_value );
			}
		}

		if ( isset( $_POST['term_color_icon'] ) ) {
			$value = $_POST['term_color_icon'];
			$current_value = get_term_meta( $term_id, 'pix_term_color_icon', true );

			if ( empty( $current_value ) ) {
				$updated = update_term_meta( $term_id, 'pix_term_color_icon', $value );
			} else {
				$updated = update_term_meta( $term_id, 'pix_term_color_icon', $value, $current_value );
			}

		}

		update_termmeta_cache( array( $term_id ) );

	}

	/**
	 * Taxonomy columns
	 */
	function add_custom_tax_column( $current_columns ) {

		$input = array_shift( $current_columns );
		$new_columns = array(
			'cb' => $input,
			'pix-taxonomy-icon' => esc_html__( 'Icon', 'knowherepro' ),
		);

		$new_columns = $new_columns + $current_columns;
		return $new_columns;
	}

	function output_custom_tax_column(  $value, $name, $id ) {
		$icon_id = get_term_meta( $id, 'pix_term_icon', true );
		if ( is_numeric( $icon_id ) )  {
			$src = wp_get_attachment_image_src( $icon_id, 'thumbnail' );
			if ( isset( $src[0] ) && ! empty( $src[0] ) ) {
				echo '<div class="pix-taxonomy-icon-column_wrap media-icon">';
					echo '<img src="' . $src[0] . '" width="60px" height="60px" />';
				echo '</div>';
			}
		}
	}


	/**
	 * create an admin page
	 */
	function add_plugin_admin_menu( ) {
		$this->plugin_screen_hook_suffix = add_theme_page(
			esc_html__( 'Category Icon', 'knowherepro' ),
			esc_html__( 'Category Icon', 'knowherepro' ),
			'manage_options',
			$this->plugin_slug, array( $this, 'display_plugin_admin_page' )
		);
	}

	function plugin_admin_init() {

		register_setting( 'knowhere-category-icon', 'knowhere-category-icon', array( $this, 'save_setting_values' ) );
		add_settings_section(
			'knowhere-category-icon', null, array( $this, 'render_settings_section_title' ), 'knowhere-category-icon'
		);
		add_settings_field('taxonomies', esc_html__( 'Select Taxonomies', 'knowherepro' ), array( $this, 'render_taxonomies_select' ), 'knowhere-category-icon', 'knowhere-category-icon');


		/**
		 * Little trick to embed svg in media modal
		 * https://gist.github.com/Lewiscowles1986/44f059876ec205dd4d27
		 */
		ob_start();
		add_action('shutdown', array ($this,'on_shutdown'), 0);
		add_filter('final_output', array( $this,'fix_svg_template'));
	}

	function render_taxonomies_select ( ) {
		$taxonomies = get_taxonomies();

		// get the current selected taxonomies
		$options = get_option('knowhere-category-icon');

		$selected_taxonomies = array();

		if ( isset( $options['taxonomies'] ) ) {
			$selected_taxonomies = $options['taxonomies'];
		} ?>
		<field class="select_taxonomies">
			<?php
			if ( ! empty( $taxonomies ) || ! is_wp_error( $taxonomies ) ) {
				foreach ( $taxonomies as $key => $tax ) {
					$selected = '';
					if ( ! empty( $selected_taxonomies ) && isset( $selected_taxonomies[$key] ) &&  $selected_taxonomies[$key] == 'on' ) {
						$selected = ' checked="selected"';
					}
					$full_key = 'knowhere-category-icon[taxonomies][' . $key  . ']'; ?>
					<label for="<?php echo esc_attr($full_key); ?>">
						<input id='<?php echo esc_attr($full_key); ?>' name='<?php echo esc_attr($full_key); ?>' size='40' type='checkbox' <?php echo $selected ?>/>
						<?php echo esc_html($key) ?>
						</br>
					</label>
				<?php }
			}?>

		</field>
	<?php }

	// this should sanitize things around
	function save_setting_values( $input ) {
		return $input;
	}

	function render_settings_section_title() { ?>
		<h2><?php esc_html_e('Category Icon Options', 'knowherepro'); ?></h2>
	<?php }

	/**
	 * Render the settings page for this plugin.
	 */
	function display_plugin_admin_page() { ?>
		<div class="wrap" id="taxonomy_icons_form">
			<div id="icon-options-general" class="icon32"></div>
			<form action="options.php" method="post">
				<?php
				settings_fields('knowhere-category-icon');
				do_settings_sections('knowhere-category-icon'); ?>
				<input name="Submit" type="submit" value="<?php esc_html_e('Save Changes', 'knowherepro'); ?>" />
			</form>
		</div>
	<?php }

	function get_plugin_option( $key ) {

		$options = get_option('knowhere-category-icon');

		if ( isset( $options [$key] ) ) {
			return $options[$key];
		}

		return null;
	}

	function get_defaults() {
		return $this->default_settings;
	}

	/** Ensure compat with wp 4.4 */
	function create_the_termmeta_table( $id = false ) {
		global $wpdb;

		if ( $id !== false)
			switch_to_blog( $id );

		$max_index_length = 191;
		$charset_collate = '';

		if ( ! empty($wpdb->charset) )
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if ( ! empty($wpdb->collate) )
			$charset_collate .= " COLLATE $wpdb->collate";

		$blog_tables = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}termmeta (
		meta_id bigint(20) unsigned NOT NULL auto_increment,
		term_id bigint(20) unsigned NOT NULL default '0',
		meta_key varchar(255) default NULL,
		meta_value longtext,
		PRIMARY KEY (meta_id),
		KEY term_id (term_id),
		KEY meta_key (meta_key($max_index_length))
	) $charset_collate; ";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( $blog_tables );
	}

	function register_the_termmeta_table() {
		global $wpdb;

		//register the termmeta table with the wpdb object if this is older than 4.4
		if ( ! isset($wpdb->termmeta)) {
			$wpdb->termmeta = $wpdb->prefix . "termmeta";
			//add the shortcut so you can use $wpdb->stats
			$wpdb->tables[] = str_replace($wpdb->prefix, '', $wpdb->prefix . "termmeta");
		}
	}
	/**
	 * Allow svg files to be uploaded
	 * @param $mimes
	 *
	 * @return mixed
	 */
	function allow_svg_in_mime_types($mimes) {
		if ( ! isset( $mimes['svg'] ) ) {
			$mimes['svg'] = 'image/svg+xml';
		}
		return $mimes;
	}

	public function on_shutdown() {
		$final = '';
		$ob_levels = count(ob_get_level());
		for ($i = 0; $i < $ob_levels; $i++) {
			$final .= ob_get_clean();
		}
		echo apply_filters('final_output', $final);
	}

	function force_svg_with_visible_sizes() {
		echo '<style>
			svg, img[src*=".svg"] {
				max-width: 150px !important;
				max-height: 150px !important;
			}
		</style>';
	}

	public function fix_svg_template($content='') {
		$content = str_replace(
			'<# } else if ( \'image\' === data.type && data.sizes && data.sizes.full ) { #>',
			'<# } else if ( \'svg+xml\' === data.subtype ) { #>
				<img class="details-image" src="{{ data.url }}" draggable="false" />
			<# } else if ( \'image\' === data.type && data.sizes && data.sizes.full ) { #>',
			$content
		);
		$content = str_replace(
			'<# } else if ( \'image\' === data.type && data.sizes ) { #>',
			'<# } else if ( \'svg+xml\' === data.subtype ) { #>
				<div class="centered">
					<img src="{{ data.url }}" class="thumbnail" draggable="false" />
				</div>
			<# } else if ( \'image\' === data.type && data.sizes ) { #>',
			$content
		);
		return $content;
	}
}

if ( ! function_exists( 'add_term_meta' ) ) {
	function add_term_meta( $term_id, $meta_key, $meta_value, $unique = false ) {
		$added = add_metadata( 'term', $term_id, $meta_key, $meta_value, $unique );

		// Bust term query cache.
		if ( $added ) {
			wp_cache_set( 'last_changed', microtime(), 'terms' );
		}

		return $added;
	}
}

if ( ! function_exists( 'delete_term_meta' ) ) {
	function delete_term_meta( $term_id, $meta_key, $meta_value = '' ) {
		$deleted = delete_metadata( 'term', $term_id, $meta_key, $meta_value );

		// Bust term query cache.
		if ( $deleted ) {
			wp_cache_set( 'last_changed', microtime(), 'terms' );
		}

		return $deleted;
	}
}

if ( ! function_exists( 'get_term_meta' ) ) {
	function get_term_meta( $term_id, $key = '', $single = false ) {
		return get_metadata( 'term', $term_id, $key, $single );
	}
}

if ( ! function_exists( 'update_term_meta' ) ) {
	function update_term_meta( $term_id, $meta_key, $meta_value, $prev_value = '' ) {
		$updated = update_metadata( 'term', $term_id, $meta_key, $meta_value, $prev_value );

		// Bust term query cache.
		if ( $updated ) {
			wp_cache_set( 'last_changed', microtime(), 'terms' );
		}

		return $updated;
	}
}

if ( ! function_exists( 'update_termmeta_cache' ) ) {
	function update_termmeta_cache( $term_ids ) {
		return update_meta_cache( 'term', $term_ids );
	}
}

if ( ! function_exists( 'wp_lazyload_term_meta' ) ) {
	function wp_lazyload_term_meta( $check, $term_id ) {
		global $wp_query;

		if ( $wp_query instanceof WP_Query && ! empty( $wp_query->posts ) && $wp_query->get( 'update_post_term_cache' ) ) {
			// We can only lazyload if the entire post object is present.
			$posts = array();
			foreach ( $wp_query->posts as $post ) {
				if ( $post instanceof WP_Post ) {
					$posts[] = $post;
				}
			}

			if ( empty( $posts ) ) {
				return;
			}

			// Fetch cached term_ids for each post. Keyed by term_id for faster lookup.
			$term_ids = array();
			foreach ( $posts as $post ) {
				$taxonomies = get_object_taxonomies( $post->post_type );
				foreach ( $taxonomies as $taxonomy ) {
					// No extra queries. Term cache should already be primed by 'update_post_term_cache'.
					$terms = get_object_term_cache( $post->ID, $taxonomy );
					if ( false !== $terms ) {
						foreach ( $terms as $term ) {
							if ( ! isset( $term_ids[ $term->term_id ] ) ) {
								$term_ids[ $term->term_id ] = 1;
							}
						}
					}
				}
			}

			if ( $term_ids ) {
				update_termmeta_cache( array_keys( $term_ids ) );
			}
		}

		return $check;
	}
	add_filter( 'get_term_metadata',        'wp_lazyload_term_meta',        10, 2 );
}
