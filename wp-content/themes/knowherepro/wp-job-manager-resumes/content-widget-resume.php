<li <?php resume_class(); ?>>

	<article class="kw-listing-item">

		<!-- - - - - - - - - - - - - - Media - - - - - - - - - - - - - - - - -->

		<div class="kw-listing-item-media">

			<a href="<?php the_resume_permalink($post); ?>" class="kw-listing-item-thumbnail">
				<?php the_candidate_photo()  ?>
			</a>

		</div>

		<!-- - - - - - - - - - - - - - End of Media - - - - - - - - - - - - - - - - -->

		<!-- - - - - - - - - - - - - - Description - - - - - - - - - - - - - - - - -->

		<div class="kw-listing-item-info">

			<header class="kw-listing-item-header">

				<h3 class="kw-listing-item-title">
					<a href="<?php the_resume_permalink(); ?>"><?php the_title(); ?></a>
				</h3>

				<div class="kw-xs-table-row kw-xs-small-offset">

					<div class="col-xs-6">
						<?php the_candidate_title(); ?>
					</div>

				</div>

			</header>

			<?php the_candidate_location( false ); ?>

		</div>

		<!-- - - - - - - - - - - - - - End of Description - - - - - - - - - - - - - - - - -->

	</article>

</li>