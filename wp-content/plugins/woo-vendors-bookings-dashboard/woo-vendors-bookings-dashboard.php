<?php

/**

 * Plugin Name: Woo Vendors Bookings Management

 * Description: Allows vendors to manage their bookings in the frontend

 * Version: 3.0.0

 * Author: Sébastien Odion

 * Author URI: https://www.mylittlewe.com/

 * License: GNU General Public License v3.0

 * License URI: http://www.gnu.org/licenses/gpl-3.0.html

 */


include_once ( dirname ( __FILE__ ) . '/includes/ajaxendpoint.class.php' );





//////////////////////////////////////////////////
// AJAX
//////////////////////////////////////////////////


$ajaxendpoint = new AjaxEndpoint();
$ajaxendpoint->registerActions();




function myplugin_ajaxurl() {

   echo '<script type="text/javascript">
           var ajaxurl = "' . admin_url('admin-ajax.php') . '";
         </script>';
}

add_action('wp_head', 'myplugin_ajaxurl');

function theme_name_scripts() {
    
  wp_enqueue_script( 'script-name', WP_PLUGIN_URL.'/woo-vendors-bookings-dashboard/js/front.js', array('jquery'), '1.0.0', true );
  wp_localize_script( 'script-name', 'MyAjax', array(
    // URL to wp-admin/admin-ajax.php to process the request
    'ajaxurl' => admin_url( 'admin-ajax.php' ),
    // generate a nonce with a unique ID "myajax-post-comment-nonce"
    // so that you can check it later when an AJAX request is sent
    'security' => wp_create_nonce( 'my-special-string' )
  ));


}
add_action( 'wp_enqueue_scripts', 'theme_name_scripts' );
 

//////////////////////////////////////////////////
// Ajout des liens dans le menu woocommerce
//////////////////////////////////////////////////


function goach_menu_link( $menu_links ){
 
    $menu_links = array_slice( $menu_links, 0, 1, true ) 
    + array( 'gestion-activites' => 'Mes activités' )
    + array_slice( $menu_links, 1, NULL, true );
 
    return $menu_links;
 
}

add_filter ( 'woocommerce_account_menu_items', 'goach_menu_link', 40 );


//////////////////////////////////////////////////
// Messagerie interne
//////////////////////////////////////////////////

function goach_message_link( $menu_links ){
 
    $menu_links = array_slice( $menu_links, 0, 2, true ) 
    + array( 'messages' => 'Messages' )
    + array_slice( $menu_links, 2, NULL, true );

    $menu_links = array_slice( $menu_links, 0, 3, true ) 
    + array( 'bookings' => 'Mes réservations' )
    + array_slice( $menu_links, 3, NULL, true );
 
    return $menu_links;
 
}

add_filter ( 'woocommerce_account_menu_items', 'goach_message_link', 40 );

add_filter( 'fep_menu_buttons', function( $menu ){
    
    unset( $menu['directory'] );
    unset( $menu['announcements'] );
    unset( $menu['edit-address'] );
    return $menu;

}, 99);


function custom_my_account_menu_items( $items ) {
    unset($items['downloads']);
    unset($items['orders']);
    unset($items['payment-methods']);
    
    return $items;
}
add_filter( 'woocommerce_account_menu_items', 'custom_my_account_menu_items', 40 );


function reorder_array(&$array, $new_order) {
  $inverted = array_flip($new_order);
  uksort($array, function($a, $b) use ($inverted) {
    return $inverted[$a] > $inverted[$b];
  });
}
/*
 * Step 2. Register Permalink Endpoint
 */

function goach_add_endpoint() {
 
    // WP_Rewrite is my Achilles' heel, so please do not ask me for detailed explanation
    add_rewrite_endpoint( 'gestion-activites/reservations/detail', EP_PAGES );
    add_rewrite_endpoint( 'gestion-activites/reservations', EP_PAGES );
    add_rewrite_endpoint( 'gestion-activites', EP_PAGES );
    add_rewrite_endpoint( 'messages', EP_PAGES );
    
 
}


add_action( 'init', 'goach_add_endpoint' );
/*
 * Step 3. Content for the new page in My Account, woocommerce_account_{ENDPOINT NAME}_endpoint
 */




function goach_messages_endpoint_content () {

    wp_register_style('my_stylesheet', plugins_url('/css/style-dash.css', __FILE__));
    wp_enqueue_style('my_stylesheet');
    ?>
    <div style="position:relative;width:100%">
        <?php
    echo do_shortcode('[front-end-pm]');
        ?>
    </div>
    <?php
}


