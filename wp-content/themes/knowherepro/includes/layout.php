<?php

if ( !function_exists('knowhere_logo') ) {

	function knowhere_logo() {
		global $knowhere_settings, $knowhere_config;

		switch ( $knowhere_config['header_type'] ) {
			case 'kw-type-1':

				if ( knowhere_is_realy_job_manager_tax() || is_post_type_archive('job_listing') || knowhere_job_listing_has_shortcode_jobs() ) {

					$term_header = $knowhere_config['term_header'];

					switch ( $term_header ) {
						case 'kw-theme-color':
						case 'kw-dark':
							$logo = $knowhere_settings['logo']['url'];
							break;
						case 'kw-light':
							$logo = $knowhere_settings['logo_header_3']['url'];
							break;
					}

				} else {
					$logo = $knowhere_settings['logo']['url'];
				}

				break;
			case 'kw-type-2':
				$logo = $knowhere_settings['logo']['url'];
				break;
			case 'kw-type-3':
				$logo = $knowhere_settings['logo_header_3']['url'];
				break;
			case 'kw-type-4':
				$logo = $knowhere_settings['logo_header_4']['url'];
				break;
			case 'kw-type-5':
				$logo = $knowhere_settings['logo_header_3']['url'];
				break;
			case 'kw-type-6':
				$logo = $knowhere_settings['logo']['url'];
				break;
			default:
				$logo = $knowhere_settings['logo_header_3']['url'];
				break;
		}

		ob_start();

		if ( !$logo ): ?>

			<h1 class="kw-logo"><?php else : ?><?php endif; ?>

				<a class="kw-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?> - <?php bloginfo( 'description' ); ?>" rel="home">
					<?php if ( $logo ) {
						echo '<img class="kw-standard-logo" src="' . esc_url(str_replace( array( 'http:', 'https:' ), '', $logo)) . '" alt="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" />';
					} else {
						bloginfo( 'name' );
					} ?>
				</a>

		<?php if ( !$logo ) : ?></h1><?php else : ?><?php endif;

		return apply_filters( 'knowhere_logo', ob_get_clean() );
	}

}

if ( !function_exists('knowhere_mobile_menu') ) {

	function knowhere_mobile_menu() {
		ob_start();

		$defaults = array(
			'container' => 'ul',
			'menu_class' => 'mobile-advanced',
			'theme_location' => 'primary',
			'fallback_cb' => false,
			'before' => '',
			'after' => '',
			'link_before' => '',
			'link_after' => '',
			'walker' => new knowhere_mobile_navwalker
		);

		if ( has_nav_menu('primary') ) {
			wp_nav_menu( $defaults );
		} else {
			echo '<ul class="mobile-advanced">';
			wp_list_pages('title_li=');
			echo '</ul>';
		}

		$output = str_replace( '&nbsp;', '', ob_get_clean() );
		return apply_filters( 'knowhere_mobile_menu', $output );
	}
}

