<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package KnowherePro
 */

get_header();
?>

<?php global $post; ?>

<?php while ( have_posts() ) : the_post(); ?>

	<article <?php if ( defined('RESUME_MANAGER_VERSION') ): ?>  class="<?php echo implode( ' ', get_resume_class() ) ?>" <?php endif; ?>>
		<?php echo apply_filters( 'the_content', get_the_content() ); ?>
	</article>

<?php endwhile; ?>

<?php get_footer(); ?>
