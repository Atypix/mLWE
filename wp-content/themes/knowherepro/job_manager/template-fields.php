<li>
	<div class="job-details-handle-area"></div>
	<div class="item">
		<h3><?php esc_html_e('Title', 'knowherepro'); ?></h3>
		<input type="text" name="knowhere_job_details[__REPLACE_SSS__][title]" value=""/>
		<p class="desc"><?php esc_html_e('Enter a title (required field)', 'knowherepro'); ?></p>
	</div>
	<div class="item wp-editor">
		<h3><?php esc_html_e('Content', 'knowherepro'); ?></h3>
		<?php wp_editor( '', '__REPLACE_SSS__', array(
			'textarea_name' => 'knowhere_job_details[__REPLACE_SSS__][content]',
			'textarea_rows' => 3,
			'quicktags' => true,
			'tinymce' => false
		) ); ?>
	</div>
	<div class="item">
		<a href="javascript:void(0)" class="button button-secondary remove-job-field-tab kw-btn kw-gray kw-small"><?php esc_html_e('Remove', 'knowherepro'); ?></a>
	</div>
</li>