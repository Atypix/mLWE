<?php

if (!class_exists('Knowhere_WooCommerce_Config')) {

	class Knowhere_WooCommerce_Config {

		public $action_quick_view = 'knowhere_action_add_product_popup';
		public $paths = array();
		public static $pathes = array();

		public function path($name, $file = '') {
			return $this->paths[$name] . (strlen($file) > 0 ? '/' . preg_replace('/^\//', '', $file) : '');
		}

		public function assetUrl($file) {
			return $this->paths['BASE_URI'] . $this->path('ASSETS_DIR_NAME', $file);
		}

		function __construct() {

			// Woocommerce support
			add_theme_support('woocommerce');

			$dir = get_template_directory() . '/config-woocommerce';

			define( 'Knowhere_Woo_Config', true );

			$this->paths = array(
				'PHP' => $dir . '/php/',
				'TEMPLATES' => $dir . '/templates/',
				'ASSETS_DIR_NAME' => 'assets',
				'BASE_URI' => get_template_directory_uri() . '/config-woocommerce/'
			);

			self::$pathes = $this->paths;

			include( $this->paths['PHP'] . 'functions.php' );
			include( $this->paths['PHP'] . 'ordering.class.php' );
			include( $this->paths['PHP'] . 'new-badge.class.php' );
			include( $this->paths['PHP'] . 'common-tab.class.php' );

			add_action( 'woocommerce_init', array($this, 'init'), 1 );

			add_action( 'admin_init', array($this, 'admin_init') );
			add_action( 'wp_enqueue_scripts', array($this, 'add_enqueue_scripts') );
			add_action( 'widgets_init', array($this, 'widgets_register') );

		}

		public function init() {
			$this->remove_actions();
			$this->add_actions();
			$this->add_filters();
		}

		public function admin_init() {
			add_filter( "manage_product_posts_columns", array($this, "manage_columns") );
		}

		public function add_filters() {

			if ( defined( 'WOOCOMMERCE_VERSION' ) ) {
				if ( version_compare( WOOCOMMERCE_VERSION, "2.1" ) >= 0 ) {
					add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
				} else {
					define( 'WOOCOMMERCE_USE_CSS', false );
				}
			}

			add_filter('woocommerce_page_title', array($this, 'woocommerce_page_title'));

			add_filter('woocommerce_general_settings', array($this, 'woocommerce_general_settings_filter'));
			add_filter('woocommerce_page_settings', array($this, 'woocommerce_general_settings_filter'));
			add_filter('woocommerce_catalog_settings', array($this, 'woocommerce_general_settings_filter'));
			add_filter('woocommerce_inventory_settings', array($this, 'woocommerce_general_settings_filter'));
			add_filter('woocommerce_shipping_settings', array($this, 'woocommerce_general_settings_filter'));
			add_filter('woocommerce_tax_settings', array($this, 'woocommerce_general_settings_filter'));
			add_filter('woocommerce_product_settings', array($this, 'woocommerce_general_settings_filter'));

			add_filter( 'woocommerce_review_gravatar_size', array($this, 'review_gravatar_size') );
			add_filter( 'woocommerce_upsell_display_args', array($this, 'upsell_display_args') );
			add_filter( 'woocommerce_cross_sells_total', array($this, 'cross_sells_total') );

			add_filter('loop_shop_columns', array($this, 'woocommerce_loop_columns'));
			add_filter('loop_shop_per_page', 'knowhere_woocommerce_product_count');

		}

		public function remove_actions() {

			remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
			remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);

			remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

			remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open');
			remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail');
			remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title');
			remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
			remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
			remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);
			remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);

			remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
			remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

			remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
			remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
			remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

			remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
			remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

			remove_action( 'woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open', 10 );
			remove_action( 'woocommerce_after_subcategory', 'woocommerce_template_loop_category_link_close', 10 );

			global $knowhere_settings;

			if ( !$knowhere_settings['product-short-description'] ) {
				remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
			}

		}

		public function add_actions() {

			add_action( 'woocommerce_edit_account_form', array( $this, 'woocommerce_edit_account_form' ) );
			add_action( 'woocommerce_save_account_details', array( $this, 'woocommerce_save_account_details' ) );

			add_action( 'knowhere-after-product-image', 'woocommerce_template_loop_add_to_cart', 12 );

			add_action( 'woocommerce_before_checkout_form', array($this, 'woocommerce_checkout_form'), 1 );

			/* Archive Hooks */
			add_action( 'woocommerce_archive_description', array($this, 'woocommerce_ordering_products') );

			/* Content Product Hooks */
			add_action( 'woocommerce_before_shop_loop_item_title', array($this, 'template_loop_product_thumbnail') );
			add_action( 'woocommerce_shop_loop_item_title', array($this, 'template_loop_product_title') );
			add_action( 'woocommerce_after_shop_loop_item_title', array($this, 'template_after_shop_loop_item_title') );

			add_action( 'woocommerce_before_subcategory', array($this, 'template_loop_category_link_open'), 10 );
			add_action( 'woocommerce_after_subcategory', 'woocommerce_template_loop_category_link_close', 10 );

			add_action( 'woocommerce_after_subcategory_title', array($this, 'category_excerpt_output') );

			/* Single Product Hooks */

			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 4 );
			add_action( 'woocommerce_single_product_summary', array($this, 'single_add_map'), 15 );
			add_action( 'woocommerce_single_product_summary', array($this, 'template_single_add_to_cart'), 40 );

			add_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 15 );
			add_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 20 );
		}

		public function woocommerce_edit_account_form() {

			$user = wp_get_current_user();
			$value = get_the_author_meta( 'knowhere_cupp_upload_meta', $user->ID );
			$allowed_mime_types = array('jpg', 'jpeg', 'gif', 'png');

			?>
				
			<fieldset>
				<p class="form-row form-row-wide">

					<label for="biography_photo" class="screen-reader-text"><?php esc_html_e( 'Profile Photo', 'knowherepro' ); ?></label>

						<div class="job-manager-uploaded-files">

							<?php if ( !empty($value) ): $image_src = $value; ?>

									<div class="job-manager-uploaded-file">
										<?php
										$extension = !empty($extension) ? $extension : substr(strrchr($image_src, '.'), 1);

										if (3 !== strlen($extension) || in_array($extension, array('jpg', 'gif', 'png', 'jpeg', 'jpe'))) : ?>
											<span class="job-manager-uploaded-file-preview">
													<img src="<?php echo esc_url($image_src); ?>" alt=""/>
													<a class="job-manager-profile-remove-uploaded-file"
													   href="#">[<?php esc_html_e('remove', 'knowherepro'); ?>]</a>
												</span>
										<?php else : ?>
											<span
												class="job-manager-uploaded-file-name"><code><?php echo esc_html(basename($image_src)); ?></code> <a
													class="job-manager-remove-uploaded-file"
													href="#">[<?php esc_html_e('remove', 'knowherepro'); ?>]</a></span>
										<?php endif; ?>

										<input type="hidden" id="current_biography_photo" name="current_biography_photo" class="input-text" value="<?php echo esc_url($image_src) ?>" />

									</div>

							<?php endif; ?>

						</div>

						<input type="file" id="biography_photo" name="biography_photo" class="profile-file-upload" data-file_types="<?php echo esc_attr( implode( '|', $allowed_mime_types ) ); ?>" />

				</p>
			</fieldset>

			<fieldset>
				<p class="form-row form-row-wide">
					<label for="biography" class="screen-reader-text"><?php esc_html_e( 'Biography', 'knowherepro' ); ?></label>
					<textarea class="input-text" name="biography" id="biography"><?php echo esc_textarea( $user->description ); ?></textarea>
				</p>
			</fieldset>

			<?php

		}

		public function woocommerce_save_account_details( $user_id ) {

			if ( isset( $_POST['biography'] ) ) {
				$biography = esc_textarea( $_POST['biography'] );
				update_user_meta( $user_id, 'description', $biography );
			}

			if ( isset( $_POST['current_biography_photo']) ) {
				$photo = esc_url( $_POST['current_biography_photo'] );
				update_user_meta( $user_id, 'knowhere_cupp_upload_meta', $photo );
			} else {
				update_user_meta( $user_id, 'knowhere_cupp_upload_meta', '' );
			}

		}

		public function single_add_map() {

			global $product;

			$address = get_post_meta( $product->get_id(), 'knowhere_product_map_address', true ); ?>

			<?php if ( isset($address) && !empty($address) ): ?>

				<div class="kw-entry-extra row kw-md-table-row">

					<div class="col-md-8">
						<div class="location"><span class="lnr icon-map-marker"></span> <?php echo esc_html($address) ?></div>
					</div>

					<?php $get_directions_link = '//maps.google.com/maps?daddr=' . $address; ?>

					<div class="col-md-4 kw-right-edge">
						<a href="<?php echo esc_url($get_directions_link); ?>" class="kw-map-link" target="_blank">
							<span class="lnr icon-map2"></span> <?php esc_html_e('View on Map', 'knowherepro') ?>
						</a>
					</div>

				</div>

			<?php endif; ?>

			<?php

		}

		public function template_single_add_to_cart() {
			global $product, $knowhere_settings;

			if ( in_array('tags', $knowhere_settings['product-metas']) ) {
				echo wc_get_product_tag_list( $product->get_id(), ' ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', count( $product->get_tag_ids() ), 'knowherepro' ) . ' ', '</span>' );
			}
		}

		public function widgets_register() {

			$before_widget = '<div id="%1$s" class="widget %2$s">';

			$widget_args = array(
				'before_widget' => $before_widget,
				'after_widget' => '</div>',
				'before_title' => '<h3 class="kw-widget-title">',
				'after_title' => '</h3>'
			);

			// Shop Widget Area
			register_sidebar(array(
				'name' => esc_html__('Shop Widget Area', 'knowherepro'),
				'id' => 'shop-widget-area',
				'description'   => esc_html__('For WooCommerce pages.', 'knowherepro'),
				'before_widget' => $widget_args['before_widget'],
				'after_widget' => $widget_args['after_widget'],
				'before_title' => $widget_args['before_title'],
				'after_title' => $widget_args['after_title']
			));

		}

		public function woocommerce_page_title($page_title) {

			if ( is_page( get_option('woocommerce_cart_page_id' ) ) ) {
				$page_title = get_the_title(get_option('woocommerce_cart_page_id' ));
			} elseif ( is_page( get_option('woocommerce_checkout_page_id') ) ) {
				$page_title = get_the_title(get_option('woocommerce_checkout_page_id' ));
			}

			return $page_title;
		}

		public function template_loop_product_thumbnail() {
			$this->product_thumbnail();
		}

		public function woocommerce_checkout_form() {
			echo knowhere_title( array( 'heading' => 'h2', 'title' => woocommerce_page_title(false) ) );
		}

		public function product_thumbnail() {

			if ( woocommerce_get_product_thumbnail() ): ?>

				<div class="kw-entry-thumb">
					<a href="<?php echo esc_url(get_the_permalink()); ?>"><?php echo woocommerce_get_product_thumbnail(); ?></a>
				</div><!--/ .kw-entry-thumb-->

			<?php else: ?>

				<div class="kw-entry-thumb">
					<?php echo wc_placeholder_img( 'shop_catalog' ); ?>
				</div>

			<?php endif;

		}

		public function review_gravatar_size() {
			return '100';
		}

		public function upsell_display_args($args) {
			global $knowhere_settings;

			$args['posts_per_page'] = $knowhere_settings['product-upsells-count'];

			return $args;
		}

		public function cross_sells_total($limit) {
			global $knowhere_settings;

			$count_limit = $knowhere_settings['product-crossell-count'];

			if ( $count_limit > 0 )
				return $count_limit;

			return $limit;
		}

		public function template_loop_product_title() {
			global $product;
			echo wc_get_product_category_list( $product->get_id(), ', ', '<div class="kw-entry-cats">', '</div>' );

			echo '<h3 class="kw-entry-title"><a href="'. esc_url(get_the_permalink()) .'">' . get_the_title() . '</a></h3>';
		}

		public function template_after_shop_loop_item_title() {
			echo '<div class="kw-entry-shop-controls">';
			$this->loop_price_output();
			woocommerce_template_loop_add_to_cart();
			echo '</div>';
		}

		public function loop_price_output() {
			woocommerce_template_loop_price();
//				woocommerce_template_loop_rating();
		}

		public function template_loop_category_link_open( $category ) {
			echo '<a class="kw-product-image" href="' . get_term_link( $category, 'product_cat' ) . '">';
		}

		public function category_excerpt_output($category) {
			?>
			<div class="kw-product-excerpt"><?php $description = $category->description; if ( $description ) { echo sprintf('%s', $description); } ?></div>
			<?php
		}

		public function manage_columns($columns) {
			unset($columns['wpseo-title']);
			unset($columns['wpseo-metadesc']);
			unset($columns['wpseo-focuskw']);
			return $columns;
		}


		public function woocommerce_loop_columns() {
			global $knowhere_settings;

			$woocommerce_columns = $knowhere_settings['shop-product-cols'];
			$overview_column_count = knowhere_get_meta_value('overview_column_count');

			if ( !empty($overview_column_count) ) { $woocommerce_columns = $overview_column_count; }

			return $woocommerce_columns;
		}

		public function add_enqueue_scripts() {
//			$woo_mod_file = $this->assetUrl('js/woocommerce-mod' . (WP_DEBUG ? '' : '.min') . '.js');
//			wp_enqueue_script( 'knowhere-woocommerce-mod', $woo_mod_file, array('jquery', 'knowhere-plugins', 'knowhere-core'), 1, true );
//
//			wp_localize_script('knowhere-woocommerce-mod', 'knowhere_woocommerce_mod', array(
//				'ajaxurl' => admin_url( 'admin-ajax.php' ),
//				'nonce_cart_item_remove' => wp_create_nonce( 'knowhere_cart_item_remove' )
//			));
		}

		public function woocommerce_ordering_products() {
			$ordering = new Knowhere_Catalog_Ordering();
			echo $ordering->output();
		}

		function woocommerce_general_settings_filter($options) {
			$delete = array('woocommerce_enable_lightbox');

			foreach ( $options as $key => $option ) {
				if (isset($option['id']) && in_array($option['id'], $delete)) {
					unset($options[$key]);
				}
			}
			return $options;
		}

		public static function content_truncate($string, $limit, $break = ".", $pad = "...") {
			if (strlen($string) <= $limit) { return $string; }

			if (false !== ($breakpoint = strpos($string, $break, $limit))) {
				if ($breakpoint < strlen($string) - 1) {
					$string = substr($string, 0, $breakpoint) . $pad;
				}
			}
			if (!$breakpoint && strlen(strip_tags($string)) == strlen($string)) {
				$string = substr($string, 0, $limit) . $pad;
			}
			return $string;
		}

		public static function create_data_string($data = array()) {
			$data_string = "";

			foreach($data as $key => $value) {
				if (is_array($value)) $value = implode(", ", $value);
				$data_string .= " data-$key={$value} ";
			}
			return $data_string;
		}

	}

	new Knowhere_WooCommerce_Config();

}