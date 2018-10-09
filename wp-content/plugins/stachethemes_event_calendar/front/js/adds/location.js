(function ($) {

    "use strict";

    $(document).on('onEventToggleOpen', function (e, data) {

        if (data.event.location == '') {
            return;
        }

        var glob = data.glob;
        var $instance = data.$instance;
        var helper = data.helper;

        var event = data.event;
        var $event = $instance.$events.find('.stec-layout-event.active');
        var $inner = $event.find('.stec-layout-event-inner-location');

        $inner.html(function (index, html) {

            return html
                    .replace(/stec_replace_location/g, event.location)
                    .replace(/stec_replace_details/g, event.location_details);
        });

        if ($.trim($inner.find('.stec-layout-event-inner-location-details').text()) == "") {
            $inner.find('.stec-layout-event-inner-location-optional-details').hide();
        }

        /**
         * @todo Instance or Single?
         */
        var gmap = function () {

            this.$tabCont = "";
            this.directionsService = "";
            this.directionsDisplay = "";
            this.map = "";
            this.geocoder = "";
            this.mapDiv = "";
            this.marker = "";
            this.address = "";

            this.init = function ($el) {

                var parent = this;

                parent.mapDiv = $el.get(0);

                parent.map = new window.google.maps.Map(parent.mapDiv, {
                    zoom: 15
                });

                parent.geocoder = new window.google.maps.Geocoder();

                parent.directionsService = new window.google.maps.DirectionsService;
                parent.directionsDisplay = new window.google.maps.DirectionsRenderer;

                parent.directionsDisplay.setMap(parent.map);

                parent.$tabCont = $el.parents(".stec-layout-event-inner-location");

                parent.address = parent.$tabCont.find(".stec-layout-event-inner-location-address").text();

                parent.setLocation(parent.address);

                // start end directions
                var $start = parent.$tabCont.find('input[name="start"]');

                var $end = parent.$tabCont.find('input[name="end"]');

                if ($.trim($end.val()) == "") {
                    $end.val(parent.$tabCont.find(".stec-layout-event-inner-location-address").text());
                }

                if ($.trim($start.val()) == "") {
                    parent.fillMyLocation($start);
                }

                this.bindControls();

                // Remove tab preloaders
                $inner.find('.stec-layout-event-inner-preload-wrap').children().first().unwrap();
                $inner.find('.stec-layout-event-inner-preload-wrap').remove();
                $inner.find('.stec-preloader').remove();

            };

            this.bindControls = function () {

                var parent = this;

                helper.onResizeEnd(function () {
                    parent.refresh();
                }, 150, 'stec-unbind-window-resize-on-event-close-' + glob.options.id);

                parent.$tabCont
                        .parents('.stec-layout-event-inner')
                        .find('[data-tab="stec-layout-event-inner-location"]')
                        .on(helper.clickHandle(), function (e) {

                            parent.refresh(false);
                        });


                parent.$tabCont.find(".stec-layout-event-inner-location-left-button").on(helper.clickHandle(), function (e) {
                    e.preventDefault();

                    var $tabCont = $(this).parents(".stec-layout-event-inner-location");

                    var $start = $tabCont.find('input[name="start"]');
                    var $end = $tabCont.find('input[name="end"]');
                    var eventLocation = $end.val();

                    if (event.location_use_coord == 1) {
                        var latlng = event.location_forecast.split(',');

                        eventLocation = {
                            lat: parseFloat($.trim(latlng[0])),
                            lng: parseFloat($.trim(latlng[1]))
                        };
                    }

                    if ($start.val() && eventLocation) {
                        parent.getDirection($start.val(), eventLocation);
                    }
                });

            },
                    this.refresh = function (centerOnLocation) {

                        var parent = this;

                        setTimeout(function () {
                            window.google.maps.event.trigger(parent.mapDiv, 'resize');

                            if (centerOnLocation === true) {
                                parent.setLocation(parent.address);
                            }
                        }, 10); // timeout fixes resize bug

                    };

            this.fillMyLocation = function ($el) {

                var parent = this;

                if (glob.options.myLocation) {
                    $el.val(glob.options.myLocation);
                    return;
                }

                if (navigator.geolocation) {

                    navigator.geolocation.getCurrentPosition(
                            function (position) {

                                var pos = position.coords.latitude + " " + position.coords.longitude;
                                parent.geocoder.geocode({'address': pos}, function (results, status) {
                                    glob.options.myLocation = (results[0].formatted_address);
                                    $el.val(glob.options.myLocation);
                                });
                            },
                            function (a, b, c) {
                                console.log('Navigator Geolocation Error');
                            }
                    );
                }
            };

            this.setLocation = function (address) {

                var parent = this;

                if (event.location_use_coord == 1) {

                    var latlng = event.location_forecast.split(',');

                    var pos = {
                        lat: parseFloat($.trim(latlng[0])),
                        lng: parseFloat($.trim(latlng[1]))
                    };

                    parent.map.setCenter(pos);
                    parent.marker = new window.google.maps.Marker({
                        map: parent.map,
                        position: pos,
                        title: address
                    });

                } else {
                    parent.geocoder.geocode({'address': address}, function (results, status) {
                        if (status === window.google.maps.GeocoderStatus.OK) {
                            parent.map.setCenter(results[0].geometry.location);
                            parent.marker = new window.google.maps.Marker({
                                map: parent.map,
                                position: results[0].geometry.location,
                                title: address
                            });

                        } else {
                            console.log("Geocoder error: " + status);
                        }
                    });
                }


                parent.refresh();
            };

            this.getDirection = function (a, b) {

                var parent = this;

                parent.directionsService.route({
                    origin: a,
                    destination: b ? b : parent.marker.position,
                    travelMode: window.google.maps.TravelMode.DRIVING
                }, function (response, status) {

                    if (status === window.google.maps.DirectionsStatus.OK) {
                        parent.directionsDisplay.setDirections(response);
                    } else {
                        console.log("Direction Service Error");

                        $inner.find('.stec-layout-event-inner-location-direction-error').stop().fadeTo(250, 1, function () {

                            setTimeout(function () {

                                $inner.find('.stec-layout-event-inner-location-direction-error').fadeTo(250, 0);

                            }, 3000);

                        });
                    }

                });
            };
        };

        function loadMap() {

            if ($inner.is(':visible')) {
                var $event = $inner.parents('.stec-layout-event');
            } else {
                return;
            }

            var $mapCont = $event.find(".stec-layout-event-inner-location-right-gmap");

            // init once
            if ($mapCont.children().length <= 0) {
                new gmap().init($mapCont);
            }
        }

        $(document).on('stec-tab-click-' + glob.options.id, function () {
            loadMap();
        });

        loadMap();

        // Remove preloader
        $inner.find('.stec-layout-event-inner-preload-wrap').children().first().unwrap();
        $inner.find('.stec-layout-event-inner-preload-wrap').remove();
        $inner.find('.stec-preloader').remove();

    });

})(jQuery);