function goach_my_account_endpoint_content() {

    global $wpdb;
    global $current_user; 
    // of course you can print dynamic content here, one of the most useful functions here is get_current_user_id()
    wp_register_style('my_stylesheet', plugins_url('/css/style-dash.css', __FILE__));
    wp_enqueue_style('my_stylesheet');
    wp_enqueue_script('script-name', WP_PLUGIN_URL.'/woo-vendors-bookings-dashboard/js/front.js?rand='.rand(0,10000000));

    $args = array(
      'author'        =>  $current_user->ID, 
      'post_type'   => 'job_listing',
      'sort_order' => 'asc',
       'posts_per_page' => 100
    );

    $job_listings = get_posts( $args );

    if ( empty( $_POST['job_id'] )) {
        $job_id = $job_listings[0]->ID;
       $_GET['job_id'] = $job_id;
    } else {
        $job_id = $_POST['job_id'];
        $_GET['job_id'] = $job_id;
    }

    if (!empty($_POST['close'] )) {
        add_post_meta($job_id, "is_private", 1, true);
        $my_post = array(
              'ID'           => $job_id
          );
        wp_update_post( $my_post );
    }

    if (!empty($_POST['open'] )) {
        delete_post_meta($job_id, "is_private");
        $my_post = array(
              'ID'           => $job_id
          );
        wp_update_post( $my_post );
    }



    

    ?>
    <h1 style="font-size: 36px!important">Gérer mes activités</h1>

    <p>Vous pouvez ici voir toutes les activités que vous avez publié sur MyLittleWE, les fermer au public (faire une pause pour partir en vacances), et gérer les réservations pour chacune.</p>

    <?php if (get_post_meta( $job_id, "is_private", 1 )) {
        wc_add_notice("<b>Cette activité est fermée au public !</b> Soit vous êtes partis dans l'ch'ud et à votre retour nous vous invitons à l'ouvrir au public, soit vous avez décidé que votre évènement sera privé et seuls vos amis (qui auront le lien) réserveront.","error");
    } ?>
   

    <?php  if (count( $job_listings) > 0) { ?>

    
     
                <div class="job_summary_shortcode" style="margin-bottom:30px!important;padding:20px;width:100%;text-align:left;">
                    <form action='' method="post" id="choose-job-listing" class="job-manager-form">
                        <fieldset data-cat-ids class="fieldset-job_title" style="width: 50% !important; min-width: 50% ; max-width: 50% ;" >
                            <label for="job_listings">Choisissez l'activité dont vous voulez voir les détails :</label>
                            <div class="field">

                                <select name="job_id" id="job_listings" class="postform" >
                                    <?php
                                    foreach ($job_listings as $job_listing) {
                                        if ($job_listing->ID == $job_id) {
                                            echo "<option value='".$job_listing->ID."' selected>".substr($job_listing->post_title, 0, 33)."...</option> ";
                                        } else {
                                            echo "<option value='".$job_listing->ID."' >".$job_listing->post_title."</option> ";
                                        }
                                        
                                    }
                                    ?>
                                   
                                </select>
                                <input type="submit" name="submit_job_id" class="button"  value="Visualiser les détails de l'activité" style="border-radius:5px !important">
                            </div>

                        </fieldset>
                    </form>
                </div>

<h2>Détails de votre activité</h2>

 <p><?php wc_print_notices(); ?></p>
    <div class="kw-finder-extend">
        <div class="job_listings">
            
          
            <div class="job_summary_shortcode" style="padding:20px;width:100%;text-align:left;"><?php
                            echo do_shortcode('[job_summary id="'.$job_id.'"]'); 
                            ?>
                            
                            <form action="reservations"  method="post" name="private_form" style="float:left;margin-top:12px;">
                                 <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
                                 <input type="submit" class="button" style="width:250px;height:50px!important;background-color:#ef6600!important;margin:0px!important;border-radius:5px !important" value="Gérer les réservations">
                            </form>
                            <?php if (get_post_meta( $job_id, "is_private", 1 )) { 
                                
                                ?>
                                <form action=""  method="post" name="private_form" style="float:left;margin:12px;">
                                    <input type="hidden" name="open" value="true">
                                    <input type="submit" class="button" style="height:50px!important;background-color:#70af1a!important;border-radius:5px !important" value="Ouvrir cette activité au public">
                                </form>
                            <?php } else { ?>
                                <form action=""  method="post" name="private_form" style="float:left;margin:12px;">
                                    <input type="hidden" name="close" value="true">
                                    <input type="submit" class="button" style="height:50px!important;border-radius:5px !important" value="Fermer cette activité au public">
                                </form>
                            <?php } ?>
                           
                           
                            <a href="/listings-stats-dashboard/?job_id=<?php echo $job_id ?>" target='_blank'><button class="kw-update-form" style="margin-top:12px">Voir toutes les Stats</button></a>
                            <span style="width:500px!important;float:left;margin-top:20px">

                                <?php
                                echo do_shortcode('[stats_dashboard job_id='.$job_id.'"]');
                                ?>
                            <p>(Statistiques sur les 7 derniers jours)</p>
                            </span>
                            <span style="margin-left:20px;float:left">
                            <ul style="margin-top:50px;">
                                    <? 
                                    $id_product = get_post_meta( $job_id, '_id_product', true );
                                    echo get_count_resa ($id_product, $job_id); 
                                    ?>
                            </ul>
                            </span>
                        </div>
        </div>

    </div>
    <?php
    } else {
        ?><p>Vous n'avez aucune activité dans votre compte. Nous vous invitons à en créer une en cliquant sur Proposez une activité !</p><?php
    }


       
        ?>

        <link rel="stylesheet" href="https://formden.com/static/cdn/bootstrap-iso.css" /> 
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/eonasdan-bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" /> 
        <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet">
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/eonasdan-bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.21/moment-timezone.js"></script>
         <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/fr.js"></script>
        

        <h2 style="margin-top:30px">Gérer mes périodes indisponibles pour cette activité</h2>
        <p>Vous souhaitez pouvoir bloquer une période et vous ne souhaitez pas recevoir de réservations pour ce lapse de temps, paramétrez vos périodes indisponibles en remplissant le formulaire ci dessous :</p> 
<div class="bootstrap-iso job_summary_shortcode" style="padding:30px!important;position:relative">
 <div class="container-fluid">
  <div class="row">
   <div class="col-md-12 col-sm-12 col-xs-12">

    <!-- Form code begins -->
    <form method="post" onsubmit="return verifForm(this)">
        <input type='hidden' id="product_id" value="<?php echo $id_product ?>" />
<div class='col-md-1'>
    <div style="margin-top:5px !important;font-weight:bold!important;text-align:right!important">Du :</div>
</div>
        <div class='col-md-3'>

        <div class="form-group">
            
            <div class='input-group date' id='datetimepicker6'>

                <input type='text' class="form-control" id="date_debut" />
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
    </div>
    <div class='col-md-1'>
    <div style="margin-top:5px !important;font-weight:bold!important;text-align:right!important">Au :</div>
</div>
    <div class='col-md-3'>
        <div class="form-group">
            <div class='input-group date' id='datetimepicker7'>
                <input type='text' class="form-control" id="date_fin" />
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
    </div>

        <div class="col-md-2 col-sm-2 col-xs-12">
           <div class="form-group"> <!-- Submit button -->
                <button id="submit_blocage" class="btn btn-primary " name="submit" type="submit">Bloquer cette période</button>
              </div>
        </div>
     

     </form>
     <!-- Form code ends --> 

    </div>
  </div>    
 </div>
</div>
<script type="text/javascript">
    jQuery(function () {
        jQuery('#datetimepicker6').datetimepicker({
            format: "DD-MM-YYYY HH:mm",
            locale : 'fr',
          
        });
        jQuery('#datetimepicker7').datetimepicker({
            format: "DD-MM-YYYY HH:mm",
            useCurrent: false //Important! See issue #1075
        });
        jQuery("#datetimepicker6").on("dp.change", function (e) {
            jQuery('#datetimepicker7').data("DateTimePicker").minDate(e.date);
        });
       jQuery("#datetimepicker7").on("dp.change", function (e) {
            jQuery('#datetimepicker6').data("DateTimePicker").maxDate(e.date);
        });
     
    });
</script>
<?php 

$aHours = get_post_meta( $id_product,  '_wc_booking_availability', true );

$aBlocs = [];

foreach ($aHours as $hour) {
    
    if ($hour["type"] == "time:range") {
        
        $today = date("d-m-Y H:i:");
        if( strtotime($hour["to_date"]) >= strtotime($today)) {
            $aBlocs[] = $hour;
        }
    }
}

if (count($aBlocs) > 0) {
?>
    <div class="bootstrap-iso job_summary_shortcode" style="padding:30px!important;position:relative;text-align:left !important" >
        <h2>Vos périodes d'indisponibilités</h2>
        <p>Durant ces périodes, les clients ne peuvent pas réserver votre activité.</p>
<table class="table" style="text-align:left !important">
    <thead>
      <tr>
        <th>DATE DE DEBUT</th>
        <th>DATE DE FIN</th>
        <th style="width:100px">ACTION</th>
      </tr>
    </thead>
    <tbody id="container-bloc">
     <!-- <tr>
        <td>10-09-2019 10:30</td>
        <td>10-09-2019 12:30</td>
        <td style="padding-left:30px">X</td>
      </tr>      
      <tr class="success">
        <td>12-09-2019 10:30</td>
        <td>16-09-2019 12:30</td>
        <td style="padding-left:30px">X</td>
      </tr>
      <tr class="danger">
        <td>24-09-2019 10:30</td>
        <td>24-09-2019 12:30</td>
        <td style="padding-left:30px">X</td>
      </tr>
      <tr class="info">
        <td>10-10-2019 10:30</td>
        <td>10-10-2019 12:30</td>
        <td style="padding-left:30px">X</td>
      </tr>
      <tr class="warning">
        <td>12-10-2019 10:30</td>
        <td>12-10-2019 12:30</td>
        <td style="padding-left:30px">X</td>
      </tr>
      <tr class="active">
        <td>20-10-2019 10:30</td>
        <td>20-10-2019 12:30</td>
        <td style="padding-left:30px">X</td>
      </tr>-->
      <?php
      $aBlocs = array_reverse ($aBlocs);
      $x=0;
      foreach ($aBlocs as $bloc) {
        $x++; 
        $class = ($x%2 == 0)? '': 'active';
        echo '<tr class="'.$class.'">';
            echo '<td>'.$bloc['from_date']." ".$bloc['from'].'</td>';
            echo '<td>'.$bloc['to_date']." ".$bloc['to'].'</td>';
            echo '<td class="delete" id_product="'.$id_product.'" from_date="'.$bloc['from_date'].'" to_date="'.$bloc['to_date'].'" from="'.$bloc['from'].'" to="'.$bloc['to'].'" style="padding-left:30px">X</td>';
        echo '</tr>';
      }
      ?>
    </tbody>
  </table>


    </div>
        <?php
} else { ?>
    <div class="bootstrap-iso job_summary_shortcode" style="padding:30px!important;position:relative;text-align:left !important" >
        <table class="table" style="text-align:left !important">
    <thead>
      <tr>
        <th>DATE DE DEBUT</th>
        <th>DATE DE FIN</th>
        <th style="width:100px">ACTION</th>
      </tr>
    </thead>
    <tbody id="container-bloc">
    
    </tbody>
  </table>
        <p id="message-bloc">Vous n'avez aucune période d'indisponibilité pour le moment :)</p>
    </div>    
    <?php
} 



    




}


