
<?php global $post, $knowhere_settings; ?>

<header class="kw-listing-single-header kw-type-3">

	<div class="container">

		<div class="kw-md-table-row kw-xs-small-offset row">

			<div class="col-sm-9">

				<div class="knowhere-resume-aside">

					<div class="knowhere-candidate-photo">
						<?php the_candidate_photo(); ?>
					</div>

					<div class="knowhere-candidate-meta">

						<h1 class="job-title"><?php the_title(); ?></h1>

						<ul class="kw-resume-item-data">

							<?php if ( $knowhere_settings['job-resume-candidate-title'] ): ?>
								<li class="kw-candidate-title"><?php the_candidate_title() ?></li>
							<?php endif; ?>

							<?php if ( $knowhere_settings['job-resume-candidate-location'] ): ?>
								<?php if ( $post->_candidate_location ): ?>
									<li><span class="lnr icon-map-marker"></span><?php the_candidate_location(false) ?></li>
								<?php endif; ?>
							<?php endif; ?>

							<?php if ( $knowhere_settings['job-resume-category'] ): ?>
								<?php if ( get_the_resume_category() ) : ?>
									<li><span class="lnr icon-folder"></span><?php the_resume_category(); ?></li>
								<?php endif; ?>
							<?php endif; ?>

							<?php if ( $knowhere_settings['job-resume-date'] ): ?>
								<li class="date-posted" itemprop="datePosted">
									<date><span class="lnr icon-clock3"></span><?php printf( __( 'Updated %s ago', 'knowherepro' ), human_time_diff( get_the_modified_time( 'U' ), current_time( 'timestamp' ) ) ); ?></date>
								</li>
							<?php endif; ?>

						</ul>

						<?php get_job_manager_template( 'contact-details.php', array( 'post' => $post ), 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/' ); ?>

						<a href="<?php echo get_resume_file_download_url() ?>" class="knowhere-job-resume-get-file-url"><i class="fa fa-download"></i><?php esc_html_e('Download', 'knowherepro') ?></a>

					</div>

				</div>

			</div>

			<div class="col-sm-3 kw-right-edge">

				<ul class="kw-listing-item-actions kw-icons-list kw-hr-type">
					<li><?php do_action( 'single_resume_start' ); ?></li>
					<li><span class="lnr icon-printer"></span>
						<a href="javascript:window.print()"><?php esc_html_e('Print', 'knowherepro') ?></a>
					</li>
				</ul>

				<div class="kw-hr-btns-group">
					<div class="kw-group-item">

						<?php
						$email   = get_post_meta( $post->ID, '_candidate_email', true );
						$subject = sprintf( __( 'Contact via the resume for "%s" on %s', 'knowherepro' ), single_post_title( '', false ), home_url() );
						?>

						<?php if ( is_email($email) && $subject ): ?>
							<?php printf( '<a class="job_application_email kw-btn-medium kw-theme-color" href="mailto:%1$s%2$s">%3$s</a>', $email, '?subject=' . rawurlencode( $subject ), esc_html__('Contact This Candidate', 'knowherepro') ); ?>
						<?php endif; ?>

					</div>
				</div>

			</div>

		</div>

	</div>

</header>