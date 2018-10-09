(function ($) {

    "use strict";

    $.stecExtend(function (master) {

        if ( master.glob.options.general_settings.show_create_event_form == '0' ) {
            return;
        }

        if ( master.glob.options.view != 'agenda' ) {
            return;
        }

        master.$instance.$agenda.find('.stec-event-create-form').remove();

        // Always open since create event is always available
        $(master.glob.template.eventCreateForm).prependTo(master.$instance.$agenda.find('.stec-layout-events-form'));

        setTimeout(function () {
            master.helper.animate.agenda.openSingle(master.$instance.$agenda.find('.stec-event-create-form'));
        }, 0);

    }, 'onLayoutSet');

    $.stecExtend(function (master) {

        if ( master.glob.options.general_settings.show_create_event_form == '0' ) {
            return;
        }

        // append form
        switch ( master.glob.options.view ) {
            case "agenda" :
                // handled from onLayoutSet above
                break;
            case "month" :
                $(master.glob.template.eventCreateForm).prependTo(master.$instance.$month.find('.stec-layout-events'));
                break;
            case "week" :
                $(master.glob.template.eventCreateForm).prependTo(master.$instance.$week.find('.stec-layout-events'));
                break
            case "day" :
                $(master.glob.template.eventCreateForm).prependTo(master.$instance.$day.find('.stec-layout-events'));
                break;
        }

        // append unapproved yet events
        var events = master.calData.getEvents(false, false, true);

        $(events).each(function () {

            if ( this.approved == 1 ) {
                return true;
            }

            var theEvent = this;

            var $event = $(master.glob.template.eventAapproval)
                    .attr('data-id', this.id)
                    .removeClass('stec-event-awaiting-approval-template')
                    .html(function (index, html) {

                        return html
                                .replace('stec_replace_summary', theEvent.title);

                    });

            if ( master.glob.options.view == "agenda" ) {
                $event.prependTo(master.$instance.$events.not('.stec-layout-agenda-events-all'));
            } else {
                $event.insertAfter(master.$instance.$events.find('.stec-event-create-form'));
            }
        });

    }, 'onEventHolderOpen');

    $.stecExtend(function (master) {

        if ( master.glob.options.general_settings.show_create_event_form == '0' ) {
            return;
        }

        // Clean unapproved li list
        master.$instance.$agenda.find('.stec-layout-event-awaiting-approval').remove();

        // remove colorpicker
        $('.colorpicker-' + master.glob.options.id).remove();

    }, 'onEventHolderClose');

    $.stecExtend(function (master) {

        if ( master.glob.options.general_settings.show_create_event_form == '0' ) {
            return;
        }

        var createEvent = {

            blockForm: false,
            valid: false,
            captchaContainer: null,

            $theForm: null,

            init: function () {

                this.bindControls();

            },

            check_disabled_fields: function () {

                var parent = this;

                switch ( $('input[name="repeat_endson"]').filter(":checked").val() ) {

                    case '0' :
                        //never
                        parent.$theForm.find('input[name="repeat_occurences"]').attr('disabled', 'disabled');
                        parent.$theForm.find('input[name="repeat_ends_on_date"]').attr('disabled', 'disabled');
                        break;
                    case '1' :
                        // after n
                        parent.$theForm.find('input[name="repeat_occurences"]').removeAttr('disabled');
                        parent.$theForm.find('input[name="repeat_ends_on_date"]').attr('disabled', 'disabled');
                        break;
                    case '2' :
                        // on date
                        parent.$theForm.find('input[name="repeat_occurences"]').attr('disabled', 'disabled');
                        parent.$theForm.find('input[name="repeat_ends_on_date"]').removeAttr('disabled');
                        break;
                }

            },

            update_rrule: function () {

                var parent = this;

                var freq = parent.$theForm.find('select[name="event_repeat"]').val();
                var byweekday = [];

                switch ( freq ) {
                    case '0' :
                        freq = false;
                        break;
                    case '1' :
                        freq = RRule.DAILY;
                        break;
                    case '2' :
                        freq = RRule.WEEKLY;

                        parent.$theForm.find('.stec-layout-event-create-form-inner-weekly-by-day')
                                .find('input[type="checkbox"]')
                                .filter(':checked')
                                .each(function () {

                                    byweekday.push(RRule[$(this).attr('name')]);

                                });

                        break;
                    case '3' :
                        freq = RRule.MONTHLY;
                        break;
                    case '4' :
                        freq = RRule.YEARLY;
                        break;
                }

                var interval = parent.$theForm.find('input[name="repeat_gap"]').val();

                var count = false;
                var until = false;

                switch ( parent.$theForm.find('input[name="repeat_endson"]').filter(":checked").val() ) {

                    case '0' :
                        //never
                        count = false;
                        until = false;
                        break;

                    case '1' :
                        // after n
                        count = parent.$theForm.find('input[name="repeat_occurences"]').val();
                        until = false;

                        break;

                    case '2' :
                        // on date
                        count = false;
                        until = new Date(parent.$theForm.find('input[name="repeat_ends_on_date"]').val());
                        break;
                }

                var rr_options = {};

                rr_options.freq = freq;

                if ( count > 0 ) {
                    rr_options.count = count;
                }

                if ( until ) {
                    rr_options.until = until;
                }

                if ( interval > 0 ) {
                    rr_options.interval = interval;
                }

                if ( byweekday.length > 0 ) {
                    rr_options.byweekday = byweekday;
                }

                var the_rule = '';

                if ( freq !== false ) {
                    // Set rule
                    the_rule = new RRule(rr_options);


                } else {
                    // Empty rrule
                    the_rule = new RRule();

                }

                var rrule = the_rule.toString();

                parent.$theForm.find('input[name="rrule"]').val(rrule);

            },

            bindControls: function () {

                var parent = this;

                $(document).on("change", master.instance + (' .stec-event-create-form input[name="repeat_endson"]'), function (e) {
                    parent.check_disabled_fields();
                });

                $(document).on(master.helper.clickHandle(), master.$instance.$agenda.path + ' .stec-layout-event-create-form-inner', function (e) {
                    e.stopPropagation();
                });

                // calendar select
                $(document).on('submit', master.instance + (' .stec-layout-event-create-form-inner form'), function (e) {

                    e.preventDefault();

                    parent.update_rrule();

                    parent.submitEvent();

                });

                // calendar select
                $(document).on('change', master.instance + (' .stec-event-create-form [name="calendar_id"]'), function (e) {

                    var color = $(this).find('option:selected').data('color');

                    parent.$theForm.find('[name="event_color"]').css({
                        background: color
                    }).val(color);

                });

                // datetime change
                $(document).on('change',
                        master.instance + (' .stec-event-create-form [name="start_date"]') + ',' +
                        master.instance + (' .stec-event-create-form [name="end_date"]') + ',' +
                        master.instance + (' .stec-event-create-form [name="start_time_hours"]') + ',' +
                        master.instance + (' .stec-event-create-form [name="end_time_hours"]') + ',' +
                        master.instance + (' .stec-event-create-form [name="start_time_minutes"]') + ',' +
                        master.instance + (' .stec-event-create-form [name="end_time_minutes"]')
                        , function (e) {

                            parent.datePicker.generalTimeFix();
                        });

                // all day
                $(document).on('change', master.instance + ' .stec-event-create-form input[name="all_day"]', function () {

                    if ( $(this).is(':checked') ) {

                        parent.$theForm.find('[name="start_time_hours"]').val($('[name="start_time_hours"]').children().first().val());
                        parent.$theForm.find('[name="start_time_minutes"]').val($('[name="start_time_minutes"]').children().first().val());

                        parent.$theForm.find('[name="end_time_hours"]').val($('[name="end_time_hours"]').children().last().val());
                        parent.$theForm.find('[name="end_time_minutes"]').val($('[name="end_time_minutes"]').children().last().val());

                        parent.$theForm.find('.stec-layout-event-create-form-time').hide();
                        parent.$theForm.find('.stec-layout-event-create-form-time').prev('p').hide();

                    } else {

                        parent.$theForm.find('.stec-layout-event-create-form-time').css('display', 'flex');
                        parent.$theForm.find('.stec-layout-event-create-form-time').prev('p').show();

                        parent.$theForm.find('[name="start_time_hours"]').val($('[name="start_time_hours"]').children().first().val());
                        parent.$theForm.find('[name="start_time_minutes"]').val($('[name="start_time_minutes"]').children().first().val());

                        parent.$theForm.find('[name="end_time_hours"]').val($('[name="end_time_hours"]').children().first().val());
                        parent.$theForm.find('[name="end_time_minutes"]').val($('[name="end_time_minutes"]').children().first().val());

                    }

                });


                // repeat toggle
                $(document).on('change', master.instance + ' .stec-event-create-form select[name="event_repeat"]', function () {

                    if ( parseInt($(this).val(), 10) !== 0 ) {

                        parent.$theForm.find('.stec-layout-event-create-form-inner-repeat-sub').css('display', 'block');
                        parent.$theForm.find('.stec-layout-event-create-form-inner-repeat-gap-block').css('display', 'block');

                        if ( $(this).val() == '2' ) {

                            parent.$theForm.find('.stec-layout-event-create-form-inner-weekly-by-day').show();

                        } else {

                            parent.$theForm.find('.stec-layout-event-create-form-inner-weekly-by-day').hide();

                        }

                    } else {

                        parent.$theForm.find('.stec-layout-event-create-form-inner-repeat-sub').hide();
                        parent.$theForm.find('.stec-layout-event-create-form-inner-repeat-gap-block').hide();
                        parent.$theForm.find('.stec-layout-event-create-form-inner-weekly-by-day').hide();
                    }
                });

                $(document).on(master.helper.clickHandle(),
                        master.instance + (" .stec-event-create-form") + ',' +
                        master.instance + (" .stec-layout-event-create-form-preview-right-event-toggle"), function (e) {


                    e.preventDefault();
                    e.stopPropagation();

                    var $form = $(this).hasClass('stec-event-create-form') ? $(this) : $(this).parents('.stec-event-create-form');

                    createEvent.toggleForm($form);
                });

                // upload image
                $(document).on(master.helper.clickHandle(), master.instance + (' .stec-layout-event-create-form-inner-date-image'), function (e) {

                    e.preventDefault();
                    e.stopPropagation();

                    $(this).next().trigger('click');
                });

                $(document).on('change', master.instance + (' .stec-layout-event-create-form-inner-date-image-file'), function (e) {

                    createEvent.$theForm.find('.stec-layout-event-create-form-inner-date-image').val(this.files[0].name);

                });

                $(document).on(master.helper.clickHandle(), master.$instance.$events.path + ('.stec-layout-event-awaiting-approval-cancel'), function (e) {

                    var eventId = parseInt($(this).parents('.stec-layout-event-awaiting-approval').first().attr('data-id'), 10);

                    createEvent.deleteEvent(eventId);

                });

                $(document).on(master.helper.clickHandle(), master.$instance.$events.path + ('.stec-layout-event-preview-left-approval-cancel'), function (e) {

                    var eventId = parseInt($(this).parents('.stec-layout-event-awaiting-approval').first().attr('data-id'), 10);

                    createEvent.deleteEvent(eventId);

                });

            },

            datePicker: {

                set: function () {

                    createEvent.$theForm.find('[name="start_date"]')
                            .datepicker("destroy")
                            .datepicker({
                                setDate: master.glob.options.year + '-' + master.glob.options.month + '-' + master.glob.options.day,
                                showAnim: 0,
                                dateFormat: "yy-mm-dd",
                                minDate: master.glob.options.general_settings.create_event_form_allow_expired == 1 ? null : 0,
                                onSelect: function () {
                                    createEvent.$theForm.find('[name="end_date"]').datepicker('option', 'minDate', $(this).val());
                                    createEvent.datePicker.generalTimeFix();
                                }
                            })
                            .datepicker("setDate", new Date(master.glob.options.year, master.glob.options.month, master.glob.options.day));

                    createEvent.$theForm.find('[name="end_date"]')
                            .datepicker("destroy")
                            .datepicker({
                                setDate: master.glob.options.year + '-' + master.glob.options.month + '-' + master.glob.options.day,
                                showAnim: 0,
                                minDate: master.glob.options.general_settings.create_event_form_allow_expired == 1 ? null : 0,
                                dateFormat: "yy-mm-dd",
                                onSelect: function () {
                                    createEvent.$theForm.find('[name="start_date"]').datepicker('option', 'maxDate', $(this).val());
                                    createEvent.datePicker.generalTimeFix();
                                }
                            })
                            .datepicker("setDate", new Date(master.glob.options.year, master.glob.options.month, master.glob.options.day));

                    createEvent.$theForm.find('[name="repeat_ends_on_date"]')
                            .datepicker("destroy")
                            .datepicker();
                },

                generalTimeFix: function () {

                    var $startDate = createEvent.$theForm.find('[name="start_date"]');
                    var $endDate = createEvent.$theForm.find('[name="end_date"]');

                    var $startHours = createEvent.$theForm.find('[name="start_time_hours"]');
                    var $endHours = createEvent.$theForm.find('[name="end_time_hours"]');

                    var $startMinutes = createEvent.$theForm.find('[name="start_time_minutes"]');
                    var $endMinutes = createEvent.$theForm.find('[name="end_time_minutes"]');

                    var start = new Date($startDate.val());
                    start.setHours($startHours.val());
                    start.setMinutes($startMinutes.val());


                    var end = new Date($endDate.val());
                    end.setHours($endHours.val());
                    end.setMinutes($endMinutes.val());

                    if ( start.getTime() > end.getTime() ) {
                        $endHours.children().eq($startHours.children().filter(':selected').index()).prop('selected', true);
                        $endMinutes.children().eq($startMinutes.children().filter(':selected').index()).prop('selected', true);
                    }

                }

            },

            colorPicker: {

                set: function () {
                    // init colorpicker
                    createEvent.$theForm.find('.stec-layout-event-create-form-inner-colorpicker').each(function (i) {

                        var th = this;

                        var color = $(th).val();

                        $(th).css({
                            backgroundColor: color
                        });

                        $(th).ColorPicker({
                            klass: 'colorpicker-' + master.glob.options.id,
                            id: 'colorpicker-' + i + '-' + master.glob.options.id,
                            color: color,
                            onShow: function (colpkr) {
                                $(colpkr).show();
                                return false;
                            },
                            onHide: function (colpkr) {
                                $(colpkr).hide();
                                return false;
                            },
                            onChange: function (hsb, hex, rgb) {
                                $(th).attr("title", "#" + hex);

                                $(th).css({
                                    backgroundColor: "#" + hex
                                });

                                $(th).val("#" + hex);
                            }
                        });

                    });
                }

            },

            toggleForm: function ($form) {

                var parent = this;

                this.$theForm = $form;

                this.$theForm.toggleClass('active');
                this.$theForm.find('.stec-layout-event-create-form-preview-right-event-toggle').toggleClass('active');

                this.datePicker.set();
                this.colorPicker.set();

                if ( master.glob.options.captcha.enabled == '1' ) {
                    this.captchaContainer = null;

                    if ( parent.$theForm.find('.stec-layout-event-create-form-g-recaptcha').children().length <= 0 ) {
                        this.captchaContainer = window.grecaptcha.render(parent.$theForm.find('.stec-layout-event-create-form-g-recaptcha').get(0), {
                            sitekey: master.glob.options.captcha.site_key,
                            callback: function (response) {
                                if ( response.length != 0 ) {
                                    parent.valid = true;
                                }
                            }
                        });
                    }
                } else {
                    // captcha is disabled
                    parent.valid = true;
                }

                // Only 1 calendar; hide the selector
                if ( parent.$theForm
                        .find('[name="calendar_id"]').children().length == 2 ) {

                    parent.$theForm
                            .find('[name="calendar_id"]').val(parent.$theForm
                            .find('[name="calendar_id"] option:last').val());

                    parent.$theForm
                            .find('[name="calendar_id"]').parents('div:first').hide();

                    var color = parent.$theForm.find('[name="calendar_id"]').find('option:selected').data('color');

                    parent.$theForm.find('[name="event_color"]').css({
                        background: color
                    }).val(color);

                }


            },

            submitEvent: function () {

                if ( createEvent.blockForm === true ) {
                    return;
                }

                if ( createEvent.valid === false ) {
                    return false;
                }

                if ( master.glob.options.general_settings.create_event_form_allow_expired != 1 ) {

                    var startDate = createEvent.$theForm.find('[name="start_date"]').val();
                    var startTimeHours = parseInt(createEvent.$theForm.find('[name="start_time_hours"]').val(), 10);
                    var startTimeMinutes = parseInt(createEvent.$theForm.find('[name="start_time_minutes"]').val(), 10);

                    var now = window.moment();
                    var eventDate = window.moment(startDate).hour(startTimeHours).minutes(startTimeMinutes);

                    if ( eventDate.isBefore(now) ) {
                        return false;
                    }
                }

                var eventOverview = {
                    title: createEvent.$theForm.find('[name="title"]').val(),
                    calendar_id: createEvent.$theForm.find('[name="calendar_id"]').val()
                };

                if ( $.trim(eventOverview.title) == '' || isNaN(parseInt(eventOverview.calendar_id, 10)) === true ) {
                    return false;
                }

                var formData = new FormData(createEvent.$theForm.find('form')[0]);

                formData.append('action', 'stec_public_ajax_action');
                formData.append('task', 'front_create_event');

                $.ajax({

                    cache: false,
                    processData: false,
                    contentType: false,
                    dataType: "json",
                    type: 'POST',
                    url: window.ajaxurl,
                    data: formData,
                    beforeSend: function () {

                        createEvent.blockForm = true;

                        $(master.glob.template.preloader).appendTo(createEvent.$theForm.find('.stec-layout-event-create-form-inner-submit-flexbox'));

                    },

                    success: function (data) {

                        createEvent.$theForm.find('.stec-preloader').remove();

                        if ( data.error == '1' || !data.event ) {

                            createEvent.$theForm.find('.stec-layout-event-create-form-inner-submit-flexbox .fa-times').show();

                            return false;
                        }

                        master.calData.addToEventsPool([data.event]);

                        createEvent.$theForm.find('.stec-layout-event-create-form-inner-submit-flexbox .fa-check').show();

                        setTimeout(function () {
                            master.layout.set();
                        }, 500);

                    },

                    error: function (xhr, status, thrown) {

                        createEvent.$theForm.find('.stec-preloader').remove();
                        createEvent.$theForm.find('.stec-layout-event-create-form-inner-submit-flexbox .fa-times').show();

                        console.log(xhr + " " + status + " " + thrown);
                    },

                    complete: function () {

                        setTimeout(function () {

                            createEvent.$theForm.find('.stec-layout-event-create-form-inner-submit-flexbox i:visible').fadeTo(250, 0, function () {
                                $(this).css('opacity', '1').hide();
                            });

                            createEvent.blockForm = false;

                            if ( master.glob.options.captcha.enabled == '1' ) {
                                createEvent.valid = false;
                                window.grecaptcha.reset(createEvent.captchaContainer);
                            }

                        }, 1000);

                    }
                });
            },

            deleteEvent: function (eventId) {

                if ( createEvent.blockForm === true ) {
                    return;
                }

                if ( isNaN(eventId) ) {
                    return;
                }

                var $event = master.$instance.$events.find('.stec-layout-event-awaiting-approval[data-id="' + eventId + '"]');

                $.ajax({
                    dataType: "json",
                    type: 'POST',
                    url: window.ajaxurl,
                    data: {
                        action: 'stec_public_ajax_action',
                        task: 'front_delete_event',
                        id: eventId
                    },
                    beforeSend: function () {

                        $(master.glob.template.preloader).prependTo($event.find('.stec-layout-event-preview-right'));

                        $event.find('.stec-layout-event-preview-left-approval-cancel').text(window.stecLang.deleting);

                        createEvent.blockForm = true;
                    },

                    success: function (data) {

                        $event.find('.stec-preloader').remove();

                        if ( data.error == '0' ) {

                            $event.find('.stec-layout-event-preview-left-approval-cancel')
                                    .addClass('stec-layout-event-preview-left-approval-cancel-success')
                                    .text(window.stecLang.deleted);
                            $event.find('.stec-layout-event-preview-right .fa-check').show();

                            master.calData.removeFromEventsPool(eventId);

                            setTimeout(function () {
                                master.layout.set();
                            }, 500);
                        }

                        if ( data.error == '1' ) {

                            $event.find('.stec-layout-event-preview-left-approval-cancel').text(window.stecLang.error);
                            $event.find('.stec-layout-event-preview-right .fa-times').show();

                        }

                    },

                    error: function (xhr, status, thrown) {
                        $event.find('.stec-preloader').remove();
                        $event.find('.stec-layout-event-preview-left-approval-cancel').text(window.stecLang.error);
                        $event.find('.stec-layout-event-preview-right .fa-times').show();
                        console.log(xhr + " " + status + " " + thrown);
                    },

                    complete: function () {
                        setTimeout(function () {
                            createEvent.blockForm = false;
                        }, 1000);
                    }
                });
            }

        };

        createEvent.init();

    });

})(jQuery);
