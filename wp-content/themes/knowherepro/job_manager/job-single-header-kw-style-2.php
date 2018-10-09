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
?>

<?php echo Knowhere_Job_Manager_Config::get_listing_ribbon_slider() ?>

<div class="kw-style-holder kw-style-2">

	<?php knowhere_job_listing_breadcrumbs(); ?>

	<header class="kw-listing-single-header kw-type-2">

		<div class="container">

			<div class="kw-md-table-row row">

				<div class="col-md-8">

					<h1 class="kw-listing-item-title" itemprop="name">
						<?php echo get_the_title(); ?>
					</h1>

					<div class="kw-listing-item-meta">

						<?php if ( $knowhere_settings['job-single-review'] ): ?>
							<?php if ( isset($rate['score']) && !empty($rate['score']) ) : ?>
								<div class="kw-listing-item-rating">
									<div itemprop="rating" class="kw-rating"
										 data-rating="<?php echo absint($rate['score']) ?>"></div>
									(<?php echo absint($rate['score']) ?>)
								</div>
							<?php endif; ?>
						<?php endif; ?>

						<ul class="kw-icons-list kw-hr-type">

							<?php if ( $knowhere_settings['job-single-review'] ): ?>
								<?php if (isset($rate['count']) && !empty($rate['count'])): ?>
									<li><span class="lnr icon-bubble-quote"></span>
										<?php $count = absint($rate['count']) ?>
										<?php printf(_n('%s review', '%s reviews', $count, 'knowherepro'), $count); ?>
									</li>
								<?php endif; ?>
							<?php endif; ?>

							<?php if ( $knowhere_settings['job-single-bookmarks'] ): ?>
								<?php if ( $job_manager_bookmarks !== null && method_exists( $job_manager_bookmarks, 'bookmark_count' ) ): ?>
									<li><span class="lnr icon-heart"></span> <?php printf( _n( '%s favorite', '%s favorites', $job_manager_bookmarks->bookmark_count(get_the_ID()), 'knowherepro' ), $job_manager_bookmarks->bookmark_count(get_the_ID()) ); ?></li>
								<?php endif; ?>
							<?php endif; ?>

							<?php if ( $knowhere_settings['job-single-views'] ): ?>
								<?php knowhere_job_listing_post_views(); ?>
							<?php endif; ?>

						</ul>

					</div>

					<?php echo knowhere_get_formatted_address(
						$post, array('classes' => array('kw-listing-item-data', 'kw-icons-list', 'kw-hr-type'))
					); ?>

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

						<li><span class="lnr icon-share2"></span><a class="kw-share-popup-link"
								href="#kw-share-popup"><?php esc_html_e('Share', 'knowherepro') ?></a></li>
						<li><span class="lnr icon-printer"></span><a
								href="javascript:window.print()"><?php esc_html_e('Print', 'knowherepro') ?></a></li>
					</ul>

					<div id="kw-share-popup" class="kw-share-popup mfp-hide">
						<?php if ( function_exists('knowhere_job_single_share') ): ?>
							<?php knowhere_job_single_share(); ?>
						<?php endif; ?>
					</div>

				</div>

			</div>

		</div>

	</header>

</div>