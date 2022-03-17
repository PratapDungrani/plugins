<?php
/**
 * Set custom email class
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class
 */
class WM_Email {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_email_classes', array( &$this, 'wm_init_emails' ) );
	}

	/**
	 * Add to the list of email classes that WooCommerce loads
	 */
	public function wm_init_emails( $email_classes ) {
		if ( ! isset( $email_classes['WM_Contact_Email'] ) ) {
			require_once( CUSTOM_WM_EMAIL_PATH . 'include/wm_order_placed_email.php' );
			
			$email_classes['WM_Contact_Email'] = new WM_Contact_Email();
		}
		return $email_classes;
	}
	
}
new WM_Email();
