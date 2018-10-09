(function ($) {

    "use strict";

    /**
     * prefixes added to prevent conflicts
     * from jQuery Easing v1.3 - http://gsgd.co.uk/sandbox/jquery/easing/
     */
    $.extend($.easing, {
        stecOutExpo: function (x, t, b, c, d) {
            return (t == d) ? b + c : c * (-Math.pow(2, -10 * t / d) + 1) + b;
        },
        stecExpo: function (x, t, b, c, d) {
            if ( t == 0 ) {
                return b;
            }

            if ( t == d ) {
                return b + c;
            }

            t = t / (d / 2);

            if ( t < 1 ) {
                return c / 2 * Math.pow(2, 10 * (t - 1)) + b;
            }

            return c / 2 * (-Math.pow(2, -10 * --t) + 2) + b;
        }
    });

    function stecSingle() {

        var stecLang = window.stecLang;

        var glob = {

            template: {
                preloader: '',
                share: ''
            },

            event: {},

            options: {

                siteurl: '',

                monthLabelsShort: [
                    stecLang.jan,
                    stecLang.feb,
                    stecLang.mar,
                    stecLang.apr,
                    stecLang.may,
                    stecLang.jun,
                    stecLang.jul,
                    stecLang.aug,
                    stecLang.sep,
                    stecLang.oct,
                    stecLang.nov,
                    stecLang.dec
                ],

                monthLabels: [
                    stecLang.january,
                    stecLang.february,
                    stecLang.march,
                    stecLang.april,
                    stecLang.may,
                    stecLang.june,
                    stecLang.july,
                    stecLang.august,
                    stecLang.september,
                    stecLang.october,
                    stecLang.november,
                    stecLang.december
                ],

                dayLabels: [
                    stecLang.sunday,
                    stecLang.monday,
                    stecLang.tuesday,
                    stecLang.wednesday,
                    stecLang.thursday,
                    stecLang.friday,
                    stecLang.saturday
                ],

                dayLabelsShort: [
                    stecLang.sun,
                    stecLang.mon,
                    stecLang.tue,
                    stecLang.wed,
                    stecLang.thu,
                    stecLang.fri,
                    stecLang.sat
                ]
            }
        };

        var helper = {

            // converts single layout date to localtime
            convertToLT: function () {

                glob.options.date_label_gmtutc = 0;
                glob.event.start_date_timestamp_tz = window.moment(glob.event.start_date).unix();
                glob.event.end_date_timestamp_tz = window.moment(glob.event.end_date).unix();
                // Add localtime offset
                glob.event.start_date = window.moment(glob.event.start_date)
                        .add(glob.event.timezone_utc_offset, 'seconds')
                        .format('YYYY-MM-DD HH:mm:ss');
                glob.event.end_date = window.moment(glob.event.end_date)
                        .add(glob.event.timezone_utc_offset, 'seconds')
                        .format('YYYY-MM-DD HH:mm:ss');
                $('.stec-layout-single .stec-get-the-timespan').text(helper.beautifyTimespan(glob.event.start_date, glob.event.end_date, glob.event.all_day));
                $('.stec-layout-single span[data-schedule-timestamp]').each(function () {
                    var $dateField = $(this);
                    var stamp = $dateField.attr('data-schedule-timestamp');
                    var start = window.moment.unix(stamp)
                            .utcOffset(glob.event.timezone_utc_offset / 60)
                            .format('YYYY-MM-DD HH:mm:ss');
                    $dateField.text(helper.beautifyScheduleTimespan(start));
                });
            },

            beautifyScheduleTimespan: function (start) {

                var d1 = window.moment(start);

                var format = '';
                var timeFormat = 'HH:mm';

                if ( glob.options.time_format == '12' ) {
                    timeFormat = 'hh:mma';
                }

                switch ( glob.options.date_format ) {
                    case 'dd.mm.yy' :
                        format = 'DD.MMM';
                        break;
                    case 'dd-mm-yy' :
                        format = 'DD MMM ';
                        break;
                    case 'mm-dd-yy' :
                        format = 'MMM DD ';
                        break;
                    case 'yy-mm-dd' :
                        format = 'MMM DD';
                        break;
                }

                format += ' ' + timeFormat;

                return d1.format(format);

            },

            /**
             * Returns user friendly timespan
             * Accepted params for start and end are String (DbDate) or Date object
             * @param {mixed} start
             * @param {mixed} end 
             * @returns {String}
             * @since 2.0.0 uses moment
             */
            beautifyTimespan: function (start, end, all_day) {

                all_day = parseInt(all_day, 10);

                var d1 = window.moment(start);
                var d2 = window.moment(end);

                var format = '';
                var timeFormat = 'HH:mm';

                if ( glob.options.time_format == '12' ) {
                    timeFormat = 'hh:mma';
                }

                switch ( glob.options.date_format ) {
                    case 'dd.mm.yy' :
                        format = 'DD.MMM.YYYY';
                        break;
                    case 'dd-mm-yy' :
                        format = 'DD MMM YYYY';
                        break;
                    case 'mm-dd-yy' :
                        format = 'MMM DD YYYY';
                        break;
                    case 'yy-mm-dd' :
                        format = 'YYYY MMM DD';
                        break;
                }

                // Show time only if not all_day event
                if ( all_day != 1 ) {
                    format += ' ' + timeFormat;
                }

                var timespanLabel = '';

                // Same Day
                if ( d1.isSame(d2, 'day') ) {

                    timespanLabel = d1.format(format);

                    if ( all_day != 1 ) {
                        timespanLabel += " - " + d2.format(timeFormat);
                    }

                } else

                // Same Month & Year
                if ( d1.isSame(d2, 'month') ) {

                    var formatEnd = format.replace('YYYY', '');

                    timespanLabel = d1.format(format);
                    timespanLabel += " - " + d2.format(formatEnd);

                }

                // Default
                else {

                    timespanLabel = d1.format(format);
                    timespanLabel += " - " + d2.format(format);
                }

                return timespanLabel;

            },

            // @todo change with date.UTC ?
            // used for dateToRFC
            treatAsUTC: function (date) {

                var result = date instanceof Date ? date : new Date(date);
                var adjustedMinutes = result.getMinutes() + result.getTimezoneOffset();
                result.setMinutes(adjustedMinutes);
                return result;
            },

            capitalizeFirstLetter: function (string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            },

            dateToFormat: function (format, date) {

                var hasTime = false;
                var ampm = 'am';

                if ( format === false ) {

                    switch ( glob.options.date_format ) {
                        case 'dd-mm-yy' :
                            format = 'd m y';
                            break;
                        case 'mm-dd-yy' :
                            format = 'm d y';
                            break;
                        case 'yy-mm-dd' :
                            format = 'y m d';
                            break;
                    }

                }

                format = format.split('');

                var string = '';

                $(format).each(function () {
                    switch ( this ) {
                        case 'y' :
                            string += date.getFullYear();
                            break;
                        case 'm' :
                            string += helper.capitalizeFirstLetter(glob.options.monthLabels[date.getMonth()]);
                            break;
                        case 'M' :
                            string += helper.capitalizeFirstLetter(glob.options.monthLabelsShort[date.getMonth()]);
                            break;
                        case 'd' :
                            string += date.getDate();
                            break;
                        case 'h' :
                            hasTime = true;

                            var h = date.getHours();

                            if ( glob.options.time_format == '12' ) {

                                if ( h == 12 ) {
                                    ampm = 'pm';
                                }

                                if ( h > 12 ) {
                                    h = h - 12;
                                    ampm = 'pm';
                                }

                                if ( h == 0 ) {
                                    h = 12;
                                    ampm = 'am';
                                }


                                string += h < 10 ? '0' + h : h;
                            } else {
                                string += h < 10 ? '0' + h : h;
                            }

                            break;
                        case 'i' :
                            var m = date.getMinutes();

                            m = m < 10 ? '0' + m : m;

                            string += m;
                            break;
                        case 's' :
                            var s = date.getSeconds();
                            string += s < 10 ? '0' + s : s;
                            break;

                        default:
                            string += this;
                    }
                });

                if ( hasTime && glob.options.time_format == '12' ) {

                    string += ' ' + ampm;
                }

                return string;
            },

            /**
             * returns now Date for given calendar offset
             * @param {int} hoursOffset
             * @returns {Date} date object
             */
            getCalNow: function (hoursOffset) {

                var date = new Date();
                date.setMinutes(date.getMinutes() + date.getTimezoneOffset()); // UTC now

                date.setHours(date.getHours() + hoursOffset); // UTC now + hours offset

                return date;

            },

            imgLoaded: function ($img, callback, step) {

                if ( typeof $.fn.imagesLoaded !== "undefined" ) {
                    // imagesLoaded script is loaded
                    $img.imagesLoaded(function () {
                        if ( typeof callback === "function" ) {
                            callback.call($img);
                        }
                    });
                    return;
                }

                var total = $img.length;
                var loaded = 0;

                if ( total <= 0 ) {
                    if ( typeof callback === "function" ) {
                        callback.call($img);
                    }
                }

                $img.each(function () {
                    var image = new Image;
                    image.onload = function () {

                        if ( typeof step === "function" ) {
                            step();
                        }

                        loaded++;
                        if ( loaded >= total ) {
                            if ( typeof callback === "function" ) {
                                callback.call($img);
                            }
                        }
                    };

                    image.onerror = function () {
                        console.warn("Could not load image");
                    };

                    image.src = $(this).attr("src");
                });
            },

            isEmail: function (email) {
                var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                return regex.test(email);
            },

            isMobile: function () {
                return (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) ? true : false;
            },

            clickHandle: function (sub) {
                var tap = sub ? 'vlick.' + sub : 'vclick';
                var click = sub ? 'click.' + sub : 'click';
                return this.isMobile() ? tap : click;
            },

            onResizeEnd: function (callback, time, keyslug) {

                var id;

                time = time ? time : 10;

                var handle = keyslug ? 'resize.' + keyslug : 'resize';

                $(window).on(handle, function () {

                    clearTimeout(id);
                    id = setTimeout(function () {
                        if ( typeof callback === "function" ) {
                            callback.call();
                        }
                    }, time);
                });
            },

            /**
             * expects months range 1-12
             * converts to 0-11
             * @param {str} string yy-mm-dd h:i:s
             */
            dbDateTimeToDate: function (string) {

                var date = string.replace(/(\s|:|-)/g, ",").split(',');

                for ( var i = 0; i < date.length; i++ ) {
                    date[i] = parseInt(date[i], 10);

                    if ( i == 1 ) {
                        date[i] = date[i] - 1;
                    }
                }

                var d = new Date(date[0], date[1], date[2], date[3], date[4]);
                return d;
            },

            /**
             * @param {type} string dbDate string
             * @param {type} offset unixstamp
             * @returns {String} dbDate string
             */
            dbDateOffset: function (string, offset) {
                var date = this.dbDateTimeToDate(string);
                date.setTime(date.getTime() + offset * 1000);
                return this.dateToDbDateTime(date);
            },

            /*
             * Converts js date to db date
             * @param {obj} date
             * @returns {String}
             */
            dateToDbDateTime: function (date) {

                var string = '';

                var y = date.getFullYear();
                var m = date.getMonth() + 1;
                var d = date.getDate();
                var h = date.getHours();
                var i = date.getMinutes();
                var s = date.getSeconds();

                string += y;
                string += '-';
                string += m < 10 ? '0' + m : m;
                string += '-';
                string += d < 10 ? '0' + d : d;
                string += ' ';
                string += h < 10 ? '0' + h : h;
                string += ':';
                string += i < 10 ? '0' + i : i;
                string += ':';
                string += s < 10 ? '0' + s : s;

                return string;
            }
        };

        this.init = function () {

            $.extend(glob.options, window.stecSingleOptions);

            glob.event = window.stecSingleEvent;

            if ( !glob.event ) {
                return;
            }

            glob.template.preloader = $(".stec-layout-single-preloader-template").html();
            glob.options.siteurl = glob.options.site_url;

            glob.template.share = $(".stec-layout-single").children(".stec-share-template").html();

            if ( glob.options.date_in_user_local_time == 1 ) {
                helper.convertToLT();
            }

            media.init();
            attachments.init();
            reminder.init();
            slider.init();
            clock.init();
            location.init();

            tabs.init();
            schedule.init();
            woocommerce.init();
            forecast.init();
            attendance.init();
            comments.init();

            bindControls();

        };

        var media = {
            init: function () {
                this.bindControls();
                this.mediaTrigger();
            },
            bindControls: function () {

                var parent = this;

                $(window).on('resize', function () {
                    parent.mediaTrigger();
                });

                $('.stec-layout-single').css({
                    visibility: 'visible'
                });
            },
            mediaTrigger: function () {

                if ( $('.stec-layout-single').width() <= 600 ) {
                    $('.stec-layout-single').removeClass("stec-layout-single-media-med");
                    $('.stec-layout-single').addClass("stec-layout-single-media-small");
                } else if ( $('.stec-layout-single').width() <= 870 ) {
                    $('.stec-layout-single').removeClass("stec-layout-single-media-small");
                    $('.stec-layout-single').addClass("stec-layout-single-media-med");
                } else {
                    $('.stec-layout-single').removeClass("stec-layout-single-media-med stec-layout-single-media-small");
                }
            }
        };

        var attachments = {
            init: function () {
                this.bindControls();
            },
            bindControls: function () {

                $('.stec-layout-single-attachments-toggle').on(helper.clickHandle(), function () {
                    $(this).toggleClass('active');
                    $('.stec-layout-single-attachments-list').toggleClass('active');
                });

            }
        };

        var reminder = {

            blockAction: false,

            ajax: null,

            init: function () {

                this.bindControls();

            },

            bindControls: function () {

                $('.stec-layout-single-preview-right-reminder').on(helper.clickHandle(), function () {
                    $(this).toggleClass('active');
                    $('.stec-layout-single-preview-left-reminder-toggle').toggleClass('active');
                    $('.stec-layout-single-reminder-form').toggleClass('active');

                });

                $('.stec-layout-single-preview-left-reminder-toggle').on(helper.clickHandle(), function () {
                    $(this).toggleClass('active');
                    $('.stec-layout-single-preview-right-reminder').toggleClass('active');
                    $('.stec-layout-single-reminder-form').toggleClass('active');
                });

                $(document).on(helper.clickHandle(), ".stec-layout-single .stec-layout-single-preview-reminder-units-selector li", function (e) {
                    e.preventDefault();

                    var value = $(this).attr('data-value');
                    var text = $(this).text();

                    $(this).parents('.stec-layout-single-preview-reminder-units-selector')
                            .find('p')
                            .attr('data-value', value)
                            .text(text);
                });

                $(document).on(helper.clickHandle(), ".stec-layout-single .stec-layout-single-preview-remind-button", function (e) {

                    e.preventDefault();

                    var $form = $(this).parents('ul:first');

                    var eventId = glob.event.id;
                    var start_date = glob.event.start_date;
                    var repeat_offset = glob.options.repeat_offset;

                    var email = $form.find('input[name="email"]').val();
                    var number = $form.find('input[name="number"]').val();
                    var units = $form.find('p[data-value]').attr('data-value');

                    if ( helper.isEmail(email) && number != '' ) {
                        reminder.remindEvent(eventId, start_date, repeat_offset, email, number, units);
                    }

                });
            },

            remindEvent: function (eventId, start_date, repeat_offset, email, number, units) {

                if ( this.blockAction === true ) {
                    return;
                }

                var parent = this;

                var remindDate = helper.dbDateTimeToDate(helper.dbDateOffset(start_date, repeat_offset));

                if ( isNaN(number) ) {
                    return;
                }

                switch ( units ) {

                    case 'hours' :

                        remindDate.setHours(remindDate.getHours() - number);

                        break;

                    case 'days' :

                        remindDate.setDate(remindDate.getDate() - number);

                        break;

                    case 'weeks' :

                        remindDate.setDate(remindDate.getDate() - number * 7);

                        break;
                }

                remindDate = helper.dateToDbDateTime(remindDate);

                reminder.ajax = $.ajax({

                    dataType: "json",
                    type: 'POST',
                    url: window.ajaxurl,
                    data: {
                        action: 'stec_public_ajax_action',
                        task: 'add_reminder',
                        event_id: eventId,
                        repeat_offset: repeat_offset,
                        email: email,
                        date: remindDate
                    },

                    beforeSend: function () {
                        if ( reminder.ajax !== null ) {
                            reminder.ajax.abort();
                        }

                        $(glob.template.preloader)
                                .appendTo($('.stec-layout-single-reminder-status'));

                        $('.stec-layout-single-reminder-form li').hide();

                        parent.blockAction = true;

                    },

                    success: function (data) {

                        $('.stec-layout-single-reminder-status').find('.stec-layout-single-preloader').remove();

                        if ( data && data.error != 1 ) {

                            $('.stec-layout-single-reminder-status p')
                                    .text(stecLang.reminderset)
                                    .show();

                        } else {

                            $('.stec-layout-single-reminder-status p')
                                    .text(stecLang.error)
                                    .show();
                        }

                    },

                    error: function (xhr, status, thrown) {

                        $('.stec-layout-single-reminder-status').find('.stec-layout-single-preloader').remove();

                        $('.stec-layout-single-reminder-status p')
                                .text(stecLang.error)
                                .show();

                        console.log(xhr + " " + status + " " + thrown);
                    },

                    complete: function () {

                        reminder.ajax = null;

                        setTimeout(function () {

                            $('.stec-layout-single-reminder-status p')
                                    .text('-')
                                    .hide();

                            $('.stec-layout-single-reminder-form li').show();

                            parent.blockAction = false;
                        }, 3000);
                    }
                });
            }
        };

        var slider = {

            cslide: 0,
            offset: 0,
            total: 0,
            blockAction: false,
            visCount: 0,
            visCountSmall: 3,
            visCountBig: 4,

            init: function () {

                var parent = this;

                var total_images = $('.stec-layout-single-media-controls-list li').length;

                if ( total_images == 1 ) {

                    $('.stec-layout-single').find('.stec-layout-single-media-controls').remove();

                } else {

                    if ( total_images < this.visCountBig ) {
                        this.visCountBig = total_images;
                    }

                    if ( total_images < this.visCountSmall ) {
                        this.visCountSmall = total_images;
                    }
                }

                this.html();

                helper.imgLoaded($('.stec-layout-single').find('.stec-layout-single-media-content img'), function () {

                    setTimeout(function () {
                        parent.controlsDimensions();
                        parent.showImage();

                        $('.stec-layout-single').find('.stec-layout-single-media').fadeTo(1000, 1);
                    }, 100);


                });

                this.bindControls();

            },

            bindControls: function () {

                var parent = this;

                helper.onResizeEnd(function () {
                    parent.controlsDimensions();
                });

                $('.stec-layout-single').find('.stec-layout-single-media-controls-next').on(helper.clickHandle(), function (e) {
                    e.preventDefault();
                    parent.slideNext();
                });

                $('.stec-layout-single').find('.stec-layout-single-media-controls-prev').on(helper.clickHandle(), function (e) {
                    e.preventDefault();
                    parent.slidePrev();
                });

                $('.stec-layout-single').find('.stec-layout-single-media-controls li').on(helper.clickHandle(), function (e) {

                    e.preventDefault();

                    if ( parent.cslide == $(this).index() ) {
                        return;
                    }

                    parent.cslide = $(this).index();

                    parent.showImage();

                });

            },

            html: function () {
            },

            controlsDimensions: function () {

                var parent = this;

                if ( !$('.stec-layout-single').is(':visible') ) {
                    return;
                }

                if ( $('.stec-layout-single').hasClass('stec-layout-single-media-small') ) {
                    parent.visCount = parent.visCountSmall;
                } else {
                    parent.visCount = parent.visCountBig;
                }

                if ( $('.stec-layout-single').find('.stec-layout-single-media-controls-list li').length == parent.visCount ) {
                    $('.stec-layout-single').find('.stec-layout-single-media-controls').addClass('no-side-controls');
                } else {
                    $('.stec-layout-single').find('.stec-layout-single-media-controls').removeClass('no-side-controls');
                }

                var maxWidth = $('.stec-layout-single').find('.stec-layout-single-media-controls-list-wrap').width();
                var $li = $('.stec-layout-single').find('.stec-layout-single-media-controls-list li');

                $('.stec-layout-single').find('.stec-layout-single-media-content').height($('.stec-layout-single').find('.stec-layout-single-media img').first().height());

                // ~'calc( (100% - 2*10px) / 3 )';
                var liWidth = (maxWidth - ((this.visCount - 1) * 10)) / this.visCount;

                var listWidth = ($li.length * liWidth) + ($li.length * 10) - 10;

                $('.stec-layout-single').find('.stec-layout-single-media-controls-list').width(listWidth);
                $li.width(liWidth);


                this.offset = 0;

                var left = -1 * ($li.first().width() * this.offset + this.offset * 10);

                $('.stec-layout-single').find('.stec-layout-single-media-controls-list').stop().css({
                    left: left
                });


            },

            showImage: function () {

                $('.stec-layout-single').find('.stec-layout-single-media-controls-list .active-thumb').removeClass('active-thumb');
                $('.stec-layout-single').find('.stec-layout-single-media-controls-list li').eq(this.cslide).addClass('active-thumb');

                var $old = $('.stec-layout-single').find('.stec-layout-single-media-content .active-image');
                var $new = $('.stec-layout-single').find('.stec-layout-single-media-content > div').eq(this.cslide);


                var $textContent = $new.find('div');

                if ( $textContent.length > 0 ) {


                    $('.stec-layout-single').find('.stec-layout-single-media-content-subs div').fadeTo(250, 0, function () {

                        var caption = $textContent.find('p').text();
                        var desc = $textContent.find('span').text();

                        $('.stec-layout-single').find('.stec-layout-single-media-content-subs p').text(caption);
                        $('.stec-layout-single').find('.stec-layout-single-media-content-subs span').text(desc);

                        var height = $('.stec-layout-single').find('.stec-layout-single-media-content-subs p').height() + $('.stec-layout-single').find('.stec-layout-single-media-content-subs span').height();

                        if ( height > 0 ) {
                            height = height + 40;
                        }

                        $('.stec-layout-single').find('.stec-layout-single-media-content-subs').stop().animate({
                            height: height
                        }, {
                            duration: 400,
                            easing: 'stecExpo',
                            complete: function () {
                                $('.stec-layout-single').find('.stec-layout-single-media-content-subs div').fadeTo(250, 1);
                            }
                        });
                    });


                } else {

                    $('.stec-layout-single').find('.stec-layout-single-media-content-subs div').fadeTo(250, 0, function () {

                        $('.stec-layout-single').find('.stec-layout-single-media-content-subs').stop().animate({
                            height: 0
                        }, {
                            duration: 400,
                            easing: 'stecExpo'
                        });

                    });

                }

                $new.addClass('fade-in');

                setTimeout(function () {

                    $old.removeClass('active-image');
                    $new.removeClass('fade-in').addClass('active-image');

                }, 250);
            },

            slideNext: function () {

                var $li = $('.stec-layout-single').find('.stec-layout-single-media-controls-list li');

                if ( this.offset + this.visCount >= $li.length ) {
                    this.offset = 0;
                } else {
                    this.offset = this.offset + this.visCount;

                    if ( this.offset > $li.length - this.visCount ) {
                        this.offset = $li.length - this.visCount;
                    }
                }


                var left = -1 * ($li.first().width() * this.offset + this.offset * 10);

                $('.stec-layout-single').find('.stec-layout-single-media-controls-list').stop().animate({
                    left: left
                }, {
                    duration: 750,
                    easing: 'stecExpo'
                });

            },

            slidePrev: function () {

                var $li = $('.stec-layout-single').find('.stec-layout-single-media-controls-list li');

                if ( this.offset <= 0 ) {
                    this.offset = $li.length - this.visCount;
                } else {
                    this.offset = this.offset - this.visCount;

                    if ( this.offset < 0 ) {
                        this.offset = 0;
                    }
                }


                var left = -1 * ($li.first().width() * this.offset + this.offset * 10);

                $('.stec-layout-single').find('.stec-layout-single-media-controls-list').stop().animate({
                    left: left
                }, {
                    duration: 750,
                    easing: 'stecExpo'
                });

            }

        };

        var clock = {
            days: 0,
            hours: 0,
            minutes: 0,
            seconds: 0,
            daysLabel: '',
            hoursLabel: '',
            mionutesLabel: '',
            secondsLabel: '',
            interval: '',

            init: function () {

                if ( $('.stec-layout-single-counter').length <= 0 ) {
                    return;
                }

                var startDate = helper.dbDateTimeToDate(helper.dbDateOffset(glob.event.start_date, glob.options.repeat_offset));
                var nowDate = helper.getCalNow(parseInt(glob.event.timezone_utc_offset, 10) / 3600);

                if ( glob.event.start_date_timestamp_tz ) {
                    startDate = window.moment
                            .unix(glob.event.start_date_timestamp_tz + glob.options.repeat_offset)
                            .utcOffset(glob.event.timezone_utc_offset / 60)
                            .toDate();
                }

                var timeLeft = Math.floor((startDate.getTime() - nowDate.getTime()) / 1000);

                this.days = Math.floor(timeLeft / 86400);
                this.hours = Math.floor(timeLeft % 86400 / 3600);
                this.minutes = Math.floor(timeLeft % 86400 % 3600 / 60);
                this.seconds = Math.floor(timeLeft % 86400 % 3600 % 60);

                $('.stec-layout-single').find('.stec-layout-single-counter-num').eq(0).text(this.days);
                $('.stec-layout-single').find('.stec-layout-single-counter-num').eq(1).text(this.hours);
                $('.stec-layout-single').find('.stec-layout-single-counter-num').eq(2).text(this.minutes);
                $('.stec-layout-single').find('.stec-layout-single-counter-num').eq(3).text(this.seconds);

                this.daysLabel = $('.stec-layout-single').find('.stec-layout-single-counter-label').eq(0);
                this.hoursLabel = $('.stec-layout-single').find('.stec-layout-single-counter-label').eq(1);
                this.minutesLabel = $('.stec-layout-single').find('.stec-layout-single-counter-label').eq(2);
                this.secondsLabel = $('.stec-layout-single').find('.stec-layout-single-counter-label').eq(3);

                this.daysLabel.text(this.days == 1 ? this.daysLabel.attr('data-singular-label') : this.daysLabel.attr('data-plural-label'));
                this.hoursLabel.text(this.hours == 1 ? this.hoursLabel.attr('data-singular-label') : this.hoursLabel.attr('data-plural-label'));
                this.minutesLabel.text(this.minutes == 1 ? this.minutesLabel.attr('data-singular-label') : this.minutesLabel.attr('data-plural-label'));
                this.secondsLabel.text(this.seconds == 1 ? this.secondsLabel.attr('data-singular-label') : this.secondsLabel.attr('data-plural-label'));

                if ( timeLeft < 0 ) {
                    this.complete();
                    return;
                }

                this.count();
            },
            count: function () {

                var parent = this;

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
                                $('.stec-layout-single').find('.stec-layout-single-counter-num').eq(0).text(parent.days);
                                parent.daysLabel.text(parent.days == 1 ? parent.daysLabel.attr('data-singular-label') : parent.daysLabel.attr('data-plural-label'));
                            }
                            $('.stec-layout-single').find('.stec-layout-single-counter-num').eq(1).text(parent.hours);
                            parent.hoursLabel.text(parent.hours == 1 ? parent.hoursLabel.attr('data-singular-label') : parent.hoursLabel.attr('data-plural-label'));
                        }
                        $('.stec-layout-single').find('.stec-layout-single-counter-num').eq(2).text(parent.minutes);
                        parent.minutesLabel.text(parent.minutes == 1 ? parent.minutesLabel.attr('data-singular-label') : parent.minutesLabel.attr('data-plural-label'));
                    }
                    $('.stec-layout-single').find('.stec-layout-single-counter-num').eq(3).text(parent.seconds);
                    parent.secondsLabel.text(parent.seconds == 1 ? parent.secondsLabel.attr('data-singular-label') : parent.secondsLabel.attr('data-plural-label'));


                    if ( parent.days == 0 && parent.hours == 0 && parent.minutes == 0 && parent.seconds == 0 ) {
                        clearInterval(parent.interval);
                        parent.complete();
                    }

                }, 1000);
            },
            complete: function () {

                $('.stec-layout-single').find('.stec-layout-single-counter-num').eq(0).text(0);
                $('.stec-layout-single').find('.stec-layout-single-counter-num').eq(1).text(0);
                $('.stec-layout-single').find('.stec-layout-single-counter-num').eq(2).text(0);
                $('.stec-layout-single').find('.stec-layout-single-counter-num').eq(3).text(0);

                this.daysLabel.text(this.days == 1 ? this.daysLabel.attr('data-singular-label') : this.daysLabel.attr('data-plural-label'));
                this.hoursLabel.text(this.hours == 1 ? this.hoursLabel.attr('data-singular-label') : this.hoursLabel.attr('data-plural-label'));
                this.minutesLabel.text(this.minutes == 1 ? this.minutesLabel.attr('data-singular-label') : this.minutesLabel.attr('data-plural-label'));
                this.secondsLabel.text(this.seconds == 1 ? this.secondsLabel.attr('data-singular-label') : this.secondsLabel.attr('data-plural-label'));

                $('.stec-layout-single').find('.stec-layout-single-counter').hide();

                $('.stec-layout-single-intro-attendance').hide();
                $('.stec-layout-single-attendance-invited').hide();

                var now = helper.getCalNow(parseInt(glob.event.timezone_utc_offset, 10) / 3600);
                var endDate = helper.dbDateTimeToDate(helper.dbDateOffset(glob.event.end_date, glob.options.repeat_offset));

                if ( now >= endDate ) {

                    $('.stec-layout-single').find('.stec-layout-single-event-status-text.event-expired').show();

                } else {

                    $('.stec-layout-single').find('.stec-layout-single-event-status-text.event-inprogress').show();

                }
            }

        };

        var location = {

            init: function () {

                var parent = this;

                if ( $('.stec-layout-single-location-gmap').length <= 0 ) {
                    return;
                }

                parent.mapDiv = $('.stec-layout-single-location-gmap').get(0);

                parent.map = new window.google.maps.Map(parent.mapDiv, {
                    zoom: 15
                });

                parent.geocoder = new window.google.maps.Geocoder();

                parent.directionsService = new window.google.maps.DirectionsService;
                parent.directionsDisplay = new window.google.maps.DirectionsRenderer;

                parent.directionsDisplay.setMap(parent.map);

                parent.$container = $('.stec-layout-single-location');

                parent.address = parent.$container.find(".stec-layout-single-location-address").text();

                parent.setLocation(parent.address);

                // start end directions
                var $start = parent.$container.find('input[name="start"]');

                var $end = parent.$container.find('input[name="end"]');

                if ( $.trim($end.val()) == "" ) {
                    $end.val(parent.address);
                }

                if ( $.trim($start.val()) == "" ) {
                    parent.fillMyLocation($start);
                }

                this.bindControls();
            },

            fillMyLocation: function ($el) {

                var parent = this;

                if ( navigator.geolocation ) {

                    navigator.geolocation.getCurrentPosition(
                            function (position) {

                                var pos = position.coords.latitude + " " + position.coords.longitude;
                                parent.geocoder.geocode({'address': pos}, function (results, status) {
                                    parent.myLocation = (results[0].formatted_address);
                                    $el.val(parent.myLocation);
                                });
                            },
                            function (a, b, c) {
                                console.log('Navigator Geolocation Error');
                            }
                    );
                }
            },

            setLocation: function (address) {

                var parent = this;

                var location_use_coord = parent.$container.find(".stec-layout-single-location-address").attr("data-location-use-coord");

                if ( location_use_coord ) {

                    var latlng = location_use_coord.split(',');

                    var pos = {
                        lat: parseFloat($.trim(latlng[0])),
                        lng: parseFloat($.trim(latlng[1]))
                    };

                    parent.map.setCenter(pos);
                    parent.marker = new window.google.maps.Marker({
                        map: parent.map,
                        position: pos,
                        title: address
                    });

                } else {

                    parent.geocoder.geocode({'address': address}, function (results, status) {
                        if ( status === window.google.maps.GeocoderStatus.OK ) {
                            parent.map.setCenter(results[0].geometry.location);
                            parent.marker = new window.google.maps.Marker({
                                map: parent.map,
                                position: results[0].geometry.location,
                                title: address
                            });

                        } else {
                            console.log("Geocoder error");
                        }
                    });

                }


                parent.refresh();
            },

            getDirection: function (a, b) {

                var parent = this;

                parent.directionsService.route({
                    origin: a,
                    destination: b ? b : parent.marker.position,
                    travelMode: window.google.maps.TravelMode.DRIVING
                }, function (response, status) {

                    if ( status === window.google.maps.DirectionsStatus.OK ) {
                        parent.directionsDisplay.setDirections(response);
                    } else {
                        console.log("Direction Service Error");

                        $('.stec-layout-single-location-direction-error').stop().fadeTo(250, 1, function () {

                            setTimeout(function () {

                                $('.stec-layout-single-location-direction-error').fadeTo(250, 0);

                            }, 3000);

                        });
                    }

                });
            },

            refresh: function (centerOnLocation) {

                var parent = this;

                setTimeout(function () {
                    window.google.maps.event.trigger(parent.mapDiv, 'resize');

                    if ( centerOnLocation === true ) {
                        parent.setLocation(parent.address);
                    }
                }, 10); // timeout fixes resize bug

            },

            bindControls: function () {

                var parent = this;

                $(".stec-layout-single-location-get-direction-btn").on(helper.clickHandle(), function (e) {

                    e.preventDefault();

                    var $tabCont = $(".stec-layout-single-location");

                    var $start = $tabCont.find('input[name="start"]');
                    var $end = $tabCont.find('input[name="end"]');

                    if ( $.trim($start.val() != "") && $.trim($end.val() != "") ) {
                        parent.getDirection($start.val(), $end.val());
                    }
                });
            }

        };

        var tabs = {

            init: function () {

                $('.stec-layout-single-tabs-list li').first().addClass('active');
                $('.stec-layout-event-single-tabs-content > div').first().show();

                this.bindControls();
            },

            bindControls: function () {

                $('.stec-layout-single-tabs-list li').on(helper.clickHandle(), function () {

                    if ( $(this).hasClass('active') ) {
                        return;
                    }

                    $('.stec-layout-single-tabs-list li').not(this).removeClass('active');
                    $(this).addClass('active');

                    var tab = $(this).attr('data-tab').replace('stec-layout-single-', '');

                    $('.stec-layout-event-single-tabs-content > div').hide();
                    $('.stec-layout-event-single-tabs-content > .stec-layout-single-' + tab).show();
                });

            }
        };

        var schedule = {

            init: function () {

                this.bindControls();
            },

            bindControls: function () {

                $('.stec-layout-single-schedule-tab-preview').on(helper.clickHandle(), function (e) {
                    e.preventDefault();
                    $(this).parents(".stec-layout-single-schedule-tab")
                            .not('.stec-layout-single-schedule-tab-no-desc')
                            .toggleClass("open");
                });

            }
        };

        var woocommerce = {

            init: function () {

                this.bindControls();

            },

            bindControls: function () {

                var parent = this;

                $('.stec-layout-single-woocommerce-product-buy-addtocart').on(helper.clickHandle(), function (e) {

                    e.preventDefault();

                    var start = window.moment(glob.event.start_date).
                            add(glob.options.repeat_offset, 'seconds')
                            .utcOffset(event.timezone_utc_offset / 60, true);

                    if ( glob.event.start_date_timestamp_tz ) {
                        start = window.moment(glob.event.start_date)
                                .utcOffset(window.moment().utcOffset(), true)
                                .add(glob.options.repeat_offset, 'seconds')
                                .utcOffset(glob.event.timezone_utc_offset / 60)
                                .format('YYYY-MM-DD HH:mm:ss');
                    }

                    var product = {
                        id: $(this).attr('data-pid'),
                        sku: $(this).attr('data-sku'),
                        quantity: $(this).attr('data-quantity'),
                        event_start_date: start + ' ' + glob.event.calendar.timezone
                    };

                    parent.addToCart(product, $(this).parent());

                });
            },

            addToCart: function (product, $button) {

                $.ajax({

                    method: "POST",
                    url: glob.options.siteurl + '/?wc-ajax=add_to_cart',

                    data: {
                        product_id: product.id,
                        product_sku: product.sku,
                        quantity: product.quantity,
                        stec_event_start_date: product.event_start_date
                    },

                    beforeSend: function () {
                        // add preloader
                        $button.find('.stec-layout-single-woocommerce-product-buy-addtocart').hide();
                        $button.find('.stec-layout-single-woocommerce-product-buy-ajax-status').empty();
                        $(glob.template.preloader).appendTo($button.find('.stec-layout-single-woocommerce-product-buy-ajax-status'));

                    },

                    success: function (data) {

                        $button.find('.stec-layout-single-woocommerce-product-buy-ajax-status').empty();

                        if ( !data || data === null || (data.error && data.error === true) ) {

                            // add success icon
                            $('<i class="fa fa-times"></i>').appendTo($button.find('.stec-layout-single-woocommerce-product-buy-ajax-status'));


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
                            $('<i class="fa fa-check"></i>').appendTo($button.find('.stec-layout-single-woocommerce-product-buy-ajax-status'));

                            // decrement quantity

                            var quantity = $button.parent()
                                    .find('.stec-layout-single-woocommerce-product-quantity span').last().text();

                            if ( !isNaN(quantity) && quantity > 0 ) {
                                quantity--;

                                $button.parent()
                                        .find('.stec-layout-single-woocommerce-product-quantity span').last().text(quantity);
                            }

                            setTimeout(function () {
                                $button.find('.stec-layout-single-woocommerce-product-buy-ajax-status i').stop().fadeTo(1000, 0, function () {

                                    $(this).remove();

                                    if ( !isNaN(quantity) && quantity > 0 || quantity == '-' ) {
                                        $button.find('.stec-layout-single-woocommerce-product-buy-addtocart').show();
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

        };

        var forecast = {

            loaded: false,

            ajax: null,

            init: function () {

                var parent = this;

                this.bindControls();

                if ( $('.stec-layout-single-tabs-list li[data-tab="stec-layout-single-forecast"]').hasClass('active') ) {

                    if ( parent.loaded === false ) {
                        parent.getWeather();
                        parent.loaded = true;
                    }

                }
            },

            fillError: function () {
                $('.stec-layout-single-forecast-content').remove();
                $('.stec-layout-single-forecast').find('.errorna').show();
            },

            getWindDir: function (bearing) {

                while ( bearing < 0 )
                    bearing += 360;
                while ( bearing >= 360 )
                    bearing -= 360;
                var val = Math.round((bearing - 11.25) / 22.5);
                var arr = [
                    window.stecLang.N,
                    window.stecLang.NNE,
                    window.stecLang.NE,
                    window.stecLang.ENE,
                    window.stecLang.E,
                    window.stecLang.ESE,
                    window.stecLang.SE,
                    window.stecLang.SSE,
                    window.stecLang.S,
                    window.stecLang.SSW,
                    window.stecLang.SW,
                    window.stecLang.WSW,
                    window.stecLang.W,
                    window.stecLang.WNW,
                    window.stecLang.NW,
                    window.stecLang.NNW
                ];
                return arr[ Math.abs(val) ];

            },

            iconToText: function (icon) {

                switch ( icon ) {

                    case ('clear-day') :
                    case ('clear-night') :
                        return window.stecLang.clear_sky;
                        break;

                    case ('partly-cloudy-day') :
                    case ('partly-cloudy-night') :
                        return window.stecLang.partly_cloudy;
                        break;

                    case ('cloudy') :
                        return window.stecLang.cloudy;
                        break;

                    case ('fog') :
                        return window.stecLang.fog;
                        break;

                    case ('rain') :
                        return window.stecLang.rain;
                        break;

                    case ('sleet') :
                        return window.stecLang.sleet;
                        break;

                    case ('snow') :
                        return window.stecLang.snow;
                        break;

                    case ('wind') :
                        return window.stecLang.wind;
                        break;

                }
            },

            iconToiconHTML: function (icon, forceday) {

//          clear-day, clear-night, rain, snow, sleet, wind, fog, cloudy, partly-cloudy-day, or partly-cloudy-night

                switch ( icon ) {

                    case ('clear-day') :
                        return '<div class="stec-forecast-icon-clear-day"></div>';
                        break;

                    case ('clear-night') :
                        return forceday ? '<div class="stec-forecast-icon-clear-day"></div>' : '<div class="stec-forecast-icon-clear-night"></div>';
                        break;

                    case ('partly-cloudy-day') :
                        return '<div class="stec-forecast-icon-cloudy-day"></div>';
                        break;

                    case ('partly-cloudy-night') :
                        return forceday ? '<div class="stec-forecast-icon-cloudy-day"></div>' : '<div class="stec-forecast-icon-cloudy-night"></div>';
                        break;

                    case ('cloudy') :
                        return '<div class="stec-forecast-icon-cloudy"></div>';
                        break;

                    case ('fog') :
                        return '<div class="stec-forecast-icon-mist"></div>';
                        break;

                    case ('rain') :
                        return  '<div class="stec-forecast-icon-rain"></div>';
                        break;

                    case ('sleet') :
                        return '<div class="stec-forecast-icon-sleet"></div>';
                        break;

                    case ('snow') :
                        return '<div class="stec-forecast-icon-snow"></div>';
                        break;

                    case ('wind') :
                        return '<div class="stec-forecast-icon-cloudy"></div>';
                        break;

                }
            },

            floorFigure: function (figure, decimals) {
                if ( !decimals )
                    decimals = 2;
                var d = Math.pow(10, decimals);
                return (parseInt(figure * d) / d).toFixed(decimals);
            },

            getWeather: function () {

                var parent = this;

                var location = glob.event.location_forecast;

                parent.ajax = $.ajax({
                    type: 'POST',
                    url: window.ajaxurl,
                    data: {
                        action: 'stec_public_ajax_action',
                        task: 'get_weather_data',
                        location: function () {

                            location = location.split(',');

                            location[0] = parent.floorFigure(location[0]);
                            location[1] = parent.floorFigure(location[1]);

                            location = location.join(',');

                            return location;

                        }
                    },
                    beforeSend: function () {
                        if ( parent.ajax !== null ) {
                            parent.ajax.abort();
                        }

                        $(glob.template.preloader).appendTo($('.stec-layout-single-forecast'));
                    },
                    success: function (data) {

                        if ( data ) {

                            // error ?
                            if ( data.error || !data ) {
                                parent.fillError();
                                return;
                            }

                            parent.fillData(data);
                        } else {
                            parent.fillError();
                        }

                    },
                    error: function (xhr, status, thrown) {
                        parent.fillError();
                        console.log(xhr + " " + status + " " + thrown);
                    },
                    complete: function () {
                        parent.ajax = null;

                        // Remove tabs preloaders
                        $('.stec-layout-single-forecast').find('.stec-layout-single-preloader').remove();

                    },
                    dataType: "json"
                });
            },

            fillData: function (data) {

                var parent = this;

                var fiveDays = [];

                var i = 0;

                var forecast = data;

                var template = $('.stec-layout-single-forecast-details-left-forecast-day-template')[0].outerHTML;
                $('.stec-layout-single-forecast-details-left-forecast-day-template').remove();

                $(forecast.daily.data).each(function () {

                    if ( i > 4 )
                        return false;

                    var th = this;
                    var icon = parent.iconToiconHTML(th.icon, true);

                    // Localtime
                    var d = helper.treatAsUTC(new Date(this.time * 1000));
                    d.setHours(d.getHours() + forecast.offset);

                    var niceday = helper.beautifyTimespan(d, d, 1);

                    fiveDays[i] = $(template).html(function (index, html) {

                        var tempFmin = Math.round(th.temperatureMin);
                        var tempCmin = Math.round((tempFmin - 32) * 5 / 9);

                        var tempFmax = Math.round(th.temperatureMax);
                        var tempCmax = Math.round((tempFmax - 32) * 5 / 9);

                        return html
                                .replace(/\bstec_replace_date\b/g, niceday)
                                .replace(/\bstec_replace_min\b/g, glob.options.temp_units == "C" ? tempCmin : tempFmin)
                                .replace(/\bstec_replace_max\b/g, glob.options.temp_units == "C" ? tempCmax : tempFmax)
                                .replace(/\bstec_replace_temp_units\b/g, glob.options.temp_units == "C" ? "C" : "F")
                                .replace(/\bstec_replace_icon_div\b/g, icon);
                    })[0].outerHTML;

                    i++;
                });
                fiveDays = fiveDays.join('');

                $('.stec-layout-single-forecast').html(function (index, html) {

                    var icon = parent.iconToiconHTML(forecast.currently.icon);

                    var tempF = Math.round(forecast.currently.temperature);
                    var tempC = Math.round((tempF - 32) * 5 / 9);

                    var apTempF = Math.round(forecast.currently.apparentTemperature);
                    var apTempC = Math.round((tempF - 32) * 5 / 9);

                    var windMPH = Math.round(forecast.currently.windSpeed);
                    var windKPH = Math.round(windMPH * 1.609344);

                    var d = helper.treatAsUTC(new Date(forecast.currently.time * 1000));
                    d.setHours(d.getHours() + forecast.offset);

                    var niceday = helper.beautifyTimespan(d, d, 1);

                    return html
                            .replace(/\bstec_replace_current_summary_text\b/g, parent.iconToText(forecast.currently.icon))
                            .replace(/\bstec_replace_today_date\b/g, niceday)
                            .replace(/\bstec_replace_location\b/g, helper.capitalizeFirstLetter(glob.event.location))
                            .replace(/\bstec_replace_current_temp\b/g, glob.options.temp_units == "C" ? tempC : tempF)
                            .replace(/\bstec_replace_current_feels_like\b/g, glob.options.temp_units == "C" ? apTempC : apTempF)
                            .replace(/\bstec_replace_current_humidity\b/g, forecast.currently.humidity * 100)
                            .replace(/\bstec_replace_current_temp_units/g, glob.options.temp_units == "C" ? "C" : "F")
                            .replace(/\bstec_replace_current_wind\b/g, glob.options.wind_units == "MPH" ? windMPH : windKPH)
                            .replace(/\bstec_replace_current_wind_units\b/g, glob.options.wind_units == "MPH" ? "MPH" : "KPH")
                            .replace(/\bstec_replace_current_wind_direction\b/g, parent.getWindDir(forecast.currently.windBearing))
                            .replace(/\bstec_replace_today_icon_div\b/g, icon)
                            .replace(/\bstec_replace_5days\b/g, fiveDays);

                });

                // Chart instance
                setTimeout(function () {


                    var humidity = [],
                            tempC = [],
                            tempF = [],
                            rain = [],
                            j = -1;


                    var charTimeLabels = [];

                    for ( var i = 0; i < 8; i++ ) {

                        j = j + 3;

                        var th = forecast.hourly.data[j];

                        var tempf = Math.round(th.temperature);
                        var tempc = Math.round((tempf - 32) * 5 / 9);

                        tempC[i] = tempc;
                        tempF[i] = tempf;
                        humidity[i] = Math.round(th.humidity * 100);
                        rain[i] = th.precipProbability * 100;

                        var timeFormat = 'HH:mm';

                        if ( glob.options.time_format == '12' ) {
                            timeFormat = 'hh:mma';
                        }

                        // Local time
                        var d = helper.treatAsUTC(new Date(th.time * 1000));
                        charTimeLabels.push(window.moment(d).format(timeFormat));

                    }

                    var ch = new parent.chart();

                    ch.setCanvas($('.stec-layout-single-forecast-details-chart canvas'));

                    ch.setChartData({
                        labels: charTimeLabels,
                        datasets: [
                            {
                                label: window.stecLang.humidity_percents,
                                data: humidity,
                                backgroundColor: "rgba(200,200,200,0.1)",
                                borderColor: "rgba(200,200,200,1)",
                                pointBackgroundColor: "rgba(200,200,200,1)",
                                fill: true,
                                lineTension: 0.3,
                                pointHoverRadius: 5,
                                pointHitRadius: 10,
                                borderWidth: 1
                            },
                            {
                                label: window.stecLang.rain_chance_percents,
                                data: rain,
                                backgroundColor: "rgba(70,129,195,0.1)",
                                borderColor: "rgba(70,129,195,1)",
                                pointBackgroundColor: "rgba(70,129,195,1)",
                                fill: true,
                                lineTension: 0.3,
                                pointHoverRadius: 5,
                                pointHitRadius: 10,
                                borderWidth: 1
                            },
                            {
                                label: window.stecLang.temperature + ' ' + '\u00B0' + (glob.options.temp_units == "C" ? 'C' : 'F'),
                                data: glob.options.temp_units == "C" ? tempC : tempF,
                                backgroundColor: "rgba(252,183,85,0.3)",
                                borderColor: "rgba(252,183,85,1)",
                                pointBackgroundColor: "rgba(252,183,85,1)",
                                fill: true,
                                lineTension: 0.3,
                                pointHoverRadius: 5,
                                pointHitRadius: 10,
                                borderWidth: 1
                            }
                        ]
                    });

                    ch.build();

                    helper.onResizeEnd(function () {

                        if ( $('.stec-layout-single').hasClass('stec-layout-single-media-small') ) {
                            ch.chart.options.legend.display = false;
                        } else {
                            ch.chart.options.legend.display = true;
                        }

                        ch.chart.update();

                    }, 50);

                }, 0);

                $('.stec-layout-single-forecast-content').show();

            },

            chart: function () {

                this.ctx,
                        this.chartData,
                        this.chart,
                        this.setCanvas = function ($canvas) {

                            var canvas = $canvas.get(0);

                            var w = parseInt($('.stec-layout-single-forecast-details-chart').width(), 10);
                            var h = parseInt($('.stec-layout-single-forecast-details-chart').height(), 10);

                            canvas.width = w;
                            canvas.height = h;

                            this.ctx = $canvas.get(0).getContext("2d");

                        },
                        this.setChartData = function (chartData) {
                            this.chartData = chartData;
                        },
                        this.destroy = function () {
                            this.chart.destroy();
                        },
                        this.build = function () {

                            var parent = this;

                            if ( this.chart ) {
                                this.destroy();
                            }

                            var generalTextColor = $('.stec-layout-single-forecast-details-left-forecast-top p').css('color');
                            var generalFontFamily = $('.stec-layout-single-forecast-details-left-forecast-top p').css('font-family');
                            var displayLegend = true;

                            if ( $('.stec-layout-single').hasClass('stec-layout-single-media-small') ) {
                                displayLegend = false;
                            }

                            this.chart = window.Chart(this.ctx, {
                                type: 'line',
                                data: parent.chartData,
                                options: {
                                    maintainAspectRatio: false,
                                    responsive: true,
                                    defaultFontFamily: generalFontFamily,
                                    defaultFontColor: generalTextColor,
                                    legend: {
                                        display: displayLegend,
                                        labels: {
                                            fontFamily: generalFontFamily,
                                            fontColor: generalTextColor,
                                            fontSize: 12
                                        }
                                    },
                                    scales: {
                                        xAxes: [{
                                                ticks: {
                                                    fontFamily: generalFontFamily,
                                                    fontSize: 11,
                                                    fontColor: generalTextColor
                                                },
                                                gridLines: {
                                                    color: 'rgba(0,0,0,0.1)',
                                                    zeroLineColor: 'rgba(0,0,0,0)'
                                                }
                                            }],
                                        yAxes: [{
                                                ticks: {
                                                    fontFamily: generalFontFamily,
                                                    fontSize: 11,
                                                    fontColor: generalTextColor
                                                },
                                                gridLines: {
                                                    color: 'rgba(0,0,0,0.1)',
                                                    zeroLineColor: 'rgba(0,0,0,0)'
                                                }
                                            }]
                                    },
                                    tooltips: {
                                        titleFontColor: '#fff',
                                        titleFontStyle: generalFontFamily,
                                        bodyFontFamily: generalFontFamily,
                                        bodyFontColor: '#fff'
                                    }

                                }
                            });

                        };

            },

            bindControls: function () {

                var parent = this;

                $('li[data-tab="stec-layout-single-forecast"]').on(helper.clickHandle(), function () {

                    if ( parent.loaded === false ) {
                        parent.getWeather();

                        parent.loaded = true;
                    }
                });
            }
        };

        var attendance = {

            ajax: null,

            init: function () {

                var startDate = helper.dbDateTimeToDate(helper.dbDateOffset(glob.event.start_date, glob.options.repeat_offset));
                var nowDate = helper.getCalNow(parseInt(glob.event.timezone_utc_offset, 10) / 3600);

                var timeLeft = Math.floor((startDate.getTime() - nowDate.getTime()) / 1000);

                if ( timeLeft < 0 ) {

                    $('.stec-layout-single-intro-attendance').hide();
                    $('.stec-layout-single-attendance-invited').hide();

                    return;
                }

                this.bindControls();

            },

            ajaxAttendance: function (status) {

                // status
                // 0 - no decision
                // 1 - accept 
                // 2 - decline

                var parent = this;

                glob.ajax = $.ajax({
                    dataType: "json",
                    type: 'POST',
                    url: window.ajaxurl,
                    data: {
                        action: 'stec_public_ajax_action',
                        task: 'set_user_event_attendance',
                        event_id: glob.event.id,
                        repeat_offset: glob.options.repeat_offset,
                        status: status
                    },
                    beforeSend: function () {
                        if ( parent.ajax !== null ) {
                            parent.ajax.abort();
                        }

                        $('.stec-layout-single-attendance-invited-buttons').hide();

                        $(glob.template.preloader).appendTo($('.stec-layout-single-attendance-invited'));

                        $('.stec-layout-single-intro-attendance').children().hide();

                        $('<li>' + glob.template.preloader + '</li>')
                                .addClass('intro-attendance').
                                appendTo($('.stec-layout-single-intro-attendance'));
                    },
                    success: function (rtrn) {

                        var status = parseInt(rtrn.status, 10);
                        var id = parseInt(rtrn.id, 10);

                        switch ( status ) {

                            case 1:
                                $('.stec-layout-single-attendance-invited-buttons').children().removeClass('active');
                                $('.stec-layout-single-attendance-invited-buttons-accept').addClass('active');

                                $('.stec-layout-single-attendance-attendee-avatar[data-userid="' + glob.options.userid + '"]')
                                        .find('ul li i')
                                        .attr('class', 'fa fa-check');

                                $('.stec-layout-single-intro-attendance').children().removeClass('active');
                                $('.stec-layout-single-intro-attendance-attend').addClass('active');

                                break;

                            case 2:
                                $('.stec-layout-single-attendance-invited-buttons').children().removeClass('active');
                                $('.stec-layout-single-attendance-invited-buttons-decline').addClass('active');

                                $('.stec-layout-single-attendance-attendee-avatar[data-userid="' + glob.options.userid + '"]')
                                        .find('ul li i')
                                        .attr('class', 'fa fa-times');

                                $('.stec-layout-single-intro-attendance').children().removeClass('active');
                                $('.stec-layout-single-intro-attendance-decline').addClass('active');
                                break;

                            default:
                                $('.stec-layout-single-attendance-invited-buttons').children().removeClass('active');

                                $('.stec-layout-single-attendance-attendee-avatar[data-userid="' + glob.options.userid + '"]')
                                        .find('ul li i')
                                        .attr('class', 'fa fa-question');

                                $('.stec-layout-single-intro-attendance').children().removeClass('active');
                                break;

                        }

                    },
                    error: function (xhr, status, thrown) {
                        console.log(xhr + " " + status + " " + thrown);
                    },
                    complete: function () {

                        parent.ajax = null;

                        $('.stec-layout-single-attendance-invited-buttons').css('display', 'flex');
                        $('.stec-layout-single-attendance-invited').find('.stec-layout-single-preloader').remove();

                        $('.stec-layout-single-intro-attendance').children().show();
                        $('.stec-layout-single-intro-attendance').children().last().remove();
                    }
                });
            },

            bindControls: function () {

                var parent = this;

                $('.stec-layout-single-attendance-invited-buttons-accept').on(helper.clickHandle(), function (e) {
                    e.preventDefault();
                    var status = $(this).hasClass('active') ? 0 : 1;
                    parent.ajaxAttendance(status);
                });

                $('.stec-layout-single-attendance-invited-buttons-decline').on(helper.clickHandle(), function (e) {
                    e.preventDefault();
                    var status = $(this).hasClass('active') ? 0 : 2;
                    parent.ajaxAttendance(status);
                });

                $('.stec-layout-single-intro-attendance-attend').on(helper.clickHandle(), function (e) {
                    e.preventDefault();
                    var status = $(this).hasClass('active') ? 0 : 1;
                    parent.ajaxAttendance(status);
                });

                $('.stec-layout-single-intro-attendance-decline').on(helper.clickHandle(), function (e) {
                    e.preventDefault();
                    var status = $(this).hasClass('active') ? 0 : 2;
                    parent.ajaxAttendance(status);
                });

            }

        };

        var comments = {

            init: function () {

                if ( glob.event.comments == 0 ) {
                    return;
                }

                var eventId = glob.event.id;
                var disqus_shortname = glob.options.disqus_shortname;
                var disqus_title = glob.event.title;

                window.disqus_url = window.location.href + "#!stecEventDiscussion" + eventId;
                window.disqus_identifier = "stecEventID" + eventId;
                window.disqus_title = disqus_title;

                if ( typeof window.DISQUS === "undefined" ) {
                    $.ajax({
                        type: "GET",
                        url: "//" + disqus_shortname + ".disqus.com/embed.js",
                        dataType: "script",
                        cache: true
                    });
                } else {
                    window.DISQUS.reset({
                        reload: true
                    });
                }
            }


        };

        var bindControls = function () {


            $('.stec-layout-single').on('click', '.stec-layout-single-share .fa-link', function (e) {

                e.stopPropagation();
                e.preventDefault();

                var linkURL = $(this).parent().attr('href');
                var eventId = glob.event.id;
                var eventOffset = glob.options.repeat_offset;

                var sharer = new window.stecSharer($('.stec-layout-single'), glob, helper, linkURL, eventId, eventOffset);


            });

        };

    }

    $(document).ready(function () {
        new stecSingle().init();
    });

})(jQuery);