<?php

/**
 * KnowherePro Child Theme functions and definitions
 *
 */


if ( !function_exists('knowherepro_child_enqueue_styles') ) {

	add_action( 'wp_enqueue_scripts', 'knowherepro_child_enqueue_styles' );

	function knowherepro_child_enqueue_styles() {

		if ( !is_admin() ) {

			$parent_style = 'knowhere-style';
			wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css', array( 'linearicons', 'animate', 'bootstrap', 'linear', 'fontawesome' ) );
			wp_enqueue_style( 'knowhere-child-style', get_stylesheet_directory_uri() . '/style.css', array( $parent_style ) );
 			//wp_enqueue_script('price', 'https://www.mylittlewe.com/wp-content/themes/knowherepro-child/js/price.js');
			if ( is_rtl() ) {
				wp_enqueue_style( 'knowherepro-child-style-rtl', get_stylesheet_directory_uri() . '/rtl.css' );
			}

		}

	}

}

/**
* Will make the Bookings calender default to the month with the first available booking.
*/
add_filter( 'wc_bookings_calendar_default_to_current_date', '__return_false' );
/**
 * Turn all subscribers into Woo Vendors
 */
 
function goach_make_users_vendors( $user_id ) {

	if (!defined('WC_PRODUCT_VENDORS_TAXONOMY')){
		return;
	}

	if (empty($user_id)) {
		wc_add_notice( __( '<strong>ERROR</strong>: Unable to create the vendor account for this user. Please contact the administrator to register your account.', 'localization-domain' ), 'error' );
	}

	$user_data = get_userdata($user_id);
	$username = $user_data->user_login;
	$email = $user_data->user_email;


	// Ensure vendor name is unique
	if ( term_exists( $username, WC_PRODUCT_VENDORS_TAXONOMY ) ) {
		$append     = 1;
		$o_username = $username;

		while ( term_exists( $username, WC_PRODUCT_VENDORS_TAXONOMY ) ) {
			$username = $o_username . $append;
			$append ++;
		}
	}

	// Create the new vendor
	$term = wp_insert_term(
		$username,
		WC_PRODUCT_VENDORS_TAXONOMY,
		array(
			'description' => sprintf( __( 'The vendor %s', 'localization-domain' ), $username ),
			'slug'        => sanitize_title( $username )
		)
	);

	if ( is_wp_error( $return ) ) {
		wc_add_notice( __( '<strong>ERROR</strong>: Unable to create the vendor account for this user. Please contact the administrator to register your account.', 'localization-domain' ), 'error' );
	} else {
		// Update vendor data
		$vendor_data['email'] = $email;
		$vendor_data['paypal'] = $email; // The email used for the account will be used for the payments
		$vendor_data['commission']   = ''; // The commission is 50% for each order
		$vendor_data['admins'][]     = $user_id; // The registered account is also the admin of the vendor
		$vendor_data['enable_bookings']     = 'yes'; // If you want vendors to access "Bookings" menu
		$vendor_data['id_user'] =  $user_id;
		update_term_meta( $term['term_id'], 'vendor_data', apply_filters( 'wcpv_registration_default_vendor_data', $vendor_data ) );

		// change this user's role to pending vendor
		wp_update_user( apply_filters( 'wcpv_registration_default_user_data', array(
			'ID'   => $user_id,
			'role' => 'wc_product_vendors_manager_vendor',
		) ) );

		$post_arr = array(
                'post_title'   => "calendar-".$user_id,
                'post_status'  => 'publish',
                'post_author'  => get_current_user_id(),
                'post_type'  => 'stec_calendar',
                'comment_status' => 'closed',
                'ping_status' => 'closed',
                'post_name' => "calendar-".$user_id,
                
            );
        $calendar_id = wp_insert_post( $post_arr );
        add_post_meta($calendar_id, 'visibility', 'wc_product_vendors_manager_vendor', true);
		add_post_meta($calendar_id, 'color', '#d13061', true);
		add_post_meta($calendar_id, 'icon', 'fa', true);
		add_post_meta($calendar_id, 'timezone', 'Europe/Paris', true);
        add_user_meta( $user_id, '_id_calendar', $calendar_id, true);
        add_user_meta( $user_id, '_id_vendor', $term['term_id'], true);
        add_user_meta( $user_id, '_vendor_name', $username, true);

	}
}
add_action( 'woocommerce_created_customer', 'goach_make_users_vendors', 10, 1 );



/* MODE PRIVE */
function filtering_private_job_listing ( $query_args, $args) {
	$query_args['meta_query'][] = array(
						'key'     => 'is_private',
						'compare'   => 'NOT EXISTS',
						
						
					);

	// This will show the 'reset' link
	add_filter( 'job_manager_get_listings_custom_filter', '__return_true' );

	return $query_args;
}
add_filter( 'job_manager_get_listings', 'filtering_private_job_listing', 10, 2 );

function return_correct_date ($job_date) {

	$new_job_date = str_replace(" h ", ":", $job_date);
	$new_job_date = str_replace("h", ":", $job_date);
	$new_job_date = str_replace(" min", "", $new_job_date);
	$new_job_date = str_replace(" ", "", $new_job_date);

	if (strlen($new_job_date) == 4) {
		$new_job_date = "0".$new_job_date;
	} 

	return $new_job_date;
}

function goach_generate_featured_image($image_url, $post_id) {
    $upload_dir = wp_upload_dir();
    $image_data = file_get_contents($image_url);
    $filename = basename($image_url);
    if (wp_mkdir_p($upload_dir['path']))
      $file = $upload_dir['path'] . '/' . $filename;
    else
      $file = $upload_dir['basedir'] . '/' . $filename;
       file_put_contents($file, $image_data);

      $wp_filetype = wp_check_filetype($filename, null);
    $attachment = array(
      'post_mime_type' => $wp_filetype['type'],
      'post_title' => sanitize_file_name($filename),
      'post_content' => '',
      'post_status' => 'inherit'
    );
    $attach_id = wp_insert_attachment($attachment, $file, $post_id);
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata($attach_id, $file);
    $res1 = wp_update_attachment_metadata($attach_id, $attach_data);
    $res2 = set_post_thumbnail($post_id, $attach_id);
}
function goach_transform_date($date_fr) {
	$months = ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"];
	$months_num = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];
	$date_fr = str_replace(" ", "-", $date_fr);
	$i = 0;
	foreach ($months as $month) {
		$date_fr = str_replace($month, $months_num[$i], $date_fr);
		$i++;
	}

	return $date_fr;

}

