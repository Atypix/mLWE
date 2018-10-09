<?php
/**
 * Template Name: Property Front Page
 *
 * @package KnowherePro
 * @since KnowherePro 1.0
*/

get_header();

global $post, $knowhere_settings;

$gallery = $knowhere_settings['job-gallery-property-front-page'];
$has_images = false;
if ( isset($gallery) && !empty($gallery) ) $has_images = true;

while ( have_posts() ) : the_post(); ?>

	<div class="kw-page-header kw-dark kw-transparent kw-type-4 kw-form-with-border <?php echo ( $has_images ) ? 'kw-has-image' : '' ?>">

		<div class="kw-page-header-content">

			<div class="container">

				<div class="align-center">

					<h1 class="kw-page-title"><?php the_title(); ?></h1>

					<?php locate_template( array( 'job_manager/job-filters-property.php' ), true, false ); ?>

				</div><!--/ .align-center-->

			</div><!--/ .container -->

		</div><!--/ .kw-page-header-content -->

		<?php if ( isset($gallery) && !empty($gallery) ): ?>

			<?php $gallery = explode(',', $gallery) ?>

			<?php if ( is_array($gallery) ): ?>

				<div class="kw-page-header-media">

					<div class="kw-header-media-slider owl-carousel">

						<?php foreach ( $gallery as $id ): ?>
							<?php $src = wp_get_attachment_image_src( $id, 'full' ); ?>

							<?php if ( $src ): ?>
								<?php $src = $src[0]; ?>
								<div class="kw-header-media-item"><img src="<?php echo esc_attr($src) ?>" alt=""></div>
							<?php endif; ?>
						<?php endforeach; ?>

					</div>

				</div><!--/ .kw-page-header-media -->

			<?php endif; ?>

		<?php endif; ?>

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