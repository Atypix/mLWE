<?php

/**
 * View for Pricing Rules tab
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
wp_enqueue_media();
?>
<div class="pw-form-cnt">
	
<form id="pw_create_level_form" class="pw_create_level_form" method="POST">		
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
				<tr>
					<th>
						<span><?php _e('Rule Image', 'pw_wc_flash_sale') ?></span>
						<div class="pw-help-icon" title="<?php _e('Description of this field','pw_wc_flash_sale');?>">
							?
						</div>
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
				
				<tr>
					<th>
						<span><?php _e('Type Of Discount', 'pw_wc_flash_sale')?></span>
						<div class="pw-help-icon" title="Description of this field">
							?
						</div>
					</th>
					<td>
						<select name="pw_type" class="pw_tr_type">
							<option value="flashsale" <?php selected("flashsale",$pw_type,1)?>><?php _e('FlashSale', 'pw_wc_flash_sale')?></option>
							<!--<option value="deal" <?php selected("deal",$pw_type,1)?>><?php _e('Deal Of The Day', 'pw_wc_flash_sale')?></option>-->
							<option value="quantity" <?php selected("quantity",$pw_type,1)?> ><?php _e('Quantity', 'pw_wc_flash_sale')?></option>
							<option value="special" <?php selected("special",$pw_type,1)?> ><?php _e('Special Offer', 'pw_wc_flash_sale')?></option>
						</select>
					</td>
				</tr>
				<tr class="tr_quantities_based">
					<th>
						<span><?php _e('Quantities based on','pw_wc_flash_sale');?></span>
						<div class="pw-help-icon" title="Description of this field">
							?
						</div>
					</th>
					<td colspan="4">						
						<select name="quantity_base" class="tr-type">
							<optgroup label="Exclusive">
								<option value="product" <?php selected("product",$quantity_base,1)?>><?php _e('Single Product', 'pw_wc_flash_sale')?></option>
								<!--<option value="variation" <?php selected("variation",$quantity_base,1)?> ><?php _e('Quantities of each variation individually', 'pw_wc_flash_sale')?></option>-->
								<option value="line" <?php selected("line",$quantity_base,1)?> ><?php _e('Item quantity in cart line', 'pw_wc_flash_sale')?></option>
							</optgroup>
							<optgroup label="Cumulative">
								<option value="categories" <?php selected("categories",$quantity_base,1)?> ><?php _e('Quantities of all selected products split by category', 'pw_wc_flash_sale')?></option>
								<option value="all" <?php selected("all",$quantity_base,1)?> ><?php _e('Sum of all products in list or category list', 'pw_wc_flash_sale')?></option>								
							</optgroup>
						</select>
					</td>
				</tr>
				<tr>
					<th>
						<span><?php _e('If conditions are matched', 'pw_wc_flash_sale') ?></span>
						<div class="pw-help-icon" title="Description of this field">
							?
						</div>
					</th>
					<td>
						<select name="pw_matched" class="tr-type-roles">
							<option value="all" <?php selected("all",$pw_matched,1);?>><?php _e('Apply this and other matched rules','pw_wc_flash_sale');?></option>
							<option value="only" <?php selected("only",$pw_matched,1);?>><?php _e('Apply only This rule(disregard other rules)','pw_wc_flash_sale');?></option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="pw-space"></div>
		<div class="pw-section-title"><?php _e('schedule', 'pw_wc_flash_sale') ?></div>
		<table class="pw-form-table">
			<tbody>
				<tr>
					<th>
						<span><?php _e('Valid From/Until', 'pw_wc_flash_sale') ?></span>
						<div class="pw-help-icon" title="Description of this field">
							?
						</div>
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
		<div class="pw-section-title"><?php _e('Conditions', 'pw_wc_flash_sale') ?></div>
		<table class="pw-form-table">
			<tbody>
				<tr class="tr_apply_to">
					<th>
						<span><?php _e('Apply to', 'pw_wc_flash_sale');?></span>
						<div class="pw-help-icon" title="Description of this field">
							?
						</div>
					</th>
					<td colspan="4">
						<select name="pw_apply_to" id="pw_apply_to" class="pw_apply_to">
							<optgroup label="Products">
								<option value="pw_all_product" <?php selected("pw_all_product",$pw_apply_to,1)?>><?php _e('All Product', 'pw_wc_flash_sale');?></option>
								<option value="pw_product" <?php selected("pw_product",$pw_apply_to,1)?>><?php _e('Product in List', 'pw_wc_flash_sale');?></option>
								<option value="pw_except_product" <?php selected("pw_except_product",$pw_apply_to,1)?>><?php _e('Product not in List', 'pw_wc_flash_sale');?></option>
							</optgroup>
							
							<optgroup label="Category">
								<option value="pw_product_category" <?php selected("pw_product_category",$pw_apply_to,1)?> ><?php _e('Category in List', 'pw_wc_flash_sale');?></option>
								<option value="pw_except_product_category" <?php selected("pw_except_product_category",$pw_apply_to,1)?> ><?php _e('Category not in List', 'pw_wc_flash_sale');?></option>
							</optgroup>
							
							<optgroup label="Tag">
								<option value="pw_product_tag" <?php selected("pw_product_tag",$pw_apply_to,1)?> ><?php _e('Tag in List', 'pw_wc_flash_sale');?></option>
								<option value="pw_except_product_tag" <?php selected("pw_except_product_tag",$pw_apply_to,1)?> ><?php _e('Tag not in List', 'pw_wc_flash_sale');?></option>
							</optgroup>
							<?php 
								//if(defined('plugin_dir_url_pw_woo_brand')){
							?>
								<optgroup label="Brand">
									<option value="pw_product_brand" <?php selected("pw_product_brand",$pw_apply_to,1)?> ><?php _e('Brand in List', 'pw_wc_flash_sale');?></option>
									<option value="pw_except_product_brand" <?php selected("pw_except_product_brand",$pw_apply_to,1)?> ><?php _e('Brand not in List', 'pw_wc_flash_sale');?></option>
								</optgroup>						
							<?php
								//}
							?>
						</select>
					</td>
				</tr>
				<tr id="pw_product_category" class="discount_apply_to_pw_product_category">
					<th>
						<span><?php _e('category', 'pw_wc_flash_sale') ?></span>
						
					</th>
					<td colspan="4">
						<?php
						global $wpdb;
						$taxonomy="product_cat";
        $sql = "SELECT terms.`term_id`, terms.`name`  FROM `" . $wpdb->prefix . "terms` as terms, `" . $wpdb->prefix . "term_taxonomy` as term_taxonomy WHERE terms.`term_id` = term_taxonomy.`term_id` AND term_taxonomy.`taxonomy` = '" . $taxonomy . "' ORDER BY terms.`term_id` ASC";                       
								$categories = $wpdb->get_results($sql,ARRAY_A) ;
							
					/*		foreach ($categories as $category) {
								
								$term_id = $category['term_id'];
								
								$output  = '<li id='.$taxonomy.'-'.$term_id.'>' ;
								$output  .= '<label class="selectit  '.$taxonomy.'-list-checkbox">' ;
								$output  .= '<input id="'.$taxonomy.'-'.$term_id.' " ';
								$output  .= 'type="checkbox" name="tax-input-[]" ';
								$output  .= 'value="'.$term_id.'" ';
								$check_found = 'no';
								$output  .= ' />'; //end input statement
								$output  .= '&nbsp;' . $category['name'];
								$output  .= '</label>';            
								$output  .= '</li>';
								  echo $output ;    
							 }						

							*/
						//	$args = array( 'hide_empty=0' );

						//	$categories = get_terms( 'product_cat', $args);
							//$categories = get_categories($args); 
							$param_line = '<select name="pw_product_category[]" class="chosen-select" multiple="multiple" data-placeholder="'.__('Choose Category','pw_wc_flash_sale').' ..." >';							
							foreach ($categories as $category) {
								$selected='';
								//$meta=($pw_level_discount_type=='pw_level_product_category' ? $pw_level_discount_applyto:"");
								//$meta=get_post_meta($category->cat_ID,'pw_product_category',true);
								$meta=$pw_product_category;
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
				</tr>
				<!--excpt category -->
				<tr id="pw_except_product_category" class="discount_apply_to_pw_except_product_category">
					<th>
						<span><?php _e('except category', 'pw_wc_flash_sale') ?></span>
						
					</th>
					<td colspan="4">
						<?php
							$param_line = '<select name="pw_except_product_category[]" class="chosen-select" multiple="multiple" data-placeholder="'.__('Choose Category','pw_wc_flash_sale').' ..." >';
							
							$args = array( 'hide_empty=0' );

							global $wpdb;
							$taxonomy="product_cat";
							$sql = "SELECT terms.`term_id`, terms.`name`  FROM `" . $wpdb->prefix . "terms` as terms, `" . $wpdb->prefix . "term_taxonomy` as term_taxonomy WHERE terms.`term_id` = term_taxonomy.`term_id` AND term_taxonomy.`taxonomy` = '" . $taxonomy . "' ORDER BY terms.`term_id` ASC";                       
							$categories = $wpdb->get_results($sql,ARRAY_A) ;								
						
							foreach ($categories as $category) {
								$selected='';
								$$meta=$pw_except_product_category;
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
				</tr>
				
				<!--Tag -->
				<tr id="pw_product_tag" class="discount_apply_to_pw_product_tag">
					<th>
						<span><?php _e('Tag', 'pw_wc_flash_sale') ?></span>
						
					</th>
					<td colspan="4">
						<?php
							$param_line = '<select name="pw_product_tag[]" class="chosen-select" multiple="multiple" data-placeholder="'.__('Choose Category','pw_wc_flash_sale').' ..." >';
							
							$args = array( 'hide_empty=0' );

							$categories = get_terms( 'product_tag', $args);
							//$categories = get_categories($args); 
							foreach ($categories as $category) {
								$selected='';
								//$meta=($pw_level_discount_type=='pw_level_product_category' ? $pw_level_discount_applyto:"");
								//$meta=get_post_meta($category->cat_ID,'pw_product_category',true);
								$meta=$pw_product_tag;
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
				</tr>
				
				
				<!--Except Tag -->
				<tr id="pw_except_product_tag"  class="discount_apply_to_pw_except_product_tag">
					<th>
						<span><?php _e('except Tag', 'pw_wc_flash_sale') ?></span>
						
					</th>
					<td colspan="4">
						<?php
							$param_line = '<select name="pw_except_product_tag[]" class="chosen-select" multiple="multiple" data-placeholder="'.__('Choose Category','pw_wc_flash_sale').' ..." >';
							
							$args = array( 'hide_empty=0' );

							$categories = get_terms( 'product_tag', $args);
							//$categories = get_categories($args); 
							foreach ($categories as $category) {
								$selected='';
								//$meta=($pw_level_discount_type=='pw_level_product_category' ? $pw_level_discount_applyto:"");
								//$meta=get_post_meta($category->cat_ID,'pw_product_category',true);
								$meta=$pw_except_product_tag;
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
				</tr>
				
				<!--Product -->
				<tr id="pw_product" class="discount_apply_to_pw_product">
					<th>
						<span><?php _e('Product', 'pw_wc_flash_sale') ?></span>
						
					</th>
					<td colspan="4">
						<select name="pw_product[]" class="chosen-select" multiple="multiple" data-placeholder="<?php _e('Choose Product', 'pw_wc_flash_sale') ?> ..." >
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
							$meta=$pw_product;
							if(is_array($meta))
								$selected=(in_array($pr,$meta) ? "SELECTED":"");
							$option_data.='<option '.$selected.' value="'.$pr.'">'.get_the_title( $pr )	.'</option>';
						}
						echo $option_data;
						
						?>
						</select>
					</td>
				</tr>
				
				<!--Except Product -->
				<tr id="pw_except_product" class="discount_apply_to_pw_except_product">
					<th>
						<span><?php _e('Except Product', 'pw_wc_flash_sale') ?></span>
						
					</th>
					<td colspan="4">
						<select name="pw_except_product[]" class="chosen-select" multiple="multiple" data-placeholder="<?php _e('Choose Product', 'pw_wc_flash_sale') ?> ..." >
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
								$meta=$pw_except_product;
								if(is_array($meta))
									$selected=(in_array($pr,$meta) ? "SELECTED":"");
								$option_data.='<option '.$selected.' value="'.$pr.'">'.get_the_title( $pr )	.'</option>';
							}
							echo $option_data;
							
							?>
						</select>
					</td>
				</tr>
				
				<?php 
				if(defined('plugin_dir_url_pw_woo_brand'))
				{
				?>
				<tr id="pw_product_brand" class="discount_apply_to_pw_product_brand">
					<th>
						<span><?php _e('Brand', 'pw_wc_flash_sale') ?></span>
					</th>
					<td colspan="4">
						<?php
							$param_line = '<select name="pw_product_brand[]" class="chosen-select" multiple="multiple" data-placeholder="'.__('Choose Brand','pw_wc_flash_sale').' ..." >';
							$args = array(
								'orderby'           => 'name', 
								'order'             => 'ASC',
								'hide_empty'        => 0,
								'exclude'           => '', 
								'exclude_tree'      => array(), 
								'include'           => array(),
								'number'            => '', 
								'fields'            => 'all', 
								'slug'              => '',
								'name'              => '',
								'parent'            => '',
								'hierarchical'      => true, 
								'child_of'          => 0, 
								'get'               => '', 
								'name__like'        => '',
								'description__like' => '',
								'pad_counts'        => false, 
								'offset'            => '', 
								'search'            => '', 
								'cache_domain'      => 'core'
							);	

							$categories = get_terms( 'product_brand', $args);
							//$categories = get_categories($args); 
							foreach ($categories as $category) {
								$selected='';
								//$meta=($pw_level_discount_type=='pw_level_product_category' ? $pw_level_discount_applyto:"");
								//$meta=get_post_meta($category->cat_ID,'pw_product_category',true);
								$meta=$pw_product_brand;
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
				</tr>
				<tr  id="pw_except_product_brand" class="discount_apply_to_pw_except_product_brand">
					<th>
						<span><?php _e('Except Brand', 'pw_wc_flash_sale') ?></span>
					</th>
					<td colspan="4">
						<?php
							$param_line = '<select name="pw_except_product_brand[]" class="chosen-select" multiple="multiple" data-placeholder="'.__('Choose Brand','pw_wc_flash_sale').' ..." >';
							
							$args = array(
								'orderby'           => 'name', 
								'order'             => 'ASC',
								'hide_empty'        => 0,
								'exclude'           => '', 
								'exclude_tree'      => array(), 
								'include'           => array(),
								'number'            => '', 
								'fields'            => 'all', 
								'slug'              => '',
								'name'              => '',
								'parent'            => '',
								'hierarchical'      => true, 
								'child_of'          => 0, 
								'get'               => '', 
								'name__like'        => '',
								'description__like' => '',
								'pad_counts'        => false, 
								'offset'            => '', 
								'search'            => '', 
								'cache_domain'      => 'core'
							);	


							$categories = get_terms( 'product_brand', $args);
							//$categories = get_categories($args); 
							foreach ($categories as $category) {
								$selected='';
								//$meta=($pw_level_discount_type=='pw_level_product_category' ? $pw_level_discount_applyto:"");
								//$meta=get_post_meta($category->cat_ID,'pw_product_category',true);
								$meta=$pw_except_product_brand;
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
				</tr>
				
				<?php 
				}
				else {
				?>
				<tr id="pw_product_brand" class="discount_apply_to_pw_product_brand">
					<th>
						<span><?php _e('Brands Dependency', 'pw_wc_flash_sale'); ?></span>
						
					</th>
					<td colspan="4">
						<?php _e('Please BUY/activated woocommerce brand', 'pw_wc_flash_sale'); ?> <a href="http://codecanyon.net/item/woocommerce-brands/8039481?ref=proword" >Click for Buy</a>
					</td>
				</tr>
				<tr id="pw_except_product_brand" class="discount_apply_to_pw_except_product_brand">
					<th>
						<span><?php _e('Brands Dependency', 'pw_wc_flash_sale'); ?></span>
						
					</th>
					<td colspan="4">
						<?php _e('Please BUY/activated woocommerce brand', 'pw_wc_flash_sale'); ?> <a href="http://codecanyon.net/item/woocommerce-brands/8039481?ref=proword" >Click for Buy</a>
					</td>
				</tr>
				<?php 
				}
				?>
				<tr class="tr-applie-to-roles">
					<th>
						<span><?php _e('Customer','pw_wc_flash_sale');?></span>
						<div class="pw-help-icon" title="Description of this field">
							?
						</div>
					</th>
					<td colspan="4">
						<select name="pw_cart_customer" class="pw_cart_customer" data-placeholder="Choose..." id="pw_cart_roles">
							<option value="everyone"><?php _e('Everyone','pw_wc_flash_sale');?></option>
							<option value="roles"><?php _e('Specific Roles in','pw_wc_flash_sale');?></option>
							<option value="capabilities"><?php _e('Specific Capabilities in','pw_wc_flash_sale');?></option>
							<option value="users"><?php _e('Specific users in','pw_wc_flash_sale');?></option>
						</select>
					</td>
				</tr>
				<tr class="pw_cart_roles" id="roles">
					<th>
						<span><?php _e('Roles','pw_wc_flash_sale');?></span>
						<div class="pw-help-icon" title="Description of this field">
							?
						</div>
					</th>
					<td colspan="4">
						<?php
							//For Create
							if (!isset($wp_roles)) {
								$wp_roles = new WP_Roles();
							}					
							$all_roles = $wp_roles->roles;
							$chunks = array_chunk($all_roles, ceil(count($all_roles) / 3), true);
							echo '<select name="pw_roles[]" class="chosen-select" multiple="multiple" data-placeholder="Choose Roles">';
							foreach ($chunks as $chunk) :					
								foreach ($chunk as $role_id => $role) :
									$meta=$pw_roles;
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
				</tr>
				<tr class="pw_cart_capabilities" id="capabilities">
					<th>
						<span><?php _e('capabilities','pw_wc_flash_sale');?></span>
						<div class="pw-help-icon" title="Description of this field">
							?
						</div>
					</th>
					<td colspan="4">
						<?php
							echo '<select name="pw_capabilities[]" class="chosen-select" multiple="multiple" data-placeholder="Choose capabilities">';
							foreach ( pw_list_capabilities() as $cap ) { 
								$meta=$pw_capabilities;
								if(is_array($meta))
								{
									$selected=(in_array($cap ,$meta) ? "SELECTED":"");
								}						
								echo '<option '.$selected.' value="'. $cap.'">'.$cap.'</option>';
							}
							echo '</select>';
						?>
					</td>
				</tr>
				<tr  class="pw_cart_users" id="users">
					<th>
						<span><?php _e('Users','pw_wc_flash_sale');?></span>
						<div class="pw-help-icon" title="Description of this field">
							?
						</div>
					</th>
					<td colspan="4">
						<?php
							echo '<select name="pw_users[]" class="chosen-select" multiple="multiple" data-placeholder="Choose Users">';
							foreach(get_users() as $user) {
								$meta=$pw_users;
								$selected="";
								if(is_array($meta))
								{
									$selected=(in_array($user->ID ,$meta) ? "SELECTED":"");
								}
								echo '<option '.$selected.' value="'. $user->ID .'">ID:'.$user->ID.' '.$user->user_email.'</option>';
							}
							echo '</select>';
						?>
					</td>
				</tr>
				<tr class="tr_flashsale">
					<th>
						<span><?php _e('Discount', 'pw_wc_flash_sale') ?></span>
						<div class="pw-help-icon" title="Description of this field">
							?
						</div>
					</th>
					<td colspan="2">
						<select name="pw_type_discount" class="td-adjust">
							<option value="percent" <?php selected("percent",$pw_type_discount,1)?>><?php _e('Percentage discount', 'pw_wc_flash_sale')?></option>
							<option value="price" <?php selected("price",$pw_type_discount,1)?> ><?php _e('Price discount', 'pw_wc_flash_sale')?></option>
						</select>
					</td>
					<td colspan="2">
						
						<input type="text" id="datepicker" name="pw_discount" value="<?php echo $pw_discount;?>">	
					</td>
				</tr>
			</tbody>
		</table>
		
		<div class="tr_special">
			<div class="pw-space"></div>
			<div class="pw-section-title"><?php _e('Special Offer', 'pw_wc_flash_sale') ?></div>
					
			<table class="pw-form-table">
				<tbody>
					<tr>
						<th>
							<span><?php _e('Amount to purchase', 'pw_wc_flash_sale') ?></span>
							<div class="pw-help-icon" title="Description of this field">
								?
							</div>
						</th>
						<td colspan="4">
							<input type="text" id="datepicker" name="amount_to_purchase" value="<?php echo $amount_to_purchase;?>">
						</td>
					</tr>
					<tr >
						<th>
							<span><?php _e('Amount to adjust', 'pw_wc_flash_sale') ?></span>
							<div class="pw-help-icon" title="Description of this field">
								?
							</div>
						</th>
						<td colspan="4">
							<input type="text" id="datepicker" name="amount_to_adjust" value="<?php echo $amount_to_adjust;?>">
						</td>
					</tr>
					<tr >
						<th>
							<span><?php _e('Adjustment Type/value', 'pw_wc_flash_sale') ?></span>
							<div class="pw-help-icon" title="Description of this field">
								?
							</div>
						</th>
						<td colspan="2">
							<select name="adjustment_type" class="td-adjust">
								<option value="percent" <?php selected("percent",$adjustment_type,1)?>><?php _e('Percentage discount', 'pw_wc_flash_sale')?></option>
								<option value="price" <?php selected("price",$adjustment_type,1)?> ><?php _e('Price discount', 'pw_wc_flash_sale')?></option>
							</select>
						</td>
						<td colspan="2">
							<input type="text" id="datepicker" name="adjustment_value" value="<?php echo $adjustment_value;?>">
						</td>
					</tr>
					<tr >
						<th>
							<span><?php _e('Repeat', 'pw_wc_flash_sale') ?></span>
							<div class="pw-help-icon" title="Description of this field">
								?
							</div>
						</th>
						<td colspan="4">
						
							   <input type="checkbox" name="repeat" value="true" <?php echo ($repeat ? 'checked="checked"' : ''); ?>>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="tr_quantity">
			<div class="pw-space"></div>
			<div class="pw-section-title"><?php _e('Quantity Discount', 'pw_wc_flash_sale') ?></div>
			<table class="pw-form-table">
				<tbody>
					<tr>
						
						<td>
							<span class="pw-inner-title"><?php _e('Min Quantity','pw_wc_flash_sale');?></span>
						</td>
						<td>
							<span class="pw-inner-title"><?php _e('Max Quantity','pw_wc_flash_sale');?></span>
						</td>
						<td>
							<span class="pw-inner-title"><?php _e('Adjustment type','pw_wc_flash_sale');?></span>
						</td>
						<td>
							<span class="pw-inner-title"><?php _e('Discount','pw_wc_flash_sale');?></span>
						</td>
						<td>
							<span class="pw-inner-title">&nbsp;</span>
						</td>
						
					</tr>
					
						
					<?php
							if(isset($pw_discount_qty) && is_array($pw_discount_qty))
							{
								$row_i=0;
								foreach($pw_discount_qty as $discount_qty){
									$remove_btn='';
									if($row_i>0)
									{
										$remove_btn='<input type="button" class="pw_discount_remove_btn" value="-">';
									}
									echo '
										<tr id="pw_discount_qty_repeatable" class="pw_discount_qty">
											<td >
												<input type="text" name="pw_discount_qty['.$row_i.'][min]" placeholder="Min Quantity" value="'.@$discount_qty['min'].'"></br>
											</td>
											<td>
												<input type="text" name="pw_discount_qty['.$row_i.'][max]" placeholder="Max Quantity" value="'.@$discount_qty['max'].'"></br>
											</td>
											<td>
												<select name="pw_discount_qty['.$row_i.'][type]" class="td-adjust" >
													<option value="percent" '.selected("percent",@$pw_discount_qty['type'],1).' >'. __("Percentage discount", "pw_wc_flash_sale").'</option>
													<option value="price"'.selected("price",@$pw_discount_qty['type'],1).'>'. __("Price discount", "pw_wc_flash_sale").'</option>
												</select>
											</td>
											<td>
												<input type="text" name="pw_discount_qty['.$row_i.'][discount]" placeholder="Discount" value="'.@$discount_qty['discount'].'">'.$remove_btn.'</br>
											</td>
											<td>
												'.$remove_btn.'
											</td>
										</tr>
									';	
									$row_i++;
								}
							}else
							{
						?>
								<tr id="pw_discount_qty_repeatable" class="pw_discount_qty">
									<td>
										<input type="text" name="pw_discount_qty[0][min]" placeholder="<?php _e('Min Quantity', 'pw_wc_flash_sale') ?>">
									</td>
									<td>
										<input type="text" name="pw_discount_qty[0][max]" placeholder="<?php _e('Max Quantity', 'pw_wc_flash_sale') ?>">
									</td>
									<td>
										<select name="pw_discount_qty[0][type]" class="td-adjust" >
											<option value="percent"><?php _e('Percentage discount', 'pw_wc_flash_sale')?></option>
											<option value="price"><?php _e('Price discount', 'pw_wc_flash_sale')?></option>
										</select>
									</td>
									<td>
										<input type="text" name="pw_discount_qty[0][discount]" placeholder="<?php _e('Discount', 'pw_wc_flash_sale') ?>">
									</td>
									<td>
										
									</td>
								</tr>
						<?php
							}
						?>
					<tr>
						<td colspan="4" id="pw_discount_add">
							<input type="button" id="pw_discount_add_btn" value="+">
						</td>
						
					</tr>
					
				</tbody>
			</table>
		</div>
		<div class="tr_adjust">		
			<table class="pw-form-table">
				<tbody>					
					<tr class="tr-adjust">
						<th>
							<span><?php _e('Products to adjust', 'pw_wc_flash_sale')?></span>
							<div class="pw-help-icon" title="Description of this field">
								?
							</div>
						</th>
						<td colspan="4">
							<select name="pw_products_to_adjust" class="pw_product_to_adjust">
								<option value="matched" <?php selected("matched",$pw_products_to_adjust,1)?>><?php _e('Same products (selected above)', 'pw_wc_flash_sale')?></option>
								<option value="other_categories" <?php selected("other_categories",$pw_products_to_adjust,1)?> ><?php _e('Specific categories', 'pw_wc_flash_sale')?></option>
								<option value="other_products" <?php selected("other_products",$pw_products_to_adjust,1)?> ><?php _e('Specific products', 'pw_wc_flash_sale')?></option>
							</select>
						</td>
					</tr>
					
					<tr id="pw_products_to_adjust_products" class="products_to_adjust_products">
						<th>
							<span><?php _e('Product List', 'pw_wc_flash_sale') ?></span>
							<div class="pw-help-icon" title="Description of this field">
								?
							</div>
						</th>
						<td colspan="4">
							<select name="pw_products_to_adjust_products[]" class="chosen-select" multiple="multiple" data-placeholder="<?php _e('Choose Product', 'pw_wc_flash_sale') ?> ..." >
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
									$meta=$pw_products_to_adjust_products;
									if(is_array($meta))
										$selected=(in_array($pr,$meta) ? "SELECTED":"");
									$option_data.='<option '.$selected.' value="'.$pr.'">'.get_the_title( $pr )	.'</option>';
								}
								echo $option_data;						
								
								?>
							</select>
						</td>
					</tr>
					
					<tr id="pw_products_to_adjust_category" class="pw_products_to_adjust_category">
						<th>
							<span><?php _e('Category List', 'pw_wc_flash_sale') ?></span>
							<div class="pw-help-icon" title="Description of this field">
								?
							</div>
						</th>
						<td colspan="4">
							<?php
								$param_line = '<select name="pw_products_to_adjust_category[]" class="chosen-select" multiple="multiple" data-placeholder="'.__('Choose Category','pw_wc_flash_sale').' ..." >';
								$args = array( 'hide_empty=0' );
								$categories = get_terms( 'product_cat', $args);
								//$categories = get_categories($args); 
								foreach ($categories as $category) {
									$selected='';
									//$meta=($pw_level_discount_type=='pw_level_product_category' ? $pw_level_discount_applyto:"");
									//$meta=get_post_meta($category->cat_ID,'pw_product_category',true);
									$meta=$pw_products_to_adjust_category;
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
					</tr>	
				</tbody>
			</table>
		</div>
		<table class="pw-form-table">
			<tbody>
				<tr>
                	<th>
						&nbsp;
					</th>
					<td colspan="5">
						<button type="submit"  class="btn button-primary"><?php _e('Save Changes', 'pw_wc_flash_sale') ?></button>
						<input type="hidden" name="action" id="pw_action_rule" value="<?php echo ($action=='edit' ? $action:"add");?>" />		
						<input type="hidden" name="tab" id="pw_tab_rule" value="pricing" />		
						<input type="hidden" name="page" id="pw_page_rule" value="rule_list" />		
						<input type="hidden" name="id" id="pw_id_rule" value="<?php echo (isset($_REQUEST['pw_id'])?$_REQUEST['pw_id']:''); ?>"  />		
					</td>
				</tr>
			</tbody>
		</table>
	</div><!--pw-form-content -->
</div><!--pw-form-cnt -->
</form>
<script language="javascript">  

jQuery(document).ready(function() {
	/////////ADD DISCOUNT REPEATABLE//////////
	var row_count=<?php echo isset($row_i) ? $row_i:1 ?>;
	jQuery('#pw_discount_add_btn').click(function(){
		jQuery('<tr id="pw_discount_qty_repeatable" class="pw_discount_qty"><td ><input type="text" name="pw_discount_qty['+row_count+'][min]" placeholder="Min Quantity"></td><td><input type="text" name="pw_discount_qty['+row_count+'][max]" placeholder="Max Quantity"></td><td><select name="pw_discount_qty['+row_count+'][type]" class="td-adjust" ><option value="percent">Percentage discount</option><option value="price">Price discount</option></select></td><td><input type="text" name="pw_discount_qty['+row_count+'][discount]" placeholder="Discount"></td><td><input type="button" class="pw_discount_remove_btn" value="-"></td></tr>').insertBefore(jQuery(this).parent().parent());
		jQuery('.pw_discount_remove_btn').click(function(){
			jQuery(this).parent().parent().remove();
		});
		row_count++;
	});
	
	jQuery('.pw_discount_remove_btn').click(function(){
		jQuery(this).parent().parent().remove();
	});
	/////////END ADD DISCOUNT REPEATABLE//////////
	
	/*
	//////////APPLY TO/////////////
	jQuery('#pw_apply_to').change(function(){
		var $id=jQuery(this).val();		
		if($id!='pw_all_product')
		{
			jQuery('.discount_apply_to').each(function(){
				jQuery(this).hide();
			});

			jQuery("#"+$id).show();
		}else{
			jQuery('.discount_apply_to').each(function(){
				jQuery(this).hide();
			});
		}
	});
	jQuery('#pw_apply_to').val("<?php echo isset($pw_apply_to) ? ($pw_apply_to=='' ? 'pw_all_product':$pw_apply_to):'pw_all_product';?>").change();
	*/
	//////////END APPLY TO//////////////
/*
	//////////APPLY TO/////////////
	jQuery('#pw_cart_roles').change(function(){
		var $id=jQuery(this).val();		
		if($id!='everyone')
		{
			jQuery('.pw_cart_roles').each(function(){
				jQuery(this).hide();
			});

			jQuery("#"+$id).show();
		}else{
			jQuery('.pw_cart_roles').each(function(){
				jQuery(this).hide();
			});
		}
	});
	jQuery('#pw_cart_roles').val("<?php echo isset($pw_cart_roles) ? ($pw_cart_roles=='' ? 'everyone':$pw_cart_roles):'everyone';?>").change();
	//////////END APPLY TO//////////////
	*/
	
	jQuery('.chosen-select').chosen();
	
});

</script>
<?php
require( 'dependency.php' );
?>