<?php

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * The Metaboxes class.
 */
class Knowhere_Theme_Metaboxes {

	/**
	 * The settings.
	 *
	 * @access public
	 * @var array
	 */
	public $data;

	/**
	 * The class constructor.
	 *
	 * @access public
	 */
	public function __construct() {
		require_once plugin_dir_path( __FILE__ ) . 'inc/loader.php';
		$loader = new MAD_Loader;
		$loader->init();

		add_filter( 'mad_meta_boxes', array($this, 'meta_boxes_array') );
	}

	public function meta_boxes_array($meta_boxes) {

		/*	Meta Box Definitions
		/* ---------------------------------------------------------------------- */

		$prefix = 'knowhere_';

		/*	Page
		/* ---------------------------------------------------------------------- */

		$meta_boxes[] = array(
			'id'       => 'page-subtitle',
			'title'    => '&#x27A4; ' . esc_html__('Front Page &raquo; Page Subtitle', 'knowherepro_app_textdomain'),
			'pages'    => array( 'page' ),
			'context'  => 'normal',
			'priority' => 'low',
			'fields'   => array(
				array(
					'name' => '',
					'id'   => $prefix . 'page_subtitle',
					'type' => 'textarea',
					'std'  => '',
					'desc' => esc_html__( 'This is the subtitle that will be shown in the page\'s Title Area, below the page title.', 'knowherepro_app_textdomain' )
				)
			)
		);

		$meta_boxes[] = array(
			'id'       => 'page-add-video',
			'title'    => '&#x27A4; ' . esc_html__('Front Page &raquo; Add Video', 'knowherepro_app_textdomain'),
			'pages'    => array( 'page' ),
			'context'  => 'normal',
			'priority' => 'low',
			'fields'   => array(
				array(
					'name' => '',
					'id'   => $prefix . 'page_add_video',
					'type' => 'input',
					'desc' => esc_html__('Add youtube or vimeo video on front page', 'knowherepro_app_textdomain'),
					'std'  => ''
				)
			)
		);

		$meta_boxes[] = array(
			'id'       => 'page-listing-categories',
			'title'    => '&#x27A4; ' . esc_html__('Front Page &raquo; Highlighted Categories', 'knowherepro_app_textdomain'),
			'pages'    => array( 'page' ),
			'context'  => 'normal',
			'priority' => 'low',
			'fields'   => array(
				array(
					'name' => '',
					'id'   => $prefix . 'frontpage_listing_categories',
					'type' => 'input',
					'desc' => wp_kses(__('<p>You can select which categories to highlight, by adding their <em>slugs</em>, separated by a comma: <em>villas, hotels, restaurants</em></p><p> You can change their <em>shown name</em> (in case it is too long) with this pattern: <em>slug (My Custom Name)</em></p>', 'knowherepro'), array( 'p' => array(), 'em' => array() ) ),
					'std'  => ''
				)
			)
		);

		/*	Layout Settings
		/* ---------------------------------------------------------------------- */

		$pages = get_pages('title_li=&orderby=name');
		$list_pages = array('' => 'None');
		foreach ( $pages as $key => $entry ) {
			$list_pages[$entry->ID] = $entry->post_title;
		}

		$registered_sidebars = Knowhere_Admin_Helper::get_registered_sidebars(array(' ' => 'Default Sidebar'), array('General Widget Area'));
		$registered_custom_sidebars = array();

		foreach( $registered_sidebars as $key => $value ) {
			if ( strpos($key, 'Footer Row') === false ) {
				$registered_custom_sidebars[$key] = $value;
			}
		}

		$meta_boxes[] = array(
			'id'       => 'layout-settings',
			'title'    => esc_html__('Knowhere Page Options', 'knowherepro_app_textdomain'),
			'pages'    => array( 'post', 'page', 'product', 'job_listing' ),
			'context'  => 'normal',
			'priority' => 'low',
			'fields'   => array(
				array(
					'name'    => esc_html__('Header Type', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'header_type',
					'type'    => 'select_advanced',
					'std'     => '',
					'js_options' => array(
						'width' => '100%',
						'minimumResultsForSearch' => '-1',
						'placeholder' => esc_html__('Default Header Type', 'knowherepro_app_textdomain')
					),
					'desc'    => esc_html__('Choose your header type', 'knowherepro_app_textdomain'),
					'options' => array(
						' ' => esc_html__('Default Header Type', 'knowherepro_app_textdomain'),
						'kw-type-1' => esc_html__('Type 1', 'knowherepro_app_textdomain'),
						'kw-type-2' => esc_html__('Type 2', 'knowherepro_app_textdomain'),
						'kw-type-3' => esc_html__('Type 3', 'knowherepro_app_textdomain'),
						'kw-type-4' => esc_html__('Type 4', 'knowherepro_app_textdomain'),
						'kw-type-5' => esc_html__('Type 5', 'knowherepro_app_textdomain'),
						'kw-type-6' => esc_html__('Type 6', 'knowherepro_app_textdomain')
					)
				),
				array(
					'name'    => esc_html__('Hide Topbar', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'page_show_topbar',
					'type'    => 'checkbox',
					'std'     => 0,
					'value' => 0,
					'visible' => array( $prefix . 'header_type', '=', 'kw-type-1' ),
					'desc'    => esc_html__('Hide topbar for home page 1', 'knowherepro_app_textdomain')
				),
				array(
					'name'    => esc_html__('Show Search, Login', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'header_show_search_and_login',
					'type'    => 'select',
					'std'     => '',
					'multiple' => true,
					'js_options' => array(
						'width' => '100%',
						'minimumResultsForSearch' => '-1',
						'placeholder' => esc_html__('Default', 'knowherepro_app_textdomain')
					),
					'visible' => array( $prefix . 'header_type', '=', 'kw-type-1' ),
					'options' => array(
						' ' => esc_html__('Default', 'knowherepro_app_textdomain'),
						'search' => esc_html__('Search', 'knowherepro_app_textdomain'),
						'login' => esc_html__('Login', 'knowherepro_app_textdomain'),
					)
				),
				array(
					'name'    => esc_html__('Sidebar Position', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'page_sidebar_position',
					'type'    => 'select',
					'std'     => '',
					'js_options' => array(
						'width' => '100%',
						'minimumResultsForSearch' => '-1',
						'placeholder' => esc_html__('Default Sidebar Position', 'knowherepro_app_textdomain')
					),
					'desc'    => esc_html__('Choose page sidebar position', 'knowherepro_app_textdomain'),
					'options' => array(
						' ' => esc_html__('Default Sidebar Position', 'knowherepro_app_textdomain'),
						'kw-no-sidebar' => esc_html__('Without Sidebar', 'knowherepro_app_textdomain'),
						'kw-left-sidebar' => esc_html__('Left Sidebar', 'knowherepro_app_textdomain'),
						'kw-right-sidebar' => esc_html__('Right Sidebar', 'knowherepro_app_textdomain')
					)
				),
				array(
					'name'    => esc_html__('Sidebar Setting', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'page_sidebar',
					'type'    => 'select',
					'std'     => '',
					'js_options' => array(
						'width' => '100%',
						'minimumResultsForSearch' => '-1',
						'placeholder' => esc_html__('Choose a custom sidebar', 'knowherepro_app_textdomain')
					),
					'desc'    => esc_html__('Choose a custom sidebar', 'knowherepro_app_textdomain'),
					'options' => $registered_custom_sidebars
				),
				array(
					'name'    => esc_html__('Page Content Padding', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'page_content_padding',
					'type'    => 'dimension',
					'std'     => '',
					'desc'    => esc_html__('In pixels ex: 50px. Leave empty for default value of 85, 85px.', 'knowherepro_app_textdomain'),
					'options' => array(
						'top', 'bottom'
					),
				),
				array(
					'name'    => esc_html__('Hidden Newsletter', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'footer_hidden_newsletter',
					'type'    => 'checkbox',
					'std'     => '',
					'value' => 1,
					'desc'    => esc_html__('Hidden newsletter from the footer', 'knowherepro_app_textdomain')
				),

			)
		);

		/*	Listing
		/* ---------------------------------------------------------------------- */

		$meta_boxes[] = array(
			'id'       => 'job-settings',
			'title'    => esc_html__('Listing Page Options', 'knowherepro_app_textdomain'),
			'pages'    => array( 'job_listing' ),
			'context'  => 'normal',
			'priority' => 'low',
			'fields'   => array(
				array(
					'name'    => esc_html__('Style', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'job_style_single_page',
					'type'    => 'select_advanced',
					'std'     => '',
					'js_options' => array(
						'width' => '100%',
						'minimumResultsForSearch' => '-1',
						'placeholder' => esc_html__('Default Style', 'knowherepro_app_textdomain')
					),
					'desc'    => esc_html__('Choose your style for job single page', 'knowherepro_app_textdomain'),
					'options' => array(
						' ' => esc_html__('Default Style', 'knowherepro_app_textdomain'),
						'kw-style-1' => esc_html__('Listing Style', 'knowherepro_app_textdomain'),
						'kw-style-2' => esc_html__('Listing Style 2', 'knowherepro_app_textdomain'),
						'kw-style-3' => esc_html__('Job Style', 'knowherepro_app_textdomain'),
						'kw-style-4' => esc_html__('Property Style', 'knowherepro_app_textdomain'),
						'kw-style-5' => esc_html__('Classified Style', 'knowherepro_app_textdomain')
					)
				),
				array(
					'name'    => esc_html__('Image Size', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'job_image_size',
					'type'    => 'select',
					'std'     => '',
					'desc'    => esc_html__('Choose image size', 'knowherepro_app_textdomain'),
					'options' => array(
						"medium" => esc_html__('Medium', 'knowherepro_app_textdomain'),
						"large" => esc_html__('Large', 'knowherepro_app_textdomain'),
						"extra-large" => esc_html__('Extra Large', 'knowherepro_app_textdomain')
					)
				),
				array(
					'name'    => esc_html__('Hidden Gallery', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'job_hidden_gallery',
					'type'    => 'checkbox',
					'std'     => '',
					'value' => 1,
					'desc'    => esc_html__('Hidden gallery', 'knowherepro_app_textdomain')
				),
				array(
					'name'    => esc_html__('Hidden Reviews', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'job_hidden_reviews',
					'type'    => 'checkbox',
					'std'     => '',
					'value' => 1,
					'desc'    => esc_html__('Hidden reviews', 'knowherepro_app_textdomain')
				),
			)
		);

		/*	Backgrounds
		/* ---------------------------------------------------------------------- */

		$meta_boxes[] = array(
			'id'       => 'page-backgrounds',
			'title'    => esc_html__('Backgrounds', 'knowherepro_app_textdomain'),
			'pages'    => array('page', 'job_listing'),
			'context'  => 'normal',
			'priority' => 'default',
			'fields'   => array(
				array(
					'name'    => esc_html__('Body Background color', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'body_bg_color',
					'type'    => 'color',
					'std'     => '',
					'desc'    => esc_html__('Select the background color of the body', 'knowherepro_app_textdomain')
				),
				array(
					'name'    => esc_html__('Background image', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'bg_image',
					'type'    => 'thickbox_image',
					'std'     => '',
					'desc'    => esc_html__('Select the background image', 'knowherepro_app_textdomain')
				),
				array(
					'name'    => esc_html__('Background repeat', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'bg_image_repeat',
					'type'    => 'select',
					'std'     => '',
					'desc'    => esc_html__('Select the repeat mode for the background image', 'knowherepro_app_textdomain'),
					'options' => array(
						'' => esc_html__('Default', 'knowherepro_app_textdomain'),
						'repeat' => esc_html__('Repeat', 'knowherepro_app_textdomain'),
						'no-repeat' => esc_html__('No Repeat', 'knowherepro_app_textdomain'),
						'repeat-x' => esc_html__('Repeat Horizontally', 'knowherepro_app_textdomain'),
						'repeat-y' => esc_html__('Repeat Vertically', 'knowherepro_app_textdomain')
					)
				),
				array(
					'name'    => esc_html__('Background position', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'bg_image_position',
					'type'    => 'select',
					'std'     => '',
					'desc'    => esc_html__('Select the position for the background image', 'knowherepro_app_textdomain'),
					'options' => array(
						'' => esc_html__('Default', 'knowherepro_app_textdomain'),
						'top left' => esc_html__('Top left', 'knowherepro_app_textdomain'),
						'top center' => esc_html__('Top center', 'knowherepro_app_textdomain'),
						'top right' => esc_html__('Top right', 'knowherepro_app_textdomain'),
						'bottom left' => esc_html__('Bottom left', 'knowherepro_app_textdomain'),
						'bottom center' => esc_html__('Bottom center', 'knowherepro_app_textdomain'),
						'bottom right' => esc_html__('Bottom right', 'knowherepro_app_textdomain')
					)
				),
				array(
					'name'    => esc_html__('Background attachment', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'bg_image_attachment',
					'type'    => 'select',
					'std'     => '',
					'desc'    => esc_html__('Select the attachment for the background image ', 'knowherepro_app_textdomain'),
					'options' => array(
						'' => esc_html__('Default', 'knowherepro_app_textdomain'),
						'scroll' => esc_html__('Scroll', 'knowherepro_app_textdomain'),
						'fixed' => esc_html__('Fixed', 'knowherepro_app_textdomain')
					)
				),
				array(
					'name'    => esc_html__('Footer Background color', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'footer_bg_color',
					'type'    => 'color',
					'std'     => '',
					'desc'    => esc_html__('Select the background color of the footer', 'knowherepro_app_textdomain')
				),
				array(
					'name'    => esc_html__('Footer Hidden Background image', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'footer_hidden_bg_image',
					'type'    => 'checkbox',
					'std'     => '',
					'value' => 1,
					'desc'    => esc_html__('Hidden background image', 'knowherepro_app_textdomain')
				),
			)
		);

		/*	Testimonials
		/* ---------------------------------------------------------------------- */

		$meta_boxes[] = array(
			'id'       => 'details-testimonials',
			'title'    => esc_html__('Testimonial Details', 'knowherepro_app_textdomain'),
			'pages'    => array( 'testimonials' ),
			'context'  => 'normal',
			'priority' => 'default',
			'fields'   => array(
				array(
					'name'    => esc_html__('Testimonial Text', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'testi_text',
					'type'    => 'textarea',
					'std'     => '',
					'desc'    => esc_html__('Write a testimonial into the textarea.', 'knowherepro_app_textdomain')
				),
				array(
					'name'    => esc_html__('By who?', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'testi_name',
					'type'    => 'text',
					'std'     => '',
					'desc'    => esc_html__('Name of the client who gave feedback', 'knowherepro_app_textdomain')
				),
				array(
					'name'    => esc_html__('Photo', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'testi_photo',
					'type'    => 'thickbox_image',
					'std'     => '',
					'desc'    => esc_html__('Select the photo', 'knowherepro_app_textdomain')
				),
				array(
					'name'    => esc_html__('City', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'testi_city',
					'type'    => 'text',
					'std'     => ''
				),
			)
		);

		/*	Product
		/* ---------------------------------------------------------------------- */

		$meta_boxes[] = array(
			'id'       => 'product-settings',
			'title'    => esc_html__('Product Details', 'knowherepro_app_textdomain'),
			'pages'    => array( 'product' ),
			'context'  => 'normal',
			'priority' => 'default',
			'fields'   => array(
				array(
					'name' => esc_html__('Location', 'knowherepro_app_textdomain'),
					'id' =>  $prefix . 'product_map_address',
					'desc' => '',
					'type' => 'text',
					'std' => '',
					'columns' => 12
				),
				array(
					'name' => esc_html__('Location at Google Map*', 'knowherepro_app_textdomain'),
					'id' => $prefix . 'agency_location',
					'desc' => esc_html__('Drag the google map marker to point your agency location. You can also use the address field above to search for your product.', 'knowherepro_app_textdomain'),
					'type' => 'map',
					'std' => '25.686540,-80.431345,15',
					'style' => 'width: 95%; height: 400px',
					'address_field' => $prefix . 'product_map_address',
					'columns' => 12
				)
			)
		);

		/*	Agency
		/* ---------------------------------------------------------------------- */

		$meta_boxes[] = array(
			'id'       => 'agency-settings',
			'title'    => esc_html__('Agency Details', 'knowherepro_app_textdomain'),
			'pages'    => array( 'knowhere_agency' ),
			'context'  => 'normal',
			'priority' => 'default',
			'fields'   => array(
				array(
					'name' => esc_html__('Agency Location', 'knowherepro_app_textdomain'),
					'id' =>  $prefix . 'agency_map_address',
					'desc' => esc_html__('Leave it empty if you want to hide map on agency detail page.', 'knowherepro_app_textdomain'),
					'type' => 'text',
					'std' => '',
					'columns' => 12
				),
				array(
					'name' => esc_html__('Agency Location at Google Map*', 'knowherepro_app_textdomain'),
					'id' => $prefix . 'agency_location',
					'desc' => esc_html__('Drag the google map marker to point your agency location. You can also use the address field above to search for your agency.', 'knowherepro_app_textdomain'),
					'type' => 'map',
					'std' => '25.686540,-80.431345,15',
					'style' => 'width: 95%; height: 400px',
					'address_field' => $prefix . 'agency_map_address',
					'columns' => 12
				),
				array(
					'name'    => esc_html__('Phone', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'agency_phone',
					'type'    => 'text',
					'std'     => '',
					'desc'    => ''
				),
				array(
					'name'    => esc_html__('E-mail', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'agency_email',
					'type'    => 'text',
					'std'     => '',
					'desc'    => esc_html__('Provide agency email address, Agency related messages from contact form on property details page, will be sent on this email address.', 'knowherepro_app_textdomain')
				),
				array(
					'name'    => esc_html__('Facebook', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'agency_facebook',
					'type'    => 'text',
					'std'     => '',
					'desc'    => ''
				),
				array(
					'name'    => esc_html__('Google Plus', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'agency_google_plus',
					'type'    => 'text',
					'std'     => '',
					'desc'    => ''
				),
				array(
					'name'    => esc_html__('Twitter', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'agency_twitter',
					'type'    => 'text',
					'std'     => '',
					'desc'    => ''
				),
				array(
					'name'    => esc_html__('LinkedIn', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'agency_linkedin',
					'type'    => 'text',
					'std'     => '',
					'desc'    => ''
				),
				array(
					'name'    => esc_html__('Pinterest', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'agency_pinterest',
					'type'    => 'text',
					'std'     => '',
					'desc'    => ''
				),
			)
		);

		/*	Agents
		/* ---------------------------------------------------------------------- */

		$meta_boxes[] = array(
			'id'       => 'agents-settings',
			'title'    => esc_html__('Agent Details', 'knowherepro_app_textdomain'),
			'pages'    => array( 'knowhere_agent' ),
			'context'  => 'normal',
			'priority' => 'default',
			'fields'   => array(
				array(
					'name'    => esc_html__('Position', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'agent_position',
					'type'    => 'text',
					'std'     => '',
					'desc'    => esc_html__('Ex: Founder & CEO.', 'knowherepro_app_textdomain')
				),
				array(
					'name' => esc_html__( 'Address', 'knowherepro_app_textdomain' ),
					'id' => $prefix . 'agent_address',
					'desc' => esc_html__('Enter your address, it will use for invoices ', 'knowherepro_app_textdomain'),
					'type' => 'text',
					'std' => "",
					'columns'   => 6
				),
				array(
					'name'    => esc_html__('Phone', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'agent_phone',
					'type'    => 'text',
					'std'     => '',
					'desc'    => ''
				),
				array(
					'name'    => esc_html__('E-mail', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'agent_email',
					'type'    => 'text',
					'std'     => '',
					'desc'    => esc_html__('Provide agent email address, Agent related messages from contact form on property details page, will be sent on this email address.', 'knowherepro_app_textdomain')
				),
				array(
					'name'    => esc_html__('Facebook', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'agent_facebook',
					'type'    => 'text',
					'std'     => '',
					'desc'    => ''
				),
				array(
					'name'    => esc_html__('Google Plus', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'agent_google_plus',
					'type'    => 'text',
					'std'     => '',
					'desc'    => ''
				),
				array(
					'name'    => esc_html__('Twitter', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'agent_twitter',
					'type'    => 'text',
					'std'     => '',
					'desc'    => ''
				),
				array(
					'name'    => esc_html__('LinkedIn', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'agent_linkedin',
					'type'    => 'text',
					'std'     => '',
					'desc'    => ''
				),
				array(
					'name'    => esc_html__('Pinterest', 'knowherepro_app_textdomain'),
					'id'      => $prefix . 'agent_pinterest',
					'type'    => 'text',
					'std'     => '',
					'desc'    => ''
				),
			)
		);

		$agencies_array = array('' => esc_html__('None', 'knowherepro_app_textdomain'));
		$agencies_posts = get_posts(
			array(
				'post_type' => 'knowhere_agency',
				'posts_per_page' => -1,
				'suppress_filters' => 0
			)
		);

		if ( !empty($agencies_posts) ) {
			foreach ( $agencies_posts as $agency_post ) {
				$agencies_array[$agency_post->ID] = $agency_post->post_title;
			}
		}

		$meta_boxes[] = array(
			'title'  => esc_html__( 'Agencies', 'knowherepro_app_textdomain' ),
			'pages'  => array( 'knowhere_agent' ),
			'context' => 'side',
			'priority' => 'high',
			'fields' => array(
				array(
					'id'        => $prefix . 'agent_agencies',
					'type'      => 'select',
					'options'   => $agencies_array,
					'desc'      => '',
					'columns' => 12,
					'multiple' => false
				),
			)
		);

		return $meta_boxes;
	}

}

new Knowhere_Theme_Metaboxes;
