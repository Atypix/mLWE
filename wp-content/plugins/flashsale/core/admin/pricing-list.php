<script type="text/javascript">
	jQuery(document).ready(function(e) {
		jQuery('#pw_matched_rule').click(function(){
			//jQuery("#loading").html('loding...');
			jQuery('#pw_rule_submit').val('<?php _e('Loading...','pw_wc_flash_sale');?>');
			jQuery.ajax ({
				type: "POST",
				url: ajaxurl,
				data:   jQuery('#pw_form_rule').serialize()+ "&action=pw_save_rule_matched",
				
				success: function(data) {
					jQuery('#pw_rule_submit').val('<?php _e('Submit','pw_wc_flash_sale');?>');
					//jQuery("#loading").html('');
					//confirm(data);
				}
			});	
		});		
		jQuery('tr.pw_list_rule_tr')
			.mouseenter(function(){
				var $this=jQuery(this);

				var $url="<?php echo admin_url( 'admin.php?page=rule_list&action=edit');?>"+'&pw_id='+$this.attr('id');
				var $url_del="<?php echo admin_url( 'admin.php?page=rule_list&tab=pricing&action=delete');?>"+'&pw_id='+$this.attr('id');
				
				var $status=$this.attr('data-active-status');
				
				if($status=='active'){
					var $url_active="<?php echo admin_url( 'admin.php?page=rule_list&tab=pricing&action=status&status_type=deactive');?>"+'&pw_id='+$this.attr('id');
					$this.find("td:first").append('<div class="pw_rule_edit_delete"><span><a href="'+$url+'"><?php _e("Edit","pw_wc_flash_sale") ?></a></span>|<span><a href="'+$url_del+'"><?php _e("Delete","pw_wc_flash_sale") ?></a></span>|<span><a href="'+$url_active+'"><?php _e("Deactive","pw_wc_flash_sale") ?></a></span></div>');
				}else{
					var $url_active="<?php echo admin_url( 'admin.php?page=rule_list&tab=pricing&action=status&status_type=active');?>"+'&pw_id='+$this.attr('id');
					$this.find("td:first").append('<div class="pw_rule_edit_delete"><span><a href="'+$url+'"><?php _e("Edit","pw_wc_flash_sale") ?></a></span>|<span><a href="'+$url_del+'"><?php _e("Delete","pw_wc_flash_sale") ?></a></span>|<span><a href="'+$url_active+'"><?php _e("Active","pw_wc_flash_sale") ?></a></span></div>');
				}
			})
			.mouseleave(function(){
				jQuery('.pw_rule_edit_delete').remove();
			});
			
			
			//pw_add_rule_display
			
			if('<?php echo isset($_REQUEST['action'])? $_REQUEST['action']: '';?>'=='edit'){
				jQuery(".pw_rule_loading").show();
				jQuery(".pw-form-cnt").hide();
				
				setTimeout(function(){
					jQuery(".pw_rule_loading").hide();	
					jQuery('.pw_add_rule_display').show();
					jQuery('.pw-form-cnt').show();
				},2000);
			}
			
			jQuery(".pw_add_rule").click(function(e){
				e.preventDefault();
				//jQuery("#pw_create_level_form")[0].reset();
				jQuery("#pw_create_level_form").find('select').find('option').prop('selected', false);
				jQuery("#pw_create_level_form").find('select').not('[multiple]').prop('selectedIndex', 0);
				jQuery("#pw_create_level_form").find(':checkbox').prop('checked', false);
				jQuery("#pw_create_level_form").find('input').val('');
				
				//document.getElementById('pw_create_level_form').reset();
				jQuery("#pw_action_rule").val("add");
				jQuery("#pw_discount_add_btn").val("+");

				jQuery("#pw_action_id").val("");
				jQuery("#pw_tab_rule").val("pricing");
				jQuery("#pw_page_rule").val("rule_list");
				//confirm(jQuery(".pw_page_rule").val());
				setTimeout(function(){
					jQuery('.pw_add_rule_display').show();
					
				},1000);
				
				jQuery("#property_type_thumbnail img").attr("src","");
				
				jQuery(".chosen-select").val('').trigger('chosen:updated');
				jQuery('#pw_create_level_form').find('select').each(function () {
					jQuery(this).trigger("change");
				});
				
				jQuery('#pw_create_level_form').find('.pw_discount_remove_btn').each(function () {
					jQuery(this).parent().parent().remove();
				});
				
			});
			
			
			//jQuery('.kir').load('<?php echo PW_flash_sale_URL.'core/admin/pricing-add-edit.php';?>');
			
	});
