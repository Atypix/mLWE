<?php

class WPBakeryShortCode_VC_mad_listing_map extends WPBakeryShortCode {

	public $atts = array();
	public $listings = '';

	protected function content( $atts, $content = null ) {

		$this->atts = shortcode_atts(array(
			'title' => '',
			'subtitle' => '',
			'title_color' => '',
			'subtitle_color' => '',
			'align_title' => '',
			'per_page' => 10
		), $atts, 'vc_mad_listing_map');

		$html = $this->html();

		return $html;
	}

	public function html() {

		$atts = $this->atts;

		$per_page = absint($atts['per_page']);
		$title = !empty($atts['title']) ? $atts['title'] : '';
		$subtitle = !empty($atts['subtitle']) ? $atts['subtitle'] : '';
		$title_color = !empty($atts['title_color']) ? $atts['title_color'] : '';
		$subtitle_color = !empty($atts['subtitle_color']) ? $atts['subtitle_color'] : '';
		$align_title = !empty($atts['align_title']) ? $atts['align_title'] : '';

		ob_start(); ?>

			<div class="kw-listings-shortcode-map">

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

				<div class="kw-shortcode-hidden-map"><?php echo do_shortcode('[jobs per_page=' . $per_page . ' show_map="false"]') ?></div>
				<div class="kw-listings-gmap" id="kw-listings-gmap"></div>

				<?php locate_template( array( 'job_manager/job-filters-shortcode.php' ), true, false ); ?>

			</div><!--/ .kw-listings-shortcode-map-->

		<?php return ob_get_clean();
	}

}