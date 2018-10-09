<?php
	if( ! defined( 'ABSPATH' ) ) exit;
	$email_format = $this->get_email_format();
	$email_format = empty( $email_format ) ? 'html' : $email_format;
?>
<div class="ui form">
  <div class="inline fields">
    <div class="field">
      <div class="ui radio checkbox">
        <input type="radio" name="email_format" <?php checked( $email_format, 'html', TRUE ); ?> value="html">
        <label><?php _e('HTML', 'wp-job-manager-emails'); ?></label>
      </div>
    </div>

    <div class="field">
      <div class="ui radio checkbox">
        <input type="radio" name="email_format" <?php checked( $email_format, 'plain', TRUE ); ?> value="plain">
        <label><?php _e('Plain Text', 'wp-job-manager-emails'); ?></label>
      </div>
    </div>

  </div>
</div>
<div class="ui divider">
</div>
<div class="ui form">
  <div class="grouped fields">

    <div class="field">
      <div class="ui checkbox">
		<input type="checkbox" id="disable_wpautop" name="disable_wpautop" <?php checked( $this->get_disable_wpautop(), TRUE, TRUE ); ?>>
		<label class="ui mini" for="disable_wpautop"><?php _e( 'Disable wpautop', 'wp-job-manager-emails' ); ?></label>
      </div>
    </div>
  </div>
</div>