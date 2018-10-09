<?php
/**
 * Widget: Listing Open Hours
 *
 */
class Knowhere_Sidebar_Listing_Open_Hours extends Knowhere_Widget {

    public function __construct() {
        $this->widget_description = esc_html__( 'Display the open hours of the listing.', 'knowherepro' );
        $this->widget_cssclass 	  = 'widget_listing_open_hours';
        $this->widget_id          = 'knowhere_listing_open_hours';
        $this->widget_name        = '&#x27A4; ' . esc_html__( 'Listing', 'knowherepro' ) . '  - ' . esc_html__( 'Open Hours', 'knowherepro' );
        $this->settings           = array(
            'title' => array(
                'type'  => 'text',
                'std'   => esc_html__('Open Hours', 'knowherepro'),
                'label' => esc_html__( 'Title:', 'knowherepro' )
            )
        );

        parent::__construct();
    }

    function widget( $args, $instance ) {

		extract( $args );

		$listing = knowhere_get_listing( get_post() );
		$job_hours = $listing->get_business_hours();
		if ( ! $job_hours ) {
			return;
		}

		$title = apply_filters( 'widget_title', $instance[ 'title' ], $instance, $this->id_base );

		global $wp_locale;

		// Loop all job hours and remove empty hours.
		foreach ( $job_hours as $day => $hours ) {
			foreach ( $hours as $index => $hour ) {
				if ( ! $hour['start'] || ! $hour['end'] ) {
					unset( $job_hours[ $day ][ $index ] );
				}
			}
		}
		// Remove empty days.
		foreach ( $job_hours as $day => $hours ) {
			if ( ! $hours ) {
				unset( $job_hours[ $day ] );
			}
		}

		if ( empty( $job_hours ) ) { return; }

		$days = knowhere_get_days();
		$format = get_option( 'time_format' );
		$current_day = $listing->get_current_day();
		$current_time = $listing->get_current_time($format);
		$time = DateTime::createFromFormat( $format, $current_time );

		if ( $listing->is_open() ) {
			$text = __( 'Open Now', 'knowherepro' );
			$class = 'kw-active-day';
		} else {
			$text = __( 'Closed Now', 'knowherepro' );
			$class = 'kw-inactive-day';
		}

        ob_start(); ?>

		<?php echo $before_widget; ?>

		<?php if ( $title ) echo $before_title . $title . $after_title; ?>

			<div class="kw-hours-holder kw-visible">

				<div class="kw-active-status" >

					<div class="kw-hours kw-toggle-status <?php echo esc_attr($class) ?>">

						<span class="kw-day"><?php echo $text ?></span>

						<?php if ( isset($job_hours[$current_day]) ): ?>

							<?php foreach ( $job_hours[$current_day] as $hours ) : ?>

								<?php if ( isset($hours['start'], $hours['end']) && $hours['start'] && $hours['end'] ) : ?>

									<?php
										$open = DateTime::createFromFormat($format, $hours['start']);
										$close = DateTime::createFromFormat($format, $hours['end']);

										if ( $time > $open && $time < $close ) {
											$active_class = 'kw-active-status';
										} else {
											$active_class = 'kw-inactive-status';
										}
									?>

									<span class="kw-hour-time <?php echo esc_attr($active_class) ?>">
										<?php if ( esc_html__( 'Closed', 'knowherepro' ) == $hours['start'] || esc_html__('Closed', 'knowherepro') == $hours['end'] ) : ?>
											<span class="kw-closed"><?php esc_html_e( 'Closed Now', 'knowherepro' ); ?></span>
										<?php else : ?>
											<span class="kw-start"><?php echo esc_html($hours['start']); ?></span> &ndash; <span class="kw-end"><?php echo esc_html($hours['end']); ?></span>
										<?php endif; ?>
									</span>

								<?php endif; ?>

							<?php endforeach; ?>

						<?php endif; ?>

						<a href="javascript:void(0)" class="kw-switch-toggle"></a>

					</div><!--/ .kw-hours-->

				</div><!--/ .kw-active-status-->
					<br/>
				<div class="kw-invisible-hours" style="display:block!important;">

				<?php foreach ( $days as $key => $day ) : ?>

						<?php if ( isset( $job_hours[ $day ] ) && is_array($job_hours[$day]) ) : ?>

							<?php foreach ( $job_hours[$day] as $index => $hour ) : ?>

								<div class="kw-hours">

									<?php if ( 0 === $index ) : ?>
										<span class="kw-day">
											<?php echo $wp_locale->get_weekday( $key ); ?>
										</span>
									<?php endif; ?>

									<span class="kw-hour-time">

										<?php if ( esc_html__( 'Closed', 'knowherepro' ) == $hour['start'] || esc_html__('Closed', 'knowherepro') == $hour['end'] ) : ?>
											<span class="kw-closed"><?php esc_html_e( 'Closed', 'knowherepro' ); ?></span>
										<?php else : ?>
											<span class="kw-start"><?php echo esc_html($hour['start']); ?></span> &ndash; <span class="kw-end"><?php echo esc_html($hour['end']); ?></span>
										<?php endif; ?>

									</span>

								</div><!--/ .kw-hours-->

							<?php endforeach; ?>

						<?php endif; ?>

					<?php endforeach; ?>

				</div><!--/ .kw-invisible-hours-->

			</div><!--/ .kw-hours-holder-->

		<?php echo $after_widget;

		$content = ob_get_clean();

		echo apply_filters( $this->widget_id, $content );
    }

}
