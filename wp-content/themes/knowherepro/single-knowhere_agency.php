<?php
/**
 * The template for displaying agency single post.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package KnowherePro
 */

get_header(); ?>

<?php global $post;
$agents = Knowhere_Query::loop_agency_agents(); ?>

<div class="kw-agency-box">

	<div class="kw-agency-single">

		<div class="kw-tabs kw-default">

			<ul class="kw-tabs-nav">

				<li><a href="#tab-about"><?php esc_html_e('About', 'knowherepro') ?></a></li>

				<?php if ( $agents->have_posts() ) : ?>
					<li><a href="#tab-agency"><?php esc_html_e('Our Agents', 'knowherepro') ?></a></li>
				<?php endif; ?>

			</ul>

			<div class="kw-tabs-container">

				<div id="tab-about" class="kw-tab">
					<?php echo apply_filters( 'the_content', get_the_content() ); ?>
				</div><!--/ .kw-tab-->

				<?php if ( $agents->have_posts() ) : ?>

					<div id="tab-agency" class="kw-tab">

						<div class="kw-team-members kw-agency-listing">

							<?php while ( $agents->have_posts() ): $agents->the_post(); ?>
								<?php get_template_part( 'job_manager/content', 'our-agents' ); ?>
							<?php endwhile; ?>

						</div><!--/ .kw-team-members-->

						<?php Knowhere_Query::loop_reset(); ?>

					</div><!--/ .kw-tab-->

				<?php endif; ?>

			</div><!--/ .kw-tabs-container-->

		</div><!--/ .kw-tabs-->

		<?php if ( function_exists('knowhere_job_single_share') ): ?>
			<?php knowhere_job_single_share(); ?>
		<?php endif; ?>

	</div><!--/ .kw-agency-single-->

	<?php
	$agency_query = Knowhere_Query::loop_agency_properties();

	$classes = apply_filters('knowhere_listings_classes', array(
		'job_listings', 'kw-listings', 'kw-type-3'
	));

	ob_start();

	if ( $agency_query->have_posts() ) {

		echo '<h3 class="kw-agent-title">'. esc_html__('Our Properties', 'knowherepro') . '</h3>';

		echo '<div class="' . implode( ' ', $classes ) . '">';
		while ( $agency_query->have_posts() ) { $agency_query->the_post();
			get_template_part( 'job_manager/content', 'property-listing' );
		}
		Knowhere_Query::loop_reset();
		echo '</div>';

	}

	$output = ob_get_clean();
	echo $output;
	?>

</div><!--/ .kw-agency-box-->

<?php get_footer(); ?>
