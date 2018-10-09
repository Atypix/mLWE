<?php

if (!defined('ABSPATH')) {
    exit;
}
//die(print_r($_REQUEST));
$action=(isset($_REQUEST['action'])? $_REQUEST['action'] : "list");
if(isset($_REQUEST['action'])=="add" || isset($_REQUEST['action'])=="edit")
{
	require_once PW_flash_sale_URL . 'core/admin/save.php';
}
if($action=="list_product")
{
	require_once PW_flash_sale_URL . 'core/admin/product-discount.php';
}	
else
{
	$pw_flash_sale_image=$pw_type_discount=$quantity_base=$pw_matched=$pw_users=$pw_cart_roles=$pw_roles=$adjustment_type=$pw_capabilities=
	$pw_flashsale_image_id=$pw_name=$pw_type=$pw_apply_to=$adjustment_value=$amount_to_adjust=
	$amount_to_purchase=$pw_products_to_adjust=$arr=$pw_products_to_adjust_products=$pw_products_to_adjust_category=$repeat=$thumbnail_id =$pw_product_category=$pw_except_product_category=$pw_product_tag=$pw_except_product_tag=$pw_product_brand=$pw_except_product_brand=$pw_product=$pw_except_product=$pw_discount=$pw_from=$pw_to=$pw_matched=$pw_discount_qty="";	
	
	if($action=="add" || $action=="edit")
	{		
		if(@$_REQUEST['action']=="edit")
		{
			if(isset($_REQUEST['pw_id']))
			{
					$pw_name=get_post_meta($_REQUEST['pw_id'],'pw_name',true);
					$pw_matched=get_post_meta($_REQUEST['pw_id'],'pw_matched',true);
					$pw_users=get_post_meta($_REQUEST['pw_id'],'pw_users',true);
					$pw_type=get_post_meta($_REQUEST['pw_id'],'pw_type',true);
					$pw_cart_roles=get_post_meta($_REQUEST['pw_id'],'pw_cart_roles',true);
					$pw_capabilities=get_post_meta($_REQUEST['pw_id'],'pw_capabilities',true);
					$pw_roles=get_post_meta($_REQUEST['pw_id'],'pw_roles',true);
					$quantity_base=get_post_meta($_REQUEST['pw_id'],'quantity_base',true);
					$thumbnail_id = get_post_meta($_REQUEST['pw_id'], 'pw_flash_sale_image', true);
					$pw_flashsale_image_id =$thumbnail_id;
					if ($thumbnail_id)
						$pw_flash_sale_image = wp_get_attachment_thumb_url( $thumbnail_id );
					$pw_flash_sale_image = str_replace( ' ', '%20', $pw_flash_sale_image );
					
					$pw_product_category=get_post_meta($_REQUEST['pw_id'],'pw_product_category',true);
					$pw_except_product_category=get_post_meta($_REQUEST['pw_id'],'pw_except_product_category',true);
					$pw_apply_to=get_post_meta($_REQUEST['pw_id'],'pw_apply_to',true);
					$pw_product_tag=get_post_meta($_REQUEST['pw_id'],'pw_product_tag',true);
					$pw_except_product_tag=get_post_meta($_REQUEST['pw_id'],'pw_except_product_tag',true);
					if(defined('plugin_dir_url_pw_woo_brand'))
					{				
						$pw_product_brand=get_post_meta($_REQUEST['pw_id'],'pw_product_brand',true);
						$pw_except_product_brand=get_post_meta($_REQUEST['pw_id'],'pw_except_product_brand',true);
					}
					$pw_product=get_post_meta($_REQUEST['pw_id'],'pw_product',true);
					$pw_except_product=get_post_meta($_REQUEST['pw_id'],'pw_except_product',true);
					$pw_discount=get_post_meta($_REQUEST['pw_id'],'pw_discount',true);
					$pw_from=get_post_meta($_REQUEST['pw_id'],'pw_from',true);
					$pw_to=get_post_meta($_REQUEST['pw_id'],'pw_to',true);
					$pw_discount_qty=get_post_meta($_REQUEST['pw_id'],'pw_discount_qty',true);
					$pw_type_discount=get_post_meta($_REQUEST['pw_id'],'pw_type_discount',true);
					$adjustment_type=get_post_meta($_REQUEST['pw_id'],'adjustment_type',true);
					$adjustment_value=get_post_meta($_REQUEST['pw_id'],'adjustment_value',true);
					$amount_to_adjust=get_post_meta($_REQUEST['pw_id'],'amount_to_adjust',true);
					$amount_to_purchase=get_post_meta($_REQUEST['pw_id'],'amount_to_purchase',true);		
					$pw_products_to_adjust=get_post_meta($_REQUEST['pw_id'],'pw_products_to_adjust',true);		
					$pw_products_to_adjust_products=get_post_meta($_REQUEST['pw_id'],'pw_products_to_adjust_products',true);		
					$pw_products_to_adjust_category=get_post_meta($_REQUEST['pw_id'],'pw_products_to_adjust_category',true);
					$repeat=get_post_meta($_REQUEST['pw_id'],'repeat',true);
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
	require_once PW_flash_sale_URL . 'core/admin/pricing-list.php';
	//require_once PW_flash_sale_URL . 'core/admin/pricing-add-edit.php';
	
}
?>