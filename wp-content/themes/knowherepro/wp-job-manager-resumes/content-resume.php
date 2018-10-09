<?php global $post; ?>

<div class="kw-listing-item-wrap <?php echo implode( ' ', get_resume_class() ) ?>">

	<article class="kw-listing-item">

		<!-- - - - - - - - - - - - - - Media - - - - - - - - - - - - - - - - -->

		<div class="kw-listing-item-media">

			<a href="<?php the_resume_permalink(); ?>" class="kw-listing-item-thumbnail">
				<?php the_candidate_photo()  ?>
			</a>

		</div>

		<!-- - - - - - - - - - - - - - End of Media - - - - - - - - - - - - - - - - -->

		<!-- - - - - - - - - - - - - - Description - - - - - - - - - - - - - - - - -->

		<div class="kw-listing-item-info">

			<header class="kw-listing-item-header">
				<?php knowhere_the_job_publish_date() ?>
			</header>

			<div class="kw-listing-item-author-info">

				<a href="<?php the_resume_permalink(); ?>" class="kw-listing-item-name"><?php the_title() ?></a>

				<h3 class="kw-listing-item-title"><?php the_candidate_title() ?></h3>

				<?php $category = get_the_resume_category(); ?>

				<?php if ( $category ) : ?>
					<h6 class="knowhere-listing-resume-category"><?php echo sprintf('%s', $category) ?></h6>
				<?php endif; ?>

			</div>

			<ul class="kw-listing-item-data kw-icons-list">

				<?php if ( $post->_candidate_location ): ?>
					<li><span class="lnr icon-map-marker"></span><?php the_candidate_location(false) ?></li>
				<?php endif; ?>

				<?php knowhere_job_salary($post); ?>

			</ul>

		</div>

		<!-- - - - - - - - - - - - - - End of Description - - - - - - - - - - - - - - - - -->

	</article>

</div>