function new_post_status( $new_status, $old_status, $post )
{

    if( 'publish' == $new_status && 'publish' != $old_status && $post->post_type == 'job_listing' ) {
    	if (get_post_meta( $post->ID, '_is_private_activite', true ) == "private") {
    		add_post_meta($post->ID, 'is_private', 1, true);
    		$arg = array(
			    'ID' => $post->ID
			);
			wp_update_post( $arg );
    	}
    	

		
    	if (empty(get_post_meta( $post->ID, '_id_product', true ))) {
    		
    		$product_id = wp_insert_post( array(
			    'post_title' => $post->post_title,
			    'post_content' => '',
			    'post_status' => 'publish',
			    'post_type' => "product",
			) );
			wp_set_object_terms( $product_id, 'booking', 'product_type' );
			add_post_meta($post->ID, '_id_product', $product_id, true);
			add_post_meta($post->ID, 'main_image', get_post_thumbnail_id( $post->ID ), true);
			add_post_meta($product_id, '_id_job', $post->ID, true);
			$result=Red_Item::create( array(
					'url'         => get_post_permalink( $product_id ),
					'action_data' => array( 'url' => get_post_permalink( $post->ID ) ),
					'regex'  	  => 0,
					'group_id'    => 1,
					'action_type' => 'url',
					'action_code' => 301,
					'match_type'  => 'url',
					'last_access' => '0000-00-00',
				) );

    	} else {

    		$product_id = get_post_meta( $post->ID, '_id_product', true );

    	}
		
   		goach_generate_featured_image (get_the_post_thumbnail_url($post->ID,'full'), $product_id);
   		
		if( strstr(WP_Job_Manager_Job_Tags::get_job_tag_list($post->ID), "Week-end complet")) { 
		
			$block = 2;
		} else {
			$block = 1;
		} 
    
		$aHours = [];
    	if (get_post_meta( $post->ID, '_type_reservation', true ) == 'recurrente') {
    		$calendar_display = "always_visible";
    		$data_week = get_post_meta( $post->ID, '_job_hours', true );
    		
    		$i = 1;

    		foreach ($data_week as $key => $day) {

    			$a = [];
    			if ($key == "mon") $i = 1;
    			if ($key == "tue") $i = 2;
    			if ($key == "wed") $i = 3;
    			if ($key == "thu") $i = 4;
    			if ($key == "fri") $i = 5;
    			if ($key == "sat") $i = 6;
    			if ($key == "sun") $i = 7;

    			$a['type'] = "time:".$i;
    			if (empty($day[0]['start']) || $day[0]['start'] == "Fermé") {
    				$a['bookable'] = 'no';
    				$a['priority'] = 1;
    				$a['from'] = "00:00";
    				$a['to'] = "00:00";
    			} else {
    				$a['bookable'] = 'yes';
    				$a['priority'] = $i;
    				$a['from'] = return_correct_date($day[0]['start'], "start");
    				$a['to'] = "23:59";
    			}
    			$aHours[] = $a;
    			
    		}

    	} else {
    		
    		$calendar_display = "always_visible";
    		$a['type'] = "custom";
    		$a['bookable'] = "yes";
    		$a['from'] = goach_transform_date(get_post_meta( $post->ID, '_date_fixe', true ));
    		$a['to'] = goach_transform_date(get_post_meta( $post->ID, '_date_fixe', true ));
    		$aHours[] = $a;
    		$date_2 = get_post_meta( $post->ID, '_date_fixe_2', true );
    		if(!empty($date_2)) {
    			$calendar_display = "always_visible";
	    		$a['type'] = "custom";
	    		$a['bookable'] = "yes";
	    		$a['from'] = goach_transform_date(get_post_meta( $post->ID, '_date_fixe_2', true ));
	    		$a['to'] = goach_transform_date(get_post_meta( $post->ID, '_date_fixe_2', true ));
	    		$aHours[] = $a;
    		}
    		$date_3 = get_post_meta( $post->ID, '_date_fixe_3', true );
    		if(!empty($date_3)) {
    			$calendar_display = "always_visible";
	    		$a['type'] = "custom";
	    		$a['bookable'] = "yes";
	    		$a['from'] = goach_transform_date(get_post_meta( $post->ID, '_date_fixe_3', true ));
	    		$a['to'] = goach_transform_date(get_post_meta( $post->ID, '_date_fixe_3', true ));
	    		$aHours[] = $a;
    		}
    	}
        

		update_post_meta( $product_id, '_visibility', 'visible' );
		update_post_meta( $product_id, '_stock_status', 'instock');
		update_post_meta( $product_id, 'total_sales', '0' );
		update_post_meta( $product_id, '_downloadable', 'no' );
		update_post_meta( $product_id, '_virtual', 'yes' );
		update_post_meta( $product_id, '_regular_price', '' );
		update_post_meta( $product_id, '_sale_price', '' );
		update_post_meta( $product_id, '_purchase_note', '' );
		update_post_meta( $product_id, '_featured', 'no' );
		update_post_meta( $product_id, '_weight', '' );
		update_post_meta( $product_id, '_length', '' );
		update_post_meta( $product_id, '_width', '' );
		update_post_meta( $product_id, '_height', '' );
		update_post_meta( $product_id, '_sku', '' );
		update_post_meta( $product_id, '_product_attributes', array() );
		update_post_meta( $product_id, '_sale_price_dates_from', '' );
		update_post_meta( $product_id, '_sale_price_dates_to', '' );
		update_post_meta( $product_id, '_price', get_post_meta( $post->ID, '_job_price_range_min', true ) );
		update_post_meta( $product_id, '_sold_individually', '' );
		update_post_meta( $product_id, '_manage_stock', 'no' );
		update_post_meta( $product_id, '_backorders', 'no' );
		update_post_meta( $product_id, '_stock', '' );
		$tax = get_post_meta( $post->ID, '_tax_enabled', true );

		if ($tax == "oui") {
			update_post_meta( $product_id, '_tax_status', 'taxable' );
			update_post_meta( $product_id, '_tax_class', '' );
			$comission = get_post_meta( $post->ID, '_job_price_range_min', true )*1.20;
			if (get_post_meta( $post->ID, '_job_price_range_min', true ) <= 2) {
				update_post_meta( $product_id, '_tax_class', 'tva-plus-30' );
			}

		} else {
			update_post_meta( $product_id, '_tax_status', 'taxable' );
			update_post_meta( $product_id, '_tax_class', 'reduced-rate' );
			$comission = get_post_meta( $post->ID, '_job_price_range_min', true );
			if (get_post_meta( $post->ID, '_job_price_range_min', true ) <= 2) {
				update_post_meta( $product_id, '_tax_class', 'zero-rate' );
			}
		}
		
		
		update_post_meta( $product_id, '_upsell_ids', '' );
		update_post_meta( $product_id, '_crosssell_ids', '' );
		update_post_meta( $product_id, '_default_attributes', '' );
		update_post_meta( $product_id, '_product_image_gallery', '' );
		update_post_meta( $product_id, '_download_limit', '' );
		update_post_meta( $product_id, '_download_expiry', '' );
		update_post_meta( $product_id, '_has_additional_costs', '' );
		update_post_meta( $product_id, '_wc_booking_apply_adjacent_buffer', '' );
		update_post_meta( $product_id, '_wc_booking_availability', $aHours );
		update_post_meta( $product_id, '_wc_booking_base_cost', '' );
		update_post_meta( $product_id, '_wc_booking_buffer_period', get_post_meta( $post->ID, '_buffering', true ) );
		update_post_meta( $product_id, '_wc_booking_calendar_display_mode', $calendar_display );
		update_post_meta( $product_id, '_wc_booking_cancel_limit_unit', 'day' );
		update_post_meta( $product_id, '_wc_booking_cancel_limit', '2' );
		update_post_meta( $product_id, '_wc_booking_check_availability_against', '' );
		update_post_meta( $product_id, '_wc_booking_cost', get_post_meta( $post->ID, '_job_price_range_min', true ) );
		update_post_meta( $product_id, '_wc_booking_default_date_availability', 'non-available' );
		update_post_meta( $product_id, '_wc_booking_duration_type', 'fixed' );
		update_post_meta( $product_id, '_wc_booking_duration_unit', 'day' );
		update_post_meta( $product_id, '_wc_booking_duration', $block );
		update_post_meta( $product_id, '_wc_booking_enable_range_picker', '' );
		update_post_meta( $product_id, '_wc_booking_first_block_time', '' );
		update_post_meta( $product_id, '_wc_booking_has_person_types', '' );
		update_post_meta( $product_id, '_wc_booking_has_persons', '1' );
		update_post_meta( $product_id, '_wc_booking_has_resources', '' );
		update_post_meta( $product_id, '_wc_booking_max_date_unit', 'month' );
		update_post_meta( $product_id, '_wc_booking_max_date', '12' );
		update_post_meta( $product_id, '_wc_booking_max_duration', '1' );
		update_post_meta( $product_id, '_wc_booking_max_persons_group', get_post_meta( $post->ID, '_nb_personnes_max', true ) );
		update_post_meta( $product_id, '_wc_booking_min_date_unit', '' );
		update_post_meta( $product_id, '_wc_booking_min_date', '' );
		update_post_meta( $product_id, '_wc_booking_min_duration', '1' );
		update_post_meta( $product_id, '_wc_booking_min_persons_group', '1' );
		update_post_meta( $product_id, '_wc_booking_person_cost_multiplier', 'yes' );
		update_post_meta( $product_id, '_wc_booking_person_qty_multiplier', 'yes' );
		update_post_meta( $product_id, '_wc_booking_pricing', get_post_meta( $post->ID, '_job_price_range_min', true ) );
		update_post_meta( $product_id, '_wc_booking_qty', get_post_meta( $post->ID, '_nb_personnes_max', true ) );
		update_post_meta( $product_id, '_wc_booking_requires_confirmation', 'no' );
		update_post_meta( $product_id, '_wc_booking_resources_assignment', '' );
		update_post_meta( $product_id, '_wc_booking_user_can_cancel', 'yes' );
		update_post_meta( $product_id, '_wc_display_cost', get_post_meta( $post->ID, '_job_price_range_min', true ) );
		update_post_meta( $product_id, 'wc_booking_resource_label', '' );
		update_post_meta( $product_id, '_resource_base_costs', '' );
		update_post_meta( $product_id, '_resource_block_costs', '' );
		update_post_meta( $product_id, '_product_version', '' );
		update_post_meta( $product_id, '_wcpv_product_commission', $comission );
		update_post_meta( $product_id, '_wcpv_product_default_pass_shipping_tax', 'no' );

		$arg = array(
		    'ID' => $product_id,
		    'post_author' => $post->post_author,
		);
		$vendor_name = get_user_meta($post->post_author, '_vendor_name', true);
		wp_update_post( $arg );
		wp_set_object_terms( $product_id, $vendor_name, WC_PRODUCT_VENDORS_TAXONOMY );

		

		

    }
}