function goach_reservations_endpoint_content() {
    

    global $wpdb;
    global $current_user; 

   

    wp_enqueue_script('jQuery', 'https://code.jquery.com/jquery-1.12.4.js');
    wp_enqueue_script('script-name', WP_PLUGIN_URL.'/woo-vendors-bookings-dashboard/js/front.js?rand='.rand(0,10000000));
   
    
    wp_register_style('my_stylesheet', plugins_url('/css/style-dash.css', __FILE__));
    wp_enqueue_style('my_stylesheet');

    

    $args = array(
      'author'        =>  $current_user->ID, 
      'post_type'   => 'job_listing',
      'sort_order' => 'asc'
    );

    if ( empty( $_POST['job_id'] )) {
        $job_id = $job_listings[0]->ID;
       $_GET['job_id'] = $job_id;
    } else {
        $job_id = $_POST['job_id'];
        $_GET['job_id'] = $job_id;
    }

    $job_listings = get_posts( $args );

    ?>
    <h1 style="font-size: 36px!important">Gérer mes réservations.</h1>
    <?php


    $id_product = get_post_meta( $job_id, '_id_product', true );
    
    $bookings = get_bookings_by_product_id ($id_product);

    $bookings_past = get_bookings_by_product_id_past ($id_product);



    foreach ($job_listings as $job_listing) {
        if ($job_listing->ID == $job_id) {
            $title = $job_listing->post_title;
        }                                  
    }
    

    ?>
    <p>Vous êtes ici sur la page qui vous permet de voir et de valider vos réservations. Vous pouvez les filtrer par dates et par status. </p>
                

              <div class="" style="width:100%!important;margin:20px 0px 20px 0px !important;text-align:left;float:left">
                    <h2>Filtrez vos réservations</h2>
                    <form action='' method="post" id="choose-job-listing" class="job-manager-form" style="width:100%!important">
                        <fieldset data-cat-ids class="fieldset-job_region" >
                            <label for="job_listings">Votre activité :</label>
                            <div class="field">

                                <select name="job_id" id="job_listings" class="postform">
                                    <?php
                                    foreach ($job_listings as $job_listing) {
                                        if ($job_listing->ID == $job_id) {
                                            echo "<option value='".$job_listing->ID."' selected>".$job_listing->post_title."</option> ";
                                        } else {
                                            echo "<option value='".$job_listing->ID."' >".$job_listing->post_title."</option> ";
                                        }
                                        
                                    }
                                    ?>
                                </select>
                                
                            </div>

                        </fieldset>
                        <fieldset data-cat-ids class="fieldset-job_region" >
                            <label for="job_listings">Date de réservations :</label>
                            <div class="field">

                                <select name="job_date" id="job_date" class="postform">
                                    <?php
                                    echo get_dates_for_select_booking ($bookings);
                                    
                                    ?>
                                </select>
                               
                            </div>

                        </fieldset>
                        <fieldset data-cat-ids class="fieldset-job_region" >
                            <label for="job_listings">Status des réservations:</label>
                            <div class="field">

                                <select name="job_status" id="job_status" class="postform" >
                                    <?php
                                    echo get_status_for_select_booking ($bookings);
                                    
                                    ?>
                                </select>
                               
                            </div>

                        </fieldset>
                        
                    </form>
                </div>

                <h3 class="explication_filtre"><?php 
                if(!empty($title)) {
                    echo "Résultats pour l'activité ".$title;
                } ?>
                    </h3>
                <div class="job_summary_shortcode" id="container_bookings" style="text-align:center;min-height:335px;width:100%!important;margin:20px 20px 0px 0px !important;padding:20px;float:left">
                    
                    <?php
                    $i=0;
                        foreach ($bookings as $booking) {
                            ?>
                           
                             <div class="job_summary_shortcode item_booking" id="item<?php echo $i; ?>" date_booking="<?php echo $booking->start; ?>" status="<?php echo $booking->status; ?>" style="width:240px!important;min-height:200px;float:left;text-align:center!important;margin:auto">   
                                <div class="roundedImage"><?php echo get_avatar( $booking->customer_id, 120 ); ?></div>
                                <?php echo get_info_user ($booking->customer_id); ?>
                                <h3 style="color:#ef6600">Réservation #<?php echo $booking->get_id(); ?></h3>
                                <div style="color:#70af1a;font-size:40px;font-weight:bold"><?php echo $booking->cost ?>€</div>
                                <div style="margin-top:20px">Réservé pour <?php echo $booking->person_counts[0] ?> personne<?php if( $booking->person_counts[0] > 1) echo 's'; ?>,</div>
                                <div>le <b><?php echo format_the_date($booking->start); ?></b></div>
                                <?php echo get_the_status($booking) ?>
                                 <form action='/my-account/gestion-activites/reservations/detail?booking-id=<?php echo  $booking->order_id ?>' method="post" id="choose-job-listing" style="margin-bottom:20px">
                                
                                    <input type="submit" name="submit_job_id" class="button" style="margin-top:10px;" value="Voir la réservation">
                                </form>
                                
                            </div>

                            <?php
                            $i++;
                        }
                    ?>
                </div>
                <div style="clear:both;"></div>
                <div id='container_past'>
                    <?php echo get_booking_ajax_by_job_id_past ($job_id) ?>
                </div>
                <div style="clear:both;"></div>
                
    <?php

}

