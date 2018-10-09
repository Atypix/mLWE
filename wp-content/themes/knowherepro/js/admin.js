(function ($) {

	$.knowhere_demo = function () {
		return {
			init: function () {
				var base = this;

				base.demosContainer = $('.mad-install-demos');
				base.demosOptionsContainer = $('#mad-install-options', base.demosContainer);
				base.demoType = $('#knowhere-install-demo-type', base.demosContainer);
				base.buttonInstallDemo = $('.button-install-demo', base.demosContainer);
				base.events();
			},
			events: function () {

				var base = this;

				base.demosContainer.on( 'click', '.button-install-demo', function( e ) {
					e.preventDefault();

					var $this = $(this),
						selected = $this.data('demo-id'),
						disabled = $this.attr('disabled');

					if ( disabled ) { return; }

					base.add_alert_leave_page();

					base.demoType.val(selected);
					$('.theme-name', base.demosOptionsContainer).html($this.closest('.theme-wrapper').find('.theme-name').html());
					base.demosOptionsContainer.slideDown();

					$('html, body').stop().animate({
						scrollTop: base.demosOptionsContainer.offset().top - 60
					}, 600);

				});

				$('#knowhere-import-no').on( 'click', function( e ) {
					e.preventDefault();
					base.demosOptionsContainer.slideUp();
					base.remove_alert_leave_page.call(base);
				});

				// import
				$('#knowhere-import-yes').on( 'click', function( e ) {
					e.preventDefault();

					var button = $(this),
						demo = base.demoType.val(),
						path = button.data('path'),
						options = {
							parent: $('#knowhere-demo-' + demo),
							demo: demo,
							path: path,
							reset_menus: $('#knowhere-reset-menus').is(':checked'),
							import_dummy: $('#knowhere-import-dummy').is(':checked'),
							import_widgets: $('#knowhere-import-widgets').is(':checked'),
							import_options: $('#knowhere-import-options').is(':checked')
						};

					base.demosOptionsContainer.slideUp();

					if ( options.demo ) {
						base.import_process.call( base, options );
					}

				});

			},
			add_alert_leave_page : function() {
				this.buttonInstallDemo.attr('disabled', 'disabled');
			},
			remove_alert_leave_page : function() {
				var base = this;
				base.buttonInstallDemo.removeAttr('disabled');
			},
			import_process: function ( options ) {
				var base = this,
					data = {
						'action': 'knowhere_import_dummy',
						'demo': options.demo,
						'path': options.path,
						'reset_menus': options.reset_menus,
						'import_dummy': options.import_dummy,
						'import_widgets': options.import_widgets,
						'import_options': options.import_options
					};

				$.ajax({
					type: "POST",
					url: ajaxurl,
					data: data,
					beforeSend: function () {
						options.parent.addClass('demo-install-process');
					},
					error: function () {
						base.import_finished.call(base, options);
					},
					success: function (response) {
						base.import_finished.call(base, options);
					},
					complete: function (response) {
						base.import_finished.call(base, options);
					}
				});

			},
			import_finished: function (options) {
				var base = this;
				setTimeout(function() {
					setTimeout( base.remove_alert_leave_page(), 1300 );
					options.parent.removeClass('demo-install-process');
				}, 1200 );
			}

		}.init();

	}

	var file_frame;
	var clickedID;

	$(document).on( 'click', '.button_upload_image', function( e ) {

		e.preventDefault();

		// If the media frame already exists, reopen it.
		if ( !file_frame ) {
			// Create the media frame.
			file_frame = wp.media.frames.downloadable_file = wp.media({
				title: 'Choose an image',
				button: {
					text: 'Use image'
				},
				multiple: false
			});
		}

		file_frame.open();

		clickedID = $(this).attr('id');

		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			attachment = file_frame.state().get('selection').first().toJSON();

			$('#' + clickedID).val( attachment.url );
			if ($('#' + clickedID).attr('data-name'))
				$('#' + clickedID).attr('name', $('#' + clickedID).attr('data-name'));

			file_frame.close();
		});
	}).on( 'click', '.button_remove_image', function( e ){

		var clickedID = jQuery(this).attr('id');
		$('#' + clickedID).val( '' );

		return false;
	});


	$(function() {
		new $.knowhere_demo();
	});

})(jQuery);

