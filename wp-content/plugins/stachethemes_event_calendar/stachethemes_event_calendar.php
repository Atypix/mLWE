<?php
/*
  Plugin Name: Stachethemes Event Calendar
  Version: 2.1.8
  Description: Stachethemes Event Calendar
  Author: Stachethemes
  Author URI: http://www.stachethemes.com/
  License: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
  Text Domain: stec
  Domain Path: /languages/
 */

namespace Stachethemes\Stec;




// Do not load directly
if ( !defined('ABSPATH') ) {
    die('-1');
}

// Define constants
const STACHETHEMES_EC_FILE__ = __FILE__;
const STEC_EXEC_TIME_LIMIT   = 45;

require_once(dirname(__FILE__) . "/stachethemes/load.php");




class stachethemes_ec_main extends Stachethemes_Plugin {



    private static $instance;



    private function __clone() {
        
    }



    private function __wakeup() {
        
    }



    public static function get_instance() {

        if ( null === static::$instance ) {
            static::$instance = new static();
        }

        return static::$instance;
    }



    protected function __construct() {

        // Comment the line below if you experience problems with sessions
        $this->session_hooks();

        parent::__construct();

        $this->load_admin_classes(array(
                "class.settings.php",
        ));

        $this->auto_load_files($this->get_path('INSTALL'));

        add_action('plugins_loaded', array($this, 'plugins_loaded'));

        add_action('init', array($this, 'init'));

        add_action('wp_loaded', array($this, 'wp_loaded'));
    }



    public function plugins_loaded() {

        parent::load_textdomain('stec');

        include($this->get_path('LANG') . 'inc.js-locale.php');

        // Add meta links in dashboard/plugins/
        add_filter('plugin_row_meta', function($links, $file) {

            if ( $file == "stachethemes_event_calendar/stachethemes_event_calendar.php" ) {
                $row_meta = array(
                        'cc'      => '<a href="' . esc_url('http://codecanyon.net/item/stachethemes-event-calendar/16168229?ref=Stachethemes') . '">' . __('Codecanyon', 'stec') . '</a>',
                        'support' => '<a href="' . esc_url('http://codecanyon.net/item/stachethemes-event-calendar/16168229/support/contact?ref=Stachethemes') . '">' . __('Support', 'stec') . '</a>',
                        'license' => '<a href="' . get_admin_url(0, 'admin.php?page=stec_menu__license') . '">' . __('Activator', 'stec') . '</a>'
                );
                return array_merge($links, $row_meta);
            }
            return (array) $links;
        }, 10, 2);
        
//        $this->localize('stec-js', 'stecApi', array(
//                'nonce' => wp_create_nonce('wp_rest')
//        ));

        /**
         *  Visual Composer Integration
         */
        if ( function_exists("vc_map") ) {

            include_once(dirname(__FILE__) . '/vc-settings.php');
        }
    }



    public function wp_loaded() {

        // Add admin menus
        if ( is_admin() ) {

            $this->set_permission(apply_filters('stec_permission', $this->permission));

            require_once($this->get_path('BASE_INC') . 'inc.admin-menu.php');

            // Updater menu
            if ( current_user_can($this->permission) ) {
                require_once($this->get_path('UPDATER') . '/index.php');
            }

            if ( false === get_option('stec-calendar-migrated', false) ) {
                require_once($this->get_path('ROOT') . '/migrate/index.php');
            }
        }
    }



    protected function session_hooks() {
        add_action('init', function() {
            if ( !session_id() ) {
                session_start();
            }
        }, 1);

        add_action('wp_logout', function() {
            session_destroy();
        });

        add_action('wp_login', function() {
            session_destroy();
        });
    }



    public function init() {


        /**
         * Load classes 
         */
        $this->load_admin_libs(array('ics/vendor/autoload.php'));
        $this->load_admin_libs(array('rrule/vendor/autoload.php'));

        $this->load_admin_classes(array(
                "exception.php",
                "class.helper.php",
                "class.cron.php",
                "class.html.php",
                "class.settings.php",
                "abstract.post.php",
                "o.remind.php",
                "i.event-meta-object.php",
                "o.event-meta-attachment.php",
                "o.event-meta-attendee.php",
                "o.event-meta-guest.php",
                "o.event-meta-product.php",
                "o.event-meta-schedule.php",
                "o.cron.php",
                "class.calendars.php",
                "class.events.php",
                "o.calendar.php",
                "o.event.php",
                "class.export.php",
                "class.export-ical.php",
                "class.import.php",
                "class.import-ical.php",
                "class.wc.php",
        ));

        $this->load_front_classes(array(
                "class.calendar-instance.php",
                "class.submit-event.php",
                
        ));

        // register plugin posts
        require_once($this->get_path('BASE_INC') . 'inc.custom-posts.php');

        // register plugin template single pages
        require_once($this->get_path('BASE_INC') . 'inc.posts-templates.php');

        //  auto load shortcode files
        $this->auto_load_files($this->get_path("SHORTCODES"));

        // rewrite rules for event posts
        $this->add_rewrite_rules();

        // handles $_GET and $_POST "saem_task" requests before headers sent
        require_once($this->get_path('BASE_INC') . 'inc.task-handler.php');

        // ajax admin task listener
        $this->ajax_private_action('stec_ajax_action', array($this, 'admin_ajax_task_handler'));

        // ajax public task listener
        $this->ajax_public_action("stec_public_ajax_action", array($this, "admin_ajax_public_task_handler"));

        // events tabs
        require_once $this->get_path('BASE_INC') . '/inc.tabs.php';

        /**
         * Display fixed message if any in session
         */
        if ( isset($_SESSION['stec-fixed-message']) ) {

            // force load scripts
            $this->force_load_scripts(true);
            $this->display_fixed_message();
        }


        /**
         * Check force load scripts
         */
        if ( Settings::get_admin_setting_value('stec_menu__general_other', 'force_load_scripts') == '1' ) {
            $this->force_load_scripts(true);
        }

        /**
         * Display dashboard admin notices if any
         */
        require_once($this->get_path('BASE_INC') . 'inc.admin-notices.php');

        /**
         * Cron
         */
        require_once($this->get_path('BASE_INC') . 'inc.cron.php');


        //  auto load api files
        $this->auto_load_files($this->get_path("API"));

        // init rest api
        new Api();
    }



