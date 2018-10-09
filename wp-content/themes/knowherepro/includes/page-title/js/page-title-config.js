(function ($) {

	$.knowhere_page_title = $.knowhere_page_title || {};

	/*	Init
	/* --------------------------------------------- */

	$.knowhere_page_title.init = function () {

		$('#knowhere_page_title_meta_box').on('click', '.add_image_available_media', function (e) {
			$.knowhere_page_title.add_available_media(e);
		}).on('click', '.remove_image_available_media', function (e) {
			$.knowhere_page_title.remove_image(e);
		}).find('select, input').styler();

		if ( $('.knowhere-page-title-color-picker').length ) {
			$('.knowhere-page-title-color-picker').wpColorPicker();
		}

		$('#knowhere_page_title_meta_box input:radio').on('change', function () {

			var $this = $(this),
				$content = $this.parents('.settings-box').children('.settings-box-content');

			if ( $this.val() == 'none' ) {
				$('.uploader-upload-input').val('');
			}

			if ( $this.val() == 'custom' ) {
				$content.slideDown(400);
			} else {
				$content.slideUp(400);
			}

		});

		//$('.knowhere-required-section').knowhere_page_title_required();

	}

	/*	Required
	/* --------------------------------------------- */

	$.fn.knowhere_page_title_required = function () {
		return this.each(function(id, el) {
			$.knowhere_page_title.required(el);
		});
	}

	$.knowhere_page_title.required = function (el) {

		if ( !$(el).length ) return;

		return {
			element: $(el),
			init: function () {
				this.data = {
					el: this.element,
					required : $('.knowhere-type-field-required', this.element).val().split(':')
				};
				this.element.css({ display: 'none' });
				this.process();
			},
			process: function () {
				var wrapper = this.element.siblings('div[id$=prefix_' + this.data.required[0] + ']'),
					element = $(':input[name*=' + this.data.required[0] + ']', wrapper);

				if ( element.is(':checkbox') ) {
					if ( element.is(':checked') ) {
						if ( element.val() == this.data.required[1] ) {
							this.element.css({ display: 'block' });
						}
					}
				} else {
					if ( element.val() == this.data.required[1] ) {
						this.element.css({ display: 'block' });
					}
				}

				if ( element.is('input[type=hidden]') ) {
					element = element.siblings();
				}

				element.on( 'change.form_required click.form_required', this.data, this.change );
			},
			change: function (passed) {
				var current = $(this),
					data = passed.data;

				current.is(':input') ? val = current.val() : val = current.data('value');

				if ( current.is('input[type=checkbox]' ) && !current.prop('checked')) val = "";

				if ( val == data.required[1] ) {
					if ( data.el.css('display') == 'none' ) {
						data.el.slideDown(400);
					}
				} else {
					if ( data.el.css('display') == 'block' ) {
						data.el.slideUp(400);
					}
				}
			}
		}.init();

	}

	/*	Add
	/* --------------------------------------------- */

	$.knowhere_page_title.add_available_media = function (e) {

		e.preventDefault();

		var $element = $(e.target),
			$parent = $element.parents('.option-element'),
			$preview = $parent.children('.img-container'),
			$hidden = $parent.children(':hidden');

			$d_target = $(e.delegateTarget),
			type = $element.data('type'),
			nonce = $('input[name=knowhere_page_title_meta_box_nonce]', $d_target).val(),
			data = {
				action: 'knowhere_page_title',
				type: type,
				knowhere_page_title_meta_box_nonce: nonce
			};

		if ( type == 'video' ) {

			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: data,
				success: function (response) {
					$element.prev('img').find(".sortable-img-items").append(response);
				},
				complete: function () {
					setTimeout(function () {
						$('.img-preview.add_animation', $preview).removeClass('add_animation');
					}, 600);
				}
			});

			return true;
		}

		var file_frame = wp.media.frames.file_frame = wp.media({
			title: 'Choose an image',
			button:  { text: 'Use image' },
			multiple: false,
			library: { type: 'image' }
		});

		file_frame.on('select', function () {

			var attachment = file_frame.state().get('selection').first().toJSON(),
				id = attachment.id; data.id = id;

			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: data,
				success: function (response) {
					$preview.html('').append(response.html);
					$hidden.val(response.id);
				},
				complete: function() {
					setTimeout(function () {
						$('.img-preview.add_animation', $preview).removeClass('add_animation');
					}, 600);
				}
			});

		});

		file_frame.open();

	}

	/*	Remove Item
	/* --------------------------------------------- */

	$.knowhere_page_title.remove_image = function ( e ) {
		e.preventDefault();

		var $target = $(e.target),
			$parent = $target.parents('.option-element'),
			$img = $parent.find('img'),
			$hidden = $parent.children(':hidden');

		if ( !$target.length ) return;

		$img.attr('src', knowhere_page_title_vars.img);
		$hidden.val('');

	}

	/*	Ready
	/* --------------------------------------------- */

	$(function () {
		$.knowhere_page_title.init();
	});

})(jQuery);