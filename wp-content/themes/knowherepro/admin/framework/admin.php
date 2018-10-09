<?php

if ( !class_exists('Knowhere_Admin') ) {

	class Knowhere_Admin {

		public function __construct() {
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts', ), 100 );
		}

		public function enqueue_scripts() {
			wp_localize_script('jquery', 'knowhere_admin_global_vars', array(
				'installing_text' => esc_html__( 'Installing', 'knowherepro' ),
				'import_theme_options_text' => esc_html__( 'Importing theme options', 'knowherepro' ),
				'finished_text' => esc_html__( 'Finished! Please visit your site.', 'knowherepro' ),
			));
		}

		public function admin_init() {

			if ( current_user_can( 'edit_theme_options' ) )  {

				if ( isset( $_GET['knowhere-deactivate'] ) && 'deactivate-plugin' == $_GET['knowhere-deactivate'] ) {
					check_admin_referer('knowhere-deactivate', 'knowhere-deactivate-nonce');

					$plugins = TGM_Plugin_Activation::$instance->plugins;

					foreach ($plugins as $plugin) {
						if ($plugin['slug'] == $_GET['plugin']) {
							deactivate_plugins($plugin['file_path']);
						}
					}

				}


				if ( isset( $_GET['knowhere-activate'] ) && 'activate-plugin' == $_GET['knowhere-activate'] ) {
					check_admin_referer( 'knowhere-activate', 'knowhere-activate-nonce' );

					$plugins = TGM_Plugin_Activation::$instance->plugins;

					foreach ( $plugins as $plugin ) {
						if ( isset( $_GET['plugin'] ) && $plugin['slug'] == $_GET['plugin'] ) {
							activate_plugin( $plugin['file_path'] );

							wp_redirect( admin_url( 'admin.php?page=knowhere-plugins' ) );
							exit;
						}
					}
				}

				if ( isset($_GET['theme_settings_export'] ) ) {

					// Widget settings
					$widget_settings = json_encode($this->export_widgets());

					// Sidebar settings
					$sidebar_settings = json_encode($this->export_sidebars());

					// Meta settings
					$meta_settings = json_encode($this->export_metadata());

					echo '<pre>'."\n"; echo '$widget_settings = "'; print_r($widget_settings); echo '";</pre>';
					echo '<pre>'."\n"; echo '$sidebar_settings = "'; print_r($sidebar_settings); echo '";</pre>'."\n\n";
					echo '<pre>'."\n"; echo '$meta_settings = "'; print_r($meta_settings); echo '";</pre>'."\n\n";
					exit();

				}

			}

		}

		public function export_widgets() {

			global $wp_registered_widgets;
			$saved_widgets = $options = array();

			foreach ($wp_registered_widgets as $registered) {
				if ( isset($registered['callback'][0]) && isset($registered['callback'][0]->option_name)) {
					$options[] = $registered['callback'][0]->option_name;
				}
			}

			foreach ($options as $key) {
				$widget = get_option($key, array());
				$treshhold = 1;
				if (array_key_exists("_multiwidget", $widget)) $treshhold = 2;

				if ($treshhold <= count($widget)) {
					$saved_widgets[$key] = $widget;
				}
			}

			$saved_widgets['sidebars_widgets'] = get_option('sidebars_widgets');
			return $saved_widgets;
		}

		function export_sidebars() {
			$custom_sidebars = get_option('knowhere_sidebars');

			if ( !empty($custom_sidebars) ) {
				return $custom_sidebars;
			}
			return '';
		}

		public function export_metadata() {
			global $wpdb;

			$meta_settings = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}product_catmeta", ARRAY_A);
			return $meta_settings;
		}


		public function plugin_link( $item ) {
			$installed_plugins = get_plugins();

			$item['sanitized_plugin'] = $item['name'];

			$actions = array();

			// We have a repo plugin
			if ( ! $item['version'] ) {
				$item['version'] = TGM_Plugin_Activation::$instance->does_plugin_have_update( $item['slug'] );
			}

			$disable_class = '';

			if ( ( 'revslider' == $item['slug'] ) && !KnowherePro()->registration->is_registered() ) {
				$disable_class = ' disabled';
			}

			/** We need to display the 'Install' hover link */
			if ( ! isset( $installed_plugins[$item['file_path']] ) ) {
				if ( ! $disable_class ) {
					$url = esc_url( wp_nonce_url(
						add_query_arg(
							array(
								'page'          => urlencode( TGM_Plugin_Activation::$instance->menu ),
								'plugin'        => urlencode( $item['slug'] ),
								'plugin_name'   => urlencode( $item['sanitized_plugin'] ),
								'plugin_source' => urlencode( $item['source'] ),
								'tgmpa-install' => 'install-plugin',
								'return_url'    => 'knowhere-plugins',
							),
							TGM_Plugin_Activation::$instance->get_tgmpa_url()
						),
						'tgmpa-install',
						'tgmpa-nonce'
					) );
				} else {
					$url = '#';
				}
				$actions = array(
					'install' => '<a href="' . $url . '" class="button button-primary' . $disable_class . '" title="' . sprintf( esc_attr__( 'Install %s', 'knowherepro' ), $item['sanitized_plugin'] ) . '">' . esc_attr__( 'Install', 'knowherepro' ) . '</a>',
				);
			}
			/** We need to display the 'Activate' hover link */
			elseif ( is_plugin_inactive( $item['file_path'] ) ) {
				if ( ! $disable_class ) {
					$url = esc_url( add_query_arg(
						array(
							'plugin'               => urlencode( $item['slug'] ),
							'plugin_name'          => urlencode( $item['sanitized_plugin'] ),
							'knowhere-activate'       => 'activate-plugin',
							'knowhere-activate-nonce' => wp_create_nonce( 'knowhere-activate' ),
						),
						admin_url( 'admin.php?page=knowhere-plugins' )
					) );
				} else {
					$url = '#';
				}

				$actions = array(
					'activate' => '<a href="' . $url . '" class="button button-primary' . $disable_class . '" title="' . sprintf( esc_attr__( 'Activate %s', 'knowherepro' ), $item['sanitized_plugin'] ) . '">' . esc_attr__( 'Activate' , 'knowherepro' ) . '</a>',
				);
			}
			/** We need to display the 'Update' hover link */
			elseif ( version_compare( $installed_plugins[$item['file_path']]['Version'], $item['version'], '<' ) ) {
				$disable_class = '';
				$url = wp_nonce_url(
					add_query_arg(
						array(
							'page'          => urlencode( TGM_Plugin_Activation::$instance->menu ),
							'plugin'        => urlencode( $item['slug'] ),
							'tgmpa-update'  => 'update-plugin',
							'version'       => urlencode( $item['version'] ),
							'return_url'    => 'knowhere-plugins',
						),
						TGM_Plugin_Activation::$instance->get_tgmpa_url()
					),
					'tgmpa-update',
					'tgmpa-nonce'
				);
				if ( ( 'revslider' == $item['slug'] ) && !KnowherePro()->registration->is_registered() ) {
					$disable_class = ' disabled';
				}
				$actions = array(
					'update' => '<a href="' . $url . '" class="button button-primary' . $disable_class . '" title="' . sprintf( esc_attr__( 'Update %s', 'knowherepro' ), $item['sanitized_plugin'] ) . '">' . esc_attr__( 'Update', 'knowherepro' ) . '</a>',
				);
			} elseif ( is_plugin_active( $item['file_path'] ) ) {
				$url = esc_url( add_query_arg(
					array(
						'plugin'                 => urlencode( $item['slug'] ),
						'plugin_name'            => urlencode( $item['sanitized_plugin'] ),
						'knowhere-deactivate'       => 'deactivate-plugin',
						'knowhere-deactivate-nonce' => wp_create_nonce( 'knowhere-deactivate' ),
					),
					admin_url( 'admin.php?page=knowhere-plugins' )
				) );
				$actions = array(
					'deactivate' => '<a href="' . $url . '" class="button button-primary" title="' . sprintf( esc_attr__( 'Deactivate %s', 'knowherepro' ), $item['sanitized_plugin'] ) . '">' . esc_attr__( 'Deactivate', 'knowherepro' ) . '</a>',
				);
			}

			return $actions;
		}

	}

	new Knowhere_Admin();

	require_once( get_theme_file_path( 'admin/framework/theme-options.php' ) );

}