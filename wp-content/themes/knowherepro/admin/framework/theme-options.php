<?php

/**
 * KnowherePro Theme Options
 */
require_once( get_template_directory() . '/admin/framework/functions.php' );

// KnowherePro Theme Settings Options
require_once( get_template_directory() . '/admin/framework/theme-options/settings.php' );

require_once( get_template_directory() . '/admin/framework/theme-options/save-settings.php' );

if ( get_option('knowhere_init_theme', '0') != '1' ) { knowhere_check_theme_options(); }
