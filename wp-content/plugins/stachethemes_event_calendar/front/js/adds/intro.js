(function ($) {

    "use strict";

    $(document).on('onEventToggleOpen', function (e, data) {

        var glob = data.glob;
        var $instance = data.$instance;
        var helper = data.helper;
        var event = data.event;

        var $event = $instance.$events.find('.stec-layout-event.active');
        var $inner = $event.find('.stec-layout-event-inner-intro');

        if ( $inner.length <= 0 ) {
            return;
        }

        var url = event.permalink;

        if ( data.repeatOffset > 0 ) {
            url += data.repeatOffset + '/';
        }

        // add the repeat offset
        var start_date = helper.dbDateOffset(event.start_date, data.repeatOffset);
        var end_date = helper.dbDateOffset(event.end_date, data.repeatOffset);

        var googleCalImportLink = helper.eventToGoogleCalImportLink(event.id, data.repeatOffset);

        $inner.html(function (index, html) {

            return html

                    .replace(/stec_replace_summary/g, event.title)
                    .replace(/stec_replace_description/g, (event.description))
                    .replace(/#stec_replace_link/g, event.link)
                    .replace(/#stec_replace_googlecal_import/g, googleCalImportLink)
                    .replace(/stec_replace_event_id/g, event.id)
                    .replace(/stec_replace_calendar_id/g, event.calid)
                    .replace(/#stec_replace_event_single_url/, url)
                    .replace(/stec_replace_event_single_url/g, url);
        });

        if ( event.link == "" ) {
            $inner.find('.stec-layout-event-inner-intro-external-link').hide();
        }

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

                if ( !event.images_meta || event.images_meta.length <= 0 ) {

                    $inner.find('.stec-layout-event-inner-intro-media').remove();

                    // Remove tab preloaders
                    $inner.find('.stec-layout-event-inner-preload-wrap').children().first().unwrap();
                    $inner.find('.stec-layout-event-inner-preload-wrap').remove();
                    $inner.find('.stec-preloader').remove();

                    return;
                }

                if ( event.images_meta.length == 1 ) {
                    $inner.find('.stec-layout-event-inner-intro-media-controls').remove();
                } else {
                    if ( event.images_meta.length < this.visCountBig ) {
                        this.visCountBig = event.images_meta.length;
                    }

                    if ( event.images_meta.length < this.visCountSmall ) {
                        this.visCountSmall = event.images_meta.length;
                    }
                }

                this.html();

                helper.imgLoaded($inner.find('.stec-layout-event-inner-intro-media-content img'), function () {

                    // Remove tab preloaders
                    $inner.find('.stec-layout-event-inner-preload-wrap').children().first().unwrap();
                    $inner.find('.stec-layout-event-inner-preload-wrap').remove();
                    $inner.find('.stec-preloader').remove();

                    setTimeout(function () {
                        parent.controlsDimensions();
                        parent.showImage();

                        $inner.find('.stec-layout-event-inner-intro-media').fadeTo(1000, 1);
                    }, 100);


                });

                this.bindControls();

            },
            bindControls: function () {

                var parent = this;

                // resize on tab click
                $(document).on('stec-tab-click-' + glob.options.id, function () {
                    parent.controlsDimensions();
                });

                helper.onResizeEnd(function () {
                    parent.controlsDimensions();
                }, 100, 'stec-unbind-window-resize-on-event-close-' + glob.options.id);

                $inner.find('.stec-layout-event-inner-intro-media-controls-next').on(helper.clickHandle(), function (e) {
                    e.preventDefault();
                    parent.slideNext();
                });

                $inner.find('.stec-layout-event-inner-intro-media-controls-prev').on(helper.clickHandle(), function (e) {
                    e.preventDefault();
                    parent.slidePrev();
                });

                $inner.find('.stec-layout-event-inner-intro-media-controls li').on(helper.clickHandle(), function (e) {

                    e.preventDefault();

                    if ( parent.cslide == $(this).index() ) {
                        return;
                    }

                    parent.cslide = $(this).index();

                    parent.showImage();

                });

            },
            html: function () {

                var html = '';

                $(event.images_meta).each(function () {

                    html += '<div style="background-image:url(' + this.src + ');">';
                    html += '   <img src="' + this.src + '" alt="' + this.alt + '">';

                    if ( this.caption != '' || this.description != '' ) {
                        html += '   <div>';

                        if ( this.caption != '' ) {
                            html += '       <p>' + this.caption + '</p>';
                        }

                        if ( this.description != '' ) {
                            html += '       <span>' + this.description + '</span>';
                        }

                        html += '   </div>';
                    }

                    html += '</div>';

                });

                $(html).appendTo($inner.find('.stec-layout-event-inner-intro-media-content'));

                html = '';

                $(event.images_meta).each(function () {
                    html += '<li style="background-image:url(' + this.thumb + ')">';
                    html += '</li>';
                });

                $(html).appendTo($inner.find('.stec-layout-event-inner-intro-media-controls-list'));

            },
            controlsDimensions: function () {

                var parent = this;

                if ( !$inner.is(':visible') ) {
                    return;
                }

                if ( $instance.hasClass('stec-media-small') ) {
                    parent.visCount = parent.visCountSmall;
                } else {
                    parent.visCount = parent.visCountBig;
                }

                if ( $inner.find('.stec-layout-event-inner-intro-media-controls-list li').length == parent.visCount ) {
                    $inner.find('.stec-layout-event-inner-intro-media-controls').addClass('no-side-controls');
                } else {
                    $inner.find('.stec-layout-event-inner-intro-media-controls').removeClass('no-side-controls');
                }

                var maxWidth = $inner.find('.stec-layout-event-inner-intro-media-controls-list-wrap').width();
                var $li = $inner.find('.stec-layout-event-inner-intro-media-controls-list li');

                $inner.find('.stec-layout-event-inner-intro-media-content').height($inner.find('.stec-layout-event-inner-intro-media img').first().height());

                // ~'calc( (100% - 2*10px) / 3 )';
                var liWidth = (maxWidth - ((this.visCount - 1) * 10)) / this.visCount;

                var listWidth = ($li.length * liWidth) + ($li.length * 10) - 10;

                $inner.find('.stec-layout-event-inner-intro-media-controls-list').width(listWidth);
                $li.width(liWidth);


                this.offset = 0;

                var left = -1 * ($li.first().width() * this.offset + this.offset * 10);

                $inner.find('.stec-layout-event-inner-intro-media-controls-list').stop().css({
                    left: left
                });


            },
            showImage: function () {

                $inner.find('.stec-layout-event-inner-intro-media-controls-list .active-thumb').removeClass('active-thumb');
                $inner.find('.stec-layout-event-inner-intro-media-controls-list li').eq(this.cslide).addClass('active-thumb');

                var $old = $inner.find('.stec-layout-event-inner-intro-media-content .active-image');
                var $new = $inner.find('.stec-layout-event-inner-intro-media-content > div').eq(this.cslide);


                var $textContent = $new.find('div');

                if ( $textContent.length > 0 ) {


                    $inner.find('.stec-layout-event-inner-intro-media-content-subs div').fadeTo(250, 0, function () {

                        var caption = $textContent.find('p').text();
                        var desc = $textContent.find('span').text();

                        $inner.find('.stec-layout-event-inner-intro-media-content-subs p').text(caption);
                        $inner.find('.stec-layout-event-inner-intro-media-content-subs span').text(desc);

                        var height = $inner.find('.stec-layout-event-inner-intro-media-content-subs p').height() + $inner.find('.stec-layout-event-inner-intro-media-content-subs span').height();

                        if ( height > 0 ) {
                            height = height + 40;
                        }

                        $inner.find('.stec-layout-event-inner-intro-media-content-subs').stop().animate({
                            height: height
                        }, {
                            duration: 400,
                            easing: 'stecExpo',
                            complete: function () {
                                $inner.find('.stec-layout-event-inner-intro-media-content-subs div').fadeTo(250, 1);
                            }
                        });
                    });


                } else {

                    $inner.find('.stec-layout-event-inner-intro-media-content-subs div').fadeTo(250, 0, function () {

                        $inner.find('.stec-layout-event-inner-intro-media-content-subs').stop().animate({
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

                var $li = $inner.find('.stec-layout-event-inner-intro-media-controls-list li');

                if ( this.offset + this.visCount >= $li.length ) {
                    this.offset = 0;
                } else {
                    this.offset = this.offset + this.visCount;

                    if ( this.offset > $li.length - this.visCount ) {
                        this.offset = $li.length - this.visCount;
                    }
                }


                var left = -1 * ($li.first().width() * this.offset + this.offset * 10);

                $inner.find('.stec-layout-event-inner-intro-media-controls-list').stop().animate({
                    left: left
                }, {
                    duration: 750,
                    easing: 'stecExpo'
                });

            },
            slidePrev: function () {

                var $li = $inner.find('.stec-layout-event-inner-intro-media-controls-list li');

                if ( this.offset <= 0 ) {
                    this.offset = $li.length - this.visCount;
                } else {
                    this.offset = this.offset - this.visCount;

                    if ( this.offset < 0 ) {
                        this.offset = 0;
                    }
                }


                var left = -1 * ($li.first().width() * this.offset + this.offset * 10);

                $inner.find('.stec-layout-event-inner-intro-media-controls-list').stop().animate({
                    left: left
                }, {
                    duration: 750,
                    easing: 'stecExpo'
                });

            }

        };

        slider.init();

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

                // Check counter disabled
                if ( event.counter != 1 ) {
                    $inner.find('.stec-layout-event-inner-intro-counter').hide();
                    return;
                }

                var nowDate = helper.getCalNow(parseInt(event.timezone_utc_offset, 10) / 3600);
                var startDate = helper.dbDateTimeToDate(start_date);

                var timeLeft = Math.floor((startDate.getTime() - nowDate.getTime()) / 1000);

                this.days = Math.floor(timeLeft / 86400);
                this.hours = Math.floor(timeLeft % 86400 / 3600);
                this.minutes = Math.floor(timeLeft % 86400 % 3600 / 60);
                this.seconds = Math.floor(timeLeft % 86400 % 3600 % 60);

                $inner.find('.stec-layout-event-inner-intro-counter-num').eq(0).text(this.days);
                $inner.find('.stec-layout-event-inner-intro-counter-num').eq(1).text(this.hours);
                $inner.find('.stec-layout-event-inner-intro-counter-num').eq(2).text(this.minutes);
                $inner.find('.stec-layout-event-inner-intro-counter-num').eq(3).text(this.seconds);

                this.daysLabel = $inner.find('.stec-layout-event-inner-intro-counter-label').eq(0);
                this.hoursLabel = $inner.find('.stec-layout-event-inner-intro-counter-label').eq(1);
                this.minutesLabel = $inner.find('.stec-layout-event-inner-intro-counter-label').eq(2);
                this.secondsLabel = $inner.find('.stec-layout-event-inner-intro-counter-label').eq(3);

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
                                $inner.find('.stec-layout-event-inner-intro-counter-num').eq(0).text(parent.days);
                                parent.daysLabel.text(parent.days == 1 ? parent.daysLabel.attr('data-singular-label') : parent.daysLabel.attr('data-plural-label'));
                            }
                            $inner.find('.stec-layout-event-inner-intro-counter-num').eq(1).text(parent.hours);
                            parent.hoursLabel.text(parent.hours == 1 ? parent.hoursLabel.attr('data-singular-label') : parent.hoursLabel.attr('data-plural-label'));
                        }
                        $inner.find('.stec-layout-event-inner-intro-counter-num').eq(2).text(parent.minutes);
                        parent.minutesLabel.text(parent.minutes == 1 ? parent.minutesLabel.attr('data-singular-label') : parent.minutesLabel.attr('data-plural-label'));
                    }
                    $inner.find('.stec-layout-event-inner-intro-counter-num').eq(3).text(parent.seconds);
                    parent.secondsLabel.text(parent.seconds == 1 ? parent.secondsLabel.attr('data-singular-label') : parent.secondsLabel.attr('data-plural-label'));


                    if ( parent.days == 0 && parent.hours == 0 && parent.minutes == 0 && parent.seconds == 0 ) {
                        clearInterval(parent.interval);
                        parent.complete();
                    }

                }, 1000);
            },
            complete: function () {

                $inner.find('.stec-layout-event-inner-intro-counter-num').eq(0).text(0);
                $inner.find('.stec-layout-event-inner-intro-counter-num').eq(1).text(0);
                $inner.find('.stec-layout-event-inner-intro-counter-num').eq(2).text(0);
                $inner.find('.stec-layout-event-inner-intro-counter-num').eq(3).text(0);

                this.daysLabel.text(this.days == 1 ? this.daysLabel.attr('data-singular-label') : this.daysLabel.attr('data-plural-label'));
                this.hoursLabel.text(this.hours == 1 ? this.hoursLabel.attr('data-singular-label') : this.hoursLabel.attr('data-plural-label'));
                this.minutesLabel.text(this.minutes == 1 ? this.minutesLabel.attr('data-singular-label') : this.minutesLabel.attr('data-plural-label'));
                this.secondsLabel.text(this.seconds == 1 ? this.secondsLabel.attr('data-singular-label') : this.secondsLabel.attr('data-plural-label'));

                $inner.find('.stec-layout-event-inner-intro-counter').hide();

                var now = helper.getCalNow(parseInt(event.timezone_utc_offset, 10) / 3600);
                var endDate = helper.dbDateTimeToDate(end_date);

                if ( now >= endDate ) {
                    $inner.find('.stec-layout-event-inner-intro-event-status-text.event-expired').show();
                } else {
                    $inner.find('.stec-layout-event-inner-intro-event-status-text.event-inprogress').show();
                }
            }

        };

        clock.init();

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

                    $inner.find('.stec-layout-event-inner-intro-attendance').children().hide();

                    $('<li>' + glob.template.preloader + '</li>').addClass('intro-attendance')
                            .appendTo($inner.find('.stec-layout-event-inner-intro-attendance'));
                },
                success: function (rtrn) {

                    var status = parseInt(rtrn.status, 10);
                    var id = parseInt(rtrn.id, 10);

                    $inner.find('.stec-layout-event-inner-intro-attendance li').removeClass('active');
                    $event.find('.stec-layout-event-inner-attendance-invited-buttons li').removeClass('active');

                    var $avatar = $event.find('.stec-layout-event-inner-attendance-attendee-avatar')
                            .filter('[data-userid="' + glob.options.userid + '"]');

                    switch ( status ) {
                        case 1 :
                            $inner.find('.stec-layout-event-inner-intro-attendance-attend').addClass('active');
                            $event.find('.stec-layout-event-inner-attendance-invited-buttons-accept').addClass('active');
                            $avatar.find('li i').attr('class', 'fa fa-check');
                            break;
                        case 2 :
                            $inner.find('.stec-layout-event-inner-intro-attendance-decline').addClass('active');
                            $event.find('.stec-layout-event-inner-attendance-invited-buttons-decline').addClass('active');
                            $avatar.find('li i').attr('class', 'fa fa-times');
                            break;
                        default:
                            $avatar.find('li i').attr('class', 'fa fa-question');

                    }

                    $(event.attendance).each(function () {

                        if ( this.userid == glob.options.userid ) {
                            this.status['r' + data.repeatOffset] = status;
                            return false; // break
                        }

                    });
                },
                error: function (xhr, status, thrown) {
                    console.log(xhr + " " + status + " " + thrown);
                },
                complete: function () {
                    glob.ajax = null;

                    $inner.find('.stec-layout-event-inner-intro-attendance').children().show();
                    $inner.find('.stec-layout-event-inner-intro-attendance').children().last().remove();
                }
            });


        }

        $inner.find('.stec-layout-event-inner-intro-attendance-attend').on(helper.clickHandle(), function (e) {
            e.preventDefault();
            var status = $(this).hasClass('active') ? 0 : 1;
            ajaxAttendance(status);
        });

        $inner.find('.stec-layout-event-inner-intro-attendance-decline').on(helper.clickHandle(), function (e) {
            e.preventDefault();
            var status = $(this).hasClass('active') ? 0 : 2;
            ajaxAttendance(status);
        });

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

            switch ( status ) {
                case 1:
                    $inner.find('.stec-layout-event-inner-intro-attendance-attend').addClass('active');
                    $inner.find('.stec-layout-event-inner-intro-attendance-decline').removeClass('active');
                    break;

                case 2:
                    $inner.find('.stec-layout-event-inner-intro-attendance-attend').removeClass('active');
                    $inner.find('.stec-layout-event-inner-intro-attendance-decline').addClass('active');
                    break;
            }
        } else {
            $inner.find('.stec-layout-event-inner-intro-attendance').hide();
        }

        // Check if event is in progress
        var nowDate = helper.getCalNow(parseInt(event.timezone_utc_offset, 10) / 3600);
        var startDate = helper.dbDateTimeToDate(helper.dbDateOffset(event.start_date, data.repeatOffset));

        if ( nowDate >= startDate ) {
            $inner.find('.stec-layout-event-inner-intro-attendance').hide();
        }

        // Attachments
        if ( event.attachments.length > 0 ) {
            var attachments_template = $inner.find('.stec-layout-event-inner-intro-attachment-template')[0].outerHTML;
            $inner.find('.stec-layout-event-inner-intro-attachment-template').remove();

            $(event.attachments).each(function () {

                var th = this;

                $(attachments_template).html(function (index, html) {
                    return html
                            .replace(/stec_replace_filename/g, th.filename)
                            .replace(/stec_replace_desc/g, th.description)
                            .replace(/\#stec_replace_url/g, th.link)
                            .replace(/stec_replace_size/g, th.size);

                }).appendTo($inner.find('.stec-layout-event-inner-intro-attachments-list'));
            });

            $inner.find('.stec-layout-event-inner-intro-attachments-toggle').on(helper.clickHandle(), function (e) {

                e.preventDefault();

                $(this).toggleClass('active');

                $inner.find('.stec-layout-event-inner-intro-attachments-list').toggleClass('active');

            });
        } else {
            $inner.find('.stec-layout-event-inner-intro-attachments').remove();
        }


        $inner.find('.stec-layout-event-inner-intro-exports-toggle').on(helper.clickHandle(), function (e) {

            e.preventDefault();

            $(this).toggleClass('active');

            $inner.find('.stec-layout-event-inner-intro-exports-options').toggleClass('active');

        });

        $inner.on('click', '.fa-link', function (e) {

            e.stopPropagation();
            e.preventDefault();

            var eventId = $(this).parents('.stec-layout-event').attr('data-id');
            var eventOffset = $(this).parents('.stec-layout-event').attr('data-repeat-time-offset');
            var linkURL = $(this).parent().attr('href');

            var sharer = new window.stecSharer($instance, glob, helper, linkURL, eventId, eventOffset);


        });
    });

})(jQuery);