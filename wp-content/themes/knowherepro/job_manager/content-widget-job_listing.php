<?php global $post, $knowhere_settings; ?>

<li <?php job_listing_class(); ?>>

	<article class="kw-listing-item">

		<!-- - - - - - - - - - - - - - Media - - - - - - - - - - - - - - - - -->

		<?php knowhere_listing_media_output(array('post' => $post));  ?>

		<!-- - - - - - - - - - - - - - End of Media - - - - - - - - - - - - - - - - -->

		<!-- - - - - - - - - - - - - - Description - - - - - - - - - - - - - - - - -->

		<div class="kw-listing-item-info">

			<header class="kw-listing-item-header">

				<?php if ( $knowhere_settings['job-type-fields'] == 'property' ): ?>

					<div class="kw-xs-table-row kw-xs-small-offset">

						<div class="col-xs-8">
							<h3 class="kw-listing-item-title">
								<a href="<?php the_job_permalink(); ?>">
									<?php echo knowhere_get_invoice_price( get_the_ID() ) ?>
								</a>
							</h3>
						</div>

						<div class="col-xs-4 align_right">
							<?php if ( get_option( 'job_manager_enable_types' ) && $job_type = wpjm_get_the_job_types( $post ) ) : ?>
								<?php knowhere_bg_color_label( $job_type ); ?>
							<?php endif; ?>
						</div>

					</div>

				<?php else: ?>

					<h3 class="kw-listing-item-title">
						<a href="<?php the_job_permalink(); ?>"><?php the_title(); ?></a>
					</h3>

					<h6 class="kw-listing-price">
						<?php echo knowhere_get_invoice_price( get_the_ID() ) ?>
					</h6>

				<?php endif; ?>

				<div class="kw-xs-table-row kw-xs-small-offset">

					<div class="col-xs-6">

						<?php knowhere_job_listing_rating() ?>

						<?php if ( $knowhere_settings['job-type-fields'] != 'property' ): ?>
							<?php if ( get_option( 'job_manager_enable_types' ) && $job_type = wpjm_get_the_job_types( $post ) ) : ?>
								<?php knowhere_bg_color_label( $job_type ); ?>
							<?php endif; ?>
						<?php endif; ?>

					</div>

				</div>

			</header>

			<?php the_job_location( false ); ?>

		</div>

		<!-- - - - - - - - - - - - - - End of Description - - - - - - - - - - - - - - - - -->

	</article>

</li>