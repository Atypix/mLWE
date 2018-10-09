<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('class_shortcode_flashsale')) {
		class class_shortcode_flashsale
		{
			public function __construct()
			{
				$this->rules=array();
				add_shortcode('test', array($this, 'test'), 100);
			}
			public function test( $atts, $content = null ) {
				global $woocommerce;
				$id=31;
				$query_meta_query=array('relation' => 'AND');
				$query_meta_query[] = array(
					'key' =>'status',
					'value' => "active",
					'compare' => '=',
				);	
				$matched_products = get_posts(
					array(
						'post_type' 	=> 'flash_sale',
						'numberposts' 	=> -1,
						'post_status' 	=> 'publish',
						'fields' 		=> 'ids',
						'no_found_rows' => true,
						'orderby'	=>'modified',
						'order'            => 'ASC',
						'meta_query' => $query_meta_query,
					)
				);
				foreach($matched_products as $pr)
				{
					$pw_product_brand=$pw_except_product_brand="";
					$pw_type = get_post_meta($pr,'pw_type',true);
					if($pw_type=="cart")
						continue;							
					if(defined('plugin_dir_url_pw_woo_brand'))
					{	
						$pw_product_brand = get_post_meta($pr,'pw_product_brand',true);
						$pw_except_product_brand = get_post_meta($pr,'pw_except_product_brand',true);
					}								
					$pw_to=get_post_meta($pr,'pw_to',true);
					$pw_from=get_post_meta($pr,'pw_from',true);
					$pw_apply_to=get_post_meta($pr,'pw_apply_to',true);
					$pw_product_category=get_post_meta($pr,'pw_product_category',true);
					$pw_except_product_category=get_post_meta($pr,'pw_except_product_category',true);
					$pw_product_tag=get_post_meta($pr,'pw_product_tag',true);
					$pw_except_product_tag=get_post_meta($pr,'pw_except_product_tag',true);
					$pw_product=get_post_meta($pr,'pw_product',true);
					$pw_except_product=get_post_meta($pr,'pw_except_product',true);
					$pw_cart_roles = get_post_meta($pr,'pw_cart_roles',true);
					$pw_roles = get_post_meta($pr,'pw_roles',true);
					$pw_capabilities = get_post_meta($pr,'pw_capabilities',true);
					$pw_users = get_post_meta($pr,'pw_users',true);
					$pw_products_to_adjust = get_post_meta($pr,'pw_products_to_adjust',true);
					$quantity_base = get_post_meta($pr,'quantity_base',true);
					$pw_discount = get_post_meta($pr,'pw_discount',true);
					$adjustment_type = get_post_meta($pr,'adjustment_type',true);
					$pw_type_discount = get_post_meta($pr,'pw_type_discount',true);
					$adjustment_value = get_post_meta($pr,'adjustment_value',true);
					$amount_to_adjust = get_post_meta($pr,'amount_to_adjust',true);
					$pw_discount_qty = get_post_meta($pr,'pw_discount_qty',true);
					$amount_to_purchase = get_post_meta($pr,'amount_to_purchase',true);
					$repeat = get_post_meta($pr,'repeat',true);
					$pw_matched = get_post_meta($pr,'pw_matched',true);
					$pw_products_to_adjust_products = get_post_meta($pr,'pw_products_to_adjust_products',true);
					$pw_products_to_adjust_category = get_post_meta($pr,'pw_products_to_adjust_category',true);
					$this->rules[$pr]=array(
						"pw_type"=>$pw_type,
						"pw_to"=>$pw_to,
						"pw_from"=>$pw_from,
						"pw_apply_to"=>$pw_apply_to,
						"pw_product_category"=>$pw_product_category,
						"pw_except_product_category"=>$pw_except_product_category,
						"pw_product_tag"=>$pw_product_tag,
						"pw_except_product_tag"=>$pw_except_product_tag,
						"pw_product_brand"=>$pw_product_brand,
						"pw_except_product_brand"=>$pw_except_product_brand,
						"pw_product"=>$pw_product,
						"pw_except_product"=>$pw_except_product,
						"pw_cart_roles"=>$pw_cart_roles,
						"pw_roles"=>$pw_roles,
						"pw_capabilities"=>$pw_capabilities,
						"pw_discount_qty"=>$pw_discount_qty,
						"pw_users"=>$pw_users,
						"pw_products_to_adjust"=>$pw_products_to_adjust,
						"quantity_base"=>$quantity_base,
						"pw_discount"=>$pw_discount,
						"adjustment_type"=>$adjustment_type,
						"pw_type_discount"=>$pw_type_discount,
						"adjustment_value"=>$adjustment_value,
						"amount_to_adjust"=>$amount_to_adjust,
						"amount_to_purchase"=>$amount_to_purchase,
						"repeat"=>$repeat,
						"pw_matched"=>$pw_matched,
						"pw_products_to_adjust_products"=>$pw_products_to_adjust_products,
						"pw_products_to_adjust_category"=>$pw_products_to_adjust_category,
					);
				}
				$RulesAplly = array();	
				$var = new PW_Discount_Price();				
				foreach ($this->rules as $rule_key => $rule) {
					$discount="";
					if ($this->check_candition_rules($rule)) {
						if($rule['pw_type']=="quantity")
						{
							
						}
						if($rule['pw_type']=="special")
						{
						
							$special_show='Buy '.$rule['amount_to_purchase']. ' of this product Get '.$rule['adjustment_value'].' Discount on ';
							echo $special_show;
						}							
						if($rule['pw_type']=="flashsale")
						{
							$flag=false;
							if($rule['pw_apply_to'] == 'pw_all_product')
								$flag=true;	
							if ($rule['pw_apply_to'] == 'pw_product_category') {
								if (count(array_intersect($this->get_categories($id), $rule['pw_product_category'])) > 0) {
									$flag=true;
								}
							}
							else if ($rule['pw_apply_to'] == 'pw_except_product_category') {
								if (count(array_intersect($this->get_categories($id), $rule['pw_except_product_category'])) == 0) {
									$flag=true;
								}
							}
							if ($rule['pw_apply_to'] == 'pw_product_tag') {
								if (count(array_intersect($this->get_tag($id), $rule['pw_product_tag'])) > 0) {
									$flag=true;
								}
							}
							else if ($rule['pw_apply_to'] == 'pw_except_product_tag') {
								if (count(array_intersect($this->get_tag($id), $rule['pw_except_product_tag'])) == 0) {
									$flag=true;
								}
							}
							if ($rule['pw_apply_to'] == 'pw_product_brand') {
								if (count(array_intersect($this->get_brands($id), $rule['pw_product_brand'])) > 0) {
									$flag=true;
								}
							}
							else if ($rule['pw_apply_to'] == 'pw_except_product_brand') {
								if (count(array_intersect($this->get_brands($id), $rule['pw_except_product_brand'])) == 0) {
									$flag=true;
								}
							}
							else if ($rule['pw_apply_to'] == 'pw_product') {
								if (in_array($id, $rule['pw_product'])) {
									$flag=true;
								}
							}
							else if ($rule['pw_apply_to'] == 'pw_except_product') {
								if (!in_array($id, $rule['pw_except_product'])) {
									$flag=true;
								}
							}
							if($flag==true)
							{
								switch ($rule['pw_type_discount'])
								{
									case 'percent':
										$discount = $rule['pw_discount'] . ' %';
									break;
									
									case 'price':
										$discount = '- '.wc_price($rule['pw_discount']);

									break;
								}
							}
							echo $discount;
						}						
					}
				}
			}
			public function check_candition_rules($rule)
			{
				if (!isset($rule['pw_type']) || !in_array($rule['pw_type'], array('flashsale', 'special','quantity')))
				{
					return false;
				}
				
				if (isset($rule['pw_to']) && !empty($rule['pw_to']) && (strtotime($rule['pw_to']) < strtotime(current_time( 'mysql' ))))
				{
					return false;
				}
				
				if (isset($rule['pw_from']) && !empty($rule['pw_from']) && (strtotime($rule['pw_from']) > strtotime(current_time( 'mysql' )))) 
				{
					return false;
				}
				
				return true;				
			}				
		}
		new class_shortcode_flashsale();
}


