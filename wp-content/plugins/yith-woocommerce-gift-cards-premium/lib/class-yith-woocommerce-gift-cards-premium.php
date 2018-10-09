<?php
if ( ! defined ( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( ! class_exists ( 'YITH_WooCommerce_Gift_Cards_Premium' ) ) {

	/**
	 *
	 * @class   YITH_WooCommerce_Gift_Cards_Premium
	 *
	 * @since   1.0.0
	 * @author  Lorenzo Giuffrida
	 */
	class YITH_WooCommerce_Gift_Cards_Premium extends YITH_WooCommerce_Gift_Cards {
		/**
		 * @var int The default product of type gift card
		 */
		public $default_gift_card_id = - 1;

		/**
		 * @var bool Let the user to enter manually the amount of the gift card
		 */
		public $allow_manual_amount = false;

		/**
		 * @var bool allow the customer to choose a product from the shop to be used as a present for the gift card
		 */
		public $allow_product_as_present = false;

		/**
		 * @var bool allow the customer to edit the content of a gift card
		 */
		public $allow_modification = false;

		/**
		 * @var bool let your customer to buy a digital card and send it later
		 */
		public $allow_send_later = false;

		/**
		 * @var bool notify the customer when a gift card he bought is used
		 */
		public $notify_customer = false;

		/**
		 * @var string the shop name
		 */
		public $shop_name;

		/**
		 * @var int limit the maximum size of custom image uploaded by the customer
		 */
		public $custom_image_max_size;

		/**
		 * @var string  the logo to be used on the gift card
		 */
		public $shop_logo_url;

		/**
		 * @var bool set if the shop logo should be shown inside the gift card template
		 */
		public $shop_logo_on_template = false;

		/**
		 * @var string the image url used as gift card header
		 */
		public $default_header_image_url;

		/**
		 * @var string the style to be used for the email
		 *
		 */
		public $template_style = 'style1';
		/**
		 * @var bool set if the admin should receive the email containing the gift card code in BCC
		 */
		public $blind_carbon_copy;

		/**
		 * @var bool enable the automatic discount when the customer click on the link in the email received
		 */
		public $automatic_discount = false;

		/**
		 * @var bool restrict the usage of the gift card to the recipient
		 */
		public $restricted_usage = false;

		/**
		 * @var bool allow to use a user picture as custom gift card design
		 */
		public $allow_custom_design = true;

		/**
		 * @var bool let the user to choose from some gift cards templates
		 */
		public $allow_template_design = false;

		/**
		 * @var bool
		 */
		public $allow_multiple_recipients = true;

		/**
		 * @var string action to perform on order cancelled
		 */
		public $order_cancelled_action = '';

		/**
		 * @var string action to perform on order refunded
		 */
		public $order_refunded_action = '';

		/**
		 * @var bool choose if the pre-printed mode is enabled for physical gift cards
		 */
		public $enable_pre_printed = false;

		/**
		 * @var bool Ask for the recipient email when adding a digital gift card to the cart
		 */
		public $mandatory_recipient = true;

		/**
		 * @var bool set if the gift card details should be shown on cart page
		 */
		public $show_details_in_cart = false;

		/**
		 * @var bool set if the image title should be shown on preset list
		 */
		public $show_preset_title = false;

		/**
		 * @var bool enable internal notes on admin side
		 */
		public $enable_internal_notes = false;

		/**
		 * @var string the pattern to use for generating gift card codes
		 */
		public $gift_card_code_pattern = '';

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

		public function includes() {

			parent::includes ();

			/**
			 * Include third-party integration classes
			 */

			//  YITH Dynamic Pricing
			defined ( 'YITH_YWDPD_VERSION' ) && require_once ( YITH_YWGC_DIR . 'lib/third-party/class-ywgc-dynamic-pricing.php' );

			//  YITH Points and Rewards
			defined ( 'YITH_YWPAR_VERSION' ) && require_once ( YITH_YWGC_DIR . 'lib/third-party/class-ywgc-points-and-rewards.php' );

			//  YITH Multi Vendor
			defined ( 'YITH_WPV_PREMIUM' ) && require_once ( YITH_YWGC_DIR . 'lib/third-party/class-ywgc-multi-vendor-module.php' );

			//  Aelia Currency Switcher
			class_exists ( 'WC_Aelia_CurrencySwitcher' ) && require_once ( YITH_YWGC_DIR . 'lib/third-party/class-ywgc-AeliaCS-module.php' );

			defined ( 'YITH_WCQV_PREMIUM' ) && require_once ( YITH_YWGC_DIR . 'lib/third-party/class-ywgc-general-integrations.php' );
			//  WPML
			global $woocommerce_wpml;
			if ( $woocommerce_wpml ) {
				require_once ( YITH_YWGC_DIR . 'lib/third-party/class-ywgc-wpml.php' );
			}
		}

		public function init_hooks() {

			parent::init_hooks ();

            /**
             * Including the GDRP
             */
            add_action( 'plugins_loaded', array( $this, 'load_privacy' ), 20 );

			if ( ! class_exists ( 'Emogrifier' ) ) {

				require_once ( WC ()->plugin_path () . '/includes/libraries/class-emogrifier.php' );
			}

			$this->register_custom_post_statuses ();

            /**
             * Add option to the admin product page to automatically gift the product
             */
            add_filter( 'product_type_options', array( $this, 'gift_this_product_product_type_options' ), 100, 1 );

            /**
             * Saving gift this product automatically
             */
            $save_product_meta_hook = version_compare( WC()->version, '3.0.0', '>=' ) ? 'woocommerce_admin_process_product_object' : 'woocommerce_process_product_meta';
            add_action( $save_product_meta_hook, array( $this, 'gift_this_product_woocommerce_process_product_meta' ) );

			/**
			 * Customize a gift card with data entered by the customer on product page
			 */
			add_filter ( 'yith_gift_cards_before_add_to_cart', array( $this, 'customize_card_before_add_to_cart' ) );

			/**
			 * Add an option to let the admin set the gift card as a physical good or digital goods
			 */
			add_filter ( 'product_type_options', array( $this, 'add_type_option' ) );

			/**
			 * When the default gift card image is changed from the plugin setting, update the product image
			 * of the default gift card
			 */
			add_action ( 'yit_panel_wc_after_update', array( $this, 'update_default_gift_card' ) );


			/**
			 * Append CSS for the email being sent to the customer
			 */
			add_action ( 'yith_gift_cards_template_before_add_to_cart_form', array( $this, 'append_css_files' ) );

			/**
			 * Set gift card expiration for gift card created manually
			 */
			add_action('save_post', array( $this,'set_expiration_date_for_gift_card_created_manually'),10,3 );
			/**
			 * Add taxonomy and assign it to gift card products
			 */
			add_action ( 'init', array(
				$this,
				'create_gift_cards_category'
			) );

			/**
			 * Set the manual amount status for gift cards that are linked to the global value
			 * */
			add_filter ( 'yith_gift_cards_is_manual_amount_enabled', array(
				$this,
				'is_manual_amount_enabled'
			), 10, 2 );

			add_filter ( 'yith_ywgc_get_product_instance', array(
				$this,
				'get_product_instance'
			), 10, 2 );
		}

        /**
         * Add option to the admin product page to automatically gift the product
         */
        public function gift_this_product_product_type_options( $options ) {

            $options[ 'yith_wcgc_gift_this_product' ] = array(
                'id'            => '_yith_wcgc_gift_this_product',
                'label'         => __( 'Gift this product automatically', 'yith-woocommerce-gift-cards' ),
                'description'   => __( 'Check this option if you want to automatically activate the option "gift this product".', 'yith-woocommerce-product-bundles' ),
                'default'       => 'no'
            );

            return $options;
        }

        /**
         * Saving gift this product automatically
         */
        public function gift_this_product_woocommerce_process_product_meta( $product )
        {

            if (!$product)
                return;

            $gift_this_product = isset($_POST['_yith_wcgc_gift_this_product']) ? 'yes' : 'no';

            yit_save_prop($product, '_yith_wcgc_gift_this_product', $gift_this_product, true);
        }

        /**
         * Including the GDRP
         */
        public function load_privacy() {

            if ( class_exists( 'YITH_Privacy_Plugin_Abstract' ) )
                require_once( YITH_YWGC_DIR . 'lib/class.yith-woocommerce-gift-cards-privacy.php' );

        }

		public function start() {
			//  Init the backend
			$this->backend = YITH_YWGC_Backend_Premium::get_instance ();

			//  Init the frontend
			$this->frontend = YITH_YWGC_Frontend_Premium::get_instance ();
		}

		// register new taxonomy which applies to attachments
		public function create_gift_cards_category() {

			$labels = array(
				'name'              => __('Gift Cards Categories','yith-woocommerce-gift-cards'),
				'singular_name'     => __('Gift Card Category','yith-woocommerce-gift-cards'),
				'search_items'      => __('Search Gift Card Categories','yith-woocommerce-gift-cards'),
				'all_items'         => __('All Gift Card Categories','yith-woocommerce-gift-cards'),
				'parent_item'       => __('Parent Gift Card Category','yith-woocommerce-gift-cards'),
				'parent_item_colon' => __('Parent Gift Card Category:','yith-woocommerce-gift-cards'),
				'edit_item'         => __('Edit Gift Card Category','yith-woocommerce-gift-cards'),
				'update_item'       => __('Update Gift Card Category','yith-woocommerce-gift-cards'),
				'add_new_item'      => __('Add New Gift Card Category','yith-woocommerce-gift-cards'),
				'new_item_name'     => __('New Gift Card Category Name','yith-woocommerce-gift-cards'),
				'menu_name'         => __('Gift Card Category','yith-woocommerce-gift-cards')
			);

			$args = array(
				'labels'            => $labels,
				'hierarchical'      => true,
				'query_var'         => true,
				'rewrite'           => true,
				'show_admin_column' => true,
				'show_ui'           => true,
				'public'            => true,
			);

			register_taxonomy ( YWGC_CATEGORY_TAXONOMY, 'attachment', $args );
		}


		/**
		 * Register all the custom post statuses of gift cards
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function register_custom_post_statuses() {

			register_post_status ( YWGC_Gift_Card_Premium::STATUS_DISABLED, array(
					'label'                     => __ ( 'Disabled', 'yith-woocommerce-gift-cards' ),
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop ( __ ( 'Disabled', 'yith-woocommerce-gift-cards' ) . '<span class="count"> (%s)</span>', __ ( 'Disabled', 'yith-woocommerce-gift-cards' ) . ' <span class="count"> (%s)</span>' ),
				)
			);

			register_post_status ( YWGC_Gift_Card_Premium::STATUS_DISMISSED, array(
					'label'                     => __ ( 'Dismissed', 'yith-woocommerce-gift-cards' ),
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop ( __ ( 'Dismissed', 'yith-woocommerce-gift-cards' ) . '<span class="count"> (%s)</span>', __ ( 'Dismissed', 'yith-woocommerce-gift-cards' ) . ' <span class="count"> (%s)</span>' ),
				)
			);

			register_post_status ( YWGC_Gift_Card_Premium::STATUS_CODE_NOT_VALID, array(
					'label'                     => __ ( 'Code not valid', 'yith-woocommerce-gift-cards' ),
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop ( __ ( 'Code not valid', 'yith-woocommerce-gift-cards' ) . '<span class="count"> (%s)</span>', __ ( 'Code not valid', 'yith-woocommerce-gift-cards' ) . ' <span class="count"> (%s)</span>' ),
				)
			);
		}


		/**
		 * Append CSS for the email being sent to the customer
		 */
		public function append_css_files() {
			YITH_YWGC ()->frontend->enqueue_frontend_style ();
		}


		/**
		 * When the default gift card image is changed from the plugin setting, update the product image
		 * of the default gift card
		 */
		public function update_default_gift_card() {
			if ( isset( $_POST["ywgc_gift_card_header_url-yith-attachment-id"] ) ) {
				yit_save_prop ( wc_get_product ( $this->default_gift_card_id ), "_thumbnail_id", $_POST["ywgc_gift_card_header_url-yith-attachment-id"] );
			}
		}

		/**
		 * Hash the gift card code so it could be used for security checks
		 *
		 * @param YWGC_Gift_Card_Premium $gift_card
		 *
		 * @return string
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function hash_gift_card( $gift_card ) {

			return hash ( 'md5', $gift_card->gift_card_number . $gift_card->ID );
		}


		/**
		 * Add an option to let the admin set the gift card as a physical good or digital goods.
		 *
		 * @param array $array
		 *
		 * @return mixed
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function add_type_option( $array ) {
			if ( isset( $array["virtual"] ) ) {
				$css_class     = $array["virtual"]["wrapper_class"];
				$add_css_class = 'show_if_gift-card';
				$class         = empty( $css_class ) ? $add_css_class : $css_class .= ' ' . $add_css_class;

				$array["virtual"]["wrapper_class"] = $class;
			}

			return $array;
		}

		/**
		 * Create a product of type gift card to be used as placeholder. Should not be visible on shop page.
		 */
		public function initialize_products() {
			//  Search for a product with meta YWGC_PRODUCT_PLACEHOLDER
			$this->default_gift_card_id = get_option ( YWGC_PRODUCT_PLACEHOLDER, - 1 );

			if ( - 1 == $this->default_gift_card_id ) {

				//  Create a default gift card product
				$args = array(
					'post_title'   => __ ( 'Gift card', 'yith-woocommerce-gift-cards' ),
					'post_name'    => __ ( 'gift_card', 'yith-woocommerce-gift-cards' ),
					'post_content' => __ ( 'This product has been automatically created by the plugin YITH Gift Cards.You must not edit it, or the plugin could not work properly', 'yith-woocommerce-gift-cards' ),
					'post_status'  => 'publish',
					'post_date'    => date ( 'Y-m-d H:i:s' ),
					'post_author'  => 0,
					'post_type'    => 'product',
				);

				$this->default_gift_card_id = wp_insert_post ( $args );
				update_option ( YWGC_PRODUCT_PLACEHOLDER, $this->default_gift_card_id );

				//  Create a taxonomy for products of type YWGC_GIFT_CARD_PRODUCT_TYPE and
				//  set the product created to the new taxonomy
				//  Create product type
				$term = wp_insert_term ( YWGC_GIFT_CARD_PRODUCT_TYPE, 'product_type' );

				$term_id = - 1;
				if ( $term instanceof WP_Error ) {
					$error_code = $term->get_error_code ();
					if ( "term_exists" == $error_code ) {
						$term_id = $term->get_error_data ( $error_code );
					}
				} else {
					$term_id = $term["term_id"];
				}

				if ( $term_id != - 1 ) {
					wp_set_object_terms ( $this->default_gift_card_id, $term_id, 'product_type' );
				} else {
					wp_die ( __ ( "An error occurred, you cannot use the plugin", 'yith-woocommerce-gift-cards' ) );
				}

				//  set this default gift card product as virtual
				$product = wc_get_product ( $this->default_gift_card_id );
				if ( $product ) {
					yit_save_prop ( $product, '_virtual', 'yes' );
				}
			}
		}

		/**
		 * Initialize plugin settings
		 */
		public function init_plugin() {
			$this->allow_manual_amount       = "yes" == get_option ( 'ywgc_permit_free_amount' );
			$this->allow_product_as_present  = "yes" == get_option ( 'ywgc_permit_its_a_present' );
            $this->allow_product_as_present_shop_page  = "yes" == get_option ( 'ywgc_permit_its_a_present_shop_page' );
			$this->allow_product_as_present_product_image  = "yes" == get_option ( 'ywgc_permit_its_a_present_product_image' );
			$this->ywgc_physical_details  = "yes" == get_option ( 'ywgc_physical_details' );
			$this->ywgc_physical_details_mandatory  = "yes" == get_option ( 'ywgc_physical_details_mandatory' );
			$this->allow_modification        = "yes" == get_option ( 'ywgc_permit_modification' );
			$this->allow_send_later          = "yes" == get_option ( 'ywgc_enable_send_later' );
			$this->notify_customer           = "yes" == get_option ( 'ywgc_notify_customer' );
			$this->blind_carbon_copy         = "yes" == get_option ( "ywgc_blind_carbon_copy" );
			$this->automatic_discount        = "yes" == get_option ( "ywgc_auto_discount" );
			$this->restricted_usage          = "yes" == get_option ( "ywgc_restricted_usage" );
			$this->allow_custom_design       = "yes" == get_option ( "ywgc_custom_design" );
			$this->allow_template_design     = "yes" == get_option ( "ywgc_template_design" );
			$this->allow_multiple_recipients = "yes" == get_option ( "ywgc_allow_multi_recipients" );
			$this->order_cancelled_action    = get_option ( "ywgc_order_cancelled_action", 'nothing' );
			$this->order_refunded_action     = get_option ( "ywgc_order_refunded_action", 'nothing' );
			$this->enable_pre_printed        = "yes" == get_option ( "ywgc_enable_pre_printed" );
			$this->shop_name                 = get_option ( 'ywgc_shop_name', '' );
			$this->custom_image_max_size     = get_option ( 'ywgc_custom_image_max_size', 1 );
			$this->shop_logo_url             = get_option ( "ywgc_shop_logo_url", YITH_YWGC_ASSETS_IMAGES_URL . 'default-giftcard-main-image.png' );
			$this->shop_logo_on_template     = "yes" == get_option ( "ywgc_shop_logo_on_gift_card" );
			$this->default_header_image_url  = get_option ( "ywgc_gift_card_header_url", YITH_YWGC_ASSETS_IMAGES_URL . 'default-giftcard-main-image.png' );
			$this->template_style            = get_option ( "ywgc_template_style", 'style1' );
			$this->mandatory_recipient       = "yes" == get_option ( 'ywgc_recipient_mandatory', 'no' );
			$this->show_details_in_cart      = "yes" == get_option ( 'ywgc_show_recipient_on_cart', 'no' );
			$this->show_preset_title         = "yes" == get_option ( 'ywgc_show_preset_title', 'no' );
			$this->gift_card_code_pattern    = get_option ( 'ywgc_code_pattern', '****-****-****-****' );
            $this->ywgc_minimal_amount_gift_card    = get_option ( 'ywgc_minimal_amount_gift_card' );
            $this->ywgc_gift_this_product_label    = ( empty( get_option ( 'ywgc_gift_this_product_label' ) ) ? 'Gift this product' : get_option ( 'ywgc_gift_this_product_label' ) );

			$this->initialize_products ();
		}

		/**
		 * Generate a new gift card code
		 *
		 * @return string
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function generate_gift_card_code() {

			//  Create a new gift card number
			$numeric_code     = (string) mt_rand ( 99999999, mt_getrandmax () );
			$numeric_code_len = strlen ( $numeric_code );

			$code     = strtoupper ( sha1 ( uniqid ( mt_rand () ) ) );
			$code_len = strlen ( $code );

			$pattern     = $this->gift_card_code_pattern == '' ? '****-****-****-****' : $this->gift_card_code_pattern;
			$pattern_len = strlen ( $pattern );

			for ( $i = 0; $i < $pattern_len; $i ++ ) {

				if ( '*' == $pattern[ $i ] ) {
					//  replace all '*'s with one letter from the unique $code generated
					$pattern[ $i ] = $code[ $i % $code_len ];
				} elseif ( 'D' == $pattern[ $i ] ) {
					//  replace all 'D's with one digit from the unique integer $numeric_code generated
					$pattern[ $i ] = $numeric_code[ $i % $numeric_code_len ];
				}
			}

			return $pattern;
		}

		/**
		 * Retrieve if the gift cards should be updated on order refunded
		 *
		 * @return bool
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function change_status_on_refund() {
			return $this->disable_on_refund () || $this->dismiss_on_refund ();
		}

		/**
		 * Retrieve if the gift cards should be updated on order cancelled
		 *
		 * @return bool
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function change_status_on_cancelled() {
			return $this->disable_on_cancelled () || $this->dismiss_on_cancelled ();
		}

		/**
		 * Retrieve if a gift card should be set as dismissed if an order change its status
		 * to refunded
		 *
		 * @return bool
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function dismiss_on_refund() {
			return 'dismiss' == $this->order_refunded_action;
		}

		/**
		 * Retrieve if a gift card should be set as disabled if an order change its status
		 * to refunded
		 *
		 * @return bool
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function disable_on_refund() {
			return 'disable' == $this->order_refunded_action;
		}

		/**
		 * Retrieve if a gift card should be set as dismissed if an order change its status
		 * to cancelled
		 *
		 * @return bool
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function dismiss_on_cancelled() {
			return 'dismiss' == $this->order_cancelled_action;
		}

		/**
		 * Retrieve if a gift card should be set as disabled if an order change its status
		 * to cancelled
		 *
		 * @return bool
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function disable_on_cancelled() {
			return 'disable' == $this->order_cancelled_action;
		}

        public function on_plugin_init() {
            parent::on_plugin_init();
            $is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;
            if ( is_admin() && !$is_ajax ) {
                $this->init_metabox();
            }
        }

		public function init_metabox() {
			$args = array(
				'label'    => __ ( 'Gift card detail', 'yith-woocommerce-gift-cards' ),
				'pages'    => YWGC_CUSTOM_POST_TYPE_NAME,   //or array( 'post-type1', 'post-type2')
				'context'  => 'normal', //('normal', 'advanced', or 'side')
				'priority' => 'high',
				'tabs'     => array(
					'General' => array( //tab
						'label'  => __ ( 'General', 'yith-woocommerce-gift-cards' ),
						'fields' => apply_filters ( 'yith_ywgc_gift_card_instance_metabox_custom_fields',
							array(

								YITH_YWGC_Gift_Card::META_AMOUNT_TOTAL  => array(
									'label'   => __ ( 'Purchased amount', 'yith-woocommerce-gift-cards' ),
									'desc'    => __ ( 'The amount purchased by the customer.', 'yith-woocommerce-gift-cards' ),
									'type'    => 'text',
									'private' => false,
									'std'     => ''
								),
								YITH_YWGC_Gift_Card::META_BALANCE_TOTAL => array(
									'label'   => __ ( 'Current balance', 'yith-woocommerce-gift-cards' ),
									'desc'    => __ ( 'The current amount available for the customer.', 'yith-woocommerce-gift-cards' ),
									'type'    => 'text',
									'private' => false,
									'std'     => ''
								),
								'_ywgc_is_digital'                      => array(
									'label'   => __ ( 'Digital', 'yith-woocommerce-gift-cards' ),
									'desc'    => __ ( 'Choose whether the gift card will be sent via email or like a physical product.', 'yith-woocommerce-gift-cards' ),
									'type'    => 'checkbox',
									'private' => false,
									'std'     => ''
								),
								'_ywgc_sender_name'                     => array(
									'label'   => __ ( 'Sender name', 'yith-woocommerce-gift-cards' ),
									'desc'    => __ ( 'The sender name, if any, of the digital gift card.', 'yith-woocommerce-gift-cards' ),
									'type'    => 'text',
									'private' => false,
									'std'     => '',
									'css'     => 'width: 80px;',
									'deps'    => array(
										'ids'    => '_ywgc_is_digital',
										'values' => 'yes',
									),
								),
								'_ywgc_recipient'                       => array(
									'label'   => __ ( 'Recipient email', 'yith-woocommerce-gift-cards' ),
									'desc'    => __ ( 'The recipient email address of the digital gift card.', 'yith-woocommerce-gift-cards' ),
									'type'    => 'text',
									'private' => false,
									'std'     => '',
									'deps'    => array(
										'ids'    => '_ywgc_is_digital',
										'values' => 'yes',
									),
								),
								'_ywgc_recipient_name'                       => array(
									'label'   => __ ( 'Recipient name', 'yith-woocommerce-gift-cards' ),
									'desc'    => __ ( 'The recipient name of the digital gift card.', 'yith-woocommerce-gift-cards' ),
									'type'    => 'text',
									'private' => false,
									'std'     => '',
									'deps'    => array(
										'ids'    => '_ywgc_is_digital',
										'values' => 'yes',
									),
								),
								'_ywgc_message'                         => array(
									'label'   => __ ( 'Message', 'yith-woocommerce-gift-cards' ),
									'desc'    => __ ( 'The message attached to the gift card.', 'yith-woocommerce-gift-cards' ),
									'type'    => 'textarea',
									'private' => false,
									'std'     => '',
									'deps'    => array(
										'ids'    => '_ywgc_is_digital',
										'values' => 'yes',
									),
								),
								'_ywgc_delivery_date'                   => array(
									'label'   => __ ( 'Delivery date', 'yith-woocommerce-gift-cards' ),
									'desc'    => __ ( 'The date when the digital gift card will be sent to the recipient. Date format is yyyy-mm-dd', 'yith-woocommerce-gift-cards' ),
									'type'    => 'text',
									'private' => false,
									'std'     => '',
									'deps'    => array(
										'ids'    => '_ywgc_is_digital',
										'values' => 'yes',
									),
								),
                                '_ywgc_internal_notes'                   =>array(
                                    'label'   => __ ( 'Internal notes', 'yith-woocommerce-gift-cards' ),
                                    'desc'    => __ ( 'Enter your notes, it will be visible only on admin side.', 'yith-woocommerce-gift-cards' ),
                                    'type'    => 'textarea',
                                    'private' => false,
                                    'std'     => '',
                                )
							) ),
					),
				)
			);

			$metabox = YIT_Metabox ( 'yit-metabox-id' );
			$metabox->init ( $args );

		}

		/**
		 * Register the custom post type
		 */
		public function init_post_type() {
			$args = array(
				'labels'        => array(
					'name'               => _x ( 'Gift Cards', 'post type general name', 'yith-woocommerce-gift-cards' ),
					'singular_name'      => _x ( 'Gift Card', 'post type singular name', 'yith-woocommerce-gift-cards' ),
					'menu_name'          => _x ( 'Gift Cards', 'admin menu', 'yith-woocommerce-gift-cards' ),
					'name_admin_bar'     => _x ( 'Gift Card', 'add new on admin bar', 'yith-woocommerce-gift-cards' ),
					'add_new'            => _x ( 'Add New', 'admin menu item', 'yith-woocommerce-gift-cards' ),
					'add_new_item'       => __ ( 'Add New Gift Card', 'yith-woocommerce-gift-cards' ),
					'new_item'           => __ ( 'New Gift Card', 'yith-woocommerce-gift-cards' ),
					'edit_item'          => __ ( 'Edit Gift Card', 'yith-woocommerce-gift-cards' ),
					'view_item'          => __ ( 'View Gift Card', 'yith-woocommerce-gift-cards' ),
					'all_items'          => __ ( 'All gift cards', 'yith-woocommerce-gift-cards' ),
					'search_items'       => __ ( 'Search gift cards', 'yith-woocommerce-gift-cards' ),
					'parent_item_colon'  => __ ( 'Parent gift cards:', 'yith-woocommerce-gift-cards' ),
					'not_found'          => __ ( 'No gift cards found.', 'yith-woocommerce-gift-cards' ),
					'not_found_in_trash' => __ ( 'No gift cards found in Trash.', 'yith-woocommerce-gift-cards' )
				),
				'label'         => __ ( 'Gift Cards', 'yith-woocommerce-gift-cards' ),
				'description'   => __ ( 'Gift Cards', 'yith-woocommerce-gift-cards' ),
				// Features this CPT supports in Post Editor
				'supports'      => array( 'title' ),
				'hierarchical'  => false,
                'capability_type'     => 'product',
                'public'        => false,
				'show_ui'       => true,
//				'show_in_admin_bar'   => true,
//				'show_in_menu'        => true,
				'menu_position' => 9,
				'can_export'    => false,
				'has_archive'   => false,
				'menu_icon'     => 'dashicons-clipboard',
				'query_var'     => false,
			);

			// Registering your Custom Post Type
			register_post_type ( YWGC_CUSTOM_POST_TYPE_NAME, $args );


		}


		/**
		 * Retrieve a gift card product instance from the gift card code
		 *
		 * @param $code string the card code to search for
		 *
		 * @return YWGC_Gift_Card_Premium
		 */
		public function get_gift_card_by_code( $code ) {

			return new YWGC_Gift_Card_Premium( array( 'gift_card_number' => $code ) );
		}


		/**
		 * Retrieve the real picture to be used on the gift card preview
		 *
		 * @param YWGC_Gift_Card_Premium $object
		 *
		 * @return string
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 *
		 */
		public function get_gift_card_header_url( $object ) {
			//  Choose a valid gift card image header
			if ( $object->has_custom_design ) {
				//  There is a custom header image or a template chosen by the customer?
				if ( is_numeric ( $object->design ) ) {
					//  a template was chosen, retrieve the picture associated
					$header_image_url = yith_get_attachment_image_url( $object->design, apply_filters( 'ywgc_email_image_size', 'full' ) );
				} else {
					$header_image_url = YITH_YWGC_SAVE_URL . $object->design;
				}
			} else {
				if ( ! empty( $this->gift_card_header_url ) ) {
					$header_image_url = $this->gift_card_header_url;
				} else {
					$header_image_url = YITH_YWGC_ASSETS_IMAGES_URL . 'default-giftcard-main-image.png';
				}
			}

			return $header_image_url;
		}

		/**
		 * Retrieve the image to be used as a main image for the gift card
		 *
		 * @param WC_product $product
		 *
		 * @return string
		 */
		public function get_header_image_for_product( $product ) {
			$header_image_url = '';

			if ( $product ) {

				$product_id = yit_get_product_id ( $product );
				if ( $product instanceof WC_Product_Gift_Card ) {
					$header_image_url = $product->get_manual_header_image ();
				}

				if ( ( '' == $header_image_url ) && has_post_thumbnail ( $product_id ) ) {
					$image            = wp_get_attachment_image_src ( get_post_thumbnail_id ( $product_id ), apply_filters( 'ywgc_email_image_size', 'full' ) );
					$header_image_url = $image[0];
				}
			}
			return $header_image_url;
		}

		public function get_default_header_image() {
			return $this->default_header_image_url ? $this->default_header_image_url : YITH_YWGC_ASSETS_IMAGES_URL . 'default-giftcard-main-image.png';
		}

		/**
		 * Retrieve the default image, configured from the plugin settings, to be used as gift card header image
		 *
		 * @param YWGC_Gift_Card_Premium|WC_Product $obj
		 *
		 * @return mixed|string|void
		 */
		public function get_header_image( $obj = null ) {

			$header_image_url = '';
			if ( $obj instanceof YWGC_Gift_Card_Premium ) {

				if ( $obj->has_custom_design ) {
					//  There is a custom header image or a template chosen by the customer?
					if ( is_numeric ( $obj->design ) ) {
						//  a template was chosen, retrieve the picture associated
						$header_image_url = yith_get_attachment_image_url ( $obj->design, apply_filters( 'ywgc_email_image_size', 'full' ) );

					} else {
						$header_image_url = YITH_YWGC_SAVE_URL . $obj->design;

					}
				} else {
					$product          = wc_get_product ( $obj->product_id );
					$header_image_url = $this->get_header_image_for_product ( $product );

				}
			}

            if ( is_object( $obj ) ){
                if ( get_class( $obj ) == 'WC_Product_Gift_Card' ){

                    $image_id = $obj->get_manual_header_image ( $obj->get_id(), 'id' );
                    $header_image_url = wp_get_attachment_url( $image_id );

                }
            }

			if ( ! $header_image_url ) {
				$header_image_url = $this->get_default_header_image ();

			}

			return $header_image_url;
		}

		/**
		 * Output a gift cards template filled with real data or with sample data to start editing it
		 * on product page
		 *
		 * @param WC_Product|YWGC_Gift_Card_Premium $object
		 * @param string                            $context
		 */
		public function preview_digital_gift_cards( $object, $context = 'shop' ) {

			if ( $object instanceof WC_Product ) {
				$product_type = version_compare ( WC ()->version, '3.0', '<' ) ? $object->product_type : $object->get_type ();

				if ( $this->allow_product_as_present && ( 'gift-card' != $product_type ) ) {
					$header_image_url = $this->get_default_header_image ();
				} else {
					$header_image_url = $this->get_header_image ( $object );
				}
				// check if the admin set a default image for gift card
				$amount = 0;
				if ( $object instanceof WC_Product_Simple || $object instanceof WC_Product_Variable || $object instanceof WC_Product_Yith_Bundle ) {
					$amount = yit_get_display_price ( $object );
				}

				$amount = wc_format_decimal ( $amount );
				$formatted_price = wc_price ( $amount );

				$gift_card_code  = "xxxx-xxxx-xxxx-xxxx";
				$message         = __ ( "Your message...", 'yith-woocommerce-gift-cards' );
			} else if ( $object instanceof YWGC_Gift_Card_Premium ) {

				$header_image_url = $this->get_header_image ( $object );

				$amount          = $object->total_amount;
				$formatted_price = apply_filters ( 'yith_ywgc_gift_card_template_amount', wc_price ( $amount ), $object, $amount );

				$gift_card_code = $object->gift_card_number;
				$message        = $object->message;

			}

			// Checking if the image sent is a product image, if so then we set $header_image_url with correct url
			if ( isset( $header_image_url ) ){
				if ( strpos( $header_image_url, '-yith_wc_gift_card_premium_separator_ywgc_template_design-') !== false ) {
				    $array_header_image_url = explode( "-yith_wc_gift_card_premium_separator_ywgc_template_design-", $header_image_url );
					$header_image_url = $array_header_image_url['1'];
				}
			}

            $product_id = isset($object->product_id) ? $object->product_id : '';

			wc_get_template ( 'yith-gift-cards/gift-card-template.php',
				array(
					'template_style'   => $this->template_style,
					'company_logo_url' => $this->shop_logo_on_template ? $this->shop_logo_url : '',
					'header_image_url' => $header_image_url,
					'formatted_price'  => $formatted_price,
					'gift_card_code'   => $gift_card_code,
					'message'          => $message,
					'context'          => $context,
					'object'		   => $object,
					'product_id'	   => $product_id,

				),
				'',
				trailingslashit ( YITH_YWGC_TEMPLATES_DIR ) );
		}

		/**
		 * Start the scheduling that let gift cards to be sent on expected date
		 */
		public static function start_gift_cards_scheduling() {
			$ve = get_option( 'gmt_offset' ) > 0 ? '+' : '-';
			wp_schedule_event( strtotime( '00:00 ' . $ve . get_option( 'gmt_offset' ) . ' HOURS' ), 'daily', 'ywgc_start_gift_cards_sending' );
		}

		/**
		 * Stop the scheduling that let gift cards to be sent on expected date
		 */
		public static function end_gift_cards_scheduling() {
			wp_clear_scheduled_hook ( 'ywgc_start_gift_cards_sending' );
		}

		/**
		 * Perform some check to a gift card that should be applied to the cart
		 * and retrieve a message code
		 *
		 * @param YWGC_Gift_Card $gift
		 *
		 * @return bool
		 */
		public function check_gift_card( $gift, $remove = false ) {
			$err_code = '';

			if ( ! $gift->exists () ) {
				$err_code = YITH_YWGC_Gift_Card::E_GIFT_CARD_NOT_EXIST;
			} elseif ( ! $gift->is_owner ( get_current_user_id () ) ) {
				$err_code = YITH_YWGC_Gift_Card::E_GIFT_CARD_NOT_YOURS;
			} elseif ( isset( WC ()->cart->applied_gift_cards[ $gift->get_code () ] ) ) {
				$err_code = YITH_YWGC_Gift_Card::E_GIFT_CARD_ALREADY_APPLIED;
			} elseif ( $gift->is_expired () ) {
				$err_code = YITH_YWGC_Gift_Card::E_GIFT_CARD_EXPIRED;
			} elseif ( $gift->is_disabled () ) {
				$err_code = YITH_YWGC_Gift_Card::E_GIFT_CARD_DISABLED;
			} elseif ( $gift->is_dismissed () ) {
				$err_code = YITH_YWGC_Gift_Card::E_GIFT_CARD_DISMISSED;
			}

			/**
			 * If the flag $remove is true and there is an error,
			 * the gift card will be removed from the cart, then we set the general
			 * error message here.
			 * */
			if ( $err_code && $remove ) {
				$err_code = YITH_YWGC_Gift_Card::E_GIFT_CARD_INVALID_REMOVED;
			}

			$err_code = apply_filters ( 'yith_ywgc_check_gift_card', $err_code, $gift );
			if ( $err_code ) {
				if ( $err_msg = $gift->get_gift_card_error ( $err_code ) ) {
					wc_add_notice ( $err_msg, 'error' );
				}

				return false;
			}

			if ( $gift->get_balance() < pow( 10, - wc_get_price_decimals() ) ) {
				$err_code = YITH_YWGC_Gift_Card::E_GIFT_CARD_EXPIRED;
				$err_msg  = $gift->get_gift_card_error( $err_code );
				wc_add_notice( $err_msg, 'error' );

				return false;
			}

			if ( ! $remove ){

				$ywgc_minimal_car_total = get_option ( 'ywgc_minimal_car_total' );

                if ( WC()->cart->total < $ywgc_minimal_car_total ) {
                    wc_add_notice( __( 'To apply the gift card the total amount in the cart has to be at least ' . $ywgc_minimal_car_total . get_woocommerce_currency_symbol(), 'yith-woocommerce-gift-cards'), 'error' );

                    return false;
                }

            }

			return true;
		}

		/**
		 * Set the manual amount status for gift cards that are linked to the global value
		 *
		 * @param bool   $enabled
		 * @param string $status
		 *
		 * @return bool
		 */
		public function is_manual_amount_enabled( $enabled, $status ) {

			if ( 'global' == $status ) {
				$enabled = $this->allow_manual_amount;
			}

			return $enabled;
		}

		/**
		 * Retrieve the product instance
		 *
		 * @param WC_Product_Gift_Card $product
		 *
		 * @return null|WC_Product
		 */
		public function get_product_instance( $product ) {

			global $sitepress;

			if ( $sitepress ) {
				$_wcml_settings = get_option ( '_wcml_settings' );
				if ( isset( $_wcml_settings['trnsl_interface'] ) && '1' == $_wcml_settings['trnsl_interface'] ) {
					$product_id = yit_get_prop ( $product, 'id' );

					if ( $product_id ) {
						$id = yit_wpml_object_id ( $product_id, 'product', true, $sitepress->get_default_language () );

						if ( $id != $product_id ) {
							$product = wc_get_product ( $id );
						}
					}
				}
			}

			return $product;
		}


		public function set_expiration_date_for_gift_card_created_manually( $post_ID, $post, $update ){
			if( $post->post_type == 'gift_card' ){
				$expiration_date = get_post_meta( $post_ID, YWGC_Gift_Card_Premium::META_EXPIRATION, true );
				if( $expiration_date == '' && isset($_POST['yit_metaboxes']) ){
					try {
						$usage_expiration       = get_option ( 'ywgc_usage_expiration', 0 );
						$date_format 			= apply_filters('yith_wcgc_date_format','Y-m-d');
						$start_usage_date       = $_POST['yit_metaboxes']['_ywgc_delivery_date'] ? $_POST['yit_metaboxes']['_ywgc_delivery_date'] : date ( $date_format );
						$d                      = DateTime::createFromFormat ( $date_format, $start_usage_date );
						$gift_card_expiration   = $usage_expiration ? strtotime ( "+$usage_expiration month", $d->getTimestamp () ) : 0;
						update_post_meta( $post_ID, YWGC_Gift_Card_Premium::META_EXPIRATION, $gift_card_expiration );
					} catch ( Exception $e ) {
						error_log ( 'An error occurred setting the expiration date for the gift card' );
					}
				}

			}
		}
	}
}
