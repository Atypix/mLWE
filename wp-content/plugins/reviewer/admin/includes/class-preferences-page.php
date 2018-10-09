<?php

/**
 * Reviewer Plugin v.2
 * Created by Michele Ivani
 */
class RWP_Preferences_Page extends RWP_Admin_Page
{
	protected static $instance = null;
	public $preferences_fields;
	public $option_value;
	private $to_inf = 5000;
	protected $capManageReviews = 'rwp_manage_user_reviews';

	public function __construct()
	{
		parent::__construct();

		$this->preferences_fields = RWP_Preferences_Page::get_preferences_fields();
		$this->menu_slug = 'reviewer-preferences-page';
		$this->parent_menu_slug = 'reviewer-main-page';
		$this->option_name = 'rwp_preferences';
		$this->option_value = RWP_Reviewer::get_option( $this->option_name );
		$this->add_menu_page();
		$this->register_page_fields();
		add_action( 'admin_enqueue_scripts', array( $this, 'localize_script') );
	}

	public function add_menu_page()
	{
		add_submenu_page( $this->parent_menu_slug, __( 'Preferences', $this->plugin_slug), __( 'Preferences', $this->plugin_slug), $this->capability, $this->menu_slug, array( $this, 'display_plugin_admin_page' ) );
	}

	public function localize_script()
	{
		$action_name = 'rwp_ajax_action_restore_data';
		wp_localize_script( $this->plugin_slug . '-admin-script', 'restoreDataObj', array('ajax_nonce' => wp_create_nonce( $action_name ), 'ajax_url' => admin_url('admin-ajax.php'), 'action' => $action_name ) );

		$action_name = 'rwp_ajax_action_demo_notification';
		wp_localize_script( $this->plugin_slug . '-admin-script', 'demoNotificationDataObj', array('ajax_nonce' => wp_create_nonce( $action_name ), 'ajax_url' => admin_url('admin-ajax.php'), 'action' => $action_name ) );

		$action_name = 'rwp_ajax_action_clear_rosu_cache';
		wp_localize_script( $this->plugin_slug . '-admin-script', 'rosuDataObj', array('ajax_nonce' => wp_create_nonce( $action_name ), 'ajax_url' => admin_url('admin-ajax.php'), 'action' => $action_name ) );
	}

	public static function send_demo_notification()
	{
		$res = array( 'code' => 400, 'data'=> array( 'msg' => __( 'Unable to send email notification. Check your Mail settings', 'reviewer' ) ) );

		// Validation
		if( !isset( $_POST['email'] ) || !is_email( $_POST['email'] ) ) {

			$res['data']['msg'] = __( 'Type a valid email address', 'reviewer' );
			die( json_encode( $res ) );
		}

		 //Get rid of wwww
		$domain_name =  preg_replace('/^www\./','',$_SERVER['SERVER_NAME']);

		//add_filter( 'wp_mail_content_type', array('RWP_Reviewer', 'set_html_content_type') );

		$to 		= $_POST['email'];
		$subject	= '[RWP] Notification';
		$headers	= array('From: Reviewer Plugin <do-not-reply@'.$domain_name);

		$eol		= "\r\n";

		$message 	 = "Reviewer Wordpress Plugin" . $eol;
		$message    .= "--------------------------------------" . $eol . $eol;
		$message    .= "Congratulation! Your email server is configured correctly." . $eol;
		$message    .= "You will ricevie an email notification when new users reviews will be submitted to your site." . $eol . $eol;
		$message    .= "If you have any issues about the Reviewer Plugin, follow the Support rules written inside documentation." . $eol . $eol;
		$message    .= "Reviewer Team". $eol;

		$message = wordwrap( $message, 70, $eol );

		// ob_start();
		// include 'email-template.php';
		// $message = ob_get_clean();

		$sending = wp_mail( $to, $subject, $message, $headers );

		//remove_filter( 'wp_mail_content_type', array('RWP_Reviewer', 'set_html_content_type') );

		if( $sending ) {
			$res['code'] = 200;
			$res['data']['msg'] = __( 'Email sent. Check your Mail Inbox or Spam folder', 'reviewer' );
		}

		die( json_encode( $res ) );
	}

