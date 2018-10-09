/*global $, jQuery, document, window*/

jQuery('document').ready(function (){
	jQuery('#the-list').on('click', '.edit-event', function (e) {
		e.preventDefault();
		jQuery('#event-dialog').dialog();
		});
});
