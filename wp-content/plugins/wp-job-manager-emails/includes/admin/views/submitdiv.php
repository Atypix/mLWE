<?php
// Exit if accessed directly
	if( ! defined( 'ABSPATH' ) ) exit;
	global $post;

	$post_status = in_array( get_post_status( $post->ID ), array('auto-draft', 'draft', 'publish', 'enabled') ) ? 'publish' : 'disabled';
	$button_status = $post_status === 'publish' ? 'email_enabled' : 'email_disabled';

	$post_status_visible = $post_status === 'disabled' ? __( 'Email is Disabled', 'wp-job-manager-emails' ) : __( 'Email is Enabled', 'wp-job-manager-emails' );
	$post_status_hidden = $post_status === 'disabled' ? __( 'Enable this Email', 'wp-job-manager-emails' ) : __( 'Disable this Email', 'wp-job-manager-emails' );
	$is_new_email = empty( $post->post_status ) || $post->post_status === 'draft' || $post->post_status === 'auto-draft';
?>
<div class="submitbox" id="submitpost">
    <div id="minor-publishing">

		<div id="minor-publishing-actions">
			<div id="toggle_email_status" class="ui animated fade button fluid <?php echo $button_status; ?>" tabindex="0" data-enable="<?php _e( 'Enable this Email', 'wp-job-manager-emails' ); ?>" data-isenabled="<?php _e( 'Email is Enabled', 'wp-job-manager-emails' ); ?>" data-disable="<?php _e( 'Disable this Email', 'wp-job-manager-emails' ); ?>" data-isdisabled="<?php _e( 'Email is Disabled', 'wp-job-manager-emails' ); ?>">
			  <div id="toggle_email_status_visible" class="visible content"><?php echo $post_status_visible; ?></div>
			  <div id="toggle_email_status_hidden" class="hidden content">
				<?php echo $post_status_hidden; ?>
			  </div>
			</div>
		</div>
        <div id="misc-publishing-actions">

			<input name="hidden_post_visibility" id="hidden-post-visibility" value="public" type="hidden">

			<div style="display:none;">
				<?php submit_button( esc_html__( 'Save', 'wp-job-manager-emails' ), 'button', 'save' ); ?>
			</div>

			<input type="hidden" name="post_status" id="hidden_post_status" value="<?php echo $post_status; ?>"/>
			<?php
			/**
			 * Published on
			 */
			$datef = __( 'M j, Y @ H:i', 'wp-job-manager-emails' );
			if ( 0 != $post->ID ) {
				if ( 'publish' == $post->post_status ) {
					$stamp = __('Last Updated: <b>%1$s</b>', 'wp-job-manager-emails');
				}
				$date = date_i18n( $datef, strtotime( $post->post_date ) );
			} else { // draft (no saves, and thus no date specified)
				$stamp = __('Publish <b>immediately</b>', 'wp-job-manager-emails');
				$date = date_i18n( $datef, strtotime( current_time('mysql') ) );
			}

			/**
			 * Send Test Email
			 */
			if ( empty( $is_new_email ) ) : ?>
			<div class="misc-pub-section">
				<a href="#" class="ui fluid label" id="send_test_email" style="text-align: center;">
					<i class="mail icon"></i> <?php _e('Send Test Email', 'wp-job-manager-emails'); ?>
				</a>
			</div>
			<div id="send_test_email_modal" class="ui basic modal">
				<div class="ui large blue header center aligned">
					<?php _e('Send Test Email', 'wp-job-manager-emails'); ?>
				</div>
				<div class="image content">
					<div class="image">
						<i class="violet mail icon"></i>
					</div>
					<div class="description">
						<p><?php _e('To send a test email, please follow the steps exactly as a normal user would on the frontend of your site.', 'wp-job-manager-emails'); ?></p>
						<p><?php _e('This will help you confirm that all actions/hooks are working correctly, and that you have your site setup correctly to send the emails you wish to send.', 'wp-job-manager-emails'); ?></p>
						<p><?php _e('You can install a plugin call "WP Mail Logging" to log outgoing email messages in the WordPress interface.', 'wp-job-manager-emails'); ?></p>
						<p><?php _e('In an upcoming release I will add the feature to send a preview email, but you should still always follow the steps on frontend of your site, to make 100% sure your emails send correctly.', 'wp-job-manager-emails'); ?></p>
					</div>
				</div>
				<div class="actions">
					<div class="ui ok green basic inverted button">
						<i class="checkmark icon"></i>
						<?php _e('OK', 'wp-job-manager-emails'); ?>
					</div>
				</div>
			</div>
			<?php endif;

			/**
			 * Revisions
			 */
			if ( ! empty( $args['publish']['revisions_count'] ) ) : ?>
			<div class="misc-pub-section misc-pub-revisions">
				<?php printf( __( 'Revisions: %s', 'wp-job-manager-emails' ), '<b>' . number_format_i18n( $args['publish']['revisions_count'] ) . '</b>' ); ?>
				<a class="hide-if-no-js" href="<?php echo esc_url( get_edit_post_link( $args['publish']['revision_id'] ) ); ?>"><span aria-hidden="true"><?php _ex( 'Browse', 'revisions', 'wp-job-manager-emails' ); ?></span> <span class="screen-reader-text"><?php _e( 'Browse revisions', 'wp-job-manager-emails' ); ?></span></a>
			</div>
			<?php endif;

			if ( ! empty( $date ) && ! empty( $stamp ) ) : ?>
			<div class="misc-pub-section curtime misc-pub-curtime">
				<span id="timestamp"><?php printf($stamp, $date); ?></span>
			</div><?php // /misc-pub-section ?>
			<?php endif; ?>

        </div>
        <div class="clear"></div>
    </div>

    <div id="major-publishing-actions">
		<div id="delete-action">
			<?php
			if ( ! EMPTY_TRASH_DAYS ) {
				$delete_text = esc_html__( 'Delete Permanently', 'wp-job-manager-emails' );
			} else {
				$delete_text = esc_html__( 'Move to Trash', 'wp-job-manager-emails' );
			}
			?>
			<a class="submitdelete deletion" href="<?php echo esc_url( get_delete_post_link( $post->ID ) ); ?>"><?php echo esc_html( $delete_text ); ?></a>
		</div>
		<div id="publishing-action">
			<span class="spinner"></span>

			<?php if ( $is_new_email ) : ?>
				<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Publish', 'wp-job-manager-emails') ?>" />
				<?php submit_button( __( 'Add New Email', 'wp-job-manager-emails' ), 'primary large', 'publish', FALSE ); ?>
			<?php else : ?>
				<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Update', 'wp-job-manager-emails' ) ?>"/>
				<input name="save" type="submit" class="button button-primary button-large" id="publish" value="<?php esc_attr_e( 'Update Email', 'wp-job-manager-emails' ) ?>"/>
			<?php endif; ?>

		</div>
        <div class="clear"></div>
    </div>
</div>