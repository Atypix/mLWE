<?php
/**
 * Template Name: Front Page With Categories
 *
 * @package KnowherePro
 * @since KnowherePro 1.0
 */

get_header();

global $post; ?>

<?php
$has_image = false;
if ( has_post_thumbnail() ) $has_image = true;
?>

<?php
while ( have_posts() ) : the_post(); ?>

	<div class="kw-page-header kw-dark kw-transparent kw-type-7 kw-form-with-border kw-has-image">

		<div class="kw-page-header-content align-center home_mobile">

			<div class="container">

				<h1 class="kw-page-title"><?php the_title(); ?></h1>
				<h4 class="kw-page-tagline"><?php echo get_post_meta( get_the_ID(), 'knowhere_page_subtitle', true ) ?></h4>

				<?php locate_template( array( 'job_manager/job-filters-flat.php' ), true, false ); ?>

				<div class="kw-front-categories">
					<?php knowhere_display_frontpage_listing_categories(); ?>
				</div>

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