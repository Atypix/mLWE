
<?php 
if(isset($_POST['pw_flashsale_discount_options']))
{
	update_option( 'pw_flashsale_discount_options', $_POST['pw_flashsale_discount_options'] );
}
$setting=get_option("pw_flashsale_discount_options");

wp_enqueue_style( 'wp-color-picker' );
wp_enqueue_script('wp-color-picker');

?>
<script language="javascript">  
	jQuery(document).ready(function() {
		
		jQuery(".wp_ad_picker_color").wpColorPicker();
	});
</script>
<form id="pw_wc_flash_sale" class="pw_wc_flash_sale" method="POST">	
	<div class="pw-form-cnt">
		<div class="pw-form-content">   
			<div class="pw-section-title"><?php _e('General Setting','pw_wc_flash_sale');?></div>
			<table class="pw-form-table">
				<tbody>
					<tr >
						<th>
							<span><?php _e('Countdown Style', 'pw_wc_flash_sale') ?></span>
							<div class="pw-help-icon" title="<?php _e('Choose Count Down', 'pw_wc_flash_sale') ?>">
								?
							</div>
						</th>
						<td>
							<select name="pw_flashsale_discount_options[pw_woocommerce_flashsale_countdown]" class="tr-type-roles" data-placeholder="Choose...">
								<option value="style1" <?php selected( $setting['pw_woocommerce_flashsale_countdown'], 'style1' ,1) ?>><?php _e('style 1','pw_wc_flash_sale');?></option>
								<option value="style2" <?php selected( $setting['pw_woocommerce_flashsale_countdown'], 'style2' ,1) ?>><?php _e('style 2','pw_wc_flash_sale');?></option>
								<option value="style3" <?php selected( $setting['pw_woocommerce_flashsale_countdown'], 'style3' ,1) ?>><?php _e('style 3','pw_wc_flash_sale');?></option>
                                <option value="style4" <?php selected( $setting['pw_woocommerce_flashsale_countdown'], 'style4' ,1) ?>><?php _e('style 4','pw_wc_flash_sale');?></option>
                                <option value="style5" <?php selected( $setting['pw_woocommerce_flashsale_countdown'], 'style5' ,1) ?>><?php _e('style 5','pw_wc_flash_sale');?></option>
                                <option value="style6" <?php selected( $setting['pw_woocommerce_flashsale_countdown'], 'style6' ,1) ?>><?php _e('style 6','pw_wc_flash_sale');?></option>
							</select>
						</td>
					</tr>	
					<tr >
						<th>
							<span><?php _e('Show Countdown On Single Pages', 'pw_wc_flash_sale') ?></span>
							<div class="pw-help-icon" title="<?php _e('Show Count Down Single', 'pw_wc_flash_sale') ?>">
								?
							</div>
						</th>
						<td>
						   <input type="radio" name="pw_flashsale_discount_options[pw_woocommerce_flashsale_single_countdown]" value="yes" <?php checked( $setting['pw_woocommerce_flashsale_single_countdown'], "yes" ); ?>><?php _e('Yes','pw_wc_flash_sale');?>
							<input type="radio" name="pw_flashsale_discount_options[pw_woocommerce_flashsale_single_countdown]" value="no" <?php if($setting['pw_woocommerce_flashsale_single_countdown']!="yes") echo "checked"; ?>><?php _e('No','pw_wc_flash_sale');?>
						</td>
					</tr>
					<tr >
						<th>
							<span><?php _e('Show Countdown On Archive Pages', 'pw_wc_flash_sale') ?></span>
							<div class="pw-help-icon" title="<?php _e('Show Count Down Archive', 'pw_wc_flash_sale') ?>">
								?
							</div>
						</th>
						<td>
						   <input type="radio" name="pw_flashsale_discount_options[pw_woocommerce_flashsale_archive_countdown]" value="yes" <?php checked( $setting['pw_woocommerce_flashsale_archive_countdown'], "yes" ); ?>><?php _e('Yes','pw_wc_flash_sale');?>
							<input type="radio" name="pw_flashsale_discount_options[pw_woocommerce_flashsale_archive_countdown]" value="no" <?php if($setting['pw_woocommerce_flashsale_archive_countdown']!="yes") echo "checked"; ?>><?php _e('No','pw_wc_flash_sale');?>
						</td>
					</tr>
					<tr >
						<th>
							<span><?php _e('Countdown Text Color', 'pw_wc_flash_sale') ?></span>
							<div class="pw-help-icon" title="<?php _e('The base text color for countdowns. Default<code>#414141</code>', 'pw_wc_flash_sale') ?>">
								?
							</div>
						</th>
						<td>
							<input name="pw_flashsale_discount_options[pw_woocommerce_flashsale_color_countdown]" id="color_set_0" type="text" class="color_set wp_ad_picker_color" value="<?php echo $setting['pw_woocommerce_flashsale_color_countdown'];?>" data-default-color="#414141">						
						</td>
					</tr>
                    <tr >
						<th>
							<span><?php _e('Countdown Back Color', 'pw_wc_flash_sale') ?></span>
							<div class="pw-help-icon" title="<?php _e('The base back color for countdowns. Default<code>#f5f5f5</code>', 'pw_wc_flash_sale') ?>">
								?
							</div>
						</th>
						<td>
							<input name="pw_flashsale_discount_options[pw_woocommerce_flashsale_back_color_countdown]" id="color_set_1" type="text" class="color_set wp_ad_picker_color" value="<?php echo $setting['pw_woocommerce_flashsale_back_color_countdown'];?>" data-default-color="#f5f5f5">						
						</td>
					</tr>
                    
					<tr >
						<th>
							<span><?php _e('Countdown Font Size', 'pw_wc_flash_sale') ?></span>
							<div class="pw-help-icon" title="<?php _e('Font Size For CountDown', 'pw_wc_flash_sale') ?>">
								?
							</div>
						</th>
						<td>
							<select name="pw_flashsale_discount_options[pw_woocommerce_flashsale_fontsize_countdown]" class="tr-type-roles" data-placeholder="Choose...">
								<option value="small" <?php selected( $setting['pw_woocommerce_flashsale_fontsize_countdown'], 'small' ,1) ?>><?php _e('Small','pw_wc_flash_sale');?></option>
								<option value="medium" <?php selected( $setting['pw_woocommerce_flashsale_fontsize_countdown'], 'medium' ,1) ?>><?php _e('Medium','pw_wc_flash_sale');?></option>
								<option value="large" <?php selected( $setting['pw_woocommerce_flashsale_fontsize_countdown'], 'large' ,1) ?>><?php _e('Large','pw_wc_flash_sale');?></option>
							</select>
						</td>
					</tr>
					<tr >
						<th>
							<span><?php _e('Timezone', 'pw_wc_flash_sale') ?></span>
							<div class="pw-help-icon" title="<?php _e('UTC time For CountDown', 'pw_wc_flash_sale') ?>">
								?
							</div>
						</th>
						<td>
							<select name="pw_flashsale_discount_options[pw_woocommerce_flashsale_timezone_countdown]" class="tr-type-roles" data-placeholder="Choose...">
								<option value="-12" <?php selected( $setting['pw_woocommerce_flashsale_timezone_countdown'], '-12' ,1) ?>><?php _e('UTC-12','pw_wc_flash_sale');?></option>
								<option value="-11" <?php selected( $setting['pw_woocommerce_flashsale_timezone_countdown'], '-11' ,1) ?>><?php _e('UTC-11','pw_wc_flash_sale');?></option>
								<option value="-10" <?php selected( $setting['pw_woocommerce_flashsale_timezone_countdown'], '-10' ,1) ?>><?php _e('UTC-10','pw_wc_flash_sale');?></option>
								<option value="-9" <?php selected( $setting['pw_woocommerce_flashsale_timezone_countdown'], '-9' ,1) ?>><?php _e('UTC-9','pw_wc_flash_sale');?></option>
								<option value="-8" <?php selected( $setting['pw_woocommerce_flashsale_timezone_countdown'], '-8' ,1) ?>><?php _e('UTC-8','pw_wc_flash_sale');?></option>
								<option value="-7" <?php selected( $setting['pw_woocommerce_flashsale_timezone_countdown'], '-7' ,1) ?>><?php _e('UTC-7','pw_wc_flash_sale');?></option>
								<option value="-6" <?php selected( $setting['pw_woocommerce_flashsale_timezone_countdown'], '-6' ,1) ?>><?php _e('UTC-6','pw_wc_flash_sale');?></option>
								<option value="-5" <?php selected( $setting['pw_woocommerce_flashsale_timezone_countdown'], '-5' ,1) ?>><?php _e('UTC-5','pw_wc_flash_sale');?></option>
								<option value="-4" <?php selected( $setting['pw_woocommerce_flashsale_timezone_countdown'], '-4' ,1) ?>><?php _e('UTC-4','pw_wc_flash_sale');?></option>
								<option value="-3" <?php selected( $setting['pw_woocommerce_flashsale_timezone_countdown'], '-3' ,1) ?>><?php _e('UTC-3','pw_wc_flash_sale');?></option>
								<option value="-3" <?php selected( $setting['pw_woocommerce_flashsale_timezone_countdown'], '-2' ,1) ?>><?php _e('UTC-2','pw_wc_flash_sale');?></option>
								<option value="-2" <?php selected( $setting['pw_woocommerce_flashsale_timezone_countdown'], '-1' ,1) ?>><?php _e('UTC-1','pw_wc_flash_sale');?></option>
								<option value="0" <?php selected( $setting['pw_woocommerce_flashsale_timezone_countdown'], '0' ,1) ?>><?php _e('UTC+0','pw_wc_flash_sale');?></option>
								<option value="1" <?php selected( $setting['pw_woocommerce_flashsale_timezone_countdown'], '1' ,1) ?>><?php _e('UTC+1','pw_wc_flash_sale');?></option>
								<option value="2" <?php selected( $setting['pw_woocommerce_flashsale_timezone_countdown'], '2' ,1) ?>><?php _e('UTC+2','pw_wc_flash_sale');?></option>
								<option value="3" <?php selected( $setting['pw_woocommerce_flashsale_timezone_countdown'], '3' ,1) ?>><?php _e('UTC+3','pw_wc_flash_sale');?></option>
								<option value="4" <?php selected( $setting['pw_woocommerce_flashsale_timezone_countdown'], '4' ,1) ?>><?php _e('UTC+4','pw_wc_flash_sale');?></option>
								<option value="5" <?php selected( $setting['pw_woocommerce_flashsale_timezone_countdown'], '5' ,1) ?>><?php _e('UTC+5','pw_wc_flash_sale');?></option>
								<option value="6" <?php selected( $setting['pw_woocommerce_flashsale_timezone_countdown'], '6' ,1) ?>><?php _e('UTC+6','pw_wc_flash_sale');?></option>
								<option value="7" <?php selected( $setting['pw_woocommerce_flashsale_timezone_countdown'], '7' ,1) ?>><?php _e('UTC+7','pw_wc_flash_sale');?></option>
								<option value="8" <?php selected( $setting['pw_woocommerce_flashsale_timezone_countdown'], '8' ,1) ?>><?php _e('UTC+8','pw_wc_flash_sale');?></option>
								<option value="9" <?php selected( $setting['pw_woocommerce_flashsale_timezone_countdown'], '9' ,1) ?>><?php _e('UTC+9','pw_wc_flash_sale');?></option>
								<option value="10" <?php selected( $setting['pw_woocommerce_flashsale_timezone_countdown'], '10' ,1) ?>><?php _e('UTC+10','pw_wc_flash_sale');?></option>
								<option value="11" <?php selected( $setting['pw_woocommerce_flashsale_timezone_countdown'], '11' ,1) ?>><?php _e('UTC+11','pw_wc_flash_sale');?></option>
								<option value="12" <?php selected( $setting['pw_woocommerce_flashsale_timezone_countdown'], '12' ,1) ?>><?php _e('UTC+12','pw_wc_flash_sale');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th>
							<?php _e('Days Text', 'pw_wc_flash_sale') ?>
						</th>
						<td>
						   <input type="text" name="pw_flashsale_discount_options[Days]" style="width: 30%;" value="<?php echo @$setting['Days'];?>" />
						</td>
					</tr>
					<tr>
						<th>
							<?php _e('Hour Text', 'pw_wc_flash_sale') ?>
						</th>
						<td>
						   <input type="text" name="pw_flashsale_discount_options[Hour]" style="width: 30%;" value="<?php echo @$setting['Hour'];?>" />
						</td>
					</tr>
					<tr>
						<th>
							<?php _e('Minute Text', 'pw_wc_flash_sale') ?>
						</th>
						<td>
						   <input type="text" name="pw_flashsale_discount_options[Minutes]" style="width: 30%;" value="<?php echo @$setting['Minutes'];?>" />
						</td>
					</tr>
					<tr>
						<th>
							<?php _e('Second Text', 'pw_wc_flash_sale') ?>
						</th>
						<td>
						   <input type="text" name="pw_flashsale_discount_options[Seconds]" style="width: 30%;" value="<?php echo @$setting['Seconds'];?>" />
						</td>
					</tr>
					<tr>
						<th>
							<div class="pw-section-title"><?php _e('Text On Archive Pages','pw_wc_flash_sale');?></div>
						</th>
						<td>
						</td>
					</tr>
					
					<tr>
						<th>
							<?php _e('Day Text', 'pw_wc_flash_sale') ?>
						</th>
						<td>
						   <input type="text" name="pw_flashsale_discount_options[Daysinarchive]" style="width: 30%;" value="<?php echo @$setting['Daysinarchive'];?>" />
						</td>
					</tr>
					<tr>
						<th>
							<?php _e('Hour Text', 'pw_wc_flash_sale') ?>
						</th>
						<td>
						   <input type="text" name="pw_flashsale_discount_options[Hourinarchive]" style="width: 30%;" value="<?php echo @$setting['Hourinarchive'];?>" />
						</td>
					</tr>
					<tr>
						<th>
							<?php _e('Minute Text', 'pw_wc_flash_sale') ?>
						</th>
						<td>
						   <input type="text" name="pw_flashsale_discount_options[Minutesinarchive]" style="width: 30%;" value="<?php echo @$setting['Minutesinarchive'];?>" />
						</td>
					</tr>
					<tr>
						<th>
							<?php _e('Second Text', 'pw_wc_flash_sale') ?>
						</th>
						<td>
						   <input type="text" name="pw_flashsale_discount_options[Secondsinarchive]" style="width: 30%;" value="<?php echo @$setting['Secondsinarchive'];?>" />
						</td>
					</tr>					
					<tr>
						<th>&nbsp;
							
						</th>
						<td>
						   <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'pw_wc_flash_sale') ?>">
						</td>
					</tr>
					
				</tbody>
			</table>
		</div>
	</div>
</form>

