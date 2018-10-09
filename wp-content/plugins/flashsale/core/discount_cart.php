<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('PW_Discount_Cart')) {
		class PW_Discount_Cart
		{
			public function __construct()
			{
				$this->rules=array();
				$this->cart_item=array();
				$this->rule_apply=array();
				$this->product_special= array();

				
				add_action('woocommerce_ajax_added_to_cart', array($this, 'adjust_cart_rule'), 100);											
				add_action('woocommerce_cart_loaded_from_session', array($this, 'adjust_cart_rule'), 100);
				
				add_action('woocommerce_after_cart_item_quantity_update', array($this, 'adjust_cart_rule'), 100);
				add_action('woocommerce_before_cart_item_quantity_zero', array($this, 'adjust_cart_rule'), 100);	

				add_action('woocommerce_check_cart_items', array($this, 'remove_cart_discount'), 1);
				
			}

			public function remove_cart_discount()
			{
				global $woocommerce;
				foreach ($woocommerce->cart->applied_coupons as $code) {
					if (apply_filters('woocommerce_coupon_code', 'DISCOUNT') === $code) {

						$coupon = new WC_Coupon($code);
						if (!$coupon->is_valid()) {
							add_filter('woocommerce_coupons_enabled', array($this, 'woocommerce_enable_coupons'));
							$this->remove_woocommerce_coupon($code);
							remove_filter('woocommerce_coupons_enabled', array($this, 'woocommerce_enable_coupons'));
						}
					}
				}
			}	
			
			public function woocommerce_enable_coupons()
			{
				return 'yes';
			}
			
			public function remove_woocommerce_coupon($coupon)
			{
				global $woocommerce;

				if ($this->get_wc_version('2.1')) {
					$woocommerce->cart->remove_coupon($coupon);
					WC()->session->set('refresh_totals', true);
				}
				else {

					$position = array_search($coupon, $woocommerce->cart->applied_coupons);

					if ($position !== false) {
						unset($woocommerce->cart->applied_coupons[$position]);
					}

					WC()->session->set('applied_coupons', $woocommerce->cart->applied_coupons);
				}

				// Flag totals for refresh
				WC()->session->set('refresh_totals', true);
			}
		
			public function adjust_cart_rule()
			{
				global $woocommerce;
				//$this->cart_item = $this->sort_cart_by_price($woocommerce->cart->cart_contents);	
				$arr = array();
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
					$pw_type = get_post_meta($pr,'pw_type',true);
					if($pw_type!="cart")
						continue;

					$pw_discount = get_post_meta($pr,'pw_discount',true);					
					$pw_type_conditions = get_post_meta($pr,'pw_type_conditions',true);	
					$pw_from = get_post_meta($pr,'pw_from',true);
					$pw_users = get_post_meta($pr,'pw_users',true);
					$pw_capabilities = get_post_meta($pr,'pw_capabilities',true);
					$pw_roles = get_post_meta($pr,'pw_roles',true);
					$pw_product = get_post_meta($pr,'pw_product',true);
					$pw_discount_qty = get_post_meta($pr,'pw_discount_qty',true);
					$pw_cart_roles = get_post_meta($pr,'pw_cart_roles',true);
					$pw_to = get_post_meta($pr,'pw_to',true);
					$pw_condition = get_post_meta($pr,'pw_condition',true);
					$this->rules[$pr]=array(
						"pw_type"=>$pw_type,
						"pw_discount"=>$pw_discount,
						"pw_type_conditions"=>$pw_type_conditions,
						"pw_users"=>$pw_users,
						"pw_capabilities"=>$pw_capabilities,
						"pw_roles"=>$pw_roles,
						"pw_product"=>$pw_product,
						"pw_cart_roles"=>$pw_cart_roles,
						"pw_discount_qty"=>$pw_discount_qty,
						"pw_from"=>$pw_from,
						"pw_condition"=>$pw_condition,
						"pw_to"=>$pw_to,
					);
				}

				$RulesAplly = array();
				$cart_subtotal=0;$sum=0;$sum_discount=0;$max_discount=0;
				$pw_matched_cart= get_option('pw_matched_cart');
				$count_of_cart=count($woocommerce->cart->cart_contents);
				
				foreach ($woocommerce->cart->cart_contents as $key => $item) {

					$tedad = (isset($item['quantity']) && $item['quantity']) ? $item['quantity'] : 1;
					$cart_subtotal += $item['data']->get_price() * $tedad;
				}


				foreach ($this->rules as $rule_key => $rule) {
					if ($this->check_candition_rules_cart($rule,$cart_subtotal,$count_of_cart)) {
						$discount=$this->price_discunt($cart_subtotal,$rule['pw_type_conditions'],$rule['pw_discount']);
						
						
						if($discount<=0)
							continue;
						if($pw_matched_cart=="big")
						{
							if($discount>$max_discount)
								$max_discount=$discount;
						}
						else
						{
                            $sum_discount += $discount;
							$sum=$cart_subtotal - $sum_discount;
							if($sum>=0)
								$cart_subtotal=$sum;
							else
								$cart_subtotal=0;
						}
					}
				}
				
				if($pw_matched_cart=="big")
				{
					$sum_discount=$max_discount;
					$sum=$cart_subtotal - $max_discount;
					if($sum>=0)
						$cart_subtotal=$sum;
					else
						$cart_subtotal=0;					
				}
				
				if($sum_discount>0)
				{
					//echo $sum_discount;
					//$pw_flashsale_Cart->discount_session=$sum_discount;
					$this->coupon_cart =$sum_discount;
					//$woocommerce->session->wc_fals_sale_discount_code =$sum_discount;
					add_filter('woocommerce_get_shop_coupon_data', array($this, 'add_coupon'), 10, 2);		
					add_action('woocommerce_after_calculate_totals', array($this, 'apply_fake_coupon'));
					
				//	add_filter('woocommerce_cart_totals_coupon_label',array($this, 'dynamic_label_coupon'), 10, 2);
				//	add_action('woocommerce_before_calculate_totals',array($this, 'apply_coupon'));
				//	add_action('woocommerce_cart_updated', array($this, 'apply_coupon_cart_discount'));		
				//	add_action('woocommerce_removed_coupon', array($this, 'apply_discount') );
				}	
				else{
					
				}
				
				
			}	
			
			public function get_wc_version($version)
			{
				if (defined('WC_VERSION') && WC_VERSION) {
					return version_compare(WC_VERSION, $version, '>=');
				}
				else if (defined('WOOCOMMERCE_VERSION') && WOOCOMMERCE_VERSION) {
					return version_compare(WOOCOMMERCE_VERSION, $version, '>=');
				}
				else {
					return false;
				}
			}
		
			public function add_coupon($param, $code)
			{
				if ($code == apply_filters('woocommerce_coupon_code', 'DISCOUNT')) {
					$coupon = array(
						'id'                            => 8877122321,
						'type'                          => 'fixed_cart',
						'amount'                        => $this->coupon_cart,
						'individual_use'                => ($this->get_wc_version('3.0') ? false : 'no'),
						'product_ids'                   => array(),
						'exclude_product_ids'           => array(),
						'usage_limit'                   => '',
						'usage_limit_per_user'          => '',
						'limit_usage_to_x_items'        => '',
						'usage_count'                   => '',
						'expiry_date'                   => '',
						'apply_before_tax'              => 'yes',
						'free_shipping'                 => ($this->get_wc_version('3.0') ? false : 'no'),
						'product_categories'            => array(),
						'exclude_product_categories'    => array(),
						'exclude_sale_items'            => ($this->get_wc_version('3.0') ? false : 'no'),
						'minimum_amount'                => '',
						'maximum_amount'                => '',
						'customer_email'                => '',
					);

					return $coupon;
				}
			}

			public function apply_fake_coupon()
			{
				global $woocommerce;

				$coupon_code = apply_filters('woocommerce_coupon_code', 'DISCOUNT');
				$the_coupon = new WC_Coupon($coupon_code);

				if ($the_coupon->is_valid() && !$woocommerce->cart->has_discount($coupon_code)) {

					// Do not apply coupon with individual use coupon already applied
					if ($woocommerce->cart->applied_coupons) {
						foreach ($woocommerce->cart->applied_coupons as $code) {
							$coupon = new WC_Coupon($code);

							if (!$this->get_wc_version('3.0') && $coupon->individual_use == 'yes') {
								return false;
							}
							else if ($this->get_wc_version('3.0') && $coupon->individual_use) {
								return false;
							}
						}
					}

					// Add coupon
					$woocommerce->cart->applied_coupons[] = $coupon_code;
					do_action('woocommerce_applied_coupon', $coupon_code);

					return true;
				}
			}
		
			function apply_coupon_cart_discount() {
				$coupon       = false;
				$discount=$this->coupon_cart;
				if ( version_compare( WC()->version, '2.7', '<' ) ) {
					$coupon                = new WC_Coupon( $coupon_label );
					$coupon->coupon_amount = $this->get_discount_amount();
				} else {
					$coupons_in_cart = WC()->cart->get_applied_coupons();

					foreach ( $coupons_in_cart as $coupon_in_cart_code ){
						$coupon_in_cart = new WC_Coupon( $coupon_in_cart_code );
						$meta = 1;
						if ( ! empty( $meta ) ) {
							$coupon = $coupon_in_cart;
							$coupon_label = $coupon_in_cart_code;
							break;
						}
					}
					if( ! $coupon ){
						$coupon_label = apply_filters( 'ywdpd_coupon_code', uniqid( strtolower( $coupon_label ) ), $coupon_label );
						$coupon       = new WC_Coupon( $coupon_label );
					}

					if ( $coupon->is_valid() ) {
						$coupon->set_amount( $discount );
					} else {
						$args = array(
							'id'             => false,
							'discount_type'  => 'fixed_cart',
							'amount'         => $discount,
							'individual_use' => false,
							'free_shipping'  => false,
							'usage_limit'    => 1,
						);

						$coupon->add_meta_data( 'ywdpd_coupon', 1 );
						$coupon->read_manual_coupon( $coupon_label, $args );
					}

					$coupon->save();
				}

				if ( $coupon->is_valid() && ! WC()->cart->has_discount( $coupon_label ) ) {
					WC()->cart->add_discount( $coupon_label );
				}

			}
			
			public function dynamic_label_coupon( $string, $coupon ) {

				if ( version_compare( WC()->version, '2.7', '>' ) ) {
					$is_ywdpd     = 1;
					$coupon_label = 'ds';
				}else{
					$is_ywdpd     = ( $coupon->code == $this->label_coupon);
					$coupon_label = YITH_WC_Dynamic_Pricing()->get_option( 'coupon_label' );
				}

				return $is_ywdpd ? esc_html( __( $coupon_label, 'ywdpd' ) ) : $string;
			}
		
	/*		public function add_coupon($param, $code)
			{

				global $pw_flashsale_Cart;
				if ($code == apply_filters('woocommerce_coupon_code', 'Cart redemption')) {
					$coupon = array(
						'id'                            => 887712,
						'type'                          => 'fixed_cart',
						'amount'                        => $this->coupon_cart,
						'individual_use'                => 'no',
						'product_ids'                   => array(),
						'exclude_product_ids'           => array(),
						'usage_limit'                   => '',
						'usage_limit_per_user'          => '',
						'limit_usage_to_x_items'        => '',
						'usage_count'                   => '',
						'expiry_date'                   => '',
						'apply_before_tax'              => 'yes',
						'free_shipping'                 => 'no',
						'product_categories'            => array(),
						'exclude_product_categories'    => array(),
						'exclude_sale_items'            => 'no',
						'minimum_amount'                => '',
						'maximum_amount'                => '',
						'customer_email'                => '',
					);
					return $coupon;
				}				
			}
		*/	
			public function apply_coupon()
			{
				global $woocommerce;
				$curent_coupon = new WC_Coupon(apply_filters('woocommerce_coupon_code', 'Cart redemption'));

				if ($curent_coupon->is_valid() && !$woocommerce->cart->has_discount('Cart redemption')) {
					if ($woocommerce->cart->applied_coupons) {
						foreach ($woocommerce->cart->applied_coupons as $code) {
							$coupon = new WC_Coupon($code);

							if ($coupon->individual_use == 'yes') {
								return false;
							}
						}
					}
					$woocommerce->cart->applied_coupons[] = apply_filters('woocommerce_coupon_code', 'Cart redemption');

					return true;
				}
			}
/**/			
			public function price_discunt($price, $type,$discount)
			{
				if($price < 0)
					$price = 0;
				else
					$price = $price;
				
				switch ($type)
				{
					case 'percent':
						$ret = $price * ($discount / 100);
					break;
					
					case 'price':
						$ret = $discount;
					break;
				}
				if($ret<0)
					return 0;
				else
					return $ret;
			}			
			public function check_candition_rules_cart($rule,$cart_subtotal,$count_of_cart)
			{
				if (isset($rule['pw_to']) && !empty($rule['pw_to']) && (strtotime($rule['pw_to']) < strtotime(current_time( 'mysql' ))))
					return false;

				if (isset($rule['pw_from']) && !empty($rule['pw_from']) && (strtotime($rule['pw_from']) > strtotime(current_time( 'mysql' )))) 
					return false;
				
				foreach($rule['pw_condition'] as $key => $condition)
				{
					//print_r($rule['pw_condition']);
					//echo $key;
					switch ($key) {
						case 'pw_product':
							
							if (!$this->products_cart($condition) && $condition!="") {
								return false;
							}
						break;
						
						case 'pw_except_product':
							if ($this->products_cart($condition) && $condition!="") {
								return false;
							}
						break;
						
						case 'pw_product_category':
							if (!$this->categories_cart($condition) && $condition!="") {
								return false;
							}
						break;
						
						case 'pw_except_product_category':
							if ($this->categories_cart($condition) && $condition!="") {
								return false;
							}
						break;
						
						case 'pw_product_tag':
							if (!$this->tags_cart($condition) && $condition!="") {
								return false;
							}
						break;
						
						case 'pw_except_product_tag':
							if ($this->tags_cart($condition) && $condition!="") {
								return false;
							}
						break;
						
						case 'pw_product_brand':
							if (!$this->brands_cart($condition) && $condition!="") {
								return false;
							}
						break;
						
						case 'pw_except_product_brand':
							if ($this->brands_cart($condition) && $condition!="") {
								return false;
							}
						break;

						case 'subtotal_least':
							if ($cart_subtotal<$condition && $condition!="") {
								return false;
							}
						break;
						
						case 'subtotal_less_than':
							if ($cart_subtotal>=$condition && $condition!="") {
								return false;
							}
						break;

						case 'count_item_least':

							if ($count_of_cart < $condition && $condition!="") {
								return false;
							}
                        break;

						case 'count_less_than':

							if ($count_of_cart >= $condition && $condition!="") {
								echo 'a';
								return false;
							}
                        break;
						
						case 'user_capabilities_in_list':
							if(get_current_user_id() == 0 && $condition!="")
								return false;
							if(count(array_intersect(PW_Discount_Price::user_capabilities(), $condition)) == 0 && $condition!="")
								return false;
                        break;
						
						case 'user_role_in_list':
							if(get_current_user_id() == 0 && $condition!="")
								return false;
							if(count(array_intersect(PW_Discount_Price::pw_user_roles(), $condition)) == 0 && $condition!="")
								return false;
                        break;		
						
						default:
							break;						
					}
				}
				return true;				
			}			

			public function brands_cart($brand)
			{
				global $woocommerce;
				foreach ($woocommerce->cart->cart_contents as $item) {
					$brands = wp_get_post_terms($item['product_id'], 'product_brand');
					foreach ($brands as $b) {
						if (in_array($cat->term_id, $brand)) {
							return true;
						}
					}
				}
				return false;
			}
			
			public function categories_cart($category)
			{
				global $woocommerce;
				foreach ($woocommerce->cart->cart_contents as $item) {
					$categories = wp_get_post_terms($item['product_id'], 'product_cat');
					foreach ($categories as $cat) {
						if (in_array($cat->term_id, $category)) {
							return true;
						}
					}
				}
				return false;
			}
			
			
			public function tags_cart($tag)
			{
				global $woocommerce;
				foreach ($woocommerce->cart->cart_contents as $item) {
					$tags = wp_get_post_terms($item['product_id'], 'product_tag');
					foreach ($tags as $t) {
						if (in_array($t->term_id, $tag)) {
							return true;
						}
					}
				}

				return false;
			
			}
			public function products_cart($product)
			{
				global $woocommerce;
				foreach ($woocommerce->cart->cart_contents as $item)
					if (in_array($item['product_id'], $product))
						return true;
				return false;
			}
		
		}
	new PW_Discount_Cart();
}

