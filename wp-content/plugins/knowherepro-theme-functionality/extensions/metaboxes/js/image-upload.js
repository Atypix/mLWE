window.mad = window.mad || {};

jQuery( function ( $ ) {
	'use strict';

	var views = mad.views = mad.views || {},
		ImageField = views.ImageField,
		ImageUploadField,
		UploadButton = views.UploadButton;

	ImageUploadField = views.ImageUploadField = ImageField.extend( {
		createAddButton: function () {
			this.addButton = new UploadButton( {controller: this.controller} );
		}
	} );

	/**
	 * Initialize fields
	 * @return void
	 */
	function init() {
		new ImageUploadField( {input: this, el: $( this ).siblings( 'div.mad-media-view' )} );
	}

	$( ':input.mad-image_upload, :input.mad-plupload_image' ).each( init );
	$( '.mad-input' )
		.on( 'clone', ':input.mad-image_upload, :input.mad-plupload_image', init )
} );
