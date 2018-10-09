<?php

namespace Stachethemes\Stec;




/**
 * LICENSE SETTINGS
 */
Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__license",
                "title"   => __('Enter your purchase key below', 'stec'),
                "desc"    => "",
                "name"    => "purchase_code",
                "type"    => "input",
                "value"   => "",
                "default" => "",
                "req"     => true
        )
);


/**
 * GENERAL SETTINGS
 */
Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('First day of the week', 'stec'),
                "desc"    => "",
                "name"    => "first_day_of_the_week",
                "type"    => "select",
                "value"   => "mon",
                "default" => "mon",
                "select"  => array(
                        'mon' => __('Monday', 'stec'),
                        'sat' => __('Saturday', 'stec'),
                        'sun' => __('Sunday', 'stec')
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Date Format', 'stec'),
                "desc"    => "",
                "name"    => "date_format",
                "type"    => "select",
                "value"   => "mm-dd-yy",
                "default" => "mm-dd-yy",
                "select"  => array(
                        'yy-mm-dd'   => __('YYYY-MM-DD', 'stec'),
                        'dd-mm-yy'   => __('DD-MM-YYYY', 'stec'),
                        'mm-dd-yy'   => __('MM-DD-YYYY', 'stec'),
                        'dd.mm.yyyy' => __('DD.MM.YYYY', 'stec')
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __("Display Date 'GMT/UTC'", 'stec'),
                "desc"    => "",
                "name"    => "date_label_gmtutc",
                "type"    => "select",
                "value"   => "1",
                "default" => "1",
                "select"  => array(
                        '0' => __('Hide', 'stec'),
                        '1' => __('Show', 'stec'),
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Display date in user local time', 'stec'),
                "desc"    => "",
                "name"    => "date_in_user_local_time",
                "type"    => "select",
                "value"   => "0",
                "default" => "0",
                "select"  => array(
                        '0' => __('No', 'stec'),
                        '1' => __('Yes', 'stec'),
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Time Format', 'stec'),
                "desc"    => "",
                "name"    => "time_format",
                "type"    => "select",
                "value"   => "24",
                "default" => "24",
                "select"  => array(
                        '12' => __('12 Hours', 'stec'),
                        '24' => __('24 Hours', 'stec')
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Weather API Key', 'stec'),
                "desc"    => "",
                "name"    => "weather_api_key",
                "type"    => "input",
                "value"   => "",
                "default" => "",
                "req"     => false
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Weather temperature units', 'stec'),
                "desc"    => "",
                "name"    => "temp_units",
                "type"    => "select",
                "value"   => "C",
                "default" => "C",
                "select"  => array(
                        'C' => __('Celsius', 'stec'),
                        'F' => __('Fahrenheit', 'stec')
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Weather wind units', 'stec'),
                "desc"    => "",
                "name"    => "wind_units",
                "type"    => "select",
                "value"   => "KPH",
                "default" => "KPH",
                "select"  => array(
                        'MPH' => __('MPH', 'stec'),
                        'KPH' => __('KPH', 'stec')
                ),
                "req"     => true
        )
);


Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Google Maps API Key', 'stec'),
                "desc"    => "",
                "name"    => "gmaps_api_key",
                "type"    => "input",
                "value"   => "",
                "default" => "",
                "req"     => false
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Disqus Shortname', 'stec'),
                "desc"    => "",
                "name"    => "disqus_shortname",
                "type"    => "input",
                "value"   => "",
                "default" => "",
                "req"     => false
        )
);


Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Reminder feature', 'stec'),
                "desc"    => "",
                "name"    => "reminder",
                "type"    => "select",
                "value"   => "1",
                "default" => "1",
                "select"  => array(
                        '0' => __('Disable', 'stec'),
                        '1' => __('Enable', 'stec')
                ),
                "req"     => true
        )
);


Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Default View', 'stec'),
                "desc"    => "",
                "name"    => "view",
                "type"    => "select",
                "value"   => "agenda",
                "default" => "agenda",
                "select"  => array(
                        'agenda' => __('Agenda', 'stec'),
                        'month'  => __('Month', 'stec'),
                        'week'   => __('Week', 'stec'),
                        'day'    => __('Day', 'stec'),
                        'grid'   => __('Grid', 'stec'),
                ),
                "req"     => true
        )
);


Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Top Menu', 'stec'),
                "desc"    => "",
                "name"    => "show_top",
                "type"    => "select",
                "value"   => "1",
                "default" => "1",
                "select"  => array(
                        '0' => __('Hide', 'stec'),
                        '1' => __('Show', 'stec')
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Different Views Buttons', 'stec'),
                "desc"    => "",
                "name"    => "show_views",
                "type"    => "select",
                "value"   => "1",
                "default" => "1",
                "select"  => array(
                        '0' => __('Hide', 'stec'),
                        '1' => __('Show', 'stec')
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Search Button', 'stec'),
                "desc"    => "",
                "name"    => "show_search",
                "type"    => "select",
                "value"   => "1",
                "default" => "1",
                "select"  => array(
                        '0' => __('Hide', 'stec'),
                        '1' => __('Show', 'stec')
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Calendar Filter Button', 'stec'),
                "desc"    => "",
                "name"    => "show_calfilter",
                "type"    => "select",
                "value"   => "1",
                "default" => "1",
                "select"  => array(
                        '0' => __('Hide', 'stec'),
                        '1' => __('Show', 'stec')
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Show event title on all cells', 'stec'),
                "desc"    => "",
                "name"    => "show_event_title_all_cells",
                "type"    => "select",
                "value"   => "0",
                "default" => "0",
                "select"  => array(
                        '0' => __('No', 'stec'),
                        '1' => __('Yes', 'stec')
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Auto-focus event', 'stec'),
                "desc"    => "",
                "name"    => "event_auto_focus",
                "type"    => "select",
                "value"   => "1",
                "default" => "1",
                "select"  => array(
                        '0' => __('No', 'stec'),
                        '1' => __('Yes', 'stec')
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Auto-focus event offset (- / +) px', 'stec'),
                "desc"    => "",
                "name"    => "event_auto_focus_offset",
                "type"    => "input",
                "value"   => "0",
                "default" => "0",
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Agenda events limit per click', 'stec'),
                "desc"    => "",
                "name"    => "agenda_get_n",
                "type"    => "input",
                "value"   => "3",
                "default" => "3",
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Agenda Calendar Display', 'stec'),
                "desc"    => "",
                "name"    => "agenda_cal_display",
                "type"    => "select",
                "value"   => "1",
                "default" => "1",
                "select"  => array(
                        '0' => __('Hide', 'stec'),
                        '1' => __('Show', 'stec')
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Agenda List Display', 'stec'),
                "desc"    => "",
                "name"    => "agenda_list_display",
                "type"    => "select",
                "value"   => "1",
                "default" => "1",
                "select"  => array(
                        '0' => __('Hide', 'stec'),
                        '1' => __('Show', 'stec')
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Agenda List Order', 'stec'),
                "desc"    => "",
                "name"    => "reverse_agenda_list",
                "type"    => "select",
                "value"   => "0",
                "default" => "0",
                "select"  => array(
                        '0' => __('Ascending', 'stec'),
                        '1' => __('Descending', 'stec')
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Event Tooltip', 'stec'),
                "desc"    => "",
                "name"    => "tooltip_display",
                "type"    => "select",
                "value"   => "1",
                "default" => "1",
                "select"  => array(
                        '0' => __('Disable', 'stec'),
                        '1' => __('Enable', 'stec')
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Display "Create an event" form', 'stec'),
                "desc"    => "",
                "name"    => "show_create_event_form",
                "type"    => "select",
                "value"   => "1",
                "default" => "1",
                "select"  => array(
                        '0' => __('No', 'stec'),
                        '1' => __('Yes', 'stec')
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Allow creating expired events (front-end) ', 'stec'),
                "desc"    => "",
                "name"    => "create_event_form_allow_expired",
                "type"    => "select",
                "value"   => "1",
                "default" => "1",
                "select"  => array(
                        '0' => __('No', 'stec'),
                        '1' => __('Yes', 'stec')
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Allow export event from front-end', 'stec'),
                "desc"    => "",
                "name"    => "show_export_buttons",
                "type"    => "select",
                "value"   => "1",
                "default" => "1",
                "select"  => array(
                        '0' => __('No', 'stec'),
                        '1' => __('Yes', 'stec')
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Social Share Links', 'stec'),
                "desc"    => "",
                "name"    => "social_links",
                "type"    => "select",
                "value"   => "1",
                "default" => "1",
                "select"  => array(
                        '0' => __('Hide', 'stec'),
                        '1' => __('Show', 'stec')
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Allow embedding', 'stec'),
                "desc"    => "",
                "name"    => "allow_embedding",
                "type"    => "select",
                "value"   => "1",
                "default" => "1",
                "select"  => array(
                        '0' => __('No', 'stec'),
                        '1' => __('Yes', 'stec')
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general",
                "title"   => __('Open events in', 'stec'),
                "desc"    => "",
                "name"    => "open_event_in",
                "type"    => "select",
                "value"   => "self",
                "default" => "self",
                "select"  => array(
                        'self'   => __('Calendar', 'stec'),
                        'single' => __('Single Page', 'stec')
                ),
                "req"     => true
        )
);


Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general_google_captcha",
                "title"   => __('Use Captcha', 'stec'),
                "name"    => "enabled",
                "type"    => "select",
                "value"   => '0',
                "default" => '0',
                "select"  => array(
                        '0' => __('No', 'stec'),
                        '1' => __('Yes', 'stec')
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general_google_captcha",
                "title"   => __('Site Key', 'stec'),
                "name"    => "site_key",
                "type"    => "input",
                "value"   => "",
                "default" => "",
                "req"     => false
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general_google_captcha",
                "title"   => __('Secret Key', 'stec'),
                "name"    => "secret_key",
                "type"    => "input",
                "value"   => "",
                "default" => "",
                "req"     => false
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general_other",
                "title"   => __('Load google maps script.', 'stec'),
                "name"    => "load_gmaps",
                "type"    => "select",
                "value"   => '1',
                "default" => '1',
                "select"  => array(
                        '0' => __('No', 'stec'),
                        '1' => __('Yes', 'stec')
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general_other",
                "title"   => __('Load jQuery UI style', 'stec'),
                "name"    => "load_jquery_ui_css",
                "type"    => "select",
                "value"   => '1',
                "default" => '1',
                "select"  => array(
                        '0' => __('No', 'stec'),
                        '1' => __('Yes', 'stec')
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general_other",
                "title"   => __('Load Chart.js', 'stec'),
                "name"    => "load_chart_js",
                "type"    => "select",
                "value"   => '1',
                "default" => '1',
                "select"  => array(
                        '0' => __('No', 'stec'),
                        '1' => __('Yes', 'stec')
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general_other",
                "title"   => __('Load Moment.js', 'stec'),
                "name"    => "load_moment_js",
                "type"    => "select",
                "value"   => '1',
                "default" => '1',
                "select"  => array(
                        '0' => __('No', 'stec'),
                        '1' => __('Yes', 'stec')
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general_other",
                "title"   => __('Load jQuery Mobile', 'stec'),
                "name"    => "load_jmobile_js",
                "type"    => "select",
                "value"   => '1',
                "default" => '1',
                "select"  => array(
                        '0' => __('No', 'stec'),
                        '1' => __('Yes', 'stec')
                ),
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__general_other",
                "title"   => __('Force load calendar scripts', 'stec'),
                "name"    => "force_load_scripts",
                "type"    => "select",
                "value"   => '0',
                "default" => '0',
                "select"  => array(
                        '0' => __('No', 'stec'),
                        '1' => __('Yes', 'stec')
                ),
                "req"     => true
        )
);



/**
 *  Fonts and Colors TOP
 */
Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_top",
                "title"   => __('Button text color & text hover color', 'stec'),
                "desc"    => "",
                "name"    => "top_btn_text_color",
                "type"    => "color",
                "value"   => "#bdc1c8",
                "default" => "#bdc1c8",
                "css"     => array(
                        'color || body .stec-top .stec-top-dropmenu-layouts > li i',
                        'color || body .stec-top .stec-top-menu > li',
                        'color || body .stec-top .stec-top-menu li[data-action="today"]:hover .stec-top-menu-count'
                )
        )
);
Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_top",
                "name"    => "top_btn_text_color_hover",
                "type"    => "color",
                "value"   => "#ffffff",
                "default" => "#ffffff",
                "css"     => array(
                        'color || body .stec-top .stec-top-menu > li:hover i',
                        'color || body .stec-top .stec-top-menu > li.active i',
                        'color || body .stec-top .stec-top-menu > li:hover p',
                        'color || body .stec-top .stec-top-menu > li.active p',
                        'color || body .stec-top .stec-top-menu .stec-top-menu-count',
                        'color || body .stec-top .stec-top-dropmenu-layouts > li:hover i'
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_top",
                "title"   => __('Button text background color & background hover color', 'stec'),
                "desc"    => "",
                "name"    => "top_btn_bg",
                "type"    => "color",
                "value"   => "#ffffff",
                "default" => "#ffffff",
                "css"     => array(
                        'background || body .stec-top .stec-top-dropmenu-layouts > li',
                        'background || body .stec-top .stec-top-menu > li',
                        'background || body .stec-top .stec-top-menu li[data-action="today"]:hover .stec-top-menu-count',
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_top",
                "title"   => "",
                "desc"    => "",
                "name"    => "top_btn_bg_hover",
                "type"    => "color",
                "value"   => "#f15e6e",
                "default" => "#f15e6e",
                "css"     => array(
                        'background || body .stec-top .stec-top-menu > li:hover',
                        'background || body .stec-top .stec-top-menu > li.active',
                        'background || body .stec-top .stec-top-menu .stec-top-menu-count',
                        'background || body .stec-top .stec-top-dropmenu-layouts > li:hover',
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_top",
                "title"   => __('Button text background color & background hover color', 'stec'),
                "desc"    => "",
                "name"    => "top_dropdown_text_color",
                "type"    => "color",
                "value"   => "#ffffff",
                "default" => "#ffffff",
                "css"     => array(
                        'color || body .stec-top .stec-top-dropmenu-layouts > li:hover > ul li p',
                        'color || body .stec-top .stec-top-menu-date-dropdown:hover .stec-top-menu-date-control-up i',
                        'color || body .stec-top .stec-top-menu-date-dropdown:hover .stec-top-menu-date-control-down i',
                        'color || body .stec-top .stec-top-menu-date-dropdown:hover li p',
                        'color || body .stec-top .stec-top-menu-date .mobile-hover .stec-top-menu-date-control-up i',
                        'color || body .stec-top .stec-top-menu-date .mobile-hover .stec-top-menu-date-control-down i',
                        'color || body .stec-top .stec-top-menu-date .mobile-hover li p'
                )
        )
);
Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_top",
                "title"   => "",
                "desc"    => "",
                "name"    => "top_dropdown_text_color_hover",
                "type"    => "color",
                "value"   => "#ffffff",
                "default" => "#ffffff",
                "css"     => array(
                        'color || body .stec-top .stec-top-dropmenu-layouts > li:hover > ul li:hover',
                        'color || body .stec-top .stec-top-menu-date-dropdown:hover .stec-top-menu-date-control-up:hover i',
                        'color || body .stec-top .stec-top-menu-date-dropdown:hover .stec-top-menu-date-control-down:hover i',
                        'color || body .stec-top .stec-top-menu-date ul li:hover p',
                        'color || body .stec-top .stec-top-menu-search .stec-top-search-results li.active i',
                        'color || body .stec-top .stec-top-menu-search .stec-top-search-results li:hover i',
                        'color || body .stec-top .stec-top-menu-search .stec-top-search-results li.active p',
                        'color || body .stec-top .stec-top-menu-search .stec-top-search-results li:hover p',
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_top",
                "title"   => __('Dropdown menu background color & background hover color', 'stec'),
                "desc"    => "",
                "name"    => "top_dropdown_bg",
                "type"    => "color",
                "value"   => "#f15e6e",
                "default" => "#f15e6e",
                "css"     => array(
                        'background || body .stec-top .stec-top-dropmenu-layouts > li:hover > ul li',
                        'background || body .stec-top .stec-top-menu-date-control-up',
                        'background || body .stec-top .stec-top-menu-date-control-down',
                        'background || body .stec-top .stec-top-menu-date ul li',
                        'background || body .stec-top .stec-top-menu-search .stec-top-search-dropdown',
                        'background || body .stec-top .stec-top-menu-filter-calendar-dropdown'
                )
        )
);
Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_top",
                "title"   => "",
                "desc"    => "",
                "name"    => "top_dropdown_bg_hover",
                "type"    => "color",
                "value"   => "#e04d5d",
                "default" => "#e04d5d",
                "css"     => array(
                        'background || body .stec-top .stec-top-dropmenu-layouts > li:hover > ul li.active',
                        'background || body .stec-top .stec-top-dropmenu-layouts > li:hover > ul li:hover',
                        'background || body .stec-top .stec-top-menu-date-control-up:hover',
                        'background || body .stec-top .stec-top-menu-date-control-down:hover',
                        'background || body .stec-top .stec-top-menu-date ul li.active',
                        'background || body .stec-top .stec-top-menu-date ul li:hover',
                        'background || body .stec-top .stec-top-menu-search .stec-top-search-results li.active',
                        'background || body .stec-top .stec-top-menu-search .stec-top-search-results li:hover'
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_top",
                "title"   => __('Search form background and text color', 'stec'),
                "desc"    => "",
                "name"    => "top_search_bg",
                "type"    => "color",
                "value"   => "#ffffff",
                "default" => "#ffffff",
                "css"     => array(
                        'background || body .stec-top .stec-top-menu-search .stec-top-search-form'
                )
        )
);
Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_top",
                "title"   => "",
                "desc"    => "",
                "name"    => "top_search_text_color",
                "type"    => "color",
                "value"   => "#bdc1c8",
                "default" => "#bdc1c8",
                "css"     => array(
                        'color || body .stec-top .stec-top-menu-search .stec-top-search-form input',
                        'color || body .stec-top .stec-top-menu-search .stec-top-search-form a i'
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_top",
                "title"   => __('Top navigation Font, Weight and Size', 'stec'),
                "desc"    => "",
                "name"    => "top",
                "type"    => "font",
                "value"   => array(
                        "Roboto",
                        "400",
                        "14px"
                ),
                "default" => array(
                        "Roboto",
                        "400",
                        "14px"
                ),
                "css"     => array(
                        'font || body .stec-top-menu-date-small',
                        'font || body .stec-top .stec-top-menu > li p',
                        'font || body .stec-top .stec-top-dropmenu-layouts ul p',
                        'font || body .stec-top .stec-top-menu-search .stec-top-search-form input'
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_top",
                "title"   => __('Add !important rule', 'stec'),
                "desc"    => "",
                "name"    => "top_important",
                "type"    => "checkbox",
                "value"   => 0,
                "default" => 0
        )
);


/**
 *  Fonts and Colors AGENDA
 */
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_agenda",
        "title"   => __('Agenda month start cell background, month text color, year text color', 'stec'),
        "desc"    => "",
        "name"    => "layouts_agenda_monthwrap_bg",
        "type"    => "color",
        "value"   => "#e6e8ed",
        "default" => "#e6e8ed",
        "css"     => array(
                "background || body .stec-layout-agenda-monthstart"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_agenda",
        "name"    => "layouts_agenda_monthwrap_month_text_color",
        "type"    => "color",
        "value"   => "#0c0c0c",
        "default" => "#0c0c0c",
        "css"     => array(
                "color || body .stec-layout-agenda-monthstart-month"
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_agenda",
        "name"    => "layouts_agenda_monthwrap_year_text_color",
        "type"    => "color",
        "value"   => "#999999",
        "default" => "#999999",
        "css"     => array(
                "color || body .stec-layout-agenda-monthstart-year"
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_agenda",
        "title"   => __('Agenda cell background, hover color and active color', 'stec'),
        "desc"    => "",
        "name"    => "layouts_agenda_bg",
        "type"    => "color",
        "value"   => "#ffffff",
        "default" => "#ffffff",
        "css"     => array(
                "background || body .stec-layout-agenda-daycell"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_agenda",
        "name"    => "layouts_agenda_bg_hover",
        "type"    => "color",
        "value"   => "#f0f1f2",
        "default" => "#f0f1f2",
        "css"     => array(
                "background || body .stec-layout-agenda-daycell:hover"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_agenda",
        "name"    => "layouts_agenda_bg_active",
        "type"    => "color",
        "value"   => "#4d576c",
        "default" => "#4d576c",
        "css"     => array(
                "background || body .stec-layout-agenda-daycell.active"
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_agenda",
        "title"   => __('Agenda day label text color, date color, text active color, text today color', 'stec'),
        "desc"    => "",
        "name"    => "layouts_agenda_daylabel_text_color",
        "type"    => "color",
        "value"   => "#999999",
        "default" => "#999999",
        "css"     => array(
                "color || body .stec-layout-agenda-daycell-label"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_agenda",
        "name"    => "layouts_agenda_date_text_color",
        "type"    => "color",
        "value"   => "#0c0c0c",
        "default" => "#0c0c0c",
        "css"     => array(
                "color || body .stec-layout-agenda-daycell-num "
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_agenda",
        "name"    => "layouts_agenda_date_text_color_active",
        "type"    => "color",
        "value"   => "#ffffff",
        "default" => "#ffffff",
        "css"     => array(
                "color || body .stec-layout-agenda-daycell.active .stec-layout-agenda-daycell-label",
                "color || body .stec-layout-agenda-daycell.active .stec-layout-agenda-daycell-num",
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_agenda",
        "name"    => "layouts_agenda_date_text_color_today",
        "type"    => "color",
        "value"   => "#e25261",
        "default" => "#e25261",
        "css"     => array(
                "color || body .stec-layout-agenda-daycell.stec-layout-agenda-daycell-today .stec-layout-agenda-daycell-label",
                "color || body .stec-layout-agenda-daycell.stec-layout-agenda-daycell-today .stec-layout-agenda-daycell-num",
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_agenda",
        "title"   => __('Agenda list date title color', 'stec'),
        "desc"    => "",
        "name"    => "layouts_agenda_list_text_color",
        "type"    => "color",
        "value"   => "#4d576c",
        "default" => "#4d576c",
        "css"     => array(
                "color || body .stec-layout-agenda-events-all-datetext"
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_agenda",
        "title"   => __('"Look for more" button background color, text color, hover background color, text hover color', 'stec'),
        "desc"    => "",
        "name"    => "layouts_agenda_list_btn_bg",
        "type"    => "color",
        "value"   => "#4d576c",
        "default" => "#4d576c",
        "css"     => array(
                "background || body .stec-layout-agenda-events-all-load-more"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_agenda",
        "name"    => "layouts_agenda_list_btn_text_color",
        "type"    => "color",
        "value"   => "#ffffff",
        "default" => "#ffffff",
        "css"     => array(
                "color || body .stec-layout-agenda-events-all-load-more p"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_agenda",
        "name"    => "layouts_agenda_list_btn_bg_hover",
        "type"    => "color",
        "value"   => "#f15e6e",
        "default" => "#f15e6e",
        "css"     => array(
                "background || body .stec-layout-agenda-events-all-load-more:hover"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_agenda",
        "name"    => "layouts_agenda_list_btn_text_color_hover",
        "type"    => "color",
        "value"   => "#ffffff",
        "default" => "#ffffff",
        "css"     => array(
                "color || body .stec-layout-agenda-events-all-load-more:hover p"
        )
));

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_agenda",
                "title"   => __('"Look for more" button Font, Weight and Size', 'stec'),
                "desc"    => "",
                "name"    => "layouts_agenda_list_btn",
                "type"    => "font",
                "value"   => array(
                        "Roboto",
                        "400",
                        "14px"
                ),
                "default" => array(
                        "Roboto",
                        "400",
                        "14px"
                ),
                "css"     => array(
                        "font || body .stec-layout-agenda-events-all-load-more"
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_agenda",
                "title"   => __('Agenda month start cell "Year" Font, Weight and Size', 'stec'),
                "desc"    => "",
                "name"    => "layouts_agenda_monthwrap_year",
                "type"    => "font",
                "value"   => array(
                        "Roboto",
                        "400",
                        "12px"
                ),
                "default" => array(
                        "Roboto",
                        "400",
                        "12px"
                ),
                "css"     => array(
                        "font || body .stec-layout-agenda-monthstart-year"
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_agenda",
                "title"   => __('Agenda month start cell "Month" Font, Weight and Size', 'stec'),
                "desc"    => "",
                "name"    => "layouts_agenda_monthwrap_month",
                "type"    => "font",
                "value"   => array(
                        "Roboto",
                        "400",
                        "18px"
                ),
                "default" => array(
                        "Roboto",
                        "400",
                        "18px"
                ),
                "css"     => array(
                        "font || body .stec-layout-agenda-monthstart-month"
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_agenda",
                "title"   => __('Daylabel Font, Weight and Size', 'stec'),
                "desc"    => "",
                "name"    => "layouts_agenda_daylabel",
                "type"    => "font",
                "value"   => array(
                        "Roboto",
                        "400",
                        "14px"
                ),
                "default" => array(
                        "Roboto",
                        "400",
                        "14px"
                ),
                "css"     => array(
                        "font || body .stec-layout-agenda-daycell-label"
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_agenda",
                "title"   => __('Date Font, Weight and Size', 'stec'),
                "desc"    => "",
                "name"    => "layouts_agenda_date",
                "type"    => "font",
                "value"   => array(
                        "Roboto",
                        "400",
                        "30px"
                ),
                "default" => array(
                        "Roboto",
                        "400",
                        "30px"
                ),
                "css"     => array(
                        "font || body .stec-layout-agenda-daycell-num"
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_agenda",
                "title"   => __('Agenda list date title Font, Weight and Size', 'stec'),
                "desc"    => "",
                "name"    => "layouts_agenda_list",
                "type"    => "font",
                "value"   => array(
                        "Roboto",
                        "700",
                        "14px"
                ),
                "default" => array(
                        "Roboto",
                        "700",
                        "14px"
                ),
                "css"     => array(
                        "font || body .stec-layout-agenda-events-all-datetext"
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_agenda",
                "title"   => __('Add !important rule', 'stec'),
                "desc"    => "",
                "name"    => "agenda_important",
                "type"    => "checkbox",
                "value"   => 0,
                "default" => 0
        )
);


/**
 *  Fonts and Colors MONTH AND WEEK
 */
Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_monthweek",
                "title"   => __('Day labels background, text color, today text color', 'stec'),
                "desc"    => "",
                "name"    => "layouts_daylabel_bg",
                "type"    => "color",
                "value"   => "#4d576c",
                "default" => "#4d576c",
                "css"     => array(
                        "background || body .stec-layout-month-daylabel td",
                        "background || body .stec-layout-week-daylabel td",
                )
        )
);
Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_monthweek",
                "name"    => "layouts_daylabel_text_color",
                "type"    => "color",
                "value"   => "#bdc1c8",
                "default" => "#bdc1c8",
                "css"     => array(
                        "color || body .stec-layout-month-daylabel p",
                        "color || body .stec-layout-week-daylabel p",
                )
        )
);
Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_monthweek",
                "name"    => "layouts_daylabel_text_today_color",
                "type"    => "color",
                "value"   => "#f6bf64",
                "default" => "#f6bf64",
                "css"     => array(
                        "color || body .stec-layout-month-daylabel .stec-layout-month-daylabel-today p",
                        "color || body .stec-layout-week-daylabel .stec-layout-week-daylabel-today p"
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_monthweek",
                "title"   => __('Grid cells background color, hover color and active color', 'stec'),
                "desc"    => "",
                "name"    => "layouts_grid_cell_bg",
                "type"    => "color",
                "value"   => "#ffffff",
                "default" => "#ffffff",
                "css"     => array(
                        "background || body .stec-layout-month-daycell .stec-layout-month-daycell-wrap",
                        "background || body .stec-layout-week-daycell .stec-layout-week-daycell-wrap",
                )
        )
);
Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_monthweek",
                "name"    => "layouts_grid_cell_bg_hover",
                "type"    => "color",
                "value"   => "#f0f1f2",
                "default" => "#f0f1f2",
                "css"     => array(
                        "background || body .stec-layout-month-daycell:hover .stec-layout-month-daycell-wrap",
                        "background || body .stec-layout-week-daycell:hover .stec-layout-week-daycell-wrap",
                        "background || body .stec-layout-week-daycell.stec-layout-week-daycell-inactive:hover .stec-layout-week-daycell-wrap",
                        "background || body .stec-layout-month-daycell.stec-layout-month-daycell-inactive:hover .stec-layout-month-daycell-wrap"
                )
        )
);
Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_monthweek",
                "name"    => "layouts_grid_cell_bg_active",
                "type"    => "color",
                "value"   => "#4d576c",
                "default" => "#4d576c",
                "css"     => array(
                        "background || body .stec-layout-month-daycell.active .stec-layout-month-daycell-wrap",
                        "background || body .stec-layout-week-daycell.active .stec-layout-week-daycell-wrap"
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_monthweek",
                "title"   => __('Grid date text color and active color', 'stec'),
                "desc"    => "",
                "name"    => "layouts_grid_cell_text_color",
                "type"    => "color",
                "value"   => "#4d576c",
                "default" => "#4d576c",
                "css"     => array(
                        "color || body .stec-layout-month-daycell:not(.stec-layout-month-daycell-today) .stec-layout-month-daycell-wrap .stec-layout-month-daycell-num",
                        "color || body .stec-layout-week-daycell:not(.stec-layout-week-daycell-today) .stec-layout-week-daycell-wrap .stec-layout-week-daycell-num",
                )
        )
);
Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_monthweek",
                "name"    => "layouts_grid_cell_text_color_active",
                "type"    => "color",
                "value"   => "#ffffff",
                "default" => "#ffffff",
                "css"     => array(
                        "background || body .stec-layout-week-daycell.active .stec-layout-week-daycell-eventmore-count-dot",
                        "background || body .stec-layout-month-daycell.active .stec-layout-month-daycell-eventmore-count-dot",
                        "color || body .stec-layout-month-daycell.active:not(.stec-layout-month-daycell-today) .stec-layout-month-daycell-wrap .stec-layout-month-daycell-num",
                        "color || body .stec-layout-week-daycell.active:not(.stec-layout-week-daycell-today) .stec-layout-week-daycell-wrap .stec-layout-week-daycell-num",
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_monthweek",
                "title"   => __('Today date background and text color', 'stec'),
                "desc"    => "",
                "name"    => "layouts_grid_cell_today_bg",
                "type"    => "color",
                "value"   => "#f15e6e",
                "default" => "#f15e6e",
                "css"     => array(
                        "background || body .stec-layout-month-daycell.stec-layout-month-daycell-today .stec-layout-month-daycell-num::before",
                        "background || body .stec-layout-week-daycell.stec-layout-week-daycell-today .stec-layout-week-daycell-num::before"
                )
        )
);
Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_monthweek",
                "name"    => "layouts_grid_cell_today_text_color",
                "type"    => "color",
                "value"   => "#ffffff",
                "default" => "#ffffff",
                "css"     => array(
                        "color || body .stec-layout-month-daycell-today .stec-layout-month-daycell-wrap .stec-layout-month-daycell-num",
                        "color || body .stec-layout-week-daycell-today .stec-layout-week-daycell-wrap .stec-layout-week-daycell-num"
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_monthweek",
                "title"   => __('Inactive cell background and date text color', 'stec'),
                "desc"    => "",
                "name"    => "layouts_grid_cell_inactive_bg",
                "type"    => "color",
                "value"   => "#ffffff",
                "default" => "#ffffff",
                "css"     => array(
                        "background || body .stec-layout-month-daycell.stec-layout-month-daycell-inactive .stec-layout-month-daycell-wrap",
                        "background || body .stec-layout-week-daycell.stec-layout-week-daycell-inactive .stec-layout-week-daycell-wrap"
                )
        )
);
Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_monthweek",
                "name"    => "layouts_grid_cell_inactive_text_color",
                "type"    => "color",
                "value"   => "#a2a8b3",
                "default" => "#a2a8b3",
                "css"     => array(
                        "color || body .stec-layout-month-daycell:not(.stec-layout-month-daycell-today).stec-layout-month-daycell-inactive .stec-layout-month-daycell-wrap .stec-layout-month-daycell-num",
                        "color || body .stec-layout-week-daycell:not(.stec-layout-week-daycell-today).stec-layout-week-daycell-inactive .stec-layout-week-daycell-wrap .stec-layout-week-daycell-num",
                )
        )
);


Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_monthweek",
                "title"   => __('Event name text color dark, text color bright', 'stec'),
                "desc"    => "",
                "name"    => "layouts_grid_cell_event_text_color_dark",
                "type"    => "color",
                "value"   => "#4d576c",
                "default" => "#4d576c",
                "css"     => array(
                        "background || body .stec-layout-month-daycell-eventmore-count-dot",
                        "background || body .stec-layout-week-daycell-eventmore-count-dot",
                        "color || body .stec-layout-month-daycell-eventmore-count",
                        "color || body .stec-layout-week-daycell-eventmore-count",
                        "color || body .stec-layout-month-daycell-events .stec-layout-month-daycell-event.stec-layout-month-daycell-event-bright .stec-layout-month-daycell-event-name",
                        "color || body .stec-layout-week-daycell-events .stec-layout-week-daycell-event.stec-layout-week-daycell-event-bright .stec-layout-week-daycell-event-name"
                )
        )
);
Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_monthweek",
                "name"    => "layouts_grid_cell_event_text_color_bright",
                "type"    => "color",
                "value"   => "#ffffff",
                "default" => "#ffffff",
                "css"     => array(
                        "color || body .stec-layout-month-daycell-events .stec-layout-month-daycell-event .stec-layout-month-daycell-event-name",
                        "color || body .stec-layout-week-daycell-events .stec-layout-week-daycell-event .stec-layout-week-daycell-event-name"
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_monthweek",
                "title"   => __('Day label Font, Weight and Size', 'stec'),
                "desc"    => "",
                "name"    => "layouts_daylabel",
                "type"    => "font",
                "value"   => array(
                        "Roboto",
                        "400",
                        "14px"
                ),
                "default" => array(
                        "Roboto",
                        "400",
                        "14px"
                ),
                "css"     => array(
                        "font || body .stec-layout-month-daylabel p",
                        "font || body .stec-layout-week-daylabel p",
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_monthweek",
                "title"   => __('Grid cell date Font, Weight and Size', 'stec'),
                "desc"    => "",
                "name"    => "layouts_grid_cell",
                "type"    => "font",
                "value"   => array(
                        "Roboto",
                        "700",
                        "16px"
                ),
                "default" => array(
                        "Roboto",
                        "700",
                        "16px"
                ),
                "css"     => array(
                        "font || body .stec-layout-month-daycell .stec-layout-month-daycell-wrap .stec-layout-month-daycell-num",
                        "font || body .stec-layout-week-daycell .stec-layout-week-daycell-wrap .stec-layout-week-daycell-num"
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_monthweek",
                "title"   => __('Event name text Font, Weight and Size', 'stec'),
                "desc"    => "",
                "name"    => "layouts_grid_cell_event",
                "type"    => "font",
                "value"   => array(
                        "Roboto",
                        "400",
                        "10px"
                ),
                "default" => array(
                        "Roboto",
                        "400",
                        "10px"
                ),
                "css"     => array(
                        "font || body .stec-layout-week-daycell-eventmore-count",
                        "font || body .stec-layout-month-daycell-eventmore-count",
                        "font || body .stec-layout-month-daycell-events .stec-layout-month-daycell-event .stec-layout-month-daycell-event-name",
                        "font || body .stec-layout-week-daycell-events .stec-layout-week-daycell-event .stec-layout-week-daycell-event-name",
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_monthweek",
                "title"   => __('Add !important rule', 'stec'),
                "desc"    => "",
                "name"    => "monthweek_important",
                "type"    => "checkbox",
                "value"   => 0,
                "default" => 0
        )
);



/**
 *  Fonts and Colors GRID
 */
Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_grid",
                "title"   => __('Grid background color, Title Color, Text Color', 'stec'),
                "desc"    => __('Grid background color', 'stec'),
                "name"    => "layouts_grid_bg",
                "type"    => "color",
                "value"   => "#fff",
                "default" => "#fff",
                "css"     => array(
                        "background || body .stec-layout-grid .stec-layout-grid-event",
                )
        )
);
Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_grid",
                "name"    => "layouts_grid_title_color",
                "type"    => "color",
                "desc"    => __('Title color', 'stec'),
                "value"   => "#4d576c",
                "default" => "#4d576c",
                "css"     => array(
                        "color || body .stec-layout-grid .stec-layout-grid-event-title a"
                )
        )
);
Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_grid",
                "name"    => "layouts_grid_text_color",
                "type"    => "color",
                "value"   => "#bdc1c8",
                "default" => "#bdc1c8",
                "desc"    => __('Text color', 'stec'),
                "css"     => array(
                        "color || body .stec-layout-grid .stec-layout-grid-event span",
                        "color || body .stec-layout-grid .stec-layout-grid-event span i",
                )
        )
);



Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_grid",
        "title"   => __('"Look for more" button background color, text color, hover background color, text hover color', 'stec'),
        "desc"    => "",
        "name"    => "layout_grid_lfm_btn_bg",
        "desc"    => __('Button background color', 'stec'),
        "type"    => "color",
        "value"   => "#4d576c",
        "default" => "#4d576c",
        "css"     => array(
                "background || body .stec-layout-grid-events-all-load-more"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_grid",
        "name"    => "layout_grid_lfm_btn_text_color",
        "type"    => "color",
        "desc"    => __('Button text color', 'stec'),
        "value"   => "#ffffff",
        "default" => "#ffffff",
        "css"     => array(
                "color || body .stec-layout-grid-events-all-load-more p"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_grid",
        "name"    => "layout_grid_lfm_btn_bg_hover",
        "type"    => "color",
        "desc"    => __('Button background hover color', 'stec'),
        "value"   => "#f15e6e",
        "default" => "#f15e6e",
        "css"     => array(
                "background || body .stec-layout-grid-events-all-load-more:hover"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_grid",
        "name"    => "layout_grid_lfm_btn_text_color_hover",
        "type"    => "color",
        "desc"    => __('Button text hover color', 'stec'),
        "value"   => "#ffffff",
        "default" => "#ffffff",
        "css"     => array(
                "color || body .stec-layout-grid-events-all-load-more:hover p"
        )
));

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_grid",
                "title"   => __('Title Font, Weight and Size', 'stec'),
                "desc"    => "",
                "name"    => "layouts_grid_event_title",
                "type"    => "font",
                "value"   => array(
                        "Roboto",
                        "600",
                        "18px"
                ),
                "default" => array(
                        "Roboto",
                        "400",
                        "18px"
                ),
                "css"     => array(
                        "font || body .stec-layout-grid .stec-layout-grid-event-title a"
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_grid",
                "title"   => __('Short Description Font, Weight and Size', 'stec'),
                "desc"    => "",
                "name"    => "layouts_grid_event_shortdesc",
                "type"    => "font",
                "value"   => array(
                        "Roboto",
                        "400",
                        "14px",
                        "1.3"
                ),
                "default" => array(
                        "Roboto",
                        "400",
                        "14px",
                        "1.3"
                ),
                "css"     => array(
                        "font || body .stec-layout-grid .stec-layout-grid-event-short-desc"
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_grid",
                "title"   => __('Text Font, Weight and Size', 'stec'),
                "desc"    => "",
                "name"    => "layouts_grid_event_text",
                "type"    => "font",
                "value"   => array(
                        "Roboto",
                        "400",
                        "14px"
                ),
                "default" => array(
                        "Roboto",
                        "400",
                        "14px"
                ),
                "css"     => array(
                        "font || body .stec-layout-grid .stec-layout-grid-event span:not(.stec-layout-grid-event-short-desc)",
                        "font || body .stec-layout-grid .stec-layout-grid-event .stec-layout-grid-invited",
                        "font || body .stec-layout-grid-event-status-expired",
                        "font || body .stec-layout-grid-event-status-progress"
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_grid",
                "title"   => __('"Look for more" button Font, Weight and Size', 'stec'),
                "desc"    => "",
                "name"    => "layout_grid_lfm_btn",
                "type"    => "font",
                "value"   => array(
                        "Roboto",
                        "400",
                        "14px"
                ),
                "default" => array(
                        "Roboto",
                        "400",
                        "14px"
                ),
                "css"     => array(
                        "font || body .stec-layout-grid-events-all-load-more"
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_grid",
                "title"   => __('Grid columns', 'stec'),
                "desc"    => "",
                "name"    => "grid_columns",
                "type"    => "input",
                "value"   => "4",
                "default" => "4",
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_grid",
                "title"   => __('Grid gutter (px)', 'stec'),
                "desc"    => "",
                "name"    => "grid_gutter",
                "type"    => "input",
                "value"   => "10",
                "default" => "10",
                "req"     => true
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_grid",
                "title"   => __('Events per click', 'stec'),
                "desc"    => "",
                "name"    => "grid_per_click",
                "type"    => "input",
                "value"   => "4",
                "default" => "4",
                "req"     => true
        )
);



Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_grid",
                "title"   => __('Grid border', 'stec'),
                "desc"    => "",
                "name"    => "grid_border",
                "type"    => "checkbox",
                "value"   => 1,
                "default" => 1
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_grid",
                "title"   => __('Add !important rule', 'stec'),
                "desc"    => "",
                "name"    => "grid_important",
                "type"    => "checkbox",
                "value"   => 0,
                "default" => 0
        )
);


/**
 *  Fonts and Colors DAY
 */
Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_day",
                "title"   => __('Misc text color', 'stec'),
                "desc"    => "",
                "name"    => "layouts_day_misc_text_color",
                "type"    => "color",
                "value"   => "#4d576c",
                "default" => "#4d576c",
                "css"     => array(
                        "color || body .stec-layout-day-noevents"
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_day",
                "title"   => __('Misc text Font, Weight and Size', 'stec'),
                "desc"    => "",
                "name"    => "layouts_day_misc",
                "type"    => "font",
                "value"   => array(
                        "Roboto",
                        "700",
                        "14px"
                ),
                "default" => array(
                        "Roboto",
                        "700",
                        "14px"
                ),
                "css"     => array(
                        "font || body .stec-layout-day-noevents"
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_day",
                "title"   => __('Add !important rule', 'stec'),
                "desc"    => "",
                "name"    => "day_important",
                "type"    => "checkbox",
                "value"   => 0,
                "default" => 0
        )
);

/**
 *  Fonts and Colors EVENT PREVIEW
 */
Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_preview",
                "title"   => __('Background color, Background hover color', 'stec'),
                "desc"    => "",
                "name"    => "preview_bg",
                "type"    => "color",
                "value"   => "#ffffff",
                "default" => "#ffffff",
                "css"     => array(
                        "background || body .stec-layout-agenda-eventholder-form .stec-layout-event-preview.stec-layout-event-preview-animate-complete",
                        "background || body .stec-event-holder .stec-layout-event-preview.stec-layout-event-preview-animate-complete",
                        "background || body .stec-layout-agenda-eventholder-form .stec-layout-event-preview.stec-layout-event-preview-animate",
                        "background || body .stec-event-holder .stec-layout-event-preview.stec-layout-event-preview-animate",
                        "background || body .stec-layout-agenda-events-all-list .stec-layout-event-preview.stec-layout-event-preview-animate",
                        "background || body .stec-layout-agenda-events-all-list .stec-layout-event-preview.stec-layout-event-preview-animate-complete"
                )
        )
);
Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_preview",
                "name"    => "preview_bg_hover",
                "type"    => "color",
                "value"   => "#f0f1f2",
                "default" => "#f0f1f2",
                "css"     => array(
                        "background || body .stec-layout-event-preview:hover",
                        "background || body .stec-event-holder .stec-layout-event-preview.stec-layout-event-preview-animate-complete:hover",
                        "background || body .stec-layout-agenda-eventholder-form .stec-layout-event-preview.stec-layout-event-preview-animate-complete:hover",
                        "background || body .stec-layout-agenda-events-all-list .stec-layout-event-preview.stec-layout-event-preview-animate-complete:hover",
                        "background || body .stec-layout-agenda-events-all-list .stec-layout-event-preview.stec-layout-event-preview-animate-complete:hover",
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_preview",
                "title"   => __('Title color, Date color', 'stec'),
                "desc"    => "",
                "name"    => "preview_title_text_color",
                "type"    => "color",
                "value"   => "#4d576c",
                "default" => "#4d576c",
                "css"     => array(
                        "color || body .stec-layout-event-preview-left-text-title",
                        "color || body .stec-layout-single-preview-left-text-title"
                )
        )
);
Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_preview",
                "name"    => "preview_date_text_color",
                "type"    => "color",
                "value"   => "#bdc1c8",
                "default" => "#bdc1c8",
                "css"     => array(
                        "color || body .stec-layout-event-preview-left-text-date",
                        "color || body .stec-layout-event-preview-left-text-sub",
                        "color || body .stec-layout-single-preview-left-text-date"
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_preview",
                "title"   => __('Reminder button icon/text color and icon/text hover color', 'stec'),
                "desc"    => "",
                "name"    => "preview_reminder_btn_text_color",
                "type"    => "color",
                "value"   => "#bdc1c8",
                "default" => "#bdc1c8",
                "css"     => array(
                        "color || body .stec-layout-event-preview-right-menu",
                        "color || body .stec-layout-event-preview-left-reminder-toggle:not(.stec-layout-event-preview-left-reminder-success)",
                        "border-color || body .stec-layout-event-preview-left-reminder-toggle:not(.stec-layout-event-preview-left-reminder-success)",
                        "color || body .stec-layout-single-preview-left-reminder-toggle:not(.stec-layout-single-preview-left-reminder-success)",
                        "border-color || body .stec-layout-single-preview-left-reminder-toggle:not(.stec-layout-single-preview-left-reminder-success)",
                        "color || body .stec-layout-event-preview-left-approval-cancel",
                        "border-color || body .stec-layout-event-preview-left-approval-cancel"
                )
        )
);
Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_preview",
                "name"    => "preview_reminder_btn_text_color_hover",
                "type"    => "color",
                "value"   => "#343d46",
                "default" => "#343d46",
                "css"     => array(
                        "color || body .stec-layout-event-preview-right-menu:hover",
                        "color || body .stec-layout-event-preview-right-menu.active",
                        "color || body .stec-layout-event-preview-left-reminder-toggle.active:not(.stec-layout-event-preview-left-reminder-success)",
                        "color || body .stec-layout-single-preview-left-reminder-toggle.active:not(.stec-layout-single-preview-left-reminder-success)"
                )
        )
);

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_preview",
        "title"   => __('Expand button icon color and icon hover color', 'stec'),
        "desc"    => "",
        "name"    => "preview_expand_btn_text_color",
        "type"    => "color",
        "value"   => "#bdc1c8",
        "default" => "#bdc1c8",
        "css"     => array(
                "color || body .stec-layout-event-preview-right-event-toggle",
                "color || body .stec-layout-event-inner-intro-exports-toggle",
                "color || body .stec-layout-event-inner-intro-attachments-toggle",
                "color || body .stec-layout-single-attachments-toggle",
                "color || body .stec-layout-event-inner-schedule-tab-toggle",
                "color || body .stec-layout-single-schedule-tab-toggle",
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_preview",
        "name"    => "preview_expand_btn_text_color_hover",
        "type"    => "color",
        "value"   => "#343d46",
        "default" => "#343d46",
        "css"     => array(
                "color || body .stec-layout-event-inner-intro-exports-toggle:hover",
                "color || body .stec-layout-event-inner-intro-exports-toggle.active",
                "color || body .stec-layout-event-inner-intro-attachments-toggle:hover",
                "color || body .stec-layout-event-inner-intro-attachments-toggle.active",
                "color || body .stec-layout-single-attachments-toggle:hover",
                "color || body .stec-layout-single-attachments-toggle.active",
                "color || body .stec-layout-event-preview-right-event-toggle.active",
                "color || body .stec-layout-event-preview-right-event-toggle:hover",
                "color || body .stec-layout-event-inner-schedule-tab-toggle:hover",
                "color || body .stec-layout-event-inner-schedule-tab.open .stec-layout-event-inner-schedule-tab-toggle",
                "color || body .stec-layout-single-schedule-tab-toggle:hover",
                "color || body .stec-layout-single-schedule-tab.open .stec-layout-single-schedule-tab-toggle"
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_preview",
        "title"   => __('Reminder menu background, input background, input text color', 'stec'),
        "desc"    => "",
        "name"    => "preview_reminder_bg",
        "type"    => "color",
        "value"   => "#4d576c",
        "default" => "#4d576c",
        "css"     => array(
                "background || body .stec-layout-event-preview-reminder::before",
                "background || body .stec-layout-event-preview-reminder",
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_preview",
        "name"    => "preview_reminder_input_bg",
        "type"    => "color",
        "value"   => "#ffffff",
        "default" => "#ffffff",
        "css"     => array(
                "background || body .stec-layout-event-preview-reminder input",
                "background || body .stec-layout-event-preview-reminder-units-selector p",
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_preview",
        "name"    => "preview_reminder_input_text_color",
        "type"    => "color",
        "value"   => "#4d576c",
        "default" => "#4d576c",
        "css"     => array(
                "color || body .stec-layout-event-preview-reminder input",
                "color || body .stec-layout-event-preview-reminder-units-selector p ",
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_preview",
        "title"   => __('Remind me button background, text color, background hover color and text hover color', 'stec'),
        "desc"    => "",
        "name"    => "preview_remind_me_btn_bg",
        "type"    => "color",
        "value"   => "#fcb755",
        "default" => "#fcb755",
        "css"     => array(
                "background || body .stec-layout-event-preview-reminder button",
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_preview",
        "name"    => "preview_remind_me_btn_text_color",
        "type"    => "color",
        "value"   => "#ffffff",
        "default" => "#ffffff",
        "css"     => array(
                "color || body .stec-layout-event-preview-reminder button",
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_preview",
        "name"    => "preview_remind_me_btn_bg_hover",
        "type"    => "color",
        "value"   => "#f15e6e",
        "default" => "#f15e6e",
        "css"     => array(
                "background || body .stec-layout-event-preview-reminder button:hover",
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_preview",
        "name"    => "preview_remind_me_btn_text_color_hover",
        "type"    => "color",
        "value"   => "#ffffff",
        "default" => "#ffffff",
        "css"     => array(
                "color || body .stec-layout-event-preview-reminder button:hover",
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_preview",
        "title"   => __('Reminder dropdown background color, text color, background hover color, text hover color', 'stec'),
        "desc"    => "",
        "name"    => "preview_reminder_dropdown_bg",
        "type"    => "color",
        "value"   => "#f15e6e",
        "default" => "#f15e6e",
        "css"     => array(
                "background || body .stec-layout-event-preview-reminder-units-selector li",
                "background || body .stec-layout-single-preview-reminder-units-selector li",
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_preview",
        "name"    => "preview_reminder_dropdown_text_color",
        "type"    => "color",
        "value"   => "#ffffff",
        "default" => "#ffffff",
        "css"     => array(
                "color || body .stec-layout-event-preview-reminder-units-selector li",
                "color || body .stec-layout-single-preview-reminder-units-selector li",
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_preview",
        "name"    => "preview_reminder_dropdown_bg_hover",
        "type"    => "color",
        "value"   => "#e25261",
        "default" => "#e25261",
        "css"     => array(
                "background || body .stec-layout-event-preview-reminder-units-selector:hover p",
                "background || body .stec-layout-event-preview-reminder-units-selector li:hover",
                "background || body .stec-layout-single-preview-reminder-units-selector:hover p",
                "background || body .stec-layout-single-preview-reminder-units-selector li:hover",
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_preview",
        "name"    => "preview_reminder_dropdown_text_color_hover",
        "type"    => "color",
        "value"   => "#ffffff",
        "default" => "#ffffff",
        "css"     => array(
                "color || body .stec-layout-event-preview-reminder-units-selector:hover p",
                "color || body .stec-layout-event-preview-reminder-units-selector li:hover",
                "color || body .stec-layout-single-preview-reminder-units-selector:hover p",
                "color || body .stec-layout-single-preview-reminder-units-selector li:hover",
        )
));

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_preview",
                "title"   => __('Featured tag background color, text color', 'stec'),
                "desc"    => "",
                "name"    => "featured_bg",
                "type"    => "color",
                "value"   => "#f15e6e",
                "default" => "#f15e6e",
                "css"     => array(
                        "background || body .stec-layout-event-preview-left-text-featured span",
                        "color || body .stec-layout-event-preview-left-text-featured i",
                        "background || body .stec-layout-single-preview-left-text-featured span",
                        "color || body .stec-layout-single-preview-left-text-featured i"
                )
        )
);
Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_preview",
                "name"    => "featured_text_color",
                "type"    => "color",
                "value"   => "#ffffff",
                "default" => "#ffffff",
                "css"     => array(
                        "color || body .stec-layout-event-preview-left-text-featured span",
                        "color || body .stec-layout-single-preview-left-text-featured span"
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_preview",
                "title"   => __('Title Font, Weight, Size and Line-Height', 'stec'),
                "desc"    => "",
                "name"    => "preview_title",
                "type"    => "font",
                "value"   => array(
                        "Roboto",
                        "400",
                        "18px",
                        "1.2"
                ),
                "default" => array(
                        "Roboto",
                        "400",
                        "18px",
                        "1.2"
                ),
                "css"     => array(
                        "font || body .stec-layout-event-preview-left-text-title",
                        "font || body .stec-layout-single-preview-left-text-title"
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_preview",
                "title"   => __('Date Font, Weight and Size', 'stec'),
                "desc"    => "",
                "name"    => "preview_date",
                "type"    => "font",
                "value"   => array(
                        "Roboto",
                        "400",
                        "14px"
                ),
                "default" => array(
                        "Roboto",
                        "400",
                        "14px"
                ),
                "css"     => array(
                        "font || body .stec-layout-event-preview-left-text-date",
                        "font || body .stec-layout-single-preview-left-text-date",
                        "font || body .stec-layout-event-preview-left-text-sub"
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_preview",
                "title"   => __('Reminder Font, Weight and Size', 'stec'),
                "desc"    => "",
                "name"    => "preview_reminder",
                "type"    => "font",
                "value"   => array(
                        "Roboto",
                        "400",
                        "14px"
                ),
                "default" => array(
                        "Roboto",
                        "400",
                        "14px"
                ),
                "css"     => array(
                        "font || body .stec-layout-event-preview-reminder input",
                        "font || body .stec-layout-event-preview-reminder button",
                        "font || body .stec-layout-event-preview-reminder-units-selector p",
                        "font || body .stec-layout-event-preview-reminder-units-selector li",
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_preview",
                "title"   => __('Reminder mobile button Font, Weight and Size', 'stec'),
                "desc"    => "",
                "name"    => "preview_reminder_small",
                "type"    => "font",
                "value"   => array(
                        "Roboto",
                        "400",
                        "11px"
                ),
                "default" => array(
                        "Roboto",
                        "400",
                        "11px"
                ),
                "css"     => array(
                        "font || body .stec-layout-single-preview-left-reminder-toggle",
                        "font || body .stec-layout-event-preview-left-reminder-toggle",
                        "font || body .stec-layout-event-preview-left-approval-cancel"
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_preview",
                "title"   => __('Featured tag Font, Weight and Size', 'stec'),
                "desc"    => "",
                "name"    => "featured_font",
                "type"    => "font",
                "value"   => array(
                        "Roboto",
                        "400",
                        "10px"
                ),
                "default" => array(
                        "Roboto",
                        "400",
                        "10px"
                ),
                "css"     => array(
                        "font || body .stec-layout-event-preview-left-text-featured span",
                        "font || body .stec-layout-single-preview-left-text-featured span"
                )
        )
);

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_preview",
                "title"   => __('Add !important rule', 'stec'),
                "desc"    => "",
                "name"    => "preview_important",
                "type"    => "checkbox",
                "value"   => 0,
                "default" => 0
        )
);

/**
 *  Fonts and Colors EVENT INNER
 */
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "title"   => __('Tab background color and text color, background active color, text active color', 'stec'),
        "desc"    => "",
        "name"    => "event_tab_bg",
        "type"    => "color",
        "value"   => "#f8f9fa",
        "default" => "#f8f9fa",
        "css"     => array(
                "background || body .stec-layout-event-inner-top-tabs"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "name"    => "event_tab_text_color",
        "type"    => "color",
        "value"   => "#bdc1c8",
        "default" => "#bdc1c8",
        "css"     => array(
                "color || body .stec-layout-event-inner-top-tabs p",
                "color || body .stec-layout-event-inner-top-tabs i"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "name"    => "event_tab_bg_active",
        "type"    => "color",
        "value"   => "#ffffff",
        "default" => "#ffffff",
        "css"     => array(
                "background || body .stec-layout-event-inner-top-tabs li.active"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "name"    => "event_tab_text_color_active",
        "type"    => "color",
        "value"   => "#4d576c",
        "default" => "#4d576c",
        "css"     => array(
                "color || body .stec-layout-event-inner-top-tabs li.active p",
                "color || body .stec-layout-event-inner-top-tabs li.active i"
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "title"   => __('Wrapper Background Color', 'stec'),
        "desc"    => "",
        "name"    => "event_bg",
        "type"    => "color",
        "value"   => "#ffffff",
        "default" => "#ffffff",
        "css"     => array(
                "background || body .stec-layout-event-inner"
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "title"   => __('Buttons background color, buttons text color, background hover color, text hover color', 'stec'),
        "desc"    => "",
        "name"    => "event_btn_bg",
        "type"    => "color",
        "value"   => "#4d576c",
        "default" => "#4d576c",
        "css"     => array(
                "background || body .stec-layout-event-btn-fontandcolor",
                "background || body .stec-layout-single-btn-fontandcolor"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "name"    => "event_btn_text_color",
        "type"    => "color",
        "value"   => "#ffffff",
        "default" => "#ffffff",
        "css"     => array(
                "color || body .stec-layout-event-btn-fontandcolor",
                "color || body .stec-layout-single-btn-fontandcolor"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "name"    => "event_btn_bg_hover",
        "type"    => "color",
        "value"   => "#f15e6e",
        "default" => "#f15e6e",
        "css"     => array(
                "background || body .stec-layout-event-btn-fontandcolor.active",
                "background || body .stec-layout-event-btn-fontandcolor:hover",
                "background || body .stec-layout-single-btn-fontandcolor.active",
                "background || body .stec-layout-single-btn-fontandcolor:hover"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "name"    => "event_btn_text_color_hover",
        "type"    => "color",
        "value"   => "#ffffff",
        "default" => "#ffffff",
        "css"     => array(
                "color || body .stec-layout-event-btn-fontandcolor.active",
                "color || body .stec-layout-event-btn-fontandcolor:hover",
                "color || body .stec-layout-single-btn-fontandcolor.active",
                "color || body .stec-layout-single-btn-fontandcolor:hover"
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "title"   => __('Secondary buttons background color, border color, text color, background hover color, border hover color, text hover color', 'stec'),
        "desc"    => "",
        "name"    => "event_btn_sec_bg",
        "type"    => "color",
        "value"   => "#ffffff",
        "default" => "#ffffff",
        "css"     => array(
                "background || body .stec-layout-event-btn-sec-fontandcolor",
                "background || body .stec-layout-single-btn-sec-fontandcolor"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "name"    => "event_btn_sec_border_color",
        "type"    => "color",
        "value"   => "#e5e5e5",
        "default" => "#e5e5e5",
        "css"     => array(
                "border-color || body .stec-layout-event-btn-sec-fontandcolor",
                "border-color || body .stec-layout-single-btn-sec-fontandcolor"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "name"    => "event_btn_sec_text_color",
        "type"    => "color",
        "value"   => "#999da2",
        "default" => "#999da2",
        "css"     => array(
                "color || body .stec-layout-event-btn-sec-fontandcolor",
                "color || body .stec-layout-single-btn-sec-fontandcolor"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "name"    => "event_btn_sec_bg_hover",
        "type"    => "color",
        "value"   => "#f15e6e",
        "default" => "#f15e6e",
        "css"     => array(
                "background || body .stec-layout-event-btn-sec-fontandcolor.active",
                "background || body .stec-layout-event-btn-sec-fontandcolor:hover",
                "background || body .stec-layout-single-btn-sec-fontandcolor.active",
                "background || body .stec-layout-single-btn-sec-fontandcolor:hover"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "name"    => "event_btn_sec_border_color_hover",
        "type"    => "color",
        "value"   => "#f15e6e",
        "default" => "#f15e6e",
        "css"     => array(
                "border-color || body .stec-layout-event-btn-sec-fontandcolor.active",
                "border-color || body .stec-layout-event-btn-sec-fontandcolor:hover",
                "border-color || body .stec-layout-single-btn-sec-fontandcolor.active",
                "border-color || body .stec-layout-single-btn-sec-fontandcolor:hover"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "name"    => "event_btn_sec_text_color_hover",
        "type"    => "color",
        "value"   => "#ffffff",
        "default" => "#ffffff",
        "css"     => array(
                "color || body .stec-layout-event-btn-sec-fontandcolor.active",
                "color || body .stec-layout-event-btn-sec-fontandcolor:hover",
                "color || body .stec-layout-single-btn-sec-fontandcolor.active",
                "color || body .stec-layout-single-btn-sec-fontandcolor:hover"
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "title"   => __('Titles text color, secondary titles text color, normal text color, links text color, links text hover color', 'stec'),
        "desc"    => "",
        "name"    => "event_title_text_color",
        "type"    => "color",
        "value"   => "#4d576c",
        "default" => "#4d576c",
        "css"     => array(
                "color || body .stec-layout-event-title-fontandcolor"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "name"    => "event_title2_text_color",
        "type"    => "color",
        "value"   => "#4d576c",
        "default" => "#4d576c",
        "css"     => array(
                "color || body .stec-layout-event-title2-fontandcolor",
                "color || body .stec-layout-event-title2-fontandcolor a",
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "name"    => "event_text_color",
        "type"    => "color",
        "value"   => "#999da2",
        "default" => "#999da2",
        "css"     => array(
                "color || body .stec-layout-event-text-fontandcolor"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "name"    => "event_link_text_color",
        "type"    => "color",
        "value"   => "#4d576c",
        "default" => "#4d576c",
        "css"     => array(
                "color || body .stec-layout-event-inner-intro-exports form button",
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "name"    => "event_link_text_color_hover",
        "type"    => "color",
        "value"   => "#f15e6f",
        "default" => "#f15e6f",
        "css"     => array(
                "color || body .stec-layout-event-inner-intro-attachment a:hover",
                "color || body .stec-layout-single-attachment a:hover",
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "title"   => __('Input background color, text color', 'stec'),
        "desc"    => "",
        "name"    => "event_input_bg",
        "type"    => "color",
        "value"   => "#f1f1f1",
        "default" => "#f1f1f1",
        "css"     => array(
                "background || body .stec-layout-event-input-fontandcolor"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "name"    => "event_input_text_color",
        "type"    => "color",
        "value"   => "#999da2",
        "default" => "#999da2",
        "css"     => array(
                "color || body .stec-layout-event-input-fontandcolor"
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "title"   => __('Counter numbers color, text color', 'stec'),
        "desc"    => "",
        "name"    => "event_counter_num_text_color",
        "type"    => "color",
        "value"   => "#202020",
        "default" => "#202020",
        "css"     => array(
                "color || body .stec-layout-event-inner-intro-counter-num",
                "color || body .stec-layout-single-counter-num",
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "name"    => "event_counter_text_color",
        "type"    => "color",
        "value"   => "#999da2",
        "default" => "#999da2",
        "css"     => array(
                "color || body .stec-layout-event-inner-intro-counter-label",
                "color || body .stec-layout-single-counter-label",
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "title"   => __('Schedule Title text color, Date text color', 'stec'),
        "desc"    => __('Schedule title text color', 'stec'),
        "name"    => "event_schedule_title_text_color",
        "type"    => "color",
        "value"   => "#4d576c",
        "default" => "#4d576c",
        "css"     => array(
                "color || body .stec-layout-event-inner-schedule-tab-right-title span",
                "color || body .stec-layout-single-schedule-tab-right-title span",
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "desc"    => __('Schedule date text color', 'stec'),
        "name"    => "event_schedule_date_text_color",
        "type"    => "color",
        "value"   => "#bdc1c8",
        "default" => "#bdc1c8",
        "css"     => array(
                "color || body .stec-layout-event-inner-schedule-tab-left span",
                "color || body .stec-layout-single-schedule-tab-left span",
        )
));


Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "title"   => __('Forecast section titles color, Summary text color, Today temperature text color, Table header background, General text color', 'stec'),
        "desc"    => __('Forecast section titles color', 'stec'),
        "name"    => "event_forecast_section_title_text_color",
        "type"    => "color",
        "value"   => "#4d576c",
        "default" => "#4d576c",
        "css"     => array(
                "color || body .stec-layout-event-inner-forecast-top-title",
                "color || body .stec-layout-event-inner-forecast-details > div > p",
                "color || body .stec-layout-single-forecast-top-title",
                "color || body .stec-layout-single-forecast-details > div > p"
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "desc"    => __('Summary text color', 'stec'),
        "name"    => "event_forecast_summary_text_color",
        "type"    => "color",
        "value"   => "#4d576c",
        "default" => "#4d576c",
        "css"     => array(
                "color || body .stec-layout-event-inner-forecast-today-left-current-text",
                "color || body .stec-layout-single-forecast-today-left-current-text",
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "desc"    => __('Today temperature text color', 'stec'),
        "name"    => "event_forecast_current_temp_text_color",
        "type"    => "color",
        "value"   => "#999da2",
        "default" => "#999da2",
        "css"     => array(
                "color || body .stec-layout-event-inner-forecast-today-left-current-temp",
                "color || body .stec-layout-single-forecast-today-left-current-temp",
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "desc"    => __('Table header background', 'stec'),
        "name"    => "event_forecast_table_header_bg",
        "type"    => "color",
        "value"   => "#f8f9fa",
        "default" => "#f8f9fa",
        "css"     => array(
                "background || body .stec-layout-event-inner-forecast-details-left-forecast-top",
                "background || body .stec-layout-single-forecast-details-left-forecast-top",
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "desc"    => __('General text color', 'stec'),
        "name"    => "event_forecast_text_color",
        "type"    => "color",
        "value"   => "#bdc1c8",
        "default" => "#bdc1c8",
        "css"     => array(
                "color || body .stec-layout-event-inner-forecast-details-left-forecast-top p",
                "color || body .stec-layout-event-inner-forecast-details-left-forecast-day p",
                "color || body .stec-layout-event-inner-forecast-today-right, body .stec-layout-event-inner-forecast-top-date",
                "color || body .stec-layout-event-inner-forecast-top-date",
                "color || body .stec-layout-single-forecast-details-left-forecast-top p",
                "color || body .stec-layout-single-forecast-details-left-forecast-day p",
                "color || body .stec-layout-single-forecast-today-right, body .stec-layout-single-forecast-top-date",
                "color || body .stec-layout-single-forecast-top-date"
        )
));


Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "title"   => __('Title Font, Weight and Size', 'stec'),
        "desc"    => "",
        "name"    => "event_title",
        "type"    => "font",
        "value"   => array(
                "Roboto",
                "400",
                "30px"
        ),
        "default" => array(
                "Roboto",
                "400",
                "30px"
        ),
        "css"     => array(
                "font || body .stec-layout-event-title-fontandcolor"
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "title"   => __('Secondary Title Font, Weight and Size', 'stec'),
        "desc"    => "",
        "name"    => "event_title2",
        "type"    => "font",
        "value"   => array(
                "Roboto",
                "400",
                "16px"
        ),
        "default" => array(
                "Roboto",
                "400",
                "16px"
        ),
        "css"     => array(
                "font || body .stec-layout-event-inner-intro-media-content-subs p",
                "font || body .stec-layout-event-inner-intro-media-content > div div p",
                "font || body .stec-layout-event-title2-fontandcolor",
                "font || body .stec-layout-event-inner-top-tabs p"
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "title"   => __('Buttons Font, Weight and Size', 'stec'),
        "desc"    => "",
        "name"    => "event_btn",
        "type"    => "font",
        "value"   => array(
                "Roboto",
                "400",
                "16px"
        ),
        "default" => array(
                "Roboto",
                "400",
                "16px"
        ),
        "css"     => array(
                "font || body .stec-layout-event-btn-fontandcolor",
                "font || body .stec-layout-single-btn-fontandcolor"
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "title"   => __('Secondary buttons Font, Weight and Size', 'stec'),
        "desc"    => "",
        "name"    => "event_btn_sec",
        "type"    => "font",
        "value"   => array(
                "Roboto",
                "400",
                "16px"
        ),
        "default" => array(
                "Roboto",
                "400",
                "14px"
        ),
        "css"     => array(
                "font || body .stec-layout-event-btn-sec-fontandcolor",
                "font || body .stec-layout-single-btn-sec-fontandcolor"
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "title"   => __('Normal text Font, Weight, Size and Line-Height', 'stec'),
        "desc"    => "",
        "name"    => "event_text",
        "type"    => "font",
        "value"   => array(
                "Roboto",
                "400",
                "16px",
                "1.6",
        ),
        "default" => array(
                "Roboto",
                "400",
                "16px",
                "1.6"
        ),
        "css"     => array(
                "font || body .stec-layout-event-inner-intro-media-content > div div span",
                "font || body .stec-layout-event-inner-intro-media-content-subs span",
                "font || body .stec-layout-event-text-fontandcolor",
                "font || body .stec-layout-event-input-fontandcolor"
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "title"   => __('Event counter numbers Font, Weight and Size', 'stec'),
        "desc"    => "",
        "name"    => "event_counter_num",
        "type"    => "font",
        "value"   => array(
                "Roboto",
                "700",
                "40px"
        ),
        "default" => array(
                "Roboto",
                "700",
                "40px"
        ),
        "css"     => array(
                "font || body .stec-layout-event-inner-intro-counter-num",
                "font || body .stec-layout-single-counter-num",
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "title"   => __('Event counter text Font, Weight and Size', 'stec'),
        "desc"    => "",
        "name"    => "event_counter_text",
        "type"    => "font",
        "value"   => array(
                "Roboto",
                "400",
                "14px"
        ),
        "default" => array(
                "Roboto",
                "400",
                "14px"
        ),
        "css"     => array(
                "font || body .stec-layout-event-inner-intro-counter-label",
                "font || body .stec-layout-single-counter-label",
        )
));


Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "title"   => __('Schedule Title Font, Weight and Size', 'stec'),
        "desc"    => __('Schedule Title Font, Weight and Size', 'stec'),
        "name"    => "event_schedule_title",
        "type"    => "font",
        "value"   => array(
                "Roboto",
                "500",
                "18px"
        ),
        "default" => array(
                "Roboto",
                "500",
                "18px"
        ),
        "css"     => array(
                "font || body .stec-layout-event-inner-schedule-tab-right-title span",
                "font || body .stec-layout-single-schedule-tab-right-title span",
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "title"   => __('Schedule date Font, Weight and Size', 'stec'),
        "desc"    => __('Schedule date Font, Weight and Size', 'stec'),
        "name"    => "event_schedule_date",
        "type"    => "font",
        "value"   => array(
                "Roboto",
                "400",
                "14px"
        ),
        "default" => array(
                "Roboto",
                "400",
                "14px"
        ),
        "css"     => array(
                "font || body .stec-layout-event-inner-schedule-tab-left span",
                "font || body .stec-layout-single-schedule-tab-left span",
        )
));


Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "title"   => __('Forecast section titles Font, Weight and Size', 'stec'),
        "desc"    => "",
        "name"    => "event_forecast_section_title",
        "type"    => "font",
        "value"   => array(
                "Roboto",
                "500",
                "24px"
        ),
        "default" => array(
                "Roboto",
                "500",
                "24px"
        ),
        "css"     => array(
                "font || body .stec-layout-event-inner-forecast-top-title",
                "font || body .stec-layout-event-inner-forecast-details > div > p",
                "font || body .stec-layout-single-forecast-top-title",
                "font || body .stec-layout-single-forecast-details > div > p"
        )
));



Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "title"   => __('Forecast Summary Font, Weight and Size ', 'stec'),
        "desc"    => __('Forecast Summary Font, Weight and Size', 'stec'),
        "name"    => "event_forecast_summary",
        "type"    => "font",
        "value"   => array(
                "Roboto",
                "500",
                "20px"
        ),
        "default" => array(
                "Roboto",
                "500",
                "20px"
        ),
        "css"     => array(
                "font || body .stec-layout-event-inner-forecast-today-left-current-text",
                "font || body .stec-layout-single-forecast-today-left-current-text",
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "title"   => __('Forecast Today temperature Font, Weight and Size', 'stec'),
        "desc"    => __('Forecast Today temperature Font, Weight and Size', 'stec'),
        "name"    => "event_forecast_current_temp",
        "type"    => "font",
        "value"   => array(
                "Roboto",
                "400",
                "43px"
        ),
        "default" => array(
                "Roboto",
                "400",
                "43px"
        ),
        "css"     => array(
                "font || body .stec-layout-event-inner-forecast-today-left-current-temp",
                "font || body .stec-layout-single-forecast-today-left-current-temp",
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "title"   => __('Forecast Table header Font, Weight and Size', 'stec'),
        "desc"    => __('Forecast Table header Font, Weight and Size', 'stec'),
        "name"    => "event_forecast_table_header",
        "type"    => "font",
        "value"   => array(
                "Roboto",
                "500",
                "12px"
        ),
        "default" => array(
                "Roboto",
                "500",
                "12px"
        ),
        "css"     => array(
                "font || body .stec-layout-event-inner-forecast-details-left-forecast-top p",
                "font || body .stec-layout-single-forecast-details-left-forecast-top p",
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "title"   => __('Forecast General text Font, Weight and Size', 'stec'),
        "desc"    => __('Forecast General text Font, Weight and Size', 'stec'),
        "name"    => "event_forecast_text",
        "type"    => "font",
        "value"   => array(
                "Roboto",
                "400",
                "16px"
        ),
        "default" => array(
                "Roboto",
                "400",
                "16px"
        ),
        "css"     => array(
                "font || body .stec-layout-event-inner-forecast-details-left-forecast-day p",
                "font || body .stec-layout-event-inner-forecast-today-right p",
                "font || body .stec-layout-event-inner-forecast-top-date",
                "font || body .stec-layout-single-forecast-details-left-forecast-day p",
                "font || body .stec-layout-single-forecast-today-right p",
                "font || body .stec-layout-single-forecast-top-date"
        )
));





Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "title"   => __('Event info title and text align', 'stec'),
        "desc"    => "",
        "name"    => "event_intro_title_align",
        "type"    => "select",
        "value"   => 'center',
        "default" => 'center',
        "select"  => array(
                'left'   => __('Left', 'stec'),
                'center' => __('Center', 'stec'),
                'right'  => __('Right', 'stec')
        ),
        "req"     => true,
        "css"     => array(
                "text-align || body .stec-layout-event-inner-intro .stec-layout-event-inner-intro-title"
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_event",
        "name"    => "event_intro_text_align",
        "type"    => "select",
        "value"   => 'left',
        "default" => 'left',
        "select"  => array(
                'left'   => __('Left', 'stec'),
                'center' => __('Center', 'stec'),
                'right'  => __('Right', 'stec')
        ),
        "req"     => true,
        "css"     => array(
                "text-align || body .stec-layout-event-inner-intro-desc"
        )
));

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_event",
                "title"   => __('Add !important rule', 'stec'),
                "desc"    => "",
                "name"    => "event_important",
                "type"    => "checkbox",
                "value"   => 0,
                "default" => 0
        )
);


/**
 * Tooltip css tab
 */
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_tooltip",
        "title"   => __('Title Color, Text Color, Background Color', 'stec'),
        "desc"    => "",
        "name"    => "tooltip_title_color",
        "type"    => "color",
        "value"   => "#4d576c",
        "default" => "#4d576c",
        "css"     => array(
                "color || body .stec-tooltip-title"
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_tooltip",
        "desc"    => "",
        "name"    => "tooltip_text_color",
        "type"    => "color",
        "value"   => "#9599a2",
        "default" => "#9599a2",
        "css"     => array(
                "color || body .stec-tooltip-desc",
                "color || body .stec-tooltip-location",
                "color || body .stec-tooltip-timespan"
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_tooltip",
        "desc"    => "",
        "name"    => "tooltip_bg",
        "type"    => "color",
        "value"   => "#ffffff",
        "default" => "#ffffff",
        "css"     => array(
                "background || body .stec-tooltip"
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_tooltip",
        "title"   => __('Counter Text Color & Background Color', 'stec'),
        "desc"    => "",
        "name"    => "tooltip_clock_color",
        "type"    => "color",
        "value"   => "#ffffff",
        "default" => "#ffffff",
        "css"     => array(
                "color || body .stec-tooltip-counter"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_tooltip",
        "desc"    => "",
        "name"    => "tooltip_clock_bg",
        "type"    => "color",
        "value"   => "#4d576c",
        "default" => "#4d576c",
        "css"     => array(
                "background || body .stec-tooltip-counter"
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_tooltip",
        "title"   => __('Expired Text Color & Background Color', 'stec'),
        "desc"    => "",
        "name"    => "tooltip_expired_color",
        "type"    => "color",
        "value"   => "#ffffff",
        "default" => "#ffffff",
        "css"     => array(
                "color || body .stec-tooltip-expired"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_tooltip",
        "desc"    => "",
        "name"    => "tooltip_expired_bg",
        "type"    => "color",
        "value"   => "#f15e6e",
        "default" => "#f15e6e",
        "css"     => array(
                "background || body .stec-tooltip-expired"
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_tooltip",
        "title"   => __('In Progress Text Color & Background Color', 'stec'),
        "desc"    => "",
        "name"    => "tooltip_prog_color",
        "type"    => "color",
        "value"   => "#ffffff",
        "default" => "#ffffff",
        "css"     => array(
                "color || body .stec-tooltip-progress"
        )
));
Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_tooltip",
        "desc"    => "",
        "name"    => "tooltip_prog_bg",
        "type"    => "color",
        "value"   => "#53b32b",
        "default" => "#53b32b",
        "css"     => array(
                "background || body .stec-tooltip-progress"
        )
));


Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_tooltip",
        "title"   => __('Title Font', 'stec'),
        "desc"    => "",
        "name"    => "tooltip_title_font",
        "type"    => "font",
        "value"   => array(
                'Roboto',
                '400',
                '20px'
        ),
        "default" => array(
                'Roboto',
                '400',
                '20px'
        ),
        "css"     => array(
                "font || body .stec-tooltip-title"
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_tooltip",
        "title"   => __('Text Font', 'stec'),
        "desc"    => "",
        "name"    => "tooltip_text_font",
        "type"    => "font",
        "value"   => array(
                'Roboto',
                '400',
                '14px',
                '1.3'
        ),
        "default" => array(
                'Roboto',
                '400',
                '14px',
                '1.3'
        ),
        "css"     => array(
                "font || body .stec-tooltip-desc",
                "font || body .stec-tooltip-location",
                "font || body .stec-tooltip-timespan"
        )
));

Settings::register_admin_setting(array(
        "page"    => "stec_menu__fontsandcolors_tooltip",
        "title"   => __('Status and Counter Font', 'stec'),
        "desc"    => "",
        "name"    => "tooltip_status_clock_font",
        "type"    => "font",
        "value"   => array(
                'Roboto',
                '400',
                '10px'
        ),
        "default" => array(
                'Roboto',
                '400',
                '10px'
        ),
        "css"     => array(
                "font || body .stec-tooltip-status",
                "font || body .stec-tooltip-counter"
        )
));

Settings::register_admin_setting(
        array(
                "page"    => "stec_menu__fontsandcolors_tooltip",
                "title"   => __('Add !important rule', 'stec'),
                "desc"    => "",
                "name"    => "tooltip_important",
                "type"    => "checkbox",
                "value"   => 0,
                "default" => 0
        )
);


/**
 * Custom css Tab
 */
Settings::register_admin_setting(array(
        "page"  => "stec_menu__fontsandcolors_custom",
        "title" => __('Custom Style', 'stec'),
        "desc"  => "",
        "name"  => "custom_css",
        "type"  => "textarea"
));


/**
 * Cache settings
 */
Settings::register_admin_setting(array(
        "page"    => "stec_menu__cache",
        "title"   => __('Cache', 'stec'),
        "desc"    => "",
        "name"    => "cache",
        "type"    => "select",
        "value"   => "0",
        "default" => "0",
        "select"  => array(
                '0' => __('No', 'stec'),
                '1' => __('Yes', 'stec'),
        ),
        "req"     => true
));

Settings::delete_admin_settings('stec_menu__cache', array(
        'cache_events', 'cache_forecast'
));
