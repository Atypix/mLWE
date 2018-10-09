<?php

if ( !function_exists('knowhere_check_theme_options') ) {

	function knowhere_check_theme_options() {
		// check default options
		global $knowhere_settings;

		ob_start();
		include( get_theme_file_path('admin/framework/theme-options/default-options.php') );
		$options = ob_get_clean();
		$default_settings = json_decode($options, true);

		foreach ( $default_settings as $key => $value ) {

			if ( is_array($value) ) {
				foreach ( $value as $key1 => $value1 ) {
					if ((!isset($knowhere_settings[$key][$key1]) || !$knowhere_settings[$key][$key1])) {
						$knowhere_settings[$key][$key1] = $default_settings[$key][$key1];
					}
				}
			} else {
				if ( !isset($knowhere_settings[$key]) ) {
					$knowhere_settings[$key] = $default_settings[$key];
				}
			}
		}

		return $knowhere_settings;
	}

}

if ( !function_exists('knowhere_options_header_types') ) {
	function knowhere_options_header_types() {
		return array(
			'kw-type-1' => array('alt' => esc_html__('Header Type 1', 'knowherepro'), 'img' => get_theme_file_uri('admin/framework/theme-options/headers/header_1.jpg')),
			'kw-type-2' => array('alt' => esc_html__('Header Type 2', 'knowherepro'), 'img' => get_theme_file_uri('admin/framework/theme-options/headers/header_2.jpg')),
			'kw-type-3' => array('alt' => esc_html__('Header Type 3', 'knowherepro'), 'img' => get_theme_file_uri('admin/framework/theme-options/headers/header_3.jpg')),
			'kw-type-4' => array('alt' => esc_html__('Header Type 4', 'knowherepro'), 'img' => get_theme_file_uri('admin/framework/theme-options/headers/header_4.jpg')),
			'kw-type-5' => array('alt' => esc_html__('Header Type 5', 'knowherepro'), 'img' => get_theme_file_uri('admin/framework/theme-options/headers/header_5.jpg')),
			'kw-type-6' => array('alt' => esc_html__('Header Type 6', 'knowherepro'), 'img' => get_theme_file_uri('admin/framework/theme-options/headers/header_6.jpg'))
		);
	}
}

if ( !function_exists('knowhere_options_layouts') ) {
	function knowhere_options_layouts() {
		return array(
			"kw-no-sidebar" => array( 'alt' => esc_html__('Without Sidebar', 'knowherepro'), 'img' => get_theme_file_uri('admin/framework/theme-options/layouts/layout-full.jpg') ),
			"kw-left-sidebar" => array( 'alt' => esc_html__('Left Sidebar', 'knowherepro'), 'img' => get_theme_file_uri('admin/framework/theme-options/layouts/layout-left.jpg') ),
			"kw-right-sidebar" => array( 'alt' => esc_html__('Right Sidebar', 'knowherepro'), 'img' => get_theme_file_uri('admin/framework/theme-options/layouts/layout-right.jpg') )
		);
	}
}

if ( !function_exists('knowhere_job_style_layouts') ) {
	function knowhere_job_style_layouts() {
		return array(
			"kw-style-1" => array( 'alt' => esc_html__('Listing Style', 'knowherepro'), 'img' => get_theme_file_uri('admin/framework/theme-options/listing/style_1.jpg') ),
			"kw-style-2" => array( 'alt' => esc_html__('Listing Style 2', 'knowherepro'), 'img' => get_theme_file_uri('admin/framework/theme-options/listing/style_2.jpg') ),
			"kw-style-3" => array( 'alt' => esc_html__('Job Style', 'knowherepro'), 'img' => get_theme_file_uri('admin/framework/theme-options/listing/style_3.jpg') ),
			"kw-style-4" => array( 'alt' => esc_html__('Property Style', 'knowherepro'), 'img' => get_theme_file_uri('admin/framework/theme-options/listing/style_4.jpg') ),
			"kw-style-5" => array( 'alt' => esc_html__('Classified Style', 'knowherepro'), 'img' => get_theme_file_uri('admin/framework/theme-options/listing/style_5.jpg') )
		);
	}
}

if ( !function_exists('knowhere_options_sidebars') ) {
	function knowhere_options_sidebars() {
		return array(
			'kw-left-sidebar',
			'kw-right-sidebar'
		);
	}
}

if ( !function_exists('knowhere_category_layout_mode') ) {
	function knowhere_category_layout_mode() {
		return array(
			"kw-type-1" => esc_html__("Type 1", 'knowherepro'),
			"kw-type-2" => esc_html__("Type 2", 'knowherepro')
		);
	}
}

if ( !function_exists('knowhere_category_view_mode') ) {
	function knowhere_category_view_mode() {
		return array(
			"kw-view-grid" => esc_html__("Grid", 'knowherepro'),
			"kw-view-list" => esc_html__("List", 'knowherepro')
		);
	}
}

if ( !function_exists('knowhere_product_columns') ) {
	function knowhere_product_columns() {
		return array(
			"2" => "2",
			"3" => "3"
		);
	}
}

if ( !function_exists('knowhere_categories_orderby') ) {
	function knowhere_categories_orderby() {
		return array(
			"id" => esc_html__("ID", 'knowherepro'),
			"name" => esc_html__("Name", 'knowherepro'),
			"slug" => esc_html__("Slug", 'knowherepro'),
			"count" => esc_html__("Count", 'knowherepro')
		);
	}
}

if ( !function_exists('knowhere_demo_types') ) {
	function knowhere_demo_types() {
		return array(
			'listing' => array( 'alt' => esc_html__('Listing', 'knowherepro'), 'img' => get_theme_file_uri('admin/framework/theme-options/demos/listing.jpg'), 'path' => 'admin/importer/data/listing' ),
			'job' => array( 'alt' => esc_html__('Job', 'knowherepro'), 'img' => get_theme_file_uri('admin/framework/theme-options/demos/job.jpg'), 'path' => 'admin/importer/data/job' ),
		);
	}
}