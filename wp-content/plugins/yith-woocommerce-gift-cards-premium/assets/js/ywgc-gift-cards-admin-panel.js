jQuery(document).ready(function ($) {

    function yith_wc_gift_card_admin_panel( trigger_brightness_args, trigger_hidden_args ){

        this.trigger_brightness_args = trigger_brightness_args;
        this.trigger_hidden_args = trigger_hidden_args;

    }

    yith_wc_gift_card_admin_panel.prototype.yith_wc_gift_card_blank_brightness_show = function( elements ){

        elements.forEach( function ( element ) {

            $( element ).closest( 'tr' ).find( 'th' ).find( '.yith_wc_gift_card_blank_brightness_ID' ).show();
            $( element ).closest( 'tr' ).find( 'td' ).find( '.yith_wc_gift_card_blank_brightness_ID' ).show();

        });

    }

    yith_wc_gift_card_admin_panel.prototype.yith_wc_gift_card_blank_brightness_hide = function( elements ){

        elements.forEach( function ( element ) {

            $( element ).closest( 'tr' ).find( 'th' ).find( '.yith_wc_gift_card_blank_brightness_ID' ).hide();
            $( element ).closest( 'tr' ).find( 'td' ).find( '.yith_wc_gift_card_blank_brightness_ID' ).hide();

        });

    }

    yith_wc_gift_card_admin_panel.prototype.yith_wc_gift_card_blank_brightness_classes = function( element ){

        $( element ).closest( 'tr' ).find( 'th' ).css( 'position', 'relative' );

        $( element ).closest( 'tr' ).find( 'th' ).append( "<div class='yith_wc_gift_card_blank_brightness_ID yith_wc_gift_card_blank_brightness'></div>" );

        $( element ).closest( 'tr' ).find( 'td' ).css( 'position', 'relative' );

        $( element ).closest( 'tr' ).find( 'td' ).append( "<div class='yith_wc_gift_card_blank_brightness_ID yith_wc_gift_card_blank_brightness'></div>" );

    }

    yith_wc_gift_card_admin_panel.prototype.yith_wc_gift_card_blank_brightness_check_dependencies = function( dependencies ){

        var $this = this;

        dependencies.forEach( function ( dependency ) {

            $this.trigger_brightness_args.forEach( function ( item ) {

                if ( item.trigger == dependency )
                    $this.yith_wc_gift_card_blank_brightness_check( item );

            });

        });

    }

    yith_wc_gift_card_admin_panel.prototype.yith_wc_gift_card_blank_brightness_check = function( item ){

        var $this = this;

        switch( item.type ) {

            case 'checkbox':

                if( $( item.trigger ).is( ':checked' ) ){

                    if ( item.hide_when == 'checked' ){
                        $this.yith_wc_gift_card_blank_brightness_show( item.elements );
                    }
                    else{
                        $this.yith_wc_gift_card_blank_brightness_hide( item.elements );
                        $this.yith_wc_gift_card_blank_brightness_check_dependencies( item.dependencies );
                    }

                }
                else{

                    if ( item.hide_when == 'checked' ){
                        $this.yith_wc_gift_card_blank_brightness_hide( item.elements );
                        $this.yith_wc_gift_card_blank_brightness_check_dependencies( item.dependencies );
                    }
                    else {
                        $this.yith_wc_gift_card_blank_brightness_show( item.elements) ;
                    }

                }
                break;

            case 'radio':

                if( $( item.trigger + ':checked' ).val() == item.show_when ){

                    $this.yith_wc_gift_card_blank_brightness_hide( item.elements) ;

                }
                else
                    $this.yith_wc_gift_card_blank_brightness_show( item.elements );

                break;
        }



    }

    yith_wc_gift_card_admin_panel.prototype.yith_wc_gift_card_set_blank_brightness = function(){

        var aux_elements = [];
        var $this = this;

        this.trigger_brightness_args.forEach( function ( item ) {

            $( item.trigger ).on( "click", function () {

                $this.yith_wc_gift_card_blank_brightness_check( item );

            });

            item.elements.forEach( function ( element ) {

                if ( $.inArray( element, aux_elements ) == -1 ){
                    aux_elements.push( element );
                    $this.yith_wc_gift_card_blank_brightness_classes( element );
                }

            });

            $this.yith_wc_gift_card_blank_brightness_check( item );

        });

    }

    yith_wc_gift_card_admin_panel.prototype.ywgc_hide_all = function( elemnts ){

        elemnts.forEach( function ( element ) {

            $( element ).closest( 'tr' ).fadeIn();

        });

    }

    yith_wc_gift_card_admin_panel.prototype.ywgc_show_all = function( elemnts ){

        elemnts.forEach( function ( element ) {

            $( element ).closest( 'tr' ).fadeOut();

        });

    }

    yith_wc_gift_card_admin_panel.prototype.yith_wc_gift_card_hide_elements_check = function( item ){

        var $this = this;

        if( $( item.trigger ).is( ':checked' ) ){

            if ( item.hide_when == 'checked' ){
                $this.ywgc_show_all( item.elements );
            }
            else{
                $this.ywgc_hide_all( item.elements );
            }

        }
        else{

            if ( item.hide_when == 'checked' ){
                $this.ywgc_hide_all( item.elements );
            }
            else {
                $this.ywgc_show_all( item.elements );
            }

        }

    }

    yith_wc_gift_card_admin_panel.prototype.yith_wc_gift_card_set_hide_elements = function(){

        var aux_elements = [];
        var $this = this;

        this.trigger_hidden_args.forEach( function ( item ) {

            $( item.trigger ).on( "click", function () {

                $this.yith_wc_gift_card_hide_elements_check( item );

            });

            $this.yith_wc_gift_card_hide_elements_check( item );

        });

    }

    var trigger_brightness_args = [
        {
            'trigger'        :'#ywgc_physical_details',
            'hide_when'      :'no_checked',
            'type'           :'checkbox',
            'elements'       :[ '#ywgc_physical_details_mandatory' ],
            'dependencies'   :[],
        },
        {
            'trigger'        :'#ywgc_permit_free_amount',
            'hide_when'      :'no_checked',
            'type'           :'checkbox',
            'elements'       :[ '#ywgc_minimal_amount_gift_card' ],
            'dependencies'   :[],
        },
        {
            'trigger'        :'#ywgc_permit_its_a_present',
            'hide_when'      :'no_checked',
            'type'           :'checkbox',
            'elements'       :[ '#ywgc_gift_this_product_label',
                                '#ywgc_permit_its_a_present_shop_page',
                                '#ywgc_gift_this_product_add_to_cart',
                                '#ywgc_gift_this_product_apply_gift_card',
                                'input:radio[name=ywgc_gift_this_product_email_button_redirect]',
                                '#select2-ywgc_gift_this_product_redirected_page-container',
                                '#ywgc_gift_this_product_email_button_label' ],
            'dependencies'   :[ 'input:radio[name=ywgc_gift_this_product_email_button_redirect]' ],
        },
        {
            'trigger'        :'#ywgc_enable_pre_printed',
            'hide_when'      :'checked',
            'type'           :'checkbox',
            'elements'       :[ '#ywgc_code_pattern' ],
            'dependencies'   :[],
        },
        {
            'trigger'        :'input:radio[name=ywgc_gift_this_product_email_button_redirect]',
            'show_when'      :'customize_page',
            'type'           :'radio',
            'elements'       :[ '#select2-ywgc_gift_this_product_redirected_page-container' ],
            'dependencies'   :[],
        },
        {
            'trigger'        :'#ywgc_custom_design',
            'hide_when'      :'no_checked',
            'type'           :'checkbox',
            'elements'       :[ '#ywgc_custom_image_max_size' ],
            'dependencies'   :[],
        },
        {
            'trigger'        :'#ywgc_template_design',
            'hide_when'      :'no_checked',
            'type'           :'checkbox',
            'elements'       :[ '#ywgc_show_preset_title' ],
            'dependencies'   :[],
        },
    ];

    var trigger_hidden_args = [
        /*{
            'trigger'        :'#ywgc_permit_its_a_present',
            'hide_when'      :'no_checked',
            'type'           :'checkbox',
            'elements'       :[
                '#ywgc_gift_this_product_label',
                '#ywgc_permit_its_a_present_shop_page',
                '#ywgc_permit_its_a_present_product_image',
                '#ywgc_add_to_cart_gift_this_product',
                '#ywgc_apply_gift_card_gift_this_product',
                'input:radio[name=ywgc_gift_this_product_redirected_page_radio]:checked',
                '#ywgc_gift_this_product_redirected_to_product_page',
                '#select2-ywgc_gift_this_product_redirected_page-container',
                '#ywgc_gift_this_product_add_to_cart',
                '#ywgc_gift_this_product_apply_gift_card',
                'input:radio[name=ywgc_gift_this_product_email_button_redirect]',
                '.yith-plugin-fw span.ywgc_permit_its_a_present_shop_page_desc',
                '.yith-plugin-fw span.ywgc_gift_this_product_email_button_actions_desc',
                '#ywgc_gift_this_product_email_button_label',
            ],
            'dependencies'   :[],
        },*/
    ];

    var yith_wc_gift_card_admin_panel_var = new yith_wc_gift_card_admin_panel( trigger_brightness_args, trigger_hidden_args );

    yith_wc_gift_card_admin_panel_var.yith_wc_gift_card_set_blank_brightness();

    yith_wc_gift_card_admin_panel_var.yith_wc_gift_card_set_hide_elements();

    // Menu css added
    $( '.yith-plugin-fw span.yith_wc_gift_card_admin_menu_additional_description' ).closest( 'td' ).prev().css( 'padding', '0' );
    $( '.yith-plugin-fw span.yith_wc_gift_card_admin_menu_additional_description' ).closest( 'td' ).css( 'padding', '0 0 12px 10px' );

    $( '#ywgc_gift_this_product_add_to_cart' ).closest( 'td' ).prev().css( 'padding', '20px 20px 5px 20px' );
    $( '#ywgc_gift_this_product_add_to_cart' ).closest( 'td' ).css( 'padding', '20px 10px 5px 10px' );

    $( '#ywgc_gift_this_product_apply_gift_card' ).closest( 'td' ).prev().css( 'padding', '0' );
    $( '#ywgc_gift_this_product_apply_gift_card' ).closest( 'td' ).css( 'padding', '0px 0 15px 10px' );
    $( '.ywgc_gift_this_product_email_button_actions_desc' ).css( 'margin-top', '-10px' );

    //$( '.yith-plugin-fw span.ywgc_gift_this_product_email_button_actions_desc' ).closest( 'td' ).prev().css( 'padding', '0' );
    //$( '.yith-plugin-fw span.ywgc_gift_this_product_email_button_actions_desc' ).closest( 'td' ).css( 'padding', '0px 0 20px 10px' );

    $( '.yith-plugin-fw span.wgc_gift_this_product_choose_page' ).closest( 'td' ).prev().css( 'padding', '20' );
    $( '.yith-plugin-fw span.ywgc_gift_this_product_choose_page' ).closest( 'td' ).css( 'padding', '20px 0 5px 30px' );


    $( 'input:radio[name=ywgc_gift_this_product_email_button_redirect]' ).closest( 'td' ).prev().css( 'padding-bottom', '0' );
    $( 'input:radio[name=ywgc_gift_this_product_email_button_redirect]' ).closest( 'td' ).css( 'padding-bottom', '0px' );

    $( '#select2-ywgc_gift_this_product_redirected_page-container' ).closest( 'td' ).prev().css( 'padding', '0' );
    $( '#select2-ywgc_gift_this_product_redirected_page-container' ).closest( 'td' ).css( 'padding', '0px 0 20px 30px' );


    $( "#ywgc_minimal_car_total" ).on( "change", function () {
        if ( this.value < 0 )
            this.value = 0;
    });

    $( "#ywgc_minimal_amount_gift_card" ).on( "change", function () {
        if ( this.value < 0 )
            this.value = 0;
    });

    $( "#ywgc_usage_expiration" ).on( "change", function () {
        if ( this.value < 0 )
            this.value = 0;
    });

});
