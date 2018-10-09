<?php
/**
 * Order email to vendor.
 *
 * @version 2.0.0
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( version_compare( WC_VERSION, '3.0.0', '>=' ) ) {
	$order_date = $order->get_date_created();
	$billing_first_name = $order->get_billing_first_name();
	$billing_last_name = $order->get_billing_last_name();
} else {
	$order_date = $order->order_date;
	$billing_first_name = $order->billing_first_name;
	$billing_last_name = $order->billing_last_name;
}
/**/
$customer_id = $order->get_user_id();
$customer = get_user_by( 'ID', $customer_id );
$order_count = count($order->get_items());
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<h1 style="color: #ffffff; font-size: 20px!important;">Félicitation ! Vous avez reçu une réservation.</h1>

<p style="text-align:center!important"><?echo $billing_first_name." ".$billing_last_name ?> souhaite participer à votre activité.</p>

<p style="text-align:center!important;width:100%;margin-bottom:30px!important"><div style="text-align:center!important;margin:0 auto 30px;"><?php echo get_avatar( $customer_id, 120 ); ?></div></p>

<p style="font-weight:bold">Rappel : Vous devez confirmer ou annuler la réservation. Toutes réservations non confirmées seront annulées et remboursées après le date de l'activité.</p>



<p>Voici le détail de la réservation :</p>

<h2><?php printf( esc_html__( 'Réservation #%s', 'woocommerce-product-vendors' ), $order->get_order_number() ); ?> (<?php printf( '<time datetime="%s">%s</time>', date_i18n( 'c', strtotime( $order_date ) ), date_i18n( wc_date_format(), strtotime( $order_date ) ) ); ?>)</h2>

<?php $email->render_order_details_table( $order, $sent_to_admin, $plain_text, $email, $this_vendor ); ?>

<?php do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email ); ?>

<?php do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email ); ?>

<?php if ($order_count == 1) { ?>

<p style="text-align:center!important;margin-bottom:30px!important;margin-top:30px!important"><a href="https://www.mylittlewe.com/my-account/gestion-activites/reservations/detail?booking-id=<?php echo $order->get_order_number() ?>&order_note=confirmed" style="padding:10px!important;border-radius: 5px;text-align:center;width:180px!important;background-color:#d13060!important;color:white;font-size:15px!important;margin-top:20px!important;margin-bottom:40px!important;text-decoration: none!important">Confirmer la réservation</a></p>

<?php } ?>

<p style="text-align:center!important"><a href="https://www.mylittlewe.com/my-account/gestion-activites/reservations" style="padding:10px!important;border-radius: 5px;text-align:center;width:180px!important;background-color:#ef6600!important;color:white;font-size:15px!important;margin-top:20px!important;margin-bottom:20px;text-decoration: none!important">Voir mes réservations</a></p>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
