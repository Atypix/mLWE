<?php
if ( !class_exists('knowhere_vc_accordion') ) {

	class knowhere_vc_accordion {

		function __construct() {
			add_action( 'admin_print_scripts', array( $this, 'enqueue_admin_scripts' ), 999 );
			add_action( 'vc_before_init', array($this, 'add_map'), 5 );
		}

		public function enqueue_admin_scripts() {
			$screen = get_current_screen();
			$screen_id = $screen->base;

			if ( $screen_id !== 'post' )
				return false;

			wp_enqueue_script( 'knowhere-vc-accordion-admin', get_theme_file_uri('config-composer/assets/js/js_accordion_admin_enqueue.js'), array( 'jquery' ), true );
			wp_enqueue_script( 'knowhere-vc-accordion-single', get_theme_file_uri('config-composer/assets/js/js_accordion_single_element.js'), array( 'jquery' ), true );
		}

		function add_map() {

			if ( function_exists('vc_map') ) {

				vc_map( array(
					"name"    => esc_html__('Accordion', 'knowherepro') ,
					"base"    => 'vc_mad_accordion',
					'icon' => 'icon-wpb-mad-accordion',
					'is_container' => true,
					'show_settings_on_create' => false,
					'as_parent' => array( 'only' => 'vc_mad_tta_section' ),
					"category"  => esc_html__('KnowherePro', 'knowherepro'),
					"description" => esc_html__("Collapsible content panels", 'knowherepro'),
					'js_view' => 'KnowhereVcBackendTtaAccordionView',
					'params' => array(
						array(
							'type' => 'textfield',
							'heading' => esc_html__( 'Title', 'knowherepro' ),
							'param_name' => 'title',
							'description' => esc_html__( 'Enter text which will be used as title. Leave blank if no title is needed.', 'knowherepro' ),
							'edit_field_class' => 'vc_col-sm-6',
						),
						array(
							'type' => 'checkbox',
							'heading' => esc_html__( 'Use toggle element?', 'knowherepro' ),
							'param_name' => 'toggle',
							'description' => esc_html__( 'If checked, will be used toggle element.', 'knowherepro' ),
							'value' => array( esc_html__( 'Yes', 'knowherepro' ) => true ),
						),
					),
					'custom_markup' => '
						<div class="vc_tta-container" data-vc-action="collapseAll">
							<div class="vc_general vc_tta vc_tta-accordion vc_tta-color-backend-accordion-white vc_tta-style-flat vc_tta-shape-rounded vc_tta-o-shape-group vc_tta-controls-align-left vc_tta-gap-2">
							   <div class="vc_tta-panels vc_clearfix {{container-class}}">
								  {{ content }}
								  <div class="vc_tta-panel vc_tta-section-append">
									 <div class="vc_tta-panel-heading">
										<h4 class="vc_tta-panel-title vc_tta-controls-icon-position-left">
										   <a href="javascript:;" aria-expanded="false" class="vc_tta-backend-add-control">
											   <span class="vc_tta-title-text">' . esc_html__( 'Add Section', 'knowherepro' ) . '</span>
												<i class="vc_tta-controls-icon vc_tta-controls-icon-plus"></i>
											</a>
										</h4>
									 </div>
								  </div>
							   </div>
							</div>
						</div>',
					'default_content' => '[vc_mad_tta_section title="' . sprintf( '%s %d', __( 'Section', 'knowherepro' ), 1 ) . '"][/vc_mad_tta_section][vc_mad_tta_section title="' . sprintf( '%s %d', __( 'Section', 'knowherepro' ), 2 ) . '"][/vc_mad_tta_section]',
				));

				vc_map( array(
					'name' => esc_html__( 'Section', 'knowherepro' ),
					'base' => 'vc_mad_tta_section',
					'icon' => '',
					'allowed_container_element' => 'vc_row',
					'is_container' => true,
					'show_settings_on_create' => false,
					"as_child" => array( 'only' => 'vc_mad_accordion' ),
					'category' => esc_html__( 'KnowherePro', 'knowherepro' ),
					'js_view' => 'KnowhereVcBackendTtaSectionView',
					'params' => array(
						array(
							'type' => 'textfield',
							'param_name' => 'title',
							'heading' => esc_html__( 'Title', 'knowherepro' ),
							'description' => esc_html__( 'Enter section title (Note: you can leave it empty).', 'knowherepro' ),
						),
						array(
							'type' => 'el_id',
							'param_name' => 'tab_id',
							'settings' => array(
								'auto_generate' => true,
							),
							'heading' => esc_html__( 'Section ID', 'knowherepro' ),
							'description' => esc_html__( 'Enter section ID (Note: make sure it is unique and valid according to <a href="%s" target="_blank">w3c specification</a>).', 'knowherepro' ),
						),
					),
					'custom_markup' => '
						<div class="vc_tta-panel-heading">
							<h4 class="vc_tta-panel-title vc_tta-controls-icon-position-left"><a href="javascript:;" data-vc-target="[data-model-id=\'{{ model_id }}\']" data-vc-accordion data-vc-container=".vc_tta-container"><span class="vc_tta-title-text">{{ section_title }}</span><i class="vc_tta-controls-icon vc_tta-controls-icon-plus"></i></a></h4>
						</div>
						<div class="vc_tta-panel-body">
							{{ editor_controls }}
							<div class="{{ container-class }}">
							{{ content }}
							</div>
						</div>',
					'default_content' => '',
				) );

			}

		}

	}

	new knowhere_vc_accordion();

	if ( class_exists('WPBakeryShortCodesContainer') ) {

		class WPBakeryShortCode_VC_mad_accordion extends WPBakeryShortCodesContainer {
			protected $controls_css_settings = 'out-tc vc_controls-content-widget';
			protected $controls_list = array( 'add', 'edit', 'clone', 'delete' );
			protected $template_vars = array();

			public $layout = 'accordion';
			protected $content;

			public $activeClass = 'vc_active';
			/**
			 * @var WPBakeryShortCode_VC_Tta_Section
			 */
			protected $sectionClass;

			public $nonDraggableClass = 'vc-non-draggable-container';

			public function getFileName() {
				return 'vc_mad_tta_global';
			}

			public function containerContentClass() {
				return 'vc_container_for_children vc_clearfix';
			}

			/**
			 * @param $atts
			 * @param $content
			 *
			 */
			public function resetVariables( $atts, $content ) {
				$this->atts = $atts;
				$this->content = $content;
				$this->template_vars = array();
			}

			/**
			 * Override default getColumnControls to make it "simple"(blue), as single element has
			 *
			 * @param string $controls
			 * @param string $extended_css
			 *
			 * @return string
			 */
			public function getColumnControls( $controls = 'full', $extended_css = '' ) {
				// we don't need containers bottom-controls for tabs
				if ( 'bottom-controls' === $extended_css ) {
					return '';
				}
				$column_controls = $this->getColumnControlsModular();

				return $output = $column_controls;
			}

			public function getTtaContainerClasses() {
				$classes = array();
				$classes[] = 'wpb_content_element';

				return implode( ' ', apply_filters( 'knowhere_vc_tta_mad_container_classes', array_filter( $classes ), $this->getAtts() ) );
			}

			public function getTtaGeneralClasses() {
				$classes = array();
				$classes[] = 'kw-accordion';
				$classes[] = $this->getTemplateVariable( 'style' );

				return implode( ' ', apply_filters( 'knowhere_vc_tta_mad_accordion_general_classes', array_filter( $classes ), $this->getAtts() ) );
			}

			public function getElementAttributes() {
				$attributes = array();
				$attributes[] = 'data-toggle="' . ( 1 == $this->atts['toggle'] ? 1 : 0 ) . '"';

				return implode( ' ', $attributes );
			}

			public function getWrapperAttributes() {
				$attributes = array();
				$attributes[] = 'class="' . esc_attr( $this->getTtaContainerClasses() ) . '"';

				return implode( ' ', $attributes );
			}

			public function getTemplateVariable( $string ) {
				if ( isset( $this->template_vars[ $string ] ) ) {
					return $this->template_vars[ $string ];
				} elseif ( method_exists( $this, 'getParam' . vc_studly( $string ) ) ) {
					$this->template_vars[ $string ] = $this->{'getParam' . vc_studly( $string )}( $this->atts, $this->content );

					return $this->template_vars[ $string ];
				}

				return '';
			}

			/**
			 * @param $atts
			 * @param $content
			 *
			 * @return string|null
			 */
			public function getParamStyle( $atts, $content ) {
				if ( isset( $atts['style'] ) && strlen( $atts['style'] ) > 0 ) {
					return $atts['style'];
				}

				return null;
			}

			/**
			 * @param $atts
			 * @param $content
			 *
			 * @return string|null
			 */
			public function getParamTitle( $atts, $content ) {
				if ( empty($atts['title']) ) return;

				return '<h2>'. esc_html($atts['title']) .'</h2>';
			}

			/**
			 * @param $atts
			 * @param $content
			 *
			 * @return string|null
			 */
			public function getParamContent( $atts, $content ) {
				$panelsContent = wpb_js_remove_wpautop( $content );
				if ( isset( $atts['c_icon'] ) && strlen( $atts['c_icon'] ) > 0 ) {
					$isPageEditable = vc_is_page_editable();
					if ( ! $isPageEditable ) {
						$panelsContent = str_replace( '{{{ control-icon }}}',
							'<i class="vc_tta-controls-icon vc_tta-controls-icon-' . $atts['c_icon'] . '"></i>',
							$panelsContent
						);
					} else {
						$panelsContent = str_replace( '{{{ control-icon }}}',
							'<i class="vc_tta-controls-icon" data-vc-tta-controls-icon="' . $atts['c_icon'] . '"></i>',
							$panelsContent
						);
					}
				} else {
					$panelsContent = str_replace( '{{{ control-icon }}}',
						'',
						$panelsContent );
				}

				return $panelsContent;
			}

			/**
			 * @param $atts
			 * @param $content
			 *
			 * @return string|null
			 */
			public function getParamShape( $atts, $content ) {
				if ( isset( $atts['shape'] ) && strlen( $atts['shape'] ) > 0 ) {
					return 'vc_tta-shape-' . $atts['shape'];
				}

				return null;
			}

			/**
			 * @param $atts
			 * @param $content
			 *
			 * @return string
			 */
			public function getParamSpacing( $atts, $content ) {
				if ( isset( $atts['spacing'] ) && strlen( $atts['spacing'] ) > 0 ) {
					return 'vc_tta-spacing-' . $atts['spacing'];
				}

				// In case if no spacing set we need to append extra class
				return 'vc_tta-o-shape-group';
			}

			/**
			 * @param $atts
			 * @param $content
			 *
			 * @return string|null
			 */
			public function getParamGap( $atts, $content ) {
				if ( isset( $atts['gap'] ) && strlen( $atts['gap'] ) > 0 ) {
					return 'vc_tta-gap-' . $atts['gap'];
				}

				return null;
			}

			/**
			 * @param $atts
			 * @param $content
			 *
			 * @return string|null
			 */
			public function getParamNoFill( $atts, $content ) {
				if ( isset( $atts['no_fill'] ) && 'true' === $atts['no_fill'] ) {
					return 'vc_tta-o-no-fill';
				}

				return null;
			}

			/**
			 * @param $atts
			 * @param $content
			 *
			 * @return string|null
			 */
			public function getParamCAlign( $atts, $content ) {
				if ( isset( $atts['c_align'] ) && strlen( $atts['c_align'] ) > 0 ) {
					return 'vc_tta-controls-align-' . $atts['c_align'];
				}

				return null;
			}

			/**
			 * Accordion doesn't have pagination
			 *
			 * @param $atts
			 * @param $content
			 *
			 * @return null
			 */
			public function getParamPaginationTop( $atts, $content ) {
				return null;
			}

			/**
			 * Accordion doesn't have pagination
			 *
			 * @param $atts
			 * @param $content
			 *
			 * @return null
			 */
			public function getParamPaginationBottom( $atts, $content ) {
				return null;
			}

			/**
			 * Get currently active section (from $atts)
			 *
			 * @param $atts
			 * @param bool $strict_bounds If true, check for min/max bounds
			 *
			 * @return int nth position (one-based) of active section
			 */
			function getActiveSection( $atts, $strict_bounds = false ) {
				$active_section = intval( $atts['active_section'] );

				if ( $strict_bounds ) {
					VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_VC_Tta_Section' );
					if ( $active_section < 1 ) {
						$active_section = 1;
					} elseif ( $active_section > WPBakeryShortCode_VC_Tta_Section::$self_count ) {
						$active_section = WPBakeryShortCode_VC_Tta_Section::$self_count;
					}
				}

				return $active_section;
			}

			/**
			 * Override default outputTitle (also Icon). To remove anything, also Icon.
			 *
			 * @param $title - just for strict standards
			 *
			 * @return string
			 */
			protected function outputTitle( $title ) {
				return '';
			}
			/**
			 * Check is allowed to add another element inside current element.
			 *
			 * @since 4.8
			 *
			 * @return bool
			 */
			public function getAddAllowed() {
				return vc_user_access_check_shortcode_all( 'vc_mad_tta_section' );
			}
		}

	}

	VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_VC_Tta_Accordion' );

	class WPBakeryShortCode_VC_mad_tta_section extends WPBakeryShortCode_VC_Tta_Accordion {
		protected $controls_css_settings = 'tc vc_control-container';
		protected $controls_list = array( 'add', 'edit', 'clone', 'delete' );
		protected $backened_editor_prepend_controls = false;
		/**
		 * @var WPBakeryShortCode_VC_Tta_Accordion
		 */
		public static $tta_base_shortcode;
		public static $self_count = 0;
		public static $section_info = array();

		public function getFileName() {
			if ( isset( self::$tta_base_shortcode ) && 'vc_tta_pageable' === self::$tta_base_shortcode->getShortcode() ) {
				return 'vc_tta_pageable_section';
			} else {
				return 'vc_mad_tta_section';
			}
		}

		public function containerContentClass() {
			return 'wpb_column_container vc_container_for_children vc_clearfix';
		}

		public function getElementClasses() {
			$classes = array();
			$classes[] = 'vc_tta-panel';
			$isActive = ! vc_is_page_editable() && $this->getTemplateVariable( 'section-is-active' );

			if ( $isActive ) {
				$classes[] = $this->activeClass;
			}

			return implode( ' ', array_filter( $classes ) );
		}

		/**
		 * @param $atts
		 * @param $content
		 *
		 * @return string
		 */
		public function getParamContent( $atts, $content ) {
			return wpb_js_remove_wpautop( $content );
		}

		/**
		 * @param $atts
		 * @param $content
		 *
		 * @return string|null
		 */
		public function getParamTabId( $atts, $content ) {
			if ( isset( $atts['tab_id'] ) && strlen( $atts['tab_id'] ) > 0 ) {
				return $atts['tab_id'];
			}

			return null;
		}

		/**
		 * @param $atts
		 * @param $content
		 *
		 * @return string|null
		 */
		public function getParamTitle( $atts, $content ) {
			if ( isset( $atts['title'] ) && strlen( $atts['title'] ) > 0 ) {
				return $atts['title'];
			}

			return null;
		}

		/**
		 * @param $atts
		 * @param $content
		 *
		 * @return string|null
		 */
		public function getParamIcon( $atts, $content ) {
			if ( ! empty( $atts['add_icon'] ) && 'true' === $atts['add_icon'] ) {
				$iconClass = '';
				if ( isset( $atts[ 'i_icon_' . $atts['i_type'] ] ) ) {
					$iconClass = $atts[ 'i_icon_' . $atts['i_type'] ];
				}
				vc_icon_element_fonts_enqueue( $atts['i_type'] );

				return '<i class="vc_tta-icon ' . esc_attr( $iconClass ) . '"></i>';
			}

			return null;
		}

		/**
		 * @param $atts
		 * @param $content
		 *
		 * @return string|null
		 */
		public function getParamIconLeft( $atts, $content ) {
			if ( 'left' === $atts['i_position'] ) {
				return $this->getParamIcon( $atts, $content );
			}

			return null;
		}

		/**
		 * @param $atts
		 * @param $content
		 *
		 * @return string|null
		 */
		public function getParamIconRight( $atts, $content ) {
			if ( 'right' === $atts['i_position'] ) {
				return $this->getParamIcon( $atts, $content );
			}

			return null;
		}

		/**
		 * Section param active
		 */
		public function getParamSectionIsActive( $atts, $content ) {
			if ( is_object( self::$tta_base_shortcode ) ) {
				if ( isset( self::$tta_base_shortcode->atts['active_section'] ) && strlen( self::$tta_base_shortcode->atts['active_section'] ) > 0 ) {
					$active = (int) self::$tta_base_shortcode->atts['active_section'];
					if ( $active === self::$self_count ) {
						return true;
					}
				}
			}

			return null;
		}

		public function getParamControlIconPosition( $atts, $content ) {
			if ( is_object( self::$tta_base_shortcode ) ) {
				if (
					isset( self::$tta_base_shortcode->atts['c_icon'] ) && strlen( self::$tta_base_shortcode->atts['c_icon'] ) > 0 &&
					isset( self::$tta_base_shortcode->atts['c_position'] ) && strlen( self::$tta_base_shortcode->atts['c_position'] ) > 0
				) {
					$c_position = self::$tta_base_shortcode->atts['c_position'];

					return 'vc_tta-controls-icon-position-' . $c_position;
				}
			}

			return null;
		}

		public function getParamControlIcon( $atts, $content ) {
			if ( is_object( self::$tta_base_shortcode ) ) {
				if ( isset( self::$tta_base_shortcode->atts['c_icon'] ) && strlen( self::$tta_base_shortcode->atts['c_icon'] ) > 0 ) {
					$c_icon = self::$tta_base_shortcode->atts['c_icon'];

					return '<i class="vc_tta-controls-icon vc_tta-controls-icon-' . $c_icon . '"></i>';
				}
			}

			return null;
		}

		public function getParamHeading( $atts, $content ) {
			$isPageEditable = vc_is_page_editable();

			$h4attributes = array();
			$h4classes = array(
				'vc_tta-panel-title',
			);
			if ( $isPageEditable ) {
				$h4attributes[] = 'data-vc-tta-controls-icon-position=""';
			} else {
				$controlIconPosition = $this->getTemplateVariable( 'control-icon-position' );
				if ( $controlIconPosition ) {
					$h4classes[] = $controlIconPosition;
				}
			}
			$h4attributes[] = 'class="' . implode( ' ', $h4classes ) . '"';

			$output = '<h4 ' . implode( ' ', $h4attributes ) . '>'; // close h4

			if ( $isPageEditable ) {
				$output .= '<a href="javascript:;" data-vc-target=""';
				$output .= ' data-vc-tta-controls-icon-wrapper';
				$output .= ' data-vc-use-cache="false"';
			} else {
				$output .= '<a href="#' . esc_attr( $this->getTemplateVariable( 'tab_id' ) ) . '"';
			}

			$output .= ' data-vc-accordion';

			$output .= ' data-vc-container=".vc_tta-container">';
			$output .= $this->getTemplateVariable( 'icon-left' );
			$output .= '<span class="vc_tta-title-text">'
				. $this->getTemplateVariable( 'title' )
				. '</span>';
			$output .= $this->getTemplateVariable( 'icon-right' );
			if ( ! $isPageEditable ) {
				$output .= $this->getTemplateVariable( 'control-icon' );
			}

			$output .= '</a>';
			$output .= '</h4>'; // close h4 fix #2229

			return $output;
		}

		/**
		 * Get basic heading
		 *
		 * These are used in Pageable element inside content and are hidden from view
		 *
		 * @param $atts
		 * @param $content
		 *
		 * @return string
		 */
		public function getParamBasicHeading( $atts, $content ) {
			$isPageEditable = vc_is_page_editable();

			if ( $isPageEditable ) {
				$attributes = array(
					'href' => 'javascript:;',
					'data-vc-container' => '.vc_tta-container',
					'data-vc-accordion' => '',
					'data-vc-target' => '',
					'data-vc-tta-controls-icon-wrapper' => '',
					'data-vc-use-cache' => 'false',
				);
			} else {
				$attributes = array(
					'data-vc-container' => '.vc_tta-container',
					'data-vc-accordion' => '',
					'data-vc-target' => esc_attr( '#' . $this->getTemplateVariable( 'tab_id' ) ),
				);
			}

			$output = '
			<span class="vc_tta-panel-title">
				<a ' . vc_convert_atts_to_string( $attributes ) . '></a>
			</span>
		';

			return $output;
		}
		/**
		 * Check is allowed to add another element inside current element.
		 *
		 * @since 4.8
		 *
		 * @return bool
		 */
		public function getAddAllowed() {
			return  vc_user_access()
				->part( 'shortcodes' )
				->checkStateAny( true, 'custom', null )->get();
		}
	}

}