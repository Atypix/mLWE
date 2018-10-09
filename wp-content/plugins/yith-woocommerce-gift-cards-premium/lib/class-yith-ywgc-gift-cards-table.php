<?php
if ( ! defined ( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( ! class_exists ( 'YITH_YWGC_Gift_Cards_Table' ) ) {
	
	/**
	 *
	 * @class   YITH_YWGC_Gift_Cards_Table
	 *
	 * @since   1.0.0
	 * @author  Lorenzo Giuffrida
	 */
	class YITH_YWGC_Gift_Cards_Table {
		
		/* Gift card table columns */
		const COLUMN_ID_ORDER = 'purchase_order';
		const COLUMN_ID_INFORMATION = 'information';
		const COLUMN_ID_AMOUNT = 'amount';
		const COLUMN_ID_BALANCE = 'balance';
		const COLUMN_ID_DEST_ORDERS = 'dest_orders';
		const COLUMN_ID_DEST_ORDERS_TOTAL = 'dest_order_total';
		const COLUMN_ID_ACTIONS = 'gift_card_actions';
		const COLUMN_ID_INTERNAL_NOTES = 'internal_notes';
		const COLUMN_ID_EXPIRATION_DATE = 'expiration_date';
		
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
			
			// Add to admin_init function
			add_filter ( 'manage_edit-gift_card_columns', array( $this, 'add_custom_columns_title' ) );
			
			// Add to admin_init function
			add_action ( 'manage_gift_card_posts_custom_column', array(
				$this,
				'show_custom_column_content',
			), 10, 2 );
		}
		
		/**
		 * Add custom columns to custom post type table
		 *
		 * @param array $defaults current columns
		 *
		 * @return array new columns
		 */
		function add_custom_columns_title( $defaults ) {
			$columns = array_slice ( $defaults, 0, 2 );
			
			$columns[ self::COLUMN_ID_ORDER ]             = __ ( "Order", 'yith-woocommerce-gift-cards' );
			$columns[ self::COLUMN_ID_AMOUNT ]            = __ ( "Amount", 'yith-woocommerce-gift-cards' );
			$columns[ self::COLUMN_ID_BALANCE ]           = __ ( "Balance", 'yith-woocommerce-gift-cards' );
			$columns[ self::COLUMN_ID_DEST_ORDERS ]       = __ ( "Orders", 'yith-woocommerce-gift-cards' );
			$columns[ self::COLUMN_ID_DEST_ORDERS_TOTAL ] = __ ( "Order total", 'yith-woocommerce-gift-cards' );
			$columns[ self::COLUMN_ID_EXPIRATION_DATE ]   = __ ( "Expiration date", 'yith-woocommerce-gift-cards' );
			$columns[ self::COLUMN_ID_INFORMATION ]       = __ ( "Information", 'yith-woocommerce-gift-cards' );
			$columns[ self::COLUMN_ID_ACTIONS ]           = '';
			
			if ( YITH_YWGC ()->enable_internal_notes ) {
				$columns[ self::COLUMN_ID_INTERNAL_NOTES ] = __ ( "Notes", 'yith-woocommerce-gift-cards' );
				
			}
			$columns = apply_filters('yith_wcgc_custom_columns_title',$columns);
			return array_merge ( $columns, array_slice ( $defaults, 1 ) );
		}
		
		/**
		 * @param WC_Order|int $order
		 *
		 * @return int
		 */
		public function get_order_number_and_details( $order ) {
			
			if ( is_numeric ( $order ) ) {
				$order = wc_get_order ( $order );
			}
			
			if ( ! $order instanceof WC_Order ) {
				return '';
			}
			$order_id = yit_get_order_id ( $order );
			$customer = $order->get_user ();
			if ( $customer ) {
				$username = '<a href="user-edit.php?user_id=' . absint ( $customer->ID ) . '">';
				
				if ( $customer->first_name || $customer->last_name ) {
					$username .= esc_html ( ucfirst ( $customer->first_name ) . ' ' . ucfirst ( $customer->last_name ) );
				} else {
					$username .= esc_html ( ucfirst ( $customer->display_name ) );
				}
				
				$username .= '</a>';
				
			} else {
				$billing_first_name = version_compare ( WC ()->version, '3.0', '<' ) ? $order->billing_first_name : $order->get_billing_first_name ();
				$billing_last_name  = version_compare ( WC ()->version, '3.0', '<' ) ? $order->billing_last_name : $order->get_billing_last_name ();
				
				if ( $billing_first_name || $billing_last_name ) {
					$username = trim ( $billing_first_name . ' ' . $billing_last_name );
				} else {
					$username = __ ( 'Guest', 'yith-woocommerce-gift-cards' );
				}
			}
			
			return sprintf ( _x ( '%s by %s', 'Order number by X', 'yith-woocommerce-gift-cards' ),
				'<a href="' . admin_url ( 'post.php?post=' . absint ( $order_id ) . '&action=edit' ) . '" class="row-title"><strong>#' .
				esc_attr ( $order->get_order_number () ) . '</strong></a>',
				$username );
		}
		
		
		/**
		 * show content for custom columns
		 *
		 * @param $column_name string column shown
		 * @param $post_ID     int     post to use
		 */
		public function show_custom_column_content( $column_name, $post_ID ) {
			
			$gift_card = new YWGC_Gift_Card_Premium( array( 'ID' => $post_ID ) );

            if ( ! $gift_card->exists () ) {
				return;
			}
			
			switch ( $column_name ) {
				case self::COLUMN_ID_ORDER :
					
					if ( $gift_card->order_id ) {
						echo $this->get_order_number_and_details ( $gift_card->order_id );
					} else {
                        echo apply_filters( 'yith_wcgc_table_created_manually_message', __( "Created manually", 'yith-woocommerce-gift-cards' ) );
					}
					
					break;
				
				case self::COLUMN_ID_AMOUNT :
					
					echo wc_price ( $gift_card->total_amount );
					
					break;
				
				case self::COLUMN_ID_BALANCE:
					
					echo wc_price ( $gift_card->get_balance () );
					
					break;
				
				case self::COLUMN_ID_DEST_ORDERS:
					$orders = $gift_card->get_registered_orders ();
					if ( $orders ) {
						foreach ( $orders as $order_id ) {
							echo $this->get_order_number_and_details ( $order_id );
							echo "<br>";
						}
					}
					else if ( $gift_card->get_balance() < $gift_card->total_amount && $gift_card->get_balance() > 0 ){
                       echo apply_filters( 'yith_wcgc_table_partially_redeemed_message', __( "Partially redeemed", 'yith-woocommerce-gift-cards' ) );
                    }
                    else if ( $gift_card->get_balance()  == 0 ){
                       echo apply_filters( 'yith_wcgc_table_completely_redeemed_message', __( "Completely redeemed", 'yith-woocommerce-gift-cards' ) );
                    }
					else {
                        echo apply_filters( 'yith_wcgc_table_code_no_used_message', __( "The code has not been used yet", 'yith-woocommerce-gift-cards' ) );
					}
					
					break;
				
				case self::COLUMN_ID_INFORMATION:
					
					$this->show_details_on_gift_cards_table ( $post_ID, $gift_card );
					
					break;
				
				case self::COLUMN_ID_DEST_ORDERS_TOTAL:
					
					$orders = $gift_card->get_registered_orders ();
					$total  = 0.00;
					
					if ( $orders ) {
						foreach ( $orders as $order_id ) {
							
							if ( $the_order = wc_get_order ( $order_id ) ) {
								//  From version 1.2.10, show the order totals instead of subtotals
								//  $order_total = floatval(preg_replace('#[^\d.]#', '', $the_order->get_subtotal_to_display()));
								$total += yit_get_prop ( $the_order, 'total' );
							}
						}
					}
					echo wc_price ( $total );
					
					if ( $gift_card->total_amount && ( $total > $gift_card->total_amount ) ) {
						$percent = (float) ( $total - $gift_card->total_amount ) / $gift_card->total_amount * 100;
						echo '<br><span class="ywgc-percent">' . sprintf ( __ ( '(+ %.2f%%)', 'yith-woocommerce-gift-cards' ), $percent ) . '</span>';
					}
					
					break;

				case self::COLUMN_ID_EXPIRATION_DATE:
					$date_format = apply_filters('yith_wcgc_date_format','Y-m-d');
					if( $gift_card->expiration != 0 ){
						echo date ( $date_format, $gift_card->expiration );
					}else{
						_e('Unlimited','yith-woocommerce-gift-cards');
					}

					break;
				
				case self::COLUMN_ID_ACTIONS:
					
					$this->show_change_status_button ( $post_ID, $gift_card );
					$this->show_send_email_button ( $post_ID, $gift_card );
					
					break;
				
				case self::COLUMN_ID_INTERNAL_NOTES:
					
					echo $gift_card->internal_notes;
					
					break;

                default:
                     echo apply_filters('yith_wcgc_column_default','',$post_ID, $column_name);
			}
		}
		
		/**
		 * @param                        $post_ID
		 * @param YWGC_Gift_Card_Premium $gift_card
		 */
		public function show_send_email_button( $post_ID, $gift_card ) {
			if ( $gift_card->is_enabled () ) {
				
				$recipient = $gift_card->recipient;
				
				if ( ! empty( $recipient ) ) {
					
					$send_now_link = sprintf ( '<a class="ywgc-actions %s" href="%s" title="%s">%s</a>',
						'gift-cards send-now',
						esc_url_raw ( add_query_arg ( array(
							YWGC_ACTION_RETRY_SENDING => 1,
							'id'                      => $post_ID
						) ) ),
						__ ( "Send now", 'yith-woocommerce-gift-cards' ),
						__ ( "Send now", 'yith-woocommerce-gift-cards' ) );
					
					echo $send_now_link;
				}
			}
		}
		
		/**
		 * @param                        $post_ID
		 * @param YWGC_Gift_Card_Premium $gift_card
		 */
		public function show_change_status_button( $post_ID, $gift_card ) {
			$status_class = "";
			$message      = "";
			$action       = '';
			
			//  Print some action button based on the gift card status, if the gift card is not dismissed
			if ( $gift_card->is_disabled () ) {
				$status_class = "gift-cards disabled";
				$message      = __ ( "Enable", 'yith-woocommerce-gift-cards' );
				$action       = YWGC_ACTION_ENABLE_CARD;
			} elseif ( $gift_card->is_enabled () ) {
				$status_class = "gift-cards enabled";
				$message      = __ ( "Disable", 'yith-woocommerce-gift-cards' );
				$action       = YWGC_ACTION_DISABLE_CARD;
			}
			
			if ( $action ) {
				echo sprintf ( '<a class="ywgc-actions %s" href="%s" title="%s">%s</a>',
					$status_class,
					esc_url ( add_query_arg ( array( $action => 1, 'id' => $post_ID ) ) ),
					$message,
					$message );
			}
		}
		
		/**
		 * @param int                    $post_ID
		 * @param YWGC_Gift_Card_Premium $gift_card
		 */
		public function show_details_on_gift_cards_table( $post_ID, $gift_card ) {
			
			if ( $gift_card->is_dismissed () ) {
				?>
				<span
					class="ywgc-dismissed-text"><?php _e ( "This card is dismissed.", 'yith-woocommerce-gift-cards' ); ?></span>
				<?php
			}
			
			if ( ! $gift_card->is_digital ) {
				?>
				<div>
					<span><?php echo __ ( "Physical product", 'yith-woocommerce-gift-cards' ); ?></span>
				</div>
				<?php
			} else {
				$date_format = apply_filters('yith_wcgc_date_format','Y-m-d');
				if ( $gift_card->delivery_send_date ) {
					$status_class = "sent";
					$message      = sprintf ( __ ( "Sent on %s", 'yith-woocommerce-gift-cards' ), $gift_card->delivery_send_date );
				} else if ( $gift_card->delivery_date >= current_time ( $date_format ) ) {
					$status_class = "scheduled";
					$message      = __ ( "Scheduled", 'yith-woocommerce-gift-cards' );
				} else {
					$status_class = "failed";
					$message      = __ ( "Failed", 'yith-woocommerce-gift-cards' );
				}
				?>
				
				<div>
					<span><?php echo sprintf ( __ ( "Recipient: %s", 'yith-woocommerce-gift-cards' ), $gift_card->recipient ); ?></span>
				</div>
				<div>
					<span><?php echo sprintf ( __ ( "Delivery date: %s", 'yith-woocommerce-gift-cards' ), $gift_card->delivery_date ); ?></span>
					<br>
					<span
						class="ywgc-delivery-status <?php echo $status_class; ?>"><?php echo $message; ?></span>
				
				</div>
				<?php
			}
		}
	}
}

YITH_YWGC_Gift_Cards_Table::get_instance ();