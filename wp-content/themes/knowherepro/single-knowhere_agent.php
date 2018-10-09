<?php
/**
 * The template for displaying agent single post.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package KnowherePro
 */

get_header();
?>

<?php global $post, $knowhere_config;

$skills = knowhere_get_taxonomy_terms( get_the_ID(), 'agent_skills', array( 'type' => 'list' ) );
?>

<?php while ( have_posts() ) : the_post(); ?>

	<div class="kw-agent-box">

		<div class="kw-agent-single">

			<div class="kw-tabs kw-default">

				<ul class="kw-tabs-nav">

					<li><a href="#tab-about"><?php esc_html_e('About', 'knowherepro') ?></a></li>

					<?php if ( !empty($skills) ): ?>
						<li><a href="#tab-skills"><?php esc_html_e('Skills & Experience', 'knowherepro') ?></a></li>
					<?php endif; ?>

				</ul>

				<div class="kw-tabs-container">

					<div id="tab-about" class="kw-tab">
						<?php echo apply_filters( 'the_content', get_the_content() ); ?>
					</div><!--/ .kw-tab-->

					<?php if ( !empty($skills) ): ?>
						<div id="tab-skills" class="kw-tab">

							<h6 class="kw-tab-desc-title"><?php esc_html_e( 'Specialities', 'knowherepro' ) ?></h6>

							<?php echo sprintf( '%s', $skills ); ?>

						</div><!--/ .kw-tab-->
					<?php endif; ?>

				</div><!--/ .kw-tabs-container-->

			</div><!--/ .kw-tabs-->

			<?php if ( function_exists('knowhere_job_single_share') ): ?>
				<?php knowhere_job_single_share(); ?>
			<?php endif; ?>

		</div><!--/ .kw-agent-single-->

		<?php
		$query = Knowhere_Query::loop_agent_properties();

		$classes = apply_filters('knowhere_listings_classes', array(
			'job_listings', 'kw-listings', 'kw-type-3', 'kw-featured-properties', 'owl-carousel'
		));

		$array_title = explode( ' ', get_the_title() );
		$title = $array_title[0];

		ob_start();

		if ( $query->have_posts() ) {

			echo '<h3 class="kw-agent-title">'. $title . '\'s ' . esc_html__('Properties', 'knowherepro') . '</h3>';

			echo '<div data-columns="2" class="' . implode( ' ', $classes ) . '">';
			while ( $query->have_posts() ) { $query->the_post();
				get_template_part( 'job_manager/content', 'property-listing' );
			}
			wp_reset_postdata();
			echo '</div>';

		}

		$output = ob_get_clean();
		echo $output;
		?>

	</div><!--/ .kw-agent-box-->

<?php endwhile; ?>

<?php get_footer(); ?>
