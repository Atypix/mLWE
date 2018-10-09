(function ($) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
	 *
	 * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
	 *
	 * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */


    $(document).ready(function (a) {

        $('#upload-btn').click(function (e) {
            e.preventDefault();
            var image = wp.media({
                title: 'Upload Image',
                // mutiple: true if you want to upload multiple files at once
                multiple: false
            }).open()
                .on('select', function (e) {
                    // This will return the selected image from the Media Uploader, the result is an object
                    var uploaded_image = image.state().get('selection').first();
                    // We convert uploaded_image to a JSON object to make accessing it easier
                    // Output to the console uploaded_image
                    console.log(uploaded_image);
                    var attachment_url = uploaded_image.toJSON().url;
                    // Let's assign the url value to the input field
                    $('#link').html(attachment_url);
                    $('#attachment_url').val(attachment_url);
                });
        });


        $(document).on('click', '.addfilter', function (e) {
            e.preventDefault();
            var controlForm = $('.migrator-export form:first'),
                currentEntry = $(this).parents('.filters:first'),
                //newEntry = $(currentEntry.clone()).appendTo(controlForm);
                newEntry = $(currentEntry.clone()).appendTo($('.fields'));

            newEntry.find('input').val('');
            controlForm.find('.filters:not(:last) .addfilter')
                .removeClass('addfilter').addClass('removefilter')
                .html('<span class="dashicons dashicons-dismiss"></span>');
        }).on('click', '.removefilter', function (e) {
            $(this).parents('.filters:first').remove();

            e.preventDefault();
            return false;
        });

        $("#export").click(function (e) {
            e.preventDefault();
            recursiveAjaxCall(1);

        });

        $('#import').click(function (e) {
            e.preventDefault();
            recursiveAjaxCall_import(1);
        });

        function recursiveAjaxCall_import(step,extract_to) {
            var attachment_url = $("#attachment_url").val();
            var progress = document.getElementById('bar');
           $.ajax({
               url: webtoffee_migrator_ajax_import.webtf_ajax_import,
               type: 'POST',
               dataType: "json",
               data: {
                   action: 'import_migration',
                   attachment_url: attachment_url,
                   step: step,
                   extract_to: extract_to
               },
                success: function (step) {
                    console.log(step);

                    if (step.step < 3) {
                        //recursively call this function if the data received from backend is less than the input number
                        recursiveAjaxCall_import(step.step,step.extract_to);
                    }

                    progress.value = step.val;
                    $('#percentage').text(step.val);
                    $("#progress_info").show();
                    $("#progress_info").html(step.msg);

                },
                error: function (error) {
                    alert("Importing Failed"); //TODO Optimize based on requirements
                }
            });
        }




        function recursiveAjaxCall(step,date) {
            var export_data = $("#migrator_export").serializeArray();
            var progress = document.getElementById('bar');

            $.ajax({
                url: webtoffee_migrator_ajax_export.webtf_ajax_export,
                type: 'POST',
                dataType: "json",
                data: {
                    action: 'export_migration',
                    export_data: export_data,
                    step: step,
                    date: date
                },
                success: function (step) {
                    console.log(step);

                    if (step.step < 5) {
                        //recursively call this function if the data received from backend is less than the input number
                        recursiveAjaxCall(step.step,step.date);
                    }

                    progress.value = step.val;
                    $('#percentage').text(step.val);
                    /*$("#alerts").append("<div class=\"notice notice-success is-dismissible\"> \n" +
                        "\t<p><strong>" +step.msg + " Exported Successfully</strong></p>\n" +
                        "\t<button type=\"button\" class=\"notice-dismiss\">\n" +
                        "\t\t<span class=\"screen-reader-text\">Dismiss this notice.</span>\n" +
                        "\t</button>\n" +
                        "</div>");*/
                    $("#progress_info").append(step.msg + " Export Complete ");
                    $('#progress_info').delay(10000).fadeOut('slow');


                    if (typeof step.url !== 'undefined') {
                        window.location = step.url;
                    }
                    },
                error: function (error) {
                    alert("Exporting Failed"); //TODO Optimize based on requirements
                }
            });
        }


        var interval = null;
        $('#loadingDiv')
            .hide()
            .ajaxStart(function () {
                var progress_value = 0;
                var progress = document.getElementById('bar');
                $('#percentage').text(0);
                progress.value = 0;
                $(this).show();
                $('#export').attr('disabled', 'true');

            })
            .ajaxStop(function () {
                clearInterval(interval);
                $(this).hide();
                $('#export').removeAttr('disabled');

            });


        var import_interval = null;
        $('#import_progress')
            .hide()
            .ajaxStart(function () {
                var progress_value = 0;
                var progress = document.getElementById('bar');
                $('#percentage').text(0);
                progress.value = 0;
                $(this).show();
                $('#import').attr('disabled', 'true');
            })
            .ajaxStop(function () {
                clearInterval(import_interval);
                $(this).hide();
                $('#import').removeAttr('disabled');
            });





        $('.deleteButton').click(function (e) {
            e.preventDefault();

            var td = $(this).parent().parent();

            var filename = $(this).data("filename");
            $.ajax({
                url: webtoffee_migrator_ajax_delete.webtf_ajax_delete,
                type: 'POST',
                data: {
                    action: 'delete_migration',
                    filename: filename
                },
                success: function (response) {
                    td.hide(500);
                    $("#alerts").append("<div class=\"notice notice-success is-dismissible\"> \n" +
                        "\t<p><strong>Deleted Successfully</strong></p>\n" +
                        "\t<button type=\"button\" class=\"notice-dismiss\">\n" +
                        "\t\t<span class=\"screen-reader-text\">Dismiss this notice.</span>\n" +
                        "\t</button>\n" +
                        "</div>");
                },
                error: function (error) {
                    alert("Deletion Failed"); //TODO Optimize based on requirements
                }
            });
        });


    });
})(jQuery);
