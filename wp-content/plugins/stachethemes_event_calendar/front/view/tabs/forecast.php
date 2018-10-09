<p class="errorna stec-layout-event-title-fontandcolor"><?php _e('Weather data is currently not available for this location', 'stec'); ?></p>

<div class="stec-layout-event-inner-forecast-content">

    <div class="stec-layout-event-inner-forecast-top">

        <p class="stec-layout-event-inner-forecast-top-title"><?php _e('Weather Report', 'stec'); ?></p>
        <p class="stec-layout-event-inner-forecast-top-date"><?php _e('Today', 'stec'); ?> stec_replace_today_date</p>

    </div>

    <div class="stec-layout-event-inner-forecast-today">

        <div class="stec-layout-event-inner-forecast-today-left">

            <div class="stec-layout-event-inner-forecast-today-left-icon">
                stec_replace_today_icon_div
            </div>

            <div>
                <p class="stec-layout-event-inner-forecast-today-left-current-text">stec_replace_current_summary_text</p>
                <p class="stec-layout-event-inner-forecast-today-left-current-temp">stec_replace_current_temp &deg;stec_replace_current_temp_units</p>
            </div>

        </div>

        <div class="stec-layout-event-inner-forecast-today-right">
            <p class=""><?php _e('Wind', 'stec'); ?> <span>stec_replace_current_wind stec_replace_current_wind_units stec_replace_current_wind_direction</span></p>
            <p class=""><?php _e('Humidity', 'stec'); ?> <span>stec_replace_current_humidity %</span></p>
            <p class=""><?php _e('Feels like', 'stec'); ?> <span>stec_replace_current_feels_like &deg;stec_replace_current_temp_units</span></p>
        </div>

    </div>

    <div class="stec-layout-event-inner-forecast-details">

        <div class="stec-layout-event-inner-forecast-details-left">
            <p class=""><?php _e('Forecast', 'stec'); ?></p>

            <div class="stec-layout-event-inner-forecast-details-left-forecast">
                <div class="stec-layout-event-inner-forecast-details-left-forecast-top">
                    <p><?php _e('Date', 'stec'); ?></p>
                    <p><?php _e('Weather', 'stec'); ?></p>
                    <p><?php _e('Temp', 'stec'); ?></p>
                </div>

                <div class="stec-layout-event-inner-forecast-details-left-forecast-day stec-layout-event-inner-forecast-details-left-forecast-day-template">
                    <p>stec_replace_date</p>
                    stec_replace_icon_div
                    <p>stec_replace_min / stec_replace_max &deg;stec_replace_temp_units</p>
                </div>

                stec_replace_5days
            </div>

        </div>

        <div class="stec-layout-event-inner-forecast-details-right">
            <p class=""><?php _e('Next 24 Hours', 'stec'); ?></p>

            <div class="stec-layout-event-inner-forecast-details-chart">
                <canvas></canvas>
            </div>
        </div>
    </div>

    <p class="stec-layout-event-inner-forecast-credits"><?php _e('Powered by', 'stec'); ?> Forecast.io</p>

</div>