add_action( 'transition_post_status', 'new_post_status', 10, 3 );







function stuff_save ($post_id) {
	if(!empty(get_post_meta( $post_id, 'geolocation_city', true ))) {

			$post = get_post($post_id);
		
			$a = ["Que faire à ", "Quoi faire à ", "Qu'est-ce que je peux faire à ", "Quelle activité je peux faire à ", "Quels loisirs je peux faire à ", "Sortir à "];
				remove_action('save_post', 'stuff_save');
					update_post_meta($post_id, '_yoast_wpseo_metadesc', $a[rand(0,5)].get_post_meta( $post_id, 'geolocation_city', true )." ? ".texte_resume($post->post_content, 280));

				add_action('save_post', 'stuff_save');

		}
}

add_action( 'save_post', 'stuff_save' );





function get_bookings_by_vendor( $vendor_id) {

  	global $WCFM, $wpdb, $_POST;

  	

	$vendor_products = WC_Product_Vendors_Utils::get_vendor_product_ids($vendor_id);

  	

	if( empty($vendor_products) ) return array(0);

		

  	$query = "SELECT * FROM {$wpdb->posts} as posts

							INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id

							WHERE 1=1

							AND posts.post_type IN ( 'wc_booking' )

							AND postmeta.meta_key = '_booking_product_id' AND postmeta.meta_value in (" . implode(',', $vendor_products) . ")";

		

		$vendor_bookings = $wpdb->get_results($query);

		if( empty($vendor_bookings) ) return array(0);

		$vendor_bookings_arr = array();
		$bookings = array();

		foreach( $vendor_bookings as $vendor_booking ) {

			$vendor_bookings_arr[] = $vendor_booking->ID;
			$bookings[] = get_wc_booking ($vendor_booking->ID);
		}

		if( !empty($vendor_bookings_arr) ) return $bookings;

		return array(0);

  }

  function goach_custom_submit_job_form_fields ($fields ) {

  	


  	unset( $fields['company']['company_phone']);
  	unset( $fields['company']['company_facebook']);
  	unset( $fields['company']['company_googleplus']);
  	unset( $fields['company']['company_linkedin']);
  	unset( $fields['company']['company_pinterest']);
  	unset( $fields['company']['company_instagram']);
  	unset( $fields['company']['company_website']);
	unset( $fields['company']['company_twitter']);
  	return $fields;

  }

  add_filter( 'submit_job_form_fields', 'goach_custom_submit_job_form_fields' , 40);

  function goach_review_accepted () {

  		global $post;
		global $wpdb;
		$user = wp_get_current_user();
		$product_id = get_post_meta( $post->ID, '_id_product', true );

        $query = "SELECT * FROM {$wpdb->posts} as posts

                            INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id

                            WHERE 1=1

                            AND posts.post_type IN ( 'wc_booking' )

                            AND posts.post_status = 'complete'

                            AND posts.post_author = ".$user->ID."

                            AND postmeta.meta_key = '_booking_product_id' AND postmeta.meta_value = ".$product_id;


        $vendor_bookings = $wpdb->get_results($query);

        

        if (count($vendor_bookings) >= 1) {

        	return true;

        } else {

        	return false;

        }
  }

 function texte_resume($texte, $nbreCar) {
        $LongueurTexteBrutSansHtml = strlen(strip_tags($texte));

        if($LongueurTexteBrutSansHtml < $nbreCar) return $texte;

        $MasqueHtmlSplit = '#</?([a-zA-Z1-6]+)(?: +[a-zA-Z]+="[^"]*")*( ?/)?>#';
        $MasqueHtmlMatch = '#<(?:/([a-zA-Z1-6]+)|([a-zA-Z1-6]+)(?: +[a-zA-Z]+="[^"]*")*( ?/)?)>#';

        $texte .= ' ';

        $BoutsTexte = preg_split($MasqueHtmlSplit, $texte, -1,  PREG_SPLIT_OFFSET_CAPTURE | PREG_SPLIT_NO_EMPTY);

        $NombreBouts = count($BoutsTexte);

        if( $NombreBouts == 1 )
        {
                $longueur = strlen($texte);

                return substr($texte, 0, strpos($texte, ' ', $longueur > $nbreCar ? $nbreCar : $longueur));
        }

        $longueur = 0;

        $indexDernierBout = $NombreBouts - 1;

        $position = $BoutsTexte[$indexDernierBout][1] + strlen($BoutsTexte[$indexDernierBout][0]) - 1;

        $indexBout = $indexDernierBout;
        $rechercheEspace = true;

        foreach( $BoutsTexte as $index => $bout )
        {
                $longueur += strlen($bout[0]);

                if( $longueur >= $nbreCar )
                {
                        $position_fin_bout = $bout[1] + strlen($bout[0]) - 1;

                        $position = $position_fin_bout - ($longueur - $nbreCar);

                        if( ($positionEspace = strpos($bout[0], ' ', $position - $bout[1])) !== false  )
                        {
                                $position = $bout[1] + $positionEspace;
                                $rechercheEspace = false;
                        }

                        if( $index != $indexDernierBout )
                                $indexBout = $index + 1;
                        break;
                }
        }

        if( $rechercheEspace === true )
        {
                for( $i=$indexBout; $i<=$indexDernierBout; $i++ )
                {
                        $position = $BoutsTexte[$i][1];
                        if( ($positionEspace = strpos($BoutsTexte[$i][0], ' ')) !== false )
                        {
                                $position += $positionEspace;
                                break;
                        }
                }
        }

        $texte = substr($texte, 0, $position);

        preg_match_all($MasqueHtmlMatch, $texte, $retour, PREG_OFFSET_CAPTURE);

        $BoutsTag = array();

        foreach( $retour[0] as $index => $tag )
        {
                if( isset($retour[3][$index][0]) )
                {
                        continue;
                }

                if( $retour[0][$index][0][1] != '/' )
                {
                        array_unshift($BoutsTag, $retour[2][$index][0]);
                }

                else
                {
                        array_shift($BoutsTag);
                }
        }

        if( !empty($BoutsTag) )
        {
                foreach( $BoutsTag as $tag )
                {
                        $texte .= '</' . $tag . '>';
                }
        }

        if ($LongueurTexteBrutSansHtml > $nbreCar)
        {
                $texte .= ' [......]';

                $texte =  str_replace('</p> [......]', '... </p>', $texte);
                $texte =  str_replace('</ul> [......]', '... </ul>', $texte);
                $texte =  str_replace('</div> [......]', '... </div>', $texte);
        }

        return $texte;
}

