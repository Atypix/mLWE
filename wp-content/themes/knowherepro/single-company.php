<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package KnowherePro
 */

get_header();
?>

<?php global $post;

?>

<div id="primary" class="kw-content-area">

	<div class="kw-entry-content">

		<header class="kw-profile-single-header">

			<div class="row">

				<div class="col-sm-9">

					<?php $content = ''; ?>

					<?php
					if ( 'job_listing' == get_post_type() ) {
						ob_start();
						knowhere_the_company_logo( 'fullsize' );
						$logo = ob_get_clean();

						if ( class_exists( 'Astoundify_Job_Manager_Companies' ) && '' != knowhere_get_the_company_name() ) {
							$companies   = Astoundify_Job_Manager_Companies::instance();
							$company_url = esc_url( $companies->company_url( knowhere_get_the_company_name() ) );

							$content .= '<a href="' . esc_url( $company_url ) . '" target="_blank">' . $logo . '</a>';
						} else {
							$content .= $logo;
						}
					} else {

						if ( function_exists('the_candidate_photo')) {

							ob_start();

							the_candidate_photo( 'fullsize' );

							$logo = ob_get_clean();

							$content .= $logo;

						}

					}

					echo $content;
					?>

					<div class="kw-profile-meta">

						<h1 class="kw-listing-item-title"><?php echo knowhere_get_the_company_name() ?></h1>

						<?php if ( knowhere_get_the_company_description() ) : ?>
							<div class="kw-job-company-about">
								<?php knowhere_the_company_description(); ?>
							</div>
						<?php endif; ?>

						<ul class="kw-listing-item-data kw-icons-list kw-hr-type">

							<?php if ( knowhere_get_the_company_website() ): ?>
								<li>
									<a href="<?php echo esc_url( knowhere_get_the_company_website() ); ?>" target="_blank">
										<?php echo knowhere_get_the_company_website() ?>
									</a>
								</li>
							<?php endif; ?>

							<?php if ( knowhere_get_the_company_phone() ): ?>
								<li><span class="lnr icon-telephone"></span><?php echo knowhere_get_the_company_phone() ?></li>
							<?php endif; ?>

						</ul>

						<ul class="kw-social-links">

							<?php if ( knowhere_get_the_company_facebook() ): ?>
								<li><a target="_blank" href="<?php echo esc_url(knowhere_get_the_company_facebook()) ?>"><i class="fa fa-facebook"></i></a></li>
							<?php endif; ?>

							<?php if ( knowhere_get_the_company_gplus() ): ?>
								<li><a target="_blank" href="<?php echo esc_url(knowhere_get_the_company_gplus()) ?>"><i class="fa fa-google-plus"></i></a></li>
							<?php endif; ?>

							<?php if ( knowhere_get_the_company_twitter() ): ?>
								<li><a target="_blank" href="<?php echo esc_url(knowhere_get_the_company_twitter()) ?>"><i class="fa fa-twitter"></i></a></li>
							<?php endif; ?>

							<?php if ( knowhere_get_the_company_linkedin() ): ?>
								<li><a target="_blank" href="<?php echo esc_url(knowhere_get_the_company_linkedin()) ?>"><i class="fa fa-linkedin"></i></a></li>
							<?php endif; ?>

							<?php if ( knowhere_get_the_company_pinterest() ): ?>
								<li><a target="_blank" href="<?php echo esc_url(knowhere_get_the_company_pinterest()) ?>"><i class="fa fa-pinterest"></i></a></li>
							<?php endif; ?>

						</ul>

					</div><!--/ .kw-agent-meta-->

				</div>

				<div class="col-sm-3 kw-right-edge">

					<ul class="kw-listing-item-actions kw-icons-list kw-hr-type">
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

		<div class="kw-company-profile row">

			<div class="kw-company-profile-jobs col-md-8 col-sm-8 col-xs-12"">

				<?php if ( have_posts() ) : ?>

					<div class="kw-flex-holder kw-listings kw-top-position kw-type-4 kw-list-view kw-cols-3 kw-without-map">

						<div class="job_listings">
							<?php while ( have_posts() ) : the_post(); ?>
								<?php get_job_manager_template_part( 'content', 'job_listing' ); ?>
							<?php endwhile; ?>
						</div>

					</div>

				<?php else : ?>

					<?php get_template_part( 'content', 'no-jobs-found' ); ?>

				<?php endif; ?>

			</div><!--/ .kw-company-profile-jobs-->

			<div class="kw-company-profile-info col-md-4 col-sm-4 col-xs-12">
				<?php the_widget( 'Knowhere_Widget_Listing_Contact_Form' ); ?>
			</div>

		</div>

	</div>
</div><!--/ #primary-->
<?php get_footer(); ?>
