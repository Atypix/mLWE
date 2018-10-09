<?php

namespace Stachethemes\Stec; ?>

<li class="stec-event-create-form stec-layout-event">

    <div class="stec-event-create-form-preview stec-layout-event-preview">

        <div class="stec-layout-event-preview-left">
            <div class="stec-layout-event-create-form-preview-left-text">
                <p class="stec-layout-event-preview-left-text-title"><?php _e('Create an event', 'stec'); ?></p>
                <p class="stec-layout-event-preview-left-text-sub"><?php _e('Click to submit your own event', 'stec'); ?></p>
            </div>
        </div>

        <?php if ( !$is_single_form ) : ?>

            <div class="stec-layout-event-preview-right">
                <div class="stec-layout-event-preview-right-event-toggle stec-layout-event-create-form-preview-right-event-toggle">
                    <i class="fa fa-plus"></i>
                    <i class="fa fa-minus"></i>
                </div>
            </div>

        <?php elseif ( $is_single_form && $is_popup ) : ?>

            <div class="stec-layout-event-preview-right">
                <div class="stec-layout-event-preview-right-event-toggle stec-layout-event-create-form-preview-right-event-toggle">
                    <i class="fa fa-plus"></i>
                    <i class="fa fa-times"></i>
                </div>
            </div>

        <?php endif; ?>

    </div>

    <div class="stec-layout-event-create-form-inner stec-layout-event-inner">
        <form>
            <p class="stec-layout-event-create-form-inner-label stec-layout-event-title2-fontandcolor">
                <?php _e('Title', 'stec'); ?>
            </p>
            <input required="required" class="stec-layout-event-input-fontandcolor" name="title" type="text" value="" placeholder="<?php _e('Event Title', 'stec'); ?>" />

            <p class="stec-layout-event-create-form-inner-label stec-layout-event-title2-fontandcolor">
                <?php _e('Description (optional)', 'stec'); ?>
            </p>
            <textarea class="stec-layout-event-input-fontandcolor" name="description" placeholder="<?php _e('Event Description', 'stec'); ?>"></textarea>

            <p class="stec-layout-event-create-form-inner-label stec-layout-event-title2-fontandcolor">
                <?php _e('Short Description (optional)', 'stec'); ?>
            </p>
            <input class="stec-layout-event-input-fontandcolor" type="text" name="description_short" placeholder="<?php _e('Few words about your event shown on the tooltip', 'stec'); ?>" />


            <p class="stec-layout-event-create-form-inner-label stec-layout-event-title2-fontandcolor">
                <?php _e('Image (optional)', 'stec'); ?>
            </p>
            <input class="stec-layout-event-input-fontandcolor stec-layout-event-create-form-inner-date-image" type="text" value="" placeholder="<?php _e('Accepts jpeg and png files up to ' . Submit_Event::get_max_upload_size() . 'MB', 'stec'); ?>" /> 
            <input class="stec-layout-event-create-form-inner-date-image-file" type="file" name="fileimage" accept="image/jpeg, image/png" />

            <p class="stec-layout-event-create-form-inner-label stec-layout-event-title2-fontandcolor">
                <?php _e('Location (optional)', 'stec'); ?>
            </p>
            <input class="stec-layout-event-input-fontandcolor" name="location" type="text" value="" placeholder="<?php _e('Google maps location', 'stec'); ?>" />

            <p class="stec-layout-event-create-form-inner-label stec-layout-event-title2-fontandcolor">
                <?php _e('Website URL (optional)', 'stec'); ?>
            </p>
            <input class="stec-layout-event-input-fontandcolor" name="link" type="text" value="" placeholder="<?php _e('URL Address', 'stec'); ?>" />

            <div class="stec-layout-event-create-form-inner-flexbox">

                <div>
                    <p class="stec-layout-event-create-form-inner-label stec-layout-event-title2-fontandcolor">
                        <?php _e('Calendar', 'stec'); ?>
                    </p>
                    <select class="stec-layout-event-input-fontandcolor" name="calendar_id" required="required">
                        <option value="" disabled selected><?php _e('-- Select Calendar --', 'stec'); ?></option>
                        <?php
                        $writable_cals = Calendars::get_writable_calendar_list();

                        foreach ( $writable_cals as $cal ) :

                            if ( !empty($writable_cal_array) ) {
                                if ( !in_array($cal->get_id(), $writable_cal_array) ) {
                                    continue;
                                }
                            }
                            ?>
                            <option data-color="<?php echo $cal->get_color(); ?>" value="<?php echo $cal->get_id(); ?>"><?php echo $cal->get_title(); ?></option>
                            <?php
                        endforeach;
                        ?>
                    </select>
                </div>

                <div>
                    <p class="stec-layout-event-create-form-inner-label stec-layout-event-title2-fontandcolor">
                        <?php _e('Icon', 'stec'); ?>
                    </p>
                    <select class="stec-layout-event-input-fontandcolor" name="icon">
                        <?php
                        $icons = Admin_Helper::get_icon_list();

                        foreach ( $icons as $key => $value ) :
                            ?>
                            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                            <?php
                        endforeach;
                        ?>
                    </select>
                </div>

            </div>

            <div class="stec-layout-event-create-form-inner-flexbox">
                <input class="stec-layout-event-create-form-inner-colorpicker" name="event_color" type="text" value="#fff" />
                <p class="stec-layout-event-create-form-inner-label stec-layout-event-title2-fontandcolor">
                    <?php _e('Color. By default will use the calendar color', 'stec'); ?>
                </p>
            </div>

            <?php
            $hours_list   = Admin_Helper::get_hours_array();
            $minutes_list = Submit_Event::get_minutes_array();
            ?>

            <div class="stec-layout-event-create-form-inner-flexbox">
                <div>
                    <p class="stec-layout-event-create-form-inner-label stec-layout-event-title2-fontandcolor">
                        <?php _e('Starts On', 'stec'); ?>
                    </p>
                    <input class="stec-layout-event-input-fontandcolor stec-layout-event-create-form-inner-date" name="start_date" type="text" value="" required="required" />
                </div>

                <div>
                    <p class="stec-layout-event-create-form-inner-label stec-layout-event-title2-fontandcolor">
                        <?php _e('From', 'stec'); ?>
                    </p>
                    <div class="stec-layout-event-create-form-inner-flexbox stec-layout-event-create-form-time stec-layout-event-create-form-inner-element-nomargin">
                        <div>
                            <select class="stec-layout-event-input-fontandcolor" name="start_time_hours" required="required">
                                <?php
                                foreach ( $hours_list as $key => $value ) :
                                    ?>
                                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                    <?php
                                endforeach;
                                ?>
                            </select>
                        </div>
                        <div>
                            <select class="stec-layout-event-input-fontandcolor" name="start_time_minutes" required="required">
                                <?php
                                foreach ( $minutes_list as $key => $value ) :
                                    ?>
                                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                    <?php
                                endforeach;
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>


            <div class="stec-layout-event-create-form-inner-flexbox">
                <div>
                    <p class="stec-layout-event-create-form-inner-label stec-layout-event-title2-fontandcolor">
                        <?php _e('Ends On', 'stec'); ?>
                    </p>
                    <input class="stec-layout-event-input-fontandcolor stec-layout-event-create-form-inner-date" name="end_date" type="text" value="" required="required" />
                </div>

                <div>
                    <p class="stec-layout-event-create-form-inner-label stec-layout-event-title2-fontandcolor">
                        <?php _e('To', 'stec'); ?>
                    </p>
                    <div class="stec-layout-event-create-form-inner-flexbox stec-layout-event-create-form-time stec-layout-event-create-form-inner-element-nomargin">
                        <div>
                            <select class="stec-layout-event-input-fontandcolor" name="end_time_hours" required="required">
                                <?php
                                foreach ( $hours_list as $key => $value ) :
                                    ?>
                                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                    <?php
                                endforeach;
                                ?>
                            </select>
                        </div>
                        <div>
                            <select class="stec-layout-event-input-fontandcolor" name="end_time_minutes" required="required">
                                <?php
                                foreach ( $minutes_list as $key => $value ) :
                                    ?>
                                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                    <?php
                                endforeach;
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>


            <div class="stec-layout-event-create-form-inner-flexbox">
                <input type="checkbox" name="all_day" />
                <p class="stec-layout-event-create-form-inner-label stec-layout-event-title2-fontandcolor">
                    <?php _e('All Day', 'stec'); ?>
                </p>
            </div>

            <div class="stec-layout-event-create-form-inner-flexbox">

                <div>

                    <p class="stec-layout-event-create-form-inner-label stec-layout-event-title2-fontandcolor">
                        <?php _e('Repeat Event', 'stec'); ?>
                    </p>

                    <select class="stec-layout-event-input-fontandcolor" name="event_repeat">
                        <option selected="selected" value="0"><?php _e('No Repeat', 'stec'); ?></option>
                        <option value="1"><?php _e('Daily', 'stec'); ?></option>
                        <option value="2"><?php _e('Weekly', 'stec'); ?></option>
                        <option value="3"><?php _e('Monthly', 'stec'); ?></option>
                        <option value="4"><?php _e('Yearly', 'stec'); ?></option>
                    </select>
                </div>

                <div class="stec-layout-event-create-form-inner-repeat-gap-block">
                    <p class="stec-layout-event-create-form-inner-label stec-layout-event-title2-fontandcolor">
                        <?php _e('Repeat gap', 'stec'); ?>
                    </p>
                    <input class="stec-layout-event-input-fontandcolor" name="repeat_gap" type="text" value="0" />
                </div>

            </div>

            <div class="stec-layout-event-create-form-inner-repeat-sub">


                <div class="stec-layout-event-create-form-inner-weekly-by-day">

                    <p class="stec-layout-event-create-form-inner-label stec-layout-event-title2-fontandcolor">
                        <?php _e('Repeat by day', 'stec'); ?>
                    </p>

                    <div class="stec-layout-event-create-form-inner-flexbox stec-layout-event-create-form-inner-element-nomargin">
                        <span>
                            <input name="SU" title="<?php _e('Sunday', 'stec') ?>" type="checkbox">
                            <label for="" class="stec-layout-event-title2-fontandcolor" title="<?php _e('Sunday', 'stec') ?>"><?php _e('SU', 'stec') ?></label>
                        </span>
                        <span>
                            <input name="MO" title="<?php _e('Monday', 'stec') ?>" type="checkbox">
                            <label for="" class="stec-layout-event-title2-fontandcolor" title="<?php _e('Monday', 'stec') ?>"><?php _e('MO', 'stec') ?></label>
                        </span>
                        <span>
                            <input name="TU" title="<?php _e('Tuesday', 'stec') ?>" type="checkbox">
                            <label for="" class="stec-layout-event-title2-fontandcolor" title="<?php _e('Tuesday', 'stec') ?>"><?php _e('TU', 'stec') ?></label>
                        </span>
                        <span>
                            <input name="WE" title="<?php _e('Wednesday', 'stec') ?>" type="checkbox">
                            <label for="" class="stec-layout-event-title2-fontandcolor" title="<?php _e('Wednesday', 'stec') ?>"><?php _e('WE', 'stec') ?></label>
                        </span>
                        <span>
                            <input name="TH" title="<?php _e('Thursday', 'stec') ?>" type="checkbox">
                            <label for="" class="stec-layout-event-title2-fontandcolor" title="<?php _e('Thursday', 'stec') ?>"><?php _e('TH', 'stec') ?></label>
                        </span>
                        <span>
                            <input name="FR" title="<?php _e('Friday', 'stec') ?>" type="checkbox">
                            <label for="" class="stec-layout-event-title2-fontandcolor" title="<?php _e('Friday', 'stec') ?>"><?php _e('FR', 'stec') ?></label>
                        </span>
                        <span>
                            <input name="SA" title="<?php _e('Saturday', 'stec') ?>" type="checkbox">
                            <label for="" class="stec-layout-event-title2-fontandcolor" title="<?php _e('Saturday', 'stec') ?>"><?php _e('SA', 'stec') ?></label>
                        </span>
                    </div>


                </div>


                <div class="stec-layout-event-create-form-inner-repeat-ends-on-block">

                    <p class="stec-layout-event-create-form-inner-label stec-layout-event-title2-fontandcolor">
                        <?php _e('Repeat Ends On', 'stec'); ?>
                    </p>

                    <div class="">
                        <span>
                            <input id="stec-repeater-popup-repeat-endson-never" name="repeat_endson" value="0" checked="checked" type="radio">
                            <label for="stec-repeater-popup-repeat-endson-never">
                                <p class="stec-layout-event-title2-fontandcolor"><?php _e('Never', 'stec') ?></p>
                            </label>
                        </span>
                        <span>
                            <input id="stec-repeater-popup-repeat-endson-after-n" name="repeat_endson" value="1" type="radio">
                            <label class="stec-layout-event-title2-fontandcolor"  for="stec-repeater-popup-repeat-endson-after-n">
                                <p class="stec-layout-event-title2-fontandcolor"><?php _e('After', 'stec') ?></p>
                                <input class="stec-layout-event-input-fontandcolor" name="repeat_occurences" size="2" value="" disabled="disabled" type="text">
                                <p class="stec-layout-event-title2-fontandcolor"><?php _e('occurences', 'stec') ?></p>
                            </label>
                        </span>
                        <span>
                            <input id="stec-repeater-popup-repeat-endson-date" name="repeat_endson" value="2" type="radio">
                            <label class="stec-layout-event-title2-fontandcolor"  for="stec-repeater-popup-repeat-endson-date">
                                <p class="stec-layout-event-title2-fontandcolor"><?php _e('On Date', 'stec') ?></p>
                                <input id="repeat_end_date" class="stec-layout-event-input-fontandcolor" name="repeat_ends_on_date" size="14" value="" disabled="disabled" autocomplete="off" type="text">
                            </label>
                        </span>
                    </div>

                </div>

                <input type="hidden" name="rrule" value="" />

            </div>

            <div class="stec-layout-event-create-form-inner-flexbox">
                <div>
                    <p class="stec-layout-event-create-form-inner-label stec-layout-event-title2-fontandcolor">
                        <?php _e('Search Keywords', 'stec'); ?>
                    </p>
                    <input class="stec-layout-event-input-fontandcolor" name="keywords" type="text" value="" placeholder="<?php _e('Keywords separated by space', 'stec'); ?>" />
                </div>

                <div>
                    <p class="stec-layout-event-create-form-inner-label stec-layout-event-title2-fontandcolor">
                        <?php _e('Counter', 'stec'); ?>
                    </p>
                    <select class="stec-layout-event-input-fontandcolor" name="counter">
                        <option value="0"><?php _e('Disable', 'stec'); ?></option>
                        <option value="1"><?php _e('Enable', 'stec'); ?></option>
                    </select>
                </div>
            </div>

            <p class="stec-layout-event-create-form-inner-label stec-layout-event-title2-fontandcolor">
                <?php _e('Your E-mail', 'stec'); ?>
            </p>

            <input required="required" class="stec-layout-event-input-fontandcolor" name="contact_email" type="email" value="<?php echo Admin_Helper::get_current_user_email(); ?>" placeholder="<?php _e('Contact E-Mail', 'stec'); ?>" />

            <p class="stec-layout-event-create-form-inner-label stec-layout-event-title2-fontandcolor">
                <?php _e('Notes to the reviewer (optional)', 'stec'); ?>
            </p>
            <textarea class="stec-layout-event-input-fontandcolor" name="review_note" type="text" value="" placeholder="<?php _e('Additional info visible to the reviewer only', 'stec'); ?>"></textarea>

            <div class="stec-layout-event-create-form-g-recaptcha"></div>

            <div class="stec-layout-event-create-form-inner-submit-flexbox">
                <button class="stec-layout-event-create-form-inner-submit stec-layout-event-btn-fontandcolor stec-layout-event-inner-button-style"><?php _e('Submit Event', 'stec'); ?></button>
                <i class="fa fa-check"></i>
                <i class="fa fa-times"></i>
            </div>

        </form>
    </div>

</li>