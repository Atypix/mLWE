<script id="tmpl-mad-image-item" type="text/html">
	<input type="hidden" name="{{{ data.fieldName }}}" value="{{{ data.id }}}" class="mad-media-input">
	<div class="mad-media-preview">
		<div class="mad-media-content">
			<div class="centered">
				<# if ( 'image' === data.type && data.sizes ) { #>
					<# if ( data.sizes.thumbnail ) { #>
						<img src="{{{ data.sizes.thumbnail.url }}}">
					<# } else { #>
						<img src="{{{ data.sizes.full.url }}}">
					<# } #>
				<# } else { #>
					<# if ( data.image && data.image.src && data.image.src !== data.icon ) { #>
						<img src="{{ data.image.src }}" />
					<# } else { #>
						<img src="{{ data.icon }}" />
					<# } #>
				<# } #>
			</div>
		</div>
	</div>
	<div class="mad-overlay"></div>
	<div class="mad-media-bar">
		<a class="mad-edit-media" title="{{{ i18nRwmbMedia.edit }}}" href="{{{ data.editLink }}}" target="_blank">
			<span class="dashicons dashicons-edit"></span>
		</a>
		<a href="#" class="mad-remove-media" title="{{{ i18nRwmbMedia.remove }}}">
			<span class="dashicons dashicons-no-alt"></span>
		</a>
	</div>
</script>