</script>
<div class="pw_rule_loading" style="display:none;"><img src="<?php echo plugin_dir_url_flash_sale.'/images/loading.gif'?>" alt="loading"/></div>
<div class="pw-form-cnt" style="<?php echo (isset($_REQUEST['action']) && $_REQUEST['action']=='edit' ? "display:none":"") ?>">
	<div class="pw-form-content">
	<div class="wg-addrule-btn">
	<?php 
		
		echo '<a href="' . admin_url('admin.php?page=rule_list&tab=pricing&action=add') . '" class="nav-tab pw_add_rule">'.__('Add Rule','').'</a>';	
	?>
	</div>
	<div class="pw_add_rule_display" style="display:none;">	
		<?php require( 'pricing-add-edit.php' );		?>
	<?php //echo PW_flash_sale_URL.'core/admin/pricing-add-edit.php';?>
	</div>
	<div style="float: right; margin-bottom:10px;">
		<form id="pw_form_rule">
			<div id="loading"></div>
			<select id="pw_matched_rule" name="pw_matched_rule" class="tr-type-roles">
				<option value="all" <?php selected("all",get_option('pw_matched_rule'),1);?>><?php _e('Apply All matched rules','pw_wc_flash_sale');?></option>
				<option value="big" <?php selected("big",get_option('pw_matched_rule'),1);?>><?php _e('Apply Biggest Discounts','pw_wc_flash_sale');?></option>
			</select>
			<button id="pw_rule_submit" type="button" class="btn button-primary"><?php _e('Submit', 'pw_wc_flash_sale') ?></button>
		</form>
	</div>	
	<table class="wp-list-table widefat fixed posts fs-rolelist-tbl" data-page-size="5" data-page-previous-text = "prev" data-filter-text-only = "true" data-page-next-text = "next" cellspacing="0">
		<thead>
			<tr>
				<th scope='col' data-toggle="true" class='manage-column column-serial_number'  style="">
					<a href="#"><span><?php _e('S.No', 'pw_wc_flash_sale'); ?></span></a>
				</th>
				<th scope='col' class='manage-column'  style=""><?php _e('Rule Name', 'pw_wc_flash_sale'); ?></th>
				<th scope='col' class='manage-column'  style=""><?php _e('List Product', 'pw_wc_flash_sale'); ?></th>
				<th scope='col' class='manage-column'  style=""><?php _e('Rule Type', 'pw_wc_flash_sale'); ?></th>
				<th scope='col' class='manage-column'  style=""><?php _e('From Date', 'pw_wc_flash_sale'); ?></th>				
				<th scope='col' class='manage-column'  style=""><?php _e('To Date', 'pw_wc_flash_sale'); ?></th>				
				<th scope='col' class='manage-column'  style=""><?php _e('Discount', 'pw_wc_flash_sale'); ?></th>
				<th scope="col" class="manage-column" style="width: 165px"><?php _e('Remaining Time', 'pw_wc_flash_sale'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Status', 'pw_wc_flash_sale'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Date Modified', 'pw_wc_flash_sale'); ?></th>
			</tr>
		</thead>
		<tbody id="grid_level_result">
		   <?php
			//$blogtime = current_time( 'mysql' );
			$args=array(
				'post_type'=>'flash_sale',
				'posts_per_page'=>-1,
				'orderby'	=>'modified',
			);
			
			$output='';
			$i=1;
			$setting=get_option("pw_flashsale_discount_options");
			$offset_utc=$setting['pw_woocommerce_flashsale_timezone_countdown']==""?'-8' : $setting['pw_woocommerce_flashsale_timezone_countdown'];
			$loop = new WP_Query( $args );
			while ( $loop->have_posts() ) : 
				$loop->the_post();
				$type=get_post_meta(get_the_ID(),'pw_type',true);
				if($type=="special" || $type=="quantity" || $type=="flashsale")
				{
					$id=$html=$pw_to=$pw_from="";
					$pw_to=get_post_meta(get_the_ID(),'pw_to',true);
					$pw_type=get_post_meta(get_the_ID(),'pw_to',true);
					$pw_from=get_post_meta(get_the_ID(),'pw_from',true);
					$id=rand(0,1000);
					$countdown="style1";
					$fontsize="medium";
					//$offset_utc=get_option('pw_woocommerce_flashsale_timezone_countdown','-8');
					$html='
						<ul id="pw_fl_timer" class="fl-'.$countdown.' fl-'.$fontsize.' fl-countdown fl-countdown-pub countdown_'.$id.'">
						  <li><span class="days">--</span><p class="days_text">'.$setting['Days'].'</p></li>
							<li class="seperator">:</li>
							<li><span class="hours">--</span><p class="hours_text">'.$setting['Hour'].'</p></li>
							<li class="seperator">:</li>
							<li><span class="minutes">--</span><p class="minutes_text">'.$setting['Minutes'].'</p></li>
							<li class="seperator">:</li>
							<li><span class="seconds">--</span><p class="seconds_text">'.$setting['Seconds'].'</p></li>
						</ul>
						<ul id="pw_fl_timer_done" style="display:none">
							<li>Timer is Done</li>
						</ul>
						<script type="text/javascript">
							jQuery(".countdown_'.$id.'").countdown({
								date: "'.$pw_to.'",
								offset: "'.$offset_utc.'",
								day: "Day",
								days: "Days"
							}, function () {
								//jQuery("#pw_fl_timer").remove();
								//jQuery("#pw_fl_timer_done").show();
							//	alert("Done!");
							});
						</script>';
					//$res=strtotime(get_post_meta(get_the_ID(),'pw_to',true))-strtotime(get_post_meta(get_the_ID(),'pw_from',true));
					//$days= floor(($res)/86400);
					//$hours=floor(($res-($days*86400))/3600);
					//$res='Days: '.$days.' H : '.$hours;
					$pw_type=get_post_meta(get_the_ID(),'pw_type',true);
					$show_discount="--";
					if($pw_type=="flashsale")
					{
						$pw_type_discount = get_post_meta( get_the_ID(),'pw_type_discount',true);						
						$pw_discount=get_post_meta(get_the_ID(),'pw_discount',true);
						if($pw_type_discount=="percent")
							$show_discount='-'.$pw_discount.'%';
						else
							$show_discount='-'.wc_price($pw_discount);
					}
					else if($pw_type=="special")
					{
						$pw_type_discount = get_post_meta( get_the_ID(),'adjustment_type',true);						
						$pw_discount=get_post_meta(get_the_ID(),'adjustment_value',true);
						if($pw_type_discount=="percent")
							$show_discount='-'.$pw_discount.'%';
						else
							$show_discount='-'.wc_price($pw_discount);
					}
					$status=get_post_meta(get_the_ID(),'status',true);
					$imgstatus="";
					if($status=="active")
						$imgstatus='<img src="'.plugin_dir_url_flash_sale."images/active.png".'" style="height: 25px;width: 25px;" title="'.__('Active','pw_wc_flash_sale').'" alt="'.__('Active','pw_wc_flash_sale').'"/>';
					else
						$imgstatus='<img src="'.plugin_dir_url_flash_sale."images/deactive.png".'" style="height: 25px;width: 25px;" title="'.__('Active','pw_wc_flash_sale').'" alt="'. __('Deactive','pw_wc_flash_sale').'"/>';
					
					$output.='
					<tr class="pw_list_rule_tr" id="'.get_the_ID().'" data-active-status="'.$status.'">
						<td>'.$i++.'</td>
						<td>'.get_post_meta(get_the_ID(),'pw_name',true).'</td>
						<td><a href="'.admin_url('admin.php?page=rule_list&tab=pricing&action=list_product&pw_id='.get_the_ID()).'">'.__('List Product','pw_wc_flash_sale').'</a></td>
						<td>'.$pw_type.'</td>
						<td>'.get_post_meta(get_the_ID(),'pw_from',true).'</td>
						<td>'.get_post_meta(get_the_ID(),'pw_to',true).'</td>
						<td>'.$show_discount.'</td>
						<td>'.$html.'</td>
						<td>'.$imgstatus.'</td>
						<td>'.get_the_modified_date('F j, Y g:i a').'</td>
					</tr>';
				}							
			endwhile;						
			echo $output;
		   ?>
		</tbody>
	</table>
	</div>
</div>