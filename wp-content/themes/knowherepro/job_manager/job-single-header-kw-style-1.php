<?php

global $post, $knowhere_settings, $job_manager_bookmarks;
$id = get_the_ID();
$job_id = ! empty( $_REQUEST[ 'job_id' ] ) ? absint( $_REQUEST[ 'job_id' ] ) : 0;

if ( $job_id ) {
	$id = $job_id;
	$post = get_post( $job_id );
	setup_postdata( $post );
}

$rate = knowhere_job_listing_header_rating($id);
$has_image = false;
$photos = knowhere_get_listing_gallery_ids();

if ( has_post_thumbnail() ) : ?>

	<?php $has_image = get_the_post_thumbnail_url( $id, 'knowhere-page-header-image' );  ?>

<?php elseif ( $photos ): ?>

	<?php
	$photo = $photos[0];
	$image = wp_get_attachment_image_src( $photo, '' );
	$src = $image[0] ? $image[0] : '';
	$has_image = $src;
	?>

<?php endif; ?>

<div class="kw-style-holder kw-style-1">

	<?php knowhere_job_listing_breadcrumbs(); ?>

	<div class="kw-page-header kw-dark kw-transparent kw-type-7 <?php echo ( $has_image ) ? 'kw-has-image' : '' ?>">

		<div class="kw-page-header-content align-center">

			<div class="container">

				<header class="kw-listing-single-header kw-type-2">
					
					<div class="kw-md-table-row row">

						<div class="col-md-8">

							<h1 class="kw-listing-item-title" itemprop="name">
								<?php echo get_the_title(); ?>
							</h1>

							<div class="kw-listing-item-meta">

								<?php if ( $knowhere_settings['job-single-review'] ): ?>
									<?php if ( isset($rate['score']) && !empty($rate['score']) ) : ?>
										<div class="kw-listing-item-rating">
											<div itemprop="rating" class="kw-rating" data-rating="<?php echo absint($rate['score']) ?>"></div> (<?php echo absint($rate['score']) ?>)
										</div>
									<?php endif; ?>
								<?php endif; ?>

								<ul class="kw-icons-list kw-hr-type">

									<?php if ( $knowhere_settings['job-single-review'] ): ?>
										<?php if ( isset($rate['count']) && !empty($rate['count']) ): ?>
											<li><span class="lnr icon-bubble-quote"></span>
												<?php $count = number_format_i18n( $rate['count'] ) ?>
												<?php printf( _n( '<a id="kw-write-review-link" href="#kw-reviews">%s review</a>', '<a id="kw-write-review-link" href="#kw-reviews">%s reviews</a>', $count, 'knowherepro' ), $count ); ?>
											</li>
										<?php endif; ?>
									<?php endif; ?>

									<?php if ( $knowhere_settings['job-single-bookmarks'] ): ?>
										<?php if ( $job_manager_bookmarks !== null && method_exists( $job_manager_bookmarks, 'bookmark_count' ) ): ?>
											<li><span class="lnr icon-heart"></span> <?php printf( _n( '%s favorite', '%s favorites', $job_manager_bookmarks->bookmark_count($id), 'knowherepro' ), $job_manager_bookmarks->bookmark_count($id) ); ?></li>
										<?php endif; ?>
									<?php endif; ?>

									<?php if ( $knowhere_settings['job-single-views'] ): ?>
										<?php knowhere_job_listing_post_views(); ?>
									<?php endif; ?>

								</ul>

							</div>

							<?php echo knowhere_get_formatted_address(
								$post, array( 'classes' => array('kw-listing-item-data', 'kw-icons-list', 'kw-hr-type') )
							); ?>

							<?php echo goach_the_next_date_disponible($post) ?>

						</div>

						<div class="col-md-4 kw-right-edge">

							<div class="kw-hr-btns-group">

								<div class="kw-group-item">
									<?php do_action('knowhere_job_listing_actions_start') ?>
								</div>

								<?php if ( class_exists('RWP_Reviewer') ) : ?>
									<div class="kw-group-item">
										<a id="knowhere-write-review-button" href="#kw-write-review"
										   class="kw-btn-medium kw-theme-color">
											<?php esc_html_e('Write a Review', 'knowherepro') ?>
										</a>
									</div>
								<?php endif; ?>

							</div>

							<ul class="kw-listing-item-actions kw-icons-list kw-hr-type">

								<?php if ( $job_manager_bookmarks !== null && method_exists( $job_manager_bookmarks, 'bookmark_form' ) ) : ?>
									<li><?php echo $job_manager_bookmarks->bookmark_form(); ?></li>
								<?php endif; ?>

								<li><span class="lnr icon-share2"></span><a class="kw-share-popup-link" href="#kw-share-popup"><?php esc_html_e('Share', 'knowherepro') ?></a></li>
								<li><span class="lnr icon-printer"></span><a href="javascript:window.print()"><?php esc_html_e('Print', 'knowherepro') ?></a></li>

							</ul>

							<div id="kw-share-popup" class="kw-share-popup mfp-hide">
								<?php if ( function_exists('knowhere_job_single_share') ): ?>
									<?php knowhere_job_single_share(); ?>
								<?php endif; ?>
							</div>

						</div>

					</div>

				</header>

			</div><!--/ .container -->

		</div><!--/ .kw-page-header-content -->

		<?php if ( $has_image ): ?>
			<div class="kw-page-header-media" <?php echo ' style="background-image: url('. esc_url($has_image) .')"' ?>></div>
		<?php endif; ?>

	</div><!--/ .kw-page-header-->

	<div class="kw-sticky-spacer"></div>

	<nav class="kw-additional-nav-wrap">

		<div class="container">

			<div class="kw-sm-table-row kw-xs-small-offset">

				<div class="col-sm-8">

					<ul class="kw-additional-nav">
						<li><a href="#kw-overview"><?php esc_html_e('Overview', 'knowherepro') ?></a></li>

						<?php $details = get_post_meta( $id, 'knowhere_job_details', true );

						if ( isset($details) && !empty($details) && count($details) > 0 ): ?>
							<li><a href="#kw-details"><?php esc_html_e('Details', 'knowherepro') ?></a></li>
			 			<?php endif; ?>
							<li id="reservez_btn"><a href="#reservez">Réservez maintenant !</a></li>
						<?php if ( class_exists('RWP_Reviewer') ): ?>
							<?php if ( isset( $rate['count']) && !empty($rate['count']) ): $count = number_format_i18n( $rate['count'] ); ?>
								<li><a href="#kw-reviews"><?php esc_html_e('Reviews', 'knowherepro') ?> <?php echo sprintf( '(%s)', $count );  ?></a></li>
							<?php endif; ?>
						<?php endif; ?>

					</ul><!--/ .kw-additional-nav -->

				</div>

				<div class="col-sm-4 kw-right-edge">
					<?php knowhere_price_range_output( get_the_ID(), '' ) ?>
				</div>

			</div>

		</div><!--/ .container -->

	</nav>

</div>

<?php wp_reset_postdata(); ?>