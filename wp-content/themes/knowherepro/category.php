<?php
/**
 * The template for displaying Category pages
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage KnowherePro
 * @since KnowherePro 1.0
 */

get_header(); ?>

<?php if ( have_posts() ) : ?>

	<?php
	global $knowhere_settings;
	$wrapper_attributes = array();

	$css_classes = array(
		'kw-entries', 'kw-blog-default'
	);

	$css_class = preg_replace( '/\s+/', ' ', implode( ' ', array_filter( $css_classes ) ) );
	$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';
	?>

	<div <?php echo implode( ' ', $wrapper_attributes ) ?>>

		<?php
		// Start the loop.
		while ( have_posts() ) : the_post();
			get_template_part( 'template-parts/loop', 'index' );
		endwhile;
		?>

	</div>

	<?php echo knowhere_pagination(); ?>

<?php else:

	// If no content, include the "No posts found" template.
	get_template_part( 'template-parts/content', 'none' );

endif; ?>

<?php get_footer(); ?>