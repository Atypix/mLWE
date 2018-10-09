<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package KnowherePro
 */

get_header(); ?>

<div class="kw-team-members">

	<?php while ( have_posts() ) : the_post();

		$id = get_the_ID();
		$name = get_the_title();
		$link = get_permalink();
		$address = get_post_meta( $id, 'knowhere_agent_address', true );
		?>

		<figure class="kw-team-member">

			<a href="<?php echo esc_url($link) ?>" class="kw-team-member-photo">
				<?php echo get_the_post_thumbnail( get_the_ID(), 'knowhere-agent-photo' ); ?>
			</a>

			<figcaption class="kw-team-member-info">

				<h4 class="kw-team-member-name"><a href="<?php echo esc_url($link) ?>"><?php echo esc_html($name) ?></a></h4>

				<?php if ( isset($address) && !empty($address) ): ?>
					<div class="kw-team-member-position"><?php echo esc_html($address) ?></div>
				<?php endif; ?>

			</figcaption>

		</figure><!--/ .kw-team-member -->

	<?php endwhile; ?>

</div><!--/ .kw-team-members-->

<?php get_footer(); ?>
