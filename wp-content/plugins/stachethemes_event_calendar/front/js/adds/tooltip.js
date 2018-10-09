(function ($) {

    "use strict";

    $.stecExtend(function (master) {

        var helper = master.helper;
        var glob = master.glob;
        var instance = master.instance;
        var $instance = master.$instance;
        var calData = master.calData;

        var tooltip = {

            init: function () {

                if ( parseInt(glob.options.general_settings.tooltip_display, 10) !== 1 ) {
                    return;
                }

                if ( helper.isMobile() === true ) {
                    // don't bind on mobile
                    return;
                }

                this.bindControls();
            },

            bindControls: function () {

                var parent = this;

                var event_class = [];
                event_class.push(instance + ' .stec-layout-month-daycell-event');
                event_class.push(instance + ' .stec-layout-week-daycell-event');

                if ( master.glob.options.agenda_tooltip == '1' ) {
                    event_class.push(instance + ' .stec-layout-agenda-daycell-event');
                }

                event_class = event_class.join(',');

                $(document).on('mouseenter', event_class, function (e) {

                    var th = this;

                    $('#tooltip-' + glob.options.id).remove();

                    if ( helper.animate !== false ) {
                        helper.animate.tooltip.clear();
                    }

                    $(glob.template.tooltip).attr('id', 'tooltip-' + glob.options.id).appendTo('body');

                    var event = calData.getEventById($(th).attr('data-id'));

                    var repeat_offset = parseInt($(th).attr('data-repeat-time-offset'), 10);

                    event.repeat_offset = repeat_offset;
                    
                    parent.fillHTML(event);

                    parent.position(th);

                    if ( helper.animate !== false ) {

                        helper.animate.tooltip.show($('#tooltip-' + glob.options.id));

                    } else {

                        $('#tooltip-' + glob.options.id).fadeTo(0, 1);

                    }


                });

                $(document).on('mouseleave', instance + event_class, function (e) {

                    if ( helper.animate !== false ) {
                        helper.animate.tooltip.hide($('#tooltip-' + glob.options.id), function () {

                            $('#tooltip-' + glob.options.id).remove();

                        });

                    } else {

                        $('#tooltip-' + glob.options.id).fadeTo(0, 0);

                    }

                    parent.clock.clear();
                });
            },

            position: function (th) {

                var klass = '';

                var $tooltip = $('#tooltip-' + glob.options.id);

                $tooltip.css({
                    left: function () {

                        var x = 0;

                        if ( $(th).hasClass('stec-layout-agenda-daycell-event') ) {
                            x = $(th).width() + $(th).offset().left + 15;
                        } else if ( $(th).parents('td').first().index() > 3 ) {
                            x = $(th).offset().left - $tooltip.width() - 5;
                            klass += ' stec-tooltip-pos-left';
                        } else {
                            x = $(th).width() + $(th).offset().left + 5;
                        }

                        return x;
                    },
                    top: function () {

                        var y = 0;

                        if ( $(th).hasClass('stec-layout-agenda-daycell-event') ) {

                            y = $(th).offset().top - 23;

                        } else if ( $(th).parents('tr').first().index() > 3 ) {

                            klass += ' stec-tooltip-pos-top';

                            y = $instance.hasClass('stec-media-small') ? $(th).offset().top - $tooltip.height() + 29 : $(th).offset().top - $tooltip.height() + 40;

                        } else {

                            y = $instance.hasClass('stec-media-small') ? $(th).offset().top - 27 : $(th).offset().top - 15;

                        }


                        return y;
                    }
                });

                $tooltip.addClass(klass);
            },

            fillHTML: function (event) {

                var $tooltip = $('#tooltip-' + glob.options.id);

                var iconHtml = '<div class="stec-tooltip-icon" style="background:' + event.color + ' "><i class="' + event.icon + '"></i></div>';

                // original date + the repeat offset
                var startDate = helper.dbDateTimeToDate(helper.dbDateOffset(event.start_date, event.repeat_offset));

                // original date + the repeat offset
                var endDate = helper.dbDateTimeToDate(helper.dbDateOffset(event.end_date, event.repeat_offset));

                var date = helper.beautifyTimespan(startDate, endDate, event.all_day);

                var imageHtml = '';

                if ( event.images_meta && event.images_meta.length > 0 ) {
                    imageHtml = '<div style="background-image: url(' + event.images_meta[0].src + ');"  ></div>';
                }

                $tooltip.html(function (index, html) {

                    return html
                            .replace(/stec_replace_image/g, imageHtml)
                            .replace(/stec_replace_summary/g, event.title)
                            .replace(/stec_replace_desc_short/g, event.description_short)
                            .replace(/stec_replace_icon/g, iconHtml)
                            .replace(/stec_replace_location/g, event.location)
                            .replace(/stec_replace_timespan/g, date);

                });

                if ( event.location == '' ) {
                    $tooltip.find('.stec-tooltip-location').hide();
                }

                if ( imageHtml == '' ) {
                    $tooltip.find('.stec-tooltip-image').hide();
                    $tooltip.find('.stec-tooltip-icon').css({
                        top: 0,
                        position: 'static',
                        marginTop: 20
                    });
                }

                var calNow = helper.getCalNow(event.timezone_utc_offset / 3600);

                if ( calNow > endDate ) {
                    $tooltip.find('.stec-tooltip-expired').css('display', 'inline-block');
                }

                if ( calNow > startDate && endDate > calNow ) {
                    $tooltip.find('.stec-tooltip-progress').css('display', 'inline-block');
                }

                // Check counter
                if ( event.counter != 0 && startDate > calNow ) {
                    $tooltip.find('.stec-tooltip-counter').css('display', 'inline-block');
                    this.clock.init(startDate, calNow);
                }

            },

            clock: {
                days: 0,
                hours: 0,
                minutes: 0,
                seconds: 0,
                daysLabel: window.stecLang.DaysAbbr,
                hoursLabel: window.stecLang.HoursAbbr,
                minutesLabel: window.stecLang.MinutesAbbr,
                secondsLabel: window.stecLang.SecondsAbbr,
                interval: '',
                init: function (date, now) {

                    this.clear();

                    var nowDate = now;
                    var startDate = date;

                    var timeLeft = Math.floor((startDate.getTime() - nowDate.getTime()) / 1000);
                    this.days = Math.floor(timeLeft / 86400);
                    this.hours = Math.floor(timeLeft % 86400 / 3600);
                    this.minutes = Math.floor(timeLeft % 86400 % 3600 / 60);
                    this.seconds = Math.floor(timeLeft % 86400 % 3600 % 60);

                    if ( timeLeft < 0 ) {
                        this.complete();
                        return;
                    }

                    this.count();
                },
                setTimer: function () {

                    var $tooltip = $('#tooltip-' + glob.options.id);
                    var countText = this.days + this.daysLabel + ' ' + this.hours + this.hoursLabel + ' ' + this.minutes + this.minutesLabel + ' ' + this.seconds + this.secondsLabel;
                    $tooltip.find('.stec-tooltip-counter span').text(countText);

                },
                count: function () {

                    var parent = this;

                    parent.setTimer();

                    parent.interval = setInterval(function () {

                        parent.seconds--;

                        if ( parent.seconds < 0 ) {
                            parent.seconds = 59;
                            parent.minutes--;
                            if ( parent.minutes < 0 ) {
                                parent.minutes = 59;
                                parent.hours--;
                                if ( parent.hours < 0 ) {
                                    parent.hours = 23;
                                    if ( parent.days > 0 ) {
                                        parent.days--;
                                    }
                                }
                            }
                        }

                        parent.setTimer();

                        if ( parent.days == 0 && parent.hours == 0 && parent.minutes == 0 && parent.seconds == 0 ) {
                            parent.clear();
                            parent.complete();
                        }

                    }, 1000);
                },

                complete: function () {
                    this.clear();
                },

                clear: function () {

                    clearInterval(this.interval);
                }

            }
        };

        tooltip.init();

    });


})(jQuery);