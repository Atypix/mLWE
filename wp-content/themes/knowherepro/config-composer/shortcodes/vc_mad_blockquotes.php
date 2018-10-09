<?php

class WPBakeryShortCode_VC_mad_blockquotes extends WPBakeryShortCode {

	public $atts = array();

	protected function content($atts, $content = null) {

		$this->atts = shortcode_atts(array(
			'image' => ''
		), $atts, 'vc_mad_blockquotes');

		$wrapper_attributes = array();
		extract( $this->atts );

		$image_id = preg_replace( '/[^\d]/', '', $image );
		$image_src = wp_get_attachment_image_src( $image_id, 'full' );
		if ( ! empty( $image_src[0] ) ) {
			$image_src = $image_src[0];
		}

		$css_classes = array();

		if ( !empty($image_src) ) {
			$css_classes[] = 'kw-blockquote-bg';
		}

		$css_class = preg_replace( '/\s+/', ' ', implode( ' ', array_filter( $css_classes ) ) );
		$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';

		ob_start(); ?>

		<blockquote <?php echo implode( ' ', $wrapper_attributes ) ?>>
			<?php
			if ( ! empty( $image_src ) ) {
				echo '<div data-bg="'. esc_attr($image_src) .'"></div>';
			}
			?>

			<?php if ( !empty($content) ): ?>
				<?php echo wpb_js_remove_wpautop( $content, false ) ?>
			<?php endif; ?>
		</blockquote>

		<?php return ob_get_clean();
	}

}