/*global jQuery, document, redux*/

jQuery(function($) { "use strict";
    $(document).on('confirmation', '.popup-license', function () {
        $('.redux-group-tab-link-li.redux-tlm-class a').click();
    });

    function note_message(content){
        $('#info-verification_status p').html(content);
        $('#info-verification_status').show('fast');
    }

    $('.redux-container').on('click', '.validation_activate_buttons', function(e) { 
        e.preventDefault();
        $('#info-verification_status').hide('fast');
        var purchase_code = $('#purchase_code_verification').val();
        var verify = $(this).data('verify');
        if (purchase_code == '') {
            var response = 'Please, enter purchase code.';
            note_message(response);
        }else{
            $.ajax({
                url : ajaxurl,
                type : 'post',
                data : {
                    action : 'current_theme_verification',
                    purchase_code : purchase_code,
                    verify : verify
                },
                success : function( response ) {                
                    note_message(response);
                },
                error: function(errorThrown){
                    console.log(errorThrown);
                }
            });
        }         
    });     
});