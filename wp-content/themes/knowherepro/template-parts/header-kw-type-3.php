
<?php global $knowhere_settings, $knowhere_config; ?>

<!-- - - - - - - - - - - - - - Header Top Bar - - - - - - - - - - - - - - - - -->

<?php if ( $knowhere_settings['show-header-type-3-top-bar'] ): ?>

	<div class="kw-top-bar">

		<div class="container">

			<div class="row">

				<div class="col-sm-12">

					<ul class="kw-hr-list">

						<?php if ( $knowhere_settings['header-type-3-social-phone']): ?>
							<li>
								<div class="kw-nomber-info">
									<span class="lnr icon-telephone"></span><?php echo esc_html($knowhere_settings['header-type-3-social-phone']) ?>
								</div>
							</li>
						<?php endif; ?>

						<?php if ( $knowhere_settings['header-type-3-social-email']): ?>
							<li>
								<div class="kw-mail-info">
									<a href="mailto:<?php echo antispambot($knowhere_settings['header-type-3-social-email'], 1) ?>">
										<span class="lnr icon-envelope"></span> <?php echo esc_html($knowhere_settings['header-type-3-social-email']); ?>
									</a>
								</div>
							</li>
						<?php endif; ?>

					</ul><!--/ .kw-hr-list -->

					<ul class="kw-hr-list">

						<?php if ( $knowhere_settings['show-header-social-links']): ?>

							<li>
								<ul class="kw-social-links">

									<?php if ( $knowhere_settings['header-social-linkedin']): ?>
										<li><a title="<?php echo esc_html__('LinkedIn', 'knowherepro') ?>" href="<?php echo esc_url($knowhere_settings['header-social-linkedin']) ?>"><i class="fa fa-linkedin"></i></a></li>
									<?php endif; ?>

									<?php if ( $knowhere_settings['header-social-tumblr']): ?>
										<li><a title="<?php echo esc_html__('Tumblr', 'knowherepro') ?>" href="<?php echo esc_url($knowhere_settings['header-social-tumblr']) ?>"><i class="fa fa-tumblr"></i></a></li>
									<?php endif; ?>

									<?php if ( $knowhere_settings['header-social-vimeo']): ?>
										<li><a title="<?php echo esc_html__('Vimeo', 'knowherepro') ?>" href="<?php echo esc_url($knowhere_settings['header-social-vimeo']) ?>"><i class="fa fa-vimeo"></i></a></li>
									<?php endif; ?>

									<?php if ( $knowhere_settings['header-social-youtube']): ?>
										<li><a title="<?php echo esc_html__('Youtube', 'knowherepro') ?>" href="<?php echo esc_url($knowhere_settings['header-social-youtube']) ?>"><i class="fa fa-youtube"></i></a></li>
									<?php endif; ?>

									<?php if ( $knowhere_settings['header-social-facebook']): ?>
										<li><a title="<?php echo esc_html__('Facebook', 'knowherepro') ?>" href="<?php echo esc_url($knowhere_settings['header-social-facebook']) ?>"><i class="fa fa-facebook"></i></a></li>
									<?php endif; ?>

									<?php if ( $knowhere_settings['header-social-twitter']): ?>
										<li><a title="<?php echo esc_html__('Twitter', 'knowherepro') ?>" href="<?php echo esc_url($knowhere_settings['header-social-twitter']) ?>"><i class="fa fa-twitter"></i></a></li>
									<?php endif; ?>

									<?php if ( $knowhere_settings['header-social-instagram']): ?>
										<li><a title="<?php echo esc_html__('Instagram', 'knowherepro') ?>" href="<?php echo esc_url($knowhere_settings['header-social-instagram']) ?>"><i class="fa fa-instagram"></i></a></li>
									<?php endif; ?>

									<?php if ( $knowhere_settings['header-social-flickr']): ?>
										<li><a title="<?php echo esc_html__('Flickr', 'knowherepro') ?>" href="<?php echo esc_url($knowhere_settings['header-social-flickr']) ?>"><i class="fa fa-flickr"></i></a></li>
									<?php endif; ?>

								</ul>
							</li>

						<?php endif; ?>

						<?php if ( $knowhere_settings['header-type-3-show-login'] ): ?>
							<?php knowhere_get_login_links(); ?>
						<?php endif; ?>

					</ul><!--/ .kw-hr-list -->

				</div>

			</div>

		</div><!--/ .container -->

	</div>

	<!-- - - - - - - - - - - - - - End of Header Top Bar - - - - - - - - - - - - - - - - -->

