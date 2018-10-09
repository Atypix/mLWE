
<?php global $knowhere_settings; ?>

<!-- - - - - - - - - - - - - - Header Section - - - - - - - - - - - - - - - - -->

<div class="kw-hsection">

	<div class="kw-sm-table-row row">

		<div class="col-sm-3">

			<!-- - - - - - - - - - - - - - Logo - - - - - - - - - - - - - - - - -->

			<?php echo knowhere_logo(); ?>

			<!-- - - - - - - - - - - - - - End of Logo - - - - - - - - - - - - - - - - -->

		</div>

		<div class="col-sm-9">

			<div class="kw-hcontent">

				<?php if ( $knowhere_settings['header-type-2-show-login'] ): ?>
					<?php knowhere_get_login_links(); ?>
				<?php endif; ?>

				<?php if ( class_exists('WP_Job_Manager') ): ?>
					<?php if ( $knowhere_settings['header-type-2-show-button-add-listing'] ):
						$job_form_page_id_url = '';
						$job_form_page_id = get_option( 'job_manager_submit_job_form_page_id', false ) ;

						if ( !empty($job_form_page_id ) ) {
							$job_form_page_id_url = get_permalink($job_form_page_id);
						}
						?>

						<?php if ( !empty($job_form_page_id_url) ): ?>

							<a href="<?php echo esc_url($job_form_page_id_url) ?>" class="kw-btn-medium kw-yellow">
								<?php echo knowhere_name_of_listing() ?>
							</a>

						<?php endif; ?>

					<?php endif; ?>
				<?php endif; ?>

				<!-- - - - - - - - - - - - - - Hidden Aside Invoker - - - - - - - - - - - - - - - - -->

				<button class="kw-hidden-aside-invoker">
					<span class="lnr icon-menu"></span>
				</button>

				<!-- - - - - - - - - - - - - - End of Hidden Aside Invoker - - - - - - - - - - - - - - - - -->

			</div><!--/ .kw-hcontent -->

		</div>

	</div><!--/ .kw-sm-table-row.row -->

</div>

<aside id="hidden-aside" class="kw-hidden-aside kw-moved">

	<button class="kw-hidden-aside-close"><span class="lnr icon-cross"></span></button>

	<div class="kw-widget">
		<nav class="kw-vr-nav-wrap">
			<?php echo Knowhere_Helper::main_navigation('kw-vr-navigation'); ?>
		</nav>
	</div><!--/ .kw-widget -->

</aside>