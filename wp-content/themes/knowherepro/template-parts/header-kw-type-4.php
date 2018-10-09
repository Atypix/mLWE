
<?php global $knowhere_settings, $knowhere_config; ?>

<!-- - - - - - - - - - - - - - Header Section - - - - - - - - - - - - - - - - -->

<div class="kw-hsection">

	<div class="container">

		<div class="kw-sm-table-row row">

			<div class="col-sm-3">

				<!-- - - - - - - - - - - - - - Logo - - - - - - - - - - - - - - - - -->

				<?php echo knowhere_logo(); ?>

				<!-- - - - - - - - - - - - - - End of Logo - - - - - - - - - - - - - - - - -->

			</div>

			<div class="col-sm-9">

				<div class="kw-hcontent">

					<div class="kw-two-tier">

						<div class="kw-top-tier">

							<!-- - - - - - - - - - - - - - Login/Register Links - - - - - - - - - - - - - - - - -->

							<?php if ( $knowhere_settings['header-type-4-show-login'] ): ?>
								<?php knowhere_get_login_links(); ?>
							<?php endif; ?>


							<!-- - - - - - - - - - - - - - End of Login/Register Links - - - - - - - - - - - - - - - - -->

							<!-- - - - - - - - - - - - - - List of Languages - - - - - - - - - - - - - - - - -->

							<?php if ( defined('ICL_LANGUAGE_CODE') ): ?>
								<?php if ( $knowhere_settings['header-type-4-show-language'] ): ?>
									<?php echo Knowhere_WC_WPML_Config::wpml_header_languages_list(); ?>
								<?php endif; ?>
							<?php endif; ?>

							<!-- - - - - - - - - - - - - - End of List of Languages - - - - - - - - - - - - - - - - -->

						</div><!--/ .kw-top-tier -->

						<div class="kw-bottom-tier">

							<!-- - - - - - - - - - - - - - Additional Info - - - - - - - - - - - - - - - - -->

							<ul class="kw-icons-list kw-hr-type">

								<?php if ( $knowhere_settings['header-type-4-social-phone']): ?>
									<li>
										<div class="kw-nomber-info">
											<span class="lnr icon-telephone"></span><?php echo esc_html($knowhere_settings['header-type-4-social-phone']) ?>
										</div>
									</li>
								<?php endif; ?>

								<?php if ( $knowhere_settings['header-type-4-social-email']): ?>
									<li>
										<div class="kw-mail-info">
											<a href="mailto:<?php echo antispambot($knowhere_settings['header-type-4-social-email'], 1) ?>">
												<span class="lnr icon-envelope"></span> <?php echo esc_html($knowhere_settings['header-type-4-social-email']); ?>
											</a>
										</div>
									</li>
								<?php endif; ?>

							</ul>

							<!-- - - - - - - - - - - - - - End of Additional Info - - - - - - - - - - - - - - - - -->

						</div><!--/ .kw-bottom-tier -->

					</div><!--/ .kw-two-tier -->

				</div><!--/ .kw-hcontent -->

			</div>

		</div><!--/ .kw-sm-table-row.row -->

	</div><!--/ .container -->

</div><!--/ .kw-hsection-->

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

<div class="kw-bottom-bar <?php echo sanitize_html_class($sticky_class) ?>">

	<div class="container">

		<!-- - - - - - - - - - - - - - Navigation - - - - - - - - - - - - - - - - -->

		<nav class="kw-nav-wrap"><?php echo Knowhere_Helper::main_navigation(); ?></nav>

		<?php if ( class_exists('WP_Job_Manager') ): ?>

			<?php if ( $knowhere_settings['header-type-4-show-button-add-listing'] ):

				$job_form_page_id_url = '';
				$job_form_page_id = get_option( 'job_manager_submit_job_form_page_id', false ) ;

				if ( !empty($job_form_page_id ) ) {
					$job_form_page_id_url = get_permalink($job_form_page_id);
				}
				?>

				<?php if ( !empty($job_form_page_id_url) ): ?>

					<a href="<?php echo esc_url($job_form_page_id_url) ?>" class="kw-bottom-bar-action">
						<span class="lnr icon-plus-square"></span><?php echo knowhere_name_of_listing(esc_html__('Add a', 'knowherepro')) ?>
					</a>

				<?php endif; ?>

			<?php endif; ?>

		<?php endif; ?>

	</div><!--/ .container-->

</div><!--/ .kw-bottom-bar-->