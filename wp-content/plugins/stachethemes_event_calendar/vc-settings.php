<?php

namespace Stachethemes\Stec;




function stec_integrateWithVC() {

    vc_map(array(
            "name"        => "Event Calendar",
            'description' => __('Event Calendar by Stachethemes', 'stec'),
            "base"        => "stachethemes_ec",
            "class"       => "",
            "icon"        => plugins_url('admin/img/avatar.jpg', __FILE__),
            "category"    => "Stachethemes",
            "params"      => array(
                    array(
                            "type"        => "textfield",
                            "holder"      => "div",
                            "heading"     => __('Calendar ID#', 'stec'),
                            "param_name"  => "cal",
                            "value"       => "",
                            "description" => __('Display only selected calendars (optional). Example: 1,2,3,4', 'stec')
                    ),
                    array(
                            "type"        => "dropdown",
                            "heading"     => __('Default View', 'stec'),
                            "param_name"  => "view",
                            "admin_label" => true,
                            "value"       => array(
                                    __("Agenda", 'stec') => 'agenda',
                                    __("Month", 'stec')  => 'month',
                                    __("Week", 'stec')   => 'week',
                                    __("Day", 'stec')    => 'day'
                            ),
                            "description" => __('Default calendar view', 'stec')
                    ),
                    array(
                            "type"        => "dropdown",
                            "heading"     => __('Top Menu', 'stec'),
                            "param_name"  => "show_top",
                            "admin_label" => true,
                            "value"       => array(
                                    __("Default", 'stec') => '',
                                    __("Hide", 'stec')    => 0,
                                    __("Show", 'stec')    => 1,
                            ),
                            "description" => __('Show/Hide top menu', 'stec')
                    ),
                    array(
                            "type"        => "dropdown",
                            "heading"     => __('Different Views Buttons', 'stec'),
                            "param_name"  => "show_views",
                            "admin_label" => true,
                            "value"       => array(
                                    __("Default", 'stec') => '',
                                    __("Hide", 'stec')    => 0,
                                    __("Show", 'stec')    => 1,
                            ),
                            "description" => __("If set to hide users won't be able to switch calendar layouts", 'stec')
                    ),
                    array(
                            "type"        => "dropdown",
                            "heading"     => __('Search Button', 'stec'),
                            "param_name"  => "show_search",
                            "admin_label" => true,
                            "value"       => array(
                                    __("Default", 'stec') => '',
                                    __("Hide", 'stec')    => 0,
                                    __("Show", 'stec')    => 1,
                            ),
                            "description" => __('Show/Hide Search button', 'stec')
                    ),
                    array(
                            "type"        => "dropdown",
                            "heading"     => __('Calendar Filter Button', 'stec'),
                            "param_name"  => "show_calfilter",
                            "admin_label" => true,
                            "value"       => array(
                                    __("Default", 'stec') => '',
                                    __("Hide", 'stec')    => 0,
                                    __("Show", 'stec')    => 1,
                            ),
                            "description" => __('Show/Hide calendar filter button', 'stec')
                    ),
                    array(
                            "type"        => "dropdown",
                            "heading"     => __('Agenda Calendar Display', 'stec'),
                            "param_name"  => "agenda_cal_display",
                            "admin_label" => true,
                            "value"       => array(
                                    __("Default", 'stec') => '',
                                    __("Hide", 'stec')    => 0,
                                    __("Show", 'stec')    => 1,
                            ),
                            "description" => __('Show/Hide calendar slider from Agenda view', 'stec')
                    ),
                    array(
                            "type"        => "dropdown",
                            "heading"     => __('Event Tooltip', 'stec'),
                            "param_name"  => "tooltip_display",
                            "admin_label" => true,
                            "value"       => array(
                                    __("Default", 'stec') => '',
                                    __("Hide", 'stec')    => 0,
                                    __("Show", 'stec')    => 1,
                            ),
                            "description" => __('Show/Hide Tooltip on event hover', 'stec')
                    ),
                    array(
                            "type"        => "dropdown",
                            "heading"     => __('Display "Create an event" form', 'stec'),
                            "param_name"  => "show_create_event_form",
                            "admin_label" => true,
                            "value"       => array(
                                    __("Default", 'stec') => '',
                                    __("Hide", 'stec')    => 0,
                                    __("Show", 'stec')    => 1,
                            ),
                            "description" => __('Show/Hide "Create an event" form', 'stec')
                    ),
            )
    ));

    vc_map(array(
            "name"        => "Event Calendar - Create Event Form",
            'description' => __('Create Event Form for Stachethemes Event Calendar', 'stec'),
            "base"        => "stachethemes_ec_create_form",
            "class"       => "",
            "icon"        => plugins_url('admin/img/avatar.jpg', __FILE__),
            "category"    => "Stachethemes",
            "params"      => array(
                    array(
                            "type"        => "textfield",
                            "holder"      => "div",
                            "heading"     => __('Button selector (.example-button or #example-button)', 'stec'),
                            "param_name"  => "selector",
                            "value"       => "",
                            "description" => __('Button class or id name. Transforms the form into popup', 'stec')
                    ),
                    array(
                            "type"        => "textfield",
                            "holder"      => "div",
                            "heading"     => __('Filter Calendars', 'stec'),
                            "param_name"  => "create_form_cal",
                            "value"       => "",
                            "description" => __('Include only following calendars by calendar id. Example 1,2,3,4', 'stec')
                    ),
            )
    ));
}

add_action('vc_before_init', 'Stachethemes\Stec\stec_integrateWithVC');
