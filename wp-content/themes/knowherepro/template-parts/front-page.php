<?php
/**
 * Template Name: Listing Front Page
 *
 * @package KnowherePro
 * @since KnowherePro 1.0
 */

get_header();

global $post; ?>

<?php while ( have_posts() ) : the_post(); ?>

	<div class="kw-page-header kw-dark kw-transparent kw-type-7 kw-has-image">

		<div class="kw-page-header-content align-center ">

			<div class="container">

				<h1 class="kw-page-title"><?php the_title(); ?></h1>
				<h4 class="kw-page-tagline"><?php echo get_post_meta( get_the_ID(), 'knowhere_page_subtitle', true ) ?></h4>

				<?php global $knowhere_settings; ?>

				<?php if ( $knowhere_settings['job-how-it-works'] && absint($knowhere_settings['job-how-it-works']) ): ?>
					<button class="kw-btn-big kw-white-type-2" data-hidden-container="#how-it-works"><?php esc_html_e('How It Works', 'knowherepro') ?></button>
				<?php endif; ?>

				<?php locate_template( array( 'job_manager/job-filters-flat.php' ), true, false ); ?>

			</div><!--/ .container -->

		</div><!--/ .kw-page-header-content -->

		<?php echo knowhere_frontpage_page_header_bg(); ?>

	</div><!--/ .kw-page-header-->

	<div class="kw-page-content">

		<div class="container">

			<?php if ( $post->post_content ): ?>
				<?php the_content(); ?>
			<?php endif; ?>

		</div><!--/ .container-->

	</div><!--/ .kw-page-content-->

<?php endwhile; ?>

<?php get_footer();