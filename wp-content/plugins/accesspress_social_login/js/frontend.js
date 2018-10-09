function apsl_open_in_popup_window(event, url){
    event.preventDefault();
    window.open(url, 'fdadas', 'toolbars=0,width=640,height=320,left=300,top=300,scrollbars=1,resizable=1');
    // parent.close();
}

jQuery(document).ready(function($){

	$('.show-apsl-container').on('click', function(e){
        e.preventDefault();
        $('.apsl-container').slideToggle();
    });

    $('.apsl-link-account-button').click(function(){
        $('.apsl-buttons-wrapper').hide();
        $('.apsl-login-form').show();
        $('.apsl-registration-wrapper').addClass('apsl-login-registration-form');
    });

    $('.apsl-create-account-button').click(function(){
        $('.apsl-buttons-wrapper').hide();
        $('.apsl-registration-form').show();
        $('.apsl-registration-wrapper').addClass('apsl-login-registration-form');
    });

    $('.apsl-back-button').click(function(){
        $('.apsl-buttons-wrapper').show();
        $('.apsl-login-form').hide();
        $('.apsl-registration-form').hide();
        $('.apsl-registration-wrapper').removeClass('apsl-login-registration-form');
    });
});