<?php $settings = knowhere_check_theme_options(); ?>

// Selection Color
@selection_color: <?php echo esc_attr($settings['selection-color']) ?>;

// Primary Color
@primary_color: <?php echo esc_attr($settings['primary-color']) ?>;
@primary_inverse_color: <?php echo esc_attr($settings['primary-inverse-color']) ?>;

// Secondary Color
@secondary_color: <?php echo esc_attr($settings['secondary-color']) ?>;
@secondary_inverse_color: <?php echo esc_attr($settings['secondary-inverse-color']) ?>;

// Header Type 1 Button Color
@header_type_1_button_color: <?php echo esc_attr($settings['header-type-1-button-color']) ?>;

// Header Background Type 2
@header_type_2_bg_repeat: <?php echo esc_attr($settings['header-type-2-bg']['background-repeat']) ?>;
@header_type_2_bg_size: <?php echo esc_attr($settings['header-type-2-bg']['background-size']) ?>;
@header_type_2_bg_attachment: <?php echo esc_attr($settings['header-type-2-bg']['background-attachment']) ?>;
@header_type_2_bg_position: <?php echo esc_attr($settings['header-type-2-bg']['background-position']) ?>;
<?php
$header_image = str_replace(array('http://', 'https://'), array('//', '//'), esc_attr($settings['header-type-2-bg']['background-image']))
?>
@header_type_2_bg_image: <?php echo esc_url($header_image) != 'none'?'url('.esc_url($header_image).')':$header_image ?>;

// Header Bg Color Type 4
@header_type_4_bg_color: <?php echo esc_attr($settings['header-type-4-bg-color']) ?>;

@page_header_bg_color: <?php echo esc_attr($settings['page-header-bg-color']) ?>;
@page_header_overlay_bg_color: <?php echo esc_attr($settings['page-header-overlay-bg-color']) ?>;

// Header Type 5
@header_type_5_search_form_bg_color: <?php echo esc_attr($settings['header-type-5-search-form-bg-color']) ?>;

// Header Type 6
@header_type_6_button_bg_color: <?php echo esc_attr($settings['header-type-6-bg-color']) ?>;
@header_type_6_button_logo_container_bg_color: <?php echo esc_attr($settings['header-type-6-logo-container-bg-color']) ?>;

// Other Color
@other_bg_color: <?php echo esc_attr($settings['other-bg-color']) ?>;
@other_text_color: <?php echo esc_attr($settings['other-text-color']) ?>;
@translucent_color: <?php echo esc_attr($settings['translucent-bg-color']) ?>;

// Job Manager Colors
@job_featured_label_color: <?php echo esc_attr($settings['job-featured-label-color']) ?>;
@job_featured_label_bg_color: <?php echo esc_attr($settings['job-featured-label-bg-color']) ?>;
@job_filter_submit_button_text_color: <?php echo esc_attr($settings['job-filter-submit-button-text-color']) ?>;
@job_filter_submit_button_bg_color: <?php echo esc_attr($settings['job-filter-submit-button-bg-color']) ?>;
@job_filter_submit_button_bg_color_hover: <?php echo esc_attr($settings['job-filter-submit-button-hover-bg-color']) ?>;
@job_listing_item_price: <?php echo esc_attr($settings['job-listing-item-price-text-color']) ?>;

// WooCommerce Colors
@product_button_color: <?php echo esc_attr($settings['product-button-color']) ?>;
@product_button_bg_color: <?php echo esc_attr($settings['product-button-bg-color']) ?>;
@product_new_label_color: <?php echo esc_attr($settings['product-new-label-color']) ?>;
@product_new_label_bg_color: <?php echo esc_attr($settings['product-new-label-bg-color']) ?>;
@product_sale_label_color: <?php echo esc_attr($settings['product-sale-label-color']) ?>;
@product_sale_label_bg_color: <?php echo esc_attr($settings['product-sale-label-bg-color']) ?>;

// Typography
@body_font_family: <?php echo esc_attr($settings['body-font']['font-family']) ?>;
@body_font_weight: <?php echo esc_attr($settings['body-font']['font-weight']) ?>;
@body_font_size: <?php echo esc_attr($settings['body-font']['font-size']) ?>;
@body_line_height: <?php echo esc_attr($settings['body-font']['line-height']) ?>;
@body_color: <?php echo esc_attr($settings['body-font']['color']) ?>;

// Headings
@h1_font_family: <?php echo esc_attr($settings['h1-font']['font-family']) ?>;
@h1_font_weight: <?php echo esc_attr($settings['h1-font']['font-weight']) ?>;
@h1_font_size: <?php echo esc_attr($settings['h1-font']['font-size']) ?>;
@h1_color: <?php echo esc_attr($settings['h1-font']['color']) ?>;

@h2_font_family: <?php echo esc_attr($settings['h2-font']['font-family']) ?>;
@h2_font_weight: <?php echo esc_attr($settings['h2-font']['font-weight']) ?>;
@h2_font_size: <?php echo esc_attr($settings['h2-font']['font-size']) ?>;
@h2_color: <?php echo esc_attr($settings['h2-font']['color']) ?>;