/*
//add_action( 'woocommerce_loaded', 'pw_cashback_woocommerce_loaded' );
function pw_cashback_woocommerce_loaded() {
	add_action('woocommerce_cart_loaded_from_session', 'apply_coupons', 100);
	if ( version_compare( WOOCOMMERCE_VERSION, "2.1.0" ) >= 0 ) {
		//add_filter( 'woocommerce_cart_item_price','pw_cashback_display_cart_item_price_html', 10, 3 );
	} else {
		//add_filter( 'woocommerce_cart_item_price_html', 'pw_cashback_display_cart_item_price_html' , 10, 3 );
	}
}
//add_action( 'woocommerce_cart_loaded_from_session', 'on_cart_loaded_from_session', 99, 1 );
 
function apply_coupons($cart) {
	global $woocommerce;
//	if ( ! is_cart())
//		return;
//	if ( $woocommerce->cart->has_discount($woocommerce->session->wc_fals_sale_discount_code ) )
//		return;		
	//$amount = floatval( preg_replace( '#[^\d.]#', '', $woocommerce->cart->get_cart_total() ) );

	$amount=0;
	foreach ($woocommerce->cart->cart_contents as $cart_item_key => $cart_item)
	{
		
		$quantity = (isset($cart_item['quantity']) && $cart_item['quantity']) ? $cart_item['quantity'] : 1;
	//	echo $cart_item['data']->get_price();
		$amount += $cart_item['data']->get_price() * $quantity;		
	}

	if($amount>0)
	{
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
		$pw_discount=$price_adjusted=false;
		foreach($matched_products as $pr)
		{
			$result=0;
			$pw_type = get_post_meta($pr,'pw_type',true);
			if($pw_type=="cart")
			{
				$pw_to=strtotime(get_post_meta($pr,'pw_to',true));
				$pw_from=strtotime(get_post_meta($pr,'pw_from',true));
				$blogtime = strtotime(current_time( 'mysql' ));
				if($pw_to=="")
				{
					$pw_from=$blogtime-1000;
					$pw_to=$blogtime+1000;
				}
				if($blogtime<$pw_to && $blogtime>$pw_from)
				{
					$pw_cart_roles = get_post_meta($pr,'pw_cart_roles',true);
					$pw_roles = get_post_meta($pr,'pw_roles',true);
					$pw_capabilities = get_post_meta($pr,'pw_capabilities',true);
					$pw_users = get_post_meta($pr,'pw_users',true);
				//	print_r ($pw_roles);
					if(($pw_cart_roles == 'roles' && empty($pw_roles )) || ($pw_cart_roles == 'capabilities' && empty($pw_capabilities )) || ($pw_cart_roles == 'users' && empty($pw_users )))
						$result = 1;
					//For Check
					if ($pw_cart_roles == 'roles' && isset($pw_roles) && is_array($pw_roles)) {
						if (is_user_logged_in()) {
							foreach ($pw_roles as $role) {
								if (current_user_can($role)) {
								
									$result = 1;
									break;
								}
							}
						}
					}//End check
					//For Check capabilities
					if ($pw_cart_roles == 'capabilities' && isset($pw_capabilities) && is_array($pw_capabilities)) {
						if (is_user_logged_in()) {
							foreach ($pw_capabilities as $capabilities) {
								if (current_user_can($capabilities)) {
									$result = 1;
									break;
								}
							}
						}
					}//End check capabilities
					
					//For Check User's
					if ($pw_cart_roles == 'users' && isset($pw_users) && is_array($pw_users)) {
						if (is_user_logged_in()) {
							if (in_array(get_current_user_id(), $pw_users)){
								$result = 1;
								goto br_cart;
							}
						}
					}//End Check Users
					
					//echo '<br/><br/>';
					if($result==1 || $pw_cart_roles == 'everyone')
					{
						$pw_matched_cart= get_option('pw_matched_cart');
						$pw_type_conditions= get_post_meta($pr,'pw_type_conditions',true);
						//echo $pw_type_conditions;
						if($pw_type_conditions=="total")
						{
							$pw_discount_qty= get_post_meta($pr,'pw_discount_qty',true);									
							if(is_array($pw_discount_qty))
							{
								foreach($pw_discount_qty as $discount_qty)
								{
									$min=$max=$discount="";
									$min=$discount_qty['min'];
									$max=$discount_qty['max'];
									echo '<br><br><br>';
									//echo  $woocommerce->cart->get_cart_total();
									if($amount>=$min && $amount<=$max)
									{
										//echo $min .'-'.$max.'-'.@$discount_qty['discount'].'-'.$amount;
										if($pw_matched_cart=="all")
										{
											if ( false !== strpos( @$discount_qty['discount'], '%' ))										
												$pw_discount +=calculate_discount_modifiera(@$discount_qty['discount'],$amount);
											else
												$pw_discount+=@$discount_qty['discount'];
											goto br_cart;	
										}
										elseif($pw_matched_cart=="only")
										{
											if ( false !== strpos( @$discount_qty['discount'], '%' ))
												$pw_discount +=calculate_discount_modifiera(@$discount_qty['discount'],$amount);
											else
												$pw_discount+=@$discount_qty['discount'];
											//$pw_discount=@$discount_qty['discount'];
											goto br_cart;
										}
									}
								}
							}
						}
						elseif($pw_type_conditions=="products")
						{
							$pw_product= get_post_meta($pr,'pw_product',true);
							foreach ($woocommerce->cart->cart_contents as $cart_item_key => $cart_item)
							{
								if (is_array($pw_product) && in_array($cart_item['product_id'], $pw_product))
								{
									if($pw_matched_cart=="all")
									{
										$pw_disc = get_post_meta($pr,'pw_discount',true);
										if ( false !== strpos( $pw_disc, '%' ))
											$pw_discount += calculate_discount_modifiera( $pw_disc, $amount );
										else
											$pw_discount +=$pw_disc;
										goto br_cart;
									}	
									elseif($pw_matched_cart=="only")
									{
										$pw_disc = get_post_meta($pr,'pw_discount',true);
										if ( false !== strpos( $pw_disc, '%' ))
											$pw_discount += calculate_discount_modifiera( $pw_disc, $amount );
										else
											$pw_discount +=$pw_disc;
										goto br_cart;
									}
								}
							}
							
						}
						//echo '<br/><br/>';
					}
				}
			}
		}//end foreach
		//echo $pw_discount;
		br_cart:
		if($pw_discount!=false)
		{
			echo 'a';
			if ( false !== strpos( $pw_discount, '%' ))
			{
				$num_decimals = apply_filters( 'woocommerce_wc_pricing_get_decimals', (int) get_option( 'wc_price_num_decimals' ) );			
				$max_discount = calculate_discount_modifiera( $pw_discount, $amount );
				$price_adjusted = round(floatval($max_discount), (int) $num_decimals );
			}
			else
				$price_adjusted=$pw_discount;

			if($price_adjusted!=false)
			{
				$woocommerce->session->wc_fals_sale_discount_code =$price_adjusted;
				add_filter('woocommerce_get_shop_coupon_data','add_coupon_v', 10, 2);
                add_action('woocommerce_before_calculate_totals','apply_coupon');
			}
		}
	}//end if 
	
}

function apply_coupon()
{
	global $woocommerce;
	$the_coupon = new WC_Coupon(apply_filters('woocommerce_coupon_code', 'Cart redemption'));

	if ($the_coupon->is_valid() && !$woocommerce->cart->has_discount('Cart redemption')) {

		// Do not apply coupon with individual use coupon already applied
		if ($woocommerce->cart->applied_coupons) {
			foreach ($woocommerce->cart->applied_coupons as $code) {
				$coupon = new WC_Coupon($code);

				if ($coupon->individual_use == 'yes') {
					return false;
				}
			}
		}
		$woocommerce->cart->applied_coupons[] = apply_filters('woocommerce_coupon_code', 'Cart redemption');

		return true;
	}
}

function add_coupon_v($param, $code)
{
	global $woocommerce;
	if ($code == apply_filters('woocommerce_coupon_code', 'Cart redemption')) {
		$coupons = array(
			'id' => 887712,
			'type' => 'fixed_cart',
			'amount' => $woocommerce->session->wc_fals_sale_discount_code,
			'individual_use' => 'no',
			'product_ids' => array(),
			'exclude_product_ids' => array(),
			'usage_limit' => '',
			'usage_limit_per_user' => '',
			'limit_usage_to_x_items' => '',
			'usage_count' => '',
			'expiry_date' => '',
			'apply_before_tax' => 'yes',
			'free_shipping' => 'no',
			'product_categories' => array(),
			'exclude_product_categories' => array(),
			'exclude_sale_items' => 'no',
			'minimum_amount' => '',
			'customer_email' => array(),
		);
		return $coupons;
	}
}


function pw_cashback_display_cart_item_price_html( $html, $cart_item ) {
		if (isset($cart_item['discounts'])) {
			$_product = $cart_item['data'];
//		print_r($cart_item['discounts']);
		if (function_exists('get_product')) {
			$price_adjusted = get_option('woocommerce_tax_display_cart') == 'excl' ? $_product->get_price_excluding_tax() : $_product->get_price_including_tax();
			$price_base = $cart_item['discounts']['display_price'];
		} else {
			if (get_option('woocommerce_display_cart_prices_excluding_tax') == 'yes') :
				$price_adjusted = $cart_item['data']->get_price_excluding_tax();
				$price_base = $cart_item['discounts']['display_price'];
			else :
				$price_adjusted = $cart_item['data']->get_price();
				$price_base = $cart_item['discounts']['display_price'];
			endif;
		}
			//$price_adjusted ="10";
			if (!empty($price_adjusted) || $price_adjusted === 0) {
				if (apply_filters('wc_dynamic_pricing_use_discount_format', true)) {
					$html = '<del>' . wc_price($price_base) . '</del><ins> ' . wc_price($price_adjusted) . '</ins>';
				} else {
					$html = '<span class="amount">' . wc_price($price_adjusted). '</span>';
				}
			}
		}
	//	return 'asdsad';
		return $html;
}


function sort_by_price($cart_item_a, $cart_item_b) {
	return $cart_item_a['data']->get_price() > $cart_item_b['data']->get_price();
}

function on_cart_loaded_from_session( $cart ) {
	global $woocommerce;

	$sorted_cart = array();
	if ( sizeof( $cart->cart_contents ) > 0 ) {
		foreach ( $cart->cart_contents as $cart_item_key => $values ) {
			//echo 'a';
			$sorted_cart[$cart_item_key] = $values;
		}
	}

	//Sort the cart so that the lowest priced item is discounted when using block rules.
	@uasort( $sorted_cart, 'sort_by_price' );
	//print_r($sorted_cart);
	//adjust_cart( $sorted_cart );
	adjust_cart_rule( $sorted_cart );

}



function get_cart_item_categories($cart_item)
{
	$categories = array();
	$current = wp_get_post_terms($cart_item['data']->id, 'product_cat');
	foreach ($current as $category) {
		$categories[] = $category->term_id;
	}
	return $categories;
}
function get_adjact_item_categories($category)
{
	$categories = array();
	$current = wp_get_post_terms($cart_item['data']->id, 'product_cat');
	foreach ($current as $category) {
		$categories[] = $category->term_id;
	}
	return $categories;
}
function get_cart_item_tags($cart_item)
{
	$tags = array();
	$current = wp_get_post_terms($cart_item['data']->id, 'product_tag');
	foreach ($current as $tag) {
		$tags[] = $tag->term_id;
	}
	return $tags;
}
function get_cart_item_brand($cart_item)
{
	$brands = array();
	$current = wp_get_post_terms($cart_item['data']->id, 'product_brand');
	foreach ($current as $brand) {
		$brands[] = $brand->term_id;
	}
	return $brands;
}

function adjust_cart_rule( $cart ) {
	global $woocommerce,$wpdb;
	$arr_cart= array();
	foreach ( $cart as $cart_item_key => $cart_item )
	{
		$arr_cart[$cart_item['data']->id]=array(
			"id"=>$cart_item['data']->id,
			"orginal_price" =>$cart_item['data']->price,
			"price_adjusted"=>0,
			"quantity"=>$cart_item['quantity'],
			'lock'=>'no',
			'lock_sp'=>'no',
		);
	}

	$arr = array();
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
		$result=0;
		$pw_type = get_post_meta($pr,'pw_type',true);
		if($pw_type=="flashsale" || $pw_type=="special" || $pw_type=="quantity")
		{
			$pw_to=strtotime(get_post_meta($pr,'pw_to',true));
			$pw_from=strtotime(get_post_meta($pr,'pw_from',true));
			$blogtime = strtotime(current_time( 'mysql' ));
			$pw_apply_to=get_post_meta($pr,'pw_apply_to',true);
		//Check For User Role
			$pw_cart_roles = get_post_meta($pr,'pw_cart_roles',true);
			$pw_roles = get_post_meta($pr,'pw_roles',true);
			$pw_capabilities = get_post_meta($pr,'pw_capabilities',true);
			$pw_users = get_post_meta($pr,'pw_users',true);
			$pw_products_to_adjust = get_post_meta($pr,'pw_products_to_adjust',true);
			$quantity_base = get_post_meta($pr,'quantity_base',true);
			
			if(($pw_cart_roles=="everyone") || ($pw_cart_roles == 'roles' && empty($pw_roles )) || ($pw_cart_roles == 'capabilities' && empty($pw_capabilities )) || ($pw_cart_roles == 'users' && empty($pw_users )))
				$result = 1;
			//For Check Roles
			if ($pw_cart_roles == 'roles' && isset($pw_roles) && is_array($pw_roles)) {
				if (is_user_logged_in()) {
					foreach ($pw_roles as $role) {
						if (current_user_can($role)) {
							$result = 1;
							break;
						}
					}
				}
			}//End check Roles
			//For Check capabilities
			if ($pw_cart_roles == 'capabilities' && isset($pw_capabilities) && is_array($pw_capabilities)) {
				if (is_user_logged_in()) {
					foreach ($pw_capabilities as $capabilities) {
						if (current_user_can($capabilities)) {
							$result = 1;
							break;
						}
					}
				}
			}//End check capabilities
			//For Check User's
			if ($pw_cart_roles == 'users' && isset($pw_users) && is_array($pw_users)) {
				if (is_user_logged_in()) {
					if (in_array(get_current_user_id(), $pw_users)){
						$result = 1;
					}
				}
			}//End Check Users
			
			//Check Time Rules
			if($pw_to=="" && ($pw_type=="quantity" || $pw_type=="special"))
			{
				
				$pw_from=$blogtime-1000;
				$pw_to=$blogtime+1000;
			}
			
			if($blogtime>$pw_to && $blogtime<$pw_from)
				$result=0;
			
            if ($pw_to > $blogtime) {
               $result=0;
            }
			
			if ($pw_from < $blogtime ) {
               $result=0;
            }
					
			//Check If Customer can give discount in this rule
			if($result==0)
				goto break_one_rule;
				
			
			//Foreach Cart Item
			foreach ( $cart as $cart_item_key => $cart_item )
			{
				$is_ok=false;$pw_discount="";
				if($pw_apply_to=="pw_product" && !in_array($cart_item['data']->id, get_post_meta($pr,'pw_product',true)))
					goto break_one_cart;
				if($pw_apply_to=="pw_except_product" && !in_array($cart_item['data']->id, get_post_meta($pr,'pw_except_product',true)))
					goto break_one_cart;
				if($pw_apply_to=="pw_product_category" && count(array_intersect(get_cart_item_categories($cart_item), get_post_meta($pr,'pw_product_category',true))) <= 0)
					goto break_one_cart;
				if($pw_apply_to=="pw_except_product_category" && count(array_intersect(get_cart_item_categories($cart_item), get_post_meta($pr,'pw_except_product_category',true))) > 0)
					goto break_one_cart;
				if($pw_apply_to=="pw_product_tag" && count(array_intersect(get_cart_item_tags($cart_item), get_post_meta($pr,'pw_product_tag',true))) <= 0)
					goto break_one_cart;
				if($pw_apply_to=="pw_except_product_tag" && count(array_intersect(get_cart_item_tags($cart_item), get_post_meta($pr,'pw_except_product_tag',true))) > 0)
					goto break_one_cart;
				if(defined('plugin_dir_url_pw_woo_brand'))
				{
					if($pw_apply_to=="pw_product_brand" && count(array_intersect(get_cart_item_brand($cart_item), get_post_meta($pr,'pw_product_brand',true))) <= 0)
						goto break_one_cart;
					if($pw_apply_to=="pw_except_product_brand" && count(array_intersect(get_cart_item_brand($cart_item), get_post_meta($pr,'pw_except_product_brand',true))) > 0)
						goto break_one_cart;
				}
				
				//echo $cart_item['data']->id.'-';
			//echo count(array_intersect(get_cart_item_categories($cart_item), get_post_meta($pr,'pw_product_category',true)));
				//echo $pw_apply_to;
				
				//Get Price Orginal
				$original_price=get_price_to_discount( $cart_item, $cart_item_key );								
				
				//Pw_type=Flashsale
				if($pw_type=="flashsale")
				{
					$pw_discount=0;
					$pw_matched= get_post_meta($pr,'pw_matched',true);
					$pw_type_discount= get_post_meta($pr,'pw_type_discount',true);
					
					$pw_dis = get_post_meta($pr,'pw_discount',true);
					if ( $pw_type_discount=="percent")
						$pw_discount = calculate_modifiera( $pw_dis, $original_price );
					else
						$pw_discount =$pw_dis;

					//Check If matched Rules
					if($pw_matched=="only" && $arr_cart[$cart_item['data']->id]['lock']=="no")
					{
						$arr_cart[$cart_item['data']->id]['lock']='yes';
						$arr_cart[$cart_item['data']->id]['price_adjusted']=$pw_discount;
					}
					elseif($pw_matched=="all" && $arr_cart[$cart_item['data']->id]['lock']=="no")
						$arr_cart[$cart_item['data']->id]['price_adjusted']+=$pw_discount;

				}//endif pw_type=Flashsale
				
				//Else Pw_type=special || Quentity
				elseif($pw_type=="special" || $pw_type=="quantity")
				{
					//Check Products To Adjust
					//in_array($cart_item['data']->id, get_post_meta($pr,'pw_products_to_adjust_category',true)))
				
				//	if($pw_products_to_adjust=="other_categories" && count(array_intersect(get_cart_item_categories($cart_item), get_post_meta($pr,'pw_products_to_adjust_category',true))) > 0)
				//		$is_ok=true;
				//	elseif($pw_products_to_adjust=="other_products" && in_array($cart_item['data']->id, get_post_meta($pr,'pw_products_to_adjust_products',true)))
			//			$is_ok=true;
				//	elseif($pw_products_to_adjust=="matched")
				//		$is_ok=true;
					
					//elseif($pw_products_to_adjust=="other_products" && in_array($cart_item['data']->id, get_post_meta($pr,'pw_products_to_adjust_products',true)))
					//	$is_ok=true;
					//elseif($pw_products_to_adjust=="matched")
					//	$is_ok=true;
					//if product_to_adjust is set and ok
				//	echo $cart_item['data']->id.'-';
					//echo $is_ok.'-';
					//if($is_ok!=true)
					//	goto break_one_cart;
					//print_r(get_post_meta($pr,'pw_products_to_adjust_products',true));
					
					//pw_products_to_adjust_products
					// Check $pw_type=="special"
					if($pw_type=="special")
					{
						$loop= get_post_meta($pr,'amount_to_adjust',true);
						$is_true=false;
						$discount_calc=0;$pricetotall=$pricetotalla=0;$pw_products_to_adjust_category="";$price_adjusted_p=false;
						$pw_matched= get_post_meta($pr,'pw_matched',true);
						$amount_to_purchase = get_post_meta($pr,'amount_to_purchase',true);
						$amount_to_adjust =get_post_meta($pr,'amount_to_adjust',true);
						$adjustment_type=get_post_meta($pr,'adjustment_type',true);
						$adjustment_value=get_post_meta($pr,'adjustment_value',true);
						$pricetotalla=$cart_item['quantity']*$original_price;
						$pw_products_to_adjust_category=get_post_meta($pr,'pw_products_to_adjust_category',true);
						//echo $cart_item['quantity'].'<br/>';
						if($quantity_base=="all")
						{
							if($pw_apply_to=="pw_product_category")
							{
								$counter_cat = array();
								foreach ( $cart as $cart_key => $item) {
									$cat = get_cart_item_categories($item);
									foreach ($cat as $id) {
										if (isset($counter_cat[$id]))
											$counter_cat[$id] += $item['quantity'];
										else 
											$counter_cat[$id] = $item['quantity'];
									}
								}
								$pw_product_category=get_post_meta($pr,'pw_product_category',true);
								foreach((array)$pw_product_category as $cat)
								{
									if(isset($counter_cat[$cat]) && $counter_cat[$cat]>=$amount_to_purchase)
									{
										$is_true=true;
										//break;
									}
								}
							}
							elseif($pw_apply_to=="pw_product_tag")
							{
								$counter_tag = array();
								foreach ( $cart as $cart_key => $item) {
									$tag = get_cart_item_tags($item);
									foreach ($tag as $id) {
										if (isset($counter_tag[$id]))
											$counter_tag[$id] += $item['quantity'];
										else 
											$counter_tag[$id] = $item['quantity'];
									}
								}
								$pw_product_tag=get_post_meta($pr,'pw_product_tag',true);
								foreach((array)$pw_product_tag as $tag)
								{
									if(isset($counter_tag[$tag]) && $counter_tag[$tag]>=$amount_to_purchase)
										$is_true=true;
								}							
							}
							elseif($pw_apply_to=="pw_product_brand")
							{
								$counter_brand = array();
								foreach ( $cart as $cart_key => $item) {
									$brand = get_cart_item_brand($item);
									foreach ($brand as $id) {
										if (isset($counter_brand[$id]))
											$counter_brand[$id] += $item['quantity'];
										else 
											$counter_brand[$id] = $item['quantity'];
									}
								}
								$pw_product_brand=get_post_meta($pr,'pw_product_brand',true);
								foreach((array)$pw_product_brand as $brand)
								{
									if(isset($counter_brand[$brand]) && $counter_brand[$brand]>=$amount_to_purchase)
										$is_true=true;
								}							
							}
							elseif($pw_apply_to=="pw_all_product")
							{
								$couner=0;
								foreach ( $cart as $cart_key => $item)
									$couner += $item['quantity'];

								if($couner>=$amount_to_purchase)
									$is_true=true;
							}
						}
						elseif($cart_item['quantity']>=$amount_to_purchase)
							$is_true=true;

						if($pw_products_to_adjust=="other_products")
						{
							//Foreach Cart For adjusted other_products
							if($is_true==true)
							{
								foreach ( $cart as $cart_key => $cart_i )
								{
									//echo $cart_i['data']->id;
									//Check If item in product_to_adjust
									$loop= get_post_meta($pr,'amount_to_adjust',true);
									if(in_array($cart_i['data']->id, get_post_meta($pr,'pw_products_to_adjust_products',true)) && $arr_cart[$cart_i['data']->id]['lock']=="no")
									{
										$original_p_s=get_price_to_discount( $cart_i, $cart_key );
										$cart_quantity= $cart_i['quantity'];
										if ( $adjustment_type=="percent" )
										{
											while($loop>0 && $cart_quantity>0)
											{
												$discount_calc += calculate_modifiera( $adjustment_value, $original_p_s );
												$loop--;
												$cart_quantity--;
											}
										}
										else
											$discount_calc = $amount_to_adjust*$adjustment_value;
										
										$discount_calc=$discount_calc/$cart_i['quantity'];
											//Foreach Amount_to_Adjust 		
										//Check If matched Rules
										if($pw_matched=="only" && $arr_cart[$cart_i['data']->id]['lock']=="no")
										{
											$arr_cart[$cart_i['data']->id]['lock']='yes';
											$arr_cart[$cart_i['data']->id]['price_adjusted']=$discount_calc;
											//break;
										}
										elseif($pw_matched=="all" && $arr_cart[$cart_i['data']->id]['lock']=="no")
										{
											$arr_cart[$cart_i['data']->id]['price_adjusted']+=$discount_calc;
											//break;
										}
									}//End if Check If item in product_to_adjust				
								}//End If $cart_i['quantity']>=$amount_to_purchase
							}//End Foreach adjusted other_products
						//count(array_intersect(get_cart_item_categories($cart_item), get_post_meta($pr,'pw_products_to_adjust_products',true))) > 0
						//	$is_ok=true;
						}
						//Else IF Adjust other_categories
						elseif($pw_products_to_adjust=="other_categories")
						{
							if($is_true==true)
							{
								//Foreach Cart For adjusted other_products
								foreach ( $cart as $cart_key => $cart_i )
								{
									//echo (count(array_intersect(get_cart_item_categories($cart_i), get_post_meta($pr,'pw_products_to_adjust_category',true))) > 0);
									
									//print_r(get_cart_item_categories($cart_i));
									//echo '@';
									//print_r(get_post_meta($pr,'pw_products_to_adjust_category',true));
									//echo '@';
									
									if((count(array_intersect(get_cart_item_categories($cart_i), get_post_meta($pr,'pw_products_to_adjust_category',true))) > 0) && $arr_cart[$cart_i['data']->id]['lock']=="no" )
									{
										$original_p_s=get_price_to_discount( $cart_i, $cart_key );
										$discount_calc=0;
										$cart_quantity= $cart_i['quantity'];
										//echo $original_p_s.'-';
										//print_r(get_cart_item_categories($cart_i));
										if ( $adjustment_type=="percent" )
										{
											while($loop>0  && $cart_quantity>0)
											{
												$discount_calc = calculate_modifiera( $adjustment_value, $original_p_s );
												$loop--;
												$cart_quantity--;
											}
										}
										else
										{
											while($loop>0  && $cart_quantity>0)
											{
												//echo $adjustment_value.'<br/>';
												$discount_calc += $adjustment_value;
												$loop--;
												$cart_quantity--;
											}
										}
										
										//echo $discount_calc;
										//print_r();
										$discount_calc=$discount_calc/$cart_i['quantity'];
										
										//echo $discount_calc.'<br/>';
										//Check If matched Rules
										if($pw_matched=="only" && $arr_cart[$cart_i['data']->id]['lock']=="no" && $arr_cart[$cart_i['data']->id]['lock_sp']=="no")
										{
											$arr_cart[$cart_i['data']->id]['lock']='yes';
											$arr_cart[$cart_i['data']->id]['lock_sp']='yes';
											$arr_cart[$cart_i['data']->id]['price_adjusted']=$discount_calc;
											//break;
										}
										elseif($pw_matched=="all" && $arr_cart[$cart_i['data']->id]['lock']=="no" && $arr_cart[$cart_i['data']->id]['lock_sp']=="no")
										{
											//echo 'dd';
											$arr_cart[$cart_i['data']->id]['lock_sp']='yes';
											$arr_cart[$cart_i['data']->id]['price_adjusted']+=$discount_calc;
											//break;
										}
									}
								}							
							//	print_r(get_cart_item_categories($cart_i));
							}//end if($cart_item['quantity']>=$amount_to_purchase)
						}//End Foreach Cart For adjusted other_products
						//Else IF Adjust matched
						elseif($pw_products_to_adjust=="matched")
						{
							if($is_true==true)
							{
								if ( $adjustment_type=="percent" )
								{
									$cart_quantity= $cart_i['quantity'];
									while($loop>0 && $cart_quantity>0)
									{
										$discount_calc += calculate_modifiera( $adjustment_value, $original_price );
										$loop--;
										$cart_quantity--;
									}
									//echo $discount_calc;
									//$temp=$cart_item['quantity']*$original_price;
									//$temp-=$discount_calc;
									//$temp=$temp/$cart_item['quantity'];
									//echo '@'.$temp.'@';
								}
								else
									$discount_calc = $amount_to_adjust*$adjustment_value;

								$discount_calc=$discount_calc/$cart_item['quantity'];									
								
								//Check If matched Rules
								if($pw_matched=="only" && $arr_cart[$cart_item['data']->id]['lock']=="no")
								{
									$arr_cart[$cart_item['data']->id]['lock']='yes';
									$arr_cart[$cart_item['data']->id]['price_adjusted']=$discount_calc;
								}
								elseif($pw_matched=="all" && $arr_cart[$cart_item['data']->id]['lock']=="no")
									$arr_cart[$cart_item['data']->id]['price_adjusted']+=$discount_calc;								
							}
						}//End Else IF Adjust matched

						//$arr_cart[$cart_item['data']->id]['price_adjusted']=$discount_calc;
						
					}//End if pw_type="special"

					// Check $pw_type=="quantity"
					elseif($pw_type=="quantity")
					{
						$pw_discount=0;$discount_calc=0;
						$pw_discount_qty= get_post_meta($pr,'pw_discount_qty',true);
						$pw_matched= get_post_meta($pr,'pw_matched',true);
						//Check if Qty is set
						if(is_array($pw_discount_qty))
						{
							if($pw_products_to_adjust=="other_products")
							{
									//Foreach Cart For adjusted other_products
									foreach ( $cart as $cart_key => $cart_i )
									{
										$original_p_s=get_price_to_discount( $cart_i, $cart_key );
										//Check If item in product_to_adjust
										if(in_array($cart_i['data']->id, get_post_meta($pr,'pw_products_to_adjust_products',true)) && $arr_cart[$cart_i['data']->id]['lock']=="no")
										{
											foreach($pw_discount_qty as $discount_qty)
											{
												$min=$max=$discount="";
												$min=$discount_qty['min'];
												$max=$discount_qty['max'];
												if($cart_item['quantity']>=$min && $cart_item['quantity']<=$max)
												{
													//if(get_option('pw_matched_rule','all')=="all"){}
													if ( false !== strpos( @$discount_qty['discount'], '%' ))
														$pw_discount = calculate_discount_modifiera( @$discount_qty['discount'], $original_p_s );
													else
														$pw_discount =@$discount_qty['discount'];
													//$pw_discount=$pw_discount/$cart_i['quantity'];			
													//Check If matched Rules
													if($pw_matched=="only" && $arr_cart[$cart_i['data']->id]['lock']=="no")
													{
														$arr_cart[$cart_i['data']->id]['lock']='yes';
														$arr_cart[$cart_i['data']->id]['price_adjusted']=$pw_discount;
														//break;
													}
													elseif($pw_matched=="all" && $arr_cart[$cart_i['data']->id]['lock']=="no")
													{
														$arr_cart[$cart_i['data']->id]['price_adjusted']+=$pw_discount;
														//break;
													}
												}
											}//End Foreach $pw_discount_qty
										}
									}
							}
							//Else IF Adjust other_categories
							elseif($pw_products_to_adjust=="other_categories")
							{
									//Foreach Cart For adjusted other_products
									foreach ( $cart as $cart_key => $cart_i )
									{
										$original_p_s=get_price_to_discount( $cart_i, $cart_key );
										//Check If item in product_to_adjust
										if((count(array_intersect(get_cart_item_categories($cart_i), get_post_meta($pr,'pw_products_to_adjust_category',true))) > 0) && $arr_cart[$cart_i['data']->id]['lock']=="no" )
										{
											foreach($pw_discount_qty as $discount_qty)
											{
												$min=$max=$discount="";
												$min=$discount_qty['min'];
												$max=$discount_qty['max'];
												if($cart_item['quantity']>=$min && $cart_item['quantity']<=$max)
												{
													//if(get_option('pw_matched_rule','all')=="all"){}
													if ( false !== strpos( @$discount_qty['discount'], '%' ))
														$pw_discount = calculate_discount_modifiera( @$discount_qty['discount'], $original_p_s );
													else
														$pw_discount =@$discount_qty['discount'];
														
													//$pw_discount=$pw_discount/$cart_i['quantity'];				
													//Check If matched Rules
													if($pw_matched=="only" && $arr_cart[$cart_i['data']->id]['lock']=="no")
													{
														$arr_cart[$cart_i['data']->id]['lock']='yes';
														$arr_cart[$cart_i['data']->id]['price_adjusted']=$pw_discount;
														//break;
													}
													elseif($pw_matched=="all" && $arr_cart[$cart_i['data']->id]['lock']=="no")
													{
														$arr_cart[$cart_i['data']->id]['price_adjusted']+=$pw_discount;
														//break;
													}
												}
											}//End Foreach $pw_discount_qty
										}
									}
							}
							//Else IF Adjust matched
							elseif($pw_products_to_adjust=="matched")
							{
								foreach($pw_discount_qty as $discount_qty)
								{
									$min=$max=$discount="";
									$min=$discount_qty['min'];
									$max=$discount_qty['max'];
									if($cart_item['quantity']>=$min && $cart_item['quantity']<=$max)
									{
										$pw_matched= get_post_meta($pr,'pw_matched',true);
										//if(get_option('pw_matched_rule','all')=="all"){}
										if ( false !== strpos( @$discount_qty['discount'], '%' ))
											$pw_discount = calculate_discount_modifiera( @$discount_qty['discount'], $original_price );
										else
											$pw_discount =@$discount_qty['discount'];
											
										//$pw_discount=$pw_discount/$cart_item['quantity'];				
										//echo $pw_discount.'-'.$discount_qty['discount'].'-'.$cart_i['quantity'];
										//Check If matched Rules
										if($pw_matched=="only" && $arr_cart[$cart_item['data']->id]['lock']=="no")
										{
											$arr_cart[$cart_item['data']->id]['lock']='yes';
											$arr_cart[$cart_item['data']->id]['price_adjusted']=$pw_discount;
										}
										elseif($pw_matched=="all" && $arr_cart[$cart_item['data']->id]['lock']=="no")
											$arr_cart[$cart_item['data']->id]['price_adjusted']+=$pw_discount;
									}
								}//End Foreach $pw_discount_qty
							}//End Else IF Adjust matched
						}//End If Check Qty					
						
					}//End if pw_type="quantity"
					
				}//End if pw_type=="special","quantity"
				break_one_cart:
			}//End Foreach Cart
		}//End If pw_type=="Quentity","flash_sale","special"
		break_one_rule:
	}// end Foreach Rules
	//print_r($arr_cart);
	
	foreach ( $cart as $cart_item_key => $cart_item )
	{
		$module="simple_category";
		$applied_rule_set_id="set_12";
		$price_adjusted=false;
		$price_adjusted_arr=$arr_cart[$cart_item['data']->id]['price_adjusted'];
		$original_price=$cart_item['data']->price;
		//Check If cart_item[id] and $arr_cart is equal		
		if($arr_cart[$cart_item['data']->id]['id']==$cart_item['data']->id && $price_adjusted_arr != 0 )
		{
			
			if ( $price_adjusted_arr != 0 &&  $price_adjusted_arr > 0 )
				$price_adjusted=$original_price-$price_adjusted_arr;
				//$price_adjusted=$price_adjusted_arr;
			//echo '**'.$original_price.'**';
			if($price_adjusted < 0)
				$price_adjusted=0;

			if( $price_adjusted!==false && floatval( $original_price ) != floatval( $price_adjusted ))
				apply_cart_item_adjustment( $cart_item_key, $original_price, $price_adjusted, $module, $applied_rule_set_id );		
		}//End Check If cart_item[id] and $arr_cart is equal
	}
	//foreach($arr_cart as $a)
	//	echo 'id:'.$a['id'].'/p:'.$a['price_adjusted'].'<br/>';
}

function adjust_cart( $cart ) {
	global $woocommerce,$wpdb;
//	if ( ! is_user_logged_in())
//	return;
 //	$arr_cart="";
//	foreach ( $cart as $cart_item_key => $cart_item )
//	{
//		$arr_cart[$cart_item['data']->id]=array(
//			"id"=>$cart_item['data']->id,
//			"orginal_price" =>$cart_item['data']->price,
//			"price_adjusted","",
//		);
//	}
	 
	//print_r($arr_cart);

	//print_r($arr_cart);
	foreach ( $cart as $cart_item_key => $cart_item ) {
		
		//echo $arr_cart[$cart_item['data']->id];
		
		//print_r($cart_item);
		$original_price=get_price_to_discount( $cart_item, $cart_item_key );
		$result=false;
		$price_adjusted=$result;
		$module="simple_category";
		$applied_rule_set_id="set_12";
		$pw_discount="";
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
		//print_r($matched_products);
		foreach($matched_products as $pr)
		{
			$arr="";
			$result = 0;
			$pw_to=strtotime(get_post_meta($pr,'pw_to',true));
			$pw_from=strtotime(get_post_meta($pr,'pw_from',true));
			$blogtime = strtotime(current_time( 'mysql' ));
			$pw_type = get_post_meta($pr,'pw_type',true);
			//Check For User Role
				$pw_cart_roles = get_post_meta($pr,'pw_cart_roles',true);
				$pw_roles = get_post_meta($pr,'pw_roles',true);
				$pw_capabilities = get_post_meta($pr,'pw_capabilities',true);
				$pw_users = get_post_meta($pr,'pw_users',true);
			//	print_r ($pw_roles);
				if(($pw_cart_roles == 'roles' && empty($pw_roles )) || ($pw_cart_roles == 'capabilities' && empty($pw_capabilities )) || ($pw_cart_roles == 'users' && empty($pw_users )))
					$result = 1;
				//For Check Roles
				if ($pw_cart_roles == 'roles' && isset($pw_roles) && is_array($pw_roles)) {
					if (is_user_logged_in()) {
						foreach ($pw_roles as $role) {
							if (current_user_can($role)) {
								$result = 1;
								break;
							}
						}
					}
				}//End check Roles
				//For Check capabilities
				if ($pw_cart_roles == 'capabilities' && isset($pw_capabilities) && is_array($pw_capabilities)) {
					if (is_user_logged_in()) {
						foreach ($pw_capabilities as $capabilities) {
							if (current_user_can($capabilities)) {
								$result = 1;
								break;
							}
						}
					}
				}//End check capabilities

				//For Check User's
				if ($pw_cart_roles == 'users' && isset($pw_users) && is_array($pw_users)) {
					if (is_user_logged_in()) {
						if (in_array(get_current_user_id(), $pw_users)){
							$result = 1;
						}
					}
				}//End Check Users
				
			if($result==1 || $pw_cart_roles == 'everyone')
			{	
				if($pw_to=="" && ($pw_type=="quantity" || $pw_type=="special"))
				{
					//echo $pr;
					$pw_from=$blogtime-1000;
					$pw_to=$blogtime+1000;
				}
				if($blogtime<$pw_to && $blogtime>$pw_from)
				{
					$arr= get_post_meta($pr,'pw_array',true);
					if($pw_type=="flashsale")
					{
					
						if (is_array($arr) && in_array($cart_item['product_id'], $arr))
						{
							$pw_matched= get_post_meta($pr,'pw_matched',true);
							if($pw_matched=="only")
							{
								$pw_dis = get_post_meta($pr,'pw_discount',true);
								if ( false !== strpos( $pw_dis, '%' ))
									$pw_discount += calculate_discount_modifiera( $pw_dis, $original_price );
								else
									$pw_discount +=$pw_dis;
								//$pw_discount+= get_post_meta($pr,'pw_discount',true);
								//echo '<br/><br/>'.$pr.'-'.$pw_discount;
								goto break_line;
							}
							elseif($pw_matched=="all")
							{
								$pw_dis = get_post_meta($pr,'pw_discount',true);
								if ( false !== strpos( $pw_dis, '%' ))
									$pw_discount += calculate_discount_modifiera( $pw_dis, $original_price );
								else
									$pw_discount +=$pw_dis;
								//$pw_discount+= get_post_meta($pr,'pw_discount',true);
								//echo '<br/><br/>'.$pr.'-'.$pw_discount;	
							}
						}
					}
					elseif($pw_type=="quantity")
					{
						$pw_discount_qty= get_post_meta($pr,'pw_discount_qty',true);
						//print_r($pw_discount_qty);
					//	echo '</br></br>';
						$arr= get_post_meta($pr,'pw_array',true);					
						if(is_array($pw_discount_qty))
						{
	//						echo $pr;
	//						print_r($pw_discount_qty);
							foreach($pw_discount_qty as $discount_qty)
							{
								$min=$max=$discount="";
								$min=$discount_qty['min'];
								$max=$discount_qty['max'];
								//$discount=@$discount_qty['discount'].'-';
								//echo '-'.$pr.'-'.$min.'-'.$max.'<br/>';
								if($cart_item['quantity']>=$min && $cart_item['quantity']<=$max)
								{
									if (is_array($arr) && in_array($cart_item['product_id'], $arr))
									{
										$pw_matched= get_post_meta($pr,'pw_matched',true);
										if($pw_matched=="only")
										{	
											if ( false !== strpos( @$discount_qty['discount'], '%' ))
												$pw_discount += calculate_discount_modifiera( @$discount_qty['discount'], $original_price );
											else
												$pw_discount +=@$discount_qty['discount'];
											goto break_line;
										}
										elseif($pw_matched=="all")
											if ( false !== strpos( @$discount_qty['discount'], '%' ))
												$pw_discount += calculate_discount_modifiera( @$discount_qty['discount'], $original_price );
											else
												$pw_discount +=@$discount_qty['discount'];
										$pw_discount+=@$discount_qty['discount'];
									}
									//echo '<br/><br/>'.$min.'-'.$max.'-'.$pw_discount;
									//break;
								}
							}
						}
					}
					elseif($pw_type=="special")
					{
					//	echo '<br><br>';
						$discount_calc=0;
						$pw_matched= get_post_meta($pr,'pw_matched',true);
						$amount_to_purchase = get_post_meta($pr,'amount_to_purchase',true);
						$amount_to_adjust =$loop= get_post_meta($pr,'amount_to_adjust',true);
						$price_adjusted_p=false;
						$pricetotall=$pricetotalla=0;
						$adjustment_value=get_post_meta($pr,'adjustment_value',true);
						$pricetotalla=$cart_item['quantity']*$original_price;
						$arr= get_post_meta($pr,'pw_array',true);								

						if (is_array($arr) && in_array($cart_item['product_id'], $arr))
						{						
							if($cart_item['quantity']>$amount_to_purchase)
							{
								if ( false !== strpos( $adjustment_value, '%' ) )
								{
									while($loop>0)
									{
										$discount_calc += calculate_discount_modifiera( $adjustment_value, $original_price );
										$loop--;
									}
									
								}
								else
								{
									$discount_calc = $amount_to_adjust*$adjustment_value;
									//echo '-'.$discount_calc.'-';
								}
								//echo '-'.$discount_calc.'-';
								if($pw_matched=="only")
								{
									$discount_calc=$discount_calc/$cart_item['quantity'];
									$pw_discount =$discount_calc;
									goto break_line;
								}
								else{
								//	echo $discount_calc .'-';
									$discount_calca=$discount_calc/$cart_item['quantity'];
									$pw_discount +=$discount_calca;
									//echo $discount_calca ;	
									}
								//echo $pw_discount ;
								//echo $original_price.'-';
							//	while($loop>0)
							//	{
							//		if ( false !== strpos( $adjustment_value, '%' ) )
							//		{
							//			//$num_decimals = apply_filters( 'woocommerce_wc_pricing_get_decimals', (int) get_option( 'wc_price_num_decimals' ) );			
							//			$max_discount += calculate_discount_modifiera( $adjustment_value, $original_price );
							//			//$price_adjusted_p+= round( floatval( $original_price ) - ( floatval( $max_discount  )), (int) $num_decimals );
							//		}
							//		else
							//		{
							//			$max_discount +=$adjustment_value;
							//			//$temp=$adjustment_value*$amount_to_adjust;
							//			//$pricetotall=$pricetotalla-$temp;
							//		}
							//		$loop--;
							//	}
							/	$pw_discount=$max_discount;
							//	
							//	$temp=$adjustment_value*$amount_to_adjust;
							//	$pricetotall=$pricetotalla-$temp;
								
							//	$temp=$amount_to_adjust*$original_price;
							//	//echo $amount_to_adjust;
							//	goto break_line;
							//	//if($price_adjusted_p!=false)
								//{
								//	if($price_adjusted_p==0)
								//	{
								///		$temp=$amount_to_adjust*$original_price;
								//		$pricetotall=$pricetotalla-$temp;
								//	}
								//	else
								//		$pricetotall=$pricetotalla-$price_adjusted_p;									
								//}
								//if($pricetotall<0)
								//	$pricetotall=0;
									
							//	if($pw_matched=="only")
							//		$price_adjusted=$pricetotall/$cart_item['quantity'];
							//	elseif($pw_matched=="all")
							//		$price_adjusted +=$pricetotall/$cart_item['quantity'];
								
							}
						}
						//if($pw_matched=="only")
							//goto break_line_sp;							
					}
				}
			}
		}
		break_line:
	//	echo '<br/>'.$pw_discount;
		if($pw_discount!="")
		{
			if ( false !== strpos( $pw_discount, '%' ) )
			{
				$num_decimals = apply_filters( 'woocommerce_wc_pricing_get_decimals', (int) get_option( 'wc_price_num_decimals' ) );			
				$max_discount = calculate_discount_modifiera( $pw_discount, $original_price );
				$price_adjusted = round( floatval( $original_price ) - ( floatval( $max_discount  )), (int) $num_decimals );
			}
			else
				$price_adjusted=$original_price-$pw_discount;
		//	echo $original_price;
		}
	//	echo $price_adjusted;
//$cart_item['product_id']
		break_line_sp:
		if ( $price_adjusted !== false && floatval( $original_price ) != floatval( $price_adjusted ) ) {
			apply_cart_item_adjustment( $cart_item_key, $original_price, $price_adjusted, $module, $applied_rule_set_id );
		}
	}
}

	function get_price_to_discount($cart_item, $cart_item_key) {
		global $woocommerce;

		$result = false;
		
		$filter_cart_item = $cart_item;
		if (isset($woocommerce->cart->cart_contents[$cart_item_key])) {
			$filter_cart_item  = $woocommerce->cart->cart_contents[$cart_item_key];
			if (isset($woocommerce->cart->cart_contents[$cart_item_key]['discounts'])) {
				//if (is_cumulative($cart_item, $cart_item_key)) {
				//	$result = $woocommerce->cart->cart_contents[$cart_item_key]['discounts']['price_adjusted'];
				//} else {
					$result = $woocommerce->cart->cart_contents[$cart_item_key]['discounts']['price_base'];
				//}
			} else {
				$result = $woocommerce->cart->cart_contents[$cart_item_key]['data']->get_price();
			}
		}

		return apply_filters('woocommerce_dynamic_pricing_get_price_to_discount', $result, $filter_cart_item, $cart_item_key);
	}

	function apply_cart_item_adjustment( $cart_item_key, $original_price, $adjusted_price, $module, $set_id ) {
		global $woocommerce;
		$adjusted_price = apply_filters( 'wc_dynamic_pricing_apply_cart_item_adjustment', $adjusted_price, $cart_item_key, $original_price, $module );

		if ( isset( $woocommerce->cart->cart_contents[$cart_item_key] ) ) {
			$_product = $woocommerce->cart->cart_contents[$cart_item_key]['data'];
			$display_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? $_product->get_price_excluding_tax() : $_product->get_price_including_tax();

			$woocommerce->cart->cart_contents[$cart_item_key]['data']->price = $adjusted_price;

			if ( !isset( $woocommerce->cart->cart_contents[$cart_item_key]['discounts'] ) ) {

				$discount_data = array(
				    'by' => array($module),
				    'set_id' => $set_id,
				    'price_base' => $original_price,
				    'display_price' => $display_price,
				    'price_adjusted' => $adjusted_price,
				    'applied_discounts' => array(array('by' => $module, 'set_id' => $set_id, 'price_base' => $original_price, 'price_adjusted' => $adjusted_price))
				);
				$woocommerce->cart->cart_contents[$cart_item_key]['discounts'] = $discount_data;
			} else {

				$existing = $woocommerce->cart->cart_contents[$cart_item_key]['discounts'];

				$discount_data = array(
				    'by' => $existing['by'],
				    'set_id' => $set_id,
				    'price_base' => $original_price,
				    'display_price' => $existing['display_price'],
				    'price_adjusted' => $adjusted_price
				);

				$woocommerce->cart->cart_contents[$cart_item_key]['discounts'] = $discount_data;

				$history = array('by' => $existing['by'], 'set_id' => $existing['set_id'], 'price_base' => $existing['price_base'], 'price_adjusted' => $existing['price_adjusted']);
				array_push( $woocommerce->cart->cart_contents[$cart_item_key]['discounts']['by'], $module );
				$woocommerce->cart->cart_contents[$cart_item_key]['discounts']['applied_discounts'][] = $history;
			}
		}

		do_action( 'woocommerce_dynamic_pricing_apply_cartitem_adjustment', $cart_item_key, $original_price, $adjusted_price, $module, $set_id );
	}

*/

?>