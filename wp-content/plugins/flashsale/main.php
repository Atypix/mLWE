<?php 

/*

Plugin Name: WooCommerce Flash Sale Pricing and Discounts

Plugin URI: http://proword.net/woo_sale_revolution/

Description: Create advanced sale & flash sale with different scenario for your woocommerce shop

Author: Proword

Version: 3.1

Author URI: http://proword.net/

Text Domain: pw_wc_flash_sale

Domain Path: /languages/ 

 */

define('plugin_dir_url_flash_sale', plugin_dir_url( __FILE__ ));

define ('PW_flash_sale_URL',plugin_dir_path( __FILE__ ));



define('flash_sale_v1_load_plugin_textdomain', 'flash_sale_load_plugin_textdomain');



if ( ! defined( 'RC_TC_BASE_FILE' ) )

    define( 'RC_TC_BASE_FILE', __FILE__ );

if ( ! defined( 'RC_TC_BASE_DIR' ) )

    define( 'RC_TC_BASE_DIR', dirname( RC_TC_BASE_FILE ) );

if ( ! defined( 'RC_TC_PLUGIN_URL' ) )

    define( 'RC_TC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );



	/**

	 * Localisation

	 **/

	load_plugin_textdomain( 'pw_wc_flash_sale', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	

//add_filter( 'template_include', 'rc_tc_template_chooser');

function rc_tc_template_chooser( $template ) {

 

    // Post ID

    $post_id = get_the_ID();

 

    // For all other CPT

    if ( get_post_type( $post_id ) != 'flash_sale' ) {

        return $template;

    }

 

    // Else use custom template

    if ( is_single() ) {

        return rc_tc_get_template_hierarchy( 'single' );

    }

 

}



function rc_tc_get_template_hierarchy( $template ) {

 

    // Get the template slug

    $template_slug = rtrim( $template, '.php' );

    $template = $template_slug . '.php';

 

    // Check if a custom template exists in the theme folder, if not, load the plugin template file

    if ( $theme_file = locate_template( array( 'plugin_template/' . $template ) ) ) {

        $file = $theme_file;

    }

    else {

        $file = RC_TC_BASE_DIR . '/includes/templates/' . $template;

    }

 

    return apply_filters( 'rc_repl_template_' . $template, $file );

}

//add_filter( 'template_include', 'rc_tc_template_chooser' );

 add_action( 'init', 'create_post_type_flashsale' );

function create_post_type_flashsale() {

  register_post_type( 'flash_sale',

    array(

      'labels' => array(

        'name' => __( 'flash_sale' ),

        'singular_name' => __( 'flash_sale' )

      ),

	'public' => true,

	'has_archive' => true,

	'show_in_menu'=>false, 

    )

  );

}



/* Filter the single_template with our custom function*/

//add_filter('single_template', 'my_custom_template');



function my_custom_template($single) {

    global $wp_query, $post;

	/* Checks for single template by post type */

	//echo dirname( __FILE__ );

	if ($post->post_type == "flash_sale"){

	//	if(file_exists(plugin_dir_url_flash_sale. 'a.php'))

			return dirname( __FILE__ ) . '/template/a.php';

	}

		return $single;

}



class woocommerce_flashsale {



	private $page_id;

	

	public function __construct() 

	{

		$this->includes();

		//add_action( 'widgets_init', array( $this, 'include_widgets' ) );

		

		//add_action('admin_init', array($this, 'plugin_options_setup'));

		

		add_action('admin_head', array( $this, 'admin_js'));		

		add_action( 'init' , array( $this, 'kv_time_js' ) );

		add_action( 'wp_head', array( $this, 'dynamic_custom_css' ));	



			add_action( 'admin_menu', array( $this, 'add_menu_link' ) );

		

		//Shortcode Ui

		//add_filter('init', array( $this,'flash_sale_shortcodes_add_scripts'));

		//add_action('admin_head', array( $this,'flash_sale_shortcodes_addbuttons'));		

		register_activation_hook( __FILE__ , array( $this,'woo_flashsale_install' ));

	}

	

	/*public function calculate_discount($pw_type_discount,$price,$pw_discount)

	{	

		if ($pw_type_discount == 'percent') {

			$discount = $price * ($pw_discount / 100);

		}

		else if ($pw_type_discount == 'price') {

			$discount = $pw_discount;

		}

		else if ($pw_type_discount == 'fixed') {

			$discount = $price - $pw_discount;

		}

		$discount = ceil($discount * pow(10, get_option('woocommerce_price_num_decimals')) - 0.5) * pow(10, -((int) get_option('woocommerce_price_num_decimals')));	

		

		return $discount;

	}

	*/

/*	public function calculate_price_discount($price,$discount)

	{

		$price=$price - $discount;

		return ($price<0 ? 0 : $price);

	}		

*/

	public function woo_flashsale_install()

	{

		$setting="";

		$setting['pw_woocommerce_flashsale_countdown']="style1";

		$setting['pw_woocommerce_flashsale_single_countdown']="yes";

		$setting['pw_woocommerce_flashsale_archive_countdown']="yes";

		$setting['pw_woocommerce_flashsale_color_countdown']="#6bb667";

		$setting['pw_woocommerce_flashsale_back_color_countdown']="#f5f5f5";

		$setting['pw_woocommerce_flashsale_fontsize_countdown']="medium";

		$setting['pw_woocommerce_flashsale_timezone_countdown']="-8";

		$setting['Days']="Days";

		$setting['Daysinarchive']="Daysinarchive";

		$setting['Hour']="Hour";

		$setting['Hourinarchive']="Hourinarchive";

		$setting['Minutes']="Minutes";

		$setting['Minutesinarchive']="Minutesinarchive";

		$setting['Seconds']="Seconds";

		$setting['Secondsinarchive']="Secondsinarchive";

		update_option( 'pw_flashsale_discount_options', $setting );

		

		update_option( 'pw_matched_cart', 'only' );

		update_option( 'pw_matched_rule', 'all' );		

		

	}

	//

	function flash_sale_shortcodes_add_scripts() {

		if(!is_admin()) {

			wp_enqueue_style('flash_sale_shortcodes', plugin_dir_url_flash_sale.'/includes/shortcodes.css');

			

			wp_enqueue_script('jquery');

			wp_register_script('flash_sale_shortcodes_js', plugin_dir_url_flash_sale.'/includes/shortcodes.js', 'jquery');

			wp_enqueue_script('flash_sale_shortcodes_js');

		} else {

			wp_enqueue_style( 'wp-color-picker' );

			wp_enqueue_script( 'wp-color-picker' );

		}		

	}





	function flash_sale_shortcodes_addbuttons() {

		global $typenow;

		// check user permissions

		if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {

		return;

		}

		// check if WYSIWYG is enabled

		if ( get_user_option('rich_editing') == 'true') {

			add_filter("mce_external_plugins", array( $this, "add_flash_sale_shortcodes_tinymce_plugin"));

			add_filter('mce_buttons', array( $this, 'register_flash_sale_shortcodes_button'));

		}

	}

	

	function add_flash_sale_shortcodes_tinymce_plugin($plugin_array) {

		$plugin_array['flash_sale_shortcodes_button'] = plugins_url( '/includes/tinymce_button.js', __FILE__ );

		return $plugin_array;

	}

	function register_flash_sale_shortcodes_button($buttons) {

	   array_push($buttons, "flash_sale_shortcodes_button");

	   return $buttons;

	}	

	

	public function includes()

	{

		add_image_size( 'pw_hor_image', '768', '506', true );

		add_image_size( 'pw_ver_image', '470', '634', true );

		add_image_size( 'pw_rec_image', '500', '500', true );	

		

		include_once (PW_flash_sale_URL.'/core/functions.php') ;

		include_once (PW_flash_sale_URL.'/core/discount_price.php') ;

		include_once (PW_flash_sale_URL.'/core/discount_product.php') ;

		

		require( 'core/discount_cart.php' );		



		//require( 'template/front/product.php' );

		require( 'core/shortcode.php' );

		//require( 'core/admin/setting-tab.php' );

	}

	

	public function include_widgets() {

		include_once( 'core/widget.php' );

	}	

	

	public function admin_js() {

		if(is_admin())

		{

			wp_register_style('kv_js_time_style' , plugin_dir_url_flash_sale. 'css/jquery.datetimepicker.css');

			wp_enqueue_style('kv_js_time_style');

			

			wp_enqueue_script('jquery-time-picker' ,  plugin_dir_url_flash_sale. 'js/jquery.datetimepicker.js',  array('jquery' ));

			wp_enqueue_style('flipclock-master-cssss', plugin_dir_url_flash_sale.'css/jquery.countdown.css');		

			wp_enqueue_script('flipclocksdsd-master-jsaaaa',  plugin_dir_url_flash_sale.'js/jquery.countdown.min.js',array( 'jquery' ));	

			////////////////ADMIN STYLE///////////////////

		    wp_enqueue_style('pw-fs-main-style',plugin_dir_url_flash_sale.'/css/admin-css.css', array() , null);

		  

		    /////////////////////////CSS CHOSEN///////////////////////

		    wp_enqueue_style('pw-fs-chosen-style',plugin_dir_url_flash_sale.'/css/chosen/chosen.css', array() , null);

		    wp_enqueue_script( 'pw-fs-chosen-script', plugin_dir_url_flash_sale.'/js/chosen/chosen.jquery.min.js', array( 'jquery' ));			

			

			wp_enqueue_script('jquery');

		    wp_enqueue_script( 'pw-dependsOn', plugin_dir_url_flash_sale.'/js/dependsOn-1.0.1.min.js', array( 'jquery' ));				

		}

	}

	public function kv_time_js() 

	{

		if(!is_admin())

		{

			//Css

			wp_register_style('pw_css_layouts' , plugin_dir_url_flash_sale. 'css/layouts/style.css');

			wp_enqueue_style('pw_css_lightslider' , plugin_dir_url_flash_sale. 'css/lightslider/lightslider.css');

			//wp_register_style('pw_css_grid_layouts' , plugin_dir_url_flash_sale. 'css/grid/style.css');

			

			//jquery

			wp_register_script('pw_jquery_easing', plugin_dir_url_flash_sale.'js/jquery.easing.1.3.js', 'jquery');

			

			wp_enqueue_script('pw_jquery_lightslider', plugin_dir_url_flash_sale.'js/lightslider/lightslider.js',array( 'jquery' ));	

			

		}

		wp_enqueue_style('public-style', plugin_dir_url_flash_sale.'css/public-style.css');

		wp_enqueue_style('fontawesome-style', plugin_dir_url_flash_sale.'css/fonts/font-awesome.css');

		

		wp_enqueue_style('flipclock-master-cssss', plugin_dir_url_flash_sale.'css/jquery.countdown.css');		

		

		wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');

		//wp_register_style( 'flipclock-master-css', plugin_dir_url_flash_sale.'css/flipclock.css' );		

		

		wp_enqueue_style('flipclock-master-css', plugin_dir_url_flash_sale.'css/flipclock.css');

		wp_enqueue_style( 'flipclock-master-css' );

		

		//slider

		wp_enqueue_style('fl-slider-style', plugin_dir_url_flash_sale.'css/bx-slider/jquery.bxslider.css');

		

		//grid

		wp_enqueue_style('fl-grid-style', plugin_dir_url_flash_sale.'css/grid/style.css');

		

		//Layouts

		wp_enqueue_style('fl-layouts-style', plugin_dir_url_flash_sale.'css/layouts/style.css');

		

		wp_enqueue_script('jquery');			

		wp_enqueue_script('flipclock-master-jsaa',  plugin_dir_url_flash_sale.'js/flipclock.js',array( 'jquery' ));	

		

		wp_enqueue_script('flipclocksdsd-master-jsaaaa',  plugin_dir_url_flash_sale.'js/jquery.countdown.min.js',array( 'jquery' ));

		//wp_register_script('flipclock-master-js', plugin_dir_url_flash_sale.'js/flipclock.js',array( 'jquery' ));		

		wp_enqueue_script('flipclock-master-js');		

		

		//slider

		wp_enqueue_script('fl-slider-jquery',  plugin_dir_url_flash_sale.'js/bx-slider/jquery.bxslider.js',array( 'jquery' ));	

	}	

	public function add_menu_link() {

		global $submenu;

		if (isset($submenu['woocommerce'])) {

			add_submenu_page(

				'woocommerce',

				__('Flash Sale Discounts', 'pw_wc_flash_sale'),

				__('Flash Sale Discounts', 'pw_wc_flash_sale'),

				'edit_posts',

				'rule_list',

				array($this, 'show_sub_menu_page')

			);

		}

	}



	public function show_sub_menu_page() {

		$this->tabs = array(

			'pricing' => array('name' => __('Pricing Rules', 'pw_wc_flash_sale')),

			'cart' => array('name' => __('Cart Discounts', 'pw_wc_flash_sale')),

			'settings' => array('name' => __('Settings', 'pw_wc_flash_sale')),

		);



		if (isset($_GET['tab']) && in_array($_GET['tab'], array_keys($this->tabs)))

			$current=$_GET['tab'];

		else {

			$keys = array_keys($this->tabs);

			$current=$keys[0];

		}

		echo '<div class="pw-wrap">';

		require_once PW_flash_sale_URL . 'core/admin/tab.php';				

		

		if($current=="pricing")

			 require_once PW_flash_sale_URL . 'core/admin/pricing.php';

		else if($current=="cart")

			require_once PW_flash_sale_URL . 'core/admin/cart.php';

		else if($current=="settings")

			require_once PW_flash_sale_URL . 'core/admin/settings.php';

		echo '</div>';

	}

	

	public function dynamic_custom_css() {

		$setting=get_option("pw_flashsale_discount_options");

		echo '<style>

        	ul.fl-countdown-pub.fl-style1 li span , ul.fl-countdown-pub.fl-style1 li p , ul.fl-countdown-pub.fl-style1 li.seperator,

			ul.fl-countdown-pub.fl-style2 li span , ul.fl-countdown-pub.fl-style2 li p , ul.fl-countdown-pub.fl-style2 li.seperator,

			ul.fl-countdown-pub.fl-style3 li span , ul.fl-countdown-pub.fl-style3 li p , ul.fl-countdown-pub.fl-style3 li.seperator,

			ul.fl-countdown-pub.fl-style4 li span , ul.fl-countdown-pub.fl-style4 li p , ul.fl-countdown-pub.fl-style4 li.seperator,

			ul.fl-countdown-pub.fl-style5 li span , ul.fl-countdown-pub.fl-style5 li p , ul.fl-countdown-pub.fl-style5 li.seperator,

			ul.fl-countdown-pub.fl-style6 li span , ul.fl-countdown-pub.fl-style6 li p , ul.fl-countdown-pub.fl-style6 li.seperator{ 

				color:'.$setting['pw_woocommerce_flashsale_color_countdown'].';

			}

					

			ul.fl-countdown-pub.fl-style2 li ,ul.fl-countdown-pub.fl-style3 li span, ul.fl-countdown-pub.fl-style4 li span  ,ul.fl-countdown-pub.fl-style5 li  ,ul.fl-countdown-pub.fl-style6 li

			 { 

			 	background:'.$setting['pw_woocommerce_flashsale_back_color_countdown'].';

			 }

		</style>';

	}

}

new woocommerce_flashsale();





add_action('wp_ajax_pw_fetch_rule', 'pw_fetch_rule');

add_action('wp_ajax_nopriv_pw_fetch_rule', 'pw_fetch_rule');

function pw_fetch_rule() {

/*	echo '<option>dsd</option>';*/

	$query_meta_query=array('relation' => 'AND');

	$query_meta_query[] = array(

		'key' =>'status',

		'value' => "active",

		'compare' => '=',

	);

	$args=array(

		'post_type'=>'flash_sale',

		'posts_per_page'=>-1,

		'order'=>'data',

		'orderby'=>'modified',

		'meta_query' => $query_meta_query,		

	);

	$loop = new WP_Query( $args );		



		while ( $loop->have_posts() ) : 

			$loop->the_post();

			$pw_type=get_post_meta(get_the_ID(),'pw_type',true);

			if($pw_type=="flashsale")

			{

				echo '<option value="'.get_the_ID().'">

						'.get_post_meta(get_the_ID(),'pw_name',true).'

					</option>';

			}

		endwhile;	



	exit(0);

}



add_action('wp_ajax_pw_fetch_product', 'pw_fetch_product');

add_action('wp_ajax_nopriv_pw_fetch_product', 'pw_fetch_product');

function pw_fetch_product() {



	$args=array(

		'post_type'=>'product',

		'posts_per_page'=>-1,

		'order'=>'data',

		'orderby'=>'DESC',

	);

	$loop = new WP_Query( $args );		



		while ( $loop->have_posts() ) : 

			$loop->the_post();

			echo '<option value='.get_the_ID().'>

					'.get_the_title().'

				</option>';

		endwhile;	



	exit(0);

}



add_action('wp_ajax_pw_save_cart_matched', 'pw_save_cart_matched');

add_action('wp_ajax_nopriv_pw_save_cart_matched', 'pw_save_cart_matched');

function pw_save_cart_matched() {



	update_option( 'pw_matched_cart', @$_POST['pw_matched_cart'] );	

}



add_action('wp_ajax_pw_save_rule_matched', 'pw_save_rule_matched');

add_action('wp_ajax_nopriv_pw_save_rule_matched', 'pw_save_rule_matched');

function pw_save_rule_matched() {



	update_option( 'pw_matched_rule', @$_POST['pw_matched_rule'] );	

}



function calculate_modifiera( $percentage, $price )

{

	$percentage = $percentage / 100;

	return $percentage * $price;

}

function calculate_discount_modifiera( $percentage, $price ) {

	$percentage = str_replace( '%', '', $percentage ) / 100;

	return $percentage * $price;

}	







function pw_list_capabilities(){

	$capabilities = array();

	$default_caps = pw_get_capabilities_def();

	$role_caps = pw_get_role_capabilities();

	

	//$capabilities = array_merge( $default_caps, $role_caps, $plugin_caps );	

	$capabilities = array_merge( $default_caps, $role_caps);	

	sort( $capabilities );

	return array_unique( $capabilities );	

}

function pw_get_role_capabilities() {

	global $wp_roles;



	/* Set up an empty capabilities array. */

	$capabilities = array();



	/* Loop through each role object because we need to get the caps. */

	foreach ( $wp_roles->role_objects as $key => $role ) {



		/* Roles without capabilities will cause an error, so we need to check if $role->capabilities is an array. */

		if ( is_array( $role->capabilities ) ) {



			/* Loop through the role's capabilities and add them to the $capabilities array. */

			foreach ( $role->capabilities as $cap => $grant )

				$capabilities[$cap] = $cap;

		}

	}



	/* Return the capabilities array, making sure there are no duplicates. */

	return array_unique( $capabilities );

}

function pw_get_capabilities_def() {

	$ret = array(

		'activate_plugins','add_users','create_users','delete_others_pages','delete_others_posts','delete_pages','delete_plugins','delete_posts','delete_private_pages','delete_private_posts','delete_published_pages','delete_published_posts','delete_users','edit_dashboard','edit_files','edit_others_pages','edit_others_posts','edit_pages','edit_plugins','edit_posts','edit_private_pages','edit_private_posts','edit_published_pages','edit_published_posts','edit_theme_options','edit_themes','edit_users','import','install_plugins','install_themes','list_users','manage_categories','manage_links','manage_options','moderate_comments','promote_users','publish_pages','publish_posts','read',

		'read_private_pages',

		'read_private_posts',

		'remove_users',

		'switch_themes',

		'unfiltered_html',

		'unfiltered_upload',

		'update_core',

		'update_plugins',

		'update_themes',

		'upload_files'

	);

	return $ret;					

}	

?>

