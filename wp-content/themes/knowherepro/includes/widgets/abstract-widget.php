<?php
/**
 * Abstract MAD Class
 *
 * @author   Velikorodnov
 * @category Widgets
 * @package  widgets/
 * @version  1.9
 * @extends  WP_Widget
 */
abstract class Knowhere_Widget extends WP_Widget {

	/**
	 * CSS class
	 *
	 * @var string
	 */
	public $widget_cssclass;

	/**
	 * Widget description
	 *
	 * @var string
	 */
	public $widget_description;

	/**
	 * Widget ID
	 *
	 * @var string
	 */
	public $widget_id;

	/**
	 * Widget name
	 *
	 * @var string
	 */
	public $widget_name;

	/**
	 * Settings
	 *
	 * @var array
	 */
	public $settings;

	/**
	 * Constructor
	 */
	public function __construct() {

		$widget_ops = array(
			'classname'   => $this->widget_cssclass,
			'description' => $this->widget_description
		);

		if ( ! $this->widget_id ) {
			$this->widget_id = null;
		}

		parent::__construct($this->widget_id, $this->widget_name, $widget_ops);
	}

	/**
	 * Output the html at the start of a widget
	 *
	 * @param  array $args
	 * @return string
	 */
	public function widget_start( $args, $instance ) {
		echo $args['before_widget'];

		if ( isset($instance['color_title']) && !empty($instance['color_title']) ) {
			$args['before_title'] = '<h4 class="kw-widget-title" style="color: '. $instance['color_title'] .'">';
			$args['after_title'] = '</h4>';
		}

		if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
	}

	/**
	 * Output the html at the end of a widget
	 *
	 * @param  array $args
	 * @return string
	 */
	public function widget_end( $args ) {
		echo $args['after_widget'];
	}

	/**
	 * update function.
	 *
	 * @see WP_Widget->update
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		if ( ! $this->settings ) {
			return $instance;
		}

		foreach ( $this->settings as $key => $setting ) {
			if ( isset( $new_instance[ $key ] ) ) {
				$instance[ $key ] = sanitize_text_field( $new_instance[ $key ] );
			} elseif ( 'checkbox' === $setting['type'] ) {
				$instance[ $key ] = 0;
			}
		}

		return $instance;
	}

	/**
	 * form function.
	 *
	 * @see WP_Widget->form
	 * @param array $instance
	 */
	public function form( $instance ) {

		if ( ! $this->settings ) { return; }

		foreach ( $this->settings as $key => $setting ) {

			$value = isset( $instance[ $key ] ) ? $instance[ $key ] : $setting['std'];

			switch ( $setting['type'] ) {

				case 'text' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
				break;

				case 'number' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="number" step="<?php echo esc_attr( $setting['step'] ); ?>" min="<?php echo esc_attr( $setting['min'] ); ?>" max="<?php echo esc_attr( $setting['max'] ); ?>" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
				break;

				case 'select' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>">
							<?php foreach ( $setting['options'] as $option_key => $option_value ) : ?>
								<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, $value ); ?>><?php echo esc_html( $option_value ); ?></option>
							<?php endforeach; ?>
						</select>
						<?php if ( isset($setting['desc']) && !empty($setting['desc']) ): ?>
							<small><?php echo esc_html($setting['desc']) ?></small>
						<?php endif; ?>
					</p>
					<?php
				break;

				case 'checkbox' :
					?>
					<p>
						<input id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" type="checkbox" value="1" <?php checked( $value, 1 ); ?> />
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
					</p>
					<?php
				break;

				case 'colorpicker' :
					?>
					<p>
						<script type="text/javascript">
							jQuery(document).ready(function($) {
								$('#<?php echo $this->get_field_id( $key ); ?>').wpColorPicker();
							});
						</script>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label><br>
						<input id="<?php echo $this->get_field_id( $key ); ?>" type="text" name="<?php echo $this->get_field_name( $key ); ?>" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
				break;

				case 'textarea' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id($key); ?>" name="<?php echo $this->get_field_name($key); ?>">
							<?php echo esc_textarea( $value ); ?>
						</textarea>
					</p>
					<?php
					break;
			}
		}
	}
}
