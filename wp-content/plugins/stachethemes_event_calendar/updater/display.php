<?php

namespace Stachethemes\Stec\Updater;




use Stachethemes\Stec\Admin_Helper;
?>

<div class="wrap">

    <?php wp_print_request_filesystem_credentials_modal(); ?>

    <h1><?php _e('Stachethemes Event Calendar - Updater', 'stec'); ?></h1>

    <p><?php printf(__('Currently installed version is %s', 'stec'), Admin_Helper::get_plugin_version()) ?></p>

    <p id="stec-status"></p>

    <button class="button button-secondary button-large" id="stec-check-for-update" type="button"><?php _e('Check for updates', 'stec'); ?></button>
    <button class="button button-primary button-large" id="stec-download-install-update" type="button"><?php _e('Download & Install', 'stec'); ?></button>
</div>