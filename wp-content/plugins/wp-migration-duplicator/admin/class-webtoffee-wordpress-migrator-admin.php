<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.webtoffee.com
 * @since      1.0.0
 *
 * @package    Webtoffee_Wordpress_Migrator
 * @subpackage Webtoffee_Wordpress_Migrator/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Webtoffee_Wordpress_Migrator
 * @subpackage Webtoffee_Wordpress_Migrator/admin
 * @author     WebToffee <info@webtoffee.com>
 */
class Webtoffee_Wordpress_Migrator_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Webtoffee_Wordpress_Migrator_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Webtoffee_Wordpress_Migrator_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/webtoffee-wordpress-migrator-admin.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Webtoffee_Wordpress_Migrator_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Webtoffee_Wordpress_Migrator_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/webtoffee-wordpress-migrator-admin.js', array('jquery'), $this->version, false);

        wp_localize_script($this->plugin_name, 'webtoffee_migrator_ajax_export', array('webtf_ajax_export' => admin_url('admin-ajax.php')));
        wp_localize_script($this->plugin_name, 'webtoffee_migrator_ajax_import', array('webtf_ajax_import' => admin_url('admin-ajax.php')));
        wp_localize_script($this->plugin_name, 'webtoffee_migrator_ajax_delete', array('webtf_ajax_delete' => admin_url('admin-ajax.php')));
    }

    public function process_delete_data()
    {
        $filename = sanitize_text_field($_POST['filename']);
        $path = WP_CONTENT_DIR . "/webtoffee_migrations/$filename";
        unlink($path);
    }

    public function process_import_data()
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '-1');
        $step = sanitize_text_field($_POST['step']);
        $extract_to = sanitize_text_field($_POST['extract_to']);
        switch ($step) {
            case 1 :
                $attachment_url = sanitize_text_field($_POST['attachment_url']);

                $parse_url = parse_url($attachment_url);
                $real_url = $_SERVER['DOCUMENT_ROOT'] . ($parse_url['path']);

                $extract_to = WP_CONTENT_DIR;
                $zip = new ZipArchive;
                $zip->open($real_url);
                $zip->extractTo($extract_to);
                $imported = $zip->close();
                echo json_encode(array("step" => $step + 1, 'msg' => "<span style='color: darkgray'>Files and Folders Imported <br><br> Importing Database... </span>", 'val' => 50, 'extract_to' => $extract_to));
                break;
            case 2 :
                $db_imported = $this->import_database($extract_to);

                echo json_encode(array("step" => $step + 1, 'msg' => "<span style='color: green'>Wordpress Imported successfully </span>", 'val' => 100));
                break;
        }
        wp_die();
    }




    public function import_database($extract_to)
    {
        global $wpdb;
        set_time_limit(0);
        $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $mysql_version = substr(mysqli_get_server_info($connection), 0, 3); // Get Mysql Version
        if (mysqli_connect_errno())
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        // Temporary variable, used to store current query
        $templine = '';
        // Read in entire file
        $upload = wp_upload_dir();
        $upload_dir = $upload['basedir'];
        $filename = $upload_dir."/" . 'database.sql';
        $fp = fopen($filename, 'r');
        // Loop through each line
        while (($line = fgets($fp)) !== false) {
            // Skip it if it's a comment
            if (substr($line, 0, 2) == '--' || $line == '')
                continue;
            // Add this line to the current segment
            $templine .= $line;

            $templine = str_replace('webtoffee_', $wpdb->prefix, $templine);

            if ($mysql_version >= 5.5) {
                $query = str_replace('utf8mb4_unicode_520_ci', 'utf8mb4_unicode_ci', $query);
            }

            // If it has a semicolon at the end, it's the end of the query
            if (substr(trim($line), -8, 8) == ';/*END*/') {
                // Perform the query
                if (!mysqli_query($connection, $templine)) {
                    print('Error performing query \'<strong>' . $templine . '\': ' . mysqli_error($connection) . '<br /><br />');
                }
                // Reset temp variable to empty
                $templine = '';
            }
        }
        mysqli_close($connection);
        fclose($fp);
    }


    public function process_export_data()
    {
        global $wpdb;
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '-1');
        $step = sanitize_text_field($_POST['step']);
        $date = sanitize_text_field($_POST['date']);

        $upload = wp_upload_dir();
        $upload_dir = $upload['basedir'];

        // Get real path for our folder
        $rootPaths = [
            WP_PLUGIN_DIR,
            get_theme_root(),
            $upload_dir
        ];

        // Initialize archive object

        $filename = WP_CONTENT_DIR . '/webtoffee_migrations';

        if (!file_exists($filename)) {
            if (!mkdir(WP_CONTENT_DIR . '/webtoffee_migrations', 0755)) {
                return false;
                die;
            }
        }

        $download_path = WP_CONTENT_DIR .'/webtoffee_migrations/.';


        switch ($step) {
            case 1 :
                $export_data = $_POST['export_data'];
                foreach ($export_data as $data => $value) {
                    if ($value['name'] == 'find[]') {
                        $search[] = sanitize_text_field($value['value']);
                    } elseif ($value['name'] == 'replace[]') {
                        $replace[] = sanitize_text_field($value['value']);
                    }
                }
                $search[] = $wpdb->prefix . 'capabilities';
                $replace[] = 'webtoffee_capabilities';

                $search[] = $wpdb->prefix . 'user_level';
                $replace[] = 'webtoffee_user_level';

                $search[] = $wpdb->prefix . 'user-settings';
                $replace[] = 'webtoffee_user-settings';

                $search[] = $wpdb->prefix . 'user-settings-time';
                $replace[] = 'webtoffee_user-settings-time';

                $search[] = $wpdb->prefix . 'dashboard_quick_press_last_post_id';
                $replace[] = 'webtoffee_dashboard_quick_press_last_post_id';

                $search[] = $wpdb->prefix . 'user_roles';
                $replace[] = 'webtoffee_user_roles';

                $exportdb = $this->export_database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, false, false, $search, $replace);


                if (!$exportdb) return;
                $date = date('Y-m-d-h-i-sa');
                echo json_encode(array("step" => $step + 1, 'msg' => "Database", 'date' => $date, 'val' => 15,));
                break;
            case  ($step == 2 || $step == 3 || $step == 4):


                $zip = new ZipArchive();
                if ($step == 2) {
                    $zip->open("$download_path/$date.zip", ZipArchive::CREATE | ZipArchive::OVERWRITE);
                } else {
                    $zip->open("$download_path/$date.zip");
                }
                if ($step == 2) {
                    $rootPath = $rootPaths[0];
                    $msg = "Plugins";
                    $val = 40;
                }
                if ($step == 3) {
                    $rootPath = $rootPaths[1];
                    $msg = "Themes";
                    $val = 65;
                }
                if ($step == 4) {
                    $rootPath = $rootPaths[2];
                    $msg = "Uploads";
                    $val = 100;
                }

                // Create recursive directory iterator
                $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath),
                    RecursiveIteratorIterator::LEAVES_ONLY);

                foreach ($files as $name => $file) {
                    // Skip directories (they would be added automatically)
                    if (!$file->isDir()) {
                        // Get real and relative path for current file
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen($rootPath) + 1);

                        // Add current file to archive
                        $zip->addFile($filePath, basename($rootPath) . '/' . $relativePath);
                    }
                }

                // Zip archive will be created only after closing object
                $zip->close();


                $json = array("step" => $step + 1, 'msg' => $msg, 'val' => $val, 'date' => $date);
                if ($step == 4) {
                    $json['url'] = content_url() . '/webtoffee_migrations/' . "$date.zip";
                }
                echo json_encode($json);
                break;
        }
        wp_die();
    }


    public function export_database($host, $user, $pass, $name, $tables = false, $backup_name = false, $search, $replace)
    {
        $k = 0;
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '-1');
        global $wpdb;
        $upload = wp_upload_dir();
        $upload_dir = $upload['basedir'];
        $download_path = $upload_dir .'/.';
        $mysqli = new mysqli($host, $user, $pass, $name);
        $mysqli->select_db($name);
        $mysqli->query("SET NAMES 'utf8'");

        //$queryTables = $mysqli->query('SELECT TABLE_NAME FROM information_schema.columns WHERE table_schema = '. $name.' ORDER BY table_name DESC');
        $queryTables = $mysqli->query('SHOW TABLES');
        while ($row = $queryTables->fetch_row()) {
            $target_tables[] = $row[0];
        }
        if ($tables !== false) {
            $target_tables = array_intersect($target_tables, $tables);
        }

        foreach ($target_tables as $table) {


            $result = $mysqli->query('SELECT * FROM ' . $table);
            $fields_amount = $result->field_count;
            $rows_num = $mysqli->affected_rows;
            $res = $mysqli->query('SHOW CREATE TABLE ' . $table);
            $TableMLine = $res->fetch_row();

            $table = str_replace($wpdb->prefix, 'webtoffee_', $table);
            $TableMLine[1] = str_replace($wpdb->prefix, 'webtoffee_', $TableMLine[1]);
            $content = (!isset($content) ? "" : $content) . "\n\n" . "DROP TABLE IF EXISTS `$table` ;/*END*/ " . "\n\n" . "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";/*END*/\r\nSET time_zone = \"+00:00\";/*END*/\r\n\r\n\r\n/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;/*END*/\r\n/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;/*END*/\r\n/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;/*END*/\r\n/*!40101 SET NAMES utf8 */;/*END*/\r\n--\r\n-- Database: `" . $name . "`\r\n--\r\n\r\n\r\n" . "\n\n" . $TableMLine[1] . ";/*END*/\n\n";


            for ($i = 0, $st_counter = 0; $i < $fields_amount; $i++, $st_counter = 0) {
                while ($row = $result->fetch_row()) { //when started (and every after 100 command cycle):
                    if ($st_counter % 100 == 0 || $st_counter == 0) {
                        $content .= "\nINSERT INTO " . $table . " VALUES";
                    }
                    $content .= "\n(";
                    for ($j = 0; $j < $fields_amount; $j++) {
                        if (isset($row[$j])) {
                            $row[$j] = $this->webtoffee_serialize($search, $replace, $row[$j]);
                            $content .= '"' . addslashes($row[$j]) . '"';
                        } else {
                            $content .= '""';
                        }
                        if ($j < ($fields_amount - 1)) {
                            $content .= ',';
                        }
                    }
                    $content .= ")";
                    if ((($st_counter + 1) % 100 == 0 && $st_counter != 0) || $st_counter + 1 == $rows_num) {
                        $content .= ";/*END*/";
                    } else {
                        $content .= ",";
                    }
                    $st_counter = $st_counter + 1;
                }
            }
            $content .= "\n\n\n";
        }

        $backup_name = $backup_name ? $backup_name : "database.sql";

        $myfile = fopen($download_path . '/' . $backup_name, "w") or die("Unable to open file!");
        $txt = $content;
        fwrite($myfile, $txt);
        if (fclose($myfile)) {
            return true;
        }
    }


    public function add_plugin_admin_menu()
    {

        /*
         * Add a settings page for this plugin to the Settings menu.
         *
         * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
         *
         *        Administration Menus: http://codex.wordpress.org/Administration_Menus
         *
         */
        add_menu_page('Webtoffee Migration', 'Webtoffee Migration', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page'),'dashicons-image-rotate-left');
    }

    /**
     * Add settings action link to the plugins page.
     *
     * @since    1.0.0
     */

    public function add_action_links($links)
    {
        /*
        *  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
        */
        $settings_link = array(
            '<a href="' . admin_url('options-general.php?page=' . $this->plugin_name) . '">' . __('Settings', $this->plugin_name) . '</a>',
        );

        if (array_key_exists('deactivate', $links)) {
            $links['deactivate'] = str_replace('<a', '<a class="migration-deactivate-link"', $links['deactivate']);
        }
        return array_merge($settings_link, $links);

    }

    /**
     * Render the settings page for this plugin.
     *
     * @since    1.0.0
     */

    public function display_plugin_setup_page()
    {
        $tab = 'export_migrations';
        if (!empty($_GET['tab'])) {
            $tab = $_GET['tab'];
        }
        include_once('partials/webtoffee-wordpress-migrator-admin-display.php');
    }

    public function import_migrations()
    {
        include_once('partials/webtoffee-wordpress-migrator-import.php');
    }

    public function export_migrations()
    {
        include_once('partials/webtoffee-wordpress-migrator-export.php');
    }

    public function backup_migrations()
    {
        include_once('partials/webtoffee-wordpress-migrator-backup.php');
    }

    public function help_migrations()
    {
        include_once('partials/webtoffee-wordpress-migrator-help.php');

    }


    //TODO Helper Fucntions to be moved using namespacing
    public function webtoffee_serialize($search = '', $replace = '', $data = '', $serialised = FALSE)
    {
        if (is_string($data) && ($unserialized = @unserialize($data)) !== FALSE) {
            $data = $this->webtoffee_serialize($search, $replace, $unserialized, TRUE);
        } elseif (is_array($data)) {
            $_tmp = [];
            foreach ($data as $key => $value) {
                $_tmp[$key] = $this->webtoffee_serialize($search, $replace, $value, FALSE);
            }

            $data = $_tmp;
            unset($_tmp);
        } elseif (is_object($data)) {
            $_tmp = $data; // new instance
            $props = get_object_vars($data);
            foreach ($props as $key => $value) {
                $_tmp->$key = $this->webtoffee_serialize($search, $replace, $value, FALSE);
            }
            $data = $_tmp;
            unset($_tmp);
        } else {
            if (is_string($data)) {
                $data = str_replace($search, $replace, $data);
            }
        }
        if ($serialised) {
            return maybe_serialize($data);
        }

        return $data;
    }

}