	public static function ajax_callback()
	{
		$restore_value =  RWP_Reviewer::get_option( 'rwp_restore' );
		if ( ! empty( $restore_value ) )
			die( json_encode( array('msg' => __( 'Data already restored', 'reviewer') ) ) );

		// - - - Templates - - -
		$previous_templates =  RWP_Reviewer::get_option( 'rwp_reviewer_templates' );
		$templates = RWP_Reviewer::get_option( 'rwp_templates' );

		foreach ($previous_templates as $t) {
			$temp = array();

			$temp['template_id'] = $t['template_id'];
			$temp['template_name'] = $t['template_title'];
			$temp['template_minimum_score'] = $t['template_items_rage']['min'];
			$temp['template_maximum_score'] = $t['template_items_rage']['max'];
			$temp['template_score_percentages'] = '30-69';

			foreach ($t['template_items'] as $criterion)
				$temp['template_criterias'][] = $criterion['label'];

			switch ($t['template_theme']) {
				case 'rwp_bars_theme':
					$theme = 'rwp-theme-1';
					break;

				case 'rwp_bars_mini_theme':
					$theme = 'rwp-theme-5';
					break;

				case 'rwp_stars_theme':
					$theme = 'rwp-theme-2';
					break;

				case 'rwp_stars_mini_theme':
					$theme = 'rwp-theme-6';
					break;

				case 'rwp_circles_theme':
					$theme = 'rwp-theme-3';
					break;

				case 'rwp_big_circles_theme':
					$theme = 'rwp-theme-7';
					break;

				default:
					$theme = 'rwp-theme-1';
					break;
			}

			$temp['template_theme'] = $theme;
			$temp['template_text_color'] = $t['template_text_color'];
			$temp['template_total_score_box_color'] = $t['template_total_score_color_box'];
			$temp['template_users_score_box_color'] = '#566473';
			$temp['template_high_score_color'] = $t['template_scores_colors']['high_score'];
			$temp['template_medium_score_color'] = $t['template_scores_colors']['medium_score'];
			$temp['template_low_score_color'] = $t['template_scores_colors']['low_score'];
			$temp['template_pros_label_color'] = $t['template_pros_settings']['label_color'];
			$temp['template_pros_label_font_size'] = $t['template_pros_settings']['label_size'];
			$temp['template_pros_text_font_size'] = $t['template_pros_settings']['text_size'];
			$temp['template_cons_label_color'] = $t['template_cons_settings']['label_color'];
			$temp['template_cons_label_font_size'] = $t['template_cons_settings']['label_size'];
			$temp['template_cons_text_font_size'] = $t['template_cons_settings']['text_size'];
			$temp['template_total_score_label'] = $t['template_total_score_label'];
			$temp['template_users_score_label'] = 'Users Score';
			$temp['template_pros_label'] = $t['template_pros_settings']['label'];
			$temp['template_cons_label'] = $t['template_cons_settings']['label'];
			$temp['template_message_to_rate'] = 'Leave your rating';
			$temp['template_message_to_rate_login'] = 'Login to rate';
			$temp['template_success_message'] = 'Thank you for your rating';
			$temp['template_failure_message'] = 'Error during rate process';
			$temp['template_rate_image'] = RWP_PLUGIN_URL . 'public/assets/images/rating-star.png';

			$templates[ $temp['template_id'] ] = $temp;
		}

		// Save new templates
		$res = update_option('rwp_templates', $templates);

		// - - - Reviews - - -
		global $wpdb;

		// Get posts ids that contain reviews
		$post_meta = $wpdb->get_results( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'rwp_reviews';", ARRAY_A );
		$posts = array();
		foreach ($post_meta as $p)
			$posts[] = $p['post_id'];

		// Loop all posts
		foreach ($posts as $post_id) {

		 	$revs = array();

		 	// Get post reviews
		 	$reviews = get_post_meta( $post_id, 'rwp_reviews', true );

		 	if ( !empty( $reviews ) ) { // Check if there are reviews

		 		// Store old reviews
		 		update_post_meta( $post_id, 'rwp_old_reviews', $reviews );

		 		// Loop all reviews
		 		foreach ($reviews as $review) {

		 			$review_id = $review['review_id'];
		 			$revs[ $review_id ]['review_id'] = $review['review_id'];
		 			$revs[ $review_id ]['review_title'] = $review['review_title'];
		 			$revs[ $review_id ]['review_template'] = $review['review_template'];
		 			$revs[ $review_id ]['review_scores'] = $review['review_items'];
		 			$revs[ $review_id ]['review_pros'] = $review['review_good_stuff'];
		 			$revs[ $review_id ]['review_cons'] = $review['review_bad_stuff'];
		 		}

		 		// Save updated reviews
		 		update_post_meta( $post_id, 'rwp_reviews', $revs );
		 	}
		 }

		update_option('rwp_restore', 1);

		die( json_encode( array('msg' => __( 'Restore completed', 'reviewer') ) ) );
	}

	public function display_plugin_admin_page()
	{
		?>
		<div class="wrap">
			<h2><?php _e( 'Preferences', $this->plugin_slug ); ?></h2>
			<?php settings_errors(); ?>
			<?php if($this->is_licensed()): ?>

			<form method="post" action="options.php" id="rwp-pref-form">
			<?php
				settings_fields( $this->option_name );
				do_settings_sections( $this->menu_slug );
				submit_button();
			?>
			</form>

			<!-- <hr/>
			<h3>Restore Data</h3>
			<p class="desctiprion"><?php _e('Important: if you already used a previous version of Reviewer Plugin please click the button below to restore the compatibility with the new version. Plaese backup your blog database before restoring data.', $this->plugin_slug); ?></p>
			<input id="rwp-restore-data-btn" type="button" class="button" value="<?php _e('Restore Data', $this->plugin_slug); ?>" data-confirm-msg="<?php _e('Do you want to continue?', $this->plugin_slug); ?>">
			<img class="rwp-loader rwp-restore" src="<?php echo admin_url(); ?>images/spinner.gif" alt="loading" />

			<div id="rwp-restore-data-notification" class="updated settings-error">
				<p><strong></strong></p>
			</div> -->
			<?php else:
            	$this->license_notice();
        	endif;?>

			<?php //RWP_Reviewer::pretty_print(  $this->option_value ); ?>
		</div><!--/wrap-->
		<?php
	}

	public function register_page_fields()
	{
		// Add sections
		$sections = array( 'rwp_preferences_users_rating_section' => __( 'Users Ratings', $this->plugin_slug), 'rwp_preferences_global_section' => __( 'Global', $this->plugin_slug), );

		foreach ( $sections as $section_id => $section_title )
			add_settings_section( $section_id, $section_title, array( $this, 'display_section'), $this->menu_slug );

		// Add Fields
		foreach ($this->preferences_fields as $field_id => $field) {

			if( 'preferences_authorization_roles' == $field_id ) {
				continue;
			}

			// Get selected value for the field
			$selected = ( isset( $this->option_value[ $field_id ] ) && ! empty( $this->option_value[ $field_id ] ) ) ? $this->option_value[ $field_id ] : $this->preferences_fields[ $field_id ]['default'];

			add_settings_field( $field_id, $field['title'], array( $this, $field_id . '_cb' ), $this->menu_slug, $field['section'], array( 'field_id' => $field_id, 'selected' => $selected, 'default' => $field['default'] ) );
		}

		register_setting( $this->option_name, $this->option_name, array( $this, 'validate_fields' ) );
	}

	public static function get_instance()
	{
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function display_section()
	{
		// Do Nothing!
	}

	public function validate_fields( $fields )
	{
		$valids = array();

		//RWP_Reviewer::pretty_print($fields); //flush(); die();

		foreach ($this->preferences_fields as $field_id => $field) {

			$default = $this->preferences_fields[ $field_id ]['default'];

			if( $field_id == 'preferences_user_review_managers' && !isset( $fields[ 'preferences_user_review_managers' ] ) ) {
				$fields[ $field_id ] = array();
			}

			if( $field_id == 'preferences_rosu' || $field_id == 'preferences_authorization_roles' ) {
				continue;
			}

			switch ( $field_id ) {

				case 'preferences_users_reviews_per_page':
					$num =  intval( $fields[ $field_id ] );
					$valids[ $field_id ] = ( $num >= 1 && $num <= 50 ) ? $num : $default;
					break;

				case 'preferences_rating_title_limits':
				case 'preferences_rating_comment_limits':

					if( is_array( $fields[ $field_id ] ) && isset( $fields[ $field_id ]['min'] ) && isset( $fields[ $field_id ]['max'] ) ) {

						$min = ( is_numeric( $fields[ $field_id ]['min'] ) ) ? intval( $fields[ $field_id ]['min'] ) : 0;
						$max = ( is_numeric( $fields[ $field_id ]['max'] ) ) ? intval( $fields[ $field_id ]['max'] ) : 99999;

						if( $min <= $max ) {

							$max = ( $max > $this->to_inf ) ? 'inf' : $max;
							$min = ( $min < 0 ) ? 0 : $min;

							$valids[ $field_id ] = $min .'-'. $max;

						} else {
							$valids[ $field_id ] = $default;
						}

					} else {
						$valids[ $field_id ] = $default;
					}
					break;


				case 'preferences_notification_email':

					$email = trim( $fields[ $field_id ] );
					if( empty( $email ) && intval( $valids['preferences_notification'] ) <= 0 ) {
						$valids[ $field_id ] = '';
						break;
					}

					if ( ! is_email( $email ) ) {

						add_settings_error( $this->option_name, 'rwp-pref-notification', __( 'Please, type a valid email address', $this->plugin_slug), 'update-nag' );
						$valids[ $field_id ] = $default;
						break;
					}

					$valids[ $field_id ] = $fields[ $field_id ];
					break;

				case 'preferences_rating_allow_zero':
				case 'preferences_users_reviews_human_date_format':
				case 'preferences_sameas':
				case 'preferences_numeric_rating_in_user_review':

					if ( isset( $fields[ $field_id ] ) ) {
						$valids[ $field_id ] = 'yes';
					} else {
						$valids[ $field_id ] = 'no';
					}
					break;

				case 'preferences_users_reviews_captcha':

					if ( !isset( $fields[ $field_id ] ) || !is_array( $fields[ $field_id ] ) ) {
						$valids[ $field_id ] = $default;
						break;
					}

					$value = $fields[ $field_id ];
				    $enabled    = isset( $value['enabled'] ) ? true : false;
			        $site_key   = isset( $value['site_key'] ) ? trim( $value['site_key'] ) : '';
			        $secret_key = isset( $value['secret_key'] ) ? trim( $value['secret_key'] ) : '';
			        $site_key   = esc_sql( esc_html( $site_key ) );
					$secret_key = esc_sql( esc_html( $secret_key ) );
					$exclude_posts = isset( $value['exclude_posts'] ) ? trim( $value['exclude_posts'] ) : '';

			        if( $enabled && ( empty( $site_key ) ||  empty( $secret_key ) ) ) {
						add_settings_error( $this->option_name, 'rwp-pref-captcha', __( 'Site key and secret key must be filled', $this->plugin_slug), 'update-nag' );
			            $valids[ $field_id ] = $default;
			            break;
					}

					$valid_ids = array();
					if( !empty($exclude_posts) ) {
						$ids = explode(',', $exclude_posts);
						if( is_array( $ids ) ) {
							foreach ($ids as  $id) {
								if( is_numeric($id)) {
									$valid_ids[] = intval(trim($id));
								}
							}
						}
					}

			        $valids[ $field_id ] =  array(
			            'enabled'       => $enabled,
			            'site_key'      => $site_key,
						'secret_key'    => $secret_key,
						'exclude_posts' => $valid_ids,
			        );
					break;

				case 'preferences_custom_login_link' :

					$custom_link = trim( $fields[ $field_id ] );
					if( empty( $custom_link ) ) {
						$valids[ $field_id ] = '';
						break;
					}

					if (filter_var( $fields[ $field_id ], FILTER_VALIDATE_URL) === FALSE ) {
					    $valids[ $field_id ] = $default;
						add_settings_error( $this->option_name, 'rwp-pref-custom-url', __( 'Invalid URL for the custom login link', $this->plugin_slug), 'update-nag' );
					} else {
						$valids[ $field_id ] = $fields[ $field_id ];
					}
					break;

				case 'preferences_user_review_verified_badge' :
					if( !isset( $fields[ $field_id ] ) ) {
						$valids[ $field_id ] = $default;
						break;
					}
					$badge = $fields[ $field_id ];
					$label = isset( $badge['label'] ) ? trim( $badge['label'] ) : $default['label'];
					$label = !empty( $badge['label'] ) ? RWP_Reviewer::sanitizeText( $badge['label'] ) : $default['label'];
        			$color = ( isset( $badge['color'] ) && preg_match( '/^#[a-f0-9]{6}$/i', $badge['color'] ) ) ? $badge['color'] : $default['color'];

        			$valids[ $field_id ] = compact( 'label', 'color' );
					break;

				case 'preferences_user_review_managers':
					$propertyId = $field_id;
					$property = $this->preferences_fields[ $field_id ];
					$value = isset( $fields[ $field_id ] ) ? $fields[ $field_id ] : array();
					$valids[ $field_id ] = $this->validate_preferences_user_review_managers( compact( 'propertyId', 'property', 'value' ));
					break;

				case 'preferences_user_review_images':
					$fieldKey = $field_id;
					$default = $this->preferences_fields[ $field_id ]['default'];
					$field = isset( $fields[ $field_id ] ) ? $fields[ $field_id ] : array();
					$valids[ $field_id ] = $this->validate_preferences_user_review_images( compact( 'fieldKey', 'default', 'field' ));
					break;

				case 'preferences_rosu':
					// Nope.
					break;

				case 'preferences_authorization':
					$value = $fields[ $field_id ];
					$default = $this->preferences_fields[ $field_id ]['default'];
					$roles = isset( $fields['preferences_authorization_roles'] ) ? $fields['preferences_authorization_roles'] : array();

					if( !array_key_exists( $value, $this->preferences_fields[ $field_id ]['options'] ) ) {
						$valids[ $field_id ] = $default;
						$valids[ 'preferences_authorization_roles' ] = array();
						break;
					}

					if( $value != 'roles' ) {
						$valids[ $field_id ] = $value;
						$valids[ 'preferences_authorization_roles' ] = array();
						break;
					}

					if( !is_array( $roles ) || empty( $roles ) ) {
						$valids[ $field_id ] = $default;
						$valids[ 'preferences_authorization_roles' ] = array();
						break;
					}

					global $wp_roles;
					if ( ! isset( $wp_roles ) ) {
						$wp_roles = new WP_Roles();
					}
					$wproles = array_keys( $wp_roles->roles );

					$vroles = array();
					foreach( $wproles as $role ) {
						if( in_array( $role, $roles, true )  ) {
							$vroles[] = $role;
						}
					}

					if( empty( $vroles ) ) {
						$valids[ $field_id ] = $default;
						$valids[ 'preferences_authorization_roles' ] = array();
						break;
					}

					$valids[ $field_id ] = $value;
					$valids[ 'preferences_authorization_roles' ] = $vroles;
					break;

				case 'preferences_sharing_networks':
				case 'preferences_nofollow':
				case 'preferences_post_types':
					if( !isset( $fields[ $field_id ] ) || !is_array( $fields[ $field_id ] ) ) {
						$valids[ $field_id ] = $default;
					} else {
						foreach ( $fields[ $field_id ] as $post_type) {
							$valids[ $field_id ][] = esc_sql( esc_html( $post_type ) );
						}
					}
					break;

				default:

					if( is_array( $fields[ $field_id ] ) ) {
						foreach ( $fields[ $field_id ] as $post_type)
							$valids[ $field_id ][] = esc_sql( esc_html( $post_type ) );
					}
					else {
						$valids[ $field_id ] = wp_kses( $fields[ $field_id ], array() );
					}
					break;
			}
		}

		//RWP_Reviewer::pretty_print($valids); flush(); die();

		return $valids;
	}

	protected function validate_preferences_user_review_managers( $args )
    {
        extract( $args ); // $propertyId, $property, $value

        $default = $this->preferences_fields[ $propertyId ]['default'];

        if ( !is_array( $value ) ) {
            $value = array();
        }

        // Query wp roles
        global $wp_roles;
        if ( ! isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles();
        }
        // Get the role names
        $roles = $wp_roles->get_names();

        // Unset the admin role. An admin can not be disabled
        if( isset( $roles['administrator'] ) ) {
            unset( $roles['administrator'] );
        }
        $roles_ids = array_keys( $value );
        $valids = array();
        // Loop the roles
        foreach ( $roles as $type => $label ) {
            $role       = get_role( $type );
            // Check if the i-role needs the new capability
            $need_cap   = in_array( $type, $value );
            // Check if the the i-role already has the new capability
            $has_cap    = ( isset( $role->capabilities[ $this->capManageReviews ] ) && $role->capabilities[ $this->capManageReviews ] == 1 );

            // If the i-role needs the new cap and it already has new cap, then just add it to the list and continue.
            if( $need_cap && $has_cap ) {
                $valids[] = $type;
                continue;
            }

            // If the i-role needs the new cap but it has not the new cap, then add the new cap to the role and add it to the list.
            if( $need_cap && !$has_cap ) {
                $role->add_cap( $this->capManageReviews );
                $valids[] = $type;
                continue;
            }

            // If the i-role does not need the new cap but it already has it, then just remove the new cap.
            if( !$need_cap && $has_cap ) {
                $role->remove_cap( $this->capManageReviews );
                continue;
            }
        }

        return $valids;
    }

   	protected function validate_preferences_user_review_images( $args )
    {
        extract( $args ); // $fieldKey, $field, // $default

        $valid = array();
        // field_enabled
        $valid['field_enabled'] = isset( $field['field_enabled'] );

        // field_placeholder
        $f = 'field_placeholder';
        $placeholder = isset( $field[ $f ] ) ? trim( $field[ $f ] ) : '';
        $valid[ $f ] = !empty( $placeholder ) ? RWP_Reviewer::sanitizeText( $placeholder ) : $default[ 'field_placeholder' ];

        // field_bound
        $f = 'field_bound';
        $valid[ $f ] = isset( $field[ $f ] ) && intval( $field[ $f ] ) > 0 ? intval( $field[ $f ] ) : $default[ $f ];

        // field_bound
        $f = 'field_min';
        $valid[ $f ] = isset( $field[ $f ] ) && intval( $field[ $f ] ) >= 0 ? intval( $field[ $f ] ) : $default[ $f ];

        // Check integrity of field_bound and field_min
        $vmin = $valid['field_min'];
        $vmax = $valid['field_bound'];
        if( $vmin > $vmax ) {
        	$valid[ $f ] = $vmax;
        }

        // field_size
        $f = 'field_size';
        $upladLimit = RWP_Reviewer::getUploadLimit();
        $imageSize = isset( $field[ $f ] ) ? floatval( $field[ $f ] ) : $default[ $f ];
        $valid[ $f ] =  ( $imageSize <= $upladLimit && $imageSize > 0 ) ? $imageSize : $default[ $f ];

        // field_dim
        $f = 'field_dim';
        $dim 	= ( isset( $field[ $f ] ) && is_array( $field[ $f ] ) && isset( $field[ $f ]['width'] ) && isset( $field[ $f ]['height'] ) ) ? $field[ $f ] : $default[ $f ];
        $width 	= intval( $dim['width'] ) > 0 ? intval( $dim['width'] ) : $default[ $f ]['width'];
        $height = intval( $dim['height'] ) > 0 ? intval( $dim['height'] ) : $default[ $f ]['height'];
        $valid[ $f ] = array( 'width' => $width, 'height' => $height );

        return $valid;
    }

	public static function get_preferences_fields()
	{
		$plugin_slug = 'reviewer';
		 return array(

            'preferences_user_review_managers' => array(
                'title'         => __('User Review Managers', $plugin_slug),
                'description'   => __('Choose which WordPress Roles can manage user reviews. Administrators can always manage user reviews.', $plugin_slug),
                'default'       => array(),
                'section'       => 'rwp_preferences_users_rating_section',
            ),

			'preferences_authorization' => array(
				'title' 	=> __( 'Rating Authorization', $plugin_slug ),
				'options' 	=> array(
					'all' 		=> __( 'All Users', $plugin_slug ),
					'logged_in' => __( 'Logged in Users only', $plugin_slug ),
					'roles'		=> __('Specific Roles only', $plugin_slug),
					'disabled'	=> __( 'Disabled', $plugin_slug )
				),
				'default'	=> 'all',
				'section' 	=> 'rwp_preferences_users_rating_section',
			),

			'preferences_authorization_roles' => array(
				'title' 	=> __( 'Rating Authorization Roles', $plugin_slug ),
				'options' 	=> array(),
				'default'	=> array(),
				'section' 	=> 'rwp_preferences_users_rating_section',
			),

			'preferences_rating_mode' => array(
				'title' 	=> __( 'Rating Mode', $plugin_slug ),
				'options' 	=> array(
					'full_five_stars' 	=> __( 'Single Criterion Rating with stars', $plugin_slug ),
					'five_stars' 		=> __( '5 Stars Rating', $plugin_slug ),
					'full'		 		=> __( 'Single Criterion Rating with sliders', $plugin_slug ),
				),
				'default'	=> 'full_five_stars',
				'section' 	=> 'rwp_preferences_users_rating_section',
			),

			'preferences_rating_limit' => array(
				'title' 	=> __( 'Rating Limit', $plugin_slug ),
				'options' 	=> array(
					'single' 	=> __( 'Users can leave one review only per Box', $plugin_slug ),
					'unlimited'	=> __( 'Users can leave an unlimited number of reviews per Box', $plugin_slug ),
				),
				'default'	=> 'single',
				'section' 	=> 'rwp_preferences_users_rating_section',
			),

			'preferences_rating_before_appears' => array(
				'title' 	=> __( 'Before a rating appears', $plugin_slug ),
				'options' 	=> array(
					'nothing' 	=> __( 'Rating does not need moderation', $plugin_slug ),
					'pending'	=> __( 'Rating must be manually approved', $plugin_slug ),
				),
				'default'	=> 'nothing',
				'section' 	=> 'rwp_preferences_users_rating_section',
			),

			'preferences_rating_title_limits' => array(
				'title' 		=> __( 'User Review Title Limits', $plugin_slug ),
				'default'		=> '0-inf',
				'description' 	=> __( 'Defines the minimum and maximum number of characters for User Review Title', $plugin_slug ),
				'section' 		=> 'rwp_preferences_users_rating_section',
			),

			'preferences_rating_comment_limits' => array(
				'title' 		=> __( 'User Review Comment Limits', $plugin_slug ),
				'default'		=> '0-inf',
				'description' 	=> __( 'Defines the minimum and maximum number of characters for User Review Comment', $plugin_slug ),
				'section' 		=> 'rwp_preferences_users_rating_section',
			),

			'preferences_users_reviews_per_page' => array(
				'title' 		=> __( 'Users Reviews to show', $plugin_slug ),
				'default'		=> 3,
				'description' 	=> __( 'Define the number of users reviews to show per page inside the reviews box', $plugin_slug ),
				'section' 		=> 'rwp_preferences_users_rating_section',
			),

			'preferences_rating_allow_zero' => array(
				'title' 		=> __( 'Allow zero score in users rating', $plugin_slug ),
				'default'		=> 'yes',
				'description' 	=> __( 'By checking the checkbox the score with zero value will be allowed inside user rating', $plugin_slug ),
				'section' 		=> 'rwp_preferences_users_rating_section',
			),

			'preferences_sharing_networks' => array(
				'title' 		=> __( 'Users Reviews Sharing', $plugin_slug ),
				'description' 	=> __( 'Share user review via the follwing networks', $plugin_slug ),
				'default'	=> array( 'facebook', 'twitter', 'google', 'email', 'link' ),
				'options' 	=> array(
					'facebook' 	=> __( 'Facebook', $plugin_slug ),
					'twitter' 	=> __( 'Twitter', $plugin_slug ),
					'google' 	=> __( 'Google+', $plugin_slug ),
					'email' 	=> __( 'Email', $plugin_slug ),
					'link' 		=> __( 'Standard Link', $plugin_slug ),
				),
				'section' 	=> 'rwp_preferences_users_rating_section',
			),

			'preferences_users_reviews_captcha' => array(
                'title'         => __( 'Users Reviews Captcha', $plugin_slug ),
                'description'   => __( 'Enable Google reCaptcha (Secure Code) for users reviews', $plugin_slug ),
                'default'       => array(
                    'enabled'    => false,
                    'site_key'   => '',
					'secret_key' => '',
					'exclude_posts' => array(),
                ),
                'section'       => 'rwp_preferences_users_rating_section',
                'type'          => 'captcha',
            ),

			'preferences_step' => array(
				'title' 	=> __( 'Scores Step', $plugin_slug ),
				'options' 	=> array( 1, .5, .1, .05, .01 ),
				'default'	=> 0.5,
				'section' 	=> 'rwp_preferences_global_section',
			),

			'preferences_nofollow' => array(
				'title' 	=> __( 'Nofollow Attribute', $plugin_slug ),
				'options' 	=> array(
					'box_image' 		=> __( 'Reviews Box Image Link', $plugin_slug ),
					'box_custom_links' 	=> __( 'Reviews Box Custom Links', $plugin_slug ),
				),
				'default'	=> array(),
                'description'   => sprintf(__( 'Add the %s attribute to the following links of reviews box', $plugin_slug ), '<em>rel="nofollow"</em>'),
				'section' 	=> 'rwp_preferences_global_section',
			),

			'preferences_sameas' => array(
				'title' 		=> __( 'Enable "sameAs" property', $plugin_slug ),
				'default'		=> 'no',
				'description' 	=> __( 'By checking the checkbox the plugin will add the "sameAs" property to Google Rich Snippets', $plugin_slug ),
				'section' 		=> 'rwp_preferences_global_section',
			),

			'preferences_post_types' => array(
				'title' 	=> __( 'Enable Reviewer Plugin inside following Post Types', $plugin_slug ),
				'default'	=> array( 'post' ),
				'section' 	=> 'rwp_preferences_global_section',
			),

			'preferences_custom_login_link' => array(
				'title' 		=> __( 'Custom Login URL', $plugin_slug ),
				'default'		=> '',
				'description' 	=> __( 'Define the custom login url for reviews boxes', $plugin_slug ),
				'section' 		=> 'rwp_preferences_global_section',
			),

			'preferences_custom_css' => array(
				'title' 		=> __( 'Custom CSS Rules', $plugin_slug ),
				'default'		=> '',
				'section' 		=> 'rwp_preferences_global_section',
				'description' 	=> __( 'You can define CSS rules for customizing the Reviewer plugin layout', $plugin_slug ),
			),

			'preferences_notification' => array(
				'title' 	=> __( 'E-mail me whenever', $plugin_slug ),
				'options' 	=> array(
					'1'		=> __( 'Anyone posts a rating', $plugin_slug ),
					'3'		=> __( '3 ratings have been posted', $plugin_slug ),
					'5'		=> __( '5 ratings have been posted', $plugin_slug ),
					'10'	=> __( '10 ratings have been posted', $plugin_slug ),
					'50'	=> __( '50 ratings have been posted', $plugin_slug ),
					'0' 	=> __( 'No, thanks. I don\'t want to receive notifications', $plugin_slug ),
				),
				'default'	=> '0',
				'section' 	=> 'rwp_preferences_users_rating_section',
			),

			'preferences_notification_email' => array(
				'title' 	=> __( 'Send notification to', $plugin_slug ),
				'default'	=> '',
				'section' 	=> 'rwp_preferences_users_rating_section',
				'description' 	=> __( 'Insert a valid e-mail address to send the notification about new users ratings. Just for testing... Press the button below to receive a demo notification. If the email is not in inbox, check the Spam folder', $plugin_slug ),

			),

			'preferences_users_reviews_human_date_format' => array(
				'title' 		=> __( 'Format user review date', $plugin_slug ),
				'default'		=> 'no',
				'description' 	=> __( 'By checking the checkbox the user review date will be converted in a human readable format, such as "1 hour ago", "5 mins ago", "2 days ago"', $plugin_slug ),
				'section' 		=> 'rwp_preferences_users_rating_section',
			),

			'preferences_numeric_rating_in_user_review' => array(
				'title' 		=> __( 'Numeric rating in user reviews', $plugin_slug ),
				'default'		=> 'no',
				'description' 	=> __( 'By checking the checkbox a numeric rating will be shown in user review', $plugin_slug ),
				'section' 		=> 'rwp_preferences_users_rating_section',
			),

			'preferences_user_review_verified_badge' => array(
				'title' 		=> __( 'Verified Badge', $plugin_slug ),
				'default'		=> array(
					'label' => __( 'Verified', $plugin_slug ),
					'color' => '#E91E63',
				),
				'description' 	=> __( 'Define label and color for the verified badge of a user review', $plugin_slug ),
				'section' 		=> 'rwp_preferences_users_rating_section',
			),
			'preferences_user_review_images' => array(
				'title' 		=> __( 'User Review Images', $plugin_slug ),
				'default'		=> array(
					'field_enabled'     => true,
                    'field_placeholder' => __('Drag and drop your images for the review', $plugin_slug),
                    'field_min'			=> 0,
                    'field_bound'       => 3,
                    'field_size'        => 0.5,
				    'field_dim'         => array( 'width' => 60, 'height' => 60 ),
				),
				'description' 	=> __( 'Define the sittings for user review image attachments', $plugin_slug ),
				'section' 		=> 'rwp_preferences_users_rating_section',
			),
			'preferences_rosu' => array(
				'title' 		=> __( 'Reviews of Single User', $plugin_slug ),
				'default'		=> '',
				'description' 	=> '',
				'section' 		=> 'rwp_preferences_users_rating_section',
			),
		);
	}

/*----------------------------------------------------------------------------*
 * Callbacks for form fields
 *----------------------------------------------------------------------------*/

	public function preferences_authorization_cb( $args )
	{
		global $wp_roles;
		extract( $args );

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
		$roles = $wp_roles->get_names();

		$vroles = isset( $this->option_value[ 'preferences_authorization_roles' ] ) ? $this->option_value[ 'preferences_authorization_roles' ] : array();

		echo '<ul class="rwp-options-ul">';

		foreach ($this->preferences_fields['preferences_authorization']['options'] as $option_id => $option_title) {

			$ck = ( $selected == $option_id ) ? 'checked' : '';

			if( 'roles' == $option_id ) {
				echo '<li>';
					echo '<input id="rwp-option-'. $field_id .'-'. $option_id .'" type="radio" name="'. $this->option_name .'[' . $field_id . ']" value="' . $option_id . '" '. $ck .' /> <label for="rwp-option-'. $field_id .'-'. $option_id .'">' . $option_title . '</label>';

					echo '<ul style="margin: 10px 0 10px 20px">';
					foreach ( $roles as $type => $label ) {
						$ak = ( in_array( $type, $vroles ) ) ? 'checked' : '';
						echo '<li>';
							echo '<input id="rwp-option-wp-roles-'. $field_id .'-'. $type .'" type="checkbox" value="'. $type .'" name="'. $this->option_name .'[preferences_authorization_roles][]" '. $ak .'/>';
							echo '<label for="rwp-option-wp-roles-'. $field_id .'-'. $type .'">'. $label .'</label>';
						echo '</li>';
					}
					echo '</ul>';

				echo '</li>';
			} else {
				echo '<li><input id="rwp-option-'. $field_id .'-'. $option_id .'" type="radio" name="'. $this->option_name .'[' . $field_id . ']" value="' . $option_id . '" '. $ck .' /> <label for="rwp-option-'. $field_id .'-'. $option_id .'">' . $option_title . '</label></li>';
			}
		}

		echo '</ul>';
	}


	public function preferences_rating_mode_cb( $args )
	{
		extract( $args );

		echo '<ul class="rwp-options-ul">';

		foreach ($this->preferences_fields['preferences_rating_mode']['options'] as $option_id => $option_title) {

			$ck = ( $selected == $option_id ) ? 'checked' : '';

			echo '<li><input type="radio" name="'. $this->option_name .'[' . $field_id . ']" value="' . $option_id . '" '. $ck .' /> <label>' . $option_title . '</label></li>';
		}

		echo '</ul>';
	}

	public function preferences_rating_limit_cb( $args )
	{
		extract( $args );

		echo '<ul class="rwp-options-ul">';

		foreach ($this->preferences_fields['preferences_rating_limit']['options'] as $option_id => $option_title) {

			$ck = ( $selected == $option_id ) ? 'checked' : '';

			echo '<li><input type="radio" name="'. $this->option_name .'[' . $field_id . ']" value="' . $option_id . '" '. $ck .' /> <label>' . $option_title . '</label></li>';
		}

		echo '</ul>';
	}

	public function preferences_rating_before_appears_cb( $args )
	{
		extract( $args );

		echo '<ul class="rwp-options-ul">';

		foreach ($this->preferences_fields['preferences_rating_before_appears']['options'] as $option_id => $option_title) {

			$ck = ( $selected == $option_id ) ? 'checked' : '';

			echo '<li><input type="radio" name="'. $this->option_name .'[' . $field_id . ']" value="' . $option_id . '" '. $ck .' /> <label>' . $option_title . '</label></li>';
		}

		echo '</ul>';
	}

	public function preferences_step_cb( $args )
	{
		extract( $args );

		echo '<ul class="rwp-options-ul">';

		foreach ($this->preferences_fields['preferences_step']['options'] as $option_id => $option_value) {

			$ck = ( $selected == $option_value ) ? 'checked' : '';

			echo '<li><input type="radio" name="'. $this->option_name .'[' . $field_id . ']" value="' . $option_value . '" '. $ck .' /> <label>' . $option_value . '</label></li>';
		}

		echo '</ul>';
	}

	public function preferences_post_types_cb( $args )
	{
		extract( $args );

		$post_types = get_post_types();

		echo '<ul class="rwp-post-type-ul">';

		foreach ($post_types as $type) {

			$ck = ( in_array( $type, $selected ) ) ? 'checked' : '';
			$post_type = get_post_type_object( $type );
            $label   = $post_type->labels->name;

			echo '<li><input type="checkbox" name="'. $this->option_name .'[' . $field_id . '][]" value="' . $type . '" '. $ck .' /> <label>'. $label . ' - <em style="color:#666">' . $type . '</em></label></li>';
		}

		echo '</ul>';
	}

	public function preferences_nofollow_cb( $args )
	{
		extract( $args );

		$links = $this->preferences_fields[ $field_id ]['options'];

		echo '<p>'. $this->preferences_fields[ $field_id ]['description'] .'</p>';
		echo '<ul class="rwp-post-type-ul">';
		foreach ($links as $key => $name) {
			$ck = ( in_array( $key, $selected ) ) ? 'checked' : '';

			echo '<li><input type="checkbox" name="'. $this->option_name .'[' . $field_id . '][]" value="' . $key . '" '. $ck .' /> <label>' . $name . '</label></li>';
		}
		echo '</ul>';
	}

	public function preferences_sharing_networks_cb( $args )
	{
		extract( $args );

		$networks = $this->preferences_fields[ $field_id ]['options'];

		echo '<p>'. $this->preferences_fields[ $field_id ]['description'] .'</p>';
		echo '<ul class="rwp-post-type-ul">';
		foreach ($networks as $key => $name) {
			$ck = ( in_array( $key, $selected ) ) ? 'checked' : '';

			echo '<li><input type="checkbox" name="'. $this->option_name .'[' . $field_id . '][]" value="' . $key . '" '. $ck .' /> <label>' . $name . '</label></li>';
		}
		echo '</ul>';
	}

	public function preferences_rating_title_limits_cb( $args )
	{
		extract( $args );

		$range 		= explode( '-', $selected );
		$defaults 	= explode( '-', $default );

		$max_r = ( $range[1] == 'inf' ) ? 'no-limit' : $range[1];
		$max_d = ( $defaults[1] == 'inf' ) ? 'no-limit' : $defaults[1];

		echo '<div class="rwp-slider-wrap">';

			echo '<input type="text" class="rwp-min" name="'. $this->option_name .'[' . $field_id . '][min]" value="" placeholder="'. $defaults[0] .'"/>';
			echo '<div class="rwp-slider-limits" data-min="'. $range[0] .'" data-max="'. $range[1] .'" ></div>';
			echo '<input type="text" class="rwp-max" name="'. $this->option_name .'[' . $field_id . '][max]" value="" placeholder="'. $max_d .'"/>';

		echo '</div><!-- /slider-wrap -->';

		echo '<p class="description">'. $this->preferences_fields[ $field_id ]['description'].'</p>';
	}

	public function preferences_rating_comment_limits_cb( $args )
	{
		extract( $args );

		$range 		= explode( '-', $selected );
		$defaults 	= explode( '-', $default );

		$max_r = ( $range[1] == 'inf' ) ? 'no-limits' : $range[1];
		$max_d = ( $defaults[1] == 'inf' ) ? 'no-limits' : $defaults[1];

		echo '<div class="rwp-slider-wrap">';

			echo '<input type="text" class="rwp-min" name="'. $this->option_name .'[' . $field_id . '][min]" value="" placeholder="'. $defaults[0] .'"/>';
			echo '<div class="rwp-slider-limits" data-min="'. $range[0] .'" data-max="'. $range[1] .'" ></div>';
			echo '<input type="text" class="rwp-max" name="'. $this->option_name .'[' . $field_id . '][max]" value="" placeholder="'. $max_d .'"/>';

		echo '</div><!-- /slider-wrap -->';

		echo '<p class="description">'. $this->preferences_fields[ $field_id ]['description'].'</p>';
	}

	public function preferences_rating_allow_zero_cb( $args )
	{
		extract( $args );

		if( $selected == 'yes' ) {
			$ck = 'checked';
			$value = 'yes';
		} else {
			$ck = '';
			$value = 'no';
		}

		echo '<input type="checkbox" name="'. $this->option_name .'['. $field_id .']" value="'. $value .'" '.$ck.'/>';
		echo '<span class="description">'. $this->preferences_fields[ $field_id ]['description'].'</span>';
	}

	public function preferences_users_reviews_human_date_format_cb( $args )
	{
		extract( $args );

		if( $selected == 'yes' ) {
			$ck = 'checked';
			$value = 'yes';
		} else {
			$ck = '';
			$value = 'no';
		}

		echo '<input type="checkbox" name="'. $this->option_name .'['. $field_id .']" value="'. $value .'" '.$ck.'/>';
		echo '<span class="description">'. $this->preferences_fields[ $field_id ]['description'].'</span>';
	}

	public function preferences_numeric_rating_in_user_review_cb( $args )
	{
		extract( $args );

		if( $selected == 'yes' ) {
			$ck = 'checked';
			$value = 'yes';
		} else {
			$ck = '';
			$value = 'no';
		}

		echo '<input type="checkbox" name="'. $this->option_name .'['. $field_id .']" value="'. $value .'" '.$ck.'/>';
		echo '<span class="description">'. $this->preferences_fields[ $field_id ]['description'].'</span>';
	}

	public function preferences_sameas_cb( $args )
	{
		extract( $args );

		if( $selected == 'yes' ) {
			$ck = 'checked';
			$value = 'yes';
		} else {
			$ck = '';
			$value = 'no';
		}

		echo '<input type="checkbox" name="'. $this->option_name .'['. $field_id .']" value="'. $value .'" '.$ck.'/>';
		echo '<span class="description">'. $this->preferences_fields[ $field_id ]['description'].'</span>';
	}

	public function preferences_custom_login_link_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'['. $field_id .']" value="'. $selected .'" class="regular-text" />';
		echo '<p class="description">'. $this->preferences_fields[ $field_id ]['description'].'</p>';
	}

	public function preferences_custom_css_cb( $args )
	{
		extract( $args );

		echo '<p class="description" style="margin: 0 0 10px 0;">'. $this->preferences_fields[ $field_id ]['description'].'</p>';

		echo '<textarea name="'. $this->option_name .'[' . $field_id . ']" id="rwp-codemirror" cols="30" rows="10">'.$selected.'</textarea>';
	}

	public function preferences_notification_cb( $args )
	{
		extract( $args );

		echo '<ul class="rwp-options-ul">';

		foreach ($this->preferences_fields[ $field_id ]['options'] as $option_id => $option_title) {

			$ck = ( $selected == $option_id ) ? 'checked' : '';

			echo '<li><input type="radio" name="'. $this->option_name .'[' . $field_id . ']" value="' . $option_id . '" '. $ck .' /> <label>' . $option_title . '</label></li>';
		}

		echo '</ul>';
	}

	public function preferences_notification_email_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $selected .'" />';

		echo '<p class="description">'. $this->preferences_fields[ $field_id ]['description'].'</p>';

		echo '<a href="#" id="rwp-notification-btn" class="button">'. __( 'Send Demo Notification', 'reviewer' ) .'</a>';

		echo '<img class="rwp-loader rwp-pref-loader" src="'.admin_url() .'images/spinner.gif" alt="loading" />';
	}

