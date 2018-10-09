function showMappingConfirm( field ){
        if(typeof jQuery=="undefined"){
            alert( "Error: AccessPress Social Login require jQuery to be installed on your wordpress in order to work!" );

            return false;
        }

        var el = jQuery( "#bb_profile_mapping_selector_" + field ).val();

        jQuery( ".bb_profile_mapping_confirm_" + field ).hide();

        jQuery( "#bb_profile_mapping_confirm_" + field + "_" + el ).show();
    }

jQuery(document).ready(function($) {
    //for sorting the social networks
    $('.network-settings').sortable({
        containment: "parent",
    });
    
    //for the tabs
    $('.nav-tab').click(function() {
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        var tab_id = 'tab-' + $(this).attr('id');
        $('.apsl-tab-contents').hide();
        $('#' + tab_id).show();
    });
    $('.apsl-label').click(function() {
        $(this).closest('.apsl-settings').find('.apsl_network_settings_wrapper').toggle('slow', function() {
            if ($(this).closest('.apsl-settings').find('.apsl_network_settings_wrapper').is(':visible')) {
                $(this).closest('.apsl-settings').find('.apsl_show_hide i').removeClass('fa-caret-down');
                $(this).closest('.apsl-settings').find('.apsl_show_hide i').addClass('fa-caret-up');
            } else {
                $(this).closest('.apsl-settings').find('.apsl_show_hide i').removeClass('fa-caret-up');
                $(this).closest('.apsl-settings').find('.apsl_show_hide i').addClass('fa-caret-down');
            }
        });
    });
    // for hide show options based on logout redirect options 
    $('.apsl_custom_logout_redirect_options').click(function() {
        if ($(this).val() === 'custom_page') {
            $('.apsl-custom-logout-redirect-link').show('slow');
        } else {
            $('.apsl-custom-logout-redirect-link').hide('show');
        }
    });
    // for hide show options based on logout redirect options
    $('.apsl_custom_login_redirect_options').click(function() {
        if ($(this).val() === 'custom_page') {
            $('.apsl-custom-login-redirect-link').show('slow');
        } else {
            $('.apsl-custom-login-redirect-link').hide('show');
        }
    });
    // for hide show options based on logout redirect options
    $('.apsl_send_email_notification_options').click(function() {
        if ($(this).val() === 'yes') {
            $('.apsl-email-format-settings').show('slow');
        } else {
            $('.apsl-email-format-settings').hide('show');
        }
    });

    // for hide show options based on logout redirect options
    $('.apsl_profile_mapping_options').click(function() {
        if ($(this).val() === 'yes') {
            $('.apsl-profile-mapping-outer').show('slow');
        } else {
            $('.apsl-profile-mapping-outer').hide('show');
        }
    });
});