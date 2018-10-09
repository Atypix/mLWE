<?php
/*
  Plugin Name: Connect - ics
  Plugin URI: http://www.mylittlewe.com
  Description: Ce plugin permet de se connecter a un .ics pour les réservation
  Version: 0.1
  Author: Sébastien Odion
  Author URI: http://www.mylittlewe.com/
  License: Ma licence GPL2
*/

require_once 'ICal/ICal.php';
require_once 'ICal/Event.php';

use ICal\ICal;

class connectICS {

	public function __construct() {
		

		$this->path = realpath(dirname ( __FILE__ ));
		
		if (is_admin()) {
			// Add the admin menu
			add_action( 'admin_menu', array($this, 'add_menu_connect_ics'));
		}

		
	}

	static function instance() {

        global $connectICS;
        $connectICS = new connectICS();
    }

    public function add_menu_connect_ics () {


    	add_options_page( 'Connect ICS', 'Connect ICS', 'manage_options', 'connect_ics', array($this, 'initialize_connect_ics') );


  	}

  	public function initialize_connect_ics () {
  		?>
  		<h2>Bienvenue dans le plugin Connect ICS</h2>
		<p>Ce plugin permet de connecter un produit de réservation à un fichier ICS externe</p>
		<?php

		$ahours = get_post_meta( 4917,  '_wc_booking_availability', true );
		var_dump($ahours);

		$calendrier = file_get_contents('https://onebrainlille.com/?booked_ical&calendar=98&sh=6b78f17a56b5e2fcbb8045b0175a4648');

		try {
		    $ical = new ICal($calendrier, array(
		        'defaultSpan'                 => 2,     // Default value
		        'defaultTimeZone'             => 'Europe/Paris',
		        'defaultWeekStart'            => 'MO',  // Default value
		        'disableCharacterReplacement' => false, // Default value
		        'skipRecurrence'              => false, // Default value
		        'useTimeZoneWithRRules'       => true, // Default value
		    ));
		    // $ical->initFile('ICal.ics');
		    // $ical->initUrl('https://raw.githubusercontent.com/u01jmg3/ics-parser/master/examples/ICal.ics');
		} catch (\Exception $e) {
		    die($e);
		}

		//var_dump($ical);

		?>
		<div class="container-fluid">
    <h3>PHP ICS Parser example</h3>
    <ul class="list-group">
        <li class="list-group-item">
            <span class="badge"><?php echo $ical->eventCount ?></span>
            Nb d'evenements
        </li>
        
    </ul>

    <?php
        $showExample = array(
            'interval' => true,
            'range'    => true,
            'all'      => true,
        );
    ?>

    <?php
    $forceTimeZone = true;
        if ($showExample['interval']) {
            $events = $ical->eventsFromInterval('1 year');
            if ($events) {
                echo '<h4>Events in the next 1 year:</h4>';
            }
            $count = 1;
    ?>
    <div class="row">
    <?php
    foreach ($events as $event) : ?>
        <div class="col-md-4">
            <div class="thumbnail">
                <div class="caption">
                    <h3><?php
                        $dtstart = $ical->iCalDateToDateTime($event->dtstart_array[3], $forceTimeZone);
                        echo $event->summary . ' (' . $dtstart->format('d-m-Y H:i') . ')';
                        
                        
                    ?></h3>
                    <?php $a = [];
                        $a['type'] = "time:range";
                        $a['bookable'] = 'no';
                        $a['priority'] = 1;
                        $sthour= $dtstart->modify("+1 minutes");
                        $a["from"] = $sthour->format('H:i');
                        $endhour= $dtstart->modify("+88 minutes");
                        $a["to"] = $endhour->format('H:i');
                        $a["from_date"] = $dtstart->format('Y-m-d');
                        $a["to_date"] = $dtstart->format('Y-m-d');
                        $b = true;
                        foreach ($ahours as $hour) {
                        	if ($hour['type'] == 'time:range' && $hour['bookable'] == "no") {
                        		if ($hour['from_date'] == $a["from_date"] && $a["from"] == $hour['from']) {
                        			$b = false;
                        		}
                        	}
                        }

                        if ($b) {
                        	$ahours[] = $a;
                        	var_dump($ahours);
                        }
                         ?>
                    <?php echo $event->printData() ?>
                </div>
            </div>
        </div>
        <?php
            if ($count > 1 && $count % 3 === 0) {
                echo '</div><div class="row">';
            }
            $count++;
        ?>
    <?php
    endforeach
    ?>
    </div>
    <?php } 
	update_post_meta( 4917, '_wc_booking_availability', $ahours );
	$arg = array(
		    'ID' => 4917
		);
		
		wp_update_post( $arg );
    ?>

</div>

<?php


  	}





}

function synchronize_onebrain () {

    $ahours = get_post_meta( 4917,  '_wc_booking_availability', true );
    

    $calendrier = file_get_contents('https://onebrainlille.com/?booked_ical&calendar=98&sh=6b78f17a56b5e2fcbb8045b0175a4648');

    try {
        $ical = new ICal($calendrier, array(
                'defaultSpan'                 => 2,     // Default value
                'defaultTimeZone'             => 'UTC',
                'defaultWeekStart'            => 'MO',  // Default value
                'disableCharacterReplacement' => false, // Default value
                'skipRecurrence'              => false, // Default value
                'useTimeZoneWithRRules'       => false, // Default value
            ));
            // $ical->initFile('ICal.ics');
            // $ical->initUrl('https://raw.githubusercontent.com/u01jmg3/ics-parser/master/examples/ICal.ics');
    } catch (\Exception $e) {
            die($e);
    }

     $showExample = array(
            'interval' => true,
            'range'    => true,
            'all'      => true,
        );

     if ($showExample['interval']) {
            $events = $ical->eventsFromInterval('1 year');
            $count = 1;
    }


    foreach ($events as $event) {
        
        $a = [];
        $a['type'] = "time:range";
        $a['bookable'] = 'no';
        $a['priority'] = 1;
        $sthour= $dtstart->modify("+1 minutes");
        $a["from"] = $sthour->format('H:i');
        $endhour= $dtstart->modify("+88 minutes");
        $a["to"] = $endhour->format('H:i');
        $a["from_date"] = $dtstart->format('Y-m-d');
        $a["to_date"] = $dtstart->format('Y-m-d');
        $b = true;
        foreach ($ahours as $hour) {
             if ($hour['type'] == 'time:range' && $hour['bookable'] == "no") {
                 if ($hour['from_date'] == $a["from_date"] && $a["from"] == $hour['from']) {
                    $b = false;
                }
            }
        }

        if ($b) {
            $ahours[] = $a;
                            
        }
                         
        
    }

    update_post_meta( 4917, '_wc_booking_availability', $ahours );
    $arg = array(
            'ID' => 4917
    );
        
    wp_update_post( $arg );

}

add_action( 'plugins_loaded', array( 'connectICS', 'instance' ) );