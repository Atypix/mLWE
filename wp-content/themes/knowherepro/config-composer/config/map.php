<?php

$target_arr = array(
	esc_html__( 'Same window', 'knowherepro' ) => '_self',
	esc_html__( 'New window', 'knowherepro' ) => '_blank',
);

/* Default Custom Shortcodes
/* --------------------------------------------------------------------- */

/* Row
----------------------------------------------------------- */

vc_map( array(
	'name' => esc_html__( 'Row' , 'knowherepro' ),
	'base' => 'vc_row',
	'is_container' => true,
	'icon' => 'icon-wpb-row',
	'show_settings_on_create' => false,
	'category' => esc_html__( 'Content', 'knowherepro' ),
	'description' => esc_html__( 'Place content elements inside the row', 'knowherepro' ),
	'params' => array(
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Row stretch', 'knowherepro' ),
			'param_name' => 'full_width',
			'value' => array(
				esc_html__( 'Default', 'knowherepro' ) => '',
				esc_html__( 'Stretch row', 'knowherepro' ) => 'stretch_row',
				esc_html__( 'Stretch row and content', 'knowherepro' ) => 'stretch_row_content',
				esc_html__( 'Stretch row and content (no paddings)', 'knowherepro' ) => 'stretch_row_content_no_spaces',
			),
			'description' => esc_html__( 'Select stretching options for row and content (Note: stretched may not work properly if parent container has "overflow: hidden" CSS property).', 'knowherepro' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Columns gap', 'knowherepro' ),
			'param_name' => 'gap',
			'value' => array(
				'0px' => '0',
				'1px' => '1',
				'2px' => '2',
				'3px' => '3',
				'4px' => '4',
				'5px' => '5',
				'10px' => '10',
				'15px' => '15',
				'20px' => '20',
				'25px' => '25',
				'30px' => '30',
				'35px' => '35',
			),
			'std' => '0',
			'description' => esc_html__( 'Select gap between columns in row.', 'knowherepro' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Full height row?', 'knowherepro' ),
			'param_name' => 'full_height',
			'description' => esc_html__( 'If checked row will be set to full height.', 'knowherepro' ),
			'value' => array( esc_html__( 'Yes', 'knowherepro' ) => 'yes' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Columns position', 'knowherepro' ),
			'param_name' => 'columns_placement',
			'value' => array(
				esc_html__( 'Middle', 'knowherepro' ) => 'middle',
				esc_html__( 'Top', 'knowherepro' ) => 'top',
				esc_html__( 'Bottom', 'knowherepro' ) => 'bottom',
				esc_html__( 'Stretch', 'knowherepro' ) => 'stretch',
			),
			'description' => esc_html__( 'Select columns position within row.', 'knowherepro' ),
			'dependency' => array(
				'element' => 'full_height',
				'not_empty' => true,
			),
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Equal height', 'knowherepro' ),
			'param_name' => 'equal_height',
			'description' => esc_html__( 'If checked columns will be set to equal height.', 'knowherepro' ),
			'value' => array( esc_html__( 'Yes', 'knowherepro' ) => 'yes' )
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Content position', 'knowherepro' ),
			'param_name' => 'content_placement',
			'value' => array(
				esc_html__( 'Default', 'knowherepro' ) => '',
				esc_html__( 'Top', 'knowherepro' ) => 'top',
				esc_html__( 'Middle', 'knowherepro' ) => 'middle',
				esc_html__( 'Bottom', 'knowherepro' ) => 'bottom',
			),
			'description' => esc_html__( 'Select content position within columns.', 'knowherepro' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Use video background?', 'knowherepro' ),
			'param_name' => 'video_bg',
			'description' => esc_html__( 'If checked, video will be used as row background.', 'knowherepro' ),
			'value' => array( esc_html__( 'Yes', 'knowherepro' ) => 'yes' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'YouTube link', 'knowherepro' ),
			'param_name' => 'video_bg_url',
			'value' => 'https://www.youtube.com/watch?v=lMJXxhRFO1k',
			'description' => esc_html__( 'Add YouTube link.', 'knowherepro' ),
			'dependency' => array(
				'element' => 'video_bg',
				'not_empty' => true,
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Parallax', 'knowherepro' ),
			'param_name' => 'video_bg_parallax',
			'value' => array(
				esc_html__( 'None', 'knowherepro' ) => '',
				esc_html__( 'Simple', 'knowherepro' ) => 'content-moving'
			),
			'description' => esc_html__( 'Add parallax type background for row.', 'knowherepro' ),
			'dependency' => array(
				'element' => 'video_bg',
				'not_empty' => true,
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Parallax', 'knowherepro' ),
			'param_name' => 'parallax',
			'value' => array(
				esc_html__( 'None', 'knowherepro' ) => '',
				esc_html__( 'Simple', 'knowherepro' ) => 'content-moving',
			),
			'description' => esc_html__( 'Add parallax type background for row (Note: If no image is specified, parallax will use background image from Design Options).', 'knowherepro' ),
			'dependency' => array(
				'element' => 'video_bg',
				'is_empty' => true,
			)
		),
		array(
			'type' => 'attach_image',
			'heading' => esc_html__( 'Image', 'knowherepro' ),
			'param_name' => 'parallax_image',
			'value' => '',
			'description' => esc_html__( 'Select image from media library.', 'knowherepro' ),
			'dependency' => array(
				'element' => 'parallax',
				'not_empty' => true,
			),
			'group' => esc_html__( 'Parallax', 'knowherepro' )
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Overlay background color', 'knowherepro' ),
			'param_name' => 'overlay_color',
			'description' => esc_html__( 'Select custom overlay color for background.', 'knowherepro' ),
			'dependency' => array(
				'element' => 'parallax',
				'not_empty' => true,
			),
			'group' => esc_html__( 'Parallax', 'knowherepro' )
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Parallax speed', 'knowherepro' ),
			'param_name' => 'parallax_speed_video',
			'value' => '1.5',
			'description' => esc_html__( 'Enter parallax speed ratio (Note: Default value is 1.5, min value is 1)', 'knowherepro' ),
			'dependency' => array(
				'element' => 'video_bg_parallax',
				'not_empty' => true,
			),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Parallax speed', 'knowherepro' ),
			'param_name' => 'parallax_speed_bg',
			'value' => '1.5',
			'description' => esc_html__( 'Enter parallax speed ratio (Note: Default value is 1.5, min value is 1)', 'knowherepro' ),
			'dependency' => array(
				'element' => 'parallax',
				'not_empty' => true,
			),
		),
		array(
			'type' => 'el_id',
			'heading' => esc_html__( 'Row ID', 'knowherepro' ),
			'param_name' => 'el_id',
			'description' => sprintf( __( 'Enter row ID (Note: make sure it is unique and valid according to <a href="%s" target="_blank">w3c specification</a>).', 'knowherepro' ), 'http://www.w3schools.com/tags/att_global_id.asp' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Disable row', 'knowherepro' ),
			'param_name' => 'disable_element', // Inner param name.
			'description' => esc_html__( 'If checked the row won\'t be visible on the public side of your website. You can switch it back any time.', 'knowherepro' ),
			'value' => array( esc_html__( 'Yes', 'knowherepro' ) => 'yes' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Extra class name', 'knowherepro' ),
			'param_name' => 'el_class',
			'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'knowherepro' ),
		),
		array(
			'type' => 'css_editor',
			'heading' => esc_html__( 'CSS box', 'knowherepro' ),
			'param_name' => 'css',
			'group' => esc_html__( 'Design Options', 'knowherepro' ),
		),
	),
	'js_view' => 'VcRowView',
) );

/* Custom Heading element
----------------------------------------------------------- */

vc_map( array(
	'name' => esc_html__( 'Custom Heading', 'knowherepro' ),
	'base' => 'vc_custom_heading',
	'icon' => 'icon-wpb-ui-custom_heading',
	'show_settings_on_create' => true,
	'category' => esc_html__( 'Content', 'knowherepro' ),
	'description' => esc_html__( 'Text with Google fonts', 'knowherepro' ),
	'params' => array(
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Text source', 'knowherepro' ),
			'param_name' => 'source',
			'value' => array(
				esc_html__( 'Custom text', 'knowherepro' ) => '',
				esc_html__( 'Post or Page Title', 'knowherepro' ) => 'post_title',
			),
			'std' => '',
			'description' => esc_html__( 'Select text source.', 'knowherepro' ),
		),
		array(
			'type' => 'textarea',
			'heading' => esc_html__( 'Text', 'knowherepro' ),
			'param_name' => 'text',
			'admin_label' => true,
			'value' => esc_html__( 'This is custom heading element', 'knowherepro' ),
			'description' => esc_html__( 'Note: If you are using non-latin characters be sure to activate them under Settings/Visual Composer/General Settings.', 'knowherepro' ),
			'dependency' => array(
				'element' => 'source',
				'is_empty' => true,
			),
		),
		array(
			'type' => 'vc_link',
			'heading' => esc_html__( 'URL (Link)', 'knowherepro' ),
			'param_name' => 'link',
			'description' => esc_html__( 'Add link to custom heading.', 'knowherepro' ),
		),
		array(
			'type' => 'font_container',
			'param_name' => 'font_container',
			'value' => 'tag:h2|text_align:left',
			'settings' => array(
				'fields' => array(
					'tag' => 'h2',
					'text_align',
					'font_size',
					'line_height',
					'color',
					'tag_description' => esc_html__( 'Select element tag.', 'knowherepro' ),
					'text_align_description' => esc_html__( 'Select text alignment.', 'knowherepro' ),
					'font_size_description' => esc_html__( 'Enter font size.', 'knowherepro' ),
					'line_height_description' => esc_html__( 'Enter line height.', 'knowherepro' ),
					'color_description' => esc_html__( 'Select heading color.', 'knowherepro' ),
				),
			),
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Use theme default font family?', 'knowherepro' ),
			'param_name' => 'use_theme_fonts',
			'value' => array( esc_html__( 'Yes', 'knowherepro' ) => 'yes' ),
			'description' => esc_html__( 'Use font family from the theme.', 'knowherepro' ),
			'std' => 'yes'
		),
		array(
			'type' => 'google_fonts',
			'param_name' => 'google_fonts',
			'value' => 'font_family:Droid Serif:regular,italic,700,700italic',
			'settings' => array(
				'fields' => array(
					'font_family_description' => esc_html__( 'Select font family.', 'knowherepro' ),
					'font_style_description' => esc_html__( 'Select font styling.', 'knowherepro' ),
				),
			),
			'dependency' => array(
				'element' => 'use_theme_fonts',
				'value_not_equal_to' => 'yes',
			),
		),
//		array(
//			'type' => 'checkbox',
//			'heading' => esc_html__( 'With border?', 'knowherepro' ),
//			'param_name' => 'with_border',
//			'value' => array( esc_html__( 'Yes', 'knowherepro' ) => true ),
//			'description' => esc_html__( 'Use border bottom.', 'knowherepro' ),
//		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Extra class name', 'knowherepro' ),
			'param_name' => 'el_class',
			'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'knowherepro' ),
		),
		array(
			'type' => 'css_editor',
			'heading' => esc_html__( 'CSS box', 'knowherepro' ),
			'param_name' => 'css',
			'group' => esc_html__( 'Design Options', 'knowherepro' ),
		),
	),
) );

/* Theme Shortcodes
/* ---------------------------------------------------------------- */



/* Blockquotes
---------------------------------------------------------- */

vc_map( array(
	'name' => esc_html__( 'Blockquotes', 'knowherepro' ),
	'base' => 'vc_mad_blockquotes',
	'icon' => 'icon-wpb-mad-testimonials',
	'category' => esc_html__( 'KnowherePro', 'knowherepro' ),
	'description' => esc_html__( 'Blockquotes with image', 'knowherepro' ),
	'params' => array(
		array(
			'type' => 'attach_image',
			'heading' => esc_html__( 'Image', 'knowherepro' ),
			'param_name' => 'image',
			'value' => '',
			'description' => esc_html__( 'Select image from media library.', 'knowherepro' ),
		),
		array(
			'type' => 'textarea_html',
			'holder' => 'div',
			'heading' => esc_html__( 'Text', 'knowherepro' ),
			'param_name' => 'content',
			'value' => esc_html__( '<p>I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.</p>', 'knowherepro' ),
		)

	)
));

/* Button
---------------------------------------------------------- */

vc_map(array(
	'name' => esc_html__( 'Button', 'knowherepro' ),
	'base' => 'vc_mad_btn',
	'icon' => 'icon-wpb-mad-button',
	'category' => array( esc_html__( 'KnowherePro', 'knowherepro' ) ),
	'description' => esc_html__( 'Eye catching button', 'knowherepro' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Text', 'knowherepro' ),
			'param_name' => 'title',
			'value' => esc_html__( 'Text on the button', 'knowherepro' ),
		),
		array(
			'type' => 'vc_link',
			'heading' => esc_html__( 'URL (Link)', 'knowherepro' ),
			'param_name' => 'link',
			'description' => esc_html__( 'Add link to button.', 'knowherepro' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Style', 'knowherepro' ),
			'param_name' => 'style',
			'description' => esc_html__( 'Select style color.', 'knowherepro' ),
			'value' => array(
				esc_html__( 'Gray', 'knowherepro' ) => 'kw-gray',
				esc_html__( 'Yellow', 'knowherepro' ) => 'kw-yellow',
				esc_html__( 'Theme Color', 'knowherepro' ) => 'kw-theme-color',
			),
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Text color', 'knowherepro' ),
			'param_name' => 'text_color',
			'group' => esc_html__( 'Styling', 'knowherepro' ),
			'description' => esc_html__( 'Select custom text color.', 'knowherepro' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Size', 'knowherepro' ),
			'param_name' => 'size',
			'description' => esc_html__( 'Select button display size.', 'knowherepro' ),
			'std' => 'kw-small',
			'value' => array(
				esc_html__('Small', 'knowherepro') => 'kw-small',
				esc_html__('Medium', 'knowherepro') => 'kw-medium',
				esc_html__('Big', 'knowherepro') => 'kw-big',
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Alignment', 'knowherepro' ),
			'param_name' => 'align',
			'description' => esc_html__( 'Select button alignment.', 'knowherepro' ),
			'value' => array(
				esc_html__( 'Inline', 'knowherepro' ) => 'kw-inline',
				esc_html__( 'Left', 'knowherepro' ) => 'kw-left',
				esc_html__( 'Right', 'knowherepro' ) => 'kw-right',
				esc_html__( 'Center', 'knowherepro' ) => 'kw-center',
			),
		),
		array(
			"type" => "choose_icons",
			"heading" => esc_html__("Icon", 'knowherepro'),
			"param_name" => "icon",
			"value" => 'none',
			"description" => esc_html__('Select icon from library.', 'knowherepro')
		),
	),
));

/* Message
---------------------------------------------------------- */

vc_map(array(
	'name' => esc_html__( 'Message Box', 'knowherepro' ),
	'base' => 'vc_mad_message',
	'icon' => 'icon-wpb-mad-message-box',
	'category' => esc_html__( 'KnowherePro', 'knowherepro' ),
	'description' => esc_html__( 'Notification boxes', 'knowherepro' ),
	'params' => array(
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Style', 'knowherepro' ),
			'param_name' => 'message_box_style',
			'value' => array(
				esc_html__('Success', 'knowherepro') => 'kw-alert-success',
				esc_html__('Warning', 'knowherepro') => 'kw-alert-warning',
				esc_html__('Info', 'knowherepro') => 'kw-alert-info',
				esc_html__('Error', 'knowherepro') => 'kw-alert-error',
			),
			'description' => esc_html__( 'Select message box style.', 'knowherepro' ),
		),
		array(
			'type' => 'textarea_html',
			'holder' => 'div',
			'class' => 'messagebox_text',
			'heading' => __( 'Message text', 'knowherepro' ),
			'param_name' => 'content',
			'value' => __( '<p>I am message box. Click edit button to change this text.</p>', 'knowherepro' ),
		)
	),
));

/* List Styles
---------------------------------------------------------- */

vc_map( array(
	'name' => esc_html__( 'List Styles', 'knowherepro' ),
	'base' => 'vc_mad_list_styles',
	'icon' => 'icon-wpb-mad-list-styles',
	'category' => esc_html__( 'KnowherePro', 'knowherepro' ),
	'description' => esc_html__( 'List styles', 'knowherepro' ),
	'params' => array(
		array(
			"type" => "choose_icons",
			"heading" => esc_html__("Icon", 'knowherepro'),
			"param_name" => "icon",
			"value" => 'none',
			"description" => esc_html__( 'Select icon from library for you list styles. If you do not select an icon get a numbered list', 'knowherepro')
		),
		array(
			'type' => 'exploded_textarea',
			'heading' => esc_html__( 'List Items', 'knowherepro' ),
			'param_name' => 'values',
			'description' => esc_html__( 'Input list items values. Divide values with (|). Example: Development|Design', 'knowherepro' ),
			'value' => ''
		),
	)
) );

/* Call to Action
---------------------------------------------------------- */

vc_map( array(
	'name' => esc_html__( 'Call to Action', 'knowherepro' ),
	'base' => 'vc_mad_cta',
	'icon' => 'icon-wpb-mad-cta',
	'category' => array( esc_html__( 'KnowherePro', 'knowherepro' ) ),
	'description' => esc_html__( 'Catch visitors attention with CTA block', 'knowherepro' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Heading', 'knowherepro' ),
			'admin_label' => true,
			'param_name' => 'h2',
			'value' => '',
			'description' => esc_html__( 'Enter text for heading line. \n = LF (Line Feed) - Used as a new line character', 'knowherepro' ),
			'edit_field_class' => 'vc_col-sm-9',
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Subheading', 'knowherepro' ),
			'param_name' => 'h5',
			'value' => '',
			'description' => esc_html__( 'Enter text for subheading line.', 'knowherepro' ),
			'edit_field_class' => 'vc_col-sm-9',
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Text Color', 'knowherepro' ),
			'param_name' => 'heading_text_color',
			'group' => esc_html__( 'Styling', 'knowherepro' ),
			'description' => esc_html__( 'Select custom text color.', 'knowherepro' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Add button or form?', 'knowherepro' ),
			'param_name' => 'add',
			'value' => array(
				esc_html__( 'No', 'knowherepro' ) => '',
				esc_html__( 'Button', 'knowherepro' ) => 'button',
			),
			'description' => esc_html__( 'Add button or form for call to action.', 'knowherepro' ),
		),
		array(
			'type' => 'vc_link',
			'heading' => esc_html__( 'URL (Link)', 'knowherepro' ),
			'param_name' => 'link',
			'description' => esc_html__( 'Add link to button.', 'knowherepro' ),
			'group' => esc_html__( 'Button', 'knowherepro' ),
			'dependency' => array(
				'element' => 'add',
				'value' => array( 'button' )
			),
		),
	),
	'js_view' => 'VcCallToActionView3',
));


/* Brands Logo
---------------------------------------------------------- */

vc_map( array(
	'name' => esc_html__( 'Partners', 'knowherepro' ),
	'base' => 'vc_mad_partners',
	'icon' => 'icon-wpb-mad-brands-logo',
	'category' => esc_html__( 'KnowherePro', 'knowherepro' ),
	'description' => esc_html__( 'Our partners logo', 'knowherepro' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Title', 'knowherepro' ),
			'param_name' => 'title',
			'description' => esc_html__( 'Enter text which will be used as title. Leave blank if no title is needed.', 'knowherepro' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Subtitle', 'knowherepro' ),
			'param_name' => 'subtitle',
			'description' => esc_html__( 'Enter text which will be used as subtitle. Leave blank if no subtitle is needed.', 'knowherepro' )
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Color for title', 'knowherepro' ),
			'param_name' => 'title_color',
			'group' => esc_html__( 'Styling', 'knowherepro' ),
			'edit_field_class' => 'vc_col-sm-6',
			'description' => esc_html__( 'Select custom color for title.', 'knowherepro' ),
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Color for subtitle', 'knowherepro' ),
			'param_name' => 'subtitle_color',
			'group' => esc_html__( 'Styling', 'knowherepro' ),
			'edit_field_class' => 'vc_col-sm-6',
			'description' => esc_html__( 'Select custom color for subtitle.', 'knowherepro' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Title Alignment', 'knowherepro' ),
			'param_name' => 'align_title',
			'description' => esc_html__( 'Select title alignment.', 'knowherepro' ),
			'value' => array(
				esc_html__( 'Center', 'knowherepro' ) => '',
				esc_html__( 'Left', 'knowherepro' ) => 'align-left',
				esc_html__( 'Right', 'knowherepro' ) => 'align-right',
			),
		),
		array(
			'type' => 'attach_images',
			'heading' => esc_html__( 'Images', 'knowherepro' ),
			'param_name' => 'images',
			'value' => '',
			'description' => esc_html__( 'Select images from media library.', 'knowherepro' )
		),
		array(
			"type" => "textarea",
			"heading" => esc_html__( 'Links', 'knowherepro' ),
			"param_name" => "links",
			"holder" => "span",
			"description" => esc_html__( 'Input links values. Divide values with linebreaks (|). Example: http://partner.com | http://partner2.com', 'knowherepro' )
		),
	)
) );

/* Blog Posts
---------------------------------------------------------- */

vc_map( array(
	'name' => esc_html__( 'Blog Posts', 'knowherepro' ),
	'base' => 'vc_mad_blog_posts',
	'icon' => 'icon-wpb-mad-blog-posts',
	'category' => esc_html__( 'KnowherePro', 'knowherepro' ),
	'description' => esc_html__( 'Blog posts', 'knowherepro' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Title', 'knowherepro' ),
			'param_name' => 'title',
			'description' => esc_html__( 'Enter text which will be used as title. Leave blank if no title is needed.', 'knowherepro' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Subtitle', 'knowherepro' ),
			'param_name' => 'subtitle',
			'description' => esc_html__( 'Enter text which will be used as subtitle. Leave blank if no subtitle is needed.', 'knowherepro' )
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Color for title', 'knowherepro' ),
			'param_name' => 'title_color',
			'group' => esc_html__( 'Styling', 'knowherepro' ),
			'edit_field_class' => 'vc_col-sm-6',
			'description' => esc_html__( 'Select custom color for title.', 'knowherepro' ),
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Color for subtitle', 'knowherepro' ),
			'param_name' => 'subtitle_color',
			'group' => esc_html__( 'Styling', 'knowherepro' ),
			'edit_field_class' => 'vc_col-sm-6',
			'description' => esc_html__( 'Select custom color for subtitle.', 'knowherepro' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Blog Layout', 'knowherepro' ),
			'param_name' => 'layout',
			'value' => array(
				esc_html__( 'Default', 'knowherepro' ) => 'kw-blog-default',
				esc_html__( 'Masonry', 'knowherepro' ) => 'kw-isotope',
				esc_html__( 'Carousel', 'knowherepro' ) => 'kw-carousel',
			),
			'std' => 'kw-blog-default',
			'description' => esc_html__( 'Choose the default blog layout here.', 'knowherepro' )
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Columns', 'knowherepro' ),
			'param_name' => 'columns',
			'value' => array(
				esc_html__( '2 Columns', 'knowherepro' ) => 2,
				esc_html__( '3 Columns', 'knowherepro' ) => 3,
				esc_html__( '4 Columns', 'knowherepro' ) => 4
			),
			'description' => esc_html__( 'How many columns should be displayed?', 'knowherepro' ),
			'dependency' => array(
				'element' => 'layout',
				'value' => array( 'kw-isotope' )
			),
			'std' => 3
		),
		array(
			"type" => "get_terms",
			"term" => "category",
			'heading' => esc_html__( 'Which categories should be used for the blog?', 'knowherepro' ),
			"param_name" => "categories",
			"holder" => "div",
			'description' => esc_html__('The Page will then show entries from only those categories.', 'knowherepro'),
			'group' => esc_html__( 'Data Settings', 'knowherepro' )
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Order By', 'knowherepro' ),
			'param_name' => 'orderby',
			'value' => array(
				esc_html__( 'Date', 'knowherepro' ) => 'date',
				esc_html__( 'ID', 'knowherepro' ) => 'ID',
				esc_html__( 'Author', 'knowherepro' ) => 'author',
				esc_html__( 'Title', 'knowherepro' ) => 'title',
				esc_html__( 'Modified', 'knowherepro' ) => 'modified',
				esc_html__( 'Random', 'knowherepro' ) => 'rand',
				esc_html__( 'Comment count', 'knowherepro' ) => 'comment_count',
				esc_html__( 'Menu order', 'knowherepro' ) => 'menu_order'
			),
			'std' => 'date',
			'description' => esc_html__( 'Sort retrieved posts by parameter', 'knowherepro' ),
			'group' => esc_html__( 'Data Settings', 'knowherepro' )
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Order', 'knowherepro' ),
			'param_name' => 'order',
			'value' => array(
				esc_html__( 'DESC', 'knowherepro' ) => 'DESC',
				esc_html__( 'ASC', 'knowherepro' ) => 'ASC'
			),
			'description' => esc_html__( 'In what direction order?', 'knowherepro' ),
			'group' => esc_html__( 'Data Settings', 'knowherepro' )
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Posts Count', 'knowherepro' ),
			'param_name' => 'items',
			'value' => Knowhere_Vc_Config::array_number(1, 50, 1, array('-1' => 'All')),
			'std' => 10,
			'description' => esc_html__( 'How many items should be displayed per page?', 'knowherepro' ),
			'edit_field_class' => 'vc_col-sm-6 vc_column'
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Pagination', 'knowherepro' ),
			'param_name' => 'paginate',
			'value' => array(
				esc_html__( 'Display Pagination', 'knowherepro' ) => 'pagination',
				esc_html__( 'No option to view additional entries', 'knowherepro' ) => 'none'
			),
			'dependency' => array(
				'element' => 'layout',
				'value' => array('kw-blog-default', 'kw-isotope')
			),
			'std' => 'none',
			'description' => esc_html__( 'Should a pagination be displayed?', 'knowherepro' )
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Hide cover image', 'knowherepro' ),
			'param_name' => 'hide_cover_image',
			'description' => esc_html__( 'Hide cover image.', 'knowherepro' ),
			'value' => array( esc_html__( 'Yes, please', 'knowherepro' ) => true )
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Show button "View All"?', 'knowherepro' ),
			'param_name' => 'show_button',
			'description' => esc_html__( 'Show button.', 'knowherepro' ),
			'value' => array( esc_html__( 'Yes, please', 'knowherepro' ) => true )
		),
	)
) );

/* Progress Bar
---------------------------------------------------------- */

vc_map(array(
	'name' => esc_html__( 'Progress Bar', 'knowherepro' ),
	'base' => 'vc_mad_progress_bar',
	'icon' => 'icon-wpb-mad-progress-bar',
	'category' => esc_html__( 'KnowherePro', 'knowherepro' ),
	'description' => esc_html__( 'Animated progress bar', 'knowherepro' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Title', 'knowherepro' ),
			'param_name' => 'title',
			'description' => esc_html__( 'Enter text which will be used as title. Leave blank if no title is needed.', 'knowherepro' ),
		),
		array(
			'type' => 'param_group',
			'heading' => esc_html__( 'Values', 'knowherepro' ),
			'param_name' => 'values',
			'description' => esc_html__( 'Enter values for graph - value, title and color.', 'knowherepro' ),
			'value' => urlencode( json_encode( array(
				array(
					'label' => esc_html__( 'HTML', 'knowherepro' ),
					'value' => '70',
				),
				array(
					'label' => esc_html__( 'CSS', 'knowherepro' ),
					'value' => '90',
				),
				array(
					'label' => esc_html__( 'Java Script', 'knowherepro' ),
					'value' => '60',
				),
				array(
					'label' => esc_html__( 'PHP', 'knowherepro' ),
					'value' => '30',
				),
				array(
					'label' => esc_html__( 'Photoshop', 'knowherepro' ),
					'value' => '80',
				),
			) ) ),
			'params' => array(
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Label', 'knowherepro' ),
					'param_name' => 'label',
					'description' => esc_html__( 'Enter text used as title of bar.', 'knowherepro' ),
					'admin_label' => true,
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Value', 'knowherepro' ),
					'param_name' => 'value',
					'description' => esc_html__( 'Enter value of bar.', 'knowherepro' ),
					'admin_label' => true,
				),
			),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Units', 'knowherepro' ),
			'param_name' => 'units',
			'description' => esc_html__( 'Enter measurement units (Example: %, px, points, etc. Note: graph value and units will be appended to graph title).', 'knowherepro' ),
		),
	),
));

if ( class_exists('RWP_Reviewer') ) {

	vc_map( array(
		'name' => esc_html__( 'Reviews', 'knowherepro' ),
		'base' => 'vc_mad_reviewer',
		'icon' => 'icon-wpb-mad-reviews',
		'category' => esc_html__( 'Listing Manager', 'knowherepro' ),
		'description' => esc_html__( 'Display rating stars about the users score', 'knowherepro' ),
		'params' => array(
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Title', 'knowherepro' ),
				'param_name' => 'title',
				'description' => esc_html__( 'Enter text which will be used as title. Leave blank if no title is needed.', 'knowherepro' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Subtitle', 'knowherepro' ),
				'param_name' => 'subtitle',
				'description' => esc_html__( 'Enter text which will be used as subtitle. Leave blank if no subtitle is needed.', 'knowherepro' )
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color for title', 'knowherepro' ),
				'param_name' => 'title_color',
				'group' => esc_html__( 'Styling', 'knowherepro' ),
				'edit_field_class' => 'vc_col-sm-6',
				'description' => esc_html__( 'Select custom color for title.', 'knowherepro' ),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color for subtitle', 'knowherepro' ),
				'param_name' => 'subtitle_color',
				'group' => esc_html__( 'Styling', 'knowherepro' ),
				'edit_field_class' => 'vc_col-sm-6',
				'description' => esc_html__( 'Select custom color for subtitle.', 'knowherepro' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Title Alignment', 'knowherepro' ),
				'param_name' => 'align_title',
				'description' => esc_html__( 'Select title alignment.', 'knowherepro' ),
				'value' => array(
					esc_html__( 'Center', 'knowherepro' ) => '',
					esc_html__( 'Left', 'knowherepro' ) => 'align-left',
					esc_html__( 'Right', 'knowherepro' ) => 'align-right',
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Sort', 'knowherepro' ),
				'param_name' => 'sort',
				'description' => esc_html__( 'Select sort.', 'knowherepro' ),
				'value' => array(
					esc_html__( 'Latest', 'knowherepro' ) => 'latest',
					esc_html__( 'Top Score', 'knowherepro' ) => 'top_score'
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Reviews Count', 'knowherepro' ),
				'param_name' => 'items',
				'value' => Knowhere_Vc_Config::array_number(1, 99, 1, array()),
				'std' => 12,
				'description' => esc_html__( 'How many items should be displayed?', 'knowherepro' )
			),
			array(
				'type' => 'checkbox',
				'heading' => esc_html__( 'Enable carousel', 'knowherepro' ),
				'param_name' => 'carousel',
				'description' => esc_html__( 'Enable carousel.', 'knowherepro' ),
				'value' => array( esc_html__( 'Yes, please', 'knowherepro' ) => true )
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Type Carousel', 'knowherepro' ),
				'param_name' => 'type_carousel',
				'value' => array(
					esc_html__('Type 1', 'knowherepro') => 'kw-testimonials-carousel-v1',
					esc_html__('Type 2', 'knowherepro') => 'kw-testimonials-carousel-v2',
					esc_html__('Type 3', 'knowherepro') => 'kw-testimonials-carousel-v3'
				),
				'std' => 1,
				'description' => esc_html__('Choose layout style.', 'knowherepro')
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Style navigation', 'knowherepro' ),
				'param_name' => 'style_dots',
				'description' => esc_html__( 'Select style dots for carousel.', 'knowherepro' ),
				'value' => array(
					esc_html__( 'Light', 'knowherepro' ) => 'kw-dots-style-light',
					esc_html__( 'Darek', 'knowherepro' ) => 'kw-dots-style-dark'
				),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Excerpt Length', 'knowherepro' ),
				'param_name' => 'limit',
				'std' => 150
			)
		)
	) );

}

if ( class_exists('WooCommerce') ) {

	$order_by_values = array(
		'',
		esc_html__( 'Date', 'knowherepro' ) => 'date',
		esc_html__( 'ID', 'knowherepro' ) => 'ID',
		esc_html__( 'Author', 'knowherepro' ) => 'author',
		esc_html__( 'Title', 'knowherepro' ) => 'title',
		esc_html__( 'Modified', 'knowherepro' ) => 'modified',
		esc_html__( 'Random', 'knowherepro' ) => 'rand',
		esc_html__( 'Comment count', 'knowherepro' ) => 'comment_count',
		esc_html__( 'Menu order', 'knowherepro' ) => 'menu_order',
	);

	$order_way_values = array(
		'',
		esc_html__( 'Descending', 'knowherepro' ) => 'DESC',
		esc_html__( 'Ascending', 'knowherepro' ) => 'ASC',
	);

	vc_map(
		array(
			'name' => esc_html__( 'Products', 'knowherepro' ),
			'base' => 'vc_mad_products',
			'icon' => 'icon-wpb-mad-woocommerce',
			'category' => esc_html__( 'KnowherePro', 'knowherepro' ),
			'description' => esc_html__( 'Show products', 'knowherepro' ),
			'params' => array(
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Per page', 'knowherepro' ),
					'value' => 12,
					'save_always' => true,
					'param_name' => 'per_page',
					'description' => esc_html__( 'The "per_page" shortcode determines how many products to show on the page', 'knowherepro' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Columns', 'knowherepro' ),
					'value' => 4,
					'param_name' => 'columns',
					'save_always' => true,
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Order by', 'knowherepro' ),
					'param_name' => 'orderby',
					'value' => $order_by_values,
					'std' => 'title',
					'save_always' => true,
					'description' => sprintf( __( 'Select how to sort retrieved products. More at %s. Default by Title', 'knowherepro' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Sort order', 'knowherepro' ),
					'param_name' => 'order',
					'value' => $order_way_values,
					'save_always' => true,
					'description' => sprintf( __( 'Designates the ascending or descending order. More at %s. Default by ASC', 'knowherepro' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
				),
				array(
					"type" => "get_terms",
					"term" => "product_cat",
					'heading' => esc_html__( 'Which categories should be used for the products?', 'knowherepro' ),
					"param_name" => "category",
					'description' => esc_html__('The Page will then show products from only those categories.', 'knowherepro'),
					'group' => esc_html__( 'Data Settings', 'knowherepro' )
				)
			)
		)
	);

}

if ( class_exists('WP_Job_Manager') ) {

	vc_map( array(
		'name' => esc_html__( 'Listing Cards', 'knowherepro' ),
		'base' => 'vc_mad_listing_cards',
		'icon' => 'icon-wpb-mad-listing-cards',
		'category' => esc_html__( 'Listing Manager', 'knowherepro' ),
		'description' => esc_html__( 'Displays a list of your listings based on different criteria.', 'knowherepro' ),
		'params' => array(
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Title', 'knowherepro' ),
				'param_name' => 'title',
				'description' => esc_html__( 'Enter text which will be used as title. Leave blank if no title is needed.', 'knowherepro' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Subtitle', 'knowherepro' ),
				'param_name' => 'subtitle',
				'description' => esc_html__( 'Enter text which will be used as subtitle. Leave blank if no subtitle is needed.', 'knowherepro' )
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Tag', 'knowherepro' ),
				'param_name' => 'font_container',
				'value' => array(
					'h1' => 'h1',
					'h2' => 'h2',
					'h3' => 'h3',
					'h4' => 'h4'
				),
				'std' => 'h1',
				'description' => esc_html__( 'Choose tag for heading', 'knowherepro' ),
				'param_holder_class' => ''
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color for title', 'knowherepro' ),
				'param_name' => 'title_color',
				'group' => esc_html__( 'Styling', 'knowherepro' ),
				'edit_field_class' => 'vc_col-sm-6',
				'description' => esc_html__( 'Select custom color for title.', 'knowherepro' ),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color for subtitle', 'knowherepro' ),
				'param_name' => 'subtitle_color',
				'group' => esc_html__( 'Styling', 'knowherepro' ),
				'edit_field_class' => 'vc_col-sm-6',
				'description' => esc_html__( 'Select custom color for subtitle.', 'knowherepro' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Title Alignment', 'knowherepro' ),
				'param_name' => 'align_title',
				'description' => esc_html__( 'Select title alignment.', 'knowherepro' ),
				'value' => array(
					esc_html__( 'Center', 'knowherepro' ) => '',
					esc_html__( 'Left', 'knowherepro' ) => 'align-left',
					esc_html__( 'Right', 'knowherepro' ) => 'align-right',
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Columns', 'knowherepro' ),
				'param_name' => 'columns',
				'value' => array(
					esc_html__( '3 Columns', 'knowherepro' ) => 3,
					esc_html__( '4 Columns', 'knowherepro' ) => 4,
					esc_html__( '5 Columns', 'knowherepro' ) => 5
				),
				'std' => 3,
				'description' => esc_html__( 'How many columns should be displayed?', 'knowherepro' ),
				'param_holder_class' => ''
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Type', 'knowherepro' ),
				'param_name' => 'type',
				'value' => array(
					esc_html__('Grid', 'knowherepro') => 'kw-type-1',
					esc_html__('Masonry', 'knowherepro') => 'kw-type-2',
					esc_html__('Real State', 'knowherepro') => 'kw-type-3',
					esc_html__('Classified', 'knowherepro') => 'kw-type-5'
				),
				'std' => 'kw-type-1',
				'description' => esc_html__('Choose layout style.', 'knowherepro')
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Filter', 'knowherepro' ),
				'param_name' => 'sort',
				'value' => array(
					esc_html__( 'No', 'knowherepro' ) => false,
					esc_html__( 'Yes', 'knowherepro' ) => true
				),
				'description' => esc_html__( 'Should the sorting options based on categories be displayed?', 'knowherepro' )
			),
			array(
				'type' => 'checkbox',
				'heading' => esc_html__( 'Enable carousel', 'knowherepro' ),
				'param_name' => 'carousel',
				'description' => esc_html__( 'Enable carousel.', 'knowherepro' ),
				'dependency' => array(
					'element' => 'type',
					'value' => array('kw-type-1', 'kw-type-3', 'kw-type-5')
				),
				'value' => array( esc_html__( 'Yes, please', 'knowherepro' ) => true )
			),
			array(
				'type' => 'checkbox',
				'heading' => esc_html__( 'List View', 'knowherepro' ),
				'param_name' => 'list_view',
				'description' => esc_html__( 'Show list view.', 'knowherepro' ),
				'dependency' => array(
					'element' => 'type',
					'value' => array('kw-type-1', 'kw-type-5')
				),
				'value' => array( esc_html__( 'Yes, please', 'knowherepro' ) => true )
			),
			array(
				'type' => 'checkbox',
				'heading' => esc_html__( 'Show button "View All"?', 'knowherepro' ),
				'param_name' => 'show_button',
				'description' => esc_html__( 'Show button.', 'knowherepro' ),
				'value' => array( esc_html__( 'Yes, please', 'knowherepro' ) => true )
			),
			array(
				'type' => 'number',
				'heading' => esc_html__( 'Number of items to show', 'knowherepro' ),
				'param_name' => 'number_of_items',
				'description' => ''
			),
			array(
				"type" => "get_terms",
				"term" => "job_listing_category",
				'heading' => esc_html__( 'Which categories should be used for the job listings?', 'knowherepro' ),
				"param_name" => "categories",
				'description' => esc_html__('The Page will then show job listings from only those categories.', 'knowherepro'),
				'group' => esc_html__( 'Data Settings', 'knowherepro' )
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Show', 'knowherepro' ),
				'param_name' => 'show',
				'value' => array(
					esc_html__('All Listings', 'knowherepro') => 'all',
					esc_html__('Featured Listings', 'knowherepro') => 'featured',
				),
				'group' => esc_html__( 'Data Settings', 'knowherepro' ),
				'std' => 'all',
				'description' => ''
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Order by', 'knowherepro' ),
				'param_name' => 'orderby',
				'value' => array(
					esc_html__('Date', 'knowherepro') => 'date',
					esc_html__('Random', 'knowherepro') => 'rand'
				),
				'group' => esc_html__( 'Data Settings', 'knowherepro' ),
				'std' => 'date',
				'description' => ''
			)
		)
	) );

	vc_map( array(
		'name' => esc_html__( 'Listing and Resumes', 'knowherepro' ),
		'base' => 'vc_mad_listing_and_resumes',
		'icon' => 'icon-wpb-mad-listing-resumes',
		'category' => esc_html__( 'Listing Manager', 'knowherepro' ),
		'description' => esc_html__( 'Display a list of listing and resumes.', 'knowherepro' ),
		'params' => array(
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Title', 'knowherepro' ),
				'param_name' => 'title',
				'description' => esc_html__( 'Enter text which will be used as title. Leave blank if no title is needed.', 'knowherepro' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Subtitle', 'knowherepro' ),
				'param_name' => 'subtitle',
				'description' => esc_html__( 'Enter text which will be used as subtitle. Leave blank if no subtitle is needed.', 'knowherepro' )
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color for title', 'knowherepro' ),
				'param_name' => 'title_color',
				'group' => esc_html__( 'Styling', 'knowherepro' ),
				'edit_field_class' => 'vc_col-sm-6',
				'description' => esc_html__( 'Select custom color for title.', 'knowherepro' ),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color for subtitle', 'knowherepro' ),
				'param_name' => 'subtitle_color',
				'group' => esc_html__( 'Styling', 'knowherepro' ),
				'edit_field_class' => 'vc_col-sm-6',
				'description' => esc_html__( 'Select custom color for subtitle.', 'knowherepro' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Title Alignment', 'knowherepro' ),
				'param_name' => 'align_title',
				'description' => esc_html__( 'Select title alignment.', 'knowherepro' ),
				'value' => array(
					esc_html__( 'Center', 'knowherepro' ) => '',
					esc_html__( 'Left', 'knowherepro' ) => 'align-left',
					esc_html__( 'Right', 'knowherepro' ) => 'align-right',
				),
			),
			array(
				'type' => 'number',
				'heading' => esc_html__( 'Number of items to show', 'knowherepro' ),
				'param_name' => 'number_of_items',
				'group' => esc_html__( 'Listing Data Settings', 'knowherepro' ),
				'description' => ''
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Order by', 'knowherepro' ),
				'param_name' => 'orderby',
				'value' => array(
					esc_html__('Date', 'knowherepro') => 'date',
					esc_html__('Random', 'knowherepro') => 'rand'
				),
				'group' => esc_html__( 'Listing Data Settings', 'knowherepro' ),
				'std' => 'date',
				'description' => ''
			),
			array(
				"type" => "get_terms",
				"term" => "job_listing_category",
				'heading' => esc_html__( 'Which categories should be used for the job listings?', 'knowherepro' ),
				"param_name" => "categories",
				'description' => esc_html__('The Page will then show job listings from only those categories.', 'knowherepro'),
				'group' => esc_html__( 'Listing Data Settings', 'knowherepro' )
			),
			array(
				'type' => 'checkbox',
				'heading' => esc_html__( 'Show resumes', 'knowherepro' ),
				'param_name' => 'show_resumes',
				'description' => esc_html__( 'If checked, will be show resumes.', 'knowherepro' ),
				'value' => array( esc_html__( 'Yes', 'knowherepro' ) => 'yes' ),
			),
			array(
				'type' => 'number',
				'heading' => esc_html__( 'Number of items to show', 'knowherepro' ),
				'param_name' => 'resumes_number_of_items',
				'group' => esc_html__( 'Resumes Data Settings', 'knowherepro' ),
				'description' => '',
				'dependency' => array(
					'element' => 'show_resumes',
					'value' => array('yes'),
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Order', 'knowherepro' ),
				'param_name' => 'resumes_orderby',
				'value' => array(
					esc_html__('DESC', 'knowherepro') => 'desc',
					esc_html__('ASC', 'knowherepro') => 'asc'
				),
				'group' => esc_html__( 'Resumes Data Settings', 'knowherepro' ),
				'std' => 'desc',
				'description' => '',
				'dependency' => array(
					'element' => 'show_resumes',
					'value' => array('yes'),
				),
			),

		)
	) );

	vc_map( array(
		'name' => esc_html__( 'Featured Listing', 'knowherepro' ),
		'base' => 'vc_mad_listing_featured',
		'icon' => 'icon-wpb-mad-featured-listing',
		'category' => esc_html__( 'Listing Manager', 'knowherepro' ),
		'description' => esc_html__( 'Display a list of listing featured.', 'knowherepro' ),
		'params' => array(
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Title', 'knowherepro' ),
				'param_name' => 'title',
				'description' => esc_html__( 'Enter text which will be used as title. Leave blank if no title is needed.', 'knowherepro' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Subtitle', 'knowherepro' ),
				'param_name' => 'subtitle',
				'description' => esc_html__( 'Enter text which will be used as subtitle. Leave blank if no subtitle is needed.', 'knowherepro' )
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color for title', 'knowherepro' ),
				'param_name' => 'title_color',
				'group' => esc_html__( 'Styling', 'knowherepro' ),
				'edit_field_class' => 'vc_col-sm-6',
				'description' => esc_html__( 'Select custom color for title.', 'knowherepro' ),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color for subtitle', 'knowherepro' ),
				'param_name' => 'subtitle_color',
				'group' => esc_html__( 'Styling', 'knowherepro' ),
				'edit_field_class' => 'vc_col-sm-6',
				'description' => esc_html__( 'Select custom color for subtitle.', 'knowherepro' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Title Alignment', 'knowherepro' ),
				'param_name' => 'align_title',
				'description' => esc_html__( 'Select title alignment.', 'knowherepro' ),
				'value' => array(
					esc_html__( 'Center', 'knowherepro' ) => '',
					esc_html__( 'Left', 'knowherepro' ) => 'align-left',
					esc_html__( 'Right', 'knowherepro' ) => 'align-right',
				),
			),
			array(
				'type' => 'number',
				'heading' => esc_html__( 'Number of items to show', 'knowherepro' ),
				'param_name' => 'number_of_items',
				'description' => ''
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Order by', 'knowherepro' ),
				'param_name' => 'orderby',
				'value' => array(
					esc_html__('Date', 'knowherepro') => 'date',
					esc_html__('Random', 'knowherepro') => 'rand'
				),
				'group' => esc_html__( 'Data Settings', 'knowherepro' ),
				'std' => 'date',
				'description' => ''
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Items IDs(optional)', 'knowherepro' ),
				'param_name' => 'items_ids',
				'group' => esc_html__( 'Data Settings', 'knowherepro' ),
				'description' => ''
			)
		)
	) );

	vc_map( array(
		'name' => esc_html__( 'Listing Categories', 'knowherepro' ),
		'base' => 'vc_mad_listing_categories',
		'icon' => 'icon-wpb-mad-listing-resumes',
		'category' => esc_html__( 'Listing Manager', 'knowherepro' ),
		'description' => esc_html__( 'Display a list of listing categories based on different criteria.', 'knowherepro' ),
		'params' => array(
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Title', 'knowherepro' ),
				'param_name' => 'title',
				'description' => esc_html__( 'Enter text which will be used as title. Leave blank if no title is needed.', 'knowherepro' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Subtitle', 'knowherepro' ),
				'param_name' => 'subtitle',
				'description' => esc_html__( 'Enter text which will be used as subtitle. Leave blank if no subtitle is needed.', 'knowherepro' )
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color for title', 'knowherepro' ),
				'param_name' => 'title_color',
				'group' => esc_html__( 'Styling', 'knowherepro' ),
				'edit_field_class' => 'vc_col-sm-6',
				'description' => esc_html__( 'Select custom color for title.', 'knowherepro' ),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color for subtitle', 'knowherepro' ),
				'param_name' => 'subtitle_color',
				'group' => esc_html__( 'Styling', 'knowherepro' ),
				'edit_field_class' => 'vc_col-sm-6',
				'description' => esc_html__( 'Select custom color for subtitle.', 'knowherepro' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Title Alignment', 'knowherepro' ),
				'param_name' => 'align_title',
				'description' => esc_html__( 'Select title alignment.', 'knowherepro' ),
				'value' => array(
					esc_html__( 'Center', 'knowherepro' ) => '',
					esc_html__( 'Left', 'knowherepro' ) => 'align-left',
					esc_html__( 'Right', 'knowherepro' ) => 'align-right',
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Type', 'knowherepro' ),
				'param_name' => 'type',
				'value' => array(
					esc_html__( 'Default', 'knowherepro' ) => 'kw-type-1',
					esc_html__( 'Details', 'knowherepro' ) => 'kw-type-2',
					esc_html__( 'List', 'knowherepro' ) => 'kw-type-3',
					esc_html__( 'Classified', 'knowherepro' ) => 'kw-type-4',
					esc_html__( 'With Image', 'knowherepro' ) => 'kw-type-5'
				),
				'description' => esc_html__( 'Type?', 'knowherepro' )
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Columns', 'knowherepro' ),
				'param_name' => 'columns',
				'value' => array(
					esc_html__( '3 Columns', 'knowherepro' ) => 3,
					esc_html__( '4 Columns', 'knowherepro' ) => 4,
					esc_html__( '5 Columns', 'knowherepro' ) => 5,
				),
				'std' => 5,
				'description' => esc_html__( 'How many columns should be displayed?', 'knowherepro' ),
				'param_holder_class' => ''
			),
			array(
				'type' => 'number',
				'heading' => esc_html__( 'Number of items to show', 'knowherepro' ),
				'param_name' => 'number_of_items',
				'description' => '',
				'std' => 10,
				'group' => esc_html__( 'Category Data Settings', 'knowherepro' )
			),
			array(
				'type' => 'number',
				'heading' => esc_html__( 'Childs Number of items', 'knowherepro' ),
				'param_name' => 'childs_number_of_items',
				'description' => '',
				'dependency' => array(
					'element' => 'type',
					'value' => array( 'kw-type-4' )
				),
				'std' => 5,
				'group' => esc_html__( 'Category Data Settings', 'knowherepro' )
			),
			array(
				'type' => 'number',
				'heading' => esc_html__( 'Columns per page', 'knowherepro' ),
				'param_name' => 'columns_per_page',
				'description' => '',
				'dependency' => array(
					'element' => 'type',
					'value' => array( 'kw-type-3' )
				)
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Order by', 'knowherepro' ),
				'param_name' => 'orderby',
				'value' => array(
					esc_html__('Default', 'knowherepro') => 'name',
					esc_html__('Number of Listings', 'knowherepro') => 'count',
					esc_html__('Random', 'knowherepro') => 'rand'
				),
				'group' => esc_html__( 'Category Data Settings', 'knowherepro' ),
				'std' => 'name',
				'description' => ''
			),
			array(
				"type" => "get_terms",
				"term" => "job_listing_category",
				'heading' => esc_html__( 'Which categories should be used?', 'knowherepro' ),
				"param_name" => "categories",
				'description' => esc_html__('The Page will then show categories from only those categories.', 'knowherepro'),
				'group' => esc_html__( 'Category Data Settings', 'knowherepro' )
			),
			array(
				'type' => 'checkbox',
				'heading' => esc_html__( 'Show Region Location', 'knowherepro' ),
				'param_name' => 'by_location',
				'description' => esc_html__( 'If checked, will be show region location tab.', 'knowherepro' ),
				'value' => array( esc_html__( 'Yes', 'knowherepro' ) => 'yes' ),
			),
			array(
				'type' => 'number',
				'heading' => esc_html__( 'Number of items to show', 'knowherepro' ),
				'param_name' => 'region_number_of_items',
				'group' => esc_html__( 'Region Data Settings', 'knowherepro' ),
				'description' => '',
				'dependency' => array(
					'element' => 'by_location',
					'value' => array( 'yes' ),
				),
			),
		)
	) );

	vc_map( array(
		'name' => esc_html__( 'Listing Map', 'knowherepro' ),
		'base' => 'vc_mad_listing_map',
		'icon' => 'icon-wpb-mad-listing-map',
		'category' => esc_html__( 'Listing Manager', 'knowherepro' ),
		'description' => esc_html__( 'Display a map of listing based on different criteria.', 'knowherepro' ),
		'params' => array(
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Title', 'knowherepro' ),
				'param_name' => 'title',
				'description' => esc_html__( 'Enter text which will be used as title. Leave blank if no title is needed.', 'knowherepro' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Subtitle', 'knowherepro' ),
				'param_name' => 'subtitle',
				'description' => esc_html__( 'Enter text which will be used as subtitle. Leave blank if no subtitle is needed.', 'knowherepro' )
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color for title', 'knowherepro' ),
				'param_name' => 'title_color',
				'group' => esc_html__( 'Styling', 'knowherepro' ),
				'edit_field_class' => 'vc_col-sm-6',
				'description' => esc_html__( 'Select custom color for title.', 'knowherepro' ),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color for subtitle', 'knowherepro' ),
				'param_name' => 'subtitle_color',
				'group' => esc_html__( 'Styling', 'knowherepro' ),
				'edit_field_class' => 'vc_col-sm-6',
				'description' => esc_html__( 'Select custom color for subtitle.', 'knowherepro' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Title Alignment', 'knowherepro' ),
				'param_name' => 'align_title',
				'description' => esc_html__( 'Select title alignment.', 'knowherepro' ),
				'value' => array(
					esc_html__( 'Center', 'knowherepro' ) => '',
					esc_html__( 'Left', 'knowherepro' ) => 'align-left',
					esc_html__( 'Right', 'knowherepro' ) => 'align-right',
				),
			),
			array(
				'type' => 'number',
				'heading' => esc_html__( 'Number of items to show', 'knowherepro' ),
				'param_name' => 'per_page',
				'description' => '',
				'std' => 10
			),
		)
	) );

}

/* Testimonials
---------------------------------------------------------- */

vc_map( array(
	'name' => esc_html__( 'Testimonials', 'knowherepro' ),
	'base' => 'vc_mad_testimonials',
	'icon' => 'icon-wpb-mad-testimonials',
	'description' => esc_html__( 'Testimonials post type', 'knowherepro' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Title', 'knowherepro' ),
			'param_name' => 'title',
			'description' => esc_html__( 'Enter text which will be used as title. Leave blank if no title is needed.', 'knowherepro' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Subtitle', 'knowherepro' ),
			'param_name' => 'subtitle',
			'description' => esc_html__( 'Enter text which will be used as subtitle. Leave blank if no subtitle is needed.', 'knowherepro' )
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Color for title', 'knowherepro' ),
			'param_name' => 'title_color',
			'group' => esc_html__( 'Styling', 'knowherepro' ),
			'edit_field_class' => 'vc_col-sm-6',
			'description' => esc_html__( 'Select custom color for title.', 'knowherepro' ),
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Color for subtitle', 'knowherepro' ),
			'param_name' => 'subtitle_color',
			'group' => esc_html__( 'Styling', 'knowherepro' ),
			'edit_field_class' => 'vc_col-sm-6',
			'description' => esc_html__( 'Select custom color for subtitle.', 'knowherepro' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Title Alignment', 'knowherepro' ),
			'param_name' => 'align_title',
			'description' => esc_html__( 'Select title alignment.', 'knowherepro' ),
			'value' => array(
				esc_html__( 'Center', 'knowherepro' ) => '',
				esc_html__( 'Left', 'knowherepro' ) => 'align-left',
				esc_html__( 'Right', 'knowherepro' ) => 'align-right',
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Count Items', 'knowherepro' ),
			'param_name' => 'items',
			'value' => Knowhere_Vc_Config::array_number(1, 50, 1, array('All' => -1)),
			'std' => -1,
			'description' => esc_html__( 'How many items should be displayed per page?', 'knowherepro' )
		),
		array(
			"type" => "get_terms",
			"term" => "testimonials_category",
			'heading' => esc_html__( 'Which categories should be used for the testimonials?', 'knowherepro' ),
			"param_name" => "categories",
			"holder" => "div",
			'description' => esc_html__('The Page will then show testimonials from only those categories.', 'knowherepro'),
			'group' => esc_html__( 'Data Settings', 'knowherepro' )
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Order By', 'knowherepro' ),
			'param_name' => 'orderby',
			'value' => Knowhere_Vc_Config::get_order_sort_array(),
			'description' => esc_html__( 'Sort retrieved items by parameter', 'knowherepro' ),
			'edit_field_class' => 'vc_col-sm-6',
			'group' => esc_html__( 'Data Settings', 'knowherepro' )
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Order', 'knowherepro' ),
			'param_name' => 'order',
			'value' => array(
				esc_html__( 'DESC', 'knowherepro' ) => 'DESC',
				esc_html__( 'ASC', 'knowherepro' ) => 'ASC',
			),
			'description' => esc_html__( 'Direction Order', 'knowherepro' ),
			'edit_field_class' => 'vc_col-sm-6',
			'group' => esc_html__( 'Data Settings', 'knowherepro' )
		)
	)
) );

/* Agency
---------------------------------------------------------- */

if ( class_exists('Knowhere_Post_Type_Agency') ) {

	vc_map( array(
		'name' => esc_html__( 'Agency', 'knowherepro' ),
		'base' => 'vc_mad_agency',
		'icon' => 'icon-wpb-mad-agency',
		'description' => esc_html__( 'Agency post type', 'knowherepro' ),
		'category' => esc_html__( 'Listing Manager', 'knowherepro' ),
		'params' => array(
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Title', 'knowherepro' ),
				'param_name' => 'title',
				'description' => esc_html__( 'Enter text which will be used as title. Leave blank if no title is needed.', 'knowherepro' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Subtitle', 'knowherepro' ),
				'param_name' => 'subtitle',
				'description' => esc_html__( 'Enter text which will be used as subtitle. Leave blank if no subtitle is needed.', 'knowherepro' )
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color for title', 'knowherepro' ),
				'param_name' => 'title_color',
				'group' => esc_html__( 'Styling', 'knowherepro' ),
				'edit_field_class' => 'vc_col-sm-6',
				'description' => esc_html__( 'Select custom color for title.', 'knowherepro' ),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color for subtitle', 'knowherepro' ),
				'param_name' => 'subtitle_color',
				'group' => esc_html__( 'Styling', 'knowherepro' ),
				'edit_field_class' => 'vc_col-sm-6',
				'description' => esc_html__( 'Select custom color for subtitle.', 'knowherepro' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Title Alignment', 'knowherepro' ),
				'param_name' => 'align_title',
				'description' => esc_html__( 'Select title alignment.', 'knowherepro' ),
				'value' => array(
					esc_html__( 'Center', 'knowherepro' ) => '',
					esc_html__( 'Left', 'knowherepro' ) => 'align-left',
					esc_html__( 'Right', 'knowherepro' ) => 'align-right',
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Count Items', 'knowherepro' ),
				'param_name' => 'items',
				'value' => Knowhere_Vc_Config::array_number(1, 50, 1, array('All' => -1)),
				'std' => -1,
				'description' => esc_html__( 'How many items should be displayed per page?', 'knowherepro' )
			),
//			array(
//				"type" => "get_terms",
//				"term" => "agent_category",
//				'heading' => esc_html__( 'Which categories should be used for the agents?', 'knowherepro' ),
//				"param_name" => "categories",
//				"holder" => "div",
//				'description' => esc_html__('The Page will then show agents from only those categories.', 'knowherepro'),
//				'group' => esc_html__( 'Data Settings', 'knowherepro' )
//			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Order By', 'knowherepro' ),
				'param_name' => 'orderby',
				'value' => Knowhere_Vc_Config::get_order_sort_array(),
				'description' => esc_html__( 'Sort retrieved items by parameter', 'knowherepro' ),
				'edit_field_class' => 'vc_col-sm-6',
				'group' => esc_html__( 'Data Settings', 'knowherepro' )
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Order', 'knowherepro' ),
				'param_name' => 'order',
				'value' => array(
					esc_html__( 'DESC', 'knowherepro' ) => 'DESC',
					esc_html__( 'ASC', 'knowherepro' ) => 'ASC',
				),
				'description' => esc_html__( 'Direction Order', 'knowherepro' ),
				'edit_field_class' => 'vc_col-sm-6',
				'group' => esc_html__( 'Data Settings', 'knowherepro' )
			)
		)
	) );

}

/* Search Properties with Slideshow
---------------------------------------------------------- */

//vc_map( array(
//	'name' => esc_html__( 'Search Properties with Slideshow', 'knowherepro' ),
//	'base' => 'vc_mad_search_properties_with_slideshow',
//	'icon' => 'icon-wpb-mad-listing-cards',
//	'description' => esc_html__( 'Search Properties with Slideshow', 'knowherepro' ),
//	'category' => esc_html__( 'Listing Manager', 'knowherepro' ),
//	'show_settings_on_create' => false
//) );

/* Agent
---------------------------------------------------------- */

if ( class_exists('Knowhere_Post_Type_Agent') ) {

	vc_map( array(
		'name' => esc_html__( 'Agents', 'knowherepro' ),
		'base' => 'vc_mad_agent',
		'icon' => 'icon-wpb-mad-agents',
		'description' => esc_html__( 'Agents post type', 'knowherepro' ),
		'category' => esc_html__( 'Listing Manager', 'knowherepro' ),
		'params' => array(
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Title', 'knowherepro' ),
				'param_name' => 'title',
				'description' => esc_html__( 'Enter text which will be used as title. Leave blank if no title is needed.', 'knowherepro' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Subtitle', 'knowherepro' ),
				'param_name' => 'subtitle',
				'description' => esc_html__( 'Enter text which will be used as subtitle. Leave blank if no subtitle is needed.', 'knowherepro' )
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color for title', 'knowherepro' ),
				'param_name' => 'title_color',
				'group' => esc_html__( 'Styling', 'knowherepro' ),
				'edit_field_class' => 'vc_col-sm-6',
				'description' => esc_html__( 'Select custom color for title.', 'knowherepro' ),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color for subtitle', 'knowherepro' ),
				'param_name' => 'subtitle_color',
				'group' => esc_html__( 'Styling', 'knowherepro' ),
				'edit_field_class' => 'vc_col-sm-6',
				'description' => esc_html__( 'Select custom color for subtitle.', 'knowherepro' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Title Alignment', 'knowherepro' ),
				'param_name' => 'align_title',
				'description' => esc_html__( 'Select title alignment.', 'knowherepro' ),
				'value' => array(
					esc_html__( 'Center', 'knowherepro' ) => '',
					esc_html__( 'Left', 'knowherepro' ) => 'align-left',
					esc_html__( 'Right', 'knowherepro' ) => 'align-right',
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Count Items', 'knowherepro' ),
				'param_name' => 'items',
				'value' => Knowhere_Vc_Config::array_number(1, 50, 1, array('All' => -1)),
				'std' => -1,
				'description' => esc_html__( 'How many items should be displayed per page?', 'knowherepro' )
			),
			array(
				"type" => "get_terms",
				"term" => "agent_category",
				'heading' => esc_html__( 'Which categories should be used for the agents?', 'knowherepro' ),
				"param_name" => "categories",
				"holder" => "div",
				'description' => esc_html__('The Page will then show agents from only those categories.', 'knowherepro'),
				'group' => esc_html__( 'Data Settings', 'knowherepro' )
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Order By', 'knowherepro' ),
				'param_name' => 'orderby',
				'value' => Knowhere_Vc_Config::get_order_sort_array(),
				'description' => esc_html__( 'Sort retrieved items by parameter', 'knowherepro' ),
				'edit_field_class' => 'vc_col-sm-6',
				'group' => esc_html__( 'Data Settings', 'knowherepro' )
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Order', 'knowherepro' ),
				'param_name' => 'order',
				'value' => array(
					esc_html__( 'DESC', 'knowherepro' ) => 'DESC',
					esc_html__( 'ASC', 'knowherepro' ) => 'ASC',
				),
				'description' => esc_html__( 'Direction Order', 'knowherepro' ),
				'edit_field_class' => 'vc_col-sm-6',
				'group' => esc_html__( 'Data Settings', 'knowherepro' )
			)
		)
	) );

}

/* Counter Bar
---------------------------------------------------------- */

vc_map( array(
	"name" => esc_html__("Counter", 'knowherepro' ),
	"base"=> 'vc_mad_counter',
	"icon" => 'icon-wpb-mad-counter',
	'category' => esc_html__( 'KnowherePro', 'knowherepro' ),
	"description" => esc_html__( 'Counter', 'knowherepro' ),
	"params" => array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Title', 'knowherepro' ),
			'param_name' => 'title',
			'description' => esc_html__( 'Enter text which will be used as title. Leave blank if no title is needed.', 'knowherepro' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Subtitle', 'knowherepro' ),
			'param_name' => 'subtitle',
			'description' => esc_html__( 'Enter text which will be used as subtitle. Leave blank if no subtitle is needed.', 'knowherepro' )
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Color for title', 'knowherepro' ),
			'param_name' => 'title_color',
			'group' => esc_html__( 'Styling', 'knowherepro' ),
			'edit_field_class' => 'vc_col-sm-6',
			'description' => esc_html__( 'Select custom color for title.', 'knowherepro' ),
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Color for subtitle', 'knowherepro' ),
			'param_name' => 'subtitle_color',
			'group' => esc_html__( 'Styling', 'knowherepro' ),
			'edit_field_class' => 'vc_col-sm-6',
			'description' => esc_html__( 'Select custom color for subtitle.', 'knowherepro' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Title Alignment', 'knowherepro' ),
			'param_name' => 'align_title',
			'description' => esc_html__( 'Select title alignment.', 'knowherepro' ),
			'value' => array(
				esc_html__( 'Center', 'knowherepro' ) => '',
				esc_html__( 'Left', 'knowherepro' ) => 'align-left',
				esc_html__( 'Right', 'knowherepro' ) => 'align-right',
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Type', 'knowherepro' ),
			'param_name' => 'type',
			'value' => array(
				esc_html__('Type 1', 'knowherepro') => 'kw-type-1',
				esc_html__('Type 2', 'knowherepro') => 'kw-type-2',
				esc_html__('Type 3', 'knowherepro') => 'kw-type-3',
			),
			'std' => 'kw-type-1',
			'description' => esc_html__('Choose layout type.', 'knowherepro')
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Columns', 'knowherepro' ),
			'param_name' => 'columns',
			'value' => array(
				2 => 2,
				3 => 3,
				4 => 4,
			),
			'std' => 4,
			'description' => esc_html__('Choose count columns.', 'knowherepro')
		),
		array(
			'type' => 'param_group',
			'heading' => esc_html__( 'Values', 'knowherepro' ),
			'param_name' => 'values',
			'description' => esc_html__( 'Enter values - value and title.', 'knowherepro' ),
			'value' => urlencode( json_encode( array(
				array(
					'label' => esc_html__( 'Satisfied Clients', 'knowherepro' ),
					'value' => '8480',
					'icon'  => ''
				),
				array(
					'label' => esc_html__( 'Customer Reviews', 'knowherepro' ),
					'value' => '5350',
					'icon'  => ''
				),
				array(
					'label' => esc_html__( 'Added Listings', 'knowherepro' ),
					'value' => '996938',
					'icon'  => ''
				),
				array(
					'label' => esc_html__( 'Successful Reservations', 'knowherepro' ),
					'value' => '6350',
					'icon'  => ''
				),
			) ) ),
			'params' => array(
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Label', 'knowherepro' ),
					'param_name' => 'label',
					'description' => esc_html__( 'Enter text used as title.', 'knowherepro' ),
					'admin_label' => true,
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Value', 'knowherepro' ),
					'param_name' => 'value',
					'description' => esc_html__( 'Enter value.', 'knowherepro' ),
					'admin_label' => true,
				),
				array(
					"type" => "choose_icons",
					"heading" => esc_html__("Icon", 'knowherepro'),
					"param_name" => "icon",
					"value" => 'none',
					"description" => esc_html__( 'Select icon from library.', 'knowherepro')
				)
			)
		),
	)
));

/* Dropcap
---------------------------------------------------------- */
vc_map( array(
	'name' => esc_html__( 'Dropcap', 'knowherepro' ),
	'base' => 'vc_mad_dropcap',
	'icon' => 'icon-wpb-mad-dropcap',
	'category' => esc_html__( 'KnowherePro', 'knowherepro' ),
	'description' => esc_html__( 'Dropcap', 'knowherepro' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Letter', 'knowherepro' ),
			'param_name' => 'letter',
			'admin_label' => true,
			'description' => ''
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Color for dropcap', 'knowherepro' ),
			'param_name' => 'dropcap_color',
			'description' => esc_html__( 'Select custom color for dropcap.', 'knowherepro' ),
		),
		array(
			'type' => 'textarea_html',
			'holder' => 'div',
			'heading' => esc_html__( 'Text', 'knowherepro' ),
			'param_name' => 'content',
			'value' => ''
		),
	)
));


/*** Visual Composer Content elements refresh ***/
class knowhereVcSharedLibrary {
	// Here we will store plugin wise (shared) settings. Colors, Locations, Sizes, etc...
	/**
	 * @var array
	 */
	private static $colors = array(
		'Blue' => 'blue',
		'Turquoise' => 'turquoise',
		'Pink' => 'pink',
		'Violet' => 'violet',
		'Peacoc' => 'peacoc',
		'Chino' => 'chino',
		'Mulled Wine' => 'mulled_wine',
		'Vista Blue' => 'vista_blue',
		'Black' => 'black',
		'Grey' => 'grey',
		'Orange' => 'orange',
		'Sky' => 'sky',
		'Green' => 'green',
		'Juicy pink' => 'juicy_pink',
		'Sandy brown' => 'sandy_brown',
		'Purple' => 'purple',
		'White' => 'white'
	);

	/**
	 * @var array
	 */
	public static $icons = array(
		'Glass' => 'glass',
		'Music' => 'music',
		'Search' => 'search'
	);

	/**
	 * @var array
	 */
	public static $sizes = array(
		'Mini' => 'xs',
		'Small' => 'sm',
		'Normal' => 'md',
		'Large' => 'lg'
	);

	/**
	 * @var array
	 */
	public static $button_styles = array(
		'Rounded' => 'rounded',
		'Square' => 'square',
		'Round' => 'round',
		'Outlined' => 'outlined',
		'3D' => '3d',
		'Square Outlined' => 'square_outlined'
	);

	/**
	 * @var array
	 */
	public static $message_box_styles = array(
		'Standard' => 'standard',
		'Solid' => 'solid',
		'Solid icon' => 'solid-icon',
		'Outline' => 'outline',
		'3D' => '3d',
	);

	/**
	 * Toggle styles
	 * @var array
	 */
	public static $toggle_styles = array(
		'Default' => 'default',
		'Simple' => 'simple',
		'Round' => 'round',
		'Round Outline' => 'round_outline',
		'Rounded' => 'rounded',
		'Rounded Outline' => 'rounded_outline',
		'Square' => 'square',
		'Square Outline' => 'square_outline',
		'Arrow' => 'arrow',
		'Text Only' => 'text_only',
	);

	/**
	 * Animation styles
	 * @var array
	 */
	public static $animation_styles = array(
		'Bounce' => 'easeOutBounce',
		'Elastic' => 'easeOutElastic',
		'Back' => 'easeOutBack',
		'Cubic' => 'easeinOutCubic',
		'Quint' => 'easeinOutQuint',
		'Quart' => 'easeOutQuart',
		'Quad' => 'easeinQuad',
		'Sine' => 'easeOutSine'
	);

	/**
	 * @var array
	 */
	public static $cta_styles = array(
		'Rounded' => 'rounded',
		'Square' => 'square',
		'Round' => 'round',
		'Outlined' => 'outlined',
		'Square Outlined' => 'square_outlined'
	);

	/**
	 * @var array
	 */
	public static $txt_align = array(
		'Left' => 'left',
		'Right' => 'right',
		'Center' => 'center',
		'Justify' => 'justify'
	);

	/**
	 * @var array
	 */
	public static $el_widths = array(
		'100%' => '',
		'90%' => '90',
		'80%' => '80',
		'70%' => '70',
		'60%' => '60',
		'50%' => '50'
	);

	/**
	 * @var array
	 */
	public static $sep_widths = array(
		'1px' => '',
		'2px' => '2',
		'3px' => '3',
		'4px' => '4',
		'5px' => '5',
		'6px' => '6',
		'7px' => '7',
		'8px' => '8',
		'9px' => '9',
		'10px' => '10'
	);

	/**
	 * @var array
	 */
	public static $sep_styles = array(
		'Border' => '',
		'Dashed' => 'dashed',
		'Dotted' => 'dotted',
		'Double' => 'double'
	);

	/**
	 * @var array
	 */
	public static $box_styles = array(
		'Default' => '',
		'Rounded' => 'vc_box_rounded',
		'Border' => 'vc_box_border',
		'Outline' => 'vc_box_outline',
		'Shadow' => 'vc_box_shadow',
		'Bordered shadow' => 'vc_box_shadow_border',
		'3D Shadow' => 'vc_box_shadow_3d',
		'Round' => 'vc_box_circle', //new
		'Round Border' => 'vc_box_border_circle', //new
		'Round Outline' => 'vc_box_outline_circle', //new
		'Round Shadow' => 'vc_box_shadow_circle', //new
		'Round Border Shadow' => 'vc_box_shadow_border_circle', //new
		'Circle' => 'vc_box_circle_2', //new
		'Circle Border' => 'vc_box_border_circle_2', //new
		'Circle Outline' => 'vc_box_outline_circle_2', //new
		'Circle Shadow' => 'vc_box_shadow_circle_2', //new
		'Circle Border Shadow' => 'vc_box_shadow_border_circle_2' //new
	);

	/**
	 * @return array
	 */
	public static function getColors() {
		return self::$colors;
	}

	/**
	 * @return array
	 */
	public static function getIcons() {
		return self::$icons;
	}

	/**
	 * @return array
	 */
	public static function getSizes() {
		return self::$sizes;
	}

	/**
	 * @return array
	 */
	public static function getButtonStyles() {
		return self::$button_styles;
	}

	/**
	 * @return array
	 */
	public static function getMessageBoxStyles() {
		return self::$message_box_styles;
	}

	/**
	 * @return array
	 */
	public static function getToggleStyles() {
		return self::$toggle_styles;
	}

	/**
	 * @return array
	 */
	public static function getAnimationStyles() {
		return self::$animation_styles;
	}

	/**
	 * @return array
	 */
	public static function getCtaStyles() {
		return self::$cta_styles;
	}

	/**
	 * @return array
	 */
	public static function getTextAlign() {
		return self::$txt_align;
	}

	/**
	 * @return array
	 */
	public static function getBorderWidths() {
		return self::$sep_widths;
	}

	/**
	 * @return array
	 */
	public static function getElementWidths() {
		return self::$el_widths;
	}

	/**
	 * @return array
	 */
	public static function getSeparatorStyles() {
		return self::$sep_styles;
	}

	/**
	 * @return array
	 */
	public static function getBoxStyles() {
		return self::$box_styles;
	}

	public static function getColorsDashed() {
		$colors = array(
			esc_html__( 'Blue', 'knowherepro' ) => 'blue',
			esc_html__( 'Turquoise', 'knowherepro' ) => 'turquoise',
			esc_html__( 'Pink', 'knowherepro' ) => 'pink',
			esc_html__( 'Violet', 'knowherepro' ) => 'violet',
			esc_html__( 'Peacoc', 'knowherepro' ) => 'peacoc',
			esc_html__( 'Chino', 'knowherepro' ) => 'chino',
			esc_html__( 'Mulled Wine', 'knowherepro' ) => 'mulled-wine',
			esc_html__( 'Vista Blue', 'knowherepro' ) => 'vista-blue',
			esc_html__( 'Black', 'knowherepro' ) => 'black',
			esc_html__( 'Grey', 'knowherepro' ) => 'grey',
			esc_html__( 'Orange', 'knowherepro' ) => 'orange',
			esc_html__( 'Sky', 'knowherepro' ) => 'sky',
			esc_html__( 'Green', 'knowherepro' ) => 'green',
			esc_html__( 'Juicy pink', 'knowherepro' ) => 'juicy-pink',
			esc_html__( 'Sandy brown', 'knowherepro' ) => 'sandy-brown',
			esc_html__( 'Purple', 'knowherepro' ) => 'purple',
			esc_html__( 'White', 'knowherepro' ) => 'white'
		);

		return $colors;
	}
}

/**
 * @param string $asset
 *
 * @return array
 */
function knowheregetVcShared( $asset = '' ) {
	switch ( $asset ) {
		case 'colors':
			return knowhereVcSharedLibrary::getColors();
			break;

		case 'colors-dashed':
			return knowhereVcSharedLibrary::getColorsDashed();
			break;

		case 'icons':
			return knowhereVcSharedLibrary::getIcons();
			break;

		case 'sizes':
			return knowhereVcSharedLibrary::getSizes();
			break;

		case 'button styles':
		case 'alert styles':
			return knowhereVcSharedLibrary::getButtonStyles();
			break;
		case 'message_box_styles':
			return knowhereVcSharedLibrary::getMessageBoxStyles();
			break;
		case 'cta styles':
			return knowhereVcSharedLibrary::getCtaStyles();
			break;

		case 'text align':
			return knowhereVcSharedLibrary::getTextAlign();
			break;

		case 'cta widths':
		case 'separator widths':
			return knowhereVcSharedLibrary::getElementWidths();
			break;

		case 'separator styles':
			return knowhereVcSharedLibrary::getSeparatorStyles();
			break;

		case 'separator border widths':
			return knowhereVcSharedLibrary::getBorderWidths();
			break;

		case 'single image styles':
			return knowhereVcSharedLibrary::getBoxStyles();
			break;

		case 'toggle styles':
			return knowhereVcSharedLibrary::getToggleStyles();
			break;

		case 'animation styles':
			return knowhereVcSharedLibrary::getAnimationStyles();
			break;

		default:
			# code...
			break;
	}

	return '';
}