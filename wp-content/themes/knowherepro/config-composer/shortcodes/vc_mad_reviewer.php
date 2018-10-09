<?php

class WPBakeryShortCode_VC_mad_reviewer extends WPBakeryShortCode {

	public $atts = array();
	public $ratings = array();
	public $templates = '';

	protected function content( $atts, $content = null ) {

		if ( ! class_exists('RWP_Reviewer') ) return;

		$this->atts = shortcode_atts(array(
			'title' => '',
			'subtitle' => '',
			'title_color' => '',
			'subtitle_color' => '',
			'align_title' => '',
			'carousel' => true,
			'type_carousel' => 'kw-testimonials-carousel-v1',
			'style_dots' => 'kw-dots-style-light',
			'items' => 12,
			'sort' => 'latest',
			'limit' => 150
		), $atts, 'vc_mad_listing_cards');

		$this->query_entries();
		$html = $this->html();

		return $html;
	}

	public function query_entries($params = array()) {

		if ( empty($params) ) $params = $this->atts;

		extract($params);

		$this->ratings = $this->get_ratings();

	}

	public static function sort_score( $a, $b )
	{
		$avg_a = RWP_Reviewer::get_avg( $a['rating_score'] );
		$avg_b = RWP_Reviewer::get_avg( $b['rating_score'] );

		if (  $avg_a ==  $avg_b )
			return 0;

		return ( $avg_a >  $avg_b ) ? -1 : 1;
	}

	public static function sort_latest( $a, $b )
	{
		if ($a["rating_date"] == $b["rating_date"])
			return 0;

		return ($a["rating_date"] > $b["rating_date"]) ? -1 : 1;
	}

	public function get_ratings()
	{
		global $wpdb;
		$result = array();
		$atts = $this->atts;
		$limit = $atts['items'];

		$post_meta = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE meta_key LIKE 'rwp_rating%';", ARRAY_A );

		foreach( $post_meta as $meta ) {

			$rating = unserialize( $meta['meta_value'] );

			if( !isset( $rating['rating_id'] ) )
				continue;

			$rating['rating_meta_id'] = $meta['meta_id'];

			$result[ $rating['rating_id'] ] = $rating;
		}

		switch ( $atts['sort'] ) {

			case 'top_score':
				usort( $result, array( $this, 'sort_score' ) );
				break;

			case 'latest':
			default:
				usort( $result, array( $this, 'sort_latest' ) );
				break;
		}

		// Limit
		$rts = array_slice ( $result , 0, $limit );

		return $rts;
	}

	public function rwp_rt_score( $item ) {
		$post_id        = $item['rating_post_id'];
		$review_id      = $item['rating_review_id'];
		$reviews        = get_post_meta( $post_id, 'rwp_reviews', true);

		$this->templates = RWP_Reviewer::get_option('rwp_templates');

		if ( !$this->templates ) return;

		if ( isset( $reviews[ $review_id ] ) && $reviews[ $review_id ]['review_id'] == $review_id )  {
			$template_id = $reviews[ $review_id ]['review_template'];
		} else if ( isset( $item['rating_template'] ) ){
			$template_id = $item['rating_template'];
		} else {
			return esc_html__('Unable to display scores', 'knowherepro');
		}

		$template = $this->templates[ $template_id ];

		if ( !$template ) return;

		$html = '';
		$criteria   = $template['template_criterias'];
		$order      = ( isset( $template['template_criteria_order'] ) ) ? $template['template_criteria_order'] : array_keys( $criteria);

		$sum = array();

		foreach ( $order as $i )  {
			$sum[] = RWP_Reviewer::format_number(  $item['rating_score'][$i] );
		}

		$count = count($sum);
		$sum = array_sum($sum);
		$rating = absint($sum / $count);

		if ( is_numeric($rating) ) {
			$html .= '<div class="kw-rating" data-rating="'. absint($rating) .'"></div>';
		}

		return $html;
	}

	public function rwp_rt_author( $item )
	{
		$user_id    = $item['rating_user_id'];
		$user_name  = ( $user_id > 0 ) ? get_user_by('id', $user_id)->display_name : $item['rating_user_name'];

		$html_avatar = '';
		$avatar = ( $user_id == 0 && isset( $item['rating_user_email'] ) && !empty( $item['rating_user_email'] ) ) ? $item['rating_user_email'] : $user_id;

		$html = '<div class="kw-author-box">';

			if ( $avatar ) {
				$html_avatar .= '<div class="kw-avatar">';
				$html_avatar .= '' . get_avatar( $avatar, 120 );
				$html_avatar .= '</div>';
				$html .= $html_avatar;
			}

			if (  $user_id == 0 && isset( $item['rating_user_email'] ) && !empty( $item['rating_user_email'] ) ) {
				$email = $item['rating_user_email'];
			} else if( $user_id != 0) {
				$email = get_user_by('email', $user_id);
			} else {
				$email = '';
			}

			$html .= '<div class="kw-author-info">';

				if ( !empty( $email ) && is_email($email) ) {
					$html .= '<a class="kw-author-name" href="mailto:'. $email .'">';
				}

				if ( !empty($user_name) ) {
					$html .= $user_name;
				}

				if ( !empty( $email ) && is_email($email) ) {
					$html .= '</a>';
				}

			$html .= '</div>';

		$html .= '</div>';

		return $html;
	}