function get_bookings_by_product_id ($product_id) {

        global $wpdb;
        $query = "SELECT * FROM {$wpdb->posts} as posts

                            INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id

                            WHERE 1=1

                            AND posts.post_type IN ( 'wc_booking' )

                            AND posts.post_status <> 'was-in-cart'

                            AND posts.post_status <> 'in-cart'

                            AND posts.post_status <> 'complete'

                            AND posts.post_status <> 'cancelled'

                            AND postmeta.meta_key = '_booking_product_id' AND postmeta.meta_value = ".$product_id;


        $vendor_bookings = $wpdb->get_results($query);
        
         
        $bookings = array();

        foreach( $vendor_bookings as $vendor_booking ) {
            $booking = get_wc_booking ($vendor_booking->ID);

            if ($booking->end >= time()) {
                $bookings[] = $booking;
            }
            
        }


        usort($bookings, "sortFunction");

        return $bookings;


}

function sortFunction( $a, $b ) {
    return $a->start - $b->start;
}

function get_bookings_by_product_id_past ($product_id = 2694) {

        global $wpdb;
        $query = "SELECT * FROM {$wpdb->posts} as posts

                            INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id

                            WHERE 1=1

                            AND posts.post_type IN ( 'wc_booking' )

                            AND posts.post_status = 'complete'

                            AND postmeta.meta_key = '_booking_product_id' AND postmeta.meta_value = ".$product_id;


        $vendor_bookings = $wpdb->get_results($query);
         
        $bookings = array();

        foreach( $vendor_bookings as $vendor_booking ) {
            $booking = get_wc_booking ($vendor_booking->ID);
            $bookings[] = $booking; 
        }

        return $bookings;


}

function get_dates_for_select_booking ($bookings) {

    $dates_bookings = array();
    $dates_bookings_timestamp = array();
    setlocale(LC_TIME, "fr_FR");



    foreach ($bookings as $booking) {
        $date_booking = strftime ("%A %d %B %Y",date($booking->start));
        if (!in_array($date_booking, $dates_bookings)) {
            $dates_bookings[] = $date_booking;
            $dates_bookings_timestamp[] = $booking->start;
        }
    }
    $str = "<option value='tous' selected>Toutes les dates</option> ";
    $i = 0;
    foreach ($dates_bookings as $dates_booking) {

        if ($dates_booking == $dates_booking_selected) {
             $str.= "<option value='".$dates_bookings_timestamp[$i]."' selected>".$dates_booking."</option> ";
        } else {
             $str.= "<option value='".$dates_bookings_timestamp[$i]."' >".$dates_booking."</option> ";
        }

        $i++;
    }

    return $str;

}

function get_status_for_select_booking ($bookings) {

    $status_bookings = array();

    foreach ($bookings as $booking) {
        if (!in_array($booking->status, $status_bookings)) {
            $status_bookings[] = $booking->status;
        }
    }
    $str = "<option value='tous' selected>Tous les status</option> ";
    foreach ($status_bookings as $status_booking) {

        if ($status_booking == $status_bookings_selected) {
             $str.= "<option value='".$status_booking."' selected>".get_status_for_select($status_booking)."</option> ";
        } else {
             $str.= "<option value='".$status_booking."' >".get_status_for_select($status_booking)."</option> ";
        }
    }

    return $str;
}

function get_status_for_select ($str) {

     switch ( $str ) {
                case 'unpaid':
                    return "Non payées";
                    break;
                case 'pending-confirmation':
                    return "A confirmer";
                    break;
                case 'confirmed':
                     return "Confirmées";
                    break;
                case 'cancelled':
                    return "Annulées";
                    break;
                case 'complete':
                    return "Terminées";
                    break;
                case 'paid':
                    return "A confirmer";
                    break;

            }
}


