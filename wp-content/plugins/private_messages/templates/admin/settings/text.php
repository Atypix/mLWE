<input type="text" class="regular-text" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name_input ); ?>" value="<?php echo esc_attr( pm_get_option( $option_name, $default ) ); ?>" />

<?php if ( $description ) : ?>
	<p class="description"><?php echo wp_kses_post( $description ); ?></p>
<?php endif; ?>
