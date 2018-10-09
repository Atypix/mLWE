<?php

namespace Stachethemes\Stec;
?>
<div class="stachethemes-admin-section-tab" data-tab="guests">

    <?php
    Admin_Html::html_info(__('Guests List', 'stec'));

    if ( $event ) : foreach ( $event->get_guests() as $guest ) :
            ?>

            <div class="stachethemes-admin-guests-guest">

                <div class="stachethemes-admin-guests-guest-head">
                    <p class="stachethemes-admin-guests-guest-title"><span></span><span><?php _e('Add Guest (optional)', 'stec'); ?></span></p>

                    <div>
                        <?php
                        Admin_Html::html_button(__('Expand', 'stec'), '', true, "light-btn expand");
                        Admin_Html::html_button(__('Collapse', 'stec'), '', true, "light-btn collapse");
                        Admin_Html::html_button(__('Delete', 'stec'), '', true, "light-btn delete");
                        ?>
                    </div>
                </div>

                <div class="stachethemes-admin-guests-toggle-wrap">

                    <?php
                    Admin_Html::html_info(__('Photo', 'stec'));
                    Admin_Html::html_add_image('guests[0][photo]', $guest->photo, __('Add Photo', 'stec'), false, true);

                    Admin_Html::html_info(__('Name', 'stec'));
                    Admin_Html::html_input('guests[0][name]', $guest->name, '', __('Name', 'stec'), false);

                    Admin_Html::html_info(__('About', 'stec'));
                    Admin_Html::html_textarea('guests[0][about]', $guest->about, '', __('About your guest', 'stec'), false);

                    Admin_Html::html_info(__('Social Links', 'stec'));


                    foreach ( $guest->links as $link ) :

                        if ( $link['link'] == "" ) {
                            continue;
                        }
                        ?>
                        <div class="stachethemes-admin-section-flex-guest-social">
                            <?php
                            Admin_Html::html_select('guests[0][social][0][ico]', Admin_Helper::social_array(), $link['ico']);
                            Admin_Html::html_input('guests[0][social][0][link]', $link['link'], '', __('Link', 'stec'));
                            ?>
                            <i class="stachethemes-admin-guests-social-remove fa fa-times"></i>
                        </div>
                        <?php
                    endforeach;
                    ?>

                    <div class="stachethemes-admin-section-flex-guest-social">
                        <?php
                        Admin_Html::html_select('guests[0][social][0][ico]', Admin_Helper::social_array(), '');
                        Admin_Html::html_input('guests[0][social][0][link]', '', '', __('Link', 'stec'));
                        ?>
                        <i class="stachethemes-admin-guests-social-remove fa fa-times"></i>
                    </div>

                    <?php
                    Admin_Html::html_button(__('Add social link', 'stec'), false, false, 'light-btn add-guests-soclink');
                    ?>

                </div>

            </div>

        <?php
        endforeach;
    endif;
    ?>

    <!-- guest template --> 
    <div class="stachethemes-admin-guests-guest stachethemes-admin-guests-guest-template">

        <div class="stachethemes-admin-guests-guest-head">
            <p class="stachethemes-admin-guests-guest-title"><span></span><span><?php _e('Add Guest (optional)', 'stec'); ?></span></p>

            <div>
                <?php
                Admin_Html::html_button(__('Expand', 'stec'), '', true, "light-btn expand");
                Admin_Html::html_button(__('Collapse', 'stec'), '', true, "light-btn collapse");
                Admin_Html::html_button(__('Delete', 'stec'), '', true, "light-btn delete");
                ?>
            </div>
        </div>

        <div class="stachethemes-admin-guests-toggle-wrap">

            <?php
            Admin_Html::html_info(__('Photo', 'stec'));
            Admin_Html::html_add_image('guests[0][photo]', false, __('Add Photo', 'stec'), false, true);

            Admin_Html::html_info(__('Name', 'stec'));
            Admin_Html::html_input('guests[0][name]', '', '', __('Name', 'stec'), false);

            Admin_Html::html_info(__('About', 'stec'));
            Admin_Html::html_textarea('guests[0][about]', '', '', __('About your guest', 'stec'), false);

            Admin_Html::html_info(__('Social Links', 'stec'));
            ?>
            <div class="stachethemes-admin-section-flex-guest-social">
                <?php
                Admin_Html::html_select('guests[0][social][0][ico]', Admin_Helper::social_array(), '', true);
                Admin_Html::html_input('guests[0][social][0][link]', '', '', __('Link', 'stec'));
                ?>
                <i class="stachethemes-admin-guests-social-remove fa fa-times"></i>
            </div>
            <?php
            Admin_Html::html_button(__('Add social link', 'stec'), false, false, 'light-btn add-guests-soclink');
            ?>
        </div>

    </div>


    <?php
    Admin_Html::html_button(__('Add Guest', 'stec'), false, false, 'add-guests-guest');
    ?>

</div>
