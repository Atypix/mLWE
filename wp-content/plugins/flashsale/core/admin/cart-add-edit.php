<?php 
wp_enqueue_media();
?> 
<form id="pw_create_level_form" class="pw_create_level_form" method="POST">	
<div class="pw-form-cnt">
	<div class="pw-form-content">
		<div class="pw-section-title"><?php _e('General Setting','pw_wc_flash_sale');?></div>
		<table class="pw-form-table">
			<tbody>
				<tr >
					<th>
						<span><?php _e('Rule Name', 'pw_wc_flash_sale') ?></span>
						<div class="pw-help-icon" title="<?php _e('Enter Rule name', 'pw_wc_flash_sale') ?>">
							?
						</div>
					</th>
					<td>
						<input type="text" name="pw_name" id="pw_name" value="<?php echo $pw_name;?>" class="require">
					</td>
				</tr>
				
				<tr >
					<th>
						<span><?php _e('Rule Image', 'pw_wc_flash_sale') ?></span>
					</th>
					<td>
					   <div id="property_type_thumbnail" style="float:left;margin-right:10px;">
						<img src="<?php echo $pw_flash_sale_image;?>" width="60px" height="60px" /></div>
						
						<input type="hidden" id="property_type_thumbnail_id" name="pw_flash_sale_image" value="<?php echo $pw_flashsale_image_id;?>" />
						<button type="button" class="upload_image_button button"><?php _e( 'Upload/Add image', 'pw_wc_flash_sale' ); ?></button>
						<button type="button" class="remove_image_button button"><?php _e( 'Remove image', 'pw_wc_flash_sale' ); ?></button>

						<script type="text/javascript">
							 if ( ! jQuery('#property_type_thumbnail_id').val() )
							var file_frame;
							jQuery(document).on( 'click', '.upload_image_button', function( event ){
							
								event.preventDefault();

								// If the media frame already exists, reopen it.
								if ( file_frame ) {
									file_frame.open();
									return;
								}
								// Create the media frame.
								file_frame = wp.media.frames.downloadable_file = wp.media({
									title: '<?php _e( 'Choose an image', 'pw_wc_flash_sale' ); ?>',
									button: {
										text: '<?php _e( 'Use image', 'pw_wc_flash_sale' ); ?>',
									},
									multiple: false
								});
			
								// When an image is selected, run a callback.
								file_frame.on( 'select', function() {
									attachment = file_frame.state().get('selection').first().toJSON();
			
									jQuery('#property_type_thumbnail_id').val( attachment.id );
									jQuery('#property_type_thumbnail img').attr('src', attachment.url );
									jQuery('.remove_image_button').show();
								});
			
								// Finally, open the modal.
								file_frame.open();
							});
			
							jQuery(document).on( 'click', '.remove_image_button', function( event ){
								jQuery('#property_type_thumbnail img').attr('src', '<?php echo ''; ?>');
								jQuery('#property_type_thumbnail_id').val('');
							//	jQuery('.remove_image_button').hide();
								return false;
							});
							
							
						</script> 
					</td>
				</tr>
			  </tbody>
		   </table>
		   <div class="pw-space"></div>
		   <div class="pw-section-title"><?php _e('schedule', 'pw_wc_flash_sale') ?></div>
			<table class="pw-form-table">
			<tbody>     
				<tr >
					<th>
						<span><?php _e('From', 'pw_wc_flash_sale') ?></span>
					</th>
					<td>
						<input type="text" id="date_timepicker_from" name="pw_from" value="<?php echo $pw_from;?>">
						<?php
	/*					$res=strtotime($pw_to)-strtotime($pw_from);
						$days= floor(($res)/86400);
						$hours   =floor(($res-($days*86400))/3600);
						echo 'Days: '.$days.' H : '.$hours;
	*/
						?>
						<script type="text/javascript">
							jQuery(function(){
								jQuery('#date_timepicker_from').datetimepicker();
							});
						</script>
					</td>
				</tr> 
				<tr >
					<th>
						<span><?php _e('To', 'pw_wc_flash_sale') ?></span>
					</th>
					<td>
						<input type="text" id="date_timepicker_to" name="pw_to" value="<?php echo $pw_to;?>">
						<script type="text/javascript">
							jQuery(function(){
								jQuery('#date_timepicker_to').datetimepicker();
							});
						</script>
					</td>
				</tr>
			 </tbody>
		</table>
			<div class="pw-space"></div>
			<div class="pw-section-title"><?php _e('Conditions', 'pw_wc_flash_sale') ?> </div>
			<table class="pw-form-table">
				<tbody>
					<tr>
						<td>
							<span class="pw-inner-title"><?php _e('Condition','pw_wc_flash_sale');?></span>
						</td>
					</tr>
					<tr id="subtotal_least">
						<td>
							<?php _e('Subtotal at least','pw_wc_flash_sale');?>
						</td>
						<td colspan="4">	
							<input type="text" value="<?php echo $pw_condition['subtotal_least'];?>" name="pw_condition[subtotal_least]" placeholder="<?php _e('e.g. 10', 'pw_wc_flash_sale') ?>">
						</td>
						<td>
							<?php _e('And','pw_wc_flash_sale');?>
						</td>
					</tr>	
					<tr id="subtotal_less_than">
						<td>
							<?php _e('Subtotal less than','pw_wc_flash_sale');?>
						</td>
						<td colspan="4">						
							<input type="text" value="<?php echo $pw_condition['subtotal_less_than'];?>" name="pw_condition[subtotal_less_than]" placeholder="<?php _e('e.g. 10', 'pw_wc_flash_sale') ?>">
						</td>
						<td>
							<?php _e('And','pw_wc_flash_sale');?>
						</td>
					</tr>
					<tr id="count_item_least">
						<td>
							<?php _e('Count of cart items at least','pw_wc_flash_sale');?>
						</td>
						<td colspan="4">						
							<input type="text" value="<?php echo $pw_condition['count_item_least'];?>" name="pw_condition[count_item_least]" placeholder="<?php _e('e.g. 10', 'pw_wc_flash_sale') ?>">
						</td>
						<td>
							<?php _e('And','pw_wc_flash_sale');?>
						</td>
					</tr>
					<tr id="count_less_than">
						<td>
							<?php _e('Count of cart items less than','pw_wc_flash_sale');?>
						</td>
						<td colspan="4">						
							<input type="text" value="<?php echo $pw_condition['count_less_than'];?>" name="pw_condition[count_less_than]" placeholder="<?php _e('e.g. 10', 'pw_wc_flash_sale') ?>">
						</td>
						<td>
							<?php _e('And','pw_wc_flash_sale');?>
						</td>
					</tr>					
					<tr id="pw_product">
						<td>
							<?php _e('One Of Product in Cart','pw_wc_flash_sale');?>
						</td>
						<td colspan="4">
							<select name="pw_condition[pw_product][]" class="chosen-select" multiple="multiple" data-placeholder="<?php _e('Choose Product', 'pw_wc_flash_sale') ?> ..." >
							<?php
							$matched_products = get_posts(
								array(
									'post_type' 	=> 'product',
									'numberposts' 	=> -1,
									'post_status' 	=> 'publish',
									'fields' 		=> 'ids',
									'no_found_rows' => true,
									)
								);
							$option_data='';
							foreach($matched_products as $pr)
							{
								$selected='';
								$meta=@$pw_condition['pw_product'];
								if(is_array($meta))
									$selected=(in_array($pr,$meta) ? "SELECTED":"");
								$option_data.='<option '.$selected.' value="'.$pr.'">'.get_the_title( $pr )	.'</option>';
							}
							echo $option_data;
							
							?>
							</select>
						</td>
						<td>
							<?php _e('And','pw_wc_flash_sale');?>
						</td>
					</tr>
					<tr id="pw_except_product">
						<td>
							<?php _e('None of Product in Cart','pw_wc_flash_sale');?>
						</td>
						<td colspan="4">
							<select name="pw_condition[pw_except_product][]" class="chosen-select" multiple="multiple" data-placeholder="<?php _e('Choose Product', 'pw_wc_flash_sale') ?> ..." >
							<?php
							$matched_products = get_posts(
								array(
									'post_type' 	=> 'product',
									'numberposts' 	=> -1,
									'post_status' 	=> 'publish',
									'fields' 		=> 'ids',
									'no_found_rows' => true,
									)
								);
							$option_data='';
							foreach($matched_products as $pr)
							{
								$selected='';
								$meta=@$pw_condition['pw_except_product'];
								if(is_array($meta))
									$selected=(in_array($pr,$meta) ? "SELECTED":"");
								$option_data.='<option '.$selected.' value="'.$pr.'">'.get_the_title( $pr )	.'</option>';
							}
							echo $option_data;
							
							?>
							</select>
						</td>
						<td>
							<?php _e('And','pw_wc_flash_sale');?>
						</td>
					</tr>
					<tr id="pw_product_category">
						<td>
							<?php _e('One Of Categories in Cart','pw_wc_flash_sale');?>
						</td>
						<td colspan="4">
							<?php
								$param_line = '<select name="pw_condition[pw_product_category][]" class="chosen-select" multiple="multiple" data-placeholder="'.__('Choose Category','pw_wc_flash_sale').' ..." >';
								
								global $wpdb;
								$taxonomy="product_cat";
				$sql = "SELECT terms.`term_id`, terms.`name`  FROM `" . $wpdb->prefix . "terms` as terms, `" . $wpdb->prefix . "term_taxonomy` as term_taxonomy WHERE terms.`term_id` = term_taxonomy.`term_id` AND term_taxonomy.`taxonomy` = '" . $taxonomy . "' ORDER BY terms.`term_id` ASC";                       
							$categories = $wpdb->get_results($sql,ARRAY_A) ;								
						
							foreach ($categories as $category) {
								$selected='';
								$meta=@$pw_condition['pw_product_category'];
								if(is_array($meta))
								{
									$selected=(in_array($category['term_id'],$meta) ? "SELECTED":"");
								}
								
								$option = '<option value="'.$category['term_id'].'" '.$selected.'>';
								$option .= $category['name'];
								$option .= '</option>';
								$param_line .= $option;
							}
							$param_line .= '</select>';
							echo $param_line; 

							?>	
						</td>
						<td>
							<?php _e('And','pw_wc_flash_sale');?>
						</td>
					</tr>
					<tr id="pw_except_product_category">>
						<td>
							<?php _e('None of selected Categories in cart','pw_wc_flash_sale');?>
						</td>
						<td colspan="4">
							<?php
								$param_line = '<select name="pw_condition[pw_except_product_category][]" class="chosen-select" multiple="multiple" data-placeholder="'.__('Choose Category','pw_wc_flash_sale').' ..." >';
								
								global $wpdb;
								$taxonomy="product_cat";
								$sql = "SELECT terms.`term_id`, terms.`name`  FROM `" . $wpdb->prefix . "terms` as terms, `" . $wpdb->prefix . "term_taxonomy` as term_taxonomy WHERE terms.`term_id` = term_taxonomy.`term_id` AND term_taxonomy.`taxonomy` = '" . $taxonomy . "' ORDER BY terms.`term_id` ASC";                       
								$categories = $wpdb->get_results($sql,ARRAY_A) ;								
						
								foreach ($categories as $category) {
									$selected='';
									$meta=@$pw_condition['pw_except_product_category'];
									if(is_array($meta))
									{
										$selected=(in_array($category['term_id'],$meta) ? "SELECTED":"");
									}
									
									$option = '<option value="'.$category['term_id'].'" '.$selected.'>';
									$option .= $category['name'];
									$option .= '</option>';
									$param_line .= $option;
								}
								$param_line .= '</select>';
								echo $param_line; 

							?>	
						</td>
						<td>
							<?php _e('And','pw_wc_flash_sale');?>
						</td>
					</tr>
					<tr id="pw_product_tag">
						<td>
							<?php _e('One Of Tag in Cart','pw_wc_flash_sale');?>
						</td>

						<td colspan="4">
							<?php
								$param_line = '<select name="pw_condition[pw_product_tag][]" class="chosen-select" multiple="multiple" data-placeholder="'.__('Choose tags','pw_wc_flash_sale').' ..." >';
								
								$args = array( 'hide_empty=0' );

								$categories = get_terms( 'product_tag', $args);
								//$categories = get_categories($args); 
								foreach ($categories as $category) {
									$selected='';
									//$meta=($pw_level_discount_type=='pw_level_product_category' ? $pw_level_discount_applyto:"");
									//$meta=get_post_meta($category->cat_ID,'pw_product_category',true);
									$meta=@$pw_condition['pw_product_tag'];
									if(is_array($meta))
									{
										$selected=(in_array($category->term_id,$meta) ? "SELECTED":"");
									}
									
									$option = '<option value="'.$category->term_id.'" '.$selected.'>';
									$option .= $category->name;
									$option .= ' ('.$category->count.')';
									$option .= '</option>';
									$param_line .= $option;
								}
								$param_line .= '</select>';
								echo $param_line; 
							?>
						</td>
						<td>
							<?php _e('And','pw_wc_flash_sale');?>
						</td>
					</tr>
					<tr id="pw_except_product_tag">
						<td>
							<?php _e('None of selected tags in cart','pw_wc_flash_sale');?>
						</td>

						<td colspan="4">
							<?php
								$param_line = '<select name="pw_condition[pw_except_product_tag][]" class="chosen-select" multiple="multiple" data-placeholder="'.__('Choose tags','pw_wc_flash_sale').' ..." >';
								
								$args = array( 'hide_empty=0' );

								$categories = get_terms( 'product_tag', $args);
								//$categories = get_categories($args); 
								foreach ($categories as $category) {
									$selected='';
									//$meta=($pw_level_discount_type=='pw_level_product_category' ? $pw_level_discount_applyto:"");
									//$meta=get_post_meta($category->cat_ID,'pw_product_category',true);
									$meta=@$pw_condition['pw_except_product_tag'];
									if(is_array($meta))
									{
										$selected=(in_array($category->term_id,$meta) ? "SELECTED":"");
									}
									
									$option = '<option value="'.$category->term_id.'" '.$selected.'>';
									$option .= $category->name;
									$option .= ' ('.$category->count.')';
									$option .= '</option>';
									$param_line .= $option;
								}
								$param_line .= '</select>';
								echo $param_line; 
							?>
						</td>
						<td>
							<?php _e('And','pw_wc_flash_sale');?>
						</td>
					</tr>	
					<tr id="user_capabilities_in_list">
						<td>
							<?php _e('User Capabilities In List','pw_wc_flash_sale');?>
						</td>
						<td colspan="4">
							<?php
							echo '<select name="pw_condition[user_capabilities_in_list][]" class="chosen-select" multiple="multiple" data-placeholder="Choose capabilities">';
							foreach ( pw_list_capabilities() as $cap ) { 
								$meta=@$pw_condition['user_capabilities_in_list'];
								if(is_array($meta))
								{
									$selected=(in_array($cap ,$meta) ? "SELECTED":"");
								}						
								echo '<option '.$selected.' value="'. $cap.'">'.$cap.'</option>';
							}
							echo '</select>';
							?>
						</td>
						<td>
							<?php _e('And','pw_wc_flash_sale');?>
						</td>
					</tr>
					<tr id="user_role_in_list">
						<td>
							<?php _e('User Role In List','pw_wc_flash_sale');?>
						</td>
						<td colspan="4">
							<?php
							//For Create
							if (!isset($wp_roles)) {
								$wp_roles = new WP_Roles();
							}					
							$all_roles = $wp_roles->roles;
							$chunks = array_chunk($all_roles, ceil(count($all_roles) / 3), true);
							echo '<select name="pw_condition[user_role_in_list][]" class="chosen-select" multiple="multiple" data-placeholder="Choose Roles">';
							foreach ($chunks as $chunk) :					
								foreach ($chunk as $role_id => $role) :
									$meta=@$pw_condition['user_role_in_list'];
									if(is_array($meta))
									{
										$selected=(in_array($role_id ,$meta) ? "SELECTED":"");
									}						
									echo '<option '.$selected.' value="'. $role_id.'">'.$role['name'].'</option>';
								endforeach;
							endforeach;
							echo '</select>';
							?>
						</td>
						<td>
							<?php _e('And','pw_wc_flash_sale');?>
						</td>
					</tr>
					<tr>
						<th>
							<span><?php _e('Discount', 'pw_wc_flash_sale') ?></span>
							<div class="pw-help-icon" title="Description of this field">
								?
							</div>
						</th>
						<td colspan="2">
							<select name="pw_type_conditions" class="td-adjust">
								<option value="percent" <?php selected("percent",$pw_type_conditions,1)?>><?php _e('Percentage discount', 'pw_wc_flash_sale')?></option>
								<option value="price" <?php selected("price",$pw_type_conditions,1)?> ><?php _e('Price discount', 'pw_wc_flash_sale')?></option>
							</select>
						</td>
						<td colspan="2">
							<input type="text" name="pw_discount" value="<?php echo $pw_discount;?>">	
						</td>
					</tr>
					<tr id="save">
						<th>
							<span>&nbsp;</span>
							
						</th>
						<td>
							<input type="hidden" id="pw_type" name="pw_type" value="cart">
							<button type="submit"  class="btn button-primary"><?php _e('Save Changes', 'pw_wc_flash_sale') ?></button>
							<input type="hidden" name="action" id="pw_action_rule" value="<?php echo ($action=='edit' ? $action:"add");?>" />		
							<input type="hidden" name="tab" id="pw_tab_rule" value="pricing" />		
							<input type="hidden" name="page" id="pw_page_rule" value="rule_list" />		
							<input type="hidden" name="id" id="pw_id_rule" value="<?php echo (isset($_REQUEST['pw_id'])?$_REQUEST['pw_id']:''); ?>"  />	
						</td>
					</tr>				
				</tbody>
			</table>	 
	</div>