(function ($) {

	$(function($) {

		var file_frame;

		$('.knowhere_cupp_wpmu_button').on('click', function (event) {

			event.preventDefault();

			// If the media frame already exists, reopen it.
			if (file_frame) {
				file_frame.open();
				return;
			}

			// Create the media frame.
			file_frame = wp.media.frames.file_frame = wp.media({
				title: $(this).data('uploader_title'),
				button: {
					text: $(this).data('uploader_button_text')
				},
				multiple: false  // Set to true to allow multiple files to be selected
			});

			// When an image is selected, run a callback.
			file_frame.on('select', function () {
				// We set multiple to false so only get one image from the uploader
				attachment = file_frame.state().get('selection').first().toJSON();

				// Do something with attachment.id and/or attachment.url here
				// write the selected image url to the value of the #knowhere_cupp_meta text field
				$('#knowhere_cupp_meta').val('');
				$('#knowhere_cupp_upload_meta').val(attachment.url);
				$('#knowhere_cupp_upload_edit_meta').val('/wp-admin/post.php?post=' + attachment.id + '&action=edit&image-editor');
				$('.knowhere-cupp-current-img').attr('src', attachment.url).removeClass('placeholder');
			});

			// Finally, open the modal
			file_frame.open();
		});

		// Toggle Image Type
		$('input[name=knowhere_img_option]').on('click', function (event) {
			var imgOption = $(this).val();

			if (imgOption == 'external') {
				$('#knowhere_cupp_upload').hide();
				$('#knowhere_cupp_external').show();
			} else if (imgOption == 'upload') {
				$('#knowhere_cupp_external').hide();
				$('#knowhere_cupp_upload').show();
			}

		});

		if ('' !== $('#knowhere_cupp_meta').val()) {
			$('#external_option').attr('checked', 'checked');
			$('#knowhere_cupp_external').show();
			$('#knowhere_cupp_upload').hide();
		} else {
			$('#knowhere_upload_option').attr('checked', 'checked');
		}

		// Update hidden field meta when external option url is entered
		$('#knowhere_cupp_meta').blur(function (event) {
			if ('' !== $(this).val()) {
				$('#knowhere_cupp_upload_meta').val('');
				$('.knowhere-cupp-current-img').attr('src', $(this).val()).removeClass('placeholder');
			}
		});

		// Remove Image Function
		$('.knowhere_edit_options').hover(function () {
			$(this).stop(true, true).animate({opacity: 1}, 100);
		}, function () {
			$(this).stop(true, true).animate({opacity: 0}, 100);
		});

		$('.knowhere_remove_img').on('click', function (event) {
			var placeholder = $('#knowhere_cupp_placeholder_meta').val();

			$(this).parent().fadeOut('fast', function () {
				$(this).remove();
				$('.knowhere-cupp-current-img').addClass('placeholder').attr('src', placeholder);
			});
			$('#knowhere_cupp_upload_meta, #knowhere_cupp_upload_edit_meta, #knowhere_cupp_meta').val('');
		});

		if ( $('select#page_template').val() == 'template-parts/front-page.php' || $('select#page_template').val() == 'template-parts/front-page-2.php' || $('select#page_template').val() == 'template-parts/front-page-3.php' || $('select#page_template').val() == 'template-parts/front-page-4.php' ) {
			$('#page-subtitle.postbox, #page-add-video, #page-listing-categories, #page-listing-categories').show();
		} else {
			$('#page-subtitle.postbox, #page-add-video, #page-listing-categories, #page-listing-categories').hide();
		}

		$('#page_template').on('change', function () {

			var val = $(this).val();

			if ( val == 'template-parts/front-page.php' || val == 'template-parts/front-page-2.php' || val == 'template-parts/front-page-3.php' || val == 'template-parts/front-page-4.php'  ) {
				$('#page-subtitle.postbox, #page-add-video, #page-listing-categories, #page-listing-categories').show();
			} else {
				$('#page-subtitle.postbox, #page-add-video, #page-listing-categories, #page-listing-categories').hide();
			}

		});

		function kw_location_initialize() {

			/* Used on main search page */
			if ( $('#_job_location').length ) {
				var input = $('#_job_location')[0];
				new google.maps.places.Autocomplete(input);
			}

		}

		if ( typeof google === 'object' && typeof google.maps === 'object' ) {
			google.maps.event.addDomListener( window, 'load', kw_location_initialize );
		}

	});

})(jQuery);
