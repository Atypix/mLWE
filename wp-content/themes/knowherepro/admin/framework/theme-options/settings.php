<?php
/**
 * KnowherePro Settings Options
 */

if ( !class_exists('knowhere_redux_settings') ) {

	class knowhere_redux_settings {

		public $args = array();
		public $sections = array();
		public $theme;
		public $version;
		public $ReduxFramework;

		public function __construct() {

			if ( !class_exists('ReduxFramework') ) {
				return;
			}

			$this->initSettings();
		}

		public function initSettings() {

			$this->theme = wp_get_theme();

			// Set the default arguments
			$this->setArguments();

			// Create the sections and fields
			$this->setSections();

			if ( !isset($this->args['opt_name']) ) { return; }

			$this->ReduxFramework = new ReduxFramework( $this->sections, $this->args );

		}

		public function arrayNumber($from = 0, $to = 50, $step = 1, $array = array()) {
			for ($i = $from; $i <= $to; $i += $step) {
				$array[$i] = $i;
			}
			return $array;
		}

		public function setSections() {

			$page_layouts = knowhere_options_layouts();
			$job_style_layouts = knowhere_job_style_layouts();
			$sidebars = knowhere_options_sidebars();
			$header_type = knowhere_options_header_types();

			$this->sections[] = array(
				'icon' => 'el-icon-dashboard',
				'icon_class' => 'icon',
				'title' => esc_html__('General', 'knowherepro'),
				'fields' => array(
					array(
						'id' => 'page-layout',
						'type' => 'image_select',
						'title' => esc_html__('Page Layout', 'knowherepro'),
						'options' => $page_layouts,
						'default' => 'kw-no-sidebar'
					),
					array(
						'id' => 'sidebar',
						'type' => 'select',
						'title' => esc_html__('Select Sidebar', 'knowherepro'),
						'required' => array( 'page-layout','equals', $sidebars ),
						'data' => 'sidebars',
						'default' => 'general-widget-area'
					)
				)
			);

			// Logo
			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title' => esc_html__('Logo', 'knowherepro'),
				'fields' => array(
					array(
						'id' => '112',
						'type' => 'info',
						'title' => esc_html__('If header type is like 1, 2, 6', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'logo',
						'type' => 'media',
						'url'=> true,
						'readonly' => false,
						'title' => esc_html__('Logo', 'knowherepro'),
						'default' => array(
							'url' => get_theme_file_uri('images/logo.png')
						)
					),
					array(
						'id' => '112',
						'type' => 'info',
						'title' => esc_html__('If header type is like 3, 5', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'logo_header_3',
						'type' => 'media',
						'url'=> true,
						'readonly' => false,
						'title' => esc_html__('Logo', 'knowherepro'),
						'default' => array(
							'url' => get_theme_file_uri('images/logo_v3.png')
						)
					),
					array(
						'id' => '121',
						'type' => 'info',
						'title' => esc_html__('If header type is like 4', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'logo_header_4',
						'type' => 'media',
						'url'=> true,
						'readonly' => false,
						'title' => esc_html__('Logo', 'knowherepro'),
						'default' => array(
							'url' => get_theme_file_uri('images/logo_dark_blue.png')
						)
					),
					array(
						'id' => '122',
						'type' => 'info',
						'title' => esc_html__('Favicon', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'favicon',
						'type' => 'media',
						'url'=> true,
						'readonly' => false,
						'title' => esc_html__('Favicon', 'knowherepro'),
						'default' => array(
							'url' => get_theme_file_uri('images/favicon.png')
						)
					),
				)
			);

			// Templates
			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title' => esc_html__('Front Pages', 'knowherepro'),
				'fields' => array(
					array(
						'id' => '115',
						'type' => 'info',
						'title' => esc_html__('If listing front page', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'job-how-it-works',
						'type' => 'select',
						'title' => esc_html__('Select Page for "How it works" page in front page', 'knowherepro'),
						'data' => 'pages',
						'default' => ''
					),
					array(
						'id' => '116',
						'type' => 'info',
						'title' => esc_html__('If job front page', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'job-title-job-front-page-carousel',
						'type' => 'text',
						'title' => esc_html__( 'Title for carousel on job front page template', 'knowherepro' ),
						'desc' => esc_html__( 'Title for carousel on job front page template', 'knowherepro' ),
						'default' => esc_html__('Explore Our Top Employers', 'knowherepro')
					),
					array(
						'id' => 'job-number-job-front-page-carousel',
						'type' => 'text',
						'title' => esc_html__( 'Count company', 'knowherepro' ),
						'desc' => esc_html__( 'Number of company to get for carousel', 'knowherepro' ),
						'default' => 6
					),
					array(
						'id' => 'job-columns-job-front-page-carousel',
						'type' => 'button_set',
						'title' => esc_html__('Count Columns', 'knowherepro'),
						'options' => array(
							"3" => "3",
							"4" => "4",
//							"5" => "5",
						),
						'default' => '4',
						'desc' => esc_html__( 'The number of columns for carousel', 'knowherepro' )
					),
					array(
						'id' => '117',
						'type' => 'info',
						'title' => esc_html__('If property front page', 'knowherepro'),
						'notice' => false
					),
					array(
						'id'       => 'job-gallery-property-front-page',
						'type'     => 'gallery',
						'title'    => esc_html__('Add/Edit Gallery', 'knowherepro'),
						'subtitle' => esc_html__('Create a new Gallery by selecting existing or uploading new images using the WordPress native uploader', 'knowherepro'),
						'desc'     => esc_html__('Select images for gallery on front page.', 'knowherepro'),
					)
				)
			);

			// Skin Styling
			$this->sections[] = array(
				'icon' => 'el-icon-broom',
				'icon_class' => 'icon',
				'title' => esc_html__('Skin', 'knowherepro'),
				'fields' => array(
					array(
						'id' => 'primary-color',
						'type' => 'color',
						'title' => esc_html__('Primary Color', 'knowherepro'),
						'desc' => esc_html__('Color for link and other', 'knowherepro'),
						'default' => '#70af1a',
						'validate' => 'color',
					),
					array(
						'id' => 'primary-inverse-color',
						'type' => 'color',
						'title' => esc_html__('Primary Inverse Color', 'knowherepro'),
						'desc' => esc_html__('Color for link hover and other', 'knowherepro'),
						'default' => '#70af1a',
						'validate' => 'color',
					),
					array(
						'id' => 'secondary-color',
						'type' => 'color',
						'title' => esc_html__('Secondary Color', 'knowherepro'),
						'desc' => esc_html__('This is theme Color', 'knowherepro'),
						'default' => '#70af1a',
						'validate' => 'color',
					),
					array(
						'id' => 'secondary-inverse-color',
						'type' => 'color',
						'title' => esc_html__('Secondary inverse color', 'knowherepro'),
						'default' => '#70af1a',
						'validate' => 'color',
					),
					array(
						'id' => 'page-header-bg-color',
						'type' => 'color',
						'title' => esc_html__('Page Header Background color', 'knowherepro'),
						'default' => '#222',
						'validate' => 'color',
					),
					array(
						'id' => 'page-header-overlay-bg-color',
						'type' => 'color',
						'title' => esc_html__('Page Header Overlay Background color', 'knowherepro'),
						'default' => '#000',
						'validate' => 'color',
					),
					array(
						'id' => 'selection-color',
						'type' => 'color',
						'desc' => esc_html__('The ::selection selector matches the portion of an element that is selected by a user.', 'knowherepro'),
						'title' => esc_html__('Selection background color', 'knowherepro'),
						'default'   => '#70af1a',
					),
					array(
						'id' => 'other-text-color',
						'type' => 'color',
						'title' => esc_html__('Other Text Color', 'knowherepro'),
						'desc' => esc_html__('Text color for other elements', 'knowherepro'),
						'default' => '#222',
						'validate' => 'color',
					),
					array(
						'id' => 'other-bg-color',
						'type' => 'color',
						'title' => esc_html__('Other Background Color', 'knowherepro'),
						'desc' => esc_html__('Background color for other elements', 'knowherepro'),
						'default' => '#e5eb0b',
						'validate' => 'color',
					),
					array(
						'id' => 'translucent-bg-color',
						'type' => 'color',
						'title' => esc_html__('Translucent Background Color', 'knowherepro'),
						'desc' => esc_html__('Background color for front page 2 template', 'knowherepro'),
						'default' => '#70af1a',
						'validate' => 'color',
					),
					array(
						'id' => '234',
						'type' => 'info',
						'title' => esc_html__('Job Manager', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'job-featured-label-color',
						'type' => 'color',
						'title' => esc_html__('Featured Label Text Color', 'knowherepro'),
						'desc' => '',
						'default' => '#fff',
						'validate' => 'color',
					),
					array(
						'id' => 'job-featured-label-bg-color',
						'type' => 'color',
						'title' => esc_html__('Featured Label Background Color', 'knowherepro'),
						'desc' => '',
						'default' => '#db0005',
						'validate' => 'color',
					),
					array(
						'id' => 'job-filter-submit-button-text-color',
						'type' => 'color',
						'title' => esc_html__('Job Filter Submit Button Text Color', 'knowherepro'),
						'desc' => '',
						'default' => '#222',
						'validate' => 'color',
					),
					array(
						'id' => 'job-filter-submit-button-bg-color',
						'type' => 'color',
						'title' => esc_html__('Job Filter Submit Button Background Color', 'knowherepro'),
						'desc' => '',
						'default' => '#e5eb0b',
						'validate' => 'color',
					),
					array(
						'id' => 'job-filter-submit-button-hover-bg-color',
						'type' => 'color',
						'title' => esc_html__('Job Filter Submit Hover Button Background Color', 'knowherepro'),
						'desc' => '',
						'default' => '#fff',
						'validate' => 'color',
					),
					array(
						'id' => 'job-listing-item-price-text-color',
						'type' => 'color',
						'title' => esc_html__('Listing Item Price Color', 'knowherepro'),
						'desc' => '',
						'default' => '#70af1a',
						'validate' => 'color',
					),
					array(
						'id' => '224',
						'type' => 'info',
						'title' => esc_html__('WooCommerce', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'product-button-color',
						'type' => 'color',
						'title' => esc_html__('Button Text Color', 'knowherepro'),
						'desc' => esc_html__('Color for buttons', 'knowherepro'),
						'default' => '#222',
						'validate' => 'color',
					),
					array(
						'id' => 'product-button-bg-color',
						'type' => 'color',
						'title' => esc_html__('Button Background Color', 'knowherepro'),
						'desc' => esc_html__('Background color for buttons', 'knowherepro'),
						'default' => '#e5eb0b',
						'validate' => 'color',
					),
					array(
						'id' => 'product-new-label-color',
						'type' => 'color',
						'title' => esc_html__('New Label Text Color', 'knowherepro'),
						'default' => '#fff',
						'validate' => 'color',
					),
					array(
						'id' => 'product-new-label-bg-color',
						'type' => 'color',
						'title' => esc_html__('New Label Background Color', 'knowherepro'),
						'default' => '#0073db',
						'validate' => 'color',
					),
					array(
						'id' => 'product-sale-label-color',
						'type' => 'color',
						'title' => esc_html__('Sale Label Text Color', 'knowherepro'),
						'default' => '#222',
						'validate' => 'color',
					),
					array(
						'id' => 'product-sale-label-bg-color',
						'type' => 'color',
						'title' => esc_html__('Sale Label Background Color', 'knowherepro'),
						'default' => '#e5eb0b',
						'validate' => 'color',
					)
				)
			);

			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title' => esc_html__('Typography', 'knowherepro'),
				'fields' => array(
					array(
						'id' => 'select-google-charset',
						'type' => 'switch',
						'title' => esc_html__('Select Google Font Character Sets', 'knowherepro'),
						'default' => false,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'google-charsets',
						'type' => 'button_set',
						'title' => esc_html__('Google Font Character Sets', 'knowherepro'),
						'multi' => true,
						'required' => array('select-google-charset', 'equals', true),
						'options'=> array(
							'cyrillic' => 'Cyrrilic',
							'cyrillic-ext' => 'Cyrrilic Extended',
							'greek' => 'Greek',
							'greek-ext' => 'Greek Extended',
							'khmer' => 'Khmer',
							'latin' => 'Latin',
							'latin-ext' => 'Latin Extneded',
							'vietnamese' => 'Vietnamese'
						),
						'default' => array('latin','greek-ext','cyrillic','latin-ext','greek','cyrillic-ext','vietnamese','khmer')
					),
					array(
						'id' => 'body-font',
						'type' => 'typography',
						'title' => esc_html__('Body Font', 'knowherepro'),
						'google' => true,
						'subsets' => false,
						'font-style' => false,
						'text-align' => false,
						'default'=> array(
							'color' => "#777",
							'google' => true,
							'font-weight' => '400',
							'font-family' => 'Heebo',
							'font-size' => '14px',
							'line-height' => '24px'
						),
					),
					array(
						'id' => 'h1-font',
						'type' => 'typography',
						'title' => esc_html__('H1 Font', 'knowherepro'),
						'google' => true,
						'subsets' => false,
						'font-style' => false,
						'text-align' => false,
						'line-height' => false,
						'default'=> array(
							'color' => "#222",
							'google' => true,
							'font-weight' => '400',
							'font-family' => 'Heebo',
							'font-size' => '36px',
						),
					),
					array(
						'id' => 'h2-font',
						'type' => 'typography',
						'title' => esc_html__('H2 Font', 'knowherepro'),
						'google' => true,
						'subsets' => false,
						'font-style' => false,
						'text-align' => false,
						'line-height' => false,
						'default'=> array(
							'color' => "#222",
							'google' => true,
							'font-weight' => '400',
							'font-family' => 'Heebo',
							'font-size' => '30px',
						),
					),
					array(
						'id' => 'h3-font',
						'type' => 'typography',
						'title' => esc_html__('H3 Font', 'knowherepro'),
						'google' => true,
						'subsets' => false,
						'font-style' => false,
						'text-align' => false,
						'line-height' => false,
						'default'=> array(
							'color' => "#222",
							'google' => true,
							'font-weight' => '500',
							'font-family' => 'Heebo',
							'font-size' => '18px',
						),
					),
					array(
						'id'=>'h4-font',
						'type' => 'typography',
						'title' => esc_html__('H4 Font', 'knowherepro'),
						'google' => true,
						'subsets' => false,
						'font-style' => false,
						'text-align' => false,
						'line-height' => false,
						'default'=> array(
							'color' => "#222",
							'google' => true,
							'font-weight' => '400',
							'font-family' => 'Heebo',
							'font-size' => '18px',
						),
					),
					array(
						'id' => 'h5-font',
						'type' => 'typography',
						'title' => esc_html__('H5 Font', 'knowherepro'),
						'google' => true,
						'subsets' => false,
						'font-style' => false,
						'text-align' => false,
						'line-height' => false,
						'default'=> array(
							'color' => "#222",
							'google' => true,
							'font-weight' => '400',
							'font-family' => 'Heebo',
							'font-size' => '16px',
						),
					),
					array(
						'id' => 'h6-font',
						'type' => 'typography',
						'title' => esc_html__('H6 Font', 'knowherepro'),
						'google' => true,
						'subsets' => false,
						'font-style' => false,
						'text-align' => false,
						'line-height' => false,
						'default'=> array(
							'color' => "#222",
							'google' => true,
							'font-weight' => '400',
							'font-family' => 'Heebo',
							'font-size' => '15px',
						),
					),
				)
			);

			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title' => esc_html__('Backgrounds', 'knowherepro'),
				'fields' => array(
					array(
						'id' => '1',
						'type' => 'info',
						'title' => esc_html__('Body Background', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'body-bg',
						'type' => 'background',
						'title' => esc_html__('Background', 'knowherepro')
					)
				)
			);

			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title' => esc_html__('Main Menu', 'knowherepro'),
				'fields' => array(
					array(
						'id' => '5',
						'type' => 'info',
						'title' => esc_html__('Header Light Background Color', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'header-light-bg-color',
						'type' => 'color',
						'title' => esc_html__('Background Color', 'knowherepro'),
						'default' => '#fff',
						'validate' => 'color',
					),
					array(
						'id' => '12',
						'type' => 'info',
						'title' => esc_html__( 'Top Level Menu Item', 'knowherepro' ),
						'notice' => false
					),
					array(
						'id' => 'menu-font',
						'type' => 'typography',
						'title' => esc_html__('Menu Font', 'knowherepro'),
						'google' => true,
						'subsets' => false,
						'font-style' => false,
						'text-align' => false,
						'color' => false,
						'default'=> array(
							'google' => true,
							'font-weight' => '400',
							'font-family'=> 'Heebo',
							'font-size' => '14px',
							'line-height' => '24px'
						),
					),
					array(
						'id' => 'menu-text-transform',
						'type' => 'button_set',
						'title' => esc_html__('Text Transform', 'knowherepro'),
						'options' => array(
							'none' => esc_html__('None', 'knowherepro'),
							'capitalize' => esc_html__('Capitalize', 'knowherepro'),
							'uppercase' => esc_html__('Uppercase', 'knowherepro'),
							'lowercase' => esc_html__('Lowercase', 'knowherepro'),
							'initial' => esc_html__('Initial', 'knowherepro')
						),
						'default' => 'initial'
					),
					array(
						'id' => 'primary-toplevel-link-color',
						'type' => 'link_color',
						'active' => false,
						'hover' => true,
						'title' => esc_html__('Link Color', 'knowherepro'),
						'default' => array(
							'regular' => '#fff',
							'hover' => '#fff'
						)
					),
					array(
						'id'=>'111',
						'type' => 'info',
						'title' => esc_html__('Top Level Menu Item If header type is like 4', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'primary-toplevel-header-type-4-link-color',
						'type' => 'link_color',
						'active' => true,
						'hover' => true,
						'title' => esc_html__('Link Color', 'knowherepro'),
						'default' => array(
							'regular' => '#222',
							'hover' => '#0054a0',
							'active' => '#0054a0'
						)
					),
					array(
						'id'=>'13411',
						'type' => 'info',
						'title' => esc_html__('Top Level Menu Item If header type is like 5', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'header-type-5-menu-font',
						'type' => 'typography',
						'title' => esc_html__('Menu Font', 'knowherepro'),
						'google' => true,
						'subsets' => false,
						'font-style' => false,
						'text-align' => false,
						'color' => false,
						'default'=> array(
							'google' => true,
							'font-weight' => '400',
							'font-family'=> 'Heebo',
							'font-size' => '12px',
							'line-height' => '24px'
						),
					),
					array(
						'id'=>'231',
						'type' => 'info',
						'title' => esc_html__('Sub Menu', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'sub-menu-font',
						'type' => 'typography',
						'title' => esc_html__('Sub Menu Font', 'knowherepro'),
						'google' => true,
						'subsets' => false,
						'font-style' => false,
						'text-align' => false,
						'color' => false,
						'default'=> array(
							'google' => true,
							'font-weight' => '400',
							'font-family'=> 'Heebo',
							'font-size' => '14px',
							'line-height' => '24px'
						),
					),
					array(
						'id' => 'sub-menu-text-color',
						'type' => 'link_color',
						'title' => esc_html__('Link Color', 'knowherepro'),
						'default' => array(
							'regular' => '#222222',
							'hover' => '#70af1a',
							'active' => '#70af1a'
						)
					),
					array(
						'id' => '5',
						'type' => 'info',
						'title' => esc_html__( 'Vertical Menu', 'knowherepro' ),
						'style' => 'info',
						'notice' => false
					),
					array(
						'id' => '1',
						'type' => 'info',
						'title' => esc_html__( 'Top Level Menu Item', 'knowherepro' ),
						'notice' => false
					),
					array(
						'id' => 'vr-menu-font',
						'type' => 'typography',
						'title' => esc_html__('Menu Font', 'knowherepro'),
						'google' => true,
						'subsets' => false,
						'font-style' => false,
						'text-align' => false,
						'color' => false,
						'default'=> array(
							'google' => true,
							'font-weight' => '400',
							'font-family'=> 'Heebo',
							'font-size' => '14px',
							'line-height' => '24px'
						),
					),
					array(
						'id' => 'vr-menu-text-transform',
						'type' => 'button_set',
						'title' => esc_html__('Text Transform', 'knowherepro'),
						'options' => array(
							'none' => esc_html__('None', 'knowherepro'),
							'capitalize' => esc_html__('Capitalize', 'knowherepro'),
							'uppercase' => esc_html__('Uppercase', 'knowherepro'),
							'lowercase' => esc_html__('Lowercase', 'knowherepro'),
							'initial' => esc_html__('Initial', 'knowherepro')
						),
						'default' => 'uppercase'
					),
					array(
						'id' => 'vr-primary-toplevel-link-color',
						'type' => 'link_color',
						'active' => true,
						'hover' => true,
						'title' => esc_html__('Link Color', 'knowherepro'),
						'default' => array(
							'regular' => '#fff',
							'hover' => '#70af1a',
							'active' => '#70af1a'
						)
					),
					array(
						'id'=>'23421',
						'type' => 'info',
						'title' => esc_html__('Sub Menu', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'vr-sub-menu-font',
						'type' => 'typography',
						'title' => esc_html__('Sub Menu Font', 'knowherepro'),
						'google' => true,
						'subsets' => false,
						'font-style' => false,
						'text-align' => false,
						'color' => false,
						'default'=> array(
							'google' => true,
							'font-weight' => '400',
							'font-family'=> 'Heebo',
							'font-size' => '14px',
							'line-height' => '24px'
						),
					),
					array(
						'id' => 'vr-sub-menu-text-color',
						'type' => 'link_color',
						'title' => esc_html__('Link Color', 'knowherepro'),
						'default' => array(
							'regular' => '#fff',
							'hover' => '#70af1a',
							'active' => '#70af1a'
						)
					),
				)
			);

			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title' => esc_html__('Footer', 'knowherepro'),
				'fields' => array(
					array(
						'id' => 'footer-bg',
						'type' => 'background',
						'title' => esc_html__('Background', 'knowherepro'),
						'default' => array(
							'background-color' => '#272727',
							'background-image' => '',
							'background-size' => 'cover',
							'background-position' => 'center center',
							'background-repeat' => 'no-repeat'
						)
					),
					array(
						'id'=>'13423421',
						'type' => 'info',
						'title' => esc_html__('Footer Row Top', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'footer-heading-color',
						'type' => 'color',
						'title' => esc_html__('Heading Color', 'knowherepro'),
						'default' => '#ffffff',
						'validate' => 'color',
					),
					array(
						'id' => 'footer-text-color',
						'type' => 'color',
						'title' => esc_html__('Text Color', 'knowherepro'),
						'default' => '#a5a5a5',
						'validate' => 'color',
					),
					array(
						'id' => 'footer-link-color',
						'type' => 'link_color',
						'active' => false,
						'title' => esc_html__('Link Color', 'knowherepro'),
						'default' => array(
							'regular' => '#70af1a',
							'hover' => '#70af1a',
						)
					),
					array(
						'id'=>'1786421',
						'type' => 'info',
						'title' => esc_html__('Footer Middle Top', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'footer-row-middle-bg-color',
						'type' => 'color',
						'title' => esc_html__('Background Color', 'knowherepro'),
						'default' => '#222',
						'validate' => 'color',
					),
					array(
						'id' => 'footer-row-middle-heading-color',
						'type' => 'color',
						'title' => esc_html__('Heading Color', 'knowherepro'),
						'default' => '#ffffff',
						'validate' => 'color',
					),
					array(
						'id' => 'footer-row-middle-text-color',
						'type' => 'color',
						'title' => esc_html__('Text Color', 'knowherepro'),
						'default' => '#a5a5a5',
						'validate' => 'color',
					),
					array(
						'id' => 'footer-row-middle-link-color',
						'type' => 'link_color',
						'active' => false,
						'title' => esc_html__('Link Color', 'knowherepro'),
						'default' => array(
							'regular' => '#70af1a',
							'hover' => '#70af1a',
						)
					),
					array(
						'id'=>'13086421',
						'type' => 'info',
						'title' => esc_html__('Copyright', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'footer-copyright-bg-color',
						'type' => 'color',
						'title' => esc_html__('Copyright Background Color', 'knowherepro'),
						'default' => '#222',
						'validate' => 'color',
					),
					array(
						'id' => 'copyright-social-link-color',
						'type' => 'color',
						'title' => esc_html__('Social Link Color', 'knowherepro'),
						'default' => '#fff',
						'validate' => 'color',
					),
					array(
						'id'=>'1086421',
						'type' => 'info',
						'title' => esc_html__('Other', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'footer-submit-button-color',
						'type' => 'color',
						'title' => esc_html__('Footer Submit Button Color', 'knowherepro'),
						'default' => '#fff',
						'validate' => 'color',
					)
				)
			);

			// Header Settings
			$this->sections[] = array(
				'icon' => 'el-icon-website',
				'icon_class' => 'icon',
				'title' => esc_html__('Header', 'knowherepro'),
				'fields' => array(
					array(
						'id' => 'header-sticky-menu',
						'type' => 'switch',
						'title' => esc_html__('Sticky Navigation', 'knowherepro'),
						'default' => true,
						'desc' => esc_html__('The sticky navigation menu is a vital part of a website, helping users move between pages and find desired information.', 'knowherepro'),
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => '3',
						'type' => 'info',
						'title' => esc_html__('Social Links', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'show-header-social-links',
						'type' => 'switch',
						'title' => __('Show Social Links', 'knowherepro'),
						'default' => false,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => "header-social-linkedin",
						'type' => 'text',
						'title' => esc_html__('LinkedIn', 'knowherepro'),
						'required' => array('show-header-social-links', 'equals', true),
						'default' => '#'
					),
					array(
						'id' => "header-social-tumblr",
						'type' => 'text',
						'title' => esc_html__('Tumblr', 'knowherepro'),
						'required' => array('show-header-social-links', 'equals', true),
						'default' => '#'
					),
					array(
						'id' => "header-social-vimeo",
						'type' => 'text',
						'title' => esc_html__('Vimeo', 'knowherepro'),
						'required' => array('show-header-social-links', 'equals', true),
						'default' => '#'
					),
					array(
						'id' => "header-social-youtube",
						'type' => 'text',
						'title' => esc_html__('Youtube', 'knowherepro'),
						'required' => array('show-header-social-links', 'equals', true),
						'default' => '#'
					),
					array(
						'id' => "header-social-facebook",
						'type' => 'text',
						'title' => esc_html__('Facebook', 'knowherepro'),
						'required' => array('show-header-social-links', 'equals', true),
						'default' => '#'
					),
					array(
						'id' => "header-social-twitter",
						'type' => 'text',
						'title' => esc_html__('Twitter', 'knowherepro'),
						'required' => array('show-header-social-links', 'equals', true),
						'default' => '#'
					),
					array(
						'id' => "header-social-instagram",
						'type' => 'text',
						'title' => esc_html__('Instagram', 'knowherepro'),
						'required' => array('show-header-social-links', 'equals', true),
						'default' => '#'
					),
					array(
						'id' => "header-social-flickr",
						'type' => 'text',
						'title' => esc_html__('Flickr', 'knowherepro'),
						'required' => array('show-header-social-links', 'equals', true),
						'default' => '#'
					),
					array(
						'id' => '1',
						'type' => 'info',
						'title' => esc_html__('If header type is like 1', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'header-type-1-show-top-bar',
						'type' => 'switch',
						'title' => esc_html__('Show Topbar', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'header-type-1-button-color',
						'type' => 'color',
						'title' => esc_html__('Button Color', 'knowherepro'),
						'default' => '#e5eb0b',
						'validate' => 'color',
					),
					array(
						'id' => 'header-type-1-show-location',
						'type' => 'switch',
						'title' => esc_html__('Show Location in topbar', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'header-type-1-show-login',
						'type' => 'switch',
						'title' => esc_html__('Show Login in topbar', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'header-type-1-show-language',
						'type' => 'switch',
						'title' => esc_html__('Show Language in topbar', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'header-type-1-show-link-to-cart',
						'type' => 'switch',
						'title' => esc_html__('Show Link to Cart', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'header-type-1-show-search-and-login',
						'type' => 'button_set',
						'multi' => true,
						'title' => esc_html__('Show Search, Login', 'knowherepro'),
						'options' => array(
							'search' => esc_html__('Search', 'knowherepro'),
							'login' => esc_html__('Login', 'knowherepro'),
						),
						'default' => 'search'
					),
					array(
						'id' => 'header-type-1-show-button-add-listing',
						'type' => 'switch',
						'title' => esc_html__('Show button "Add Listing"', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => '5',
						'type' => 'info',
						'title' => esc_html__('If header type is like 2', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'header-type-2-bg',
						'type' => 'background',
						'title' => esc_html__('Background', 'knowherepro'),
						'default' => array(
							'background-image' => '',
							'background-size' => 'cover',
							'background-position' => 'center center',
							'background-repeat' => 'no-repeat'
						),
						'background-color' => false
					),
					array(
						'id' => 'header-type-2-show-login',
						'type' => 'switch',
						'title' => esc_html__('Show Login', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'header-type-2-show-button-add-listing',
						'type' => 'switch',
						'title' => esc_html__('Show button "Add Listing"', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => '2',
						'type' => 'info',
						'title' => esc_html__('If header type is like 3', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'show-header-type-3-top-bar',
						'type' => 'switch',
						'title' => __('Show Top Bar', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => "header-type-3-social-phone",
						'type' => 'text',
						'title' => esc_html__('Phone', 'knowherepro'),
						'required' => array('show-header-type-3-top-bar','equals',true),
						'default' => '8 800 123-456-789'
					),
					array(
						'id' => "header-type-3-social-email",
						'type' => 'text',
						'title' => esc_html__('Email', 'knowherepro'),
						'required' => array('show-header-type-3-top-bar','equals',true),
						'default' => 'mail@knowhere.com'
					),
					array(
						'id' => 'header-type-3-show-login',
						'type' => 'switch',
						'title' => esc_html__('Show Login', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'header-type-3-show-link-to-cart',
						'type' => 'switch',
						'title' => esc_html__('Show Link to Cart', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'header-type-3-show-search',
						'type' => 'switch',
						'title' => esc_html__('Show Search', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'header-type-3-show-button-add-listing',
						'type' => 'switch',
						'title' => esc_html__('Show button "Add Listing"', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => '2324',
						'type' => 'info',
						'title' => esc_html__('If header type is like 4', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'header-type-4-show-login',
						'type' => 'switch',
						'title' => esc_html__('Show Login', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => "header-type-4-social-phone",
						'type' => 'text',
						'title' => esc_html__('Phone', 'knowherepro'),
						'default' => '8 800 123-456-789'
					),
					array(
						'id' => "header-type-4-social-email",
						'type' => 'text',
						'title' => esc_html__('Email', 'knowherepro'),
						'default' => 'info@knowhere.com'
					),
					array(
						'id' => 'header-type-4-show-language',
						'type' => 'switch',
						'title' => esc_html__('Show Language', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'header-type-4-show-button-add-listing',
						'type' => 'switch',
						'title' => esc_html__('Show button "Add Listing"', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => '29421',
						'type' => 'info',
						'title' => esc_html__('If header type is like 5', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'header-type-5-show-language',
						'type' => 'switch',
						'title' => esc_html__('Show Language in topbar', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'header-type-5-show-button-add-listing',
						'type' => 'switch',
						'title' => esc_html__('Show button "Add Listing"', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'header-type-5-search-form-bg-color',
						'type' => 'color',
						'title' => esc_html__('Search Form Background Color', 'knowherepro'),
						'default' => '#5ab291',
						'validate' => 'color'
					),
//					array(
//						'id' => 'header-type-5-button-link-color',
//						'type' => 'link_color',
//						'active' => false,
//						'hover' => true,
//						'title' => esc_html__('Button Link Color', 'knowherepro'),
//						'default' => array(
//							'regular' => '#65daae',
//							'hover' => '#fff'
//						)
//					),
					array(
						'id' => '2932421',
						'type' => 'info',
						'title' => esc_html__('If header type is like 6', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'header-type-6-show-login',
						'type' => 'switch',
						'title' => esc_html__('Show Login', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'header-type-6-show-link-to-cart',
						'type' => 'switch',
						'title' => esc_html__('Show Link to Cart', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'header-type-6-show-button-add-listing',
						'type' => 'switch',
						'title' => esc_html__('Show button "Add Listing"', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'header-type-6-bg-color',
						'type' => 'color',
						'active' => false,
						'hover' => true,
						'title' => esc_html__('Header Background Color', 'knowherepro'),
						'default' => '#5ab291',
						'validate' => 'color'
					),
					array(
						'id' => 'header-type-6-logo-container-bg-color',
						'type' => 'color',
						'active' => false,
						'hover' => true,
						'title' => esc_html__('Logo Background Color', 'knowherepro'),
						'default' => '#60c6a0',
						'validate' => 'color'
					),
				)
			);

			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title' => esc_html__('Page Header Type', 'knowherepro'),
				'fields' => array(
					array(
						'id' => 'header-type',
						'type' => 'image_select',
						'full_width' => true,
						'title' => esc_html__('Header Type', 'knowherepro'),
						'options' => $header_type,
						'default' => 'kw-type-1'
					),
					array(
						'id' => 'header-type-4-bg-color',
						'type' => 'color',
						'title' => esc_html__('Background Color', 'knowherepro'),
						'desc' => esc_html__('Background color for header type 4', 'knowherepro'),
						'default' => '#00294d',
						'validate' => 'color',
						'required' => array( 'header-type','equals', array('kw-type-4') )
					),
				)
			);

			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title' => esc_html__('Listing Header Type', 'knowherepro'),
				'fields' => array(
					array(
						'id' => 'listing-header-type',
						'type' => 'image_select',
						'full_width' => true,
						'title' => esc_html__('Listing Header Type', 'knowherepro'),
						'options' => $header_type,
						'default' => 'kw-type-1'
					),
				)
			);

			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title' => esc_html__('Product Header Type', 'knowherepro'),
				'fields' => array(
					array(
						'id' => 'product-header-type',
						'type' => 'image_select',
						'full_width' => true,
						'title' => esc_html__('Product Header Type', 'knowherepro'),
						'options' => $header_type,
						'default' => 'kw-type-1'
					),
				)
			);

			$this->sections[] = array(
				'icon' => 'el-icon-website',
				'icon_class' => 'icon',
				'title' => esc_html__('Pages & Posts', 'knowherepro'),
				'fields' => array(
					array(
						'id' => 'show-pagetitle',
						'type' => 'switch',
						'title' => esc_html__('Show Page Title', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'show-breadcrumbs',
						'type' => 'switch',
						'title' => esc_html__('Show Breadcrumbs', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'align-title-and-breadcrumbs',
						'type' => 'button_set',
						'title' => esc_html__('Align Title and Breadcrumbs', 'knowherepro'),
						'options' => array(
							'align-left' => esc_html__('Left', 'knowherepro'),
							'align-center' => esc_html__('Center', 'knowherepro')
						),
						'default' => 'align-left',
					),
					array(
						'id' => 'page-header-upload',
						'type' => 'media',
						'url'=> true,
						'readonly' => false,
						'title' => esc_html__('Page Header Background Image', 'knowherepro'),
						'default' => ''
					),
				)
			);

			// Blog
			$this->sections[] = array(
				'icon' => 'el-icon-file',
				'icon_class' => 'icon',
				'title' => esc_html__('Blog', 'knowherepro'),
				'fields' => array(

				)
			);

			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title' => esc_html__('Blog Post', 'knowherepro'),
				'fields' => array(
					array(
						'id' => 'post-metas',
						'type' => 'button_set',
						'title' => esc_html__('Post Meta', 'knowherepro'),
						'multi' => true,
						'options'=> array(
							'date' => esc_html__('Date', 'knowherepro'),
							'cats' => esc_html__('Categories', 'knowherepro'),
							'tags' => esc_html__('Tags', 'knowherepro'),
							'-' => esc_html__('None', 'knowherepro')
						),
						'default' => array( 'date','cats', 'tags', '-' )
					),
					array(
						'id' => 'excerpt-count-thumbs',
						'type' => 'text',
						'title' => esc_html__( 'Excerpt Length', 'knowherepro' ),
						'desc' => esc_html__( 'The number of words for blog ', 'knowherepro' ),
						'default' => '120'
					),
				)
			);

			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title' => esc_html__('Post Archive', 'knowherepro'),
				'fields' => array(
					array(
						'id' => 'post-archive-layout',
						'type' => 'image_select',
						'title' => esc_html__('Page Layout', 'knowherepro'),
						'options' => $page_layouts,
						'default' => 'kw-right-sidebar'
					),
				)
			);

			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title' => esc_html__('Single Post', 'knowherepro'),
				'fields' => array(
					array(
						'id' => 'post-single-layout',
						'type' => 'image_select',
						'title' => esc_html__('Page Layout', 'knowherepro'),
						'options' => $page_layouts,
						'default' => 'kw-right-sidebar'
					),
					array(
						'id' => 'single-post-metas',
						'type' => 'button_set',
						'title' => esc_html__('Post Meta', 'knowherepro'),
						'multi' => true,
						'options'=> array(
							'categories' => esc_html__('Categories', 'knowherepro'),
							'date' => esc_html__('Date', 'knowherepro'),
							'comments' => esc_html__('Comments', 'knowherepro'),
							'-' => esc_html__('None', 'knowherepro')
						),
						'required' => array( 'post-single-layout','equals', array('kw-no-sidebar') ),
						'desc' => esc_html__('Located at the top of the post', 'knowherepro'),
						'default' => array( 'categories', 'date','author', 'comments', '-' )
					),
					array(
						'id' => 'post-breadcrumbs',
						'type' => 'switch',
						'title' => esc_html__('Show Breadcrumbs', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'post-tag',
						'type' => 'switch',
						'title' => esc_html__('Show Tags', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'post-nav',
						'type' => 'switch',
						'title' => esc_html__('Prev/Next Navigation', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'post-author',
						'type' => 'switch',
						'title' => esc_html__('Show Author Info Box', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'post-related-posts',
						'type' => 'switch',
						'title' => esc_html__('Show Related Posts', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'post-comments',
						'type' => 'switch',
						'title' => esc_html__('Show Comments', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => '1',
						'type' => 'info',
						'title' => esc_html__('Social Links', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'post-single-share',
						'type' => 'switch',
						'title' => esc_html__('Show Social Links', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'post-share-facebook',
						'type' => 'switch',
						'title' => esc_html__('Enable Facebook Share', 'knowherepro'),
						'required' => array('post-single-share','equals',true),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'post-share-twitter',
						'type' => 'switch',
						'title' => esc_html__('Enable Twitter Share', 'knowherepro'),
						'required' => array('post-single-share','equals',true),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'post-share-googleplus',
						'type' => 'switch',
						'title' => esc_html__('Enable Google Plus Share', 'knowherepro'),
						'required' => array('post-single-share','equals',true),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'post-share-pinterest',
						'type' => 'switch',
						'title' => esc_html__('Enable Pinterest Share', 'knowherepro'),
						'required' => array('post-single-share','equals',true),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'post-share-email',
						'type' => 'switch',
						'title' => esc_html__('Enable Email Share', 'knowherepro'),
						'required' => array('post-single-share','equals',true),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
				)
			);

			$this->sections[] = array(
				'title'  => esc_html__( 'Price & Currency', 'knowherepro' ),
				'id'     => 'price-format',
				'desc'   => '',
				'icon'   => 'el-icon-usd el-icon-small',
				'fields'		=> array(
					array(
						'id'		=> 'currency_symbol',
						'type'		=> 'text',
						'title'		=> esc_html__( 'Currency Symbol', 'knowherepro' ),
						'read-only'	=> false,
						'default'	=> '$',
						'subtitle'	=> esc_html__( 'Provide currency sign. For Example: $.', 'knowherepro' ),
					),
					array(
						'id'		=> 'decimals',
						'type'		=> 'select',
						'title'		=> esc_html__( 'Number of decimal points?', 'knowherepro' ),
						'read-only'	=> false,
						'options'	=> array(
							'0'	=> '0',
							'1'	=> '1',
							'2'	=> '2',
							'3'	=> '3',
							'4'	=> '4',
							'5'	=> '5',
							'6'	=> '6',
							'7'	=> '7',
							'8'	=> '8',
							'9'	=> '9',
							'10' => '10',
						),
						'default'	=> '0',
						'subtitle'	=> '',
					),
					array(
						'id' => 'currency_pos',
						'type'		=> 'select',
						'title'		=> esc_html__( 'This controls the position of the currency symbol.', 'knowherepro' ),
						'options'  => array(
							'left'        => __( 'Left', 'knowherepro' ),
							'right'       => __( 'Right', 'knowherepro' ),
						),
						'default'	=> 'left'
					),
					array(
						'id'		=> 'decimal_point_separator',
						'type'		=> 'text',
						'title'		=> esc_html__( 'Decimal Point Separator', 'knowherepro' ),
						'read-only'	=> false,
						'default'	=> '.',
						'subtitle'	=> esc_html__( 'Provide the decimal point separator. For Example: .', 'knowherepro' ),
					),
					array(
						'id'		=> 'thousands_separator',
						'type'		=> 'text',
						'title'		=> esc_html__( 'Thousands Separator', 'knowherepro' ),
						'read-only'	=> false,
						'default'	=> ',',
						'subtitle'	=> esc_html__( 'Provide the thousands separator. For Example: ,', 'knowherepro' ),
					),
					array(
						'id'     => 'pricerang',
						'type'   => 'info',
						'notice' => false,
						'style'  => 'info',
						'title'  => esc_html__( 'Search Price Range for price slider', 'knowherepro' ),
						'desc'   => ''
					),
					array(
						'id' => 'show-search-price',
						'type' => 'switch',
						'title' => esc_html__('Show Price Slider', 'knowherepro'),
						'desc' => esc_html__(' ', 'knowherepro'),
						'default' => false,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id'		=> 'min_price',
						'type'		=> 'text',
						'title'		=> esc_html__( 'Minimum Price', 'knowherepro' ),
						'read-only'	=> false,
						'default'	=> '500',
						'subtitle'	=> '',
					),
					array(
						'id'		=> 'max_price',
						'type'		=> 'text',
						'title'		=> esc_html__( 'Maximum Price', 'knowherepro' ),
						'read-only'	=> false,
						'default'	=> '220000',
						'subtitle'	=> '',
					),
				),
			);

			// Job Manager
			$this->sections[] = array(
				'icon' => 'el-icon-picture',
				'icon_class' => 'icon',
				'title' => esc_html__('Job Manager', 'knowherepro'),
				'fields' => array(
					array(
						'id' => 'job-type-fields',
						'type' => 'select',
						'title' => esc_html__('Select type fields', 'knowherepro'),
						'options' => array(
							'listing' => esc_html__('Listing', 'knowherepro'),
							'job' => esc_html__('Job', 'knowherepro'),
							'property' => esc_html__('Property', 'knowherepro'),
						),
						'default' => 'listing'
					),
					array(
						'id' => 'name-of-listing-singular',
						'type' => 'text',
						'title' => esc_html__('Singular name of listing', 'knowherepro'),
						'default' => esc_html__('Listing', 'knowherepro')
					),
					array(
						'id' => 'name-of-listing-plural',
						'type' => 'text',
						'title' => esc_html__('Plural name of listing', 'knowherepro'),
						'default' => esc_html__('Listings', 'knowherepro')
					),
					array(
						'id' => 'before-text-of-listing',
						'type' => 'text',
						'title' => esc_html__('Text before "Listing" on the button in the header', 'knowherepro'),
						'desc' => esc_html__('e.g.: type "Post a" for the "Post a Listing" button', 'knowherepro'),
						'default' => esc_html__('Add', 'knowherepro')
					),
					array(
						'id' => 'job-filter-style-position',
						'type' => 'button_set',
						'title' => esc_html__('Filter Style Position', 'knowherepro'),
						'options' => array(
							'kw-top-position' => esc_html__('Top', 'knowherepro'),
							'kw-left-position' => esc_html__('Left', 'knowherepro'),
						),
						'default' => 'kw-top-position',
						'desc' => esc_html__( 'Select filter style position for listings', 'knowherepro' )
					),
					array(
						'id' => 'job-filter-left-position-extend',
						'type' => 'switch',
						'title' => esc_html__('Extend to Classified', 'knowherepro'),
						'default' => true,
						'required' => array(
							'job-filter-style-position',
							'equals',
							array( 'kw-left-position' )
						),
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'job-filter-left-extend-type',
						'type' => 'button_set',
						'title' => esc_html__('Default Content for explore', 'knowherepro'),
						'options' => array(
							'kw-grid-view' => esc_html__('Grid', 'knowherepro'),
							'kw-list-view' => esc_html__('List', 'knowherepro'),
							'kw-map-view' => esc_html__('Map', 'knowherepro'),
						),
						'required' => array(
							'job-filter-left-position-extend',
							'equals',
							array( true )
						),
						'default' => 'kw-grid-view',
						'desc' => esc_html__( 'Select default content for explore', 'knowherepro' )
					),
					array(
						'id' => 'job-listings-columns',
						'type' => 'button_set',
						'title' => esc_html__('Count Columns', 'knowherepro'),
						'options' => array(
							"2" => "2",
							"3" => "3",
							"4" => "4",
							"5" => "5"
						),
						'default' => '3',
						'desc' => esc_html__( 'The number of columns for listings', 'knowherepro' )
					),
					array(
						'id' => 'job-excerpt-count-content',
						'type' => 'text',
						'title' => esc_html__( 'Content Length', 'knowherepro' ),
						'desc' => esc_html__( 'The number of words for content ', 'knowherepro' ),
						'default' => '120'
					),
					array(
						'id' => 'job-show-phone-in-popup',
						'type' => 'switch',
						'title' => esc_html__('Show phone in popup', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => '129',
						'type' => 'info',
						'title' => esc_html__('Listing', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'job-listings-style',
						'type' => 'button_set',
						'title' => esc_html__('Style', 'knowherepro'),
						'options' => array(
							"kw-type-1" => esc_html__('Style 1', 'knowherepro'),
							"kw-type-2" => esc_html__('Style 2', 'knowherepro'),
							"kw-type-3" => esc_html__('Style 3 ( property )', 'knowherepro'),
							"kw-type-4" => esc_html__('Style 4 ( job )', 'knowherepro'),
							"kw-type-5" => esc_html__('Style 5 ( classified )', 'knowherepro')
						),
						'default' => 'kw-type-1',
						'desc' => esc_html__( 'Select style for listings', 'knowherepro' )
					),
					array(
						'id' => 'job-category-view',
						'type' => 'button_set',
						'title' => esc_html__('Select view', 'knowherepro'),
						'options' => array(
							'kw-grid-view' => esc_html__('Grid', 'knowherepro'),
							'kw-list-view' => esc_html__('List', 'knowherepro'),
						),
						'required' => array( 'job-listings-style','equals', array('kw-type-1', 'kw-type-3') ),
						'default' => 'kw-grid-view'
					),
					array(
						'id' => 'job-pintpoint-event',
						'type' => 'button_set',
						'title' => esc_html__('Pintpoint Event', 'knowherepro'),
						'options' => array(
							'click' => esc_html__('Click', 'knowherepro'),
							'hover' => esc_html__('Hover', 'knowherepro'),
						),
						'default' => 'click'
					),
					array(
						'id' => 'job-show-label-open-hours',
						'type' => 'switch',
						'title' => esc_html__('Show Label Open Hours', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'job-show-label-featured',
						'type' => 'switch',
						'title' => esc_html__('Show Label Featured', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'job-label-featured-text',
						'type' => 'text',
						'title' => esc_html__( 'Featured Label Text', 'knowherepro' ),
						'required' => array( 'job-show-label-featured','equals', true ),
						'default' => esc_html__('Featured', 'knowherepro')
					),
					array(
						'id' => '29',
						'type' => 'info',
						'title' => esc_html__('Footer Settings', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'job-get-widgets-from-page',
						'type' => 'select',
						'title' => esc_html__('Get widgets for footer from page', 'knowherepro'),
						'desc' => esc_html__('Get widgets for footer from page on listing pages. You can model the footer of any page and then use it to the listing pages', 'knowherepro'),
						'data' => 'pages',
						'default' => ''
					),
				)
			);

			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title' => esc_html__( 'Property Settings', 'knowherepro' ),
				'fields' => array(
					array(
						'id'		=> 'area_prefix_default',
						'type'		=> 'select',
						'title'		=> esc_html__( 'Default area prefix', 'knowherepro' ),
						'subtitle'	=> esc_html__( 'Default option for area prefix.', 'knowherepro' ),
						'options'	=> array(
							'SqFt' => 'Square Feet - ft?',
							'm?' => 'Square Meters - m?',
						),
						'default' => 'SqFt'
					),
					array(
						'id'		=> 'unit_sqft_text',
						'type'		=> 'text',
						'title'		=> esc_html__( 'Square Feet Text', 'knowherepro' ),
						'subtitle'	=> esc_html__( 'Enter text for square feet', 'knowherepro' ),
						'default' => 'sq ft'
					),
					array(
						'id'		=> 'unit_square_meter_text',
						'type'		=> 'text',
						'title'		=> esc_html__( 'Square Meters Text', 'knowherepro' ),
						'subtitle'	=> esc_html__( 'Enter text for square meters', 'knowherepro' ),
						'default' => 'm?'
					),
					array(
						'id'       => 'beds_baths_search',
						'type'     => 'select',
						'title'    => esc_html__( 'Bedrooms, Bathrooms', 'knowherepro' ),
						'subtitle'    => esc_html__( 'Search criteria for bedrooms and bathrooms', 'knowherepro' ),
						'desc'     => '',
						'options'  => array(
							'equal' => esc_html__('Equal', 'knowherepro'),
							'greater' => esc_html__('Greater', 'knowherepro')
						),
						'default' => 'equal'
					),
				)
			);

			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title' => esc_html__( 'Radius', 'knowherepro' ),
				'fields' => array(
					array(
						'id' => 'min_radius',
						'type' => 'slider',
						'title' => esc_html__('Min Radius', 'knowherepro'),
						'subtitle' => esc_html__('Choose min radius', 'knowherepro'),
						'desc' => '',
						"default" => 0,
						"min" => 0,
						"step" => 1,
						"max" => 100,
						'display_value' => ''
					),
					array(
						'id' => 'default_radius',
						'type' => 'slider',
						'title' => esc_html__('Default Radius', 'knowherepro'),
						'subtitle' => esc_html__('Choose default radius', 'knowherepro'),
						'desc' => '',
						"default" => 50,
						"min" => 0,
						"step" => 1,
						"max" => 500,
						'display_value' => ''
					),
					array(
						'id' => 'max_radius',
						'type' => 'slider',
						'title' => esc_html__('Max Radius', 'knowherepro'),
						'subtitle' => esc_html__('Choose max radius', 'knowherepro'),
						'desc' => '',
						"default" => 100,
						"min" => 0,
						"step" => 1,
						"max" => 500,
						'display_value' => ''
					),
					array(
						'id'       => 'radius_unit',
						'type'     => 'select',
						'title'    => esc_html__('Radius Unit', 'knowherepro'),
						'description' => '',
						'options'  => array(
							'km' => 'km',
							'mi' => 'mi'
						),
						'default' => 'km'
					)

				)

			);

			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title' => esc_html__( 'Job Archive', 'knowherepro' ),
				'fields' => array(
					array(
						'id' => 'job-category-header',
						'type' => 'select',
						'title' => esc_html__('Select header type', 'knowherepro'),
						'options' => array(
							'kw-theme-color' => esc_html__('Theme Color', 'knowherepro'),
							'kw-dark' => esc_html__('Dark', 'knowherepro'),
							'kw-light' => esc_html__('Light', 'knowherepro'),
						),
						'default' => 'kw-theme-color'
					),
					array(
						'id' => 'job-category-dark-color',
						'type' => 'color',
						'title' => esc_html__('Dark Color', 'knowherepro'),
						'required' => array( 'job-category-header','equals', 'kw-dark' ),
						'default' => '#222',
						'validate' => 'color',
					),
					array(
						'id' => 'job-category-light-color',
						'type' => 'color',
						'title' => esc_html__('Light Color', 'knowherepro'),
						'required' => array( 'job-category-header','equals', 'kw-light' ),
						'default' => '#fff',
						'validate' => 'color',
					),
					array(
						'id' => 'job-category-layout',
						'type' => 'image_select',
						'title' => esc_html__('Page Layout', 'knowherepro'),
						'options' => $page_layouts,
						'default' => 'kw-no-sidebar'
					),
					array(
						'id' => 'job-category-sidebar',
						'type' => 'select',
						'title' => esc_html__('Select Sidebar', 'knowherepro'),
						'required' => array( 'job-category-layout','equals', $sidebars ),
						'data' => 'sidebars',
						'default' => 'listing_sidebar'
					),
					array(
						'id' => 'job-category-columns',
						'type' => 'button_set',
						'title' => esc_html__('Count Columns', 'knowherepro'),
						'options' => array(
							"2" => "2",
							"3" => "3",
						),
						'default' => '3',
						'desc' => esc_html__( 'The number of columns for categories, tags, regions', 'knowherepro' )
					),
					array(
						'id' => 'job-show-map',
						'type' => 'checkbox',
						'title' => esc_html__('Show Map', 'knowherepro'),
						'required' => array( 'job-category-layout','equals', 'kw-no-sidebar' ),
						'default' => 1
					)
				)
			);

			$reviews_templates = array();

			if ( class_exists('RWP_Reviewer') ) {

				$templates = RWP_Reviewer::get_option('rwp_templates');

				if ( $templates ) {
					foreach( $templates as $template ) {
						$reviews_templates[$template['template_id']] = $template['template_name'];
					}
				}

			}

			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title' => esc_html__( 'Single Job', 'knowherepro' ),
				'fields' => array(
					array(
						'id' => 'job-single-layout',
						'type' => 'image_select',
						'title' => esc_html__('Page Layout', 'knowherepro'),
						'options' => $page_layouts,
						'default' => 'kw-right-sidebar'
					),
					array(
						'id' => 'job-single-sidebar',
						'type' => 'select',
						'title' => esc_html__('Select Sidebar', 'knowherepro'),
						'required' => array( 'job-single-layout','equals', $sidebars ),
						'data' => 'sidebars',
						'default' => 'listing_sidebar_single'
					),
					array(
						'id' => 'job-single-review-template',
						'type' => 'select',
						'title' => esc_html__('Select Template for review', 'knowherepro'),
						'options' => $reviews_templates,
						'default' => ''
					),
//					array(
//						'id' => 'job-single-reset-views-counter',
//						'type' => 'checkbox',
//						'title' => esc_html__('Reset Views Counter', 'knowherepro'),
//						'default' => '0'
//					),
					array(
						'id' => 'job-single-style',
						'type' => 'image_select',
						'title' => esc_html__('Select style for single job page', 'knowherepro'),
						'options' => $job_style_layouts,
						'default' => 'kw-style-1'
					),
					array(
						'id' => 'job-single-review',
						'type' => 'switch',
						'title' => esc_html__('Show Review', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'job-single-bookmarks',
						'type' => 'switch',
						'title' => esc_html__('Show Bookmarks', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'job-single-views',
						'type' => 'switch',
						'title' => esc_html__('Show Views', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'job-single-address',
						'type' => 'switch',
						'title' => esc_html__('Show Address', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'job-single-phone',
						'type' => 'switch',
						'title' => esc_html__('Show Phone', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'job-single-website',
						'type' => 'switch',
						'title' => esc_html__('Show Website', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => '225',
						'type' => 'info',
						'title' => esc_html__('Related Listings', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'job-related',
						'type' => 'switch',
						'title' => esc_html__('Show Related Listings', 'knowherepro'),
						'default' => false,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => "job-related-title",
						'type' => 'text',
						'title' => esc_html__('Related Title', 'knowherepro'),
						'required' => array('job-related', 'equals', true),
						'default' => ''
					),
					array(
						'id' => 'job-related-count',
						'type' => 'text',
						'required' => array('job-related', 'equals', true),
						'title' => esc_html__('Related Count items', 'knowherepro'),
						'default' => 3
					),
					array(
						'id' => 'job-related-columns',
						'type' => 'select',
						'title' => esc_html__('Select count columns', 'knowherepro'),
						'required' => array('job-related', 'equals', true),
						'options' => array(
							2 => 2,
							3 => 3
						),
						'default' => 2
					),
					array(
						'id' => 'job-related-style',
						'type' => 'button_set',
						'title' => esc_html__('Style', 'knowherepro'),
						'options' => array(
							"kw-type-1" => esc_html__('Style 1', 'knowherepro'),
							"kw-type-5" => esc_html__('Style 5 ( classified )', 'knowherepro')
						),
						'required' => array('job-related', 'equals', true),
						'default' => 'kw-type-1',
						'desc' => esc_html__( 'Select style for listings', 'knowherepro' )
					),
					array(
						'id' => 'job-related-list-view',
						'type' => 'switch',
						'title' => esc_html__('List View', 'knowherepro'),
						'default' => true,
						'required' => array('job-related', 'equals', true),
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => '2345',
						'type' => 'info',
						'title' => esc_html__('Social Links', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'job-single-share',
						'type' => 'switch',
						'title' => esc_html__('Show Social Links', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'job-share-facebook',
						'type' => 'switch',
						'title' => esc_html__('Enable Facebook Share', 'knowherepro'),
						'required' => array('job-single-share','equals',true),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'job-share-twitter',
						'type' => 'switch',
						'title' => esc_html__('Enable Twitter Share', 'knowherepro'),
						'required' => array('job-single-share','equals',true),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'job-share-googleplus',
						'type' => 'switch',
						'title' => esc_html__('Enable Google Plus Share', 'knowherepro'),
						'required' => array('job-single-share','equals',true),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'job-share-pinterest',
						'type' => 'switch',
						'title' => esc_html__('Enable Pinterest Share', 'knowherepro'),
						'required' => array('job-single-share','equals',true),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'job-share-email',
						'type' => 'switch',
						'title' => esc_html__('Enable Email Share', 'knowherepro'),
						'required' => array('job-single-share','equals',true),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),

				)
			);

			if ( class_exists( 'WP_Resume_Manager' ) ) {

				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title' => esc_html__( 'Resume', 'knowherepro' ),
					'fields' => array(
						array(
							'id' => 'job-resume-layout',
							'type' => 'image_select',
							'title' => esc_html__('Page Layout', 'knowherepro'),
							'options' => $page_layouts,
							'default' => 'kw-right-sidebar'
						),
						array(
							'id' => 'job-resume-sidebar',
							'type' => 'select',
							'title' => esc_html__('Select Sidebar', 'knowherepro'),
							'required' => array( 'job-resume-layout','equals', $sidebars ),
							'data' => 'sidebars',
							'default' => 'resume_sidebar'
						),
						array(
							'id' => 'job-resume-candidate-title',
							'type' => 'switch',
							'title' => esc_html__('Show Candidate Title', 'knowherepro'),
							'default' => true,
							'on' => esc_html__('Yes', 'knowherepro'),
							'off' => esc_html__('No', 'knowherepro'),
						),
						array(
							'id' => 'job-resume-candidate-location',
							'type' => 'switch',
							'title' => esc_html__('Show Candidate Location', 'knowherepro'),
							'default' => true,
							'on' => esc_html__('Yes', 'knowherepro'),
							'off' => esc_html__('No', 'knowherepro'),
						),
						array(
							'id' => 'job-resume-category',
							'type' => 'switch',
							'title' => esc_html__('Show Category', 'knowherepro'),
							'default' => true,
							'on' => esc_html__('Yes', 'knowherepro'),
							'off' => esc_html__('No', 'knowherepro'),
						),
						array(
							'id' => 'job-resume-date',
							'type' => 'switch',
							'title' => esc_html__('Show Date', 'knowherepro'),
							'default' => true,
							'on' => esc_html__('Yes', 'knowherepro'),
							'off' => esc_html__('No', 'knowherepro'),
						),
						array(
							'id' => 'job-resume-social-links',
							'type' => 'switch',
							'title' => esc_html__('Show Social Links', 'knowherepro'),
							'default' => true,
							'on' => esc_html__('Yes', 'knowherepro'),
							'off' => esc_html__('No', 'knowherepro'),
						),
						array(
							'id' => '212215',
							'type' => 'info',
							'title' => esc_html__('Header Admin Panel', 'knowherepro'),
							'notice' => false
						),
						array(
							'id' => 'job-resume-candidate-dashboard-url',
							'type' => 'switch',
							'title' => esc_html__('Show Candidate Dashboard URL', 'knowherepro'),
							'default' => true,
							'on' => esc_html__('Yes', 'knowherepro'),
							'off' => esc_html__('No', 'knowherepro'),
						),
						array(
							'id' => 'job-resume-resumes-url',
							'type' => 'switch',
							'title' => esc_html__('Show Resumes URL', 'knowherepro'),
							'default' => true,
							'on' => esc_html__('Yes', 'knowherepro'),
							'off' => esc_html__('No', 'knowherepro'),
						),
						array(
							'id' => 'job-resume-post-form-url',
							'type' => 'switch',
							'title' => esc_html__('Show Post a Resume URL', 'knowherepro'),
							'default' => true,
							'on' => esc_html__('Yes', 'knowherepro'),
							'off' => esc_html__('No', 'knowherepro'),
						),
					)
				);

			}

			$this->sections[] = array(
				'icon' => 'el-icon-user',
				'icon_class' => 'icon',
				'title' => esc_html__('Agents', 'knowherepro'),
				'fields' => array(
					array(
						'id' => 'agent-archive-layout',
						'type' => 'image_select',
						'title' => esc_html__('Page Layout', 'knowherepro'),
						'options' => $page_layouts,
						'default' => 'kw-right-sidebar'
					),
					array(
						'id' => 'job-agent-sidebar',
						'type' => 'select',
						'title' => esc_html__('Select Sidebar', 'knowherepro'),
						'data' => 'sidebars',
						'default' => 'agent_sidebar'
					),
					array(
						'id' => '29',
						'type' => 'info',
						'title' => esc_html__('Footer Settings', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'agent-get-widgets-from-page',
						'type' => 'select',
						'title' => esc_html__('Get widgets for footer from page', 'knowherepro'),
						'desc' => esc_html__('Get widgets for footer from page on agent pages. You can model the footer of any page and then use it to the agent pages', 'knowherepro'),
						'data' => 'pages',
						'default' => ''
					),
				)
			);

			$this->sections[] = array(
				'icon' => 'el-icon-user',
				'icon_class' => 'icon',
				'title' => esc_html__('Agency', 'knowherepro'),
				'fields' => array(
					array(
						'id' => 'agency-archive-layout',
						'type' => 'image_select',
						'title' => esc_html__('Page Layout', 'knowherepro'),
						'options' => $page_layouts,
						'default' => 'kw-right-sidebar'
					),
					array(
						'id' => 'job-agency-sidebar',
						'type' => 'select',
						'title' => esc_html__('Select Sidebar', 'knowherepro'),
						'data' => 'sidebars',
						'default' => 'agency_sidebar'
					),
					array(
						'id' => 'agency-get-widgets-from-page',
						'type' => 'select',
						'title' => esc_html__('Get widgets for footer from page', 'knowherepro'),
						'desc' => esc_html__('Get widgets for footer from page on agency pages. You can model the footer of any page and then use it to the agency pages', 'knowherepro'),
						'data' => 'pages',
						'default' => ''
					),
				)
			);

			// Javascript Code
			$this->sections[] = array(
				'icon_class' => 'el-icon-edit',
				'title' => esc_html__('Javascript Code', 'knowherepro'),
				'fields' => array(
					array(
						'id' => 'js-code-head',
						'type' => 'ace_editor',
						'title' => esc_html__('Javascript Code Before &lt;/head&gt;', 'knowherepro'),
						'subtitle' => esc_html__('Paste your JS code here.', 'knowherepro'),
						'mode' => 'javascript',
						'theme' => 'monokai',
						'default' => "jQuery(document).ready(function(){});",
						'options' => array(
							'minLines' => 35
						)
					)
				)
			);

			// Footer Settings
			$this->sections[] = array(
				'icon' => 'el-icon-website',
				'icon_class' => 'icon',
				'title' => esc_html__('Footer', 'knowherepro'),
				'fields' => array(
					array(
						'id' => '24',
						'type' => 'info',
						'title' => esc_html__('Social Links', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'show-footer-social-links',
						'type' => 'switch',
						'title' => __('Show Social Links', 'knowherepro'),
						'default' => false,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => "footer-social-facebook",
						'type' => 'text',
						'title' => esc_html__('Facebook', 'knowherepro'),
						'required' => array('show-footer-social-links', 'equals', true),
						'default' => '#'
					),
					array(
						'id' => "footer-social-google-plus",
						'type' => 'text',
						'title' => esc_html__('GooglePlus', 'knowherepro'),
						'required' => array('show-footer-social-links', 'equals', true),
						'default' => '#'
					),
					array(
						'id' => "footer-social-twitter",
						'type' => 'text',
						'title' => esc_html__('Twitter', 'knowherepro'),
						'required' => array('show-footer-social-links', 'equals', true),
						'default' => '#'
					),
					array(
						'id' => "footer-social-linkedin",
						'type' => 'text',
						'title' => esc_html__('LinkedIn', 'knowherepro'),
						'required' => array('show-footer-social-links', 'equals', true),
						'default' => '#'
					),
					array(
						'id' => "footer-social-email",
						'type' => 'text',
						'title' => esc_html__('Email', 'knowherepro'),
						'required' => array('show-footer-social-links', 'equals', true),
						'default' => '#'
					),
					array(
						'id' => "footer-social-tumblr",
						'type' => 'text',
						'title' => esc_html__('Tumblr', 'knowherepro'),
						'required' => array('show-footer-social-links', 'equals', true),
						'default' => ''
					),
					array(
						'id' => "footer-social-vimeo",
						'type' => 'text',
						'title' => esc_html__('Vimeo', 'knowherepro'),
						'required' => array('show-footer-social-links', 'equals', true),
						'default' => ''
					),
					array(
						'id' => "footer-social-youtube",
						'type' => 'text',
						'title' => esc_html__('Youtube', 'knowherepro'),
						'required' => array('show-footer-social-links', 'equals', true),
						'default' => ''
					),
					array(
						'id' => "footer-social-instagram",
						'type' => 'text',
						'title' => esc_html__('Instagram', 'knowherepro'),
						'required' => array('show-footer-social-links', 'equals', true),
						'default' => ''
					),
					array(
						'id' => "footer-social-flickr",
						'type' => 'text',
						'title' => esc_html__('Flickr', 'knowherepro'),
						'required' => array('show-footer-social-links', 'equals', true),
						'default' => ''
					),
					array(
						'id' => "footer-copyright",
						'type' => 'textarea',
						'title' => esc_html__('Copyright', 'knowherepro'),
						'default' => sprintf( __('Copyright <a href="%s">%s</a> &copy; %s. All Rights Reserved', 'knowherepro'), home_url('/'), get_bloginfo('name'), date('Y') )
					),
				)
			);

			$wysija_forms = array();

			if ( defined('WYSIJA') ) {
				$model_forms = WYSIJA::get( 'forms', 'model' );
				$model_forms->reset();
				$forms = $model_forms->getRows( array( 'form_id', 'name' ) );
				if ( $forms ) {
					foreach( $forms as $form ) {
						if ( isset($form) )
							$wysija_forms[$form['form_id']] = $form['name'];
					}
				}
			}

			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title' => esc_html__('Newsletter', 'knowherepro'),
				'fields' => array(
					array(
						'id' => 'show-footer-newsletter',
						'type' => 'switch',
						'title' => esc_html__('Show Newsletter', 'knowherepro'),
						'default' => true,
						'on' => esc_html__('Yes', 'knowherepro'),
						'off' => esc_html__('No', 'knowherepro'),
					),
					array(
						'id' => 'footer-newsletter-title',
						'type' => 'text',
						'title' => esc_html__('Title', 'knowherepro'),
						'desc' => '',
						'default' => esc_html__('Sign Up to Newsletter', 'knowherepro'),
						'required' => array( 'show-footer-newsletter', 'equals', true )
					),
					array(
						'id' => 'footer-newsletter-desc',
						'type' => 'text',
						'validate' => 'html_custom',
						'default' => 'and get the latest <b>deals, reviews &amp; articles</b>',
						'allowed_html' => array(
							'a' => array(
								'href' => array(),
								'title' => array()
							),
							'br' => array(),
							'em' => array(),
							'b' => array(),
							'strong' => array()
						),
						'title' => esc_html__('Description', 'knowherepro'),
						'required' => array( 'show-footer-newsletter', 'equals', true )
					),
					array(
						'id' => 'footer-select-form',
						'type' => 'select',
						'title' => esc_html__('Select Form', 'knowherepro'),
						'options' => $wysija_forms,
						'default' => '1',
						'required' => array( 'show-footer-newsletter', 'equals', true )
					),
				)
			);

			// 404 Page
			$this->sections[] = array(
				'icon' => 'el-icon-error',
				'icon_class' => 'icon',
				'title' => esc_html__('404 Page', 'knowherepro'),
				'fields' => array(
					array(
						'id' => 'error-content',
						'type' => 'textarea',
						'title' => esc_html__('Error text', 'knowherepro'),
						'validate' => 'html_custom',
						'default' => '<h1>404</h1><h3>We\'re sorry, but we can\'t find the page you were looking for.</h3><p>It\'s probably some thing we\'ve done wrong but now we know about it and we\'ll try to fix it. In the meantime, try one of these options:</p>',
						'allowed_html' => array(
							'h1' => array(),
							'h3' => array(),
							'p' => array(),
							'a' => array(
								'href' => array(),
								'title' => array()
							),
							'br' => array(),
							'em' => array(),
							'strong' => array()
						)
					),
				)
			);

			if ( class_exists( 'WooCommerce' ) ) {

				// Shop
				$this->sections[] = array(
					'icon' => 'el-icon-shopping-cart',
					'icon_class' => 'icon',
					'title' => esc_html__('Shop', 'knowherepro'),
					'fields' => array(
						array(
							'id' => '1',
							'type' => 'info',
							'title' => esc_html__('Label Status', 'knowherepro'),
							'notice' => false
						),
						array(
							'id' => 'product-stock',
							'type' => 'switch',
							'title' => esc_html__('Show "Out of stock" Status', 'knowherepro'),
							'default' => true,
							'on' => esc_html__('Yes', 'knowherepro'),
							'off' => esc_html__('No', 'knowherepro'),
						),
						array(
							'id' => 'product-featured',
							'type' => 'switch',
							'title' => esc_html__('Show "Featured" Status', 'knowherepro'),
							'default' => true,
							'on' => esc_html__('Yes', 'knowherepro'),
							'off' => esc_html__('No', 'knowherepro'),
						),
						array(
							'id' => 'product-sale',
							'type' => 'switch',
							'title' => esc_html__('Show "Sale" Status', 'knowherepro'),
							'default' => true,
							'on' => esc_html__('Yes', 'knowherepro'),
							'off' => esc_html__('No', 'knowherepro'),
						),
						array(
							'id' => 'product-sale-percent',
							'type' => 'switch',
							'title' => esc_html__('Show saved sale price percentage', 'knowherepro'),
							'default' => true,
							'on' => esc_html__('Yes', 'knowherepro'),
							'off' => esc_html__('No', 'knowherepro'),
							'required' => array( 'product-sale', 'equals', true ),
						),
						array(
							'id' => 'product-new',
							'type' => 'switch',
							'title' => esc_html__('Show "New" Status', 'knowherepro'),
							'default' => true,
							'on' => esc_html__('Yes', 'knowherepro'),
							'off' => esc_html__('No', 'knowherepro')
						),
						array(
							'id' => '27',
							'type' => 'info',
							'title' => esc_html__('Footer Settings', 'knowherepro'),
							'notice' => false
						),
						array(
							'id' => 'product-get-widgets-from-page',
							'type' => 'select',
							'title' => esc_html__('Get widgets for footer from page', 'knowherepro'),
							'desc' => esc_html__('Get widgets for footer from page on shop pages. You can model the footer of any page and then use it to the product pages', 'knowherepro'),
							'data' => 'pages',
							'default' => ''
						),

					)
				);

				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title' => esc_html__('Product Archives', 'knowherepro'),
					'fields' => array(
						array(
							'id' => 'product-archive-layout',
							'type' => 'image_select',
							'title' => esc_html__('Page Layout', 'knowherepro'),
							'options' => $page_layouts,
							'default' => 'kw-right-sidebar'
						),
						array(
							'id' => 'product-sidebar',
							'type' => 'select',
							'title' => esc_html__('Select Sidebar', 'knowherepro'),
							'required' => array('product-archive-layout', 'equals', $sidebars),
							'data' => 'sidebars',
							'default' => 'shop-widget-area'
						),
						array(
							'id' => 'category-item',
							'type' => 'text',
							'title' => esc_html__('Products per Page', 'knowherepro'),
							'desc' => esc_html__('Product counts.', 'knowherepro'),
							'default' => '10'
						),
						array(
							'id' => 'shop-product-cols',
							'type' => 'button_set',
							'title' => esc_html__('Shop Page Product Columns', 'knowherepro'),
							'options' => knowhere_product_columns(),
							'default' => '2',
						),
						array(
							'id' => 'category-product-cols',
							'type' => 'button_set',
							'title' => esc_html__('Category Product Columns', 'knowherepro'),
							'options' => knowhere_product_columns(),
							'default' => '2',
						),
					)
				);

				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title' => esc_html__('Single Product', 'knowherepro'),
					'fields' => array(
						array(
							'id' => 'product-single-layout',
							'type' => 'image_select',
							'title' => esc_html__('Single Layout', 'knowherepro'),
							'options' => $page_layouts,
							'default' => 'kw-right-sidebar'
						),
						array(
							'id' => 'product-single-sidebar',
							'type' => 'select',
							'title' => esc_html__('Select Sidebar', 'knowherepro'),
							'required' => array('product-single-layout', 'equals', $sidebars),
							'data' => 'sidebars',
							'default' => 'shop-widget-area'
						),
						array(
							'id' => 'product-short-description',
							'type' => 'switch',
							'title' => esc_html__('Show Short Description', 'knowherepro'),
							'default' => true,
							'on' => esc_html__('Yes', 'knowherepro'),
							'off' => esc_html__('No', 'knowherepro'),
						),
						array(
							'id' => 'product-metas',
							'type' => 'button_set',
							'title' => esc_html__('Product Meta', 'knowherepro'),
							'multi' => true,
							'options' => array(
								'categories' => esc_html__('Categories', 'knowherepro'),
								'pagetitle' => esc_html__('Page Title', 'knowherepro'),
								'breadcrumbs' => esc_html__('Breadcrumbs', 'knowherepro'),
								'tags' => esc_html__('Tags', 'knowherepro'),
								'-' => esc_html__('None', 'knowherepro'),
							),
							'default' => array( 'categories', 'pagetitle', 'breadcrumbs', 'tags', '-' )
						),
						array(
							'id' => 'product-related',
							'type' => 'switch',
							'title' => esc_html__('Show Related Products', 'knowherepro'),
							'default' => true,
							'on' => esc_html__('Yes', 'knowherepro'),
							'off' => esc_html__('No', 'knowherepro'),
						),
						array(
							'id' => 'product-related-count',
							'type' => 'text',
							'required' => array('product-related', 'equals', true),
							'title' => esc_html__('Related Count items', 'knowherepro'),
							'default' => '3'
						),
						array(
							'id' => 'product-related-cols',
							'type' => 'button_set',
							'required' => array('product-related', 'equals', true),
							'title' => esc_html__('Related Product Columns', 'knowherepro'),
							'options' => knowhere_product_columns(),
							'default' => '3',
						),
						array(
							'id' => 'product-upsells',
							'type' => 'switch',
							'title' => esc_html__('Show Up-Sells', 'knowherepro'),
							'default' => true,
							'on' => esc_html__('Yes', 'knowherepro'),
							'off' => esc_html__('No', 'knowherepro'),
						),
						array(
							'id' => 'product-upsells-count',
							'type' => 'text',
							'required' => array('product-upsells', 'equals', true),
							'title' => esc_html__('Up-Sells Count items', 'knowherepro'),
							'default' => '3'
						),
						array(
							'id' => 'product-upsells-cols',
							'type' => 'button_set',
							'required' => array('product-upsells', 'equals', true),
							'title' => esc_html__('Up-Sells Product Columns', 'knowherepro'),
							'options' => knowhere_product_columns(),
							'default' => '3',
						),
						array(
							'id' => '1',
							'type' => 'info',
							'title' => esc_html__('Social Links', 'knowherepro'),
							'notice' => false
						),
						array(
							'id' => 'product-single-share',
							'type' => 'switch',
							'title' => esc_html__('Show Social Links', 'knowherepro'),
							'default' => true,
							'on' => esc_html__('Yes', 'knowherepro'),
							'off' => esc_html__('No', 'knowherepro'),
						),
						array(
							'id' => 'product-share-facebook',
							'type' => 'switch',
							'title' => esc_html__('Enable Facebook Share', 'knowherepro'),
							'required' => array('product-single-share', 'equals', true),
							'default' => true,
							'on' => esc_html__('Yes', 'knowherepro'),
							'off' => esc_html__('No', 'knowherepro'),
						),
						array(
							'id' => 'product-share-twitter',
							'type' => 'switch',
							'title' => esc_html__('Enable Twitter Share', 'knowherepro'),
							'required' => array('product-single-share', 'equals', true),
							'default' => true,
							'on' => esc_html__('Yes', 'knowherepro'),
							'off' => esc_html__('No', 'knowherepro'),
						),
						array(
							'id' => 'product-share-googleplus',
							'type' => 'switch',
							'title' => esc_html__('Enable Google Plus Share', 'knowherepro'),
							'required' => array('product-single-share', 'equals', true),
							'default' => true,
							'on' => esc_html__('Yes', 'knowherepro'),
							'off' => esc_html__('No', 'knowherepro'),
						),
						array(
							'id' => 'product-share-pinterest',
							'type' => 'switch',
							'title' => esc_html__('Enable Pinterest Share', 'knowherepro'),
							'required' => array('product-single-share', 'equals', true),
							'default' => true,
							'on' => esc_html__('Yes', 'knowherepro'),
							'off' => esc_html__('No', 'knowherepro'),
						),
						array(
							'id' => 'product-share-email',
							'type' => 'switch',
							'title' => esc_html__('Enable Email Share', 'knowherepro'),
							'required' => array('product-single-share', 'equals', true),
							'default' => true,
							'on' => esc_html__('Yes', 'knowherepro'),
							'off' => esc_html__('No', 'knowherepro'),
						)
					)
				);

				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title' => esc_html__('Cart', 'knowherepro'),
					'fields' => array(
						array(
							'id' => 'product-crossell',
							'type' => 'switch',
							'title' => esc_html__('Show Cross-Sells', 'knowherepro'),
							'default' => true,
							'on' => __('Yes', 'knowherepro'),
							'off' => __('No', 'knowherepro'),
						),
						array(
							'id' => 'product-crossell-count',
							'type' => 'text',
							'required' => array('product-crossell', 'equals', true),
							'title' => esc_html__('Cross Sells Count', 'knowherepro'),
							'default' => '4'
						),
						array(
							'id' => 'product-crossell-cols',
							'type' => 'button_set',
							'required' => array('product-crossell', 'equals', true),
							'title' => esc_html__('Cross Sells Product Columns', 'knowherepro'),
							'options' => array(
								"3" => "3",
								"4" => "4"
							),
							'default' => '4',
						),
					)
				);

			}

			// Google
			$this->sections[] = array(
				'icon' => 'el-googleplus',
				'icon_class' => 'el',
				'title' => esc_html__('Google', 'knowherepro'),
				'fields' => array(
					array(
						'id' => '1',
						'type' => 'info',
						'style' => 'normal',
						'title' => esc_html__('Google recently changed the way their map service works. New pages which want to use Google Maps need to register an API key for their website. Older pages should  work fine without this API key. If the google map elements of this theme do not work properly you need to register a new API key.', 'knowherepro'),
						'notice' => false
					),
					array(
						'id' => 'gmap-api',
						'type' => 'textarea',
						'title' => esc_html__('Google Maps API Key', 'knowherepro'),
						'desc' => esc_html__('Enter a valid Google Maps API Key to use all map related theme functions.', 'knowherepro'),
						'default' => ''
					),
				)
			);

		}

		public function setArguments() {

			$theme = $this->theme;

			$this->args = array(
				'opt_name'          => 'knowhere_settings',
				'display_name'      => $theme->get('Name') . ' ' . esc_html__('Theme Options', 'knowherepro'),
				'display_version'   => esc_html__('Theme Version: ', 'knowherepro') . strtolower($theme->get('Version')),
				'menu_type'         => 'submenu',
				'allow_sub_menu'    => true,
				'menu_title'        => esc_html__('Theme Options', 'knowherepro'),
				'page_title'        => esc_html__('Theme Options', 'knowherepro'),
				'footer_credit'     => esc_html__('Theme Options', 'knowherepro'),

				'google_api_key' => 'AIzaSyBQft4vTUGW75YPU6c0xOMwLKhxCEJDPwg',
				'disable_google_fonts_link' => true,

				'async_typography'  => false,
				'admin_bar'         => false,
				'admin_bar_icon'       => 'dashicons-admin-generic',
				'admin_bar_priority'   => 50,
				'global_variable'   => '',
				'dev_mode'          => false,
				'customizer'        => false,
				'compiler'          => false,

				'page_priority'     => null,
				'page_parent'       => 'themes.php',
				'page_permissions'  => 'manage_options',
				'menu_icon'         => '',
				'last_tab'          => '',
				'page_icon'         => 'icon-themes',
				'page_slug'         => 'knowhere_settings',
				'save_defaults'     => true,
				'default_show'      => false,
				'default_mark'      => '',
				'show_import_export' => true,
				'show_options_object' => false,

				'transient_time'    => 60 * MINUTE_IN_SECONDS,
				'output'            => false,
				'output_tag'        => false,

				'database'              => '',
				'system_info'           => false,

				'hints' => array(
					'icon'          => 'icon-question-sign',
					'icon_position' => 'right',
					'icon_color'    => 'lightgray',
					'icon_size'     => 'normal',
					'tip_style'     => array(
						'color'         => 'light',
						'shadow'        => true,
						'rounded'       => false,
						'style'         => '',
					),
					'tip_position'  => array(
						'my' => 'top left',
						'at' => 'bottom right',
					),
					'tip_effect'    => array(
						'show'          => array(
							'effect'        => 'slide',
							'duration'      => '500',
							'event'         => 'mouseover',
						),
						'hide'      => array(
							'effect'    => 'slide',
							'duration'  => '500',
							'event'     => 'click mouseleave',
						),
					),
				),
				'ajax_save'                 => false,
				'use_cdn'                   => true,
			);

		}

	}

	global $knowhere_redux_settings;
	$knowhere_redux_settings = new knowhere_redux_settings();

}