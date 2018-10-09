<?php

class WPBakeryShortCode_VC_mad_cta extends WPBakeryShortCode {

	protected $template_vars = array();

	public function buildTemplate( $atts, $content ) {
		$output = array();

		$container_classes = array();

		if ( !empty( $atts['add'] ) ) {

			switch( $atts['add'] ) {
				case 'button':
					$output[ 'kw-actions-button' ] = $this->getOneButton( $atts );
					$column_left_clases[] = 'col-sm-9';
					$columns_right_classes[] = 'col-sm-3';
					break;
			}

		}

		$output['heading'] = $this->getHeading( 'h2', $atts );
		$output['subheading'] = $this->getHeading( 'h5', $atts );

		$output['container-class'] = $container_classes;
		$output['column-left-class'] = $column_left_clases;
		$output['column-right-class'] = $columns_right_classes;
		$this->template_vars = $output;
	}

	public function getHeading( $tag, $atts ) {
		$inline_css = $styling = '';
		if ( isset( $atts[ $tag ] ) && '' !== trim( $atts[ $tag ] ) ) {

			if ( !empty($atts['heading_text_color']) ) {
				$styling = 'style="' . vc_get_css_color( 'color', $atts['heading_text_color'] ) . '"';
			}

			if ( $tag == 'h2' ) {
				$inline_css = 'class="kw-super-bold"';
			}

			return '<' . $tag . ' ' . $inline_css . ' ' . $styling . '>' . $atts[$tag] . '</' . $tag . '>';
		}

		return '';
	}

	public function getOneButton( $atts ) {
		$output = '';

		$output .= '<div class="kw-right-edge">';
			$output .= $this->getButton($atts);
		$output .= '</div>';

		return $output;
	}

	public function getButton( $atts ) {
		$link = $atts['link'];

		if ( empty($link) ) return '';

		$url = vc_build_link( $link );
		$buttonClasses = 'kw-btn-big kw-yellow';

		if ( strlen( $link ) > 0 && strlen( $url['url'] ) > 0 ) {
			return '<a class="' . esc_attr($buttonClasses) .'"
			href="' . esc_attr( $url['url'] ) . '"
			target="' . ( strlen( $url['target'] ) > 0 ? esc_attr( $url['target'] ) : '_self' ) . '">' . esc_html( $url['title'] ) . '</a>';
		}

		return '';
	}

	public function getTemplateVariable( $string ) {
		if ( is_array( $this->template_vars ) && isset( $this->template_vars[ $string ] ) ) {
			return $this->template_vars[ $string ];
		}
		return '';
	}

}