</div>
</form>
<?php 
/*
$pw_array=array(
		"tr_product"=>array(
			"parent_id"=>array("tr_type_conditions"),
			"tr_type_conditions"=>array(
				"value"=>array("products"),
				"type"=>"select"
			)
		),
		"tr_total"=>array(
			"parent_id"=>array("tr_type_conditions"),
			"tr_type_conditions"=>array(
				"value"=>array("total"),
				"type"=>"select"
			)
		),
	);
	$final_js='';

	foreach($pw_array as $key=>$depends){
		
		$final_js.='jQuery("."+"'.$key.'").dependsOn({';
		
		$parents=$depends['parent_id'];
		foreach($parents as $parent){
			
			$parent_type=$depends[$parent]['type'];
			
			switch ($parent_type){
				case "select":{
					$final_js.= '
						".'.$parent.'": {
								values: [\''.implode("','", $depends[$parent]['value']).'\']
						},';		
				}
				break;
				
				case "checkbox":{
				}
				break;
			}
		}
		
		$final_js.='});
		';
		
	}*/
?>
<script type="text/javascript">
	"use strict";
	jQuery( document ).ready(function( $ ) {
		//confirm('as');
		<?php echo $final_js;?>;
		
	});	
		
</script>
<script language="javascript">  
jQuery(document).ready(function() {
	
		jQuery('.chosen-select').chosen();
	/////////ADD DISCOUNT REPEATABLE//////////
	var row_count=<?php echo isset($row_i) ? $row_i:1 ?>;
	jQuery('#pw_discount_add_btn').click(function(){
		jQuery('<div class="pw_discount_qty"><input type="text" name="pw_discount_qty['+row_count+'][min]" placeholder="Min Quantity"></br><input type="text" name="pw_discount_qty['+row_count+'][max]" placeholder="Max Quantity"></br><input type="text" name="pw_discount_qty['+row_count+'][discount]" placeholder="Discount"><input type="button" class="pw_discount_remove_btn" value="-"></br></div>').insertBefore(this);
		jQuery('.pw_discount_remove_btn').click(function(){
			jQuery(this).parent().remove();
		});
		row_count++;
	});
	
	jQuery('.pw_discount_remove_btn').click(function(){
		jQuery(this).parent().remove();
	});

/*	jQuery('.tr-roles').dependsOn({
		'.tr-type-roles': {
			values: ['roles']
		}
	});
	jQuery('.tr-roles-capabilities').dependsOn({
		'.tr-type-roles': {
			values: ['capabilities']
		}
	});
	
	jQuery('.tr-roles-users').dependsOn({
		'.tr-type-roles': {
			values: ['users']
		}
	});
	
	jQuery('.tr-total').dependsOn({
		'.tr-type-conditions': {
			values: ['total']
		}
	});
	
	jQuery('.tr-product').dependsOn({
		'.tr-type-conditions': {
			values: ['products']
		}
	});
	
	jQuery('.tr-discount').dependsOn({
		'.tr-type-conditions': {
			values: ['products']
		}
	});
	*/

});
</script>