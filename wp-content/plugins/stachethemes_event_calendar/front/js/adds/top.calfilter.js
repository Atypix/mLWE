(function ($) {

    "use strict";

    $.stecExtend(function (master, events) {

        var filtered = [];

        $(master.calData.filterGetEvents).each(function () {

            if ( master.calData.featuredOnly === true || master.glob.options.featured_only == '1' ) {

                if ( this.featured != '1' ) {
                    return true;
                }
            }

            if ( master.glob.options.filter_location ) {
                if ( this.location.toLowerCase() != master.glob.options.filter_location.toLowerCase() ) {
                    return true;
                }
            }

            if ( $.inArray(parseInt(this.calid, 10), master.calData.calendarFilter) != -1 ) {
                filtered.push(this);
            }

        });

        master.calData.filterGetEvents = filtered;

    }, 'beforeProccessGetEvents');

    $.stecExtend(function (master, eventsPool) {

        var calendars = [];

        /**
         * @todo better implementation of sort by unique key value ?
         */
        $(eventsPool).each(function () {

            var event = this;
            var pushed = false;

            $(calendars).each(function () {
                if ( this.id == event.calid ) {
                    pushed = true;
                }
            });

            if ( pushed === false ) {

                calendars.push({
                    id: event.calid,
                    title: event.calendar.title,
                    color: event.calendar.color
                });
            }
        });

        master.calData.calendarsPool = calendars;

        // build HTML for calendar filter list
        var html = '';

        if ( master.glob.options.featured_only == '1' ) {
            html += '<li class="active" data-featured="1" data-calid="0"><p><span></span>' + window.stecLang.featured_events + '</p></li>';
        } else {
            html += '<li data-featured="1" data-calid="0"><p><span></span>' + window.stecLang.featured_events + '</p></li>';
        }

        if ( master.calData.calendarsPool.length > 3 ) {
            html += '<li class="stec-select-all-calendars active"><p><span></span>' + window.stecLang.select_all + '</p></li>';
        }

        $(master.calData.calendarsPool).each(function () {

            // add to filter by default
            master.calData.calendarFilter.push(parseInt(this.id, 10));
            $.uniqueSort(master.calData.calendarFilter);

            html += '<li class="active" data-calid="' + this.id + '"><p><span style="background:' + this.color + '"></span>' + this.title + '</p></li>';
        });

        master.$instance.$top.find('.stec-top-menu-filter-calendar-dropdown').children('li').remove();

        $(html).appendTo(master.$instance.$top.find('.stec-top-menu-filter-calendar-dropdown'));


    }, 'onAddToEventsPool');

    $.stecExtend(function (master) {

        var $instance = master.$instance;
        var helper = master.helper;

        // Calendar filter button toggle show/hide
        $(document).on(helper.clickHandle(), $instance.$top.path + " .stec-top-menu-filter-calendar", function (e) {
            e.preventDefault();
            e.stopPropagation();

            $instance.$top.find('.stec-top-menu-search').removeClass('active');

            // fix for left offset since today button is not fixed width
            var $dropdown = $(this).find('.stec-top-menu-filter-calendar-dropdown');

            $dropdown.css({
                left: -1 * Math.round($(this).position().left)
            });

            $(this).toggleClass("active");
        });

        // Block filter toggle for inner content
        $(document).on(helper.clickHandle(), $instance.$top.path + " .stec-top-menu-filter-calendar-dropdown", function (e) {
            // preventDefault blocks mobile keyboard
            e.stopPropagation();
        });

        // Toggle active calendars
        $(document).on(helper.clickHandle(), $instance.$top.path + " .stec-top-menu-filter-calendar-dropdown li:not(.stec-select-all-calendars)", function (e) {

            e.preventDefault();

            // Disable click if featured is forced via shortcode parameter
            if ( master.glob.options.featured_only == '1' && $(this).attr('data-featured') ) {
                return false;
            }

            $(this).toggleClass('active');

            if ( $(this).hasClass('active') ) {

                // add to filter

                master.calData.calendarFilter.push(parseInt($(this).attr('data-calid'), 10));

                $.uniqueSort(master.calData.calendarFilter);

                if ( $(this).attr('data-featured') ) {
                    master.calData.featuredOnly = true;
                }

            } else {

                // remove from filter

                if ( $(this).attr('data-featured') ) {
                    master.calData.featuredOnly = false;
                }

                if ( master.calData.calendarFilter !== false ) {

                    var index = master.calData.calendarFilter.indexOf(parseInt($(this).attr('data-calid'), 10));

                    if ( index > -1 ) {
                        master.calData.calendarFilter.splice(index, 1);
                    }

                }

            }

            master.layout.set();

            // fix for left offset since today button is not fixed width
            $instance.$top.find('.stec-top-menu-filter-calendar-dropdown').css({
                left: -1 * Math.round($instance.$top.find(" .stec-top-menu-filter-calendar").position().left)
            });

        });

        $(document).on(helper.clickHandle(), $instance.$top.path + " .stec-top-menu-filter-calendar-dropdown li.stec-select-all-calendars", function (e) {

            e.preventDefault();

            $(this).toggleClass('active');

            if ( $(this).hasClass('active') ) {

                // add to filter

                $(this).nextAll('li').addClass('active').each(function () {

                    master.calData.calendarFilter.push($(this).attr('data-calid'));

                    $.uniqueSort(master.calData.calendarFilter);

                });


            } else {

                // remove from filter

                $(this).nextAll('li').removeClass('active').each(function () {

                    master.calData.calendarFilter = [];

                });

            }

            master.layout.set();

            // fix for left offset since today button is not fixed width
            $instance.$top.find('.stec-top-menu-filter-calendar-dropdown').css({
                left: -1 * Math.round($instance.$top.find(" .stec-top-menu-filter-calendar").position().left)
            });

        });


    });

})(jQuery);