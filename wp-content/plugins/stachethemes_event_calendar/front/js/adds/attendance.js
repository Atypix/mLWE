(function ($) {

    "use strict";

    $(document).on('onEventToggleOpen', function (e, data) {

        if ( data.event.attendance.length <= 0 ) {
            return;
        }

        var $instance = data.$instance;
        var helper = data.helper;
        var glob = data.glob;

        var $event = $instance.$events.find('.stec-layout-event.active');
        var $inner = $event.find('.stec-layout-event-inner-attendance');
        var event = data.event;

        if ( $inner.length <= 0 || !$inner.find('.stec-layout-event-inner-attendance-attendee-template')[0] ) {
            return;
        }

        var template = $inner.find('.stec-layout-event-inner-attendance-attendee-template')[0].outerHTML;
        $inner.find('.stec-layout-event-inner-attendance-attendee-template').remove();

        if ( event.attendance.length > 0 ) {

            $(event.attendance).each(function (i) {

                var th = this;

                $(template).html(function (index, html) {

                    var statusKlass = '';

                    switch ( parseInt(th.status, 10) ) {
                        case 1:
                            statusKlass = 'fa fa-check';
                            break;

                        case 2:
                            statusKlass = 'fa fa-times';
                            break;

                        default:
                            statusKlass = 'fa fa-question';
                    }

                    return html
                            .replace(/stec_replace_name/g, th.name)
                            .replace(/#stec_replace_avatar/g, th.avatar)
                            .replace(/stec_replace_userid/g, th.userid ? th.userid : '')
                            .replace(/stec_replace_status/g, '<li class=""><i class="' + statusKlass + '"></i></li>');

                })
                        .removeClass('stec-layout-event-inner-attendance-attendee-template')
                        .appendTo($inner.find('.stec-layout-event-inner-attendance-attendees'));
            });

        }

        var invited_user = false;

        if ( !isNaN(glob.options.userid) ) {

            // check if user is invited
            $(event.attendance).each(function () {
                if ( this.userid == glob.options.userid ) {
                    invited_user = true;
                    return false; // break
                }
            });
        }

        if ( invited_user !== false ) {

            var status = 0;

            $(event.attendance).each(function () {
                if ( this.userid == glob.options.userid && this.status[data.repeatOffset] ) {
                    status = parseInt(this.status[data.repeatOffset], 10);
                    return false; // break
                }
            });

            var $avatar = $inner.find('.stec-layout-event-inner-attendance-attendee-avatar')
                    .filter('[data-userid="' + glob.options.userid + '"]');

            switch ( status ) {
                case 1:
                    // accepted
                    $inner.find('.stec-layout-event-inner-attendance-invited-buttons-accept').addClass('active');
                    $inner.find('.stec-layout-event-inner-attendance-invited-buttons-decline').removeClass('active');

                    $avatar.find('li i').attr('class', 'fa fa-check');

                    break;

                case 2:
                    // declined
                    $inner.find('.stec-layout-event-inner-attendance-invited-buttons-accept').removeClass('active');
                    $inner.find('.stec-layout-event-inner-attendance-invited-buttons-decline').addClass('active');

                    $avatar.find('li i').attr('class', 'fa fa-times');

                    break;
            }
        } else {
            $inner.find('.stec-layout-event-inner-attendance-invited').remove();
        }

        // Attend / Decline

        function ajaxAttendance(status) {

            // status
            // 0 - no decision
            // 1 - accept 
            // 2 - decline

            glob.ajax = $.ajax({
                dataType: "json",
                type: 'POST',
                url: window.ajaxurl,
                data: {
                    action: 'stec_public_ajax_action',
                    task: 'set_user_event_attendance',
                    event_id: event.id,
                    repeat_offset: data.repeatOffset,
                    status: status
                },
                beforeSend: function () {
                    if ( glob.ajax !== null ) {
                        glob.ajax.abort();
                    }

                    $inner.find('.stec-layout-event-inner-attendance-invited-buttons').hide();
                    $(glob.template.preloader).appendTo($inner.find('.stec-layout-event-inner-attendance-invited'));
                },
                success: function (rtrn) {

                    var status = parseInt(rtrn.status, 10);
                    var id = parseInt(rtrn.id, 10);

                    $event.find('.stec-layout-event-inner-intro-attendance li').removeClass('active');
                    $inner.find('.stec-layout-event-inner-attendance-invited-buttons li').removeClass('active');

                    var $avatar = $inner.find('.stec-layout-event-inner-attendance-attendee-avatar')
                            .filter('[data-userid="' + glob.options.userid + '"]');

                    switch ( parseInt(rtrn.status, 10) ) {
                        case 1 :
                            $event.find('.stec-layout-event-inner-intro-attendance-attend').addClass('active');
                            $inner.find('.stec-layout-event-inner-attendance-invited-buttons-accept').addClass('active');
                            $avatar.find('li i').attr('class', 'fa fa-check');
                            break;
                        case 2 :
                            $event.find('.stec-layout-event-inner-intro-attendance-decline').addClass('active');
                            $inner.find('.stec-layout-event-inner-attendance-invited-buttons-decline').addClass('active');
                            $avatar.find('li i').attr('class', 'fa fa-times');
                            break;
                        default:
                            $avatar.find('li i').attr('class', 'fa fa-question');

                    }

                    $(event.attendance).each(function () {

                        if ( this.userid == glob.options.userid ) {
                            this.status[data.repeatOffset] = status;
                            return false; // break
                        }

                    });
                },
                error: function (xhr, status, thrown) {
                    console.log(xhr + " " + status + " " + thrown);
                },
                complete: function () {
                    glob.ajax = null;

                    $inner.find('.stec-layout-event-inner-attendance-invited-buttons').css('display', 'flex');
                    $inner.find('.stec-layout-event-inner-attendance-invited').find('.stec-preloader').remove();

                }
            });

        }

        $inner.find('.stec-layout-event-inner-attendance-invited-buttons-accept').on(helper.clickHandle(), function (e) {
            e.preventDefault();
            var status = $(this).hasClass('active') ? 0 : 1;
            ajaxAttendance(status);
        });

        $inner.find('.stec-layout-event-inner-attendance-invited-buttons-decline').on(helper.clickHandle(), function (e) {
            e.preventDefault();
            var status = $(this).hasClass('active') ? 0 : 2;
            ajaxAttendance(status);
        });

        // Check if event is in progress
        var nowDate = helper.getCalNow(parseInt(event.timezone_utc_offset, 10) / 3600);
        var startDate = helper.dbDateTimeToDate(helper.dbDateOffset(event.start_date, data.repeatOffset));

        if ( nowDate >= startDate ) {
            $inner.find('.stec-layout-event-inner-intro-attendance').hide();
        }

        if ( nowDate >= startDate ) {
            $inner.find('.stec-layout-event-inner-attendance-invited').hide();
        }

        // Remove tab preloaders
        $inner.find('.stec-layout-event-inner-preload-wrap').children().first().unwrap();
        $inner.find('.stec-layout-event-inner-preload-wrap').remove();
        $inner.find('.stec-preloader').remove();

    });


})(jQuery);