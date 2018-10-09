(function ($, window) {

	function knowhere_sidebar_generator () {
		var base = this;
			base.widgetArea = $('#widgets-right');
			base.widgetTemplate  = $('#knowhere-tmpl-add-widget');
			base.widgetLiquid = $('.widget-liquid-right');

			base.createForm();
			base.createRemoveButton();
			base.eventListeners();
	}

	knowhere_sidebar_generator.prototype = {
		createForm: function ()  {
            this.widgetLiquid.append(this.widgetTemplate.html());
            this.nonce = this.widgetLiquid.find('input[name="knowhere-custom-sidebar-nonce"]').val();
        },
		createRemoveButton: function () {
            this.widgetArea.find('.sidebar-knowhere-widget-custom').append('<span class="custom-sidebar-delete dashicons-trash"></span>');
        },
        eventListeners: function () {
            this.widgetLiquid.on('click', '.custom-sidebar-delete', $.proxy(this, 'deleteSidebar'));
        },
        deleteSidebar: function (e) {
        	var deleteWidget = confirm("Do you really want to delete this Widget Area?");
			if (deleteWidget) {
				var base = this,
					widget = $(e.currentTarget).parents('.widgets-holder-wrap:eq(0)'),
					title = widget.find('.sidebar-name h2'),
					widgetName = $.trim(title.text());
				$.ajax({
					type: "POST",
					url: window.ajaxurl,
					data: {
						action: 'delete_custom_sidebar',
						name: widgetName,
						_wpnonce: base.nonce
					},
					success: function (response) {
						if (response == 'widget-deleted')  {
							widget.slideUp(250, function(){
								widget.remove();
								wpWidgets.saveOrder();
							});
						}
					}
				});
			}
        }
    
    }
	
	$(function () {
		new knowhere_sidebar_generator();
 	});

})(jQuery, window);