<?php

class WPBakeryShortCode_VC_mad_btn extends WPBakeryShortCode {

	public $atts = array();

	protected function content($atts, $content = null) {

		$this->atts = shortcode_atts(array(
			'title' => '',
			'link' => '',
			'text_color' => '',
			'style' => 'kw-gray',
			'size' => 'kw-small',
			'icon' => '',
			'align' => 'kw-inline'
		), $atts, 'vc_mad_btn');

		return $this->html();
	}

	public function html() {

		$title = $link = $size = $style = $icon = $align = $href = $style_text = '';
		$atts = $this->atts;
		$attributes = array();

		extract($atts);

		$text_color = !empty($atts['text_color']) ? $atts['text_color'] : '';

		if ( !empty($text_color) ) {
			$style_text = 'style="' . vc_get_css_color( 'color', $text_color ) . '"';
		}

		$link = ( '||' === $link ) ? '' : $link;
		$link = vc_build_link( $link );
		$use_link = false;
		if ( strlen( $link['url'] ) > 0 ) {
			$use_link = true;
			$a_href = $link['url'];
			$a_title = $link['title'];
			$a_target = $link['target'];
			$a_rel = $link['rel'];
		}

		$css_classes = array(
			'kw-button-container', $align
		);

		$button_classes = array(
			'kw-btn', $style, $size
		);

		if ( $button_classes ) {
			$button_classes = esc_attr( implode( ' ', array_filter( $button_classes ) ) );
			$attributes[] = 'class="' . trim( $button_classes ) . '"';
		}

		$button_text = $title;

		$wrapper_attributes = array();
		$css_class = preg_replace( '/\s+/', ' ', implode( ' ', array_filter( $css_classes ) ) );
		$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';

		if ( $use_link ) {
			$attributes[] = 'href="' . trim( $a_href ) . '"';
			$attributes[] = 'title="' . esc_attr( trim( $a_title ) ) . '"';
			if ( ! empty( $a_target ) ) {
				$attributes[] = 'target="' . esc_attr( trim( $a_target ) ) . '"';
			}
			if ( ! empty( $a_rel ) ) {
				$attributes[] = 'rel="' . esc_attr( trim( $a_rel ) ) . '"';
			}
		}

		$icon = trim($icon);

		if ( $icon !== '' ) {
			$icon = '<i class="lnr ' . $icon . '"></i>';
		}

		$attributes = implode( ' ', $attributes );

		ob_start(); ?>

		<div <?php echo implode( ' ', $wrapper_attributes ) ?>>
			<?php if ( $use_link ) {
				echo '<a '. $style_text . ' ' . $attributes . '>' . $icon . $button_text . '</a>';
			} else {
				echo '<button '. $style_text . ' ' . $attributes . '>' . $icon . $button_text . '</button>';
			} ?>
		</div>

		<?php return ob_get_clean();
	}

}