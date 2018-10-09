(function ($) {

    "use strict";

    $(document).on('onEventToggleOpen', function (e, data) {


        if ( data.event.products.length <= 0 ) {
            return;
        }

        var $instance = data.$instance;
        var $event = $instance.$events.find('.stec-layout-event.active');
        var $inner = $event.find('.stec-layout-event-inner-woocommerce');

        if ( $inner.length <= 0 || !$inner.find('.stec-layout-event-inner-woocommerce-product-template')[0] ) {
            return;
        }

        var template = $inner.find('.stec-layout-event-inner-woocommerce-product-template')[0].outerHTML;
        $inner.find('.stec-layout-event-inner-woocommerce-product-template').remove();

        $(data.event.products).each(function () {

            var th = this;

            $(template).html(function (index, html) {

                var price = th.html_price;

                if ( th.has_child ) {
                    price += '<a href="' + th.url + '">' + window.stecLang.select_options + '</a>';
                }

                var quantity = th.stock_quantity === null ? '-' : th.stock_quantity;

                return html
                        .replace(/stec_replace_product_image/g, th.image)
                        .replace(/stec_replace_product_title/g, '<a href="' + th.url + '">' + th.title + '</a>')
                        .replace(/stec_replace_product_short_desc/g, th.post_data.excerpt)
                        .replace(/stec_replace_product_quantity/g, quantity)
                        .replace(/stec_replace_product_price/g, price);

            })
                    .removeClass('stec-layout-event-inner-woocommerce-product-template')
                    .appendTo($inner.find('.stec-layout-event-inner-woocommerce-products'));

            // get last appended
            var $product = $inner.find('.stec-layout-event-inner-woocommerce-product').last();

            var $link = $product.find('.stec-layout-event-inner-woocommerce-product-buy-addtocart');

            $link
                    .attr('data-pid', th.id)
                    .attr('data-quantity', '1')
                    .attr('data-sku', th.sku);

            if ( th.is_in_stock !== true || th.purchesable !== true ) {
                $product.find('.stec-layout-event-inner-woocommerce-product-about-outofstock').css('display', 'inline-block');
                $product.find('.stec-layout-event-inner-woocommerce-product-buy-addtocart').hide();
            }

            if ( th.is_on_sale === true ) {
                $product.find('.stec-layout-event-inner-woocommerce-product-about-sale').css('display', 'inline-block');
            }

            if ( th.is_featured === true ) {
                $product.find('.stec-layout-event-inner-woocommerce-product-about-featured').css('display', 'inline-block');
            }


        });

        // Remove tab preloaders
        $inner.find('.stec-layout-event-inner-preload-wrap').children().first().unwrap();
        $inner.find('.stec-layout-event-inner-preload-wrap').remove();
        $inner.find('.stec-preloader').remove();

    });

    $.stecExtend(function (master) {

        $(document).on(master.helper.clickHandle(), master.$instance.$events.path + ' .stec-layout-event-inner-woocommerce-product-buy-addtocart', function (e) {

            e.preventDefault();

            var event_id = $(this).parents('.stec-layout-event').first().attr('data-id');
            var repeat_offset = $(this).parents('.stec-layout-event').first().attr('data-repeat-time-offset');
            var event = master.calData.getEventById(event_id);

            var start = window.moment(event.start_date).
                    add(repeat_offset, 'seconds')
                    .utcOffset(event.timezone_utc_offset / 60, true)
                    .format('YYYY-MM-DD HH:mm:ss');

            if ( event.start_date_timestamp_tz ) {
                start = window.moment(event.start_date)
                        .utcOffset(window.moment().utcOffset(), true)
                        .add(repeat_offset, 'seconds')
                        .utcOffset(event.timezone_utc_offset / 60)
                        .format('YYYY-MM-DD HH:mm:ss');
            }

            var product = {
                id: $(this).attr('data-pid'),
                sku: $(this).attr('data-sku'),
                quantity: $(this).attr('data-quantity'),
                ajaxurl: $(this).attr('data-ajaxurl'),
                event_start_date: start + ' ' + event.calendar.timezone
            };

            addToCart(product, $(this).parent());

        });

        function addToCart(product, $button) {

            $.ajax({
                method: "POST",
                url: master.glob.options.siteurl + '/?wc-ajax=add_to_cart',
                data: {
                    product_id: product.id,
                    product_sku: product.sku,
                    quantity: product.quantity,
                    stec_event_start_date: product.event_start_date
                },
                beforeSend: function () {
                    // add preloader
                    $button.find('.stec-layout-event-inner-woocommerce-product-buy-addtocart').hide();
                    $button.find('.stec-layout-event-inner-woocommerce-product-buy-ajax-status').empty();
                    $(master.glob.template.preloader).appendTo($button.find('.stec-layout-event-inner-woocommerce-product-buy-ajax-status'));

                },
                success: function (data) {

                    $button.find('.stec-layout-event-inner-woocommerce-product-buy-ajax-status').empty();

                    if ( !data || data === null || (data.error && data.error === true) ) {

                        // add success icon
                        $('<i class="fa fa-times"></i>').appendTo($button.find('.stec-layout-event-inner-woocommerce-product-buy-ajax-status'));


                        // error handle
                        console.log('Error adding product to cart');

                    } else {

                        // update fragments 
                        if ( data.fragments ) {
                            for ( var key in data.fragments ) {
                                if ( data.fragments.hasOwnProperty(key) ) {
                                    $(key).replaceWith(data.fragments[key]);
                                }
                            }
                        }

                        // add success icon
                        $('<i class="fa fa-check"></i>').appendTo($button.find('.stec-layout-event-inner-woocommerce-product-buy-ajax-status'));

                        // decrement quantity

                        var quantity = $button.parent()
                                .find('.stec-layout-event-inner-woocommerce-product-quantity span').last().text();

                        if ( !isNaN(quantity) && quantity > 0 ) {
                            quantity--;

                            $button.parent()
                                    .find('.stec-layout-event-inner-woocommerce-product-quantity span').last().text(quantity);
                        }

                        setTimeout(function () {
                            $button.find('.stec-layout-event-inner-woocommerce-product-buy-ajax-status i').stop().fadeTo(1000, 0, function () {

                                $(this).remove();

                                if ( !isNaN(quantity) && quantity > 0 || quantity == '-' ) {
                                    $button.find('.stec-layout-event-inner-woocommerce-product-buy-addtocart').show();
                                }
                            });
                        }, 3000);


                    }

                },
                error: function (xhr, status, thrown) {
                    console.log(xhr + " " + status + " " + thrown);
                },
                dataType: 'json'

            });
        }

    });
})(jQuery);