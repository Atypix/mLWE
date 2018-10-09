;(function($){

	'use strict';

	$.knowhere = {

		/**
		 * Main Constants
		 **/
		ISRTL: getComputedStyle(document.body).direction === 'rtl',
		TRANSITIONDURATION: 350, // base jQuery animation duration

		EVENT: Modernizr.touchevents ? 'touchstart' : 'click',
		FLEXBOXSUPPORTED: Modernizr.flexbox,
		ISTOUCH: Modernizr.touchevents,
		ANIMATIONSUPPORTED: Modernizr.cssanimations,

		/**
		 * Ancillary constants
		 **/
		TRANSITIONEND : "webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend",
		ANIMATIONEND: "webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend",

		DOMReady: function(){

			this.refresh_elements();

			if( $('.kw-page-header').not('.kw-type-3, .kw-type-4').length ) this.templateHelpers.pageHeader.init();
			if( $('.kw-navigation').length ) this.modules.navigation.init(this);

			this.events.searchBox();

			// dynamically set background image
			if($('[data-bg]').length) this.templateHelpers.bgImage();

			// back to top button init
			this.modules.backToTop.init({
				easing: 'easeOutQuint',
				speed: 550,
				cssPrefix: 'kw-'
			});

			this.modules.modalWindows.init();

			this.modules.dropdown.init({
				'cssPrefix': 'kw-',
				'speed': 500,
				'animation-in': 'fadeInUp',
				'animation-out': 'fadeOutDown'
			});

			// close btn init
			this.modules.closeBtn();

			if ( $('.kw-hidden-aside-invoker' ).length ) this.events.hiddenAside();

			if ( $('.kw-vr-nav-wrap').length ) this.modules.verticalNav.init({
				cssPrefix: 'kw-',
				easing: 'easeOutQuint',
				speed: 500
			});

			if ( $('[data-hidden-container]' ).length ) this.events.hiddenElement();
			if ( $('[data-hidden-item]' ).length ) this.events.hiddenItem();

			// initialize of synchronized carousels
			if( $('.owl-carousel[data-sync]').length ) this.templateHelpers.owlSync.init();

			if ( $('.kw-select-group-container').length ) this.events.selectGroup();

			if ( $('.kw-popup-gallery').length ) {
				$('.kw-popup-gallery').magnificPopup({
					type: 'image',
					mainClass: 'mfp-img-mobile',
					gallery: {
						enabled: true,
						navigateByImgClick: true,
						preload: [0,1] // Will preload 0 - before current, and 1 after the current image
					}
				});
			}
		},

		elements: {
			'html' : 'html',
			'body' : 'body',
			'#theme-wrapper' : 'wrapper',
			'#nav-panel' : 'navPanel',
			'.mobile-advanced': 'navMobile',
			'.kw-navigation': 'navMain'
		},
		$: function (selector) { return $(selector); },
		refresh_elements: function() {
			for (var key in this.elements) {
				this[this.elements[key]] = this.$(key);
			}
		},

		OuterResourcesLoaded: function(){

			// sticky section init
			if ( $('.kw-sticky').length ) this.modules.stickySection.init();
			if ( $('.kw-additional-nav-wrap').length ) this.modules.stickySectionAdditional.init();
			if ( $('.kw-isotope').length ) this.modules.isotope.init();

			var self = this;
			self.templateHelpers.productRating();

			window.knowhereProductRating = self.templateHelpers.productRating;

			// init animation for counters
			// if($('.kw-counters-holder').length) this.modules.animatedCounters.init();

			// init animation for progress bars
			if ($('.kw-progress-bars-holder').length ) this.modules.animatedProgressBars.init({
				speed: 500,
				easing: 'easeOutQuint'
			});
			
		},

		initCarousels: function () {

			var slideshow 		  = $('.owl-carousel.kw-slideshow'),
				slideshowProduct  = $('.owl-carousel.kw-slideshow-product'),
				slideshowThumbs   = $('.owl-carousel.kw-slideshow-thumbs:not([class*="kw-type-"])'),
				slideshowThumbs2  = $('.owl-carousel.kw-slideshow-thumbs.kw-type-2'),
				listingsCarousel  = $('.kw-listings-carousel-v1.owl-carousel'),
				listingsV2        = $('.kw-listings-carousel-v2.owl-carousel'),
				listingsV3        = $('.kw-listings-carousel-v3.owl-carousel'),
				testimonialsV1    = $('.kw-testimonials-carousel-v1.owl-carousel'),
				testimonialsV2    = $('.kw-testimonials-carousel-v2.owl-carousel'),
				testimonialsV3    = $('.kw-testimonials-carousel-v3.owl-carousel'),
				testimonialsV4    = $('.kw-testimonials-carousel-v4.owl-carousel'),
				ribbonSlider      = $('.kw-ribbon-slider.owl-carousel'),
				latestPosts  	  = $('.kw-entries.owl-carousel'),
				jobsCarousel 	  = $('.job_listings.kw-jobs-carousel .job_listings'),
				employersCarousel = $('.kw-employers.owl-carousel'),
				teamMembers 	  = $('.kw-team-members.owl-carousel'),
				partners	  	  = $('.kw-partners-carousel.owl-carousel'),
				properties = 		$('.owl-carousel.kw-featured-properties'),
				//newProperties 	  = $('.owl-carousel.kw-new-properties'),
				featuredListings  = $('.owl-carousel.kw-featured-listings'),
				headerMediaSlider  = $('.owl-carousel.kw-header-media-slider');

			if ( properties.length ) {

				properties.each(function () {

					var $this = $(this);
					var columnsProperties = $this.data('columns');

					var PropertiesItemsConfig = {
							0: {
								items: 1
							},
							520: {
								items: 2
							},
							991: {
								items: 3
							},
							1280: {
								items: columnsProperties
							}
						},
						hasSidebar = properties.closest('.kw-page-content.kw-no-sidebar');

					if ( !hasSidebar.length ) {
						PropertiesItemsConfig = {
							0: {
								items: 1
							},
							520: {
								items: 2
							},
							1199: {
								items: columnsProperties
							}
						}
					}

					$this.owlCarousel($.extend({}, $.knowhere.templateHelpers.owlHelper.baseConfig, {
						responsive: PropertiesItemsConfig,
						margin: 30,
						nav: true,
						autoplay: false,
						loop: false,
						dots: false
					}));


				});

			}

			if ( featuredListings.length ) {

				var featuredListingsItemsConfig = {
						0: {
							items: 1
						},
						520: {
							items: 2
						},
						991: {
							items: 3
						},
						1280: {
							items: 4
						}
					},
					hasSidebar = featuredListings.closest('.kw-section.kw-has-sidebar');

				if(hasSidebar.length){
					featuredListingsItemsConfig = {
						0: {
							items: 1
						},
						520: {
							items: 2
						},
						1199: {
							items: 3
						}
					}
				}

			}

			if(listingsV2.length) {

				listingsV2.owlCarousel($.extend({}, $.knowhere.templateHelpers.owlHelper.baseConfig, {
					items: 1,
					margin: 5,
					nav: true,
					autoplay: true,
					loop: true, // important
					dots: false,
					autoHeight: true,
					autoHeightClass: 'owl-height',
					animateIn: false,
					animateOut: false
				}));

			}

			if(listingsV3.length) {

				listingsV3.owlCarousel($.extend({}, $.knowhere.templateHelpers.owlHelper.baseConfig, {
					responsive: {
						0: {
							items: 1
						},
						600: {
							items: 2
						},
						991: {
							items: 3
						},
						1199: {
							items: 4
						}
					},
					margin: 30,
					nav: true,
					autoplay: true,
					loop: true,
					animateIn: false,
					animateOut: false,
					dots: false
				}));

			}

			if ( ribbonSlider.length ) {
				ribbonSlider.owlCarousel($.extend({}, $.knowhere.templateHelpers.owlHelper.baseConfig, {
					items: 2,
					margin: 2,
					center: true,
					nav: true,
					autoplay: true,
					loop: true,
					animateIn: false,
					animateOut: false,
					dots: false
				}));
			}


			//if(newProperties.length){
			//
			//	var newPropertiesItemsConfig = {
			//			0: {
			//				items: 1
			//			},
			//			520: {
			//				items: 2
			//			},
			//			991: {
			//				items: 3
			//			}
			//		},
			//		hasSidebar = newProperties.closest('.kw-section.kw-has-sidebar');
			//
			//	if(hasSidebar.length){
			//		newPropertiesItemsConfig = {
			//			0: {
			//				items: 1
			//			},
			//			520: {
			//				items: 2
			//			},
			//			1199: {
			//				items: 3
			//			}
			//		}
			//	}
			//
			//	newProperties.owlCarousel($.extend({}, $.knowhere.templateHelpers.owlHelper.baseConfig, {
			//		responsive: newPropertiesItemsConfig,
			//		margin: 30,
			//		nav: true,
			//		autoplay: false,
			//		loop: false, // important
			//		dots: false
			//	}));
			//
			//}

			if ( employersCarousel.length ) {

				var $columns = employersCarousel.data('columns');

				employersCarousel.owlCarousel($.extend({}, $.knowhere.templateHelpers.owlHelper.baseConfig, {
					responsive: {
						0: {
							items: 1
						},
						640: {
							items: 2
						},
						991: {
							items: 3
						},
						1480: {
							items: 4
						},
						1800: {
							items: $columns
						}
					},
					margin: 30,
					nav: true,
					dots: false
				}));

			}

			if(teamMembers.length){

				teamMembers.owlCarousel($.extend({}, $.knowhere.templateHelpers.owlHelper.baseConfig, {
					responsive: {
						0: {
							items: 1
						},
						400: {
							items: 2
						},
						640: {
							items: 2
						},
						991: {
							items: 3
						},
						1300: {
							items: 3
						}
					},
					margin: 30,
					nav: true,
					dots: true
				}));

			}

			if(jobsCarousel.length){

				jobsCarousel.addClass('owl-carousel owl-nav-position-bottom').owlCarousel($.extend({}, $.knowhere.templateHelpers.owlHelper.baseConfig, {
					onInitialized: function(){
						//$.knowhere.templateHelpers.productRating(jobsCarousel.find('.kw-rating'));
					}
				}));

				$.knowhere.templateHelpers.owlHelper.init(jobsCarousel);

			}

			if(slideshow.length){

				slideshow.owlCarousel($.extend({}, $.knowhere.templateHelpers.owlHelper.baseConfig, {
					autoHeight: true,
					autoplay: false, // important
					animateIn: 'fadeIn',
					animateOut: 'fadeOut',
					loop: false // important
				}));

			}

			if(slideshowProduct.length){

				slideshowProduct.owlCarousel($.extend({}, $.knowhere.templateHelpers.owlHelper.baseConfig, {
					autoHeight: true,
					autoplay: false,
					animateIn: 'fadeIn',
					animateOut: 'fadeOut',
					loop: true
				}));

			}

			if(slideshowThumbs.length){

				slideshowThumbs.owlCarousel($.extend({}, $.knowhere.templateHelpers.owlHelper.baseConfig, {
					responsive: {
						0: {
							items: 3
						},
						320: {
							items: 4
						},
						500: {
							items: 5
						}
					},
					margin: 6,
					loop: false,
					mouseDrag: false,
					nav: false,
					dots: false
				}));

			}

			if(slideshowThumbs2.length){

				slideshowThumbs2.owlCarousel($.extend({}, $.knowhere.templateHelpers.owlHelper.baseConfig, {
					responsive: {
						0: {
							items: 3
						},
						320: {
							items: 4
						},
						500: {
							items: 7
						}
					},
					margin: 6,
					loop: false,
					mouseDrag: false,
					nav: true,
					dots: false
				}));

			}

			if ( listingsCarousel.length ) {

				var ListingColumns = listingsCarousel.data('columns') || 4;

				listingsCarousel.owlCarousel($.extend({}, $.knowhere.templateHelpers.owlHelper.baseConfig, {
					nav: true,
					dots: true,
					lazyLoad: true,
					margin: 30,
					responsive: {
						0: {
							items: 1
						},
						620: {
							items: 2
						},
						767: {
							items: 3
						},
						991: {
							items: 4
						},
						1500: {
							items: ListingColumns
						}
					},
					onInitialized: function(){
						$.knowhere.templateHelpers.productRating(listingsCarousel.find('.kw-rating'));
					}
				}));

			}

			if(testimonialsV1.length){

				testimonialsV1.owlCarousel($.extend({}, $.knowhere.templateHelpers.owlHelper.baseConfig, {
					dots: true,
					nav: true,
					animateIn: false,
					animateOut: false,
					smartSpeed: 700,
					fluidSpeed: 700,
					dragEndSpeed: 700,
					navSpeed: 800,
					autoplaySpeed: 700,
					items: 1,
					onInitialized: function(){
						$.knowhere.templateHelpers.productRating(testimonialsV1.find('.kw-rating'));
					}
				}));

			}

			if(testimonialsV2.length){

				testimonialsV2.owlCarousel($.extend({}, $.knowhere.templateHelpers.owlHelper.baseConfig, {
					responsive: {
						0: {
							items: 1
						},
						767: {
							items: 2
						},
						991: {
							items: 3
						}
					},
					dots: true,
					nav: true,
					margin: 30,
					onInitialized: function(){
						$.knowhere.templateHelpers.productRating(testimonialsV2.find('.kw-rating'));
					}
				}));

			}

			if ( testimonialsV3.length ) {

				testimonialsV3.owlCarousel($.extend({}, $.knowhere.templateHelpers.owlHelper.baseConfig, {
					items: 1,
					animateIn: false,
					animateOut: false,
					dots: true,
					autoplay: true,
					smartSpeed: 700,
					fluidSpeed: 700,
					dragEndSpeed: 700,
					dotsSpeed: 800,
					autoplaySpeed: 700,
					nav: false,
					margin: 30,
					onInitialized: function(){
						$.knowhere.templateHelpers.productRating(testimonialsV3.find('.kw-rating'));
					}
				}));

			}

			if ( testimonialsV4.length ) {

				testimonialsV4.owlCarousel($.extend({}, $.knowhere.templateHelpers.owlHelper.baseConfig, {
					items: 1, // important
					dots: true, // important
					loop: false, // important
					nav: false,
					onInitialized: function(){
						//$.knowhere.templateHelpers.productRating(testimonialsV4.find('.kw-rating'));
					}
				}));

				//if($('.kw-testimonials.kw-testimonials-carousel-v4.owl-carousel').length){
					this.templateHelpers.testimonialsOwlV4(testimonialsV4);
				//}


			}

			if ( partners.length ) {

				partners.owlCarousel($.extend({}, $.knowhere.templateHelpers.owlHelper.baseConfig, {
					dots: true,
					nav: true,
					responsive: {
						0: {
							items: 2
						},
						620: {
							items: 3
						},
						991: {
							items: 6
						}
					}
				}));

			}

			if ( latestPosts.length ) {

				latestPosts.owlCarousel($.extend({}, $.knowhere.templateHelpers.owlHelper.baseConfig, {
					responsive: {
						0: {
							items: 1
						},
						620: {
							items: 2
						},
						991: {
							items: 3
						}
					},
					dots: true,
					margin: 30
				}));

			}

			if ( headerMediaSlider.length ) {

				headerMediaSlider.owlCarousel($.extend({}, $.knowhere.templateHelpers.owlHelper.baseConfig, {
					responsive: {
						991: {
							items: 1
						}
					},
					dots: false,
					margin: 0,
					animateOut: 'fadeOut',
					animateIn: 'fadeIn',
					loop: true,
					autoplay: true,
					autoplayTimeout: 4000,
					autoHeight: false,
					autoHeightClass: 'owl-height'
				}));

			}

			$.knowhere.templateHelpers.propertySlideshow();

		},

		loginWithAjax: function() {
			if ( $('.lwa-modal').length ) {

				$('.knowhere-lwa-open-remember-form').add('.knowhere-js-lwa-close-remember-form').on( 'click', function (e) {
					e.stopPropagation(); e.preventDefault();

					$('.js-lwa-login, .js-lwa-remember').toggleClass('knowhere-form-visible');
				});

				$('.js-lwa-open-register-form').on('click', function(e) {
					e.stopPropagation(); e.preventDefault();

					$('.js-lwa-login, .js-lwa-register').toggleClass('knowhere-form-visible');
				});

				$('.lwa-links-register-inline-cancel').on('click', function(e) {
					e.stopPropagation(); e.preventDefault();

					$('.js-lwa-login').addClass('knowhere-form-visible');
					$('.js-lwa-register').removeClass('knowhere-form-visible');
				});


			}
		},

		jQueryExtend: function(){

			$.fn.extend({

				kwImagesLoaded : function () {

				    var $imgs = this.find('img[src!=""]');

				    if (!$imgs.length) {return $.Deferred().resolve().promise();}

				    var dfds = [];

				    $imgs.each(function(){
				        var dfd = $.Deferred();
				        dfds.push(dfd);
				        var img = new Image();
				        img.onload = function(){dfd.resolve();};
				        img.onerror = function(){dfd.resolve();};
				        img.src = this.src;
				    });

				    return $.when.apply($,dfds);

				}

			});

		},

		modules: {

			syncOwlCarousel: {

				init: function(){

					this.collection = $('.owl-carousel[data-sync]');
					if(!this.collection.length) return false;

					this.bindEvents();

				},

				bindEvents: function(){

					var self = this;

					this.collection.each(function(i, el){

						var $this = $(el),
							sync = $($this.data('sync'));

						if(!sync.length){
							console.log('Not found carousel with selector ' + $this.data('sync'));
							return;
						}

						// nav
						$this.on('click', '.owl-prev', function(e){
							sync.trigger('prev.owl.carousel');
						});
						$this.on('click', '.owl-next', function(e){
							sync.trigger('next.owl.carousel');
						});

						sync.on('click', '.owl-prev', function(e){
							$this.trigger('prev.owl.carousel');
						});
						sync.on('click', '.owl-next', function(e){
							$this.trigger('next.owl.carousel');
						});

						// // drag 
						$this.on('dragged.owl.carousel', function(e){

					        if(e.relatedTarget.state.direction == 'left'){
					            sync.trigger('next.owl.carousel');
					        }
					        else{
					            sync.trigger('prev.owl.carousel');
					        }
					        
						});

						sync.on('dragged.owl.carousel', function(e){

							if(e.relatedTarget.state.direction == 'left'){
					            $this.trigger('next.owl.carousel');
					        }
					        else{
					            $this.trigger('prev.owl.carousel');
					        }

						});

					});

				}

			},

			verticalNav: {

				baseConfig: {
					cssPrefix: '',
					easing: 'easeOutQuint',
					speed: 500,
					activeClass: 'selected'
				},

				init: function( options ) {

					this.navigation = $('.kw-vr-nav-wrap');
					if ( !this.navigation.length ) return;

					var config;

					if ( options ) config = $.extend( {}, this.baseConfig, options );

					this.navigation.data('config', config);

					this._defineHelperProperties();

					this.navigation.find( '.kw-sub-menu, .sub-menu' ).hide();

					this.navigation.on( 'click.verticalNav', '.menu-item-has-children a', { self: this }, this.handler);

				},

				_defineHelperProperties: function(){

					var _self = this,
						config = _self.navigation.data('config');

					Object.defineProperties(this, {

						cssPrefix: {
							get: function(){
								return config.cssPrefix;
							}
						},

						submenuSelector: {
							get: function(){
								return '.' + this.cssPrefix + 'sub-menu, .sub-menu';
							}
						},

						itemSelector: {
							get: function(){
								return '.' + this.cssPrefix + 'has-children, .menu-item-has-children';
							}
						},

						activeClass: {
							get: function(){
								return this.cssPrefix + config.activeClass;
							}
						},

						easing: {

							get: function(){

								return config.easing;

							}

						},

						speed: {

							get: function(){

								return config.speed;

							}

						}

					});

				},

				handler: function(e){

					var _self = e.data.self,
						$this = $(this),
						$item = $this.parent();

					if (!$this.hasClass('kw-prevented') && $item.hasClass('menu-item-has-children')) {

						$this.addClass('kw-prevented');

						$item
							.addClass(_self.activeClass)
							.children(_self.submenuSelector)
							.stop()
							.slideDown({
								duration: _self.speed,
								easing: _self.easing
							})
							.end()
							.siblings(_self.itemSelector)
							.removeClass(_self.activeClass)
							.children('.kw-prevented')
							.removeClass('kw-prevented')
							.siblings(_self.submenuSelector)
							.stop()
							.slideUp({
								duration: _self.speed,
								easing: _self.easing
							});

						e.preventDefault();

					}

				},

				reset: function(){



				}

			},

			/**
			 * Generates back to top button
			 **/
			backToTop: {

				init: function(config){
					
					var self = this;

					if(config) this.config = $.extend(this.config, config);

					this.btn = $('<button></button>', {
						class: self.config.cssPrefix+'back-to-top animated stealthy',
						html: '<span class="lnr icon-chevron-up"></span>'
					});

					this.bindEvents();

					$('body').append(this.btn);

				},

				config: {
					breakpoint: 700,
					showClass: 'zoomIn',
					hideClass: 'zoomOut',
					easing: 'linear',
					speed: 500,
					cssPrefix: ''
				},

				bindEvents: function(){

					var page = $('html, body'),
						self = this;

					this.btn.on('click', function(e){

						page.stop().animate({

							scrollTop: 0

						}, {
							easing: self.config.easing,
							duration: self.config.speed
						});

					});

					this.btn.on($.knowhere.ANIMATIONEND, function(e){

						e.preventDefault();
						
						var $this = $(this);

						if($this.hasClass(self.config.hideClass)){

							$this
								.addClass('stealthy')
								.removeClass(self.config.hideClass + " " + self.config.cssPrefix + "inview");

						}

					});

					$(window).on('scroll.backtotop', { self: this}, this.toggleBtn);

				},

				toggleBtn: function(e){

					var $this = $(this),
						self = e.data.self;

					if($this.scrollTop() > self.config.breakpoint && !self.btn.hasClass(self.config.cssPrefix + 'inview')){

						self.btn
								.addClass(self.config.cssPrefix + 'inview')
								.removeClass('stealthy');

						if($.knowhere.ANIMATIONSUPPORTED){
							self.btn.addClass(self.config.showClass);
						}

					}
					else if($this.scrollTop() < self.config.breakpoint && self.btn.hasClass(self.config.cssPrefix + 'inview')){

						self.btn.removeClass(self.config.cssPrefix + 'inview');

						if(!$.knowhere.ANIMATIONSUPPORTED){
							self.btn.addClass('stealthy');
						}
						else{
							self.btn.removeClass(self.config.showClass)
									.addClass(self.config.hideClass);
						}

					}

				}

			},

			/**
			 * Function to show alert message wherever.
			 * Required: Handlebars
			 * 
			 * @param Object options
			 *
			 * @return jQuery messageBox
			 */
			alertMessage: function(options){

				var data = {
					element: options && options.element || $('body'),
					type: options && options.type || 'intermediate',
					message: options && options.message || ''
				};

				var template = '<div class="kw-alert-{{type}}">\
									<button type="button" class="kw-close"></button>\
									<div class="kw-alert-inner">\
										{{message}}\
									</div>\
								</div>';

				var messageBox = $(Handlebars.compile(template)(data)).hide();

				data.element.append(messageBox);

				return messageBox.slideDown({
					duration: 500,
					easing: 'easeOutQuint'
				});

			},

			/**
			 * Describes the behavior of drop-down lists.
			 */
			dropdown: {

				config: {
					'cssPrefix': '',
					'speed': 1000,
					'animation-in': 'fadeInUp',
					'animation-out': 'fadeOutDown'
				},

				init: function(options){

					var self = this;

					if(options) $.extend(this.config, options);

					// Auxiliary properties

					Object.defineProperties(this, {

						invoker: {

							get: function(){

								return '.' + this.config.cssPrefix + 'dropdown-invoker';

							}

						},

						dropdown: {

							get: function(){

								return '.' + this.config.cssPrefix + 'dropdown-list';

							}

						},

						container: {

							get: function(){

								return '.' + this.config.cssPrefix + 'dropdown';

							}

						},

						activeClass: {

							get: function(){

								return this.config.cssPrefix + 'opened';

							}

						}

					});

					$('body').on('click.dropdown', this.invoker, { self: this }, this.handler);
					$(document).on('click.dropdown', function(e){

						e.stopPropagation();
						if(!$(e.target).closest(self.container).length) self.close($('body').find(self.dropdown));

					});

				},

				handler: function(e){

					var $this = $(this),
						self = e.data.self,
						dropdown = $this.siblings(self.dropdown),
						container = $this.closest(self.container);

					if(dropdown.length){

						if(!dropdown.data('initialized')) self.initDropdown(dropdown);

						if(!container.hasClass(self.activeClass)){

							container.addClass(self.activeClass);
							dropdown.addClass(self.config['animation-in']);

						}
						else{

							if(!dropdown.data('timeOutId')){

								self.close(dropdown);

							}


						}

					}

					e.stopPropagation();
					e.preventDefault();

				},

				close: function(dropdown){

					var self = this,
						container = dropdown.closest(this.container);

					dropdown
						.removeClass(self.config['animation-in'])
						.addClass(self.config['animation-out'])
						.data('timeOutId', setTimeout(function(){

							container.removeClass(self.activeClass);
							dropdown
								.removeClass(self.config['animation-out'])
								.data('timeOutId', false);

						}, self.config.speed));

				},

				initDropdown: function(dropdown){

					dropdown
						.addClass('animated')
						.attr('style', 'animation-duration: ' + this.config['speed'] + 'ms')
						.data('initialized', true);

				}

			},

			/**
			 * Describes the loading of the modal windows.
			 */
			modalWindows: {

				init: function(){

					$('.application-button').magnificPopup({
						//delegate: 'application-button',
						type: 'inline',
						preloader: false,
						//focus: '#full-name',
						callbacks: {
							beforeOpen: function() {
								//if($(window).width() < 700) {
								//	this.st.focus = false;
								//} else {
								//	this.st.focus = '#full-name';
								//}
							}
						}
					});

					$('.apply-with-facebook-button').magnificPopup({
						//delegate: 'application-button',
						items: {
							src: '#wp-job-manager-application-details',
							type: 'inline'
						},
						//type: 'inline',
						preloader: false,
						//focus: '#full-name',
						callbacks: {
							beforeOpen: function() {
								//if($(window).width() < 700) {
								//	this.st.focus = false;
								//} else {
								//	this.st.focus = '#full-name';
								//}
							}
						}
					});

					$(document).on('click', '.kw-application-close', function (e) {
						e.preventDefault();
						$.magnificPopup.close();
					});

				}

			},

			/**
			 * Initialize global close event
			 * @return Object Core;
			 **/
			closeBtn: function(){

				$('body').on('click.globalclose', '.kw-close:not(.kw-shopping-cart-full .kw-close)', function(e){

					e.preventDefault();

					$(this).parent().stop().animate({
						opacity: 0
					}, function(){

						$(this).stop().slideUp(function(){

							$(this).remove();

						});

					});

				});

				return this;

			},

			navigation: {

				init: function(base){

					this.navigation = base.navMain;
					this.bindEvents();
					this.createResponsiveButtons(base);
					this.navProcess(base);

					if ( $.knowhere.ISTOUCH ) {
						this.touchNavMobileNavigation(base);
						this.touchNavHeaderNavigation(base);
					}

				},

				navProcess: function (base) {

					var self = this;

					base.navButton.on($.knowhere.EVENT, function (e) {
						e.preventDefault();

						if ( base.html.hasClass('panel-opened') ) {
							base.html.removeClass('panel-opened');
							base.panelOverlay.removeClass('active');
						} else {
							base.html.addClass('panel-opened');
							base.panelOverlay.addClass('active');
						}

					});

					base.panelOverlay.click(function(e) {
						e.preventDefault();
						base.html.removeClass('panel-opened');
						$(this).removeClass('active');
					});

					base.navHide.on($.knowhere.EVENT, function (e) {
						e.preventDefault();
						base.panelOverlay.trigger('click');
					});

					$(window).on('resize', function() {
						if ( $(window).width() > 991 - self.getScrollbarWidth() ) {
							base.panelOverlay.trigger('click');
						}
					});

				},

				touchNavMobileNavigation: function (base) {

					base.navMobile.on($.knowhere.EVENT, '.arrow', function (e) {
						e.preventDefault();
						var $this = $(this),
							$parent = $this.closest('li');

						$this.next().stop().slideToggle();
						if ( $parent.hasClass('open-menu') ) {
							$parent.removeClass('open-menu');
						} else {
							$parent.addClass('open-menu');
						}

					});

				},

				touchNavHeaderNavigation: function (base) {
					var clicked = false;

					$("#header li.menu-item-has-children > a, li.cat-parent > a, #header li.page_item_has_children > a").on($.knowhere.EVENT, function (e) {
						if ( clicked != this ) {
							e.preventDefault();
							clicked = this;
						}
					});

				},

				createResponsiveButtons : function (base) {

					var buttonData = {
						'class' : 'kw-mobile-nav-btn'
					}

					if ( !base.navMobile.length ) return;

					base.navButton = $('<span></span>', buttonData).insertBefore(base.navMain);

					base.navHide = $('<a></a>', {
						id: 'advanced-menu-hide',
						'class': 'advanced-menu-hide',
						'href' : 'javascript:void(0)'
					}).insertAfter(base.navMobile);

					base.panelOverlay = $('<div></div>', {
						'class': 'panel-overlay'
					}).insertBefore(base.navPanel);

				},

				bindEvents: function() {
					this.navigation.on('mouseenter.smart touchstart.mobilenav', 'li[class*="menu-item-has-children"], li[class*="page_item_has_children"]', this.smartPosition);
				},

				getScrollbarWidth: function() {
					// thx David
					if (this.scrollbarSize === undefined) {
						var scrollDiv = document.createElement("div");
						scrollDiv.style.cssText = 'width: 99px; height: 99px; overflow: scroll; position: absolute; top: -9999px;';
						document.body.appendChild(scrollDiv);
						this.scrollbarSize = scrollDiv.offsetWidth - scrollDiv.clientWidth;
						document.body.removeChild(scrollDiv);
					}
					return this.scrollbarSize;
				},

				smartPosition: function(e){

					var $this = $(this),
						$wW = $(window).width();

					var child = $this.children('.kw-sub-menu, ul.children'),
						transformCoeficient = child.outerWidth() - child.outerWidth() * .85;

					if ( $.knowhere.ISRTL ) {

						if ( child.offset().left - transformCoeficient < 0 ) child.addClass('kw-reverse');

					} else {
						var posX = child.offset().left,
							oW = child.outerWidth();

						if ( posX + oW > $wW ) child.addClass('kw-reverse');
					}

				},

				resetSmartPosition: function(e){
					var $this = $(this);

					setTimeout(function() {
						$this.find('.kw-reverse').removeClass('kw-reverse');
					}, 15);
				}

			},

			stickySectionAdditional: {

				STICKYPADDING: 1,

				init: function () {

					this.body = $('body');
					this.sticky = $('.kw-additional-nav-wrap');
					this.headerSticky = $('#header').find('.kw-sticky');
					this.adminbar = $('#wpadminbar');

					if ( !this.sticky.length ) return;

					this.bindEvents();
					this.updateDocumentState();

				},
				updateDocumentState: function () {

					var self = this;

					if (self.resizeTimeoutId) clearTimeout(self.resizeTimeoutId);

					self.resizeTimeoutId = setTimeout(function(){

						if ( $(window).width() < 768 ) return;

						self.stickyHeight = self.sticky.outerHeight();
						self.topHeight = parseInt(self.headerSticky.outerHeight()) - self.STICKYPADDING;

						if ( knowhere_global_vars.sticky == 0 ) {
							self.topHeight = 0;
						}

						if ( self.adminbar.length ) {
							//self.stickyHeight = self.stickyHeight + self.adminbar.outerHeight();
							self.topHeight += self.adminbar.outerHeight();
						}

						self.stickyOffset = self.sticky.offset().top - self.stickyHeight - self.topHeight + 10;

						$(window).trigger('scroll.sticky_bar');

					}, 120);

				},
				bindEvents: function () {

					var $w = $(window),
						self = this;

					$w.on( 'scroll.sticky_bar', { self: this }, this.scrollHandler );
					$w.on( 'resize.sticky_bar', function() {
						self.updateDocumentState();
					});

					self.hasEvents = true;

				},
				scrollHandler: function(e) {

					var $w = $(this),
						self = e.data.self;

					if ( $w.scrollTop() > self.stickyOffset && !self.sticky.hasClass('kw-sticked') ) {

						self.sticky.addClass('kw-sticked');
						self.sticky.css({
							'top' : self.topHeight
						});

						self.fillSpace();

					} else if( $w.scrollTop() <= self.stickyOffset && self.sticky.hasClass('kw-sticked') ) {

						self.sticky.removeClass('kw-sticked');
						self.freeSpace();

					}

				},

				fillSpace: function(){

					var self = this,
						spacer = self.sticky.prev('.kw-sticky-spacer');

					spacer.show().css( 'height', self.stickyHeight );

				},

				freeSpace: function(){
					var self = this,
						parent = self.sticky.parent(),
						spacer = parent.children('.kw-sticky-spacer');

					if ( spacer.length ) spacer.hide();
				}

			},

			/**
			 * Sticky header section
			 **/
			stickySection: {

				STICKYPADDING: 0,
				MAXSTICKYHEIGHT: 90,

				init: function(){

					this.body = $('body');
					this.sticky = $('#header').find('.kw-sticky');
					this.adminbar = $('#wpadminbar');

					if ( knowhere_global_vars.sticky == 0 ) return;

					if ( !this.sticky.length ) return;

					this.bindEvents();
					this.updateDocumentState();

				},

				updateDocumentState: function(){
					
					var self = this;

					if(self.resizeTimeoutId) clearTimeout(self.resizeTimeoutId);

					self.resizeTimeoutId = setTimeout(function(){

						self.reset();

						self.sticky.removeAttr('style');

						if ( $(window).width() < 768 ) return;

						self.stickyHeight = self.sticky.outerHeight();

						if ( self.adminbar.length ) {
							self.stickyHeight = self.stickyHeight - self.adminbar.outerHeight();
						}

						if( self.stickyHeight > self.MAXSTICKYHEIGHT ) {

							self.needScale = true;

							self.defPaddingTop = parseInt(self.sticky.css('padding-top'), 10);
							self.defPaddingBottom = parseInt(self.sticky.css('padding-bottom'), 10);

							self.stickyOffset = self.sticky.offset().top + self.defPaddingTop - self.STICKYPADDING;

							//console.log(self.stickyOffset);

							if ( self.adminbar.length ) {
								self.stickyOffset = self.stickyOffset - self.adminbar.outerHeight();
							}

						} else {

							self.needScale = false;
							self.stickyOffset = self.sticky.offset().top;

							if ( self.adminbar.length ) {
								self.stickyOffset = self.stickyOffset - self.adminbar.outerHeight();
							}

						}					

						$(window).trigger('scroll.sticky');

					}, 120);

				},

				reset: function(){

					var $w = $(window);

					if ( this.sticky.hasClass('kw-sticked') ) {
						this.sticky.removeClass('kw-sticked');
					}

					this.freeSpace();

					if($w.width() < 768 && this.hasEvents){

						var spacer = this.sticky.siblings('.kw-sticky-spacer');
						if(spacer.length) spacer.remove();

						$w.off('scroll.sticky');
						this.hasEvents = false;

						return;

					}
					else if($w.width() >= 768 && !this.hasEvents){

						$w.on('scroll.sticky', {self: this}, this.scrollHandler);
						this.hasEvents = true;

					}

				},

				bindEvents: function() {

					var $w = $(window),
						self = this;

					$w.on( 'scroll.sticky', { self: this }, this.scrollHandler );
					$w.on( 'resize.sticky', function() {
						self.updateDocumentState();
					});

					self.hasEvents = true;

				},

				scrollHandler: function(e){

					var $w = $(this),
						self = e.data.self;

					if ( $w.scrollTop() > self.stickyOffset && !self.sticky.hasClass('kw-sticked') ) {

						self.sticky.addClass('kw-sticked');

						if ( self.needScale ) {

							self.sticky.css({
								'padding-top': self.STICKYPADDING,
								'padding-bottom': self.STICKYPADDING
							});

						}

						self.fillSpace();

					} else if( $w.scrollTop() <= self.stickyOffset && self.sticky.hasClass('kw-sticked') ) {

						self.sticky.removeClass('kw-sticked');

						if(self.needScale){
						
							self.sticky.css({
								'padding-top': self.defPaddingTop,
								'padding-bottom': self.defPaddingBottom
							});

						}

						self.freeSpace();

					}

				},

				fillSpace: function(){

					var self = this,
						parent = self.sticky.parent(),
						spacer = parent.children('.kw-sticky-spacer');

					if ( spacer.length ) {
						spacer.show().css( 'height', self.stickyHeight );
						return false;
					}
					else{

						spacer = $('<div></div>', {
							'class': 'kw-sticky-spacer',
							'style': 'height:' + self.stickyHeight + 'px'
						});

						self.sticky.before(spacer);

					}

				},

				freeSpace: function(){
					var self = this,
						parent = self.sticky.parent(),
						spacer = parent.children('.kw-sticky-spacer');

					if ( spacer.length ) spacer.hide();
				}

			},

			animatedProgressBars: {

				init: function(config){

					this.collection = $('.kw-pbar');
					if(!this.collection.length) return;

					this.holdersCollection = $('.kw-progress-bars-holder');
					this.w = $(window);

					this.preparePBars();

					$.extend(this.config, config);

					this.updateDocumentState();

					this.w.on('resize.animatedprogressbars', this.updateDocumentState.bind(this));

					this.w.on('scroll.animatedprogressbars', {self: this}, this.scrollHandler);

					this.w.trigger('scroll.animatedprogressbars');

				},

				config: {
					speed: $.fx.speed,
					easing: 'linear'
				},

				updateDocumentState: function(){

					this.breakpoint = this.w.height() / 1.4;

				},

				preparePBars: function(){

					this.collection.each(function(i, el){

						var $this = $(el),
							indicator = $this.children('.kw-pbar-inner'),
							value = $this.data('value');

						$this.add(indicator).data('r-value', value);
						$this.add(indicator).attr('data-value', 0);

						indicator.css('width', 0);

					});

				},

				scrollHandler: function(e){

					var self = e.data.self;

					self.holdersCollection.each(function(i, el){

						var holder = $(el);

						if(self.w.scrollTop() + self.breakpoint >= holder.offset().top && !holder.hasClass('kw-animated')){

							self.animateAllBarsIn(holder);
							holder.addClass('kw-animated');

							if(i === self.holdersCollection.length - 1) self.destroy();

						}

					});


				},

				animateAllBarsIn: function(holder){

					var self = this,
						pbarsCollection = holder.find('.kw-pbar');

					pbarsCollection.each(function(i, el){

						var pbar = $(el),
							indicator = pbar.children('.kw-pbar-inner'),
							value = pbar.data('r-value'),
							unit = pbar.data('unit'),
							pbarWidth = pbar.outerWidth();

						indicator.stop().animate({
							width: value + '%'
						}, {
							duration: self.config.speed,
							easing: self.config.easing,
							step: function(now){
								pbar.add(indicator).attr('data-value', Math.round(now) + unit);
							}
						});

					});

				},

				destroy: function(){

					this.w.off('scroll.animatedprogressbars');

				}

			},	

			animatedCounters: {

				init: function(){

					this.collection = $('.kw-counter');
					if(!this.collection.length) return;

					this.w = $(window);

					this.prepareCounters();
					this.updateDocumentState();

					this.w.on('scroll.animatedcounter', {self: this}, this.scrollHandler);
					this.w.on('resize.animatedcounter', this.updateDocumentState.bind(this));

					this.w.trigger('scroll.animatedcounter');

				},

				updateDocumentState: function(){

					this.breakpoint = this.w.height() / 1.4;

				},

				prepareCounters: function(){

					this.collection.each(function(i, el){

						var $this = $(el),
							value = $this.data('value');

						$this.data('r-value', value);
						$this.attr('data-value', 0);
						$this.find('.kw-counter-value').text(0)

					});

				},

				scrollHandler: function(e){

					var self = e.data.self;

					self.collection.each(function(i, el){

						var counter = $(el);

						if(self.w.scrollTop() + self.breakpoint > counter.offset().top && !counter.hasClass('nv-animated')){

							counter.addClass('nv-animated');
							self.animateCounter(counter);

							if(i === self.collection.length - 1) self.destroy();

						}

					});

				},

				animateCounter: function(counter){

					var value = counter.data('r-value'),
						intId, currentValue = 0;

					intId = setInterval(function(){

						counter.attr('data-value', currentValue+=19);
						counter.find('.kw-counter-value').text(currentValue+=19);

						if(currentValue >= value){
							counter.attr('data-value', value);
							counter.find('.kw-counter-value').text(value);
							clearInterval(intId);
						}

					}, 4);

				},

				destroy: function(){

					this.w.off('scroll.animatedcounter');
					this.w.off('resize.animatedcounter');

				}

			},

			maxHeightItems: function( collection, isotope, config ) {

				collection = collection ? collection : $('.kw-listings');
				if ( !collection.length || collection.hasClass('kw-listings-carousel-v2') ) return;

				collection.not('.kw-type-2, .kw-type-3, .kw-type-4').each(function() {

					var $this = $(this);

					$this.kwImagesLoaded().then(function () {

						var max = 0,
							items = $this.find('.kw-entry-wrap, .kw-advertising-wrap, .kw-listing-item-wrap'),
							elements = items.find('.kw-listing-item-info').css('height', 'auto');

						elements.each(function (i, el) {
							var $this = $(el),
								height = $this.outerHeight();

							if (height > max) max = height;
						});

						elements.animate({
							height: max
						}, {
							duration: 150,
							complete: function () {
								if (isotope) {
									collection.isotope(config);
								}
								collection.removeClass('kw-loading');
							}
						});

					});

				});

			},

			isotope: {

				baseConfig: {
					itemSelector: '.kw-entry-wrap, .kw-advertising-wrap, .kw-listing-item-wrap',
					percentPosition: true,
					transitionDuration: '0.5s'
				},

				init: function(){

					this.collection = $('.kw-isotope');

					if(!this.collection.length) return;

					if(window.navigator.userAgent.toLowerCase().indexOf('android') !== -1) this.collection.addClass('kw-android');

					$.extend(this.baseConfig, {
						isOriginLeft: !$.knowhere.ISRTL
					});

					this.run();

				},

				run: function(){

					var self = this;

					this.collection.each(function(i, el) {

						var container = $(el),
							holder = container.parents('.kw-listings-holder'),
							config = $.extend({
								layoutMode: container.data('masonry') ? 'masonry' : 'fitRows'
							}, self.baseConfig);



						if ( container.data('sort') ) {
							self.initFilter(container, holder);
						}

						//if ( container.data('load-more-element') ) {
						//	self.initLoadMore(container);
						//}

						$.knowhere.modules.maxHeightItems( container, true, config );

						container.kwImagesLoaded().then(function(){
							container.isotope(config);
						});

					});

				},

				initFilter: function(isotope, holder){

					var filterElement = $('.kw-filter', holder);

					if(!filterElement.length) return;

					filterElement.on('click.filter', '[data-filter]', function(e){

						e.preventDefault();

						var $this = $(this);

						$this
							.addClass('kw-active')
							.siblings()
							.removeClass('kw-active');

						isotope.isotope({filter: $this.data('filter')});

					});

				}

			}

		},

		events: {

			searchBox: function() {

				var $searchbox = $('.kw-hidden-search-box');

				if ( !$searchbox.length ) return;

				if ( $('.kw-search-box-opener').length ) {
					$('.kw-search-box-opener').magnificPopup({
						items: {
							src: '#search-box',
							type: 'inline'
						},
						preloader: false,
						focus: '#s',
						callbacks: {
							beforeOpen: function() {

							}
						}
					});
				}

			},

			selectGroup: function() {

				var container = $('.kw-select-group-container');
					//group = container.find('.kw-select-group-inner'),
					//defaultIcon = container.find('.kw-select-group-icon-default'),
					//activeIcon = container.find('.kw-select-group-icon-active');

				if ( !container.length ) return;

				container.each(function () {

					var cont = $(this),
						group = cont.find('.kw-select-group-inner');

					group.on('click', function(e){

						e.stopPropagation();

					});

					cont.on('click', function(e){

						$(this).toggleClass('kw-active');

						group.slideToggle();

					});

				});

			},

			hiddenAside: function(){

				var aside = $('#hidden-aside'),
					page = $('.kw-wide-layout-type');
				if ( !aside.length || !page.length ) return;

				$('body').on('click.hiddenAside', '.kw-hidden-aside-invoker', function(e) {
					aside.add(page).addClass('kw-moved');
					e.preventDefault();
				});

				$('body').on('click.hiddenAside', '.kw-hidden-aside-close', function(e){
					aside.add(page).removeClass('kw-moved');
					e.preventDefault();
				});

			},

			hiddenElement: function() {

				var $body = $('body');

				function handler($container) {

					if(!$container.length) return;

					var animateIn = $container.data('animate-in') ? $container.data('animate-in') : 'fadeInDown',
						animateOut = $container.data('animate-out') ? $container.data('animate-out') : 'fadeOutDown';

					// init
					if(!$container.hasClass('animated')) {
						$container.addClass('animated');
						$container.on($.knowhere.ANIMATIONEND, function(e){

							if($container.hasClass(animateOut)) {
								$container.removeClass(animateOut + ' kw-visible');
								if($container.data('lock-body')) $body.removeClass('kw-locked-by-hidden-element');
							}

						});
					}

					if($container.hasClass(animateIn)) {
						$container.removeClass(animateIn).addClass(animateOut);
						return;
					}

					
					$container.removeClass(animateOut).addClass(animateIn + ' kw-visible');
					if($container.data('lock-body')) $body.addClass('kw-locked-by-hidden-element');

				}

				$(document).on('keyup', function(e){
					if(e.keyCode == 27) {
						$('.kw-hidden-element.kw-visible').each(function(){

							handler($(this));

						});
					}
				});

				$body.on('click.hiddenElement','[data-hidden-container]', function(e){

					var $this = $(this),
						$container = $($this.data('hidden-container'));

					handler($container);

					e.preventDefault();

				});

				if( $.knowhere.ISTOUCH ) {
					$body.on('click.hiddenElementFocusOut', function(e){

						if(!$(e.target).closest('.kw-hidden-element').length && !($(e.target).attr('data-hidden-container') || $(e.target).closest('[data-hidden-container]').length )) {

							$('.kw-hidden-element.kw-visible').each(function(){
								handler($(this));
							});

						}

					});
				}

			},

			hiddenItem: function() {

				$('body').on('click.hiddenItem', '[data-hidden-item]', function(e) {

					var $this = $(this),
						secondStateText = $this.data('second-state-text'),
						$item = $($this.data('hidden-item'));

					if (!$this.data('base-text') ) $this.data( 'base-text', $this.text() );

					if(!$item.length) return;

					$this.toggleClass('kw-active');

					if ( secondStateText ) {

						if ( $this.hasClass('kw-active') ) {
							$this.text(secondStateText);
						} else {
							$this.text($this.data('base-text'));
						}

					}

					$item.slideToggle({
						duration: $.knowhere.TRANSITIONDURATION,
						easeing: 'easeOutQuint'
					});

					e.preventDefault();

				});

			}

		},

		templateHelpers: {

			owlSync: {

				init: function(){

					this.collection = $('.owl-carousel[data-sync]');
					if(!this.collection.length) return;

					this.prepare();

				},

				prepare: function(){

					this.collection.each(function(i, el){

						var $this = $(el),
							sync = $($this.data('sync'));

						sync.on('changed.owl.carousel', function(e){

							var index = e.item.index,
								$thumb = $this.find('.owl-item').eq(index).find('.kw-slideshow-thumb');

							if(!sync.data('afterClicked')) $this.trigger('to.owl.carousel', [index, 350, true]);

							sync.data('afterClicked', false);

							if($thumb.length) {
								
								$thumb.addClass('kw-active')
								.closest('.owl-item')
								.siblings()
								.find('.kw-slideshow-thumb')
								.removeClass('kw-active');

							}



						});

						$this.on('prev.owl.carousel', function(){

							sync.trigger('prev.owl.carousel');

						});

						$this.on('next.owl.carousel', function(){

							sync.trigger('next.owl.carousel');

						});

						$this.on('click.owlSync', '.owl-item', function(e){

							e.preventDefault();

							var index = $(this).index(),
								thumb = $(this).find('.kw-slideshow-thumb');

							if(thumb.length) {

								thumb
									.addClass('kw-active')
									.closest('.owl-item')
									.siblings()
									.find('.kw-slideshow-thumb')
									.removeClass('kw-active');

							}

							sync.data('afterClicked', true);

							sync.trigger('to.owl.carousel', [index, 350, true]);

						});

					});

				}

			},

			/**
			 * Dynamically set background image
			 * @return jQuery collection;
			 **/
			bgImage: function(collection){

				collection = collection ? collection : $('[data-bg]');
				if(!collection.length) return;

				collection.each(function(i, el){

					var $this = $(el),
						imageSrc = $this.data('bg');

					if(imageSrc) $this.css('background-image', 'url('+imageSrc+')');

				});

				return collection;

			},

			testimonialsOwlV4: function(collection){

				collection = collection ? collection : $('.kw-testimonials.kw-testimonials-carousel-v4.owl-carousel');
				if(!collection.length) return;

				collection.each(function(i, el){

					var $this = $(el),
						$dots = $this.find('.owl-dot');

					$this.find('.kw-testimonial').each(function(i, el){

						var authorBox = $(this).find('.kw-author-box');

						$dots.eq(i).append(authorBox);

					});


				});

			},

			/**
			 * Sets correct inner offsets in page header (only for fixed header types)
			 * @return undefined;
			 **/
			pageHeader: {

				init: function() {

					var header = $('#header'),
						pageHeader = $('.kw-page-header'),
						$w = $(window);

					function correctPosition() {

						if ( $w.width() < 768 ) return false;

						var hHeight = header.outerHeight();

						pageHeader.add(pageHeader.find('.kw-page-header-media')).css({
							'margin-top': hHeight * -1
						});

						pageHeader.find('.kw-page-header-content').css({
							'padding-top' : hHeight
						})

					}

					correctPosition();

					$(window).on('resize.pageHeader', correctPosition);

				}

			},

			owlHelper: {

				baseConfig: {
					items: 1,
					loop: true,
					nav: true,
					navElement: "button",
					dots: false,
					navText: [],
					rtl: getComputedStyle(document.body).direction === 'rtl',
					autoplay: false,
					autoplayTimeout: 4000,
					autoplayHoverPause: true,
					smartSpeed: 350,
					autoplaySpeed: 350,
					navSpeed: 350,
					dotsSpeed: 350,
					animateIn: false,
					animateOut: false
				},

				init: function(collection) {

					collection = collection ? collection : $('.owl-carousel');
					if ( !collection.length ) return;

					collection.addClass('kw-loading');

					this.adaptive(collection);

				},

				adaptive: function(collection){

					var self = this;

					collection.kwImagesLoaded().then(function() {

						collection.each(function(i, el) {

							var $this = $(el);

							if ( $this.hasClass('kw-listings') ) {
								$.knowhere.modules.maxHeightItems( $this, false );
							}

							//var owlData = $this.data('owl.carousel');
							//
							//if ( owlData ) {
							//	if ( owlData.settings.dots ) {
							//		$this.addClass('owl-dotted');
							//	}
							//}
							//
							//$this.on('resized.owl.carousel', function(e) {
							//	self.containerHeight($this);
							//});
							//
							//$this.on('changed.owl.carousel', function(e) {
							//	self.containerHeight($this);
							//});
							//
							//self.containerHeight($this);


							$this.removeClass('kw-loading');

						});

					});

				},

				containerHeight: function(owl) {

					var _this = this;

					setTimeout(function(){

						var max = 0,
							items = owl.find('.owl-item'),
							activeItems = items.filter('.active').children();

						items.children().css( 'height', 'auto' );

						activeItems.each( function( i, el ) {

							var $this = $(el),
								height = $this.outerHeight();

							if ( height > max ) max = height;

						});

						owl.find('.owl-stage-outer:first').stop().animate({
							height: max
						}, {
							duration: 150,
							complete: function(){

								//var isotopeContainer = owl.closest('.kw-isotope');
								var owlContainer = owl.closest('.owl-carousel');

								//if(isotopeContainer.length) isotopeContainer.isotope('layout');
								if ( owlContainer.length ) _this.containerHeight(owlContainer);

							}
						});

					}, 20);

				}

			},

			/**
			** product raring
			**/
			productRating : function(collection){

				var $ratings = collection ? collection : $('.kw-rating').not('.owl-carousel .kw-rating');

				$ratings.each(function() {

					var $this = $(this);

					if ( !$this.children().length ) {

						$this.append("<div class='kw-empty-state'><i class='kw-icon-star-empty'></i><i class='kw-icon-star-empty'></i><i class='kw-icon-star-empty'></i><i class='kw-icon-star-empty'></i><i class='kw-icon-star-empty'></i></div><div class='kw-fill-state'><i class='kw-icon-star'></i><i class='kw-icon-star'></i><i class='kw-icon-star'></i><i class='kw-icon-star'></i><i class='kw-icon-star'></i></div>");

						var rating = $this.data("rating"),
							fillState = $this.children('.kw-fill-state'),
							w = $this.outerWidth();

						fillState.css('width', Math.floor(rating / 5 * w));

					}

				});

			},

			propertySlideshow: function( slideshow ) {

				var $slideshow = slideshow ? slideshow : $('.kw-property-slideshow.owl-carousel');

				if ( $slideshow.length ) {

					$slideshow.owlCarousel($.extend({}, $.knowhere.templateHelpers.owlHelper.baseConfig, {
						autoplay: false,
						loop: false,
						mouseDrag: false,
						touchDrag: false
					}));

				}

			}

		}

	};

	$.knowhere.jQueryExtend();

	$(function() {
		$.knowhere.DOMReady();
	});

	$(window).on('load', function() {

		$.knowhere.OuterResourcesLoaded();
		$.knowhere.initCarousels();
		$.knowhere.loginWithAjax();

		if ( $('.owl-carousel').length ) {
			$.knowhere.templateHelpers.owlHelper.init();
		}

	});

})(jQuery);

