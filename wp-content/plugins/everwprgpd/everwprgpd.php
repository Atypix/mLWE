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
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class EverWpRgpd
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Plugin Details
        $this->plugin               = new stdClass;
        $this->plugin->name         = 'everwprgpd'; // Plugin Folder
        $this->plugin->displayName  = 'everwprgpd'; // Plugin Name
        $this->plugin->version      = '1.1.2';
        $this->plugin->folder       = plugin_dir_path(__FILE__);
        $this->plugin->url          = plugin_dir_url(__FILE__);
    }
}

/**
 * Loads plugin textdomain
 */
function everwprgpd_plugins_loaded() {
    load_plugin_textdomain( 'everwprgpd', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'everwprgpd_plugins_loaded', 0 );
/**
 * Install function : create database
 */
function everwprgpd_install()
{
	add_option( 'everwprgpdeverofficername', 'Name of your data officer' );
	add_option( 'everwprgpdeverofficeremail', 'Email of your data officer' );
	add_option( 'everwprgpdeverrgpdmessage', 'Custom message shown in front office' );
  add_option( 'everwprgpdlegalmentions', 'URL of your legal mentions (required)' );

    if (!file_exists(WP_PLUGIN_DIR.'woocommerce/woocommerce.php')) {
        // Create post object
        $evercustomerrgpd = array(
          'post_title'    => 'everrgpdaccount',
          'post_content'   => '[everrgpd]',
          'post_status'   => 'publish',
          'post_author'   => get_current_user_id(),
          'post_type' => 'page'
        );
        // Insert the post into the database
        wp_insert_post($evercustomerrgpd);
    }
}

/**
 * Uninstall function
 */
function everwprgpd_uninstall()
{
	delete_option( 'everofficername' );
	delete_option( 'everofficeremail' );
	delete_option( 'everrgpdmessage' );
  delete_option( 'everwprgpdlegalmentions' );
}

/**
 * Create admin Page
 */
 add_action('admin_menu', 'everbackofficergpd');

add_action( 'wp_enqueue_scripts', 'everrgpd_enqueue_styles' );
function everrgpd_enqueue_styles(){
    wp_register_style( 'customeverrgpd', get_site_url() . '/wp-content/plugins/everwprgpd/views/css/custom.css' );
    wp_enqueue_style( 'customeverrgpd' );
}

/**
 * Adds administration menu.
 */
function everbackofficergpd()
{
    //Settings
    add_menu_page(
        __('RGPD', 'textdomain'),
        __('RGPD', 'textdomain'),
        'manage_options',
        'everwprgpdlist',
        'everrgpdcallback',
        ''
    );
}

/**
 * Display callback for the rgpd admin page.
 */
function everrgpdcallback()
{
    // Load Settings Form
    include_once(WP_PLUGIN_DIR.'/everwprgpd/views/back/settings.php');
}

/**
 * Display shortcode for the rgpd customer page.
 */
function everwprgpd_shortcode($atts)
{
    if (is_user_logged_in()) {
		include(WP_PLUGIN_DIR.'/everwprgpd/views/front/rgpdfront.php');
    } else {
        wp_login_form();
    }
}

/**
 * Ajax admin enqueue scripts
 */
function everwprgpdadmin_enqueue_scripts(){

  wp_register_script( 'everwprgpd', get_site_url() . '/wp-content/plugins/everwprgpd/views/js/everwprgpd.js', array(), false, true );

  wp_enqueue_script( 'everwprgpd' );

  wp_localize_script( 'everwprgpd', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

}

/**
 * Ajax front enqueue scripts
 */
function everrgpd_enqueue_scripts(){

  wp_register_script( 'everwprgpdfront', get_site_url() . '/wp-content/plugins/everwprgpd/views/js/everwprgpdfront.js', array(), false, true );

  wp_enqueue_script( 'everwprgpdfront' );

  wp_localize_script( 'everwprgpdfront', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

}

/**
 * Set back values into database
 */
function everrgpdbackcallbacks(){
  //Save data depending on callback
  if (isset($_POST['everofficername']) && !empty($_POST['everofficername'])) {
      $everofficername = stripslashes($_POST['everofficername']);
      $update = update_option( 'everwprgpdeverofficername', $everofficername );
  }

  //Save data depending on callback
  if (isset($_POST['everofficeremail']) && !empty($_POST['everofficeremail'])) {
      $everofficeremail = stripslashes($_POST['everofficeremail']);
      $update = update_option( 'everwprgpdeverofficeremail', $everofficeremail );
  }

  //Save data depending on callback
  if (isset($_POST['everrgpdmessage']) && !empty($_POST['everrgpdmessage'])) {
      $everrgpdmessage = stripslashes($_POST['everrgpdmessage']);
      $update = update_option( 'everwprgpdeverrgpdmessage', $everrgpdmessage );
  }

  //Save data depending on callback
  if (isset($_POST['everwprgpdlegalmentions']) && !empty($_POST['everwprgpdlegalmentions'])) {
      $everwprgpdlegalmentions = stripslashes($_POST['everwprgpdlegalmentions']);
      $update = update_option( 'everwprgpdlegalmentions', $everwprgpdlegalmentions );
  }

	wp_die();

}

/**
 * Set front values into database
 */

function everrgpdfrontcallbacks(){
  //Save data depending on callback
  if (isset($_POST['format']) && !empty($_POST['format'])) {
  	$format = $_POST['format'];
  	switch ($format) {
  		case 'json':
		    $folder = get_site_url().'/wp-content/plugins/everwprgpd/data/json/everrgpdjson.php';
        echo $folder;
  			break;

  		case 'xml':
		    $folder = get_site_url().'/wp-content/plugins/everwprgpd/data/xml/everrgpdxml.php';
        echo $folder;
  			break;

  		case 'csv':
		    $folder = get_site_url().'/wp-content/plugins/everwprgpd/data/csv/everrgpdcsv.php';
        echo $folder;
  			break;
  		
  		default:
  			# code...
  			break;
  	}

  }

	wp_die();

}

// Add term and conditions check box on registration form
if (file_exists(WP_PLUGIN_DIR.'/woocommerce/woocommerce.php')) {
  add_action( 'woocommerce_register_form', 'add_terms_and_conditions_to_registration', 20 );
  function add_terms_and_conditions_to_registration() {
      $terms_page_id = wc_get_page_id( 'terms' );
      if ( $terms_page_id > 0 ) {
          $terms         = get_post( $terms_page_id );
          $terms_content = has_shortcode( $terms->post_content, 'woocommerce_checkout' ) ? '' : wc_format_content( $terms->post_content );

          if ( $terms_content ) {
              echo '<div class="woocommerce-terms-and-conditions" style="display: none; max-height: 200px; overflow: auto;">' . $terms_content . '</div>';
          }
          ?>

          <p class="form-row terms wc-terms-and-conditions">
              <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                  <input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="terms" <?php checked( apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms'] ) ), true ); ?> id="terms" /> <span><?php printf( _e( 'I&rsquo;ve read and accept the <a href="%s" target="_blank" class="woocommerce-terms-and-conditions-link">terms &amp; conditions</a>', 'everwprgpd' ), esc_url( wc_get_page_permalink( 'terms' ) ) ); ?></span> <span class="required">*</span>
              </label>
              <input type="hidden" name="terms-field" value="1" />
          </p>

      <?php
      }
  }

} else {

  // Add privacy policy field.

  add_action( 'register_form', 'loginpress_add_privacy_policy_field' );

  function loginpress_add_privacy_policy_field() { ?>
    <p>
      <label for="lp_privacy_policy">
        <a href="<?php echo esc_html( get_option( 'everwprgpdlegalmentions' ) ); ?>"><?php _e( 'Privacy Policy', 'everwprgpd' ) ?></a>
        <br />
        <input type="checkbox" name="lp_privacy_policy" id="lp_privacy_policy" class="checkbox" />
      </label>
    </p>
    <?php
  }

  // Add validation. In this case, we make sure lp_privacy_policy is required.

  add_filter( 'registration_errors', 'loginpresss_privacy_policy_auth', 10, 3 );   

  function loginpresss_privacy_policy_auth( $errors, $sanitized_user_login, $user_email ) {
  
    if ( ! isset( $_POST['lp_privacy_policy'] ) ) :
      $errors->add( 'policy_error', "<strong>ERROR</strong>: Please accept the privacy policy." );
      return $errors;
    endif;
    return $errors;
  }

  // Lastly, save our extra registration user meta.

  add_action( 'user_register', 'loginpress_privacy_policy_save' );

  function loginpress_privacy_policy_save( $user_id ) {
    if ( isset( $_POST['lp_privacy_policy'] ) )
       update_user_meta( $user_id, 'lp_privacy_policy', $_POST['lp_privacy_policy'] );
  }
}


// Validate required term and conditions check box
add_action( 'woocommerce_register_post', 'terms_and_conditions_validation', 20, 3 );

function terms_and_conditions_validation( $username, $email, $validation_errors ) {
    if ( ! isset( $_POST['terms'] ) )
        $validation_errors->add( 'terms_error', _e( 'Terms and condition are not checked!', 'everwprgpd' ) );

    return $validation_errors;

}

//Enqueue scripts
add_action( 'admin_enqueue_scripts', 'everwprgpdadmin_enqueue_scripts' );
//Ajax backoff callbacks
add_action( 'wp_ajax_everrgpdbackcallbacks', 'everrgpdbackcallbacks' );
add_action( 'wp_ajax_nopriv_everrgpdbackcallbacks', 'everrgpdbackcallbacks' );
//Ajax frontoff callbacks
add_action( 'wp_enqueue_scripts', 'everrgpd_enqueue_scripts' );
add_action( 'wp_ajax_everrgpdfrontcallbacks', 'everrgpdfrontcallbacks' );
add_action( 'wp_ajax_nopriv_everrgpdfrontcallbacks', 'everrgpdfrontcallbacks' );
//Add shortcode
add_shortcode('everrgpd', 'everwprgpd_shortcode');
//Add options on install
register_activation_hook(__FILE__, 'everwprgpd_install');

//Delete options on uninstall
register_deactivation_hook(__FILE__, 'everwprgpd_uninstall');

/**
 * Custom tab for showing websites
 */
class everwprgpd_endpoint
{
    /**
     * Custom endpoint name.
     *
     * @var string
     */
    public static $endpoint = 'everwprgpd';

    /**
     * Plugin actions.
     */
    public function __construct()
    {
        // Actions used to insert a new endpoint in the WordPress.
        add_action('init', array( $this, 'add_endpoints' ));
        add_filter('query_vars', array( $this, 'add_query_vars' ), 0);


        // Change the My Accout page title.
        add_filter('the_title', array( $this, 'endpoint_title' ));

        // Insering your new tab/page into the My Account page.
        add_filter('woocommerce_account_menu_items', array( $this, 'new_menu_items' ));
        add_action('woocommerce_account_' . self::$endpoint .  '_endpoint', array( $this, 'endpoint_content' ));

    }


    /**
     * Register new endpoint to use inside My Account page.
     *
     * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
     */
    public function add_endpoints()
    {
        add_rewrite_endpoint(self::$endpoint, EP_ROOT | EP_PAGES);
    }


    /**
     * Add new query var.
     *
     * @param array $vars
     * @return array
     */
    public function add_query_vars($vars)
    {
        $vars[] = self::$endpoint;
        return $vars;
    }

    /**
     * Set endpoint title.
     *
     * @param string $title
     * @return string
     */
    public function endpoint_title($title)
    {
        global $wp_query;

        $is_endpoint = isset($wp_query->query_vars[ self::$endpoint ]);

        if ($is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page()) {
            // New page title.
            $title = __('Get my account datas', 'everwprgpd');
            remove_filter('the_title', array( $this, 'endpoint_title' ));
        }
        return $title;
    }

    /**
     * Insert the new endpoint into the My Account menu.
     *
     * @param array $items
     * @return array
     */
    public function new_menu_items($items)
    {
        // Remove the logout menu item.
        $logout = $items['customer-logout'];
        unset($items['customer-logout']);

        // Insert your custom endpoint.
        $items[ self::$endpoint ] = __('Get my account datas', 'everwprgpd');

        // Insert back the logout item.
        $items['customer-logout'] = $logout;
        return $items;
    }



    /**
     * Endpoint HTML content.
     */
    public function endpoint_content()
    {
        echo do_shortcode('[everrgpd]');
    }

    /**
     * Plugin install action.
     * Flush rewrite rules to make our custom endpoint available.
     */
    public static function install()
    {
        flush_rewrite_rules();
    }
}

new everwprgpd_endpoint();
// Flush rewrite rules on plugin activation.
register_activation_hook(__FILE__, array( 'everwprgpd_endpoint', 'install' ));