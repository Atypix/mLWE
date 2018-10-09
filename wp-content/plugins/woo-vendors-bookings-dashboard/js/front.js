jQuery(document).ready(function(){
	var bookings;
	var dateSelected = "tous";
	var statusSelected = "tous";
	create_sentence();

	jQuery( ".button_confirm" ).click (function () {
		if (confirm("Vous désirez vraiment confirmer cette réservation ?")) {
		    window.location.href = jQuery(this).attr('href');
		  } else {
		  	return false;
		  }
		 
	});

	jQuery( ".button_cancelled" ).click (function () {
		if (confirm("Vous désirez vraiment annuler cette réservation ?")) {
		    window.location.href = jQuery(this).attr('href');
		  } else {
		  	return false;
		  }
		 
	});
	jQuery( "#job_listings" ).change(function() {

	 
		var optionSelected = jQuery(this).find("option:selected");
	    var valueSelected  = optionSelected.val();

	    
	    
	    jQuery( "#container_bookings" ).empty();
	    jQuery( "#container_bookings" ).append('<div class="loader_activite" ></div>');
	    jQuery('#job_date').empty();
	    jQuery('#job_status').empty();
	    jQuery(".explication_filtre").empty().append("Chargement...");
	    jQuery('#container_past').empty();
	    var data = {
		    action: 'get_booking_ajax_by_job_id',
		    security : MyAjax.security,
		    'job_id': valueSelected,
		    dataType: "json"
	 	 };

		  jQuery.post(MyAjax.ajaxurl, data, function(response) {
		  	console.log(response);
		  	enabled_form ();
		  	jQuery( "#container_bookings" ).empty();
		    var a = jQuery.parseJSON(response);
		    jQuery( "#container_bookings" ).append(a[0]);
		    jQuery('#job_date').append(a[1]);
	    	jQuery('#job_status').append(a[2]);
	    	bookings = a[3];
	    	create_sentence();
	    	jQuery('#container_past').empty().append(a[3]);
		  });


		disabled_form ();
	 
	});

	jQuery('#job_date').change(function() {
		var optionSelected = jQuery(this).find("option:selected");
	    dateSelected  = optionSelected.val();
		check_filter ();

	});
	jQuery('#job_status').change(function() {
		var optionSelected = jQuery(this).find("option:selected");
	    statusSelected  = optionSelected.val();
		check_filter ();

	});

	function check_filter () {

		jQuery( ".item_booking" ).each (function () {
			jQuery( this ).show();
		});
		if (dateSelected != "tous") {
			jQuery( ".item_booking" ).each (function () {
				if (dateSelected != jQuery( this ).attr("date_booking") && dateSelected != "tous") {
					jQuery( this ).hide();
				}
			});
		}
		if (statusSelected != "tous") {
			jQuery( ".item_booking" ).each (function () {
				if (statusSelected != jQuery( this ).attr("status") && statusSelected != "tous") {
					jQuery( this ).hide();
				}
			});
		}

		create_sentence();
	}

	function create_sentence () {
		var i = 0;
		jQuery( ".item_booking" ).each (function () {
			if( jQuery(this).is(":visible") ){
			    i++;
			} 
		});

		var optionSelected = jQuery("#job_listings").find("option:selected");
	    
	     var textSelected   = optionSelected.text();

	     str = i +" résultat pour l'activité " + textSelected;

	     if (i > 1) {
			str = i +" résultats pour l'activité " + textSelected;
	     }

	     var optionSelected = jQuery("#job_date").find("option:selected");
	    
	     var textSelected   = optionSelected.text();

	     if (textSelected != "Toutes les dates") {
	     	str += ", le " + textSelected;
	     }

	      var optionSelected = jQuery("#job_status").find("option:selected");
	    
	     var textSelected   = optionSelected.text();

	     if (textSelected != "Tous les status") {
	     	str += ", avec pour status : " + textSelected;
	     }

	     jQuery(".explication_filtre").empty().append(str);

	}


	function disabled_form () {
		jQuery( ".postform" ).prop( "disabled", true );
		jQuery( ".postform" ).css( "opacity", .5 );
		
	}

	function enabled_form () {
		jQuery( ".postform" ).prop( "disabled", false );
		jQuery( ".postform" ).css( "opacity", 1 );
		
	}


	init_delete ();
	



});

function init_delete () {
	jQuery('.delete').click (function (e) {
		

		var temp = "Vous désirez vraiment supprimer cette période d'indisponibilité ?"
		if (confirm(temp))
		{
		  var date_debut = jQuery(this).attr( "from_date" );
		  var date_fin = jQuery(this).attr( "to_date" );
		  var from = jQuery(this).attr( "from" );
		  var to = jQuery(this).attr( "to" );
		  var product_id = jQuery(this).attr( "id_product" );
		  
		  	jQuery('#submit_blocage').hide();
			jQuery("#datetimepicker6").find(':input').prop('disabled', true);
			jQuery("#datetimepicker7").find(':input').prop('disabled', true);
			var data = {
			    action: 'delete_date_range',
			    security : MyAjax.security,
			    'date_debut': date_debut,
			    'date_fin': date_fin,
			    'from': from,
			    'to': to,
			    'product_id' : product_id,
			    dataType: "json"
		 	 };

			  jQuery.post(MyAjax.ajaxurl, data, function(response) {
			  	console.log(response);
			  	jQuery('#submit_blocage').show();
				jQuery("#datetimepicker6").find(':input').prop('disabled', false);
				jQuery("#datetimepicker7").find(':input').prop('disabled', false);
				jQuery("#datetimepicker6").data("DateTimePicker").clear();
				jQuery("#datetimepicker7").data("DateTimePicker").clear();
				jQuery( "#container-bloc" ).empty();
				jQuery( "#message-bloc").hide();
		    	var a = jQuery.parseJSON(response);
		    	jQuery( "#container-bloc" ).append(a);
		    	init_delete ();
			  });

		}
	})
}

function verifForm(f) {

		var product_id = jQuery("#product_id").val();
		var date_debut = jQuery("#datetimepicker6").find("input").val();
		var date_fin = jQuery("#datetimepicker7").find("input").val();
		

		if (date_debut != '' && date_fin != '')  {
			jQuery('#submit_blocage').hide();
			jQuery("#datetimepicker6").find(':input').prop('disabled', true);
			jQuery("#datetimepicker7").find(':input').prop('disabled', true);
			var data = {
			    action: 'add_date_range',
			    security : MyAjax.security,
			    'date_debut': date_debut,
			    'date_fin': date_fin,
			    'product_id' : product_id,
			    dataType: "json"
		 	 };

			  jQuery.post(MyAjax.ajaxurl, data, function(response) {
			  	console.log(response);
			  	jQuery('#submit_blocage').show();
				jQuery("#datetimepicker6").find(':input').prop('disabled', false);
				jQuery("#datetimepicker7").find(':input').prop('disabled', false);
				jQuery("#datetimepicker6").data("DateTimePicker").clear();
				jQuery("#datetimepicker7").data("DateTimePicker").clear();
				jQuery( "#container-bloc" ).empty();
				jQuery( "#message-bloc").hide();
		    	var a = jQuery.parseJSON(response);
		    	jQuery( "#container-bloc" ).append(a);
		    	init_delete ();
			  });
		} else {
			alert ("Vous devez remplir une date de début et une date de fin pour bloquer des disponibilités !")
		}


		return false;
}