function get_count_resa ($id, $jobID) {
    global $wpdb;
        $query = "SELECT * FROM {$wpdb->posts} as posts

                            INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id

                            WHERE 1=1

                            AND posts.post_type IN ( 'wc_booking' )


                            AND posts.post_status <> 'was-in-cart'

                            AND posts.post_status <> 'in-cart'

                            AND postmeta.meta_key = '_booking_product_id' AND postmeta.meta_value = ".$id;


        $vendor_bookings = $wpdb->get_results($query);
        
        $bookings = array();
        $bookingsUnpaid = array();
        $bookingsConfirmed = array();
        $bookingsPaid = array();
        $bookingsComplete = array();
        $bookingsInCard = array();
        $bookingsCancelled = array();
        $bookingsPending = array();

        $str ="";
 
        foreach( $vendor_bookings as $vendor_booking ) {
            $booking = get_wc_booking ($vendor_booking->ID);
            $bookings[] = $booking;

            echo 'toto 2';
            switch ($booking->get_status()) {
                case 'unpaid':
                    $bookingsUnpaid[] = $booking;
                    break;
                case 'pending-confirmation':
                    $bookingsPending[] = $booking;
                    break;
                case 'confirmed':
                    $bookingsConfirmed[] = $booking;
                    break;
                case 'cancelled':
                    $bookingsCancelled[] = $booking;
                    break;
                case 'complete':
                    $bookingsComplete[] = $booking;
                    break;
                case 'paid':
                    $bookingsPaid[] = $booking;
                    break;

            }

        }

        $terme = "demande de réservation";
        if(count($bookings)>1) $terme = "demandes de réservation";
        $str .= '<li ><h3 style="color:#3b3b3b!important">Depuis le '.get_the_date( "j M Y", $jobID ).' :</h3></li>';
        $str .= '<li >'.count($bookings).' '.$terme.'</li>';
        

        $terme = "Réservation terminée";
        if(count($bookingsComplete)>1) $terme = "Réservations terminées";

        $str .= '<li style="color:#70af1a!important;font-size:20px;margin-bottom:10px">'.count($bookingsComplete).' '.$terme.'</li>';
         
        /*$str .= '<li style="padding:5px!important;border-radius: 5px;text-align:center;width:180px!important;background-color:#70af1a!important;color:white;font-size:20px!important;margin-top:20px;margin-bottom:30px">TOTAL : '.get_post_meta( $id, '_wc_booking_pricing', true )*count($bookingsComplete).'€</li>';*/
       /* if(count($bookingsComplete)>0) {
             $str .= '<li style="color:#70af1a!important;font-size:20px;margin-bottom:10px">'.count($bookingsComplete).' '.$terme.'</li>';
        }*/

      

        /*$terme = "Réservation à confirmer";
        if(count($bookingsPending)>1) $terme = "Réservations à confirmer";

        if (count($bookingsPending)>0) $str .= '<li style="color:#ef6600!important;font-weight:bold">'.count($bookingsPending).' '.$terme.'</li>';*/

        $terme = "Réservation confirmée";
        if(count($bookingsConfirmed)>1) $terme = "Réservations confirmées";
        if (count($bookingsConfirmed)>0) {
                $str .= '<li style="color:#3b3b3b!important;font-weight:bold">'.count($bookingsConfirmed).' '.$terme.'</li>';
        }
        $terme = "Réservation annulée";
        if(count($bookingsCancelled)>1) $terme = "Réservations annulée";
        if (count($bookingsCancelled)>0) {
            //$str .= '<li style="color:#3b3b3b!important;font-weight:bold">'.count($bookingsCancelled).' '.$terme.'</li>';
        }

        $terme = "Réservation à confirmer";
        if(count($bookingsPaid)>1) $terme = "Réservations à confirmer";
        if (count($bookingsPaid)>0) {
            $str .= '<li style="margin-bottom:20px!important">
                            <form action="reservations"  method="post" name="private_form" style="margin-top:12px;">
                                 <input type="hidden" name="job_id" value="'.$jobID.'">
                                 <input type="submit" class="button" style="width:200px;height:50px!important;background-color:#ff0000;margin:0px!important" value="'.count($bookingsPaid).' '.$terme.'">
                            </form>
                    </li>';
            
        } 
        if (count($bookingsPaid) > 0 || count($bookingsConfirmed) > 0) {
            $str .= '<li style="clear:both;padding:5px!important;border-radius: 5px;text-align:center;width:180px!important;background-color:#ef6600!important;color:white;font-size:20px!important;margin-top:20px!important;margin-bottom:30px">A VENIR : '.get_post_meta( $id, '_wc_booking_pricing', true )*(count($bookingsComplete)+count($bookingsPaid)+count($bookingsConfirmed)).'€</li>';
        }
        

        /*$terme = "Réservation payée";
        if(count($bookingsPaid)>1) $terme = "Réservations payées";

        $str .= '<li style="color:#3b3b3b!important;font-weight:bold">'.count($bookingsPaid).' '.$terme.'</li>';*/

        return $str;
}

function get_info_user ($customer_id) {
    $str = "";
    $customer = get_user_by( 'ID', $customer_id );
    $str .= "<div style='font-weight:bold;font-size:17px;margin-bottom:10px'><a href='".get_author_posts_url( $customer_id, $customer->display_name )."' target='_blank'>".$customer->display_name."</a></div>";
    


    return $str;
}

function format_the_date ($timestamp) {
        setlocale(LC_TIME, "fr_FR");

        $date_booking = strftime ("%A %d %B %Y",$timestamp);
        return $date_booking;
}

function get_the_status ($booking) {
    $str = "<div style='margin:20px'>";
    switch ( $booking->get_status()) {
                case 'unpaid':
                    $str .= "<span style='padding:0 10px 0 10px;background-color:#ff0000;border-radius:5px;font-weight:bold;color:white'>NON PAYEE</span>";
                    break;
                case 'pending-confirmation':
                    $str .= "<span style='color:#ef6600;font-weight:bold'>A CONFIRMER</span>";
                    break;
                case 'confirmed':
                    $str .= "<span style='color:#3b3b3b;font-weight:bold'>CONFIRMÉE</span>";
                    break;
                case 'cancelled':
                    $str .= "<span style='color:#3b3b3b;font-weight:bold'>ANNULÉE</span>";
                    break;
                case 'complete':
                    $str .= "<span style='color:#70af1a;font-weight:bold'>TERMINÉE</span>";
                    break;
                case 'paid':
                    $str .= "<span style='padding:0 10px 0 10px;background-color:#ff0000;border-radius:5px;font-weight:bold;color:white'>A CONFIRMER</span>";
                    break;

    }

    
    

    $str .= "</div>";
    switch ($booking->get_status()) {
                case 'unpaid':
                    $str .= "<div style='position:absolute;top:0px;width:100%;min-height:5px;background-color:#ff0000'></div>";
                    break;
                case 'pending-confirmation':
                    $str .= "<div style='position:absolute;top:0px;width:100%;min-height:5px;background-color:#ef6600'></div>";
                    break;
                case 'confirmed':
                    $str .= "<div style='position:absolute;top:0px;width:100%;min-height:5px;background-color:#3b3b3b'></div>";
                    break;
                case 'cancelled':
                    $str .= "<div style='position:absolute;top:0px;width:100%;min-height:5px;background-color:#3b3b3b'></div>";
                    break;
                case 'complete':
                    $str .= "<div style='position:absolute;top:0px;width:100%;min-height:5px;background-color:#70af1a'></div>";
                    break;
                case 'paid':
                    $str .= "<div style='position:absolute;top:0px;width:100%;min-height:5px;background-color:#ff0000'></div>";
                    break;

    }

    return $str;
}

function get_booking_ajax_by_job_id_past ($job_id) {
    
      $return_array = array();



        
        $product_id = get_post_meta( $job_id, '_id_product', true );
        
        //on récupère les réservations
        //TODO METTRE LE PRODUCT ID

        $bookings = get_bookings_by_product_id_past ($product_id);

        if (count($bookings) > 0) {
                     if (count($bookings) > 1) {
                           $str = '<h3 class="explication_filtre_past" style="margin-top:20px!important">'.count($bookings).' personnes ont déjà participées à cette activité</h3><div class="job_summary_shortcode" id="container_bookings_past" style="text-align:center;min-height:200px;width:100%!important;margin:0px 20px 0px 0px !important;padding:20px;float:left">';
                     } else {
                           $str = '<h2 class="explication_filtre_past" style="margin-top:20px!important">'.count($bookings).' personne a déjà participée à cette activité</h2><div class="job_summary_shortcode" id="container_bookings_past" style="text-align:center;min-height:200px;width:100%!important;margin:0px 20px 0px 0px !important;padding:20px;float:left">';
                     }
                    $i = 0;
                    foreach ($bookings as $booking) {
                                        
                            $str .=  '<div class="" id="container_bookings_past" style="width:200px!important;min-height:200px;float:left;text-align:center!important;margin:auto">';
                                            
                                $str .=  '<div class="roundedImage_past">'.get_avatar( $booking->customer_id, 120 ).'</div>';
                                $str .=  get_info_user ($booking->customer_id);
                                $str .=  '<div>le <b>'.format_the_date($booking->start).'</b></div>';           
                                $str .=  '</div>';
                                $i++;

                        }

                   
                    $str .=  '</div>'; 
                } else {
                     $str = '<h2 class="explication_filtre_past" style="margin-top:20px!important">Personne n\'a encore participé à votre activité</h2>';
                }

        
        return $str;
        
    }


