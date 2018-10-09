<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$title = $tab_id = '';

extract( shortcode_atts( array(
	'title' => '',
	'tab_id' => '',
), $atts ) );

ob_start(); ?>

<dt class="kw-accordion-title"><?php echo esc_attr($title) ?></dt>
<dd class="kw-accordion-definition">
	<div class="kw-accordion-def-inner">
		<?php echo wpb_js_remove_wpautop( $content, true ) ?>
	</div>
</dd>
<?php echo ob_get_clean();