<?php endif; ?>

<!-- - - - - - - - - - - - - - Header Section - - - - - - - - - - - - - - - - -->

<?php
$sticky_class = 'kw-sticky';
if ( knowhere_is_realy_job_manager_tax() || is_post_type_archive('job_listing') || knowhere_job_listing_has_shortcode_jobs() ) {

	if ( knowhere_is_realy_job_manager_tax() ) {

		if ( $knowhere_config['sidebar_position'] !== 'kw-no-sidebar' ) {

		} else {
			$sticky_class = '';
		}

	} else {
		$sticky_class = '';
	}

}
?>

<div class="kw-hsection <?php echo sanitize_html_class($sticky_class) ?>">

	<div class="container">

		<div class="kw-sm-table-row row">

			<div class="col-sm-2 col-md-3">

				<!-- - - - - - - - - - - - - - Logo - - - - - - - - - - - - - - - - -->

				<?php echo knowhere_logo(); ?>

				<!-- - - - - - - - - - - - - - End of Logo - - - - - - - - - - - - - - - - -->

			</div>

			<div class="col-sm-10 col-md-9">

				<div class="kw-hcontent">

					<!-- - - - - - - - - - - - - - Navigation - - - - - - - - - - - - - - - - -->

					<nav class="kw-nav-wrap">
						<?php echo Knowhere_Helper::main_navigation(); ?>
					</nav>

					<!-- - - - - - - - - - - - - - End of Navigation - - - - - - - - - - - - - - - - -->

					<?php if ( knowhere_is_shop_installed() ) : ?>

						<?php if ( knowhere_is_realy_woocommerce_page(false) || knowhere_is_shop() || knowhere_is_product_category() || knowhere_is_product() || knowhere_is_product_tax() ) : ?>

							<?php if ( $knowhere_settings['header-type-3-show-link-to-cart'] ): ?>

								<?php
								global $woocommerce;
								$cart_url = wc_get_cart_url();
								?>

								<?php if ( $cart_url ): ?>
									<a class="kw-cart-btn" href="<?php echo esc_url($cart_url) ?>"><span class="lnr icon-cart"></span></a>
								<?php endif; ?>

							<?php endif; ?>

						<?php endif; ?>

					<?php endif; ?>

					<?php if ( $knowhere_settings['header-type-3-show-search'] ): ?>

						<!-- - - - - - - - - - - - - - Search Form - - - - - - - - - - - - - - - - -->

						<button class="kw-search-btn kw-search-box-opener"><span class="lnr icon-magnifier"></span></button>

						<!-- - - - - - - - - - - - - - End of Search Form - - - - - - - - - - - - - - - - -->

					<?php endif; ?>

					<?php if ( class_exists('WP_Job_Manager') ): ?>

						<?php if ( $knowhere_settings['header-type-3-show-button-add-listing'] ):
							$job_form_page_id_url = '';
							$job_form_page_id = get_option( 'job_manager_submit_job_form_page_id', false ) ;

							if ( !empty($job_form_page_id ) ) {
								$job_form_page_id_url = get_permalink($job_form_page_id);
							}
							?>

							<?php if ( !empty($job_form_page_id_url) ): ?>

								<a href="<?php echo esc_url($job_form_page_id_url) ?>" class="kw-btn-medium kw-theme-color-type-2">
									<?php echo knowhere_name_of_listing() ?>
								</a>

							<?php endif; ?>

						<?php endif; ?>
					<?php endif; ?>

				</div><!--/ .kw-hcontent -->

			</div>

		</div><!--/ .kw-sm-table-row.row -->

	</div><!--/ .container -->

</div>