<?php

class WPBakeryShortCode_VC_mad_partners extends WPBakeryShortCode {

	public $atts = array();

	protected function content($atts, $content = null) {

		$this->atts = shortcode_atts(array(
			'title' => '',
			'subtitle' => '',
			'title_color' => '',
			'subtitle_color' => '',
			'align_title' => '',
			'images' => "",
			'links' => "",
		), $atts, 'vc_mad_partners');

		return $this->html();
	}

	public function html() {

		$wrapper_attributes = array();
		$images = $links = '';

		$atts = $this->atts;

		$title = !empty($atts['title']) ? $atts['title'] : '';
		$subtitle = !empty($atts['subtitle']) ? $atts['subtitle'] : '';
		$title_color = !empty($atts['title_color']) ? $atts['title_color'] : '';
		$subtitle_color = !empty($atts['subtitle_color']) ? $atts['subtitle_color'] : '';
		$align_title = !empty($atts['align_title']) ? $atts['align_title'] : '';

		extract($this->atts);

		$links = !empty($links) ? explode('|', $links) : '';
		$images = explode( ',', $images);

		$css_classes = array( 'kw-partners-carousel', 'owl-carousel', 'owl-left-aligned-dots' );

		$css_class = preg_replace( '/\s+/', ' ', implode( ' ', array_filter( $css_classes ) ) );
		$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';

		ob_start(); ?>

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

		<div <?php echo implode( ' ', $wrapper_attributes ) ?>>

			<!-- - - - - - - - - - - - - - Item- - - - - - - - - - - - - - - - -->

			<?php $i = 0; $img = array(); ?>

			<?php foreach ( $images as $id => $attach_id ): ?>

				<?php if ( $attach_id > 0 ): ?>

					<?php $img = wpb_getImageBySize( array( 'attach_id' => (int) $attach_id, 'thumb_size' => 'knowhere-agent-photo' ) ); ?>

				<?php else: ?>

					<?php $img['thumbnail'] = '<img alt="" src="' . vc_asset_url( 'vc/no_image.png' ) . '" />'; ?>

				<?php endif; ?>

				<?php $link = ( isset($links[$i]) && !empty($links[$i]) ) ? trim($links[$i]) : ''; ?>

				<div class="kw-partners-item">
					<a href="<?php echo esc_url($link) ?>">
						<?php echo sprintf('%s', $img['thumbnail']); ?>
					</a>
				</div><!--/ .kw-partners-item-->

				<?php $i++; ?>

			<?php endforeach; ?>

			<!-- - - - - - - - - - - - - - End of Item - - - - - - - - - - - - - - - - -->

		</div>

		<?php return ob_get_clean();
	}

}