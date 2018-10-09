<?php

namespace Stachethemes\Stec;




if ( have_posts() ) :
    while ( have_posts() ) : the_post();


        $repeat_offset                     = get_query_var('stec_repeat_offset', 0);
        $event                             = new Event_Post(get_the_ID());
        $settings                          = array();
        $general_settings                  = Settings::get_admin_settings_kv('stec_menu__general');
        $general_settings['userid']        = get_current_user_id();
        $general_settings['site_url']      = get_site_url();
        $general_settings['repeat_offset'] = $repeat_offset;
        $the_permalink                     = get_the_permalink() . ( $repeat_offset ? $repeat_offset : '');

        add_action('wp_head', function() use($event, $the_permalink) {

            ob_start();

            $image = $event->get_parsed_images();

            if ( $image ) :
                $image = $image[0];
            endif;
            ?>
            <meta property="og:title" content="<?php echo $event->get_title(); ?>" />
            <meta property="og:description" content="<?php echo $event->get_description_short(); ?>" />
            <meta property="og:url" content="<?php echo $the_permalink ?>" />
            <meta property="og:type" content="article" />
            <meta property="og:site_name" content="<?php echo get_bloginfo('name'); ?>" />
            <?php if ( $image ) : ?>
                <meta property="og:image" content="<?php echo $image->src; ?>"/>
            <?php endif; ?>

            <meta name="twitter:card" content="summary" /> 
            <meta name="twitter:title" content="<?php echo $event->get_title(); ?>" />
            <meta name="twitter:description" content="<?php echo $event->get_description_short(); ?>" />
            <?php if ( $image ) : ?>
                <meta name="twitter:image" content="<?php echo $image->src; ?>" />
                <?php
            endif;

            $meta = ob_get_clean();
            $og   = apply_filters('stec_single_og_props', array(
                    'meta'      => $meta,
                    'event'     => $event,
                    'permalink' => $the_permalink
            ));

            echo $og['meta'];
        });

        get_header();
        ?>

        <div class="stec-layout-single" itemscope itemtype="http://schema.org/Event">

            <div class="stec-layout-single-preloader-template">
                <div class="stec-layout-single-preloader"></div>
            </div>

            <div class="stec-layout-single-preview">

                <div class="stec-layout-single-preview-left">

                    <div style="background:<?php echo $event->get_color(); ?>" class="stec-layout-single-preview-left-icon <?php
                    if ( $event->get_icon() == 'fa' ) :
                        echo 'stec-layout-single-no-icon';
                    endif;
                    ?>">
                        <i class="<?php echo $event->get_icon(); ?>"></i>
                    </div>

                    <div class="stec-layout-single-preview-left-text">

                        <?php if ( $event->get_featured() >= '1' ) : ?>
                            <p class="stec-layout-single-preview-left-text-featured">
                                <i class="fa fa-star"></i>
                                <span><?php _e('Featured', 'stec'); ?></span>
                            </p>
                        <?php endif; ?>

                        <h1 class="stec-layout-single-preview-left-text-title" itemprop="name"><?php echo $event->get_title(); ?> <?php if ( is_super_admin() ) : ?>
                                <a class="stec-layout-single-edit-link" href="<?php echo get_admin_url(null, "admin.php?page=stec_menu__events&view=edit&calendar_id={$event->get_calid()}&event_id={$event->get_id()}"); ?>"><?php _e('(Edit event)', 'stec') ?></a>
                            <?php endif; ?></h1>

                        <p class="stec-layout-single-preview-left-text-date stec-get-the-timespan"><?php echo Admin_Helper::get_the_timespan($event, $repeat_offset); ?></p>

                        <?php Admin_Helper::meta_schema_datetime_iso8601($event, $repeat_offset); ?>

                        <?php if ( !Admin_Helper::reminder_expired($event, $repeat_offset) ) : ?>
                            <a class="stec-layout-single-preview-left-reminder-toggle" href="javascript:void(0);">
                                <?php _e('Reminder', 'stec'); ?>
                            </a>
                        <?php endif; ?>

                    </div>

                </div>

                <?php if ( !Admin_Helper::reminder_expired($event, $repeat_offset) ) : ?>
                    <div class="stec-layout-single-preview-right">

                        <div class="stec-layout-single-preview-right-reminder stec-layout-single-button-style stec-layout-single-btn-fontandcolor">
                            <div>
                                <i class="fa fa-bell"></i>
                                <span><?php _e('Reminder', 'stec'); ?></span>
                            </div>

                            <div>
                                <i class="fa fa-times"></i>
                                <span><?php _e('Close', 'stec'); ?></span>
                            </div>

                        </div>

                    </div>
                <?php endif; ?>

            </div>

            <div class="stec-layout-single-reminder-form">
                <ul>
                    <li>
                        <input class="stec-layout-event-input-fontandcolor" type="email" name="email" value="<?php echo Admin_Helper::get_current_user_email(); ?>" placeholder="<?php _e('E-Mail Address', 'stec'); ?>" />
                    </li>
                    <li>
                        <input class="stec-layout-event-input-fontandcolor" type="text" name="number" value="3" placeholder="<?php _e('Time before', 'stec'); ?>" />
                    </li>
                    <li class="stec-layout-single-preview-reminder-units-selector">
                        <p class="stec-layout-event-input-fontandcolor" data-value='hours'><?php _e('hours', 'stec'); ?></p>
                        <ul>
                            <li data-value="hours"><?php _e('hours', 'stec'); ?></li>
                            <li data-value="days"><?php _e('days', 'stec'); ?></li>
                            <li data-value="weeks"><?php _e('weeks', 'stec'); ?></li>
                        </ul>
                    </li>
                    <li>
                        <button class="stec-layout-single-preview-remind-button stec-layout-single-button-style stec-layout-single-btn-fontandcolor"><?php _e('Remind me', 'stec'); ?></button>
                    </li>
                </ul>

                <div class="stec-layout-single-reminder-status">
                    <p class="stec-layout-event-title2-fontandcolor">-</p>
                </div>
            </div>

            <?php if ( $event->get_images() ) : ?>

                <div class="stec-layout-single-media">

                    <div class="stec-layout-single-media-content">

                        <?php
                        foreach ( $event->get_parsed_images() as $image ) :
                            ?>
                            <div style="background-image:url(<?php echo $image->src; ?>);">
                                <img alt="<?php echo $image->title; ?>" src="<?php echo $image->src; ?>">
                                <meta itemprop="image" content="<?php echo $image->src; ?>" />
                            </div>
                            <?php
                        endforeach;
                        ?>

                    </div>

                    <div class="stec-layout-single-media-content-subs">
                        <div>
                            <p></p>
                            <span></span>
                        </div>
                    </div>

                    <div class="stec-layout-single-media-controls">
                        <div class="stec-layout-single-media-controls-prev stec-layout-single-btn-fontandcolor"><i class="fa fa-angle-left"></i></div>
                        <div class="stec-layout-single-media-controls-list-wrap">
                            <ul class="stec-layout-single-media-controls-list">

                                <?php
                                if ( ($event->get_images() ) ) :
                                    foreach ( $event->get_parsed_images() as $image ) :
                                        $image = (object) $image;
                                        ?>
                                        <li style="background-image: url(<?php echo $image->thumb; ?>);"></li>
                                        <?php
                                    endforeach;
                                endif;
                                ?>

                            </ul>
                        </div>
                        <div class="stec-layout-single-media-controls-next stec-layout-single-btn-fontandcolor"><i class="fa fa-angle-right"></i></div>
                    </div>

                </div>

            <?php endif; ?>

            <?php if ( $event->get_description() != '' ) : ?>

                <div class="stec-layout-single-description stec-layout-event-text-fontandcolor" itemprop="description">
                    <?php echo wpautop($event->get_description()); ?>
                </div>

            <?php endif; ?>

            <?php if ( $event->get_link() != '' ) : ?>
                <a href="<?php echo $event->get_link(); ?>" class="stec-layout-single-external-link stec-layout-single-button-style stec-layout-event-btn-fontandcolor" target="_BLANK"><?php _e('Visit Website', 'stec'); ?></a>
            <?php endif; ?>

            <?php if ( $event->get_counter() == '1' ) : ?>

                <ul class="stec-layout-single-counter">
                    <li>
                        <p class="stec-layout-single-counter-num">0</p>
                        <p class="stec-layout-single-counter-label" data-plural-label="<?php _e('Days', 'stec'); ?>" data-singular-label="<?php __('Day', 'stec'); ?>">days</p>
                    </li>
                    <li>
                        <p class="stec-layout-single-counter-num">0</p>
                        <p class="stec-layout-single-counter-label" data-plural-label="<?php _e('Hours', 'stec'); ?>" data-singular-label="<?php __('Hour', 'stec'); ?>">hours</p>
                    </li>
                    <li>
                        <p class="stec-layout-single-counter-num">0</p>
                        <p class="stec-layout-single-counter-label" data-plural-label="<?php _e('Minutes', 'stec'); ?>" data-singular-label="<?php __('Minute', 'stec'); ?>">minutes</p>
                    </li>
                    <li>
                        <p class="stec-layout-single-counter-num">0</p>
                        <p class="stec-layout-single-counter-label" data-plural-label="<?php _e('Seconds', 'stec'); ?>" data-singular-label="<?php __('Second', 'stec'); ?>">seconds</p>
                    </li>
                </ul>

            <?php endif; ?>

            <p class="stec-layout-single-event-status-text event-expired stec-layout-event-title2-fontandcolor"><?php _e('Event expired', 'stec'); ?></p>
            <p class="stec-layout-single-event-status-text event-inprogress stec-layout-event-title2-fontandcolor"><?php _e('Event is in progress', 'stec'); ?></p>

            <div class="stec-single-sections">

                <?php if ( Admin_Helper::user_is_invited($event) ) : ?>

                    <ul class="stec-layout-single-intro-attendance">

                        <li class="stec-layout-single-button-style stec-layout-single-intro-attendance-attend stec-layout-event-btn-fontandcolor <?php
                        if ( Admin_Helper::get_user_attendance_status($event, false, $repeat_offset) == 1 ) {
                            echo 'active';
                        }
                        ?>"><p><?php _e('Attend', 'stec'); ?></p></li>

                        <li class="stec-layout-single-button-style stec-layout-single-intro-attendance-decline stec-layout-event-btn-fontandcolor <?php
                        if ( Admin_Helper::get_user_attendance_status($event, false, $repeat_offset) == 2 ) {
                            echo 'active';
                        }
                        ?>"><p><?php _e('Decline', 'stec'); ?></p></li>
                    </ul>

                <?php endif; ?>

                <?php if ( $event->get_attachments() ) : ?>


                    <div class="stec-layout-single-attachments">

                        <div class="stec-layout-single-attachments-top">

                            <p class="stec-layout-event-title2-fontandcolor"><?php _e('Attachments', 'stec'); ?></p>

                            <div class="stec-layout-single-attachments-toggle">
                                <i class="fa fa-plus"></i>
                                <i class="fa fa-minus"></i>
                            </div>

                        </div>

                        <ul class="stec-layout-single-attachments-list">

                            <?php foreach ( $event->get_parsed_attachments() as $file ) : ?>

                                <li class="stec-layout-single-attachment">
                                    <div>
                                        <p class="stec-layout-single-attachment-title stec-layout-event-title2-fontandcolor"><a href="<?php echo $file->link ?>"><?php echo $file->filename; ?></a></p>
                                        <p class="stec-layout-single-attachment-desc stec-layout-event-text-fontandcolor"><?php echo $file->description; ?></p>
                                    </div>

                                    <div>
                                        <a href="<?php echo $file->link ?>" class="stec-layout-event-title2-fontandcolor"><?php _e('Download', 'stec'); ?></a>
                                        <p class="stec-layout-single-attachment-size stec-layout-event-text-fontandcolor"><?php echo $file->size; ?></p>
                                    </div>
                                </li>

                            <?php endforeach; ?>
                        </ul>
                    </div>


                <?php endif; ?>

                <meta itemprop="url" content="<?php echo $the_permalink; ?>"/>

                <?php
                if ( Settings::get_admin_setting_value('stec_menu__general', 'social_links') == '1' ||
                        Settings::get_admin_setting_value('stec_menu__general', 'show_export_buttons') == '1' ) :
                    ?>  

                    <div class="stec-layout-single-share-and-export">

                        <div class="stec-layout-single-share">
                            <?php if ( Settings::get_admin_setting_value('stec_menu__general', 'social_links') == '1' ) : ?>

                                <a target="_BLANK" href="http://www.facebook.com/share.php?u=<?php echo $the_permalink; ?>"><i class="fa fa-facebook"></i></a>
                                <a target="_BLANK" href="http://twitter.com/home?status=<?php echo $the_permalink; ?>"><i class="fa fa-twitter"></i></a>
                                <a target="_BLANK" href="https://plus.google.com/share?url=<?php echo $the_permalink; ?>"><i class="fa fa-google-plus"></i></a>
                                <a target="_BLANK" href="<?php echo $the_permalink; ?>"><i class="fa fa-link"></i></a>

                            <?php endif; ?>
                        </div>

                        <?php if ( Settings::get_admin_setting_value('stec_menu__general', 'show_export_buttons') == '1' ) : ?>
                            <div class="stec-layout-single-export">
                                <form method="POST"> 
                                    <button href="" class="stec-layout-single-button-sec-style stec-layout-event-btn-sec-fontandcolor"><?php _e('Export to .ICS file', 'stec'); ?></button>
                                    <input type="hidden" value="<?php echo $event->get_id(); ?>" name="event_id">
                                    <input type="hidden" value="<?php echo $event->get_calid(); ?>" name="calendar_id">
                                    <input type="hidden" value="stec_public_export_to_ics" name="task">
                                </form>

                                <a class="stec-layout-single-button-sec-style stec-layout-event-btn-sec-fontandcolor" href="<?php echo $event->get_gcal_link($repeat_offset); ?>" target="_BLANK" class="stec-layout-event-text-fontandcolor"><?php _e('Import to Google Calendar', 'stec'); ?></a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ( $event->get_location() ) : ?>

                    <div class="stec-layout-single-location" itemprop="location" itemscope itemtype="http://schema.org/Place">

                        <div class="stec-layout-single-location-flex">

                            <div class="stec-layout-single-location-left">
                                <p class="stec-layout-event-title2-fontandcolor"><?php _e('Location', 'stec'); ?></p>
                                <span class="stec-layout-single-location-address stec-layout-event-text-fontandcolor" data-location-use-coord="<?php
                                if ( $event->get_location_use_coord() ) {
                                    echo $event->get_location_forecast();
                                }
                                ?>" itemprop="name"><?php echo $event->get_location(); ?></span>
                            </div>

                            <div class="stec-layout-single-location-right">
                                <p class="stec-layout-single-location-directions-title stec-layout-event-title2-fontandcolor"
                                   itemprop="address" itemscope itemtype="http://schema.org/Text"
                                   ><?php _e('Directions', 'stec'); ?></p>
                            </div>

                        </div>

                        <div class="stec-layout-single-location-flex">

                            <div class="stec-layout-single-location-left" itemprop="hasMap">
                                <div class="stec-layout-single-location-gmap" itemprop="map" itemtype="https://schema.org/Map"></div>
                            </div>

                            <div class="stec-layout-single-location-right">
                                <input class="stec-layout-event-input-fontandcolor" type="text" name="start" placeholder="<?php _e('Start Location', 'stec'); ?>" />
                                <input class="stec-layout-event-input-fontandcolor" type="text" name="end" placeholder="<?php _e('Destination', 'stec'); ?>" />
                                <div class="stec-layout-single-button-style stec-layout-single-location-get-direction-btn stec-layout-event-btn-fontandcolor"><p><?php _e('Get Directions', 'stec'); ?></p></div>
                                <p class="stec-layout-single-location-direction-error stec-layout-event-text-fontandcolor"><?php _e('Could not find route!', 'stec'); ?></p>
                            </div>

                        </div>

                        <?php if ( $event->get_location_details() != '' ) : ?>

                            <p class="stec-layout-single-location-details-title stec-layout-event-title2-fontandcolor"><?php _e('Location Details', 'stec'); ?></p>

                            <p class='stec-layout-single-location-details stec-layout-event-text-fontandcolor' itemprop="address">
                                <?php echo $event->get_location_details(); ?>
                            </p>

                        <?php endif; ?>

                    </div>

                <?php endif; ?>

                <?php if ( Admin_Helper::event_has_tabs($event) ) : ?>

                    <div class="stec-layout-single-tabs">

                        <ul class="stec-layout-single-tabs-list">

                            <?php if ( $event->get_schedule() ) : ?>
                                <li class="stec-layout-event-title2-fontandcolor" data-tab="stec-layout-single-schedule">
                                    <i class="fa fa-th-list"></i>
                                    <p><?php _e('Schedule', 'stec'); ?></p>
                                </li>
                            <?php endif; ?>

                            <?php if ( $event->get_guests() ) : ?>
                                <li class="stec-layout-event-title2-fontandcolor" data-tab="stec-layout-single-guests">
                                    <i class="fa fa-star-o"></i>
                                    <p><?php _e('Guests', 'stec'); ?></p>
                                </li>
                            <?php endif; ?>

                            <?php if ( $event->get_products() ) : ?>
                                <li class="stec-layout-event-title2-fontandcolor" data-tab="stec-layout-single-woocommerce">
                                    <i class="fa fa-shopping-cart"></i>
                                    <p><?php _e('Shop', 'stec'); ?></p>
                                </li>
                            <?php endif; ?>

                            <?php
                            if ( $event->get_location_forecast() &&
                                    Settings::get_admin_setting_value('stec_menu__general', 'weather_api_key') != "" ) :
                                ?>
                                <li class="stec-layout-event-title2-fontandcolor" data-tab="stec-layout-single-forecast">
                                    <i class="fa fa-sun-o"></i>
                                    <p><?php _e('Forecast', 'stec'); ?></p>
                                </li>
                            <?php endif; ?>

                            <?php if ( $event->get_attendance() ) : ?>

                                <li class="stec-layout-event-title2-fontandcolor" data-tab="stec-layout-single-attendance">
                                    <i class="fa fa-user"></i>
                                    <p><?php _e('Attendance', 'stec'); ?></p>
                                </li>

                            <?php endif; ?>

                            <?php if ( $event->get_comments() == '1' ) : ?>

                                <li class="stec-layout-event-title2-fontandcolor" data-tab="stec-layout-single-comments">
                                    <i class="fa fa-commenting-o"></i>
                                    <p><?php _e('Comments', 'stec'); ?></p>
                                </li>

                            <?php endif; ?>

                        </ul>

                        <div class="stec-layout-event-single-tabs-content">

                            <?php if ( $event->get_schedule() ) : ?>

                                <div class="stec-layout-single-schedule">

                                    <?php foreach ( $event->get_schedule() as $schedule ) :
                                        ?>

                                        <div class="stec-layout-single-schedule-tab<?php
                                        if ( $schedule->details == '' ) {
                                            echo ' stec-layout-single-schedule-tab-no-desc';
                                        }

                                        if ( $schedule->icon == 'fa' ) {
                                            echo ' stec-layout-single-schedule-tab-no-icon';
                                        }
                                        ?>">

                                            <div class="stec-layout-single-schedule-tab-preview">
                                                <div class="stec-layout-single-schedule-tab-left">
                                                    <span class="" data-schedule-timestamp="<?php echo strtotime($schedule->start_date) + $repeat_offset; ?>"><?php echo Admin_Helper::get_the_schedule_timespan($schedule->start_date, $event, $repeat_offset); ?></span>
                                                </div>
                                                <div class="stec-layout-single-schedule-tab-right">

                                                    <div class="stec-layout-single-schedule-tab-right-title">
                                                        <i style="color: <?php echo $schedule->icon_color; ?>" 
                                                           class="<?php echo $schedule->icon; ?>"></i><span class=""><?php echo $schedule->title; ?></span>
                                                    </div>

                                                    <div class="stec-layout-single-schedule-tab-toggle">
                                                        <i class="fa fa-plus"></i>
                                                        <i class="fa fa-minus"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if ( $schedule->details != '' ) : ?>
                                                <div class="stec-layout-single-schedule-tab-desc">
                                                    <span class="stec-layout-event-text-fontandcolor"><?php echo nl2br($schedule->details); ?></span>
                                                </div>
                                            <?php endif; ?>

                                        </div>

                                    <?php endforeach; ?>

                                </div>

                            <?php endif; ?>


                            <?php if ( $event->get_guests() ) : ?>

                                <div class="stec-layout-single-guests">

                                    <?php foreach ( $event->get_guests() as $guest ) : ?>

                                        <div class="stec-layout-single-guests-guest">

                                            <div class="stec-layout-single-guests-guest-left">
                                                <div class="stec-layout-single-guests-guest-left-avatar">
                                                    <img alt="<?php echo $guest->name; ?>" src="<?php echo $guest->photo_full; ?>">
                                                    <ul>
                                                        <?php foreach ( $guest->links as $k => $link ) : $link = (object) $link; ?>
                                                            <li class="stec-layout-single-guests-guest-left-avatar-icon-position-<?php echo $k; ?>">
                                                                <a href="<?php echo $link->link; ?>">
                                                                    <i class="<?php echo $link->ico; ?>"></i>
                                                                </a>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="stec-layout-single-guests-guest-right">
                                                <p class="stec-layout-single-guests-guest-right-title stec-layout-event-title2-fontandcolor"><?php echo $guest->name; ?></p>
                                                <div class="stec-layout-single-guests-guest-right-desc  stec-layout-event-text-fontandcolor">
                                                    <p><?php echo $guest->about; ?></p>
                                                </div>
                                            </div>

                                        </div>

                                    <?php endforeach; ?>

                                </div>

                            <?php endif; ?>


                            <?php if ( $event->get_products() ) : ?>

                                <div class="stec-layout-single-woocommerce">

                                    <div class="stec-layout-single-woocommerce-top">
                                        <p class="stec-layout-event-text-fontandcolor"><?php _e('Product', 'stec'); ?></p>
                                        <p class="stec-layout-event-text-fontandcolor"><?php _e('Name', 'stec'); ?></p>
                                        <p class="stec-layout-event-text-fontandcolor"><?php _e('Quantity', 'stec'); ?></p>
                                        <p class="stec-layout-event-text-fontandcolor"><?php _e('Price', 'stec'); ?></p>
                                        <p class="stec-layout-event-text-fontandcolor"><?php _e('Action', 'stec'); ?></p>
                                    </div>

                                    <div class='stec-layout-single-woocommerce-products'>

                                        <?php foreach ( $event->get_parsed_products() as $product ) :
                                            ?>

                                            <div class="stec-layout-single-woocommerce-product">

                                                <div class="stec-layout-single-woocommerce-product-image">
                                                    <?php
                                                    echo $product->image;
                                                    ?>
                                                </div>

                                                <div class="stec-layout-single-woocommerce-product-desc">

                                                    <div class="stec-layout-single-woocommerce-product-status">

                                                        <?php if ( $product->is_featured ): ?>
                                                            <span class="stec-layout-event-text-fontandcolor stec-layout-single-woocommerce-product-about-featured"><?php _e('featured', 'stec'); ?></span>
                                                        <?php endif; ?>

                                                        <?php if ( $product->is_on_sale ): ?>
                                                            <span class="stec-layout-event-text-fontandcolor stec-layout-single-woocommerce-product-about-sale"><?php _e('sale', 'stec'); ?></span>
                                                        <?php endif; ?>

                                                        <?php if ( !$product->is_in_stock ): ?>
                                                            <span class="stec-layout-event-text-fontandcolor stec-layout-single-woocommerce-product-about-outofstock"><?php _e('out of stock', 'stec'); ?></span>
                                                        <?php endif; ?>
                                                    </div>

                                                    <p class="stec-layout-event-title2-fontandcolor"><?php echo $product->title; ?></p>
                                                    <p class="stec-layout-event-text-fontandcolor"><?php
                                                        $product->post_data = (object) $product->post_data;
                                                        echo $product->post_data->excerpt;
                                                        ?></p>
                                                </div>

                                                <p class="stec-layout-single-woocommerce-product-quantity stec-layout-event-title2-fontandcolor"><span class="stec-layout-event-text-fontandcolor"><?php _e('QTY:', 'stec'); ?></span><span><?php echo $product->stock_quantity == '' ? '-' : $product->stock_quantity; ?></span></p>

                                                <p class="stec-layout-single-woocommerce-product-price stec-layout-event-title2-fontandcolor"><span class="stec-layout-event-text-fontandcolor"><?php _e('PRICE:', 'stec'); ?></span><?php echo Admin_Helper::get_product_price($product); ?></p>

                                                <div>
                                                    <?php if ( $product->is_in_stock ): ?>
                                                        <div class="stec-layout-single-woocommerce-product-buy-addtocart 
                                                             stec-layout-single-button-style 
                                                             stec-layout-event-btn-fontandcolor"

                                                             data-pid="<?php echo $product->id; ?>" 
                                                             data-quantity="1" 
                                                             data-sku="<?php echo $product->sku; ?>">

                                                            <p><?php _e('Add to Cart', 'stec'); ?></p>
                                                            <i class="fa fa-shopping-cart"></i>
                                                        </div>
                                                        <div class="stec-layout-single-woocommerce-product-buy-ajax-status"></div>
                                                    <?php endif; ?>
                                                </div>

                                            </div>

                                        <?php endforeach; ?>

                                    </div>

                                    <div class='stec-layout-single-woocommerce-links'>
                                        <a class="stec-layout-single-button-sec-style stec-layout-event-btn-sec-fontandcolor" href='<?php echo wc_get_checkout_url(); ?>'><?php echo _e('Checkout', 'stec'); ?></a>
                                        <a class="stec-layout-single-button-sec-style stec-layout-event-btn-sec-fontandcolor" href='<?php echo wc_get_cart_url(); ?>'><?php echo _e('View Cart', 'stec'); ?></a>
                                    </div>

                                </div>

                            <?php endif; ?>


                            <?php if ( $event->get_location_forecast() ) : ?>

                                <div class="stec-layout-single-forecast">

                                    <p class="errorna stec-layout-event-title-fontandcolor"><?php _e('Weather data is currently not available for this location', 'stec'); ?></p>

                                    <div class="stec-layout-single-forecast-content">

                                        <div class="stec-layout-single-forecast-top">

                                            <p class="stec-layout-single-forecast-top-title"><?php _e('Weather Report', 'stec'); ?></p>
                                            <p class="stec-layout-single-forecast-top-date"><?php _e('Today', 'stec'); ?> stec_replace_today_date</p>

                                        </div>

                                        <div class="stec-layout-single-forecast-today">

                                            <div class="stec-layout-single-forecast-today-left">

                                                <div class="stec-layout-single-forecast-today-left-icon">
                                                    stec_replace_today_icon_div
                                                </div>

                                                <div>
                                                    <p class="stec-layout-single-forecast-today-left-current-text">stec_replace_current_summary_text</p>
                                                    <p class="stec-layout-single-forecast-today-left-current-temp">stec_replace_current_temp &deg;stec_replace_current_temp_units</p>
                                                </div>

                                            </div>

                                            <div class="stec-layout-single-forecast-today-right">
                                                <p class=""><?php _e('Wind', 'stec'); ?> <span>stec_replace_current_wind stec_replace_current_wind_units stec_replace_current_wind_direction</span></p>
                                                <p class=""><?php _e('Humidity', 'stec'); ?> <span>stec_replace_current_humidity %</span></p>
                                                <p class=""><?php _e('Feels like', 'stec'); ?> <span>stec_replace_current_feels_like &deg;stec_replace_current_temp_units</span></p>
                                            </div>

                                        </div>

                                        <div class="stec-layout-single-forecast-details">

                                            <div class="stec-layout-single-forecast-details-left">
                                                <p class=""><?php _e('Forecast', 'stec'); ?></p>

                                                <div class="stec-layout-single-forecast-details-left-forecast">
                                                    <div class="stec-layout-single-forecast-details-left-forecast-top">
                                                        <p><?php _e('Date', 'stec'); ?></p>
                                                        <p><?php _e('Weather', 'stec'); ?></p>
                                                        <p><?php _e('Temp', 'stec'); ?></p>
                                                    </div>

                                                    <div class="stec-layout-single-forecast-details-left-forecast-day stec-layout-single-forecast-details-left-forecast-day-template">
                                                        <p>stec_replace_date</p>
                                                        stec_replace_icon_div
                                                        <p>stec_replace_min / stec_replace_max &deg;stec_replace_temp_units</p>
                                                    </div>

                                                    stec_replace_5days
                                                </div>

                                            </div>

                                            <div class="stec-layout-single-forecast-details-right">
                                                <p class=""><?php _e('Next 24 Hours', 'stec'); ?></p>

                                                <div class="stec-layout-single-forecast-details-chart">
                                                    <canvas></canvas>
                                                </div>
                                            </div>
                                        </div>

                                        <p class="stec-layout-single-forecast-credits"><?php _e('Powered by', 'stec'); ?> Forecast.io</p>

                                    </div>

                                </div>

                            <?php endif; ?>

                            <?php if ( $event->get_attendance() ) : ?>

                                <div class="stec-layout-single-attendance">


                                    <?php if ( Admin_Helper::user_is_invited($event) ) : ?>

                                        <div class="stec-layout-single-attendance-invited">
                                            <p class="stec-layout-event-title2-fontandcolor">
                                                <?php _e('You are invited to this event!', 'stec'); ?>
                                            </p>
                                            <ul class="stec-layout-single-attendance-invited-buttons">
                                                <li class="stec-layout-single-button-style stec-layout-single-attendance-invited-buttons-accept stec-layout-event-btn-fontandcolor <?php
                                                if ( Admin_Helper::get_user_attendance_status($event, false, $repeat_offset) == 1 ) {
                                                    echo 'active';
                                                }
                                                ?>">
                                                    <p><?php _e('Attend', 'stec'); ?></p>
                                                </li>

                                                <li class="stec-layout-single-button-style stec-layout-single-attendance-invited-buttons-decline stec-layout-event-btn-fontandcolor <?php
                                                if ( Admin_Helper::get_user_attendance_status($event, false, $repeat_offset) == 2 ) {
                                                    echo 'active';
                                                }
                                                ?>">
                                                    <p><?php _e('Decline', 'stec'); ?></p>
                                                </li>
                                            </ul>
                                        </div>

                                    <?php endif; ?>

                                    <ul class="stec-layout-single-attendance-attendees">

                                        <?php $attendees = $event->get_attendance(); ?>

                                        <?php foreach ( $attendees as $attendee ) :
                                            ?>

                                            <li class="stec-layout-single-attendance-attendee" itemprop="attendee" itemscope itemtype="http://schema.org/Person">

                                                <div data-userid="<?php echo $attendee->userid; ?>" class="stec-layout-single-attendance-attendee-avatar">
                                                    <img src="<?php echo $attendee->avatar; ?>" alt="<?php echo $attendee->name; ?>" />
                                                    <meta itemprop="image" contnet="<?php echo $attendee->avatar; ?>" />
                                                    <ul>
                                                        <?php
                                                        switch ( Admin_Helper::get_user_attendance_status($event, $attendee->userid) ) :

                                                            case 1:
                                                                echo '<li class=""><i class="fa fa-check"></i></li>';
                                                                break;

                                                            case 2:
                                                                echo '<li class=""><i class="fa fa-times"></i></li>';
                                                                break;

                                                            case 0:
                                                            default:
                                                                echo '<li class=""><i class="fa fa-question"></i></li>';

                                                        endswitch;
                                                        ?>
                                                    </ul>
                                                </div>

                                                <p class="stec-layout-event-title2-fontandcolor" itemprop="name"><?php echo $attendee->name; ?></p>
                                            </li>

                                        <?php endforeach; ?>
                                    </ul>

                                </div>

                            <?php endif; ?>

                            <?php if ( $event->get_comments() == '1' ) : ?>

                                <div class="stec-layout-single-comments">
                                    <div id="disqus_thread"></div>
                                </div>

                            <?php endif; ?>

                        </div>

                    </div>

                <?php endif ?>

            </div>

            <div class="stec-share-template">
                <?php require(__DIR__ . "/popup/share.php"); ?>
            </div>

        </div>

        <script type="text/javascript">
            var stecSingleOptions = <?php echo json_encode($general_settings); ?>;
            var stecSingleEvent = <?php echo json_encode($event->get_front_data()); ?>;
        </script>

        <?php
    endwhile;
endif;


get_footer();
