<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('PW_Discount_Price')) {
    class PW_Discount_Price
    {
        public function __construct()
        {
            $this->rules = array();
            $this->cart_item = array();
            $this->rule_apply = array();
            $this->product_special = array();
            add_action('woocommerce_cart_loaded_from_session', array($this, 'adjust_cart_rule'), 100);
            add_action('woocommerce_ajax_added_to_cart', array($this, 'adjust_cart_rule'), 100);

            add_action('woocommerce_loaded', array($this, 'pw_flashsale_woocommerce_load'), 100);
        }

        public function pw_flashsale_woocommerce_load()
        {
            if (version_compare(WOOCOMMERCE_VERSION, "2.1.0") >= 0) {
                add_filter('woocommerce_cart_item_price', array($this, 'pw_flashsale_cart_item_price_html'), 100, 3);
            } else {
                add_filter('woocommerce_cart_item_price_html', array($this, 'pw_flashsale_cart_item_price_html'), 100, 3);
            }
        }

        public function pw_flashsale_cart_item_price_html($html, $cart_item, $cart_item_key)
        {
            //$woocommerce->cart->cart_contents[$cart_item_key]['pwdiscounts']
            //print_r($cart_item);

            if (isset($cart_item['pwdiscounts'])) {
                $_product = $cart_item['data'];
                //die('a');
                $price_adjusted = get_option('woocommerce_tax_display_cart') == 'excl' ? $this->price_excliding_tax_function($_product, 1, $cart_item['pwdiscounts']['price_adjusted']) : $this->price_including_tax_function($_product, 1, $cart_item['pwdiscounts']['price_adjusted']);
                //$price_adjusted=$cart_item['pwdiscounts']['price_adjusted'];
                $price_base = $cart_item['pwdiscounts']['display_price'];
                /*if (function_exists('get_product')) {

                }
                else {

                    if (get_option('woocommerce_display_cart_prices_excluding_tax') == 'yes') :
                        $price_adjusted = $cart_item['data']->get_price_excluding_tax();
                        $price_base = $cart_item['pwdiscounts']['display_price'];
                    else :
                        $price_adjusted = $cart_item['data']->get_price();
                        $price_base = $cart_item['pwdiscounts']['display_price'];
                    endif;
*/
                //if (!empty($price_adjusted) || $price_adjusted === 0) {

                $html = '<del>' . wc_price($price_base) . '</del><ins> ' . wc_price($price_adjusted) . '</ins>';

                //}
            }
            return $html;
        }

        public function func_get_max_for_spechial($rule, $count)
        {
            if ($rule['pw_type'] == 'special')
                return max($count);
            return false;
        }

        public function adjust_cart_rule()
        {
            global $woocommerce;
            $this->cart_item = $this->sort_cart_by_price($woocommerce->cart->cart_contents);
            //echo '<br>';
            //echo '<br>';
            //echo '<br>';
            /*		foreach ($this->cart_item as $cart_item_key => $cart_item) {
                        print_r($cart_item);
                        echo '<br/>';
                        echo '<br/>';
                    }
                    */
            $arr = array();
            $query_meta_query = array('relation' => 'AND');
            $query_meta_query[] = array(
                'key' => 'status',
                'value' => "active",
                'compare' => '=',
            );
            $matched_products = get_posts(
                array(
                    'post_type' => 'flash_sale',
                    'numberposts' => -1,
                    'post_status' => 'publish',
                    'fields' => 'ids',
                    'no_found_rows' => true,
                    'orderby' => 'modified',
                    'order' => 'ASC',
                    'meta_query' => $query_meta_query,
                )
            );
            foreach ($matched_products as $pr) {
                $pw_product_brand = $pw_except_product_brand = "";
                $pw_type = get_post_meta($pr, 'pw_type', true);
                if ($pw_type == "cart")
                    continue;
                if (defined('plugin_dir_url_pw_woo_brand')) {
                    $pw_product_brand = get_post_meta($pr, 'pw_product_brand', true);
                    $pw_except_product_brand = get_post_meta($pr, 'pw_except_product_brand', true);
                }
                $pw_to = get_post_meta($pr, 'pw_to', true);
                $pw_from = get_post_meta($pr, 'pw_from', true);
                $pw_apply_to = get_post_meta($pr, 'pw_apply_to', true);
                $pw_product_category = get_post_meta($pr, 'pw_product_category', true);
                $pw_except_product_category = get_post_meta($pr, 'pw_except_product_category', true);
                $pw_product_tag = get_post_meta($pr, 'pw_product_tag', true);
                $pw_except_product_tag = get_post_meta($pr, 'pw_except_product_tag', true);
                $pw_product = get_post_meta($pr, 'pw_product', true);
                $pw_except_product = get_post_meta($pr, 'pw_except_product', true);
                $pw_cart_roles = get_post_meta($pr, 'pw_cart_roles', true);
                $pw_roles = get_post_meta($pr, 'pw_roles', true);
                $pw_capabilities = get_post_meta($pr, 'pw_capabilities', true);
                $pw_users = get_post_meta($pr, 'pw_users', true);
                $pw_products_to_adjust = get_post_meta($pr, 'pw_products_to_adjust', true);
                $quantity_base = get_post_meta($pr, 'quantity_base', true);
                $pw_discount = get_post_meta($pr, 'pw_discount', true);
                $adjustment_type = get_post_meta($pr, 'adjustment_type', true);
                $pw_type_discount = get_post_meta($pr, 'pw_type_discount', true);
                $adjustment_value = get_post_meta($pr, 'adjustment_value', true);
                $amount_to_adjust = get_post_meta($pr, 'amount_to_adjust', true);
                $pw_discount_qty = get_post_meta($pr, 'pw_discount_qty', true);
                $amount_to_purchase = get_post_meta($pr, 'amount_to_purchase', true);
                $repeat = get_post_meta($pr, 'repeat', true);
                $pw_matched = get_post_meta($pr, 'pw_matched', true);
                $pw_products_to_adjust_products = get_post_meta($pr, 'pw_products_to_adjust_products', true);
                $pw_products_to_adjust_category = get_post_meta($pr, 'pw_products_to_adjust_category', true);
                $this->rules[$pr] = array(
                    "pw_type" => $pw_type,
                    "pw_to" => $pw_to,
                    "pw_from" => $pw_from,
                    "pw_apply_to" => $pw_apply_to,
                    "pw_product_category" => $pw_product_category,
                    "pw_except_product_category" => $pw_except_product_category,
                    "pw_product_tag" => $pw_product_tag,
                    "pw_except_product_tag" => $pw_except_product_tag,
                    "pw_product_brand" => $pw_product_brand,
                    "pw_except_product_brand" => $pw_except_product_brand,
                    "pw_product" => $pw_product,
                    "pw_except_product" => $pw_except_product,
                    "pw_cart_roles" => $pw_cart_roles,
                    "pw_roles" => $pw_roles,
                    "pw_capabilities" => $pw_capabilities,
                    "pw_discount_qty" => $pw_discount_qty,
                    "pw_users" => $pw_users,
                    "pw_products_to_adjust" => $pw_products_to_adjust,
                    "quantity_base" => $quantity_base,
                    "pw_discount" => $pw_discount,
                    "adjustment_type" => $adjustment_type,
                    "pw_type_discount" => $pw_type_discount,
                    "adjustment_value" => $adjustment_value,
                    "amount_to_adjust" => $amount_to_adjust,
                    "amount_to_purchase" => $amount_to_purchase,
                    "repeat" => $repeat,
                    "pw_matched" => $pw_matched,
                    "pw_products_to_adjust_products" => $pw_products_to_adjust_products,
                    "pw_products_to_adjust_category" => $pw_products_to_adjust_category,
                );
            }

            $RulesAplly = array();

            foreach ($this->rules as $rule_key => $rule) {
                if ($this->check_candition_rules($rule)) {
                    //	die('a');
                    $RulesAplly[$rule_key] = $rule;
                    $count = $this->func_get_count_cart_product_by_rule($rule);

                    $get_max_for_spechial = $this->func_get_max_for_spechial($rule, $count);

                    $hit = 0;
                    $pw_type = $rule['pw_type'];
                    if ($rule['pw_type'] == 'flashsale') {
                        $e = array();
                        $e = array(
                            'type' => $rule['pw_type_discount'],
                            'discount' => $rule['pw_discount'],
                        );
                        $RulesAplly[$rule_key]['quantity_adj'] = $e;
                    } else if ($pw_type == 'quantity') {
                        if (!in_array($rule['quantity_base'], array('categories', 'all')) && $rule['pw_products_to_adjust'] == 'matched') {
                            $e = array();
                            foreach ($count as $quantity_key => $quantity) {
                                foreach ($rule['pw_discount_qty'] as $row_key => $row) {
                                    $max_discunt = "";
                                    $max_discunt = 9999;
                                    if ($row['max'] != "")
                                        $max_discunt = $row['max'];
                                    if ($row['discount'] > 0 && $quantity >= $row['min'] && $quantity <= $max_discunt) {
                                        $e[$quantity_key] = array(
                                            'type' => $row['type'],
                                            'discount' => $row['discount'],
                                        );
                                    }
                                }
                            }
                            $RulesAplly[$rule_key]['quantity_adj'] = $e;
                        } else if ($rule['pw_products_to_adjust'] != 'matched' || in_array($rule['quantity_base'], array('categories', 'all'))) {
                            $bishtar = 0;
                            foreach ($count as $quantity) {
                                foreach ($rule['pw_discount_qty'] as $row_key => $row) {
                                    $max_discunt = "";
                                    $max_discunt = 9999;
                                    if ($row['max'] != "")
                                        $max_discunt = $row['max'];
                                    if ($row['discount'] > 0 && $quantity >= $row['min'] && $quantity <= $max_discunt) {
                                        if ($quantity > $bishtar) {
                                            $bishtar = $quantity;
                                            $quantity_adj = array(
                                                'bishtar' => $quantity,
                                                'type' => $row['type'],
                                                'discount' => $row['discount'],
                                            );
                                        }
                                    }
                                }
                            }
                            $RulesAplly[$rule_key]['quantity_adj'] = $quantity_adj;
                        }
                    } else if ($pw_type == 'special') {
                        if ($rule['pw_products_to_adjust'] == 'matched' && !in_array($rule['quantity_base'], array('categories', 'all'))) {
                            $e = array();
                            foreach ($count as $key => $c) {
                                $repeat = 0;
                                $loop = $rule['amount_to_purchase'];
                                while (($c - $loop) > 0) {
                                    if (($c - $loop) < $rule['amount_to_adjust'])
                                        $count_to_adjust = $c - $loop;
                                    else
                                        $count_to_adjust = $rule['amount_to_adjust'];

                                    $repeat += $count_to_adjust;
                                    $loop += ($rule['amount_to_purchase'] + $count_to_adjust);
                                    if (!$rule['repeat'])
                                        break;
                                }
                                $e[$key] = $repeat;
                            }
                            $RulesAplly[$rule_key]['repeat'] = $e;
                            $RulesAplly[$rule_key]['quantity_adj'] = array(
                                'type' => $rule['adjustment_type'],
                                'discount' => $rule['adjustment_value'],
                            );
                        } else if ($rule['pw_type'] == 'special' && ($rule['pw_products_to_adjust'] != 'matched' || in_array($rule['quantity_base'], array('categories', 'all')))) {
                            $hit = 0;
                            if ($rule['pw_products_to_adjust'] == 'matched') {
                                $loop = $rule['amount_to_purchase'] + $rule['amount_to_adjust'];
                            } else {
                                $loop = $rule['amount_to_purchase'] + 0;
                            }
                            //echo $loop;
                            while (($get_max_for_spechial - $loop) >= 0) {
                                $hit += $rule['amount_to_adjust'];
                                if ($rule['pw_products_to_adjust'] == 'matched')
                                    $loop += $rule['amount_to_purchase'] + $rule['amount_to_adjust'];
                                else
                                    $loop += $rule['amount_to_purchase'] + 0;

                                if (!$rule['repeat'])
                                    break;
                            }
                            //echo $hit;
                            $RulesAplly[$rule_key]['repeat'] = $hit;
                            $RulesAplly[$rule_key]['quantity_adj'] = array(
                                'type' => $rule['adjustment_type'],
                                'discount' => $rule['adjustment_value'],
                            );
                        }
                    }
                }
            }

            foreach ($RulesAplly as $rule_key => $rule) {
                $flag = false;
                foreach ($this->cart_item as $cart_item_key => $cart_item) {

                    if ($this->unset_rule_if_no_apply($rule_key, $rule, $cart_item_key, $cart_item, true) !== false) {

                        $flag = true;
                        break;
                    }

                }
                if (!$flag) {

                    unset($RulesAplly[$rule_key]);
                }
            }

            foreach ($RulesAplly as $key => $rule) {
                if ($rule['pw_matched'] == 'only') {
                    $RulesAplly = array();
                    $RulesAplly[$key] = $rule;
                    break;
                }
            }

            $this->rule_apply = $RulesAplly;
            foreach ($this->cart_item as $cart_item_key => $cart_item) {

                $price = $this->cart_item[$cart_item_key]['data']->get_price();
                $price_base = $this->cart_item[$cart_item_key]['data']->get_price();

                $pw_matched_rule = get_option('pw_matched_rule');
                if ($pw_matched_rule == "all") {
                    foreach ($RulesAplly as $rule_key => $apply) {
                        $takhfif = $this->unset_rule_if_no_apply($rule_key, $apply, $cart_item_key, $cart_item, false, $price);

                        if ($takhfif) {
                            //echo $cart_item['data']->id.'-';
                            $price = $price - $takhfif;
                            //$this->rule_apply[$rule_key] = 0;
                        }
                    }

                } else if ($pw_matched_rule == "big") {
                    $max_price = 0;
                    $rule_key_big = 0;
                    foreach ($RulesAplly as $rule_key => $apply) {
                        $takhfif = $this->unset_rule_if_no_apply($rule_key, $apply, $cart_item_key, $cart_item, false);
                        if ($takhfif) {
                            if ($max_price < $takhfif) {
                                $max_price = $takhfif;
                                $rule_key_big = $rule_key;
                            }
                            if ($max_price != 0 && $rule_key_big != 0) {
                                $price = $price - $max_price;
                            }
                        }
                    }
                }

                if ($price != $price_base) {

                    $this->apply_cart_item_adjustment($cart_item_key, $price_base, $price);
                } else if (isset($woocommerce->cart->cart_contents[$cart_item_key]['pwdiscounts'])) {
                    unset($woocommerce->cart->cart_contents[$cart_item_key]['pwdiscounts']);
                }

            }

        }

        public function price_excliding_tax_function($product, $qty = 1, $price = '')
        {
            if (version_compare(WC()->version, '2.7.0', '>=')) {

                $price = wc_get_price_excluding_tax($product, array('qty' => $qty, 'price' => $price));
            } else {

                //$price = $product->get_price_excluding_tax( $qty, $price );
            }

            return $price;
        }

        public function price_including_tax_function($product, $qty = 1, $price = '')
        {

            if (version_compare(WC()->version, '2.7.0', '>=')) {

                $price = wc_get_price_including_tax($product, array('qty' => $qty, 'price' => $price));
            } else {

                $price = $product->get_price_including_tax($qty, $price);
            }

            return $price;
        }

        public function apply_cart_item_adjustment($cart_item_key, $price_base, $adjusted_price)
        {

            global $woocommerce;
            if (isset($woocommerce->cart->cart_contents[$cart_item_key])) {

                $_product = $woocommerce->cart->cart_contents[$cart_item_key]['data'];
                $display_price = get_option('woocommerce_tax_display_cart') == 'excl' ? $this->price_excliding_tax_function($_product) : $this->price_including_tax_function($_product);
                $this->product_set_price($woocommerce->cart->cart_contents[$cart_item_key]['data'], $adjusted_price);
                $woocommerce->cart->cart_contents[$cart_item_key]['data']->price = $adjusted_price;
                $module = "flash_sale";
                $discount_data = array(
                    'by' => array($module),
                    'price_base' => $price_base,
                    'display_price' => $display_price,
                    'price_adjusted' => $adjusted_price,
                    'applied_discounts' => array(array('by' => $module, 'price_base' => $price_base, 'price_adjusted' => $adjusted_price))
                );
                $woocommerce->cart->cart_contents[$cart_item_key]['pwdiscounts'] = $discount_data;
                //}
            }
        }

        public function product_set_price($product, $price)
        {
            if ($this->get_wc_version('3.0')) {
                $product->set_price($price);
            } else {
                $product->price = $price;
            }
        }

        public function get_wc_version($version)
        {
            if (defined('WC_VERSION') && WC_VERSION) {
                return version_compare(WC_VERSION, $version, '>=');
            } else if (defined('WOOCOMMERCE_VERSION') && WOOCOMMERCE_VERSION) {
                return version_compare(WOOCOMMERCE_VERSION, $version, '>=');
            } else {
                return false;
            }
        }

        public static function price_discunt($price, $type)
        {
            if ($price < 0)
                $price = 0;
            else
                $price = $price;
            //echo $type['type'];
            //die('a');
            switch ($type['type']) {
                case 'percent':
                    $discount = $price * ($type['discount'] / 100);

                    break;

                case 'price':
                    $discount = $type['discount'];
                    break;
            }
            //$discount = ceil($discount * pow(10, get_option('wc_price_num_decimals')) - 0.5) * pow(10, -((int) get_option('wc_price_num_decimals')));
//print_r($discount);
            //	die;
            if ($discount < 0)
                return 0;
            else
                return $discount;
        }

        public function unset_rule_if_no_apply($rule_key, $rule, $cart_item_key, $cart_item, $ret = true, $quentity = null)
        {
            if ($quentity != null)
                $price = $quentity;
            else
                $price = $cart_item['data']->get_price();

            $count_price_for_disccunt = 0;
            $cart_item_count = $cart_item['quantity'];
            if ($rule['pw_type'] == 'flashsale') {
                //print_r($rule['quantity_adj']);

                if ($rule['pw_apply_to'] == 'pw_all_product') {
                    return self::price_discunt($price, $rule['quantity_adj']);
                }

                if ($rule['pw_apply_to'] == 'pw_product_category') {
                    if (count(array_intersect($this->cart_categories($cart_item), $rule['pw_product_category'])) > 0) {
                        return self::price_discunt($price, $rule['quantity_adj']);
                    }
                } else if ($rule['pw_apply_to'] == 'pw_except_product_category') {
                    if (count(array_intersect($this->cart_categories($cart_item), $rule['pw_except_product_category'])) == 0) {
                        return self::price_discunt($price, $rule['quantity_adj']);
                    }
                } else if ($rule['pw_apply_to'] == 'pw_product_tag') {
                    if (count(array_intersect($this->get_cart_item_tag($cart_item), $rule['pw_product_tag'])) > 0) {
                        return self::price_discunt($price, $rule['quantity_adj']);
                    }
                } else if ($rule['pw_apply_to'] == 'pw_except_product_tag') {
                    if (count(array_intersect($this->get_cart_item_tag($cart_item), $rule['pw_except_product_tag'])) == 0) {
                        return self::price_discunt($price, $rule['quantity_adj']);
                    }
                } else if ($rule['pw_apply_to'] == 'pw_product_brand') {
                    if (count(array_intersect($this->get_cart_item_brand($cart_item), $rule['pw_product_brand'])) > 0) {
                        return self::price_discunt($price, $rule['quantity_adj']);
                    }
                } else if ($rule['pw_apply_to'] == 'pw_except_product_brand') {
                    if (count(array_intersect($this->get_cart_item_brand($cart_item), $rule['pw_except_product_brand'])) == 0) {
                        return self::price_discunt($price, $rule['quantity_adj']);
                    }
                } else if ($rule['pw_apply_to'] == 'pw_product') {
                    $product_id = $cart_item['product_id'];
                    //	$product_id = ( isset( $cart_item['variation_id'] ) && $cart_item['variation_id'] != '' ) ? $cart_item['variation_id'] : $product_id;
                    if (in_array($product_id, $rule['pw_product'])) {
                        return self::price_discunt($price, $rule['quantity_adj']);
                    }
                } else if ($rule['pw_apply_to'] == 'pw_except_product') {
                    $product_id = $cart_item['product_id'];
                    $product_id = (isset($cart_item['variation_id']) && $cart_item['variation_id'] != '') ? $cart_item['variation_id'] : $product_id;
                    if (!in_array($product_id, $rule['pw_except_product'])) {
                        return self::price_discunt($price, $rule['quantity_adj']);
                    }
                }

            } else if ($rule['pw_type'] == 'quantity' && $rule['pw_products_to_adjust'] == 'matched' && !in_array($rule['quantity_base'], array('categories', 'all'))) {

                $id_quesntity = false;
                if ($rule['quantity_base'] == "product") {
                    $product_id = $cart_item['product_id'];
                    //$product_id = ( isset( $cart_item['variation_id'] ) && $cart_item['variation_id'] != '' ) ? $cart_item['variation_id'] : $product_id;
                    $id_quesntity = $product_id;
                } else if ($rule['quantity_base'] == "variation") {
                    if (isset($cart_item['variation_id']) && !empty($cart_item['variation_id']))
                        $id_quesntity = $cart_item['variation_id'];
                    else {
                        $product_id = $cart_item['product_id'];
                        $product_id = (isset($cart_item['variation_id']) && $cart_item['variation_id'] != '') ? $cart_item['variation_id'] : $product_id;
                        $id_quesntity = $product_id;
                    }
                } else if ($rule['quantity_base'] == "line") {
                    $id_quesntity = $cart_item_key;
                }

                if (isset($rule['quantity_adj'][$id_quesntity])) {
                    return self::price_discunt($price, $rule['quantity_adj'][$id_quesntity]);
                }
            } else if ($rule['pw_type'] == 'quantity' && $rule['pw_products_to_adjust'] == 'matched' && in_array($rule['quantity_base'], array('categories', 'all'))) {
                if ($rule['pw_apply_to'] == 'pw_all_product')
                    return self::price_discunt($price, $rule['quantity_adj']);

                if ($rule['pw_apply_to'] == 'pw_product_category') {
                    if (count(array_intersect($this->cart_categories($cart_item), $rule['pw_product_category'])) > 0) {
                        return self::price_discunt($price, $rule['quantity_adj']);
                    }
                } else if ($rule['pw_apply_to'] == 'pw_except_product_category') {
                    if (count(array_intersect($this->cart_categories($cart_item), $rule['pw_except_product_category'])) == 0) {
                        return self::price_discunt($price, $rule['quantity_adj']);
                    }
                } else if ($rule['pw_apply_to'] == 'pw_product_tag') {
                    if (count(array_intersect($this->get_cart_item_tag($cart_item), $rule['pw_product_tag'])) > 0) {
                        return self::price_discunt($price, $rule['quantity_adj']);
                    }
                } else if ($rule['pw_apply_to'] == 'pw_except_product_tag') {
                    if (count(array_intersect($this->get_cart_item_tag($cart_item), $rule['pw_except_product_tag'])) == 0) {
                        return self::price_discunt($price, $rule['quantity_adj']);
                    }
                } else if ($rule['pw_apply_to'] == 'pw_product_brand') {
                    if (count(array_intersect($this->get_cart_item_brand($cart_item), $rule['pw_product_brand'])) > 0) {
                        return self::price_discunt($price, $rule['quantity_adj']);
                    }
                } else if ($rule['pw_apply_to'] == 'pw_except_product_brand') {
                    if (count(array_intersect($this->get_cart_item_brand($cart_item), $rule['pw_except_product_brand'])) == 0) {
                        return self::price_discunt($price, $rule['quantity_adj']);
                    }
                } else if ($rule['pw_apply_to'] == 'pw_product') {
                    $product_id = $cart_item['product_id'];
                    //$product_id = ( isset( $cart_item['variation_id'] ) && $cart_item['variation_id'] != '' ) ? $cart_item['variation_id'] : $product_id;
                    if (in_array($product_id, $rule['pw_product'])) {
                        return self::price_discunt($price, $rule['quantity_adj']);
                    }
                } else if ($rule['pw_apply_to'] == 'pw_except_product') {
                    $product_id = $cart_item['product_id'];
                    $product_id = (isset($cart_item['variation_id']) && $cart_item['variation_id'] != '') ? $cart_item['variation_id'] : $product_id;
                    if (!in_array($product_id, $rule['pw_except_product'])) {
                        return self::price_discunt($price, $rule['quantity_adj']);
                    }
                }
            } else if ($rule['pw_type'] == 'quantity' && (!in_array($rule['quantity_base'], array('categories', 'all')) || $rule['pw_products_to_adjust'] != 'matched')) {
                if ($rule['pw_products_to_adjust'] == 'other_categories') {
                    if (count(array_intersect($this->cart_categories($cart_item), $rule['pw_products_to_adjust_category'])) > 0) {
                        return self::price_discunt($price, $rule['quantity_adj']);
                    }
                } else if ($rule['pw_products_to_adjust'] == 'other_products') {
                    $product_id = $cart_item['product_id'];
                    //$product_id = ( isset( $cart_item['variation_id'] ) && $cart_item['variation_id'] != '' ) ? $cart_item['variation_id'] : $product_id;

                    if (in_array($product_id, $rule['pw_products_to_adjust_products'])) {
                        return self::price_discunt($price, $rule['quantity_adj']);
                    }
                }
            } else if ($rule['pw_type'] == 'special' && $rule['pw_products_to_adjust'] == 'matched' && !in_array($rule['quantity_base'], array('categories', 'all'))) {
                $id_quesntity = false;
                if ($rule['quantity_base'] == "product") {
                    $product_id = $cart_item['product_id'];
                    $product_id = (isset($cart_item['variation_id']) && $cart_item['variation_id'] != '') ? $cart_item['variation_id'] : $product_id;
                    $id_quesntity = $product_id;
                } else if ($rule['quantity_base'] == "variation") {
                    if (isset($cart_item['variation_id']) && !empty($cart_item['variation_id']))
                        $id_quesntity = $cart_item['variation_id'];
                    else {
                        $product_id = $cart_item['product_id'];
                        //	$product_id = ( isset( $cart_item['variation_id'] ) && $cart_item['variation_id'] != '' ) ? $cart_item['variation_id'] : $product_id;
                        $id_quesntity = $product_id;
                    }
                } else if ($rule['quantity_base'] == "line")
                    $id_quesntity = $cart_item_key;

                if (isset($rule['repeat'][$id_quesntity])) {
                    if ($ret) {
                        return self::price_discunt($price, $rule['quantity_adj']);
                    } else {
                        if (!isset($this->product_special[$rule_key][$id_quesntity])) {
                            $this->product_special[$rule_key][$id_quesntity] = $rule['repeat'][$id_quesntity];
                        }
                        $count_item = $cart_item['quantity'];
                        while ($this->product_special[$rule_key][$id_quesntity] > 0 && $count_item > 0) {
                            $count_price_for_disccunt += self::price_discunt($price, $rule['quantity_adj']);
                            $this->product_special[$rule_key][$id_quesntity]--;
                            $count_item--;
                        }
                        $return = $count_price_for_disccunt / $cart_item['quantity'];
                        return $return;
                    }
                }
            } else if ($rule['pw_type'] == 'special' && in_array($rule['quantity_base'], array('categories', 'all')) && $rule['pw_products_to_adjust'] == 'matched') {
                if ($rule['pw_apply_to'] == 'pw_product_category') {
                    if (count(array_intersect($this->cart_categories($cart_item), $rule['pw_product_category'])) <= 0) {
                        return false;
                    }
                } else if ($rule['pw_apply_to'] == 'pw_except_product_category') {
                    if (count(array_intersect($this->cart_categories($cart_item), $rule['pw_except_product_category'])) != 0) {
                        return false;
                    }
                } else if ($rule['pw_apply_to'] == 'pw_product_tag') {
                    if (count(array_intersect($this->get_cart_item_tag($cart_item), $rule['pw_product_tag'])) <= 0) {
                        return false;
                    }
                } else if ($rule['pw_apply_to'] == 'pw_except_product_tag') {
                    if (count(array_intersect($this->get_cart_item_tag($cart_item), $rule['pw_except_product_tag'])) != 0) {
                        return false;
                    }
                } else if ($rule['pw_apply_to'] == 'pw_product_brand') {
                    if (count(array_intersect($this->get_cart_item_brand($cart_item), $rule['pw_product_brand'])) <= 0) {
                        return false;
                    }
                } else if ($rule['pw_apply_to'] == 'pw_except_product_brand') {
                    if (count(array_intersect($this->get_cart_item_brand($cart_item), $rule['pw_except_product_brand'])) != 0) {
                        return false;
                    }
                } else if ($rule['pw_apply_to'] == 'pw_product') {
                    $product_id = $cart_item['product_id'];
                    //	$product_id = ( isset( $cart_item['variation_id'] ) && $cart_item['variation_id'] != '' ) ? $cart_item['variation_id'] : $product_id;
                    if (!in_array($product_id, $rule['pw_product'])) {
                        return false;
                    }
                } else if ($rule['pw_apply_to'] == 'pw_except_product') {
                    $product_id = $cart_item['product_id'];
                    $product_id = (isset($cart_item['variation_id']) && $cart_item['variation_id'] != '') ? $cart_item['variation_id'] : $product_id;
                    if (in_array($product_id, $rule['pw_except_product'])) {
                        return false;
                    }
                }

                if (!$ret) {
                    if (!isset($this->product_special[$rule_key])) {
                        $this->product_special[$rule_key] = $rule['repeat'];
                    }
                    while ($this->product_special[$rule_key] > 0 && $cart_item_count > 0) {
                        $count_price_for_disccunt += self::price_discunt($price, $rule['quantity_adj']);
                        $this->product_special[$rule_key]--;
                        $cart_item_count--;
                    }
                    $return_discunt = $count_price_for_disccunt / $cart_item['quantity'];
                    return $return_discunt;

                } else {
                    return self::price_discunt($price, $rule['quantity_adj']);
                }
            } elseif ($rule['pw_type'] == 'special' && (!in_array($rule['quantity_base'], array('categories', 'all')) || $rule['pw_products_to_adjust'] != 'matched')) {
                if ($rule['pw_products_to_adjust'] == 'other_categories') {
                    if (count(array_intersect($this->cart_categories($cart_item), $rule['pw_products_to_adjust_category'])) <= 0)
                        return false;
                } else if ($rule['pw_products_to_adjust'] == 'other_products') {
                    $product_id = $cart_item['product_id'];
                    //	$product_id = ( isset( $cart_item['variation_id'] ) && $cart_item['variation_id'] != '' ) ? $cart_item['variation_id'] : $product_id;
                    if (!in_array($product_id, $rule['pw_products_to_adjust_products']))
                        return false;
                }
                if (!$ret) {
                    if (!isset($this->product_special[$rule_key])) {
                        $this->product_special[$rule_key] = $rule['repeat'];
                    }
                    $cart_item_count = $cart_item['quantity'];
                    while ($this->product_special[$rule_key] > 0 && $cart_item_count > 0) {
                        $count_price_for_disccunt += self::price_discunt($price, $rule['quantity_adj']);
                        $this->product_special[$rule_key]--;
                        $cart_item_count--;
                    }
                    $return_discunt = $count_price_for_disccunt / $cart_item['quantity'];
                    return $return_discunt;

                } else {
                    return self::price_discunt($price, $rule['quantity_adj']);
                }
            }
            return false;
        }

        public function check_candition_rules($rule)
        {
            if (!isset($rule['pw_type']) || !in_array($rule['pw_type'], array('flashsale', 'special', 'quantity')))
                return false;

            if (isset($rule['pw_to']) && !empty($rule['pw_to']) && (strtotime($rule['pw_to']) < strtotime(current_time('mysql'))))
                return false;

            if (isset($rule['pw_from']) && !empty($rule['pw_from']) && (strtotime($rule['pw_from']) > strtotime(current_time('mysql'))))
                return false;

            if ($rule['pw_cart_roles'] == 'roles') {
                if (count(array_intersect($this->pw_user_roles(), $rule['pw_roles'])) < 1) {
                    return false;
                }
            }
            if ($rule['pw_cart_roles'] == 'roles') {
                if (count(array_intersect($this->pw_user_roles(), $rule['pw_roles'])) < 1) {
                    return false;
                }
            }
            if ($rule['pw_cart_roles'] == 'capabilities') {
                if (count(array_intersect($this->user_capabilities(), $rule['pw_capabilities'])) < 1) {
                    return false;
                }
            }

            if ($rule['pw_cart_roles'] == 'users') {
                if (!in_array(get_current_user_id(), $rule['pw_users'])) {
                    return false;
                }
            }

            if ($rule['pw_type'] != 'flashsale' && $rule['pw_type'] != "cart") {
                $counts = $this->func_get_count_cart_product_by_rule($rule);
                //print_r($counts);
                if (!$this->func_check_count_quantity($rule, $counts))
                    return false;
            }
            return true;
        }

        public function func_check_count_quantity($rule, $counts)
        {
            if ($rule['pw_type'] == 'quantity') {
                $flag = false;
                //print_r($rule['pw_discount_qty']);
                foreach ($counts as $quantity_key => $quantity) {
                    foreach ($rule['pw_discount_qty'] as $key => $row) {
                        $max_discunt = "";
                        $max_discunt = 9999;
                        if ($row['max'] != "")
                            $max_discunt = $row['max'];
                        if ($quantity >= $row['min'] && $quantity <= $max_discunt) {
                            $flag = true;
                            break;
                        }
                    }
                }
                if (!$flag) {
                    return false;
                }
            } else if ($rule['pw_type'] == 'special') {
                $flag = false;
                foreach ($counts as $key => $quantity) {
                    if ($quantity >= $rule['amount_to_purchase']) {
                        $flag = true;
                        break;
                    }
                }
                //echo $flag.'$';
                if (!$flag) {
                    return false;
                }
            }
            return true;
        }

        public function func_get_count_cart_product_by_rule($rule)
        {
            $cart_items = array();
            foreach ($this->cart_item as $cart_item_key => $cart_item) {
                if ($rule['pw_apply_to'] == 'pw_all_product') {
                    $cart_items[$cart_item_key] = $cart_item;
                } else if ($rule['pw_apply_to'] == 'pw_product_category') {
                    $categories = $this->cart_categories($cart_item);
                    //print_r($rule['pw_product_category']);
                    if (count(array_intersect($categories, $rule['pw_product_category'])) > 0) {

                        $cart_items[$cart_item_key] = $cart_item;
                    }
//                    print_r($categories);
//                    die;
                } else if ($rule['pw_apply_to'] == 'pw_except_product_category') {
                    $categories = $this->cart_categories($cart_item);

                    if (count(array_intersect($categories, $rule['pw_except_product_category'])) == 0) {
                        $cart_items[$cart_item_key] = $cart_item;
                    }
                } else if ($rule['pw_apply_to'] == 'pw_product_tag') {
                    $categories = $this->get_cart_item_tag($cart_item);

                    if (count(array_intersect($categories, $rule['pw_product_tag'])) > 0) {
                        $cart_items[$cart_item_key] = $cart_item;
                    }
                } else if ($rule['pw_apply_to'] == 'pw_except_product_tag') {
                    $categories = $this->get_cart_item_tag($cart_item);

                    if (count(array_intersect($categories, $rule['pw_except_product_tag'])) == 0) {
                        $cart_items[$cart_item_key] = $cart_item;
                    }
                } else if ($rule['pw_apply_to'] == 'pw_product_brand') {
                    $categories = $this->get_cart_item_brand($cart_item);

                    if (count(array_intersect($categories, $rule['pw_product_brand'])) > 0) {
                        $cart_items[$cart_item_key] = $cart_item;
                    }
                } else if ($rule['pw_apply_to'] == 'pw_except_product_brand') {
                    $categories = $this->get_cart_item_brand($cart_item);

                    if (count(array_intersect($categories, $rule['pw_except_product_brand'])) == 0) {
                        $cart_items[$cart_item_key] = $cart_item;
                    }
                } else if ($rule['pw_apply_to'] == 'pw_product') {
                    $product_id = $cart_item['product_id'];
                    //print_r($rule['pw_product']);
                    //die ;
                    //$product_id = ( isset( $cart_item['variation_id'] ) && $cart_item['variation_id'] != '' ) ? $cart_item['variation_id'] : $product_id;
                    if (in_array($product_id, $rule['pw_product'])) {
                        $cart_items[$cart_item_key] = $cart_item;

                        //print_r($cart_items[$cart_item_key]);


                    }
                } else if ($rule['pw_apply_to'] == 'pw_except_product') {
                    $product_id = $cart_item['product_id'];
                    $product_id = (isset($cart_item['variation_id']) && $cart_item['variation_id'] != '') ? $cart_item['variation_id'] : $product_id;
                    if (!in_array($product_id, $rule['pw_except_product'])) {
                        $cart_items[$cart_item_key] = $cart_item;
                    }
                }
            }

            //print_r($cart_items);
            if ($rule['pw_type'] == 'flashsale')
                return $cart_items;

            $quantity = array();
            if ($rule['quantity_base'] == "product") {
                $quantity = $this->quantity_base_product($cart_items);
            } else if ($rule['quantity_base'] == "variation") {
                $quantity = $this->quantity_base_variation($cart_items);
            } else if ($rule['quantity_base'] == "line") {
                $quantity = $this->quantity_base_line($cart_items);
            } else if ($rule['quantity_base'] == "categories") {
                foreach ($cart_items as $item_key => $item) {
                    $categories = $this->cart_categories($item);

                    foreach ($categories as $category_id) {
                        if (isset($quantity[$category_id])) {
                            $quantity[$category_id] += $item['quantity'];
                        } else {
                            $quantity[$category_id] = $item['quantity'];
                        }
                    }
                }
            } else if ($rule['quantity_base'] == "all") {
                $quantity = $this->quantity_base_all($cart_items);
            }
            return $quantity;
        }

        public function quantity_base_all($cart_items)
        {
            $quantity = array();
            foreach ($cart_items as $item_key => $cart_item) {
                if (isset($quantity['all'])) {
                    $quantity['all'] += $cart_item['quantity'];
                } else {
                    $quantity['all'] = $cart_item['quantity'];
                }
            }
            return $quantity;
        }

        public function quantity_base_line($cart_items)
        {
            $quantity = array();
            foreach ($cart_items as $item_key => $item) {
                if (isset($quantity[$item_key])) {
                    $quantity[$item_key] += $item['quantity'];
                } else {
                    $quantity[$item_key] = $item['quantity'];
                }
            }
            return $quantity;
        }

        public function quantity_base_variation($cart_items)
        {
            $quantity = array();
            foreach ($cart_items as $item_key => $item) {
                if (isset($item['variation_id']) && !empty($item['variation_id'])) {
                    if (isset($quantity[$item['variation_id']]))
                        $quantity[$item['variation_id']] += $item['quantity'];
                    else
                        $quantity[$item['variation_id']] = $item['quantity'];
                } else {
                    if (isset($quantity[$item['product_id']]))
                        $quantity[$item['product_id']] += $item['quantity'];
                    else
                        $quantity[$item['product_id']] = $item['quantity'];
                }
            }
            return $quantity;
        }

        public function quantity_base_product($cart_items)
        {
            $quantity = array();
            foreach ($cart_items as $item_key => $item) {
                if (isset($quantity[$item['product_id']])) {
                    $quantity[$item['product_id']] += $item['quantity'];
                } else {
                    $quantity[$item['product_id']] = $item['quantity'];
                }
            }
            return $quantity;
        }

        public function get_cart_item_tag($cart_item)
        {
            $product_id = $cart_item['product_id'];
            $product_id = (isset($cart_item['variation_id']) && $cart_item['variation_id'] != '') ? $cart_item['variation_id'] : $product_id;
            $tags = array();
            $current = wp_get_post_terms($product_id, 'product_tag');
            foreach ($current as $tag) {
                $tags[] = $tag->term_id;
            }
            return $tags;
        }

        public function get_cart_item_brand($cart_item)
        {
            $brands = array();
            $product_id = $cart_item['product_id'];
            $product_id = (isset($cart_item['variation_id']) && $cart_item['variation_id'] != '') ? $cart_item['variation_id'] : $product_id;
            $current = wp_get_post_terms($product_id, 'product_brand');
            foreach ($current as $brand) {
                $brands[] = $brand->term_id;
            }
            return $brands;
        }

        public function cart_categories($cart_item)
        {
            $product_id = $cart_item['product_id'];
//            echo $product_id;
//            $product_id = (isset($cart_item['variation_id']) && $cart_item['variation_id'] != '') ? $cart_item['variation_id'] : $product_id;

            $categories = array();
            $current = wp_get_post_terms($product_id, 'product_cat');
            foreach ($current as $category) {
                $categories[] = $category->term_id;
            }
            return $categories;
        }

        public function user_capabilities()
        {
            global $current_user;
            get_currentuserinfo();
            $capabilities = $current_user->allcaps;
            $user_capabilities = array();

            if (is_array($capabilities)) {
                foreach ($capabilities as $capability => $ok) {
                    if ($ok) {
                        $user_capabilities[] = $capability;
                    }
                }
            }

            return $user_capabilities;
        }

        public function sort_cart_by_price($cart)
        {
            $sorted_cart = array();
            foreach ($cart as $cart_item_key => $values) {
                $sorted_cart[$cart_item_key] = $values;
            }
            //Sort the cart so that the lowest priced item is discounted when using block rules.
            uasort($sorted_cart, array($this, 'sort_by_price'));
            return $sorted_cart;
        }

        public function sort_by_price($cart_item_a, $cart_item_b)
        {
            return $cart_item_a['data']->get_price() > $cart_item_b['data']->get_price();
        }

        public function pw_user_roles()
        {
            global $current_user;
            get_currentuserinfo();
            return $current_user->roles;
        }

    }

    new PW_Discount_Price();
}
?>