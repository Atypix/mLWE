<?php

namespace Stachethemes\Stec;




/**
 * Abstract Main Plugin Template
 * @version 2.0 STEC
 */
abstract class Stachethemes_Plugin {



    protected $permission         = "edit_posts";
    protected $text_domain        = '';
    protected $front_js_locale    = array();
    protected $path               = array();
    protected $main_menu          = array();
    protected $sub_menu           = array();
    protected $admin_js           = array();
    protected $admin_css          = array();
    protected $force_load_scripts = false;
    protected $head_loaded        = false;



    public function __toString() {
        return '';
    }



    protected function __construct() {

        $base = realpath(__DIR__ . '/../') . '/';

        $this->set_paths(array(
                'ROOT'        => $base,
                'ADMIN'       => $base . 'admin/',
                'ADMIN_CSS'   => $base . 'admin/css/',
                'ADMIN_JS'    => $base . 'admin/js/',
                'ADMIN_CLASS' => $base . 'admin/class/',
                'ADMIN_LIBS'  => $base . 'admin/libs/',
                'ADMIN_VIEW'  => $base . 'admin/view/',
                'FRONT'       => $base . 'front/',
                'FRONT_CSS'   => $base . 'front/css/',
                'FRONT_JS'    => $base . 'front/js/',
                'FRONT_CLASS' => $base . 'front/class/',
                'FRONT_VIEW'  => $base . 'front/view/',
                'FONTS'       => $base . 'fonts/',
                'LANG'        => $base . 'languages/',
                'INSTALL'     => $base . 'install/',
                'API'         => $base . 'api/',
                'BASE_INC'    => $base . 'inc/',
                'SHORTCODES'  => $base . 'shortcodes/',
                'ADDONS'      => $base . 'addons/',
                'UPDATER'     => $base . 'updater/',
        ));

        add_action('wp_head', array($this, 'set_ajaxurl'));
        add_action('admin_head', array($this, 'set_ajaxurl'));
        add_action('style_loader_tag', array($this, 'css_less'));
    }



    /**
     * Set access level required to access the backend
     * @param String $permission 
     * @see https://codex.wordpress.org/Roles_and_Capabilities
     */
    protected function set_permission($permission = 'edit_posts') {
        $this->permission = $permission;
    }



    /**
     * Support for wp_enqueue_style .less files
     */
    public function css_less($tag) {

        if ( false !== strpos($tag, '.less?ver') ) {
            $tag = str_replace("text/css", "text/less", $tag);
        }

        return $tag;
    }



    /**
     * Define 'admin-ajax.php' ajaxurl path for js
     * Define 'wp-json' resturl path for js
     */
    public function set_ajaxurl() {
        ?>
        <script type='text/javascript'>
            if ( typeof ajaxurl === 'undefined' ) {
                var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
            }
            if ( typeof resturl === 'undefined' ) {
                var resturl = '<?php echo get_rest_url(); ?>';
            }
        </script>
        <?php
    }



    /**
     * Register admin ajax function
     * @param string $action
     * @param array $function ex. array($this, func)
     * @return this;
     */
    public function ajax_private_action($action, $function) {
        add_action("wp_ajax_$action", $function);
        return $this;
    }



    /**
     * Register public ajax function
     * @param string $action
     * @param array $function ex. array($this, func)
     * @return this;
     */
    public function ajax_public_action($action, $function) {
        add_action("wp_ajax_$action", $function);
        add_action("wp_ajax_nopriv_$action", $function);
        return $this;
    }



    /**
     * Add path to path array
     * @param string $paths
     */
    protected function set_paths($paths) {
        $this->path = $paths;
    }



    /**
     * Get paths from path array
     * @return string folder paths
     */
    public function get_paths() {
        return $this->path;
    }



    /**
     * Get path from path array
     * @param string $path
     * @return string folder path
     */
    public function get_path($path) {
        return $this->path[$path];
    }



    /**
     * Language files must be named like following: domain-locale.po
     * @param string $domain
     * @return bool
     */
    public function load_textdomain($domain) {

        $this->text_domain = $domain;

        return load_plugin_textdomain($this->text_domain, false, plugin_basename($this->get_path('LANG')));
    }



    /**
     * @return string plugin text domain
     */
    public function get_text_domain() {
        return $this->text_domain;
    }



    /**
     * @param bool|array $files path to ADMIN_LIBS\$file
     * @return this;
     */
    public function load_admin_libs($files) {

        if ( true === $files ) {
            return $this->auto_load_files($this->get_path("ADMIN_LIBS"));
        } else if ( is_array($files) ) {
            foreach ( $files as $file ) {
                require_once($this->get_path('ADMIN_LIBS') . $file);
            }
        }

        return $this;
    }



    /**
     * @param bool|array $files path to API\$file
     * @return this;
     */
    public function load_api_classes($files = array()) {

        if ( true === $files ) {
            return $this->auto_load_files($this->get_path("API"));
        } else if ( is_array($files) ) {
            foreach ( $files as $file ) {
                require_once($this->get_path('API') . $file);
            }
        }

        return $this;
    }