function goach_detail_endpoint_content () {

    global $wpdb;
    $booking_id = $_GET['booking-id'];
    $status = $_GET['order_note'];

    wp_register_style('my_stylesheet', plugins_url('/css/style-dash.css', __FILE__));
    wp_enqueue_style('my_stylesheet');

    wp_enqueue_script('jQuery', 'https://code.jquery.com/jquery-1.12.4.js');
    wp_enqueue_script('script-name', WP_PLUGIN_URL.'/woo-vendors-bookings-dashboard/js/front.js?ran='.rand());
    wp_enqueue_script('jQueryEasing', 'http://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js');

    $query = "SELECT * FROM {$wpdb->posts} as posts

                            INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id

                            WHERE 1=1

                            AND posts.post_type IN ( 'wc_booking' )
                            AND posts.post_parent = ".$booking_id
                            ;


    $result = $wpdb->get_row($query);
   
    $booking = get_wc_booking ($result->ID);

    $job_id = get_post_meta( $booking->product_id, '_id_job', true );
     
     $post_booking = get_post($job_id);

    if (get_current_user_id()) {

     if (get_current_user_id() != get_post($booking->product_id)->post_author) {
        echo "<ul class='woocommerce-error' role='alert'><li><b>Vous ne pouvez pas voir cette réservation ! (p'tit malin va !)</b></li></ul>";
        return;
     }

    } else {
        echo "<ul class='woocommerce-error' role='alert'><li><b>Vous devez vous connecter pour voir cette réservation !</b></li></ul>";
        return;
    }
    $usermeta_event = get_usermeta( get_current_user_id(), '_id_event_'.$booking->start.'_'.$booking->product_id);
    
     
    //$booking->update_status("pending-confirmation", 'order_note');
    if (!empty($status)) {
       
        if ($status == "confirmed" && $booking->status != $status) {
            

            $booking->update_status($status);
            
            send_email_confirmed_to_client($booking);


            $usermeta_event = get_usermeta( get_current_user_id(), '_id_event_'.$booking->start.'_'.$booking->product_id);

            if (empty($usermeta_event)) {
                $str = '<h3 class="explication_filtre_past" style="font-size:15px;margin-top:20px!important">Les personnes ayant réservés ce jour là :</h3><div class="job_summary_shortcode" id="container_bookings_past" style="text-align:center;min-height:200px;width:100%!important;margin:0px 20px 0px 0px !important;padding:20px;float:left">';
                     
                    
                                        
                $str .=  '<div class="" id="container_bookings_past" style="width:200px!important;min-height:200px;float:left;text-align:center!important;margin:auto">';
                                            
                $str .=  '<div class="roundedImage_past">'.get_avatar( $booking->customer_id, 120 ).'</div>';
                $str .=  get_info_user ($booking->customer_id);
                                          
                $str .=  '</div>';
                $str .=  '</div>';

                $post_arr = array(
                    'post_title'   => $booking->get_product()->get_title(),
                    'post_content' => $str,
                    'post_status'  => 'publish',
                    'post_author'  => get_current_user_id(),
                    'post_type'  => 'stec_event',
              
                );
               $id_event = wp_insert_post( $post_arr );
               goach_generate_featured_image (get_the_post_thumbnail_url($job_id,'full'), $id_event);
               add_post_meta($id_event, 'visibility', 'stec_cal_default', true);
               add_post_meta($id_event, 'start_date', strftime ('%Y-%m-%d 00:05:00',$booking->start), true);
               add_post_meta($id_event, 'end_date', strftime ('%Y-%m-%d 23:55:00',$booking->end), true);
               add_post_meta($id_event, 'approved', 1, true);
               add_post_meta($id_event, 'calid', get_usermeta( get_current_user_id(), '_id_calendar'), true);
               add_post_meta($id_event, 'color', '#d13061', true);
               add_post_meta($id_event, 'icon', 'fa', true);
               add_post_meta($id_event, 'counter', 1, true);
               add_post_meta($id_event, 'images', array(get_post_thumbnail_id( $job_id )), true);
               add_post_meta($id_event, 'link', get_post_permalink( $job_id ), true);
               add_post_meta($id_event, 'location', get_post_meta( $job_id, '_job_location', true ), true);

               // on ajoute un post_meta au user pour recuperer l'event plus tard
               // post meta : _id_event_ + start booking + _ + id_product
               add_user_meta( get_current_user_id(), '_id_event_'.$booking->start.'_'.$booking->product_id, $id_event, true);




            } else {

                $event = get_post($usermeta_event);
                $content = substr($event->post_content, 0, -6);
                $str =  '<div class="" id="container_bookings_past" style="width:200px!important;min-height:200px;float:left;text-align:center!important;margin:auto">';
                                            
                $str .=  '<div class="roundedImage_past">'.get_avatar( $booking->customer_id, 120 ).'</div>';
                $str .=  get_info_user ($booking->customer_id);
                                          
                $str .=  '</div>';
                $content .= $str.'</div>';
                $my_post = array(
                      'ID'           => $usermeta_event,
                      'post_content' => $content,
                );
                wp_update_post( $my_post );

            }

            $usermeta_booking_event = get_usermeta( get_current_user_id(), '_id_booking_event_'.$booking->start.'_'.$booking->product_id);

            $str = '<h3 class="explication_filtre_past" style="font-size:15px;margin-top:20px!important">Les personnes ayant réservés ce jour là :</h3><div class="job_summary_shortcode" id="container_bookings_past" style="text-align:center;min-height:200px;width:100%!important;margin:0px 20px 0px 0px !important;padding:20px;float:left">';
                     
                    
                                        
                $str .=  '<div class="" id="container_bookings_past" style="width:200px!important;min-height:200px;float:left;text-align:center!important;margin:auto">';
                                            
                $str .=  '<div class="roundedImage_past">'.get_avatar( get_current_user_id(), 120 ).'</div>';
                $str .=  get_info_user (get_current_user_id());
                                          
                $str .=  '</div>';
                $str .=  '</div>';

                $post_arr = array(
                    'post_title'   => $booking->get_product()->get_title(),
                    'post_content' => $str,
                    'post_status'  => 'publish',
                    'post_author'  => $booking->customer_id,
                    'post_type'  => 'stec_event',
              
                );
               $id_booking_event = wp_insert_post( $post_arr );
               goach_generate_featured_image (get_the_post_thumbnail_url($job_id,'full'), $id_booking_event);
               add_post_meta($id_booking_event, 'visibility', 'stec_cal_default', true);
               add_post_meta($id_booking_event, 'start_date', strftime ('%Y-%m-%d 00:05:00',$booking->start), true);
               add_post_meta($id_booking_event, 'end_date', strftime ('%Y-%m-%d 23:55:00',$booking->end), true);
               add_post_meta($id_booking_event, 'approved', 1, true);
               add_post_meta($id_booking_event, 'calid', get_usermeta( $booking->customer_id, '_id_calendar'), true);
               add_post_meta($id_booking_event, 'color', '#32CD32', true);
               add_post_meta($id_booking_event, 'icon', 'fa', true);
               add_post_meta($id_booking_event, 'counter', 1, true);
               add_post_meta($id_booking_event, 'link', get_post_permalink( $job_id ), true);
               add_post_meta($id_booking_event, 'location', get_post_meta( $job_id, '_job_location', true ), true);
               add_post_meta($id_booking_event, 'images', array(get_post_thumbnail_id( $job_id )), true);
               
            if (empty($usermeta_booking_event)) {

                $a[] = $id_booking_event;
                add_user_meta( get_current_user_id(), '_id_booking_event_'.$booking->start.'_'.$booking->product_id, $a, true);

            } else {
                
                foreach ($usermeta_booking_event as $id_event) {
                    $event = get_post($id_event);
                    $content = substr($event->post_content, 0, -6);
                    $str =  '<div class="" id="container_bookings_past" style="width:200px!important;min-height:200px;float:left;text-align:center!important;margin:auto">';
                                                
                    $str .=  '<div class="roundedImage_past">'.get_avatar( $booking->customer_id, 120 ).'</div>';
                    $str .=  get_info_user ($booking->customer_id);
                                              
                    $str .=  '</div>';
                    $content .= $str.'</div>';
                    $my_post = array(
                          'ID'           => $id_event,
                          'post_content' => $content,
                    );
                    wp_update_post( $my_post );

                   
                }

                
                $usermeta_booking_event[] = $id_booking_event;
                update_user_meta(get_current_user_id(), '_id_booking_event_'.$booking->start.'_'.$booking->product_id, $usermeta_booking_event);
            }
            
           
         
        } else if ($status == "cancelled") {

            $booking->update_status($status);
            send_email_cancelled_to_client($booking);

            if (!empty($usermeta_event)) {

                $usermeta_booking_event = get_usermeta( get_current_user_id(), '_id_booking_event_'.$booking->start.'_'.$booking->product_id);
                $str =  '<div class="" id="container_bookings_past" style="width:200px!important;min-height:200px;float:left;text-align:center!important;margin:auto">';
                                                                        
                $str .=  '<div class="roundedImage_past">'.get_avatar( $booking->customer_id, 120 ).'</div>';
                $str .=  get_info_user ($booking->customer_id);
                                                                      
                $str .=  '</div>';

               
                if (!empty($usermeta_booking_event)) {
                    
                    //on supprime l'avatar de tous les autres events
                    foreach ($usermeta_booking_event as $id_event) {
                        $event = get_post($id_event);
                        
                        


                        $content = str_replace ($str, '', $event->post_content);

                        $my_post = array(
                              'ID'           => $id_event,
                              'post_content' => $content,
                        );
                        wp_update_post( $my_post );

                        if (get_post_meta($id_event, 'calid',true) == get_usermeta( $booking->customer_id, '_id_calendar')) {
                            $delete_event_id =$id_event;
                        }
                       
                    }

                    if (!empty($delete_event_id)) {
                        wp_delete_post($delete_event_id);
                        unset($usermeta_booking_event[array_search($delete_event_id, $usermeta_booking_event)]);
                    }
                }

                $event = get_post($usermeta_event);
                $content = str_replace ($str, '', $event->post_content);
                $my_post = array(
                              'ID'           => $event->ID,
                              'post_content' => $content,
                );

                if (count($usermeta_booking_event) > 0) {
                    wp_update_post( $my_post );
                } else {
                    wp_delete_post($event->ID);
                }
                
            }
        }
    }
    
 
    $customer = get_user_by( "ID", $booking->get_customer_id() );
    $description = get_user_meta($booking->get_customer_id(), 'description', true);
    ?>
    <div style="width:100%;position:relative">
        <h1 style="margin-bottom:50px">Réservation #<?php echo $booking->get_id(); ?></h1>
    
    <div style="position:absolute;right:0px;text-align:center;top:0px;width:300px!important">
<?php

    echo do_shortcode("[yith_render_barcode id='".$booking->get_order_id()."']");

?>

</div>

<div style="position:relative;width:100%;text-align:left">
 <div class="roundedImage" style="float:left;margin-right:40px!important"><?php echo get_avatar( $booking->customer_id, 120 ); ?></div>
 <div style="padding:30px!important" ><span style="font-size:20px;font-weight:bold;">Participant : <?php echo $customer->display_name; ?></span><div style="" ><i><?php echo $description; ?></i></div></div>
 
 <div style="clear: both;"></div>
</div>
    <table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
    <tbody>
        <tr>
            <th scope="row" style="text-align:left; border: 1px solid #eee;">Activité réservée</th>
            <td style="text-align:left; border: 1px solid #eee;"><?php echo $booking->get_product()->get_title(); ?></td>
        </tr>
        <tr>
            <th style="text-align:left; border: 1px solid #eee;" scope="row"><?php _e( 'Booking ID', 'woocommerce-bookings' ); ?></th>
            <td style="text-align:left; border: 1px solid #eee;"><?php echo $booking->get_id(); ?></td>
        </tr>
        <?php if ( $booking->has_resources() && ( $resource = $booking->get_resource() ) ) : ?>
            <tr>
                <th style="text-align:left; border: 1px solid #eee;" scope="row"><?php _e( 'Booking Type', 'woocommerce-bookings' ); ?></th>
                <td style="text-align:left; border: 1px solid #eee;"><?php echo $resource->post_title; ?></td>
            </tr>
        <?php endif; ?>
        <tr>
            <th style="text-align:left; border: 1px solid #eee;" scope="row"><?php _e( 'Booking Start Date', 'woocommerce-bookings' ); ?></th>
            <td style="text-align:left; border: 1px solid #eee;"><?php echo $booking->get_start_date(); ?></td>
        </tr>
        <tr>
            <th style="text-align:left; border: 1px solid #eee;" scope="row"><?php _e( 'Booking End Date', 'woocommerce-bookings' ); ?></th>
            <td style="text-align:left; border: 1px solid #eee;"><?php echo $booking->get_end_date(); ?></td>
        </tr>
        <?php if ( $booking->has_persons() ) : ?>
            <?php
                foreach ( $booking->get_persons() as $id => $qty ) :
                    if ( 0 === $qty ) {
                        continue;
                    }

                    $person_type = ( 0 < $id ) ? get_the_title( $id ) : __( 'Person(s)', 'woocommerce-bookings' );
            ?>
                <tr>
                    <th style="text-align:left; border: 1px solid #eee;" scope="row"><?php echo $person_type; ?></th>
                    <td style="text-align:left; border: 1px solid #eee;"><?php echo $qty; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</div>
<div style="position:relative; width:100%;margin-top:35px">
    
    <div style="width:100%">
        <a href="https://www.mylittlewe.com/my-account/messages/?fepaction=newmessage&to=<?php echo $customer->display_name; ?>" class="button" style="float:right;margin:5px">Contacter le participant</a>
        <?php if ($booking->get_status() == "pending-confirmation" || $booking->get_status() == "unpaid" || $booking->get_status() == "paid") { ?>
        
        <a href="?booking-id=<?php echo $booking_id ?>&order_note=confirmed" class="kw-update-form button_confirm" style="color:white!important;margin-bottom:20px">Confirmer la réservation</a>
        
        <?php } ?>
        <?php if (get_2daysbefore($booking) && $booking->get_status() != "cancelled" ) { ?>
        <a href="?booking-id=<?php echo $booking_id ?>&order_note=cancelled" class="button button_cancelled" style="float:right;margin:5px">Annuler la réservation</a>
        <?php } ?>
    </div>
  <div style="clear: both;"></div>

</div>
<h3>Ce jour là, le <?php echo $booking->get_start_date() ?>:</h3>
<?php
    setlocale(LC_TIME, "en_EN");
 
    echo do_shortcode('[stachethemes_ec cal='.get_usermeta( get_current_user_id(), '_id_calendar').' start_date="'.strftime ("%A %d %B %Y",$booking->start).'" view="day"]');

    ?>
    <div style="clear: both;"></div>
<?php
$customer = get_user_by( "ID", $booking->get_customer_id() );
?>
  <div style="clear: both;"></div>


    <?php


}



