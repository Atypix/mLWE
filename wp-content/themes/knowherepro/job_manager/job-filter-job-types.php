
<?php

//$selected_job_type = '';
//$search_types = isset( $_REQUEST['filter_job_type'] ) ? $_REQUEST['filter_job_type'] : '';
//
//if ( ! empty( $search_types ) && is_array( $search_types ) ) {
//	$search_types = $search_types[0];
//}
//
//$search_types = sanitize_text_field( stripslashes( $search_types ) );

?>

<?php
/**
 * Filter in `[jobs]` shortcode for job types.
 *
 * This template can be overridden by copying it to yourtheme/job_manager/job-filter-job-types.php.
 *
 * @see         https://wpjobmanager.com/document/template-overrides/
 * @author      Automattic
 * @package     WP Job Manager
 * @category    Template
 * @version     1.20.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<?php if ( get_option( 'job_manager_enable_types' ) ): ?>
	<?php if ( ! is_tax( 'job_listing_type' ) && empty( $job_types ) ) : ?>
		<div class="kw-job-types">
			<label><?php echo esc_html__('Types', 'knowherepro') ?></label>
			<ul class="job_types">
				<?php foreach ( get_job_listing_types() as $type ) : ?>
					<li><label for="job_type_<?php echo $type->slug; ?>" class="<?php echo sanitize_title( $type->name ); ?>">
							<input type="checkbox" name="filter_job_type[]" value="<?php echo $type->slug; ?>" <?php checked( in_array( $type->slug, $selected_job_types ), true ); ?> id="job_type_<?php echo $type->slug; ?>" /> <?php echo $type->name; ?></label>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<input type="hidden" name="filter_job_type[]" value="" />
	<?php elseif ( $job_types ) : ?>
		<?php foreach ( $job_types as $job_type ) : ?>
			<input type="hidden" name="filter_job_type[]" value="<?php echo sanitize_title( $job_type ); ?>" />
		<?php endforeach; ?>
	<?php endif; ?>
<?php endif; ?>





