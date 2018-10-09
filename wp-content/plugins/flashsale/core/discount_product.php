<?php
class WC_flash_sale_Product_product {

	public $check;
	public function __construct()
	{
		/*
		add_filter( 'woocommerce_grouped_price_html', array(&$this, 'on_price_htmla'), 10, 2 );
		add_filter( 'woocommerce_variable_price_html', array(&$this, 'on_price_htmla'), 10, 2 );
		add_filter( 'woocommerce_sale_price_html', array(&$this, 'on_price_htmla'), 10, 2 );
		add_filter( 'wc_price_html', array(&$this, 'on_price_htmla'), 10, 2 );
		add_filter( 'woocommerce_empty_price_html', array(&$this, 'on_price_htmla'), 10, 2 );
		add_filter( 'woocommerce_variation_price_html', array(&$this, 'on_price_htmla'), 10, 2 );
		add_filter( 'woocommerce_variation_sale_price_html', array(&$this, 'on_price_htmla'), 10, 2 );
		add_filter( 'woocommerce_variable_sale_price_html', array(&$this, 'on_price_htmla'), 10, 2 );
		*/
		//For Markup
		add_filter( 'woocommerce_get_variation_price_html', array(&$this, 'markup_price_html'), 10, 2 );
		add_filter( 'woocommerce_variation_price_html', array(&$this, 'markup_price_html'), 10, 2 );
		add_filter( 'woocommerce_get_price_html', array(&$this, 'markup_price_html'), 10, 2 );

		
		add_filter( 'woocommerce_get_variation_price_html', array(&$this, 'on_price_htmla'), 10, 2 );
		add_filter( 'woocommerce_variation_price_html', array(&$this, 'on_price_htmla'), 10, 2 );
		add_filter( 'woocommerce_get_price_html', array(&$this, 'on_price_htmla'), 10, 2 );
		$this->check=false; 
		$this->rules=array();
		$this->settings=array();
	}

	public function check_rule_list() 
	{
		$arr = array();
		$this->settings=get_option("pw_flashsale_discount_options");
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
				'meta_query' => $query_meta_query,
			)
		);
		foreach($matched_products as $pr)
		{
			$pw_type = get_post_meta($pr,'pw_type',true);
			if($pw_type!="flashsale")
			{
				continue;
			}
			$pw_product_brand=$pw_except_product_brand="";
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
				"pw_product"=>$pw_product,
				"pw_except_product"=>$pw_except_product,
				"pw_product_brand"=>$pw_product_brand,
				"pw_except_product_brand"=>$pw_except_product_brand,
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
			$pw_apply_to_day=get_option("pw_apply_to_day");
			$this->rules[9812345]=array(
				"pw_type"=>'deal',
				"pw_to"=>'',
				"pw_from"=>'',
				"pw_apply_to"=>'pw_all_product',
				"pw_product_category"=>'',
				"pw_except_product_category"=>'',
				"pw_product_tag"=>'',
				"pw_except_product_tag"=>'',
				"pw_product_brand"=>'',
				"pw_except_product_brand"=>'',
				"pw_product"=>'',
				"pw_except_product"=>'',
				"pw_cart_roles"=>'',
				"pw_roles"=>'',
				"pw_capabilities"=>'',
				"pw_discount_qty"=>'',
				"pw_users"=>'',
				"pw_products_to_adjust"=>'',
				"quantity_base"=>'',
				"pw_discount"=>'',
				"adjustment_type"=>'',
				"pw_type_discount"=>'',
				"adjustment_value"=>'',
				"amount_to_adjust"=>'',
				"amount_to_purchase"=>'',
				"repeat"=>'',
				"pw_matched"=>$pw_apply_to_day['pw_matched'],
				"pw_products_to_adjust_products"=>'',
				"pw_products_to_adjust_category"=>'',
				"pw_apply_to_day"=>$pw_apply_to_day,
			);
				
			$RulesAplly = array();			
			$this->check=true; 
		}
			
	}
	