	public function html() {

		if ( empty($this->ratings) ) return;

		$atts = $this->atts;

		$title = !empty($atts['title']) ? $atts['title'] : '';
		$subtitle = !empty($atts['subtitle']) ? $atts['subtitle'] : '';
		$title_color = !empty($atts['title_color']) ? $atts['title_color'] : '';
		$subtitle_color = !empty($atts['subtitle_color']) ? $atts['subtitle_color'] : '';
		$align_title = !empty($atts['align_title']) ? $atts['align_title'] : '';
		$carousel = $atts['carousel'] == true ? true : false;
		$type_carousel = $atts['type_carousel'];
		$limit = !empty($atts['limit']) ? $atts['limit'] : 150;
		$items = !empty($atts['items']) ? $atts['items'] : 12;
		$style_dots = $atts['style_dots'];

		$css_classes = array( $type_carousel );

		if ( $type_carousel == 'kw-testimonials-carousel-v1' ) {
		} elseif ( $type_carousel == 'kw-testimonials-carousel-v2' ) {
			$css_classes[] = 'kw-testimonials kw-type-3 owl-left-aligned-dots';
		} elseif ( $type_carousel == 'kw-testimonials-carousel-v3' ) {
			$css_classes[] = 'kw-testimonials kw-type-4 owl-right-aligned-dots';
		}

		if ( $carousel ) {
			$css_classes[] = 'owl-carousel';
			$css_classes[] = $style_dots;
		}

		$css_class = preg_replace( '/\s+/', ' ', implode( ' ', array_filter( $css_classes ) ) );

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

		<?php if ( $type_carousel == 'kw-testimonials-carousel-v1' ): ?>

			<?php
			$this->ratings = array_slice($this->ratings, 0, $items);
			$container = array_chunk( $this->ratings, 4 );
			?>

			<?php if ( !empty($container) ): ?>

				<div class="<?php echo esc_attr( trim($css_class) ) ?>">

					<?php foreach ( $container as $item ): ?>

						<div class="kw-testimonials kw-container-v1">

							<?php foreach ( $item as $rating ) : ?>

								<article class="kw-testimonial">

									<?php echo $this->rwp_rt_author($rating); ?>

									<div class="kw-testimonial-content">

										<h4 class="kw-testimonial-title"><?php echo sprintf('%s', $rating['rating_title']) ?></h4>

										<blockquote><?php echo knowhere_get_excerpt( $rating['rating_comment'], $limit ) ?></blockquote>

										<?php echo $this->rwp_rt_score($rating); ?>

									</div><!--/ .kw-testimonial-content -->

								</article>

							<?php endforeach; ?>

						</div>

					<?php endforeach; ?>

				</div>

			<?php endif; ?>

		<?php elseif ( $type_carousel == 'kw-testimonials-carousel-v2' ):  ?>

			<?php $this->ratings = array_slice($this->ratings, 0, $items); ?>

			<div class="<?php echo esc_attr($css_class) ?>">

				<?php foreach ( $this->ratings as $rating ) : ?>

					<article class="kw-testimonial">

						<div class="kw-testimonial-content">

							<h4 class="kw-testimonial-title"><a href="<?php echo esc_url(get_the_permalink($rating['rating_post_id'])) ?>"><?php echo sprintf('%s', $rating['rating_title']) ?></a></h4>

							<blockquote><?php echo knowhere_get_excerpt( $rating['rating_comment'], $limit ) ?></blockquote>

							<?php echo $this->rwp_rt_score($rating); ?>

						</div><!--/ .kw-testimonial-content -->

						<?php echo $this->rwp_rt_author($rating); ?>

					</article>

				<?php endforeach; ?>

			</div>

		<?php elseif ( $type_carousel == 'kw-testimonials-carousel-v3' ):  ?>

			<?php $this->ratings = array_slice($this->ratings, 0, $items); ?>

			<div class="<?php echo esc_attr($css_class) ?>">

				<?php foreach ( $this->ratings as $rating ) : ?>

					<article class="kw-testimonial">

						<div class="kw-testimonial-content">

							<blockquote><?php echo knowhere_get_excerpt( $rating['rating_comment'], $limit ) ?></blockquote>

							<?php echo $this->rwp_rt_score($rating); ?>

						</div><!--/ .kw-testimonial-content -->

						<?php echo $this->rwp_rt_author($rating); ?>

					</article>

				<?php endforeach; ?>

			</div>

		<?php endif; ?>

		<?php return ob_get_clean();
	}

}