<?php

if ( !function_exists('knowhere_page_title_meta_html') ) {

	function knowhere_page_title_meta_html( $page_title, $meta_box ) {

		$meta_box_value = $name = $title = $desc = $type = $default = $options = $required_field = $el_class = '';

		extract(wp_parse_args($meta_box, array(
			"name" => '',
			"title" => '',
			"desc" => '',
			"type" => '',
			"default" => '',
			"required" => '',
			"options" => ''
		)));

		if ( isset($page_title[$name]) && !empty($page_title[$name]) ) {
			$meta_box_value = $page_title[$name];
		}

		if ( $meta_box_value == "" ) {
			$meta_box_value = $default;
		}

		if ( isset($required) && is_array($required) ) {
			ob_start(); $el_class = 'knowhere-required-section'; ?>
			<input type="hidden" value="<?php echo esc_attr($required[0]) ?>:<?php echo esc_attr(esc_attr($required[1])) ?>" class="knowhere-type-field-required" />
			<?php $required_field = ob_get_clean();
		}

		?>

		<div id="prefix_<?php echo esc_attr($name) ?>" class="option-type option-type-<?php echo sanitize_html_class($type) ?> <?php echo sanitize_html_class($el_class) ?>">

			<?php echo $required_field; ?>

			<?php if ( $type == 'text' ): ?>

				<h2><?php echo esc_html($title) ?></h2>

				<div class="option-element">
					<input value="<?php echo esc_attr($meta_box_value) ?>" type="text" class="styler" name="knowhere_page_title[<?php echo esc_attr($name) ?>]" id="<?php echo esc_attr($name) ?>" />
				</div>

				<div class="option-description">
					<?php if ( $desc ): ?>
						<?php echo esc_html($desc); ?>
					<?php endif; ?>
				</div>

			<?php endif; ?>

			<?php if ( $type == 'select' ): ?>

				<h2><?php echo esc_html($title) ?></h2>

				<div class="option-element">

					<select name="knowhere_page_title[<?php echo esc_attr($name) ?>]" id="<?php echo esc_attr($name) ?>" class="strip_custom_select">

						<?php if ( $options == 'page' ): ?>

							<option value=""><?php esc_html_e('None', 'knowherepro'); ?></option>

							<?php $options = get_pages('title_li=&orderby=name');

							if ( is_array($options) ) :
								foreach ( $options as $key => $entry ) :
									$std = $entry->ID;
									$title = $entry->post_title;
									?>
									<option value="<?php echo esc_attr($std) ?>"<?php echo $meta_box_value == $std ? ' selected="selected"' : '' ?>><?php echo esc_html($title) ?></option>
								<?php endforeach;
							endif; ?>

						<?php else: ?>

							<?php if ( is_array($options) ) :
								foreach ($options as $key => $value) : ?>
									<option value="<?php echo esc_attr($key) ?>"<?php echo $meta_box_value == $key ? ' selected="selected"' : '' ?>><?php echo esc_html($value) ?></option>
								<?php endforeach;
							endif; ?>
						<?php endif; ?>

					</select>

				</div>

				<div class="option-description">
					<?php if ( $desc ): ?>
						<?php echo esc_html($desc); ?>
					<?php endif; ?>
				</div>

			<?php endif; ?>

			<?php if ( $type == 'upload' ): ?>

				<h2><?php echo esc_html($title) ?></h2>

				<div class="option-element">
					<input class="uploader-upload-input" value="<?php echo esc_attr($meta_box_value) ?>" type="text" name="knowhere_page_title[<?php echo esc_attr($name) ?>]" id="<?php echo esc_attr($name) ?>" size="50%" />
					<div class="img-container">
						<div class="img-preview">
							<?php if ( strpos($meta_box_value, 'placeholder.png') ): ?>
								<img src="<?php echo esc_url($meta_box_value); ?>" alt="">
							<?php else: ?>
								<img src="<?php echo esc_url(Knowhere_Helper::get_post_attachment_image( $meta_box_value, '100*100', true )); ?>" alt="">
							<?php endif; ?>
						</div>
					</div>
					<div class="group-buttons">
						<button data-type="image" class="button button-secondary add_image_available_media"><?php esc_html_e('Upload Image', 'knowherepro') ?></button>
						<button class="button button-secondary remove_image_available_media"><?php esc_html_e('Remove Image', 'knowherepro') ?></button>
					</div>
				</div>

				<div class="option-description">
					<?php if ( $desc ): ?>
						<?php echo esc_html($desc); ?>
					<?php endif; ?>
				</div>

			<?php endif; ?>

			<?php if ( $type == 'color' ): ?>

				<h2><?php echo esc_html($title) ?></h2>

				<div class="option-element">
					<input class="knowhere-page-title-color-picker" value="<?php echo esc_attr($meta_box_value) ?>" type="text" name="knowhere_page_title[<?php echo esc_attr($name) ?>]" id="<?php echo esc_attr($name) ?>" />
				</div>

				<div class="option-description">
					<?php if ( $desc ): ?>
						<?php echo esc_html($desc); ?>
					<?php endif; ?>
				</div>

			<?php endif; ?>

			<?php if ( $type == 'checkbox' ):

				if ( $meta_box_value == $name ) { $checked = 'checked = "checked"'; } else { $checked = ''; } ?>

				<div class="option-element">
					<input type="checkbox" id="<?php echo esc_attr($name) ?>" name="knowhere_page_title[<?php echo esc_attr($name) ?>]" value="<?php echo esc_attr($name) ?>" <?php echo esc_attr($checked) ?> />
					<label for="<?php echo esc_attr($name) ?>"><?php echo esc_html($title) ?></label>
				</div>

				<div class="option-description">
					<?php if ( $desc ): ?>
						<?php echo esc_html($desc); ?>
					<?php endif; ?>
				</div>

			<?php endif; ?>

		</div><!--/ .option-type-->
		<?php
	}

}

if (!function_exists('knowhere_page_title_get_value')) {

	function knowhere_page_title_get_value( $meta_key = false ) {
		$value = false;
		$page_title = get_post_meta( knowhere_post_id(), 'knowhere_page_title', true );

		if ( $meta_key ) {
			if ( isset($page_title[$meta_key]) ) {
				$value = $page_title[$meta_key];
			}
		}

		return $value;
	}

}