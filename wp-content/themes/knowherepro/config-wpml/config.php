<?php

if (!class_exists('Knowhere_WC_WPML_Config')) {

	class Knowhere_WC_WPML_Config {

		public $paths = array();

		protected function path($name, $file = '') {
			return $this->paths[$name] . (strlen($file) > 0 ? '/' . preg_replace('/^\//', '', $file) : '');
		}
		protected function assetUrl($file) {
			return $this->paths['BASE_URI'] . $this->path('ASSETS_DIR_NAME', $file);
		}

		function __construct() {

			$dir = get_template_directory() . '/config-wpml';

			$this->paths = array(
				'PHP' => $dir . '/php/',
				'ASSETS_DIR_NAME' => 'assets',
				'BASE_URI' => get_template_directory_uri() . '/config-wpml/'
			);

		}

		public static function wpml_header_languages_list() {

			$languages = array();
			$my_const = ICL_LANGUAGE_CODE;

			if ( defined('ICL_LANGUAGE_CODE') && !empty($my_const) ) {
				$languages = apply_filters( 'wpml_active_languages', NULL, 'skip_missing=0' );
			}

			if ( 1 < count($languages) ) { ?>

				<li>

					<div class="kw-dropdown kw-language-options">

						<a href="javascript:void(0)" class="kw-dropdown-invoker">
							<?php
							foreach( $languages as $l ) {
								if ( $l['active'] ) {
									echo '<img src="' . esc_attr($l['country_flag_url']) . '" alt="' . esc_attr($l['language_code']) . '" />';
									echo icl_disp_language($l['native_name'], $l['translated_name']);
								}
							}
							?>
						</a>

						<?php
						echo '<ul class="kw-dropdown-list">';
							foreach ( $languages as $l ) {
								if ($l['active']) continue;
								echo '<li>';
								if(!$l['active']) echo '<a href="'. esc_url($l['url']) .'">';
								echo '<img src="' . esc_attr($l['country_flag_url']) . '" alt="' . esc_attr($l['language_code']) . '" />';
								echo esc_html($l['native_name']);
								if(!$l['active']) echo '</a>';
								echo '</li>';
							}
						echo '</ul>';
						?>

					</div>

				</li>

			<?php
			}
		}

	}

	new Knowhere_WC_WPML_Config();

}