function notifyauthor($post_id) {
 
$post = get_post($post_id);
$author = get_userdata($post->post_author);
$subject = "Votre activité ".$post->post_title." n'a pas été retenue !";
$message = get_header_email("Ouch! Votre activité n'a pas été approuvée.");
$message .= '
<strong>Ok c\'est une déception. Mais rien n\'est perdu car... tout se transforme! ;)</strong>
<br/><br/>

Nous ne l\'avons pas publiée parce qu\'elle ne correspond pas totalement à <a href="https://www.mylittlewe.com/charte-dutilisation-du-site/">notre charte</a> ! Nous vous invitons à relire cette charte en cliquant ici !</strong>
<br/>
<h2 style="color: #a074a4; display: block;">Les conseils de l\'équipe myLittleWE</h2>

Retravaillez votre texte de description, soignez la qualité de vos photos. Ce sont des détails importants qui permettent d\'être plus visibles et donc d\'avoir plus de clients. Nous sommes très sensibles à la qualité de vos contenus.
<br/><br/>
Nous sommes sûrs que vous trouverez les mots et les images pour vous faire publier sur myLittleWE ! 

<br/>
<br/>
L\'équipe de myLittleWE vous remercie pour votre contribution.
<br/><br/><br/>
Ci-dessous le texte de description que vous nous avez transmis.
<br/><br/><strong>Retentez votre chance !</strong>
<br/><br/><br/><i>
'.$post->post_content.'
</i>
</div></td>
</tr>
</tbody>
</table>
<!-- End Content --></td>
</tr>
</tbody>
</table>
<!-- End Body --></td>
</tr>
<tr>
<td align="center" valign="top"><!-- Footer -->
<table id="template_footer" border="0" width="600" cellspacing="0" cellpadding="10">
<tbody>
<tr>
<td valign="top">
<table border="0" width="100%" cellspacing="0" cellpadding="10">
<tbody>
<tr>
<td id="credit" style="padding: 0 48px 48px 48px; border: 0; color: #c6acc8; font-family: Arial; font-size: 12px; line-height: 125%; text-align: center;" colspan="2" valign="middle">myLittleWE</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<!-- End Footer --></td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</div>';

$message .= get_footer_email ();

      $headers = array('Content-Type: text/html; charset=UTF-8','From: myLittleWE <infos@mylittlewe.com>');
      

      
   wp_mail($author->user_email, $subject, $message, $headers);

}
add_action('pending_to_trash', 'notifyauthor');