//
//add_shortcode( 'flash_sale_product_rule_auto', 'pw_flashsale_shortcode_auto' );
function pw_flashsale_shortcode_auto( $atts, $content = null ) {
	$brands_attr = shortcode_atts( array(
		'rule'=>'',
		'item_layout'=>'layout1',			
		'item_image_eff'=>'fl-none',
		'product_thumb_size'=>'thumbnail',
		'show_countdown'=>'',
		'show_discount'=>'',
		'countdown_style'=>'',
		'countdown_size'=>'',
		'text_colour'=>'#ffffff',
		'countdown_backcolour'=>'#333333',
		'description_area_backcolour'=>'#f5f5f5',
		'column'=>'fl-col-md-12',
		'column_tablet'=>'fl-col-sm-12',
		'column_mobile'=>'fl-col-xs-12',
		'fl_custom_class'=>'',
	), $atts );
	
	$rule='';
	if($brands_attr['rule']!="")
		$rule=array($brands_attr['rule']);
	
	$query_meta_query=array('relation' => 'AND');
		
	$matched_products_rule = get_posts(
		array(
			'post_type' 	=> 'flash_sale',
			'numberposts' 	=> -1,
			'post_status' 	=> 'publish',
			'fields' 		=> 'ids',
			'post__in'=>$rule,
			'no_found_rows' => true,
			'orderby'	=>'modified',
		)
	);
	$matched_products=$ret="";
//	print_r($matched_products_rule);
	if(!is_array($matched_products_rule) || count($matched_products_rule)<=0)
		return;
	
	$setting=get_option("pw_flashsale_discount_options");
	$offset_utc=$setting['pw_woocommerce_flashsale_timezone_countdown']==""?'-8' : $setting['pw_woocommerce_flashsale_timezone_countdown'];
	$blogtime = current_time( 'mysql' );
	
	foreach($matched_products_rule as $p)
	{
		$status=get_post_meta($p,'status',true);
		if($status!="active")
			continue;
		$pw_from=get_post_meta($p,'pw_from',true);
		$pw_to=get_post_meta($p,'pw_to',true);		
		if(strtotime($blogtime)<strtotime($pw_to) && strtotime($blogtime)>strtotime($pw_from))
		{
			$pw_discount= get_post_meta($p,'pw_discount',true);
			$pw_type_discount= get_post_meta($p,'pw_type_discount',true);
			$pw_apply_to=get_post_meta($p,'pw_apply_to',true);
			$pw_product_category=get_post_meta($p,'pw_product_category',true);
			$pw_except_product_category=get_post_meta($p,'pw_except_product_category',true);
			$pw_product_tag=get_post_meta($p,'pw_product_tag',true);
			$pw_except_product_tag=get_post_meta($p,'pw_except_product_tag',true);
			$pw_product=get_post_meta($p,'pw_product',true);
			$pw_except_product=get_post_meta($p,'pw_except_product',true);
			$arr=$except_product='';
			if((is_array($pw_product_category) && count($pw_product_category)>0) || (is_array($pw_except_product) && count($pw_except_product)>0)|| (is_array($pw_except_product_category) && count($pw_except_product_category)>0) || (is_array($pw_product_tag) && count($pw_product_tag)>0) || (is_array($pw_except_product_tag) && count($pw_except_product_tag)>0)){
				$arr=array('relation' => 'AND');
			}
			
			if($pw_apply_to=="pw_except_product")
				$except_product=$pw_except_product;
			elseif($pw_apply_to=="pw_product_category")
			{
				if(is_array($pw_product_category) && count($pw_product_category)>0)
				{
					$arr[]=array(
								'taxonomy' => 'product_cat',
								'field'    => 'id',
								'terms'    => $pw_product_category,
							);
				}
			}
			elseif($pw_apply_to=="pw_except_product_category")
			{
				if(is_array($pw_except_product_category) && count($pw_except_product_category)>0)
				{
					$arr[]=array(
								'taxonomy' => 'product_cat',
								'field'    => 'id',
								'terms'    => $pw_except_product_category,
								'operator' => 'NOT IN',
							);			
				}
			}
			elseif($pw_apply_to=="pw_product_tag")
			{
				if(is_array($pw_product_tag) && count($pw_product_tag)>0)
				{
					$arr[]=array(
						'taxonomy' => 'product_tag',
						'field'    => 'id',
						'terms'    => $pw_product_tag,
					);
				}
			}
			elseif($pw_apply_to=="pw_except_product_tag")
			{
				add_post_meta($post_id, 'pw_except_product_tag', @$_POST['pw_except_product_tag']);
				if(is_array($pw_except_product_tag) && count($pw_except_product_tag)>0)
				{				
					$arr[]=array(
						'taxonomy' => 'product_tag',
						'field'    => 'id',
						'terms'    => $pw_except_product_tag,
						'operator' => 'NOT IN',
					);
					
				}
			}
			if($pw_apply_to=="pw_product")
				$matched_products =$pw_product;
			else
			{
				$matched_products = get_posts(
					array(
						'post_type' 	=> 'product',
						'numberposts' 	=> -1,
						'post_status' 	=> 'publish',
						'fields' 		=> 'ids',
						'post__not_in'		=>$except_product,
						'no_found_rows' => true,
						'tax_query' => $arr,
					)
				);
			}
			break;
		}
	}
	if($matched_products=="" || !is_array($matched_products) || count($matched_products)<=0)
		return;	

	$ret.='<div class="fl-row '.$brands_attr['fl_custom_class'].'">';
	foreach($matched_products as $a)
	{
		$rand_id=rand(0,1000);		
		$title=$result=$countdown="";$result="";

		$product = wc_get_product( $a );

		$image = get_the_post_thumbnail( $a, 'full' );
		
		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($a), $brands_attr['product_thumb_size'] );
		$thumb_url = $thumb['0'];
		//$image = get_the_post_thumbnail( $a, 'full' );
		$image = '<img src="'.$thumb_url.'" />';		
		
		$result= $product->get_price_html();
		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );		
		//$base_price = $tax_display_mode == 'incl' ? PW_Discount_function::price_including_tax_function($product) : PW_Discount_function::price_excliding_tax_function($product);
		$base_price=$product->get_price();
		$title=$product->get_title();// get_the_title( $p,true );
		$permalink= $product->get_permalink();//get_page_link($p);
		
		if($pw_discount!="")
		{
			if ( $pw_type_discount=="percent" )
			{
				$max_discount = calculate_modifiera( $pw_discount, $base_price );
				$result = round( floatval( $base_price ) - ( floatval( $max_discount  )), (int) $num_decimals );
			}
			else
				$result=$base_price-$pw_discount;

			$result='<del>' .   wc_price($base_price). '</del><ins> ' .  wc_price($result). '</ins>';
		}
		if($brands_attr['show_countdown']=="yes" && $pw_discount!="" && $pw_to!="")
		{
			$id=rand(0,1000);
			//$offset_utc=get_option('pw_woocommerce_flashsale_timezone_countdown','-8');
			$countdown ='
				<div class="fl-rule-coundown countdown-'.$rand_id.'	">
					<ul class="fl-'.$brands_attr['countdown_style'].' fl-'.$brands_attr['countdown_size'].' fl-countdown countdown_'.$id.'">
					  <li><span class="days">--</span><p class="days_text">Days</p></li>
						<li class="seperator">:</li>
						<li><span class="hours">--</span><p class="hours_text">'.$setting['Hour'].'</p></li>
						<li class="seperator">:</li>
						<li><span class="minutes">--</span><p class="minutes_text">'.$setting['Minutes'].'</p></li>
						<li class="seperator">:</li>
						<li><span class="seconds">--</span><p class="seconds_text">'.$setting['Seconds'].'</p></li>
					</ul>
				</div>
				<script type="text/javascript">
					jQuery(".countdown_'.$id.'").countdown({
						date: "'.$pw_to.'",
						offset: "'.$offset_utc.'",
						day: "Day",
						days: "'.$setting['Days'].'",
						hours: "'.$setting['Hour'].'",
						minutes: "'.$setting['Minutes'].'",
						seconds: "'.$setting['Seconds'].'",
					}, function () {
					//	alert("Done!");
					});
				</script>';
		}

		$ret.= '<div class="'.$brands_attr['column_mobile'].' '.$brands_attr['column_tablet'].' '.$brands_attr['column'].' col-'.$rand_id.'" >';	
			
							
			if($brands_attr['item_layout']=="layout1")
				include( PW_flash_sale_URL . 'core/front-end/item-layout1.php');	
			
			else if($brands_attr['item_layout']=="layout2")
				include( PW_flash_sale_URL . 'core/front-end/item-layout2.php');	
			
			else if($brands_attr['item_layout']=="layout3")
				include( PW_flash_sale_URL . 'core/front-end/item-layout3.php');	
			
			else if($brands_attr['item_layout']=="layout4")
				include( PW_flash_sale_URL . 'core/front-end/item-layout4.php');	
			
			else if($brands_attr['item_layout']=="layout5")
				include( PW_flash_sale_URL . 'core/front-end/item-layout5.php');	 
		$ret.= '</div>';
	}
	$ret .='</div>';
	PW_Discount_function::fl_top_product_grid_custom_style( $rand_id , $brands_attr['text_colour'] , $brands_attr['countdown_backcolour'] , $brands_attr['description_area_backcolour']);
	return $ret;
}

