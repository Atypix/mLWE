<?php

class AjaxEndpoint {

	public function registerActions() {
		
        foreach ( get_class_methods( $this ) as $method_name ) {
            if ( $method_name !== 'registerActions' ) {

                add_action( "wp_ajax_".$method_name, array( $this, $method_name ) );
                add_action( "wp_ajax_nopriv_".$method_name, array( $this, $method_name ) );

            }
        }
    }

 

  	public function get_booking_ajax_by_job_id () {
		
  		$return_array = array();

  		try {

	  		//on récupère l'id_du produit
	  		$job_id = $_POST['job_id'];
	  		$product_id = get_post_meta( $job_id, '_id_product', true );
	  		
	  		
	  		$bookings = get_bookings_by_product_id ($product_id);
	  		$str = "";
	  		$i = 0;
	  		foreach ($bookings as $booking) {
                            
                $str .=  '<div class="job_summary_shortcode item_booking" id="item'.$i.'" date_booking="'.$booking->start.'" status="'.$booking->status.'" style="width:240px!important;min-height:200px;float:left;text-align:center!important;margin:auto">';
                                
                    $str .=  '<div class="roundedImage">'.get_avatar( $booking->customer_id, 120 ).'</div>';
                    $str .=  get_info_user ($booking->customer_id);
                    $str .=  '<h3 style="color:#ef6600">Réservation #'.$booking->get_id().'</h3>';
                    $str .=  '<div style="color:#70af1a;font-size:40px;font-weight:bold">'.$booking->cost.'€</div>';
                    $str .=  '<div style="margin-top:20px">Réservé pour '.$booking->person_counts[0].' personne'.(( $booking->person_counts[0] > 1) ? "s" : "").',</div>';
                    $str .=  '<div>le <b>'.format_the_date($booking->start).'</b></div>';
                    $str .=  get_the_status($booking);
                    $str .=  '<form action="/my-account/gestion-activites/reservations/detail?booking-id='. $booking->order_id.'" method="post" id="choose-job-listing" style="margin-bottom:20px">';
                                
                    $str .=  '<input type="submit" name="submit_job_id" class="button" style="margin-top:10px;" value="Voir la réservation">';
                    $str .=  '</form>';
                                
                    $str .=  '</div>';
                    $i++;

            }
	  		$return_array[] = $str;
	  		$date_bookings = get_dates_for_select_booking ($bookings);

	  		$return_array[] = $date_bookings;

	  		$status = get_status_for_select_booking ($bookings);
	  		$return_array[] = $status;

	  		$return_array[] = get_booking_ajax_by_job_id_past($job_id);
        $return_array[] = $job_id;

	  		echo json_encode($return_array);

  		} catch (Exception $e) {

			echo json_encode($e->getMessage());

		}

  		die();

  	}

    public function add_date_range () {

      $product_id = $_POST['product_id'];
      $date_debut = strtotime($_POST['date_debut'].' +1 minute');
      $date_fin = strtotime($_POST['date_fin'].' -1 minute');
     
      $aHours = get_post_meta( $product_id,  '_wc_booking_availability', true );
      $a = [];
      $a['type'] = "time:range";
      $a['bookable'] = 'no';
      $a['priority'] = 1;
      $dstart = date('d-m-Y', $date_debut);
      $hstart = date('H:i', $date_debut);
      $dend = date('d-m-Y', $date_fin);
      $hend = date('H:i', $date_fin);
      $a['from'] = $hstart;
      $a['to'] = $hend;
      $a['from_date'] = $dstart;
      $a['to_date'] = $dend;

      $aHours[] = $a;
      update_post_meta( $product_id, '_wc_booking_availability', $aHours );
      $arg = array(
            'ID' => $product_id
        );
        
      wp_update_post( $arg );

      $str='';

      $aBlocs = [];

      foreach ($aHours as $hour) {
          
          if ($hour["type"] == "time:range") {
              
              $today = date("d-m-Y H:i:");
              if( strtotime($hour["to_date"]) >= strtotime($today)) {
                  $aBlocs[] = $hour;
              }
          }
      }
      $aBlocs = array_reverse ($aBlocs);
      $x=0;
      foreach ($aBlocs as $bloc) {
        $x++; 
        $class = ($x%2 == 0)? '': 'active';
        if ($x == 1) { $class = 'success'; }
        $str .= '<tr class="'.$class.'">';
            $str .= '<td>'.$bloc['from_date']." ".$bloc['from'].'</td>';
            $str .= '<td>'.$bloc['to_date']." ".$bloc['to'].'</td>';
            $str .= '<td class="delete" id_product="'.$product_id.'"  from_date="'.$bloc['from_date'].'" to_date="'.$bloc['to_date'].'" from="'.$bloc['from'].'" to="'.$bloc['to'].'" style="padding-left:30px">X</td>';
        $str .= '</tr>';
      }

      echo json_encode($str);
      die();

    }

    public function delete_date_range () {

      $product_id = $_POST['product_id'];
      $date_debut = $_POST['date_debut'];
      $date_fin = $_POST['date_fin'];
      $to = $_POST['to'];
      $from = $_POST['from'];
     
      $aHours = get_post_meta( $product_id,  '_wc_booking_availability', true );
      
      $i = 0;
      $id = -1;
      foreach ($aHours as $hour) {
        if ($hour["from_date"] == $date_debut && $hour["to_date"] == $date_fin && $hour["to"] == $to && $hour["from"] == $from) {
          $id = $i;
        }
        $i++;
      }

      if ($id >= 0) {
        array_splice($aHours, $id, 1);
      }


      update_post_meta( $product_id, '_wc_booking_availability', $aHours );
      $arg = array(
            'ID' => $product_id
        );
        
      wp_update_post( $arg );

      $str='';

      $aBlocs = [];

      foreach ($aHours as $hour) {
          
          if ($hour["type"] == "time:range") {
              
              $today = date("d-m-Y H:i");
              if( strtotime($hour["to_date"]) >= strtotime($today)) {
                  $aBlocs[] = $hour;
              }
          }
      }
      $aBlocs = array_reverse ($aBlocs);
      $x=0;
      foreach ($aBlocs as $bloc) {
        $x++; 
        $class = ($x%2 == 0)? '': 'active';
        $str .= '<tr class="'.$class.'">';
            $str .= '<td>'.$bloc['from_date']." ".$bloc['from'].'</td>';
            $str .= '<td>'.$bloc['to_date']." ".$bloc['to'].'</td>';
            $str .= '<td class="delete" id_product="'.$product_id.'"  from_date="'.$bloc['from_date'].'" to_date="'.$bloc['to_date'].'" from="'.$bloc['from'].'" to="'.$bloc['to'].'" style="padding-left:30px">X</td>';
        $str .= '</tr>';
      }

      echo json_encode($str);
      die();

    }



   



}




  	