    /**
     * @param bool|array $files path to ADMIN_CLASS\$file
     * @return this;
     */
    public function load_admin_classes($files = array()) {

        if ( true === $files ) {
            return $this->auto_load_files($this->get_path("ADMIN_CLASS"));
        } else if ( is_array($files) ) {
            foreach ( $files as $file ) {
                require_once($this->get_path('ADMIN_CLASS') . $file);
            }
        }

        return $this;
    }



    /**
     * @param bool|array $files path to FRONT_CLASS\$file
     * @return this;
     */
    public function load_front_classes($files = array()) {

        if ( true === $files ) {
            return $this->auto_load_files($this->get_path("FRONT_CLASS"));
        } else if ( is_array($files) ) {
            foreach ( $files as $file ) {
                require_once($this->get_path('FRONT_CLASS') . $file);
            }
        }

        return $this;
    }



    /**
     * Adds the plugin main menu info to _main_menu array
     * @param string $name
     * @param string $slug
     * @param string $icon
     * @param int $priority
     * @return this;
     */
    public function add_menu($name, $slug, $icon, $priority) {

        $this->main_menu = array(
                'name'     => $name,
                'slug'     => $slug,
                'icon'     => $icon,
                'priority' => $priority
        );

        return $this;
    }



    /**
     * Adds submenu to _sub_menu array
     * @param string $name
     * @param string $slug
     * @return this;
     */
    public function add_submenu($name, $slug, $literal = false) {

        $submenu = array(
                'name'    => $name,
                'slug'    => $slug,
                'literal' => $literal
        );

        array_push($this->sub_menu, $submenu);

        return $this;
    }



    /**
     * Register admin menu (hook to admin_menu)
     */
    public function register_menu() {
        add_action('admin_menu', array($this, 'create_menus'));
    }



    /**
     * Requires admin page
     * slug__foldername = path to ADMIN_VIEW/foldername/index.php
     */
    public function load_admin_page() {
        $page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);

