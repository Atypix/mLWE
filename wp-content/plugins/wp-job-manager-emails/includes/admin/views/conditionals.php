<?php
// Exit if accessed directly
	if( ! defined( 'ABSPATH' ) ) exit;
	$fields = is_array( $args ) && isset( $args['fields'] ) && ! empty( $args['fields'] ) ? $args['fields'] : $this->cpt()->shortcodes()->get_conditionals();
	if( empty($fields) ) return;

	$hidden_box = '';
?>
<div class="ui divided selection list sc_conditionals">
	<?php foreach( $fields as $field => $config ): ?>
		<?php
			$placeholder   = isset($config['placeholder']) ? $config['placeholder'] : '';
			$field_label = isset( $config['title'] ) ? $config['title'] : "[{$field}]";
			$popup_label = "<div class='ui fluid top large attached label'>{$field_label}</div>";
			$desc          = isset($config['description']) ? $config['description'] : $placeholder;
			$args_html = $usage_html = $popup_html = '';

			// Usage Output
			if( array_key_exists( 'usage', $config ) && ! empty( $config['usage'] ) ){

				$usage = $config['usage'];

				if( preg_match_all( "@\\[[^\\]]*\\]@", $usage, $usage_shortcodes ) ){

					foreach( $usage_shortcodes[0] as $usage_shortcode ){
						$usage = str_replace( $usage_shortcode, "<span style='color: #E4EB7A;'>{$usage_shortcode}</span>", $usage );
					}

				}

				$usage_html .= "<div class='ui label grey'><pre style='font-weight: normal; margin: 2px; overflow-x: visible; overflow: inherit;'>{$usage}</pre></div>";

				// Opening list DIV
				$popup_html .= "<div class='ui list'>";
				$popup_html .= "<div class='item'><div class='ui header small'>" . __( 'Usage:', 'wp-job-manager-emails' ) . "</div>{$usage_html}</div>";
			}

			// Arguments Output
			if( array_key_exists( 'args', $config ) && ! empty( $config['args'] ) ){

				foreach( (array) $config['args'] as $config_arg => $config_arg_config ){
					$label_req = isset($config_arg_config['required']) && ! empty($config_arg_config['required']) ? 'red' : '';
					$args_html .= "<br /><div class='ui label {$label_req}'>{$config_arg}</div>";

					$arg_val_label = ' <div class=""><em>' . ( ! empty( $label_req ) ? __( 'Required', 'wp-job-manager-emails' ) : __( 'Optional', 'wp-job-manager-emails' ) ) . ' ';
					// Add Required/Optional value:
					if( array_key_exists( 'value', $config_arg_config ) && ! empty( $config_arg_config['value'] ) ) $args_html .= $arg_val_label . __( 'value:', 'wp-job-manager-emails' ) . "</em> {$config_arg_config['value']}</div>";
					// Add required small label above desc
					if ( ! empty( $label_req ) ) $args_html .= "<small style='color: red;'>" . __( 'Required', 'wp-job-manager-emails' ) . '</small>';
					// Add description block
					if( array_key_exists( 'desc', $config_arg_config ) && ! empty($config_arg_config['desc']) ) $args_html .= "<br><small>{$config_arg_config['desc']}</small>";
					$args_html .= '<br/>';
				}

				// Open list DIV if usage HTML did not already do so
				if( empty( $usage_html ) ) $popup_html .= "<div class='ui list'>";

				$popup_html .= "<div class='item'><div class='ui dividing header small'>" . __( 'Arguments:', 'wp-job-manager-emails' ) . "</div>{$args_html}</div>";
			}

			// Close list DIV tag (if required)
			if( ! empty( $args_html ) || ! empty( $usage_html ) ) $popup_html .= '</div>';

			$popup_html = $popup_html . "<div class='ui message compact tiny'>" . ( isset($config['description']) ? $config['description'] : $placeholder ) . '</div>';
			$popup_html = "{$popup_label}<div class='ui content fluid'>{$popup_html}</div>";
			$popup_html = str_replace( '"', "'", $popup_html );
		?>
		<div class="item popover" data-variation="" data-html="<?php echo $popup_html; ?>">
			<div class="ui small label">[<?php echo $field; ?>]</div>
			<div class="content right floated" style="font-size: 80%;">
				<?php _e( $config['label'], 'wp-job-manager-emails' ); ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>

