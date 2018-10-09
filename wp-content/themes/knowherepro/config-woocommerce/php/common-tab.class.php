<?php

if (!class_exists('Knowhere_Custom_Tab')) {

	class Knowhere_Custom_Tab {

		public $paths = array();

		public function path($name, $file = '') {
			return $this->paths[$name] . (strlen($file) > 0 ? '/' . preg_replace('/^\//', '', $file) : '');
		}

		public function assetUrl($file)  {
			return $this->paths['BASE_URI'] . $this->path('ASSETS_DIR_NAME', $file);
		}

		function __construct() {

			$this->paths = array(
				'BASE_URI' => get_template_directory_uri() . '/config-woocommerce/',
				'ASSETS_DIR_NAME' => 'assets'
			);

			if ( is_admin() ) {
				add_action( 'add_meta_boxes', array($this, 'dynamic_add_custom_tab') );

				add_action( 'load-post.php', array($this, 'add_assets') , 4 );
				add_action( 'load-post-new.php', array($this, 'add_assets') , 4 );

				/* Do something with the data entered */
				add_action( 'save_post', array($this, 'dynamic_save_postdata') );
			} else {
				add_action( 'init', array($this, 'init') );
			}

		}

		function init() {
			add_filter('woocommerce_product_tabs', array($this, 'product_custom_tabs'), 11);
		}

		function product_custom_tabs ($tabs) {
			global $post;

			$custom_tabs = get_post_meta($post->ID, 'knowhere_custom_tabs', true);
			$priority = 50;

			if ( isset($custom_tabs) && !empty($custom_tabs) && count($custom_tabs) > 0 ) {
				foreach(@$custom_tabs as $id => $tab) {
					if ( isset($tab['title_product_tab']) && $tab['title_product_tab'] != '' && isset($tab['content_product_tab']) ) {
						$tabs[$id] = array(
							'title' => $tab['title_product_tab'],
							'priority' => $priority,
							'callback' => 'knowhere_woocommerce_product_custom_tab'
						);
					}
					$priority = $priority + 1;
				}
			}
			return $tabs;
		}

		function add_assets() {
			add_action('print_media_templates', array($this, 'add_tmpl') );
			wp_enqueue_script( 'knowhere_custom_tab_js', $this->assetUrl('js/custom_tab.js'), array('jquery'));
			wp_enqueue_style( 'knowhere_custom_tab_css', $this->assetUrl('css/custom_tab.css'));
		}

		public function add_tmpl() {

			$settings = array(
				'textarea_name' => 'knowhere_custom_tabs[__REPLACE_SSS__][content_product_tab]',
				'textarea_rows' => 3,
				'quicktags' => true,
				'tinymce' => false
			);

			ob_start(); ?>

			<script type="text/html" id="tmpl-add-custom-tab">
				<li>
					<div class="handle-area"></div>
					<div class="item">
						<h3><?php esc_html_e('Title Custom Tab', 'knowherepro'); ?></h3>
						<input type="text" name="knowhere_custom_tabs[__REPLACE_SSS__][title_product_tab]" value=""/>
						<p class="desc"><?php esc_html_e('Enter a title for the tab (required field)', 'knowherepro'); ?></p>
					</div>
					<div class="item wp-editor">
						<h3><?php esc_html_e('Content Custom Tab', 'knowherepro'); ?></h3>
						<?php wp_editor( '', '__REPLACE_SSS__', $settings ); ?>
					</div>
					<div class="item">
						<a href="javascript:void(0)" class="button button-secondary remove-custom-tab"><?php _e('Remove Custom Tab', 'knowherepro'); ?></a>
					</div>
				</li>
			</script>

			<?php echo ob_get_clean();
		}

		function dynamic_add_custom_tab() {
			add_meta_box('knowhere_dynamic_custom_tab', esc_html__( 'Custom Tabs', 'knowherepro' ), array($this, 'dynamic_inner_custom_tab'), 'product', 'advanced', 'high');
		}

		/* Prints the box content */
		function dynamic_inner_custom_tab() {
			global $post;

			// Use nonce for verification
			wp_nonce_field( 'knowhere-custom-tab', 'dynamicMeta_noncename' );
			?>

			<div id="meta_custom_tabs">

				<?php $custom_tabs = get_post_meta($post->ID, 'knowhere_custom_tabs', true); ?>

				<ul class="custom-box-holder">

					<?php if (isset($custom_tabs) && !empty($custom_tabs) && count($custom_tabs) > 0): ?>

						<?php foreach($custom_tabs as $id => $tab): ?>

							<?php if (isset($tab['title_product_tab']) || isset($tab['content_product_tab'])): ?>

								<li>
									<div class="handle-area"></div>
									<div class="item">
										<h3><?php esc_html_e('Title Custom Tab', 'knowherepro'); ?></h3>
										<input type="text" name="knowhere_custom_tabs[<?php echo esc_attr($id); ?>][title_product_tab]" value="<?php echo esc_attr($tab['title_product_tab']); ?>" />
										<p class="desc"><?php esc_html_e('Enter a title for the tab (required field)', 'knowherepro'); ?></p>
									</div>
									<div class="item wp-editor">
										<h3><?php esc_html_e('Content Custom Tab', 'knowherepro'); ?></h3>
										<?php wp_editor( $tab['content_product_tab'], esc_attr($id), array('textarea_name' => 'knowhere_custom_tabs['. $id .'][content_product_tab]', 'textarea_rows' => 3, 'tinymce' => false) ); ?>
									</div>
									<div class="item">
										<a href="javascript:void(0)" class="button button-secondary remove-custom-tab"><?php esc_html_e('Remove Custom Tab', 'knowherepro'); ?></a>
									</div>
								</li>

							<?php endif; ?>

						<?php endforeach; ?>

					<?php endif; ?>

				</ul><!--/ .custom-tabs-->

				<a href="javascript:void(0);" class="button button-primary add-custom-tab"><?php esc_html_e('Add Custom Tab', 'knowherepro'); ?></a>

			</div><?php

		}

		/* When the post is saved, saves our custom data */
		function dynamic_save_postdata ($post_id) {

			// verify if this is an auto save routine.
			// If it is our form has not been submitted, so we dont want to do anything
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
				return;

			// verify this came from the our screen and with proper authorization,
			// because save_post can be triggered at other times
			if ( !isset( $_POST['dynamicMeta_noncename'] ) )
				return;

			if ( !wp_verify_nonce( $_POST['dynamicMeta_noncename'], 'knowhere-custom-tab' ) )
				return;

			if ( !isset( $_POST['knowhere_custom_tabs'] ) ) {
				$_POST['knowhere_custom_tabs'] = '';
			}

			$tabs = $_POST['knowhere_custom_tabs'];
			update_post_meta($post_id, 'knowhere_custom_tabs', $tabs);
		}

	}

	new Knowhere_Custom_Tab();
}

