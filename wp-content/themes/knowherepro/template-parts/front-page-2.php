<?php
/**
 * Template Name: Job Front Page
 *
 * @package KnowherePro
 * @since KnowherePro 1.0
 */

get_header();

global $post;

$has_image = false;
if ( has_post_thumbnail() ) $has_image = true;

while ( have_posts() ) : the_post(); ?>

	<div class="kw-page-header kw-dark kw-transparent kw-type-6 <?php echo ( $has_image ) ? 'kw-has-image' : '' ?>">

		<div class="kw-page-header-content align-center">

			<div class="container">

				<h1 class="kw-page-title"><?php the_title(); ?></h1>
				<p class="kw-page-caption"><?php echo get_post_meta( get_the_ID(), 'knowhere_page_subtitle', true ) ?></p>

				<?php locate_template( array( 'job_manager/job-filters-flat.php' ), true, false ); ?>

			</div><!--/ .container -->

			<?php knowhere_employers_carousel() ?>

		</div><!--/ .kw-page-header-content -->

		<?php
		$has_image_url = false; ?>

		<?php if ( has_post_thumbnail() ) {
			$has_image_url = get_the_post_thumbnail_url();
		}
		?>

		<div class="kw-page-header-media" <?php if ( ! empty( $has_image_url ) ) {
			echo ' style="background-image: url('. esc_url($has_image_url) .');"';
		} ?>></div><!--/ .kw-page-header-media -->

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