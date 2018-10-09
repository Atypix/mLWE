<?php
if (!class_exists('knowhere_info_block')) {

	class knowhere_info_block {

		function __construct() {
			add_action('vc_before_init', array($this, 'add_map'));
		}

		function add_map() {

			if ( function_exists('vc_map') ) {

				vc_map(
					array(
						"name" => esc_html__("Infoblock", 'knowherepro' ),
						"base" => "vc_mad_info_block",
						"class" => "vc_mad_info_block",
						"icon" => "icon-wpb-mad-info-block",
						"category"  => esc_html__('KnowherePro', 'knowherepro'),
						"description" => esc_html__('Styled info blocks', 'knowherepro'),
						"as_parent" => array('only' => 'vc_mad_info_block_item'),
						"content_element" => true,
						"show_settings_on_create" => false,
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
								"type" => "dropdown",
								"heading" => esc_html__( 'Select type', 'knowherepro' ),
								"param_name" => "type",
								"value" => array(
									esc_html__('Type 1', 'knowherepro') => 'kw-type-1',
									esc_html__('Type 2', 'knowherepro') => 'kw-type-2',
								),
								"std" => 'kw-type-1',
								"description" => esc_html__( 'Choose type for this info block.', 'knowherepro' )
							),
							array(
								"type" => "dropdown",
								"heading" => esc_html__( 'Layout', 'knowherepro' ),
								"param_name" => "layout",
								"value" => array(
									esc_html__('Grid', 'knowherepro') => 'kw-grid',
									esc_html__('List', 'knowherepro') => 'kw-list',
								),
								"std" => 'kw-grid',
								"description" => esc_html__( 'Choose type layout grid or list for this info block.', 'knowherepro' )
							),
							array(
								'type' => 'dropdown',
								'heading' => esc_html__( 'Box Alignment', 'knowherepro' ),
								'param_name' => 'box_align',
								'description' => esc_html__( 'Select box alignment.', 'knowherepro' ),
								'value' => array(
									esc_html__( 'Center', 'knowherepro' ) => '',
									esc_html__( 'Left', 'knowherepro' ) => 'align-left',
									esc_html__( 'Right', 'knowherepro' ) => 'align-right',
								),
							),
													),
						"js_view" => 'VcColumnView'
					));

				vc_map(
					array(
						"name" => esc_html__("Info Block Item", 'knowherepro'),
						"base" => "vc_mad_info_block_item",
						"class" => "vc_mad_info_block_item",
						"icon" => "icon-wpb-mad-info-block",
						"category" => esc_html__('Infoblock', 'knowherepro'),
						"content_element" => true,
						"as_child" => array('only' => 'vc_mad_info_block'),
						"is_container" => true,
						"params" => array(
							array(
								"type" => "textfield",
								"heading" => esc_html__( 'Title', 'knowherepro' ),
								"param_name" => "title",
								"holder" => "h4",
								"description" => ''
							),
							array(
								'type' => 'attach_image',
								'heading' => esc_html__('Image', 'knowherepro'),
								'param_name' => 'image',
								'value' => '',
								'description' => esc_html__('Select image from media library.', 'knowherepro')
							),
							array(
								"type" => "choose_icons",
								"heading" => esc_html__("Icon", 'knowherepro'),
								"param_name" => "icon",
								"value" => 'none',
								"description" => esc_html__( 'Select icon from library.', 'knowherepro')
							),
							array(
								'type' => 'textarea_html',
								'holder' => 'div',
								'heading' => esc_html__( 'Text', 'knowherepro' ),
								'param_name' => 'content',
								'value' => wp_kses(__( '<p>Click edit button to change this text.</p>', 'knowherepro' ), array('p' => array()) )
							),
							array(
								'type' => 'vc_link',
								'heading' => esc_html__( 'URL (Link)', 'knowherepro' ),
								'param_name' => 'link',
								'description' => esc_html__( 'Add link to info block.', 'knowherepro' ),
							),
						)
					)
				);

			}
		}

	}

	if (class_exists('WPBakeryShortCodesContainer')) {

		class WPBakeryShortCode_vc_mad_info_block extends WPBakeryShortCodesContainer {

			protected function content($atts, $content = null) {

				$type = $layout = '';

				extract( shortcode_atts( array(
					'title' => '',
					'subtitle' => '',
					'title_color' => '',
					'subtitle_color' => '',
					'align_title' => '',
					'type' => 'kw-type-1',
					'layout' => 'kw-grid',
					'box_align' => ''
				), $atts ) );

				$title = !empty($atts['title']) ? $atts['title'] : '';
				$subtitle = !empty($atts['subtitle']) ? $atts['subtitle'] : '';
				$title_color = !empty($atts['title_color']) ? $atts['title_color'] : '';
				$subtitle_color = !empty($atts['subtitle_color']) ? $atts['subtitle_color'] : '';
				$align_title = !empty($atts['align_title']) ? $atts['align_title'] : '';
				$box_align = !empty($atts['box_align']) ? $atts['box_align'] : '';

				$css_class = array( 'kw-iconbox', $type, $layout, $box_align );

				global $vc_mad_info_block_args;

				$vc_mad_info_block_args[] = array (
					'content' => $content
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
						<?php echo wpb_js_remove_wpautop ($content, false ) ?>
					</div><!--/ .kw-iconbox-->

				</div>

				<?php return ob_get_clean() ;

			}

		}

		class WPBakeryShortCode_vc_mad_info_block_item extends WPBakeryShortCode {

			protected function content($atts, $content = null) {

				$wrapper_attributes = array();
				$title = $style = $icon = $image = $link = '';

				extract(shortcode_atts(array(
					'title' => '',
					'image' => '',
					'icon' => '',
					'link' => ''
				),$atts));

				$css_classes = array(
					'kw-infoblock-item'
				);

				$link = ($link == '||') ? '' : $link;
				$link = vc_build_link($link);
				$a_href = $link['url'];
				$a_title = $link['title'];
				($link['target'] != '') ? $a_target = $link['target'] : $a_target = '_self';

//				global $vc_mad_info_block_args;
//
//				if ( isset($vc_mad_info_block_args) && is_array($vc_mad_info_block_args) ) {
//					foreach ( $vc_mad_info_block_args as $info_block ) {
//						if ( strpos( $info_block['content'], $content ) == true ) {
//							if ( isset($info_block['type']) && !empty($info_block['type']) ) {
//								$type = $info_block['type'];
//							}
//						}
//					}
//				}

				if ( $content == null )
					$content = ' ';

				$css_class = preg_replace( '/\s+/', ' ', implode( ' ', array_filter( $css_classes ) ) );
				$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';

				ob_start(); ?>

				<div <?php echo implode( ' ', $wrapper_attributes ) ?>>

					<div class="kw-icon-boxes">

						<?php if ( $image != '' || $icon != '' ): ?>

							<?php if ( $image != '' ): ?>

								<div class="kw-icon-wrap">
									<?php
									if ( $image && absint($image) ) {
										echo wp_get_attachment_image( $image, '' );
									}
									?>
								</div>

							<?php elseif ( $icon != '' ): ?>

								<div class="kw-icon-wrap">
									<span class="lnr <?php echo esc_attr($icon) ?>"></span>
								</div>

							<?php endif; ?>

						<?php endif; ?>

						<div class="kw-icon-text-wrap">

							<?php if ( !empty($title) ): ?>
								<h3><?php echo esc_html($title); ?></h3>
							<?php endif; ?>

							<?php if ( !empty($content) ): ?>
								<?php echo wpb_js_remove_wpautop( $content, true ) ?>
							<?php endif; ?>

							<?php if ( !empty($a_href) ): ?>
								<a class="kw-btn kw-theme-color kw-small" title="<?php echo esc_attr($a_title) ?>" target="<?php echo esc_attr($a_target) ?>" href="<?php echo esc_url($a_href) ?>"><?php echo esc_html($a_title) ?></a>
							<?php endif; ?>

						</div><!--/ .kw-icon-text-wrap-->

					</div><!--/ .kw-icon-boxes-->

				</div><!--/ .kw-infoblock-item-->

				<?php return ob_get_clean();
			}

		}

	}

	new knowhere_info_block();
}