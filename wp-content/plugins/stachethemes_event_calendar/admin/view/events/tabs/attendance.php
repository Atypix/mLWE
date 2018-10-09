<?php

namespace Stachethemes\Stec;
?>
<div class="stachethemes-admin-section-tab" data-tab="attendance">

    <?php
    Admin_Html::html_info(__('Invite by e-mail', 'stec'));

    Admin_Html::html_input('attendee', '', '', __('E-Mail Address', 'stec'), false);

    Admin_Html::html_button(__('Add e-mail', 'stec'), '', false, 'add-attendee-mail blue-button');
    ?>

    <ul class="attendee-email-list" start="1">
        <li>
            %mail%<span class="attendee-email-remove"><?php _e('Remove', 'stec'); ?></span>
            <?php Admin_Html::html_hidden('attendee[][email]', '%mail%'); ?>
        </li>

        <?php
        if ( $event ) :
            foreach ( $event->get_attendance() as $k => $attendee ) :

                if ( $attendee->userid || $attendee->email == '' ) {
                    continue;
                }
                ?>
                <li>
                    <?php echo $attendee->email; ?><span class="attendee-email-remove"><?php _e('Remove', 'stec'); ?></span>
                    <?php
                    Admin_Html::html_hidden("attendee[$k][email]", $attendee->email);
                    Admin_Html::html_hidden("attendee[$k][id]", isset($attendee->id) ? $attendee->id : null);
                    Admin_Html::html_hidden("attendee[$k][access_token]", isset($attendee->access_token) ? $attendee->access_token : null);
                    Admin_Html::html_hidden("attendee[$k][mail_sent]", isset($attendee->mail_sent) ? $attendee->mail_sent : null);
                    ?>
                </li>
                <?php
            endforeach;
        endif;
        ?>
    </ul>

    <div class="stachethemes-admin-section-tab-attendance-wrap">

        <div class="attendance-all">
            <p><?php _e('All users', 'stec'); ?></p>
            <?php
            Admin_Html::html_button(__('Invite All', 'stec'), '', true, 'invite-all light-btn');
            Admin_Html::html_button(__('Uninvite All', 'stec'), '', true, 'uninvite-all light-btn');
            ?>
        </div>
        <?php
        echo '<ul class="attendee-list">';

        foreach ( $users as $user ) :
            ?>
            <li data-userid="<?php echo $user->ID; ?>">
                <p><?php echo $user->display_name; ?></p>
                <div>
                    <?php
                    Admin_Html::html_button(__('Invite', 'stec'), '', true, 'light-btn invite-user');
                    Admin_Html::html_button(__('Uninvite', 'stec'), '', true, 'light-btn uninvite-user');
                    ?>
                </div>

                <?php
                if ( $event ) :
                    foreach ( $event->get_attendance() as $k => $attendee ) :
                        if ( !$attendee->userid ) {
                            continue;
                        }

                        if ( $attendee->userid == $user->ID ) {
                            Admin_Html::html_hidden("attendee[$k][userid]", $user->ID);
                            Admin_Html::html_hidden("attendee[$k][access_token]", isset($attendee->access_token) ? $attendee->access_token : null);
                            Admin_Html::html_hidden("attendee[$k][mail_sent]", isset($attendee->mail_sent) ? $attendee->mail_sent : null);
                            foreach ( $attendee->status as $repeat_offset => $status ) {
                                Admin_Html::html_hidden("attendee[$k][status][$repeat_offset]", $status);
                            }
                        }
                    endforeach;
                endif;
                ?>  

            </li>
            <?php
        endforeach;

        echo '</ul>';
        ?>

    </div>

</div>