function get_header_email ($title) {

	// Load colors.
	$bg              = get_option( 'woocommerce_email_background_color' );
	$body            = get_option( 'woocommerce_email_body_background_color' );
	$base            = get_option( 'woocommerce_email_base_color' );
	$base_text       = wc_light_or_dark( $base, '#202020', '#ffffff' );
	$text            = get_option( 'woocommerce_email_text_color' );

	// Pick a contrasting color for links.
	$link = wc_hex_is_light( $base ) ? $base : $base_text;
	if ( wc_hex_is_light( $body ) ) {
		$link = wc_hex_is_light( $base ) ? $base_text : $base;
	}

	$bg_darker_10    = wc_hex_darker( $bg, 10 );
	$body_darker_10  = wc_hex_darker( $body, 10 );
	$base_lighter_20 = wc_hex_lighter( $base, 20 );
	$base_lighter_40 = wc_hex_lighter( $base, 40 );
	$text_lighter_20 = wc_hex_lighter( $text, 20 );

	$str = '<style>
			.text {
				color:#636363;
				font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
			}

			#wrapper {
				background-color: <?php echo esc_attr( $bg ); ?>;
				margin: 0;
				padding: 70px 0 70px 0;
				-webkit-text-size-adjust: none !important;
				width: 100%;
			}

			#template_container {
				box-shadow: 0 1px 4px rgba(0,0,0,0.1) !important;
				background-color: <?php echo esc_attr( $body ); ?>;
				border: 1px solid <?php echo esc_attr( $bg_darker_10 ); ?>;
				border-radius: 3px !important;
			}

			#template_header {
				background-color: <?php echo esc_attr( $base ); ?>;
				border-radius: 3px 3px 0 0 !important;
				color: <?php echo esc_attr( $base_text ); ?>;
				border-bottom: 0;
				font-weight: bold;
				line-height: 100%;
				vertical-align: middle;
				font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
			}

			#template_header h1,
			#template_header h1 a {
				color: <?php echo esc_attr( $base_text ); ?>;
			}

			#template_footer td {
				padding: 0;
				-webkit-border-radius: 6px;
			}

			#template_footer #credit {
				border:0;
				color: <?php echo esc_attr( $base_lighter_40 ); ?>;
				font-family: Arial;
				font-size:12px;
				line-height:125%;
				text-align:center;
				padding: 0 48px 48px 48px;
			}

			#body_content {
				background-color: <?php echo esc_attr( $body ); ?>;
			}

			#body_content table td {
				padding: 48px 48px 0;
			}

			#body_content table td td {
				padding: 12px;
			}

			#body_content table td th {
				padding: 12px;
			}

			#body_content td ul.wc-item-meta {
				font-size: small;
				margin: 1em 0 0;
				padding: 0;
				list-style: none;
			}

			#body_content td ul.wc-item-meta li {
				margin: 0.5em 0 0;
				padding: 0;
			}

			#body_content td ul.wc-item-meta li p {
				margin: 0;
			}

			#body_content p {
				margin: 0 0 16px;
			}

			#body_content_inner {
				color: <?php echo esc_attr( $text_lighter_20 ); ?>;
				font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
				font-size: 14px;
				line-height: 150%;
				text-align: right; ?>;
			}

			.td {
				color: <?php echo esc_attr( $text_lighter_20 ); ?>;
				border: 1px solid <?php echo esc_attr( $body_darker_10 ); ?>;
			}

			.address {
				padding:12px 12px 0;
				color: <?php echo esc_attr( $text_lighter_20 ); ?>;
				border: 1px solid <?php echo esc_attr( $body_darker_10 ); ?>;
			}

			.text {
				color: <?php echo esc_attr( $text ); ?>;
				font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
			}

			.link {
				color: <?php echo esc_attr( $base ); ?>;
			}

			#header_wrapper {
				padding: 36px 48px;
				display: block;
			}

			h1 {
				color: <?php echo esc_attr( $base ); ?>;
				font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
				font-size: 30px;
				font-weight: 300;
				line-height: 150%;
				margin: 0;
				text-align: right;
				text-shadow: 0 1px 0 <?php echo esc_attr( $base_lighter_20 ); ?>;
			}

			h2 {
				color: <?php echo esc_attr( $base ); ?>;
				display: block;
				font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
				font-size: 18px;
				font-weight: bold;
				line-height: 130%;
				margin: 0 0 18px;
				text-align: right;
			}

			h3 {
				color: <?php echo esc_attr( $base ); ?>;
				display: block;
				font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
				font-size: 16px;
				font-weight: bold;
				line-height: 130%;
				margin: 16px 0 8px;
				text-align: right;
			}

			a {
				color: <?php echo esc_attr( $link ); ?>;
				font-weight: normal;
				text-decoration: underline;
			}

			img {
				border: none;
				display: inline;
				font-size: 14px;
				font-weight: bold;
				height: auto;
				line-height: 100%;
				outline: none;
				text-decoration: none;
				text-transform: capitalize;
			}
			</style>
			<div id="wrapper" dir="ltr" style="background-color: #222222; margin: 0; padding: 70px 0 70px 0; width: 100%;">
			<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tbody>
			<tr>
			<td align="center" valign="top">
			<div id="template_header_image">
			<p style="margin-top: 0;"><img src="https://www.mylittlewe.com/wp-content/uploads/2018/02/logo2-2.png" alt="myLittleWE" /></p>

			</div>
			<table id="template_container" style="background-color: #ffffff; border-radius: 3px!important;" border="0" width="600" cellspacing="0" cellpadding="0">
			<tbody>
			<tr>
			<td align="center" valign="top"><!-- Header -->
			<table id="template_header" style="background-color: #a074a4; color: #ffffff; border-bottom: 0; font-weight: bold; line-height: 100%; vertical-align: middle; font-family: Helvetica Neue,Helvetica,Roboto,Arial,sans-serif;" border="0" width="600" cellspacing="0" cellpadding="0">
			<tbody>
			<tr>
			<td id="header_wrapper" style="padding: 36px 48px; display: block;">
			<h1 style="color: #ffffff; font-size: 20px!important;">'.$title.'</h1>
			</td>
			</tr>
			</tbody>
			</table>
			<!-- End Header --></td>
			</tr>
			<tr>
			<td align="center" valign="top"><!-- Body -->
			<table id="template_body" style="background-color: #ffffff;" border="0" width="600" cellspacing="0" cellpadding="0">
			<tbody>
			<tr>
			<td id="body_content" style="background-color: #ffffff;" valign="top">
			<table border="0" width="100%" cellspacing="0" cellpadding="20">
			<tbody>
			<tr>
			<td style="padding: 48px 48px 0;" valign="top">
			<div id="body_content_inner" style="color: #636363;">';

	return $str;

}

function get_footer_email () {
	$str = '</div></td>
			</tr>
			</tbody>
			</table>
			<!-- End Content --></td>
			</tr>
			</tbody>
			</table>
			<!-- End Body --></td>
			</tr>
			<tr>
			<td align="center" valign="top"><!-- Footer -->
			<table id="template_footer" border="0" width="600" cellspacing="0" cellpadding="10">
			<tbody>
			<tr>
			<td valign="top">
			<table border="0" width="100%" cellspacing="0" cellpadding="10">
			<tbody>
			<tr>
			<td id="credit" style="padding: 0 48px 48px 48px; border: 0; color: #c6acc8; font-family: Arial; font-size: 12px; line-height: 125%; text-align: center;" colspan="2" valign="middle">myLittleWE</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			</tbody>
			</table>
			<!-- End Footer --></td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			</tbody>
			</table>
			</div>';

			return $str;

}

//fonction pour compter le nombre d'activité d'un user
function count_post_all_types ($user_ID){
    global $wpdb;
 
    if(empty($user_ID))
        return 0;
 
    //on sélectionne bien tous les types de contenus voulus
    $post_type = array('job_listing');
    $sql = $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_author = %d AND post_status = 'publish' AND ", $user_ID );
 
    //Inclure les CPT dans la requête
    if(!empty($post_type)){
        $argtype = array_fill(0, count($post_type), '%s');
        $where = "(post_type=".implode( " OR post_type=", $argtype).') AND ';
        $sql .= $wpdb->prepare($where,$post_type);
    }
 
    $sql .='1=1';
    return $count_p = $wpdb->get_var($sql);
}

