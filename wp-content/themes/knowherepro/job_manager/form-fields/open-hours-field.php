
<?php
global $wp_locale;
wp_enqueue_script( 'timepicker' );
wp_enqueue_style( 'timepicker' );

if ( empty( $field[ 'value' ] ) ) {
	$field[ 'value' ] = get_post_meta( get_the_ID(), '_job_hours', true );
}
$days = knowhere_get_days();
$job_hours = knowhere_sanitize_hours( isset( $field['value'] ) ? $field['value'] : array() );

?>

<div class="kw-select-group-container">

	<div class="kw-info-text"><?php esc_html_e('Please click here to fill in the fields', 'knowherepro') ?></div>

	<i class="fa fa-plus-circle kw-select-group-icon-default"></i>
	<i class="fa fa-minus-circle kw-select-group-icon-active"></i>

	<div class="kw-table-container kw-horizontal kw-select-group-inner">

		<table>
			<thead>
				<tr>
					<th width="40%"><div class="kw-days-of-week kw-cell-content"><?php esc_html_e('Days Of The Week', 'knowherepro') ?></div></th>
					<th align="left"><div class="kw-cell-content"><?php esc_html_e( 'Open Time', 'knowherepro' ); ?></div></th>
					<th align="left"><div class="kw-cell-content"><?php esc_html_e( 'Close Time', 'knowherepro' ); ?></div></th>
				</tr>
			</thead>

			<tbody>

				<?php foreach ( $days as $key => $day ) : ?>
					<tr class="kw-open-hour-row" data-day="<?php echo esc_attr( $day ); ?>">
						<td class="kw-hour-day" data-title="<?php echo esc_html__('Days Of The Week', 'knowherepro') ?>" align="left">
							<div class="kw-cell-content">
								<?php echo esc_html($wp_locale->get_weekday( $key )) ?><a class="kw-add-hours" href="javascript:void(0)">[+]</a></div>
						</td>
						<td class="kw-hour-open" data-title="<?php echo esc_html__('Open Time', 'knowherepro') ?>" align="left" class="kw-open-hour">
							<div class="kw-cell-content">

								<?php if ( isset( $job_hours[$day ] ) && is_array( $job_hours[ $day ] ) ) : ?>
									<?php foreach ( $job_hours[ $day ] as $index => $hours ) :
										$hour = isset( $hours['start'] ) ? $hours['start'] : '';
										?>
										<input type="text" class="timepicker" name="job_hours[<?php echo $day; ?>][<?php echo $index; ?>][start]" value="<?php echo sanitize_text_field( $hour ); ?>" autocomplete="off"/>
									<?php endforeach; ?>
								<?php else : ?>
									<input type="text" class="timepicker" name="job_hours[<?php echo $day; ?>][0][start]" value="" autocomplete="off"/>
								<?php endif; ?>

							</div>
						</td>
						<td class="kw-hour-close" data-title="<?php echo esc_html__('Close Time', 'knowherepro') ?>" align="left" class="kw-open-hour">
							<div class="kw-cell-content">

								<?php if ( isset( $job_hours[ $day ] ) && is_array( $job_hours[ $day ] ) ) : ?>
									<?php foreach ( $job_hours[ $day ] as $index => $hours ) :
										$hour = isset( $hours['end'] ) ? $hours['end'] : '';
										?>
										<input type="text" class="timepicker" name="job_hours[<?php echo $day; ?>][<?php echo $index; ?>][end]" value="<?php echo sanitize_text_field( $hour ); ?>" autocomplete="off"/>
									<?php endforeach; ?>
								<?php else : ?>
									<input type="text" class="timepicker" name="job_hours[<?php echo $day; ?>][0][end]" value="" autocomplete="off"/>
								<?php endif; ?>

							</div>
						</td>
					</tr>
				<?php endforeach; ?>

			</tbody>

		</table>

		<script>
			(function($) {

				function knowhere_time_picker() {
					$( '.timepicker' ).timepicker({
						timeFormat: '<?php echo str_replace( '\\', '\\\\', get_option( 'time_format' ) ); ?>',
						noneOption: {
							label: '<?php esc_html_e( 'Closed', 'knowherepro' ); ?>',
							value: '<?php esc_html_e( 'Closed', 'knowherepro' ); ?>'
						}
					});
				}
				knowhere_time_picker();

				$( '.kw-add-hours' ).click( function(e) {
					e.preventDefault();

					var row = $( this ).parents( '.kw-open-hour-row' );
					var day = row.data( 'day' );
					var open_el = row.find( '.kw-hour-open').children('.kw-cell-content');
					var close_el = row.find( '.kw-hour-close').children('.kw-cell-content');

					open_el.append( '<input type="text" class="timepicker" value="" autocomplete="off"/>' );
					close_el.append( '<input type="text" class="timepicker" value="" autocomplete="off"/>' );

					open_el.find( 'input[type="text"]' ).each( function(i) {
						$( this ).attr( 'name', 'job_hours[' + day + '][' + i + '][start]');
					} );
					close_el.find( 'input[type="text"]' ).each( function(i) {
						$( this ).attr( 'name', 'job_hours[' + day + '][' + i + '][end]');
					} );

					knowhere_time_picker();

				} );
			})(jQuery);
		</script>

	</div>

</div>