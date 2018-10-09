<script type="text/javascript">
	jQuery(document).ready(function(e) {
		jQuery('#pw_cart_submit').click(function(){
			//jQuery("#loading").html('loding...');
			jQuery('#pw_cart_submit').val('<?php _e('Loading...','pw_wc_flash_sale');?>');
			jQuery.ajax ({
				type: "POST",
				url: ajaxurl,
				data:   jQuery('#pw_form_cart').serialize()+ "&action=pw_save_cart_matched",
				
				success: function(data) {
					jQuery('#pw_cart_submit').val('<?php _e('Submit','pw_wc_flash_sale');?>');
					//jQuery("#loading").html('');
					//confirm(data);
				}
			});	
		});
        jQuery('tr.pw_list_rule_tr')
			.mouseenter(function(){
				var $this=jQuery(this);

				var $url="<?php echo admin_url( 'admin.php?page=rule_list&tab=cart&action=edit');?>"+'&pw_id='+$this.attr('id');
				var $url_del="<?php echo admin_url( 'admin.php?page=rule_list&tab=cart&action=delete');?>"+'&pw_id='+$this.attr('id');
				
				var $status=$this.attr('data-active-status');
				
				if($status=='active'){
					var $url_active="<?php echo admin_url( 'admin.php?page=rule_list&tab=cart&action=status&status_type=deactive');?>"+'&pw_id='+$this.attr('id');
					$this.find("td:first").append('<div class="pw_rule_edit_delete"><span><a href="'+$url+'"><?php _e("Edit","pw_wc_flash_sale") ?></a></span>|<span><a href="'+$url_del+'"><?php _e("Delete","pw_wc_flash_sale") ?></a></span>|<span><a href="'+$url_active+'"><?php _e("Deactive","pw_wc_flash_sale") ?></a></span></div>');
				}else{
					var $url_active="<?php echo admin_url( 'admin.php?page=rule_list&tab=cart&action=status&status_type=active');?>"+'&pw_id='+$this.attr('id');
					$this.find("td:first").append('<div class="pw_rule_edit_delete"><span><a href="'+$url+'"><?php _e("Edit","pw_wc_flash_sale") ?></a></span>|<span><a href="'+$url_del+'"><?php _e("Delete","pw_wc_flash_sale") ?></a></span>|<span><a href="'+$url_active+'"><?php _e("Active","pw_wc_flash_sale") ?></a></span></div>');
				}
				
				
				
				
			})
			.mouseleave(function(){
				jQuery('.pw_rule_edit_delete').remove();
			});

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
				jQuery("#pw_tab_rule").val("cart");
				jQuery("#pw_page_rule").val("rule_list");
				jQuery("#pw_type").val("cart");
				jQuery(".chosen-select").val('').trigger('chosen:updated');
				jQuery('#pw_create_level_form').find('select').each(function () {
					jQuery(this).trigger("change");
				});
				
				jQuery('#pw_create_level_form').find('.pw_discount_remove_btn').each(function () {
					jQuery(this).parent().parent().remove();
				});
				
				jQuery("#property_type_thumbnail img").attr("src","");
				
				//confirm(jQuery(".pw_page_rule").val());
				setTimeout(function(){
					jQuery('.pw_add_rule_display').show();
					
				},1000);
				
				
			});			
    });
