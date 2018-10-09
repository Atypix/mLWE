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
jQuery(document).ready(function(){

  jQuery('.rgpdbtn').click(function() {

    var userid = jQuery(this).attr("data-user");
    var format = jQuery(this).attr("data-format");

    jQuery.ajax({
      url: ajax_object.ajaxurl,
      type: 'POST',
      data:{
        action: 'everrgpdfrontcallbacks',
        userid: userid,
        format: format
      },
      success: function( data ){
        console.log(data);
        window.open(data, '_self');
      }
    });

  });

});