	public function preferences_users_reviews_per_page_cb( $args )
	{
		extract( $args );

		$min = 1;
		$max = 50;

		echo '<div class="rwp-slider-wrap">';

			echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $selected .'" />';
			echo '<div class="rwp-slider-std" data-min="'. $min .'" data-max="'. $max .'" data-val="'. $selected .'" ></div>';

		echo '</div><!-- /slider-wrap -->';

		echo '<p class="description">'. $this->preferences_fields[ $field_id ]['description'].'</p>';
	}

	/**
     * Render captcha
     *
     * @since    4.0.0
     * @access   public
     */
    public function preferences_users_reviews_captcha_cb( $args )
    {

        extract( $args ); // $field_id, $selected, $default
        $value = $selected;

        $enabled    = isset( $value['enabled'] )    ? $value['enabled']     : $default['enabled'];
        $site_key   = isset( $value['site_key'] )   ? $value['site_key']    : $default['site_key'];
		$secret_key = isset( $value['secret_key'] ) ? $value['secret_key']  : $default['secret_key'];
		$exclude_posts = isset( $value['exclude_posts'] ) ? $value['exclude_posts']  : $default['exclude_posts'];

        $checked = $enabled ? 'checked' : '';

        echo '<p class="description rwp-description">'. $this->preferences_fields[ $field_id ]['description'] .'</p>';
        echo '<p class="rwp-input-wrapper">';
            echo '<input id="rwp-enable-captcha" type="checkbox" name="'. $this->option_name .'[' . $field_id . '][enabled]" value="1" '. $checked .'/>';
            echo '<label for="rwp-enable-captcha">'. __('Enable captcha', 'reviewer') .'</label>';
        echo '</p>';

        echo '<p class="rwp-input-wrapper">';
            echo '<label style="min-width: 150px; display: inline-block; vertical-align: middle;">'. __('ReCaptcha Site Key', 'reviewer') .'</label>';
            echo '<input type="text" class="regular-text" name="'. $this->option_name .'[' . $field_id . '][site_key]" value="'. $site_key .'" />';
        echo '</p>';

        echo '<p class="rwp-input-wrapper">';
            echo '<label style="min-width: 150px; display: inline-block; vertical-align: middle;">'. __('ReCaptcha Secret Key', 'reviewer') .'</label>';
            echo '<input type="text" class="regular-text" name="'. $this->option_name .'[' . $field_id . '][secret_key]" value="'. $secret_key .'" />';
		echo '</p>';

		echo '<div class="rwp-input-wrapper" style="margin-top: 30px">';
            echo '<label style="min-width: 150px; display: block; margin-bottom: 5px">'. __('Exclude ReCaptcha JS Lib Import', 'reviewer') .'</label>';
            echo '<input type="text" class="regular-text" name="'. $this->option_name .'[' . $field_id . '][exclude_posts]" value="'. implode(', ', $exclude_posts) .'" placeholder="23, 45, 67, 234"/>';
			echo '<p class="description" style="display: block">'. __('Type the post/page IDs separated by a comma for excluding the import for Google ReCaptcha JS lib', 'reviewer') .'</p>';
		echo '</div>';
    }