/*    public function getuserid()
    {
        if (defined('DOING_AJAX') && $this->useridsession()) {
            return $_SESSION['userIdForAjax'];
        }
    
        $userId = get_current_user_id();
    
        return $userId;
    }
  
    private function useridsession()
    {
        return isset($_SESSION['userIdForAjax']);
    } 
	
	public function get_customer_total_order() {
		global $wpdb;
		
		//$post_status = implode("','", array('wc-processing', 'wc-completed') );
		$post_status = implode("','", array('wc-completed') );
		$current_user = get_current_user_id();

		$result = $wpdb->get_results( "SELECT COUNT(id) as idcount FROM $wpdb->posts 
					WHERE post_type = 'shop_order'
					AND post_status IN ('{$post_status}')
					AND post_author = $current_user
					AND post_status  NOT IN ('trash')
				");
		echo $result[0]->idcount;
	}*/  	
	public function markup_price_html( $html, $_product ) 
	{
		//print_r($this->get_customer_orders());
		//echo $this->get_customer_total_order();
		//die('a');
		$pw_apply_to_markup=get_option("pw_apply_to_markup");
		$get_roles=$this->pw_user_roles();
		if($pw_apply_to_markup['active']=="disable" || !$this->pw_user_roles())
		{
			return apply_filters( 'wc_flash_sale_pricing_price_html', $html, $_product );			
		}
		
		$markup=$pw_apply_to_markup[$get_roles[0]]['markup'];		
		$type=$pw_apply_to_markup[$get_roles[0]]['type'];
		
		if($markup===0 || $markup=="")
		{
			return apply_filters( 'wc_flash_sale_pricing_price_html', $html, $_product );						
		}  
		
		$base_price=PW_Discount_function::pw_get_base_price_by_product($_product);
		
		$markup_show=PW_Discount_function::pw_get_markup_price($base_price,$type,$markup);
		
		if ( $markup_show && $markup_show != $base_price ) {
			if ( apply_filters( 'wc_dynamic_pricing_use_discount_format', true ) ) {
				if ( $_product->is_type( 'variable' ) ) {
				
					$from = '<span class="from">' . _x( 'From:', 'min_price', flash_sale_v1_load_plugin_textdomain ) . ' d</span>';
				}
				$html = wc_price($markup_show);
			} else {

				if ( $_product->is_type( 'variable' ) ) {
					$from = '<span class="from">d' . _x( 'From:', 'min_price', flash_sale_v1_load_plugin_textdomain ) . ' </span>';
				}

				$html = $from . $markup_show ;
			}
		}
		elseif ( $markup_show === 0 || $markup_show === 0.00 ) {
			return apply_filters( 'wc_flash_sale_pricing_price_html', $html, $_product );			
		//	$html = $_product->get_price_html_from_to( $_product->regular_price, __( 'Free!', flash_sale_v1_load_plugin_textdomain ) );
		}		
		
		return apply_filters( 'wc_flash_sale_pricing_price_html', $html, $_product );		
		//$this->pw_user_roles();
	//	echo 'sssssssssssssss<br/>';


		/*if($active=="disable" || count(array_intersect($this->pw_user_roles(), $rule['pw_roles'])) < 1)
		{
			return apply_filters( 'wc_flash_sale_pricing_price_html', $html, $_product );			
		}
*/		
		//print_r($this->pw_user_roles());
	}
	public function on_price_htmla( $html, $_product ) 
	{
		$Days=$Hour=$Minutes=$Seconds=$hide_price_for_unregister_user="";
		/*$setting=get_option("pw_flashsale_discount_options");
		$hide_price_for_unregister_user=$setting['hide_price_for_unregister_user'];
		if(!$this->getuserid() && $hide_price_for_unregister_user==true)
		{
			$html=$setting['hide_price_text'];
			return apply_filters( 'wc_flash_sale_pricing_price_html', $html, $_product );
		}
		*/
		if($this->check==false)
		{			
			$this->check_rule_list();
		}		

		$Days=$this->settings['Days'];
		$Hour=$this->settings['Hour'];
		$Minutes=$this->settings['Minutes'];
		$Seconds=$this->settings['Seconds'];
		if ( is_shop()) {
			$Days=$this->settings['Daysinarchive'];
			$Hour=$this->settings['Hourinarchive'];
			$Minutes=$this->settings['Minutesinarchive'];
			$Seconds=$this->settings['Secondsinarchive'];
			if($this->settings['pw_woocommerce_flashsale_archive_countdown']=="no")
			{
				return apply_filters( 'wc_flash_sale_pricing_price_html', $html, $_product );
			}
		}
		if ( is_singular( 'product' ) )
		{
			if($this->settings['pw_woocommerce_flashsale_single_countdown']!="yes")
			{
				return apply_filters( 'wc_flash_sale_pricing_price_html', $html, $_product );
			}
		}
		//$base_price=function_exists('wc_get_price_to_display') ? wc_get_price_to_display( $_product, array( 'qty' => 1, 'price' => $_product->get_regular_price()  ) ): $_product->get_display_price( $_product->get_regular_price() );		
		$base_price=PW_Discount_function::pw_get_base_price_by_product($_product);
		//$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );				
		//$base_price = $tax_display_mode == 'incl' ? $_product->get_price_including_tax() : $_product->get_price_excluding_tax();				


		if(!is_array($this->rules))
		{
			return apply_filters( 'wc_flash_sale_pricing_price_html', $html, $_product );
		}

		$RulesAplly = array();	
		$var = new PW_Discount_Price();
		foreach ($this->rules as $rule_key => $rule) {
			if ($var->check_candition_rules($rule)) {
				$flag=false;

				if($rule['pw_apply_to'] == 'pw_all_product')
				{
					$flag=true;
				}
				if ($rule['pw_apply_to'] == 'pw_product_category') {
					if (count(array_intersect($this->get_categories($_product->get_id()), $rule['pw_product_category'])) > 0) {
						$flag=true;
					}
				}
				else if ($rule['pw_apply_to'] == 'pw_except_product_category') {
					if (count(array_intersect($this->get_categories($_product->get_id()), $rule['pw_except_product_category'])) == 0) {
						$flag=true;
					}
				}
				if ($rule['pw_apply_to'] == 'pw_product_tag') {
					if (count(array_intersect($this->get_tag($_product->get_id()), $rule['pw_product_tag'])) > 0) {
						$flag=true;
					}
				}
				else if ($rule['pw_apply_to'] == 'pw_except_product_tag') {
					if (count(array_intersect($this->get_tag($_product->get_id()), $rule['pw_except_product_tag'])) == 0) {
						$flag=true;
					}
				}
				if ($rule['pw_apply_to'] == 'pw_product_brand') {
					if (count(array_intersect($this->get_brands($_product->get_id()), $rule['pw_product_brand'])) > 0) {
						$flag=true;
					}
				}
				else if ($rule['pw_apply_to'] == 'pw_except_product_brand') {
					if (count(array_intersect($this->get_brands($_product->get_id()), $rule['pw_except_product_brand'])) == 0) {
						$flag=true;
					}
				}
				else if ($rule['pw_apply_to'] == 'pw_product') {
					
					if (in_array($_product->get_id(), $rule['pw_product'])) {
						$flag=true;
					}
				}
				else if ($rule['pw_apply_to'] == 'pw_except_product') {
					if (!in_array($_product->get_id(), $rule['pw_except_product'])) {
						$flag=true;
					}
				}
				//Rule don't Applly
				if($flag==false)
				{
					continue;
				}
				
				$discount=PW_Discount_function::pw_get_discunt_price($base_price,$rule['pw_type_discount'],$rule['pw_discount']);
				if ( $discount && $discount != $base_price ) {
					if ( $_product->is_type( 'variable' ) ) {
						
						$from = '<span class="from">' . _x( 'From:', 'min_price', flash_sale_v1_load_plugin_textdomain ) . '</span>';
					}
					
					$html = '<del>' .   wc_price($base_price). '</del><ins>' .  wc_price($discount). '</ins>';
				}
				elseif ( $discount === 0 || $discount === 0.00 ) {
					return apply_filters( 'wc_flash_sale_pricing_price_html', $html, $_product );
					//$html = $_product->get_price_html_from_to( $_product->regular_price, __( 'Free!', flash_sale_v1_load_plugin_textdomain ) );
				}
				//$offset_utc=$setting['pw_woocommerce_flashsale_timezone_countdown'];
				$offset_utc=$this->settings['pw_woocommerce_flashsale_timezone_countdown']==""?'-8' : $this->settings['pw_woocommerce_flashsale_timezone_countdown'];
				$id=rand(0,1000);
				$countdown=$this->settings['pw_woocommerce_flashsale_countdown'];
				$fontsize=$this->settings['pw_woocommerce_flashsale_fontsize_countdown'];
				if(is_admin())
				{
					$countdown="style1";
					$fontsize="medium";
				}

				if(isset($rule['pw_to']) && $rule['pw_to']!="")
				{
					$html.='
						<div id="pw_fl_timer" class="fl-pcountdown-cnt">
							<ul class="fl-'.$countdown.' fl-'.$fontsize.' fl-countdown fl-countdown-pub countdown_'.$id.'">
							  <li><span class="days">00</span><p class="days_text">'.$Days.'</p></li>
								<li class="seperator">:</li>
								<li><span class="hours">00</span><p class="hours_text">'.$Hour.'</p></li>
								<li class="seperator">:</li>
								<li><span class="minutes">00</span><p class="minutes_text">'.$Minutes.'</p></li>
								<li class="seperator">:</li>
								<li><span class="seconds">00</span><p class="seconds_text">'.$Seconds.'</p></li>
							</ul>
							<ul id="pw_fl_timer_done" style="display:none">
								<li>Timer is Done</li>
							</ul>										
						</div>
						<script type="text/javascript">
							jQuery(".countdown_'.$id.'").countdown({
								date: "'.$rule['pw_to'].'",
								offset: "'.$offset_utc.'",
								day: "Day",
								days: "'.$Days.'",
								hours: "'.$Hour.'",
								minutes: "'.$Minutes.'",
								seconds: "'.$Seconds.'",
							}, function () {
									jQuery("#pw-fl-timer").remove();
									jQuery("#pw-fl-timer-done").show();
							//	alert("Done!");
							});
						</script>';
				}
			}
		}
		
		return apply_filters( 'wc_flash_sale_pricing_price_html', $html, $_product );
	}
	
	public function get_tag($id)
	{
		$arr = array();
		$get_tag = wp_get_post_terms($id, 'product_tag');

		foreach ($get_tag as $tag)
			$arr[] = $tag->term_id;
			
		return $arr;
	}
	public function pw_user_roles()
	{
		$current_user="";
		$current_user = wp_get_current_user();	
		//get_currentuserinfo();
		return $current_user->roles;
	}		
	public function get_brands($id)
	{
		$arr = array();
		$get_brand = wp_get_post_terms($id, 'product_brand');

		foreach ($get_brand as $brands)
			$arr[] = $brands->term_id;
			
		return $arr;
	}
	
	public function get_categories($id)
	{
		$cat = array();
		$get_category = wp_get_post_terms($id, 'product_cat');

		foreach ($get_category as $category)
			$cat[] = $category->term_id;
			
		return $cat;
	}	
}
new WC_flash_sale_Product_product();
?>