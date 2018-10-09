<?php
/*
Plugin URI: https://www.team-ever.com
Plugin Name: everwprgpd
Description: Utilisez ce plugin pour être au plus proche de la loi RGPD
Version: 1.0
Author: Ever Team
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: everwprgpd
Domain Path:  /languages
Author URI: https://www.team-ever.com/
License: GPL2
*/
define('WP_USE_THEMES', false);

require_once('../../../../../wp-load.php');

global $wpdb;

if (!is_user_logged_in()) {
    wp_redirect(home_url());
    exit;
}

if(current_user_can('administrator')) {
    $users = get_users( array( 'fields' => array( 'ID' ) ) );
    foreach($users as $user){
        $usermetaid = $user->ID;
        $userdata = array_map(
            function ($meta_data) {
                return $meta_data[0];
            },
            get_user_meta($usermetaid)
        );
        $xml = new SimpleXMLElement(
            "<?xml version=\"1.0\" encoding=\"UTF-8\" ?><PersonalData></PersonalData>"
        );

        $node = $xml->addChild('Userdata');

        // function call to convert array to xml
        array_to_xml($userdata, $node);
        header('Content-disposition: inline; filename=personaldata.xml');
        header("Content-Type:text/xml");

        //output the XML data
        print $xml->asXML();

        // if you want to directly download then set expires time
        header("Expires: 0");
    }
} else {
    $current_user = wp_get_current_user();
    $usermetaid = $current_user->ID;
    $userdata = array_map(
        function ($meta_data) {
            return $meta_data[0];
        },
        get_user_meta($usermetaid)
    );
    $xml = new SimpleXMLElement(
        "<?xml version=\"1.0\" encoding=\"UTF-8\" ?><PersonalData></PersonalData>"
    );

    $node = $xml->addChild('Userdata');

    // function call to convert array to xml
    array_to_xml($userdata, $node);
    header('Content-disposition: attachment; filename=personaldata.xml');
    header("Content-Type:text/xml");

    //output the XML data
    print $xml->asXML();

    // if you want to directly download then set expires time
    header("Expires: 0");
}

// function to convert an array to XML using SimpleXML
function array_to_xml($user_data, &$xml)
{
    foreach ($user_data as $meta_name => $meta_value) {
        if (empty($meta_value)) {
            continue;
        }

        if (is_array($meta_value)) {
            array_to_xml($meta_value, $xml);
        } else {
            $xml->addChild("{$meta_name}", htmlspecialchars("{$meta_value}"));
        }
    }
}