</script>
<div class="pw_rule_loading" style="display:none;"><img src="<?php echo plugin_dir_url_flash_sale.'/images/loading.gif'?>" alt="loading"/></div>
<div class="pw-form-cnt" style="<?php echo (isset($_REQUEST['action']) && $_REQUEST['action']=='edit' ? "display:none":"") ?>">
<div class="pw-form-content">
	<div class="wg-addrule-btn">
	<?php
		echo '<a href="' . admin_url('admin.php?page=rule_list&tab=cart&action=add') . '" class="nav-tab pw_add_rule">'.__('Add Rule Discounts','').'</a>';
	?>

    </div>
	<div class="pw_add_rule_display" style="display:none;">	
		<?php require( 'cart-add-edit.php' );		?>
	<?php //echo PW_flash_sale_URL.'core/admin/pricing-add-edit.php';?>
	</div>	
	<div style="float: right; margin-bottom:10px;">
		<form id="pw_form_cart">
			<div id="loading"></div>
			<select id="pw_matched_cart" name="pw_matched_cart" class="tr-type-roles">
				<option value="all" <?php selected("all",get_option('pw_matched_cart'),1);?>><?php _e('Apply this and other matched rules','pw_wc_flash_sale');?></option>
				<option value="only" <?php selected("only",get_option('pw_matched_cart'),1);?>><?php _e('Apply Biggest Discounts','pw_wc_flash_sale');?></option>
			</select>
			<button id="pw_cart_submit" type="button" class="btn button-primary"><?php _e('Submit', 'pw_wc_flash_sale') ?></button>
		</form>
	</div>
    
	<table class="wp-list-table widefat fixed posts fs-rolelist-tbl" data-page-size="5" data-page-previous-text = "prev" data-filter-text-only = "true" data-page-next-text = "next" cellspacing="0">
        <thead>
            <tr>
                <th scope='col' data-toggle="true" class='manage-column column-serial_number'  style="">
                    <a href="#"><span><?php _e('S.No', 'pw_wc_flash_sale'); ?></span></a>
                </th>
                <th scope='col' class='manage-column'  style=""><?php _e('Rule Name', 'pw_wc_flash_sale'); ?></th>
                <th scope='col' class='manage-column'  style=""><?php _e('From Date', 'pw_wc_flash_sale'); ?></th>				
                <th scope='col' class='manage-column'  style=""><?php _e('To Date', 'pw_wc_flash_sale'); ?></th>				
                <th scope="col" class="manage-column" style="width: 165px"><?php _e('Remaining Time', 'pw_wc_flash_sale'); ?></th>
                <th scope="col" class="manage-column" style=""><?php _e('Status', 'pw_wc_flash_sale'); ?></th>
                <th scope="col" class="manage-column" style=""><?php _e('Date Modified', 'pw_wc_flash_sale'); ?></th>
            </tr>
        </thead>
        <tbody id="grid_level_result">
           <?php
            $blogtime = current_time( 'mysql' );
            $args=array(
                'post_type'=>'flash_sale',
                'posts_per_page'=>5,
                'orderby'	=>'modified',
            );
            $output='';
            $i=1;
            $loop = new WP_Query( $args );
            while ( $loop->have_posts() ) : 
                $loop->the_post();
                $id=$html=$pw_to=$pw_from="";
                $type=get_post_meta(get_the_ID(),'pw_type',true);
                if($type=="cart")
                {
                    $pw_name="";
                    $pw_to=get_post_meta(get_the_ID(),'pw_to',true);
                    $pw_type=get_post_meta(get_the_ID(),'pw_to',true);
                    $pw_from=get_post_meta(get_the_ID(),'pw_from',true);
                    $pw_name=get_post_meta(get_the_ID(),'pw_name',true);
                    $id=rand(0,1000);
                    $countdown="style1";
                    $fontsize="medium";
                    $offset_utc=get_option('pw_woocommerce_flashsale_timezone_countdown','-8');
                    $html='
                        <ul class="fl-'.$countdown.' fl-'.$fontsize.' fl-countdown fl-countdown-pub countdown_'.$id.'">
                          <li><span class="days">--</span><p class="days_text">'.__('Days','pw_wc_flash_sale').'</p></li>
                            <li class="seperator">:</li>
                            <li><span class="hours">--</span><p class="hours_text">'.__('Hour','pw_wc_flash_sale').'</p></li>
                            <li class="seperator">:</li>
                            <li><span class="minutes">--</span><p class="minutes_text">'.__('Minutes','pw_wc_flash_sale').'</p></li>
                            <li class="seperator">:</li>
                            <li><span class="seconds">--</span><p class="seconds_text">'.__('Seconds','pw_wc_flash_sale').'</p></li>
                        </ul>
                        <script type="text/javascript">
                            jQuery(".countdown_'.$id.'").countdown({
                                date: "'.$pw_to.'",
                                offset: "'.$offset_utc.'",
                                day: "Day",
                                days: "Days"
                            }, function () {
                            //	alert("Done!");
                            });
                        </script>';
                    $res=strtotime(get_post_meta(get_the_ID(),'pw_to',true))-strtotime(get_post_meta(get_the_ID(),'pw_from',true));
                    $days= floor(($res)/86400);
                    $hours=floor(($res-($days*86400))/3600);
                    $res='Days: '.$days.' H : '.$hours;
                    
                    $status=get_post_meta(get_the_ID(),'status',true);
					$imgstatus="";
					if($status=="active")
						$imgstatus='<img src="'.plugin_dir_url_flash_sale."images/active.png".'" style="height: 25px;width: 25px;" title="'.__('Active','pw_wc_flash_sale').'" alt="'.__('Active','pw_wc_flash_sale').'"/>';
					else
						$imgstatus='<img src="'.plugin_dir_url_flash_sale."images/deactive.png".'" style="height: 25px;width: 25px;" title="'.__('Active','pw_wc_flash_sale').'" alt="'. __('Deactive','pw_wc_flash_sale').'"/>';
                    $output.='
                    <tr class="pw_list_rule_tr" id="'.get_the_ID().'" data-active-status="'.$status.'">
                        <td>'.$i++.'</td>
                        <td><a href="'.wp_nonce_url( remove_query_arg( "points_balance", add_query_arg( array( "pw_action_type" => "edit", 'pw_id' => get_the_ID()) ) ), "wc_points_rewards_update" ).'">'.$pw_name.'</a></td>
                        <td>'.get_post_meta(get_the_ID(),'pw_from',true).'</td>
                        <td>'.get_post_meta(get_the_ID(),'pw_to',true).'</td>
                        <td>'.$html.'</td>
                        <td>'.$imgstatus.'</td>
                        <td>'.get_the_modified_date('F j, Y g:i a').'</td>
                    </tr>
                    ';
            }						
            endwhile;						

                echo $output;
           ?>
        </tbody>
</table>

</div>
</div>