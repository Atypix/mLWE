(function ($) {

    'use strict';

    $(function () {

        var $status = $('#stec-status');

        var nonce = window.stecMigrateSettings.nonce;

        var calendars = null;
        var next_calendar = 0;

        function doAjax(task, custom_data, callback) {

            var data = $('#stec-migrate form').serializeArray();

            data.push({
                name: 'task',
                value: task
            });

            data.push({
                name: 'action',
                value: 'stec_migrate_ajax_action'
            });

            if ( custom_data ) {
                $.each(custom_data, function (k, v) {
                    data.push({
                        name: k,
                        value: v
                    });
                });
            }

            $.ajax({
                method: 'POST',
                url: ajaxurl,
                data: data,
                dataType: 'json',

                beforeSend: function (xhr) {
                },

                error: function (xhr, status, thrown) {
                    console.log(xhr + " " + status + " " + thrown);
                    statusMsg(xhr + " " + status + " " + thrown);
                },

                success: function (response) {

                    if ( typeof callback === 'function' ) {
                        callback(response);
                    }

                },

                complete: function () {
                }
            });
        }

        function statusMsg(text) {

            $('<span>' + text + ' </span><br>').appendTo($status);

        }

        function migrate(calendar, offset, new_calendar_id) {

            if ( !calendars ) {
                statusMsg('Calendars not found');
                return;
            }

            if ( !calendar ) {
                calendar = calendars[0];
            }

            if ( !offset ) {
                offset = 0;
            }

            if ( !new_calendar_id ) {
                statusMsg('Migrating calendar: <strong>' + calendar.title + ' ...</strong>');
            }

            var data = {
                calendar_id: calendar.id,
                offset: offset,
                new_calendar_id: new_calendar_id ? new_calendar_id : null
            };

            doAjax('stec_ajax_migrate', data, function (r) {

                if ( !r || !r.data || !r.data.calendar_id ) {
                    statusMsg('Unexpected error');
                    return;
                }

                var completed = parseInt(r.data.completed, 10);
                var offset = parseInt(r.data.next_offset, 10);

                statusMsg(completed + '% completed');

                if ( completed < 100 ) {
                    migrate(calendar, offset, r.data.new_calendar_id);
                } else {
                    next_calendar++;

                    if ( typeof calendars[next_calendar] !== 'undefined' ) {
                        calendar = calendars[next_calendar];
                        migrate(calendar);
                    } else {
                        statusMsg('Database migration complete');
                        forget();
                    }
                }
            });
        }

        function forget() {

            doAjax('stec_ajax_migrate_forget', null, function (r) {
                window.location.href = r.data.location;
            });
        }

        $('#stec-migrate form button:first').on('click', function (e) {
            e.preventDefault();

            $('#stec-migrate form').hide();

            doAjax('stec_ajax_db_status', null, function (r) {

                calendars = r.data.calendars;

                migrate();
            });

        });


    });

})(jQuery);