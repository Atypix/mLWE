<form method="post" id="job_preview" action="<?php echo esc_url( $form->get_action() ); ?>">

	<div class="job_listing_preview_title clearfix">
		<input type="submit" name="continue" id="job_preview_submit_button" class="button job-manager-button-submit-listing" value="<?php echo apply_filters( 'submit_job_step_preview_submit_text', __( 'Submit Listing', 'knowherepro' ) ); ?>"/>
		<input type="submit" name="edit_job" class="button job-manager-button-edit-listing" value="<?php esc_html_e( 'Edit listing', 'knowherepro' ); ?>"/>
		<input type="hidden" name="job_id" value="<?php echo esc_attr( $form->get_job_id() ); ?>"/>
		<input type="hidden" name="step" value="<?php echo esc_attr( $form->get_step() ); ?>"/>
		<input type="hidden" name="job_manager_form" value="<?php echo esc_attr($form->form_name); ?>"/>

		<h3><?php esc_html_e( 'Preview', 'knowherepro' ); ?></h3>
	</div>

	<?php get_job_manager_template_part( 'content-single', 'job_listing-preview' ); ?>

	<?php knowhere_output_single_listing_icon($form->get_job_id()); ?>

</form>
