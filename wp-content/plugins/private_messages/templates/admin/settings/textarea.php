<textarea id="<?php echo esc_attr( $id ); ?>" class="large-text" rows="10" name="<?php echo esc_attr( $name_input ); ?>"><?php echo pm_get_option( $option_name, $default ); ?></textarea>
<?php if ( $description ) : ?>
	<p class="description"><?php echo wp_kses_post( $description ); ?></p>
<?php endif; ?>