function send_email_cancelled_to_client ($booking) {
    $customer = get_userdata($booking->customer_id);

    $subject = "Votre réservation pour ".$booking->get_product()->get_title()." a été annulée !";
    $message = get_header_email("Zut! Votre réservation est annulée.");
    $product_id = $booking->get_product()->ID;
    $vendor_id = get_post_field( 'post_author', $product_id );
    $vendor = get_userdata( $vendor_id );
    $vendor_name = $vendor->user_login;
    $vendor = $booking->get_product();

    $message .= '
                <strong>Flûte et re-flûte : l\'hôte de l\'activité "'.$booking->get_product()->get_title().'" a annulé votre réservation.</strong>
                <br/><br/>

                Rien de grave, votre paiement à été annulé et vous avez été remboursé pour votre réservation !</strong>
                <br/>
                <h2 style="color: #a074a4; display: block;">Les conseils de l\'équipe myLittleWE</h2>

                Nous vous invitons à contacter l\'hôte si vous souhaitez absolument participer à cette activité. Pour cela rien de plus simple <a ref="https://www.mylittlewe.com/my-account/messages/?fepaction=newmessage&to='.$vendor_name.'">cliquez ici</a>
                <br/><br/>
                Nous sommes sûrs que vous trouverez une date qui vous convient à tous les deux ! 

                <br/>
                <br/>
                L\'équipe de myLittleWE vous remercie pour votre contribution.
                <br/><br/><br/>
                
               ';

    $message .= get_footer_email();

    $headers = array('Content-Type: text/html; charset=UTF-8','From: myLittleWE <infos@mylittlewe.com>');
      

      
   wp_mail($customer->user_email, $subject, $message, $headers);

}

