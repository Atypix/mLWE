<div class="settings-box">

	<div class="group-radio">
		<label><input type="radio" <?php checked( $mode, 'default' ) ?> name="knowhere_page_title[mode]" value="default"><?php esc_html_e('Default', 'knowherepro') ?></label>
		<label><input type="radio" <?php checked( $mode, 'custom' ) ?> name="knowhere_page_title[mode]" value="custom"><?php esc_html_e('Custom', 'knowherepro') ?></label>
		<label><input type="radio" <?php checked( $mode, 'none' ) ?> name="knowhere_page_title[mode]" value="none"><?php esc_html_e('None', 'knowherepro') ?></label>
	</div>

	<div class="settings-box-content <?php if ( $mode !== 'custom' ): ?>knowhere-hidden<?php endif; ?>">
		<?php
			foreach( $options as $option ) {
				knowhere_page_title_meta_html( $page_title, $option );
			}
		?>
	</div><!--/ .settings-box-content-->

</div><!--/ .settings-box-->

