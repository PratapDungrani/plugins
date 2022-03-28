<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use \Mailjet\Resources;

add_filter( 'add_contact_to_mailjet', 'wm_mailjet_action', 10, 4 );
function wm_mailjet_action($send_email, $email, $apikey, $secretkey) {
	//setup connection	
	$mj = new \Mailjet\Client($apikey, $secretkey,true,['version' => 'v3']);
	
	//get email from mailjet contact
	$request = $mj->get(Resources::$Contact, [
		'id' => $email
	]);
	
	//return if contact already created
	if($request->success()) {
		$send_email = 'false';
		return $send_email;
	}
	
	//create contact if email does not exist and send mail to admin		
	$body = [
		'Email' => $email
	];
	
	$response = $mj->post(Resources::$Contact, [
		'body' => $body
	]);
	
	if($response->success()) {
		$send_email = 'true';
	} else {
		$send_email = 'false';
	}
	
	return $send_email;
}