function knowhere_fbRowGetAllElementsWithAttribute( attribute ) {
	var matchingElements = [],
		allElements = document.getElementsByTagName( '*' ),
		i,
		n;

	for ( i = 0, n = allElements.length; i < n; i++ ) {
		if ( allElements[i].getAttribute( attribute ) ) {
			matchingElements.push( allElements[i] );
		}
	}
	return matchingElements;
}


function knowhere_fbRowOnPlayerReady( event ) {
	var player   = event.target,
		currTime = 0,
		firstRun = true,
		prevCurrTime,
		timeLastCall;

	//player.playVideo();
	if ( player.isMute ) {
		player.mute();
	}

	prevCurrTime = player.getCurrentTime();
	timeLastCall = +new Date() / 1000;

	player.loopInterval = setInterval( function() {
			if ( 'undefined' !== typeof player.loopTimeout ) {
				clearTimeout( player.loopTimeout );
			}

			if ( prevCurrTime === player.getCurrentTime() ) {
				currTime = prevCurrTime + ( +new Date() / 1000 - timeLastCall );
			} else {
				currTime = player.getCurrentTime();
				timeLastCall = +new Date() / 1000;
			}
			prevCurrTime = player.getCurrentTime();

			if ( currTime + ( firstRun ? 0.45 : 0.21 ) >= player.getDuration() ) {
				player.pauseVideo();
				player.seekTo( 0 );
				player.playVideo();
				firstRun = false;
			}
		}, 150
	);
}