function alert_stripe () {
	$user_id = get_current_user_id(); 
	if (count_post_all_types($user_id) > 0) {
		$vendor_id = WC_Product_Vendors_Utils::get_logged_in_vendor();
        if(empty($vendor_id)) {
             $vendor_id = get_user_meta(get_current_user_id(), "_id_vendor", true);
        }

        if (!get_term_meta($vendor_id, '_stripe_connected', true)) {
        	 
        	 echo "<ul class='woocommerce-error' role='alert'><h2>Nous souhaitons pouvoir vous payer !</h2><li><b>Vous n'êtes pas connecté à Stripe, notre solution sécurisée de paiement ! Au moins une de vos activités est en cours, et des participants peuvent réserver. Il serait dommage que l'on ne puisses pas vous payer non ?</b></li></ul>";

        	 echo "<div class='job-manager-message'><p>Nous avons choisi Stripe pour sa facilité d'utilisation et le fait qu'il soit totalement sécurisé. Cette solution nous permet de vous régler les paiements des participants à vos activités. Vous créez votre compte sur Stripe et renseignez vos coordonnées bancaires. Ainsi, vos coordonnées bancaires ne sont pas stockées sur myLittleWE, ce qui sécurise totalement nos transactions. Cliquez sur le bouton ci-dessous pour vous connecter ou créer un compte Stripe.</p></div>";

        	 

        	 $stripe_connect = new WC_Product_Vendors_Stripe_Connect ();
			 echo $stripe_connect->render_stripe_connect_page();

			 echo "<br/>";
        }
	}
}

function goach_woocommerce_after_edit_account_form () {
	?>
			<fieldset style="margin-top:30px!important">
				<p class="form-row form-row-wide">
					<label for="biography_photo" class="screen-reader-text">Connection à Stripe pour les paiements des participants à vos activités</label>
					<?php $stripe_connect = new WC_Product_Vendors_Stripe_Connect ();
			 				echo $stripe_connect->render_stripe_connect_page(); ?>
				</p>
			</fieldset>
	<?php
}

add_action('woocommerce_after_edit_account_form', 'goach_woocommerce_after_edit_account_form', 10, 2);

function goach_send_email_review($booking) {

	$author_obj = get_user_by('id', $booking->customer_id);
	$email = $author_obj->user_email;

	$subject = "Venez noter votre activité myLittleWE !";
	$message = get_header_email("Ola! Votre activité s'est bien passée ?");
	$job_id = get_post_meta( $booking->product_id, '_id_job', true );
	$link = get_post_permalink( $job_id);

	$message .= '
<strong>Nous espérons que vous avez passé un bon moment !</strong>
<br/><br/>

L\'heure est maintenant au verdict pour votre hôte. C\'est un moment très important pour lui (et pour nous). Votre notation sera visible sur la page d\'activité. Une bonne note encouragera d\'autres participants alors qu\'une mauvaise au contraire découragera.<br/><br/><strong>Soyez juste !</strong>
<br/><br/>
<a href="'.$link.'" target="_blank">Pour noter cette activité, rendez vous ici !</a><br/><br/>
L\'équipe de myLittleWE vous remercie pour votre contribution.
<br/><br/><br/>

</div></td>
</tr>
</tbody>
</table>
<!-- End Content --></td>
</tr>
</tbody>
</table>
<!-- End Body --></td>
</tr>
<tr>
<td align="center" valign="top"><!-- Footer -->
<table id="template_footer" border="0" width="600" cellspacing="0" cellpadding="10">
<tbody>
<tr>
<td valign="top">
<table border="0" width="100%" cellspacing="0" cellpadding="10">
<tbody>
<tr>
<td id="credit" style="padding: 0 48px 48px 48px; border: 0; color: #c6acc8; font-family: Arial; font-size: 12px; line-height: 125%; text-align: center;" colspan="2" valign="middle">myLittleWE</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<!-- End Footer --></td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</div>';

$message .= get_footer_email ();

      $headers = array('Content-Type: text/html; charset=UTF-8','From: myLittleWE <infos@mylittlewe.com>');
      

      
   wp_mail($author->user_email, $subject, $message, $headers);


}





function goach_is_private_header_metadata() {

    // Post object if needed
    global $post;

    // Page conditional if needed
    // if( is_page() ){}
    if (get_post_meta( $post->ID,"is_private", true) == 1) {


   ?>
    <meta name="robots" content="noindex">
    <meta name="googlebot" content="noindex">

  <?php
  	 }
}
add_action( 'wp_head', 'goach_is_private_header_metadata' );

function get_2daysbefore ($booking) {
	$dataString = $booking->get_start_date( 'Y-m-d', 'H:i:s' );
	$result=date('Y-m-d', strtotime($dataString.' - 2 days'));
	$today = date('Y-m-d');   
	if(strtotime($today) < strtotime($result)) {
		return true;
	} else {
		return false;
	}
    
}

function verif_date_for_commission ($order) {
	global  $wpdb;
	
	foreach ( $order->get_items() as $order_item_id => $item ) {
		$booking_id = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_booking_order_item_id' AND meta_value = %d", $order_item_id ) );

	}


	$booking = get_wc_booking( $booking_id[0]);

	return get_8days_after ($booking);
	//return true;
}

function get_8days_after ($booking) {
	$dataString = $booking->get_start_date( 'Y-m-d', 'H:i:s' );
	$result=date('Y-m-d', strtotime($dataString.' + 8 days'));
	$today = date('Y-m-d'); 
	
	if(strtotime($today) >= strtotime($result)) {
		
		return true;

	} else {

		return false;
	}
    
}

add_filter( 'submit_job_form_prefix_post_name_with_location', '__return_false' );

function goach_custom_job_post_type_link( $post_id, $post ) {

    	// don't add the id if it's already part of the slug
    	$permalink = $post->post_name;
    	if(empty(get_post_meta( $post_id, 'geolocation_city', true ))) {
    		return;
    	}
    	
		if( stripos($permalink, goach_slugify(get_post_meta( $post_id, 'geolocation_city', true ))) === false) { 
				// unhook this function to prevent infinite looping
				remove_action( 'save_post_job_listing', 'goach_custom_job_post_type_link', 10, 2 );

				// add the id to the slug
		    	$permalink = get_post_meta( $post->ID, 'geolocation_postcode', true )."-".get_post_meta( $post_id, 'geolocation_city', true ).'-'.$permalink;
		    
		    	// update the post slug
		    	wp_update_post( array(
		        	'ID' => $post_id,
		        	'post_name' => $permalink
		    	));

		    	// re-hook this function
		    	add_action( 'save_post_job_listing', 'goach_custom_job_post_type_link', 10, 2 );
		} 
 	
}

