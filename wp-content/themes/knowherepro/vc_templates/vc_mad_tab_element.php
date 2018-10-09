<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$title = $tag_title = $description = $title_color = $description_color = '';

extract( shortcode_atts( array(
	'title' => '',
	'description' => ''
), $atts ) );

global $tabarr;
$tabarr = array();

do_shortcode( $content );

ob_start(); ?>

	<div class="wpb_content_element">

		<?php if ( $title ):  ?>
			<h2><?php echo esc_html($title) ?></h2>
		<?php endif; ?>

		<div class="kw-tabs kw-default">

			<ul class="kw-tabs-nav">

				<?php if ( isset($tabarr) && !empty($tabarr) ): ?>

					<?php foreach( $tabarr as $key => $value ): ?>
						<li><a href="#tab-<?php echo esc_attr($value['tab_id']) ?>">
								<?php if (isset($value['icon']) && $value['icon'] != 'none'): ?>
									<span class="<?php echo esc_attr($value['icon']) ?>"></span>
								<?php endif; ?>
							<?php echo esc_html($value['title']) ?></a>
						</li>
					<?php endforeach; ?>

				<?php endif; ?>

			</ul>

			<div class="kw-tabs-container">
				<?php echo wpb_js_remove_wpautop( $content ) ?>
			</div>

		</div>

	</div>

<?php echo ob_get_clean();
