(function ($) {

    'use strict';

    $(function () {

        var nonce = window.stecUpdaterSettings.nonce;
        var expectHash = '';

        var FileSystemCred = {
            e: null,
            data: null,
            hasDialog: function () {
                return $('#request-filesystem-credentials-dialog').length > 0;
            },
            showDialog: function () {
                $('#request-filesystem-credentials-dialog').show();
            },
            hideDialog: function () {
                $('#request-filesystem-credentials-dialog').hide();
            },
            mergeData: function (data) {
                $.each(this.data, function () {
                    data[this.name] = this.value;
                });
                return data;
            },
            setErrorMessage: function (message) {
                $('#request-filesystem-credentials-dialog').find('.notice-error').remove();
                $('<div class="notice notice-alt notice-error"><p>' + message + '</p></div>')
                        .insertAfter('#request-filesystem-credentials-title');
            },
            Controller: function () {
                var parent = this;
                $('#request-filesystem-credentials-dialog').find('.cancel-button').on('click', function () {
                    FileSystemCred.hideDialog();
                });
                $('#request-filesystem-credentials-dialog form').on('submit', function (e) {
                    e.preventDefault();
                    parent.data = $(this).serializeArray();
                    $(parent.e).trigger('click');
                    parent.hideDialog();
                });
            }
        };

        var checkForUpdate = function (callback) {

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'stec_updater',
                    task: 'check_update',
                    security: nonce
                },
                dataType: 'json',
                beforeSend: function () {
                    $('#stec-check-for-update').hide();
                    $('#stec-status').text('Checking for updates...');
                },
                success: function (result) {

                    if ( typeof callback === 'function' ) {
                        callback(result);
                    }

                }
            });
        };

        var downloadUpdate = function (callback) {

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'stec_updater',
                    task: 'download_update',
                    security: nonce
                },
                dataType: 'json',
                beforeSend: function () {
                    $('#stec-check-for-update').hide();
                    $('#stec-download-install-update').hide();
                    $('#stec-status').text('Downloading update...');
                },
                success: function (result) {

                    if ( result.error == 1 ) {

                        if ( result.data && result.data.file_system_error == 1 ) {
                            FileSystemCred.data = null;
                            FileSystemCred.showDialog();
                            FileSystemCred.setErrorMessage(result.error_msg);
                        } else {
                            $('#stec-status').text(result.error_msg);
                        }

                        return;
                    }

                    if ( result.data.success === 1 ) {
                        if ( typeof callback === 'function' ) {
                            callback(result);
                        }
                    }

                }
            });

        };

        var installUpdate = function (filename, callback) {

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'stec_updater',
                    task: 'install_update',
                    stec_filename: filename,
                    security: nonce
                },
                dataType: 'json',
                success: function (result) {
                    if ( typeof callback === 'function' ) {
                        callback(result);
                    }
                }
            });
        };

        $('#stec-check-for-update').on('click', function () {

            checkForUpdate(function (result) {
                if ( result.data && result.data.has_update == 1 ) {
                    $('#stec-check-for-update').hide();
                    $('#stec-download-install-update').show();
                    $('#stec-status').text('New version ' + result.data.version + ' is available for download');

                    expectHash = result.data.hash;
                } else {
                    $('#stec-status').text('You have the latest version');
                }
            });

        });

        $('#stec-download-install-update').on('click', function (e) {
            
            e.preventDefault();

            if ( FileSystemCred.hasDialog() && FileSystemCred.data === null ) {
                FileSystemCred.showDialog();
                FileSystemCred.e = this;
                return;
            }

            downloadUpdate(function (result) {

                if ( result.data.success === 1 && result.data.hash === expectHash && result.data.filename ) {

                    installUpdate(result.data.filename, function (result) {
                        if ( result.data && result.data.success == 1 ) {
                            location.reload();
                            return;
                        }

                        if ( result.error === 1 && result.error_msg ) {
                            $('#stec-status').text(result.error_msg);
                        } else {
                            $('#stec-status').text('Error updating plugin');
                        }
                    });

                } else {

                    if ( result.data.error == 1 && result.data.error_msg ) {
                        $('#stec-status').text(result.data.error_msg);
                    } else {
                        $('#stec-status').text('Error downloading update');
                    }
                }
            });

        });

        FileSystemCred.Controller();

    });

})(jQuery);