function knowhere_fbRowOnPlayerStateChange( event ) {
	if ( event.data === YT.PlayerState.ENDED ) {
		if ( 'undefined' !== typeof event.target.loopTimeout ) {
			clearTimeout( event.target.loopTimeout );
		}
		event.target.seekTo( 0 );

		// Make the video visible when we start playing
	} else if ( event.data === YT.PlayerState.PLAYING ) {
		jQuery( event.target.getIframe() ).parent().css( 'visibility', 'visible' );
	}
}

function onYouTubeIframeAPIReady() {
	var videos = knowhere_fbRowGetAllElementsWithAttribute( 'data-youtube-video-id' ),
		i, videoID, elemID, k, player;

	for ( i = 0; i < videos.length; i++ ) {

		videoID = videos[i].getAttribute( 'data-youtube-video-id' );

		if ( '' === videos[ i ] ) {
			continue;
		}

		player = new YT.Player(
			videos[ i ], {
				width: "100%",
				height: "100%",
				videoId: videoID,
				playerVars: {
					autohide: 1,
					autoplay: 1,
					loop: 0,
					fs: 0,
					showinfo: 0,
					modestBranding: 1,
					start: 0,
					controls: 0,
					rel: 0,
					disablekb: 1,
					iv_load_policy: 3,
					wmode: 'transparent'
				},
				events: {
					'onReady': knowhere_fbRowOnPlayerReady,
					'onStateChange': knowhere_fbRowOnPlayerStateChange
				}
			}
		);

		player.isMute = true;

		// Force YT video to load in HD
		if ( 'true' === videos[ i ].getAttribute( 'data-youtube-video-id' ) ) {
			player.setPlaybackQuality( 'hd720' );
		}

	}
}