        if ( $page ) {
            $page = explode('__', $page);
            require($this->get_path('ADMIN_VIEW') . str_replace(array('.', '..', '/', '\\'), '', $page[1]) . '/index.php');
        }
    }



    /**
     * Create admin menu and enqueue styles and scripts from load_admin_css and load_admin_js arrays
     */
    public function create_menus() {

        $menu = add_menu_page(
                $this->main_menu['name'], $this->main_menu['name'], $this->permission, $this->main_menu['slug'], array($this, 'load_admin_page'), $this->main_menu['icon'], $this->main_menu['priority']);

        add_action('admin_print_styles-' . $menu, array($this, 'load_admin_css'));
        add_action('admin_print_scripts-' . $menu, array($this, 'load_admin_js'));

        if ( is_array($this->sub_menu) ) {
            foreach ( $this->sub_menu as $submenu ) {

                if ( false !== $submenu['literal'] ) {

                    $submenu = add_submenu_page(
                            $this->main_menu['slug'], $submenu['name'], $submenu['name'], $this->permission, $submenu['literal'], false);
                } else {

                    $submenu = add_submenu_page(
                            $this->main_menu['slug'], $submenu['name'], $submenu['name'], $this->permission, $submenu['slug'], array($this, 'load_admin_page'));
                }

                add_action('admin_print_styles-' . $submenu, array($this, 'load_admin_css'));
                add_action('admin_print_scripts-' . $submenu, array($this, 'load_admin_js'));
            }
        }
    }



    /**
     * Enqueue admin js from _admin_css array
     */
    public function load_admin_js() {

        // media
        wp_enqueue_media();
        wp_enqueue_script('wp-color-picker');

        foreach ( $this->admin_js as $js ) {
            $require = array();

            // enqueue required scripts if any
            if ( $js['require'] != '' ) {
                $require = explode(',', $js['require']);
                foreach ( $require as $req ) {
                    wp_enqueue_script($req);
                }
            }

            wp_enqueue_script($js['slug'], $js['url'], $require);
        }

        $this->load_js_locales();
    }



    /**
     * Enqueue admin css from _admin_css array
     */
    public function load_admin_css() {

        wp_enqueue_style('wp-color-picker');

        foreach ( $this->admin_css as $css ) {
            wp_enqueue_style($css['slug'], $css['url']);
        }
    }



    /**
     * Add js script to the _admin_js array
     * @param string $slug slug
     * @param string $url style location. Relative path to admin fonts folder. Add // for absolute path (http://...)
     * @param string $require list with required scripts 'jquery','jquery-ui' etc... comma separated
     * @return this;
     */
    public function add_menu_js($slug, $url, $require = '') {

        if ( false === strpos($url, '//') ) {
            $url = plugins_url($url, $this->get_path('ADMIN_JS') . '/.');
        }

        $js = array(
                'slug'    => $slug,
                'url'     => $url,
                'require' => $require
        );

        array_push($this->admin_js, $js);

        return $this;
    }



    /**
     * Add font style to the _admin_css array
     * @param string $slug slug
     * @param string $url style location. Relative path to admin fonts folder. Add // for absolute path (http://...)
     * @return this;
     */
    public function add_menu_font($slug, $url) {

        if ( false === strpos($url, '//') ) {
            $url = plugins_url($url, $this->get_path('FONTS') . '/.');
        }

        $css = array(
                'slug' => $slug,
                'url'  => $url
        );

        array_push($this->admin_css, $css);

        return $this;
    }



    /**
     * Add css style to the _admin_css array
     * @param string $slug slug
     * @param string $url style location. Relative path to admin css folder. Add // for absolute path (http://...)
     * @return this;
     */
    public function add_menu_css($slug, $url) {

        if ( false === strpos($url, '//') ) {
            $url = plugins_url($url, $this->get_path('ADMIN_CSS') . '/.');
        }

        $css = array(
                'slug' => $slug,
                'url'  => $url
        );

        array_push($this->admin_css, $css);

        return $this;
    }



    /**
     * Load js locales
     */
    public function load_js_locales() {

        foreach ( $this->front_js_locale as $locale ) :
            wp_localize_script($locale['handle'], $locale['name'], $locale['data']);
        endforeach;
    }



    /**
     * Add js locale to _front_js_locale array
     */
    public function localize($handle, $name, $data) {
        $locale = array(
                'handle' => $handle,
                'name'   => $name,
                'data'   => $data
        );
        array_push($this->front_js_locale, $locale);
    }



    /**
     * Force load shortcode scripts and css true, false
     * @param bool $val
     */
    public function force_load_scripts($val) {

        $this->force_load_scripts = (bool) $val;
    }



    public function scripts_are_forced() {

        return $this->force_load_scripts;
    }



    /**
     *  Add Custom head attributes 
     */
    public function load_head() {
        
    }



    public function add_front_css($handle, $url) {
        if ( false === strpos($url, '//') ) {
            $url = plugins_url($url, $this->get_path('FRONT_CSS') . '/.');
        }
        wp_enqueue_style($handle, $url);
    }



    public function add_font($handle, $url) {
        if ( false === strpos($url, '//') ) {
            $url = plugins_url($url, $this->get_path('FONTS') . '/.');
        }
        wp_enqueue_style($handle, $url);
    }



    public function add_front_js($handle, $url, $dep = false) {

        if ( false === strpos($url, '//') ) {
            $url = plugins_url($url, $this->get_path('FRONT_JS') . '/.');
        }

        if ( $dep ) {
            $dep = explode(',', $dep);
        }

        wp_enqueue_script($handle, $url, $dep);
    }



    /**
     * Adds meta rows ot the plugin section
     * @param array $rows meta array
     * @param type $plugin_file main plugin file including dirname
     */
    public function add_plugin_row_meta($rows, $plugin_file) {

        // Add Meta to the Plugins section
        add_filter('plugin_row_meta', function($links, $file) use($rows, $plugin_file) {

            if ( $file == $plugin_file ) {
                return array_merge($links, $rows);
            }
            return (array) $links;
        }, 10, 2);
    }



    /**
     * Auto load php files in folder
     * Does not look in sub folders
     * @param type $path the folder path
     * @return class this 
     */
    protected function auto_load_files($path) {

        if ( !is_dir($path) ) {
            return $this;
        }

        $files = scandir($path);

        foreach ( $files as $name ) {
            $filepath = $path . $name;
            if ( is_file($filepath) ) {
                $ext = pathinfo($filepath, PATHINFO_EXTENSION);
                if ( "php" == $ext ) {

                    if ( substr($name, 0, 4) === "inc." ) {
                        continue;
                    }

                    require_once($filepath);
                }
            }
        }

        return $this;
    }



    /**
     * Register wp post
     * @param string post slug name
     * @param string singular post name
     * @param string plural post name
     * @param array supported fields
     * @param string post description
     * @return class this class
     */
    public function register_custom_post($post, $singular, $plural, $slug, $supports = array('title', 'editor'), $description = false) {

        register_post_type($post, array(
                'labels'             => array(),
                'description'        => $description ? $description : $plural,
                'publicly_queryable' => true,
                'show_ui'            => false,
                'show_in_menu'       => true,
                'query_var'          => true,
                'rewrite'            => array('slug' => $slug),
                'capability_type'    => 'post',
                'public'             => false,
                'has_archive'        => false,
                'hierarchical'       => false,
                'menu_position'      => null,
                'menu_icon'          => '',
                'supports'           => $supports,
        ));

        return $this;
    }



    /**
     * Request file system credentials
     * @return bool false on failure, true on success.
     */
    protected function get_file_system_credentials() {

        ob_start();
        $credentials = request_filesystem_credentials('/');
        ob_end_clean();

        return WP_Filesystem($credentials);
    }



    /**
     * Get file system credentials and return the $wp_filesystem;
     * @global Object $wp_filesystem
     * @return Object the $wp_filesystem object
     */
    protected function get_file_system() {

        global $wp_filesystem;

        if ( !$wp_filesystem ) {
            $this->get_file_system_credentials();
        }

        return $wp_filesystem;
    }



    abstract public function lcns();
}
