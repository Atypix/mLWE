<?php
// Exit if accessed directly
	if( ! defined( 'ABSPATH' ) ) exit;
	$fields = is_array( $args ) && isset($args['fields']) && ! empty($args['fields']) ? $args['fields'] : $this->cpt()->get_fields();
	if( empty($fields) ) return;

	$file_fields = array_keys( wp_list_filter( $fields, array('type' => 'file') ) );

	$selected = $this->get_attachments() ? maybe_unserialize( $this->get_attachments() ) : array();
	wp_nonce_field( 'jme_get_attachment_fields', 'jme_get_attachment_fields' );
	?>
<div class="ui icon small message attach" style="width: auto;">
	<i class="attach icon"></i>
	<div class="content">
		<div class="header">
			<?php _e( 'Attachments', 'wp-job-manager-emails' ); ?>
		</div>
		<p><?php _e( 'Select any meta keys below that you would like to add as an attachment to the email that is sent.', 'wp-job-manager-emails' ); ?></p>
	</div>
</div>

<div class="field">
	<div id="attachments_dropdown" class="ui fluid search multiple selection dropdown fluid_auto attachments_dropdown">
		<input id="attachments_dropdown_val" type="hidden" name="attachments" value="<?php echo implode( ',', $selected); ?>">
		<i class="dropdown icon"></i>
		<div class="default text"><?php _e('Select a meta key ...', 'wp-job-manager-emails'); ?></div>
		<div id="attachments_menu" class="menu">
			<?php foreach( (array) $file_fields as $field ): ?>
				<div class="attachments_item item" data-value="<?php echo $field; ?>"><?php echo $field; ?></div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
