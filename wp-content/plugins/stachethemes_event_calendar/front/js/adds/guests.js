(function ($) {

    "use strict";


    $(document).on('onEventToggleOpen', function (e, data) {

        if ( data.event.guests.length <= 0 ) {
            return;
        }

        var $instance = data.$instance;

        var $event = $instance.$events.find('.stec-layout-event.active');
        var $inner = $event.find('.stec-layout-event-inner-guests');
        var event = data.event;

        if ( $inner.length <= 0 || !$inner.find('.stec-layout-event-inner-guests-guest-template')[0] ) {
            return;
        }

        var template = $inner.find('.stec-layout-event-inner-guests-guest-template')[0].outerHTML;
        $inner.find('.stec-layout-event-inner-guests-guest-template').remove();

        if ( event.guests.length > 0 ) {

            $(event.guests).each(function (i) {

                var th = this;

                $(template).html(function (index, html) {

                    var lis = [];

                    $(th.links).each(function (pos) {
                        lis[pos] = '<li class="stec-layout-event-inner-guests-guest-left-avatar-icon-position-' + pos + '"><a href="' + this.link + '"><i class="' + this.ico + '"></i></a></li>';
                    });

                    var avatar;

                    if ( !th.photo_full ) {
                        avatar = '<div class="stec-layout-event-inner-guests-guest-left-avatar-default"></div>';
                    } else {
                        avatar = '<img src="' + th.photo_full + '" alt="stec_replace_name" >';
                    }

                    return html
                            .replace(/stec_replace_ico_position/g, i)
                            .replace(/stec_replace_avatar/g, avatar)
                            .replace(/stec_replace_social/g, lis.join(''))
                            .replace(/stec_replace_name/g, th.name)
                            .replace(/stec_replace_about/g, th.about);

                })
                        .removeClass('stec-layout-event-inner-guests-guest-template')
                        .appendTo($inner);

            });

        }

        // Remove tab preloaders
        $inner.find('.stec-layout-event-inner-preload-wrap').children().first().unwrap();
        $inner.find('.stec-layout-event-inner-preload-wrap').remove();
        $inner.find('.stec-preloader').remove();

    });


})(jQuery);