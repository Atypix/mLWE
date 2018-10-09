<?php

class WPBakeryShortCode_VC_mad_counter extends WPBakeryShortCode {

	public $atts = array();

	protected function content($atts, $content = null) {

		$this->atts = shortcode_atts(array(
			'title' => '',
			'subtitle' => '',
			'title_color' => '',
			'subtitle_color' => '',
			'align_title' => '',
			'type' => 'kw-type-1',
			'columns' => 4,
			'values' => '',
		), $atts, 'vc_mad_counter');

		$html = $this->html();

		return $html;
	}

	public function html() {

		$type = $values = $css_animation = '';

		$atts = $this->atts;

		extract($atts);
		$values = (array) vc_param_group_parse_atts( $values );

		$title = !empty($atts['title']) ? $atts['title'] : '';
		$subtitle = !empty($atts['subtitle']) ? $atts['subtitle'] : '';
		$title_color = !empty($atts['title_color']) ? $atts['title_color'] : '';
		$subtitle_color = !empty($atts['subtitle_color']) ? $atts['subtitle_color'] : '';
		$align_title = !empty($atts['align_title']) ? $atts['align_title'] : '';
		$columns = !empty($atts['columns']) ? 'kw-cols-' . $atts['columns'] : '';

		$css_classes = array(
			'kw-counters-holder', $columns, $type
		);

		$css_class = preg_replace( '/\s+/', ' ', implode( ' ', array_filter( $css_classes ) ) );

		ob_start(); ?>

		<!-- - - - - - - - - - - - - - Counter - - - - - - - - - - - - - - - - -->

		<?php if ( !empty($values) ): ?>

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

				<div class="<?php echo esc_attr(trim($css_class)) ?>">

					<?php foreach ( $values as $value ): ?>

						<?php if ( isset($value['icon']) ): ?>
							<?php $icon = trim($value['icon']); ?>
						<?php endif; ?>

						<div class="kw-counter" data-value="<?php echo esc_attr($value['value']) ?>">

							<div class="kw-counter-inner">

								<?php if ( !empty($icon) && $icon !== 'none' ): ?>
									<span class="lnr <?php echo trim($value['icon']) ?>"></span>
								<?php endif; ?>

								<div class="kw-counter-value"><?php echo esc_attr($value['value']) ?></div>
								<div class="kw-counter-caption"><?php echo esc_html($value['label']) ?></div>
							</div>

						</div><!--/ .kw-counter-->

					<?php endforeach; ?>

				</div>

			</div>

		<?php endif; ?>

		<!-- - - - - - - - - - - - - - End of Counter - - - - - - - - - - - - - - - - -->

		<?php return ob_get_clean();
	}

}