<?php
/**
 * Uninstall
**/
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();


/* Options
------------------------------------------ */
delete_option( 'wpjmel_enable_city_suggest' );
delete_option( 'wpjmel_enable_map' );
delete_option( 'wpjmel_map_start_location' );
delete_option( 'wpjmel_google_maps_api_key' );
delete_option( 'wpjmel_start_geo_lat' );
delete_option( 'wpjmel_start_geo_long' );