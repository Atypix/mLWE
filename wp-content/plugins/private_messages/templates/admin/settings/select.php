<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name_input ); ?>">
	<?php foreach ( $options as $val => $option ) : ?>
		<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $val, pm_get_option( $option_name, $default ) );?>><?php echo esc_html( $option ); ?></option>
	<?php endforeach; ?>
</select>
<p class="description"><?php echo wp_kses_post( $description ); ?></p>