    public function preferences_user_review_verified_badge_cb( $args )
    {
        extract( $args ); // $field_id, $selected, $default
        $badge = $selected;
        $label = isset( $badge['label'] ) ? $badge['label'] : '';
        $color = isset( $badge['color'] ) ? $badge['color'] : '';

        echo '<p class="description rwp-description">'. $this->preferences_fields[ $field_id ]['description'] .'</p>';
        echo '<p class="rwp-input-wrapper rwp-badge-field">';
            echo '<label>'. __('Badge Label', 'reviewer') .'</label>';
			echo '<input type="text" name="'. $this->option_name .'['. $field_id .'][label]" value="'. $label .'" placeholder="'. $default['label'] .'"/>';
        echo '</p>';

        echo '<p class="rwp-input-wrapper rwp-badge-field">';
            echo '<label>'. __('Badge Color', 'reviewer') .'</label>';
			echo '<input type="text" name="'. $this->option_name .'['. $field_id .'][color]" value="'. $color .'" class="rwp-color-picker" placeholder="'. $default['color'] .'"/>';
        echo '</p>';
    }

     public function preferences_user_review_managers_cb( $args )
    {
        extract( $args ); // $field_id, $selected, $default
        $value = $selected;

        global $wp_roles;
        if ( ! isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles();
        }
        $roles = $wp_roles->get_names();
        if( isset( $roles['administrator'] ) ) {
            unset( $roles['administrator'] );
        }

        echo '<p class="description">'. $this->preferences_fields[ $field_id ]['description'] .'</p>';
        echo '<ul>';
        foreach ( $roles as $type => $label ) {
            $role = get_role( $type );
            $hasCap = ( isset( $role->capabilities[ $this->capManageReviews ] ) && $role->capabilities[ $this->capManageReviews ] == 1 );
            echo '<li>';
                echo '<input id="rwp-option-'. $field_id .'-'. $type .'" type="checkbox" name="'. $this->option_name .'['. $field_id .'][]" value="'. $type .'" '. ( ( in_array( $type, $value ) && $hasCap ) ? 'checked' : '' ) .'/>';
                echo '<label for="rwp-option-'. $field_id .'-'. $type .'">'. $label .'</label>';
            echo '</li>';
        }
        echo '</ul>';
    }

