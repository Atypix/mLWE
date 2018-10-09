<?php
	$pw_array=array(
		"tr_flashsale"=>array(
			"parent_id"=>array("pw_tr_type"),
			"pw_tr_type"=>array(
				"value"=>array("flashsale"),
				"type"=>"select"
			)
		),
		"tr_quantities_based"=>array(
			"parent_id"=>array("pw_tr_type"),
			"pw_tr_type"=>array(
				"value"=>array("quantity","special"),
				"type"=>"select"
			)
		),
		"tr_special"=>array(
			"parent_id"=>array("pw_tr_type"),
			"pw_tr_type"=>array(
				"value"=>array("special"),
				"type"=>"select"
			)
		),
		"tr_quantity"=>array(
			"parent_id"=>array("pw_tr_type"),
			"pw_tr_type"=>array(
				"value"=>array("quantity"),
				"type"=>"select"
			)
		),
		"discount_apply_to_pw_product"=>array(
			"parent_id"=>array("pw_apply_to"),
			"pw_apply_to"=>array(
				"value"=>array("pw_product"),
				"type"=>"select"
			)
		),
		"discount_apply_to_pw_product_category"=>array(
			"parent_id"=>array("pw_apply_to"),
			"pw_apply_to"=>array(
				"value"=>array("pw_product_category"),
				"type"=>"select"
			)
		),
		"discount_apply_to_pw_except_product_category"=>array(
			"parent_id"=>array("pw_apply_to"),
			"pw_apply_to"=>array(
				"value"=>array("pw_except_product_category"),
				"type"=>"select"
			)
		),
		"discount_apply_to_pw_product_tag"=>array(
			"parent_id"=>array("pw_apply_to"),
			"pw_apply_to"=>array(
				"value"=>array("pw_product_tag"),
				"type"=>"select"
			)
		),
		"discount_apply_to_pw_except_product_tag"=>array(
			"parent_id"=>array("pw_apply_to"),
			"pw_apply_to"=>array(
				"value"=>array("pw_except_product_tag"),
				"type"=>"select"
			)
		),
		"discount_apply_to_pw_except_product"=>array(
			"parent_id"=>array("pw_apply_to"),
			"pw_apply_to"=>array(
				"value"=>array("pw_except_product"),
				"type"=>"select"
			)
		),
		"discount_apply_to_pw_product_brand"=>array(
			"parent_id"=>array("pw_apply_to"),
			"pw_apply_to"=>array(
				"value"=>array("pw_product_brand"),
				"type"=>"select"
			)
		),
		"discount_apply_to_pw_except_product_brand"=>array(
			"parent_id"=>array("pw_apply_to"),
			"pw_apply_to"=>array(
				"value"=>array("pw_except_product_brand"),
				"type"=>"select"
			)
		),
		"pw_cart_roles"=>array(
			"parent_id"=>array("pw_cart_customer"),
			"pw_cart_customer"=>array(
				"value"=>array("roles"),
				"type"=>"select"
			)
		),
		"pw_cart_capabilities"=>array(
			"parent_id"=>array("pw_cart_customer"),
			"pw_cart_customer"=>array(
				"value"=>array("capabilities"),
				"type"=>"select"
			)
		),
		"pw_cart_users"=>array(
			"parent_id"=>array("pw_cart_customer"),
			"pw_cart_customer"=>array(
				"value"=>array("users"),
				"type"=>"select"
			)
		),
		"tr_adjust"=>array(
			"parent_id"=>array("pw_tr_type"),
			"pw_tr_type"=>array(
				"value"=>array("quantity","special"),
				"type"=>"select"
			),			
		),
		"products_to_adjust_products"=>array(
			"parent_id"=>array("pw_tr_type","pw_product_to_adjust"),
			"pw_tr_type"=>array(
				"value"=>array("quantity","special"),
				"type"=>"select"
			),
			"pw_product_to_adjust"=>array(
				"value"=>array("other_products"),
				"type"=>"select"
			),			
		),
		"pw_products_to_adjust_category"=>array(
			"parent_id"=>array("pw_tr_type","pw_product_to_adjust"),
			"pw_tr_type"=>array(
				"value"=>array("quantity","special"),
				"type"=>"select"
			),
			"pw_product_to_adjust"=>array(
				"value"=>array("other_categories"),
				"type"=>"select"
			),			
		),
	);
/*		"tr_quantity"=>array(
			"parent_id"=>array("pw_tr_type"),
			"pw_tr_type"=>array(
				"value"=>array("quantity"),
				"type"=>"select"
			)
		),
		"pw_products_to_adjust_category"=>array(
			"parent_id"=>array("pw_tr_type","pw_product_to_adjust"),
			"pw_tr_type"=>array(
				"value"=>array("quantity"),
				"type"=>"select"
			),
			"pw_product_to_adjust"=>array(
				"value"=>array("other_categories"),
				"type"=>"select"
			)
		)	
		*/
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
		
	}
	
	
?>

<script type="text/javascript">
	"use strict";
	jQuery( document ).ready(function( $ ) {
		//confirm('as');
		<?php echo $final_js;?>;
		
	});	
		
</script>