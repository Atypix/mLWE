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
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>

<div class="wrap">

    <div id="poststuff">

      <div id="post-body" class="metabox-holder columns-2">

        <div id="post-body-content">

        <div id="normal-sortables" class="meta-box-sortables ui-sortable">

          <div class="jumbotron">

            <h1 class="display-3"><?php _e('European data protection law (GDPR)', 'everwprgpd'); ?></h1>

            <a href="https://www.team-ever.com" target="_blank"><img src="https://www.team-ever.com/wp-content/uploads/2016/08/Logo-full.png" style="float:left;"></a>

            <p class="lead"><?php _e('Please read', 'everwprgpd'); ?> <a href="https://ec.europa.eu/info/law/law-topic/data-protection_en" target="_blank"><?php _e('official european law', 'everwprgpd'); ?></a> <?php _e('before using current plugin.', 'everwprgpd'); ?></p>

            <p class="lead"><?php _e('Note that this current plugin is only here to help you, you will be able to export customers data, specify Data Protection Officer, check for pages without SSL, add specific message showed in front-office, allow customers extract in JSON/XML format their own personnal data and send you a specific message', 'everwprgpd'); ?></p>

            <p><?php _e('Use [everrgpd] shortcode to add customer data extract form on front-office', 'everwprgpd'); ?></p>

            <p><?php _e('You can add your own design by using custom.css file in /views/css folder in current plugin :-)'); ?></p>

            <p><?php _e('Thanks for using <a href="https://www.team-ever.com" target="_blank">Team Ever\'s plugins', 'everwprgpd'); ?></a> !</p>

            <button type="button" class="btn btn-info rgpdbtn" data-format="json"><?php _e('Export all users data in JSON', 'everwprgpd'); ?></button>
            <button type="button" class="btn btn-info rgpdbtn" data-format="xml"><?php _e('Export all users data in XML', 'everwprgpd'); ?></button>
            <button type="button" class="btn btn-info rgpdbtn" data-format="csv"><?php _e('Export all users data in CSV', 'everwprgpd'); ?></button>

          </div>

          <div class="row marketing">

            <div class="col-lg-12">

              <h4><?php _e('Data protector officer'); ?></h4>

              <form id="everwprgpdform">
                <div class="form-group">
                  <label for="everofficername"><?php _e('Please insert data protection officer name', 'everwprgpd'); ?></label>
                  <input type="text" class="form-control" id="everofficername" name="everofficername" placeholder="<?php _e('Data protection officer name', 'everwprgpd'); ?>" value="<?php echo get_option('everwprgpdeverofficername'); ?>">
                </div>

                <div class="form-group">
                  <label for="everwprgpdlegalmentions"><?php _e('Please insert here URL of your legal mentions', 'everwprgpd'); ?></label>
                  <input type="text" class="form-control" id="everwprgpdlegalmentions" name="everwprgpdlegalmentions" placeholder="<?php _e('URL of legal mentions', 'everwprgpd'); ?>" value="<?php echo get_option('everwprgpdlegalmentions'); ?>" required>
                </div>

                <div class="form-group">
                  <label for="everofficeremail"><?php _e('Please insert data protection officer email', 'everwprgpd'); ?></label>
                  <input type="email" class="form-control" id="everofficeremail" name="everofficeremail" placeholder="<?php _e('Data protection officer email', 'everwprgpd'); ?>" value="<?php echo get_option('everwprgpdeverofficeremail'); ?>" required>
                </div>

                <div class="form-group">
                  <label for="everrgpdfrontmessage"><?php _e('Front office message', 'everwprgpd'); ?></label>
                  <?php
                  $content = get_option('everwprgpdeverrgpdmessage');
                  $args = array(
                      'textarea_rows' => 15,
                      'teeny' => true,
                      'quicktags' => false
                  );
                  $editor_id = 'everrgpdfrontmessage';
                  wp_editor( $content, $editor_id, $args );
                  ?>
                </div>
                <input type="submit" class="btn btn-info" value="<?php _e('Save', 'everwprgpd'); ?>">
              </form>
            </div>

            <div id="everwpsuccess" class="alert alert-success" style="display:none;"><?php _e('Settings saved successfully', 'everwprgpd'); ?></div>
            <div id="everwperror" class="alert alert-warning" style="display:none;"><?php _e('An error has occured, please check console', 'everwprgpd'); ?></div>

            <div class="col-lg-12 mt-3">

              <h4><?php _e('URL in database without SSL protection', 'everwprgpd'); ?></h4>

              <p><?php _e('If you don\'t know how to change this errors, feel free to contact us at ', 'everwprgpd'); ?><a href="mailto:contact@team-ever.com?subject=How to change URL in my own database ?">contact@team-ever.com</a></p>

              <?php
              global $wpdb;
              $results = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.'posts WHERE INSTR(guid, "http://") > 0', OBJECT );
              if (!empty($results)) {
                foreach($results as $row)
                {
                  echo "<p><a href='".$row->guid."' target='_blank'>".$row->guid."</a></p>";
                }
              } else {
                echo _e('Congratulations ! There is no SSL error in your posts', 'everwprgpd');
              }
             ?>
            </div>

            <div class="col-lg-12 mt-3">
              <h4><?php _e('Database prefix'); ?></h4>
              <p><?php _e('Do not use default WordPress database prefix', 'everwprgpd'); ?></p>

              <?php
              if ($wpdb->prefix == 'wp_') {
                echo _e('Your database prefix is default. Please change this after saving all datas', 'everwprgpd');
              } else {
                echo _e('Database prefix has been changed, congratulations !', 'everwprgpd');
              }
             ?>

            </div>

            <div class="col-lg-12 mt-3">

              <h4><?php _e('Admin user'); ?></h4>

              <p><?php _e('You should ban all users named "admin". Here\'s a list of all users using admin nicename', 'everwprgpd'); ?></p>

              <?php
              global $wpdb;
              $results = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.'users WHERE user_nicename = "admin"', OBJECT );
              if (!empty($results)) {
                foreach($results as $row)
                {
                  echo "<p>".$row->user_email."</p>";
                }
              } else {
                echo _e('No admin nicename set in database.', 'everwprgpd');
              }
             ?>

            </div>

            <footer class="text-muted ">
              <div class="container">
                <p><?php _e('Want to secure more your own WordPress or WooCommerce ?'); ?> <a href="https://www.team-ever.com" target="_blank"><?php _e('See our latests posts on our website !'); ?></a></p>
              </div>
            </footer>

          </div>

        </div>

        </div>

      </div>

  </div>      

</div>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>