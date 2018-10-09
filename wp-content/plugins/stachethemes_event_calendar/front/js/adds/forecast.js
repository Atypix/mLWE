(function ($) {

    "use strict";


    $(document).on('onBeforeEventWeatherDataAjax', function (e, data) {

        var glob = data.glob;
        var $instance = data.$instance;

        var $tab = $instance.find('.stec-layout-event.active').find('.stec-layout-event-inner-forecast');
        $tab.wrapInner('<div class="stec-layout-event-inner-preload-wrap" />');
        $(glob.template.preloader).appendTo($tab);
    });

    $(document).on('onEventToggleOpen', function (e, data) {

        if ( !data.event.location_forecast ) {
            return;
        }
        
        var glob = data.glob;
        var $instance = data.$instance;
        var helper = data.helper;

        var $event = $instance.$events.find('.stec-layout-event.active');
        var $inner = $event.find('.stec-layout-event-inner-forecast');

        if ( $inner.length <= 0 || !$inner.find('.stec-layout-event-inner-forecast-details-left-forecast-day-template')[0] ) {
            return;
        }
        var template = $inner.find('.stec-layout-event-inner-forecast-details-left-forecast-day-template')[0].outerHTML;
        $inner.find('.stec-layout-event-inner-forecast-details-left-forecast-day-template').remove();

        function floorFigure(figure, decimals) {
            if ( !decimals )
                decimals = 2;
            var d = Math.pow(10, decimals);
            return (parseInt(figure * d) / d).toFixed(decimals);
        }

        var getWeather = function () {

            if ( data.event.forecast ) {

                if ( !data.event.forecast.error ) {
                    fillData();
                } else {
                    fillError();
                }
                return;
            }

            var location = data.event.location_forecast;

            glob.ajax = $.ajax({
                type: 'POST',
                url: window.ajaxurl,
                data: {
                    action: 'stec_public_ajax_action',
                    task: 'get_weather_data',
                    location: function () {

                        location = location.split(',');

                        location[0] = floorFigure(location[0]);
                        location[1] = floorFigure(location[1]);

                        location = location.join(',');

                        return location;

                    }
                },
                beforeSend: function () {
                    if ( glob.ajax !== null ) {
                        glob.ajax.abort();
                    }

                    $instance.trigger('onBeforeEventWeatherDataAjax', {
                        $instance: $instance,
                        glob: glob
                    });
                },
                success: function (rtrn) {

                    if ( rtrn ) {

                        if ( !data.event.forecast ) {
                            data.event.forecast = rtrn;
                        }

                        // error ?
                        if ( data.event.forecast.error || !data.event.forecast ) {
                            fillError();
                            return;
                        }

                        fillData();
                    } else {
                        fillError();
                    }

                },
                error: function (xhr, status, thrown) {
                    fillError();
                    console.log(xhr + " " + status + " " + thrown);
                },
                complete: function () {
                    glob.ajax = null;

                    // Remove tabs preloaders
                    $inner.find('.stec-layout-event-inner-preload-wrap').children().first().unwrap();
                    $inner.find('.stec-layout-event-inner-preload-wrap').remove();
                    $inner.find('.stec-preloader').remove();
                },
                dataType: "json"
            });
        };

        var fillData = function () {

            var fiveDays = [];

            var i = 0;

            var forecast = data.event.forecast;

            $(forecast.daily.data).each(function () {

                if ( i > 4 )
                    return false;

                var th = this;
                var icon = iconToiconHTML(th.icon, true);

                // Localtime
                var d = helper.treatAsUTC(new Date(this.time * 1000));
                d.setHours(d.getHours() + forecast.offset);

                var niceday = helper.beautifyTimespan(d, d, 1);

                fiveDays[i] = $(template).html(function (index, html) {

                    var tempFmin = Math.round(th.temperatureMin);
                    var tempCmin = Math.round((tempFmin - 32) * 5 / 9);

                    var tempFmax = Math.round(th.temperatureMax);
                    var tempCmax = Math.round((tempFmax - 32) * 5 / 9);

                    return html
                            .replace(/\bstec_replace_date\b/g, niceday)
                            .replace(/\bstec_replace_min\b/g, glob.options.general_settings.temp_units == "C" ? tempCmin : tempFmin)
                            .replace(/\bstec_replace_max\b/g, glob.options.general_settings.temp_units == "C" ? tempCmax : tempFmax)
                            .replace(/\bstec_replace_temp_units\b/g, glob.options.general_settings.temp_units == "C" ? "C" : "F")
                            .replace(/\bstec_replace_icon_div\b/g, icon);
                })[0].outerHTML;

                i++;
            });

            fiveDays = fiveDays.join('');

            $inner.html(function (index, html) {

                var icon = iconToiconHTML(forecast.currently.icon);

                var tempF = Math.round(forecast.currently.temperature);
                var tempC = Math.round((tempF - 32) * 5 / 9);

                var apTempF = Math.round(forecast.currently.apparentTemperature);
                var apTempC = Math.round((tempF - 32) * 5 / 9);

                var windMPH = Math.round(forecast.currently.windSpeed);
                var windKPH = Math.round(windMPH * 1.609344);

                // Local time
                var d = helper.treatAsUTC(new Date(forecast.currently.time * 1000));
                d.setHours(d.getHours() + forecast.offset);

                var niceday = helper.beautifyTimespan(d, d, 1);

                return html
                        .replace(/\bstec_replace_current_summary_text\b/g, iconToText(forecast.currently.icon))
                        .replace(/\bstec_replace_today_date\b/g, niceday)
                        .replace(/\bstec_replace_location\b/g, helper.capitalizeFirstLetter(data.event.location))
                        .replace(/\bstec_replace_current_temp\b/g, glob.options.general_settings.temp_units == "C" ? tempC : tempF)
                        .replace(/\bstec_replace_current_feels_like\b/g, glob.options.general_settings.temp_units == "C" ? apTempC : apTempF)
                        .replace(/\bstec_replace_current_humidity\b/g, forecast.currently.humidity * 100)
                        .replace(/\bstec_replace_current_temp_units/g, glob.options.general_settings.temp_units == "C" ? "C" : "F")
                        .replace(/\bstec_replace_current_wind\b/g, glob.options.general_settings.wind_units == "MPH" ? windMPH : windKPH)
                        .replace(/\bstec_replace_current_wind_units\b/g, glob.options.general_settings.wind_units == "MPH" ? "MPH" : "KPH")
                        .replace(/\bstec_replace_current_wind_direction\b/g, getWindDir(forecast.currently.windBearing))
                        .replace(/\bstec_replace_today_icon_div\b/g, icon)
                        .replace(/\bstec_replace_5days\b/g, fiveDays);

            });

            // Chart instance
            setTimeout(function () {


                var humidity = [],
                        tempC = [],
                        tempF = [],
                        rain = [],
                        j = -1;


                var charTimeLabels = [];

                for ( var i = 0; i < 8; i++ ) {

                    j = j + 3;

                    var th = forecast.hourly.data[j];

                    var tempf = Math.round(th.temperature);
                    var tempc = Math.round((tempf - 32) * 5 / 9);

                    tempC[i] = tempc;
                    tempF[i] = tempf;
                    humidity[i] = Math.round(th.humidity * 100);
                    rain[i] = th.precipProbability * 100;
                    
                    var timeFormat = 'HH:mm';

                    if ( glob.options.general_settings.time_format == '12' ) {
                        timeFormat = 'hh:mma';
                    }

                    // Local time
                    var d = helper.treatAsUTC(new Date(th.time * 1000));
                    charTimeLabels.push(window.moment(d).format(timeFormat));

                }

                var ch = new chart();

                ch.setCanvas($inner.find('.stec-layout-event-inner-forecast-details-chart canvas'));

                ch.setChartData({
                    labels: charTimeLabels,
                    datasets: [
                        {
                            label: window.stecLang.humidity_percents,
                            data: humidity,
                            backgroundColor: "rgba(200,200,200,0.1)",
                            borderColor: "rgba(200,200,200,1)",
                            pointBackgroundColor: "rgba(200,200,200,1)",
                            fill: true,
                            lineTension: 0.3,
                            pointHoverRadius: 5,
                            pointHitRadius: 10,
                            borderWidth: 1
                        },
                        {
                            label: window.stecLang.rain_chance_percents,
                            data: rain,
                            backgroundColor: "rgba(70,129,195,0.1)",
                            borderColor: "rgba(70,129,195,1)",
                            pointBackgroundColor: "rgba(70,129,195,1)",
                            fill: true,
                            lineTension: 0.3,
                            pointHoverRadius: 5,
                            pointHitRadius: 10,
                            borderWidth: 1
                        },
                        {
                            label: window.stecLang.temperature + ' ' + '\u00B0' + (glob.options.general_settings.temp_units == "C" ? 'C' : 'F'),
                            data: glob.options.general_settings.temp_units == "C" ? tempC : tempF,
                            backgroundColor: "rgba(252,183,85,0.3)",
                            borderColor: "rgba(252,183,85,1)",
                            pointBackgroundColor: "rgba(252,183,85,1)",
                            fill: true,
                            lineTension: 0.3,
                            pointHoverRadius: 5,
                            pointHitRadius: 10,
                            borderWidth: 1
                        }
                    ]
                });

                ch.build();

                helper.onResizeEnd(function () {

                    if ( $instance.hasClass('stec-media-small') ) {
                        ch.chart.options.legend.display = false;
                    } else {
                        ch.chart.options.legend.display = true;
                    }

                    ch.chart.update();

                }, 50, 'stec-unbind-window-resize-on-event-close-' + glob.options.id);

            }, 0);

        };

        function chart() {

            this.ctx,
                    this.chartData,
                    this.chart,
                    this.setCanvas = function ($canvas) {

                        var canvas = $canvas.get(0);

                        var w = parseInt($inner.find('.stec-layout-event-inner-forecast-details-chart').width(), 10);
                        var h = parseInt($inner.find('.stec-layout-event-inner-forecast-details-chart').height(), 10);

                        canvas.width = w;
                        canvas.height = h;

                        this.ctx = $canvas.get(0).getContext("2d");

                    },
                    this.setChartData = function (chartData) {
                        this.chartData = chartData;
                    },
                    this.destroy = function () {
                        this.chart.destroy();
                    },
                    this.build = function () {

                        var parent = this;

                        if ( this.chart ) {
                            this.destroy();
                        }

                        var generalTextColor = $inner.find('.stec-layout-event-inner-forecast-details-left-forecast-top p').css('color');
                        var generalFontFamily = $inner.find('.stec-layout-event-inner-forecast-details-left-forecast-top p').css('font-family');
                        var displayLegend = true;

                        if ( $instance.hasClass('stec-media-small') ) {
                            displayLegend = false;
                        }

                        this.chart = window.Chart(this.ctx, {
                            type: 'line',
                            data: parent.chartData,
                            options: {
                                maintainAspectRatio: false,
                                responsive: true,
                                defaultFontFamily: generalFontFamily,
                                defaultFontColor: generalTextColor,
                                legend: {
                                    display: displayLegend,
                                    labels: {
                                        fontFamily: generalFontFamily,
                                        fontColor: generalTextColor,
                                        fontSize: 12
                                    }
                                },
                                scales: {
                                    xAxes: [{
                                            ticks: {
                                                fontFamily: generalFontFamily,
                                                fontSize: 11,
                                                fontColor: generalTextColor
                                            },
                                            gridLines: {
                                                color: 'rgba(0,0,0,0.1)',
                                                zeroLineColor: 'rgba(0,0,0,0)'
                                            }
                                        }],
                                    yAxes: [{
                                            ticks: {
                                                fontFamily: generalFontFamily,
                                                fontSize: 11,
                                                fontColor: generalTextColor
                                            },
                                            gridLines: {
                                                color: 'rgba(0,0,0,0.1)',
                                                zeroLineColor: 'rgba(0,0,0,0)'
                                            }
                                        }]
                                },
                                tooltips: {
                                    titleFontColor: '#fff',
                                    titleFontStyle: generalFontFamily,
                                    bodyFontFamily: generalFontFamily,
                                    bodyFontColor: '#fff'
                                }

                            }
                        });

                    };

        }

        function getWindDir(bearing) {

            while ( bearing < 0 )
                bearing += 360;
            while ( bearing >= 360 )
                bearing -= 360;
            var val = Math.round((bearing - 11.25) / 22.5);
            var arr = [
                window.stecLang.N,
                window.stecLang.NNE,
                window.stecLang.NE,
                window.stecLang.ENE,
                window.stecLang.E,
                window.stecLang.ESE,
                window.stecLang.SE,
                window.stecLang.SSE,
                window.stecLang.S,
                window.stecLang.SSW,
                window.stecLang.SW,
                window.stecLang.WSW,
                window.stecLang.W,
                window.stecLang.WNW,
                window.stecLang.NW,
                window.stecLang.NNW
            ];
            return arr[ Math.abs(val) ];

        }

        var fillError = function () {
            $inner.find('.stec-layout-event-inner-forecast-content').remove();
            $inner.find('.errorna').show();
        };

        function iconToText(icon) {

            switch ( icon ) {

                case ('clear-day') :
                case ('clear-night') :
                    return window.stecLang.clear_sky;
                    break;

                case ('partly-cloudy-day') :
                case ('partly-cloudy-night') :
                    return window.stecLang.partly_cloudy;
                    break;

                case ('cloudy') :
                    return window.stecLang.cloudy;
                    break;

                case ('fog') :
                    return window.stecLang.fog;
                    break;

                case ('rain') :
                    return window.stecLang.rain;
                    break;

                case ('sleet') :
                    return window.stecLang.sleet;
                    break;

                case ('snow') :
                    return window.stecLang.snow;
                    break;

                case ('wind') :
                    return window.stecLang.wind;
                    break;

            }
        }

        function iconToiconHTML(icon, forceday) {

//          clear-day, clear-night, rain, snow, sleet, wind, fog, cloudy, partly-cloudy-day, or partly-cloudy-night

            switch ( icon ) {

                case ('clear-day') :
                    return '<div class="stec-forecast-icon-clear-day"></div>';
                    break;

                case ('clear-night') :
                    return forceday ? '<div class="stec-forecast-icon-clear-day"></div>' : '<div class="stec-forecast-icon-clear-night"></div>';
                    break;

                case ('partly-cloudy-day') :
                    return '<div class="stec-forecast-icon-cloudy-day"></div>';
                    break;

                case ('partly-cloudy-night') :
                    return forceday ? '<div class="stec-forecast-icon-cloudy-day"></div>' : '<div class="stec-forecast-icon-cloudy-night"></div>';
                    break;

                case ('cloudy') :
                    return '<div class="stec-forecast-icon-cloudy"></div>';
                    break;

                case ('fog') :
                    return '<div class="stec-forecast-icon-mist"></div>';
                    break;

                case ('rain') :
                    return  '<div class="stec-forecast-icon-rain"></div>';
                    break;

                case ('sleet') :
                    return '<div class="stec-forecast-icon-sleet"></div>';
                    break;

                case ('snow') :
                    return '<div class="stec-forecast-icon-snow"></div>';
                    break;

                case ('wind') :
                    return '<div class="stec-forecast-icon-cloudy"></div>';
                    break;

            }
        }

        var isLoaded = false;

        $(window).on('stec-tab-click-' + glob.options.id, function () {
            if ( !$(this).hasClass('active') && isLoaded === false ) {
                getWeather();
                isLoaded = true;
            }
        });

        if ( $inner.hasClass('active') ) {
            getWeather();
            isLoaded = true;
        }

    });

})(jQuery);