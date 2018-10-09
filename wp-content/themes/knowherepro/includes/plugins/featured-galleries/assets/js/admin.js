
(function ($) {

	$.mad_media_featured_uploader = $.mad_media_featured_uploader || {};

	/*	Init
	/* --------------------------------------------- */

	$.mad_media_featured_uploader.init = function () {

		$('#mad_featured_gallery').on('click', '#mad_add_featured_images', function (e) {
			$.mad_media_featured_uploader.add(e);
		}).on('click', '.remove-featured-image', function (e) {
			$.mad_media_featured_uploader.remove_image(e);
		}).on('click', '#mad_remove_all', function (e) {
			$.mad_media_featured_uploader.remove_all(e);
		});

	}

	/*	Add
	/* --------------------------------------------- */

	$.mad_media_featured_uploader.add = function (e) {

		e.preventDefault();

		// If the media frame already exists, reopen it.
		if ( file_frame ) {
			file_frame.open();
			$.mad_media_featured_uploader.fixBackButton();
			return;
		}

		// Create the media frame.
		var file_frame = wp.media.frame = wp.media({
			frame: "post",
			state: "featured-gallery",
			library : { type : 'image'},
			button: { text: "Edit Image Order"},
			multiple: true
		});

		// Create Featured Gallery state. This is essentially the Gallery state, but selection behavior is altered.
		file_frame.states.add([
			new wp.media.controller.Library({
				id:         'featured-gallery',
				title:      'Select Images for Gallery',
				priority:   20,
				toolbar:    'main-gallery',
				filterable: 'uploaded',
				library:    wp.media.query( file_frame.options.library ),
				multiple:   file_frame.options.multiple ? 'reset' : false,
				editable:   true,
				allowLocalEdits: true,
				displaySettings: true,
				displayUserSettings: true
			})
		]);

		file_frame.on('open', function() {
			var selection = file_frame.state().get('selection');
			var library = file_frame.state('gallery-edit').get('library');
			var ids = $('#mad_perm_metadata').val();
			if (ids) {
				idsArray = ids.split(',');
				idsArray.forEach(function(id) {
					attachment = wp.media.attachment(id);
					attachment.fetch();
					selection.add( attachment ? [ attachment ] : [] );
				});
				file_frame.setState('gallery-edit');
				idsArray.forEach(function(id) {
					attachment = wp.media.attachment(id);
					attachment.fetch();
					library.add( attachment ? [ attachment ] : [] );
				});
			}
		});

		file_frame.on('ready', function() {
			$( '.media-modal' ).addClass( 'no-sidebar' );
			$.mad_media_featured_uploader.fixBackButton();
		});

		// When an image is selected, run a callback.
		file_frame.on('update', function() {
			var imageIDArray = [];
			var imageHTML = '';
			var metadataString = '';
			images = file_frame.state().get('library');
			images.each(function(attachment) {
				imageIDArray.push(attachment.attributes.id);
				imageHTML += '<li><button class="remove-featured-image"></button><img id="' + attachment.attributes.id + '" src="' + attachment.attributes.url + '"></li>';
			});
			metadataString = imageIDArray.join(",");

			if (metadataString) {
				$("#mad_perm_metadata").val(metadataString);
				$("#mad_featured_gallery ul").html(imageHTML);
				$('#mad_add_featured_images').text('Edit Selection');
				$('#mad_remove_all').addClass('visible');
				setTimeout(function(){
					$.mad_media_featured_uploader.ajaxUpdateTempMetaData();
				},0);
			}
		});

		// Finally, open the modal
		file_frame.open();

	}

	/*	Remove Image
	/* --------------------------------------------- */

	$.mad_media_featured_uploader.remove_image = function (e) {

		e.preventDefault();
		var element = $(e.target);

		if (confirm('Are you sure you want to remove this image?')) {

			var removedImage = element.parent().children('img').attr('id'),
				oldGallery = $("#mad_perm_metadata").val(),
				newGallery = oldGallery.replace(',' + removedImage,'').replace(removedImage + ',','').replace(removedImage, '');

				element.parent('li').remove();

			$("#mad_perm_metadata").val(newGallery);

			if (newGallery == "") {
				$('#mad_add_featured_images').text('Select Images');
				$('#mad_remove_all').removeClass('visible');
			}

			$.mad_media_featured_uploader.ajaxUpdateTempMetaData();
		}

	}

	/*	Remove All
	/* --------------------------------------------- */

	$.mad_media_featured_uploader.remove_all = function (e) {
		e.preventDefault();

		if (confirm('Are you sure you want to remove all images?')) {
			$("#mad_featured_gallery ul").html("");

			$("#mad_perm_metadata").val("");
			$('#mad_remove_all').removeClass('visible');
			$('#mad_add_featured_images').text('Select Images');

			$.mad_media_featured_uploader.ajaxUpdateTempMetaData();
		}
	}

	/*	ajaxUpdateTempMetaData
	/* --------------------------------------------- */

	$.mad_media_featured_uploader.ajaxUpdateTempMetaData = function() {

		$.ajax({
			type : "post",
			dataType : "json",
			url : ajaxurl,
			data : {
				action: "mad_update_temp",
				mad_post_id: $("#mad_perm_metadata").data("post_id"),
				mad_temp_noncedata: $("#mad_temp_noncedata").val(),
				mad_temp_metadata: $("#mad_perm_metadata").val()
			},
			success: function(response) {
				if (response == "error") {
					alert("There was an issue with updating the live preview. Make sure that you click Save to ensure your changes aren't lost.");
				}
			}
		});

	}

	/*	fixBackButton
	/* --------------------------------------------- */

	$.mad_media_featured_uploader.fixBackButton = function (e) {
		setTimeout(function(){
			$('.media-menu a:first-child').text('← Edit Selection').addClass('button button-large button-primary');
		}, 0);
	}


	$(function () {
		$.mad_media_featured_uploader.init();
	});

})(jQuery);