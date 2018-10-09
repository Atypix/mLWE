

(function ($) {

	$(function() {
		if ( $('#attribute_category').length ) {

			if ( $.isFunction( $.fn.chosen ) ) {
				$('#attribute_category').chosen({
					search_contains: true
				})
			}

		}
	});

})(jQuery);