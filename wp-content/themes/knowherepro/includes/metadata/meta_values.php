<?php


if (!function_exists('knowhere_cat_sidebars')) {

	function knowhere_cat_sidebars() {

		$registered_sidebars = Knowhere_Helper::get_registered_sidebars(array());
		$registered_custom_sidebars = array(
			'' => esc_html__('Default', 'knowherepro')
		);

		if (!empty($registered_sidebars)) {
			foreach($registered_sidebars as $key => $value) {
				if (strpos($key, 'Footer Row') === false) {
					$registered_custom_sidebars[$key] = $value;
				}
			}
		}

		return $registered_custom_sidebars;

	}

}


if (!function_exists('knowhere_cat_meta_view')) {

	function knowhere_cat_meta_view() {

		$sidebar_options = knowhere_cat_sidebars();

		return array(
			'sidebar_position' => array(
				'name' => 'sidebar_position',
				'title' => esc_html__('Sidebar Position', 'knowherepro'),
				'desc' => esc_html__('Choose sidebar position', 'knowherepro'),
				'type' => 'select',
				'default' => '',
				'options' => array(
					'' => esc_html__('Default Sidebar Position', 'knowherepro'),
					'kw-no-sidebar' => esc_html__('No Sidebar', 'knowherepro'),
					'kw-left-sidebar' => esc_html__('Left Sidebar', 'knowherepro'),
					'kw-right-sidebar' => esc_html__('Right Sidebar', 'knowherepro')
				)
			),
			'sidebar' => array(
				'name' => 'sidebar',
				'title' => esc_html__('Sidebar Setting', 'knowherepro'),
				'desc' => esc_html__('Select the sidebar you would like to display.', 'knowherepro'),
				'type' => 'select',
				'default' => '',
				'options' => $sidebar_options
			),
			'overview_column_count' => array(
				'name' => 'overview_column_count',
				'title' => esc_html__('Column Count', 'knowherepro'),
				'desc' => esc_html__('This controls how many columns should be appeared on overview pages.', 'knowherepro'),
				'type' => 'select',
				'default' => '',
				'options' => array(
					'' => esc_html__('Default', 'knowherepro'),
					2 => 2,
					3 => 3
				)
			)
		);

	}

}