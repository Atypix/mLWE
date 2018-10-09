
(function ($) {

	$.knowhere_widget_metaBox = function () {
		return ({
			init: function () {
				var base = this;
				base.widgetTemplate = $('#knowhere-tmpl-options-hidden');
				base.listeners();
			},
			listeners: function () {
				var base = this,
					obj = $.parseJSON(base.widgetTemplate.html());

				$(".meta-list-set ul.options-columns").on('click', 'li', function () {
					var $this = $(this),
						val = $this.data('val'),
						container = $this.closest('.meta-set'),
						hidden = container.find('.data-widgets-hidden'),
						display = container.find('.meta-columns-set');

					var newValue = {};
					newValue[val] = [obj[val][0]];
					hidden.attr('data-columns', val).attr('value', JSON.stringify(newValue));

					$this.siblings('li').removeClass('active').end().addClass('active');

					display.children().removeClass('hidden');
					display.children().slice(val).addClass('hidden');

					for (i = 0; i < obj[val][0].length; i++) {
						display.find('.mod-columns:nth-child(' + (i + 1) + ')').attr("class", "mod-columns mod-grid-" + obj[val][0][i] + "");
					}

					container.find('.options-type').html('');

					for (i = 0; i < obj[val].length; i++) {
						var class_active = '';
						if (i == 0) { class_active = 'class="active"'; }
						container.find('.options-type').append('<li data-val="' + (i + 1) + '" ' + class_active + '>' + (i + 1) + '</li>');
					}

					return false;
				});

				$(".meta-list-set ul.options-type").on('click', 'li', function () {
					var $this = $(this),
						val = $this.data('val'),
						container = $this.closest('.meta-set'),
						hidden = container.find('.data-widgets-hidden'),
						display = container.find('.meta-columns-set'),
						all_val = hidden.data('columns');

					var newValue = {};
					newValue[all_val] = [obj[all_val][(val - 1)]];

					hidden.val(JSON.stringify(newValue));

					$this.siblings('li').removeClass('active').end().addClass('active');

					display.children().removeClass('hidden');
					display.children().slice(val).addClass('hidden');

					for (i = 0; i < obj[all_val][(val - 1)].length; i++) {
						display.find('.mod-columns:nth-child(' + (i + 1) + ')').attr("class", "mod-columns mod-grid-" + obj[all_val][(val - 1)][i] + "");
					}

					return false;
				});

				$('[name="footer_row_top_show"], [name="footer_row_middle_show"]').on('click', function () {
					var $this = $(this),
						value = $this.val(),
						toggleContainer = $('.' + $this.attr('name'));

					if ( value == 1 ) {
						toggleContainer.slideDown(300);
					} else {
						toggleContainer.slideUp(300);
					}
				});

			}
		}.init());

	}

	$(function() {
		new $.knowhere_widget_metaBox();
	});

})(jQuery);