knowhere_resize_video_background();

jQuery(window).bind("debouncedresize", function() {
	knowhere_resize_video_background()
});

function knowhere_resize_video_background() {
	var $element = jQuery('.kw-page-entry-featured');
	var iframeW, iframeH, marginLeft, marginTop, containerW = $element.innerWidth(),
		containerH = $element.innerHeight(),
		ratio1 = 16,
		ratio2 = 9;
		containerW / containerH < ratio1 / ratio2 ?
			( iframeW = containerH * (ratio1 / ratio2),
			iframeH = containerH,
			marginLeft = -Math.round((iframeW - containerW) / 2) + "px",
			marginTop = -Math.round((iframeH - containerH) / 2) + "px",
			iframeW += "px", iframeH += "px" ) : (iframeW = containerW, iframeH = containerW * (ratio2 / ratio1), marginTop = -Math.round((iframeH - containerH) / 2) + "px", marginLeft = -Math.round((iframeW - containerW) / 2) + "px", iframeW += "px", iframeH += "px"),
		$element.find(".kw-video-bg iframe").css({
			maxWidth: "1000%",
			marginLeft: marginLeft,
			marginTop: marginTop,
			width: iframeW,
			height: iframeH
		});

		setTimeout(function() {
			$element.addClass('kw-is-stretched');
		}, 150);
}