    private function add_rewrite_rules() {
        
        $stec = $this;
        
        add_action('stec_add_rewrite_rules', function() use ($stec) {
            add_rewrite_tag('%stec_repeat_offset%', '([^&]+)');
            add_rewrite_rule('^' . $stec->get_permalinks('event') . '/([^&]+)/([^&]+)?', 'index.php?stec_event=$matches[1]&stec_repeat_offset=$matches[2]', 'top');
            add_rewrite_rule('^' . $stec->get_permalinks('event') . '/([^&]+)/?', 'index.php?stec_event=$matches[1]', 'top');
        });

        do_action('stec_add_rewrite_rules');
    }



    public function get_permalinks($page = null) {

        $permalinks = get_option('stec_permalinks', array(
                'calendar' => 'stec_calendar',
                'event'    => 'stec_event',
                'cron'     => 'stec_cron',
        ));

        if ( $page ) {
            return isset($permalinks[$page]) ? $permalinks[$page] : null;
        }

        return $permalinks;
    }



    /**
     * Handles admin ajax tasks
     */
    public function admin_ajax_task_handler() {

        require($this->get_path('BASE_INC') . 'inc.admin-ajax-task-handler.php');
    }



    /**
     * Handles admin ajax public tasks
     */
    public function admin_ajax_public_task_handler() {

        require($this->get_path('BASE_INC') . 'inc.admin-ajax-public-task-handler.php');
    }



    private function theme_list() {
        return array();
    }



    public function lcns() {
        $theme = wp_get_theme();
        $nfo   = get_option('stec_activated', false);

        if ( isset($nfo['purchase_code']) ) {
            return $nfo;
        } else if ( in_array($theme->name, $this->theme_list()) ) {
            return true;
        } else {
            return false;
        }
    }



    /**
     * Custom head attributes
     */
    public function load_head() {

        if ( $this->head_loaded === true ) {
            return;
        }

        /**
         * Meta viewport tag for mobile devices
         * @todo make optional
         */
        ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <?php
        $this->generate_css();
        $this->head_loaded = true;
    }



    private function set_fixed_message($msg, $type = '') {

        $_SESSION['stec-fixed-message'] = array(
                'msg'  => $msg,
                'type' => $type
        );
    }



    private function display_fixed_message() {

        $session_msg = $_SESSION['stec-fixed-message'];

        $msg  = $session_msg['msg'];
        $type = $session_msg['type'];

        switch ( $type ) :

            case 'success' :

                add_action('wp_footer', function() use($msg) {

                    $stachethemes_ec_main = stachethemes_ec_main::get_instance();

                    ob_start();
                    include $stachethemes_ec_main->get_path('FRONT') . '/view/msg/success.php';
                    $html = ob_get_clean();
                    $html = preg_filter('/stec_replace_msg/', $msg, $html);
                    echo $html;
                });

                break;

            case 'error' :

                add_action('wp_footer', function() use($msg) {
                    ob_start();

                    $stachethemes_ec_main = stachethemes_ec_main::get_instance();

                    include $stachethemes_ec_main->get_path('FRONT') . '/view/msg/error.php';
                    $html = ob_get_clean();
                    $html = preg_filter('/stec_replace_msg/', $msg, $html);
                    echo $html;
                });

                break;

            default :

                add_action('wp_footer', function() use($msg) {

                    $stachethemes_ec_main = stachethemes_ec_main::get_instance();

                    ob_start();
                    include $stachethemes_ec_main->get_path('FRONT') . '/view/msg/msg.php';
                    $html = ob_get_clean();
                    $html = preg_filter('/stec_replace_msg/', $msg, $html);
                    echo $html;
                });

                break;

        endswitch;

        unset($_SESSION['stec-fixed-message']);
    }



    private function generate_css() {

        include_once $this->get_path('BASE_INC') . '/inc.generate-css.php';
    }

}

$stachethemes_ec_main = stachethemes_ec_main::get_instance();
