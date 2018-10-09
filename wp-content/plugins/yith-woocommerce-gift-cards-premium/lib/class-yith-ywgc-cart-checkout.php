<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YITH_YWGC_Cart_Checkout' ) ) {

	/**
	 *
	 * @class   YITH_YWGC_Cart_Checkout
	 *
	 * @since   1.0.0
	 * @author  Lorenzo Giuffrida
	 */
	class YITH_YWGC_Cart_Checkout {

		const ORDER_GIFT_CARDS = '_ywgc_applied_gift_cards';
		const ORDER_GIFT_CARDS_TOTAL = '_ywgc_applied_gift_cards_totals';

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
			if ( is_null( self::$instance ) ) {
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

			$this->includes();
			$this->init_hooks();
		}

		public function includes() {

		}

		public function init_hooks() {

			/**
			 * set the price when a gift card product is added to the cart
			 */
			add_filter( 'woocommerce_add_cart_item', array(
				$this,
				'set_price_in_cart'
			), 10, 2 );

			add_filter( 'woocommerce_get_cart_item_from_session', array(
				$this,
				'get_cart_item_from_session'
			), 10, 2 );

			/**
			 *  Let the user to edit the gift card content
			 */
			add_action( 'wp_ajax_edit_gift_card', array(
				$this,
				'edit_gift_card_callback'
			) );

			if ( version_compare( WC()->version, '2.7', '<' ) ) {
				add_action( 'woocommerce_add_order_item_meta', array(
					$this,
					'append_gift_card_data_to_order_item'
				), 10, 2 );
			} else {
				add_action( 'woocommerce_new_order_item', array(
					$this,
					'append_gift_card_data_to_new_order_item'
				), 10, 2 );
			}

			/**
			 * Custom add_to_cart handler for gift card product type
			 */
			add_action( 'woocommerce_add_to_cart_handler_gift-card', array(
				$this,
				'add_to_cart_handler'
			) );

			/**
			 * Show gift card details in cart page
			 */
			add_filter( 'woocommerce_get_item_data', array(
				$this,
				'show_gift_card_details_in_cart'
			), 10, 2 );

			/* Ajax action for applying a gift card to the cart */
			add_action( 'wp_ajax_ywgc_apply_gift_card_code', array(
				$this,
				'apply_gift_card_code_callback'
			) );
			add_action( 'wp_ajax_nopriv_ywgc_apply_gift_card_code', array(
				$this,
				'apply_gift_card_code_callback'
			) );

			/* Ajax action for applying a gift card to the cart */
			add_action( 'wp_ajax_ywgc_remove_gift_card_code', array(
				$this,
				'remove_gift_card_code_callback'
			) );
			add_action( 'wp_ajax_nopriv_ywgc_remove_gift_card_code', array(
				$this,
				'remove_gift_card_code_callback'
			) );
			/**
			 * Apply the discount to the cart using the gift cards submitted, is any exists.
			 */
			add_action( 'woocommerce_after_calculate_totals', array(
				$this,
				'apply_gift_cards_discount'
			), 20 );

			/*
			 * Compatibility with YITH WooCommerce Subscription Premium
			 */
			if( defined('YITH_YWSBS_PREMIUM') ){
				add_action( 'ywcsb_after_calculate_totals', array( $this, 'apply_gift_cards_discount_for_subscriptions' ), 10 );
			}

			/**
			 * Show gift card amount usage on cart totals - checkout page
			 */
			add_action( 'woocommerce_review_order_before_order_total', array(
				$this,
				'show_gift_card_amount_on_cart_totals'
			) );

			/**
			 * Show gift card amount usage on cart totals - cart page
			 */
			add_action( 'woocommerce_cart_totals_before_order_total', array(
				$this,
				'show_gift_card_amount_on_cart_totals'
			) );

			add_action( 'woocommerce_new_order', array(
				$this,
				'register_gift_cards_usage'
			) );

			add_filter( 'woocommerce_get_order_item_totals', array(
				$this,
				'show_gift_cards_total_applied_to_order'
			), 10, 2 );

			add_action( 'wp_loaded', array( //plugins_loaded //wp_loaded //woocommerce_init
				$this,
				'check_email_discount'
			) );

			add_filter( 'woocommerce_is_purchasable', array(
				$this,
				'gift_card_is_purchasable'
			), 10, 2 );

			// Customizing the image of the gif from the product image
            add_action( 'wp_ajax_nopriv_yith_wc_gift_card_ajax_replace_for_product_image', array(
            $this,
            'yith_wc_gift_card_ajax_replace_for_product_image'
            ) );

			add_action( 'wp_ajax_yith_wc_gift_card_ajax_replace_for_product_image', array(
				$this,
				'yith_wc_gift_card_ajax_replace_for_product_image'
			) );

		}

        // Customizing the image of the gif from the product image
		public function yith_wc_gift_card_ajax_replace_for_product_image () {

            $product_id = $_POST['product_id'];

            ?>

            <script>

                document.getElementById( "ywgc-main-image" ).src = "<?php echo wp_get_attachment_url( get_post_thumbnail_id( $product_id ) ); ?>";
				document.getElementById( "ywgc-template-design" ).value = "<?php echo wp_get_attachment_url( get_post_thumbnail_id( $product_id ) ); ?>";

            </script>

            <?php

            die();

        }

		/**
		 * Allow gift card as a present always, no matter if the gift card is set as purchasable or not
		 *
		 * @param bool       $purchasable
		 * @param WC_Product $product
		 *
		 * @return bool
		 */
		public function gift_card_is_purchasable( $purchasable, $product ) {

			if ( ( $product instanceof WC_Product_Gift_Card ) && ( yit_get_prop( $product, 'id' ) == YITH_YWGC()->default_gift_card_id ) ) {
				return true;
			}

			return $purchasable;
		}

		/**
		 * Show gift cards usage on order item totals
		 *
		 * @param array    $total_rows
		 * @param WC_Order $order
		 *
		 * @return array
		 */
		public function show_gift_cards_total_applied_to_order( $total_rows, $order ) {

            $gift_cards = yit_get_prop( $order, self::ORDER_GIFT_CARDS, true );
			if ( $gift_cards ) {
				$row_totals = $total_rows['order_total'];
				unset( $total_rows['order_total'] );

				$gift_cards_message = '';
				foreach ( $gift_cards as $code => $amount ) {
					$amount = apply_filters( 'yith_ywgc_gift_card_coupon_amount', $amount );
					$gift_cards_message .= "-" . wc_price( $amount ) . ' (' . $code . ')';
				}

				$total_rows['gift_cards'] = array(
					'label' => __( 'Gift cards:', 'yith-woocommerce-gift-cards' ),
					'value' => $gift_cards_message
				);

                $total_rows = apply_filters( 'ywgc_gift_card_thankyou_table_total_rows', $total_rows, $code );

				$total_rows['order_total'] = $row_totals;
			}

			return $total_rows;
		}

		/**
		 * Show gift card amount usage on cart totals
		 */
		public function show_gift_card_amount_on_cart_totals() {

			if ( isset( WC()->cart->applied_gift_cards ) ) {

				foreach ( WC()->cart->applied_gift_cards as $code ) :

					$label = apply_filters( 'yith_ywgc_cart_totals_gift_card_label', esc_html( __( 'Gift card:', 'yith-woocommerce-gift-cards' ) . ' ' . $code ), $code );
					$amount = isset( WC()->cart->applied_gift_cards_amounts[ $code ] ) ? - WC()->cart->applied_gift_cards_amounts[ $code ] : 0;
					$value = wc_price( $amount ) . ' <a href="' . esc_url( add_query_arg( 'remove_gift_card_code', urlencode( $code ),
							defined( 'WOOCOMMERCE_CHECKOUT' ) ? wc_get_checkout_url() : wc_get_cart_url() ) ) .
					         '" class="ywgc-remove-gift-card " data-gift-card-code="' . esc_attr( $code ) . '">' . apply_filters('ywgc_remove_gift_card_text',__( '[Remove]', 'yith-woocommerce-gift-cards' ) ) . '</a>';
					?>
					<tr class="ywgc-gift-card-applied">
						<th><?php echo $label; ?></th>
						<td><?php echo $value; ?></td>
					</tr>

                    <?php do_action('ywgc_gift_card_checkout_cart_table', $code, $amount ); ?>
                
				<?php endforeach;
			}
		}

		// Comparison function
		function cmp( $a, $b ) {
			if ( $a == $b ) {
				return 0;
			}

			return ( $a < $b ) ? - 1 : 1;
		}

		/**
		 * Apply a gift card discount to current cart
		 *
		 * @param string $code
		 */
		protected function add_gift_card_code_to_session( $code ) {
			$applied_gift_cards = $this->get_gift_cards_from_session();
			if ( ! in_array( $code, $applied_gift_cards ) ) {
				$applied_gift_cards[] = $code;
				WC()->session->set( 'applied_gift_cards', $applied_gift_cards );
			}
		}

		/**
		 * Remove a gift card discount from current cart
		 *
		 * @param string $code
		 */
		protected function remove_gift_card_code_from_session( $code ) {
			$applied_gift_cards = $this->get_gift_cards_from_session();

			if ( ( $key = array_search( $code, $applied_gift_cards ) ) !== false ) {
				unset( $applied_gift_cards[ $key ] );
			}

			WC()->session->set( 'applied_gift_cards', $applied_gift_cards );
		}

		private function get_gift_cards_from_session() {
			$value = array();

			if ( isset( WC()->session ) ) {
				$value = WC()->session->get( 'applied_gift_cards', array() );
			}

			return $value;
		}

		private function empty_gift_cards_session() {
			if ( isset( WC()->session ) ) {
				WC()->session->__unset( 'applied_gift_cards' );
			}
		}

		/**
		 * Apply the gift cards discount to the cart
		 *
		 * @param WC_Cart $cart
		 *
		 */
		public function apply_gift_cards_discount( $cart ) {

			$cart->applied_gift_cards         = array();
			$cart->applied_gift_cards_amounts = array();

			$gift_card_codes = $this->get_gift_cards_from_session();
			if ( $gift_card_codes ) {

				$cart_total = version_compare(WC()->version, '3.2.0', '<') ? $cart->total : $cart->get_total('edit');

				$gift_card_amounts = array();
				foreach ( $gift_card_codes as $code ) {
					/** @var YWGC_Gift_Card_Premium|YITH_YWGC_Gift_Card $gift_card */
					$gift_card = YITH_YWGC()->get_gift_card_by_code( $code );

					if ( YITH_YWGC()->check_gift_card( $gift_card, true ) ) {
						$gift_card_amounts[ $code ] = apply_filters( 'yith_ywgc_gift_card_coupon_amount',
							$gift_card->get_balance(),
							$gift_card );
					} else {
						$this->remove_gift_card_code_from_session( $code );
						wc_print_notices();
					}
				}

				uasort( $gift_card_amounts, array( $this, 'cmp' ) );

				foreach ( $gift_card_amounts as $code => $amount ) {

					$cart->applied_gift_cards[] = $code;

					if ( ( $cart_total + $cart->shipping_total > 0 ) && ( $amount > 0 ) ) {

						$discount = min( $amount, $cart_total );

						$residue = $cart_total - $discount;


						if( $residue > 0 ){
							if( ( $cart->shipping_total - $residue) >= 0 ){
								if( apply_filters('yith_ywgc_detract_residue_to_shipping_total',false) ){
									$cart->shipping_total -= $residue;
								}
							}else{
								$residue = $residue - $cart->shipping_total;
							}

						}

						$cart->applied_gift_cards_amounts[ $code ] = $discount;
						$cart_total -= $discount;


					}
				}

				$cart->ywgc_original_cart_total = $cart->total;
				$cart->total                    = abs($cart_total);

			}
		}


		public function apply_gift_cards_discount_for_subscriptions( $order ){

		    $gift_totals = yit_get_prop( $order, '_ywgc_applied_gift_cards_totals', true );

		    $order->set_total( $order->get_total( 'edit')-$gift_totals );
		    $order->save();

        }

		/**
		 * Check if the gift card code provided is valid and store the amount for
		 * applying the discount to the cart
		 */
		public function apply_gift_card_code_callback() {

			check_ajax_referer( 'apply-gift-card', 'security' );
			$code = sanitize_text_field( $_POST['code'] );

			if ( ! empty( $code ) ) {
				$gift = YITH_YWGC()->get_gift_card_by_code( $code );
				if ( YITH_YWGC()->check_gift_card( $gift ) ) {
					$this->add_gift_card_code_to_session( $code );

					wc_add_notice( $gift->get_gift_card_message( YITH_YWGC_Gift_Card::GIFT_CARD_SUCCESS ) );
				}
				wc_print_notices();
			}

			die();
		}

		/**
		 * Check if the gift card code provided is valid and store the amount for
		 * applying the discount to the cart
		 */
		public function remove_gift_card_code_callback() {

			check_ajax_referer( 'apply-gift-card', 'security' );

			$code = sanitize_text_field( $_POST['code'] );

			if ( ! empty( $code ) ) {

				$gift = YITH_YWGC()->get_gift_card_by_code( $code );
				if ( YITH_YWGC()->check_gift_card( $gift, true ) ) {
					$this->remove_gift_card_code_from_session( $code );

					wc_add_notice( $gift->get_gift_card_message( YITH_YWGC_Gift_Card::GIFT_CARD_REMOVED ) );
				}
				wc_print_notices();
			}

			die();
		}

		/**
		 * Update the balance for all gift cards applied to an order
		 *
		 * @throws Exception
		 *
		 * @param int $order_id
		 */
		public function register_gift_cards_usage( $order_id ) {

			$applied_gift_cards = array();
			$applied_discount   = 0.00;

			if ( isset( WC()->cart->applied_gift_cards_amounts ) ) {
				foreach ( WC()->cart->applied_gift_cards_amounts as $code => $amount ) {
					$gift = YITH_YWGC()->get_gift_card_by_code( $code );

					if ( $gift->exists() ) {
						$amount                      = apply_filters( 'yith_ywgc_gift_card_amount_before_deduct', $amount );
						$applied_gift_cards[ $code ] = $amount;
						$applied_discount += $amount;

						$new_balance = max( 0.00, $gift->get_balance() - $amount );
						$gift->update_balance( $new_balance );
						$gift->register_order( $order_id );
						$this->notify_customer_if_gift_cards_used( $gift );
					}
				}
			}

			if ( $applied_gift_cards ) {
				$order = wc_get_order( $order_id );
				yit_save_prop( $order, self::ORDER_GIFT_CARDS, $applied_gift_cards );
				yit_save_prop( $order, self::ORDER_GIFT_CARDS_TOTAL, $applied_discount );
				$order->add_order_note( sprintf( __( 'Order paid with gift cards for a total amount of %s.', 'yith-woocommerce-gift-cards' ), wc_price( $applied_discount ) ) );
			}

			$this->empty_gift_cards_session();
		}

		/**
		 * Notify the customer if a gift cards he bought is used
		 *
		 * @param YITH_YWGC_Gift_Card $gift_card
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		private function notify_customer_if_gift_cards_used( $gift_card ) {

			if ( YITH_YWGC()->notify_customer ) {

				if ( $gift_card->exists() ) {
					WC()->mailer();
					do_action( 'ywgc-email-notify-customer_notification', $gift_card );
				}
			}
		}


		/**
		 * @param int    $order_id
		 * @param string $code
		 * @param float  $discount
		 *
		 *
		 * @return bool|mixed
		 */
		public function add_order_gift_card( $order_id, $code, $discount ) {

			// Store gift card
			$item_id = wc_add_order_item( $order_id,
				array(
					'order_item_name' => $code,
					'order_item_type' => 'yith-gift-card'
				) );

			if ( ! $item_id ) {
				return false;
			}

			wc_add_order_item_meta( $item_id, 'discount_amount', $discount );

			do_action( 'yith_ywgc_order_add_gift_card', $order_id, $item_id, $code, $discount );

			return $item_id;
		}


		/**
		 * Show gift card details in cart page
		 *
		 * @param array $item_data
		 * @param array $cart_item
		 *
		 * @return array
		 */
		public function show_gift_card_details_in_cart( $item_data, $cart_item ) {
			if ( ! YITH_YWGC()->show_details_in_cart ) {
				return $item_data;
			}

			if ( ! empty( $cart_item['ywgc_recipient_name'] ) ) {
				$value                            = $cart_item['ywgc_recipient_name'];
				$item_data['ywgc_recipient_name'] = array(
					'key'     => __( 'Recipient name', 'yith-woocommerce-gift-cards' ),
					'name'    => __( 'Recipient name', 'yith-woocommerce-gift-cards' ),
					'display' => $value,
					'value'   => $value,
				);
			}

			if ( ! empty( $cart_item['ywgc_recipients'] ) ) {
				$value = is_array( $cart_item['ywgc_recipients'] ) ? implode( ', ', $cart_item['ywgc_recipients'] ) : $cart_item['ywgc_recipients'];
				if ( ! $value ) {
					$value = __( 'Your billing email address', 'yith-woocommerce-gift-cards' );
				}

				$item_data['ywgc_recipients'] = array(
					'key'     => __( 'Recipient email', 'yith-woocommerce-gift-cards' ),
					'name'    => __( 'Recipient email', 'yith-woocommerce-gift-cards' ),
					'display' => $value,
					'value'   => $value,
				);
			}

			return $item_data;
		}

		/**
		 * Build cart item meta to pass to add_to_cart when adding a gift card to the cart
		 * @since 1.5.0
		 */
		public function build_cart_item_data() {

			$cart_item_data = array();

			$product_as_present = isset( $_POST["ywgc-as-present"] ) && ( 1 == $_POST["ywgc-as-present"] );

			/**
			 * Check if the current gift card has a manually entered amount set
			 */
			$ywgc_is_manual_amount = isset( $_REQUEST['ywgc-manual-amount'] ) &&
			                         ( floatval( $_REQUEST['ywgc-manual-amount'] ) > 0 ) &&
			                         ( ! isset( $_REQUEST['gift_amounts'] ) || ( "-1" == $_REQUEST['gift_amounts'] ) );
			$ywgc_is_manual_amount = wc_format_decimal ( $ywgc_is_manual_amount );

			/**
			 * Check if the current gift card has a prefixed amount set
			 */
			$ywgc_is_preset_amount = ! $ywgc_is_manual_amount &&
			                         isset( $_REQUEST['gift_amounts'] ) &&
			                         ( floatval( $_REQUEST['gift_amounts'] ) > 0 );
			$ywgc_is_preset_amount = wc_format_decimal ( $ywgc_is_preset_amount );

			/**
			 * Neither manual or fixed? Something wrong happened!
			 */
			if ( ! $product_as_present && ! $ywgc_is_manual_amount && ! $ywgc_is_preset_amount && apply_filters( 'yith_ywgc_allow_zero_gift_cards', true ) ) {
				wp_die( __( 'The gift card has invalid amount', 'yith-woocommerce-gift-cards' ) );
			}

			/**
			 * Check if it is a physical gift card
			 */
			$ywgc_is_physical = isset( $_REQUEST['ywgc-is-physical'] ) && $_REQUEST['ywgc-is-physical'];
			if ( $ywgc_is_physical ) {

				/**
				 * Retrieve sender name
				 */
				$sender_name = isset( $_REQUEST['ywgc-sender-name'] ) ? $_REQUEST['ywgc-sender-name'] : '';

				/**
				 * Recipient name
				 */
				$recipient_name = isset( $_REQUEST['ywgc-recipient-name'] ) ? $_REQUEST['ywgc-recipient-name'] : '';

				/**
				 * Retrieve the sender message
				 */
				$sender_message = isset( $_REQUEST['ywgc-edit-message'] ) ? $_REQUEST['ywgc-edit-message'] : '';

			}

			/**
			 * Check if it is a digital gift card
			 */
			$ywgc_is_digital = isset( $_REQUEST['ywgc-is-digital'] ) && $_REQUEST['ywgc-is-digital'];
			if ( $ywgc_is_digital ) {

				/**
				 * Retrieve gift card recipient
				 */
				$recipients = isset( $_REQUEST['ywgc-recipient-email'] ) ? $_REQUEST['ywgc-recipient-email'] : '';

				/**
				 * Retrieve sender name
				 */
				$sender_name = isset( $_REQUEST['ywgc-sender-name'] ) ? $_REQUEST['ywgc-sender-name'] : '';

				/**
				 * Recipient name
				 */
				$recipient_name = isset( $_REQUEST['ywgc-recipient-name'] ) ? $_REQUEST['ywgc-recipient-name'] : '';

				/**
				 * Retrieve the sender message
				 */
				$sender_message = isset( $_REQUEST['ywgc-edit-message'] ) ? $_REQUEST['ywgc-edit-message'] : '';

				/**
				 * Gift card should be delivered on a specific date?
				 */
				$postdated = isset( $_REQUEST['ywgc-postdated'] ) ? true : false;
				if ( $postdated ) {
					/**
					 * Retrieve the facultative delivery date
					 */
					//todo use the web site date format
					$delivery_date = isset( $_REQUEST['ywgc-delivery-date'] ) ? $_REQUEST['ywgc-delivery-date'] : '';
				}

				$gift_card_design = - 1;
				$design_type      = isset( $_POST['ywgc-design-type'] ) ? $_POST['ywgc-design-type'] : 'default';

				if ( 'custom' == $design_type ) {
					/**
					 * The user has uploaded a file
					 */
					if ( isset( $_FILES["ywgc-upload-picture"] ) ) {
						$custom_image = $_FILES["ywgc-upload-picture"];
						if ( isset( $custom_image["tmp_name"] ) && ( 0 == $custom_image["error"] ) ) {
							$gift_card_design = $this->save_uploaded_file( $custom_image );
						}
					}
				} else if ( 'template' == $design_type ) {
					if ( isset( $_POST['ywgc-template-design'] ) && is_numeric( $_POST['ywgc-template-design'] ) ) {
						$gift_card_design = intval( $_POST['ywgc-template-design'] );
					}

				}
				else if ( 'product_image' == $design_type )
					if ( isset( $_POST['ywgc-template-design'] )  ) {
						$gift_card_design = "-yith_wc_gift_card_premium_separator_ywgc_template_design-" . $_POST['ywgc-template-design'];
					}

			}

			if ( $product_as_present ) {
				$cart_item_data['ywgc_product_id'] = YITH_YWGC()->default_gift_card_id;

				$present_product_id   = $_POST["add-to-cart"];
				$present_variation_id = 0;

				if ( isset( $_POST["variation_id"] ) ) {
					$present_variation_id = $_POST["variation_id"];
				}

				$product = $present_variation_id ? wc_get_product( $present_variation_id ) : wc_get_product( $present_product_id );

				$ywgc_amount = $product->get_price();
                $ywgc_amount = apply_filters( 'yith_ywgc_submitting_as_present_amount', $ywgc_amount );

				$cart_item_data['ywgc_product_as_present']   = $product_as_present;
				$cart_item_data['ywgc_present_product_id']   = $present_product_id;
				$cart_item_data['ywgc_present_variation_id'] = $present_variation_id;

			} else {
				$cart_item_data['ywgc_product_id'] = absint( $_POST['add-to-cart'] );

				/**
				 * Set the gift card amount
				 */
				if ( $ywgc_is_manual_amount ) {
//					$ywgc_amount = number_format( doubleval( $_REQUEST['ywgc-manual-amount']), wc_get_price_decimals(), '.', '' );
					$ywgc_amount = $_REQUEST['ywgc-manual-amount'];
					$ywgc_amount = apply_filters( 'yith_ywgc_submitting_manual_amount', $ywgc_amount );
				} else {
					$ywgc_amount = $_REQUEST['gift_amounts'];
                    $ywgc_amount = apply_filters( 'yith_ywgc_submitting_select_amount', $ywgc_amount );
				}
			}

			$cart_item_data['ywgc_amount']           = $ywgc_amount;
			$cart_item_data['ywgc_is_manual_amount'] = $ywgc_is_manual_amount;
			$cart_item_data['ywgc_is_digital']       = $ywgc_is_digital;
			$cart_item_data['ywgc_is_physical']       = $ywgc_is_physical;

			/**
			 * Retrieve the gift card recipient, if digital
			 */
			if ( $ywgc_is_digital ) {
				$cart_item_data['ywgc_recipients']     = $recipients;
				$cart_item_data['ywgc_sender_name']    = $sender_name;
				$cart_item_data['ywgc_recipient_name'] = $recipient_name;
				$cart_item_data['ywgc_message']        = $sender_message;
				$cart_item_data['ywgc_postdated']      = $postdated;

				if ( $postdated ) {
					$cart_item_data['ywgc_delivery_date'] = $delivery_date;
				}


				$cart_item_data['ywgc_design_type']       = $design_type;
				$cart_item_data['ywgc_has_custom_design'] = $gift_card_design != - 1;
				if ( $gift_card_design ) {
					$cart_item_data['ywgc_design'] = $gift_card_design;
				}

			}

			if ( $ywgc_is_physical ) {
				$cart_item_data['ywgc_recipient_name'] = $recipient_name;
				$cart_item_data['ywgc_sender_name']    = $sender_name;
				$cart_item_data['ywgc_message']        = $sender_message;
			}


			return $cart_item_data;
		}

		/**
		 * Custom add_to_cart handler for gift card product type
		 */
		public function add_to_cart_handler() {

			$quantity = isset( $_REQUEST['quantity'] ) ? intval( $_REQUEST['quantity'] ) : 1;

			$item_data  = $this->build_cart_item_data();
			$product_id = $item_data['ywgc_product_id'];

			if ( ! $product_id ) {
				wc_add_notice( __( 'An error occurred while adding the product to the cart.', 'yith-woocommerce-gift-cards' ), 'error' );

				return false;
			}

//			$recipient_count = 1;
			$added_to_cart = false;


			if ( $item_data['ywgc_is_digital'] ) {

				$recipients = $item_data['ywgc_recipients'];
				/**
				 * Check if all mandatory fields are filled or throw an error
				 */
				if ( YITH_YWGC()->mandatory_recipient && ! count( $recipients ) ) {
					wc_add_notice( __( 'Add a valid email for the recipient', 'yith-woocommerce-gift-cards' ), 'error' );

					return false;
				}

				/**
				 * Validate all email addresses submitted
				 */
				$email_error = '';
				if ( YITH_YWGC()->mandatory_recipient && $recipients ) {
					foreach ( $recipients as $recipient ) {

						if ( YITH_YWGC()->mandatory_recipient && empty( $recipient ) ) {
							wc_add_notice( __( 'The recipient(s) email address is mandatory', 'yith-woocommerce-gift-cards' ), 'error' );

							return false;
						}

						if ( $recipient && ! filter_var( $recipient, FILTER_VALIDATE_EMAIL ) ) {
							$email_error .= '<br>' . $recipient;
						}
					}

					if ( $email_error ) {
						wc_add_notice( __( 'Email address not valid, please check the following: ', 'yith-woocommerce-gift-cards' ) . $email_error, 'error' );

						return false;
					}
				}

				/** The user can purchase 1 gift card with multiple recipient emails or [quantity] gift card for the same user.
				 * It's not possible to mix both, purchasing multiple instance of gift card with multiple recipients
				 * */
				$recipient_count = count( $item_data['ywgc_recipients'] );
				$quantity        = ( $recipient_count > 1 ) ? $recipient_count : ( isset( $_REQUEST['quantity'] ) ? intval( $_REQUEST['quantity'] ) : 1 );

				if ( $recipient_count > 1 ) {
					$item_data_to_card = $item_data;

					for ( $i = 0; $i < $recipient_count; $i++ ) {

						$item_data_to_card['ywgc_recipients'] = array( $item_data['ywgc_recipients'][$i] );
						$item_data_to_card['ywgc_recipient_name'] = $item_data['ywgc_recipient_name'][$i];

						$added_to_cart = WC()->cart->add_to_cart( $product_id, 1, 0, array(), $item_data_to_card );
					}

				} else {
                    $item_data['ywgc_recipient_name'] = is_array($item_data['ywgc_recipient_name']) ? $item_data['ywgc_recipient_name'][0] : $item_data['ywgc_recipient_name'];
					$added_to_cart = WC()->cart->add_to_cart( $product_id, $quantity, 0, array(), $item_data );

				}

			}else if ( $item_data['ywgc_is_physical'] ) {
					/** The user can purchase 1 gift card with multiple recipient names or [quantity] gift card for the same user.
					 * It's not possible to mix both, purchasing multiple instance of gift card with multiple recipients
					 * */

					$recipient_name_count = count( $item_data['ywgc_recipient_name'] );
					$quantity        = ( $recipient_name_count > 1 ) ? $recipient_name_count : ( isset( $_REQUEST['quantity'] ) ? intval( $_REQUEST['quantity'] ) : 1 );

					if ( $recipient_name_count > 1 ) {
						$item_data_to_card = $item_data;

						for ( $i = 0; $i < $recipient_name_count; $i++ ) {

							$item_data_to_card['ywgc_recipient_name'] = $item_data['ywgc_recipient_name'][$i];

							$added_to_cart = WC()->cart->add_to_cart( $product_id, 1, 0, array(), $item_data_to_card );
						}

					} else {
						$item_data['ywgc_recipient_name'] = $item_data['ywgc_recipient_name'][0];
						$added_to_cart = WC()->cart->add_to_cart( $product_id, $quantity, 0, array(), $item_data );

					}

				}
				else {
					$quantity      = isset( $_REQUEST['quantity'] ) ? intval( $_REQUEST['quantity'] ) : 1;
					$added_to_cart = WC()->cart->add_to_cart( $product_id, $quantity, 0, array(), $item_data );
				}

			if ( $added_to_cart ) {
				$this->show_cart_message_on_added_product( $product_id, $quantity );
			}


			// If we added the product to the cart we can now optionally do a redirect.
			if ( wc_notice_count( 'error' ) === 0 ) {

				$url = '';
				// If has custom URL redirect there
				if ( $url = apply_filters( 'woocommerce_add_to_cart_redirect', $url ) ) {
					wp_safe_redirect( $url );
					exit;
				} elseif ( get_option( 'woocommerce_cart_redirect_after_add' ) === 'yes' ) {
					if ( function_exists( 'wc_get_cart_url' ) ) {
						wp_safe_redirect( wc_get_cart_url() );
					} else {
						wp_safe_redirect( WC()->cart->get_cart_url() );
					}
					exit;
				}
			}
		}

		public function show_cart_message_on_added_product( $product_id, $quantity = 1 ) {
			//  From WC 2.6.0 the parameter format in wc_add_to_cart_message changed
			$gt_255 = version_compare( WC()->version, '2.5.5', '>' );
			$param  = $gt_255 ? array( $product_id => $quantity ) : $product_id;
			wc_add_to_cart_message( $param, true );
		}

		/**
		 * Set the real amount for the gift card product
		 *
		 * @param array $cart_item
		 *
		 * @since 1.5.0
		 * @return mixed
		 */
		public function set_price_in_cart( $cart_item ) {
			if ( isset( $cart_item['data'] ) ) {
				if ( $cart_item['data'] instanceof WC_Product_Gift_Card ) {

					yit_set_prop( $cart_item['data'], 'price', $cart_item['ywgc_amount'] );
				}
			}

			return $cart_item;
		}

		/**
		 * Update cart item when retrieving cart from session
		 *
		 * @param $session_data mixed Session data to add to cart
		 * @param $values       mixed Values stored in session
		 *
		 * @return mixed Session data
		 * @since 1.5.0
		 */
		public function get_cart_item_from_session( $session_data, $values ) {

			if ( isset( $values['ywgc_product_id'] ) && $values['ywgc_product_id'] ) {

				$session_data['ywgc_product_id']       = isset( $values['ywgc_product_id'] ) ? $values['ywgc_product_id'] : '';
				$session_data['ywgc_amount']           = isset( $values['ywgc_amount'] ) ? $values['ywgc_amount'] : '';
				$session_data['ywgc_is_manual_amount'] = isset( $values['ywgc_is_manual_amount'] ) ? $values['ywgc_is_manual_amount'] : false;
				$session_data['ywgc_is_digital']       = isset( $values['ywgc_is_digital'] ) ? $values['ywgc_is_digital'] : false;

				if ( $session_data['ywgc_is_digital'] ) {
					$session_data['ywgc_recipients']     = isset( $values['ywgc_recipients'] ) ? $values['ywgc_recipients'] : '';
					$session_data['ywgc_sender_name']    = isset( $values['ywgc_sender_name'] ) ? $values['ywgc_sender_name'] : '';
					$session_data['ywgc_recipient_name'] = isset( $values['ywgc_recipient_name'] ) ? $values['ywgc_recipient_name'] : '';
					$session_data['ywgc_message']        = isset( $values['ywgc_message'] ) ? $values['ywgc_message'] : '';

					$session_data['ywgc_has_custom_design'] = isset( $values['ywgc_has_custom_design'] ) ? $values['ywgc_has_custom_design'] : false;
					$session_data['ywgc_design_type']       = isset( $values['ywgc_design_type'] ) ? $values['ywgc_design_type'] : '';
					if ( $session_data['ywgc_has_custom_design'] ) {
						$session_data['ywgc_design'] = isset( $values['ywgc_design'] ) ? $values['ywgc_design'] : '';
					}

					$session_data['ywgc_postdated'] = isset( $values['ywgc_postdated'] ) ? $values['ywgc_postdated'] : false;
					if ( $session_data['ywgc_postdated'] ) {
						$session_data['ywgc_delivery_date'] = isset( $values['ywgc_delivery_date'] ) ? $values['ywgc_delivery_date'] : false;
					}
				}

				if ( isset( $values['ywgc_amount'] ) ) {
					$product_price = apply_filters( 'yith_ywgc_set_cart_item_price', $values['ywgc_amount'], $values );
					yit_set_prop( $session_data['data'], 'price', $product_price );
				}
			}

			return $session_data;
		}

		/**
		 * move an uploaded file into a persistent folder with a unique name
		 *
		 * @param string $image uploaded image
		 *
		 * @return string   real path of the uploaded image
		 */
		public
		function save_uploaded_file(
			$image
		) {
			// Create folders for storing documents
			$date     = getdate();
			$folder   = sprintf( "%s/%s", $date["year"], $date["mon"] );
			$filename = $image["name"];

			while ( true ) {

				$relative_path = sprintf( "%s/%s", $folder, $filename );
				$dir_path      = sprintf( "%s/%s", YITH_YWGC_SAVE_DIR, $folder );
				$full_path     = sprintf( "%s/%s", YITH_YWGC_SAVE_DIR, $relative_path );

				if ( ! file_exists( $full_path ) ) {
					if ( ! file_exists( $dir_path ) ) {
						wp_mkdir_p( $dir_path );
					}

					move_uploaded_file( $image["tmp_name"], $full_path );

					return $relative_path;
				} else {

					$unique_id = rand();

					$name_without_ext = pathinfo( $filename, PATHINFO_FILENAME );
					$ext              = pathinfo( $filename, PATHINFO_EXTENSION );

					$filename = $name_without_ext . $unique_id . '.' . $ext;
				}
			}
		}

		/**
		 * Let the user to edit che gift card content
		 */
		public function edit_gift_card_callback() {
			//todo check it
			if ( ! YITH_YWGC()->allow_modification ) {
				return;
			}

			$order_item_id = intval( sanitize_text_field( $_POST['item_id'] ) );
			$gift_card_id  = intval( sanitize_text_field( $_POST['gift_card_id'] ) );
			$sender        = sanitize_text_field( $_POST['sender'] );
			$recipient     = sanitize_email( $_POST['recipient'] );
			$message       = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST['message'] ) ) );

			/** Retrieve the gift card content.
			 *  If a valid gift card was generated, the content to be edited is a post meta of the gift card.
			 *  In the opposite case all the data are order item meta
			 */
			$item_gift_card_ids = ywgc_get_order_item_giftcards( $order_item_id );

			if ( in_array( $gift_card_id, $item_gift_card_ids ) ) {
				//  The gift card exists, edit it as custom post type
				$curr_card = new YWGC_Gift_Card_Premium( array( 'ID' => $gift_card_id ) );
				if ( $curr_card->exists() ) {

					//  Update current gift card content without saving, this card will be dismissed leaving a new gift card build as a clone from it
					$clone_it               = $recipient != $curr_card->recipient;
					$curr_card->sender_name = $sender;
					$curr_card->recipient   = $recipient;
					$curr_card->message     = $message;

					//  check if the recipient changes, if so, set_dismissed_status the current gift card and
					//  create a new one
					if ( $clone_it ) {

						//  The gift cards being changed will be closed and a new one will be created
						$new_gift = YITH_YWGC()->clone_gift_card( $curr_card );
						$new_gift->save();

						$curr_card->set_dismissed_status();

						//  assign the new gift card to the order item
						$item_gift_card_ids[] = $new_gift->ID;
						ywgc_set_order_item_giftcards( $order_item_id, $item_gift_card_ids );

						wp_send_json( array(
							"code"   => 2,
							"values" => array(
								"new_id" => $new_gift->ID,
							),
						) );
					} else {

						//  update the current gift card
						$curr_card->save();

						wp_send_json( array(
							"code" => 1,
						) );
					}
				}
			} else {
				//  a gift card custom post type object doesn't exists, edit order item meta values
				$meta = wc_get_order_item_meta( $order_item_id, YWGC_ORDER_ITEM_DATA );

				//edit order item meta
				$meta["sender"]    = $sender;
				$meta["recipient"] = $recipient;
				$meta["message"]   = $message;

				wc_update_order_item_meta( $order_item_id, YWGC_ORDER_ITEM_DATA, $meta );

				wp_send_json( array(
					"code" => 1,
				) );
			}

			wp_send_json( array(
				"code" => - 1,
			) );
		}

		/**
		 * @param                       $item_id
		 * @param WC_Order_Item_Product $item
		 */

		public function append_gift_card_data_to_new_order_item( $item_id, $item ) {

			if ( 'line_item' == $item->get_type() ) {

			    if ( isset( $item->legacy_values ) )
				    $this->append_gift_card_data_to_order_item( $item_id, $item->legacy_values );
			}
		}

		/**
		 * Append data to order item
		 *
		 * @param int   $item_id
		 * @param array $values
		 *
		 * @return mixed
		 * @author Lorenzo Giuffrida
		 * @since  1.5.0
		 */
		public function append_gift_card_data_to_order_item( $item_id, $values ) {

			if ( ! isset( $values['ywgc_product_id'] ) ) {
				return;
			}

			/**
			 * Store all fields related to Gift Cards
			 */

			foreach ( $values as $key => $value ) {
				if ( strpos( $key, 'ywgc_' ) === 0 ) {
					$meta_key = '_' . $key;
					wc_update_order_item_meta( $item_id, $meta_key, $value );
				}
			}

			/**
			 * Store subtotal and subtotal taxes applied to the gift card
			 */
			wc_update_order_item_meta( $item_id, '_ywgc_subtotal', $values['line_subtotal'] );
			wc_update_order_item_meta( $item_id, '_ywgc_subtotal_tax', $values['line_subtotal_tax'] );

			/**
			 * Store the plugin version for future use
			 */
			wc_update_order_item_meta( $item_id, '_ywgc_version', YITH_YWGC_VERSION );

		}

		/**
		 * Manage the request from an email for a gift card code to be applied to the cart
		 *
		 */
		public function check_email_discount() {

		    $actions = array();

		    /**
		     *
		     * Old version support from version 1.8.6 on
             * after some versions we could remove the following way of retrieving the
             * 'add-discount' and 'verify-code'
             *
		     */

            /*************** START OLD VERSION VAR RETRIEVES **************/

            if ( isset( $_GET[ 'add-discount' ] ) &&
                isset( $_GET[ 'verify-code' ] ) )
                $actions = array(
                        'add_discount' => $_GET[ 'add-discount' ],
                        'verify_code' => $_GET[ 'verify-code' ],
                        'product_id' => $_GET[ YWGC_ACTION_PRODUCT_ID ],
                );

            /*************** END OLD VERSION VAR RETRIEVES **************/

			if ( isset( $_GET[ YWGC_ACTION_ADD_DISCOUNT_TO_CART ] ) &&
			     isset( $_GET[ YWGC_ACTION_VERIFY_CODE ] )
			)
                $actions = array(
                    'add_discount' => $_GET[ YWGC_ACTION_ADD_DISCOUNT_TO_CART ],
                    'verify_code' => $_GET[ YWGC_ACTION_VERIFY_CODE ],
                    'product_id' => $_GET[ YWGC_ACTION_PRODUCT_ID ],
                );

			if ( is_array( $actions ) && ! empty( $actions ) )
			{

			    /**
                 *
                 * we add the product to the cart directly so in case the browser of the user never has used the site
                 * we create the cart session. If the admin does not want to add the product directly to the cart
                 * we remove it
                 *
                 */

                WC()->cart->add_to_cart( $actions[ 'product_id' ] );

                if ( get_option ( 'ywgc_gift_this_product_add_to_cart' ) != 'yes' ){

                    $items = WC()->cart->get_cart();
                    foreach ( $items as $cart_item_key => $values ) {
                        if ( $values[ 'product_id' ] == $actions[ 'product_id' ] )
                            WC()->cart->remove_cart_item( $cart_item_key );

                    }

                }

                if ( get_option ( 'ywgc_gift_this_product_apply_gift_card' ) != 'no' ){

                    $gift = YITH_YWGC()->get_gift_card_by_code( $actions[ 'add_discount' ] );

                    if ( $gift->can_be_used() ) {

                        //  Check the hash value and compare with the one provided
                        $hash_value = YITH_YWGC()->hash_gift_card( $gift );

                        if ( $hash_value == $actions[ 'verify_code' ] ) {
                            //  can add the discount to the cart
                            if ( YITH_YWGC()->check_gift_card( $gift ) ) {
                                $this->add_gift_card_code_to_session( $gift->get_code() );
                                wc_add_notice( $gift->get_gift_card_message( YITH_YWGC_Gift_Card::GIFT_CARD_SUCCESS ) );
                            }
                        }
                    }

                }

			}
		}
	}
}

YITH_YWGC_Cart_Checkout::get_instance();