//php=ok
add_shortcode( 'flash_sale_product_rule', 'pw_flashsale_shortcode' );
function pw_flashsale_shortcode( $atts, $content = null ) {
		$brands_attr = shortcode_atts( array(
			'rule'=>'',			
			'item_layout'=>'layout1',			
			'item_image_eff'=>'fl-none',
			'product_thumb_size'=>'thumbnail',
			'count_items'=>'-1',
			'show_countdown'=>'yes',
			'show_discount'=>'yes',
			'countdown_style'=>'style1',
			'countdown_size'=>'medium',
			'text_colour'=>'#ffffff',
			'countdown_backcolour'=>'#333333',
			'countdown_area_backcolour'=>'#ffffff',
			'item_width'=>'300',
			'item_marrgin'=>'10',
			'slide_direction' => 'vertical',			
			'show_paginatin' => 'true',			
			'show_control' => 'true',			
			'item_per_view' => '1',			
			'item_per_slide' => '1',			
			'slide_speed' => '3000',			
			'auto_play' => 'true',			
			'description_area_backcolour' => '#f5f5f5',			
			'fl_custom_class' => '',
		), $atts );
		$ret="";
		$cunt=$brands_attr['count_items']=="" ? -1 : $brands_attr['count_items'];
		
		$rule=$brands_attr['rule']== "" ? "" : array($brands_attr['rule']);
		//if($brands_attr['rule']!="")
		//	$rule=array($brands_attr['rule']);
	
		$query_meta_query=array('relation' => 'AND');
		$query_meta_query[] = array(
			'key' =>'status',
			'value' => "active",
			'compare' => '=',
		);
		$matched_products = get_posts(
			array(
				'post_type' 	=> 'flash_sale',
				'numberposts' 	=> $cunt,
				'post_status' 	=> 'publish',
				'include'		=> $rule,
				'fields' 		=> 'ids',
				'no_found_rows' => true,
				'orderby'	=>'modified',
				'order'            => 'ASC',
				'meta_query' => $query_meta_query,
			)
		);	
		
		$setting=get_option("pw_flashsale_discount_options");
		$offset_utc=$setting['pw_woocommerce_flashsale_timezone_countdown']==""?'-8' : $setting['pw_woocommerce_flashsale_timezone_countdown'];
		$pw_array="";
		
			$show_countdown="";
			$flag=false;
			
			$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );		
			
		foreach($matched_products as $pr)
		{
			$flag=false;
			$pw_type = get_post_meta($pr,'pw_type',true);
			$pw_to=get_post_meta($pr,'pw_to',true);
			$pw_from=get_post_meta($pr,'pw_from',true);
			$pw_type_discount = get_post_meta($pr,'pw_type_discount',true);
			$pw_discount = get_post_meta($pr,'pw_discount',true);
		
			if($pw_type_discount=="percent")
				$pw_discount_show='-'.$pw_discount.'%';
			else
				$pw_discount_show='-'.wc_price($pw_discount);				
			
			$type=array(
				'type'=>$pw_type_discount,
				'discount'=>$pw_discount,
			);		
			
			if(strtotime(current_time( 'mysql' ))<strtotime($pw_to) && strtotime(time())>strtotime($pw_from)){
				$pw_type= get_post_meta(get_the_ID(),'pw_type',true);
				if($pw_type=="flashsale")
					$flag=true;
			}
			if($flag=false)
				break;
			
			$rand_id = rand(0,1000);
			
			if (trim($brands_attr['show_countdown']) == "yes")
			{
				$ret .= '<div class="fl-countdown-cnt countdown-'.$rand_id.' '.$brands_attr['fl_custom_class'].'">';
				$id=rand(0,1000);			
				$ret.='<ul class="fl-'.$brands_attr['countdown_style'].' fl-'.$brands_attr['countdown_size'].' fl-countdown countdown_'.$id.'">
						  <li><span class="days">00</span><p class="days_text">'.$setting['Days'].'</p></li>
							<li class="seperator">:</li>
							<li><span class="hours">00</span><p class="hours_text">'.$setting['Hour'].'</p></li>
							<li class="seperator">:</li>
							<li><span class="minutes">00</span><p class="minutes_text">'.$setting['Minutes'].'</p></li>
							<li class="seperator">:</li>
							<li><span class="seconds">00</span><p class="seconds_text">'.$setting['Seconds'].'</p></li>
						</ul>
					<script type="text/javascript">
						jQuery(".countdown_'.$id.'").countdown({
							date: "'.$pw_to.'",
							offset: "'.$offset_utc.'",
							day: "Day",
							days: "'.$setting['Days'].'",
							hours: "'.$setting['Hour'].'",
							minutes: "'.$setting['Minutes'].'",
							seconds: "'.$setting['Seconds'].'",
						}, function () {
						//	alert("Done!");
						});
					</script>';
				$ret .= '</div>';
			}

			$ret .= '<ul id="sidecar_'.$rand_id.'" class="fs-bxslider fs-single-car  fs-carousel-layout car-'.$rand_id.'">';
			
			$pw_array = get_post_meta($pr,'pw_array',true);
			foreach($pw_array as $a)
			{
				$product = wc_get_product( $a );
				//print_r($product);
				$base_price=$product->get_price();
				
				//$base_price = ($tax_display_mode == 'incl' ? PW_Discount_function::price_including_tax_function($product,1,$base_price) : PW_Discount_function::price_excliding_tax_function($product,1,$base_price));

				$price=PW_Discount_Price::price_discunt($base_price,$type);
				$price=$base_price-$price;
				
				//$size = 'shop_catalog';
				$title= $product->get_title();//get_the_title( $a );
				//$image = $product->get_image(); //get_the_post_thumbnail( $a, 'medium' );
		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($a), $brands_attr['product_thumb_size'] );
		$thumb_url = $thumb['0'];
		//$image = get_the_post_thumbnail( $a, 'full' );
		$image = '<img src="'.$thumb_url.'" />';
		
				$permalink=$product->get_permalink();
				
				$ret.= '<li >';							
					if($brands_attr['item_layout']=="layout1")
						include( PW_flash_sale_URL . 'core/front-end/product_rule/item-layout1.php');	
					
					else if($brands_attr['item_layout']=="layout2")
						include( PW_flash_sale_URL . 'core/front-end/product_rule/item-layout2.php');	
					
					else if($brands_attr['item_layout']=="layout3")
						include( PW_flash_sale_URL . 'core/front-end/product_rule/item-layout3.php');	
					
					else if($brands_attr['item_layout']=="layout4")
						include( PW_flash_sale_URL . 'core/front-end/product_rule/item-layout4.php');	
					
					else if($brands_attr['item_layout']=="layout5")
						include( PW_flash_sale_URL . 'core/front-end/product_rule/item-layout5.php');	
				$ret.= '</li>';
			}//end foreach
			$ret .='</ul>';

				
				
				
									
		}

		if ( ($brands_attr['slide_direction']=='vertical') || ($brands_attr['slide_direction']=='horizontal'))	{
			$item_width=$brands_attr['item_width'];
			if($item_width=="")
				$item_width="1000";
			$ret .= "<script type='text/javascript'>
					/* <![CDATA[  */
					jQuery(document).ready(function() {
						sidecar_" . $rand_id ." =
						 jQuery('#sidecar_" . $rand_id ."').bxSlider({ 
							  
							  mode : '".($brands_attr['slide_direction']=='vertical' ? 'vertical' : 'horizontal' )."' ,
							  touchEnabled : true ,
							  adaptiveHeight : true ,
							  slideMargin : ".($brands_attr['item_marrgin']!='' ? $brands_attr['item_marrgin'] : '10').", 
							  wrapperClass : 'fs-bx-wrapper fs-sidebar-car ".$brands_attr['fl_custom_class']." ' ,
							  infiniteLoop: true,
							  pager: ".$brands_attr['show_paginatin'] .",
							  controls: ".$brands_attr['show_control'] .",
							  ".($brands_attr['slide_direction']=='horizontal' ? 'slideWidth:'.$item_width.',' : 'slideWidth:5000,' )."
							  minSlides:1,
							  maxSlides: ".($brands_attr['item_per_view']!="" ? $brands_attr['item_per_view'] : "1").",
							  moveSlides: ".($brands_attr['item_per_slide']!='' ? $brands_attr['item_per_slide'] : '1').",
							  auto: true,
							  pause : ".$brands_attr['slide_speed']."	,
							  autoHover  : true , 
							  autoStart: ".$brands_attr['auto_play']."
						 });";
						 
						if ($brands_attr['auto_play']=='true'){
							$ret.="
							 jQuery('.fs-bx-wrapper .fs-bx-controls-direction a').click(function(){
								  sidecar_" . $rand_id .".startAuto();
							 });";
						}
						$ret.="});	
					/* ]]> */
				</script>";
		}
		
		PW_Discount_function::fl_product_rule_custom_style( $rand_id , $brands_attr['text_colour'] , $brands_attr['countdown_backcolour'] , $brands_attr['countdown_area_backcolour'] , $brands_attr['description_area_backcolour']);
			
			
		
		return $ret;
	}	

//php=ok
//css=
add_shortcode( 'flash_sale_top_products_carosel', 'pw_flashsalerule_product_shortcode' );
function pw_flashsalerule_product_shortcode( $atts, $content = null ){

	$brands_attr = shortcode_atts( array(
		'products' => '',
		'item_layout' => 'layout1',
		'item_image_eff' => 'fl-none',
		'product_thumb_size' => 'thumbnail',
		'show_discount'=>'yes',
		'show_countdown'=>'',
		'countdown_style'=>'',
		'countdown_size'=>'',
		'text_colour'=>'#ffffff',
		'countdown_backcolour'=>'#333333',
		'item_width'=>'',
		'item_marrgin'=>'0',
		'slide_direction'=>'',
		'show_pagination'=>'',
		'show_control'=>'',
		'item_per_view'=>'',
		'item_per_slide'=>'',
		'slide_speed'=>'',
		'auto_play'=>'',
		'description_area_backcolour'=>'',
		'fl_custom_class'=>'',
	), $atts );
	$ret ='';
	$blogtime = current_time( 'mysql' );
	$products=explode(",",$brands_attr['products']);
	
	$query_meta_query=array('relation' => 'AND');
	$query_meta_query[] = array(
		'key' =>'pw_type',
		'value' => "flashsale",
		'compare' => '=',
	);	
	$matched_products = get_posts(
		array(
			'post_type' 	=> 'flash_sale',
			'numberposts' 	=> -1,
			'post_status' 	=> 'publish',
			'fields' 		=> 'ids',
			'no_found_rows' => true,
			'orderby'	=>'modified',
			'meta_query' => $query_meta_query,
		)
	);
	$setting=get_option("pw_flashsale_discount_options");
	$offset_utc=$setting['pw_woocommerce_flashsale_timezone_countdown']==""?'-8' : $setting['pw_woocommerce_flashsale_timezone_countdown'];		
	$rand_id= rand(0,1000);
	$ret .= '<ul id="sidecar_'.$rand_id.'" class="fs-bxslider fs-single-car  fs-carousel-layout car-'.$rand_id.' '.$brands_attr['fl_custom_class'].'">';	
	$i=1;
	foreach($products as $a)
	{
		$pw_discount=$pw_to=$title=$result=$countdown=$result="";
		$product = wc_get_product( $a );
		//$base_price = get_post_meta( $p, '_regular_price',true);
		//$product = wc_get_product( $p );
		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );		
		$price = $tax_display_mode == 'incl' ? PW_Discount_function::price_including_tax_function($product) : PW_Discount_function::price_excliding_tax_function($product);
		
		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($a), $brands_attr['product_thumb_size'] );
		$thumb_url = $thumb['0'];
		//$image = get_the_post_thumbnail( $a, 'full' );
		$image = '<img src="'.$thumb_url.'" />';
		
		//$image = get_the_post_thumbnail( $a, 'full' );
		$result= $product->get_price_html();
		//$base_price = $tax_display_mode == 'incl' ? PW_Discount_function::price_including_tax_function($product) : PW_Discount_function::price_excliding_tax_function($product);
		$base_price=$product->get_price();
		$flag=false;
		foreach($matched_products as $r)
		{
			$arr="";
			$pw_to=get_post_meta($r,'pw_to',true);				
			$pw_from=get_post_meta($r,'pw_from',true);				
			$pw_type_discount=get_post_meta($r,'pw_type_discount',true);				
			if(strtotime($blogtime)<strtotime($pw_to) && strtotime($blogtime)>strtotime($pw_from))
			{
				$arr= get_post_meta($r,'pw_array',true);
				if (is_array($arr) && in_array($a, $arr))
				{
					$pw_discount= get_post_meta($r,'pw_discount',true);
					
					if($pw_type_discount=="percent")
						$pw_discount_show='-'.$pw_discount.'%';
					else
						$pw_discount_show='-'.wc_price($pw_discount);
		
					$type=array(
						'type'=>$pw_type_discount,
						'discount'=>$pw_discount,
					);	
					
					$price=PW_Discount_Price::price_discunt($base_price,$type);
					$flag=true;
					//$result=$base_price-$price;
					//$result='<del>' . wc_price($base_price). '</del><ins> ' .  wc_price($result). '</ins>';
					break;
				}
			}
		}
		$title=$product->get_title();// get_the_title( $p,true );
		$permalink= $product->get_permalink();//get_page_link($p);
		$ret.= '<li >';							
			if($brands_attr['item_layout']=="layout1")
				include( PW_flash_sale_URL . 'core/front-end/item-layout1.php');	
			
			else if($brands_attr['item_layout']=="layout2")
				include( PW_flash_sale_URL . 'core/front-end/item-layout2.php');	
			
			else if($brands_attr['item_layout']=="layout3")
				include( PW_flash_sale_URL . 'core/front-end/item-layout3.php');	
			
			else if($brands_attr['item_layout']=="layout4")
				include( PW_flash_sale_URL . 'core/front-end/item-layout4.php');	
			
			else if($brands_attr['item_layout']=="layout5")
				include( PW_flash_sale_URL . 'core/front-end/item-layout5.php');	
		$ret.= '</li>';
				
	}
	$ret.='</ul>';
	$item_width=$brands_attr['item_width'];
	if($item_width=="")
		$item_width="1000";	
	$ret.= "<script type='text/javascript'>
			jQuery(document).ready(function() {
				sidecar_" . $rand_id ." =
				 jQuery('#sidecar_" . $rand_id ."').bxSlider({ 
					  mode : '".($brands_attr['slide_direction']=='vertical' ? 'vertical' : 'horizontal' )."' ,
					  touchEnabled : true ,
					  adaptiveHeight : true ,
					  slideMargin : ".($brands_attr['item_marrgin']!='' ? $brands_attr['item_marrgin'] : '10').", 
					  wrapperClass : 'fs-bx-wrapper fs-sidebar-car ".$brands_attr['fl_custom_class']." ' ,
					  infiniteLoop: true,
					  pager: ".$brands_attr['show_pagination'] .",
					  controls: ".$brands_attr['show_control'] .",
					  ".($brands_attr['slide_direction']=='horizontal' ? 'slideWidth:'.$item_width.',' : 'slideWidth:5000,' )."
					  minSlides:1,
					  maxSlides: ".($brands_attr['item_per_view']!='' ? $brands_attr['item_per_view'] : '1').",
					  moveSlides: ".($brands_attr['item_per_slide'] !='' ? $brands_attr['item_per_slide'] : '1').",
					  auto: true,
					  pause : ".($brands_attr['slide_speed']!='' ? $brands_attr['slide_speed'] : '2000')."	,
					  autoHover  : true , 
					  autoStart: ".$brands_attr['auto_play']."
				 });";
				 
				if ($brands_attr['auto_play']=='true'){
					$ret.="
					 jQuery('.fs-bx-wrapper .fs-bx-controls-direction a').click(function(){
						  sidecar_" . $rand_id .".startAuto();
					 });";
				}
				$ret.="
				});	
		</script>";
		
	PW_Discount_function::fl_top_product_carousel_custom_style( $rand_id , $brands_attr['text_colour'] , $brands_attr['countdown_backcolour'] , $brands_attr['description_area_backcolour']);	
	return $ret;
}


