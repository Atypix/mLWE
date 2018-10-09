<?php

class WPBakeryShortCode_VC_mad_agent extends WPBakeryShortCode {

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
		), $atts, 'vc_mad_agent');

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
					'taxonomy' => 'agent_category',
					'field' => 'id',
					'terms' => $categories
				)
			);
		}

		$query = array(
			'post_type' => 'knowhere_agent',
			'orderby' => $params['orderby'],
			'order' => $params['order'],
			'paged' => $page,
			'posts_per_page' => $params['items']
		);

		if ( !empty($tax_query) ) {
			$query['tax_query'] = $tax_query;
		}

		$this->agents = new WP_Query($query);
	}

	public function html() {

		if ( empty($this->agents ) || empty($this->agents->posts)) return;

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

			<div class="kw-team-members owl-carousel">

				<?php foreach ( $this->agents->posts as $agent ):
					$id = $agent->ID;
					$name = get_the_title($id);
					$link = get_permalink($id);
					$address = get_post_meta( $id, 'knowhere_agent_address', true );
					?>

					<figure class="kw-team-member">

						<a href="<?php echo esc_url($link) ?>" class="kw-team-member-photo">
							<?php echo get_the_post_thumbnail( $agent, 'knowhere-agent-photo' ); ?>
						</a>

						<figcaption class="kw-team-member-info">

							<h4 class="kw-team-member-name"><a href="<?php echo esc_url($link) ?>"><?php echo esc_html($name) ?></a></h4>

							<?php if ( isset($address) && !empty($address) ): ?>
								<div class="kw-team-member-position"><?php echo esc_html($address) ?></div>
							<?php endif; ?>

						</figcaption>

					</figure><!--/ .kw-team-member -->

				<?php endforeach; ?>

			</div><!--/ .kw-team-members-->

		<?php return ob_get_clean();
	}

}