<?php
if ( empty( $field[ 'value' ] ) ) {
	$field[ 'value' ] = get_post_meta( get_the_ID(), 'knowhere_job_details', true );
}

$details = isset( $field['value'] ) ? $field['value'] : array(); ?>


<div id="job_field_meta_custom_tabs" class="kw-select-group-container">

	<div class="kw-info-text"><?php esc_html_e('Please click here to fill in the fields', 'knowherepro') ?></div>

	<i class="fa fa-plus-circle kw-select-group-icon-default"></i>
	<i class="fa fa-minus-circle kw-select-group-icon-active"></i>

	<div class="kw-select-group-inner">

		<a href="javascript:void(0);" class="button button-primary add-job-field-tab kw-btn kw-gray kw-small"><?php esc_html_e('Add Field', 'knowherepro'); ?></a>

		<ul class="job-field-custom-box-holder">

			<?php if ( isset($field[ 'value' ] ) && !empty( $field[ 'value' ] ) && count( $field[ 'value' ] ) > 0 ): ?>

				<?php foreach( $field[ 'value' ] as $id => $field ): ?>

					<?php if ( isset($field['title']) || isset($field['content']) ): ?>

						<li>

							<div class="job-details-handle-area"></div>

							<div class="item">
								<h3><?php esc_html_e('Title', 'knowherepro'); ?></h3>
								<input type="text" name="knowhere_job_details[<?php echo esc_attr($id); ?>][title]" value="<?php echo esc_attr($field['title']); ?>" />
								<p class="desc"><?php esc_html_e('Enter a title (required field)', 'knowherepro'); ?></p>
							</div>

							<div class="item wp-editor">
								<h3><?php esc_html_e('Content', 'knowherepro'); ?></h3>
								<?php wp_editor(
									$field['content'],
									esc_attr($id),
									array(
										'textarea_name' => 'knowhere_job_details['. esc_attr($id) .'][content]',
										'textarea_rows' => 5,
										'tinymce' => true
									)); ?>
							</div>

							<div class="item">
								<a href="javascript:void(0)" class="button button-secondary remove-job-field-tab kw-btn kw-gray kw-small"><?php esc_html_e('Remove', 'knowherepro'); ?></a>
							</div>

						</li>

					<?php endif; ?>

				<?php endforeach; ?>

			<?php endif; ?>

		</ul><!--/ .custom-tabs-->

	</div>

</div>