//php=ok
//css=
add_shortcode( 'flash_sale_rule_list', 'pw_flashsalerule_shortcode' );
function pw_flashsalerule_shortcode( $atts, $content = null ) {
		$brands_attr = shortcode_atts( array(
			'rule'=>'',
			'show_discount'=>'yes',
			'show_countdown' => 'yes',
			'countdown_style' => 'style1',
			'countdown_size' => 'small',
			'countdown_position' => 'top-right',
			'text_colour' => '#ffffff',
			'countdown_backcolour' => '#333333',
			'structure' => 'fl-col-md-12',
			'structure_tablet' => 'fl-col-sm-12',
			'structure_mobile' => 'fl-col-xs-12',
			'fl_custom_class' => '',
		), $atts );
		$ret ='';
		$rule=$brands_attr['rule']!="" ? explode(",",$brands_attr['rule']) : "";
		
		$blogtime = current_time( 'mysql' );
		
		$setting=get_option("pw_flashsale_discount_options");
		$offset_utc=$setting['pw_woocommerce_flashsale_timezone_countdown']==""?'-8' : $setting['pw_woocommerce_flashsale_timezone_countdown'];		
		$rand_id = rand(1,1000);
		
		$query_meta_query=array('relation' => 'AND');
		$query_meta_query[] = array(
			'key' =>'status',
			'value' => "active",
			'compare' => '=',
		);
		
		$matched_products = get_posts(
			array(
				'post_type' 	=> 'flash_sale',
				'numberposts' 	=> -1,
				'post_status' 	=> 'publish',
				'include'		=> $rule,
				'fields' 		=> 'ids',
				'no_found_rows' => true,
				'orderby'	=>'modified',
				'order'            => 'ASC',
				'meta_query' => $query_meta_query,
			)
		);	
		$ret .= '<div class="fl-row '.$brands_attr['fl_custom_class'].'">';
		
		foreach($matched_products as $pr)
		{
			$flag=false;
			$pw_flash_sale_image=get_post_meta($pr,'pw_flash_sale_image',true);			
			$pw_type = get_post_meta($pr,'pw_type',true);
			$pw_to=get_post_meta($pr,'pw_to',true);
			$pw_from=get_post_meta($pr,'pw_from',true);
			$pw_type_discount = get_post_meta($pr,'pw_type_discount',true);
			$pw_discount = get_post_meta($pr,'pw_discount',true);
			$pw_name = get_post_meta($pr,'pw_name',true);
			
			if($pw_type_discount=="percent")
				$pw_discount_show='-'.$pw_discount.'%';
			else
				$pw_discount_show='-'.wc_price($pw_discount);
			$ret .= '<div class="'.$brands_attr['structure_mobile'].' '.$brands_attr['structure_tablet'].' '.$brands_attr['structure'].'">';
			
			$ret .='<div class="fl-rulecnt fl-rulelist fl-slide-overlay rulelist-'.$rand_id.'">';
				$ret .='<div class="fl-countdown-cnt '.$brands_attr['countdown_position'].'" >';		
					//$ret .='<h3><a href="'.get_page_link(get_the_ID()).'">'.get_the_title().'</a></h3>';	
					if($brands_attr['show_discount']=="yes")
					{
						$ret .= '<div class="fl-rulcnt-discount">' . $pw_discount_show.'</div>';
					}	
					
					$pw_to=get_post_meta($pr,'pw_to',true);
					if($brands_attr['show_countdown']=="yes" && strtotime($blogtime)<strtotime($pw_to) )

					{
						$id=rand(0,1000);
						//$offset_utc=get_option('pw_woocommerce_flashsale_timezone_countdown','-8');
						$ret .='
							<div class="fl-rule-coundown ">
								<ul class="fl-'.$brands_attr['countdown_style'].' fl-'.$brands_attr['countdown_size'].' fl-countdown countdown_'.$id.'">
								  <li><span class="days">00</span><p class="days_text">'.$setting['Days'].'</p></li>
									<li class="seperator">:</li>
									<li><span class="hours">00</span><p class="hours_text">'.$setting['Hour'].'</p></li>
									<li class="seperator">:</li>
									<li><span class="minutes">00</span><p class="minutes_text">'.$setting['Minutes'].'</p></li>
									<li class="seperator">:</li>
									<li><span class="seconds">00</span><p class="seconds_text">'.$setting['Seconds'].'</p></li>
								</ul>
							</div>
							<script type="text/javascript">
								jQuery(".countdown_'.$id.'").countdown({
									date: "'.$pw_to.'",
									offset: "'.$offset_utc.'",
									day: "Day",
									days: "'.$setting['Days'].'",
									hours: "'.$setting['Hour'].'",
									minutes: "'.$setting['Minutes'].'",
									seconds: "'.$setting['Seconds'].'",
								}, function () {
								//	alert("Done!");
								});
							</script>';							
					}
					
				$ret .='</div>';
					
				$ret .='<a class="fl-imglink" href="'.get_page_link($pr).'">'.wp_get_attachment_image( $pw_flash_sale_image, 'full' ).'</a>';	
			$ret .=	'</div>';
			$ret .=	'</div><!--col -->';
			
		}
		$ret.='</div><!--rule wrapper --->';
					
		PW_Discount_function::fl_rule_list_custom_style( $rand_id , $brands_attr['text_colour'] , $brands_attr['countdown_backcolour'] );
		return $ret;
		
}

