<?php

namespace Stachethemes\Stec\Updater;




use Stachethemes\Stec\Admin_Helper;
use Stachethemes\Stec\stachethemes_ec_main;
use Exception;

add_action('wp_ajax_stec_updater', function() {

    header('Content-Type: application/json');

    $task = filter_input(INPUT_POST, 'task', FILTER_DEFAULT);

    if ( check_ajax_referer('stec-updater-nonce', 'security', false) === false ) {
        $response = new response_object();
        $response->set_error(1);
        $response->set_error_msg(__('Invalid nonce', 'stec'));
        $response->get();
        exit;
    }

    $stachethemes_ec = stachethemes_ec_main::get_instance();

    switch ( $task ) {

        case 'check_update' :

            $response        = new response_object();
            $current_version = Admin_Helper::get_plugin_version();
            $remote          = wp_remote_get('https://api.stachethemes.com/stec/updater/version');
            $remote_content  = json_decode($remote['body']);

            if ( version_compare($remote_content->data->version, $current_version, '>') ) {
                $response->set_data(array(
                        'has_update' => 1,
                        'version'    => $remote_content->data->version,
                        'hash'       => $remote_content->data->hash
                ));
            } else {
                $response->set_data(array(
                        'has_update' => 0,
                ));
            }

            $response->get();

            break;

        case 'download_update' :

            $response = new response_object();

            try {

                $lcns          = $stachethemes_ec->lcns();
                $purchase_code = isset($lcns['purchase_code']) ? $lcns['purchase_code'] : '';
                if ( !$purchase_code ) {
                    throw new Exception(__('Invalid purchase code!', 'stec'));
                }

                // Download file
                $remote         = wp_remote_get('https://api.stachethemes.com/stec/updater/download/' . $purchase_code);
                $cd             = $remote['headers']['content-disposition'];
                $filename       = str_replace('attachment; filename=', '', $cd);
                $updates_folder = $stachethemes_ec->get_path('UPDATER') . '/downloads';

                if ( wp_remote_retrieve_response_code($remote) === 401 ) {
                    throw new Exception(__('Unauthorized!', 'stec'));
                }

                if ( wp_remote_retrieve_response_code($remote) === 404 ) {
                    throw new Exception(__('File not found!', 'stec'));
                }

                // Check FS Credentials
                if ( !$stachethemes_ec->get_file_system_credentials() ) {
                    $response->set_data(array(
                            'file_system_error' => 1
                    ));
                    throw new Exception(__('Unable to connect to the file system. Check your credentials.', 'stec'));
                }

                $fs       = $stachethemes_ec->get_file_system();
                $filepath = $updates_folder . DIRECTORY_SEPARATOR . $filename;

                if ( !$fs->is_dir($updates_folder) ) {
                    $fs->mkdir($updates_folder);
                }

                if ( false === $fs->put_contents($filepath, $remote['body'], FS_CHMOD_FILE) ) {
                    if ( is_wp_error($fs->errors) && $fs->errors->get_error_code() ) {
                        var_dump($fs->errors->get_error_message());
                    }

                    throw new Exception(__('Unable to save update', 'stec'));
                }

                $response->set_data(array(
                        'success'  => 1,
                        'filename' => $filename,
                        'hash'     => md5($remote['body'])
                ));

                $response->get();
            } catch ( Exception $ex ) {

                $response->set_error(1);
                $response->set_error_msg($ex->getMessage());
                $response->get();
                exit;
            }

            break;

        case 'install_update' :

            $response = new response_object();
            $filename = Admin_Helper::post('stec_filename', null);

            try {

                // Check FS Credentials
                if ( !$stachethemes_ec->get_file_system_credentials() ) {
                    $response->set_data(array(
                            'file_system_error' => 1
                    ));
                    throw new Exception(__('Unable to connect to the file system. Check your credentials.', 'stec'));
                }

                $updates_folder = $stachethemes_ec->get_path('UPDATER') . DIRECTORY_SEPARATOR . 'downloads';
                $fs             = $stachethemes_ec->get_file_system();
                $file           = $updates_folder . DIRECTORY_SEPARATOR . $filename;
                $to             = $fs->wp_plugins_dir();

                if ( !$fs->is_file($file) ) {
                    throw new Exception(__('File not found.', 'stec'));
                }

                /*
                  $main_path = $stachethemes_ec->get_path('ROOT');
                  $paths     = array(
                  $main_path . '/admin',
                  ...
                  ...
                  );

                  foreach ( $paths as $path ) {
                  $fs->delete($path, true);
                  }
                 */

                $result = unzip_file($file, $to);

                if ( $result !== true ) {
                    throw new Exception(__('Unable to extract archive.', 'stec'));
                }

                activate_plugin(\Stachethemes\Stec\STACHETHEMES_EC_FILE__, null, true, false);

                $_SESSION['stec_updater_success_message'] = 1;

                $response->set_data(array(
                        'success' => 1
                ));

                $response->get();
            } catch ( Exception $ex ) {

                $response->set_error(1);
                $response->set_error_msg($ex->getMessage());
                $response->get();
                exit;
            }

            break;
    }

    exit;
});
