
<?php global $knowhere_settings; ?>

<!-- - - - - - - - - - - - - - Header Section - - - - - - - - - - - - - - - - -->

<div class="kw-hsection kw-sticky">

	<div class="container">

		<div class="row">

			<div class="col-sm-12">

				<div class="kw-md-table-row row">

					<div class="col-md-3">

						<!-- - - - - - - - - - - - - - Logo - - - - - - - - - - - - - - - - -->

						<?php echo knowhere_logo(); ?>

						<!-- - - - - - - - - - - - - - End of Logo - - - - - - - - - - - - - - - - -->

					</div>

					<div class="col-md-9">

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

							<?php if ( defined('ICL_LANGUAGE_CODE') ): ?>
								<?php if ( $knowhere_settings['header-type-5-show-language'] ): ?>
									<ul>
										<?php echo Knowhere_WC_WPML_Config::wpml_header_languages_list(); ?>
									</ul>
								<?php endif; ?>
							<?php endif; ?>

							<?php if ( class_exists('WP_Job_Manager') ): ?>

								<?php if ( $knowhere_settings['header-type-5-show-button-add-listing'] ):
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

			</div>

		</div>

	</div><!--/ .container -->

</div>

<div class="kw-page-in-header kw-type-5">
	<div class="kw-page-header-content">
		<div class="container">
			<div class="kw-listings-search">
				<?php locate_template(array('job_manager/job-filters-classified.php'), true, false); ?>
			</div>
			<!--/ .kw-listings-search-->
		</div>
	</div>
</div>