<?php
/**
 * Woo Mailjet Uninstall
 *
 * Uninstalling all settings for the plugin
 *
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option('woocommerce_wm_contact_email_settings');