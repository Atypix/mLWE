<?php

namespace Stachethemes\Stec;




if ( !$this instanceof stachethemes_ec_main ) {
    die('File is loaded incorrectly!');
}





// Check for awaiting approval events and display notification if any

$count = Events::get_aaproval_count();

if ( $count && $count > 0 ) :

    add_action('admin_notices', function() use($count) {
        ?>
        <div class="notice notice-info is-dismissible">

            <p>
                <?php
                if ( $count == 1 ) {
                    printf(__('You have %d new event awaiting approval!', 'stec'), $count);
                } else {
                    printf(__('You have %d new events awaiting approval!', 'stec'), $count);
                }
                ?>
            </p>
        </div>
        <?php
    });

endif;