//php=ok
//css=
add_shortcode( 'flash_sale_rule_slider', 'pw_flashsalerule_slider_shortcode' );
function pw_flashsalerule_slider_shortcode( $atts, $content = null ) {
		$brands_attr = shortcode_atts( array(
			'rule'=>'',		
			'show_discount'=>'yes',			
			'show_countdown' => 'no',
			'countdown_style'=>'style1',
			'countdown_position'=>'top-right',
			'countdown_size'=>'small',
			'text_colour'=>'',
			'countdown_backcolour'=>'',
			'slider_direction'=>'vertical',
			'slider_thumb_item'=>'5',
			'slider_ver_height'=>'400',
			'slider_ver_thumb_width'=>'200',
			'slider_mod'=>'slide',
			'show_control'=>'true',
			'slider_speed'=>'1000',
			'auto_play'=>'true',
			'fl_custom_class'=>'',
		), $atts );
		$ret ='';
		$rule=$brands_attr['rule']!=="" ? explode(",",$brands_attr['rule']) : $rule;
		//if($brands_attr['rule']!="")
			//$rule=explode(",",$brands_attr['rule']);
		$setting=get_option("pw_flashsale_discount_options");
		$offset_utc=$setting['pw_woocommerce_flashsale_timezone_countdown']==""?'-8' : $setting['pw_woocommerce_flashsale_timezone_countdown'];

		$query_meta_query=array('relation' => 'AND');
		$query_meta_query[] = array(
			'key' =>'status',
			'value' => "active",
			'compare' => '=',
		);
		$matched_products = get_posts(
			array(
				'post_type' 	=> 'flash_sale',
				'numberposts' 	=> -1,
				'post_status' 	=> 'publish',
				'include'		=> $rule,
				'fields' 		=> 'ids',
				'no_found_rows' => true,
				'orderby'	=>'modified',
				'order'            => 'ASC',
				'meta_query' => $query_meta_query,
			)
		);	
		
		$rand_id = rand(0,1000);
		$ret .= '<div class="fl-promobox-cnt '.$brands_attr['fl_custom_class'].'">';
		$ret .= '<ul id="fl_image_gallery_'.$rand_id.'" class="gallery list-unstyled cS-hidden ruleslider-'.$rand_id.'">';
		
		foreach($matched_products as $pr)
		{
			$flag=false;
			$pw_flash_sale_image=get_post_meta($pr,'pw_flash_sale_image',true);			
			$pw_type = get_post_meta($pr,'pw_type',true);
			$pw_to=get_post_meta($pr,'pw_to',true);
			$pw_from=get_post_meta($pr,'pw_from',true);
			$pw_type_discount = get_post_meta($pr,'pw_type_discount',true);
			$pw_discount = get_post_meta($pr,'pw_discount',true);
			$pw_name = get_post_meta($pr,'pw_name',true);
			$blogtime = current_time( 'mysql' );
			
			if($pw_type_discount=="percent")
				$pw_discount_show='-'.$pw_discount.'%';
			else
				$pw_discount_show='-'.wc_price($pw_discount);
			
			$ret .='<li data-title="'.$pw_name.'">
					<div class="fl-slide-overlay">
					<div class="fl-countdown-cnt '.$brands_attr['countdown_position'].' " >';
						if($brands_attr['show_discount']=="yes")
						{
							$ret .= '<div class="fl-rulcnt-discount">' . $pw_discount_show .'</div>';
						}	
						if($brands_attr['show_countdown']=="yes" && strtotime($blogtime)<strtotime($pw_to) )
						{
							$id=rand(0,1000);
							$ret .='
								
									<div class="fl-rule-coundown">
										<ul class="fl-'.$brands_attr['countdown_style'].' fl-'.$brands_attr['countdown_size'].' fl-countdown countdown_'.$id.'">
										  <li><span class="days">00</span><p class="days_text">Days</p></li>
											<li class="seperator">:</li>
											<li><span class="hours">00</span><p class="hours_text">'.$setting['Hour'].'</p></li>
											<li class="seperator">:</li>
											<li><span class="minutes">00</span><p class="minutes_text">'.$setting['Minutes'].'</p></li>
											<li class="seperator">:</li>
											<li><span class="seconds">00</span><p class="seconds_text">'.$setting['Seconds'].'</p></li>
										</ul>
									</div>
								
								<script type="text/javascript">
									jQuery(".countdown_'.$id.'").countdown({
										date: "'.$pw_to.'",
										offset: "'.$offset_utc.'",
										day: "Day",
										days: "'.$setting['Days'].'",
										hours: "'.$setting['Hour'].'",
										minutes: "'.$setting['Minutes'].'",
										seconds: "'.$setting['Seconds'].'",
									}, function () {
									//	alert("Done!");
									});
								</script>';							
						}
					$ret .='</div>';
						
						
					//$img_src = wp_get_attachment_image_src( $pw_flash_sale_image, 'full' )	;
					$ret .='<a class="fl-imglink" href="'.get_page_link($pr).'" >'.wp_get_attachment_image( $pw_flash_sale_image, 'full' ).'</a>';	
					
				$ret .='</div>';		
			$ret .=	'</li>';			

		}			

	  $ret.= '</ul>';
	  $ret .='</div>';	
	  
	  $brands_attr['slider_ver_thumb_width'] = ($brands_attr['slider_ver_thumb_width']!=''?$brands_attr['slider_ver_thumb_width']:'200');
	  $brands_attr['slider_ver_height'] = ($brands_attr['slider_ver_height']!=''?$brands_attr['slider_ver_height']:'400');
	  $ret .= "<script type='text/javascript'>
			jQuery(document).ready(function() {
				fl_image_gallery_" . $rand_id ." =
				 jQuery('#fl_image_gallery_" . $rand_id ."').lightSlider({ 
					gallery:true,
					item:1,
					thumbItem:".$brands_attr['slider_thumb_item'].",
					slideMargin: 0,
					pause:".$brands_attr['slider_speed'].",
					auto:".$brands_attr['auto_play'].",
					loop:true,
					mode: '".($brands_attr['slider_mod']=='fade' ? 'fade' : 'slide' )."',
					pauseOnHover: true,
					vertical: ".($brands_attr['slider_direction']=='vertical' ? 'true' : 'false' ).",
					galleryMargin: 0,
					thumbMargin: 0,
					vThumbWidth: ".$brands_attr['slider_ver_thumb_width'].",
					verticalHeight:".$brands_attr['slider_ver_height'].",
					controls: ".$brands_attr['show_control'].",
					adaptiveHeight: true,
					onSliderLoad: function() {
						jQuery('#fl_image_gallery_" . $rand_id ."').removeClass('cS-hidden');
					}  
				 });
				 ";
			$ret.="
			});	
		</script>";
		
		PW_Discount_function::fl_rule_slider_custom_style( $rand_id , $brands_attr['text_colour'] , $brands_attr['countdown_backcolour'] );
		return $ret;
}

