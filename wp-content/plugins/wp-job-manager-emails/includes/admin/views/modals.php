<?php if ( ! defined( 'ABSPATH' ) ) exit;
$templates = $this->cpt()->hooks()->get_hooks_with_templates();

?>

<div id="hook_template_modal" class="ui small modal hook_modals">
    <i class="close icon"></i>
    <div class="header" id="hook_template_modal_top">
		<i class="ui icon mail outline"></i>
        <?php _e( 'Email Templates', 'wp-job-manager-emails' ); ?>
    </div>

	<div class="content">
		<div id="htm_tabs" class="ui secondary pointing menu" style="padding-top: 0em;">

			<?php
				foreach( (array) $templates as $section => $scfg ):
						if( ! array_key_exists( 'templates', $scfg ) || empty( $scfg['templates'] ) ) continue;
						$tab_classes = ( $section === 'core' ) ? 'active ' : '';
						$tab_classes .= "htm_{$section}_tab";
					?>
						<a id="<?php echo "{$tab_classes}_link"; ?>" class="<?php echo $tab_classes;?> item htm_tab" data-tab="<?php echo $section; ?>-tab">
							<?php echo $scfg['tab']; ?>
						</a>
			<?php
				endforeach;
			?>
		</div>

		<?php
			foreach( (array) $templates as $tsection => $tscfg ){
				$seg_classes = $tsection === 'core' ? 'active ' : '';
				$seg_classes .= "htm_{$tsection}_seg";
				if ( ! array_key_exists( 'templates', $tscfg ) || empty( $tscfg[ 'templates' ] ) ) continue;
				?>

				<div class="ui tab segments <?php echo $seg_classes; ?>" data-tab="<?php echo $tsection; ?>-tab">
					<div class="ui secondary segment">
						<h3 class="ui header center aligned" style="color: #867EA3;">
							<?php echo $tscfg['label']; ?>
							<?php if( array_key_exists( 'desc', $tscfg ) ): ?><div class="ui divider"></div><div class="sub header" style="text-align: justify;"><?php echo $tscfg['desc']; ?></div><?php endif; ?>
						</h3>
					</div>
					<div class="ui segment">
						<div class="ui divided items">
						<?php
						$item_counter = 1;
						foreach( (array) $tscfg['templates'] as $tindex => $template ): ?>
							<div class="item">
								<div class="content">
									<div class="ui header <?php echo ( ( $item_counter % 2 ) ? 'grey' : 'black' ); ?>">
										<?php
											echo $template[ 'label' ];
										?>
									</div>
									<div class="meta">
										<div class="ui right floated primary button hook_view_template" data-hook="<?php echo $template['action']; ?>" data-group="<?php echo $tsection; ?>" data-index="<?php echo $tindex; ?>">
											<?php _e( 'Preview', 'wp-job-manager-emails' ); ?>
											<i class="right chevron icon"></i>
										</div>
									</div>
									<div class="description">
										<?php echo $template[ 'desc' ]; ?>
									</div>
									<div class="extra">
										<?php echo $template[ 'action' ]; ?>
									</div>
								</div>
							</div>
						<?php
							$item_counter++;
						endforeach;
						?>
						</div>
					</div>
				</div>
				<?php
			}

		?>
	</div>

    <div class="actions">
        <div class="ui cancel button"><?php _e( 'Cancel', 'wp-job-manager-emails' ); ?></div>
    </div>
</div>

<div id="hook_view_template_modal" class="ui small hook_modals modal">
    <i class="close icon"></i>
    <div class="ui header" id="hook_view_template_modal_top">
		<i class="mail icon"></i>
		<div class="content">
			<?php _e( 'Email Template', 'wp-job-manager-emails' ); ?>
			<div class="sub header" id="hvt_header">
			</div>
		</div>
    </div>
	<div class="content">
		<table class="ui definition table">
			<thead>
				<tr>
					<th></th>
					<th><?php _e( 'Value', 'wp-job-manager-emails' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="right aligned collapsing"><?php _e( 'Action/Hook:', 'wp-job-manager-emails' ); ?></td>
					<td id="hvt_action"></td>
				</tr>
				<tr>
					<td class="right aligned collapsing"><?php _e( 'Title:', 'wp-job-manager-emails' ); ?></td>
					<td id="hvt_post_title"></td>
				</tr>
				<tr>
					<td class="right aligned collapsing"><?php _e( 'To:', 'wp-job-manager-emails' ); ?></td>
					<td id="hvt_to"></td>
				</tr>
				<tr>
					<td class="right aligned collapsing"><?php _e( 'Subject:', 'wp-job-manager-emails' ); ?></td>
					<td id="hvt_subject"></td>
				</tr>
				<tr id="hvt_attachments_row">
					<td class="right aligned collapsing"><?php _e( 'Attachments:', 'wp-job-manager-emails' ); ?></td>
					<td id="hvt_attachments"></td>
				</tr>
				<tr>
					<td rowspan="2" class="right aligned collapsing"><?php _e( 'Email Template:', 'wp-job-manager-emails' ); ?></td>
					<td rowspan="2" id="hvt_post_content" style="white-space: pre-line;"></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="ui icon red message">
		<i class="warning sign icon"></i>
		<div class="content">
			<p><?php _e( 'Clicking Use Template below will replace any values you have set on this template!', 'wp-job-manager-emails' ); ?></p>
		</div>
	</div>
    <div class="actions">
        <div class="ui button" id="hvt_back">
			<i class="left chevron icon"></i>
	        <?php _e( 'Back', 'wp-job-manager-emails' ); ?>
		</div>
        <div class="ui cancel red button"><?php _e( 'Cancel', 'wp-job-manager-emails' ); ?></div>
		<div id="hvt_use_template" class="ui positive right labeled icon button" data-hook="" data-group="">
			<?php _e( 'Use Template', 'wp-job-manager-emails' ); ?>
			<i class="checkmark icon"></i>
		</div>
    </div>
</div>