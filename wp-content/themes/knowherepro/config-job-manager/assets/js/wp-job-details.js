(function ($) {

	$.knowhere_wp_job_details = $.knowhere_wp_job_details || {};

	$.knowhere_wp_job_details.init = function () {

		var template = $('#knowhere-tmpl-details-field').html(),
			box = $('.job-field-custom-box-holder');

		if ( !box.length ) return;

		box.sortable({
			placeholder: 'job-details-tab-highlight',
			handle: ".job-details-handle-area"
	});

		({
			init: function () {
				this.listeners();
			},
			listeners: function () {
				var base = this;

				$('.kw-select-group-inner').on('click', '.add-job-field-tab', function (e) {
					e.preventDefault();

					var rString = base.randomString(5, 'abcdefghijklmnopqrstuvwxyz'),
						html = template.replace(/__REPLACE_SSS__/gi, rString);

					newTemplate = $(html).appendTo(box).css({ display: "none" });

					var id = rString, settings = { id : id };

					quicktags(settings);
					QTags._buttonsInit();
					tinymce.execCommand( 'mceRemoveEditor', false, id );
					tinymce.execCommand( 'mceAddEditor', false, id );

					newTemplate.slideDown(200);

				}).on('click', '.remove-job-field-tab', function (e) {
					e.preventDefault();
					var $this = $(this),
						$item = $this.parents('li');
					$item.slideUp(200, function () { $item.remove(); });
				});
			},
			randomString: function ( length, chars ) {
				var result = '';
				for ( var i = length; i > 0; --i ) result += chars[Math.round(Math.random() * (chars.length - 1))];
				return result;
			}
		}).init();
	}

	$(function() {

		if ( $('[name="knowhere_job_listing_term_meta[bg_color_label]"]').length ) {
			$('[name="knowhere_job_listing_term_meta[bg_color_label]"]').wpColorPicker();
		}

		$.knowhere_wp_job_details.init();
	});

})(jQuery);