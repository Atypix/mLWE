
jQuery( document ).ready(function() {

	
	//console.log(jQuery('.bookable').first().find('.ui-state-default').text());
	jQuery('.bookable').each(function (index) {
			console.log(index);
		if (jQuery(this).hasAttribute("data-event")) {
			console.log("coucou : " + jQuery(this).find("a").text());
		}

	});
  
});
