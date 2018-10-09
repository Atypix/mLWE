<?php
// Exit if accessed directly
	if( ! defined( 'ABSPATH' ) ) exit;
	$actions = $this->cpt()->hooks()->get_full_hooks();
	if( empty($actions) ) return;
	$post_id = ( isset( $this->post ) && is_object( $this->post ) && isset( $this->post->ID ) ) ? $this->post->ID : FALSE;

	// Output JS variable jme_current_hook in script tag to allow jQuery handling to hide/show any specific meta boxes that may be required.
	// Specifically because there are some hooks that support fields from other post types (like Job listing, etc)
	//
	// @see jme_meta_box_check() in assets/core/src/admin.js
	// @see localize() in includes/hooks/class-resume.php
	$hook = $this->get_hook();
	$poststatus_divide = false;
	$cpoststatus_divide = false;
	echo "<script>var jme_current_hook = '{$hook}';</script>";
?>

<div class="ui icon small message attach" style="width: auto;">
	<i class="icon mail outline"></i>
	<div class="content">
		<div class="header">
			<?php _e( 'What should cause this email to be sent?', 'wp-job-manager-emails' ); ?>
		</div>
		<p><?php _e( 'Select the action/filter you want to cause this email to be sent.  Additional details will be shown when selected, if available.', 'wp-job-manager-emails' ); ?></p>
	</div>
</div>

<div class="ui yellow message" id="hook_description_message" style="width: auto; display: none;">
	<div id="hook_description"></div>
</div>

<div class="ui red icon message" id="hook_req_description_message" style="width: auto; display: none;">
	<i class="huge icons">
		<i class="wordpress icon"></i>
		<i class="corner puzzle icon"></i>
	</i>
	<div class="content" id="hook_req_description"></div>
</div>

<div id="hook_dropdown" class="ui selection dropdown labeled fluid fluid_auto small hook_dropdown" style="width: auto;">
	<input type="hidden" id="selected_hook" name="hook" value="<?php $this->the_hook(); ?>">
	<i id="hook_dropdown_icon" class="dropdown icon"></i>
  	<div id="hook_dropdown_text" class="default text">
		<?php _e( 'Select an action/hook ....', 'wp-job-manager-emails' ); ?>
	</div>
	<div class="menu small">
	<div class="ui icon search input">
		<i class="search icon"></i>
		<input type="text" placeholder="<?php _e('Search actions/hooks ...', 'wp-job-manager-emails'); ?>">
	</div>
	<div class="ui center aligned purple header" style="padding: 1em; margin: 0; background-color: #eeeeee; border: 1px solid #dddddd;">
		<i class="wordpress icon"></i><?php _e( 'Core Action Hooks', 'wp-job-manager-emails' ); ?>
	</div>
		<?php foreach( (array) $actions as $action => $config): ?>

			<?php if( ! $poststatus_divide && ! empty( $config['poststatus'] ) ): ?>
				<div class="ui center aligned blue header" style="padding: 1em; margin: 0; background-color: #eeeeee; border: 1px solid #dddddd;">
					<i class="wizard icon"></i><?php _e('Default (Post Status) Action Hooks', 'wp-job-manager-emails'); ?>
				</div>
				<?php
					$poststatus_divide = true;
				endif;
			?>

			<?php if( ! $cpoststatus_divide && ! empty( $config['custom_ps'] ) && ! empty( $config['poststatus'] ) ): ?>
				<div class="ui center aligned teal header" style="padding: 1em; margin: 0; background-color: #eeeeee; border: 1px solid #dddddd;">
					<i class="lab icon"></i><?php _e('Custom Post Status Action Hooks', 'wp-job-manager-emails'); ?>
				</div>
				<?php
					$cpoststatus_divide = true;
				endif;
			?>

			<div class="item small" data-value="<?php echo $action; ?>" data-text="<?php _e('This email will be sent ...', 'wp-job-manager-emails'); ?> <?php echo $config['desc']; ?>">
				<span class="description">
					<?php
						if( $post_id && isset( $config['posts'] ) && ! empty( $config['posts'] ) && in_array( $post_id, $config[ 'posts' ] ) ){
								echo __( '(Current)', 'wp-job-manager-emails' ) . ' ';
						}

						echo $action;
					?>
				</span>
				<span class="small text"><?php echo $config['desc']; ?></span>
			</div>
		<?php endforeach; ?>
	</div>
</div>