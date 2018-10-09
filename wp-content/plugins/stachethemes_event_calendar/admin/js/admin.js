(function ($) {

    "use strict";

    $(document).ready(function () {

        // Set boostrap datetimepicker to no conflict mode
        if ( typeof $.fn.datepicker.noConflict === 'function' ) {
            $.fn.bootstrapDP = $.fn.datepicker.noConflict();
        }

        (function woocommerce() {

            if ( $('.stachethemes-admin-woocommerce-product-template').length <= 0 )
                return;

            var template = $('.stachethemes-admin-woocommerce-product-template')[0].outerHTML;
            $('.stachethemes-admin-woocommerce-product-template').remove();


            $('.add-wc-item').on('click', function (e) {
                e.preventDefault();

                var item_id = $('select[name="wc_item_list"]').children(':selected').val();
                var item_name = $('select[name="wc_item_list"]').children(':selected').text();

                var html = $(template);

                html.html(function (index, html) {

                    return html
                            .replace(/stec_replace_wc_product_id/g, item_id)
                            .replace(/stec_replace_wc_product_name/g, item_name);

                });

                $(html).appendTo($('.stachethemes-admin-section-tab[data-tab="woocommerce"]'));

            });


            $(document).on('click', '.stachethemes-admin-woocommerce-product .delete', function (e) {

                e.preventDefault();

                var parent = $(this).parents('.stachethemes-admin-woocommerce-product');

                parent.remove();

            });
        })();

        (function attachments() {

            if ( $('.stachethemes-admin-attachments-attachment-template').length <= 0 )
                return;

            var template = $('.stachethemes-admin-attachments-attachment-template')[0].outerHTML;
            $('.stachethemes-admin-attachments-attachment-template').remove();

            function arrange() {
                $(".stachethemes-admin-attachments-attachment").each(function (i) {
                    $(this).find("input").each(function () {
                        this.name = this.name.replace(/attachment\[\d]/g, function (str, p1) {
                            return 'attachment[' + i + ']';
                        });
                    });
                });
            }

            $(document).on('click', '.add-attachments-attachment', function (e) {

                e.preventDefault();

                var th = this;

                var media_frame = wp.media({
                    button: {text: "Pick Selected"},
                    library: {type: ''},
                    frame: 'select',
                    title: "Select Attachment",
                    multiple: true
                });

                media_frame.open();

                media_frame.on('select', function () {

                    var attachments = media_frame.state().get('selection').toJSON();

                    $(attachments).each(function (i) {

                        var att = this;
                        var html = $(template);

                        html.removeClass('stachethemes-admin-attachments-attachment-template');

                        html.html(function (index, html) {

                            return html
                                    .replace(/%title%/g, att.filename)
                                    .replace(/%id%/g, att.id);

                        });

                        $(html).insertBefore(th);


                    });

                    arrange();

                });


            });

            $(document).on('click', '.stachethemes-admin-attachments-attachment .delete', function (e) {

                e.preventDefault();

                var parent = $(this).parents('.stachethemes-admin-attachments-attachment');

                parent.remove();
                arrange();

            });

            arrange();

        })();

        (function list() {

            $('.stec-list-bulk input').on('click', function (e) {
                if ( $(this).is(':checked') ) {
                    $('.stec-list li').find('input[type="checkbox"]').prop('checked', true);
                } else {
                    $('.stec-list li').find('input[type="checkbox"]').prop('checked', false);
                }
            });

            $('.stec-list-bulk form').on('submit', function (e) {
                var form = this;
                $('.stec-list li input[type="checkbox"]').filter(':checked').each(function () {
                    $('<input type="hidden" name="idlist[]" value="' + $(this).val() + '" />').appendTo(form);
                });
                return true;
            });

        })();

        (function attendance() {

            $('.attendee-list').children().each(function () {
                if ( $(this).find('input[type="hidden"]').length > 0 ) {
                    $(this).addClass('active');
                }
            });

            $(document).on('click', '.attendance-all .uninvite-all', function (e) {

                e.preventDefault();

                $(this).parents('.attendance-all').removeClass('active');

                $('.attendee-list li').each(function () {
                    $(this)
                            .removeClass('active')
                            .find('input[type="hidden"]')
                            .remove();
                });

            });

            $(document).on('click', '.attendance-all .invite-all', function (e) {

                e.preventDefault();

                $(this).parents('.attendance-all').addClass('active');

                $('.attendee-list li').each(function () {

                    var userid = $(this).attr('data-userid');

                    var html = '<input type="hidden" name="attendee[][userid]" value="' + userid + '" />';

                    $(html).appendTo(this);

                    $(this).addClass('active');

                });

            });

            $(document).on('click', '.attendee-list .uninvite-user', function (e) {

                e.preventDefault();

                $(this).parents('li')
                        .removeClass('active')
                        .find('input[type="hidden"]')
                        .remove();

            });

            $(document).on('click', '.attendee-list .invite-user', function (e) {

                e.preventDefault();

                var userid = $(this).parents('li').attr('data-userid');

                var html = '<input type="hidden" name="attendee[][userid]" value="' + userid + '" />';

                $(html).appendTo($(this).parents('li'));

                $(this).parents('li').addClass('active');

            });

            $('.add-attendee-mail').on('click', function (e) {

                e.preventDefault();

                var mail = $(this).prev().val();

                var regex = /^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i;

                if ( regex.test(mail) === false ) {
                    return; // invalid mail
                }

                var html = $(this)
                        .parents('.stachethemes-admin-section-tab')
                        .find('.attendee-email-list li')
                        .first()
                        .clone(false);

                $(html).html(function (a, b) {

                    return b.replace(/%mail%/g, mail);

                }).appendTo($(this)
                        .parents('.stachethemes-admin-section-tab')
                        .find('.attendee-email-list'));

                $(this).prev().val('');

            });

            $(document).on('click', '.attendee-email-remove', function (e) {
                e.preventDefault();
                $(this).parents('li').remove();
            });

        })();

        (function schedule() {

            if ( $('.stachethemes-admin-schedule-timespan-template').length <= 0 )
                return;

            var template = $('.stachethemes-admin-schedule-timespan-template')[0].outerHTML;
            $('.stachethemes-admin-schedule-timespan-template').remove();

            function arrange() {
                $(".stachethemes-admin-schedule-timespan").each(function (i) {
                    $(this).find("input, select, textarea").each(function () {
                        this.name = this.name.replace(/schedule\[\d]/g, function (str, p1) {
                            return 'schedule[' + i + ']';
                        });
                    });
                });
            }

            $(document).on('change, keyup', '.stachethemes-admin-schedule-timespan .stachethemes-admin-input:not(.input-date)', function (e) {

                var $parent = $(this).parents('.stachethemes-admin-schedule-timespan');

                var value = $(this).val();

                if ( value != "" ) {
                    $parent.find('.stachethemes-admin-schedule-timespan-title span').first().text(value);
                    $parent.find('.stachethemes-admin-schedule-timespan-title span').last().hide();
                } else {
                    $parent.find('.stachethemes-admin-schedule-timespan-title span').first().text('');
                    $parent.find('.stachethemes-admin-schedule-timespan-title span').last().show();
                }

            });

            $(document).on('click', '.stachethemes-admin-schedule-timespan .collapse', function (e) {
                e.preventDefault();
                $(this).parents('.stachethemes-admin-schedule-timespan').addClass('stachethemes-admin-schedule-collapse');
            });

            $(document).on('click', '.stachethemes-admin-schedule-timespan .expand', function (e) {
                e.preventDefault();
                $(this).parents('.stachethemes-admin-schedule-timespan').removeClass('stachethemes-admin-schedule-collapse');
            });

            $(document).on('click', '.stachethemes-admin-schedule-timespan .delete', function (e) {

                e.preventDefault();

                var parent = $(this).parents('.stachethemes-admin-schedule-timespan');

                parent.remove();
                arrange();
            });

            $(document).on('click', '.add-schedule-timespan', function (e) {

                e.preventDefault();

                var html = $(template);

                html.removeClass('tachethemes-admin-schedule-timespan-template');

                $(html).insertBefore($(this).parents('.stachethemes-admin-section-tab').find('.add-schedule-timespan'));

                arrange();

                $(".stachethemes-admin-schedule-timespan").not(":last").find('.collapse').trigger('click');
                $(".stachethemes-admin-schedule-timespan").last().find('.expand').trigger('click');

                controlBind($(".stachethemes-admin-schedule-timespan").last());

            });

            $(".stachethemes-admin-schedule-timespan").addClass('stachethemes-admin-schedule-collapse');

            $(".stachethemes-admin-schedule-timespan").each(function (i) {

                var title = $(this).find('input[type="text"]').not('.input-date').val();

                if ( title != '' ) {
                    $(this).find('.stachethemes-admin-schedule-timespan-title span').first().text(title);
                    $(this).find('.stachethemes-admin-schedule-timespan-title span').last().hide();
                }

                controlBind($(this));
            });

            function controlBind($el) {

                $el.find('.input-date').datepicker({
                    showAnim: 0,
                    dateFormat: "yy-mm-dd",
                    minDate: $('.stachethemes-admin-section-tab[data-tab="general"] .input-date[name="start_date"]').val(),
                    maxDate: $('.stachethemes-admin-section-tab[data-tab="general"] .input-date[name="end_date"]').val(),
                    onSelect: function () {
                        scheduleFixTime($(this).parents('.stachethemes-admin-schedule-timespan'));
                    }
                });

                $('.stachethemes-admin-section-tab[data-tab="general"] .input-date[name="start_date"]').on('change', function () {
                    $el.find('.input-date').datepicker('option', 'minDate', $(this).val());
                });

                $('.stachethemes-admin-section-tab[data-tab="general"] .input-date[name="end_date"]').on('change', function () {
                    $el.find('.input-date').datepicker('option', 'maxDate', $(this).val());
                });

                $el.find('.stachethemes-admin-select').on('change', function () {
                    scheduleFixTime($(this).parents('.stachethemes-admin-schedule-timespan'));
                });


                $el.find('.stachethemes-admin-colorpicker').each(function () {

                    var th = this;

                    var color = $(th).val();

                    $(th).css({
                        backgroundColor: color
                    });

                    $(th).ColorPicker({
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

            function scheduleFixTime($el) {

                var $scheduleDate = $el.find('.input-date');
                var $scheduleHours = $el.find('.stachethemes-admin-select').first();
                var $scheduleMinutes = $el.find('.stachethemes-admin-select').last();

                var $startDate = $('.stachethemes-admin-section-tab[data-tab="general"] .input-date[name="start_date"]');
                var $endDate = $('.stachethemes-admin-section-tab[data-tab="general"] .input-date[name="end_date"]');

                var $startHours = $('.stachethemes-admin-section-tab[data-tab="general"] select[name="start_time_hours"]');
                var $endHours = $('.stachethemes-admin-section-tab[data-tab="general"] select[name="end_time_hours"]');

                var $startMinutes = $('.stachethemes-admin-section-tab[data-tab="general"] select[name="start_time_minutes"]');
                var $endMinutes = $('.stachethemes-admin-section-tab[data-tab="general"] select[name="end_time_minutes"]');


                var schedule = new Date($scheduleDate.val());
                schedule.setHours($scheduleHours.val());
                schedule.setMinutes($scheduleMinutes.val());

                var start = new Date($startDate.val());
                start.setHours($startHours.val());
                start.setMinutes($startMinutes.val());

                var end = new Date($endDate.val());
                end.setHours($endHours.val());
                end.setMinutes($endMinutes.val());

                if ( schedule.getTime() < start.getTime() ) {
                    $scheduleDate.datepicker({
                        setDate: $startDate.val()
                    });

                    $scheduleHours.val($startHours.val());
                    $scheduleMinutes.val($startMinutes.val());
                }

                if ( schedule.getTime() > end.getTime() ) {
                    $scheduleDate.datepicker({
                        setDate: $endDate.val()
                    });

                    $scheduleHours.val($endHours.val());
                    $scheduleMinutes.val($endMinutes.val());
                }
            }

            arrange();

        })();

        (function guests() {

            if ( $('.stachethemes-admin-guests-guest-template').length <= 0 )
                return;

            var template = $('.stachethemes-admin-guests-guest-template')[0].outerHTML;
            $('.stachethemes-admin-guests-guest-template').remove();

            function arrange() {
                $(".stachethemes-admin-guests-guest").each(function (i) {
                    $(this).find("input, select, textarea").each(function () {
                        this.name = this.name.replace(/guests\[\d]/g, function (str, p1) {
                            return 'guests[' + i + ']';
                        });
                    });

                    $(this).find("[data-name]").each(function () {
                        $(this).attr('data-name', $(this).attr('data-name').replace(/guests\[\d]/g, function (str, p1) {
                            return 'guests[' + i + ']';
                        }));
                    });
                });
            }
            ;

            function arrangeSocLinks($el) {

                $el.find('.stachethemes-admin-section-flex-guest-social').each(function (i) {
                    $(this).find("input, select, textarea").each(function () {
                        this.name = this.name.replace(/\[social]\[\d]/g, function (str, p1) {
                            return '[social][' + i + ']';
                        });
                    });
                });
            }

            $(document).on('click', '.stachethemes-admin-guests-guest .collapse', function (e) {
                e.preventDefault();
                $(this).parents('.stachethemes-admin-guests-guest').addClass('stachethemes-admin-guests-collapse');
            });

            $(document).on('click', '.stachethemes-admin-guests-guest .expand', function (e) {
                e.preventDefault();
                $(this).parents('.stachethemes-admin-guests-guest').removeClass('stachethemes-admin-guests-collapse');
            });

            $(document).on('click', '.add-guests-soclink', function (e) {
                e.preventDefault();

                var soc = $(this).parent().find('.stachethemes-admin-section-flex-guest-social').last().clone();

                $(soc).insertAfter($(this).parent().find('.stachethemes-admin-section-flex-guest-social').last());

                $(this).parent().find('.stachethemes-admin-section-flex-guest-social').last().find('input, select').val('');

                arrangeSocLinks($(this).parent());
            });

            $(document).on('click', '.stachethemes-admin-guests-social-remove', function (e) {
                e.preventDefault();

                var total = $(this).parents('.stachethemes-admin-guests-guest').find('.stachethemes-admin-section-flex-guest-social').length;

                if ( total <= 1 ) {
                    $(this).parent().find('select, input').val('');
                } else {
                    $(this).parent().remove();

                    arrangeSocLinks($(this).parent());
                }


            });

            $(document).on('click', '.add-guests-guest', function (e) {
                e.preventDefault();

                var html = $(template).removeClass('.stachethemes-admin-guests-guest-template');

                $(html).insertBefore($(this).parents('.stachethemes-admin-section-tab').find('.add-guests-guest'));

                arrange();

                $(".stachethemes-admin-guests-guest").not(":last").find('.collapse').trigger('click');
                $(".stachethemes-admin-guests-guest").last().find('.expand').trigger('click');

            });

            $(document).on('click', '.stachethemes-admin-guests-guest .delete', function (e) {

                e.preventDefault();

                var parent = $(this).parents('.stachethemes-admin-guests-guest');

                parent.remove();
                arrange();

            });

            $(document).on('change, keyup', '.stachethemes-admin-guests-guest .stachethemes-admin-input:first', function (e) {

                var $parent = $(this).parents('.stachethemes-admin-guests-guest');

                var value = $(this).val();

                if ( value != "" ) {
                    $parent.find('.stachethemes-admin-guests-guest-title span').first().text(value);
                    $parent.find('.stachethemes-admin-guests-guest-title span').last().hide();
                } else {
                    $parent.find('.stachethemes-admin-guests-guest-title span').first().text('');
                    $parent.find('.stachethemes-admin-guests-guest-title span').last().show();
                }

            });

            if ( $('.stachethemes-admin-add-image-list[data-single="1"] li').length > 0 ) {
                $('.stachethemes-admin-add-image-list[data-single="1"]').next().hide();
            }

            $(".stachethemes-admin-guests-guest").addClass('stachethemes-admin-guests-collapse');

            arrange();

            $(".stachethemes-admin-guests-guest").each(function (i) {

                arrangeSocLinks($(this));

                var title = $(this).find('input[type="text"]').first().val();

                if ( title != '' ) {
                    $(this).find('.stachethemes-admin-guests-guest-title span').first().text(title);
                    $(this).find('.stachethemes-admin-guests-guest-title span').last().hide();
                }

            });


        })();

        (function tabs() {

            $('.stachethemes-admin-tabs-list li').first().addClass('active');
            $('.stachethemes-admin-section-tab').first().show();

            $('.stachethemes-admin-tabs-list li').on('click', function (e) {
                e.preventDefault();

                $('.stachethemes-admin-tabs-list li').removeClass('active');
                $(this).addClass('active');

                var tab = $(this).attr('data-tab');

                $('.stachethemes-admin-section-tab').hide();
                $('.stachethemes-admin-section-tab[data-tab="' + tab + '"]').show();
            });

        })();

        (function images() {

            $(document).on('click', '.stachethemes-admin-image-container i', function () {

                if ( $(this).parents(".stachethemes-admin-add-image-list").attr('data-single') == true ) {
                    $(this).parents(".stachethemes-admin-add-image-list").next().show();
                }

                $(this).parent().remove();
            });

            $('.stachethemes-admin-add-image-list').sortable();

            $(document).on("click", '.stachethemes-admin-add-image', function (e) {

                e.preventDefault();

                var th = this;

                var data_name = $(this).attr('data-name');
                var data_single = $(this).attr('data-single');

                var media_frame = wp.media({
                    button: {text: "Pick Selected"},
                    library: {type: 'image'},
                    frame: 'select',
                    title: data_single ? "Select Image" : "Select Images",
                    multiple: data_single ? false : true
                });

                media_frame.open();

                media_frame.on('select', function () {
                    var attachments = media_frame.state().get('selection').toJSON();

                    $(attachments).each(function (i) {

                        var att = this;

                        if ( att.type == "image" ) {

                            var src = att.sizes.medium ? att.sizes.medium.url : att.sizes.full.url;

                            var html = '<li class="stachethemes-admin-image-container">';
                            html += '<i class="fa fa-times"></i>';
                            html += '<img data-id="' + att.id + '" src="' + src + '" alt="' + att.alt + '" />';
                            html += '<input type="hidden" name="' + data_name + '" value="' + att.id + '">';
                            html += '</li>';

                            $(html).appendTo($(th).prev('.stachethemes-admin-add-image-list'));
                        }

                    });

                    if ( data_single ) {
                        $(th).hide();
                    }
                });
            });

        })();

        (function general() {

            $('.stachethemes-admin-msg-notice, .stachethemes-admin-msg-error').on('click', function () {
                $(this).remove();
            });

            $('.stachethemes-admin-section-tab[data-tab="general"] .input-date[name="start_date"]').datepicker({
                showAnim: 0,
                dateFormat: "yy-mm-dd"
            });

            $('.stachethemes-admin-section-tab[data-tab="general"] .input-date[name="end_date"]').datepicker({
                showAnim: 0,
                dateFormat: "yy-mm-dd",
                minDate: $('.stachethemes-admin-section-tab[data-tab="general"] .input-date[name="start_date"]').val()
            });

            /** 
             * End date cannot be less than start date
             */

            $('.stachethemes-admin-section-tab[data-tab="general"] .input-date[name="start_date"]').on('change', function () {
                $('.stachethemes-admin-section-tab[data-tab="general"] .input-date[name="end_date"]').datepicker('option', 'minDate', $(this).val());
                generalTimeFix();
            });

            $('.stachethemes-admin-section-tab[data-tab="general"] .input-date[name="end_date"]').on('change', function () {
                generalTimeFix();
            });

            $('.stachethemes-admin-section-tab[data-tab="general"] select[name="start_time_hours"]').on('change', function (e) {
                generalTimeFix();
            });

            $('.stachethemes-admin-section-tab[data-tab="general"] select[name="end_time_hours"]').on('change', function (e) {
                generalTimeFix();
            });

            $('.stachethemes-admin-section-tab[data-tab="general"] select[name="start_time_minutes"]').on('change', function (e) {
                generalTimeFix();
            });

            $('.stachethemes-admin-section-tab[data-tab="general"] select[name="end_time_minutes"]').on('change', function (e) {
                generalTimeFix();
            });

            function generalTimeFix() {

                var $startDate = $('.stachethemes-admin-section-tab[data-tab="general"] .input-date[name="start_date"]');
                var $endDate = $('.stachethemes-admin-section-tab[data-tab="general"] .input-date[name="end_date"]');

                var $startHours = $('.stachethemes-admin-section-tab[data-tab="general"] select[name="start_time_hours"]');
                var $endHours = $('.stachethemes-admin-section-tab[data-tab="general"] select[name="end_time_hours"]');

                var $startMinutes = $('.stachethemes-admin-section-tab[data-tab="general"] select[name="start_time_minutes"]');
                var $endMinutes = $('.stachethemes-admin-section-tab[data-tab="general"] select[name="end_time_minutes"]');

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

            $('.stachethemes-admin-colorpicker').each(function () {

                var th = this;

                var color = $(th).val();

                $(th).css({
                    backgroundColor: color
                });

                $(th).ColorPicker({
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

            var allDay = function () {

                if ( $('[name="all_day"]').is(':checked') ) {

                    $('[name="start_time_hours"]').hide().val($('[name="start_time_hours"]').children().first().val());
                    $('[name="start_time_minutes"]').hide().val($('[name="start_time_minutes"]').children().first().val());


                    $('[name="end_time_hours"]').hide().val($('[name="end_time_hours"]').children().last().val());
                    $('[name="end_time_minutes"]').hide().val($('[name="end_time_minutes"]').children().last().val());

                } else {

                    $('[name="start_time_hours"]').show();
                    $('[name="start_time_minutes"]').show();

                    $('[name="end_time_hours"]').show();
                    $('[name="end_time_minutes"]').show();

                }
            };

            $('[name="all_day"]').on('change', function (e) {
                allDay();
            });

            allDay();

            $('.stachethemes-admin-section-tab[data-tab="general"] select[name="calid"]').on('change', function () {

                var id = $(this).val();
                var color = $('.stachethemes-admin-section-tab[data-tab="general"] [name="calendar_colors_by_id[' + id + ']"]').val();

                $('.stachethemes-admin-section-tab[data-tab="general"] [name="event_color"]')
                        .val(color)
                        .attr('title', color)
                        .attr('value', color)
                        .css('background-color', color);

            });

        })();

        (function other() {

            $('.stec-list .delete-item').on('click', function (e) {

                var lang = $(this).attr('data-confirm');

                if ( confirm(lang) ) {
                    return true;
                } else {
                    return false;
                }

            });

            $('.stec-list-bulk .delete-all-items').last().on('click', function (e) {

                var lang = $(this).attr('data-confirm');

                if ( confirm(lang) ) {
                    return true;
                } else {
                    return false;
                }

            });

        })();

        (function activator() {

            $('.stec-activator-success').hide();
            $('.stec-activator-beforesend').hide();

            var block = false;

            $('#stec-activator .stachethemes-admin-form').on('submit', function (e) {

                if ( block === true ) {
                    return false;
                }

                e.preventDefault();

                var purchase_code = $(this).find('[name="purchase_code"]').val();
                var task = $(this).find('[name="task"]').val();
                var domain = window.location.hostname;

                if ( $.trim(purchase_code) == '' ) {
                    return false;
                }

                switch ( task ) {

                    case 'activate' :

                        stec_api.activate(purchase_code, domain, function (result) {

                            if ( result === true ) {

                                stec_activator.activate(purchase_code, domain);

                            } else {

                                stec_activator.throwError();
                            }

                        });

                        break;

                    case 'deactivate' :

                        stec_api.deactivate(purchase_code, domain, function (result) {

                            if ( result === true ) {

                                stec_activator.deactivate(purchase_code, domain);

                            } else {

                                stec_activator.throwError();
                            }

                        });

                        break;
                }

            });

            var stec_api = {

                beforeSend: function () {
                    $('.stec-activator-beforesend').show();
                    block = true;
                    $('#stec-activator .stachethemes-admin-button').hide();
                },

                complete: function () {
                    $('.stec-activator-success').show();
                    $('.stec-activator-beforesend').hide();
                    block = false;
                },

                activate: function (purchase_code, domain, callback) {

                    var parent = this;

                    $.ajax({
                        method: 'GET',
                        url: 'https://api.stachethemes.com/stec/activate/' + purchase_code + '/' + domain,
                        dataType: 'json',

                        beforeSend: function (xhr) {
                            parent.beforeSend();
                        },

                        error: function (xhr, status, thrown) {
                            console.log(xhr + " " + status + " " + thrown);
                        },

                        success: function (response) {

                            var result = false;
                            
                            if ( response && response.data.success === 1 ) {
                                result = true;
                            }

                            if ( typeof callback === 'function' ) {

                                callback(result);

                            }

                        },

                        complete: function () {
                            parent.complete();
                        }
                    });

                },

                deactivate: function (purchase_code, domain, callback) {

                    var parent = this;

                    $.ajax({
                        method: 'GET',
                        url: 'https://api.stachethemes.com/stec/deactivate/' + purchase_code + '/' + domain,
                        dataType: 'json',
                        beforeSend: function (xhr) {
                            parent.beforeSend();
                        },
                        error: function (xhr, status, thrown) {
                            console.log(xhr + " " + status + " " + thrown);
                        },
                        success: function (response) {

                            var result = false;

                            if ( response && response.data.success === 1 ) {
                                result = true;
                            }

                            if ( typeof callback === 'function' ) {
                                callback(result);
                            }

                        },
                        complete: function () {
                            parent.complete();
                        }
                    });

                }

            };

            var stec_activator = {

                throwError: function () {
                    $.ajax({
                        method: 'POST',
                        url: window.ajaxurl,
                        dataType: 'json',
                        data: {
                            action: 'stec_ajax_action',
                            task: 'throwerror_license',
                            security: window.stec_ajax_nonce
                        },
                        error: function (xhr, status, thrown) {
                            console.log(xhr + " " + status + " " + thrown);
                        },
                        success: function (data) {
                            location.reload();
                        }
                    });
                },

                activate: function (purchase_code, domain) {

                    $.ajax({
                        method: 'POST',
                        url: window.ajaxurl,
                        dataType: 'json',
                        data: {
                            action: 'stec_ajax_action',
                            purchase_code: purchase_code,
                            server_name: domain,
                            security: window.stec_ajax_nonce,
                            task: 'activate_license'
                        },

                        error: function (xhr, status, thrown) {
                            console.log(xhr + " " + status + " " + thrown);
                        },

                        success: function (data) {
                            location.reload();
                        }
                    });
                },

                deactivate: function (purchase_code, domain) {
                    $.ajax({
                        method: 'POST',
                        url: window.ajaxurl,
                        dataType: 'json',
                        data: {
                            action: 'stec_ajax_action',
                            purchase_code: purchase_code,
                            security: window.stec_ajax_nonce,
                            server_name: domain,
                            task: 'deactivate_license'
                        },

                        error: function (xhr, status, thrown) {
                            console.log(xhr + " " + status + " " + thrown);
                        },

                        success: function (data) {
                            location.reload();

                        }
                    });
                }
            };

        })();

        (function repeater() {

            if ( $('input[name="rrule"]').length <= 0 ) {
                return;
            }

            var the_rule = '';
            var rr_text = '';

            $('.stec-repeater-popup-bg').on('click', function (e) {
                $('.stec-repeater-popup-bg').hide();
                $('.stec-repeater-popup').hide();
            });

            $('.set-repeater-button').on('click', function (e) {

                e.preventDefault();

                $('.stec-repeater-popup-bg').show();
                $('.stec-repeater-popup').show();

                // Show the advanced settings if is advanced prop
                if ( $('input[name="is_advanced_rrule"]').val() == '1' ) {
                    $('.stec-repeater-popup tbody').not('.stec-repeater-popup-repeat-advanced').hide();
                    $('.stec-repeater-popup .stec-repeater-popup-repeat-advanced').show();
                } else {
                    $('input[name="advanced_rrule"]').val('');
                    $('.stec-repeater-popup tbody').not('.stec-repeater-popup-repeat-advanced').show();
                    $('.stec-repeater-popup .stec-repeater-popup-repeat-advanced').hide();
                }

                rrule_to_settings();

            });

            $('select[name="repeat_freq"]').on('change', function () {
                check_visible_options();
            });

            $('input[name="repeat_endson"]').on('change', function () {
                check_disabled_fields();
            });

            $('.stec-repeater-popup-endson-options #repeat_end_date').datepicker({
                showAnim: 0,
                dateFormat: "yy-mm-dd",
                minDate: $('.stachethemes-admin-section-tab[data-tab="general"] .input-date[name="end_date"]').val(),
                onSelect: function () {
                    update_repeat_rules();
                }
            });

            $('.stec-repeater-popup-endson-options #repeat_end_date').datepicker("setDate", new Date());
            $('.stachethemes-admin-section-tab[data-tab="general"] .input-date[name="end_date"]').on('change', function () {
                $('.stec-repeater-popup-endson-options #repeat_end_date').datepicker('option', 'minDate', $(this).val());
            });

            // accepts numbers only
            $('.stec-repeater-popup-endson-options input[name="repeat_occurences"]').on('keyup', function (a) {
                var val = $(this).val();
                $(this).val(val.replace(/[^0-9]/g, ''));
            });

            $('.stec-repeater-popup').on('keyup change', 'select, input:not([name="advanced_rrule"])', function () {
                update_repeat_rules();
            });

            $('.stec-repeater-popup').on('keydown', function (e) {

                if ( e.ctrlKey && e.altKey && e.shiftKey && e.keyCode === 82 ) {
                    e.preventDefault();

                    $('.stec-repeater-popup tbody').toggle();

                    if ( $('.stec-repeater-popup-repeat-advanced').is(':visible') ) {
                        $('.stec-repeater-popup-exdate-menu').appendTo('.stec-repeater-popup-repeat-advanced');
                    } else {
                        $('.stec-repeater-popup-exdate-menu').insertBefore($('.stec-repeater-popup-repeat-advanced').prev().find('tr').last());
                    }


                }

            });

            $('.stec-repeater-popup button').first().on('click', function (e) {
                e.preventDefault();

                update_repeat_rules();

                var advanced_rrule_string = $('input[name="advanced_rrule"]').val().replace(/\s+/g, '');

                if ( advanced_rrule_string != '' ) {
                    the_rule = new window.RRule.fromString(advanced_rrule_string);
                    $('input[name="is_advanced_rrule"]').val(1);
                    rr_text = the_rule.toText();
                    rr_text = rr_text.charAt(0).toUpperCase() + rr_text.slice(1);
                } else {
                    $('input[name="is_advanced_rrule"]').val(0);
                }

                $('input[name="rrule"]').val(the_rule.toString().replace('T000000Z', '')); // ignore timezone
                $('.stec-repeater-summary span').text(rr_text);

                $('.stec-repeater-popup-bg').hide();
                $('.stec-repeater-popup').hide();

            });

            $('.stec-repeater-popup button').last().on('click', function (e) {
                e.preventDefault();

                $('.stec-repeater-popup-bg').hide();
                $('.stec-repeater-popup').hide();
            });

            $('.stec-repeater-popup #stec-repeater-popup-exdate-datepicker').datepicker({
                showAnim: 0,
                dateFormat: "yy-mm-dd",
                altFormat: "yymmdd",
                altField: "#stec-repeater-popup-exdate-datepicker-exdate-value",
                onSelect: function () {

                }
            });

            $('.stec-repeater-popup #stec-add-exdate').on('click', function () {

                var template = $('.stec-repeater-popup .stec-repeater-popup-exdate-datelist-template')[0].outerHTML;

                var date = $('#stec-repeater-popup-exdate-datepicker').val();
                var altDate = $('#stec-repeater-popup-exdate-datepicker-exdate-value').val();

                if ( !date ) {
                    return false;
                }

                var html = $(template);

                html.removeClass('stec-repeater-popup-exdate-datelist-template')
                        .html(function (index, html) {
                            return html
                                    .replace(/stec_replace_altdate/g, altDate)
                                    .replace(/stec_replace_date/g, date);
                        });

                $(html).appendTo($('.stec-repeater-popup .stec-repeater-popup-exdate-datelist'));

            });

            $('.stec-repeater-popup').on('click', '.stec-remove-exdate', function () {
                $(this).parents('li').first().remove();
            });

            function check_visible_options() {

                switch ( $('select[name="repeat_freq"]').val() ) {

                    case '0':
                        $('.stec-repeater-popup-weekdays').hide();
                        break;
                    case '2':
                        $('.stec-repeater-popup-weekdays').show();
                        break;
                    default:
                        $('.stec-repeater-popup-weekdays').hide();
                        $('.stec-repeater-popup-exdate-menu').show();
                }
            }

            function check_disabled_fields() {

                switch ( $('input[name="repeat_endson"]').filter(":checked").val() ) {

                    case '0' :
                        //never
                        $('input[name="repeat_occurences"]').attr('disabled', 'disabled');
                        $('input[name="repeat_ends_on_date"]').attr('disabled', 'disabled');
                        break;

                    case '1' :
                        // after n
                        $('input[name="repeat_occurences"]').removeAttr('disabled');
                        $('input[name="repeat_ends_on_date"]').attr('disabled', 'disabled');
                        break;

                    case '2' :
                        // on date
                        $('input[name="repeat_occurences"]').attr('disabled', 'disabled');
                        $('input[name="repeat_ends_on_date"]').removeAttr('disabled');
                        break;

                }

            }

            function rrule_to_settings() {
                if ( $('input[name="is_advanced_rrule"]').val() != '1' ) {

                    // These settings apply only for basic repeater

                    var rrule_string = $('input[name="rrule"]').val();

                    if ( rrule_string == '' ) {
                        $('.stec-repeater-summary span').text('-');
                        return;
                    }

                    var rrule = new window.RRule.fromString(rrule_string);

                    // set freq
                    $('select[name="repeat_freq"]').val(Math.abs(4 - rrule.origOptions.freq)); // invert order 4,3,2,1 to 1,2,3,4...

                    if ( rrule.origOptions.byweekday ) {

                        var weekdays = ['MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU'];

                        $('.stec-repeater-popup-repeat-on input[type="checkbox"]').prop('checked', '');

                        $(rrule.origOptions.byweekday).each(function (a, b) {

                            var val = (weekdays[b.weekday]);

                            $('.stec-repeater-popup-repeat-on input[name="' + val + '"]').prop('checked', 'checked');

                        });

                    }

                    if ( rrule.origOptions.interval ) {
                        $('select[name="repeat_gap"]').val(rrule.origOptions.interval);
                    } else {
                        $('select[name="repeat_gap"]').val(0);
                    }

                    if ( rrule.origOptions.count ) {
                        $('select[name="repeat_gap"]').val(rrule.origOptions.interval);

                        $('input[name="repeat_occurences"]')
                                .removeAttr('disabled')
                                .val(rrule.origOptions.count);

                        $('#stec-repeater-popup-repeat-endson-after-n').prop('checked', 'checked');

                        $('input[name="repeat_ends_on_date"]').prop('disabled', 'disabled');
                    }

                    if ( rrule.origOptions.until ) {
                        $('.stec-repeater-popup-endson-options #repeat_end_date').datepicker("setDate", rrule.origOptions.until);
                        $('#stec-repeater-popup-repeat-endson-date').prop('checked', 'checked');
                        $('input[name="repeat_ends_on_date"]').removeProp('disabled', 'disabled');
                        $('input[name="repeat_occurences"]').prop('disabled', 'disabled');
                    }

                    rr_text = rrule.toText();
                    rr_text = rr_text.charAt(0).toUpperCase() + rr_text.slice(1);

                    $('.stec-repeater-summary span').text(rr_text);
                    $('.stec-repeater-popup-repeat-summary').text(rr_text);

                } else {

                    // Advanced rrule sets only text
                    
                    the_rule = new window.RRule.fromString($('input[name="rrule"]').val());
                    rr_text = the_rule.toText();
                    rr_text = rr_text.charAt(0).toUpperCase() + rr_text.slice(1);
                    
                    // Move exdate to advanced menu
                    $('.stec-repeater-popup-exdate-menu').appendTo('.stec-repeater-popup-repeat-advanced');

                    $('.stec-repeater-summary span').text(rr_text);

                }

                // exdate
                var exdate_string = $('input[name="exdate"]').val();
                if ( exdate_string != '' ) {

                    $('.stec-repeater-popup .stec-repeater-popup-exdate-datelist li')
                            .not('.stec-repeater-popup-exdate-datelist-template').remove();

                    var exdate_array = exdate_string.split(',');

                    $.each(exdate_array, function () {

                        var template = $('.stec-repeater-popup .stec-repeater-popup-exdate-datelist-template')[0].outerHTML;

                        var altDate = this;
                        var date = altDate.substr(0, 4) + '-' + altDate.substr(4, 2) + '-' + altDate.substr(6, 2);

                        var html = $(template);

                        html.removeClass('stec-repeater-popup-exdate-datelist-template')
                                .html(function (index, html) {
                                    return html
                                            .replace(/stec_replace_date/g, date)
                                            .replace(/stec_replace_altdate/g, altDate);
                                });

                        $(html).appendTo($('.stec-repeater-popup .stec-repeater-popup-exdate-datelist'));

                    });

                }

                check_visible_options();

            }

            function update_repeat_rules() {

                var freq = $('.stec-repeater-popup').find('select[name="repeat_freq"]').val();
                var byweekday = [];

                switch ( freq ) {
                    case '0' :
                        freq = false;
                        break;
                    case '1' :
                        freq = window.RRule.DAILY;
                        break;
                    case '2' :
                        freq = window.RRule.WEEKLY;

                        $('.stec-repeater-popup-repeat-on')
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

                var interval = $('.stec-repeater-popup').find('select[name="repeat_gap"]').val();

                var count = false;
                var until = false;

                switch ( $('input[name="repeat_endson"]').filter(":checked").val() ) {

                    case '0' :
                        //never
                        count = false;
                        until = false;
                        break;

                    case '1' :
                        // after n
                        count = $('input[name="repeat_occurences"]').val();
                        until = false;

                        break;

                    case '2' :
                        // on date
                        count = false;
                        until = new Date($('input[name="repeat_ends_on_date"]').val());
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

                if ( freq !== false ) {
                    // Set rule
                    the_rule = new window.RRule(rr_options);

                    rr_text = the_rule.toText();
                    rr_text = rr_text.charAt(0).toUpperCase() + rr_text.slice(1);

                } else {
                    // Empty rrule
                    the_rule = new window.RRule();
                    rr_text = '-';
                }

                $('.stec-repeater-popup-repeat-summary').text(rr_text);

                var exdates = $('.stec-repeater-popup .stec-repeater-popup-exdate-datelist li')
                        .not('.stec-repeater-popup-exdate-datelist-template');

                if ( exdates.length > 0 ) {

                    var exdates_array = [];

                    $(exdates).each(function () {
                        var value = $(this).find('.stec-repeater-popup-exdate-datelist-submit-value').text();
                        exdates_array.push(value);
                    });

                    $('input[name="exdate"]').val(exdates_array.join(','));
                } else {
                    $('input[name="exdate"]').val('');
                }

            }

            check_disabled_fields();
            rrule_to_settings();

        })();
        
    });

})(jQuery);