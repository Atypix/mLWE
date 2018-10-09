<?php
/**
 * Job Submission Form
 */
if ( ! defined( 'ABSPATH' ) ) exit;

global $job_manager;


?>
<form action="<?php echo esc_url( $action ); ?>" method="post" id="submit-job-form" class="job-manager-form" enctype="multipart/form-data">

	<?php do_action( 'submit_job_form_start' ); ?>

	<?php if ( apply_filters( 'submit_job_form_show_signin', true ) ) : ?>

		<div class="job-fields-section">
			<?php get_job_manager_template( 'account-signin.php' ); ?>
		</div>

	<?php endif; ?>

	<?php if ( job_manager_user_can_post_job() || job_manager_user_can_edit_job( $job_id ) ) : ?>

		<!-- Job Information Fields -->
		<?php do_action( 'submit_job_form_job_fields_start' ); ?>

			<div class="job-fields-section">

				<h3 class="job-fields-title"><?php esc_html_e('General Information', 'knowherepro') ?></h3>

				<?php foreach ( $job_fields as $key => $field ) : ?>

					<?php $cat_ids = array(); ?>

					<?php if ( function_exists('kw_attribute_taxonomy_category_by_name') && isset($field['taxonomy']) && !empty($field['taxonomy']) ): ?>
						<?php $cat_ids = kw_attribute_taxonomy_category_by_name( $field['taxonomy'] ); ?>
					<?php endif; ?>

					<fieldset data-cat-ids='<?php echo implode(',', $cat_ids) ?>' class="fieldset-<?php echo esc_attr( $key ); ?> <?php if ( isset($field['css_class']) && $field['css_class'] ): ?><?php echo sanitize_html_class($field['css_class']) ?><?php endif; ?>">
						<label for="<?php echo esc_attr( $key ); ?>">
							<?php
							if ( $field['label'] == "__( 'Listing Region', 'wp-job-manager-locations' )") {
								echo "Sélectionnez une région";
							} else if ( $field['label'] == "Listing tags") {
								echo "Période de l'activité";
							} else if ( isset( $field['label'] ) ) {
								echo esc_html($field['label']);
							}
							echo apply_filters( 'submit_job_form_required_label', (isset($field['required']) && $field['required']) ? '' : ' <small>' . esc_html__( '(optional)', 'knowherepro' ) . '</small>', $field ); ?>
						</label>

						<div class="field <?php echo ( isset($field['required']) && $field['required'] ) ? 'required-field' : ''; ?>">
							<?php get_job_manager_template( 'form-fields/' . $field['type'] . '-field.php', array( 'key' => $key, 'field' => $field ) ); ?>
						</div>
					</fieldset>

				<?php endforeach; ?>

			</div>

		<?php do_action( 'submit_job_form_job_fields_end' ); ?>

		<!-- Company Information Fields -->
		<?php if ( $company_fields ) : ?>

			<?php do_action( 'submit_job_form_company_fields_start' ); ?>

				<div class="job-fields-section">

					<h3 class="job-fields-title"><?php esc_html_e('Details', 'knowherepro') ?></h3>

					<?php foreach ( $company_fields as $key => $field ) : ?>

						<fieldset class="fieldset-<?php echo esc_attr( $key ); ?> <?php if ( isset($field['css_class']) && $field['css_class'] ): ?><?php echo sanitize_html_class($field['css_class']) ?><?php endif; ?>">
							<label for="<?php echo esc_attr( $key ); ?>">
								<?php
								if ( isset( $field['label'] ) ) {
									echo esc_html($field['label']);
								}
								echo apply_filters( 'submit_job_form_required_label', (isset($field['required']) && $field['required']) ? '' : ' <small>' . esc_html__( '(optional)', 'knowherepro' ) . '</small>', $field ); ?>
							</label>
							<div class="field <?php echo ( isset($field['required']) && $field['required'] ) ? 'required-field' : ''; ?>">
								<?php
								if ( isset( $field['type'] ) ) {
									get_job_manager_template( 'form-fields/' . $field['type'] . '-field.php', array( 'key' => $key, 'field' => $field ) );
								} ?>
							</div>
						</fieldset>
					<?php endforeach; ?>

				</div>

			<?php do_action( 'submit_job_form_company_fields_end' ); ?>

		<?php endif; ?>

		<?php do_action( 'submit_job_form_end' ); ?>

		<p>
			<input type="hidden" name="job_manager_form" value="<?php echo esc_attr($form); ?>" />
			<input type="hidden" name="job_id" value="<?php echo esc_attr( $job_id ); ?>" />
			<input type="hidden" name="step" value="<?php echo esc_attr( $step ); ?>" />
			<input type="submit" name="submit_job" class="button" value="<?php echo esc_attr( $submit_button_text ); ?>" />
		</p>

	<?php else : ?>

		<?php do_action( 'submit_job_form_disabled' ); ?>

	<?php endif; ?>
</form>
