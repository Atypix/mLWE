<?php

namespace Stachethemes\Stec;
?>
<div class="stachethemes-admin-section-tab" data-tab="woocommerce">

    <?php
    Admin_Html::html_info(__('Add Woocommerce Product', 'stec'));

    $products_list = Stec_WooCommerce::get_products_as_array_list();

    Admin_Html::html_select('wc_item_list', $products_list, '', false);

    Admin_Html::html_button(__('Add to Event', 'stec'), '', false, 'add-wc-item blue-button');
    ?>

    <!-- product template --> 
    <div class="stachethemes-admin-woocommerce-product stachethemes-admin-woocommerce-product-template">

        <div class="stachethemes-admin-woocommerce-product-head">
            <p class="stachethemes-admin-woocommerce-product-title"><span></span><span>stec_replace_wc_product_name</span></p>
            <input type="hidden" name="wc_product[]" value="stec_replace_wc_product_id" />
            <div>
                <?php
                Admin_Html::html_button(__('Remove', 'stec'), '', true, "light-btn delete");
                ?>
            </div>
        </div>

    </div>

    <?php if ( $event ) : ?>

        <?php
        foreach ( $event->get_products() as $product ) :
            
            if (!$product instanceof Event_Meta_Product) {
                continue;
            }
            

            $product_info = WC()->product_factory->get_product($product->id);

            if ( !$product_info ) {
                continue;
            }
            ?>

            <div class="stachethemes-admin-woocommerce-product">

                <div class="stachethemes-admin-woocommerce-product-head">
                    <p class="stachethemes-admin-woocommerce-product-title"><span></span><span><?php echo $product_info->get_title(); ?></span></p>
                    <input type="hidden" name="wc_product[]" value="<?php echo $product_info->get_id(); ?>" />
                    <div>
                        <?php
                        Admin_Html::html_button(__('Remove', 'stec'), '', true, "light-btn delete");
                        ?>
                    </div>
                </div>

            </div>

        <?php endforeach; ?>

<?php endif; ?>

</div>