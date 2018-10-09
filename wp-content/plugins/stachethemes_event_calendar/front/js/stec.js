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

    /**
     *  Main Calendar front javascript
     */
    function stachethemesEventCalendar() {

        var moment = window.moment;

        var instance = '',
                $instance = '';

        var stecLang = window.stecLang;

        var glob = {

            options: {
                day: 1,
                month: 2,
                year: 2016,
                view: 'month',
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
                ],
                myLocation: ''
            },

            template: {
                event: '',
                eventInner: '',
                preloader: '',
                reminder: '',
                tooltip: '',
                eventCreateForm: ''
            },

            blockAction: false,

            ajax: null
        };

        var helper = {

            animate: false,

            isEmail: function (email) {
                var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                return regex.test(email);
            },

            /**
             * Date to ICS time string
             * @returns yyyymmddThhmmssZ
             */
            dateToRFC: function (date) {
                var dt = moment(date);
                var rfc = dt.utc().format('YYYYMMDDTHHmmss') + 'Z';
                return rfc;
            },

            eventToGoogleCalImportLink: function (eventId, repeatOffset) {

                var event = calData.getEventById(eventId);

                var d1 = helper.dbDateTimeToDate(event.start_date);
                d1.setTime(d1.getTime() + repeatOffset * 1000);

                var start_date = helper.dateToRFC(d1);

                var d2 = helper.dbDateTimeToDate(event.end_date);
                d2.setTime(d2.getTime() + repeatOffset * 1000);

                var end_date = helper.dateToRFC(d2);

                var description = event.description_short;
                var location = event.location;

                return "https://calendar.google.com/calendar/render?action=TEMPLATE&text=" + event.title + "&dates=" + start_date + "/" + end_date + "&details=" + description + "&location=" + location + "&sf=true&output=xml";
            },

            beautifyScheduleTimespan: function (start) {

                var d1 = window.moment(start);

                var format = '';
                var timeFormat = 'HH:mm';

                if ( glob.options.general_settings.time_format == '12' ) {
                    timeFormat = 'hh:mma';
                }

                switch ( glob.options.general_settings.date_format ) {
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

                var label = d1.format(format).split(' ');
                var tr_label = [];

                $.each(label, function () {
                    tr_label.push(typeof stecLang[this.toLowerCase()] !== 'undefined' ? stecLang[this.toLowerCase()] : this);
                });

                return tr_label.join(' ');
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

                if ( glob.options.general_settings.time_format == '12' ) {
                    timeFormat = 'hh:mma';
                }

                switch ( glob.options.general_settings.date_format ) {
                    case 'dd.mm.yyyy' :
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
                    default:
                        format = glob.options.general_settings.date_format;
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

                timespanLabel = timespanLabel.split(' ');

                var tr_timespanLabel = [];

                $.each(timespanLabel, function () {
                    tr_timespanLabel.push(typeof stecLang[this.toLowerCase()] !== 'undefined' ? stecLang[this.toLowerCase()] : this);
                });

                return tr_timespanLabel.join(' ');

            },

            diffDays: function (dt1, dt2) {
                return Math.abs(moment(dt2).diff(dt1, 'days'));
            },

            diffWeeks: function (d1, d2) {
                return Math.abs(moment(d2).diff(d1, 'weeks'));
            },

            focus: function (el) {

                if ( parseInt(glob.options.general_settings.event_auto_focus, 10) !== 1 ) {
                    return;
                }

                $('html, body').animate({
                    scrollTop: $(el).offset().top - $("#wpadminbar").height() + parseInt(glob.options.general_settings.event_auto_focus_offset, 10)
                }, {
                    duration: 750,
                    easing: "stecExpo"
                });
            },

            nl2br: function (txt) {
                return txt.replace(/(\r\n|\n\r|\r|\n)/g, "<br>");
            },

            capitalize: function (text) {
                return text.charAt(0).toUpperCase() + text.slice(1);
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

            /**
             * returns now Date for given calendar offset
             * @since 2.0.0 uses moment.js
             * @param {int} hoursOffset
             * @returns {Date} date object
             */
            getCalNow: function (hoursOffset) {
                return window.moment().utcOffset(parseInt(hoursOffset, 10) * 60).toDate();
            },

            /**
             * @param {type} string dbDate string
             * @param {type} offset seconds
             * @returns {String} dbDate string
             * @since 2.0.0 uses moment.js
             */
            dbDateOffset: function (string, offset) {
                return window.moment(string).add(offset, 'seconds').toDate();
            },

            /**
             * expects months range 1-12
             * converts to 0-11
             * @param {str} string yy-mm-dd h:i:s
             * @since 2.0.0 uses moment.js
             */
            dbDateTimeToDate: function (string) {
                return window.moment(string).toDate();
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
            },

            // @since 2.0.0
            dateToUnixStamp: function (date) {
                return window.moment(date).unix();
            },

            getColorBrightness: function (hex) {
                var hex = hex.substring(1);      // strip #
                var rgb = parseInt(hex, 16);   // convert rrggbb to decimal
                var r = (rgb >> 16) & 0xff;  // extract red
                var g = (rgb >> 8) & 0xff;   // extract green
                var b = (rgb >> 0) & 0xff;   // extract blue
                var luma = 0.2126 * r + 0.7152 * g + 0.0722 * b; // per ITU-R BT.709
                return luma;
            },

            extendBind: function (funcArr, sub, rtrn) {

                if ( typeof window[funcArr] !== "undefined" ) {

                    if ( sub ) {

                        if ( typeof window[funcArr][sub] !== "undefined" ) {
                            $(window[funcArr][sub]).each(function () {

                                if ( typeof this == "function" ) {

                                    this({
                                        instance: instance,
                                        $instance: $instance,
                                        glob: glob,
                                        helper: helper,
                                        calData: calData,
                                        layout: layout,
                                        events: events
                                    }, rtrn ? rtrn : null);
                                }
                            });
                        }

                    } else {

                        $(window[funcArr]).each(function () {

                            if ( typeof this == "function" ) {

                                this({
                                    instance: instance,
                                    $instance: $instance,
                                    glob: glob,
                                    helper: helper,
                                    calData: calData,
                                    layout: layout,
                                    events: events
                                }, rtrn ? rtrn : null);

                            }
                        });

                    }
                }

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

            iso8601Week: function (date) {
                var time;
                var checkDate = new Date(date.getTime());

                // Find Thursday of this week starting on Monday
                checkDate.setDate(checkDate.getDate() + 4 - (checkDate.getDay() || 7));

                time = checkDate.getTime();
                checkDate.setMonth(0); // Compare with Jan 1
                checkDate.setDate(1);
                return Math.floor(Math.round((time - checkDate) / 86400000) / 7) + 1;
            },

            /**
             * @param {Date} alt new Date()
             */
            getWeekInfo: function (alt) {

                var year, month, day;

                if ( alt ) {
                    year = alt.getFullYear();
                    month = alt.getMonth();
                    day = alt.getDate();
                } else {
                    year = glob.options.year;
                    month = glob.options.month;
                    day = glob.options.day;
                }

                var activeDate = new Date(year, month, day);

                switch ( glob.options.general_settings.first_day_of_the_week ) {
                    case 'mon' :

                        if ( activeDate.getDay() != 1 ) {
                            while ( activeDate.getDay() != 1 ) {
                                activeDate.setDate(activeDate.getDate() - 1);
                            }
                        }

                        break;

                    case 'sat' :

                        if ( activeDate.getDay() != 6 ) {
                            while ( activeDate.getDay() != 6 ) {
                                activeDate.setDate(activeDate.getDate() - 1);
                            }
                        }

                        break;

                    case 'sun' :

                        if ( activeDate.getDay() != 0 ) {
                            while ( activeDate.getDay() != 0 ) {
                                activeDate.setDate(activeDate.getDate() - 1);
                            }
                        }

                        break;
                }

                var firstDayOfTheWeek = new Date(activeDate);
                var lastDayOfTheWeek = new Date(firstDayOfTheWeek);
                lastDayOfTheWeek.setDate(lastDayOfTheWeek.getDate() + 6);

                return {
                    start: {
                        day: firstDayOfTheWeek.getDate(),
                        month: firstDayOfTheWeek.getMonth(),
                        year: firstDayOfTheWeek.getFullYear()
                    },
                    end: {
                        day: lastDayOfTheWeek.getDate(),
                        month: lastDayOfTheWeek.getMonth(),
                        year: lastDayOfTheWeek.getFullYear()
                    },
                    week: helper.iso8601Week(activeDate)
                };
            },

            getMonthInfo: function (month, year) {

                if ( isNaN(month) ) {
                    month = parseInt(glob.options.month, 10);
                }

                if ( isNaN(year) ) {
                    year = parseInt(glob.options.year, 10);
                }

                var daysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

                var firstDay = new Date(year, month, 1);
                var startingDay = firstDay.getDay();

                var monthLength = daysInMonth[month];

                if ( month == 1 ) {
                    if ( (year % 4 == 0 && year % 100 != 0) || year % 400 == 0 ) {
                        monthLength = 29;
                    }
                }

                var dayOffset = "";

                switch ( glob.options.general_settings.first_day_of_the_week ) {
                    case 'mon' :
                        dayOffset = startingDay - 1 < 0 ? startingDay + 6 : startingDay - 1;
                        break;

                    case 'sat' :
                        dayOffset = startingDay - 7 < 0 ? startingDay + 1 : startingDay - 7;

                        break;

                    case 'sun' :
                        dayOffset = startingDay;
                        break;
                }


                var monthInfo = {
                    startingDay: startingDay,
                    monthLength: monthLength,
                    year: year,
                    month: month,
                    dayOffset: dayOffset,
                    monthName: glob.options.monthLabels[month],
                    monthNameShort: glob.options.monthLabelsShort[month]
                };

                return monthInfo;
            },

            /**
             * data-date="yyyy-mm-dd" to Date()
             * @param {string} str yyyy-mm-dd
             * @returns {Date} returns new Date()
             */
            getDateFromData: function (str) {
                var d = str.split('-');
                return new Date(d[0], d[1], d[2]);
            },

            /**
             * Date() to yyy-mm-dd string
             * @param {Date} date
             * @returns {String}
             */
            getDataFromDate: function (date) {
                return date.getFullYear() + '-' + date.getMonth() + '-' + date.getDate();
            },

            /**
             * Translates labels
             * @param {string} label
             * @param {object} week
             * @returns string
             */
            getWeekLabel: function (label, week) {

                return  label.replace(/sday/g, week.start.day)
                        .replace(/smonth/g, glob.options.monthLabelsShort[week.start.month])
                        .replace(/syear/g, week.start.year)
                        .replace(/eday/g, week.end.day)
                        .replace(/emonth/g, glob.options.monthLabelsShort[week.end.month])
                        .replace(/eyear/g, week.end.year);

            },

            isMobile: function () {
                return (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) ? true : false;
            },

            clickHandle: function (sub) {

                var tap = sub ? 'vclick.' + sub : 'vclick';
                var click = sub ? 'click.' + sub : 'click';


                return this.isMobile() ? tap : click;

            },

            instaClickHandle: function () {
                return this.isMobile() ? "touchstart" : "mousedown";
            }

        };

        this.init = function (options) {

            $.extend(glob.options, options);

            instance = "#" + glob.options.id;
            $instance = $(instance);

            if ( $instance.length <= 0 ) {
                console.log('Stachethemes Event Calendar - License is not activated');
                return;
            }

            if ( helper.isMobile() ) {
                $instance.addClass('stec-mobile');
            }

            $instance.$top = $instance.children(".stec-top");                                 // inc.top.php wrap
            $instance.$agenda = $instance.children(".stec-layout").find(".stec-layout-agenda"); // layout.agenda.inc.php wrap
            $instance.$month = $instance.children(".stec-layout").find(".stec-layout-month"); // layout.month.inc.php wrap
            $instance.$week = $instance.children(".stec-layout").find(".stec-layout-week"); // layout.month.inc.php wrap
            $instance.$day = $instance.children(".stec-layout").find(".stec-layout-day"); // layout.day.inc.php wrap
            $instance.$grid = $instance.children(".stec-layout").find(".stec-layout-grid"); // layout.grid.inc.php wrap
            $instance.$events = $instance.children(".stec-layout").find(".stec-layout-events"); // layout.event.inc.php wrap

            $instance.$top.path = instance + " .stec-top ";
            $instance.$agenda.path = instance + " .stec-layout .stec-layout-agenda ";
            $instance.$month.path = instance + " .stec-layout .stec-layout-month ";
            $instance.$week.path = instance + " .stec-layout .stec-layout-week ";
            $instance.$day.path = instance + " .stec-layout .stec-layout-day ";
            $instance.$events.path = instance + " .stec-layout-events ";

            glob.template.eventAapproval = $instance.children(".stec-event-awaiting-approval-template").html();
            glob.template.eventCreateForm = $instance.children(".stec-event-create-form-template").html();
            glob.template.event = $instance.children(".stec-event-template").html();
            glob.template.gridevent = $instance.children(".stec-grid-event-template").html();
            glob.template.eventInner = $instance.children(".stec-event-inner-template").html();
            glob.template.tooltip = $instance.children(".stec-tooltip-template").html();
            glob.template.preloader = $instance.children(".stec-preloader-template").html();
            glob.template.reminder = $instance.children(".stec-layout-event-preview-reminder-template").html();
            glob.template.share = $instance.children(".stec-share-template").html();

            glob.options.view = glob.options.general_settings.view;

            if ( '1' == glob.options.general_settings.date_in_user_local_time ) {
                glob.options.general_settings.date_label_gmtutc = 0;
            }

            var now = new Date();

            // Go to user specified date on init
            if ( typeof glob.options.start_date !== 'undefined' ) {
                now = new Date(glob.options.start_date);
            }

            glob.options.day = now.getDate();
            glob.options.month = now.getMonth();
            glob.options.year = now.getFullYear();

            if ( typeof window.stecAnimate !== 'undefined' ) {
                helper.animate = new stecAnimate($instance);
            }

            preloader.add();

            top.init();
            layout.init();
            events.init();

            helper.extendBind("stachethemes_ec_extend");

        };

        /**
         * Calendar pre-init preloader 
         */
        var preloader = {

            add: function () {
                $(glob.template.preloader).addClass('stec-init-preloader stec-init-preloader-id-' + glob.options.id).insertBefore($instance);
            },

            destroy: function () {
                $('.stec-init-preloader-id-' + glob.options.id).remove();
            }

        };

        /**
         * handles top bar
         */
        var top = {

            dropDownScrollSpeed: 450,

            init: function () {
                this.bindControls();
                this.bindMobile();
            },

            bindMobile: function () {

                if ( !helper.isMobile() ) {
                    return;
                }

                $(document).on(helper.clickHandle(), function () {

                    $instance.$top.find('.mobile-hover').removeClass('mobile-hover');

                });

                $instance.$top.find('.stec-top-dropmenu-layouts').children('li').on(helper.clickHandle(), function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).toggleClass('mobile-hover');
                });

                $instance.$top.find('.stec-top-menu-date').children('li').on(helper.clickHandle(), function (e) {
                    e.preventDefault();

                    $(this).toggleClass('mobile-hover');

                    // on active; fill dropdowns
                    if ( $(this).hasClass('mobile-hover') ) {
                        e.stopPropagation();
                        // dropdown day display
                        if ( $(this).hasClass('stec-top-menu-date-day') ) {

                            $(this).attr("data-hovering", true);

                            $(this).find(".stec-top-menu-date-dropdown ul").remove();

                            var d = glob.options.day;

                            var html = '<ul>';

                            var m = helper.getMonthInfo();
                            var maxD = m.monthLength;

                            // center current year
                            for ( var j = 0; j < 3; j++ ) {
                                d = d - 1 < 1 ? maxD : d - 1;
                            }


                            for ( var i = 0; i < 5; i++ ) {

                                d = d + 1 > maxD ? 1 : d + 1;

                                html += '<li><p data-day="' + d + '">' + d + '</p></li>';
                            }
                            html += '</ul>';

                            $(this).find(".stec-top-menu-date-control-up").after(html);

                            top.dateDropdownSetActive();
                        }

                        // Dropdown week display
                        if ( $(this).hasClass('stec-top-menu-date-week') ) {

                            $(this).find(".stec-top-menu-date-dropdown ul").remove();

                            var w = new Date(glob.options.year, glob.options.month, glob.options.day);
                            w.setDate(w.getDate() - 3 * 7);

                            var format = '';

                            switch ( glob.options.general_settings.date_format ) {
                                case 'dd-mm-yy' :
                                    format = 'sday smonth - eday emonth eyear';
                                    break;
                                case 'mm-dd-yy' :
                                    format = 'smonth sday - emonth eday eyear';
                                    break;
                                case 'yy-mm-dd' :
                                    format = 'smonth sday - eyear emonth eday';
                                    break;
                            }

                            var html = '<ul>';
                            for ( var i = 0; i < 5; i++ ) {

                                w.setDate(w.getDate() + 7);

                                var week = helper.getWeekInfo(w);

                                var label = helper.getWeekLabel(format, week);

                                html += '<li><p data-week="' + week.week + '" data-date="' + week.start.year + "-" + week.start.month + "-" + week.start.day + '">' + label + '</p></li>';
                            }
                            html += '</ul>';

                            $(this).find(".stec-top-menu-date-control-up").after(html);

                            top.dateDropdownSetActive();
                        }

                        // Dropdown month display
                        if ( $(this).hasClass('stec-top-menu-date-month') ) {

                            $(this).find(".stec-top-menu-date-dropdown ul").remove();

                            var m = glob.options.month;

                            // center current month
                            for ( var i = 0; i < 3; i++ ) {
                                m = m - 1 < 0 ? 11 : m - 1;
                            }

                            var html = '<ul>';
                            for ( var i = 0; i < 5; i++ ) {
                                m = m + 1 > 11 ? 0 : m + 1;
                                html += '<li><p data-month="' + m + '">' + glob.options.monthLabels[m] + '</p></li>';
                            }
                            html += '</ul>';

                            $(this).find(".stec-top-menu-date-control-up").after(html);

                            top.dateDropdownSetActive();
                        }


                        // Dropdown year display
                        if ( $(this).hasClass('stec-top-menu-date-year') ) {

                            $(this).find(".stec-top-menu-date-dropdown ul").remove();

                            var y = glob.options.year;

                            var html = '<ul>';

                            // center current year
                            y = y - 3;

                            for ( var i = 0; i < 5; i++ ) {
                                y++;
                                html += '<li><p data-year="' + y + '">' + y + '</p></li>';
                            }
                            html += '</ul>';

                            $(this).find(".stec-top-menu-date-control-up").after(html);

                            top.dateDropdownSetActive();
                        }

                    }

                });

                $instance.$top.find('.stec-top-menu-date-control-up, .stec-top-menu-date-control-down').on(helper.clickHandle(), function (e) {
                    e.stopPropagation();
                });

            },

            bindControls: function () {

                var parent = this;

                // Click handle for top view buttons
                $instance.$top.find('[data-view]').on(helper.clickHandle(), function () {
                    glob.options.view = $(this).attr("data-view");
                    layout.set();
                });

                // Mousewheel for year dropdown
                $instance.$top.find(".stec-top-menu-date-year").on('DOMMouseScroll mousewheel', function (e) {

                    e.preventDefault();

                    if ( e.originalEvent.wheelDelta > 0 || e.originalEvent.detail < 0 ) {
                        parent.dateYearScrollUp();
                    } else {
                        parent.dateYearScrollDown();
                    }
                });

                // Mousewheel for month dropdown
                $instance.$top.find(".stec-top-menu-date-month").on('DOMMouseScroll mousewheel', function (e) {

                    e.preventDefault();

                    if ( e.originalEvent.wheelDelta > 0 || e.originalEvent.detail < 0 ) {
                        parent.dateMonthScrollUp();
                    } else {
                        parent.dateMonthScrollDown();
                    }
                });

                // Mousewheel for week dropdown
                $instance.$top.find(".stec-top-menu-date-week").on('DOMMouseScroll mousewheel', function (e) {

                    e.preventDefault();

                    if ( e.originalEvent.wheelDelta > 0 || e.originalEvent.detail < 0 ) {
                        parent.dateWeekScrollUp();
                    } else {
                        parent.dateWeekScrollDown();
                    }
                });

                // Mousewheel for day dropdown
                $instance.$top.find(".stec-top-menu-date-day").on('DOMMouseScroll mousewheel', function (e) {

                    e.preventDefault();

                    if ( e.originalEvent.wheelDelta > 0 || e.originalEvent.detail < 0 ) {
                        parent.dateDayScrollUp();
                    } else {
                        parent.dateDayScrollDown();
                    }
                });

                // Remove data-hovering from dropdown menu on mouseout
                $instance.$top.find(".stec-top-menu-date-year, .stec-top-menu-date-month, .stec-top-menu-date-week, .stec-top-menu-date-day").on("mouseleave", function () {
                    $(this).removeAttr("data-hovering");
                });

                // Dropdown day mouseenter display
                $instance.$top.find(".stec-top-menu-date-day").on("mouseenter", function () {

                    if ( $(this).attr("data-hovering") )
                        return;

                    $(this).attr("data-hovering", true);

                    $(this).find(".stec-top-menu-date-dropdown ul").remove();

                    var d = glob.options.day;

                    var html = '<ul>';

                    var m = helper.getMonthInfo();
                    var maxD = m.monthLength;

                    // center current year
                    for ( var j = 0; j < 3; j++ ) {
                        d = d - 1 < 1 ? maxD : d - 1;
                    }
                    ;

                    for ( var i = 0; i < 5; i++ ) {

                        d = d + 1 > maxD ? 1 : d + 1;

                        html += '<li><p data-day="' + d + '">' + d + '</p></li>';
                    }
                    html += '</ul>';

                    $(this).find(".stec-top-menu-date-control-up").after(html);

                    top.dateDropdownSetActive();
                });

                // Dropdown week mouseenter display
                $instance.$top.find(".stec-top-menu-date-week").on("mouseenter", function () {

                    if ( $(this).attr("data-hovering") ) {
                        return;
                    }

                    $(this).attr("data-hovering", true);

                    $(this).find(".stec-top-menu-date-dropdown ul").remove();


                    var w = new Date(glob.options.year, glob.options.month, glob.options.day);
                    w.setDate(w.getDate() - 3 * 7);

                    var format = '';

                    switch ( glob.options.general_settings.date_format ) {
                        case 'dd-mm-yy' :
                            format = 'sday smonth - eday emonth eyear';
                            break;
                        case 'mm-dd-yy' :
                            format = 'smonth sday - emonth eday eyear';
                            break;
                        case 'yy-mm-dd' :
                            format = 'smonth sday - eyear emonth eday';
                            break;
                    }

                    var html = '<ul>';
                    for ( var i = 0; i < 5; i++ ) {

                        w.setDate(w.getDate() + 7);

                        var week = helper.getWeekInfo(w);

                        var label = helper.getWeekLabel(format, week);

                        html += '<li><p data-week="' + week.week + '" data-date="' + week.start.year + "-" + week.start.month + "-" + week.start.day + '">' + label + '</p></li>';
                    }
                    html += '</ul>';

                    $(this).find(".stec-top-menu-date-control-up").after(html);

                    top.dateDropdownSetActive();
                });

                // Dropdown month mouseenter display
                $instance.$top.find(".stec-top-menu-date-month").on("mouseenter", function () {

                    if ( $(this).attr("data-hovering") ) {
                        return;
                    }

                    $(this).attr("data-hovering", true);

                    $(this).find(".stec-top-menu-date-dropdown ul").remove();

                    var m = glob.options.month;

                    // center current month
                    for ( var i = 0; i < 3; i++ ) {
                        m = m - 1 < 0 ? 11 : m - 1;
                    }

                    var html = '<ul>';
                    for ( var i = 0; i < 5; i++ ) {
                        m = m + 1 > 11 ? 0 : m + 1;
                        html += '<li><p data-month="' + m + '">' + glob.options.monthLabels[m] + '</p></li>';
                    }
                    html += '</ul>';

                    $(this).find(".stec-top-menu-date-control-up").after(html);

                    top.dateDropdownSetActive();
                });

                // Dropdown year mouseenter display
                $instance.$top.find(".stec-top-menu-date-year").on("mouseenter", function () {

                    if ( $(this).attr("data-hovering") ) {
                        return;
                    }

                    $(this).attr("data-hovering", true);

                    $(this).find(".stec-top-menu-date-dropdown ul").remove();

                    var y = glob.options.year;

                    var html = '<ul>';

                    // center current year
                    y = y - 3;

                    for ( var i = 0; i < 5; i++ ) {
                        y++;
                        html += '<li><p data-year="' + y + '">' + y + '</p></li>';
                    }
                    html += '</ul>';

                    $(this).find(".stec-top-menu-date-control-up").after(html);

                    top.dateDropdownSetActive();
                });

                // Dropdown week control up
                $instance.$top.find(".stec-top-menu-date-week .stec-top-menu-date-control-up").on(helper.clickHandle(), function (e) {
                    e.preventDefault();
                    parent.dateWeekScrollUp();
                });

                // Dropdown week control down
                $instance.$top.find(".stec-top-menu-date-week .stec-top-menu-date-control-down").on(helper.clickHandle(), function (e) {
                    e.preventDefault();
                    parent.dateWeekScrollDown();
                });

                // Dropdown year control up
                $instance.$top.find(".stec-top-menu-date-year .stec-top-menu-date-control-up").on(helper.clickHandle(), function (e) {
                    e.preventDefault();
                    parent.dateYearScrollUp();
                });

                // Dropdown year control down
                $instance.$top.find(".stec-top-menu-date-year .stec-top-menu-date-control-down").on(helper.clickHandle(), function (e) {
                    e.preventDefault();
                    parent.dateYearScrollDown();
                });

                // Dropdown month control up
                $instance.$top.find(".stec-top-menu-date-month .stec-top-menu-date-control-up").on(helper.clickHandle(), function (e) {
                    e.preventDefault();
                    parent.dateMonthScrollUp();
                });

                // Dropdown month control down
                $instance.$top.find(".stec-top-menu-date-month .stec-top-menu-date-control-down").on(helper.clickHandle(), function (e) {
                    e.preventDefault();
                    parent.dateMonthScrollDown();
                });

                // Dropdown day control up
                $instance.$top.find(".stec-top-menu-date-day .stec-top-menu-date-control-up").on(helper.clickHandle(), function (e) {
                    e.preventDefault();
                    parent.dateDayScrollUp();
                });

                // Dropdown day control down
                $instance.$top.find(".stec-top-menu-date-day .stec-top-menu-date-control-down").on(helper.clickHandle(), function (e) {
                    e.preventDefault();
                    parent.dateDayScrollDown();
                });

                // Dropdown month li pick
                $(document).on(helper.clickHandle(), $instance.$top.path + ".stec-top-menu-date-month ul li", function (e) {

                    e.preventDefault();

                    var month = $(this).find("[data-month]").attr("data-month");

                    glob.options.month = parseInt(month, 10);

                    var m = helper.getMonthInfo();

                    if ( glob.options.day > m.monthLength ) {
                        glob.options.day = parseInt(m.monthLength, 10);
                    }

                    layout.set();

                });

                // Dropdown year li pick
                $(document).on(helper.clickHandle(), $instance.$top.path + ".stec-top-menu-date-year ul li", function (e) {

                    e.preventDefault();

                    var year = $(this).find("[data-year]").attr("data-year");

                    glob.options.year = parseInt(year, 10);

                    layout.set();

                });

                // Dropdown week li pick
                $(document).on(helper.clickHandle(), $instance.$top.path + ".stec-top-menu-date-week ul li", function (e) {

                    e.preventDefault();

                    var d = helper.getDateFromData($(this).find("[data-date]").attr("data-date"));

                    glob.options.year = d.getFullYear();
                    glob.options.month = d.getMonth();
                    glob.options.day = d.getDate();

                    layout.set();

                });

                // Dropdown day li pick
                $(document).on(helper.clickHandle(), $instance.$top.path + ".stec-top-menu-date-day ul li", function (e) {

                    e.preventDefault();

                    glob.options.day = parseInt($(this).find("[data-day]").attr("data-day"), 10);

                    layout.set();

                });

                // Previous date depending on view layout
                $instance.$top.find("[data-action='previous']").on(helper.clickHandle(), function (e) {

                    e.preventDefault();

                    switch ( glob.options.view ) {

                        case "grid":
                        case "agenda" :

                            glob.options.month = glob.options.month - 1;

                            if ( glob.options.month < 0 ) {
                                glob.options.month = 11;
                                glob.options.year = glob.options.year - 1;
                            }

                            glob.options.day = 1;

                            layout.set();
                            break;

                        case "month" :

                            glob.options.month = glob.options.month - 1;

                            if ( glob.options.month < 0 ) {
                                glob.options.month = 11;
                                glob.options.year = glob.options.year - 1;
                            }

                            layout.set();
                            break;

                        case "week" :

                            var week = helper.getWeekInfo();

                            var next = new Date(week.start.year, week.start.month, week.start.day - 7);

                            glob.options.year = next.getFullYear();
                            glob.options.month = next.getMonth();
                            glob.options.day = next.getDate();

                            layout.set();

                            break

                        case "day" :

                            var m;

                            if ( glob.options.day - 1 < 1 ) {

                                if ( glob.options.month - 1 < 0 ) {

                                    glob.options.year = glob.options.year - 1;
                                    glob.options.month = 11;

                                    m = helper.getMonthInfo();
                                    glob.options.day = m.monthLength;

                                } else {

                                    glob.options.month = glob.options.month - 1;

                                    m = helper.getMonthInfo();
                                    glob.options.day = m.monthLength;
                                }

                            } else {
                                glob.options.day = glob.options.day - 1;
                            }

                            layout.set();
                            break;
                    }
                });

                // Next date depending on view layout
                $instance.$top.find("[data-action='next']").on(helper.clickHandle(), function (e) {

                    e.preventDefault();

                    switch ( glob.options.view ) {

                        case "grid":
                        case "agenda" :

                            glob.options.month = glob.options.month + 1;

                            if ( glob.options.month > 11 ) {
                                glob.options.month = 0;
                                glob.options.year = glob.options.year + 1;
                            }

                            glob.options.day = 1;

                            layout.set();
                            break;

                        case "month" :

                            glob.options.month = glob.options.month + 1;

                            if ( glob.options.month > 11 ) {
                                glob.options.month = 0;
                                glob.options.year = glob.options.year + 1;
                            }

                            layout.set();
                            break;

                        case "week" :

                            var week = helper.getWeekInfo();

                            var next = new Date(week.start.year, week.start.month, week.start.day + 7);

                            glob.options.year = next.getFullYear();
                            glob.options.month = next.getMonth();
                            glob.options.day = next.getDate();

                            layout.set();

                            break;

                        case "day" :

                            var m = helper.getMonthInfo();

                            if ( glob.options.day + 1 > m.monthLength ) {

                                if ( glob.options.month + 1 > 11 ) {

                                    glob.options.year = glob.options.year + 1;
                                    glob.options.month = 0;
                                    glob.options.day = 1;

                                } else {

                                    glob.options.month = glob.options.month + 1;
                                    glob.options.day = 1;
                                }

                            } else {
                                glob.options.day = glob.options.day + 1;
                            }

                            layout.set();
                            break;
                    }
                });

                // Today date depending on view layout
                $instance.$top.find("[data-action='today']").on(helper.clickHandle(), function (e) {

                    e.preventDefault();

                    glob.options.year = new Date().getFullYear();
                    glob.options.month = new Date().getMonth();
                    glob.options.day = new Date().getDate();
                    layout.set();
                });

                $(document)
                        .on(helper.clickHandle(), function () {
                            $instance.$top.find('.stec-top-menu-search.active').removeClass('active');
                            $instance.$top.find('.stec-top-menu-filter-calendar.active').removeClass('active');
                        });

            },

            /**
             * Set all top data
             */
            set: function () {

                // today events count
                var now = new Date();
                var events = calData.getEvents(now);

                if ( events.length > 0 ) {
                    $instance.$top.find('.stec-top-menu-count').text(events.length);
                    $instance.$top.find('.stec-top-menu-count').show();
                } else {
                    $instance.$top.find('.stec-top-menu-count').hide();
                }


                // Set .active current view 
                $instance.$top.find("[data-view]").removeClass("active");
                $instance.$top.find("[data-view='" + glob.options.view + "']").addClass("active");

                // Hide date menu
                $instance.$top.find(".stec-top-menu-date-month").hide();
                $instance.$top.find(".stec-top-menu-date-year").hide();
                $instance.$top.find(".stec-top-menu-date-week").hide();
                $instance.$top.find(".stec-top-menu-date-day").hide();

                // Show date menu for the active view
                // Sets date label visible on small devices only
                switch ( glob.options.view ) {

                    case "grid":
                    case "agenda" :

                        var rad = $instance.$top.find(".stec-top-menu-date-year").css("border-top-right-radius");

                        $instance.$top.find(".stec-top-menu-date-month, .stec-top-menu-date-month .stec-top-menu-date-dropdown").css({
                            borderTopLeftRadius: rad,
                            borderBottomLeftRadius: rad
                        });

                        $instance.$top.find(".stec-top-menu-date-month").show();
                        $instance.$top.find(".stec-top-menu-date-year").show();

                        $instance.$top.find(".stec-top-menu-date-month > [data-month]")
                                .attr("data-month", glob.options.month)
                                .text(glob.options.monthLabels[glob.options.month]);

                        $instance.$top.find(".stec-top-menu-date-day").show();
                        $instance.$top.find(".stec-top-menu-date-day > [data-day]")
                                .attr("data-month", glob.options.day)
                                .text(glob.options.day);

                        $instance.find(".stec-top-menu-date-year > [data-year]")
                                .attr("data-year", glob.options.year)
                                .text(glob.options.year);


                        switch ( glob.options.general_settings.date_format ) {
                            case 'dd-mm-yy' :
                                $instance.$top.find(".stec-top-menu-date-small")
                                        .text(glob.options.day + " " + glob.options.monthLabels[glob.options.month] + " " + glob.options.year);
                                break;
                            case 'mm-dd-yy' :
                                $instance.$top.find(".stec-top-menu-date-small")
                                        .text(glob.options.monthLabels[glob.options.month] + " " + glob.options.day + " " + glob.options.year);
                                break;
                            case 'yy-mm-dd' :
                                $instance.$top.find(".stec-top-menu-date-small")
                                        .text(glob.options.year + " " + glob.options.monthLabels[glob.options.month] + " " + glob.options.day);
                                break;
                        }

                        break;

                    case "month" :

                        var rad = $instance.$top.find(".stec-top-menu-date-year").css("border-top-right-radius");

                        $instance.$top.find(".stec-top-menu-date-month, .stec-top-menu-date-month .stec-top-menu-date-dropdown").css({
                            borderTopLeftRadius: rad,
                            borderBottomLeftRadius: rad
                        });

                        $instance.$top.find(".stec-top-menu-date-month").show();
                        $instance.$top.find(".stec-top-menu-date-year").show();

                        $instance.$top.find(".stec-top-menu-date-month > [data-month]")
                                .attr("data-month", glob.options.month)
                                .text(glob.options.monthLabels[glob.options.month]);

                        $instance.find(".stec-top-menu-date-year > [data-year]")
                                .attr("data-year", glob.options.year)
                                .text(glob.options.year);


                        switch ( glob.options.general_settings.date_format ) {
                            case 'dd-mm-yy' :
                                $instance.$top.find(".stec-top-menu-date-small")
                                        .text(glob.options.monthLabels[glob.options.month] + " " + glob.options.year);
                                break;
                            case 'mm-dd-yy' :
                                $instance.$top.find(".stec-top-menu-date-small")
                                        .text(glob.options.monthLabels[glob.options.month] + " " + glob.options.year);
                                break;
                            case 'yy-mm-dd' :
                                $instance.$top.find(".stec-top-menu-date-small")
                                        .text(glob.options.year + " " + glob.options.monthLabels[glob.options.month]);
                                break;
                        }


                        break;

                    case "week" :

                        var week = helper.getWeekInfo();
                        var label;

                        var format;

                        if ( week.start.year == week.end.year ) {
                            if ( week.start.month == week.end.month ) {

                                switch ( glob.options.general_settings.date_format ) {
                                    case 'dd-mm-yy' :
                                        format = 'sday - eday emonth eyear';
                                        break;
                                    case 'mm-dd-yy' :
                                        format = 'sday - emonth eday eyear';
                                        break;
                                    case 'yy-mm-dd' :
                                        format = 'sday - eyear emonth eday';
                                        break;
                                }

                            } else {

                                switch ( glob.options.general_settings.date_format ) {
                                    case 'dd-mm-yy' :
                                        format = 'sday smonth - eday emonth eyear';
                                        break;
                                    case 'mm-dd-yy' :
                                        format = 'smonth sday - emonth eday eyear';
                                        break;
                                    case 'yy-mm-dd' :
                                        format = 'smonth sday - eyear emonth eday';
                                        break;
                                }

                            }

                        } else {

                            switch ( glob.options.general_settings.date_format ) {
                                case 'dd-mm-yy' :
                                    format = 'sday smonth syear - eday emonth eyear';
                                    break;
                                case 'mm-dd-yy' :
                                    format = 'smonth sday syear - emonth eday eyear';
                                    break;
                                case 'yy-mm-dd' :
                                    format = 'syear smonth sday - eyear emonth eday';
                                    break;
                            }

                        }

                        label = format;

                        label = helper.getWeekLabel(label, week);

                        $instance.$top.find(".stec-top-menu-date-week").show();

                        $instance.$top.find(".stec-top-menu-date-week > [data-week]")
                                .attr("data-week", week.week)
                                .text(label);

                        $instance.$top.find(".stec-top-menu-date-small").text(label);

                        break;

                    case "day" :

                        $instance.$top.find(".stec-top-menu-date-month").css({
                            borderRadius: 0
                        }).find(".stec-top-menu-date-dropdown").css({
                            borderTopLeftRadius: 0
                        });

                        $instance.$top.find(".stec-top-menu-date-day").show();
                        $instance.$top.find(".stec-top-menu-date-month").show();
                        $instance.$top.find(".stec-top-menu-date-year").show();

                        $instance.$top.find(".stec-top-menu-date-day > [data-day]")
                                .attr("data-day", glob.options.day)
                                .text(glob.options.day);

                        $instance.$top.find(".stec-top-menu-date-month > [data-month]")
                                .attr("data-month", glob.options.month)
                                .text(glob.options.monthLabels[glob.options.month]);

                        $instance.$top.find(".stec-top-menu-date-year > [data-year]")
                                .attr("data-year", glob.options.year)
                                .text(glob.options.year);


                        switch ( glob.options.general_settings.date_format ) {
                            case 'dd-mm-yy' :
                                $instance.$top.find(".stec-top-menu-date-small")
                                        .text(glob.options.day + " " + glob.options.monthLabels[glob.options.month] + " " + glob.options.year);
                                break;
                            case 'mm-dd-yy' :
                                $instance.$top.find(".stec-top-menu-date-small")
                                        .text(glob.options.monthLabels[glob.options.month] + " " + glob.options.day + " " + glob.options.year);
                                break;
                            case 'yy-mm-dd' :
                                $instance.$top.find(".stec-top-menu-date-small")
                                        .text(glob.options.year + " " + glob.options.monthLabels[glob.options.month] + " " + glob.options.day);
                                break;
                        }

                        break;
                }

                // Add active class for the given year,month,week,day for menu dropdown
                this.dateDropdownSetActive();
            },

            /**
             * Add active class for the given year,month,week,day for menu dropdown
             */
            dateDropdownSetActive: function () {

                $instance.$top
                        .find(".stec-top-menu-date-dropdown .active").removeClass("active");

                $instance.$top
                        .find(".stec-top-menu-date-dropdown")
                        .find('[data-day="' + glob.options.day + '"]')
                        .parent()
                        .addClass('active');

                $instance.$top
                        .find(".stec-top-menu-date-dropdown")
                        .find('[data-month="' + glob.options.month + '"]')
                        .parent()
                        .addClass('active');

                $instance.$top
                        .find(".stec-top-menu-date-dropdown")
                        .find('[data-year="' + glob.options.year + '"]')
                        .parent()
                        .addClass('active');

                var week = helper.getWeekInfo();
                var weekDate = week.start.year + "-" + week.start.month + "-" + week.start.day;

                $instance.$top
                        .find(".stec-top-menu-date-dropdown")
                        .find('[data-date="' + weekDate + '"]')
                        .parent()
                        .addClass('active');

            },

            /**
             * Month scrolldown action
             */
            dateMonthScrollDown: function () {

                var parent = this;

                $instance.$top.find(".stec-top-menu-date-month").find("ul").stop(true, true);

                var m = $instance.$top.find(".stec-top-menu-date-month").find("ul:first").find("li:last [data-month]").attr("data-month");
                m = parseInt(m, 10);

                var html = '';

                for ( var i = 0; i <= 5; i++ ) {
                    m = m + 1 > 11 ? 0 : m + 1;
                    html += '<li><p data-month="' + m + '">' + glob.options.monthLabels[m] + '</p></li>';
                }

                $instance.$top.find(".stec-top-menu-date-month ul li:last").after(html);

                $instance.$top.find(".stec-top-menu-date-month").find("ul").css({
                    top: 45
                }).stop(true, true).animate({
                    top: -1 * 4 * 45
                }, {
                    duration: parent.dropDownScrollSpeed,
                    easing: "stecOutExpo",
                    complete: function () {
                        $(this).css("top", 45).find("li:lt(5)").remove();
                    }
                });

                this.dateDropdownSetActive();
            },

            /**
             * Month scrollup action
             */
            dateMonthScrollUp: function () {

                var parent = this;

                var m = $instance.$top.find(".stec-top-menu-date-month").find("ul:first").find("li:first [data-month]").attr("data-month");

                var html = '';
                var mArr = [];

                for ( var i = 0; i <= 4; i++ ) {
                    m = m - 1 < 0 ? 11 : m - 1;
                    mArr[i] = '<li><p data-month="' + m + '">' + glob.options.monthLabels[m] + '</p></li>';
                }

                html += mArr.reverse().join('');

                $instance.$top.find(".stec-top-menu-date-month ul li:first").before(html);

                $instance.$top.find(".stec-top-menu-date-month").find("ul").css({
                    top: -1 * 4 * 45
                }).stop().animate({
                    top: 45
                }, {
                    duration: parent.dropDownScrollSpeed,
                    easing: "stecOutExpo",
                    complete: function () {
                        $(this).css("top", 45).find("li:gt(5)").remove();
                    }
                });

                this.dateDropdownSetActive();

            },

            /**
             * Year scrolldown action
             */
            dateYearScrollDown: function () {

                var parent = this;

                $instance.$top.find(".stec-top-menu-date-year").find("ul").stop(true, true);

                var m = $instance.$top.find(".stec-top-menu-date-year").find("ul:first").find("li:last [data-year]").attr("data-year");
                m = parseInt(m, 10);

                var html = '';

                for ( var i = 0; i <= 5; i++ ) {
                    m = m + 1;
                    html += '<li><p data-year="' + m + '">' + m + '</p></li>';
                }

                $instance.$top.find(".stec-top-menu-date-year ul li:last").after(html);
                $instance.$top.find(".stec-top-menu-date-year").find("ul").css({
                    top: 45
                }).stop(true, true).animate({
                    top: -1 * 4 * 45
                }, {
                    duration: parent.dropDownScrollSpeed,
                    easing: "stecOutExpo",
                    complete: function () {
                        $(this).css("top", 45).find("li:lt(5)").remove();
                    }
                });

                this.dateDropdownSetActive();

            },

            /**
             * Year scrollup action
             */
            dateYearScrollUp: function () {

                var parent = this;

                var m = $instance.$top.find(".stec-top-menu-date-year").find("ul:first").find("li:first [data-year]").attr("data-year");

                var html = '';
                var mArr = [];

                for ( var i = 0; i <= 4; i++ ) {
                    m = m - 1;
                    mArr[i] = '<li><p data-year="' + m + '">' + m + '</p></li>';
                }

                html += mArr.reverse().join('');

                $instance.$top.find(".stec-top-menu-date-year ul li:first").before(html);

                $instance.$top.find(".stec-top-menu-date-year").find("ul").css({
                    top: -1 * 4 * 45
                }).stop().animate({
                    top: 45
                }, {
                    duration: parent.dropDownScrollSpeed,
                    easing: "stecOutExpo",
                    complete: function () {
                        $(this).css("top", 45).find("li:gt(5)").remove();
                    }
                });

                this.dateDropdownSetActive();
            },

            /**
             * Week scrolldown action
             */
            dateWeekScrollDown: function () {

                var parent = this;

                $instance.$top.find(".stec-top-menu-date-week").find("ul").stop(true, true);

                var lastWeek = $instance.$top.find(".stec-top-menu-date-week").find("ul:last").find("li:last [data-date]").attr("data-date");

                var html = '', week, label, w;

                var format;

                switch ( glob.options.general_settings.date_format ) {
                    case 'dd-mm-yy' :
                        format = 'sday smonth - eday emonth eyear';
                        break;
                    case 'mm-dd-yy' :
                        format = 'smonth sday - emonth eday eyear';
                        break;
                    case 'yy-mm-dd' :
                        format = 'smonth sday - eyear emonth eday';
                        break;
                }

                for ( var i = 1; i <= 6; i++ ) {

                    w = helper.getDateFromData(lastWeek);

                    w.setDate(w.getDate() + i * 7);

                    week = helper.getWeekInfo(w);

                    label = helper.getWeekLabel(format, week);

                    var date = week.start.year + "-" + week.start.month + "-" + week.start.day;

                    html += '<li><p data-week="' + week.week + '" data-date="' + date + '">' + label + '</p></li>';

                }

                $instance.$top.find(".stec-top-menu-date-week ul li:last").after(html);

                $instance.$top.find(".stec-top-menu-date-week").find("ul").css({
                    top: 45
                }).stop(true, true).animate({
                    top: -1 * 4 * 45
                }, {
                    duration: parent.dropDownScrollSpeed,
                    easing: "stecOutExpo",
                    complete: function () {
                        $(this).css("top", 45).find("li:lt(5)").remove();
                    }
                });

                this.dateDropdownSetActive();

            },

            dateWeekScrollUp: function () {

                var parent = this;

                var firstWeek = $instance.$top.find(".stec-top-menu-date-week").find("ul:first").find("li:first [data-date]").attr("data-date");

                var html = '', week, label, w, mArr = [];

                var format;

                switch ( glob.options.general_settings.date_format ) {
                    case 'dd-mm-yy' :
                        format = 'sday smonth - eday emonth eyear';
                        break;
                    case 'mm-dd-yy' :
                        format = 'smonth sday - emonth eday eyear';
                        break;
                    case 'yy-mm-dd' :
                        format = 'smonth sday - eyear emonth eday';
                        break;
                }

                for ( var i = 0; i <= 5; i++ ) {

                    w = helper.getDateFromData(firstWeek);

                    w.setDate(w.getDate() - i * 7);

                    week = helper.getWeekInfo(w);

                    label = helper.getWeekLabel(format, week);

                    var date = week.start.year + "-" + week.start.month + "-" + week.start.day;

                    mArr[i] = '<li><p data-week="' + week.week + '" data-date="' + date + '">' + label + '</p></li>';

                }

                html = mArr.reverse().join('');

                $instance.$top.find(".stec-top-menu-date-week ul li:first").before(html);

                $instance.$top.find(".stec-top-menu-date-week").find("ul").css({
                    top: -1 * 4 * 45
                }).stop().animate({
                    top: 45
                }, {
                    duration: parent.dropDownScrollSpeed,
                    easing: "stecOutExpo",
                    complete: function () {
                        $(this).css("top", 45).find("li:gt(5)").remove();
                    }
                });

                this.dateDropdownSetActive();

            },

            dateDayScrollDown: function () {

                var parent = this;

                $instance.$top.find(".stec-top-menu-date-day").find("ul").stop(true, true);

                var d = $instance.$top.find(".stec-top-menu-date-day").find("ul:first").find("li:last [data-day]").attr("data-day");
                d = parseInt(d, 10);

                var m = helper.getMonthInfo();
                var maxD = m.monthLength;

                var html = '';

                for ( var i = 0; i <= 5; i++ ) {
                    d = d + 1 > maxD ? 1 : d + 1;
                    html += '<li><p data-day="' + d + '">' + d + '</p></li>';
                }

                $instance.$top.find(".stec-top-menu-date-day ul li:last").after(html);

                $instance.$top.find(".stec-top-menu-date-day").find("ul").css({
                    top: 45
                }).stop(true, true).animate({
                    top: -1 * 4 * 45
                }, {
                    duration: parent.dropDownScrollSpeed,
                    easing: "stecOutExpo",
                    complete: function () {
                        $(this).css("top", 45).find("li:lt(5)").remove();
                    }
                });

                this.dateDropdownSetActive();

            },

            dateDayScrollUp: function () {

                var parent = this;

                var d = $instance.$top.find(".stec-top-menu-date-day").find("ul:first").find("li:first [data-day]").attr("data-day");

                var m = helper.getMonthInfo();
                var maxD = m.monthLength;

                var html = '';
                var mArr = [];

                for ( var i = 0; i <= 4; i++ ) {
                    d = d - 1 < 1 ? maxD : d - 1;
                    mArr[i] = '<li><p data-day="' + d + '">' + d + '</p></li>';
                }

                html += mArr.reverse().join('');

                $instance.$top.find(".stec-top-menu-date-day ul li:first").before(html);

                $instance.$top.find(".stec-top-menu-date-day").find("ul").css({
                    top: -1 * 4 * 45
                }).stop().animate({
                    top: 45
                }, {
                    duration: parent.dropDownScrollSpeed,
                    easing: "stecOutExpo",
                    complete: function () {
                        $(this).css("top", 45).find("li:gt(5)").remove();
                    }
                });

                this.dateDropdownSetActive();
            }
        };

        var layout = {
            init: function () {

                this.agenda.bindControls();
                this.month.bindControls();
                this.week.bindControls();
                this.day.bindControls();
                this.grid.bindControls();

                calData.pullEvents(function () {

                    preloader.destroy();
                    $instance.show();
                    layout.set();

                });
            },

            set: function () {

                $instance.$agenda.hide();
                $instance.$month.hide();
                $instance.$week.hide();
                $instance.$day.hide();
                $instance.$grid.hide();

                top.set();

                switch ( glob.options.view ) {
                    case "agenda" :
                        this.agenda.set();
                        break;

                    case "month" :
                        this.month.set();
                        break;

                    case "week" :
                        this.week.set();
                        break;

                    case "day" :
                        this.day.set();
                        break;

                    case "grid" :
                        this.grid.set();
                        break;
                }

                // after layout set
                helper.extendBind("stachethemes_ec_extend", "onLayoutSet");

            },

            /**
             * AGENDA
             */
            agenda: {

                cache: {
                    getNfutureEvents: false
                },

                bindControls: function () {

                    var parent = this;

                    // Agenda layout needs readjusting on window resize

                    helper.onResizeEnd(function () {
                        if ( $instance.$agenda.is(":visible") ) {
                            parent.set(true);
                        }
                    }, 50);

                    $instance.$agenda.find('.stec-layout-agenda-events-all-load-more').on(helper.clickHandle(), function (e) {
                        e.preventDefault();
                        parent.fillAgendaAllList();
                    });


                    // Click handle for cell click
                    $(document).on(helper.clickHandle(), $instance.$agenda.path + ' .stec-layout-agenda-daycell', function (e) {

                        e.preventDefault();

                        if ( $(this).hasClass("active") ) {
                            // Close active cell
                            events.eventHolder.close();
                            $(this).removeClass("active");
                            return;
                        }

                        var date = helper.getDateFromData($(this).attr('data-date'));

                        glob.options.day = date.getDate();
                        glob.options.month = date.getMonth();
                        glob.options.year = date.getFullYear();
                        top.set();

                        parent.setActiveCell();

                    });

                    // Instant click handle for cell click. Used for drag
                    $(document).on(helper.instaClickHandle(), $instance.$agenda.path + ' .stec-layout-agenda-daycell', function (e) {

                        e.preventDefault();

                        var date = helper.getDateFromData($(this).attr('data-date'));
                        var curr = new Date(glob.options.year, glob.options.month, glob.options.day);

                        if ( date.getTime() != curr.getTime() ) {

                            glob.options.day = date.getDate();
                            glob.options.month = date.getMonth();
                            glob.options.year = date.getFullYear();
                            top.set();
                        }

                    });

                    // Draggable slider
                    $instance.$agenda.find(".stec-layout-agenda-list").draggable({

                        axis: "x",

                        start: function (event, ui) {

                            $instance.$agenda.find(".stec-layout-agenda-list").stop();
                            this.previousPosition = ui.position;
                            this.time = new Date().getTime();

                        },

                        stop: function (event, ui) {

                            var time = new Date().getTime() - this.time;
                            var moved = Math.abs(ui.position.left - this.previousPosition.left);

                            parent.dragFill($(this), ui.position.left);
                            parent.innertia($(this), this.previousPosition.left > ui.position.left ? 1 : -1, time, moved);

                        },

                        drag: function (event, ui) {
                            parent.dragFill($(this), ui.position.left);
                        }

                    });
                },

                // Set agenda layout
                set: function (resizeOnly) {

                    // Check if Agenda Slider is set to show; else remove it
                    if ( glob.options.general_settings.agenda_cal_display != 0 ) {

                        var DIM = this.getSlideDimensions(true);

                        $instance.$agenda.find(".stec-layout-agenda-list")
                                .stop()
                                .css({
                                    left: 0,
                                    width: DIM.width
                                });

                        var cells = this.getCells(false, true, 1, true);

                        $instance.$agenda.find(".stec-layout-agenda-list-b").empty().css('left',
                                -1 * $instance.$agenda.find(".stec-layout-agenda-list-a").width()
                                );

                        this.fillHTML($instance.$agenda.find(".stec-layout-agenda-list-a"), cells);

                    } else {

                        $instance.$agenda.find('.stec-layout-agenda-list-wrap').remove();

                    }

                    if ( resizeOnly !== true ) {
                        events.eventHolder.close();
                        this.clearAgendaAllList();
                        this.fillAgendaAllList();
                    }

                    $instance.$agenda.show();

                },

                setActiveCell: function () {

                    var activeCell = glob.options.year + "-" + glob.options.month + "-" + glob.options.day;

                    $instance.$agenda
                            .find(".stec-layout-agenda-daycell")
                            .removeClass("active");

                    $instance.$agenda
                            .find(".stec-layout-agenda-daycell[data-date='" + activeCell + "']")
                            .addClass("active");

                    events.eventHolder.open();

                },

                getSlideDimensions: function (refresh) {

                    if ( refresh !== true && this.getSlideDimensions.cache ) {
                        return this.getSlideDimensions.cache;
                    }

                    var windowWidth = $(window).width() < 2000 ? 2000 : $(window).width();
                    var innerMaxCells = Math.floor($instance.width() / 80);
                    var cellWidth = Math.floor($instance.width() / innerMaxCells);
                    var maxCells = Math.round(windowWidth / cellWidth);
                    var width = maxCells * cellWidth;

                    var DIM = {
                        width: width,
                        maxCells: maxCells,
                        cellWidth: cellWidth
                    };

                    this.getSlideDimensions.cache = DIM;

                    return DIM;
                },

                /**
                 * Return cells data for active date
                 * @param {Date} date Alternative date
                 * @param {bool} centerOnDate Center on active date
                 * @param {int} direction -1 backwards 1 forwards
                 * @param {bool} borderMonthCell prevents el2 slide monthcell overlap
                 * @returns {array} returns cells data 
                 */
                getCells: function (date, centerOnDate, direction, borderMonthCell) {

                    if ( !date ) {
                        date = new Date(glob.options.year, glob.options.month, glob.options.day);
                    }

                    var DIM = this.getSlideDimensions();

                    var d = date.getDate();
                    var m = helper.getMonthInfo(date.getMonth());
                    var y = date.getFullYear();

                    if ( centerOnDate === true ) {

                        for ( var j = 0; j < Math.round(DIM.maxCells / ((DIM.width / $instance.width()) * 2)); j++ ) {

                            d = d - 1;

                            if ( d <= 0 ) {

                                m = m.month - 1;

                                if ( m < 0 ) {
                                    m = 11;
                                    y = y - 1;
                                }

                                m = helper.getMonthInfo(m, y);
                                d = m.monthLength;
                            }
                        }
                    }

                    var cellArray = [];
                    var count = 0;

                    // Fill cells
                    if ( direction === 1 ) {

                        for ( var i = 0; i < DIM.maxCells; i++ ) {

                            d = d + 1;

                            if ( d > m.monthLength ) {

                                m = m.month + 1;

                                if ( m > 11 ) {
                                    y = y + 1;
                                    m = 0;
                                }

                                m = helper.getMonthInfo(m, y);
                                d = 1;

                                if ( count != 0 || borderMonthCell === true ) {
                                    cellArray[count] = {
                                        dataDate: false,
                                        dayNum: false,
                                        day: false,
                                        year: y,
                                        month: m.month,
                                        monthStartCell: true,
                                        hasEvents: false
                                    };

                                    count++;
                                }


                            }

                            var date = new Date(y, m.month, d);

                            cellArray[count] = {
                                dataDate: y + "-" + m.month + "-" + d,
                                dayNum: date.getDay(),
                                day: d,
                                year: y,
                                month: m.month,
                                monthStartCell: false,
                                hasEvents: false
                            };

                            count++;
                        }

                        cellArray = cellArray.slice(0, DIM.maxCells);

                    } else {

                        for ( var i = 0; i < DIM.maxCells; i++ ) {

                            d = d - 1;

                            if ( d <= 0 ) {

                                if ( count != 0 || borderMonthCell === true ) {
                                    cellArray[count] = {
                                        dataDate: false,
                                        dayNum: false,
                                        day: false,
                                        year: y,
                                        month: m.month,
                                        monthStartCell: true,
                                        hasEvents: false
                                    };

                                    count++;
                                }

                                m = m.month - 1;

                                if ( m < 0 ) {
                                    y = y - 1;
                                    m = 11;
                                }

                                m = helper.getMonthInfo(m, y);
                                d = m.monthLength;

                            }


                            var date = new Date(y, m.month, d);

                            cellArray[count] = {
                                dataDate: y + "-" + m.month + "-" + d,
                                dayNum: date.getDay(),
                                day: d,
                                year: y,
                                month: m.month,
                                monthStartCell: false,
                                hasEvents: false
                            };

                            count++;

                        }

                        cellArray = cellArray.slice(0, DIM.maxCells);

                        cellArray.reverse();
                    }

                    return cellArray;

                },

                /**
                 * Create and append html for given cells
                 * @param {object} $el Element to append to
                 * @param {array} cells array
                 */
                fillHTML: function ($el, cells) {

                    var DIM = this.getSlideDimensions();

                    $el.empty();

                    var html = "";

                    $(cells).each(function () {

                        var cell = this;

                        if ( cell.monthStartCell === true ) {

                            html += '<li style="width: ' + DIM.cellWidth + 'px" class="stec-layout-agenda-monthstart" data-year="' + cell.year + '" data-month="' + cell.month + '" >';
                            html += '<div class="stec-layout-agenda-monthstart-wrap">';
                            html += '<p class="stec-layout-agenda-monthstart-year">' + cell.year + '</p>';
                            html += '<p class="stec-layout-agenda-monthstart-month">' + glob.options.monthLabelsShort[cell.month] + '</p>';
                            html += '</div>';
                            html += '</li>';

                        } else {

                            html += '<li style="width: ' + DIM.cellWidth + 'px" class="stec-layout-agenda-daycell" data-date="' + cell.dataDate + '">';
                            html += '<div class="stec-layout-agenda-daycell-wrap">';
                            html += '<p class="stec-layout-agenda-daycell-label">' + glob.options.dayLabelsShort[cell.dayNum] + '</p>';
                            html += '<p class="stec-layout-agenda-daycell-num">' + cell.day + '</p>';
                            html += '<div class="stec-layout-agenda-daycell-events">';
//                                    html += '<div class="stec-layout-agenda-daycell-event"></div>';
                            html += '</div>';
                            html += '</div>';
                            html += '</li>';
                        }

                    });

                    $(html).appendTo($el);

                    // Set Today 
                    var today = new Date();
                    var date = today.getFullYear() + "-" + today.getMonth() + "-" + today.getDate();

                    $instance.$agenda
                            .find(".stec-layout-agenda-daycell[data-date='" + date + "']")
                            .addClass("stec-layout-agenda-daycell-today");

                    this.fillEvents();

                },

                fillEvents: function () {

                    var $a = $instance.$agenda.find(".stec-layout-agenda-list-a");
                    var $b = $instance.$agenda.find(".stec-layout-agenda-list-b");

                    var start, end;

                    if ( $b.children().length <= 0 ) {
                        // b is empty (init start)

                        start = $a.children('.stec-layout-agenda-daycell').first().attr('data-date');
                        end = $a.children('.stec-layout-agenda-daycell').last().attr('data-date');

                    } else {

                        if ( $a[0].offsetLeft > $b[0].offsetLeft ) {
                            // b is behind

                            start = $b.children('.stec-layout-agenda-daycell').first().attr('data-date');
                            end = $a.children('.stec-layout-agenda-daycell').last().attr('data-date');
                        } else {

                            // b is ahead
                            end = $b.children('.stec-layout-agenda-daycell').last().attr('data-date');
                            start = $a.children('.stec-layout-agenda-daycell').first().attr('data-date');
                        }
                    }

                    $instance.$agenda
                            .children('.stec-layout-agenda-list-wrap')
                            .find('.stec-layout-agenda-daycell-events').empty();

                    // Populate cells with events

                    var startDate = helper.getDateFromData(start);
                    var endDate = helper.getDateFromData(end);

                    var events = calData.getEvents(startDate, endDate);

                    if ( events.length <= 0 ) {
                        // no events
                        return;
                    }

                    $(events).each(function () {

                        /**
                         * @todo use moment
                         */
                        var startDate = new Date(this.start_date_timestamp * 1000);
                        var endDate = new Date(this.end_date_timestamp * 1000);

                        var d1, d2, days = 0;

                        // we care for number of difference dates not per 24 hours
                        d1 = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate());
                        d2 = new Date(endDate.getFullYear(), endDate.getMonth(), endDate.getDate());

                        days = helper.diffDays(d1, d2);

                        for ( var day = 0; day <= days; day++ ) {

                            var stamp = moment(this.start_date).add(day, 'days').unix();

                            var dataDate = helper.getDataFromDate(new Date(stamp * 1000));

                            if ( $instance.$agenda
                                    .children('.stec-layout-agenda-list-wrap')
                                    .find('.stec-layout-agenda-daycell[data-date="' + dataDate + '"]')
                                    .find('.stec-layout-agenda-daycell-event').length > 2 ) {

                                // cells are full

                            } else {

                                var extraClass = '';

                                var calNow = helper.getCalNow(this.timezone_utc_offset / 3600);

                                if ( calNow > endDate ) {
                                    extraClass = 'stec-layout-agenda-daycell-event-expired';
                                }

                                var html = '<div class="stec-layout-agenda-daycell-event ' + extraClass + '" data-id="' + this.id + '" data-repeat-time-offset="' + this.repeat_time_offset + '" style="background:' + this.color + '"></div>';

                                $(html).appendTo($instance.$agenda
                                        .children('.stec-layout-agenda-list-wrap')
                                        .find('.stec-layout-agenda-daycell[data-date="' + dataDate + '"]')
                                        .find('.stec-layout-agenda-daycell-events'));

                            }

                        }


                    });

                },

                /**
                 * Build cells on the run while dragging if required
                 * @param {object} $el Dragged element
                 * @param {Number} pos current position
                 */
                dragFill: function ($el, pos) {

                    var $el2 = $el.parent().children(".stec-layout-agenda-list").not($el);

                    $el2.css('left', pos + (pos < 0 ? 1 : -1) * $el.width());

                    if ( $el2[0].offsetLeft > $el[0].offsetLeft ) {
                        // increment el2

                        var rebuild = false;

                        var d1 = helper.getDateFromData($el.children('.stec-layout-agenda-daycell').last().attr('data-date'));
                        var d2 = $el2.children('.stec-layout-agenda-daycell').first().attr('data-date');

                        if ( !d2 ) {
                            rebuild = true;
                        } else {
                            d2 = helper.getDateFromData(d2);
                            d2.setDate(d2.getDate() - 1);
                            if ( d1.getTime() != d2.getTime() ) {
                                rebuild = true;
                            }
                        }

                        if ( rebuild === true ) {

                            var borderMonthCell = true;

                            if ( $el.children("li").last().hasClass("stec-layout-agenda-monthstart") ) {
                                borderMonthCell = false;
                            }

                            d1.setDate(d1.getDate());
                            var cells = this.getCells(d1, false, 1, borderMonthCell);
                            this.fillHTML($el2, cells);
                        }

                    } else {
                        // decrement el2

                        var rebuild = false;

                        var d1 = helper.getDateFromData($el.children('.stec-layout-agenda-daycell').first().attr('data-date'));
                        var d2 = $el2.children('.stec-layout-agenda-daycell').last().attr('data-date');

                        if ( !d2 ) {
                            rebuild = true;
                        } else {
                            d2 = helper.getDateFromData(d2);
                            d2.setDate(d2.getDate() + 1);
                            if ( d1.getTime() != d2.getTime() ) {
                                rebuild = true;
                            }
                        }

                        if ( rebuild === true ) {

                            var borderMonthCell = true;

                            if ( $el.children("li").first().hasClass("stec-layout-agenda-monthstart") ) {
                                borderMonthCell = false;
                            }

                            var cells = this.getCells(d1, false, -1, borderMonthCell);
                            this.fillHTML($el2, cells);
                        }

                    }
                },

                innertia: function ($el, dir, time, moved) {

                    var parent = this;
                    var x = 0;

                    x = moved / time * 100;

                    x = x > $el.width() ? $el.width() / 4 : x;

                    $instance.$agenda.find(".stec-layout-agenda-list").stop();

                    switch ( dir ) {

                        case 1:
                            $el.animate({
                                left: $el.position().left - x
                            }, {
                                easing: "stecOutExpo",
                                duration: 1000,
                                step: function (a, b) {
                                    parent.dragFill($el, b.now);
                                }
                            });
                            break;

                        case - 1:
                            $el.animate({
                                left: $el.position().left + x
                            }, {
                                easing: "stecOutExpo",
                                duration: 1000,
                                step: function (a, b) {
                                    parent.dragFill($el, b.now);
                                }
                            });
                            break;

                    }

                },

                /**
                 * Reset agenda all-list
                 */
                clearAgendaAllList: function () {
                    this.cache.getNfutureEvents = false;
                    $instance.$agenda.find('.stec-layout-agenda-events-all-control').show();
                    $instance.$agenda.find('.stec-layout-agenda-events-all ul').remove();
                    $instance.$agenda.find('.stec-layout-agenda-events-all-datetext').remove();
                },

                /**
                 * Caches all future eventis initially
                 * Each call pulls N events from the cache array
                 * @return (object) events list
                 */
                getNfutureEvents: function () {

                    if ( !this.cache.getNfutureEvents ) {
                        // Load cache

                        this.getNfutureEvents.n = parseInt(glob.options.general_settings.agenda_get_n, 10);

                        if ( glob.options.general_settings.reverse_agenda_list == '1' ) {
                            this.cache.getNfutureEvents = $(calData.getFutureEvents()).get().reverse();
                        } else {
                            this.cache.getNfutureEvents = calData.getFutureEvents();
                        }


                        this.getNfutureEvents.i = 0;
                    }

                    var x = this.getNfutureEvents.i++ * this.getNfutureEvents.n;
                    var y = x + this.getNfutureEvents.n;

                    return this.cache.getNfutureEvents.slice(x, y);

                },

                /**
                 * Builds agenda all list events html
                 */
                fillAgendaAllList: function () {

                    if ( glob.options.general_settings.agenda_list_display == '0' ) {
                        return;
                    }

                    var events = this.getNfutureEvents();

                    if ( !events || events.length <= 0 ) {

                        // no events
                        $instance.$agenda.find('.stec-layout-agenda-events-all-control').hide();

                        return;
                    }

                    var lastLabel = $instance.$agenda.find('.stec-layout-agenda-events-all-datetext').last();
                    lastLabel.month = lastLabel.attr('data-month');
                    lastLabel.year = lastLabel.attr('data-year');

                    var now = new Date();

                    $(events).each(function (i) {

                        var event = this;

                        var d = helper.dbDateTimeToDate(this.start_date);

                        if ( now > d ) {
                            var noReminder = true;
                        }

                        if ( lastLabel.month != d.getMonth() || lastLabel.year != d.getFullYear() ) {
                            // Add new label
                            lastLabel.month = d.getMonth();
                            lastLabel.year = d.getFullYear();

                            $('<p data-year="' + d.getFullYear() + '" data-month="' + d.getMonth() + '" class="stec-layout-agenda-events-all-datetext">' + glob.options.monthLabels[d.getMonth()] + ' ' + d.getFullYear() + '</p>')
                                    .insertBefore($instance.$agenda.find('.stec-layout-agenda-events-all-control'));

                            $('<ul class="stec-layout-agenda-events-all-list"></ul>')
                                    .insertAfter($instance.$agenda.find('.stec-layout-agenda-events-all-datetext').last());
                        }

                        var featured_class = '';

                        switch ( parseInt(this.featured, 10) ) {
                            case 1:
                                featured_class = ' stec-event-featured ';
                                break;

                            case 2:
                                featured_class = ' stec-event-featured stec-event-featured-bg ';
                                break;

                            default:
                                featured_class = '';
                        }

                        var additional_class = "";

                        if ( event.icon == 'fa' ) {
                            additional_class += ' stec-no-icon ';
                        }

                        $(glob.template.event)
                                .addClass(featured_class)
                                .addClass(additional_class)
                                .addClass(noReminder ? 'stec-layout-event-no-reminder' : '')
                                .attr('data-id', event.id)
                                .attr('data-repeat-time-offset', event.repeat_time_offset ? event.repeat_time_offset : 0)
                                .html(function (index, html) {

                                    var date = helper.beautifyTimespan(event.start_date, event.end_date, event.all_day);

                                    var gmtutc_offset = parseInt(event.timezone_utc_offset, 10) / 3600;
                                    gmtutc_offset = gmtutc_offset > 0 ? '+' + gmtutc_offset : gmtutc_offset;

                                    if ( gmtutc_offset == 0 ) {
                                        gmtutc_offset = '';
                                    }

                                    var timezoneOffsetLabel = glob.options.general_settings.date_label_gmtutc == 0 ? '' : 'UTC/GMT ' + gmtutc_offset;

                                    html += '<a class="stec-layout-event-single-page-link" href="' + event.permalink + (event.repeat_time_offset > 0 ? event.repeat_time_offset : '') + '">' + event.title + '</a>';

                                    return html
                                            .replace('stec_replace_summary', event.title)
                                            .replace('stec_replace_date', date + ' ' + timezoneOffsetLabel)
                                            .replace('stec_replace_event_background', 'style="background:' + event.color + '"') // edge is retarded
                                            .replace('stec_replace_icon_class', event.icon)
                                            .replace('#stec-replace-edit-link', 'admin.php?page=stec_menu__events&view=edit&calendar_id=' +
                                                    event.calid + '&event_id=' + event.id);


                                }).appendTo($instance.find('.stec-layout-agenda-events-all-list').last());

                    });

                    // Remove + when single pages
                    if ( glob.options.general_settings.open_event_in == 'single' ) {
                        $instance
                                .find('.stec-layout-agenda-events-all')
                                .find('.stec-layout-event-preview-right-event-toggle')
                                .remove();
                    }

                    if ( helper.animate !== false ) {
                        helper.animate.agenda.fillList($instance.$agenda);
                    }


                }

            },

            /**
             * MONTH LAYOUT
             */
            month: {
                bindControls: function () {

                    var parent = this;

                    // Day cell click handle
                    $instance.$month.find(".stec-layout-month-daycell").on(helper.clickHandle(), function (e) {

                        e.preventDefault();

                        if ( $(this).hasClass("active") ) {
                            // Close active cell
                            events.eventHolder.close();
                            $(this).removeClass("active");
                            return;
                        }

                        var reset = false;

                        var date = helper.getDateFromData($(this).attr("data-date"));

                        if ( glob.options.year != date.getFullYear() || glob.options.month != date.getMonth() ) {
                            reset = true;
                        }

                        glob.options.year = date.getFullYear();
                        glob.options.month = date.getMonth();
                        glob.options.day = date.getDate();

                        if ( reset === true ) {
                            layout.set();
                            parent.setActiveCell();
                        } else {
                            parent.setActiveCell();
                        }

                    });

                },

                // Set to month layout
                set: function () {
                    $instance.$month.show();
                    this.setDayLabels();
                    this.fillGridDays();
                    this.fillEvents();
                },

                setDayLabels: function () {

                    var offset = 0;

                    switch ( glob.options.general_settings.first_day_of_the_week ) {
                        case 'mon' :
                            offset = 1;
                            break;

                        case 'sat' :
                            offset = 6;
                            break;

                        case 'sun' :
                            offset = 0;
                            break;
                    }

                    var a = offset;

                    $instance.$month.find('.stec-layout-month-daylabel td').each(function (i) {

                        var label = helper.capitalizeFirstLetter(glob.options.dayLabels[a]);
                        var labelShort = helper.capitalizeFirstLetter(glob.options.dayLabelsShort[a]);

                        $(this).find('p').eq(0).text(label);
                        $(this).find('p').eq(1).text(labelShort);
                        $(this).find('p').eq(2).text(labelShort.charAt(0));

                        a = a + 1 >= glob.options.dayLabels.length ? 0 : a + 1;

                    });
                },

                setActiveCell: function () {

                    var parent = this;

                    var activeCell = glob.options.year + "-" + glob.options.month + "-" + glob.options.day;

                    $instance.$month
                            .find(".stec-layout-month-daycell")
                            .removeClass("active");

                    $instance.$month
                            .find(".stec-layout-month-daycell[data-date='" + activeCell + "']")
                            .addClass("active");

                    $instance.$month.find(".stec-layout-month-eventholder")
                            .insertAfter(
                                    $instance.$month
                                    .find(".stec-layout-month-daycell.active")
                                    .parents("tr")
                                    );

                    events.eventHolder.open();
                },

                resetGridCells: function () {

                    var parent = this;

                    $instance.$month
                            .find(".stec-layout-month-daylabel td")
                            .removeClass("stec-layout-month-daylabel-today");

                    $instance.$month
                            .find(".stec-layout-month-daycell")
                            .removeAttr("data-date")
                            .removeClass("stec-layout-month-daycell-today stec-layout-month-daycell-inactive active");

                    $instance.$month
                            .find(".stec-layout-month-daycell")
                            .removeClass("active");

                    $instance.$month
                            .find('.stec-layout-month-daycell-events').empty();

                    events.eventHolder.close();

                },

                fillGridDays: function () {

                    var parent = this;

                    parent.resetGridCells();

                    // Active Month
                    var activeMonthInfo = helper.getMonthInfo();

                    for ( var i = 0; i < activeMonthInfo.monthLength; i++ ) {
                        var realDayNumber = i + 1;

                        $instance.$month
                                .find(".stec-layout-month-daycell")
                                .eq(activeMonthInfo.dayOffset + i)
                                .attr("data-date", activeMonthInfo.year + "-" + activeMonthInfo.month + "-" + realDayNumber)
                                .find(".stec-layout-month-daycell-num").text(realDayNumber);
                    }

                    // Prev Month 
                    var prevMonthInfo = helper.getMonthInfo(activeMonthInfo.month - 1 < 0 ? 11 : activeMonthInfo.month - 1, activeMonthInfo.month - 1 < 0 ? activeMonthInfo.year - 1 : activeMonthInfo.year);
                    for ( var i = activeMonthInfo.dayOffset; i > 0; i-- ) {
                        var realDayNumber = prevMonthInfo.monthLength - activeMonthInfo.dayOffset + i;
                        $instance.$month
                                .find(".stec-layout-month-daycell").eq(i - 1)
                                .addClass("stec-layout-month-daycell-inactive")
                                .attr("data-date", prevMonthInfo.year + "-" + prevMonthInfo.month + "-" + realDayNumber)
                                .find(".stec-layout-month-daycell-num").text(realDayNumber);
                    }

                    // Next Month 
                    var nextMonthInfo = helper.getMonthInfo(activeMonthInfo.month + 1 > 11 ? 0 : activeMonthInfo.month + 1, activeMonthInfo.month + 1 > 11 ? activeMonthInfo.year + 1 : activeMonthInfo.year);
                    for ( var i = 0; i < 6 * 7 - (activeMonthInfo.monthLength + activeMonthInfo.dayOffset); i++ ) {
                        var offset = activeMonthInfo.monthLength + activeMonthInfo.dayOffset + i;
                        var realDayNumber = i + 1;
                        $instance.$month
                                .find(".stec-layout-month-daycell").eq(offset)
                                .addClass("stec-layout-month-daycell-inactive")
                                .attr("data-date", nextMonthInfo.year + "-" + nextMonthInfo.month + "-" + realDayNumber)
                                .find(".stec-layout-month-daycell-num").text(realDayNumber);
                    }

                    // Set Today 
                    var today = new Date();
                    var date = today.getFullYear() + "-" + today.getMonth() + "-" + today.getDate();

                    $instance.$month
                            .find(".stec-layout-month-daycell[data-date='" + date + "']")
                            .addClass("stec-layout-month-daycell-today");

                    if ( $instance.$month.find(".stec-layout-month-daycell-today").length > 0 ) {

                        var offset = $instance.$month
                                .find(".stec-layout-month-weekrow")
                                .find(".stec-layout-month-daycell[data-date='" + date + "']").index();

                        $instance.$month
                                .find(".stec-layout-month-daylabel td")
                                .eq(offset)
                                .addClass("stec-layout-month-daylabel-today");
                    }

                },

                fillEvents: function () {

                    // Get layout date span

                    var from = helper.getDateFromData($instance.$month.find('.stec-layout-month-daycell').first().attr('data-date'));
                    var to = helper.getDateFromData($instance.$month.find('.stec-layout-month-daycell').last().attr('data-date'));

                    // Get all events for this timespan

                    var events = calData.getEvents(from, to);

                    if ( events.length <= 0 ) {
                        return;
                    }

                    // Loop each event

                    var hiddenEvents;

                    $(events).each(function () {

                        hiddenEvents = [];

                        var startDate = helper.dbDateTimeToDate(this.start_date);
                        var endDate = helper.dbDateTimeToDate(this.end_date);

                        var d1, d2, days = 0;

                        // we care for number of difference dates not per 24 hours
                        d1 = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate());
                        d2 = new Date(endDate.getFullYear(), endDate.getMonth(), endDate.getDate());

                        days = helper.diffDays(d1, d2);

                        for ( var day = 0; day <= days; day++ ) {

                            var stamp = moment(this.start_date).add(day, 'days').unix();

                            var dataDate = helper.getDataFromDate(new Date(stamp * 1000));

                            var $eventCont = $instance.$month
                                    .find('.stec-layout-month-daycell[data-date="' + dataDate + '"]')
                                    .find('.stec-layout-month-daycell-events');

                            var html = '';

                            if ( $eventCont.find('.stec-layout-month-daycell-event').length > 2 || (hiddenEvents.indexOf(this.id) > -1) ) {

                                // if container is full || event started from full container

                                if ( $eventCont.find('.stec-layout-month-daycell-eventmore').length > 0 ) {

                                    var text = $eventCont.find('.stec-layout-month-daycell-eventmore-count').text();
                                    var num = parseInt(text.match(/[0-9]+/), 10) + 1;
                                    var newText = '+' + num + ' ' + stecLang.MorePlural;

                                    $eventCont.find('.stec-layout-month-daycell-eventmore-count').text(newText);

                                } else {

                                    html += '<li class="stec-layout-month-daycell-eventmore">';
                                    html += '<p class="stec-layout-month-daycell-eventmore-count">+1 ' + stecLang.MoreSingular + '</p>';
                                    html += '<p class="stec-layout-month-daycell-eventmore-count-dot"></p>';
                                    html += '<p class="stec-layout-month-daycell-eventmore-count-dot"></p>';
                                    html += '<p class="stec-layout-month-daycell-eventmore-count-dot"></p>';
                                    html += '</li>';
                                }

                                hiddenEvents.push(this.id);

                            } else {

                                // Add event cell

                                var extraClass = '';

                                var style = 'style="background-color:' + this.color + '"';

                                if ( day == 0 ) {
                                    extraClass += ' stec-layout-month-daycell-event-start';
                                }

                                if ( day == days ) {
                                    extraClass += ' stec-layout-month-daycell-event-end';
                                }

                                var featured_class;

                                switch ( parseInt(this.featured, 10) ) {
                                    case 1:
                                        featured_class = ' stec-event-featured ';
                                        break;

                                    case 2:
                                        featured_class = ' stec-event-featured stec-event-featured-bg ';
                                        break;

                                    default:
                                        featured_class = '';
                                }

                                extraClass += featured_class;

                                // determine event position
                                var positions = [1, 2, 3];
                                var pos = 0;

                                // repeat instance acts like subid of the event
                                var repeat_time_offset = this.repeat_time_offset ? this.repeat_time_offset : 0;
                                var pos_id = this.id + '-' + repeat_time_offset;

                                var $first = $instance.$month.find('.stec-layout-month-daycell-event[data-pos-id="' + pos_id + '"]').first();

                                if ( $first.length > 0 ) {

                                    pos = $first.attr('data-pos');

                                } else {

                                    $eventCont.find('.stec-layout-month-daycell-event').each(function () {

                                        var i = positions.indexOf(parseInt($(this).attr('data-pos'), 10));

                                        if ( i > -1 ) {
                                            positions.splice(i, 1);
                                        }
                                    });

                                    pos = positions[0];

                                }

                                var brightness = helper.getColorBrightness(this.color);

                                if ( brightness > 170 ) {
                                    extraClass += " stec-layout-month-daycell-event-bright";
                                }

                                var calNow = helper.getCalNow(this.timezone_utc_offset / 3600);

                                if ( calNow > endDate ) {
                                    extraClass += " stec-layout-month-daycell-event-expired";
                                }

                                html = '<li data-pos="' + pos + '" data-pos-id="' + pos_id + '" data-repeat-time-offset="' + repeat_time_offset + '" data-id="' + this.id + '" class="stec-layout-month-daycell-event ' + extraClass + '" ' + style + '>';

                                if ( day == 0 || glob.options.general_settings.show_event_title_all_cells != 0 ) {

                                    html += '<p class="stec-layout-month-daycell-event-name">' + this.title + '</p>';
                                }

                                html += '</li>';

                            }

                            $(html).appendTo($eventCont);
                        }
                    });

                }
            },

            /**
             * WEEK LAYOUT
             */
            week: {
                bindControls: function () {

                    var parent = this;

                    // Week daycell click handle
                    $instance.$week.find(".stec-layout-week-daycell").on(helper.clickHandle(), function (e) {

                        e.preventDefault();

                        if ( $(this).hasClass("active") ) {
                            // Close active cell
                            events.eventHolder.close();
                            $(this).removeClass("active");
                            return;
                        }

                        var date = helper.getDateFromData($(this).attr("data-date"));

                        glob.options.year = date.getFullYear();
                        glob.options.month = date.getMonth();
                        glob.options.day = date.getDate();

                        parent.setActiveCell();


                    });
                },

                set: function () {
                    $instance.$week.show();
                    this.setDayLabels();
                    this.fillGridDays();
                    this.fillEvents();
                },

                setDayLabels: function () {

                    var offset = 0;

                    switch ( glob.options.general_settings.first_day_of_the_week ) {
                        case 'mon' :
                            offset = 1;
                            break;

                        case 'sat' :
                            offset = 6;
                            break;

                        case 'sun' :
                            offset = 0;
                            break;
                    }

                    var a = offset;

                    $instance.$week.find('.stec-layout-week-daylabel td').each(function (i) {

                        var label = helper.capitalizeFirstLetter(glob.options.dayLabels[a]);
                        var labelShort = helper.capitalizeFirstLetter(glob.options.dayLabelsShort[a]);

                        $(this).find('p').eq(0).text(label);
                        $(this).find('p').eq(1).text(labelShort);
                        $(this).find('p').eq(2).text(labelShort.charAt(0));

                        a = a + 1 >= glob.options.dayLabels.length ? 0 : a + 1;

                    });
                },

                setActiveCell: function () {

                    var parent = this;

                    // Set Active Cell

                    var activeCell = glob.options.year + "-" + glob.options.month + "-" + glob.options.day;

                    $instance.$week
                            .find(".stec-layout-week-daycell")
                            .removeClass("active");

                    $instance.$week
                            .find(".stec-layout-week-daycell[data-date='" + activeCell + "']")
                            .addClass("active");

                    events.eventHolder.open();

                },

                resetGridCells: function () {

                    // Reset data 

                    $instance.$week
                            .find(".stec-layout-week-daylabel td")
                            .removeClass("stec-layout-week-daylabel-today");

                    $instance.$week
                            .find(".stec-layout-week-daycell")
                            .removeAttr("data-date")
                            .removeClass("stec-layout-week-daycell-today stec-layout-week-daycell-inactive active");

                    $instance.$week
                            .find(".stec-layout-week-daycell")
                            .removeClass("active");

                    $instance.$week
                            .find('.stec-layout-week-daycell-events').empty();

                    events.eventHolder.close();

                },

                fillGridDays: function () {

                    var parent = this;

                    parent.resetGridCells();

                    var week = helper.getWeekInfo();

                    // Active Week
                    $instance.$week
                            .find(".stec-layout-week-daycell").each(function (i) {

                        var cellDate = new Date(week.start.year, week.start.month, week.start.day);
                        var next = i * 24 * 60 * 60 * 1000;

                        cellDate.setTime(cellDate.getTime() + next);

                        $(this)
                                .attr("data-date", cellDate.getFullYear() + "-" + cellDate.getMonth() + "-" + cellDate.getDate())
                                .find(".stec-layout-week-daycell-num").text(cellDate.getDate());

                    });


                    // Set Today 
                    var today = new Date();
                    var date = today.getFullYear() + "-" + today.getMonth() + "-" + today.getDate();

                    $instance.$week
                            .find(".stec-layout-week-daycell[data-date='" + date + "']")
                            .addClass("stec-layout-week-daycell-today");

                    if ( $instance.$week.find(".stec-layout-week-daycell-today").length > 0 ) {

                        var offset = $instance.$week
                                .find(".stec-layout-week-weekrow")
                                .find(".stec-layout-week-daycell[data-date='" + date + "']").index();

                        $instance.$week
                                .find(".stec-layout-week-daylabel td")
                                .eq(offset)
                                .addClass("stec-layout-week-daylabel-today");
                    }
                },

                fillEvents: function () {

                    // Get layout date span

                    var from = helper.getDateFromData($instance.$week.find('.stec-layout-week-daycell').first().attr('data-date'));
                    var to = helper.getDateFromData($instance.$week.find('.stec-layout-week-daycell').last().attr('data-date'));

                    // Get all events for this timespan

                    var events = calData.getEvents(from, to);

                    if ( events.length <= 0 ) {
                        return;
                    }

                    // Loop each event

                    var hiddenEvents;

                    $(events).each(function () {

                        hiddenEvents = [];

                        var startDate = helper.dbDateTimeToDate(this.start_date);
                        var endDate = helper.dbDateTimeToDate(this.end_date);

                        var d1, d2, days = 0;

                        // we care for number of difference dates not per 24 hours
                        d1 = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate());
                        d2 = new Date(endDate.getFullYear(), endDate.getMonth(), endDate.getDate());

                        days = helper.diffDays(d1, d2);

                        for ( var day = 0; day <= days; day++ ) {

                            var stamp = moment(this.start_date).add(day, 'days').unix();

                            var dataDate = helper.getDataFromDate(new Date(stamp * 1000));

                            var $eventCont = $instance.$week
                                    .find('.stec-layout-week-daycell[data-date="' + dataDate + '"]')
                                    .find('.stec-layout-week-daycell-events');

                            var html = '';

                            if ( $eventCont.find('.stec-layout-week-daycell-event').length > 2 || (hiddenEvents.indexOf(this.id) > -1) ) {

                                // if container is full || event started from full container

                                if ( $eventCont.find('.stec-layout-week-daycell-eventmore').length > 0 ) {

                                    var text = $eventCont.find('.stec-layout-week-daycell-eventmore-count').text();
                                    var num = parseInt(text.match(/[0-9]+/), 10) + 1;
                                    var newText = '+' + num + ' ' + stecLang.MorePlural;

                                    $eventCont.find('.stec-layout-week-daycell-eventmore-count').text(newText);

                                } else {

                                    html += '<li class="stec-layout-week-daycell-eventmore">';
                                    html += '<p class="stec-layout-week-daycell-eventmore-count">+1 ' + stecLang.MoreSingular + '</p>';
                                    html += '<p class="stec-layout-week-daycell-eventmore-count-dot"></p>';
                                    html += '<p class="stec-layout-week-daycell-eventmore-count-dot"></p>';
                                    html += '<p class="stec-layout-week-daycell-eventmore-count-dot"></p>';
                                    html += '</li>';
                                }

                                hiddenEvents.push(this.id);

                            } else {

                                // Add event cell

                                var extraClass = '';

                                var style = 'style="background-color:' + this.color + '"';

                                if ( day == 0 ) {
                                    extraClass += ' stec-layout-week-daycell-event-start';
                                }

                                if ( day == days ) {
                                    extraClass += ' stec-layout-week-daycell-event-end';
                                }

                                var featured_class;

                                switch ( parseInt(this.featured, 10) ) {
                                    case 1:
                                        featured_class = ' stec-event-featured ';
                                        break;

                                    case 2:
                                        featured_class = ' stec-event-featured stec-event-featured-bg ';
                                        break;

                                    default:
                                        featured_class = '';
                                }

                                extraClass += featured_class;

                                // determine event position
                                var positions = [1, 2, 3];
                                var pos = 0;

                                // repeat instance acts like subid of the event
                                var repeat_time_offset = this.repeat_time_offset ? this.repeat_time_offset : 0;
                                var pos_id = this.id + '-' + repeat_time_offset;

                                var $first = $instance.$week.find('.stec-layout-week-daycell-event[data-pos-id="' + pos_id + '"]').first();

                                if ( $first.length > 0 ) {

                                    pos = $first.attr('data-pos');

                                } else {

                                    $eventCont.find('.stec-layout-week-daycell-event').each(function () {

                                        var i = positions.indexOf(parseInt($(this).attr('data-pos'), 10));

                                        if ( i > -1 ) {
                                            positions.splice(i, 1);
                                        }
                                    });

                                    pos = positions[0];

                                }

                                var brightness = helper.getColorBrightness(this.color);

                                if ( brightness > 170 ) {
                                    extraClass += " stec-layout-week-daycell-event-bright";
                                }

                                var calNow = helper.getCalNow(this.timezone_utc_offset / 3600);

                                if ( calNow > endDate ) {
                                    extraClass += " stec-layout-month-daycell-event-expired";
                                }

                                html = '<li data-pos="' + pos + '" data-pos-id="' + pos_id + '" data-repeat-time-offset="' + repeat_time_offset + '" data-id="' + this.id + '" class="stec-layout-week-daycell-event ' + extraClass + '" ' + style + '>';

                                if ( day == 0 || glob.options.general_settings.show_event_title_all_cells != 0 ) {
                                    html += '<p class="stec-layout-week-daycell-event-name">' + this.title + '</p>';
                                }

                                html += '</li>';

                            }

                            $(html).appendTo($eventCont);
                        }
                    });

                }
            },

            /**
             * DAY LAYOUT
             */
            day: {

                bindControls: function () {

                },
                set: function () {
                    $instance.$day.show();
                    events.eventHolder.open();
                }

            },

            /**
             * GRID
             */
            grid: {
                cache: {
                    getNfutureEvents: false
                },

                columns: 0,
                options: {
                    offset: 0,
                    perClick: 4,
                    blockMinWidth: 270,
                    defaultColumns: 4,
                    gutter: 10
                },

                bindControls: function () {

                    this.options.defaultColumns = parseInt(glob.options.general_settings.grid_columns, 10);
                    this.options.gutter = parseInt(glob.options.general_settings.grid_gutter, 10);
                    this.options.perClick = parseInt(glob.options.general_settings.grid_per_click, 10);

                    var parent = this;

                    $instance.$grid.find('.stec-layout-grid-events-all-load-more').on(helper.clickHandle(), function (e) {
                        e.preventDefault();
                        parent.fill();
                    });

                    helper.onResizeEnd(function () {
                        parent.resizeEvents();
                    }, 50);

                },

                set: function () {

                    $instance.$grid.show();
                    this.clear();
                    this.fill();

                },

                clear: function () {
                    this.cache.getNfutureEvents = false;
                    $instance.$grid.find('.stec-layout-grid-events').empty();
                    $instance.$grid.find('.stec-layout-grid-noevents').hide();

                },

                getNfutureEvents: function () {

                    if ( !this.cache.getNfutureEvents ) {
                        // Load cache

                        this.getNfutureEvents.n = this.options.perClick;

                        if ( glob.options.general_settings.reverse_agenda_list == '1' ) {
                            this.cache.getNfutureEvents = $(calData.getFutureEvents()).get().reverse();
                        } else {
                            this.cache.getNfutureEvents = calData.getFutureEvents();
                        }

                        this.getNfutureEvents.i = 0;
                    }

                    var x = this.getNfutureEvents.i++ * this.getNfutureEvents.n;
                    var y = x + this.getNfutureEvents.n;

                    return this.cache.getNfutureEvents.slice(x, y);

                },

                fill: function () {

                    var events = this.getNfutureEvents();

                    if ( !events || events.length <= 0 ) {

                        // no events
                        $instance.$grid.find('.stec-layout-grid-events-all-control').hide();

                        return;
                    }

                    $(events).each(function () {

                        var event = this;

                        var d = helper.dbDateTimeToDate(this.start_date);

                        if ( now > d ) {
                            var noReminder = true;
                        }

                        var featured_class = '';

                        switch ( parseInt(this.featured, 10) ) {
                            case 1:
                                featured_class = ' stec-event-featured ';
                                break;

                            case 2:
                                featured_class = ' stec-event-featured stec-event-featured-bg ';
                                break;

                            default:
                                featured_class = '';
                        }

                        var additional_class = "";

                        if ( event.icon == 'fa' ) {
                            additional_class += ' stec-no-icon ';
                        }

                        if ( event.images_meta.length > 0 ) {
                            additional_class += ' stec-has-image';
                        }

                        if ( event.products.length > 0 ) {
                            additional_class += ' stec-has-products';
                        }

                        if ( event.location != '' ) {
                            additional_class += ' stec-has-location';
                        }

                        if ( event.guests.length > 0 ) {
                            additional_class += ' stec-has-guests';
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

                        if ( invited_user ) {
                            additional_class += ' stec-user-invited';
                        }

                        var start_date = helper.dbDateOffset(event.start_date, event.repeat_time_offset);
                        var end_date = helper.dbDateOffset(event.end_date, event.repeat_time_offset);
                        var now = helper.getCalNow(parseInt(event.timezone_utc_offset, 10) / 3600);
                        var endDate = helper.dbDateTimeToDate(end_date);

                        if ( now > start_date ) {
                            if ( now >= endDate ) {
                                additional_class += ' stec-expired';
                            } else {
                                additional_class += ' stec-in-progress';
                            }
                        }

                        $(glob.template.gridevent)
                                .addClass(featured_class)
                                .addClass(additional_class)
                                .addClass(noReminder ? 'stec-layout-event-no-reminder' : '')
                                .attr('data-id', event.id)
                                .attr('data-repeat-time-offset', event.repeat_time_offset ? event.repeat_time_offset : 0)
                                .html(function (index, html) {

                                    var date = helper.beautifyTimespan(event.start_date, event.end_date, event.all_day);

                                    var gmtutc_offset = parseInt(event.timezone_utc_offset, 10) / 3600;
                                    gmtutc_offset = gmtutc_offset > 0 ? '+' + gmtutc_offset : gmtutc_offset;

                                    if ( gmtutc_offset == 0 ) {
                                        gmtutc_offset = '';
                                    }

                                    var timezoneOffsetLabel = glob.options.general_settings.date_label_gmtutc == 0 ? '' : 'UTC/GMT ' + gmtutc_offset;

                                    var guestString = '';

                                    if ( event.guests.length > 0 ) {
                                        guestString = event.guests[0].name;

                                        if ( event.guests.length > 1 ) {
                                            guestString += ' +' + (event.guests.length - 1) + ' ' + (event.guests.length - 1 > 1 ? stecLang.MorePlural : stecLang.MoreSingular);
                                        }
                                    }

                                    var productsString = '';

                                    if ( event.products.length > 0 ) {
                                        productsString = event.products[0].title;
                                        if ( event.products.length > 1 ) {
                                            productsString += ' +' + (event.products.length - 1) + ' ' + (event.products.length - 1 > 1 ? stecLang.MorePlural : stecLang.MoreSingular);
                                        }
                                    }

                                    return html
                                            .replace(/#stec_replace_permalink/g, event.permalink + (event.repeat_time_offset > 0 ? event.repeat_time_offset : ''))
                                            .replace('stec_replace_summary', event.title)
                                            .replace('stec_replace_date', date + ' ' + timezoneOffsetLabel)
                                            .replace('stec_replace_image', 'style="background-image:url(' + (event.images_meta.length > 0 ? event.images_meta[0].src : '') + ')"')
                                            .replace('stec_replace_event_background', 'style="background:' + event.color + '"') // edge is retarded
                                            .replace('stec_replace_guest_image', 'style="background-image: url(' + (event.guests.length > 0 ? event.guests[0].photo_full : '') + ')"')
                                            .replace('stec_replace_guest_name', guestString)
                                            .replace('stec_replace_product_name', productsString)
                                            .replace('stec_replace_icon_class', event.icon)
                                            .replace('stec_replace_location', event.location)
                                            .replace('stec_replace_short_desc', event.description_short)
                                            .replace('#stec-replace-edit-link', 'admin.php?page=stec_menu__events&view=edit&calendar_id=' +
                                                    event.calid + '&event_id=' + event.id);

                                }).appendTo($instance.$grid.find('.stec-layout-grid-events'));

                    });

                    this.resizeEvents();
                },

                resizeEvents: function () {

                    var parent = this;

                    parent.columns = parent.options.defaultColumns;

                    while ( ($instance.$grid.find('.stec-layout-grid-events').width() - ((parent.columns - 1) * parent.options.gutter)) / parent.columns < parent.options.blockMinWidth ) {
                        parent.columns--;

                        if ( parent.columns === 1 ) {
                            break;
                        }
                    }

                    $instance.$grid.find('.stec-layout-grid-event').css({
                        width: function () {
                            var val = ($instance.$grid.find('.stec-layout-grid-events').outerWidth() - ((parent.columns - 1) * parent.options.gutter)) / parent.columns;
                            return Math.floor(val);
                        }
                    });

                    this.positionEvents();
                },

                positionEvents: function () {

                    var parent = this;
                    var maxHeight = 0;
                    var currentRow = 0;
                    var currentColumn = 0;

                    $instance.$grid.find('.stec-layout-grid-event').each(function (i) {

                        if ( i > 0 && i % parent.columns === 0 ) {
                            currentRow++;
                            currentColumn = 0;
                        }

                        var x = 0, y = 0;

                        if ( currentColumn > 0 ) {
                            var $prev = $(this).prev();
                            x = $prev.outerWidth() + $prev.position().left + parent.options.gutter;
                        }

                        if ( currentRow > 0 ) {
                            var $prev = $instance.$grid.find('.stec-layout-grid-event').eq(i - parent.columns);
                            y = $prev.outerHeight() + $prev.position().top + parent.options.gutter;
                        }

                        $(this).css({
                            top: y,
                            left: x
                        });

                        var currentHeight = y + $(this).outerHeight() + parent.options.gutter;

                        maxHeight = Math.max(currentHeight, maxHeight);

                        currentColumn++;
                    });

                    $instance.$grid.find('.stec-layout-grid-events').height(maxHeight);
                }
            }

        };

        /**
         * handles events
         */
        var events = {

            init: function () {
                this.bindControls();
            },

            bindControls: function () {

                var parent = this;

                // activate anchors for tabs content
                $(document).on(helper.clickHandle(), $instance.$events.path + (" a"), function (e) {
                    e.stopPropagation();
                    return true;
                });

                // excludes .create-form toggle
                // .stec-event-create-form is handled by adds/event.create.js
                $(document).on(helper.clickHandle(), $instance.$events.path + (".stec-layout-event-preview-right-event-toggle:not('.stec-layout-event-create-form-preview-right-event-toggle')"), function (e) {

                    e.preventDefault();
                    e.stopPropagation();

                    var $event = $(this).parents(".stec-layout-event");
                    parent.eventToggle($event);

                });

                // excludes .create-form toggle, .stec-layout-event-awaiting-approval
                // .stec-event-create-form is handled by adds/event.create.js
                $(document).on(helper.clickHandle(), $instance.$events.path + (".stec-layout-event:not('.stec-event-create-form, .stec-layout-event-awaiting-approval')"), function (e) {
                    e.preventDefault();

                    var $event = $(this);
                    parent.eventToggle($event);
                });

                // Prevent toggling from inner content
                $(document).on(helper.clickHandle(), $instance.$events.path + '.stec-layout-event-inner', function (e) {
                    e.stopPropagation();
                });

                $(document).on(helper.clickHandle(), $instance.$events.path + (".stec-layout-event-inner-top-tabs li"), function (e) {
                    e.preventDefault();
                    parent.activateTab($(this));
                });

                $(document).on(helper.clickHandle(), $instance.$events.path + (".stec-layout-event-preview-left-reminder-toggle"), function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    parent.attachReminder(this);
                });

                $(document).on(helper.clickHandle(), $instance.$events.path + (".stec-layout-event-preview-reminder"), function (e) {
                    e.stopPropagation();
                });

                $(document).on(helper.clickHandle(), $instance.$events.path + (".stec-layout-event-preview-right-menu"), function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    parent.attachReminder(this);
                });

                $(document).on(helper.clickHandle(), $instance.$events.path + (" .stec-layout-event-preview-reminder input"), function (e) {
                    e.stopPropagation();
                });

                $(document).on(helper.clickHandle(), $instance.$events.path + (".stec-layout-event-preview-reminder-units-selector li"), function (e) {
                    e.preventDefault();

                    var value = $(this).attr('data-value');
                    var text = $(this).text();

                    $(this).parents('.stec-layout-event-preview-reminder-units-selector')
                            .find('p')
                            .attr('data-value', value)
                            .text(text);
                });

                $(document).on(helper.clickHandle(), $instance.$events.path + (".stec-layout-event-preview-remind-button"), function (e) {

                    e.preventDefault();

                    var $event = $(this).parents('.stec-layout-event');
                    var $form = $(this).parents('ul:first');

                    var eventId = $event.attr('data-id');
                    var repeat_offset = $event.attr('data-repeat-time-offset');
                    var email = $form.find('input[name="email"]').val();
                    var number = $form.find('input[name="number"]').val();
                    var units = $form.find('p[data-value]').attr('data-value');

                    if ( helper.isEmail(email) && number != '' ) {
                        reminder.remindEvent(eventId, repeat_offset, email, number, units);
                    }

                });

            },

            /**
             * Attached the reminder template to (this) element
             */
            attachReminder: function (th) {

                var leftSided = false;

                if ( $instance.hasClass('stec-media-small') ) {
                    leftSided = true;
                }

                $instance.$events.find(".stec-layout-event-preview-left-reminder-toggle").not(th).removeClass("active");
                $instance.$events.find(".stec-layout-event-preview-right-menu").not(th).removeClass("active");
                $(th).toggleClass("active");

                $(window).unbind('resize.' + 'reminder-' + glob.options.id);
                $instance.find('.stec-layout-event-preview-reminder').remove();

                function position() {

                    // remove if button not visible anymore 
                    if ( !$(th).is(":visible") ) {
                        $(th).removeClass('active');
                        $(window).unbind('resize.' + 'reminder-' + glob.options.id);
                        $instance.find('.stec-layout-event-preview-reminder').remove();

                        return;
                    }

                    $instance.find('.stec-layout-event-preview-reminder').css({

                        left: function () {

                            if ( leftSided === true ) {

                                return 'initial'; // should be 0 but iPhone is ...

                            } else {

                                return $(th).position().left - $instance.find('.stec-layout-event-preview-reminder').width() + $(th).width() / 2 + 3;

                            }

                        },
                        top: function () {

                            if ( leftSided === true ) {

                                return $(th).position().top - $(th).height() - 12 - $instance.find('.stec-layout-event-preview-reminder').height();

                            } else {

                                return $(th).position().top - $instance.find('.stec-layout-event-preview-reminder').height() - 10;

                            }

                        }
                    });

                    if ( leftSided === true ) {
                        // sets the bottom arrow to the left side
                        $instance.find('.stec-layout-event-preview-reminder').addClass('stec-layout-event-preview-reminder-left');

                    }
                }

                if ( $(th).hasClass('active') ) {
                    $(glob.template.reminder).appendTo($(th).parents('.stec-layout-event').first());

                    helper.onResizeEnd(function () {
                        position();
                    }, 10, 'reminder-' + glob.options.id);

                    position();
                }

            },

            /**
             * Hides tab if event has no data for this tab
             * @param {type} $event the event jquery html object
             * @todo automate
             */
            setEventTabs: function ($event) {

                // Remove unused tabs
                var event = calData.getEventById($event.attr('data-id'));
                var tabs = 0;

                // comments
                if ( event.comments == 0 ) {
                    $event.find('.stec-layout-event-inner-top-tabs [data-tab="stec-layout-event-inner-comments"]').hide();
                } else {
                    tabs++;
                }

                // location
                if ( !event.location ) {
                    $event.find('.stec-layout-event-inner-top-tabs [data-tab="stec-layout-event-inner-location"]').hide();
                } else {
                    tabs++;
                }

                // forecast
                if ( !event.location_forecast ) {
                    $event.find('.stec-layout-event-inner-top-tabs [data-tab="stec-layout-event-inner-forecast"]').hide();
                } else {
                    tabs++;
                }

                // schedule
                if ( event.schedule.length <= 0 ) {
                    $event.find('.stec-layout-event-inner-top-tabs [data-tab="stec-layout-event-inner-schedule"]').hide();
                } else {
                    tabs++;
                }

                // guests
                if ( event.guests.length <= 0 ) {
                    $event.find('.stec-layout-event-inner-top-tabs [data-tab="stec-layout-event-inner-guests"]').hide();
                } else {
                    tabs++;
                }

                // attendance
                if ( event.attendance.length <= 0 ) {
                    $event.find('.stec-layout-event-inner-top-tabs [data-tab="stec-layout-event-inner-attendance"]').hide();
                } else {
                    tabs++;
                }

                // woocommerce
                if ( event.products.length <= 0 ) {
                    $event.find('.stec-layout-event-inner-top-tabs [data-tab="stec-layout-event-inner-woocommerce"]').hide();
                } else {
                    tabs++;
                }

                if ( tabs < 1 ) {
                    $event.find('.stec-layout-event-inner-top-tabs').hide();
                }
            },

            eventToggle: function ($event) {

                if ( glob.options.general_settings.open_event_in == 'single' ) {
                    window.location.href = $event.find('.stec-layout-event-single-page-link').attr('href');
                    return false;
                }

                if ( !$event.hasClass("active") ) {

                    // Add inner content
                    if ( $event.find(".stec-layout-event-inner").length <= 0 ) {
                        $(glob.template.eventInner).appendTo($event);
                    }

                    // Set active 
                    $event
                            .addClass("active")
                            .find(".stec-layout-event-preview-right-event-toggle").addClass("active");

                    var $siblings = $instance.$events.find(".stec-layout-event").not($event);

                    $siblings
                            .removeClass("active")
                            .find(".stec-layout-event-preview-right-event-toggle")
                            .removeClass("active");


                    this.setEventTabs($event);

                    // Load first tab if no tab is active
                    if ( $event.find('.stec-layout-event-inner-top-tabs').children('li.active').length <= 0 ) {
                        $event.find('.stec-layout-event-inner-top-tabs').children('li').first().trigger(helper.clickHandle());
                    }

                    var event = calData.getEventById($event.attr('data-id'));

                    $instance.trigger('onEventToggleOpen', {
                        $instance: $instance,
                        event: event,
                        glob: glob,
                        helper: helper,
                        repeatOffset: $event.attr('data-repeat-time-offset')
                    });

                    // Focus on event
                    helper.focus($event);


                } else {

                    $event
                            .removeClass("active")
                            .find(".stec-layout-event-preview-right-event-toggle").removeClass("active");
                }


            },

            activateTab: function ($tab) {

                if ( $tab.hasClass("active") ) {
                    return;
                }

                $tab.addClass("active")
                        .siblings()
                        .removeClass("active");

                // remove only children active
                $tab
                        .parents(".stec-layout-event-inner")
                        .find(".stec-layout-event-inner-top-tabs-content")
                        .children('.active')
                        .removeClass("active");


                var tabClass = "." + $tab.attr("data-tab");

                $tab
                        .parents(".stec-layout-event-inner")
                        .find(tabClass)
                        .addClass("active");

                $(document).trigger('stec-tab-click-' + glob.options.id);

            },

            eventHolder: {

                getEventHolder: function () {

                    var $eventHolder = "";

                    switch ( glob.options.view ) {

                        case "agenda":
                            $eventHolder = $instance.$agenda.find(".stec-event-holder");
                            break;

                        case "month":
                            $eventHolder = $instance.$month.find(".stec-event-holder");
                            break;

                        case "week":
                            $eventHolder = $instance.$week.find(".stec-event-holder");
                            break;

                        case "day":
                            $eventHolder = $instance.$day.find(".stec-event-holder");
                            break;
                    }

                    return $eventHolder;
                },

                removeEvents: function () {

                    // prevents duplicate triggers  
                    $(document).unbind('stec-tab-click-' + glob.options.id);

                    // resize.stec-unbind-window-resize-on-event-close
                    // prevents duplicate triggers 
                    $(window).unbind('resize.stec-unbind-window-resize-on-event-close-' + glob.options.id);

                    $instance.$events.not('.stec-layout-agenda-events-all').children().remove();
                },

                /**
                 * Displays events for active date
                 * @returns bool true of false if no events
                 */
                displayEvents: function () {

                    var d = new Date(glob.options.year, glob.options.month, glob.options.day);

                    var events = calData.getEvents(d, false);

                    if ( events.length <= 0 ) {
                        // no events
                        return false;
                    }

                    var now = new Date();

                    $(events).each(function () {

                        var event = this;

                        // stec-layout-event-preview-right-menu

                        var startDate = new Date(event.start_date_timestamp * 1000);

                        if ( now > startDate ) {
                            var noReminder = true;
                        }

                        var date = helper.beautifyTimespan(event.start_date, event.end_date, event.all_day);

                        var gmtutc_offset = parseInt(event.timezone_utc_offset, 10) / 3600;
                        gmtutc_offset = gmtutc_offset > 0 ? '+' + gmtutc_offset : gmtutc_offset;

                        if ( gmtutc_offset == 0 ) {
                            gmtutc_offset = '';
                        }

                        var timezoneOffsetLabel = glob.options.general_settings.date_label_gmtutc == 0 ? '' : 'UTC/GMT ' + gmtutc_offset;

                        var featured_class = '';

                        switch ( parseInt(this.featured, 10) ) {
                            case 1:
                                featured_class = ' stec-event-featured ';
                                break;

                            case 2:
                                featured_class = ' stec-event-featured stec-event-featured-bg ';
                                break;

                            default:
                                featured_class = '';
                        }

                        var additional_class = "";

                        if ( event.icon == 'fa' ) {
                            additional_class += ' stec-no-icon ';
                        }

                        $(glob.template.event)
                                .addClass(featured_class)
                                .addClass(additional_class)
                                .addClass(noReminder ? 'stec-layout-event-no-reminder' : '')
                                .attr('data-id', event.id)
                                .attr('data-repeat-time-offset', event.repeat_time_offset ? event.repeat_time_offset : 0)
                                .html(function (index, html) {

                                    html += '<a class="stec-layout-event-single-page-link" href="' + event.permalink + (event.repeat_time_offset > 0 ? event.repeat_time_offset : '') + '">' + event.title + '</a>';

                                    return html
                                            .replace('stec_replace_summary', event.title)
                                            .replace('stec_replace_date', date + ' ' + timezoneOffsetLabel)
                                            .replace('stec_replace_event_background', ' style="background:' + event.color + '" ') // edge is retarded
                                            .replace('stec_replace_icon_class', event.icon)
                                            .replace('#stec-replace-edit-link', 'admin.php?page=stec_menu__events&view=edit&calendar_id=' +
                                                    event.calid + '&event_id=' + event.id);

                                }).appendTo($instance.$events.not('.stec-layout-agenda-events-all'));

                    });

                    // Remove + when single pages
                    if ( glob.options.general_settings.open_event_in == 'single' ) {
                        $instance.$events
                                .not('.stec-layout-agenda-events-all')
                                .find('.stec-layout-event-preview-right-event-toggle')
                                .remove();
                    }

                    return true;
                },

                close: function () {

                    $instance.$events
                            .not('.stec-layout-agenda-events-all')
                            .find(".active")
                            .removeClass("active");

                    this.getEventHolder().hide();

                    // last visible children fix for border-radius
                    if ( glob.options.view == "month" ) {
                        $instance.$month.find(".stec-layout-month-weekrow").last().addClass("stec-layout-month-weekrow-last");
                    }

                    if ( glob.options.view == "week" ) {
                        $instance.$week.find(".stec-layout-week-weekrow").addClass("stec-layout-week-weekrow-last");
                    }

                    helper.extendBind("stachethemes_ec_extend", "onEventHolderClose");

                    // Remove inner content
                    this.removeEvents();
                },

                open: function () {

                    this.close();

                    var result = this.displayEvents();

                    // Day layout specifics
                    if ( glob.options.view == 'day' ) {

                        if ( result === false ) {
                            $instance.$day.find('.stec-layout-day-noevents').show();
                        } else {
                            $instance.$day.find('.stec-layout-day-noevents').hide();
                        }

                    }

                    // If create form is disabled and there are no events do not open the event holder
                    if ( result === false && glob.options.general_settings.show_create_event_form == '0' ) {
                        return;
                    }

                    // If there is event holder...

                    // Month layout specifics
                    if ( glob.options.view == "month" ) {

                        // last visible children fix...

                        if ( $instance.$month.find(".stec-layout-month-eventholder").is(":last-child") ) {
                            $instance.$month.find(".stec-layout-month-weekrow-last").removeClass("stec-layout-month-weekrow-last");
                        } else {
                            $instance.$month.find("tr").last().addClass("stec-layout-month-weekrow-last");
                        }

                        // focus on event
                        helper.focus($instance.$month.find('.stec-layout-month-daycell.active'));
                    }

                    // Week layout specifics
                    if ( glob.options.view == "week" ) {
                        $instance.$week.find(".stec-layout-week-weekrow-last").removeClass("stec-layout-week-weekrow-last");
                    }

                    helper.extendBind("stachethemes_ec_extend", "onEventHolderOpen");

                    if ( helper.animate ) {
                        helper.animate.eventHolder.open(this.getEventHolder());
                    } else {
                        this.getEventHolder().show();
                    }


                }
            }
        };

        var calData = {

            featuredOnly: false,
            calendarFilter: [],
            calendarsPool: [],
            eventsPool: [],

            /**
             * Returns event by id
             * 
             * @param {int} event_id
             * @returns event object
             * @todo add params for time offset
             */
            getEventById: function (event_id) {

                var event = [];

                $(calData.eventsPool).each(function () {
                    if ( this.id == event_id ) {
                        event = this;
                        return false; // break;
                    }
                });

                return event;
            },

            // sort by featured
            // @todo recheck solution...
            sortByFeatured: function (a, b) {
                if ( a.featured != 0 && b.featured != 0 ) {
                    return a.start_date_timestamp - b.start_date_timestamp;
                } else {
                    return b.featured - a.featured;
                }
            },

            // sort by timestamp oldest -> newest
            sortByTimestamp: function (a, b) {
                if ( a.start_date_timestamp < b.start_date_timestamp )
                    return -1;
                else if ( a.start_date_timestamp > b.start_date_timestamp )
                    return 1;
                else
                    return 0;
            },

            removeFromEventsPool: function (eventId) {

                var newPool = []; // probably the safest way to do it...

                $(this.eventsPool).each(function (i) {

                    if ( this.id != eventId ) {

                        newPool.push(this);
                    }

                });

                this.eventsPool = newPool;
            },

            addToEventsPool: function (events) {

                $(events).each(function () {

                    if ( '1' == glob.options.general_settings.date_in_user_local_time && this.all_day != 1 ) {

                        // Store original unixtimestamp before offset
                        this.start_date_timestamp_tz = window.moment(this.start_date).unix();
                        this.end_date_timestamp_tz = window.moment(this.end_date).unix();

                        // Add localtime offset
                        this.start_date = window.moment(this.start_date)
                                .add(this.timezone_utc_offset, 'seconds')
                                .format('YYYY-MM-DD HH:mm:ss');

                        this.end_date = window.moment(this.end_date)
                                .add(this.timezone_utc_offset, 'seconds')
                                .format('YYYY-MM-DD HH:mm:ss');
                    }

                    this.start_date_timestamp = window.moment(this.start_date).unix();
                    this.end_date_timestamp = window.moment(this.end_date).unix();

                });

                this.eventsPool = this.eventsPool.concat(events);

                helper.extendBind("stachethemes_ec_extend", "onAddToEventsPool", this.eventsPool);

            },

            addDataToEvent: function (data) {

                var parent = this;

                $(parent.eventsPool).each(function (i) {
                    if ( this.id == data.general.id ) {
                        this.data = data;
                    }
                });
            },

            pullEvents: function (callback) {

                var parent = this;

                glob.ajax = $.ajax({
                    dataType: "json",
                    type: 'POST',
                    url: window.ajaxurl,
                    data: {
                        action: 'stec_public_ajax_action',
                        cal: glob.options.cal ? glob.options.cal : '',
                        min_date: glob.options.min_date ? glob.options.min_date : null,
                        max_date: glob.options.max_date ? glob.options.max_date : null,
                        task: 'get_events'
                    },

                    beforeSend: function () {
                        if ( glob.ajax !== null ) {
                            glob.ajax.abort();
                        }
                    },

                    success: function (data) {
                        if ( data ) {
                            parent.addToEventsPool(data);
                        }
                    },

                    error: function (xhr, status, thrown) {
                        console.log(xhr + " " + status + " " + thrown);
                    },

                    complete: function () {
                        glob.ajax = null;

                        if ( typeof callback === "function" ) {
                            callback();
                        }
                    }
                });

            },

            /**
             * Pulls all events from now up to 12 months in the future, including repeated events
             */
            getFutureEvents: function () {

                var date = new Date(glob.options.year, glob.options.month, glob.options.day);

//              Agenda can now look expired events so comment these lines
//              var now     = new Date();
//              var a = date > now ? date : now;

                var a = date;
                var b = new Date(a);
                b.setMonth(b.getMonth() + 12);

                var events = calData.getEvents(a, b);

                return events;
            },

            /**
             * 
             * Return all events for given timespan
             * 
             * @param {date} startDate
             * @param {date} endDate
             * @returns {array}
             */
            getEvents: function (startDate, endDate, incAapproval) {

                var parent = this;

                if ( !incAapproval ) {
                    incAapproval = false;
                }

                if ( !startDate ) {
                    startDate = new Date(glob.options.year, glob.options.month, glob.options.day);
                }

                startDate.setHours(0);
                startDate.setMinutes(0);
                startDate.setSeconds(0);
                startDate.setMilliseconds(0);

                if ( endDate ) {
                    endDate.setHours(0);
                    endDate.setMinutes(0);
                    endDate.setSeconds(0);
                    endDate.setMilliseconds(0);
                } else {
                    endDate = new Date(startDate);
                }

                endDate.setHours(24);

                var a = helper.dateToUnixStamp(startDate);
                var b = helper.dateToUnixStamp(endDate);

                var pick = [];

                // Get overrides in this range and add them to "pick" array
                var overrides = calData.getOverridesInRange(a, b);
                if ( overrides.length > 0 ) {
                    parent.filterGetEvents = [];
                    parent.filterGetEvents = overrides;
                    helper.extendBind("stachethemes_ec_extend", "beforeProccessGetEvents");
                    overrides = parent.filterGetEvents;
                    $.merge(pick, parent.filterGetEvents);
                }

                // Exclude overriden
                parent.filterGetEvents = [];
                parent.filterGetEvents = $.grep(calData.eventsPool, function (poolEvent) {
                    return !poolEvent.recurrence_id;
                });

                helper.extendBind("stachethemes_ec_extend", "beforeProccessGetEvents");

                $(parent.filterGetEvents).each(function () {

                    var event_loop = this;

                    if ( incAapproval !== true && this.approved == '0' ) {
                        return true;
                    }

                    //  If repeating event get repeater
                    if ( this.rrule && this.rrule != '' ) {
                        event_loop = parent.repeater.get(this, a, b);
                    }

                    var now = window.moment().unix();

                    // Picker
                    $(event_loop).each(function () {

                        if ( overrides.length > 0 && calData.isOverriden(this) ) {
                            return true; // continue;
                        }

                        var start, end;

                        start = this.start_date_timestamp;
                        end = this.end_date_timestamp;

                        // if param expired_only=1 in [stachethemes_ec]
                        if ( glob.options.expired_only == '1' ) {
                            if ( start > now ) {
                                return true; // continue;
                            }
                        } else

                        // if param upcoming_only=1 in [stachethemes_ec]
                        if ( glob.options.upcoming_only == '1' ) {
                            if ( start < now ) {
                                return true; // continue;
                            }
                        }

                        if ( b > start && end >= a ) {
                            pick.push(this);
                        }
                    });

                });

                // reorder full events by timestamp
                parent.filterGetEvents = pick.sort(parent.sortByTimestamp);

                // reorder by featured
                if ( glob.options.view != 'agenda' && glob.options.view != 'grid' ) {
                    parent.filterGetEvents.sort(calData.sortByFeatured);
                }

                helper.extendBind("stachethemes_ec_extend", "beforeReturnGetEvents");

                return parent.filterGetEvents;
            },

            isOverriden: function (event) {

                var recurrence_id = moment.unix(event.start_date_timestamp).format('YMMDDTHmss');
                var overrides = calData.getOverrides(event.uid, recurrence_id);
                if ( overrides.length > 0 ) {
                    return true;
                }

                return false;

            },

            getOverridesInRange: function (a, b, filteredPool) {

                var pool = filteredPool ? filteredPool : this.eventsPool;

                return  $.grep(pool, function (event) {

                    if ( !event.recurrence_id ) {
                        return false;
                    }

                    if ( b > event.start_date_timestamp && event.end_date_timestamp >= a ) {
                        return true;
                    }

                    return false;
                });
            },

            /**
             * Looks for recurrence-id override
             * @param {string} uid 
             * @param {string} recurrence_id
             * 
             * The "recurrence_id" value must contain the override time in format YYYYMMDDTHHmmss
             * 
             * @return Array
             */
            getOverrides: function (uid, recurrence_id) {

                return  $.grep(this.eventsPool, function (poolEvent) {

                    if ( poolEvent.recurrence_id && poolEvent.recurrence_id.indexOf('Z') != -1 ) {

                        var m = new moment(poolEvent.recurrence_id);
                        var offset = 0;

                        if ( m.isDST() ) {
                            offset = -1 * ((poolEvent.timezone_utc_offset - 3600) / 3600);
                        } else {
                            offset = -1 * (poolEvent.timezone_utc_offset / 3600);
                        }

                        m.utcOffset(offset);

                        poolEvent.recurrence_id = m.format('YYYYMMDDTHHmmss');

                    }

                    return uid == poolEvent.uid && poolEvent.recurrence_id == recurrence_id;
                });
            },

            repeater: {

                get: function (event, rangeStart, rangeEnd) {

                    var eventStartDate = new Date((event.start_date_timestamp * 1000));
                    var rangeStartDate = new Date(rangeStart * 1000);
                    var rangeEndDate = new Date(rangeEnd * 1000);
                    var dtstartRFC = helper.dateToRFC(eventStartDate);
                    var rfcString = event.rrule + ';DTSTART=' + dtstartRFC;

                    if ( event.exdate ) {

                        var exdates = event.exdate.split(',');
                        var exdatesFixedArray = [];

                        $.each(exdates, function () {

                            if ( this.length === 8 ) {

                                var dt = moment(this);

                                exdatesFixedArray.push(dt
                                        .hours(eventStartDate.getHours())
                                        .minutes(eventStartDate.getMinutes())
                                        .seconds(0)
                                        .utc().format('YYYYMMDD\THHmmss') + 'Z');

                            } else {

                                exdatesFixedArray.push(this);

                            }
                        });

                        rfcString += '\nEXDATE:' + exdatesFixedArray.join(',');
                    }

                    var rruleSet = window.rrulestr(rfcString, {
                        forceset: true
                    });

                    var result = rruleSet.between(eventStartDate, rangeEndDate, true);
                    var r_events = [];

                    if ( result.length <= 0 ) {
                        return r_events;
                    }

                    $(result).each(function () {

                        var offset = helper.dateToUnixStamp(this) - event.start_date_timestamp;

                        // not by reference! need fresh copy
                        var r_event = JSON.parse(JSON.stringify(event));

                        r_event.start_date_timestamp = r_event.start_date_timestamp + offset;
                        r_event.end_date_timestamp = r_event.end_date_timestamp + offset;

                        r_event.start_date = helper.dateToDbDateTime(new Date(r_event.start_date_timestamp * 1000));
                        r_event.end_date = helper.dateToDbDateTime(new Date(r_event.end_date_timestamp * 1000));
                        r_event.repeat_time_offset = r_event.start_date_timestamp - event.start_date_timestamp;

                        if ( r_event.end_date_timestamp < rangeStart ) {
                            return true;
                        }

                        r_events.push(r_event);

                    });

                    return r_events;

                },

                getClosest: function (event, date) {

                    var eventStartDate = new Date((event.start_date_timestamp * 1000));

                    if ( !date ) {
                        var date = new Date();
                    }

                    var dtstartRFC = helper.dateToRFC(eventStartDate);
                    var rfcString = event.rrule + ';DTSTART=' + dtstartRFC;

                    if ( event.exdate ) {

                        var exdates = event.exdate.split(',');
                        var exdatesFixedArray = [];

                        $.each(exdates, function () {

                            if ( this.length === 8 ) {

                                var dt = moment(this);

                                exdatesFixedArray.push(dt
                                        .hours(eventStartDate.getHours())
                                        .minutes(eventStartDate.getMinutes())
                                        .seconds(0)
                                        .utc().format('YYYYMMDD\THHmmss') + 'Z');

                            } else {

                                exdatesFixedArray.push(this);

                            }
                        });

                        rfcString += '\nEXDATE:' + exdatesFixedArray.join(',');
                    }


                    var rruleSet = window.rrulestr(rfcString, {
                        forceset: true
                    });

                    var r_events = [];
                    var result = rruleSet.after(date, true);

                    if ( result.length <= 0 ) {
                        result = rruleSet.before(date, true);
                    }

                    if ( result.length <= 0 ) {
                        return r_events;
                    }

                    $(result).each(function () {

                        var offset = helper.dateToUnixStamp(this) - event.start_date_timestamp;

                        // not by reference! need fresh copy
                        var r_event = JSON.parse(JSON.stringify(event));

                        r_event.start_date_timestamp = r_event.start_date_timestamp + offset;
                        r_event.end_date_timestamp = r_event.end_date_timestamp + offset;

                        r_event.start_date = helper.dateToDbDateTime(new Date(r_event.start_date_timestamp * 1000));
                        r_event.end_date = helper.dateToDbDateTime(new Date(r_event.end_date_timestamp * 1000));
                        r_event.repeat_time_offset = r_event.start_date_timestamp - event.start_date_timestamp;

                        r_events.push(r_event);

                    });

                    return r_events;
                }
            }


        };

        var reminder = {

            blockAction: false,
            ajax: null,

            remindEvent: function (eventId, repeat_offset, email, number, units) {

                if ( this.blockAction === true ) {
                    return;
                }

                var parent = this;

                var event = calData.getEventById(eventId);

                var remindDate = parseInt(event.start_date_timestamp, 10) + (parseInt(repeat_offset, 10));

                remindDate = new Date(remindDate * 1000);

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

                var $menu = $instance.$events.find('.stec-layout-event-preview-right-menu.active');

                var $mini = $instance.$events.find('.stec-layout-event-preview-left-reminder-toggle.active');

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

                        $menu.find('> i').hide();
                        $(glob.template.preloader).appendTo($menu);

                        $mini.text(stecLang.setting);

                        parent.blockAction = true;

                    },
                    success: function (data) {

                        $menu.find('> i').show();

                        if ( data && data.error != 1 ) {
                            $menu.find('> i').removeClass('fa-bell').addClass('fa-check');
                            $mini.text(stecLang.remindrset);
                            $mini.addClass('stec-layout-event-preview-left-reminder-success');

                            setTimeout(function () {
                                $menu.find('> i').removeClass('fa-check').addClass('fa-bell');
                                $mini.text(stecLang.reminder);
                                $mini.removeClass('stec-layout-event-preview-left-reminder-success');
                            }, 3000);

                        } else {

                            $menu.find('> i').removeClass('fa-bell').addClass('fa-times');
                            $mini.text(stecLang.error);

                            setTimeout(function () {
                                $menu.find('> i').removeClass('fa-times').addClass('fa-bell');
                                $mini.text(stecLang.reminder);
                            }, 3000);

                        }

                    },
                    error: function (xhr, status, thrown) {

                        $menu.find('> i').removeClass('fa-bell').addClass('fa-times');

                        setTimeout(function () {
                            $menu.find('> i').removeClass('fa-times').addClass('fa-bell');
                        }, 3000);

                        console.log(xhr + " " + status + " " + thrown);
                    },
                    complete: function () {

                        reminder.ajax = null;

                        $menu.find('.stec-preloader').remove();

                        setTimeout(function () {
                            parent.blockAction = false;
                        }, 3000);

                    }
                });

            }

        };

    }

    $(document).ready(function () {

        // Set boostrap datetimepicker to no conflict mode
        if ( typeof $.fn.datepicker.noConflict === 'function' ) {
            $.fn.bootstrapDP = $.fn.datepicker.noConflict();
        }

        $(document).on('click', '.stec-fixed-message a', function (e) {
            e.preventDefault();

            $(this).parents('.stec-fixed-message').remove();
        });

        if ( typeof window.stachethemes_ec_instance !== "undefined" ) {
            $(window.stachethemes_ec_instance).each(function () {

                var stec = new stachethemesEventCalendar();
                stec.init(this);

            });
        }


    });

})(jQuery);