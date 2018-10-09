

(function ($, window) {

	$.knowhere_woocommerce_mod = $.knowhere_woocommerce_mod || {};

	/*	Cart
	/* --------------------------------------------- */

	$.knowhere_woocommerce_mod.cart = function () {
		({
			init: function () {
				var base = this;

				base.support = {
					touchevents: Modernizr.touchevents,
					transitions: Modernizr.csstransitions
				};

				base.eventtype = base.support.touchevents ? 'touchstart' : 'click';
				base.listeners();
			},
			listeners: function () {
				var base = this;

				base.track_ajax_refresh_cart(base);
				base.track_ajax_adding_to_cart();
				base.track_ajax_added_to_cart(base);
			},
			track_ajax_refresh_cart: function (base) {

				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: knowhere_global_vars.ajaxurl,
					data: {
						action: "knowhere_refresh_cart_fragment"
					},
					success: function (response) {
						base.update_cart_fragment(response.fragments);
						$('body').trigger('wc_fragments_loaded');
					}
				});

			},
			track_ajax_adding_to_cart: function () {

				$('body').on('adding_to_cart', function (e, $thisbutton, $data) {
					e.preventDefault();

					$thisbutton.block({
							message: null,
							overlayCSS: {
								background: '#fff url(' + knowhere_global_vars.ajax_loader_url + ') no-repeat center',
								backgroundSize: '16px 16px',
								opacity: 0.6
							}
						}
					);

				});

			},
			track_ajax_added_to_cart: function (base) {

				$('body').on('added_to_cart', function (e, fragments, cart_hash, $thisbutton) {
					$thisbutton.unblock().hide();
					base.update_cart_dropdown.call(base, e);
				});

			},
			update_count_and_subtotal: function (fragments) {

			},
			update_cart_dropdown: function (e) {
				this.ajax_remove_cart_item(this);
			},
			update_cart_fragment: function (fragments) {
				if ( fragments ) {
					$.each(fragments, function (key, value) {
						$(key).replaceWith(value);
					});
				}
			},
			ajax_remove_cart_item: function (base) {


			}

		}.init());
	}

	/*	DOM READY
	 /* --------------------------------------------- */

	$(function () {
		$.knowhere_woocommerce_mod.cart();
	});

})(jQuery, window);

