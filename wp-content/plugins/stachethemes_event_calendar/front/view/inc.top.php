<?php

namespace Stachethemes\Stec;
?>

<div class="stec-top">

    <p class="stec-top-menu-date-small">DATE_LABEL</p>

    <ul class="stec-top-menu">
        <li data-action="today">
            <i class="fa fa-calendar-check-o"></i>
            <p><?php _e("Today", 'stec'); ?></p>
            <p class="stec-top-menu-count">0</p>
        </li>


        <?php if ( $calendar->get_shortcode_option('stec_menu__general', 'show_search') == '1' ) : ?>

            <li class="stec-top-menu-search"><i class="fa fa-search"></i>
                <div class="stec-top-search-dropdown">
                    <div class="stec-top-search-form">
                        <input type="text" placeholder="<?php _e("Search", 'stec'); ?>">
                        <a href="#"><i class="fa fa-search"></i></a>
                    </div>
                    <ul class="stec-top-search-results">
                        <!--<li><p>Search for ...</p></li>-->
                    </ul>
                    <p class="stec-top-search-dropdown-noresult"><?php _e("Nothing found", 'stec'); ?></p>
                </div>
            </li>

        <?php endif; ?>



        <?php if ( $calendar->get_shortcode_option('stec_menu__general', 'show_calfilter') == '1' ) : ?>
            <li class="stec-top-menu-filter-calendar"><i class="fa fa-calendar"></i>
                <ul class="stec-top-menu-filter-calendar-dropdown">
                    <!--<li><p><span style="background:#53b32b"></span>Calendar 1</p></li>-->
                </ul>
            </li>
        <?php endif; ?>
        <li data-action="previous"><i class="fa fa-angle-left"></i></li>
        <li data-action="next"><i class="fa fa-angle-right"></i></li>
    </ul>

    <ul class="stec-top-menu stec-top-menu-date">

        <li class="stec-top-menu-date-week">
            <p data-week="">WEEK_RANGE_LABEL</p>

            <div class="stec-top-menu-date-dropdown">
                <div class="stec-top-menu-date-control-up"><i class="fa fa-caret-up"></i></div>
                <div class="stec-top-menu-date-control-down"><i class="fa fa-caret-down"></i></div>
            </div>
        </li>

        <li class="stec-top-menu-date-day">
            <p data-day="">DAY_LABEL</p>

            <div class="stec-top-menu-date-dropdown">
                <div class="stec-top-menu-date-control-up"><i class="fa fa-caret-up"></i></div>
                <div class="stec-top-menu-date-control-down"><i class="fa fa-caret-down"></i></div>
            </div>
        </li>

        <li class="stec-top-menu-date-month">
            <p data-month="">MONTH_LABEL</p>

            <div class="stec-top-menu-date-dropdown">
                <div class="stec-top-menu-date-control-up"><i class="fa fa-caret-up"></i></div>
                <div class="stec-top-menu-date-control-down"><i class="fa fa-caret-down"></i></div>
            </div>
        </li>

        <li class="stec-top-menu-date-year">
            <p data-year="">YEAR_LABEL</p>

            <div class="stec-top-menu-date-dropdown">
                <div class="stec-top-menu-date-control-up"><i class="fa fa-caret-up"></i></div>
                <ul>
                    <!--<li><p>2016</p></li>-->
                </ul>
                <div class="stec-top-menu-date-control-down"><i class="fa fa-caret-down"></i></div>
            </div>
        </li>

    </ul>

    <?php if ( $calendar->get_shortcode_option('stec_menu__general', 'show_views') == '1' ) : ?>

        <ul class="stec-top-menu stec-top-menu-layouts">
            <li data-view="agenda"><p><?php _e("Agenda", 'stec'); ?></p></li>
            <li data-view="month"><p><?php _e("Month", 'stec'); ?></p></li>
            <li data-view="week"><p><?php _e("Week", 'stec'); ?></p></li>
            <li data-view="day"><p><?php _e("Day", 'stec'); ?></p></li>
            <li data-view="grid"><p><?php _e("Grid", 'stec'); ?></p></li>
        </ul>

        <ul class="stec-top-dropmenu-layouts">
            <li>
                <i class="fa fa-bars"></i>
                <ul>
                    <li data-view="agenda"><p><?php _e("Agenda", 'stec'); ?></p></li>
                    <li data-view="month"><p><?php _e("Month", 'stec'); ?></p></li>
                    <li data-view="week"><p><?php _e("Week", 'stec'); ?></p></li>
                    <li data-view="day"><p><?php _e("Day", 'stec'); ?></p></li>
                    <li data-view="grid"><p><?php _e("Grid", 'stec'); ?></p></li>
                </ul>
            </li>
        </ul>

    <?php endif; ?>
</div>