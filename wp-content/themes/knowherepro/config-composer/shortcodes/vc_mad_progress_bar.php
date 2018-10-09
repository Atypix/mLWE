<?php

class WPBakeryShortCode_VC_mad_progress_bar extends WPBakeryShortCode {

	public $atts = array();
	public $content = '';

	protected function content($atts, $content = null) {

		$this->atts = shortcode_atts(array(
			'title' => '',
			'values' => '',
			'units' => '',
		), $atts, 'vc_mad_progress_bar');

		$html = $this->html();
		return $html;
	}

	public function html() {

		$title = $values = $units = '';

		extract($this->atts);

		$values = (array) vc_param_group_parse_atts( $values );
		$max_value = 0.0;
		$graph_lines_data = array();

		foreach ( $values as $data ) {
			$new_line = $data;
			$new_line['value'] = isset( $data['value'] ) ? $data['value'] : 0;
			$new_line['label'] = isset( $data['label'] ) ? $data['label'] : '';
			$new_line['color'] = isset( $data['color'] ) ? $data['color'] : '';

			if ( $max_value < (float) $new_line['value'] ) {
				$max_value = $new_line['value'];
			}
			$graph_lines_data[] = $new_line;
		}

		$wrapper_attributes = array();

		$css_classes = array(
			'wpb_content_element',
			'kw-progress-bars-holder'
		);

		$css_class = preg_replace( '/\s+/', ' ', implode( ' ', array_filter( $css_classes ) ) );
		$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';

		ob_start(); ?>

		<?php if ( !empty($values) ): ?>

			<?php
			if ( $title ) {
				echo '<h2>' . esc_html($title) . '</h2>';
			}
			?>

			<div <?php echo implode( ' ', $wrapper_attributes ) ?>>

				<?php foreach ( $graph_lines_data as $line ): ?>

					<?php $unit = ( '' !== $units ) ? $units : ''; ?>

					<div class="kw-pbar-wrap">

						<h6 class="kw-pbar-title"><?php echo esc_html($line['label']) ?></h6>

						<div class="kw-pbar" data-unit="<?php echo esc_attr($unit) ?>" data-value="<?php echo esc_attr( $line['value'] ) ?>">
							<div class="kw-pbar-inner" data-value="<?php echo esc_attr( $line['value'] ) ?>" data-unit="<?php echo esc_attr($unit) ?>"></div>
						</div>

					</div>

				<?php endforeach; ?>

			</div>

		<?php endif; ?>

		<?php return ob_get_clean();
	}

}