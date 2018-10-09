<?php
if ( !class_exists('knowhere_pricing_box') ) {

	class knowhere_pricing_box {

		function __construct() {
			add_action('vc_before_init', array($this, 'add_map_pricing_box'));
		}
		
		function add_map_pricing_box() {

			if ( function_exists('vc_map') ) {

				vc_map(
					array(
					   "name" => esc_html__("Pricing Box", 'knowherepro' ),
					   "base" => "vc_mad_pricing_box",
					   "class" => "vc_mad_pricing_box",
					   "icon" => "icon-wpb-mad-pricing-box",
					   "category"  => esc_html__('KnowherePro', 'knowherepro'),
					   "description" => esc_html__('Styled pricing tables', 'knowherepro'),
					   "as_parent" => array('only' => 'vc_mad_pricing_box_item'),
					   "content_element" => true,
					   "show_settings_on_create" => true,
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
							   'heading' => esc_html__( 'Spacing between items', 'knowherepro' ),
							   'param_name' => 'spacing',
							   'value' => array(
								   esc_html__( 'With Spacing', 'knowherepro' ) => 'kw-with-spacing',
								   esc_html__( 'Without Spacing', 'knowherepro' ) => 'kw-without-spacing'
							   ),
							   'description' => esc_html__( 'Select spacing mode', 'knowherepro' )
						   ),
						   array(
							   'type' => 'dropdown',
							   'heading' => esc_html__( 'Columns', 'knowherepro' ),
							   'param_name' => 'columns',
							   'value' => array(
								   esc_html__( '3 Columns', 'knowherepro' ) => 3,
								   esc_html__( '4 Columns', 'knowherepro' ) => 4,
							   ),
							   'std' => 3,
							   'description' => esc_html__( 'How many columns should be displayed?', 'knowherepro' )
						   )
						),
						"js_view" => 'VcColumnView'
					));

				vc_map(
					array(
					   "name" => esc_html__("Pricing Box Item", 'knowherepro'),
					   "base" => "vc_mad_pricing_box_item",
					   "class" => "vc_mad_pricing_box_item",
					   "icon" => "icon-wpb-mad-pricing-box",
					   "category" => esc_html__('Pricing Box', 'knowherepro'),
					   "content_element" => true,
					   "as_child" => array('only' => 'vc_mad_pricing_box'),
					   "is_container" => false,
					   "params" => array(
						   array(
							   "type" => "textfield",
							   "heading" => esc_html__( 'Package Name / Title', 'knowherepro' ),
							   "param_name" => "title",
							   "holder" => "h4",
							   "description" => esc_html__( 'Enter the package name or table heading.', 'knowherepro' ),
							   "value" => '',
						   ),
						   array(
							   "type" => "textfield",
							   "heading" => esc_html__( 'Package Price', 'knowherepro' ),
							   "param_name" => "price",
							   "holder" => "span",
							   "description" => esc_html__( 'Enter the price for this package', 'knowherepro' ),
							   "value" => ''
						   ),
						   array(
							   "type" => "textfield",
							   "heading" => esc_html__( 'Price Unit', 'knowherepro' ),
							   "param_name" => "time",
							   "holder" => "span",
							   "description" => esc_html__( 'Enter the price unit for this package. e.g. per month', 'knowherepro' ),
							   "value" => esc_html__( 'per month', 'knowherepro' )
						   ),
						   array(
							   "type" => "textarea",
							   "heading" => esc_html__( 'Features', 'knowherepro' ),
							   "param_name" => "features",
							   "holder" => "span",
							   "description" => esc_html__( 'Create the features list using un-ordered list elements. Divide values with linebreaks (Enter). Example: Up to 50 users|Limited team members', 'knowherepro' ),
							   "value" => esc_html__('1 user | No VPN access | 2 Gb allowed', 'knowherepro')
						   ),
						   array(
							   "type" => "vc_link",
							   "heading" => esc_html__( 'Add URL to the whole box (optional)', 'knowherepro' ),
							   "param_name" => "link",
						   ),
						   array(
							   'type' => 'checkbox',
							   'heading' => esc_html__( 'Featured', 'knowherepro' ),
							   'param_name' => 'add_label',
							   'description' => esc_html__( 'Adds a nice label to your pricing box.', 'knowherepro' ),
							   'value' => array( esc_html__( 'Yes, please', 'knowherepro' ) => true )
						   ),
					    )
					)
				);

			}
		}

	}

	if ( class_exists('WPBakeryShortCodesContainer') ) {

		class WPBakeryShortCode_vc_mad_pricing_box extends WPBakeryShortCodesContainer {

			protected function content($atts, $content = null) {

				$tag_title = $description = $title_color = $description_color = $layout = $spacing = $columns = '';

				extract(shortcode_atts(array(
					'title' => '',
					'subtitle' => '',
					'title_color' => '',
					'subtitle_color' => '',
					'align_title' => '',
					'spacing' => 'kw-with-spacing',
					'columns' => 3
				), $atts) );

				$title = !empty($atts['title']) ? $atts['title'] : '';
				$subtitle = !empty($atts['subtitle']) ? $atts['subtitle'] : '';
				$title_color = !empty($atts['title_color']) ? $atts['title_color'] : '';
				$subtitle_color = !empty($atts['subtitle_color']) ? $atts['subtitle_color'] : '';
				$align_title = !empty($atts['align_title']) ? $atts['align_title'] : '';

				$css_class = array(
					'kw-pricing-tables', $spacing, 'kw-cols-' . absint($columns)
				);

				ob_start(); ?>

				<div class="wpb_content_element">

					<?php
					echo Knowhere_Vc_Config::getParamTitle(
						array(
							'title' => $title,
							'subtitle' => $subtitle,
							'title_color' => $title_color,
							'subtitle_color' => $subtitle_color,
							'align_title' => $align_title
						)
					);
					?>

					<div class="<?php echo esc_attr( implode(' ', $css_class) ); ?>">
						<?php echo wpb_js_remove_wpautop( $content, false ) ?>
					</div>

				</div>

				<?php return ob_get_clean() ;
			}

		}

		class WPBakeryShortCode_vc_mad_pricing_box_item extends WPBakeryShortCode {

			protected function content($atts, $content = null) {
				$title = $price = $time = $features = $add_label = $link = "";

				extract( shortcode_atts(array(
					'title' => esc_html__('Free', 'knowherepro'),
					'price' => '',
					'time' => esc_html__('per month', 'knowherepro'),
					'features' => '',
					'link' => '',
					'add_label' => false,
				),$atts) );

				$link = ($link == '||') ? '' : $link;
				$link = vc_build_link($link);
				$a_href = $link['url'];
				$a_title = $link['title'];
				( $link['target'] != '' ) ? $a_target = $link['target'] : $a_target = '_self';

				$wrapper_attributes = array();
				$css_classes = array( 'kw-pricing-plan-container' );

				$css_class = preg_replace( '/\s+/', ' ', implode( ' ', array_filter( $css_classes ) ) );
				$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';

				ob_start(); ?>

				<div <?php echo implode( ' ', $wrapper_attributes ) ?>>

					<section class="kw-pricing-plan <?php if ( $add_label ): ?>kw-active<?php endif; ?>">

						<header class="kw-pp-header">
							<h5 class="kw-pp-type"><?php echo esc_html($title); ?></h5>
							<div class="kw-pp-price">
								<?php echo esc_html($price); ?>
								<div class="kw-pp-lifetime"><?php echo esc_html($time) ?></div>
							</div>
						</header>

						<ul class="kw-pp-features-list">
							<?php
							$features = explode( '|', wp_strip_all_tags($features) );
							$feature_list = '';
							if ( is_array($features) ) {
								foreach ( $features as $feature ) {
									$feature_list .= "<li>{$feature}</li>";
								}
							}
							?>
							<?php echo wp_kses( $feature_list, array(
								'a' => array(
									'href' => true,
									'title' => true,
								),
								'li' => array()
							)); ?>
						</ul>

						<?php if ( !empty($a_title) ): ?>
							<footer class="kw-pp-footer">
								<a href="<?php echo esc_url($a_href); ?>" title="<?php echo esc_attr($a_title) ?>" target="<?php echo esc_attr($a_target) ?>" class="kw-btn-medium kw-theme-color"><?php echo esc_html($a_title); ?></a>
							</footer>
						<?php endif; ?>

					</section>

				</div>

				<?php return ob_get_clean() ;
			}

		}
	}

	new knowhere_pricing_box();

}