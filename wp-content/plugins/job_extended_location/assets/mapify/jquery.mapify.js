/**
 * Mapify: Add Map To Address Input
 */
;( function ( $ ) {
	$.fn.extend({

		/* Create Function */
		mapify: function( options ) {

			/* Set default options */
			var settings = $.extend({
				height        : '200px',
				lat           : 0,
				lng           : 0,
				lock          : 'unlock',
				lat_input     : 'mapify_lat',
				lng_input     : 'mapify_lng',
				lock_input    : 'mapify_lock',
			}, options );

			var that = $( this );

			/* Do this for each element */
			return this.each( function(){

				/* Prepare
				------------------------------------------ */

				/* Always add wrapper for consistency */
				that.wrap( '<div class="mapify"><span class="geo-tag"></span></div>' );

				/* Wrap El Vars */
				var mapifyEl = that.parents( '.mapify' );
				var geotagEl = that.parent( '.geo-tag' );

				/* Add Map */
				mapifyEl.data( 'lock_status', settings.lock );
				if( ! $( "#" + settings.id ).length ){
					mapifyEl.append( '<div class="map-canvas" style="width:100%;height:' + settings.height + ';"></div>' );
				}
				var lock_text = 'unlock' == settings.lock ? mapifyl10n.locked : mapifyl10n.unlocked;
				var lock_class = 'unlock' == settings.lock ? 'unlocked' : 'locked';
				mapifyEl.append( '<div class="map-canvas-options"><span class="map-action-lock ' + lock_class + '" data-lock_status="' + settings.lock + '" data-lock="' + mapifyl10n.locked + '" data-unlock="' + mapifyl10n.unlocked + '">' + lock_text + '</span></div>' );
				mapifyEl.append( '<input autocomplete="off" type="hidden" name="' + settings.lock_input + '" value="' + settings.lock + '">' );
				mapifyEl.append( '<input autocomplete="off" type="hidden" name="' + settings.lat_input + '" value="' + settings.lat + '">' );
				mapifyEl.append( '<input autocomplete="off" type="hidden" name="' + settings.lng_input + '" value="' + settings.lng + '">' );

				/* Vars */
				var addressInput = that;
				var mapCanvas    = mapifyEl.find( '.map-canvas' );
				var lockInput    = mapifyEl.find( 'input[name="' + settings.lock_input + '"]' );
				var latInput     = mapifyEl.find( 'input[name="' + settings.lat_input + '"]' );
				var lngInput     = mapifyEl.find( 'input[name="' + settings.lng_input + '"]' );
				var map;
				var marker;
				var pos;
				var geocoder;
				var addressAutoComplete;

				/* Method Handler
				------------------------------------------ */
				var mapifyHandler = {

					/* Init
					------------------------------------------ */
					init: function(){

						/* Set Coordinate if empty */
						if( '' != addressInput.val() && ! settings.lat && ! settings.lng ){
							mapifyHandler.SetCoordinate();
						}

						/* Load Map */
						mapifyHandler.loadMap();

						/* Load Address Auto Complete */
						mapifyHandler.loadAddressAutoComplete();

						/* Add Geo Tag */
						mapifyHandler.geoTag();

						/* Lock Toggle Option */
						mapifyHandler.toggleLock();

					},


					/* Load Map
					------------------------------------------ */
					loadMap: function(){
						/* Geo coder */
						geocoder = new google.maps.Geocoder();
						/* Get position */
						pos = new google.maps.LatLng( settings.lat, settings.lng );
						/* Render Map */
						map = new google.maps.Map( mapCanvas[0], { 
							center           : pos, 
							zoom             : 13,
							streetViewControl: false 
						});
						/* Render Marker */
						var marker_dragable = 'unlock' == mapifyEl.data( 'lock_status' ) ? true : false;
						marker = new google.maps.Marker({
							map         : map,
							draggable   : marker_dragable,
							animation   : google.maps.Animation.DROP,
							anchorPoint : new google.maps.Point( 0, -29 ),
							position    : pos,
						});
						/* Marker Drag-End Event */
						google.maps.event.addListener( marker, 'dragend', function( event ) {
							if ( 'unlock' == mapifyEl.data( 'lock_status' ) ) {
								var pos = event.latLng;
								var lat = pos.lat();
								var lng = pos.lng();
								/* Update position */
								mapifyHandler.updatePosition( lat, lng, pos );
								/* Update address */
								mapifyHandler.updateAddress( pos );
							}
						});
						/* Make responsive */
						google.maps.event.addDomListener( window, 'resize', function() {
							var center = map.getCenter();
							google.maps.event.trigger( map, 'resize' );
							map.setCenter( center ); 
						});
						/* Custom event to force resize if needed */
						mapifyEl.on( 'mapify_resize', function(){
							var center = map.getCenter();
							google.maps.event.trigger( map, 'resize' );
							map.setCenter( center ); 
						});
					},

					/* Set Coordinate
					------------------------------------------ */
					SetCoordinate: function(){
						/* Geo coder */
						geocoder = new google.maps.Geocoder();
						geocoder.geocode( {
							"address": addressInput.val(),
						}, function( results ) {
							var pos = results[0].geometry.location;
							if( undefined != pos ){
								var lat = pos.lat();
								var lng = pos.lng();
								/* Update input */
								latInput.val( lat );
								lngInput.val( lng );
								/* Update position */
								mapifyHandler.updatePosition( lat, lng, pos );
								/* Set marker position */
								marker.setPosition( pos );
							}
						} );
					},

					/* Geo Tag
					------------------------------------------ */
					geoTag: function(){
						if ( navigator.geolocation ) {
							/* Chrome need SSL */
							var is_chrome = /chrom(e|ium)/.test( navigator.userAgent.toLowerCase() );
							var is_ssl    = 'https:' == document.location.protocol;
							if( is_chrome && ! is_ssl ){
								return false;
							}
							geotagEl.prepend( '<i class="location"></i>' );
							geotagEl.children( '.location' ).click( function(e){
								e.preventDefault();
								var icon = $( this );
								icon.addClass( 'loading' );
								navigator.geolocation.getCurrentPosition(
									function( position ){ // success cb
										var lat = position.coords.latitude;
										var lng = position.coords.longitude;
										var pos = new google.maps.LatLng( lat, lng );
										/* Update position */
										mapifyHandler.updatePosition( lat, lng, pos );
										/* Set marker position */
										marker.setPosition( pos );
										/* Update address */
										mapifyHandler.updateAddress( pos );
										/* Done */
										icon.removeClass( 'loading' );
									},
									function(){ // fail cb
										icon.removeClass( 'loading' );
									}
								);
							});
						}
					},

					/* Load Address AutoComplete
					------------------------------------------ */
					loadAddressAutoComplete: function(){
						/* AutoComplete */
						addressAutoComplete = new google.maps.places.Autocomplete( addressInput[0] );
						addressAutoComplete.bindTo( 'bounds', map );
						/* Place Changed Event */
						google.maps.event.addListener( addressAutoComplete, 'place_changed', function() {
							if ( 'unlock' == mapifyEl.data( 'lock_status' ) ) {
								var place = addressAutoComplete.getPlace();
								if( undefined !== place ){
									var pos = place.geometry.location;
									var lat = pos.lat();
									var lng = pos.lng();
									/* Update position */
									mapifyHandler.updatePosition( lat, lng, pos );
									/* Set marker position */
									marker.setPosition( pos );
								}
							}
						});
						/* Disable "Enter" Key and use it to manually fill coordinate based on address. */
						google.maps.event.addDomListener( addressInput[0], 'keydown', function(e) { 
							if ( e.keyCode == 13 ) {
								e.preventDefault(); 
								mapifyHandler.SetCoordinate();
								$( ".pac-container" ).hide(); // hide autocomplete option.
							}
						});
					},


					/* Toggle Lock
					------------------------------------------ */
					toggleLock: function(){
						mapifyEl.find( '.map-action-lock' ).click( function(e){
							e.preventDefault();
							var lockStatus = $( this ).data( 'lock_status' );
							if( 'unlock' == lockStatus ){
								marker.setDraggable( false );
								$( this ).removeClass( 'locked unlocked' ).addClass( 'locked' );
								$( this ).text( $( this ).data( 'unlock' ) );
								$( this ).data( 'lock_status', 'lock' );
								lockStatus = $( this ).data( 'lock_status' );
								$( this ).attr( 'data-lock_status', lockStatus );
								mapifyEl.data( 'lock_status', lockStatus );
								lockInput.val( lockStatus );
							}
							else{
								marker.setDraggable( true );
								$( this ).removeClass( 'locked unlocked' ).addClass( 'unlocked' );
								$( this ).text( $( this ).data( 'lock' ) );
								$( this ).data( 'lock_status', 'unlock' );
								lockStatus = $( this ).data( 'lock_status' );
								$( this ).attr( 'data-lock_status', lockStatus );
								mapifyEl.data( 'lock_status', lockStatus );
								lockInput.val( lockStatus );
							}
						});
					},


					/* Utility
					------------------------------------------ */

					/* Update Coordinate Position */
					updatePosition : function( lat, lng, pos ){
						/* Update input */
						latInput.val( lat );
						lngInput.val( lng );
						/* Set map position */
						map.panTo( pos );
					},

					/* Update Address by Position */
					updateAddress : function( pos ){
						geocoder.geocode(
							{ 'latLng': pos },
							function( results, status ) {
								if ( status == google.maps.GeocoderStatus.OK && results[0] ) {
									addressInput.val( results[0].formatted_address );
								}
							}
						);
					},

				}; // var mapifyHandler

				/* Load Init */
				mapifyHandler.init();

			}); // end return;
		},

	});
}( jQuery ));