@h3_font_family: <?php echo esc_attr($settings['h3-font']['font-family']) ?>;
@h3_font_weight: <?php echo esc_attr($settings['h3-font']['font-weight']) ?>;
@h3_font_size: <?php echo esc_attr($settings['h3-font']['font-size']) ?>;
@h3_color: <?php echo esc_attr($settings['h3-font']['color']) ?>;

@h4_font_family: <?php echo esc_attr($settings['h4-font']['font-family']) ?>;
@h4_font_weight: <?php echo esc_attr($settings['h4-font']['font-weight']) ?>;
@h4_font_size: <?php echo esc_attr($settings['h4-font']['font-size']) ?>;
@h4_color: <?php echo esc_attr($settings['h4-font']['color']) ?>;

@h5_font_family: <?php echo esc_attr($settings['h5-font']['font-family']) ?>;
@h5_font_weight: <?php echo esc_attr($settings['h5-font']['font-weight']) ?>;
@h5_font_size: <?php echo esc_attr($settings['h5-font']['font-size']) ?>;
@h5_color: <?php echo esc_attr($settings['h5-font']['color']) ?>;

@h6_font_family: <?php echo esc_attr($settings['h6-font']['font-family']) ?>;
@h6_font_weight: <?php echo esc_attr($settings['h6-font']['font-weight']) ?>;
@h6_font_size: <?php echo esc_attr($settings['h6-font']['font-size']) ?>;
@h6_color: <?php echo esc_attr($settings['h6-font']['color']) ?>;

// Body
@body_bg_color: <?php echo esc_attr($settings['body-bg']['background-color']) ?>;
@body_bg_repeat: <?php echo esc_attr($settings['body-bg']['background-repeat']) ?>;
@body_bg_size: <?php echo esc_attr($settings['body-bg']['background-size']) ?>;
@body_bg_attachment: <?php echo esc_attr($settings['body-bg']['background-attachment']) ?>;
@body_bg_position: <?php echo esc_attr($settings['body-bg']['background-position']) ?>;
<?php
$image = str_replace(array('http://', 'https://'), array('//', '//'), esc_attr($settings['body-bg']['background-image']))
?>
@body_bg_image: <?php echo esc_attr($image) != 'none'?'url('.esc_url($image).')':$image ?>;

// Listing
@job_category_dark_color: <?php echo esc_attr($settings['job-category-dark-color']) ?>;
@job_category_light_color: <?php echo esc_attr($settings['job-category-light-color']) ?>;

<?php

$all_listing_category_terms = get_terms(
	'job_listing_category',
	array(
		'order' => 'DESC',
		'hide_empty' => false,
		'hierarchical' => true,
		'pad_counts' => true
	)
);

$listing_colors_icons = array();

if ( ! is_wp_error( $all_listing_category_terms ) && ( is_array( $all_listing_category_terms ) || is_object( $all_listing_category_terms ) ) ) {
	foreach( $all_listing_category_terms as $key => $term ) {

		$term_color_icon = knowhere_job_get_term( 'pix_term_color_icon', $term->term_id, false );

		if ( !empty($term_color_icon) ) {
			$listing_colors_icons[] = 'kw-category-icon-color-' . $term->term_id . ' ' . $term_color_icon;
		}

	}
}

?>

@kw-listing-icon-colors: <?php echo implode( ', ', $listing_colors_icons ); ?>;

// Menu
@menu_light_bg_color: <?php echo esc_attr($settings['header-light-bg-color']) ?>;
@menu_font_family: <?php echo esc_attr($settings['menu-font']['font-family']) ?>;
@menu_font_weight: <?php echo esc_attr($settings['menu-font']['font-weight']) ?>;
@menu_font_size: <?php echo esc_attr($settings['menu-font']['font-size']) ?>;
@menu_line_height: <?php echo esc_attr($settings['menu-font']['line-height']) ?>;
@menu_text_transform: <?php echo esc_attr($settings['menu-text-transform']) ?>;
@main_menu_top_level_link_color: <?php echo esc_attr($settings['primary-toplevel-link-color']['regular']) ?>;
@main_menu_top_level_hover_color: <?php echo esc_attr($settings['primary-toplevel-link-color']['hover']) ?>;

@header_type_5_menu_font_family: <?php echo esc_attr($settings['header-type-5-menu-font']['font-family']) ?>;
@header_type_5_menu_font_weight: <?php echo esc_attr($settings['header-type-5-menu-font']['font-weight']) ?>;
@header_type_5_menu_font_size: <?php echo esc_attr($settings['header-type-5-menu-font']['font-size']) ?>;
@header_type_5_menu_line_height: <?php echo esc_attr($settings['header-type-5-menu-font']['line-height']) ?>;

