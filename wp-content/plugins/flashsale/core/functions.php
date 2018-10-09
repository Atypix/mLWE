<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('PW_Discount_function')) {
		class PW_Discount_function
		{
			public function __construct()
			{
			}			
			public static function show_add_to_cart_product($type='',$id='') {
				global $product;
				$read_more_title=__('Read More','pw_wc_flash_sale');
				
				$add_to_cart_title=__('Add To Cart','pw_wc_flash_sale');
				
				$select_options_title=__('Select options','pw_wc_flash_sale');
				
				$view_options_title=__('View options','pw_wc_flash_sale');

				if($id!='')
					$product = get_product($id);
				
				
				if (!$product->is_in_stock()) :
					
					if($type=='link') :
						return apply_filters('out_of_stock_add_to_cart_url', get_permalink($product->id));
					elseif($type=='label')	:
						return apply_filters('not_purchasable_text', $read_more_title);
					endif;
					
				else :
					$link = array(
						'url' => '',
						'label' => '',
						'class' => '',
					);

					$handler = apply_filters('woocommerce_add_to_cart_handler', $product->product_type, $product);

					switch ($handler) {
						case "variable" :
							$link['url'] = apply_filters('variable_add_to_cart_url', get_permalink($product->id));
							$link['label'] = apply_filters('variable_add_to_cart_text wt-addtocart', $select_options_title);
							$link['class'] = apply_filters('add_to_cart_class', 'button  product_type_variable');
							break;
						case "grouped" :
							$link['url'] = apply_filters('grouped_add_to_cart_url', get_permalink($product->id));
							$link['label'] = apply_filters('grouped_add_to_cart_text', $view_options_title);
							$link['class'] = apply_filters('add_to_cart_class', 'button  product_type_simple');
							break;
						case "external" :
							$link['url'] = apply_filters('external_add_to_cart_url', get_permalink($product->id));
							$link['label'] = $product->get_button_text();
							$link['class'] = apply_filters('add_to_cart_class', 'button  product_type_simple');
							break;
						default :
							if ($product->is_purchasable()) {
								$link['url'] = "?add-to-cart=".$id;
								$link['label'] = apply_filters('add_to_cart_text', $add_to_cart_title);
								$link['class'] = apply_filters('add_to_cart_class', 'button product_type_simple add_to_cart_button ajax_add_to_cart');
							} else {
								$link['url'] = "?add-to-cart=".$id;
								$link['label'] = apply_filters('not_purchasable_text', $read_more_title);
								$link['class'] = apply_filters('add_to_cart_class', 'button  product_type_simple');
							}
							break;
					}
					
					if($type=='link')
						return apply_filters('woocommerce_loop_add_to_cart_link', esc_url($link['url']));
					else if($type=='label')	
						return $link['label'];

					if(strpos($link['url'], 'href='))
					{
						$add_to_cart_has_tag_a=true;
						return apply_filters('woocommerce_loop_add_to_cart_link', esc_url($link['url']));
					}
					else if($type=='btn_icon_type'){
					
						return apply_filters('woocommerce_loop_add_to_cart_link', sprintf('<div class="woo-addcart" title="%s"><a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="%s  product_type_%s" title="%s"></a></div>', esc_html($link['label']),esc_url($link['url']), esc_attr($product->id), esc_attr($product->get_sku()), esc_attr($link['class']), esc_attr($product->product_type), esc_html($link['label'])), $product, $link);
					
					}else if($type=='btn_text_type'){
						
						return apply_filters('woocommerce_loop_add_to_cart_link', sprintf('<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="%s  product_type_%s">%s</a>', esc_url($link['url']), esc_attr($product->id), esc_attr($product->get_sku()), esc_attr($link['class']), esc_attr($product->product_type), esc_html($link['label'])), $product, $link);
						
					}
					

				endif;
		}				
			
			
			
			public static function fl_product_rule_custom_style($rand_id , $text_colour , $countdown_backcolour , $countdown_area_backcolour , $description_area_backcolour) {
			
				wp_enqueue_style('pw-pl-custom-style', plugin_dir_url_flash_sale . '/css/custom.css', array() , null); 
				$custom_css = '
					.countdown-'.$rand_id.'{
						background-color: '.$countdown_area_backcolour.'
					}
					.countdown-'.$rand_id.' ul.fl-countdown li span , .countdown-'.$rand_id.' ul.fl-countdown li p ,.countdown-'.$rand_id.' ul.fl-countdown li.seperator{ 
						color:'.$text_colour.';
					}
					.countdown-'.$rand_id.' ul.fl-countdown.fl-style2 li ,.countdown-'.$rand_id.' ul.fl-countdown.fl-style3 li span, .countdown-'.$rand_id.' ul.fl-countdown.fl-style4 li span  ,.countdown-'.$rand_id.' ul.fl-countdown.fl-style5 li  ,.countdown-'.$rand_id.' ul.fl-countdown.fl-style6 li { 
						background: '.$countdown_backcolour.'
					 }
					.car-'.$rand_id.' .fs-itemdesc,.car-'.$rand_id.' .fl-outerdesc-layout1 .fl-outer-details , .car-'.$rand_id.' .fl-outerdesc-layout3 .fl-outer-details{
						background:'.$description_area_backcolour.';
					}
					';
				wp_add_inline_style( 'pw-pl-custom-style', $custom_css );
			}
			public static function fl_rule_list_custom_style($rand_id , $text_colour , $countdown_backcolour  ) {
						
				wp_enqueue_style('pw-pl-custom-style', plugin_dir_url_flash_sale . '/css/custom.css', array() , null); 
				$custom_css = '
					
					.rulelist-'.$rand_id.' ul.fl-countdown li span , .rulelist-'.$rand_id.' ul.fl-countdown li p ,.rulelist-'.$rand_id.' ul.fl-countdown li.seperator{ 
						color:'.$text_colour.';
					}
					.rulelist-'.$rand_id.' ul.fl-countdown.fl-style2 li ,.rulelist-'.$rand_id.' ul.fl-countdown.fl-style3 li span, .rulelist-'.$rand_id.' ul.fl-countdown.fl-style4 li span  ,.rulelist-'.$rand_id.' ul.fl-countdown.fl-style5 li  ,.rulelist-'.$rand_id.' ul.fl-countdown.fl-style6 li { 
						background: '.$countdown_backcolour.'
					}
					 
					';
				wp_add_inline_style( 'pw-pl-custom-style', $custom_css );
			}
			
			public static function fl_rule_slider_custom_style($rand_id , $text_colour , $countdown_backcolour  ) {
						
				wp_enqueue_style('pw-pl-custom-style', plugin_dir_url_flash_sale . '/css/custom.css', array() , null); 
				$custom_css = '
					
					.ruleslider-'.$rand_id.' ul.fl-countdown li span , .ruleslider-'.$rand_id.' ul.fl-countdown li p ,.ruleslider-'.$rand_id.' ul.fl-countdown li.seperator{ 
						color:'.$text_colour.';
					}
					.ruleslider-'.$rand_id.' ul.fl-countdown.fl-style2 li ,.ruleslider-'.$rand_id.' ul.fl-countdown.fl-style3 li span,.ruleslider-'.$rand_id.' ul.fl-countdown.fl-style4 li span  ,.ruleslider-'.$rand_id.' ul.fl-countdown.fl-style5 li  ,.ruleslider-'.$rand_id.' ul.fl-countdown.fl-style6 li { 
						background: '.$countdown_backcolour.'
					}
					 
					';
				wp_add_inline_style( 'pw-pl-custom-style', $custom_css );
			}
			
			public static function fl_top_product_grid_custom_style($rand_id , $text_colour , $countdown_backcolour , $description_area_backcolour ) {
						
				wp_enqueue_style('pw-pl-custom-style', plugin_dir_url_flash_sale . '/css/custom.css', array() , null); 
				$custom_css = '
					.countdown-'.$rand_id.' ul.fl-countdown li span , .countdown-'.$rand_id.' ul.fl-countdown li p ,.countdown-'.$rand_id.' ul.fl-countdown li.seperator{ 
						color:'.$text_colour.';
					}
					.countdown-'.$rand_id.' ul.fl-countdown.fl-style2 li ,.countdown-'.$rand_id.' ul.fl-countdown.fl-style3 li span, .countdown-'.$rand_id.' ul.fl-countdown.fl-style4 li span  ,.countdown-'.$rand_id.' ul.fl-countdown.fl-style5 li  ,.countdown-'.$rand_id.' ul.fl-countdown.fl-style6 li { 
						background: '.$countdown_backcolour.'
					 }
					.col-'.$rand_id.' .fs-itemdesc,.col-'.$rand_id.' .fl-outerdesc-layout1 .fl-outer-details , .col-'.$rand_id.' .fl-outerdesc-layout3 .fl-outer-details{
						background:'.$description_area_backcolour.';
					}
					';
				wp_add_inline_style( 'pw-pl-custom-style', $custom_css );
			}
			public static function fl_top_product_carousel_custom_style($rand_id , $text_colour , $countdown_backcolour , $description_area_backcolour ) {
						
				wp_enqueue_style('pw-pl-custom-style', plugin_dir_url_flash_sale . '/css/custom.css', array() , null); 
				$custom_css = '
					.countdown-'.$rand_id.' ul.fl-countdown li span , .countdown-'.$rand_id.' ul.fl-countdown li p ,.countdown-'.$rand_id.' ul.fl-countdown li.seperator{ 
						color:'.$text_colour.';
					}
					.countdown-'.$rand_id.' ul.fl-countdown.fl-style2 li ,.countdown-'.$rand_id.' ul.fl-countdown.fl-style3 li span, .countdown-'.$rand_id.' ul.fl-countdown.fl-style4 li span  ,.countdown-'.$rand_id.' ul.fl-countdown.fl-style5 li  ,.countdown-'.$rand_id.' ul.fl-countdown.fl-style6 li { 
						background: '.$countdown_backcolour.'
					 }
					.car-'.$rand_id.' .fs-itemdesc,.car-'.$rand_id.' .fl-outerdesc-layout1 .fl-outer-details , .car-'.$rand_id.' .fl-outerdesc-layout3 .fl-outer-details{
						background:'.$description_area_backcolour.';
					}
					';
				wp_add_inline_style( 'pw-pl-custom-style', $custom_css );
			}


			public static function pw_get_discunt_price($price,$type,$discunt)
			{
				$discount="";
				switch ($type)
				{
					case 'percent':
						$discount = $price * ($discunt / 100);
					break;
					
					case 'price':
						$discount = $discunt;
					break;
				}
				//$discount = ceil($discount * pow(10, get_option('wc_price_num_decimals')) - 0.5) * pow(10, -((int) get_option('wc_price_num_decimals')));
				$discount= $price - $discount;
				
				($discount < 0 ? $discount = 0 : $discount);
				
				return $discount;
			}
			public function price_including_tax_function( $product, $qty = 1, $price = '' ) {

				if ( version_compare( WC()->version, '2.7.0', '>=' ) ) {

					$price = wc_get_price_including_tax( $product, array( 'qty' => $qty, 'price' => $price ) );
				} else {

					$price = $product->get_price_including_tax( $qty, $price );
				}

				return $price;
			}	
			public function price_excliding_tax_function($product , $qty = 1, $price = '' )
			{
				if ( version_compare( WC()->version, '2.7.0', '>=' ) ) {

					$price = wc_get_price_excluding_tax( $product, array( 'qty' => $qty, 'price' => $price ) );
				} else {

					//$price = $product->get_price_excluding_tax( $qty, $price );
				}

				return $price;			
			}
			
			public static function pw_get_base_price_by_product($product)
			{
				//remove_filter( 'woocommerce_get_price', array($this, 'on_get_price'), 10, 2 );
				if ( apply_filters( 'pw_wc_sale_get_use_sale_price', true, $product ) ) {
					$base_price = $product->get_price();
				} else {
					$base_price = function_exists('wc_get_price_to_display') ? wc_get_price_to_display( $product, array( 'qty' => 1, 'price' => $product->get_regular_price()  ) ): $product->get_display_price( $product->get_regular_price() );					
					//$base_price = $product->get_regular_price();
				}
				$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );

				//$base_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? $this->price_excliding_tax_function($product) : $this->price_including_tax_function($product);	
				
				($base_price < 0 ? $base_price = 0 : $base_price);

				return $base_price;	
				//add_filter( 'woocommerce_get_price', array($this, 'on_get_price'), 10, 2 );	
			}
		}
		
	new PW_Discount_function();
}