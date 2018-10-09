<?php

namespace Stachethemes\Stec;




use WP_Query;




class Stec_WooCommerce {



    private static $products = false;



    /**
     * Adds the event start date meta data to orders
     */
    public static function add_filters() {

        $start_date_label = __('Event start date', 'stec');

        add_filter('woocommerce_add_cart_item_data', function ( $cartItemData, $productId, $variationId ) {

            $start_date = Admin_Helper::post('stec_event_start_date', null);

            if ( $start_date ) {
                $cartItemData['stec_event_start_date'] = $start_date;
            }

            return $cartItemData;
        }, 10, 3);

        add_filter('woocommerce_get_cart_item_from_session', function ( $cartItemData, $cartItemSessionData, $cartItemKey ) {
            if ( isset($cartItemSessionData['stec_event_start_date']) ) {
                $cartItemData['stec_event_start_date'] = $cartItemSessionData['stec_event_start_date'];
            }

            return $cartItemData;
        }, 10, 3);

        add_filter('woocommerce_get_item_data', function ( $data, $cartItem ) use ($start_date_label) {
            if ( isset($cartItem['stec_event_start_date']) ) {
                $data[] = array(
                        'name'  => $start_date_label,
                        'value' => $cartItem['stec_event_start_date']
                );
            }

            return $data;
        }, 10, 2);

        add_action('woocommerce_add_order_item_meta', function ( $itemId, $values, $key ) use ($start_date_label) {
            if ( isset($values['stec_event_start_date']) ) {
                wc_add_order_item_meta($itemId, $start_date_label, $values['stec_event_start_date']);
            }
        }, 10, 3);
    }



    public static function get_products() {

        if ( self::$products !== false ) {
            return self::$products;
        }

        $products_list = array();

        $query = new WP_Query(
                array(
                'post_type'      => array('product'),
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'post_status'    => 'publish'
                )
        );

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) :
                $query->the_post();

                $products_list[] = WC()->product_factory->get_product(get_the_ID());


            endwhile;
        }

        self::$products = $products_list;

        return self::$products;
    }



    public static function get_products_as_array_list() {

        $arr = array();

        $products = self::get_products();

        foreach ( $products as $item ) {
            $arr[$item->get_id()] = $item->get_title();
        }

        return $arr;
    }

}
