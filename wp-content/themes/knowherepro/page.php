<?php
/**
* The template for displaying pages
*
* This is the template that displays all pages by default.
* Please note that this is the WordPress construct of pages and that
* other "pages" on your WordPress site will use a different template.
*
* @package WordPress
* @subpackage KnowherePro
* @since KnowherePro 1.0
*/

get_header(); ?>


<!-- - - - - - - - - - - - - Page - - - - - - - - - - - - - - - -->

<?php if ( have_posts() ) : ?>

	<div class="kw-content-area">

	<?php while ( have_posts() ) : the_post(); ?>

		<?php
		the_content();
		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'knowherepro' ),
			'after'  => '</div>',
		) );
		?>

		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}

	endwhile; ?>

	</div>

<?php endif; ?>

<?php wp_reset_postdata(); ?>

<!-- - - - - - - - - - - - -/ Page - - - - - - - - - - - - - - -->

<?php get_footer();

