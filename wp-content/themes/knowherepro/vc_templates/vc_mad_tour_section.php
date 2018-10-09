<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$wrapper_attributes = array();
$title = $tab_id = '';

extract( shortcode_atts( array(
	'title' => '',
	'icon' => 'none',
	'tab_id' => '',
), $atts ) );

global $tabarr;

$tabarr[] = array (
	'title' => $title,
	'tab_id' => $tab_id,
	'content' => $content
);

$wrapper_attributes[] = 'id="tour-' . esc_attr( trim($tab_id) ) . '"';
$wrapper_attributes[] = 'class="kw-tab"';

ob_start(); ?>

<div <?php echo implode( ' ', $wrapper_attributes ) ?>>
	<?php echo wpb_js_remove_wpautop( $content, true ) ?>
</div>

<?php echo ob_get_clean();
