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
		$datas[] = $userdata;
		$json .= PHP_EOL.json_encode(array('data'=>$datas));
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
	$datas[] = $userdata;
	$json = json_encode(array('data'=>$datas));
}

header('Content-disposition: attachment; filename=personaldata.json');
header('Content-type: application/json');
echo $json;
?>
