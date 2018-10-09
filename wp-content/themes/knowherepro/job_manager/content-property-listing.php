<?php

global $post;

$card_image = 'knowhere-card-image';

$job_is_featured = false;
if ( is_position_featured( get_the_ID() ) ) $job_is_featured = true;
$post_image_src = knowhere_get_post_image_src( $post->ID, $card_image );
$photos = knowhere_get_listing_gallery_ids();
?>

<div class="kw-listing-item-wrap">

	<article <?php job_listing_class('kw-listing-item'); ?> data-longitude="<?php echo esc_attr( $post->geolocation_long ); ?>" data-latitude="<?php echo esc_attr( $post->geolocation_lat ); ?>">

		<?php if ( $photos || !empty($post_image_src) ): ?>

			<!-- - - - - - - - - - - - - - Media - - - - - - - - - - - - - - - - -->

			<div class="kw-listing-item-media">

				<?php if ( $photos && count($photos) > 1 ): ?>

					<div class="owl-carousel kw-property-slideshow">

						<?php foreach ( $photos as $key => $photo_id ):
							$src = wp_get_attachment_image_src( $photo_id, $card_image ); ?>
							<a href="<?php the_job_permalink() ?>"><img src="<?php echo esc_url($src[0]); ?>" alt=""/></a>
						<?php endforeach; ?>

					</div><!--/ .kw-property-slideshow-->

				<?php else: ?>

					<a href="<?php the_job_permalink(); ?>" class="kw-listing-item-thumbnail">
						<img src="<?php echo esc_url($post_image_src); ?>" alt="">
					</a>

				<?php endif; ?>

				<?php if ( true === $job_is_featured ): ?>
					<span class="kw-label-featured kw-right-aligned"><?php esc_html_e( 'Hot', 'knowherepro' ); ?></span>
				<?php endif; ?>

			</div><!--/ .kw-listing-item-media-->

			<!-- - - - - - - - - - - - - - End of Media - - - - - - - - - - - - - - - - -->

		<?php endif; ?>

		<!-- - - - - - - - - - - - - - Description - - - - - - - - - - - - - - - - -->

		<div class="kw-listing-item-info">

			<div class="kw-listing-meta">

				<ul class="kw-listing-cats">
					<?php echo get_the_term_list( get_the_ID(), 'job_listing_category', '<li>', ', ', '</li>' ); ?>
				</ul><!--/ .kw-listing-cats-->

			</div><!--/ .kw-listing-meta-->

			<h3 class="kw-listing-price">
				<?php echo knowhere_get_invoice_price( get_the_ID() ) ?>
			</h3>

			<div class="kw-listing-address"><?php echo get_the_job_location(); ?></div>

			<?php echo knowhere_get_property_features( get_the_ID() ) ?>

		</div><!--/ .kw-listing-item-info-->

		<!-- - - - - - - - - - - - - - End of Description - - - - - - - - - - - - - - - - -->

		<footer class="kw-listing-extra">

			<div class="kw-listing-extra-container">

				<div class="kw-extended-link">
					<a href="<?php the_job_permalink() ?>"><?php esc_html_e( 'Details', 'knowherepro' ) ?></a>
				</div><!--/ .kw-extended-link -->

				<div class="kw-action-col">

					<?php $id = rand(200, 500); ?>

					<button class="kw-listing-action">
						<a class="kw-share-popup-link" href="#kw-share-popup-<?php echo absint($id) ?>"><span class="lnr icon-share2"></span></a>
					</button>

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

	</article><!--/ .kw-listing-item-->

</div><!--/ .kw-listing-item-wrap-->