(function ($) {

    "use strict";

    function createEventForm() {

        var $instance = '';
        var instance = '';
        var blockForm = false;
        var valid = false;
        var captchaContainer = null;
        var $theForm = null;
        var glob = {
            options: null,
            template: {
                preloader: null
            }
        };

        this.init = function (atts) {

            glob.options = atts;

            instance = '#' + glob.options.id;
            $instance = $(instance);

            $theForm = $instance;
            $theForm.toggleClass('active');
            $theForm.find('.stec-layout-event-create-form-preview-right-event-toggle').toggleClass('active');

            glob.template.preloader = $instance.children(".stec-preloader-template").html();

            datePicker.set();
            colorPicker.set();

            // Only 1 calendar; hide the selector
            if ( $theForm.find('[name="calendar_id"]').children().length == 2 ) {

                $theForm.find('[name="calendar_id"]').val($theForm
                        .find('[name="calendar_id"] option:last').val());

                $theForm.find('[name="calendar_id"]').parents('div:first').hide();

                var color = $theForm.find('[name="calendar_id"]').find('option:selected').data('color');

                $theForm.find('[name="event_color"]').css({
                    background: color
                }).val(color);

            }

            if ( glob.options.captcha.enabled == '1' ) {
                captchaContainer = null;

                if ( $theForm.find('.stec-layout-event-create-form-g-recaptcha').children().length <= 0 ) {
                    captchaContainer = window.grecaptcha.render($theForm.find('.stec-layout-event-create-form-g-recaptcha').get(0), {
                        sitekey: glob.options.captcha.site_key,
                        callback: function (response) {
                            if ( response.length != 0 ) {
                                valid = true;
                            }
                        }
                    });
                }
            } else {
                // captcha is disabled
                valid = true;
            }

            if ( glob.options.selector ) {

                if ( $('.stec-create-form-popup-blackscreen').length <= 0 ) {
                    $('<div class="stec-create-form-popup-blackscreen"></div>').appendTo('body');
                }

                $theForm.addClass('is-popup');

                $theForm.hide();
            }

            bindControls();
        };

        var helper = {

            isMobile: function () {
                return (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) ? true : false;
            },

            clickHandle: function (sub) {

                var tap = sub ? 'vclick.' + sub : 'vclick';
                var click = sub ? 'click.' + sub : 'click';

                return this.isMobile() ? tap : click;

            }
        };

        var check_disabled_fields = function () {

            switch ( $('input[name="repeat_endson"]').filter(":checked").val() ) {

                case '0' :
                    //never
                    $theForm.find('input[name="repeat_occurences"]').attr('disabled', 'disabled');
                    $theForm.find('input[name="repeat_ends_on_date"]').attr('disabled', 'disabled');
                    break;
                case '1' :
                    // after n
                    $theForm.find('input[name="repeat_occurences"]').removeAttr('disabled');
                    $theForm.find('input[name="repeat_ends_on_date"]').attr('disabled', 'disabled');
                    break;
                case '2' :
                    // on date
                    $theForm.find('input[name="repeat_occurences"]').attr('disabled', 'disabled');
                    $theForm.find('input[name="repeat_ends_on_date"]').removeAttr('disabled');
                    break;
            }

        };

        var update_rrule = function () {

            var freq = $theForm.find('select[name="event_repeat"]').val();
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

                    $theForm.find('.stec-layout-event-create-form-inner-weekly-by-day')
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

            var interval = $theForm.find('input[name="repeat_gap"]').val();
            var count = false;
            var until = false;

            switch ( $theForm.find('input[name="repeat_endson"]').filter(":checked").val() ) {

                case '0' :
                    //never
                    count = false;
                    until = false;
                    break;

                case '1' :
                    // after n
                    count = $theForm.find('input[name="repeat_occurences"]').val();
                    until = false;

                    break;

                case '2' :
                    // on date
                    count = false;
                    until = new Date($theForm.find('input[name="repeat_ends_on_date"]').val());
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

            $theForm.find('input[name="rrule"]').val(rrule);

        };

        var datePicker = {

            set: function () {

                var parent = this;

                $theForm.find('[name="start_date"]')
                        .datepicker("destroy")
                        .datepicker({
                            setDate: glob.options.year + '-' + glob.options.month + '-' + glob.options.day,
                            showAnim: 0,
                            minDate: glob.options.create_event_form_allow_expired == 1 ? null : 0,
                            dateFormat: "yy-mm-dd",
                            onSelect: function () {
                                $theForm.find('[name="end_date"]').datepicker('option', 'minDate', $(this).val());
                                datePicker.generalTimeFix();
                            }
                        })
                        .datepicker("setDate", new Date(glob.options.year, glob.options.month, glob.options.day));

                $theForm.find('[name="end_date"]')
                        .datepicker("destroy")
                        .datepicker({
                            setDate: glob.options.year + '-' + glob.options.month + '-' + glob.options.day,
                            showAnim: 0,
                            minDate: glob.options.create_event_form_allow_expired == 1 ? null : 0,
                            dateFormat: "yy-mm-dd",
                            onSelect: function () {
                                $theForm.find('[name="start_date"]').datepicker('option', 'maxDate', $(this).val());
                                datePicker.generalTimeFix();
                            }
                        })
                        .datepicker("setDate", new Date(glob.options.year, glob.options.month, glob.options.day));

                $theForm.find('[name="repeat_ends_on_date"]')
                        .datepicker("destroy")
                        .datepicker();
            },

            generalTimeFix: function () {

                var $startDate = $theForm.find('[name="start_date"]');
                var $endDate = $theForm.find('[name="end_date"]');

                var $startHours = $theForm.find('[name="start_time_hours"]');
                var $endHours = $theForm.find('[name="end_time_hours"]');

                var $startMinutes = $theForm.find('[name="start_time_minutes"]');
                var $endMinutes = $theForm.find('[name="end_time_minutes"]');

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

        };

        var colorPicker = {

            set: function () {
                // init colorpicker
                $theForm.find('.stec-layout-event-create-form-inner-colorpicker').each(function (i) {

                    var th = this;

                    var color = $(th).val();

                    $(th).css({
                        backgroundColor: color
                    });

                    $(th).ColorPicker({
                        klass: 'colorpicker-' + glob.options.id,
                        id: 'colorpicker-' + i + '-' + glob.options.id,
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

        };

        var submitEvent = function () {

            if ( blockForm === true ) {

                return;

            }

            if ( valid === false ) {

                return false;

            }
            
            if ( glob.options.create_event_form_allow_expired != 1 ) {

                var startDate = $theForm.find('[name="start_date"]').val();
                var startTimeHours = parseInt($theForm.find('[name="start_time_hours"]').val(), 10);
                var startTimeMinutes = parseInt($theForm.find('[name="start_time_minutes"]').val(), 10);

                var now = window.moment();
                var eventDate = window.moment(startDate).hour(startTimeHours).minutes(startTimeMinutes);

                if ( eventDate.isBefore(now) ) {
                    return false;
                }
            }

            var eventOverview = {
                title: $theForm.find('[name="title"]').val(),
                calendar_id: $theForm.find('[name="calendar_id"]').val()
            };

            if ( $.trim(eventOverview.title) == '' || isNaN(parseInt(eventOverview.calendar_id, 10)) === true ) {
                return false;
            }

            var formData = new FormData($theForm.find('form')[0]);

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

                    blockForm = true;

                    $(glob.template.preloader).appendTo($theForm.find('.stec-layout-event-create-form-inner-submit-flexbox'));

                    $instance.trigger('stecCreateFormBeforeSend', [$instance]);

                },

                success: function (data) {

                    $theForm.find('.stec-preloader').remove();

                    if ( data.error == '1' || !data.event ) {

                        $theForm.find('.stec-layout-event-create-form-inner-submit-flexbox .fa-times').show();

                        $instance.trigger('stecCreateFormSubmitError', [$instance, data]);

                        return false;
                    }

                    $theForm.find('.stec-layout-event-create-form-inner-submit-flexbox .fa-check').show();

                    $instance.trigger('stecCreateFormSubmit', [$instance, data]);

                },

                error: function (xhr, status, thrown) {

                    $theForm.find('.stec-preloader').remove();

                    $theForm.find('.stec-layout-event-create-form-inner-submit-flexbox .fa-times').show();

                    console.log(xhr + " " + status + " " + thrown);

                    $instance.trigger('stecCreateFormSubmitAjaxError', [$instance, xhr, status, thrown]);
                },

                complete: function () {

                    setTimeout(function () {

                        $theForm.find('.stec-layout-event-create-form-inner-submit-flexbox i:visible').fadeTo(250, 0, function () {
                            $(this).css('opacity', '1').hide();
                        });

                        blockForm = false;

                        if ( glob.options.captcha.enabled == '1' ) {
                            valid = false;
                            window.grecaptcha.reset(captchaContainer);
                        }

                    }, 1000);

                }
            });
        };

        var deleteEvent = function (eventId) {

            if ( blockForm === true ) {
                return;
            }

            if ( isNaN(eventId) ) {
                return;
            }

            var $event = $instance.$events.find('.stec-layout-event-awaiting-approval[data-id="' + eventId + '"]');

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

                    $event.find('.stec-layout-event-preview-left-approval-cancel').text(window.stecLang.deleting);

                    blockForm = true;
                },

                success: function (data) {

                    if ( data.error == '0' ) {

                        $event.find('.stec-layout-event-preview-left-approval-cancel')
                                .addClass('stec-layout-event-preview-left-approval-cancel-success')
                                .text(window.stecLang.deleted);
                        $event.find('.stec-layout-event-preview-right .fa-check').show();

                    }

                    if ( data.error == '1' ) {

                        $event.find('.stec-layout-event-preview-left-approval-cancel').text(window.stecLang.error);
                        $event.find('.stec-layout-event-preview-right .fa-times').show();

                    }

                },

                error: function (xhr, status, thrown) {
                    $event.find('.stec-layout-event-preview-left-approval-cancel').text(window.stecLang.error);
                    $event.find('.stec-layout-event-preview-right .fa-times').show();
                    console.log(xhr + " " + status + " " + thrown);
                },

                complete: function () {
                    setTimeout(function () {
                        blockForm = false;
                    }, 1000);
                }
            });
        };

        var bindControls = function () {

            if ( glob.options.selector ) {

                $(document).on(helper.clickHandle(), '.stec-create-form-popup-blackscreen', function (e) {
                    $theForm.hide();
                    $('.stec-create-form-popup-blackscreen').hide();
                });

                $(document).on(helper.clickHandle(), glob.options.selector, function (e) {
                    e.preventDefault();
                    $theForm.css({
                        top: $(window).scrollTop()
                    }).toggle();
                    $('.stec-create-form-popup-blackscreen').toggle();
                });
            }

            $(document).on("change", instance + (' .stec-event-create-form input[name="repeat_endson"]'), function (e) {
                check_disabled_fields();
            });

            // calendar select
            $(document).on('submit', instance + (' .stec-layout-event-create-form-inner form'), function (e) {

                e.preventDefault();

                update_rrule();

                submitEvent();

            });

            // calendar select
            $(document).on('change', instance + (' .stec-event-create-form [name="calendar_id"]'), function (e) {

                var color = $(this).find('option:selected').data('color');

                $theForm.find('[name="event_color"]').css({
                    background: color
                }).val(color);

            });

            // datetime change
            $(document).on('change',
                    instance + (' .stec-event-create-form [name="start_date"]') + ',' +
                    instance + (' .stec-event-create-form [name="end_date"]') + ',' +
                    instance + (' .stec-event-create-form [name="start_time_hours"]') + ',' +
                    instance + (' .stec-event-create-form [name="end_time_hours"]') + ',' +
                    instance + (' .stec-event-create-form [name="start_time_minutes"]') + ',' +
                    instance + (' .stec-event-create-form [name="end_time_minutes"]')
                    , function (e) {
                        datePicker.generalTimeFix();
                    });

            // all day
            $(document).on('change', instance + ' .stec-event-create-form input[name="all_day"]', function () {

                if ( $(this).is(':checked') ) {

                    $theForm.find('[name="start_time_hours"]').val($('[name="start_time_hours"]').children().first().val());
                    $theForm.find('[name="start_time_minutes"]').val($('[name="start_time_minutes"]').children().first().val());

                    $theForm.find('[name="end_time_hours"]').val($('[name="end_time_hours"]').children().last().val());
                    $theForm.find('[name="end_time_minutes"]').val($('[name="end_time_minutes"]').children().last().val());

                    $theForm.find('.stec-layout-event-create-form-time').hide();
                    $theForm.find('.stec-layout-event-create-form-time').prev('p').hide();

                } else {

                    $theForm.find('.stec-layout-event-create-form-time').css('display', 'flex');
                    $theForm.find('.stec-layout-event-create-form-time').prev('p').show();

                    $theForm.find('[name="start_time_hours"]').val($('[name="start_time_hours"]').children().first().val());
                    $theForm.find('[name="start_time_minutes"]').val($('[name="start_time_minutes"]').children().first().val());

                    $theForm.find('[name="end_time_hours"]').val($('[name="end_time_hours"]').children().first().val());
                    $theForm.find('[name="end_time_minutes"]').val($('[name="end_time_minutes"]').children().first().val());

                }

            });


            // repeat toggle
            $(document).on('change', instance + ' .stec-event-create-form select[name="event_repeat"]', function () {

                if ( parseInt($(this).val(), 10) !== 0 ) {

                    $theForm.find('.stec-layout-event-create-form-inner-repeat-sub').css('display', 'block');
                    $theForm.find('.stec-layout-event-create-form-inner-repeat-gap-block').css('display', 'block');

                    if ( $(this).val() == '2' ) {

                        $theForm.find('.stec-layout-event-create-form-inner-weekly-by-day').show();

                    } else {

                        $theForm.find('.stec-layout-event-create-form-inner-weekly-by-day').hide();

                    }

                } else {

                    $theForm.find('.stec-layout-event-create-form-inner-repeat-sub').hide();
                    $theForm.find('.stec-layout-event-create-form-inner-repeat-gap-block').hide();
                    $theForm.find('.stec-layout-event-create-form-inner-weekly-by-day').hide();
                }
            });

            // upload image
            $(document).on(helper.clickHandle(), instance + (' .stec-layout-event-create-form-inner-date-image'), function (e) {

                e.preventDefault();
                e.stopPropagation();

                $(this).next().trigger('click');
            });

            $(document).on('change', instance + (' .stec-layout-event-create-form-inner-date-image-file'), function (e) {

                $theForm.find('.stec-layout-event-create-form-inner-date-image').val(this.files[0].name);

            });

            $(document).on(helper.clickHandle(), instance + (' .stec-layout-event-preview-right-event-toggle'), function (e) {

                $('.stec-create-form-popup-blackscreen').hide();
                $theForm.hide();

            });

            /* Not implemented in standalone form yet
             $(document).on(helper.clickHandle(), $instance.$events.path + ('.stec-layout-event-awaiting-approval-cancel'), function (e) {
             var eventId = parseInt($(this).parents('.stec-layout-event-awaiting-approval').first().attr('data-id'), 10);
             createEventForm.deleteEvent(eventId);
             });
             */

        };


    }

    $(document).ready(function () {

        // Set boostrap datetimepicker to no conflict mode
        if ( typeof $.fn.datepicker.noConflict === 'function' ) {
            $.fn.bootstrapDP = $.fn.datepicker.noConflict();
        }

        if ( typeof window.stachethemes_ec_create_form_instance !== "undefined" ) {
            $(window.stachethemes_ec_create_form_instance).each(function () {

                var cef = new createEventForm();
                cef.init(this);

            });
        }
    });

})(jQuery);
