<?php if ( resume_manager_user_can_view_resume( $post->ID ) ) : ?>

	<?php $content = apply_filters( 'the_resume_description', get_the_content() ); ?>

	<div class="single-resume-content">

		<div class="kw-tabs">

			<ul class="kw-tabs-nav">

				<?php if ( !empty($content) ): ?>
					<li><a href="#tab-resume-about"><?php echo esc_html__('About', 'knowherepro') ?></a></li>
				<?php endif; ?>

				<?php if ( $items = get_post_meta( $post->ID, '_candidate_education', true ) ) : ?>
					<li><a href="#tab-resume-education"><?php echo esc_html__('Education', 'knowherepro') ?></a></li>
				<?php endif; ?>

				<?php if ( $items = get_post_meta( $post->ID, '_candidate_experience', true ) ) : ?>
					<li><a href="#tab-resume-experience"><?php echo esc_html__('Experience', 'knowherepro') ?></a></li>
				<?php endif; ?>

				<?php if ( $items = get_post_meta( $post->ID, '_candidate_awards', true ) ) : ?>
					<li><a href="#tab-resume-awards"><?php echo esc_html__('Awards', 'knowherepro') ?></a></li>
				<?php endif; ?>

				<?php if ( ( $skills = wp_get_object_terms( $post->ID, 'resume_skill', array( 'fields' => 'names' ) ) ) && is_array( $skills ) ) : ?>
					<li><a href="#tab-resume-skills"><?php echo esc_html__('Skills', 'knowherepro') ?></a></li>
				<?php endif; ?>

				<?php if ( get_the_candidate_video() ): ?>
					<li><a href="#tab-resume-video"><?php echo esc_html__('Video', 'knowherepro') ?></a></li>
				<?php endif; ?>

				<?php if ( resume_has_links() || resume_has_file() ) : ?>
					<li><a href="#tab-resume-links"><?php echo esc_html__('Links', 'knowherepro') ?></a></li>
				<?php endif; ?>

			</ul><!--/ .kw-tabs-nav-->

			<div class="kw-tabs-container">

				<?php if ( !empty($content) ): ?>

					<div id="tab-resume-about" class="kw-tab">

						<div class="resume_description">
							<?php echo apply_filters( 'the_resume_description', get_the_content() ); ?>
						</div>

						<ul class="meta">
							<?php do_action( 'single_resume_meta_start' ); ?>
							<?php do_action( 'single_resume_meta_end' ); ?>
						</ul>

					</div><!--/ .kw-tab-->

				<?php endif; ?>

				<?php if ( $items = get_post_meta( $post->ID, '_candidate_education', true ) ) : ?>

					<div id="tab-resume-education" class="kw-tab">

						<dl class="resume-manager-education">

							<?php
							foreach( $items as $item ) : ?>

								<dt>
									<small class="date"><?php echo esc_html( $item['date'] ); ?></small>
									<h3><?php printf( __( '%s at %s', 'knowherepro' ), '<strong class="qualification">' . esc_html( $item['qualification'] ) . '</strong>', '<strong class="location">' . esc_html( $item['location'] ) . '</strong>' ); ?></h3>
								</dt>
								<dd>
									<?php echo wpautop( wptexturize( $item['notes'] ) ); ?>
								</dd>

							<?php endforeach;
							?>

						</dl>

					</div>

				<?php endif; ?>

				<?php if ( $items = get_post_meta( $post->ID, '_candidate_experience', true ) ) : ?>

					<div id="tab-resume-experience" class="kw-tab">

						<dl class="resume-manager-experience">
							<?php
							foreach( $items as $item ) : ?>

								<dt>
									<small class="date"><?php echo esc_html( $item['date'] ); ?></small>
									<h3><?php printf( __( '%s at %s', 'knowherepro' ), '<strong class="job_title">' . esc_html( $item['job_title'] ) . '</strong>', '<strong class="employer">' . esc_html( $item['employer'] ) . '</strong>' ); ?></h3>
								</dt>
								<dd>
									<?php echo wpautop( wptexturize( $item['notes'] ) ); ?>
								</dd>

							<?php endforeach;
							?>
						</dl>

					</div>

				<?php endif; ?>

				<?php if ( $items = get_post_meta( $post->ID, '_candidate_awards', true ) ) : ?>

					<div id="tab-resume-awards" class="kw-tab">

						<dl class="resume-manager-awards">

							<?php
							foreach( $items as $item ) : ?>

								<dt><h6><?php echo esc_html( $item['title'] ); ?></h6></dt>
								<dd><?php echo wpautop( wptexturize( $item['description'] ) ); ?></dd>

							<?php endforeach;
							?>

						</dl>

					</div>

				<?php endif; ?>

				<?php if ( ( $skills = wp_get_object_terms( $post->ID, 'resume_skill', array( 'fields' => 'names' ) ) ) && is_array( $skills ) ) : ?>

					<div id="tab-resume-skills" class="kw-tab">
						<?php echo sprintf( '%s', implode(', ', $skills) ) ?>
					</div>

				<?php endif; ?>

				<?php if ( get_the_candidate_video() ): ?>

					<div id="tab-resume-video" class="kw-tab">
						<?php the_candidate_video(); ?>
					</div>

				<?php endif; ?>

				<?php if ( resume_has_links() || resume_has_file() ) : ?>

					<div id="tab-resume-links" class="kw-tab">

						<ul class="resume-links">
							<?php foreach( get_resume_links() as $link ) : ?>
								<?php get_job_manager_template( 'content-resume-link.php', array( 'post' => $post, 'link' => $link ), 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/' ); ?>
							<?php endforeach; ?>
							<?php if ( resume_has_file() ) : ?>
								<?php get_job_manager_template( 'content-resume-file.php', array( 'post' => $post ), 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/' ); ?>
							<?php endif; ?>
						</ul>

					</div>

				<?php endif; ?>

			</div><!--/ .kw-tabs-container-->

		</div><!--/ .kw-tabs-->

		<?php do_action( 'single_resume_end' ); ?>

	</div><!--/ .single-resume-content-->

<?php else : ?>

	<?php get_job_manager_template_part( 'access-denied', 'single-resume', 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/' ); ?>

<?php endif; ?>