function goach_slugify($text)
{
  // replace non letter or digits by -
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  // trim
  $text = trim($text, '-');

  // remove duplicate -
  $text = preg_replace('~-+~', '-', $text);

  // lowercase
  $text = strtolower($text);

  if (empty($text)) {
    return 'n-a';
  }

  return $text;
}
add_action( 'save_post_job_listing', 'goach_custom_job_post_type_link', 10, 2 );

function goach_the_next_date_disponible ($post) {

	$product_id = get_post_meta( $post->ID, '_id_product', true );
	
	$aDates = get_post_meta( $product_id, '_wc_booking_availability', true );


	foreach ($aDates as $date_activite) {

		if ( $date_activite['type'] == "custom") {
			if(strtotime($date_activite['from']) > strtotime(date("d-m-Y")) && empty($startDate)) {
			 if ( $date_activite[ 'bookable' ] == 'yes' ) {
				$startDate = $date_activite['from'];
			 }
			} 
		} else {
			$startDate ="empty";
		}
		
	}


	if (empty($startDate)) {
		echo "<div style='padding:5px!important;border-radius: 5px;text-align:center;width:150px!important;background-color:#ff0000!important;color:white;font-size:20px!important;margin-top:20px'>COMPLET</div>";
	} else {
		echo "<div style='padding:5px!important;border-radius: 5px;text-align:center;width:150px!important;background-color:#70af1a!important;color:white;font-size:20px!important;margin-top:20px'>DISPONIBLE</div>";
	}

}

function goach_next_day($day_number, $offset = 0)
{
	  for ($i = 2; $i <= 8; $i++)
	  {
	    $next_day = mktime(0,0,0, date("m"), date("d")+($i+$offset), date("Y"));
	    if(date("w",$next_day)==$day_number)
	    {
	      $XDate = getdate($next_day);
	      $next_day_fund = sprintf('%02d', $XDate['mday']).'-'.sprintf('%02d', $XDate['mon']).'-'.sprintf('%02d', $XDate['year']);
	    }
	  }
	  return $next_day_fund;
}



function goach_get_the_job_location( $post = null ) {
	global $post;
	$post = get_post( $post );
	
	if ( get_post_meta( $post->ID, '_job_location_visible', true ) == 1) {
		return get_post_meta( $post->ID, 'geolocation_city', true );
	} else {
		return  $post->_job_location;
	}

	

}

add_filter ('the_job_location', 'goach_get_the_job_location');





//create your function, that runs on cron
function goach_cron_function() {
    global $wpdb;
    $stripe_commission = new WC_Product_Vendors_Stripe_Connect ();
    $stripe_commission->do_payment();
    $query = "SELECT * FROM wp_posts as posts INNER JOIN wp_postmeta AS postmeta ON posts.ID = postmeta.post_id WHERE 1=1 AND posts.post_type IN ( 'wc_booking' ) AND posts.post_status = 'confirmed' GROUP BY ID ORDER BY `posts`.`post_modified` DESC";


    $bookings_confirmed = $wpdb->get_results($query);

    foreach( $bookings_confirmed as $booking_confirmed ) {

            $booking = get_wc_booking ($booking_confirmed->ID);
            
            if ($booking->end < time()) {
            	
				$booking->update_status('complete');
				goach_send_email_review ($booking);
           	}
            
            
    }

/*
    $stripe_commission = new WC_Product_Vendors_Stripe_Connect ();
    $stripe_commission->do_payment();


    $query = "SELECT * FROM wp_posts as posts INNER JOIN wp_postmeta AS postmeta ON posts.ID = postmeta.post_id WHERE 1=1 AND posts.post_type IN ( 'wc_booking' ) AND posts.post_status = 'unpaid' GROUP BY ID ORDER BY `posts`.`post_modified` DESC";

	$bookings_unpaid = $wpdb->get_results($query);

    foreach( $bookings_unpaid as $booking_unpaid ) {

            $booking = get_wc_booking ($bookings_unpaid->ID);
            
            if ($booking->end < time()) {
            	
            	$order = new WC_Order( $booking->get_order_id() );
				$order->update_status('cancelled', 'order_note');
				wp_update_post( array(
		        	'ID' => $booking->get_order_id()
		        	
		    	));
				$booking->update_status('cancelled');
				wp_update_post( array(
		        	'ID' => $booking->get_id()
		    	));
				
				
            	
           	}
            
            
    }
*/
}

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

add_action( 'woocommerce_before_cart', 'move_proceed_button' );
function move_proceed_button( $checkout ) {
    echo '<div class="mobile-checkout-btn text-right"><a href="' . esc_url( WC()->cart->get_checkout_url() ) . '" class="checkout-button button alt wc-forward" >Commander</a></div>';
}

