<?php

namespace Stachethemes\Stec;
?>
<div class="stec-share">

    <div class="stec-share-block">
        <span class="stec-share-block-title stec-share-fw"><?php _e('Share event', 'stec'); ?></span>

        <span class="stec-share-close"><i class="fa fa-times"></i></span>

        <span class="stec-share-fw"><?php _e('Permalink', 'stec'); ?></span>

        <div class="stec-share-flex">
            <input name="stec_permalink" type="text" value="" /><button data-copy-permalink title="<?php _e('Copy to clipboard', 'stec'); ?>" type="button"><i class="fa fa-clipboard"></i></button>
        </div>

        <?php if ( Settings::get_admin_setting_value('stec_menu__general', 'allow_embedding') == '1' ) : ?>

            <span class="stec-share-fw"><?php _e('Embed code', 'stec'); ?></span>

            <div class="stec-share-flex">
                <textarea name="stec_embed"></textarea><button data-copy-embed title="<?php _e('Copy to clipboard', 'stec'); ?>" type="button"><i class="fa fa-clipboard"></i></button>
            </div>

            <span class="stec-share-fw"><?php _e('Width in pixels (Height will be calculated automatically)', 'stec'); ?></span>
            <input name="stec_embed_width" type="text" value="400" />

        <?php endif; ?>

    </div>
</div>