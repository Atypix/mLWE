
<?php if ( $apply = get_the_job_application_method() ) : ?>

	<?php if ( 'url' === $apply->type ) : ?>

		<div class="job_application application">
			<a href="<?php echo esc_url( $apply->url ); ?>" class="application_button button kw-btn-medium kw-theme-color" target="_blank" rel="nofollow"><?php esc_html_e('Apply Now', 'knowherepro') ?></a>
		</div>

	<?php else : ?>

		<?php wp_enqueue_script( 'wp-job-manager-job-application' ); ?>

		<?php $id = rand(200, 500); ?>

		<div class="job_application application">

			<a href="#application-popup-<?php echo absint($id) ?>" class="application-button kw-btn-medium kw-theme-color"><?php echo knowhere_name_of_listing(esc_html__( 'Apply for', 'knowherepro' ), true ) ?></a>

			<div id="application-popup-<?php echo absint($id) ?>" class="application-popup mfp-hide">

				<div class="application-details">

					<?php
					/**
					 * job_manager_application_details_email or job_manager_application_details_url hook
					 */
					do_action( 'job_manager_application_details_' . $apply->type, $apply );
					?>

				</div>

			</div><!--/ .application-popup-->

		</div><!--/ .job_application-->

		<?php do_action( 'job_application_start', $apply ); ?>

		<?php do_action( 'job_application_end', $apply ); ?>

	<?php endif; ?>

<?php endif; ?>
