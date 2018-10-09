(function ($) {

    "use strict";

    $.stecExtend(function (master) {

        var $instance = master.$instance;
        var helper = master.helper;
        var glob = master.glob;
        var layout = master.layout;
        var calData = master.calData;

        /**
         * Top Search form handling
         */
        var search = {

            init: function () {
                this.bindControls();
            },

            bindControls: function () {

                var parent = this;

                var suggestionTimeout;

                $instance.$top.find(".stec-top-search-dropdown").on("keyup", function (e) {

                    clearTimeout(suggestionTimeout);

                    var value = $instance.$top.find(".stec-top-search-form input").val();
                    var $lis = $instance.$top.find(".stec-top-search-results li");

                    switch ( e.which ) {

                        // esc
                        case 27 :
                            parent.closeSearch();
                            break;

                            // enter 
                        case 13 :

                            if ( $lis.filter(".active").length <= 0 ) {
                                parent.getResults(value);
                                return;
                            }

                            var $selected = $lis.filter(".active");

                            // Jump date check
                            if ( typeof $selected.attr("data-jumpdate") !== "undefined" ) {
                                parent.jumpToDate($selected);
                                parent.closeSearch();
                            }

                            break;

                            // up arrow
                        case 38 :
                            if ( $lis.filter(".active").length > 0 ) {
                                $lis.filter(".active")
                                        .removeClass("active")
                                        .prev()
                                        .addClass("active");
                            } else {
                                $lis.filter(":last").addClass("active");
                            }
                            break;

                            // down arrow
                        case 40 :
                            if ( $lis.filter(".active").length > 0 ) {
                                $lis.filter(".active")
                                        .removeClass("active")
                                        .next()
                                        .addClass("active");
                            } else {
                                $lis.filter(":first").addClass("active");
                            }
                            break;

                        default:
                            suggestionTimeout = setTimeout(function () {
                                parent.getResults(value);
                            }, 250);

                    }

                });

                // Result list click handle
                $(document).on(helper.clickHandle(), $instance.$top.path + " .stec-top-search-results li", function (e) {

                    var $lis = $instance.$top.find(".stec-top-search-results li");

                    $lis.removeClass("active");
                    $(this).addClass("active");

                    var $selected = $lis.filter(".active");

                    if ( typeof $selected.attr("data-jumpsingle") === "undefined" ) {
                        e.preventDefault();
                    }

                    // Jump date check
                    if ( typeof $selected.attr("data-jumpdate") !== "undefined" ) {
                        parent.jumpToDate($selected);
                        parent.closeSearch();
                    }

                });

                // Search button click handle
                $instance.$top.find(".stec-top-search-form a").on(helper.clickHandle(), function (e) {

                    e.preventDefault();

                    var value = $instance.$top.find("input").val();
                    parent.getResults(value);
                });

                // Search main button toggle show/hide
                $(document).on(helper.clickHandle(), $instance.$top.path + " .stec-top-menu-search", function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    $instance.$top.find('.stec-top-menu-filter-calendar').removeClass('active');

                    // fix for left offset since today button is not fixed width
                    var $dropdown = $(this).find('.stec-top-search-dropdown');

                    $dropdown.css({
                        left: -1 * Math.round($(this).position().left)
                    });

                    $(this).toggleClass("active");
                });

                // Block search toggle for inner content
                $(document).on(helper.clickHandle(), $instance.$top.path + " .stec-top-search-dropdown", function (e) {
                    // preventDefault blocks mobile keyboard
                    e.stopPropagation();
                });

            },

            /**
             * jump to $selected data-jumpdate attribute
             * data-jumpdate="yyyy-mm-dd"
             * @param {object} $selected $(element)
             */
            jumpToDate: function ($selected) {

                var date = helper.getDateFromData($selected.attr("data-jumpdate"));

                glob.options.year = date.getFullYear();
                glob.options.month = date.getMonth();
                glob.options.day = date.getDate();

                layout.set();

            },

            closeSearch: function () {
                this.resetResults();
                $instance.$top.find(".stec-top-menu-search").removeClass("active");
            },

            resetResults: function () {
                $instance.$top.find('.stec-top-search-dropdown-noresult').hide();
                $instance.$top.find(".stec-top-search-results").empty();
            },

            escapeHtml: function (string) {

                var entityMap = {
                    "&": "&amp;",
                    "<": "&lt;",
                    ">": "&gt;",
                    '"': '&quot;',
                    "'": '&#39;',
                    "/": '&#x2F;'
                };

                return String(string).replace(/[&<>"'\/]/g, function (s) {
                    return entityMap[s];
                });
            },

            getResults: function (keyword) {

                var parent = this;

                parent.resetResults();

                keyword = $.trim(keyword);

                if ( keyword == "" ) {
                    return;
                }

                var keywordArray = keyword.split(" ");

                var WORD = {
                    generic: false,
                    date: {
                        year: false,
                        month: false,
                        day: false
                    }
                };

                $(keywordArray).each(function () {

                    // Start as Generic word
                    var generic = true;

                    if ( parent.isYear(this) ) {
                        WORD.date.year = this;
                        generic = false;
                    }

                    if ( parent.isMonthString(this) ) {
                        var monthName = this;

                        $(glob.options.monthLabels).each(function (monthNumber) {
                            if ( this.toLowerCase() == monthName.toLowerCase() ) {
                                WORD.date.month = monthNumber;
                                generic = false;
                            }
                        });

                        if ( WORD.date.month === false ) {
                            // check shortname
                            $(glob.options.monthLabelsShort).each(function (monthNumber) {
                                if ( this.toLowerCase() == monthName.toLowerCase() ) {
                                    WORD.date.month = monthNumber;
                                    generic = false;
                                }
                            });
                        }
                    }

                    if ( parent.isDay(this) ) {
                        WORD.date.day = this;
                        generic = false;
                    }

                    // NO Func Match Found
                    if ( generic === true ) {
                        WORD.generic = true;
                    }

                });

                // Not a generic word
                if ( WORD.generic === false ) {
                    if ( WORD.date.year !== false || WORD.date.month !== false || WORD.date.day !== false ) {

                        if ( WORD.date.year === false ) {
                            WORD.date.year = glob.options.year;
                        }
                        if ( WORD.date.month === false ) {
                            WORD.date.month = glob.options.month;
                        }
                        if ( WORD.date.day === false ) {
                            WORD.date.day = glob.options.day;
                        }

                        // Suggest navigate to Date

                        var dateData = WORD.date.year + "-" + WORD.date.month + "-" + WORD.date.day;
                        var searchDate = new Date(WORD.date.year, WORD.date.month, WORD.date.day);

                        var dateString = helper.beautifyTimespan(searchDate, searchDate, 1);

                        var html = '<li data-jumpDate="' + dateData + '"><i class="fa fa-calendar-check-o"></i> <p>' + dateString + '</p></li>';
                        $(html).appendTo($instance.$top.find(".stec-top-search-results"));

                    }

                } else {
                    // Generic
                    // Search for keywords

                    var result = [];

                    $(keywordArray).each(function () {

                        var word = this;

                        if ( word.length <= 2 ) {
                            return true;
                        }

                        word = word.toLowerCase();

                        var eventsPool = [];

                        eventsPool = calData.eventsPool;

                        $(eventsPool).each(function () {

                            if ( calData.calendarFilter.indexOf(parseInt(this.calid, 10)) === -1 ) {
                                return; // continue loop
                            }

                            var summary = this.title.toLowerCase();
                            var location = this.location.toLowerCase();
                            var keywords = this.keywords.toLowerCase();

                            if ( keywords.indexOf(word) > -1

                                    || summary.indexOf(word) > -1

                                    || location.indexOf(word) > -1 ) {

                                if ( this.rrule ) {
                                    var closest = calData.repeater.getClosest(this);
                                    result.push(closest[0]);
                                } else {
                                    result.push(this);
                                }
                            }
                        });

                    });

                    if ( result.length > 0 ) {
                        // found match

                        var html = '';

                        $(result).each(function () {

                            var permalink = this.permalink;

                            if ( this.repeat_time_offset ) {
                                permalink += this.repeat_time_offset;
                            }

                            html += '<li data-jumpsingle><a href="' + permalink + '"><i class="' + this.icon + '"></i><p>' + this.title + '</p></a></li>';
                        });

                        $(html).appendTo($instance.$top.find(".stec-top-search-results"));

                    } else {
                        // no match found
                        $instance.$top.find('.stec-top-search-dropdown-noresult').show();
                    }

                }

            },

            isMonthString: function (keyword) {
                var found = false;
                if ( isNaN(keyword) ) {

                    if ( $.inArray(keyword.toLowerCase(), glob.options.monthLabels) !== -1 ) {
                        found = true;
                    }

                    if ( $.inArray(keyword.toLowerCase(), glob.options.monthLabelsShort) !== -1 ) {
                        found = true;
                    }

                }

                return found;
            },

            isDay: function (keyword) {
                var found = false;
                if ( !isNaN(keyword) ) {
                    // numbers
                    if ( keyword.length <= 2 ) {
                        if ( keyword <= 31 && keyword > 0 ) {
                            // use as day
                            found = true;
                        }
                    }
                }
                return found;
            },

            isYear: function (keyword) {
                var found = false;
                if ( !isNaN(keyword) ) {
                    // numbers
                    if ( keyword.length == 4 && keyword >= 1800 && keyword <= 2200 ) {
                        // use as year
                        found = true;
                    }
                }
                return found;
            }
        };

        search.init();

    });

})(jQuery);