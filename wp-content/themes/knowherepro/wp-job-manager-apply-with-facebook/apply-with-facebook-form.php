<?php
/**
 * Form to display after retrieving content from facebook.
 *
 * This template can be overridden by copying it to yourtheme/wp-job-manager-apply-with-facebook/apply-with-facebook-form.php.
 *
 * @see         https://wpjobmanager.com/document/template-overrides/
 * @author      Automattic
 * @package     WP Job Manager - Apply with Facebook
 * @category    Template
 * @version     1.0.3
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form id="wp-job-manager-application-details" method="post" class="apply-with-facebook-details wp-job-manager-application-details" style="display:none;">
	<div id="fb-root"></div>
	<div class="apply-with-facebook-profile">
		<div class="profile-avatar-holder">
			<img src="" />
			<h2 class="profile-name"></h2>
		</div>
		<strong class="profile-bio"></strong>
		<em class="profile-location"></em>
		<dl>
			<dt class="profile-current-positions"><?php _e( 'Current', 'knowherepro' ); ?></dt>
			<dd class="profile-current-positions"><ul></ul></dd>

			<dt class="profile-past-positions"><?php _e( 'Past', 'knowherepro' ); ?></dt>
			<dd class="profile-past-positions"><ul></ul></dd>

			<dt class="profile-educations"><?php _e( 'Education', 'knowherepro' ); ?></dt>
			<dd class="profile-educations"><ul></ul></dd>

			<dt class="profile-email"><?php _e( 'Email', 'knowherepro' ); ?></dt>
			<dd class="profile-email"></dd>

			<?php if ( in_array( $cover_letter, array( 'optional', 'required' ) ) ) : ?>
				<dt class="apply-with-facebook-cover-letter"><label for="apply-with-facebook-cover-letter"><?php _e( 'Cover letter', 'knowherepro' ); ?> <?php if ( 'optional' === $cover_letter ) _e( '(optional)', 'knowherepro' ); ?></label></dt>
				<dd class="apply-with-facebook-cover-letter">
					<textarea name="apply-with-facebook-cover-letter" id="apply-with-facebook-cover-letter" <?php if ( 'required' === $cover_letter ) echo 'required="required"'; ?>><?php echo _x( 'To whom it may concern,', 'default cover letter', 'knowherepro' ); ?>


<?php printf( _x( 'I am very interested in the %s position at %s. I believe my skills and work experience make me an ideal candidate for this role. I look forward to speaking with you soon about this position. Thank you for your consideration.', 'default cover letter', 'knowherepro' ), $job_title, $company_name ); ?>


<?php echo _x( 'Best regards,', 'default cover letter', 'knowherepro' ); ?> </textarea>
				</dd>

			<?php endif; ?>
		</dl>
		<p class="apply-with-facebook-submit">
			<input type="submit" name="apply-with-facebook-submit" value="<?php _e( 'Submit Application', 'knowherepro' ); ?>" /> <?php printf( __( 'Clicking submit will submit your full profile to %s.', 'knowherepro' ), '<strong>' . esc_html( $company_name ) . '</strong>' ); ?>
			<input type="hidden" name="apply-with-facebook-profile-data" id="apply-with-facebook-profile-data" />
			<input type="hidden" name="apply-with-facebook-profile-picture" id="apply-with-facebook-profile-picture" />
			<input type="hidden" name="apply-with-facebook-job-id" value="<?php echo esc_attr( $job_id ); ?>" />
		</p>
	</div>
</form>