function get_date_of_activite () {

	global $post;

	$product_id = get_post_meta( $post->ID, '_id_product', true );
	$aDates = get_post_meta( $product_id, '_wc_booking_availability', true );
	$buffer = get_post_meta( $post->ID, '_buffering', true );
	$buffer -= 1;
	foreach ($aDates as $date_activite) {

		if ( $date_activite['type'] == "custom") {
			if(strtotime($date_activite['from']) > strtotime(date("d-m-Y")) && empty($startDate)) {
				$startDate = $date_activite['from'];
			} 
		} else {
			if ($date_activite['type'] == "time:1") $startDate = date('d-m-Y', strtotime('next monday'));
			if ($date_activite['type'] == "time:2") $startDate = date('d-m-Y', strtotime('next tuesday'));
			if ($date_activite['type'] == "time:3") $startDate = date('d-m-Y', strtotime('next wednesday'));
			if ($date_activite['type'] == "time:4") $startDate = date('d-m-Y', strtotime('next thursday'));
			if ($date_activite['type'] == "time:5") $startDate = date('d-m-Y', strtotime('next friday'));
			if ($date_activite['type'] == "time:6") $startDate = date('d-m-Y', strtotime('next saturday'));
			if ($date_activite['type'] == "time:7") $startDate = date('d-m-Y', strtotime('next sunday'));
		}
		
	}

if ($startDate && get_post_meta( $post->ID, '_buffering', true ) > 1) {
	echo '<div class="kw-box">';
	echo '<h2>A propos de '.get_the_title().'</h2>';		
	$timestamp = strtotime($startDate);
	$reservation = strtotime('-'.$buffer.' day', $timestamp);

	$today = strtotime(date("Y-m-d H:i:s"));
	if ($today > $reservation) {
		$timestamp = strtotime($startDate." + 7 days");
		$reservation = strtotime('-'.$buffer.' day', $timestamp);
	}
	$date_min =  date("Y, n-1, d+2, H, i, s", $reservation);
	//echo $date_min." :: ".$reservation;
	echo "Date prévue de l'activité : <span style='font-size:18px;font-weight:bold;'>".date("d/m/Y",$timestamp)."</span>";
	?>
<div id="compte_a_rebours"></div>
<script type="text/javascript">
function compte_a_rebours()
{
    var compte_a_rebours = document.getElementById("compte_a_rebours");

    var date_actuelle = new Date();
    var date_evenement = new Date(<?php echo $date_min; ?>);
    
	
    var total_secondes = (date_evenement - date_actuelle) / 1000;

    var prefixe = "Les réservations se terminent dans :<br/><div style='font-size:20px;font-weight:bold;margin-top:10px;color:#70af1a'>";
    if (total_secondes < 0)
    {
        prefixe = "Compte à rebours terminé il y a "; // On modifie le préfixe si la différence est négatif
        total_secondes = Math.abs(total_secondes); // On ne garde que la valeur absolue
    }

    if (total_secondes > 0)
    {
        var jours = Math.floor(total_secondes / (60 * 60 * 24));
        var heures = Math.floor((total_secondes - (jours * 60 * 60 * 24)) / (60 * 60));
        minutes = Math.floor((total_secondes - ((jours * 60 * 60 * 24 + heures * 60 * 60))) / 60);
        secondes = Math.floor(total_secondes - ((jours * 60 * 60 * 24 + heures * 60 * 60 + minutes * 60)));

        var et = "et";
        var mot_jour = "jours";
        var mot_heure = "heures";
        var mot_minute = "minutes";
        var mot_seconde = "s";

        if (jours == 0)
        {
            jours = '';
            mot_jour = '';
        }
        else if (jours == 1)
        {
            mot_jour = "jour";
        }

        if (heures == 0)
        {
            heures = '';
            mot_heure = '';
        }
        else if (heures == 1)
        {
            mot_heure = "heure";
        }

        if (minutes == 0)
        {
            minutes = '';
            mot_minute = '';
        }
        else if (minutes == 1)
        {
            mot_minute = "minute";
        }

        if (secondes == 0)
        {
            secondes = '';
            mot_seconde = '';
            et = '';
        }
        else if (secondes == 1)
        {
            mot_seconde = "s";
        }

        if (minutes == 0 && heures == 0 && jours == 0)
        {
            et = "";
        }

        compte_a_rebours.innerHTML = prefixe + jours + ' ' + mot_jour + ' ' + heures + ' ' + mot_heure + ' ' + minutes + ' ' + mot_minute + ' ' + et + ' ' + secondes + ' ' + mot_seconde +"<div>";
    }
    else
    {
        compte_a_rebours.innerHTML = 'Compte à rebours terminé.';
    }

    var actualisation = setTimeout("compte_a_rebours();", 1000);
}
compte_a_rebours();

		</script>

	<?php
	echo '</div>';
}	

}





function booking_register_widget() {
register_widget( 'booking_widget' );
}

add_action( 'widgets_init', 'booking_register_widget' );

class booking_widget extends WP_Widget {

function __construct() {
parent::__construct(
// widget ID
'booking_widget',
// widget name
__('booking Sample Widget', ' booking_widget_domain'),
// widget description
array( 'description' => __( 'booking Widget ', 'booking_widget_domain' ), )
);
}
public function widget( $args, $instance ) {
	global $post;
	
	$title = apply_filters( 'widget_title', $instance['title'] );
	echo $args['before_widget'];
	//if title is present
	
	//output
	$product_id = get_post_meta( $post->ID, '_id_product', true );
	
	$aDates = get_post_meta( $product_id, '_wc_booking_availability', true );


	foreach ($aDates as $date_activite) {

		if ( $date_activite['type'] == "custom") {
			if(strtotime($date_activite['from']) > strtotime(date("d-m-Y")) && empty($startDate)) {
			 if ( $date_activite[ 'bookable' ] == 'yes' ) {
				$startDate = $date_activite['from'];
			 }
			} 
		} else {
			
			$startDate ="empty";
		}
		
	}


	if (!empty($startDate) && get_post_meta( $post->ID, '_job_price_range_min', true ) != "0" ) {
			$nb = get_post_meta( $post->ID, '_nb_personnes_max', true );
			if ($nb > 1 ) {
				$str = $nb." personnes";
			} else {
				$str = $nb." personne";
			}
	 ?>	
	<div class="kw-box">
		
		
		<h2 id="reservez" style="margin-bottom:0px !important">Réservez maintenant !</h2>
		
		
		<!--<p style="font-size:20px"><strong>Cette activité est limitée à <?php echo $str ?> par jour.</strong></p>-->
		<!--<p style="margin-bottom:0px !important"><strong>Pour réserver veuillez remplir le formulaire ci-dessous :</strong></p>-->
			<?php 
	$id_product = get_post_meta ($post->ID, '_id_product', true);
	echo do_shortcode('[product_page  id="'.$id_product .'" ]'); 

	?>
	<p><img src="https://www.mylittlewe.com/wp-content/uploads/2018/07/rasurre_2.jpg" ></p>
</div>
	<?php } else {?>	
	
	<?php } 
	echo $args['after_widget'];
	

}
public function form( $instance ) {
if ( isset( $instance[ 'title' ] ) )
$title = $instance[ 'title' ];
else
$title = __( 'Réservez maintenant !', 'booking_widget_domain' );
?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<?php
}
public function update( $new_instance, $old_instance ) {
$instance = array();
$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
return $instance;
}

}


function goach_shortcode_activite($atts) {

	global $knowhere_settings;
	$id = $atts['id'];
	$activite = get_post($id);

	$str = '<div  style="width:100% !important;  border-bottom: 1px solid #dedede;min-height:150px!important;margin:0px 0 0 0;background-image: linear-gradient(white, #dedede 450px);"><article ><div style="float:left;width:150px !important;margin-right:30px!important;position:relative;">';
	$str .="<div class='kw-listing-item-media kw-listing-style-4' style='position:absolute:top:20px;left:20px;'><a href='".get_permalink($activite)."' class='kw-listing-item-thumbnail'><img src='".get_the_post_thumbnail_url( $activite, $size = 'thumbnail' )."'></a></div>";
	$str .="</div>";
	$str .="<div style='padding-bottom:20px!important'><header ><div class='kw-listing-item-title' style='font-size:15px!important;font-weight:bold!important'><a href='";
	$str .= get_permalink($activite)."'>".$activite->post_title."</a></div><span class='kw-listing-price'>";
	$str .= get_post_meta( $activite->ID, '_job_price_range_min', true )."€";
	$str .= '</span><br/><a href="'.get_permalink($activite).'" ><button type="submit" class="fep-button" name="fep_action" value="shortcode-newmessage">Découvrir</button></a></header></div>';

	$str .= "</article></div>" ;


 	return $str;

 
}
add_shortcode('activite', 'goach_shortcode_activite');

function ja_remove_hentry( $class ) {
	$class = array_diff( $class, array( 'hentry' ) );	
	return $class;
}
add_filter( 'post_class', 'ja_remove_hentry' );




















