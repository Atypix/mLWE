<div class="<?php if ( knowhere_using_login_with_ajax() ) { echo 'lwa'; } ?> job-manager-form wp-job-manager-bookmarks-form">

	<?php
	$url = knowhere_get_login_url();
	if ( ! empty( $url ) ) : ?>
		<span class="lnr icon-heart"></span>
		<a class="<?php echo knowhere_get_login_link_class('bookmark-notice'); ?>" href="<?php echo esc_url($url); ?>">
			<?php printf( esc_html__( 'Add to favorites', 'knowherepro' ) ); ?>
		</a>
	<?php endif; ?>

</div>