<?php
	if( ! defined( 'ABSPATH' ) ) exit;
	$delay_send_hours = $this->get_delay_send_hours();
	$delay_send_hours = empty( $delay_send_hours ) ? '' : $delay_send_hours;
?>
<div class="ui form">
  <div class="inline fields">
	<div class="field inline">
		<label><?php _e('Delay Sending Email By:', 'wp-job-manager-emails'); ?></label>
		<div class="ui left icon right labeled input">
			<i class="wait icon"></i>
			<input class="" name="delay_send_hours" type="text" placeholder="0" value="<?php echo $delay_send_hours; ?>" style="width: 50%;">
			<div class="ui icon label" data-inverted="" data-tooltip="Total hours to delay sending this email by (blank or 0 means no delay)" data-position="left center">
				<?php _e('Hour(s)', 'wp-job-manager-emails'); ?>
			</div>
		</div>
	</div>
  </div>
	<p>
		<small>
			<?php _e('This is a beta feature, and only recommended for testing on development sites.  Please make sure to test thoroughly.', 'wp-job-manager-emails'); ?>
		</small>
	</p>
</div>