    public function preferences_user_review_images_cb( $args )
    {
        extract( $args ); // $field_id, $selected, $default
        $value = $selected;

	    echo '<div class="rwp-image-attachment-settings">';
	        // Enable
	        $enabled = ( isset( $value['field_enabled'] ) && $value['field_enabled'] );
	        echo '<p class="rwp-input-wrapper">';
				echo '<input id="rwp-enable-images-input" type="checkbox" name="'. $this->option_name .'['. $field_id .'][field_enabled]" value="'. true .'" '. (( $enabled ) ? 'checked': '').' />';
	            echo '<label for="rwp-enable-images-input">'. __('Enable users to attach images to a review', 'reviewer') .'</label>';
	        echo '</p>';

	        // Placeholder
	        $placeholder = ( isset( $value['field_placeholder'] ) && !empty( $value['field_placeholder'] ) ) ? $value['field_placeholder'] : '';
	        echo '<p class="rwp-input-wrapper">';
	            echo '<label>'. __('Field Placeholder', 'reviewer') .'</label>';
				echo '<input type="text" name="'. $this->option_name .'['. $field_id .'][field_placeholder]" value="'. $placeholder .'" placeholder="'. $default['field_placeholder'] .'"  class="regular-text" />';
	        echo '</p>';

	        // Bound
	        $bound = ( isset( $value['field_bound'] ) && !empty( $value['field_bound'] ) ) ? $value['field_bound'] : '';
	        echo '<p class="rwp-input-wrapper">';
	            echo '<label>'. __('Maximum number of image attachments inside a user review', 'reviewer') .'</label>';
				echo '<input type="number" step="1" min="1" name="'. $this->option_name .'['. $field_id .'][field_bound]" value="'. $bound .'" placeholder="'. $default['field_bound'] .'" />';
	        echo '</p>';

	        // Min
	        $minimum = ( isset( $value['field_min'] ) && !empty( $value['field_min'] ) ) ? $value['field_min'] : '';
	        echo '<p class="rwp-input-wrapper">';
	            echo '<label>'. __('Minimum number of images that a user have to upload for posting his review', 'reviewer') .'</label>';
				echo '<input type="number" step="1" min="0" name="'. $this->option_name .'['. $field_id .'][field_min]" value="'. $minimum .'" placeholder="'. $default['field_min'] .'" />';
	        echo '</p>';

	        // Size
	        $size = ( isset( $value['field_size'] ) && !empty( $value['field_size'] ) ) ? $value['field_size'] : '';
	        echo '<p class="rwp-input-wrapper">';
	            echo '<label>'. __('Maximum size in MB of image attachment inside users review. To increase the maximum value you need to edit the php configuration file of your host', 'reviewer') .'</label>';
				echo '<input type="number" step=".1" min=".1" name="'. $this->option_name .'['. $field_id .'][field_size]" value="'. $size .'" placeholder="'. $default['field_size'] .'" />';
	        	echo '<em>'. sprintf(__('Maximum Size is %d MB', 'reviewer'), RWP_Reviewer::getUploadLimit()) .'</em>';
	        echo '</p>';

	        // Dim
			$dim = ( isset( $value['field_dim'] ) && !empty( $value['field_dim'] ) ) ? $value['field_dim'] : $default['field_dim'];
			echo '<h4>'. __('Thumbnail Size (px)', 'reviewer') .'</h4>';
			echo '<p class="rwp-input-wrapper">';
				echo '<label>'. __('Width', 'reviewer') .'</label>';
				echo '<input type="number" step="1" min="1" name="'. $this->option_name .'['. $field_id .'][field_dim][width]" value="'. $dim['width'] .'" placeholder="'. $default['field_dim']['width'] .'" />';
			echo '</p>';
			echo '<p class="rwp-input-wrapper">';
				echo '<label>'. __('Height', 'reviewer') .'</label>';
				echo '<input type="number" step="1" min="1" name="'. $this->option_name .'['. $field_id .'][field_dim][height]" value="'. $dim['height'] .'" placeholder="'. $default['field_dim']['height'] .'" />';
			echo '</p>';
	    echo '</div>';
    }

	public function preferences_rosu_cb( $args )
    {
        ?>
		<p class="description"><?php printf(__('It could sometimes happen that reviews of a single user, fetched by %s, do not reflect the real one. The Reviewer caches the collection of reviews of a single user. So to fix that issue try to clear the cache by pressing the following button.', 'reviewer'), '[rwp_user_reviews user="..."]')?></p>
		<div id="rwp-clear-rosu-cache">
			<span style="display: block">
				<a href="#" id="rwp-clear-rosu-cache-btn" class="button"><?php _e('Clear Cache')?></a>
				<img class="rwp-loader rwp-pref-loader" src="<?php echo admin_url() ?>images/spinner.gif" alt="loading" />
			</span>
			<p class="rwp-clear-rosu-cache-notice-yes"><span class="dashicons dashicons-yes"></span> <?php _e('The cache was cleared')?></p>
			<p class="rwp-clear-rosu-cache-notice-no"><span class="dashicons dashicons-no"></span> <?php _e('Unable to clear the cache or the cache was already cleared')?></p>
		</div>
		<?php
	}
}
