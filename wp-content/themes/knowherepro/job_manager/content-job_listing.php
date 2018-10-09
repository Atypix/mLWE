<?php global $post, $knowhere_settings;

$card_image = 'knowhere-card-image';
$company_phone = get_post_meta( get_the_ID(), '_company_phone', true);
$terms = get_the_terms( get_the_ID(), 'job_listing_category' );
$listing_type = get_the_terms( get_the_ID(), 'job_listing_type' );
$termString = '';
$data_output = '';
if ( ! is_wp_error( $terms ) && ( is_array( $terms ) || is_object( $terms ) ) ) {
	$firstTerm = $terms[0];
	if ( ! $firstTerm == null ) {
		$term_id = $firstTerm->term_id;
		$data_output .= ' data-icon="' . knowhere_get_term_icon_url( $term_id ) . '"';
		$count = 1;
		foreach ( $terms as $term ) {
			$termString .= $term->name;
			if ( $count != count( $terms ) ) {
				$termString .= ', ';
			}
			$count ++;
		}
	}
}

$post_image_src = knowhere_get_post_image_src( $post->ID, $card_image );

?>

<div class="kw-listing-item-wrap">

	<article <?php job_listing_class('kw-listing-item'); ?>
		data-latitude="<?php echo esc_attr( get_post_meta( $post->ID, 'geolocation_lat', true ) ); ?>"
		data-longitude="<?php echo esc_attr( get_post_meta( $post->ID, 'geolocation_long', true ) ); ?>"

		<?php if ( $knowhere_settings['job-show-phone-in-popup'] ): ?>
			data-phone="<?php echo knowhere_get_the_company_phone($post->ID) ? knowhere_get_the_company_phone($post->ID) : '' ?>"
		<?php endif; ?>

		data-categories="<?php echo esc_attr( $termString ); ?>"
		data-img="<?php echo esc_attr( knowhere_get_post_image_src( $post->ID, 'thumbnail' ) ); ?>"
		data-permalink="<?php echo esc_attr( get_the_job_permalink() ); ?>"
		<?php echo $data_output;  ?>>

		<?php if ( !empty($post_image_src) ): ?>

			<!-- - - - - - - - - - - - - - Media - - - - - - - - - - - - - - - - -->

			<div class="kw-listing-item-media">

				<?php knowhere_label_hours_output( get_the_ID() ); ?>

				<?php knowhere_label_featured( get_the_ID() ); ?>

				<a href="<?php the_job_permalink(); ?>" class="kw-listing-item-thumbnail">
					<img src="<?php echo esc_url($post_image_src); ?>" alt="">
				</a>
				<div style="text-align:center;position:absolute;top:-5px;right:20px;background-color:white;color:#d13060;border-radius: 5px;padding:10px 10px 10px 10px;font-size:18px">
					<?php if (get_post_meta( $post->ID, '_job_price_range_min', true ) != 0) {
						?><div style="font-size:10px;color:#4f4f4f;margin-bottom:5px">Dès </div><?php echo get_post_meta( $post->ID, '_job_price_range_min', true ).'<span style="font-size:15px">€</span>';
					} else {
						?><span style="font-size:15px">GRATUIT</span><?php
					} ?>
					
				</div>

				<ul class="kw-listing-card-meta">
					<?php if ( ! is_wp_error( $terms ) && ( is_array( $terms ) || is_object( $terms ) ) ) { ?>
						<?php $i = 0; ?>
						<?php foreach ( $terms as $term ) {
							$icon_url      = knowhere_get_term_icon_url( $term->term_id );
							$attachment_id = knowhere_get_term_icon_id( $term->term_id );

							if ( $i > 2 ) { continue; }

							if ( empty( $icon_url ) ) {
								continue;
							} ?>
							<li class="kw-listing-term-list">
								<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" title="<?php echo sprintf('%s', $term->name ) ?>" class="kw-listing-item-icon">
									<?php knowhere_display_icon_or_image( $icon_url, '', true, $attachment_id ); ?>
								</a>
							</li>
							<?php $i++; ?>
						<?php } ?>
					<?php } ?>

					<?php do_action('knowhere_listing_card_meta_end', $post) ?>

				</ul><!--/ .kw-listing-card-meta-->

			</div>

			<!-- - - - - - - - - - - - - - End of Media - - - - - - - - - - - - - - - - -->

		<?php endif; ?>

		<div class="kw-listing-item-media kw-listing-style-3">

			<?php $photos = knowhere_get_listing_gallery_ids(); ?>

			<?php if ( $photos && count($photos) > 1 ): ?>

				<div class="kw-property-slideshow owl-carousel">

					<?php foreach ( $photos as $key => $photo_id ):
						$src = wp_get_attachment_image_src( $photo_id, $card_image ); ?>
						<a href="<?php the_job_permalink() ?>"><img src="<?php echo esc_url($src[0]); ?>" alt=""/></a>
					<?php endforeach; ?>

				</div><!--/ .kw-property-slideshow-->

			<?php else: ?>

				<a href="<?php the_job_permalink(); ?>" class="kw-listing-item-thumbnail">
					<img src="<?php echo knowhere_get_post_image_src( $post->ID, $card_image ); ?>" alt="">
				</a>

			<?php endif; ?>

			<?php knowhere_label_hours_output( get_the_ID() ) ?>

			<?php knowhere_label_featured( get_the_ID(), array( 'classes' => array('kw-right-aligned') ) ); ?>

			<?php
			if ( ! is_wp_error( $listing_type ) && ( is_array( $listing_type ) || is_object( $listing_type ) ) ) {
				$firstType = $listing_type[0];
				if ( ! $firstType == NULL ) {
					$name = $firstType->name;
					echo '<span class="kw-label-type">'. esc_html($name) .'</span>';
				}
			}
			?>

		</div><!--/ .kw-listing-item-media-->

		<?php knowhere_listing_media_output( array('post' => $post) );  ?>

		<div class="kw-listing-item-media kw-listing-style-5">

			<?php knowhere_label_hours_output( get_the_ID() ); ?>

			<?php knowhere_label_featured( get_the_ID() ); ?>

			<a href="<?php the_job_permalink(); ?>" class="kw-listing-item-thumbnail">
				<img src="<?php echo esc_url($post_image_src); ?>" alt="">
			</a>

			<a href="<?php the_job_permalink(); ?>" class="kw-listing-item-like">
				<span class="lnr icon-heart"></span>
			</a>

			<?php $photos = knowhere_get_listing_gallery_ids(); ?>

			<?php if ( $photos ): ?>
				<div class="kw-listing-item-photo-amount"><?php echo absint(count($photos)) ?></div>
			<?php endif; ?>

		</div><!--/ .kw-listing-item-media-->

		<!-- - - - - - - - - - - - - - Description - - - - - - - - - - - - - - - - -->

		<div class="kw-listing-item-info">

			<header class="kw-listing-item-header">

				<h3 class="kw-listing-item-title">
					<a href="<?php the_job_permalink(); ?>"><?php echo the_title(); ?></a>
				</h3>

				<div class="kw-card-rating">
					<?php knowhere_job_listing_rating() ?>
				</div>

			</header><!--/ .kw-listing-item-header-->

			<ul class="kw-listing-item-data kw-icons-list">
				<?php if ( get_the_job_location() ): ?>
					<li class="kw-listing-item-location"><span class="lnr icon-map-marker"></span><?php echo get_the_job_location(); ?></li>
				<?php endif; ?>

				<?php if ( !empty($company_phone) ): ?>
					<li class="kw-listing-item-phone"><span class="lnr icon-telephone"></span><?php echo esc_html($company_phone); ?></li>
				<?php endif; ?>

			</ul>

			<a href="javascript:void(0)" target="_blank" class="kw-listing-item-pintpoint"><span class="lnr icon-pushpin"></span> <?php esc_html_e( 'Pintpoint', 'knowherepro' ) ?></a>

		</div><!--/ .kw-listing-item-info-->

		<div class="kw-listing-item-info kw-listing-style-3">

			<div class="kw-listing-meta">

				<?php if ( ! is_wp_error( $terms ) && ( is_array( $terms ) || is_object( $terms ) ) ): ?>

					<ul class="kw-listing-cats">
						<?php echo get_the_term_list( get_the_ID(), 'job_listing_category', '<li>', ', ', '</li>' ); ?>
					</ul><!--/ .kw-listing-cats-->

				<?php endif; ?>

				<?php if ( $knowhere_settings['job-type-fields'] == 'property' ): ?>

					<?php $open_date = get_post_meta( get_the_ID(), '_open_house_date', true ); ?>

					<?php if ( !empty($open_date) ): ?>

						<?php $new_open_date = date( "d M", strtotime($open_date) ); ?>

						<?php if ( !empty($new_open_date) ): ?>
							<div class="kw-listing-pubdate"><?php echo esc_html__('Open At', 'knowherepro') . ' ' . $new_open_date ?></div>
						<?php endif; ?>

					<?php endif; ?>

				<?php endif; ?>

			</div><!--/ .kw-listing-meta-->

			<h4 class="kw-item-title"><a href="<?php the_job_permalink(); ?>"><?php the_title(); ?></a></h4>

			<h3 class="kw-listing-price">
				<?php echo knowhere_get_invoice_price( get_the_ID() ) ?>
			</h3>

			<div class="kw-listing-address"><?php echo get_the_job_location(); ?></div>

			<?php if ( $knowhere_settings['job-type-fields'] == 'property' ): ?>
				<?php echo knowhere_get_property_features( get_the_ID() ) ?>
			<?php endif; ?>

		</div><!--/ .kw-listing-item-info-->

		<div class="kw-listing-item-info kw-listing-style-4">

			<header class="kw-listing-item-header">

				<div class="kw-xs-table-row">

					<?php if ( get_option( 'job_manager_enable_types' ) && $job_type = wpjm_get_the_job_types( $post ) ) : ?>
						<div class="col-xs-6">
							<?php knowhere_bg_color_label( $job_type ); ?>
						</div>
					<?php endif; ?>

					<div class="col-xs-6 kw-right-edge">
						<span class="kw-listing-item-date"><?php the_job_publish_date($post); ?></span>
					</div>

				</div>

			</header>

			<h3 class="kw-listing-item-title"><a href="<?php the_job_permalink($post); ?>"><?php echo get_the_title($post) ?></a></h3>

			<ul class="kw-listing-item-data kw-icons-list">

				<?php knowhere_job_listing_company($post) ?>

				<?php if ( get_the_job_location($post) ): ?>
					<li><span class="lnr icon-map-marker"></span><?php echo get_the_job_location($post); ?></li>
				<?php endif; ?>

				<?php knowhere_price_range_output( $post->ID ) ?>

			</ul>

		</div>

		<div class="kw-listing-item-info kw-listing-style-5">

			<header class="kw-listing-item-header">

				<?php if ( !is_wp_error( $terms ) && ( is_array( $terms ) || is_object( $terms ) ) ) { ?>

					<div class="kw-listing-item-categories">
						<?php echo get_the_term_list( get_the_ID(), 'job_listing_category', '', ', ', '' ); ?>
					</div>

				<?php } ?>

				<h3 class="kw-listing-item-title">
					<a href="<?php the_job_permalink(); ?>"><?php the_title(); ?></a>
				</h3>

			</header>

			<?php if ( knowhere_get_the_company_description() ) : ?>
				<div class="kw-listing-item-description">
					<?php knowhere_the_company_description(); ?>
				</div>
			<?php endif; ?>

			<?php echo knowhere_the_job_publish_date(); ?>

			<footer class="kw-listing-item-footer">

				<div class="kw-xs-table-row row">

					<div class="col-xs-5">
						<?php if ( knowhere_get_invoice_price( get_the_ID(), get_post_meta( get_the_ID(), '_job_price_range_min', true ) ) ): ?>
							<strong class="kw-listing-item-price">
								<?php echo knowhere_get_invoice_price( get_the_ID(), get_post_meta( get_the_ID(), '_job_price_range_min', true ) ) ?>
							</strong>
						<?php endif; ?>
					</div>

					<div class="col-xs-7 kw-right-edge">
						<?php $job_location = get_post_meta( get_the_ID(), 'geolocation_city', true); ?>
						<?php if ( !empty($job_location) ): ?>
							<ul class="kw-listing-item-data kw-icons-list">
								<li><span class="lnr icon-map-marker"></span><?php echo trim( $job_location ); ?></li>
							</ul>
						<?php endif; ?>
					</div>

				</div>

			</footer><!--/ .kw-listing-item-footer-->

		</div><!--/ .kw-listing-item-info-->

		<!-- - - - - - - - - - - - - - End of Description - - - - - - - - - - - - - - - - -->

		<footer class="kw-listing-extra">

			<div class="kw-listing-extra-container">

				<div class="kw-extended-link">
					<a href="<?php the_job_permalink() ?>"><?php esc_html_e( 'Details', 'knowherepro' ) ?></a>
				</div><!--/ .kw-extended-link -->

				<div class="kw-action-col">

					<?php $id = rand(200, 500); ?>

					<a class="kw-listing-action kw-share-popup-link" href="#kw-share-popup-<?php echo absint($id) ?>"><span class="lnr icon-share2"></span></a>

					<div id="kw-share-popup-<?php echo absint($id) ?>" class="kw-share-popup mfp-hide">
						<?php if ( function_exists('knowhere_job_single_share') ): ?>
							<?php knowhere_job_single_share(); ?>
						<?php endif; ?>
					</div>

				</div>

				<div class="kw-action-col">
					<?php knowhere_add_bookmark_heart_to_content( $post ) ?>
				</div>

			</div><!--/ .kw-listing-extra-container -->

		</footer><!--/ .kw-listing-extra-->
		

		<div class="kw-listing-hidden-data">
			<h3 class="kw-listing-item-title">
				<a href="<?php the_job_permalink(); ?>"><?php echo the_title(); ?></a>
			</h3>
			<div class="kw-listing-item-location"><span class="lnr icon-map-marker"></span><?php echo get_the_job_location(); ?></div>
			<ul class="kw-listing-card-meta">
				<?php if ( ! is_wp_error( $terms ) && ( is_array( $terms ) || is_object( $terms ) ) ) { ?>
					<?php $i = 0; ?>
					<?php foreach ( $terms as $term ) {
						$icon_url      = knowhere_get_term_icon_url( $term->term_id );
						$attachment_id = knowhere_get_term_icon_id( $term->term_id );

						if ( $i > 0 ) continue;

						if ( empty( $icon_url ) ) {
							continue;
						} ?>
						<li class="kw-listing-term-list">
							<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" title="<?php echo sprintf('%s', $term->name ) ?>" class="kw-listing-item-icon">
								<?php knowhere_display_icon_or_image( $icon_url, '', true, $attachment_id ); ?>
							</a>
						</li>
						<?php $i++; ?>
					<?php } ?>
				<?php } ?>

				<?php do_action('knowhere_listing_card_meta_end', $post) ?>

			</ul>
		</div><!--/ .kw-listing-hidden-data-->

	</article>

</div><!--/ .kw-listing-item-wrap-->