<?php
if ( ! defined ( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( ! class_exists ( 'YITH_YWGC_Frontend_Premium' ) ) {

	/**
	 *
	 * @class   YITH_YWGC_Frontend_Premium
	 *
	 * @since   1.0.0
	 * @author  Lorenzo Giuffrida
	 */
	class YITH_YWGC_Frontend_Premium extends YITH_YWGC_Frontend {

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
			parent::__construct ();

			/**
			 * Permit to enter free gift card amount
			 */
			add_action ( 'yith_gift_cards_template_after_amounts', array(
				$this,
				'show_free_amount_area'
			) );

			/**
			 * Let the user to enter a free amount instead of choosing from the select
			 */
			add_action ( 'yith_gift_cards_template_append_amount', array(
				$this,
				'add_manual_amount_item'
			) );

			/**
			 * Show a live preview of how the gift card will look like
			 */
			add_action ( 'yith_gift_cards_template_after_gift_card_form', array(
				$this,
				'show_gift_card_generator'
			), 1 );

            /**
             * Add the input hidden to set if gift this product automatically
             */
            add_action ( 'woocommerce_after_add_to_cart_button', array(
                $this,
                'show_give_as_present_link_simple'
            ) );

			/**
			 * Let the customer to use a product of type WC_Product_Simple  as source for a gift card
			 */
			add_action ( 'woocommerce_after_add_to_cart_button', array(
				$this,
				'yith_wcgc_display_input_hidden_gift_this_product'
			) );

			/**
			 * Let the customer to use a product of type WC_Product_Variable  as source for a gift card
			 */
			add_action ( 'woocommerce_after_variations_form', array(
				$this,
				'show_give_as_present_link_variable'
			), 99 );

            /**
             * Integration with yith woocommerce product bundle
             * Let the customer to use a product of type WC_Product_Yith_Bundle  as source for a gift card
             */
            add_action ( 'woocommerce_after_add_to_cart_button', array(
                $this,
                'show_give_as_present_link_product_bundle_product'
            ), 99 );

			add_action ( 'yith_ywgc_gift_card_preview_end', array(
				$this,
				'append_design_presets'
			) );

			add_action ( 'yith_ywgc_gift_card_preview_content', array(
				$this,
				'show_design_section'
			) );

			add_action ( 'yith_ywgc_gift_card_preview_content', array(
				$this,
				'show_gift_card_details'
			), 15 );

			add_action ( 'yith_ywgc_physical_gift_card_preview_content', array(
				$this,
				'show_physical_gift_card_details'
			), 15 );

			add_action ( 'yith_ywgc_generator_buttons_before', array(
				$this,
				'show_cancel_button_on_gift_this_product'
			) );

			add_action ( 'yith_ywgc_gift_card_preview', array(
				$this,
				'show_template_preview'
			) );

			/**
			 * Enqueue frontend scripts
			 */
			add_action ( 'wp_enqueue_scripts', array(
				$this,
				'enqueue_prettyphoto'
			), 99 );

			add_action ( 'yith_gift_card_amount_selection_last_option', array(
				$this,
				'show_manual_amount_area'
			) );

			add_action( 'woocommerce_product_query', array(
				$this,
				'hide_from_shop_page'
			) );

            //Register new endpoint to use for My Account page
			add_action( 'init', array(
				$this,
				'yith_ywgc_add_endpoint'
			) );

            //Add new query var
			add_filter( 'query_vars', array(
				$this,
				'yith_ywgc_gift_cards_query_vars'
			) );

			//Insert the new endpoint into the My Account menu
			add_filter( 'woocommerce_account_menu_items', array(
				$this,
				'yith_ywgc_add_gift_cards_link_my_account'
			) );

            //Add content to the new endpoint
			add_action( 'woocommerce_account_gift-cards_endpoint', array(
				$this,
				'yith_ywgc_gift_cards_content'
			) );

			add_action ( 'woocommerce_order_item_meta_start', array(
				$this,
				'show_gift_card_code_on_order_item'
			), 10, 3 );

			//Gift this product button on the shop loop
            add_action ( 'woocommerce_after_shop_loop_item', array(
                $this,
                'yiyh_wc_gift_card_woocommerce_after_shop_loop_item_call_back'
            ), 10 );


		}

        /**
         * Display the input hidden to set if gift this product automatically
         */
        public function yith_wcgc_display_input_hidden_gift_this_product() {

            global $product;
            echo "<input type='hidden' id='yith_wcyc_automatically_gift_this_product' value='" . get_post_meta( $product->get_id(), '_yith_wcgc_gift_this_product', true ) . "'>";
            
        }

        //Gift this product button on the shop loop
        public function yiyh_wc_gift_card_woocommerce_after_shop_loop_item_call_back() {

		    $product = apply_filters( 'yith_wc_gift_this_product_shop_page_product_filter', wc_get_product() );

            if ( $product && ( $product->get_type() != 'gift-card' ) && ( get_option( 'ywgc_permit_its_a_present_shop_page' ) == 'yes' ) && ( get_option( 'ywgc_permit_its_a_present' ) == 'yes' ) && apply_filters('yith_ywgc_give_product_as_present', true, $product ) ) {

                ?>

                <a href="<?php echo get_permalink( $product->get_id() ) . '?yith-gift-this-product-form=yes'; ?>"
                   class="<?php echo apply_filters( 'yith_wc_gift_this_product_shop_page_class_filter', 'button yith_wc_gift_this_product_shop_page_class' ); ?>" rel="nofollow"><?php echo apply_filters( 'yith_wcgc_gift_this_product_shop_page_button_label', _x( YITH_YWGC()->ywgc_gift_this_product_label, 'Gift this product from the shop page', 'yith-woocommerce-gift-cards' ) ); ?></a>

                <?php

            }

        }

		//Register new endpoint to use for My Account page
		public function yith_ywgc_add_endpoint() {
			add_rewrite_endpoint( 'gift-cards', EP_ROOT | EP_PAGES );
		}

        //Add new query var
		public function yith_ywgc_gift_cards_query_vars( $vars ) {
			$vars[] = 'gift-cards';

			return $vars;
		}

		//Insert the new endpoint into the My Account menu
		public function yith_ywgc_add_gift_cards_link_my_account( $items ) {


			$item_position = ( array_search( 'orders', array_keys( $items ) ) );

			$items_part1 = array_slice( $items, 0, $item_position + 1 );
			$items_part2 = array_slice( $items, $item_position );

			$items_part1['gift-cards'] = apply_filters( 'yith_wcgc_my_account_menu_item_title', __( 'Gift Cards', 'yith-woocommerce-gift-cards' ) );

			$items = array_merge( $items_part1, $items_part2 );


			return $items;
		}

		//Add content to the new endpoint
		public function yith_ywgc_gift_cards_content() {
			wc_get_template( 'myaccount/my-giftcards.php',
				'',
				'',
				trailingslashit( YITH_YWGC_TEMPLATES_DIR ) );
		}

		/**
		 * Hide the temporary gift card product from being shown on shop page
		 *
		 * @param WP_Query $query The current query
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function hide_from_shop_page( $query ) {

			if ( YITH_YWGC ()->default_gift_card_id ) {
				$query->set ( 'post__not_in', array( YITH_YWGC ()->default_gift_card_id ) );
			}
		}

		public function show_manual_amount_area( $product ) {
			//  Check if the current product permit free entered amount...
			if ( $this->is_manual_amount_allowed ( $product ) ) {
				echo '<option value="-1">' . apply_filters('yith_wcgc_manual_amount_option_text',__ ( "Manual amount", 'yith-woocommerce-gift-cards' )) . '</option>';
			}
		}

		public function show_template_preview( $product ) {
			YITH_YWGC ()->preview_digital_gift_cards ( $product );
		}

		/**
		 * Add frontend style to gift card product page
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		public function enqueue_frontend_script() {

			if ( is_product () || is_cart () || is_checkout () || apply_filters ( 'yith_ywgc_do_eneuque_frontend_scripts', false ) ) {
				wp_register_script ( 'accounting', WC ()->plugin_url () . yit_load_js_file ( '/assets/js/accounting/accounting.js' ), array( 'jquery' ), '0.4.2' );

				$frontend_deps = array(
					'jquery',
					'woocommerce',
					'jquery-ui-datepicker',
					'accounting',
				);

				if ( is_cart () ) {
					$frontend_deps[] = 'wc-cart';
				}
				//  register and enqueue ajax calls related script file
				wp_register_script ( "ywgc-frontend-script",
                    apply_filters( 'yith_ywgc_enqueue_script_source_path', YITH_YWGC_SCRIPT_URL . yit_load_js_file ( 'ywgc-frontend.js' ) ),
					$frontend_deps,
					YITH_YWGC_VERSION,
					true );

				global $post;

                $manual_minimal_amount = get_option ( 'ywgc_minimal_amount_gift_card' );
                if ( is_numeric( $manual_minimal_amount ) )
                    $manual_minimal_amount_error = __ ( "The minimal amount is " . $manual_minimal_amount, 'yith-woocommerce-gift-cards' );
                else
                    $manual_minimal_amount_error = '';


				wp_localize_script ( 'ywgc-frontend-script',
					'ywgc_data',
					array(
						'loader'                       => apply_filters ( 'yith_gift_cards_loader', YITH_YWGC_ASSETS_URL . '/images/loading.gif' ),
						'ajax_url'                     => admin_url ( 'admin-ajax.php' ),
						'currency'                     => get_woocommerce_currency_symbol (),
						'custom_image_max_size'        => YITH_YWGC ()->custom_image_max_size,
						'invalid_image_extension'      => __ ( "File format is not valid, select a jpg, jpeg, png, gif or bmp file", 'yith-woocommerce-gift-cards' ),
						'invalid_image_size'           => __ ( "The size fo the uploaded file exceeds the maximum allowed", 'yith-woocommerce-gift-cards' ) . " (" . YITH_YWGC()->custom_image_max_size . " MB)",
						'default_gift_card_image'      => YITH_YWGC ()->get_header_image ( is_product () ? wc_get_product ( $post ) : null ),
						'notify_custom_image_small'    => apply_filters ( "yith_gift_cards_custom_image_editor", __ ( '<b>Attention</b>: the <b>suggested minimum</b> size of the image is 490x195', 'yith-woocommerce-gift-cards' ) ),
						'multiple_recipient'           => __ ( "You have selected more than one recipient: a gift card for each recepient will be generated.", 'yith-woocommerce-gift-cards' ),
						'missing_scheduled_date'       => __ ( "Please enter a valid delivery date", 'yith-woocommerce-gift-cards' ),
						'wc_ajax_url'                  => WC_AJAX::get_endpoint ( "%%endpoint%%" ),
						'gift_card_nonce'              => wp_create_nonce ( 'apply-gift-card' ),
						// For accounting JS
						'currency_format'              => esc_attr ( str_replace ( array( '%1$s', '%2$s' ), array(
							'%s',
							'%v'
						), get_woocommerce_price_format () ) ),
						'mon_decimal_point'            => wc_get_price_decimal_separator (),
						'currency_format_num_decimals' => wc_get_price_decimals (),
						'currency_format_symbol'       => get_woocommerce_currency_symbol (),
						'currency_format_decimal_sep'  => esc_attr ( wc_get_price_decimal_separator () ),
						'currency_format_thousand_sep' => esc_attr ( wc_get_price_thousand_separator () ),
						'manual_amount_wrong_format'   => sprintf ( apply_filters( 'yith_ywgc_manual_amount_wrong_format_text',  _x ( "Please use only digits and the decimal separator '%1\$s'. Valid examples are '123', '123%1\$s9 and '123%1\$s99'.", 'yith-woocommerce-gift-cards' ),
							"Alert: the manual gift card field was filled with a wrong formatted value. It should contains only digits and a facultative decimal separator followed by one or two digits",
							'yith-woocommerce-gift-cards' ), wc_get_price_decimal_separator () ),
                        'manual_minimal_amount'        => $manual_minimal_amount,
                        'manual_minimal_amount_error'  => $manual_minimal_amount_error,
						'email_bad_format'             => __ ( "Please enter a valid email address", 'yith-woocommerce-gift-cards' ),
						'mandatory_email'              => YITH_YWGC ()->mandatory_recipient,
						'name'                         => __( "name", 'yith-woocommerce-gift-cards' ),
						'email'                        => __( "email", 'yith-woocommerce-gift-cards' ),
						'notice_target'                => apply_filters ( 'yith_ywgc_gift_card_notice_target', 'div.woocommerce' ),
						'add_gift_text'                => apply_filters ( 'yith_gift_card_layout_add_gift_button_text', __ ( "Add gift", 'yith-woocommerce-gift-cards' ) ),
					) );

				wp_enqueue_script ( "ywgc-frontend-script" );
			}
		}

		/**
		 * Add frontend style to gift card product page
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		public function enqueue_frontend_style() {

			if ( is_product () || is_cart () || is_checkout () || apply_filters ( 'yith_ywgc_do_eneuque_frontend_scripts', false ) ) {
				wp_enqueue_style ( 'ywgc-frontend',
					YITH_YWGC_ASSETS_URL . '/css/ywgc-frontend.css',
					array(),
					YITH_YWGC_VERSION );

				if ( apply_filters ( 'yith_ywgc_enqueue_jquery_ui_css', true ) ) {
					wp_enqueue_style ( 'jquery-ui-css',
						'//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css' );
				}
			}
		}

		/**
		 * Append the design preset to the gift card preview
		 *
		 * @param WC_Product $product
		 */
		public function append_design_presets( $product ) {

			$product_id = yit_get_product_id ( $product );
			if ( ! $this->can_show_template_design ( $product_id ) ) {
				return;
			}

			$categories = get_terms ( YWGC_CATEGORY_TAXONOMY, array( 'hide_empty' => 1 ) );

			$item_categories = array();
			foreach ( $categories as $item ) {
				$object_ids = get_objects_in_term ( $item->term_id, YWGC_CATEGORY_TAXONOMY );
				foreach ( $object_ids as $object_id ) {
					$item_categories[ $object_id ] = isset( $item_categories[ $object_id ] ) ? $item_categories[ $object_id ] . ' ywgc-category-' . $item->term_id : 'ywgc-category-' . $item->term_id;
				}
			}

			wc_get_template ( 'yith-gift-cards/gift-card-presets.php',
				array(
					'categories'      => $categories,
					'item_categories' => $item_categories
				),
				'',
				trailingslashit ( YITH_YWGC_TEMPLATES_DIR ) );
		}

		/**
		 * Show custom design area for the product
		 *
		 * @param WC_Product $product
		 */
		public function show_design_section( $product ) {

			$product_id = yit_get_product_id ( $product );

			// Load the template
			wc_get_template ( 'yith-gift-cards/gift-card-design.php',
				array(
					'allow_templates'       => $this->can_show_template_design ( $product_id ),
					'allow_customer_images' => YITH_YWGC ()->allow_custom_design,
					'allow_use_product_image' => YITH_YWGC ()->allow_product_as_present_product_image,
				),
				'',
				trailingslashit ( YITH_YWGC_TEMPLATES_DIR ) );
		}

		/**
		 * Show Gift Cards details
		 *
		 * @param WC_Product $product
		 */
		public function show_gift_card_details( $product ) {
			$product_id = yit_get_product_id ( $product );

			// Load the template
			wc_get_template ( 'yith-gift-cards/gift-card-details.php',
				array(
					'allow_templates'           => $this->can_show_template_design ( $product_id ),
					'allow_customer_images'     => YITH_YWGC ()->allow_custom_design,
					'allow_multiple_recipients' => YITH_YWGC ()->allow_multiple_recipients && ( $product instanceof WC_Product_Gift_Card ),
					'mandatory_recipient'       => YITH_YWGC ()->mandatory_recipient,
					'gift_this_product'         => ! ( $product instanceof WC_Product_Gift_Card ),
					'allow_send_later'          => YITH_YWGC ()->allow_send_later,
				),
				'',
				trailingslashit ( YITH_YWGC_TEMPLATES_DIR ) );
		}

		/**
		 * Show Physical Gift Cards details
		 *
		 * @param WC_Product $product
		 */
		public function show_physical_gift_card_details( $product ) {

			if ( YITH_YWGC ()->ywgc_physical_details )
			{

				$product_id = yit_get_product_id ( $product );

				// Load the template
				wc_get_template ( 'yith-gift-cards/physical-gift-card-details.php',
					array(
						'allow_templates'           => $this->can_show_template_design ( $product_id ),
						'allow_customer_images'     => YITH_YWGC ()->allow_custom_design,
						'allow_multiple_recipients' => YITH_YWGC ()->allow_multiple_recipients && ( $product instanceof WC_Product_Gift_Card ),
						'ywgc_physical_details_mandatory'       => YITH_YWGC ()->ywgc_physical_details_mandatory,
						'gift_this_product'         => ! ( $product instanceof WC_Product_Gift_Card ),
						'allow_send_later'          => YITH_YWGC ()->allow_send_later,
					),
					'',
					trailingslashit ( YITH_YWGC_TEMPLATES_DIR ) );

			}


		}

		public function show_cancel_button_on_gift_this_product( $product ) {

			if ( $product instanceof WC_Product_Gift_Card ) {
				return;
			}
			?>
			<button id="ywgc-cancel-gift-card"
			        class="button"><?php echo apply_filters( 'ywgc_cancel_gift_card_button_text',__( "Cancel", 'yith-woocommerce-gift-cards' )); ?></button>
			<?php
		}

		public function enqueue_prettyphoto() {

			if ( ! is_product () ) {
				return;
			}

			global $post;
			if ( ! apply_filters ( 'yith_ywgc_enqueue_pretty_photo', true, $post ) ) {
				return;
			}

			$product = wc_get_product ( $post );

			if ( ( YWGC_GIFT_CARD_PRODUCT_TYPE == $product->get_type () ) ||
			     YITH_YWGC ()->allow_product_as_present
			) {
				$assets_path = str_replace ( array( 'http:', 'https:' ), '', WC ()->plugin_url () ) . '/assets/';
				$suffix      = defined ( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_register_script ( 'prettyPhoto',
					$assets_path . 'js/prettyPhoto/jquery.prettyPhoto' . $suffix . '.js',
					array( 'jquery' ),
					'3.1.6',
					true );
				wp_register_script ( 'prettyPhoto-init',
					$assets_path . 'js/prettyPhoto/jquery.prettyPhoto.init' . $suffix . '.js',
					array(
						'jquery',
						'prettyPhoto'
					) );

				wp_enqueue_script ( 'prettyPhoto-init' );
				wp_enqueue_script ( 'prettyPhoto' );
				wp_enqueue_style ( 'woocommerce_prettyPhoto_css',
					$assets_path . 'css/prettyPhoto.css' );
			}
		}


		/**
		 * Show my gift cards status on myaccount page
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function show_my_gift_cards_table() {
			wc_get_template ( 'myaccount/my-giftcards.php',
				'',
				'',
				trailingslashit ( YITH_YWGC_TEMPLATES_DIR ) );
		}


		/**
		 * Let the user to edit the gift card
		 *
		 * @param $order_item_id
		 * @param $item
		 * @param $order
		 */
		public function edit_gift_card( $order_item_id, $item, $order ) {

			if ( ! YITH_YWGC ()->allow_modification ) {
				return;
			}

			//  Allow editing only on checkout or my orders pages
			if ( ! is_checkout () && ! is_account_page () ) {
				return;
			}

			$item_meta_array = $item["item_meta"];
			//  Check if current order item is a gift card
			if ( ! isset( $item_meta_array[ YWGC_ORDER_ITEM_DATA ] ) ) {

				return;
			}

			//  Retrieve the gift card content. If a valid gift card was generated, the content to be edited is a postmeta of the
			//  Gift card post type, else the content is still on the order_item_meta.
			$gift_cards = ywgc_get_order_item_giftcards ( $order_item_id );

			if ( $gift_cards ) {
				$_gift_card_id = is_array ( $gift_cards ) ? $gift_cards[0] : $gift_cards;

				//  edit values from a gift card object stored on the DB
				$gift_card = new YWGC_Gift_Card_Premium( array( 'ID' => $_gift_card_id ) );

			} else {
				//  edit the data stored as order item meta because the final gift card is not created yet
				$order_item_meta = $item_meta_array[ YWGC_ORDER_ITEM_DATA ];
				$order_item_meta = $order_item_meta[0];
				$order_item_meta = maybe_unserialize ( $order_item_meta );

				$gift_card = new YWGC_Gift_Card_Premium( $order_item_meta );
			}

			//  Check if the gift card still exists
			//todo do not block the editing on gift card that are not generated yet
			if ( ! $gift_card->exists () ) {
				//return;
			}

			//  There is nothing to edit for physical gift card product, only virtual gift cards
			//  can be edited

			if ( ! $gift_card->is_virtual () ) {
				return;
			}

			?>

			<div id="current-gift-card-<?php echo $order_item_id; ?>" class="ywgc-gift-card-content">
				<a href="#"
				   class="edit-details"><?php _e ( "See card details", 'yith-woocommerce-gift-cards' ); ?></a>

				<div class="ywgc-gift-card-details ywgc-hide">
					<h3><?php _e ( "Gift card details", 'yith-woocommerce-gift-cards' ); ?></h3>
					<fieldset class="ywgc-sender-details" style="border: none">
						<label><?php _e ( "Sender: ", 'yith-woocommerce-gift-cards' ); ?></label>
						<span class="ywgc-sender"><?php echo $gift_card->sender_name; ?></span>
					</fieldset>

					<fieldset class="ywgc-recipient-details" style="border: none">
						<label><?php _e ( "Recipient: ", 'yith-woocommerce-gift-cards' ); ?></label>
						<span class="ywgc-recipient"><?php echo $gift_card->recipient; ?></span>
					</fieldset>

					<fieldset class="ywgc-message-details" style="border: none">
						<label><?php _e ( "Message: ", 'yith-woocommerce-gift-cards' ); ?></label>
						<span class="ywgc-message"><?php echo $gift_card->message; ?></span>
					</fieldset>
					<button
						class="ywgc-do-edit btn btn-ghost"
						style="display: none;"><?php _e ( "Edit", 'yith-woocommerce-gift-cards' ); ?></button>
				</div>

				<div class="ywgc-gift-card-edit-details ywgc-hide" style="display: none">
					<h3><?php _e ( "Gift card details", 'yith-woocommerce-gift-cards' ); ?></h3>

					<form name="form-gift-card-<?php echo $gift_card->ID; ?>">
						<input type="hidden" name="ywgc-gift-card-id" value="<?php echo $gift_card->ID; ?>">
						<input type="hidden" name="ywgc-item-id" value="<?php echo $order_item_id; ?>">
						<fieldset>
							<label
								for="ywgc-edit-sender"><?php _e ( "Sender: ", 'yith-woocommerce-gift-cards' ); ?></label>
							<input type="text" name="ywgc-edit-sender" id="ywgc-edit-sender"
							       value="<?php echo $gift_card->sender_name; ?>">
						</fieldset>

						<fieldset>
							<label
								for="ywgc-edit-recipient"><?php _e ( "Recipient: ", 'yith-woocommerce-gift-cards' ); ?></label>
							<input type="email" name="ywgc-edit-recipient" id="ywgc-edit-recipient"
							       value="<?php echo $gift_card->recipient; ?>"">
						</fieldset>

						<fieldset>
							<label
								for="ywgc-edit-message"><?php _e ( "Message: ", 'yith-woocommerce-gift-cards' ); ?></label>
							<textarea name="ywgc-edit-message" id="ywgc-edit-message"
							          rows="5"><?php echo $gift_card->message; ?></textarea>
						</fieldset>
					</form>

					<button
						class="ywgc-apply-edit btn apply"><?php _e ( "Apply", 'yith-woocommerce-gift-cards' ); ?></button>
					<button
						class="ywgc-cancel-edit btn btn-ghost"><?php _e ( "Cancel", 'yith-woocommerce-gift-cards' ); ?></button>
				</div>
			</div>
			<?php
		}

		/**
		 * Let the customer to use a product of type WC_Product_Simple  as source for a gift card
		 */
		public function show_give_as_present_link_simple() {

            global $product;

			if ( ! YITH_YWGC ()->allow_product_as_present && get_post_meta( $product->get_id(), '_yith_wcgc_gift_this_product', true ) != 'yes' ) {
				return;
			}

			if ( $product instanceof WC_Product_Simple && apply_filters('yith_ywgc_give_product_as_present',true,$product) ) {
				// Load the template
				wc_get_template ( 'single-product/add-to-cart/give-product-as-present.php',
                    array(
                        'product'         => $product
                    ),
                    '',
                    trailingslashit ( YITH_YWGC_TEMPLATES_DIR ) );
			}
		}

		/**
		 * Let the customer to use a product of type WC_Product_Variable  as source for a gift card
		 */
		public function show_give_as_present_link_variable() {

            global $product;

		    if ( ! YITH_YWGC ()->allow_product_as_present && get_post_meta( $product->get_id(), '_yith_wcgc_gift_this_product', true ) != 'yes' ) {
				return;
			}

			if ( $product instanceof WC_Product_Variable && apply_filters('yith_ywgc_give_product_as_present',true,$product) )  {

                // Load the template
                wc_get_template ( 'single-product/add-to-cart/give-product-as-present.php',
                    array(
                        'product'         => $product
                    ),
                    '',
                    trailingslashit ( YITH_YWGC_TEMPLATES_DIR ) );
			}
		}

        /**
         * Integration with yith woocommerce product bundle
         * Let the customer to use a product of type WC_Product_Yith_Bundle  as source for a gift card
         */
        public function show_give_as_present_link_product_bundle_product() {

            global $product;

            if ( ! YITH_YWGC ()->allow_product_as_present && get_post_meta( $product->get_id(), '_yith_wcgc_gift_this_product', true ) != 'yes' ) {
                return;
            }


            if ( $product instanceof WC_Product_Yith_Bundle && apply_filters('yith_ywgc_give_product_as_present', true, $product ) ) {

                // Load the template
                wc_get_template ( 'single-product/add-to-cart/give-product-as-present.php',
                    array(
                        'product'         => $product
                    ),
                    '',
                    trailingslashit ( YITH_YWGC_TEMPLATES_DIR ) );
            }
        }

		/**
		 * Check if a gift card product avoid entering manual amount value
		 *
		 * @param WC_Product_Gift_Card $product
		 *
		 * @return bool
		 */
		public function is_manual_amount_allowed( $product ) {

			$manual_amount = $product->get_manual_amount_status ();

			//  if the gift card have specific manual entered amount behaviour, return that
			if ( "global" != $manual_amount ) {
				return "accept" == $manual_amount;
			}

			return YITH_YWGC ()->allow_manual_amount;
		}

		/**
		 * Show a live preview of how the gift card will look like
		 */
		public function show_gift_card_generator() {
			global $product;

			if ( ( $product instanceof WC_Product_Gift_Card ) && ! $product->is_virtual () ) {
				// Load the template
				wc_get_template ( 'yith-gift-cards/physical-gift-card-generator.php',
					array(
						'product' => $product,
					),
					'',
					trailingslashit ( YITH_YWGC_TEMPLATES_DIR ) );
				return;
			}

			if ( ( $product instanceof WC_Product_Gift_Card ) && ! $product->is_purchasable () ) {
				return;
			}

			// Load the template
			wc_get_template ( 'yith-gift-cards/gift-card-generator.php',
				array(
					'product' => $product,
				),
				'',
				trailingslashit ( YITH_YWGC_TEMPLATES_DIR ) );
		}

		/**
		 * Permit to enter free gift card amount
		 *
		 * @param WC_Product_Gift_Card $product
		 */
		public function show_free_amount_area( $product ) {
			if ( ! $this->is_manual_amount_allowed ( $product ) ) {
				return;
			}

			$amounts  = $product->get_amounts_to_be_shown ();
			$hide_css = count ( $amounts ) ? 'ywgc-hidden' : '';

			?>
			<input id="ywgc-manual-amount" name="ywgc-manual-amount"
			       class="ywgc-manual-amount <?php echo $hide_css; ?>" type="text"
			       placeholder="<?php echo apply_filters('yith_wcgc_manual_amount_input_placeholder',__ ( "Enter amount(Only digits)", 'yith-woocommerce-gift-cards' )); ?>">
			<?php
		}


		/**
		 * Retrieve if the templates design should be shown for the product
		 *
		 * @param int $product_id the product id
		 *
		 * @author Lorenzo Giuffrida
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function can_show_template_design( $product_id ) {
			$product = wc_get_product ( $product_id );

			if ( $product instanceof WC_Product_Gift_Card ) {
				if ( ! $this->is_template_design_allowed ( $product_id ) ) {
					return false;
				}
			} elseif ( ! YITH_YWGC ()->allow_template_design ) {
				return false;
			}

			//  If template design are allowed, show it (if there are at least one!)
			return $this->template_design_count ();
		}


		/**
		 * Retrieve the number of templates available
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function template_design_count() {
			global $wp_version;
			if ( version_compare ( $wp_version, '4.5', '<' ) ) {
				$media_terms = get_terms ( YWGC_CATEGORY_TAXONOMY, array( 'hide_empty' => 1 ) );
			} else {
				$media_terms = get_terms ( array( 'taxonomy' => YWGC_CATEGORY_TAXONOMY, 'hide_empty' => 1, 'hierarchical' => false) );
			}
			$ids = array();
			foreach ( $media_terms as $media_term ) {
				$ids[] = $media_term->term_id;
			}

			$template_ids = array_unique ( get_objects_in_term ( $ids, YWGC_CATEGORY_TAXONOMY ) );

			return count ( $template_ids );
		}

		/**
		 * Check if a gift card product permit to choose from a custom template design
		 *
		 * @param $product_id int the product id to check
		 *
		 * @return bool
		 */
		public function is_template_design_allowed( $product_id ) {
			$product        = new WC_Product_Gift_Card( $product_id );
			$show_templates = $product->get_design_status ();

			//  If the product have a custom status related to the use of template design, return that settings
			if ( "enabled" == $show_templates ) {
				return true;
			}

			if ( "disabled" == $show_templates ) {
				return false;
			}

			//  If there isn't a custom status, retrieve the global settings

			return YITH_YWGC ()->allow_template_design;
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
				<?php endif;			}
		}

	}
}
