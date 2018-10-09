<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'YWGC_Plugin_FW_Loader' ) ) {

    /**
     *
     * @class   YWGC_Plugin_FW_Loader
     *
     * @since   1.0.0
     * @author  Lorenzo Giuffrida
     */
    class YWGC_Plugin_FW_Loader {

        /**
         * @var $_panel Panel Object
         */
        protected $_panel;

        /**
         * @var $_premium string Premium tab template file name
         */
        protected $_premium = 'premium.php';

        /**
         * @var string the YITH plugin stats page
         */
        protected $_status_page = 'status.php';

        /**
         * @var string Premium version landing link
         */
        protected $_premium_landing = '//yithemes.com/themes/plugins/yith-woocommerce-gift-cards/';

        /**
         * @var string Plugin official documentation
         */
        protected $_official_documentation = 'https://docs.yithemes.com/yith-woocommerce-gift-cards/';

        /**
         * @var string Plugin panel page
         */
        protected $_panel_page = 'yith_woocommerce_gift_cards_panel';

        /**
         * @var string Official plugin landing page
         */
        protected $_premium_live = 'https://plugins.yithemes.com/yith-woocommerce-gift-cards/';

        /**
         * @var string Official plugin support page
         */
        protected $_support = 'https://yithemes.com/my-account/support/dashboard/';

        /**
         * Single instance of the class
         *
         * @since 1.0.0
         */
        protected static $instance;

        /**
         * Returns single instance of the class
         *
         * @since 1.0.0
         */
        public static function get_instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public function __construct() {

            $this->plugin_fw_loader();
            /**
             * Register actions and filters to be used for creating an entry on YIT Plugin menu
             */
            add_action( 'admin_init', array( $this, 'register_pointer' ) );

            //Add action links
            add_filter( 'plugin_action_links_' . plugin_basename( YITH_YWGC_DIR . '/' . basename( YITH_YWGC_FILE ) ), array(
                $this,
                'action_links',
            ) );

            add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 4 );

            //  Add stylesheets and scripts files
            add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

            //  Show plugin premium tab
            add_action( 'yith_gift_cards_tab_premium', array( $this, 'premium_tab' ) );
            /**
             * register plugin to licence/update system
             */
            $this->licence_activation();

        }

        /**
         * Load YIT core plugin
         *
         * @since  1.0
         * @access public
         * @return void
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function plugin_fw_loader() {
            if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
                global $plugin_fw_data;
                if ( ! empty( $plugin_fw_data ) ) {
                    $plugin_fw_file = array_shift( $plugin_fw_data );
                    require_once( $plugin_fw_file );
                }
            }
        }

        /**
         * Add a panel under YITH Plugins tab
         *
         * @return   void
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @use      /Yit_Plugin_Panel class
         * @see      plugin-fw/lib/yit-plugin-panel.php
         */
        public function register_panel() {

            if ( ! empty( $this->_panel ) ) {
                return;
            }

            $admin_tabs['general']  = __( 'General', 'yith-woocommerce-gift-cards' );
            $admin_tabs['template'] = __( 'Template', 'yith-woocommerce-gift-cards' );

            if ( ! defined( 'YITH_YWGC_PREMIUM' ) ) {
                $admin_tabs['premium-landing'] = __( 'Premium Version', 'yith-woocommerce-gift-cards' );
            }
            $args = array(
                'create_menu_page' => true,
                'parent_slug'      => '',
                'page_title'       => 'Gift Cards',
                'menu_title'       => 'Gift Cards',
                'capability'       => 'manage_options',
                'parent'           => '',
                'parent_page'      => 'yit_plugin_panel',
                'page'             => $this->_panel_page,
                'admin-tabs'       => $admin_tabs,
                'options-path'     => YITH_YWGC_DIR . 'plugin-options',
            );

            /* === Fixed: not updated theme  === */
            if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {

                require_once( YITH_YWGC_DIR . 'plugin-fw/lib/yit-plugin-panel-wc.php' );
            }

            $this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

            if ( defined( 'YITH_YWGC_PREMIUM' ) ) {
                add_action( 'woocommerce_admin_field_ywgc_upload_image', array( $this->_panel, 'yit_upload' ), 10, 1 );
            }
        }

        /**
         * Premium Tab Template
         *
         * Load the premium tab template on admin page
         *
         * @return   void
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @return void
         */
        public function premium_tab() {
            $premium_tab_template = YITH_YWGC_TEMPLATES_DIR . 'admin/' . $this->_premium;
            if ( file_exists( $premium_tab_template ) ) {
                include_once( $premium_tab_template );
            }
        }

        /**
         * Action Links
         *
         * add the action links to plugin admin page
         *
         * @param $links | links plugin array
         *
         * @return   mixed Array
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @return mixed
         * @use      plugin_action_links_{$plugin_file_name}
         */
        public function action_links( $links ) {
            $links[] = '<a href="' . admin_url( "admin.php?page={$this->_panel_page}" ) . '">' . __( 'Settings', 'yith-woocommerce-gift-cards' ) . '</a>';


            if ( defined( 'YITH_YWGC_FREE_INIT' ) ) {
                $links[] = '<a href="' . $this->get_premium_landing_uri() . '" target="_blank">' . __( 'Premium Version', 'yith-woocommerce-gift-cards' ) . '</a>';
            }

            if( ! defined( 'YITH_YWRAQ_FREE_INIT' ) && class_exists( 'YIT_Plugin_Licence' ) ){
                $links[] = sprintf( '<a href="%s">%s</a>', YIT_Plugin_Licence::get_license_activation_url(),__( 'License',  'yith-woocommerce-gift-cards' ) );
            }

            return $links;
        }

        /**
         * plugin_row_meta
         *
         * add the action links to plugin admin page
         *
         * @param $plugin_meta
         * @param $plugin_file
         * @param $plugin_data
         * @param $status
         *
         * @return   Array
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @use      plugin_row_meta
         */
        public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_YWGC_INIT' ) {

            /*if ( ( defined( 'YITH_YWGC_INIT' ) && ( YITH_YWGC_INIT == $plugin_file ) ) ||
                 ( defined( 'YITH_YWGC_FREE_INIT' ) && ( YITH_YWGC_FREE_INIT == $plugin_file ) )
            ) {
                $plugin_meta[] = '<a href="' . $this->_official_documentation . '" target="_blank">' . __( 'Plugin Documentation', 'yith-woocommerce-gift-cards' ) . '</a>';
            }

            return $plugin_meta;*/

            $to_add = array();
            if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
                $to_add[] = array(
                    'label' => _x( 'Live Demo', 'Plugin Row Meta', 'yith-woocommerce-gift-cards' ),
                    'url'   => $this->_premium_live,
                    'icon'  => 'dashicons  dashicons-laptop',
                );

                $to_add[] = array(
                    'label' => _x( 'Documentation', 'Plugin Row Meta', 'yith-woocommerce-gift-cards' ),
                    'url'   => $this->_official_documentation,
                    'icon'  => 'dashicons  dashicons-search',
                );

                $to_add[] = array(
                    'label' => __( 'Support', 'Plugin Row Meta', 'yith-woocommerce-gift-cards' ),
                    'url'   => $this->_support,
                    'icon'  => 'dashicons  dashicons-admin-users',
                );
            }

            if ( defined( 'YITH_YWRAQ_FREE_INIT' ) && YITH_YWRAQ_FREE_INIT == $plugin_file ) {
                $to_add[] = array(
                    'label' => __( 'Premium version', 'Plugin Row Meta', 'yith-woocommerce-gift-cards' ),
                    'url'   => $this->get_premium_landing_uri(),
                    'icon'  => 'dashicons  dashicons-cart',
                );
            }

            foreach ( $to_add as $row_meta ){
                $plugin_meta[] = sprintf( '<a href="%s" target="_blank"><span class="%s"></span>%s</a>', $row_meta['url'], $row_meta['icon'], $row_meta['label'] );
            }

            return $plugin_meta;

        }

        public function register_pointer() {

            $is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

            if ( $is_ajax ){
                return;
            }

            if ( ! class_exists( 'YIT_Pointers' ) ) {
                include_once( 'plugin-fw/lib/yit-pointers.php' );
            }

            $premium_message = defined( 'YITH_YWGC_PREMIUM' )
                ? ''
                : __( 'YITH WooCommerce Gift Cards is available in an outstanding PREMIUM version with many new options, discover it now.', 'yith-woocommerce-gift-cards' ) .
                ' <a href="' . $this->get_premium_landing_uri() . '">' . __( 'Premium version', 'yith-woocommerce-gift-cards' ) . '</a>';

            $args[] = array(
                'screen_id'  => 'plugins',
                'pointer_id' => 'yith_woocommerce_gift_cards',
                'target'     => '#toplevel_page_yit_plugin_panel',
                'content'    => sprintf( '<h3> %s </h3> <p> %s </p>',
                    __( 'YITH WooCommerce Gift Cards', 'yith-woocommerce-gift-cards' ),
                    __( 'In the YITH Plugins tab you can find YITH WooCommerce Gift Cards options.<br> From this menu you can access all the settings of your active YITH plugins.', 'yith-woocommerce-gift-cards' ) . '<br>' . $premium_message
                ),
                'position'   => array( 'edge' => 'left', 'align' => 'center' ),
                'init'       => defined( 'YITH_YWGC_PREMIUM' ) ? YITH_YWGC_INIT : YITH_YWGC_FREE_INIT,
            );

            YIT_Pointers()->register( $args );
        }

        /**
         * Get the premium landing uri
         *
         * @since   1.0.0
         * @author  Andrea Grillo <andrea.grillo@yithemes.com>
         * @return  string The premium landing link
         */
        public function get_premium_landing_uri() {
            return defined( 'YITH_REFER_ID' ) ? $this->_premium_landing . '?refer_id=' . YITH_REFER_ID : $this->_premium_landing . '?refer_id=1030585';
        }

        //region    ****    licence related methods ****

        /**
         * Add actions to manage licence activation and updates
         */
        public function licence_activation() {
            if ( ! defined( 'YITH_YWGC_PREMIUM' ) ) {
                return;
            }

            add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
            add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
        }

        /**
         * Register plugins for activation tab
         *
         * @return void
         * @since    2.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_activation() {

            if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
                require_once YITH_YWGC_DIR . '/plugin-fw/licence/lib/yit-licence.php';
                require_once YITH_YWGC_DIR . '/plugin-fw/licence/lib/yit-plugin-licence.php';
            }

            YIT_Plugin_Licence()->register( YITH_YWGC_INIT, YITH_YWGC_SECRET_KEY, YITH_YWGC_SLUG );
        }

        /**
         * Register plugins for update tab
         *
         * @return void
         * @since    2.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_updates() {
            if ( ! class_exists( 'YIT_Upgrade' ) ) {
                require_once YITH_YWGC_DIR . '/plugin-fw/lib/yit-upgrade.php';
            }
            YIT_Upgrade()->register( YITH_YWGC_SLUG, YITH_YWGC_INIT );
        }
        //endregion
    }
}
YWGC_Plugin_FW_Loader::get_instance();