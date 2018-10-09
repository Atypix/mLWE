;(function ($) {

    var gc_quantity_display_status = $("div.gift_card_template_button input[name='quantity']").css("display");

    var init_plugin = function () {
        if (jQuery().prettyPhoto) {
            $('[rel="prettyPhoto[ywgc-choose-design]"]').prettyPhoto({

                hook: 'rel',
                social_tools: false,
                theme: 'pp_woocommerce',
                default_width: '80%',
                default_height: '100%',
                horizontal_padding: 20,
                opacity: 0.8,
                deeplinking: false,
                keyboard_shortcuts: true,
                allow_resize: false,
                changepicturecallback: function() {
                    $( document.body ).trigger( 'ywgc_request_window_created' );
                }
            });
        }
    };

    $( document.body ).on( 'ywgc_request_window_created', function () {

        $( '.pp_content_container' ).css( "position", "fixed" );
        $( '.pp_content_container' ).css( "top", "50%" );
        $( '.pp_content_container' ).css( "transform", "translateY(-50%)" );
        $( '.pp_content_container' ).css( "max-width", "80%" );
        $( '.pp_content_container' ).css( "max-height", "100%" );
        $( '.pp_content_container .pp_content' ).css( "max-width", "100%" );
        $( '.pp_content_container .pp_content' ).css( "max-height", "600px" );
        $( '.pp_content_container .pp_content' ).css( "overflow-y", "scroll" );

    });

    $(document).ready(function () {

        init_plugin();

        /** init datepicker */
        $("div.ywgc-generator .datepicker").datepicker({dateFormat: "yy-mm-dd", minDate: +1, maxDate: "+1Y"});
    });

    var hide_on_gift_as_present = function () {
        if ($('input[name="ywgc-as-present-enabled"]').length) {
            $('.ywgc-generator').hide();
            show_gift_card_editor(false);
        }
    }

    show_hide_add_to_cart_button();

    hide_on_gift_as_present();

    // $(document).on('click', '.ywgc-choose-image.ywgc-choose-template', function (e) {
    //     init_plugin();
    // });

    $(document).on('click', '.pp_content_container .ywgc-choose-preset', function (e) {
        var id = $(this).data('design-id');
        var design_url = $(this).data('design-url');
        $('#ywgc-main-image').attr('src', design_url);
        $(document).trigger('ywgc-picture-changed', ['template', id]);
        $.prettyPhoto.close();
    });

    $(document).on('click', 'a.ywgc-show-category', function (e) {

        var current_category = $(this).data("category-id");

        //  highlight the selected category
        $('a.ywgc-show-category').removeClass('ywgc-category-selected');
        $(this).addClass('ywgc-category-selected');

        //  Show only the design of the selected category
        if ('all' !== current_category) {
            $('.ywgc-design-item').hide();
            $('.ywgc-design-item.' + current_category).show();
        }
        else {
            $('.ywgc-design-item').show();

        }
        return false;
    });

    $(document).on('ywgc-picture-changed', function (event, type, id) {

            $('#ywgc-design-type').val(type);
            $('#ywgc-template-design').val(id);
        }
    );


    $(document).on('click', 'a.ywgc-show-giftcard', show_gift_card_form);

    function show_gift_card_form() {
        $('.ywgc-enter-code').slideToggle(400, function () {
            $('.ywgc-enter-code').find(':input:eq(0)').focus();
        });
        return false;
    }

    /** Show the edit gift card button */
    $("button.ywgc-do-edit").css("display", "inline");


    function update_gift_card_amount(amount) {

        $("div.ywgc-card-amount span.amount").text(amount);

    }

    function show_gift_card_editor(val) {
        $('button.gift_card_add_to_cart_button').attr('disabled', !val);
    }

    function show_hide_add_to_cart_button() {
        var select_element = $(".gift-cards-list select");
        var gift_this_product = $('#give-as-present');

        if (!gift_this_product.length) {
            $('.gift-cards-list input.ywgc-manual-amount').addClass('ywgc-hidden');
            $('.ywgc-manual-amount-error').remove();

            var amount = 0;
            if ((select_element.length == 0) || ("-1" == select_element.val())) {
                /* the user should enter a manual value as gift card amount */
                var manual_amount_element = $('.gift-cards-list input.ywgc-manual-amount');
                if (manual_amount_element.length) {
                    var manual_amount = manual_amount_element.val();
                    manual_amount_element.removeClass('ywgc-hidden');

                    var test_amount = new RegExp('^[1-9]\\d*(?:' + '\\' + ywgc_data.currency_format_decimal_sep + '\\d{1,2})?$', 'g')

                    if (manual_amount.length && !test_amount.test(manual_amount)) {
                        manual_amount_element.after('<div class="ywgc-manual-amount-error">' + ywgc_data.manual_amount_wrong_format + '</div>');
                        show_gift_card_editor(false);
                    }
                    else {
                        if ( parseInt( manual_amount ) < parseInt( ywgc_data.manual_minimal_amount ) && ( ywgc_data.manual_minimal_amount_error.length > 0 ) ){
                            manual_amount_element.after('<div class="ywgc-manual-amount-error">' + ywgc_data.manual_minimal_amount_error + '</div>');
                            show_gift_card_editor(false);
                        }
                        else
                            /** If the user entered a valid amount, show "add to cart" button and gift card
                             *  editor.
                             */
                            if (manual_amount) {
                                // manual amount is a valid numeric value
                                show_gift_card_editor(true);

                                amount = accounting.unformat(manual_amount, ywgc_data.mon_decimal_point);

                                if (amount <= 0) {
                                    show_gift_card_editor(false);
                                }
                                else {
                                    amount = accounting.formatMoney(amount, {
                                        symbol: ywgc_data.currency_format_symbol,
                                        decimal: ywgc_data.currency_format_decimal_sep,
                                        thousand: ywgc_data.currency_format_thousand_sep,
                                        precision: ywgc_data.currency_format_num_decimals,
                                        format: ywgc_data.currency_format
                                    });

                                    show_gift_card_editor(true);
                                }
                            }
                            else {
                                show_gift_card_editor(false);
                            }
                    }
                }
            }
            else if (!select_element.val()) {
                show_gift_card_editor(false);
            }
            else {
                show_gift_card_editor(true);

                amount = select_element.children("option:selected").data('wc-price');
            }

            update_gift_card_amount(amount);
        }
    }

    $( document ).on('input', '.gift-cards-list input.ywgc-manual-amount', function (e) {

        show_hide_add_to_cart_button();

    });

    function add_recipient() {
        var last = $('div.ywgc-single-recipient').last();
        var required = ywgc_data.mandatory_email ? 'required' : '';
        var new_div = '<div class="ywgc-single-recipient">' +
            '<input type="email" name="ywgc-recipient-email[]" class="ywgc-recipient yith_wc_gift_card_input_recipient_details" placeholder="' + ywgc_data.email + '" ' + required + '/>' +
            '<input type="text" name="ywgc-recipient-name[]" class="yith_wc_gift_card_input_recipient_details" placeholder="' + ywgc_data.name + '" ' + required + '/>' +
            '<a href="#" class="ywgc-remove-recipient">x</a> ' +
            '</div>';

        last.after(new_div);

        //  show the remove recipient links
        $("a.ywgc-remove-recipient").css('visibility', 'visible');

        $("div.gift_card_template_button input[name='quantity']").css("display", "none");

        //  show a message for quantity disabled when multi recipients is entered
        if (!$("div.gift_card_template_button div.ywgc-multi-recipients").length) {
            $("div.gift_card_template_button div.quantity").after("<div class='ywgc-multi-recipients'><span>" + ywgc_data.multiple_recipient + "</span></div>");
        }
    }

    function add_physical_recipient() {
        var last = $('div.ywgc-single-recipient').last();
        var required = ywgc_data.mandatory_email ? 'required' : '';
        var new_div = '<div class="ywgc-single-recipient">' +
            '<input type="text" name="ywgc-recipient-name[]" class="ywgc-recipient yith_wc_gift_card_input_recipient_details" placeholder="' + ywgc_data.name + '" ' + required + '/>' +
            '<a href="#" class="ywgc-remove-recipient">x</a> ' +
            '</div>';

        last.after(new_div);

        //  show the remove recipient links
        $("a.ywgc-remove-recipient").css('visibility', 'visible');

        $("div.gift_card_template_button input[name='quantity']").css("display", "none");

        //  show a message for quantity disabled when multi recipients is entered
        if (!$("div.gift_card_template_button div.ywgc-multi-recipients").length) {
            $("div.gift_card_template_button div.quantity").after("<div class='ywgc-multi-recipients'><span>" + ywgc_data.multiple_recipient + "</span></div>");
        }
    }

    function remove_recipient(element) {
        //  remove the element
        $(element).parent("div.ywgc-single-recipient").remove();

        //  Avoid the deletion of all recipient
        var emails = $('input[name="ywgc-recipient-email[]"]');
        if (emails.length == 1) {
            //  only one recipient is entered...
            $("a.hide-if-alone").css('visibility', 'hidden');
            $("div.gift_card_template_button input[name='quantity']").css("display", gc_quantity_display_status);

            $("div.ywgc-multi-recipients").remove();
        }
    }

    $(document).on('click', 'a.add-recipient', function (e) {
        e.preventDefault();
        add_recipient();
    });

    $(document).on('click', 'a.add-physical-recipient', function (e) {
        e.preventDefault();
        add_physical_recipient();
    });

    $(document).on('click', 'a.ywgc-remove-recipient', function (e) {
        e.preventDefault();
        remove_recipient($(this));
    });

    $(document).on('input', '#ywgc-edit-message', function (e) {
        $(".ywgc-card-message").html($('#ywgc-edit-message').val());
    });

    $(document).on('change', '.gift-cards-list select', function (e) {
        show_hide_add_to_cart_button();
    });

    $(document).on('click', 'a.customize-gift-card', function (e) {
        e.preventDefault();
        $('div.summary.entry-summary').after('<div class="ywgc-customizer"></div>');
    });

    /** Set to default the image used on the gift card editor on product page */
    $(document).on('click', '.ywgc-default-picture', function (e) {
        e.preventDefault();
        var control = $('#ywgc-upload-picture');
        control.replaceWith(control = control.clone(true));
        $('.ywgc-main-image img.ywgc-main-image').attr('src', ywgc_data.default_gift_card_image);

        //  Reset style if previously a custom image was used
        $(".ywgc-main-image").css("background-color", "");
        $("div.gift-card-too-small").remove();
        $(document).trigger('ywgc-picture-changed', ['default']);
    });

    // Integration with yith woocommerce product bundles premium
    if ( $( '.yith-wcpb-product-bundled-items' ).length ){

        setTimeout( function(){
            $( '#give-as-present' ).prop( 'disabled', false );
        }, 1000 );

    }

    // Customizing the image of the gif from the product image
    $( 'body' ).on( 'click', '.ywgc-product-picture', function () {

        var next = $( 'body .single_add_to_cart_button' ).next();
        if ( $( next ).attr( 'name' ) == "product_id" )
            var product_id = $( next ).val();
        else{
            var next = $( next ).next();
            if ( $( next ).attr( 'name' ) == "product_id" )
                var product_id = $( next ).val();
            else
                var product_id = $( 'body .single_add_to_cart_button' ).val();
        }


        // Integration with yith woocommerce product bundles premium
        if ( $( '.yith-wcpb-product-bundled-items' ).length ){

            var product_id = $( '.yith-wcpb-bundle-form' ).find( 'input[name=add-to-cart]' ).val();

        }

        var data = {
            action       : 'yith_wc_gift_card_ajax_replace_for_product_image',
            product_id   : product_id,
        }

        yith_wc_gift_card_ajaxGo( data, '#yith_wc_gift_card_ajax_replace_for_product_image' );

        $(document).trigger('ywgc-picture-changed', ['product_image']);

    });

    /** Show the custom file choosed by the user as the image used on the gift card editor on product page */
    $(document).on('click', '.ywgc-custom-picture', function (e) {
        $('#ywgc-upload-picture').click();
    });

    $('#ywgc-upload-picture').on('change', function () {
        var preview_image = function (file) {
            var oFReader = new FileReader();
            oFReader.readAsDataURL(file);

            oFReader.onload = function (oFREvent) {
                document.getElementById("ywgc-main-image").src = oFREvent.target.result;
                $(document).trigger('ywgc-picture-changed', ['custom']);

                /** Check the size of the file choosed and notify it to the user if it is too small */
                if ($("#ywgc-main-image").width() < $(".ywgc-main-image").width()) {
                    $(".ywgc-main-image").css("background-color", "#ffe326");
                    $(".ywgc-preview").prepend('<div class="gift-card-too-small">' + ywgc_data.notify_custom_image_small + '</div>');
                }
                else {
                    $(".ywgc-main-image").css("background-color", "");
                }
            }
        }

        //  Remove previous errors shown
        $(".ywgc-picture-error").remove();

        var ext = $(this).val().split('.').pop().toLowerCase();

        if ( $.inArray(ext, ['gif', 'png', 'jpg', 'jpeg', 'bmp'] ) == -1) {
            $( "div.gift-card-content-editor.step-appearance" ).append( '<span class="ywgc-picture-error">' +
                ywgc_data.invalid_image_extension + '</span>' );
            return;
        }

        if ( $(this)[0].files[0].size > ywgc_data.custom_image_max_size * 1024 * 1024 && ywgc_data.custom_image_max_size > 0 ) {
            $( "div.gift-card-content-editor.step-appearance").append('<span class="ywgc-picture-error">' +
                ywgc_data.invalid_image_size + '</span>' );
            return;
        }

        preview_image( $(this)[0].files[0] );
    });

    $(document).on('click', '#give-as-present', function (e) {

        e.preventDefault();
        $("#give-as-present").css("display", "none");

        $("div.ywgc-generator").append('<input type="hidden" name="ywgc-as-present" value="1">');
        if ($('.woocommerce-variation-add-to-cart').length) {
            $("div.ywgc-generator").insertBefore($('.woocommerce-variation-add-to-cart '));
        } else {
            $("div.ywgc-generator").prependTo($('form.cart'));
        }

        $('button.single_add_to_cart_button').data('add-to-cart-text', $('button.single_add_to_cart_button').text());
        $('button.single_add_to_cart_button').html(ywgc_data.add_gift_text);


        $("#ywgc-cancel-gift-card").css("display", "inline-block");
        $("div.ywgc-generator").css('display', 'block');

        // Integration with yith woocommerce product bundles premium
        if ( $( '.yith-wcpb-product-bundled-items' ).length ){

            if ( $( '#yith_wcyc_automatically_gift_this_product' ).val() == 'yes' ){
                $( '.ywgc-template' ).before( $( '.yith-wcpb-product-bundled-items' ) );
                //$( '.single_add_to_cart_button' ).css( 'margin-bottom', '20px' );
            }
            else{
                $( '.single_add_to_cart_button' ).after( $( '.yith-wcpb-product-bundled-items' ) );
                $( '.single_add_to_cart_button' ).css( 'margin-bottom', '20px' );
            }

        }

    });

    $(document).on('click', '#ywgc-cancel-gift-card', function (e) {
        e.preventDefault();
        $("div.ywgc-generator input[name='ywgc-as-present']").remove();

        $('button.single_add_to_cart_button').html($('button.single_add_to_cart_button').data('add-to-cart-text'));

        $("#give-as-present").css("display", "inline-block");
        $("#ywgc-cancel-gift-card").css("display", "none");

        $("div.ywgc-generator").css('display', 'none');
    });

    $(document).on('change', '#ywgc-postdated', function (e) {
        if ($(this).is(':checked')) {
            $("#ywgc-delivery-date").removeClass("ywgc-hidden");
        }
        else {
            $("#ywgc-delivery-date").addClass("ywgc-hidden");
        }
    });

    function set_giftcard_value(value) {
        $("div.ywgc-card-amount span.amount").html(value);
    }

    $('.variations_form.cart').on('found_variation', function (ev, variation) {
        if (typeof variation !== "undefined") {
            $('#give-as-present').prop('disabled', false);
                var price_html = variation.price_html != '' ? $(variation.price_html).html() : $(".product-type-variable").find(".woocommerce-Price-amount.amount").first().html();
                set_giftcard_value(price_html);

        }
    });

    $(document).on('reset_data', function () {
        $('#give-as-present').prop('disabled', true);
        set_giftcard_value('');
    });

    function show_edit_gift_cards(element, visible) {
        var container = $(element).closest("div.ywgc-gift-card-content");
        var edit_container = container.find("div.ywgc-gift-card-edit-details");
        var details_container = container.find("div.ywgc-gift-card-details");

        if (visible) {
            //go to edit
            edit_container.removeClass("ywgc-hide");
            edit_container.addClass("ywgc-show");

            details_container.removeClass("ywgc-show");
            details_container.addClass("ywgc-hide");
        }
        else {
            //go to details
            edit_container.removeClass("ywgc-show");
            edit_container.addClass("ywgc-hide");

            details_container.removeClass("ywgc-hide");
            details_container.addClass("ywgc-show");
        }
    }

    $(document).on('click', 'button.ywgc-apply-edit', function (e) {

        var clicked_element = $(this);

        var container = clicked_element.closest("div.ywgc-gift-card-content");

        var sender = container.find('input[name="ywgc-edit-sender"]').val();
        var recipient = container.find('input[name="ywgc-edit-recipient"]').val();
        var message = container.find('textarea[name="ywgc-edit-message"]').val();
        var item_id = container.find('input[name="ywgc-item-id"]').val();

        var gift_card_element = container.find('input[name="ywgc-gift-card-id"]');
        var gift_card_id = gift_card_element.val();

        //  Apply changes, if apply button was clicked
        if (clicked_element.hasClass("apply")) {
            var data = {
                'action': 'edit_gift_card',
                'gift_card_id': gift_card_id,
                'item_id': item_id,
                'sender': sender,
                'recipient': recipient,
                'message': message
            };

            container.block({
                message: null,
                overlayCSS: {
                    background: "#fff url(" + ywgc_data.loader + ") no-repeat center",
                    opacity: .6
                }
            });

            $.post(ywgc_data.ajax_url, data, function (response) {
                if (response.code > 0) {
                    container.find("span.ywgc-sender").text(sender);
                    container.find("span.ywgc-recipient").text(recipient);
                    container.find("span.ywgc-message").text(message);

                    if (response.code == 2) {
                        gift_card_element.val(response.values.new_id);
                    }
                }

                container.unblock();

                //go to details
                show_edit_gift_cards(clicked_element, false);
            });
        }
    });

    $(document).on('click', 'button.ywgc-cancel-edit', function (e) {

        var clicked_element = $(this);

        //go to details
        show_edit_gift_cards(clicked_element, false);
    });

    $(document).on('click', 'button.ywgc-do-edit', function (e) {

        var clicked_element = $(this);
        //go to edit
        show_edit_gift_cards(clicked_element, true);
    });

    $(document).on('click', 'form.gift-cards_form button.gift_card_add_to_cart_button', function (e) {

        $('div.gift-card-content-editor.step-content p.ywgc-filling-error').remove();
        if ($('#ywgc-postdated').is(':checked') && !$.datepicker.parseDate('yy-mm-dd', $('#ywgc-delivery-date').val())) {
            $('div.gift-card-content-editor.step-content').append('<p class="ywgc-filling-error">' + ywgc_data.missing_scheduled_date + '</p>');
            e.preventDefault();
        }
    });

    $(document).on('click', '.ywgc-gift-card-content a.edit-details', function (e) {
        e.preventDefault();
        $(this).addClass('ywgc-hide');
        $('div.ywgc-gift-card-details').toggleClass('ywgc-hide');
    });


    $('.ywgc-single-recipient input[name="ywgc-recipient-email[]"]').each(function (i, obj) {
        $(this).on('input', function () {
            $(this).closest('.ywgc-single-recipient').find('.ywgc-bad-email-format').remove();
        });
    });

    function validateEmail(email) {
        var test_email = new RegExp('^[A-Z0-9._%+-]+@[A-Z0-9.-]+\\.[A-Z]{2,}$', 'i');
        return test_email.test(email);
    }

    $(document).on('submit', '.gift-cards_form', function (e) {
        var can_submit = true;
        $('.ywgc-single-recipient input[name="ywgc-recipient-email[]"]').each(function (i, obj) {

            if ($(this).val() && !validateEmail($(this).val())) {
                $(this).closest('.ywgc-single-recipient').find('.ywgc-bad-email-format').remove();
                $(this).after('<span class="ywgc-bad-email-format">' + ywgc_data.email_bad_format + '</span>');
                can_submit = false;
            }
        });
        if (!can_submit) {
            e.preventDefault();
        }
    });
    /** Manage the WooCommerce 2.6 changes in the cart template
     * with AJAX
     * @since 1.4.0
     */

    $(document).on(
        'click',
        'a.ywgc-remove-gift-card ',
        remove_gift_card_code);

    function remove_gift_card_code(evt) {
        evt.preventDefault();
        var $table = $(evt.currentTarget).parents('table');
        var gift_card_code = $(evt.currentTarget).data('gift-card-code');

        block($table);

        var data = {
            security: ywgc_data.gift_card_nonce,
            code: gift_card_code,
            action: 'ywgc_remove_gift_card_code'
        };

        $.ajax({
            type: 'POST',
            url: ywgc_data.ajax_url,
            data: data,
            dataType: 'html',
            success: function (response) {
                show_notice(response);
                $(document.body).trigger('removed_gift_card');
                unblock($table);
            },
            complete: function () {
                update_cart_totals();
            }
        });
    }

    /**
     * Apply the gift card code the same way WooCommerce do for Coupon code
     *
     * @param {JQuery Object} $form The cart form.
     */
    $('form.ywgc-enter-code').submit(function (e) {
        e.preventDefault();
        block($(this));

        var $form = $(this);
        var $text_field = $form.find('input[name="gift_card_code"]');
        var gift_card_code = $text_field.val();

        var data = {
            security: ywgc_data.gift_card_nonce,
            code: gift_card_code,
            action: 'ywgc_apply_gift_card_code'
        };

        $.ajax({
            type: 'POST',
            url: ywgc_data.ajax_url,
            data: data,
            dataType: 'html',
            success: function (response) {
                show_notice(response);
                $(document.body).trigger('applied_gift_card');
            },
            complete: function () {

                unblock($form);
                $text_field.val('');

                update_cart_totals();
            }
        });
    });

    /**
     * Block a node visually for processing.
     *
     * @param {JQuery Object} $node
     */
    var block = function ($node) {
        $node.addClass('processing').block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });
    };

    /**
     * Unblock a node after processing is complete.
     *
     * @param {JQuery Object} $node
     */
    var unblock = function ($node) {
        $node.removeClass('processing').unblock();
    };

    /**
     * Gets a url for a given AJAX endpoint.
     *
     * @param {String} endpoint The AJAX Endpoint
     * @return {String} The URL to use for the request
     */
    var get_url = function (endpoint) {
        return ywgc_data.wc_ajax_url.toString().replace(
            '%%endpoint%%',
            endpoint
        );
    };

    /**
     * Clear previous notices and shows new one above form.
     *
     * @param {Object} The Notice HTML Element in string or object form.
     */
    var show_notice = function (html_element) {
        $('.woocommerce-error, .woocommerce-message').remove();
        $(ywgc_data.notice_target).before(html_element);
    };

    /**
     * Update the cart after something has changed.
     */
    function update_cart_totals() {
        block($('div.cart_totals'));

        $.ajax({
            url: get_url('get_cart_totals'),
            dataType: 'html',
            success: function (response) {
                $('div.cart_totals').replaceWith(response);
            }
        });

        $(document.body).trigger('update_checkout');
    }

    /**
     * Integration with YITH Quick View and some third party themes
     */
    $(document).on('qv_loader_stop yit_quick_view_loaded flatsome_quickview', function () {

        show_hide_add_to_cart_button();

        hide_on_gift_as_present();

    });

    /**
     * Checking if the gift this product form has to be displayed automatically coming from shop page
     * or from the settings of the product
     */
    function yith_check_show_gift_this_product_form() {

        if ( $( '#yith_show_gift_this_product_form' ).val() == 'yes' )
            $( '#give-as-present' ).click();

        if ( $( '#yith_wcyc_automatically_gift_this_product' ).val() == 'yes' ){

            $( '#give-as-present' ).click();
            $( '.ywgc-product-picture' ).click();

        }


    }

    yith_check_show_gift_this_product_form();



    function yith_wc_gift_card_ajaxGo( params, result, beforeSend ) {
        $.ajax({
            data      : params,
            url       : ywgc_data.ajax_url,
            type      : 'post',
            beforeSend: function () {
                $( result ).html( beforeSend );
            },
            error     : function ( response ) {
                console.log( 'yith_wc_gift_card_ajaxGo ajax error -> ' + response );
            },
            success   : function ( response ) {
                $( result ).html( response );
            }
        });
    }

})(jQuery);
