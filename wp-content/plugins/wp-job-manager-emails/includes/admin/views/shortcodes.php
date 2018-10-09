<?php
// Exit if accessed directly
	if( ! defined( 'ABSPATH' ) ) exit;
	$fields = is_array( $args ) && isset( $args['fields'] ) && ! empty( $args['fields'] ) ? $args['fields'] : $this->cpt()->shortcodes()->get_all();
	if( empty($fields) ) return;

	$hidden_box = '';

	// Output json encoded array of shortcodes in JS variable
	$js_fields = json_encode( array_keys( $fields ) );
	echo "<script>var jme_{$metabox['id']} = {$js_fields};</script>";
?>
<div class="ui divided selection list haspopover shortcodes_mb" style="<?php echo $hidden_box; ?>">
	<?php foreach( $fields as $field => $config ): ?>
		<?php
			$placeholder   = isset($config['placeholder']) ? $config['placeholder'] : '';
			$desc          = htmlentities( isset($config['description']) ? $config['description'] : $placeholder );
			$popover_class = ! empty($desc) ? 'popover' : '';
			// Set label color for non-metakey shortcodes
			$label_color   = isset( $config['nonmeta'] ) ? 'basic grey' : '';
			// Set label color for required fields
			if( isset( $config['required'] ) && ! empty( $config['required']) ) $label_color = 'grey';
			$hidden_sc = array_key_exists( 'visible', $config ) && empty( $config[ 'visible' ] ) ? ' shortcode_sb_dynamic' : '';

		?>
		<div class="shortcode_item item <?php echo $popover_class; echo $hidden_sc; ?> <?php echo "shortcode_{$field}"; ?>" data-variation="small" data-content="<?php echo $desc; ?>" data-title="[<?php echo $field; ?>]" data-shortcode="[<?php echo $field; ?>]">
			<div class="ui label <?php echo $label_color; ?>">[<?php echo $field; ?>]</div>
			<div class="content right floated" style="font-size: 80%;">
				<?php _e( $config['label'], 'wp-job-manager-emails' ); ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>

