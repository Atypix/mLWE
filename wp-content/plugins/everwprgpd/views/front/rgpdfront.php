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

global $wpdb;
$current_user = wp_get_current_user();
$userdata = get_user_meta( $current_user->ID );
?>

<div  id="everfrontrgpd">
	<?php echo get_option( 'everwprgpdeverrgpdmessage' ); ?>
	
	<h4><?php _e('Our Data Protection Officer infos :', 'everwprgpd'); ?></h4>

	<p><?php echo esc_html( get_option( 'everwprgpdeverofficername' ) ); ?>, <?php _e('feel free to contact this person at ', 'everwprgpd'); ?> <a href="mailto:<?php echo esc_html( get_option( 'everwprgpdeverofficeremail' ) ); ?>"><?php echo esc_html( get_option( 'everwprgpdeverofficeremail' ) ); ?></a></p>

	<p><?php _e('Please make sure you have read and accepted our legal mentions at', 'everwprgpd'); ?> <a href="<?php echo esc_html( get_option( 'everwprgpdlegalmentions' ) ); ?>"><?php _e('this URL', 'everwprgpd'); ?></a></p>
	<button type="button" class="btn button btn-info rgpdbtn" data-format="json" data-user="<?php echo $current_user->ID; ?>"><?php _e('Export your personnal data in JSON format', 'everwprgpd'); ?></button>
	<button type="button" class="btn button btn-info rgpdbtn" data-format="xml" data-user="<?php echo $current_user->ID; ?>"><?php _e('Export your personnal data in XML format', 'everwprgpd'); ?></button>
	<button type="button" class="btn button btn-info rgpdbtn" data-format="csv" data-user="<?php echo $current_user->ID; ?>"><?php _e('Export your personnal data in CSV format', 'everwprgpd'); ?></button>

	
</div>