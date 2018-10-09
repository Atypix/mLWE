(function ($) {

    "use strict";

    $(document).on('onEventToggleOpen', function (e, data) {

        if ( data.event.schedule.length <= 0 ) {
            return;
        }

        var $instance = data.$instance;
        var helper = data.helper;
        var glob = data.glob;
        var event = data.event;

        var $event = $instance.$events.find('.stec-layout-event.active');
        var $inner = $event.find('.stec-layout-event-inner-schedule');

        if ( $inner.length <= 0 || !$inner.find('.stec-layout-event-inner-schedule-tab-template')[0] ) {
            return;
        }

        var template = $inner.find('.stec-layout-event-inner-schedule-tab-template')[0].outerHTML;
        $inner.find('.stec-layout-event-inner-schedule-tab-template').remove();

        $(data.event.schedule).each(function () {

            var th = this;

            var customKlass = '';

            if ( th.description == '' ) {
                customKlass += ' stec-layout-event-inner-schedule-tab-no-desc ';
            }

            if ( th.icon == "" || th.icon == "fa" ) {
                customKlass += ' stec-layout-event-inner-schedule-tab-no-icon ';
            }

            $(template)
                    .addClass(customKlass)
                    .html(function (index, html) {

                        var start = helper.dbDateTimeToDate(helper.dbDateOffset(th.start_date, data.repeatOffset));

                        if ( data.event.start_date_timestamp_tz ) {
                            // event has been converted to localtime
                            // convert shedule as well
                            start = new moment(th.start_date)
                                    .add(data.repeatOffset, 'seconds')
                                    .utcOffset(data.event.timezone_utc_offset / 60, true).utcOffset(window.moment().utcOffset());

                        }

                        return html
                                .replace(/stec_replace_date/g, helper.beautifyScheduleTimespan(start))
                                .replace(/stec_replace_time/g, '')
                                .replace(/stec_replace_icon/g, th.icon)
                                .replace(/stec_replace_color/g, 'style="color:' + th.icon_color + '"')
                                .replace(/stec_replace_title/g, th.title)
                                .replace(/stec_replace_desc/g, th.details);

                    })
                    .removeClass('stec-layout-event-inner-schedule-tab-template')
                    .appendTo($inner);

        });

        $inner.find('.stec-layout-event-inner-schedule-isempty').remove();
        $inner.find('.stec-layout-event-inner-schedule-tab').first().addClass('open');


        $inner.find('.stec-layout-event-inner-schedule-tab-preview')
                .off(helper.clickHandle('open'))
                .on(helper.clickHandle('open'), function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).parents(".stec-layout-event-inner-schedule-tab").not('.stec-layout-event-inner-schedule-tab-no-desc').toggleClass("open");
                });

        // Remove tab preloaders
        $inner.find('.stec-layout-event-inner-preload-wrap').children().first().unwrap();
        $inner.find('.stec-layout-event-inner-preload-wrap').remove();
        $inner.find('.stec-preloader').remove();

    });


})(jQuery);