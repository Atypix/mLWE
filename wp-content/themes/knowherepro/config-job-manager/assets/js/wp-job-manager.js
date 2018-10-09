window.wp = window.wp || {};

(function( window, undefined ) {

	'use strict';

	window.wp = window.wp || {};
	var document = window.document;
	var $ = window.jQuery;

	var ctor, inherits;
	var api = {};

	ctor = function() {};

	inherits = function( parent, protoProps, staticProps ) {
		var child;
		if ( protoProps && protoProps.hasOwnProperty( 'constructor' ) ) {
			child = protoProps.constructor;
		} else {
			child = function() {
				var result = parent.apply( this, arguments );
				return result;
			};
		}

		$.extend( child, parent );

		ctor.prototype  = parent.prototype;
		child.prototype = new ctor();

		if ( protoProps ) {
			$.extend( child.prototype, protoProps );
		}

		if ( staticProps ) {
			$.extend( child, staticProps );
		}

		child.prototype.constructor = child;
		child.__super__ = parent.prototype;

		return child;
	};

	api.Class = function( applicator, argsArray, options ) {
		var magic, args = arguments;

		if ( applicator && argsArray && api.Class.applicator === applicator ) {
			args = argsArray;
			$.extend( this, options || {} );
		}

		magic = this;
		if ( this.instance ) {
			magic = function() {
				return magic.instance.apply( magic, arguments );
			};

			$.extend( magic, this );
		}

		magic.initialize.apply( magic, args );

		return magic;
	};

	api.Class.extend = function( protoProps, classProps ) {
		var child = inherits( this, protoProps, classProps );
		child.extend = this.extend;

		return child;
	};

	api.Class.applicator = {};
	api.Class.prototype.initialize = function() {};
	api.Class.prototype.extended = function( constructor ) {
		var proto = this;

		while ( typeof proto.constructor !== 'undefined' ) {
			if ( proto.constructor === constructor ) {
				return true;
			}

			if ( typeof proto.constructor.__super__ === 'undefined' ) {
				return false;
			}

			proto = proto.constructor.__super__;
		}

		return false;
	};

	api.settings = knowhere_job_manager_localize;
	api.controllers = {};
	api.DataServices = [];
	api.DataService = api.Class.extend({
		response: null,
		activeResponse: $.Deferred(),
		resultsContainer: 'ul.job_listings',
		initialize: function( options ) {
			$.extend( this, options || {} );
		},
		geocoder: null,
		auto: null,
		update: function() {},
		addResults: function( resultsContainer ) {
			var self = this;
				self.setFound();
		},
		setFound: function() {
			var found = ! _.isUndefined( api.controllers.dataService.response.found_posts ) ? api.controllers.dataService.response.found_posts : 0;
			$('.kw-results-count').replaceWith('<div class="kw-results-count">'+ api.settings.i18n.resultsFound.replace( '%d', found ) +'</div>');
		}
	});

	api.DataServices.Job = api.DataService.extend({

		initialize: function( options ) {

			api.DataService.prototype.initialize.call( options );

			this.geocoder = new google.maps.Geocoder();
			this.isAddressGeocoded = false;
			this.prevAddress = $( '#search_location' ).val();
			this.$target = $( 'div.job_listings' );
			this.$form = $( 'form.job_filters' );
			this.$address = $( '#search_location' );
			this.$lat = $( '#search_lat' );
			this.$lng = $( '#search_lng' );
			this.auto = 'search_location';

			this.resultsContainer = this.$target.find( 'ul.job_listings' );

			this.radiusSlider();

			this.locateMe();

			if ( 0 === this.$target.length ) {
				return;
			}

			var self = this;

			this.$target.on( 'updated_results', function( event, data ) {

				self.response = data;

				if ( ! self.activeResponse.state( 'resolved' ) ) {
					if ( window.console ) {
						console.log( 'Data Service ready...' );
					}
				} else {
					self.addResults();
				}

			} );

			this.$target.on( 'update_results', function(e) {
				self.maybeReverseGeocode();
			} );
		},

		maybeReverseGeocode: function() {
			if ( ! api.controllers.dataService ) {
				return;
			}

			if ( ! api.controllers.dataService.geocode ) {
				return;
			}

			if ( '' === this.$address.val() ) {
				return;
			}

			if ( this.$address.val() != this.prevAddress ) {
				this.isAddressGeocoded = false;
			}

			if ( this.isAddressGeocoded || this.isAddressGeocoding ) {
				return;
			}

			api.controllers.dataService.geocode( this.$address.val() );
		},

		radiusSearch: function( lat, lng ) {
			this.$lat.val( lat );
			this.$lng.val( lng );

			this.update();
		},

		radiusSlider: function() {
			var self = this;

			var min_radius = parseInt(knowhere_job_manager_localize.min_radius);
			var max_radius = parseInt(knowhere_job_manager_localize.max_radius);
			var default_radius = parseInt(knowhere_job_manager_localize.default_radius);

			var radius_search_slider = function( default_radius ) {

				$('#radius-range-slider').slider({
					value: default_radius,
					min: min_radius,
					max: max_radius,
					step: 1,
					slide: function (event, ui) {
						self.updateRadius(ui);
					},
					change: function (event, ui) {
						self.update();
					},
					stop: function( event, ui ) { }
				});

				$( '.radi' ).html( $('#radius-range-slider' ).slider('value'));
				$( '#search_radius' ).val( $('#radius-range-slider' ).slider('value'));
			}

			if ( $( '#radius-range-slider' ).length > 0 ) {
				radius_search_slider(default_radius);
			}

		},

		update: function() {
			this.$target.triggerHandler( 'update_results', [ 1, false ] );
		},

		updateRadius: function( ui ) {
			$('.radi').html(ui.value);
			$('#search_radius').val(ui.value);
		},

		locateMe: function() {

			if ( ! navigator.geolocation ) {
				return;
			}

			var self = this;

			$( '.search_location' ).append( '<i class="kw-locate-me"></i>' );
if ( $('.kw-locate-me').length ) {
			$( '.kw-locate-me' ).on( $.knowhere.EVENT, function( e ) {
				e.preventDefault();

				$(this).addClass( 'loading' );

				navigator.geolocation.getCurrentPosition( self.locateMeSuccess, null, {
					enableHighAccuracy: true
				});
			});
}

		},

		locateMeSuccess: function( position ) {
			var lat = position.coords.latitude;
			var lng = position.coords.longitude;

			if ( api.controllers.dataService.geocode ) {
				api.controllers.dataService.geocode( lat + ', ' + lng );
			}

			$( '.kw-locate-me' ).removeClass( 'loading' );
		},

		autoComplete: function( field ) {
			var self = this;

			var $field = $( '#' + field );

			if ( 0 === $field.length ) {
				return;
			}

			var autocomplete = new google.maps.places.Autocomplete( document.getElementById( field ), {
				types: ['geocode']
			} );

			$field.unbind( 'change' );

			$field.keypress(function( e ) {
				if ( e.which == 13 ) {
					api.controllers.dataService.update();
				}
			});

			autocomplete.addListener( 'place_changed', function() {
				var place = autocomplete.getPlace();

				if ( place.geometry ) {
					api.controllers.dataService.isAddressGeocoded = true;
					api.controllers.dataService.radiusSearch( place.geometry.location.lat(), place.geometry.location.lng() );
				} else {
					api.controllers.dataService.isAddressGeocoded = false;
					self.geocode( place.name );
				}
			} );

		},

		geocode: function( address ) {
			this.isAddressGeocoding = true;

			this.geocoder.geocode({
				address: address
			}, function( results, status ) {

				if ( status == google.maps.GeocoderStatus.OK ) {
					var loc = results[0].geometry.location;

					api.controllers.dataService.prevAddress = address;
					api.controllers.dataService.isAddressGeocoded = true;
					api.controllers.dataService.$address.val( results[0].formatted_address );

					api.controllers.dataService.radiusSearch( loc.lat(), loc.lng() );
				} else {
					api.controllers.dataService.isAddressGeocoded = false;
				}

				this.isAddressGeocoding = false;
			} );
		}

	});

	var $supports_html5_history = false;
	if ( window.history && window.history.pushState ) {
		$supports_html5_history = true;
	}

	function job_manager_store_state( target, page ) {
		if ( $supports_html5_history ) {
			var form  = target.find( '.job_filters' );
			var data  = $( form ).serialize();
			var index = $( 'div.job_listings' ).index( target );
			window.history.replaceState( { id: 'job_manager_state', page: page, data: data, index: index }, '', location + '#s=1' );
		}
	}

	function knowhere_empty (data) {
		if ( typeof( data ) == 'number' || typeof( data ) == 'boolean' ) { return false; }
		if ( typeof( data ) == 'undefined' || data === null ) { return true; }
		if ( typeof( data.length ) != 'undefined' ) { return data.length === 0; }
		var count = 0;
		for ( var i in data ) {
			if ( Object.prototype.hasOwnProperty.call(data, i) ) { count++; }
		}
		return count === 0;
	}

	$.knowhere_job_manager_mod = $.knowhere_job_manager_mod || {};

	$.knowhere_job_manager_mod = {

		init: function() {

			this.initLayoutControl();
			this.initMapModule();
			this.searchModule();

			var $uploader = $('.wp-job-manager-file-upload');

			if ( $uploader.length ) {
				$uploader.each(function(i, obj) {
					var $input = $(obj),
						id = $input.attr('id'),
						$label = $('label[for="' + id + '"]'),
						$btn = $('<div class="kw-file-input-field"><div class="kw-file-input-inner"><span class="lnr icon-picture kw-lead-icon"></span><span class="lnr icon-plus"></span>' + knowhere_job_manager_localize.strings['wp-job-manager-file-upload'] + '</div></div>').insertAfter($input);

					$btn.on('click', function() {
						$label.trigger('click');
					});
				});
			}
		},

		initScroll: function () {

			$(window).on("resize", function() {

				var elements = $('.kw-page-listings:not(.kw-page-add-listing) .kw-left-position:not(.kw-without-map):not(.kw-left-position-extend) .kw-finder-filters, .kw-page-listings:not(.kw-page-add-listing) .kw-left-position:not(.kw-without-map):not(.kw-left-position-extend) .kw-finder-listings');

				if ( !elements.length ) return;

				window.innerWidth >= 993 ? elements.mCustomScrollbar({
					theme: "minimal-dark"
				}) : elements.mCustomScrollbar("destroy");

			}).trigger("resize");

		},

		customizePadding: function(window) {

			var $content = $('.kw-page-listings:not(.kw-page-add-listing) .kw-content-area'),
				$map = $('.kw-page-listings .kw-listings-gmap'),
				$header = $('.kw-page-listings .kw-header'),
				$leftPosition = $('.kw-page-listings .kw-left-position'),
				$headerTop = $('.kw-page-listings .kw-header-top'),
				flexRight = $('.kw-page-listings .kw-flex-right'),
				finderSearch = $('.kw-finder-filters'),
				finderListings = $('.kw-finder-listings'),
				finderExtend = $('.kw-finder-extend'),
				headerHeight = $header.outerHeight(),
				headerHeightWithAdminBar = headerHeight, position = headerHeight;

			if ( !$map.length ) return;

			if ( finderExtend.length && $leftPosition.hasClass('kw-left-position-extend') ) {
				position = finderExtend.position().top + 30;
			}

			if ( window.width() < 993 ) {
				$content.css('marginTop', '');
				return;
			}

			if ( $('#wpadminbar').length ) {
				headerHeightWithAdminBar += $('#wpadminbar').outerHeight();
			}

			if ( $headerTop.length ) {
				$headerTop.css( {'height': headerHeightWithAdminBar} );
			}

			$content.css( 'marginTop', headerHeightWithAdminBar );

			if ( $leftPosition.length && !$leftPosition.hasClass('kw-left-position-extend') ) {

				if (finderSearch.length && finderListings.length && flexRight.length) {
					finderSearch.add(finderListings).add(flexRight).css({'height': 'calc(100vh - ' + headerHeightWithAdminBar + 'px)'});
				}

			} else if ( $leftPosition.hasClass('kw-left-position-extend') ) {
				$map.css( 'top', position );
			} else {
				$map.css( 'top', headerHeightWithAdminBar );
				$map.css({'height': 'calc(100vh - ' + headerHeightWithAdminBar + 'px)'});
			}

		},

		eventHanlers: function () {

			var base = this, window = $(window);

			window.on('debouncedresize', function() {

				base.customizePadding($(this));

				setTimeout(function() {
					window.trigger('update:map');
					window.trigger('pxg:refreshmap');
				});

			});

		},

		initMapModule: function(){

			var Map = (
				function () {

					var $body = $('body'),
						$window = $(window),
						map, markers, custom_html_icon;

					function init() {

						$.knowhere_job_manager_mod.customizePadding($window);
						$.knowhere_job_manager_mod.eventHanlers();

						if ( !$('#kw-listings-gmap').length ) {
							$('.job_listings').on('updated_results', function(e, result) {
								updateListing(result.total_found);
							});
							return;
						}

						if ( typeof L !== "object" || !L.hasOwnProperty('map') ) {
							return;
						}

						map = L.map('kw-listings-gmap', {
							scrollWheelZoom: false
						});

						markers = new L.MarkerClusterGroup({
							showCoverageOnHover: false
						});

						custom_html_icon = L.HtmlIcon.extend({
							options: {
								iconSize: [50, 60], // size of the icon
								iconAnchor: [25, 60], // point of the icon which will correspond to marker's location
								popupAnchor: [-1, -60] // point from which the popup should open relative to the iconAnchor
							}
						});

						$window.on('pxg:refreshmap', function () {
							map._onResize();
						});

						var tileLayer = L.gridLayer.googleMutant({
							type: 'roadmap'
						});

						map.addLayer(tileLayer);

						if ( !$('#kw-listings-gmap').is('.kw-listing-widget-gmap') ) {

							updateListing();

							$('.job_listings').on('updated_results', function(e, result) {
								updateListing(result.total_found);
							});

							$(document).on('facetwp-loaded facetwp-refresh', function(e, result) {
								if ( $('select.facetwp-dropdown').length ) {
									$('select.facetwp-dropdown').chosen();
								}
								updateListing();
							});

						} else {

							var $item = $('.single_job_listing');

							if ( typeof $item.data('latitude') !== "undefined" && typeof $item.data('longitude') !== "undefined" ) {

								addPinToMap($item);
								map.addLayer(markers);
								map.setView([$item.data('latitude'), $item.data('longitude')], 13);
								$(window).on('update:map', function() {
									map.setView([$item.data('latitude'), $item.data('longitude')], 13);
								});

							} else {
								$('.kw-listing-widget-gmap').hide();
							}
						}

					}

					function updateListing($total) {

						var $listings = $('.job_listings'),
							$listing = $('.job_listing', $listings),
							listingWithLocation = 0;

						if ( !$listing.length ) {
							defaultMap();
							return;
						}

						if ( $listing.find('.kw-rating').length ) {
							$.knowhere.templateHelpers.productRating($listing.find('.kw-rating'));
						}

						if ( $listing.find('.kw-share-popup-link').length ) {
							$.knowhere_job_manager_mod.popupLink($listing.find('.kw-share-popup-link'));
						}

						if ( $listing.find('.kw-property-slideshow.owl-carousel').length ) {
							$.knowhere.templateHelpers.propertySlideshow($listing.find('.kw-property-slideshow.owl-carousel'));
						}

						//if ( $listings.find('.kw-listings').length ) {
						//	$.knowhere.modules.maxHeightItems( $listings.find('.kw-listings'), false );
						//}

						if ( typeof $total !== 'undefined' ) {
							$('.kw-results-count').replaceWith('<div class="kw-results-count">' + $total + ' ' + knowhere_job_manager_localize.strings['results'] + '</div>');
						} else {
							$('.kw-results-count').replaceWith('<div class="kw-results-count">' + $listing.length + ' ' + knowhere_job_manager_localize.strings['results'] + '</div>');
						}

						if ( $('.kw-listings-gmap').length && typeof map !== "undefined" ) {

							map.removeLayer(markers);

							markers = new L.MarkerClusterGroup({
								showCoverageOnHover: false
							});

							$listing.each(function(i, obj) {

								var cardHasLocation = addPinToMap($(obj), true);

								if ( cardHasLocation ) {
									listingWithLocation += 1;
								}
							});

							if ( listingWithLocation != 0 ) {
								map.fitBounds(markers.getBounds(), {
									padding: [50, 50]
								});
								map.addLayer(markers);
								//map.setZoom(8);

								var mapZoom = map.getZoom();
								var bounds = markers.getBounds();
								var lat = (bounds._northEast.lat + bounds._southWest.lat) / 2;
								var lng = (bounds._northEast.lng + bounds._southWest.lng) / 2;
								bounds = [lat, lng];

								Cookies.set('kw-bounds', JSON.stringify(bounds));
								Cookies.set('kw-mapZoom', mapZoom);

							} else {
								defaultMap();
							}
						}

					}

					function addPinToMap($item, tax) {
						var categories = $item.data('categories'),
							iconClass, m, $img, ratingHTML = '';

						if ( typeof categories !== "undefined" && !categories.length ) {
							iconClass = 'kw-pin kw-pin-empty';
						} else {
							iconClass = 'kw-pin';
						}

						if ( knowhere_empty($item.data('latitude')) || knowhere_empty($item.data('longitude')) ) {
							return false;
						}

						var iconSVG = wp.template('knowhere-pin-selected-svg'),
							emptyiconSVG = wp.template('knowhere-empty-icon-svg'),
							$categories = $item.find('.kw-listing-item-icon').first(),
							iconHTML = "<div class='" + iconClass + "'>" + emptyiconSVG() + "</div>";

						if ( $body.is('.single-job_listing') || $body.is('.kw-page-add-listing') ) {
							if ( $('.kw-single-map-category-icon').length ) {
								iconHTML = "<div class='" + iconClass + "'>" + iconSVG() + "<div class='kw-pin-icon'>" + $('.kw-single-map-category-icon').html() + "</div></div>";
							}
						} else if ( $categories.length ) {
							iconHTML = "<div class='" + iconClass + "'>" + iconSVG() + "<div class='kw-pin-icon'>" + $categories.html() + "</div></div>";
						}

						m = L.marker([$item.data('latitude'), $item.data('longitude')], {
							icon: new custom_html_icon({
								html: iconHTML
							})
						});

						if ( typeof tax !== "undefined" ) {

							if ( knowhere_empty($item.data('img')) ) { } else {
								$img = '<img src="'+ $item.data('img') +'" alt="" />';
							}

							$item.hover(function() {
								$(m._icon).find('.kw-pin').addClass('kw-pin-selected');
							}, function() {
								$(m._icon).find('.kw-pin').removeClass('kw-pin-selected');
							});

							var event = knowhere_job_manager_localize.pintpoint_event ? knowhere_job_manager_localize.pintpoint_event : 'click';

							$item.find('.kw-listing-item-pintpoint')[event](function(e) {
								e.preventDefault();

								map.setView([$item.data('latitude'), $item.data('longitude')], 20);
								$(window).on('update:map', function() {
									map.setView([$item.data('latitude'), $item.data('longitude')], 20);
								});
							});

							var rating = $item.find('.kw-card-rating');

							if ( rating.length ) {
								ratingHTML = rating.length ? rating.html() : "";
							}

							var phone = $item.data('phone') ? '<div class="kw-popup-phone">' + $item.data('phone') + '</div>' : '';

							m.bindPopup(
								"<a class='kw-popup' href='" + $item.data('permalink') + "'>" + $img +
								"<div class='kw-popup-content'>" +
								"<h5 class='kw-popup-title'>" + $item.find('.kw-listing-item-title a').html() + "</h5>" + ratingHTML +
								"<div class='kw-popup-address'>" + $item.find('.kw-listing-item-location').html() + "</div>" + phone +
								"</div>" +
								"</a>").openPopup();

						}

						markers.addLayer(m);

						return true;
					}

					function defaultMap() {
						var bounds = Cookies.get('kw-bounds'),
							zoom = Cookies.get('kw-mapZoom');

						if ( typeof bounds === 'undefined' ) {
							bounds = [46.073085, 14.450286];
							zoom = 9;
						} else {
							bounds = JSON.parse(bounds);
						}

						map.removeLayer(markers);
						map.setView(bounds, zoom);
					}

					return {
						init: init
					}

				}
			)();

			Map.init();

		},

		searchModule: function () {

			var price_range_min = parseInt( knowhere_job_manager_localize.min_price );
			var price_range_max = parseInt( knowhere_job_manager_localize.max_price );
			var currency_symb = knowhere_job_manager_localize.currency_symbol;
			var thousands_separator = knowhere_job_manager_localize.thousands_separator;

			var addCommas = function(nStr) {
				nStr += '';
				var x = nStr.split('.');
				var x1 = x[0];
				var x2 = x.length > 1 ? '.' + x[1] : '';
				var rgx = /(\d+)(\d{3})/;
				while (rgx.test(x1)) {
					x1 = x1.replace(rgx, '$1' + thousands_separator + '$2');
				}
				return x1 + x2;
			}

			//$( '.job_listings' ).on( 'reset', function() {
			//
			//	$('.knowhere-active-tags, .knowhere-active-regions').empty();
			//	$('.knowhere-tags-select, .filter-job-types, .filter-job-regions').trigger("chosen:updated");
			//
			//	$('.knowhere-tags-select, .filter-job-types, .filter-job-regions, .search_bedrooms, .search_bathrooms').find(':selected').each(function(i, e) {
			//		$(e).attr('selected', false);
			//	});
			//
			//	$('.search_feature:checked').each( function( i, e ) {
			//		$(e).attr('checked', false);
			//	});
			//
			//	$('input[name="search_keywords"], .search_bedrooms, .search_bathrooms').each(function(i, e) {
			//		$(e).val('').trigger('chosen:updated');
			//	});
			//});

			if ( $('.knowhere-tags-select').length ) {
				var $tags = $('.knowhere-tags-select').chosen(),
					updateTags = function() {
						$('.knowhere-active-tags').empty();
						$tags.find(':selected').each(function(i, obj) {

							if ( knowhere_empty(obj.value) ) {
								return;
							}

							$('<div class="knowhere-active-tag">' + obj.value + '<div class="knowhere-remove-tag"></div></div>').appendTo('.knowhere-active-tags').on('click', function() {
								$(this).remove();
								$(obj).attr('selected', false);
								$tags.trigger("chosen:updated");
								$('.knowhere-active-tags input[value="' + obj.value + '"]').remove();
								$('.job_listings').triggerHandler('update_results', [1, false]);
							});

							$('<input type="hidden" name="job_tag[]" value="' + obj.value + '" />').appendTo('.knowhere-active-tags');
						});
						$('.job_listings').triggerHandler('update_results', [1, false]);
					};

				$tags.on('change', updateTags);
			}

			if ( $('.search_feature').length ) {
				$('.search_feature').on('change', function () {
					$('.job_listings').triggerHandler('update_results', [1, false]);
				});
			}

			if ( $('.filter-job-regions').length ) {
				var $regions = $('.filter-job-regions').chosen(),
					updateRegions = function() {

						$('.knowhere-active-regions').empty();

						$regions.find(':selected').each(function(i, obj) {

							if ( knowhere_empty(obj.value) ) {
								return;
							}

							$('<input type="hidden" name="search_region" value="' + obj.value + '" />').appendTo('.knowhere-active-regions');

						});

						$('.job_listings').triggerHandler('update_results', [1, false]);

					};

				$regions.on('change', updateRegions);
			}
			if ( $('.knowhere-search-jobs-frontpage ').length || $('.job-manager-category-dropdown').length || $('.search_bedrooms').length || $('.search_bathrooms').length || $('.job-manager-filter').length) {
				$('.knowhere-search-jobs-frontpage .job-manager-category-dropdown, .search_bedrooms, .search_bathrooms, .job-manager-filter').chosen();
			}
			if ( $('#job_type').length ) {
				$( '#job_type' ).chosen({
					search_contains: true
				});
			}
		if ( $('.kw-finder-filters').length ) {
			$( '.kw-finder-filters' ).on( $.knowhere.EVENT, '.reset', function () {
				var target = $(this).closest( 'div.job_listings' );
				var form = target.find( 'form' );

				form.find( ':input[name="search_keywords"], :input[name="search_location"], .job-manager-filter' ).not(':input[type="hidden"]').val( '' ).trigger( 'chosen:updated' );
				form.find( ':input[name^="search_categories"]' ).not(':input[type="hidden"]').val( '' ).trigger( 'chosen:updated' );
				$( ':input[name="filter_job_type[]"]', form ).not(':input[type="hidden"]').attr( 'checked', 'checked' );

				form.find('.min-price-range-hidden, .max-price-range-hidden').val( '' );
				form.find('.knowhere-active-tags, .knowhere-active-regions').empty();
				form.find('.knowhere-tags-select, .filter-job-types, .filter-job-regions').trigger("chosen:updated");

				form.find('.knowhere-tags-select, .filter-job-types, .filter-job-regions, .search_bedrooms, .search_region, .search_bathrooms').find(':selected').each(function(i, e) {
					$(e).attr('selected', false);
				}).end().trigger( 'chosen:updated' );

				form.find('.search_feature:checked').each( function( i, e ) {
					$(e).attr('checked', false);
				});

				form.find('input[name="search_keywords"], .search_bedrooms, .search_region, .search_bathrooms').each(function(i, e) {
					$(e).val('').trigger('chosen:updated');
				});

				var filters_search = target.find('.kw-job-filters-search');
				if ( filters_search.length ) {
					filters_search.empty();
				}

				target.triggerHandler( 'reset' );
				target.triggerHandler( 'update_results', [ 1, false ] );
				job_manager_store_state( target, 1 );

				return false;
			} );
		}

			function kw_search_keywords() {
				var form_keyword = $('.job_filters input[name="search_keywords"]');
				var value = $('.kw-controls-form input[name="search_keywords"]').val();

				if ( form_keyword.length ) {
					if ( value ) {
						form_keyword.val(value);
					} else {
						form_keyword.val(' ');
					}
				}
			}

			$( '.kw-controls-form input[name="search_keywords"]' ).change( function() {
				kw_search_keywords();
			} );
		if ( $('.kw-search-keywords-btn').length ) {
			$('.kw-search-keywords-btn').on($.knowhere.EVENT, function (e) {
				e.preventDefault();
				kw_search_keywords();

				$( this ).closest( 'div.job_listings' ).triggerHandler( 'update_results', [ 1, false ] );
			});
		}
			function kw_search_sort() {

				var form_sort = $('.job_filters [name^="search_sort"]');
				var selected = $(this).val();

				if ( form_sort.length ) {
					if ( selected ) {

						form_sort.find('option').each(function () {
							$(this).attr('selected', false);
						});

						$('.job_filters [name^="search_sort"] option[value=' + selected + ']').attr('selected', 'selected');

					}
				}

			}

			var price_range = function( min_price, max_price ) {

				$('.price-range-advanced').slider({
					range: true,
					min: min_price,
					max: max_price,
					values: [min_price, max_price],
					slide: function (event, ui) {

						var min_price_range = addCommas(ui.values[0]) + currency_symb ;
						var max_price_range = addCommas(ui.values[1]) + currency_symb;

						$('.min-price-range-hidden').val( min_price_range );
						$('.max-price-range-hidden').val( max_price_range);

						$('.min-price-range').text( min_price_range );
						$('.max-price-range').text( max_price_range );

					},
					stop: function( event, ui ) {
						$( this ).closest( 'div.job_listings' ).triggerHandler( 'update_results', [ 1, false ] );
					}
				});

				var min_price_range = addCommas($('.price-range-advanced').slider('values', 0)) + currency_symb;
				var max_price_range = addCommas($('.price-range-advanced').slider('values', 1))+ currency_symb;

				$('.min-price-range').text(min_price_range);
				$('.max-price-range').text(max_price_range);

			}

			if ( $('.price-range-advanced').length ) {
				price_range( price_range_min, price_range_max );
			}

			$('.kw-controls-form [name^="search_sort"]').change( function() {
				kw_search_sort();
			});
		if ( $('.button.kw-update-form').length ) {
			$( 'button.kw-update-form' ).on( $.knowhere.EVENT, function() {
				$(this).parents( 'form' ).submit();
			});
		}
			$( 'form.job_search_form input' ).keypress(function(event) {
				if ( event.which == 13 ) {
					event.preventDefault();
					$( 'form.job_search_form' ).submit();
				}
			});

		},

		initLayoutControl: function () {

			this.controls = $('.kw-listing-layout-control');
			if ( this.controls.length ) this.initLayoutModule();
			if ($('.kw-layout-control').length) {

				$('.kw-layout-control').on($.knowhere.EVENT, function(t) {
					$(window).trigger('pxg:refreshmap'),
					t.preventDefault();

					var i = $(this).data('col'), n = [];
					$(this).siblings().each(function() {
						n.push($(this).data("col"));
					});

					var holder = $('.kw-flex-holder'),
						b = n[0], o = n[1];

					holder.find('.job_listings').css( 'opacity', 0 );
					holder.removeClass(b).removeClass(o).removeClass('kw-loading-opacity').addClass(i);
					$(this).addClass('kw-active').siblings().removeClass('kw-active');

					setTimeout(function() {
						holder.addClass('kw-loading-opacity');
						$('.kw-property-slideshow.owl-carousel').trigger('refresh.owl.carousel');
					}, 350);

				});
			}
			if ( $('.kw-search-adjust').length ) {
				$('.kw-search-adjust').on($.knowhere.EVENT, function ( e ) {
					e.preventDefault();

					var filters = $('.kw-finder-filters');
					if ( !filters.length ) return;

					var holder = $('.kw-flex-holder');

					if ( !holder.is('.kw-moved') ) {
						holder.addClass('kw-moved');
					} else {
						holder.removeClass('kw-moved');
					}
				});

			}

		},

		initLayoutModule: function(){
			this.controls.on($.knowhere.EVENT, { self: this }, this.changeLayout);
		},

		changeLayout: function(e){

			var $this = $(this),
				container = $('.kw-listings'),
				items = container.find('.job_listing');

			if ( !items.length ) return false;

			$this
				.addClass('kw-active')
				.siblings('.kw-listing-layout-control')
				.removeClass('kw-active');

			container.removeClass('kw-grid-view kw-list-view').addClass('kw-' + $this.data('layout') + '-view');

			if ( container.hasClass('kw-isotope') ) container.isotope('layout');

			$(window).trigger('pxg:refreshmap');

			e.preventDefault();
			return false;
		}

	}

	$.knowhere_job_manager_mod.detailsNav = function() {

		$(".kw-additional-nav a")
			.add('#knowhere-write-review-button')
			.add('#kw-write-review-link')
			.on('click', function (e) {

				e.preventDefault();

				var el = $($(this).attr('href'));

				if ( el.length ) {
					var top = el.offset().top; top -= $('#header').outerHeight();
					$('body, html').animate( { scrollTop: top }, 600 );
				}

			});

	}

	$.knowhere_job_manager_mod.contactForm = function() {

		$('#knowhere-detail-contact-form-btn').click(function(e) {

			e.preventDefault();

			var $this = $(this),
				$form = $this.parents( 'form' );

			$.ajax({
				type: 'post',
				url: knowhere_job_manager_localize.ajaxurl,
				data: $form.serialize(),
				method: $form.attr('method'),
				dataType: "JSON",
				beforeSend: function( ) { },
				success: function( result ) {
					if( result.success ) {
						$('.knowhere-contact-form-messages').empty().append(result.msg);
					} else {
						$('.knowhere-contact-form-messages').empty().append(result.msg);
					}
				}

			});
		});

	}

	/*	Popup Link
	 /* --------------------------------------------- */

	$.knowhere_job_manager_mod.popupLink = function ( link ) {

		if ( !$.isFunction($.fn.magnificPopup) ) return;

		var $link = link ? link : $('.kw-share-popup-link');

		if ( $link.length ) {
			$link.magnificPopup({
				type: 'inline',
				preloader: false,
				//focus: '#full-share-name',
				callbacks: {
					beforeOpen: function() {
						//if($(window).width() < 700) {
						//	this.st.focus = false;
						//} else {
						//	this.st.focus = '#full-share-name';
						//}
					}
				}
			});
		}

	}

	/*	Open Hours Toggle
	 /* --------------------------------------------- */

	$.knowhere_job_manager_mod.openHoursToggle = function () {

		var $hoursHolder = $('.kw-hours-holder');

		if ( !$('.kw-hours-holder').length ) return;

		var $invisible = $('.kw-invisible-hours', $hoursHolder);

if ( $('.kw-switch-toggle').length ) {
		$('.kw-switch-toggle').on($.knowhere.EVENT, function(e) {

			e.preventDefault();

			if ( $hoursHolder.hasClass('kw-visible') ) {
				$hoursHolder.removeClass('kw-visible');

				$invisible.stop().animate({
					'opacity' : 0
				}, 300, function () {
					$(this).slideUp(200);
				});

			} else {
				$hoursHolder.addClass('kw-visible');

				$invisible.stop().animate({
					'opacity' : 1
				}, 50, function () {
					$(this).slideDown(200);
				});

			}

		});
}

	};

	$.knowhere_job_manager_mod.visible_search_jobs = function () {

		var $body = $('body');

		function visibleFilters(el) {

			var $filters = el ? $(el) : $('.job_filters');

			if ( $filters.length ) {
				$filters.addClass('visible-filters');
			}

		}

		if ( $body.hasClass('kw-using-facetwp') ) {

			visibleFilters();

			$(document).on('facetwp-refresh', function() {
				setTimeout(function() {
					visibleFilters();
				}, 10);
			});

		} else {

			$('.job_listings').on('update_results', function(  event, page, append, loading_previous ) {
				visibleFilters();
			});

			if ( $('.kw-listings-search .job_filters').length ) {
				visibleFilters('.kw-listings-search .job_filters');
			}

			$('.kw-listings-controls-wrap .kw-options-list.sort-param-orderby').on('click', 'li', function(e) {
				e.preventDefault();

				var $this = $(this),
					value = $this.data('value');
				if ( value ) {
					var target = $( this ).closest( 'div.job_listings' );
					target.data('orderby', value);
					target.triggerHandler( 'update_results', [ 1, false ] );
				}
			});

		}

	}

	$.knowhere_job_manager_mod.autocompleteLocation = function () {

		function initialize() {

			/* Used on main search page */
			if ( $('#job_location').length ) {
				var input = $('#job_location')[0];
				new google.maps.places.Autocomplete(input);
			}

		}

		if ( typeof google === 'object' && typeof google.maps === 'object' ) {
			google.maps.event.addDomListener( window, 'load', initialize );
		}

	}

	/*	File Uploader
	 /* --------------------------------------------- */

	$.knowhere_job_manager_mod.profileFileUploader = function () {

		var $profileUploader = $('.profile-file-upload');

		if ( $profileUploader.length ) {

			$profileUploader.each(function(i, obj) {
				var $input = $(obj),
					id = $input.attr('id'),
					$label = $('label[for="' + id + '"]'),
					$btn = $('<div class="kw-file-input-field"><div class="kw-file-input-inner"><span class="lnr icon-picture kw-lead-icon"></span><span class="lnr icon-plus"></span>' + knowhere_job_manager_localize.strings['wp-job-manager-file-upload'] + '</div></div>').insertAfter($input);

				$btn.on('click', function() {
					$label.trigger('click');
				});
			});

			$('body').on( 'click', '.job-manager-profile-remove-uploaded-file', function() {
				$(this).closest( '.job-manager-uploaded-file' ).remove();
				return false;
			});

			$profileUploader.each(function(){
				$(this).fileupload({
					dataType: 'json',
					dropZone: $(this),
					url: knowhere_job_manager_localize.ajax_url.toString().replace( '%%endpoint%%', 'upload_file' ),
					maxNumberOfFiles: 1,
					formData: {
						script: true
					},
					add: function (e, data) {
						var $file_field     = $( this );
						var $form           = $file_field.closest( 'form' );
						var $uploaded_files = $file_field.parent().find('.job-manager-uploaded-files');
						var uploadErrors    = [];

						// Validate type
						var allowed_types = $(this).data('file_types');

						if ( allowed_types ) {
							var acceptFileTypes = new RegExp( '(\.|\/)(' + allowed_types + ')$', 'i' );

							if ( data.originalFiles[0].name.length && ! acceptFileTypes.test( data.originalFiles[0].name ) ) {
								uploadErrors.push( job_manager_ajax_file_upload.i18n_invalid_file_type + ' ' + allowed_types );
							}
						}

						if ( uploadErrors.length > 0 ) {
							window.alert( uploadErrors.join( '\n' ) );
						} else {
							$form.find(':input[type="submit"]').attr( 'disabled', 'disabled' );
							data.context = $('<progress value="" max="100"></progress>').appendTo( $uploaded_files );
							data.submit();
						}
					},
					progress: function (e, data) {
						var progress = parseInt(data.loaded / data.total * 100, 10);
						data.context.val( progress );
					},
					fail: function (e, data) {
						var $file_field     = $( this );
						var $form           = $file_field.closest( 'form' );

						if ( data.errorThrown ) {
							window.alert( data.errorThrown );
						}

						data.context.remove();

						$form.find(':input[type="submit"]').removeAttr( 'disabled' );
					},
					done: function (e, data) {
						var $file_field     = $( this );

						var $form           = $file_field.closest( 'form' );
						var $uploaded_files = $file_field.parent().find('.job-manager-uploaded-files');
						var image_types     = [ 'jpg', 'gif', 'png', 'jpeg', 'jpe' ];

						data.context.remove();

						// Handle JSON errors when success is false
						if( typeof data.result.success !== 'undefined' && ! data.result.success ){
							window.alert( data.result.data );
						}

						$.each(data.result.files, function(index, file) {
							if ( file.error ) {
								window.alert( file.error );
							} else {
								var html;

								if ( $.inArray( file.extension, image_types ) >= 0 ) {
									html = $.parseHTML( knowhere_job_manager_localize.js_field_html_img );
									$( html ).find('.job-manager-uploaded-file-preview img').attr( 'src', file.url );
								}

								$( html ).find('.input-text').val( file.url );
								$( html ).find('.input-text').attr( 'name', 'current_' + $file_field.attr( 'name' ) );

								$uploaded_files.html( html );
							}
						});

						$form.find(':input[type="submit"]').removeAttr( 'disabled' );
					}
				});
			});

		}

	}

	/*	Category Isotope
	/* --------------------------------------------- */

	//$.knowhere_job_manager_mod.categoryIsotope = function () {
	//
	//	if ( $('.kw-categories.kw-type-4').length ) {
	//
	//		var isotopeCategories = $('.kw-categories.kw-type-4');
	//
	//		isotopeCategories.each(function( i, el ) {
	//
	//			$(el).children('.kw-categories-inner').isotope({
	//				layoutMode: 'masonry',
	//				items: 1,
	//				loop: true,
	//				nav: true,
	//				navElement: "button",
	//				dots: false,
	//				navText: [],
	//				rtl: getComputedStyle(document.body).direction === 'rtl',
	//				autoplay: false,
	//				autoplayTimeout: 4000,
	//				autoplayHoverPause: true,
	//				smartSpeed: 350,
	//				autoplaySpeed: 350,
	//				navSpeed: 350,
	//				dotsSpeed: 350,
	//				animateIn: false,
	//				animateOut: false
	//			});
	//
	//		});
	//
	//	}
	//
	//}

	/*	DOM READY
	/* --------------------------------------------- */

	$(function () {

		var $window = $(window), $body = $('body');

		if ( $('#job_preview').length ) {
			$body.addClass('single-job_listing single-job_listing_preview');
			$window.trigger('pxg:refreshmap');
			$window.trigger('debouncedresize');
			$('#job_preview').css('opacity', 1);
		}
		if ( $('.kw-button-view').length ) {
			$('.kw-button-view').on( $.knowhere.EVENT, function (e) {
				e.preventDefault();

				$body.toggleClass('kw-show-map');

				$('html, body').scrollTop(0);

				setTimeout(function() {
					$window.trigger('pxg:refreshmap');
				});

			});
		}
if ( $('.kw-button-filter').length ) {
		$('.kw-button-filter').on($.knowhere.EVENT, function (e) {
			e.preventDefault();

			if ( $body.hasClass('kw-show-filters') ) {
				$window.scrollTop(0);
			}
			$body.toggleClass('kw-show-filters');
		});
}
if ( $('.kw-show-more-list-button').length ) {
		$('.kw-show-more-list-button').on($.knowhere.EVENT, function(e) {
			e.preventDefault();

			var $this = $(this);
			var $content = $this.parents('.kw-categories-item-list').children('li.kw-list-item');
			var linkData = $this.data('show');
			var container = $('.kw-categories-inner');

			if ( linkData == 'more' ) {
				linkData = $this.data('show', 'less');
				$this.removeClass('kw-more-list').addClass('kw-less-list');
				$content.removeClass('kw-show-less-list').addClass('kw-show-more-list');
				var linkText = knowhere_job_manager_localize.i18n.show_less;
			} else {
				linkData = $this.data('show', 'more');
				$this.removeClass('kw-less-list').addClass('kw-more-list');
				$content.removeClass('kw-show-more-list').addClass('kw-show-less-list');
				var linkText = knowhere_job_manager_localize.i18n.show_more;
			}

			$this.text(linkText);

		});
}

		$.knowhere_job_manager_mod.popupLink();
		$.knowhere_job_manager_mod.detailsNav();
		$.knowhere_job_manager_mod.contactForm();
		$.knowhere_job_manager_mod.visible_search_jobs();
		$.knowhere_job_manager_mod.openHoursToggle();
		$.knowhere_job_manager_mod.autocompleteLocation();
		$.knowhere_job_manager_mod.profileFileUploader();

		api.controllers.dataService = new api.DataServices.Job();

		// Add auto complete if available.
		if ( api.controllers.dataService.auto ) {
			api.controllers.dataService.autoComplete( api.controllers.dataService.auto );
		}

	});

	$(window).load(function() {

		// initialization of the WP Job manager plugin
		$.knowhere_job_manager_mod.init();
		$.knowhere_job_manager_mod.initScroll();
if ( $('.knowhere-tooltip-trigger').length ) {
		$('.knowhere-tooltip-trigger').on($.knowhere.EVENT, function(e) {
			e.preventDefault();
			$(this).parents('form').toggleClass('active');
		});
}
		var $bookmarks_form = $('.wp-job-manager-bookmarks-form');

		$(document).on('click.tooltipFocusOut', function(e) {
			if ( $bookmarks_form.hasClass('active') && !$(e.target).closest($bookmarks_form).length ) {
				e.preventDefault();
				$bookmarks_form.removeClass('active');
			}
		});

		$(window).trigger('pxg:refreshmap');

	});

})(window);