<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Class
 *
 * @extends \WC_Email
 */
use \Mailjet\Resources;

class WM_Contact_Email extends WC_Email {
	
	/**
	 * Set email defaults
	 */
	public function __construct() {
		// Unique ID for custom email
		$this->id = 'wm_contact_email';		
		
		// Title field in WooCommerce Email settings
		$this->title = __( 'Mailjet Contact', 'woo-mailjet' );
		
		// Description field in WooCommerce email settings
		$this->description = __( 'Contact email is sent to admin when new contact is added in mailjet.', 'woo-mailjet' );
		
		// Default heading and subject lines
		$this->subject = apply_filters( 'wm_email_default_subject', __( 'Contact exported successfully', 'woo-mailjet' ) );
		$this->heading = apply_filters( 'wm_email_default_heading', __( 'New contact is added to Mailjet', 'woo-mailjet' ) );
		
		// Email receiver
		$this->recipient     =  get_option( 'admin_email' );
		
		$this->template_base  = CUSTOM_WM_EMAIL_PATH . 'templates/';
		$this->template_html  = 'emails/wm-contact-email.php';
		$this->template_plain = 'emails/plain/wm-contact-email.php';
		
		// Trigger email when payment is complete
		add_action( 'woocommerce_payment_complete', array( $this, 'trigger' ) );
		
		// Call parent constructor to load any other defaults not explicity defined here
		parent::__construct();
	}
	/**
	 * Prepares email content and triggers the email
	 *
	 * @param int $order_id
	 */
	public function trigger( $order_id ) {
		// Bail if no order ID is present
		if ( ! $order_id ) {
			return;
		}
		
		$this->order_id  = $order_id;
		
		// setup order object
		$this->object = new WC_Order( $order_id );
		
		// get order items as array
		$order_items = $this->object->get_items();
		
		//get user email
		$email = $this->object->get_billing_email();
		
		//check if user has checked subscribe box
		$if_subscribed = get_post_meta( $order_id, 'mailjet_checkbox', true);
		if(!$if_subscribed) {
			return;
		}
		
		//get api key values
		$email_settings = get_option('woocommerce_wm_contact_email_settings');
		$apikey = $email_settings['apikey'];
		$secretkey = $email_settings['secretkey'];
		
		//return if api keys are not added
		if(!$apikey || !$secretkey) {
			return;
		}
		
		//setup connection	
		$mj = new \Mailjet\Client($apikey, $secretkey,true,['version' => 'v3']);
		
		//get email from mailjet contact
		$request = $mj->get(Resources::$Contact, [
			'id' => $email
		]);
		
		//return if contact already created
		if($request->success()) {
			return;
		}
		
		//create contact if email does not exist and send mail to admin		
		$body = [
			'Email' => $email
		];
		
		$response = $mj->post(Resources::$Contact, [
			'body' => $body
		]);
		
		//if contact added successfully
		if($response->success()) {
		/* Proceed with sending email */
		
			if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
				return;
			}
			// send the email
			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			
			// add order note about the same
			$this->object->add_order_note( sprintf( __( '%s email sent to admin.', 'woo-mailjet' ), $this->title ) );		
		}
	}
	
	/**
	 * get_content_html function.
	 *
	 * @return string
	 */
	public function get_content_html() {
		return wc_get_template_html( 
			$this->template_html, array(
				'order_id'      	=> $this->order_id,
				'email_heading'		=> $this->heading,
				'sent_to_admin'		=> true,
				'plain_text'		=> false,
				'email'				=> $this
			), '', $this->template_base 
		);
	}
	/**
	 * get_content_plain function.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		return wc_get_template_html( 
			$this->template_plain, array(
				'order_id'      	=> $this->order_id,
				'email_heading'		=> $this->heading,
				'sent_to_admin'		=> true,
				'plain_text'		=> true,
				'email'				=> $this
			), '', $this->template_base 
		);
	}
	
	
	/**
	 * Initialize settings form fields
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'    => array(
				'title'   => __( 'Enable/Disable', 'woo-mailjet' ),
				'type'    => 'checkbox',
				'label'   => 'Enable this email notification',
				'default' => 'yes'
			),
			'apikey'    => array(
				'title'       => __( 'Api key', 'woo-mailjet' ),
				'type'        => 'text',
				'description' => sprintf( 'Add Mailjet Api key' ),
				'placeholder' => '',
				'default'     => ''
			),
			'secretkey'    => array(
				'title'       => __( 'Secret key', 'woo-mailjet' ),
				'type'        => 'text',
				'description' => sprintf( 'Add Mailjet Secret key' ),
				'placeholder' => '',
				'default'     => ''
			),
			'subject'    => array(
				'title'       => __( 'Subject', 'woo-mailjet' ),
				'type'        => 'text',
				'description' => sprintf( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', $this->subject ),
				'placeholder' => 'Contact exported successfully',
				'default'     => 'Contact exported successfully'
			),
			'heading'    => array(
				'title'       => __( 'Email Heading', 'woo-mailjet' ),
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.' ), $this->heading ),
				'placeholder' => '',
				'default'     => ''
			),
			'email_type' => array(
				'title'       => __( 'Email type', 'woo-mailjet' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'woo-mailjet' ),
				'default'       => 'html',
				'class'         => 'email_type wc-enhanced-select',
				'options'     => array(
					'plain'	    => __( 'Plain text', 'woo-mailjet' ),
					'html' 	    => __( 'HTML', 'woo-mailjet' ),
					'multipart' => __( 'Multipart', 'woo-mailjet' ),
				)
			)
		);
	}
}
return new WM_Contact_Email();