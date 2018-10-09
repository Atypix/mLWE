<?php
/*
Plugin Name: WP Google Analytics Events
Plugin URI: http://wpflow.com
Description: Adds the Google Analytics code to your website and enables you to send events on scroll or click.
Version: 2.5.1
Author: Yuval Oren
Author URI: http://wpflow.com
License: GPLv2
*/

/* Copyright 2013 Yuval Oren (email : yuval@pinewise.com)
This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/
error_reporting(E_ERROR | E_PARSE);

include( dirname( __FILE__ ) . '/includes/notice.php' );
if ( function_exists( 'ga_events_set_review_trigger_date' ) ) {
    register_activation_hook( __FILE__,  'ga_events_set_review_trigger_date' );
}
/**
 * Set Trigger Date.
 *
 * @since  1.0.0
 */
function ga_events_set_review_trigger_date() {
    $triggerreview = mktime(0, 0, 0, date('m')  , date('d') + 30 , date('Y'));
    if ( ! get_option( 'ga_events_activation_date')) {
        add_option( 'ga_events_activation_date', $triggerreview, '', 'yes' );
    }
}

$plugin_path = plugin_dir_path(__FILE__);

require_once('includes/functions.php');
require_once(plugin_dir_path(__FILE__) ."/includes/admin.php");




/*
 * Plugin Activation
 */




register_activation_hook( __FILE__, 'ga_events_install' );
register_deactivation_hook( __FILE__, 'ga_events_deactivate' );




function ga_events_install() {
    $ga_events_options = array(
        'id' => '',
        'exclude_snippet' => '0',
        'universal' => '1',
        'anonymizeip' => '0',
        'advanced' => '0',
        'divs' => array(array(id => '',type =>'', action => '', cat => '', label => '', interaction => 0, value => '')),
        'click' => array(array(id => '',type =>'', action => '', cat => '', label => '', interaction => 0, value => ''))
    );
        if (!get_option('ga_events_options')) {
        update_option( 'ga_events_options', $ga_events_options );
        }

    wpgae_reactivate_notice();    

}

function ga_events_deactivate() {

}
/*
 * Init
 */

add_action('init','ga_events_scripts');

function ga_events_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('scrolldepth',plugins_url( '/js/ga-scroll-events.js', __FILE__) , array('jquery'), '2.5.1', false);
}

add_action( 'plugins_loaded', 'ga_events_setup');

function ga_events_setup() {
  add_action( 'wp_head', 'ga_events_header', 100 );
}


function ga_events_header() {
    $options = get_option('ga_events_options');
    $id = $options['id'];
    $domain = $_SERVER['SERVER_NAME'];
    if (!isset($options['exclude_snippet']) || $options['exclude_snippet'] != '1' ) {
    if (isset($options['universal']) && $options['universal']) { ?>
        <script>
                if (typeof ga === 'undefined') {
                  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

                  <?php echo (isset($options['anonymizeip']) && $options['anonymizeip']) ?  "ga('create','$id', '$domain', {anonymizeIp: true});" : "ga('create','$id', '$domain');";?>
                  ga('send', 'pageview');
                                }
            </script> <?php
    } else {
        ?> <script type='text/javascript'>
         if (typeof _gaq === 'undefined') {
            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', '$id']);
            <?php if (isset($options['anonymizeip']) && $options['anonymizeip']) echo "_gaq.push (['_gat._anonymizeIp'])";  ?>
            _gaq.push(['_setDomainName', '$domain']);
            _gaq.push(['_setAllowLinker', true]);
            _gaq.push(['_trackPageview']);

            (function() {
                var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
            })();
         }

</script> <?php
    }
    }
}

add_action( 'wp_footer', 'ga_events_footer', 100 );

function ga_events_footer() {
    $options = get_option('ga_events_options');
    $divs = $options['divs'];
    $click = $options['click'];
    $universal = $options['universal'];
    $gtm 	= $options['gtm'];
    $gst 	= $options['gst'];

  if ($universal == ""){
        $universal = 0;
    }

  if ($gtm == ""){
    $gtm = 0;
  }

  if ($gst == ""){
    $gst = 0;
  }
    echo "
    <!-- BEGIN: wpflow ga events array -->
    <script>

                jQuery(document).ready(function() {
                    scroll_events.bind_events( {
                        universal: ".$universal.",
                        gtm:".$gtm.",
                        gst:".$gst.",

                        scroll_elements: [";
                                        $i = 0;
                                        if (is_array($divs)){
                                            foreach ($divs as $div) {
                                                if ($i == 0) {
                                                    echo ga_events_get_selector($div);
                                                }else{
                                                    echo ",".ga_events_get_selector($div);
                                                }
                                                $i++;
                                            }
                                        }

                        echo "],
                        click_elements: [";
                                        $i = 0;
                                         if (is_array($click)){
                                            foreach ($click as $cl) {
                                                if ($i == 0) {
                                                    echo ga_events_get_selector($cl);
                                                }else{
                                                    echo ",".ga_events_get_selector($cl);
                                                }
                                                $i++;
                                            }
                                        }
                        echo "],
                    });
                });

    </script>
    <!-- END: wpflow ga events array -->";


}

add_filter( 'plugin_action_links', 'ga_events_action_link', 10, 5 );
function ga_events_action_link( $actions, $plugin_file ) {
	static $plugin;
	if (!isset($plugin))
		$plugin = plugin_basename(__FILE__);
	if ($plugin == $plugin_file) {
			$upgrade = array('upgrade' => '<a href="https://wpflow.com/upgrade/">' . __('Upgrade', 'General') . '</a>');		
			$settings = array('settings' => '<a href="admin.php?page=wp-google-analytics-events">' . __('Settings', 'General') . '</a>');		
			
			$actions = array_merge($upgrade, $actions);			
			$actions = array_merge($settings, $actions);			
		}
		
		return $actions;
}

function ga_events_get_selector($element) {
	$options = get_option('ga_events_options');
    if ($element[0]){
        $selector = '{';
		if($element[1] =='advanced'){
			 $selector .= "'select':'".$element[0]."',";
		}else{
			$selector = "{'select':'";
			$selector .= ($element[1] =='class') ? '.':'#';
			$selector .= str_replace(' ','',$element[0])."',";
		}       
        $selector .= "'category':'".$element[2]."',";
        $selector .= "'action':'".$element[3]."',";
        $selector .= "'label':'".$element[4]."',";
        $selector .= "'bounce':'".$element[5]."',";
        $selector .= "'evalue':'".$element[6]."'";
        $selector .= '}';
        return $selector;
    }else{
        return '';
    }

}

?>