function send_email_confirmed_to_client ($booking) {

    $customer = get_userdata($booking->customer_id);
    $job_id = get_post_meta( $booking->product_id, '_id_job', true );
    $address = get_post_meta( $job_id, 'geolocation_formatted_address', true );
    $subject = "Votre réservation pour ".$booking->get_product()->get_title()." a été confirmée !";
    $message = get_header_email("Yeah ! Votre réservation est confirmée.");
    $message .= '
                <strong>Parfait ! l\'hôte de votre activité a confirmé votre réservation !</strong>
                <br/><br/>

                Nous sommes sûrs que vous allez passer une super activité. Les détails de votre réservation sont affichés ci-dessous.
                <br/><br/><br/>

                <table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
                    <tbody>
                        <tr>
                            <th scope="row" style="text-align:left; border: 1px solid #eee;">Activité</th>
                            <td style="text-align:left; border: 1px solid #eee;">'.$booking->get_product()->get_title().'</td>
                        </tr>
                        <tr>
                            <th style="text-align:left; border: 1px solid #eee;" scope="row">Identifiant de la réservation</th>
                            <td style="text-align:left; border: 1px solid #eee;">'.$booking->get_id().'</td>
                        </tr>
                       
                        <tr>
                            <th style="text-align:left; border: 1px solid #eee;" scope="row">Date de début de la réservation</th>
                            <td style="text-align:left; border: 1px solid #eee;">'.$booking->get_start_date().'</td>
                        </tr>
                        <tr>
                            <th style="text-align:left; border: 1px solid #eee;" scope="row">Date de fin de la réservation</th>
                            <td style="text-align:left; border: 1px solid #eee;">'.$booking->get_end_date().'</td>
                        </tr>
                        <tr>
                            <th style="text-align:left; border: 1px solid #eee;" scope="row">Adresse</th>
                            <td style="text-align:left; border: 1px solid #eee;">'.$address.'</td>
                        </tr>';
                         if ( $booking->has_persons() ) : 
                            
                                foreach ( $booking->get_persons() as $id => $qty ) :
                                    if ( 0 === $qty ) {
                                        continue;
                                    }

                                    $person_type = ( 0 < $id ) ? get_the_title( $id ) :  __( 'Person(s)', 'woocommerce-bookings' );
                            
                                $message .='<tr>
                                    <th style="text-align:left; border: 1px solid #eee;" scope="row">'.$person_type.'</th>
                                    <td style="text-align:left; border: 1px solid #eee;">'.$qty.'</td>
                                </tr>';
                                endforeach; 
                        endif; 
                     $message .='</tbody>
                </table>


                <br/>
                <br/>
                L\'équipe de myLittleWE vous remercie pour votre réservation.
                <br/><br/><br/>';

$message .= get_footer_email();


$headers = array('Content-Type: text/html; charset=UTF-8','From: myLittleWE <infos@mylittlewe.com>');
      

      
   wp_mail($customer->user_email, $subject, $message, $headers);

}

add_action( 'woocommerce_account_messages_endpoint', 'goach_messages_endpoint_content' );
add_action( 'woocommerce_account_gestion-activites/reservations/detail_endpoint', 'goach_detail_endpoint_content' );
add_action( 'woocommerce_account_gestion-activites/reservations_endpoint', 'goach_reservations_endpoint_content' );
add_action( 'woocommerce_account_gestion-activites_endpoint', 'goach_my_account_endpoint_content' );



