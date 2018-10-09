<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * e.g., it puts together the home page when no home.php file exists.
 *
 * Learn more: {@link https://codex.wordpress.org/Template_Hierarchy}
 *
 * @package WordPress
 * @subpackage KnowherePro
 * @since KnowherePro 1.0
 */

if (function_exists('get_header')) {
	get_header();
} else {
	die();
}

if ( have_posts() ) : ?>

	<?php
	global $knowhere_settings;
	$wrapper_attributes = array();

	$css_classes = array(
		'kw-entries', 'kw-blog-default', 'facetwp-template'
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