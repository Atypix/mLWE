<?php

if (!class_exists('Knowhere_Metadata_Product')) {

	class Knowhere_Metadata_Product {

		public $paths = array();

		public function path($file = '') {
			return (strlen($file) > 0 ? '/' . preg_replace('/^\//', '', $file) : '');
		}

		public function assetUrl($file) {
			return $this->paths['BASE_URI'] . $this->path($file);
		}

		public function __construct() {

			$this->paths = array(
				'ASSETS_DIR_NAME' => 'assets',
				'BASE_URI' => get_template_directory_uri() . '/includes/metadata/'
			);

			$this->create_product_cat_meta_table();

			add_action('admin_menu', array($this, 'product_get_postdata'));

			$this->product_cat_fields_form();

			add_action('created_term', array($this, 'save_product_cat'), 10, 3);
			add_action('edit_term', array($this, 'save_product_cat'), 10, 3);

			$this->add_enqueue_script_and_styles();
		}

		function add_enqueue_script_and_styles() {
			add_action('admin_menu', array($this, 'add_enqueue_styles'));
		}

		function add_enqueue_styles() {
			$css_file = $this->assetUrl('css/metadata-mod.css');
			wp_enqueue_style( 'knowhere_metadata-mod', $css_file );
		}

		function product_cat_fields_form() {
			add_action( 'product_cat_add_form_fields', array($this, 'add_product_cat'), 20, 2 );
			add_action( 'product_cat_edit_form_fields', array($this, 'edit_product_cat'), 20, 2 );
		}

		function add_product_cat() {
			global $product_cat_meta_array;
			$this->show_tax_add_meta_boxes($product_cat_meta_array);
		}

		function edit_product_cat($term, $taxonomy) {
			global $product_cat_meta_array;
			$this->show_tax_edit_meta_boxes($term, $taxonomy, $product_cat_meta_array);
		}

		function show_tax_add_meta_boxes($meta_boxes) {
			if (!isset($meta_boxes) || empty($meta_boxes))
				return;

			foreach ($meta_boxes as $meta_box) {
				knowhere_show_tax_add_meta_html($meta_box);
			}
		}

		function show_tax_edit_meta_boxes($term, $taxonomy, $meta_boxes) {
			if (!isset($meta_boxes) || empty($meta_boxes))
				return;

			foreach ($meta_boxes as $meta_box) {
				knowhere_show_tax_edit_meta_html($term, $taxonomy, $meta_box);
			}
		}

		function create_product_cat_meta_table() {
			global $wpdb;
			$type = 'product_cat';
			$table_name = $wpdb->prefix . $type . 'meta';
			$variable_name = $type . 'meta';
			$wpdb->$variable_name = $table_name;

			if (!empty ($wpdb->charset))
				$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
			if (!empty ($wpdb->collate))
				$charset_collate .= " COLLATE {$wpdb->collate}";

			if (!$wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
				$sql = "CREATE TABLE {$table_name} (
				meta_id bigint(20) NOT NULL AUTO_INCREMENT,
				{$type}_id bigint(20) NOT NULL default 0,
				meta_key varchar(255) DEFAULT NULL,
				meta_value longtext DEFAULT NULL,
				UNIQUE KEY meta_id (meta_id)
			) {$charset_collate};";
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);
			}

		}

		function product_get_postdata() {
			global $product_cat_meta_array;
			$product_cat_meta_array = knowhere_cat_meta_view();
		}

		function save_product_cat($term_id, $tt_id, $taxonomy) {
			if (!$term_id) return;

			global $product_cat_meta_array;

			$this->product_get_postdata();
			return knowhere_save_taxdata( $term_id, $tt_id, $taxonomy, $product_cat_meta_array );
		}

	}

	new Knowhere_Metadata_Product();

}