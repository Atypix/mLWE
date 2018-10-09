<?php

class WPBakeryShortCode_VC_mad_testimonials extends WPBakeryShortCode {

	public $atts = array();
	public $entries = '';

	protected function content($atts, $content = null) {

		$this->atts = shortcode_atts(array(
			'title' => '',
			'subtitle' => '',
			'title_color' => '',
			'subtitle_color' => '',
			'align_title' => '',
			'orderby' => 'date',
			'order' => 'DESC',
			'items' => 6,
			'categories' => array(),
		), $atts, 'vc_mad_testimonials');

		$this->query_entries();

		return $this->html();
	}

	public function query_entries() {
		$params = $this->atts;
		$page = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : get_query_var( 'page' );
		if ( !$page ) $page = 1;

		$tax_query = array();

		if ( !empty($params['categories']) ) {
			$categories = explode(',', $params['categories']);
			$tax_query = array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'testimonials_category',
					'field' => 'id',
					'terms' => $categories
				)
			);
		}

		$query = array(
			'post_type' => 'testimonials',
			'orderby' => $params['orderby'],
			'order' => $params['order'],
			'paged' => $page,
			'posts_per_page' => $params['items']
		);

		if ( !empty($tax_query) ) {
			$query['tax_query'] = $tax_query;
		}

		$this->testimonials = new WP_Query($query);
	}

	public function html() {

		if ( empty($this->testimonials ) || empty($this->testimonials->posts)) return;

		$atts = $this->atts;
		extract( $this->atts );

		$title = !empty($atts['title']) ? $atts['title'] : '';
		$subtitle = !empty($atts['subtitle']) ? $atts['subtitle'] : '';
		$title_color = !empty($atts['title_color']) ? $atts['title_color'] : '';
		$subtitle_color = !empty($atts['subtitle_color']) ? $atts['subtitle_color'] : '';
		$align_title = !empty($atts['align_title']) ? $atts['align_title'] : '';

		ob_start(); ?>

		<?php
			echo Knowhere_Vc_Config::getParamTitle(
				array(
					'heading' => 'h3',
					'title' => $title,
					'subtitle' => $subtitle,
					'title_color' => $title_color,
					'subtitle_color' => $subtitle_color,
					'align_title' => $align_title
				)
			);
		?>

		<div class="kw-testimonials kw-testimonials-carousel-v4 owl-carousel">

			<?php foreach ( $this->testimonials->posts as $testimonial ):
				$id = $testimonial->ID;
				$name = get_the_title($id);
				$link = get_permalink($id);
				$photo_image_id = get_post_meta( $id, 'knowhere_testi_photo', true );
				?>

				<article class="kw-testimonial">

					<div class="kw-author-box">

						<a href="<?php echo esc_url($link) ?>" class="kw-avatar">
							<?php
							$photo = '';
							if ( ! empty( $photo_image_id ) ) {
								$photo = wp_get_attachment_image_src( $photo_image_id );
							}

							if ( ! empty( $photo ) && ( strstr( $photo[0], 'http' ) || file_exists( $photo[0] ) ) ) {
								$photo = $photo[0];
								echo '<img src="' . esc_attr( $photo ) . '" alt="' . esc_attr( get_the_title( $id ) ) . '" />';
							}
							?>
						</a>

						<div class="kw-author-info">
							<a href="<?php echo esc_url($link) ?>" class="kw-author-name">
								<?php echo get_post_meta( $id, 'knowhere_testi_name', true ); ?>
							</a>
							<div class="kw-author-position"><?php echo get_post_meta( $id, 'knowhere_testi_city', true ); ?></div>
						</div>

					</div><!--/ .kw-author-box -->

					<div class="kw-testimonial-content">
						<h4><?php echo esc_html($name) ?></h4>
						<blockquote><?php echo get_post_meta( $id, 'knowhere_testi_text', true ); ?></blockquote>
					</div><!--/ .kw-testimonial-content -->

				</article><!--/ .kw-testimonial-->

			<?php endforeach; ?>

		</div><!--/ .kw-testimonials-->

		<?php return ob_get_clean();
	}

}