<?php
/**
 *
 * New contact email html template
 *
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$order = new WC_order( $order_id );
$first_name = $order->get_billing_first_name();
$last_name  = $order->get_billing_last_name();

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email );
?>

<p><strong><?php echo esc_attr( 'First name:', 'woo-mailjet' ); ?></strong> <?php echo esc_attr( $first_name ); ?></p>
<p><strong><?php echo esc_attr( 'Last name:', 'woo-mailjet' ); ?></strong><?php echo esc_attr( $last_name ); ?></p>
<p><strong><?php echo esc_attr( 'Order id:', 'woo-mailjet' ); ?></strong><?php echo esc_attr( $order_id ); ?></p>
<?php

/**
* @hooked WC_Emails::customer_details() Shows customer details
* @hooked WC_Emails::email_address() Shows email address
*/
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );
/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );