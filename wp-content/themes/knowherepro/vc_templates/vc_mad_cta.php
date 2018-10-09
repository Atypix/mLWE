<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Shortcode attributes
 * @var $atts
 * @var $content - shortcode content
 * Shortcode class
 */

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
$this->buildTemplate( $atts, $content );
$containerClass = trim( 'kw-call-out ' . esc_attr( implode( ' ', $this->getTemplateVariable( 'container-class' ) ) ) );
$columnLeftClasses = esc_attr( implode( ' ', $this->getTemplateVariable( 'column-left-class' ) ) );
$columnRightClasses = esc_attr( implode( ' ', $this->getTemplateVariable( 'column-right-class' ) ) );
?>
<div class="<?php echo esc_attr( $containerClass ); ?>">

	<div class="kw-sm-table-row">

		<div class="<?php echo esc_attr($columnLeftClasses)  ?>">

			<?php echo $this->getTemplateVariable( 'heading' ); ?>
			<?php echo $this->getTemplateVariable( 'subheading' ); ?>

		</div>

		<div class="<?php echo esc_attr($columnRightClasses)  ?>">

			<?php echo $this->getTemplateVariable( 'kw-actions-button' ); ?>

		</div>

	</div><!--/ .kw-sm-table-row-->
</div><!--/ .kw-call-out-->