<?php
if ( ! defined ( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( ! class_exists ( 'YITH_YWGC_Backend' ) ) {

	/**
	 *
	 * @class   YITH_YWGC_Backend
	 *
	 * @since   1.0.0
	 * @author  Lorenzo Giuffrida
	 */
	class YITH_YWGC_Backend {

		const YWGC_GIFT_CARD_LAST_VIEWED_ID = 'ywgc_last_viewed';

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null ( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		protected function __construct() {

			/**
			 * Remove unwanted WordPress submenu item
			 */
			add_action ( 'admin_menu', array( $this, 'remove_unwanted_custom_post_type_features' ), 5 );

			/**
			 * show a bubble with the number of new gift cards from the last visit
			 */
			add_action ( 'admin_menu', array( $this, 'show_number_of_new_gift_cards' ), 99 );

			/**
			 * Enqueue scripts and styles
			 */
			add_action ( 'admin_enqueue_scripts', array( $this, 'enqueue_backend_files' ) );

			/**
			 * Add the "Gift card" type to product type list
			 */
			add_filter ( 'product_type_selector', array(
				$this,
				'add_gift_card_product_type'
			) );

			/**
			 * * Save gift card data when a product of type "gift card" is saved
			 */
			add_action ( 'save_post', array(
				$this,
				'save_gift_card'
			), 1, 2 );

			/**
			 * * Save gift card data when a product of type "gift card" is saved
			 */
			add_action ( 'save_post', array(
				$this,
				'save_pre_printed_gift_card_code'
			), 1, 2 );

			/**
			 * Ajax call for adding and removing gift card amounts on product edit page
			 */
			add_action ( 'wp_ajax_add_gift_card_amount', array(
				$this,
				'add_gift_card_amount_callback'
			) );
			add_action ( 'wp_ajax_remove_gift_card_amount', array(
				$this,
				'remove_gift_card_amount_callback'
			) );

			/**
			 * Hide some item meta from product edit page
			 */
			add_filter ( 'woocommerce_hidden_order_itemmeta', array(
				$this,
				'hide_item_meta'
			) );


			if ( version_compare ( WC ()->version, '2.6.0', '<' ) ) {

				/**
				 * Append gift card amount generation controls to general tab of product page, below the SKU element
				 */
				add_action ( 'woocommerce_product_options_sku', array(
					$this,
					'show_gift_card_product_settings'
				) );

			} else {
				/**
				 * Append gift card amount generation controls to general tab on product page
				 */
				add_action ( 'woocommerce_product_options_general_product_data', array(
					$this,
					'show_gift_card_product_settings'
				) );
			}

			/**
			 * Generate a valid card number for every gift card product in the order
			 */
			add_action ( 'woocommerce_order_status_changed', array(
				$this,
				'order_status_changed'
			), 10, 3 );

			add_action ( 'woocommerce_before_order_itemmeta', array(
				$this,
				'show_gift_card_code_on_order_item'
			), 10, 3 );

			/**
			 * Set the CSS class 'show_if_gift-card in tax section
			 */
			add_action ( 'woocommerce_product_options_general_product_data', array(
				$this,
				'show_tax_class_for_gift_cards'
			) );
		}

		/**
		 * Show the gift card code under the order item, in the order admin page
		 *
		 * @param int        $item_id
		 * @param array      $item
		 * @param WC_product $_product
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function show_gift_card_code_on_order_item( $item_id, $item, $_product ) {

			global $theorder;
			$gift_ids = ywgc_get_order_item_giftcards ( $item_id );


			if ( empty( $gift_ids ) ) {
				return;
			}

			foreach ( $gift_ids as $gift_id ) {

				$gc = new YWGC_Gift_Card_Premium( array( 'ID' => $gift_id ) );

				if ( ! $gc->is_pre_printed () ):
					?>
					<div>
					<span
						class="ywgc-gift-code-label"><?php _e ( "Gift card code: ", 'yith-woocommerce-gift-cards' ); ?></span>

						<a href="<?php echo admin_url ( 'edit.php?s=' . $gc->get_code () . '&post_type=gift_card&mode=list' ); ?>"
						   class="ywgc-card-code"><?php echo $gc->get_code (); ?></a>
					</div>
				<?php elseif ( apply_filters ( 'yith_ywgc_enter_pre_printed_gift_card_code', true, $theorder, $_product ) ): ?>
					<div>
					<span
						class="ywgc-gift-code-label"><?php _e ( "Enter the pre-printed code: ", 'yith-woocommerce-gift-cards' ); ?></span>
						<input type="text" name="ywgc-pre-printed-code[<?php echo $gc->ID; ?>]"
						       class="ywgc-pre-printed-code">
					</div>
				<?php endif;
			}
		}

		/**
		 * show a bubble with the number of new gift cards from the last visit
		 */
		public function show_number_of_new_gift_cards() {
			global $menu;
			foreach ( $menu as $key => $value ) {
				if ( isset( $value[5] ) && ( $value[5] == 'menu-posts-' . YWGC_CUSTOM_POST_TYPE_NAME ) ) {
					//  Add a bubble with the new gift card created since the last time
					$last_viewed = get_option ( self::YWGC_GIFT_CARD_LAST_VIEWED_ID, 0 );

					global $wpdb;
					$new_ids = $wpdb->get_var ( $wpdb->prepare ( "SELECT count(id) FROM {$wpdb->prefix}posts WHERE post_type = %s and ID > %d", YWGC_CUSTOM_POST_TYPE_NAME, $last_viewed ) );
					$bubble  = "<span class='awaiting-mod count-{$new_ids}'><span class='pending-count'>{$new_ids}</span></span>";
					$menu[ $key ][0] .= $bubble;

					return;
				}
			}
		}

		/*
		 * Remove features for the review custom post type
		 */
		public function remove_unwanted_custom_post_type_features() {
			global $submenu;

			return;
			if ( isset( $submenu[ "edit.php?post_type=" . YWGC_CUSTOM_POST_TYPE_NAME ] ) ) {
				$gift_card_menu = $submenu[ 'edit.php?post_type=' . YWGC_CUSTOM_POST_TYPE_NAME ];

				foreach ( $gift_card_menu as $key => $value ) {
					if ( $value[2] == 'post-new.php?post_type=' . YWGC_CUSTOM_POST_TYPE_NAME ) {
						//  it's the add-new submenu item, we want to remove it
						unset( $submenu[ "edit.php?post_type=" . YWGC_CUSTOM_POST_TYPE_NAME ][ $key ] );
						break;
					}
				}
			}
		}


		/**
		 * Enqueue scripts on administration comment page
		 *
		 * @param $hook
		 */
		function enqueue_backend_files( $hook ) {
			global $post_type;

			$screen = get_current_screen ();

			//  Enqueue style and script for the edit-gift_card screen id
			if ( "edit-gift_card" == $screen->id ) {

				//  When viewing the gift card page, store the max id so all new gift cards will be notified next time
				global $wpdb;
				$last_id = $wpdb->get_var ( $wpdb->prepare ( "SELECT max(id) FROM {$wpdb->prefix}posts WHERE post_type = %s", YWGC_CUSTOM_POST_TYPE_NAME ) );
				update_option ( self::YWGC_GIFT_CARD_LAST_VIEWED_ID, $last_id );
			}

			if ( isset( $_REQUEST[ 'page' ] ) && $_REQUEST[ 'page' ] == 'yith_woocommerce_gift_cards_panel' )
            {

                //  Add style and scripts
                wp_enqueue_style ( 'ywgc_gift_cards_admin_panel_css',
                    YITH_YWGC_ASSETS_URL . '/css/ywgc-gift-cards-admin-panel.css',
                    array(),
                    YITH_YWGC_VERSION );

                wp_register_script ( "ywgc_gift_cards_admin_panel",

                    YITH_YWGC_SCRIPT_URL . yit_load_js_file ( 'ywgc-gift-cards-admin-panel.js' ),
                    array(
                        'jquery',
                        'jquery-blockui',
                    ),
                    YITH_YWGC_VERSION,
                    true );

                wp_localize_script ( 'ywgc_gift_cards_admin_panel',
                    'ywgc_data', array(
                        'loader'            => apply_filters ( 'yith_gift_cards_loader', YITH_YWGC_ASSETS_URL . '/images/loading.gif' ),
                        'ajax_url'          => admin_url ( 'admin-ajax.php' ),
                    )
                );

                wp_enqueue_script ( "ywgc_gift_cards_admin_panel" );

            }

			if ( ( 'product' == $post_type ) || ( 'gift_card' == $post_type ) ) {

				//  Add style and scripts
				wp_enqueue_style ( 'ywgc-backend-css',
					YITH_YWGC_ASSETS_URL . '/css/ywgc-backend.css',
					array(),
					YITH_YWGC_VERSION );

				wp_register_script ( "ywgc-backend",

					YITH_YWGC_SCRIPT_URL . yit_load_js_file ( 'ywgc-backend.js' ),
					array(
						'jquery',
						'jquery-blockui',
					),
					YITH_YWGC_VERSION,
					true );

				wp_localize_script ( 'ywgc-backend',
					'ywgc_data', array(
						'loader'            => apply_filters ( 'yith_gift_cards_loader', YITH_YWGC_ASSETS_URL . '/images/loading.gif' ),
						'ajax_url'          => admin_url ( 'admin-ajax.php' ),
						'choose_image_text' => __ ( 'Choose Image', 'yith-woocommerce-gift-cards' ),
					)
				);

				wp_enqueue_script ( "ywgc-backend" );
			}

			if ( "upload" == $screen->id ) {

				wp_register_script ( "ywgc-categories",
					YITH_YWGC_SCRIPT_URL . yit_load_js_file ( 'ywgc-categories.js' ),
					array(
						'jquery',
						'jquery-blockui',
					),
					YITH_YWGC_VERSION,
					true );

				$categories1_id = 'categories1_id';
				$categories2_id = 'categories2_id';

				wp_localize_script ( 'ywgc-categories', 'ywgc_data', array(
					'loader'                => apply_filters ( 'yith_gift_cards_loader', YITH_YWGC_ASSETS_URL . '/images/loading.gif' ),
					'ajax_url'              => admin_url ( 'admin-ajax.php' ),
					'set_category_action'   => __ ( "Set gift card category", 'yith-woocommerce-gift-cards' ),
					'unset_category_action' => __ ( "Unset gift card category", 'yith-woocommerce-gift-cards' ),
					'categories1'           => $this->get_category_select ( $categories1_id ),
					'categories1_id'        => $categories1_id,
					'categories2'           => $this->get_category_select ( $categories2_id ),
					'categories2_id'        => $categories2_id,
				) );

				wp_enqueue_script ( "ywgc-categories" );
			}
		}

		public function get_category_select( $select_id ) {
			$media_terms = get_terms ( YWGC_CATEGORY_TAXONOMY, 'hide_empty=0' );

			$select = '<select id="' . $select_id . '" name="' . $select_id . '">';
			foreach ( $media_terms as $entry ) {
				$select .= '<option value="' . $entry->term_id . '">' . $entry->name . '</option>';
			}
			$select .= '</select>';

			return $select;

		}

		/**
		 * Add the "Gift card" type to product type list
		 *
		 * @param array $types current type array
		 *
		 * @return mixed
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function add_gift_card_product_type( $types ) {
			if ( YITH_YWGC ()->current_user_can_create () ) {
				$types[ YWGC_GIFT_CARD_PRODUCT_TYPE ] = __ ( "Gift card", 'yith-woocommerce-gift-cards' );
			}

			return $types;
		}

		/**
		 * Save gift card additional data
		 *
		 * @param $product_id
		 */
		public function save_gift_card_data( $product_id ) {

			$product = new WC_Product_Gift_Card( $product_id );

			/**
			 * Save custom gift card header image, if exists
			 */
			if ( isset( $_REQUEST['ywgc_product_image_id'] ) ) {
				if ( intval ( $_REQUEST['ywgc_product_image_id'] ) ) {

					$product->set_header_image ( $_REQUEST['ywgc_product_image_id'] );
				} else {

					$product->unset_header_image ();
				}
			}


			/**
			 * Save gift card amounts
			 */
			$amounts = isset( $_POST["gift-card-amounts"] ) ? $_POST["gift-card-amounts"] : array();
			$product->save_amounts ( $amounts );

			/**
			 * Save gift card settings about template design
			 */
			if ( isset( $_POST['template-design-mode'] ) ) {
				$product->set_design_status ( $_POST['template-design-mode'] );
			}
		}


		/**
		 * Check if there are pre-printed gift cards that were filled and need to be updated
		 *
		 * @param $post_id
		 * @param $post
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function save_pre_printed_gift_card_code( $post_id, $post ) {

			if ( 'shop_order' != $post->post_type ) {
				return;
			}

			if ( ! isset( $_POST["ywgc-pre-printed-code"] ) ) {
				return;
			}

			$codes = $_POST["ywgc-pre-printed-code"];

			foreach ( $codes as $gift_id => $gift_code ) {
				if ( ! empty( $gift_code ) ) {
					$gc = new YWGC_Gift_Card_Premium( array( 'ID' => $gift_id ) );

					$gc->gift_card_number = $gift_code;
					$gc->set_enabled_status ( true );
					$gc->save ();
				}
			}
		}


		/**
		 * Save gift card amount when a product is saved
		 *
		 * @param $post_id int
		 * @param $post    object
		 *
		 * @return mixed
		 */
		function save_gift_card( $post_id, $post ) {

			$product = wc_get_product ( $post_id );

			if ( null == $product ) {
				return;
			}

			if ( ! isset( $_POST["product-type"] ) || ( YWGC_GIFT_CARD_PRODUCT_TYPE != $_POST["product-type"] ) ) {

				return;
			}

			// verify this is not an auto save routine.
			if ( defined ( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			/**
			 * Update gift card amounts
			 */
			$this->save_gift_card_data ( $post_id );


			do_action ( 'yith_gift_cards_after_product_save', $post_id, $post, $product );
		}


		/**
		 * Add a new amount to a gift card prdduct
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		public function add_gift_card_amount_callback() {

			$amount = wc_format_decimal ( $_POST['amount'] );

			if ( ! is_numeric( $amount ) )
				return;

			$product_id = intval ( $_POST['product_id'] );
			$gift       = new WC_Product_Gift_Card( $product_id );
			$res        = false;

			if ( $gift->exists () ) {
				$res = $gift->add_amount ( $amount );
			}

			wp_send_json (
				array(
					"code"  => $res ? 1 : 0,
					"value" => $this->gift_card_amount_list_html ( $product_id )
				) );
		}

		/**
		 * Remove amount to a gift card prdduct
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		public function remove_gift_card_amount_callback() {
			$amount     = wc_format_decimal ( $_POST['amount'] );
			$product_id = intval ( $_POST['product_id'] );

			$gift = new WC_Product_Gift_Card( $product_id );
			if ( $gift->exists () ) {
				$gift->remove_amount ( $amount );
			}

			wp_send_json ( array( "code" => '1' ) );
		}

		/**
		 * Retrieve the html content that shows the gift card amounts list
		 *
		 * @param $product_id int gift card product id
		 *
		 * @return string
		 */
		private function gift_card_amount_list_html( $product_id ) {

			ob_start ();
			$this->show_gift_card_amount_list ( $product_id );
			$html = ob_get_contents ();
			ob_end_clean ();

			return $html;
		}


		/**
		 * Hide some item meta from order edit page
		 */
		public function hide_item_meta( $args ) {
			$args[] = YWGC_META_GIFT_CARD_POST_ID;

			return $args;
		}

		/**
		 * Show checkbox enabling the product to avoid use of free amount
		 */
		public function show_manual_amount_settings( $product_id ) {

			$product        = new WC_Product_Gift_Card( $product_id );
			$manual_mode    = $product->get_manual_amount_status ();
			$global_checked = ( $manual_mode == "global" ) || ( ( $manual_mode != "accept" ) && ( $manual_mode != "reject" ) );
			?>

			<p class="form-field permit_free_amount">
				<label><?php _e ( "Variable amount mode", 'yith-woocommerce-gift-cards' ); ?></label>
				<span class="wrap">
                    <input type="radio" class="ywgc-manual-amount-mode global-manual-mode" name="manual_amount_mode"
                           value="global" <?php checked ( $global_checked, true ); ?>>
                    <span><?php _e ( "Default", 'yith-woocommerce-gift-cards' ); ?></span>
                    <input type="radio" class="ywgc-manual-amount-mode accept-manual-mode" name="manual_amount_mode"
                           value="accept" <?php checked ( $manual_mode, "accept" ); ?>>
                    <span><?php _e ( "Enabled", 'yith-woocommerce-gift-cards' ); ?></span>
                    <input type="radio" class="ywgc-manual-amount-mode deny-manual-mode" name="manual_amount_mode"
                           value="reject" <?php checked ( $manual_mode, "reject" ); ?>>
                    <span><?php _e ( "Disabled", 'yith-woocommerce-gift-cards' ); ?></span>
                </span>
			</p>

			<?php
		}

		/**
		 * Show the settings to let the admin choose if for the product is available the custom design
		 *
		 * @param int $product_id
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function show_template_design_settings( $product_id ) {

			$product = new WC_Product_Gift_Card( $product_id );

			$allow_template = $product->get_design_status ();
			$global_checked = ( $allow_template == "global" ) || ( ( $allow_template != "enabled" ) && ( $allow_template != "disabled" ) );
			?>

			<p class="form-field permit_template_design show_if_virtual">
				<label><?php _e ( "Show template design", 'yith-woocommerce-gift-cards' ); ?></label>
				<span class="wrap">
                    <input type="radio" class="ywgc-template-design-mode" name="template-design-mode"
                           value="global" <?php checked ( $global_checked, true ); ?>>
                    <span><?php _e ( "Default", 'yith-woocommerce-gift-cards' ); ?></span>
                    <input type="radio" class="ywgc-template-design-mode" name="template-design-mode"
                           value="enabled" <?php checked ( $allow_template, "enabled" ); ?>>
                    <span><?php _e ( "Enabled", 'yith-woocommerce-gift-cards' ); ?></span>
                    <input type="radio" class="ywgc-template-design-mode" name="template-design-mode"
                           value="disabled" <?php checked ( $allow_template, "disabled" ); ?>>
                    <span><?php _e ( "Disabled", 'yith-woocommerce-gift-cards' ); ?></span>
                </span>
			</p>

			<?php
		}

		/**
		 * Show checkbox enabling the product to avoid use of free amount
		 *
		 * @param int $product_id
		 */
		public function show_custom_header_image_settings( $product_id ) {
			$gift_product = new WC_Product_Gift_Card( $product_id );

			$image_id = $gift_product->get_manual_header_image ( $product_id, 'id' );
			?>
			<p id="ywgc_header_image" class="form-field">
				<label><?php _e ( "Gift card image", 'yith-woocommerce-gift-cards' ); ?></label>
				<span id="ywgc-card-header-image" class="wrap">
                        <?php if ( $image_id ) {
	                        echo '<a target="_blank" href="' . yith_get_attachment_image_url ( $image_id, "full" ) . '">';
	                        echo wp_get_attachment_image ( $image_id, array( 80, 80 ) );
	                        echo '</a>';
                        } else {
	                        _e ( 'No image selected, the featured image will be used', 'yith-woocommerce-gift-cards' );
                        }
                        ?>
					<input type="button"
					       name="ywgc_product_image"
					       value="<?php _e ( 'Choose image', 'yith-woocommerce-gift-cards' ) ?>"
					       class="image-gallery-chosen button" />

                        <input type="button"
                               name="ywgc_reset_product_image"
                               value="<?php _e ( 'Reset image', 'yith-woocommerce-gift-cards' ) ?>"
                               class="image-gallery-reset button" />

                        <input type="hidden"
                               id="ywgc_product_image_id"
                               name="ywgc_product_image_id"
                               value="<?php echo esc_attr ( $image_id ); ?>" />

					<?php echo wc_help_tip ( 'Choose the image to be used as the gift card main image. Leave it blank if you want to use the featured image instead.' ); ?>
                    </span>
			</p>
			<?php
		}

		/**
		 * Show controls on backend product page to let create the gift card price
		 */
		public function show_gift_card_product_settings() {

			if ( ! YITH_YWGC ()->current_user_can_create () ) {
				return;
			}

			global $post, $thepostid;
			?>
			<div class="options_group show_if_gift-card">
				<p class="form-field">
					<label><?php _e ( "Gift card amount", 'yith-woocommerce-gift-cards' ); ?></label>
					<span class="wrap add-new-amount-section">
                    <input type="text" id="gift_card-amount" name="gift_card-amount" class="short wc_input_price" style=""
                           placeholder="">
                    <a href="#" class="add-new-amount"><?php _e ( "Add", 'yith-woocommerce-gift-cards' ); ?></a>
                </span>
				</p>

				<?php
				$this->show_gift_card_amount_list ( $thepostid );
				do_action ( 'yith_ywgc_product_settings_after_amount_list', $thepostid );

				?>
			</div>
			<?php
		}

		/**
		 * Show the gift card amounts list
		 *
		 * @param $product_id int gift card product id
		 */
		private function show_gift_card_amount_list( $product_id ) {

			$gift_card = new WC_Product_Gift_Card( $product_id );
			if ( ! $gift_card->exists () ) {
				return;
			}
			$amounts = $gift_card->get_product_amounts ();

			?>

			<p class="form-field _gift_card_amount_field">
				<?php if ( $amounts ): ?>
					<?php foreach ( $amounts as $amount ) : ?>
						<span class="variation-amount"><?php echo wc_price ( $amount ); ?>
							<input type="hidden" name="gift-card-amounts[]" value="<?php _e ( $amount ); ?>">
                        <a href="#" class="remove-amount"></a></span>
					<?php endforeach; ?>
				<?php else: ?>
					<span
						class="no-amounts"><?php _e ( "You don't have configured any gift card yet", 'yith-woocommerce-gift-cards' ); ?></span>
				<?php endif; ?>
			</p>
			<?php
		}


		/**
		 * When the order is completed, generate a card number for every gift card product
		 *
		 * @param int|WC_Order $order      The order which status is changing
		 * @param string       $old_status Current order status
		 * @param string       $new_status New order status
		 *
		 */
		public function order_status_changed( $order, $old_status, $new_status ) {

			if ( is_numeric ( $order ) ) {
				$order = wc_get_order ( $order );
			}

			$allowed_status = apply_filters ( 'yith_ywgc_generate_gift_card_on_order_status',
				array( 'completed', 'processing' ) );

			if ( in_array ( $new_status, $allowed_status ) ) {
				$this->generate_gift_card_for_order ( $order );
			} elseif ( 'refunded' == $new_status ) {
				$this->change_gift_cards_status_on_order ( $order, YITH_YWGC ()->order_refunded_action );
			} elseif ( 'cancelled' == $new_status ) {
				$this->change_gift_cards_status_on_order ( $order, YITH_YWGC ()->order_cancelled_action );
			}
		}

		/**
		 * Generate the gift card code, if not yet generated
		 *
		 * @param WC_Order $order
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function generate_gift_card_for_order( $order ) {
			if ( is_numeric ( $order ) ) {
				$order = new WC_Order( $order );
			}

			if ( apply_filters ( 'yith_gift_cards_generate_on_order_completed', true, $order ) ) {

				$this->create_gift_cards_for_order ( $order );
			}
		}

		/**
		 * Create the gift cards for the order
		 *
		 * @param WC_Order $order
		 */
		public function create_gift_cards_for_order( $order ) {

			foreach ( $order->get_items ( 'line_item' ) as $order_item_id => $order_item_data ) {

				$product_id = $order_item_data["product_id"];
				$product    = wc_get_product ( $product_id );

				//  skip all item that belong to product other than the gift card type
				if ( ! $product instanceof WC_Product_Gift_Card ) {
					continue;
				}

				//  Check if current product, of type gift card, has a previous gift card
				// code before creating another
                if ( $gift_ids = ywgc_get_order_item_giftcards ( $order_item_id ) ) {
                    continue;
                }

                if ( ! apply_filters ( 'yith_ywgc_create_gift_card_for_order_item', true, $order, $order_item_id, $order_item_data ) ) {
                    continue;
                }

                $is_postdated = true == wc_get_order_item_meta ( $order_item_id, '_ywgc_postdated', true );
                if ( $is_postdated ) {
                    $delivery_date = wc_get_order_item_meta ( $order_item_id, '_ywgc_delivery_date', true );
                }

                $is_product_as_present = wc_get_order_item_meta ( $order_item_id, '_ywgc_product_as_present', true );
                $present_product_id    = 0;
                $present_variation_id  = 0;

                if ( $is_product_as_present ) {
                    $present_product_id   = wc_get_order_item_meta ( $order_item_id, '_ywgc_present_product_id', true );
                    $present_variation_id = wc_get_order_item_meta ( $order_item_id, '_ywgc_present_variation_id', true );
                }

                $order_id = yit_get_order_id ( $order );

                $line_subtotal     = apply_filters ( 'yith_ywgc_line_subtotal', $order_item_data["line_subtotal"], $order_item_data, $order_id, $order_item_id );
                $line_subtotal_tax = apply_filters ( 'yith_ywgc_line_subtotal_tax', $order_item_data["line_subtotal_tax"], $order_item_data, $order_id, $order_item_id );

                //  Generate as many gift card code as the quantity bought
                $quantity      = $order_item_data["qty"];
                $single_amount = (float) ( $line_subtotal / $quantity );
                $single_tax    = (float) ( $line_subtotal_tax / $quantity );

                $new_ids = array();

                //$order_currency = yit_get_prop ( $order, '_order_currency' );
                $order_currency = version_compare( WC()->version, '3.0', '<' ) ? $order->get_order_currency() : $order->get_currency();

                $product_id       = wc_get_order_item_meta ( $order_item_id, '_ywgc_product_id' );
                $amount           = wc_get_order_item_meta ( $order_item_id, '_ywgc_amount' );
                $is_manual_amount = wc_get_order_item_meta ( $order_item_id, '_ywgc_is_manual_amount' );
                $is_digital       = wc_get_order_item_meta ( $order_item_id, '_ywgc_is_digital' );

				if ( $is_digital ) {
					$recipients        = wc_get_order_item_meta ( $order_item_id, '_ywgc_recipients' );
					$recipient_count   = count ( $recipients );
					$sender            = wc_get_order_item_meta ( $order_item_id, '_ywgc_sender_name' );
					$recipient_name    = wc_get_order_item_meta ( $order_item_id, '_ywgc_recipient_name' );
					$message           = wc_get_order_item_meta ( $order_item_id, '_ywgc_message' );
					$has_custom_design = wc_get_order_item_meta ( $order_item_id, '_ywgc_has_custom_design' );
					$design_type       = wc_get_order_item_meta ( $order_item_id, '_ywgc_design_type' );
					$postdated         = wc_get_order_item_meta ( $order_item_id, '_ywgc_postdated' );
					$delivery_date     = wc_get_order_item_meta ( $order_item_id, '_ywgc_delivery_date' );

				}

				for ( $i = 0; $i < $quantity; $i ++ ) {

					//  Generate a gift card post type and save it
					$gift_card = new YWGC_Gift_Card_Premium();

					$gift_card->product_id       = $product_id;
					$gift_card->order_id         = $order_id;
					$gift_card->is_digital       = $is_digital;
					$gift_card->is_manual_amount = $is_manual_amount;

					$gift_card->product_as_present = $is_product_as_present;
					if ( $is_product_as_present ) {
						$gift_card->present_product_id   = $present_product_id;
						$gift_card->present_variation_id = $present_variation_id;
					}

					if ( $gift_card->is_digital ) {
						$gift_card->sender_name        = $sender;
						$gift_card->recipient_name     = $recipient_name;
						$gift_card->message            = $message;
						$gift_card->postdated_delivery = $is_postdated;
						if ( $is_postdated ) {
							$gift_card->delivery_date = $delivery_date;
						}

						$gift_card->has_custom_design = $has_custom_design;
						$gift_card->design_type       = $design_type;

						if ( $has_custom_design ) {
							$gift_card->design = wc_get_order_item_meta ( $order_item_id, '_ywgc_design' );
						}

						$gift_card->postdated_delivery = $postdated;
						if ( $postdated ) {
							$gift_card->delivery_date = $delivery_date;
						}

						/**
						 * If the user entered several recipient email addresses, one gift card
						 * for every recipient will be created and it will be the unique recipient for
						 * that email. If only one, or none if allowed, recipient email address was entered
						 * then create '$quantity' specular gift cards
						 */
						if ( ( $recipient_count == 1 ) && ! empty( $recipients[0] ) ) {
							$gift_card->recipient = $recipients[0];
						} elseif ( ( $recipient_count > 1 ) && ! empty( $recipients[ $i ] ) ) {
							$gift_card->recipient = $recipients[ $i ];
						} else {
							/**
							 * Set the customer as the recipient of the gift card
							 *
							 */
							$gift_card->recipient = apply_filters ( 'yith_ywgc_set_default_gift_card_recipient', yit_get_prop($order, 'billing_email') );
						}
					}

					if ( ! $gift_card->is_digital && YITH_YWGC ()->enable_pre_printed ) {
						$gift_card->set_as_pre_printed ();
					} else {
						$attempts = 100;
						do {
							$code       = YITH_YWGC ()->generate_gift_card_code ();
							$check_code = get_page_by_title ( $code, OBJECT, YWGC_CUSTOM_POST_TYPE_NAME );

							if ( ! $check_code ) {
								$gift_card->gift_card_number = $code;
								break;
							}
							$attempts --;
						} while ( $attempts > 0 );

						if ( ! $attempts ) {
							//  Unable to find a unique code, the gift card need a manual code entered
							$gift_card->set_as_code_not_valid ();
						}
					}

					$gift_card->total_amount = $single_amount + $single_tax;
					$gift_card->update_balance ( $gift_card->total_amount );
					$gift_card->version  = YITH_YWGC_VERSION;
					$gift_card->currency = $order_currency;

					try {
						$usage_expiration      = get_option ( 'ywgc_usage_expiration', 0 );
						$date_format = apply_filters('yith_wcgc_date_format','Y-m-d');
						$start_usage_date      = $gift_card->postdated_delivery ? $gift_card->delivery_date : date ( $date_format );
						$d                     = DateTime::createFromFormat ( $date_format, $start_usage_date );
						$gift_card->expiration = $usage_expiration ? strtotime ( "+$usage_expiration month", $d->getTimestamp () ) : 0;
					} catch ( Exception $e ) {
						error_log ( 'An error occurred setting the expiration date for gift card: ' . $gift_card->gift_card_number );
					}

					$gift_card->save ();

					//  Save the gift card id
					$new_ids[] = $gift_card->ID;

					//  ...and send it now if it's not postdated
					if ( ! $is_postdated ) {

						YITH_YWGC_Emails::get_instance ()->send_gift_card_email ( $gift_card );
					}
				}

				// save gift card Post ids on order item
				ywgc_set_order_item_giftcards ( $order_item_id, $new_ids );

			}
		}


		/**
		 * The order is set to completed
		 *
		 * @param WC_Order $order
		 * @param string   $action
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function change_gift_cards_status_on_order( $order, $action ) {

			if ( 'nothing' == $action ) {
				return;
			}

			foreach ( $order->get_items () as $item_id => $item ) {
				$ids = ywgc_get_order_item_giftcards ( $item_id );

				if ( $ids ) {
					foreach ( $ids as $gift_id ) {

						$gift_card = new YWGC_Gift_Card_Premium( array( 'ID' => $gift_id ) );

						if ( ! $gift_card->exists () ) {
							continue;
						}

						if ( 'dismiss' == $action ) {
							$gift_card->set_dismissed_status ();
						} elseif ( 'disable' == $action ) {

							$gift_card->set_enabled_status ( false );
						}
					}
				}
			}
		}

		public function show_tax_class_for_gift_cards() {
			echo '<script>
                jQuery("select#_tax_status").closest(".options_group").addClass("show_if_gift-card");
            </script>';
		}
	}
}
