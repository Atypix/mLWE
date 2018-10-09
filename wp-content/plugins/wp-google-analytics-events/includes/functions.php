<?php

add_action("wp_ajax_add_wpgae_event", "add_wpgae_event_action");

function add_wpgae_event_action() {

  check_ajax_referer( 'wpgae_nonce_CRX0XDPfqe5dd3P', 'security' );

  $response = array();
  $response['success'] = false;

  $event = strtolower ( sanitize_text_field( $_POST['event'] ) );

  $input = stripslashes($_POST['input']);
  $decoded = json_decode($input);

  if( isValidEvent($event) ) {
    $response = ajax_add($event, $decoded);
  }


  echo json_encode($response);
  die();
}

/*
    description:
        function to add new tracking on both click and scroll event
        TODO: create separate function for each event when inputs needed will no 
        longer the same
    param:
        $data array
    return: 
        array     
*/
function ajax_add($event, $data){

  $options = get_option('ga_events_options');
  $response = array();

  $cleanData = cleanAjaxFeilds($data);
  $newEntry = extractEventData($event, $cleanData);

  if (!isset($options[$event])) {
    $options[$event] = array();
  }

  array_push($options[$event], $newEntry);
  if (sizeof($options[$event][0][0]) == 0 && sizeof($options[$event][0][1]) == 0) {
    array_shift($options[$event]);
  }


  //Remove sanitizing for adding
  remove_filter( "sanitize_option_ga_events_options", "ga_events_validate" );

  $response['success'] = update_option( 'ga_events_options', $options );

  return $response;
}


add_action("wp_ajax_update_wpgae_event", "update_wpgae_event_action");

function update_wpgae_event_action() {

  check_ajax_referer( 'wpgae_nonce_CRX0XDPfqe5dd3P', 'security' );

  $response = array();
  $response['success'] = false;

  $event = strtolower ( sanitize_text_field( $_POST['event'] ) );

  $index = intval( $_POST['index'] );

  //index should not be null or empty and non-negative, 0 should be treated as int and not boolean false
  if ( ( !$index && $index != 0 ) || $index < 0 ) {
    $event = 'INVALID';
  }

  $input = stripslashes($_POST['input']);
  $decoded = json_decode($input);

  if( isValidEvent($event) ) {
    $response = ajax_update($event, $index, $decoded);
  }

  echo json_encode($response);
  die();
}

function ajax_update($event, $index, $data){

  $options = get_option('ga_events_options');
  $response =  array();
  $cleanData = cleanAjaxFeilds($data);
  $newEvent = extractEventData($event, $cleanData);

  $options[$event][$index] = $newEvent;

  //Remove sanitizing for updating
  remove_filter( "sanitize_option_ga_events_options", "ga_events_validate" );

  $response['success'] = update_option( 'ga_events_options', $options );
  return $response;
}

add_action("wp_ajax_remove_wpgae_event", "remove_wpgae_event_action");
function remove_wpgae_event_action() {

  check_ajax_referer( 'wpgae_nonce_CRX0XDPfqe5dd3P', 'security' );

  $response = array();
  $response['success'] = false;
  $event = strtolower ( sanitize_text_field( $_POST['event'] ) );
  $index = intval( $_POST['index'] );

  //index should not be null and non-negative, 0 should be treated as int and not boolean false
  if ( ( !$index && $index != 0 ) || $index < 0 ) {
    $event = 'INVALID';
  }

  if( isValidEvent($event) ) {
    $response = ajax_remove($event,$index);
  }

  echo json_encode($response);
  die();
}

function ajax_remove($event, $index){
  $options = get_option('ga_events_options');
  $response = array();


  unset($options[$event][$index]);
  $options[$event] = array_values(array_filter($options[$event])); //re-index array

  //Remove sanitizing for adding
  remove_filter( "sanitize_option_ga_events_options", "ga_events_validate" );

  $response['success'] = update_option( 'ga_events_options', $options );
  return $response;
}

function isValidEvent($input){

  $valid = array('click','divs','youtube','vimeo');

  return in_array($input, $valid);
}

function extractEventData($event, $data){

  $extracted = array();
  switch ($event) {
    case 'click':
      $extracted = array(
        $data['name'],
        $data['type'],
        $data['category'],
        $data['action'],
        $data['label'],
        $data['interaction'],
        $data['value']
      );
      break;
    case 'divs':
      $extracted = array(
        $data['name'],
        $data['type'],
        $data['category'],
        $data['action'],
        $data['label'],
        $data['interaction'],
        $data['value'],
        // $data['tracktime'],
        //$data['time']

      );
      break;
    case 'youtube':
      $extracted = array(
        'title' => $data['title'],
        'id' => $data['id'],
        'id_type' => $data['type'],
        'play' => $data['play'],
        'pause' => $data['pause'],
        'end' => $data['end'],
        'quality' => $data['quality']
      );
      break;
    case 'vimeo':
      $extracted = array(
        'title' => $data['title'],
        'id' => $data['id'],
        'id_type' => $data['type'],
        'play' => $data['play'],
        'pause' => $data['pause'],
        'end' => $data['end'],
        'skip' => $data['skip']
      );
      break;

    default:
      // do nothing
      break;
  }
  return $extracted;
}

function doLog($text){
  // open log file
  $filename = "event.log";
  $fh = fopen($filename, "a") or die("Could not open log file.");
  fwrite($fh, date("d-m-Y, H:i")." - $text\n") or die("Could not write file!");
  fclose($fh);
}


function migrateOptions() {
  $current_options = get_option('ga_events_options');
  $new_options = array();
  $new_options['id'] = $current_options['id'];
  $new_options['exclude_snippet'] = $current_options['exclude_snippet'];
  $new_options['universal'] = $current_options['universal'];
  $new_options['events'] = array();
  for ($i=0; $i < sizeof($current_options['divs']); $i++) {
    $current_option = $current_options['divs'][$i];
    $event = new Event('scroll', $current_option[0], $current_option[1], $current_option[2], $current_option[3], $current_option[4]);
    array_push($new_options['events'], $event->getEventArray() );
  }

  for ($i=0; $i < sizeof($current_options['click']); $i++) {
    $current_option = $current_options['click'][$i];
    $event = new Event('click', $current_option[0], $current_option[1], $current_option[2], $current_option[3], $current_option[4]);
    array_push($new_options['events'], $event->getEventArray() );
  }

  update_option('ga_events_options', $new_options);
//	print(var_dump($new_options));
}


function isOptionMigrationRequired(){
  $current_options = get_option('ga_events_options');
  if (array_key_exists('divs', $current_options) || array_key_exists('clicks', $current_options)) {
    return true;
  }
  return false;
}

function is_advanced_mode(){
  $ga_events_options =  get_option( 'ga_events_options' );
  return $ga_events_options['advanced'];
}

function is_advanced_type($type){
  return 'advanced' == $type;
}

function createDropdown($name, $id, $options = array(), $selected = 'unknown'){
  $html = '';
  if(!empty($options)){
    $html .= "<select id='$id' name='$name'>";

    if(!in_array($selected, $options)){
      // even advanced mode is off, 'avanced' should still be accepted as valid option
      if('advanced' == $selected){
        $options['advanced'] = 'advanced';
      }else{
        $selected = reset($options); // set first element's key to be default
      }
    }

    foreach ($options as $key => $value){
      $html .= $selected == $key ? "<option selected value='$key' >$value</option>":"<option  value='$key' >$value</option>";
    }

    $html .= "</select>";
  }
  return $html;
}

?>
