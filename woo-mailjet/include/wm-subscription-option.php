<?php
/**
 * Add subscribe checkbox meta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class
 */
class WM_Subscribe {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_review_order_before_submit', array( $this, 'add_subscribe_checkbox' ), 10 );
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'save_subscribe_checkbox_order_meta' ) );
		add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'subscribe_checkbox_display_admin_order_meta'), 10, 1 );
	}

	/**
	 * Add subscribe checkbox on checkout page
	 */
	public function add_subscribe_checkbox() {
	   
		woocommerce_form_field( 'mailjet_checkbox', array(
		   'type'          => 'checkbox',
		   'class'         => array('form-row mycheckbox'), // CSS Class
		   'label_class'   => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
		   'input_class'   => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
		   'required'      => false, // Mandatory or Optional
		   'label'         => 'Subscribe me to Tyche news later',
		));    
	}
	
	/**
	 * Add subscribe checkbox as order meta with field value to database
	 */
	public function save_subscribe_checkbox_order_meta( $order_id ) {
		if ( ! empty( $_POST['mailjet_checkbox'] ) ) {
			update_post_meta( $order_id, 'mailjet_checkbox', sanitize_text_field( $_POST['mailjet_checkbox'] ) );
		}
	}
	
	/**
	 * Display subscribe checkbox value on the backend WooCOmmerce order
	 */
	public function subscribe_checkbox_display_admin_order_meta($order) {
		$value = 'Not subscribed';
		if(get_post_meta( $order->get_id(), 'mailjet_checkbox', true )) {
			$value = 'Subscribed';
		}
		echo '<p><strong>'.__('Mailjet').':</strong> ' . $value . '<p>'; 
	}
	
}
new WM_Subscribe();