<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $content - shortcode content
 */
$el_class = $css = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
$this->resetVariables( $atts, $content );
extract( $atts );

$prepareContent = $this->getTemplateVariable( 'content' );

$class_to_filter = $this->getTtaGeneralClasses();
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class );
$css_class = $class_to_filter;

$output = '<div ' . $this->getWrapperAttributes() . '>';
	$output .= $this->getTemplateVariable( 'title' );
	$output .= '<dl ' . $this->getElementAttributes()  . ' class="' . esc_attr( $css_class ) . '">';
		$output .= $prepareContent;
	$output .= '</dl>';
$output .= '</div>';

echo $output;