@vr_menu_font_family: <?php echo esc_attr($settings['vr-menu-font']['font-family']) ?>;
@vr_menu_font_weight: <?php echo esc_attr($settings['vr-menu-font']['font-weight']) ?>;
@vr_menu_font_size: <?php echo esc_attr($settings['vr-menu-font']['font-size']) ?>;
@vr_menu_line_height: <?php echo esc_attr($settings['vr-menu-font']['line-height']) ?>;

@vr_menu_text_transform: <?php echo esc_attr($settings['vr-menu-text-transform']) ?>;
@vr_main_menu_top_level_link_color: <?php echo esc_attr($settings['vr-primary-toplevel-link-color']['regular']) ?>;
@vr_main_menu_top_level_hover_color: <?php echo esc_attr($settings['vr-primary-toplevel-link-color']['hover']) ?>;
@vr_main_menu_top_level_active_color: <?php echo esc_attr($settings['vr-primary-toplevel-link-color']['active']) ?>;

// Sub Menu
@sub_menu_font_family: <?php echo esc_attr($settings['sub-menu-font']['font-family']) ?>;
@sub_menu_weight: <?php echo esc_attr($settings['sub-menu-font']['font-weight']) ?>;
@sub_menu_size: <?php echo esc_attr($settings['sub-menu-font']['font-size']) ?>;
@sub_menu_line_height: <?php echo esc_attr($settings['sub-menu-font']['line-height']) ?>;
@sub_menu_link_color: <?php echo esc_attr($settings['sub-menu-text-color']['regular']) ?>;
@sub_menu_hover_color: <?php echo esc_attr($settings['sub-menu-text-color']['hover']) ?>;
@sub_menu_active_color: <?php echo esc_attr($settings['sub-menu-text-color']['active']) ?>;

@vr_sub_menu_font_family: <?php echo esc_attr($settings['vr-sub-menu-font']['font-family']) ?>;
@vr_sub_menu_weight: <?php echo esc_attr($settings['vr-sub-menu-font']['font-weight']) ?>;
@vr_sub_menu_size: <?php echo esc_attr($settings['vr-sub-menu-font']['font-size']) ?>;
@vr_sub_menu_line_height: <?php echo esc_attr($settings['vr-sub-menu-font']['line-height']) ?>;
@vr_sub_menu_link_color: <?php echo esc_attr($settings['vr-sub-menu-text-color']['regular']) ?>;
@vr_sub_menu_hover_color: <?php echo esc_attr($settings['vr-sub-menu-text-color']['hover']) ?>;
@vr_sub_menu_active_color: <?php echo esc_attr($settings['vr-sub-menu-text-color']['active']) ?>;
@primary_toplevel_header_type_4_link_hover_bg_color: <?php echo esc_attr($settings['primary-toplevel-header-type-4-link-color']['hover']) ?>;
@primary_toplevel_header_type_4_link_active_bg_color: <?php echo esc_attr($settings['primary-toplevel-header-type-4-link-color']['active']) ?>;

// Footer
@footer_bg_color: <?php echo esc_attr($settings['footer-bg']['background-color']) ?>;
@footer_bg_repeat: <?php echo esc_attr($settings['footer-bg']['background-repeat']) ?>;
@footer_bg_size: <?php echo esc_attr($settings['footer-bg']['background-size']) ?>;
@footer_bg_attachment: <?php echo esc_attr($settings['footer-bg']['background-attachment']) ?>;
@footer_bg_position: <?php echo esc_attr($settings['footer-bg']['background-position']) ?>;
<?php
$image = str_replace(array('http://', 'https://'), array('//', '//'), esc_attr($settings['footer-bg']['background-image']));
?>
@footer_bg_image: <?php echo esc_attr($image) != 'none'?'url('.esc_url($image).')':$image ?>;

@footer_heading_color: <?php echo esc_attr($settings['footer-heading-color']) ?>;
@footer_text_color: <?php echo esc_attr($settings['footer-text-color']) ?>;
@footer_link_color: <?php echo esc_attr($settings['footer-link-color']['regular']) ?>;
@footer_hover_color: <?php echo esc_attr($settings['footer-link-color']['hover']) ?>;
@footer_copyright_bg_color: <?php echo esc_attr($settings['footer-copyright-bg-color']) ?>;
@footer_submit_button_color: <?php echo esc_attr($settings['footer-submit-button-color']) ?>;
@footer_copyright_social_link_color: <?php echo esc_attr($settings['copyright-social-link-color']) ?>;

@footer_row_middle_bg_color: <?php echo esc_attr($settings['footer-row-middle-bg-color']) ?>;
@footer_row_middle_heading_color: <?php echo esc_attr($settings['footer-row-middle-heading-color']) ?>;
@footer_row_middle_text_color: <?php echo esc_attr($settings['footer-row-middle-text-color']) ?>;
@footer_row_middle_link_color: <?php echo esc_attr($settings['footer-row-middle-link-color']['regular']) ?>;
@footer_row_middle_hover_color: <?php echo esc_attr($settings['footer-row-middle-link-color']['hover']) ?>;