//php=ok
//css=
add_shortcode( 'flash_sale_top_products_grid', 'pw_flashsalerule_product_shortcode_grid' );
function pw_flashsalerule_product_shortcode_grid( $atts, $content = null )
{
	
	$brands_attr = shortcode_atts( array(
		'products' => '',
		'item_layout' => 'layout1',
		'item_image_eff' => 'fl-none',
		'product_thumb_size' => 'thumbnail',
		'show_countdown'=>'yes',
		'show_discount'=>'yes',
		'countdown_style'=>'style1',
		'countdown_size'=>'small',
		'text_colour'=>'#ffffff',
		'countdown_backcolour'=>'#333333',
		'description_area_backcolour'=>'#f5f5f5',
		'column'=>'fl-col-md-12',
		'column_tablet'=>'fl-col-sm-12',
		'column_mobile'=>'fl-col-xs-12',
		'fl_custom_class'=>'',
	), $atts );
	$ret ='';
	if($brands_attr['products']=="" || $brands_attr['products']=="null")
		return ;
	$products=explode(",",$brands_attr['products']);
	$get_rule = get_posts(
		array(
			'post_type' 	=> 'flash_sale',
			'numberposts' 	=> -1,
			'post_status' 	=> 'publish',
			'fields' 		=> 'ids',
			'orderby'	=>'modified',
			'no_found_rows' => true,
		)
	);
	$ret.='<div class="fl-row '.$brands_attr['fl_custom_class'].'">';
	$rand_id=rand(0,1000);
	$setting=get_option("pw_flashsale_discount_options");
	$offset_utc=$setting['pw_woocommerce_flashsale_timezone_countdown']==""?'-8' : $setting['pw_woocommerce_flashsale_timezone_countdown'];
	$blogtime = current_time( 'mysql' );
	foreach($products as $a)
	{
		$pw_discount=$pw_to=$title=$result=$countdown=$pw_discount_show=$result=$show_price="";
		$product = wc_get_product( $a );

		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($a), $brands_attr['product_thumb_size'] );
		$thumb_url = $thumb['0'];
		//$image = get_the_post_thumbnail( $a, 'full' );
		$image = '<img src="'.$thumb_url.'" />';
		$result= $product->get_price_html();
		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
		//$base_price =$price= ($tax_display_mode == 'incl' ? PW_Discount_function::price_including_tax_function($product) : PW_Discount_function::price_excliding_tax_function($product));
		$base_price=$product->get_price();
		$flag=false;
		foreach($get_rule as $r)
		{
			$rules=array();	
			$pw_product_brand=$pw_discount_show=$price=$pw_except_product_brand="";
			$arr="";
			$title=$product->get_title();// get_the_title( $p,true );		
			$pw_to=get_post_meta($r,'pw_to',true);				
			$pw_from=get_post_meta($r,'pw_from',true);				
			$pw_type=get_post_meta($r,'pw_type',true);				
			
			if($pw_type!="flash_sale")
			{
				continue;
			}
			
			$pw_type_discount=get_post_meta($r,'pw_type_discount',true);	
			if(strtotime($blogtime)<strtotime($pw_to) && strtotime($blogtime)>strtotime($pw_from))
			{
				$arr= get_post_meta($r,'pw_array',true);
				if (is_array($arr) && in_array($a, $arr))
				{
					$pw_discount= get_post_meta($r,'pw_discount',true);
					
					if($pw_type_discount=="percent")
						$pw_discount_show='-'.$pw_discount.'%';
					else
						$pw_discount_show='-'.wc_price($pw_discount);
		
					$type=array(
						'type'=>$pw_type_discount,
						'discount'=>$pw_discount,
					);	
					
					$price=PW_Discount_Price::price_discunt($base_price,$type);
					$flag=true;
					//$result=$base_price-$price;
					//$result='<del>' . wc_price($base_price). '</del><ins> ' .  wc_price($result). '</ins>';
					break;
				}
			}
		}

		$ret.= '<div class="'.$brands_attr['column_mobile'].' '.$brands_attr['column_tablet'].' '.$brands_attr['column'].' col-'.$rand_id.' " >';		
			$permalink= $product->get_permalink();//get_page_link($p);				
			if($brands_attr['item_layout']=="layout1")
				include( PW_flash_sale_URL . 'core/front-end/item-layout1.php');	
			
			else if($brands_attr['item_layout']=="layout2")
				include( PW_flash_sale_URL . 'core/front-end/item-layout2.php');	
			
			else if($brands_attr['item_layout']=="layout3")
				include( PW_flash_sale_URL . 'core/front-end/item-layout3.php');	
			
			else if($brands_attr['item_layout']=="layout4")
				include( PW_flash_sale_URL . 'core/front-end/item-layout4.php');	
			
			else if($brands_attr['item_layout']=="layout5")
				include( PW_flash_sale_URL . 'core/front-end/item-layout5.php');	 
		$ret.= '</div>';
		
				
	}
	$ret .='</div>';
	PW_Discount_function::fl_top_product_grid_custom_style( $rand_id , $brands_attr['text_colour'] , $brands_attr['countdown_backcolour'] , $brands_attr['description_area_backcolour']);
	return $ret;
	
}


?>