<?php
if (!defined('ABSPATH')) {
    exit;
}
$action=(isset($_REQUEST['action'])? $_REQUEST['action'] : "list");
if(isset($_REQUEST['action'])=="add" || isset($_REQUEST['action'])=="edit")
{
	require_once PW_flash_sale_URL . 'core/admin/save.php';
}
	$pw_name=$pw_flash_sale_image=$pw_flashsale_image_id=$pw_product_category=$pw_type_conditions=$pw_users=$pw_cart_roles=$pw_capabilities=$pw_roles=$pw_except_product_category=$pw_product_tag=$pw_except_product_tag=$pw_product_brand=$pw_except_product_brand=$pw_product=$pw_except_product=$pw_discount=$pw_from=$pw_to="";
	$pw_condition=array();
	$pw_condition=array(
		'subtotal_least'=>'',
		'subtotal_less_than'=>'',
		'count_item_least'=>'',
		'count_less_than'=>'',
		'pw_product'=>'',
		'pw_except_product'=>'',
		'pw_product_category'=>'',
		'pw_except_product_category'=>'',
		'pw_product_tag'=>'',
		'pw_except_product_tag'=>'',
		'user_capabilities_in_list'=>'',
		'user_role_in_list'=>'',
		);	
if($action=="add" || $action=="edit")
{



	if(@$_REQUEST['action']=="edit")
	{
		//$action='save';	
		if(isset($_REQUEST['pw_id']))
		{
			$pw_name=get_post_meta($_REQUEST['pw_id'],'pw_name',true);

			$pw_type_conditions=get_post_meta($_REQUEST['pw_id'],'pw_type_conditions',true);
			
			$pw_type=get_post_meta($_REQUEST['pw_id'],'pw_type',true);
			$thumbnail_id = get_post_meta($_REQUEST['pw_id'], 'pw_flash_sale_image', true);
			$pw_flashsale_image_id =$thumbnail_id;
			if ($thumbnail_id)
				$pw_flash_sale_image = wp_get_attachment_thumb_url( $thumbnail_id );
			$pw_flash_sale_image = str_replace( ' ', '%20', $pw_flash_sale_image );
			
			if(defined('plugin_dir_url_pw_woo_brand'))
			{					
				$pw_product_brand=get_post_meta($_REQUEST['pw_id'],'pw_product_brand',true);
				$pw_except_product_brand=get_post_meta($_REQUEST['pw_id'],'pw_except_product_brand',true);
			}

			$pw_discount=get_post_meta($_REQUEST['pw_id'],'pw_discount',true);
			$pw_from=get_post_meta($_REQUEST['pw_id'],'pw_from',true);
			$pw_to=get_post_meta($_REQUEST['pw_id'],'pw_to',true);

			$pw_condition=get_post_meta($_REQUEST['pw_id'],'pw_condition',true);	
		}		
	}
}

else if(@$_REQUEST['action']=='delete' && isset($_REQUEST['pw_id']))
{
	wp_delete_post($_REQUEST['pw_id']);	
}
else if(@$_REQUEST['action']=='status' && isset($_REQUEST['pw_id']))
{
	update_post_meta($_REQUEST['pw_id'], 'status', @$_REQUEST['status_type']);
}

require_once PW_flash_sale_URL . 'core/admin/cart-list.php';	