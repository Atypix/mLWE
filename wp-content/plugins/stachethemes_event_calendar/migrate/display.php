<?php

namespace Stachethemes\Stec\Migrate;




use Stachethemes\Stec\Admin_Html;

global $plugin_page;
?>

<div class="wrap" id="stec-migrate">

    <?php wp_print_request_filesystem_credentials_modal(); ?>

    <h1><?php _e('Stachethemes Event Calendar - Migrate', 'stec'); ?></h1>

    <p><?php _e('Click "migrate" to start transfering your events to the new database.', 'stec'); ?></p>
    
    <p id="stec-status"></p>

    <?php Admin_Html::html_form_start('?page=' . $plugin_page); ?>
    <button class="button button-primary button-large" id="stec-migrate" type="submit" name="task" value="stec_migrate"><?php _e('Migrate', 'stec'); ?></button>
    <button class="button button-secondary button-large" id="stec-migrate" type="submit" name="task" value="stec_forget_migrate"><?php _e('Forget', 'stec'); ?></button>
    <?php Admin_Html::html_form_end(); ?>
</div>