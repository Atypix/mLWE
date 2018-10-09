window.mad = window.mad || {};

jQuery( function ( $ ) {
	'use strict';

	var views = mad.views = mad.views || {},
		MediaField = views.MediaField,
		MediaItem = views.MediaItem,
		MediaList = views.MediaList,
		ImageField;

	ImageField = views.ImageField = MediaField.extend( {
		createList: function () {
			this.list = new MediaList( {
				controller: this.controller,
				itemView: MediaItem.extend( {
					className: 'mad-image-item',
					template: wp.template( 'mad-image-item' )
				} )
			} );
		}
	} );

	/**
	 * Initialize image fields
	 */
	function initImageField() {
		new ImageField( {input: this, el: $( this ).siblings( 'div.mad-media-view' )} );
	}

	$( 'input.mad-image_advanced' ).each( initImageField );
	$( '#wpbody' ).on( 'clone', 'input.mad-image_advanced', initImageField )
} );
