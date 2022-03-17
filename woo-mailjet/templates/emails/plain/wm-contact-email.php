<?php
/**
 *
 * New contact email plain template
 *
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo "= " . esc_attr( $email_heading ) . " =\n\n";

$order = new WC_order( $order_id );
$first_name = $order->get_billing_first_name();
$last_name  = $order->get_billing_last_name();

if ( $order && $first_name && $last_name ) {
	echo "First name: " . esc_attr( $first_name ) . "\n\n";
	echo "Last name: " . esc_attr( $last_name ) . "\n\n";
	echo "Order id: " . esc_attr( $order_id ) . "\n\n";
}

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";


/**
* @hooked WC_Emails::customer_details() Shows customer details
* @hooked WC_Emails::email_address() Shows email address
*/
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";


/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );