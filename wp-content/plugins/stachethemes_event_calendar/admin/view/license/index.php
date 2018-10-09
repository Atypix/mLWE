<?php

namespace Stachethemes\Stec;




global $plugin_page;

$stachethemes_ec_main = stachethemes_ec_main::get_instance();

// GET
switch ( Admin_Helper::get("task") ) {
    
}

// POST
switch ( Admin_Helper::post("task") ) {
    
}

// VIEW
switch ( Admin_Helper::get("view") ) :

    default:
        ?>
        <script type="text/javascript">
            var stec_ajax_nonce = "<?php echo wp_create_nonce("stec-nonce-string"); ?>";
        </script>
        <?php
        include __dir__ . '/license.php';
endswitch;



