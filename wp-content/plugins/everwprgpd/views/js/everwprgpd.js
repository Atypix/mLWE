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

	var form = jQuery('#everwprgpdform');
	
	form.submit(function(e) {
	    var everofficername = jQuery('#everofficername').val();
	    var everofficeremail = jQuery('#everofficeremail').val();
	    var everwprgpdlegalmentions = jQuery('#everwprgpdlegalmentions').val();
	    var everrgpdmessage = tinymce.activeEditor.getContent();
	    jQuery.ajax({
	      url: ajax_object.ajaxurl,
	      type: 'POST',
	      data:{
	        action: 'everrgpdbackcallbacks',
	        everofficername: everofficername,
	        everofficeremail: everofficeremail,
	        everwprgpdlegalmentions: everwprgpdlegalmentions,
	        everrgpdmessage: everrgpdmessage
	      },
	      success: function( data ){
	        jQuery('#everwpsuccess').slideDown();
	        setTimeout(function(){ location.reload(); }, 2000);
	        // form.slideUp();
	      }
	    });
	    e.preventDefault();
  	});

	jQuery('.rgpdbtn').click(function() {

		var format = jQuery(this).attr("data-format");

		jQuery.ajax({
		  url: ajax_object.ajaxurl,
		  type: 'POST',
		  data:{
		    action: 'everrgpdfrontcallbacks',
		    format: format
		  },
		  success: function( data ){
		    console.log(data);
		    window.open(data, '_self');
		  }
		});

	});

});